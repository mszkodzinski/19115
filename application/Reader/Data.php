<?php

class Reader_Data
{
    public function getTime()
    {
        $result = array(
            'label' => array(),
            'value' => array(),
        );
        $q = $this->getDBObject()->query('select round(avg(notification_time)/60/24) as value ,o.name as label from notification n
join organization o on o.id = n.k_organization
where notification_time > 0 and length(trim(o.name)) > 0
group by k_organization order by value desc');
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['label'][] = $item['label'];
                $result['value'][] = intval($item['value']);
            }
        }
        return $result;
    }

    public function getStats()
    {
        $result = array(
            'sum' => 0,
            'sum30' => 0,
            'diff30' => 0
        );

        $q = Medoo_Medoo::getInstance()->count("notification");//'select count(1) as sum from notification');
        if ($q) {
            //$r = $q->fetchAll();
            $result['sum'] = $q;
        }
        $q = Medoo_Medoo::getInstance()->count("notification", array("date_of_acceptance[>]" => date('Y-m-d', strtotime('-30 days'))));//'select count(1) as sum from notification where date_of_acceptance > \'' . date('Y-m-d', strtotime('-30 days')) . '\'');
        if ($q) {
            //$r = $q->fetchAll();
            $result['sum30'] = $q;
        }
        $q = Medoo_Medoo::getInstance()->count("notification", array("date_of_acceptance[>]" => date('Y-m-d', strtotime('-60 days'))));//select count(1) as sum from notification where date_of_acceptance > \'' . date('Y-m-d', strtotime('-60 days')) . '\'');
        if ($q) {
            //$r = $q->fetchAll();
            $result['sum60'] = $q;
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
        $q = Medoo_Medoo::getInstance()->select("organization", "*");
        if ($q) {
            foreach ($q as $item) {
                $result['organization'][$item['id']] = $item['name'];
            }
        }
        $q = Medoo_Medoo::getInstance()->select("source", "*");//('select * from source');
        if ($q) {
            foreach ($q as $item) {
                $result['source'][$item['id']] = $item['name'];
            }
        }
        $q = Medoo_Medoo::getInstance()->select("district", "*");//('select * from district');
        if ($q) {
            foreach ($q as $item) {
                $result['district'][$item['id']] = $item['name'];
            }
        }
        $q = Medoo_Medoo::getInstance()->select("status", "*");//('select * from status');
        if ($q) {
            foreach ($q as $item) {
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
                $params["GROUP"] = 'year(date_of_acceptance)';
                break;
            case 'month':
                $params["GROUP"] = 'year(date_of_acceptance)';
                break;
            case 'year_month':
                $params["GROUP"] = 'concat(year(date_of_acceptance),\'-\',month(date_of_acceptance))';
                break;
            case 'year_month_day':
                $params["GROUP"] = 'concat(year(date_of_acceptance),\'-\',month(date_of_acceptance),\'-\',day(date_of_acceptance))';
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
        echo $sql;
        $q = $this->getDBObject()->query($sql);
        if ($q) {
            foreach ($q->fetchAll() as $item) {
                $result['label'][] = $item['label'];
                $result['value'][] = intval($item['value']);
            }
        }

        return $result;
    }

    public function getMaps()
    {
        $result = array(
        );

        $q = Medoo_Medoo::getInstance()->select("notification", array("[>]status" => array("k_status" => "id")), array("notification.longtitude(lon)", "notification.lattitude(lat)", "status.name(des)", "status.id(type)"), array(
            "AND" => array("AND" => array("longtitude[>]" => 19, "longtitude[<]" => 23), "AND" => array("lattitude[>]" => 50, "lattitude[<]" => 54)),
            "ORDER" => "date_of_acceptance DESC",
            "LIMIT" => 300
        ));
//        $q = $this->getDBObject()->query('SELECT longtitude as lon,lattitude as lat,s.name as des,s.id as type FROM notification n
//join status s on s.id = n.k_status
//where (longtitude > 19 and longtitude < 23) and (lattitude > 50 and lattitude < 54)
//order by date_of_acceptance desc
//limit 300');
        if (!empty($q)) {
            foreach ($q as $item) {
                $result[] = array(
                    'points' =>  array(floatval($item['lat']), floatval($item['lon'])),
                    'description' => $item['des'],
                    'type' => intval($item['type'])
                );
            }
        }
        return $result;
    }
}