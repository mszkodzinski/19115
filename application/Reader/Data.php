<?php

class Reader_Data extends DB_DB
{
    public function getData($params)
    {
        $fields = '*';
        if ($params['groupby']) {
            $fields = ' sum(1), ' . $params['groupby'];
        }
        $sql = 'select ' . $fields . ' from notification ';
        if ($params['filter']) {
            $where = array();
            foreach ($params['filter'] as $filterName => $filterDef) {
                $cond = $filterName . ' ';
                if (!is_array($filterDef)) {
                    $filterDef = array($filterDef);
                }
                if (count($filterDef) == 1) {
                    $cond .= ' = ' . $filterDef[0];
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
        if ($params['groupby']) {
            $sql .= ' group by ' . $params['groupby'];
        }

        $result = $this->getDBObject()->query($sql);


        return array($result->fetchAll());
    }
}