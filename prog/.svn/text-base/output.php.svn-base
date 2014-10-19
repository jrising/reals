<?php

/*
 * Generate a pretty HTML display of an array
 * Stolen from php.net
 */
function print_r_html($arr, $style = "display: none; margin-left: 10px;") {
  static $i = 0; $i++;
  echo "\n<div id=\"array_tree_$i\" class=\"array_tree\">\n";
  foreach($arr as $key => $val)
  { switch (gettype($val))
   { case "array":
       echo "<a onclick=\"document.getElementById('";
       echo "array_tree_element_$i').style.display = ";
       echo "document.getElementById('array_tree_element_$i";
       echo "').style.display == 'block' ?";
       echo "'none' : 'block';\"\n";
       echo "name=\"array_tree_link_$i\" href=\"#array_tree_link_$i\">".htmlspecialchars($key)."</a><br />\n";
       echo "<div class=\"array_tree_element_\" id=\"array_tree_element_$i\" style=\"$style\">";
       echo print_r_html($val);
       echo "</div>";
     break;
     case "integer":
       echo "<b>".htmlspecialchars($key)."</b> => <i>".htmlspecialchars($val)."</i><br />";
     break;
     case "double":
       echo "<b>".htmlspecialchars($key)."</b> => <i>".htmlspecialchars($val)."</i><br />";
     break;
     case "boolean":
       echo "<b>".htmlspecialchars($key)."</b> => ";
       if ($val)
       { echo "true"; }
       else
       { echo "false"; }
       echo  "<br />\n";
     break;
     case "string":
       echo "<b>".htmlspecialchars($key)."</b> => <code>".htmlspecialchars($val)."</code><br />";
     break;
     default:
       echo "<b>".htmlspecialchars($key)."</b> => ".gettype($val)."<br />";
     break; }
   echo "\n"; }
  echo "</div>\n";
}

?>