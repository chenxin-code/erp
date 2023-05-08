<?php
namespace Group\Controller;
use Think\Controller;
//团购淘宝箱下单
class BuildboxController extends Controller{

    var $ERPId;
    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        if(!$this->config['UseBoxGroup']){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                $this->error('淘宝箱团购功能未开启');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '淘宝箱团购功能未开启']);
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
            'ERPId' => $this->ERPId,
            'config' => $this->config,
        ]);

        if($this->config['Wap0Right'] && !M()->table('WebUserRight')->where(['UserName' => session('ERP_Wap0_User.UserName'),'URName' => '淘宝箱下单'])->find()){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                $this->error('没有权限');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '没有权限']);
            }
            die;
        }

        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(CONTROLLER_NAME.'/'.ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
    }

    /********** 页面 **********/

    public function t(){
        $get = I('get.');
        $r = D('Product')->checkBase($get['Id'],'Box');
        if(!$r['bool']){
            $this->error($r['reason']);die;
        }
        $ProductInfo = M()->table('WebProduct')
            ->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])
            ->field('Id,MatNo,Title,IsRangePrice,Price,MarketPrice,BuildMin,BuildMax,BeginTime,EndTime,Pic')
            ->find();
        unset($ProductInfo['ROW_NUMBER']);
        if($ProductInfo['IsRangePrice']){
            $MinPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->min('Price');
            $MaxPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->max('Price');
            $ProductInfo['Price'] = PriceFormat($MinPrice).'~'.PriceFormat($MaxPrice);
        }else{
            $ProductInfo['Price'] = PriceFormat($ProductInfo['Price']);
        }
        $ProductInfo['MarketPrice'] = PriceFormat($ProductInfo['MarketPrice']);
        $ProductInfo['BuildMin'] = (int)$ProductInfo['BuildMin'];
        $ProductInfo['BuildMax'] = (int)$ProductInfo['BuildMax'];
        $ProductInfo['Pic'] = $ProductInfo['Pic']?explode(',',$ProductInfo['Pic']):[C('ProductNoPicDefaultPic')];
        $CustomerDNSelect = M()->table('CustomerDN')->where(['CusId' => $this->ERPId])->field('CusSubNo,SubDNAddress')->select();
        foreach($CustomerDNSelect as $k => $v){
            unset($CustomerDNSelect[$k]['ROW_NUMBER']);
        }
        $this->assign([
            'LayoutTitle' => '淘宝箱下单',
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
            'ProductInfo' => jejuu($ProductInfo),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    public function t_api(){
        $post = I('post.');
        $r1 = D('Product')->checkBase($post['Id'],'Box');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Bcheck')->t($post);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        $r3 = D('Wap0/Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r3['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r3['reason'],
            ]);die;
        }
        $time = time();
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $post['Id']])->field('MatNo,Title,Pic,IsRangePrice,Price,MarketPrice')->find();
        if($wp['IsRangePrice']){
            $wprp = M()->table('WebProductRangePrice')->where(['WebProductId' => $post['Id'],'From' => ['elt',$post['OrdQty']],'To' => ['egt',$post['OrdQty']]])->field('Price')->order('Price')->find();
            $Price = $wprp['Price'];
        }else{
            $Price = $wp['Price'];
        }
        $Cost = $Price * $post['OrdQty'];
        if($Cost <= 0){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '下单金额必须大于0',
            ]);die;
        }
        $r4 = D('Product')->checkSize($post['Id'],$this->ERPId,$post['OrdQty']);
        if(!$r4['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r4['reason'],
            ]);die;
        }
        try {
            M()->startTrans();
            $r5 = M()->table('WebappOrder')->add([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $r3['CusPoNo'],
                'CType' => 't',
                'PON' => $post['PON'],
                'MatNo' => $wp['MatNo'],
                'OrdQty' => $post['OrdQty'],
                'DeliveryDate' => $post['DeliveryDate'],
                'CusSubNo' => $post['CusSubNo'],
                'DNRemark' => $post['DNRemark'],
                'ProRemark' => $post['ProRemark'],
                'BuildDate' => date('Y-m-d',$time),
                'BuildTime' => date('Y-m-d H:i:s',$time),
                'IsDel' => 0,
                'Checked' => 0,
                'IsCard' => 0,
                'IsGroup' => 1,
            ]);
            $UsePay = $this->config['UseWxPay'] || $this->config['UseAliPay'];
            $r6 = M()->table('WebGroupOrder')->add([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $r3['CusPoNo'],
                'WebProductId' => $post['Id'],
                'UsePay' => $UsePay,
                'Title' => $wp['Title'],
                'FirstPic' => $wp['Pic']?explode(',',$wp['Pic'])[0]:NULL,
                'Price' => $Price,
                'MarketPrice' => $wp['MarketPrice'],
                'Cost' => sprintf('%.2f',$Cost),
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $post['OrdQty']) - $Cost),
            ]);
            $r8 = $UsePay?M()->table('WebPay')->add([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $r3['CusPoNo'],
                'ValidPayTime' => $this->config['ValidPayTime'],
                'PayDeadlineTime' => $time + $this->config['ValidPayTime'],
                'Paid' => 0,
                'Apply' => 0,
                'Refund' => 0,
            ]):TRUE;
            if($r5 && $r6 && $r8){
                M()->commit();
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '下单成功',
                    'CusPoNo' => $r3['CusPoNo'],
                ]);
            }else{
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '下单失败',
                ]);
            }
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);
        }
    }
    public function bcheck_t_api(){
        $post = I('post.');
        $r1 = D('Product')->checkBase($post['Id'],'Box');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Bcheck')->t($post);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        $r3 = D('Wap0/Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r3['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r3['reason'],
            ]);die;
        }
        echo jejuu(['ret' => C('succ_ret')]);
    }

    public function calcCost_api(){
        $get = I('get.');
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('IsRangePrice,Price,MarketPrice,BuildMin,BuildMax')->find();
        if($get['OrdQty'] < $wp['BuildMin'] || $get['OrdQty'] > $wp['BuildMax']){
            echo jejuu([
                'validOrdQty' => 0,
            ]);die;
        }
        if($wp['IsRangePrice']){
            $help = [];
            $wprp1 = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id'],'From' => ['elt',$get['OrdQty']],'To' => ['egt',$get['OrdQty']]])->field('To,Price')->order('Price')->find();
            $wprp2 = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id'],'From' => ['egt',$wprp1['To']]])->field('From,Price')->select();
            foreach($wprp2 as $k => $v){
                $help[$k] = [
                    'OrdQty' => (int)$v['From'],
                    'Price' => PriceFormat($v['Price']),
                ];
            }
            $Cost = $wprp1['Price'] * $get['OrdQty'];
            echo jejuu([
                'validOrdQty' => 1,
                'Cost' => sprintf('%.2f',$Cost),
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $get['OrdQty']) - $Cost),
                'IsRangePrice' => 1,
                'Price' => PriceFormat($wprp1['Price']),
                'help' => $help,
            ]);
        }else{
            $Cost = $wp['Price'] * $get['OrdQty'];
            echo jejuu([
                'validOrdQty' => 1,
                'Cost' => sprintf('%.2f',$Cost),
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $get['OrdQty']) - $Cost),
                'IsRangePrice' => 0,
            ]);
        }
    }

}
