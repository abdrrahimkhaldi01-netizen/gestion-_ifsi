<?php

namespace App\Exports;

use App\Services\Notes\StudentResultsService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StagiaireNotesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private readonly int $stagiaireId)
    {
    }

    public function collection(): Collection
    {
        return app(StudentResultsService::class)
            ->getBulletinRows(stagiaireId: $this->stagiaireId);
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Groupe',
            'Filiere',
            'Unite',
            'Moyenne Unite',
            'Coefficient total of the unit',
            'Periode',
            'PFE grade',
            'Moyenne Generale',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A3A5C');
        $sheet->getStyle('A:I')->getAlignment()->setVertical('center');
        $sheet->getStyle('E:I')->getAlignment()->setHorizontal('center');

        return [];
    }

    public function title(): string
    {
        return 'Bulletin';
    }
}
