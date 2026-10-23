<?php

namespace App\Http\Controllers\ChildOfficer;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\QRCode;
use App\Services\QRCodeService;
use Illuminate\Http\Request;

class QRCodeController extends Controller
{
    public function scan()
    {
        return view('child_officer.qr.scan');
    }

    public function offlineQr(Request $request, QRCodeService $qrService)
    {
        $children = Child::with('guardians')
            ->orderBy('first_name')
            ->get();

        $selectedChildId = $request->query('child_id');

        $childrenData = $children->map(function ($child) use ($qrService) {
            $primaryGuardian = $child->guardians->where('is_primary', true)->first()
                ?: $child->guardians->first();

            return [
                'id' => $child->id,
                'child_code' => $child->child_code,
                'full_name' => trim(
                    $child->first_name . ' ' .
                    ($child->middle_name ? $child->middle_name . ' ' : '') .
                    $child->last_name
                ),
                'first_name' => $child->first_name,
                'middle_name' => $child->middle_name,
                'last_name' => $child->last_name,
                'gender' => $child->gender,
                'date_of_birth' => $child->date_of_birth ? $child->date_of_birth->format('Y-m-d') : '',
                'school_name' => $child->school_name,
                'grade' => $child->grade,
                'district' => $child->district,
                'gn_division' => $child->gn_division,
                'status' => $child->status ?? 'active',
                'profile_photo' => $child->profile_photo ? asset('storage/' . $child->profile_photo) : null,
                'guardian_name' => $primaryGuardian ? $primaryGuardian->full_name : '',
                'guardian_relation' => $primaryGuardian ? $primaryGuardian->relationship : '',
                'guardian_phone' => $primaryGuardian ? $primaryGuardian->phone : '',
                'offline_payload' => $qrService->generateOfflinePayload($child),
            ];
        });

        return view('child_officer.qr.offline', [
            'children' => $children,
            'childrenData' => $childrenData,
            'selectedChildId' => $selectedChildId,
            'activeTool' => in_array($request->query('tool'), ['generator', 'scanner', 'log'], true)
                ? $request->query('tool')
                : 'scanner',
        ]);
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $scannedValue = trim($request->token);

        // Check if scanned value is self-contained JSON (offline QR format)
        if (str_starts_with($scannedValue, '{') && str_ends_with($scannedValue, '}')) {
            $jsonData = json_decode($scannedValue, true);
            if (is_array($jsonData)) {
                $code = $jsonData['code'] ?? null;
                $child = null;
                if ($code) {
                    $child = Child::where('child_code', $code)->first();
                }

                if ($child) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Child identified successfully (Offline QR Code).',
                        'child' => [
                            'id' => $child->id,
                            'child_code' => $child->child_code,
                            'full_name' => trim(
                                $child->first_name . ' ' .
                                ($child->middle_name ?? '') . ' ' .
                                $child->last_name
                            ),
                            'gender' => $child->gender,
                            'date_of_birth' => $child->date_of_birth
                                ? $child->date_of_birth->format('Y-m-d')
                                : null,
                            'school_name' => $child->school_name,
                            'grade' => $child->grade,
                            'district' => $child->district,
                            'gn_division' => $child->gn_division,
                            'status' => $child->status,
                        ],
                    ]);
                } elseif (!empty($jsonData['name'])) {
                    // Even if child not found in local database, return decoded offline data
                    return response()->json([
                        'success' => true,
                        'message' => 'Child identified from offline QR badge data.',
                        'child' => [
                            'id' => $jsonData['id'] ?? 0,
                            'child_code' => $jsonData['code'] ?? 'N/A',
                            'full_name' => $jsonData['name'],
                            'gender' => $jsonData['gender'] ?? 'N/A',
                            'date_of_birth' => $jsonData['dob'] ?? 'N/A',
                            'school_name' => $jsonData['school'] ?? 'N/A',
                            'grade' => $jsonData['grade'] ?? 'N/A',
                            'district' => $jsonData['district'] ?? 'N/A',
                            'gn_division' => $jsonData['gn'] ?? 'N/A',
                            'status' => $jsonData['status'] ?? 'active',
                        ],
                    ]);
                }
            }
        }

        /*
         * The existing KFHI QR code contains:
         *
         * /qr/scan/{token}
         *
         * Extract only the token from the scanned URL.
         */
        $path = parse_url($scannedValue, PHP_URL_PATH);

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code format.',
            ], 400);
        }

        $prefix = '/qr/scan/';

        if (!str_starts_with($path, $prefix)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid KFHI QR code.',
            ], 400);
        }

        $token = substr($path, strlen($prefix));

        if ($token === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code token.',
            ], 400);
        }

        // Hash the original token exactly like QRCodeService.
        $tokenHash = hash('sha256', $token);

        // Find the active QR code.
        $qrCode = QRCode::with('child')
            ->where('token_hash', $tokenHash)
            ->where('status', 'active')
            ->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive QR code.',
            ], 404);
        }

        // Update last scanned time.
        $qrCode->update([
            'last_scanned_at' => now(),
        ]);

        $child = $qrCode->child;

        if (!$child) {
            return response()->json([
                'success' => false,
                'message' => 'Child record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Child identified successfully.',

            'child' => [
                'id' => $child->id,

                'child_code' => $child->child_code,

                'full_name' => trim(
                    $child->first_name . ' ' .
                    ($child->middle_name ?? '') . ' ' .
                    $child->last_name
                ),

                'gender' => $child->gender,

                'date_of_birth' => $child->date_of_birth
                    ? $child->date_of_birth->format('Y-m-d')
                    : null,

                'school_name' => $child->school_name,

                'grade' => $child->grade,

                'district' => $child->district,

                'gn_division' => $child->gn_division,

                'status' => $child->status,
            ],
        ]);
    }
}