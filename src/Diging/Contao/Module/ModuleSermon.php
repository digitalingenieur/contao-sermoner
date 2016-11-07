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
 * Class ModuleSermon
 *
 * @copyright  Samuel Heer 2014
 * @author     Samuel Heer
 */
abstract class ModuleSermon extends \Module
{

	/**
	 * Sort out protected archives
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrArchives)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrArchives) || empty($arrArchives))
		{
			return $arrArchives;
		}

		$this->import('FrontendUser', 'User');
		$objArchives = \SermonArchiveModel::findMultipleByIds($arrArchives);
		$arrArchives = array();

		if ($objArchives !== null)
		{
			while ($objArchives->next())
			{
				if ($objArchives->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$groups = deserialize($objArchives->groups);

					if (!is_array($groups) || empty($groups) || count(array_intersect($groups, $this->User->groups)) < 1)
					{
						continue;
					}
				}

				$arrArchives[] = $objArchives->id;
			}
		}

		return $arrArchives;
	}
}
