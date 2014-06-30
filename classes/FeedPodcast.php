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

namespace sermoner;

/**
 * Creates Podcast feeds
 *
 * The class provides an interface to create Podcast feeds. You can add the
 * feed item objects and the class will generate the XML markup.
 *
 * Usage:
 *
 *     $feed = new Feed('news');
 *     $feed->title = 'Podcast feed';
 *
 *     $item = new FeedItem();
 *     $item->title = 'Latest sermon';
 *     $item->author = 'Samuel Heer';
 *
 *     $feed->addItem($item);
 *     echo $feed->generatePodcast();
 *
 * @package   sermoner
 * @author    Samuel Heer
 * @license   GNU/LGPL
 * @copyright Samuel Heer 2014
 */
class FeedPodcast extends \Feed
{
	/**
	 * Generate an iTunes Podcast feed and return it as XML string
	 *
	 * @return string The RSS feed markup
	 */
	public function generatePodcast()
	{
		
		$this->adjustPublicationDate();

		$xml  = '<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>';
		$xml .= '<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">';
		$xml .= '<channel>';
		$xml .= '<title>' . specialchars($this->title) . '</title>';
		$xml .= '<description>' . specialchars($this->description) . '</description>';
		$xml .= '<link>' . specialchars($this->link) . '</link>';
		$xml .= '<language>' . $this->language . '</language>';
		$xml .= '<pubDate>' . date('r', $this->published) . '</pubDate>';
		$xml .= '<generator>Contao Open Source CMS</generator>';
		
		$xml .= '<itunes:owner>';
		$xml .= '<itunes:name>'.$GLOBALS['TL_CONFIG']['websiteTitle'].'</itunes:name>';
		$xml .= '<itunes:email>'.$GLOBALS['TL_CONFIG']['adminEmail'].'</itunes:email>';
		$xml .= '</itunes:owner>';

		$xml .= '<itunes:image href="'. $this->imageUrl .'" />';
		
		$xml .= '<itunes:category text="Religion &amp; Spirituality">';
		$xml .= '<itunes:category text="Christianity" />';
		$xml .= '</itunes:category>';


		foreach ($this->arrItems as $objItem)
		{
			$xml .= '<item>';
			$xml .= '<title>' . specialchars(strip_tags($objItem->title)) . '</title>';
			$xml .= '<author>' . specialchars(strip_tags($objItem->author)) . '</author>';
			$xml .= '<description><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $objItem->description) . ']]></description>';
			$xml .= '<link>' . specialchars($objItem->link) . '</link>';
			$xml .= '<pubDate>' . date('r', $objItem->published) . '</pubDate>';
			$xml .= '<itunes:subtitle><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $objItem->description) . ']]></itunes:subtitle>';
			$xml .= '<itunes:duration>'.$objItem->duration.'</itunes:duration>';

			// Add the GUID
			if ($objItem->guid)
			{
				// Add the isPermaLink attribute if the guid is not a link (see #4930)
				if (strncmp($objItem->guid, 'http://', 7) !== 0 && strncmp($objItem->guid, 'https://', 8) !== 0)
				{
					$xml .= '<guid isPermaLink="false">' . $objItem->guid . '</guid>';
				}
				else
				{
					$xml .= '<guid>' . $objItem->guid . '</guid>';
				}
			}
			else
			{
				$xml .= '<guid>' . specialchars($objItem->link) . '</guid>';
			}

			// Enclosures
			if (is_array($objItem->enclosure))
			{
				foreach ($objItem->enclosure as $arrEnclosure)
				{
					$xml .= '<enclosure url="' . $arrEnclosure['url'] . '" length="' . $arrEnclosure['length'] . '" type="' . $arrEnclosure['type'] . '" />';
				}
			}

			$xml .= '</item>';
		}

		$xml .= '</channel>';
		$xml .= '</rss>';

		return $xml;
	}
}
