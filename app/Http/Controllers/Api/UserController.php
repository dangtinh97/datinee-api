<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Requests\UpdateInfoMeRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param \App\Http\Requests\StoreFavoriteRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeFavorite(StoreFavoriteRequest $request):JsonResponse
    {
        $listChoice = $request->get('list_choice', []);
        $add = $this->userService->storeFavorite($listChoice);

        return response()->json($add->toArray());
    }

    /**
     * @param \App\Http\Requests\UpdateInfoMeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfo(UpdateInfoMeRequest $request):JsonResponse
    {
        $request->merge([
            'age' => (int)$request->get('age')
        ]);
        $data = $request->only([
            'full_name',
            'age',
            'gender',
            'address',
            'care_about_gender',
            'introduce',
            'list_favorite',
            'avatar'
        ]);
        $update = $this->userService->updateInfo($data);
        return response()->json($update->toArray());
    }

    /**
     * @param \App\Http\Requests\StoreImageRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeImage(StoreImageRequest $request):JsonResponse
    {
        $images = $request->get('add_images',[]);
        $addImage = $this->userService->storeImage($images);
        return response()->json($addImage->toArray());
    }

    public function infoMe():JsonResponse
    {
        $get = $this->userService->infoMe();
        return response()->json($get->toArray());
    }

    public function updateImage(UpdateImageRequest $request)
    {
        $imageAdd = $request->get('add_images',[]);
        $imageDelete = $request->get('delete_images',[]);
        $update = $this->userService->updateImage($imageAdd,$imageDelete);
        return response()->json($update->toArray());
    }

    public function updateLatLong(Request $request):JsonResponse
    {
        $lat = (double)$request->get('latitude');
        $long = (double)$request->get('longitude');

        $update = $this->userService->updateInfo([
            'location' => [
                'type' => "Point",
                'coordinates' => [
                    $long,
                    $lat
                ]
            ]
        ]);
        return response()->json($update->toArray());
    }
}
