<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2017 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

$pag = "interconnessioni.php";
$titolo = "HotelDruid: Interconnessioni";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
include("./includes/funzioni_backup.php");
$numconnessione = connetti_db_per_backup($PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata);
if (!isset($anno)) {
if (isset($_GET['anno'])) $anno = $_GET['anno'];
if (isset($_POST['anno'])) $anno = $_POST['anno'];
if (!isset($anno)) {
@include_once(C_DATI_PATH."/versione.php");
$anno = date("Y",(time() + (C_DIFF_ORE * 3600)));
if (@is_file(C_DATI_PATH."/selectperiodi".($anno + 1).".1.php")) $anno = $anno + 1;
elseif (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php") and @is_file(C_DATI_PATH."/selectperiodi".($anno - 1).".1.php")) $anno = $anno - 1;
} # fine if (!isset($anno))
} # fine if (!isset($anno))
include("./includes/funzioni.php");
if (function_exists('ini_set')) @ini_set('opcache.enable',0);
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablegruppi = $PHPR_TAB_PRE."gruppi";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablesessioni = $PHPR_TAB_PRE."sessioni";
$tableversioni = $PHPR_TAB_PRE."versioni";
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableinterconnessioni = $PHPR_TAB_PRE."interconnessioni";
$tablecache = $PHPR_TAB_PRE."cache";

if ($user) $nome_utente_phpr = $user;
if ($password) $password_phpr = $password;
$id_utente_azione_ic = "";

if (defined("C_UTENTE_AZIONE_IC") and C_UTENTE_AZIONE_IC != "" and $utente_azione_ic == C_UTENTE_AZIONE_IC) {
#$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
#if (!numlin_query($id_utente_az)) {
$id_utente = "a";
$id_utente_azione_ic = "a";
include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];
if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "") {
$id_utente_sessione = "-1";
$adesso = $anno.date("mdHis",(time() + (C_DIFF_ORE * 3600)));
$versione_unica = prendi_numero_versione($tableversioni);
list($usec, $sec) = explode(' ', microtime());
mt_srand((float) $sec + ((float) $usec * 100000));
$val_casuale = mt_rand(100000,999999);
$id_sessione = $adesso.$val_casuale.$versione_unica;
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
$REMOTE_ADDR = aggslashdb($REMOTE_ADDR);
if ($_SERVER['REMOTE_PORT']) $REMOTE_PORT = $_SERVER['REMOTE_PORT'];
$REMOTE_PORT = aggslashdb($REMOTE_PORT);
if ($_SERVER['HTTP_USER_AGENT']) $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
$HTTP_USER_AGENT = aggslashdb($HTTP_USER_AGENT);
if ($_SERVER['HTTPS'] == "on" or $_SERVER['SERVER_PORT'] == "443") $tipo_conn = "HTTPS";
else $tipo_conn = "HTTP";
esegui_query("insert into $tablesessioni (idsessioni,idutente,indirizzo_ip,tipo_conn,user_agent,ultimo_accesso) values ('$id_sessione','$id_utente_sessione','$REMOTE_ADDR','$tipo_conn','$HTTP_USER_AGENT','$ultimo_accesso')","",1);
} # fine if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
#} # fine if (!numlin_query($id_utente_az))
} # fine if (defined("C_UTENTE_AZIONE_IC") and C_UTENTE_AZIONE_IC != "" and $utente_azione_ic == C_UTENTE_AZIONE_IC)

if (!$id_utente_azione_ic) $id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);

if ($id_utente != 1 and $id_utente != "a") {
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else $anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
$priv_crea_interconnessioni = substr($priv_mod_pers,3,1);
} # fine if ($id_utente != 1 and $id_utente != "a")
else {
if ($id_utente == 1) {
$priv_crea_interconnessioni = "s";
$anno_utente_attivato = "SI";
} # fine if ($id_utente == 1)
else $priv_crea_interconnessioni = "n";
} # fine else if ($id_utente != 1 and $id_utente != "a")

if ($id_utente and ($priv_crea_interconnessioni != "s" or $anno_utente_attivato != "SI") and !$id_utente_azione_ic) {
$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
if (numlin_query($id_utente_az) == 1) $id_utente_azione_ic = risul_query($id_utente_az,0,'idlocale');
} # fine if ($id_utente and ($priv_crea_interconnessioni != "s" or $anno_utente_attivato != "SI") and !$id_utente_azione_ic)

if ($id_utente and (($priv_crea_interconnessioni == "s" and $anno_utente_attivato == "SI") or $id_utente == $id_utente_azione_ic) and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO")) {

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
$installazione_subordinata = "SI";
$inserimento_nuovi_clienti = "NO";
$priv_ins_nuove_prenota = "n";
$priv_mod_prenotazioni = "n";
$modifica_clienti = "NO";
$priv_ins_nuove_prenota = "n";
$priv_ins_spese = "n";
$priv_ins_entrate = "n";
$priv_ins_costi_agg = "n";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");



function scarica_backup_subordinazione ($file_backup,$url_subordinazione,$utente_subordinazione,$password_subordinazione,$anno,$compresso_subordinazione) {
global $tableversioni,$pag;
$errore = "NO";
if ($output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione&anno=$anno&azione=SI&crea_backup=SI")) {
$backup = @fopen($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione&anno=$anno&azione=SI&mostra_header=NO&salva_backup=SI&compresso=$compresso_subordinazione","r");
if ($backup) {
if ($fileaperto = fopen($file_backup,"w")) {
flock($fileaperto,2);
while (!feof($backup)) {
$linee = fread($backup,524288);
fwrite($fileaperto,$linee);
} # fine while (!feof($fbackup))
flock($fileaperto,3);
fclose($fileaperto);
} # fine if ($fileaperto = fopen($file_backup,"w"))
else {
$errore = "SI";
echo mex("Non ho i permassi di scrittura sulla cartella dati",$pag).".<br>";
} # fine else if ($fileaperto = fopen($file_backup,"w"))
fclose ($backup);
if ($errore != "SI") {
if ($compresso_subordinazione == "SI") {
if (rename($file_backup,$file_backup.".gz")) {
if ($fileaperto = fopen($file_backup,"w")) {
flock($fileaperto,2);
$gzbackup = gzopen($file_backup.".gz","r");
if ($gzbackup) {
while (!feof($gzbackup)) {
$linee = gzread($gzbackup,524288);
fwrite($fileaperto,$linee);
} # fine while (!feof($gzbackup))
} # fine if ($gzbackup)
gzclose($gzbackup);
flock($fileaperto,3);
fclose($fileaperto);
} # fine if ($fileaperto = fopen($file_backup,"w"))
unlink($file_backup.".gz");
} # fine if (rename($file_backup,$file_backup.".gz"))
} # fine if ($compresso_subordinazione == "SI")
$linee = "";
if ($fbackup = fopen($file_backup,"r")) {
$linee = fread($fbackup,524288);
fclose($fbackup);
} # fine if ($fbackup = fopen($file,"r"))
$versione_corrente = esegui_query("select * from $tableversioni where idversioni = 1");
$versione_corrente = risul_query($versione_corrente,0,'num_versione');
if (str_replace("<versione>$versione_corrente</versione>","",$linee) == $linee) {
$errore = "SI";
if (str_replace("<versione>","",$linee) == $linee) echo mex("Non si è potuto scaricare il backup remoto",$pag).".<br>";
else echo mex("Versione locale diversa da quella remota",$pag).".<br>";
include("./includes/funzioni_aggiorna.php");
global $agg_cod_sorg;
unset($linee);
$agg_cod_sorg = aggiorna_codice_sorgente_phpr($url_subordinazione);
if ($agg_cod_sorg) echo "<br>".mex("Aggiornamento codice sorgente eseguito con successo",$pag)."!<br><br>";
} # fine if (str_replace("<versione>$versione_corrente</versione>","",$linee) == $linee)
unset($linee);
} # fine if ($errore != "SI")
} # fine if ($backup)
else {
$errore = "SI";
echo mex("Non si è potuto stabilire la connessione",$pag).".<br>";
} # fine else if ($backup)
} # fine if ($output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione"))
else {
$errore = "SI";
echo mex("Non si è potuto stabilire la connessione",$pag).".<br>";
} # fine else if ($output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione"))
if ($errore != "SI") $output = @file($url_subordinazione."crea_backup.php?nome_utente_phpr=$utente_subordinazione&password_phpr=$password_subordinazione&anno=$anno&azione=SI&cancella_backup=SI");
return $errore;
} # fine function scarica_backup_subordinazione



if ($azione_ic == "SI" or $id_utente_azione_ic) {
$mostra_form_iniziale = "NO";
$modifica_interconnessione = "NO";


if (($aggiorna_subordinazione or $id_utente_azione_ic) and @is_file(C_DATI_PATH."/dati_subordinazione.php")) {
include(C_DATI_PATH."/dati_subordinazione.php");
$file_backup = C_DATI_PATH."/sub_backup.php";
$filelock = crea_lock_file($file_backup);
$errore = scarica_backup_subordinazione($file_backup,$url_subordinazione,$utente_subordinazione,$password_subordinazione,$anno,$compresso_subordinazione);


if ($errore != "SI") {
$file_sub = @fopen(C_DATI_PATH."/dati_subordinazione.php","w+");
if ($file_sub) {
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
flock($file_sub,2);
fwrite($file_sub,"<?php
\$url_subordinazione = \"$url_subordinazione\";
\$utente_subordinazione = \"".aggiungi_slash($utente_subordinazione)."\";
\$password_subordinazione = \"".aggiungi_slash($password_subordinazione)."\";
\$commento_subordinazione = \"".aggiungi_slash($commento_subordinazione)."\";
\$compresso_subordinazione = \"".aggiungi_slash($compresso_subordinazione)."\";
\$mantieni_anni = \"".aggiungi_slash($mantieni_anni)."\";
\$ultimo_aggiornamento = \"$adesso\";
?>");
flock($file_sub,3);
fclose($file_sub);
$N_PHPR_DB_TYPE = $PHPR_DB_TYPE;
$N_PHPR_DB_NAME = $PHPR_DB_NAME;
$N_PHPR_DB_HOST = $PHPR_DB_HOST;
$N_PHPR_DB_PORT = $PHPR_DB_PORT;
$N_PHPR_DB_USER = $PHPR_DB_USER;
$N_PHPR_DB_PASS = $PHPR_DB_PASS;
$N_PHPR_LOAD_EXT = $PHPR_LOAD_EXT;
$N_PHPR_TAB_PRE = $PHPR_TAB_PRE;
if ($id_sessione) {
$dati_sessione = esegui_query("select * from $tablesessioni where idsessioni = '$id_sessione'");
$num_lin_dati_sessione = numlin_query($dati_sessione);
} # fine if ($id_sessione)
else $num_lin_dati_sessione = 0;
if ($num_lin_dati_sessione == 1) {
$idutente = risul_query($dati_sessione,0,'idutente');
$indirizzo_ip = risul_query($dati_sessione,0,'indirizzo_ip');
$user_agent = risul_query($dati_sessione,0,'user_agent');
$ultimo_accesso = risul_query($dati_sessione,0,'ultimo_accesso');
} # fine if ($num_lin_dati_sessione == 1)
ripristina_backup($file_backup,"SI",$pag,$numconnessione,$database_esistente,$tempdatabase,$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata,$mantieni_anni);
esegui_query("update $tablepersonalizza set valpersonalizza = 'SI' where idpersonalizza = 'subordinazione' and idutente = '1'");
if (@is_file(C_DATI_PATH."/dati_interconnessioni.php")) unlink(C_DATI_PATH."/dati_interconnessioni.php");
if ($num_lin_dati_sessione == 1) esegui_query("insert into $tablesessioni (idsessioni,idutente,indirizzo_ip,user_agent,ultimo_accesso) values ('$id_sessione','$idutente','$indirizzo_ip','$user_agent','$ultimo_accesso')","",1);
echo mex("Aggiornamento eseguito con successo",$pag).".<br>";
} # fine if ($file_sub)
} # fine if ($errore != "SI")
elseif (!$agg_cod_sorg) echo mex("Non si è potuto portare a termine l'<div style=\"display: inline; color: red;\">aggiornamento</div>",$pag).".<br>";
@unlink($file_backup);
distruggi_lock_file($filelock,$file_backup);
} # fine if (($aggiorna_subordinazione or...


if ($id_utente_azione_ic) {
$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unset($interconnection_name);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($ic_present[$interconnection_name] == "SI" and (!$id_ric or $int_ric == $interconnection_name)) {
include("./includes/interconnect/$mod_ext/functions_import.php");
$funz_import_reservations = "import_reservations_".$interconnection_func_name;
$id_utente_origi = $id_utente;
$id_utente = 1;
$testo = $funz_import_reservations("",$id_ric,$file_interconnessioni,$anno,$PHPR_TAB_PRE,2,$id_utente,$HOSTNAME);
$id_utente = $id_utente_origi;
echo $testo;
} # fine if ($ic_present[$interconnection_name] == "SI" and (!$id_ric or...
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))
} # fine if ($id_utente_azione_ic)


if ($mostra_form_iniziale == "NO") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./inizio.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input class=\"sbutton\" type=\"submit\" name=\"indietro\" value=\"".mex("OK",$pag)."\">
</div></form>";
} # fine if ($mostra_form_iniziale == "NO")


} # fine if ($azione_ic == "SI" or $id_utente_azione_ic)



if ($priv_crea_interconnessioni == "s") {


$lista_tutti_app = "";
$maxocc = array();
$appartamenti = esegui_query("select * from $tableappartamenti order by idappartamenti");
$num_appartamenti = numlin_query($appartamenti);
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxocc[$idapp] = risul_query($appartamenti,$num1,'maxoccupanti');
$lista_tutti_app .= "$idapp,";
} # fine for $num1
$lista_tutti_app = substr($lista_tutti_app,0,-1);

$lista_regola2 = array();
$regole2 = esegui_query("select * from $tableregole where tariffa_per_app != ''");
$num_regole2 = numlin_query($regole2);
for ($num1 = 0 ; $num1 < $num_regole2 ; $num1++) {
$tariffa = risul_query($regole2,$num1,'tariffa_per_app');
$lista_app = risul_query($regole2,$num1,'motivazione');
if (strcmp($lista_app,"")) {
$lista_regola2[$tariffa] = explode(",",$lista_app);
$lista_regola2[$tariffa]['num'] = count($lista_regola2[$tariffa]);
} # fine if (strcmp($lista_app,""))
else $lista_regola2[$tariffa]['num'] = 0;
$lista_regola2[$tariffa]['txt'] = $lista_app;
$lista_app2 = risul_query($regole2,$num1,'motivazione2');
$lista_regola2[$tariffa]['txt2'] = $lista_app;
if ($lista_app and $lista_app2) $lista_regola2[$tariffa]['txt2'] .= ",$lista_app2";
$lista_regola2[$tariffa]['napp'] = risul_query($regole2,$num1,'motivazione3');
if (substr($lista_regola2[$tariffa]['napp'],0,1) == "v") {
$lista_regola2[$tariffa]['vapp'] = 1;
$lista_regola2[$tariffa]['napp'] = substr($lista_regola2[$tariffa]['napp'],1);
} # fine if (substr($lista_regola2[$tariffa]['napp'],0,1) == "v")
} # fine for $num1

$lista_regola4 = array();
$regole4 = esegui_query("select * from $tableregole where tariffa_per_persone != '' order by tariffa_per_persone");
$num_regole4 = numlin_query($regole4);
for ($num1 = 0 ; $num1 < $num_regole4 ; $num1++) {
$tariffa = risul_query($regole4,$num1,'tariffa_per_persone');
$lista_regola4[$tariffa] = risul_query($regole4,$num1,'iddatainizio');
} # fine for $num1



if ($modifica_interconnessione == "SI") {
$mostra_form_iniziale = "NO";


if ($modulo) {
unset($interconnection_name);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($modulo == $interconnection_name) {
include("./includes/interconnect/$mod_ext/form.php");
} # fine if ($modulo == $interconnection_name)
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if ($modulo)


else {

if ($crea_subordinazione) {
$file_backup = C_DATI_PATH."/sub_backup.php";
$filelock = crea_lock_file($file_backup);
$errore = "NO";
if (get_magic_quotes_gpc()) {
$url_subordinazione = stripslashes($url_subordinazione);
$utente_subordinazione = stripslashes($utente_subordinazione);
$password_subordinazione = stripslashes($password_subordinazione);
} # fine if (get_magic_quotes_gpc())
if ($compresso_subordinazione != "SI") $compresso_subordinazione = "";
if ($mantieni_anni != 1) $mantieni_anni = "";
if (substr($url_subordinazione,0,7) != "http://" and substr($url_subordinazione,0,8) != "https://") $errore = "SI";
else {
if (substr($url_subordinazione,-1) != "/") $url_subordinazione .= "/";
$errore = scarica_backup_subordinazione($file_backup,$url_subordinazione,$utente_subordinazione,$password_subordinazione,$anno,$compresso_subordinazione);
} # fine else if (substr($url_subordinazione,0,7) != "http://")

if ($errore != "SI") {
if (!$continua) {
echo "<br><big>".mex("<div style=\"display: inline; color: red;\"><b>ATTENZIONE</b></div>: premendo su <b>\"<i>Continua</i>\"</b> tutti i dati del <i>database attuale</i> verranno <b>cancellati</b>",$pag)."!</big><br><br>
<div style=\"text-align: center;\"><form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<input type=\"hidden\" name=\"crea_subordinazione\" value=\"SI\">
<input type=\"hidden\" name=\"url_subordinazione\" value=\"$url_subordinazione\">
<input type=\"hidden\" name=\"utente_subordinazione\" value=\"$utente_subordinazione\">
<input type=\"hidden\" name=\"password_subordinazione\" value=\"$password_subordinazione\">
<input type=\"hidden\" name=\"commento_subordinazione\" value=\"$commento_subordinazione\">
<input type=\"hidden\" name=\"compresso_subordinazione\" value=\"$compresso_subordinazione\">
<input type=\"hidden\" name=\"mantieni_anni\" value=\"$mantieni_anni\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" name=\"vai\" value=\"".mex("Continua",$pag)."\">
</div></form></div><br>";
} # fine if (!$continua)
else {
$file_sub = @fopen(C_DATI_PATH."/dati_subordinazione.php","w+");
if ($file_sub) {
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
flock($file_sub,2);
fwrite($file_sub,"<?php
\$url_subordinazione = \"$url_subordinazione\";
\$utente_subordinazione = \"".aggiungi_slash($utente_subordinazione)."\";
\$password_subordinazione = \"".aggiungi_slash($password_subordinazione)."\";
\$commento_subordinazione = \"".aggiungi_slash(htmlspecialchars($commento_subordinazione))."\";
\$compresso_subordinazione = \"".aggiungi_slash($compresso_subordinazione)."\";
\$mantieni_anni = \"".aggiungi_slash($mantieni_anni)."\";
\$ultimo_aggiornamento = \"$adesso\";
?>");
flock($file_sub,3);
fclose($file_sub);
$N_PHPR_DB_TYPE = $PHPR_DB_TYPE;
$N_PHPR_DB_NAME = $PHPR_DB_NAME;
$N_PHPR_DB_HOST = $PHPR_DB_HOST;
$N_PHPR_DB_PORT = $PHPR_DB_PORT;
$N_PHPR_DB_USER = $PHPR_DB_USER;
$N_PHPR_DB_PASS = $PHPR_DB_PASS;
$N_PHPR_LOAD_EXT = $PHPR_LOAD_EXT;
$N_PHPR_TAB_PRE = $PHPR_TAB_PRE;
ripristina_backup($file_backup,"SI",$pag,$numconnessione,$database_esistente,$tempdatabase,$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata,$mantieni_anni);
esegui_query("update $tablepersonalizza set valpersonalizza = 'SI' where idpersonalizza = 'subordinazione' and idutente = '1'");
echo mex("Subordinazione creata",$pag).".<br>";
} # fine if ($file_sub)
} # fine else if (!$continua)
} # fine if ($errore != "SI")

else {
echo mex("Impossibile effettuare il collegamento, controllare i dati immessi",$pag).".<br>";
} # fine else if ($errore != "SI")
@unlink($file_backup);
distruggi_lock_file($filelock,$file_backup);
} # fine if ($crea_subordinazione)


if ($cancella_subordinazione) {
@unlink(C_DATI_PATH."/dati_subordinazione.php");
esegui_query("update $tablepersonalizza set valpersonalizza = 'NO' where idpersonalizza = 'subordinazione' and idutente = '1'");
echo mex("Subordinazione cancellata",$pag).".<br>";
} # fine if ($cancella_subordinazione)


if ($modifica_utente_agg and $n_utente_agg and $n_utente_agg != "1") {
$utente_esistente = esegui_query("select * from $tableutenti where idutenti = '".aggslashdb($n_utente_agg)."'");
if (numlin_query($utente_esistente) > 0) {
$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
if (numlin_query($id_utente_az) > 0) esegui_query("update $tableinterconnessioni set idlocale = '$n_utente_agg' where tipoid = 'id_utente_az' ");
else esegui_query("insert into $tableinterconnessioni (idlocale,tipoid) values ('$n_utente_agg','id_utente_az') ");
echo "".mex("Utente per l'aggiornamento remoto modificato",$pag).".<br>";
} # fine if (numlin_query($utente_esistente) > 0)
} # fine if ($modifica_utente_agg and $n_utente_agg and...

} # fine else if ($modulo)


if ($mostra_form_iniziale == "NO") {
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";
} # fine if ($mostra_form_iniziale == "NO")

} # fine if ($modifica_interconnessione == "SI")




if ($mostra_form_iniziale != "NO") {
 
# Pagina iniziale
echo "<h3 id=\"h_intc\"><span>".mex("Interconnessioni con sorgenti esterne di dati",$pag).".</span></h3>
<hr style=\"width: 95%\">";

$id_utente_az = esegui_query("select idlocale from $tableinterconnessioni where tipoid = 'id_utente_az' ");
if (numlin_query($id_utente_az) == 1) $id_utente_azione_ic = risul_query($id_utente_az,0,"idlocale");
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<input type=\"hidden\" name=\"modifica_utente_agg\" value=\"SI\">
".mex("Utente per l'aggiornamento remoto delle interconnessioni",$pag).":
 <select name=\"n_utente_agg\">";
echo "<option value=\"\"$sel_tutti>----</option>";
$tutti_utenti = esegui_query("select idutenti,nome_utente from $tableutenti where idutenti != '1' order by idutenti");
$num_tutti_utenti = numlin_query($tutti_utenti);
for ($num1 = 0 ; $num1 < $num_tutti_utenti ; $num1++) {
$nome_utente = risul_query($tutti_utenti,$num1,'nome_utente');
$idutenti = risul_query($tutti_utenti,$num1,"idutenti");
if ($idutenti == $id_utente_azione_ic) {
$sel = " selected";
$nome_utente_agg = $nome_utente;
} # fine if ($idutenti == $id_utente_azione_ic)
else $sel = "";
echo "<option value=\"$idutenti\"$sel>$nome_utente</option>";
} # fine for $num1
echo "</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Modifica",$pag)."\">";
if ($nome_utente_agg) {
$url_corr = "http://";
if ($_SERVER['HTTPS']) $url_corr = "https://";
$url_corr .= $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
echo "<br><div class=\"separ\" style=\"height: 3px;\"></div>
<small>(URL: ".$url_corr."?user=$nome_utente_agg&amp;password=XXXXXX)</small>";
} # fine if ($nome_utente_agg)
echo "</div></form><hr style=\"width: 95%\">";

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" name=\"cancella_subordinazione\" value=\"".mex("Cancella",$pag)."\">
 ".mex("la subordinazione",$pag).".<br>
</div></form>";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))
else {

if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") echo "<div style=\"text-align: center;\"><b>".mex("Crea la subordinazione",$pag)."</b></div><br>";
else echo "<div style=\"text-align: center;\"><b>".mex("Subordinazione ad un'altra installazione di hoteldruid",$pag)."</b></div><br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./interconnessioni.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_interconnessione\" value=\"SI\">
<div style=\"line-height: 1.3;\">
".mex("Con la subordinazione non si potranno inserire nuovi dati o apportare modifiche, ma solo importare i dati dall'installazione principale",$pag).".<br>";
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") echo str_replace("hoteldruid","",mex("Subordina questa installazione di hoteldruid a quella che si trova all'indirizzo",$pag)).":<br>";
else echo mex("Subordina questa installazione di hoteldruid a quella che si trova all'indirizzo",$pag).":<br>";
echo "<input type=\"text\" name=\"url_subordinazione\" value=\"http://\" size=60><br>
".ucfirst(mex("amministratore o utente con i privilegi per creare backup",$pag)).":<br>
<table cellspacing=\"0\"><tr><td style=\"width: 30px;\"></td><td>
".ucfirst(mex("nome utente",$pag)).":
<input type=\"text\" name=\"utente_subordinazione\" size=20><br>
".mex("Password",$pag).":
<input type=\"text\" name=\"password_subordinazione\" size=20></td></tr></table>
".mex("Commento da aggiungere al titolo di questa installazione",$pag).":
<input type=\"text\" name=\"commento_subordinazione\" size=20><br>
<label><input type=\"checkbox\" name=\"compresso_subordinazione\" value=\"SI\" checked> ".mex("Usa compressione",$pag)."</label><br>
<label><input type=\"checkbox\" name=\"mantieni_anni\" value=\"1\"> ".mex("Prova a mantenere i dati degli anni non presenti nell'installazione remota",$pag)."</label><br></div>
<div style=\"text-align: center;\"><input class=\"sbutton\" type=\"submit\" name=\"crea_subordinazione\" value=\"".mex("Crea la subordinazione",$pag)."\">
</div></div></form>";

} # fine else if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))

$interconn_dir = opendir("./includes/interconnect/");
$interconnessioni = array();
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) $interconnessioni[$mod_ext] = 1;
} # fine while ($mod_ext = readdir($interconn_dir))
ksort($interconnessioni);
reset($interconnessioni);
foreach ($interconnessioni as $mod_ext => $val_i) {
include("./includes/interconnect/$mod_ext/name.php");
if ($interconnection_name_show[$lingua_mex]) $titolo = $interconnection_name_show[$lingua_mex];
else $titolo = $interconnection_name_show['en'];
echo "<hr style=\"width: 95%\"><div id=\"h$interconnection_data_dir\" style=\"text-align: center;\"><b>$titolo</b></div><br>";
include("./includes/interconnect/$mod_ext/form.php");
} # fine foreach ($interconnessioni as $mod_ext => $val_i)
closedir($interconn_dir);


$templates_dir = opendir("./includes/templates/");
$modelli = array();
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) $modelli[$modello_ext] = 1;
} # fine while ($file = readdir($lang_dig))
ksort($modelli);
reset($modelli);
foreach ($modelli as $modello_ext => $val_i) {
include("./includes/templates/$modello_ext/name.php");
if ($template_name_show['tpl_type'] == "interconnection") {
if (!function_exists('mex2')) {
$perc_cart_mod_sel = "";
include("./includes/templates/funzioni_modelli.php");
$percorso_cartella_modello = $perc_cart_mod_vett[0];
$anno_modello = $anno;
$lingua_modello = $lingua_mex;
} # fine if (!function_exists('mex2'))
if ($template_name_show[$lingua_mex]) $titolo = $template_name_show[$lingua_mex];
else {
$messaggio = "";
if (@is_file("./includes/lang/$lingua_mex/modt_$modello_ext.php")) {
$messaggio = $template_name_show['ita'];
include("./includes/lang/$lingua_mex/modt_$modello_ext.php");
if ($messaggio == $template_name_show['ita']) $messaggio = "";
} # fine if (@is_file("./includes/lang/$lingua_mex/modt_rat.php"))
if ($messaggio) $titolo = $messaggio;
else $titolo = $template_name_show['en'];
} # fine else if ($template_name_show[$lingua_mex])
echo "<hr style=\"width: 95%\"><div id=\"h".$modello_ext."\" style=\"text-align: center;\"><b>$titolo</b></div><br>";
include("./includes/templates/$modello_ext/form.php");
} # fine if ($template_name_show['tpl_type'] == "interconnection")
} # fine foreach ($modelli as $modello_ext => $val_i)
closedir($templates_dir);


echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";

} # fine if ($mostra_form_iniziale != "NO")



} # fine if ($priv_crea_interconnessioni == "s")

if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($id_utente and (($priv_crea_interconnessioni == "s" and $anno_utente_attivato == "SI") or $id_utente == $id_utente_azione_ic) and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO"))



?>
