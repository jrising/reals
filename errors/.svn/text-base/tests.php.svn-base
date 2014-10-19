<?php

/*
 * Tests the basic functioning of the Error System defined in errors
 */

/* Output of `php tests.php`:
Content-type: text/html

FailingFunction failed
*/

require_once("base.php");

CallingFunction(array());

if (IsError()) {
	$errors = GetErrors();
	foreach ($errors as $errid) {
		echo ErrorMessage($errid) . "\n";
	}
}

function CallingFunction($arg) {
	FailingFunction($arg);
}

function FailingFunction($arg) {
	if (empty($arg)) {
		EFailure(EC_USER, "FailingFunction failed");
		return true;
	}

	return true;
}

?>
