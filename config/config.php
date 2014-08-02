<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   sermoner
 * @author    Samuel Heer
 * @license   GNU/LGPL
 * @copyright Samuel Heer 2014
 */


/**
 * BACK END MODULES
 **/

array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'sermoner' => array
	(
		'tables'      => array('tl_sermon_archive', 'tl_sermoner_items', 'tl_sermon_feed'),
		'icon'        => 'system/modules/sermoner/assets/icon.png',
		'table'       => array('TableWizard', 'importTable'),
		'list'        => array('ListWizard', 'importList'),
		'stylesheet'  => 'system/modules/sermoner/assets/style.css',
		'import'      => array('SermonerItems', 'importSermon')
	)
));


/**
 * FRONT END MODULES
**/
array_insert($GLOBALS['FE_MOD'], 1, array
(
	'sermoner' => array
	(
		'sermonlist'    => 'ModuleSermonList',
		'sermonreader'	=> 'ModuleSermonReader'
	)
));


/**
 * CONTENT ELEMENTS
**/
array_insert($GLOBALS['TL_CTE']['includes'], 1, array(
	'chosenSermon' => 'ContentChosenSermon',
	'latestSermon' => 'ContentLatestSermon',
	));


/**
 * Cron jobs
 */
$GLOBALS['TL_CRON']['daily'][] = array('Sermoner', 'generateFeeds');

/**
 * Register hook to add news items to the indexer
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('Sermoner', 'purgeOldFeeds');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Sermoner', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['generateXmlFiles'][] = array('Sermoner', 'generateFeeds');


/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'sermoner';
$GLOBALS['TL_PERMISSIONS'][] = 'sermonerp';
$GLOBALS['TL_PERMISSIONS'][] = 'sermonfeeds';
$GLOBALS['TL_PERMISSIONS'][] = 'sermonfeedp';