<?php
namespace Pay\Controller;
use Think\Controller;
class OrderController extends Controller{

    var $ERPId;
    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        if((!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']) || (!$this->config['UseWxPay'] && !$this->config['UseAliPay'])){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                $this->error('团购或支付功能未开启');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '团购或支付功能未开启']);
            }
            die;
        }
        if(!session('ERP_Wap0_User')){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                //把要访问的地址保存到session中，这样登录成功之后会跳回来
                session('ERP_Wap0_ReturnUrl','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                $this->redirect('Wap0/Login/login');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '未登录']);
            }
            die;
        }

        $this->ERPId = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap0_User.UserName')])->getField('ERPId');
        $CusShortName = M()->table('Customer')->where(['CusId' => $this->ERPId])->getField('CusShortName');

        $this->assign([
            'HeaderFlag' => $CusShortName.'（'.$this->ERPId.'）',
            'config' => $this->config,
        ]);

        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
    }

    public function detail(){
        $get = I('get.');
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $this->ERPId,
            'CusPoNo' => $get['CusPoNo'],
        ];
        $wo = M()->table('WebappOrder')->where($where)->field('BoardId,MatNo,IsDel,DelTime,Checked,CheckTime,IsGroup,BuildTime')->find();
        if(!$wo){
            $this->error('参数错误');die;
        }
        $wgo = M()->table('WebGroupOrder')->where($where)->field('WebProductId,UsePay,Title,FirstPic,Price,MarketPrice,Cost,SaveCost')->find();
        if($wo['IsGroup'] === '0' || $wgo['UsePay'] === '0'){
            $this->error('非团购支付订单');die;
        }
        if(!$wgo['FirstPic']){
            $wgo['FirstPic'] = C('ProductNoPicDefaultPic');
        }
        $wgo['Price'] = PriceFormat($wgo['Price']);
        $wgo['MarketPrice'] = PriceFormat($wgo['MarketPrice']);
        $wgo['Cost'] = sprintf('%.2f',$wgo['Cost']);
        $wgo['SaveCost'] = sprintf('%.2f',$wgo['SaveCost']);
        $wp = M()->table('WebPay')->where($where)->field('PayDeadlineTime,OneCentPay,PayAmount,Paid,PaidTime,Apply,ApplyTime,ApplyReason,Refund,RefundTime,WxOutTradeNo,AliOutTradeNo')->find();
        $timeline = [['descr' => '下单成功，订单金额：¥'.$wgo['Cost'].'，节省金额：¥'.$wgo['SaveCost'],'datetime' => date('Y-m-d H:i:s',strtotime($wo['BuildTime']))]];
        if($wp['Paid'] === '1'){
            if($wp['WxOutTradeNo']){
                $way = '微信';
            }elseif($wp['AliOutTradeNo']){
                $way = '支付宝';
            }
            $PayAmount = sprintf('%.2f',$wp['PayAmount']);
            $timeline[] = ['descr' => $way.'支付成功，'.($wp['OneCentPay']?'使用1分钱支付模式，':'').'支付金额：¥'.$PayAmount,'datetime' => date('Y-m-d H:i:s',$wp['PaidTime'])];
            if($wo['Checked'] === '1'){
                $timeline[] = ['descr' => '已审核，无法申请退款','datetime' => date('Y-m-d H:i:s',strtotime($wo['CheckTime']))];
            }else{
                if($wp['Apply'] === '1'){
                    $timeline[] = ['descr' => '申请退款成功，退款原因：'.$wp['ApplyReason'].'，需退款金额：¥'.$PayAmount,'datetime' => date('Y-m-d H:i:s',$wp['ApplyTime'])];
                }
            }
            if($wp['Refund'] === '1'){
                if($wo['Checked'] === '0' && $wp['Apply'] === '1'){
                    $prefix = '同意退款申请，';
                }elseif($wo['Checked'] === '1' && $wp['Apply'] === '0'){
                    $prefix = 'ERP删除订单，';
                }
                $timeline[] = ['descr' => $prefix.$way.'退款成功，退款金额：¥'.$PayAmount,'datetime' => date('Y-m-d H:i:s',$wp['RefundTime'])];
            }
//            if($wo['IsDel'] === '1'){
//                $timeline[] = ['descr' => '已删除','datetime' => date('Y-m-d H:i:s',strtotime($wo['DelTime']))];
//            }
        }
        $timeline = array_reverse($timeline);
        unset(
            $wo['ROW_NUMBER'],$wo['DelTime'],$wo['CheckTime'],$wo['IsGroup'],$wo['BuildTime'],
            $wgo['ROW_NUMBER'],$wgo['UsePay'],
            $wp['ROW_NUMBER'],$wp['OneCentPay'],$wp['PayAmount'],$wp['PaidTime'],$wp['ApplyTime'],
            $wp['ApplyReason'],$wp['RefundTime'],$wp['WxOutTradeNo'],$wp['AliOutTradeNo']
        );
        $this->assign([
            'LayoutTitle' => '详情',
            'CusPoNo' => $get['CusPoNo'],
            'wo' => jejuu($wo),
            'wgo' => jejuu($wgo),
            'wp' => jejuu($wp),
            'timeline' => jejuu($timeline),
        ]);
        $this->display();
    }

    //付款方式
    public function way(){
        $get = I('get.');
        if(!$get['CusPoNo']){
            $this->error('参数错误');die;
        }
        $CusPoNo = explode(',',$get['CusPoNo']);
        if(count($CusPoNo) !== count(array_unique($CusPoNo))){
            $this->error('参数错误');die;
        }
        $wu = M()->table('WebappUser')->where(['ERPId' => $this->ERPId,'UserType' => 0])->field('OneCentPay')->find();
        $orderName = [];
        $PayDeadlineTime = NULL;
        $PayAmount = 0;
        foreach($CusPoNo as $k => $v){
            $where = [
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $v,
            ];
            $wp = M()->table('WebPay')->where($where)->field('PayDeadlineTime,Paid')->find();
            if(!$wp){
                $this->error('参数错误');die;
            }
            if($wp['Paid'] === '1'){
                $this->error('订单已付款');die;
            }
            if($PayDeadlineTime){
                $PayDeadlineTime = min($PayDeadlineTime,$wp['PayDeadlineTime']);
            }else{
                $PayDeadlineTime = $wp['PayDeadlineTime'];
            }
            $wo = M()->table('WebappOrder')->where($where)->field('BoardId,MatNo,IsDel')->find();
            if($wo['IsDel'] === '1'){
                $this->error('订单已删除');die;
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
            $PayAmount += $_PayAmount;
        }
        $orderName = implode('，',$orderName);
        $PayAmount = sprintf('%.2f',$PayAmount);
        $this->assign([
            'LayoutTitle' => '付款方式',
            'CusId' => $this->ERPId,
            'CusPoNo' => $get['CusPoNo'],
            'secret' => D('Func')->makeSecret($this->ERPId,$get['CusPoNo'],TRUE),
            'OneCentPay' => $wu['OneCentPay']?1:0,
            'orderName' => $orderName,
            'PayAmount' => $PayAmount,
            'PayDeadlineTime' => $PayDeadlineTime,
        ]);
        $this->display();
    }

    public function apply(){
        $get = I('get.');
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $this->ERPId,
            'CusPoNo' => $get['CusPoNo'],
        ];
        $wp = M()->table('WebPay')->where($where)->field('Paid')->find();
        if(!$wp){
            $this->error('参数错误');die;
        }
        if($wp['Paid'] === '0'){
            $this->error('订单未付款');die;
        }
        $wo = M()->table('WebappOrder')->where($where)->field('Checked')->find();
        if($wo['Checked'] === '1'){
            $this->error('订单已审核');die;
        }
        $this->assign([
            'LayoutTitle' => '申请退款',
            'CusPoNo' => $get['CusPoNo'],
        ]);
        $this->display();
    }

    public function getApplyInfo_api(){
        $get = I('get.');
        $wp = M()->table('WebPay')->where([
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $this->ERPId,
            'CusPoNo' => $get['CusPoNo'],
        ])->field('PayAmount,Apply,ApplyReason,Refund')->find();
        unset($wp['ROW_NUMBER']);
        $wp['PayAmount'] = sprintf('%.2f',$wp['PayAmount']);
        echo jejuu($wp);
    }

    public function apply_api(){
        $post = I('post.');
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $this->ERPId,
            'CusPoNo' => $post['CusPoNo'],
        ];
        $wp = M()->table('WebPay')->where($where)->field('Paid,Apply')->find();
        if(!$wp){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($wp['Paid'] === '0'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单未付款',
            ]);die;
        }
        $wo = M()->table('WebappOrder')->where($where)->field('Checked')->find();
        if($wo['Checked'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单已审核',
            ]);die;
        }
        if($wp['Apply'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单已申请退款',
            ]);die;
        }
        if(!$post['ApplyReason']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '没有提交退款原因',
            ]);die;
        }
        if(M()->table('WebPay')->where($where)->save([
                'Apply' => 1,
                'ApplyTime' => time(),
                'ApplyReason' => $post['ApplyReason'],
            ]) !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '订单申请退款成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单申请退款失败',
            ]);
        }
    }

}
