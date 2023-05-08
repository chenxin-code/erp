<?php
namespace Common\Model;
//函数库
class FuncModel{

    //获取项目配置
    public function getConfig(){
        $tableData = [];
        $_tableData = M()->table('WebConfig')->select();
        foreach($_tableData as $k => $v){
            $tableData[$v['Name']] = $v['Value'];
        }
        $finalData = array_merge(C('WebConfig'),$tableData);
        return $finalData;
    }

    //添加或修改项目配置
    //找到记录就修改，否则就添加
    public function aosConfig($Name,$Value){
        if(M()->table('WebConfig')->where(['Name' => $Name])->find()){
            $r1 = M()->table('WebConfig')->where(['Name' => $Name])->save(['Value' => $Value]);
            if($r1 !== FALSE){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            $r2 = M()->table('WebConfig')->add(['Name' => $Name,'Value' => $Value]);
            if($r2){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    //支付模块生成密钥方法
    public function makeSecret($CusId,$CusPoNo,$strrev = FALSE){
        $md5_salt = C('md5_salt');
        if($strrev){
            $md5_salt = strrev($md5_salt);
        }
        return md5($CusId.$CusPoNo.$md5_salt);
    }

    //gbk 转 utf-8
    public function gbk_to_utf8($a){
        return eval('return '.iconv('gbk','utf-8',var_export($a,true).';'));
    }

    //utf-8 转 gbk
    public function utf8_to_gbk($a){
        return eval('return '.iconv('utf-8','gbk',var_export($a,true).';'));
    }

    //验证码检测
    public function check_code($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    //将秒数转为可读的字符串
    public function seconds_to_readstring($seconds){
        $readstring = '';
        if($seconds >= 86400){
            $readstring .= floor($seconds / 86400).'天';
            $seconds %= 86400;
        }
        if($seconds >= 3600){
            $readstring .= floor($seconds / 3600).'小时';
            $seconds %= 3600;
        }
        if($seconds >= 60){
            $readstring .= floor($seconds / 60).'分钟';
            $seconds %= 60;
        }
        if($seconds >= 1){
            $readstring .= $seconds.'秒';
        }
        return $readstring;
    }

    //带密钥的加密解密函数
    //在一些开发中，我们使用curl等进行通信，如果你的一些隐私数据不进行加密，就可能造成信息泄露，带来不必要的麻烦
    //我们提供一个带密钥的加密解密函数，只要你的密钥不泄露，就可能很好的保护你的传输
    //下面的两个函数，一个为加密函数，一个为解密函数
    //encrypt($str, $key) 为加密函数，其中$str为要加密的字符串，$key为你的密钥
    //decrypt($str, $key) 为解密函数，其中$str为要解密的字符串，$key为你的密钥
    public function encrypt($str,$key){
        $key = md5($key);
        $x = 0;
        $len = strlen($str);
        $l = strlen($key);
        $char = '';
        $r = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {$x = 0;}
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++){
            $r .= chr(ord($str{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($r);
    }
    public function decrypt($str,$key){
        $key = md5($key);
        $x = 0;
        $data = base64_decode($str);
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        $r = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {$x = 0;}
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $r .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $r .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $r;
    }

    /**
     * 上传图片
     * @param $filename  表单name文件名  <input type="file" name="文件名">
     * @param $savePath  图片存储目录【格式：xxx/xxx/xxx/】
     * 返回上传结果信息
     */
    public function upload_img($filename,$savePath = ''){
        if(isset($_FILES[$filename]) && $_FILES[$filename]['error'] == 0){
            $upload = new \Think\Upload([
                'rootPath' => './res/',//设置附件上传根目录
                'savePath' => $savePath,//图片二级目录的名称
                'autoSub' => false,//避免系统自动生成日期文件夹存放图片
            ]);
            //上传文件时指定一个要上传的图片的名称，否则会把表单中所有的图片都处理，之后再想其他图片时就再找不到图片了
            $info = $upload->upload([$filename => $_FILES[$filename]]);
            if($info){
                return [
                    'bool' => TRUE,
                    'images' => $info[$filename]['savepath'].$info[$filename]['savename'],
                ];
            }else{
                return [
                    'bool' => FALSE,
                    'error' => $upload->getError(),
                ];
            }
        }else{
            return [
                'bool' => FALSE,
                'error' => '没有提交图片',
            ];
        }
    }

    /**
     * 多图上传
     * @param $filename 即upload_img方法第1个参数  <input type="file" name="文件名[]">
     * @param $savePath 自行配置upload_img方法第2个参数【图片存储目录】
     * 返回上传结果信息
     */
    public function upload_more_imgs($filename,$savePath = ''){
        $img_arr = [];
        if($this->has_img($filename)){
            //先整理二维数组
            $files = [];
            foreach($_FILES[$filename]['name'] as $k => $v){
                if(!$v){continue;}
                $files[] = [
                    'name' => $v,
                    'type' => $_FILES[$filename]['type'][$k],
                    'tmp_name' => $_FILES[$filename]['tmp_name'][$k],
                    'error' => $_FILES[$filename]['error'][$k],
                    'size' => $_FILES[$filename]['size'][$k],
                ];
            }
            $temp = $_FILES;//用临时变量来保存$_FILES【解决同一个脚本里二次调用该函数时失效问题】
            //用整理好的数组覆盖原图片数组，因为upload_img方法中会去到$_FILES里找图片
            $_FILES = $files;
            //循环上传
            foreach($files as $k => $v){
                if(!$v){continue;}
                $r = $this->upload_img($k,$savePath);
                if($r['bool']){
                    $img_arr[] = $r['images'];
                }
            }
            $_FILES = $temp;//用临时变量来还原$_FILES，不影响二次调用该函数
        }
        return $img_arr;
    }

    //判断多图上传时是否提交了图片
    public function has_img($name){
        foreach($_FILES[$name]['name'] as $v){
            if($v){return TRUE;}
        }
        return FALSE;
    }

    //递归删除指定目录下的文件和目录（用于删除runtime下的ThinkPHP缓存文件）
    //链接：http://blog.csdn.net/pzp_118/article/details/8820400
    public function del_dir($path){
        //给定的目录不是一个文件夹
        if(!is_dir($path)){
            return false;
        }
        $fh = opendir($path);
        while(($row = readdir($fh)) !== false){
            //过滤掉虚拟目录
            if($row == '.' || $row == '..'){
                continue;
            }
            if(!is_dir($path.'/'.$row)){
                unlink($path.'/'.$row);
            }
            $this->del_dir($path.'/'.$row);
        }
        //关闭目录句柄，否则出Permission denied
        closedir($fh);
        //删除文件之后再删除自身
        //if(!rmdir($path)){
        //	echo $path.'无权限删除<br>';
        //}
        rmdir($path);
        return true;
    }

}
