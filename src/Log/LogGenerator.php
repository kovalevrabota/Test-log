<?php
namespace Test\Log;

use Exception;

/**
 * Class LogGenerator
 * @package Test\Log
 */
class LogGenerator 
{
    /**
     * @var mixed
     */
    private $handler = null;

    /**
     * LogGenerator constructor.
     *
     * @param string $path
     */
    public function __construct(string $path) 
    {
        if(!($this->handler = fopen($path, "r"))) {
            throw new Exception("Cannot open the file");
        }
    }

    /**
     * Log generator.
     *
     * @return mixed
     */
    public function generator() : mixed
    {
        if(!$this->handler) {
            throw new Exception("Invalid file pointer");
        }

        while(!feof($this->handler)) {
            yield fgets($this->handler);
        }
    }

    /**
     * LogGenerator destruct.
     *
     * @param string $path
     */
    public function __destruct() 
    {
        if($this->handler) {
            fclose($this->handler);
        }
    }
}