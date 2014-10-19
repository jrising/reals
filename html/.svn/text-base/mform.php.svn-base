<?php

/***** HTML Form Controls *****/
/*
 * Each of these generates an input block with various options
 */

// return an text input
function mitext($name, $size = 20) {
	return "<input type=\"text\" name=\"$name\" value=\"[{" . $name . "}]\" size=\"$size\" />[{" . $name . "_MOD}]";
}

// return a text area input
function mitextarea($name, $rows, $cols) {
	return "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\">[{" . $name . "}]</textarea>[{" . $name . "_MOD}]";
}

// return an hidden form value
function mihidden($name, $value = null) {
	if ($value === null) {
		return "<input type=\"hidden\" name=\"$name\" value=\"[{" . $name . "}]\" />";
	} else {
		return "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
	}
}

// returns a checkbox control
function micheckbox($name, $form = array()) {
	if (isset($form[$name]) && $form[$name]) {
		$checked = "checked=\"checked\" ";
	} else {
		$checked = "";
	}
	return "<input type=\"checkbox\" name=\"$name\" $checked />";
}

// return a block of radio buttons, each with a title (or other content)
// opts is an array of <selector key> => <content>
function miradio($name, $opts, $form = array()) {
	$output = "";
	foreach ($opts as $key => $content) {
		if (isset($form[$name]) && $form[$name] == $key) {
			// this is the default
			$checked = "checked=\"checked\" ";
		} else {
			$checked = "";
		}
		if (empty($output)) { // first row
			$output .= "<input type=\"radio\" name=\"$name\" value=\"$key\" $checked/>$content [{" . $name . "_MOD}]<br />";
		} else {
			$output .= "<input type=\"radio\" name=\"$name\" value=\"$key\" $checked/>$content<br />";
		}
	}

	return $output;
}

// return a select box
// selector is a string of options, such as is created by CreateOptionsFromQuery and CreateOptionsFromArray
function miselect($name, $selector) {
	$output = "<select name=\"$name\">\n";
	$output .= $selector;
	$output .= "</select>[{" . $name . "_MOD}]";

	return $output;
}

// return a submit button
function misubmit($title) {
	return "<input type=\"submit\" name=\"submit\" value=\"$title\">";
}

// return a form to select a date
function midateform($id, $form) {
	$days = array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,
				  12, 13, 14, 15, 16, 17, 18, 19, 20, 21,
				  22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
	$cinfo = cal_info(CAL_GREGORIAN);
	$months = $cinfo['abbrevmonths'];

	// find default month if provided
	if (isset($form[$id . '_month'])) {
		$valmonth = $form[$id . '_month'];
	} else {
		$valmonth = null;
	}
	// find default day if provided
	if (isset($form[$id . '_day'])) {
		$valday = $form[$id . '_day'];
	} else {
		$valday = null;
	}
	
	// display [ MONTH \/ ] [ DAY \/ ] [ YEAR ]
	return miselect($id . '_month', CreateOptionsFromArray($months, $valmonth))
		. miselect($id . '_day', CreateOptionsFromArray($days, $valday))
		. mitext($id . '_year', 4);
}

/*
 * Display options for a select element
 *   $query must return two columns: the first will be used as values for the
 *   option elements, the second will be the displayed names
 * Set $sel to one of the elements names to have that as the default
 */
function CreateOptionsFromQuery($query, $sel = false, $attrfunc = null) {
	$result = dbquery($query);
	$options = "";
	if ($result) {
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			if (!is_null($attrfunc)) {
				$attr = $attrfunc($row);
			} else {
				$attr = "";
			}
			if ($sel !== false && $sel == $row[0]) {
				// this is the default row
				$options .= '<option value="' . $row[0] . '" ' . $attr . ' selected="selected">' . $row[1] . '</option>';
			} else {
				// just another row
				$options .= '<option value="' . $row[0] . '" ' . $attr . ' >' . $row[1] . '</option>';
			}
		}
	}

	return $options;
}

/*
 * Display options for a select element
 *   $query must be of the following type:
 * SHOW COLUMNS FROM t_SupportTicket LIKE 'Product'
 *	 the output's 'Type' field is then parsed for all the enum values
 * Set $sel to one of the elements names to have that as the default
 */
function CreateOptionsFromEnumQuery($query, $sel = false, $attrfunc = null) {
	$result = dbquery($query);
	$options = "";
	if ($result && $row = mysql_fetch_array($result, MYSQL_NUM)) {
		$type = $row[1];
		preg_match_all("/'([A-Za-z0-9 ._-]+)'/", $type, $matches);
		if (sizeof($matches[1]) > 1) {
			$enumToEnum = array();	// value -> name array of enums
			foreach ($matches[1] as $value) {
			   $enumToEnum[] = array($value, $value);
			}
			
			$row = current($enumToEnum);
			while ($row) {
				if (!is_null($attrfunc)) {
					$attr = $attrfunc($row);
				} else {
					$attr = "";
				}
				if ($sel !== false && $sel == $row[0]) {
					// this is the default row
					$options .= '<option value="' . $row[0] . '" ' . $attr . ' selected="selected">' . $row[1] . '</option>';
				} else {
					// just another row
					$options .= '<option value="' . $row[0] . '" ' . $attr . ' >' . $row[1] . '</option>';
				}
				$row = next($enumToEnum);
			}
		}
	}

	return $options;
}

/*
 * Display options for a select element
 *   The keys of $arr will be used as the option names, with the values as
 *   their displayed names
 * Set $sel to one of the elements names to have that as the default
 */
function CreateOptionsFromArray($arr, $sel = false) {
	$options = "";
	foreach ($arr as $id => $val) {
		if ($sel !== false && strval($sel) == strval($id)) {
			// this is the default row
			$options .= '<option value="' . $id . '" selected="selected">' . $val . '</option>';
		} else {
			// just another row
			$options .= '<option value="' . $id . '">' . $val . '</option>';
		}
	}

	return $options;
}

?>