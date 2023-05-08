<?php
namespace Wap1\Controller;
//统计
class StatisController extends BaseController{

    /********** 页面 **********/

    //统计下的ERP订单【隐藏界面】
    public function GetOrders(){
        $get = I('get.');
        $this->assign([
            'LayoutTitle' => '统计下的ERP订单',
            'SType' => $get['SType'],
            'strWhere2_k' => $get['strWhere2_k'],
            'strWhere2_v' => $get['strWhere2_v'],
            'DateType' => $get['DateType'],
            'BeginDate' => $get['BeginDate'],
            'EndDate' => $get['EndDate'],
            'RemainDay' => $get['RemainDay'],
            'DiffDay' => $get['DiffDay'],
        ]);
        $this->display();
    }

    //订单统计
    public function GetOrderSum(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '订单统计',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }
    public function OnlyShowCharts_GetOrderSum(){
        $this->display();
    }

    //退货统计
    public function GetOrdReturnSum(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '退货统计',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }
    public function OnlyShowCharts_GetOrdReturnSum(){
        $this->display();
    }

    //传单统计
    public function GetSchSum(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '传单统计',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }
    public function OnlyShowCharts_GetSchSum(){
        $this->display();
    }

    //库存统计
    public function GetOrdStock(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '库存统计',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }
    public function OnlyShowCharts_GetOrdStock(){
        $this->display();
    }

    //生产分析总计
    public function GetProInfo(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '生产分析总计',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }
    public function OnlyShowCharts_GetProInfo(){
        $this->display();
    }


    /********** 接口 **********/

    //统计下的ERP订单【隐藏界面】
    public function GetOrders_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['StatisGetOrdersMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['StatisGetOrdersMaxDate'])));
        if(strtotime($get['BeginDate']) < $MinTime || strtotime($get['BeginDate']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '开始日期非法',
            ]);die;
        }
        if(strtotime($get['EndDate']) < $MinTime || strtotime($get['EndDate']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '结束日期非法',
            ]);die;
        }
        if($this->TaskId){
            if($get['strWhere2_k'] === 'TaskId' && $get['strWhere2_v'] !== $this->TaskId){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '业务员不能查看其他业务员的ERP订单',
                ]);die;
            }
            if($get['strWhere2_k'] === 'CusId' && !M()->table('Customer')->where(['CusId' => $get['strWhere2_v'],'TaskId' => $this->TaskId])->find()){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '业务员不能查看不属于自己的客户的ERP订单',
                ]);die;
            }
        }
        //构建 $strWhere1
        $strWhere1 = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if($get['DateType'] === 'OrderDate'){
            $strWhere1 .= ' AND OrderDate >= \'\''.$get['BeginDate'].'\'\' AND OrderDate <= \'\''.$get['EndDate'].'\'\'';
        }else if($get['DateType'] === 'DeliveryDate'){
            $strWhere1 .= ' AND DeliveryDate >= \'\''.$get['BeginDate'].'\'\' AND DeliveryDate <= \'\''.$get['EndDate'].'\'\'';
        }else if($get['DateType'] === 'ReturnDate'){
            $strWhere1 .= ' AND ReturnDate >= \'\''.$get['BeginDate'].'\'\' AND ReturnDate <= \'\''.$get['EndDate'].'\'\'';
        }else if($get['DateType'] === 'IssueDate'){
            $strWhere1 .= ' AND rd.IssueDate >= \'\''.$get['BeginDate'].'\'\' AND rd.IssueDate <= \'\''.$get['EndDate'].'\'\'';
        }else{
            $strWhere1 .= '';
        }
        if($this->TaskId){
            $strWhere1 .= ' AND c.TaskId = \'\''.$this->TaskId.'\'\'';
        }
        //构建 $strWhere2
        if($get['strWhere2_k'] && $get['strWhere2_v']){
            $strWhere2 = ' AND '.$get['strWhere2_k'].' = \'\''.$get['strWhere2_v'].'\'\'';
        }else{
            $strWhere2 = '';
        }
        if($get['SType'] === 'GetOrdStock'){
            if($get['RemainDay']){
                $strWhere2 .= ' AND RemainDay > '.$get['RemainDay'];
            }
            if($get['DiffDay']){
                $strWhere2 .= ' AND DiffDay > '.$get['DiffDay'];
            }
        }
        $State = '';//强制限制条件（全部）
        $CurPage = $get['CurPage']?$get['CurPage']:1;
        $PageSize = $get['PageSize']?$get['PageSize']:6;
        try {
            //$data = D('Procedure')->GetOrders($strWhere1,$State,$strWhere2,$get['SType'],$CurPage,$PageSize);
            $data = M()->query('exec GetOrders \''.$strWhere1.'\',\''.$State.'\',\''.$strWhere2.'\',\''.$get['SType'].'\',\''.$CurPage.'\',\''.$PageSize.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //订单统计
    public function GetOrderSum_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrderSumMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrderSumMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND '.$get['form']['DateType'].' >= \'\''.$get['form']['BeginDate'].'\'\' AND '.$get['form']['DateType'].' <= \'\''.$get['form']['EndDate'].'\'\'';
        if($this->TaskId){
            $strWhere .= ' AND c.TaskId = \'\''.$this->TaskId.'\'\'';
        }
        try {
            //$data = D('Procedure')->GetOrderSum($strWhere,$get['State']);
            $data = M()->query('exec GetOrderSum \''.$strWhere.'\',\''.$get['State'].'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach($data as $k => $v){
            $data[$k]['sumArea'] = round($v['sumArea'],0);
            $data[$k]['sumOrdArea'] = round($v['sumOrdArea'],0);
            $data[$k]['sumOrdVol'] = round($v['sumOrdVol'],0);
            $data[$k]['sumLength'] = round($v['sumLength'],0);
            $data[$k]['sumAmt'] = sprintf('%.2f',$v['sumAmt']);
            $data[$k]['sumArea1'] = round($v['sumArea1'],0);
            $data[$k]['sumLength1'] = round($v['sumLength1'],0);
            $data[$k]['sumAmt1'] = sprintf('%.2f',$v['sumAmt1']);
            $data[$k]['sumArea2'] = round($v['sumArea2'],0);
            $data[$k]['sumLength2'] = round($v['sumLength2'],0);
            $data[$k]['sumAmt2'] = sprintf('%.2f',$v['sumAmt2']);
            $data[$k]['sumArea3'] = round($v['sumArea3'],0);
            $data[$k]['sumLength3'] = round($v['sumLength3'],0);
            $data[$k]['sumAmt3'] = sprintf('%.2f',$v['sumAmt3']);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //退货统计
    public function GetOrdReturnSum_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdReturnSumMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdReturnSumMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND rd.'.$get['form']['DateType'].' >= \'\''.$get['form']['BeginDate'].'\'\' AND rd.'.$get['form']['DateType'].' <= \'\''.$get['form']['EndDate'].'\'\'';
        if($this->TaskId){
            $strWhere .= ' AND TaskId = \'\''.$this->TaskId.'\'\'';
        }
        try {
            //$data = D('Procedure')->GetOrdReturnSum($strWhere,$get['State']);
            $data = M()->query('exec GetOrdReturnSum \''.$strWhere.'\',\''.$get['State'].'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach($data as $k => $v){
            $data[$k]['ReturnFee'] = sprintf('%.2f',$v['ReturnFee']);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //传单统计
    public function GetSchSum_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetSchSumMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetSchSumMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND '.$get['form']['DateType'].' >= \'\''.$get['form']['BeginDate'].'\'\' AND '.$get['form']['DateType'].' <= \'\''.$get['form']['EndDate'].'\'\'';
        if($get['form']['SState']){$strWhere .= ' AND SState = '.$get['form']['SState'];}
        try {
            //$data = D('Procedure')->GetSchSum($strWhere,$get['State']);
            $data = M()->query('exec GetSchSum \''.$strWhere.'\',\''.$get['State'].'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //库存统计
    public function GetOrdStock_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdStockMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdStockMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND '.$get['form']['DateType'].' >= \'\''.$get['form']['BeginDate'].'\'\' AND '.$get['form']['DateType'].' <= \'\''.$get['form']['EndDate'].'\'\'';
        if($this->TaskId){
            $strWhere .= ' AND TaskId = \'\''.$this->TaskId.'\'\'';
        }
        try {
            //$data = D('Procedure')->GetOrdStock($strWhere,$get['State'],$get['form']['iRemainDay'],$get['form']['iDiffDDay']);
            $data = M()->query('exec GetOrdStock \''.$strWhere.'\',\''.$get['State'].'\',\''.$get['form']['iRemainDay'].'\',\''.$get['form']['iDiffDDay'].'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach($data as $k => $v){
            $data[$k]['StockAmt'] = sprintf('%.2f',$v['StockAmt']);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //生产分析总计
    public function GetProInfo_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetProInfoMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetProInfoMaxDate'])));
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
        try {
            //$data = D('Procedure')->GetProInfo($get['form']['BeginDate'],$get['form']['EndDate'],$this->config['FactoryId']);
            $data = M()->query('exec GetProInfo \''.$get['form']['BeginDate'].'\',\''.$get['form']['EndDate'].'\',\''.$this->config['FactoryId'].'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach($data as $k => $v){
            $data[$k]['sumOrdArea'] = round($v['sumOrdArea'],0);
            $data[$k]['sumAmt'] = sprintf('%.2f',$v['sumAmt']);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

}