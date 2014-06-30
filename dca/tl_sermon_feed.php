<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package sermoner
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Table tl_sermon_feed
 */
$GLOBALS['TL_DCA']['tl_sermon_feed'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_sermon_feed', 'checkPermission'),
			array('tl_sermon_feed', 'generateFeed')
		),
		'onsubmit_callback' => array
		(
			array('tl_sermon_feed', 'scheduleUpdate')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'alias' => 'index'
			)
		),
		'backlink'                    => 'do=sermoner'
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_sermon_feed']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_sermon_feed']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_sermon_feed']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_sermon_feed']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'				=> array('format'),
		'default'                   => '{title_legend},title,alias,language;{archives_legend},archives;{config_legend},format,source,maxItems,feedBase,description',
		'podcast'			  		=> '{title_legend},title,alias,language;{archives_legend},archives;{config_legend},format,source,maxItems,feedBase,description;{podcast_legend},podcastSingleSRC,podcastSubtitle'
	),

/*
	'subpalettes' => array(
		'format_podcast' =>
	),*/

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['alias'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('tl_sermon_feed', 'checkFeedAlias')
			),
			'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
		),
		'language' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['language'],
			'exclude'                 => true,
			'search'                  => true,
			'filter'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>32, 'tl_class'=>'w50'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'archives' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['archives'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_sermon_feed', 'getAllowedArchives'),
			'eval'                    => array('multiple'=>true, 'mandatory'=>true),
			'sql'                     => "blob NULL"
		),
		'format' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['format'],
			'default'                 => 'rss',
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => array('rss'=>'RSS 2.0', 'atom'=>'Atom', 'podcast' => 'Podcast'),
			'eval'                    => array('tl_class'=>'w50', 'submitOnChange'=>true),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'source' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['source'],
			'default'                 => 'source_teaser',
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('source_teaser', 'source_text'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_sermon_feed'],
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'maxItems' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['maxItems'],
			'default'                 => 25,
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
		),
		'feedBase' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['feedBase'],
			'default'                 => Environment::get('base'),
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('trailingSlash'=>true, 'rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['description'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('style'=>'height:60px', 'tl_class'=>'clr'),
			'sql'                     => "text NULL"
		),
		'podcastSingleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['podcastSingleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes'], 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL"
		),
		'podcastSubtitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sermon_feed']['podcastSubtitle'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50', 'mandatory'=>true, 'helpwizard'=>true),
			'explanation'			  => 'podcastSubtitle',
			'sql'                     => "varchar(255) NOT NULL default ''"
		),

	)
);


/**
 * Class tl_sermon_feed
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package    sermoner
 */
class tl_sermon_feed extends Backend
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
	 * Check permissions to edit table tl_sermon_feed
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Set the root IDs
		if (!is_array($this->User->sermonfeeds) || empty($this->User->sermonfeeds))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->sermonfeeds;
		}

		$GLOBALS['TL_DCA']['tl_sermon_feed']['list']['sorting']['root'] = $root;

		// Check permissions to add feeds
		if (!$this->User->hasAccess('create', 'sermonfeedp'))
		{
			$GLOBALS['TL_DCA']['tl_sermon_feed']['config']['closed'] = true;
		}

		// Check current action
		switch (Input::get('act'))
		{
			case 'create':
			case 'select':
				// Allow
				break;

			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array(Input::get('id'), $root))
				{
					$arrNew = $this->Session->get('new_records');

					if (is_array($arrNew['tl_sermon_feed']) && in_array(Input::get('id'), $arrNew['tl_sermon_feed']))
					{
						// Add permissions on user level
						if ($this->User->inherit == 'custom' || !$this->User->groups[0])
						{
							$objUser = $this->Database->prepare("SELECT sermonfeeds, sermonfeedp FROM tl_user WHERE id=?")
													   ->limit(1)
													   ->execute($this->User->id);

							$arrSermonfeedp = deserialize($objUser->sermonfeedp);

							if (is_array($arrSermonfeedp) && in_array('create', $arrSermonfeedp))
							{
								$arrSermonfeeds = deserialize($objUser->sermonfeeds);
								$arrSermonfeeds[] = Input::get('id');

								$this->Database->prepare("UPDATE tl_user SET sermonfeeds=? WHERE id=?")
											   ->execute(serialize($arrSermonfeeds), $this->User->id);
							}
						}

						// Add permissions on group level
						elseif ($this->User->groups[0] > 0)
						{
							$objGroup = $this->Database->prepare("SELECT sermonfeeds, sermonfeedp FROM tl_user_group WHERE id=?")
													   ->limit(1)
													   ->execute($this->User->groups[0]);

							$arrSermonfeedp = deserialize($objGroup->sermonfeedp);

							if (is_array($arrSermonfeedp) && in_array('create', $arrSermonfeedp))
							{
								$arrSermonfeeds = deserialize($objGroup->sermonfeeds);
								$arrSermonfeeds[] = Input::get('id');

								$this->Database->prepare("UPDATE tl_user_group SET sermonfeeds=? WHERE id=?")
											   ->execute(serialize($arrSermonfeeds), $this->User->groups[0]);
							}
						}

						// Add new element to the user object
						$root[] = Input::get('id');
						$this->User->sermonfeeds = $root;
					}
				}
				// No break;

			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array(Input::get('id'), $root) || (Input::get('act') == 'delete' && !$this->User->hasAccess('delete', 'sermonfeedp')))
				{
					$this->log('Not enough permissions to '.Input::get('act').' sermon feed ID "'.Input::get('id').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $this->Session->getData();
				if (Input::get('act') == 'deleteAll' && !$this->User->hasAccess('delete', 'sermonfeedp'))
				{
					$session['CURRENT']['IDS'] = array();
				}
				else
				{
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$this->Session->setData($session);
				break;

			default:
				if (strlen(Input::get('act')))
				{
					$this->log('Not enough permissions to '.Input::get('act').' sermon feeds', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}


	/**
	 * Check for modified sermon feeds and update the XML files if necessary
	 */
	public function generateFeed()
	{
		$session = $this->Session->get('sermon_feed_updater');

		if (!is_array($session) || empty($session))
		{
			return;
		}

		$this->import('Sermoner');

		foreach ($session as $id)
		{
			$this->Sermoner->generateFeed($id, true);
		}

		$this->import('Automator');
		$this->Automator->generateSitemap();

		$this->Session->set('sermon_feed_updater', null);
	}


	/**
	 * Schedule a sermon feed update
	 *
	 * This method is triggered when a single sermon archive or multiple sermon
	 * archives are modified (edit/editAll).
	 * @param \DataContainer
	 */
	public function scheduleUpdate(DataContainer $dc)
	{
		// Return if there is no ID
		if (!$dc->id)
		{
			return;
		}

		// Store the ID in the session
		$session = $this->Session->get('sermon_feed_updater');
		$session[] = $dc->id;
		$this->Session->set('sermon_feed_updater', array_unique($session));
	}


	/**
	 * Return the IDs of the allowed sermon archives as array
	 * @return array
	 */
	public function getAllowedArchives()
	{
		if ($this->User->isAdmin)
		{
			$objArchive = SermonArchiveModel::findAll();
		}
		else
		{
			$objArchive = SermonArchiveModel::findMultipleByIds($this->User->sermoner);
		}

		$return = array();

		if ($objArchive !== null)
		{
			while ($objArchive->next())
			{
				$return[$objArchive->id] = $objArchive->title;
			}
		}

		return $return;
	}


	/**
	 * Check the RSS-feed alias
	 * @param mixed
	 * @param \DataContainer
	 * @return mixed
	 * @throws \Exception
	 */
	public function checkFeedAlias($varValue, DataContainer $dc)
	{
		// No change or empty value
		if ($varValue == $dc->value || $varValue == '')
		{
			return $varValue;
		}

		$varValue = standardize($varValue); // see #5096

		$this->import('Automator');
		$arrFeeds = $this->Automator->purgeXmlFiles(true);

		// Alias exists
		if (array_search($varValue, $arrFeeds) !== false)
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
		}

		return $varValue;
	}
}
