<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailPenjualan extends Model
{
    // use HasFactory;

    protected $table = "tb_penjualan_detail";
    protected $primaryKey = "id";
    public $timestamps = "false";

    protected $fillable = [
        'id_penjualan',
        'nama_barang',
        'quantity',
        'harga'
    ];

    public function penjualan(){
        return $this->belongsTo(Penjualan::class);
    }

    public function getDetailExportReport($id){
        // $id_penjualan = array_column($id, 'id');
        dd($id);
        $query = DB::table('tb_penjualan_detail')->where('id_penjualan', $id)->get();
        dd($id, $query);
        return $query;
    }

    public function getDetailById($request, $id){
        $sidx = $request->input('sidx', 'id');
        $sord = $request->input('sord', 'asc');

        $query = DB::table('tb_penjualan_detail')
        ->join('tb_penjualan', 'tb_penjualan.id', '=', 'tb_penjualan_detail.id_penjualan')
        ->where('id_penjualan', '=', $id)
        ->select(
            'tb_penjualan_detail.id',
            'tb_penjualan.no_bukti',
            'tb_penjualan_detail.nama_barang',
            'tb_penjualan_detail.quantity',
            'tb_penjualan_detail.harga',
            DB::raw('tb_penjualan_detail.quantity * tb_penjualan_detail.harga as total')
        )
        ->orderBy($sidx, $sord)
        ->get();

        return $query;
    }

    public function createDetail($addData, $request){
        // $masterDetail = json_decode($request->input('masterDetail', '[]'), true);
        $nama_barang = $request->input('nama_barang');
        $quantity = $request->input('quantity');
        $harga = str_replace(',', '', $request->input('harga'));
        // $total = 0;

        // foreach ($masterDetail as &$item) {
        //     $total += $item['quantity'] * $item['harga'];
        //     // var_dump($item['harga']);
        //     $item['id_penjualan'] = $addData;
        // }

        // DB::table('tb_penjualan_detail')->insert($masterDetail);
        for ($i=0; $i <count($nama_barang) ; $i++) {
            DB::table('tb_penjualan_detail')->insert([
                'id_penjualan' => $addData,
                'nama_barang' => $nama_barang[$i],
                'quantity' => $quantity[$i],
                'harga' => $harga[$i]
            ]);
        }
        return "sukses";
    }

    public function updateDetail($request, $id){
        $nama_barang = $request->input('nama_barang');
        $quantity = $request->input('quantity');
        $harga = str_replace(',', '', $request->input('harga'));

        DB::table('tb_penjualan_detail')->where('id_penjualan', $id)->delete();
        
        for ($i=0; $i <count($nama_barang) ; $i++) {
            DB::table('tb_penjualan_detail')->insert([
                'id_penjualan' => $id,
                'nama_barang' => $nama_barang[$i],
                'quantity' => $quantity[$i],
                'harga' => $harga[$i]
            ]);
        }
        return "sukses";
    }

    public function deleteDetail($id){
        return DB::table('tb_penjualan_detail')
        ->where('id_penjualan', $id)
        ->delete();
    }
    
}
