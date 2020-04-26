<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

Class Mahasiswa extends Model 

{
    protected $table = 'mhs';
    protected $fillable = ['point_ipk','point_penghasilan_orangtua','point_tagihan_listrik','point_prestasi', 'point_bahasa'];
}