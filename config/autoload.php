<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package   sermoner
 * @author    Samuel Heer
 * @license   GNU/LGPL
 * @copyright diging.de 2017
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Diging'
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'Diging\News' => 'system/modules/sermoner/src/News.php'
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'news_sermon' => 'system/modules/sermoner/templates'
));
