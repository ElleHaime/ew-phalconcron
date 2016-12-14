<?php
/**
 * @namespace
 */
namespace CronManager\Tools\Debug;

/**
 * Interface DeInterface
 *
 * @package Tools
 * @subpackage Debug
 */
interface DeInterface
{
    CONST MODE_HYBRID = 2;
    CONST MODE_DEBUG = 1;
    CONST MODE_STANDART = 0;

    CONST LOG_RESULT_ECHO   = 'echo';
    CONST LOG_RESULT_RETURN = 'return';

    /**
     * Set work mode
     *
     * @param string $mode
     */
    public function setDebugMode($mode);

    /**
     * Return current work mode
     *
     * @return string
     */
    public function getDebugMode();
}