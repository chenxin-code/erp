<?php
namespace Wap1\Controller;
//东恒订单仿写
class Order1Controller extends BaseController{

    /********** 页面 **********/

    //客户每日订单
    public function WGetCusOrder(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '客户每日订单',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    //xx每日订单【隐藏界面】
    public function GetOrdersP(){
        $get = I('get.');
        if(!$get['CusId'] || !M()->table('Customer')->where(['CusId' => $get['CusId']])->find()){
            $this->error('参数错误');die;
        }
        $CusShortName = M()->table('Customer')->where(['CusId' => $get['CusId']])->getField('CusShortName');
        if($this->TaskId && !M()->table('Customer')->where(['CusId' => $get['CusId'],'TaskId' => $this->TaskId])->find()){
            $this->error('业务员不能查看不属于自己的客户的每日订单');die;
        }
        if(($get['BeginDate'] && !preg_match(C('xxxx-xx-xxDatePattern'),$get['BeginDate'])) || ($get['EndDate'] && !preg_match(C('xxxx-xx-xxDatePattern'),$get['EndDate']))){
            $this->error('参数错误');die;
        }
        $this->assign([
            'LayoutTitle' => $CusShortName.'（'.$get['CusId'].'）每日订单',
            'CusId' => $get['CusId'],
            'OrderStatus' => jejuu(C('OrderStatus')),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //客户每日订单
    public function WGetCusOrder_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if($get['form']['CusId']){
            $strWhere .= ' AND b.CusId = \'\''.$get['form']['CusId'].'\'\'';
        }
        $strWhere .= ' AND OrderDate >= \'\''.$get['form']['BeginDate'].'\'\' AND OrderDate <= \'\''.$get['form']['EndDate'].'\'\'';
        if($this->TaskId){
            $strWhere .= ' AND TaskId = \'\''.$this->TaskId.'\'\'';
        }
        //下单员
        if($get['form']['AddUserId'] === 'yes'){
            $strWhere .= ' AND AddUserId = \'\''.$this->ERPId.'\'\'';
        }
        try {
            $data = M()->query('exec WGetCusOrder \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach($data as $k => $v){
            $data[$k]['OrdAmt'] = round($v['OrdAmt'],0);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //员工信息
    public function WGetUserOrder_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND OrderDate >= \'\''.$get['BeginDate'].'\'\' AND OrderDate <= \'\''.$get['EndDate'].'\'\'';
        if($this->TaskId){
            $strWhere .= ' AND TaskId = \'\''.$this->TaskId.'\'\'';
        }
        //下单员
        if($get['AddUserId'] === 'yes'){
            $strWhere .= ' AND AddUserId = \'\''.$this->ERPId.'\'\'';
        }
        try {
            $data = M()->query('exec WGetUserOrder \''.$strWhere.'\',\'\'')[0];
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        if($data['OrdAmt']){
            $data['OrdAmt'] = round($data['OrdAmt'],2);
        }
        if($data['TLength']){
            $data['TLength'] = round($data['TLength'],2);
        }
        if($data['TSalesArea']){
            $data['TSalesArea'] = round($data['TSalesArea'],2);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //日期选择
    public function GetOrderCount_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMaxDate'])));
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
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND CusId = \'\''.$get['CusId'].'\'\'';
        $strWhere .= ' AND OrderDate >= \'\''.$get['form']['BeginDate'].'\'\' AND OrderDate <= \'\''.$get['form']['EndDate'].'\'\'';
        if($get['form']['OrderId']){
            $strWhere .= ' AND b.OrderId LIKE \'\'%'.$get['form']['OrderId'].'%\'\'';
        }
        if($get['form']['CusPoNo']){
            $strWhere .= ' AND CusPoNo LIKE \'\'%'.$get['form']['CusPoNo'].'%\'\'';
        }
        if($get['form']['Length']){
            $strWhere .= ' AND Length = '.$get['form']['Length'];
        }
        if($get['form']['Width']){
            $strWhere .= ' AND Width = '.$get['form']['Width'];
        }
        if($get['form']['ScoreInfo']){
            $strWhere .= ' AND ScoreInfo LIKE \'\'%'.$get['form']['ScoreInfo'].'%\'\'';
        }
        if($get['form']['OrdQty']){
            $strWhere .= ' AND OrdQty = '.$get['form']['OrdQty'];
        }
        try {
            $data = M()->query('exec GetOrderCount \'0\',\''.$get['form']['sstate'].'\',\''.$strWhere.'\'');
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //每日订单
    public function GetOrdersP_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMaxDate'])));
        if(strtotime($get['OrderDate']) < $MinTime || strtotime($get['OrderDate']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单日期非法',
            ]);die;
        }
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND CusId = \'\''.$get['CusId'].'\'\'';
        $strWhere .= ' AND OrderDate = \'\''.$get['OrderDate'].'\'\'';
        if($get['form']['OrderId']){
            $strWhere .= ' AND b.OrderId LIKE \'\'%'.$get['form']['OrderId'].'%\'\'';
        }
        if($get['form']['CusPoNo']){
            $strWhere .= ' AND CusPoNo LIKE \'\'%'.$get['form']['CusPoNo'].'%\'\'';
        }
        if($get['form']['Length']){
            $strWhere .= ' AND Length = '.$get['form']['Length'];
        }
        if($get['form']['Width']){
            $strWhere .= ' AND Width = '.$get['form']['Width'];
        }
        if($get['form']['ScoreInfo']){
            $strWhere .= ' AND ScoreInfo LIKE \'\'%'.$get['form']['ScoreInfo'].'%\'\'';
        }
        if($get['form']['OrdQty']){
            $strWhere .= ' AND OrdQty = '.$get['form']['OrdQty'];
        }
        if(in_array($get['form']['sstate'],C('OrderStatus'),true)){
            $strWhere2 = ' AND sstate = \'\''.$get['form']['sstate'].'\'\'';
        }else{
            $strWhere2 = '';
        }
        try {
            $data = M()->query('exec GetOrdersP \''.$strWhere.'\',\''.$strWhere2.'\'');
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach($data as $k => $v){
            if(in_array($v['sstate'],C('OrderSPStatus'),true)){
                $strWhere = ($this->config['FactoryId'] === '')?'':' AND d.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
                $strWhere .= ' AND d.OrderType = \'\''.$v['OrderType'].'\'\' AND d.OrderId = \'\''.$v['OrderId'].'\'\'';
                try {
                    $patch = M()->query('exec WGetOrdersPDN \''.$strWhere.'\'')[0];
                } catch(\Exception $e){
                    echo jejuu([
                        'ret' => C('fail_ret'),
                        'msg' => $e->getMessage(),
                    ]);die;
                }
                if($patch['TimeToGo']){
                    $patch['TimeToGo'] = date('Y-m-d H:i:s',strtotime($patch['TimeToGo']));
                }
                if($patch['InTime']){
                    $patch['InTime'] = date('Y-m-d H:i:s',strtotime($patch['InTime']));
                }
                $data[$k] = array_merge($v,[
                    'TimeToGo' => $patch['TimeToGo'],
                    'ConfQty' => $patch['ConfQty'],
                    'InTime' => $patch['InTime'],
                    'CarNo' => $patch['CarNo'],
                    'CarPName' => $patch['CarPName'],
                    'Phone' => $patch['Phone'],
                ]);
            }
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //客户信息
    public function GetCusDateInfo_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WGetCusOrderMaxDate'])));
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
        $strWhere = ' AND CusId = \'\''.$get['CusId'].'\'\'';
        $strWhere .= ' AND OrderDate >= \'\''.$get['BeginDate'].'\'\' AND OrderDate <= \'\''.$get['EndDate'].'\'\'';
        try {
            $data = M()->query('exec GetCusDateInfo \''.$strWhere.'\'')[0];
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        if($data['TOrdVol']){
            $data['TOrdVol'] = round($data['TOrdVol'],2);
        }
        if($data['TProVol']){
            $data['TProVol'] = round($data['TProVol'],2);
        }
        if($data['TStockVol']){
            $data['TStockVol'] = round($data['TStockVol'],2);
        }
        if($data['TUnDeliVol']){
            $data['TUnDeliVol'] = round($data['TUnDeliVol'],2);
        }
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