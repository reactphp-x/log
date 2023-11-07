<?php

namespace Wpjscc\Log;

use React\Stream\WritableStreamInterface;
use React\Filesystem\Factory;
use Evenement\EventEmitter;

class FileWriteStream extends EventEmitter implements WritableStreamInterface
{

    private $filesystem;
    private $path;


    private $writable = true;
    private $closed = false;

    private $date = false;

    private $handling = false;

    private $mode = \FILE_APPEND;

    private $datas = [];


    public function __construct($path, $mode = \FILE_APPEND, $date = false)
    {
        $this->filesystem = Factory::create();
        $this->path = $path;
        $this->mode = $mode;
        $this->date = $date;
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function write($data)
    {
        if (!$this->writable) {
            return false;
        }

        $this->datas[] = $data;
        $this->handle();
        return true;
    }


    public function handle()
    {
      
        if ($this->handling) {
            return;
        }

        $this->handling = true;

        if (empty($this->datas)) {
            $this->handling = false;
            return;
        }

        $data = \array_shift($this->datas);

        $this->filesystem->file($this->getFilePath())
            ->putContents($data, $this->mode)
            ->then(function () {
                $this->handling = false;
                $this->handle();
            });

    }

    protected function getFilePath()
    {
        if ($this->date) {
            $dir = \dirname($this->path);
            $basename = \basename($this->path);
            return $dir . \DIRECTORY_SEPARATOR . \date('Y-m-d') . '-' . $basename;
        }
        return $this->path;
    }

    public function end($data = null)
    {
        if ($data !== null) {
            $this->write($data);
        }
        $this->writable = false;

        if (empty($this->datas)) {
            $this->close();
        }
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }
        $this->closed = true;
        $this->writable = false;
        $this->datas = [];

        $this->emit('close');
        $this->removeAllListeners();
    }
}
