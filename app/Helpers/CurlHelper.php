<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CurlHelper
{
    /**
     * @param string $accessToken
     *
     * @return array|null
     */
    public static function accessTokenFacebook(string $accessToken): ?array
    {
        $data = [
            [
                "full_name" => "account 0",
                "fb_uid" => "2326836650792103"
            ],
            [
                "full_name" => "account 1",
                "fb_uid" => "2326836650792104"
            ]
        ];
        return $data[(int)$accessToken];
        $curl = Http::get("https://graph.facebook.com/v13.0/me?access_token=$accessToken")->json();
        if (Arr::get($curl, 'id') === null) {
            return null;
        }

        return [
            'fb_uid' => Arr::get($curl, 'id'),
            'full_name' => Arr::get($curl, 'name')
        ];
    }
}
