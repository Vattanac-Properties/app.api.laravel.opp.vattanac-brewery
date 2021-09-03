<?php

namespace App\Http\Traits;

trait ResponseTrait 
{
    /**
     * Response success function
     *
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function ok($data, $code = 200)
    {
        return response()->json(['success' => true, 'data' => $data], $code);
    }

    /**
     * Response error function
     *
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail($data, $code = 400)
    {
        return response()->json(["success" => false, "message" => $data], $code);
    }
}