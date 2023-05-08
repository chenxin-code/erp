<?php
namespace Wap0\Controller;
//常用材质
class UsedboardController extends BaseController{

    /********** 页面 **********/

    public function lists(){
//        for($i = 1;$i <= 999;$i++){
//            D('Odbc')->query(D('Func')->utf8_to_gbk('UPDATE BoardCode SET BoardName = \'材质名称编号['.rand(231232,4444444).']号\' WHERE Id = '.rand(1,20000)));
//        }
        $this->assign([
            'LayoutTitle' => '常用材质',
        ]);
        $this->display();
    }

    /********** 接口 **********/

    public function lists_api(){
        $get = I('get.');
        //先判断是否使用报价价格里的材质
        if($this->config['UseQuoBoard']){
            $QuotaId = M()->table('QuotaBdComT')->where([
                'FactoryId' => $this->config['FactoryId'],
                'Effctive' => 1,
            ])->getField('QuotaId');
            $p3 = '';
            if($get['Key']){
                $p3 .= ' AND a.BoardId LIKE \'\'%'.$get['Key'].'%\'\'';
            }
            if($get['OnlyShowChecked'] === 'true'){
                $p3 .= ' AND w.BoardId IS NOT NULL';
            }
            $BoardCodeCheckbox = M()->query('exec GetQuoPriceByCus2 \'\'\''.$this->ERPId.'\'\'\',\'q.FactoryId = \'\''.$this->config['FactoryId'].'\'\' AND q.QuotaId = \'\''.$QuotaId.'\'\'\',\''.$p3.'\'');
            foreach ($BoardCodeCheckbox as $k => $v) {
                unset($BoardCodeCheckbox[$k]['GroupId']);
                unset($BoardCodeCheckbox[$k]['UnitPrice']);
                unset($BoardCodeCheckbox[$k]['CusId']);
            }
            $_BeChecked = M()->query('exec GetQuoPriceByCus2 \'\'\''.$this->ERPId.'\'\'\',\'q.FactoryId = \'\''.$this->config['FactoryId'].'\'\' AND q.QuotaId = \'\''.$QuotaId.'\'\'\',\' AND w.BoardId IS NOT NULL\'');
            //二维转一维
            $BeChecked = [];
            foreach($_BeChecked as $v){
                $BeChecked[] = $v['BoardId'];
            }
            //dump($BoardCodeCheckbox);die;
            //dump($BeChecked);die;
            echo jejuu([
                'BoardCodeCheckbox' => $BoardCodeCheckbox,
                'BeChecked' => $BeChecked,
            ]);
        }else{
            $_BeChecked = M()->table('WebappUsedBoard')->where(['CusId' => $this->ERPId])->field('BoardId')->select();
            //二维转一维再转字符串
            $BeChecked = [];
            foreach($_BeChecked as $v){
                $BeChecked[] = $v['BoardId'];
            }
            $BeChecked = implode(',',$BeChecked);
            $where = [];
            if($this->config['FactoryId'] === ''){
                $field1 = 'PaperName';
                $field2 = 'PaperName AS BoardName';
            }else{
                $field1 = 'BoardName';
                $field2 = 'BoardName';
            }
            if($get['Key']){
                $where['BoardId|'.$field1] = ['like','%'.$get['Key'].'%'];
            }
            if($get['OnlyShowChecked'] === 'true'){
                $where['BoardId'] = ['IN',$BeChecked];
            }
            $count = M()->table('BoardCode')->where($where)->count();
            $model = M()->table('BoardCode')->where($where)->field('BoardId,'.$field2);
            if($get['CurPage'] && $get['PageSize']){
                $MaxPage = ceil($count/$get['PageSize']);
                if($MaxPage < 1){$MaxPage = 1;}
                $BoardCodeCheckbox = $model->page($get['CurPage'],$get['PageSize'])->select();
            }else{
                $MaxPage = '当前不是分页取数据该值无意义';
                $BoardCodeCheckbox = $model->select();
            }
            //dump($BoardCodeCheckbox);die;
            //dump($BeChecked);die;
            echo jejuu([
                'count' => $count,
                'MaxPage' => $MaxPage,
                'BoardCodeCheckbox' => $BoardCodeCheckbox,
                'BeChecked' => $BeChecked?explode(',',$BeChecked):[],
            ]);
        }
    }

    //保存
    public function save_api(){
        $post = I('post.');
        //dump($post['BeChecked']);die;
        $data = [];
        foreach($post['BeChecked'] as $v){
            $data[] = ['CusId' => $this->ERPId,'BoardId' => $v];
        }
        //dump($data);die;
        M()->startTrans();
        $r1 = M()->table('WebappUsedBoard')->where(['CusId' => $this->ERPId])->delete();
        $r2 = $data?M()->table('WebappUsedBoard')->addAll($data):TRUE;
        if($r1 !== FALSE && $r2 !== FALSE){
            M()->commit();
            echo jejuu([
                'ret' => C('succ_ret'),
                'msg' => '保存成功',
            ]);
        }else{
            M()->rollback();
            echo jejuu([
                'ret' => C('fail_ret'),
                'msg' => '保存失败',
            ]);
        }
        //        $sqls = [];
//        foreach($post['BeChecked'] as $v){
//            $sqls[] = 'INSERT INTO WebappUsedBoard (CusId,BoardId) VALUES (\''.$this->ERPId.'\',\''.$v.'\')';
//        }
//        //dump($sqls);die;
//        //事务处理：① 删除旧的 ② 添加新的
//        $r = D('Odbc')->execTransaction(array_merge(['DELETE FROM WebappUsedBoard WHERE CusId = \''.$this->ERPId.'\''],$sqls));
//        //dump($r);die;
//        if($r === TRUE){
//            echo jejuu([
//                'ret' => C('succ_ret'),
//                'msg' => '保存成功',
//            ]);
//        }else{
//            echo jejuu([
//                'ret' => C('fail_ret'),
//                'msg' => '保存失败',
//            ]);
//        }
    }

}