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

#Funzioni per usare il database POSTGRESQL

ignore_user_abort(1);

# variabili per le differenze nella sintassi delle query
#global $ILIKE,$LIKE;
$ILIKE = "ILIKE";
$LIKE = "LIKE";
$DATETIME = "timestamp";
$MEDIUMTEXT = "text";



function connetti_db ($database,$host,$port,$user,$password,$estensione) {

if ($estensione == "SI") dl("pgsql.so");
$numconnessione = pg_connect("dbname=$database host=$host port=$port user=$user password=$password ");
pg_exec("set datestyle to 'iso'");

return $numconnessione;

} # fine function connetti_db



function disconnetti_db ($numconnessione) {

$risul = pg_close($numconnessione);
return $risul;

} # fine function disconnetti_db



if (substr($PHPR_LOG,0,2) != "SI") {

function esegui_query ($query,$silenzio = "",$idlog = "") {

$risul = pg_exec($query);
if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR in: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)."<br>";
} # fine (!$risul and !$silenzio)
return $risul;

} # fine function esegui_query

} # fine if (substr($PHPR_LOG,0,2) != "SI")


else {
if (!function_exists("inserisci_log")) include("./includes/funzioni_log.php");

function esegui_query ($query,$silenzio = "",$idlog = "") {

$risul = pg_exec($query);
if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR in: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)."<br>";
} # fine (!$risul and !$silenzio)

if ($idlog != 1) inserisci_log($query,$idlog);

return $risul;

} # fine function esegui_query

} # fine else if (substr($PHPR_LOG,0,2) != "SI")



function risul_query ($query,$riga,$colonna,$tab="") {

$risul = pg_result($query,$riga,$colonna);
return $risul;

} # fine function risul_query



function numlin_query ($query) {

$risul = pg_numrows($query);
return $risul;

} # fine function numlin_query



if (function_exists('pg_escape_string')) {
function aggslashdb ($stringa) {

$risul = pg_escape_string($stringa);
return $risul;

} # fine function aggslashdb
} # fine if (function_exists('pg_escape_string'))

else {
function aggslashdb ($stringa) {

$risul = addslashes($stringa);
return $risul;

} # fine function aggslashdb
} # fine else if (function_exists('pg_escape_string'))



function arraylin_query ($query,$num) {

$risul = pg_fetch_row($query,$num);
return $risul;

} # fine function arraylin_query



function numcampi_query ($query) {

$risul = pg_numfields($query);
return $risul;

} # fine function numcampi_query



function nomecampo_query ($query,$num) {

$risul = pg_fieldname($query,$num);
return $risul;

} # fine function nomecampo_query



function tipocampo_query ($query,$num) {

$risul = pg_fieldtype($query,$num);
return $risul;

} # fine function tipocampo_query



function dimcampo_query ($query,$num) {

$risul = pg_fieldsize($query,$num);
return $risul;

} # fine function dimcampo_query



function chiudi_query (&$query) {

pg_free_result($query);
$query = "";

} # fine function chiudi_query



function lock_tabelle ($tabelle,$altre_tab_usate = "") {

if (@is_array($tabelle)) {
pg_exec("begin");
$num_tabelle = count($tabelle);
for ($num1 = 0 ; $num1 < $num_tabelle; $num1++) {
$tabella = $tabelle[$num1];
pg_exec("lock table $tabella");
} # fine for $num1
$risul = "commit";
} # fine if (@is_array($tabelle))
else $risul = "nocommit";

return $risul;

} # fine function lock_tabelle



function unlock_tabelle ($tabelle_lock,$azione = "") {

if ($tabelle_lock != "nocommit") {
if ($azione == "rollback") pg_exec("rollback");
else pg_exec("commit");
} # fine if ($tabelle_lock != "nocommit")

} # fine function unlock_tabelle



function crea_indice ($tabella,$colonne,$nome) {

pg_exec("create index $nome on $tabella ($colonne)");

} # fine function crea_indice



?>