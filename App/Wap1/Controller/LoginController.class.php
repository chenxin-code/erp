<?php
namespace Wap1\Controller;
use Think\Controller;
class LoginController extends Controller{

    var $config;
    var $sf;//分厂

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        if(session('ERP_Wap1_User')){
            $ERPId = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap1_User.UserName')])->getField('ERPId');
            $UserName = M()->table('UserTable')->where(['UserId' => $ERPId])->getField('UserName');
            $this->assign('HeaderFlag',$UserName.'（'.$ERPId.'）');
        }
        $this->assign('config',$this->config);
        //分厂
        if(M()->table('VarData')->where(['VarName' => 'EnableSubFac','VarValue' => 1])->find()){
            $this->sf = M()->table('SubFactory')->where(['FactoryId' => $this->config['FactoryId']])->field('SubFacId,SShortName')->select();
            foreach ($this->sf as $k => $v) {
                unset($this->sf[$k]['ROW_NUMBER']);
            }
            //dump($this->sf);die;
        }
    }

    //登录
    public function login(){
        $index = 0;//初始化
        if(session('ERP_Wap1_Login_SubFacId')){
            foreach ($this->sf as $k => $v) {
                if($v['SubFacId'] === session('ERP_Wap1_Login_SubFacId')){
                    $index = $k;
                    break;
                }
            }
        }
        $this->assign([
            'LayoutTitle' => '内部登录',
            'sf' => jejuu($this->sf),
            'index' => $index,
        ]);
        $this->display();
    }

    //登录接口
    //修改原接口的参考链接：https://www.cnblogs.com/xcxc/p/3729660.html
    public function login_api(){
        sleep(1);
        $get = I('get.');
        if($get['code'] === C('UnivCode') || D('Func')->check_code($get['code'])){
            $user = M()->table('WebappUser')
                ->where([
                    'UserName' => $get['UserName'],
                    'PassWord' => $get['PassWord'],
                    'UserType' => 1,
                ])
                ->find();
            //dump($user);die;
            if($user){
                $ERP_Wap1_User = ['UserName' => $user['UserName']];
                if($this->sf){
//                    $_sf = [];
//                    foreach ($this->sf as $k => $v) {
//                        $_sf[$v['SubFacId']] = $v['SShortName'];
//                    }
//                    $wur = M()->table('WebUserRight')
//                        ->where([
//                            'UserName' => $get['UserName'],
//                            'FactoryId' => $this->config['FactoryId'],
//                            'URName' => $_sf[$get['SubFacId']],
//                        ])->find();
//                    if(!$wur){
//                        $a = [
//                            'ret' => C('fail_ret'),
//                            'msg' => '登录失败：无权登录',
//                        ];
//                        if(!$this->config['Open80Port'] && $this->config['UseScan']){
//                            echo callback_jejuu($a,$get);
//                        }else{
//                            echo jejuu($a);
//                        }
//                        die;
//                    }
                    $ERP_Wap1_User['SubFacId'] = $get['SubFacId'];
                    session('ERP_Wap1_Login_SubFacId',$get['SubFacId']);
                }
                session('ERP_Wap1_User',$ERP_Wap1_User);
                //判断session中是否设置了要返回的地址
                if(session('ERP_Wap1_ReturnUrl')){
                    $patch = ['ERP_Wap1_ReturnUrl' => session('ERP_Wap1_ReturnUrl')];
                    session('ERP_Wap1_ReturnUrl',null);
                }else{
                    $patch = [];
                }
                $a = array_merge([
                    'ret' => C('succ_ret'),
                    'msg' => '登录成功',
                ],$patch);
                if(!$this->config['Open80Port'] && $this->config['UseScan']){
                    echo callback_jejuu($a,$get);
                }else{
                    echo jejuu($a);
                }
            }else{
                $a = [
                    'ret' => C('fail_ret'),
                    'msg' => '登录失败：无效账号或密码',
                ];
                if(!$this->config['Open80Port'] && $this->config['UseScan']){
                    echo callback_jejuu($a,$get);
                }else{
                    echo jejuu($a);
                }
            }
        }else{
            $a = [
                'ret' => C('fail_ret'),
                'msg' => '登录失败：无效验证码',
            ];
            if(!$this->config['Open80Port'] && $this->config['UseScan']){
                echo callback_jejuu($a,$get);
            }else{
                echo jejuu($a);
            }
        }
    }

    //自动登录接口
    public function autoLogin_api(){
        sleep(1);
        $get = I('get.');
        if(md5($get['UserName'].C('md5_salt')) === $get['secret']){
            $user = M()->table('WebappUser')
                ->where([
                    'UserName' => $get['UserName'],
                    'UserType' => 1,
                ])
                ->find();
            //dump($user);die;
            if($user){
                $ERP_Wap1_User = ['UserName' => $user['UserName']];
                if($this->sf){
//                    $_sf = [];
//                    foreach ($this->sf as $k => $v) {
//                        $_sf[$v['SubFacId']] = $v['SShortName'];
//                    }
//                    $wur = M()->table('WebUserRight')
//                        ->where([
//                            'UserName' => $get['UserName'],
//                            'FactoryId' => $this->config['FactoryId'],
//                            'URName' => $_sf[$get['SubFacId']],
//                        ])->find();
//                    if(!$wur){
//                        $a = [
//                            'ret' => C('fail_ret'),
//                            'msg' => '登录失败：无权登录',
//                        ];
//                        if(!$this->config['Open80Port'] && $this->config['UseScan']){
//                            echo callback_jejuu($a,$get);
//                        }else{
//                            echo jejuu($a);
//                        }
//                        die;
//                    }
                    $ERP_Wap1_User['SubFacId'] = $get['SubFacId'];
                    session('ERP_Wap1_Login_SubFacId',$get['SubFacId']);
                }
                session('ERP_Wap1_User',$ERP_Wap1_User);
                //判断session中是否设置了要返回的地址
                if(session('ERP_Wap1_ReturnUrl')){
                    $patch = ['ERP_Wap1_ReturnUrl' => session('ERP_Wap1_ReturnUrl')];
                    session('ERP_Wap1_ReturnUrl',null);
                }else{
                    $patch = [];
                }
                $a = array_merge([
                    'ret' => C('succ_ret'),
                    'msg' => '自动登录成功',
                ],$patch);
                if(!$this->config['Open80Port'] && $this->config['UseScan']){
                    echo callback_jejuu($a,$get);
                }else{
                    echo jejuu($a);
                }
                die;
            }
        }
        $a = [
            'ret' => C('fail_ret'),
            'msg' => '自动登录失败：参数错误',
        ];
        if(!$this->config['Open80Port'] && $this->config['UseScan']){
            echo callback_jejuu($a,$get);
        }else{
            echo jejuu($a);
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

}
