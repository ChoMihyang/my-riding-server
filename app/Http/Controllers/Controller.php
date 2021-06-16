<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Null_;
use PhpParser\Node\Scalar\String_;
use App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function makeResponseJson($msg, $statusCode, $data = null): JsonResponse
    {
        return response()->json([
            "message" => $msg,
            "data" => $data
        ], $statusCode);
    }

    /**
     * [WEB] json response 생성
     *
     * @param string $message
     * @param $data
     * @param int $http_code
     * @return JsonResponse
     */
    public function responseJson(
        string $message,
        $data,
        int $http_code
    ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $http_code);
    }

    /**
     * [APP] json response 생성
     *
     * @param string $message
     * @param string $type
     * @param $data
     * @param int $http_code
     * @return JsonResponse
     */
    public function responseAppJson(
        string $message,
        string $type,
        $data,
        int $http_code
    ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            $type => $data
        ], $http_code
        );
    }

    /**
     * 사용자 이미지 불러오기
     *
     * @param int|null $user_id
     * @return string
     */
    public function loadImage(
        int $user_id = null
    ): string
    {
        if ($user_id != null) {
            $user = User::find($user_id);
        } else {
            $user = Auth::guard('api')->user();
        }
        $user_img = $user->getAttribute('user_picture');
        if ($user_img == "null") {
            return "null";
        }

        return $loadImg = $this->getBase64Img($user_img);
    }

    public function getBase64Img($img_name): string
    {
        $data = Storage::get('public/' . $img_name);
        $type = pathinfo('storage/' . $img_name, PATHINFO_EXTENSION);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * 사용자 이미지 삭제
     *
     * @param String $url
     */
    public function deleteImage(String $url)
    {
        Storage::delete($url);
    }

    /**
     * 사용자 이미지 저장
     *
     * @param UploadedFile $uploadedFile
     * @param string $imgFileName
     * @param string $folderName
     * @return string
     */
    public
    function getImage(
        UploadedFile $uploadedFile,
        string $imgFileName,
        string $folderName
    ): string
    {
        $extension = $uploadedFile->extension();
        $storagePath = "{$folderName}{$imgFileName}.{$extension}";

        Storage::putFileAs('public', $uploadedFile, $storagePath);

        return $storagePath;
    }
}
