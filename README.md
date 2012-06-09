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

Roadmap
-------

* Abstract connection handling
* Integrate Net\_Gearman from PEAR
* Integrate Zend\Console for a BaseWorker
