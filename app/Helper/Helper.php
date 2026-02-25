<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class Helper
{

    // Upload Image
    /* public static function fileUpload($file, $folder, $name)
    {
        $imageName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $imageName);
        $path = 'uploads/' . $folder . '/' . $imageName;
        return $path;
    } */

    // Upload Image
    public static function fileUpload($file, $folder, $nameWithoutExt = null)
    {
        // If no name provided, use original filename without extension
        $baseName = $nameWithoutExt ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Slug the base name and append the actual file extension
        $imageName = Str::slug($baseName) . '.' . $file->getClientOriginalExtension();

        // Full upload path (public/uploads/cars)
        $uploadPath = public_path('uploads/' . $folder);

        // Ensure the folder exists
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Move the file
        $file->move($uploadPath, $imageName);

        // Return relative path for DB
        return 'uploads/' . $folder . '/' . $imageName;
    }

    //tableCheckbox
    public static function tableCheckbox($row_id)
    {
        return '<div class="form-checkbox">
                <input type="checkbox" class="form-check-input select_data" id="checkbox-' . $row_id . '" value="' . $row_id . '" onClick="select_single_item(' . $row_id . ')">
                <label class="form-check-label" for="checkbox-' . $row_id . '"></label>
            </div>';
    }

    //video upload
    public static function videoUpload($file, $folder, $name)
    {
        $videoName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $videoName);
        $path = 'uploads/' . $folder . '/' . $videoName;
        return $path;
    }

    // audio upload
    public static function audioUpload($file, $folder, $name)
    {
        $audioName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $audioName);
        $path = 'uploads/' . $folder . '/' . $audioName;
        return $path;
    }


    //! JSON Response
    public static function jsonResponse(bool $status, string $message, int $code, $data = null, bool $paginate = false, $paginateData = null): JsonResponse
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'code'    => $code,
        ];

        if ($paginate && !empty($paginateData)) {
            $response['data'] = $data;
            $response['pagination'] = [
                'current_page' => $paginateData->currentPage(),
                'last_page' => $paginateData->lastPage(),
                'per_page' => $paginateData->perPage(),
                'total' => $paginateData->total(),
                'first_page_url' => $paginateData->url(1),
                'last_page_url' => $paginateData->url($paginateData->lastPage()),
                'next_page_url' => $paginateData->nextPageUrl(),
                'prev_page_url' => $paginateData->previousPageUrl(),
                'from' => $paginateData->firstItem(),
                'to' => $paginateData->lastItem(),
                'path' => $paginateData->path(),
            ];
        } elseif ($paginate && !empty($data)) {
            $response['data'] = $data->items();
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'first_page_url' => $data->url(1),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'path' => $data->path(),
            ];
        } elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function jsonErrorResponse(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'status'  => false,
            'message' => $message,
            'code'    => $code,
            'errors'  => $errors,
        ];
        return response()->json($response, $code);
    }
}
