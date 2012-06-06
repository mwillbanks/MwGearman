<?php
/**
 * mwGearman
 *
 * @category   mwGearman
 * @package    Module
 */

namespace mwGearman;

/**
 * Module Setup
 * 
 * @category   mwGearman
 * @package    Module
 */
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
