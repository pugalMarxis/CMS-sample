<?php

namespace App\Http\Controllers\ChildOfficer;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Exports\ChildRegistrationReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ChildRegistrationReportController extends Controller
{
    private function filteredChildrenQuery(Request $request): Builder
    {
        $query = Child::query()->with('creator');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        if ($request->filled('district')) {
            $query->where('district', $request->input('district'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('child_code', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $children = $this->filteredChildrenQuery($request)
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalRegistered = $this->filteredChildrenQuery($request)->count();

        $registeredToday = Child::whereDate('created_at', Carbon::today())->count();

        $registeredThisMonth = Child::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->count();

        $districts = Child::query()
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return view('child_officer.reports.registration', compact(
            'children',
            'totalRegistered',
            'registeredToday',
            'registeredThisMonth',
            'districts'
        ));
    }

    public function exportExcel(Request $request)
    {
        $children = $this->filteredChildrenQuery($request)
            ->latest('created_at')
            ->get();

        return Excel::download(
            new ChildRegistrationReportExport($children),
            'child-registration-report.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $children = $this->filteredChildrenQuery($request)
            ->latest('created_at')
            ->get();

        $pdf = Pdf::loadView(
            'child_officer.reports.registration-pdf',
            compact('children')
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('child-registration-report.pdf');
    }
}
