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
   	//echo 'IMPLEMENT';
     
    //Das hier funktioniert irgendwie nicht, die Daten werden nicht in die Tabelle geschrieben? Liegt es am Zeitpunkt des Aufrufs? 
    //AUch nach Googeln nichts wirkliches gefunden.
    //Vor neuem Versionsprung, wichtig anzupassen!

      if ($this->Database->tableExists('tl_sermoner')) {
		
        $objArchives =  \Database::getInstance()->execute("SELECT * FROM tl_sermoner"); 

        while($objArchives->next()){
          
          /*$archive = new NewsArchiveModel();
          $archive->tstamp = $objArchives->tstamp;
          $archive->title = $objArchives->title;
          $archive->jumpTo = $objArchives->jumpTo;
          var_dump($archive);
          $archive->save();*/
          $arrInsert = array(
            'title' => 'Test',
            'jumpTo' => 14
            );
           \Database::getInstance()->prepare("INSERT INTO tl_news_archive %s")->set($arrInsert)->execute();
        }

        //DROP TABLE TL_SERMONER
      }

      $feed = new NewsFeedModel();
      $feed->title = 'testbnllbla';
      var_dump($feed->save());
   } // run
} // class
$objSermonerRunonceJob = new SermonerRunonceJob();
$objSermonerRunonceJob->run();