<?php

$GLOBALS['TL_LANG']['XPL']['podcastSubtitle'] = array 
( 
    array(
    	'colspan',
    	'Folgende Platzhalter sind in diesem Feld möglich.'
    ),
    array(
    	'{{title}}',
    	'Thema'
    ),
    array(
    	'{{speaker}}',
    	'Prediger'
    ),
    array(
    	'{{date}}',
    	'Datum'
    )
);


/*
foreach($GLOBALS['TL_DCA']['tl_sermoner_items']['fields'] as $key => $field){
	$arrBuffer = array('{{'.$key.'}}',$field['label'][0]);
	array_push($GLOBALS['TL_LANG']['XPL']['podcastSubtitle'],$arrBuffer);
}*/

?>