<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleCloudStorageHelper
{
    /**
     * @param        $fileContent
     * @param string $name
     * @param string $region
     * @param bool   $custom
     *
     * @return string[]
     */
    public static function upload($fileContent, string $name, string $region="ASIA", bool $custom=false):array
    {
        try{
            $path = date("Ymd")."/".Str::uuid()."-".$name;
            $disk = Storage::disk('gcs');
            $disk->put($path,$fileContent);
//            $disk->setVisibility($path, 'public');

            return [
                'path' => "/$path",
                'full_url' => self::getUrl($region)."/$path",
                'region' => $region,
                'disk' => 'gcs',
                'bucket_name' => config('filesystems.disks.gcs.bucket')
            ];
        }catch (\Exception $exception){
            dd($exception);
        }
//        dd(config('filesystems.disks.gcs'));

    }

    /**
     * @param string $region
     *
     * @return string
     */
    public static function getUrl(string $region = ""):string
    {
        return trim(config('filesystems.disks.gcs.apiEndpoint_2'),"/");
    }
}
