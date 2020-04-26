<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

Class Point extends Model 

{
    protected $table = 'point';
    protected $fillable = ['nama_mahasiswa', 'ipk', 'tagihan_listrik', 'prestasi', 'bahasa_asing', 'penghasilan_orangtua', 'score'];
}