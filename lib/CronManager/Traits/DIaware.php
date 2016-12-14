<?php
/**
 * @namespace
 */
namespace CronManager\Traits;

/**
 * Class DIaware
 * @package CronManager\Traits
 */
trait DIaware
{
    /**
     * Dependency Injection
     * @var \Phalcon\DiInterface
     */
    protected $_di;

    /**
     * Sets the dependency injector
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function setDi(\Phalcon\DiInterface $dependencyInjector = null)
    {
        $this->_di = $dependencyInjector;
    }

    /**
     * Returns the internal dependency injector
     *
     * @return \Phalcon\DiInterface
     */
    public function getDi()
    {
        return $this->_di;
    }
	
}