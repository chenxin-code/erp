<?php
namespace Wap0\Controller;
use Think\Controller;
class LoginController extends Controller{

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

    //登录
    public function login(){
        $this->assign([
            'LayoutTitle' => '外部登录',
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
                    'UserType' => 0,
                ])
                ->find();
            //dump($user);die;
            if($user){
                $ERP_Wap0_User = ['UserName' => $user['UserName']];
                session('ERP_Wap0_User',$ERP_Wap0_User);
                //判断session中是否设置了要返回的地址
                if(session('ERP_Wap0_ReturnUrl')){
                    $patch = ['ERP_Wap0_ReturnUrl' => session('ERP_Wap0_ReturnUrl')];
                    session('ERP_Wap0_ReturnUrl',null);
                }else{
                    $patch = [];
                }
                $a = array_merge([
                    'ret' => C('succ_ret'),
                    'msg' => '登录成功',
                ],$patch);
                if(($this->config['UseBoardGroup'] || $this->config['UseBoxGroup']) && !$this->config['Open80Port'] && $this->config['UseWxPay']){
                    echo callback_jejuu($a,$get);
                }else{
                    echo jejuu($a);
                }
            }else{
                $a = [
                    'ret' => C('fail_ret'),
                    'msg' => '登录失败：无效账号或密码',
                ];
                if(($this->config['UseBoardGroup'] || $this->config['UseBoxGroup']) && !$this->config['Open80Port'] && $this->config['UseWxPay']){
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
            if(($this->config['UseBoardGroup'] || $this->config['UseBoxGroup']) && !$this->config['Open80Port'] && $this->config['UseWxPay']){
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
                    'UserType' => 0,
                ])
                ->find();
            //dump($user);die;
            if($user){
                $ERP_Wap0_User = ['UserName' => $user['UserName']];
                session('ERP_Wap0_User',$ERP_Wap0_User);
                //判断session中是否设置了要返回的地址
                if(session('ERP_Wap0_ReturnUrl')){
                    $patch = ['ERP_Wap0_ReturnUrl' => session('ERP_Wap0_ReturnUrl')];
                    session('ERP_Wap0_ReturnUrl',null);
                }else{
                    $patch = [];
                }
                $a = array_merge([
                    'ret' => C('succ_ret'),
                    'msg' => '自动登录成功',
                ],$patch);
                if(($this->config['UseBoardGroup'] || $this->config['UseBoxGroup']) && !$this->config['Open80Port'] && $this->config['UseWxPay']){
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
        if(($this->config['UseBoardGroup'] || $this->config['UseBoxGroup']) && !$this->config['Open80Port'] && $this->config['UseWxPay']){
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
