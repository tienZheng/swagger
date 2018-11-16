<?php

namespace Tien\Swagger;

use Tien\Swagger\exceptions\FileException;
use Tien\Swagger\exceptions\InvalidArgumentException;

class HandleMethod extends Handle
{
    /**
     * 路径.
     *
     * @var string
     */
    protected $path;

    /**
     * 简介.
     *
     * @var string
     */
    protected $summary;

    /**
     * 说明.
     *
     * @var string
     */
    protected $description;

    /**
     * 方法请求方式.
     *
     * @var array
     */
    protected $methodParam = [
        'post' => 'formData',
        'get' => 'query',
    ];

    /**
     * 请求方法.
     *
     * @var string
     */
    protected $method = 'get';

    /**
     * 请求标签名.
     *
     * @var string
     */
    protected $tagName;

    /**
     * 请求方法名.
     *
     * @var string
     */
    protected $methodName;

    /**
     * 连接符.
     *
     * @var string
     */
    protected $glue = ' * ';

    /**
     * 二级链接符.
     *
     * @var string
     */
    protected $secGlue = "\t\t";

    /**
     * 制表符.
     *
     * @var string
     */
    protected $tabs = "\t";

    /**
     * 注释开始字符串.
     *
     * @var string
     */
    protected $notesStart = '/**';

    /**
     * 注释结束字符串.
     *
     * @var string
     */
    protected $notesEnd = '*/';

    /**
     * 换行符.
     *
     * @var string
     */
    protected $rowEnd = '",'.PHP_EOL;

    /**
     * 操作方法.
     *
     * @var string
     */
    protected $operationId;

    /**
     * swagger方法固定开头.
     *
     * @var string
     */
    protected $swaggerMethod;

    /**
     * @var string
     */
    protected $Parameter = '';

    /**
     * HandleMethod constructor.
     *
     * @param string $filePath
     * @param bool   $isCreate
     * @param string $path
     *
     * @throws InvalidArgumentException
     * @throws exceptions\FileException
     */
    public function __construct(string $filePath, string $path, $isCreate = true)
    {
        $this->filePath = $filePath;
        $this->isCreate = $isCreate;
        if (!$isCreate) {
            return;
        }

        $this->path = $path;
        $this->getTagName();
        $this->filename = $filePath.$this->tagName.'.php';
        $this->crateFilePath();
        $this->createFile();
        $this->verifyFileIsExists();
        $this->getoperationId();
        $this->path = DIRECTORY_SEPARATOR.$this->path;
        $this->getFileContent();
    }

    /**
     *获取标签名.
     *
     * @throws InvalidArgumentException
     */
    protected function getTagName()
    {
        $this->tagName = ucfirst(substr($this->path, 0, strpos($this->path, DIRECTORY_SEPARATOR)));
        if (!$this->tagName) {
            throw new InvalidArgumentException('传参不符合要求');
        }
    }

    /**
     * 获取操作方法.
     *
     * @throws InvalidArgumentException
     */
    protected function getOperationId()
    {
        $this->operationId = substr($this->path, strrpos($this->path, DIRECTORY_SEPARATOR) + 1);
        if (!$this->operationId) {
            throw new InvalidArgumentException('传参不符合要求');
        }
    }

    /**
     * get请求
     *
     * @return $this
     */
    public function get()
    {
        $this->method = 'get';

        return $this;
    }

    /**
     * post请求
     *
     * @return $this
     */
    public function post()
    {
        $this->method = 'post';

        return $this;
    }

    /**
     * 总结.
     *
     * @param string $summary
     *
     * @return $this
     */
    public function summary(string $summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * 详细说明.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param array $param ['sceneId' => ['integer', '渠道id', true]]
     *
     * @return string
     */
    protected function formatContent(array $param): string
    {
        $key = current(array_keys($param));
        $methodParam = $this->methodParam[strtolower($this->method)];
        $type = $this->getType($param);
        $description = $param[$key][1] ?? $key;
        $required = ($param[$key][2] ?? '') == true ? 'true' : 'false';

        return "{$this->glue}{$this->tabs}".Config::$parameter.'('.PHP_EOL.
            $this->glue.$this->secGlue.'name="'.$key.$this->rowEnd.
            $this->glue.$this->secGlue.'in="'.$methodParam.$this->rowEnd.
            $this->glue.$this->secGlue.'description="'.$description.$this->rowEnd.
            $this->glue.$this->secGlue.'required='.$required.','.PHP_EOL.
            $this->glue.$this->secGlue.'type="'.$type.$this->rowEnd.
            "{$this->glue}{$this->tabs}),";
    }

    /**
     * 新建注释.
     *
     * @return bool|int
     *
     * @throws FileException
     */
    public function create()
    {
        if (!$this->isCreate) {
            return true;
        }

        $this->verifyIsExists($this->path);

        //如果存在且没有强制刷新
        if ($this->isExists && !$this->isForced) {
            return true;
        }

        //如果存在，删除
        if ($this->isExists) {
            $this->delete($this->path);
        }

        //新建
        $this->write($this->formatNotes());

        //写入文件
        if (!$this->writeFile()) {
            throw new FileException('写入文件失败');
        }

        return true;
    }

    /**
     * 更新.
     *
     * @return bool|int
     *
     * @throws FileException
     */
    public function update()
    {
        if (!$this->isCreate) {
            return true;
        }

        $this->verifyIsExists($this->path);

        //如果存在，删除
        if ($this->isExists) {
            $this->delete($this->path);
        }

        //新建
        $this->write($this->formatNotes());

        //写入文件
        if (!$this->writeFile()) {
            throw new FileException('写入文件失败');
        }

        return true;
    }

    /**
     * 完成一条完整的注释.
     *
     * @return string
     */
    private function formatNotes()
    {
        $rowStart = $this->glue.$this->tabs;
        $this->getSwaggerMethod();
        $notes = $this->notesStart.PHP_EOL.
            $this->glue.$this->swaggerMethod.'('.PHP_EOL.
            $rowStart.'path="'.$this->path.$this->rowEnd.
            $rowStart.'tags={"'.$this->tagName.'"},'.PHP_EOL.
            $rowStart.'operationId="'.$this->operationId.$this->rowEnd.
            $rowStart.'summary="'.$this->summary.$this->rowEnd.
            $rowStart.'description="'.$this->description.$this->rowEnd.
            $rowStart.'consumes={"multipart/form-data"},'.PHP_EOL.
            $rowStart.'produces={"multipart/form-data"},'.PHP_EOL;
        //拼接param参数
        foreach ($this->content as $key => $param) {
            $parameter = [$key => $param];
            $notes .= $this->formatContent($parameter).PHP_EOL;
        }
        //拼接返回信息
        $notes .= $rowStart.Config::$response.'('.PHP_EOL.
            $rowStart.$this->tabs.'response=405,'.PHP_EOL.
            $rowStart.$this->tabs.'description="Invalid input",'.PHP_EOL.
            $this->glue.$this->tabs.'),'.PHP_EOL;
        //拼接notes尾部
        $notes .= $this->glue.')'.PHP_EOL.' '.$this->notesEnd.PHP_EOL;

        return $notes;
    }

    /**
     * 获得参数的类型.
     *
     * @param array $param ['sceneId' => ['integer','渠道id']]
     *
     * @return string
     */
    private function getType(array $param): string
    {
        $type = 'string';
        $key = current(array_keys($param));

        return $param[$key][0] ?? $type;
    }

    /**
     * 确定swagger请求方法.
     */
    private function getSwaggerMethod()
    {
        if ('get' == strtolower($this->method)) {
            $this->swaggerMethod = Config::$methodGet;

            return;
        }
        $this->swaggerMethod = Config::$methodPost;
    }
}
