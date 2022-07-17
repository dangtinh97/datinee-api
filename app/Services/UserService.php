<?php

namespace App\Services;

use App\Helpers\GoogleCloudStorageHelper;
use App\Helpers\StrHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseSuccess;
use App\Models\User;
use App\Models\UserImage;
use App\Repositories\FavoriteRepository;
use App\Repositories\SetupAppRepository;
use App\Repositories\UserImageRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class UserService
{
    protected FavoriteRepository $favoriteRepository;

    protected UserRepository $userRepository;

    protected UserImageRepository $userImageRepository;

    protected SetupAppRepository $setupAppRepository;

    private $lang = "vi";

    public function __construct(
        FavoriteRepository $favoriteRepository,
        UserRepository $userRepository,
        UserImageRepository $userImageRepository,
        SetupAppRepository $setupAppRepository
    ) {
        $this->favoriteRepository = $favoriteRepository;
        $this->userRepository = $userRepository;
        $this->userImageRepository = $userImageRepository;
        $this->setupAppRepository = $setupAppRepository;
        if(request()->header('lang')==="en") $this->lang="en";
    }

    /**
     * @param array $data
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function storeFavorite(array $data): ApiResponse
    {
        if (count($data) === 0) {
            return new ResponseSuccess([], "Thêm sở thích để mọi người biết rõ về bạn hơn.");
        }

        $favorites = $this->favoriteRepository->find([
            'user_id' => Auth::user()->id
        ])->toArray();

        $list = array_filter($data, function ($key) use ($favorites) {
            return array_search($key, array_column($favorites, 'key')) === false;
        });

        if (count($list) === 0) {
            return new ResponseSuccess([], "Thêm sở thích để mọi người biết rõ về bạn hơn.");
        }

        $add = array_map(function ($key) {
            return [
                'user_id' => Auth::user()->id,
                'user_oid' => new ObjectId(Auth::user()->_id),
                'key' => $key,
                'created_at' => new UTCDateTime(time() * 1000),
                'updated_at' => new UTCDateTime(time() * 1000)
            ];
        }, array_values($list));

        $this->favoriteRepository->insert($add);

        return new ResponseSuccess([], "Thêm sở thích thành công.");
    }

    /**
     * @param array $data
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function updateInfo(array $data): ApiResponse
    {
        $data = array_filter($data,function ($value,$key){
            return !empty($value);
        },ARRAY_FILTER_USE_BOTH);

        $listFavorite = $data['list_favorite'] ?? [];
        Arr::forget($data, 'list_favorite');

        if (!is_null(Arr::get($data, 'avatar')) && StrHelper::isObjectId(Arr::get($data, 'avatar'))) {
            $data = array_merge($data, [
                'avatar' => new ObjectId(Arr::get($data, 'avatar'))
            ]);
        }

        $this->userRepository->findOneAndUpdate([
            '_id' => new ObjectId(Auth::user()->_id)
        ], [
            '$set' => array_merge($data, [
                'region' => request()->header('lang') === "vi" ? "ASIA" : "USA"
            ])
        ]);
        $this->storeFavorite($listFavorite);

        return new ResponseSuccess([], "Cập nhật thông tin thành công.");
    }

    /**
     * @param array $images
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function storeImage(array $images): ApiResponse
    {
        $insert = [];
        foreach ($images as $str) {
            if (StrHelper::isObjectId($str)) {
                $insert[] = [
                    'attachment_oid' => new ObjectId($str),
                    'user_id' => Auth::user()->id,
                    'user_oid' => new ObjectId(Auth::user()->_id),
                    'type' => UserImage::TYPE_PHOTO,
                    'status' => UserImage::STATUS_NORMAL
                ];
            }
        }
        if (count($insert) === 0) {
            return (new ResponseSuccess([], "Bạn chưa chọn ảnh."));
        }

        $this->userImageRepository->insert($insert);

        return new ResponseSuccess([], "Thêm ảnh thành công.");
    }

    /**
     * @param array $addImages
     * @param array $deleteImages
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function updateImage(array $addImages, array $deleteImages): ApiResponse
    {
        $this->storeImage($addImages);
        $this->userImageRepository->deleteImages($deleteImages);

        return new ResponseSuccess();
    }

    public function infoMe(): ApiResponse
    {
        /**
         *         "user_oid": "624004ce60ac1090019ab4b1",
         * "user_id": 111,
         * "full_name": "Đăng Tính Official",
         * "age": 23,
         * "address": "",
         * "avatar": "https://storage.googleapis.com/datinee-dev/2022328/aaecb2fb-9cc6-4f44-9f05-4ad763bb3110tumblr_bcc875cb54010cef943ffa80604bf127_1e4a1426_500.jpeg",
         * "latitude": 21.5773739,
         * "longitude": 105.7684703,
         * "images": [
         * {
         * "image_oid": "6240fe20a1b8f156cd21995a",
         * "attachment_oid": "6240fe0aa1b8f156cd219954",
         * "url": "https://storage.googleapis.com/datinee-dev/2022328/aaecb2fb-9cc6-4f44-9f05-4ad763bb3110tumblr_bcc875cb54010cef943ffa80604bf127_1e4a1426_500.jpeg"
         * }
         * ],
         * "favorites": [
         * {
         * "key": "READING_BOOKS",
         * "label": "đọc sách"
         * },
         * {
         * "key": "LISTEN_TO_MUSIC",
         * "label": "nghe nhạc"
         * }
         * ],
         * "introduce": "",
         * "gender": ""
         */
        $user = $this->userRepository->infoMe(Auth::user()->_id);

        $favoritesConfig = $this->setupAppRepository->findOne([
                'type' => 'LIST_FAVORITE'
            ])->data ?? [];
        $avatar = GoogleCloudStorageHelper::getUrl($user->region ?? "") . (!empty($user->avatars) ? $user->avatars[0]['path'] : User::AVATAR_DEFAULT_URL);
        $favorites = [];

        $userFavorites = array_column($user->favorites ?? [],'key');

        foreach ($userFavorites as $key)
        {
            $search = array_search($key , array_column($favoritesConfig,'key'));
            if($search===false) continue ;
            $favorites[] = [
                'key' => $key,
                'label' => $favoritesConfig[$search][$this->lang]
            ];
        }
        $images = [];

        foreach ($user->images ?? [] as $img){
            if(empty($img['attachments'])) continue;
            $images[] = [
                'image_oid' => $img['_id']->__toString(),
                'attachment_oid' => $img['attachment_oid']->__toString(),
                'url' => GoogleCloudStorageHelper::getUrl().$img['attachments'][0]['path']
            ];
        }

        return new ResponseSuccess([
            'user_oid' => $user->_id,
            'user_id' => $user->id,
            'gender' => Arr::get($user, 'gender', ''),
            'address' => Arr::get($user, 'address', ''),
            'age' => Arr::get($user, 'age', ''),
            'introduce' => Arr::get($user, 'introduce', ''),
            'latitude' => Arr::get($user->location, 'coordinates.1' ?? ""),
            'longitude' => Arr::get($user->location, 'coordinates.0' ?? ""),
            'avatar' => $avatar,
            'favorites' => $favorites,
            'images' => $images
        ]);
    }
}
