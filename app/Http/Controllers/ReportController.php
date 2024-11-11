<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Penjualan;

class ReportController extends Controller
{
    public function reportData(Request $request) {
        $penjualanModel = new Penjualan();
        $res = $penjualanModel->getAllPenjualanExportReport($request);
        $data = [];

        foreach ($res['data'] as $item) {
            $detail = DB::table('tb_penjualan_detail')
            // ->join('tb_penjualan', 'tb_penjualan.id', '=', 'tb_penjualan_detail.id_penjualan')
            // ->select('tb_penjualan_detail.*', 'tb_penjualan.no_bukti')
            ->where('id_penjualan', $item->id)
            ->get();

            // dd($item->no_bukti, $detail);

            $dataDetail = [];
            foreach ($detail as $itemDetail) {
                $dataDetail[] = [
                    'id' => $item->id,
                    'no_bukti' => strtoupper($item->no_bukti),
                    'tgl_bukti' => $item->tgl_bukti,
                    'nama_pelanggan' => strtoupper($item->nama_pelanggan),
                    'total' => $item->total,
                    'nama_barang' => strtoupper($itemDetail->nama_barang),
                    'quantity' => $itemDetail->quantity,
                    'harga' => $itemDetail->harga
                ];
            }
            // dd($request->input('sname'), $request->input('sorder'), $detail); 
            $data[] = $dataDetail;
        }

        // $mrtData = response()->json(['customers' => $data]);
        $mrtData = json_encode(['customers' => $data]);
        // dd($mrtData);
        // return view('stimulsoftReport', compact('mrtData'));
        return view('stimulsoftReport', ['mrtData' => $mrtData]);
        // return response()->view('stimulsoftReport', ['customers' => $data])->header('Content-Type', 'application/json');
        
    }
    
}


?>
