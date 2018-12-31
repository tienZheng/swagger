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

    protected $request;

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

    /**
     * :初始化 tien.
     *
     * @param string $filePath
     * @param bool   $isThrow
     *
     * @throws Exception
     */
    public function tienInit(string $filePath = '', bool $isThrow = true)
    {
        $this->getFilePath($filePath);
        $this->getPath();
        $this->getAction();

        //获取验证对象
        $this->getValidate($isThrow);

        //获取 api 文档参数
        $this->getApiParam();

        //检验是否是测试环境
        $this->isDev = $this->verifyIsDev();
    }

    /**
     * :获取 swagger 写入的文件路径.
     *
     * @param string $filePath
     */
    protected function getFilePath(string $filePath = '')
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
    public function handleError(string $error): string
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
            $this->path = $this->request->routeInfo()['rule'] ?? $this->request->path();
        }
    }

    /**
     * 获得操作的方法.
     */
    protected function getAction()
    {
        $this->action = $this->request->action(true);
    }

    /**
     * 获得验证对象
     *
     * @throws Exception
     */
    protected function getValidate($isThrow = true)
    {
        $class = 'app\\'.$this->request->module().'\\validate\\'.$this->request->controller();
        if (class_exists($class)) {
            $this->validate = new $class();

            return;
        } elseif (!$isThrow) {
            return;
        }

        throw new Exception($class.'验证类不存在');
    }

    /**
     * 获取api需要的参数.
     */
    protected function getApiParam()
    {
        $paramName = $this->action.'Msg';
        $specialApiParam = $this->validate->specialApiParam ?? [];
        $this->apiParam = array_merge($specialApiParam, $this->validate->{$paramName} ?? []);
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
