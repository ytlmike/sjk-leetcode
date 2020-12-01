<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait OutFormatTrait
{
    protected static $default_headers = [
        'Content-Type' => 'application/json; charset=UTF-8'
    ];

    /**
     *  成功，返回数据
     * @param mixed $data
     * @param array|string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successWithData($data, $msg = 'ok')
    {
        return $this->outFormat(Response::HTTP_OK, $data, $msg);
    }

    /**
     * 成功只返回提示信息
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($msg = 'ok')
    {
        return $this->outFormat(Response::HTTP_OK, [], $msg);
    }

    /**
     * 返回错误 code 及 信息
     *
     * @param int $code
     * @param array|string $msg
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($code, $msg = '有点问题，请稍后重试', $data = [])
    {
        return $this->outFormat($code, $data, $msg);
    }

    /**
     * 统一格式输出
     *
     * @param int $code
     * @param mixed $data
     * @param array|string $msg
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    protected function outFormat($code, $data, $msg, array $headers = [], $options = JSON_UNESCAPED_UNICODE)
    {
        return response()->json([
            'code' => $code,
            'data' => $data ?: null,
            'msg' => $msg,
        ], Response::HTTP_OK, array_merge(self::$default_headers, $headers), $options);
    }
}
