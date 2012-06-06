<?php
/**
 * mwGearman
 *
 * @category   mwGearman
 * @package    Module
 */

namespace mwGearman;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

/**
 * Module Setup
 * 
 * @category   mwGearman
 * @package    Module
 */
class Module implements AutoloaderProviderInterface
{
    /**
     * Get Config
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Set Autoloader Configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
