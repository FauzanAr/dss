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
      // $mahasiswa_all = app('db')->select("SELECT * FROM mhs");
      // $point_all = app('db')->select("SELECT * FROM point");

      $point_all = MahasiswaController::transform_to_array();

      // $point_all = $point_all->map(function($value){
      //    // return (array) $value;
      //    return $value->toArray();
      // });
      // dd($point_all);
      // $point = count($point_all);
      return $point_all;
      
      // return response()->json($point_all);
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
               $res['status'] = true;
               $res['message'] = "Data Berhasil Di Inputkan";
               return response($res, 200);
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
         $point_all = MahasiswaController::get_point();
         $point_ipk = 0;
         $point_penghasilan_orangtua = 0;
         $point_tagihan_listrik = 0;
         $point_prestasi = 0;
         $point_bahasa = 0;

         for ($i=0; $i < count($point_all); $i++) { 
            $point_ipk = $point_ipk + ($point_all[$i]['point_ipk'] * $point_all[$i]['point_ipk']);
            $point_penghasilan_orangtua = $point_penghasilan_orangtua + ($point_all[$i]['point_penghasilan_orangtua'] * $point_all[$i]['point_penghasilan_orangtua']);
            $point_tagihan_listrik = $point_tagihan_listrik + ($point_all[$i]['point_tagihan_listrik'] * $point_all[$i]['point_tagihan_listrik']);
            $point_prestasi = $point_prestasi + ($point_all[$i]['point_prestasi'] * $point_all[$i]['point_prestasi']);
            $point_bahasa = $point_bahasa + ($point_all[$i]['point_bahasa'] * $point_all[$i]['point_bahasa']);
         }

         $array = array('point_ipk' => $point_ipk, 
                        'point_penghasilan_orangtua' => $point_penghasilan_orangtua, 
                        'point_tagihan_listrik' => $point_tagihan_listrik, 
                        'point_prestasi' => $point_prestasi, 
                        'point_bahasa' => $point_bahasa);
         $res['data'] = $array;
         $res['point'] = $point_all;
         return $res;
     }

     private function normalisasi_2()
     {
        $data = MahasiswaController::normalisasi_1();
        $data_point = $data['point'];
        $data = $data['data'];
        for ($i=0; $i < count($data_point) ; $i++) { 
           $data_point[$i]['point_ipk'] = $data_point[$i]['point_ipk']/sqrt($data['point_ipk']);
           $data_point[$i]['point_penghasilan_orangtua'] = $data_point[$i]['point_penghasilan_orangtua']/sqrt($data['point_penghasilan_orangtua']);
           $data_point[$i]['point_tagihan_listrik'] = $data_point[$i]['point_tagihan_listrik']/sqrt($data['point_tagihan_listrik']);
           $data_point[$i]['point_prestasi'] = $data_point[$i]['point_prestasi']/sqrt($data['point_prestasi']);
           $data_point[$i]['point_bahasa'] = $data_point[$i]['point_bahasa']/sqrt($data['point_bahasa']);
        }

        return $data_point;
     }

     private function normalisasi_bobot()
     {
        $data = MahasiswaController::normalisasi_2();
        for ($i=0; $i < count($data); $i++) { 
           $data[$i]['point_ipk']                   = 5 * $data[$i]['point_ipk'];
           $data[$i]['point_penghasilan_orangtua']  = 4 * $data[$i]['point_penghasilan_orangtua'];
           $data[$i]['point_tagihan_listrik']       = 3 * $data[$i]['point_tagihan_listrik'];
           $data[$i]['point_prestasi']              = 2 * $data[$i]['point_prestasi'];
           $data[$i]['point_bahasa']                = 1 * $data[$i]['point_bahasa'];
        }

        return $data;
     }

     private function normalisasi_ideal_positif_negatif()
     {
        $data = MahasiswaController::normalisasi_bobot();
        $data_point = array();
        for ($i=0; $i < count($data); $i++) { 
           for ($j=0; $j < 5; $j++) { 
              if ($i == 0) {
                 $data_point['positif'][$j] = $data;
              }
           }
        }
     }
}
