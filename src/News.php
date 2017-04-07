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


namespace Diging;

class News extends \Contao\News{
	
	function generateFiles($arrFeed){
		
		
		parent::generateFiles($arrFeed);
		
		if($arrFeed['podcast'] == 1){
			$strFile = $arrFeed['feedName'];

			$file = new \File('web/share/'.$strFile.'.xml');
			$strXml = $file->getContent();

			$xml = new \SimpleXMLElement($strXml);
			//Add Itunes Namespace
			$xml->addAttribute('itunes:somename','somevalue','http://www.itunes.com/dtds/podcast-1.0.dtd');
			unset($xml->attributes('itunes', TRUE)['somename']);
		
			//Handle Enclosures
			foreach($xml->channel[0]->item as $item){
				$audioEnclosure = array();
				$imageEnclosure = array();

				foreach($item->enclosure as $enclosure){
					if(strpos($enclosure['type'], 'audio') !== false){
						$audioEnclosure = (array)$enclosure;
					}
					if(strpos($enclosure['type'], 'image') !== false){
						$imageEnclosure = (array)$enclosure;
					}
				}

				//Delete all Enclosures
				unset($item->enclosure);

				//Add AudioEnclosure to Item
				$newEnclosure = $item->addChild('enclosure');
				foreach($audioEnclosure['@attributes'] as $k => $v){
					$newEnclosure->addAttribute($k,$v);
				}

				//Add Image to Item
				$itunesImage = $item->addChild('itunes:itunes:image');
				$itunesImage->addAttribute('href',$imageEnclosure['@attributes']['url']);
			}

			//Handle Categories
			$categories = deserialize($arrFeed['itunescategory']);
			$addedCategories = array();
			if(empty($categories) != true){
				foreach($categories as $categoryValue){
					$category = explode('::',$categoryValue);
					if(array_search($category[0],$addedCategories) === false){
						$itunesCategory = $xml->channel[0]->addChild('itunes:itunes:category');
						$itunesCategory->addAttribute('text',htmlentities($category[0]));
						$addedCategories[] = $category[0];	
					}
					
					if($category[0] != $category[1]){
						$itunesSubCategory = $itunesCategory->addChild('itunes:itunes:category');
						$itunesSubCategory->addAttribute('text',htmlentities($category[1]));
					}
				}
			}
			
			$file->truncate();
			$file->write($xml->asXML());
			$file->close();
		}
	}
}