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
      if ($this->Database->tableExists('tl_sermoner')) 
      {
			  
        $archives = $this->Database->execute("SELECT * FROM `tl_sermoner`"); 
        foreach($archives as $archive){
          $this->Database->prepare("INSERT INTO `tl_news_archive` (`tstamp`,`title`,`jumpTo`) VALUES (?, ?, ?)")->execute($archive->tstamp,$archive->title, $archive->jumpTo);
        }

      }
		
   } // run
} // class
$objSermonerRunonceJob = new SermonerRunonceJob();
$objSermonerRunonceJob->run();