<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Lychee\Message;
use Helpers\Http;
use Helpers\Response;
use Helpers\Sign;

$params = json_decode(file_get_contents("php://input"), true);
if (! $params)
{
    return Response::fail(40000, "参数不能为空");
}

$requiredParams = [
    "appid", "token", "mp_username", "url", "MsgType", "Msg"
];
foreach ($requiredParams as $key)
{
    if (! isset($params[$key]))
    {
        return Response::fail(40001, "缺失参数：" . $key);
    }
}
