<?php

use Illuminate\Support\Arr;

function get_current_datetime()
{
    $dt = new DateTime('now', new DateTimeZone("Asia/phnom_penh"));
    $now = $dt->format('Y-m-d H:i:s');
    return $now;
}

function randomDigits($length)
{
    $digits = '';
    $numbers = range(0, 9);
    shuffle($numbers);
    for ($i = 0; $i < $length; $i++) {
        global $digits;
        $digits .= $numbers[$i];
    }

    return $digits;
}

function currency($amount)
{
    return '$' . number_format($amount, 2, '.', ',');
}

function substruct_two_datetime($date1, $date2)
{
    $datetime1 = strtotime($date1);
    $datetime2 = strtotime($date2);
    $secs = $datetime2 - $datetime1;
    $hours = $secs / 3600;
    return $hours;
}

/**
 * Sort list of data
 *
 * @param  $data
 * @return Illuminate\Database\Eloquent\Builder
 */
function sortList($data, $params)
{
    if (filled($params['order'])) {
        return $data->orderBy($params['order'], $params['sort']);
    }

    return $data->orderBy('created_at', 'desc');
}

/**
 * Count total data in a list
 *
 * @param  $data
 * @return Illuminate\Database\Eloquent\Builder
 */
function countList($data)
{
    return $data
        ->count();
}

/**
 * set offset & limit for list of data
 *
 * @param  $data
 * @param $params
 * @return Illuminate\Database\Eloquent\Collection
 */
function offsetLimit($data, $params)
{   

    $page = $params['page'];
    $limit = $params['limit'];
    $offset = ($limit * $page) - $limit;
    return $data
        ->offset($offset)
        ->limit($limit)
        ->get();
}

/**
 * Get All data
 *
 * @param $data
 * @return mixed
 */
function getAll($data)
{
    return $data->get();
}

/**
 * set offset & limit for list of data
 *
 * @param  $list
 * @param $params
 * @return array
 */
function listLimit($list, $params)
{
    $list = sortList($list, $params);

    $total = countList($list);

    $list = offsetLimit($list, $params);

    return compact('list', 'total');
}

/**
 * sort for list of all data
 *
 * @param $list
 * @param $params
 * @return array
 */
function listAll($list, $params)
{
    $list = sortList($list, $params);

    $total = countList($list);

    $list = getAll($list);

    return compact('list', 'total');
}

/**
 * Mapping request to store
 *
 * @param $fields
 * @param $request
 * @return array
 */
function mapRequest($fields, $request)
{
    $value = [];
    foreach ($fields as $key => $field) {
        if (Arr::has($request, $field)) {
            $value[$field] = $request[$field];
        }
    }

    return $value;
}
