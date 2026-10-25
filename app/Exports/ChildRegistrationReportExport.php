<?php

namespace App\Exports;

use App\Models\Child;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChildRegistrationReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private Collection $children
    ) {}

    public function collection(): Collection
    {
        return $this->children;
    }

    public function headings(): array
    {
        return [
            'Child Code',
            'First Name',
            'Middle Name',
            'Last Name',
            'Gender',
            'District',
            'School',
            'Grade',
            'Registration Date',
            'Registered By',
            'Status',
        ];
    }

    public function map($child): array
    {
        return [
            $child->child_code,
            $child->first_name,
            $child->middle_name,
            $child->last_name,
            $child->gender,
            $child->district,
            $child->school_name,
            $child->grade,
            $child->created_at?->format('Y-m-d H:i'),
            $child->creator?->name ?? 'Unknown',
            $child->status,
        ];
    }
}
