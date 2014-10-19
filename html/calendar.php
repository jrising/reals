<?php

define('CALOPT_WIDTH100', 0x01);
define('CALOPT_BORDERCOLOR', 0x02);
define('CALOPT_LARGETOP', 0x04);
define('CALOPT_SMALLTOP', 0x08);
define('CALOPT_DAYNAMES', 0x10);
define('CALOPT_DAYLETTERS', 0x20);
define('CALOPT_HEIGHTREQ', 0x40);
define('CALOPT_FUNCNUMS', 0x80);
define('CALOPTSET_LARGECAL', 0x57);
define('CALOPTSET_SMALLCAL', 0xA8);

function calendar($dayfunc, $date, $options) {
  $daysmonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

  if ($date) {
    $current = getdate($date);
  } else {
    $date = mktime();
    $current = getdate();
  }

  // this month information
  if ($current['mon'] == 2 && $current['year'] % 4 == 0) {
    $monthlen = 29;
  } else {
    $monthlen = $daysmonth[$current['mon'] - 1];
  }

  // last month
  $lastm = $current['mon'] - 1;
  if ($lastm == 0) {
    $lastm = 12;
    $lastdate = mktime(0, 0, 0, $lastm, 10, $current['year'] - 1);
  } else {
    $lastdate = mktime(0, 0, 0, $lastm, 10, $current['year']);
  }

  // next month
  $nextm = $current['mon'] + 1;
  if ($nextm == 13) {
    $nextm = 1;
    $nextdate = mktime(0, 0, 0, $nextm, 10, $current['year'] + 1);
  } else {
    $nextdate = mktime(0, 0, 0, $nextm, 10, $current['year']);
  }

  // Create the calendar:
  $otheropts = "";
  if ($options & CALOPT_WIDTH100)
    $otheropts .= "width='100%' ";
  if ($options & CALOPT_BORDERCOLOR)
    $otheropts .= "bordercolor=${main_bordercolor}";
  $output = "<table border=1 bgcolor='#ffffff' ${otheropts}>\n";

  // Month
  if ($options & CALOPT_LARGETOP) {
    $output .= "\t<tr>\n";
    $output .= "\t\t<td colspan=7 align=center>\n";
    $output .= "\t\t\t<table border=0 cellspacing=0 cellpadding=0 width='75%'>\n";
    $output .= "\t\t\t\t<tr>\n";
    $output .= "\t\t\t\t\t<td width='33%' align=center><a href='${_SERVER[PHP_SELF]}?date=${lastdate}'>&lt; " . date("F", mktime(0, 0, 0, $lastm)) . "</a></td>\n";
    $output .= "\t\t\t\t\t<td width='33%' align=center><font size='+2'>" . date("F Y", $date) . "</font></td>\n";
    $output .= "\t\t\t\t\t<td width='33%' align=center><a href='${_SERVER[PHP_SELF]}?date=${nextdate}'>" . date("F", mktime(0, 0, 0, $nextm)) ." &gt;</a></td>\n";
    $output .= "\t\t\t\t</tr>\n";
    $output .= "\t\t\t</table>\n";
    $output .= "\t\t</td>\n";
    $output .= "\t</tr>\n";
  }
  if ($options & CALOPT_SMALLTOP) {
    $output .= "\t<tr>\n";
    $output .= "\t\t<td colspan=7 align=center>\n";
    $output .= date("F Y", mktime(0, 0, 0, $current['mon'], 10, $current['year']));
    $output .= "\t\t</td>\n";
    $output .= "\t</tr>\n";
  }

  // Days of week
  
  if ($options & CALOPT_DAYNAMES) {
    $output .= "\t<tr>\n";
    $output .= "\t\t<td width='14%' align=center>Sunday</td><td width='14%' align=center>Monday</td><td width='14%' align=center>Tuesday</td><td width='14%' align=center>Wednesday</td><td width='14%' align=center>Thursday</td><td width='14%' align=center>Friday</td><td width='14%' align=center>Saturday</td>\n";
    $output .= "\t</tr>\n";
  }
  if ($options & CALOPT_DAYLETTERS) {
    $output .= "\t<tr>\n";
    $output .= "\t\t<td width='14%' align=center>S</td><td width='14%' align=center>M</td><td width='14%' align=center>T</td><td width='14%' align=center>W</td><td width='14%' align=center>R</td><td width='14%' align=center>F</td><td width='14%' align=center>S</td>\n";
    $output .= "\t</tr>\n";
  }

  // days fill in
  $first = getdate(mktime(0, 0, 0, $current['mon'], 1, $current['year']));
  if ($options & CALOPT_HEIGHTREQ)
    $output .= "\t<tr height=70>\n";

  // fill in blank days
  for ($i = 0; $i < $first['wday']; $i++) {
    $output .= "\t\t<td>&nbsp;</td>\n";
  }

  // fill in rest of calendar
  for ($i = 1; $i <= $monthlen; $i++) {
    $thisdate = mktime(0, 0, 0, $current['mon'], $i, $current['year']);
    $thisday = getdate($thisdate);
    if ($options & CALOPT_FUNCNUMS)
      $output .= "\t\t<td valign=top>" . $dayfunc($thisday['mday'], $thisdate) . "</td>\n";
    else
      $output .= "\t\t<td valign=top>" . $thisday['mday'] . $dayfunc($thisdate) . "</td>\n";
    if ($thisday['wday'] == 6 && $i != $monthlen) {
      $output .= "\t</tr>\n";
      if ($options & CALOPT_HEIGHTREQ) {
	$output .= "\t<tr height=70>\n";
      } else {
	$output .= "\t<tr>\n";
      }
    }
  }

  // fill in end of month
  for ($i = $thisday['wday'] + 1; $i <= 6; $i++) {
    $output .= "\t\t<td>&nbsp;</td>\n";
  }

  $output .= "\t</tr>\n";
  $output .= "</table>\n";

  return $output;
}
?>
