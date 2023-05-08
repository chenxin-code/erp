<?php
namespace Admin\Controller;
class UserController extends BaseController{

    public function lists(){
        $this->assign([
            'LayoutTitle' => '用户列表',
        ]);
        $this->display();
    }

    public function lists_api(){
        $get = I('get.');
        $count = M()->table('WebAdminUser')->count();
        $model = M()->table('WebAdminUser');
        if($get['CurPage'] && $get['PageSize']){
            $MaxPage = ceil($count/$get['PageSize']);
            if($MaxPage < 1){$MaxPage = 1;}
            $data = $model->page($get['CurPage'],$get['PageSize'])->select();
        }else{
            $MaxPage = '当前不是分页取数据该值无意义';
            $data = $model->select();
        }
        echo jejuu([
            'count' => $count,
            'MaxPage' => $MaxPage,
            'data' => $data,
        ]);
    }

    public function add(){
        $this->assign([
            'LayoutTitle' => '添加用户',
        ]);
        $this->display();
    }

    public function add_api(){
        $post = I('post.');
        if(!$post['UserName']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交账号',
            ]);die;
        }
        if(!$post['PassWord']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '未提交密码',
            ]);die;
        }
        if(M()->table('WebAdminUser')->where(['UserName' => $post['UserName']])->find()){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '账号已存在',
            ]);die;
        }
        $r = M()->table('WebAdminUser')->add(['UserName' => $post['UserName'],'PassWord' => $post['PassWord']]);
        if($r){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '添加成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '添加失败',
            ]);
        }
    }

    public function editPwd(){
        $get = I('get.');
        $user = M()->table('WebAdminUser')->where(['UserName' => $get['UserName']])->find();
        if(!$user){
            $this->error('参数错误');die;
        }
        $this->assign([
            'LayoutTitle' => '修改密码',
            'user' => $user,
        ]);
        $this->display();
    }

    public function editPwd_api(){
        $post = I('post.');
        if(M()->table('WebAdminUser')->where(['UserName' => $post['UserName']])->find()){
            $r = M()->table('WebAdminUser')->where(['UserName' => $post['UserName']])->save(['PassWord' => $post['PassWord']]);
            if($r !== FALSE){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '修改密码成功',
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '修改密码失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);
        }
    }

    public function del_api(){
        $get = I('get.');
        if($get['UserName'] === session('ERP_Admin_User.UserName')){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '不能删除当前登录的用户',
            ]);die;
        }
        if(M()->table('WebAdminUser')->count() <= 1){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '至少保留一个用户',
            ]);die;
        }
        if(M()->table('WebAdminUser')->where(['UserName' => $get['UserName']])->find()){
            $r = M()->table('WebAdminUser')->where(['UserName' => $get['UserName']])->delete();
            if($r !== FALSE){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '删除成功',
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '删除失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);
        }
    }

}