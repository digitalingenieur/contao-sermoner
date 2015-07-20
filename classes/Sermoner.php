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
 * Class Sermoner
 *
 * @copyright  Samuel Heer 2014
 * @author     Samuel Heer
 */
class Sermoner extends \Frontend
{

	/**
	 * Update a particular RSS feed
	 * @param integer
	 * @param boolean
	 */
	public function generateFeed($intId, $blnIsFeedId=false)
	{
		$objFeed = $blnIsFeedId ? \SermonFeedModel::findByPk($intId) : \SermonFeedModel::findByArchive($intId);

		if ($objFeed === null)
		{
			return;
		}

		$objFeed->feedName = $objFeed->alias ?: 'sermon' . $objFeed->id;

		// Delete XML file
		if (\Input::get('act') == 'delete')
		{
			$this->import('Files');
			$this->Files->delete($objFeed->feedName . '.xml');
		}

		// Update XML file
		else
		{
			$this->generateFiles($objFeed->row());
			$this->log('Generated sermon feed "' . $objFeed->feedName . '.xml"', __METHOD__, TL_CRON);
		}
	}


	/**
	 * Delete old files and generate all feeds
	 */
	public function generateFeeds()
	{
		$this->import('Automator');
		$this->Automator->purgeXmlFiles();

		$objFeed = \SermonFeedModel::findAll();

		if ($objFeed !== null)
		{
			while ($objFeed->next())
			{
				$objFeed->feedName = $objFeed->alias ?: 'sermon' . $objFeed->id;
				$this->generateFiles($objFeed->row());
				$this->log('Generated sermon feed "' . $objFeed->feedName . '.xml"', __METHOD__, TL_CRON);
			}
		}
	}

	/**
	 * Generate an XML files and save them to the root directory
	 * @param array
	 */
	protected function generateFiles($arrFeed)
	{
		$arrArchives = deserialize($arrFeed['archives']);

		if (!is_array($arrArchives) || empty($arrArchives))
		{
			return;
		}

		switch($arrFeed['format']){
			case 'rss':
				$strType = 'generateRss';
			break;

			case 'atom':
				$strType = 'generateAtom';
			break;

			case 'podcast':
				$strType = 'generatePodcast';
			break;
		}
		$strLink = $arrFeed['feedBase'] ?: \Environment::get('base');
		$strFile = $arrFeed['feedName'];

		$objFeed = new FeedPodcast($strFile);
		$objFeed->link = $strLink;
		$objFeed->title = $arrFeed['title'];
		$objFeed->description = $arrFeed['description'];
		$objFeed->language = $arrFeed['language'];
		$objFeed->published = $arrFeed['tstamp'];

		//Add Feed Image
		if($arrFeed['format'] == 'podcast'){
			$objFile = \FilesModel::findByUuid($arrFeed['podcastSingleSRC']);

			if ($objFile !== null)
			{
				$objFeed->imageUrl = \Environment::get('base').$objFile->path;
			}
			
		}

		// Get the items
		if ($arrFeed['maxItems'] > 0)
		{
			$objSermons = \SermonerItemsModel::findPublishedByPids($arrArchives, $arrFeed['maxItems']);
		}
		else
		{
			$objSermons = \SermonerItemsModel::findPublishedByPids($arrArchives);
		}

		// Parse the items
		if ($objSermons !== null)
		{
			$arrUrls = array();

			while ($objSermons->next())
			{
				$jumpTo = $objSermons->getRelated('pid')->jumpTo;

				// No jumpTo page set (see #4784)
				if (!$jumpTo)
				{
					continue;
				}

				// Get the jumpTo URL
				if (!isset($arrUrls[$jumpTo]))
				{
					$objParent = \PageModel::findWithDetails($jumpTo);

					// A jumpTo page is set but does no longer exist (see #5781)
					if ($objParent === null)
					{
						$arrUrls[$jumpTo] = false;
					}
					else
					{
						$arrUrls[$jumpTo] = $this->generateFrontendUrl($objParent->row(), (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias']) ?  '/%s' : '/items/%s'), $objParent->language);
					}
				}

				// Skip the event if it requires a jumpTo URL but there is none
				if ($arrUrls[$jumpTo] === false && $objSermons->source == 'default')
				{
					continue;
				}

				$strUrl = $arrUrls[$jumpTo];
				$objItem = new \FeedItem();
				
				$objItem->title = $objSermons->title;
				$objItem->link = $strLink . sprintf($strUrl, (($objSermons->alias != '' && !$GLOBALS['TL_CONFIG']['disableAlias']) ? $objSermons->alias : $objSermons->id));
				
				$objItem->published = $objSermons->date;
				$objItem->author = $objSermons->speaker;

				// Prepare the description
				if ($arrFeed['format'] == 'podcast')
				{
					$objItem->description = $this->replaceSermonInsertTags($arrFeed['podcastSubtitle'],$objSermons);
				}				

				// Add the article image as enclosure
				if ($objSermons->addImage)
				{
					$objFile = \FilesModel::findByUuid($objSermons->singleSRC);

					if ($objFile !== null)
					{
						$objItem->addEnclosure($objFile->path);
					}
				}
			
				// Add the Sermon Audio File
				if ($objSermons->audioSingleSRC)
				{
					$objFile = \FilesModel::findByUuid($objSermons->audioSingleSRC);
					if ($objFile !== null)
					{
						$objItem->addEnclosure($objFile->path);

						//Prepare the duration if it's a podcast
						if($arrFeed['format'] == 'podcast'){
							$this->import('getid3');
							$getID3 = new \getID3();
							$mp3FileInfo = $getID3->analyze(TL_ROOT.'/'.$objFile->path);
							$objItem->duration = @$mp3FileInfo['playtime_string'];
						}
					}
				}
		
				$objFeed->addItem($objItem);
			}
		}
		// Create the file
		\File::putContent('share/' . $strFile . '.xml', $this->replaceInsertTags($objFeed->$strType()));
	}

	/**
	 * Add news items to the indexer
	 * @param array
	 * @param integer
	 * @param boolean
	 * @return array
	 */
	public function getSearchablePages($arrPages, $intRoot=0, $blnIsSitemap=false)
	{
		$arrRoot = array();

		if ($intRoot > 0)
		{
			$arrRoot = $this->Database->getChildRecords($intRoot, 'tl_page');
		}

		$time = time();
		$arrProcessed = array();

		// Get all news archives
		$objArchive = \SermonArchiveModel::findByProtected('');

		// Walk through each archive
		if ($objArchive !== null)
		{
			while ($objArchive->next())
			{
				// Skip news archives without target page
				if (!$objArchive->jumpTo)
				{
					continue;
				}

				// Skip news archives outside the root nodes
				if (!empty($arrRoot) && !in_array($objArchive->jumpTo, $arrRoot))
				{
					continue;
				}

				// Get the URL of the jumpTo page
				if (!isset($arrProcessed[$objArchive->jumpTo]))
				{
					$objParent = \PageModel::findWithDetails($objArchive->jumpTo);

					// The target page does not exist
					if ($objParent === null)
					{
						continue;
					}

					// The target page has not been published (see #5520)
					if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop < $time))
					{
						continue;
					}

					// The target page is exempt from the sitemap (see #6418)
					if ($blnIsSitemap && $objParent->sitemap == 'map_never')
					{
						continue;
					}

					// Set the domain (see #6421)
					$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

					// Generate the URL
					$arrProcessed[$objArchive->jumpTo] = $domain . $this->generateFrontendUrl($objParent->row(), (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias']) ?  '/%s' : '/items/%s'), $objParent->language);
				}

				$strUrl = $arrProcessed[$objArchive->jumpTo];

				// Get the items
				$objSermons = \SermonerItemsModel::findPublishedDefaultByPid($objArchive->id);

				if ($objSermons !== null)
				{
					while ($objSermons->next())
					{
						$arrPages[] = $this->getLink($objSermons, $strUrl);
					}
				}
			}
		}

		return $arrPages;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @param object
	 * @return string
	 */
	public function parseSermon($objSermon, $blnAddArchive=false, $strClass='', $intCount=0, $objConfig)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($objConfig->template);
		$objTemplate->setData($objSermon->row());
		$objTemplate->sermonId = standardize(\String::restoreBasicEntities($objSermon->title));

		$objTemplate->class = (($objSermon->cssClass != '') ? ' ' . $objSermon->cssClass : '') . $strClass;
		$objTemplate->count = $intCount; // see #5708

		$objTemplate->date = \Date::parse($objPage->dateFormat,$objSermon->date);
		$objTemplate->subject = $objSermon->title;

		$objTemplate->speakerLabel = $GLOBALS['TL_LANG']['MSC']['preacher'];
		$objTemplate->moderatorLabel = $GLOBALS['TL_LANG']['MSC']['moderator'];
		
		$objTemplate->addImage = false;

		// Add an image
		if ($objSermon->addImage && $objSermon->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objSermon->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objSermon->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrSermon = $objSermon->row();

				$arrSermon['singleSRC'] = $objModel->path;
				$this->addImageToTemplate($objTemplate, $arrSermon);
			}
		}

		// Add the audio file
		if ($objSermon->audioSingleSRC != '')
		{
			/*$this->import('getid3');
			$getID3 = new \getID3();

			$ThisFileInfo = $getID3->analyze($objAudioModel = \FilesModel::findByUuid($objSermon->audioSingleSRC)->path);
			print_r($ThisFileInfo['tags']['id3v2']);*/
			 
			$objAudio = $objSermon;
			$objAudio->playerSRC = serialize(array($objSermon->audioSingleSRC)); 
			$contentPlayer = new \ContentMedia($objAudio);
			$objTemplate->player = $contentPlayer->generate();
		}
		
		if($objSermon->getRelated('pid')->showRssFeed){
			$linkedRssFeed = \SermonFeedModel::findByPk($objSermon->getRelated('pid')->linkedRssFeed);
			if($linkedRssFeed)
			{
				$objTemplate->feedHref = sprintf("%s/share/%s.xml",
				\Environment::get('path'),
				$linkedRssFeed->alias
				);	
			}
		}
		
	
		$objTemplate->addReference = false;

		// Add an Reference
		switch ($objSermon->addReference){
			case 'none':
				$objTemplate->addReference = false;
			break;

			case 'file':
				$objTemplate->addReference = true;

				$objDownload = $objSermon;
				$objDownload->singleSRC = $objSermon->fileSingleSRC;
					
				$contentDownload = new \ContentDownload($objDownload);
				$objTemplate->reference = $contentDownload->generate();
			break;

			case 'link':
				$objTemplate->addReference = true;

				$contentHyperlink = new \ContentHyperlink($objSermon);
				$objTemplate->reference = $contentHyperlink->generate();

			break;
		}

		//Syndication Facebook
		$objRedirectPage = \PageModel::findByPk($objSermon->getRelated('pid')->jumpTo);

		//Download Predigt
		$objDownload = $objSermon;
		$objDownload->singleSRC = $objSermon->audioSingleSRC;
		$objDownload->linkTitle = '<img src="files/layout/icons/download.png" width="20" height="20" alt="">';
		$objDownload->titleText = $GLOBALS['TL_LANG']['sermoner']['downloadTitle'];
					
		$contentDownload = new \ContentDownload($objDownload);
		$objTemplate->sermonDownload = $contentDownload->generate();

		$objTemplate->encUrl = rawurlencode(\Environment::get('base') . ampersand($this->generateFrontendUrl($objRedirectPage->row(), (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias']) ?  '/' : '/items/') . ((!$GLOBALS['TL_CONFIG']['disableAlias'] && $objSermon->alias != '') ? $objSermon->alias : $objSermon->id))));
		$objTemplate->encTitle = rawurlencode($objSermon->title);

		return $objTemplate->parse();
	}

	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	public function parseSermons($objSermons, $objConfig)
	{
		$limit = $objSermons->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrSermons = array();

		while ($objSermons->next())
		{
			$arrSermons[] = \Sermoner::parseSermon($objSermons, $blnAddArchive, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count, $objConfig);
		}

		return $arrSermons;
	}

		/**
	 * Return the names of the existing feeds so they are not removed
	 * @return array
	 */
	public function purgeOldFeeds()
	{
		$arrFeeds = array();
		$objFeeds = \SermonFeedModel::findAll();

		if ($objFeeds !== null)
		{
			while ($objFeeds->next())
			{
				$arrFeeds[] = $objFeeds->alias ?: 'sermon' . $objFeeds->id;
			}
		}

		return $arrFeeds;
	}

	public function replaceSermonInsertTags($strBuffer,$objSermon){
		$tags = preg_split('/\{\{(([^\{\}]*|(?R))*)\}\}/', $strBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);
		$strBuffer = '';
		for ($_rit=0, $_cnt=count($tags); $_rit<$_cnt; $_rit+=3)
		{
			$strBuffer .= $tags[$_rit];
			$strTag = strtolower($tags[$_rit+1]);

			// Skip empty tags
			if ($strTag == '')
			{
				continue;
			}

			$strBuffer .= $objSermon->$strTag;
		}
		return $strBuffer;
	}
}


