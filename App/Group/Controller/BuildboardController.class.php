<?php
namespace Group\Controller;
use Think\Controller;
//团购纸板下单
class BuildboardController extends Controller{

    var $ERPId;
    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        if(!$this->config['UseBoardGroup']){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                $this->error('纸板团购功能未开启');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '纸板团购功能未开启']);
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

        if($this->config['Wap0Right'] && !M()->table('WebUserRight')->where(['UserName' => session('ERP_Wap0_User.UserName'),'URName' => '纸板下单'])->find()){
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

    //简单纸板下单
    public function s(){
        $get = I('get.');
        $r = D('Product')->checkBase($get['Id'],'Board');
        if(!$r['bool']){
            $this->error($r['reason']);die;
        }
        $ProductInfo = M()->table('WebProduct')
            ->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])
            ->field('Id,BoardId,Title,IsRangePrice,Price,MarketPrice,BuildMin,BuildMax,BeginTime,EndTime,Pic')
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
        $ProductInfo['BuildMin'] = sprintf('%.3f',$ProductInfo['BuildMin']);
        $ProductInfo['BuildMax'] = sprintf('%.3f',$ProductInfo['BuildMax']);
        $ProductInfo['Pic'] = $ProductInfo['Pic']?explode(',',$ProductInfo['Pic']):[C('ProductNoPicDefaultPic')];
        $ScoreNameSelect = $this->config['BuildScoreName']?explode(',',$this->config['BuildScoreName']):[];
        $CustomerDNSelect = M()->table('CustomerDN')->where(['CusId' => $this->ERPId])->field('CusSubNo,SubDNAddress')->select();
        foreach($CustomerDNSelect as $k => $v){
            unset($CustomerDNSelect[$k]['ROW_NUMBER']);
        }
        $this->assign([
            'LayoutTitle' => '简单纸板下单',
            'ScoreNameSelect' => jejuu($ScoreNameSelect),
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
            'ProductInfo' => jejuu($ProductInfo),
        ]);
        $this->display();
    }

    //纸箱纸板下单
    public function c(){
        $get = I('get.');
        $r = D('Product')->checkBase($get['Id'],'Board');
        if(!$r['bool']){
            $this->error($r['reason']);die;
        }
        $ProductInfo = M()->table('WebProduct')
            ->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])
            ->field('Id,BoardId,Title,IsRangePrice,Price,MarketPrice,BuildMin,BuildMax,BeginTime,EndTime,Pic')
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
        $ProductInfo['BuildMin'] = sprintf('%.3f',$ProductInfo['BuildMin']);
        $ProductInfo['BuildMax'] = sprintf('%.3f',$ProductInfo['BuildMax']);
        $ProductInfo['Pic'] = $ProductInfo['Pic']?explode(',',$ProductInfo['Pic']):[C('ProductNoPicDefaultPic')];
        if($this->config['BuildAutoGetTonLenAndULen']){
            $BoardId = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->getField('BoardId');
            $sql = 'SELECT ISNULL(c.TonLen,f.TonLen) AS TonLen,ISNULL(c.ULen,f.ULen) AS ULen FROM FluteRate f 
INNER JOIN BoardCode b ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = f.Flute AND b.LayerCount = f.LayerCount 
LEFT OUTER JOIN CusSpec1 c ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = c.Flutes AND b.LayerCount = c.LayerCount AND c.CusId = \''.$this->ERPId.'\' WHERE b.BoardId = \''.$BoardId.'\'';
            $data = D('Odbc')->query($sql,'fetch');
            $TonLen = sprintf('%.1f',$data['TonLen']);
            $ULen = sprintf('%.1f',$data['ULen']);
        }else{
            $TonLen = '';
            $ULen = '';
        }
        $CustomerDNSelect = M()->table('CustomerDN')->where(['CusId' => $this->ERPId])->field('CusSubNo,SubDNAddress')->select();
        foreach($CustomerDNSelect as $k => $v){
            unset($CustomerDNSelect[$k]['ROW_NUMBER']);
        }
        $BoxCodeSelect = M()->table('BoxCode')->field('BoxId,BoxName,LengthF,WidthF')->select();
        foreach($BoxCodeSelect as $k => $v){
            unset($BoxCodeSelect[$k]['ROW_NUMBER']);
        }
        $TonLenSelect = $this->config['BuildTonLen']?explode(',',$this->config['BuildTonLen']):[];
        foreach($TonLenSelect as $k => $v){
            $TonLenSelect[$k] = sprintf('%.1f',$v);
        }
        $ULenSelect = $this->config['BuildULen']?explode(',',$this->config['BuildULen']):[];
        foreach($ULenSelect as $k => $v){
            $ULenSelect[$k] = sprintf('%.1f',$v);
        }
        $this->assign([
            'LayoutTitle' => '纸箱纸板下单',
            'TonLen' => $TonLen,
            'ULen' => $ULen,
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
            'BoxCodeSelect' => jejuu($BoxCodeSelect),
            'TonLenSelect' => jejuu($TonLenSelect),
            'ULenSelect' => jejuu($ULenSelect),
            'ProductInfo' => jejuu($ProductInfo),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //简单纸板下单
    public function s_api(){
        $post = I('post.');
        $r1 = D('Product')->checkBase($post['Id'],'Board');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Bcheck')->s($post);
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
        $BuildScoreName = $this->config['BuildScoreName']?explode(',',$this->config['BuildScoreName']):[];
        $key = array_search($post['ScoreName'],$BuildScoreName,true);
        $time = time();
        $BdQty = $post['OrdQty'];//简单纸板下单的纸板数=订单数
        $Area = $post['Length'] * $post['Width'] * $BdQty / 1000000;
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $post['Id']])->field('BoardId,Title,Pic,IsRangePrice,Price,MarketPrice')->find();
        if($wp['IsRangePrice']){
            $wprp = M()->table('WebProductRangePrice')->where(['WebProductId' => $post['Id'],'From' => ['elt',$Area],'To' => ['egt',$Area]])->field('Price')->order('Price')->find();
            $Price = $wprp['Price'];
        }else{
            $Price = $wp['Price'];
        }
        $Cost = $Price * $Area;
        if($Cost <= 0){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '下单金额必须大于0',
            ]);die;
        }
        $r4 = D('Product')->checkSize($post['Id'],$this->ERPId,$Area);
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
                'CType' => 's',
                'BoardId' => $wp['BoardId'],
                'Length' => $post['Length'],
                'Width' => $post['Width'],
                'ScoreType' => $key,
                'ScoreInfo' => $key === 0?NULL:$post['ScoreInfo'],
                'OrdQty' => $post['OrdQty'],
                'BdQty' => $BdQty,
                'Area' => $Area,
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
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $Area) - $Cost),
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
                    //'Id' => $r5,
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
    public function bcheck_s_api(){
        $post = I('post.');
        $r1 = D('Product')->checkBase($post['Id'],'Board');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Bcheck')->s($post);
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

    //纸箱纸板下单
    public function c_api(){
        $post = I('post.');
        $r1 = D('Product')->checkBase($post['Id'],'Board');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Bcheck')->c($post);
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
        $Area = $post['Length'] * $post['Width'] * $post['BdQty'] / 1000000;
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $post['Id']])->field('BoardId,Title,Pic,IsRangePrice,Price,MarketPrice')->find();
        if($wp['IsRangePrice']){
            $wprp = M()->table('WebProductRangePrice')->where(['WebProductId' => $post['Id'],'From' => ['elt',$Area],'To' => ['egt',$Area]])->field('Price')->order('Price')->find();
            $Price = $wprp['Price'];
        }else{
            $Price = $wp['Price'];
        }
        $Cost = $Price * $Area;
        if($Cost <= 0){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '下单金额必须大于0',
            ]);die;
        }
        $r4 = D('Product')->checkSize($post['Id'],$this->ERPId,$Area);
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
                'CType' => 'c',
                'BoardId' => $wp['BoardId'],
                'Length' => $post['Length'],
                'Width' => $post['Width'],
                'OrdQty' => $post['OrdQty'],
                'BdQty' => $post['BdQty'],
                'BdMultiple' => $post['BdMultiple'],
                'BoxId' => $post['BoxId'],
                'BoxL' => $post['BoxL'],
                'BoxW' => $post['BoxW'],
                'BoxH' => $post['BoxH'],
                'TonLen' => $post['TonLen'],
                'ULen' => $post['ULen'],
                'Area' => $Area,
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
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $Area) - $Cost),
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
                    //'Id' => $r5,
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
    public function bcheck_c_api(){
        $post = I('post.');
        $r1 = D('Product')->checkBase($post['Id'],'Board');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Bcheck')->c($post);
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

    //根据箱型获取公式
    public function getFormula_api(){
        $data = M()->table('BoxCode')->where(['BoxId' => $_GET['BoxId']])->field('LengthF,WidthF,Multiple')->find();
        unset($data['ROW_NUMBER']);
        echo jejuu($data);
    }

    public function calcAreaCost_api(){
        $get = I('get.');
        //根据是否提交了纸板数，判断区分简单纸板下单/纸箱纸板下单
        if($get['BdQty']){
            $BdQty = $get['BdQty'];//纸箱纸板下单
        }else{
            $BdQty = $get['OrdQty'];//简单纸板下单（纸板数=订单数）
        }
        $Area = $get['Length'] * $get['Width'] * $BdQty / 1000000;
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('IsRangePrice,Price,MarketPrice,BuildMin,BuildMax')->find();
        if($Area < $wp['BuildMin'] || $Area > $wp['BuildMax']){
            echo jejuu([
                'Area' => $Area,
                'validArea' => 0,
            ]);die;
        }
        if($wp['IsRangePrice']){
            $help = [];
            $wprp1 = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id'],'From' => ['elt',$Area],'To' => ['egt',$Area]])->field('To,Price')->order('Price')->find();
            $wprp2 = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id'],'From' => ['egt',$wprp1['To']]])->field('From,Price')->select();
            foreach($wprp2 as $k => $v){
                $help[$k] = [
                    'BdQty' => ceil($v['From']/($get['Length'] * $get['Width'] / 1000000)),
                    'Price' => PriceFormat($v['Price']),
                ];
            }
            $Cost = $wprp1['Price'] * $Area;
            echo jejuu([
                'Area' => $Area,
                'validArea' => 1,
                'Cost' => sprintf('%.2f',$Cost),
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $Area) - $Cost),
                'IsRangePrice' => 1,
                'Price' => PriceFormat($wprp1['Price']),
                'help' => $help,
            ]);
        }else{
            $Cost = $wp['Price'] * $Area;
            echo jejuu([
                'Area' => $Area,
                'validArea' => 1,
                'Cost' => sprintf('%.2f',$Cost),
                'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $Area) - $Cost),
                'IsRangePrice' => 0,
            ]);
        }
    }

}
