<?php

namespace App\Http\Controllers;

use App\Models\Pesantren;

abstract class Controller
{
    protected function getPesantren(): ?Pesantren
    {
        $pesantren = Pesantren::first();

        if (!$pesantren && request()->route() && !request()->routeIs('admin.konfigurasi.*')) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->route('admin.konfigurasi.index')
                    ->with('error', 'Silakan lengkapi Profil Pesantren terlebih dahulu sebelum menggunakan fitur lain.')
            );
        }

        return $pesantren;
    }
}
