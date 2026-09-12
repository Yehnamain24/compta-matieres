<?php

namespace App\Http\Controllers;

use App\Models\Item;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{
    public function exportMateriels()
    {
        try {
            $materiels = Item::with(['categorie', 'statut'])->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Matériels');

        // En-têtes
            $headers = ['code','Nom', 'Catégorie', 'Statut', 'Quantité', 'Emplacement', 'Date d\'ajout'];
            $sheet->fromArray($headers, null, 'A1');

        // Style des en-têtes
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
            $sheet->getStyle('A1:G1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('0F2942');
            $sheet->getStyle('A1:G1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Données
        $row = 2;
        foreach ($materiels as $m) {
            $sheet->setCellValue("A{$row}", $m->code);
            $sheet->setCellValue("B{$row}", $m->nom);
            $sheet->setCellValue("C{$row}", $m->categorie->nom ?? '—');
            $sheet->setCellValue("D{$row}", $m->statut->nom ?? '—');
            $sheet->setCellValue("E{$row}", $m->quantite);
            $sheet->setCellValue("F{$row}", $m->emplacement ?? '—');
            $sheet->setCellValue("G{$row}", $m->created_at->format('d/m/Y'));
            $row++;
        }

        // Ajuster automatiquement la largeur des colonnes
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Génération du fichier
        $writer = new Xlsx($spreadsheet);
        $filename = 'materiels_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de l\'exportation des données: ' . $e->getMessage());
    }
}

}