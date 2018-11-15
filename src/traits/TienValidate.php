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
     * @param $param
     * @param $error
     * @param array $msgArr 中文提示语数组['sceneId' => '渠道id']
     * @return string
     */
    public function handleErrorMsg($param, $error, $msgArr = []): string
    {
        if (empty($param) || !is_array($param)) {
            return $error;
        }
        foreach ($param as $key => $val) {
            if (strpos($error, $key) !== false && isset($msgArr[$key])) {
                return str_replace($key, $key."({$msgArr[$key]})", $error);
            }
        }
        return $error;
    }
}