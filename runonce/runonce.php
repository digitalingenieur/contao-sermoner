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

class SermonerRunonceJob extends Controller
{
   public function __construct()
   {
       parent::__construct();
       $this->import('Database');
   }
   public function run()
   {
   	//TODO: Implement
   	echo 'IMPLEMENT';
      	/*if ($this->Database->tableExists('tl_news')) 
		{
			if ($this->Database->fieldExists('banner_template', 'tl_banner_category') 
               && !$this->Database->fieldExists('banner_template', 'tl_module'))
			{
                   //Feld anlegen
                   $this->Database->execute("ALTER TABLE `tl_module` ADD `banner_template` varchar(32) NOT NULL default ''");
                   //nun sollte es angelegt sein
                   if ( $this->Database->fieldExists('banner_template', 'tl_banner_category') 
                     && $this->Database->fieldExists('banner_template', 'tl_module') )
                   {
                       //füllen
                       $this->Database->execute("UPDATE tl_module SET banner_template='mod_banner_list_all' WHERE type='banner' AND banner_template=''");
                   }
               }
		} // if tableExists */
   } // run
} // class
$objSermonerRunonceJob = new SermonerRunonceJob();
$objSermonerRunonceJob->run();