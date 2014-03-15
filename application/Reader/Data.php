<?php

class Reader_Data extends DB_DB
{
    public function getData($params)
    {
        $sql = 'select * from notification ';
        if ($params['filter']) {
            $where = array();
            foreach ($params['filter'] as $filterName => $filterDef) {
                $cond = $filterName . ' ';
                if (!is_array($filterDef)) {
                    $filterDef = array($filterDef);
                }
                if (count($filterDef) == 1) {
                    $cond .= ' = ' . $filterDef;
                } else {
                    $cond .= ' in (' . implode(',', $filterDef) . ')';
                }
                $where[] = $cond;
            }
            if (count($where)) {
                $sql .= ' where ' . implode(' and ', $where);
            }
        }
        if ($params['sortby']) {
            $sql .= ' order by ' . $params['sortby'];
            if ($params['order']) {
                $sql .= $params['order'];
            }
        }
        return array(1,2,3);
    }
}