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
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['sermonlist']    = '{title_legend},name,headline,type;{config_legend},serm_sermonarchive,numberOfItems,perPage,skipFirst,showRssFeed;{template_legend:hide},serm_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['sermonreader']  = '{title_legend},name,headline,type;{config_legend},serm_sermonarchive;{syndicationSettings_legend},skipReader;{template_legend:hide},serm_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'skipReader';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'showRssFeed';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['skipReader'] = 'jumpToList';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['showRssFeed'] = 'linkedRssFeed,iconSRC';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['serm_sermonarchive'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['serm_sermonarchive'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_sermoner', 'getArchives'),
	'eval'                    => array('mandatory'=>true, 'multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['showRssFeed'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['showRssFeed'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class' => 'clr m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['linkedRssFeed'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['linkedRssFeed'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_sermon_feed.title',
	'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio', 'includeBlankOption' => true, 'tl_class' => 'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
	'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iconSRC'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['iconSRC'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('filesOnly'=>true, 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes'], 'fieldType'=>'radio', 'mandatory'=>false, 'tl_class'=>'w50'),
	'sql'                     => "binary(16) NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['serm_template'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['serm_template'],
	'default'                 => 'sermon_full',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_sermoner', 'getSermonerTemplates'),
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['skipReader'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['skipReader'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpToList'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['jumpToList'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'foreignKey'              => 'tl_page.title',
	'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
	'relation'                => array('type'=>'hasOne', 'load'=>'eager')
);


class tl_module_sermoner extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Get all calendars and return them as array
	 * @return array
	 */
	public function getArchives()
	{
		if (!$this->User->isAdmin && !is_array($this->User->sermoner))
		{
			return array();
		}

		$arrSermons = array();
		$objSermons = $this->Database->execute("SELECT id, title FROM tl_sermon_archive ORDER BY title");

		while ($objSermons->next())
		{
			if ($this->User->isAdmin || $this->User->hasAccess($objSermons->id, 'sermoner'))
			{
				$arrSermons[$objSermons->id] = $objSermons->title;
			}
		}
		return $arrSermons;
	}


		/**
	 * Return all event templates as array
	 * @return array
	 */
	public function getSermonerTemplates()
	{
		return $this->getTemplateGroup('sermon_');
	}
}
