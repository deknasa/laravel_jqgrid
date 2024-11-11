<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function getAllPelanggan(Request $request) {
        $pelangganModel = new Pelanggan;

        return $pelangganModel->getAllPelanggan($request);
    }
    
}


?>