<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2009 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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



function trova_rel ($rel,&$rel_sing,&$rel_plur,&$tablerel) {
global $tablenazioni,$tableregioni,$tablecitta,$tabledocumentiid,$tableparentele;
$rel_sing = "nazione";
$rel_plur = "nazioni";
$tablerel = $tablenazioni;
if ($rel == "regione") {
$rel_sing = "regione";
$rel_plur = "regioni";
$tablerel = $tableregioni;
} # fine if ($rel == "regione")
if ($rel == "citta") {
$rel_sing = "citta";
$rel_plur = "citta";
$tablerel = $tablecitta;
} # fine if ($rel == "citta")
if ($rel == "documentoid") {
$rel_sing = "documentoid";
$rel_plur = "documentiid";
$tablerel = $tabledocumentiid;
} # fine if ($rel == "documentoid")
if ($rel == "parentela") {
$rel_sing = "parentela";
$rel_plur = "parentele";
$tablerel = $tableparentele;
} # fine if ($rel == "parentela")
} # fine function trova_rel



function mostra_frame_rel ($id,$rel,$rel_sup,$id_ut_sel,$cmp,$mostra_cod,$pieno,$titolo,$size="20",$maxlength="50") {
global $tablerelutenti,$tablenazioni,$tableregioni,$tablecitta,$tabledocumentiid,$tableparentele;

trova_rel($rel,$rel_sing,$rel_plur,$tablerel);
trova_rel($rel_sup,$rel_sup_sing,$rel_sup_plur,$tablerel_sup);

if (get_magic_quotes_gpc()) $id = stripslashes($id);
$id = htmlspecialchars($id);
$id_sup = esegui_query("select distinct $tablerel_sup.id$rel_sup_plur from $tablerelutenti inner join $tablerel_sup on $tablerelutenti.id$rel_sup_sing = $tablerel_sup.id$rel_sup_plur where $tablerelutenti.idutente = '$id_ut_sel' and $tablerel_sup.nome_$rel_sup_sing = '".aggslashdb($id)."' ");
if (numlin_query($id_sup)) $is_id = "= '".risul_query($id_sup,0,"id$rel_sup_plur",$tablerel_sup)."'";
else $is_id = "is NULL";



echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"
        \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >
<title> $titolo </title>
</head>
<body>
<div id=\"dati_rel\">";


$rel_utente = esegui_query("select distinct $tablerel.nome_$rel_sing,$tablerel.codice_$rel_sing,$tablerel.codice2_$rel_sing,$tablerel.codice3_$rel_sing from $tablerelutenti inner join $tablerel on $tablerelutenti.id$rel_sing = $tablerel.id$rel_plur where $tablerelutenti.idutente = '$id_ut_sel' and $tablerelutenti.idsup $is_id order by $tablerel.nome_$rel_sing");
$num_rel_utente = numlin_query($rel_utente);
if ($num_rel_utente) {
echo "<select name=\"$cmp\" id=\"$cmp\">";
if ($pieno != "SI") echo "<option value=\"\">------</option>";
for ($num1 = 0 ; $num1 < $num_rel_utente ; $num1++) {
$rel = htmlspecialchars(risul_query($rel_utente,$num1,"nome_$rel_sing",$tablerel));
echo "<option value=\"$rel\">$rel";
if ($mostra_cod) {
$codice = htmlspecialchars(risul_query($rel_utente,$num1,"codice_$rel_sing",$tablerel));
$codice2 = htmlspecialchars(risul_query($rel_utente,$num1,"codice2_$rel_sing",$tablerel));
$codice3 = htmlspecialchars(risul_query($rel_utente,$num1,"codice3_$rel_sing",$tablerel));
if (strcmp($codice,"")) echo " ($codice)";
if (strcmp($codice2,"")) echo " (".mex("2°",'personalizza.php')." $codice2)";
if (strcmp($codice3,"")) echo " (".mex("3°",'personalizza.php')." $codice3)";
} # fine if ($mostra_cod)
echo "</option>";
} # fine for $num1
echo "</select>";
} # fine if ($num_rel_utente)
else echo "<input type=\"text\" name=\"$cmp\" id=\"$cmp\" size=\"$size\" maxlength=\"$maxlength\">";


echo "</div>
</body>
</html>
";
} # fine function mostra_frame_rel



?>