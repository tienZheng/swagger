<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/12/29
 * Time: 11:28 PM
 */

namespace Tien\Swagger\traits;


trait TienValidateMiddle
{
    use Tien;

    protected $errorMsg;

    /**
     *
     * @param $request
     * @param array $checkData
     * @param bool $isThrow
     * @return bool
     * @throws \Tien\Swagger\exceptions\Exception
     */
    public function tienCheck($request, array $checkData = [], bool $isThrow)
    {
        $this->request = $request;

        //初始化
        $this->tienInit('', $isThrow);

        //如果不存在验证类，表示不需要验证，直接返回 true
        if (!$this->validate) {
            return true;
        }

        //需要验证的信息
        $validateParam = $this->validate->{$this->action} ?? [];
        $checkData = empty($checkData) ? $request->param() : $checkData;

        //验证
        $verify = $this->validate->check($checkData, $validateParam);

        //如果验证不通过
        if (!$verify) {
            $this->errorMsg =  $this->handleError($this->validate->getError());
        }
        return $verify;
    }
}