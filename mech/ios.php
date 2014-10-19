<?php

/***** Cross-Language Communication *****/

/*
 * The general output syntax for our remote scripts is:
 *   [name=value]\t[name=value]\t[name=value]\n
 *   [name=value]\t[name=value]\t[name=value]\n
 * This is interpreted into a number keyed array containing string keyed arrays
 */

/*
 * Call a script with a web interface (GET arguments, displays result)
 * Return the result as an array
 */
function CallWebScript($page, $args) {
	global $kDNSCheckURL, $kAdminEmailAddr;

	// Construct url to go to
	if (!empty($args)) {
		$query = http_build_query($args);
		$url = $page . '?' . $query;
	} else {
		$url = $page;
	}

    // Call script and get results
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

	// check that the output looks right
	if (!preg_match("/^([a-zA-Z0-9_]*=[^\t]*\t?)*$/", $output)) {
		// Is the problem a bad DNS lookup?  check and mail
		$ch = curl_init($kDNSCheckURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$dnsout = curl_exec($ch);
		curl_close($ch);

		mb_send_mail($kAdminEmailAddr, 'Warning: CallWebScript got invalid result', "Syntax of result of $url unexpected: $output\n\nDNS Check: $dnsout");

		return array(); // no data
	}

    // Parse results
    $lines = explode("\n", $output);

    $result = array();
	foreach ($lines as $ii => $line) {
		if (empty($line)) {
			continue;
		}
		$pairs = explode("\t", $line);
		$result[$ii] = array();
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair);
			$result[$ii][$key] = $value;
		}
	}

	return $result;
}

/*
 * Call a script with a command-line interface (arguments of the form name=val)
 * Return the result as an array
 */
function CallScript($cmd, $args) {
	// Construct url to go to
	$argstr = "";
	if (!empty($args)) {
		foreach ($args as $key => $val) {
			$argstr .= " $key=$val";
		}
	}

    // Call script and get results
	exec($cmd . $argstr, $lines);

    // Parse results
    $result = array();
	$jj = 0;
	foreach ($lines as $ii => $line) {
		if ($line == "Content-type: text/html") {
			continue;
		}
		if (empty($line)) {
			continue;
		}
		$pairs = explode("\t", $line);
		$result[$jj] = array();
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair);
			$result[$jj][$key] = $value;
		}
		$jj++;
	}

	return $result;
}

?>