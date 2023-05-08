<?php
namespace Wap0\Controller;
class OrderController extends BaseController{

    /********** 页面 **********/

    //ERP订单
    public function GetOrders(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap0_User.rememberTab_'.$mca);
        $rememberForm = session('ERP_Wap0_User.rememberForm_'.$mca)?session('ERP_Wap0_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => 'ERP订单',
            'rememberTab' => $rememberTab,
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    //对账单
    public function GetCusFreeMB(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap0_User.rememberTab_'.$mca);
        $rememberForm = session('ERP_Wap0_User.rememberForm_'.$mca)?session('ERP_Wap0_User.rememberForm_'.$mca):[];
        $data = D('Odbc')->query('SELECT LastMBDate FROM Customer WHERE CusId = \''.$this->ERPId.'\'','fetch');
        $this->assign([
            'LayoutTitle' => '对账单',
            'rememberTab' => $rememberTab,
            'rememberForm' => jejuu($rememberForm),
            'BeginDate' => date('Y-m-d',max(strtotime($data['LastMBDate']),strtotime('-3 month'))),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //ERP订单
    public function GetOrders_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap0_User.rememberTab_'.$mca,$get['State']);
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap0_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap0_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['Wap0GetOrdersMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['Wap0GetOrdersMaxDate'])));
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

        //构建 $strWhere2
        $strWhere2 = ' AND CusId = \'\''.$this->ERPId.'\'\'';//必须存在的限制条件,只能查询自己的订单
        if($get['form']['IsFromCus'] === '1'){
            $strWhere2 .= ' AND IsFromCus = \'\'1\'\'';
        }else if($get['form']['IsFromCus'] === '0'){
            $strWhere2 .= ' AND IsFromCus = \'\'0\'\'';
        }else{
            $strWhere2 .= '';
        }
        if($get['form']['OrderId']){
            $strWhere2 .= ' AND ( OrderId LIKE \'\'%'.$get['form']['OrderId'].'%\'\' OR strOrderId LIKE \'\'%'.$get['form']['OrderId'].'%\'\' )';
            //$strWhere2 = D('Func')->utf8_to_gbk($strWhere2);
        }
        if($get['form']['OrdQty']){
            $strWhere2 .= ' AND OrdQty = '.$get['form']['OrdQty'];
        }

        $CurPage = $get['CurPage']?$get['CurPage']:1;
        $PageSize = $get['PageSize']?$get['PageSize']:6;

        try {
            //$data = D('Procedure')->GetOrders($strWhere1,$get['State'],$strWhere2,'',$CurPage,$PageSize);
            $data = M()->query('exec GetOrders \''.$strWhere1.'\',\''.$get['State'].'\',\''.$strWhere2.'\',\'\',\''.$CurPage.'\',\''.$PageSize.'\'');
        } catch(\Exception $e){
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

    //对账单
    public function GetCusFreeMB_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap0_User.rememberTab_'.$mca,$get['Type']);
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap0_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap0_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['GetCusFreeMBMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['GetCusFreeMBMaxDate'])));
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
        //$Procedure = D('Procedure');
        $strWhere = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if($get['Type'] === 'GetCusFreeMBDN'){
            try {
                //$data = $Procedure->GetCusFreeMBDN($this->ERPId,$get['form']['BeginDate'],$get['form']['EndDate'],$strWhere);
                $data = M()->query('exec GetCusFreeMBDN \''.$this->ERPId.'\',\''.$get['form']['BeginDate'].'\',\''.$get['form']['EndDate'].'\',\''.$strWhere.'\'');
            } catch(\Exception $e){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => $e->getMessage(),
                ]);die;
            }
        }elseif($get['Type'] === 'GetCusFreeMBDNRe'){
            try {
                //$data = $Procedure->GetCusFreeMBDNRe($this->ERPId,$get['form']['BeginDate'],$get['form']['EndDate'],$strWhere);
                $data = M()->query('exec GetCusFreeMBDNRe \''.$this->ERPId.'\',\''.$get['form']['BeginDate'].'\',\''.$get['form']['EndDate'].'\',\''.$strWhere.'\'');
            } catch(\Exception $e){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => $e->getMessage(),
                ]);die;
            }
        }elseif($get['Type'] === 'GetCusFreeMBDis'){
            try {
                //$data = $Procedure->GetCusFreeMBDis($this->ERPId,$get['form']['BeginDate'],$get['form']['EndDate'],$strWhere);
                $data = M()->query('exec GetCusFreeMBDis \''.$this->ERPId.'\',\''.$get['form']['BeginDate'].'\',\''.$get['form']['EndDate'].'\',\''.$strWhere.'\'');
            } catch(\Exception $e){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => $e->getMessage(),
                ]);die;
            }
        }else{
            $data = [];
        }
        //dump($data);die;
        foreach ($data as $k => $v) {
            if($v['IssueDate']){
                $data[$k]['IssueDate'] = date('Y-m-d',strtotime($v['IssueDate']));
            }
            $data[$k]['Amount'] = sprintf('%.2f',$v['Amount']);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //订单详情
    public function detail_api(){
        //sleep(2);
        $get = I('get.');
        $data = D('Wap1/Order')->getOrderDetail($get['OrderType'],$get['OrderId']);
        echo jejuu($data);
    }

}
