<?php
namespace Group\Model;
class ProductModel{

    var $config;

    public function __construct(){
        $this->config = D('Func')->getConfig();
    }

    public function checkBase($Id,$Type){
        $where = ['FactoryId' => $this->config['FactoryId'],'Id' => $Id];
        if($Type === 'Board'){
            $where['BoardId'] = ['EXP','IS NOT NULL'];
            $where['MatNo'] = ['EXP','IS NULL'];
        }elseif($Type === 'Box'){
            $where['BoardId'] = ['EXP','IS NULL'];
            $where['MatNo'] = ['EXP','IS NOT NULL'];
        }
        $wp = M()->table('WebProduct')->where($where)->field('IsDel,BeginTime,EndTime')->find();
        if(!$wp){
            return [
                'bool' => FALSE,
                'reason' => '参数错误',
            ];
        }
        if($wp['IsDel'] === '0'){
            $time = time();
            if($time < $wp['BeginTime']){
                return [
                    'bool' => FALSE,
                    'reason' => '团购未开始',
                ];
            }elseif($time >= $wp['BeginTime'] && $time <= $wp['EndTime']){
                return [
                    'bool' => TRUE,
                ];
            }elseif($time > $wp['EndTime']){
                return [
                    'bool' => FALSE,
                    'reason' => '团购已结束',
                ];
            }else{
                return [
                    'bool' => FALSE,
                    'reason' => '异常错误',
                ];
            }
        }else{
            return [
                'bool' => FALSE,
                'reason' => '产品已删除',
            ];
        }
    }

    //团购下单前检查  单/总客户限量
    public function checkSize($Id,$CusId,$Build){
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $Id])->field('BoardId,MatNo,CusMax,Total')->find();
        if($wp['BoardId'] && !$wp['MatNo']){
            $field = 'Area';$unit = '㎡';
        }elseif(!$wp['BoardId'] && $wp['MatNo']){
            $field = 'OrdQty';$unit = '个';
        }
        $Sale = M()->table('WebappOrder')->alias('wo')
            ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
            ->where(['WebProductId' => $Id])
            ->sum($field);
        $Surp1 = $wp['Total'] - $Sale;
        if($Build > $Surp1){
            return [
                'bool' => FALSE,
                'reason' => '总客户限量'.(float)$wp['Total'].$unit.'，剩余'.(float)$Surp1.$unit,
            ];
        }
        $Cus = M()->table('WebappOrder')->alias('wo')
            ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
            ->where(['wgo.FactoryId' => $this->config['FactoryId'],'wgo.CusId' => $CusId,'WebProductId' => $Id])
            ->sum($field);
        $Surp2 = $wp['CusMax'] - $Cus;
        if($Build > $Surp2){
            return [
                'bool' => FALSE,
                'reason' => '单客户限量'.(float)$wp['CusMax'].$unit.'，剩余'.(float)$Surp2.$unit,
            ];
        }
        return ['bool' => TRUE];
    }

}
