<?php
namespace Common\Model;
//存储过程
class ProcedureModel extends OdbcModel{

    //原纸出库
    public function DoStockOut($FactoryId,$StockNo,$OpTime,$OpClass,$SFluteTo,$BZWt){
        //$sql = "exec DoStockOut '$FactoryId','$OpTime','$OpClass','','$StockNo','$SFluteTo','0','$BZWt','','0','','','0','0','0','0','',''";
        $sql = 'exec DoStockOut ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
        $res = $this->PDO->prepare($sql);
        $OpRemark = '';
        $PWBatchNo = 0;
        $OutUserId = '';
        $ErrorNo = 0;
        $IsWholeStock = '';
        $PaperWidthCode = '';
        $PaperWt = 0;
        $SDiameter = 0;
        $SLength = 0;
        $InWt = 0;
        $PaperCode = '';
        $ShortName = '';
        $res->bindParam(1, $FactoryId , \PDO::PARAM_STR);
        $res->bindParam(2, $OpTime , \PDO::PARAM_STR);
        $res->bindParam(3, $OpClass , \PDO::PARAM_STR);
        $res->bindParam(4, $OpRemark , \PDO::PARAM_STR);
        $res->bindParam(5, $StockNo , \PDO::PARAM_STR);
        $res->bindParam(6, $SFluteTo , \PDO::PARAM_STR);
        $res->bindParam(7, $PWBatchNo , \PDO::PARAM_STR);
        $res->bindParam(8, $BZWt , \PDO::PARAM_STR);
        $res->bindParam(9, $OutUserId , \PDO::PARAM_STR);
        $res->bindParam(10, $ErrorNo, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, \PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
        $res->bindParam(11, $IsWholeStock, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 1);
        $res->bindParam(12, $PaperWidthCode, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 10);
        $res->bindParam(13, $PaperWt, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, \PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
        $res->bindParam(14, $SDiameter, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, \PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
        $res->bindParam(15, $SLength, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, \PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
        $res->bindParam(16, $InWt, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, \PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
        $res->bindParam(17, $PaperCode, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 10);
        $res->bindParam(18, $ShortName, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 20);
        $res->execute();
        do {
            $Rowset = $res->fetch(\PDO::FETCH_NUM);
            if($Rowset){
                return $Rowset[0];
            }
        } while ($res->nextRowset());
    }

    //原纸入库
    public function DoRStockIn($FactoryId,$StockNo,$OpTime,$Weight){
        //$sql = "exec DoRStockIn '$FactoryId','$OpTime','','','$StockNo','$Weight','0','0','0','','0'";
        $sql = 'exec DoRStockIn ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
        $res = $this->PDO->prepare($sql);
        $OpRemark = '';
        $InUserId = '';
        $OpClass = '';
        $PWBatchNo = 0;
        $ErrorNo = 0;
        $SDiameter = 0;
        $SLength = 0;
        $res->bindParam(1, $FactoryId , \PDO::PARAM_STR);
        $res->bindParam(2, $OpTime , \PDO::PARAM_STR);
        $res->bindParam(3, $OpClass , \PDO::PARAM_STR);
        $res->bindParam(4, $OpRemark , \PDO::PARAM_STR);
        $res->bindParam(5, $StockNo , \PDO::PARAM_STR);
        $res->bindParam(6, $Weight , \PDO::PARAM_STR);
        $res->bindParam(7, $SLength , \PDO::PARAM_STR);
        $res->bindParam(8, $SDiameter , \PDO::PARAM_STR);
        $res->bindParam(9, $PWBatchNo , \PDO::PARAM_STR);
        $res->bindParam(10, $InUserId , \PDO::PARAM_STR);
        $res->bindParam(11, $ErrorNo, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, \PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
        $res->execute();
        do {
            $Rowset = $res->fetch(\PDO::FETCH_NUM);
            if($Rowset){
                return $Rowset[0];
            }
        } while ($res->nextRowset());
    }

//    //ERP订单
//    public function GetOrders($strWhere1,$State,$strWhere2,$SType,$CurPage,$PageSize){
//        $sql = "exec GetOrders '$strWhere1','$State','$strWhere2','$SType','$CurPage','$PageSize'";
//        //echo $sql;die;
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        //dump(D('Func')->gbk_to_utf8($res->errorInfo()));die;
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //订单详情
//    public function GetOrderDetail($strWhere,$OrderType,$OrderId){
//        $sql = "exec GetOrderDetail '$strWhere','$OrderType','$OrderId'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetch(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //传单明细
//    public function GetOrderSch($strWhere,$OrderType,$OrderId){
//        $sql = "exec GetOrderSch '$strWhere','$OrderType','$OrderId'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetch(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //送货明细
//    public function GetOrderDN($strWhere,$OrderType,$OrderId){
//        $sql = "exec GetOrderDN '$strWhere','$OrderType','$OrderId'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetch(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //退货详细
//    public function GetOrderDNRe($strWhere,$OrderType,$OrderId){
//        $sql = "exec GetOrderDNRe '$strWhere','$OrderType','$OrderId'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetch(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //订单统计
//    public function GetOrderSum($strWhere,$State){
//        $sql = "exec GetOrderSum '$strWhere','$State'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //退货统计
//    public function GetOrdReturnSum($strWhere,$State){
//        $sql = "exec GetOrdReturnSum '$strWhere','$State'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //传单统计
//    public function GetSchSum($strWhere,$State){
//        $sql = "exec GetSchSum '$strWhere','$State'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //库存统计
//    public function GetOrdStock($strWhere,$State,$iRemainDay,$iDiffDDay){
//        $sql = "exec GetOrdStock '$strWhere','$State','$iRemainDay','$iDiffDDay'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //生产分析总计
//    public function GetProInfo($BeginDate,$EndDate,$FactoryId){
//        $sql = "exec GetProInfo '$BeginDate','$EndDate','$FactoryId'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //原纸库存
//    public function GetSStock($strWhere){
//        $sql = "exec GetSStock '$strWhere'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //外部对账单 1 送货
//    public function GetCusFreeMBDN($CusId,$BeginDate,$EndDate,$strWhere){
//        $sql = "exec GetCusFreeMBDN '$CusId','$BeginDate','$EndDate','$strWhere'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //外部对账单 2 退货
//    public function GetCusFreeMBDNRe($CusId,$BeginDate,$EndDate,$strWhere){
//        $sql = "exec GetCusFreeMBDNRe '$CusId','$BeginDate','$EndDate','$strWhere'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }
//
//    //外部对账单 3 调账折扣
//    public function GetCusFreeMBDis($CusId,$BeginDate,$EndDate,$strWhere){
//        $sql = "exec GetCusFreeMBDis '$CusId','$BeginDate','$EndDate','$strWhere'";
//        $res = $this->PDO->prepare($sql);
//        $res->execute();
//        $data = $res->fetchAll(\PDO::FETCH_ASSOC);
//        $data = D('Func')->gbk_to_utf8($data);
//        return $data;
//    }

}