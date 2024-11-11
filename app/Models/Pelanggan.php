<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pelanggan extends Model
{
    // use HasFactory; 

    protected $table = "tb_pelanggan";

    protected $fillable = [
        'nama_pelanggan',
        'alamat',
        'no_hp'
    ];

    public $timestamps = "false";

    public function getAllPelanggan($request){
        $q = $request->input('q', "");

        $pelanggan = DB::table('tb_pelanggan')
        ->where('nama_pelanggan', 'like', '%' . $q . '%')
        ->orderBy('nama_pelanggan', 'asc')
        ->get();
        return $pelanggan;
    }
}
