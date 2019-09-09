<?php
namespace Helpers;

class Sign
{
    /**
     * 生成 signature
     *
     * @param string $token
     * @param string $timestamp
     * @param string $nonce
     * @return array
     */
    public static function generate(string $token, string $timestamp = null, string $nonce = null): array
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        }
        if (is_null($nonce)) {
            $nonce = self::randStr();
        }
        $arr = [$token, $timestamp, $nonce];
        sort($arr, SORT_STRING);
        return [$timestamp, $nonce, sha1(implode($arr))];
    }

    /**
     * 检查 signature
     *
     * @param string $signature
     * @param string $token
     * @param integer $timestamp
     * @param string $nonce
     * @return boolean
     */
    public static function check(string $signature, string $token, int $timestamp, string $nonce): bool
    {
        list($timestamp, $nonce, $sign) = self::generate($token, $timestamp, $nonce);
        return ($signature === $sign);
    }

    /**
     * 构建接收消息 url
     *
     * @param string $url
     * @param array $signData
     * @return string
     */
    public static function url(string $url, array $signData): string
    {
        list($timestamp, $nonce, $signature) = $signData;
        if (strpos($url, "?") === false) {
            $url .= "?" . http_build_query(compact("timestamp", "nonce", "signature"));
        } else {
            $url .= "&" . http_build_query(compact("timestamp", "nonce", "signature"));
        }
        return $url;
    }

    /**
     * 生成随机字符串
     *
     * @param integer $length
     * @return string
     */
    public static function randStr(int $length = 16): string
    {
        $dict = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456798";
        $randStr = "";
        for ($i = 0; $i < $length; $i++) {
            $randStr .= $dict[mt_rand(0, strlen($dict) - 1)];
        }
        return $randStr;
    }
}
