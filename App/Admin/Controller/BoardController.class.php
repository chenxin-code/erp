<?php
namespace Admin\Controller;
//团购纸板管理
class BoardController extends BaseController{

    public function __construct(){
        parent::__construct();
        if(!$this->config['UseBoardGroup']){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                $this->error('纸板团购功能未开启');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '纸板团购功能未开启']);
            }
            die;
        }
    }

    public function lists(){
        $this->assign([
            'LayoutTitle' => '纸板列表',
        ]);
        $this->display();
    }

    public function lists_api(){
        $get = I('get.');
        $XU = $get['XU']?$get['XU']:'desc';
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'BoardId' => ['EXP','IS NOT NULL'],
            'MatNo' => ['EXP','IS NULL'],
            'IsDel' => 0,
        ];
        $time = time();
        if($get['State'] === 'ing'){
            $where['BeginTime'] = ['elt',$time];
            $where['EndTime'] = ['egt',$time];
        }elseif($get['State'] === 'tobe'){
            $where['BeginTime'] = ['egt',$time];
        }elseif($get['State'] === 'ed'){
            $where['EndTime'] = ['elt',$time];
        }
        if($get['IsFlag'] === '1'){
            $where['IsFlag'] = 1;
        }elseif($get['IsFlag'] === '0'){
            $where['IsFlag'] = 0;
        }
        $count = M()->table('WebProduct')->where($where)->count();
        $model = M()->table('WebProduct')->where($where)->order('Id '.$XU);
        if($get['CurPage'] && $get['PageSize']){
            $MaxPage = ceil($count/$get['PageSize']);
            if($MaxPage < 1){$MaxPage = 1;}
            $data = $model->page($get['CurPage'],$get['PageSize'])->select();
        }else{
            $MaxPage = '当前不是分页取数据该值无意义';
            $data = $model->select();
        }
        foreach ($data as $k => $v) {
            unset($data[$k]['ROW_NUMBER']);
            if($v['IsRangePrice']){
                $MinPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->min('Price');
                $MaxPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->max('Price');
                $data[$k]['Price'] = PriceFormat($MinPrice).'~'.PriceFormat($MaxPrice);
            }else{
                $data[$k]['Price'] = PriceFormat($v['Price']);
            }
            $data[$k]['MarketPrice'] = PriceFormat($v['MarketPrice']);
            $data[$k]['BuildMin'] = (float)$v['BuildMin'];
            $data[$k]['BuildMax'] = (float)$v['BuildMax'];
            $data[$k]['CusMax'] = (float)$v['CusMax'];
            $data[$k]['Total'] = (float)$v['Total'];
            $Sale = M()->table('WebappOrder')->alias('wo')
                ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                ->where(['WebProductId' => $v['Id']])
                ->sum('Area');
            $data[$k]['Sale'] = (float)$Sale;
            if($time < $v['BeginTime']){
                $data[$k]['State'] = 'tobe';
            }elseif($time >= $v['BeginTime'] && $time <= $v['EndTime']){
                $data[$k]['State'] = 'ing';
            }elseif($time > $v['EndTime']){
                $data[$k]['State'] = 'ed';
            }
            $data[$k]['BeginTime'] = date('Y-m-d H:i:s',$v['BeginTime']);
            $data[$k]['EndTime'] = date('Y-m-d H:i:s',$v['EndTime']);
            $data[$k]['AddTime'] = date('Y-m-d H:i:s',$v['AddTime']);
            $data[$k]['Pic'] = $v['Pic']?explode(',',$v['Pic']):[];
        }
        echo jejuu([
            'count' => $count,
            'MaxPage' => $MaxPage,
            'data' => $data,
        ]);
    }

    public function delLists(){
        $this->assign([
            'LayoutTitle' => '已删除纸板列表',
        ]);
        $this->display();
    }

    public function delLists_api(){
        $get = I('get.');
        $XU = $get['XU']?$get['XU']:'desc';
        $where = [
            'FactoryId' => $this->config['FactoryId'],
            'BoardId' => ['EXP','IS NOT NULL'],
            'MatNo' => ['EXP','IS NULL'],
            'IsDel' => 1,
        ];
        $count = M()->table('WebProduct')->where($where)->count();
        $model = M()->table('WebProduct')->where($where)->order('Id '.$XU);
        if($get['CurPage'] && $get['PageSize']){
            $MaxPage = ceil($count/$get['PageSize']);
            if($MaxPage < 1){$MaxPage = 1;}
            $data = $model->page($get['CurPage'],$get['PageSize'])->select();
        }else{
            $MaxPage = '当前不是分页取数据该值无意义';
            $data = $model->select();
        }
        foreach ($data as $k => $v) {
            unset($data[$k]['ROW_NUMBER']);
            if($v['IsRangePrice']){
                $MinPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->min('Price');
                $MaxPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->max('Price');
                $data[$k]['Price'] = PriceFormat($MinPrice).'~'.PriceFormat($MaxPrice);
            }else{
                $data[$k]['Price'] = PriceFormat($v['Price']);
            }
            $data[$k]['MarketPrice'] = PriceFormat($v['MarketPrice']);
            $data[$k]['BuildMin'] = (float)$v['BuildMin'];
            $data[$k]['BuildMax'] = (float)$v['BuildMax'];
            $data[$k]['CusMax'] = (float)$v['CusMax'];
            $data[$k]['Total'] = (float)$v['Total'];
            $Sale = M()->table('WebappOrder')->alias('wo')
                ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                ->where(['WebProductId' => $v['Id']])
                ->sum('Area');
            $data[$k]['Sale'] = (float)$Sale;
            $data[$k]['BeginTime'] = date('Y-m-d H:i:s',$v['BeginTime']);
            $data[$k]['EndTime'] = date('Y-m-d H:i:s',$v['EndTime']);
            $data[$k]['DelTime'] = date('Y-m-d H:i:s',$v['DelTime']);
            $data[$k]['Pic'] = $v['Pic']?explode(',',$v['Pic']):[];
        }
        echo jejuu([
            'count' => $count,
            'MaxPage' => $MaxPage,
            'data' => $data,
        ]);
    }

    public function add(){
        $BoardCodeSelect = M()->table('BoardCode')->where(['Stopped' => 0])->field('BoardId')->select();
        foreach($BoardCodeSelect as $k => $v){
            unset($BoardCodeSelect[$k]['ROW_NUMBER']);
        }
        $this->assign([
            'LayoutTitle' => '添加纸板',
            'BoardCodeSelect' => jejuu($BoardCodeSelect),
        ]);
        $this->display();
    }

    public function add_api(){
        $get = I('get.');
        $ProductData = [
            'FactoryId' => $this->config['FactoryId'],
            'BoardId' => $get['BoardId'],
            'Title' => $get['Title'],
            'MarketPrice' => $get['MarketPrice'],
            'BuildMin' => $get['BuildMin'],
            'BuildMax' => $get['BuildMax'],
            'CusMax' => $get['CusMax'],
            'Total' => $get['Total'],
            'BeginTime' => strtotime($get['BeginTime']),
            'EndTime' => strtotime($get['EndTime']),
            'AddTime' => time(),
            'IsFlag' => $get['IsFlag'],
            'IsDel' => 0,
        ];
        if($get['DefaultPic'] === '1'){
            $ProductData['Pic'] = $this->config['BoardDefaultPic'];
        }
        if($get['IsRangePrice']){
            $ProductData['IsRangePrice'] = 1;
            $ProductData['Price'] = NULL;
            M()->startTrans();
            $r2 = M()->table('WebProduct')->add($ProductData);
            if(!$r2){
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '添加失败',
                ]);die;
            }
            $RangePrice = $get['RangePrice']?$get['RangePrice']:[];
            array_unshift($RangePrice,['From' => $get['BuildMin'],'To' => $get['FirstTo'],'Price' => $get['FirstPrice']]);
            array_push($RangePrice,['From' => $get['LastFrom'],'To' => $get['BuildMax'],'Price' => $get['LastPrice']]);
            foreach($RangePrice as $k => $v){
                $RangePrice[$k]['WebProductId'] = $r2;
            }
            if(M()->table('WebProductRangePrice')->addAll($RangePrice)){
                M()->commit();
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '添加成功',
                ]);
            }else{
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '添加失败',
                ]);
            }
        }else{
            $ProductData['IsRangePrice'] = 0;
            $ProductData['Price'] = $get['Price'];
            if(M()->table('WebProduct')->add($ProductData)){
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '添加成功',
                ]);
            }else{
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '添加失败',
                ]);
            }
        }
    }

    public function edit(){
        $get = I('get.');
        $thisProductInfo = M()->table('WebProduct')
            ->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])
            ->field('BoardId,Title,IsRangePrice,Price,MarketPrice,BuildMin,BuildMax,CusMax,Total,BeginTime,EndTime,IsFlag,IsDel')
            ->find();
        if(!$thisProductInfo){
            $this->error('参数错误');die;
        }
        if($thisProductInfo['IsDel'] === '1'){
            $this->error('非法操作');die;
        }
        unset($thisProductInfo['ROW_NUMBER']);
        if($thisProductInfo['IsRangePrice'] === '1'){
            $RangePrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->field('From,To,Price')->select();
            $thisProductInfo['FirstTo'] = (float)$RangePrice[0]['To'];
            $thisProductInfo['FirstPrice'] = PriceFormat($RangePrice[0]['Price']);
            $thisProductInfo['LastFrom'] = (float)$RangePrice[count($RangePrice) - 1]['From'];
            $thisProductInfo['LastPrice'] = PriceFormat($RangePrice[count($RangePrice) - 1]['Price']);
            array_shift($RangePrice);
            array_pop($RangePrice);
            foreach($RangePrice as $k => $v){
                unset($RangePrice[$k]['ROW_NUMBER']);
                $RangePrice[$k]['From'] = (float)$v['From'];
                $RangePrice[$k]['To'] = (float)$v['To'];
                $RangePrice[$k]['Price'] = PriceFormat($v['Price']);
            }
        }else{
            $thisProductInfo['Price'] = PriceFormat($thisProductInfo['Price']);
            $RangePrice = [];
        }
        $thisProductInfo['RangePrice'] = jejuu($RangePrice);
        $thisProductInfo['MarketPrice'] = PriceFormat($thisProductInfo['MarketPrice']);
        $thisProductInfo['BuildMin'] = (float)$thisProductInfo['BuildMin'];
        $thisProductInfo['BuildMax'] = (float)$thisProductInfo['BuildMax'];
        $thisProductInfo['CusMax'] = (float)$thisProductInfo['CusMax'];
        $thisProductInfo['Total'] = (float)$thisProductInfo['Total'];
        $thisProductInfo['BeginTime'] = date('Y-m-d H:i:s',$thisProductInfo['BeginTime']);
        $thisProductInfo['EndTime'] = date('Y-m-d H:i:s',$thisProductInfo['EndTime']);
        $BoardCodeSelect = M()->table('BoardCode')->where(['Stopped' => 0])->field('BoardId')->select();
        foreach($BoardCodeSelect as $k => $v){
            unset($BoardCodeSelect[$k]['ROW_NUMBER']);
        }
        $this->assign([
            'LayoutTitle' => '产品基本信息',
            'thisProductInfo' => $thisProductInfo,
            'BoardCodeSelect' => jejuu($BoardCodeSelect),
        ]);
        $this->display();
    }

    public function edit_api(){
        $get = I('get.');
        $where = ['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']];
        $wp = M()->table('WebProduct')->where($where)->find();
        if(!$wp){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($wp['IsDel'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非法操作',
            ]);die;
        }
        $ProductData = [
            'BoardId' => $get['BoardId'],
            'Title' => $get['Title'],
            'MarketPrice' => $get['MarketPrice'],
            'BuildMin' => $get['BuildMin'],
            'BuildMax' => $get['BuildMax'],
            'CusMax' => $get['CusMax'],
            'Total' => $get['Total'],
            'BeginTime' => strtotime($get['BeginTime']),
            'EndTime' => strtotime($get['EndTime']),
            'IsFlag' => $get['IsFlag'],
        ];
        M()->startTrans();
        if(M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->delete() === FALSE){
            M()->rollback();
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '修改失败',
            ]);die;
        }
        if($get['IsRangePrice']){
            $ProductData['IsRangePrice'] = 1;
            $ProductData['Price'] = NULL;
            if(M()->table('WebProduct')->where($where)->save($ProductData) === FALSE){
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '修改失败',
                ]);die;
            }
            $RangePrice = $get['RangePrice']?$get['RangePrice']:[];
            array_unshift($RangePrice,['From' => $get['BuildMin'],'To' => $get['FirstTo'],'Price' => $get['FirstPrice']]);
            array_push($RangePrice,['From' => $get['LastFrom'],'To' => $get['BuildMax'],'Price' => $get['LastPrice']]);
            foreach($RangePrice as $k => $v){
                $RangePrice[$k]['WebProductId'] = $get['Id'];
            }
            if(M()->table('WebProductRangePrice')->addAll($RangePrice)){
                M()->commit();
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '修改成功',
                ]);
            }else{
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '修改失败',
                ]);
            }
        }else{
            $ProductData['IsRangePrice'] = 0;
            $ProductData['Price'] = $get['Price'];
            if(M()->table('WebProduct')->where($where)->save($ProductData) !== FALSE){
                M()->commit();
                echo jejuu([
                    'ret' => C('succ_ret'),
                    'msg' => '修改成功',
                ]);
            }else{
                M()->rollback();
                echo jejuu([
                    'ret' => C('fail_ret'),
                    'msg' => '修改失败',
                ]);
            }
        }
    }

    public function DefaultPic(){
        $this->assign([
            'LayoutTitle' => '纸板默认图片',
        ]);
        $this->display();
    }

    public function addDefaultPic_api(){
        $BoardDefaultPic = $this->config['BoardDefaultPic'];
        $r1 = D('Func')->upload_img('pic');
        if(!$r1['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r1['error'],
            ]);die;
        }
        //如果原来就有图片，则修改数据时需要保存之前的图片路径
        if($BoardDefaultPic){
            $BoardDefaultPic = $BoardDefaultPic.','.$r1['images'];
        }else{
            $BoardDefaultPic = $r1['images'];
        }
        $r2 = D('Func')->aosConfig('BoardDefaultPic',$BoardDefaultPic);
        if($r2){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '添加成功',
            ]);
        }else{
            //把已上传的图片删除
            unlink('./res/'.$r1['images']);
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '添加失败',
            ]);
        }
    }

    public function delDefaultPic_api(){
        $get = I('get.');
        $BoardDefaultPic = $this->config['BoardDefaultPic'];
        //通过下面3行代码去除Value字段中存有的图片
        $BoardDefaultPic = str_replace(','.$get['Pic'],'',$BoardDefaultPic);
        $BoardDefaultPic = str_replace($get['Pic'].',','',$BoardDefaultPic);
        $BoardDefaultPic = str_replace($get['Pic'],'',$BoardDefaultPic);
        $r = D('Func')->aosConfig('BoardDefaultPic',$BoardDefaultPic);
        if($r){
            //默认图片没有被使用才能删除
            if(!(M()->table('WebProduct')->where(['Pic' => ['like','%'.$get['Pic'].'%']])->select()) && !(M()->table('WebGroupOrder')->where(['FirstPic' => ['like','%'.$get['Pic'].'%']])->select())){
                unlink('./res/'.$get['Pic']);
            }
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

    public function showDefaultPic_api(){
        $BoardDefaultPic = $this->config['BoardDefaultPic'];
        $BoardDefaultPic = $BoardDefaultPic?explode(',',$BoardDefaultPic):[];
        echo jejuu($BoardDefaultPic);
    }

    public function Pic(){
        $get = I('get.');
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('IsDel')->find();
        if(!$wp){
            $this->error('参数错误');die;
        }
        if($wp['IsDel'] === '1'){
            $this->error('非法操作');die;
        }
        $this->assign([
            'LayoutTitle' => '产品图片',
        ]);
        $this->display();
    }

    public function addPic_api(){
        $get = I('get.');
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('Pic')->find();
        $r = D('Func')->upload_img('pic');
        if(!$r['bool']){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $r['error'],
            ]);die;
        }
        //如果原来就有图片，则修改数据时需要保存之前的图片路径
        if($wp['Pic']){
            $Pic = $wp['Pic'].','.$r['images'];
        }else{
            $Pic = $r['images'];
        }
        $r = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->save(['Pic' => $Pic]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '添加成功',
            ]);
        }else{
            //把已上传的图片删除
            unlink('./res/'.$r['images']);
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '添加失败',
            ]);
        }
    }

    public function delPic_api(){
        $get = I('get.');
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('Pic')->find();
        //通过下面3行代码去除Pic字段中存有的图片
        $NewPic = str_replace(','.$get['Pic'],'',$wp['Pic']);
        $NewPic = str_replace($get['Pic'].',','',$NewPic);
        $NewPic = str_replace($get['Pic'],'',$NewPic);
        $r = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->save(['Pic' => $NewPic]);
        if($r !== FALSE){
            $BoardDefaultPic = $this->config['BoardDefaultPic'];
            $BoardDefaultPic = $BoardDefaultPic?explode(',',$BoardDefaultPic):[];
            //不是默认图片且没被使用才能删除
            if(!in_array($get['Pic'],$BoardDefaultPic,true) && !(M()->table('WebGroupOrder')->where(['FirstPic' => ['like','%'.$get['Pic'].'%']])->select())){
                unlink('./res/'.$get['Pic']);
            }
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

    public function showPic_api(){
        //能进入Pic页面并调用该接口，说明Id肯定存在并且有意义
        $get = I('get.');
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('Pic')->find();
        $Pic = $wp['Pic']?explode(',',$wp['Pic']):[];
        echo jejuu($Pic);
    }

    public function Descr(){
        $get = I('get.');
        $wp = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->field('IsDel,Descr')->find();
        if(!$wp){
            $this->error('参数错误');die;
        }
        if($wp['IsDel'] === '1'){
            $this->error('非法操作');die;
        }
        $this->assign([
            'LayoutTitle' => '产品描述',
            'Descr' => $wp['Descr'],
        ]);
        $this->display();
    }

    public function saveDescr_api(){
        $r = M()->table('WebProduct')
            ->where(['FactoryId' => $this->config['FactoryId'],'Id' => $_POST['Id']])
            ->save(['Descr' => str_replace(["\r\n","\r","\n"],'',$_POST['Descr'])]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '保存成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '保存失败',
            ]);
        }
    }

    public function del_api(){
        $get = I('get.');
        $where = ['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']];
        $wp = M()->table('WebProduct')->where($where)->find();
        if(!$wp){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($wp['IsDel'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非法操作',
            ]);die;
        }
        $r = M()->table('WebProduct')->where($where)->save(['IsDel' => 1,'DelTime' => time()]);
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

    public function undel_api(){
        $get = I('get.');
        $where = ['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']];
        $wp = M()->table('WebProduct')->where($where)->find();
        if(!$wp){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($wp['IsDel'] === '0'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非法操作',
            ]);die;
        }
        $r = M()->table('WebProduct')->where($where)->save(['IsDel' => 0,'DelTime' => NULL]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '还原成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '还原失败',
            ]);
        }
    }

    public function changeFlag_api(){
        $get = I('get.');
        $where = ['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']];
        $wp = M()->table('WebProduct')->where($where)->field('IsFlag,IsDel')->find();
        if(!$wp){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '参数错误',
            ]);die;
        }
        if($wp['IsDel'] === '1'){
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '非法操作',
            ]);die;
        }
        if($wp['IsFlag'] === '0'){
            $IsFlag = '1';
            $msg = '设置“'.$this->config['BoardFlag'].'”';
        }else{
            $IsFlag = '0';
            $msg = '取消“'.$this->config['BoardFlag'].'”';
        }
        $r = M()->table('WebProduct')->where($where)->save(['IsFlag' => $IsFlag]);
        if($r !== FALSE){
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => $msg.'成功',
            ]);
        }else{
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => $msg.'失败',
            ]);
        }
    }

}
