<?php
namespace Wap1\Controller;
class OrderController extends BaseController{

    /********** 页面 **********/

    //ERP订单
    public function GetOrders(){
        //$data = D('Odbc')->query('SELECT CusId,CusShortName AS CusName FROM Customer','fetchAll');
        //file_put_contents('./test.json',jejuu($data));die;
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap1_User.rememberTab_'.$mca);
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => 'ERP订单',
            'rememberTab' => $rememberTab,
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //ERP订单
    public function GetOrders_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap1_User.rememberTab_'.$mca,$get['State']);
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['Wap1GetOrdersMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['Wap1GetOrdersMaxDate'])));
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
        //构建 $strWhere1
        $strWhere1 = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if($get['form']['DateType'] === 'OrderDate'){
            $strWhere1 .= ' AND OrderDate >= \'\''.$get['form']['BeginDate'].'\'\' AND OrderDate <= \'\''.$get['form']['EndDate'].'\'\'';
        }else if($get['form']['DateType'] === 'DeliveryDate'){
            $strWhere1 .= ' AND DeliveryDate >= \'\''.$get['form']['BeginDate'].'\'\' AND DeliveryDate <= \'\''.$get['form']['EndDate'].'\'\'';
        }else{
            $strWhere1 .= '';
        }
        if($get['form']['Length']){
            $strWhere1 .= ' AND Length = '.$get['form']['Length'];
        }
        if($get['form']['Width']){
            $strWhere1 .= ' AND Width = '.$get['form']['Width'];
        }
        if($get['form']['BoxL']){
            $strWhere1 .= ' AND BoxL = '.$get['form']['BoxL'];
        }
        if($get['form']['BoxW']){
            $strWhere1 .= ' AND BoxW = '.$get['form']['BoxW'];
        }
        if($get['form']['BoxH']){
            $strWhere1 .= ' AND BoxH = '.$get['form']['BoxH'];
        }
        if($this->TaskId){
            $strWhere1 .= ' AND TaskId = \'\''.$this->TaskId.'\'\'';
        }
        //构建 $strWhere2
        $strWhere2 = '';
        if($get['form']['OrderId']){
            $strWhere2 .= ' AND ( OrderId LIKE \'\'%'.$get['form']['OrderId'].'%\'\' OR strOrderId LIKE \'\'%'.$get['form']['OrderId'].'%\'\' )';
            //$strWhere2 = D('Func')->utf8_to_gbk($strWhere2);
        }
        if($get['form']['OrdQty']){
            $strWhere2 .= ' AND OrdQty = '.$get['form']['OrdQty'];
        }
        if($get['form']['CusId']){
            $strWhere2 .= ' AND CusId = \'\''.$get['form']['CusId'].'\'\'';
        }
        $CurPage = $get['CurPage']?$get['CurPage']:1;
        $PageSize = $get['PageSize']?$get['PageSize']:6;
        try {
            //$data = D('Procedure')->GetOrders($strWhere1,$get['State'],$strWhere2,'',$CurPage,$PageSize);
            $data = M()->query('exec GetOrders \''.$strWhere1.'\',\''.$get['State'].'\',\''.$strWhere2.'\',\'\',\''.$CurPage.'\',\''.$PageSize.'\'');
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

    //客户Picker
    public function CusPicker_api(){
        $get = I('get.');
        $data = D('Cus')->getCusPicker($this->TaskId,$get['CusKeyword']);
        echo jejuu($data);
    }

    //订单详情
    public function detail_api(){
        //sleep(2);
        $get = I('get.');
        $data = D('Order')->getOrderDetail($get['OrderType'],$get['OrderId']);
        echo jejuu($data);
    }

}
