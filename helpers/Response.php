<?php
namespace Helpers;

class Response
{
    /**
     * 响应 JSON
     *
     * @param mixed $data
     * @return void
     */
    public static function json($data)
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return;
    }

    /**
     * 响应成功数据
     *
     * @param mixed $data
     * @return void
     */
    public static function succ($data)
    {
        $code = 0;
        $msg = "OK";
        return self::json(compact("code", "msg", "data"));
    }

    /**
     * 响应错误消息
     *
     * @param integer $code
     * @param string $msg
     * @return void
     */
    public static function fail(int $code, string $msg)
    {
        $data = null;
        return self::json(compact("code", "msg", "data"));
    }
}
