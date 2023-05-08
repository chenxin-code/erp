<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller{

    var $config;
	
	public function __construct(){
		parent::__construct();
		if(!session('ERP_Admin_User')){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                //把要访问的地址保存到session中，这样登录成功之后会跳回来
                session('ERP_Admin_ReturnUrl','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                $this->redirect('Login/login');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '未登录']);
            }
            die;
		}
        $this->config = D('Func')->getConfig();
        $this->assign('config',$this->config);
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(CONTROLLER_NAME.'/'.ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
	}

}