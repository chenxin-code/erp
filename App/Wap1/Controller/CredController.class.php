<?php
namespace Wap1\Controller;
//信用
class CredController extends BaseController{

    /********** 页面 **********/

    //客户信用余额
    public function WGetCusAmt(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '客户信用余额',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    //xx信用余额【隐藏界面】
    public function WGetCusAmt_(){
        $get = I('get.');
        if(!$get['CusId'] || !M()->table('Customer')->where(['CusId' => $get['CusId']])->find()){
            $this->error('参数错误');die;
        }
        $CusShortName = M()->table('Customer')->where(['CusId' => $get['CusId']])->getField('CusShortName');
        if($this->TaskId && !M()->table('Customer')->where(['CusId' => $get['CusId'],'TaskId' => $this->TaskId])->find()){
            $this->error('业务员不能查看不属于自己的客户的信用余额');die;
        }
        $this->assign([
            'LayoutTitle' => $CusShortName.'（'.$get['CusId'].'）信用余额',
            'CusId' => $get['CusId'],
        ]);
        $this->display();
    }

    /********** 接口 **********/

    public function WGetCusAmt_api(){
        $get = I('get.');
        $QueryTime = date('Y-m-d H:i:s',time());
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $strWhere = '';
        if($get['form']['CusId']){
            $strWhere .= ' AND c.CusId = \'\''.$get['form']['CusId'].'\'\'';
        }
        if($get['form']['Stopped'] === '1'){
            $strWhere .= ' AND Stopped = 1';
        }else if($get['form']['Stopped'] === '0'){
            $strWhere .= ' AND Stopped = 0';
        }
        if($get['form']['SettleDay'] === '1'){
            $strWhere .= ' AND SettleDay != 0';
        }else if($get['form']['SettleDay'] === '0'){
            $strWhere .= ' AND SettleDay = 0';
        }
        if($this->TaskId){
            $strWhere .= ' AND c.TaskId = \'\''.$this->TaskId.'\'\'';
        }
        try {
            $data = M()->query('exec WGetCusAmt \''.$strWhere.'\'');
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        $RealAmt = 0;
        foreach ($data as $k => $v) {
            $RealAmt += sprintf('%.2f',$v['RealAmt']);
            if($v['LeftMinAmtCond']){
                $data[$k]['LeftMinAmtCond'] = round($v['LeftMinAmtCond'],0);
            }
            if($v['MinAmtCond']){
                $data[$k]['MinAmtCond'] = round($v['MinAmtCond'],0);
            }
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
            'RealAmt' => $RealAmt,
            'QueryTime' => $QueryTime,
        ]);
    }

    public function WGetCusAmt__api(){
        $get = I('get.');
        $QueryTime = date('Y-m-d H:i:s',time());
        if(!$get['CusId'] || !M()->table('Customer')->where(['CusId' => $get['CusId']])->find()){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($this->TaskId && !M()->table('Customer')->where(['CusId' => $get['CusId'],'TaskId' => $this->TaskId])->find()){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '业务员不能查看不属于自己的客户的信用余额',
            ]);die;
        }
        try {
            $data = M()->query('exec WGetCusAmt \' AND c.CusId = \'\''.$get['CusId'].'\'\'\'')[0];
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        if($data['OrdNeedAmt']){
            $data['OrdNeedAmt'] = sprintf('%.2f',$data['OrdNeedAmt']);
        }
        if($data['CurAmt']){
            $data['CurAmt'] = sprintf('%.2f',$data['CurAmt']);
        }
        if($data['LeftMinAmtCond']){
            $data['LeftMinAmtCond'] = sprintf('%.2f',$data['LeftMinAmtCond']);
        }
        if($data['MinAmtCond']){
            $data['MinAmtCond'] = sprintf('%.2f',$data['MinAmtCond']);
        }
        if($data['RealAmt']){
            $data['RealAmt'] = sprintf('%.2f',$data['RealAmt']);
        }
        $data['Cus'] = $data['CusShortName'].'（'.$data['CusId'].'）';
        $data['Task'] = $data['TaskName'].'（'.$data['TaskId'].'）';
        $data['QueryTime'] = $QueryTime;
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //客户Picker
    public function CusPicker_api(){
        $get = I('get.');
        $data = D('Cus')->getCusPicker($this->TaskId,$get['CusKeyword']);
        echo jejuu($data);
    }

}