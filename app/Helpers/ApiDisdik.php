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
        $response = Http::get(env('API_DISDIK_URL')."/sekolah/".env('API_DISDIK_TOKEN')."/".$kode_kecamatan);
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
        $response = Http::get(env('API_DISDIK_URL')."/pd/".env('API_DISDIK_TOKEN')."/".$npsn);
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
        $response = Http::get(env('API_DISDIK_URL')."/cek_pd/".env('API_DISDIK_TOKEN')."/".$niknisn);
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