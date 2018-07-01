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

$pag = "tab_tariffe.php";
$titolo = "HotelDruid: Tariffe";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/funzioni_testo.php");
include("./includes/sett_gio.php");
$base_js = 1;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tabledescrizioni = $PHPR_TAB_PRE."descrizioni";
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_periodi = substr($priv_vedi_tab,3,1);
if ($priv_vedi_tab_periodi == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_regole = substr($priv_vedi_tab,4,1);
$priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
$priv_ins_assegnazione_app = substr($priv_ins_prenota,1,1);
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_assegnazione_app = substr($priv_mod_prenota,2,1);
$regole1_consentite = risul_query($privilegi_annuali_utente,0,'regole1_consentite');
$attiva_regole1_consentite = substr($regole1_consentite,0,1);
$applica_regole1 = substr($regole1_consentite,1,1);
if ($attiva_regole1_consentite != "n" or $applica_regole1 == "n") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
$tariffe_consentite = risul_query($privilegi_annuali_utente,0,'tariffe_consentite');
$attiva_tariffe_consentite = substr($tariffe_consentite,0,1);
if ($attiva_tariffe_consentite == "s") {
$tariffe_consentite = explode(",",substr($tariffe_consentite,2));
unset($tariffe_consentite_vett);
for ($num1 = 0 ; $num1 < count($tariffe_consentite) ; $num1++) if ($tariffe_consentite[$num1]) $tariffe_consentite_vett[$tariffe_consentite[$num1]] = "SI";
} # fine if ($attiva_tariffe_consentite == "s")
$costi_agg_consentiti = risul_query($privilegi_annuali_utente,0,'costi_agg_consentiti');
$attiva_costi_agg_consentiti = substr($costi_agg_consentiti,0,1);
if ($attiva_costi_agg_consentiti == "s") {
$costi_agg_consentiti = explode(",",substr($costi_agg_consentiti,2));
unset($costi_agg_consentiti_vett);
for ($num1 = 0 ; $num1 < count($costi_agg_consentiti) ; $num1++) if ($costi_agg_consentiti[$num1]) $costi_agg_consentiti_vett[$costi_agg_consentiti[$num1]] = "SI";
} # fine if ($attiva_costi_agg_consentiti == "s")
$priv_ins_tariffe = risul_query($privilegi_annuali_utente,0,'priv_ins_tariffe');
$priv_mod_tariffe = substr($priv_ins_tariffe,0,1);
$priv_ins_costi_agg = substr($priv_ins_tariffe,1,1);
$priv_mod_costo_agg = substr($priv_ins_tariffe,2,1);
$priv_canc_costi_agg = substr($priv_ins_tariffe,3,1);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)

unset($attiva_regole1_consentite_gr);
unset($regole1_consentite_gr);
unset($attiva_tariffe_consentite_gr);
unset($tariffe_consentite_vett_gr);
unset($priv_ins_nuove_prenota_gr);
unset($priv_ins_assegnazione_app_gr);
unset($priv_mod_prenotazioni_gr);
unset($priv_mod_assegnazione_app_gr);
unset($attiva_costi_agg_consentiti_gr);
unset($costi_agg_consentiti_vett_gr);
$priv_app_gruppi = "NO";
if ($priv_vedi_tab_periodi == "g") $priv_app_gruppi = "SI";
if ($priv_app_gruppi == "SI") {
$attiva_regole1_consentite_gr[$id_utente] = $attiva_regole1_consentite;
$regole1_consentite_gr[$id_utente] = $regole1_consentite;
$attiva_tariffe_consentite_gr[$id_utente] = $attiva_tariffe_consentite;
$tariffe_consentite_vett_gr[$id_utente] = $tariffe_consentite_vett;
$priv_ins_nuove_prenota_gr[$id_utente] = $priv_ins_nuove_prenota;
$priv_ins_assegnazione_app_gr[$id_utente] = $priv_ins_assegnazione_app;
$priv_mod_prenotazioni_gr[$id_utente] = $priv_mod_prenotazioni;
$priv_mod_assegnazione_app_gr[$id_utente] = $priv_mod_assegnazione_app;
} # fine if ($priv_app_gruppi == "SI")
unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$attiva_costi_agg_consentiti_gr = $attiva_costi_agg_consentiti;
$costi_agg_consentiti_vett_gr = $costi_agg_consentiti_vett;
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) {
$idutente_gruppo = risul_query($utenti_gruppo,$num2,'idutente');
if ($idutente_gruppo != $id_utente and !$utenti_gruppi[$idutente_gruppo]) {
$utenti_gruppi[$idutente_gruppo] = 1;
unset($priv_anno_ut_gr);

if ($priv_app_gruppi == "SI") {
$priv_anno_ut_gr = esegui_query("select * from $tableprivilegi where idutente = '$idutente_gruppo' and anno = '$anno'");
if (numlin_query($priv_anno_ut_gr) == 1) {
$regole1_consentite_gr[$idutente_gruppo] = risul_query($priv_anno_ut_gr,0,'regole1_consentite');
$attiva_regole1_consentite_gr[$idutente_gruppo] = substr($regole1_consentite_gr[$idutente_gruppo],0,1);
if ($attiva_regole1_consentite_gr[$idutente_gruppo] != "n") $regole1_consentite_gr[$idutente_gruppo] = explode("#@^",substr($regole1_consentite_gr[$idutente_gruppo],3));
$tariffe_consentite_tmp = risul_query($priv_anno_ut_gr,0,'tariffe_consentite');
$attiva_tariffe_consentite_gr[$idutente_gruppo] = substr($tariffe_consentite_tmp,0,1);
if ($attiva_tariffe_consentite_gr[$idutente_gruppo] == "s") {
$tariffe_consentite_tmp = explode(",",substr($tariffe_consentite_tmp,2));
$tariffe_consentite_vett_gr[$idutente_gruppo] = "";
for ($num3 = 0 ; $num3 < count($tariffe_consentite_tmp) ; $num3++) if ($tariffe_consentite_tmp[$num3]) $tariffe_consentite_vett_gr[$idutente_gruppo][$tariffe_consentite_tmp[$num3]] = "SI";
} # fine if ($attiva_tariffe_consentite_gr[$idutente_gruppo] == "s")
$priv_ins_prenota_tmp = risul_query($priv_anno_ut_gr,0,'priv_ins_prenota');
$priv_ins_nuove_prenota_gr[$idutente_gruppo] = substr($priv_ins_prenota_tmp,0,1);
$priv_ins_assegnazione_app_gr[$idutente_gruppo] = substr($priv_ins_prenota_tmp,1,1);
$priv_mod_prenota_tmp = risul_query($priv_anno_ut_gr,0,'priv_mod_prenota');
$priv_mod_prenotazioni_gr[$idutente_gruppo] = substr($priv_mod_prenota_tmp,0,1);
$priv_mod_assegnazione_app_gr[$idutente_gruppo] = substr($priv_mod_prenota_tmp,2,1);
} # fine if (numlin_query($priv_anno_ut_gr) == 1)
else {
$priv_ins_nuove_prenota_gr[$idutente_gruppo] = "n";
$priv_mod_prenotazioni_gr[$idutente_gruppo] = "n";
} # fine else if (numlin_query($priv_anno_ut_gr) == 1)
} # fine if ($priv_app_gruppi == "SI")

if ($priv_vedi_tab_periodi == "g") {
if (!$priv_anno_ut_gr) $priv_anno_ut_gr = esegui_query("select * from $tableprivilegi where idutente = '$idutente_gruppo' and anno = '$anno'");
if (numlin_query($priv_anno_ut_gr) == 1) {
$costi_agg_consentiti_tmp = risul_query($priv_anno_ut_gr,0,'costi_agg_consentiti');
$attiva_costi_agg_consentiti_tmp = substr($costi_agg_consentiti_tmp,0,1);
if ($attiva_costi_agg_consentiti_tmp == "n") $attiva_costi_agg_consentiti_gr = "n";
if ($attiva_costi_agg_consentiti_gr == "s") {
$costi_agg_consentiti_tmp = explode(",",substr($costi_agg_consentiti_tmp,2));
for ($num3 = 0 ; $num3 < count($costi_agg_consentiti_tmp) ; $num3++) if ($costi_agg_consentiti_tmp[$num3]) $costi_agg_consentiti_vett_gr[$costi_agg_consentiti_tmp[$num3]] = "SI";
} # fine if ($attiva_costi_agg_consentiti_gr == "s")
} # fine if (numlin_query($priv_anno_ut_gr) == 1)
} # fine if ($priv_vedi_tab_periodi == "g")

} # fine if ($idutente_gruppo != $id_utente)
} # fine for $num2
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")


} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$priv_vedi_tab_periodi = "s";
$priv_vedi_tab_regole = "s";
$priv_vedi_tab_mesi = "s";
$priv_ins_nuove_prenota = "s";
$priv_ins_assegnazione_app = "s";
$priv_mod_prenotazioni = "s";
$priv_mod_assegnazione_app = "s";
$attiva_regole1_consentite = "n";
$attiva_tariffe_consentite = "n";
$attiva_costi_agg_consentiti = "n";
$priv_mod_tariffe = "s";
$priv_ins_costi_agg = "s";
$priv_mod_costo_agg = "s";
$priv_canc_costi_agg = "s";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI" and $priv_vedi_tab_periodi != "n") {

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
$priv_ins_nuove_prenota = "n";
$priv_mod_assegnazione_app = "n";
$priv_mod_tariffe = "n";
$priv_ins_costi_agg = "n";
$priv_mod_costo_agg = "n";
$priv_canc_costi_agg = "n";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


include("./includes/funzioni_tariffe.php");
include("./includes/funzioni_costi_agg.php");
$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();

unset($tabelle_lock);
$tabelle_lock = array();
$altre_tab_lock = array($tableperiodi,$tablenometariffe,$tableregole);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

$periodi = esegui_query("select * from $tableperiodi order by idperiodi");
$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num']);
$dati_r2 = "";
dati_regole2($dati_r2,$app_regola2_predef,"","","",$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
unlock_tabelle($tabelle_lock);

$numero_tariffe = $dati_tariffe['num'];
if (!$num_tariffe_mostra) $num_tariffe_mostra = 1;
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if (${"numtariffa".$num1} < 1 or ${"numtariffa".$num1} > $numero_tariffe) {
if ($num1 == 1) ${"numtariffa".$num1} = 1;
else $num_tariffe_mostra = ($num1 - 1);
} # fine if (${"numtariffa".$num1} < 1 or ${"numtariffa".$num1} > $numero_tariffe)
} # fine for $num1

$attiva_tariffe_consentite_ut = $attiva_tariffe_consentite;
$tariffe_consentite_vett_ut = $tariffe_consentite_vett;
$attiva_costi_agg_consentiti_ut = $attiva_costi_agg_consentiti;
$costi_agg_consentiti_vett_ut = $costi_agg_consentiti_vett;
if ($priv_vedi_tab_periodi == "g") {
$priv_vedi_tab_periodi = "p";
foreach ($attiva_tariffe_consentite_gr as $val) if ($val == "n") $attiva_tariffe_consentite = "n";
unset($tariffe_consentite_vett);
foreach ($tariffe_consentite_vett_gr as $idut_gr => $val) {
if (is_array($val)) {
$tar_cons_vett_tmp = $val;
foreach ($tar_cons_vett_tmp as $val2) if ($val2 == "SI") $tariffe_consentite_vett[$tar] = "SI";
} # fine if (is_array($val))
} # fine foreach ($tariffe_consentite_vett_gr as $idut_gr => $val)
$attiva_costi_agg_consentiti = $attiva_costi_agg_consentiti_gr;
$costi_agg_consentiti_vett = $costi_agg_consentiti_vett_gr;
} # fine if ($priv_vedi_tab_periodi == "g")



if ($azione) {
$mostra_tab_tariffe = "NO";


if ($modificadescr and $priv_mod_tariffe != "n") {
$tabelle_lock = array($tabledescrizioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
if (strcmp($n_descrizione_ita,"")) {
if (get_magic_quotes_gpc()) $n_descrizione_ita = stripslashes($n_descrizione_ita);
$n_descrizione_ita = aggslashdb(htmlspecialchars($n_descrizione_ita));
$descr_esistente = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = 'ita' and numero = '1' ");
if (numlin_query($descr_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_descrizione_ita' where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = 'ita' and numero = '1' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('tariffa".$numtariffa1."','tardescr','ita','1','$n_descrizione_ita') ");
} # fine if (strcmp($n_descrizione_ita,""))
else esegui_query("delete from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = 'ita' and numero = '1' ");
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." and strlen($ini_lingua) <= 3 and preg_replace("/[a-z]/","",$ini_lingua) == "") {
$n_descrizione = ${"n_descrizione_".$ini_lingua};
if (strcmp($n_descrizione,"")) {
if (get_magic_quotes_gpc()) $n_descrizione = stripslashes($n_descrizione);
$n_descrizione = aggslashdb(htmlspecialchars($n_descrizione));
$descr_esistente = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = '$ini_lingua' and numero = '1' ");
if (numlin_query($descr_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_descrizione' where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = '$ini_lingua' and numero = '1' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('tariffa".$numtariffa1."','tardescr','$ini_lingua','1','$n_descrizione') ");
} # fine if (strcmp($n_descrizione,""))
else esegui_query("delete from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = '$ini_lingua' and numero = '1' ");
} # fine if ($file != "." && $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "<br>".ucfirst(mex("descrizione della tariffa",$pag))." ".mex("aggiornata",$pag).".<br>";
unlock_tabelle($tabelle_lock);
} # fine if ($modificadescr and $priv_mod_tariffe != "n")


if ($aggurlfoto and $priv_mod_tariffe != "n" and (!defined('C_RESTRIZIONI_DEMO_ADMIN') or C_RESTRIZIONI_DEMO_ADMIN != "SI")) {
if (get_magic_quotes_gpc()) $n_urlfoto = stripslashes($n_urlfoto);
$lowurl = strtolower($n_urlfoto);
if (substr($lowurl,-4) != ".jpg" and substr($lowurl,-5) != ".jpeg" and substr($lowurl,-4) != ".gif" and substr($lowurl,-4) != ".png") $errore = "SI";
if (str_replace("<","",$n_urlfoto) != $n_urlfoto or str_replace(">","",$n_urlfoto) != $n_urlfoto or str_replace("\"","",$n_urlfoto) != $n_urlfoto) $errore = "SI";
if ($errore != "SI") {
$tabelle_lock = array($tabledescrizioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
$foto_esistenti = esegui_query("select * from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarfoto' order by numero desc ");
if (numlin_query($foto_esistenti)) $numfoto = (risul_query($foto_esistenti,0,'numero') + 1);
else $numfoto = 1;
esegui_query("insert into $tabledescrizioni (nome,tipo,numero,testo) values ('tariffa".$numtariffa1."','tarfoto','$numfoto','".aggslashdb($n_urlfoto)."') ");
echo "<br>".ucfirst(mex("la nuova foto è stata aggiunta",$pag)).".<br>";
unlock_tabelle($tabelle_lock);
} # fine if ($errore != "SI")
else echo "".ucfirst(mex("l'url della foto è sbagliata",$pag)).".<br>";
} # fine if ($aggurlfoto and $priv_mod_tariffe != "n" and...


if ($commentofoto and $priv_mod_tariffe != "n") {
if ($numfoto and controlla_num_pos($numfoto) == "SI") {
$tabelle_lock = array($tabledescrizioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
$foto_esistente = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarfoto' and numero = '$numfoto' ");
if (numlin_query($foto_esistente)) {
if (strcmp($n_commento_ita,"")) {
if (get_magic_quotes_gpc()) $n_commento_ita = stripslashes($n_commento_ita);
$n_commento_ita = aggslashdb(htmlspecialchars($n_commento_ita));
$comm_esistente = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
if (numlin_query($comm_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_commento_ita' where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('tariffa".$numtariffa1."','tarcommfoto','ita','$numfoto','$n_commento_ita') ");
} # fine if (strcmp($n_commento_ita,""))
else esegui_query("delete from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." and strlen($ini_lingua) <= 3 and preg_replace("/[a-z]/","",$ini_lingua) == "") {
$n_commento = ${"n_commento_".$ini_lingua};
if (strcmp($n_commento,"")) {
if (get_magic_quotes_gpc()) $n_commento = stripslashes($n_commento);
$n_commento = aggslashdb(htmlspecialchars($n_commento));
$comm_esistente = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
if (numlin_query($comm_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_commento' where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('tariffa".$numtariffa1."','tarcommfoto','$ini_lingua','$numfoto','$n_commento') ");
} # fine if (strcmp($n_commento,""))
else esegui_query("delete from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
} # fine if ($file != "." && $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "<br>".ucfirst(mex("commento della foto",$pag))." $numfoto ".mex("aggiornato",$pag).".<br>";
} # fine if (numlin_query($foto_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($numfoto and controlla_num_pos($numfoto) == "SI") 
} # fine if ($commentofoto and $priv_mod_tariffe != "n")


if ($cancurlfoto and $priv_mod_tariffe != "n") {
if ($numfoto and controlla_num_pos($numfoto) == "SI") {
esegui_query("delete from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and (tipo = 'tarfoto' or tipo = 'tarcommfoto') and numero = '$numfoto' ");
echo "".ucfirst(mex("foto eliminata",$pag)).".<br>";
} # fine if ($numfoto and controlla_num_pos($numfoto) == "SI") 
} # fine if ($cancurlfoto and $priv_mod_tariffe != "n")


echo "<br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"1\">
<input type=\"hidden\" name=\"numtariffa1\" value=\"".$numtariffa1."\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form></div>";

} # fine if ($azione)



if ($mostra_tab_tariffe != "NO") {

if ($cambia_mostra_tab) {
if ($mostra_disp != "s") $mostra_disp = "n";
if ($mostra_pmm != "s") $mostra_pmm = "n";
if ($raggruppa_date != "s") $raggruppa_date = "n";
} # fine if ($cambia_mostra_tab)
if ($mostra_disp != "n") $mostra_disp = "s";
if ($mostra_pmm != "n") $mostra_pmm = "s";
if ($raggruppa_date != "n") $raggruppa_date = "s";

include(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php");
$raggr_per = 0;
$date_menu = array();
for ($num1 = 0 ; $num1 <  count($d_increment) ; $num1++) if ($d_increment[$num1] != 1) $raggr_per = 1;
if ($raggr_per = 1 and $mos_per_sing != "SI") {
$datafine_periodi = explode("<option value=\"",$dates_options_list);
$num_dp = count($datafine_periodi);
for ($num1 = 1 ; $num1 < $num_dp ; $num1++) $date_menu[substr($datafine_periodi[$num1],0,10)] = 1;
$datafine_periodi = substr($datafine_periodi[($num_dp - 1)],0,10);
} # fine if ($raggr_per = 1 and $mos_per_sing != "SI")
else $datafine_periodi = "1970-01-01";
$max_periodo = risul_query($periodi,(numlin_query($periodi) - 1),'idperiodi');



if ($mostra_disp == "s") {
unset($limiti_var);
unset($profondita);
unset($dati_app);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);

include("./includes/liberasettimane.php");
include("./includes/funzioni_quadro_disp.php");
$id_data_inizio_tab_disp = 1;
$num_colonne_tab_disp = $max_periodo - $id_data_inizio_tab_disp + 1;
$limiti_var['n_ini'] = $id_data_inizio_tab_disp;
$limiti_var['n_fine'] = $max_periodo;

$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
$tot_prenota = risul_query($max_prenota,0,0);
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");

$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != '' and motivazione2 = 'x' ");
$num_app_agenzia = numlin_query($app_agenzia);
if ($num_app_agenzia != 0) {
$info_periodi_ag = array();
$info_periodi_ag['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$info_periodi_ag['app'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi_ag['ini'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi_ag['fine'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$info_periodi_ag['numero']++;
} # fine for $num1
if ($info_periodi_ag['numero']) {
inserisci_prenota_fittizie($info_periodi_ag,$profondita,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett,$app_assegnabili_id);
$app_orig_prenota_id = $app_prenota_id;
} # fine if ($info_periodi_ag['numero'])
} # fine if ($num_app_agenzia != 0)
} # fine if ($mostra_disp == "s")


$app_regola2_orig = array();
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
$tariffa[$num1] = "tariffa".${"numtariffa".$num1};
$nometariffa[$num1] = $dati_tariffe[$tariffa[$num1]]['nome'];
if (!strcmp($nometariffa[$num1],"")) {
$nometariffa_vedi[$num1] = mex("tariffa",$pag).${"numtariffa".$num1};
$nometariffa[$num1] = $nometariffa_vedi[$num1];
} # fine if (!strcmp($nometariffa[$num1],""))
else {
if (num_caratteri_testo($nometariffa[$num1]) > 10) $nometariffa_vedi[$num1] = "<small><small>".$nometariffa[$num1]."</small></small>";
else $nometariffa_vedi[$num1] = $nometariffa[$num1];
} # fine else if (!strcmp($nometariffa[$num1],""))
$nome_tariffa[${"numtariffa".$num1}] = $tariffa[$num1];
if (($priv_vedi_tab_periodi == "s" and $priv_vedi_tab_regole == "s" and $priv_vedi_tab_mesi == "s") or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num1}] == "SI") {
if (!$dati_r2[$tariffa[$num1]]) $app_regola2_orig[${"numtariffa".$num1}] = substr($dati_app['lista'],1,-1);
else $app_regola2_orig[${"numtariffa".$num1}] = $dati_r2[$tariffa[$num1]];
} # fine if (($priv_vedi_tab_periodi == "s" and $priv_vedi_tab_regole == "s" and $priv_vedi_tab_mesi == "s") or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num1}] == "SI")
} # fine for $num1


if ($mostra_disp == "s") {
$c_sfondo_tab_disp = "";
$c_inisett_tab_disp = "";
$c_libero_tab_disp = "#0cc80c";
$c_occupato_tab_disp = "#f8011e";
$aper_font_tab_disp = "";
$chiu_font_tab_disp = "";
$fr_persone = "";
$fr_persona = "";
$nome_mese = array();
$nome_mese["01"] = "01";
$nome_mese["02"] = "02";
$nome_mese["03"] = "03";
$nome_mese["04"] = "04";
$nome_mese["05"] = "05";
$nome_mese["06"] = "06";
$nome_mese["07"] = "07";
$nome_mese["08"] = "08";
$nome_mese["09"] = "09";
$nome_mese["10"] = "10";
$nome_mese["11"] = "11";
$nome_mese["12"] = "12";
$colonna_destra_tab_disp = "";
trova_app_consentiti_per_tab_disp($app_consentito,$app_consentito_sett,$quadro_non_preciso,$dati_app,$dati_tariffe,$id_data_inizio_tab_disp,$num_colonne_tab_disp,$dati_r2,$attiva_regole1_consentite,0,"",$condizioni_regole1_consentite,"","","","","","","",$tableregole);
$tab_liberi = crea_quadro_disp($id_data_inizio_tab_disp,$num_colonne_tab_disp,"reg2","SI",$app_consentito,$app_consentito_sett,$app_regola2_orig,$tipo_periodi,$dati_tariffe['num'],$nome_tariffa,$dati_app,$prenota_in_app_sett,$app_orig_prenota_id,$tableperiodi,"SI",$dati_tariffe,1);
} # fine if ($mostra_disp == "s")



if ($mostra_pmm == "s") {
$permanenza_minima = array();
$permanenza_massima = array();
$pmax_attiva = 0;
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
for ($num2 = 0 ; $num2 < $dati_ca['num'] ; $num2++) {
if ($dati_ca[$num2]["tipo_associa_".$tariffa[$num1]] == "s" or $dati_ca[$num2]["tipo_associa_".$tariffa[$num1]] == "r") {
if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) != "s") {
$p_min = 0;
$p_max = 0;
if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) == ">") $p_min = substr($dati_ca[$num2][$tariffa[$num1]],1);
if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) == "<") $p_max = substr($dati_ca[$num2][$tariffa[$num1]],1);
if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) == "=") {
$p_min = substr($dati_ca[$num2][$tariffa[$num1]],1);
$p_max = $p_min;
} # fine if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) == "=")
if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) == "|") {
$p_min = explode("<",substr($dati_ca[$num2][$tariffa[$num1]],1));
$p_max = $p_min[1];
$p_min = $p_min[0];
} # fine if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) == "|")
$num_for = 1;
$ini_for[1] = 1;
$fine_for[1] = $max_periodo;
if ($dati_ca[$num2]["tipo_associa_".$tariffa[$num1]] == "r" and $dati_ca[$num2]['periodipermessi']) {
for ($num3 = 0 ; $num3 < count($dati_ca[$num2]['sett_periodipermessi_ini']) ; $num3++) {
$num_for = $num3 + 1;
$ini_for[$num_for] = $dati_ca[$num2]['sett_periodipermessi_ini'][$num3];
$fine_for[$num_for] = $dati_ca[$num2]['sett_periodipermessi_fine'][$num3];
} # fine for $num3
} # fine if ($dati_ca[$num2]["tipo_associa_".$tariffa[$num1]] == "r" and...
for ($num3 = 1 ; $num3 <= $num_for ; $num3++) {
for ($num4 = $ini_for[$num3] ; $num4 <= $fine_for[$num3] ; $num4++) {
if ($p_min and (!$permanenza_minima[$tariffa[$num1]][$num4] or $p_min > $permanenza_minima[$tariffa[$num1]][$num4])) $permanenza_minima[$tariffa[$num1]][$num4] = $p_min;
if ($p_max and (!$permanenza_massima[$tariffa[$num1]][$num4] or $p_max < $permanenza_massima[$tariffa[$num1]][$num4])) $permanenza_massima[$tariffa[$num1]][$num4] = $p_max;
} # fine for $num4
} # fine for $num3
if ($p_max > 0) $pmax_attiva = 1;
} # fine if (substr($dati_ca[$num2][$tariffa[$num1]],0,1) != "s")
} # fine if ($dati_ca[$num2]["tipo_associa_".$tariffa[$num1]] == "s" or...
} # fine for $num2
} # fine for $num1
} # fine if ($mostra_pmm == "s")



$num_colonne_periodi = 0;
$nuova_colonna = "NO";
unset($tariffa_colonna_periodo);
unset($tariffap_colonna_periodo);
unset($chiusa_colonna_periodo);
unset($importata_colonna_periodo);
unset($liberi_colonna_periodo);
unset($pminima_colonna_periodo);
unset($pmassima_colonna_periodo);
$ini_colonna_periodo[0] = risul_query($periodi,0,'datainizio');
$per_colonna_periodo[0] = risul_query($periodi,0,'idperiodi');
#$ini_colonna_periodo[0] = formatta_data($ini_colonna_periodo[0],$stile_data);
$sett_in_per = 0;
$p_pers = mex("p","visualizza_tabelle.php");
$p_app = array();

while ($sett_in_per == 0 or ($datafine <= $datafine_periodi and !$date_menu[$datafine] and !$mos_per_sing)) {
$sett_in_per++;
$datafine = risul_query($periodi,($sett_in_per - 1),'datafine');
$idper_corr = risul_query($periodi,($sett_in_per - 1),'idperiodi');
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num1}] == "SI") {
$tar_corr = (string) risul_query($periodi,($sett_in_per - 1),$tariffa[$num1]);
$tarp_corr = (string) risul_query($periodi,($sett_in_per - 1),$tariffa[$num1]."p");
if ($dati_r2['napp'][$tariffa[$num1]] > 1) {
$p_app[${"numtariffa".$num1}] = "*".$dati_r2['napp'][$tariffa[$num1]];
if (!$form_tabella) $tar_corr = (string) ((double) $tar_corr * (double) $dati_r2['napp'][$tariffa[$num1]]);
#$tarp_corr = (string) ((double) $tarp_corr * (double) $dati_r2['napp'][$tariffa[$num1]]);
} # fine if ($dati_r2['napp'][$tariffa[$num1]] > 1)
if ($sett_in_per == 1 or (!strcmp($tar_corr,"") and !strcmp($tarp_corr,""))) {
if ($sett_in_per == 1) {
$chiusa_colonna_periodo[$num1][0] = "";
$importata_colonna_periodo[$num1][0] = "";
if ($mostra_disp == "s") $liberi_colonna_periodo[$num1][0] = $tab_liberi[$tariffa[$num1]][$idper_corr];
if ($mostra_pmm == "s") $pminima_colonna_periodo[$num1][0] = $permanenza_minima[$tariffa[$num1]][$idper_corr];
if ($mostra_pmm == "s") $pmassima_colonna_periodo[$num1][0] = $permanenza_massima[$tariffa[$num1]][$idper_corr];
} # fine if ($sett_in_per == 1)
$tariffa_colonna_periodo[$num1][0] = $tar_corr;
$tariffap_colonna_periodo[$num1][0] = $tarp_corr;
} # fine if ($sett_in_per == 1 or (!strcmp($tar_corr,"") and !strcmp($tarp_corr,"")))
elseif (strcmp($tariffa_colonna_periodo[$num1][0],"") or strcmp($tariffap_colonna_periodo[$num1][0],"")) {
if (!strcmp($tariffa_colonna_periodo[$num1][0],"")) $tariffa_colonna_periodo[$num1][0] = 0;
if (!strcmp($tariffap_colonna_periodo[$num1][0],"")) $tariffap_colonna_periodo[$num1][0] = 0;
$tariffa_colonna_periodo[$num1][0] += (double) $tar_corr;
$tariffap_colonna_periodo[$num1][0] += (double) $tarp_corr;
} # fine elseif (strcmp($tariffa_colonna_periodo[$num1][0],"") or strcmp($tariffap_colonna_periodo[$num1][0],""))
if ($dati_tariffe[$tariffa[$num1]]['chiusa'][$idper_corr]) $chiusa_colonna_periodo[$num1][0] = 1;
$tar_imp = periodo_importato_tar($tariffa[$num1],$idper_corr,$dati_tariffe);
if (!$importata_colonna_periodo[$num1][0]) $importata_colonna_periodo[$num1][0] = $tar_imp;
elseif ($tar_imp and $importata_colonna_periodo[$num1][0] != $tar_imp) $importata_colonna_periodo[$num1][0] = -1;
if ($mostra_disp == "s" and $tab_liberi[$tariffa[$num1]][$idper_corr] < $liberi_colonna_periodo[$num1][0]) $liberi_colonna_periodo[$num1][0] = $tab_liberi[$tariffa[$num1]][$idper_corr];
if ($mostra_pmm == "s" and $permanenza_minima[$tariffa[$num1]][$idper_corr] > $pminima_colonna_periodo[$num1][0]) $pminima_colonna_periodo[$num1][0] = $permanenza_minima[$tariffa[$num1]][$idper_corr];
if ($mostra_pmm == "s" and $permanenza_massima[$tariffa[$num1]][$idper_corr] < $pmassima_colonna_periodo[$num1][0]) $pmassima_colonna_periodo[$num1][0] = $permanenza_massima[$tariffa[$num1]][$idper_corr];
} # fine if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or...
} # fine for $num1
} # fine while ($sett_in_per == 0 or...
$sett_in_per_col[0] = $sett_in_per;

$num_periodi = numlin_query($periodi);
$lista_periodi = "";
$lista_sett_in_per = "";
for ($num1 = $sett_in_per_col[0] ; $num1 < $num_periodi ; $num1++) {
$datainizio1 = risul_query($periodi,$num1,'datainizio');
$periodo1 = risul_query($periodi,$num1,'idperiodi');
$sett_in_per = 0;
while ($sett_in_per == 0 or ($datafine <= $datafine_periodi and !$date_menu[$datafine]) and !$mos_per_sing) {
$sett_in_per++;
if ($sett_in_per > 1) $num1++;
$datafine = risul_query($periodi,$num1,'datafine');
$idper_corr = risul_query($periodi,$num1,'idperiodi');
for ($num2 = 1 ; $num2 <= $num_tariffe_mostra ; $num2++) {
if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num2}] == "SI") {
$tar_corr = (string) risul_query($periodi,$num1,$tariffa[$num2]);
$tarp_corr = (string) risul_query($periodi,$num1,$tariffa[$num2]."p");
if ($dati_r2['napp'][$tariffa[$num2]] > 1) {
if (!$form_tabella) $tar_corr = (string) ((double) $tar_corr * (double) $dati_r2['napp'][$tariffa[$num2]]);
#$tarp_corr = (string) ((double) $tarp_corr * (double) $dati_r2['napp'][$tariffa[$num2]]);
} # fine if ($dati_r2['napp'][$tariffa[$num2]] > 1)
if ($sett_in_per == 1 or (!strcmp($tar_corr,"") and !strcmp($tarp_corr,""))) {
${$tariffa[$num2]} = $tar_corr;
${$tariffa[$num2]."p"} = $tarp_corr;
if ($sett_in_per == 1) {
${$tariffa[$num2]."c"} = "";
${$tariffa[$num2]."i"} = "";
if ($mostra_disp == "s") ${$tariffa[$num2]."l"} = $tab_liberi[$tariffa[$num2]][$idper_corr];
if ($mostra_pmm == "s") ${$tariffa[$num2]."pmin"} = $permanenza_minima[$tariffa[$num2]][$idper_corr];
if ($mostra_pmm == "s") ${$tariffa[$num2]."pmax"} = $permanenza_massima[$tariffa[$num2]][$idper_corr];
} # fine if ($sett_in_per == 1)
} # fine if ($sett_in_per == 1 or (!strcmp($tar_corr,"") and !strcmp($tarp_corr,"")))
elseif (strcmp(${$tariffa[$num2]},"") or strcmp(${$tariffa[$num2]."p"},"")) {
if (!strcmp(${$tariffa[$num2]},"")) ${$tariffa[$num2]} = 0;
if (!strcmp(${$tariffa[$num2]."p"},"")) ${$tariffa[$num2]."p"} = 0;
${$tariffa[$num2]} += (double) $tar_corr;
${$tariffa[$num2]."p"} += (double) $tarp_corr;
} # fine elseif (strcmp(${$tariffa[$num2]},"") or strcmp(${$tariffa[$num2]."p"},""))
if ($dati_tariffe[$tariffa[$num2]]['chiusa'][$idper_corr]) ${$tariffa[$num2]."c"} = 1;
$tar_imp = periodo_importato_tar($tariffa[$num2],$idper_corr,$dati_tariffe);
if (!${$tariffa[$num2]."i"}) ${$tariffa[$num2]."i"} = $tar_imp;
elseif ($tar_imp and ${$tariffa[$num2]."i"} != $tar_imp) ${$tariffa[$num2]."i"} = -1;
if ($mostra_disp == "s" and $tab_liberi[$tariffa[$num2]][$idper_corr] < ${$tariffa[$num2]."l"}) ${$tariffa[$num2]."l"} = $tab_liberi[$tariffa[$num2]][$idper_corr];
if ($mostra_pmm == "s" and $permanenza_minima[$tariffa[$num2]][$idper_corr] > ${$tariffa[$num2]."pmin"}) ${$tariffa[$num2]."pmin"} = $permanenza_minima[$tariffa[$num2]][$idper_corr];
if ($mostra_pmm == "s" and $permanenza_massima[$tariffa[$num2]][$idper_corr] > ${$tariffa[$num2]."pmax"}) ${$tariffa[$num2]."pmax"} = $permanenza_massima[$tariffa[$num2]][$idper_corr];
} # fine if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or...
} # fine for $num2
} # fine while ($sett_in_per == 0 or...
if ($sett_in_per != $sett_in_per_col[$num_colonne_periodi]) $nuova_colonna = "SI";
for ($num2 = 1 ; $num2 <= $num_tariffe_mostra ; $num2++) {
if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num2}] == "SI") {
if ((string) ${$tariffa[$num2]} != (string) $tariffa_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if ((string) ${$tariffa[$num2]."p"} != (string) $tariffap_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if (${$tariffa[$num2]."c"} != $chiusa_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if (${$tariffa[$num2]."i"} != $importata_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if ($mostra_disp == "s" and ${$tariffa[$num2]."l"} != $liberi_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if ($mostra_pmm == "s" and ${$tariffa[$num2]."pmin"} != $pminima_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if ($mostra_pmm == "s" and ${$tariffa[$num2]."pmax"} != $pmassima_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
if ($raggruppa_date != "s") $nuova_colonna = "SI";
} # fine if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or...
} # fine for $num2

if ($nuova_colonna == "SI") {
$datainizio = $datainizio1;
#$datainizio = formatta_data($datainizio,$stile_data);
$fine_colonna_periodo[$num_colonne_periodi] = $datainizio;
if ($per_colonna_periodo[$num_colonne_periodi] == ($periodo1 - 1)) $lista_periodi .= $per_colonna_periodo[$num_colonne_periodi].",";
else $lista_periodi .= $per_colonna_periodo[$num_colonne_periodi]."-".($periodo1 - 1).",";
$lista_sett_in_per .= $sett_in_per_col[$num_colonne_periodi].",";
$num_colonne_periodi++;
$ini_colonna_periodo[$num_colonne_periodi] = $datainizio;
$per_colonna_periodo[$num_colonne_periodi] = $periodo1;
$sett_in_per_col[$num_colonne_periodi] = $sett_in_per;
for ($num2 = 1 ; $num2 <= $num_tariffe_mostra ; $num2++) {
if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num2}] == "SI") {
$tariffa_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]};
$tariffap_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."p"};
$chiusa_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."c"};
$importata_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."i"};
if ($mostra_disp == "s") $liberi_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."l"};
if ($mostra_pmm == "s") $pminima_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."pmin"};
if ($mostra_pmm == "s") $pmassima_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."pmax"};
} # fine if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num2}] == "SI")
} # fine for $num2
$nuova_colonna = "NO";
} # fine if ($nuova_colonna == "SI")
} # fine for $num1
$datafine = risul_query($periodi,($num1 - 1),'datafine');
#$datafine = formatta_data($datafine,$stile_data);
$fine_colonna_periodo[$num_colonne_periodi] = $datafine;
if ($per_colonna_periodo[$num_colonne_periodi] == $periodo1) $lista_periodi .= $per_colonna_periodo[$num_colonne_periodi].",";
else $lista_periodi .= $per_colonna_periodo[$num_colonne_periodi]."-$periodo1,";
$lista_sett_in_per .= $sett_in_per_col[$num_colonne_periodi].",";

if (!$mobile_device) $max_num_colonne = 18;
else $max_num_colonne = 8;
$num_tabelle = ceil($num_colonne_periodi / $max_num_colonne);
$max_num_colonne = ceil($num_colonne_periodi / $num_tabelle);
if ($raggruppa_date == "n") {
$mese_ini = substr($ini_colonna_periodo[0],5,2);
$anno_ini = substr($ini_colonna_periodo[0],0,4);
$mese_fine = substr($ini_colonna_periodo[$num_colonne_periodi],5,2);
$anno_fine = substr($ini_colonna_periodo[$num_colonne_periodi],0,4);
if ($anno_ini == $anno_fine) $num_tabelle = $mese_fine - $mese_ini + 1;
else $num_tabelle = (12 - $mese_ini + 1) + (($anno_fine - $anno_ini - 1) * 12) + $mese_fine;
$anno_corr = $anno_ini;
$mese_corr = $mese_ini - 1;
} # fine if ($raggruppa_date == "n")

if ($num_tariffe_mostra == 1) {
echo "<h3 id=\"h_rat\"><span>".ucfirst(mex("tariffa",$pag))." ".$numtariffa1;
if ($dati_tariffe[$tariffa[1]]['nome']) echo ": ".$dati_tariffe[$tariffa[1]]['nome'];
echo ".</span>";
if ($dati_tariffe[$tariffa[1]]['importa_prezzi'][0]) echo "<br><small><small>(".mex("prezzi importati",'visualizza_tabelle.php')." ".mex("dalla tariffa",'visualizza_tabelle.php')." ".$dati_tariffe[$tariffa[1]]['importa_prezzi'][0].")</small></small>";
echo "</h3>";
} # fine if ($num_tariffe_mostra == 1)
else echo "<h3 id=\"h_rat\"><span>".mex("Tabella tariffe del",$pag)." $anno.</span></h3>";
echo "<br><div style=\"text-align: center; font-size: 80%;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"cambia_mostra_tab\" value=\"1\">
<input type=\"hidden\" name=\"mos_per_sing\" value=\"$mos_per_sing\">
<input type=\"hidden\" name=\"form_tabella\" value=\"$form_tabella\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"$num_tariffe_mostra\">";
$lista_tariffe_sel = "";
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
echo "<input type=\"hidden\" name=\"numtariffa$num1\" value=\"".${"numtariffa".$num1}."\">";
$lista_tariffe_sel .= "&amp;numtariffa$num1=".${"numtariffa".$num1}."";
} # fine for $num1
if ($mostra_disp == "n") $check_disp =  "";
else $check_disp =  " checked";
if ($mostra_pmm == "n") $check_pmm = "";
else $check_pmm = " checked";
if ($raggruppa_date == "n") $check_ragd = "";
else $check_ragd = " checked";
echo "<label><input type=\"checkbox\" name=\"mostra_disp\" value=\"s\"$check_disp>".mex("mostra disponibilità",$pag)."</label>
 <label><input type=\"checkbox\" name=\"mostra_pmm\" value=\"s\"$check_pmm>".mex("mostra permanenza minima",$pag)."</label>
 <label><input type=\"checkbox\" name=\"raggruppa_date\" value=\"s\"$check_ragd>".mex("raggruppa le date",$pag)."</label>
 <button class=\"edtm\" type=\"submit\" style=\"font-size: 80%; margin-top: -5px;\"><div>".ucfirst(mex("modifica",$pag))."</div></button>
</div></form></div>";

if ($priv_mod_tariffe != "n") {
if (!$form_tabella) $azione = $pag;
else $azione = 'visualizza_tabelle.php';
echo "<form id=\"f_tpt\" accept-charset=\"utf-8\" method=\"post\" action=\"$azione\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<input type=\"hidden\" name=\"mostra_disp\" value=\"$mostra_disp\">
<input type=\"hidden\" name=\"mostra_pmm\" value=\"$mostra_pmm\">
<input type=\"hidden\" name=\"raggruppa_date\" value=\"$raggruppa_date\">
<input type=\"hidden\" name=\"mos_per_sing\" value=\"$mos_per_sing\">
<input type=\"hidden\" name=\"lista_periodi\" value=\"".substr($lista_periodi,0,-1)."\">
<input type=\"hidden\" name=\"lista_sett_in_per\" value=\"".substr($lista_sett_in_per,0,-1)."\">
<input type=\"hidden\" name=\"tariffe_sel\" value=\"SI\">
<input type=\"hidden\" name=\"origine\" value=\"tab_tariffe.php\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"$num_tariffe_mostra\">";
if ($form_tabella) echo "<input type=\"hidden\" name=\"ins_form_tabella\" value=\"SI\">";
else echo "<input type=\"hidden\" name=\"solo_sel\" value=\"1\">";
$tab_modificabile = 0;
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if (!$dati_tariffe[$tariffa[$num1]]['importa_prezzi'][0] and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI")) $tab_modificabile = 1;
if ($mostra_pmm == "s" and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and $priv_ins_costi_agg != "n" and $priv_mod_costo_agg != "n" and $priv_canc_costi_agg != "n") $tab_modificabile = 1;
echo "<input type=\"hidden\" name=\"numtariffa$num1\" value=\"".${"numtariffa".$num1}."\">
<input type=\"hidden\" name=\"tariffa_sel".${"numtariffa".$num1}."\" value=\"SI\">";
} # fine for $num1
} # fine if ($priv_mod_tariffe != "n")
else $form_tabella = "";

for ($num0 = 1 ; $num0 <= $num_tabelle ; $num0++) {
if ($raggruppa_date == "n") {
$mese_corr++;
if (strlen($mese_corr) < 2) $mese_corr = "0".$mese_corr;
if ($mese_corr > 12) {
$mese_corr = "01";
$anno_corr++;
} # fine if ($mese_corr > 12)
echo "<h4><span>$mese_corr-$anno_corr</span></h4>";
} # fine if ($raggruppa_date == "n")
echo "<div class=\"tab_cont\">
<table id=\"t_pertar$num0\" class=\"t1\" style=\"background-color: $t1color; margin-left: auto; margin-right: auto;\" border=\"$t1border\" cellspacing=\"$t1cellspacing\" cellpadding=\"$t1cellpadding\"><tr><td>&nbsp;</td>";
$mostra_data = array();
for ($num1 = 0 ; $num1 <= $num_colonne_periodi ; $num1++) {
if ($raggruppa_date == "n" and substr($ini_colonna_periodo[$num1],0,8) == "$anno_corr-$mese_corr-") $mostra_data[$num1] = 1;
if ($raggruppa_date != "n" and (($num1 == 0 and $num0 == 1) or $num1 > ($max_num_colonne * ($num0 - 1))) and $num1 <= ($max_num_colonne * $num0)) $mostra_data[$num1] = 1;
if ($mostra_data[$num1]) {
echo "<td align=\"center\" valign=\"top\"><small>";
if ($raggruppa_date != "s") {
echo substr($ini_colonna_periodo[$num1],8,2);
if ($sett_in_per_col[$num1] > 1) echo " - ".substr($fine_colonna_periodo[$num1],8,2);
} # fine if ($raggruppa_date != "s")
else echo formatta_data($ini_colonna_periodo[$num1],$stile_data)."<br>---<br>".formatta_data($fine_colonna_periodo[$num1],$stile_data);
if ($sett_in_per_col[$num1] > 1) echo "<br><small>".str_replace(" ","&nbsp;","(".$sett_in_per_col[$num1]." ".mex($parola_settimane,$pag).")")."</small><a
 href=\"./$pag?anno=$anno&amp;id_sessione=$id_sessione&amp;mostra_disp=$mostra_disp&amp;mostra_pmm=$mostra_pmm&amp;raggruppa_date=$raggruppa_date&amp;mos_per_sing=1&amp;form_tabella=$form_tabella$lista_tariffe_sel\" style=\"text-decoration: none; color: #777777;\" title=\"".mex("mostra prezzi $parola_settimanali",'visualizza_tabelle.php')."\"><b>&prime;</b></a>";
echo "</small></td>";
} # fine if ($mostra_data[$num1])
} # fine for $num1
echo "</tr>";
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num1}] == "SI") {
echo "<tr><td style=\"text-align: left;\">".$nometariffa_vedi[$num1];
if (!$form_tabella and $p_app[${"numtariffa".$num1}]) echo "<small><small> (".$p_app[${"numtariffa".$num1}].")</small></small>";
if ($num_tariffe_mostra > 1 and $dati_tariffe[$tariffa[$num1]]['importa_prezzi'][0]) echo "<small><small> (".mex("dalla tariffa",'visualizza_tabelle.php')." ".$dati_tariffe[$tariffa[$num1]]['importa_prezzi'][0].")</small></small>";
echo "</td>";
for ($num2 = 0 ; $num2 <= $num_colonne_periodi ; $num2++) {
if ($mostra_data[$num2]) {
if ($form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and !$importata_colonna_periodo[$num1][$num2]) {
$tariffa_vedi = "<input type=\"text\" name=\"per".$per_colonna_periodo[$num2]."tar".${"numtariffa".$num1}."\" value=\"".$tariffa_colonna_periodo[$num1][$num2]."\" size=\"8\">".$p_app[${"numtariffa".$num1}];
if ($dati_tariffe[$tariffa[$num1]]['moltiplica'] == "p") $tariffa_vedi .= " + <input type=\"text\" name=\"per".$per_colonna_periodo[$num2]."tar".${"numtariffa".$num1}.""."p\" value=\"".$tariffap_colonna_periodo[$num1][$num2]."\" size=\"6\">*$p_pers";
} # fine if ($form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and...
else {
if (!$tariffa_colonna_periodo[$num1][$num2] and !$tariffap_colonna_periodo[$num1][$num2]) $tariffa_vedi = "&nbsp;";
else {
if ($tariffa_colonna_periodo[$num1][$num2]) $tariffa_vedi = punti_in_num($tariffa_colonna_periodo[$num1][$num2],$stile_soldi);
else $tariffa_vedi = "";
if ($tariffap_colonna_periodo[$num1][$num2]) {
if ($tariffa_colonna_periodo[$num1][$num2]) $tariffa_vedi .= " + ";
$tariffa_vedi .= punti_in_num($tariffap_colonna_periodo[$num1][$num2],$stile_soldi)."*$p_pers";
} # fine if ($tariffap_colonna_periodo[$num1][$num2])
} # fine else if (!$tariffa_colonna_periodo[$num1][$num2] and...
if ($importata_colonna_periodo[$num1][$num2] and $importata_colonna_periodo[$num1][$num2] != $dati_tariffe[$tariffa[$num1]]['importa_prezzi'][0]) {
$tariffa_vedi .= "<br><small><small>";
if ((string) $importata_colonna_periodo[$num1][$num2] == "-1") $tariffa_vedi .= "(".mex("da varie tariffe",'visualizza_tabelle.php').")";
else $tariffa_vedi .= str_replace(" ","&nbsp;","(".mex("dalla tariffa",'visualizza_tabelle.php')." ".$importata_colonna_periodo[$num1][$num2].")");
$tariffa_vedi .= "</small></small>";
} # fine if ($importata_colonna_periodo[$num1][$num2] and $importata_colonna_periodo[$num1][$num2] != $dati_tariffe[$tariffa[$num1]]['importa_prezzi'][0])
} # fine else if ($form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and...
if ($chiusa_colonna_periodo[$num1][$num2]) $style = " style=\"background-color: #777777;\"";
else $style = "";
if (!$form_tabella and !$dati_tariffe[$tariffa[$num1]]['importa_prezzi'][0] and !$importata_colonna_periodo[$num1][$num2] and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI")) $id_td = " id=\"per".$per_colonna_periodo[$num2]."tar".${"numtariffa".$num1}."\"";
else $id_td = "";
echo "<td$id_td align=\"center\"$style>$tariffa_vedi</td>";
} # fine if ($mostra_data[$num2])
} # fine for $num2
echo "</tr>";
if ($mostra_disp == "s" and (($priv_vedi_tab_periodi == "s" and $priv_vedi_tab_regole == "s" and $priv_vedi_tab_mesi == "s") or $attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[${"numtariffa".$num1}] == "SI")) {
echo "<tr><td style=\"text-align: left;\">&nbsp;&nbsp;<small><small><em>".mex("disponibilità",$pag)."</em></small></small></td>";
for ($num2 = 0 ; $num2 <= $num_colonne_periodi ; $num2++) {
if ($mostra_data[$num2]) {
if ($liberi_colonna_periodo[$num1][$num2]) $style = " style=\"background-color: $c_libero_tab_disp;\"";
else $style = " style=\"background-color: $c_occupato_tab_disp;\"";
echo "<td align=\"center\"$style>".$liberi_colonna_periodo[$num1][$num2]."</td>";
} # fine if ($mostra_data[$num2])
} # fine for $num2
echo "</tr>";
} # fine if ($mostra_disp == "s" and (($priv_vedi_tab_periodi == "s" and $priv_vedi_tab_regole == "s" and...
if ($mostra_pmm == "s") {
echo "<tr><td style=\"text-align: left;\">&nbsp;&nbsp;<small><small><em>".mex("permanenza min.",$pag)."</em></small></small></td>";
for ($num2 = 0 ; $num2 <= $num_colonne_periodi ; $num2++) {
if ($mostra_data[$num2]) {
if ($form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and $priv_ins_costi_agg != "n" and $priv_mod_costo_agg != "n" and $priv_canc_costi_agg != "n") {
$p_min = "<input type=\"text\" name=\"per".$per_colonna_periodo[$num2]."tar".${"numtariffa".$num1}."pmin\" value=\"".$pminima_colonna_periodo[$num1][$num2]."\" size=\"3\"><small><small>".mex($parola_settimane,$pag)."</small></small>";
} # fine if ($form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and...
else {
if ($pminima_colonna_periodo[$num1][$num2]) $p_min = $pminima_colonna_periodo[$num1][$num2]."&nbsp;<small><small>".mex($parola_settimane,$pag)."</small></small>";
else $p_min = "&nbsp;";
} # fine else if ($form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI") and...
if (!$form_tabella and ($attiva_tariffe_consentite_ut == "n" or $tariffe_consentite_vett_ut[${"numtariffa".$num1}] == "SI")) $id_td = " id=\"per".$per_colonna_periodo[$num2]."tar".${"numtariffa".$num1}."pmin\"";
else $id_td = "";
echo "<td$id_td align=\"center\">$p_min</td>";
} # fine if ($mostra_data[$num2])
} # fine for $num2
echo "</tr>";
if ($pmax_attiva) {
echo "<tr><td style=\"text-align: left;\">&nbsp;&nbsp;<small><small><em>".mex("permanenza mass.",$pag)."</em></small></small></td>";
for ($num2 = 0 ; $num2 <= $num_colonne_periodi ; $num2++) {
if ($mostra_data[$num2]) {
if ($pmassima_colonna_periodo[$num1][$num2]) $p_max = $pmassima_colonna_periodo[$num1][$num2]."&nbsp;<small><small>".mex($parola_settimane,$pag)."</small></small>";
else $p_max = "&nbsp;";
echo "<td align=\"center\">$p_max</td>";
} # fine if ($mostra_data[$num2])
} # fine for $num2
echo "</tr>";
} # fine if ($pmax_attiva)
} # fine if ($mostra_pmm == "s")
} # fine if ($priv_vedi_tab_periodi == "s" or $attiva_tariffe_consentite == "n" or...
} # fine for $num1
echo "</table></div><br>";
} # fine for $num0


if ($priv_mod_tariffe != "n") {
echo "<div style=\"text-align: center;\">";
$id_repl = "";
if ($num_tariffe_mostra == 1 or ($num_tariffe_mostra * $num_tabelle) > 8) $id_repl = " id=\"modi\"";
if (!$form_tabella) {
echo "<input type=\"hidden\" name=\"form_tabella\" value=\"SI\">
<input type=\"hidden\" name=\"fine_form\" value=\"1\">";
if ($tab_modificabile) echo "<button$id_repl class=\"edit\" type=\"submit\"><div id=\"but_tpt\">".mex("Modifica i campi della tabella",'visualizza_tabelle.php')."</div></button>";
echo "<script type=\"text/javascript\">
<!--
var frase_mod_prezzi_tpt = '".htmlspecialchars(mex("Modifica i valori",$pag))."';
var subm_tpt = 0;
var tab_tariffe = 1;
var num_tab_per_tar = $num_tabelle;
var fr_premere_per_modificare = '".htmlspecialchars(mex("Premere per modificare",'visualizza_tabelle.php'))."';
var tar_per_pers = new Array();
var tar_per_app = new Array();
";
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($dati_tariffe["tariffa$numtariffa"]['moltiplica'] == "p") echo "tar_per_pers[$numtariffa] = 1;
";
if ($p_app[$numtariffa]) echo "tar_per_app[$numtariffa] = '".$p_app[$numtariffa]."';
";
} # fine if for $numtariffa
echo "attiva_mod_prezzi_cella();
-->
</script>";
} # fine if (!$form_tabella)
else echo "<input type=\"hidden\" name=\"fine_form\" value=\"1\">
<button$id_repl class=\"mpri\" type=\"submit\"><div>".mex("Modifica i valori",$pag)."</div></button>";
echo "</div></div></form>";
if ($tab_modificabile) echo "<br>";
} # fine if ($priv_mod_tariffe != "n")


$select_tariffe = "";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$presente = "NO";
for ($num2 = 1 ; $num2 <= $num_tariffe_mostra ; $num2++) if (${"numtariffa".$num2} == $numtariffa) $presente = "SI";
if ($presente == "NO") {
$tariffa_corr = "tariffa".$numtariffa;
$nometariffa_vedi_corr = $dati_tariffe[$tariffa_corr]['nome'];
if (!strcmp($nometariffa_vedi_corr,"")) $nometariffa_vedi_corr = mex("tariffa",$pag).$numtariffa;
else $nometariffa_vedi_corr = mex("tariffa",$pag)."$numtariffa ($nometariffa_vedi_corr)";
$select_tariffe .= "<option value=\"$numtariffa\">$nometariffa_vedi_corr</option>";
} # fine if ($presente == "NO")
} # fine if ($attiva_tariffe_consentite == "n" or...
} # fine for $numtariffa
if ($select_tariffe) {
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"mostra_disp\" value=\"$mostra_disp\">
<input type=\"hidden\" name=\"mostra_pmm\" value=\"$mostra_pmm\">
<input type=\"hidden\" name=\"raggruppa_date\" value=\"$raggruppa_date\">
<input type=\"hidden\" name=\"mos_per_sing\" value=\"$mos_per_sing\">
<input type=\"hidden\" name=\"form_tabella\" value=\"$form_tabella\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"".($num_tariffe_mostra + 1)."\">";
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
echo "<input type=\"hidden\" name=\"numtariffa$num1\" value=\"".${"numtariffa".$num1}."\">";
} # fine for $num1
echo "".ucfirst(mex("tariffa",$pag))."
 <select name=\"numtariffa".($num_tariffe_mostra + 1)."\">
$select_tariffe</select>
<button class=\"plus\" type=\"submit\"><div>".ucfirst(mex("aggiungi",$pag))."</div></button>
</div></form></div><br>";
} # fine if ($select_tariffe)



if ($num_tariffe_mostra == 1) {

if ($dati_r2[$tariffa[1]]) {
$num_app = explode(",",$dati_r2[$tariffa[1]]);
$num_app = count($num_app);
} # fine if ($dati_r2[$tariffa[1]])
else {
if ($dati_app) $num_app = $dati_app['totapp'];
else {
$num_app = esegui_query("select idappartamenti from $tableappartamenti ");
$num_app = numlin_query($num_app);
} # fine else if ($dati_app)
} # fine else if ($dati_r2[$tariffa[1]])
if ($dati_r2['napp'][$tariffa[1]] > 1) {
echo "<em><b>".$dati_r2['napp'][$tariffa[1]]."</b> ";
if ($dati_r2['napp']['v'][$tariffa[1]]) echo strtolower(mex("Appartamenti vicini",'unit.php'));
else echo mex("appartamenti",'unit.php');
echo "</em>"." ".mex("tra",'crearegole.php')." ";
} # fine if ($dati_r2['napp'][$tariffa[1]] > 1)
if ($num_app != 1) echo "$num_app ".strtolower(mex("Appartamenti disponibili",'unit.php'));
else echo "$num_app ".strtolower(mex("Appartamento disponibile",'unit.php'));
echo " ".mex("in modo predefinito per la tariffa",$pag)." \"".$nometariffa_vedi[1]."\": ";
if (!$dati_r2[$tariffa[1]]) echo "<span class=\"colwarn\"><em>".mex("tutti gli appartamenti",'unit.php')."</em></span>.";
else echo "<em>".str_replace(",","</em>, <em>",$dati_r2[$tariffa[1]])."</em>.";
if ($id_utente == 1) echo " <a style=\"display: inline-block; margin-bottom: 2px;\" href=\"./crearegole.php?id_sessione=$id_sessione&amp;anno=$anno&amp;tipotariffa_regola2=".$tariffa[1]."&amp;origine=tab_tariffa\">".mex("Modifica la regola di assegnazione",'interconnessioni.php')." 2</a>";
echo "<br><br>";

$regola4 = esegui_query("select * from $tableregole where tariffa_per_persone = '".$tariffa[1]."'");
if (numlin_query($regola4)) $num_pers_reg4 = risul_query($regola4,0,'iddatainizio');
else $num_pers_reg4 = mex("non definito",$pag);
echo mex("Numero di persone predefinito per la tariffa",$pag)." \"".$nometariffa_vedi[1]."\": <em>$num_pers_reg4</em>.";
if ($id_utente == 1) echo " <a style=\"display: inline-block; margin-bottom: 2px;\" href=\"./crearegole.php?id_sessione=$id_sessione&amp;anno=$anno&amp;tipotariffa_regola4=".$tariffa[1]."&amp;origine=tab_tariffa\">".mex("Modifica la regola di assegnazione",'interconnessioni.php')." 4</a>";
echo "<br><br><br>";

if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa1] == "SI") {
$d_descrizione = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = 'ita' and numero = '1' ");
if (numlin_query($d_descrizione)) $d_descrizione = risul_query($d_descrizione,0,'testo');
else $d_descrizione = "";
if ($priv_mod_tariffe != "n") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"1\">
<input type=\"hidden\" name=\"numtariffa1\" value=\"".$numtariffa1."\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"modificadescr\" value=\"SI\">
".ucfirst(mex("descrizione della tariffa",$pag))." \"<em>".$nometariffa[1]."</em>\":<br>
<table class=\"nomob\"><tr><td>Italiano:<br>
<textarea class=\"widetxt\" name=\"n_descrizione_ita\" rows=4 cols=60 style=\"white-space: pre; overflow: auto;\">$d_descrizione</textarea></td>";
} # fine if ($priv_mod_tariffe != "n")
else echo "".ucfirst(mex("descrizione della tariffa",$pag))." \"<em>".$nometariffa[1]."</em>\":<br>
<table><tr><td>Italiano:<br>
<div style=\"width: 580px;\">\"<em>".nl2br($d_descrizione)."</em>\"</div></td>";
$col = 0;
$max_col = 2;
unset($lingue_vett);
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." and strlen($ini_lingua) <= 3 and preg_replace("/[a-z]/","",$ini_lingua) == "") {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
$lingue_vett[$ini_lingua] = $nome_lingua;
$d_descrizione = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tardescr' and lingua = '$ini_lingua' and numero = '1' ");
if (numlin_query($d_descrizione)) $d_descrizione = risul_query($d_descrizione,0,'testo');
else $d_descrizione = "";
$col++;
if ($col == $max_col) {
$col = 0;
echo "</tr><tr>";
} # fine if ($col == $max_col)
else echo "<td style=\"width: 30px;\"></td>";
echo "<td>".ucfirst($nome_lingua).":<br>";
if ($priv_mod_tariffe != "n") echo "<textarea class=\"widetxt\" name=\"n_descrizione_$ini_lingua\" rows=4 cols=60 style=\"white-space: pre; overflow: auto;\">$d_descrizione</textarea></td>";
else echo "<div style=\"width: 580px;\">\"<em>".nl2br($d_descrizione)."</em>\"</div></td>";
} # fine if ($file != "." && $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "</tr></table>";
if ($priv_mod_tariffe != "n") echo "<button class=\"edit\" type=\"submit\"><div>".ucfirst(mex("modifica",$pag))."</div></button></div></form><br>";

$foto = esegui_query("select * from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarfoto' order by numero ");
$num_foto = numlin_query($foto);
echo "<br>".ucfirst(mex("foto della tariffa",$pag))." \"<em>".$nometariffa[1]."</em>\":<br>";
for ($num1 = 1 ; $num1 <= $num_foto ; $num1++) {
$url_foto = risul_query($foto,($num1 - 1),'testo');
$numfoto = risul_query($foto,($num1 - 1),'numero');
echo "<table><tr><td valign=\"top\">$num1.</td><td>
<a href=\"$url_foto\"><img class=\"dphoto\" style=\"border: 0px none ; text-decoration: none;\" src=\"$url_foto\" alt=\"".htmlspecialchars($url_foto)."\"></a>
</td><td style=\"width: 20px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div class=\"linhbox\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"1\">
<input type=\"hidden\" name=\"numtariffa1\" value=\"".$numtariffa1."\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"commentofoto\" value=\"SI\">
<input type=\"hidden\" name=\"numfoto\" value=\"$numfoto\">
".ucfirst(mex("commento",$pag)).":<br>";
$d_commento = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
if (numlin_query($d_commento)) $d_commento = risul_query($d_commento,0,'testo');
else $d_commento = "";
if ($priv_mod_tariffe != "n") echo "Italiano: <input type=\"text\" name=\"n_commento_ita\" value=\"$d_commento\" size=\"24\"><br>";
else echo "<div style=\"width: 300px;\">Italiano: \"<em>$d_commento</em>\"</div>";
reset($lingue_vett);
foreach ($lingue_vett as $ini_lingua => $nome_lingua) {
$d_commento = esegui_query("select testo from $tabledescrizioni where nome = 'tariffa".$numtariffa1."' and tipo = 'tarcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
if (numlin_query($d_commento)) $d_commento = risul_query($d_commento,0,'testo');
else $d_commento = "";
if ($priv_mod_tariffe != "n") echo "".ucfirst($nome_lingua).": <input type=\"text\" name=\"n_commento_$ini_lingua\" value=\"$d_commento\" size=\"24\"><br>";
else echo "<div style=\"width: 300px;\">".ucfirst($nome_lingua).": \"<em>$d_commento</em>\"</div>";
} # fine foreach ($lingue_vett as $ini_lingua => $nome_lingua)
if ($priv_mod_tariffe != "n") echo "<button class=\"edtm\" type=\"submit\"><div>".ucfirst(mex("modifica",$pag))."</div></button>";
echo "</div></form><br></td><td style=\"width: 20px;\"></td><td valign=\"middle\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"1\">
<input type=\"hidden\" name=\"numtariffa1\" value=\"".$numtariffa1."\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"cancurlfoto\" value=\"SI\">
<input type=\"hidden\" name=\"numfoto\" value=\"$numfoto\">";
if ($priv_mod_tariffe != "n") echo "<button class=\"cpho\" type=\"submit\"><div>".ucfirst(mex("elimina",$pag))."</div></button>";
echo "</div></form></td></tr></table>";
} # fine for $num1
echo "<br>";

if ($priv_mod_tariffe != "n") {
if (defined('C_RESTRIZIONI_DEMO_ADMIN') and C_RESTRIZIONI_DEMO_ADMIN == "SI") $readonly = " readonly=\"readonly\"";
else $readonly = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tariffe_mostra\" value=\"1\">
<input type=\"hidden\" name=\"numtariffa1\" value=\"".$numtariffa1."\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"aggurlfoto\" value=\"SI\">
".ucfirst(mex("url di una nuova foto",$pag)).":
<input type=\"text\" name=\"n_urlfoto\" size=\"30\" value=\"http://\"$readonly>
<button class=\"apho\" type=\"submit\"><div>".ucfirst(mex("aggiungi",$pag))."</div></button>
</div></form><br>";
} # fine if ($priv_mod_tariffe != "n")
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa1] == "SI")

} # fine if ($num_tariffe_mostra == 1)



echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<button id=\"indi\" class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";

} # fine if ($mostra_tab_tariffe != "NO")


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and $priv_vedi_tab_periodi != "n")
} # fine if ($id_utente)



?>
