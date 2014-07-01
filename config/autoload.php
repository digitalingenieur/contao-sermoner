<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Sermoner
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'sermoner'
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'sermoner\Sermoner'            	=> 'system/modules/sermoner/classes/Sermoner.php',
	'sermoner\FeedPodcast'          => 'system/modules/sermoner/classes/FeedPodcast.php',

	// Elements
	'sermoner\ContentChosenSermon' 	=> 'system/modules/sermoner/elements/ContentChosenSermon.php',
	'sermoner\ContentLatestSermon' 	=> 'system/modules/sermoner/elements/ContentLatestSermon.php',

	// Library
	'getid3'                       	=> 'system/modules/sermoner/vendor/james-heinrich/getid3/getid3/getid3.php',

	// Models
	'sermoner\SermonerItemsModel'  	=> 'system/modules/sermoner/models/SermonerItemsModel.php',
	'sermoner\SermonArchiveModel'  	=> 'system/modules/sermoner/models/SermonArchiveModel.php',
	'sermoner\SermonFeedModel'  	=> 'system/modules/sermoner/models/SermonFeedModel.php',

	// Modules
	'sermoner\ModuleSermon'        => 'system/modules/sermoner/modules/ModuleSermon.php',
	'sermoner\ModuleSermonList'    => 'system/modules/sermoner/modules/ModuleSermonList.php',
	'sermoner\ModuleSermonReader'  => 'system/modules/sermoner/modules/ModuleSermonReader.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_chosenSermon'      => 'system/modules/sermoner/templates/elements',
	'mod_sermonlist'       => 'system/modules/sermoner/templates/modules',
	'mod_sermonlist_empty' => 'system/modules/sermoner/templates/modules',
	'mod_sermonlist_ajax' 	=> 'system/modules/sermoner/templates/modules',
	'mod_sermonreader'		=> 'system/modules/sermoner/templates/modules',
	'sermon_full'          => 'system/modules/sermoner/templates/sermoner',
	'sermon_startpage'     => 'system/modules/sermoner/templates/sermoner',
	
));
