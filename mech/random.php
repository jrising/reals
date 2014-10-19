<?php

function include_random($dir) {
  $dh  = opendir($dir);
  while (false !== ($filename = readdir($dh))) {
    if ($filename{0} == '.')
      continue;
    $files[] = $filename;
  }
  $incfile = $files[array_rand($files)];
  include($dir . $incfile);
}

function box($content) {
  return <<<EOT
<table cellspacing="0" cellpadding="0">
  <tr>
    <td>
      $content
    </td>
  </tr>
</table>
EOT;
}

?>