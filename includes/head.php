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



$t1color = "#b9ccd4";
$t1border = "3";
$t1cellspacing = "2";
$t1cellpadding = "3";
$t2row1color = "#ffffff";
$t2row2color = "#f7f7f7";
$t1dates = "#daedff";
$t1datesout = "#b7dcff";
$t1seldate = "#ffffff";
$t1dropin = "#05e105";
$t1dropout = "#297929";


if ($pag == "punto_vendita.php") $iscale = "0.8";
else $iscale = "1.0";
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") $titolo = "";
if (defined('C_FILE_TITOLO_PERS') and C_FILE_TITOLO_PERS != "" and @is_file(C_FILE_TITOLO_PERS)) $titolo = trim(substr(implode("",file(C_FILE_TITOLO_PERS)),0,40))." - $titolo";
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"
        \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >
<meta name=\"viewport\" content=\"initial-scale=$iscale\">
<title> $titolo </title>";
if ($pag == "visualizza_contratto.php" and $extra_head) echo $extra_head;
if (defined('C_URL_FAVICON') and C_URL_FAVICON != "" and @is_file(C_URL_FAVICON)) echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"".C_URL_FAVICON."\">
";
elseif (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI") echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"./img/favicon.ico\">
";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./base.css$vers_hinc\">
";
if ($base_js) echo "<script type=\"text/javascript\" src=\"./base.js$vers_hinc\">
</script>
";
if (defined('C_FILE_CSS_PERS') and C_FILE_CSS_PERS != "" and @is_file(C_FILE_CSS_PERS)) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".C_FILE_CSS_PERS."$vers_hinc\" media=\"all\">
";
if ($mobile_device and defined('C_FILE_MOB_CSS_PERS') and C_FILE_MOB_CSS_PERS != "" and @is_file(C_FILE_MOB_CSS_PERS)) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".C_FILE_MOB_CSS_PERS."\" media=\"all\">
";
echo "</head>
<body";
if ($drag_drop) echo " ondragover=\"event.preventDefault();\" ondragenter=\"event.preventDefault();\" ondrop=\"event.preventDefault();drp_out();\"";
echo " style=\"background-color: #ffffff;\">
<div>
";




?>