<?php
namespace Admin\Controller;
class ContactController extends BaseController{

    public function lists(){
        $this->assign([
            'LayoutTitle' => '联系方式列表',
        ]);
        $this->display();
    }

    public function lists_api(){
        $get = I('get.');
        $count = M()->table('WebContact')->count();
        $model = M()->table('WebContact');
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
            'LayoutTitle' => '添加联系方式',
            'ContactIcon' => jejuu(C('ContactIcon')),
        ]);
        $this->display();
    }

    public function add_api(){
        $post = I('post.');
        $r = M()->table('WebContact')->add(['Icon' => $post['Icon'],'Name' => $post['Name'],'Content' => $post['Content']]);
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

    public function edit(){
        $get = I('get.');
        $data = M()->table('WebContact')->where(['Id' => $get['Id']])->find();
        if(!$data){
            $this->error('参数错误');die;
        }
        $this->assign([
            'LayoutTitle' => '修改联系方式',
            'ContactIcon' => jejuu(C('ContactIcon')),
            'data' => $data,
        ]);
        $this->display();
    }

    public function edit_api(){
        $post = I('post.');
        if(M()->table('WebContact')->where(['Id' => $post['Id']])->find()){
            $r = M()->table('WebContact')->where(['Id' => $post['Id']])->save(['Icon' => $post['Icon'],'Name' => $post['Name'],'Content' => $post['Content']]);
            if($r !== FALSE){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '修改成功',
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '修改失败',
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
        if(M()->table('WebContact')->where(['Id' => $get['Id']])->find()){
            $r = M()->table('WebContact')->where(['Id' => $get['Id']])->delete();
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
