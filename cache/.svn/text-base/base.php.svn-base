<?php

require_once(incdir("dbfuncs.php"));
require_once(incdir("SSEmail.php"));

define('MAX_PRICE', 31);
define('DAY_SECS', 86400);
define('TIMEOUT_SECS', 1800); // 30 minutes
define('TEMP_SECS', 60);

$memconn = null;
$rowlocs = array();

// table id columns, used with GetDBConstantRow;
// only need to put in if the ID column is not 'ID'
$kTableIDColumn = array('t_ShoppingCart' => 'CartID',
						't_SharingUser' => 'id');

/*
 * Retrieve a multiple-entry row result, in pieces
 */
function CacheCollection($query, $start = 0, $count = 20, $expiry = DAY_SECS) {
	$memconn = GetMemcacheConnection();
	$resrows = array();

	// check if we already have this
	if ($memconn) {
		if ($allrows = memcache_get($memconn, CacheKeyCollection($query))) {
			// do we have all these ids?
			$foundall = true;
			for ($ii = 0; $ii < $count; $ii++) {
				if (!isset($arr[$start + $ii])) {
					$foundall = false;
					break;
				} else {
					$resrows[$start + $ii] = $arr[$start + $ii];
				}
			}

			if ($foundall) {
				return $resrows;
			}
		}
	}

	if (empty($allrows)) {
		$allrows = array();
		$ii = 0;
	}

	// we need to perform the query
	$result = dbquery($query . " limit %d, %d",
					  $start + $ii, $count - $ii);
	while ($result && $row = dbfetch($result, DB_NUM)) {
		if (count($row) == 1) {
			// don't keep arrays
			$row = array_shift($row);
		}
		$allrows[$start + $ii] = $resrows[$start + $ii] = $row;
		$ii++;
	}

	// cache result
	if ($memconn) {
		memcache_set($memconn, CacheKeyCollection($query),
					 $allrows, false, $expiry);
	}

	return $resrows;
}

/*
 * Invalidate a particular cache entry (set it to expire in 1 second)
 */
function CacheInvalidate($key) {
	if ($memconn = GetMemcacheConnection()) {
		memcache_delete($memconn, $key);
	}
}

/*
 * Check and store a temporary value reflecting a user's form submission
 * returns false on unique, true or a result from RecordInsertResult on duplicate
 */
function CheckDuplicateInsert($id, $form) {
	global $_DUPFORM;

	// for testing purposes, we may want to disable this feature
	if (isset($_DUPFORM) && $_DUPFORM) {
		return false;
	}

	$myhash = crc32(serialize($form));

	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($oldhash = memcache_get($memconn, $id . '-form')) {
			if ($oldhash == $myhash) {
				if ($oldvalue = memcache_get($memconn, $id . '-value')) {
					return $oldvalue;
				} else {
					return true; // don't know value (yet)
				}
			} // we can replace
		} // nothing there

		memcache_set($memconn, $id . '-form', $myhash, false, TIMEOUT_SECS);
	}

	return false;
}

/*
 * Record a saved result from a form insert
 * Returns true on success, false on failure
 */
function RecordInsertResult($id, $result) {
	$memconn = GetMemcacheConnection();
	if ($memconn) {
		return memcache_set($memconn, $id . '-value', $result,
							false, TIMEOUT_SECS);
	}

	return false;
}

/*
 * Check if a message has occurred limit times in the last timeout seconds
 * (timeout should be consistent for a given message)
 * If limit is true (the default) then checks if message occurred once
 */
function CacheCheckMessage($id, $timeout, $limit = true) {
	$memconn = GetMemcacheConnection();
	if ($memconn) {
		$count = memcache_get($memconn, $id);
		if ($count) {
			if ($limit === true || $count > $limit) {
				return true;
			} else {
				memcache_set($memconn, $id, $count + 1, false, $timeout);
				return false;
			}
		}

		memcache_set($memconn, $id, 1, false, $timeout);
	}

	return false;
}

/*
 * Look up the price of an item (or a group of items, with prodid as an array)
 */
function GetDBProductPrice($prodid, $type) {
	if (is_array($prodid)) {
		// $prodid is an array of products to sum
		$query = dbprep("SELECT Sum(Price) as Total, Count(*) as Cnt FROM t_ProductPricing WHERE Type = %s AND (", $type);
		$ii = 0;
		foreach ($prodid as $oneprod) {
			if ($ii != 0) {
				$query .= " OR ";
			}
			$ii++;
			$query .= dbprep("ProductID LIKE %s", $oneprod);
		}
		$query .= ") GROUP BY ID ORDER BY Cnt DESC";

		$row = GetDBConstantQuery($query);
		if ($row) {
			return $row['Total'];
		} else {
			SendStatusAlertEmail("Failed to get a price from the Cache system.  The query was $query.",
								 "$prodid", null, "Memcache Error");
			return MAX_PRICE;
		}
	} else {
		// try to get the single product price
		$row = GetDBConstantQuery("SELECT Price FROM t_ProductPricing WHERE ProductID LIKE %s AND Type = %s", $prodid, $type);
		if ($row) {
			return $row['Price'];
		} else {
			SendStatusAlertEmail("Failed to get a price from the Cache system.  Product ID: $prodid, Type: $type.",
								 "$prodid", null, "Memcache Error");
			return MAX_PRICE;
		}
	}
}

/*
 * DB rows are stored in full for one day, under the key '<TABLE>:<ID>'
 */
function GetDBConstantRow($table, $id) {
	global $kTableIDColumn;

	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($row = memcache_get($memconn, CacheKeyRow($table, $id))) {
			return $row;
		}
	}

	// get all columns for a given row (based on its ID)
	$idcol = isset($kTableIDColumn[$table]) ? $kTableIDColumn[$table] : 'ID';

	$result = dbquery("SELECT * FROM $table WHERE $idcol = %s", $id);
	if ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($memconn) {
			memcache_set($memconn, CacheKeyRow($table, $id), $row,
						 false, DAY_SECS);
		}
		mysql_free_result($result);
		return $row;
	} else {
		return false;
	}
}

/*
 * Returns array of all values, stored under key '<TABLE>:<COLUMN>:<WHERE>'
 */
function GetDBConstantArray($table, $column, $where = "") {
	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($array = memcache_get($memconn, $table . ':' .
								  $column . ':' . $where)) {
			return $array;
		}
	}

	// return an array of ID => COLUMN for all rows that match WHERE
	if (!empty($where)) {
		$result = dbgetarray("SELECT ID, $column FROM $table WHERE $where");
	} else {
		$result = dbgetarray("SELECT ID, $column FROM $table");
	}
	if (!empty($result)) {
		if ($memconn) {
			memcache_set($memconn, $table . ':' . $column . ':' . $where,
						 $result, false, DAY_SECS);
		}
	}
	return $result;
}

/*
 * DB rows are stored in full for one day, under the key MD5('<QUERY>')
 */
function GetDBConstantQuery() {
	$args = func_get_args();
	$query = call_user_func_array('dbprep', $args);

	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($row = memcache_get($memconn, md5($query))) {
			return $row;
		}
	}

	// cache the single row result from an arbitrary query
	$result = dbquery($query);
	if ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($memconn) {
			memcache_set($memconn, md5($query), $row, false, DAY_SECS);
		}
		mysql_free_result($result);
		return $row;
	} else {
		return false;
	}
}

/*
 * Perform the same logic as dbgetarray
 */
function GetDBConstantQueryArray() {
	$args = func_get_args();
	$query = call_user_func_array('dbprep', $args);

	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($result = memcache_get($memconn, CacheKeyQueryArray($query))) {
			return $result;
		}
	}

	$result = dbgetarray($query);
	if ($memconn) {
		memcache_set($memconn, CacheKeyQueryArray($query),
					 $result, false, DAY_SECS);
	}
	return $result;
}

/*
 * Row sets are stored under their query string: '<QUERY>:<ROW>'
 *   If the values are stroed, <QUERY>: will be keyed to the number of rows
 * Store state in $rowlocs and return unique id
 */
function GetDBConstantRowSet($table, $id = null, $where = null, $order = null) {
	global $rowlocs;

	if (is_null($order)) {
		$poststr = "";
	} else {
		$poststr = "ORDER BY $order";
	}

	// construct the query
	if (is_null($id)) {
		if (is_null($where)) {
			$query = dbprep("SELECT * FROM $table $poststr");
		} else {
			$query = dbprep("SELECT * FROM $table WHERE $where $poststr");
		}
	} else {
		if (is_null($where)) {
			$query = dbprep("SELECT * FROM $table WHERE ID = %s $poststr", $id);
		} else {
			$query = dbprep("SELECT * FROM $table WHERE ID = %s AND $where $poststr", $id);
		}
	}

	$memconn = GetMemcacheConnection();
	if ($memconn) {
		if ($exists = memcache_get($memconn, $query . ':')) {
			// store current row in global memory
			$rowlocs[$key = uniqid(rand(), true)] = array($query, 0);
			return $key;
		}
	}

	// perform query and store mysql result variable in global memory
	$result = dbquery($query);
	$rowlocs[$key = uniqid(rand(), true)] = array($query, 0, $result);
	return $key;
}

/*
 * If this is a stored value, retrieve it.
 * Otherwise, get and store it.
 *   When all are available, cache the query to the number of rows
 */
function GetDBConstantRowFromSet($rowset) {
	global $rowlocs;

	if (!isset($rowlocs[$rowset])) {
		return false;  // fail
	}

	$keyarr = $rowlocs[$rowset];
	$memconn = GetMemcacheConnection();

	// check whether this is from previously cached data
	if (!isset($keyarr[2])) {  // already stored
		if ($memconn) {
			if ($row = memcache_get($memconn, $keyarr[0] . ':' . $keyarr[1])) {
				$rowlocs[$rowset][1]++;
				return $row;
			} else {
				unset($rowlocs[$rowset]);
				return false;  // no rows left
			}
		} else {
			unset($rowlocs[$rowset]);
			return false;  // fail
		}
	}

	// not cached: store as go along
	$result = $keyarr[2];
	if ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($memconn) {
			memcache_set($memconn, $keyarr[0] . ':' . $keyarr[1], $row,
						 false, DAY_SECS);
		}
		// next time get the next row
		$rowlocs[$rowset][1]++;
		return $row;
	} else {
		if ($result) {
			// all done!  store query in cache to signify full result cached
			if ($memconn) {
				memcache_set($memconn, $keyarr[0] . ':', $keyarr[1],
							 false, DAY_SECS);
			}
			mysql_free_result($result);
		}
		return false;
	}
}

/*
 * Get a connection to the cache
 */
function GetMemcacheConnection() {
	global $memconn;

	if ($memconn == null) {
		$memconn = memcache_connect('localhost', 11211);
		if (!$memconn) {
			SendStatusAlertEmail("Failed to connect to memcache",
								 "", null, "Memcache Error");
		}
	}

	return $memconn;
}

// Key formation functions

function CacheKeyCollection($query) {
	return md5($query) . '::';
}

function CacheKeyRow($table, $id) {
	return $table . ':' . $id;
}

function CacheKeyQueryArray($query) {
	return md5($query) . '{}';
}

?>
