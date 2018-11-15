<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/14
 * Time: 10:00 PM
 */

namespace Tien\Swagger;


use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Tien\Swagger\exceptions\FileException;

abstract class Handle
{
    /**
     * 文件存放的路径
     *
     * @var string
     */
    protected $filePath;

    /**
     * 文件名
     *
     * @var string
     */
    protected $filename;

    /**
     * 是否已存在
     *
     * @var bool
     */
    protected $isExists;

    /**
     * 是否强制生成
     *
     * @var bool
     */
    protected $isForced;

    /**
     * 内容
     *
     * @var array
     */
    protected $content;


    /**
     * 文件内容　
     *
     * @var string
     */
    protected $fileContents;

    /**
     * 注释开始字符串
     *
     * @var string
     */
    protected $notesStart = '/**';

    /**
     * 注释结束字符串
     *
     * @var string
     */
    protected $notesEnd = '*/';

    /**
     * 是否创建
     *
     * @var bool
     */
    protected $isCreate;


    /**
     * 创建内容
     *
     * @return bool|int
     */
    abstract public function create();

    /**
     * 更新内容　
     *
     * @return bool|int
     */
    abstract public function update();

    /**
     * 删除内容
     *
     * @param string $name
     * @return mixed
     */
    public function delete(string $name)
    {
        $tagNameIndex       = strpos($this->fileContents, $name);
        $tagStartIndex      = strrpos(substr($this->fileContents, 0, $tagNameIndex), $this->notesStart);
        $tagEndIndex        = strpos($this->fileContents, $this->notesEnd, $tagNameIndex);
        $deleteLength       = $tagEndIndex + strlen($this->notesEnd) - $tagStartIndex;
        $this->fileContents = substr_replace($this->fileContents, '', $tagStartIndex, $deleteLength);
        return $this;
    }

    /**
     * 格式化内容
     *
     * @param array $param
     * @return string
     */
    abstract protected function formatContent(array $param): string ;


    /**
     * 设置内容
     *
     * @param array $content
     * @return $this
     */
    public function setContent(array $content)
    {
        $this->content = $content;
        return $this;
    }


    /**
     * 是否强制刷新
     *
     * @return $this
     */
    public function isForced()
    {
        $this->isForced = true;
        return $this;
    }

    /**
     * 是否创建
     *
     * @param bool $isCreate
     * @return $this
     */
    public function isCreate($isCreate = true)
    {
        $this->isCreate = $isCreate;
        return $this;
    }



    /**
     * 读取文章内容
     */
    protected function getFileContent()
    {
        $this->fileContents = file_get_contents($this->filename);
    }

    /**
     * 写文章
     *
     * @return bool|int
     */
    protected function writeFile()
    {
        return file_put_contents($this->filename, $this->fileContents);
    }

    /**
     * 写入内容
     *
     * @param string $content
     */
    protected function write(string $content)
    {
        $this->fileContents .= $content;
    }

    /**
     * @throws FileException
     */
    protected function verifyFileIsExists()
    {
        if (!file_exists($this->filename)) {
            throw new FileException('文件不存在，且创建失败');
        }
    }

    /**
     * 创建文件夹
     */
    protected function crateFilePath()
    {
        //如果文件路径不存在，循环创建
        if (!is_dir($this->filePath)) {
            mkdir($this->filePath, 0777, true);
        }
    }

    /**
     * 创建文件
     */
    protected function createFile()
    {
        if (!file_exists($this->filename)) {
            file_put_contents($this->filename, '<?php'.PHP_EOL);
        }
    }

    /**
     * 验证是否存在
     *
     * @param string $name
     * @return $this
     */
    protected function verifyIsExists(string $name)
    {
        $fileContent = file_get_contents($this->filename);
        if (strpos($fileContent, '"'.$name.'"') !== false) {
            $this->isExists = true;
        } else {
            $this->isExists = false;
        }
        return $this;
    }






}