<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2018 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

#Funzioni per usare il database MYSQL

ignore_user_abort(1);

# variabili per le differenze nella sintassi delle query
#global $ILIKE,$LIKE;
$ILIKE = "LIKE";
$LIKE = "LIKE BINARY";
$DATETIME = "datetime";
$MEDIUMTEXT = "mediumtext";



function connetti_db ($database,$host,$port,$user,$password,$estensione) {

if ($estensione == "SI") dl("mysql.so");
$numconnessione = mysql_connect("$host:$port",$user,$password);
@mysql_query("SET NAMES 'utf8'");
mysql_select_db($database);

return $numconnessione;

} # fine function connetti_db



function disconnetti_db ($numconnessione) {

$risul = mysql_close($numconnessione);
return $risul;

} # fine function disconnetti_db



if (substr($PHPR_LOG,0,2) != "SI") {

function esegui_query ($query,$silenzio = "",$idlog = "") {

$risul = mysql_query($query);
if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR IN: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)." <br>".mysql_errno().": ".mysql_error()."<br>";
} # fine if (!$risul and !$silenzio)

return $risul;

} # fine function esegui_query

} # fine if (substr($PHPR_LOG,0,2) != "SI")


else {
if (!function_exists("inserisci_log")) include("./includes/funzioni_log.php");

function esegui_query ($query,$silenzio = "",$idlog = "") {

$risul = mysql_query($query);
if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR IN: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)." <br>".mysql_errno().": ".mysql_error()."<br>";
} # fine if (!$risul and !$silenzio)

if ($idlog != 1) inserisci_log($query,$idlog);

return $risul;

} # fine function esegui_query

} # fine else if (substr($PHPR_LOG,0,2) != "SI")



function risul_query ($query,$riga,$colonna,$tab="") {

$risul = mysql_result($query,$riga,$colonna);
#if (!$risul) echo "<br>Nessun risultato in riga $riga colonna $colonna<br>";

return $risul;

} # fine function risul_query



function numlin_query ($query) {

$risul = mysql_num_rows($query);
return $risul;

} # fine function numlin_query



function aggslashdb ($stringa) {

$risul = addslashes($stringa);
return $risul;

} # fine function aggslashdb



function arraylin_query ($query,$num) {

mysql_data_seek($query,$num);
$risul =  mysql_fetch_row($query);
return $risul;

} # fine function arraylin_query



function numcampi_query ($query) {

$risul = mysql_num_fields($query);
return $risul;

} # fine function numcampi_query



function nomecampo_query ($query,$num) {

$risul = mysql_field_name($query,$num);
return $risul;

} # fine function nomecampo_query



function tipocampo_query ($query,$num) {

$risul = mysql_field_type($query,$num);
return $risul;

} # fine function tipocampo_query



function dimcampo_query ($query,$num) {

$risul = mysql_field_len($query,$num);
return $risul;

} # fine function dimcampo_query



function chiudi_query (&$query) {

mysql_free_result($query);
$query = "";

} # fine function chiudi_query



function lock_tabelle ($tabelle,$altre_tab_usate = "") {

if (@is_array($tabelle)) {
for ($num1 = 0 ; $num1 < count($tabelle); $num1++) {
$lista_tabelle .= $tabelle[$num1]." write,";
} # fine for $num1
} # fine if (@is_array($tabelle))
if (@is_array($altre_tab_usate)) {
for ($num1 = 0 ; $num1 < count($altre_tab_usate); $num1++) {
$lista_tabelle .= $altre_tab_usate[$num1]." read,";
} # fine for $num1
} # fine if (@is_array($altre_tab_usate))
$lista_tabelle = substr($lista_tabelle,0,-1);
$risul = mysql_query("lock tables $lista_tabelle");
if (!$risul) echo "<br>ERROR IN: lock tables $lista_tabelle<br>".mysql_errno().": ".mysql_error()."<br>";

return $risul;

} # fine function lock_tabelle



function unlock_tabelle ($tabelle_lock,$azione = "") {

$risul = mysql_query("unlock tables");

} # fine function unlock_tabelle



function crea_indice ($tabella,$colonne,$nome) {

mysql_query("alter table $tabella add index $nome ($colonne)");

} # fine function crea_indice



?>