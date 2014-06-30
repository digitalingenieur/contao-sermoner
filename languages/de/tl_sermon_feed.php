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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_sermon_feed']['title'] = array('Titel', 'Bitte geben Sie einen Feed-Titel ein.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['alias'] = array('Feed-Alias', 'Hier können Sie einen eindeutigen Dateinamen (ohne Endung) eingeben. Die XML-Datei wird automatisch im <em>share/</em>-Ordner Ihrer Contao-Installation erstellt, z.B. als <em>share/name.xml</em>.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['language'] = array('Feed-Sprache', 'Bitte geben Sie die Sprache der Seite gemäß des ISO-639 Standards ein (z.B. <em>de</em>, <em>de-ch</em>).');
$GLOBALS['TL_LANG']['tl_sermon_feed']['archives'] = array('Predigtarchive', 'Hier legen Sie fest, welche Predigtarchive in dem Feed enthalten sind.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['format'] = array('Feed-Format', 'Bitte wählen Sie ein Format.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['source'] = array('Export-Einstellungen', 'Hier können Sie festlegen, was exportiert werden soll.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['maxItems'] = array('Maximale Anzahl an Beiträgen', 'Hier können Sie die Anzahl der Beiträge limitieren. Geben Sie 0 ein, um alle zu exportieren.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['feedBase'] = array('Basis-URL', 'Bitte geben Sie die Basis-URL mit Protokoll (z.B. <em>http://</em>) ein.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['description'] = array('Feed-Beschreibung','Bitte geben Sie eine kurze Beschreibung des Predigt-Feeds ein.');

$GLOBALS['TL_LANG']['tl_sermon_feed']['podcastSingleSRC'] = array('Podcast Grafik','iTunes bevorzugt quadratische Bilder im JPG-Format mit einer Größe von mindestens 1400 x 1400 Pixeln und weicht damit von den Angaben für das standardmäßige RSS-„image“-Tag ab. Damit ein Podcast für eine Vorstellung im iTunes Store infrage kommt, muss das zugehörige Bild mindestens 1400 x 1400 Pixel groß sein. Es lohnt sich, etwas Zeit in die Erstellung eines ansprechenden, originellen Bildes zu investieren, das den Podcast gut darstellt. Potenzielle Abonnenten werden dieses Bild auf der Seite des Podcasts sehen. Eine kleinere Version des Bildes (50x50px) wird in den Suchergebnissen und bei einer Vorstellung des Podcasts angezeigt. Das Design sollte in beiden Größen effektiv sein.');
$GLOBALS['TL_LANG']['tl_sermon_feed']['podcastSubtitle'] = array('Element Untertitel','Der Inhalt dieses Tags wird in iTunes in der Spalte „Beschreibung“ angezeigt. Die Anzeige des Untertitels ist am besten möglich, wenn er nur wenige Wörter umfasst. Platzhalter sind im Format {{...}} möglich.');

$GLOBALS['TL_LANG']['tl_sermon_feed']['title_legend'] = 'Titel und Sprache';
$GLOBALS['TL_LANG']['tl_sermon_feed']['archives_legend'] = 'Predigtarchive';
$GLOBALS['TL_LANG']['tl_sermon_feed']['config_legend'] = 'Feed-Einstellungen';
$GLOBALS['TL_LANG']['tl_sermon_feed']['podcast_legend'] = 'Podcast-Einstellungen';

$GLOBALS['TL_LANG']['tl_sermon_feed']['source_teaser'] = 'Teasertexte';
$GLOBALS['TL_LANG']['tl_sermon_feed']['source_text'] = 'Komplette Beiträge';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_sermon_feed']['new'] 			= 	array('Neuer Feed', 'Einen neuen Feed erstellen');
$GLOBALS['TL_LANG']['tl_sermon_feed']['edit'] 			= 	array('Feed bearbeiten', 'Feed ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_sermon_feed']['copy'] 			= 	array('Feed duplizieren', 'Feed ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_sermon_feed']['delete'] 		= 	array('Feed löschen', 'Feed ID %s löschen');
$GLOBALS['TL_LANG']['tl_sermon_feed']['show'] 			= 	array('Feeddetails','Die Details des Feeds ID %s anzeigen');

