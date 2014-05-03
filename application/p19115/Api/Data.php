<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/3/14
 * Time: 12:45 PM
 */
class p19115_Api_Data extends Api_Engine
{
    public function getLabels()
    {
        $r = new Api_Responce();

        $reader = new p19115_Service_Data();
        $result = $reader->getLabels();

        if (!$result) {
            $r->status = false;
            $r->code = Api_Responce::CODE_ERROR;
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

        $reader = new p19115_Service_Data();
        $result = $reader->getData($params);

        if (!$result) {
            $r->status = false;
            $r->code = Api_Responce::CODE_ERROR;
        } else {
            $r->data = $result;
        }
        return $r;
    }

    public function getStats()
    {
        $r = new Api_Responce();

        $reader = new p19115_Service_Data();
        $result = $reader->getStats();

        if (!$result) {
            $r->status = false;
            $r->code = Api_Responce::CODE_ERROR;
        } else {
            $r->data = $result;
        }
        return $r;
    }

    public function getTime()
    {
        $r = new Api_Responce();

        $reader = new p19115_Service_Data();
        $result = $reader->getTime();

        if (!$result) {
            $r->status = false;
            $r->code = Api_Responce::CODE_ERROR;
        } else {
            $r->data = $result;
        }
        return $r;
    }

    public function getMaps()
    {
        $r = new Api_Responce();

        $reader = new p19115_Service_Data();
        $result = $reader->getMaps();

        if (!$result) {
            $r->status = false;
            $r->code = Api_Responce::CODE_ERROR;
        } else {
            $r->data = $result;
        }
        return $r;
    }
}