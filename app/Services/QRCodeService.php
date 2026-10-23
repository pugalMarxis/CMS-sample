<?php

namespace App\Services;

use App\Models\Child;
use App\Models\QRCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRCodeService
{
    public function generateForChild(Child $child): QRCode
    {
        // Generate a secure random token.
        $token = Str::random(64);

        // Store only the SHA-256 hash in the database.
        $tokenHash = hash('sha256', $token);

        // URL encoded inside the QR code.
        $scanUrl = url('/qr/scan/' . $token);

        // Generate QR code as SVG.
        $renderer = new ImageRenderer(
            new RendererStyle(400, 2),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $qrImage = $writer->writeString($scanUrl);

        // Store QR image.
        $imagePath = 'qrcodes/' . $child->child_code . '_' . time() . '.svg';

        Storage::disk('public')->put($imagePath, $qrImage);

        // Save QR information.
        return QRCode::create([
            'child_id' => $child->id,
            'token_hash' => $tokenHash,
            'qr_image_path' => $imagePath,
            'status' => 'active',
            'issued_at' => now(),
            'expires_at' => null,
        ]);
    }

    /**
     * Generate a self-contained offline payload for a child.
     * Contains essential identification data so it can be verified
     * anywhere without internet, network access, or an external API.
     *
     * @param Child $child
     * @return array<string, mixed>
     */
    public function generateOfflinePayload(Child $child): array
    {
        $primaryGuardian = $child->guardians->where('is_primary', true)->first()
            ?: $child->guardians->first();

        $fullName = trim(
            $child->first_name . ' ' .
            ($child->middle_name ? $child->middle_name . ' ' : '') .
            $child->last_name
        );

        return [
            'kfhi' => 'offline_v1',
            'id' => $child->id,
            'code' => $child->child_code,
            'name' => $fullName,
            'gender' => $child->gender,
            'dob' => $child->date_of_birth ? $child->date_of_birth->format('Y-m-d') : null,
            'school' => $child->school_name,
            'grade' => $child->grade,
            'gn' => $child->gn_division,
            'district' => $child->district,
            'guardian' => $primaryGuardian ? $primaryGuardian->full_name : null,
            'relationship' => $primaryGuardian ? $primaryGuardian->relationship : null,
            'phone' => $primaryGuardian ? $primaryGuardian->phone : null,
            'status' => $child->status ?? 'active',
            'issued' => now()->format('Y-m-d'),
        ];
    }

    /**
     * Generate offline JSON string for a child.
     */
    public function generateOfflineJson(Child $child): string
    {
        return json_encode($this->generateOfflinePayload($child), JSON_UNESCAPED_SLASHES);
    }

    /**
     * Render an offline QR code SVG using BaconQrCode (100% offline, pure PHP).
     */
    public function generateOfflineSvg(Child $child, int $size = 350): string
    {
        $payload = $this->generateOfflineJson($child);

        $renderer = new ImageRenderer(
            new RendererStyle($size, 2),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($payload);
    }
}