<?php
namespace Wap1\Controller;
class FrecController extends BaseController{

    /********** 页面 **********/

    public function RecAdjust(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap1_User.rememberTab_'.$mca);
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $_PayType = D('Odbc')->query('SELECT ShortName FROM PayType WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND IsSell = 1 AND State = 1 ORDER BY IsPay','fetchAll');
        $PayType = ['全部'];
        foreach($_PayType as $v){
            $PayType[] = $v['ShortName'];
        }
        //dump($PayType);die;
        $this->assign([
            'LayoutTitle' => '收款调账',
            'rememberTab' => $rememberTab,
            'rememberForm' => jejuu($rememberForm),
            'PayType' => jejuu($PayType),
        ]);
        $this->display();
    }

    public function CusContact(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberForm = session('ERP_Wap1_User.rememberForm_'.$mca)?session('ERP_Wap1_User.rememberForm_'.$mca):[];
        $this->assign([
            'LayoutTitle' => '客户往来统计',
            'rememberForm' => jejuu($rememberForm),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    public function RecAdjust_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap1_User.rememberTab_'.$mca,$get['IsPay']);
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['RecAdjustMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['RecAdjustMaxDate'])));
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
        $where = [
            'p.FactoryId' => $this->config['FactoryId'],
            'p.IsSell' => 1,
            'p.State' => 1,
        ];
        if($get['IsPay'] === '1'){
            //收款
            $where['p.IsPay'] = '1';
        }else if($get['IsPay'] === '0'){
            //调账
            $where['p.IsPay'] = '0';
        }
        if($get['form']['CusId']){
            $where['p.CusId'] = $get['form']['CusId'];
        }
        if($this->TaskId){
            $where['c.TaskId'] = $this->TaskId;
        }else{
            if($get['form']['TaskId']){
                $where['c.TaskId'] = $get['form']['TaskId'];
            }
        }
        if($get['form']['DateType'] === 'OpDate'){
            $where['OpDate'] = ['between',[$get['form']['BeginDate'],$get['form']['EndDate']]];
        }else if($get['form']['DateType'] === 'IssueDate'){
            $where['IssueDate'] = ['between',[$get['form']['BeginDate'],$get['form']['EndDate']]];
        }
        $_PayType = D('Odbc')->query('SELECT ShortName FROM PayType WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND IsSell = 1 AND State = 1 ORDER BY IsPay','fetchAll');
        $PayType = [];
        foreach($_PayType as $v){
            $PayType[] = $v['ShortName'];
        }
        if(in_array($get['form']['PayType'],$PayType,true)){
            $where['t.ShortName'] = $get['form']['PayType'];
        }
        $data = M()->table('Payment')->alias('p')
            ->join('LEFT OUTER JOIN Customer c ON p.CusId = c.CusId')
            ->join('LEFT OUTER JOIN Worker s ON c.TaskId = s.TaskId')
            ->join('LEFT OUTER JOIN PayType t ON p.FactoryId = t.FactoryId AND p.PayTypeId = t.TypeId AND p.IsSell = t.IsSell AND p.IsPay = t.IsPay')
            ->where($where)
            ->field('p.*,c.CusShortName,t.ShortName,c.TaskId,s.TaskName')
            ->select();
        foreach ($data as $k => $v) {
            $data[$k]['Cus'] = $v['CusShortName'].'('.$v['CusId'].')';
            $data[$k]['Task'] = $v['TaskName'].'('.$v['TaskId'].')';
            $data[$k]['IssueDate'] = date('Y-m-d',strtotime($v['IssueDate']));
            $data[$k]['OpDate'] = date('Y-m-d',strtotime($v['OpDate']));
            $data[$k]['AddTime'] = date('Y-m-d H:i:s',strtotime($v['AddTime']));
            $data[$k]['CheckTime'] = date('Y-m-d H:i:s',strtotime($v['CheckTime']));
            $data[$k]['Amount'] = sprintf('%.3f',$v['Amount']);
            $data[$k]['InvoAmt'] = sprintf('%.3f',$v['InvoAmt']);
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'data' => $data,
        ]);
    }

    public function CusContact_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap1_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap1_User.rememberForm_'.$mca,null);
        }
        $where = [
            'a.FactoryId' => $this->config['FactoryId'],
        ];
        if($get['form']['CusId']){
            $where['a.CusId'] = $get['form']['CusId'];
        }
        if($this->TaskId){
            $where['a.TaskId'] = $this->TaskId;
        }else{
            if($get['form']['TaskId']){
                $where['a.TaskId'] = $get['form']['TaskId'];
            }
        }
        $data = M()->table('CustomerAmt')->alias('a')
            ->join('INNER JOIN Customer c ON a.CusId = c.CusId')
            ->where($where)
            ->field('a.*,c.PreAmt,CASE WHEN a.MinAmtCond > 0 THEN a.MinAmtCond - ISNULL(CurAmt,0) - ISNULL(OrdNeedAmt,0) ELSE 0 END LeftMinAmtCond,ISNULL(CurAmt,0) + ISNULL(OrdNeedAmt,0) CurNeedPay')
            ->order('CusId')
            ->select();
        foreach ($data as $k => $v) {
            $data[$k]['Cus'] = $v['CusShortName'].'('.$v['CusId'].')';
            $data[$k]['Task'] = $v['TaskName'].'('.$v['TaskId'].')';
            $data[$k]['LastMBDate'] = date('Y-m-d',strtotime($v['LastMBDate']));
            $data[$k]['LastAmt'] = sprintf('%.3f',$v['LastAmt']);
            $data[$k]['ConfAmt'] = sprintf('%.3f',$v['ConfAmt']);
            $data[$k]['AdjustAmt'] = sprintf('%.3f',$v['AdjustAmt']);
            $data[$k]['CusPayAmt'] = sprintf('%.3f',$v['CusPayAmt']);
            $data[$k]['OrdNeedAmt'] = sprintf('%.3f',$v['OrdNeedAmt']);
            $data[$k]['LastInvAmt'] = sprintf('%.3f',$v['LastInvAmt']);
            $data[$k]['InvAmt'] = sprintf('%.3f',$v['InvAmt']);
            $data[$k]['InvAdjustAmt'] = sprintf('%.3f',$v['InvAdjustAmt']);
            $data[$k]['ConfAmtT'] = sprintf('%.3f',$v['ConfAmtT']);
            $data[$k]['CurAmt'] = sprintf('%.2f',$v['CurAmt']);
            $data[$k]['ConfArea'] = sprintf('%.3f',$v['ConfArea']);
            $data[$k]['RAmt'] = sprintf('%.3f',$v['RAmt']);
            $data[$k]['DInvoAmt'] = sprintf('%.2f',$v['DInvoAmt']);
            $data[$k]['MinAmtCond'] = sprintf('%.2f',$v['MinAmtCond']);
            $data[$k]['PreAmt'] = sprintf('%.2f',$v['PreAmt']);
            $data[$k]['LeftMinAmtCond'] = sprintf('%.3f',$v['LeftMinAmtCond']);
            $data[$k]['CurNeedPay'] = sprintf('%.3f',$v['CurNeedPay']);
        }
        //dump($data);die;
        echo jejuu($data);
    }

    //客户Picker
    public function CusPicker_api(){
        $get = I('get.');
        $data = D('Cus')->getCusPicker($this->TaskId,$get['CusKeyword']);
        echo jejuu($data);
    }

}