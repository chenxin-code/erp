<?php
namespace Wap0\Controller;
//信用
class CredController extends BaseController{

    /********** 页面 **********/

    public function WGetCusAmt(){
        $this->assign([
            'LayoutTitle' => '信用余额',
        ]);
        $this->display();
    }

    /********** 接口 **********/

    public function WGetCusAmt_api(){
        $QueryTime = date('Y-m-d H:i:s',time());
        try {
            $data = M()->query('exec WGetCusAmt \' AND c.CusId = \'\''.$this->ERPId.'\'\'\'')[0];
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

}