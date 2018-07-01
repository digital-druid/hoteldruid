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

#Funzioni per effettuare il backup


# Workaround for Ubuntu Bug #1315888
if (!function_exists('gzopen') and function_exists('gzopen64')) {
function gzopen ($arg1,$arg2) { return gzopen64($arg1,$arg2); }
if (!function_exists('gzread') and function_exists('gzread64')) { function gzread (&$arg1,$arg2) { return gzread64($arg1,$arg2); } }
if (!function_exists('gzwrite') and function_exists('gzwrite64')) { function gzwrite (&$arg1,$arg2) { return gzwrite64($arg1,$arg2); } }
if (!function_exists('gzclose') and function_exists('gzclose64')) { function gzclose (&$arg1) { return gzclose64($arg1); } }
} # fine if (!function_exists('gzopen') and function_exists('gzopen64'))



function connetti_db_per_backup ($PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,&$ext_pgsql_caricata,&$ext_mysql_caricata) {
global $ext_sqlite_caricata;

if ($PHPR_DB_TYPE == "postgresql") {
if ($PHPR_LOAD_EXT == "SI") {
dl("pgsql.so");
$ext_pgsql_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI")
$numconnessione = pg_connect("dbname=$PHPR_DB_NAME host=$PHPR_DB_HOST port=$PHPR_DB_PORT user=$PHPR_DB_USER password=$PHPR_DB_PASS ");
pg_exec("set datestyle to 'iso'");
} # fine if ($PHPR_DB_TYPE == "postgresql")
if ($PHPR_DB_TYPE == "mysql") {
if ($PHPR_LOAD_EXT == "SI") {
dl("mysql.so");
$ext_mysql_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI")
$numconnessione = mysql_connect("$PHPR_DB_HOST:$PHPR_DB_PORT", "$PHPR_DB_USER", "$PHPR_DB_PASS");
@mysql_query("SET NAMES 'utf8'");
@mysql_query("SET default_storage_engine=MYISAM");
mysql_select_db($PHPR_DB_NAME);
} # fine if ($PHPR_DB_TYPE == "mysql")
if ($PHPR_DB_TYPE == "mysqli") {
if ($PHPR_LOAD_EXT == "SI") {
dl("mysqli.so");
$ext_mysql_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI")
global $link_mysqli;
$numconnessione = mysqli_connect($PHPR_DB_HOST,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_DB_NAME,$PHPR_DB_PORT);
$link_mysqli = $numconnessione;
@mysqli_query($numconnessione,"SET NAMES 'utf8'");
@mysqli_query($numconnessione,"SET default_storage_engine=MYISAM");
} # fine if ($PHPR_DB_TYPE == "mysqli")
if ($PHPR_DB_TYPE == "sqlite") {
if ($PHPR_LOAD_EXT == "SI") {
dl("sqlite.so");
$ext_sqlite_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI")
$numconnessione = new SQLite3(C_DATI_PATH."/db_".$PHPR_DB_NAME);
} # fine if ($PHPR_DB_TYPE == "sqlite")

return $numconnessione;

} # fine function connetti_db_per_backup


# Function per passare una tabella nel backup
function dump_tabella ($tabella,&$file,$cond="",$contr="") {

# Keep alive
http_keep_alive();


global $PHPR_TAB_PRE,$id_utente;
fwrite($file,"<tabella>
<nometabella>$tabella</nometabella>
<colonnetabella>
");
if ($tabella == "transazioni") $contenuto_tab = esegui_query_unbuffered("select * from $PHPR_TAB_PRE$tabella where tipo_transazione != 'err_l' ");
else {
if ($tabella == "contratti") $contenuto_tab = esegui_query_unbuffered("select * from $PHPR_TAB_PRE$tabella order by tipo desc, numero ");
else $contenuto_tab = esegui_query_unbuffered("select * from $PHPR_TAB_PRE$tabella $cond ");
} # fine else if ($tabella == "transazioni")
$num_colonne = numcampi_query($contenuto_tab);
for ($num1 = 0 ; $num1 < $num_colonne ; $num1++) {
$nome_colonna = nomecampo_query($contenuto_tab,$num1);
if ($num1 == 0) $nome_colonna0 = $nome_colonna;
$tipo_colonna = tipocampo_query($contenuto_tab,$num1);
#$dim_colonna = dimcampo_query($contenuto_tab,$num1);
fwrite($file,"<nomecolonna>$nome_colonna</nomecolonna>
<tipocolonna>$tipo_colonna</tipocolonna>
");
#fwrite($file,"<nomecolonna>$nome_colonna</nomecolonna>
#<tipocolonna>$tipo_colonna</tipocolonna>
#<dimcolonna>$dim_colonna</dimcolonna>
#");
} # fine for $num1
fwrite($file,"</colonnetabella>
<righetabella>
");

$num1 = 0;
$num_righe = numlin_query_unbuffered($contenuto_tab);
for ($num1 = 0 ; $num1 < $num_righe ; $num1++) {
#while ($riga = arraylin_query_unbuffered($contenuto_tab,$num1)) {
#$num1++;
# Keep alive
if (substr($num1,-4) == "0000") http_keep_alive("&nbsp;");
$riga_corr = "";
$riga = array();
for ($num2 = 0 ; $num2 < $num_colonne ; $num2++) {
$riga[$num2] = risul_query_unbuffered($contenuto_tab,$num1,$num2);
$riga[$num2] = str_replace("@%&@","@%&@@%&@",$riga[$num2]);
$riga[$num2] = str_replace("</cmp>","</cmp@%&@>",$riga[$num2]);
if ($id_utente == "b" and $tabella == "utenti") {
if ($num2 == 1 and $riga[0] == "1") $riga[$num2] = "u".$riga[0];
if ($num2 == 2 and $riga[0] == "1") $riga[$num2] = "u".$riga[0];
if ($num2 == 2 and $riga[0] != "1") $riga[$num2] = "";
if ($num2 == 4 and $riga[0] == "1") $riga[$num2] = "t";
if ($num2 == 4 and $riga[0] != "1") $riga[$num2] = "n";
} # fine if ($id_utente == "b" and $tabella == "utenti")
if ($contr and $tabella == "contratti" and $num2 == 2 and $riga[1] == "api") {
$var_tmp = explode(";",$riga[$num2]);
$riga[$num2] = substr($riga[$num2],0,(strlen($var_tmp[(count($var_tmp) - 1)]) * -1));
} # fine if ($contr and $tabella == "contratti" and $num2 == 2 and $riga[1] == "api")
$riga_corr .= "<cmp>".$riga[$num2]."</cmp>";
} # fine for $num2
$riga_corr = str_replace("</righetabella>","</righetabella@%&@>",$riga_corr);
$riga_corr = str_replace("</riga>","</riga@%&@>",$riga_corr);
$riga_corr = str_replace("<riga>","<riga@%&@>",$riga_corr);
fwrite($file,"<riga>$riga_corr</riga>
");
fflush($file);
} # fine while ($riga = arraylin_query_unbuffered($contenuto_tab,$num1))
fwrite($file,"</righetabella>
</tabella>
");
chiudi_query_unbuffered($contenuto_tab);
fflush($file);

} # fine function dump_tabella


# Function per passare un file nel backup
function dump_testo ($file_dump,&$file) {

fwrite($file,"<file>
<nomefile>./dati$file_dump</nomefile>
<contenuto>
");
$linee_file = file(C_DATI_PATH.$file_dump);
$num_linee_file = count($linee_file);
for ($num1 = 0 ; $num1 < $num_linee_file ; $num1++) {
fwrite($file,$linee_file[$num1]);
} # fine for $num1
fwrite($file,"</contenuto>
</file>
");
unset($linee_file);
fflush($file);

} # fine function dump_testo


# Function per passare un modello nel backup
function dump_modello ($file_dump,$dir_dump,&$file) {
global $PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE;
$pag = "crea_modelli.php";

$limite1 = "
############################################################################
###        ".mex("NON MODIFICARE NIENTE A PARTIRE DA QUI",$pag)."
############################################################################
";
$linee_file = implode("",file("$dir_dump/$file_dump"));
$linee_file = str_replace("</contenuto>","&lt;/contenuto&gt;",$linee_file);
$linee_file = str_replace("<database>","&lt;database&gt;",$linee_file);
$linee_file = str_replace("<file>","&lt;file&gt;",$linee_file);
$linee_file = explode($limite1,$linee_file);
if (count($linee_file) == 2) {
$prima_parte = $linee_file[0];
$dati_db = "
\$".mex("var_tipo_db",$pag)." = \"$PHPR_DB_TYPE\";
\$".mex("var_nome_db",$pag)." = \"$PHPR_DB_NAME\";
\$".mex("var_computer_db",$pag)." = \"$PHPR_DB_HOST\";
\$".mex("var_porta_db",$pag)." = \"$PHPR_DB_PORT\";
\$".mex("var_utente_db",$pag)." = \"$PHPR_DB_USER\";
\$".mex("var_password_db",$pag)." = \"$PHPR_DB_PASS\";
\$".mex("var_carica_estensione_db",$pag)." = \"".mex("$PHPR_LOAD_EXT",$pag)."\";
\$".mex("var_prefisso_tabelle_db",$pag)." = \"$PHPR_TAB_PRE\";";
if (str_replace($dati_db,"",$prima_parte) != $prima_parte) {
$prima_parte = str_replace($dati_db,"",$prima_parte);
$limite2 = "
<!-- ".mex("INIZIO DELLA SECONDA PARTE DELL'HTML PERSONALE",$pag)."  -->
";
$seconda_parte = explode($limite2,$linee_file[1]);
if (count($seconda_parte) == 2) {
$seconda_parte = $seconda_parte[1];
$prima_parte = str_replace("</contenuto>","&lt;/contenuto&gt;",$prima_parte);
$prima_parte = str_replace("<database>","&lt;database&gt;",$prima_parte);
$prima_parte = str_replace("<file>","&lt;file&gt;",$prima_parte);
$seconda_parte = str_replace("</contenuto>","&lt;/contenuto&gt;",$seconda_parte);
$seconda_parte = str_replace("<database>","&lt;database&gt;",$seconda_parte);
$seconda_parte = str_replace("<file>","&lt;file&gt;",$seconda_parte);

fwrite($file,"<modello>
<dirmodello>$dir_dump</dirmodello>
<nomemodello>$file_dump</nomemodello>
<contenuto>
");
fwrite($file,$prima_parte.$limite1);
fwrite($file,$limite2.$seconda_parte);
fwrite($file,"</contenuto>
</modello>
");

} # fine if (count($seconda_parte) == 2)
} # fine if (str_replace($dati_db,"",$prima_parte) != $prima_parte)
} # fine if (count($linee_file) == 2)

} # fine function dump_modello


function esegui_query2 ($query,$tipo_db,$silenzio = "") {
if ($tipo_db == "postgresql") $risul = pg_exec($query);
if ($tipo_db == "mysql") $risul = mysql_query($query);
if ($tipo_db == "mysqli") {
global $numconnessione;
$risul = mysqli_query($numconnessione,$query);
} # fine if ($tipo_db == "mysqli")
if ($tipo_db == "sqlite") {
global $numconnessione;
$risul = $numconnessione->query($query);
if (is_object($risul)) $risul->finalize();
} # fine if ($tipo_db == "sqlite")
if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR in: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)."<br>";
} # fine (!$risul and !$silenzio)
return $risul;
} # fine function esegui_query2


function esegui_query3 (&$query,$tipo_db,$silenzio = "") {
if ($tipo_db == "postgresql") $risul = pg_exec($query);
if ($tipo_db == "mysql") $risul = mysql_query($query);
if ($tipo_db == "mysqli") {
global $numconnessione;
$risul = mysqli_query($numconnessione,$query);
} # fine if ($tipo_db == "mysqli")
if ($tipo_db == "sqlite") {
global $numconnessione;
$risul = $numconnessione->query($query);
if (is_object($risul)) $risul->finalize();
} # fine if ($tipo_db == "sqlite")
if (!$risul and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR in: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)."<br>";
} # fine (!$risul and !$silenzio)
return $risul;
} # fine function esegui_query3


function crea_indice2 ($tabella,$colonne,$nome,$tipo_db) {
if ($tipo_db == "postgresql") pg_exec("create index $nome on $tabella ($colonne)");
if ($tipo_db == "mysql") mysql_query("alter table $tabella add index $nome ($colonne)");
if ($tipo_db == "mysqli") {
global $numconnessione;
$risul = mysqli_query($numconnessione,"alter table $tabella add index $nome ($colonne)");
} # fine if ($tipo_db == "mysqli")
if ($tipo_db == "sqlite") {
global $numconnessione;
$risul = $numconnessione->query("create index $nome on $tabella ($colonne)");
if (is_object($risul)) $risul->finalize();
} # fine if ($tipo_db == "sqlite")
} # fine function crea_indice2


function aggslashdb2 (&$stringa,$tipo_db) {
if ($tipo_db == "postgresql") {
if (function_exists('pg_escape_string')) $stringa = pg_escape_string($stringa);
else $stringa = addslashes($stringa);
} # fine if ($tipo_db == "postgresql")
if ($tipo_db == "mysql") $stringa = addslashes($stringa);
if ($tipo_db == "mysqli") $stringa = addslashes($stringa);
if ($tipo_db == "sqlite") {
global $numconnessione;
$stringa = $numconnessione->escapeString($stringa);
} # fine if ($tipo_db == "sqlite")
} # fine function aggslashdb2


function ripristina_nome_var_cond ($lista_cmp,&$cond_vecchia,$nuovo_nome_var) {
reset($lista_cmp);
foreach ($lista_cmp as $key => $num_cmp) {
if ($nuovo_nome_var[$cond_vecchia[$num_cmp]]) $cond_vecchia[$num_cmp] = $nuovo_nome_var[$cond_vecchia[$num_cmp]];
} # fine foreach ($lista_cmp as $key => $num_cmp)
} # fine function ripristina_nome_var_cond



function ripristina_backup ($file,$silenzio,$pag,&$numconnessione,$database_esistente,$tempdatabase,$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata,$mantieni_anni="") {

$tableanni = $PHPR_TAB_PRE."anni";
$tableutenti = $PHPR_TAB_PRE."utenti";
include("./includes/variabili_contratto.php");
unset($var_predef);
allunga_tempo_limite();
global $LIKE,$MEDIUMTEXT,$ext_sqlite_caricata;
if (function_exists('ini_set')) @ini_set('opcache.enable',0);
$fatto = "NO";

if (defined('C_CREA_NUOVI_APP') and C_CREA_NUOVI_APP == "NO") {
$num_appartamenti_prec = esegui_query("select idappartamenti from ".$PHPR_TAB_PRE."appartamenti");
$num_appartamenti_prec = numlin_query($num_appartamenti_prec);
} # fine if (defined('C_CREA_NUOVI_APP') and C_CREA_NUOVI_APP == "NO")
elseif (defined("C_MASSIMO_NUM_APP") and C_MASSIMO_NUM_APP != 0) $num_appartamenti_prec = C_MASSIMO_NUM_APP;

if (defined('C_CAMBIA_TIPO_PERIODI') and C_CAMBIA_TIPO_PERIODI == "NO") {
unset($tipo_periodi_prec);
$anni = esegui_query("select * from $tableanni order by idanni");
for ($num1 = 0 ; $num1 < numlin_query($anni) ; $num1++) {
$ultimo_anno_prec = risul_query($anni,$num1,'idanni');
$tipo_periodi_prec[$ultimo_anno_prec] = risul_query($anni,$num1,'tipo_periodi');
} # fine for $num1
} # fine if (defined('C_CAMBIA_TIPO_PERIODI') and C_CAMBIA_TIPO_PERIODI == "NO")


# Prima lettura di controllo del file
$file_coerente = "SI";
$num_err = "";

$ultima_linea = "";
$fbackup = fopen($file,"r");
if ($fbackup) {
$leggendo_righe = "NO";
while (!feof($fbackup)) {
unset($linee_backup);
$linee_backup = fread($fbackup,524288);
$linee_backup = explode("\n",$linee_backup);
$num_linee_backup = (count($linee_backup) - 1);
$linee_backup[0] = $ultima_linea.$linee_backup[0];
if (!feof($fbackup)) $ultima_linea = $linee_backup[$num_linee_backup];
else $num_linee_backup++;
for ($num1 = 0 ; $num1 < $num_linee_backup ; $num1++) {

$linea = togli_acapo($linee_backup[$num1]);
if ($linea == "</backup>" and $leggendo_righe != "SI") $leggendo_backup = "NO";
if ($leggendo_backup == "SI") {
if (substr($linea,0,5) == "<log>" and substr($linea,-6) == "</log>") $phpr_log = substr($linea,5,-6);
if ($linea == "</database>" and $leggendo_righe != "SI") $leggendo_database = "NO";
if ($leggendo_database == "SI") {
if ($linea == "</tabella>" and $leggendo_righe != "SI") $leggendo_tabella = "NO";
if ($leggendo_tabella == "SI") {
if (substr($linea,0,13) == "<nometabella>") {
$nome_tabella = substr($linea,13);
$nome_tabella = substr($nome_tabella,0,-14);
} # fine if (substr($linea,0,13) == "<nometabella>")

if ($linea == "</colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne == "NO";
$anno_ins = "";
if ($nome_tabella == "clienti") $tab_presente['clienti'] = "SI";
if ($nome_tabella == "relclienti") $tab_presente['relclienti'] = "SI";
if ($nome_tabella == "anni") $tab_presente['anni'] = "SI";
if ($nome_tabella == "versioni") $tab_presente['versioni'] = "SI";
if ($nome_tabella == "nazioni") $tab_presente['nazioni'] = "SI";
if ($nome_tabella == "regioni") $tab_presente['regioni'] = "SI";
if ($nome_tabella == "citta") $tab_presente['citta'] = "SI";
if ($nome_tabella == "documentiid") $tab_presente['documentiid'] = "SI";
if ($nome_tabella == "parentele") $tab_presente['parentele'] = "SI";
if ($nome_tabella == "personalizza") $tab_presente['personalizza'] = "SI";
if ($nome_tabella == "appartamenti") $tab_presente['appartamenti'] = "SI";
if ($nome_tabella == "utenti") $tab_presente['utenti'] = "SI";
if ($nome_tabella == "gruppi") $tab_presente['gruppi'] = "SI";
if ($nome_tabella == "privilegi") $tab_presente['privilegi'] = "SI";
if ($nome_tabella == "relutenti") $tab_presente['relutenti'] = "SI";
if ($nome_tabella == "relgruppi") $tab_presente['relgruppi'] = "SI";
if ($nome_tabella == "sessioni") $tab_presente['sessioni'] = "SI";
if ($nome_tabella == "transazioni") $tab_presente['transazioni'] = "SI";
if ($nome_tabella == "transazioniweb") $tab_presente['transazioniweb'] = "SI";
if ($nome_tabella == "descrizioni") $tab_presente['descrizioni'] = "SI";
if ($nome_tabella == "beniinventario") $tab_presente['beniinventario'] = "SI";
if ($nome_tabella == "magazzini") $tab_presente['magazzini'] = "SI";
if ($nome_tabella == "relinventario") $tab_presente['relinventario'] = "SI";
if ($nome_tabella == "casse") $tab_presente['casse'] = "SI";
if ($nome_tabella == "contratti") $tab_presente['contratti'] = "SI";
if ($nome_tabella == "interconnessioni") $tab_presente['interconnessioni'] = "SI";
if ($nome_tabella == "messaggi") $tab_presente['messaggi'] = "SI";
if (substr($nome_tabella,0,7) == "prenota") { $anno_ins = substr($nome_tabella,7); $tab_presente[$anno_ins]['prenota'] = "SI"; }
if (substr($nome_tabella,0,12) == "costiprenota") { $anno_ins = substr($nome_tabella,12); $tab_presente[$anno_ins]['costiprenota'] = "SI"; }
else if (substr($nome_tabella,0,5) == "costi") { $anno_ins = substr($nome_tabella,5); $tab_presente[$anno_ins]['costi'] = "SI"; }
if (substr($nome_tabella,0,15) == "rclientiprenota") { $anno_ins = substr($nome_tabella,15); $tab_presente[$anno_ins]['rclientiprenota'] = "SI"; }
if (substr($nome_tabella,0,6) == "regole") { $anno_ins = substr($nome_tabella,6); $tab_presente[$anno_ins]['regole'] = "SI"; }
if (substr($nome_tabella,0,5) == "soldi") { $anno_ins = substr($nome_tabella,5); $tab_presente[$anno_ins]['soldi'] = "SI"; }
if (substr($nome_tabella,0,7) == "periodi") { $anno_ins = substr($nome_tabella,7); $tab_presente[$anno_ins]['periodi'] = "SI"; }
if (substr($nome_tabella,0,8) == "ntariffe") { $anno_ins = substr($nome_tabella,8); $tab_presente[$anno_ins]['ntariffe'] = "SI"; }
if ($anno_ins and controlla_anno($anno_ins) == "NO") { $file_coerente = "NO"; $num_err .= "#1"; }
if ($anno_ins and $tab_anno_esistente[$anno_ins] != "SI") $tab_anno_esistente[$anno_ins] = "SI";
} # fine if ($linea == "</colonnetabella>" and $leggendo_righe != "SI")
if ($leggendo_colonne == "SI") {
if (substr($linea,0,13) == "<nomecolonna>") {
$num_colonne++;
$nome_colonna[$num_colonne] = substr($linea,13);
$nome_colonna[$num_colonne] = substr($nome_colonna[$num_colonne],0,-14);
$num_colonna[$nome_colonna[$num_colonne]] = $num_colonne - 1;
if (substr($nome_tabella,0,7) == "periodi" and substr($nome_colonna[$num_colonne],0,7) == "tariffa") $lista_tariffe++;
if (substr($nome_tabella,0,8) == "ntariffe" and substr($nome_colonna[$num_colonne],0,7) == "tariffa") $lista_tariffe++;
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $lista_tariffe > (max(C_MASSIMO_NUM_TARIFFE,10) * 3)) { $file_coerente = "NO"; $num_err .= "#2"; }
} # fine if (substr($linea,0,13) == "<nomecolonna>")
if (substr($linea,0,13) == "<tipocolonna>") {
$tipo_colonna[$num_colonne] = substr($linea,13);
$tipo_colonna[$num_colonne] = substr($tipo_colonna[$num_colonne],0,-14);
} # fine if (substr($linea,0,13) == "<tipocolonna>")
} # fine if ($leggendo_colonne == "SI")
if ($linea == "<colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne = "SI";
$num_colonne = 0;
$lista_tariffe = 0;
} # fine if ($linea == "<colonnetabella>" and $leggendo_righe != "SI")

if ($linea == "</righetabella>") $leggendo_righe = "NO";
if ($leggendo_righe == "SI") {
if (substr($linea,0,6) == "<riga>") $riga = substr($linea,11);
else {
$riga .= "
".$linea;
} # fine else if (substr($linea,0,6) == "<riga>")
if (substr($linea,-7) == "</riga>") {
$riga = substr($riga,0,-13);
$riga = explode("</cmp><cmp>",$riga);

if ($nome_tabella == "contratti") {
$tipo_contr = $riga[$num_colonna['tipo']];
if (substr($tipo_contr,0,3) == "var") {
$nome = $riga[$num_colonna['testo']];
if ($var_riserv[$nome]) { $file_coerente = "NO"; $num_err .= "#3"; }
} # fine if (substr($tipo_contr,0,3) == "var")
if (substr($tipo_contr,0,4) == "vett") {
$nome = explode(";",$riga[$num_colonna['testo']]);
if ($var_riserv[$nome[0]]) { $file_coerente = "NO"; $num_err .= "#4"; }
if ($var_riserv[$nome[1]]) { $file_coerente = "NO"; $num_err .= "#5"; }
} # fine if (substr($tipo_contr,0,4) == "vett")
} # fine if ($nome_tabella == "contratti")

unset($valore_colonna);
for ($num2 = 0 ; $num2 < count($riga) ; $num2++) {
$valore = aggslashdb($riga[$num2]);
if ($valore != "") {
$valore = str_replace("</righetabella@%&@>","</righetabella>",$valore);
$valore = str_replace("</riga@%&@>","</riga>",$valore);
$valore = str_replace("<riga@%&@>","<riga>",$valore);
$valore = str_replace("</cmp@%&@>","</cmp>",$valore);
$valore = str_replace("@%&@@%&@","@%&@",$valore);
$valore_colonna[$nome_colonna[($num2+1)]] = $valore;
} # fine if ($valore != "")
} # fine for $num2
if (((defined('C_CREA_NUOVI_APP') and C_CREA_NUOVI_APP == "NO") or (defined('C_MASSIMO_NUM_APP') and C_MASSIMO_NUM_APP != 0)) and $nome_tabella == "appartamenti" and $linee_inserite_in_tabella[$nome_tabella] >= $num_appartamenti_prec) { $file_coerente = "NO"; $num_err .= "#6"; }
if ($nome_tabella == "anni") $campo_anno_esistente[$valore_colonna['idanni']] = $valore_colonna['tipo_periodi'];
if (defined('C_CREA_ANNO_NON_ATTUALE') and C_CREA_ANNO_NON_ATTUALE == "NO" and $nome_tabella == "anni" and $valore_colonna['idanni'] < C_PRIMO_ANNO_CREATO) { $file_coerente = "NO"; $num_err .= "#7"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "utenti" and $linee_inserite_in_tabella[$nome_tabella] >= C_MASSIMO_NUM_UTENTI) { $file_coerente = "NO"; $num_err .= "#8"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "gruppi" and $linee_inserite_in_tabella[$nome_tabella] >= C_MASSIMO_NUM_UTENTI) { $file_coerente = "NO"; $num_err .= "#9"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and ($nome_tabella == "sessioni" or $nome_tabella == "transazioni") and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_UTENTI * 25)) { $file_coerente = "NO"; $num_err .= "#10"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "privilegi" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 20)) { $file_coerente = "NO"; $num_err .= "#11"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "personalizza" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_UTENTI * 50)) { $file_coerente = "NO"; $num_err .= "#12"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "nazioni" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 3000)) { $file_coerente = "NO"; $num_err .= "#13"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "regioni" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 3000)) { $file_coerente = "NO"; $num_err .= "#14"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "citta" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 30000)) { $file_coerente = "NO"; $num_err .= "#15"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "documentiid" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 2000)) { $file_coerente = "NO"; $num_err .= "#16"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "parentele" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 1000)) { $file_coerente = "NO"; $num_err .= "#17"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "relutenti" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 80000)) { $file_coerente = "NO"; $num_err .= "#18"; }
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0 and $nome_tabella == "relgruppi" and $linee_inserite_in_tabella[$nome_tabella] > (C_MASSIMO_NUM_UTENTI * 8000)) { $file_coerente = "NO"; $num_err .= "#19"; }
if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0 and $nome_tabella == "clienti" and $linee_inserite_in_tabella[$nome_tabella] >= C_MASSIMO_NUM_CLIENTI) { $file_coerente = "NO"; $num_err .= "#20"; }
if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0 and $nome_tabella == "relclienti" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_CLIENTI * 10)) { $file_coerente = "NO"; $num_err .= "#21"; }
if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0 and $nome_tabella == "transazioniweb" and $linee_inserite_in_tabella[$nome_tabella] >= C_MASSIMO_NUM_CLIENTI) { $file_coerente = "NO"; $num_err .= "#22"; }
if (defined("C_MASSIMO_NUM_COSTI_AGG") and C_MASSIMO_NUM_COSTI_AGG != 0 and substr($nome_tabella,0,8) == "ntariffe" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_COSTI_AGG + 4)) { $file_coerente = "NO"; $num_err .= "#23"; }
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $nome_tabella == "descrizioni" and $linee_inserite_in_tabella[$nome_tabella] >= ((C_MASSIMO_NUM_TARIFFE * 50) + 40)) { $file_coerente = "NO"; $num_err .= "#24"; }
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $nome_tabella == "beniinventario" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_TARIFFE * 10000)) { $file_coerente = "NO"; $num_err .= "#25"; }
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $nome_tabella == "magazzini" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_TARIFFE * 1000)) { $file_coerente = "NO"; $num_err .= "#26"; }
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $nome_tabella == "relinventario" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_TARIFFE * 100000)) { $file_coerente = "NO"; $num_err .= "#27"; }
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $nome_tabella == "casse" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_TARIFFE * 1000)) { $file_coerente = "NO"; $num_err .= "#27.1"; }
if (defined("C_MASSIMO_NUM_CONTRATTI") and C_MASSIMO_NUM_CONTRATTI != 0 and $nome_tabella == "contratti" and $linee_inserite_in_tabella[$nome_tabella] >= ((C_MASSIMO_NUM_CONTRATTI * 200) + 100)) { $file_coerente = "NO"; $num_err .= "#28"; }
if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0 and $nome_tabella == "interconnessioni" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_CLIENTI * 10)) { $file_coerente = "NO"; $num_err .= "#29"; }
if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0 and $nome_tabella == "messaggi" and $linee_inserite_in_tabella[$nome_tabella] >= C_MASSIMO_NUM_CLIENTI) { $file_coerente = "NO"; $num_err .= "#30"; }
if (defined("C_MASSIMO_NUM_STORIA_SOLDI") and C_MASSIMO_NUM_STORIA_SOLDI != 0 and substr($nome_tabella,0,5) == "soldi" and $linee_inserite_in_tabella[$nome_tabella] >= (C_MASSIMO_NUM_STORIA_SOLDI + 1)) { $file_coerente = "NO"; $num_err .= "#31"; }
if (defined("C_MASSIMO_NUM_COSTI") and C_MASSIMO_NUM_COSTI != 0 and substr($nome_tabella,0,5) == "costi" and $linee_inserite_in_tabella[$nome_tabella] >= C_MASSIMO_NUM_COSTI) { $file_coerente = "NO"; $num_err .= "#32"; }
if (substr($nome_tabella,0,8) == "ntariffe" and $valore_colonna['idntariffe'] == 1) $numero_tariffe[substr($nome_tabella,8)] = $valore_colonna['nomecostoagg'];
if ($nome_tabella == "versioni" and $linee_inserite_in_tabella[$nome_tabella] >= 2) { $file_coerente = "NO"; $num_err .= "#33"; }
$linee_inserite_in_tabella[$nome_tabella]++;
} # fine if (substr($linea,-7) == "</riga>")
} # fine if ($leggendo_righe == "SI")
if ($linea == "<righetabella>") {
$leggendo_righe = "SI";
$linee_inserite_in_tabella[$nome_tabella] = 0;
} # fine if ($linea == "<righetabella>")
} # fine if ($leggendo_tabella == "SI")
if ($linea == "<tabella>") $leggendo_tabella = "SI";
} # fine if ($leggendo_database == "SI")

else {
if ($linea == "</file>") $leggendo_file = "NO";
if ($leggendo_file == "SI") {
if ($nome_file) {
if ($leggendo_contenuto == "SI") {
if (substr($linea,-12) == "</contenuto>") {
$leggendo_contenuto = "NO";
$leggendo_cont_file_sel = "NO";
$linea = substr($linea,0,-12);
} # fine if (substr($linea,-12) == "</contenuto>")
$linea = trim($linea);
if ($leggendo_prima_linea == "SI") {
$prima_linea = $linea;
$leggendo_prima_linea = "NO";
} # fine if ($leggendo_prima_linea == "SI")
if ($nome_file == "./dati/versione.php") {
if ($linea != "" and $linea != "<?php" and $linea != "define('C_VERSIONE_ATTUALE',".C_PHPR_VERSIONE_NUM.");" and preg_replace("/define\('C_DIFF_ORE',-?[0-9]{1,2}\);/","",$linea) != "" and $linea != "?>") { $file_coerente = "NO"; $num_err .= "#34"; }
} # fine if ($nome_file == "./dati/versione.php")
if ($nome_file == "./dati/selectappartamenti.php") {
if ($linea == "\";") $leggendo_cont_file_sel = "NO";
if ($leggendo_cont_file_sel != "SI" and $linea != "" and $linea != "<?php" and $linea != "?>" and $linea != "\";" and $linea != "echo \"") { $file_coerente = "NO"; $num_err .= "#35"; }
if ($leggendo_cont_file_sel == "SI") {
if (str_replace("\\\\","",$linea) != $linea) { $file_coerente = "NO"; $num_err .= "#36"; }
$linea = str_replace("\\\"","",$linea);
if (str_replace("\"","",$linea) != $linea) { $file_coerente = "NO"; $num_err .= "#37"; }
} # fine if ($leggendo_cont_file_sel == "SI")
if ($linea == "echo \"") $leggendo_cont_file_sel = "SI";
} # fine if ($nome_file == "./dati/selectappartamenti.php")

if (substr($nome_file,0,20) == "./dati/selectperiodi" or substr($nome_file,0,21) == "./dati/selperiodimenu") {
if ($linea == "\";") $leggendo_cont_file_sel = "NO";
if ($leggendo_cont_file_sel != "SI") {
$linea_trovata = "NO";
if ($linea == "") $linea_trovata = "SI";
if ($linea == "<?php") $linea_trovata = "SI";
if ($linea == "?>") $linea_trovata = "SI";
if ($linea == "\";") $linea_trovata = "SI";
if ($linea == "\$dates_options_list = \"") $linea_trovata = "SI";
if (str_replace("\$y_ini_menu = array();","",$linea) == "")  $linea_trovata = "SI";
if (str_replace("\$m_ini_menu = array();","",$linea) == "")  $linea_trovata = "SI";
if (str_replace("\$d_ini_menu = array();","",$linea) == "")  $linea_trovata = "SI";
if (str_replace("\$n_dates_menu = array();","",$linea) == "")  $linea_trovata = "SI";
if (str_replace("\$d_increment = array();","",$linea) == "")  $linea_trovata = "SI";
if (preg_replace("/\\\$y_ini_menu\\[[0-9]{1,2}\\] = \"[0-9]{1,4}\";/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$m_ini_menu\\[[0-9]{1,2}\\] = \"[0-9]{1,2}\";/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$d_ini_menu\\[[0-9]{1,2}\\] = \"[0-9]{1,2}\";/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$n_dates_menu\\[[0-9]{1,2}\\] = \"[0-9]{1,4}\";/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$d_increment\\[[0-9]{1,2}\\] = \"[0-9]{1,2}\";/","",$linea) == "") $linea_trovata = "SI";
if ($linea == "\$partial_dates = 1;") $linea_trovata = "SI";
if (preg_replace("/\\\$d_names = \"\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\"\";/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$m_names = \"\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\",\\\\\"[^\"]{1,16}\\\\\"\";/","",$linea) == "") $linea_trovata = "SI";
if ($linea_trovata != "SI") { $file_coerente = "NO"; $num_err .= "#38"; }
} # fine if ($leggendo_cont_file_sel != "SI")
if ($leggendo_cont_file_sel == "SI") {
if (str_replace("\\\\","",$linea) != $linea) { $file_coerente = "NO"; $num_err .= "#39"; }
$linea = str_replace("\\\"","",$linea);
if (str_replace("\"","",$linea) != $linea) { $file_coerente = "NO"; $num_err .= "#40"; }
} # fine if ($leggendo_cont_file_sel == "SI")
if ($linea == "\$dates_options_list = \"") $leggendo_cont_file_sel = "SI";
} # fine if (substr($nome_file,0,20) == "./dati/selectperiodi" or...

if ($nome_file == "./dati/abilita_login" and $linea != "") { $file_coerente = "NO"; $num_err .= "#41"; }
if ($nome_file == "./dati/lingua.php" and $linea != "" and $linea != "<?php" and $linea != "?>" and preg_replace("/\\\$lingua\\[[0-9]{1,8}\\] = \"[a-z]{1,3}\";/","",$linea) != "") { $file_coerente = "NO"; $num_err .= "#42"; }
if ($nome_file == "./dati/unit.php" or $nome_file == "./dati/unit_single.php") {
$linea_trovata = "NO";
if ($linea == "") $linea_trovata = "SI";
if ($linea == "<?php") $linea_trovata = "SI";
if ($linea == "?>") $linea_trovata = "SI";
if (preg_replace("/^\\\$unit\\['(p_n|s_n|gender)'\\] = \\\$trad_var\\['[a-z_]*'\\];/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/^\\\$unit\\['lang'\\]\\['[a-z]{2,3}'\\]\\['(p_n|s_n|gender)'\\] = '[^']*';/","",$linea) == "") $linea_trovata = "SI";
if ($linea == "\$unit['s_n'] = \$unit['lang'][\$lingua_mex]['s_n'];") $linea_trovata = "SI";
if ($linea == "\$unit['p_n'] = \$unit['lang'][\$lingua_mex]['p_n'];") $linea_trovata = "SI";
if ($linea == "\$unit['gender'] = \$unit['lang'][\$lingua_mex]['gender'];") $linea_trovata = "SI";
if ($linea == "\$unit['special'] = 0;") $linea_trovata = "SI";
if ($linea == "\$car_spec = explode(\",\",\$trad_var['special_characters']);") $linea_trovata = "SI";
if ($linea == "for (\$num1 = 0 ; \$num1 < count(\$car_spec) ; \$num1++) if (substr(\$unit['p_n'],0,strlen(\$car_spec[\$num1])) == \$car_spec[\$num1]) \$unit['special'] = 1;") $linea_trovata = "SI";
if ($linea_trovata != "SI") { $file_coerente = "NO"; $num_err .= "#42.1"; }
} # fine if ($nome_file == "./dati/unit.php" or $nome_file == "./dati/unit_single.php")
if ($nome_file == "./dati/tema.php" and $linea != "" and $linea != "<?php" and $linea != "?>" and preg_replace("/\\\$tema\\[[0-9]{1,8}\\] = \"[a-z]{1,4}\";/","",$linea) != "" and preg_replace("/\\\$parole_sost = [01];/","",$linea) != "") { $file_coerente = "NO"; $num_err .= "#43"; }
if ($nome_file == "./dati/parole_sost.php" and $linea != "" and $linea != "<?php" and $linea != "?>" and preg_replace("/\\\$messaggio = str_replace\\(\"[^\"]*\",\"[^\"]*\",\\\$messaggio\\);/","",$linea) != "") { $file_coerente = "NO"; $num_err .= "#44"; }
if ($nome_file == "./dati/dati_interconnessioni.php") {
$linea_trovata = "NO";
if ($linea == "") $linea_trovata = "SI";
if ($linea == "<?php") $linea_trovata = "SI";
if ($linea == "?>") $linea_trovata = "SI";
if (preg_replace("/^#[0-9a-zA-Z _]*/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/^unset\\(\\\$ic_[a-z_]+[0-9a-z_]*\\);/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$ic_[a-z_]+[0-9a-z_]*\\[?\"?[0-9a-zA-Z_]*\"?\\]?\\[?\"?[0-9a-zA-Z_]*\"?\\]?\\[?\"?[0-9a-zA-Z_]*\"?\\]? = \"[^\"]*\";/","",$linea) == "") $linea_trovata = "SI";
if (preg_replace("/\\\$ic_org[a-z_]+[0-9a-z_]*_ic = C_[A-Z][A-Z_]*[A-Z]_IC;/","",$linea) == "") $linea_trovata = "SI";
if ($linea_trovata != "SI") { $file_coerente = "NO"; $num_err .= "#45<br>".htmlspecialchars($linea)."<br>"; }
} # fine if ($nome_file == "./dati/dati_interconnessioni.php")
if ($nome_file == "./dati/log_utenti.php" and ($prima_linea != "<?php exit(); ?>" or ($linea != "<?php exit(); ?>" and preg_replace("/^[a0-9-]{1,1}[0-9]*>[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2} [0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}>[a-zA-Z]?.*/","",$linea) != ""))) { $file_coerente = "NO"; $num_err .= "#46"; }
if ($leggendo_contenuto == "NO") $nome_file = "";
} # fine if ($leggendo_contenuto == "SI")
if ($linea == "<contenuto>") {
$leggendo_contenuto = "SI";
$leggendo_prima_linea = "SI";
} # fine if ($linea == "<contenuto>")
} # fine if ($nome_file)
if (substr($linea,0,10) == "<nomefile>") {
$nome_file = substr($linea,10);
$nome_file = substr($nome_file,0,-11);
if (defined('C_CREA_SUBORDINAZIONI') and C_CREA_SUBORDINAZIONI == "NO" and str_replace("dati_subordinazione","",$nome_file) != $nome_file) { $file_coerente = "NO"; $num_err .= "#47"; }
if (substr($nome_file,0,7) != "./dati/") { $file_coerente = "NO"; $num_err .= "#48"; }
$nome_file_vett = explode("/",$nome_file);
if (count($nome_file_vett) > 3) { $file_coerente = "NO"; $num_err .= "#49"; }
$nome_file_trovato = "NO";
if ($nome_file == "./dati/selectappartamenti.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/abilita_login") $nome_file_trovato = "SI";
if ($nome_file == "./dati/lingua.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/unit.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/unit_single.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/tema.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/parole_sost.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/dati_interconnessioni.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/log_utenti.php") $nome_file_trovato = "SI";
if ($nome_file == "./dati/versione.php") $nome_file_trovato = "SI";
if (substr($nome_file,0,20) == "./dati/selectperiodi") $nome_file_trovato = "SI";
if (substr($nome_file,0,21) == "./dati/selperiodimenu") $nome_file_trovato = "SI";
if ($nome_file_trovato == "NO") { $file_coerente = "NO"; $num_err .= "#50"; }
} # fine if (substr($linea,0,9) == "<nomefile>")
} # fine if ($leggendo_file == "SI")
if ($linea == "<file>" and $leggendo_modello != "SI") $leggendo_file = "SI";

} # fine else if ($leggendo_database == "SI")
if ($linea == "<database>" and $leggendo_modello != "SI") $leggendo_database = "SI";
if ($linea == "</modello>") $leggendo_modello = "NO";
if ($linea == "<modello>" and $leggendo_database != "SI" and $leggendo_file != "SI") $leggendo_modello = "SI";
} # fine if ($leggendo_backup == "SI")
if ($linea == "<backup>") $leggendo_backup = "SI";

} # fine for $num1
} # fine while (!feof($fbackup))
fclose ($fbackup);
} # fine if ($fbackup)
else { $file_coerente = "NO"; $num_err .= "#51"; }

if (preg_replace("/SI[0-9,]*/","",$phpr_log) != "") $phpr_log = "NO";
if (!@is_array($campo_anno_esistente) or !@is_array($tab_anno_esistente)) { $file_coerente = "NO"; $num_err .= "#52"; }
else {
if (count($campo_anno_esistente) != count($tab_anno_esistente)) { $file_coerente = "NO"; $num_err .= "#53"; }
reset($campo_anno_esistente);
foreach ($campo_anno_esistente as $key => $val) {
if ($tab_anno_esistente[$key] != "SI") { $file_coerente = "NO"; $num_err .= "#54"; }
if ($tab_presente[$key]['prenota'] != "SI") { $file_coerente = "NO"; $num_err .= "#55"; }
if ($tab_presente[$key]['costiprenota'] != "SI") { $file_coerente = "NO"; $num_err .= "#56"; }
if ($tab_presente[$key]['rclientiprenota'] != "SI") { $file_coerente = "NO"; $num_err .= "#57"; }
if ($tab_presente[$key]['costi'] != "SI") { $file_coerente = "NO"; $num_err .= "#58"; }
if ($tab_presente[$key]['regole'] != "SI") { $file_coerente = "NO"; $num_err .= "#59"; }
if ($tab_presente[$key]['soldi'] != "SI") { $file_coerente = "NO"; $num_err .= "#60"; }
if ($tab_presente[$key]['periodi'] != "SI") { $file_coerente = "NO"; $num_err .= "#61"; }
if ($tab_presente[$key]['ntariffe'] != "SI") { $file_coerente = "NO"; $num_err .= "#62"; }
if (defined("C_PRIMO_ANNO_CREATO") and C_CREA_ANNO_NON_ATTUALE == "NO" and $key < C_PRIMO_ANNO_CREATO) { $file_coerente = "NO"; $num_err .= "#63"; }
if (defined('C_CREA_ANNO_NON_ATTUALE') and C_CREA_ANNO_NON_ATTUALE == "NO" and $key > date("Y",(time() + (C_DIFF_ORE * 3600)))) { $file_coerente = "NO"; $num_err .= "#64"; }
if (!$anno_max or $key > $anno_max) {
$anno_max = $key;
$ultimo_tipo_periodi = $val;
} # fine if (!$anno_max or $key > $anno_max)
if (!$numero_tariffe[$key] or controlla_num_pos($numero_tariffe[$key]) == "NO") { $file_coerente = "NO"; $num_err .= "#65"; }
if (defined("C_MASSIMO_NUM_TARIFFE") and C_MASSIMO_NUM_TARIFFE != 0 and $numero_tariffe[$key] > C_MASSIMO_NUM_TARIFFE) { $file_coerente = "NO"; $num_err .= "#66"; }
if ($val != "g" and $val != "s") { $file_coerente = "NO"; $num_err .= "#67"; }
if (($val == "g" and $linee_inserite_in_tabella["periodi".$key] > 1825) or ($val == "s" and $linee_inserite_in_tabella["periodi".$key] > 260)) { $file_coerente = "NO"; $num_err .= "#68"; }
$prenotazioni_max = ($linee_inserite_in_tabella["periodi".$key] * ($linee_inserite_in_tabella["appartamenti"] + 2));
if (defined("C_MASSIMO_NUM_COSTI_AGG_IN_PRENOTA") and C_MASSIMO_NUM_COSTI_AGG_IN_PRENOTA != 0 and $linee_inserite_in_tabella["costiprenota".$key] > (C_MASSIMO_NUM_COSTI_AGG_IN_PRENOTA * $prenotazioni_max)) { $file_coerente = "NO"; $num_err .= "#69"; }
if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0 and $linee_inserite_in_tabella["rclientiprenota".$key] > (C_MASSIMO_NUM_CLIENTI * $prenotazioni_max)) { $file_coerente = "NO"; $num_err .= "#70"; }
if ($linee_inserite_in_tabella["prenota".$key] > $prenotazioni_max) { $file_coerente = "NO"; $num_err .= "#71"; }
if ($linee_inserite_in_tabella["regole".$key] > ($prenotazioni_max + ($numero_tariffe[$key] * 2))) { $file_coerente = "NO"; $num_err .= "#72"; }
} # fine foreach ($campo_anno_esistente as $key => $val)
} # fine else if (!@is_array($campo_anno_esistente) or...
if (defined('C_CAMBIA_TIPO_PERIODI') and C_CAMBIA_TIPO_PERIODI == "NO" and $tipo_periodi_prec[$ultimo_anno_prec] != $ultimo_tipo_periodi) { $file_coerente = "NO"; $num_err .= "#73"; }
if ($tab_presente['clienti'] != "SI") { $file_coerente = "NO"; $num_err .= "#74"; }
if ($tab_presente['relclienti'] != "SI") { $file_coerente = "NO"; $num_err .= "#75"; }
if ($tab_presente['anni'] != "SI") { $file_coerente = "NO"; $num_err .= "#76"; }
if ($tab_presente['versioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#77"; }
if ($tab_presente['nazioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#78"; }
if ($tab_presente['regioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#79"; }
if ($tab_presente['citta'] != "SI") { $file_coerente = "NO"; $num_err .= "#80"; }
if ($tab_presente['documentiid'] != "SI") { $file_coerente = "NO"; $num_err .= "#81"; }
if ($tab_presente['parentele'] != "SI") { $file_coerente = "NO"; $num_err .= "#82"; }
if ($tab_presente['personalizza'] != "SI") { $file_coerente = "NO"; $num_err .= "#83"; }
if ($tab_presente['appartamenti'] != "SI") { $file_coerente = "NO"; $num_err .= "#84"; }
if ($tab_presente['utenti'] != "SI") { $file_coerente = "NO"; $num_err .= "#85"; }
if ($tab_presente['gruppi'] != "SI") { $file_coerente = "NO"; $num_err .= "#86"; }
if ($tab_presente['privilegi'] != "SI") { $file_coerente = "NO"; $num_err .= "#87"; }
if ($tab_presente['relutenti'] != "SI") { $file_coerente = "NO"; $num_err .= "#88"; }
if ($tab_presente['relgruppi'] != "SI") { $file_coerente = "NO"; $num_err .= "#89"; }
if ($tab_presente['sessioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#90"; }
if ($tab_presente['transazioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#91"; }
if ($tab_presente['transazioniweb'] != "SI") { $file_coerente = "NO"; $num_err .= "#92"; }
if ($tab_presente['descrizioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#93"; }
if ($tab_presente['beniinventario'] != "SI") { $file_coerente = "NO"; $num_err .= "#94"; }
if ($tab_presente['magazzini'] != "SI") { $file_coerente = "NO"; $num_err .= "#95"; }
if ($tab_presente['relinventario'] != "SI") { $file_coerente = "NO"; $num_err .= "#96"; }
if ($tab_presente['casse'] != "SI") { $file_coerente = "NO"; $num_err .= "#96.1"; }
if ($tab_presente['contratti'] != "SI") { $file_coerente = "NO"; $num_err .= "#97"; }
if ($tab_presente['interconnessioni'] != "SI") { $file_coerente = "NO"; $num_err .= "#98"; }
if ($tab_presente['messaggi'] != "SI") { $file_coerente = "NO"; $num_err .= "#99"; }
unset($tab_presente);
unset($var_riserv);


if ($file_coerente == "NO") {
if ($silenzio != "SI") echo mex("Il formato del file è errato",$pag).".<br>";
# debug backup COMPLETO
#if ($num_err) echo mex("Errori",$pag).": $num_err.<br>";
} # fine if ($file_coerente == "NO")
else {



if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) {
$HOTELD_DB_TYPE = "";
$HOTELD_DB_NAME = "";
$HOTELD_DB_HOST = "";
$HOTELD_DB_PORT = "";
$HOTELD_DB_USER = "";
$HOTELD_DB_PASS = "";
$HOTELD_TAB_PRE = "";
include(C_EXT_DB_DATA_PATH);
if ($HOTELD_DB_TYPE) $N_PHPR_DB_TYPE = $HOTELD_DB_TYPE;
if ($HOTELD_DB_NAME) $N_PHPR_DB_NAME = $HOTELD_DB_NAME;
if ($HOTELD_DB_HOST) $N_PHPR_DB_HOST = $HOTELD_DB_HOST;
if (strcmp($HOTELD_DB_PORT,"")) $N_PHPR_DB_PORT = $HOTELD_DB_PORT;
if ($HOTELD_DB_USER) $N_PHPR_DB_USER = $HOTELD_DB_USER;
if (strcmp($HOTELD_DB_PASS,"")) $N_PHPR_DB_PASS = $HOTELD_DB_PASS;
if ($HOTELD_TAB_PRE) $N_PHPR_TAB_PRE = $HOTELD_TAB_PRE;
} # fine if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH)
if ($N_PHPR_DB_TYPE == "mysql" and @function_exists('mysqli_connect')) $N_PHPR_DB_TYPE = "mysqli";


if ($N_PHPR_DB_NAME != $PHPR_DB_NAME or $N_PHPR_DB_HOST != $PHPR_DB_HOST or $N_PHPR_DB_PORT != $PHPR_DB_PORT or $N_PHPR_DB_TYPE != $PHPR_DB_TYPE) {
$nuovo_db = "SI";
if ($N_PHPR_DB_NAME == $PHPR_DB_NAME and $N_PHPR_DB_HOST == $PHPR_DB_HOST and $N_PHPR_DB_PORT == $PHPR_DB_PORT and substr($N_PHPR_DB_TYPE,0,5) == "mysql" and substr($PHPR_DB_TYPE,0,5) == "mysql") $database_esistente = "SI";
} # fine if ($N_PHPR_DB_NAME != $PHPR_DB_NAME or $N_PHPR_DB_HOST != $PHPR_DB_HOST or...
if ($nuovo_db == "SI" or $N_PHPR_TAB_PRE != $PHPR_TAB_PRE) $mantieni_anni = "";
unset($anno_mantieni);

$tabelle_lock = array($tableanni,$tableutenti);
$altre_tab_lock = array($PHPR_TAB_PRE."clienti",$PHPR_TAB_PRE."personalizza",$PHPR_TAB_PRE."privilegi");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$anni_vecchi = esegui_query("select * from $tableanni order by idanni");
$num_anni_vecchi = numlin_query($anni_vecchi);
$utenti_vecchi = esegui_query("select * from $tableutenti order by idutenti");
unlink(C_DATI_PATH."/dati_connessione.php");
unlink(C_DATI_PATH."/selectappartamenti.php");
unlink(C_DATI_PATH."/versione.php");
unlink(C_DATI_PATH."/tema.php");
if (@is_file(C_DATI_PATH."/parole_sost.php")) unlink(C_DATI_PATH."/parole_sost.php");
if (@is_file(C_DATI_PATH."/dati_interconnessioni.php")) unlink(C_DATI_PATH."/dati_interconnessioni.php");
if (@is_file(C_DATI_PATH."/abilita_login")) unlink(C_DATI_PATH."/abilita_login");
if (@is_file(C_DATI_PATH."/log_utenti.php")) unlink(C_DATI_PATH."/log_utenti.php");
#if (@is_file(C_DATI_PATH."/ultimo_accesso")) unlink(C_DATI_PATH."/ultimo_accesso");
esegui_query("drop table ".$PHPR_TAB_PRE."anni");
esegui_query("drop table ".$PHPR_TAB_PRE."utenti");
unlock_tabelle($tabelle_lock);
for ($num1 = 0 ; $num1 < $num_anni_vecchi ; $num1 = $num1 + 1) {
$anno_cancella = risul_query($anni_vecchi,$num1,'idanni');
if (!$mantieni_anni or $campo_anno_esistente[$anno_cancella]) {
esegui_query("drop table ".$PHPR_TAB_PRE."prenota$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."costiprenota$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."rclientiprenota$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."periodi$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."ntariffe$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."regole$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."soldi$anno_cancella");
esegui_query("drop table ".$PHPR_TAB_PRE."costi$anno_cancella");
for ($num2 = 0 ; $num2 < numlin_query($utenti_vecchi) ; $num2++) {
$idutente_canc = risul_query($utenti_vecchi,$num2,'idutenti');
if (@is_file(C_DATI_PATH."/selectperiodi$anno_cancella.$idutente_canc.php")) unlink(C_DATI_PATH."/selectperiodi$anno_cancella.$idutente_canc.php");
if (@is_file(C_DATI_PATH."/selperiodimenu$anno_cancella.$idutente_canc.php")) unlink(C_DATI_PATH."/selperiodimenu$anno_cancella.$idutente_canc.php");
} # fine for $num2
} # fine if (!$mantieni_anni or $campo_anno_esistente[$anno_cancella])
elseif ($mantieni_anni) $anno_mantieni[$anno_cancella] = 1;
} # fine for $num1
if ($mantieni_anni and @is_array($anno_mantieni)) {
$clienti_vecchi = esegui_query("select * from ".$PHPR_TAB_PRE."clienti order by idclienti");
$max_clienti_vecchi = esegui_query("select max(idclienti) from ".$PHPR_TAB_PRE."clienti");
$max_clienti_vecchi = risul_query($max_clienti_vecchi,0,0);
$privilegi_vecchi = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi order by idutente");
$personalizza_vecchi = esegui_query("select * from ".$PHPR_TAB_PRE."personalizza where idpersonalizza $LIKE 'giorno_vedi_ini_sett%' ");
} # fine if ($mantieni_anni and @is_array($anno_mantieni))
esegui_query("drop table ".$PHPR_TAB_PRE."appartamenti");
esegui_query("drop table ".$PHPR_TAB_PRE."clienti");
esegui_query("drop table ".$PHPR_TAB_PRE."relclienti");
esegui_query("drop table ".$PHPR_TAB_PRE."personalizza");
esegui_query("drop table ".$PHPR_TAB_PRE."versioni");
esegui_query("drop table ".$PHPR_TAB_PRE."nazioni");
esegui_query("drop table ".$PHPR_TAB_PRE."regioni");
esegui_query("drop table ".$PHPR_TAB_PRE."citta");
esegui_query("drop table ".$PHPR_TAB_PRE."documentiid");
esegui_query("drop table ".$PHPR_TAB_PRE."parentele");
esegui_query("drop table ".$PHPR_TAB_PRE."gruppi");
esegui_query("drop table ".$PHPR_TAB_PRE."privilegi");
esegui_query("drop table ".$PHPR_TAB_PRE."relutenti");
esegui_query("drop table ".$PHPR_TAB_PRE."relgruppi");
esegui_query("drop table ".$PHPR_TAB_PRE."sessioni");
esegui_query("drop table ".$PHPR_TAB_PRE."transazioni");
esegui_query("drop table ".$PHPR_TAB_PRE."transazioniweb");
esegui_query("drop table ".$PHPR_TAB_PRE."descrizioni");
esegui_query("drop table ".$PHPR_TAB_PRE."beniinventario");
esegui_query("drop table ".$PHPR_TAB_PRE."magazzini");
esegui_query("drop table ".$PHPR_TAB_PRE."relinventario");
esegui_query("drop table ".$PHPR_TAB_PRE."casse");
esegui_query("drop table ".$PHPR_TAB_PRE."contratti");
esegui_query("drop table ".$PHPR_TAB_PRE."cache");
esegui_query("drop table ".$PHPR_TAB_PRE."interconnessioni");
esegui_query("drop table ".$PHPR_TAB_PRE."messaggi");

disconnetti_db($numconnessione);

if ($nuovo_db == "SI") {
if ($silenzio != "SI") echo "<br>".mex("Vecchio database svuotato, per rimuoverlo del tutto procedere manualmente",$pag).".<br><br>";
if ($database_esistente != "SI") {
if ($N_PHPR_DB_TYPE == "postgresql") {
if ($N_PHPR_LOAD_EXT == "SI" and $ext_pgsql_caricata != "SI") {
dl("pgsql.so");
$ext_pgsql_caricata = "SI";
} # fine if ($N_PHPR_LOAD_EXT == "SI" and $ext_pgsql_caricata != "SI")
$numconnessione = pg_connect("dbname=$tempdatabase host=$N_PHPR_DB_HOST port=$N_PHPR_DB_PORT user=$N_PHPR_DB_USER password=$N_PHPR_DB_PASS ");
$encoding = " with encoding = 'SQL_ASCII'";
$encoding = "";
pg_exec("set datestyle to 'iso'");
} # fine if ($N_PHPR_DB_TYPE == "postgresql")
if ($N_PHPR_DB_TYPE == "mysql") {
if ($N_PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI") {
dl("mysql.so");
$ext_mysql_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI")
$numconnessione = mysql_connect("$N_PHPR_DB_HOST:$N_PHPR_DB_PORT", "$N_PHPR_DB_USER", "$N_PHPR_DB_PASS");
@mysql_query("SET NAMES 'utf8'");
@mysql_query("SET default_storage_engine=MYISAM");
$encoding = "";
} # fine if ($N_PHPR_DB_TYPE == "mysql")
if ($N_PHPR_DB_TYPE == "mysqli") {
if ($N_PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI") {
dl("mysqli.so");
$ext_mysql_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI")
$numconnessione = mysqli_connect($N_PHPR_DB_HOST,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,"",$N_PHPR_DB_PORT);
@mysqli_query($numconnessione,"SET NAMES 'utf8'");
@mysqli_query($numconnessione,"SET default_storage_engine=MYISAM");
$encoding = "";
} # fine if ($N_PHPR_DB_TYPE == "mysqli")
if ($N_PHPR_DB_TYPE == "sqlite") {
if ($N_PHPR_LOAD_EXT == "SI" and $ext_sqlite_caricata != "SI") {
dl("sqlite.so");
$ext_sqlite_caricata = "SI";
} # fine if ($PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI")
$numconnessione = new SQLite3(C_DATI_PATH."/db_".$N_PHPR_DB_NAME);
$query = $numconnessione;
$database_esistente = "SI";
} # fine if ($N_PHPR_DB_TYPE == "sqlite")
if ($database_esistente != "SI") $query = esegui_query2("create database $N_PHPR_DB_NAME $encoding",$N_PHPR_DB_TYPE);
if ($N_PHPR_DB_TYPE == "postgresql") pg_close($numconnessione);
if ($N_PHPR_DB_TYPE == "mysql") mysql_close($numconnessione);
if ($N_PHPR_DB_TYPE == "mysqli") mysqli_close($numconnessione);
if ($N_PHPR_DB_TYPE == "sqlite") $numconnessione->close();
} # fine if ($database_esistente != "SI")
else $query = 1;
} # fine if ($nuovo_db == "SI")
else $query = 1;
unlink(C_DATI_PATH."/lingua.php");
unlink(C_DATI_PATH."/unit.php");
unlink(C_DATI_PATH."/unit_single.php");

if ($query) {
$character_set_db = "";
$collation_db = "";
if ($N_PHPR_DB_TYPE == "postgresql") {
$DATETIME = "timestamp";
$MEDIUMTEXT = "text";
if ($N_PHPR_LOAD_EXT == "SI" and $ext_pgsql_caricata != "SI") {
dl("pgsql.so");
$ext_pgsql_caricata = "SI";
} # fine if ($N_PHPR_LOAD_EXT == "SI" and $ext_pgsql_caricata != "SI")
$numconnessione = pg_connect("dbname=$N_PHPR_DB_NAME host=$N_PHPR_DB_HOST port=$N_PHPR_DB_PORT user=$N_PHPR_DB_USER password=$N_PHPR_DB_PASS ");
pg_exec("set datestyle to 'iso'");
} # fine if ($N_PHPR_DB_TYPE == "postgresql")
if ($N_PHPR_DB_TYPE == "mysql") {
$DATETIME = "datetime";
$MEDIUMTEXT = "mediumtext";
if ($N_PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI") {
dl("mysql.so");
$ext_mysql_caricata = "SI";
} # fine if ($N_PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI")
$numconnessione = mysql_connect ("$N_PHPR_DB_HOST:$N_PHPR_DB_PORT", "$N_PHPR_DB_USER", "$N_PHPR_DB_PASS");
@mysql_query("SET NAMES 'utf8'");
@mysql_query("SET default_storage_engine=MYISAM");
$select_db = mysql_select_db($N_PHPR_DB_NAME);
if (!$select_db) $numconnessione = "";
else {
$character_set = mysql_query("SHOW VARIABLES LIKE 'character_set_database'");
$collation = mysql_query("SHOW VARIABLES LIKE 'collation_database'");
if (mysql_num_rows($character_set) == 1 and mysql_num_rows($collation) == 1) {
$character_set_db = mysql_result($character_set,0,'Value');
$collation_db = mysql_result($collation,0,'Value');
if ($character_set_db != "utf8" or $collation_db != "utf8_general_ci") mysql_query("alter database $N_PHPR_DB_NAME default character set 'utf8' collate 'utf8_general_ci'");
} # fine if (mysql_num_rows($character_set) == 1 and mysql_num_rows($collation) == 1)
} # fine else if (!$select_db)
} # fine if ($N_PHPR_DB_TYPE == "mysql")
if ($N_PHPR_DB_TYPE == "mysqli") {
$DATETIME = "datetime";
$MEDIUMTEXT = "mediumtext";
if ($N_PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI") {
dl("mysqli.so");
$ext_mysql_caricata = "SI";
} # fine if ($N_PHPR_LOAD_EXT == "SI" and $ext_mysql_caricata != "SI")
global $link_mysqli;
$numconnessione = mysqli_connect($N_PHPR_DB_HOST,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,"",$N_PHPR_DB_PORT);
$link_mysqli = $numconnessione;
@mysqli_query($numconnessione,"SET NAMES 'utf8'");
@mysqli_query($numconnessione,"SET default_storage_engine=MYISAM");
$select_db = mysqli_select_db($numconnessione,$N_PHPR_DB_NAME);
if (!$select_db) $numconnessione = "";
else {
$character_set = mysqli_query($numconnessione,"SHOW VARIABLES LIKE 'character_set_database'");
$collation = mysqli_query($numconnessione,"SHOW VARIABLES LIKE 'collation_database'");
if (mysqli_num_rows($character_set) == 1 and mysqli_num_rows($collation) == 1) {
$character_set_db = mysqli_fetch_assoc($character_set);
$character_set_db = $character_set_db['Value'];
$collation_db = mysqli_fetch_assoc($collation);
$collation_db = $collation_db['Value'];
if ($character_set_db != "utf8" or $collation_db != "utf8_general_ci") mysqli_query($numconnessione,"alter database $N_PHPR_DB_NAME default character set 'utf8' collate 'utf8_general_ci'");
} # fine if (mysqli_num_rows($character_set) == 1 and mysqli_num_rows($collation) == 1)
} # fine else if (!$select_db)
} # fine if ($N_PHPR_DB_TYPE == "mysqli")
if ($N_PHPR_DB_TYPE == "sqlite") {
$DATETIME = "text";
$MEDIUMTEXT = "text";
if ($N_PHPR_LOAD_EXT == "SI" and $ext_sqlite_caricata != "SI") {
dl("sqlite.so");
$ext_sqlite_caricata = "SI";
} # fine if ($N_PHPR_LOAD_EXT == "SI" and $ext_sqlite_caricata != "SI")
$numconnessione = new SQLite3(C_DATI_PATH."/db_".$N_PHPR_DB_NAME);
} # fine if ($N_PHPR_DB_TYPE == "sqlite")

if ($numconnessione) {

$fileaperto = fopen(C_DATI_PATH."/dati_connessione.php","w+");
if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) {
if ($HOTELD_DB_TYPE) $N_PHPR_DB_TYPE = "";
if ($HOTELD_DB_NAME) $N_PHPR_DB_NAME = "";
if ($HOTELD_DB_HOST) $N_PHPR_DB_HOST = "";
if (strcmp($HOTELD_DB_PORT,"")) $N_PHPR_DB_PORT = "";
if ($HOTELD_DB_USER) $N_PHPR_DB_USER = "";
if (strcmp($HOTELD_DB_PASS,"")) $N_PHPR_DB_PASS = "";
if ($HOTELD_TAB_PRE) $N_PHPR_TAB_PRE = "";
} # fine if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH)
$N_PHPR_DB_NAME = aggiungi_slash($N_PHPR_DB_NAME);
$N_PHPR_DB_HOST = aggiungi_slash($N_PHPR_DB_HOST);
$N_PHPR_DB_USER = aggiungi_slash($N_PHPR_DB_USER);
$N_PHPR_DB_PASS = aggiungi_slash($N_PHPR_DB_PASS);
fwrite($fileaperto,"<?php
\$PHPR_DB_TYPE = \"$N_PHPR_DB_TYPE\";
\$PHPR_DB_NAME = \"$N_PHPR_DB_NAME\";
\$PHPR_DB_HOST = \"$N_PHPR_DB_HOST\";
\$PHPR_DB_PORT = \"$N_PHPR_DB_PORT\";
\$PHPR_DB_USER = \"$N_PHPR_DB_USER\";
\$PHPR_DB_PASS = \"$N_PHPR_DB_PASS\";
\$PHPR_LOAD_EXT = \"$N_PHPR_LOAD_EXT\";
\$PHPR_TAB_PRE = \"$N_PHPR_TAB_PRE\";
\$PHPR_LOG = \"$phpr_log\";
");
if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) {
fwrite($fileaperto,"
\$HOTELD_DB_TYPE = \"\";
\$HOTELD_DB_NAME = \"\";
\$HOTELD_DB_HOST = \"\";
\$HOTELD_DB_PORT = \"\";
\$HOTELD_DB_USER = \"\";
\$HOTELD_DB_PASS = \"\";
\$HOTELD_TAB_PRE = \"\";
require('".C_EXT_DB_DATA_PATH."');
if (\$HOTELD_DB_TYPE) {
\$PHPR_DB_TYPE = \$HOTELD_DB_TYPE;
if (\$PHPR_DB_TYPE == \"mysql\" and @function_exists('mysqli_connect')) \$PHPR_DB_TYPE = \"mysqli\";
}
if (\$HOTELD_DB_NAME) \$PHPR_DB_NAME = \$HOTELD_DB_NAME;
if (\$HOTELD_DB_HOST) \$PHPR_DB_HOST = \$HOTELD_DB_HOST;
if (strcmp(\$HOTELD_DB_PORT,\"\")) \$PHPR_DB_PORT = \$HOTELD_DB_PORT;
if (\$HOTELD_DB_USER) \$PHPR_DB_USER = \$HOTELD_DB_USER;
if (strcmp(\$HOTELD_DB_PASS,\"\")) \$PHPR_DB_PASS = \$HOTELD_DB_PASS;
if (\$HOTELD_TAB_PRE) \$PHPR_TAB_PRE = \$HOTELD_TAB_PRE;
");
if ($HOTELD_DB_TYPE) {
$N_PHPR_DB_TYPE = $HOTELD_DB_TYPE;
if ($N_PHPR_DB_TYPE == "mysql" and @function_exists('mysqli_connect')) $N_PHPR_DB_TYPE = "mysqli";
} # fine if ($HOTELD_DB_TYPE)
if ($HOTELD_DB_NAME) $N_PHPR_DB_NAME = $HOTELD_DB_NAME;
if ($HOTELD_DB_HOST) $N_PHPR_DB_HOST = $HOTELD_DB_HOST;
if (strcmp($HOTELD_DB_PORT,"")) $N_PHPR_DB_PORT = $HOTELD_DB_PORT;
if ($HOTELD_DB_USER) $N_PHPR_DB_USER = $HOTELD_DB_USER;
if (strcmp($HOTELD_DB_PASS,"")) $N_PHPR_DB_PASS = $HOTELD_DB_PASS;
if ($HOTELD_TAB_PRE) $N_PHPR_TAB_PRE = $HOTELD_TAB_PRE;
} # fine if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH)
fwrite($fileaperto,"?>");
fclose($fileaperto);
@chmod(C_DATI_PATH."/dati_connessione.php", 0640);

$leggendo_backup = "NO";
$leggendo_database = "NO";
$leggendo_tabella = "NO";
$leggendo_colonne = "NO";
$leggendo_righe = "NO";
$leggendo_file = "NO";
$leggendo_contenuto = "NO";
$nome_file = "";
$linee_inserite_in_tabella = array();


$ultima_linea = "";
$fbackup = fopen($file,"r");
if ($fbackup) {
$leggendo_righe = "NO";
while (!feof($fbackup)) {
unset($linee_backup);
$linee_backup = fread($fbackup,524288);
$linee_backup = explode("\n",$linee_backup);
$num_linee_backup = (count($linee_backup) - 1);
$linee_backup[0] = $ultima_linea.$linee_backup[0];
if (!feof($fbackup)) $ultima_linea = $linee_backup[$num_linee_backup];
else $num_linee_backup++;
for ($num1 = 0 ; $num1 < $num_linee_backup ; $num1++) {

$linea = togli_acapo($linee_backup[$num1]);
unset($linee_backup[$num1]);

# Keep alive
if (substr($num1,-3) == "000") http_keep_alive("&nbsp;");

if ($linea == "</backup>" and $leggendo_righe != "SI") $leggendo_backup = "NO";

if ($leggendo_backup == "SI") {

if ($linea == "</database>" and $leggendo_righe != "SI") $leggendo_database = "NO";

# restore del database
if ($leggendo_database == "SI") {

if ($linea == "</tabella>" and $leggendo_righe != "SI") $leggendo_tabella = "NO";

if ($leggendo_tabella == "SI") {

if (substr($linea,0,13) == "<nometabella>") {
$nome_tabella = substr($linea,13);
$nome_tabella = substr($nome_tabella,0,-14);
} # fine if (substr($linea,0,13) == "<nometabella>")

if ($linea == "</colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne == "NO";
if ($nome_tabella == "clienti") esegui_query2("create table ".$N_PHPR_TAB_PRE."clienti (idclienti integer primary key, cognome varchar(70) not null, nome varchar(70), soprannome varchar(70), sesso char, titolo varchar(30), lingua varchar(14), datanascita date, cittanascita varchar(70), regionenascita varchar(70), nazionenascita varchar(70), documento varchar(70), scadenzadoc date, tipodoc varchar(70), cittadoc varchar(70), regionedoc varchar(70), nazionedoc  varchar(70), nazionalita varchar(70), nazione varchar(70), regione varchar(70), citta varchar(70), via varchar(70), numcivico varchar(30), cap varchar(30), telefono varchar(50), telefono2 varchar(50), telefono3 varchar(50), fax varchar(50), email text, cod_fiscale varchar(50), partita_iva varchar(50), commento text, max_num_ordine integer, idclienti_compagni text, doc_inviati text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "relclienti") {
esegui_query2("create table ".$N_PHPR_TAB_PRE."relclienti (idclienti integer, numero integer, tipo varchar(12), testo1 text, testo2 text, testo3 text, testo4 text, testo5 text, testo6 text, testo7 text, testo8 text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
crea_indice2($N_PHPR_TAB_PRE.$nome_tabella,"idclienti",$N_PHPR_TAB_PRE."iidp".$nome_tabella,$N_PHPR_DB_TYPE);
} # fine if ($nome_tabella == "relclienti")
if ($nome_tabella == "anni") esegui_query2("create table ".$N_PHPR_TAB_PRE."anni (idanni integer primary key, tipo_periodi text)",$N_PHPR_DB_TYPE);
if ($nome_tabella == "versioni") esegui_query2("create table ".$N_PHPR_TAB_PRE."versioni (idversioni integer primary key, num_versione float4)",$N_PHPR_DB_TYPE);
if ($nome_tabella == "nazioni") esegui_query2("create table ".$N_PHPR_TAB_PRE."nazioni (idnazioni integer primary key, nome_nazione varchar(70), codice_nazione varchar(50), codice2_nazione varchar(50), codice3_nazione varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "regioni") esegui_query2("create table ".$N_PHPR_TAB_PRE."regioni (idregioni integer primary key, nome_regione varchar(70), codice_regione varchar(50), codice2_regione varchar(50), codice3_regione varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "citta") esegui_query2("create table ".$N_PHPR_TAB_PRE."citta (idcitta integer primary key, nome_citta varchar(70), codice_citta varchar(50), codice2_citta varchar(50), codice3_citta varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "documentiid") esegui_query2("create table ".$N_PHPR_TAB_PRE."documentiid (iddocumentiid integer primary key, nome_documentoid varchar(70), codice_documentoid varchar(50), codice2_documentoid varchar(50), codice3_documentoid varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "parentele") esegui_query2("create table ".$N_PHPR_TAB_PRE."parentele (idparentele integer primary key, nome_parentela varchar(70), codice_parentela varchar(50), codice2_parentela varchar(50), codice3_parentela varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "personalizza") esegui_query2("create table ".$N_PHPR_TAB_PRE."personalizza (idpersonalizza varchar(50) not null, idutente integer, valpersonalizza text, valpersonalizza_num integer)",$N_PHPR_DB_TYPE);
if ($nome_tabella == "appartamenti")esegui_query2("create table ".$N_PHPR_TAB_PRE."appartamenti (idappartamenti varchar(100) primary key, numpiano text, maxoccupanti integer, numcasa text, app_vicini text, priorita integer, priorita2 integer, letto varchar(1), commento text )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "utenti") esegui_query2("create table ".$N_PHPR_TAB_PRE."utenti (idutenti integer primary key, nome_utente text, password text, salt text, tipo_pass varchar(1), datainserimento $DATETIME, hostinserimento varchar(50) )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "gruppi") esegui_query2("create table ".$N_PHPR_TAB_PRE."gruppi (idgruppi integer primary key, nome_gruppo text )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "privilegi") esegui_query2("create table ".$N_PHPR_TAB_PRE."privilegi (idutente integer, anno integer, regole1_consentite text, tariffe_consentite text, costi_agg_consentiti text, contratti_consentiti text, casse_consentite text, cassa_pagamenti varchar(70), priv_ins_prenota varchar(20), priv_mod_prenota varchar(35), priv_mod_pers varchar(15), priv_ins_clienti varchar(5), prefisso_clienti text, priv_ins_costi varchar(10), priv_vedi_tab varchar(30), priv_ins_tariffe varchar(10), priv_ins_regole varchar(10), priv_messaggi varchar(10), priv_inventario varchar(10) )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "relutenti") {
esegui_query2("create table ".$N_PHPR_TAB_PRE."relutenti (idutente integer not null, idnazione integer, idregione integer, idcitta integer, iddocumentoid integer, idparentela integer, idsup integer, predef integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
crea_indice2($N_PHPR_TAB_PRE.$nome_tabella,"idutente",$N_PHPR_TAB_PRE."iidp".$nome_tabella,$N_PHPR_DB_TYPE);
} # fine if ($nome_tabella == "relutenti")
if ($nome_tabella == "relgruppi") {
esegui_query2("create table ".$N_PHPR_TAB_PRE."relgruppi (idutente integer not null, idgruppo integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
crea_indice2($N_PHPR_TAB_PRE.$nome_tabella,"idutente",$N_PHPR_TAB_PRE."iidp".$nome_tabella,$N_PHPR_DB_TYPE);
} # fine if ($nome_tabella == "relgruppi")
if ($nome_tabella == "sessioni") esegui_query2("create table ".$N_PHPR_TAB_PRE."sessioni (idsessioni varchar(30) primary key, idutente integer, indirizzo_ip text, tipo_conn varchar(12), user_agent text, ultimo_accesso $DATETIME )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "transazioni")esegui_query2("create table ".$N_PHPR_TAB_PRE."transazioni (idtransazioni varchar(30) primary key, idsessione varchar(30), tipo_transazione varchar(5), anno integer, spostamenti text, dati_transazione1 text, dati_transazione2 text, dati_transazione3 text, dati_transazione4 text, dati_transazione5 text, dati_transazione6 text, dati_transazione7 text, dati_transazione8 text, dati_transazione9 text, dati_transazione10 text, dati_transazione11 text, dati_transazione12 text, dati_transazione13 text, dati_transazione14 text, dati_transazione15 text, dati_transazione16 text, dati_transazione17 text, dati_transazione18 text, dati_transazione19 text, dati_transazione20 text, ultimo_accesso $DATETIME)",$N_PHPR_DB_TYPE);
if ($nome_tabella == "transazioniweb")esegui_query2("create table ".$N_PHPR_TAB_PRE."transazioniweb (idtransazioni varchar(30) primary key, idsessione varchar(30), tipo_transazione varchar(5), anno integer, spostamenti text, dati_transazione1 text, dati_transazione2 text, dati_transazione3 text, dati_transazione4 text, dati_transazione5 text, dati_transazione6 text, dati_transazione7 text, dati_transazione8 text, dati_transazione9 text, dati_transazione10 text, dati_transazione11 text, dati_transazione12 text, dati_transazione13 text, dati_transazione14 text, dati_transazione15 text, dati_transazione16 text, dati_transazione17 text, dati_transazione18 text, dati_transazione19 text, dati_transazione20 text, ultimo_accesso $DATETIME)",$N_PHPR_DB_TYPE);
if ($nome_tabella == "descrizioni") esegui_query2("create table ".$N_PHPR_TAB_PRE."descrizioni (nome text not null, tipo varchar(16), lingua varchar(3), numero integer, testo $MEDIUMTEXT )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "beniinventario") esegui_query2("create table ".$N_PHPR_TAB_PRE."beniinventario (idbeniinventario integer primary key, nome_bene varchar(70), codice_bene varchar(50), descrizione_bene text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "magazzini") esegui_query2("create table ".$N_PHPR_TAB_PRE."magazzini (idmagazzini integer primary key, nome_magazzino varchar(70), codice_magazzino varchar(50), descrizione_magazzino text, numpiano text, numcasa text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "relinventario") {
esegui_query2("create table ".$N_PHPR_TAB_PRE."relinventario (idbeneinventario integer not null, idappartamento varchar(100), idmagazzino integer, quantita integer, quantita_min_predef integer, richiesto_checkin varchar(2), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
crea_indice2($N_PHPR_TAB_PRE.$nome_tabella,"idbeneinventario",$N_PHPR_TAB_PRE."iidp".$nome_tabella,$N_PHPR_DB_TYPE);
} # fine if ($nome_tabella == "relinventario")
if ($nome_tabella == "casse") esegui_query2("create table ".$N_PHPR_TAB_PRE."casse (idcasse integer primary key, nome_cassa varchar(70), stato varchar(8), codice_cassa varchar(50), descrizione_cassa text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "contratti") esegui_query2("create table ".$N_PHPR_TAB_PRE."contratti (numero integer, tipo varchar(8), testo $MEDIUMTEXT )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "cache") esegui_query2("create table ".$N_PHPR_TAB_PRE."cache (numero integer, tipo varchar(8), testo $MEDIUMTEXT, data_modifica $DATETIME, datainserimento $DATETIME )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "interconnessioni") esegui_query2("create table ".$N_PHPR_TAB_PRE."interconnessioni (idlocale integer, idremoto1 text, idremoto2 text, tipoid varchar(12), nome_ic varchar(24), anno integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
if ($nome_tabella == "messaggi") esegui_query2("create table ".$N_PHPR_TAB_PRE."messaggi (idmessaggi integer primary key, tipo_messaggio varchar(8), stato varchar(8), idutenti text, idutenti_visto text, datavisione $DATETIME, mittente text, testo text, dati_messaggio1 text, dati_messaggio2 text, dati_messaggio3 text, dati_messaggio4 text, dati_messaggio5 text, dati_messaggio6 text, dati_messaggio7 text, dati_messaggio8 text, dati_messaggio9 text, dati_messaggio10 text, dati_messaggio11 text, dati_messaggio12 text, dati_messaggio13 text, dati_messaggio14 text, dati_messaggio15 text, dati_messaggio16 text, dati_messaggio17 text, dati_messaggio18 text, dati_messaggio19 text, dati_messaggio20 text, datainserimento $DATETIME)",$N_PHPR_DB_TYPE);
if (substr($nome_tabella,0,7) == "prenota") esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idprenota integer primary key, idclienti integer, idappartamenti  varchar(100), iddatainizio integer, iddatafine integer, assegnazioneapp varchar(4), app_assegnabili text, num_persone integer, idprenota_compagna text, tariffa text, tariffesettimanali text, incompatibilita text, sconto float8, tariffa_tot float8, caparra float8, commissioni float8, tasseperc float4, pagato float8, metodo_pagamento text, origine varchar(70), codice varchar(10), commento text, conferma varchar(4), checkin $DATETIME, checkout $DATETIME, id_anni_prec text, datainserimento $DATETIME, hostinserimento varchar(50), data_modifica $DATETIME, utente_inserimento integer )",$N_PHPR_DB_TYPE);
if (substr($nome_tabella,0,12) == "costiprenota") {
esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idcostiprenota integer primary key, idprenota integer, tipo varchar(2), nome varchar(40), valore float8, valore_perc float8, arrotonda float4, tasseperc float4, associasett varchar(1), settimane text, moltiplica text, categoria text, letto varchar(1), numlimite integer, idntariffe integer, variazione varchar(10), varmoltiplica text, varnumsett varchar(20), varperiodipermessi text, varbeniinv text, varappincompatibili text, vartariffeassociate varchar(10), vartariffeincomp text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer)",$N_PHPR_DB_TYPE);
crea_indice2($N_PHPR_TAB_PRE.$nome_tabella,"idprenota",$N_PHPR_TAB_PRE."iidp".$nome_tabella,$N_PHPR_DB_TYPE);
} # fine if (substr($nome_tabella,0,12) == "costiprenota")
else if (substr($nome_tabella,0,5) == "costi") esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idcosti integer unique,nome_costo text, val_costo float8, tipo_costo text, nome_cassa varchar(70), persona_costo text, provenienza_costo text, metodo_pagamento text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer)",$N_PHPR_DB_TYPE);
if (substr($nome_tabella,0,15) == "rclientiprenota") {
esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idprenota integer, idclienti integer, num_ordine integer, parentela varchar(70), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )",$N_PHPR_DB_TYPE);
crea_indice2($N_PHPR_TAB_PRE.$nome_tabella,"idprenota",$N_PHPR_TAB_PRE."iidp".$nome_tabella,$N_PHPR_DB_TYPE);
} # fine if (substr($nome_tabella,0,15) == "rclientiprenota")
if (substr($nome_tabella,0,6) == "regole") esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idregole integer, app_agenzia varchar(100), tariffa_chiusa text, tariffa_per_app text, tariffa_per_utente text, tariffa_per_persone text, tariffa_commissioni integer, iddatainizio integer, iddatafine integer, motivazione text, motivazione2 text, motivazione3 text )",$N_PHPR_DB_TYPE);
if (substr($nome_tabella,0,5) == "soldi") esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idsoldi integer unique, motivazione text, id_pagamento text, metodo_pagamento text, saldo_prenota float8, saldo_cassa float8, soldi_prima float8, data_inserimento $DATETIME, utente_inserimento integer )",$N_PHPR_DB_TYPE);
if (substr($nome_tabella,0,7) == "periodi") esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idperiodi integer primary key, datainizio date not null, datafine date$lista_tariffe)",$N_PHPR_DB_TYPE);
if (substr($nome_tabella,0,8) == "ntariffe") esegui_query2("create table ".$N_PHPR_TAB_PRE."$nome_tabella (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, valore_perc_ca float8, arrotonda_ca float4, tasseperc_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca text, periodipermessi_ca text, beniinv_ca text, appincompatibili_ca text, variazione_ca varchar(20), mostra_ca varchar(10), categoria_ca text, letto_ca varchar(1), numlimite_ca integer, regoleassegna_ca varchar(30), utente_inserimento integer$lista_tariffe)",$N_PHPR_DB_TYPE);
} # fine if ($linea == "</colonnetabella>" and $leggendo_righe != "SI")
if ($leggendo_colonne == "SI") {
if (substr($linea,0,13) == "<nomecolonna>") {
$num_colonne++;
$nome_colonna[$num_colonne] = substr($linea,13);
$nome_colonna[$num_colonne] = substr($nome_colonna[$num_colonne],0,-14);
if (substr($nome_tabella,0,7) == "periodi" and substr($nome_colonna[$num_colonne],0,7) == "tariffa") $lista_tariffe .= ", ".$nome_colonna[$num_colonne]." float8";
if (substr($nome_tabella,0,8) == "ntariffe" and substr($nome_colonna[$num_colonne],0,7) == "tariffa") $lista_tariffe .= ", ".$nome_colonna[$num_colonne]." text";
} # fine if (substr($linea,0,13) == "<nomecolonna>")
if (substr($linea,0,13) == "<tipocolonna>") {
$tipo_colonna[$num_colonne] = substr($linea,13);
$tipo_colonna[$num_colonne] = substr($tipo_colonna[$num_colonne],0,-14);
} # fine if (substr($linea,0,13) == "<tipocolonna>")
} # fine if ($leggendo_colonne == "SI")
if ($linea == "<colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne = "SI";
$num_colonne = 0;
$lista_tariffe = "";
} # fine if ($linea == "<colonnetabella>" and $leggendo_righe != "SI")

if ($linea == "</righetabella>") $leggendo_righe = "NO";
if ($leggendo_righe == "SI") {
if (substr($linea,0,6) == "<riga>") $riga = substr($linea,11);
else {
$riga .= "
".$linea;
} # fine else if (substr($linea,0,6) == "<riga>")
if (substr($linea,-7) == "</riga>") {
$riga = substr($riga,0,-13);
$riga = explode("</cmp><cmp>",$riga);
$lista_valori = "";
$query = "insert into ".$N_PHPR_TAB_PRE."$nome_tabella (";
for ($num2 = 0 ; $num2 < count($riga) ; $num2++) {
aggslashdb2($riga[$num2],$N_PHPR_DB_TYPE);
if ($riga[$num2] != "") $query .= $nome_colonna[($num2+1)].",";
} # fine for $num2
$query = substr($query,0,-1).") values (";
for ($num2 = 0 ; $num2 < count($riga) ; $num2++) {
if ($riga[$num2] != "") {
$riga[$num2] = str_replace("</righetabella@%&@>","</righetabella>",$riga[$num2]);
$riga[$num2] = str_replace("</riga@%&@>","</riga>",$riga[$num2]);
$riga[$num2] = str_replace("<riga@%&@>","<riga>",$riga[$num2]);
$riga[$num2] = str_replace("</cmp@%&@>","</cmp>",$riga[$num2]);
$riga[$num2] = str_replace("@%&@@%&@","@%&@",$riga[$num2]);
$query .= "'".$riga[$num2]."',";
} # fine if ($riga[$num2] != "")
} # fine for $num2
$query = substr($query,0,-1).")";
#echo $query<br>";
esegui_query3($query,$N_PHPR_DB_TYPE);
} # fine if (substr($linea,-7) == "</riga>")
} # fine if ($leggendo_righe == "SI")
if ($linea == "<righetabella>") {
$leggendo_righe = "SI";
$linee_inserite_in_tabella[$nome_tabella] = 0;
} # fine if ($linea == "<righetabella>")

} # fine if ($leggendo_tabella == "SI")

if ($linea == "<tabella>" and $leggendo_righe != "SI") $leggendo_tabella = "SI";

} # fine if ($leggendo_database == "SI")
else {

# restore dei files
if ($linea == "</file>") $leggendo_file = "NO";

if ($leggendo_file == "SI") {

if ($nome_file) {
if ($leggendo_contenuto == "SI") {
if (substr($linea,-12) == "</contenuto>") {
$nome_file = "";
$leggendo_contenuto = "NO";
$linea = substr($linea,0,-12);
fwrite($fileaperto,$linea);
fclose($fileaperto);
} # fine if (substr($linea,-12) == "</contenuto>")
else {
fwrite($fileaperto,$linea."
");
} # fine else if (substr($linea,-12) == "</contenuto>")
} # fine if ($leggendo_contenuto == "SI")

if ($linea == "<contenuto>") $leggendo_contenuto = "SI";
} # fine if ($nome_file)

if (substr($linea,0,10) == "<nomefile>") {
$nome_file = substr($linea,10);
$nome_file = substr($nome_file,0,-11);
if (substr($nome_file,0,7) == "./dati/") $nome_file = C_DATI_PATH.substr($nome_file,6);
if ($nome_file) $fileaperto = fopen("$nome_file","w+");
} # fine if (substr($linea,0,10) == "<nomefile>")

} # fine if ($leggendo_file == "SI")

if ($linea == "<file>" and $leggendo_modello != "SI") $leggendo_file = "SI";

} # fine else if ($leggendo_database == "SI")

if ($linea == "<database>" and $leggendo_modello != "SI") $leggendo_database = "SI";


# restore dei modelli
if ($linea == "</modello>") {
$leggendo_modello = "NO";
$leggendo_contenuto_mod = "NO";
$nome_modello = "";
} # fine if ($linea == "</modello>")

if ($leggendo_modello == "SI") {

if ($nome_modello) {
if ($leggendo_contenuto_mod == "SI") {
if (substr($linea,-12) == "</contenuto>") {
$leggendo_contenuto_mod = "NO";
$linea = substr($linea,0,-12);
$contenuto_mod[$dir_modello][$nome_modello] .= $linea."
";
$nome_modello = "";
} # fine if (substr($linea,-12) == "</contenuto>")
else {
$contenuto_mod[$dir_modello][$nome_modello] .= $linea."
";
} # fine else if (substr($linea,-12) == "</contenuto>")
} # fine if ($leggendo_contenuto_mod == "SI")

if ($linea == "<contenuto>") $leggendo_contenuto_mod = "SI";
} # fine if ($nome_modello)

else {

if (substr($linea,0,12) == "<dirmodello>") {
$dir_modello = substr($linea,12);
$dir_modello = substr($dir_modello,0,-13);
if (substr($dir_modello,-1) == "/") $dir_modello = substr($dir_modello,0,-1);
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
$c_cartella_crea_mod = C_CARTELLA_CREA_MODELLI;
if (substr($c_cartella_crea_mod,-1) == "/") $c_cartella_crea_mod = substr($c_cartella_crea_mod,0,-1);
if (substr($dir_modello."/",0,strlen($c_cartella_crea_mod."/")) != $c_cartella_crea_mod."/") $dir_modello = "";
if (str_replace("..","",$dir_modello) != $dir_modello) $dir_modello = "";
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
} # fine if (substr($linea,0,12) == "<dirmodello>")

if (substr($linea,0,13) == "<nomemodello>" and $dir_modello) {
$nome_modello = substr($linea,13);
$nome_modello = substr($nome_modello,0,-14);
if (substr($nome_modello,-4) != ".php") $nome_modello = "";
if (str_replace(".php","",substr($nome_modello,0,-4)) != substr($nome_modello,0,-4)) $nome_modello = "";
} # fine if (substr($linea,0,13) == "<nomemodello>" and $dir_modello)

} # fine else if ($nome_modello)

} # fine if ($leggendo_modello == "SI")

if ($linea == "<modello>" and $leggendo_database != "SI" and $leggendo_file != "SI") $leggendo_modello = "SI";



} # fine if ($leggendo_backup == "SI")

if ($linea == "<backup>") $leggendo_backup = "SI";

} # fine for $num1
} # fine while (!feof($fbackup))
fclose ($fbackup);
echo "<br>";
} # fine if ($fbackup)



# Se si sono mantenuti gli anni non presenti nel backup, aggiorno i loro dati ed importo eventuali clienti mancanti
if ($mantieni_anni and @is_array($anno_mantieni)) {

for ($num1 = 0 ; $num1 < $num_anni_vecchi ; $num1++) {
$anno_vecchio = risul_query($anni_vecchi,$num1,'idanni');
if ($anno_mantieni[$anno_vecchio]) {
esegui_query("insert into ".$PHPR_TAB_PRE."anni (idanni,tipo_periodi) values ('$anno_vecchio','".risul_query($anni_vecchi,$num1,'tipo_periodi')."') ");
} # fine if ($anno_mantieni[$anno_vecchio])
} # fine for $num1

unset($id_utenti_vecchi);
$id_utenti_vecchi[1] = 1;
for ($num1 = 0 ; $num1 < numlin_query($utenti_vecchi) ; $num1++) {
$idutente_vecchio = risul_query($utenti_vecchi,$num1,'idutenti');
if ($idutente_vecchio != 1) {
$datainserimento = risul_query($utenti_vecchi,$num1,'datainserimento');
if (!$datainserimento) $cond_datainserimento = "datainserimento is NULL";
else  $cond_datainserimento = "datainserimento = '".aggslashdb($datainserimento)."'";
$hostinserimento = risul_query($utenti_vecchi,$num1,'hostinserimento');
if (!strcmp($hostinserimento,"")) $cond_hostinserimento = "(hostinserimento is NULL or hostinserimento = '')";
else  $cond_hostinserimento = "hostinserimento = '".aggslashdb($hostinserimento)."'";
$utente_esistente = esegui_query("select idutenti from ".$PHPR_TAB_PRE."utenti where idutenti = '$idutente_vecchio' and $cond_datainserimento and $cond_hostinserimento ");
if (!numlin_query($utente_esistente)) {
reset($anno_mantieni);
foreach ($anno_mantieni as $anno_vecchio => $val) {
esegui_query("update ".$PHPR_TAB_PRE."prenota$anno_vecchio set utente_inserimento = '1' where utente_inserimento = '$idutente_vecchio' ");
esegui_query("update ".$PHPR_TAB_PRE."costi$anno_vecchio set utente_inserimento = '1' where utente_inserimento = '$idutente_vecchio' ");
esegui_query("update ".$PHPR_TAB_PRE."costiprenota$anno_vecchio set utente_inserimento = '1' where utente_inserimento = '$idutente_vecchio' ");
esegui_query("update ".$PHPR_TAB_PRE."rclientiprenota$anno_vecchio set utente_inserimento = '1' where utente_inserimento = '$idutente_vecchio' ");
esegui_query("update ".$PHPR_TAB_PRE."soldi$anno_vecchio set utente_inserimento = '1' where utente_inserimento = '$idutente_vecchio' ");
if (@is_file(C_DATI_PATH."/selectperiodi$anno_vecchio.$idutente_vecchio.php")) unlink(C_DATI_PATH."/selectperiodi$anno_vecchio.$idutente_vecchio.php");
if (@is_file(C_DATI_PATH."/selperiodimenu$anno_vecchio.$idutente_vecchio.php")) unlink(C_DATI_PATH."/selperiodimenu$anno_vecchio.$idutente_vecchio.php");
} # fine foreach ($anno_mantieni as $anno_vecchio => $val)
$id_utenti_vecchi[$idutente_vecchio] = 1;
} # fine if (!numlin_query($utente_esistente))
else $id_utenti_vecchi[$idutente_vecchio] = $idutente_vecchio;
} # fine if ($idutente_vecchio != 1)
} # fine for $num1

unset($id_clienti_vecchi);
unset($id_clienti_inseriti);
$max_clienti = esegui_query("select max(idclienti) from ".$PHPR_TAB_PRE."clienti");
$max_clienti = risul_query($max_clienti,0,0);
if ($max_clienti_vecchi > $max_clienti) $max_clienti = $max_clienti_vecchi;
for ($num1 = 0 ; $num1 < numlin_query($clienti_vecchi) ; $num1++) {
$idcliente_vecchio = risul_query($clienti_vecchi,$num1,'idclienti');
$cliente_presente = 0;
reset($anno_mantieni);
foreach ($anno_mantieni as $anno_vecchio => $val) {
$idcliente_presente = esegui_query("select idclienti from ".$PHPR_TAB_PRE."prenota$anno_vecchio where idclienti = '$idcliente_vecchio' ");
$idcliente_presente2 = esegui_query("select idclienti from ".$PHPR_TAB_PRE."rclientiprenota$anno_vecchio where idclienti = '$idcliente_vecchio' ");
$idcliente_presente3 = esegui_query("select idsoldi from ".$PHPR_TAB_PRE."soldi$anno_vecchio where motivazione $LIKE '$idcliente_vecchio;%' ");
if (numlin_query($idcliente_presente) or numlin_query($idcliente_presente2) or numlin_query($idcliente_presente3)) {
$cliente_presente = 1;
break;
} # fine if (numlin_query($idcliente_presente) or numlin_query($idcliente_presente2) or...
} # fine foreach ($anno_mantieni as $anno_vecchio => $val)
if ($cliente_presente) {
$datainserimento = risul_query($clienti_vecchi,$num1,'datainserimento');
if (!$datainserimento) $cond_datainserimento = "datainserimento is NULL";
else  $cond_datainserimento = "datainserimento = '".aggslashdb($datainserimento)."'";
$hostinserimento = risul_query($clienti_vecchi,$num1,'hostinserimento');
if (!strcmp($hostinserimento,"")) $cond_hostinserimento = "(hostinserimento is NULL or hostinserimento = '')";
else  $cond_hostinserimento = "hostinserimento = '".aggslashdb($hostinserimento)."'";
$cliente_esistente = esegui_query("select idclienti from ".$PHPR_TAB_PRE."clienti where idclienti = '$idcliente_vecchio' and $cond_datainserimento and $cond_hostinserimento ");
if (!numlin_query($cliente_esistente)) {
$cliente_esistente = esegui_query("select idclienti from ".$PHPR_TAB_PRE."clienti where cognome = '".aggslashdb(risul_query($clienti_vecchi,$num1,'cognome'))."' and $cond_datainserimento and $cond_hostinserimento ");
if (numlin_query($cliente_esistente) == 1) $id_nuovo = risul_query($cliente_esistente,0,'idclienti');
else {
$id_esistente = esegui_query("select idclienti from ".$PHPR_TAB_PRE."clienti where idclienti = '$idcliente_vecchio' ");
if (numlin_query($id_esistente)) {
$max_clienti++;
$id_nuovo = $max_clienti;
} # fine if (numlin_query($id_esistente))
else $id_nuovo = $idcliente_vecchio;
esegui_query("insert into ".$PHPR_TAB_PRE."clienti (idclienti,cognome,nome,soprannome,sesso,titolo,lingua,cittanascita,regionenascita,nazionenascita,documento,tipodoc,cittadoc,regionedoc,nazionedoc,nazionalita,nazione,regione,citta,via,numcivico,cap,telefono,telefono2,telefono3,fax,email,cod_fiscale,partita_iva,commento,max_num_ordine,idclienti_compagni,doc_inviati,hostinserimento,utente_inserimento) values ('$id_nuovo',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'cognome'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'nome'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'soprannome'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'sesso'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'titolo'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'lingua'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'cittanascita'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'regionenascita'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'nazionenascita'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'documento'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'tipodoc'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'cittadoc'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'regionedoc'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'nazionedoc'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'nazionalita'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'nazione'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'regione'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'citta'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'via'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'numcivico'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'cap'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'telefono'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'telefono2'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'telefono3'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'fax'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'email'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'cod_fiscale'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'partita_iva'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'commento'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'max_num_ordine'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'idclienti_compagni'))."',
'".aggslashdb(risul_query($clienti_vecchi,$num1,'doc_inviati'))."',
'".aggslashdb($hostinserimento)."',
'".$id_utenti_vecchi[risul_query($clienti_vecchi,$num1,'utente_inserimento')]."') ");
$datanascita = risul_query($clienti_vecchi,$num1,'datanascita');
if ($datanascita) esegui_query("update ".$PHPR_TAB_PRE."clienti set datanascita = '".aggslashdb($datanascita)."' where idclienti = '$id_nuovo' ");
$scadenzadoc = risul_query($clienti_vecchi,$num1,'scadenzadoc');
if ($scadenzadoc) esegui_query("update ".$PHPR_TAB_PRE."clienti set scadenzadoc = '".aggslashdb($scadenzadoc)."' where idclienti = '$id_nuovo' ");
if ($datainserimento) esegui_query("update ".$PHPR_TAB_PRE."clienti set datainserimento = '".aggslashdb($datainserimento)."' where idclienti = '$id_nuovo' ");
$id_clienti_inseriti[$idcliente_vecchio] = $id_nuovo;
} # fine else if (numlin_query($cliente_esistente) == 1)
$id_clienti_vecchi[$idcliente_vecchio] = $id_nuovo;
if ($id_nuovo != $idcliente_vecchio) {
reset($anno_mantieni);
foreach ($anno_mantieni as $anno_vecchio => $val) {
esegui_query("update ".$PHPR_TAB_PRE."prenota$anno_vecchio set idclienti = '$id_nuovo' where idclienti = '$idcliente_vecchio' ");
esegui_query("update ".$PHPR_TAB_PRE."rclientiprenota$anno_vecchio set idclienti = '$id_nuovo' where idclienti = '$idcliente_vecchio' ");
$storia_soldi = esegui_query("select * from ".$PHPR_TAB_PRE."soldi$anno_vecchio where motivazione $LIKE '$idcliente_vecchio;%' ");
for ($num2 = 0 ; $num2 < numlin_query($storia_soldi) ; $num2++) {
$idsoldi = risul_query($storia_soldi,$num2,'idsoldi');
$motivazione = risul_query($storia_soldi,$num2,'motivazione');
$motivazione = $id_nuovo.substr($motivazione,strlen($idcliente_vecchio));
esegui_query("update ".$PHPR_TAB_PRE."soldi$anno_vecchio set motivazione = '$motivazione' where idsoldi = '$idsoldi' ");
} # fine for $num2
} # fine foreach ($anno_mantieni as $anno_vecchio => $val)
} # fine if ($id_nuovo != $idcliente_vecchio)
} # fine if (!numlin_query($cliente_esistente))
else $id_clienti_vecchi[$idcliente_vecchio] = $idcliente_vecchio;
} # fine if ($cliente_presente)
} # fine for $num1
if (@is_array($id_clienti_inseriti)) {
reset($id_clienti_inseriti);
foreach ($id_clienti_inseriti as $idcliente_vecchio => $id_nuovo) {
$n_idclienti_compagni = ",";
$idclienti_compagni = esegui_query("select idclienti_compagni from ".$PHPR_TAB_PRE."clienti where idclienti = '$id_nuovo' ");
$idclienti_compagni = risul_query($idclienti_compagni,0,'idclienti_compagni');
$idclienti_compagni_vett = explode(",",$idclienti_compagni);
for ($num1 = 1 ; $num1 < (count($idclienti_compagni_vett) - 1) ; $num1++) {
if ($id_clienti_vecchi[$idclienti_compagni_vett[$num1]]) $n_idclienti_compagni .= $id_clienti_vecchi[$idclienti_compagni_vett[$num1]].",";
} # fine for $num1
if ($n_idclienti_compagni != $idclienti_compagni) esegui_query("update ".$PHPR_TAB_PRE."clienti set idclienti_compagni = '$n_idclienti_compagni' where idclienti = '$id_nuovo' ");
} # fine foreach ($id_clienti_inseriti as $idcliente_vecchio => $id_nuovo)
} # fine if (@is_array($id_clienti_inseriti))

for ($num1 = 0 ; $num1 < numlin_query($privilegi_vecchi) ; $num1++) {
$anno_priv = risul_query($privilegi_vecchi,$num1,'anno');
if ($anno_mantieni[$anno_priv]) {
$idutente_priv = risul_query($privilegi_vecchi,$num1,'idutente');
if ($id_utenti_vecchi[$idutente_priv] != 1) {
esegui_query("insert into ".$PHPR_TAB_PRE."privilegi (idutente,anno,regole1_consentite,tariffe_consentite,costi_agg_consentiti,contratti_consentiti,priv_ins_prenota,priv_mod_prenota,priv_mod_pers,priv_ins_clienti,prefisso_clienti,priv_ins_costi,priv_vedi_tab,priv_ins_tariffe,priv_ins_regole,priv_messaggi,priv_inventario) values ('$idutente_priv','$anno_priv',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'regole1_consentite'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'tariffe_consentite'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'costi_agg_consentiti'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'contratti_consentiti'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_ins_prenota'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_mod_prenota'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_mod_pers'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_ins_clienti'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'prefisso_clienti'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_ins_costi'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_vedi_tab'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_ins_tariffe'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_ins_regole'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_messaggi'))."',
'".aggslashdb(risul_query($privilegi_vecchi,$num1,'priv_inventario'))."') ");
} # fine if ($id_utenti_vecchi[$idutente_priv] != 1)
} # fine if ($anno_mantieni[$anno_priv])
} # fine for $num1

for ($num1 = 0 ; $num1 < numlin_query($personalizza_vecchi) ; $num1++) {
$idpersonalizza = risul_query($personalizza_vecchi,$num1,'idpersonalizza');
if ($anno_mantieni[str_replace("giorno_vedi_ini_sett","",$idpersonalizza)]) {
$idutente_pers = risul_query($personalizza_vecchi,$num1,'idutente');
if ($id_utenti_vecchi[$idutente_pers] != 1) {
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza,valpersonalizza_num) values ('$idpersonalizza','$idutente_pers',
'".aggslashdb(risul_query($personalizza_vecchi,$num1,'valpersonalizza'))."',
'".aggslashdb(risul_query($personalizza_vecchi,$num1,'valpersonalizza_num'))."') ");
} # fine if ($id_utenti_vecchi[$idutente_pers] != 1)
} # fine if ($anno_mantieni[str_replace("giorno_vedi_ini_sett","",$idpersonalizza)])
} # fine for $num1

} # fine if ($mantieni_anni and @is_array($anno_mantieni))


if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "") {
esegui_query("delete from ".$PHPR_TAB_PRE."personalizza where idpersonalizza = 'priv_key_cc' or idpersonalizza = 'cert_cc' or idpersonalizza = 'gest_cvc' ");
esegui_query("delete from ".$PHPR_TAB_PRE."relclienti where tipo = 'cc' ");
} # fine if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")


if ($silenzio != "SI") echo "".mex("Database creato",$pag)."<br>";
$fatto = "SI";
if (($nuovo_db != "SI" or $database_esistente == "SI") and $character_set_db and ($character_set_db != "utf8" or $collation_db != "utf8_general_ci")) esegui_query2("alter database $N_PHPR_DB_NAME default character set '$character_set_db' collate '$collation_db'",$N_PHPR_DB_TYPE);


if (@is_array($contenuto_mod) and ($N_PHPR_DB_TYPE == $PHPR_DB_TYPE)) {
global $prima_parte_html,$lingua_mex;
echo "<br>";
$lingua_mex_orig = $lingua_mex;
if ($silenzio != "SI") $silenzio_mod = "SI";
else $silenzio_mod = "totale";
global $anno_modello_presente,$num_periodi_date,$modello_esistente,$cambia_frasi,$lingua_modello,$percorso_cartella_modello,$nome_file;
include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];
$pag_orig = $pag;
$pag = "crea_modelli.php";
function assegna_var_conn_mod ($N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE) {
global $M_PHPR_DB_TYPE,$M_PHPR_DB_NAME,$M_PHPR_DB_HOST,$M_PHPR_DB_PORT,$M_PHPR_DB_USER,$M_PHPR_DB_PASS,$M_PHPR_LOAD_EXT,$M_PHPR_TAB_PRE;
$M_PHPR_DB_TYPE = $N_PHPR_DB_TYPE;
$M_PHPR_DB_NAME = $N_PHPR_DB_NAME;
$M_PHPR_DB_HOST = $N_PHPR_DB_HOST;
$M_PHPR_DB_PORT = $N_PHPR_DB_PORT;
$M_PHPR_DB_USER = $N_PHPR_DB_USER;
$M_PHPR_DB_PASS = $N_PHPR_DB_PASS;
$M_PHPR_LOAD_EXT = $N_PHPR_LOAD_EXT;
$M_PHPR_TAB_PRE = $N_PHPR_TAB_PRE;
} # fine function assegna_var_conn_mod
$PHPR_TAB_PRE = $N_PHPR_TAB_PRE;
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
include("./includes/funzioni_costi_agg.php");
include("./includes/templates/funzioni_modelli.php");
$modello_esistente = "SI";
$cambia_frasi = "NO";
include("./includes/templates/frasi_mod_disp.php");
include("./includes/templates/funzioni_mod_disp.php");
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if ($contenuto_mod[$percorso_cartella_modello]['mdl_disponibilita.php']) {
$lingua_modello = "ita";
$nome_file = mex2("mdl_disponibilita",$pag,$lingua_modello).".php";
$nome_file_tmp = substr($nome_file,0,-4).".tmp";
$num_periodi_date = "";
$anno_modello = "";
scrivi_file("<?php exit(); ?>
".$contenuto_mod[$percorso_cartella_modello]["$nome_file"],"$percorso_cartella_modello/$nome_file_tmp");
recupera_var_modello_disponibilita($nome_file_tmp,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$prima_parte_html = str_replace("<?php exit(); ?>
","",$prima_parte_html);
if (substr($prima_parte_html,0,70) == "<?php if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed']) { ?>") $prima_parte_html = substr($prima_parte_html,70);
if (substr($prima_parte_html,0,74) == "<?php if (!@\$framed and !@\$_GET[\\'framed\\'] and !@\$_POST[\\'framed\\']) { ?>") $prima_parte_html = substr($prima_parte_html,74);
unlink("$percorso_cartella_modello/$nome_file_tmp");
assegna_var_conn_mod($N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($contenuto_mod[$percorso_cartella_modello]["mdl_disponibilita.php"])
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
$nome_file_tmp = substr($nome_file,0,-4).".tmp";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if ($contenuto_mod[$percorso_cartella_modello][$nome_file]) {
$lingua_modello = $ini_lingua;
$num_periodi_date = "";
$anno_modello = "";
scrivi_file("<?php exit(); ?>
".$contenuto_mod[$percorso_cartella_modello][$nome_file],"$percorso_cartella_modello/$nome_file_tmp");
recupera_var_modello_disponibilita($nome_file_tmp,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$prima_parte_html = str_replace("<?php exit(); ?>
","",$prima_parte_html);
if (substr($prima_parte_html,0,70) == "<?php if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed']) { ?>") $prima_parte_html = substr($prima_parte_html,70);
if (substr($prima_parte_html,0,74) == "<?php if (!@\$framed and !@\$_GET[\\'framed\\'] and !@\$_POST[\\'framed\\']) { ?>") $prima_parte_html = substr($prima_parte_html,74);
unlink("$percorso_cartella_modello/$nome_file_tmp");
assegna_var_conn_mod($N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($contenuto_mod[$percorso_cartella_modello][$nome_file])
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
include("./includes/templates/$modello_ext/phrases.php");
include("./includes/templates/$modello_ext/functions.php");
$funz_recupera_var_modello = "recupera_var_modello_".$modello_ext;
$funz_crea_modello = "crea_modello_".$modello_ext;
$funz_mext = "mext_".$modello_ext;
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name["en"];
$nome_file_tmp = substr($nome_file,0,-4).".tmp";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if ($contenuto_mod[$percorso_cartella_modello][$nome_file]) {
$lingua_modello = "ita";
$num_periodi_date = "";
$anno_modello = "";
scrivi_file("<?php exit(); ?>
".$contenuto_mod[$percorso_cartella_modello][$nome_file],"$percorso_cartella_modello/$nome_file_tmp");
$funz_recupera_var_modello($nome_file_tmp,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$prima_parte_html = str_replace("<?php exit(); ?>
","",$prima_parte_html);
if (substr($prima_parte_html,0,70) == "<?php if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed']) { ?>") $prima_parte_html = substr($prima_parte_html,70);
if (substr($prima_parte_html,0,74) == "<?php if (!@\$framed and !@\$_GET[\\'framed\\'] and !@\$_POST[\\'framed\\']) { ?>") $prima_parte_html = substr($prima_parte_html,74);
unlink("$percorso_cartella_modello/$nome_file_tmp");
assegna_var_conn_mod($N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($contenuto_mod[$percorso_cartella_modello][$nome_file])
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name['en'];
$nome_file_tmp = substr($nome_file,0,-4).".tmp";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if ($contenuto_mod[$percorso_cartella_modello][$nome_file]) {
$lingua_modello = $ini_lingua;
$num_periodi_date = "";
$anno_modello = "";
scrivi_file("<?php exit(); ?>
".$contenuto_mod[$percorso_cartella_modello]["$nome_file"],"$percorso_cartella_modello/$nome_file_tmp");
$funz_recupera_var_modello($nome_file_tmp,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$prima_parte_html = str_replace("<?php exit(); ?>
","",$prima_parte_html);
if (substr($prima_parte_html,0,70) == "<?php if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed']) { ?>") $prima_parte_html = substr($prima_parte_html,70);
if (substr($prima_parte_html,0,74) == "<?php if (!@\$framed and !@\$_GET[\\'framed\\'] and !@\$_POST[\\'framed\\']) { ?>") $prima_parte_html = substr($prima_parte_html,74);
unlink("$percorso_cartella_modello/$nome_file_tmp");
assegna_var_conn_mod($N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($contenuto_mod[$percorso_cartella_modello][$nome_file])
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
$pag = $pag_orig;
$lingua_mex = $lingua_mex_orig;
} # fine if (@is_array($contenuto_mod) and...


} # fine if ($numconnessione)
else if ($silenzio != "SI") echo mex("Non è stato possibile connettersi al nuovo database, controllare i dati per la connessione, i privilegi dell' utente o il nome del database",$pag).".<br>";
} # fine if ($query)
else if ($silenzio != "SI") echo mex("Non è stato possibile creare il nuovo database, controllare i dati per la connessione, i privilegi dell' utente, il nome del database o se esiste già un database chiamato",$pag)." $N_PHPR_DB_NAME.<br>";


} # fine else if ($file_coerente == "NO")

return $fatto;

} # fine function ripristina_backup






function ripristina_backup_contr ($linee_backup,$silenzio,$pag,$PHPR_TAB_PRE,$modalita,$contr_agg="") {
global $LIKE,$MEDIUMTEXT;
include("./includes/variabili_contratto.php");
unset($var_predef);

$versione_corrente = esegui_query("select * from ".$PHPR_TAB_PRE."versioni where idversioni = 1");
$versione_corrente = risul_query($versione_corrente,0,'num_versione');
for ($num1 = 0 ; $num1 < count($linee_backup) ; $num1++) {
$linea = togli_acapo($linee_backup[$num1]);
if (substr($linea,0,10) == "<versione>") {
$versione_file = substr($linea,10);
$versione_file = substr($versione_file,0,-11);
break;
} # fine if (substr($linea,0,10) == "<versione>")
} # fine for $num1
if ($versione_file and ($versione_file <= $versione_corrente and $versione_file >= "2.22")) {

if ($modalita != "aggiungi") $contr_agg = "";
$contr_agg_var = $contr_agg;
$contr_agg_all = "";
$contr_agg_trovato = 0;

# Prima lettura di controllo del file contratti
$file_coerente = "SI";
$num_err = "";
$leggendo_righe = "NO";
unset($tab_presente);
$info_contr['max_contr'] = 0;
$num_linee_backup = count($linee_backup);
for ($num1 = 0 ; $num1 < $num_linee_backup ; $num1++) {
$linea = togli_acapo($linee_backup[$num1]);
if ($linea == "</backup>" and $leggendo_righe != "SI") $leggendo_backup = "NO";
if ($leggendo_backup == "SI") {
if ($linea == "</database>" and $leggendo_righe != "SI") $leggendo_database = "NO";
if ($leggendo_database == "SI") {
if ($linea == "</tabella>" and $leggendo_righe != "SI") $leggendo_tabella = "NO";
if ($leggendo_tabella == "SI") {
if (substr($linea,0,13) == "<nometabella>") {
$nome_tabella = substr($linea,13);
$nome_tabella = substr($nome_tabella,0,-14);
} # fine if (substr($linea,0,13) == "<nometabella>")

if ($linea == "</colonnetabella>") {
$leggendo_colonne == "NO";
if ($nome_tabella == "contratti") $tab_presente["contratti"] = "SI";
if ($nome_tabella != "contratti") { $file_coerente = "NO"; $num_err .= "#1"; }
} # fine if ($linea == "</colonnetabella>")
if ($leggendo_colonne == "SI") {
if (substr($linea,0,13) == "<nomecolonna>") {
$num_colonne++;
$nome_colonna[$num_colonne] = substr($linea,13);
$nome_colonna[$num_colonne] = substr($nome_colonna[$num_colonne],0,-14);
$num_colonna[$nome_colonna[$num_colonne]] = $num_colonne - 1;
} # fine if (substr($linea,0,13) == "<nomecolonna>")
if (substr($linea,0,13) == "<tipocolonna>") {
$tipo_colonna[$num_colonne] = substr($linea,13);
$tipo_colonna[$num_colonne] = substr($tipo_colonna[$num_colonne],0,-14);
} # fine if (substr($linea,0,13) == "<tipocolonna>")
} # fine if ($leggendo_colonne == "SI")
if ($linea == "<colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne = "SI";
$num_colonne = 0;
} # fine if ($linea == "<colonnetabella>" and $leggendo_righe != "SI")

if ($linea == "</righetabella>") $leggendo_righe = "NO";
if ($leggendo_righe == "SI") {
if (substr($linea,0,6) == "<riga>") $riga = substr($linea,11);
else {
$riga .= "
".$linea;
} # fine else if (substr($linea,0,6) == "<riga>")
if (substr($linea,-7) == "</riga>") {
$riga = substr($riga,0,-13);
$riga = explode("</cmp><cmp>",$riga);

if ($nome_tabella == "contratti") {
$tipo_contr = $riga[$num_colonna['tipo']];
if (substr($tipo_contr,0,3) == "var") {
$nome = $riga[$num_colonna['testo']];
if ($var_riserv[$nome]) { $file_coerente = "NO"; $num_err .= "#2"; }
} # fine if (substr($tipo_contr,0,3) == "var")
if (substr($tipo_contr,0,4) == "vett") {
$nome = explode(";",$riga[$num_colonna['testo']]);
if ($var_riserv[$nome[0]]) { $file_coerente = "NO"; $num_err .= "#3"; }
if ($var_riserv[$nome[1]]) { $file_coerente = "NO"; $num_err .= "#4"; }
} # fine if (substr($tipo_contr,0,4) == "vett")
if ($modalita == "info") {
if ($riga[$num_colonna['tipo']] == "nomi_con" and $riga[$num_colonna['numero']] == "1") $info_contr['nomi_con'] = $riga[$num_colonna['testo']];
if (substr($riga[$num_colonna['tipo']],0,5) == "contr" and $riga[$num_colonna['numero']] > $info_contr['max_contr']) $info_contr['max_contr'] = $riga[$num_colonna['numero']];
} # fine if ($modalita == "info")
if ($contr_agg and $riga[$num_colonna['numero']] == $contr_agg) {
if (substr($riga[$num_colonna['tipo']],0,5) == "contr") $contr_agg_trovato = 1;
if ($riga[$num_colonna['tipo']] == "impor_vc") $contr_agg_var = $riga[$num_colonna['testo']];
if ($riga[$num_colonna['tipo']] == "allegato") $contr_agg_all .= ",".$riga[$num_colonna['testo']].",";
} # fine if ($contr_agg and $riga[$num_colonna['numero']] == $contr_agg)
} # fine if ($nome_tabella == "contratti")

$lista_valori = "";
$lista_colonne = "";
unset($valore_colonna);
for ($num2 = 0 ; $num2 < count($riga) ; $num2++) {
$valore = aggslashdb($riga[$num2]);
if ($valore != "") {
$valore = str_replace("</righetabella@%&@>","</righetabella>",$valore);
$valore = str_replace("</riga@%&@>","</riga>",$valore);
$valore = str_replace("<riga@%&@>","<riga>",$valore);
$valore = str_replace("</cmp@%&@>","</cmp>",$valore);
$valore = str_replace("@%&@@%&@","@%&@",$valore);
$valore_colonna[$nome_colonna[($num2+1)]] = $valore;
} # fine if ($valore != "")
} # fine for $num2
if (defined('C_MASSIMO_NUM_CONTRATTI') and C_MASSIMO_NUM_CONTRATTI != 0 and $nome_tabella == "contratti" and $linee_inserite_in_tabella[$nome_tabella] >= ((C_MASSIMO_NUM_CONTRATTI * 200) + 100)) { $file_coerente = "NO"; $num_err .= "#5"; }
$linee_inserite_in_tabella[$nome_tabella]++;
} # fine if (substr($linea,-7) == "</riga>")
} # fine if ($leggendo_righe == "SI")
if ($linea == "<righetabella>") {
$leggendo_righe = "SI";
$linee_inserite_in_tabella[$nome_tabella] = 0;
} # fine if ($linea == "<righetabella>")
} # fine if ($leggendo_tabella == "SI")
if ($linea == "<tabella>") $leggendo_tabella = "SI";
} # fine if ($leggendo_database == "SI")

if ($linea == "<database>") $leggendo_database = "SI";
} # fine if ($leggendo_backup == "SI")
if ($linea == "<backup>") $leggendo_backup = "SI";
} # fine for $num1

if ($tab_presente["contratti"] != "SI") { $file_coerente = "NO"; $num_err .= "#6"; }
if ($contr_agg and !$contr_agg_trovato) { $file_coerente = "NO"; $num_err .= "#7"; }


if ($file_coerente == "NO") {
if ($silenzio != "SI") echo mex("Il formato del file è errato",$pag).".<br>";
# debug backup DOCUMENTI
#if ($num_err) echo $num_err.".<br>";
} # fine if ($file_coerente == "NO")
else {

unset($var_riserv);
if ($modalita == "info") return $info_contr;

if ($modalita == "rimpiazza") esegui_query("delete from ".$PHPR_TAB_PRE."contratti");
$max_contr = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'contr%'");
if (numlin_query($max_contr) != 0) $max_contr = risul_query($max_contr,0,0);
else $max_contr = 0;
if (!$max_contr) $max_contr = 0;
$max_var = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'var%'");
if (numlin_query($max_var) != 0) $max_var = risul_query($max_var,0,0);
else $max_var = 0;
if (!$max_var) $max_var = 0;
$max_arr = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'vett%'");
if (numlin_query($max_arr) != 0) $max_arr = risul_query($max_arr,0,0);
else $max_arr = 0;
if (!$max_arr) $max_arr = 0;
$max_cond = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo = 'cond'");
if (numlin_query($max_cond) != 0) $max_cond = risul_query($max_cond,0,0);
else $max_cond = 0;
if (!$max_cond) $max_cond = 0;
$max_all = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo = 'file_all'");
if (numlin_query($max_all) != 0) $max_all = risul_query($max_all,0,0);
else $max_all = 0;
if (!$max_all) $max_all = 0;
unset($nuovo_num_contr);
unset($nuovo_num_var);
$nuovo_num_var['-1'] = '-1';
$nuovo_num_var['-2'] = '-2';
unset($nuovo_nome_var);
unset($nuovo_num_all);

$leggendo_backup = "NO";
$leggendo_database = "NO";
$leggendo_tabella = "NO";
$leggendo_colonne = "NO";
$leggendo_righe = "NO";
$leggendo_file = "NO";
$leggendo_contenuto = "NO";
$nome_file = "";
unset($linee_inserite_in_tabella);
if (defined('C_CARTELLA_CREA_MODELLI') and C_CARTELLA_CREA_MODELLI != "") {
if (defined('C_CARTELLA_DOC') and C_CARTELLA_DOC != "" and @is_dir(C_CARTELLA_CREA_MODELLI."/".C_CARTELLA_DOC)) $dir_salva_home = C_CARTELLA_CREA_MODELLI."/".C_CARTELLA_DOC;
else $dir_salva_home = C_CARTELLA_CREA_MODELLI;
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else $dir_salva_home = C_DATI_PATH;

for ($num1 = 0 ; $num1 < $num_linee_backup ; $num1++) {
$linea = togli_acapo($linee_backup[$num1]);

if ($linea == "</backup>" and $leggendo_righe != "SI") $leggendo_backup = "NO";

if ($leggendo_backup == "SI") {
if ($linea == "</database>" and $leggendo_righe != "SI") $leggendo_database = "NO";

# restore del database
if ($leggendo_database == "SI") {
if ($linea == "</tabella>" and $leggendo_righe != "SI") $leggendo_tabella = "NO";

if ($leggendo_tabella == "SI") {
if (substr($linea,0,13) == "<nometabella>") {
$nome_tabella = substr($linea,13);
$nome_tabella = substr($nome_tabella,0,-14);
} # fine if (substr($linea,0,13) == "<nometabella>")

if ($linea == "</colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne == "NO";
#if ($nome_tabella == "contratti") esegui_query2("create table ".$N_PHPR_TAB_PRE."contratti (numero integer, tipo varchar(8), testo $MEDIUMTEXT )",$N_PHPR_DB_TYPE);
} # fine if ($linea == "</colonnetabella>")
if ($leggendo_colonne == "SI") {
if (substr($linea,0,13) == "<nomecolonna>") {
$num_colonne++;
$nome_colonna[$num_colonne] = substr($linea,13);
$nome_colonna[$num_colonne] = substr($nome_colonna[$num_colonne],0,-14);
$num_colonna[$nome_colonna[$num_colonne]] = $num_colonne - 1;
} # fine if (substr($linea,0,13) == "<nomecolonna>")
if (substr($linea,0,13) == "<tipocolonna>") {
$tipo_colonna[$num_colonne] = substr($linea,13);
$tipo_colonna[$num_colonne] = substr($tipo_colonna[$num_colonne],0,-14);
} # fine if (substr($linea,0,13) == "<tipocolonna>")
} # fine if ($leggendo_colonne == "SI")
if ($linea == "<colonnetabella>" and $leggendo_righe != "SI") {
$leggendo_colonne = "SI";
$num_colonne = 0;
$lista_tariffe = "";
} # fine if ($linea == "<colonnetabella>" and $leggendo_righe != "SI")

if ($linea == "</righetabella>") $leggendo_righe = "NO";
if ($leggendo_righe == "SI") {
if (substr($linea,0,6) == "<riga>") $riga = substr($linea,11);
else {
$riga .= "
".$linea;
} # fine else if (substr($linea,0,6) == "<riga>")
if (substr($linea,-7) == "</riga>") {
$riga = substr($riga,0,-13);
$riga = explode("</cmp><cmp>",$riga);
$lista_valori = "";
$lista_colonne = "";
unset($valore_colonna);

$inserire_riga = "SI";
$tipo_contr = $riga[$num_colonna['tipo']];
$vecchio_num = $riga[$num_colonna['numero']];

if (substr($tipo_contr,0,5) == "contr" or $tipo_contr == "opzeml" or $tipo_contr == "oggetto" or $tipo_contr == "allegato" or $tipo_contr == "headhtm" or $tipo_contr == "foothtm" or $tipo_contr == "est_txt" or $tipo_contr == "dir" or substr($tipo_contr,0,4) == "mln_" or $tipo_contr == "impor_vc" or $tipo_contr == "num_prog" or $tipo_contr == "nomefile" or $tipo_contr == "autosalv" or $tipo_contr == "compress" or $tipo_contr == "incr_np" or $tipo_contr == "api") {
if (!$contr_agg or $vecchio_num == $contr_agg) {
if (!$nuovo_num_contr[$vecchio_num]) {
if ($contr_agg) $nuovo_num_contr[$vecchio_num] = $max_contr + 1;
else $nuovo_num_contr[$vecchio_num] = $max_contr + $vecchio_num;
} # fine if (!$nuovo_num_contr[$vecchio_num])
$riga[$num_colonna['numero']] = $nuovo_num_contr[$vecchio_num];
if (substr($tipo_contr,0,5) == "contr" or substr($tipo_contr,0,4) == "mln_") {
if (@is_array($nuovo_nome_var)) {
reset($nuovo_nome_var);
foreach ($nuovo_nome_var as $v_nome => $n_nome) {
$riga[$num_colonna['testo']] = str_replace("[$v_nome]","[$n_nome]",$riga[$num_colonna['testo']]);
$riga[$num_colonna['testo']] = str_replace("[c $v_nome=","[c $n_nome=",$riga[$num_colonna['testo']]);
$riga[$num_colonna['testo']] = str_replace("[c $v_nome!=","[c $n_nome!=",$riga[$num_colonna['testo']]);
if (substr($v_nome,-1) == ")") {
$v_nome_arr = explode("(",$v_nome);
$v_nome_arr = $v_nome_arr[0];
$n_nome_arr = explode("(",$n_nome);
$n_nome_arr = $n_nome_arr[0];
$riga[$num_colonna['testo']] = str_replace("[r4 array=\"$v_nome_arr\"]","[r4 array=\"$n_nome_arr\"]",$riga[$num_colonna['testo']]);
$riga[$num_colonna['testo']] = str_replace("[$v_nome_arr('","[$n_nome_arr('",$riga[$num_colonna['testo']]);
$riga[$num_colonna['testo']] = str_replace("[c $v_nome_arr('","[c $n_nome_arr('",$riga[$num_colonna['testo']]);
} # fine if (substr($v_nome,-1) == ")")
} # fine foreach ($nuovo_nome_var as $v_nome => $n_nome)
} # fine if (@is_array($nuovo_nome_var))
} # fine if (substr($tipo_contr,0,5) == "contr" or substr($tipo_contr,0,4) == "mln_")
if ($tipo_contr == "dir") {
if (!function_exists('formatta_dir_salva_doc')) include('./includes/funzioni_contratti.php');
$dir_salva = formatta_dir_salva_doc($riga[$num_colonna['testo']]);
if ($dir_salva == "~") $dir_salva = $dir_salva_home;
if (!@is_dir($dir_salva)) $inserire_riga = "NO";
else {
$fileaperto = @fopen("$dir_salva/prova.tmp","a+");
if (!$fileaperto) $inserire_riga = "NO";
else {
fclose($fileaperto);
unlink("$dir_salva/prova.tmp");
} # fine else if (!$fileaperto)
} # fine else if (!@is_dir($dir_salva))
} # fine if ($tipo_contr == "dir")
if ($tipo_contr == "allegato") {
$vecchio_all = $riga[$num_colonna['testo']];
if (!$nuovo_num_all[$vecchio_all]) $nuovo_num_all[$vecchio_all] = $max_all + $vecchio_all;
$riga[$num_colonna['testo']] = $nuovo_num_all[$vecchio_all];
} # fine if ($tipo_contr == "allegato")
if ($tipo_contr == "impor_vc") {
if (!$contr_agg) {
$vecchio_num_impor_vc = $riga[$num_colonna['testo']];
if (!$nuovo_num_contr[$vecchio_num_impor_vc]) $nuovo_num_contr[$vecchio_num_impor_vc] = $max_contr + $vecchio_num_impor_vc;
$riga[$num_colonna['testo']] = $nuovo_num_contr[$vecchio_num_impor_vc];
} # fine if (!$contr_agg)
else $inserire_riga = "NO";
} # fine if ($tipo_contr == "impor_vc")
if ($tipo_contr == "num_prog") {
if (!$contr_agg) {
$vecchio_num_np = $riga[$num_colonna['testo']];
if (!$nuovo_num_contr[$vecchio_num_np]) $nuovo_num_contr[$vecchio_num_np] = $max_contr + $vecchio_num_np;
$riga[$num_colonna['testo']] = $nuovo_num_contr[$vecchio_num_np];
} # fine if (!$contr_agg)
else $inserire_riga = "NO";
} # fine if ($tipo_contr == "num_prog")
if ($tipo_contr == "api") {
$valori = "23456789ABCDEFGHJKLMNPQRSTUVWXZabcdefghijkmnpqrstuvwxyz";
srand((double)microtime() * 1000000);
unset($pass_generata);
for ($num2 = 0 ; $num2 < 8 ; $num2++) $pass_generata .= substr($valori,rand(0,54),1);
$riga[$num_colonna['testo']] .= $pass_generata;
} # fine if ($tipo_contr == "api")
} # fine if (!$contr_agg or $vecchio_num == $contr_agg)
else $inserire_riga = "NO";
} # fine if (substr($tipo_contr,0,5) == "contr" or $tipo_contr == "opzeml" or...

if (substr($tipo_contr,0,3) == "var") {
$contr_cond = substr($tipo_contr,3);
if (!$contr_agg or $contr_agg_var == $contr_cond) {
if (strcmp($contr_cond,"")) {
if (!$nuovo_num_contr[$contr_cond]) {
if ($contr_agg) $nuovo_num_contr[$contr_cond] = $max_contr + 1;
else $nuovo_num_contr[$contr_cond] = $max_contr + $contr_cond;
} # fine if (!$nuovo_num_contr[$contr_cond])
$contr_cond = $nuovo_num_contr[$contr_cond];
$tipo_contr = "var$contr_cond";
$riga[$num_colonna['tipo']] = $tipo_contr;
} # fine if (strcmp($contr_cond,""))
if (!$nuovo_num_var[$vecchio_num]) {
$num_var_esistente = "";
$testo = $riga[$num_colonna['testo']];
$var_esistente = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where (tipo $LIKE 'var%' and testo = '".aggslashdb($testo)."') or (tipo $LIKE 'vett%' and testo $LIKE '".aggslashdb($testo).";%') ");
for ($num2 = 0 ; $num2 < numlin_query($var_esistente) ; $num2++) if ($testo == risul_query($var_esistente,$num2,'testo') and $tipo_contr == risul_query($var_esistente,$num2,'tipo')) $num_var_esistente = ($num2 + 1);
if ($num_var_esistente) {
$nuovo_num_var[$vecchio_num] = risul_query($var_esistente,($num_var_esistente - 1),'numero');
$inserire_riga = "NO";
} # fine if ($num_var_esistente)
else {
$nuovo_num_var[$vecchio_num] = $max_var + $vecchio_num;
if (numlin_query($var_esistente)) {
$nuovo_nome_trovato = "NO";
$nuovo_nome = $testo;
while ($nuovo_nome_trovato == "NO") {
$nuovo_nome .= "_";
$var_esistente = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where (tipo $LIKE 'var%' and testo = '".aggslashdb($nuovo_nome)."') or (tipo $LIKE 'vett%' and testo $LIKE '".aggslashdb($nuovo_nome).";%') ");
if (!numlin_query($var_esistente)) $nuovo_nome_trovato = "SI";
} # fine while ($nuovo_nome_trovato == "NO")
$nuovo_nome_var[$testo] = $nuovo_nome;
# cambio il nome della variabile anche negli array già inseriti
reset($nuovo_nome_var);
foreach ($nuovo_nome_var as $key => $val) {
$nuovo_nome_var[$key] = str_replace("($testo)","($nuovo_nome)",$val);
if ($val != $nuovo_nome_var[$key]) {
$nome_arr = substr($nuovo_nome_var[$key],0,(-1 * (strlen($nuovo_nome) + 2)));
$arr_esistente = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo = 'vett$contr_cond' and testo = '".aggslashdb($nome_arr).";".aggslashdb($testo)."' ");
if (numlin_query($arr_esistente)) {
$testo_arr = explode(";",risul_query($arr_esistente,0,'testo'));
$testo_arr[1] = $nuovo_nome;
$testo_arr = implode(";",$testo_arr);
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '".aggslashdb($testo_arr)."' where tipo = 'vett$contr_cond' and testo = '".aggslashdb($nome_arr).";".aggslashdb($testo)."' ");
} # fine if (numlin_query($arr_esistente))
} # fine if ($val != $nuovo_nome_var[$key])
} # fine foreach ($nuovo_nome_var as $key => $val)
$riga[$num_colonna['testo']] = $nuovo_nome;
} # fine if (numlin_query($var_esistente))
} # fine else if ($num_var_esistente)
} # fine if (!$nuovo_num_contr[$vecchio_num])
$riga[$num_colonna['numero']] = $nuovo_num_var[$vecchio_num];
} # fine if (!$contr_agg or $contr_agg_var == $contr_cond)
else $inserire_riga = "NO";
} # fine if (substr($tipo_contr,0,3) == "var")

if (substr($tipo_contr,0,4) == "vett") {
$contr_cond = substr($tipo_contr,4);
if (!$contr_agg or $contr_agg_var == $contr_cond) {
if (strcmp($contr_cond,"")) {
if (!$nuovo_num_contr[$contr_cond]) {
if ($contr_agg) $nuovo_num_contr[$contr_cond] = $max_contr + 1;
else $nuovo_num_contr[$contr_cond] = $max_contr + $contr_cond;
} # fine if (!$nuovo_num_contr[$contr_cond])
$contr_cond = $nuovo_num_contr[$contr_cond];
$tipo_contr = "vett$contr_cond";
$riga[$num_colonna['tipo']] = $tipo_contr;
} # fine if (strcmp($contr_cond,""))
if (!$nuovo_num_var["a$vecchio_num"]) {
$num_arr_esistente = "";
$testo = $riga[$num_colonna['testo']];
$nome_arr = explode(";",$testo);
$var_arr = $nome_arr[1];
$nome_arr = $nome_arr[0];
$arr_esistente = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where (tipo $LIKE 'var%' and testo = '".aggslashdb($nome_arr)."') or (tipo $LIKE 'vett%' and testo $LIKE '".aggslashdb($nome_arr).";%') ");
for ($num2 = 0 ; $num2 < numlin_query($arr_esistente) ; $num2++) if ($testo == risul_query($arr_esistente,$num2,'testo') and $tipo_contr == risul_query($arr_esistente,$num2,'tipo')) $num_arr_esistente = ($num2 + 1);
if ($num_arr_esistente) {
$nuovo_num_var["a$vecchio_num"] = "a".risul_query($arr_esistente,($num_arr_esistente - 1),'numero');
$inserire_riga = "NO";
} # fine if ($num_arr_esistente)
else {
$nuovo_num_var["a$vecchio_num"] = "a".($max_arr + $vecchio_num);
if (numlin_query($arr_esistente)) {
$nuovo_nome_trovato = "NO";
$nuovo_nome = $nome_arr;
while ($nuovo_nome_trovato == "NO") {
$nuovo_nome .= "_";
$arr_esistente = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where (tipo $LIKE 'var%' and testo = '".aggslashdb($nuovo_nome)."') or (tipo $LIKE 'vett%' and testo $LIKE '".aggslashdb($nuovo_nome).";%') ");
if (!numlin_query($arr_esistente)) $nuovo_nome_trovato = "SI";
} # fine while ($nuovo_nome_trovato == "NO")
$nuovo_nome_var["$nome_arr($var_arr)"] = "$nuovo_nome($var_arr)";
$riga[$num_colonna["testo"]] = $nuovo_nome.substr($testo,strlen($nome_arr));
} # fine if (numlin_query($arr_esistente))
else $nuovo_nome_var["$nome_arr($var_arr)"] = "$nome_arr($var_arr)";
} # fine else if ($num_arr_esistente)
} # fine if (!$nuovo_num_contr["a$vecchio_num"])
$riga[$num_colonna['numero']] = substr($nuovo_num_var["a$vecchio_num"],1);
} # fine if (!$contr_agg or $contr_agg_var == $contr_cond)
else $inserire_riga = "NO";
} # fine if (substr($tipo_contr,0,4) == "vett")

if (substr($tipo_contr,0,4) == "cond") {
$contr_cond = substr($tipo_contr,4);
if (!$contr_agg or $contr_agg_var == $contr_cond) {
if (!strcmp($contr_cond,"")) $riga[$num_colonna['numero']] = $max_cond + $vecchio_num;
else {
$vecchio_num = $contr_cond;
if (!$nuovo_num_contr[$vecchio_num]) {
if ($contr_agg) $nuovo_num_contr[$vecchio_num] = $max_contr + 1;
else $nuovo_num_contr[$vecchio_num] = $max_contr + $vecchio_num;
} # fine if (!$nuovo_num_contr[$vecchio_num])
$riga[$num_colonna['tipo']] = "cond".$nuovo_num_contr[$vecchio_num];
$contr_cond = $nuovo_num_contr[$vecchio_num];
} # fine else if (!strcmp($contr_cond,""))
$cond_vecchia = $riga[$num_colonna['testo']];
$cond_vecchia = explode("#@?",$cond_vecchia);
$azione_vecchia = explode("#%?",$cond_vecchia[2]);
if (substr($cond_vecchia[0],0,3) == "rar") $cond_nuova = "rar".substr($nuovo_num_var["a".substr($cond_vecchia[0],3)],1)."#@?";
else $cond_nuova = $cond_vecchia[0]."#@?";
if ($cond_vecchia[1]) {
$cond_vecchia = explode("#$?",$cond_vecchia[1]);
$cond_nuova .= $cond_vecchia[0];
$num_cond = count($cond_vecchia);
for ($num2 = 1 ; $num2 < $num_cond ; $num2++) {
$cond_nuova .= "#$?";
$cond_vett = explode("#%?",$cond_vecchia[$num2]);
if ($cond_vett[2] == "var") ripristina_nome_var_cond(array(0,3),$cond_vett,$nuovo_nome_var);
else ripristina_nome_var_cond(array('0'),$cond_vett,$nuovo_nome_var);
for ($num3 = 0 ; $num3 < count($cond_vett) ; $num3++) $cond_nuova .= $cond_vett[$num3]."#%?";
$cond_nuova = substr($cond_nuova,0,-3);
} # fine for $num2
} # fine ($cond_vecchia[1])
$cond_nuova .= "#@?".$azione_vecchia[0];
if ($azione_vecchia[0] == "set") {
$azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
if ($azione_vecchia[3] == "var") ripristina_nome_var_cond(array(4),$azione_vecchia,$nuovo_nome_var);
if ($azione_vecchia[5] == "var") ripristina_nome_var_cond(array(6),$azione_vecchia,$nuovo_nome_var);
if ($azione_vecchia[7] == "var") ripristina_nome_var_cond(array(8),$azione_vecchia,$nuovo_nome_var);
} # fine if ($azione_vecchia[0] == "set")
if ($azione_vecchia[0] == "trunc") $azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
if ($azione_vecchia[0] == "oper") {
$azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
if ($azione_vecchia[4] == "var") ripristina_nome_var_cond(array(2,5),$azione_vecchia,$nuovo_nome_var);
else ripristina_nome_var_cond(array(2),$azione_vecchia,$nuovo_nome_var);
} # fine if ($azione_vecchia[0] == "oper")
if ($azione_vecchia[0] == "date") {
$azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
ripristina_nome_var_cond(array(2),$azione_vecchia,$nuovo_nome_var);
} # fine if ($azione_vecchia[0] == "date")
if ($azione_vecchia[0] == "opdat") {
$azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
ripristina_nome_var_cond(array(3,4),$azione_vecchia,$nuovo_nome_var);
} # fine if ($azione_vecchia[0] == "opdat")
if ($azione_vecchia[0] == "unset") $azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
if ($azione_vecchia[0] == "array") {
$azione_vecchia[1] = $nuovo_num_var[$azione_vecchia[1]];
if ($azione_vecchia[2] == "cop") $azione_vecchia[3] = $nuovo_num_var[$azione_vecchia[3]];
} # fine if ($azione_vecchia[0] == "array")
for ($num2 = 1 ; $num2 < count($azione_vecchia) ; $num2++) $cond_nuova .= "#%?".$azione_vecchia[$num2];
$riga[$num_colonna['testo']] = $cond_nuova;
$testo = aggslashdb($riga[$num_colonna['testo']]);
#$cond_esistente = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo = 'cond$contr_cond' and testo = '$testo' ");
#for ($num2 = 0 ; $num2 < numlin_query($cond_esistente) ; $num2++) if ($cond_nuova == risul_query($cond_esistente,$num2,'testo')) $inserire_riga = "NO";
} # fine if (!$contr_agg or $contr_agg_var == $contr_cond)
else $inserire_riga = "NO";
} # fine if (substr($tipo_contr,0,4) == "cond")

if ($tipo_contr == "file_all") {
$vecchio_all = $riga[$num_colonna['numero']];
if (!$contr_agg or str_replace(",$vecchio_all,","",$contr_agg_all) != $contr_agg_all) {
if (!$nuovo_num_all[$vecchio_all]) $nuovo_num_all[$vecchio_all] = $max_all + $vecchio_all;
$riga[$num_colonna['numero']] = $nuovo_num_all[$vecchio_all];
} # fine if (!$contr_agg or str_replace(",$vecchio_all,","",$contr_agg_all) != $contr_agg_all)
else $inserire_riga = "NO";
} # fine if ($tipo_contr == "file_all")

for ($num2 = 0 ; $num2 < count($riga) ; $num2++) {
$valore = aggslashdb($riga[$num2]);
if ($valore != "") {
$valore = str_replace("</righetabella@%&@>","</righetabella>",$valore);
$valore = str_replace("</riga@%&@>","</riga>",$valore);
$valore = str_replace("<riga@%&@>","<riga>",$valore);
$valore = str_replace("</cmp@%&@>","</cmp>",$valore);
$valore = str_replace("@%&@@%&@","@%&@",$valore);
$lista_valori .= ",'".$valore."'";
$lista_colonne .= ",".$nome_colonna[($num2+1)];
} # fine if ($valore != "")
} # fine for $num2
$lista_valori = substr($lista_valori,1);
$lista_colonne = substr($lista_colonne,1);
#echo "insert into $nome_tabella ($lista_colonne) values ($lista_valori)<br>";
if ($inserire_riga != "NO") esegui_query("insert into ".$PHPR_TAB_PRE."$nome_tabella ($lista_colonne) values ($lista_valori) ");
} # fine if (substr($linea,-7) == "</riga>")
} # fine if ($leggendo_righe == "SI")
if ($linea == "<righetabella>") {
$leggendo_righe = "SI";
$linee_inserite_in_tabella[$nome_tabella] = 0;
} # fine if ($linea == "<righetabella>")

} # fine if ($leggendo_tabella == "SI")

if ($linea == "<tabella>") $leggendo_tabella = "SI";
} # fine if ($leggendo_database == "SI")

if ($linea == "<database>") $leggendo_database = "SI";
} # fine if ($leggendo_backup == "SI")

if ($linea == "<backup>") $leggendo_backup = "SI";
} # fine for $num1

$tabelle_lock = array($PHPR_TAB_PRE."contratti",$PHPR_TAB_PRE."personalizza");
$altre_tab_lock = array($PHPR_TAB_PRE."versioni");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$nomi_contratti = esegui_query("select testo from $PHPR_TAB_PRE"."contratti where numero = '1' and tipo = 'nomi_con' ");
$nomi_contratti = risul_query($nomi_contratti,0,'testo');
if ($modalita == "rimpiazza") esegui_query("update $PHPR_TAB_PRE"."personalizza set valpersonalizza = '".aggslashdb($nomi_contratti)."' where idpersonalizza = 'nomi_contratti'");
else {
$nomi_contratti = explode("#@&",$nomi_contratti);
$n_nomi_contratti = "";
for ($num1 = 0 ; $num1 < count($nomi_contratti) ; $num1++) {
$nome_contr = explode("#?&",$nomi_contratti[$num1]);
if (!$contr_agg or $nome_contr[0] == $contr_agg) $n_nomi_contratti .= "#@&".$nuovo_num_contr[$nome_contr[0]]."#?&".$nome_contr[1];
} # fine for $num1
$d_nomi_contr = esegui_query("select * from $PHPR_TAB_PRE"."personalizza where idpersonalizza = 'nomi_contratti' ");
for ($num1 = 0 ; $num1 < numlin_query($d_nomi_contr) ; $num1++) {
$idutente_contr = risul_query($d_nomi_contr,$num1,'idutente');
$nomi_contr_utente = risul_query($d_nomi_contr,$num1,'valpersonalizza').$n_nomi_contratti;
esegui_query("update $PHPR_TAB_PRE"."personalizza set valpersonalizza = '".aggslashdb($nomi_contr_utente)."' where idpersonalizza = 'nomi_contratti' and idutente = '$idutente_contr' ");
} # fine for $num1
} # fine else if ($modalita == "rimpiazza")
esegui_query("delete from $PHPR_TAB_PRE"."contratti where numero = '1' and tipo = 'nomi_con' ");
unlock_tabelle($tabelle_lock);

if ($silenzio != "SI") echo mex("Documenti ripristinati",$pag).".<br>";
} # fine else if ($file_coerente == "NO")

} # fine if ($versione_file and ($versione_file <= $versione_corrente and...
else if ($silenzio != "SI") echo mex("La versione attuale di hoteldruid e quella del file non coincidono",$pag).".<br>";

} # fine function ripristina_backup_contr






?>
