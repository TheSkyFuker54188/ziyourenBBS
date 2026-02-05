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


$url = $weixinClass->get_url();

# tom oauth start
$tom_oauth_hosts = trim($payConfig['oauth_hosts']);
$tom_oauth_hosts = str_replace("https://", "", $tom_oauth_hosts);
$tom_oauth_hosts = str_replace("http://", "", $tom_oauth_hosts);
$tom_oauth_hosts = rtrim($tom_oauth_hosts,"/");
preg_match("#((http|https)://([^?]*)/)[a-z_0-9]*.php#", $url, $urlmatches);
if(!empty($tom_oauth_hosts) && is_array($urlmatches) && !empty($urlmatches['0'])){
    $tom_visit_hosts = $urlmatches['3'];
    if(strpos($urlmatches['3'],'/') !== FALSE){
        $tom_visit_hosts_arr = explode('/', $urlmatches['3']);
        $tom_visit_hosts = $tom_visit_hosts_arr[0];
    }
    if($tom_visit_hosts !== $tom_oauth_hosts){
        if($payConfig['oauth_http'] == 2){
            $tom_oauth_url = "https://".$tom_oauth_hosts."/tom_oauth.php";
        }else{
            $tom_oauth_url = "http://".$tom_oauth_hosts."/tom_oauth.php";
        }
        $oauth_back_url = $urlmatches['0'];
        $url = str_replace($urlmatches['0'], $tom_oauth_url, $url)."&oauth_back_url=".urlencode($oauth_back_url);
    }
}
# tom oauth end

$redirect_uri = urlencode($url);

$openid = '';
$subscribeFlag = false; 

$oauth2_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=1#wechat_redirect";

if(isset($_GET['code']) && !empty($_GET['code'])){
    $code = $_GET['code'];
    $openid = get_oauth2_openid($code,$appid,$appsecret);
    if(!empty($openid)){
    }else{
        dheader('location:'.$oauth2_url);
        exit;
    }
    
}else{
    dheader('location:'.$oauth2_url);
    exit;
}
function get_oauth2_openid($code,$appid,$appsecret){
    $openid = '';
    $get_openid_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
    $return = get_html($get_openid_url);
    if(!empty($return)){
        $content = json_decode($return,true);
        if(is_array($content) && !empty($content) && isset($content['openid']) && !empty($content['openid'])){
            $openid = $content['openid'];
        }
    }
    return $openid;
}