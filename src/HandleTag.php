<?php

namespace Tien\Swagger;

use Tien\Swagger\exceptions\FileException;

class HandleTag extends Handle
{
    /**
     * HandleTag constructor.
     *
     * @param string $filePath
     * @param bool   $isCreate
     * @param string $filename
     *
     * @throws FileException
     */
    public function __construct(string $filePath, $isCreate = true, $filename = 'Tag.php')
    {
        $this->isCreate = $isCreate;
        if (!$isCreate) {
            return;
        }
        $this->filePath = $filePath;
        $this->filename = $filePath.$filename;
        $this->crateFilePath();
        $this->createFile();
        $this->verifyFileIsExists();
        $this->getFileContent();
    }

    /**
     * 格式化tag内容.
     *
     * @param array $param
     *
     * @return string
     */
    protected function formatContent(array $param): string
    {
        $tagName = ucfirst(current(array_keys($param)));
        $description = current(array_values($param));

        return $this->notesStart.PHP_EOL.' * '.Config::$tag.'('.PHP_EOL." *\t   name=\"".$tagName.'",'.PHP_EOL." *\t   description=\"".$description.'",'.PHP_EOL.' *  ),'.PHP_EOL.$this->notesEnd.PHP_EOL;
    }

    /**
     * 创建标签.
     *
     * @throws FileException
     *
     * @return bool
     */
    public function create(): bool
    {
        if (!$this->isCreate) {
            return true;
        }

        foreach ($this->content as $value) {
            $tagName = ucfirst(current(array_keys($value)));
            $this->verifyIsExists($tagName);

            //如果存在且没有强制更新
            if ($this->isExists && !$this->isForced) {
                continue;
            }

            //如果存在，删除
            if ($this->isExists) {
                $this->delete($tagName);
            }

            //创建
            $this->write($this->formatContent($value));
        }
        //写入文件
        if (!$this->writeFile()) {
            throw new FileException('写入文件失败');
        }

        return true;
    }

    /**
     * 更新标签.
     *
     * @return bool
     *
     * @throws FileException
     */
    public function update(): bool
    {
        if (!$this->isCreate) {
            return true;
        }

        foreach ($this->content as $value) {
            $tagName = ucfirst(current(array_keys($value)));

            //验证是否存在
            $this->verifyIsExists($tagName);

            //如果存在，删除
            if ($this->isExists) {
                $this->delete($tagName);
            }

            //写入新建内容
            $this->write($this->formatContent($value));
        }
        //写入文件
        if (!$this->writeFile()) {
            throw new FileException('写入文件失败');
        }

        return true;
    }
}
