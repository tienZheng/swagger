<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/15
 * Time: 10:48 AM.
 */

namespace Tien\Swagger\traits;

use Tien\Swagger\exceptions\Exception;
use Tien\Swagger\HandleMethod;
use Tien\Swagger\HandleSummary;
use Tien\Swagger\HandleTag;

trait Tien
{
    protected $path;

    /**
     * 控制器调用的方法.
     *
     * @var string
     */
    protected $action;

    /**
     * 验证类.
     *
     * @var
     */
    protected $validate;

    /**
     * api需要的数据.
     *
     * @var array
     */
    protected $apiParam;

    /**
     * 验证提示信息.
     *
     * @var array
     */
    protected $validateMsgParam;

    protected $filePath = '';

    protected $isDev = false;

    /**
     * 创建方法.
     *
     * @return HandleMethod
     *
     * @throws Exception
     * @throws \Tien\Swagger\exceptions\FileException
     * @throws \Tien\Swagger\exceptions\InvalidArgumentException
     */
    public function tienMethod()
    {
        $handleMethod = new HandleMethod($this->filePath, $this->path, $this->isDev);
        $handleMethod->setContent($this->apiParam);

        return $handleMethod;
    }

    public function tienInit($filePath = '')
    {
        $this->getFilePath($filePath);
        $this->getPath();
        $this->getAction();

        //获取验证对象
        $this->getValidate();

        //获取 api 文档参数
        $this->getApiParam();

        $this->isDev = $this->verifyIsDev();
    }

    protected function getFilePath($filePath = '')
    {
        $this->filePath = $filePath ?: \think\facade\Env::get('APP_PATH').'swagger'.DIRECTORY_SEPARATOR;
    }

    /**
     * 创建标签.
     *
     * @return HandleTag
     *
     * @throws \Tien\Swagger\exceptions\FileException
     */
    public function tienTag()
    {
        return new HandleTag($this->filePath, $this->isDev);
    }

    /**
     * 文档说明.
     *
     * @return HandleSummary
     */
    public function tienSummary()
    {
        return new HandleSummary($this->filePath, $this->isDev);
    }

    /**
     * 处理错误信息.
     *
     * @param string $error
     *
     * @return string
     */
    public function handleErrorMsg(string $error): string
    {
        if (!$this->validateMsgParam) {
            $this->getValidateMsg();
        }

        return $this->validate->handleErrorMsg($error, $this->validateMsgParam);
    }

    /**
     * 验证是否是开发环境.
     *
     * @return bool
     */
    public function verifyIsDev(): bool
    {
        return \think\facade\Env::get('app_debug');
    }

    /**
     * 获取请求路径.
     */
    protected function getPath()
    {
        if (!$this->path) {
            $this->path = $this->request->routeInfo()['rule'];
        }
    }

    /**
     * 获得操作的方法.
     */
    protected function getAction()
    {
        $this->action = $this->request->action();
    }

    /**
     * 获得验证对象
     *
     * @throws Exception
     */
    protected function getValidate()
    {
        $class = 'app\\'.$this->request->module().'\\validate\\'.$this->request->controller();

        try {
            $this->validate = new $class();
        } catch (\Exception $e) {
            throw new Exception('验证类不存在');
        }
    }

    /**
     * 获取api需要的参数.
     */
    protected function getApiParam()
    {
        $paramName = $this->action.'Msg';
        $specialApiParam = $this->validate->specialApiParam ?? [];
        $this->apiParam = array_merge($this->validate->{$paramName} ?? [], $specialApiParam);
    }

    /**
     * 获得验证提示信息.
     */
    protected function getValidateMsg()
    {
        foreach ($this->apiParam as $key => $value) {
            $msg = explode(',', $value[1] ?? $key);
            $this->validateMsgParam[$key] = current($msg);
        }
    }
}
