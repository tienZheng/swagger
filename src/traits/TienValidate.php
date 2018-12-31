<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/15
 * Time: 10:42 AM.
 */

namespace Tien\Swagger\traits;

trait TienValidate
{
    use Tien;

    /**
     * @var array
     */
    protected $specialKeys = ['tienAppend'];

    /**
     * 替换错误信息，汉化.
     *
     * @param string $error
     * @param array  $msgArr
     *
     * @return string
     */
    public function handleErrorMsg(string $error, array $msgArr = []): string
    {
        if (empty($msgArr)) {
            return $error;
        }

        foreach ($msgArr as $key => $value) {
            if (false !== strpos($error, $key) && isset($msgArr[$key])) {
                return str_replace($key, $key."({$msgArr[$key]})", $error);
            }
        }

        return $error;
    }

    /**
     * 验证传参的键是否合法，若是，返回true.
     *
     * @param array $param
     * @param array $verifyInfo
     *
     * @return bool|string
     */
    public function verifyKey(array $param, array $verifyInfo)
    {
        $keys = array_keys($param);
        $verifyKeys = array_keys($verifyInfo);
        foreach ($keys as $key) {
            if (in_array($key, $this->specialKeys)) {
                continue;
            }
            if (!in_array($key, $verifyKeys)) {
                return $key.'传参非法';
            }
        }

        return true;
    }

    /**
     * :排序规则.
     *
     * @param string $value
     * @param string $rule
     *
     * @return bool|string
     */
    public function order(string $value, string $rule)
    {
        $orderArr = explode('|', $value);
        $rules = explode(',', $rule);
        $error = '排序字段规则错误，';
        foreach ($orderArr as $order) {
            $item = explode(',', $order);
            if (count($item) > 2) {
                return false;
            }
            if (!in_array($item[0], $rules)) {
                return "{$error}{$item[0]}不在{$rule}范围之中";
            }
            if (($item[1] ?? '') && !in_array($item[1], ['asc', 'desc'])) {
                return "{$error}{$item[1]}应是asc或desc}";
            }
        }

        return true;
    }

    /**
     * :禁止某些字段.
     *
     * @param string $value
     * @param string $rule
     * @param array  $data
     *
     * @return string
     */
    public function forbidden(string $value, string $rule, array $data)
    {
        return $value.'('.array_flip($data)[$value].'):该字段是禁止存在的';
    }
}
