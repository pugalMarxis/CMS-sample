<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ChildReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private Collection $children
    ) {}

    public function collection(): Collection
    {
        return $this->children->map(function ($child) {
            return [
                $child->child_code,
                $child->first_name,
                $child->middle_name,
                $child->last_name,
                $child->district,
                $child->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Child Code',
            'First Name',
            'Middle Name',
            'Last Name',
            'District',
            'Status',
        ];
    }
}
