<?php

abstract class Medoo_Model
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

    public function init()
    {
        $this->_medoo = Medoo_Medoo::getInstance();
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
}