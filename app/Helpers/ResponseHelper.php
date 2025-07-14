<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Success Response Helper
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function SuccessResponse($data, $message = 'Success', $itemKey = '', $code = 200)
    {




        $params = [];

        if (!empty($itemKey)) {
            $params['item'] = trans('auth.items.' . $itemKey);
        }

        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => trans("auth.{$message}", $params),
        ], $code);




        if (app()->getLocale() == 'ar') {
            $item = trans('auth.items.' . $itemKey);
        } elseif (app()->getLocale() == 'en') {
            $item = trans('auth.items.' . $itemKey); // ما تستخدم $itemKey كـنص حرفي، خليه يترجم من ملف en أيضًا

        }
        // $item = trans('auth.items.' . $itemKey);
        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => trans("auth.{$message}", ['item' => $item]),
        ], $code);
    }

    /**
     * Failure Response Helper
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function FailureResponse($data = null, $message = 'Failed', $itemKey = '', $code = 400)
    {





        $params = [];

        if (!empty($itemKey)) {
            $params['item'] = trans('auth.items.' . $itemKey);
        }

        return response()->json([
            'data' => $data,
            'success' => false,
            'message' => trans("auth.{$message}", $params),
        ], $code);




        if (app()->getLocale() == 'ar') {
            $item = trans('auth.items.' . $itemKey);
        } elseif (app()->getLocale() == 'en') {
            $item = trans('auth.items.' . $itemKey); // ما تستخدم $itemKey كـنص حرفي، خليه يترجم من ملف en أيضًا

        }
        return response()->json([
            'data' => $data,
            'success' => false,
            'message' => trans("auth.{$message}", ['item' => $item]),
        ], $code);
    }
}
