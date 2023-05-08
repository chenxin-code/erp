<?php
namespace Wap0\Controller;
//报价查询
class QuoController extends BaseController{

    /********** 页面 **********/

    //报价价格
    public function GetQuoPriceByCus(){
        $this->assign([
            'LayoutTitle' => '报价价格',
        ]);
        $this->display();
    }

    //报价规则
    public function GetQuoRuleByCus(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap0_User.rememberTab_'.$mca);
        $this->assign([
            'LayoutTitle' => '报价规则',
            'rememberTab' => $rememberTab,
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //报价价格
    public function GetQuoPriceByCus_api(){
        $QuotaId = M()->table('QuotaBdComT')->where(['FactoryId' => $this->config['FactoryId'],'Effctive' => 1])->getField('QuotaId');
        $data = M()->query('exec GetQuoPriceByCus \''.$this->config['FactoryId'].'\',\''.$QuotaId.'\',\''.$this->ERPId.'\'');
        foreach ($data as $k => $v) {
            $data[$k]['UnitPrice'] = sprintf('%.2f',$v['UnitPrice']);
            //$data[$k]['BoardName'] = str_replace('的名字','name',$v['BoardName']);
        }
        //dump($data);die;
        echo jejuu($data);
    }

    //报价规则
    public function GetQuoRuleByCus_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap0_User.rememberTab_'.$mca,$get['IsCom']);
        $QuotaId = M()->table('QuotaBdComT')->where(['FactoryId' => $this->config['FactoryId'],'Effctive' => 1])->getField('QuotaId');
        $data = M()->query('exec GetQuoRuleByCus \''.$this->config['FactoryId'].'\',\''.$QuotaId.'\',\''.$this->ERPId.'\','.$get['IsCom']);
        foreach ($data as $k => $v) {
            $data[$k]['ValueBegin'] = (int)$v['ValueBegin'];
            $data[$k]['ValueEnd'] = (int)$v['ValueEnd'];
            $data[$k]['Value'] = (float)$v['Value'];
            //折扣方式
            if($v['DisType'] === '0'){
                $data[$k]['DisType'] = '数量折';
            }elseif($v['DisType'] === '1'){
                $data[$k]['DisType'] = '面积折';
            }
            //增减类型
            if($v['ValueType'] === '0'){
                $data[$k]['ValueType'] = '固定价格';
            }elseif($v['ValueType'] === '1'){
                $data[$k]['ValueType'] = '价格百分比';
            }elseif($v['ValueType'] === '2'){
                $data[$k]['ValueType'] = '固定折扣';
            }
            //纸板类型
            if($v['ScoreType'] === '0'){
                $data[$k]['ScoreType'] = '全部';
            }elseif($v['ScoreType'] === '1'){
                $data[$k]['ScoreType'] = '有压线';
            }elseif($v['ScoreType'] === '2'){
                $data[$k]['ScoreType'] = '毛片';
            }
        }
        //dump($data);die;
        echo jejuu($data);
    }

}