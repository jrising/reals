<?php

require_once("dbfuncs.php");
require_once("SSEmail.php");

define('MAX_PRICE', 31);

function CheckDuplicateInsert($id, $form) {
	return false;
}

function RecordInsertResult($id, $result) {
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
			SendStatusAlertEmail("Failed to get a price from the DB.  The query was $query.",
								 "$prodid", null, "DB Error");
			return MAX_PRICE;
		}
	} else {
		// try to get the single product price
		$row = GetDBConstantQuery(dbprep("SELECT Price FROM t_ProductPricing WHERE ProductID LIKE %s AND Type = %s", $prodid, $type));
		if ($row) {
			return $row['Price'];
		} else {
			SendStatusAlertEmail("Failed to get a price from the DB.  Product ID: $prodid, Type: $type.",
								 "$prodid", null, "DB Error");
			return MAX_PRICE;
		}
	}
}

function GetDBConstantArray($table, $column, $where = "") {
	if (!empty($where)) {
		return dbgetarray("SELECT ID, $column FROM $table WHERE $where");
	} else {
		return dbgetarray("SELECT ID, $column FROM $table");
	}
}

function GetDBConstantQuery() {
	$args = func_get_args();
	$query = call_user_func_array('dbprep', $args);

	$result = dbquery($query);
	if ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		mysql_free_result($result);
		return $row;
	} else {
		return false;
	}
}

function GetDBConstantRow($table, $id) {
	$result = dbquery("SELECT * FROM $table WHERE ID = %s", $id);
	if ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		mysql_free_result($result);
		return $row;
	} else {
		return false;
	}
}

function GetDBConstantRowSet($table, $id, $where = null, $order = null) {
	if (is_null($order)) {
		$poststr = "";
	} else {
		$poststr = "ORDER BY $order";
	}		

	if (is_null($id)) {
		if (is_null($where)) {
			return dbquery("SELECT * FROM $table $poststr");
		} else {
			return dbquery("SELECT * FROM $table WHERE $where $poststr");
		}
	} else {
		if (is_null($where)) {
			return dbquery("SELECT * FROM $table WHERE ID = %s $poststr", $id);
		} else {
			return dbquery("SELECT * FROM $table WHERE ID = %s AND $where $poststr", $id);
		}
	}
}

function GetDBConstantRowFromSet($rowset) {
	if ($rowset && $row = mysql_fetch_array($rowset, MYSQL_ASSOC)) {
		return $row;
	} else {
		if ($rowset) {
			mysql_free_result($rowset);
		}
		return false;
	}
}

?>
