<?php
/*
 *  ---------------------
 *  2000
 *  ---------------------
 *  ThinkPHP 的 Model 不支持  ODBC 方式支持
 *  只能使用 ODBC 方式连接数据库
 *  这种方式对分页读取表数据不太友好
 *  ---------------------
 *  2008
 *  ---------------------
 *  ThinkPHP 的 Model 支持  ODBC 方式也支持
 *  应尽量使用 ThinkPHP 的 Model 连接数据库
 *  ---------------------
 */
namespace Common\Model;
//ODBC方式连接数据库的模型类
class OdbcModel{

    protected $Driver = '{SQL Server}';
    //protected $Server = C('DB_HOST');
    //protected $Database = C('DB_NAME');
    //protected $dbUser = C('DB_USER');
    //protected $dbPasswd = C('DB_PWD');
    protected $dbCharset = 'gbk';
    protected $PDO;

    public function __construct(){
        //链接1：http://www.cnblogs.com/huangtailang/p/6485528.html
        //链接2：http://blog.csdn.net/sunck1970/article/details/51315688
        //使用ODBC方式连接
        //php.ini文件需打开扩展extension=php_pdo_odbc.dll
        $this->PDO = new \PDO('odbc:Driver='.$this->Driver.';Server='.C('DB_HOST').';Database='.C('DB_NAME'), C('DB_USER'), C('DB_PWD'));
        $this->PDO->exec('SET character_set_connection='.$this->dbCharset.', character_set_results='.$this->dbCharset.', character_set_client=binary');
    }

    /**
     * Query 查询
     *
     * @param String $sql SQL语句
     * @param String $queryMode 查询方式(fetchAll or fetch)
     */
    public function query($sql, $queryMode){
        $recordset = $this->PDO->query($sql);
        if ($recordset) {
            $recordset->setFetchMode(\PDO::FETCH_ASSOC);
            if ($queryMode == 'fetchAll') {
                $result = $recordset->fetchAll();
            } elseif ($queryMode == 'fetch') {
                $result = $recordset->fetch();
            } else {
                $result = null;
            }
        } else {
            $result = null;
        }
        $result = D('Func')->gbk_to_utf8($result);
        return $result;
    }

    /**
     * Update 更新
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @param String $where 条件
     * @return Int
     */
    public function update($table, $arrayDataValue, $where = ''){
        if ($where) {
            $sql = '';
            foreach ($arrayDataValue as $k => $v) {
                $sql .= ", $k = '$v'";
            }
            $sql = substr($sql, 1);
            $sql = "UPDATE $table SET $sql WHERE $where";
        } else {
            $sql = "REPLACE INTO $table (".implode(',', array_keys($arrayDataValue)).") VALUES ('".implode("','", $arrayDataValue)."')";
        }
        $sql = D('Func')->utf8_to_gbk($sql);
        return $this->PDO->exec($sql);
    }

    /**
     * Insert 插入
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @return Int
     */
    public function insert($table, $arrayDataValue){
        $sql = "INSERT INTO $table (".implode(',', array_keys($arrayDataValue)).") VALUES ('".implode("','", $arrayDataValue)."')";
        $sql = D('Func')->utf8_to_gbk($sql);
        return $this->PDO->exec($sql);
    }

    /**
     * Replace 覆盖方式插入
     *
     * @param String $table 表名
     * @param Array $arrayDataValue 字段与值
     * @return Int
     */
    public function replace($table, $arrayDataValue){
        $sql = "REPLACE INTO $table(".implode(',', array_keys($arrayDataValue)).") VALUES ('".implode("','", $arrayDataValue)."')";
        $sql = D('Func')->utf8_to_gbk($sql);
        return $this->PDO->exec($sql);
    }

    /**
     * Delete 删除
     *
     * @param String $table 表名
     * @param String $where 条件
     * @return Int
     */
    public function delete($table, $where){
        $sql = "DELETE FROM $table WHERE $where";
        $sql = D('Func')->utf8_to_gbk($sql);
        return $this->PDO->exec($sql);
    }

    /**
     * 获取字段最大值
     *
     * @param string $table 表名
     * @param string $field_name 字段名
     * @param string $where 条件
     */
    public function getMaxValue($table, $field_name, $where = ''){
        $sql = "SELECT MAX(".$field_name.") AS MAX_VALUE FROM $table";
        if ($where != '') {
            $sql .= " WHERE $where";
        }
        $sql = D('Func')->utf8_to_gbk($sql);
        $arrTemp = $this->query($sql,'fetch');
        $maxValue = $arrTemp["MAX_VALUE"];
//        if ($maxValue == "" || $maxValue == null) {
//            $maxValue = 0;
//        }
        return $maxValue;
    }

    /**
     * 获取指定列的数量
     *
     * @param string $table
     * @param string $field_name
     * @param string $where
     * @return int
     */
    public function getCount($table, $field_name, $where = ''){
        $sql = "SELECT COUNT($field_name) AS NUM FROM $table";
        if ($where != '') {
            $sql .= " WHERE $where";
        }
        $sql = D('Func')->utf8_to_gbk($sql);
        $arrTemp = $this->query($sql, 'fetch');
        return $arrTemp['NUM'];
    }

    /**
     * transaction 通过事务处理多条SQL语句
     *
     * @param array $arraySql
     * @return Boolean
     */
    public function execTransaction($arraySql){
        $retval = 1;
        $this->PDO->beginTransaction();
        $arraySql = D('Func')->utf8_to_gbk($arraySql);
        foreach ($arraySql as $sql) {
            if ($this->PDO->exec($sql) === FALSE) {
                $retval = 0;
            }
        }
        if ($retval == 0) {
            $this->PDO->rollback();
            return false;
        } else {
            $this->PDO->commit();
            return true;
        }
    }

    /**
     * destruct 关闭数据库连接
     */
    public function destruct(){
        $this->PDO = null;
    }

}