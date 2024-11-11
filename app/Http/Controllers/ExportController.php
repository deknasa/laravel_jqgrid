<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Penjualan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{
    public function exportData(Request $request) {
        try {
        
            $penjualanModel = new Penjualan();
            $data = $penjualanModel->getAllPenjualanExportReport($request);
            // dd($data['detail']);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Data Penjualan');
            $sheet->setCellValue('A1', strtoupper('Data Penjualan'));
            $sheet->mergeCells('A1:E1');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getColumnDimension('A')->setWidth(25);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);

            $row = 3;
            
            foreach ($data['data'] as $item) {
                $sheet->setCellValue('A' . $row, 'NO BUKTI');
                $sheet->setCellValue('A' . $row + 1, 'TGL BUKTI');
                $sheet->setCellValue('A' . $row + 2, 'NAMA PELANGGAN');

                // $row++;
                $sheet->setCellValue('B' . $row, strtoupper($item->no_bukti));
                $sheet->setCellValue('B' . $row + 1, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($item->tgl_bukti));
                $sheet->setCellValue('B' . $row + 2, strtoupper($item->nama_pelanggan));

                $sheet->setCellValue('A' . ($row + 3), 'NO BUKTI');
                $sheet->setCellValue('B' . ($row + 3), 'NAMA BARANG');
                $sheet->setCellValue('C' . ($row + 3), 'QUANTITY');
                $sheet->setCellValue('D' . ($row + 3), 'HARGA');
                $sheet->setCellValue('E' . ($row + 3), 'TOTAL');

                $sheet->getStyle('A' . ($row + 3) . ':E' . ($row + 3))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . ($row + 3) . ':E' . ($row + 3))->getFont()->setBold(true);

                $sheet->getStyle('B'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B'.$row + 1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B'.$row + 2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('B' . $row + 1)->getNumberFormat()->setFormatCode("dd/mm/yyyy");

                $detail = DB::table('tb_penjualan_detail')
                ->where('id_penjualan', $item->id)
                ->get();

                $rowDetail = $row + 4;
                // $subtotal = 0; 

                foreach ($detail as $itemDetail) {
                    // $total = $itemDetail->harga * $itemDetail->quantity;
                    $sheet->setCellValue('A' . $rowDetail, strtoupper($item->no_bukti));
                    $sheet->setCellValue('B' . $rowDetail, strtoupper($itemDetail->nama_barang));
                    $sheet->setCellValue('C' . $rowDetail, $itemDetail->quantity);
                    $sheet->setCellValue('D' . $rowDetail, $itemDetail->harga);
                    // $sheet->setCellValue('E' . $rowDetail, $total);
                    $sheet->setCellValue('E' . $rowDetail, '=C'. $rowDetail. '*'. 'D'.$rowDetail);

                    $sheet->getStyle('A' . $rowDetail)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('B' . $rowDetail)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('C' . $rowDetail)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('D' . $rowDetail)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('E' . $rowDetail)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                    // $subtotal += floatval(str_replace(',', '', $total));
                    $rowDetail++;
                }
                
                $sheet->setCellValue('D' . $rowDetail, 'Sub Total:');
                // dd($rowDetail);
                // $sheet->setCellValue('E' . $rowDetail, $subtotal);
                // $sheet->setCellValue('E' . $rowDetail, '=SUM(E'.$rowDetail.':E'.$rowDetail); 
                $sheet->setCellValue('E' . $rowDetail, '=SUM(E' . ($row + 4) . ':E' . ($rowDetail - 1) . ')');

                // $sheet->getStyle('D' . $rowDetail)->getNumberFormat()->setFormatCode('#.##0,00'); 
                // $sheet->getStyle('E' . $rowDetail)->getNumberFormat()->setFormatCode('#.##0,00');
                for ($i = $row+4; $i <= $rowDetail; $i++) {
                    $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('#,##0.00'); 
                    $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('#,##0.00'); 
                }
                
                $row = $rowDetail + 2;
            }

            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="data_penjualan.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Throwable $th) {
            return response();
        }
    }
    
}


?>