<?php

namespace App\Http\Controllers\V1\Income;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Income;
use Validator;

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
        $kredit_akun            = $request->get('account');
        $transaksi              = $request->get('transaction');
        $jumlah                 = str_replace('.', '', $request->get('total'));
        $date                   = $request->get('date');
        $tanggal                = date("Y-m-d", strtotime($date) );
        $tanggal_buat           = date('Y-m-d');
        $user                   = auth()->user();
        $unit_id                = $user->unit_id;
		$cabang_id              = $user->cabang_id;
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
            $file_name->move("images/pemasukan", $updated_fileName);

        }
        else{
            $updated_fileName  = '';
        } 
       

        //memasukan data ke tabel pemasukan pada database
        $query = new Income();
        $query->jenis_id        = $category;
        $query->unit_id         = $unit_id;
		$query->cabang_id       = $cabang_id;
        $query->account_id      = $kredit_akun;
		$query->laznas_type    = '0';
        $query->transaksi       = $transaksi;
        $query->jumlah          = $jumlah;
        $query->file_bukti      = $updated_fileName;
        $query->tanggal         = $tanggal;
		$query->bulanx			= $bulan;
		$query->tahunx			= $tahun;
        $query->tanggal_buat    = $tanggal_buat;
        $query->save();
        
        if($query){

            $mutasi = new MutasiKas();
            $mutasi->ref             = $category;
            $mutasi->unit_id         = $unit_id;
			$mutasi->jenis_id        = $category;
            $mutasi->pemasukan_id    = $query->id;
            $mutasi->pengeluaran_id  = 0;
            $mutasi->account_id      = $kredit_akun;
            $mutasi->transaksi       = $transaksi;
            $mutasi->debit           = $jumlah;
            $mutasi->kredit          = '0';
            $mutasi->tanggal         = $tanggal;
			$mutasi->bulan           = $bulan;
			$mutasi->tahun           = $tahun;
            $mutasi->save();
        }

        return response()->json([
                'message'       => 'Income Berhasil Ditambahkan',
                'data_pemasukan'  => $mutasi
            ], 200);
    }
	
	 public function edit($id)
    {
        $pemasukan = Income::find($id);
        return response()->json([
                'message'       => 'success',
                'data_pemasukan'  => $pemasukan
            ], 200);
    }

    

    public function delete($id)
    {
        $pemasukan = Income::find($id)->delete();
		$delmutasi = MutasiKas::where('pemasukan_id', $id)->first();
            $delmutasi->delete();

        return response()->json([
                'message'       => 'data Income berhasil dihapus'
            ], 200);
    }
}
