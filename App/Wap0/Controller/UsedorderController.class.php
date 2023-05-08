<?php
namespace Wap0\Controller;
//常用订单
class UsedorderController extends BaseController{

    /********** 页面 **********/

    //常用订单
    public function lists(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('ERP_Wap0_User.rememberTab_'.$mca);
        $this->assign([
            'LayoutTitle' => '常用订单',
            'rememberTab' => $rememberTab,
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //常用订单
    public function lists_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('ERP_Wap0_User.rememberTab_'.$mca,$get['CType']);
        $where = [
            'a.FactoryId' => $this->config['FactoryId'],
            'a.CusId' => $this->ERPId,
            'a.IsCard' => 1,
            'a.CType' => $get['CType'],
        ];
        $count = M()->table('WebappOrder')->alias('a')->where($where)->count();
        if($this->config['FactoryId'] === ''){
            $on1 = '';
            $on2 = '';
        }else{
            $on1 = 'a.FactoryId = c.FactoryId AND ';
            $on2 = 'a.FactoryId = d.FactoryId AND ';
        }
        $model = M()->table('WebappOrder')->alias('a')
            ->join('LEFT OUTER JOIN CustomerDN b ON a.CusId = b.CusId AND a.CusSubNo = b.CusSubNo')
            ->join('LEFT OUTER JOIN BoxSetMain c ON '.$on1.'a.CusId = c.CusId AND a.ProductId = c.ProductId')
            ->join('LEFT OUTER JOIN BoxCode d ON '.$on2.'a.BoxId = d.BoxId')
            ->where($where)
            ->field('a.CusPoNo,a.BoardId,a.CardFlag,a.OrdQty,a.BuildDate,a.DeliveryDate,a.CType,a.Id,
            a.Length,a.Width,a.BoxL,a.BoxW,a.BoxH,
            b.SubDNAddress,c.ProductName,d.BoxName')
            ->order('Id desc');
        if($get['CurPage'] && $get['PageSize']){
            $MaxPage = ceil($count/$get['PageSize']);
            if($MaxPage < 1){$MaxPage = 1;}
            $data = $model->page($get['CurPage'],$get['PageSize'])->select();
        }else{
            $MaxPage = '当前不是分页取数据该值无意义';
            $data = $model->select();
        }
        foreach($data as $k => $v){
            unset($data[$k]['ROW_NUMBER']);
            unset($data[$k]['Id']);
            $data[$k]['DeliveryDate'] = date('Y-m-d',strtotime($v['DeliveryDate']));
            $data[$k]['BuildDate'] = date('Y-m-d',strtotime($v['BuildDate']));
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
            'count' => $count,
            'MaxPage' => $MaxPage,
            'data' => $data,
        ]);
    }

    //取消常用
    public function cancel_api(){
        $get = I('get.');
        $r = M()->table('WebappOrder')
            ->where(['FactoryId' => $this->config['FactoryId'],'CusId' => $this->ERPId,'CusPoNo' => $get['CusPoNo']])
            ->save(['IsCard' => 0,'CardFlag' => NULL]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '取消成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '取消失败',
            ]);
        }
    }

}
