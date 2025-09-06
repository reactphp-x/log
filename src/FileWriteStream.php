<?php

namespace ReactphpX\Log;

use React\Stream\WritableStreamInterface;
use React\Filesystem\Factory;
use React\Filesystem\AdapterInterface;
use Evenement\EventEmitter;

class FileWriteStream extends EventEmitter implements WritableStreamInterface
{

    private $filesystem;
    private $path;

    private $writable = true;
    private $closed = false;
    

    public function __construct($path, ?AdapterInterface $adapter = null)
    {   
        $this->filesystem = $adapter ?? Factory::create();
        $this->path = $path;
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

        $this->filesystem->file($this->path)->putContents($data, \FILE_APPEND);
    }



    public function end($data = null)
    {
        if ($data !== null) {
            $this->write($data);
        }
        $this->writable = false;
        $this->close();
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }
        $this->closed = true;
        $this->writable = false;

        $this->emit('close');
        $this->removeAllListeners();
    }
}
