<?php
namespace Wap0\Controller;
use Think\Controller;
class Index1Controller extends Controller{

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
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(CONTROLLER_NAME.'/'.ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
    }

    //首页
    public function index(){
        $AdverPic = $this->config['AdverPic'];
        $AdverPic = $AdverPic?explode(',',$AdverPic):[];
        $this->assign([
            'LayoutTitle' => '首页',
            'AdverPic' => jejuu($AdverPic),
        ]);
        $this->display();
    }

    //注册
    public function reg(){
        if(!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']){
            $this->error('团购功能未开启');die;
        }
        if($this->config['UseBoxGroup'] && !(M()->table('MatTable')->where(['CusId' => 'TAOBAO'.$this->config['FactoryId']])->select())){
            $this->error('没有货品号');die;
        }
        $this->assign([
            'LayoutTitle' => '注册',
        ]);
        $this->display();
    }

    //注册接口
    public function reg_api(){
        if(!$this->config['UseBoardGroup'] && !$this->config['UseBoxGroup']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '团购功能未开启',
            ]);die;
        }
        $post = I('post.');
        if(!$post['code']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交验证码',
            ]);die;
        }
        if(!D('Func')->check_code($post['code'])){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '无效验证码',
            ]);die;
        }
        if(!$post['UserName']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交账号',
            ]);die;
        }
        if(!preg_match('@'.C('UserNamePattern').'@',$post['UserName'])){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非法账号',
            ]);die;
        }
        if(!$post['PassWord']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交密码',
            ]);die;
        }
        if(!$post['CusSubChiName']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交客户全称',
            ]);die;
        }
        if(!$post['CusShortName']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交客户简称',
            ]);die;
        }
        if(!$post['ERPId']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交客户编号',
            ]);die;
        }
        if(!preg_match('@'.C('CusIdPattern').'@',$post['ERPId'])){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非法客户编号',
            ]);die;
        }
        if(!$post['SubDNAddress']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交送货地址',
            ]);die;
        }
        if(!$post['SubContactPerson']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交联系人',
            ]);die;
        }
        if(!$post['SubTelNo']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交联系电话',
            ]);die;
        }
        $ERPId = $post['ERPId'].'-T';
        try {
            if($this->config['UseBoxGroup'] && !(M()->table('MatTable')->where(['CusId' => 'TAOBAO'.$this->config['FactoryId']])->select())){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '没有货品号',
                ]);die;
            }
            if(M()->table('WebappUser')->where(['UserName' => $post['UserName']])->find()){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '账号已使用',
                ]);die;
            }
            if(M()->table('Customer')->where(['CusId' => $ERPId])->find()){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '客户编号已使用',
                ]);die;
            }
            M()->startTrans();
            $r1 = M()->table('WebappUser')->add([
                'UserName' => $post['UserName'],
                'PassWord' => $post['PassWord'],
                'UserType' => 0,
                'ERPId' => $ERPId,
                'TaskId' => NULL,
                'OneCentPay' => 0,
            ]);
            $r2 = M()->table('Customer')->add([
                'CusId' => $ERPId,'FactoryId' => $this->config['FactoryId'],
                'CusShortName' => $post['CusShortName'],'BuyBox' => $this->config['UseBoxGroup']?1:0,'BuyBoard' => $this->config['UseBoardGroup']?1:0,
                'CusKind' => 'T','SaAreaAddTrim' => 0,'ZiTiDiscount' => 1,'OrderDisRate' => 1,'MinAmtCond' => 0,
                'PreAmt' => 0,'BdDecPoint' => 2,'BdPriceType' => 1,'BoxDecPoint' => 2,'BdTDecPoint' => 2,'AreaPoint' => 2,
                'BoxPriceType' => 0,'Stopped' => 0,'LastMBDate' => '2017-01-01','SettleDay' => 0,'CreditTerm' => 0,'Checked'  => 0,'Distance' => 0,
                'CusSubChiName' => $post['CusSubChiName'],'SubDNAddress' => $post['SubDNAddress'],'SubTelNo' => $post['SubTelNo'],
                'SubFaxNo' => $post['SubFaxNo'],'SubContactPerson' => $post['SubContactPerson'],
                'BuyColor' => 0,'SaAreaAddArea'  => 0,'MBDisRate' => 1.0,'Addm' => 0,'Addn' => 0,'AddCut' => 0,
                'CreditTAD' => 0,'DInvoAmt' => 0,'LimitDate' => '2117-01-01',
                'BuyStock' => 0,'SAddn' => 0,'LAddCut' => '','TransSPAdd' => 0,'IsDefTax' => 1,
                'CushType' => '现金','XCusQuoType' => '2','RemindDay' => 0,'L2OrderDisRate' => 1,'L2AddCut' => 0,'IsHide' => 0,
            ]);
            $r3 = $this->config['UseBoxGroup']?M()->execute('INSERT INTO MatTable (CusId,MatNo,MatName,Stopped,PTaxInclude,PUnitPrice,PChecked,IsCutNo,IsPrintNo,PSquarePrice,MTRemark,SpCheck) SELECT \''.$ERPId.'\',MatNo,MatName,Stopped,PTaxInclude,PUnitPrice,PChecked,IsCutNo,IsPrintNo,PSquarePrice,MTRemark,SpCheck FROM MatTable WHERE CusId = \'TAOBAO'.$this->config['FactoryId'].'\''):TRUE;
            $r4 = $this->config['UseBoardGroup']?M()->table('WebUserRight')->add([
                'UserName' => $post['UserName'],
                'FactoryId' => '0',
                'URName' => '纸板下单',
                'Authed' => 1,
            ]):TRUE;
            $r5 = $this->config['UseBoxGroup']?M()->table('WebUserRight')->add([
                'UserName' => $post['UserName'],
                'FactoryId' => '0',
                'URName' => '淘宝箱下单',
                'Authed' => 1,
            ]):TRUE;
            if($r1 && $r2 && $r3 && $r4 && $r5){
                M()->commit();
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '注册成功',
                ]);
            }else{
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '注册失败',
                ]);
            }
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);
        }
    }

    //生成验证码
    public function make_code(){
        ob_clean();
        $verify = new \Think\Verify([
            'fontSize'    =>    25,
            'length'      =>    3,
            'codeSet'      =>   '0123456789',
            'useNoise'    =>    false,
            'useCurve'   =>     false,
            'useImgBg'   =>     true,
        ]);
        $verify->entry();
    }

    //联系方式
    public function contact(){
        $wc = [];
        foreach (C('ContactIcon') as $v1) {
            $_wc = M()->table('WebContact')->where(['Icon' => $v1])->field('Icon,Name,Content')->select();
            foreach ($_wc as $k2 => $v2) {
                unset($_wc[$k2]['ROW_NUMBER']);
            }
            $wc = array_merge($wc,$_wc);
        }
        $this->assign([
            'LayoutTitle' => '联系方式',
            'wc' => jejuu($wc),
        ]);
        $this->display();
    }

}
