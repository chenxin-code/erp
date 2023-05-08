<?php
namespace Wap1\Controller;
class StockController extends BaseController{


    /********** 页面 **********/


    //库存修改
    public function MStockDetailR(){
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
            'LayoutTitle' => '库存修改',
            'signPackage' => $signPackage,
            'StockAreaSelect' => jejuu($StockAreaSelect),
        ]);
        $this->display();
    }


    //库区面积
    public function deliveryArea(){
        if(!$this->config['bSAreaControl']){
            $this->error('库区控制已关闭');die;
        }
        $this->assign([
            'LayoutTitle' => '库区面积',
        ]);
        $this->display();
    }


    /********** 接口 **********/


    //库存修改
    public function GetStockDetail_api(){
        $sql = 'SELECT * FROM StockDetail WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND OrderType + OrderId = \''.$_GET['strOrderId'].'\' AND OpType IN (1,2,3,5,8) ORDER BY Effecttime';
        $data = D('Odbc')->query($sql,'fetchAll');
        foreach ($data as $k => $v) {
            $data[$k]['Effecttime'] = date('Y-m-d',strtotime($v['Effecttime']));
        }
        //dump($data);die;
        echo jejuu($data);
    }

    //库区面积
    public function deliveryArea_api(){
        $sql = 'SELECT d.SAreaCode,d.MaxSArea,d.CurSArea,(d.MaxSArea - d.CurSArea) AS LeftArea FROM DeliveryArea d WHERE d.FactoryId = \''.$this->config['FactoryId'].'\'';
        $data = D('Odbc')->query($sql,'fetchAll');
        foreach ($data as $k => $v) {
            $data[$k]['MaxSArea'] = floatval($v['MaxSArea']);
            $data[$k]['CurSArea'] = floatval($v['CurSArea']);
            $data[$k]['LeftArea'] = floatval($v['LeftArea']);
        }
        //dump($data);die;
        echo jejuu($data);
    }





}