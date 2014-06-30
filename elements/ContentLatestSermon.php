<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   liwi
 * @author    Samuel Heer
 * @license   GNU/LGPL
 * @copyright Samuel Heer
 */


/**
 * Namespace
 */
namespace sermoner;


/**
 * Class ContentChosenSermon
 *
 * @copyright  Samuel Heer
 * @author     Samuel Heer
 * @package    Devtools
 */
class ContentLatestSermon extends \ContentText
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_chosenSermon';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;
		$this->import('Sermoner');

		$objConfig = new \stdClass();
		$objConfig->template = 'sermon_startpage';

		$objSermon = \SermonerItemsModel::findLatestPublishedByPids();
		$this->Template->sermon = $this->Sermoner->parseSermon($objSermon, false, '', 0, $objConfig);
		$GLOBALS['TL_HEAD'][] = '<meta property="og:title" content="'.$objSermon->title.'"/>';
		$GLOBALS['TL_HEAD'][] = '<meta property="og:description" content="Eine Predigt der Kirche Lindenwiese mit '.$objSermon->speaker.'"/>';		
	}
}
