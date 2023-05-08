<?php
namespace Wap0\Model;
//下单
class BuildModel{

    var $config;

    public function __construct(){
        $this->config = D('Func')->getConfig();
    }

    //生成客订单号的方法
    public function makeCusPoNo($handCusPoNo,$CusId){
        $CusPoNoSuffixLength = C('CusPoNoSuffixLength');//客订单号后缀长度
        $ymd = date('Ymd',time());
        $todayCusPoNoAssem = [];//系统自动生成当天的客订单号集合
        for($i = 1;$i <= (int)str_repeat('9',$CusPoNoSuffixLength);$i ++){
            $todayCusPoNoAssem[] = $ymd.str_repeat('0',$CusPoNoSuffixLength - strlen($i)).$i;
        }
        if($handCusPoNo || $handCusPoNo === '0'){
            if(in_array($handCusPoNo,$todayCusPoNoAssem,true)){
                return [
                    'bool' => FALSE,
                    'reason' => '客订单号格式与系统自动生成格式一致，请调整格式',
                ];
            }else{
                if(M()->table('WebappOrder')->where([
                    'FactoryId' => $this->config['FactoryId'],
                    'CusId' => $CusId,
                    'CusPoNo' => $handCusPoNo,
                ])->find()){
                    return [
                        'bool' => FALSE,
                        'reason' => '客订单号已使用',
                    ];
                }
                return [
                    'bool' => TRUE,
                    'CusPoNo' => $handCusPoNo,
                ];
            }
        }else{
            //自动生成客订单号
            $_CusPoNoArr = M()->table('WebappOrder')->where([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $CusId,
                'CusPoNo' => ['IN',$todayCusPoNoAssem],
            ])->field('CusPoNo')->select();
            $CusPoNoArr = [];
            foreach ($_CusPoNoArr as $v) {
                $CusPoNoArr[] = $v['CusPoNo'];
            }
            if($CusPoNoArr){
                $autoCusPoNo = (string)(max($CusPoNoArr) + 1);
                if(in_array($autoCusPoNo,$todayCusPoNoAssem,true)){
                    return [
                        'bool' => TRUE,
                        'CusPoNo' => $autoCusPoNo,
                    ];
                }else{
                    return [
                        'bool' => FALSE,
                        'reason' => '系统自动生成客订单号已超出范围，请手动添加客订单号',
                    ];
                }
            }else{
                $autoCusPoNo = $ymd.str_repeat('0',$CusPoNoSuffixLength - 1).'1';
                return [
                    'bool' => TRUE,
                    'CusPoNo' => $autoCusPoNo,
                ];
            }
        }
    }

    //检测ERP是否停止下单
    public function checkERPstopBuild(){
        $where = [
            'DataName' => 'DisWebOrd',
            'DataValue' => 1,
        ];
        if($this->config['FactoryId'] !== ''){
            $where['FactoryId'] = $this->config['FactoryId'];
        }
        if(M()->table('SysPara')->where($where)->find()){
            return TRUE;
        }else{
            return FALSE;
        }
    }

}
