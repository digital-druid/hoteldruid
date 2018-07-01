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



function aggiorna_codice_sorgente_phpr ($url_subordinazione) {

$aggiorna_files = 0;
if (function_exists('ini_set')) @ini_set('opcache.enable',0);
if (!@is_file('NO_SOURCE_CODE_UPDATE')) {
$file_lock_update = fopen(C_DATI_PATH."/update.lock","w");
if ($file_lock_update) {
flock($file_lock_update,2);
include("./includes/files_sorgente.php");
$v_files_sorgente = $files_sorgente;
unset($files_sorgente);
$files_sorgente = implode("",file($url_subordinazione."mostra_sorgente.php?raw=SI"));
if ($files_sorgente) {
$files_sorgente = explode("#",$files_sorgente);
$num_files_sorgente = count($files_sorgente);
if (substr($files_sorgente[0],1) > C_PHPR_VERSIONE_NUM) {

allunga_tempo_limite();
# controllo che tutti i file e le directory siano scrivibili
$aggiorna_files = 1;
for ($num1 = 1 ; $num1 < $num_files_sorgente ; $num1++) {
$fsorg = str_replace("..","",$files_sorgente[$num1]);
if (substr($fsorg,-1) != "/") {
if (substr($fsorg,-4) != ".png" and substr($fsorg,-4) != ".gif" and substr($fsorg,-4) != ".jpg" and substr($fsorg,-5) != ".jpeg" and substr($fsorg,-4) != ".ico") {
$file_esist = 0;
if (@is_file($fsorg) or @is_link($fsorg) or @is_dir($fsorg)) $file_esist = 1;
$punt_file = @fopen($fsorg,"a");
if (!$punt_file) $aggiorna_files = 0;
else {
fclose($punt_file);
if (!$file_esist) unlink($fsorg);
} # fine else if (!$punt_file)
} # fine if (substr($fsorg,-4) != ".png" and...
} # fine if (substr($fsorg,-1) != "/")
elseif (!@is_dir($fsorg)) {
if (!@mkdir($fsorg,0755)) $aggiorna_files = 0;
#else @rmdir($fsorg);
} # fine elseif (!is_dir($fsorg))
if (!$aggiorna_files) break;
} # fine for $num1

if ($aggiorna_files) {
unset($file_esist);
for ($num1 = 1 ; $num1 < $num_files_sorgente ; $num1++) {
$fsorg = str_replace("..","",$files_sorgente[$num1]);
$file_esist[$fsorg] = 1;
if (substr($fsorg,-1) != "/") {
if (substr($fsorg,-4) == ".png" or substr($fsorg,-4) == ".gif" or substr($fsorg,-4) == ".jpg" or substr($fsorg,-5) == ".jpeg" or substr($fsorg,-4) == ".ico") {
copy($url_subordinazione.$fsorg,$fsorg);
} # fine if (substr($fsorg,-4) == ".png" or...
else {
$file_sorgente = implode("",file($url_subordinazione."mostra_sorgente.php?file_sorgente=".$fsorg."&raw=SI"));
$punt_file = fopen($fsorg,"w");
fwrite($punt_file,$file_sorgente);
fclose($punt_file);
# keep alive
echo " ";
} # fine else if (substr($fsorg,-4) == ".png" or...
} # fine if (substr($fsorg,-1) != "/")
elseif (!@is_dir($fsorg)) mkdir($fsorg,0755);
} # fine for $num1
for ($num1 = (count($v_files_sorgente) - 1) ; $num1 >= 0 ; $num1--) {
if (!$file_esist[$v_files_sorgente[$num1]]) {
if (substr($v_files_sorgente[$num1],-1) != "/") unlink($v_files_sorgente[$num1]);
else @rmdir($v_files_sorgente[$num1]);
} # fine if (!$file_esist[$v_files_sorgente[$num1]])
} # fine for $num1
} # fine if ($aggiorna_files)

} # fine if (substr($files_sorgente[0],1) > C_PHPR_VERSIONE_NUM)
} # fine if ($files_sorgente)
flock($file_lock_update,3);
fclose($file_lock_update);
} # fine if ($file_lock_update)
@unlink(C_DATI_PATH."/update.lock");
} # fine if (!@is_file('NO_SOURCE_CODE_UPDATE'))

return $aggiorna_files;

} # fine function aggiorna_codice_sorgente_phpr






function aggiorna_versione_phpr ($numconnessione,$id_utente_orig,$id_sessione_orig,$nome_utente_phpr_orig,$password_phpr_orig,$anno_orig) {
global $PHPR_TAB_PRE,$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT;
global $HOSTNAME,$ILIKE,$LIKE,$DATETIME,$MEDIUMTEXT,$pag,$lingua_mex;

if (function_exists('ini_set')) @ini_set('opcache.enable',0);
$file_lock_update = fopen(C_DATI_PATH."/update.lock","w");
if ($file_lock_update) {
flock($file_lock_update,2);

allunga_tempo_limite();
include_once("./includes/funzioni_costi_agg.php");
include(C_DATI_PATH."/lingua.php");
if (@is_array($lingua)) $lingua_mex = $lingua[1];
else $lingua_mex = $lingua;
$aggiornato = "";


if ($PHPR_DB_TYPE == "mysql" or $PHPR_DB_TYPE == "mysqli") @esegui_query("SET default_storage_engine=MYISAM",1);

$anni = esegui_query("select * from ".$PHPR_TAB_PRE."anni order by idanni");
$num_anni = numlin_query($anni);

$versione_corrente = @esegui_query("select * from ".$PHPR_TAB_PRE."versioni where idversioni = 1");
if (!$versione_corrente) {
pg_exec("create table versioni (idversioni integer primary key, num_versione float4)");
pg_exec("insert into versioni (idversioni, num_versione) values (1, '0.1')");
$versione_corrente = "0.1";
} # fine if (!$versione_corrente)
else $versione_corrente = risul_query($versione_corrente,0,'num_versione');


if ($versione_corrente < "0.12") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.12")

# c'è scitto 0.19 ma in realtà è la 0.2
if ($versione_corrente < "0.19") {
$aggiornato = "SI";
#pg_exec("lock table appartamenti");
pg_exec("alter table appartamenti add column commento text");
rename("datipermanenti/connessione_db.inc","datipermanenti/connessione_db.php");
rename("datipermanenti/selectappartamenti.inc","datipermanenti/selectappartamenti.php");
$anni = pg_exec("select * from anni order by idanni");
$num_anni = pg_numrows($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_cambia = pg_result($anni,$num1,'idanni');
rename("$anno_cambia/selectperiodi.inc","$anno_cambia/selectperiodi.php");
} # fine for $num1
pg_exec("insert into versioni (idversioni, num_versione) values (2, '100')");
$fileaperto = fopen("datipermanenti/lingua.php","a+");
fwrite($fileaperto,"<?php
\$lingua = \"ita\";
?>");
fclose($fileaperto);
pg_exec("alter table anni add column tipo_periodi text");
$anni = pg_exec("select * from anni order by idanni");
pg_exec("update anni set tipo_periodi = 's'");
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_mostra = pg_result($anni,$num1,'idanni');
$tableprenota = prenota. $anno_mostra;
pg_exec("alter table $tableprenota add column conferma varchar(4)");
$tableregole = regole. $anno_mostra;
#pg_exec("lock table $tableregole");
pg_exec("alter table $tableregole add column tariffa_per_app text");
pg_exec("alter table $tableregole add column motivazione text");
$tablesoldi = soldi. $anno_mostra;
pg_exec("create table $tablesoldi (idsoldi serial, motivazione text, saldo_prenota float8, saldo_cassa float8, soldi_prima float8, data_inserimento datetime)");
#pg_exec("lock table $tablesoldi");
pg_exec("insert into $tablesoldi (motivazione,soldi_prima) values ('soldi_prenotazioni_cancellate','0')");
} # fine for $num1
pg_exec("create table personalizza (idpersonalizza text primary key, valpersonalizza text, valpersonalizza_num integer)");
#pg_exec("lock table personalizza");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza) values ('col_tab_tutte_prenota','ca#@&pa#@&ap#@&pe#@&co')");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza) values ('valuta','Euro')");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza) values ('stile_soldi','europa')");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza) values ('costi_agg_in_tab_prenota','')");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza_num) values ('num_linee_tab2_prenota','30')");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza) values ('nomi_contratti','')");
# cambio idappartamenti da integer a text in appartamenti, $tableprenota e $tableregole
pg_exec("alter table appartamenti rename to appart");
pg_exec("drop index appartamenti_pkey");
pg_exec("alter table appart rename column idappartamenti to idappart");
pg_exec("alter table appart add column idappartamenti text");
$appartamenti = pg_exec("select * from appart");
$num_appartamenti = pg_numrows($appartamenti);
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1 = $num1 + 1) {
$idapp = pg_result($appartamenti,$num1,'idappart');
pg_exec("update appart set idappartamenti = '$idapp' where idappart = '$idapp'");
} # fine for $num1
pg_exec("create table appartamenti ( idappartamenti text primary key, numpiano text, maxoccupanti integer, numcasa text, app_vicini text, priorita integer, commento text )");
pg_exec("insert into appartamenti select idappartamenti,numpiano,maxoccupanti,numcasa,app_vicini,priorita,commento from appart");
pg_exec("drop table appart");
$anni = pg_exec("select * from anni order by idanni");
$num_anni = pg_numrows($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_cambia = pg_result($anni,$num1,'idanni');
$tableprenota = 'prenota'.$anno_cambia;
$tableprenota_temp = 'pren'.$anno_cambia;
pg_exec("alter table $tableprenota rename to $tableprenota_temp");
pg_exec("drop index $tableprenota"."_pkey");
pg_exec("alter table $tableprenota_temp rename column idappartamenti to idappart");
pg_exec("alter table $tableprenota_temp add column idappartamenti text");
pg_exec("alter table $tableprenota_temp rename column sconto to scon");
pg_exec("alter table $tableprenota_temp add column sconto float8");
pg_exec("alter table $tableprenota_temp rename column tariffa_tot to tariffa_t");
pg_exec("alter table $tableprenota_temp add column tariffa_tot float8");
pg_exec("alter table $tableprenota_temp rename column caparra to capar");
pg_exec("alter table $tableprenota_temp add column caparra float8");
pg_exec("alter table $tableprenota_temp rename column pagato to paga");
pg_exec("alter table $tableprenota_temp add column pagato float8");
$prenotazioni = pg_exec("select * from $tableprenota_temp");
$num_prenotazioni = pg_numrows($prenotazioni);
for ($num2 = 0 ; $num2 < $num_prenotazioni ; $num2 = $num2 + 1) {
$idprenota = pg_result($prenotazioni,$num2,'idprenota');
$idapp = pg_result($prenotazioni,$num2,'idappart');
$scon = pg_result($prenotazioni,$num2,'scon');
$tariffa_t = pg_result($prenotazioni,$num2,'tariffa_t');
$capar = pg_result($prenotazioni,$num2,'capar');
$paga = pg_result($prenotazioni,$num2,'paga');
pg_exec("update $tableprenota_temp set idappartamenti = '$idapp' where idprenota = '$idprenota'");
pg_exec("update $tableprenota_temp set sconto = '$scon' where idprenota = '$idprenota'");
pg_exec("update $tableprenota_temp set tariffa_tot = '$tariffa_t' where idprenota = '$idprenota'");
pg_exec("update $tableprenota_temp set caparra = '$capar' where idprenota = '$idprenota'");
pg_exec("update $tableprenota_temp set pagato = '$paga' where idprenota = '$idprenota'");
} # fine for $num2
pg_exec("create table $tableprenota (idprenota integer primary key, idclienti integer, idappartamenti text, iddatainizio integer, iddatafine integer, assegnazioneapp varchar(4), app_assegnabili text, num_persone integer, idprenota_compagna text, tariffa text, costiaggiuntivi text, sconto float8, tariffa_tot float8, caparra float8, pagato float8, commento text, conferma varchar(4), datainserimento datetime, hostinserimento varchar(50), data_modifica datetime )");
pg_exec("insert into $tableprenota select idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,app_assegnabili,num_persone,idprenota_compagna,tariffa,costiaggiuntivi,sconto,tariffa_tot,caparra,pagato,commento,conferma,datainserimento,hostinserimento,data_modifica from $tableprenota_temp");
pg_exec("drop table $tableprenota_temp");
$tableregole = 'regole'.$anno_cambia;
$tableregole_temp = 'reg'.$anno_cambia;
pg_exec("alter table $tableregole rename to $tableregole_temp");
pg_exec("alter table $tableregole_temp rename column app_agenzia to app_ag");
pg_exec("alter table $tableregole_temp add column app_agenzia text");
$regole = pg_exec("select * from $tableregole_temp");
$num_regole = pg_numrows($regole);
for ($num2 = 0 ; $num2 < $num_regole ; $num2 = $num2 + 1) {
$idregole = pg_result($regole,$num2,'idregole');
$app_ag = pg_result($regole,$num2,'app_ag');
pg_exec("update $tableregole_temp set app_agenzia = '$app_ag' where idregole = '$idregole'");
} # fine for $num2
pg_exec("create table $tableregole (idregole integer, app_agenzia text, tariffa_per_app text, iddatainizio integer, iddatafine integer, motivazione text)");
pg_exec("insert into $tableregole select idregole,app_agenzia,tariffa_per_app,iddatainizio,iddatafine,motivazione from $tableregole_temp");
pg_exec("drop table $tableregole_temp");
$tablenometariffe = 'ntariffe'.$anno_cambia;
$tablenometariffe_temp = 'ntarif'.$anno_cambia;
pg_exec("alter table $tablenometariffe rename to $tablenometariffe_temp ");
pg_exec("alter table $tablenometariffe_temp rename column costosettimanale to costosett");
pg_exec("alter table $tablenometariffe_temp add column costosettimanale float8");
pg_exec("alter table $tablenometariffe_temp rename column costofinale to costofin");
pg_exec("alter table $tablenometariffe_temp add column costofinale float8");
pg_exec("alter table $tablenometariffe_temp rename column costop_arrotond to costop_arr");
pg_exec("alter table $tablenometariffe_temp add column costop_arrotond float8");
pg_exec("alter table $tablenometariffe_temp rename column caparra_arrotond to caparra_arr");
pg_exec("alter table $tablenometariffe_temp add column caparra_arrotond float8");
$nomitariffe = pg_exec("select * from $tablenometariffe_temp");
$num_nomitariffe = pg_numrows($nomitariffe);
for ($num2 = 0 ; $num2 < $num_nomitariffe ; $num2 = $num2 + 1) {
$idntariffe = pg_result($nomitariffe,$num2,'idntariffe');
$costosett = pg_result($nomitariffe,$num2,'costosett');
$costofin = pg_result($nomitariffe,$num2,'costofin');
$costop_arr = pg_result($nomitariffe,$num2,'costop_arr');
$caparra_arr = pg_result($nomitariffe,$num2,'caparra_arr');
pg_exec("update $tablenometariffe_temp set costosettimanale = '$costosett' where idntariffe = '$idntariffe'");
pg_exec("update $tablenometariffe_temp set costofinale = '$costofin' where idntariffe = '$idntariffe'");
pg_exec("update $tablenometariffe_temp set costop_arrotond = '$costop_arr' where idntariffe = '$idntariffe'");
pg_exec("update $tablenometariffe_temp set caparra_arrotond = '$caparra_arr' where idntariffe = '$idntariffe'");
} # fine for $num2
pg_exec("create table $tablenometariffe (idntariffe integer, tariffa1 varchar(40), tariffa2 varchar(40), tariffa3 varchar(40), tariffa4 varchar(40), tariffa5 varchar(40), tariffa6 varchar(40), tariffa7 varchar(40), tariffa8 varchar(40), tariffa9 varchar(40), tariffa10 varchar(40), costosettimanale float8, costofinale float8, costopercentuale integer, costop_arrotond float8, regole varchar(40), caparra_percent integer, caparra_arrotond float8 )");
pg_exec("insert into $tablenometariffe select idntariffe,tariffa1,tariffa2,tariffa3,tariffa4,tariffa5,tariffa6,tariffa7,tariffa8,tariffa9,tariffa10,costosettimanale,costofinale,costopercentuale,costop_arrotond,regole,caparra_percent,caparra_arrotond from $tablenometariffe_temp");
pg_exec("drop table $tablenometariffe_temp");
$tableperiodi = periodi . $anno_cambia;
$tableperiodi_temp = perio . $anno_cambia;
pg_exec("drop index $tableperiodi"."_pkey");
pg_exec("alter table $tableperiodi rename to $tableperiodi_temp ");
pg_exec("alter table $tableperiodi_temp rename column tariffa1 to tari1");
pg_exec("alter table $tableperiodi_temp add column tariffa1 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa2 to tari2");
pg_exec("alter table $tableperiodi_temp add column tariffa2 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa3 to tari3");
pg_exec("alter table $tableperiodi_temp add column tariffa3 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa4 to tari4");
pg_exec("alter table $tableperiodi_temp add column tariffa4 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa5 to tari5");
pg_exec("alter table $tableperiodi_temp add column tariffa5 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa6 to tari6");
pg_exec("alter table $tableperiodi_temp add column tariffa6 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa7 to tari7");
pg_exec("alter table $tableperiodi_temp add column tariffa7 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa8 to tari8");
pg_exec("alter table $tableperiodi_temp add column tariffa8 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa9 to tari9");
pg_exec("alter table $tableperiodi_temp add column tariffa9 float8");
pg_exec("alter table $tableperiodi_temp rename column tariffa10 to tari10");
pg_exec("alter table $tableperiodi_temp add column tariffa10 float8");
$periodi = pg_exec("select * from $tableperiodi_temp");
$num_periodi = pg_numrows($periodi);
for ($num2 = 0 ; $num2 < $num_periodi ; $num2 = $num2 + 1) {
$idperiodi = pg_result($periodi,$num2,'idperiodi');
$tari1 = pg_result($periodi,$num2,'tari1');
$tari2 = pg_result($periodi,$num2,'tari2');
$tari3 = pg_result($periodi,$num2,'tari3');
$tari4 = pg_result($periodi,$num2,'tari4');
$tari5 = pg_result($periodi,$num2,'tari5');
$tari6 = pg_result($periodi,$num2,'tari6');
$tari7 = pg_result($periodi,$num2,'tari7');
$tari8 = pg_result($periodi,$num2,'tari8');
$tari9 = pg_result($periodi,$num2,'tari9');
$tari10 = pg_result($periodi,$num2,'tari10');
pg_exec("update $tableperiodi_temp set tariffa1 = '$tari1' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa2 = '$tari2' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa3 = '$tari3' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa4 = '$tari4' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa5 = '$tari5' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa6 = '$tari6' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa7 = '$tari7' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa8 = '$tari8' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa9 = '$tari9' where idperiodi = '$idperiodi'");
pg_exec("update $tableperiodi_temp set tariffa10 = '$tari10' where idperiodi = '$idperiodi'");
} # fine for $num2
pg_exec("create table $tableperiodi (idperiodi integer primary key, datainizio date not null, datafine date, tariffa1 float8, tariffa2 float8, tariffa3 float8, tariffa4 float8, tariffa5 float8, tariffa6 float8, tariffa7 float8, tariffa8 float8, tariffa9 float8, tariffa10 float8)");
pg_exec("insert into $tableperiodi select idperiodi,datainizio,datafine,tariffa1,tariffa2,tariffa3,tariffa4,tariffa5,tariffa6,tariffa7,tariffa8,tariffa9,tariffa10 from $tableperiodi_temp");
pg_exec("drop table $tableperiodi_temp");
$tablecosti = costi . $anno_cambia;
$tablecosti_temp = cos . $anno_cambia;
pg_exec("drop index $tablecosti"."_idcosti_key");
pg_exec("alter table $tablecosti rename to $tablecosti_temp ");
pg_exec("alter table $tablecosti_temp rename column val_costo to val_cos");
pg_exec("alter table $tablecosti_temp add column val_costo float8");
$costi = pg_exec("select * from $tablecosti_temp");
$num_costi = pg_numrows($costi);
for ($num2 = 0 ; $num2 < $num_costi ; $num2 = $num2 + 1) {
$idcosti = pg_result($costi,$num2,'idcosti');
$val_cos = pg_result($costi,$num2,'val_cos');
pg_exec("update $tablecosti_temp set val_costo = '$val_cos' where idcosti = '$idcosti'");
} # fine for $num2
pg_exec("create table $tablecosti (idcosti integer unique,nome_costo text, val_costo float8, persona_costo text, provenienza_costo text, tipo_costo text, datainserimento datetime, hostinserimento varchar(50))");
pg_exec("insert into $tablecosti select idcosti,nome_costo,val_costo,persona_costo,provenienza_costo,tipo_costo,datainserimento,hostinserimento from $tablecosti_temp");
pg_exec("drop table $tablecosti_temp");
} # fine for $num1
rename ("datipermanenti","dati");
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_mostra = pg_result($anni,$num1,'idanni');
copy("$anno_mostra/selectperiodi.php","dati/selectperiodi$anno_mostra.php");
unlink("$anno_mostra/selectperiodi.php");
rmdir("$anno_mostra");
} # fine for $num1
} # fine if ($versione_corrente < "0.19")
else {
#pg_exec("lock table appartamenti");
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableregole = 'regole'.$anno_mostra;
#pg_exec("lock table $tableregole");
$tablesoldi = 'soldi'.$anno_mostra;
#pg_exec("lock table $tablesoldi");
} # fine for $num1
#pg_exec("lock table personalizza");
} # fine else if ($versione_corrente < "0.19")

if ($versione_corrente < "0.21") {
$aggiornato = "SI";
$anni = pg_exec("select * from anni order by idanni");
$num_anni = pg_numrows($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_cambia = pg_result($anni,$num1,'idanni');
copy ("dati/selectperiodi$anno_cambia.php","dati/selperiodimenu$anno_cambia.php");
} # fine for $num1
} # fine if ($versione_corrente < "0.21")

if ($versione_corrente < "0.22") {
$aggiornato = "SI";
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza_num) values ('aggiunta_tronca_nomi_tab1','0')");
} # fine if ($versione_corrente < "0.22")

if ($versione_corrente < "0.23") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.23")

if ($versione_corrente < "0.24") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.24")

if ($versione_corrente < "0.30") {
$aggiornato = "SI";
$anni = pg_exec("select * from anni order by idanni");
$num_anni = pg_numrows($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_cambia = pg_result($anni,$num1,'idanni');
$tablenometariffe = ntariffe . $anno_cambia;
$tablenometariffe_temp = ntar . $anno_cambia;
pg_exec("alter table $tablenometariffe rename to $tablenometariffe_temp ");
$riga_ntariffe = pg_exec("select * from $tablenometariffe_temp where idntariffe = 1");
$caparra_percent = pg_result($riga_ntariffe,0,'caparra_percent');
$caparra_arrotond = pg_result($riga_ntariffe,0,'caparra_arrotond');
pg_exec("create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), costosettimanale float8, costofinale float8, costopercentuale integer, costop_arrotond float8, regole varchar(40), tariffa1 varchar(40), tariffa2 varchar(40), tariffa3 varchar(40), tariffa4 varchar(40), tariffa5 varchar(40), tariffa6 varchar(40), tariffa7 varchar(40), tariffa8 varchar(40), tariffa9 varchar(40), tariffa10 varchar(40) )");
pg_exec("insert into $tablenometariffe (idntariffe,costosettimanale,costofinale,costopercentuale,costop_arrotond,regole,tariffa1,tariffa2,tariffa3,tariffa4,tariffa5,tariffa6,tariffa7,tariffa8,tariffa9,tariffa10) select idntariffe,costosettimanale,costofinale,costopercentuale,costop_arrotond,regole,tariffa1,tariffa2,tariffa3,tariffa4,tariffa5,tariffa6,tariffa7,tariffa8,tariffa9,tariffa10 from $tablenometariffe_temp");
pg_exec("drop table $tablenometariffe_temp");
pg_exec("update $tablenometariffe set nomecostoagg = 10 where idntariffe = 1");
$idntariffe = pg_exec("select max(idntariffe) from $tablenometariffe");
$idntariffe = pg_result($idntariffe,0,max) + 1;
$num_spostamenti = 3;
if ($idntariffe < 4) $num_spostamenti = $idntariffe - 1;
for ($num2 = $idntariffe ; $num2 <= 3 ; $num2++) {
pg_exec("insert into $tablenometariffe (idntariffe) values ('$num2')");
$idntariffe++;
} # fine for $num2
for ($num2 = 2 ; $num2 <= $num_spostamenti ; $num2++) {
$riga_ntariffe = pg_exec("select * from $tablenometariffe where idntariffe = '$num2'");
if (pg_numrows($riga_ntariffe) != 0) {
$tariffa1 = pg_result($riga_ntariffe,0,'tariffa1');
$costosettimanale = pg_result($riga_ntariffe,0,'costosettimanale');
$costofinale = pg_result($riga_ntariffe,0,'costofinale');
$costopercentuale = pg_result($riga_ntariffe,0,'costopercentuale');
$costop_arrotond = pg_result($riga_ntariffe,0,'costop_arrotond');
$regole = pg_result($riga_ntariffe,0,'regole');
pg_exec("insert into $tablenometariffe (idntariffe) values ('$idntariffe') ");
if ($tariffa1) pg_exec("update $tablenometariffe set tariffa1 = '$tariffa1' where idntariffe = '$idntariffe'");
if ($costosettimanale) pg_exec("update $tablenometariffe set costosettimanale = '$costosettimanale' where idntariffe = '$idntariffe'");
if ($costofinale) pg_exec("update $tablenometariffe set costofinale = '$costofinale' where idntariffe = '$idntariffe'");
if ($costopercentuale) pg_exec("update $tablenometariffe set costopercentuale = '$costopercentuale' where idntariffe = '$idntariffe'");
if ($costop_arrotond) pg_exec("update $tablenometariffe set costop_arrotond = '$costop_arrotond' where idntariffe = '$idntariffe'");
if ($regole) pg_exec("update $tablenometariffe set regole = '$regole' where idntariffe = '$idntariffe'");
$idntariffe++;
pg_exec("delete from $tablenometariffe where idntariffe = '$num2'");
} # fine if (pg_numrows($riga_ntariffe) != 0)
pg_exec("insert into $tablenometariffe (idntariffe) values ('$num2')");
} # fine for $num2
for ($num2 = 1 ; $num2 <= 10 ; $num2++) {
$tariffa = "tariffa".$num2;
pg_exec("update $tablenometariffe set $tariffa = '$caparra_percent' where idntariffe = 2");
pg_exec("update $tablenometariffe set $tariffa = '$caparra_arrotond' where idntariffe = 3");
} # fine for $num2
for ($num2 = 4 ; $num2 < $idntariffe ; $num2++) {
$riga_ntariffe = pg_exec("select * from $tablenometariffe where idntariffe = '$num2'");
if (pg_numrows($riga_ntariffe) != 0) {
$tariffa1 = pg_result($riga_ntariffe,0,'tariffa1');
pg_exec("update $tablenometariffe set nomecostoagg = '$tariffa1' where idntariffe = '$num2'");
pg_exec("update $tablenometariffe set tariffa1 = '' where idntariffe = '$num2'");
$regole = pg_exec("select regole from $tablenometariffe where idntariffe = '$num2'");
$regole = pg_result($regole,0,'regole');
if (substr($regole,0,1) == "s") {
for ($num3 = 1 ; $num3 <= 10 ; $num3++) {
$tariffa = "tariffa".$num3;
pg_exec("update $tablenometariffe set $tariffa = '1' where idntariffe = '$num2'");
} # fine for $num3
} # fine if (substr($regole,0,1) == "s")
} # fine if (pg_numrows($riga_ntariffe) != 0)
} # fine for $num2
$righe_costi_agg = pg_exec("select * from $tablenometariffe where idntariffe > 3");
for ($num2 = 0 ; $num2 < pg_numrows($righe_costi_agg) ; $num2++) {
$nome_costo_agg = pg_result($righe_costi_agg,$num2,'nomecostoagg');
if (!$nome_costo_agg) {
$idntariffe = pg_result($righe_costi_agg,$num2,'idntariffe');
pg_exec("delete from $tablenometariffe where idntariffe = '$idntariffe'");
} # fine if (!$nome_costo_agg)
} # fine for $num2
pg_exec("update $tablenometariffe set costosettimanale = NULL,costofinale = NULL,costopercentuale = NULL where idntariffe < 4");
} # fine for $num1
$linee_conn = file("./dati/connessione_db.php");
$PHPR_LOAD_EXT = "NO";
for ($num1 = 0 ; $num1 < count($linee_conn) ; $num1++) {
$linea = $linee_conn[$num1];
if (substr($linea,0,15) == "\$numconnessione") {
$linea = explode("=",$linea);
for ($num2 = 0 ; $num2 < count($linea) ; $num2++) {
if (substr($linea[$num2],-6) ==  "dbname" or substr($linea[$num2],-7) ==  "dbname ") {
$parte = explode(" ",$linea[$num2+1]);
for ($num3 = 0 ; $num3 < count($parte) ; $num3++) {
if ($parte[$num3] != "") {
$PHPR_DB_NAME = str_replace("\"","",$parte[$num3]);
$PHPR_DB_NAME = str_replace("\'","",$PHPR_DB_NAME);
break;
} # fine if ($parte[$num3] != "")
} # fine for $num3
} # fine if (substr($linea[$num2],-6) ==  "dbname" or...)
if (substr($linea[$num2],-4) ==  "host" or substr($linea[$num2],-5) ==  "host ") {
$parte = explode(" ",$linea[$num2+1]);
for ($num3 = 0 ; $num3 < count($parte) ; $num3++) {
if ($parte[$num3] != "") {
$PHPR_DB_HOST = str_replace("\"","",$parte[$num3]);
$PHPR_DB_HOST = str_replace("\'","",$PHPR_DB_HOST);
break;
} # fine if ($parte[$num3] != "")
} # fine for $num3
} # fine if (substr($linea[$num2],-4) ==  "host" or...)
if (substr($linea[$num2],-4) ==  "port" or substr($linea[$num2],-5) ==  "port ") {
$parte = explode(" ",$linea[$num2+1]);
for ($num3 = 0 ; $num3 < count($parte) ; $num3++) {
if ($parte[$num3] != "") {
$PHPR_DB_PORT = str_replace("\"","",$parte[$num3]);
$PHPR_DB_PORT = str_replace("\'","",$PHPR_DB_PORT);
break;
} # fine if ($parte[$num3] != "")
} # fine for $num3
} # fine if (substr($linea[$num2],-4) ==  "port" or...)
if (substr($linea[$num2],-4) ==  "user" or substr($linea[$num2],-5) ==  "user ") {
$parte = explode(" ",$linea[$num2+1]);
for ($num3 = 0 ; $num3 < count($parte) ; $num3++) {
if ($parte[$num3] != "") {
$PHPR_DB_USER = str_replace("\"","",$parte[$num3]);
$PHPR_DB_USER = str_replace("\'","",$PHPR_DB_USER);
break;
} # fine if ($parte[$num3] != "")
} # fine for $num3
} # fine if (substr($linea[$num2],-4) ==  "user" or...)
if (substr($linea[$num2],-8) ==  "password" or substr($linea[$num2],-9) ==  "password ") {
$parte = explode(" ",$linea[$num2+1]);
for ($num3 = 0 ; $num3 < count($parte) ; $num3++) {
if ($parte[$num3] != "") {
$PHPR_DB_PASS = str_replace("\"","",$parte[$num3]);
$PHPR_DB_PASS = str_replace("\'","",$PHPR_DB_PASS);
break;
} # fine if ($parte[$num3] != "")
} # fine for $num3
} # fine if (substr($linea[$num2],-4) ==  "password" or...)
} # fine for $num2
} # fine if (substr($linea,0,15) == "\$numconnessione")
if (substr($linea,0,14) == "dl(\"pgsql.so\")") $PHPR_LOAD_EXT = "SI";
} # fine for $num1
unlink("./dati/connessione_db.php");
$file_conn = fopen("./dati/dati_connessione.php","w+");
fwrite($file_conn,"<?php
\$PHPR_DB_TYPE = \"postgresql\";
\$PHPR_DB_NAME = \"$PHPR_DB_NAME\";
\$PHPR_DB_HOST = \"$PHPR_DB_HOST\";
\$PHPR_DB_PORT = \"$PHPR_DB_PORT\";
\$PHPR_DB_USER = \"$PHPR_DB_USER\";
\$PHPR_DB_PASS = \"$PHPR_DB_PASS\";
\$PHPR_LOAD_EXT = \"$PHPR_LOAD_EXT\";
?>");
fclose($file_conn);
# cambio idappartamenti da text a varchar(100) in appartamenti, $tableprenota e $tableregole
pg_exec("alter table appartamenti rename to appart");
pg_exec("drop index appartamenti_pkey");
pg_exec("alter table appart rename column idappartamenti to idappart");
pg_exec("alter table appart add column idappartamenti varchar(100)");
$appartamenti = pg_exec("select * from appart");
$num_appartamenti = pg_numrows($appartamenti);
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1 = $num1 + 1) {
$idapp = pg_result($appartamenti,$num1,'idappart');
pg_exec("update appart set idappartamenti = '$idapp' where idappart = '$idapp'");
} # fine for $num1
pg_exec("create table appartamenti ( idappartamenti varchar(100) primary key, numpiano text, maxoccupanti integer, numcasa text, app_vicini text, priorita integer, commento text )");
pg_exec("insert into appartamenti select idappartamenti,numpiano,maxoccupanti,numcasa,app_vicini,priorita,commento from appart");
pg_exec("drop table appart");
$anni = pg_exec("select * from anni order by idanni");
$num_anni = pg_numrows($anni);
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_cambia = pg_result($anni,$num1,'idanni');
$tableprenota = prenota . $anno_cambia;
$tableprenota_temp = pren . $anno_cambia;
pg_exec("alter table $tableprenota rename to $tableprenota_temp");
pg_exec("drop index $tableprenota"."_pkey");
pg_exec("alter table $tableprenota_temp rename column idappartamenti to idappart");
pg_exec("alter table $tableprenota_temp add column idappartamenti varchar(100)");
$prenotazioni = pg_exec("select * from $tableprenota_temp");
$num_prenotazioni = pg_numrows($prenotazioni);
for ($num2 = 0 ; $num2 < $num_prenotazioni ; $num2 = $num2 + 1) {
$idprenota = pg_result($prenotazioni,$num2,'idprenota');
$idapp = pg_result($prenotazioni,$num2,'idappart');
pg_exec("update $tableprenota_temp set idappartamenti = '$idapp' where idprenota = '$idprenota'");
} # fine for $num2
pg_exec("create table $tableprenota (idprenota integer primary key, idclienti integer, idappartamenti varchar(100), iddatainizio integer, iddatafine integer, assegnazioneapp varchar(4), app_assegnabili text, num_persone integer, idprenota_compagna text, tariffa text, costiaggiuntivi text, sconto float8, tariffa_tot float8, caparra float8, pagato float8, commento text, conferma varchar(4), datainserimento datetime, hostinserimento varchar(50), data_modifica datetime )");
pg_exec("insert into $tableprenota select idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,app_assegnabili,num_persone,idprenota_compagna,tariffa,costiaggiuntivi,sconto,tariffa_tot,caparra,pagato,commento,conferma,datainserimento,hostinserimento,data_modifica from $tableprenota_temp");
pg_exec("drop table $tableprenota_temp");
$tableregole = regole . $anno_cambia;
$tableregole_temp = reg . $anno_cambia;
pg_exec("alter table $tableregole rename to $tableregole_temp");
pg_exec("alter table $tableregole_temp rename column app_agenzia to app_ag");
pg_exec("alter table $tableregole_temp add column app_agenzia varchar(100)");
$regole = pg_exec("select * from $tableregole_temp");
$num_regole = pg_numrows($regole);
for ($num2 = 0 ; $num2 < $num_regole ; $num2 = $num2 + 1) {
$idregole = pg_result($regole,$num2,'idregole');
$app_ag = pg_result($regole,$num2,'app_ag');
pg_exec("update $tableregole_temp set app_agenzia = '$app_ag' where idregole = '$idregole'");
} # fine for $num2
pg_exec("create table $tableregole (idregole integer, app_agenzia varchar(100), tariffa_per_app text, iddatainizio integer, iddatafine integer, motivazione text)");
pg_exec("insert into $tableregole select idregole,app_agenzia,tariffa_per_app,iddatainizio,iddatafine,motivazione from $tableregole_temp");
pg_exec("drop table $tableregole_temp");
} # fine for $num1
pg_exec("alter table personalizza rename to pers");
pg_exec("drop index personalizza_pkey");
pg_exec("alter table pers rename column idpersonalizza to idpers");
pg_exec("alter table pers add column idpersonalizza varchar(50)");
$personalizzazioni = pg_exec("select * from pers");
$num_personalizzazioni = pg_numrows($personalizzazioni);
for ($num1 = 0 ; $num1 < $num_personalizzazioni ; $num1 = $num1 + 1) {
$idpers = pg_result($personalizzazioni,$num1,'idpers');
pg_exec("update pers set idpersonalizza = '$idpers' where idpers = '$idpers'");
} # fine for $num1
pg_exec("create table personalizza (idpersonalizza varchar(50) primary key, valpersonalizza text, valpersonalizza_num integer)");
pg_exec("insert into personalizza select idpersonalizza,valpersonalizza,valpersonalizza_num from pers");
pg_exec("drop table pers");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza_num) values ('num_righe_tab_tutte_prenota',200)");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza_num) values ('num_righe_tab_storia_soldi',200)");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza) values ('stile_data','europa')");
pg_exec("insert into personalizza (idpersonalizza,valpersonalizza_num) values ('num_righe_tab_tutti_clienti','200')");
pg_exec("set datestyle to 'iso'");
for ($num_a = 0 ; $num_a < $num_anni ; $num_a++) {
$anno_cambia = pg_result($anni,$num_a,'idanni');
$tableperiodi = "periodi" . $anno_cambia;
$tipo_periodi = pg_exec("select * from anni where idanni = $anno_cambia");
$tipo_periodi = pg_result($tipo_periodi,0,'tipo_periodi');
if ($tipo_periodi == "g") $aggiungi_giorni = 1;
else $aggiungi_giorni = 7;
$periodi = pg_exec("select * from $tableperiodi order by idperiodi");
$num_periodi = pg_numrows($periodi);
$numgiorno = pg_result($periodi,0,'datainizio');
$numgiorno = explode("-",$numgiorno);
$mese_ini = $numgiorno[1];
$numgiorno = $numgiorno[2];
$fileaperto = fopen("dati/selectperiodi$anno_cambia.php","w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php 
echo \"
");
for ($num1 = 0 ; $num1 < $num_periodi ; $num1 = $num1 + 1) {
$datainizio = date("Y-m-d" , mktime(0,0,0,$mese_ini,$numgiorno,$anno_cambia));
$nome_giorno = date("D" , mktime(0,0,0,$mese_ini,$numgiorno,$anno_cambia));
$numgiorno = $numgiorno + $aggiungi_giorni;
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
$nome_mese = substr($datainizio,5,2);
if ($nome_mese == "01") $nome_mese = mex("Gen",$pag);
if ($nome_mese == "02") $nome_mese = mex("Feb",$pag);
if ($nome_mese == "03") $nome_mese = mex("Mar",$pag);
if ($nome_mese == "04") $nome_mese = mex("Apr",$pag);
if ($nome_mese == "05") $nome_mese = mex("Mag",$pag);
if ($nome_mese == "06") $nome_mese = mex("Giu",$pag);
if ($nome_mese == "07") $nome_mese = mex("Lug",$pag);
if ($nome_mese == "08") $nome_mese = mex("Ago",$pag);
if ($nome_mese == "09") $nome_mese = mex("Set",$pag);
if ($nome_mese == "10") $nome_mese = mex("Ott",$pag);
if ($nome_mese == "11") $nome_mese = mex("Nov",$pag);
if ($nome_mese == "12") $nome_mese = mex("Dic",$pag);
$numero_giorno = substr($datainizio,8,2);
$numero_anno = substr($datainizio,0,4);
fwrite($fileaperto,"<option value=\\\"$datainizio\\\">$nome_mese $numero_giorno$nome_giorno, $numero_anno</option>
");
} # fine for $num1
$datafine = date("Y-m-d" , mktime(0,0,0,$mese_ini,$numgiorno,$anno_cambia));
$nome_mese = substr($datafine,5,2);
if ($nome_mese == "01") $nome_mese = mex("Gen",$pag);
if ($nome_mese == "02") $nome_mese = mex("Feb",$pag);
if ($nome_mese == "03") $nome_mese = mex("Mar",$pag);
if ($nome_mese == "04") $nome_mese = mex("Apr",$pag);
if ($nome_mese == "05") $nome_mese = mex("Mag",$pag);
if ($nome_mese == "06") $nome_mese = mex("Giu",$pag);
if ($nome_mese == "07") $nome_mese = mex("Lug",$pag);
if ($nome_mese == "08") $nome_mese = mex("Ago",$pag);
if ($nome_mese == "09") $nome_mese = mex("Set",$pag);
if ($nome_mese == "10") $nome_mese = mex("Ott",$pag);
if ($nome_mese == "11") $nome_mese = mex("Nov",$pag);
if ($nome_mese == "12") $nome_mese = mex("Dic",$pag);
$numero_giorno = substr($datafine,8,2);
$numero_anno = substr($datafine,0,4);
fwrite($fileaperto,"<option value=\\\"$datafine\\\">$nome_mese $numero_giorno, $numero_anno</option>
");
fwrite($fileaperto,"\";
?>");
flock($fileaperto,3);
fclose($fileaperto);
copy ("dati/selectperiodi$anno_cambia.php","dati/selperiodimenu$anno_cambia.php");
} # fine for $num_a
} # fine if ($versione_corrente < "0.30")

if ($versione_corrente < "0.31") {
$aggiornato = "SI";
$anni = esegui_query("select * from anni order by idanni");
$num_anni = numlin_query($anni);
for ($num_a = 0 ; $num_a < $num_anni ; $num_a++) {
$anno_cambia = risul_query($anni,$num_a,'idanni');
$tablenometariffe = ntariffe . $anno_cambia;
$idntariffe = esegui_query("select max(idntariffe) from $tablenometariffe");
$idntariffe = risul_query($idntariffe,0,0) + 1;
esegui_query("update $tablenometariffe set regole = '$idntariffe' where idntariffe = 1");
} # fine for $num_a
} # fine if ($versione_corrente < "0.31")

if ($versione_corrente < "0.32") {
$aggiornato = "SI";
include("./dati/dati_connessione.php");
$file_conn = fopen("./dati/dati_connessione.php","w+");
fwrite($file_conn,"<?php
\$PHPR_DB_TYPE = \"$PHPR_DB_TYPE\";
\$PHPR_DB_NAME = \"$PHPR_DB_NAME\";
\$PHPR_DB_HOST = \"$PHPR_DB_HOST\";
\$PHPR_DB_PORT = \"$PHPR_DB_PORT\";
\$PHPR_DB_USER = \"$PHPR_DB_USER\";
\$PHPR_DB_PASS = \"$PHPR_DB_PASS\";
\$PHPR_LOAD_EXT = \"$PHPR_LOAD_EXT\";
\$PHPR_TAB_PRE = \"\";
?>");
fclose($file_conn);
$PHPR_TAB_PRE = "";
$col_tab_tutte_prenota = esegui_query("select * from personalizza where idpersonalizza = 'col_tab_tutte_prenota'");
$col_tab_tutte_prenota = risul_query($col_tab_tutte_prenota,0,'valpersonalizza');
if ($col_tab_tutte_prenota) $col_tab_tutte_prenota .= "#@&tc";
else $col_tab_tutte_prenota = "tc";
esegui_query("update personalizza set valpersonalizza = '$col_tab_tutte_prenota' where idpersonalizza = 'col_tab_tutte_prenota'");
} # fine if ($versione_corrente < "0.32")

if ($versione_corrente < "0.33") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.33")

if ($versione_corrente < "0.40") {
$aggiornato = "SI";
esegui_query("create table ".$PHPR_TAB_PRE."utenti (idutenti integer primary key, nome_utente text, password text, tipo_pass varchar(1) )");
esegui_query("insert into ".$PHPR_TAB_PRE."utenti (idutenti, nome_utente, tipo_pass) values (1,'admin','n')");
esegui_query("create table ".$PHPR_TAB_PRE."privilegi (idutente integer, anno integer, regole1_consentite text, tariffe_consentite text, costi_agg_consentiti text, contratti_consentiti text, priv_ins_prenota varchar(20), priv_mod_prenota varchar(35), priv_mod_pers varchar(15), priv_ins_clienti varchar(10), prefisso_clienti text, priv_ins_costi varchar(5), priv_vedi_tab varchar(30), priv_ins_tariffe varchar(10), priv_ins_regole varchar(10) )");
esegui_query("create table ".$PHPR_TAB_PRE."sessioni (idsessioni varchar(30) primary key, idutente integer, indirizzo_ip text, user_agent text, ultimo_accesso datetime)");
esegui_query("create table ".$PHPR_TAB_PRE."transazioni (idtransazioni varchar(30) primary key, idsessione varchar(30), tipo_transazione varchar(5), anno integer, spostamenti text, dati_transazione1 text, dati_transazione2 text, dati_transazione3 text, dati_transazione4 text, dati_transazione5 text, dati_transazione6 text, dati_transazione7 text, dati_transazione8 text, dati_transazione9 text, dati_transazione10 text, dati_transazione11 text, dati_transazione12 text, dati_transazione13 text, dati_transazione14 text, dati_transazione15 text, dati_transazione16 text, dati_transazione17 text, dati_transazione18 text, ultimo_accesso datetime)");
esegui_query("alter table ".$PHPR_TAB_PRE."personalizza rename to ".$PHPR_TAB_PRE."pers");
esegui_query("create table ".$PHPR_TAB_PRE."personalizza (idpersonalizza varchar(50) not null, idutente integer, valpersonalizza text, valpersonalizza_num integer)");
$pers = esegui_query("select * from ".$PHPR_TAB_PRE."pers");
for ($num1 = 0 ; $num1 < numlin_query($pers) ; $num1++) {
$idpersonalizza = risul_query($pers,$num1,'idpersonalizza');
$valpersonalizza = risul_query($pers,$num1,'valpersonalizza');
$valpersonalizza_num = risul_query($pers,$num1,'valpersonalizza_num');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,valpersonalizza,valpersonalizza_num) values ('$idpersonalizza','$valpersonalizza','$valpersonalizza_num')");
} # fine for $num1
esegui_query("drop table ".$PHPR_TAB_PRE."pers");
esegui_query("update ".$PHPR_TAB_PRE."personalizza set idutente = '1'");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('minuti_durata_sessione','1','90')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('ore_anticipa_periodo_corrente','1','0')");
esegui_query("alter table ".$PHPR_TAB_PRE."clienti add column utente_inserimento integer");
esegui_query("update ".$PHPR_TAB_PRE."clienti set utente_inserimento = '1'");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableprenota = $PHPR_TAB_PRE."prenota". $anno_mostra;
esegui_query("alter table $tableprenota add column utente_inserimento integer");
esegui_query("update $tableprenota set utente_inserimento = '1'");
$tablecosti = $PHPR_TAB_PRE."costi". $anno_mostra;
esegui_query("alter table $tablecosti add column utente_inserimento integer");
esegui_query("update $tablecosti set utente_inserimento = '1' where idcosti != '0'");
$tablesoldi = $PHPR_TAB_PRE."soldi". $anno_mostra;
esegui_query("alter table $tablesoldi add column utente_inserimento integer");
esegui_query("update $tablesoldi set utente_inserimento = '1'");
} # fine for $num1
include("./dati/lingua.php");
$fileaperto = fopen("dati/lingua.php","w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php
\$lingua[1] = \"$lingua\";
?>");
flock($fileaperto,3);
fclose($fileaperto);
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_cambia = risul_query($anni,$num1,'idanni');
rename("./dati/selectperiodi$anno_cambia.php","./dati/selectperiodi$anno_cambia.1.php");
rename("./dati/selperiodimenu$anno_cambia.php","./dati/selperiodimenu$anno_cambia.1.php");
} # fine for $num1
} # fine if ($versione_corrente < "0.40")

if ($versione_corrente < "0.41") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableregole = $PHPR_TAB_PRE."regole".$anno_mostra;
esegui_query("alter table $tableregole add column tariffa_per_utente text");
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno_mostra;
esegui_query("update $tablesoldi set utente_inserimento = '1' where utente_inserimento = '' or utente_inserimento is null");
} # fine for $num1
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('tutti_fissi','1','NO')");
} # fine if ($versione_corrente < "0.41")

if ($versione_corrente < "0.42") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.42")

if ($versione_corrente < "0.43") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.43")

if ($versione_corrente < "0.44") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.44")


$utenti = esegui_query("select * from ".$PHPR_TAB_PRE."utenti order by idutenti");
if ($id_utente_orig) $id_utente = $id_utente_orig;
else $id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione_orig,$nome_utente_phpr_orig,$password_phpr_orig,$anno_orig);
if ($id_utente) {

# metto l'utente come 1 per evitare rallentamenti per la scrittura dei log
$id_utente_vero = $id_utente;
global $id_utente;
$id_utente = 1;


if ($versione_corrente < "0.50") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe". $anno_mostra;
$tablenometariffetemp = $PHPR_TAB_PRE."ntariffetemp". $anno_mostra;
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1");
$num_tariffe_tab = risul_query($rigatariffe,0,'nomecostoagg');
esegui_query("alter table $tablenometariffe rename to $tablenometariffetemp");
$query = "create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, arrotonda_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca varchar(1), periodipermessi_ca text, variazione_ca varchar(10), mostra_ca varchar(1), letto_ca varchar(1), numlimite_ca integer";
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$nome_nuova_tariffa = "tariffa" . $num2;
$query .= ", $nome_nuova_tariffa varchar(40)";
} # fine for $num2
esegui_query($query.")");
$dati_ntariffe = esegui_query("select * from $tablenometariffetemp order by idntariffe");
for ($num2 = 0 ; $num2 < 3 ; $num2++) {
$idntariffe = risul_query($dati_ntariffe,$num2,'idntariffe');
$lista_colonne = "idntariffe";
$lista_valori = "'$idntariffe'";
if ($num2 == 0) {
$nomecostoagg = risul_query($dati_ntariffe,$num2,'nomecostoagg');
$numlimite_ca = 5;
$lista_colonne .= ",nomecostoagg,numlimite_ca";
$lista_valori .= ",'$nomecostoagg','$numlimite_ca'";
} # fine if ($num2 == 0)
for ($num3 = 1 ; $num3 <= $num_tariffe_tab ; $num3++) {
$nome_tariffa = "tariffa" . $num3;
$val_tariffa = risul_query($dati_ntariffe,$num2,$nome_tariffa);
$lista_colonne .= ",$nome_tariffa";
$lista_valori .= ",'$val_tariffa'";
} # fine for $num3
esegui_query("insert into $tablenometariffe ($lista_colonne) values ($lista_valori)");
} # fine for $num2
esegui_query("insert into $tablenometariffe (idntariffe) values ('4')");
$idntariffe = 5;
unset($id_costo_tiponome);
for ($num2 = 3 ; $num2 < numlin_query($dati_ntariffe) ; $num2++) {
$nomecostoagg = risul_query($dati_ntariffe,$num2,'nomecostoagg');
$lista_colonne = "idntariffe,nomecostoagg";
$lista_valori = "'$idntariffe','$nomecostoagg'";
$idntariffe++;
esegui_query("update $tablenometariffe set numlimite_ca = '$idntariffe' where idntariffe = '1'");
$regole = risul_query($dati_ntariffe,$num2,'regole');
if (substr($regole,0,1) == "r") $mostra_ca = "s";
else $mostra_ca = "n";
$costofinale = (string) risul_query($dati_ntariffe,$num2,'costofinale');
if ($costofinale != "") {
$tipo_ca = "uf";
$valore_ca = $costofinale;
} # fine if ($costofinale != "")
$costopercentuale = (string) risul_query($dati_ntariffe,$num2,'costopercentuale');
if ($costopercentuale != "") {
$tipo_ca = "up";
$valore_ca = $costopercentuale;
$arrotonda_ca = risul_query($dati_ntariffe,$num2,'costop_arrotond');
$lista_colonne .= ",arrotonda_ca";
$lista_valori .= ",'$arrotonda_ca'";
} # fine if ($costopercentuale != "")
$costosettimanale = (string) risul_query($dati_ntariffe,$num2,'costosettimanale');
if ($costosettimanale != "") {
$tipo_ca = "sf";
$valore_ca = $costosettimanale;
$associasett_ca = "n";
$numsett_ca = substr($regole,1,1);
if (substr($regole,2,1) == "s") $moltiplica_ca = "c";
else $moltiplica_ca = "1";
if (substr($regole,3,1) == "s") $letto_ca = "s";
else $letto_ca = "n";
} # fine if ($costosettimanale != "")
else {
$associasett_ca = "";
$numsett_ca = "";
if (substr($regole,1,1) == "s") $moltiplica_ca = "c";
else $moltiplica_ca = "1";
if (substr($regole,2,1) == "s") $letto_ca = "s";
else $letto_ca = "n";
} # fine else if ($costosettimanale)
$id_costo_tiponome[$tipo_ca][$nomecostoagg] = ($idntariffe - 1);
if (substr($tipo_ca,1,1) == "p") $variazione_ca = "snnnn";
else $variazione_ca = "nnnnn";
$lista_colonne .= ",tipo_ca,valore_ca,mostra_ca,variazione_ca,associasett_ca,numsett_ca,moltiplica_ca,letto_ca";
$lista_valori .= ",'$tipo_ca','$valore_ca','$mostra_ca','$variazione_ca','$associasett_ca','$numsett_ca','$moltiplica_ca','$letto_ca'";
for ($num3 = 1 ; $num3 <= $num_tariffe_tab ; $num3++) {
$nome_tariffa = "tariffa" . $num3;
$val_tariffa = risul_query($dati_ntariffe,$num2,$nome_tariffa);
if ($val_tariffa == 1) $val_tariffa = "ss";
$lista_colonne .= ",$nome_tariffa";
$lista_valori .= ",'$val_tariffa'";
} # fine for $num3
esegui_query("insert into $tablenometariffe ($lista_colonne) values ($lista_valori)");
} # fine for $num2
esegui_query("drop table $tablenometariffetemp");
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota". $anno_mostra;
esegui_query("create table $tablecostiprenota (idcostiprenota integer primary key, idprenota integer, tipo varchar(2), nome varchar(40), valore float8, arrotonda float4, associasett varchar(1), settimane text, moltiplica text, letto varchar(1), numlimite integer, idntariffe integer, variazione varchar(10), varmoltiplica varchar(1), varnumsett varchar(20), varperiodipermessi text, vartariffeassociate varchar(10), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer)");
crea_indice($tablecostiprenota,"idprenota",$PHPR_TAB_PRE."iidpcostiprenota".$anno_mostra);
$tableprenota = $PHPR_TAB_PRE."prenota". $anno_mostra;
$tableperiodi = $PHPR_TAB_PRE."periodi". $anno_mostra;
if ($PHPR_DB_TYPE == "postgresql") esegui_query("alter table $tableprenota rename column costiaggiuntivi to tariffesettimanali");
if ($PHPR_DB_TYPE == "mysql") esegui_query("alter table $tableprenota change costiaggiuntivi tariffesettimanali text");
esegui_query("alter table $tableprenota add column costiassociati varchar(1)");
$datainserimento = date("Y-m-d H:i:s");
$prenotazioni = esegui_query("select idprenota,iddatainizio,iddatafine,tariffa,tariffesettimanali,utente_inserimento from $tableprenota");
$idcostiprenota = 1;
for ($num2 = 0 ; $num2 < numlin_query($prenotazioni) ; $num2++) {
$idprenota = risul_query($prenotazioni,$num2,'idprenota');
$id_utente_ins = risul_query($prenotazioni,$num2,'utente_inserimento');
$d_costi_aggiuntivi_int = risul_query($prenotazioni,$num2,'tariffesettimanali');
if ($d_costi_aggiuntivi_int) {
$d_costi_aggiuntivi = explode("#@&",$d_costi_aggiuntivi_int);
$d_num_costi_aggiuntivi = count($d_costi_aggiuntivi);
} # fine if ($d_costi_aggiuntivi)
else $d_num_costi_aggiuntivi = 0;
if ($d_num_costi_aggiuntivi > 0) esegui_query("update $tableprenota set costiassociati = 's' where idprenota = '$idprenota'");
else esegui_query("update $tableprenota set costiassociati = 'n' where idprenota = '$idprenota'");
for ($numca = 0 ; $numca < $d_num_costi_aggiuntivi ; $numca++) {
$dd_costi_aggiuntivi = explode("#?&",$d_costi_aggiuntivi[$numca]);
$dd_num_costi_aggiuntivi = count($dd_costi_aggiuntivi);
$nome = $dd_costi_aggiuntivi[1];
if ($dd_costi_aggiuntivi[0] == "cf") {
$tipo = "uf";
$valore = $dd_costi_aggiuntivi[2];
$moltiplica = $dd_costi_aggiuntivi[3];
if (!$moltiplica) $moltiplica = 1;
if ($dd_costi_aggiuntivi[4] == "s") $letto = "s";
else $letto = "n";
} # fine if ($dd_costi_aggiuntivi[0] == "cf")
if ($dd_costi_aggiuntivi[0] == "cs") {
$tipo = "sf";
$settimane = $dd_costi_aggiuntivi[2];
$moltiplica = $dd_costi_aggiuntivi[3];
if (!$moltiplica) $moltiplica = 1;
$valore = $dd_costi_aggiuntivi[4];
if ($dd_costi_aggiuntivi[5] == "s") $letto = "s";
else $letto = "n";
} # fine if ($dd_costi_aggiuntivi[0] == "cs")
else $settimane = "";
if ($dd_costi_aggiuntivi[0] == "cp") {
$tipo = "up";
$moltiplica = $dd_costi_aggiuntivi[2];
$valore = $dd_costi_aggiuntivi[3];
$arrotonda = $dd_costi_aggiuntivi[4];
if ($dd_costi_aggiuntivi[5] == "s") $letto = "s";
else $letto = "n";
} # fine if ($dd_costi_aggiuntivi[0] == "cp")
else $arrotonda = "";
if ($id_costo_tiponome[$tipo][$nome]) $idntariffe_ins = $id_costo_tiponome[$tipo][$nome];
else $idntariffe_ins = -1;
esegui_query("insert into $tablecostiprenota (idcostiprenota,idprenota,tipo,nome,valore,arrotonda,associasett,settimane,moltiplica,letto,idntariffe,varmoltiplica,varnumsett,datainserimento,hostinserimento,utente_inserimento) values ('$idcostiprenota','$idprenota','$tipo','$nome','$valore','$arrotonda','n','$settimane','$moltiplica','$letto','$idntariffe_ins','c','c','$datainserimento','$HOSTNAME','$id_utente_ins')");
$idcostiprenota++;
} # fine for $numca
$tariffa = risul_query($prenotazioni,$num2,'tariffa');
$tariffa = explode("#@&",$tariffa);
$nome_tariffa = $tariffa[0];
$costo_tariffa = $tariffa[1];
$iddatainizio = risul_query($prenotazioni,$num2,'iddatainizio');
$iddatafine = risul_query($prenotazioni,$num2,'iddatafine');
if ($iddatainizio != 0) {
$tariffa_trovata = "NO";
unset($lista_tariffe);
for ($numtariffa = 1 ; $numtariffa <= $num_tariffe_tab ; $numtariffa++) {
$nometariffa = risul_query($rigatariffe,0,"tariffa".$numtariffa);
if ($tariffa == $nometariffa or $tariffa == "tariffa".$numtariffa) {
$tariffa_trovata = "SI";
$tariffa_num = "tariffa".$numtariffa;
} # fine if ($tariffa == $nometariffa or $tariffa == "tariffa".$numtariffa)
} # fine for $numtariffa
if ($tariffa_trovata != "NO") {
$somma_tariffe = 0;
for ($num3 = $iddatainizio ; $num3 <= $iddatafine ; $num3++) {
$tariffa_periodo = esegui_query("select $tariffa_num from $tableperiodi where idperiodi = '$num3'");
$tariffa_periodo = risul_query($tariffa_periodo,0,$tariffa_num);
if ($tariffa_periodo != "") {
$lista_tariffe .= ",".$tariffa_periodo;
$somma_tariffe = $somma_tariffe + $tariffa_periodo;
} # fine if ($tariffa_periodo != "")
else $tariffa_trovata = "NO";
} # fine for $num3
if ($somma_tariffe != $costo_tariffa) $tariffa_trovata = "NO";
} # fine if ($tariffa_trovata != "NO")
if ($tariffa_trovata == "NO") {
unset($lista_tariffe);
$numero_settimane = $iddatafine - $iddatainizio + 1;
$tariffa_periodo = $costo_tariffa / $numero_settimane;
$tariffa_periodo_ar = floor($tariffa_periodo * 100);
$tariffa_periodo_ar = $tariffa_periodo_ar / 100;
for ($num3 = $iddatainizio ; $num3 <= $iddatafine ; $num3++) {
if ($num3 == $iddatafine) $tariffa_periodo_ar = $tariffa_periodo_ar + ( $costo_tariffa - ( $tariffa_periodo_ar * $numero_settimane ) );
$lista_tariffe .= ",".$tariffa_periodo_ar;
} # fine for $num3
} # fine if ($tariffa_trovata == "NO")
$lista_tariffe = substr($lista_tariffe,1);
esegui_query("update $tableprenota set tariffesettimanali = '$lista_tariffe' where idprenota = '$idprenota'");
} # fine if ($iddatainizio != 0)
} # fine for $num2
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
for ($num2 = 0 ; $num2 < numlin_query($utenti) ; $num2++) {
$idutente_mostra = risul_query($utenti,$num2,'idutenti');
$file1 = "./dati/selectperiodi$anno_mostra.$idutente_mostra".".php";
$file2 = "./dati/selperiodimenu$anno_mostra.$idutente_mostra".".php";
for ($num3 = 1 ; $num3 <= 2 ; $num3++) {
$file = ${"file".$num3};
$file_intero = @file($file);
if ($file_intero) {
$fileaperto = fopen("$file","w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php
echo \"
");
for ($num4 = 0 ; $num4 < count($file_intero) ; $num4++) {
if (substr($file_intero[$num4],0,7) == "<option") {
$data_option = substr($file_intero[$num4],16,10);
fwrite($fileaperto,str_replace("$data_option\\\">","$data_option\\\"\$sel".str_replace("-","",$data_option).">",$file_intero[$num4]));
} # fine if (substr($file_intero[$num4],0,7) == "<option")
} # fine for $num1
fwrite($fileaperto,"\";
?>");
flock($fileaperto,3);
fclose($fileaperto);
} # fine if ($file_intero)
} # fine for $num3
} # fine for $num2
} # fine for $num1
$righe = esegui_query("select * from ".$PHPR_TAB_PRE."personalizza where idpersonalizza = 'col_tab_tutte_prenota'");
$num_righe = numlin_query($righe);
for ($num1 = 0 ; $num1 < $num_righe ; $num1++) {
$col_tab_tutte_prenota = risul_query($righe,$num1,'valpersonalizza');
$id_utente_mod = risul_query($righe,$num1,'idutente');
if (substr($col_tab_tutte_prenota,0,5) == "cf#?&" or substr($col_tab_tutte_prenota,0,5) == "cp#?&") $col_tab_tutte_prenota = "cu#?&".substr($col_tab_tutte_prenota,5);
$col_tab_tutte_prenota = str_replace("#@&cf#?&","#@&cu#?&",$col_tab_tutte_prenota);
$col_tab_tutte_prenota = str_replace("#@&cp#?&","#@&cu#?&",$col_tab_tutte_prenota);
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = '$col_tab_tutte_prenota' where idpersonalizza = 'col_tab_tutte_prenota' and idutente = '$id_utente_mod'");
} # fine for $num1
} # fine if ($versione_corrente < "0.50")

if ($versione_corrente < "0.51") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno_mostra;
esegui_query("alter table $tableprenota add column metodo_pagamento text");
esegui_query("alter table $tablesoldi add column metodo_pagamento text");
esegui_query("alter table $tableprenota add column checkin $DATETIME");
esegui_query("alter table $tableprenota add column checkout $DATETIME");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$dati_ntariffe = esegui_query("select * from $tablenometariffe where idntariffe > 4 order by idntariffe");
for ($num2 = 0 ; $num2 < numlin_query($dati_ntariffe) ; $num2++) {
$idntariffe = risul_query($dati_ntariffe,$num2,'idntariffe');
$variazione_ca = risul_query($dati_ntariffe,$num2,'variazione_ca');
esegui_query("update $tablenometariffe set variazione_ca = '$variazione_ca"."n' where idntariffe = '$idntariffe'");
} # fine for $num2
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
esegui_query("alter table $tablecostiprenota add column vartariffeincomp text");
} # fine for $num1
$fileaperto = fopen("dati/tema.php","w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php
");
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('metodi_pagamento','$idutente_mostra','')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('attiva_checkin','$idutente_mostra','NO')");
fwrite($fileaperto,"\$tema[$idutente_mostra] = \"sim\";
");
} # fine for $num1
fwrite($fileaperto,"?>");
flock($fileaperto,3);
fclose($fileaperto);
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_ins_prec = risul_query($privilegi_anni,$num1,'priv_ins_prenota');
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_ins_prenota = '".$priv_ins_prec."s', priv_mod_prenota = '".$priv_mod_prec."sn' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
$col_tab = esegui_query("select * from ".$PHPR_TAB_PRE."personalizza where idpersonalizza = 'col_tab_tutte_prenota' ");
for ($num1 = 0 ; $num1 < numlin_query($col_tab) ; $num1++) {
$idutente_pers =  risul_query($col_tab,$num1,'idutente');
$val_pers =  risul_query($col_tab,$num1,'valpersonalizza');
if ($val_pers) esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = 'nu#@&cg#@&in#@&fi#@&".$val_pers."' where idutente = '$idutente_pers' and idpersonalizza = 'col_tab_tutte_prenota' ");
else esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = 'nu#@&cg#@&in#@&fi' where idutente = '$idutente_pers' and idpersonalizza = 'col_tab_tutte_prenota' ");
} # fine for $num1
define(C_DIFF_ORE,0);
} # fine if ($versione_corrente < "0.51")

if ($versione_corrente < "0.52") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.52")

if ($versione_corrente < "0.53") {
$aggiornato = "SI";
esegui_query("create table ".$PHPR_TAB_PRE."transazioniweb (idtransazioni varchar(30) primary key, idsessione varchar(30), tipo_transazione varchar(5), anno integer, spostamenti text, dati_transazione1 text, dati_transazione2 text, dati_transazione3 text, dati_transazione4 text, dati_transazione5 text, dati_transazione6 text, dati_transazione7 text, dati_transazione8 text, dati_transazione9 text, dati_transazione10 text, dati_transazione11 text, dati_transazione12 text, dati_transazione13 text, dati_transazione14 text, dati_transazione15 text, dati_transazione16 text, dati_transazione17 text, dati_transazione18 text, ultimo_accesso $DATETIME)");
esegui_query("insert into  ".$PHPR_TAB_PRE."transazioniweb (idtransazioni, anno) values ('2', '100')");
esegui_query("alter table ".$PHPR_TAB_PRE."clienti rename to ".$PHPR_TAB_PRE."clien");
esegui_query("create table ".$PHPR_TAB_PRE."clienti (idclienti integer primary key, cognome varchar(50) not null, nome varchar(50), sesso char, cognome2 varchar(50), nome2 varchar(50), sesso2 char, datanascita date, datanascita2 date, cittanascita varchar(50), cittanascita2 varchar(50), nazione varchar(50), citta varchar(50), via varchar(50), numcivico varchar(20), cap varchar(10), telefono varchar(50), telefono2 varchar(50), telefono3 varchar(50), fax varchar(50), email text, commento text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
esegui_query("insert into ".$PHPR_TAB_PRE."clienti (idclienti,cognome,nome,sesso,cognome2,nome2,sesso2,datanascita,datanascita2,cittanascita,cittanascita2,nazione,citta,via,numcivico,cap,telefono,telefono2,telefono3,fax,commento,datainserimento,hostinserimento,utente_inserimento) select idclienti,cognome,nome,sesso,cognome2,nome2,sesso2,datanascita,datanascita2,cittanascita,cittanascita2,nazione,citta,via,numcivico,cap,telefono,telefono2,telefono3,fax,commento,datainserimento,hostinserimento,utente_inserimento from ".$PHPR_TAB_PRE."clien ");
$clienti = esegui_query("select idclienti,email from ".$PHPR_TAB_PRE."clien ");
for ($num1 = 0 ; $num1 < numlin_query($clienti) ; $num1++) {
$idclienti = risul_query($clienti,$num1,'idclienti');
$email = risul_query($clienti,$num1,'email');
esegui_query("update ".$PHPR_TAB_PRE."clienti set email = '".addslashes($email)."' where idclienti = '$idclienti' ");
} # fine for $num1
esegui_query("drop table ".$PHPR_TAB_PRE."clien ");
$lingua_mex_orig = $lingua_mex;
@include("./dati/lingua.php");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tipo_periodi = esegui_query("select * from ".$PHPR_TAB_PRE."anni where idanni = '$anno_mostra'");
$tipo_periodi = risul_query($tipo_periodi,0,'tipo_periodi');
if ($tipo_periodi == "s") $aggiungi_giorni = 7;
else $aggiungi_giorni = 1;
for ($num2 = 0 ; $num2 < numlin_query($utenti) ; $num2++) {
$idutente_mostra = risul_query($utenti,$num2,'idutenti');
$lingua_mex = $lingua[$idutente_mostra];
$file1 = "./dati/selectperiodi$anno_mostra.$idutente_mostra".".php";
$file2 = "./dati/selperiodimenu$anno_mostra.$idutente_mostra".".php";
for ($num3 = 1 ; $num3 <= 2 ; $num3++) {
$file = ${"file".$num3};
$file_intero = @file($file);
if ($file_intero) {
$fileaperto = fopen("$file","w+");
flock($fileaperto,2);
$date_option = "";
$n_date_menu = 0;
for ($num4 = 0 ; $num4 < count($file_intero) ; $num4++) {
if (substr($file_intero[$num4],0,7) == "<option") {
$data_option = substr($file_intero[$num4],16,10);
if (!$date_option) {
$a_ini_menu = substr($data_option,0,4);
$m_ini_menu = (substr($data_option,5,2) - 1);
$g_ini_menu = substr($data_option,8,2);
} # fine if (!$date_option)
$n_date_menu++;
$date_option .= str_replace("$data_option\\\"\$sel".str_replace("-","",$data_option).">","$data_option\\\">",$file_intero[$num4]);
} # fine if (substr($file_intero[$num4],0,7) == "<option")
} # fine for $num4
fwrite($fileaperto,"<?php 

\$y_ini_menu[0] = \"$a_ini_menu\";
\$m_ini_menu[0] = \"$m_ini_menu\";
\$d_ini_menu[0] = \"$g_ini_menu\";
\$n_dates_menu[0] = \"$n_date_menu\";
\$d_increment[0] = \"$aggiungi_giorni\";
\$d_names = \"\\\"".mex(" Do","inizio.php")."\\\",\\\"".mex(" Lu","inizio.php")."\\\",\\\"".mex(" Ma","inizio.php")."\\\",\\\"".mex(" Me","inizio.php")."\\\",\\\"".mex(" Gi","inizio.php")."\\\",\\\"".mex(" Ve","inizio.php")."\\\",\\\"".mex(" Sa","inizio.php")."\\\"\";
\$m_names = \"\\\"".mex("Gen","inizio.php")."\\\",\\\"".mex("Feb","inizio.php")."\\\",\\\"".mex("Mar","inizio.php")."\\\",\\\"".mex("Apr","inizio.php")."\\\",\\\"".mex("Mag","inizio.php")."\\\",\\\"".mex("Giu","inizio.php")."\\\",\\\"".mex("Lug","inizio.php")."\\\",\\\"".mex("Ago","inizio.php")."\\\",\\\"".mex("Set","inizio.php")."\\\",\\\"".mex("Ott","inizio.php")."\\\",\\\"".mex("Nov","inizio.php")."\\\",\\\"".mex("Dic","inizio.php")."\\\"\";

\$dates_options_list = \"

$date_option
\";

?>");
flock($fileaperto,3);
fclose($fileaperto);
} # fine if ($file_intero)
} # fine for $num3
} # fine for $num2
} # fine for $num1
$lingua_mex = $lingua_mex_orig;
} # fine if ($versione_corrente < "0.53")

if ($versione_corrente < "0.54") {
$aggiornato = "SI";
esegui_query("create table ".$PHPR_TAB_PRE."contratti (numero integer, tipo varchar(8), testo text )");
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('mostra_quadro_disp','$idutente_mostra','')");
} # fine for $num1
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('ultime_sel_ins_prezzi','1','')");
} # fine if ($versione_corrente < "0.54")

if ($versione_corrente < "0.55") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
$costiprenota_cambia = esegui_query("select * from $tablecostiprenota where vartariffeassociate != ''");
$num_costiprenota_cambia = numlin_query($costiprenota_cambia);
for ($num2 = 0 ; $num2 < $num_costiprenota_cambia ; $num2++) {
$idcostiprenota = addslashes(risul_query($costiprenota_cambia,$num2,'idcostiprenota'));
$vartariffeassociate = addslashes(risul_query($costiprenota_cambia,$num2,'vartariffeassociate'));
if ($vartariffeassociate) esegui_query("update $tablecostiprenota set vartariffeassociate = 'p$vartariffeassociate' where idcostiprenota = '$idcostiprenota'");
} # fine for $num2
$max_idprenota = esegui_query("select max(idprenota) from $tableprenota");
$max_idprenota = risul_query($max_idprenota,0,0) + 1;
$max_idcostiprenota = esegui_query("select max(idcostiprenota) from $tablecostiprenota");
$max_idcostiprenota = risul_query($max_idcostiprenota,0,0) + 1;
esegui_query("update $tablecostiprenota set idcostiprenota = '$max_idcostiprenota' where idcostiprenota = '1'");
esegui_query("insert into $tablecostiprenota (idcostiprenota,numlimite) values ('1','$max_idprenota')");
} # fine for $num1
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('auto_crea_anno','1','NO')");
} # fine if ($versione_corrente < "0.55")

if ($versione_corrente < "0.60") {
$aggiornato = "SI";
esegui_query("create table ".$PHPR_TAB_PRE."messaggi (idmessaggi integer primary key, tipo_messaggio varchar(8), idutenti text, idutenti_visto text, datavisione $DATETIME, mittente text, testo text, dati_messaggio1 text, dati_messaggio2 text, dati_messaggio3 text, dati_messaggio4 text, dati_messaggio5 text, dati_messaggio6 text, dati_messaggio7 text, dati_messaggio8 text, dati_messaggio9 text, dati_messaggio10 text, dati_messaggio11 text, dati_messaggio12 text, dati_messaggio13 text, dati_messaggio14 text, dati_messaggio15 text, dati_messaggio16 text, dati_messaggio17 text, dati_messaggio18 text, datainserimento $DATETIME )");
function contr_utf8 ($testo) {
if (utf8_encode(utf8_decode($testo)) == $testo) $risul = 1;
else $risul = 0;
return $risul;
} # fine function contr_utf8
function converti_encode_tabella ($tabella) {
global $PHPR_TAB_PRE;
$contenuto_tab = esegui_query("select * from ".$PHPR_TAB_PRE."$tabella");
$num_colonne = numcampi_query($contenuto_tab);
for ($num1 = 0 ; $num1 < $num_colonne ; $num1++) {
$nome_colonna = nomecampo_query($contenuto_tab,$num1);
$contenuto_col = esegui_query("select distinct $nome_colonna from ".$PHPR_TAB_PRE."$tabella where $nome_colonna is not NULL and $nome_colonna != ''");
$num_righe = numlin_query($contenuto_col);
for ($num2 = 0 ; $num2 < $num_righe ; $num2++) {
$val = risul_query($contenuto_col,$num2,$nome_colonna);
if (!contr_utf8($val)) esegui_query("update ".$PHPR_TAB_PRE."$tabella set $nome_colonna = '".addslashes(utf8_encode($val))."' where $nome_colonna = '".addslashes($val)."'");
} # fine for $num2
} # fine for $num1
} # fine function converti_encode_tabella
function converti_encode_file ($file) {
$contenuto_file = implode("",file($file));
if (!contr_utf8($contenuto_file)) {
$contenuto_file = utf8_encode($contenuto_file);
scrivi_file($contenuto_file,$file);
} # fine if (!contr_utf8($contenuto_file))
} # fine function converti_encode_file
converti_encode_file("./dati/dati_connessione.php");
converti_encode_file("./dati/lingua.php");
converti_encode_file("./dati/tema.php");
converti_encode_file("./dati/selectappartamenti.php");
converti_encode_file("./dati/versione.php");
if (@is_file("./dati/abilita_login")) converti_encode_file("./dati/abilita_login");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_reg = risul_query($anni,$num1,'idanni');
for ($num2 = 0 ; $num2 < numlin_query($utenti) ; $num2++) {
$idutente_reg = risul_query($utenti,$num2,'idutenti');
$file = "./dati/selectperiodi$anno_reg.$idutente_reg.php";
if (@is_file("./dati/selectperiodi$anno_reg.$idutente_reg.php")) converti_encode_file("./dati/selectperiodi$anno_reg.$idutente_reg.php");
if (@is_file("./dati/selperiodimenu$anno_reg.$idutente_reg.php")) converti_encode_file("./dati/selperiodimenu$anno_reg.$idutente_reg.php");
} # fine for $num2
} # fine for $num1
converti_encode_tabella("anni");
converti_encode_tabella("appartamenti");
converti_encode_tabella("clienti");
converti_encode_tabella("personalizza");
converti_encode_tabella("versioni");
converti_encode_tabella("utenti");
converti_encode_tabella("privilegi");
converti_encode_tabella("sessioni");
converti_encode_tabella("transazioni");
converti_encode_tabella("transazioniweb");
converti_encode_tabella("contratti");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_reg = risul_query($anni,$num1,'idanni');
converti_encode_tabella("prenota$anno_reg");
converti_encode_tabella("costiprenota$anno_reg");
converti_encode_tabella("periodi$anno_reg");
converti_encode_tabella("ntariffe$anno_reg");
converti_encode_tabella("regole$anno_reg");
converti_encode_tabella("soldi$anno_reg");
converti_encode_tabella("costi$anno_reg");
} # fine for $num1
$priv_reg1_cons = esegui_query("select idutente,anno,regole1_consentite from ".$PHPR_TAB_PRE."privilegi where regole1_consentite is not NULL and regole1_consentite != ''");
for ($num1 = 0 ; $num1 < numlin_query($priv_reg1_cons) ; $num1++) {
$reg1_cons_mod = str_replace("#@£","#@^",risul_query($priv_reg1_cons,$num1,'regole1_consentite'));
$idutente_mod = risul_query($priv_reg1_cons,$num1,'idutente');
$anno_mod = risul_query($priv_reg1_cons,$num1,'anno');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set regole1_consentite = '".addslashes($reg1_cons_mod)."' where idutente = '$idutente_mod' and anno = '$anno_mod'");
} # fine for $num1
esegui_query("alter table ".$PHPR_TAB_PRE."clienti add column regione varchar(50)");
esegui_query("alter table ".$PHPR_TAB_PRE."clienti add column documento varchar(60)");
esegui_query("alter table ".$PHPR_TAB_PRE."clienti add column documento2 varchar(60)");
esegui_query("alter table ".$PHPR_TAB_PRE."clienti add column scadenzadoc date");
esegui_query("alter table ".$PHPR_TAB_PRE."clienti add column scadenzadoc2 date");
esegui_query("alter table ".$PHPR_TAB_PRE."privilegi add column priv_messaggi varchar(10)");
for ($num1 = 1 ; $num1 <= 18 ; $num1++) {
if (strlen($num1) == 1) $num_contr = "0".$num1;
else $num_contr = $num1;
$contratto = implode("",file("./contr/contr".$num_contr.".php"));
if (!contr_utf8($contratto)) $contratto = utf8_encode($contratto);
$contratto = preg_split("/[$]contratto *= *\"/",$contratto);
$contratto = $contratto[1];
$contratto_exp = explode("\"",$contratto);
$contratto = substr($contratto,0,(-1 - strlen($contratto_exp[(count($contratto_exp) - 1)])));
$contratto = str_replace("\\\"","\"",$contratto);
unset($var_da_sostituire);
$leggendo_var = "NO";
for ($num2 = 0 ; $num2 < strlen($contratto) ; $num2++) {
$car = substr($contratto,$num2,1);
if ($leggendo_var == "SI") {
if (preg_replace("/[A-Za-z0-9_]/","",$car) == "") $var .= $car;
else {
$leggendo_var = "NO";
$var_da_sostituire[$var] = "SI";
} # fine else if (preg_replace("/[a-z0-9_]/","",$car) == "")
} # fine if ($leggendo_var == "SI")
if ($car == "\$" and substr($contratto,($num2 - 1),1) != "\\") {
$leggendo_var = "SI";
$var = "";
} # fine if (substr($contratto,$num2,1) == "\$" and...
} # fine for $num2
if ($leggendo_var == "SI") $var_da_sostituire[$var] = "SI";
if (@is_array($var_da_sostituire)) {
krsort($var_da_sostituire);
reset($var_da_sostituire);
foreach ($var_da_sostituire as $key => $val) {
$contratto = str_replace("\$".$key,"[".$key."]",$contratto);
} # fine foreach ($var_da_sostituire as $key => $val)
} # fine if (@is_array($var_da_sostituire))
$contratto = addslashes($contratto);
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('$num1','contrhtm','$contratto')");
@unlink("./contr/contr".$num_contr.".php");
} # fine for $num1
@rmdir("./contr/");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('1','var','Mr')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('2','var','Mr2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('3','var','il')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('4','var','Il')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('5','var','al')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('6','var','e')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('7','var','o')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('8','var','il2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('9','var','Il2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('10','var','al2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('11','var','e2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('12','var','o2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('13','var','el')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('14','var','El')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('15','var','al3')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('16','var','a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('17','var','o3')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('18','var','el2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('19','var','El2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('20','var','al4')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('21','var','a2')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('22','var','o4')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('1','cond','if#%?sex#%?=#%?f#%?1#%?s')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('2','cond','if#%?sex2#%?=#%?f#%?2#%?s')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('3','cond','if#%?sesso#%?!=#%?f#%?3#%?il')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('4','cond','if#%?sesso#%?=#%?f#%?3#%?la')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('5','cond','if#%?sesso#%?!=#%?f#%?4#%?Il')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('6','cond','if#%?sesso#%?=#%?f#%?4#%?La')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('7','cond','if#%?sesso#%?!=#%?f#%?5#%?al')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('8','cond','if#%?sesso#%?=#%?f#%?5#%?alla')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('9','cond','if#%?sesso#%?!=#%?f#%?6#%?e')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('10','cond','if#%?sesso#%?=#%?f#%?6#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('11','cond','if#%?sesso#%?!=#%?f#%?7#%?o')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('12','cond','if#%?sesso#%?=#%?f#%?7#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('13','cond','if#%?sesso2#%?!=#%?f#%?8#%?il')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('14','cond','if#%?sesso2#%?=#%?f#%?8#%?la')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('15','cond','if#%?sesso2#%?!=#%?f#%?9#%?Il')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('16','cond','if#%?sesso2#%?=#%?f#%?9#%?La')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('17','cond','if#%?sesso2#%?!=#%?f#%?10#%?al')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('18','cond','if#%?sesso2#%?=#%?f#%?10#%?alla')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('19','cond','if#%?sesso2#%?!=#%?f#%?11#%?e')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('20','cond','if#%?sesso2#%?=#%?f#%?11#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('21','cond','if#%?sesso2#%?!=#%?f#%?12#%?o')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('22','cond','if#%?sesso2#%?=#%?f#%?12#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('23','cond','if#%?sexo#%?!=#%?f#%?13#%?el')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('24','cond','if#%?sexo#%?=#%?f#%?13#%?la')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('25','cond','if#%?sexo#%?!=#%?f#%?14#%?El')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('26','cond','if#%?sexo#%?=#%?f#%?14#%?La')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('27','cond','if#%?sexo#%?!=#%?f#%?15#%?al')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('28','cond','if#%?sexo#%?=#%?f#%?15#%?a la')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('29','cond','if#%?sexo#%?=#%?f#%?16#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('30','cond','if#%?sexo#%?!=#%?f#%?17#%?o')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('31','cond','if#%?sexo#%?=#%?f#%?17#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('32','cond','if#%?sexo2#%?!=#%?f#%?18#%?el')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('33','cond','if#%?sexo2#%?=#%?f#%?18#%?la')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('34','cond','if#%?sexo2#%?!=#%?f#%?19#%?El')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('35','cond','if#%?sexo2#%?=#%?f#%?19#%?La')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('36','cond','if#%?sexo2#%?!=#%?f#%?20#%?al')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('37','cond','if#%?sexo2#%?=#%?f#%?20#%?a la')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('38','cond','if#%?sexo2#%?=#%?f#%?21#%?a')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('39','cond','if#%?sexo2#%?!=#%?f#%?22#%?o')");
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('40','cond','if#%?sexo2#%?=#%?f#%?22#%?a')");
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('tipi_documento','$idutente_mostra','')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('indirizzo_email','$idutente_mostra','')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('selezione_tab_tutte_prenota','$idutente_mostra','tutte')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('linee_ripeti_date_tab_mesi','$idutente_mostra','25')");
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_messaggi = 'nn' where idutente = '$idutente_mostra' and anno = '1' ");
} # fine for $num1
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_ins_prec = risul_query($privilegi_anni,$num1,'priv_ins_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_ins_prenota = '".$priv_ins_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = '25' where idpersonalizza = 'tutti_fissi' and idutente = '1' and valpersonalizza = 'NO'");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('maschera_email','1','NO')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('percorso_cartella_modello','1','./dati')");
$file_tema = implode("",@file("./dati/tema.php"));
$file_tema = str_replace("<?php","<?php
\$parole_sost = 0;",$file_tema);
scrivi_file($file_tema,"./dati/tema.php");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno_mostra;
$soldi_prenota = esegui_query("select * from $tablesoldi where saldo_prenota != '' order by idsoldi");
$num_soldi_prenota = numlin_query($soldi_prenota);
for ($num2 = 0 ; $num2 < $num_soldi_prenota ; $num2++) {
$motivazione = risul_query($soldi_prenota,$num2,'motivazione');
$id_soldi = risul_query($soldi_prenota,$num2,'idsoldi');
esegui_query("update $tablesoldi set motivazione = '$motivazione".";' where idsoldi = '$id_soldi'");
} # fine for $num2
} # fine for $num1
} # fine if ($versione_corrente < "0.60")

if ($versione_corrente < "0.61") {
$aggiornato = "SI";
if (@is_file("./dati/dati_subordinazione.php")) $subord = "SI";
else $subord = "NO";
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('subordinazione','1','$subord')");
} # fine if ($versione_corrente < "0.61")

if ($versione_corrente < "0.62") {
$aggiornato = "SI";
$apertura_tag_font_rosse = "<font color=\"red\"><b>";
$chiusura_tag_font_rosse = "</b></font>";
$mostra_calendario_scelta_date = "NO";
$stile_riquadro_calendario = "style=\"z-index: 1; visibility: hidden; position: absolute; top: 0px; left: 0px; background: #FFFFFF; padding: 2px; border: 1px solid #000000; font: bold 10px Verdana, Arial, Helvetica, sans-serif; color: #000000; text-align: center;\"";
$stile_tabella_calendario = "style=\"border-collapse: collapse; font-size: 10px; margin-left: 1%; margin-right: 1%; cursor: default; text-align: center; padding: 2px\"";
$stile_bottoni_calendario = "style=\"font-size: 9px; padding: 0 3px 0 3px; border-color: #333333; border-width: 1px;\"";
$stile_bottone_apertura_calendario = "style=\"padding: 0; border-color: #333333; border-width: 1px;\"";
$colore_data_attiva_calendario = "#d8e1e6";
$colore_data_selezionata_calendario = "#eeeeee";
esegui_query("alter table ".$PHPR_TAB_PRE."transazioni add column dati_transazione19 text");
esegui_query("alter table ".$PHPR_TAB_PRE."transazioni add column dati_transazione20 text");
esegui_query("alter table ".$PHPR_TAB_PRE."transazioniweb add column dati_transazione19 text");
esegui_query("alter table ".$PHPR_TAB_PRE."transazioniweb add column dati_transazione20 text");
esegui_query("alter table ".$PHPR_TAB_PRE."messaggi add column dati_messaggio19 text");
esegui_query("alter table ".$PHPR_TAB_PRE."messaggi add column dati_messaggio20 text");
$transazioniweb = esegui_query("select * from ".$PHPR_TAB_PRE."transazioniweb");
for ($num1 = 0 ; $num1 < numlin_query($transazioniweb) ; $num1++) {
$id_trans = risul_query($transazioniweb,$num1,'idtransazioni');
$dati_richiedente_dt = explode(";;",risul_query($transazioniweb,$num1,'dati_transazione15'));
$cognome_richiedente = $dati_richiedente_dt[0];
$nome_richiedente = $dati_richiedente_dt[1];
$email_richiedente = $dati_richiedente_dt[2];
$nazione = $dati_richiedente_dt[3];
$citta = $dati_richiedente_dt[4];
$cap = $dati_richiedente_dt[5];
$via = $dati_richiedente_dt[6];
$telefono = $dati_richiedente_dt[7];
$dati_transazione15 = addslashes($cognome_richiedente)."<d>".addslashes($nome_richiedente)."<d>".addslashes($email_richiedente)."<d><d><d><d>".addslashes($nazione)."<d><d>";
$dati_transazione15 .= addslashes($citta)."<d>".addslashes($via)."<d><d>".addslashes($cap)."<d>".addslashes($telefono)."<d><d><d><d>";
esegui_query("update ".$PHPR_TAB_PRE."transazioniweb set dati_transazione15 = '$dati_transazione15' where idtransazioni = '$id_trans'");
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
esegui_query("alter table $tableprenota add column codice varchar(10)");
$prenota = esegui_query("select idprenota from $tableprenota");
$num_prenota = numlin_query($prenota);
srand((double)microtime() * 1000000);
for ($num2 = 0 ; $num2 < $num_prenota ; $num2++) {
$id_prenota = risul_query($prenota,$num2,'idprenota');
$valori = "abcdefghijkmnpqrstuvwxz";
unset($cod_prenota);
for ($num3 = 0 ; $num3 < 4 ; $num3++) $cod_prenota .= substr($valori,rand(0,22),1);
esegui_query("update $tableprenota set codice = '$cod_prenota' where idprenota = '$id_prenota'");
} # fine for $num2
} # fine for $num1
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_prenota = '".$priv_mod_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
} # fine if ($versione_corrente < "0.62")

if ($versione_corrente < "0.63") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.63")

if ($versione_corrente < "0.64") {
$aggiornato = "SI";
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_prenota = '".$priv_mod_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
esegui_query("create table ".$PHPR_TAB_PRE."descrizioni (nome varchar(16) primary key, testo $MEDIUMTEXT )");
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('mostra_giorni_tab_mesi','$idutente_mostra','NO')");
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe". $anno_mostra;
$tableregole = $PHPR_TAB_PRE."regole".$anno_mostra;
esegui_query("alter table $tablenometariffe add column regoleassegna_ca varchar(30)");
esegui_query("update $tablenometariffe set regoleassegna_ca = ';' where idntariffe > 4");
esegui_query("alter table $tableregole add column tariffa_per_persone text");
} # fine for $num1
} # fine if ($versione_corrente < "0.64")

if ($versione_corrente < "0.70") {
$aggiornato = "SI";
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_prenota = '".$priv_mod_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
$tableclienti = $PHPR_TAB_PRE."clienti";
esegui_query("alter table $tableclienti add column nazionalita varchar(70)");
esegui_query("alter table $tableclienti add column regionenascita varchar(70)");
esegui_query("alter table $tableclienti add column nazionenascita varchar(70)");
esegui_query("alter table $tableclienti add column tipodoc varchar(70)");
esegui_query("alter table $tableclienti add column cittadoc varchar(70)");
esegui_query("alter table $tableclienti add column nazionedoc varchar(70)");
esegui_query("alter table $tableclienti add column max_num_ordine integer");
esegui_query("update $tableclienti set max_num_ordine = '1' ");
esegui_query("alter table $tableclienti add column idclienti_compagni text");
esegui_query("update $tableclienti set idclienti_compagni = ',' ");
$clienti2 = esegui_query("select * from $tableclienti where cognome2 != '' ");
$num_clienti2 = numlin_query($clienti2);
$max_idclienti = esegui_query("select max(idclienti) from $tableclienti");
$max_idclienti = risul_query($max_idclienti,0,0) + 1;
for ($num1 = 0 ; $num1 < $num_clienti2 ; $num1++) {
$idclienti = risul_query($clienti2,$num1,'idclienti');
$cognome2 = aggslashdb(risul_query($clienti2,$num1,'cognome2'));
$nome2 = aggslashdb(risul_query($clienti2,$num1,'nome2'));
$sesso2 = risul_query($clienti2,$num1,'sesso2');
$datanascita2 = risul_query($clienti2,$num1,'datanascita2');
$documento2 = aggslashdb(risul_query($clienti2,$num1,'documento2'));
$scadenzadoc2 = risul_query($clienti2,$num1,'scadenzadoc2');
$datainserimento = risul_query($clienti2,$num1,'datainserimento');
$hostinserimento = aggslashdb(risul_query($clienti2,$num1,'hostinserimento'));
$utente_inserimento = risul_query($clienti2,$num1,'utente_inserimento');
esegui_query("insert into $tableclienti (idclienti,cognome,nome,documento,max_num_ordine,idclienti_compagni,datainserimento,hostinserimento,utente_inserimento) values ('$max_idclienti','$cognome2','$nome2','$documento2','2',',$idclienti,','$datainserimento','$hostinserimento','$utente_inserimento')");
if ($sesso2) esegui_query("update $tableclienti set sesso = '$sesso2' where idclienti = '$max_idclienti' ");
if ($datanascita2) esegui_query("update $tableclienti set datanascita = '$datanascita2' where idclienti = '$max_idclienti' ");
if ($scadenzadoc2) esegui_query("update $tableclienti set scadenzadoc = '$scadenzadoc2' where idclienti = '$max_idclienti' ");
esegui_query("update $tableclienti set idclienti_compagni = ',$max_idclienti,' where idclienti = '$idclienti' ");
$nuovo_idclienti[$idclienti] = $max_idclienti;
$max_idclienti++;
} # fine for $num1
$tableclienti_temp = $PHPR_TAB_PRE."clien";
esegui_query("alter table $tableclienti rename to $tableclienti_temp ");
esegui_query("create table $tableclienti (idclienti integer primary key, cognome varchar(70) not null, nome varchar(70), sesso char, datanascita date, cittanascita varchar(70), regionenascita varchar(70), nazionenascita varchar(70), documento varchar(70), scadenzadoc date, tipodoc varchar(70), cittadoc varchar(70), nazionedoc  varchar(70), nazionalita varchar(70), nazione varchar(70), regione varchar(70), citta varchar(70), via varchar(70), numcivico varchar(30), cap varchar(30), telefono varchar(50), telefono2 varchar(50), telefono3 varchar(50), fax varchar(50), email text, commento text, max_num_ordine integer, idclienti_compagni text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
esegui_query("insert into $tableclienti (idclienti,cognome,nome,sesso,datanascita,cittanascita,regionenascita,nazionenascita,documento,scadenzadoc,tipodoc,cittadoc,nazionedoc,nazionalita,nazione,regione,citta,via,numcivico,cap,telefono,telefono2,telefono3,fax,email,commento,max_num_ordine,idclienti_compagni,datainserimento,hostinserimento,utente_inserimento) select idclienti,cognome,nome,sesso,datanascita,cittanascita,regionenascita,nazionenascita,documento,scadenzadoc,tipodoc,cittadoc,nazionedoc,nazionalita,nazione,regione,citta,via,numcivico,cap,telefono,telefono2,telefono3,fax,email,commento,max_num_ordine,idclienti_compagni,datainserimento,hostinserimento,utente_inserimento from $tableclienti_temp ");
esegui_query("drop table $tableclienti_temp ");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno_mostra;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
esegui_query("create table $tablerclientiprenota (idprenota integer, idclienti integer, num_ordine integer, parentela varchar(70), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer)");
crea_indice($tablerclientiprenota,"idprenota",$PHPR_TAB_PRE."iidprclientiprenota".$anno_mostra);
$prenota_anno = esegui_query("select * from $tableprenota");
$num_prenota_anno = numlin_query($prenota_anno);
for ($num2 = 0 ; $num2 < $num_prenota_anno ; $num2++) {
$idprenota = risul_query($prenota_anno,$num2,'idprenota');
$idclienti = risul_query($prenota_anno,$num2,'idclienti');
$datainserimento = risul_query($prenota_anno,$num2,'datainserimento');
$hostinserimento = aggslashdb(risul_query($prenota_anno,$num2,'hostinserimento'));
$utente_inserimento = risul_query($prenota_anno,$num2,'utente_inserimento');
esegui_query("insert into $tablerclientiprenota (idprenota,idclienti,num_ordine,datainserimento,hostinserimento,utente_inserimento) values ('$idprenota','$idclienti','1','$datainserimento','$hostinserimento','$utente_inserimento') ");
} # fine for $num2
for ($num2 = 0 ; $num2 < $num_clienti2 ; $num2++) {
$idclienti = risul_query($clienti2,$num2,'idclienti');
$prenota_clienti2 = esegui_query("select * from $tableprenota where idclienti = '$idclienti' ");
for ($num3 = 0 ; $num3 < numlin_query($prenota_clienti2) ; $num3++) {
$idprenota = risul_query($prenota_clienti2,$num3,'idprenota');
$datainserimento = risul_query($prenota_clienti2,$num3,'datainserimento');
$hostinserimento = aggslashdb(risul_query($prenota_clienti2,$num3,'hostinserimento'));
$utente_inserimento = risul_query($prenota_clienti2,$num3,'utente_inserimento');
esegui_query("insert into $tablerclientiprenota (idprenota,idclienti,num_ordine,hostinserimento) values ('$idprenota','".$nuovo_idclienti[$idclienti]."','2','$hostinserimento') ");
if ($datainserimento) esegui_query("update $tablerclientiprenota set datainserimento = '$datainserimento' where idprenota = '$idprenota' and idclienti = '".$nuovo_idclienti[$idclienti]."' ");
if ($utente_inserimento) esegui_query("update $tablerclientiprenota set utente_inserimento = '$utente_inserimento' where idprenota = '$idprenota' and idclienti = '".$nuovo_idclienti[$idclienti]."' ");
} # fine for $num3
} # fine for $num2
} # fine for $num1
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('minuti_durata_insprenota','1','15')");
$tablenazioni = $PHPR_TAB_PRE."nazioni";
esegui_query("create table $tablenazioni (idnazioni integer primary key, nome_nazione varchar(70), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tableregioni = $PHPR_TAB_PRE."regioni";
esegui_query("create table $tableregioni (idregioni integer primary key, nome_regione varchar(70), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tableparentele = $PHPR_TAB_PRE."parentele";
esegui_query("create table $tableparentele (idparentele integer primary key, nome_parentela varchar(70), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
esegui_query("create table $tablerelutenti (idutente integer not null, idnazione integer, idregione integer, idparentela integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
} # fine if ($versione_corrente < "0.70")

if ($versione_corrente < "0.71") {
$aggiornato = "SI";
$cond_contr = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo = 'cond' ");
for ($num1 = 0 ; $num1 < numlin_query($cond_contr) ; $num1++) {
$cond = risul_query($cond_contr,$num1,"testo");
if (substr($cond,0,5) == "if#%?") {
$num_cond = risul_query($cond_contr,$num1,"numero");
$condizione = explode("#%?",$cond);
$len = -1 * (strlen($condizione[5]));
$cond = substr($cond,0,$len);
$cond .= "=#%?".$condizione[5];
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '".aggslashdb($cond)."' where numero = '$num_cond' and tipo = 'cond' ");
} # fine if (substr($cond,0,5) == "if#%?")
} # fine for $num1
$tableinterconnessioni = $PHPR_TAB_PRE."interconnessioni";
esegui_query("create table $tableinterconnessioni (idlocale integer, idremoto1 text, idremoto2 text, tipoid varchar(12), nome_ic varchar(24), anno integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$stile_tabella_prenotazione = "border=1 cellpadding=5 cellspacing=1";
$tableclienti = $PHPR_TAB_PRE."clienti";
esegui_query("alter table $tableclienti add column cod_fiscale varchar(50)");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno_mostra;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
esegui_query("alter table $tablesoldi add column id_pagamento text ");
esegui_query("alter table $tableprenota add column origine varchar(70) ");
} # fine for $num1
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('origini_prenota','$idutente_mostra','')");
} # fine for $num1
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv = risul_query($privilegi_anni,$num1,'anno');
$idutente_priv = risul_query($privilegi_anni,$num1,'idutente');
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
$priv_ins_prec = risul_query($privilegi_anni,$num1,'priv_ins_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_prenota = '".$priv_mod_prec."n', priv_ins_prenota = '".$priv_ins_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
} # fine if ($versione_corrente < "0.71")

if ($versione_corrente < "0.72") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "0.72")

if ($versione_corrente < "1.00") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "1.00")

if ($versione_corrente < "1.01") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "1.01")

if ($versione_corrente < "1.10") {
$aggiornato = "SI";
$tablegruppi = $PHPR_TAB_PRE."gruppi";
esegui_query("create table $tablegruppi (idgruppi integer primary key, nome_gruppo text )");
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
esegui_query("create table $tablerelgruppi (idutente integer not null, idgruppo integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelgruppi,"idutente",$PHPR_TAB_PRE."iidprelgruppi");
$tablecitta = $PHPR_TAB_PRE."citta";
esegui_query("create table $tablecitta (idcitta integer primary key, nome_citta varchar(70), codice_citta varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
esegui_query("create table $tabledocumentiid (iddocumentiid integer primary key, nome_documentoid varchar(70), codice_documentoid varchar(50), datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tablebeniinventario = $PHPR_TAB_PRE."beniinventario";
esegui_query("create table $tablebeniinventario (idbeniinventario integer primary key, nome_bene varchar(70), codice_bene varchar(50), descrizione_bene text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tablemagazzini = $PHPR_TAB_PRE."magazzini";
esegui_query("create table $tablemagazzini (idmagazzini integer primary key, nome_magazzino varchar(70), codice_magazzino varchar(50), descrizione_magazzino text, numpiano text, numcasa text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
esegui_query("create table $tablerelinventario (idbeneinventario integer not null, idappartamento integer, idmagazzino integer, quantita integer, quantita_min_predef integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelinventario,"idbeneinventario",$PHPR_TAB_PRE."iidprelinventario");
$tableclienti = $PHPR_TAB_PRE."clienti";
esegui_query("alter table $tableclienti add column titolo varchar(30)");
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
esegui_query("alter table $tablerelutenti add column idcitta integer ");
esegui_query("alter table $tablerelutenti add column iddocumentoid integer ");
esegui_query("alter table $tablerelutenti add column idsup integer ");
esegui_query("alter table $tablerelutenti add column predef integer ");
crea_indice($tablerelutenti,"idutente",$PHPR_TAB_PRE."iidprelutenti");
$tableregioni = $PHPR_TAB_PRE."regioni";
esegui_query("alter table $tableregioni add column codice_regione varchar(50) ");
$tablenazioni = $PHPR_TAB_PRE."nazioni";
esegui_query("alter table $tablenazioni add column codice_nazione varchar(50) ");
$tableparentele = $PHPR_TAB_PRE."parentele";
esegui_query("alter table $tableparentele add column codice_parentela varchar(50) ");
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
esegui_query("alter table $tableprivilegi add column priv_inventario varchar(10) ");
esegui_query("update $tableprivilegi set priv_ins_tariffe = 'nnnn' where anno != '1' ");
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv = risul_query($privilegi_anni,$num1,'anno');
$idutente_priv = risul_query($privilegi_anni,$num1,'idutente');
$priv_vedi_tab_prec = risul_query($privilegi_anni,$num1,'priv_vedi_tab');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_vedi_tab = '".$priv_vedi_tab_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablentariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
esegui_query("alter table $tablentariffe add column beniinv_ca text ");
esegui_query("alter table $tablentariffe add column appincompatibili_ca text ");
esegui_query("alter table $tablentariffe add column utente_inserimento integer ");
esegui_query("update $tablentariffe set utente_inserimento = '1' where idntariffe > 4 ");
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
esegui_query("alter table $tablecostiprenota add column varbeniinv text ");
esegui_query("alter table $tablecostiprenota add column varappincompatibili text ");
} # fine for $num1
$filelock = crea_lock_file("./dati/dati_connessione.php");
$file_dati_conn = @file("./dati/dati_connessione.php");
unset($linee);
$num_lin = 0;
for ($num1 = 0 ; $num1 < count($file_dati_conn) ; $num1++) {
if (substr($file_dati_conn[$num1],0,2) == "?>") {
$linee[$num_lin] = "\$PHPR_LOG = \"NO\";
";
$num_lin++;
$linee[$num_lin] = $file_dati_conn[$num1];
} # fine if (substr($file_lingua[$num1],0,2) ==..
else $linee[$num_lin] = $file_dati_conn[$num1];
$num_lin++;
} # fine for $num1
scrivi_file ($linee,"dati/dati_connessione.php");
distruggi_lock_file($filelock,"dati/dati_connessione.php");
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
$tipi_documento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'tipi_documento' and idutente = '$idutente_mostra'");
$tipi_documento = risul_query($tipi_documento,0,'valpersonalizza');
if ($tipi_documento) {
$tipi_documento = explode(",",$tipi_documento);
for ($num2 = 0 ; $num2 < count($tipi_documento) ; $num2++) {
$tipo_doc = $tipi_documento[$num2];
$tipo_doc_esist = esegui_query("select iddocumentiid from $tabledocumentiid where nome_documentoid = '".aggslashdb($tipo_doc)."' ");
if (numlin_query($tipo_doc_esist) == 1) $num_documentoid = risul_query($tipo_doc_esist,0,'iddocumentiid');
else {
$num_documentoid = esegui_query("select max(iddocumentiid) from $tabledocumentiid ");
$num_documentoid = risul_query($num_documentoid,0,0) + 1;
esegui_query("insert into $tabledocumentiid (iddocumentiid,nome_documentoid) values ('$num_documentoid','".aggslashdb($tipo_doc)."') ");
} # fine else if (numlin_query($tipo_doc_esist) == 1)
esegui_query("insert into $tablerelutenti (idutente,iddocumentoid) values ('$idutente_mostra','$num_documentoid') ");
} # fine for $num2
} # fine if ($tipi_documento)
esegui_query("delete from $tablepersonalizza where idpersonalizza = 'tipi_documento' and idutente = '$idutente_mostra'");
} # fine for $num1
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") $percorso_cartella_modello = C_CARTELLA_CREA_MODELLI;
else {
$percorso_cartella_modello = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'percorso_cartella_modello' and idutente = '1'");
$percorso_cartella_modello = risul_query($percorso_cartella_modello,0,'valpersonalizza');
} # fine else if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
function aggiorna_nomi_modelli110 ($nome_vecchio,$nome_nuovo,$percorso_cartella_modello) {
if (@is_file("$percorso_cartella_modello/$nome_vecchio")) {
if (rename("$percorso_cartella_modello/$nome_vecchio","$percorso_cartella_modello/$nome_nuovo")) {
scrivi_file("<?php
include(\"./$nome_nuovo\");
?>","$percorso_cartella_modello/$nome_vecchio");
$nome_vecchio_inc = str_replace(".php","_inc.php",$nome_vecchio);
$nome_nuovo_inc = str_replace(".php","_inc.php",$nome_nuovo);
if (@is_file("$percorso_cartella_modello/$nome_vecchio_inc")) rename("$percorso_cartella_modello/$nome_vecchio_inc","$percorso_cartella_modello/$nome_nuovo_inc");
} # fine if (rename("$percorso_cartella_modello/$nome_vecchio","$percorso_cartella_modello/$nome_nuovo"))
} # fine if (@is_file("$percorso_cartella_modello/$nome_vecchio"))
} # fine function aggiorna_nomi_modelli110
aggiorna_nomi_modelli110("modello_disponibilita.php","mdl_disponibilita.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("availability_template.php","availability_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modelo_disponibilidad.php","mdl_disponibilidad.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("bg_availability_template.php","bg_availability_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modele_disponible.php","mdl_disponible.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("beschikbaarheids_malplaatje.php","beschikbaarheids_mlp.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modelo_disponibilidade.php","mdl_disponibilidade.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("ru_availability_template.php","ru_availability_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modello_calendario_disponibilita.php","mdl_calendario_disponibilita.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("availability_calendar_template.php","availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modelo_calendario_disponibilidad.php","mdl_calendario_disponibilidad.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("bg_availability_calendar_template.php","bg_availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("de_availability_calendar_template.php","de_availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("fr_availability_calendar_template.php","fr_availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("nl_availability_calendar_template.php","nl_availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("pt_availability_calendar_template.php","pt_availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("ru_availability_calendar_template.php","ru_availability_calendar_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("reservation_completion_template.php","confirm_reservation_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modello_completa_prenotazione.php","mdl_conferma_prenotazione.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modelo_completa_reserva.php","mdl_confirma_reserva.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("fr_reservation_completion_template.php","fr_confirm_reservation_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("paypal_booking_template.php","instant_booking_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modello_prenotazioni_paypal.php","mdl_prenotazione_immediata.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modelo_reservas_paypal.php","mdl_reserva_instantanea.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("fr_paypal_booking_template.php","fr_instant_booking_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("rates_table_template.php","rates_table_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modello_tabella_tariffe.php","mdl_tabella_tariffe.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("modelo_tabla_tarifas.php","mdl_tabla_tarifas.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("bg_rates_table_template.php","bg_rates_table_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("de_rates_table_template.php","de_rates_table_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("fr_rates_table_template.php","fr_rates_table_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("nl_rates_table_template.php","nl_rates_table_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("pt_rates_table_template.php","pt_rates_table_tpl.php",$percorso_cartella_modello);
aggiorna_nomi_modelli110("ru_rates_table_template.php","ru_rates_table_tpl.php",$percorso_cartella_modello);
unset($percorso_cartella_modello);
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
$c_cartella_crea_mod = C_CARTELLA_CREA_MODELLI;
if (substr($c_cartella_crea_mod,-1) == "/") $c_cartella_crea_mod = substr($c_cartella_crea_mod,0,-1);
esegui_query("update $tablepersonalizza set valpersonalizza = '$c_cartella_crea_mod' where idpersonalizza = 'percorso_cartella_modello' and idutente = '1'");
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
} # fine if ($versione_corrente < "1.10")

if ($versione_corrente < "1.11") {
$aggiornato = "SI";
$tableclienti = $PHPR_TAB_PRE."clienti";
esegui_query("alter table $tableclienti add column regionedoc varchar(70)");
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
esegui_query("update $tableprivilegi set priv_inventario = 'nnnnnnnnn' where anno = '1' ");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$tablenometariffe_temp = $PHPR_TAB_PRE."ntarif".$anno_mostra;
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
$lista_tariffe = "";
$lista_tariffe_crea = "";
for ($num2 = 1 ; $num2 <= $numero_tariffe ; $num2++) {
$nome_tariffa = "tariffa".$num2;
$lista_tariffe .= ",$nome_tariffa";
$lista_tariffe_crea .= ", $nome_tariffa varchar(40)";
} # fine for $num2
esegui_query("alter table $tablenometariffe rename to $tablenometariffe_temp ");
esegui_query("create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, arrotonda_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca varchar(1), periodipermessi_ca text, beniinv_ca text, appincompatibili_ca text, variazione_ca varchar(20), mostra_ca varchar(10), letto_ca varchar(1), numlimite_ca integer, regoleassegna_ca varchar(30), utente_inserimento integer$lista_tariffe_crea)");
esegui_query("insert into $tablenometariffe select idntariffe,nomecostoagg,tipo_ca,valore_ca,arrotonda_ca,associasett_ca,numsett_ca,moltiplica_ca,periodipermessi_ca,beniinv_ca,appincompatibili_ca,variazione_ca,mostra_ca,letto_ca,numlimite_ca,regoleassegna_ca,utente_inserimento$lista_tariffe from $tablenometariffe_temp ");
esegui_query("drop table $tablenometariffe_temp ");
$dati_ntariffe = esegui_query("select * from $tablenometariffe where idntariffe > 4 order by idntariffe");
for ($num2 = 0 ; $num2 < numlin_query($dati_ntariffe) ; $num2++) {
$idntariffe = risul_query($dati_ntariffe,$num2,'idntariffe');
$mostra_ca = risul_query($dati_ntariffe,$num2,'mostra_ca');
$variazione_ca = risul_query($dati_ntariffe,$num2,'variazione_ca');
esegui_query("update $tablenometariffe set mostra_ca = '$mostra_ca"."n', variazione_ca = '$variazione_ca"."nn' where idntariffe = '$idntariffe'");
} # fine for $num2
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tableprenota_temp = $PHPR_TAB_PRE."pren".$anno_mostra;
esegui_query("alter table $tableprenota add column incompatibilita text ");
esegui_query("alter table $tableprenota rename to $tableprenota_temp ");
esegui_query("create table $tableprenota (idprenota integer primary key, idclienti integer, idappartamenti varchar(100), iddatainizio integer, iddatafine integer, assegnazioneapp varchar(4), app_assegnabili text, num_persone integer, idprenota_compagna text, tariffa text, tariffesettimanali text, incompatibilita text, sconto float8, tariffa_tot float8, caparra float8, pagato float8, metodo_pagamento text, codice varchar(10), origine varchar(70), commento text, conferma varchar(4), checkin $DATETIME, checkout $DATETIME, datainserimento $DATETIME, hostinserimento varchar(50), data_modifica $DATETIME, utente_inserimento integer) ");
esegui_query("insert into $tableprenota select idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,app_assegnabili,num_persone,idprenota_compagna,tariffa,tariffesettimanali,incompatibilita,sconto,tariffa_tot,caparra,pagato,metodo_pagamento,codice,origine,commento,conferma,checkin,checkout,datainserimento,hostinserimento,data_modifica,utente_inserimento from $tableprenota_temp ");
esegui_query("drop table $tableprenota_temp ");
} # fine for $num1
global $trad_var;
$trad_var = "";
function mex111 ($messaggio) {
global $trad_var,$lingua_mex;
if (!$trad_var and $lingua_mex != "ita") include("./includes/lang/$lingua_mex/visualizza_contratto_var.php");
if ($trad_var[$messaggio]) $messaggio = $trad_var[$messaggio];
return $messaggio;
} # fine function mex111
include("./includes/variabili_contratto.php");
$cond_contr = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo = 'cond' ");
for ($num1 = 0 ; $num1 < numlin_query($cond_contr) ; $num1++) {
$num_cond = risul_query($cond_contr,$num1,'numero');
$cond = risul_query($cond_contr,$num1,'testo')."#%?";
if ($lingua_mex != "ita") {
for ($num2 = 0 ; $num2 < $num_var_predef ; $num2++) {
$cond = str_replace("#%?".mex111($var_predef[$num2])."#%?","#%?".$var_predef[$num2]."#%?",$cond);
} # fine for $num2
} # fine ($lingua_mex != "ita")
$cond = substr($cond,0,-3);
if (substr($cond,0,3) == "if3") $cond = "2if".substr($cond,3);
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '".aggslashdb($cond)."' where numero = '$num_cond' and tipo = 'cond' ");
} # fine for $num1
} # fine if ($versione_corrente < "1.11")

if ($versione_corrente < "1.2") {
$aggiornato = "SI";
$tableclienti = $PHPR_TAB_PRE."clienti";
esegui_query("alter table $tableclienti add column soprannome varchar(70)");
esegui_query("alter table $tableclienti add column partita_iva varchar(50)");
esegui_query("alter table $tableclienti add column doc_inviati text");
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tablerelinventario_temp = $PHPR_TAB_PRE."relinven";
esegui_query("alter table $tablerelinventario rename to $tablerelinventario_temp ");
esegui_query("create table $tablerelinventario (idbeneinventario integer not null, idappartamento varchar(100), idmagazzino integer, quantita integer, quantita_min_predef integer, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
esegui_query("insert into $tablerelinventario select idbeneinventario,idappartamento,idmagazzino,quantita,quantita_min_predef,datainserimento,hostinserimento,utente_inserimento from $tablerelinventario_temp ");
esegui_query("drop table $tablerelinventario_temp ");
esegui_query("delete from $tablerelinventario where idappartamento = '0' ");
esegui_query("alter table $tablerelinventario add column richiesto_checkin varchar(2)");
esegui_query("update $tablerelinventario set richiesto_checkin = 'n' ");
$tablerelclienti = $PHPR_TAB_PRE."relclienti";
esegui_query("create table $tablerelclienti (idclienti integer, numero integer, tipo varchar(12), testo1 text, testo2 text, testo3 text, testo4 text, testo5 text, testo6 text, testo7 text, testo8 text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer )");
crea_indice($tablerelclienti,"idclienti",$PHPR_TAB_PRE."iidprelclienti");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$riga4esist = esegui_query("select * from $tablenometariffe where idntariffe = '4'");
if (!numlin_query($riga4esist)) esegui_query("insert into $tablenometariffe (idntariffe) values ('4')");
$tableregole = $PHPR_TAB_PRE."regole".$anno_mostra;
esegui_query("alter table $tableregole add column motivazione2 text");
} # fine for $num1
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
esegui_query("alter table $tablemessaggi add column stato varchar(8)");
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$indirizzi_email = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'indirizzo_email'");
$num_ind_email = numlin_query($indirizzi_email);
for ($num1 = 0 ; $num1 < $num_ind_email ; $num1++) {
$idut_email = risul_query($indirizzi_email,$num1,'idutente');
$ind_email = risul_query($indirizzi_email,$num1,'valpersonalizza');
esegui_query("update $tablepersonalizza set idpersonalizza = 'dati_struttura', valpersonalizza = '#@&#@&$ind_email#@&#@&#@&#@&#@&#@&#@&#@&#@&' where idpersonalizza = 'indirizzo_email' and idutente = '$idut_email' ");
} # fine for $num1
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('arrotond_predef','$idutente_mostra','1')");
} # fine for $num1
$cond_contr = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'cond%' ");
for ($num1 = 0 ; $num1 < numlin_query($cond_contr) ; $num1++) {
$num_cond = risul_query($cond_contr,$num1,'numero');
$tipo_cond = risul_query($cond_contr,$num1,'tipo');
$condizione = explode("#%?",risul_query($cond_contr,$num1,'testo'));
$nuova_cond = "";
if ($condizione[0] == "ini") {
$nuova_cond = "inr#@?#@?set#%?".$condizione[1]."#%?=#%?txt#%?".$condizione[2]."#%?txt#%?#%?txt#%?";
} # fine if ($condizione[0] == "ini")
if ($condizione[0] == "set") {
$nuova_cond = "rpt#@?#@?set#%?".$condizione[1]."#%?=#%?txt#%?".$condizione[2]."#%?txt#%?#%?txt#%?";
} # fine if ($condizione[0] == "set")
if (substr($condizione[0],0,2) == "if") {
$nuova_cond = "rpt#@?#$?".$condizione[1]."#%?".$condizione[2]."#%?";
if ($condizione[0] == "if3" or $condizione[0] == "if4") $nuova_cond .= "var#%?".$condizione[3];
else $nuova_cond .= "txt#%?".$condizione[3];
$nuova_cond .= "#@?set#%?".$condizione[4]."#%?".$condizione[5]."#%?";
if ($condizione[0] == "if2" or $condizione[0] == "if4") $nuova_cond .= "var#%?".$condizione[6];
else $nuova_cond .= "txt#%?".$condizione[6];
$nuova_cond .= "#%?txt#%?#%?txt#%?";
} # fine if (substr($condizione[0],0,2) == "if")
if (substr($condizione[0],0,3) == "2if") {
$nuova_cond = "rpt#@?".$condizione[4]."#$?".$condizione[1]."#%?".$condizione[2]."#%?txt#%?".$condizione[3];
$nuova_cond .= "#$?".$condizione[5]."#%?".$condizione[6]."#%?txt#%?".$condizione[7];
$nuova_cond .= "#@?set#%?".$condizione[8]."#%?".$condizione[9]."#%?";
if ($condizione[0] == "2if2") $nuova_cond .= "var#%?".$condizione[10];
else $nuova_cond .= "txt#%?".$condizione[10];
$nuova_cond .= "#%?txt#%?#%?txt#%?";
} # fine if (substr($condizione[0],0,3) == "2if")
if ($condizione[0] == "replace" or $condizione[0] == "replace2") {
$nuova_cond = "rpt#@?#@?set#%?".$condizione[1]."#%?=#%?var#%?".$condizione[2];
if ($condizione[0] == "replace2") $nuova_cond .= "#%?var#%?".$condizione[3]."#%?var#%?".$condizione[4];
else $nuova_cond .= "#%?txt#%?".$condizione[3]."#%?txt#%?".$condizione[4];
} # fine if ($condizione[0] == "replace" or $condizione[0] == "replace2")
if ($condizione[0] == "trunc") {
$nuova_cond = "rpt#@?#@?trunc#%?".$condizione[1]."#%?".$condizione[2]."#%?".$condizione[3]."#%?".$condizione[4];
} # fine if ($condizione[0] == "trunc")
if ($condizione[0] == "oper" or $condizione[0] == "oper2") {
$nuova_cond = "rpt#@?#@?oper#%?".$condizione[1]."#%?".$condizione[2]."#%?".$condizione[3]."#%?";
if ($condizione[0] == "oper2") $nuova_cond .= "var#%?".$condizione[4];
else $nuova_cond .= "txt#%?".$condizione[4];
$nuova_cond .= "#%?".$condizione[5];
} # fine if ($condizione[0] == "oper" or $condizione[0] == "oper2")
if ($condizione[0] == "unset") {
$nuova_cond = "rpt#@?#$?".$condizione[1]."#%?".$condizione[2]."#%?txt#%?".$condizione[3]."#@?unset#%?".$condizione[4];
} # fine if ($condizione[0] == "unset")
if ($condizione[0] == "break") {
$nuova_cond = "rpt#@?#$?".$condizione[1]."#%?".$condizione[2]."#%?txt#%?".$condizione[3]."#@?break#%?";
} # fine if ($condizione[0] == "break")
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '".aggslashdb($nuova_cond)."' where numero = '$num_cond' and tipo = '$tipo_cond' ");
} # fine for $num1
$vett_contr = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'vett%' ");
for ($num1 = 0 ; $num1 < numlin_query($vett_contr) ; $num1++) {
$num_vett = risul_query($vett_contr,$num1,'numero');
$tipo_vett = risul_query($vett_contr,$num1,'tipo');
$vett = explode(";",risul_query($vett_contr,$num1,'testo'));
$nuovo_vett = $vett[0].";".$vett[1];
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '".aggslashdb($nuovo_vett)."' where numero = '$num_vett' and tipo = '$tipo_vett' ");
if ($vett[2] == "dat" or $vett[2] == "val") {
$contr_cond = substr($tipo_vett,4);
$max_cond = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo = 'cond$contr_cond' ");
if (numlin_query($max_cond) != 0) $max_cond = (risul_query($max_cond,0,0) + 1);
else $max_cond = 1;
$nuova_cond = "ind#@?#@?array#%?a$num_vett#%?".$vett[2]."#%?".$vett[3];
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('$max_cond','cond$contr_cond','".aggslashdb($nuova_cond)."')");
} # fine if ($vett[2] == "dat" or $vett[2] == "val")
} # fine for $num1
$allegati = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo = 'allegato'");
for ($num1 = 0 ; $num1 < numlin_query($allegati) ; $num1++) {
$allegato = risul_query($allegati,$num1,'testo');
if ($allegato) {
$num_contr = risul_query($allegati,$num1,'numero');
$num_all = ($num1 + 1);
esegui_query("insert into ".$PHPR_TAB_PRE."contratti (numero,tipo,testo) values ('$num_all','file_all','".aggslashdb($allegato)."')");
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '$num_all' where numero = '$num_contr' and tipo = 'allegato'");
} # fine if ($allegato)
} # fine for $num1
$max_contr = esegui_query("select max(numero) from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'contr%'");
$max_contr = risul_query($max_contr,0,0);
$nomi_contr = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'nomi_contratti' ");
$num_nomi_contr = numlin_query($nomi_contr);
for ($num1 = 0 ; $num1 < $num_nomi_contr ; $num1++) {
$val_nc = "";
$idut_nc = risul_query($nomi_contr,$num1,'idutente');
$val_nc_vett = explode("#@&",risul_query($nomi_contr,$num1,'valpersonalizza'));
for ($num2 = 0 ; $num2 < count($val_nc_vett) ; $num2++) {
$num_nc = explode("#?&",$val_nc_vett[$num2]);
if ($num_nc[0] <= $max_contr) $val_nc .= "#@&".$val_nc_vett[$num2];
} # fine for $num2
$val_nc = substr($val_nc,3);
esegui_query("update $tablepersonalizza set valpersonalizza = '$val_nc' where idutente = '$idut_nc' and idpersonalizza = 'nomi_contratti'");
} # fine for $num1
$tablecache = $PHPR_TAB_PRE."cache";
esegui_query("create table $tablecache (numero integer, tipo varchar(8), testo $MEDIUMTEXT )");
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$priv_glob = esegui_query("select * from $tableprivilegi where anno = '1' ");
for ($num1 = 0 ; $num1 < numlin_query($priv_glob) ; $num1++) {
$id_ut = risul_query($priv_glob,$num1,'idutente');
$priv_mod_pers = risul_query($priv_glob,$num1,'priv_mod_pers');
esegui_query("update $tableprivilegi set priv_mod_pers = '$priv_mod_pers"."n' where anno = '1' and idutente = '$id_ut' ");
} # fine for $num1
} # fine if ($versione_corrente < "1.2")

if ($versione_corrente < "1.21") {
$aggiornato = "SI";
/*$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_prenota = '".$priv_mod_prec."ss' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1*/
$tablenazioni = $PHPR_TAB_PRE."nazioni";
esegui_query("alter table $tablenazioni add column codice2_nazione varchar(50) ");
esegui_query("alter table $tablenazioni add column codice3_nazione varchar(50) ");
$tableregioni = $PHPR_TAB_PRE."regioni";
esegui_query("alter table $tableregioni add column codice2_regione varchar(50) ");
esegui_query("alter table $tableregioni add column codice3_regione varchar(50) ");
$tablecitta = $PHPR_TAB_PRE."citta";
esegui_query("alter table $tablecitta add column codice2_citta varchar(50) ");
esegui_query("alter table $tablecitta add column codice3_citta varchar(50) ");
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
esegui_query("alter table $tabledocumentiid add column codice2_documentoid varchar(50) ");
esegui_query("alter table $tabledocumentiid add column codice3_documentoid varchar(50) ");
$tableparentele = $PHPR_TAB_PRE."parentele";
esegui_query("alter table $tableparentele add column codice2_parentela varchar(50) ");
esegui_query("alter table $tableparentele add column codice3_parentela varchar(50) ");
$tabledescrizioni = $PHPR_TAB_PRE."descrizioni";
esegui_query("drop table $tabledescrizioni ");
esegui_query("create table $tabledescrizioni (nome text not null, tipo varchar(16), numero integer, testo $MEDIUMTEXT )");
} # fine if ($versione_corrente < "1.21")

if ($versione_corrente < "1.22") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "1.22")

if ($versione_corrente < "1.3") {
$aggiornato = "SI";
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv = risul_query($privilegi_anni,$num1,'anno');
$idutente_priv = risul_query($privilegi_anni,$num1,'idutente');
$priv_vedi_tab_prec = risul_query($privilegi_anni,$num1,'priv_vedi_tab');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_vedi_tab = '".$priv_vedi_tab_prec."n' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
$priv_ca_cons_prec = risul_query($privilegi_anni,$num1,'costi_agg_consentiti');
if (strlen($priv_ca_cons_prec) > 2) {
$priv_ca_cons_prec = explode(",",$priv_ca_cons_prec);
$priv_ca_cons = "s";
for ($num2 = 1 ; $num2 < count($priv_ca_cons_prec) ; $num2++) {
$priv_ca_cons .= ",".($priv_ca_cons_prec[$num2] + 6);
} # fine for $num2
esegui_query("update ".$PHPR_TAB_PRE."privilegi set costi_agg_consentiti = '$priv_ca_cons' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine if (strlen($priv_ca_cons_prec) > 2)
} # fine for $num1
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('rig_tab_tutte_prenota','$idutente_mostra','to#@&ta#@&ca')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_doc_salvati','$idutente_mostra','100')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('arrotond_tasse','$idutente_mostra','0.01')");
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tableprenota_temp = $PHPR_TAB_PRE."pren".$anno_mostra;
esegui_query("alter table $tableprenota add column id_anni_prec text ");
esegui_query("alter table $tableprenota add column tasseperc float4 ");
esegui_query("alter table $tableprenota add column commissioni float8 ");
esegui_query("alter table $tableprenota rename to $tableprenota_temp ");
esegui_query("create table $tableprenota (idprenota integer primary key, idclienti integer, idappartamenti varchar(100), iddatainizio integer, iddatafine integer, assegnazioneapp varchar(4), app_assegnabili text, num_persone integer, idprenota_compagna text, tariffa text, tariffesettimanali text, incompatibilita text, sconto float8, tariffa_tot float8, caparra float8, commissioni float8, tasseperc float4, pagato float8, metodo_pagamento text, codice varchar(10), origine varchar(70), commento text, conferma varchar(4), checkin $DATETIME, checkout $DATETIME, id_anni_prec text, datainserimento $DATETIME, hostinserimento varchar(50), data_modifica $DATETIME, utente_inserimento integer) ");
esegui_query("insert into $tableprenota select idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,app_assegnabili,num_persone,idprenota_compagna,tariffa,tariffesettimanali,incompatibilita,sconto,tariffa_tot,caparra,commissioni,tasseperc,pagato,metodo_pagamento,codice,origine,commento,conferma,checkin,checkout,id_anni_prec,datainserimento,hostinserimento,data_modifica,utente_inserimento from $tableprenota_temp ");
esegui_query("drop table $tableprenota_temp ");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$tablenometariffe_temp = $PHPR_TAB_PRE."ntari".$anno_mostra;
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1");
$num_tariffe_tab = risul_query($rigatariffe,0,'nomecostoagg');
$num_costi_agg_v = risul_query($rigatariffe,0,'numlimite_ca');
$query = "create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, arrotonda_ca float4, tasseperc_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca varchar(1), periodipermessi_ca text, beniinv_ca text, appincompatibili_ca text, variazione_ca varchar(20), mostra_ca varchar(10), categoria_ca text, letto_ca varchar(1), numlimite_ca integer, regoleassegna_ca varchar(30), utente_inserimento integer";
$colonne = "idntariffe,nomecostoagg,tipo_ca,valore_ca,arrotonda_ca,tasseperc_ca,associasett_ca,numsett_ca,moltiplica_ca,periodipermessi_ca,beniinv_ca,appincompatibili_ca,variazione_ca,mostra_ca,categoria_ca,letto_ca,numlimite_ca,regoleassegna_ca,utente_inserimento";
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$query .= ", tariffa$num2 varchar(40)";
$colonne .= ",tariffa$num2";
} # fine for $num2
esegui_query("alter table $tablenometariffe add column tasseperc_ca float4 ");
esegui_query("alter table $tablenometariffe add column categoria_ca text ");
esegui_query("alter table $tablenometariffe rename to $tablenometariffe_temp ");
esegui_query($query.")");
esegui_query("insert into $tablenometariffe select $colonne from $tablenometariffe_temp ");
esegui_query("drop table $tablenometariffe_temp ");
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
$tablecostiprenota_temp = $PHPR_TAB_PRE."costipren".$anno_mostra;
esegui_query("alter table $tablecostiprenota add column tasseperc float4 ");
esegui_query("alter table $tablecostiprenota rename to $tablecostiprenota_temp ");
esegui_query("create table $tablecostiprenota (idcostiprenota integer primary key, idprenota integer, tipo varchar(2), nome varchar(40), valore float8, arrotonda float4, tasseperc float4, associasett varchar(1), settimane text, moltiplica text, letto varchar(1), numlimite integer, idntariffe integer, variazione varchar(10), varmoltiplica varchar(1), varnumsett varchar(20), varperiodipermessi text, varbeniinv text, varappincompatibili text, vartariffeassociate varchar(10), vartariffeincomp text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer)");
esegui_query("insert into $tablecostiprenota select idcostiprenota,idprenota,tipo,nome,valore,arrotonda,tasseperc,associasett,settimane,moltiplica,letto,numlimite,idntariffe,variazione,varmoltiplica,varnumsett,varperiodipermessi,varbeniinv,varappincompatibili,vartariffeassociate,vartariffeincomp,datainserimento,hostinserimento,utente_inserimento from $tablecostiprenota_temp ");
esegui_query("drop table $tablecostiprenota_temp ");
for ($num2 = 5 ; $num2 < $num_costi_agg_v ; $num2++) {
esegui_query("update $tablenometariffe set idntariffe = '".($num2 + $num_costi_agg_v + 11)."' where idntariffe = '$num2'");
esegui_query("update $tablecostiprenota set idntariffe = '".($num2 + $num_costi_agg_v + 11)."' where idntariffe = '$num2'");
} # fine for $num2
for ($num2 = 5 ; $num2 < $num_costi_agg_v ; $num2++) {
esegui_query("update $tablenometariffe set idntariffe = '".($num2 + 6)."' where idntariffe = '".($num2 + $num_costi_agg_v + 11)."'");
esegui_query("update $tablecostiprenota set idntariffe = '".($num2 + 6)."' where idntariffe = '".($num2 + $num_costi_agg_v + 11)."'");
} # fine for $num2
esegui_query("update $tablenometariffe set numlimite_ca = '".($num_costi_agg_v + 6)."' where idntariffe = '1'");
esegui_query("insert into $tablenometariffe (idntariffe) values ('5')");
$tableregole = $PHPR_TAB_PRE."regole".$anno_mostra;
esegui_query("alter table $tableregole add column tariffa_commissioni integer");
} # fine for $num1
$tabledescrizioni = $PHPR_TAB_PRE."descrizioni";
$tabledescrizioni_temp = $PHPR_TAB_PRE."descri";
esegui_query("alter table $tabledescrizioni add column lingua varchar(3) ");
esegui_query("alter table $tabledescrizioni rename to $tabledescrizioni_temp ");
esegui_query("create table $tabledescrizioni (nome text not null, tipo varchar(16), lingua varchar(3), numero integer, testo $MEDIUMTEXT )");
esegui_query("insert into $tabledescrizioni select nome,tipo,lingua,numero,testo from $tabledescrizioni_temp ");
esegui_query("drop table $tabledescrizioni_temp ");
esegui_query("update $tabledescrizioni set lingua = '$lingua_mex' where tipo = 'tardescr' or tipo = 'appdescr' ");
if (!function_exists('aggiorna_var_modelli')) {
function aggiorna_var_modelli ($nome_file,$percorso_cartella_modello,$lingua_modello,$anno_modello,$PHPR_TAB_PRE,$tipo_periodi,$pag) {
global $costi_aggiuntivi_mostra,$nomi_costi_agg_imposti,$costo_aggiungi_letti;
if ($costo_aggiungi_letti) $costo_aggiungi_letti = $costo_aggiungi_letti + 6;
if (@is_file("./dati/selectperiodi$anno_modello.1.php")) {
$tablenometariffe_modello = $PHPR_TAB_PRE."ntariffe".$anno_modello;
$rigatariffe = esegui_query("select * from $tablenometariffe_modello where idntariffe = 1 ");
$num_costi = risul_query($rigatariffe,0,'numlimite_ca');
$SI = mex("SI",$pag);
for ($num1 = $num_costi ; $num1 > 4 ; $num1--) {
$attiva_costo = "attiva_costo".($num1 + 6);
$nome_costo_imposto = "nome_costo_imposto".($num1 + 6);
global $$attiva_costo,$$nome_costo_imposto;
$$attiva_costo = "";
if (strtoupper($costi_aggiuntivi_mostra[$num1]) == $SI) $$attiva_costo = "SI";
$$nome_costo_imposto = $nomi_costi_agg_imposti[$num1];
} # fine for $num1
} # fine if (@is_file("./dati/selectperiodi$anno_modello.1.php"))
} # fine function aggiorna_var_modelli
} # fine if (!function_exists('aggiorna_var_modelli'))
} # fine if ($versione_corrente < "1.3")

if ($versione_corrente < "1.31") {
$aggiornato = "SI";
$tableclienti = $PHPR_TAB_PRE."clienti";
esegui_query("alter table $tableclienti add column lingua varchar(14) ");
$tablecache = $PHPR_TAB_PRE."cache";
esegui_query("alter table $tablecache add column data_modifica $DATETIME ");
esegui_query("alter table $tablecache add column datainserimento $DATETIME ");
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
$rig_tab_tutte_prenota = esegui_query("select * from ".$PHPR_TAB_PRE."personalizza where idpersonalizza = 'rig_tab_tutte_prenota' and idutente = '$idutente_mostra' ");
$rig_tab_tutte_prenota = risul_query($rig_tab_tutte_prenota,0,'valpersonalizza');
if ($rig_tab_tutte_prenota) $rig_tab_tutte_prenota .= "#@&pc";
else $rig_tab_tutte_prenota = "pc";
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = '$rig_tab_tutte_prenota' where idpersonalizza = 'rig_tab_tutte_prenota' and idutente = '$idutente_mostra' ");
} # fine for $num1
} # fine if ($versione_corrente < "1.31")

if ($versione_corrente < "1.32") {
$aggiornato = "SI";
$fileaperto = fopen("./dati/unit.php","w+");
fwrite($fileaperto,"<?php
\$unit['s_n'] = \$trad_var['apartment'];
\$unit['p_n'] = \$trad_var['apartments'];
\$unit['gender'] = \$trad_var['apartment_gender'];
\$unit['special'] = 0;
\$car_spec = explode(\",\",\$trad_var['special_characters']);
for (\$num1 = 0 ; \$num1 < count(\$car_spec) ; \$num1++) if (substr(\$unit['p_n'],0,strlen(\$car_spec[\$num1])) == \$car_spec[\$num1]) \$unit['special'] = 1;
?>");
fclose($fileaperto);
$tableutenti = $PHPR_TAB_PRE."utenti";
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("alter table $tableutenti add column datainserimento $DATETIME ");
esegui_query("alter table $tableutenti add column hostinserimento varchar(50) ");
esegui_query("update $tableutenti set datainserimento = '$datainserimento', hostinserimento = '".aggslashdb($HOSTNAME)."' where idutenti != '1' ");
$tablecasse = $PHPR_TAB_PRE."casse";
esegui_query("create table $tablecasse (idcasse integer primary key, nome_cassa varchar(70), stato varchar(8), codice_cassa varchar(50), descrizione_cassa text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer) ");
esegui_query("insert into $tablecasse (idcasse,datainserimento,hostinserimento,utente_inserimento) values ('1','".date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)))."','$HOSTNAME','1')");
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
esegui_query("alter table $tableprivilegi add column casse_consentite text ");
esegui_query("update $tableprivilegi set casse_consentite = 's,1' where anno = '1' ");
esegui_query("alter table $tableprivilegi add column cassa_pagamenti varchar(70) ");
$priv_glob = esegui_query("select * from $tableprivilegi where anno = '1' ");
for ($num1 = 0 ; $num1 < numlin_query($priv_glob) ; $num1++) {
$id_ut = risul_query($priv_glob,$num1,'idutente');
$priv_mod_pers = risul_query($priv_glob,$num1,'priv_mod_pers');
esegui_query("update $tableprivilegi set priv_mod_pers = '$priv_mod_pers"."n' where anno = '1' and idutente = '$id_ut' ");
$priv_ins_clienti = risul_query($priv_glob,$num1,'priv_ins_clienti');
if (strlen($priv_ins_clienti) == 3) esegui_query("update $tableprivilegi set priv_ins_clienti = '$priv_ins_clienti"."ss' where anno = '1' and idutente = '$id_ut' ");
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablecosti = $PHPR_TAB_PRE."costi".$anno_mostra;
esegui_query("alter table $tablecosti add column nome_cassa varchar(70) ");
esegui_query("alter table $tablecosti add column metodo_pagamento text ");
} # fine for $num1
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_casse','$idutente_mostra','50')");
} # fine for $num1
global $larghezza_finestra_foto,$altezza_finestra_foto,$scelta_app,$costo_scelta_appartamento,$sett_no_scelta_app,$scelta_app_in_regola1,$email_regola1,$utente_mess,$apri_nuova_finestra_da_frame,$altezza_finestra_da_frame,$larghezza_finestra_da_frame,$num_motivazioni,$mostra_bottone_conferma;
$larghezza_finestra_foto = "760";
$altezza_finestra_foto = "550";
$scelta_app = "NO";
$costo_scelta_appartamento = "";
$sett_no_scelta_app = "4";
$scelta_app_in_regola1 = "NO";
$email_regola1 = "";
$utente_mess = "";
$apri_nuova_finestra_da_frame = "NO";
$altezza_finestra_da_frame = "620";
$larghezza_finestra_da_frame = "700";
$num_motivazioni = 0;
$mostra_bottone_conferma = "NO";
} # fine if ($versione_corrente < "1.32")

if ($versione_corrente < "2.00") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno_mostra;
$tableperiodi_temp = $PHPR_TAB_PRE."perio".$anno_mostra;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe < 6 order by idntariffe ");
$num_tariffe_tab = risul_query($rigatariffe,0,'nomecostoagg');
$query = "create table $tableperiodi (idperiodi integer primary key, datainizio date not null, datafine date";
$colonne = "idperiodi,datainizio,datafine";
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$query .= ", tariffa$num2 float8, tariffa$num2"."p float8";
$colonne .= ",tariffa$num2,tariffa$num2"."p";
esegui_query("alter table $tableperiodi add column tariffa$num2"."p float8 ");
} # fine for $num2
esegui_query("alter table $tableperiodi rename to $tableperiodi_temp ");
esegui_query($query.")");
esegui_query("insert into $tableperiodi select $colonne from $tableperiodi_temp ");
esegui_query("drop table $tableperiodi_temp ");
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$molt_tariffa = risul_query($rigatariffe,3,'tariffa'.$num2);
if ($molt_tariffa == "p") {
$prezzi = esegui_query("select idperiodi,tariffa$num2 from $tableperiodi order by idperiodi ");
for ($num3 = 0 ; $num3 < numlin_query($prezzi) ; $num3++) {
$prezzo = risul_query($prezzi,$num3,'tariffa'.$num2);
if (strcmp($prezzo,"")) {
$idper = risul_query($prezzi,$num3,'idperiodi');
esegui_query("update $tableperiodi set tariffa$num2 = NULL, tariffa$num2"."p = '$prezzo' where idperiodi = '$idper' ");
} # fine if (strcmp($prezzo,""))
} # fine for $num3
} # fine if ($molt_tariffa == "p")
} # fine for $num2
$tableprenota = $PHPR_TAB_PRE."prenota".$anno_mostra;
$prenota_tariffe_pers = esegui_query("select * from $tableprenota where tariffa $LIKE '%#@&p' ");
for ($num2 = 0 ; $num2 < numlin_query($prenota_tariffe_pers) ; $num2++) {
$idprenota = risul_query($prenota_tariffe_pers,$num2,'idprenota');
$tariffesett = risul_query($prenota_tariffe_pers,$num2,'tariffesettimanali');
esegui_query("update $tableprenota set tariffesettimanali = '$tariffesett;$tariffesett' where idprenota = '$idprenota' ");
} # fine for $num2
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$tablenometariffe_temp = $PHPR_TAB_PRE."ntari".$anno_mostra;
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1");
$num_tariffe_tab = risul_query($rigatariffe,0,'nomecostoagg');
$num_costi_agg_v = risul_query($rigatariffe,0,'numlimite_ca');
$query = "create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, valore_perc_ca float8, arrotonda_ca float4, tasseperc_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca text, periodipermessi_ca text, beniinv_ca text, appincompatibili_ca text, variazione_ca varchar(20), mostra_ca varchar(10), categoria_ca text, letto_ca varchar(1), numlimite_ca integer, regoleassegna_ca varchar(30), utente_inserimento integer";
$colonne = "idntariffe,nomecostoagg,tipo_ca,valore_ca,valore_perc_ca,arrotonda_ca,tasseperc_ca,associasett_ca,numsett_ca,moltiplica_ca,periodipermessi_ca,beniinv_ca,appincompatibili_ca,variazione_ca,mostra_ca,categoria_ca,letto_ca,numlimite_ca,regoleassegna_ca,utente_inserimento";
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$query .= ", tariffa$num2 varchar(40)";
$colonne .= ",tariffa$num2";
} # fine for $num2
esegui_query("alter table $tablenometariffe add column valore_perc_ca float8 ");
esegui_query("alter table $tablenometariffe rename to $tablenometariffe_temp ");
esegui_query($query.")");
esegui_query("insert into $tablenometariffe select $colonne from $tablenometariffe_temp ");
esegui_query("drop table $tablenometariffe_temp ");
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
$tablecostiprenota_temp = $PHPR_TAB_PRE."costipren".$anno_mostra;
esegui_query("alter table $tablecostiprenota add column valore_perc float8 ");
esegui_query("alter table $tablecostiprenota add column categoria text ");
esegui_query("alter table $tablecostiprenota rename to $tablecostiprenota_temp ");
esegui_query("create table $tablecostiprenota (idcostiprenota integer primary key, idprenota integer, tipo varchar(2), nome varchar(40), valore float8, valore_perc float8, arrotonda float4, tasseperc float4, associasett varchar(1), settimane text, moltiplica text, categoria text, letto varchar(1), numlimite integer, idntariffe integer, variazione varchar(10), varmoltiplica text, varnumsett varchar(20), varperiodipermessi text, varbeniinv text, varappincompatibili text, vartariffeassociate varchar(10), vartariffeincomp text, datainserimento $DATETIME, hostinserimento varchar(50), utente_inserimento integer) ");
esegui_query("insert into $tablecostiprenota select idcostiprenota,idprenota,tipo,nome,valore,valore_perc,arrotonda,tasseperc,associasett,settimane,moltiplica,categoria,letto,numlimite,idntariffe,variazione,varmoltiplica,varnumsett,varperiodipermessi,varbeniinv,varappincompatibili,vartariffeassociate,vartariffeincomp,datainserimento,hostinserimento,utente_inserimento from $tablecostiprenota_temp ");
esegui_query("drop table $tablecostiprenota_temp ");
$costi_agg = esegui_query("select * from $tablenometariffe where idntariffe > 10 ");
for ($num2 = 0 ; $num2 < numlin_query($costi_agg) ; $num2++) {
$idntariffe = risul_query($costi_agg,$num2,'idntariffe');
$tipo = risul_query($costi_agg,$num2,'tipo_ca');
$moltiplica_ca = risul_query($costi_agg,$num2,'moltiplica_ca');
esegui_query("update $tablenometariffe set moltiplica_ca = '$moltiplica_ca"."x0,' where idntariffe = '$idntariffe' ");
$mostra_ca = risul_query($costi_agg,$num2,'mostra_ca');
esegui_query("update $tablenometariffe set mostra_ca = '$mostra_ca"."n' where idntariffe = '$idntariffe' ");
$variazione_ca = risul_query($costi_agg,$num2,'variazione_ca');
esegui_query("update $tablenometariffe set variazione_ca = '$variazione_ca"."n' where idntariffe = '$idntariffe' ");
if (substr($tipo,1,1) != "f") {
$valore_ca = risul_query($costi_agg,$num2,'valore_ca');
esegui_query("update $tablenometariffe set valore_perc_ca = '$valore_ca', valore_ca = '0' where idntariffe = '$idntariffe' ");
} # fine if (substr($tipo,1,1) != "f")
} # fine for $num2
$costi_pren = esegui_query("select * from $tablecostiprenota where idcostiprenota > 1 ");
for ($num2 = 0 ; $num2 < numlin_query($costi_pren) ; $num2++) {
$idcostiprenota = risul_query($costi_pren,$num2,'idcostiprenota');
$tipo = risul_query($costi_pren,$num2,'tipo');
$varmoltiplica = risul_query($costi_pren,$num2,'varmoltiplica');
if ($varmoltiplica) esegui_query("update $tablecostiprenota set varmoltiplica = '$varmoltiplica"."x0,' where idcostiprenota = '$idcostiprenota' ");
if (substr($tipo,1,1) != "f") {
$valore = risul_query($costi_pren,$num2,'valore');
esegui_query("update $tablecostiprenota set valore_perc = '$valore', valore = '0' where idcostiprenota = '$idcostiprenota' ");
} # fine if (substr($tipo,1,1) != "f")
esegui_query("update $tablecostiprenota set variazione = 'n' where idcostiprenota = '$idcostiprenota' ");
} # fine for $num2
} # fine for $num1
$tableutenti = $PHPR_TAB_PRE."utenti";
$tableutenti_temp = $PHPR_TAB_PRE."uten";
esegui_query("alter table $tableutenti add column salt text ");
esegui_query("alter table $tableutenti rename to $tableutenti_temp ");
esegui_query("create table $tableutenti (idutenti integer primary key, nome_utente text, password text, salt text, tipo_pass varchar(1), datainserimento $DATETIME, hostinserimento varchar(50) )");
esegui_query("insert into $tableutenti select idutenti,nome_utente,password,salt,tipo_pass,datainserimento,hostinserimento from $tableutenti_temp ");
esegui_query("drop table $tableutenti_temp ");
$utenti = esegui_query("select * from $tableutenti order by idutenti");
srand((double) microtime() * 1000000);
$valori = "=?#@%abcdefghijkmnpqrstuvwxzABCDEFGHJKLMNPQRSTUVWXZ1234567890";
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$tipo_pass = risul_query($utenti,$num1,'tipo_pass');
if ($tipo_pass != "n") {
$id_ut = risul_query($utenti,$num1,'idutenti');
$pass = risul_query($utenti,$num1,'password');
$salt = "";
if ($tipo_pass == "t") {
$salt = substr($valori,rand(0,4),1);
for ($num2 = 0 ; $num2 < 19 ; $num2++) $salt .= substr($valori,rand(0,60),1);
$pass = md5($pass.$salt);
} # fine if ($tipo_pass == "t")
for ($num2 = 1 ; $num2 < 15 ; $num2++) $pass = md5($pass.substr($salt,0,(20 - $num2)));
esegui_query("update $tableutenti set password = '$pass', salt = '$salt', tipo_pass = '5' where idutenti = '$id_ut' ");
} # fine if ($tipo_pass != "n")
} # fine for $num1
$utenti = esegui_query("select * from $tableutenti order by idutenti");
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$priv_glob = esegui_query("select * from $tableprivilegi where anno = '1' ");
for ($num1 = 0 ; $num1 < numlin_query($priv_glob) ; $num1++) {
$id_ut = risul_query($priv_glob,$num1,'idutente');
$priv_ins_clienti = risul_query($priv_glob,$num1,'priv_ins_clienti');
if (strlen($priv_ins_clienti) == 3) esegui_query("update $tableprivilegi set priv_ins_clienti = '$priv_ins_clienti"."ss' where anno = '1' and idutente = '$id_ut' ");
} # fine for $num1
$sel_ins_prezzi = esegui_query("select * from ".$PHPR_TAB_PRE."personalizza where idpersonalizza = 'ultime_sel_ins_prezzi' ");
for ($num1 = 0 ; $num1 < numlin_query($sel_ins_prezzi) ; $num1++) {
$idutente_pers = risul_query($sel_ins_prezzi,$num1,'idutente');
$ultime_sel_ins_prezzi = risul_query($sel_ins_prezzi,$num1,'valpersonalizza');
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = '8,$ultime_sel_ins_prezzi' where idpersonalizza = 'ultime_sel_ins_prezzi' and idutente = '$idutente_pers' ");
} # fine for $num1
$bcc = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo = 'bcc'");
for ($num1 = 0 ; $num1 < numlin_query($bcc) ; $num1++) {
$num_contr = risul_query($bcc,$num1,'numero');
$opz_eml = risul_query($bcc,$num1,'testo');
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = ';$opz_eml', tipo = 'opzeml' where numero = '$num_contr' and tipo = 'bcc'");
} # fine for $num1
} # fine if ($versione_corrente < "2.00")

if ($versione_corrente < "2.01") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "2.01")

if ($versione_corrente < "2.02") {
$aggiornato = "SI";
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv = risul_query($privilegi_anni,$num1,'anno');
$idutente_priv = risul_query($privilegi_anni,$num1,'idutente');
$priv_vedi_tab_prec = risul_query($privilegi_anni,$num1,'priv_vedi_tab');
if (strlen($priv_vedi_tab_prec) == 8) esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_vedi_tab = '".$priv_vedi_tab_prec."o' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
} # fine if ($versione_corrente < "2.02")

if ($versione_corrente < "2.03") {
$aggiornato = "SI";
esegui_query("delete from ".$PHPR_TAB_PRE."personalizza where idpersonalizza = 'tipi_documento' ");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
esegui_query("insert into $tablenometariffe (idntariffe) values ('6')");
} # fine for $num1
} # fine if ($versione_corrente < "2.03")

if ($versione_corrente < "2.10") {
$aggiornato = "SI";
if ($PHPR_DB_TYPE == "mysql" and function_exists('mysqli_connect')) {
$filelock = crea_lock_file(C_DATI_PATH."/dati_connessione.php");
$file_dati_conn = implode("",@file(C_DATI_PATH."/dati_connessione.php"));
if ($file_dati_conn) {
$file_dati_conn = str_replace("PHPR_DB_TYPE = \"mysql\";","PHPR_DB_TYPE = \"mysqli\";",$file_dati_conn);
scrivi_file($file_dati_conn,C_DATI_PATH."/dati_connessione.php");
} # fine if ($file_dati_conn)
distruggi_lock_file($filelock,C_DATI_PATH."/dati_connessione.php");
} # fine if ($PHPR_DB_TYPE == "mysql" and function_exists('mysqli_connect'))
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
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableappartamenti_temp = $PHPR_TAB_PRE."appart";
esegui_query("alter table $tableappartamenti add column priorita2 integer ");
esegui_query("alter table $tableappartamenti add column letto varchar(1) ");
esegui_query("alter table $tableappartamenti rename to $tableappartamenti_temp ");
esegui_query("create table $tableappartamenti (idappartamenti varchar(100) primary key, numpiano text, maxoccupanti integer, numcasa text, app_vicini text, priorita integer, priorita2 integer, letto varchar(1), commento text )");
esegui_query("insert into $tableappartamenti select idappartamenti,numpiano,maxoccupanti,numcasa,app_vicini,priorita,priorita2,letto,commento from $tableappartamenti_temp ");
esegui_query("drop table $tableappartamenti_temp ");
$tablesessioni = $PHPR_TAB_PRE."sessioni";
$tablesessioni_temp = $PHPR_TAB_PRE."sessi";
esegui_query("alter table $tablesessioni add column tipo_conn varchar(12) ");
esegui_query("alter table $tablesessioni rename to $tablesessioni_temp ");
esegui_query("create table $tablesessioni (idsessioni varchar(30) primary key, idutente integer, indirizzo_ip text, tipo_conn varchar(12), user_agent text, ultimo_accesso $DATETIME)");
esegui_query("insert into $tablesessioni select idsessioni,idutente,indirizzo_ip,tipo_conn,user_agent,ultimo_accesso from $tablesessioni_temp ");
esegui_query("drop table $tablesessioni_temp ");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableregole = $PHPR_TAB_PRE."regole".$anno_mostra;
$tableregole_temp = $PHPR_TAB_PRE.'reg'.$anno_mostra;
esegui_query("alter table $tableregole add column tariffa_chiusa text ");
esegui_query("alter table $tableregole rename to $tableregole_temp ");
esegui_query("create table $tableregole (idregole integer, app_agenzia varchar(100), tariffa_chiusa text, tariffa_per_app text, tariffa_per_utente text, tariffa_per_persone text, tariffa_commissioni integer, iddatainizio integer, iddatafine integer, motivazione text, motivazione2 text )");
esegui_query("insert into $tableregole select idregole,app_agenzia,tariffa_chiusa,tariffa_per_app,tariffa_per_utente,tariffa_per_persone,tariffa_commissioni,iddatainizio,iddatafine,motivazione,motivazione2 from $tableregole_temp ");
esegui_query("drop table $tableregole_temp ");
} # fine for $num1
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_messaggi','$idutente_mostra','80')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('colori_tab_mesi','$idutente_mostra','#99CCD4,#FFEE22,#FF9900,#CC0000')");
} # fine for $num1
esegui_query("update ".$PHPR_TAB_PRE."relclienti set testo5 = NULL where tipo = 'cc' ");
} # fine if ($versione_corrente < "2.10")

if ($versione_corrente < "2.11") {
$aggiornato = "SI";
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('gest_cvc','1','NO')");
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('tot_giornalero_tab_casse','$idutente_mostra','NO')");
} # fine for $num1
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv = risul_query($privilegi_anni,$num1,'anno');
$idutente_priv = risul_query($privilegi_anni,$num1,'idutente');
$priv_contr_cons_prec = risul_query($privilegi_anni,$num1,'contratti_consentiti');
$priv_contr_cons = substr($priv_contr_cons_prec,0,1)."s".substr($priv_contr_cons_prec,1);
esegui_query("update ".$PHPR_TAB_PRE."privilegi set contratti_consentiti = '$priv_contr_cons' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
} # fine if ($versione_corrente < "2.11")

if ($versione_corrente < "2.12") {
$aggiornato = "SI";
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = 'tab' where idpersonalizza = 'tot_giornalero_tab_casse' and valpersonalizza = 'NO' ");
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = 'gior,mens,tab' where idpersonalizza = 'tot_giornalero_tab_casse' and valpersonalizza = 'SI' ");
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$costi_agg = esegui_query("select * from $tablenometariffe where idntariffe > 10 ");
for ($num2 = 0 ; $num2 < numlin_query($costi_agg) ; $num2++) {
$idntariffe = risul_query($costi_agg,$num2,'idntariffe');
$mostra_ca = risul_query($costi_agg,$num2,'mostra_ca');
esegui_query("update $tablenometariffe set mostra_ca = '$mostra_ca"."n' where idntariffe = '$idntariffe' ");
} # fine for $num2
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
$costi_pren = esegui_query("select * from $tablecostiprenota where idcostiprenota > 1 ");
for ($num2 = 0 ; $num2 < numlin_query($costi_pren) ; $num2++) {
$idcostiprenota = risul_query($costi_pren,$num2,'idcostiprenota');
$variazione = risul_query($costi_pren,$num2,'variazione');
esegui_query("update $tablecostiprenota set variazione = '$variazione"."n' where idcostiprenota = '$idcostiprenota' ");
} # fine for $num2
} # fine for $num1
global $spostamento_orizzontale_calendario;
$spostamento_orizzontale_calendario = 2;
} # fine if ($versione_corrente < "2.12")

if ($versione_corrente < "2.13") {
$aggiornato = "SI";
$priv_glob = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno = '1' ");
for ($num1 = 0 ; $num1 < numlin_query($priv_glob) ; $num1++) {
$id_ut = risul_query($priv_glob,$num1,'idutente');
$priv_mod_pers = risul_query($priv_glob,$num1,'priv_mod_pers');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_pers = '$priv_mod_pers"."n' where anno = '1' and idutente = '$id_ut' ");
} # fine for $num1
$privilegi_anni = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno != '1' ");
for ($num1 = 0 ; $num1 < numlin_query($privilegi_anni) ; $num1++) {
$anno_priv =  risul_query($privilegi_anni,$num1,'anno');
$idutente_priv =  risul_query($privilegi_anni,$num1,'idutente');
$priv_ins_prec = risul_query($privilegi_anni,$num1,'priv_ins_prenota');
$priv_ins_comm_prec = substr($priv_ins_prec,6,1);
$priv_mod_prec = risul_query($privilegi_anni,$num1,'priv_mod_prenota');
$priv_mod_comm_prec = substr($priv_mod_prec,5,1);
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_ins_prenota = '".$priv_ins_prec.$priv_ins_comm_prec."', priv_mod_prenota = '".$priv_mod_prec."s".$priv_mod_comm_prec."' where idutente = '$idutente_priv' and anno = '$anno_priv' ");
} # fine for $num1
for ($num1 = 0 ; $num1 < numlin_query($utenti) ; $num1++) {
$idutente_mostra = risul_query($utenti,$num1,'idutenti');
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('ordine_inventario','$idutente_mostra','ins')");
esegui_query("insert into ".$PHPR_TAB_PRE."personalizza (idpersonalizza,idutente,valpersonalizza) values ('tasti_pos','$idutente_mostra','x2;x10;s;+1;+2;+3;+4;+5;+6;+7;+8;+9;s;-1')");
} # fine for $num1
} # fine if ($versione_corrente < "2.13")

if ($versione_corrente < "2.14") {
$aggiornato = "SI";
} # fine if ($versione_corrente < "2.14")

if ($versione_corrente < "2.20") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableregole = $PHPR_TAB_PRE."regole".$anno_mostra;
esegui_query("alter table $tableregole add column motivazione3 text");
} # fine for $num1
$filelock = crea_lock_file(C_DATI_PATH."/tema.php");
$file_tema = implode("",@file(C_DATI_PATH."/tema.php"));
$file_tema = str_replace("\"sim\";","\"blu\";",$file_tema);
scrivi_file($file_tema,C_DATI_PATH."/tema.php");
distruggi_lock_file($filelock,C_DATI_PATH."/tema.php");
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza = '#70C6D4,#FFD800,#FF9900,#FF3115' where idpersonalizza = 'colori_tab_mesi' and (valpersonalizza = '#99CCD4,#FFEE22,#FF9900,#CC0000' or valpersonalizza = '#70C6D4,#FFEA00,#FF9900,#FF3115') ");
} # fine if ($versione_corrente < "2.20")

if ($versione_corrente < "2.21") {
$aggiornato = "SI";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno_mostra;
$tablenometariffe_temp = $PHPR_TAB_PRE."ntari".$anno_mostra;
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1");
$num_tariffe_tab = risul_query($rigatariffe,0,'nomecostoagg');
$num_costi_agg_v = risul_query($rigatariffe,0,'numlimite_ca');
$query = "create table $tablenometariffe (idntariffe integer, nomecostoagg varchar(40), tipo_ca varchar(2), valore_ca float8, valore_perc_ca float8, arrotonda_ca float4, tasseperc_ca float4, associasett_ca varchar(1), numsett_ca varchar(20), moltiplica_ca text, periodipermessi_ca text, beniinv_ca text, appincompatibili_ca text, variazione_ca varchar(20), mostra_ca varchar(10), categoria_ca text, letto_ca varchar(1), numlimite_ca integer, regoleassegna_ca varchar(30), utente_inserimento integer";
$colonne = "idntariffe,nomecostoagg,tipo_ca,valore_ca,valore_perc_ca,arrotonda_ca,tasseperc_ca,associasett_ca,numsett_ca,moltiplica_ca,periodipermessi_ca,beniinv_ca,appincompatibili_ca,variazione_ca,mostra_ca,categoria_ca,letto_ca,numlimite_ca,regoleassegna_ca,utente_inserimento";
for ($num2 = 1 ; $num2 <= $num_tariffe_tab ; $num2++) {
$query .= ", tariffa$num2 text";
$colonne .= ",tariffa$num2";
} # fine for $num2
esegui_query("alter table $tablenometariffe rename to $tablenometariffe_temp ");
esegui_query($query.")");
esegui_query("insert into $tablenometariffe select $colonne from $tablenometariffe_temp ");
esegui_query("drop table $tablenometariffe_temp ");
} # fine for $num1
} # fine if ($versione_corrente < "2.21")

if ($versione_corrente < "2.22") {
$aggiornato = "SI";
$data_ins = date("YmdH",(time() + (C_DIFF_ORE * 3600)));
esegui_query("update ".$PHPR_TAB_PRE."personalizza set valpersonalizza_num = '$data_ins' where idpersonalizza = 'cert_cc' or idpersonalizza = 'priv_key_cc' ");
$cond_contr = esegui_query("select * from ".$PHPR_TAB_PRE."contratti where tipo $LIKE 'cond%' ");
for ($num1 = 0 ; $num1 < numlin_query($cond_contr) ; $num1++) {
$condizione = risul_query($cond_contr,$num1,'testo');
$azione = explode("#@?",$condizione);
$azione = explode("#%?",$azione[2]);
if ($azione[0] == "set") {
$num_cond = risul_query($cond_contr,$num1,'numero');
$tipo_cond = risul_query($cond_contr,$num1,'tipo');
esegui_query("update ".$PHPR_TAB_PRE."contratti set testo = '".aggslashdb($condizione."#%?")."' where numero = '$num_cond' and tipo = '$tipo_cond' ");
} # fine if ($azione[0] == "set")
} # fine for $num1
$priv_glob = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno = '1' ");
for ($num1 = 0 ; $num1 < numlin_query($priv_glob) ; $num1++) {
$id_ut = risul_query($priv_glob,$num1,'idutente');
$priv_mod_pers = risul_query($priv_glob,$num1,'priv_mod_pers');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_pers = '$priv_mod_pers"."n' where anno = '1' and idutente = '$id_ut' ");
} # fine for $num1
} # fine if ($versione_corrente < "2.22")

if ($versione_corrente < "2.23") {
$aggiornato = "SI";
$priv_glob = esegui_query("select * from ".$PHPR_TAB_PRE."privilegi where anno = '1' ");
for ($num1 = 0 ; $num1 < numlin_query($priv_glob) ; $num1++) {
$id_ut = risul_query($priv_glob,$num1,'idutente');
$priv_mod_pers = risul_query($priv_glob,$num1,'priv_mod_pers');
esegui_query("update ".$PHPR_TAB_PRE."privilegi set priv_mod_pers = '$priv_mod_pers"."n' where anno = '1' and idutente = '$id_ut' ");
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
for ($num2 = 0 ; $num2 < numlin_query($utenti) ; $num2++) {
$idutente_mostra = risul_query($utenti,$num2,'idutenti');
$file = C_DATI_PATH."/selectperiodi$anno_mostra.$idutente_mostra".".php";
if (@is_file($file)) {
$filelock = crea_lock_file($file);
$contenuto_file = implode("",@file($file));
if (!strstr($contenuto_file,"array()")) {
$contenuto_file = str_replace("\$y_ini_menu[0] =","\$y_ini_menu = array();
\$m_ini_menu = array();
\$d_ini_menu = array();
\$n_dates_menu = array();
\$d_increment = array();
\$y_ini_menu[0] =",$contenuto_file);
scrivi_file($contenuto_file,$file);
} # fine if (!strstr($contenuto_file,"array()"))
distruggi_lock_file($filelock,$file);
} # fine if (@is_file($file))
$file = C_DATI_PATH."/selperiodimenu$anno_mostra.$idutente_mostra".".php";
if (@is_file($file)) {
$filelock = crea_lock_file($file);
$contenuto_file = implode("",@file($file));
if (!strstr($contenuto_file,"array()")) {
$contenuto_file = str_replace("\$y_ini_menu[0] =","\$y_ini_menu = array();
\$m_ini_menu = array();
\$d_ini_menu = array();
\$n_dates_menu = array();
\$d_increment = array();
\$y_ini_menu[0] =",$contenuto_file);
scrivi_file($contenuto_file,$file);
} # fine if (!strstr($contenuto_file,"array()"))
distruggi_lock_file($filelock,$file);
} # fine if (@is_file($file))
} # fine for $num2
} # fine for $num1
} # fine if ($versione_corrente < "2.23")





$id_utente = $id_utente_vero;

if (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI") {
if (@is_file("./COPYING")) $file_copying = "file <a href=\"COPYING\">COPYING</a>";
else $file_copying = "<a href=\"http://www.gnu.org/licenses/agpl-3.0.html\">AGPLv3</a> License";
echo "<div style=\"text-align: center;\">
HOTELDRUID version ".C_PHPR_VERSIONE_TXT.", Copyright (C) 2001-2018 Marco M. F. De Santis<br>
HotelDruid comes with ABSOLUTELY NO WARRANTY; <br>
for details see the $file_copying.<br>
This is free software, and you are welcome to redistribute it<br>
under certain conditions; see the $file_copying for details.<br>
</div><hr style=\"width: 95%\">
<br>";
} # fine if (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI")

if ($aggiornato == "SI") {
esegui_query("update ".$PHPR_TAB_PRE."versioni set num_versione = '".C_PHPR_VERSIONE_NUM."' where idversioni = 1");
scrivi_file("<?php
define('C_VERSIONE_ATTUALE',".C_PHPR_VERSIONE_NUM.");
define('C_DIFF_ORE',".C_DIFF_ORE.");
?>",C_DATI_PATH."/versione.php");
$testo_mess = "<big><big>".mex("Database aggiornato alla versione","aggiorna.php")." <b>".C_PHPR_VERSIONE_TXT."</b>!<br></big></big><br>";
echo $testo_mess;

# ricreo i modelli internet anche con eventuali nuove frasi
$lingua_mex_orig = $lingua_mex;
include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];
if ($id_utente != 1 and function_exists("ob_start")) ob_start();
global $anno_modello_presente,$num_periodi_date,$modello_esistente,$cambia_frasi,$lingua_modello,$percorso_cartella_modello,$nome_file;
$pag_orig = $pag;
$pag = "crea_modelli.php";
include("./includes/templates/funzioni_modelli.php");
$modello_esistente = "SI";
$cambia_frasi = "NO";
include("./includes/templates/frasi_mod_disp.php");
include("./includes/templates/funzioni_mod_disp.php");
$frasi_ripristinate = 0;
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/mdl_disponibilita.php")) {
$lingua_modello = "ita";
$nome_file = mex2("mdl_disponibilita",$pag,$lingua_modello).".php";
if (!$frasi_ripristinate) {
$frasi_ripristinate = 1;
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) {
global ${$fr_frase[$num_fr]};
${$fr_frase[$num_fr]} = mex2($frase[$num_fr],$pag,$lingua_modello);
} # fine for $num_fr
} # fine if (!$frasi_ripristinate)
$num_periodi_date = "";
$anno_modello = "";
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
if (function_exists('aggiorna_var_modelli')) aggiorna_var_modelli($nome_file,$percorso_cartella_modello,$lingua_modello,$anno_modello,$PHPR_TAB_PRE,$tipo_periodi,$pag);
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if (@is_file("$percorso_cartella_modello/mdl_disponibilita.php"))
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
$frasi_ripristinate = 0;
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = $ini_lingua;
if (!$frasi_ripristinate) {
$frasi_ripristinate = 1;
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) {
global ${$fr_frase[$num_fr]};
${$fr_frase[$num_fr]} = mex2($frase[$num_fr],$pag,$lingua_modello);
} # fine for $num_fr
} # fine if (!$frasi_ripristinate)
$num_periodi_date = "";
$anno_modello = "";
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
if (function_exists('aggiorna_var_modelli')) aggiorna_var_modelli($nome_file,$percorso_cartella_modello,$lingua_modello,$anno_modello,$PHPR_TAB_PRE,$tipo_periodi,$pag);
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
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
$funz_mext = "mext_".$modello_ext;
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
$frasi_ripristinate = 0;
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = "ita";
if (!$frasi_ripristinate) {
$frasi_ripristinate = 1;
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) {
global ${$fr_frase[$num_fr]};
${$fr_frase[$num_fr]} = $funz_mext($frase[$num_fr],$pag,$lingua_modello);
} # fine for $num_fr
} # fine if (!$frasi_ripristinate)
$num_periodi_date = "";
$anno_modello = "";
$funz_recupera_var_modello($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
if (function_exists('aggiorna_var_modelli')) aggiorna_var_modelli($nome_file,$percorso_cartella_modello,$lingua_modello,$anno_modello,$PHPR_TAB_PRE,$tipo_periodi,$pag);
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else {
$n_file = $funz_mext($template_file_name['ita'],$pag,$ini_lingua);
if ($n_file and $n_file != $template_file_name['ita'] and $n_file != $template_file_name['en']) $nome_file = $n_file;
else $nome_file = $ini_lingua."_".$template_file_name['en'];
} # fine else if ($template_file_name[$ini_lingua])
$frasi_ripristinate = 0;
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = $ini_lingua;
if (!$frasi_ripristinate) {
$frasi_ripristinate = 1;
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) {
global ${$fr_frase[$num_fr]};
${$fr_frase[$num_fr]} = $funz_mext($frase[$num_fr],$pag,$lingua_modello);
} # fine for $num_fr
} # fine if (!$frasi_ripristinate)
$num_periodi_date = "";
$anno_modello = "";
$funz_recupera_var_modello($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
$anno_modello = $anno_modello_presente;
if (function_exists('aggiorna_var_modelli')) aggiorna_var_modelli($nome_file,$percorso_cartella_modello,$lingua_modello,$anno_modello,$PHPR_TAB_PRE,$tipo_periodi,$pag);
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
$pag = $pag_orig;
if ($id_utente != 1 and function_exists("ob_get_contents")) $testo_mess .= ob_get_contents();
if ($id_utente != 1 and function_exists("ob_end_clean")) ob_end_clean();
$lingua_mex = $lingua_mex_orig;
echo "<br>";


if ($id_utente != 1) {
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$tabelle_lock = array("$tablemessaggi");
$altre_tab_lock = array();
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$max_mess = esegui_query("select max(idmessaggi) from $tablemessaggi");
if (numlin_query($max_mess) != 0) $max_mess = (risul_query($max_mess,0,0) + 1);
else $max_mess = 1;
$lista_utenti = ",1,";
$datavisione = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
if (@get_magic_quotes_gpc()) $testo = stripslashes($testo);
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tablemessaggi (idmessaggi,tipo_messaggio,idutenti,idutenti_visto,datavisione,mittente,testo,datainserimento) values ('$max_mess','mess','$lista_utenti','$lista_utenti','$datavisione','1','".aggslashdb($testo_mess)."','$datainserimento')");
unlock_tabelle($tabelle_lock);
} # fine if ($id_utente != 1)

} # fine if ($aggiornato == "SI")
else echo "<big><big>".mex("Niente da aggiornare",'aggiorna.php').".<br></big></big>";


echo "<div style=\"text-align: center;\"><br><form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione_orig\">
<input type=\"hidden\" name=\"anno\" value=\"$anno_orig\">
<input type=\"hidden\" name=\"vers_hinc\" value=\"1\">
<input class=\"sbutton\" type=\"submit\" name=\"indietro\" value=\"".mex("Vai al menù principale","aggiorna.php")."\">
</div></form></div>";


} # fine if ($id_utente)

flock($file_lock_update,3);
fclose($file_lock_update);
} # fine if ($file_lock_update)
else echo mex("Non ho i permessi di scrittura sulla cartella dati",'inizio.php').".<br>";
@unlink(C_DATI_PATH."/update.lock");

} # fine function aggiorna_versione_phpr





?>