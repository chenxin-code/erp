<?php
namespace Wap1\Controller;
//扫描装货2个页面及相关接口
class StowController extends BaseController{

    /********** 页面 **********/

    //列表
    public function lists(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '扫描装货（列表）',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    //详情
    public function detail(){
        $signPackage = D('Wxjssdk')->getSignPackage();//微信JSSDK获取SignPackage
        $CustomerDNSelect = D('Odbc')->query('SELECT CusId,CusSubNo,SubDNAddress FROM CustomerDN','fetchAll');
        $this->assign([
            'LayoutTitle' => '扫描装货（详情）',
            'signPackage' => $signPackage,
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //列表
    public function lists_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['StowMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['StowMaxDate'])));
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
        $strWhere = ' AND PackDate >= \'\''.$get['form']['BeginDate'].'\'\' AND PackDate <= \'\''.$get['form']['EndDate'].'\'\' AND p.FactoryId = \'\''.$this->config['FactoryId'].'\'\''.(session('ERP_Wap1_User.SubFacId')?' AND p.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'':'').' AND CarState <> 3';
        if($get['form']['bShowPack'] === 'no'){
            $strWhere .= ' AND CarState <> 1';
        }
        if($get['form']['bShowSign'] === 'no'){
            $strWhere .= ' AND CarState <> 2';
        }
        try {
            $data = M()->query('exec GetPackageList \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        foreach ($data as $k => $v) {
            $data[$k]['OrderTypeName'] = ($v['OrderType'] === 'x')?'纸箱':'纸板';
            $data[$k]['TVolume'] = round($v['TVolume'],2);
            $data[$k]['TWeight'] = round($v['TWeight'],0);
            $data[$k]['To5Area'] = round($v['To5Area'],2);
            $data[$k]['CarState'] = (int)$v['CarState'];
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //详情
    public function GetPDNDetail_api(){
        $get = I('get.');
        $strWhere = ' AND PListNo = '.$get['PListNo'].' AND FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $data = M()->query('exec GetPDNDetail \''.$strWhere.'\'');
        foreach ($data as $k => $v) {
            $data[$k]['OtherFee'] = sprintf('%.2f',$v['OtherFee']);
            $data[$k]['Length'] = (int)$v['Length'];
            $data[$k]['Width'] = (int)$v['Width'];
        }
        //dump($data);die;
        echo jejuu($data);
    }
    public function GetStockArea_api(){
        $get = I('get.');
        $sql = 'SELECT StockArea,Qty FROM StockArea WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND OrderType + OrderId = \''.$get['strOrderId'].'\' AND ( SchQty - MSchQty > 0 OR InQty > 0 ) ORDER BY StockArea';
        $data = D('Odbc')->query($sql,'fetchAll');
        //dump($data);die;
        echo jejuu($data);
    }
    public function GetOrdPackInfo_api(){
        $get = I('get.');
        $strWhere = ' AND b.OrderType + b.OrderId = \'\''.$get['strOrderId'].'\'\' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND b.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        try {
            $data = M()->query('exec GetOrdPackInfo \''.$strWhere.'\'');
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        //dump($data);die;
        if($data){
            echo jejuu([
                'ret' => C('succ_ret'),
                'data' => $data[0],
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '无法定位有效的订单号',
            ]);
        }
    }

}