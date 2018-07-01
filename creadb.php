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



include("./costanti.php");
include("./includes/funzioni.php");
if (!defined("C_CREADB_TIPODB")) include("./includes/costanti.php");
if (function_exists('ini_set')) @ini_set('opcache.enable',0);
$pag = "creadb.php";
$titolo = "HotelDruid: Crea Database";

if (!defined('C_CREA_ULTIMO_ACCESSO') or C_CREA_ULTIMO_ACCESSO != "SI" or !@is_file(C_DATI_PATH."/ultimo_accesso")) {

unset($numconnessione);
unset($PHPR_TAB_PRE);
unset($id_sessione);
unset($nome_utente_phpr);
unset($password_phpr);
$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente and $id_utente == 1) {


$show_bar = "NO";
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


function mex2 ($messaggio,$pagina,$lingua) {

if ($lingua != "ita") {
include("./includes/lang/$lingua/$pagina");
} # fine if ($lingua != "ita")
elseif ($pagina == "unit.php") include("./includes/unit.php");

return $messaggio;

} # fine function mex2


if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO") $creabase = 1;




if ($creabase and !@is_file(C_DATI_PATH."/dati_connessione.php")) {
$mostra_form_iniziale = "NO";
$insappartamenti = "";

allunga_tempo_limite();
if ($tipo_db == "mysql" and @function_exists('mysqli_connect')) $tipo_db = "mysqli";
$carica_estensione == "NO";
if (($tipo_db == "postgresql" and !@function_exists('pg_connect')) or ($tipo_db == "mysql" and !@function_exists('mysql_connect'))) $carica_estensione == "SI";
if ($tipo_db == "sqlite") {
if (!@class_exists('SQLite3')) $carica_estensione == "SI";
} # fine if ($tipo_db == "sqlite")
if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and (C_UTILIZZA_SEMPRE_DEFAULTS == "SI" or C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO")) {
$tipo_db = C_CREADB_TIPODB;
if ($tipo_db == "mysql" and @function_exists('mysqli_connect')) $tipo_db = "mysqli";
$database_phprdb = C_CREADB_NOMEDB;
$database_esistente = C_CREADB_DB_ESISTENTE;
$host_phprdb = C_CREADB_HOST;
$port_phprdb = C_CREADB_PORT;
$user_phprdb = C_CREADB_USER;
if (!$password_phprdb) $password_phprdb = C_CREADB_PASS;
if (C_CREADB_ESTENSIONE) $carica_estensione = C_CREADB_ESTENSIONE;
$tempdatabase = C_CREADB_TEMPDB;
$prefisso_tab = C_CREADB_PREFISSO_TAB;
} # fine if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and (C_UTILIZZA_SEMPRE_DEFAULTS == "SI" or C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO"))
if (!controlla_num_pos($numletti) == "NO") $numletti = 0;
if ((!$numappartamenti and !$numletti) or controlla_num_pos($numappartamenti) == "NO") $numappartamenti = 5;

if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) {
$HOTELD_DB_TYPE = "";
$HOTELD_DB_NAME = "";
$HOTELD_DB_HOST = "";
$HOTELD_DB_PORT = "";
$HOTELD_DB_USER = "";
$HOTELD_DB_PASS = "";
$HOTELD_TAB_PRE = "";
include(C_EXT_DB_DATA_PATH);
if ($HOTELD_DB_TYPE) {
$tipo_db = $HOTELD_DB_TYPE;
if ($tipo_db == "mysql" and @function_exists('mysqli_connect')) $tipo_db = "mysqli";
} # fine if ($HOTELD_DB_TYPE)
if ($HOTELD_DB_NAME) $database_phprdb = $HOTELD_DB_NAME;
if ($HOTELD_DB_HOST) $host_phprdb = $HOTELD_DB_HOST;
if (strcmp($HOTELD_DB_PORT,"")) $port_phprdb = $HOTELD_DB_PORT;
if ($HOTELD_DB_USER) $user_phprdb = $HOTELD_DB_USER;
if (strcmp($HOTELD_DB_PASS,"")) $password_phprdb = $HOTELD_DB_PASS;
if ($HOTELD_TAB_PRE) $prefisso_tab = $HOTELD_TAB_PRE;
} # fine if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH)

if (!$prefisso_tab or preg_match('/^[_a-z][_0-9a-z]*$/',$prefisso_tab)) {
if ($tipo_db == "postgresql") {
if ($carica_estensione == "SI") dl("pgsql.so");
if ($database_esistente == "SI") $tempdatabase = $database_phprdb;
$numconnessione = pg_connect("dbname=$tempdatabase host=$host_phprdb port=$port_phprdb user=$user_phprdb password=$password_phprdb ");
$encoding = " with encoding = 'SQL_ASCII'";
$encoding = "";
} # fine if ($tipo_db == "postgresql")
if ($tipo_db == "mysql") {
if ($carica_estensione == "SI") dl("mysql.so");
$numconnessione = mysql_connect("$host_phprdb:$port_phprdb", "$user_phprdb", "$password_phprdb");
@mysql_query("SET NAMES 'utf8'");
@mysql_query("SET default_storage_engine=MYISAM");
if ($numconnessione and $database_esistente == "SI") {
$query_db = mysql_select_db($database_phprdb);
if (!$query_db) $numconnessione = $query_db;
} # fine if ($numconnessione and $database_esistente == "SI")
$encoding = "";
} # fine if ($tipo_db == "mysql")
if ($tipo_db == "mysqli") {
if ($carica_estensione == "SI") dl("mysqli.so");
$numconnessione = mysqli_connect($host_phprdb,$user_phprdb,$password_phprdb,"",$port_phprdb);
@mysqli_query($numconnessione,"SET NAMES 'utf8'");
@mysqli_query($numconnessione,"SET default_storage_engine=MYISAM");
if ($numconnessione and $database_esistente == "SI") {
$query_db = mysqli_select_db($numconnessione,$database_phprdb);
if (!$query_db) $numconnessione = $query_db;
} # fine if ($numconnessione and $database_esistente == "SI")
$encoding = "";
} # fine if ($tipo_db == "mysqli")
if ($tipo_db == "sqlite") {
if ($carica_estensione == "SI") dl("sqlite.so");
$database_phprdb = str_replace("..","",$database_phprdb);
$numconnessione = new SQLite3(C_DATI_PATH."/db_".$database_phprdb);
$database_esistente = "SI";
} # fine if ($tipo_db == "sqlite")

if ($numconnessione) {
include("./includes/funzioni_$tipo_db.php");
if ($database_esistente == "NO") {
$link_mysqli = $numconnessione;
$query = esegui_query("create database $database_phprdb $encoding");
if ($query) echo mex2("Database creato",$pag,$lingua)."!<br>";
} # fine if ($database_esistente == "NO")
else $query = "SI";
disconnetti_db($numconnessione);

if ($query) {
$character_set_db = "";
$collation_db = "";
if ($tipo_db == "postgresql") {
$numconnessione = pg_connect("dbname=$database_phprdb host=$host_phprdb port=$port_phprdb user=$user_phprdb password=$password_phprdb ");
} # fine if ($tipo_db == "postgresql")
if ($tipo_db == "mysql" or $tipo_db == "mysqli") {
if ($tipo_db == "mysql") {
$numconnessione = mysql_connect("$host_phprdb:$port_phprdb", "$user_phprdb", "$password_phprdb");
@mysql_query("SET NAMES 'utf8'");
@mysql_query("SET default_storage_engine=MYISAM");
mysql_select_db($database_phprdb);
} # fine if ($tipo_db == "mysql")
if ($tipo_db == "mysqli") {
$numconnessione = mysqli_connect($host_phprdb,$user_phprdb,$password_phprdb,$database_phprdb,$port_phprdb);
$link_mysqli = $numconnessione;
@mysqli_query($numconnessione,"SET NAMES 'utf8'");
@mysqli_query($numconnessione,"SET default_storage_engine=MYISAM");
} # fine if ($tipo_db == "mysqli")
$character_set = esegui_query("SHOW VARIABLES LIKE 'character_set_database'");
$collation = esegui_query("SHOW VARIABLES LIKE 'collation_database'");
if (numlin_query($character_set) == 1 and numlin_query($collation) == 1) {
$character_set_db = risul_query($character_set,0,"Value");
$collation_db = risul_query($collation,0,"Value");
if ($character_set_db != "utf8" or $collation_db != "utf8_general_ci") esegui_query("alter database $database_phprdb default character set 'utf8' collate 'utf8_general_ci'");
} # fine if (numlin_query($character_set) == 1 and...
} # fine if ($tipo_db == "mysql" or $tipo_db == "mysqli")
if ($tipo_db == "sqlite") {
$numconnessione = new SQLite3(C_DATI_PATH."/db_".$database_phprdb);
} # fine if ($tipo_db == "sqlite")

# creo la tabella appartamenti.
$tableappartamenti = $prefisso_tab."appartamenti";
esegui_query("create table $tableappartamenti ( idappartamenti varchar(100) primary key, numpiano text, maxoccupanti integer, numcasa text, app_vicini text, priorita integer, priorita2 integer, letto varchar(1), commento text )");
# creo la tabella clienti.
$tableclienti = $prefisso_tab."clienti";
esegui_query("create table $tableclienti (idclienti integer primary key, cognome varchar(70) not null, nome varchar(70), soprannome varchar(70), sesso char, titolo varchar(30), lingua varchar(14), datanascita date, cittanascita varchar(70), regionenascita varchar(70), nazionenascita varchar(70), documento varchar(70), scadenzadoc date, tipodoc varchar(70), cittadoc varchar(70), regionedoc varchar(70), nazionedoc  varchar(70), nazionalita varchar(70), nazione varchar(70), regione varchar(70), citta varchar(70), via varchar(70), numcivico varchar(30), cap varchar(30), telefono varchar(50), telefono2 varchar(50), telefono3 varchar(50), fax varchar(50), email text, cod_fiscale varchar(50), partita_iva varchar(50), commento text, max_num_ordine integer, idclienti_compagni text, doc_inviati text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
# creo la tabella di relazione tra clienti e dati vari.
$tablerelclienti = $prefisso_tab."relclienti";
esegui_query("create table $tablerelclienti (idclienti integer, numero integer, tipo varchar(12), testo1 text, testo2 text, testo3 text, testo4 text, testo5 text, testo6 text, testo7 text, testo8 text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelclienti,"idclienti",$prefisso_tab."iidprelclienti");
# creo la tabella anni.
$tableanni = $prefisso_tab."anni";
esegui_query("create table $tableanni (idanni integer primary key, tipo_periodi text)");
# creo la tabella versione ed inserisco quella corrente.
$tableversioni = $prefisso_tab."versioni";
esegui_query("create table $tableversioni (idversioni integer primary key, num_versione float4)");
esegui_query("insert into $tableversioni (idversioni, num_versione) values ('1', '".C_PHPR_VERSIONE_NUM."')");
esegui_query("insert into $tableversioni (idversioni, num_versione) values ('2', '100')");
# creo la tabella per la lista delle nazioni.
$tablenazioni = $prefisso_tab."nazioni";
esegui_query("create table $tablenazioni (idnazioni integer primary key, nome_nazione varchar(70), codice_nazione varchar(50), codice2_nazione varchar(50), codice3_nazione varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
# creo la tabella per lista delle regioni (province/stati).
$tableregioni = $prefisso_tab."regioni";
esegui_query("create table $tableregioni (idregioni integer primary key, nome_regione varchar(70), codice_regione varchar(50), codice2_regione varchar(50), codice3_regione varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
# creo la tabella per lista delle città.
$tablecitta = $prefisso_tab."citta";
esegui_query("create table $tablecitta (idcitta integer primary key, nome_citta varchar(70), codice_citta varchar(50), codice2_citta varchar(50), codice3_citta varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
# creo la tabella per lista dei documenti di identità.
$tabledocumentiid = $prefisso_tab."documentiid";
esegui_query("create table $tabledocumentiid (iddocumentiid integer primary key, nome_documentoid varchar(70), codice_documentoid varchar(50), codice2_documentoid varchar(50), codice3_documentoid varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
# creo la tabella per lista delle parentele.
$tableparentele = $prefisso_tab."parentele";
esegui_query("create table $tableparentele (idparentele integer primary key, nome_parentela varchar(70), codice_parentela varchar(50), codice2_parentela varchar(50), codice3_parentela varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
# creo la tabella per le personalizzazioni.
$tablepersonalizza = $prefisso_tab."personalizza";
esegui_query("create table $tablepersonalizza (idpersonalizza varchar(50) not null, idutente integer, valpersonalizza text, valpersonalizza_num integer)");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('col_tab_tutte_prenota','1','nu#@&cg#@&in#@&fi#@&tc#@&ca#@&pa#@&ap#@&pe#@&co')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('rig_tab_tutte_prenota','1','to#@&ta#@&ca#@&pc')");
if (defined("C_MASCHERA_EMAIL") and C_MASCHERA_EMAIL == "SI") $maschera_email = "SI";
else $maschera_email = "NO";
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('maschera_email','1','$maschera_email')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('dati_struttura','1','#@&#@&#@&#@&#@&#@&#@&#@&#@&#@&#@&')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('valuta','1','".aggslashdb(mex2("Euro",$pag,$lingua))."')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('arrotond_predef','1','1')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('arrotond_tasse','1','0.01')");
if ($lingua == "ita" or $lingua == "es" or $lingua == "fr" or $lingua == "de" or $lingua == "pt") $stile_soldi = "europa";
else $stile_soldi = "usa";
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('stile_soldi','1','$stile_soldi')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('costi_agg_in_tab_prenota','1','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('aggiunta_tronca_nomi_tab1','1','-2')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('linee_ripeti_date_tab_mesi','1','25')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('mostra_giorni_tab_mesi','1','SI')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('colori_tab_mesi','1','#70C6D4,#FFD800,#FF9900,#FF3115')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_linee_tab2_prenota','1','30')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('nomi_contratti','1','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_tutte_prenota','1','200')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('selezione_tab_tutte_prenota','1','tutte')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_tutti_clienti','1','200')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_casse','1','50')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('tot_giornalero_tab_casse','1','gior,mens,tab')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_messaggi','1','80')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_doc_salvati','1','100')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_storia_soldi','1','200')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('stile_data','1','europa')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('minuti_durata_sessione','1','90')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('minuti_durata_insprenota','1','10')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('ore_anticipa_periodo_corrente','1','0')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('tutti_fissi','1','10')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('auto_crea_anno','1','SI')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('metodi_pagamento','1','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('origini_prenota','1','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('attiva_checkin','1','NO')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('mostra_quadro_disp','1','reg2')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('ultime_sel_ins_prezzi','1','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('subordinazione','1','NO')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('percorso_cartella_modello','1','".C_DATI_PATH."')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('gest_cvc','1','NO')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('ordine_inventario','1','alf')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('tasti_pos','1','x2;x10;s;+1;+2;+3;+4;+5;+6;+7;+8;+9;s;-1')");
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
$c_cartella_crea_mod = C_CARTELLA_CREA_MODELLI;
if (substr($c_cartella_crea_mod,-1) == "/") $c_cartella_crea_mod = substr($c_cartella_crea_mod,0,-1);
esegui_query("update $tablepersonalizza set valpersonalizza = '$c_cartella_crea_mod' where idpersonalizza = 'percorso_cartella_modello' and idutente = '1'");
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
# creo la tabella degli utenti.
$tableutenti = $prefisso_tab."utenti";
esegui_query("create table $tableutenti (idutenti integer primary key, nome_utente text, password text, salt text, tipo_pass varchar(1), datainserimento $DATETIME, hostinserimento varchar(50) )");
esegui_query("insert into $tableutenti (idutenti,nome_utente,tipo_pass) values ('1','admin','n') ");
http_keep_alive();
# creo la tabella dei gruppi.
$tablegruppi = $prefisso_tab."gruppi";
esegui_query("create table $tablegruppi (idgruppi integer primary key, nome_gruppo text )");
# creo la tabella per i privilegi degli utenti.
$tableprivilegi = $prefisso_tab."privilegi";
esegui_query("create table $tableprivilegi (idutente integer, anno integer, regole1_consentite text, tariffe_consentite text, costi_agg_consentiti text, contratti_consentiti text, casse_consentite text, cassa_pagamenti varchar(70), priv_ins_prenota varchar(20), priv_mod_prenota varchar(35), priv_mod_pers varchar(15), priv_ins_clienti varchar(5), prefisso_clienti text, priv_ins_costi varchar(10), priv_vedi_tab varchar(30), priv_ins_tariffe varchar(10), priv_ins_regole varchar(10), priv_messaggi varchar(10), priv_inventario varchar(10) )");
# creo la tabella per le relazioni tra utenti e loro personalizzazioni di liste.
$tablerelutenti = $prefisso_tab."relutenti";
esegui_query("create table $tablerelutenti (idutente integer not null, idnazione integer, idregione integer, idcitta integer, iddocumentoid integer, idparentela integer, idsup integer, predef integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelutenti,"idutente",$prefisso_tab."iidprelutenti");
$tablerelgruppi = $prefisso_tab."relgruppi";
esegui_query("create table $tablerelgruppi (idutente integer not null, idgruppo integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelgruppi,"idutente",$prefisso_tab."iidprelgruppi");
$tablesessioni = $prefisso_tab."sessioni";
esegui_query("create table $tablesessioni (idsessioni varchar(30) primary key, idutente integer, indirizzo_ip text, tipo_conn varchar(12), user_agent text, ultimo_accesso $DATETIME)");
$tabletransazioni = $prefisso_tab."transazioni";
esegui_query("create table $tabletransazioni (idtransazioni varchar(30) primary key, idsessione varchar(30), tipo_transazione varchar(5), anno integer, spostamenti text, dati_transazione1 text, dati_transazione2 text, dati_transazione3 text, dati_transazione4 text, dati_transazione5 text, dati_transazione6 text, dati_transazione7 text, dati_transazione8 text, dati_transazione9 text, dati_transazione10 text, dati_transazione11 text, dati_transazione12 text, dati_transazione13 text, dati_transazione14 text, dati_transazione15 text, dati_transazione16 text, dati_transazione17 text, dati_transazione18 text, dati_transazione19 text, dati_transazione20 text, ultimo_accesso $DATETIME)");
$tabletransazioniweb = $prefisso_tab."transazioniweb";
esegui_query("create table $tabletransazioniweb (idtransazioni varchar(30) primary key, idsessione varchar(30), tipo_transazione varchar(5), anno integer, spostamenti text, dati_transazione1 text, dati_transazione2 text, dati_transazione3 text, dati_transazione4 text, dati_transazione5 text, dati_transazione6 text, dati_transazione7 text, dati_transazione8 text, dati_transazione9 text, dati_transazione10 text, dati_transazione11 text, dati_transazione12 text, dati_transazione13 text, dati_transazione14 text, dati_transazione15 text, dati_transazione16 text, dati_transazione17 text, dati_transazione18 text, dati_transazione19 text, dati_transazione20 text, ultimo_accesso $DATETIME)");
esegui_query("insert into  $tabletransazioniweb (idtransazioni, anno) values ('2', '100')");
$tablemessaggi = $prefisso_tab."messaggi";
esegui_query("create table $tablemessaggi (idmessaggi integer primary key, tipo_messaggio varchar(8), stato varchar(8), idutenti text, idutenti_visto text, datavisione $DATETIME, mittente text, testo text, dati_messaggio1 text, dati_messaggio2 text, dati_messaggio3 text, dati_messaggio4 text, dati_messaggio5 text, dati_messaggio6 text, dati_messaggio7 text, dati_messaggio8 text, dati_messaggio9 text, dati_messaggio10 text, dati_messaggio11 text, dati_messaggio12 text, dati_messaggio13 text, dati_messaggio14 text, dati_messaggio15 text, dati_messaggio16 text, dati_messaggio17 text, dati_messaggio18 text, dati_messaggio19 text, dati_messaggio20 text, datainserimento $DATETIME )");
$tabledescrizioni = $prefisso_tab."descrizioni";
esegui_query("create table $tabledescrizioni (nome text not null, tipo varchar(16), lingua varchar(3), numero integer, testo $MEDIUMTEXT )");
$tablebeniinventario = $prefisso_tab."beniinventario";
esegui_query("create table $tablebeniinventario (idbeniinventario integer primary key, nome_bene varchar(70), codice_bene varchar(50), descrizione_bene text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tablemagazzini = $prefisso_tab."magazzini";
esegui_query("create table $tablemagazzini (idmagazzini integer primary key, nome_magazzino varchar(70), codice_magazzino varchar(50), descrizione_magazzino text, numpiano text, numcasa text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tablerelinventario = $prefisso_tab."relinventario";
esegui_query("create table $tablerelinventario (idbeneinventario integer not null, idappartamento varchar(100), idmagazzino integer, quantita integer, quantita_min_predef integer, richiesto_checkin varchar(2), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelinventario,"idbeneinventario",$prefisso_tab."iidprelinventario");
# Creo la tabella con le casse
$tablecasse = $prefisso_tab."casse";
esegui_query("create table $tablecasse (idcasse integer primary key, nome_cassa varchar(70), stato varchar(8), codice_cassa varchar(50), descrizione_cassa text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer) ");
esegui_query("insert into $tablecasse (idcasse,datainserimento,hostinserimento,utente_inserimento) values ('1','".date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)))."','$HOSTNAME','1')");
# Creo la tabella con i dati dei documenti
$tablecontratti = $prefisso_tab."contratti";
esegui_query("create table $tablecontratti (numero integer, tipo varchar(8), testo $MEDIUMTEXT )");
# Creo la tabella con la cache per interconnessioni, ecc.
$tablecache = $prefisso_tab."cache";
esegui_query("create table $tablecache (numero integer, tipo varchar(8), testo $MEDIUMTEXT, data_modifica $DATETIME, datainserimento $DATETIME )");
# Creo la tabella con i dati delle interconnessioni
$tableinterconnessioni = $prefisso_tab."interconnessioni";
esegui_query("create table $tableinterconnessioni (idlocale integer, idremoto1 text, idremoto2 text, tipoid varchar(12), nome_ic varchar(24), anno integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");

include("./includes/funzioni_backup.php");
if (defined("C_CARTELLA_FILES_REALI")) $f_pre = C_CARTELLA_FILES_REALI;
else $f_pre = "";
if ($lingua == "ita") $file_contr_backup = $f_pre."./includes/hoteld_doc_backup.php";
else {
if (@is_file($f_pre."./includes/lang/$lingua/hoteld_doc_backup.php")) $file_contr_backup = $f_pre."./includes/lang/$lingua/hoteld_doc_backup.php";
else {
if (@is_file($f_pre."./includes/lang/en/hoteld_doc_backup.php")) $file_contr_backup = $f_pre."./includes/lang/en/hoteld_doc_backup.php";
else $file_contr_backup = $f_pre."./includes/hoteld_doc_backup.php";
} # fine else if (@is_file($f_pre."./includes/lang/$lingua/hoteld_doc_backup.php"))
} # fine else if ($lingua == "ita")
if ($linee_backup = @file($file_contr_backup)) {
ripristina_backup_contr($linee_backup,"SI","crea_backup.php",$prefisso_tab,"rimpiazza");
} # fine if ($linee_backup = @file($file_contr_backup))


# creo i file permanenti.
if ($fileaperto = fopen(C_DATI_PATH."/dati_connessione.php","a+")) {
if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) {
if ($HOTELD_DB_TYPE) $tipo_db = "";
if ($HOTELD_DB_NAME) $database_phprdb = "";
if ($HOTELD_DB_HOST) $host_phprdb = "";
if (strcmp($HOTELD_DB_PORT,"")) $port_phprdb = "";
if ($HOTELD_DB_USER) $user_phprdb = "";
if (strcmp($HOTELD_DB_PASS,"")) $password_phprdb = "";
if ($HOTELD_TAB_PRE) $prefisso_tab = "";
} # fine if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH)
$database_scrivi = aggiungi_slash($database_phprdb);
$host_scrivi = aggiungi_slash($host_phprdb);
$user_scrivi = aggiungi_slash($user_phprdb);
$password_scrivi = aggiungi_slash($password_phprdb);
fwrite($fileaperto,"<?php
\$PHPR_DB_TYPE = \"$tipo_db\";
\$PHPR_DB_NAME = \"$database_scrivi\";
\$PHPR_DB_HOST = \"$host_scrivi\";
\$PHPR_DB_PORT = \"$port_phprdb\";
\$PHPR_DB_USER = \"$user_scrivi\";
\$PHPR_DB_PASS = \"$password_scrivi\";
\$PHPR_LOAD_EXT = \"$carica_estensione\";
\$PHPR_TAB_PRE = \"$prefisso_tab\";
\$PHPR_LOG = \"NO\";
");
if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) fwrite($fileaperto,"
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
fwrite($fileaperto,"?>");
fclose($fileaperto);
@chmod(C_DATI_PATH."/dati_connessione.php", 0640);
if ($lingua != "ita" and (!@is_dir("./includes/lang/".$lingua) or strlen($lingua) > 3)) $lingua = "en";
$fileaperto = fopen(C_DATI_PATH."/lingua.php","w+");
fwrite($fileaperto,"<?php
\$lingua[1] = \"$lingua\";
?>");
fclose($fileaperto);
if ($nomeappartamenti != "appartamenti") $nomeappartamenti = "camere";
$fileaperto = fopen(C_DATI_PATH."/unit.php","w+");
fwrite($fileaperto,"<?php
");
if ($nomeappartamenti == "appartamenti") {
fwrite($fileaperto,"\$unit['s_n'] = \$trad_var['apartment'];
\$unit['p_n'] = \$trad_var['apartments'];
\$unit['gender'] = \$trad_var['apartment_gender'];
");
} # fine if ($nomeappartamenti == "appartamenti")
else {
fwrite($fileaperto,"\$unit['s_n'] = \$trad_var['room'];
\$unit['p_n'] = \$trad_var['rooms'];
\$unit['gender'] = \$trad_var['room_gender'];
");
} # fine else if ($nomeappartamenti == "appartamenti")
fwrite($fileaperto,"\$unit['special'] = 0;
\$car_spec = explode(\",\",\$trad_var['special_characters']);
for (\$num1 = 0 ; \$num1 < count(\$car_spec) ; \$num1++) if (substr(\$unit['p_n'],0,strlen(\$car_spec[\$num1])) == \$car_spec[\$num1]) \$unit['special'] = 1;
?>");
fclose($fileaperto);
$fileaperto = fopen(C_DATI_PATH."/unit_single.php","w+");
fwrite($fileaperto,"<?php
\$unit['s_n'] = \$trad_var['bed'];
\$unit['p_n'] = \$trad_var['beds'];
\$unit['gender'] = \$trad_var['bed_gender'];
\$unit['special'] = 0;
\$car_spec = explode(\",\",\$trad_var['special_characters']);
for (\$num1 = 0 ; \$num1 < count(\$car_spec) ; \$num1++) if (substr(\$unit['p_n'],0,strlen(\$car_spec[\$num1])) == \$car_spec[\$num1]) \$unit['special'] = 1;
?>");
fclose($fileaperto);
$fileaperto = fopen(C_DATI_PATH."/tema.php","w+");
fwrite($fileaperto,"<?php
\$parole_sost = 0;
\$tema[1] = \"blu\";
?>");
fclose($fileaperto);
$fileaperto = fopen(C_DATI_PATH."/versione.php","w+");
fwrite($fileaperto,"<?php
define('C_VERSIONE_ATTUALE',".C_PHPR_VERSIONE_NUM.");
define('C_DIFF_ORE',0);
?>");
fclose($fileaperto);

include("./includes/funzioni_relutenti.php");
aggiorna_relutenti("","SI","","",$id_utente,$id_utente,"","","","","","","","nazione","nazioni",$tablenazioni,$tablerelutenti);
aggiorna_relutenti("","SI","","",$id_utente,$id_utente,"","","","","","","","regione","regioni",$tableregioni,$tablerelutenti,"nazione","nazioni",$tablenazioni);
if (defined('C_CREADB_CITTA_DEFAULT') and C_CREADB_CITTA_DEFAULT == "SI") aggiorna_relutenti("","SI","","",$id_utente,$id_utente,"","","","","","","","citta","citta",$tablecitta,$tablerelutenti,"regione","regioni",$tableregioni);
aggiorna_relutenti("","SI","","",$id_utente,$id_utente,"","","","","","","","documentoid","documentiid",$tabledocumentiid,$tablerelutenti);
aggiorna_relutenti("","SI","","",$id_utente,$id_utente,"","","","","","","","parentela","parentele",$tableparentele,$tablerelutenti);

if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI" and defined('C_CARTELLA_CREA_MODELLI') and @is_file(C_CARTELLA_CREA_MODELLI."/index.html")) @unlink(C_CARTELLA_CREA_MODELLI."/index.html");

if (!defined('C_UTILIZZA_SEMPRE_DEFAULTS') or C_UTILIZZA_SEMPRE_DEFAULTS != "AUTO") {
# seconda form di inserimento (appartamenti).
echo "<br>".mex2("Inserisci ora i dati sugli appartamenti",'unit.php',$lingua)." (<b>".mex2("almeno il numero, diverso per ogni appartamento",'unit.php',$lingua)."</b>).<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creadb.php\"><div>
<input type=\"hidden\" name=\"numappartamenti\" value=\"$numappartamenti\">
<input type=\"hidden\" name=\"numletti\" value=\"$numletti\">
<hr style=\"width: 95%\">";
$zeri = (string) "0000000000000000000000000000";
$lettere = (string) "abcdefghijklmnopqrstuvwxyz";
$pos_lettera = 0;
$num_dorm = $numappartamenti;
if (!$numletti) $num_app_max = $numappartamenti;
else $num_app_max = $numappartamenti + ceil((double) $numletti / 26);
for ( $num = 1; $num <= ($numappartamenti + $numletti) ; $num = $num + 1) {
$numapp = "numapp" . $num;
$piano = "piano" . $num;
$maxoccupanti = "maxoccupanti" . $num;
$numcasa = "numcasa" . $num;
$priorita = "priorita" . $num;
$app_vicini = "app_vicini" . $num;
if ($num <= $numappartamenti) $num_default = (string) substr($zeri,0,(strlen($num_app_max) - strlen($num))).$num;
else {
if ($pos_lettera == 0) {
$num_dorm++;
$num_dorm = (string) substr($zeri,0,(strlen($num_app_max) - strlen($num_dorm))).$num_dorm;
} # fine if ($pos_lettera == 0)
$num_default = $num_dorm.substr($lettere,$pos_lettera,1);
$pos_lettera++;
if ($pos_lettera == 26) $pos_lettera = 0;
} # fine else if ($num <= $numappartamenti)
echo "
$num). ";
if ($num <= $numappartamenti) echo "".mex2("Numero (o nome) dell' appartamento",'unit.php',$lingua).": ";
else echo "".mex2("[1]Numero (o nome) dell' appartamento",'unit.php',$lingua).": ";
echo "<input type=\"text\" name=\"$numapp\" size=\"5\" value=\"$num_default\">
 ".mex2("Massimo numero di occupanti",$pag,$lingua).": ";
if ($num <= $numappartamenti) echo "<input type=\"text\" name=\"$maxoccupanti\" size=\"3\"><br>";
else echo "1<br>";
echo "".mex2("Numero (o nome) piano",$pag,$lingua).": <input type=\"text\" name=\"$piano\" size=\"4\">
 ".mex2("Numero (o nome) casa",$pag,$lingua).": 
<input type=\"text\" name=\"$numcasa\" size=\"4\">
 ".mex2("Priorità (più bassa è, prima viene assegnato)",'unit.php',$lingua).": 
<input type=\"text\" name=\"$priorita\" size=\"3\">";
#echo "<br>Appartamenti vicini (separati da virgole): 
#<input type=\"text\" name=\"$app_vicini\">";
echo "<hr style=\"width: 95%\"><br>";
} # fine for $num
echo "<div style=\"text-align: center;\">";
#echo "<input type=\"checkbox\" name=\"assegna_vicini_nc\" value=\"SI\">
#Assegna come vicini gli appartamenti nella stessa casa (invalida i campi dei singoli appartamenti).<br>
#<input type=\"checkbox\" name=\"assegna_vicini_np\" value=\"SI\">Devono essere anche sullo stesso piano.<br>";
echo "<input class=\"sbutton\" type=\"submit\" name=\"insappartamenti\" value=\"".mex2("Inserisci i dati sugli appartamenti",'unit.php',$lingua)."\">
</div><br></div></form>";
} # fine if (!defined('C_UTILIZZA_SEMPRE_DEFAULTS') or C_UTILIZZA_SEMPRE_DEFAULTS != "AUTO")
else $insappartamenti = 1;

} # fine if ($fileaperto = @fopen(C_DATI_PATH"/dati_connessione.php","a+"))

else {
esegui_query("drop table $tableappartamenti");
esegui_query("drop table $tableclienti");
esegui_query("drop table $tableanni");
esegui_query("drop table $tableversioni");
esegui_query("drop table $tablenazioni");
esegui_query("drop table $tableregioni");
esegui_query("drop table $tablecitta");
esegui_query("drop table $tabledocumentiid");
esegui_query("drop table $tableparentele");
esegui_query("drop table $tablepersonalizza");
esegui_query("drop table $tableutenti");
esegui_query("drop table $tablegruppi");
esegui_query("drop table $tableprivilegi");
esegui_query("drop table $tablerelutenti");
esegui_query("drop table $tablesessioni");
esegui_query("drop table $tabletransazioni");
esegui_query("drop table $tabletransazioniweb");
esegui_query("drop table $tablecontratti");
esegui_query("drop table $tablecache");
esegui_query("drop table $tableinterconnessioni");
esegui_query("drop table $tablemessaggi");
esegui_query("drop table $tabledescrizioni");
esegui_query("drop table $tableinventario");
esegui_query("drop table $tablemagazzini");
esegui_query("drop table $tablerelinventario");
esegui_query("drop table $tablecasse");
esegui_query("drop table $tablerelclienti");
esegui_query("drop table $tablerelgruppi");
disconnetti_db($numconnessione);
if ($database_esistente == "NO") {
sleep(3);
if ($tipo_db == "postgresql") {
$numconnessione = pg_connect("dbname=$tempdatabase host=$host_phprdb port=$port_phprdb user=$user_phprdb password=$password_phprdb ");
} # fine if ($tipo_db == "postgresql")
if ($tipo_db == "mysql") {
$numconnessione = mysql_connect("$host_phprdb:$port_phprdb", "$user_phprdb", "$password_phprdb");
} # fine if ($tipo_db == "mysql")
esegui_query("drop database $database_phprdb");
} # fine if ($database_esistente == "NO")
echo "<br>".mex2("Non ho i permessi di scrittura sulla directory dati, cambiarli e reiniziare l'installazione",$pag,$lingua).".<br>";
$permessi_scrittura_controllati = "SI";
$torna_indietro = "SI";
} # fine else if ($fileaperto = @fopen(C_DATI_PATH."/dati_connessione.php","a+"))

if ($database_esistente != "NO" and $character_set_db and ($character_set_db != "utf8" or $collation_db != "utf8_general_ci")) esegui_query("alter database $database_phprdb default character set '$character_set_db' collate '$collation_db'");
} # fine if ($query)

else {
echo mex2("Non è stato possibile creare il database, controllare i privilegi dell' utente, il nome del database o se esiste già un database chiamato",$pag,$lingua)." $database_phprdb.<br>";
$torna_indietro = "SI";
} # fine else if ($query)
} # fine if ($numconnessione)
else {
echo "<br>".mex2("I dati inseriti per il collegamento al database non sono esatti o il database non è in ascolto",$pag,$lingua);
if ($tipo_db == "postgresql") echo " (".mex2("se postgres assicurarsi che venga avviato con -i e di avere i permessi giusti in pg_hba.conf",$pag,$lingua).")";
echo ".<br>";
$torna_indietro = "SI";
} # fine else if ($numconnessione)
} # fine if (!$prefisso_tab or preg_match('/^[_a-z][_0-9a-z]*$/',$prefisso_tab))
else {
echo "<br>".mex2("Il prefisso del nome delle tabelle è sbagliato (accettate solo lettere minuscole, numeri e _ , primo carattere lettera)",$pag,$lingua).".<br>";
$torna_indietro = "SI";
} # fine else if (!$prefisso_tab or preg_match('/^[_a-z][_0-9a-z]*$/',$prefisso_tab))
if ($permessi_scrittura_controllati != "SI") {
$fileaperto = @fopen(C_DATI_PATH."/prova.tmp","a+");
if (!$fileaperto) echo "<br>".mex2("Non ho i permessi di scrittura sulla directory dati, cambiarli e reiniziare l'installazione",$pag,$lingua).".<br>";
else {
fclose($fileaperto);
unlink(C_DATI_PATH."/prova.tmp");
} # fine else if (!$fileaperto)
} # fine if ($permessi_scrittura_controllati != "SI")
if ($torna_indietro == "SI") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creadb.php\"><div>
<input type=\"hidden\" name=\"lingua\" value=\"$lingua\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex2("Torna indietro",$pag,$lingua)."\"><br>
</div></form>";
} # fine if ($torna_indietro == "SI")
} # fine if ($creabase and !@is_file(C_DATI_PATH."/dati_connessione.php"))




// inserisco i dati forniti nella tabella appartamenti e creo il file selezione appartamenti.
if ($insappartamenti and !@is_file(C_DATI_PATH."/selectappartamenti.php")) {
$mostra_form_iniziale = "NO";
if (!controlla_num_pos($numletti) == "NO") $numletti = 0;
if ((!$numappartamenti and !$numletti) or controlla_num_pos($numappartamenti) == "NO") $numappartamenti = 5;
unset($lingua);
include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];
include(C_DATI_PATH."/dati_connessione.php");
include_once("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$fileaperto = fopen(C_DATI_PATH."/selectappartamenti.php","a+");
fwrite($fileaperto,"<?php \necho \"\n");
$zeri = (string) "0000000000000000000000000000";
$lettere = (string) "abcdefghijklmnopqrstuvwxyz";
$pos_lettera = 0;
$pos_lettera_tot = 0;
$suff_lettera = "";
$num_dorm = $numappartamenti;
if ($lista_camere_letti) {
$lista_camere_letti = explode(",",",$lista_camere_letti");
$num_camera_l = 1;
} # fine if ($lista_camere_letti)
else $num_camera_l = 0;
$app_vicini_vett = array();
if (!$numletti) $num_app_max = $numappartamenti;
else {
if ($num_camera_l) $num_app_max = $numappartamenti + count($lista_camere_letti) - 1;
else $num_app_max = $numappartamenti + ceil((double) $numletti / 26);
} # fine else if (!$numletti)
if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO" and $num_app_max < 10) $num_app_max = 10;

for ($num = 1 ; $num <= ($numappartamenti + $numletti) ; $num = $num + 1) {
$numapp = "numapp" . $num;
$numapp = $$numapp;
if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and (C_UTILIZZA_SEMPRE_DEFAULTS == "SI" or C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO")) {
if ($num <= $numappartamenti) $numapp = (string) substr($zeri,0,(strlen($num_app_max) - strlen($num))).$num;
else {
if ($pos_lettera_tot == 0) {
$num_dorm++;
$num_dorm = (string) substr($zeri,0,(strlen($num_app_max) - strlen($num_dorm))).$num_dorm;
if ($num_camera_l and $lista_camere_letti[$num_camera_l] > 26) $suff_lettera = "a";
} # fine if ($pos_lettera_tot == 0)
$numapp = $num_dorm.$suff_lettera.substr($lettere,$pos_lettera,1);
if ($num_camera_l) {
$app_vicini_vett['pos'][$num] = $numapp;
if ($pos_lettera > 0) {
if ($pos_lettera > 1) {
$app_vicini_vett[($num - 2)] .= ",$numapp";
$app_vicini_vett[$num] .= ",".$app_vicini_vett['pos'][($num - 2)];
} # fine if ($pos_lettera > 1)
$app_vicini_vett[($num - 1)] .= ",$numapp";
$app_vicini_vett[$num] .= ",".$app_vicini_vett['pos'][($num - 1)];
} # fine if ($pos_lettera > 0)
} # fine if ($num_camera_l)
$pos_lettera++;
$pos_lettera_tot++;
if (($pos_lettera == 26 and !$num_camera_l) or ($num_camera_l and $pos_lettera_tot == $lista_camere_letti[$num_camera_l])) {
$pos_lettera = 0;
$pos_lettera_tot = 0;
$suff_lettera = "";
if ($num_camera_l) $num_camera_l++;
} # fine if (($pos_lettera == 26 and !$num_camera_l) or ($num_camera_l and...
if ($pos_lettera >= 26) {
$pos_lettera = 0;
if (!$suff_lettera) $suff_lettera = "a";
else $suff_lettera = substr(strstr($lettere,$suff_lettera),1,1);
} # fine if ($pos_lettera >= 26)
} # fine else if ($num <= $numappartamenti)
} # fine if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and (C_UTILIZZA_SEMPRE_DEFAULTS == "SI" or C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO"))
$numapp = str_replace (",","",$numapp);
if (str_replace (" ","",$numapp) == "") $numapp = str_replace (" ","_",$numapp);
$numapp = trim($numapp);
$numapp = aggslashdb(htmlspecialchars(elimina_caratteri_slash($numapp)));
$piano = "piano" . $num;
$piano = aggslashdb(htmlspecialchars(elimina_caratteri_slash($$piano)));
$maxoccupanti = "maxoccupanti" . $num;
$maxoccupanti = $$maxoccupanti;
if ($num > $numappartamenti) $maxoccupanti = 1;
$numcasa = "numcasa" . $num;
$numcasa = aggslashdb(htmlspecialchars(elimina_caratteri_slash($$numcasa)));
$priorita = "priorita" . $num;
$priorita = $$priorita;
$app_vicini = "app_vicini" . $num;
$app_vicini = aggslashdb(htmlspecialchars($$app_vicini));
if (controlla_num($maxoccupanti) != "SI") unset($maxoccupanti);
if (controlla_num($priorita) != "SI") unset($priorita);
esegui_query("insert into $tableappartamenti ( idappartamenti ) values ( '$numapp' )");
fwrite($fileaperto,"<option value=\\\"$numapp\\\">$numapp</option>
");
if ($piano) {
esegui_query("update $tableappartamenti set numpiano = '$piano' where idappartamenti = '$numapp'");
} # fine if ($piano)
if ($maxoccupanti) {
esegui_query("update $tableappartamenti set maxoccupanti = '$maxoccupanti' where idappartamenti = '$numapp'");
} # fine if ($maxoccupanti)
if ($numcasa) {
esegui_query("update $tableappartamenti set numcasa = '$numcasa' where idappartamenti = '$numapp'");
} # fine if ($numcasa)
if ($priorita) {
esegui_query("update $tableappartamenti set priorita = '$priorita' where idappartamenti = '$numapp'");
} # fine if ($priorita)
if ($app_vicini and $assegna_vicini_nc != "SI") {
esegui_query("update $tableappartamenti set app_vicini = '$app_vicini' where idappartamenti = '$numapp'");
} # fine if ($app_vicini and assegna_vicini_nc != "SI")
if ($num > $numappartamenti) esegui_query("update $tableappartamenti set letto = '1' where idappartamenti = '$numapp'");
} # fine for $num

fwrite($fileaperto,"\"; \n?>");
fclose($fileaperto);
if ($assegna_vicini_nc == "SI") {
$appart = esegui_query("select * from $tableappartamenti");
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1 = $num1 + 1) {
$idapp = risul_query($appart,$num1,'idappartamenti');
$nc = risul_query($appart,$num1,'numcasa');
$np = risul_query($appart,$num1,'numpiano');
$query = "select idappartamenti from $tableappartamenti where numcasa = '$nc' and idappartamenti != '$idapp'";
if ($assegna_vicini_np == "SI") {
$query = $query." and numpiano = '$np'";
} # fine if ($assegna_vicini_np == "SI")
$av = esegui_query($query);
$num_av = numlin_query($av);
$app_vicini = "";
for ( $num2 = 0; $num2 < $num_av; $num2 = $num2 + 1) {
$id_av = risul_query($av,$num2,'idappartamenti');
if ($app_vicini == "") { $app_vicini = $id_av; }
else { $app_vicini = $app_vicini . "," . $id_av; }
} # fine for $num2
esegui_query("update $tableappartamenti set app_vicini = '$app_vicini' where idappartamenti = '$idapp'");
} # fine for $num1
} # fine if ($assegna_vicini_nc == "SI")
if ($num_camera_l) {
for ($num = 1 ; $num <= ($numappartamenti + $numletti) ; $num = $num + 1) {
if ($app_vicini_vett[$num]) {
$app_vicini = substr($app_vicini_vett[$num],1);
esegui_query("update $tableappartamenti set app_vicini = '$app_vicini' where idappartamenti = '".$app_vicini_vett['pos'][$num]."'");
} # fine if ($app_vicini_vett[$num])
} # fine for $num
} # fine if ($num_camera_l)

if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO" and @is_file(C_DATI_PATH."/ini.php")) {
include(C_DATI_PATH."/ini.php");
$admin = "";
if (defined('C_ADMIN_NAME')) $admin = C_ADMIN_NAME;
if (htmlspecialchars($admin) != $admin) $admin = "";
if (strcmp($admin,"")) {
esegui_query("update $tableutenti set nome_utente = '".aggslashdb($admin)."' where idutenti = '1'");
$passw = "";
if (defined('C_ADMIN_PASS')) $passw = C_ADMIN_PASS;
if ($passw != str_replace("&","",$passw)) $passw = "";
if (strcmp($passw,"")) {
if (C_ADMIN_MD5P >= "1" and C_ADMIN_MD5P <= "15") $md5p = C_ADMIN_MD5P;
else $md5p = 0;
if (defined('C_ADMIN_SALT')) $salt = C_ADMIN_SALT;
else {
if ($md5p) $salt = "";
else {
srand((double) microtime() * 1000000);
$valori = "=?#@%abcdefghijkmnpqrstuvwxzABCDEFGHJKLMNPQRSTUVWXZ1234567890";
$salt = substr($valori,rand(0,4),1);
for ($num1 = 0 ; $num1 < 19 ; $num1++) $salt .= substr($valori,rand(0,60),1);
} # fine else if ($md5p)
} # fine else if (defined('C_ADMIN_SALT'))
for ($num1 = $md5p ; $num1 < 15 ; $num1++) $passw = md5($passw.substr($salt,0,(20 - $num1)));
esegui_query("update $tableutenti set password = '$passw', salt = '$salt', tipo_pass = '5' where idutenti = '1'");
$fileaperto = fopen(C_DATI_PATH."/abilita_login","w+");
fclose($fileaperto);
} # fine if (strcmp($passw,""))
} # fine if (strcmp($admin,""))
@unlink(C_DATI_PATH."/ini.php");
} # fine if (defined('C_UTILIZZA_SEMPRE_DEFAULTS') and C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO" and @is_file(C_DATI_PATH."/ini.php"))

$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$testo = "<div style=\"max-width: 600px; line-height: 1.1;\">";
if (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI") $testo .= "<h4>".mex("Benvenuto a HotelDruid!",$pag)."</h4><br>";
$testo .= "".mex("Questi sono alcuni semplici passi che puoi seguire per configurare le funzionalità di base di HotelDruid",$pag).":<br>
<ul style=\"line-height: 1.2;\">
<li>".mex("Inserisci le informazioni sugli appartamenti dalla",'unit.php')."
 <em><b><a href=\"./visualizza_tabelle.php?tipo_tabella=appartamenti&amp;<sessione>\">".mex("tabella appartamenti",'unit.php')."</a></b></em>, 
 ".mex("utilizzando l'apposito tasto al di sotto di essa",$pag).". ".mex("Gli appartamenti possono essere creati, cancellati e rinominati",'unit.php').". 
 ".mex("Si consiglia di inserire almeno la capienza massima per ogni appartamento",'unit.php').".<br><br></li>
<li>".mex("Inserisci il numero di tariffe, un nome per ciascuna di esse ed i prezzi corrispondenti dalla",$pag)." 
 <em><b><a href=\"./creaprezzi.php?<sessione>\">".mex("pagina inserimento prezzi",$pag)."</a></b></em>.
 ".mex("Considera che le tariffe di HotelDruid fungono anche da tipologie di appartamenti",'unit.php')." (".mex("vedi passo successivo",$pag).").<br><br></li>
<li>".mex("Associa una lista di appartamenti ad ogni tariffa, inserendo una regola di assegnazione 2 per ognuna di esse, dalla",'unit.php')." 
 <em><b><a href=\"./crearegole.php?<sessione>#regola2\">".mex("pagina inserimento regole",$pag)."</a></b></em>.
 ".mex("Ogni appatamento può essere associato a più tariffe",'unit.php').".<br><br></li>
<li>".mex("Se questo server web è pubblico si può abilitare il login e creare nuovi utenti dalla",$pag)."
 <em><b><a href=\"./gestione_utenti.php?<sessione>\">".mex("pagina gestione utenti",$pag)."</a></b></em>.<br><br></li>
<li>".mex("Vai alla pagina",$pag)."
 \"<em><b><a href=\"./personalizza.php?<sessione>\">".mex("configura e personalizza",$pag)."</a></b></em>\"
 ".mex("per cambiare il nome della valuta, abilitare la registrazione delle entrate, inserire i metodi di pagamento, ed impostare molte altre opzioni",$pag).".<br><br></li>
</ul></div>";
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") $testo = str_replace("HotelDruid",mex("questo programma",$pag),$testo);
$testo = aggslashdb($testo);
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tablemessaggi (idmessaggi,tipo_messaggio,idutenti,idutenti_visto,datavisione,mittente,testo,datainserimento) values ('1','sistema',',1,',',1,','$datainserimento','1','$testo','$datainserimento')");

echo mex("Dati inseriti",$pag)."!<br>".mex("Tutti i dati permanenti sono stati inseriti",$pag).".<br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"nuovo_mess\" value=\"1\">
<input class=\"sbutton\" type=\"submit\" name=\"ok\" value=\"OK\"><br>
</div></form>";
if (defined('C_CREA_ULTIMO_ACCESSO') and C_CREA_ULTIMO_ACCESSO == "SI") {
$fileaperto = @fopen(C_DATI_PATH."/ultimo_accesso","w+");
@fwrite($fileaperto,date("d-m-Y H:i:s"));
@fclose($fileaperto);
@chmod(C_DATI_PATH."/ultimo_accesso",0644);
} # fine if (defined('C_CREA_ULTIMO_ACCESSO') and C_CREA_ULTIMO_ACCESSO == "SI")
} # fine if ($insappartamenti and !@is_file(C_DATI_PATH."/selectappartamenti.php"))




if ($mostra_form_iniziale != "NO") {

// prima form di inserimento
echo "<h4>".mex2("Inserimento dei dati permanenti",$pag,$lingua)."</h4><br>
".mex2("Inserisci questi dati per poi creare il database",$pag,$lingua).".<br>
<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creadb.php\"><div>
".mex2("Tipo di database",$pag,$lingua).": 
<select name=\"tipo_db\">";
if (C_CREADB_TIPODB == "postgresql") $selected = " selected";
else $selected = "";
echo "<option value=\"postgresql\"$selected>".mex2("Postgresql",$pag,$lingua)."</option>";
if (C_CREADB_TIPODB == "mysql") $selected = " selected";
else $selected = "";
echo "<option value=\"mysql\"$selected>".mex2("Mysql",$pag,$lingua)."</option>";
if (C_CREADB_TIPODB == "sqlite") $selected = " selected";
else $selected = "";
echo "<option value=\"sqlite\"$selected>".mex2("Sqlite",$pag,$lingua)."</option>
</select><br>
".mex2("Nome del database da utilizzare",$pag,$lingua).": 
<input type=\"text\" name=\"database_phprdb\" value=\"".C_CREADB_NOMEDB."\"><br>
".mex2("Database già esistente",$pag,$lingua)."?
<select name=\"database_esistente\">";
if (C_CREADB_DB_ESISTENTE == "SI") $selected = " selected";
else $selected = "";
echo "<option value=\"SI\"$selected>".mex2("Si",$pag,$lingua)."</option>";
if (C_CREADB_DB_ESISTENTE == "NO") $selected = " selected";
else $selected = "";
echo "<option value=\"NO\"$selected>".mex2("No",$pag,$lingua)."</option>
</select><small>(".mex2("Se già esistente e non vuoto usare un prefisso non presente nel database per il nome delle tabelle",$pag,$lingua).")</small><br>
".mex2("Nome del computer a cui collegarsi",$pag,$lingua).":
<input type=\"text\" name=\"host_phprdb\" value=\"".C_CREADB_HOST."\"><br>
".mex2("Numero della porta a cui collegarsi",$pag,$lingua).": 
<input type=\"text\" name=\"port_phprdb\" value=\"".C_CREADB_PORT."\" size=\"7\">(".mex2("Normalmete 5432 o 5433 per Postgresql o 3306 per Mysql",$pag,$lingua).")<br>
".mex2("Nome per l'autenticazione al database",$pag,$lingua).": 
<input type=\"text\" name=\"user_phprdb\" value=\"".C_CREADB_USER."\"><br>
".mex2("Parola segreta per l'autenticazione al database",$pag,$lingua).": 
<input type=\"text\" name=\"password_phprdb\" value=\"".C_CREADB_PASS."\"><br>";
/*echo "".mex2("Caricare la libreria dinamica \"pgsql.so\" o \"mysql.so\"",$pag,$lingua)."?
<select name=\"carica_estensione\">";
if (C_CREADB_ESTENSIONE == "SI") $selected = " selected";
else $selected = "";
echo "<option value=\"SI\"$selected>".mex2("Si",$pag,$lingua)."</option>";
if (C_CREADB_ESTENSIONE == "NO") $selected = " selected";
else $selected = "";
echo "<option value=\"NO\"$selected>".mex2("No",$pag,$lingua)."</option>
</select> <small>(".mex2("scegliere si se non viene caricata automaticamente da php",$pag,$lingua).")</small><br>";*/
if ($lingua == "ita") include("./includes/unit.php");
else include("./includes/lang/$lingua/unit.php");
echo "".mex2("Nome del database a cui collegarsi temporaneamente",$pag,$lingua).":
<input type=\"text\" name=\"tempdatabase\" value=\"".C_CREADB_TEMPDB."\"><small>(".mex2("solo per Postgresql con database non esistente",$pag,$lingua).")</small><br>
".mex2("Prefisso nel nome delle tabelle",$pag,$lingua).":
<input type=\"text\" name=\"prefisso_tab\" value=\"".C_CREADB_PREFISSO_TAB."\" maxlength=\"8\" size=\"9\"><small>(".mex2("opzionale, utile per più installazioni di HotelDruid nello stesso database",$pag,$lingua).")</small><br>
<div style=\"height: 8px\"></div>
".mex2("Nome delle unità da gestire",$pag,$lingua).": <select name=\"nomeappartamenti\">
<option value=\"camere\">".$trad_var['rooms']."</option>
<option value=\"appartamenti\">".$trad_var['apartments']."</option>
</select><br>
".mex2("Numero di unità da gestire",$pag,$lingua).": 
<input type=\"text\" name=\"numappartamenti\" size=\"5\"><br>
<div style=\"height: 6px\"></div>
".mex2("Nome delle unità singole da gestire",$pag,$lingua).": <em>".$trad_var['beds']."</em> <small>(".mex2("non incluse nelle unità normali",$pag,$lingua).")</small><br>
".mex2("Numero di unità singole da gestire",$pag,$lingua).": 
<input type=\"text\" name=\"numletti\" value=\"0\" size=\"5\"><br>
<div style=\"text-align: center;\"><input class=\"sbutton\" type=\"submit\" name=\"creabase\" value=\"".mex2("Crea il database",$pag,$lingua)."\"></div><br>
<input type=\"hidden\" name=\"lingua\" value=\"$lingua\">
</div></form>";

} # fine if ($mostra_form_iniziale != "NO")




if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($id_utente and $id_utente == 1)


} # fine if (!defined('C_CREA_ULTIMO_ACCESSO') or CREA_ULTIMO_ACCESSO != "SI" or !@is_file(C_DATI_PATH."/ultimo_accesso"))


?>
