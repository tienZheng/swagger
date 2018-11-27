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

    protected $errorMsg;

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
     * @param $request
     *
     * @return bool
     *
     * @throws \Tien\Swagger\exceptions\Exception
     */
    public function tienCheck($request)
    {
        $this->request = $request;

        $this->getPath();
        $this->getAction();

        //获取到验证类
        $this->getValidate();
        $this->getApiParam();
        $validateParam = $this->validate->{$this->action} ?? [];
        $verify = $this->validate->check($request->param(), $validateParam);
        $this->getValidateMsg();
        if (!$verify) {
            $this->errorMsg = $this->handleErrorMsg($this->validate->getError(), $this->validateMsgParam);
        }

        return $verify;
    }

    /**
     * 验证传参的键是否合法，若是，返回true.
     *
     * @param $param
     * @param $verifyInfo
     *
     * @return bool|string
     */
    public function verifyKey($param, $verifyInfo)
    {
        $keys = array_keys($param);
        $verifyKeys = array_keys($verifyInfo);
        foreach ($keys as $key) {
            if (!in_array($key, $verifyKeys)) {
                return $key.'传参非法';
            }
        }

        return true;
    }
}
