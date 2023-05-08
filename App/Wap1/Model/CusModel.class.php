<?php
namespace Wap1\Model;
//客户
class CusModel{

    //获取CusPicker的方法
    public function getCusPicker($TaskId,$CusKeyword){
        $where = ['Stopped' => 0];
        if($TaskId){
            $where['TaskId'] = $TaskId;
        }
        if($CusKeyword){
            $where['CusId|CusShortName'] = ['like','%'.$CusKeyword.'%'];
        }
        $data = M()->table('Customer')->where($where)->field('CusId,CusShortName AS CusName')->select();
        foreach($data as $k => $v){
            unset($data[$k]['ROW_NUMBER']);
        }
        return $data;
    }

}