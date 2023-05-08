<?php
namespace Pay\Model;
class AliModel{

    var $config;
    var $charset;

    public function __construct(){
        $this->config = D('Func')->getConfig();
        $this->charset = 'utf8';
    }

    /**
     * 发起订单
     * @param float $totalFee 收款总费用 单位元
     * @param string $outTradeNo 唯一的订单号 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
     * @param string $orderName 订单名称
     * @param string $timestamp 订单发起时间
     * @param string $returnUrl 付款成功后的同步回调地址
     * @param string $notifyUrl 付款成功后的异步回调地址
     * @return array
     */
    public function doPay($totalFee,$outTradeNo,$orderName,$returnUrl,$notifyUrl){
        //请求参数
        $requestConfigs = [
            'out_trade_no' => $outTradeNo,
            'product_code' => 'QUICK_WAP_WAY',
            'total_amount' => $totalFee, //单位 元
            'subject' => $orderName,  //订单标题
        ];
        //公共参数
        $commonConfigs = [
            'app_id' => $this->config['AliAppId'],
            'method' => 'alipay.trade.wap.pay', //接口名称
            'format' => 'JSON',
            'return_url' => $returnUrl,
            'charset' => $this->charset,
            'sign_type' => 'RSA2',//签名算法类型，支持RSA2和RSA，推荐使用RSA2
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'notify_url' => $notifyUrl,
            'biz_content' => json_encode($requestConfigs),
        ];
        $commonConfigs['sign'] = $this->generateSign($commonConfigs,$commonConfigs['sign_type']);
        return $commonConfigs;
    }

    /**
     * 退款
     * @param float $tradeNo 在支付宝系统中的交易流水号。最短 16 位，最长 64 位。和out_trade_no不能同时为空
     * @param string $outTradeNo 唯一的订单号 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
     * @param string $refundAmount 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
     * @param string $out_request_no 部分退款需要，否则传null
     * @param string $refund_reason 退款原因
     * @return array
     */
    public function doRefund($outTradeNo,$tradeNo,$refundAmount,$out_request_no,$refund_reason){
        //请求参数
        $requestConfigs = [
            'trade_no'=>$tradeNo,
            'out_trade_no'=>$outTradeNo,
            'refund_amount'=>$refundAmount,
            'out_request_no' => $out_request_no,
            'refund_reason' => $refund_reason,
        ];
        //公共参数
        $commonConfigs = [
            'app_id' => $this->config['AliAppId'],
            'method' => 'alipay.trade.refund',//接口名称
            'format' => 'JSON',
            'charset' => $this->charset,
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($requestConfigs),
        ];
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do',$commonConfigs);
        $resultArr = json_decode($result,true);
        if(empty($resultArr)){
            $result = iconv('GBK','UTF-8//IGNORE',$result);
            return json_decode($result,true);
        }
        return $resultArr;
    }

    public function generateSign($params,$signType = 'RSA'){
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function sign($data,$signType = 'RSA'){
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($this->config['AliRsaPrivateKey'], 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ('RSA2' == $signType) {
            openssl_sign($data,$sign,$res,OPENSSL_ALGO_SHA256);//OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data,$sign,$res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     *  验证签名
     **/
    public function rsaCheck($params) {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    public function verify($data, $sign, $signType = 'RSA') {
        $res = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->config['AliPublicKey'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value){
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === '')
            return true;

        return false;
    }

    public function getSignContent($params){
        ksort($params);
        $stringToBeSigned = '';
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && '@' != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= $k . '=' . $v;
                } else {
                    $stringToBeSigned .= '&' . $k . '=' . $v;
                }
                $i++;
            }
        }
        unset($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    public function characet($data,$targetCharset){
        if(!empty($data)){
            $fileType = $this->charset;
            if(strcasecmp($fileType, $targetCharset) != 0){
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    public function curlPost($url, $postData = '', $options = []){
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

}