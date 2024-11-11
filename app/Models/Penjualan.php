<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\DetailPenjualan;

class Penjualan extends Model
{
    // use HasFactory;

    protected $table = "tb_penjualan";
    protected $primaryKey = 'id';
    public $timestamps = "false";

    protected $fillable = [
        'no_bukti',
        'tgl_bukti',
        'id_pelanggan',
        'total'
    ];

    // public function __construct(){
    //     parent::__construct();

    //     $this->load->model('DetailPenjualan');
    // }

    public function detailPenjualan(){
        return $this->hasMany(DetailPenjualan::class);
    }

    public function getAllPenjualan($sidx, $sord, $rows, $start, $filters){
        $query = DB::table('tb_penjualan')
        ->join('tb_pelanggan', 'tb_pelanggan.id', '=', 'tb_penjualan.id_pelanggan');

        if ($filters) {
            $filters = json_decode($filters, true);
            $groupOp = $filters['groupOp'];

            if ($groupOp == "AND") {
                foreach ($filters['rules'] as $rule) {
                    if ($rule['field'] === 'nama_pelanggan') {
                        $query->where('tb_pelanggan.nama_pelanggan', 'like', '%' . $rule['data'] . '%');
                    } else {
                        $query->where($rule['field'], 'like', '%' . $rule['data'] . '%');
                    }
                }
            } elseif ($groupOp == "OR") {
                foreach ($filters['rules'] as $rule) {
                    $query->orWhere($rule['field'], 'like', '%' . $rule['data'] . '%');
                }
            }
        }
        $count = $query->count();

        $data = $query
        ->select('tb_penjualan.*', 'tb_pelanggan.nama_pelanggan') // ini buat ambil field dari tb_penjualan dan tb_pelanggan.nama_pelanggan setelah search ataupun sebelum search
        ->orderBy($sidx, $sord)
        ->skip($start)
        ->take($rows)
        ->get();
        // dd($count, $data);

        return [ "count" => $count, "data" => $data ];
    }

    public function createPenjualan($request){
        $total = str_replace(',', '', $request->input('total'));
        $tgl_bukti = date("Y-m-d", strtotime($request->input('tgl_bukti', null)));
        $detailModel = new DetailPenjualan();

        $lastData = DB::table('tb_penjualan')
        ->where('no_bukti', 'like', 'NB-%')
        // ->select('no_bukti')
        ->lockForUpdate()
        ->orderBy('no_bukti', 'desc')
        ->first();
        // dd($lastData);
        
        $lastNb = explode("NB-", $lastData->no_bukti);
        $getNumber = $lastNb[1];
        $newNb = 'NB-'.str_pad($getNumber + 1, 4, "0", STR_PAD_LEFT);
        // dd($lastNb, $nomorUrut, str_pad($urut+2, 4, "0", STR_PAD_LEFT), $urut);

        $addData = DB::table('tb_penjualan')->insertGetId([
            'no_bukti' => $newNb,
            'tgl_bukti' => $tgl_bukti,
            'id_pelanggan' => $request->input('id_pelanggan'),
            'total' => $total
        ]);

        $allData = DB::table('tb_penjualan')
        ->join('tb_pelanggan', 'tb_pelanggan.id', '=', 'tb_penjualan.id_pelanggan')
        ->select('tb_penjualan.id')
        ->orderBy($request->input('sname'), $request->input('sorder'))
        ->pluck('id')->toArray();

        // $count = array_search($addData, array_column($allData, 'id'));
        $count = array_search($addData, $allData);
        
        $detailModel->createDetail($addData, $request);

        return [
            "id" => $addData,
            "count" => $count + 1,
        ];
    }

    public function getPenjualanById($request, $id){
        $detailModel = new DetailPenjualan;

        $getData = DB::table('tb_penjualan')
        ->join('tb_pelanggan', 'tb_pelanggan.id', '=', 'tb_penjualan.id_pelanggan')
        // ->join('tb_penjualan_detail', 'tb_penjualan_detail.id_penjualan', '=', 'tb_penjualan.id')
        ->select('tb_penjualan.*', 'tb_pelanggan.nama_pelanggan')
        ->where('tb_penjualan.id', '=', $id)
        ->get();

        $getData->map(function ($item) {
            $item->tgl_bukti = date('d-m-Y', strtotime($item->tgl_bukti));
            // dd($item->tgl_bukti);
            return $item;
        });

        $result = $detailModel->getDetailById($request, $id);

        return [
            "data" => $getData,
            "dataDetail" => $result
        ];
    }

    public function updatePenjualan($sname, $sorder, $request, $id){
        $no_bukti = $request->input('no_bukti', null);
        $tgl_bukti = date("Y-m-d", strtotime($request->input('tgl_bukti', null)));
        $id_pelanggan = $request->input('id_pelanggan', null);
        $total = str_replace(',', '', $request->input('total'));
        
        $detailModel = new DetailPenjualan();

        $a = DB::table('tb_penjualan')->where('id', $id)
        ->lockForUpdate()
        ->first();

        $updatePenjualan = DB::table('tb_penjualan')
        ->where('id', $id)
        ->update([
            // 'no_bukti' => $no_bukti,
            'tgl_bukti' => $tgl_bukti,
            'id_pelanggan' => $id_pelanggan,
            'total' => $total
        ]);

        $allData = DB::table('tb_penjualan')
        ->join('tb_pelanggan', 'tb_pelanggan.id', '=', 'tb_penjualan.id_pelanggan')
        // ->select('tb_penjualan.*', 'tb_pelanggan.nama_pelanggan')
        ->select('tb_penjualan.id')
        ->orderBy($sname, $sorder)
        ->get()->toArray();
        
        
        $count = array_search($id, array_column($allData, 'id'));
        // $count = array_search($id, $allData);
        $detailModel->updateDetail($request, $id);
        // $total = $detailModel->updateDetail($request, $id);
        // DB::table('tb_penjualan')->where('id', $id)->update(['total' => $total]);
        dd($sname, $sorder, $count, $id, $allData);

        return [ "count" => $count + 1 ];
    }   

    public function deletePenjualan($request, $id){
        $detailModel = new DetailPenjualan;
        $newId;
        $count = 0;

        $allData = DB::table('tb_penjualan')
        ->join('tb_pelanggan', 'tb_pelanggan.id', '=', 'tb_penjualan.id_pelanggan')
        ->select('tb_penjualan.id')
        ->orderBy($request->input('sname'), $request->input('sorder'))
        ->get()->toArray();

        $currentIdx = array_search($id, array_column($allData, 'id'));

        if ($currentIdx !== false) {
            $count = $currentIdx + 1;
            if ($count >= count($allData)) {
                $count = $currentIdx - 1;
                $newId = $allData[$count]->id;
            } else {
                $newId = $allData[$count]->id;
            }
        }
        // dd($currentIdx, $newId, $count);

        $detailModel->deleteDetail($id);

        DB::table("tb_penjualan")->where("id", $id)->delete();

        return [
            "id" => $newId,
            "count" => $count + 1
        ];
    }

    public function getAllPenjualanExportReport($request){
        $start = $request->input('start');
        $end = $request->input('end');
        $sname = $request->input('sname');
        $sorder = $request->input('sorder');
        $filters = json_decode($request->input('filters'), true);
        $limit = $end - $start + 1;
        $offset = $start - 1;

        $detailModel = new DetailPenjualan();

        $query = DB::table('tb_penjualan')
        ->join('tb_pelanggan', 'tb_pelanggan.id', '=', 'tb_penjualan.id_pelanggan');

        if ($filters) {
            if ($filters['groupOp'] == "AND") {
                foreach ($filters['rules'] as $rule) {
                    $field = $rule['field'];
                    $data = $rule['data'];
                    
                    if ($field === 'nama_pelanggan') {
                        $query->where('tb_pelanggan.nama_pelanggan', 'like', '%' . $data . '%');
                    } else {
                        $query->where($field, 'like', '%' . $data . '%');
                    }
                }
            } elseif ($filters['groupOp'] == "OR") {
                foreach ($filters['rules'] as $rule) {
                    $field = $rule['field'];
                    $data = $rule['data'];
                    
                    if ($field === 'nama_pelanggan') {
                        $query->orWhere('tb_pelanggan.nama_pelanggan', 'like', '%' . $data . '%');
                    } else {
                        $query->orWhere($field, 'like', '%' . $data . '%');
                    }
                }
            }
        }

        $query = $query
        ->select('tb_penjualan.*', 'tb_pelanggan.nama_pelanggan')
        ->orderBy($sname, $sorder)
        ->skip($offset)
        ->take($limit)
        ->get();

        // $id;
        // foreach ($query as $item) {
        //     $id[] = [
        //         "id" => $item->id
        //     ];
        //     $a = $detailModel->getDetailExportReport($id);
        //     dd($id);
        //     return $id;
        //     // dd($a);
        // }
        
        // $a = $detailModel->getDetailExportReport($id);
        // // dd($id);
        
        return [ 
            "data" => $query, 
            // "detail" => $a 
        ];
    }
    
}
