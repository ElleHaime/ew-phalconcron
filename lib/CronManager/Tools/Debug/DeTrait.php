<?php
/**
 * @namespace
 */
namespace CronManager\Tools\Debug;

/**
 * Class DeTrait
 *
 * @package Tools
 * @subpackage Debug
 */
trait DeTrait
{
    /**
     * Work mode
     * @var string
     */
    protected $_debugMode = DeInterface::MODE_STANDART;

    /**
     * Set work mode
     *
     * @param string $mode
     */
    public function setDebugMode($mode)
    {
        $this->_debugMode = $mode;
    }

    /**
     * Return current work mode
     *
     * @return string
     */
    public function getDebugMode()
    {
        return $this->_debugMode;
    }

    /**
     * Is mode set as debug
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return ($this->_debugMode == DeInterface::MODE_DEBUG) ? true : false;
    }

    /**
     * Is mode set as hybrid
     *
     * @return bool
     */
    public function isHybridMode()
    {
        return ($this->_debugMode == DeInterface::MODE_HYBRID) ? true : false;
    }

    /**
     * Check using RAM
     *
     * @param integer $memoryLimit
     * @return bool
     */
    public function checkMemory($memoryLimit = 128)
    {
        $usedMemory = $this->getUsedMemory();
        if ($usedMemory > $memoryLimit) {
            return false;
        }

        return true;
    }

    /**
     * Return used RAM
     *
     * @return float
     */
    public function getUsedMemory()
    {
        $mem_usage = memory_get_usage();
        $usedMemory = round($mem_usage/1048576,2);

        return $usedMemory;
    }

    /**
     * Loging(print) message with some value/param
     *
     * @param string $message
     * @param mixed $arg
     * @return mixed
     */
    public function log($message, $arg, $actionResult = DeInterface::LOG_RESULT_ECHO)
    {
        $str = "------------------------------------".PHP_EOL;
        $str .= "Notify date: ".date("Y-m-d H:i:s").PHP_EOL;
        $str .= "".PHP_EOL;
        $str .= "Debug message: ".$message.PHP_EOL;
        $str .= "------------------------------------".PHP_EOL;

        $str .= $this->dump($arg).PHP_EOL;

        switch ($actionResult) {
            case DeInterface::LOG_RESULT_ECHO:
                echo $str;
                break;
            case DeInterface::LOG_RESULT_RETURN:
                return $str;
                break;
        }
    }

    /**
     * Returns the string representation of an variable.
     *
     * Returns the dump of the given variable, respecting the $maxData and
     * $maxChildren paramaters when arrays or objects are dumped. $maxDepth is
     * the maximum recursion depth when dumping arrays and objects.
     *
     * @param mixed $arg
     * @param int $maxData
     * @param int $maxChildren
     * @param int $maxDepth
     * @return string
     */
    public function dump($arg, $maxData = 1000, $maxChildren = 100, $maxDepth = 3)
    {
        switch (gettype($arg)) {
            case 'boolean':
                return "".$this->_cutString(($arg ? 'TRUE' : 'FALSE'), $maxData)."";
            case 'integer':
            case 'double':
                return "".$this->_cutString((string) $arg, $maxData)."";
            case 'string':
                return "".sprintf(
                    "'%s'",
                    $this->_cutString((string) $arg, $maxData)
               );
            case 'array':
                return "".$this->_dumpArray($arg, $maxData, $maxChildren, $maxDepth);
            case 'object':
                return "".$this->_dumpObject($arg, $maxData, $maxChildren, $maxDepth);
            case 'resource':
                return "".$this->_dumpResource($arg, $maxData);
            case 'NULL':
                return 'NULL';
            default:
                return 'unknown type';
        }
    }
    /**
     * Returns the string representation of an array.
     *
     * Returns the dump of the given array, respecting the $maxData and
     * $maxChildren paramaters.
     *
     * @param array $arg
     * @param int $maxData
     * @param int $maxChildren
     * @param int $maxDepth
     * @return string
     */
    private function _dumpArray(array $arg, $maxData, $maxChildren, $maxDepth)
    {
        $arrayContent = '';
        if ($maxDepth !== 0) {
            $max = (
            $maxChildren === false
                ? count($arg)
                : min(count($arg), $maxChildren)
           );

            $results = array();
            reset($arg);
            for ($i = 0; $i < $max; ++$i) {
                $results[] =
                    $this->dump(
                        key($arg),
                        $maxData,
                        $maxChildren,
                        ($maxDepth === false ? $maxDepth : $maxDepth - 1)
                   )
                    . ' =: '
                    . $this->dump(
                        current($arg),
                        $maxData,
                        $maxChildren,
                        ($maxDepth === false ? $maxDepth : $maxDepth - 1)
                   );
                next($arg);
            }
            if ($max < count($arg)) {
                $results[] = '...';
            }
            $arrayContent = implode(','.PHP_EOL.'   ', $results);
        } else {
            $arrayContent = '...';
        }

        return sprintf(
            'array (
    %s
)', $arrayContent
       );
    }
    /**
     * Returns the string representation of an object.
     *
     * Returns the dump of the given object, respecting the $maxData,
     * $maxChildren and $maxDepth paramaters.
     *
     * @param object $arg
     * @param int $maxData
     * @param int $maxChildren
     * @param int $maxDepth
     * @return string
     */
    private function _dumpObject($arg, $maxData, $maxChildren, $maxDepth)
    {
        $refObj   = new \ReflectionObject($arg);

        $objectContent = '';
        if ($maxDepth !== 0) {
            $refProps = $refObj->getProperties();
            $max = (
            $maxChildren === false
                ? min(count($refProps), $maxChildren)
                : count($refProps)
           );
            $results = array();
            reset($refProps);
            for($i = 0; $i < $max; $i++) {
                $refProp = current($refProps);
                $results[] = sprintf(
                    '%s $%s = %s',
                    $this->_getPropertyVisibility($refProp),
                    $refProp->getName(),
                    $this->_getPropertyValue(
                        $refProp,
                        $arg,
                        $maxData,
                        $maxChildren,
                        ($maxDepth === false ? $maxDepth : $maxDepth - 1)
                   )
               );
                next($refProps);
            }
            $objectContent = implode(';'.PHP_EOL.'  ', $results);
        } else {
            $objectContent = '...';
        }

        return sprintf(
            'class %s {
    %s
}',
            $refObj->getName(),
            $objectContent
       );
    }
    /**
     * Returns the string representation of a resource.
     *
     * Returns the dump of the given resource, respecting the $maxData
     * paramater.
     *
     * @param resource $res
     * @param int $maxData
     * @return string
     */
    private function _dumpResource($res, $maxData)
    {
        // @TODO: Ugly, but necessary.
        // 'resource(5) of type (stream)'
        preg_match('(^Resource id #(?P<id>\d+)$)', (string) $res, $matches);
        return sprintf(
            'resource(%s) of type (%s)',
            $matches['id'],
            get_resource_type($res)
       );
    }
    /**
     * Returns the $value cut to $length and padded with '...'.
     *
     * @param string $value
     * @param int $length
     * @return string
     */
    private function _cutString($value, $length)
    {
        if ($length !== false && (strlen($value) > ($length - 3))) {
            return substr($value, 0, ($length - 3)) . '...';
        }

        return $value;
    }
    /**
     * Returns private, protected or public.
     *
     * Returns the visibility of the given relfected property $refProp as a
     * readable string.
     *
     * @param ReflectionProperty $refProp
     * @return string
     */
    private function _getPropertyVisibility(\ReflectionProperty $refProp)
    {
        $info = '%s %s = %s';
        if ($refProp->isPrivate()) {
            return 'private';
        }
        if ($refProp->isProtected()) {
            return 'protected';
        }

        return 'public';
    }
    /**
     * Returns the dumped property value.
     *
     * Returns the dumped value for the given reflected property ($refProp) on
     * the given $obj. Makes use of the ugly array casting hack to determine
     * values of private and protected properties.
     *
     * @param ReflectionProperty $refProp
     * @param object $obj
     * @param int $maxData
     * @param int $maxChildren
     * @param int $maxDepth
     * @return string
     */
    private function _getPropertyValue(\ReflectionProperty $refProp, $obj, $maxData, $maxChildren, $maxDepth)
    {
        $value = null;
        // @TODO: If we switch to PHP version 5.3, where Reflection can access
        // protected/private property values, we should change this to the
        // correct way.
        if (!$refProp->isPublic()) {
            $objArr    = (array) $obj;
            $className = ($refProp->isProtected() ? '*' : $refProp->getDeclaringClass()->getName());
            $propName  = $refProp->getName();
            $value     = $objArr["\0{$className}\0{$propName}"];
        } else {
            $value = $refProp->getValue($obj);
        }

        return $this->dump($value, $maxData, $maxChildren, $maxDepth);
    }
}