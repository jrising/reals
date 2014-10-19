<?php

// Product a price text: $#.00 or #.00 (currency)
function FormatPrice($amt, $currency = null, $decimals = 2) {
	if (is_null($currency)) {
		if ($amt < 0) {
			return sprintf("-$%.${decimals}f", -$amt);
		} else {
			return sprintf("$%.${decimals}f", $amt);
		}
	} else {
		return sprintf("%.${decimals}f (%s)", $amt, $currency);
	}
}

// Product a percentage
function FormatPercent($frac, $decimals = 0) {
	return sprintf("%.${decimals}f%%", $frac * 100);
}

// Produce a long english style date
function FormatLongDate($date) {
	if (!is_integer($date)) {
		$date = strtotime($date);
	}

	return date('F j, Y, g:i A T', $date) . " (GMT " . date('O)', $date);
}

/***** Text Block Formatting *****/

// Indent a block of text with a line prepending string or a number of spaces
function TextIndent($prepend, $text) {
	if (is_int($prepend)) {
		$prepend = str_repeat(' ', $prepend);
	}
	return $prepend . str_replace("\n", "\n" . $prepend, $text);
}

// Label some data, indenting all data lines by at least ppnum spaces
function LabelText($ppnum, $label, $data) {
	$firstn = mb_strpos($data, "\n");
	if ($firstn === false) {
		$firstline = $data;
		$restlines = "";
	} else {
		$firstline = substr($data, 0, $firstn);
		$restlines = substr($data, $firstn + 1);
	}
	if (mb_strlen($label) > $ppnum) {
		$result = $label . ' ' . $firstline . "\n";
	} else {
		$result = $label . str_repeat(' ', $ppnum - mb_strlen($label)) .
			$firstline . "\n";
	}

	if ($restlines != "") {
		$result .= TextIndent($ppnum, $restlines) . "\n";
	}
	return $result;
}

/*
 * Crop a region with ... both vertically and horizontally
 *   rows or cols may be null to allow unlimited of them
 */
function TextCrop($text, $rows, $cols) {
	$result = "";
	$lines = explode("\n", $text);
	if (!is_null($rows) && count($lines) > $rows) {
		$rows--; // one line for '...'
	}
	foreach ($lines as $line) {
		if (!is_null($rows)) {
			$rows--;
			if ($rows < 0) {
				break;
			}
		}
		if (!is_null($cols) && mb_strlen($line) > $cols) {
			$result .= substr($line, 0, $cols - 3) . "...\n";
		} else {
			$result .= $line . "\n";
		}
	}
	if (!is_null($rows) && $rows < 0) {
		$result .= "...\n";
	}

	return $result;
}

/***** Friendly Formatting *****/

function FriendlyExpiryTime($exp) {
	if ($exp == 0) {
		return "";
	}
	if (($exp >= 1) && ($exp <= 59)) {
		if ($exp == 1) {
			return "$exp minute";
		} else {
			return "$exp minutes";
		}
	}
	if (($exp >= 60) && ($exp <= 1440)) {
		$hours = intval($exp / 60);
		$mins = $exp % 60;
		if ($hours == 1) {
			$ret = $hours . " hour";
		} else {
			$ret = $hours . " hours";
		}
		if ($mins > 0) {
			$ret .= " & " . FriendlyExpiryTime($mins);
		}
	}
	if ($exp >= 1441) {
		$days = intval($exp / 1440);
		$exp = $exp - ($days * 1440);
		$hours = intval($exp / 60);
		$mins = $exp % 60;
		if ($days == 1) {
			$ret = $days . " day";
		} else {
			$ret = $days . " days";
		}
		if ($hours > 0) {
			if ($mins == 0) {
				$ret .= " & ";
			} else {
				$ret .= " ";
			}
			return $ret . FriendlyExpiryTime($hours * 60 + $mins);
		}
		if ($mins > 0) {
			return $ret . FriendlyExpiryTime($mins);
		}
    }
    return $ret;
}

function FriendlyFileSize($sz) {
	if ($sz <= 1023) {
		return $sz . " Bytes";
	}
	if (($sz >= 1024) && ($sz <= 1048575)) {
		$sz = intval($sz / 1024);
		return $sz . " KB";
	}
	if ($sz >= 1048576) {
		$sz = $sz / 1048576;
		$sz = intval($sz * 100) / 100;
		return $sz . " MB";
	}
}

?>