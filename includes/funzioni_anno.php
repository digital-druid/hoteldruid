<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2016 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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




function crea_nuovo_anno ($anno,$PHPR_TAB_PRE,$DATETIME,$tipo_periodi,$giorno_ini_fine,$mese_ini,$mese_fine,$importa_anno_prec,$silenzio,$pag) {

allunga_tempo_limite();
global $lingua_mex,$LIKE,$ILIKE,$PHPR_DB_TYPE;
$lingua_mex_orig = $lingua_mex;
include_once("./includes/funzioni_menu.php");
include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];
if (function_exists('ini_set')) @ini_set('opcache.enable',0);

$tableanni = $PHPR_TAB_PRE."anni";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tabletransazioniweb = $PHPR_TAB_PRE."transazioniweb";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$tableinterconnessioni  = $PHPR_TAB_PRE."interconnessioni";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";


if ($PHPR_DB_TYPE == "mysql" or $PHPR_DB_TYPE == "mysqli") @esegui_query("SET default_storage_engine=MYISAM",1);

$filelock = fopen(C_DATI_PATH."/anni.lock","w+");
if ($filelock) {
flock($filelock,2);
$anno_esistente = esegui_query("select * from $tableanni where idanni = $anno");
if (numlin_query($anno_esistente) == 0) {

if (defined('C_CAMBIA_TIPO_PERIODI') and C_CAMBIA_TIPO_PERIODI == "NO") {
$tipo_periodi_esistenti = esegui_query("select * from $tableanni order by idanni desc");
if (numlin_query($tipo_periodi_esistenti) != 0) $tipo_periodi = risul_query($tipo_periodi_esistenti,0,'tipo_periodi');
} # fine if (defined('C_CAMBIA_TIPO_PERIODI') and C_CAMBIA_TIPO_PERIODI == "NO")
if ($tipo_periodi != "g") $tipo_periodi = "s";

// creo la tabella con periodi settimanali e prezzi e la tabella con i nomi delle tariffe
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
if ($importa_anno_prec == "SI") {
$anno_prec = $anno - 1;
$tablenometariffe_prec = $PHPR_TAB_PRE."ntariffe".$anno_prec;
$num_tariffe_tab = esegui_query("select nomecostoagg from $tablenometariffe_prec where idntariffe = 1");
$num_tariffe_tab = risul_query($num_tariffe_tab,0,'nomecostoagg');
} # fine if ($importa_anno_prec == "SI")
else $num_tariffe_tab = 8;
$query = "create table $tableperiodi (idperiodi integer primary key, datainizio date not null, datafine date";
$query2 = "create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, valore_perc_ca float8, arrotonda_ca float4, tasseperc_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca text, periodipermessi_ca text, beniinv_ca text, appincompatibili_ca text, variazione_ca varchar(20), mostra_ca varchar(10), categoria_ca text, letto_ca varchar(1), numlimite_ca integer, regoleassegna_ca varchar(30), utente_inserimento integer";
$num_col_tariffe_db = $num_tariffe_tab;
if ($num_col_tariffe_db < 12) $num_col_tariffe_db = 12;
for ($num1 = 1 ; $num1 <= $num_col_tariffe_db ; $num1++) {
$nome_nuova_tariffa = "tariffa" . $num1;
$query .= ", $nome_nuova_tariffa float8, $nome_nuova_tariffa"."p float8";
$query2 .= ", $nome_nuova_tariffa text";
} # fine for $num1

$crea_tab = esegui_query($query.")");
if ($crea_tab) {

esegui_query($query2.")");


function estrai_col_tabella ($col_table) {
$col_table = explode(",",$col_table);
for ($num1 = 0 ; $num1 < count($col_table) ; $num1++) {
$col = explode(" ",trim($col_table[$num1]));
$tutte_col .= $col[0].",";
} # fine for $num1
return substr($tutte_col,0,-1);
} # fine function estrai_col_tabella

# Creo la tabella delle prernotazioni
$tableprenotazioni = $PHPR_TAB_PRE."prenota".$anno;
$col_tableprenota = "idprenota integer primary key, idclienti integer, idappartamenti varchar(100), iddatainizio integer, iddatafine integer, assegnazioneapp varchar(4), app_assegnabili text, num_persone integer, idprenota_compagna text, tariffa text, tariffesettimanali text, incompatibilita text, sconto float8, tariffa_tot float8, caparra float8, commissioni float8, tasseperc float4, pagato float8, metodo_pagamento text, codice varchar(10), origine varchar(70), commento text, conferma varchar(4), checkin $DATETIME, checkout $DATETIME, id_anni_prec text, datainserimento $DATETIME, hostinserimento varchar(50), data_modifica $DATETIME, utente_inserimento integer ";
esegui_query("create table $tableprenotazioni ($col_tableprenota)");
$col_tableprenota = estrai_col_tabella($col_tableprenota);

# Creo la tabella con i costi aggiuntivi delle prenotazioni
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$col_tablecostiprenota = "idcostiprenota integer primary key, idprenota integer, tipo varchar(2), nome varchar(40), valore float8, valore_perc float8, arrotonda float4, tasseperc float4, associasett varchar(1), settimane text, moltiplica text, categoria text, letto varchar(1), numlimite integer, idntariffe integer, variazione varchar(10), varmoltiplica text, varnumsett varchar(20), varperiodipermessi text, varbeniinv text, varappincompatibili text, vartariffeassociate varchar(10), vartariffeincomp text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer";
esegui_query("create table $tablecostiprenota ($col_tablecostiprenota)");
$col_tablecostiprenota = estrai_col_tabella($col_tablecostiprenota);
crea_indice($tablecostiprenota,"idprenota",$PHPR_TAB_PRE."iidpcostiprenota".$anno);
esegui_query("insert into $tablecostiprenota (idcostiprenota,numlimite) values ('1','1')");

# Creo la tabella le relazioni tra prenotazioni e clienti non titolari
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$col_tablerclientiprenota = "idprenota integer, idclienti integer, num_ordine integer, parentela varchar(70), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer";
esegui_query("create table $tablerclientiprenota ($col_tablerclientiprenota)");
$col_tablerclientiprenota = estrai_col_tabella($col_tablerclientiprenota);
crea_indice($tablerclientiprenota,"idprenota",$PHPR_TAB_PRE."iidprclientiprenota".$anno);

# Creo la tabella per le regole di assegnazione
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$col_tableregole = "idregole integer, app_agenzia varchar(100), tariffa_chiusa text, tariffa_per_app text, tariffa_per_utente text, tariffa_per_persone text, tariffa_commissioni integer, iddatainizio integer, iddatafine integer, motivazione text, motivazione2 text, motivazione3 text";
esegui_query("create table $tableregole ($col_tableregole)");
$col_tableregole = estrai_col_tabella($col_tableregole);

# Creo la tabella con i costi di gestione
$tablecosti = $PHPR_TAB_PRE."costi".$anno;
$col_tablecosti = "idcosti integer unique,nome_costo text, val_costo float8, tipo_costo text, nome_cassa varchar(70), persona_costo text, provenienza_costo text, metodo_pagamento text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer";
esegui_query("create table $tablecosti ($col_tablecosti)");
$col_tablecosti = estrai_col_tabella($col_tablecosti);

# Creo la tabella per la storia entrate-uscite
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$col_tablesoldi = "idsoldi integer unique, motivazione text, id_pagamento text, metodo_pagamento text, saldo_prenota float8, saldo_cassa float8, soldi_prima float8, data_inserimento $DATETIME, utente_inserimento integer";
esegui_query("create table $tablesoldi ($col_tablesoldi)");
$col_tablesoldi = estrai_col_tabella($col_tablesoldi);

$tabelle_lock = array($tableanni,$tableprenotazioni,$tablecostiprenota,$tablerclientiprenota,$tablenometariffe,$tableperiodi,$tablerelclienti,$tablecosti,$tableregole,$tablesoldi,$tablepersonalizza,$tableprivilegi,$tabletransazioni);
if ($importa_anno_prec == "SI") {
$anno_prec = $anno - 1;
$tablenometariffe_prec = $PHPR_TAB_PRE."ntariffe".$anno_prec;
$tableprenota_prec = $PHPR_TAB_PRE."prenota".$anno_prec;
$tablecostiprenota_prec = $PHPR_TAB_PRE."costiprenota".$anno_prec;
$tablerclientiprenota_prec = $PHPR_TAB_PRE."rclientiprenota".$anno_prec;
$tableperiodi_prec = $PHPR_TAB_PRE."periodi".$anno_prec;
$tableregole_prec = $PHPR_TAB_PRE."regole".$anno_prec;
$tablesoldi_prec = $PHPR_TAB_PRE."soldi".$anno_prec;
$tablecosti_prec = $PHPR_TAB_PRE."costi".$anno_prec;
$tabelle_lock = array($tableanni,$tableprenota_prec,$tableprenotazioni,$tablecostiprenota_prec,$tablecostiprenota,$tablerclientiprenota_prec,$tablerclientiprenota,$tablenometariffe,$tableperiodi,$tablerelclienti,$tablecosti_prec,$tablecosti,$tableregole,$tablesoldi_prec,$tablesoldi,$tableinterconnessioni,$tablemessaggi,$tablepersonalizza,$tableprivilegi,$tabletransazioni);
$altre_tab_lock = array($tablenometariffe_prec,$tableperiodi_prec,$tableregole_prec,$tablecontratti,$tableutenti);
$max_data_ini_prec = esegui_query("select max(datainizio) from $tableperiodi_prec");
if (numlin_query($max_data_ini_prec) != 0) {
$max_data_ini_prec = risul_query($max_data_ini_prec,0,0);
$max_mese_prec = 0;
if (substr($max_data_ini_prec,0,4) >= $anno) $max_mese_prec = substr($max_data_ini_prec,5,2);
if (substr($max_data_ini_prec,0,4) == ($anno + 1)) $max_mese_prec = $max_mese_prec + 12;
if (substr($max_data_ini_prec,0,4) == ($anno + 2)) $max_mese_prec = $max_mese_prec + 24;
if (substr($max_data_ini_prec,0,4) == ($anno + 3)) $max_mese_prec = $max_mese_prec + 36;
if (substr($max_data_ini_prec,0,4) == ($anno + 4)) $max_mese_prec = $max_mese_prec + 48;
if ($max_mese_prec > $mese_fine) $mese_fine = $max_mese_prec;
$data_ini_agg = date("Y-m-d",mktime(0,0,0,(substr($max_data_ini_prec,5,2) + 1),1,substr($max_data_ini_prec,0,4)));
} # fine if (numlin_query($max_data_ini_prec) != 0)
else {
$max_data_ini_prec = "";
$max_mese_prec = "";
$data_ini_agg = "";
} # fine else if (numlin_query($max_data_ini_prec) != 0)
} # fine if ($importa_anno_prec == "SI")
else unset($altre_tab_lock);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

# Elimino tutte le transazioni, potrebbero contenere date dell'anno creato
esegui_query("delete from $tabletransazioni");

# Inserisco l'anno nella tabella anni
esegui_query("insert into $tableanni (idanni,tipo_periodi) values ('$anno','$tipo_periodi')");

esegui_query("insert into $tablecosti (idcosti) values ('0')");
esegui_query("insert into $tablesoldi (idsoldi,motivazione,soldi_prima) values ('1','soldi_prenotazioni_cancellate','0')");
esegui_query("delete from $tablepersonalizza where idpersonalizza = 'visto_messaggio_periodi' ");

// trovo il primo giorno di inizio/fine dell'anno (nel caso di periodi settimanali)
$numgiorno = 1;
$giorno_ini_fine = (string) $giorno_ini_fine;
if ($giorno_ini_fine != "0" and $giorno_ini_fine != "1" and $giorno_ini_fine != "2" and $giorno_ini_fine != "3" and $giorno_ini_fine != "4" and $giorno_ini_fine != "5" and $giorno_ini_fine != "6") $giorno_ini_fine = 6;
do {
$nomegiorno = date("w",mktime(0,0,0,1,$numgiorno,$anno));
$numgiorno = $numgiorno + 1;
} while ($nomegiorno != $giorno_ini_fine);
$numgiorno  = $numgiorno - 1;

if ($tipo_periodi == "g") {
$numgiorno = 1;
$aggiungi_giorni = 1;
} # fine if ($tipo_periodi == "g")
else $aggiungi_giorni = 7;

if ($mese_ini > $mese_fine) {
if ($silenzio != "SI") echo mex("I mesi erano indicati erroneamente, sono stati inseriti i mesi da Gennaio a Dicembre",$pag).".<br>";
$mese_ini = 1;
if ($mese_fine < 12) $mese_fine = 12;
} # fine if ($mese_ini > $mese_fine)

$anno_fine = $anno + 3;

$idperiodi = 1;
$fileaperto = fopen(C_DATI_PATH."/selectperiodi$anno.1.php","a+");
flock($fileaperto,2);
$date_option = "";
$n_date_menu = 0;

do {
$datainizio = date("Y-m-d",mktime(0,0,0,1,$numgiorno,$anno));
$annocreato = date("Y",mktime(0,0,0,1,$numgiorno,$anno));
$mesecreato = date("n",mktime(0,0,0,1,$numgiorno,$anno));
$nome_giorno = date("D",mktime(0,0,0,1,$numgiorno,$anno));
$nome_mese = date("M",mktime(0,0,0,1,$numgiorno,$anno));
$numero_giorno = date("d",mktime(0,0,0,1,$numgiorno,$anno));
$numero_anno = $annocreato;
$numgiorno = $numgiorno + $aggiungi_giorni;
$datafine = date("Y-m-d",mktime(0,0,0,1,$numgiorno,$anno));
$annocreato2 = date("Y",mktime(0,0,0,1,$numgiorno,$anno));
$mesecreato2 = date("n",mktime(0,0,0,1,$numgiorno,$anno));
if ($annocreato > $anno) {
$diff = $annocreato - $anno;
$mesecreato = ($diff * 12) + $mesecreato;
} # fine if ($annocreato > $anno)
if ($annocreato2 > $anno) {
$diff = $annocreato2 - $anno;
$mesecreato2 = ($diff * 12) + $mesecreato2;
} # fine if ($annocreato2 > $anno)
if ($mesecreato >= $mese_ini and $mesecreato <= $mese_fine) {
esegui_query("insert into $tableperiodi ( idperiodi, datainizio, datafine) values ( $idperiodi, '$datainizio', '$datafine')");
if ($tipo_periodi == "g") {
if ($nome_giorno == "Sun") $nome_giorno = mex(" Do",$pag);
if ($nome_giorno == "Mon") $nome_giorno = mex(" Lu",$pag);
if ($nome_giorno == "Tue") $nome_giorno = mex(" Ma",$pag);
if ($nome_giorno == "Wed") $nome_giorno = mex(" Me",$pag);
if ($nome_giorno == "Thu") $nome_giorno = mex(" Gi",$pag);
if ($nome_giorno == "Fri") $nome_giorno = mex(" Ve",$pag);
if ($nome_giorno == "Sat") $nome_giorno = mex(" Sa",$pag);
} # fine if ($tipo_periodi == "g")
else $nome_giorno = "";
if ($nome_mese == "Jan") $nome_mese = mex("Gen",$pag);
if ($nome_mese == "Feb") $nome_mese = mex("Feb",$pag);
if ($nome_mese == "Mar") $nome_mese = mex("Mar",$pag);
if ($nome_mese == "Apr") $nome_mese = mex("Apr",$pag);
if ($nome_mese == "May") $nome_mese = mex("Mag",$pag);
if ($nome_mese == "Jun") $nome_mese = mex("Giu",$pag);
if ($nome_mese == "Jul") $nome_mese = mex("Lug",$pag);
if ($nome_mese == "Aug") $nome_mese = mex("Ago",$pag);
if ($nome_mese == "Sep") $nome_mese = mex("Set",$pag);
if ($nome_mese == "Oct") $nome_mese = mex("Ott",$pag);
if ($nome_mese == "Nov") $nome_mese = mex("Nov",$pag);
if ($nome_mese == "Dec") $nome_mese = mex("Dic",$pag);
if (!$date_option) {
$a_ini_menu = substr($datainizio,0,4);
$m_ini_menu = (substr($datainizio,5,2) - 1);
$g_ini_menu = substr($datainizio,8,2);
} # fine if (!$date_option)
$n_date_menu++;
$date_option .= "<option value=\\\"$datainizio\\\">$nome_mese $numero_giorno$nome_giorno, $numero_anno</option>
";
$idperiodi = $idperiodi + 1;
$datafine2 = $datafine;
} # fine if ($mesecreato >= $mese_ini and $mesecreato <= $mese_fine)
} while ($annocreato2 <= $anno_fine or $mesecreato2 == 48);

$numero_mese = substr($datafine2,5,2);
if ($numero_mese == "01") $nome_mese = mex("Gen",$pag);
if ($numero_mese == "02") $nome_mese = mex("Feb",$pag);
if ($numero_mese == "03") $nome_mese = mex("Mar",$pag);
if ($numero_mese == "04") $nome_mese = mex("Apr",$pag);
if ($numero_mese == "05") $nome_mese = mex("Mag",$pag);
if ($numero_mese == "06") $nome_mese = mex("Giu",$pag);
if ($numero_mese == "07") $nome_mese = mex("Lug",$pag);
if ($numero_mese == "08") $nome_mese = mex("Ago",$pag);
if ($numero_mese == "09") $nome_mese = mex("Set",$pag);
if ($numero_mese == "10") $nome_mese = mex("Ott",$pag);
if ($numero_mese == "11") $nome_mese = mex("Nov",$pag);
if ($numero_mese == "12") $nome_mese = mex("Dic",$pag);
$numero_giorno = substr($datafine2,8,2);
$numero_anno = substr($datafine2,0,4);
$n_date_menu++;
$date_option .= "<option value=\\\"$datafine2\\\">$nome_mese $numero_giorno, $numero_anno</option>
";
fwrite($fileaperto,"<?php 

\$y_ini_menu = array();
\$m_ini_menu = array();
\$d_ini_menu = array();
\$n_dates_menu = array();
\$d_increment = array();
\$y_ini_menu[0] = \"$a_ini_menu\";
\$m_ini_menu[0] = \"$m_ini_menu\";
\$d_ini_menu[0] = \"$g_ini_menu\";
\$n_dates_menu[0] = \"$n_date_menu\";
\$d_increment[0] = \"$aggiungi_giorni\";
\$d_names = \"\\\"".mex(" Do",$pag)."\\\",\\\"".mex(" Lu",$pag)."\\\",\\\"".mex(" Ma",$pag)."\\\",\\\"".mex(" Me",$pag)."\\\",\\\"".mex(" Gi",$pag)."\\\",\\\"".mex(" Ve",$pag)."\\\",\\\"".mex(" Sa",$pag)."\\\"\";
\$m_names = \"\\\"".mex("Gen",$pag)."\\\",\\\"".mex("Feb",$pag)."\\\",\\\"".mex("Mar",$pag)."\\\",\\\"".mex("Apr",$pag)."\\\",\\\"".mex("Mag",$pag)."\\\",\\\"".mex("Giu",$pag)."\\\",\\\"".mex("Lug",$pag)."\\\",\\\"".mex("Ago",$pag)."\\\",\\\"".mex("Set",$pag)."\\\",\\\"".mex("Ott",$pag)."\\\",\\\"".mex("Nov",$pag)."\\\",\\\"".mex("Dic",$pag)."\\\"\";

\$dates_options_list = \"

$date_option
\";

?>");
flock($fileaperto,3);
fclose($fileaperto);
if ($importa_anno_prec == "SI" and @is_file(C_DATI_PATH."/selperiodimenu".($anno - 1).".1.php")) estendi_menu_date(C_DATI_PATH."/selperiodimenu".($anno - 1).".1.php",C_DATI_PATH."/selperiodimenu$anno.1.php",$tipo_periodi,date("Y-m-d",mktime(0,0,0,$mese_ini,1,$anno)),$data_ini_agg,date("Y-m-d",mktime(0,0,0,($mese_fine + 1),1,$anno)),$anno,$pag);
else copy(C_DATI_PATH."/selectperiodi$anno.1.php",C_DATI_PATH."/selperiodimenu$anno.1.php");

$lista_clienti_importati = "";


# Importo eventuali prenotazioni, tariffe e regole dell'anno precedente
if ($importa_anno_prec == "SI") {
$tableprenota = $tableprenotazioni;
$data_inizio_periodi = esegui_query("select * from $tableperiodi where idperiodi = 1");
$data_inizio_periodi = risul_query($data_inizio_periodi,0,'datainizio');
$ini_periodo_interferenza =  esegui_query("select * from $tableperiodi_prec where datainizio = '$data_inizio_periodi'");
$num_periodo_interferenza = numlin_query($ini_periodo_interferenza);
$tipo_periodi_prec = esegui_query("select * from $tableanni where idanni = $anno_prec");
$tipo_periodi_prec = risul_query($tipo_periodi_prec,0,'tipo_periodi');
if ($tipo_periodi_prec != $tipo_periodi) $num_periodo_interferenza = 0;
if ($num_periodo_interferenza == 0) {
if ($silenzio != "SI") echo mex("Non ci sono periodi che riguardano il ",$pag).$anno.mex(" nel ",$pag).$anno_prec.mex(", o il giorno di inizio/fine locazione era differente, sono stati importati solo i dati riguardanti costi aggiuntivi, caparra, nome delle tariffe, privilegi degli utenti e regole di assegnazione 2 e 3",$pag).".<br>";
} # fine if ($num_periodo_interferenza == 0)
else {

$id_data_fine_periodi = esegui_query("select max(idperiodi) from $tableperiodi");
$id_data_fine_periodi = risul_query($id_data_fine_periodi,0,0);
$id_data_ini_periodi_prec = risul_query($ini_periodo_interferenza,0,'idperiodi');
$id_data_fine_periodi_prec = esegui_query("select max(idperiodi) from $tableperiodi_prec");
$id_data_fine_periodi_prec = risul_query($id_data_fine_periodi_prec,0,0);
$num_periodi_prec = $id_data_fine_periodi_prec - $id_data_ini_periodi_prec + 1;
if ($num_periodi_prec > $id_data_fine_periodi) {
$id_data_fine_periodi_prec = $id_data_ini_periodi_prec + $id_data_fine_periodi - 1;
$prenota_non_importabili = esegui_query("select * from $tableprenota_prec where iddatafine > $id_data_fine_periodi_prec ");
$num_prenota_non_importabili = numlin_query($prenota_non_importabili);
if ($num_prenota_non_importabili != 0) echo "<br>ERROR<br>";
} # fine if ($num_periodi_prec > $id_data_fine_periodi)

$idprenota_corr = 0;
$max_idprenota_prec = esegui_query("select max(idprenota) from $tableprenota_prec");
$max_idprenota_prec = risul_query($max_idprenota_prec,0,0) + 1;
$max_idcostiprenota_prec = esegui_query("select max(idcostiprenota) from $tablecostiprenota_prec");
$max_idcostiprenota_prec = risul_query($max_idcostiprenota_prec,0,0) + 1;
$prenota_gia_iniziate = esegui_query("select * from $tableprenota_prec where iddatainizio < $id_data_ini_periodi_prec and iddatafine >= $id_data_ini_periodi_prec and iddatafine <= $id_data_fine_periodi_prec ");
$num_prenota_gia_iniziate = numlin_query($prenota_gia_iniziate);
for ($num1 = 0 ; $num1 < $num_prenota_gia_iniziate ; $num1 = $num1 + 1) {
$idprenota = risul_query($prenota_gia_iniziate,$num1,'idprenota');
$idclienti = risul_query($prenota_gia_iniziate,$num1,'idclienti');
$lista_clienti_importati .= "and idclienti != '$idclienti' ";
$idappartamenti = risul_query($prenota_gia_iniziate,$num1,'idappartamenti');
$iddatafine = risul_query($prenota_gia_iniziate,$num1,'iddatafine');
$iddatafine = $iddatafine - $id_data_ini_periodi_prec + 1;
$datainserimento = risul_query($prenota_gia_iniziate,$num1,'datainserimento');
$hostinserimento = risul_query($prenota_gia_iniziate,$num1,'hostinserimento');
$data_modifica = risul_query($prenota_gia_iniziate,$num1,'data_modifica');
$utente_inserimento = risul_query($prenota_gia_iniziate,$num1,'utente_inserimento');
$checkin = risul_query($prenota_gia_iniziate,$num1,'checkin');
$checkout = risul_query($prenota_gia_iniziate,$num1,'checkout');
$codice = risul_query($prenota_gia_iniziate,$num1,'codice');
$idprenota_corr++;
esegui_query("insert into $tableprenota (idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,commento,codice,datainserimento,hostinserimento,utente_inserimento) values ('$idprenota_corr','$idclienti','$idappartamenti','0',$iddatafine,'k','$idprenota','$codice','$datainserimento','$hostinserimento','$utente_inserimento') ");
if ($checkin) esegui_query("update $tableprenota set checkin = '".aggslashdb($checkin)."' where idprenota = '$idprenota_corr'");
if ($checkout) esegui_query("update $tableprenota set checkout = '".aggslashdb($checkout)."' where idprenota = '$idprenota_corr'");
esegui_query("update $tablecostiprenota_prec set idprenota = '$max_idprenota_prec' where idprenota = '$idprenota'");
esegui_query("insert into $tablecostiprenota select $col_tablecostiprenota from $tablecostiprenota_prec where idprenota = '$max_idprenota_prec' ");
esegui_query("update $tablecostiprenota set idprenota = '$idprenota_corr' where idprenota = '$max_idprenota_prec'");
esegui_query("update $tablecostiprenota_prec set idprenota = '$idprenota' where idprenota = '$max_idprenota_prec'");
esegui_query("update $tablerclientiprenota_prec set idprenota = '$max_idprenota_prec' where idprenota = '$idprenota'");
esegui_query("insert into $tablerclientiprenota select $col_tablerclientiprenota from $tablerclientiprenota_prec where idprenota = '$max_idprenota_prec' ");
esegui_query("update $tablerclientiprenota set idprenota = '$idprenota_corr' where idprenota = '$max_idprenota_prec'");
esegui_query("update $tablerclientiprenota_prec set idprenota = '$idprenota' where idprenota = '$max_idprenota_prec'");
} # fine for $num1
$nuovo_idprenota = "";
$nuovo_iddatainizio = "";
$nuovo_iddatafine = "";
$prenota_importate = esegui_query("select * from $tableprenota_prec where iddatainizio >= '$id_data_ini_periodi_prec' and iddatafine <= '$id_data_fine_periodi_prec' order by idprenota ");
$num_prenota_importate = numlin_query($prenota_importate);
for ($num1 = 0 ; $num1 < $num_prenota_importate ; $num1 = $num1 + 1) {
$idprenota = risul_query($prenota_importate,$num1,'idprenota');
$iddatainizio = risul_query($prenota_importate,$num1,'iddatainizio');
$iddatainizio = $iddatainizio - $id_data_ini_periodi_prec + 1;
$iddatafine = risul_query($prenota_importate,$num1,'iddatafine');
$iddatafine = $iddatafine - $id_data_ini_periodi_prec + 1;
$idclienti = risul_query($prenota_importate,$num1,'idclienti');
$lista_clienti_importati .= "and idclienti != '$idclienti' ";
$id_anni_prec = risul_query($prenota_importate,$num1,'id_anni_prec');
if (!$id_anni_prec) $id_anni_prec = ";";
$id_anni_prec .= "$anno_prec,$idprenota;";
$idprenota_corr++;
esegui_query("update $tableprenota_prec set idprenota = '$max_idprenota_prec' where idprenota = '$idprenota'");
esegui_query("insert into $tableprenota select $col_tableprenota from $tableprenota_prec where idprenota = '$max_idprenota_prec' ");
esegui_query("update $tableprenota set iddatainizio = '$iddatainizio' where idprenota = '$max_idprenota_prec'");
esegui_query("update $tableprenota set iddatafine = '$iddatafine' where idprenota = '$max_idprenota_prec'");
esegui_query("update $tableprenota set id_anni_prec = '$id_anni_prec' where idprenota = '$max_idprenota_prec'");
esegui_query("update $tableprenota set idprenota = '$idprenota_corr' where idprenota = '$max_idprenota_prec'");
esegui_query("delete from $tableprenota_prec where idprenota = '$max_idprenota_prec'");
esegui_query("update $tablecostiprenota_prec set idprenota = '$max_idprenota_prec' where idprenota = '$idprenota'");
esegui_query("insert into $tablecostiprenota select $col_tablecostiprenota from $tablecostiprenota_prec where idprenota = '$max_idprenota_prec' ");
esegui_query("update $tablecostiprenota set idprenota = '$idprenota_corr' where idprenota = '$max_idprenota_prec'");
esegui_query("delete from $tablecostiprenota_prec where idprenota = '$max_idprenota_prec'");
esegui_query("update $tablerclientiprenota_prec set idprenota = '$max_idprenota_prec' where idprenota = '$idprenota'");
esegui_query("insert into $tablerclientiprenota select $col_tablerclientiprenota from $tablerclientiprenota_prec where idprenota = '$max_idprenota_prec' ");
esegui_query("update $tablerclientiprenota set idprenota = '$idprenota_corr' where idprenota = '$max_idprenota_prec'");
esegui_query("delete from $tablerclientiprenota_prec where idprenota = '$max_idprenota_prec'");
esegui_query("update $tableinterconnessioni set idlocale = '$idprenota_corr', anno = '$anno' where idlocale = '$idprenota' and ( tipoid = 'prenota' or tipoid = 'mess' ) and anno = '$anno_prec' ");
$nuovo_idprenota[$idprenota] = $idprenota_corr;
$nuovo_iddatainizio[$idprenota] = $iddatainizio;
$nuovo_iddatafine[$idprenota] = $iddatafine;
} # fine for $num1
esegui_query("update $tablecostiprenota set numlimite = '".($idprenota_corr + 1)."' where idcostiprenota = '1'");
$prenota_compagne = esegui_query("select idprenota,idprenota_compagna from $tableprenota where idprenota_compagna != '' ");
$num_prenota_compagne = numlin_query($prenota_compagne);
for ($num1 = 0 ; $num1 < $num_prenota_compagne ; $num1++) {
$idprenota = risul_query($prenota_compagne,$num1,'idprenota');
$idprenota_compagna = risul_query($prenota_compagne,$num1,'idprenota_compagna');
$idprenota_compagna = explode(",",$idprenota_compagna);
$n_idprenota_compagna = $nuovo_idprenota[$idprenota_compagna[0]];
for ($num2 = 1 ; $num2 < count($idprenota_compagna) ; $num2++) $n_idprenota_compagna .= ",".$nuovo_idprenota[$idprenota_compagna[$num2]];
esegui_query("update $tableprenota set idprenota_compagna = '$n_idprenota_compagna' where idprenota = '$idprenota'");
} # fine for $num1

$id_costi_agg_importati = esegui_query("select idcostiprenota from $tablecostiprenota where idcostiprenota != '1' order by idcostiprenota");
$num_id_costi_agg_importati = numlin_query($id_costi_agg_importati);
for ($num1 = 0 ; $num1 < $num_costi_agg_importati ; $num1++) {
$idcostiprenota = risul_query($id_costi_agg_importati,$num1,'idcostiprenota');
$max_idcostiprenota_prec++;
esegui_query("update $tablecostiprenota set idcostiprenota = '$max_idcostiprenota_prec' where idcostiprenota = '$idcostiprenota'");
} # fine for $num1
$costi_agg_importati = esegui_query("select * from $tablecostiprenota where idcostiprenota != '1' order by idcostiprenota");
$num_costi_agg_importati = numlin_query($costi_agg_importati);
for ($num1 = 0 ; $num1 < $num_costi_agg_importati ; $num1++) {
$idcostiprenota = risul_query($costi_agg_importati,$num1,'idcostiprenota');
$settimane = risul_query($costi_agg_importati,$num1,'settimane');
if (str_replace(",","",$settimane) != $settimane) {
$settimane = explode(",",$settimane);
$settimane_nuove = ",";
for ($num2 = 1 ; $num2 < (count($settimane) - 1) ; $num2++) $settimane_nuove .= ($settimane[$num2] - $id_data_ini_periodi_prec + 1).",";
esegui_query("update $tablecostiprenota set settimane = '$settimane_nuove' where idcostiprenota = '$idcostiprenota'");
} # fine if (str_replace(",","",$settimane) != $settimane)
$varperiodipermessi = risul_query($costi_agg_importati,$num1,'varperiodipermessi');
if ($varperiodipermessi) {
$vpp_nuovi = substr($varperiodipermessi,0,1);
$varperiodipermessi = explode(",",substr($varperiodipermessi,1));
for ($num2 = 0 ; $num2 < count($varperiodipermessi) ; $num2++) {
$id_data_fine_vpp = explode("-",$varperiodipermessi[$num2]);
$id_data_inizio_vpp = $id_data_fine_vpp[0];
$id_data_fine_vpp = $id_data_fine_vpp[1];
if ($id_data_fine_vpp >= $id_data_ini_periodi_prec and $id_data_inizio_vpp <= $id_data_fine_periodi_prec) {
if ($id_data_inizio_vpp < $id_data_ini_periodi_prec) $id_data_inizio_vpp = $id_data_ini_periodi_prec;
if ($id_data_fine_vpp > $id_data_fine_periodi_prec) $id_data_fine_vpp = $id_data_fine_periodi_prec;
$id_data_inizio_vpp = $id_data_inizio_vpp - $id_data_ini_periodi_prec + 1;
$id_data_fine_vpp = $id_data_fine_vpp - $id_data_ini_periodi_prec + 1;
$vpp_nuovi .= $id_data_inizio_vpp."-".$id_data_fine_vpp.",";
} # fine if ($id_data_fine_vpp >= $id_data_ini_periodi_prec and...
} # fine for $num2
if (strlen($vpp_nuovi) > 1) $vpp_nuovi = substr($vpp_nuovi,0,-1);
esegui_query("update $tablecostiprenota set varperiodipermessi = '$vpp_nuovi' where idcostiprenota = '$idcostiprenota'");
} # fine if ($varperiodipermessi)
esegui_query("update $tablecostiprenota set idcostiprenota = '".($num1 + 2)."' where idcostiprenota = '$idcostiprenota'");
} # fine for $num1

$tariffe_importate = esegui_query("select * from $tableperiodi_prec where idperiodi >= '$id_data_ini_periodi_prec' and idperiodi <= '$id_data_fine_periodi_prec' ");
$num_tariffe_iportate = numlin_query($tariffe_importate);
for ($num1 = 0 ; $num1 < $num_tariffe_iportate ; $num1 = $num1 + 1) {
$idperiodi = risul_query($tariffe_importate,$num1,'idperiodi');
$idperiodi = $idperiodi - $id_data_ini_periodi_prec + 1;
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$tariffa = (string) risul_query($tariffe_importate,$num1,"tariffa".$num2);
if (strcmp($tariffa,"")) esegui_query("update $tableperiodi set tariffa$num2 = '$tariffa' where idperiodi = $idperiodi ");
$tariffap = (string) risul_query($tariffe_importate,$num1,"tariffa".$num2."p");
if (strcmp($tariffap,"")) esegui_query("update $tableperiodi set tariffa$num2"."p = '$tariffap' where idperiodi = $idperiodi ");
} # fine for $num2
} # fine for $num1

$regole_prec = esegui_query("select * from $tableregole_prec where iddatafine >= '$id_data_ini_periodi_prec' ");
$num_regole_prec = numlin_query($regole_prec);
for ($num1 = 0 ; $num1 < $num_regole_prec ; $num1 = $num1 + 1) {
$idregole = risul_query($regole_prec,$num1,'idregole');
$app_agenzia = risul_query($regole_prec,$num1,'app_agenzia');
$tariffa_chiusa = risul_query($regole_prec,$num1,'tariffa_chiusa');
$tariffa_commissioni = risul_query($regole_prec,$num1,'tariffa_commissioni');
$iddatainizio = risul_query($regole_prec,$num1,'iddatainizio');
$motivazione = risul_query($regole_prec,$num1,'motivazione');
$motivazione2 = risul_query($regole_prec,$num1,'motivazione2');
$motivazione3 = risul_query($regole_prec,$num1,'motivazione3');
if ($iddatainizio < $id_data_ini_periodi_prec) { $iddatainizio = $id_data_ini_periodi_prec; }
$iddatainizio = $iddatainizio - $id_data_ini_periodi_prec + 1;
$iddatafine = risul_query($regole_prec,$num1,'iddatafine');
if ($iddatafine > $id_data_fine_periodi_prec) { $iddatafine = $id_data_fine_periodi_prec; }
$iddatafine = $iddatafine - $id_data_ini_periodi_prec + 1;
if ($tariffa_commissioni) esegui_query("insert into $tableregole (idregole,tariffa_commissioni,iddatainizio,iddatafine) values ('$idregole','$tariffa_commissioni','$iddatainizio','$iddatafine') ");
else {
if ($tariffa_chiusa) esegui_query("insert into $tableregole (idregole,tariffa_chiusa,iddatainizio,iddatafine) values ('$idregole','$tariffa_chiusa','$iddatainizio','$iddatafine') ");
else esegui_query("insert into $tableregole (idregole,app_agenzia,iddatainizio,iddatafine) values ('$idregole','$app_agenzia','$iddatainizio','$iddatafine') ");
} # fine else $tariffa_commissioni)
if (strcmp($motivazione,"")) esegui_query("update $tableregole set motivazione = '".aggslashdb($motivazione)."' where idregole = '$idregole' ");
if (strcmp($motivazione2,"")) esegui_query("update $tableregole set motivazione2 = '".aggslashdb($motivazione2)."' where idregole = '$idregole' ");
if (strcmp($motivazione3,"")) esegui_query("update $tableregole set motivazione3 = '".aggslashdb($motivazione3)."' where idregole = '$idregole' ");
} # fine for $num1

$soldi_importati = esegui_query("select * from $tablesoldi_prec where data_inserimento >= '$data_inizio_periodi' and  saldo_prenota is not NULL order by idsoldi");
$num_soldi_importati = numlin_query($soldi_importati);
$idsoldi_corr = 1;
for ($num1 = 0 ; $num1 < $num_soldi_importati ; $num1++) {
$motiv = explode(";",risul_query($soldi_importati,$num1,'motivazione'));
if ($nuovo_idprenota[$motiv[3]]) {
$idsoldi_corr++;
$idsoldi_prec = risul_query($soldi_importati,$num1,'idsoldi');
$n_motiv = $motiv[0].";".$nuovo_iddatainizio[$motiv[3]].";".$nuovo_iddatafine[$motiv[3]].";".$nuovo_idprenota[$motiv[3]];
esegui_query("insert into $tablesoldi select $col_tablesoldi from $tablesoldi_prec where idsoldi = '$idsoldi_prec' ");
esegui_query("delete from $tablesoldi_prec where idsoldi = '$idsoldi_prec' ");
esegui_query("update $tablesoldi set motivazione = '$n_motiv' where idsoldi = '$idsoldi_prec' ");
esegui_query("update $tablesoldi set idsoldi = '$idsoldi_corr' where idsoldi = '$idsoldi_prec' ");
} # fine if ($nuovo_idprenota[$motiv[3]])
} # fine for $num1

# aggiorno i messaggi con prenotazioni del nuovo anno già inserite, quelli da inserire sono aggiornati con i modelli internet
$messaggi_importati = esegui_query("select * from $tablemessaggi where stato = 'ins' and dati_messaggio18 = '$anno_prec' ");
$num_messaggi_importati = numlin_query($messaggi_importati);
for ($num1 = 0 ; $num1 < $num_messaggi_importati ; $num1++) {
$agg_mess = 1;
$n_lista_pren = "";
$lista_pren = explode(",",risul_query($messaggi_importati,$num1,'dati_messaggio1'));
for ($num2 = 0 ; $num2 < count($lista_pren) ; $num2++) {
if ($nuovo_idprenota[$lista_pren[$num2]]) $n_lista_pren .= $nuovo_idprenota[$lista_pren[$num2]].",";
else $agg_mess = 0;
} # fine for $num2
if ($agg_mess) {
$idmess = risul_query($messaggi_importati,$num1,'idmessaggi');
$n_lista_pren = substr($n_lista_pren,0,-1);
esegui_query("update $tablemessaggi set dati_messaggio1 = '$n_lista_pren', dati_messaggio18 = '$anno' where idmessaggi = '$idmess' ");
} # fine if ($agg_mess)
} # fine for $num1

# aggiorno i numeri delle prenotazioni importate nei nomi dei documenti già creati nel nuovo anno
$dirs_salva = esegui_query("select * from $tablecontratti where tipo = 'dir'");
$num_dirs_salva = numlin_query($dirs_salva);
if ($num_dirs_salva) {
unset($contr_salva_in);
$max_contr = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'contr%'");
$max_contr = risul_query($max_contr,0,0);
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
if (C_CARTELLA_DOC != "" and @is_dir(C_CARTELLA_CREA_MODELLI."/".C_CARTELLA_DOC)) $dir_salva_home = C_CARTELLA_DOC;
else $dir_salva_home = "";
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else $dir_salva_home = C_DATI_PATH;
$utenti = esegui_query("select * from ".$PHPR_TAB_PRE."utenti order by idutenti");
$num_utenti = numlin_query($utenti);
unset($nomi_contr_ut);
$parola_documento = mex("documento",'visualizza_contratto.php');
for ($num1 = 0 ; $num1 < $num_utenti ; $num1++) {
$idutente_contr = risul_query($utenti,$num1,'idutenti');
for ($num2 = 1 ; $num2 <= $max_contr ; $num2++) $nomi_contr_ut[$idutente_contr][$num2] = $parola_documento.$num2;
$nomi_contratti = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '$idutente_contr'");
$nomi_contratti = risul_query($nomi_contratti,0,'valpersonalizza');
$nomi_contratti = explode("#@&",$nomi_contratti);
$num_nomi_contratti = count($nomi_contratti);
for ($num2 = 0 ; $num2 < $num_nomi_contratti ; $num2++) {
$dati_nome_contratto = explode("#?&",$nomi_contratti[$num2]);
$nomi_contr_ut[$idutente_contr][$dati_nome_contratto[0]] = $dati_nome_contratto[1];
} # fine for $num2
} # fine for $num1
unset($prefissi_contr);
for ($num1 = 0 ; $num1 < $num_dirs_salva ; $num1++) {
$dir_salva = risul_query($dirs_salva,$num1,'testo');
if ($dir_salva == "~") $dir_salva = $dir_salva_home;
if (defined('C_CARTELLA_CREA_MODELLI') and C_CARTELLA_CREA_MODELLI != "") $dir_salva = C_CARTELLA_CREA_MODELLI."/".str_replace("..","",$dir_salva);
if (@is_dir($dir_salva)) {
$num_contr = risul_query($dirs_salva,0,'numero');
$tipo_contratto = esegui_query("select tipo from ".$PHPR_TAB_PRE."contratti where numero = '$num_contr' and tipo $LIKE 'contr%' ");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
$suff_file = "html";
if ($tipo_contratto == "contrrtf") $suff_file = "rtf";
if ($tipo_contratto == "contrtxt") $suff_file = "txt";
$filelock_contr = fopen($dir_salva."/crea_contr.lock","w+");
flock($filelock_contr,2);
for ($num2 = 0 ; $num2 < $num_utenti ; $num2++) {
$idutente_contr = risul_query($utenti,$num2,'idutenti');
if (!$prefissi_contr[$dir_salva."/".$nomi_contr_ut[$idutente_contr][$num_contr]]) {
$nome_contratto = $nomi_contr_ut[$idutente_contr][$num_contr];
$prefissi_contr[$dir_salva."/".$nome_contratto] = 1;
$contr_dir = opendir($dir_salva."/");
while ($contr_corr = readdir($contr_dir)) {
if ($contr_corr != "." and $contr_corr != ".." and is_file($dir_salva."/".$contr_corr)) {
if (substr($contr_corr,0,strlen($nome_contratto)) == $nome_contratto) {
$resto_nome_contr = substr($contr_corr,strlen($nome_contratto));
if (substr($resto_nome_contr,0,6) == "_$anno"."_") {
if (preg_replace("/_[0-9]{4,4}_[0-9]{5,5}(_[0-9]+(-[0-9]+)?)*\.$suff_file/","",$resto_nome_contr) == "") {
$num_pren_esist = substr($resto_nome_contr,12);
$num_pren_esist = substr($num_pren_esist,0,(-1 * (strlen($suff_file) + 1)));
if ($num_pren_esist) {
$agg_contr = 1;
$n_lista_pren = "";
$num_pren_esist = explode("_",$num_pren_esist);
for ($num3 = 0 ; $num3 < count($num_pren_esist) ; $num3++) {
$num_pren_esist2 = explode("-",$num_pren_esist[$num3]);
$fine_for = $num_pren_esist2[(count($num_pren_esist2) - 1)];
for ($num4 = $num_pren_esist2[0] ; $num4 <= $fine_for ; $num4++) {
if (!$nuovo_idprenota[$num4]) {
$agg_contr = 0;
break;
} # fine if (!$nuovo_idprenota[$num4])
if ($num4 == $num_pren_esist2[0]) $n_lista_pren .= "_".$nuovo_idprenota[$num4];
elseif ($num4 == $fine_for) $n_lista_pren .= "-".$nuovo_idprenota[$num4];
} # fine for $num4
} # fine for $num3
if ($agg_contr) {
$n_nome_contr = $nome_contratto.substr($resto_nome_contr,0,11).$n_lista_pren.".$suff_file";
rename($dir_salva."/".$contr_corr,$dir_salva."/".$n_nome_contr);
} # fine if ($agg_contr)
} # fine if ($num_pren_esist)
} # fine if (preg_replace("/_[0-9]{4,4}_[0-9]{5,5}\.$suff_file/","",$resto_nome_contr) == "")
} # fine if (substr($resto_nome_contr,0,6) == "_$anno"."_")
} # fine if (substr($contr_corr,0,strlen($nome_contratto)) == $nome_contratto)
} # fine if ($contr_corr != "." and $contr_corr != ".." and...
} # fine while ($fattura_corr = readdir($fatture_dir))
closedir($contr_dir);
} # fine if (!$prefissi_contr[$dir_salva."/".$nomi_contr_ut[$idutente_contr][$num_contr]])
} # fine for $num2
flock($filelock_contr,3);
fclose($filelock_contr);
unlink($dir_salva."/crea_contr.lock");
} # fine if (@is_dir($dir_salva))
} # fine for $num1
} # fine if ($num_dirs_salva)

} # fine else if ($num_periodo_interferenza == 0)


$costi_importati = esegui_query("select * from $tablecosti_prec where datainserimento >= '$data_inizio_periodi' and  tipo_costo is not NULL order by idcosti");
$num_costi_importati = numlin_query($costi_importati);
$idcosti_corr = 0;
for ($num1 = 0 ; $num1 < $num_costi_importati ; $num1++) {
$idcosti_corr++;
$idcosti_prec = risul_query($costi_importati,$num1,'idcosti');
esegui_query("insert into $tablecosti select $col_tablecosti from $tablecosti_prec where idcosti = '$idcosti_prec' ");
esegui_query("delete from $tablecosti_prec where idcosti = '$idcosti_prec' ");
esegui_query("update $tablecosti set idcosti = '$idcosti_corr' where idcosti = '$idcosti_prec' ");
} # fine for $num1
$costo_cassa = 0;
$data_lim = ($anno + 1)."-01-01 00:00:00";
$costi_cassa = esegui_query("select * from $tablecosti where tipo_costo = 'e' and provenienza_costo = 'p' and datainserimento < '$data_lim' ");
$num_costi_cassa = numlin_query($costi_cassa);
for ($num2 = 0 ; $num2 < $num_costi_cassa ; $num2++) $costo_cassa = $costo_cassa + risul_query($costi_cassa,$num2,'val_costo');
if ($costo_cassa) esegui_query("update $tablecosti set val_costo = '$costo_cassa' where idcosti = '0' ");
$cassa_prec = array();
$costi_non_importati = esegui_query("select * from $tablecosti_prec where tipo_costo is not NULL order by idcosti");
$num_costi_non_importati = numlin_query($costi_non_importati);
for ($num1 = 0 ; $num1 < $num_costi_non_importati ; $num1++) {
$tipo_costo = risul_query($costi_non_importati,$num1,'tipo_costo');
$val_costo = risul_query($costi_non_importati,$num1,'val_costo');
$utente_costo = risul_query($costi_non_importati,$num1,'utente_inserimento');
$nome_cassa = risul_query($costi_non_importati,$num1,'nome_cassa');
if ($tipo_costo == "e") $cassa_prec[$utente_costo][$nome_cassa] = $cassa_prec[$utente_costo][$nome_cassa] + (double) $val_costo;
else $cassa_prec[$utente_costo][$nome_cassa] = $cassa_prec[$utente_costo][$nome_cassa] - (double) $val_costo;
} # fine for $num1
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
reset($cassa_prec);
foreach ($cassa_prec as $utente_costo => $casse) {
reset($casse);
foreach ($casse as $nome_cassa => $val_costo) {
$idcosti_corr++;
$nome_utente = esegui_query("select * from $tableutenti where idutenti = '$utente_costo' ");
if (numlin_query($nome_utente)) $nome_utente = risul_query($nome_utente,0,'nome_utente');
else $nome_utente = "";
$tipo_costo = "e";
if ($val_costo < 0) {
$tipo_costo = "s";
$val_costo = (double) $val_costo * -1;
} # fine if ($val_costo < 0)
esegui_query("insert into $tablecosti (idcosti,nome_costo,val_costo,persona_costo,tipo_costo,datainserimento,hostinserimento,utente_inserimento) values ('$idcosti_corr','$anno_prec','$val_costo','".aggslashdb($nome_utente)."','$tipo_costo','$datainserimento','$HOSTNAME','$utente_costo') ");
if ($nome_cassa != "@#principale#@") esegui_query("update $tablecosti set nome_cassa = '".aggslashdb($nome_cassa)."' where idcosti = '$idcosti_corr' ");
} # fine foreach ($casse as $nome_cassa => $val_costo)
} # fine foreach ($cassa_prec as $utente_costo => $casse)

$colonne = "idntariffe,nomecostoagg,tipo_ca,valore_ca,valore_perc_ca,arrotonda_ca,tasseperc_ca,associasett_ca,numsett_ca,moltiplica_ca,periodipermessi_ca,beniinv_ca,appincompatibili_ca,variazione_ca,mostra_ca,categoria_ca,letto_ca,numlimite_ca,regoleassegna_ca,utente_inserimento";
for ($num1 = 1 ; $num1 <= $num_tariffe_tab ; $num1++) {
$nome_nuova_tariffa = "tariffa" . $num1;
$colonne .= ",$nome_nuova_tariffa";
} # fine for $num1
esegui_query("insert into $tablenometariffe ($colonne) select $colonne from $tablenometariffe_prec");
$costi_agg_importati = esegui_query("select * from $tablenometariffe where idntariffe > 10 ");
$num_costi_agg_importati = numlin_query($costi_agg_importati);
for ($num1 = 0 ; $num1 < $num_costi_agg_importati ; $num1++) {
$idntariffe = risul_query($costi_agg_importati,$num1,'idntariffe');
$periodipermessi = risul_query($costi_agg_importati,$num1,'periodipermessi_ca');
if ($periodipermessi) {
$pp_nuovi = substr($periodipermessi,0,1);
$periodipermessi = explode(",",substr($periodipermessi,1));
for ($num2 = 0 ; $num2 < count($periodipermessi) ; $num2++) {
$id_data_fine_pp = explode("-",$periodipermessi[$num2]);
$id_data_inizio_pp = $id_data_fine_pp[0];
$id_data_fine_pp = $id_data_fine_pp[1];
if ($id_data_fine_pp >= $id_data_ini_periodi_prec and $id_data_inizio_pp <= $id_data_fine_periodi_prec) {
if ($id_data_inizio_pp < $id_data_ini_periodi_prec) $id_data_inizio_pp = $id_data_ini_periodi_prec;
if ($id_data_fine_pp > $id_data_fine_periodi_prec) $id_data_fine_pp = $id_data_fine_periodi_prec;
$id_data_inizio_pp = $id_data_inizio_pp - $id_data_ini_periodi_prec + 1;
$id_data_fine_pp = $id_data_fine_pp - $id_data_ini_periodi_prec + 1;
$pp_nuovi .= $id_data_inizio_pp."-".$id_data_fine_pp.",";
} # fine if ($id_data_fine_pp >= $id_data_ini_periodi_prec and...
} # fine for $num2
if (strlen($pp_nuovi) > 1) $pp_nuovi = substr($pp_nuovi,0,-1);
esegui_query("update $tablenometariffe set periodipermessi_ca = '$pp_nuovi' where idntariffe = '$idntariffe'");
} # fine if ($periodipermessi)
} # fine for $num1
esegui_query("insert into $tableregole select $col_tableregole from $tableregole_prec where tariffa_per_app != ''");
esegui_query("insert into $tableregole select $col_tableregole from $tableregole_prec where tariffa_per_utente != ''");
esegui_query("insert into $tableregole select $col_tableregole from $tableregole_prec where tariffa_per_persone != ''");
esegui_query("insert into $tableregole select $col_tableregole from $tableregole_prec where tariffa_commissioni is not NULL and iddatainizio is NULL ");
$privilegi_prec = esegui_query("select * from $tableprivilegi where anno = '$anno_prec'");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_prec) ; $num1++) {
$idutente_p = risul_query($privilegi_prec,$num1,'idutente');
$regole1_consentite_p = risul_query($privilegi_prec,$num1,'regole1_consentite');
$tariffe_consentite_p = risul_query($privilegi_prec,$num1,'tariffe_consentite');
$costi_agg_consentiti_p = risul_query($privilegi_prec,$num1,'costi_agg_consentiti');
$contratti_consentiti_p = risul_query($privilegi_prec,$num1,'contratti_consentiti');
$cassa_pagamenti_p = aggslashdb(risul_query($privilegi_prec,$num1,'cassa_pagamenti'));
$priv_ins_prenota_p = risul_query($privilegi_prec,$num1,'priv_ins_prenota');
$priv_mod_prenota_p = risul_query($privilegi_prec,$num1,'priv_mod_prenota');
$priv_mod_pers_p = risul_query($privilegi_prec,$num1,'priv_mod_pers');
$priv_ins_clienti_p = risul_query($privilegi_prec,$num1,'priv_ins_clienti');
$prefisso_clienti_p = risul_query($privilegi_prec,$num1,'prefisso_clienti');
$priv_ins_costi_p = risul_query($privilegi_prec,$num1,'priv_ins_costi');
$priv_vedi_tab_p = risul_query($privilegi_prec,$num1,'priv_vedi_tab');
$priv_ins_tariffe_p = risul_query($privilegi_prec,$num1,'priv_ins_tariffe');
$priv_ins_regole_p = risul_query($privilegi_prec,$num1,'priv_ins_regole');
esegui_query("insert into $tableprivilegi (idutente,anno,regole1_consentite,tariffe_consentite,costi_agg_consentiti,contratti_consentiti,cassa_pagamenti,priv_ins_prenota,priv_mod_prenota,priv_mod_pers,priv_ins_clienti,prefisso_clienti,priv_ins_costi,priv_vedi_tab,priv_ins_tariffe,priv_ins_regole) values ('$idutente_p','$anno','$regole1_consentite_p','$tariffe_consentite_p','$costi_agg_consentiti_p','$contratti_consentiti_p','$cassa_pagamenti_p','$priv_ins_prenota_p','$priv_mod_prenota_p','$priv_mod_pers_p','$priv_ins_clienti_p','$prefisso_clienti_p','$priv_ins_costi_p','$priv_vedi_tab_p','$priv_ins_tariffe_p','$priv_ins_regole_p')");
$lingua_mex = $lingua[$idutente_p];
crea_menu_date(C_DATI_PATH."/selectperiodi$anno.1.php",C_DATI_PATH."/selectperiodi$anno.$idutente_p.php",$tipo_periodi);
if (@is_file(C_DATI_PATH."/selperiodimenu".($anno - 1).".$idutente_p.php")) estendi_menu_date(C_DATI_PATH."/selperiodimenu".($anno - 1).".$idutente_p.php",C_DATI_PATH."/selperiodimenu$anno.$idutente_p.php",$tipo_periodi,date("Y-m-d",mktime(0,0,0,$mese_ini,1,$anno)),$data_ini_agg,date("Y-m-d",mktime(0,0,0,($mese_fine + 1),1,$anno)),$anno,$pag);
else copy(C_DATI_PATH."/selectperiodi$anno.$idutente_p.php",C_DATI_PATH."/selperiodimenu$anno.$idutente_p.php");
$lingua_mex = $lingua[1];
$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$anno_prec' and idutente = '$idutente_p'");
if (numlin_query($giorno_vedi_ini_sett) == 1 and $tipo_periodi == "g") esegui_query("insert into $tablepersonalizza (idpersonalizza,valpersonalizza_num,idutente) values ('giorno_vedi_ini_sett$anno','".risul_query($giorno_vedi_ini_sett,0,"valpersonalizza_num")."','$idutente_p')");
} # fine for $num1
$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$anno_prec' and idutente = '1'");
if (numlin_query($giorno_vedi_ini_sett) == 1 and $tipo_periodi == "g") esegui_query("insert into $tablepersonalizza (idpersonalizza,valpersonalizza_num,idutente) values ('giorno_vedi_ini_sett$anno','".risul_query($giorno_vedi_ini_sett,0,"valpersonalizza_num")."','1')");
$inserisci_in_ntariffe = "NO";
} # fine if ($importa_anno_prec == "SI")

if ($inserisci_in_ntariffe != "NO") {
# rigo 1: nomi tariffe; 2-3: caparra (% e arrotondo).
for ($numtariffe = 1 ; $numtariffe <= 6 ; $numtariffe = $numtariffe + 1) {
esegui_query("insert into $tablenometariffe (idntariffe) values ('$numtariffe')");
} # fine for $numtariffe
esegui_query("update $tablenometariffe set nomecostoagg = '8' where idntariffe = '1'");
esegui_query("update $tablenometariffe set numlimite_ca = '11' where idntariffe = '1'");
} # fine if ($inserisci_in_ntariffe != "NO")

if ((!defined('C_URL_MOD_EXT_CARTE_CREDITO') or C_URL_MOD_EXT_CARTE_CREDITO == "") and $importa_anno_prec == "SI") {
$lista_clienti_conserva_cc = $lista_clienti_importati;
$ini_ultimo_quad =  esegui_query("select * from $tableperiodi_prec where datainizio = '$anno_prec-09-01'");
if (numlin_query($ini_ultimo_quad)) {
$ini_ultimo_quad = risul_query($ini_ultimo_quad,0,'idperiodi');
$prenota_ultimo_quad = esegui_query("select * from $tableprenota_prec where iddatafine > $ini_ultimo_quad ");
$num_prenota_ultimo_quad = numlin_query($prenota_ultimo_quad);
for ($num1 = 0 ; $num1 < $num_prenota_ultimo_quad ; $num1++) {
$idclienti = risul_query($prenota_ultimo_quad,$num1,'idclienti');
$lista_clienti_conserva_cc .= "and idclienti != '$idclienti' ";
} # fine for $num1
} # fine if (numlin_query($ini_ultimo_quad))
esegui_query("delete from $tablerelclienti where tipo = 'cc' $lista_clienti_conserva_cc");
} # fine if ((!defined('C_URL_MOD_EXT_CARTE_CREDITO') or C_URL_MOD_EXT_CARTE_CREDITO == "") and $importa_anno_prec == "SI")

unlock_tabelle($tabelle_lock);


if ($silenzio != "SI") echo "<br> ".mex("Anno ",$pag).$anno.mex(" creato",$pag)."! <br><br>";


if ($importa_anno_prec == "SI" and C_RESTRIZIONI_DEMO_ADMIN != "SI") {
if ($silenzio != "SI") $silenzio_mod = "SI";
else $silenzio_mod = "totale";
global $anno_modello_presente,$num_periodi_date,$modello_esistente,$cambia_frasi,$lingua_modello,$percorso_cartella_modello,$nome_file;
$pag_orig = $pag;
$pag = "crea_modelli.php";
include("./includes/templates/funzioni_modelli.php");
$modello_esistente = "SI";
$cambia_frasi = "NO";
$anno_modello = $anno;
include("./includes/templates/frasi_mod_disp.php");
include("./includes/templates/funzioni_mod_disp.php");
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/mdl_disponibilita.php")) {
$lingua_modello = "ita";
$nome_file = mex2("mdl_disponibilita",$pag,$lingua_modello).".php";
$num_periodi_date = "";
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno_prec) {
aggiorna_var_anno_modello_disponibilita($id_data_ini_periodi_prec,$tableperiodi_prec,$tableperiodi,$tabletransazioniweb,$tablemessaggi,$tipo_periodi);
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno_prec)
} # fine if (@is_file("$percorso_cartella_modello/modello_disponibilita.php"))
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = $ini_lingua;
$num_periodi_date = "";
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno_prec) {
aggiorna_var_anno_modello_disponibilita($id_data_ini_periodi_prec,$tableperiodi_prec,$tableperiodi,$tabletransazioniweb,$tablemessaggi,$tipo_periodi);
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno_prec)
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
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
$funz_aggiorna_var_anno_modello = "aggiorna_var_anno_modello_".$modello_ext;
if ($template_file_name["ita"]) $nome_file = $template_file_name["ita"];
else $nome_file = "ita_".$template_file_name["en"];
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = "ita";
$num_periodi_date = "";
$funz_recupera_var_modello($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno_prec) {
$funz_aggiorna_var_anno_modello($id_data_ini_periodi_prec,$tableperiodi_prec,$tableperiodi,$tabletransazioniweb,$tablemessaggi,$tipo_periodi);
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno_prec)
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name["en"];
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = $ini_lingua;
$num_periodi_date = "";
$funz_recupera_var_modello($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno_prec) {
$funz_aggiorna_var_anno_modello($id_data_ini_periodi_prec,$tableperiodi_prec,$tableperiodi,$tabletransazioniweb,$tablemessaggi,$tipo_periodi);
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio_mod,$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno_prec)
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
$pag = $pag_orig;
if ($silenzio != "SI") echo "<br>";
} # fine if ($importa_anno_prec == "SI" and C_RESTRIZIONI_DEMO_ADMIN != "SI")


} # fine if ($crea_tab)
else if ($silenzio != "SI") echo mex("Non ho i permessi per creare nuove tabelle nel database",$pag).".<br>";


} # fine if (numlin_query($anno_esistente) == 0)
else if ($silenzio != "SI") echo mex("Anno già creato",$pag).".<br>";


flock($filelock,3);
fclose($filelock);
unlink(C_DATI_PATH."/anni.lock");
} # fine if ($filelock)
else if ($silenzio != "SI") echo mex("Non ho i permessi di scrittura sulla cartella dati",$pag).".<br>";


$lingua_mex = $lingua_mex_orig;

} # fine function crea_nuovo_anno



?>