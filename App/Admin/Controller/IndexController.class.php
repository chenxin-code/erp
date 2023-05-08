<?php
namespace Admin\Controller;
class IndexController extends BaseController{

	//项目配置
    public function config(){
        $this->assign([
            'LayoutTitle' => '项目配置',
        ]);
        $this->display();
    }

    public function getConfig_api(){
        echo jejuu($this->config);
    }

    public function saveConfig_api(){
        $OriTableData = [];
        $NewTableData = [];
        $_OriTableData = M()->table('WebConfig')->select();
        foreach($_OriTableData as $k => $v){
            $OriTableData[$v['Name']] = $v['Value'];
        }
        $_NewTableData = array_merge($OriTableData,I('post.config'));
        foreach($_NewTableData as $k => $v){
            $NewTableData[] = [
                'Name' => $k,
                'Value' => $v,
            ];
        }
        M()->startTrans();
        $r1 = M()->table('WebConfig')->where('1 = 1')->delete();
        $r2 = M()->table('WebConfig')->addAll($NewTableData);
        if($r1 !== FALSE && $r2){
            M()->commit();
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '保存成功',
            ]);
        }else{
            M()->rollback();
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '保存失败',
            ]);
        }
    }

    //图片配置
    public function imgConfig(){
        $this->assign([
            'LayoutTitle' => '图片配置',
        ]);
        $this->display();
    }
    public function showFactoryLogo_api(){
        echo jejuu($this->config['FactoryLogo']);
    }
    public function setFactoryLogo_api(){
        $r = D('Func')->upload_img('pic');
        if($r['bool']){
            $r = D('Func')->aosConfig('FactoryLogo',$r['images']);
            if($r !== FALSE){
                //把之前的图片删除
                unlink('./res/'.$this->config['FactoryLogo']);
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '设置成功',
                ]);
            }else{
                //把已上传的图片删除
                unlink('./res/'.$r['images']);
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '设置失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r['error'],
            ]);
        }
    }
    public function addAdverPic_api(){
        $AdverPic = $this->config['AdverPic'];
        $r1 = D('Func')->upload_img('pic');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['error'],
            ]);die;
        }
        //如果原来就有图片，则修改数据时需要保存之前的图片路径
        if($AdverPic){
            $AdverPic = $AdverPic.','.$r1['images'];
        }else{
            $AdverPic = $r1['images'];
        }
        $r2 = D('Func')->aosConfig('AdverPic',$AdverPic);
        if($r2){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '添加成功',
            ]);
        }else{
            //把已上传的图片删除
            unlink('./res/'.$r1['images']);
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '添加失败',
            ]);
        }
    }
    public function delAdverPic_api(){
        $get = I('get.');
        $AdverPic = $this->config['AdverPic'];
        //通过下面3行代码去除Value字段中存有的图片
        $AdverPic = str_replace(','.$get['Pic'],'',$AdverPic);
        $AdverPic = str_replace($get['Pic'].',','',$AdverPic);
        $AdverPic = str_replace($get['Pic'],'',$AdverPic);
        $r = D('Func')->aosConfig('AdverPic',$AdverPic);
        if($r){
            unlink('./res/'.$get['Pic']);
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
    }
    public function showAdverPic_api(){
        $AdverPic = $this->config['AdverPic'];
        $AdverPic = $AdverPic?explode(',',$AdverPic):[];
        echo jejuu($AdverPic);
    }
    public function showBoardGroupPic_api(){
        echo jejuu($this->config['BoardGroupPic']);
    }
    public function setBoardGroupPic_api(){
        $r = D('Func')->upload_img('pic');
        if($r['bool']){
            $r = D('Func')->aosConfig('BoardGroupPic',$r['images']);
            if($r !== FALSE){
                //把之前的图片删除
                unlink('./res/'.$this->config['BoardGroupPic']);
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '设置成功',
                ]);
            }else{
                //把已上传的图片删除
                unlink('./res/'.$r['images']);
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '设置失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r['error'],
            ]);
        }
    }
    public function showFlagBoardGroupPic_api(){
        echo jejuu($this->config['FlagBoardGroupPic']);
    }
    public function setFlagBoardGroupPic_api(){
        $r = D('Func')->upload_img('pic');
        if($r['bool']){
            $r = D('Func')->aosConfig('FlagBoardGroupPic',$r['images']);
            if($r !== FALSE){
                //把之前的图片删除
                unlink('./res/'.$this->config['FlagBoardGroupPic']);
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '设置成功',
                ]);
            }else{
                //把已上传的图片删除
                unlink('./res/'.$r['images']);
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '设置失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r['error'],
            ]);
        }
    }
    public function showBoxGroupPic_api(){
        echo jejuu($this->config['BoxGroupPic']);
    }
    public function setBoxGroupPic_api(){
        $r = D('Func')->upload_img('pic');
        if($r['bool']){
            $r = D('Func')->aosConfig('BoxGroupPic',$r['images']);
            if($r !== FALSE){
                //把之前的图片删除
                unlink('./res/'.$this->config['BoxGroupPic']);
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '设置成功',
                ]);
            }else{
                //把已上传的图片删除
                unlink('./res/'.$r['images']);
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '设置失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r['error'],
            ]);
        }
    }
    public function showFlagBoxGroupPic_api(){
        echo jejuu($this->config['FlagBoxGroupPic']);
    }
    public function setFlagBoxGroupPic_api(){
        $r = D('Func')->upload_img('pic');
        if($r['bool']){
            $r = D('Func')->aosConfig('FlagBoxGroupPic',$r['images']);
            if($r !== FALSE){
                //把之前的图片删除
                unlink('./res/'.$this->config['FlagBoxGroupPic']);
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '设置成功',
                ]);
            }else{
                //把已上传的图片删除
                unlink('./res/'.$r['images']);
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '设置失败',
                ]);
            }
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r['error'],
            ]);
        }
    }

    //清除服务器缓存
    public function clean_api(){
        //echo RUNTIME_PATH;die;
        $r = D('Func')->del_dir(RUNTIME_PATH);
        //dump($r);die;
        //sleep(3);
        if($r){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '清除缓存成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '清除缓存失败',
            ]);
        }
    }

    //退出
    public function logout(){
        session('ERP_Admin_User',null);
        echo '<script>window.location.href = "'.U('Login/login').'";</script>';die;
    }

}
