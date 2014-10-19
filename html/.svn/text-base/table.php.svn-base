<?php

require_once(reals('html/mform.php'));

// Constants for CreateTableFrom* function
// change dynamically (in $GLOBALS) to override default settings
$kTable_fontSize = 'medium'; // css font-size for all text in table
$kTable_showCount = true; // boolean; display "N rows returned" at top
$kTable_showFilter = null; // boolean or functions array; show filtering row
$kTable_showName = null; // string or null; title to display at top of table

// Constant Strings used in tables
define('FILTER_BUTTON', "Filter:");
define('NO_FILTER', 'None');

/*
 * Filters:
 *   Filters are performed as a post-query removal of certain rows
 *   To add a filter row, set $kTable_showFilter to a non-empty array;
 *   The array may contain functions like those in the $funcs array to
 *     translate values into classes for filtering (so the range of the
 *     filter functions should be a subset of the range of the funcs
 *     functions.
 *   In addition, the values in this array may be one of the following:
 *     "" or 0 - displays no filter for that column
 *     1 - display a search textbox
 */

/*
 * Display a cleanly formatted table from the data in $results
 * $results is array(KEY => array(NAME => DATA))
 * $funcs is array(NAME => FUNCTION(KEY, COLDATA, ROWDATA))
 *   called to display data for all columns NAME
 */
function CreateTableFromArray($results, $funcs) {
	global $kTable_fontSize;

	$output = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\">\n";

	// display title row, if requested
	$output .= DisplayTitleRow(count($results[array_rand($results)]));

	// Iterate through each row in the array
	$rowNum = 0;
	foreach ($results as $key => $row) {
		if ($rowNum == 0) {
			// Display the header row first!
			$output .= "\t<tr bgcolor=\"FFCC66\">\n";
			foreach ($row as $title => $data) {
				$output .= "\t\t<th align=\"center\"><span style=\"font-size: $kTable_fontSize\">$title</span></th>\n";
			}
			$output .= "\t</tr>\n";
		}

		// alternate colors for better viewing
		$rowNum++;
		if ($rowNum % 2 == 0) {
			$output .= "\t<tr bgcolor=\"66CCFF\">\n";
		} else {
			$output .= "\t<tr bgcolor=\"99FFFF\">\n";
		}

		// display each value
		foreach ($row as $name => $data) {
			// display the function result if provided
			if (isset($funcs[$name])) {
				$output .= "\t\t<td><span style=\"font-size: $kTable_fontSize\">" . $funcs[$name]($key, $data, $row) . "</span></td>\n";
			} else {
				$output .= "\t\t<td><span style=\"font-size: $kTable_fontSize\">$data</span></td>\n";
			}
		}
		$output .= "\t</tr>\n";
	}

	$output .= "</table>\n";
	return $output;
}


/*
 * Generate a nice table from a SQL query
 * $query is a valid SQL query, without order by or limit clauses
 * $funcs is array(NAME => FUNCTION(KEY, COLDATA, ROWDATA))
 *   called for every column with a matching NAME
 */
function CreateTableFromQuery($query, $funcs = array(), $order = null,
							  $limit = null, $offset = null) {
	global $kTable_fontSize, $kTable_showCount;

	// Prepare the actual query, by adding ordering and limiting
	$finalquery = $query;
	if ($order != null) {
		$finalquery .= " ORDER BY $order";
	}
	if ($limit != null) {
		if ($offset != null) {
			$finalquery .= " LIMIT $limit OFFSET $offset";
		} else {
			$finalquery .= " LIMIT $limit";
		}
	}
	
	//$timeBefore = array_sum(explode(' ', microtime()));	// time the query execution
	
	// Perform the query
	$result = dbquery($finalquery);

	//$timeAfter = array_sum(explode(' ', microtime()));	// time the query execution

	if (!empty($result)) {
		// get the number of results
		$rows = mysql_num_rows($result);
	} else {
		// Error or no results
		$rows = 0;
	}

	// display number of rows (0) and return if no results
	if ($rows == 0) {
		if ($kTable_showCount) {
			//$output = "<p><font color=\"green\" size=\"+1\">$rows rows returned in " . ($timeAfter - $timeBefore) . " seconds.</font></p>\n";
			$output = "<p><font color=\"green\" size=\"+1\">$rows rows returned.</font></p>\n";
		} else {
			$output = "";
		}
		return $output;
	}

	$table = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\">\n";

	// determine url to use in sorting links
	if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
		$urlquery = $_SERVER['QUERY_STRING'];
		// strip out old GET arguments
		$urlquery = preg_replace('/query=.*&|query=.*$/', '', $urlquery);
		$urlquery = preg_replace('/order=.*&|order=.*$/', '', $urlquery);
		if ($urlquery == '?') {
			$urlquery = '';
		}
	} else {
		$urlquery = '';
	}

	// put together new url with query
	if (!empty($urlquery)) {
		$orderurl = $_SERVER['PHP_SELF'] . '?' . $urlquery . '&query=' . urlencode($query);
	} else {
		$orderurl = $_SERVER['PHP_SELF'] . '?query=' . urlencode($query);
	}

	// create header row
	$numFields = mysql_num_fields($result);
	
	// store the table header in separate var to be reused at the bottom of the table, if needed
	
	// display title row, if requested
	$tableHeader = DisplayTitleRow($numFields);

	// display column titles, as sorting links
	$tableHeader .= "\t<tr bgcolor=\"FFCC66\">\n";
	for ($i = 0; $i < $numFields; $i++) {
		$fieldName = mysql_field_name($result, $i);
		if (isset($funcs[$fieldName]) && empty($funcs[$fieldName])) {
			// this is a hidden column
			continue;
		}
		$colnum = $i + 1;
		// only display sorting links if more than one result
		if ($rows > 1) {
			if (preg_match("/^$colnum ASC| +$colnum ASC/", $order)) {
				// descending link
				$tableHeader .= "\t\t<th align=\"center\"><span style=\"font-size: $kTable_fontSize\"><a href=\"$orderurl&order=$colnum+DESC\">$fieldName</a></span><img src=\"graphics/asc.gif\"></th>\n";
			} else if (preg_match("/^$colnum DESC| +$colnum DESC/", $order)) {
				// ascending link
				$tableHeader .= "\t\t<th align=\"center\"><span style=\"font-size: $kTable_fontSize\"><a href=\"$orderurl&order=$colnum+ASC\">$fieldName</a></span><img src=\"graphics/desc.gif\"></th>\n";
			} else {
				// default (ascending) link
				$tableHeader .= "\t\t<th align=\"center\"><span style=\"font-size: $kTable_fontSize\"><a href=\"$orderurl&order=$colnum+ASC\">$fieldName</a></span></th>\n";
			}
		} else {
			// non-sorting title
			$tableHeader .= "\t\t<th align=\"center\"><span style=\"font-size: $kTable_fontSize\">$fieldName</span></th>\n";
		}
	}
	$tableHeader .= "\t</tr>\n";

	// display filter row, if requested
	$tableHeader .= DisplayFilterRow($finalquery, $funcs);

	// insert the table header
	$table .= $tableHeader;

	// display the actual data rows
	$rowNum = 0;
	while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
		// check if this row is shown
		if (!IsVisibleRow($row, $result)) {
			$rows--;
			continue;
		}

		// alternate coloring of rows
		$rowNum++;
		if ($rowNum % 2 == 0) {
			$table .= "\t<tr bgcolor=\"66CCFF\">\n";
		} else {
			$table .= "\t<tr bgcolor=\"99FFFF\">\n";
		}

		$colNum = 0;
		foreach ($row as $name => $data) {
			if (isset($funcs[$name]) && empty($funcs[$name])) {
				// don't display this column
				continue;
			}
			if (is_numeric($name)) {
				continue;  // only process MYSQL_ASSOC part
			}
			// display the cell
			if (isset($funcs[$colNum])) {
				// show result of function, based on column number
				$table .= "\t\t<td><span style=\"font-size: $kTable_fontSize\">" . $funcs[$colNum]($rowNum, $data, $row) . "</span></td>\n";
			} else if (isset($funcs[$name])) {
				// show result of funciton, based on column name
				$table .= "\t\t<td><span style=\"font-size: $kTable_fontSize\">" . $funcs[$name]($rowNum, $data, $row) . "</span></td>\n";
			} else {
				// just show the data
				$table .= "\t\t<td><span style=\"font-size: $kTable_fontSize\">$data</span></td>\n";
			}
			$colNum++;
		}
		$table .= "\t</tr>\n";
	}

	// if there are many rows, insert the table header again at the bottom
	if ($rowNum > 20) {
		$table .= $tableHeader;
	}

	$table .= "</table>\n";

	// display the number of rows (adjusted for those we didn't display)
	if ($kTable_showCount) {
		//$output = "<p><font color=\"green\" size=\"+1\">$rows rows returned in " . ($timeAfter - $timeBefore) . " seconds.</font></p>\n" . $table;
		$output = "<p><font color=\"green\" size=\"+1\">$rows rows returned.</font></p>\n" . $table;
	} else {
		$output = $table;
	}

	mysql_free_result($result);

	return $output;
}

/*
 * Display a form table
 * $contents is an array of Subsection Titles and their contents
 *   Subsection contents is an array of lines
 *   lines are arrays with any of the following keys:
 *     name: label on the row (in left column)
 *     note: note to go under the label
 *     form: input elements (in right column)
 *     desc: longer description to go under the input elements
 * $wleft and $wright are the widths of the two columns
 */
function DisplayUtilityBlock($contents, $wleft, $wright) {
	$width = $wleft + $wright;

	// form to post back to this page
	$output = "<form action=\"${_SERVER['REQUEST_URI']}\" method=\"post\">";
	$output .= "\t<table width=\"$width\" border=\"1\" align=\"center\">\n";

	// display the contents of the table
	foreach ($contents as $title => $subsect) {
		// Generate title or divider line
		$output .= "\t\t<tr bordercolor=\"#CCCCCC\" bgcolor=\"#CCCCCC\">\n";
		if (!is_numeric($title)) {
			// only show if it's a string
			$output .= "\t\t\t<td colspan=\"2\"><div align=\"center\"><strong>$title</strong></div></td>\n";
		} else {
			$output .= "\t\t\t<td colspan=\"2\"></td>\n";
		}
		$output .= "\t\t</tr>\n";

		// display each row of the table
		foreach ($subsect as $key => $line) {
			$titlebox = "";
			if (isset($line['name'])) {
				$titlebox .= $line['name'];  // name goes in first
			}
			if (isset($line['name']) && isset($line['note'])) {
				$titlebox .= "<br />";  // break if both name and note
			}
			if (isset($line['note'])) {
				$titlebox .= "<font size=\"-1\">${line['note']}</font>";
			}

			if (isset($line['form'])) {
				$formbox = $line['form']; // form goes in first
			} else {
				$formbox = "";
			}

			if (isset($line['desc'])) {
				$formbox .= " <span style=\"float: right\"><font size=\"-1\">${line['desc']}</font></span>";  // description goes below and to the right
			}

			// add the row to our result
			if (!empty($titlebox)) {
				$output .= "\t\t<tr>\n";
				$output .= "\t\t\t<td><div align=\"right\">$titlebox</div></td>\n";
				$output .= "\t\t\t<td width=\"$wright\">$formbox</td>\n";
				$output .= "\t\t</tr>\n";
			} else {
				$output .= "\t\t<tr>\n";
				$output .= "\t\t\t<td colspan=\"2\" align=\"center\">$formbox</td>\n";
				$output .= "\t\t</tr>\n";
			}
		}
	}

	$output .= "\t<table>\n";
	$output .= "</form>\n";

	return $output;
}

/***** Utility Functions for Table Creation *****/

// Display a multiple-column title row, if requested
function DisplayTitleRow($numcols) {
	global $kTable_showName;

	if (!is_null($kTable_showName)) {
		$output = '<tr><th colspan="' . $numcols . '">' . $kTable_showName . '</th></tr>';
	} else {
		$output = '';
	}

	return $output;
}

// Display a row of filter selectors, if requested
function DisplayFilterRow($query, $funcs) {
    global $kTable_showFilter;

    if (empty($kTable_showFilter)) {
        return '';
    }

    // uniques is {colid => {value => value}}
    $uniques = array();
	// colnames is {colid => column name}
	$colnames = array();

	// collect all unique values by performing query
    $result = dbquery($query);
    while ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $colid = 0;
        foreach ($row as $assoc => $value) {
			// skip for filtering if null
			if (isset($kTable_showFilter[$assoc]) &&
				(empty($kTable_showFilter[$assoc]) ||
				 is_int($kTable_showFilter[$assoc]))) {
				$colnames[$colid] = $assoc;
				$colid++;
				continue;
			}
			if (!isset($uniques[$colid])) {
				// this is the first sign of need for a filter here!
				$uniques[$colid] = array(NO_FILTER => NO_FILTER);
				$colnames[$colid] = $assoc;
			}

			// display from a function, if provided
            if (isset($kTable_showFilter[$assoc])) {
				$disp = $kTable_showFilter[$assoc](null, $value, $row);
			} else {
				$disp = $value;
			}

			// add element
			$uniques[$colid][$disp] = $disp;
			$colid++;
		}
	}

	// Display row
	$numcols = mysql_num_fields($result);
	$output = '<form method="post"><td>' . misubmit(FILTER_BUTTON) . '</td>';
	for ($colid = 1; $colid < $numcols; $colid++) {
		// check that this is a visible column
		if (isset($funcs[mysql_field_name($result, $colid)]) &&
			empty($funcs[mysql_field_name($result, $colid)])) {
			continue;
		}
		if (!isset($uniques[$colid])) {
			// Look for special features
			if (isset($kTable_showFilter[$colnames[$colid]])) {
				if ($kTable_showFilter[$colnames[$colid]] == 1) {
					// display a search field
					if (isset($_POST['F:' . $colid])) {
						$output .= '<td><input class="filter" type="text" name="F:' . $colid . '" value="' . $_POST['F:' . $colid] . '" /></td>';
					} else {
						$output .= '<td><input class="filter" type="text" name="F:' . $colid . '" /></td>';
					}
				} else {
					// display a blank
					$output .= '<td></td>';
				}
			} else {
				// display a blank
				$output .= '<td></td>';
			}
		} else {
			// display the filter
			if (isset($_POST['F:' . $colid])) {
				$output .= '<td><select name="F:' . $colid . '">' . CreateOptionsFromArray($uniques[$colid], $_POST['F:' . $colid]) . '</select></td>';
			} else {
				$output .= '<td><select name="F:' . $colid . '">' . CreateOptionsFromArray($uniques[$colid]) . '</select></td>';
			}
		}
	}
	$output .= '</form>';

	mysql_free_result($result);

	return '<tr>' . $output . '</tr>';
}

/*
 * Check if this is a visible row, based on the filtering data
 */
function IsVisibleRow($row, $result) {
	global $kTable_showFilter;

	//if (isset($_POST['submit']) && $_POST['submit'] == FILTER_BUTTON &&
	if (!empty($kTable_showFilter)) {

		// yes, we did just filter
		foreach ($row as $key => $value) {
			$name = mysql_field_name($result, $key);

			if (isset($_POST['F:' . $key])) {
				// Check if this is a normal select filter column
				if (!isset($kTable_showFilter[$name]) ||
					$kTable_showFilter[$name] != 1) {
					// Did we just filter on this column?
					if ($_POST['F:' . $key] != NO_FILTER) {
						// see what value this would show as for filtering
						if (isset($kTable_showFilter[$name])) {
							$disp = $kTable_showFilter[$name](null, $value, $row);
						} else {
							$disp = $value;
						}

						// if it doesn't match, don't show it
						if ($disp != $_POST['F:' . $key]) {
							return false;
						}
					}
				} else {
					// Search filtering
					// Did we just filter on this column?
					if (!empty($_POST['F:' . $key])) {
						if (mb_strpos($_POST['F:' . $key], '%') === false) {
							// Simple "text contained" search
							if (stristr($value, $_POST['F:' . $key]) === false) {
								return false;
							}
						} else {
							// Translate SQL search into PREG
							$pattern = str_replace(array('%', '_'), array('.*', '.'),
												   preg_quote($_POST['F:' . $key], "/"));
							if (preg_match('/^' . $pattern . '$/i', $value) == 0) {
								return false;
							}
						}
					}
				}
			}
		}
	}

	return true;
}

?>