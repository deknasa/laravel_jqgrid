<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;

class IndexController extends Controller
{
    // protected $penjualanModel;

    // public function __construct(Penjualan $penjualan){
    //     $this->penjualanModel = $penjualan;
    // }

    public function index() {
        return view("index");
    }

    public function getPenjualan(Request $request) {
        $sidx = $request->input('sidx', 'no_bukti');
        $sord = $request->input('sord', 'asc');
        $rows = $request->input('rows', 10);
        $page = $request->input('page', 1);
        $filters = $request->input('filters', null);

        $start = ($page - 1) * $rows;
        $penjualanModel = new Penjualan;

        try {
            $result = $penjualanModel->getAllPenjualan($sidx, $sord, $rows, $start, $filters);
            $count = $result['count'];

            $totalPage = $count > 0 ? ceil($count / $rows) : 0;
            // dd($sidx, $sord, $rows, $start, $totalPage, $page, $count, $result['data']);

            return response()->json([
                'total' => $totalPage,
                'page' => $page,
                'records' => $count,
                'rows' => $result['data']
            ]);
            
        } catch (\Throwable $th) {
            dd($th);
            return response("Failed to get data!");
        }
    }

    public function getDetail(Request $request, $id){
        $detailModel = new DetailPenjualan;

        return $detailModel->getDetailById($request, $id);
    }

    public function createPenjualan(Request $request){
        // if (!$request->input('tgl_bukti') || !$request->input('id_pelanggan') || !$request->input('total')) {
        //     return response('Harap isi semua data', 400);
        // }

        $validator = Validator::make($request->all(),[
            'tgl_bukti' => 'required',
            'id_pelanggan' => 'required',
            'total' => 'required',
            'nama_barang.*' => 'required',
            'quantity.*' => 'required',
            'harga.*' => 'required'
        ]);
        if ($validator->fails()) {
            return response('Harap isi semua data', 400);
        }

        $penjualanModel = new Penjualan();

        DB::beginTransaction();
        try {
            $result = $penjualanModel->createPenjualan($request);
            DB::commit();
            return response()->json([
                'id' => $result['id'],
                'count' => $result['count']
            ]);
            
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function getPenjualanById(Request $request, $id){
        $penjualanModel = new Penjualan;

        try {
            $result = $penjualanModel->getPenjualanById($request, $id);
            return $result;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updatePenjualan(Request $request, $id){
        $sname = $request->input('sname', 'no_bukti');
        $sorder = $request->input('sorder', 'asc');
        $penjualanModel = new Penjualan();

        $validator = Validator::make($request->all(),[
            'tgl_bukti' => 'required',
            'id_pelanggan' => 'required',
            'total' => 'required',
            'nama_barang.*' => 'required',
            'quantity.*' => 'required',
            'harga.*' => 'required'
        ]);
        if ($validator->fails()) {
            return response('Harap isi semua data', 400); 
        }

        DB::beginTransaction();
        try {
            $result = $penjualanModel->updatePenjualan($sname, $sorder, $request, $id);
            // dd($sname, $sorder, $result['count']);

            DB::commit();
            return response()->json([
                'count' => $result['count']
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                dd($th),
                "error" => $th,
                "message" => "failed to update data"
            ]);
        }
    }

    public function deletePenjualan(Request $request, $id){
        $penjualanModel = new Penjualan();

        DB::beginTransaction();
        try {
            $res = $penjualanModel->deletePenjualan($request, $id);

            DB::commit();

            return response()->json([
                'id' => $res['id'],
                'count' => $res['count'],
                // 'message' => "Success delete data penjualan"
            ], 200);

        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'error' => "Failed to delete data penjualan",
                'message' => $e->getMessage()
            ], 400);
        }
    }

}


?>