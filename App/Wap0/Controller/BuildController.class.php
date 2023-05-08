<?php
namespace Wap0\Controller;
class BuildController extends BaseController{

    /********** 页面 **********/

    //简单纸板下单
    public function s(){
        $_BoardId = M()->table('WebappUsedBoard')->where(['CusId' => $this->ERPId])->field('BoardId')->select();
        //二维转一维再转字符串
        $BoardId = [];
        foreach($_BoardId as $v){
            $BoardId[] = $v['BoardId'];
        }
        $BoardId = implode(',',$BoardId);
        if($this->config['FactoryId'] === ''){
            $field = 'PaperName AS BoardName';
        }else{
            $field = 'BoardName';
        }
        $BoardCodeSelect = M()->table('BoardCode')->where(['BoardId' => ['IN',$BoardId]])->field('BoardId,'.$field.',1 AS IsUsedBoard')->select();
        foreach($BoardCodeSelect as $k => $v){
            unset($BoardCodeSelect[$k]['ROW_NUMBER']);
        }
        $ScoreNameSelect = $this->config['BuildScoreName']?explode(',',$this->config['BuildScoreName']):[];
        $CustomerDNSelect = M()->table('CustomerDN')->where(['CusId' => $this->ERPId])->field('CusSubNo,SubDNAddress')->select();
        foreach($CustomerDNSelect as $k => $v){
            unset($CustomerDNSelect[$k]['ROW_NUMBER']);
        }
        //是否为快速下单
        $get = I('get.');
        if(isset($get['CusPoNo'])){
            $sql = 'SELECT 
CAST(round(Length,0) AS INT) AS Length,
CAST(round(Width,0) AS INT) AS Width,
ScoreInfo,BoardId,BoardId AS BoardId_,CusSubNo,CusSubNo AS CusSubNo_,DNRemark,ProRemark FROM WebappOrder WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND CusId = \''.$this->ERPId.'\' AND CusPoNo = \''.$get['CusPoNo'].'\'';
            $FastBuildInfo = D('Odbc')->query($sql,'fetch');
            //如果快速下单的材质不在常用材质里，则要补上
            if(strpos($BoardId,$FastBuildInfo['BoardId']) === FALSE){
                $BoardCodeSelectPatch = M()->table('BoardCode')->where(['BoardId' => $FastBuildInfo['BoardId']])->field('BoardId,'.$field.',0 AS IsUsedBoard')->find();
                unset($BoardCodeSelectPatch['ROW_NUMBER']);
                array_unshift($BoardCodeSelect,$BoardCodeSelectPatch);//补在首位
            }
        }else{
            $FastBuildInfo = [];
        }
        $this->assign([
            'LayoutTitle' => '简单纸板下单',
            'BoardCodeSelect' => jejuu($BoardCodeSelect),
            'ScoreNameSelect' => jejuu($ScoreNameSelect),
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
            'FastBuildInfo' => jejuu($FastBuildInfo),
        ]);
        $this->display();
    }

    //纸箱纸板下单
    public function c(){
        $_BoardId = M()->table('WebappUsedBoard')->where(['CusId' => $this->ERPId])->field('BoardId')->select();
        //二维转一维再转字符串
        $BoardId = [];
        foreach($_BoardId as $v){
            $BoardId[] = $v['BoardId'];
        }
        $BoardId = implode(',',$BoardId);
        if($this->config['FactoryId'] === ''){
            $field = 'PaperName AS BoardName';
        }else{
            $field = 'BoardName';
        }
        $BoardCodeSelect = M()->table('BoardCode')->where(['BoardId' => ['IN',$BoardId]])->field('BoardId,'.$field.',1 AS IsUsedBoard')->select();
        foreach($BoardCodeSelect as $k => $v){
            unset($BoardCodeSelect[$k]['ROW_NUMBER']);
        }
        $CustomerDNSelect = M()->table('CustomerDN')->where(['CusId' => $this->ERPId])->field('CusSubNo,SubDNAddress')->select();
        foreach($CustomerDNSelect as $k => $v){
            unset($CustomerDNSelect[$k]['ROW_NUMBER']);
        }
        //$BoardCodeSelect = D('Odbc')->query('SELECT a.BoardId,b.BoardName FROM WebappUsedBoard a JOIN BoardCode b ON a.BoardId = b.BoardId WHERE a.CusId = \''.$this->ERPId.'\'','fetchAll');
        //$CustomerDNSelect = D('Odbc')->query('SELECT CusSubNo,SubDNAddress FROM CustomerDN WHERE CusId = \''.$this->ERPId.'\'','fetchAll');
        $BoxCodeSelect = D('Odbc')->query('SELECT BoxId,BoxName,LengthF,WidthF FROM BoxCode','fetchAll');
        //是否为快速下单
        $get = I('get.');
        if(isset($get['CusPoNo'])){
            $sql = 'SELECT BoardId,BoardId AS BoardId_,BoxId,BoxId AS BoxId_,
CAST(round(BoxL,0) AS INT) AS BoxL,
CAST(round(BoxW,0) AS INT) AS BoxW,
CAST(round(BoxH,0) AS INT) AS BoxH,
CAST(round(TonLen,0) AS INT) AS TonLen,CAST(round(TonLen,0) AS INT) AS TonLen_,
CAST(round(ULen,0) AS INT) AS ULen,CAST(round(ULen,0) AS INT) AS ULen_,
BdMultiple,CusSubNo,CusSubNo AS CusSubNo_,DNRemark,ProRemark FROM WebappOrder WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND CusId = \''.$this->ERPId.'\' AND CusPoNo = \''.$get['CusPoNo'].'\'';
            $FastBuildInfo = D('Odbc')->query($sql,'fetch');
            //如果快速下单的材质不在常用材质里，则要补上
            if(strpos($BoardId,$FastBuildInfo['BoardId']) === FALSE){
                $BoardCodeSelectPatch = M()->table('BoardCode')->where(['BoardId' => $FastBuildInfo['BoardId']])->field('BoardId,'.$field.',0 AS IsUsedBoard')->find();
                unset($BoardCodeSelectPatch['ROW_NUMBER']);
                array_unshift($BoardCodeSelect,$BoardCodeSelectPatch);//补在首位
            }
        }else{
            $FastBuildInfo = [];
        }
        $TonLenSelect = $this->config['BuildTonLen']?explode(',',$this->config['BuildTonLen']):[];
        $ULenSelect = $this->config['BuildULen']?explode(',',$this->config['BuildULen']):[];
        foreach ($TonLenSelect as $k => $v) {
            $TonLenSelect[$k] = sprintf('%.1f',$v);
        }
        foreach ($ULenSelect as $k => $v) {
            $ULenSelect[$k] = sprintf('%.1f',$v);
        }
        $this->assign([
            'LayoutTitle' => '纸箱纸板下单',
            'BoardCodeSelect' => jejuu($BoardCodeSelect),
            'BoxCodeSelect' => jejuu($BoxCodeSelect),
            'TonLenSelect' => jejuu($TonLenSelect),
            'ULenSelect' => jejuu($ULenSelect),
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
            'FastBuildInfo' => jejuu($FastBuildInfo),
        ]);
        $this->display();
    }

    //纸箱下单
    public function x(){
        $BoxSetMainSelect = D('Odbc')->query('SELECT ProductId,ProductName FROM BoxSetMain WHERE CusId = \''.$this->ERPId.'\'','fetchAll');
        $CustomerDNSelect = D('Odbc')->query('SELECT CusSubNo,SubDNAddress FROM CustomerDN WHERE CusId = \''.$this->ERPId.'\'','fetchAll');
        //是否为快速下单
        $get = I('get.');
        if(isset($get['CusPoNo'])){
            $sql = 'SELECT PON,ProductId,ProductId AS ProductId_,CusSubNo,CusSubNo AS CusSubNo_,DNRemark,ProRemark FROM WebappOrder WHERE FactoryId = \''.$this->config['FactoryId'].'\' AND CusId = \''.$this->ERPId.'\' AND CusPoNo = \''.$get['CusPoNo'].'\'';
            $FastBuildInfo = D('Odbc')->query($sql,'fetch');
        }else{
            $FastBuildInfo = [];
        }
        $this->assign([
            'LayoutTitle' => '纸箱下单',
            'BoxSetMainSelect' => jejuu($BoxSetMainSelect),
            'CustomerDNSelect' => jejuu($CustomerDNSelect),
            'FastBuildInfo' => jejuu($FastBuildInfo),
        ]);
        $this->display();
    }


    /********** 接口 **********/

    //简单纸板下单
    public function s_api(){
        $post = I('post.');
        $r1 = D('Bcheck')->s($post);
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        $BuildScoreName = $this->config['BuildScoreName']?explode(',',$this->config['BuildScoreName']):[];
        $key = array_search($post['ScoreName'],$BuildScoreName,true);
        $time = time();
        $BdQty = $post['OrdQty'];//简单纸板下单的纸板数=订单数
        $Area = $post['Length'] * $post['Width'] * $BdQty / 1000000;
        try {
            $r3 = M()->table('WebappOrder')->add([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $r2['CusPoNo'],
                'CType' => 's',
                'BoardId' => $post['BoardId'],
                'Length' => $post['Length'],
                'Width' => $post['Width'],
                'ScoreType' => $key,
                'ScoreInfo' => $key === 0?NULL:$post['ScoreInfo'],
                'OrdQty' => $post['OrdQty'],
                'BdQty' => $BdQty,
                'Area' => $Area,
                'DeliveryDate' => $post['DeliveryDate'],
                'CusSubNo' => $post['CusSubNo'],
                'DNRemark' => $post['DNRemark'],
                'ProRemark' => $post['ProRemark'],
                'BuildDate' => date('Y-m-d',$time),
                'BuildTime' => date('Y-m-d H:i:s',$time),
                'IsDel' => 0,
                'Checked' => 0,
                'IsCard' => 0,
                'IsGroup' => 0,
            ]);
            //dump($r3);die;
            if($r3){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '下单成功',
                    //'Id' => $r3,
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '下单失败',
                ]);
            }
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);
        }
    }
    public function bcheck_s_api(){
        $post = I('post.');
        $r1 = D('Bcheck')->s($post);
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        echo jejuu(['ret' => C('succ_ret')]);
    }

    //纸箱纸板下单
    public function c_api(){
        $post = I('post.');
        $r1 = D('Bcheck')->c($post);
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        $time = time();
        $Area = $post['Length'] * $post['Width'] * $post['BdQty'] / 1000000;
        try {
            $r3 = M()->table('WebappOrder')->add([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $r2['CusPoNo'],
                'CType' => 'c',
                'BoardId' => $post['BoardId'],
                'Length' => $post['Length'],
                'Width' => $post['Width'],
                'OrdQty' => $post['OrdQty'],
                'BdQty' => $post['BdQty'],
                'BdMultiple' => $post['BdMultiple'],
                'BoxId' => $post['BoxId'],
                'BoxL' => $post['BoxL'],
                'BoxW' => $post['BoxW'],
                'BoxH' => $post['BoxH'],
                'TonLen' => $post['TonLen'],
                'ULen' => $post['ULen'],
                'Area' => $Area,
                'DeliveryDate' => $post['DeliveryDate'],
                'CusSubNo' => $post['CusSubNo'],
                'DNRemark' => $post['DNRemark'],
                'ProRemark' => $post['ProRemark'],
                'BuildDate' => date('Y-m-d',$time),
                'BuildTime' => date('Y-m-d H:i:s',$time),
                'IsDel' => 0,
                'Checked' => 0,
                'IsCard' => 0,
                'IsGroup' => 0,
            ]);
            //dump($r3);die;
            if($r3){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '下单成功',
                    //'Id' => $r3,
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '下单失败',
                ]);
            }
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);
        }
    }
    public function bcheck_c_api(){
        $post = I('post.');
        $r1 = D('Bcheck')->c($post);
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        echo jejuu(['ret' => C('succ_ret')]);
    }

    //纸箱下单
    public function x_api(){
        $post = I('post.');
        $r1 = D('Bcheck')->x($post);
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        $time = time();
        try {
            $r3 = M()->table('WebappOrder')->add([
                'FactoryId' => $this->config['FactoryId'],
                'CusId' => $this->ERPId,
                'CusPoNo' => $r2['CusPoNo'],
                'CType' => 'x',
                'PON' => $post['PON'],
                'ProductId' => $post['ProductId'],
                'OrdQty' => $post['OrdQty'],
                'DeliveryDate' => $post['DeliveryDate'],
                'CusSubNo' => $post['CusSubNo'],
                'DNRemark' => $post['DNRemark'],
                'ProRemark' => $post['ProRemark'],
                'BuildDate' => date('Y-m-d',$time),
                'BuildTime' => date('Y-m-d H:i:s',$time),
                'IsDel' => 0,
                'Checked' => 0,
                'IsCard' => 0,
                'IsGroup' => 0,
            ]);
            //dump($r3);die;
            if($r3){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '下单成功',
                    //'Id' => $r3,
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '下单失败',
                ]);
            }
        } catch(\Exception $e){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $e->getMessage(),
            ]);
        }
    }
    public function bcheck_x_api(){
        $post = I('post.');
        $r1 = D('Bcheck')->x($post);
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['reason'],
            ]);die;
        }
        $r2 = D('Build')->makeCusPoNo($post['CusPoNo'],$this->ERPId);
        if(!$r2['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r2['reason'],
            ]);die;
        }
        echo jejuu(['ret' => C('succ_ret')]);
    }

    //根据箱型获取公式
    public function getFormula_api(){
        $data = M()->table('BoxCode')->where(['BoxId' => $_GET['BoxId']])->field('LengthF,WidthF,Multiple')->find();
        unset($data['ROW_NUMBER']);
        echo jejuu($data);
    }

    public function calcArea_api(){
        $get = I('get.');
        //根据是否提交了纸板数，判断区分简单纸板下单/纸箱纸板下单
        if($get['BdQty']){
            $BdQty = $get['BdQty'];//纸箱纸板下单
        }else{
            $BdQty = $get['OrdQty'];//简单纸板下单（纸板数=订单数）
        }
        $Area = $get['Length'] * $get['Width'] * $BdQty / 1000000;
        if($Area < $this->config['BuildMinArea'] || $Area > $this->config['BuildMaxArea']){
            echo jejuu([
                'Area' => $Area,
                'validArea' => 0,
            ]);
        }else{
            echo jejuu([
                'Area' => $Area,
                'validArea' => 1,
            ]);
        }
    }

    //纸箱纸板下单改变材质时自动获取箱舌和封箱调整
    public function AutoGetTonLenAndULen_api(){
        //$sql = 'SELECT f.TonLen,f.ULen FROM FluteRate f INNER JOIN BoardCode b ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = f.Flute AND b.LayerCount = f.LayerCount WHERE b.BoardId = \''.$_GET['BoardId'].'\'';
        $sql = 'SELECT ISNULL(c.TonLen,f.TonLen) AS TonLen,ISNULL(c.ULen,f.ULen) AS ULen FROM FluteRate f 
INNER JOIN BoardCode b ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = f.Flute AND b.LayerCount = f.LayerCount 
LEFT OUTER JOIN CusSpec1 c ON b.Flute1 + b.Flute2 + b.Flute3 + b.Flute4 = c.Flutes AND b.LayerCount = c.LayerCount AND c.CusId = \''.$this->ERPId.'\' WHERE b.BoardId = \''.$_GET['BoardId'].'\'';
        $data = D('Odbc')->query($sql,'fetch');
        //dump($data);die;
        echo jejuu([
            'TonLen' => $data['TonLen']?sprintf('%.1f',$data['TonLen']):null,
            'ULen' => $data['ULen']?sprintf('%.1f',$data['ULen']):null,
        ]);
    }

}
