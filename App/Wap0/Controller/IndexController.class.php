<?php
namespace Wap0\Controller;
class IndexController extends BaseController{

    //菜单
    public function menu(){
        //查出所有的权限用于菜单过滤显示
        $_URName = M()->table('WebUserRight')->where(['UserName' => session('ERP_Wap0_User.UserName')])->field('URName')->select();
        //二维转一维
        $URName = [];
        foreach($_URName as $v){
            $URName[] = $v['URName'];
        }
        //过滤WebUserRight表中的非法URName
        foreach($URName as $k => $v){
            if(is_null(C('Wap0URName')[$v])){
                unset($URName[$k]);
            }
        }
        $URName = implode(',',$URName);
        $URName = ','.$URName.',';
        $this->assign([
            'LayoutTitle' => '菜单',
            'URName' => $URName,
        ]);
        $this->display();
    }

    //更改密码
    public function change_pwd(){
        $this->assign([
            'LayoutTitle' => '更改密码',
        ]);
        $this->display();
    }

    //更改密码接口
    public function change_pwd_api(){
        $post = I('post.');
        if(!$post['OriPwd']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交原密码',
            ]);die;
        }
        if(!$post['NewPwd']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交新密码',
            ]);die;
        }
        $user = M()->table('WebappUser')
            ->where([
                'UserName' => session('ERP_Wap0_User.UserName'),
                'PassWord' => $post['OriPwd'],
                'UserType' => 0,
            ])
            ->find();
        if($user){
            $r = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap0_User.UserName')])->save(['PassWord' => $post['NewPwd']]);
            if($r !== FALSE){
                session('ERP_Wap0_User',null);
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '更改密码成功',
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '更改密码失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '原密码错误',
            ]);
        }
    }

    //退出接口
    public function logout_api(){
        session('ERP_Wap0_User',null);
        $a = [
            'msg' => '已退出',
            'redirect_url' => U('Login/login'),
        ];
        if(($this->config['UseBoardGroup'] || $this->config['UseBoxGroup']) && !$this->config['Open80Port'] && $this->config['UseWxPay']){
            echo callback_jejuu($a,I('get.'));
        }else{
            echo jejuu($a);
        }
    }

}
