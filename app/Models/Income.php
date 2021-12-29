<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class Income extends Model
{
    //menghubungkan tabel database
    protected $table = 'pemasukan';
    protected $primaryKey = 'id';
    protected $fillable = ['transaksi','jumlah','file_bukti','tanggal','bulanx','tahunx','tgl_buat','user_id'];
    public $timestamps = false;
}
   

