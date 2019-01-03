<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/26
 * Time: 2:52 PM.
 */

namespace Tien\Swagger\traits;

use Tien\Swagger\HandleMethod;

trait TienSwaggerMiddle
{
    use Tien;

    /**
     *: 生成 swagger 文档中间件处理 handle.
     *
     * @param $request
     * @param string $filePath
     * @param bool   $isThrow
     *
     * @return mixed
     *
     * @throws \Tien\Swagger\exceptions\Exception
     * @throws \Tien\Swagger\exceptions\FileException
     * @throws \Tien\Swagger\exceptions\InvalidArgumentException
     */
    public function tienHandle($request, string $filePath = '', bool $isThrow = true)
    {
        //是否是开发环境, 不是开发环境，结束操作
        if (!$this->verifyIsDev()) {
            return true;
        }

        $this->request = $request;

        //初始化
        $this->tienInit($filePath, $isThrow);

        //如果为空，表示当前类不需要生成文档和验证
        if (!$this->validate) {
            return true;
        }

        //获取到 api 文本简介信息
        $apiTextName = $this->action.'Text';
        $apiText = $this->validate->{$apiTextName};
        $handleMethod = new HandleMethod($this->filePath, $this->path);
        $handleMethod->setContent($this->apiParam);

        //确定请求方法
        $method = strtolower($request->method());
        if ('post' == $method) {
            $handleMethod->post();
        } elseif ('get' == $method) {
            $handleMethod->get();
        } elseif ('put' == $method) {
            $handleMethod->put();
        } elseif ('delete' == $method) {
            $handleMethod->methodDelete();
        }

        //生成解析文档
        $handleMethod->summary($apiText['summary'] ?? '');
        $handleMethod->description($apiText['description'] ?? $apiText['summary'] ?? '');

        return $handleMethod->create();
    }
}
