<?php
namespace Wap1\Model;
//订单
class OrderModel{

    //获取订单详情的方法
    public function getOrderDetail($OrderType,$OrderId){
        //$Procedure = D('Procedure');
        $FactoryId = D('Func')->getConfig()['FactoryId'];
        $strWhere = ($FactoryId === '')?'':'FactoryId = \'\''.$FactoryId.'\'\'';
        $data = [
            //'GetOrderDNRe' => $Procedure->GetOrderDNRe($strWhere,$OrderType,$OrderId),
            //'GetOrderDN' => $Procedure->GetOrderDN($strWhere,$OrderType,$OrderId),
            //'GetOrderSch' => $Procedure->GetOrderSch($strWhere,$OrderType,$OrderId),
            //'GetOrderDetail' => $Procedure->GetOrderDetail($strWhere,$OrderType,$OrderId),
            'GetOrderDNRe' => M()->query('exec GetOrderDNRe \''.$strWhere.'\',\''.$OrderType.'\',\''.$OrderId.'\'')[0],
            'GetOrderDN' => M()->query('exec GetOrderDN \''.$strWhere.'\',\''.$OrderType.'\',\''.$OrderId.'\'')[0],
            'GetOrderSch' => M()->query('exec GetOrderSch \''.$strWhere.'\',\''.$OrderType.'\',\''.$OrderId.'\'')[0],
            'GetOrderDetail' => M()->query('exec GetOrderDetail \''.$strWhere.'\',\''.$OrderType.'\',\''.$OrderId.'\'')[0],
        ];
        //dump($data);die;
        if($data['GetOrderDNRe']){
            $data['GetOrderDNRe']['TSalesArea'] = floatval($data['GetOrderDNRe']['TSalesArea']);
            $data['GetOrderDNRe']['DeliAmt'] = sprintf('%.2f',$data['GetOrderDNRe']['DeliAmt']);
            $data['GetOrderDNRe']['Price'] = sprintf('%.2f',$data['GetOrderDNRe']['Price']);
            $data['GetOrderDNRe']['SquarePrice'] = sprintf('%.2f',$data['GetOrderDNRe']['SquarePrice']);
            if($data['GetOrderDNRe']['IssueDate']){
                $data['GetOrderDNRe']['IssueDate'] = date('Y-m-d',strtotime($data['GetOrderDNRe']['IssueDate']));
            }
        }
        if($data['GetOrderDN']){
            $data['GetOrderDN']['TSalesArea'] = floatval($data['GetOrderDN']['TSalesArea']);
            $data['GetOrderDN']['DeliAmt'] = sprintf('%.2f',$data['GetOrderDN']['DeliAmt']);
            $data['GetOrderDN']['Price'] = sprintf('%.2f',$data['GetOrderDN']['Price']);
            $data['GetOrderDN']['SquarePrice'] = sprintf('%.2f',$data['GetOrderDN']['SquarePrice']);
            if($data['GetOrderDN']['PackDate']){
                $data['GetOrderDN']['PackDate'] = date('Y-m-d',strtotime($data['GetOrderDN']['PackDate']));
            }
            if($data['GetOrderDN']['DNDate']){
                $data['GetOrderDN']['DNDate'] = date('Y-m-d',strtotime($data['GetOrderDN']['DNDate']));
            }
            if($data['GetOrderDN']['IssueDate']){
                $data['GetOrderDN']['IssueDate'] = date('Y-m-d',strtotime($data['GetOrderDN']['IssueDate']));
            }
        }
        if($data['GetOrderSch']){
            $data['GetOrderSch']['SPaperWidth'] = floatval($data['GetOrderSch']['SPaperWidth']);
            if($data['GetOrderSch']['AddTime']){
                $data['GetOrderSch']['AddTime'] = date('Y-m-d H:i:s',strtotime($data['GetOrderSch']['AddTime']));
            }
        }
        if($data['GetOrderDetail']){
            $data['GetOrderDetail']['BoxL'] = floatval($data['GetOrderDetail']['BoxL']);
            $data['GetOrderDetail']['BoxW'] = floatval($data['GetOrderDetail']['BoxW']);
            $data['GetOrderDetail']['BoxH'] = floatval($data['GetOrderDetail']['BoxH']);
            $data['GetOrderDetail']['Length'] = floatval($data['GetOrderDetail']['Length']);
            $data['GetOrderDetail']['Width'] = floatval($data['GetOrderDetail']['Width']);
            $data['GetOrderDetail']['TSalesArea'] = floatval($data['GetOrderDetail']['TSalesArea']);
            $data['GetOrderDetail']['OrdPrice'] = sprintf('%.2f',$data['GetOrderDetail']['OrdPrice']);
            $data['GetOrderDetail']['Price'] = sprintf('%.2f',$data['GetOrderDetail']['Price']);
            $data['GetOrderDetail']['SquarePrice'] = sprintf('%.2f',$data['GetOrderDetail']['SquarePrice']);
            $data['GetOrderDetail']['Amt'] = sprintf('%.2f',$data['GetOrderDetail']['Amt']);
        }
        //dump($data);die;
        return $data;
    }

}