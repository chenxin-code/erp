<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{

    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        $this->assign('config',$this->config);
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(CONTROLLER_NAME.'/'.ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
    }

	//后台登录
	public function login(){
        //已登录则自动跳转
        if(session('ERP_Admin_User')){
            $this->redirect('Index/config');die;
        }
        $this->assign([
            'LayoutTitle' => '后台登录',
        ]);
		$this->display();
	}

    //后台登录接口
    public function login_api(){
        $get = I('get.');
        if(D('Func')->check_code($get['code'])){
            $user = M()->table('WebAdminUser')->where(['UserName' => $get['UserName'],'PassWord' => $get['PassWord']])->find();
            if($user){
                $ERP_Admin_User = ['UserName' => $user['UserName']];
                session('ERP_Admin_User',$ERP_Admin_User);
                //判断session中是否设置了要返回的地址
                if(session('ERP_Admin_ReturnUrl')){
                    $patch = ['ERP_Admin_ReturnUrl' => session('ERP_Admin_ReturnUrl')];
                    session('ERP_Admin_ReturnUrl',null);
                }else{
                    $patch = [];
                }
                echo jejuu(array_merge([
                    'ret' => C('succ_ret'),
                    'msg' => '登录成功',
                ],$patch));
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '登录失败：无效账号或密码',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '登录失败：无效验证码',
            ]);
        }
    }

    //生成验证码
    public function make_code(){
        ob_clean();
        $verify = new \Think\Verify([
            'fontSize'    =>    25,
            'length'      =>    4,
            'codeSet'      =>   '0123456789',
            'useNoise'    =>    false,
            'useCurve'   =>     false,
            'useImgBg'   =>     true,
        ]);
        $verify->entry();
    }

}
