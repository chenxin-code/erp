<?php
namespace Pay\Controller;
use Think\Controller;
//require_once('./wxpay/lib/WxPay.Api.php');
//require_once('./wxpay/example/WxPay.JsApiPay.php');
class WxController extends Controller{

    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        if(session('ERP_Wap0_User')){
            $ERPId = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap0_User.UserName')])->getField('ERPId');
            $CusShortName = M()->table('Customer')->where(['CusId' => $ERPId])->getField('CusShortName');
            $this->assign('HeaderFlag',$CusShortName.'（'.$ERPId.'）');
        }
        $this->assign('config',$this->config);
    }

    public function jsapi(){
        //跳转到80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] !== $this->config['Frp80PortDomain']){
            $this->redirect(ACTION_NAME.'@'.$this->config['Frp80PortDomain'],$_GET);die;
        }
        /////------------------相同代码片段------------------/////
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseWxPay']){
            $this->error('团购或微信支付功能未开启');die;
        }
        $get = I('get.');
        if(D('Func')->makeSecret($get['CusId'],$get['CusPoNo'],TRUE) !== $get['secret']){
            $this->error('参数错误');die;
        }
        if(!$get['CusPoNo']){
            $this->error('参数错误');die;
        }
        $CusPoNo = explode(',',$get['CusPoNo']);
        if(count($CusPoNo) !== count(array_unique($CusPoNo))){
            $this->error('参数错误');die;
        }
        $wu = M()->table('WebappUser')->where(['ERPId' => $get['CusId'],'UserType' => 0])->field('OneCentPay')->find();
        $WxOutTradeNo = uniqid();
        $orderName = [];
        $PayAmount = 0;
        M()->startTrans();
        foreach($CusPoNo as $v){
            $where = [
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $get['CusId'],
                'CusPoNo' => $v,
            ];
            $wp = M()->table('WebPay')->where($where)->field('PayDeadlineTime,Paid')->find();
            if(!$wp){
                M()->rollback();
                $this->error('参数错误');die;
            }
            if($wp['Paid'] === '1'){
                M()->rollback();
                $this->error('订单已付款');die;
            }
            $wo = M()->table('WebappOrder')->where($where)->field('BoardId,MatNo,IsDel')->find();
            if($wo['IsDel'] === '1'){
                M()->rollback();
                $this->error('订单已删除');die;
            }
            if(time() > $wp['PayDeadlineTime']){
                M()->rollback();
                $this->error('订单已超过有效支付时间');die;
            }
            $wgo = M()->table('WebGroupOrder')->where($where)->field('Title,Cost')->find();
            if(!is_null($wo['BoardId']) && is_null($wo['MatNo'])){
                $BM = $wo['BoardId'];
            }elseif(is_null($wo['BoardId']) && !is_null($wo['MatNo'])){
                $BM = $wo['MatNo'];
            }
            $orderName[] = $BM.($wgo['Title'] || $wgo['Title'] === '0'?','.$wgo['Title']:'');
            if($wu['OneCentPay']){
                $_PayAmount = '0.01';
            }else{
                $_PayAmount = sprintf('%.2f',$wgo['Cost']);
            }
            if(M()->table('WebPay')->where($where)->save([
                    'OneCentPay' => $wu['OneCentPay']?1:0,
                    'PayAmount' => $_PayAmount,
                    'WxOutTradeNo' => $WxOutTradeNo,
                    'AliOutTradeNo' => NULL,
                ]) === FALSE){
                M()->rollback();
                $this->error('异常错误：数据更新失败');die;
            }
            $PayAmount += $_PayAmount;
        }
        $orderName = implode('，',$orderName);
        $PayAmount = sprintf('%.2f',$PayAmount);
        /////------------------相同代码片段------------------/////
        /*$tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($wgo['Title']);
        //$input->SetAttach('');
        $input->SetOut_trade_no($WxOutTradeNo);
        $input->SetTotal_fee((string)($PayAmount * 100));
        //$input->SetTime_start(date('YmdHis'));
        //$input->SetTime_expire(date('YmdHis', time() + 600));
        //$input->SetGoods_tag('');
        //80端口
        //$input->SetNotify_url(U('notify','',true,true));
        //非80端口
        $input->SetNotify_url(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U('notify@'.$this->config['OriDomain'])));
        $input->SetTrade_type('JSAPI');
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        try {
            $jsApiParameters = $tools->GetJsApiParameters($order);
        } catch (\Exception $e) {
            $this->error($e->getMessage());die;
        }*/
        $openId = D('Wx')->GetOpenid();
        if(!$openId){
            M()->rollback();
            $this->error('获取openid失败');die;
        }
        $r = D('Wx')->createJsBizPackage($openId,$PayAmount,$WxOutTradeNo,$orderName,
            //str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U('notify@'.$this->config['OriDomain'])),
            U('notify','',true,true),
            time());
        if(!$r['bool']){
            M()->rollback();
            $this->error($r['reason']);die;
        }
        M()->commit();
        $jsApiParameters = json_encode($r['data']);
        $this->assign([
            'jsApiParameters' => $jsApiParameters,
            'CusPoNo' => $get['CusPoNo'],
        ]);
        $this->display();
    }
    public function native(){
        //跳转到非80端口访问
        /*if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }*/
        //跳转到80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] !== $this->config['Frp80PortDomain']){
            $this->redirect(ACTION_NAME.'@'.$this->config['Frp80PortDomain'],$_GET);die;
        }
        /////------------------相同代码片段------------------/////
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseWxPay']){
            $this->error('团购或微信支付功能未开启');die;
        }
        $get = I('get.');
        if(D('Func')->makeSecret($get['CusId'],$get['CusPoNo'],TRUE) !== $get['secret']){
            $this->error('参数错误');die;
        }
        if(!$get['CusPoNo']){
            $this->error('参数错误');die;
        }
        $CusPoNo = explode(',',$get['CusPoNo']);
        if(count($CusPoNo) !== count(array_unique($CusPoNo))){
            $this->error('参数错误');die;
        }
        $wu = M()->table('WebappUser')->where(['ERPId' => $get['CusId'],'UserType' => 0])->field('OneCentPay')->find();
        $WxOutTradeNo = uniqid();
        $orderName = [];
        $PayAmount = 0;
        M()->startTrans();
        foreach($CusPoNo as $v){
            $where = [
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $get['CusId'],
                'CusPoNo' => $v,
            ];
            $wp = M()->table('WebPay')->where($where)->field('PayDeadlineTime,Paid')->find();
            if(!$wp){
                M()->rollback();
                $this->error('参数错误');die;
            }
            if($wp['Paid'] === '1'){
                M()->rollback();
                $this->error('订单已付款');die;
            }
            $wo = M()->table('WebappOrder')->where($where)->field('BoardId,MatNo,IsDel')->find();
            if($wo['IsDel'] === '1'){
                M()->rollback();
                $this->error('订单已删除');die;
            }
            if(time() > $wp['PayDeadlineTime']){
                M()->rollback();
                $this->error('订单已超过有效支付时间');die;
            }
            $wgo = M()->table('WebGroupOrder')->where($where)->field('Title,Cost')->find();
            if(!is_null($wo['BoardId']) && is_null($wo['MatNo'])){
                $BM = $wo['BoardId'];
            }elseif(is_null($wo['BoardId']) && !is_null($wo['MatNo'])){
                $BM = $wo['MatNo'];
            }
            $orderName[] = $BM.($wgo['Title'] || $wgo['Title'] === '0'?','.$wgo['Title']:'');
            if($wu['OneCentPay']){
                $_PayAmount = '0.01';
            }else{
                $_PayAmount = sprintf('%.2f',$wgo['Cost']);
            }
            if(M()->table('WebPay')->where($where)->save([
                    'OneCentPay' => $wu['OneCentPay']?1:0,
                    'PayAmount' => $_PayAmount,
                    'WxOutTradeNo' => $WxOutTradeNo,
                    'AliOutTradeNo' => NULL,
                ]) === FALSE){
                M()->rollback();
                $this->error('异常错误：数据更新失败');die;
            }
            $PayAmount += $_PayAmount;
        }
        $orderName = implode('，',$orderName);
        $PayAmount = sprintf('%.2f',$PayAmount);
        /////------------------相同代码片段------------------/////
        $r = D('Wx')->createJsBizPackage1($PayAmount,$WxOutTradeNo,$orderName,
            //str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U('notify@'.$this->config['OriDomain'])),
            U('notify','',true,true),
            time());
        if(!$r['bool']){
            M()->rollback();
            $this->error($r['reason']);die;
        }
        M()->commit();
        $this->assign([
            'LayoutTitle' => '微信支付',
            'code_url' => $r['data']['code_url'],
            'PayAmount' => $PayAmount,
            'CusPoNo' => $get['CusPoNo'],
        ]);
        $this->display();
    }

    public function notify(){
        //跳转到非80端口访问
        /*if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }*/
        //跳转到80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] !== $this->config['Frp80PortDomain']){
            $this->redirect(ACTION_NAME.'@'.$this->config['Frp80PortDomain'],$_GET);die;
        }
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseWxPay']){
            die;
        }
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        //将服务器返回的XML数据转化为数组
        $data = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        //保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = D('Wx')->makeSign($data);
        //判断签名是否正确 判断支付状态
        if($sign === $data_sign && $data['return_code'] === 'SUCCESS' && $data['result_code'] === 'SUCCESS'){
            //file_put_contents('wx_notify'.rand(111,999), json_encode($data));
            $total_fee = sprintf('%.2f',$data['total_fee'] / 100);//付款金额
            if(!M()->table('WebPay')->where(['WxOutTradeNo' => $data['out_trade_no']])->select()){
                $r1 = D('Wx')->doRefund($total_fee,$total_fee,uniqid(),$data['transaction_id'],$data['out_trade_no'],'系统自动退款：无效账单');
                if($r1 === FALSE){die;}
                if($r1['return_code'] !== 'SUCCESS'){die;}
                if($r1['result_code'] !== 'SUCCESS'){die;}
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';die;
            }
            $r2 = M()->table('WebPay')
                ->where(['WxOutTradeNo' => $data['out_trade_no']])
                ->save([
                    'Paid' => 1,
                    'PaidTime' => time(),
                    'WxTransactionId' => $data['transaction_id'],
                ]);
            if($r2 !== FALSE){
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            }
        }else{
            echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
    }

    //退款接口
    public function refund_api(){
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseWxPay']){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '团购或微信支付功能未开启',
            ]);die;
        }
        $get = I('get.');
        if(D('Func')->makeSecret($get['CusId'],$get['CusPoNo']) !== $get['secret']){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $get['CusId'],
            'CusPoNo' => $get['CusPoNo'],
        ];
        //查询订单,根据订单里边的数据进行退款
        $wp = M()->table('WebPay')->where($where)->field('PayAmount,Paid,Apply,ApplyReason,Refund,WxOutTradeNo,WxTransactionId')->find();
        if(!$wp){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($wp['Paid'] === '0'){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '订单未付款',
            ]);die;
        }
        $wo = M()->table('WebappOrder')->where($where)->field('Checked')->find();
        if($wp['Apply'] === '0' && $wo['Checked'] === '0'){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '订单未申请退款且未审核',
            ]);die;
        }
        if($wp['Refund'] === '1'){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '订单已退款',
            ]);die;
        }
        $_PayAmount = M()->table('WebPay')->where(['WxOutTradeNo' => $wp['WxOutTradeNo'],'WxTransactionId' => $wp['WxTransactionId']])->sum('PayAmount');
        $_PayAmount = sprintf('%.2f',$_PayAmount);
        $PayAmount = sprintf('%.2f',$wp['PayAmount']);
        M()->startTrans();
        if(M()->table('WebPay')->where($where)->save(['Refund' => 1,'RefundTime' => time()]) === FALSE){
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '异常错误：数据更新失败',
            ]);die;
        }
        /*$merchid = (new \WxPayConfig)->MCHID;
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($wp['WxOutTradeNo']);
        //$input->SetTransaction_id($wp['WxTransactionId']);//微信官方生成的订单流水号，在支付成功中有返回
        $input->SetOut_refund_no(uniqid());//退款单号
        $input->SetTotal_fee((string)($_PayAmount * 100));//订单标价金额，单位为分
        $input->SetRefund_fee((string)($PayAmount * 100));//退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOp_user_id($merchid);
        try {
            $r = \WxPayApi::refund($input);//退款操作
        } catch (\Exception $e) {
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        if($r['return_code'] === 'SUCCESS' && $r['result_code'] === 'SUCCESS'){
            M()->commit();
            echo json_encode([
                'ret' => C('succ_ret'),
                'msg' => '订单退款成功',
            ]);
        }elseif($r['return_code'] === 'FAIL' || $r['result_code'] === 'FAIL'){
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => $r['err_code_des'],
            ]);
        }else{
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '异常错误：未知原因',
            ]);
        }*/
        $r = D('Wx')->doRefund($_PayAmount,$PayAmount,uniqid(),$wp['WxTransactionId'],$wp['WxOutTradeNo'],$wp['Apply'] === '1'?'用户申请退款：'.$wp['ApplyReason']:'ERP删除订单');
        if($r === FALSE){
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => 'parse xml error',
            ]);die;
        }
        if($r['return_code'] !== 'SUCCESS'){
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => $r['return_msg'],
            ]);die;
        }
        if($r['result_code'] !== 'SUCCESS'){
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => $r['err_code_des'],
            ]);die;
        }
        M()->commit();
        echo json_encode([
            'ret' => C('succ_ret'),
            'msg' => '订单退款成功',
        ]);
    }

}
