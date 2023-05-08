<?php
namespace Wap0\Controller;
use Think\Controller;
class BaseController extends Controller{

    var $ERPId;
    var $config;
	
	public function __construct(){
		parent::__construct();
		if(!session('ERP_Wap0_User')){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                //把要访问的地址保存到session中，这样登录成功之后会跳回来
                session('ERP_Wap0_ReturnUrl','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                $this->redirect('Login/login');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '未登录']);
            }
            die;
		}

        $this->ERPId = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap0_User.UserName')])->getField('ERPId');
        $CusShortName = M()->table('Customer')->where(['CusId' => $this->ERPId])->getField('CusShortName');
        $this->config = D('Func')->getConfig();

        $this->assign([
            'HeaderFlag' => $CusShortName.'（'.$this->ERPId.'）',
            'ERPId' => $this->ERPId,
            'config' => $this->config,
        ]);

        $CA = CONTROLLER_NAME.'/'.ACTION_NAME;

        //Index控制器下的方法肯定能访问
        if(CONTROLLER_NAME === 'Index'){

        }else{
            if($this->config['Wap0Right']){
                $URName = [];//初始化
                foreach (C('Wap0URName') as $k => $v) {
                    if(in_array($CA,$v,true)){
                        $URName[] = $k;
                    }
                }
                //阻止tp语法报错
                if($URName === []){
                    $URName = '';
                }
                //用户权限判断
                //内部用户：有配置FactoryId用UserName,URName,FactoryId判断,没配置FactoryId用UserName,URName判断
                //外部用户：直接用UserName,URName判断
                $where = [
                    'UserName' => session('ERP_Wap0_User.UserName'),
                    'URName' => ['IN',$URName],
                ];
                if(!M()->table('WebUserRight')->where($where)->select()){
                    if(strpos(ACTION_NAME,'_api') === FALSE){
                        $this->error('没有权限');
                    }else{
                        echo jejuu(['ret' => C('fail_ret'),'msg' => '没有权限']);
                    }
                    die;
                }
            }
        }

        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain'] && !in_array($CA,C('Must80PortUrl'),true)){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U($CA.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }

	}

}