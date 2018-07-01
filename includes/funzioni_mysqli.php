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

#Funzioni per usare il database MYSQL con estensioni mysqli

ignore_user_abort(1);

# variabili per le differenze nella sintassi delle query
#global $ILIKE,$LIKE;
$ILIKE = "LIKE";
$LIKE = "LIKE BINARY";
$DATETIME = "datetime";
$MEDIUMTEXT = "mediumtext";
unset($link_mysqli);



function connetti_db ($database,$host,$port,$user,$password,$estensione) {

global $link_mysqli;
if ($estensione == "SI") dl("mysqli.so");
$link_mysqli = mysqli_connect($host,$user,$password,$database,$port);
@mysqli_query($link_mysqli,"SET NAMES 'utf8'");

return $link_mysqli;

} # fine function connetti_db



function disconnetti_db (&$numconnessione) {

$risul = mysqli_close($numconnessione);
return $risul;

} # fine function disconnetti_db



if (substr($PHPR_LOG,0,2) != "SI") {

function esegui_query ($query,$silenzio = "",$idlog = "") {

global $link_mysqli;
$risul[-1] = mysqli_query($link_mysqli,$query);
$risul[-2] = -1;
if (!$risul[-1] and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR IN: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)." <br>".mysqli_errno($link_mysqli).": ".mysqli_error($link_mysqli)."<br>";
} # fine if (!$risul and !$silenzio)

return $risul;

} # fine function esegui_query

} # fine if (substr($PHPR_LOG,0,2) != "SI")


else {
if (!function_exists("inserisci_log")) include("./includes/funzioni_log.php");

function esegui_query ($query,$silenzio = "",$idlog = "") {

global $link_mysqli;
$risul[-1] = mysqli_query($link_mysqli,$query);
$risul[-2] = -3;
if (!$risul[-1] and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR IN: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)." <br>".mysqli_errno($link_mysqli).": ".mysqli_error($link_mysqli)."<br>";
} # fine if (!$risul and !$silenzio)

if ($idlog != 1) inserisci_log($query,$idlog);

return $risul;

} # fine function esegui_query

} # fine else if (substr($PHPR_LOG,0,2) != "SI")



function risul_query (&$query,$riga,$colonna,$tab="") {

/*
# version that uses too much memory ($query stores each line)
if (!$query[$riga]) {
if ($query[-2] != ($riga - 1)) mysqli_data_seek($query[-1],$riga);
$query[$riga] = mysqli_fetch_array($query[-1]);
$query[-2] = $riga;
#if (!$risul) echo "<br>Nessun risultato in riga $riga colonna $colonna<br>";
} # fine if (!$query[$riga])

return $query[$riga][$colonna];
*/

if ($query[-2] != $riga) {
if ($query[-2] != ($riga - 1)) mysqli_data_seek($query[-1],$riga);
$query[1] = mysqli_fetch_array($query[-1]);
$query[-2] = $riga;
#if (!$risul) echo "<br>Nessun risultato in riga $riga colonna $colonna<br>";
} # fine if ($query[-2] != $riga)

return $query[1][$colonna];

} # fine function risul_query



function numlin_query ($query) {

$risul = mysqli_num_rows($query[-1]);
return $risul;

} # fine function numlin_query


function aggslashdb ($stringa) {

$risul = addslashes($stringa);
return $risul;

} # fine function aggslashdb



function arraylin_query (&$query,$num) {

if ($query[-2] != ($num - 1)) mysqli_data_seek($query[-1],$num);
$risul =  mysqli_fetch_row($query[-1]);
$query[-2] = $num;
return $risul;

} # fine function arraylin_query



function numcampi_query ($query) {

$risul = mysqli_num_fields($query[-1]);
return $risul;

} # fine function numcampi_query



function nomecampo_query ($query,$num) {

$risul = mysqli_fetch_field_direct($query[-1],$num);
return $risul->name;

} # fine function nomecampo_query



function tipocampo_query ($query,$num) {

$risul = mysqli_fetch_field_direct($query[-1],$num);
return $risul->type;

} # fine function tipocampo_query



function dimcampo_query ($query,$num) {

$risul = mysqli_fetch_field_direct($query[-1],$num);
return $risul->max_length;

} # fine function dimcampo_query



function chiudi_query (&$query) {

mysqli_free_result($query[-1]);
$query = array();

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
global $link_mysqli;
$risul = mysqli_query($link_mysqli,"lock tables $lista_tabelle");
if (!$risul) echo "<br>ERROR IN: lock tables $lista_tabelle<br>".mysqli_errno($link_mysqli).": ".mysqli_error($link_mysqli)."<br>";

return $risul;

} # fine function lock_tabelle


function unlock_tabelle ($tabelle_lock,$azione = "") {

global $link_mysqli;
$risul = mysqli_query($link_mysqli,"unlock tables");

} # fine function unlock_tabelle



function crea_indice ($tabella,$colonne,$nome) {

global $link_mysqli;
mysqli_query($link_mysqli,"alter table $tabella add index $nome ($colonne)");

} # fine function crea_indice



?>