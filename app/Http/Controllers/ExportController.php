<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
    public function exportMateriels()
    {
        try {
            $user = Auth::user();

            $mouvements = StockMovement::with(['item.category', 'movementType', 'user'])
                ->where('user_id', $user->id)
                ->orderBy('movement_date', 'desc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Mouvements');

            $headers = [
                'Date', 'Code', 'Matériel', 'Catégorie',
                'Type de mouvement', 'Stock initial', 'Quantité',
                'Reste en stock', 'Effectué par', 'Note'
            ];
            $sheet->fromArray($headers, null, 'A1');

            $sheet->getStyle('A1:J1')->getFont()->setBold(true);
            $sheet->getStyle('A1:J1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('0F2942');
            $sheet->getStyle('A1:J1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A1:J1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row = 2;
            foreach ($mouvements as $mvt) {
                $sheet->setCellValue("A{$row}", $mvt->movement_date
                    ? \Carbon\Carbon::parse($mvt->movement_date)->format('d/m/Y')
                    : '—');
                $sheet->setCellValue("B{$row}", $mvt->item->code ?? '—');
                $sheet->setCellValue("C{$row}", $mvt->item->name ?? '—');
                $sheet->setCellValue("D{$row}", $mvt->item->category->name ?? '—');
                $sheet->setCellValue("E{$row}", $mvt->movementType->name ?? '—');
                $sheet->setCellValue("F{$row}", $mvt->stock_initial ?? 0);
                $sheet->setCellValue("G{$row}", $mvt->quantity ?? 0);
                $sheet->setCellValue("H{$row}", $mvt->stock_final ?? 0);
                $sheet->setCellValue("I{$row}", $mvt->user->name ?? 'Système');
                $sheet->setCellValue("J{$row}", $mvt->note ?? '');

                $typeName = strtolower(str_replace(['é','è','ê'], 'e', $mvt->movementType->name ?? ''));
                $color = '12283F';
                if ($typeName === 'sortie') {
                    $color = 'C0392B';
                } elseif ($typeName === 'retour') {
                    $color = 'A9782C';
                } elseif ($typeName === 'entree') {
                    $color = '1E8449';
                }
                $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB($color);
                $sheet->getStyle("E{$row}")->getFont()->setBold(true);

                $row++;
            }

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $lastRow = $row - 1;
            if ($lastRow >= 1) {
                $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
            }

            $sheet->freezePane('A2');

            $writer = new Xlsx($spreadsheet);
            $filename = 'mouvements_' . now()->format('Ymd_His') . '.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'exportation : ' . $e->getMessage());
        }
    }
}