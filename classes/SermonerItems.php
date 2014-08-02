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
 * Class SermonerItems
 *
 * @copyright  Samuel Heer 2014
 * @author     Samuel Heer
 */
class SermonerItems extends \Frontend
{
	/**
	 * Return a form to choose an existing sermon and import it
	 * @return string
	 * @throws \Exception
	 */
	public function importSermon()
	{
		if (\Input::get('key') != 'import')
		{
			return '';
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Import Sermon
		if (\Input::post('FORM_SUBMIT') == 'tl_sermoner_items_import')
		{
			$uploadPath = \FilesModel::findByUuid(SermonArchiveModel::findByPk(\Input::get('id'))->directUploadDestination)->path;
			$arrUploaded = $objUploader->uploadTo($uploadPath);

			if (empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}
			foreach ($arrUploaded as $strAudioFile)
			{
				// Folders cannot be imported
				if (is_dir(TL_ROOT . '/' . $strAudioFile))
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($strAudioFile)));
					continue;
				}

				$objFile = \Dbafs::addResource($strAudioFile);

				// Check the file extension
				if ($objFile->extension != 'mp3')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				$this->import('getid3');
				$getID3 = new \getID3();

				$ThisFileInfo = $getID3->analyze(TL_ROOT.'/'.$objFile->path);
				$metadata = $ThisFileInfo['tags']['id3v2'];

				//prepare date out of comment field
				$comment = array_pop($metadata['comment']);
				preg_match("/[0-9]{2}\.{1}[0-9]{2}\.[0-9]{4}/", $comment, $date);
				$date = new \Date($date[0]);
				
				$objSermon = $this->Database->prepare("INSERT INTO tl_sermoner_items (pid, tstamp, title, speaker, date, audioSingleSRC) VALUES (?,?,?,?,?,?)")
											->execute(\Input::get('id'), time(), $metadata['title'][0],$metadata['artist'][0],$date->timestamp, $objFile->uuid);

				$insertId = $objSermon->insertId;

				if (!is_numeric($insertId) || $insertId < 0)
				{
					throw new \Exception('Invalid insert ID');
				}
			}

			// Redirect
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=import', '&act=edit&id='.$insertId, \Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="' .ampersand(str_replace('&key=import', '', \Environment::get('request'))). '" class="header_back" title="' .specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']). '" accesskey="b">' .$GLOBALS['TL_LANG']['MSC']['backBT']. '</a>
</div>

<h2 class="sub_headline">' .$GLOBALS['TL_LANG']['tl_sermoner_items']['import'][1]. '</h2>
' .\Message::generate(). '
<form action="' .ampersand(\Environment::get('request'), true). '" id="tl_sermoner_items_import" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_sermoner_items_import">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">
<input type="hidden" name="MAX_FILE_SIZE" value="'.$GLOBALS['TL_CONFIG']['maxFileSize'].'">

<div class="tl_tbox">
  <h3>'.$GLOBALS['TL_LANG']['tl_sermoner_items']['source'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['tl_sermoner_items']['source'][1]) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_sermoner_items']['source'][1].'</p>' : '').'
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="' .specialchars($GLOBALS['TL_LANG']['tl_sermoner_items']['import'][0]). '">
</div>

</div>
</form>';
	}
}