<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/26
 * Time: 2:52 PM
 */

namespace Tien\Swagger\traits;


use Tien\Swagger\HandleMethod;

trait TienMiddleware
{
    use Tien;

    protected $request;

    /**
     *
     *
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws \Tien\Swagger\exceptions\Exception
     * @throws \Tien\Swagger\exceptions\FileException
     * @throws \Tien\Swagger\exceptions\InvalidArgumentException
     */
    public function handle($request, \Closure $next)
    {
        $this->request = $request;

        //是否是开发环境, 不是开发环境，结束操作
        if (!$this->verifyIsDev()) {
            return $next($request);
        }

        $this->getFilePath();
        $this->getPath();
        $this->getAction();

        //获取到验证类
        $this->getValidate();

        //获取到 api 信息
        $this->getApiParam();
        //获取到 api 文本简介信息
        $apiTextName = $this->action.'Text';
        $apiText = $this->validate->{$apiTextName};
        $method = strtolower($request->method());

        $handleMethod = new HandleMethod($this->filePath, $this->path);
        $handleMethod->setContent($this->apiParam);
        if ($method == 'post') {
            $handleMethod->post();
        } elseif ($method == 'get') {
            $handleMethod->get();
        }
        $handleMethod->summary($apiText['summary'] ?? '');
        $handleMethod->description($apiText['description'] ?? $apiText['summary'] ?? '');
        $handleMethod->create();
        return $next($request);
    }
}