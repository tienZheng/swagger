<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/12/29
 * Time: 11:35 PM.
 */

namespace Tien\Swagger\traits;

trait TienTools
{
    /**
     * :获取排序条件.
     *
     * @param array    $param
     * @param \Closure $next
     *
     * @return array
     */
    public function getOrder(array $param, \Closure $next = null): array
    {
        if (!($param['order'] ?? '')) {
            return [];
        }

        $orderArr = explode('|', $param['order']);
        $order = [];
        foreach ($orderArr as $value) {
            $item = explode(',', $value);
            $item[0] = $next($item[0]);
            if (($item[1] ?? '') && 'asc' == $item[1]) {
                $order[$item[0]] = 'asc';
            } else {
                $order[$item[0]] = 'desc';
            }
        }

        return $order;
    }
}
