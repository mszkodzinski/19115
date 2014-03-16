<?php

class Reader_Data extends DB_DB
{
    public function getStats()
    {
        $result = array(
            'sum' => 0,
            'sum30' => 0,
            'diff30' => 0
        );

        $q = $this->getDBObject()->query('select count(1) as sum from notification');
        if ($q) {
            $r = $q->fetchAll();
            $result['sum'] = $r[0]['sum'];
        }
        $q = $this->getDBObject()->query('select count(1) as sum from notification where date_of_acceptance > \'' . date('Y-m-d', strtotime('-30 days')) . '\'');
        if ($q) {
            $r = $q->fetchAll();
            $result['sum30'] = $r[0]['sum'];
        }
        $q = $this->getDBObject()->query('select count(1) as sum from notification where date_of_acceptance > \'' . date('Y-m-d', strtotime('-60 days')) . '\'');
        if ($q) {
            $r = $q->fetchAll();
            $result['sum60'] = $r[0]['sum'];
            $result['diff60p'] = ($result['sum60'] - $result['sum30']) != 0 ? -1 * round(100 * ((float)$result['sum60'] - 2 * $result['sum30']) / ($result['sum60'] - $result['sum30'])) : 0;
        }
        $result['sum'] = number_format($result['sum'], 0, '.', ' ');
        $result['sum30'] = number_format($result['sum30'], 0, '.', ' ');
        return $result;
    }

    public function getLabels()
    {
        $result = array(
            'organization' => array(),
            'source' => array(),
            'district' => array(),
            'status' => array()
        );
        $q = $this->getDBObject()->query('select * from organization');
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['organization'][$item['id']] = $item['name'];
            }
        }
        $q = $this->getDBObject()->query('select * from source');
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['source'][$item['id']] = $item['name'];
            }
        }
        $q = $this->getDBObject()->query('select * from district');
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['district'][$item['id']] = $item['name'];
            }
        }
        $q = $this->getDBObject()->query('select * from status');
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['status'][$item['id']] = $item['name'];
            }
        }
        return $result;
    }

    public function getData($params)
    {
        $fields = '*';

        switch ($params['groupby']) {
            case 'year':
                $params['groupby'] = 'year(date_of_acceptance)';
                break;
            case 'month':
                $params['groupby'] = 'year(date_of_acceptance)';
                break;
            case 'year_month':
                $params['groupby'] = 'concat(year(date_of_acceptance),\'-\',month(date_of_acceptance))';
                break;
            case 'year_month_day':
                $params['groupby'] = 'concat(year(date_of_acceptance),\'-\',month(date_of_acceptance),\'-\',day(date_of_acceptance))';
                break;
        }

        if ($params['groupby']) {
            $fields = ' sum(1) as value, ' . $params['groupby'] . ' as label';
        }
        $sql = 'select ' . $fields . ' from notification ';
        $where = array();
        if ($params['filter']) {
            foreach ($params['filter'] as $filterName => $filterDef) {
                switch ($filterName) {
                    case 'date':
                        if (count($filterDef) == 2) {
                            $cond = ' date_of_acceptance between \'' . $filterDef[0] . '\' and \'' . $filterDef[1] . '\'';
                        }
                        break;
                    default:
                        $cond = $filterName . ' ';
                        if (!is_array($filterDef)) {
                            $filterDef = array($filterDef);
                        }
                        if (count($filterDef) == 1) {
                            $cond .= ' = ' . $filterDef[0];
                        } else {
                            $cond .= ' in (' . implode(',', $filterDef) . ')';
                        }
                        break;
                }
                $where[] = $cond;
            }
        }
//        if ($params['groupby'] && $params['notnull']) {
//            $where[] = $params['groupby'] . ' is not null';
//        }
        if (count($where)) {
            $sql .= ' where ' . implode(' and ', $where);
        }
        if ($params['groupby']) {
            $sql .= ' group by ' . $params['groupby'];
        }
        if ($params['sortby']) {
            $sql .= ' order by ' . $params['sortby'];
            if ($params['order']) {
                $sql .= ' ' . $params['order'];
            }
        }
        if ($params['limit']) {
            $sql .= ' limit ' . $params['limit'];
        }

        $result = array(
            'label' => array(),
            'value' => array(),
        );
//        echo 'aa'.$sql.'aa';
        $q = $this->getDBObject()->query($sql);
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['label'][] = $item['label'];
                $result['value'][] = intval($item['value']);
            }
        }

        return $result;
    }
}