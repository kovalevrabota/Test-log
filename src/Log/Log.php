<?php
namespace Test\Log;

use Test\Log\LogGenerator;

/**
 * Class Log
 * @package Test\Log
 */
class Log 
{
    /**
     * @var string
     */
    private static $format = '~\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}~';

    /**
     * @var null|string
     */
    private $path = null;

    /**
     * @var null|string
     */
    private $buffer = null;

    /**
     * Log constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Get line log.
     *
     * @param object $generator
     * @return mixed
     */
    private function getLine(object $generator) : mixed
    {
        foreach($generator as $item) {
            if(empty($this->buffer)) {
                $this->buffer = $item;
            }else {
                preg_match(self::$format, $item, $matches);

                if(!empty($matches) && count($matches)) {
                    $result = $this->buffer;
                    $this->buffer = $item;

                    return $result;
                }else {
                    $this->buffer .= $item;
                    continue;
                }
            }
        }

        if($this->buffer == null) {
            return false;
        }

        $result = $this->buffer;
        $this->buffer = null;
        
        return $result;
    }
    
    /**
     * Search in log.
     *
     * @param string $key
     * @param array $values
     * @return mixed
     */
    public function search(string $key, array $values) : mixed
    {
        $generator = new LogGenerator($this->path);

        while($line = $this->getLine($generator->generator())) {
            $lineExplode = explode(": ", $line);

            if(count($lineExplode) === 2) {
                $pos = strripos($lineExplode[1], 'array');
                if ($pos === false) {
                    continue;
                }

                try {
                    eval('$lineEval = ' . $lineExplode[1].';');
                } catch (Throwable $t) {
                    $lineEval = null;
                }

                if($lineEval) {
                    $searchKeyValue = array();

                    if(!empty($lineEval[$key])) {
                        $searchKeyValue[] = $lineEval[$key];
                    }

                    $searchKeyValue = array_merge($searchKeyValue, array_column($lineEval, $key));
                    $intersect = array_intersect($searchKeyValue, $values);

                    if(!empty($intersect) && count($intersect)) {
                        yield $line;
                    }
                }
            }
        }
    }
}