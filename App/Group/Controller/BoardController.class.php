<?php
namespace Group\Controller;
use Think\Controller;
class BoardController extends Controller{

    var $config;

    public function __construct(){
        parent::__construct();
        $this->config = D('Func')->getConfig();
        if(session('ERP_Wap0_User')){
            $ERPId = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap0_User.UserName')])->getField('ERPId');
            $CusShortName = M()->table('Customer')->where(['CusId' => $ERPId])->getField('CusShortName');
            $this->assign('HeaderFlag',$CusShortName.'（'.$ERPId.'）');
        }
        $this->assign('config',$this->config);
        if(!$this->config['UseBoardGroup']){
            if(strpos(ACTION_NAME,'_api') === FALSE){
                $this->error('纸板团购功能未开启');
            }else{
                echo jejuu(['ret' => C('fail_ret'),'msg' => '纸板团购功能未开启']);
            }
            die;
        }
        //跳转到非80端口访问
        if(!$this->config['Open80Port'] && $_SERVER['SERVER_NAME'] === $this->config['Frp80PortDomain']){
            redirect(str_replace($this->config['OriDomain'],$this->config['OriDomain'].':'.$this->config['OriPort'],U(CONTROLLER_NAME.'/'.ACTION_NAME.'@'.$this->config['OriDomain'],$_GET)),0,'');die;
        }
    }

    public function lists(){
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.'_api'));
        $rememberTab = session('rememberTab_'.$mca);
        $this->assign([
            'LayoutTitle' => '纸板团购',
            'rememberTab' => $rememberTab,
        ]);
        $this->display();
    }

    //获取筛选项接口
    public function getFilterSelect_api(){
        $get = I('get.');
        $FilterSelect = ['全部'];
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
        if(M()->table('WebProduct')->where(array_merge($where,['IsFlag' => 1]))->select()){
            $FilterSelect[] = $this->config['BoardFlag'];
        }
        $_FilterSelect = M()->table('WebProduct')
            ->where($where)
            ->field('DISTINCT SUBSTRING(BoardId,CHARINDEX(\'/\',BoardId) + 1,LEN(BoardId) - CHARINDEX(\'/\',BoardId)) Flutes')
            ->select();
        foreach($_FilterSelect as $v){
            $FilterSelect[] = $v['Flutes'];
        }
        echo jejuu($FilterSelect);
    }

    public function lists_api(){
        $get = I('get.');
        $mca = md5(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME));
        session('rememberTab_'.$mca,$get['State']);
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
        if($get['Filter'] === '全部'){

        }elseif($get['Filter'] === $this->config['BoardFlag']){
            $where['IsFlag'] = 1;
        }else{
            $where['BoardId'] = ['like','%/'.$get['Filter']];
        }
        $count = M()->table('WebProduct')->where($where)->count();
        $model = M()->table('WebProduct')->where($where)->field('Id,BoardId,Title,IsRangePrice,Price,MarketPrice,Total,IsFlag,Pic,BeginTime,EndTime')->order('Id desc');
        if($get['CurPage'] && $get['PageSize']){
            $MaxPage = ceil($count/$get['PageSize']);
            if($MaxPage < 1){$MaxPage = 1;}
            $data = $model->page($get['CurPage'],$get['PageSize'])->select();
        }else{
            $MaxPage = '当前不是分页取数据该值无意义';
            $data = $model->select();
        }
        /*$sql = 'SELECT * FROM WebProduct WHERE FactoryId = \''.$this->config['FactoryId'].'\'';
        $time = time();
        if($get['State'] === 'ing'){
            //$sql .= ' AND GETDATE() BETWEEN BeginTime AND EndTime';
            $sql .= ' AND '.$time.' BETWEEN BeginTime AND EndTime';
        }elseif($get['State'] === 'tobe'){
            //$sql .= ' AND GETDATE() < BeginTime';
            $sql .= ' AND '.$time.' < BeginTime';
        }elseif($get['State'] === 'ed'){
            //$sql .= ' AND GETDATE() > EndTime';
            $sql .= ' AND '.$time.' > EndTime';
        }
        $data = D('Odbc')->query($sql,'fetchAll');*/
        foreach($data as $k => $v){
            unset($data[$k]['ROW_NUMBER']);
            unset($data[$k]['Total']);
            if($v['IsRangePrice']){
                $MinPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->min('Price');
                $MaxPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->max('Price');
                $data[$k]['Price'] = PriceFormat($MinPrice).'~'.PriceFormat($MaxPrice);
            }else{
                $data[$k]['Price'] = PriceFormat($v['Price']);
            }
            $data[$k]['MarketPrice'] = PriceFormat($v['MarketPrice']);
            $Sale = M()->table('WebappOrder')->alias('wo')
                ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                ->where(['WebProductId' => $v['Id']])
                ->sum('Area');
            $data[$k]['Pic'] = $v['Pic']?explode(',',$v['Pic']):[C('ProductNoPicDefaultPic')];
            $data[$k]['SalePercent'] = round(($Sale / $v['Total']) * 100,2);
            $data[$k]['CurTime'] = $time;
        }
        echo jejuu([
            'count' => $count,
            'MaxPage' => $MaxPage,
            'data' => $data,
        ]);
    }

    public function FlagLists(){
        $time = time();
        $ing_data = M()->table('WebProduct')->where([
            'FactoryId' => $this->config['FactoryId'],
            'BoardId' => ['EXP','IS NOT NULL'],
            'MatNo' => ['EXP','IS NULL'],
            'BeginTime' => ['elt',$time],
            'EndTime' => ['egt',$time],
            'IsFlag' => 1,
            'IsDel' => 0,
        ])->field('Id,BoardId,Title,IsRangePrice,Price,MarketPrice,Total,Pic,BeginTime,EndTime')->order('Id desc')->select();
        foreach ($ing_data as $k => $v) {
            unset($ing_data[$k]['ROW_NUMBER']);
        }
        $tobe_data = M()->table('WebProduct')->where([
            'FactoryId' => $this->config['FactoryId'],
            'BoardId' => ['EXP','IS NOT NULL'],
            'MatNo' => ['EXP','IS NULL'],
            'BeginTime' => ['egt',$time],
            'IsFlag' => 1,
            'IsDel' => 0,
        ])->field('Id,BoardId,Title,IsRangePrice,Price,MarketPrice,Total,Pic,BeginTime,EndTime')->order('Id desc')->select();
        foreach ($tobe_data as $k => $v) {
            unset($tobe_data[$k]['ROW_NUMBER']);
        }
        if($this->config['ShowEdBoard']){
            $ed_data = M()->table('WebProduct')->where([
                'FactoryId' => $this->config['FactoryId'],
                'BoardId' => ['EXP','IS NOT NULL'],
                'MatNo' => ['EXP','IS NULL'],
                'EndTime' => ['elt',$time],
                'IsFlag' => 1,
                'IsDel' => 0,
            ])->field('Id,BoardId,Title,IsRangePrice,Price,MarketPrice,Total,Pic,BeginTime,EndTime')->order('Id desc')->select();
            foreach ($ed_data as $k => $v) {
                unset($ed_data[$k]['ROW_NUMBER']);
            }
        }else{
            $ed_data = [];
        }
        $data = array_merge($ing_data,$tobe_data,$ed_data);
        foreach ($data as $k => $v) {
            unset($data[$k]['Total']);
            if($v['IsRangePrice']){
                $MinPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->min('Price');
                $MaxPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $v['Id']])->max('Price');
                $data[$k]['Price'] = PriceFormat($MinPrice).'~'.PriceFormat($MaxPrice);
            }else{
                $data[$k]['Price'] = PriceFormat($v['Price']);
            }
            $data[$k]['MarketPrice'] = PriceFormat($v['MarketPrice']);
            $Sale = M()->table('WebappOrder')->alias('wo')
                ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                ->where(['WebProductId' => $v['Id']])
                ->sum('Area');
            $data[$k]['Pic'] = $v['Pic']?explode(',',$v['Pic']):[C('ProductNoPicDefaultPic')];
            $data[$k]['SalePercent'] = round(($Sale / $v['Total']) * 100,2);
            $data[$k]['CurTime'] = $time;
        }
        $this->assign([
            'LayoutTitle' => '纸板团购（'.$this->config['BoardFlag'].'）',
            'FlagLists' => jejuu($data),
        ]);
        $this->display();
    }

    public function detail(){
        $get = I('get.');
        $detail = M()->table('WebProduct')
            ->where([
                'FactoryId' => $this->config['FactoryId'],
                'Id' => $get['Id'],
                'BoardId' => ['EXP','IS NOT NULL'],
                'MatNo' => ['EXP','IS NULL'],
            ])
            ->field('BoardId,Title,IsRangePrice,Price,MarketPrice,IsDel,BuildMin,BuildMax,CusMax,Total,BeginTime,EndTime,Pic')
            ->find();
        if(!$detail){
            $this->error('参数错误');die;
        }
        if($detail['IsDel'] === '1'){
            $this->error('产品已删除');die;
        }
        unset($detail['ROW_NUMBER']);
        if($detail['IsRangePrice']){
            $MinPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->min('Price');
            $MaxPrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->max('Price');
            $detail['Price'] = PriceFormat($MinPrice).'~'.PriceFormat($MaxPrice);
            $RangePrice = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id']])->field('From,To,Price')->select();
            foreach($RangePrice as $k => $v){
                unset($RangePrice[$k]['ROW_NUMBER']);
                $RangePrice[$k]['From'] = (float)$v['From'];
                $RangePrice[$k]['To'] = (float)$v['To'];
                $RangePrice[$k]['Price'] = PriceFormat($v['Price']);
            }
            $detail['RangePrice'] = $RangePrice;
        }else{
            $detail['Price'] = PriceFormat($detail['Price']);
        }
        $detail['MarketPrice'] = PriceFormat($detail['MarketPrice']);
        $detail['BuildMin'] = (float)$detail['BuildMin'];
        $detail['BuildMax'] = (float)$detail['BuildMax'];
        $detail['CusMax'] = (float)$detail['CusMax'];
        $detail['Total'] = (float)$detail['Total'];
        $Sale = M()->table('WebappOrder')->alias('wo')
            ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
            ->where(['WebProductId' => $get['Id']])
            ->sum('Area');
        $detail['SalePercent'] = round(($Sale / $detail['Total']) * 100,2);
        $detail['Surp1'] = round($detail['Total'] - $Sale,3);
        if(session('ERP_Wap0_User')){
            $detail['isLogin'] = 1;
            $ERPId = M()->table('WebappUser')->where(['UserName' => session('ERP_Wap0_User.UserName')])->getField('ERPId');
            $Cus = M()->table('WebappOrder')->alias('wo')
                ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                ->where(['wgo.FactoryId' => $this->config['FactoryId'],'wgo.CusId' => $ERPId,'WebProductId' => $get['Id']])
                ->sum('Area');
            $detail['CusPercent'] = round(($Cus / $detail['CusMax']) * 100,2);
            $detail['Surp2'] = round($detail['CusMax'] - $Cus,3);
        }else{
            $detail['isLogin'] = 0;
        }
        $detail['Pic'] = $detail['Pic']?explode(',',$detail['Pic']):[C('ProductNoPicDefaultPic')];
        //买家订单
        if(1){
            //真实数据
            $BuyerOrder = M()->table('WebappOrder')->alias('wo')
                ->join('LEFT OUTER JOIN WebGroupOrder wgo ON wo.FactoryId = wgo.FactoryId AND wo.CusId = wgo.CusId AND wo.CusPoNo = wgo.CusPoNo')
                ->where(['WebProductId' => $get['Id']])
                ->field('wgo.Id,wgo.CusId,Area,Cost,SaveCost')
                ->order('Id desc')
                ->select();
            foreach($BuyerOrder as $k => $v){
                unset($BuyerOrder[$k]['ROW_NUMBER']);
                unset($BuyerOrder[$k]['Id']);
                $BuyerOrder[$k]['CusId'] = CusIdSecret($v['CusId']);
                $BuyerOrder[$k]['Area'] = (float)$v['Area'];
                $BuyerOrder[$k]['Cost'] = sprintf('%.2f',$v['Cost']);
                $BuyerOrder[$k]['SaveCost'] = sprintf('%.2f',$v['SaveCost']);
            }
        }else{
            //伪造数据
            $BeginTime = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->getField('BeginTime');
            if(time() < $BeginTime){
                $BuyerOrder = [];
            }else{
                if(!session('BuyerOrder')){
                    $rand = rand(30,50);
                    $temp = [];
                    for ($i = 0; $i < $rand; $i ++) {
                        $Area = rand(50,5000);
                        $wp = M()->table('WebProduct')->where(['Id' => $get['Id']])->field('IsRangePrice,Price,MarketPrice')->find();
                        if($wp['IsRangePrice']){
                            $wprp = M()->table('WebProductRangePrice')->where(['WebProductId' => $get['Id'],'From' => ['elt',$Area],'To' => ['egt',$Area]])->field('Price')->order('Price')->find();
                            $Cost = $wprp['Price'] * $Area;
                        }else{
                            $Cost = $wp['Price'] * $Area;
                        }
                        $temp[$i] = [
                            'CusId' => chr(rand(65,90)).'***'.chr(rand(65,90)),
                            'Area' => $Area,
                            'Cost' => sprintf('%.2f',$Cost),
                            'SaveCost' => sprintf('%.2f',($wp['MarketPrice'] * $Area) - $Cost),
                        ];
                    }
                    session('BuyerOrder',$temp);
                }
                $BuyerOrder = session('BuyerOrder');
            }
        }
        $Descr = M()->table('WebProduct')->where(['FactoryId' => $this->config['FactoryId'],'Id' => $get['Id']])->getField('Descr');
        $this->assign([
            'LayoutTitle' => '产品详情',
            'detail' => jejuu($detail),
            'BuyerOrder' => jejuu($BuyerOrder),
            'Descr' => $Descr,
        ]);
        $this->display();
    }

}
