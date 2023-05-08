<?php
namespace Wap1\Controller;
class UserController extends BaseController{

    /********** 页面 **********/

    public function manage(){
        $this->assign([
            'LayoutTitle' => '用户管理',
            'Type' => $this->TaskId?0:1,
        ]);
        $this->display();
    }


    /********** 接口 **********/

    public function getWebUser0_api(){
        if($this->TaskId){
            $data = M()->table('WebappUser')->alias('wu')
                ->join('LEFT OUTER JOIN Customer c ON wu.ERPId = c.CusId')
                ->where(['wu.UserType' => 0,'c.TaskId' => $this->TaskId])
                ->field('wu.UserName,c.CusId,c.CusShortName')
                ->select();
            foreach($data as $k => $v){
                unset($data[$k]['ROW_NUMBER']);
            }
            echo jejuu([
                'ret' => C('succ_ret'),
                'data' => $data,
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非业务员不能管理外部用户',
            ]);
        }
    }

    public function getWebUser1_api(){
        if($this->TaskId){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '业务员不能管理内部用户',
            ]);
        }else{
            $data = M()->table('WebappUser')->alias('wu')
                ->join('LEFT OUTER JOIN UserTable ut ON wu.ERPId = ut.UserId')
                ->where(['wu.UserType' => 1])
                ->field('wu.UserName,ut.UserId,ut.UserName AS UserName_,wu.TaskId')
                ->select();
            foreach($data as $k => $v){
                unset($data[$k]['ROW_NUMBER']);
            }
            echo jejuu([
                'ret' => C('succ_ret'),
                'data' => $data,
            ]);
        }
    }

    public function getQrcode_api(){
        $get = I('get.');
        $wu = M()->table('WebappUser')->where(['UserName' => $get['UserName']])->find();
        if($wu){
            echo jejuu([
                'ret' => C('succ_ret'),
                'url' => U('Wap'.$wu['UserType'].'/Login/login','',true,true).'?UserName='.$get['UserName'].'&secret='.md5($get['UserName'].C('md5_salt')),
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);
        }

    }

    public function getURName_api(){
        $get = I('get.');
        $UserType = M()->table('WebappUser')->where(['UserName' => $get['UserName']])->getField('UserType');
        $URNameSelect = [];
        foreach(C('Wap'.$UserType.'URName') as $k => $v){
            $URNameSelect[] = $k;
        }
        //dump($URNameSelect);die;
        $where = ['UserName' => $get['UserName']];
        if($UserType === '0'){
            $where['FactoryId'] = '0';
        }elseif($UserType === '1'){
            if($this->config['FactoryId'] === ''){
                $where['FactoryId'] = '0';
            }else{
                $where['FactoryId'] = $this->config['FactoryId'];
            }
        }
        $_URName = M()->table('WebUserRight')->where($where)->field('URName')->select();
        //二维转一维
        $URName = [];
        foreach($_URName as $v){
            $URName[] = $v['URName'];
        }
        //过滤WebUserRight表中的非法URName
        foreach($URName as $k => $v){
            if(is_null(C('Wap'.$UserType.'URName')[$v])){
                unset($URName[$k]);
            }
        }
        $URName = array_values($URName);
        //dump($URName);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'URNameSelect' => $URNameSelect,
            'URName' => $URName,
        ]);
    }

    public function saveURName_api(){
        $get = I('get.');
        $wu = M()->table('WebappUser')->where(['UserName' => $get['UserName']])->find();
        if($wu){
            if($wu['UserType'] === '0'){
                //管理外部用户
                if($this->TaskId){
                    //业务员在管理
                    if(!M()->table('Customer')->where(['CusId' => $wu['ERPId'],'TaskId' => $this->TaskId])->find()){
                        //外部用户不属于业务员
                        echo jejuu([
                            'ret' => C('fail_ret'),
                            'msg' => '业务员不能管理不属于自己的外部用户',
                        ]);die;
                    }
                    $FactoryId = '0';
                    $C = C('Wap0URName');
                }else{
                    //非业务员在管理
                    echo jejuu([
                        'ret' => C('fail_ret'),
                        'msg' => '非业务员不能管理外部用户',
                    ]);die;
                }
            }elseif($wu['UserType'] === '1'){
                //管理内部用户
                if($this->TaskId){
                    //业务员在管理
                    echo jejuu([
                        'ret' => C('fail_ret'),
                        'msg' => '业务员不能管理内部用户',
                    ]);die;
                }
                if($this->config['FactoryId'] === ''){
                    $FactoryId = '0';
                }else{
                    $FactoryId = $this->config['FactoryId'];
                }
                $C = C('Wap1URName');
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '异常错误',
                ]);die;
            }
            $where = ['UserName' => $get['UserName'],'FactoryId' => $FactoryId];
            $data = [];
            foreach($get['URName'] as $v){
                if(is_null($C[$v])){
                    echo jejuu([
                        'ret' => C('fail_ret'),
                        'msg' => '参数错误',
                    ]);die;
                }
                $data[] = ['UserName' => $get['UserName'],'FactoryId' => $FactoryId,'URName' => $v,'Authed' => 1];
            }
            M()->startTrans();
            $r1 = M()->table('WebUserRight')->where($where)->delete();
            $r2 = $data?M()->table('WebUserRight')->addAll($data):TRUE;
            if($r1 !== FALSE && $r2 !== FALSE){
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
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);
        }
    }

}