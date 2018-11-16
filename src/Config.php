<?php

namespace Tien\Swagger;

class Config
{
    /**
     * swagger开头.
     *
     * @var string
     */
    public static $swagger = '@SWG\Swagger';

    /**
     * get请求方法.
     *
     * @var string
     */
    public static $methodGet = '@SWG\Get';

    /**
     * post请求方法.
     *
     * @var string
     */
    public static $methodPost = '@SWG\Post';

    /**
     * 成员变量.
     *
     * @var string
     */
    public static $parameter = '@SWG\Parameter';

    /**
     * tag标签.
     *
     * @var string
     */
    public static $tag = '@SWG\Tag';

    /**
     * 返回.
     *
     * @var string
     */
    public static $response = '@SWG\Response';
}
