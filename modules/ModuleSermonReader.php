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
 * Namespace
 */
namespace sermoner;


/**
 * Class ModuleSermonReader
 *
 * @copyright  Samuel Heer 2014
 * @author     Samuel Heer
 */
class ModuleSermonReader extends \ModuleSermon
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_sermonreader';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['sermonreader'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		// Do not index or cache the page if no news item has been specified
		if (!\Input::get('items'))
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}

		$this->serm_sermonarchive = $this->sortOutProtected(deserialize($this->serm_sermonarchive));

		// Do not index or cache the page if there are no archives
		if (!is_array($this->serm_sermonarchive) || empty($this->serm_sermonarchive))
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}

		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;
		$this->import('Sermoner');

		$this->Template->sermons = '';

		// Get the sermon item
		$objSermon = \SermonerItemsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->serm_sermonarchive);

		if ($objSermon === null)
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			// Send a 404 header
			header('HTTP/1.1 404 Not Found');
			$this->Template->sermons = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
			return;
		}

		//Add OG-Tags for Facebook to Head
		$GLOBALS['TL_HEAD'][] = '<meta property="og:title" content="'.$objSermon->title.'"/>';
		$GLOBALS['TL_HEAD'][] = '<meta property="og:description" content="Eine Predigt der Kirche Lindenwiese mit '.$objSermon->speaker.'"/>';
		// Add an image
		if ($objSermon->addImage && $objSermon->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objSermon->singleSRC);
			if (is_file(TL_ROOT . '/' . $objModel->path))
			{
				$GLOBALS['TL_HEAD'][] = '<meta property="og:image" content="'.\Environment::get('base').$objModel->path.'"/>';	
			}
		}

		$strSermon = $this->Sermoner->parseSermon($objSermon);
		$this->Template->sermons = $strSermon;

		// Overwrite the page title (see #2853 and #4955)
		if ($objSermon->title != '')
		{
			$objPage->pageTitle = strip_tags(strip_insert_tags($objSermon->title));
		}

		//Weiterleitung auf Predigtarchiv, wenn in Moduleeinstellungen gesetzt (Ausnahme: Facebook-Crawler)
		if($this->skipReader){
			if(\Environment::get('httpUserAgent') != "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)"){
				if (($objNextPage = \PageModel::findPublishedById($this->jumpToList)) !== null)
				{
						$this->redirect($this->generateFrontendUrl($objNextPage->row())."#".standardize(\String::restoreBasicEntities($objSermon->title)));
				}
			}	
		}
		return;
	}
}
