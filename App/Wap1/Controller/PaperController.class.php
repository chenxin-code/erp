<?php
namespace Wap1\Controller;
//原纸
class PaperController extends BaseController{

    /********** 页面 **********/

    //原纸库存
    public function GetSStock(){
        //$CodeSelect = D('Odbc')->query('SELECT PaperId,PaperName FROM PaperCode WHERE PaperId NOT IN (\'-\',\'--\')','fetchAll');
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND sr.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND sr.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        try {
            $WidthSelect = M()->query('exec GetStockInfoCur \''.$strWhere.'\',\'0\'');
            $CodeSelect = M()->query('exec GetStockInfoCur \''.$strWhere.'\',\'1\'');
        } catch (\Exception $e) {
            $this->error($e->getMessage());die;
        }
        foreach ($WidthSelect as $k => $v) {
            unset($WidthSelect[$k]['PaperCode']);
            unset($WidthSelect[$k]['PaperName']);
            $WidthSelect[$k]['PaperWidth'] = (int)$v['PaperWidth'];
        }
        foreach ($CodeSelect as $k => $v) {
            unset($CodeSelect[$k]['PaperWidth']);
        }
        //dump($WidthSelect);dump($CodeSelect);die;
        $this->assign([
            'LayoutTitle' => '原纸库存',
            'WidthSelect' => jejuu($WidthSelect),
            'CodeSelect' => jejuu($CodeSelect),
        ]);
        $this->display();
    }

    //安全库存
    public function GetSafeStockQ(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap1_User.rememberTab_'.$mca);
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '安全库存',
            'rememberTab' => $rememberTab,
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    //原纸出库
    public function DoStockOut(){
        $signPackage = D('Wxjssdk')->getSignPackage();//微信JSSDK获取SignPackage
        $this->assign([
            'LayoutTitle' => '原纸出库',
            'signPackage' => $signPackage,
        ]);
        $this->display();
    }

    //原纸入库
    public function DoRStockIn(){
        $signPackage = D('Wxjssdk')->getSignPackage();//微信JSSDK获取SignPackage
        $this->assign([
            'LayoutTitle' => '原纸入库',
            'signPackage' => $signPackage,
        ]);
        $this->display();
    }

    //直接入库
    public function DirectInStock(){
        $signPackage = D('Wxjssdk')->getSignPackage();//微信JSSDK获取SignPackage
        if($this->config['bMStockArea']){
            if($this->config['bSAreaControl']){
                $sql = 'SELECT SAreaCode AS StockArea FROM DeliveryArea WHERE FactoryId = \''.$this->config['FactoryId'].'\' ORDER BY SAreaCode';
            }else{
                $sql = 'SELECT RCId AS StockArea FROM RemarkConf WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND Type = \'3\' ORDER BY RCId';
            }
            $StockAreaSelect = D('Odbc')->query($sql,'fetchAll');
        }else{
            $StockAreaSelect = [];
        }
        $this->assign([
            'LayoutTitle' => '直接入库',
            'signPackage' => $signPackage,
            'StockAreaSelect' => jejuu($StockAreaSelect),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //原纸库存
    public function GetSStock_api(){
        $get = I('get.');
        $strWhere = ($this->config['FactoryId'] === '')?'':'FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND p.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        if(!is_null($get['PaperWidth']) && is_null($get['PaperCode'])){
            $strWhere .= ' AND PaperWidth = \'\''.$get['PaperWidth'].'\'\'';
            $flag1 = 'PaperWidth';
            $flag2 = 'PaperCode';
        }elseif(is_null($get['PaperWidth']) && !is_null($get['PaperCode'])){
            $strWhere .= ' AND PaperCode = \'\''.$get['PaperCode'].'\'\'';
            $flag1 = 'PaperCode';
            $flag2 = 'PaperWidth';
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        try {
            $data = M()->query('exec GetSStock \''.$strWhere.'\'');
        } catch (\Exception $e) {
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        if($data){
            $SumZJWt = 0;
            $SumiZJCount = 0;
            $SumRWt = 0;
            $SumiRCount = 0;
            foreach ($data as $k => $v) {
                unset($data[$k][$flag1]);
                $data[$k]['ZJWt'] = (int)$v['ZJWt'];
                $data[$k]['iZJCount'] = (int)$v['iZJCount'];
                $data[$k]['RWt'] = (int)$v['RWt'];
                $data[$k]['iRCount'] = (int)$v['iRCount'];
                $SumZJWt += (int)$v['ZJWt'];
                $SumiZJCount += (int)$v['iZJCount'];
                $SumRWt += (int)$v['RWt'];
                $SumiRCount += (int)$v['iRCount'];
            }
            $data[] = [
                $flag2 => '合计',
                'ZJWt' => $SumZJWt,
                'iZJCount' => $SumiZJCount,
                'RWt' => $SumRWt,
                'iRCount' => $SumiRCount,
            ];
        }
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    //安全库存
    public function GetSafeStockQ_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap1_User.rememberTab_'.$mca,$get['State']);
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $strWhere = ($this->config['FactoryId'] === '')?'':' AND a.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if($get['State'] === '1'){
            $strWhere .= ' AND PCount < SafeCount';//小于安全库存的
        }elseif($get['State'] === '2'){
            $strWhere .= ' AND PCount > MaxCount';//大于上限库存的
        }elseif($get['State'] === '3'){
            $strWhere .= ' AND PCount > 0';//仅显示有库存的
        }
        if($get['form']['PaperType'] === '0'){
            $strWhere .= ' AND PaperType = 0';//无
        }elseif($get['form']['PaperType'] === '1'){
            $strWhere .= ' AND PaperType = 1';//牛皮挂面
        }elseif($get['form']['PaperType'] === '2'){
            $strWhere .= ' AND PaperType = 2';//涂布白板
        }elseif($get['form']['PaperType'] === '3'){
            $strWhere .= ' AND PaperType = 3';//瓦纸
        }
        if($get['form']['PaperCode']){
            $strWhere .= ' AND PaperCode = \'\''.$get['form']['PaperCode'].'\'\'';
        }
        if($get['form']['PaperWidth']){
            $strWhere .= ' AND a.PaperWidth = '.$get['form']['PaperWidth'];
        }
        if($get['form']['PaperName']){
            $strWhere .= ' AND PaperName LIKE \'\'%'.$get['form']['PaperName'].'%\'\'';
        }
        if($get['form']['SRemark']){
            $strWhere .= ' AND a.SRemark LIKE \'\'%'.$get['form']['SRemark'].'%\'\'';
        }
        $sql = 'exec GetSafeStockQ \''.$strWhere.'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $sql .= ',\''.session('ERP_Wap1_User.SubFacId').'\'';
        }
        $data = M()->query($sql);
        foreach ($data as $k => $v) {
            $data[$k]['PaperWidth'] = (int)$v['PaperWidth'];
            if($v['PaperType'] === '0'){
                $data[$k]['PaperType'] = '无';
            }elseif($v['PaperType'] === '1'){
                $data[$k]['PaperType'] = '牛皮挂面';
            }elseif($v['PaperType'] === '2'){
                $data[$k]['PaperType'] = '涂布白板';
            }elseif($v['PaperType'] === '3'){
                $data[$k]['PaperType'] = '瓦纸';
            }
        }
        //dump($data);die;
        echo jejuu($data);
    }

    //通过 StockNo 获取原纸信息 （原纸出库）
    public function GetInfoByDoStockOut_api(){
        $get = I('get.');
        if($this->config['FactoryId'] === ''){
            $sql = 'SELECT PaperWidth,PaperCode,PaperWt,CurWt FROM ZJStock WHERE StockNo = \''.$get['StockNo'].'\' AND ISNULL(PWBatchNo,0) = 0 
            UNION ALL SELECT z.PaperWidth,z.PaperCode,z.PaperWt,s.CWeight CurWt FROM StockRemain s LEFT OUTER JOIN ZJStock z ON s.StockNo = z.StockNo 
            WHERE s.StockNo = \''.$get['StockNo'].'\' AND s.State = 0';
        }else{
            $sql = 'SELECT PaperWidth,PaperCode,PaperWt,CurWt FROM ZJStock WHERE StockNo = \''.$get['StockNo'].'\' AND ISNULL(PWBatchNo,0) = 0 AND FactoryId = \''.$this->config['FactoryId'].'\''.(session('ERP_Wap1_User.SubFacId')?' AND SubFacId = \''.session('ERP_Wap1_User.SubFacId').'\'':'').' 
            UNION ALL SELECT z.PaperWidth,z.PaperCode,z.PaperWt,s.CWeight CurWt FROM StockRemain s LEFT OUTER JOIN ZJStock z ON s.StockNo = z.StockNo AND s.FactoryId = z.FactoryId 
            WHERE s.StockNo = \''.$get['StockNo'].'\' AND s.State = 0 AND s.FactoryId = \''.$this->config['FactoryId'].'\''.(session('ERP_Wap1_User.SubFacId')?' AND z.SubFacId = \''.session('ERP_Wap1_User.SubFacId').'\'':'');
        }
        $data = D('Odbc')->query($sql,'fetch');
        if($data){
            echo jejuu([
                'ret' => C('succ_ret'),
                'data' => $data,
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '没有找到相关原纸信息',
            ]);
        }
    }

    //通过 StockNo 获取原纸信息 （原纸入库）
    public function GetInfoByDoRStockIn_api(){
        $get = I('get.');
        if($this->config['FactoryId'] === ''){
            $sql = 'SELECT z.PaperWidth,z.PaperCode,z.PaperWt,s.OriWt FROM StockOutIn s 
LEFT OUTER JOIN ZJStock z ON s.StockNo = z.StockNo 
WHERE s.StockNo = \''.$get['StockNo'].'\' AND s.State = 0';
        }else{
            $sql = 'SELECT z.PaperWidth,z.PaperCode,z.PaperWt,s.OriWt FROM StockOutIn s 
LEFT OUTER JOIN ZJStock z ON s.StockNo = z.StockNo AND s.FactoryId = z.FactoryId 
WHERE s.StockNo = \''.$get['StockNo'].'\' AND s.State = 0 AND s.FactoryId = \''.$this->config['FactoryId'].'\''.(session('ERP_Wap1_User.SubFacId')?' AND s.SubFacId = \''.session('ERP_Wap1_User.SubFacId').'\'':'');
        }
        $data = D('Odbc')->query($sql,'fetch');
        if($data){
            echo jejuu([
                'ret' => C('succ_ret'),
                'data' => $data,
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '没有找到相关原纸信息',
            ]);
        }
    }

    //原纸出库
    public function DoStockOut_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['DoStockOutMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['DoStockOutMaxDate'])));
        if(strtotime($get['OpTime']) < $MinTime || strtotime($get['OpTime']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '出库日期非法',
            ]);die;
        }
        $ErrorNo = D('Procedure')->DoStockOut($this->config['FactoryId'],$get['StockNo'],$get['OpTime'],$get['OpClass'],$get['SFluteTo'],$get['BZWt']);
        switch ($ErrorNo) {
            case '0':
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '出库成功',
                ]);
                break;
            case '1':
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '残卷和整卷都没找到在库记录',
                ]);
                break;
            case '3':
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '没找到原纸的收货记录',
                ]);
                break;
            default:
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '出库失败（未知错误）',
                ]);
        }
    }

    //原纸入库
    public function DoRStockIn_api(){
        $get = I('get.');
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['DoRStockInMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['DoRStockInMaxDate'])));
        if(strtotime($get['OpTime']) < $MinTime || strtotime($get['OpTime']) > $MaxTime){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '入库日期非法',
            ]);die;
        }
        $ErrorNo = D('Procedure')->DoRStockIn($this->config['FactoryId'],$get['StockNo'],$get['OpTime'],$get['Weight']);
        switch ($ErrorNo) {
            case '0':
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '入库成功',
                ]);
                break;
            case '2':
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '异常没有找到整卷出库记录',
                ]);
                break;
            case '4':
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '异常该收货编号的残卷有未出记录就再次入库',
                ]);
                break;
            case '7':
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '该批号和收货编号的原纸无上机记录',
                ]);
                break;
            case '8':
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '该批号和收货编号的原纸已回仓',
                ]);
                break;
            default:
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '入库失败（未知错误）',
                ]);
        }
    }

    //直接入库
    public function GetLastSchArea_api(){
        $strWhere = ' AND b.OrderType + b.OrderId = \'\''.$_GET['strOrderId'].'\'\' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $data = M()->query('exec GetLastSchArea \''.$strWhere.'\'');
        //dump($data);die;
        echo jejuu($data);
    }
    public function GetOrdSchArea_api(){
        $strWhere = ' AND s.OrderType + s.OrderId = \'\''.$_GET['strOrderId'].'\'\' AND s.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        $data = M()->query('exec GetOrdSchArea \''.$strWhere.'\'');
        //dump($data);die;
        echo jejuu($data);
    }
    public function GetOrdInInfo_api(){
        $strWhere = ' AND b.OrderType + b.OrderId = \'\''.$_GET['strOrderId'].'\'\' AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
        if(session('ERP_Wap1_User.SubFacId')){
            $strWhere .= ' AND b.SubFacId = \'\''.session('ERP_Wap1_User.SubFacId').'\'\'';
        }
        try {
            $data = M()->query('exec GetOrdInInfo \''.$strWhere.'\'');
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