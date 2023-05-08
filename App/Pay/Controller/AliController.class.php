<?php
namespace Pay\Controller;
use Think\Controller;
//require_once('./alipay/wappay/service/AlipayTradeService.php');
//require_once('./alipay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php');
//require_once('./alipay/wappay/buildermodel/AlipayTradeRefundContentBuilder.php');
class AliController extends Controller{

    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
    }

    public function build(){
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
        /////------------------相同代码片段------------------/////
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseAliPay']){
            $this->error('团购或支付宝支付功能未开启');die;
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
        $AliOutTradeNo = uniqid();
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
                    'WxOutTradeNo' => NULL,
                    'AliOutTradeNo' => $AliOutTradeNo,
                ]) === FALSE){
                M()->rollback();
                $this->error('异常错误：数据更新失败');die;
            }
            $PayAmount += $_PayAmount;
        }
        $orderName = implode('，',$orderName);
        $PayAmount = sprintf('%.2f',$PayAmount);
        /////------------------相同代码片段------------------/////
        /*$payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody('');//商品描述，可空
        $payRequestBuilder->setSubject('测试');//订单名称，必填
        $payRequestBuilder->setOutTradeNo($AliOutTradeNo);
        $payRequestBuilder->setTotalAmount($PayAmount);//付款金额，必填
        $payRequestBuilder->setTimeExpress('1m');//超时时间
        $payResponse = new \AlipayTradeService(C('Ali'));
        $payResponse->wapPay($payRequestBuilder,C('Ali')['return_url'],C('Ali')['notify_url']);*/
        M()->commit();
        $queryStr = http_build_query(D('Ali')->doPay($PayAmount,$AliOutTradeNo,$orderName,U('rtn','',true,true),U('notify','',true,true)));
        if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') === FALSE){
            //非微信浏览器
            redirect('https://openapi.alipay.com/gateway.do?'.$queryStr);
        }else{
            //微信浏览器
            $this->assign('queryStr',$queryStr);
            $this->display();
        }
    }
    public function wx(){
        $this->display();
    }

    public function rtn(){
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseAliPay']){
            $this->error('团购或支付宝支付功能未开启');die;
        }
        /*$alipaySevice = new \AlipayTradeService(C('Ali'));
        //实际验证过程建议商户添加以下校验。
        //1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        //2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        //3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        //4、验证app_id是否为该商户本身。
        if($alipaySevice->check($_GET)){
            //验证成功
            //请在这里加上商户的业务逻辑程序代码
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);//商户订单号
            //$trade_no = htmlspecialchars($_GET['trade_no']);//支付宝交易号
            //echo "验证成功<br />外部订单号：".$out_trade_no;
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            $wp = M()->table('WebPay')->where(['AliOutTradeNo' => $out_trade_no])->field('CusPoNo')->find();
            redirect(U('Order/detail?CusPoNo='.$wp['CusPoNo']));
        }else{
            //echo "验证失败";
            $this->error('验证失败');
        }*/
        if(D('Ali')->rsaCheck($_GET) === true){
            //同步回调一般不处理业务逻辑，显示一个付款成功的页面，或者跳转到用户的财务记录页面即可。
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            $wp = M()->table('WebPay')->where(['AliOutTradeNo' => $out_trade_no])->field('CusId,CusPoNo')->select();
            //跳转浏览器支付的自动登录逻辑补上
            $wu = M()->table('WebappUser')->where(['UserType' => 0,'ERPId' => $wp[0]['CusId']])->find();
            $ERP_Wap0_User = ['UserName' => $wu['UserName']];
            session('ERP_Wap0_User',$ERP_Wap0_User);
            if(count($wp) === 1){
                redirect(U('Order/detail?CusPoNo='.$wp[0]['CusPoNo']));
            }else{
                redirect(U('Wap0/Weborder/lists'));
            }
        }else{
            $this->error('验证失败');
        }
    }

    public function notify(){
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseAliPay']){
            echo 'fail';die;
        }
        /*$alipaySevice = new \AlipayTradeService(C('Ali'));
        $alipaySevice->writeLog(var_export($_POST,true));
        //实际验证过程建议商户添加以下校验。
        //1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        //2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        //3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        //4、验证app_id是否为该商户本身。
        if($alipaySevice->check($_POST)){
            //file_put_contents('ali_notify'.rand(111,999), json_encode($_POST));
            //验证成功，请在这里加上商户的业务逻辑
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $out_trade_no = $_POST['out_trade_no'];//商户订单号
            $trade_no = $_POST['trade_no'];//支付宝交易号
            $trade_status = $_POST['trade_status'];//交易状态
            if($_POST['trade_status'] === 'TRADE_FINISHED'){
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }elseif($_POST['trade_status'] === 'TRADE_SUCCESS'){
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                $r = M()->table('WebPay')
                    ->where(['AliOutTradeNo' => $_POST['out_trade_no']])
                    ->save([
                        'Paid' => 1,
                        'PaidTime' => time(),
                        'AliTradeNo' => $_POST['trade_no'],
                    ]);
                if($r === FALSE){
                    echo 'fail';die;
                }
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo 'success';
        }else{
            //验证失败
            echo 'fail';
        }*/
        if(D('Ali')->rsaCheck($_POST) === true){
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            if($_POST['trade_status'] === 'TRADE_FINISHED'){

            }elseif($_POST['trade_status'] === 'TRADE_SUCCESS'){
                if(M()->table('WebPay')->where(['AliOutTradeNo' => $_POST['out_trade_no']])->save(['Paid' => 1,'PaidTime' => time(),'AliTradeNo' => $_POST['trade_no']]) === FALSE){
                    echo 'fail';die;
                }
            }
            echo 'success';
        }else{
            echo 'fail';
        }
    }

    //退款接口
    public function refund_api(){
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || !$this->config['UseAliPay']){
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '团购或支付宝支付功能未开启',
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
        $wp = M()->table('WebPay')->where($where)->field('PayAmount,Paid,Apply,ApplyReason,Refund,AliOutTradeNo,AliTradeNo')->find();
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
        if(M()->table('WebPay')->where(['AliOutTradeNo' => $wp['AliOutTradeNo'],'AliTradeNo' => $wp['AliTradeNo']])->count() > 1){
            //部分退款
            $out_request_no = uniqid();
        }else{
            $out_request_no = null;
        }
        M()->startTrans();
        if(M()->table('WebPay')->where($where)->save(['Refund' => 1,'RefundTime' => time()]) === FALSE){
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => '异常错误：数据更新失败',
            ]);die;
        }
        /*$RequestBuilder = new \AlipayTradeRefundContentBuilder();
        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        $RequestBuilder->setOutTradeNo($wp['AliOutTradeNo']);//商户订单号，和支付宝交易号二选一
        $RequestBuilder->setTradeNo($wp['AliTradeNo']);//支付宝交易号，和商户订单号二选一
        $RequestBuilder->setRefundAmount(sprintf('%.2f',$wp['PayAmount']));//退款金额，不能大于订单总金额
        $RequestBuilder->setRefundReason('退款原因');//退款的原因说明
        $RequestBuilder->setOutRequestNo(uniqid());//标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
        $Response = new \AlipayTradeService(C('Ali'));
        $r = $Response->Refund($RequestBuilder);
        $r = json_decode(json_encode($r),true);*/
        $r = D('Ali')->doRefund($wp['AliOutTradeNo'],$wp['AliTradeNo'],sprintf('%.2f',$wp['PayAmount']),$out_request_no,$wp['Apply'] === '1'?'用户申请退款：'.$wp['ApplyReason']:'ERP删除订单');
        $r = $r['alipay_trade_refund_response'];
        if($r['code'] && $r['code'] === '10000'){
            M()->commit();
            echo json_encode([
                'ret' => C('succ_ret'),
                'msg' => '订单退款成功',
            ]);
        }else{
            M()->rollback();
            echo json_encode([
                'ret' => C('fail_ret'),
                'msg' => $r['sub_msg'],
            ]);
        }
    }

}
