<?php
namespace Wap0\Controller;
//微信订单
class WeborderController extends BaseController{

    /********** 页面 **********/

    public function lists(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap0_User.rememberTab_'.$mca);
        $rememberForm = session('ERP_Wap0_User.rememberForm_'.$mca)?session('ERP_Wap0_User.rememberForm_'.$mca):[];
        $WeborderDefaultDelRemark = [];
        $_WeborderDefaultDelRemark = $this->config['WeborderDefaultDelRemark']?explode(',',$this->config['WeborderDefaultDelRemark']):[];
        foreach($_WeborderDefaultDelRemark as $k => $v){
            $WeborderDefaultDelRemark[$k] = ['DelRemark' => $v];
        }
        $this->assign([
            'LayoutTitle' => '微信订单',
            'rememberTab' => $rememberTab,
            'rememberForm' => jejuu($rememberForm),
            'WeborderDefaultDelRemark' => jejuu($WeborderDefaultDelRemark),
        ]);
        $this->display();
    }

    /********** 接口 **********/

    public function lists_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap0_User.rememberTab_'.$mca,$get['State']);
        if($get['form']['rememberForm'] === 'yes'){
            session('ERP_Wap0_User.rememberForm_'.$mca,$get['form']);
        }elseif($get['form']['rememberForm'] === 'no'){
            session('ERP_Wap0_User.rememberForm_'.$mca,null);
        }
        $MinTime = strtotime(date('Y-m-d',strtotime($this->config['WeborderMinDate'])));
        $MaxTime = strtotime(date('Y-m-d',strtotime($this->config['WeborderMaxDate'])));
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
        $where1 = [
            'wo.FactoryId' => $this->config['FactoryId'],
            'wo.CusId' => $this->ERPId,
        ];
        if($get['form']['CType']){
            $where1['wo.CType'] = $get['form']['CType'];
        }
        if($get['form']['IsGroup'] === '1'){
            $where1['wo.IsGroup'] = 1;
        }elseif($get['form']['IsGroup'] === '0'){
            $where1['wo.IsGroup'] = 0;
        }
        if($get['form']['DateType'] === 'BuildDate'){
            $where1['wo.BuildDate'] = ['between',[$get['form']['BeginDate'],$get['form']['EndDate']]];
        }else if($get['form']['DateType'] === 'DeliveryDate'){
            $where1['wo.DeliveryDate'] = ['between',[$get['form']['BeginDate'],$get['form']['EndDate']]];
        }
        $Paid0Order = [];
        if($get['State'] === '1'){
            //未审核
            $where1['wo.IsDel'] = 0;
            $where1['wo.Checked'] = 0;
            //获取未付款团购订单
            $where2 = [
                'wo.FactoryId' => $this->config['FactoryId'],
                'wo.CusId' => $this->ERPId,
                'wo.IsDel' => 0,
                'wo.Checked' => 0,
                'wp.Paid' => 0,
            ];
            if($get['form']['CType']){
                $where2['wo.CType'] = $get['form']['CType'];
            }
            if($get['form']['IsGroup'] === '1'){
                $where2['wo.IsGroup'] = 1;
            }elseif($get['form']['IsGroup'] === '0'){
                $where2['wo.IsGroup'] = 0;
            }
            if($get['form']['DateType'] === 'BuildDate'){
                $where2['wo.BuildDate'] = ['between',[$get['form']['BeginDate'],$get['form']['EndDate']]];
            }else if($get['form']['DateType'] === 'DeliveryDate'){
                $where2['wo.DeliveryDate'] = ['between',[$get['form']['BeginDate'],$get['form']['EndDate']]];
            }
            try {
                $Paid0Order = M()->table('WebappOrder')->alias('wo')
                    ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                    ->join('LEFT OUTER JOIN WebPay wp ON wo.FactoryId = wp.FactoryId AND wo.CusId = wp.CusId AND wo.CusPoNo = wp.CusPoNo')
                    ->where($where2)
                    ->field('wo.CusPoNo,wo.Id,wo.BoardId,wo.MatNo,wgo.Title,wgo.FirstPic,wgo.Cost,wp.PayDeadlineTime')
                    ->order('Id desc')
                    ->select();
            } catch(\Exception $e){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => $e->getMessage(),
                ]);die;
            }
            foreach($Paid0Order as $k => $v){
                unset($Paid0Order[$k]['ROW_NUMBER']);
                unset($Paid0Order[$k]['Id']);
                if(!$v['FirstPic']){
                    $Paid0Order[$k]['FirstPic'] = C('ProductNoPicDefaultPic');
                }
                $Paid0Order[$k]['Cost'] = sprintf('%.2f',$v['Cost']);
            }
        }else if($get['State'] === '2'){
            //已审核
            $where1['wo.IsDel'] = 0;
            $where1['wo.Checked'] = 1;
        }else if($get['State'] === '3'){
            //已删除
            $where1['wo.IsDel'] = 1;
        }
        try {
            $count = M()->table('WebappOrder')->alias('wo')->where($where1)->count();
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);die;
        }
        if($this->config['FactoryId'] === ''){
            $on = '';
        }else{
            $on = 'wo.FactoryId = bsm.FactoryId AND ';
        }
        $model = M()->table('WebappOrder')->alias('wo')
            ->join('LEFT OUTER JOIN CustomerDN cdn ON wo.CusId = cdn.CusId AND wo.CusSubNo = cdn.CusSubNo')
            ->join('LEFT OUTER JOIN BoxSetMain bsm ON '.$on.'wo.CusId = bsm.CusId AND wo.ProductId = bsm.ProductId')
            ->join('LEFT OUTER JOIN BoxCode bc ON wo.BoxId = bc.BoxId')
            ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
            ->join('LEFT OUTER JOIN WebPay wp ON wo.FactoryId = wp.FactoryId AND wo.CusId = wp.CusId AND wo.CusPoNo = wp.CusPoNo')
            ->where($where1)
            ->field('wo.CusPoNo,wo.BoardId,wo.ProductId,wo.MatNo,wo.Id,wo.OrdQty,wo.CType,wo.DeliveryDate,wo.BuildDate,wo.IsDel,wo.DelRemark,wo.Checked,wo.IsCard,wo.CardFlag,wo.IsGroup,
            wo.Length,wo.Width,wo.BoxL,wo.BoxW,wo.BoxH,wo.Area,
            cdn.SubDNAddress,bsm.ProductName,bc.BoxName,wgo.WebProductId,wgo.UsePay,wgo.Title,wgo.FirstPic,wp.PayDeadlineTime,wp.Paid,wp.Apply,wp.Refund')
            ->order('Id desc');
        if($get['CurPage'] && $get['PageSize']){
            $MaxPage = ceil($count/$get['PageSize']);
            if($MaxPage < 1){$MaxPage = 1;}
            try {
                $data = $model->page($get['CurPage'],$get['PageSize'])->select();
            } catch(\Exception $e){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => $e->getMessage(),
                ]);die;
            }
        }else{
            $MaxPage = '当前不是分页取数据该值无意义';
            try {
                $data = $model->select();
            } catch(\Exception $e){
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => $e->getMessage(),
                ]);die;
            }
        }
        foreach($data as $k => $v){
            unset($data[$k]['ROW_NUMBER']);
            unset($data[$k]['Id']);
            $data[$k]['CTypeName'] = getCTypeName($v['CType']);
            $data[$k]['DeliveryDate'] = date('Y-m-d',strtotime($v['DeliveryDate']));
            $data[$k]['BuildDate'] = date('Y-m-d',strtotime($v['BuildDate']));
            if(!$v['FirstPic']){
                $data[$k]['FirstPic'] = C('ProductNoPicDefaultPic');
            }
            if($v['Length']){
                $data[$k]['Length'] = (float)$v['Length'];
            }
            if($v['Width']){
                $data[$k]['Width'] = (float)$v['Width'];
            }
            if($v['BoxL']){
                $data[$k]['BoxL'] = (float)$v['BoxL'];
            }
            if($v['BoxW']){
                $data[$k]['BoxW'] = (float)$v['BoxW'];
            }
            if($v['BoxH']){
                $data[$k]['BoxH'] = (float)$v['BoxH'];
            }
        }
        //dump($data);die;
        echo jejuu([
            'ret' => C('succ_ret'),
            'count' => $count,
            'MaxPage' => $MaxPage,
            'data' => $data,
            'Paid0Order' => $Paid0Order,
            'time' => time(),
        ]);
    }

    //设为常用订单
    public function used_api(){
        $get = I('get.');
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $this->ERPId,
            'CusPoNo' => $get['CusPoNo'],
        ];
        $wo = M()->table('WebappOrder')->where($where)->field('Checked,IsCard,CType')->find();
        if($wo['Checked'] === '0'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单未审核',
            ]);die;
        }
        if($wo['IsCard'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单已设为常用',
            ]);die;
        }
        if($wo['CType'] === 't'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '淘宝箱订单不能设为常用',
            ]);die;
        }
        $r = M()->table('WebappOrder')->where($where)->save(['IsCard' => 1,'CardFlag' => $get['CardFlag']]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '设置成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '设置失败',
            ]);
        }
    }

    //删除订单
    //确保erp删除逻辑一致
    public function delete_api(){
        $get = I('get.');
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'CusId' => $this->ERPId,
            'CusPoNo' => $get['CusPoNo'],
        ];
        $wo = M()->table('WebappOrder')->where($where)->field('IsDel,Checked,IsGroup')->find();
        if($wo['IsDel'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单已删除',
            ]);die;
        }
        if($wo['Checked'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '订单已审核',
            ]);die;
        }
        if($wo['IsGroup'] === '1'){
            $wgo = M()->table('WebGroupOrder')->where($where)->field('UsePay')->find();
            if($wgo['UsePay'] === '1'){
                $wp = M()->table('WebPay')->where($where)->field('Paid,Refund')->find();
                if($wp['Paid'] === '1' && $wp['Refund'] === '0'){
                    echo jejuu([
                        'ret' => C('fail_ret'),
                        'msg' => '当前团购支付订单状态不允许删除',
                    ]);die;
                }
            }
        }
        $r = M()->table('WebappOrder')->where($where)->save(['IsDel' => 1,'DelTime' => date('Y-m-d H:i:s',time()),'DelRemark' => $get['DelRemark']]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '删除成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '删除失败',
            ]);
        }
    }

    public function detail_api(){
        $get = I('get.');
        $data = M()->table('WebappOrder')->where(['FactoryId' => $this->config['FactoryId'],'CusId' => $this->ERPId,'CusPoNo' => $get['CusPoNo']])->find();
        unset($data['ROW_NUMBER']);
        $data['BoxL'] = floatval($data['BoxL']);
        $data['BoxW'] = floatval($data['BoxW']);
        $data['BoxH'] = floatval($data['BoxH']);
        $data['TonLen'] = floatval($data['TonLen']);
        $data['ULen'] = floatval($data['ULen']);
        $data['Length'] = floatval($data['Length']);
        $data['Width'] = floatval($data['Width']);
        $data['CTypeName'] = getCTypeName($data['CType']);
        if($data['CType'] === 's'){
            $BuildScoreName = $this->config['BuildScoreName']?explode(',',$this->config['BuildScoreName']):[];
            $data['ScoreName'] = $BuildScoreName[(int)$data['ScoreType']];
        }elseif($data['CType'] === 'c'){
            $data['BoxName'] = M()->table('BoxCode')->where(['BoxId' => $data['BoxId']])->getField('BoxName');
        }
        $data['SubDNAddress'] = M()->table('CustomerDN')->where(['CusId' => $this->ERPId,'CusSubNo' => $data['CusSubNo']])->getField('SubDNAddress');
        $data['DeliveryDate'] = date('Y-m-d',strtotime($data['DeliveryDate']));
        $data['BuildTime'] = date('Y-m-d H:i:s',strtotime($data['BuildTime']));
        echo jejuu($data);
    }
    public function detail2_api(){
        $get = I('get.');
        $wo = M()->table('WebappOrder')
            ->where(['FactoryId' => $this->config['FactoryId'],'CusId' => $this->ERPId,'CusPoNo' => $get['CusPoNo']])
            ->field('IsGroup,BoardId,MatNo')
            ->find();
        if($wo['IsGroup'] === '1'){
            $wgo = M()->table('WebGroupOrder')
                ->where(['FactoryId' => $this->config['FactoryId'],'CusId' => $this->ERPId,'CusPoNo' => $get['CusPoNo']])
                ->field('WebProductId,Title,FirstPic,Price,MarketPrice,Cost,SaveCost')
                ->find();
            unset($wgo['ROW_NUMBER']);
            if(!$wgo['FirstPic']){
                $wgo['FirstPic'] = C('ProductNoPicDefaultPic');
            }
            $wgo['Price'] = PriceFormat($wgo['Price']);
            $wgo['MarketPrice'] = PriceFormat($wgo['MarketPrice']);
            $wgo['Cost'] = sprintf('%.2f',$wgo['Cost']);
            $wgo['SaveCost'] = sprintf('%.2f',$wgo['SaveCost']);
            $data = array_merge(['BoardId' => $wo['BoardId'],'MatNo' => $wo['MatNo']],$wgo);
            echo jejuu([
                'ret' => C('succ_ret'),
                'data' => $data,
            ]);
        }else{
            echo jejuu(['ret' => C('fail_ret')]);
        }
    }

}
