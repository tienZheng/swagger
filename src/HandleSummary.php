<?php
/**
 * Created by PhpStorm.
 * User: Tien
 * Date: 2018/11/27
 * Time: 10:24 AM.
 */

namespace Tien\Swagger;

class HandleSummary extends Handle
{
    protected $schemes = '{"http","https"}';

    protected $host;

    protected $produces = 'application/json';

    protected $version;

    protected $title;

    protected $description;

    public function __construct(string $filePath, $isCreate = true, $filename = 'Swagger.php')
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

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function create()
    {
        if (!$this->isCreate) {
            return true;
        }
        //验证是否存在，存在即删除
        if ($this->verifyIsExists($this->host)) {
            $this->fileContents = '<?php';
        }
        $this->write(PHP_EOL.$this->formatContent([]));
        //写入文件
        if (!$this->writeFile()) {
            throw new FileException('写入文件失败');
        }

        return true;
    }

    public function formatContent(array $param): string
    {
        return $this->notesStart.PHP_EOL.' * '.Config::$swagger.'('.PHP_EOL.
            " *\t   schemes={$this->schemes},".PHP_EOL.
            " *\t   host=\"{$this->host}\",".PHP_EOL.
            " *\t   consumes={\"multipart/form-data\"},".PHP_EOL.
            " *\t   produces={\"$this->produces\"},".PHP_EOL.
            " *\t   ".Config::$info.'('.PHP_EOL.
            " *\t     version=\"$this->version\",".PHP_EOL.
            " *\t     title=\"$this->title\",".PHP_EOL.
            " *\t     description=\"$this->description\"".PHP_EOL.
            " *\t),".PHP_EOL.
            ' * )'.PHP_EOL.
            ' '.$this->notesEnd;
    }

    public function host(string $host)
    {
        $this->host = $host;

        return $this;
    }

    public function schemes(array $schemes)
    {
        $this->schemes = '';
        foreach ($schemes as $scheme) {
            $this->schemes .= "{$scheme},";
        }
        $this->schemes = '{'.$this->schemes.'}';

        return $this;
    }

    public function produces(string $produces)
    {
        $this->produces = $produces;

        return $this;
    }

    public function version(string $version)
    {
        $this->version = $version;

        return $this;
    }

    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }
}
