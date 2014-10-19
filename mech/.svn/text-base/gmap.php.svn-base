<?php

define('DEFAULT_LONGITUDE', -71.0975);
define('DEFAULT_LATITUDE', 42.3654);
define('DEFAULT_ZOOM', 13);

if (!defined("GOOGLE_KEY")) {
  // existencia.org key
  define('GOOGLE_KEY', "ABQIAAAA0xtpFQtpepONKtub50dLhRThFHdD-uf8dHmVkgL5XwupbVYpkBSWXi4PWGPxiHoQo6WTKQf5b6QwJg");
}

// Displays map of a given location (long, lat, zoom)
function gmap($name, $width, $height, $clat = DEFAULT_LATITUDE,
	      $clong = DEFAULT_LONGITUDE, $czoom = DEFAULT_ZOOM,
	      $afterload = null) {
  $googlekey = GOOGLE_KEY;

  if (is_null($afterload)) {
    $aljs = "";
  } else {
    $aljs = "$afterload(map);";
  }

  $newhead = <<<EOT
    <style type="text/css">
    v\:* {
      behavior:url(#default#VML);
    }
    </style>

    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=$googlekey"
            type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("$name"));

	map.addControl(new GLargeMapControl());

	GEvent.addListener(map, "moveend", function() {
	  var center = map.getCenter();
	  document.getElementsByName("${name}[lat]")[0].value = center.lat();
	  document.getElementsByName("${name}[long]")[0].value = center.lng();
	});

        map.setCenter(new GLatLng($clat, $clong), $czoom);

	$aljs
      }
    }

    //]]>
    </script>
EOT;

  AddReplaces('head', $newhead);
  AddReplaces('bodytags', array('onload' => "load()",
				'onunload' => "GUnload()"));
  AddReplaces('headtags', array('xmlns:v' => "urn:schemas-microsoft-com:vml"));

  return div("", null, array('id' => $name, 'style' => "width: ${width}px; height: ${height}px"))
    . ihidden("${name}[lat]", $clat) . ihidden("${name}[long]", $clong);
}

?>