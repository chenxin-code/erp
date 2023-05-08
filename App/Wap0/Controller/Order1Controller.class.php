<?php
namespace Wap0\Controller;
//东恒订单仿写
class Order1Controller extends BaseController{

    /********** 页面 **********/

    //每日订单
    public function GetOrdersP(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap0_User.rememberForm_'.$mca)?session('ERP_Wap0_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '每日订单',
            'rememberForm' => jejuu($rememberForm),
            'OrderStatus' => jejuu(C('OrderStatus')),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //日期选择
    public function GetOrderCount_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.'GetOrdersP_api'));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap0_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap0_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdersPMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdersPMaxDate'])));
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
        $strWhere .= ' AND CusId = \'\''.$this->ERPId.'\'\'';
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
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdersPMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdersPMaxDate'])));
        if(strtotime($get['OrderDate']) < $MinTime || strtotime($get['OrderDate']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单日期非法',
            ]);die;
        }
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $strWhere .= ' AND CusId = \'\''.$this->ERPId.'\'\'';
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
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdersPMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetOrdersPMaxDate'])));
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
        $strWhere = ' AND CusId = \'\''.$this->ERPId.'\'\'';
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

//    public function history(){
//        $today = date('Y-m-d');
//        $yesterday = date('Y-m-d',strtotime('-1 day'));
//        $the_day_before_yesterday = date('Y-m-d',strtotime('-2 day'));
//        $tomorrow = date('Y-m-d',strtotime('+1 day'));
//        $the_day_after_tomorrow = date('Y-m-d',strtotime('+2 day'));
//        if($BeginDate === $today){
//            $BeginDate = '今天';
//        }elseif($BeginDate === $yesterday){
//            $BeginDate = '昨天';
//        }elseif($BeginDate === $the_day_before_yesterday){
//            $BeginDate = '前天';
//        }elseif($BeginDate === $tomorrow){
//            $BeginDate = '明天';
//        }elseif($BeginDate === $the_day_after_tomorrow){
//            $BeginDate = '后天';
//        }
//        if($EndDate === $today){
//            $EndDate = '今天';
//        }elseif($EndDate === $yesterday){
//            $EndDate = '昨天';
//        }elseif($EndDate === $the_day_before_yesterday){
//            $EndDate = '前天';
//        }elseif($EndDate === $tomorrow){
//            $EndDate = '明天';
//        }elseif($EndDate === $the_day_after_tomorrow){
//            $EndDate = '后天';
//        }
//    }

}