<?php

namespace Helpers;

class Http
{
    private $ch;

    public $useragent = "Lychee wechat mp debugger";
    public $timeout = 5;

    /**
    * 构造函数
    *
    * @access public
    * @return void
    */
    public function __construct()
    {
        $this->ch = curl_init();
    }

    /**
    * 发起 GET 请求
    *
    * @access public
    * @param string $url
    * @return string
    */
    public function get($url)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => $this->useragent,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_URL            => $url,
        ];
        curl_setopt_array($this->ch, $options);
        $result = curl_exec($this->ch);
        return $result;
    }

    /**
     * POST 表单数据
     *
     * @param string $url
     * @param string $datas
     * @return string
     */
    public function postFormData($url, $datas)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => $this->useragent,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => http_build_query($data)
        ];
        curl_setopt_array($this->ch, $options);
        $result = curl_exec($this->ch);
        return $result;
    }

    /**
     * POST Raw
     *
     * @param string $url
     * @param string $rawData
     * @param string $type
     * @return string
     */
    public function postRaw($url, $rawData, $type = null)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => $this->useragent,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_URL            => $url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $rawData,
            CURLOPT_HTTPHEADER     => is_null($type) ? ['Content-Type: text/plain'] : ['Content-Type: ' . $type]
        ];
        curl_setopt_array($this->ch, $options);
        $result = curl_exec($this->ch);
        return $result;
    }

    public function head($url)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT      => $this->useragent,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_URL            => $url
        ];
        curl_setopt_array($this->ch, $options);
        $result = curl_exec($this->ch);
        
        $header_text = substr($result, 0, strpos($result, "\r\n\r\n"));
        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list($key, $value) = explode(': ', $line);
    
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
