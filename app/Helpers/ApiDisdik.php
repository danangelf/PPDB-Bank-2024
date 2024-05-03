<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;



class ApiDisdik
{
    public static function synchKecamatan()
    {
        $response = Http::withoutVerifying()->get(env('URL_API_DISDIK')."/kecamatan/".env('TOKEN_API_DISDIK'));
        if($response->failed()){
            return array(
                "error" => true,
                "message" => "failed response from api"
            );
        }

        return array(
            "error" => false,
            "message" => "success",
            "response" => $response->json()
        );
    }
    public static function synchSekolah($kode_kecamatan)
    {
        $response = Http::withoutVerifying()->get(env('URL_API_DISDIK')."/sekolah/".env('TOKEN_API_DISDIK')."/".$kode_kecamatan);
        if($response->failed()){
            return array(
                "error" => true,
                "message" => "failed response from api"
            );
        }

        return array(
            "error" => false,
            "message" => "success",
            "response" => $response->json()
        );
    }
    public static function synchPesertaDidik($npsn)
    {
        $response = Http::withoutVerifying()->get(env('URL_API_DISDIK')."/pd/".env('TOKEN_API_DISDIK')."/".$npsn);
        if($response->failed()){
            return array(
                "error" => true,
                "message" => "failed response from api"
            );
        }

        return array(
            "error" => false,
            "message" => "success",
            "response" => $response->json()
        );
    }

    public static function synchPesertaDidikSingle($niknisn)
    {
        $response = Http::get(env('URL_API_DISDIK')."/cek_pd/".env('TOKEN_API_DISDIK')."/".$niknisn);
        if($response->failed()){
            return array(
                "error" => true,
                "message" => "failed response from api"
            );
        }

        return array(
            "error" => false,
            "message" => "success",
            "response" => $response->json()
        );
    }

}