<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// use App\Models\
class DetailPenjualanController extends Controller
{
    // public function getDetailPenjualan(Request $request, $id) {
    //     $sidx = $request->input('sidx', 'no_bukti');
    //     $sord = $request->input('sord', 'asc');
    //     // $id_penjualan = $request->input('id');

    //     $query = DB::table('tb_penjualan_detail')
    //     ->join('tb_penjualan', 'tb_penjualan.id', '=', 'tb_penjualan_detail.id_penjualan')
    //     ->where('id_penjualan', '=', $id)
    //     ->select(
    //         'tb_penjualan_detail.id',
    //         'tb_penjualan.no_bukti',
    //         'tb_penjualan_detail.nama_barang',
    //         'tb_penjualan_detail.quantity',
    //         'tb_penjualan_detail.harga',
    //         DB::raw('tb_penjualan_detail.quantity * tb_penjualan_detail.harga as total')
    //     )
    //     ->orderBy($sidx, $sord)
    //     ->get();

    //     return $query;
    // }

    // public function createDetailPenjualan(Request $request, $id) {
    //     $masterDetail = json_decode($request->input('masterDetail', '[]'), true);
    //     $total = 0;

    //     // $validator = Validator::make($request->all(), [
    //     //     'masterDetail' => 'required|array',
    //     //     'masterDetail.*.nama_barang' => 'required|string',
    //     //     'masterDetail.*.quantity' => 'required|integer|min:1',
    //     //     'masterDetail.*.harga' => 'required|numeric|min:0',
    //     // ]);

    //     // if ($validator->fails()) {
    //     //     return response('Harap isi semua data', 400);
    //     // }
        
    //     foreach ($masterDetail as &$item) {
    //         $total += $item['quantity'] * $item['harga'];
    //         $item['id_penjualan'] = $id;
    //     }

    //     DB::beginTransaction();
    //     try {
    //         DB::table('tb_penjualan_detail')->insert($masterDetail);

    //         DB::table('tb_penjualan')->where('id', $id)->update(['total' => $total]);

    //         DB::commit();

    //         return response("sukses", 200);
    //     } catch (\Throwable $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'success' => false,
    //             'message' => "Failed to create master detail"
    //         ]);
    //     }
    // }

    // public function updateDetail(Request $request, $id){
    //     $masterDetail = json_decode($request->input('masterDetail', '[]'), true);
    //     $total = 0;

    //     // $delete = DB::table('tb_penjualan_detail')
    //     // ->where('id_penjualan', $id)
    //     // ->delete();

    //     // if ($delete) {
    //         foreach ($masterDetail as &$item) {
    //             $total += $item['quantity'] * $item['harga'];
    //             $item['id_penjualan'] = $id;
    //         }

    //         // DB::table('tb_penjualan_detail')->insert($data);
    //         // return response("sukses", 200);
    //     // }
    //     // dd($masterDetail);
    //     DB::beginTransaction();
    //     try {
    //         DB::table('tb_penjualan_detail')->where('id_penjualan', $id)->delete();

    //         DB::table('tb_penjualan_detail')->insert($masterDetail);

    //         DB::table('tb_penjualan')->where('id', $id)->update(['total' => $total]);

    //         DB::commit();

    //         return response("sukses", 200);

    //     } catch (\Throwable $th) {
    //         DB::rollback();
    //     }
    // }

    // public function deleteDetail($id){
    //     $delete = DB::table('tb_penjualan_detail')
    //     ->where('id_penjualan', $id)
    //     ->delete();
    //     // dd($delete);
    //     if ($delete) {
    //         return response("sukses", 200);
    //     }
    // }
    
}
 

?>