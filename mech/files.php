<?php

$kMimeTypes = array('jpg' => "image/jpeg",
		    'gif' => "image/gif",
		    'cgm' => "image/cgm",
		    'gif' => "image/gif",
		    'htm' => "text/html",
		    'html' => "text/html",
		    'txt' => "text/plain",
		    'pdf' => "application/pdf",
		    'mpg' => "video/mpeg",
		    'mpeg' => "video/mpeg",
		    'rm' => "audio/x-pn-realaudio",
		    'wmv' => "application/x-ms-wmv",
		    'swf' => "application/x-shockwave-flash",
		    'mov' => "video/quicktime",
		    'asf' => "video/x-ms-asf",
		    'asx' => "video/x-ms-asf",
		    'rm' => "audio/x-realaudio",
		    'ram' => "audio/x-pn-realaudio",
		    'zip' => "application/zip",
		    'sit' => "application/x-stuffit",
		    'sitx' => "application/x-stuffit",
		    'exe' => "application/octet-stream");

/*
 * Serve a file as the apparent content of a php function
 */
function ServeFile($filepath) {
  $contenttype = ExtentionToMimeType(pathinfo($filepath, PATHINFO_EXTENSION));
  header("Content-type: $contenttype");
  $filename = basename($filepath);
  if ($contenttype == "application/octet-stream")
    header("Content-Disposition: attachment; filename=$filename");
  readfile($filepath);
  return true;
}

function ExtentionToMimeType($ext) {
  global $kMimeTypes;

  $ext = strtolower($ext);
  if (isset($kMimeTypes[$ext])) {
    return $kMimeTypes[$ext];
  } else {
    return "application/octet-stream";
  }
}

?>