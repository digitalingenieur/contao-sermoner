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
 * Class ModuleSermonlist
 *
 * @copyright  Samuel Heer 2014
 * @author     Samuel Heer
 */
class ModuleSermonList extends \ModuleSermon
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_sermonlist';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['sermonlist'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->serm_sermonarchive = $this->sortOutProtected(deserialize($this->serm_sermonarchive, true));

		// Return if there are no calendars
		if (!is_array($this->serm_sermonarchive) || empty($this->serm_sermonarchive))
		{
			return '';
		}

		return parent::generate();
	}


	public function generateAjax(){
		$this->strTemplate = $this->strTemplate.'_ajax';
		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->import('Sermoner');

		$offset = intval($this->skipFirst);
		$limit = null;
		$this->Template->sermons = array();

		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

		// Get the total number of items
		$intTotal = \SermonerItemsModel::countPublishedByPids($this->serm_sermonarchive);

		if ($intTotal < 1)
		{
			$this->Template = new \FrontendTemplate('mod_sermonlist_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
			return;
		}

		$total = $intTotal - $offset;

		// Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $this->id;
			$page = \Input::get($id) ?: 1;



			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return;
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;
			$skip = intval($this->skipFirst);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id);
			$this->Template->pagination = $objPagination->generate("\n  ");

		}

		//Configuration
		$objConfig = new \stdClass();
		$objConfig->feedHref = sprintf("%s/share/%s.xml",
						\Environment::get('path'),
						\SermonFeedModel::findByPk($this->linkedRssFeed)->alias
					);
		$objConfig->feedIcon = $this->iconSRC;
		$objConfig->template = 'sermon_full';


		// Get the items
		if (isset($limit))
		{
			$objSermons = \SermonerItemsModel::findPublishedByPids($this->serm_sermonarchive, $limit, $offset);
		}
		else
		{
			$objSermons = \SermonerItemsModel::findPublishedByPids($this->serm_sermonarchive, 0, $offset);
		}

		// No items found
		if ($objSermons === null)
		{
			$this->Template = new \FrontendTemplate('mod_sermonlist_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
		}
		else
		{	
			$this->Template->sermons = $this->Sermoner->parseSermons($objSermons, $objConfig, $this->serm_template);
		}
	}
}
