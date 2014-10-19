<?php

// Basic Messages
//   standard results to bad user input
$kRequiredFieldMod = terror(" (required)");
$kInvalidNameMod = terror(" (invalid name)");
$kInvalidStateMod = terror(" (invalid state)");
$kInvalidZipMod = terror(" (invalid zip code)");
$kInvalidPhoneMod = terror(" (invalid phone #)");
$kInvalidEmailMod = terror(" (invalid e-mail)");
$kInvalidEmailsMod = terror(" (invalid e-mails)");
$kEmailMismatchMod = terror(" (confirm e-mail)");
$kInvalidExpirationMod = terror(" (invalid expiration)");
$kInvalidDateMod = terror(" (invalid date)");
$kRequireUniqueMod = terror(" (duplicate entry)");
$kFieldTooLong = terror(" (too long)");
$kInvalidCharacters = terror(" (invalid characters)");
$kInvalidStateCombo = terror(" (invalid combination)");
$kValidatingError = terror(" (internal error)");
$kInvalidPrice = terror(" (invalid price)");

// Masks
//   for use with RequireFormCode and OptionalCode
$kCapitalAlphaMask = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$kNumericMask = "0123456789";
$kPrintableMask = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+'\"[]{}`~;:,<.>/?\\| \t\n\r";

/*
 * The functions!  Comine the results of these functions into one
 * array for testing the validity of the whole form and preparing the
 * error result page
 */

/*
 * Input arguments:
 * $form: the input array (e.g., $_POST or $_GET)
 * $id: the input name to check (e.g., 'email')
 * $kMod: response on input failure (e.g., terror(" (invalid email)"))
 */

// check that a required field is filled
function RequireFormField($form, $id) {
	global $kRequiredFieldMod;

	if (!isset($form[$id]) || empty($form[$id])) {
		return MakeMod($id, $kRequiredFieldMod);
	} else {
		return array();
	}
}

// input must be a-zA-Z
function RequireFormAlpha(&$form, $id, $kMod) {
	global $kRequiredFieldMod;

	$validmask = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

	if (isset($form[$id]) && !empty($form[$id])) {
		$length = mb_strlen($form[$id]);
		if (strspn($form[$id], $validmask) == $length) {
			return array();
		} else {
			return MakeMod($id, $kMod);
		}
	} else {
		return MakeMod($id, $kRequiredFieldMod);
	}
}

// only printable characters may be used
function OptionalPrintable($form, $id, $maxlen) {
	global $kFieldTooLong, $kInvalidCharacters, $kPrintableMask;

	if (isset($form[$id]) && !empty($form[$id])) {
		$length = mb_strlen($form[$id]);
		if (strspn($form[$id], $kPrintableMask) == $length) {
			if ($length <= $maxlen) {
				return array();
			} else {
				return MakeMod($id, $kFieldTooLong);
			}
		} else {
			return MakeMod($id, $kInvalidCharacters);
		}
	} else {
		return array();
	}
}

// only printable characters may be used
function RequirePrintable($form, $id, $maxlen) {
	global $kRequiredFieldMod, $kFieldTooLong;

	if (isset($form[$id]) && !empty($form[$id])) {
		return OptionalPrintable($form, $id, $maxlen);
	} else {
		return MakeMod($id, $kRequiredFieldMod);
	}
}

// check that a name is entered correctly
function RequireFormName(&$form, $id) {
	global $kInvalidNameMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		$form[$id] = trim($form[$id]);
		if (mb_strpos($form[$id], ' ') === false) {
			return MakeMod($id, $kInvalidNameMod);
		} else {
			return array();
		}
	} else {
		return RequireFormField($form, $id);
	}
}

// Check that a given State, Country pair is possible
function OptionalState($form, $idstate, $idcountry) {
	global $kInvalidStateCombo;

	if (isset($form[$idstate]) && isset($form[$idcountry]) &&
		!empty($form[$idstate]) && !empty($form[$idcountry])) {
		$result = dbquery("SELECT CountryID FROM t_StateProvince WHERE Code = %s", $form[$idstate]);
		if ($result && $row = mysql_fetch_array($result, MYSQL_NUM)) {
			if ($row[0] == $form[$idcountry]) {
				return array();
			} else {
				return MakeMod($idcountry, $kInvalidStateCombo);
			}
		} else {
			return MakeMod($idcountry, $ValidatingError);
		}
	} else {
		return array();
	}
}

// require a state if the country is USA or Canada
function RequireFormState(&$form, $id, $idcry) {
	if (isset($form[$idcry]) &&
		($form[$idcry] == 209 || $form[$idcry] == 35)) {
		return RequireFormField($form, $id);
	} else {
		return array();
	}

	/*global $kInvalidStateMod;
	$validmask = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	return RequireFormCode($form, $id, $validmask, 2, 2,
	'strtoupper', $kInvalidStateMod);*/

}

// check that a zip code is valid
function OptionalZipCode(&$form, $id) {
	global $kInvalidZipMod;
	
	$validmask = "0123456789- ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	return OptionalCode($form, $id, $validmask, 4, 10,
						'strtoupper', $kInvalidZipMod);
}

// check that a zip code is valid
function RequireFormZipCode(&$form, $id) {
	global $kInvalidZipMod;

	$validmask = "0123456789- ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	return RequireFormCode($form, $id, $validmask, 3, 10,
						   'strtoupper', $kInvalidZipMod);
}

// check that a phone number is valid
// uses OptionalCode, but doesn't modify original form
function OptionalPhone($form, $id) {
	global $kInvalidPhoneMod;

	$validmask = "0123456789";
	return OptionalCode($form, $id, $validmask, 9, 30,
						'phonestrip', $kInvalidPhoneMod);
}

// check that a phone number is valid
// uses RequireFormCode, but doesn't modify original form
function RequireFormPhone($form, $id) {
	global $kInvalidPhoneMod;

	$validmask = "0123456789";
	return RequireFormCode($form, $id, $validmask, 9, 30,
						   'phonestrip', $kInvalidPhoneMod);
}

// check that a month/year combination is a valid expiration date
function RequireFormExpiration(&$form, $idmonth, $idyear, $idall) {
	global $kInvalidExpirationMod, $kRequiredFieldMod;

	if (!isset($form[$idmonth]) || empty($form[$idmonth]) ||
		!isset($form[$idyear]) || empty($form[$idyear])) {
		return MakeMod($idall, $kRequiredFieldMod);
	} else {
		if (!is_numeric($form[$idyear]) || mb_strlen($form[$idyear]) != 2 ||
			!is_numeric($form[$idmonth]) || mb_strlen($form[$idmonth]) > 2) {
			return MakeMod($idall, $kInvalidExpirationMod);
		}
		if (mb_strlen($form[$idmonth]) < 2) {
			$form[$idmonth] = sprintf("%02d", $form[$idmonth]);
		}
		// check if a valid date in the future
		if ($form[$idmonth] == 12) {
			$exptime = mktime(0, 0, 0, 1, 1, $form[$idyear] + 1);
		} else {
			$exptime = mktime(0, 0, 0, $form[$idmonth] + 1, 1, $form[$idyear]);
		}
		if ($exptime < time()) { // this catches invalid mktime arguments
			return MakeMod($idall, $kInvalidExpirationMod);
		} else {
			return array();
		}
	}
}

// check that an email address is valid
function OptionalEmail($form, $id) {
	global $kInvalidEmailMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		if (checkEmail($form[$id])) {
			return array();
		} else {
			return MakeMod($id, $kInvalidEmailMod);
		}
	} else {
		return array();
	}
}

// check that multiple, csv email addresses are valid
function OptionalEmails($form, $id) {
	global $kInvalidEmailsMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		if (strstr($form[$id], ',')) {
			preg_match_all("/([^,]+),/", $form[$id] . ',', $matches);
			if (sizeof($matches[1]) > 0) {
				foreach ($matches[1] as $value) {
					if (!checkEmail(trim($value))) {
						return MakeMod($id, $kInvalidEmailsMod);
					}
				}
			}
		} else {
			if (checkEmail($form[$id])) {
				return array();
			} else {
				return MakeMod($id, $kInvalidEmailsMod);
			}
		}
	} else {
		return array();
	}
}

// check that an email address is valid
function RequireFormEmail($form, $id) {
	global $kRequiredFieldMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		return OptionalEmail($form, $id);
	} else {
		return MakeMod($id, $kRequiredFieldMod);
	}
}

// check that multiple email addresses are valid
function RequireFormEmails($form, $id) {
	global $kRequiredFieldMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		return OptionalEmails($form, $id);
	} else {
		return MakeMod($id, $kRequiredFieldMod);
	}
}

// check that generalized code is valid
//  applies $preproc, then checks that all characters are in $validmask,
//  and that the total length is between $minlen and $maxlen
function OptionalCode(&$form, $id, $validmask, $minlen, $maxlen,
					  $preproc, $kInvalidMod) {
	global $kRequiredFieldMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		$form[$id] = $preproc($form[$id]);
		$length = mb_strlen($form[$id]);
		if ($length >= $minlen && $length <= $maxlen &&
			strspn($form[$id], $validmask) == $length) {
			return array();
		} else {
			return MakeMod($id, $kInvalidMod);
		}
	} else {
		return array();
	}
} 

// check that generalized code is valid
//  applies $preproc, then checks that all characters are in $validmask,
//  and that the total length is between $minlen and $maxlen
function RequireFormCode(&$form, $id, $validmask, $minlen, $maxlen,
						 $preproc, $kInvalidMod) {
	global $kRequiredFieldMod;

	if (isset($form[$id]) && !empty($form[$id])) {
		$form[$id] = $preproc($form[$id]);
		$length = mb_strlen($form[$id]);
		if ($length >= $minlen && $length <= $maxlen &&
			strspn($form[$id], $validmask) == $length) {
			return array();
		} else {
			return MakeMod($id, $kInvalidMod);
		}
	} else {
		return MakeMod($id, $kRequiredFieldMod);
	}
} 

// require that an input equals a particular value
function RequireEquals($form, $id, $value, $kMod) {
	global $kRequiredFieldMod;

	if (isset($form[$id])) {
		if ($form[$id] == $value) {
			return array();
		} else {
			return MakeMod($id, $kMod);
		}
	} else {
		return MakeMod($id, $kRequiredFieldMod);
	}
}

// Check that a date is valid
function CheckValidDate(&$form, $idday, $idmonth, $idyear, $idMod) {
	global $kInvalidDateMod;

	if ((!isset($form[$idday]) || empty($form[$idday])) &&
		(!isset($form[$idyear]) || empty($form[$idyear]))) {
		return array(); // not required here
	} else {
		// We only handle numeric data forms
		if (!is_numeric($form[$idyear]) || 
			(mb_strlen($form[$idyear]) != 2 && mb_strlen($form[$idyear]) != 4) ||
			!is_numeric($form[$idmonth]) || mb_strlen($form[$idmonth]) > 2 ||
			!is_numeric($form[$idday]) || mb_strlen($form[$idday]) > 2) {
			return MakeMod($idMod, $kInvalidDateMod);
		}
		if (mb_strlen($form[$idyear]) == 2) {
			// 2-digit years are from 1951 - 2050
			if ($form[$idyear] < 1) {
				return MakeMod($idMod, $kInvalidDateMod);
			}
			if ($form[$idyear] > 50) {
				$form[$idyear] = '19' . $form[$idyear];
			} else {
				$form[$idyear] = '20' . $form[$idyear];
			}
		} else if ($form[$idyear] < 1 || $form[$idyear] > 3000) {
			// 4 digit years are from 1 - 3000, A.D.
			return MakeMod($idMod, $kInvalidDateMod);
		}
		if ($form[$idmonth] < 1 || $form[$idmonth] > 12) {
			// months are Jan - Dec. (1 - 12)
			return MakeMod($idMod, $kInvalidDateMod);
		}
		// days depend on the month-- check how many
		$monthdays = cal_days_in_month(CAL_GREGORIAN, $form[$idmonth],
									   $form[$idyear]);
		if ($form[$idday] < 1 || $form[$idday] > $monthdays) {
			return MakeMod($idMod, $kInvalidDateMod);
		}
	}

	return array();
}

// Check that a number, if provided, lies within limits
//   either min or max can be null (no min or max, respectively)
function CheckNumberRange($form, $id, $min, $max, $kMod) {
	if (!isset($form[$id]) || empty($form[$id])) {
		return array(); // not required here
	} else {
		if (!is_numeric($form[$id])) {
			return MakeMod($id, $kMod);
		}
		if ($min !== null && $form[$id] < $min) {
			return MakeMod($id, $kMod);
		}
		if ($max !== null && $form[$id] > $max) {
			return MakeMod($id, $kMod);
		}
	}

	return array();
}

// check if this represents a valid price
function CheckFormPrice(&$form, $id) {
	global $kInvalidPrice;

	if (!isset($form[$id]) || empty($form[$id])) {
		return array();
	} else {
		$form[$id] = preg_replace('/^\$/', '', trim($form[$id]));
		if (!is_numeric($form[$id]) || $form[$id] < 0) {
			return MakeMod($id, $kInvalidPrice);
		} else {
			return array();
		}
	}
}

// check that a date, $id_month/$id_day/$id_year is valid and
//   set $form[id] to a sql compatible representation of the date (or null)
function CheckFormDate(&$form, $id) {
	global $kInvalidMonth, $kInvalidDay, $kInvalidYear;

	$idm = $id . '_month';
	$idd = $id . '_day';
	$idy = $id . '_year';
	$mods1 = array_merge(CheckNumberRange($form, $idy, 1900, 2100, $kInvalidYear),
						 CheckNumberRange($form, $idm, 1, 12, $kInvalidMonth),
						 CheckNumberRange($form, $idd, 1,
										  cal_days_in_month(CAL_GREGORIAN,
															$form[$idm],
															$form[$idy]),
										  $kInvalidDay));

	$mods2 = array_merge(RequireFormField($form, $idy),
						 RequireFormField($form, $idm),
						 RequireFormField($form, $idd));
	
	if (empty($mods2)) {
		$form[$id] = $form[$idy] . '-' . $form[$idm] . '-' . $form[$idd];
	} else {
		$form[$id] = null;
	}

	return $mods1;
}


// require that one of these fields is valid
function RequireOneOf($mods1, $mods2) {
	if (empty($mods1) || empty($mods2)) {
		return array();
	} else {
		return RequireFirstSecond($mods1, $mods2);
	}
}

// require that two fields are equal
function RequireFieldsMatch($form, $id1, $id2, $idErr, $kErrMod) {
	if (isset($form[$id1]) && isset($form[$id2]) &&
		$form[$id1] == $form[$id2]) {
		return array();
	} else {
		return MakeMod($idErr, $kErrMod);
	}
}

// return first mod, if there is one, and second if there isn't
function RequireFirstSecond($mods1, $mods2) {
	if (empty($mods1)) {
		return $mods2;
	} else {
		return $mods1;
	}
}

// returns id of newly created substring
function RequireForSubstring(&$form, $id, $start, $len) {
	$newid = $id . '[' . $start . ':' . $len . ']';
	if (isset($form[$id])) {
		$form[$newid] = substr($form[$id], $start, $len);
	}
	return $newid;
}

// Replace substring with any results from using RequireForSubstring
// with another require function.  Used as follows:
//CleanupForSubstring($form, 'Foo', 0, 2,
//					  RequireFormAlpha($form,
//									   RequireForSubstring($form, 'Foo', 0, 2)));
function CleanupForSubstring(&$form, $id, $start, $len, $retval = true) {
	$newid = $id . '[' . $start . ':' . $len . ']';
	if (isset($form[$id]) && isset($form[$newid])) {
		if ($start > 0) {
			$newval = substr($form[$id], 0, $start);
		} else {
			$newval = "";
		}
		$newval .= $form[$newid];
		if (mb_strlen($form[$id]) - $start > $len) {
			$newval .= substr($form[$id], $start + $len);
		}

		$form[$id] = $newval;
		unset($form[$newid]);
	} else if (isset($form[$newid])) {
		unset($form[$newid]);
	}

	return $retval;
}

// require that a field not exist in a table
function RequireUniqueEntry($form, $id, $table, $col) {
	global $kRequiredFieldMod, $kRequireUniqueMod;

	if (!isset($form[$id]) || empty($form[$id])) {
		return MakeMod($id, $kRequiredFieldMod);
	} else {
		$result = dbquery("SELECT COUNT(*) FROM $table WHERE $col = %s",
						  $form[$id]);
		if ($result && $row = mysql_fetch_array($result, MYSQL_NUM)) {
			if ($row[0] > 0) {
				mysql_free_result($result);
				return MakeMod($id, $kRequireUniqueMod);
			} else {
				mysql_free_result($result);
				return array();
			}
		}
		return array();
	}
}

// Generate an error if given an empty (errorless) mod array, or
// return an errorless result if given an error mod array
function RequireNot($modarr, $id, $kmod) {
	if (empty($modarr)) {
		return MakeMod($id, $kmod);
	} else {
		return array();
	}
}

/***** Utility functions *****/

// generate a modification array
function MakeMod($tagid, $replace) {
	return array($tagid . "_MOD" => $replace);
}

// generate an error message
function terror($text) {
	return "<span class=\"ERROR_TEXT_STYLE\">$text</span>";
}

// strip additional characters from phone numbers
function phonestrip($text) {
	return str_replace(array(' ', '-', '+', '(', ')'), array(), $text);
}

// expand a date into its components
function ExpandFormDate(&$repl, $id) {
	if (preg_match('/^(\d+)-(\d+)-(\d+)\s.*/', $repl[$id], $matches)) {
		$repl[$id . '_year'] = $matches[1];
		$repl[$id . '_month'] = $matches[2];
		$repl[$id . '_day'] = $matches[3];
	}
}

function checkEmail($email) {
	return preg_match("/^[A-Za-z0-9]+[A-Za-z0-9_.-]+@[A-Za-z0-9]+[A-Za-z0-9_.-]+\.[A-Za-z]{2,6}$/", $email) == 1;
}

?>
