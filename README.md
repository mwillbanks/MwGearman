mwGearman
===================
Version 0.1.0 Created by Mike Willbanks

Introduction
------------

mwGearman is a module that handles interfacing with the Gearman extension.
This module presently can handle client and worker communication and abstracts
portions of task handling.  The overall goal is once Zend\Console has been
completed to integrate workers into the Console to make building out gearman
worker models far more simplistic.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (beta4+)
* [PECL Gearman](http://pecl.php.net/package/gearman)

Installation
------------

*Composer*
Your composer.json should include the following. 

	{
	"repositories": [
	        {
	            "type": "package",
	            "package": {
	                "version": "master",
	                "name": "mwGearman",
	                "source": {
	                    "type": "git",
	                    "url": "https://github.com/mwillbanks/mwGearman",
	                    "reference": "master"
	                } 
	            }

	        }
	    ],
		"require": {
		        "mwGearman": "master"
		    }
    }

*Git Submodule*

* git submodule add [repo-url] ./vendor/mwGearman
* add 'mwGearman' to your application.config.php file

Usage
-----

*DI Configuration for Connection Handling*
```php
<?php
return array(
    'di' => array(
        'instance' => array(
            'mwGearman\Client\Pecl' => array(
                'parameters' => array(
                    'servers' => array(
                        array('localhost'),
                    ),
                ),
            ),
            'mwGearman\Worker\Pecl' => array(
                'parameters' => array(
                    'servers' => array(
                        array('localhost'),
                    ),
                ),
            ),
        ),
    ),
);
```

*Submitting a Job to Gearman*
```php
<?php
$gearman = $serviceMananger->get('mwGearman\Client\Pecl');
$gearman->connect();

$workload = 'some-string';

$task = new mwGearman\Task\Task();
$task->setBackground(true)
     ->setFunction('myJob')
     ->setWorkload($workload)
     ->setUnique(crc32($workload));

$handle = $gearman->doTask($task);
```

*Retrieving a Job from Gearman*
```php
<?php
$gearman = $serviceMananger->get('mwGearman\Worker\Pecl');
$gearman->register('myJob', 'handleJob');
$gearman->connect();
while($gearman->work());

function handleJob($job) {
    $workload = $job->workload();
    echo $workload;
}

```
Roadmap
-------
* Integrate Net\_Gearman from PEAR
* Integrate Zend\Console for a BaseWorker
