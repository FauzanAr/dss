<?php
namespace App\Http\Controllers;

use App\Mahasiswa;
use App\Point;
use Illuminate\Http\Request;
use DB;


class MahasiswaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
      $mhs_all = DB::table('mhs')
                  ->orderBy('score', 'desc')
                  ->get();
      return response()->json($mhs_all);
    }
     public function create(Request $request)
     {
        $mhs = new Mahasiswa;
        $point = new Point; 

        $mhs->nama_mahasiswa                 = $request->nama_mahasiswa;
        $mhs->ipk                            = $request->ipk;
        $mhs->tagihan_listrik                = $request->tagihan_listrik;
        $mhs->prestasi                       = $request->prestasi;
        $mhs->bahasa_asing                   = $request->bahasa_asing;
        $mhs->penghasilan_orangtua           = $request->penghasilan_orangtua;
        $mhs->score                          = 0;

        $point->point_ipk                    = MahasiswaController::convert_ipk($mhs->ipk);
        $point->point_penghasilan_orangtua   = MahasiswaController::convert_gaji_orangtua($mhs->penghasilan_orangtua);
        $point->point_tagihan_listrik        = MahasiswaController::convert_tagihan_listrik($mhs->tagihan_listrik);
        $point->point_prestasi               = MahasiswaController::convert_prestasi_dan_bahasa($mhs->prestasi);
        $point->point_bahasa                 = MahasiswaController::convert_prestasi_dan_bahasa($mhs->bahasa_asing);
        
        try {
         if($point->save())
         {
            if($mhs->save()){
               $score_save = MahasiswaController::calculate();
               if($score_save["status"]){
                  $res['status'] = true;
                  $res['message'] = "Data Berhasil Di Inputkan";
                  return response($res, 200);
               }else {
                  $res['status'] = false;
                  $res['message'] = "Score tidak berhasil di input";
                  return response($res, 500);
               }
            }
            else {
               $res['status'] = false;
               $res['message'] = "Data mahasiswa tidak dapat di input";
               return response($res, 500);
            }
         }
         else {
            $res['status'] = false;
            $res['message'] = "Data point tidak dapat di input";
            return response($res, 500);
         }
        } catch (\Illuminate\Database\QueryException $ex) {
           $res['status'] = false;
           $res['message'] = $ex->getMessage();
           return response($res, 500);
        }
     }

     public function show($id)
     {
        
     }

     public function update(Request $request, $id)
     { 
        
     }

     public function destroy($id)
     {
        
     }

     private function convert_ipk($value)
     {
        if($value <= 3.45)
        {
           return 2;
        }
        else if ($value <= 3.60)
        {
           return 3;
        }
        else if ($value <= 3.80)
        {
           return 4;
        }
        else if ($value <= 4) {
           return 5;
        }
        else
        {
           return 0;
        }
     }

     private function convert_gaji_orangtua($value)
     {
        if ($value <= 1000000)
        {
           return 5;
        }
        elseif ($value <= 2500000) {
           return 4;
        }
        elseif ($value <= 4000000) {
           return 3;
        }
        elseif ($value > 4000000) {
           return 2;
        }else {
           return 0;
        }
     }

     private function convert_tagihan_listrik($value)
     {
        if ($value <= 50000) {
           return 5;
        }
        elseif ($value <= 150000) {
           return 4;
        }
        elseif ($value <= 250000) {
           return 3;
        }
        elseif ($value > 250000) {
           return 2;
        }
        else {
           return 0;
        }
     }

     private function convert_prestasi_dan_bahasa($value)
     {
         if($value > 20 && $value <= 40)
         {
            return 1;
         }
         elseif ($value <= 60) {
            return 2;
         }
         elseif ($value <= 80) {
            return 3;
         }
         elseif ($value <= 90) {
            return 4;
         }
         elseif ($value <= 100) {
            return 5;
         }
         else {
            return 0;
         }
     }

     private function calculate()
     {
        $data = MahasiswaController::nilai_preferensi();
        $preferensi = $data['preferensi'];
        
        try {
            for ($i=0; $i < count($preferensi); $i++) { 
               if ($preferensi[$i]['data'] == 1) {
                     $preferensi[$i]['data'] = 0.999;
               }
               DB::table('mhs')
                  ->where('id', $preferensi[$i]['id'])
                  ->update(['score'=> $preferensi[$i]['data']]);
            }
            $res['status'] = true;
            $res['message'] = "Success";
            return $res;
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['status'] = false;
            $res['message'] = $ex->getMessage();
            return $res;
        }
     }

     private function get_point()
     {
      return $point_all = DB::table('point')
                           ->get()
                           ->map(function($value){
                              return (array) $value;
                           })
                           ->all();
     }

     private function transform_to_array()
     {
         $data = MahasiswaController::get_point();
         $tmp = array();
         for ($i=0; $i < count($data); $i++) { 
            for ($j=0; $j < 6; $j++) { 
               if ($j==0) {
                  $tmp[$i][$j] = $data[$i]['id'];
               }elseif ($j==1) {
                  $tmp[$i][$j] = $data[$i]['point_ipk'];
               }elseif ($j==2) {
                  $tmp[$i][$j] = $data[$i]['point_penghasilan_orangtua'];
               }elseif ($j==3) {
                  $tmp[$i][$j] = $data[$i]['point_tagihan_listrik'];
               }elseif ($j==4) {
                  $tmp[$i][$j] = $data[$i]['point_prestasi'];
               }elseif ($j==5) {
                  $tmp[$i][$j] = $data[$i]['point_bahasa'];
               }
            }
         }

         return $tmp;
     }

     private function normalisasi_1()
     {
         $point_all = MahasiswaController::transform_to_array();
         $tmp = array();

         for ($i=0; $i < count($point_all); $i++) { 
            for ($j=0; $j < count($point_all[$i]); $j++) { 
               if ($j==0) {
               }else {
                  if ($i==0) {
                     $tmp[$j] = $point_all[$i][$j] * $point_all[$i][$j];
                  }else {
                     $tmp[$j] = $tmp[$j] + ($point_all[$i][$j] * $point_all[$i][$j]);
                  }
               }
            }
         }
         $res['data'] = $tmp;
         $res['point'] = $point_all;
         return $res;
     }

     private function normalisasi_2()
     {
        $data = MahasiswaController::normalisasi_1();
        $data_point = $data['point'];
        $data = $data['data'];

        for ($i=0; $i < count($data_point); $i++) { 
           for ($j=0; $j <= count($data); $j++) {
              if ($j != 0) {
                  $data_point[$i][$j] = $data_point[$i][$j]/sqrt($data[$j]);
              } 
           }
        }

        return $data_point;
     }

     private function normalisasi_bobot()
     {
        $bobot = [5,4,3,2,1];
        $data = MahasiswaController::normalisasi_2();
        for ($i=0; $i < count($data); $i++) { 
           for ($j=0; $j < count($data[$i]); $j++) {
              if ($j != 0) {
                  $data[$i][$j] = $data[$i][$j] * $bobot[$j-1];
              } 
           }
        }

        return $data;
     }

     private function normalisasi_ideal_positif_negatif()
     {
        $data_normalisasi = MahasiswaController::normalisasi_bobot();
        $data_point = array();
        $data = array();
        for ($i=0; $i < count($data_normalisasi); $i++) { 
           for ($j=0; $j < count($data_normalisasi[$i]); $j++) { 
              if ($j != 0) {
                 if ($i == 0) {
                    $data_point['positif'][$j] = $data_normalisasi[$i][$j];
                    $data_point['negatif'][$j] = $data_normalisasi[$i][$j];
                 }else {
                    if ($data_point['positif'][$j] < $data_normalisasi[$i][$j]) {
                        $data_point['positif'][$j] = $data_normalisasi[$i][$j];
                    }elseif($data_point['negatif'][$j] > $data_normalisasi[$i][$j]){
                        $data_point['negatif'][$j] = $data_normalisasi[$i][$j];
                    }
                 }
              }
           }
        }

        $data['normalisasi'] = $data_normalisasi;
        $data['positif_negatif'] = $data_point;
        
        return $data;
     }

     private function jarak_ideal_positif_negatif()
     {
        $data = MahasiswaController::normalisasi_ideal_positif_negatif();
        $tmp_data = array();
        for ($i=0; $i < count($data['normalisasi']); $i++) {
            for ($j=0; $j < count($data['normalisasi'][$i]); $j++) { 
               if($j!=0){
                  if ($j == 1) {
                     $tmp_data['positif'][$i] = ($data['normalisasi'][$i][$j]-$data['positif_negatif']['positif'][$j]) * ($data['normalisasi'][$i][$j]-$data['positif_negatif']['positif'][$j]);
                     $tmp_data['negatif'][$i] = ($data['normalisasi'][$i][$j]-$data['positif_negatif']['negatif'][$j]) * ($data['normalisasi'][$i][$j]-$data['positif_negatif']['negatif'][$j]);
                  }else {
                     $tmp_data['positif'][$i] = $tmp_data['positif'][$i] + ($data['normalisasi'][$i][$j]-$data['positif_negatif']['positif'][$j]) * ($data['normalisasi'][$i][$j]-$data['positif_negatif']['positif'][$j]);
                     $tmp_data['negatif'][$i] = $tmp_data['negatif'][$i] + ($data['normalisasi'][$i][$j]-$data['positif_negatif']['negatif'][$j]) * ($data['normalisasi'][$i][$j]-$data['positif_negatif']['negatif'][$j]);
                  }
               }
            }
         }

         for ($i=0; $i < count($tmp_data['positif']); $i++) { 
            $tmp_data['positif'][$i] = sqrt($tmp_data['positif'][$i]);
            $tmp_data['negatif'][$i] = sqrt($tmp_data['negatif'][$i]);
         }
         $data['positif_negatif'] = $tmp_data;
        return $data;
     }

     private function nilai_preferensi()
     {
        $data = MahasiswaController::jarak_ideal_positif_negatif();
        $nilai_preferensi = array();
        
        for ($i=0; $i < count($data['positif_negatif']['positif']); $i++) { 
            $nilai_preferensi[$i]['id'] = $data['normalisasi'][$i][0];
            $nilai_preferensi[$i]['data'] = round($data['positif_negatif']['negatif'][$i]/($data['positif_negatif']['positif'][$i]+$data['positif_negatif']['negatif'][$i]),3);
        }
        $data['preferensi'] = $nilai_preferensi;

        return $data;
     }
}
