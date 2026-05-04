<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /*Di Laravel 11+, file ini sengaja dibikin minimal (abstract, kosong). Alasan tim Laravel:

    Helper-nya pindah ke pola yang lebih eksplisit. $this->validate() digantikan $request->validate() (sudah lama jadi konvensi). $this->authorize() digantikan Gate::authorize() atau dilakukan via FormRequest. $this->dispatch() digantikan helper global dispatch().
    Mengurangi inheritance magic. Controller jadi class biasa tanpa "warisan tersembunyi" yang harus diingat developer baru.
    Tetap ada sebagai extension point. Kalau Anda mau tambah trait/method yang dipakai semua controller (mis. response helper, audit log scope), Anda tinggal isi di sini.
    */
}
