<?php

require_once('dbfuncs2.php');
require_once('cached.php');
require_once('errors.php');

define('MAX_CACHE_ROWS', 100);

// Transparent database function wrappers for using the cache

function cdbquery() {
	global $rowlocs;

	$args = func_get_args();
	$query = call_user_func_array('dbprep', $args);

	// is this in the cache?
	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($exists = memcache_get($memconn, $query . '%')) {
			// store current row in global memory
			$rowlocs[$key = uniqid(rand(), true)] = array($query, 0);
			return $key;
		}
	}

	// perform query
	$result = dbquery($query);
	if (!$result) {
		return $result;
	}

	// if there's too much data, don't cache
	if (!$memconn || dbrows($result) > MAX_CACHE_ROWS) {
		$rowlocs[$key = uniqid(rand(), true)] = $result;
		return $key;
	}

	memcache_set($memconn, $query . '%', dbrows($result), false, DAY_SECS);
	// collect all result rows
	for ($rowii = 0; $rowii < dbrows($result); $rowii++) {
		if (!($row1 = dbfetch($result, DB_NUM))) {
			EWarning(EC_DATABASE, "Failed to get DB_NUM row");
		}
		dbseek($result, $rowii);
		if (!($row2 = dbfetch($result, DB_ASSOC))) {
			EWarning(EC_DATABASE, "Failed to get DB_ASSOC row");
		}
		memcache_set($memconn, $query . '%' . $rowii,
					 array(DB_NUM => $row1, DB_ASSOC => $row2),
					 false, DAY_SECS);
	}

	dbfree($result);

	$rowlocs[$key = uniqid(rand(), true)] = array($query, 0);
	
	return $key;
}

function cdbfetch($reskey, $type) {
	global $rowlocs;

	if (!isset($rowlocs[$reskey])) {
		return false;  // fail
	}

	$getval = $rowlocs[$reskey];
	if (!is_array($getval)) {
		return dbfetch($getval, $type);
	}

	$memconn = GetMemcacheConnection();
	if (!$memconn) {
		EWarning(EC_DATABASE, "Required cache but couldn't get");
		return false; // fail
	}

	// check whether this is from previously cached data
	if ($row = memcache_get($memconn, $getval[0] . '%' . $getval[1])) {
		$rowlocs[$reskey][1]++;
		if ($type == DB_NUM) {
			return $row[DB_NUM];
		} else if ($type == DB_ASSOC) {
			return $row[DB_ASSOC];
		} else if ($type == DB_BOTH) {
			return $row[DB_NUM] + $row[DB_ASSOC];
		} else {
			return false; // unknown type
		}
	} else {
		unset($rowlocs[$reskey]);
		return false;  // no rows left
	}
}

// return the number of rows of a query result
function cdbrows($reskey) {
	global $rowlocs;

	if (!isset($rowlocs[$reskey])) {
		return false;  // fail
	}

	if (!is_array($rowlocs[$reskey])) {
		return dbrows($rowlocs[$reskey]);
	}

	$memconn = GetMemcacheConnection();
	if (!$memconn) {
		EWarning(EC_DATABASE, "Required cache but couldn't get");
		return false; // fail
	}
	
	// check whether this is from previously cached data
	if ($rowcnt = memcache_get($memconn, $getval[0] . '%')) {
		return $rowcnt;
	}

	return false;
}

// move to a given row in the result
function cdbseek($reskey, $index) {
	global $rowlocs;

	if (!isset($rowlocs[$reskey])) {
		return false;  // fail
	}

	if (!is_array($rowlocs[$reskey])) {
		return dbseek($rowlocs[$reskey], $index);
	}

	$rowlocs[$reskey][1] = $index;

	return true;
}

// free a result (doesn't clear cache, just removes pointer into it)
function cdbfree($reskey) {
	global $rowlocs;

	if (isset($rowlocs[$reskey])) {
		if (!is_array($rowlocs[$reskey])) {
			dbfree($rowlocs[$reskey]);
		}
		unset($rowlocs[$reskey]);
	}
}

?>