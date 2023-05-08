<?php
namespace Wap1\Controller;
use Think\Controller;
class BaseController extends Controller{

    var $ERPId;
    var $TaskId;
    var $config;
	
	public function __construct(){
		parent::__construct();
		if(!session('ERP_Wap1_User')){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                //把要访问的地址保存到session中，这样登录成功之后会跳回来
                session('ERP_Wap1_ReturnUrl','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                $this->redirect('Login/login');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '未登录']);
            }
            die;
		}

        $wu = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap1_User.UserName')])->field('ERPId,TaskId')->find();
        $this->ERPId = $wu['ERPId'];
        $this->TaskId = $wu['TaskId'];
        $UserName = M()->table('UserTable')->where(['UserId' => $this->ERPId])->getField('UserName');
        $this->config = D('Func')->getConfig();

        if(session('ERP_Wap1_User.SubFacId')){
            $SubFacName = M()->table('SubFactory')->where(['FactoryId' => $this->config['FactoryId'],'SubFacId' => session('ERP_Wap1_User.SubFacId')])->getField('SShortName');
        }else{
            $SubFacName = '';
        }

        if(!$this->config['Open80Port'] && $this->config['UseScan']){
            $Domain = $this->config['OriDomain'];
        }else{
            $Domain = $_SERVER['SERVER_NAME'];
        }
        $ErpApiPrefix = 'http://'.$Domain.':'.$this->config['ErpPort'].'/datasnap/rest/TServerMethods1';

        $this->assign([
            'HeaderFlag' => $UserName.'（'.$this->ERPId.'）',
            'ERPId' => $this->ERPId,
            'TaskId' => $this->TaskId,
            'config' => $this->config,
            'SubFacName' => $SubFacName,
            'ErpApiPrefix' => $ErpApiPrefix,
        ]);

        $CA = CONTROLLER_NAME.'/'.ACTION_NAME;
        //Index控制器下的方法肯定能访问
        if(CONTROLLER_NAME === 'Index'){

        }else{
            if($this->config['Wap1Right']){
                $URName = [];//初始化
                foreach (C('Wap1URName') as $k => $v) {
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
                    'UserName' => session('ERP_Wap1_User.UserName'),
                    'URName' => ['IN',$URName],
                ];
                if($this->config['FactoryId'] !== ''){
                    $where['FactoryId'] = $this->config['FactoryId'];
                }
                if(!M()->table('WebUserRight')->where($where)->select()){
                    if(strpos(ACTION_NAME,'_api') === FALSE){
                        $this->error('没有权限');
                    }else{
                        echo jejuu(['ret' => C('fail_ret'),'msg' => '没有权限']);
                    }
                    die;
                }
            }
            if($this->config['FactoryId'] === '' && in_array($CA,C('MustFactoryIdUrl'),true)){
                $this->error('该页面必须配置厂商Id才能访问');die;
            }
            //跳转到80端口访问
            if(!$this->config['Open80Port'] && $this->config['UseScan'] && $_SERVER['SERVER_NAME'] !== $this->config['Frp80PortDomain'] && in_array($CA,C('Must80PortUrl'),true)){
                $this->redirect($CA.'@'.$this->config['Frp80PortDomain'],$_GET);die;
            }
        }

        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $this->config['UseScan'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain'] && !in_array($CA,C('Must80PortUrl'),true)){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U($CA.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }

	}

}