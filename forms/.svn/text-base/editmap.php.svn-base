<?php

/*
 * Displays a powerful google maps map for selecting locations
 */

require_once("control.php");
require_once(comdir("mech/xml.php"));
require_once(comdir("mech/gmap.php"));

function makeEditMap($label, $id, $selects, $value = null, $desc = null) {
  global $kMessageAboveFormat;

  if (is_null($value)) {
    $value = array('name' => "",
		   'lat' => DEFAULT_LATITUDE,
		   'long' => DEFAULT_LONGITUDE,
		   'sel' => 0);
  }

  return makeControl('map', $label, $id, $value, $desc) +
    array('form' => 'formEditMap', 'test' => 'testEditMap',
	  'proc' => 'procEditMap', 'undb' => 'undbEditMap',
	  'view' => 'viewEditMap',
	  'selects' => $selects,
	  'format' => $kMessageAboveFormat);
}

function formEditMap($map, $form = array(), $message = "") {
  $value = formDefault($map, $form, $message);
  $myval = $value['form'];

  $id = $map['id'];
  $locations = options(array(0 => " -- New Location -- ") + $map['selects']);
  $form = table(tr(td("Type Name: " . itext("${id}[name]", $myval['name'], 51))) .
		tr(td("&nbsp;&nbsp;Navigate the Map <sc>or</sc> " .
		      isubmit("map$id", "Search") . " for an address <sc>or</sc> ")) .
		tr(td("&nbsp;&nbsp;Select: " .
		      iselect($locations, "${id}[sel]", $myval['sel'],
			      array('style' => 'width: 360px')))) .
		tr(td(gmap("${id}[map]", 415, 280,
			   $myval['lat'], $myval['long']))));
		
  return array('input' => $form,
	       'form' => $form . $message);
}

function testEditMap($map, &$form) {
  // Was this a search?
  if (isset($form['map' . $map['id']])) {
    $xml = curl_string_get("http://maps.google.com/maps/geo",
			   array('q' => $form[$map['id']]['name'],
				 'key' => GOOGLE_KEY,
				 'output' => 'xml'));

    if (!is_null($xml)) {
      $parsed = ParseFullXML($xml);
      if (!is_null($parsed)) {
	$namex = SearchXML(array("Response" => null, "Placemark" => null,
				"address" => null), $parsed);
	if (!is_null($namex)) {
	  $name = $namex[0];
	} else {
	  $name = $form[$map['id']]['name'];
	}

	$coordsx = SearchXML(array("Response" => null, "Placemark" => null,
				   "Point" => null,
				   "coordinates" => null), $parsed);

	if (!is_null($coordsx)) {
	  $firstcomma = strcspn($coordsx[0], ',');
	  $secondcomma = strcspn($coordsx[0], ',', $firstcomma + 1);
	  $longitude = substr($coordsx[0], 0, $firstcomma);
	  $latitude = substr($coordsx[0], $firstcomma + 1, $secondcomma);

	  $form[$map['id']] = array('name' => $name, 'xml' => $xml,
				    'lat' => $latitude,
				    'long' => $longitude,
				    'sel' => 0);
	}
      }
    }
  }

  return true;
}

function procEditMap(&$map, $form) {
  return array();
}

function undbEditMap(&$map, $fields, $relations, $data) {
  $longitude = sqlGetValue('longitude', $fields, $relations, $data);
  $latitude = sqlGetValue('latitude', $fields, $relations, $data);
  $id_location = sqlGetValue('id_location', $fields, $relations, $data);

  $map['value'] = array('name' => "",
			'lat' => $latitude, 'long' => $longitude,
			'sel' => $id_location);
}

function viewEditMap($map) {
  return gmap("${id}[map]", 300, 200);
}

?>
