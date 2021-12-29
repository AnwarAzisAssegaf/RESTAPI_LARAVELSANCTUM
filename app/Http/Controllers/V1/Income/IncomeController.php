<?php

namespace App\Http\Controllers\V1\Income;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use Validator;
use Aws\S3\S3Client;

class IncomeController extends Controller
{







    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required'
        ]);

         $this->validate($request, [
            'category'          => 'required',
            'total'             => 'required',
            'transaction'       => 'required|min:3',
           // 'file_bukti'       => 'mimes:pdf,xlsx|max:5000'
       ]); 
		    
       
        $category               = $request->get('category');
        $transaksi              = $request->get('transaction');
        $jumlah                 = str_replace('.', '', $request->get('total'));
        $date                   = $request->get('date');
        $tanggal                = date("Y-m-d", strtotime($date) );
        $tanggal_buat           = date('Y-m-d');
		$bulanx 				= $tanggal;
        $date 					= explode('-',$bulanx);
        $tahun 					= $date[0]; 
		$bulan 					= $date[1];

       
        
        if($request->get('file_bukti')){
            $file_name          = $request->get('file_bukti');   
        // var_dump($file_name);
            $original_file_name = $file_name->getClientOriginalName();
            $extension          = $file_name->getClientOriginalExtension();
            $fileWithoutExt     = str_replace(".","",basename($original_file_name, $extension));  
            $updated_fileName   = $fileWithoutExt."_".rand(0,99).".".$extension; 
            //$file_name->move("images/pemasukan", $updated_fileName);

        }
        else{
            $updated_fileName  = '';
        } 

        try {
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => 'ap-southeast-1',
                'endpoint' => 'https://aws.com',
                'Bucket' => 'restapi',
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key' => 'restapi',
                    'secret' => 'YCewxk7bGTAdL5KCboRFLoNizOYXZQjD',
                ],
            ]);
            // dd($s3);

            $s3->putObject(array(
                'Bucket' => "restapi",
                'Key' => 'upload/file/' . $updated_fileName,
                'SourceFile' => $request->file('file_bukti')->getRealPath(),
            ));

        } catch (\Exception $exception) {
            throw new \Exception('File could not upload to stook account.');
        }
       

        //memasukan data ke tabel pemasukan pada database
        $query = new Income();
        $query->transaksi       = $transaksi;
        $query->jumlah          = $jumlah;
        $query->file_bukti      = $updated_fileName;
        $query->tanggal         = $tanggal;
		$query->bulanx			= $bulan;
		$query->tahunx			= $tahun;
        $query->tanggal_buat    = $tanggal_buat;
        $query->save();
        
        

        return response()->json([
                'message'       => 'Income Berhasil Ditambahkan',
                'result'  => $query
            ], 200);
    }
	
	 public function edit($id)
    {
        $income = Income::find($id);
        return response()->json([
                'message'       => 'success',
                'result'  => $income
            ], 200);
    }

    

    public function delete($id)
    {
        Income::find($id)->delete();
        return response()->json([
                'message'       => 'data Income berhasil dihapus'
            ], 200);
    }
}
