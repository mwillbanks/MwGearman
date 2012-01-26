<?php
/**
 * mwGearman
 *
 * @category   mwGearman
 * @package    Module
 */

namespace mwGearman;

use Zend\Module\Manager,
    Zend\Module\Consumer\AutoloaderProvider;

/**
 * Module Setup
 * 
 * @category   mwGearman
 * @package    Module
 */
class Module implements AutoloaderProvider
{
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
