<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Lychee\Message;
use Helpers\Http;
use Helpers\Response;
use Helpers\Sign;

$params = json_decode(file_get_contents("php://input"), true);
if (! $params) {
    return Response::fail(40000, "参数不能为空");
}

$requiredParams = [
    "appid", "token", "mp_username", "url", "MsgType", "Msg"
];
foreach ($requiredParams as $key) {
    if (! isset($params[$key])) {
        return Response::fail(40001, "缺失参数：" . $key);
    }
}

switch ($params["MsgType"]) {
    case 'text':
        $msg = new Message\Text;
        $msg->setToUserName($params["mp_username"])
            ->setFromUserName($params["openid"])
            ->setCreateTime(time())
            ->setMsgId(time())
            ->setContent($params["Msg"]["Content"]);
        break;

    case 'event':
        if (! isset($params['Event'])) {
            return Response::fail(40001, "缺少参数： Event");
        }
        switch ($params['Event']) {
            case 'Click':
                $msg = new Message\Event\Click;
                $msg->setToUserName($params["mp_username"])
                    ->setFromUserName($params["openid"])
                    ->setCreateTime(time())
                    ->setMsgId(time())
                    ->setEventKey($params['Msg']['EventKey']);
                break;

            case 'Location':
                $msg = new Message\Event\Location;
                $msg->setToUserName($params["mp_username"])
                    ->setFromUserName($params["openid"])
                    ->setCreateTime(time())
                    ->setMsgId(time())
                    ->setLatitude($params['Msg']['Latitude'])
                    ->setLongitude($params['Msg']['Longitude'])
                    ->setPrecision($params['Msg']['Precision']);
                break;

            case 'Scan':
                $msg = new Message\Event\Scan;
                $msg->setToUserName($params["mp_username"])
                    ->setFromUserName($params["openid"])
                    ->setCreateTime(time())
                    ->setMsgId(time())
                    ->setEventKey($params['Msg']['EventKey'])
                    ->setTicket($params['Msg']['Ticket']);
                break;

            case 'Subscribe':
                $msg = new Message\Event\Subscribe;
                $msg->setToUserName($params["mp_username"])
                    ->setFromUserName($params["openid"])
                    ->setCreateTime(time())
                    ->setMsgId(time());
                if (isset($params['Msg']['EventKey']) && isset($params['Msg']['Ticket'])) {
                    $msg->setEventKey($params['Msg']['EventKey'])
                        ->setTicket($params['Msg']['Ticket']);
                }
                break;

            case 'Unsubscribe':
                $msg = new Message\Event\Unsubscribe;
                $msg->setToUserName($params["mp_username"])
                    ->setFromUserName($params["openid"])
                    ->setCreateTime(time())
                    ->setMsgId(time());
                break;
                
            case 'View':
                $msg = new Message\Event\View;
                $msg->setToUserName($params["mp_username"])
                    ->setFromUserName($params["openid"])
                    ->setCreateTime(time())
                    ->setMsgId(time())
                    ->setEventKey($params['Msg']['EventKey']);
                break;
            
            default:
                return Response::fail(40002, "消息类型不正确");
                break;
        }
        break;
    
    case 'image':
    case 'link':
    case 'location':
    case 'music':
    case 'news':
    case 'shortvideo':
    case 'video':
    case 'voice':
        // TO-DO
        break;

    default:
        return Response::fail(40002, "消息类型不正确");
        break;
}

$url = Sign::url($params["url"], Sign::generate($params["token"])) . "&openid=" . $params["openid"];

$http = new Http;
$reply = $http->postRaw($url, $msg->toXML());

if ($reply == "") {
    return Response::succ("");
} else {
    try {
        $replyMsg = Message\Auto::init($reply);
    } catch (\Exception $e) {
        return Response::fail(40003, "响应数据不合法");
    }
}
return Response::succ($replyMsg->toArray());
