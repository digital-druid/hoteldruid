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

#Funzioni per usare il database SQLITE

ignore_user_abort(1);

# variabili per le differenze nella sintassi delle query
#global $ILIKE,$LIKE;
$ILIKE = "LIKE";
$LIKE = "GLOB";
$DATETIME = "text";
$MEDIUMTEXT = "text";



function connetti_db ($database,$host,$port,$user,$password,$estensione) {

if ($estensione == "SI") dl("sqlite3.so");
if (defined("C_PERCORSO_A_DATI")) $numconnessione = new SQLite3(C_PERCORSO_A_DATI."db_".$database);
else $numconnessione = new SQLite3(C_DATI_PATH."/db_".$database);
return $numconnessione;

} # fine function connetti_db



function disconnetti_db ($numconnessione) {

$risul = $numconnessione->close();
return $risul;

} # fine function disconnetti_db



function prepara_query_sqlite (&$query) {

if (str_replace(" GLOB '","",$query) != $query) {
$query .= " ";
$q_vett = explode(" GLOB '",$query);
for ($n = 1 ; $n < count($q_vett) ; $n++) {
if (substr(str_replace("''","",$q_vett[$n]),0,1) != "'") {
$arg = str_replace("''","^'^",$q_vett[$n]);
$arg = explode("' ",$arg);
$arg = str_replace("^'^","''",$arg[0]);
if (str_replace("''","",$arg) == str_replace("'","",str_replace("''","",$arg))) {
$query = str_replace(" GLOB '$arg' "," GLOB '".str_replace("%","*",str_replace("_","?",$arg))."' ",$query);
} # fine if (str_replace("''","",$arg) == str_replace("'","",str_replace("''","",$arg)))
} # fine if (substr(str_replace("''","",$q_vett[$n]),0,1) != "'")
} # fine for $n
} # fine if (str_replace(" GLOB '","",$query) != $query)

} # fine function prepara_query_sqlite



function esegui_query_reale ($query,$silenzio = "") {
global $numconnessione;
prepara_query_sqlite($query);

$risul = $numconnessione->query($query);
if ($risul) {
$risultato = array();
$num1 = 0;
if (strtolower(substr(trim($query),0,6)) == "select" and is_object($risul)) {
while ($risultato[$num1] = $risul->fetchArray(SQLITE3_ASSOC)) $num1++;
$risultato['numcol'] = $risul->numColumns();
for ($num2 = 0 ; $num2 < $risultato['numcol'] ; $num2++) $risultato['col'][$num2] = $risul->columnName($num2);
$risul->finalize();
} # fine if (strtolower(substr(trim($query),0,6)) == "select" and is_object($risul))
$risultato['num'] = $num1;
} # fine if ($risul)
else $risultato = $risul;

if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR in: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)."<br>";
} # fine (!$risul and !$silenzio)

return $risultato;

} # fine function esegui_query_reale



if (substr($PHPR_LOG,0,2) != "SI") {

function esegui_query ($query,$silenzio = "",$idlog = "") {
$risul = esegui_query_reale($query,$silenzio);
return $risul;
} # fine function esegui_query

} # fine if (substr($PHPR_LOG,0,2) != "SI")


else {
if (!function_exists("inserisci_log")) include("./includes/funzioni_log.php");

function esegui_query ($query,$silenzio = "",$idlog = "") {
$risul = esegui_query_reale($query,$silenzio);

if ($idlog != 1) inserisci_log($query,$idlog);

return $risul;

} # fine function esegui_query

} # fine else if (substr($PHPR_LOG,0,2) != "SI")



function risul_query ($query,$riga,$colonna,$tab="") {

#if ($tab) $colonna = "$tab.$colonna";
if (is_integer($colonna)) $colonna = $query['col'][$colonna];
$risul = $query[$riga][$colonna];

return $risul;

} # fine function risul_query



function numlin_query ($query) {

return $query['num'];

} # fine function numlin_query



function aggslashdb ($stringa) {
global $numconnessione;

$risul = $numconnessione->escapeString($stringa);
return $risul;

} # fine function aggslashdb



function arraylin_query ($query,$num) {

for ($num1 = 0 ; $num1 < $query['numcol'] ; $num1++) $risul[$num1] = $query[$num][$query['col'][$num1]];
return $risul;

} # fine function arraylin_query



function numcampi_query ($query) {

return $query['numcol'];

} # fine function numcampi_query



function nomecampo_query ($query,$num) {

return $query['col'][$num];

} # fine function nomecampo_query



function tipocampo_query ($query,$num) {

$risul = "unknown";
return $risul;

} # fine function tipocampo_query



function dimcampo_query ($query,$num) {

$risul = "unknown";
return $risul;

} # fine function dimcampo_query



function chiudi_query (&$query) {

$query = array();

} # fine function chiudi_query



function lock_tabelle ($tabelle,$altre_tab_usate = "") {
global $numconnessione;

$risul = $numconnessione->exec("begin transaction");
return $risul;

} # fine function lock_tabelle



function unlock_tabelle ($tabelle_lock,$azione = "") {
global $numconnessione;

$numconnessione->exec("commit transaction");

} # fine function unlock_tabelle



function crea_indice ($tabella,$colonne,$nome) {
global $numconnessione;

$numconnessione->exec("create index $nome on $tabella ($colonne)");

} # fine function crea_indice



?>