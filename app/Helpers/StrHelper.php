<?php

namespace App\Helpers;

use App\Models\Attachment;
use App\Models\User;

class StrHelper
{
    /**
     * @param $value
     *
     * @return bool
     */
    public static function isObjectId($value):bool
    {
        if ($value instanceof \MongoDB\BSON\ObjectID) {
            return true;
        }
        try {
            new \MongoDB\BSON\ObjectID($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param          $data
     * @param string   $md5Key
     *
     * @return string
     */
    public static function securedEncrypt($data,string $md5Key):string
    {
        $data= openssl_encrypt($data, 'aes-256-cbc', $md5Key, OPENSSL_RAW_DATA, substr($md5Key,0,16));
        return base64_encode($data);
    }

    /**
     * @param string $dataEncrypt
     * @param string $md5Key
     *
     * @return string
     */
    public static function securedDecrypt(string $dataEncrypt,string $md5Key):string
    {
        return openssl_decrypt(base64_decode($dataEncrypt), 'aes-256-cbc', $md5Key, OPENSSL_RAW_DATA, substr($md5Key,0,16));
    }

    /**
     * @param \App\Models\Attachment|null $attachment
     *
     * @return string
     */
    public static function urlFromAttachment(?Attachment $attachment):string
    {
        if(is_null($attachment)) return GoogleCloudStorageHelper::getUrl().User::AVATAR_DEFAULT_URL;
        return GoogleCloudStorageHelper::getUrl($attachment->bucket ?? "").$attachment->path;
    }
}
