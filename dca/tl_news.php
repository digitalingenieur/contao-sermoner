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


$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = str_replace('teaser;', 'teaser;{sermon_legend},speaker,moderator;', $GLOBALS['TL_DCA']['tl_news']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_news']['fields']['speaker'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_news']['speaker'],
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		);

$GLOBALS['TL_DCA']['tl_news']['fields']['moderator'] = array
(
			'label'                   => &$GLOBALS['TL_LANG']['tl_news']['moderator'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
);