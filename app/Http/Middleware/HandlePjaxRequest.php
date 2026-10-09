<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menangani request PJAX (navigasi parsial dari resources/js/ajax-router.js).
 *
 * Masalah: PJAX mengirim header X-Requested-With sehingga Laravel menganggapnya
 * AJAX dan TIDAK menyimpan "previous URL" ke session. Akibatnya redirect()->back()
 * dan validasi gagal selalu kembali ke halaman terakhir yang di-load penuh,
 * bukan ke halaman yang sedang dibuka.
 */
class HandlePjaxRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->header('X-PJAX') !== 'true') {
            // Halaman penuh dan parsial memakai URL yang sama -> bedakan cache-nya
            $response->headers->set('Vary', 'X-PJAX', false);
            return $response;
        }

        // Simpan URL halaman yang sedang dibuka sebagai previous URL
        if ($request->isMethod('GET')
            && $request->hasSession()
            && $response->isSuccessful()
        ) {
            $request->session()->setPreviousUrl($request->fullUrl());
        }

        // Jangan biarkan browser menyimpan respons parsial (mencegah halaman
        // tanpa layout saat tombol Back/Forward atau buka ulang tab)
        $response->headers->set('Vary', 'X-PJAX', false);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('X-PJAX-URL', $request->fullUrl());

        return $response;
    }
}
