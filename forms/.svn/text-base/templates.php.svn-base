<?php

$kStandardControlFormat = <<<EOT
			<tr>
				<td class="editlabel" valign="top">\${label}</td>
				<td class="editinput">\${input} \${msg}</td>
			</tr>
EOT;

$kMessageAboveFormat = <<<EOT
			<tr>
				<td class="editmessage" colspan="2">\${msg}</td>
			</tr>
			<tr>
				<td class="editlabel" valign="top">\${label}</td>
				<td class="editinput">\${input}</td>
			</tr>
EOT;

$kStandardNoLabelFormat = <<<EOT
			<tr>
				<td class="editinput" colspan="2">\${input}</td>
			</tr>
EOT;

$kNoLabelMessageAboveFormat = <<<EOT
			<tr>
				<td class="editmessage" colspan="2">\${msg}</td>
			</tr>
			<tr>
				<td class="editinput" colspan="2">\${input}</td>
			</tr>
EOT;

function getFormat($ctrl) {
  global $kStandardControlFormat, $kMessageAboveFormat,
    $kStandardNoLabelFormat, $kNoLabelMessageAboveFormat;

  if (isset($ctrl['format'])) {
    $format = $ctrl['format'];
  } else {
    if (!is_null($ctrl['label'])) {
      if (isset($ctrl['errors']) && $ctrl['errors'] == "above") {
	$format = $kMessageAboveFormat;
      } else {
	$format = $kStandardControlFormat;
      }
    } else {
      if (isset($ctrl['errors']) && $ctrl['errors'] == "above") {
	$format = $kNoLabelMessageAboveFormat;
      } else {
	$format = $kStandardNoLabelFormat;
      }
    }
  }

  return $format;
}

?>