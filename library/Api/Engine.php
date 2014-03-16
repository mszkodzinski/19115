<?php
class Api_Engine
{
    public function action($name, $data)
    {
        $r = new Api_Responce();
        switch ($name) {
            case 'test':
                $r = $this->test($data);
                break;
            case 'getData':
                $r = $this->getData($data);
                break;
            case 'getLabels':
                $r = $this->getLabels($data);
                break;
            case 'getStats':
                $r = $this->getStats($data);
                break;
            case 'getCoords':
                $r = $this->getCoords($data);
                break;
        }
        return $r->serialize();
    }

    public function test($data)
    {
        $r = new Api_Responce();
        $r->data = $data;
        return $r;
    }
    public function getLabels()
    {
        $r = new Api_Responce();

        $reader = new Reader_Data();
        $result = $reader->getLabels();

        if (!$result) {
            $r->status = false;
            $r->code = 500;
        } else {
            $r->data = $result;
        }
        return $r;
    }

    public function getData($params)
    {
        $r = new Api_Responce();
        foreach (array('filter', 'groupby', 'sortby', 'order', 'limit'/*, 'notnull'*/) as $key) {
            if (!isset($params[$key])) {
                $params[$key] = null;
            }
        }

        if (!$params['groupby']) {
            $r->status = false;
            $r->error = array(
                'groupby' => 'brak parametru'
            );
            return $r;
        } else {
            $map = array(
                'status' => 'k_status',
                'source' => 'k_source',
                'organization' => 'k_organization',
                'district' => 'k_district',
                'year' => 'year',
                'month' => 'month',
                'year_month' => 'year_month',
                'year_month_day' => 'year_month_day'
            );
            if (!isset($map[$params['groupby']])) {
                $r->status = false;
                $r->error = array(
                    'groupby' => 'zla wartosc'
                );
                return $r;
            }
            $params['groupby'] = $map[$params['groupby']];
        }

        $reader = new Reader_Data();
        $result = $reader->getData($params);

        if (!$result) {
            $r->status = false;
            $r->code = 500;
        } else {
            $r->data = $result;
        }
        return $r;
    }

    public function getStats()
    {
        $r = new Api_Responce();

        $reader = new Reader_Data();
        $result = $reader->getStats();

        if (!$result) {
            $r->status = false;
            $r->code = 500;
        } else {
            $r->data = $result;
        }
        return $r;
    }

    public function getCoords($params)
    {
        $r = new Api_Responce();

        $reader = new Reader_Data();
        $result = $reader->getCoords($params);

        if (!$result) {
            $r->status = false;
            $r->code = 500;
        } else {
            $r->data = $result;
        }
        return $r;
    }
}