<?php
//把微信官方 Jssdk 类改造成 ThinkPHP 模型类
namespace Common\Model;
//JAVA, Node, Python 部分代码只实现了签名算法，需要开发者传入 jsapi_ticket 和 url
//其中 jsapi_ticket 需要通过 http://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=ACCESS_TOKEN 接口获取，url 为调用页面的完整 url
//PHP 部分代码包括了获取 access_token 和 jsapi_ticket 的操作，只需传入 appid 和 appsecret 即可
//但要注意如果已有其他业务需要使用 access_token 的话，应修改获取 access_token 部分代码从全局缓存中获取，防止重复获取 access_token ，超过调用频率
//注意事项： jsapi_ticket 的有效期为 7200 秒，开发者必须全局缓存 jsapi_ticket ，防止超过调用频率
class WxjssdkModel{
  private $appId;
  private $appSecret;
  public function __construct() {
      $config = D('Func')->getConfig();
      $this->appId = $config['WxAppId'];
      $this->appSecret = $config['WxAppSecret'];
  }
  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)?'https://':'http://';
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();
    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);
    $signPackage = [
      'appId'     => $this->appId,
      'nonceStr'  => $nonceStr,
      'timestamp' => $timestamp,
      'url'       => $url,
      'signature' => $signature,
      'rawString' => $string
    ];
    return $signPackage;
  }
  private function createNonceStr($length = 16) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }
  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file('jsapi_ticket.php'));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $this->set_php_file('jsapi_ticket.php', json_encode($data));
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }
    return $ticket;
  }
  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file('access_token.php'));
    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
       //$url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$this->appId.'&corpsecret='.$this->appSecret;
        //个人测试号：需要appsecret获取access_token
        $url1 = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId.'&secret='.$this->appSecret;
        $res1 = json_decode($this->httpGet($url1));
        $at1 = $res1->access_token;
        //正式公众号：无需appsecret也能获取access_token
        $url2 = 'http://'
            .'101.132.185.22'//三年内不用更改此IP
            .':30001/datasnap/rest/TServerMethods1/GetToken/'
            .$this->appId;
        $res2 = json_decode($this->httpGet($url2),true);
        $at2 = $res2['result'][0];
        //dump($at1);dump($at2);die;
        $access_token = $at1?$at1:($at2?$at2:'');// 2个都获取 谁不为空就取谁
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("access_token.php", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }
  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//解决问题的关键链接：http://www.cnblogs.com/lemonphp/p/5942909.html
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
  }
  private function get_php_file($filename) {
    return trim(substr(file_get_contents($filename), 15));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }
}