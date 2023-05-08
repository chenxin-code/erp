<?php
namespace Pay\Model;
class WxModel{

    var $config;

    public function __construct(){
        $this->config = D('Func')->getConfig();
    }

    /**
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     * @return 用户的openid
     */
    public function GetOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
            $uri = $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
            if($_SERVER['REQUEST_URI']) $uri = $_SERVER['REQUEST_URI'];
            $baseUrl = urlencode($scheme.$_SERVER['HTTP_HOST'].$uri);
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }
    }

    /**
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        $res = self::curlGet($url);
        //取出openid
        $data = json_decode($res,true);
        $openid = $data['openid'];
        return $openid;
    }

    /**
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->config['WxAppId'];
        $urlObj["secret"] = $this->config['WxAppSecret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->config['WxAppId'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign") $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 统一下单
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @param float $totalFee 收款总费用 单位元
     * @param string $outTradeNo 唯一的订单号
     * @param string $orderName 订单名称
     * @param string $notifyUrl 支付结果通知url 不要有问号
     * @param string $timestamp 支付时间
     */
    public function createJsBizPackage($openid, $totalFee, $outTradeNo, $orderName, $notifyUrl, $timestamp)
    {
        //$orderName = iconv('GBK','UTF-8',$orderName);
        $unified = [
            'appid' => $this->config['WxAppId'],
            'attach' => 'pay',
            'body' => strlen($orderName) > 100?substr($orderName,0,97).'...':$orderName,
            'mch_id' => $this->config['WxMchId'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $notifyUrl,
            'openid' => $openid,            //rade_type=JSAPI，此参数必传
            'out_trade_no' => $outTradeNo,
            'spbill_create_ip' => '127.0.0.1',
            'total_fee' => intval($totalFee * 100),       //单位 转为分
            'trade_type' => 'JSAPI',
        ];
        $unified['sign'] = self::getSign($unified, $this->config['WxKey']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === FALSE) {
            return [
                'bool' => FALSE,
                'reason' => 'parse xml error',
            ];
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            return [
                'bool' => FALSE,
                'reason' => $unifiedOrder->return_msg,
            ];
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            return [
                'bool' => FALSE,
                'reason' => $unifiedOrder->err_code,
            ];
        }
        $data = [
            "appId" => $this->config['WxAppId'],
            "timeStamp" => "$timestamp",        //这里是字符串的时间戳，不是int，所以需加引号
            "nonceStr" => self::createNonceStr(),
            "package" => "prepay_id=" . $unifiedOrder->prepay_id,
            "signType" => 'MD5',
        ];
        $data['paySign'] = self::getSign($data, $this->config['WxKey']);
        return [
            'bool' => TRUE,
            'data' => $data,
        ];
    }
    /**
     * 发起订单
     * @param float $totalFee 收款总费用 单位元
     * @param string $outTradeNo 唯一的订单号
     * @param string $orderName 订单名称
     * @param string $notifyUrl 支付结果通知url 不要有问号
     * @param string $timestamp 订单发起时间
     */
    public function createJsBizPackage1($totalFee, $outTradeNo, $orderName, $notifyUrl, $timestamp)
    {
        $config = [
            'mch_id' => $this->config['WxMchId'],
            'appid' => $this->config['WxAppId'],
            'key' => $this->config['WxKey'],
        ];
        //$orderName = iconv('GBK','UTF-8',$orderName);
        $unified = [
            'appid' => $config['appid'],
            'attach' => 'pay',
            'body' => strlen($orderName) > 100?substr($orderName,0,97).'...':$orderName,
            'mch_id' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'notify_url' => $notifyUrl,
            'out_trade_no' => $outTradeNo,
            'spbill_create_ip' => '127.0.0.1',
            'total_fee' => intval($totalFee * 100),       //单位 转为分
            'trade_type' => 'NATIVE',
        ];
        $unified['sign'] = self::getSign($unified, $config['key']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($unifiedOrder === FALSE) {
            return [
                'bool' => FALSE,
                'reason' => 'parse xml error',
            ];
        }
        if ($unifiedOrder->return_code != 'SUCCESS') {
            return [
                'bool' => FALSE,
                'reason' => $unifiedOrder->return_msg,
            ];
        }
        if ($unifiedOrder->result_code != 'SUCCESS') {
            return [
                'bool' => FALSE,
                'reason' => $unifiedOrder->err_code,
            ];
        }
        $codeUrl = (array)($unifiedOrder->code_url);
        if(!$codeUrl[0]){
            return [
                'bool' => FALSE,
                'reason' => 'get code_url error',
            ];
        }
        $data = [
            "appId" => $config['appid'],
            "timeStamp" => $timestamp,
            "nonceStr" => self::createNonceStr(),
            "package" => "prepay_id=" . $unifiedOrder->prepay_id,
            "signType" => 'MD5',
            "code_url" => $codeUrl[0],
        ];
        $data['paySign'] = self::getSign($data, $config['key']);
        return [
            'bool' => TRUE,
            'data' => $data,
        ];
    }

    /**
     * 退款
     * @param float $totalFee 订单金额 单位元
     * @param float $refundFee 退款金额 单位元
     * @param string $refundNo 退款单号
     * @param string $wxOrderNo 微信订单号
     * @param string $orderNo 商户订单号
     * @return string
     */
    public function doRefund($totalFee, $refundFee, $refundNo, $wxOrderNo,$orderNo,$ApplyReason)
    {
        $unified = [
            'appid' => $this->config['WxAppId'],
            'mch_id' => $this->config['WxMchId'],
            'nonce_str' => self::createNonceStr(),
            'total_fee' => intval($totalFee * 100),       //订单金额	 单位 转为分
            'refund_fee' => intval($refundFee * 100),       //退款金额 单位 转为分
            'sign_type' => 'MD5',           //签名类型 支持HMAC-SHA256和MD5，默认为MD5
            'transaction_id'=>$wxOrderNo,               //微信订单号
            'out_trade_no'=>$orderNo,        //商户订单号
            'out_refund_no'=>$refundNo,        //商户退款单号
            'refund_desc'=>$ApplyReason,     //退款原因（选填）
        ];
        $unified['sign'] = self::getSign($unified, $this->config['WxKey']);
        $responseXml = $this->curlPost1('https://api.mch.weixin.qq.com/secapi/pay/refund', self::arrayToXml($unified));
        $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $unifiedOrder = json_decode(json_encode($unifiedOrder),true);
        return $unifiedOrder;
    }

    public static function curlGet($url = '', $options = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function curlPost($url = '', $postData = '', $options = [])
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    //退款专用
    public function curlPost1($url = '', $postData = '', $options = [])
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/cert/apiclient_cert.pem');
        curl_setopt($ch,CURLOPT_SSLCERT,$this->config['WxSslcertPath']);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/cert/apiclient_key.pem');
        curl_setopt($ch,CURLOPT_SSLKEY,$this->config['WxSslkeyPath']);
        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function createNonceStr($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 获取签名
     */
    public static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }

    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * 控制器 => 模型
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量
     */
    public function makeSign($data){
        //获取微信支付秘钥
        $key = $this->config['WxKey'];
        //去空
        $data = array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = http_build_query($data);
        $string_a = urldecode($string_a);
        //签名步骤二：在string后加入KEY
        $string_sign_temp = $string_a.'&key='.$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        //签名步骤四：所有字符转为大写
        $sign = strtoupper($sign);
        return $sign;
    }

}
