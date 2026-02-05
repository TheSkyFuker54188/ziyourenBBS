<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: [点微]支付中心 6.3
 *      下载地址: https://addon.dismall.com/plugins/tom_pay.html
 *      应用开发者: 点微科技
 *      开发者QQ: 800046233
 *      更新日期: 202602051144
 *      授权域名: 43.143.218.163
 *      授权码: 2026020503VZ4u4wh6Fb
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */


/*
   This is NOT a freeware, use is subject to license terms
   版权所有：点微科技 www.tomwx.cn
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function qf_nonce($length = 32){
     $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
     $str ="";
     for ( $i = 0; $i < $length; $i++ )  {  
         $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
     } 
     return $str;
}
function qf_sign($params, $secret) {
    ksort($params);
    $sparams = array();
    foreach ($params as $k => $v) {
        if ("@" != substr($v, 0, 1)) {
            $sparams[] = "$k=$v";
        }
    }
    $sparams[] = "secret=" . $secret;
    return strtoupper(md5(implode("&", $sparams)));
}

function qf_curl($url){
    if(function_exists('curl_init')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $return = curl_exec($ch);
        curl_close($ch); 
        return $return;
    }
    return false;
}


class QF_HTTP_CLIENT
{
    private $hosts      = QF_HOSTS;       // 接口hosts
    private $token      = QF_TOKEN;       // 开放平台token

    /**
     * @param string $method 请求方式
     * @param string $path 请求路径
     * @param array $requestBody 请求体参数
     * @return array
     * @throws Exception
     */
    private function _request($method, $path, $requestBody)
    {
        $header = array(
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-cache",
            "Accept: application/json",
            "User-Agent: QianFanHttpClient/1.0.0"
        );
        $url = "{$this->hosts}/openapi/{$path}";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
        if (count($requestBody)) {
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($requestBody));
        }
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 2); // 连接超时
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 2); // 响应超时

        $response = curl_exec($curlHandle);
        $error = curl_error($curlHandle);
        $errno = curl_errno($curlHandle);
        // $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);

        if ($error) {
            throw new Exception("cURL Error #{$errno}. $error", 1);
        }

        $data = json_decode($response, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $error = json_last_error_msg();
            $errno = json_last_error();
            throw new Exception("Json Parsing Error #{$errno}. $error", 1);
        }
        return $data;
    }

    public function get($path)
    {
        return $this->_request('GET', $path, []);
    }

    public function put($path, $data)
    {
        return $this->_request('PUT', $path, $data);
    }

    public function post($path, $data)
    {
        return $this->_request('POST', $path, $data);
    }

    public function delete($path, $data)
    {
        return $this->_request('DELETE', $path, $data);
    }
}