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

$GLOBALS['TL_DCA']['tl_news_feed']['palettes']['__selector__'][] = 'podcast';
$GLOBALS['TL_DCA']['tl_news_feed']['palettes']['default'] .= ';{podcast_legend},podcast';
$GLOBALS['TL_DCA']['tl_news_feed']['subpalettes']['podcast'] = 'itunescategory,itunessubcategory';

$GLOBALS['TL_DCA']['tl_news_feed']['fields']['podcast'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['podcast'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_news_feed']['fields']['itunescategory'] = array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_news_feed']['itunescategory'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_news_feed_sermoner', 'getCategoryOptions'),
			'eval'                    => array('tl_class'=>'w50', 'includeBlankOption'=>true, 'chosen'=>true, 'multiple'=>true),
			'sql'                     => "varchar(128) NOT NULL default ''"
);


class tl_news_feed_sermoner {

	
	function getCategoryOptions($strValue){
		
		$options = array();

		$categories = $this->getITunesCategories();

		foreach($categories as $category => $subcategories){
			foreach($subcategories as $subcategory){
				$options[$category][$category.'::'.$subcategory] = $subcategory;
			}
		}

		return $options;
	}

	/**
	 * Get an array with iTunes categories
	 * based on https://help.apple.com/itc/podcasts_connect/#/itc9267a2f12
	 * as of 2017-04-07
	 */
	function getITunesCategories(){
		return array(
			'Arts'=> array(
				'Design',
				'Fashion & Beauty',
				'Food',
				'Literature',
				'Performing Arts',
				'Visual Arts'
				),
			'Business' => array(
				'Business News',
				'Careers',
				'Investing',
				'Management & Marketing',
				'Shopping'
				),
			'Comedy' => array(
				'Comedy'
				),
			'Education' => array(
				'Educational Technology',
				'Higher Education',
				'K-12',
				'Language Courses',
				'Training'
				),
			'Games & Hobbies' => array(
				'Automotive',
				'Aviation',
				'Hobbies',
				'Other Games',
				'Video Games'
				),
			'Government & Organizations' => array(
				'Local',
				'National',
				'Non-Profit',
				'Regional'
				),
			'Health' => array(
				'Alternative Health',
				'Fitness & Nutrition',
				'Self-Help',
				'Sexuality'
				),
			'Kids & Family' => array(
				'Kids & Family'
				),
			'Music' => array(
				'Music'
				),
			'News & Politics' => array(
				'News & Politics'
				),
			'Religion & Spirituality' => array(
				'Buddhism',
				'Christianity',
				'Hinduism',
				'Islam',
				'Judaism',
				'Other',
				'Spirituality'
				),
			'Science & Medicine' => array(
				'Medicine',
				'Natural Sciences',
				'Social Sciences'
				),
			'Society & Culture' => array(
				'History',
				'Personal Journals',
				'Philosophy',
				'Places & Travel'
				),
			'Sports & Recreation' => array(
				'History',
				'Personal Journals',
				'Philosophy',
				'Places & Travel'
				),
			'Technology' => array(
				'Gadgets',
				'Tech News',
				'Podcasting',
				'Software How-To'
				),
			'TV & Film' => array(
				'TV & Film'
				)
		);
	}
}