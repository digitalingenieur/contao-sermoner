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

$GLOBALS['TL_DCA']['tl_content']['palettes']['chosenSermon'] = '{type_legend},type,headline;{sermon_legend},sermon;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['latestSermon'] = '{type_legend},type,headline;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['sermon'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['sermon'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_chosensermon', 'getSermons'),
	'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);


class tl_content_chosensermon extends Backend
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
	 * Get all modules and return them as array
	 * @return array
	 */
	public function getSermons()
	{
		if (!$this->User->isAdmin && !is_array($this->User->sermoner))
		{
			return array();
		}

		$arrSermoner = array();
		$objSermoner = $this->Database->execute("SELECT id, pid, title FROM tl_sermoner_items ORDER BY title");

		while ($objSermoner->next())
		{
			if ($this->User->isAdmin || $this->User->hasAccess($objSermoner->pid, 'sermoner'))
			{
				$objSermonArchive = $this->Database->prepare("SELECT title FROM tl_sermoner WHERE id=?")->execute($objSermoner->pid);
				$arrSermoner[$objSermonArchive->title][$objSermoner->id] = $objSermoner->title . ' (ID ' . $objSermoner->id . ')';
			}
		}

		return $arrSermoner;
	}
}


?>