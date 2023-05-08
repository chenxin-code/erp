<?php
namespace Wap1\Controller;
use Think\Controller;
//隐藏功能（东恒专用）
class HideController extends Controller{

    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        $this->assign('config',$this->config);
    }

    //销售统计
    public function saleStatis(){
        $get = I('get.');
        if(preg_match(C('xxxxxxxxDatePattern'),$get['OrderDate']) && md5($get['OrderDate'].C('md5_salt')) === $get['secret']){
            $Title = date('Y/m/d',strtotime($get['OrderDate'])).'&nbsp;销售统计';
            $strWhere = $strWhere2 = ($this->config['FactoryId'] === '')?'':'AND b.FactoryId = \'\''.$this->config['FactoryId'].'\'\'';
            $strWhere .= ' AND b.OrderDate = \'\''.$get['OrderDate'].'\'\'';
            $strWhere2 .= ' AND b.OrderDate <= \'\''.$get['OrderDate'].'\'\'';
            if(M()->table('VarData')->where(['VarName' => 'EnableSubFac','VarValue' => 1])->find() && $get['SubFacId']){
                $strWhere .= ' AND c.CSubFacId = \'\''.$get['SubFacId'].'\'\'';
                $strWhere2 .= ' AND c.CSubFacId = \'\''.$get['SubFacId'].'\'\'';
                $SShortName = M()->table('SubFactory')->where(['FactoryId' => $this->config['FactoryId'],'SubFacId' => $get['SubFacId']])->getField('SShortName');
                if(!$SShortName){
                    $this->error('参数错误');die;
                }
                $Title .= '（分厂：'.$SShortName.'）';
            }
            try {
                $FacOrdSum = M()->query('exec GetFacOrdSum \''.$strWhere.'\',\''.$strWhere2.'\'')[0];
                $CusOrdSum = M()->query('exec GetCusOrdSum \''.$strWhere.'\'');
                $TaskOrdSum = M()->query('exec GetTaskOrdSum \''.$strWhere.'\'');
            } catch (\Exception $e) {
                $this->error($e->getMessage());die;
            }
            $FacOrdSum['OrdAmt'] = round($FacOrdSum['OrdAmt'],0);
            $FacOrdSum['TLength0'] = round($FacOrdSum['TLength0'],0);
            $FacOrdSum['TLength1'] = round($FacOrdSum['TLength1'],0);
            $FacOrdSum['TLength2'] = round($FacOrdSum['TLength2'],0);
            $FacOrdSum['TLength3'] = round($FacOrdSum['TLength3'],0);
            $FacOrdSum['TLength'] = round($FacOrdSum['TLength'],0);
            $FacOrdSum['WTLength'] = round($FacOrdSum['WTLength'],0);
            foreach($CusOrdSum as $k => $v){
                $CusOrdSum[$k]['TLength'] = round($v['TLength'],0);
                $CusOrdSum[$k]['Amt'] = round($v['Amt'],0);
            }
            foreach($TaskOrdSum as $k => $v){
                $TaskOrdSum[$k]['TLength'] = round($v['TLength'],0);
                $TaskOrdSum[$k]['Amt'] = round($v['Amt'],0);
            }
            $this->assign([
                'LayoutTitle' => $Title,
                'FacOrdSum' => jejuu($FacOrdSum),
                'CusOrdSum' => jejuu($CusOrdSum),
                'TaskOrdSum' => jejuu($TaskOrdSum),
            ]);
            $this->display();
        }else{
            $this->error('参数错误');
        }
    }

}
