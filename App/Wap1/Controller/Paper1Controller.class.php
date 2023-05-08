<?php
namespace Wap1\Controller;
//东恒原纸仿写
class Paper1Controller extends BaseController{

    /********** 页面 **********/

    //原纸采购
    public function WGetPOMain(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '原纸采购',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    //原纸收货
    public function WGetPOIn(){
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND z.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND z.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        try {
            $POIn = M()->query('exec WGetPOIn \''.$strWhere.'\'');
        } catch (\Exception $e) {
            $this->error($e->getMessage());die;
        }
        foreach ($POIn as $k => $v) {
            $POIn[$k]['RecDate'] = date('Y-m-d',strtotime($v['RecDate']));
            $POIn[$k]['SumInWt'] = (int)$v['SumInWt'];
        }
        $this->assign([
            'LayoutTitle' => '原纸收货',
            'POInSelect' => jejuu($POIn),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //原纸采购
    public function WGetPOMain_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WGetPOMainMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WGetPOMainMaxDate'])));
        if(strtotime($get['form']['BeginDate']) < $MinTime || strtotime($get['form']['BeginDate']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '开始日期非法',
            ]);die;
        }
        if(strtotime($get['form']['EndDate']) < $MinTime || strtotime($get['form']['EndDate']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '结束日期非法',
            ]);die;
        }
        $PODateSelect = [];
        $POMain = [];
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND pm.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND pm.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        $strWhere .= ' AND pm.PODate >= \'\''.$get['form']['BeginDate'].'\'\' AND pm.PODate <= \'\''.$get['form']['EndDate'].'\'\'';
        try {
            $_POMain = M()->query('exec WGetPOMain \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach ($_POMain as $k => $v) {
            $PODate = date('Y-m-d',strtotime($v['PODate']));
            unset($v['PODate']);
            $PODateSelect[] = $PODate;
            $POMain[$PODate][] = $v;
        }
        $PODateSelect = array_values(array_unique($PODateSelect));
        $POMain = array_values($POMain);
        //dump($PODateSelect);dump($POMain);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'PODateSelect' => $PODateSelect,
            'POMain' => $POMain,
        ]);
    }

    //原纸采购明细
    public function WGetPODetail_api(){
        $get = I('get.');
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND p.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND p.PONo = '.$get['PONo'];
        try {
            $data = M()->query('exec WGetPODetail \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        $SumSumWeight = 0;
        $SumInQty = 0;
        $SumQty = 0;
        foreach ($data as $k => $v) {
            $data[$k]['PaperWidth'] = (int)$v['PaperWidth'];
            $data[$k]['PoPrice'] = sprintf('%.3f',$v['PoPrice']);
            $SumSumWeight += $v['SumWeight'];
            $SumInQty += $v['InQty'];
            $SumQty += $v['Qty'];
        }
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
            'SumSumWeight' => $SumSumWeight,
            'SumInQty' => $SumInQty,
            'SumQty' => $SumQty,
        ]);
    }

    //原纸收货
    public function WGetPOIn2_api(){
        $get = I('get.');
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND z.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND z.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        $strWhere .= ' AND z.RecDate = \'\''.$get['RecDate'].'\'\'';
        try {
            $data = M()->query('exec WGetPOIn2 \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach ($data as $k => $v) {
            $data[$k]['RecDate'] = date('Y-m-d',strtotime($v['RecDate']));
            $data[$k]['SumInWt'] = (int)$v['SumInWt'];
        }
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //原纸收货明细
    public function WGetPOInDetail_api(){
        $get = I('get.');
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND z.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND z.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        $strWhere .= ' AND z.InNo = '.$get['InNo'];
        try {
            $data = M()->query('exec WGetPOInDetail \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach ($data as $k => $v) {
            $data[$k]['PaperWidth'] = (int)$v['PaperWidth'];
            $data[$k]['InWt'] = (int)$v['InWt'];
            $data[$k]['dPrice'] = sprintf('%.3f',$v['dPrice']);
        }
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

}