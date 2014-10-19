<?php

/* 
 * Site-Wide Error System:
 *
 * ENotice($class, $errstr, ...): sets and returns the notice status
 * EWarning($class, $errstr, ...): sets and returns the warning status
 * EFailure($class, $errstr, ...): sets and returns the failure status
 * EFatal($class, $errstr, ...): sets and returns the fatal status
 * EError($level, $class, $errstr, ...): base function for above
 *   this also saves the current trace with debug_backtrace
 * ECascade($errid, $class, $errstr, ...): returns the current status
 * GetErrors($class, $level): checks to see if an error has occurred, returns an array of qualifying error ids, false if no error
 * IsError($class, $level): checks to see if an error has occurred, returns first qualifying error id if so
 * HandleError($errid): remove an error; all errors removed on null
 * ErrorMessage($errid): returns a nicely formated admin error message
 * UserErrorMessage($errid): returns a nicely formated user error message
 */

/*
 * $class may be an integer or a function name string
 * If $class is a function, that function should take the following:
 *   $class(ECARG_START, $errstr, ...): called when error occurs
 *   $class(ECARG_CLASS, $class, $errstr, ...): checks if $class is contained
 *   $class(ECARG_CONTN, $value, $errstr, ...): attempt to continue at error site,
 *      return false if fails again, or if this isn't implemented
 *   $class(ECARG_CLEAN, $errstr, ...): clean up after error
 */

require_once("classes.php");
require_once("output.php");

define('ERRLEVEL_NOTICE', 0);      // just a comment
define('ERRLEVEL_WARNING', 1);     // unexpected result
define('ERRLEVEL_FAILURE', 4);     // things aren't working right
define('ERRLEVEL_PAGEERROR', 4);   // display an error-class page error
define('ERRLEVEL_NODISPLAY', 8);   // don't display result to user
define('ERRLEVEL_FATALERROR', 16); // immediate stop!

// Error class function arguments
define('ECARG_START', 'start');
define('ECARG_CLASS', 'class');
define('ECARG_CONTN', 'cont');
define('ECARG_CLEAN', 'clean');

define('NO_ERROR', "No Error");

// Global Error State
$kErrors = array(0 => NO_ERROR); // dummy error insures id != 0 for errors
$kELevel = 0;

/*
 * ENotice($class, $errstr, ...): sets and returns the notice status
 */
function ENotice() {
	$args = func_get_args();
	array_unshift($args, ERRLEVEL_NOTICE);
	return call_user_func_array('EError', $args);
}

/*
 * EWarning($class, $errstr, ...): sets and returns the warning status
 */
function EWarning() {
	$args = func_get_args();
	array_unshift($args, ERRLEVEL_WARNING);
	return call_user_func_array('EError', $args);
}

/*
 * EFailure($class, $errstr, ...): sets and returns the failure status
 */
function EFailure() {
	$args = func_get_args();
	array_unshift($args, ERRLEVEL_FAILURE);
	return call_user_func_array('EError', $args);
}

/*
 * EFatal($class, $errstr, ...): sets and returns the fatal status
 */
function EFatal() {
	$args = func_get_args();
	array_unshift($args, ERRLEVEL_FATALERROR);
	$result = call_user_func_array('EError', $args);
	fexit();
}

/*
 * EError($level, $class, $errstr, ...): base function for above
 */
function EError() {
	global $kErrors, $kELevel, $kPageMessage, $kErrorMessages;
	
	$args = func_get_args();
	$level = array_shift($args);
	$class = array_shift($args);

	$errstr = array_shift($args);
	$message = vsprintf($errstr, $args);
	$backtrace = debug_backtrace();

	$kELevel += $level;
  
	// page error if error level is high enough
	if ($kELevel >= ERRLEVEL_PAGEERROR && isset($kErrorMessages[$class])) {
		$kPageMessage = $kErrorMessages[$class];
	}
  
	$error = array('level' => $level, 'class' => $class,
				   'errstr' => $errstr, 'args' => $args,
				   'trace' => $backtrace);

	HandleClassFunction($error, ECARG_START);
	
	$kErrors[] = $error;
}

/*
 * ECascade($errid, $class, $errstr, ...): returns the current status
 */
function ECascade() {
	global $kErrors, $kELevel;

	$args = func_get_args();
	$errid = array_shift($args);
	$class = array_shift($args);
  
	$errstr = array_shift($args);
	$message = vsprintf($errstr, $args);
	$backtrace = debug_backtrace();

	$level = $kErrors[$errid]['level'];

	$error = array('level' => $level, 'class' => $class,
				   'errstr' => $errstr, 'args' => $args,
				   'cascade' => $errid, 'trace' => $backtrace);

	HandleClassFunction($error, ECARG_START);
  
	$kErrors[] = $error;
}

/*
 * GetErrors
 * $class is integer error class, or null for all classes
 * $level is error level, or null for all levels
 * Returns array of qualifying errors, or false if the error level is too low
 */
function GetErrors($class = null, $level = null) {
	global $kErrors, $kELevel;

	if (!is_null($level) && $kELevel < $level) {
		return false;
	}

	$returns = array();
	foreach ($kErrors as $errid => $error) {
		if ($errid == 0) {
			continue;
		}
		if ((is_null($level) || $error['level'] >= $level)) {
			if (is_null($class) || (($error['class'] & $class) == $class)) {
				$returns[] = $errid;
				continue;
			}
			if (HandleClassFunction($error, ECARG_CLASS, $class)) {
				$returns[] = $errid;
				continue;
			}
		}
	}

	return $returns;
}

/*
 * IsError
 * $class is integer error class, or null for all classes
 * $level is error level, or null for all levels
 * Returns an errid for a qualifying error, false otherwise
 */
function IsError($class = null, $level = null) {
	global $kErrors, $kELevel;

	if (!is_null($level) && $kELevel < $level) {
		return false;
	}

	foreach ($kErrors as $errid => $error) {
		if ($errid == 0) {
			continue;
		}
		if ((is_null($level) || $error['level'] >= $level)) {
			if (is_null($class) || (($error['class'] & $class) == $class)) {
				return $errid;
			}
			if (HandleClassFunction($error, ECARG_CLASS, $class)) {
				return $errid;
			}
		}
	}
	
	return false;
}

/*
 * Remove an error, or all errors if removed is null
 */
function HandleError($errid = null) {
	global $kErrors, $kELevel, $kPageMessage, $kGenericError;

	if (is_null($errid)) {
		// remove page error, if we made one
		if ($kELevel >= ERRLEVEL_PAGEERROR && $kPageMessage == $kGenericError) {
			$kPageMessage = "";
		}
		$kELevel = 0;
		$kErrors = array(0 => NO_ERROR);
	} else {
		// remove any further cascaded
		foreach ($kErrors as $otherid => $error) {
			if ($otherid == 0) {
				continue;
			}
			if (isset($error['cascade']) && $error['cascade'] == $errid) {
				HandleError($otherid);
			}
		}
    
		HandleClassFunction($kErrors[$errid], ECARG_CLEAN);
		
		$oldelevel = $kELevel;
		
		// only remove level if not cascaded
		if (!isset($kErrors[$errid]['cascade'])) {
			$kELevel -= $kErrors[$errid]['level'];
		}

		// remove page error, if we made one
		if ($kELevel < ERRLEVEL_PAGEERROR && $oldelevel >= ERRLEVEL_PAGEERROR &&
			$kPageMessage == $kGenericError) {
			$kPageMessage = "";
		}

		unset($kErrors[$errid]);
	}
}

/*
 * Return admin-level error message
 */
function ErrorMessage($errid = null) {
	global $kErrors, $kELevel;

	if (is_null($errid)) {
		$errid = GetMostSevere();
	}

	return vsprintf($kErrors[$errid]['errstr'], $kErrors[$errid]['args']);
}

/*
 * Return user-level error message
 */
function UserErrorMessage($errid = null) {
	global $kErrors, $kELevel, $kErrorMessages, $kGenericError;

	if (is_null($errid)) {
		$errid = GetMostSevere();
	}

	$class = $kErrors[$errid]['class'];
  
	if (isset($kErrorMessages[$class])) {
		return $kErrorMessages[$class];
	} else {
		return $kGenericError;
	}
}

/*
 * Return additional debugging information
 */
function ErrorBacktrace($errid = null) {
	global $kErrors, $kELevel;

	if (is_null($errid)) {
		$errid = GetMostSevere();
	}
  
	return print_r(ProtectArrayOutput($kErrors[$errid]['trace']), true);
}

/* Utility Functions */

/*
 * GetMostSevere: return the most severe error id
 */
function GetMostSevere() {
	$errid = null;
  
	// find the highest level
	foreach ($kErrors as $otherid => $error) {
		if ($otherid == 0) {
			continue;
		}
		if (is_null($errid) ||
			$kErrors[$errid]['level'] > $kErrors[$otherid]['level']) {
			$errid = $otherid;
		}
	}
  
	return $errid;
}

/*
 * HandleClass: call a class function, if provided
 */
function HandleClassFunction($error, $func, $optarg = null) {
	if (is_string($error['class'])) {
		if ($func == ECARG_CLASS || $func == ECARG_CONTN) {
			return call_user_func_array($error['class'],
										array_merge(array($func, $optarg,
														  $error['errstr']),
													$error['args']));
		} else {
			return call_user_func_array($error['class'],
										array_merge(array($func,
														  $error['errstr']),
													$error['args']));
		}
	} else {
		if ($func == ECARG_CLASS) {
			return ($error['class'] & $optarg) == $optarg;
		} else if ($func == ECARG_CONTN) {
			return false;
		} else {
			return null;
		}
	}
}

?>
