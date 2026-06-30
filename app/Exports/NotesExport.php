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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NotesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private ?Collection $cachedCollection = null;

    public function __construct(
        private readonly ?int $filiereId = null,
        private readonly ?int $groupeId = null,
    ) {}

    public function collection(): Collection
    {
        if ($this->cachedCollection === null) {
            $this->cachedCollection = app(StudentResultsService::class)
                ->getBulletinRows($this->filiereId, $this->groupeId);
        }

        return $this->cachedCollection;
    }

    public function headings(): array
    {
        $firstRow = $this->collection()->first();

        return $firstRow ? array_keys((array) $firstRow) : [];
    }

    public function styles(Worksheet $sheet): array
    {
        $totalColumns = count($this->headings());
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

        // Header style — ديناميكي حسب عدد الأعمدة
        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1A3A5C');

        // Alignment
        $sheet->getStyle("A:{$lastColumn}")
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E:' . $lastColumn)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function title(): string
    {
        return 'Bulletin';
    }
}