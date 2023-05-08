<?php
namespace Group\Model;
//下单前检测
class BcheckModel{

    var $config;

    public function __construct(){
        $this->config = D('Func')->getConfig();
    }

    //简单纸板下单
    public function s($data){
        if(D('Wap0/Build')->checkERPstopBuild()){
            return [
                'bool' => FALSE,
                'reason' => '当前ERP系统禁止下单',
            ];
        }
        if(!$data['Length']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交板长',
            ];
        }
        if($data['Length'] < $this->config['BuildMinLength'] || $data['Length'] > $this->config['BuildMaxLength']){
            return [
                'bool' => FALSE,
                'reason' => '板长范围必须在'.$this->config['BuildMinLength'].'~'.$this->config['BuildMaxLength'].'之间',
            ];
        }
        if(!$data['Width']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交板宽',
            ];
        }
        if($data['Width'] < $this->config['BuildMinWidth'] || $data['Width'] > $this->config['BuildMaxWidth']){
            return [
                'bool' => FALSE,
                'reason' => '板宽范围必须在'.$this->config['BuildMinWidth'].'~'.$this->config['BuildMaxWidth'].'之间',
            ];
        }
        if(!$data['ScoreName']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交压线名称',
            ];
        }
        $BuildScoreName = $this->config['BuildScoreName']?explode(',',$this->config['BuildScoreName']):[];
        $key = array_search($data['ScoreName'],$BuildScoreName,true);
        if($key === FALSE){
            return [
                'bool' => FALSE,
                'reason' => '非法压线名称',
            ];
        }else{
            if($key === 0){
                if($data['ScoreInfo']){
                    return [
                        'bool' => FALSE,
                        'reason' => '【'.$BuildScoreName[0].'】不能提交压线信息',
                    ];
                }
            }else{
                if(!$data['ScoreInfo']){
                    return [
                        'bool' => FALSE,
                        'reason' => '没有提交压线信息',
                    ];
                }
            }
        }
        if($data['ScoreInfo'] && !preg_match('@'.C('ScoreInfoPatternPHP').'@',$data['ScoreInfo'])){
            return [
                'bool' => FALSE,
                'reason' => '非法压线信息',
            ];
        }
        if($data['ScoreInfo'] && (int)$data['Width'] !== (int)eval('return '.$data['ScoreInfo'].';')){
            return [
                'bool' => FALSE,
                'reason' => '压线和不等于板宽',
            ];
        }
        if(!$data['OrdQty']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交订单数',
            ];
        }
        $BdQty = $data['OrdQty'];//简单纸板下单的纸板数=订单数
        $Area = ($data['Length'] / 1000) * ($data['Width'] / 1000) * $BdQty;
        $BuildMin = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $data['Id']])->getField('BuildMin');
        $BuildMax = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $data['Id']])->getField('BuildMax');
        if($Area < $BuildMin){
            return [
                'bool' => FALSE,
                'reason' => '下单面积不能小于'.(float)$BuildMin.'㎡',
            ];
        }
        if($Area > $BuildMax){
            return [
                'bool' => FALSE,
                'reason' => '下单面积不能大于'.(float)$BuildMax.'㎡',
            ];
        }
        if(!$data['CusSubNo']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交送货公司',
            ];
        }
        if(!M()->table('CustomerDN')->where(['CusSubNo' => $data['CusSubNo']])->find()){
            return [
                'bool' => FALSE,
                'reason' => '非法送货公司',
            ];
        }
        if(!$data['DeliveryDate']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交交货日期',
            ];
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['BuildMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['BuildMaxDate'])));
        if(strtotime($data['DeliveryDate']) < $MinTime || strtotime($data['DeliveryDate']) > $MaxTime){
            return [
                'bool' => FALSE,
                'reason' => '交货日期非法',
            ];
        }
        return ['bool' => TRUE];
    }

    //纸箱纸板下单
    public function c($data){
        if(D('Wap0/Build')->checkERPstopBuild()){
            return [
                'bool' => FALSE,
                'reason' => '当前ERP系统禁止下单',
            ];
        }
        if(!$data['BoxId']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交箱型',
            ];
        }
        if(!M()->table('BoxCode')->where(['BoxId' => $data['BoxId']])->find()){
            return [
                'bool' => FALSE,
                'reason' => '非法箱型',
            ];
        }
        if($data['BoxL'] && ($data['BoxL'] < $this->config['BuildMinBoxL'] || $data['BoxL'] > $this->config['BuildMaxBoxL'])){
            return [
                'bool' => FALSE,
                'reason' => '箱长范围必须在'.$this->config['BuildMinBoxL'].'~'.$this->config['BuildMaxBoxL'].'之间',
            ];
        }
        if($data['BoxW'] && ($data['BoxW'] < $this->config['BuildMinBoxW'] || $data['BoxW'] > $this->config['BuildMaxBoxW'])){
            return [
                'bool' => FALSE,
                'reason' => '箱宽范围必须在'.$this->config['BuildMinBoxW'].'~'.$this->config['BuildMaxBoxW'].'之间',
            ];
        }
        if($data['BoxH'] && ($data['BoxH'] < $this->config['BuildMinBoxH'] || $data['BoxH'] > $this->config['BuildMaxBoxH'])){
            return [
                'bool' => FALSE,
                'reason' => '箱高范围必须在'.$this->config['BuildMinBoxH'].'~'.$this->config['BuildMaxBoxH'].'之间',
            ];
        }
        if($data['TonLen'] && !in_array($data['TonLen'],$this->config['BuildTonLen']?explode(',',$this->config['BuildTonLen']):[])){
            return [
                'bool' => FALSE,
                'reason' => '非法箱舌',
            ];
        }
        if($data['ULen'] && !in_array($data['ULen'],$this->config['BuildULen']?explode(',',$this->config['BuildULen']):[])){
            return [
                'bool' => FALSE,
                'reason' => '非法封箱调整',
            ];
        }
        if(!$data['Length']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交板长',
            ];
        }
        if(!$data['Width']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交板宽',
            ];
        }
        if(!$data['BdMultiple']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交张数',
            ];
        }
        if(!$data['OrdQty']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交订单数',
            ];
        }
        if(!$data['BdQty']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交纸板数',
            ];
        }
        $Area = ($data['Length'] / 1000) * ($data['Width'] / 1000) * $data['BdQty'];
        $BuildMin = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $data['Id']])->getField('BuildMin');
        $BuildMax = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $data['Id']])->getField('BuildMax');
        if($Area < $BuildMin){
            return [
                'bool' => FALSE,
                'reason' => '下单面积不能小于'.(float)$BuildMin.'㎡',
            ];
        }
        if($Area > $BuildMax){
            return [
                'bool' => FALSE,
                'reason' => '下单面积不能大于'.(float)$BuildMax.'㎡',
            ];
        }
        if(!$data['CusSubNo']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交送货公司',
            ];
        }
        if(!M()->table('CustomerDN')->where(['CusSubNo' => $data['CusSubNo']])->find()){
            return [
                'bool' => FALSE,
                'reason' => '非法送货公司',
            ];
        }
        if(!$data['DeliveryDate']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交交货日期',
            ];
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['BuildMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['BuildMaxDate'])));
        if(strtotime($data['DeliveryDate']) < $MinTime || strtotime($data['DeliveryDate']) > $MaxTime){
            return [
                'bool' => FALSE,
                'reason' => '交货日期非法',
            ];
        }
        return ['bool' => TRUE];
    }

    //淘宝箱下单
    public function t($data){
        if(D('Wap0/Build')->checkERPstopBuild()){
            return [
                'bool' => FALSE,
                'reason' => '当前ERP系统禁止下单',
            ];
        }
        if(!$data['OrdQty']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交订单数',
            ];
        }
        $BuildMin = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $data['Id']])->getField('BuildMin');
        $BuildMax = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $data['Id']])->getField('BuildMax');
        if($data['OrdQty'] < $BuildMin){
            return [
                'bool' => FALSE,
                'reason' => '订单数不能小于'.(float)$BuildMin,
            ];
        }
        if($data['OrdQty'] > $BuildMax){
            return [
                'bool' => FALSE,
                'reason' => '订单数不能大于'.(float)$BuildMax,
            ];
        }
        if(!$data['CusSubNo']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交送货公司',
            ];
        }
        if(!M()->table('CustomerDN')->where(['CusSubNo' => $data['CusSubNo']])->find()){
            return [
                'bool' => FALSE,
                'reason' => '非法送货公司',
            ];
        }
        if(!$data['DeliveryDate']){
            return [
                'bool' => FALSE,
                'reason' => '没有提交交货日期',
            ];
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['BuildMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['BuildMaxDate'])));
        if(strtotime($data['DeliveryDate']) < $MinTime || strtotime($data['DeliveryDate']) > $MaxTime){
            return [
                'bool' => FALSE,
                'reason' => '交货日期非法',
            ];
        }
        return ['bool' => TRUE];
    }

}
