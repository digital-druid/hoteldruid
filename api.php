<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2017-2018 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

$pag = "api.php";
$titolo = "HotelDruid: API";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablesessioni = $PHPR_TAB_PRE."sessioni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$tableanni = $PHPR_TAB_PRE."anni";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tabletransazioniweb = $PHPR_TAB_PRE."transazioniweb";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableversioni = $PHPR_TAB_PRE."versioni";
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tableparentele = $PHPR_TAB_PRE."parentele";
$tablecache = $PHPR_TAB_PRE."cache";





# API Documenti
if ($doc) {
$session_data = "";
$id_sessione = "";
$numero_contratto = aggslashdb((int) $doc);
$api_esistente = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'api'");
if (numlin_query($api_esistente)) {
$max_log_sbagliati = 10;
$minuti_durata_sessione = 30;
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
$REMOTE_ADDR = aggslashdb($REMOTE_ADDR);
$limite_transazioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_sessione * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tabletransazioniweb where tipo_transazione = 'api_l' and ultimo_accesso <= '$limite_transazioni_vecchie'");
$login_sbagliati = esegui_query("select tipo_transazione from $tabletransazioniweb where tipo_transazione = 'api_l' and dati_transazione1 = 'doc$numero_contratto' and dati_transazione2 = '$REMOTE_ADDR' ");
$login_sbagliati = numlin_query($login_sbagliati);
if ($login_sbagliati >= $max_log_sbagliati) echo "Excessive number of failed logins";
else {

if (@get_magic_quotes_gpc()) $pass = stripslashes($pass);
$pass_api = risul_query($api_esistente,0,'testo');
$id_utente = explode(";",$pass_api);
$id_utente = $id_utente[0];
$pass_api = substr($pass_api,(strlen($id_utente) + 1));
if ($pass == $pass_api) {

$raggiunto_limite = 0;
if (defined('C_LIMITE_CHIAMATE_API_AL_MINUTO') and C_LIMITE_CHIAMATE_API_AL_MINUTO > 0) {
$tabelle_lock = array($tablecache);
$tabelle_lock = lock_tabelle($tabelle_lock);
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$minuto = substr($adesso,0,16);
esegui_query("delete from $tablecache where tipo = 'lim_api' and datainserimento < '$minuto:00' ");
$lim_api = esegui_query("select * from $tablecache where tipo = 'lim_api' and datainserimento >= '$minuto:00' ");
if (!numlin_query($lim_api)) esegui_query("insert into $tablecache (numero,tipo,data_modifica,datainserimento) values ('1','lim_api','$adesso','$adesso') ");
else {
$num_lim_api = risul_query($lim_api,0,'numero');
if ($num_lim_api >= C_LIMITE_CHIAMATE_API_AL_MINUTO) $raggiunto_limite = 1;
else esegui_query("update $tablecache set numero = '".($num_lim_api + 1)."', data_modifica = '$adesso' where tipo = 'lim_api' and datainserimento >= '$minuto:00' ");
} # fine else if (!numlin_query($lim_api))
unlock_tabelle($tabelle_lock);
} # fine if (defined('C_LIMITE_CHIAMATE_API_AL_MINUTO') and C_LIMITE_CHIAMATE_API_AL_MINUTO > 0)
if ($raggiunto_limite) echo "REACHED LIMIT IN LAST MINUTE (".C_LIMITE_CHIAMATE_API_AL_MINUTO.").\n";
else {


if ($res_year) {
if (preg_match("/[0-9]{4,4}/",$res_year)) {
if (@is_file(C_DATI_PATH."/selectperiodi$res_year.1.php")) $anno = $res_year;
else {
if (@is_file(C_DATI_PATH."/selectperiodi".($res_year - 1).".1.php")) $anno = $res_year - 1;
else $res_year = "";
} # fine else if (@is_file(C_DATI_PATH."/selectperiodi$res_year.1.php"))
} # fine if (preg_match("/[0-9]{4,4}/",$res_year))
else $res_year = "";
} # fine if ($res_year)

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
if ($priv_vedi_tab_mesi == "q" or $priv_vedi_tab_mesi == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_costi = substr($priv_vedi_tab,2,1);
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
if ($priv_vedi_tab_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_doc = substr($priv_vedi_tab,7,1);
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_codice = substr($priv_mod_prenota,21,1);
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$priv_vedi_telefoni = substr($priv_ins_clienti,3,1);
$priv_vedi_indirizzo = substr($priv_ins_clienti,4,1);
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
unset($contratto_trovato);
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1] and $contratti_consentiti[$num1] == $numero_contratto) $contratto_trovato = "SI";
if ($contratto_trovato != "SI") $anno_utente_attivato = "NO";
} # fine if ($attiva_contratti_consentiti == "s")
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) $utenti_gruppi[risul_query($utenti_gruppo,$num2,'idutente')] = 1;
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$modifica_pers = "SI";
$priv_vedi_tab_mesi = "s";
$priv_vedi_tab_costi = "s";
$priv_vedi_tab_prenotazioni = "s";
$priv_vedi_tab_doc = "s";
$priv_mod_prenotazioni = "s";
$priv_mod_codice = "s";
$vedi_clienti = "SI";
$priv_vedi_telefoni = "s";
$priv_vedi_indirizzo = "s";
$attiva_contratti_consentiti = "n";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI") {

include("./includes/funzioni_contratti.php");
include("./includes/funzioni_testo.php");
$priv_cancella_contratti = "n";
@include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[$id_utente];
$pag_orig = $pag;
$pag = "visualizza_contratto.php";
$nomi_contratti = trova_nomi_contratti($max_contr,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag);
$nome_contratto = $nomi_contratti['salv'][$numero_contratto];
$cont_salva = "SI";
$sovrascrivi = "";

define('C_ID_UTENTE',$id_utente);
$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '$id_utente'");
if (numlin_query($campi_pers_comm) == 1) {
$campi_pers_comm = explode(">",risul_query($campi_pers_comm,0,'valpersonalizza'));
$num_commenti_pers = count($campi_pers_comm);
} # fine if (numlin_query($campi_pers_comm) == 1)
else $num_commenti_pers = 0;
$campi_pers_cliente = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente' ");
if (numlin_query($campi_pers_cliente)) {
$campi_pers_cliente = explode(">",risul_query($campi_pers_cliente,0,'valpersonalizza'));
$num_campi_pers_cliente = count($campi_pers_cliente);
} # fine if (numlin_query($campi_pers_cliente))
else $num_campi_pers_cliente = 0;
$commento_personalizzato_ = "commento_personalizzato_";
$campo_personalizzato_ = "campo_personalizzato_";
include("./includes/variabili_contratto.php");

$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$query_prenota = "";
$query_clienti = "";
$lista_prenota = 0;
$sec_oggi = time() + (C_DIFF_ORE * 3600);


if ($res_from or $res_to) {
if ($res_from == "today") $res_from = date("Y-m-d",$sec_oggi);
if ($res_from == "tomorrow") $res_from = date("Y-m-d",($sec_oggi + 86400));
if ($res_from == "yesterday") $res_from = date("Y-m-d",($sec_oggi - 86400));
if ($res_to == "today") $res_to = date("Y-m-d",$sec_oggi);
if ($res_to == "tomorrow") $res_to = date("Y-m-d",($sec_oggi + 86400));
if ($res_to == "yesterday") $res_to = date("Y-m-d",($sec_oggi - 86400));
if (!$res_from or !$res_to or $res_from <= $res_to) {
$primo_periodo = 1;
if ($res_from and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_from)) {
$primo_periodo = esegui_query("select idperiodi from $tableperiodi where datainizio >= '$res_from' order by idperiodi ");
if (numlin_query($primo_periodo)) $primo_periodo = risul_query($primo_periodo,0,'idperiodi');
else $primo_periodo = "";
} # fine if ($res_from and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_from))
if ($primo_periodo) {
$ultimo_periodo = "";
if ($res_to and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_to)) {
$ultimo_periodo = esegui_query("select idperiodi from $tableperiodi where datainizio < '$res_to' order by idperiodi desc ");
if (numlin_query($ultimo_periodo)) $ultimo_periodo = risul_query($ultimo_periodo,0,'idperiodi');
else $ultimo_periodo = -1;
} # fine if ($res_to and preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_to))
if (!$ultimo_periodo or $ultimo_periodo >= $primo_periodo) {
$query_prenota = "select idprenota from $tableprenota where iddatafine >= '$primo_periodo'";
if ($ultimo_periodo) $query_prenota .= " and iddatainizio <= '$ultimo_periodo'";
$query_prenota .= " order by iddatainizio";
} # fine if (!$ultimo_periodo or $ultimo_periodo >= $primo_periodo)
} # fine if ($primo_periodo)
} # fine if (!$res_from or !$res_to or $res_from <= $res_to)
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if ($res_from or $res_to)

if ($res_num and !$query_prenota) {
$prenota_in_lista = array();
$res_num = explode(",",$res_num);
$num_res = count($res_num);
for ($num1 = 0 ; $num1 < $num_res ; $num1++) {
$num_pren = $res_num[$num1];
if (str_replace("-","",$num_pren) != $num_pren) {
$num_pren = explode("-",$num_pren);
if (preg_match("/[0-9]+/",$num_pren[0])) $res_ini = $num_pren[0];
else $res_ini = "";
if (preg_match("/[0-9]+/",$num_pren[1])) $res_fine = $num_pren[1];
else $res_fine = "";
if (!$res_ini or !$res_fine or $res_fine >= $res_ini) {
if (!$res_ini) $ini_for = 1;
else $ini_for = $res_ini;
if (!$res_fine) {
$fine_for = esegui_query("select max(idprenota) from $tableprenota ");
if (numlin_query($fine_for)) $fine_for = risul_query($fine_for,0,0);
else $fine_for = 0;
} # fine if (!$res_fine)
else $fine_for = $res_fine;
for ($num2 = $ini_for ; $num2 <= $fine_for ; $num2++) {
if (!$prenota_in_lista[$num2]) {
$prenota_in_lista[$num2] = 1;
$query_prenota .= ",$num2";
} # fine if (!$prenota_in_lista[$num2])
} # fine for $num2
} # fine if (!$res_ini or !$res_fine or $res_fine >= $res_ini)
} # fine if (str_replace("-","",$num_pren) != $num_pren)
else {
if (preg_match("/[0-9]*/",$num_pren) and !$prenota_in_lista[$num_pren]) {
$prenota_in_lista[$num_pren] = 1;
$query_prenota .= ",$num_pren";
} # fine if (preg_match("/[0-9]*/",$num_pren) and !$prenota_in_lista[$num_pren])
} # fine else if (str_replace("-","",$num_pren) != $num_pren)
} # fine for $num1
if ($query_prenota) $lista_prenota = 1;
else $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if ($res_num and !$query_prenota)

if (($res_ins_from or $res_ins_to) and !$query_prenota) {
if ($res_ins_from == "today") $res_ins_from = date("Y-m-d",$sec_oggi);
if ($res_ins_from == "tomorrow") $res_ins_from = date("Y-m-d",($sec_oggi + 86400));
if ($res_ins_from == "yesterday") $res_ins_from = date("Y-m-d",($sec_oggi - 86400));
if ($res_ins_to == "today") $res_ins_to = date("Y-m-d",$sec_oggi);
if ($res_ins_to == "tomorrow") $res_ins_to = date("Y-m-d",($sec_oggi + 86400));
if ($res_ins_to == "yesterday") $res_ins_to = date("Y-m-d",($sec_oggi - 86400));
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_ins_from)) $res_ins_from = "";
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_ins_to)) $res_ins_to = "";
if ($res_ins_from or $res_ins_to) {
if (!$res_ins_from) $res_ins_from = "1980-01-01";
if (!$res_ins_to) $res_ins_to = date("Y-m-d",mktime(0,0,0,date("m"),(date("d") + 2),date("Y")));
if ($res_ins_from <= $res_ins_to) {
$query_prenota = "select idprenota from $tableprenota where datainserimento >= '$res_ins_from 00:00:00' and datainserimento <= '$res_ins_to 23:59:59' order by iddatainizio";
} # fine if ($res_ins_from <= $res_ins_to)
} # fine if ($res_ins_from or $res_ins_to)
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if (($res_ins_from or $res_ins_to) and !$query_prenota)

if ($res_arr and !$query_prenota) {
if ($res_arr == "today") $res_arr = date("Y-m-d",$sec_oggi);
if ($res_arr == "tomorrow") $res_arr = date("Y-m-d",($sec_oggi + 86400));
if ($res_arr == "yesterday") $res_arr = date("Y-m-d",($sec_oggi - 86400));
if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_arr)) {
$arrivo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$res_arr' ");
if (numlin_query($arrivo)) {
$arrivo = risul_query($arrivo,0,'idperiodi');
$query_prenota = "select idprenota from $tableprenota where iddatainizio = '$arrivo' ";
} # fine if (numlin_query($arrivo))
} # fine if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_arr))
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if ($res_arr and !$query_prenota)

if ($res_dep and !$query_prenota) {
if ($res_dep == "today") $res_dep = date("Y-m-d",$sec_oggi);
if ($res_dep == "tomorrow") $res_dep = date("Y-m-d",($sec_oggi + 86400));
if ($res_dep == "yesterday") $res_dep = date("Y-m-d",($sec_oggi - 86400));
if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_dep)) {
$partenza = esegui_query("select idperiodi from $tableperiodi where datafine = '$res_dep' ");
if (numlin_query($partenza)) {
$partenza = risul_query($partenza,0,'idperiodi');
$query_prenota = "select idprenota from $tableprenota where iddatafine = '$partenza' ";
} # fine if (numlin_query())
} # fine if (preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$res_dep))
if (!$query_prenota) $query_prenota = "select idprenota from $tableprenota where iddatainizio < '-3' ";
} # fine if ($res_dep and !$query_prenota)

if ($clients and !$query_prenota) {
if ($clients == "all") $query_clienti = "select idclienti from $tableclienti where max_num_ordine = '1' order by cognome ";
if ($clients == "guests") $query_clienti = "select idclienti from $tableclienti order by cognome ";
if (!$query_clienti) $query_clienti = "select idclienti from $tableclienti where idclienti < '-3'";
} # fine if ($clients and !$query_prenota)

if (!$query_prenota and !$query_clienti) {
if (!$res_year) $res_year = $anno;
$primo_periodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$res_year-01-01' ");
if (numlin_query($primo_periodo)) {
$primo_periodo = risul_query($primo_periodo,0,'idperiodi');
$ultimo_periodo = esegui_query("select max(idperiodi) from $tableperiodi where datainizio <= '$res_year-12-31' ");
$ultimo_periodo = risul_query($ultimo_periodo,0,0);
$query_prenota = "select idprenota from $tableprenota where iddatafine >= '$primo_periodo' and iddatainizio <= '$ultimo_periodo' order by iddatainizio";
} # fine if (numlin_query($primo_periodo))
} # fine if (!$query_prenota and !$query_clienti)


if ($query_prenota or $query_clienti) {
$num_ripeti = 0;

if ($query_prenota and ($priv_vedi_tab_prenotazioni != "n" or ($priv_vedi_tab_mesi != "n" and $priv_mod_prenotazioni != "n"))) {
if ($lista_prenota) {
$lista_prenota = explode(",",$query_prenota);
$num_ripeti = count($lista_prenota) - 1;
} # fine if ($lista_prenota)
else {
$prenotazioni = esegui_query($query_prenota);
$num_ripeti = numlin_query($prenotazioni);
$lista_prenota = array();
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) $lista_prenota[$num1] = risul_query($prenotazioni,($num1 - 1),'idprenota');
chiudi_query($prenotazioni);
} # fine else if ($lista_prenota)
include("./includes/dati_lista_prenota.php");
} # fine if ($query_prenota and ($priv_vedi_tab_prenotazioni != "n" or...


if ($query_clienti and $vedi_clienti != "NO") {
$clienti = esegui_query($query_clienti);
$num_ripeti = numlin_query($clienti);
$lista_clienti = array();
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) $lista_clienti[$num1] = risul_query($clienti,($num1 - 1),'idclienti');
chiudi_query($clienti);
include("./includes/dati_lista_clienti.php");
} # fine if ($query_clienti and $vedi_clienti != "NO")

$mostra_headers = "SI";
$messaggio_di_errore = "";
$num_contr_esist = 0;
$contratto = crea_contratto($numero_contratto,$tipo_contratto,"1","","","");

if ($messaggio_di_errore) {
$tipo_contratto = "contrhtm";
$dir_salva = "";
$contratto = "<div style=\"padding: 5px;\">
<br><span class=\"colred\">".mex("Errore",$pag)."</span>:<br>
<br><div style=\"padding: 0 0 0 10px;\">
$messaggio_di_errore
</div></div>";
} # fine if ($messaggio_di_errore)

if ($dir_salva and !$num_contr_esist) $mostra_headers = "NO";
$foothtm = "";

if ($tipo_contratto == "contrhtm") {
$show_bar = "NO";
$headhtm = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'headhtm'");
if (numlin_query($headhtm) == 1) {
$mostra_headers = "NO";
echo risul_query($headhtm,0,'testo');
$foothtm = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'foothtm'");
if (numlin_query($foothtm)) $foothtm = risul_query($foothtm,0,'testo');
} # fine if (numlin_query($headhtm) == 1)
} # fine if ($tipo_contratto == "contrhtm")
if ($tipo_contratto == "contrrtf") {
$mostra_headers = "NO";
if ($nome_file_contr) $nome_file = $nome_file_contr[$n_file];
else $nome_file = str_replace("\\","_",str_replace("/","_",str_replace(" ","_",$nome_contratto))).".rtf";
if (substr($nome_file,-3) == ".gz") $nome_file = substr($nome_file,0,-3);
header("Pragma: public");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0");
header("Content-Transfer-Encoding: none");
header("Content-Type: application/rtf; name=\"$nome_file\"");
#header("Content-Type: application/octetstream; name=\"$nome_file\"");
#header("Content-Type: application/octet-stream; name=\"$nome_file\"");
header("Content-Disposition: inline; filename=\"$nome_file\"");
#header("Content-length: $lunghezza_file");
} # fine if ($tipo_contratto == "contrrtf")
if ($tipo_contratto == "contrtxt") {
$mostra_headers = "NO";
if ($nome_file_contr) {
$nome_file = $nome_file_contr[$n_file];
if (substr($nome_file,-3) == ".gz") $nome_file = substr($nome_file,0,-3);
} # fine if ($nome_file_contr)
else $nome_file = str_replace("\\","_",str_replace("/","_",str_replace(" ","_",$nome_contratto))).".txt";
$est_txt = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'est_txt'");
if (numlin_query($est_txt)) {
$est_txt = risul_query($est_txt,0,'testo');
$nome_file = substr($nome_file,0,-4).".$est_txt";
} # fine if (numlin_query($est_txt))
header("Pragma: public");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0");
header("Content-Transfer-Encoding: none");
header("Content-Type: application/text; name=\"$nome_file\"");
#header("Content-Type: application/octetstream; name=\"$nome_file\"");
#header("Content-Type: application/octet-stream; name=\"$nome_file\"");
header("Content-Disposition: inline; filename=\"$nome_file\"");
#header("Content-length: $lunghezza_file");
} # fine if ($tipo_contratto == "contrtxt")

if ($mostra_headers == "SI") {
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
} # fine if ($mostra_headers == "SI")

echo $contratto;

if ($mostra_headers == "SI") {
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");
} # fine if ($mostra_headers == "SI")
if ($foothtm) echo $foothtm;

} # fine if ($query_prenota or $query_clienti)

$pag = $pag_orig;
} # fine if ($anno_utente_attivato == "SI")
} # fine else if ($raggiunto_limite)
} # fine if ($pass == $pass_api)
else {
$versione_transazione = prendi_numero_versione($tabletransazioniweb,"idtransazioni","anno");
$adesso = date("YmdHis");
list($usec, $sec) = explode(' ', microtime());
mt_srand((float) $sec + ((float) $usec * 100000));
$val_casuale = mt_rand(100000,999999);
$id_transazione = $adesso.$val_casuale.$versione_transazione;
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tabletransazioniweb (idtransazioni,tipo_transazione,dati_transazione1,dati_transazione2,ultimo_accesso) values ('$id_transazione','api_l','doc$numero_contratto','$REMOTE_ADDR','$ultimo_accesso')");
} # fine else if ($pass == $pass_api)

} # fine else if ($login_sbagliati >= $max_log_sbagliati)
} # fine if (numlin_query($api_esistente))
} # fine if ($doc)





# Conferma sessione da modulo carte di credito
if ($session_data and $id_sessione and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "") {
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1")) {
$cli_pass = 0;
$cli_presfut = 0;
# Non fare lock in scrittura sulle stesse tabelle dalle funzioni delle interconnessioni, altrimenti si blocca quando si attiva cc_hd_token
if ($tweb) $tabelle_lock = array($tabletransazioniweb);
else $tabelle_lock = array($tablesessioni);
if (($cli_id == "pass" or $cli_id == "pres_fut") and !$tweb) {
$anni_esist = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni_esist);
unset($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) $anni[$num1] = risul_query($anni_esist,$num1,'idanni');
$anno_corr = $anno_corrente;
if ($cli_id == "pres_fut") {
# Mantenere carte degli ultimi 4 mesi (120gg)
$data_limite_passato = date("Y-m-d",(time() - (120 * 86400) + (C_DIFF_ORE * 3600)));
$anno_corr = substr($data_limite_passato,0,4);
} # fine if ($cli_id == "pres_fut")
else $data_limite_passato = "";
if ($anni[0] > $anno_corr) $anno_corr = $anni[0];
if ($anni[($num_anni - 1)] < $anno_corr) $anno_corr = $anni[($num_anni - 1)];
$altre_tab_lock = array($tableanni);
$num_lock = 1;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
if ($anni[$num1] >= $anno_corr) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."prenota".$anni[$num1];
$num_lock++;
} # fine if ($anni[$num1] >= $anno_corr)
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
if ($anni[$num1] >= $anno_corr) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."rclientiprenota".$anni[$num1];
$num_lock++;
} # fine if ($anni[$num1] >= $anno_corr)
} # fine for $num1
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."periodi".$anno_corr;
$num_lock++;
$altre_tab_lock[$num_lock] = $tableclienti;
$num_lock++;
$altre_tab_lock[$num_lock] = $tablepersonalizza;
$num_lock++;
$altre_tab_lock[$num_lock] = $tableprivilegi;
$num_lock++;
$altre_tab_lock[$num_lock] = $tablerelgruppi;
} # fine if (($cli_id == "pass" or $cli_id == "pres_fut") and !$tweb)
else {
if (strstr($cli_id,",")) $altre_tab_lock = array($tableclienti,$tablepersonalizza,$tableprivilegi,$tablerelgruppi,$tabletransazioni);
else {
if ($cli_id or $priv_r) $altre_tab_lock = array($tableclienti,$tablepersonalizza,$tableprivilegi,$tablerelgruppi);
else $altre_tab_lock = array($tablepersonalizza);
} # fine else if (strstr($cli_id,","))
} # fine else if (($cli_id == "pass" or $cli_id == "pres_fut") and !$tweb)
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$sessione_trovata = 0;
if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC and $cli_id == "pres_fut") $sessione_trovata = 1;
else {
$minuti_durata_sessione = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_sessione' and idutente = '1'");
$minuti_durata_sessione = risul_query($minuti_durata_sessione,0,'valpersonalizza_num');
$limite_sessioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_sessione * 60) + (C_DIFF_ORE * 3600)));
if ($tweb) {
esegui_query("delete from $tabletransazioniweb where tipo_transazione = 'pay_c' and ultimo_accesso <= '$limite_sessioni_vecchie'");
$sessione = esegui_query("select * from $tabletransazioniweb where tipo_transazione = 'pay_c' and idtransazioni = '".aggslashdb($id_sessione)."' ");
$sessione_trovata = numlin_query($sessione);
} # fine if ($tweb)
else {
esegui_query("delete from $tablesessioni where ultimo_accesso <= '$limite_sessioni_vecchie'");
$sessione = esegui_query("select * from $tablesessioni where idsessioni = '$id_sessione'");
$sessione_trovata = numlin_query($sessione);
} # fine else if ($tweb)
} # fine else if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC and $cli_id == "pres_fut")
if ($sessione_trovata) {

@include(C_DATI_PATH."/lingua.php");
@include(C_DATI_PATH."/tema.php");
if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC) $id_utente = 1;
else {
if ($tweb) {
$id_utente = "t";
$lingua[$id_utente] = $lingua[1];
$tema[$id_utente] = $tema[1];
} # fine if ($tweb)
else $id_utente = risul_query($sessione,0,'idutente');
} # fine else if (defined('C_UTENTE_CANC_CC') and $id_sessione == C_UTENTE_CANC_CC)
echo "<user_id>$id_utente<user_id>
<lang>".$lingua[$id_utente]."</lang>
<theme>".$tema[$id_utente]."</theme>";
if (defined('C_FILE_CSS_PERS') and C_FILE_CSS_PERS != "" and @is_file(C_FILE_CSS_PERS)) echo "<css_pers>".C_FILE_CSS_PERS."</css_pers>";
if (defined('C_FILE_MOB_CSS_PERS') and C_FILE_MOB_CSS_PERS != "" and @is_file(C_FILE_MOB_CSS_PERS)) echo "<mob_css_pers>".C_FILE_MOB_CSS_PERS."</mob_css_pers>";
$idclienti = aggslashdb($cli_id);

if ($priv_r or ($cli_id and $id_utente != 1 and !$tweb)) {
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
$priv_gest_pass_cc = substr($priv_mod_pers,5,1);
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$modifica_clienti = "NO";
if (substr($priv_ins_clienti,1,1) == "s") $modifica_clienti = "SI";
if (substr($priv_ins_clienti,1,1) == "p") $modifica_clienti = "PROPRI";
if (substr($priv_ins_clienti,1,1) == "g") { $modifica_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) $utenti_gruppi[risul_query($utenti_gruppo,$num2,'idutente')] = 1;
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($priv_r or ($cli_id and $id_utente != 1 and !$tweb))

if (($cli_id == "pass" or $cli_id == "pres_fut") and !$tweb) {
if ($cli_id == "pass") $cli_pass = 1;
if ($cli_id == "pres_fut") $cli_presfut = 1;
$cli_id = "";
if ($id_utente != 1 and ($modifica_clienti == "PROPRI" or $modifica_clienti == "GRUPPI")) {
$condizione_utente_canc = "where ( utente_inserimento = '$id_utente'";
if ($modifica_clienti == "GRUPPI") {
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_utente_canc .= " or utente_inserimento = '$idut_gr'";
} # fine if ($modifica_clienti == "GRUPPI")
$condizione_utente_canc .= " )";
} # fine if ($id_utente != 1 and ($modifica_clienti == "PROPRI" or $modifica_clienti == "GRUPPI"))
else $condizione_utente_canc = "";
} # fine if (($cli_id == "pass" or $cli_id == "pres_fut") and !$tweb)

if ($cli_id and $id_utente != 1) {
$cli_priv = 0;
if ($tweb) {
$idclienti_tweb = risul_query($sessione,0,'dati_transazione14');
if ($idclienti_tweb == $idclienti) $cli_priv = 1;
} # fine if ($tweb)
else {
if (strstr($idclienti,",")) {
$limite_transazioni_vecchie = date("Y-m-d H:i:s",(time() - (15 * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tabletransazioni where ultimo_accesso <= '$limite_transazioni_vecchie' and tipo_transazione = 'cn_cc' ");
$transaz_esist = esegui_query("select * from $tabletransazioni where idsessione = '$id_sessione' and tipo_transazione = 'cn_cc' and dati_transazione1 = '$idclienti' ");
if (numlin_query($transaz_esist)) {
$cli_priv = 1;
esegui_query("delete from $tabletransazioni where idsessione = '$id_sessione' and tipo_transazione = 'cn_cc' and dati_transazione1 = '$idclienti' ");
} # fine if (numlin_query($transaz_esist))
} # fine if (strstr($idclienti,","))
else {
if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI") {
$cliente_proprio = esegui_query("select idclienti from $tableclienti where idclienti = '$idclienti' and utente_inserimento = '$id_utente'");
if (numlin_query($cliente_proprio) == 0) $modifica_clienti = "NO";
} # fine if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI")
elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI") {
$cliente_proprio = esegui_query("select utente_inserimento from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($cliente_proprio) == 0) $utente_inserimento = "0";
else $utente_inserimento = risul_query($cliente_proprio,0,'utente_inserimento');
if (!$utenti_gruppi[$utente_inserimento]) $modifica_clienti = "NO";
} # fine elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI")
if ($anno_utente_attivato == "SI" and $modifica_clienti != "NO" and $vedi_clienti != "NO" and $idclienti and controlla_num_pos($idclienti) == "SI") $cli_priv = 1;
} # fine else if (strstr($idclienti,","))
} # fine else if ($tweb)
echo "<cli_priv>$cli_priv</cli_priv>";
} # fine if ($cli_id and $id_utente != 1)

if ($cli_pass or $cli_presfut) {
$idperiodocorrente = (calcola_id_periodo_corrente($anno_corr,"NO",$data_limite_passato) + 1);
unset($cliente_attivo);
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = $anni[$num1];
if ($anno_mostra >= $anno_corr) {
$tableprenota_mostra = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tablerclientiprenota_mostra = $PHPR_TAB_PRE."rclientiprenota".$anno_mostra;
if ($anno_mostra == $anno_corr) $prenota = esegui_query("select idprenota,idclienti from $tableprenota_mostra where iddatafine > '$idperiodocorrente' or (pagato is NULL and tariffa_tot > 0) or pagato < tariffa_tot ");
else  $prenota = esegui_query("select idprenota,idclienti from $tableprenota_mostra");
$num_prenota = numlin_query($prenota);
for ($num2 = 0 ; $num2 < $num_prenota ; $num2++) {
$cliente_attivo[risul_query($prenota,$num2,'idclienti')] = 1;
$idprenota = risul_query($prenota,$num2,'idprenota');
$ospiti = esegui_query("select idclienti from $tablerclientiprenota_mostra where idprenota = '$idprenota' ");
for ($num3 = 0 ; $num3 < numlin_query($ospiti) ; $num3++) $cliente_attivo[risul_query($ospiti,$num3,'idclienti')] = 1;
} # fine for $num2
} # fine ($anno_mostra >= $anno_corr)
} # fine for $num1
if ($cli_pass) {
$clienti = esegui_query("select * from $tableclienti $condizione_utente_canc");
$num_clienti = numlin_query($clienti);
$lista_clienti_canc = "";
for ($num1 = 0; $num1 < $num_clienti; $num1++) {
$idclienti = risul_query($clienti,$num1,'idclienti');
if (!$cliente_attivo[$idclienti]) $lista_clienti_canc .= ",$idclienti";
} # fine for $num1
echo "<cli_pass>".substr($lista_clienti_canc,1)."</cli_pass>";
} # fine if ($cli_pass)
if ($cli_presfut) {
$lista_clienti_att = "";
foreach ($cliente_attivo as $idcli_at => $val) $lista_clienti_att .= ",$idcli_at";
echo "<cli_presfut>".substr($lista_clienti_att,1).";</cli_presfut>";
} # fine if ($cli_presfut)
} # fine if ($cli_pass or $cli_presfut)

if ($priv_r) {
$val_priv_r = "";
if ($priv_r == "priv_gest_pass_cc") $val_priv_r = $priv_gest_pass_cc;
if ($val_priv_r) echo "<privr_$priv_r>$val_priv_r</privr_$priv_r>";
} # fine if ($priv_r)

} # fine if ($sessione_trovata)
unlock_tabelle($tabelle_lock);
} # fine if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1"))
exit();
} # fine if ($session_data and $id_sessione and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "")





# Azioni da modulo carte di credito
if ($azione and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "") {
if ($_SERVER['REMOTE_ADDR']) $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1")) {


if ($azione == "pull_xml" and $tipo_ic == "boo" and $valore_xml and $token) {

$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unset($interconnection_name);
$funz_import_reservations = "";

$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and $mod_ext == $tipo_ic.$ord_ic and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($ic_present[$interconnection_name] == "SI") {
include("./includes/interconnect/$mod_ext/functions_import.php");
$funz_import_reservations = "import_reservations_".$interconnection_func_name;
} # fine if ($ic_present[$interconnection_name] == "SI")
break;
} # fine if ($mod_ext != "." and $mod_ext != ".." and $mod_ext == $tipo_ic.$ord_ic and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
if ($funz_import_reservations) {

if (substr(C_URL_PULL_TOKEN,0,17) == "https://localhost") $token_res_id = file(C_URL_PULL_TOKEN."boo$token.php",false,stream_context_create(array("ssl" => array("verify_peer" => true,"allow_self_signed" => true))));
else $token_res_id = @file(C_URL_PULL_TOKEN."boo$token.php");
if (is_array($token_res_id)) $token_res_id = trim(implode("",$token_res_id));
else $token_res_id = "";
if (stristr($valore_xml,"<id>$token_res_id<")) {

@include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];
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
$id_utente_origi = $id_utente;
$id_utente = 1;
$testo = $funz_import_reservations("","",$file_interconnessioni,$anno,$PHPR_TAB_PRE,2,$id_utente,$HOSTNAME,$valore_xml);
$id_utente = $id_utente_origi;

} # fine if (stristr($valore_xml,"<id>$token_res_id<"))

} # fine if ($funz_import_reservations)
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))
} # fine if ($azione == "pull_xml" and $tipo_ic == "boo" and $valore_xml and $token)


} # fine if ($REMOTE_ADDR == C_IP_MOD_EXT_CARTE_CREDITO or ($REMOTE_ADDR == "::1" and C_IP_MOD_EXT_CARTE_CREDITO == "127.0.0.1"))
} # fine if ($azione and defined('C_IP_MOD_EXT_CARTE_CREDITO') and C_IP_MOD_EXT_CARTE_CREDITO != "")




?>
