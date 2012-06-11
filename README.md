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

Roadmap
-------

* Abstract connection handling
* Integrate Net\_Gearman from PEAR
* Integrate Zend\Console for a BaseWorker
