<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/15
 * Time: 10:42 AM
 */

namespace Tien\Swagger\traits;


trait TienValidate
{


    /**
     * 替换错误信息，汉化
     *
     * @param string $error
     * @param array $msgArr
     * @return string
     */
    public function handleErrorMsg(string $error, array $msgArr = []): string
    {
        if (empty($msgArr)) {
            return $error;
        }
        foreach ($msgArr as $key => $value) {
            if (strpos($error, $key) !== false && isset($msgArr[$key])) {
                return str_replace($key, $key."({$msgArr[$key]})", $error);
            }
        }
        return $error;
    }

}