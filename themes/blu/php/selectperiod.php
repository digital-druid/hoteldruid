<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2012 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    any later version accepted by Marco Maria Francesco De Santis, which
#    shall act as a proxy as defined in Section 14 of version 3 of the
#    license.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
##################################################################################



global $id_select_dates_menu;
if (!$id_select_dates_menu) $id_select_dates_menu = 0;
if (!$js) $id_select_dates_menu++;
if (!$standalone_dates_menu) $events = " onChange=\\\"update_selected_dates('$id_select_dates_menu')\\\"";
elseif (!$js) $id_select_dates_menu++;



if (!$js) {
echo "<script type=\"text/javascript\">
<!--
";
$doc_write_parentesis = "document.write(";
$close_parentesis = ")";
} # fine if (!$js)
else {
echo "
if (window.id_sdm === undefined) {
id_sdm = 0;
var elem_sdm = 1;
while (elem_sdm) {
id_sdm = id_sdm + 2;
elem_sdm = document.getElementById('id_sdm'+id_sdm);
}
}
id_sdm++;
";
if (!$standalone_dates_menu) $events = " onChange=\\\"update_selected_dates(\"+id_sdm+\")\\\"";
else echo "id_sdm++;
";
$doc_write_parentesis = "$js += ";
$close_parentesis = "";
} # fine else if (!$js)

if ($second_date_selected) {
if (!strcmp(preg_replace("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/","",$second_date_selected),"")) {
if (!$js) echo "var second_date_selected$id_select_dates_menu = '$second_date_selected';
";
else echo "window['second_date_selected'+id_sdm] = '$second_date_selected';
";
} # fine if (!strcmp(preg_replace("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/","",$second_date_selected),""))
} # fine if ($second_date_selected)

if (!$js) echo "document.write(\"<select name=\\\"$name_date_var\\\" id=\\\"id_sdm$id_select_dates_menu\\\"$events>\");
";
else echo "$js += \"<select name=\\\"\"+$name_date_var+\"\\\" id=\\\"id_sdm\"+id_sdm+\"\\\"$events>\";
";
if ($show_blank_option) echo "$doc_write_parentesis\"<option value=\\\"\\\"$blank_selected>----<\/option>\"$close_parentesis;
";


if ($last_dates_menu != $file) {
$hide_default_dates = 1;
echo "giorni = new Array($d_names);
mesi = new Array($m_names);
opz = \"\";
";
$periods_num = count($d_increment);
$day_name = "";
for ($num1 = 0 ; $num1 < $periods_num ; $num1++) if ($d_increment[$num1] != 7) $day_name = "+giorni[d.getDay()]";
for ($num1 = 0 ; $num1 < $periods_num ; $num1++) {
echo "giorno = ".$d_ini_menu[$num1].";
mese = ".$m_ini_menu[$num1].";
anno = ".$y_ini_menu[$num1].";
for (n1 = 1 ; n1 <= ".$n_dates_menu[$num1]." ; n1++) {
d = new Date(anno,mese,giorno,2);
anno = d.getFullYear();
mese = d.getMonth();
giorno = d.getDate();
opz += \"<option value=\\\"\"+anno+\"-\"+agg_zero((mese + 1))+(mese + 1)+\"-\"+agg_zero(giorno)+giorno+\"\\\">\"+mesi[mese]+\" \"+agg_zero(giorno)+giorno".$day_name."+\", \"+anno+\"<\/option>\";
giorno = giorno + ".$d_increment[$num1].";
} // fine for n1
";
} # fine for $num1
} # fine if ($last_dates_menu != $file)


if ($date_selected) echo $doc_write_parentesis."opz.replace(\"$date_selected\\\">\",\"$date_selected\\\" selected>\")$close_parentesis;";
else echo $doc_write_parentesis."opz$close_parentesis;";

echo "
$doc_write_parentesis\"<\/select>\"$close_parentesis;
";

if (!$js) echo "//-->
</script><input type=\"button\" class=\"dbutton\" id=\"bcal$id_select_dates_menu\" onclick=\"mos_cal($id_select_dates_menu)\" value=\"..\">
<div id=\"cal$id_select_dates_menu\" class=\"datepick\"></div>";
else echo "$js += \"<input type=\\\"button\\\" class=\\\"dbutton\\\" id=\\\"bcal\"+id_sdm+\"\\\" onclick=\\\"mos_cal(\"+id_sdm+\")\\\" value=\\\"..\\\">\
<div id=\\\"cal\"+id_sdm+\"\\\" class=\\\"datepick\\\"><\/div>\";
";





?>