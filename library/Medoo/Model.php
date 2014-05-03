<?php

class Medoo_Model
{
    /**
     * @var null|string
     */
    protected $_tableName = null;

    /**
     * @var medoo
     */
    protected $_medoo = null;


    public function __construct()
    {
        $this->init();
    }

    /**
     * @param $tableName
     * @return Medoo_Model
     */
    public static function factory($tableName)
    {
        $model = new self();
        return $model->setTableName($tableName);
    }

    public function init()
    {
        $this->_medoo = Medoo_Medoo::getInstance();
    }

    /**
     * @param $tableName
     * @return Medoo_Model
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * @param $datas
     * @return array
     */
    public function insert($datas)
    {
        return $this->_medoo->insert($this->_tableName, $datas);
    }

    /**
     * @param $datas
     * @param null $where
     * @return int
     */
    public function update($datas, $where = null)
    {
        return $this->_medoo->update($this->_tableName, $datas, $where);
    }

    /**
     * @param $where
     * @return array
     */
    public function delete($where)
    {
        return $this->_medoo->delete($this->_tableName, $where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return array|bool
     */
    public function select($join, $column = null, $where = null)
    {
        return $this->_medoo->select($this->_tableName, $join, $column, $where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return int
     */
    public function count($join = null, $column = null, $where = null)
    {
        return $this->_medoo->count($this->_tableName, $join, $column, $where);
    }

    /**
     * @param $query
     * @return PDOStatement
     */
    public function query($query)
    {
        return $this->_medoo->query($query);
    }

    /**
     * @param $value
     * @param string $columnName
     * @return array
     */
    public function insertIfNotExist($value, $columnName = 'name')
    {
        if ($res = $this->select('id', array($columnName => $value))) {
            return $res['id'];
        }
        return $this->insert(array($columnName => $value));
    }
}