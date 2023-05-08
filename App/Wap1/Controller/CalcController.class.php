<?php
namespace Wap1\Controller;
//订单试算
class CalcController extends BaseController{

    /********** 页面 **********/

    public function index(){
        $this->assign([
            'LayoutTitle' => '订单试算',
        ]);
        $this->display();
    }

    /********** 接口 **********/

    //客户Picker
    public function CusPicker_api(){
        $get = I('get.');
        $data = D('Cus')->getCusPicker($this->TaskId,$get['CusKeyword']);
        echo jejuu($data);
    }

    //材质Picker
    public function BoardPicker_api(){
        $get = I('get.');
        $where = [];
        if($this->config['FactoryId'] === ''){
            $field1 = 'PaperName';
            $field2 = 'PaperName AS BoardName';
        }else{
            $field1 = 'BoardName';
            $field2 = 'BoardName';
        }
        if($get['BoardKeyword']){
            $where['BoardId|'.$field1] = ['like','%'.$get['BoardKeyword'].'%'];
        }
        $data = M()->table('BoardCode')->where($where)->field('BoardId,'.$field2)->select();
        foreach($data as $k => $v){
            unset($data[$k]['ROW_NUMBER']);
        }
        echo jejuu($data);
    }

    //箱型Picker
    public function BoxPicker_api(){
        $get = I('get.');
        $where = [];
        if($get['BoxKeyword']){
            $where['BoxId|BoxName'] = ['like','%'.$get['BoxKeyword'].'%'];
        }
        $data = M()->table('BoxCode')->where($where)->field('BoxId,BoxName')->select();
        foreach($data as $k => $v){
            unset($data[$k]['ROW_NUMBER']);
        }
        echo jejuu($data);
    }

    //自动获取客户是否默认加修边＆加面积
    public function AutoGetTrimAndAreaByCus_api(){
        $get = I('get.');
        $data = M()->table('Customer')->where(['CusId' => $get['CusId']])->field('SaAreaAddTrim,SaAreaAddArea')->find();
        unset($data['ROW_NUMBER']);
        echo jejuu($data);
    }

    //自动获取默认的箱舌＆封箱调整
    public function AutoGetTonLenAndULen_api(){
        $get = I('get.');
        $sql = 'SELECT ISNULL(c.TonLen,f.TonLen) AS TonLen,ISNULL(c.ULen,f.ULen) AS ULen FROM FluteRate f 
INNER JOIN BoardCode b ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = f.Flute AND b.LayerCount = f.LayerCount 
LEFT OUTER JOIN CusSpec1 c ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = c.Flutes AND b.LayerCount = c.LayerCount AND c.CusId = \''.$get['CusId'].'\' WHERE b.BoardId = \''.$get['BoardId'].'\'';
        $data = D('Odbc')->query($sql,'fetch');
        echo jejuu([
            'TonLen' => sprintf('%.1f',$data['TonLen']),
            'ULen' => sprintf('%.1f',$data['ULen']),
        ]);
    }

}