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

$pag = "disponibilita.php";
$titolo = "HotelDruid: Disponibilità";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/liberasettimane.php");
include("./includes/spezzaprenota.php");
include("./includes/funzioni.php");
include("./includes/funzioni_tariffe.php");
include("./includes/funzioni_costi_agg.php");
include("./includes/sett_gio.php");
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tablerelinventario = $PHPR_TAB_PRE."relinventario";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
$regole1_consentite = risul_query($privilegi_annuali_utente,0,'regole1_consentite');
$attiva_regole1_consentite = substr($regole1_consentite,0,1);
$applica_regole1 = substr($regole1_consentite,1,1);
if ($attiva_regole1_consentite != "n" or $applica_regole1 == "n" or $applica_regole1 == "f") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
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
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
unset($contratti_consentiti_vett);
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1]) $contratti_consentiti_vett[$contratti_consentiti[$num1]] = "SI";
} # fine if ($attiva_contratti_consentiti == "s")
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
$priv_ins_assegnazione_app = substr($priv_ins_prenota,1,1);
$priv_ins_costi_agg = substr($priv_ins_prenota,5,1);
$priv_ins_multiple = substr($priv_ins_prenota,9,1);
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
$priv_mod_assegnazione_app = substr($priv_mod_prenota,2,1);
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
$tableutenti = $PHPR_TAB_PRE."utenti";
$nome_utente = esegui_query("select * from $tableutenti where idutenti = '$id_utente'");
$nome_utente = risul_query($nome_utente,0,'nome_utente');
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$modifica_pers = "SI";
$attiva_regole1_consentite = "n";
$applica_regole1 = "s";
$attiva_tariffe_consentite = "n";
$attiva_costi_agg_consentiti = "n";
$attiva_contratti_consentiti = "n";
$priv_ins_nuove_prenota = "s";
$priv_ins_assegnazione_app = "s";
$priv_ins_costi_agg = "s";
$priv_ins_multiple = "s";
$priv_mod_prenotazioni = "s";
$priv_mod_assegnazione_app = "s";
$priv_vedi_tab_mesi = "s";
$priv_vedi_tab_prenotazioni = "s";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI" and ($priv_ins_nuove_prenota == "s" or $priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n")) {

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


$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();

$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unset($interconnection_name);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($ic_present[$interconnection_name] == "SI") {
include("./includes/interconnect/$mod_ext/functions_import.php");
$funz_import_reservations = "import_reservations_".$interconnection_func_name;
$id_utente_origi = $id_utente;
$id_utente = 1;
$funz_import_reservations("","",$file_interconnessioni,$anno,$PHPR_TAB_PRE,1,$id_utente,$HOSTNAME);
$id_utente = $id_utente_origi;
} # fine if ($ic_present[$interconnection_name] == "SI")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))

unset($regole2);
unset($id_periodo_corrente);
unset($beniinv_presenti);

$tabelle_lock = "";
$altre_tab_lock = array($tableprenota,$tablecostiprenota,$tablenometariffe,$tableperiodi,$tableappartamenti,$tableregole,$tablepersonalizza,$tablerelinventario);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

$appartamenti = esegui_query(" select * from $tableappartamenti ");
$numappartamenti = numlin_query($appartamenti);
unset($app_richiesti_senza_cal);
unset($app_richiesti);
$num_app_richiesti = 1;
$data_inizioperiodo = $inizioperiodo;
$data_inizioperiodo_f = formatta_data($data_inizioperiodo,$stile_data);
$data_fineperiodo = $fineperiodo;
$data_fineperiodo_f = formatta_data($data_fineperiodo,$stile_data);
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$inizioperiodo' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo == 0) { $idinizioperiodo = 10000; }
else { $idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi'); }
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '$fineperiodo' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo == 0) { $idfineperiodo = -1; }
else { $idfineperiodo = risul_query($idfineperiodo,0,'idperiodi'); }
if ($modifica_pers == "NO") {
@include(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php");
if (!strpos($dates_options_list,$inizioperiodo)) $idinizioperiodo = 10000;
if (!strpos($dates_options_list,$fineperiodo)) $idfineperiodo = -1;
} # fine if ($modifica_pers == "NO")
$inizioperiodo = $idinizioperiodo;
$fineperiodo = $idfineperiodo ;
$idinizioperiodo_vett = $idinizioperiodo;
$idfineperiodo_vett = $idfineperiodo;
unset($numpersone_vett);

if ($idfineperiodo < $idinizioperiodo) {
$verificare = "NO";
echo mex("Le date sono sbagliate",$pag).". <br>";
} # fine if ($idfineperiodo < $idinizioperiodo)

$dati_tariffe = dati_tariffe($tablenometariffe);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num'],"NO","",$tableappartamenti);
$dati_r2 = "";
dati_regole2($dati_r2,$app_regola2_predef,"","","",$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);

$dati_email = "";
if ($cognome_1) {
if (@get_magic_quotes_gpc()) $cognome_1 = stripslashes($cognome_1);
$dati_email .= "<input type=\"hidden\" name=\"cognome_1\" value=\"$cognome_1\">";
} # fine if ($cognome_1)
if ($email_1) $dati_email .= "<input type=\"hidden\" name=\"email_1\" value=\"$email_1\">";
if ($testo_email_richiesta) {
if (@get_magic_quotes_gpc()) $testo_email_richiesta = stripslashes($testo_email_richiesta);
$dati_email .= "<input type=\"hidden\" name=\"testo_email_richiesta\" value=\"".str_replace("\"","&quot;",$testo_email_richiesta)."\">";
if ($origine) $dati_email .= "<input type=\"hidden\" name=\"origine\" value=\"$origine\">";
} # fine if ($testo_email_richiesta)

# Espando le variabili dei costi combinabili (aumentando $numcostiagg per ogni $n_t, alla fine saranno tutti uguali)
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
if (substr(${"idcostoagg".$numca},0,1) == "c") {
$categoria = substr(${"idcostoagg".$numca},1);
$num_in_cat = 0;
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($dati_ca[$num1]['mostra'] == "s" and $dati_ca[$num1]['combina'] == "s" and $dati_ca[$num1]['categoria'] == $categoria) {
$num_in_cat++;
if ($num_in_cat != 1) {
$numcostiagg++;
${"costoagg".$numcostiagg} = ${"costoagg".$numca};
${"idcostoagg".$numcostiagg} = $dati_ca[$num1]['id'];
${"numsettimane".$numcostiagg} = ${"numsettimane".$numca};
${"nummoltiplica_ca".$numcostiagg} = ${"nummoltiplica_ca".$numca};
} # fine else if ($num_in_cat == 1)
else ${"idcostoagg".$numca} = $dati_ca[$num1]['id'];
} # fine if ($dati_ca[$num1]['mostra'] == "s" and $dati_ca[$num1]['combina'] == "s" and...
} # fine for $num1
if (!$num_in_cat) $verificare = "NO";
} # fine if (substr(${"idcostoagg".$numca},0,1) == "c")
} # fine for $numca

$max_maxoccupanti = 0;
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if (!$maxoccupanti) {
$max_maxoccupanti = 0;
break;
} # fine if (!$maxoccupanti)
elseif ($maxoccupanti > $max_maxoccupanti) $max_maxoccupanti = $maxoccupanti;
} # fine for $num1
$numpersone_max = $numpersone;
if ($controlla_tariffe) {
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if (${"reg2_tariffa".$numtariffa} == "SI" and (!$numpersone_max or ${"pers_reg2_tariffa".$numtariffa} > $numpersone_max)) $numpersone_max = ${"pers_reg2_tariffa".$numtariffa};
} # fine for $numtariffa
} # fine if ($controlla_tariffe)

unset($costo_aggiungi_letti);
unset($costo_agg_letti_vett);
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($dati_ca[$numca]['mostra'] == "s") {
$numcostoagg = "";
for ($num1 = 1 ; $num1 <= $numcostiagg ; $num1++) if (${"idcostoagg".$num1} == $dati_ca[$numca]['id']) $numcostoagg = $num1;
if ($priv_ins_costi_agg == "s" and ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$numca]['id']] == "SI")) {
if ($dati_ca[$numca]['letto'] == "s") {
if ($numcostoagg and ${"costoagg".$numcostoagg} == "SI") {
$costo_aggiungi_letti = "";
break;
} # fine if ($numcostoagg and ${"costoagg".$numcostoagg} == "SI")
elseif (!$costo_aggiungi_letti and ($dati_ca[$numca]['numsett'] != "c" or $dati_ca[$numca]['associasett'] != "s")) {
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,($idfineperiodo - $idinizioperiodo + 1));
if ($periodo_costo_trovato != "NO") {
$num_aggiungi_letti = 1;
if ($dati_ca[$numca]['moltiplica'] == "c" and $max_maxoccupanti and $numpersone_max > $max_maxoccupanti) {
$num_aggiungi_letti = $numpersone_max - $max_maxoccupanti;
if ($dati_ca[$numca]['molt_max'] == "n" and $num_aggiungi_letti > $dati_ca[$numca]['molt_max_num']) $num_aggiungi_letti = $dati_ca[$numca]['molt_max_num'];
} # fine if ($dati_ca[$numca]['moltiplica'] == "c" and $max_maxoccupanti and $numpersone_max > $max_maxoccupanti)
if ($dati_ca[$numca]['numlimite'] and $num_aggiungi_letti > $dati_ca[$numca]['numlimite']) $num_aggiungi_letti = $dati_ca[$numca]['numlimite'];
$settimane_costo_cal = calcola_settimane_costo($tableperiodi,$dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"",($idfineperiodo - $idinizioperiodo + 1));
calcola_moltiplica_costo($dati_ca,$numca,$moltiplica_costo_cal,$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$num_aggiungi_letti,"","");
unset($num_costi_presenti);
$limite_costo = controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal);
if ($dati_ca[$numca]['tipo_beniinv'] == "mag") {
$nrc = "";
unset($beniinv_presenti_copia);
$risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca,$beniinv_presenti_copia,$nrc,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,"");
} # fine if ($dati_ca[$numca]['tipo_beniinv'] == "mag")
else $risul_beniinv = "SI";
if ($limite_costo != "NO" and $risul_beniinv == "SI") {
$numca_cal = $numca;
$costo_aggiungi_letti = $dati_ca[$numca]['id'];
$app_incomp_cal = $dati_ca[$numca]['appincompatibili'];
if ($dati_ca[$numca]['tipo_beniinv'] == "app") $app_beniinv_cal = 1;
else $app_beniinv_cal = 0;
$nome_cal = $dati_ca[$numca]['nome'];
} # fine if ($limite_costo != "NO" and $risul_beniinv == "SI")
} # fine if ($periodo_costo_trovato != "NO")
} # fine elseif (!$costo_aggiungi_letti and...
} # fine if ($dati_ca[$numca]['letto'] == "s")
} # fine if ($priv_ins_costi_agg == "s" and ($attiva_costi_agg_consentiti == "n" or...
} # fine if ($dati_ca[$num_costo]['mostra'] == "s")
} # fine for $numca



unset($num_app_richiesti_invia);
$num_app_richiesti_invia[1] = 1;
$num_app_richiesti_invia[0][1] = 1;
unset($num_persone_invia);
unset($persone_tariffa);
$numpersone_orig = $numpersone;
$numpersone_orig_nt = array();
$numpersone_r2 = array();
$persone_tariffa_r2 = array();
$num_tipologie_r2 = 0;
$controlla_con_costo_letto = 0;
$controlla_con_costo_letto_r2 = array();
$minoccupanti_con_cal_vett = array();


if ($numpersone) {
unset($num_persone_casa);
unset($controlla_tariffe);
$posto = 0;
$posto_r2 = 0;
$posto_senza_cal_r2 = array();
$app_richiesti_r2 = array();
$app_richiesti_senza_cal_r2 = array();

for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$numpersone_corr = ceil((double) $numpersone / (double) $dati_r2['napp']["tariffa".$numtariffa]);
if ($numpersone_corr != $numpersone) {
$persone_tariffa_r2[$numtariffa] = $numpersone_corr;
$persone_tariffa[$numtariffa] = $numpersone_corr;
$numpersone_r2[$numpersone_corr] = 1;
} # fine if ($numpersone_corr != $numpersone)
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
} # fine for $numtariffa
ksort($numpersone_r2);
$numpersone_r2 = array_keys($numpersone_r2);
reset($numpersone_r2);
$num_tipologie_r2 = count($numpersone_r2);

for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxoccupanti_orig = risul_query($appartamenti,$num1,'maxoccupanti');
$maxoccupanti = $maxoccupanti_orig;
if ($maxoccupanti and $costo_aggiungi_letti and !strstr(",$app_incomp_cal,",",$idapp,")) $maxoccupanti += $num_aggiungi_letti;
if ($maxoccupanti and $maxoccupanti < $numpersone) $app_richiesti[$idapp] = "NO";
else {
$app_richiesti[$idapp] = "SI";
$posto = 1;
} # fine else if ($maxoccupanti and $maxoccupanti < $numpersone)
if (!$maxoccupanti_orig or $maxoccupanti_orig >= $numpersone) $app_richiesti_senza_cal[$idapp] = "SI";
elseif ($app_richiesti[$idapp] == "SI") $controlla_con_costo_letto = 1;

for ($num2 = 0 ; $num2 < $num_tipologie_r2 ; $num2++) {
if (!$maxoccupanti or $maxoccupanti >= $numpersone_r2[$num2]) {
if (strcmp($app_richiesti_r2[$num2],"")) $app_richiesti_r2[$num2] .= ",";
$app_richiesti_r2[$num2] .= $idapp;
$posto_r2 = 1;
} # fine if (!$maxoccupanti or $maxoccupanti >= $numpersone_r2[$num2])
if (!$maxoccupanti_orig or $maxoccupanti_orig >= $numpersone_r2[$num2]) $app_richiesti_senza_cal_r2[$num2][$idapp] = "SI";
elseif ($maxoccupanti >= $numpersone_r2[$num2]) $controlla_con_costo_letto_r2[$num2] = 1;
} # fine for $num2

} # fine for $num1
$num_persone_invia[1] = $numpersone;
if (!$posto and !$posto_r2) {
echo mex("Non c'è nessun appartamento che possa ospitare",'unit.php')." $numpersone ".mex("persone",$pag).".<br>";
$verificare = "NO";
} # fine if (!$posto and !$posto_r2)

elseif ($num_tipologie_r2) {
# Se ci sono tariffe che richiedono appartamenti multipli allora inserisco gli appartamenti per le persone alternative nelle
# altre tipologie (per i controlli), prendendo poi separatamente ognuna di queste tipologie quando si deve usare liberasettimane
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
$app_richiesti_copia = $app_richiesti;
$app_richiesti = array();
$app_richiesti[',numero,'] = 0;
$numpersone_r2_copia = $numpersone_r2;
$numpersone_r2 = array();
$controlla_con_costo_letto_r2_copia = $controlla_con_costo_letto_r2;
$controlla_con_costo_letto_r2 = array();
$app_richiesti_senza_cal_r2_copia = $app_richiesti_senza_cal_r2;
$app_richiesti_senza_cal_r2 = array();
for ($num1 = 0 ; $num1 < $num_tipologie_r2 ; $num1++) {
if (strcmp($app_richiesti_r2[$num1],"")) {
$app_richiesti[',numero,']++;
$numpersone_r2[$numpersone_r2_copia[$num1]] = $app_richiesti[',numero,'];
$app_richiesti[$app_richiesti[',numero,']] = $app_richiesti_r2[$num1];
$idinizioperiodo_vett[$app_richiesti[',numero,']] = $idinizioperiodo;
$idfineperiodo_vett[$app_richiesti[',numero,']] = $idfineperiodo;
$numpersone_vett[$app_richiesti[',numero,']] = $numpersone_r2_copia[$num1];
$controlla_con_costo_letto_r2[$app_richiesti[',numero,']] = $controlla_con_costo_letto_r2_copia[$num1];
$app_richiesti_senza_cal_r2[$app_richiesti[',numero,']] = $app_richiesti_senza_cal_r2_copia[$num1];
} # fine if (strcmp($app_richiesti_r2[$num1],""))
} # fine for $num1
if ($posto) {
$app_richiesti[',numero,']++;
$lista_app_richiesti = "";
foreach ($app_richiesti_copia as $key => $val) if ($val == "SI") $lista_app_richiesti .= $key.",";
$app_richiesti[$app_richiesti[',numero,']] = substr($lista_app_richiesti,0,-1);
$idinizioperiodo_vett[$app_richiesti[',numero,']] = $idinizioperiodo;
$idfineperiodo_vett[$app_richiesti[',numero,']] = $idfineperiodo;
} # fine if ($posto)
} # fine elseif ($num_tipologie_r2)

} # fine if ($numpersone)


if ($num_persone_casa) {
unset($controlla_tariffe);
$posto = "NO";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1 = $num1 + 1) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if ($maxoccupanti and $maxoccupanti != $num_persone_casa) $app_richiesti[$idapp] = "NO";
else {
$app_richiesti[$idapp] = "SI";
$posto = "SI";
} # fine else if ($maxoccupanti and $maxoccupanti != $num_persone_casa)
} # fine for $num1
if ($posto == "NO") {
echo mex("Non c'è nessun appartamento da",'unit.php')." $num_persone_casa ".mex("persone",$pag).".<br>";
$verificare = "NO";
} # fine if ($posto == "NO")
else {
if (controlla_num_pos($molt_app_persone_casa) == "NO" or $molt_app_persone_casa == 0 or strlen($molt_app_persone_casa) > 3 or $priv_ins_multiple == "n") $molt_app_persone_casa = 1;
$num_app_richiesti_invia[1] = $molt_app_persone_casa;
$num_app_richiesti_invia[0][1] = $molt_app_persone_casa;
if ($molt_app_persone_casa > 1) {
$num_app_richiesti = $molt_app_persone_casa;
$app_richiesti_copia = $app_richiesti;
unset($app_richiesti);
$lista_app_richiesti = "";
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
$app_richiesti[',numero,'] = $molt_app_persone_casa;
if (!$app_richiesti_copia) {
#$lista_app_richiesti = ",tutti,,";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) $lista_app_richiesti .= risul_query($appartamenti,$num1,'idappartamenti').",";
} # fine if (!$app_richiesti_copia)
else foreach ($app_richiesti_copia as $key => $val) if ($val == "SI") $lista_app_richiesti .= $key.",";
$lista_app_richiesti = substr($lista_app_richiesti,0,-1);
for ($num1 = 1 ; $num1 <= $molt_app_persone_casa ; $num1++) {
$app_richiesti[$num1] = $lista_app_richiesti;
$idinizioperiodo_vett[$num1] = $idinizioperiodo;
$idfineperiodo_vett[$num1] = $idfineperiodo;
} # fine for $num1
} # fine if ($molt_app_persone_casa > 1)
} # fine else if ($posto == "NO")
} # fine if ($num_persone_casa)


if ($priv_ins_multiple == "n") unset($controlla_tariffe);
if ($priv_ins_multiple != "s") $prenota_vicine = "";
if ($controlla_tariffe) {
unset($mostra_tariffa);
$mostra_non_disp = 0;
$id_richiesti = 0;
$num_tipologie = 0;
unset($app_richiesti);
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
if ($prenota_vicine != "SI") {
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) if (${"reg2_tariffa".$numtariffa} == "SI" and $dati_r2['napp']['v']["tariffa".$numtariffa]) $app_richiesti[',vicini,'] = "SI";
} # fine if ($prenota_vicine != "SI")
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa = $numtariffa + 1) {
if (${"reg2_tariffa".$numtariffa} == "SI") {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$appartamenti_regola2 = dati_regole2($dati_r2,$app_regola2_predef,$tariffa,$idinizioperiodo,$idfineperiodo,$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
if ($appartamenti_regola2) {
$numpersone_corr = ${"pers_reg2_tariffa".$numtariffa};
if (!$numpersone_corr) {
$regola4 = esegui_query("select * from $tableregole where tariffa_per_persone = '$tariffa'");
if (numlin_query($regola4) == 1) $numpersone_corr = risul_query($regola4,0,'iddatainizio');
} # fine if (!$numpersone_corr)
$molt_tipologia = ${"molt_reg2_tariffa".$numtariffa};
$numpersone_tot = $numpersone_corr;
if (controlla_num_pos($molt_tipologia) == "NO" or $molt_tipologia == 0 or strlen($molt_tipologia) > 3) ${"molt_reg2_tariffa".$numtariffa} = 1;
$numpersone_orig_nt[$numtariffa] = $numpersone_corr;
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$molt_tipologia = ${"molt_reg2_tariffa".$numtariffa} * $dati_r2['napp']["tariffa".$numtariffa];
$numpersone_corr = ceil((double) $numpersone_corr / (double) $dati_r2['napp']["tariffa".$numtariffa]);
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
else $molt_tipologia = ${"molt_reg2_tariffa".$numtariffa};
if ($numpersone_corr) {
$appartamenti_regola2_orig = $appartamenti_regola2;
$app_regola2_predef_orig = $app_regola2_predef;
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if ($maxoccupanti and $maxoccupanti < $numpersone_corr) {
$appartamenti_regola2 = substr(str_replace(",$idapp,",",",",".$appartamenti_regola2.","),1,-1);
if ($app_regola2_predef_orig) $app_regola2_predef = substr(str_replace(",$idapp,",",",",".$app_regola2_predef.","),1,-1);
} # fine if ($maxoccupanti and $maxoccupanti < $numpersone_corr)
} # fine for $num1
if ((!$appartamenti_regola2 or ($app_regola2_predef_orig and !$app_regola2_predef)) and $costo_aggiungi_letti and $dati_ca[$numca_cal]["incomp_tariffa".$numtariffa] != "i") {
$appartamenti_regola2 = $appartamenti_regola2_orig;
$app_regola2_predef = $app_regola2_predef_orig;
$numpersone_corr = $numpersone_corr - $num_aggiungi_letti;
$costo_agg_letti_vett[$numtariffa] = $costo_aggiungi_letti;
if ($app_beniinv_cal) $nrc = "";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if ($app_beniinv_cal) $risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca_cal,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,$idapp);
else $risul_beniinv = "SI";
if (($maxoccupanti and $maxoccupanti < $numpersone_corr) or str_replace(",$idapp,","",",$app_incomp_cal,") != ",$app_incomp_cal," or $risul_beniinv != "SI") {
$appartamenti_regola2 = substr(str_replace(",$idapp,",",",",".$appartamenti_regola2.","),1,-1);
if ($app_regola2_predef_orig) $app_regola2_predef = substr(str_replace(",$idapp,",",",",".$app_regola2_predef.","),1,-1);
} # fine if (($maxoccupanti and $maxoccupanti < $numpersone_corr) or...
elseif ((strstr(",$appartamenti_regola2,",",$idapp,") or ($app_regola2_predef_orig and strstr(",$app_regola2_predef,",",$idapp,"))) and (!$minoccupanti_con_cal_vett[$numtariffa] or $minoccupanti_con_cal_vett[$numtariffa] > $maxoccupanti)) $minoccupanti_con_cal_vett[$numtariffa] = $maxoccupanti;
} # fine for $num1
} # fine if ((!$appartamenti_regola2 or ($app_regola2_predef_orig and !$app_regola2_predef)) and...
if ($app_regola2_predef_orig and !$app_regola2_predef) $appartamenti_regola2 = $app_regola2_predef;
} # fine if ($numpersone_corr)
$num_ripeti = 0;
for ($num1 = 1 ; $num1 <= $molt_tipologia ; $num1++) {
$id_richiesti++;
$app_richiesti[$id_richiesti] = $appartamenti_regola2;
$idinizioperiodo_vett[$id_richiesti] = $idinizioperiodo;
$idfineperiodo_vett[$id_richiesti] = $idfineperiodo;
if ($numpersone_corr == $numpersone_tot or ($numpersone_corr * $num1) <= $numpersone_tot) $numpersone_vett[$id_richiesti] = $numpersone_corr;
else $numpersone_vett[$id_richiesti] = ($numpersone_corr - 1);
if ($app_richiesti[',vicini,'] == "SI") {
if (!$dati_r2['napp']['v']["tariffa".$numtariffa]) $app_richiesti[",succ_non_vicino,"][$id_richiesti] = 1;
else {
$num_ripeti++;
if ($dati_r2['napp']["tariffa".$numtariffa] > 1 and $num_ripeti == $dati_r2['napp']["tariffa".$numtariffa]) {
$app_richiesti[",succ_non_vicino,"][$id_richiesti] = 1;
$num_ripeti = 0;
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1 and $num_ripeti == $dati_r2['napp']["tariffa".$numtariffa])
} # fine else if (!$dati_r2['napp']['v']["tariffa".$numtariffa])
} # fine if ($app_richiesti[',vicini,'] == "SI")
} # fine for $num1
$mostra_tariffa[$numtariffa] = ${"molt_reg2_tariffa".$numtariffa};
$persone_tariffa[$numtariffa] = $numpersone_corr;
$num_tipologie++;
$tariffa_invia[$num_tipologie] = $tariffa;
$num_app_richiesti_invia[$num_tipologie] = ${"molt_reg2_tariffa".$numtariffa};
$num_app_richiesti_invia[0][$num_tipologie] = $molt_tipologia;
$num_persone_invia[$num_tipologie] = $numpersone_orig_nt[$numtariffa];
} # fine if ($appartamenti_regola2)
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine if (${"reg2_tariffa".$numtariffa} == "SI")
} # fine for $numtariffa
$costo_totale_tariffe = 0;
if ($id_richiesti > 0) {
$app_richiesti[',numero,'] = $id_richiesti;
if ($prenota_vicine == "SI") $app_richiesti[',vicini,'] = "SI";
} # fine if ($id_richiesti > 0)
else unset($controlla_tariffe);
} # fine if ($controlla_tariffe)
else $num_tipologie = 1;


$num_prenota_tot = 0;
for ($num1 = 1 ; $num1 <= $num_tipologie ; $num1++) $num_prenota_tot = $num_prenota_tot + $num_app_richiesti_invia[0][$num1];

if (!$app_richiesti[',numero,']) {
$app_richiesti_copia = $app_richiesti;
unset($app_richiesti);
$lista_app_richiesti = "";
$app_richiesti[',numero,'] = 1;
if (!$app_richiesti_copia) {
#$lista_app_richiesti = ",tutti,,";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) $lista_app_richiesti .= risul_query($appartamenti,$num1,'idappartamenti').",";
} # fine if (!$app_richiesti_copia)
else foreach ($app_richiesti_copia as $key => $val) if ($val == "SI") $lista_app_richiesti .= $key.",";
$app_richiesti[1] = substr($lista_app_richiesti,0,-1);
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
$idinizioperiodo_vett[1] = $idinizioperiodo;
$idfineperiodo_vett[1] = $idfineperiodo;
} # fine if (!$app_richiesti[',numero,'])


# se vi sono costi con appartamenti incompatibili
$app_incomp_costi = "";
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
$costoagg = "costoagg".$numca;
$idcostoagg = "idcostoagg".$numca;
if ($priv_ins_costi_agg != "s" or ($attiva_costi_agg_consentiti != "n" and $costi_agg_consentiti_vett[$$idcostoagg] != "SI")) $$costoagg = "";
if ($$costoagg == "SI" and $dati_ca[$dati_ca['id'][$$idcostoagg]]['appincompatibili']) $app_incomp_costi .= ",".$dati_ca[$dati_ca['id'][$$idcostoagg]]['appincompatibili'];
} # fine for $numca
if ($app_incomp_costi) {
$app_incomp_costi .= ",";
for ($n_r = 1 ; $n_r <= $app_richiesti[',numero,'] ; $n_r++) {
$lista_app_richiesti = ",".$app_richiesti[$n_r].",";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if (str_replace(",$idapp,","",$app_incomp_costi) != $app_incomp_costi) {
$app_richiesti[$n_r] = substr(str_replace(",$idapp,",",",",".$app_richiesti[$n_r].","),1,-1);
} # fine if (str_replace(",$idapp,","",$app_incomp_costi) != $app_incomp_costi)
} # fine for $num1
} # fine for $n_r
} # fine if ($app_incomp_costi)

# se vi sono costi con beni inventario dall'appartamento
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
$costoagg = "costoagg".$numca;
$idcostoagg = "idcostoagg".$numca;
$num_costo = $dati_ca['id'][$$idcostoagg];
if ($dati_ca[$num_costo]['tipo_beniinv'] == "app" and $$costoagg == "SI" and $dati_ca[$num_costo]['mostra'] == "s") {
$nrc = "";
$numsettimane_aux = ${"numsettimane".$numca};
$settimane_costo = calcola_settimane_costo($tableperiodi,$dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,"",aggslashdb($numsettimane_aux));
for ($n_r = 1 ; $n_r <= $app_richiesti[',numero,'] ; $n_r++) {
$numpersone_corr = $numpersone;
if ($numpersone_vett[$n_r]) $numpersone_corr = $numpersone_vett[$n_r];
$nummoltiplica_ca_aux = ${"nummoltiplica_ca".$numca};
calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo,aggslashdb($nummoltiplica_ca_aux),$numpersone_corr,"");
$lista_app_richiesti = ",".$app_richiesti[$n_r].",";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if (str_replace(",$idapp,","",",".$app_richiesti[$n_r].",") != ",".$app_richiesti[$n_r].",") {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica_costo,$idapp);
if ($risul != "SI") {
$app_richiesti[$n_r] = substr(str_replace(",$idapp,",",",",".$app_richiesti[$n_r].","),1,-1);
$app_incomp_costi = "SI";
} # fine ($risul != "SI")
} # fine if (str_replace(",$idapp,","",",".$app_richiesti[$n_r].",") != ",".$app_richiesti[$n_r].",")
} # fine for $num1
} # fine for $n_r
} # fine if ($dati_ca[$num_costo]['tipo_beniinv'] == "app" and $$costoagg == "SI" and...
} # fine for $numca


if ($attiva_regole1_consentite == "s" and $verificare != "NO") {
unset($condizioni_regole1_consentite);
unset($lista_app_richiesti);
for ($num1 = 0 ; $num1 < count($regole1_consentite) ; $num1++) if ($regole1_consentite[$num1]) $condizioni_regole1_consentite .= "motivazione = '".$regole1_consentite[$num1]."' or ";
if (!$condizioni_regole1_consentite) {
echo mex("Non c'è nussun periodo delle regole 1 in cui sia consentito inserire prenotazioni per l'utente",$pag)." $nome_utente.<br>";
$verificare = "NO";
} # fine if (!$condizioni_regole1_consentite)
else {
$condizioni_regole1_consentite = "(".str_replace("motivazione = ' '","motivazione = '' or motivazione is null",substr($condizioni_regole1_consentite,0,-4)).")";
for ($n_r = 1 ; $n_r <= $app_richiesti[',numero,'] ; $n_r++) {
$lista_app_richiesti = ",".$app_richiesti[$n_r].",";
unset($lista_app_richiesti2);
$posti = 0;
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if ($lista_app_richiesti == ",,tutti,," or str_replace(",$idapp,","",$lista_app_richiesti) != $lista_app_richiesti) {
$appartamento_consentito = esegui_query("select idregole,iddatainizio,iddatafine from $tableregole where app_agenzia = '$idapp' and (motivazione2 != 'x' or motivazione2 is NULL) and iddatainizio <= '$idfineperiodo' and iddatafine >= '$idinizioperiodo' and $condizioni_regole1_consentite order by iddatainizio");
unset($iddatainizio_regole_tot);
unset($iddatafine_regole_tot);
for ($num2 = 0 ; $num2 < numlin_query($appartamento_consentito) ; $num2++) {
$iddatainizio_regola = risul_query($appartamento_consentito,$num2,'iddatainizio');
$iddatafine_regola = risul_query($appartamento_consentito,$num2,'iddatafine');
if ($num2 == 0) {
$iddatainizio_regole_tot = $iddatainizio_regola;
$iddatafine_regole_tot = $iddatafine_regola;
} # fine if ($num2 == 0)
else {
if ($iddatainizio_regola == ($iddatafine_regole_tot + 1)) $iddatafine_regole_tot = $iddatafine_regola;
else break;
} # fine else if ($num2 == 0)
} # fine for $num2
if (numlin_query($appartamento_consentito) > 0 and $iddatainizio_regole_tot <= $idinizioperiodo and $iddatafine_regole_tot >= $idfineperiodo) {
$posti++;
$lista_app_richiesti2 .= $idapp.",";
} # fine if (numlin_query($appartamento_consentito) > 0 and...
} # fine if ($lista_app_richiesti == ",,tutti,," or...
} # fine for $num1
$lista_app_richiesti2 = substr($lista_app_richiesti2,0,-1);
if ($posti == 0) {
echo mex("Non c'è nessun appartamento tra quelli richiesti in cui sia consentito inserire prenotazioni per l'utente",'unit.php')." $nome_utente";
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_r)";
echo ".<br>";
$verificare = "NO";
} # fine if ($posti == 0)
else $app_richiesti[$n_r] = $lista_app_richiesti2;
} # fine for $n_r
} # fine else if (!$condizioni_regole1_consentite)
} # fine if ($attiva_regole1_consentite == "s" and $verificare != "NO")

# se si possono usare solo alcuni appartamenti a causa delle tariffe consentite e le rispettive regole 2
if (($priv_mod_assegnazione_app != "s" or $priv_mod_prenotazioni != "s") and ($priv_ins_assegnazione_app != "s" or $priv_ins_nuove_prenota != "s") and $verificare != "NO") {
unset($appartamenti_consentiti_regola2);
unset($lista_app_richiesti);
$tutti_app_consentiti = "NO";
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$appartamenti_regola2 = dati_regole2($dati_r2,$app_regola2_predef,$tariffa,$idinizioperiodo,$idfineperiodo,$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
if (!$appartamenti_regola2) {
$tutti_app_consentiti = "SI";
break;
} # fine if (!$appartamenti_regola2)
else {
$appartamenti_regola2 = explode(",",$appartamenti_regola2);
for ($num1 = 0 ; $num1 < count($appartamenti_regola2) ; $num1++) $appartamenti_consentiti_regola2[$appartamenti_regola2[$num1]] = "SI";
} # fine else if (!$appartamenti_regola2)
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
if ($tutti_app_consentiti != "SI") {
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
if ($appartamenti_consentiti_regola2[$id_appartamento] != "SI") $appartamenti_consentiti_regola2[$id_appartamento] = "NO";
} # fine for $num1
} # fine if ($tutti_app_consentiti != "SI")
for ($n_r = 1 ; $n_r <= $app_richiesti[',numero,'] ; $n_r++) {
$lista_app_richiesti = ",".$app_richiesti[$n_r].",";
unset($lista_app_richiesti2);
$posti = 0;
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if ($lista_app_richiesti == ",,tutti,," or str_replace(",$idapp,","",$lista_app_richiesti) != $lista_app_richiesti) {
if ($appartamenti_consentiti_regola2[$idapp] != "NO") {
$posti++;
$lista_app_richiesti2 .= $idapp.",";
} # fine if ($appartamenti_consentiti_regola2[$idapp] != "NO")
} # fine if ($lista_app_richiesti == ",,tutti,," or...
} # fine for $num1
$lista_app_richiesti2 = substr($lista_app_richiesti2,0,-1);
if ($posti == 0) {
echo mex("Non c'è nessun appartamento tra quelli richiesti in cui sia consentito inserire prenotazioni per l'utente",'unit.php')." $nome_utente";
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_r)";
echo ".<br>";
$verificare = "NO";
} # fine if ($posti == 0)
else $app_richiesti[$n_r] = $lista_app_richiesti2;
} # fine for $n_r
} # fine if (($priv_mod_assegnazione_app != "s" or...

if ($verificare != "NO") {
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1 = $num1 + 1) {
$prenotazioni = esegui_query("select * from $tableprenota where iddatainizio <= $num1 and iddatafine >= $num1");
$numprenotazioni = numlin_query($prenotazioni);
$rigasettimana = esegui_query("select * from $tableperiodi where idperiodi = '$num1' ");
if ($numprenotazioni >= $numappartamenti) {
if (!$mostra_non_disp) $verificare = "NO";
$inizioperiodopieno = risul_query($rigasettimana,0,'datainizio');
$inizioperiodopieno_f = formatta_data($inizioperiodopieno,$stile_data);
$fineperiodopieno = risul_query($rigasettimana,0,'datafine');
$fineperiodopieno_f = formatta_data($fineperiodopieno,$stile_data);
echo mex("$parola_La $parola_settimana dal",$pag)." $inizioperiodopieno_f ".mex("al",$pag)." $fineperiodopieno_f ".mex("è pien$lettera_a",$pag).".<br>";
} # fine if ($numprenotazioni >= $numappartamenti)
} # fine for $num1
if ($verificare == "NO") {
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"numpersone\" value=\"$numpersone\">
<input type=\"hidden\" name=\"mostra_non_disp\" value=\"1\">
$dati_email
<button class=\"xavl\" type=\"submit\"><div>".mex("Mostra le tariffe non disponibili",$pag)."</div></button>
</div></form><br>";
} # fine if ($verificare == "NO")
} # fine if ($verificare != "NO")



if ($verificare != "NO") {


unset($limiti_var);
unset($profondita);
unset($dati_app);
$limiti_var['idperiodocorrente'] = calcola_id_periodo_corrente($anno);
if ($idinizioperiodo < $limiti_var['idperiodocorrente']) $n_ini = $idinizioperiodo;
else $n_ini = $limiti_var['idperiodocorrente'];
$limiti_var['n_ini'] = $n_ini;
$max_periodo = esegui_query("select max(idperiodi) from $tableperiodi");
$max_periodo = risul_query($max_periodo,0,0);
if ($idfineperiodo <= $limiti_var['idperiodocorrente']) $n_fine = $idfineperiodo;
else $n_fine = $max_periodo;
$limiti_var['n_fine'] = $n_fine;

if ($priv_vedi_tab_mesi != "n") {
$mostra_quadro_disp = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'mostra_quadro_disp' and idutente = '$id_utente'");
$mostra_quadro_disp = risul_query($mostra_quadro_disp,0,'valpersonalizza');
} # fine if ($priv_vedi_tab_mesi != "n")
else $mostra_quadro_disp = "";
if ($mostra_quadro_disp) {
if ($tipo_periodi == "s") $num_colonne_tab_disp = 5;
else $num_colonne_tab_disp = 32;
$id_data_inizio_tab_disp = (floor(( (double) $idinizioperiodo + (double) $idfineperiodo) / 2) - floor((double) $num_colonne_tab_disp / 2));
if (($id_data_inizio_tab_disp + $num_colonne_tab_disp - 1) > $max_periodo) $id_data_inizio_tab_disp = ($max_periodo - $num_colonne_tab_disp + 1);
if ($id_data_inizio_tab_disp < 1) $id_data_inizio_tab_disp = 1;
if ($num_colonne_tab_disp > $max_periodo) $num_colonne_tab_disp = $max_periodo;
if ($limiti_var['n_ini'] > $id_data_inizio_tab_disp) $limiti_var['n_ini'] = $id_data_inizio_tab_disp;
if ($limiti_var['n_fine'] < ($id_data_inizio_tab_disp + $num_colonne_tab_disp - 1)) $limiti_var['n_fine'] = ($id_data_inizio_tab_disp + $num_colonne_tab_disp - 1);
} # fine if ($mostra_quadro_disp)

$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
$tot_prenota = risul_query($max_prenota,0,0);
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
$fatto_libera = "";

unset($condizioni_regole1_non_sel);
if ($applica_regole1 == "n" or ($applica_regole1 == "f" and $attiva_regole1_consentite != "n")) {
for ($num1 = 0 ; $num1 < count($regole1_consentite) ; $num1++) if ($regole1_consentite[$num1]) $condizioni_regole1_non_sel .= "motivazione != '".$regole1_consentite[$num1]."' and ";
if ($condizioni_regole1_non_sel) $condizioni_regole1_non_sel = " and (motivazione2 = 'x' or (".str_replace("motivazione != ' '","motivazione != '' and motivazione is not null",substr($condizioni_regole1_non_sel,0,-5))."))";
} # fine if ($applica_regole1 == "n" or ($applica_regole1 == "f" and...
if (!$condizioni_regole1_non_sel and ($applica_regole1 == "m" or $applica_regole1 == "f")) $condizioni_regole1_non_sel = " and motivazione2 = 'x'";
$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != ''$condizioni_regole1_non_sel");
$num_app_agenzia = numlin_query($app_agenzia);

unlock_tabelle($tabelle_lock);


if ($num_tipologie_r2) {
for ($n_r = 1 ; $n_r < $app_richiesti[',numero,'] ; $n_r++) {
if ($controlla_con_costo_letto_r2[$n_r]) {
$app_richiesti_senza_cal_r2_copia = $app_richiesti_senza_cal_r2[$n_r];
if (@is_array($app_richiesti_senza_cal_r2_copia)) {
$app_richiesti_senza_cal_r2[$n_r] = array();
reset($app_richiesti_senza_cal_r2_copia);
foreach ($app_richiesti_senza_cal_r2_copia as $numapp => $val) {
if (strstr(",".$app_richiesti[$n_r].",",",$numapp,") or $app_richiesti[$n_r] == ",tutti,") $app_richiesti_senza_cal_r2[$n_r][$numapp] = "SI";
} # fine foreach ($app_richiesti_senza_cal_r2_copia as $numapp => $val)
} # fine if (@is_array($app_richiesti_senza_cal_r2_copia))
# Levo da app_richiesti gli appartamenti incompatibili con il costo letto aggiuntivo solo ora
# perchè app_richiesti_senza_cal doveva essere un sottoinsieme di app_richiesti
if ($app_beniinv_cal) $nrc = "";
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if ($app_beniinv_cal) $risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca_cal,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,$numapp);
else $risul_beniinv = "SI";
if (strstr(",$app_incomp_cal,",",$numapp,") or $risul_beniinv != "SI") {
$app_richiesti[$nr] = substr(str_replace(",$numapp,",",",",".$app_richiesti[$nr].","),1,-1);
} # fine if (strstr(",$app_incomp_cal,",",$numapp,") or $risul_beniinv != "SI")
} # fine for $num1
} # fine if ($controlla_con_costo_letto_r2[$n_r])
} # fine for $n_r
$app_richiesti_r2 = $app_richiesti;
} # fine if ($num_tipologie_r2)


if (!$num_tipologie_r2 or $posto) {

if ($num_tipologie_r2) {
unset($app_richiesti);
$app_richiesti[',numero,'] = 1;
$app_richiesti[1] = $app_richiesti_r2[$app_richiesti_r2[',numero,']];
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
$idinizioperiodo_vett[1] = $idinizioperiodo;
$idfineperiodo_vett[1] = $idfineperiodo;
} # fine if ($num_tipologie_r2)

$controllato_con_costo_letto = 0;
if ($controlla_con_costo_letto) {
$lista_app_richiesti = "";
if (@is_array($app_richiesti_senza_cal)) {
reset($app_richiesti_senza_cal);
foreach ($app_richiesti_senza_cal as $numapp => $val) {
if (str_replace(",$numapp,","",",".$app_richiesti[1].",") != ",".$app_richiesti[1]."," or $app_richiesti[1] == ",tutti,") $lista_app_richiesti .= "$numapp,";
} # fine foreach ($app_richiesti_senza_cal as $numapp => $val)
} # fine if (@is_array($app_richiesti_senza_cal))
# Levo da app_richiesti gli appartamenti incompatibili con il costo letto aggiuntivo solo ora
# perchè app_richiesti_senza_cal doveva essere un sottoinsieme di app_richiesti
if ($app_beniinv_cal) $nrc = "";
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if ($app_beniinv_cal) $risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca_cal,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,$numapp);
else $risul_beniinv = "SI";
if (str_replace(",$numapp,","",",$app_incomp_cal,") != ",$app_incomp_cal," or $risul_beniinv != "SI") {
$app_richiesti[1] = substr(str_replace(",$numapp,",",",",".$app_richiesti[1].","),1,-1);
} # fine if (str_replace(",$numapp,","",",$app_incomp_cal,") != ",$app_incomp_cal," or...
} # fine for $num1
if ($lista_app_richiesti) {
$app_richiesti_con_cal = $app_richiesti;
unset($app_richiesti);
$app_richiesti[',numero,'] = 1;
$app_richiesti[1] = substr($lista_app_richiesti,0,-1);
$app_richiesti_senza_cal = $app_richiesti;
} # fine if ($lista_app_richiesti)
else {
$controllato_con_costo_letto = 1;
$controlla_con_costo_letto = 0;
} # fine else if ($lista_app_richiesti)
} # fine if ($controlla_con_costo_letto)


if ($num_app_agenzia != 0) {
$info_periodi_ag = array();
$info_periodi_ag['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 == "x") {
$info_periodi_ag['app'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi_ag['ini'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi_ag['fine'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$info_periodi_ag['numero']++;
} # fine if ($mot2 == "x")
} # fine for $num1
if ($info_periodi_ag['numero']) {
inserisci_prenota_fittizie($info_periodi_ag,$profondita,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett,$app_assegnabili_id);
$app_orig_prenota_id = $app_prenota_id;
} # fine if ($info_periodi_ag['numero'])
$profondita2 = $profondita;
$prenota_in_app_sett2 = $prenota_in_app_sett;
$inizio_prenota_id2 = $inizio_prenota_id;
$fine_prenota_id2 = $fine_prenota_id;
$app_prenota_id2 = $app_prenota_id;
$app_assegnabili_id2 = $app_assegnabili_id;
$limiti_var2 = $limiti_var;
unset($info_periodi_ag);
$info_periodi_ag['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 != "x") {
$info_periodi_ag['app'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi_ag['ini'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi_ag['fine'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$id_app_agenzia[$info_periodi_ag['numero']] = $info_periodi_ag['app'][$info_periodi_ag['numero']];
$idinizio_app_agenzia[$info_periodi_ag['numero']] = $info_periodi_ag['ini'][$info_periodi_ag['numero']];
$idfine_app_agenzia[$info_periodi_ag['numero']] = $info_periodi_ag['fine'][$info_periodi_ag['numero']];
$motivazione_app_agenzia[$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'motivazione');
$info_periodi_ag['numero']++;
} # fine if ($mot2 != "x")
} # fine for $num1
if ($info_periodi_ag['numero']) inserisci_prenota_fittizie($info_periodi_ag,$profondita2,$app_prenota_id2,$inizio_prenota_id2,$fine_prenota_id2,$prenota_in_app_sett2,$app_assegnabili_id2);
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
$numprenotazioni = 0;
for ($num2 = 0 ; $num2 < $dati_app['totapp'] ; $num2++) if ($prenota_in_app_sett2[$dati_app['posizione'][$num2]][$num1]) $numprenotazioni++;
if ($numprenotazioni >= $numappartamenti) $occupare_app_agenzia_sempre = "SI";
} # fine for $num1
if ($occupare_app_agenzia_sempre != "SI") {
$occupare_app_agenzia = 0;
$app_orig_prenota_id2 = $app_prenota_id2;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var2,$anno,$fatto_libera,$app_liberato_vett,$profondita2,$app_richiesti,$app_prenota_id2,$app_orig_prenota_id2,$inizio_prenota_id2,$fine_prenota_id2,$app_assegnabili_id2,$prenota_in_app_sett2,$dati_app,$PHPR_TAB_PRE."prenota");
} # fine if ($occupare_app_agenzia_sempre != "SI")
if ($fatto_libera != "SI") $limiti_var['t_limite'] = (time() + $sec_limite_libsett);
} # fine if ($num_app_agenzia != 0)

if ($fatto_libera != "SI") {
$occupare_app_agenzia = 1;
$app_orig_prenota_id = $app_prenota_id;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera,$app_liberato_vett,$profondita,$app_richiesti,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");
if ($num_app_agenzia != 0 and ($applica_regole1 == "f" and $attiva_regole1_consentite != "n")) $fatto_libera = "NO";
} # fine if ($fatto_libera != "SI")

if ($controlla_con_costo_letto and $fatto_libera != "SI") {
$controllato_con_costo_letto = 1;
$app_richiesti = $app_richiesti_con_cal;

if ($num_app_agenzia != 0 and $occupare_app_agenzia_sempre != "SI") {
$occupare_app_agenzia = 0;
$limiti_var2['t_limite'] = (time() + $sec_limite_libsett);
$app_prenota_id2 = $app_orig_prenota_id2;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var2,$anno,$fatto_libera,$app_liberato_vett,$profondita2,$app_richiesti,$app_prenota_id2,$app_orig_prenota_id2,$inizio_prenota_id2,$fine_prenota_id2,$app_assegnabili_id2,$prenota_in_app_sett2,$dati_app,$PHPR_TAB_PRE."prenota");
} # fine if ($num_app_agenzia != 0 and $occupare_app_agenzia_sempre != "SI")

if ($fatto_libera != "SI") {
$occupare_app_agenzia = 1;
$limiti_var['t_limite'] = (time() + $sec_limite_libsett);
$app_prenota_id = $app_orig_prenota_id;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera,$app_liberato_vett,$profondita,$app_richiesti,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");
if ($num_app_agenzia != 0 and ($applica_regole1 == "f" and $attiva_regole1_consentite != "n")) $fatto_libera = "NO";
} # fine if ($fatto_libera != "SI")

} # fine if ($controlla_con_costo_letto and $fatto_libera != "SI")
/*if ($num_app_agenzia != 0) {
unset($app_orig_prenota_id2);
unset($app_prenota_id2);
unset($inizio_prenota_id2);
unset($fine_prenota_id2);
unset($app_assegnabili_id2);
unset($prenota_in_app_sett2);
} # fine if ($num_app_agenzia != 0)*/

} # fine if (!$num_tipologie_r2 or $posto)


$liberato = "SI";
if ($fatto_libera == "SI") {
echo mex("<b>C'è</b> ancora disponibilità nel periodo richiesto",$pag);
if ($numpersone) echo mex(" in un appartamento da <b>almeno",'unit.php')." $numpersone ".mex("persone",$pag)."</b>";
if ($num_persone_casa) {
if ($app_richiesti[',numero,'] == 1) echo mex(" in un appartamento da",'unit.php')." <b>$num_persone_casa ".mex("persone",$pag)."</b>";
else echo mex(" in",'unit.php')." <b>".$app_richiesti[',numero,']."</b> ".mex("appartamenti da",'unit.php')." <b>$num_persone_casa ".mex("persone",$pag)."</b>";
} # fine if ($num_persone_casa)
if ($controlla_tariffe) {
if ($app_richiesti[',numero,'] == 1) echo mex(" in un appartamento della <b>tariffa selezionata</b>",'unit.php');
else {
echo mex(" in",'unit.php')." <b>".$app_richiesti[',numero,']."</b> ";
if ($prenota_vicine != "SI") echo mex("appartamenti delle <b>tariffe selezionate</b>",'unit.php');
else echo mex("appartamenti vicini delle <b>tariffe selezionate</b>",'unit.php');
} # fine else if ($app_richiesti[',numero,'] == 1)
} # fine if ($controlla_tariffe)
if ($num_app_agenzia != 0 and $occupare_app_agenzia) {
echo mex(", ma si dovranno fare degli spostamenti nei periodi della <div style=\"display: inline; color: blue;\">regola di assegnazione 1</div>",$pag)." (";
for ($num1 = 0 ; $num1 < $info_periodi_ag['numero'] ; $num1++) {
for ($num2 = 1 ; $num2 <= $num_app_richiesti ; $num2++) {
if (@is_array($app_liberato_vett)) $app_liberato = $app_liberato_vett[$num2];
else $app_liberato = $app_liberato_vett;
if ($app_liberato == $id_app_agenzia[$num1] and $idinizioperiodo <= $idfine_app_agenzia[$num1] and $idfineperiodo >= $idinizio_app_agenzia[$num1]) {
if ($motivazione_presente[$motivazione_app_agenzia[$num1]] != "SI") {
$motivazione_presente[$motivazione_app_agenzia[$num1]] = "SI";
if (!$lista_motivazione) $lista_motivazione = "<b>".$motivazione_app_agenzia[$num1]."</b>";
else $lista_motivazione .= ", <b>".$motivazione_app_agenzia[$num1]."</b>";
} # fine if ($motivazione_presente[$motivazione_app_agenzia[$num1]] != "SI")
} # fine if ($app_liberato == $id_app_agenzia[$num1] and...
} # fine for $num2
} # fine for $num1
if (@is_array($app_orig_prenota_id)) {
reset($app_orig_prenota_id);
foreach ($app_orig_prenota_id as $idprenota => $app_prenota) {
if ($app_prenota_id[$idprenota] != $app_prenota) {
for ($num1 = 0 ; $num1 < $info_periodi_ag['numero'] ; $num1 = $num1 + 1) {
if (($app_prenota_id[$idprenota] == $id_app_agenzia[$num1] or $app_prenota == $id_app_agenzia[$num1]) and $inizio_prenota_id[$idprenota] <= $idfine_app_agenzia[$num1] and $fine_prenota_id[$idprenota] >= $idinizio_app_agenzia[$num1]) {
if ($motivazione_presente[$motivazione_app_agenzia[$num1]] != "SI") {
$motivazione_presente[$motivazione_app_agenzia[$num1]] = "SI";
if (!$lista_motivazione) $lista_motivazione = "<b>".$motivazione_app_agenzia[$num1]."</b>";
else $lista_motivazione .= ", <b>".$motivazione_app_agenzia[$num1]."</b>";
} # fine if ($motivazione_presente[$motivazione_app_agenzia[$num1]] != "SI")
} # fine if (($app_prenota_id[$idprenota] == $id_app_agenzia[$num1] or...
} # fine for $num1
} # fine if ($app_prenota_id[$idprenota] != $app_prenota)
} # fine foreach ($app_orig_prenota_id as $idprenota => $app_prenota)
} # fine if (@is_array($app_orig_prenota_id))
echo $lista_motivazione.")";
} # fine if ($num_app_agenzia != 0 and $occupare_app_agenzia)
echo ".<br>";
} # fine if ($fatto_libera == "SI")
else {
echo mex("<b>Non c'è</b> più disponibilità nel periodo richiesto",$pag);
if ($app_incomp_costi) $frase_app_incomp_costi = " ".mex("con i costi aggiuntivi selezionati",$pag);
else $frase_app_incomp_costi = "";
if ($numpersone) echo mex(" in un appartamento da <b>almeno",'unit.php')." $numpersone ".mex("persone",$pag)."</b>";
if ($num_persone_casa) {
if ($app_richiesti[',numero,'] == 1) echo mex(" in un appartamento da",'unit.php')." <b>$num_persone_casa ".mex("persone",$pag)."</b>";
else echo mex(" in",'unit.php')." <b>".$app_richiesti[',numero,']."</b> ".mex("appartamenti da",'unit.php')." <b>$num_persone_casa ".mex("persone",$pag)."</b>";
} # fine if ($num_persone_casa)
if ($controlla_tariffe) {
if ($app_richiesti[',numero,'] == 1) echo mex(" in un appartamento della <b>tariffa selezionata</b>",'unit.php')."$frase_app_incomp_costi.<br>";
else {
echo mex(" in",'unit.php')." <b>".$app_richiesti[',numero,']."</b> ";
if ($prenota_vicine != "SI") echo mex("appartamenti delle <b>tariffe selezionate</b>",'unit.php')."$frase_app_incomp_costi.<br>";
else {
echo mex("appartamenti vicini delle <b>tariffe selezionate</b>",'unit.php').".<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"num_persone_casa\" value=\"$num_persone_casa\">
<input type=\"hidden\" name=\"molt_app_persone_casa\" value=\"$molt_app_persone_casa\">
<input type=\"hidden\" name=\"numpersone\" value=\"$numpersone\">
<input type=\"hidden\" name=\"controlla_tariffe\" value=\"$controlla_tariffe\">
$dati_email";
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
echo "<input type=\"hidden\" name=\"reg2_tariffa$numtariffa\" value=\"".${"reg2_tariffa".$numtariffa}."\">
<input type=\"hidden\" name=\"molt_reg2_tariffa$numtariffa\" value=\"".${"molt_reg2_tariffa".$numtariffa}."\">
<input type=\"hidden\" name=\"pers_reg2_tariffa$numtariffa\" value=\"".${"pers_reg2_tariffa".$numtariffa}."\">";
} # fine for $numtariffa
echo "<button class=\"xavl\" type=\"submit\"><div>".mex("Riprova senza cercare appartamenti vicini",'unit.php')."</div></button>
</div></form>";
} # fine else if ($prenota_vicine != "SI")
} # fine else if ($app_richiesti[',numero,'] == 1)
} # fine if ($controlla_tariffe)
else echo "$frase_app_incomp_costi.<br>";
if (!$mostra_non_disp and !$num_tipologie_r2) {
$liberato = "NO";
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"numpersone\" value=\"$numpersone\">
<input type=\"hidden\" name=\"mostra_non_disp\" value=\"1\">
$dati_email
<button class=\"xavl\" type=\"submit\"><div>".mex("Mostra le tariffe non disponibili",$pag)."</div></button>
</div></form><br>";
} # fine if (!$mostra_non_disp and !$num_tipologie_r2)
} # fine else if ($fatto_libera == "SI")

if ($liberato != "SI" and !$num_persone_casa and !$controlla_tariffe and $app_richiesti[',numero,'] == 1 and !$app_incomp_costi and (!$num_tipologie_r2 or $posto)) {
$app_prenota_id = $app_orig_prenota_id;
$inizio_prenota_id3 = $inizio_prenota_id;
$fine_prenota_id3 = $fine_prenota_id;
$prenota_in_app_sett3 = $prenota_in_app_sett;
spezzaprenota($inizioperiodo,$fineperiodo,$anno,$limiti_var,$profondita,$n_tronchi,$vet_appartamenti,$vett_idinizio,$vett_idfine,$numpersone,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id3,$fine_prenota_id3,$app_assegnabili_id,$prenota_in_app_sett3,$dati_app);
if ($n_tronchi != -1) {
echo mex("Si potrebbe inserire la prenotazione dividendola in",$pag)." <b>$n_tronchi ".mex("parti",$pag)."</b>.<br>";
#$liberato = "SI";
} # fine if ($n_tronchi != -1)
} # fine if ($liberato != "SI" and !$num_persone_casa and !$controlla_tariffe and...



$tabelle_lock = "";
$altre_tab_lock = array($tableprenota,$tablecostiprenota,$tablenometariffe,$tableperiodi,$tableappartamenti,$tableregole,$tablepersonalizza,$tablerelinventario);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);


if ($liberato == "SI") {
$form_regola2 = "NO";
$lunghezza_perioido = $fineperiodo - $inizioperiodo + 1;
echo "<br>".mex("Periodo di",$pag)." <b>$lunghezza_perioido</b> ";
if ($lunghezza_perioido == 1) echo mex("$parola_settimana",$pag);
else echo mex("$parola_settimane",$pag);
echo " ".mex("dal",$pag)." $data_inizioperiodo_f ".mex("al",$pag)." $data_fineperiodo_f.<br><br>
<table cellspacing=0 cellpadding=0 border=0><tr><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"controlla_tariffe\" value=\"SI\">
$dati_email
<table cellspacing=0 cellpadding=0 border=0><tr><td><div class=\"rbox\">
<table cellspacing=0 cellpadding=0 border=0>";
$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
unset($continuare_vett);
unset($app_regola2_vett);
unset($app_regola2_orig);
unset($num_app_reg2_vett);
unset($num_pers_regola4);

unset($app_richiesti_orig);
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
for ($n_r = 1 ; $n_r <= $app_richiesti[',numero,'] ; $n_r++) {
if (str_replace(",$numapp,","",",".$app_richiesti[$n_r].",") != ",".$app_richiesti[$n_r]."," or $app_richiesti[$n_r] == ",tutti,") $app_richiesti_orig[$numapp] = "SI";
} # fine for $n_r
} # fine for $num1


# Controllo per ogni tariffa se esistono i prezzi, i permessi, se vi sono appartamenti compatibili
$rigasettimana = array();
$costo_tariffa_nr = array();
$tariffesettimanali_nr = array();
$tariffesettimanalip_nr = array();
$caparra_nr = array();
$commissioni_nr = array();
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
$tariffa = "tariffa".$numtariffa;
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$numtariffa] != "SI") $continuare_vett[$numtariffa] = "NO";
if ($num_tipologie_r2 and !$posto and !$persone_tariffa_r2[$numtariffa]) $continuare_vett[$numtariffa] = "NO";
if ($controllato_con_costo_letto and $dati_ca[$dati_ca['id'][$costo_aggiungi_letti]]["incomp_tariffa".$numtariffa] == "i" and !$persone_tariffa_r2[$numtariffa]) $continuare_vett[$numtariffa] = "NO";
if ($continuare_vett[$numtariffa] != "NO") {

$app_richiesti_corr = $app_richiesti_orig;
if ($numpersone_orig_nt[$numtariffa]) $numpersone_orig_tar = $numpersone_orig_nt[$numtariffa];
else $numpersone_orig_tar = $numpersone_orig;
$numpersone = $numpersone_orig_tar;
if ($persone_tariffa_r2[$numtariffa]) {
$numpersone = $persone_tariffa_r2[$numtariffa];
$tipologia_r2 = $numpersone_r2[$numpersone];
unset($app_richiesti_corr);
if ($controlla_con_costo_letto_r2[$tipologia_r2]) $app_richiesti_corr = $app_richiesti_senza_cal_r2[$tipologia_r2];
else {
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if (strstr($app_richiesti_r2[$tipologia_r2].",",",$numapp,")) $app_richiesti_corr[$numapp] = "SI";
} # fine for $num1
} # fine else if ($controlla_con_costo_letto_r2[$tipologia_r2])
} # fine if ($persone_tariffa_r2[$numtariffa])
else $tipologia_r2 = -1;
if ($persone_tariffa[$numtariffa]) $numpersone = $persone_tariffa[$numtariffa];
if (!$numpersone) {
$regola4 = esegui_query("select * from $tableregole where tariffa_per_persone = '$tariffa'");
if (numlin_query($regola4) == 1) {
$numpersone = risul_query($regola4,0,'iddatainizio');
$num_pers_regola4[$numtariffa] = $numpersone;
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if ($dati_app['maxocc'][$numapp] and $dati_app['maxocc'][$numapp] < $num_pers_regola4[$numtariffa]) $app_richiesti_corr[$numapp] = "NO";
} # fine for $num1
} # fine if (numlin_query($regola4) == 1)
} # fine if (!$numpersone)
if ($controllato_con_costo_letto and !$persone_tariffa_r2[$numtariffa]) {
$costo_agg_letti_vett[$numtariffa] = $costo_aggiungi_letti;
$numpersone = $numpersone - $num_aggiungi_letti;
$persone_tariffa[$numtariffa] = $numpersone;
} # fine if ($controllato_con_costo_letto and !$persone_tariffa_r2[$numtariffa])

if (!$controlla_tariffe or $mostra_quadro_disp == "reg2") {
if ($persone_tariffa_r2[$numtariffa]) $app_richiesti_con_cal_corr = $app_richiesti_r2[$tipologia_r2];
else $app_richiesti_con_cal_corr = $app_richiesti_con_cal[1];
$appartamenti_regola2 = dati_regole2($dati_r2,$app_regola2_predef,$tariffa,$idinizioperiodo,$idfineperiodo,$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);

# Se ci sono regole2 predefinite (siamo in eccezione alla regola2) controllo che ci siano app compatibili con la regola originaria
if ($app_regola2_predef) {
$app_regola2_predef = explode(",",$app_regola2_predef);
$app_compatibile_trovato = "NO";
for ($num1 = 0 ; $num1 < count($app_regola2_predef) ; $num1++) {
if ($app_richiesti_corr[$app_regola2_predef[$num1]] == "SI") $app_compatibile_trovato = "SI";
} # fine for $num1
if ($app_compatibile_trovato == "NO") {
# Se non ci sono app compatibili provo ad aggiungere il costo per il letto aggiuntivo
if ((($controlla_con_costo_letto and !$persone_tariffa_r2[$numtariffa]) or $controlla_con_costo_letto_r2[$tipologia_r2]) and !$controllato_con_costo_letto and $dati_ca[$dati_ca['id'][$costo_aggiungi_letti]]["incomp_tariffa".$numtariffa] != "i") {
$costo_agg_letti_vett[$numtariffa] = $costo_aggiungi_letti;
$numpersone = $numpersone - $num_aggiungi_letti;
$persone_tariffa[$numtariffa] = $numpersone;
if ($app_beniinv_cal) $nrc = "";
for ($num1 = 0 ; $num1 < count($app_regola2_predef) ; $num1++) {
$numapp = $app_regola2_predef[$num1];
if ($app_beniinv_cal) $risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca_cal,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,$numapp);
else $risul_beniinv = "SI";
if (!strstr(",$app_incomp_cal,",",$numapp,") and $risul_beniinv == "SI") {
if (strstr(",$app_richiesti_con_cal_corr,",",$numapp,") or $app_richiesti_con_cal_corr == ",tutti,") {
$app_compatibile_trovato = "SI";
if (!$minoccupanti_con_cal_vett[$numtariffa] or $minoccupanti_con_cal_vett[$numtariffa] > $dati_app['maxocc'][$numapp]) $minoccupanti_con_cal_vett[$numtariffa] = $dati_app['maxocc'][$numapp];
} # fine if (strstr(",$app_richiesti_con_cal_corr,",",$numapp,") or $app_richiesti_con_cal_corr == ",tutti,")
} # fine if (!strstr(",$app_incomp_cal,",",$numapp,") and $risul_beniinv == "SI")
} # fine for $num1
} # fine if ((($controlla_con_costo_letto and !$persone_tariffa_r2[$numtariffa]) or $controlla_con_costo_letto_r2[$tipologia_r2]) and !$controllato_con_costo_letto and...
if ($app_compatibile_trovato == "NO") $continuare_vett[$numtariffa] = "NO";
} # fine if ($app_compatibile_trovato == "NO")
} # fine if ($app_regola2_predef)

# Controllo sulla regola2 effettiva
if (!strcmp($appartamenti_regola2,"")) $appartamenti_regola2 = substr($dati_app['lista'],1,-1);
if ($appartamenti_regola2) {
$app_compatibile_trovato = "NO";
$app_regola2_vett[$numtariffa] = $appartamenti_regola2;
$app_regola2_orig[$numtariffa] = $app_regola2_vett[$numtariffa];
$appartamenti_regola2 = explode(",",$app_regola2_vett[$numtariffa]);
$num_app_reg2_vett[$numtariffa] = count($appartamenti_regola2);
for ($num1 = 0 ; $num1 < count($appartamenti_regola2) ; $num1++) {
if ($app_richiesti_corr[$appartamenti_regola2[$num1]] == "SI") $app_compatibile_trovato = "SI";
else {
$app_regola2_vett[$numtariffa] = substr(str_replace(",".$appartamenti_regola2[$num1].",",",",",".$app_regola2_vett[$numtariffa].","),1,-1);
$num_app_reg2_vett[$numtariffa]--;
} # fine else if ($app_richiesti_corr[$appartamenti_regola2[$num1]] == "SI")
} # fine for $num1
if ($app_compatibile_trovato == "NO") {
# Se non ci sono app compatibili provo ad aggiungere il costo per il letto aggiuntivo
if ((($controlla_con_costo_letto and !$persone_tariffa_r2[$numtariffa]) or $controlla_con_costo_letto_r2[$tipologia_r2]) and !$controllato_con_costo_letto and $dati_ca[$dati_ca['id'][$costo_aggiungi_letti]]["incomp_tariffa".$numtariffa] != "i") {
if (!$costo_agg_letti_vett[$numtariffa]) {
$costo_agg_letti_vett[$numtariffa] = $costo_aggiungi_letti;
$numpersone = $numpersone - $num_aggiungi_letti;
$persone_tariffa[$numtariffa] = $numpersone;
} # fine if (!$costo_agg_letti_vett[$numtariffa])
$app_regola2_vett[$numtariffa] = $app_regola2_orig[$numtariffa];
$num_app_reg2_vett[$numtariffa] = count($appartamenti_regola2);
if ($app_beniinv_cal) $nrc = "";
for ($num1 = 0 ; $num1 < count($appartamenti_regola2) ; $num1++) {
$numapp = $appartamenti_regola2[$num1];
if ($app_beniinv_cal) $risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca_cal,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,$numapp);
else $risul_beniinv = "SI";
if ((strstr(",$app_richiesti_con_cal_corr,",",$numapp,") or $app_richiesti_con_cal_corr == ",tutti,") and str_replace(",$numapp,","",",$app_incomp_cal,") == ",$app_incomp_cal," and $risul_beniinv == "SI") {
$app_compatibile_trovato = "SI";
if (!$minoccupanti_con_cal_vett[$numtariffa] or $minoccupanti_con_cal_vett[$numtariffa] > $dati_app['maxocc'][$numapp]) $minoccupanti_con_cal_vett[$numtariffa] = $dati_app['maxocc'][$numapp];
} # fine if ((strstr(",$app_richiesti_con_cal_corr,",",$numapp,") or $app_richiesti_con_cal_corr == ",tutti,") and...
else {
$app_regola2_vett[$numtariffa] = substr(str_replace(",".$numapp.",",",",",".$app_regola2_vett[$numtariffa].","),1,-1);
$num_app_reg2_vett[$numtariffa]--;
} # fine else if (str_replace(",$numapp,","",",".$app_richiesti_con_cal[1].",") != ",".$app_richiesti_con_cal[1]."," or...
} # fine for $num1
} # fine if ((($controlla_con_costo_letto and !$persone_tariffa_r2[$numtariffa]) or $controlla_con_costo_letto_r2[$tipologia_r2]) and !$controllato_con_costo_letto and...
if ($app_compatibile_trovato == "NO") $continuare_vett[$numtariffa] = "NO";
} # fine if ($app_compatibile_trovato == "NO")
$app_regola2_orig['napp'][$numtariffa] = $dati_r2['napp']["tariffa".$numtariffa];
} # fine if ($appartamenti_regola2)

} # fine if (!$controlla_tariffe or $mostra_quadro_disp == "reg2")

$costo_tariffa_vett[$numtariffa] = 0;
$tariffesettimanali_vett[$numtariffa] = "";
$tariffesettimanalip_vett[$numtariffa] = "";
for ($num1 = $idinizioperiodo ; $num1 <= $idfineperiodo ; $num1++) {
if (!$rigasettimana[$num1]) $rigasettimana[$num1] = esegui_query("select * from $tableperiodi where idperiodi = '$num1' ");
$esistetariffa = risul_query($rigasettimana[$num1],0,$tariffa);
$esistetariffap = risul_query($rigasettimana[$num1],0,$tariffa."p");
if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and (!strcmp($esistetariffap,"") or $esistetariffap < 0)) {
$continuare_vett[$numtariffa] = "NO";
break;
} # fine if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and...
else {
$costo_tariffa_settimana = risul_query($rigasettimana[$num1],0,$tariffa);
$costo_tariffap_sett = risul_query($rigasettimana[$num1],0,$tariffa."p");
if (!strcmp($costo_tariffa_settimana,"")) $costo_tariffa_settimana = 0;
if (!strcmp($costo_tariffap_sett,"")) $costo_tariffap_sett = 0;
$costo_tariffap_settimana = (double) $costo_tariffap_sett * (double) $numpersone;
$costo_tariffa_settimana_tot = $costo_tariffa_settimana + $costo_tariffap_settimana;
$costo_tariffa_vett[$numtariffa] = $costo_tariffa_vett[$numtariffa] + $costo_tariffa_settimana_tot;
$tariffesettimanali_vett[$numtariffa] .= ",".$costo_tariffa_settimana_tot;
if ($dati_tariffe[$tariffa]['moltiplica'] == "p") $tariffesettimanalip_vett[$numtariffa] .= ",".$costo_tariffap_settimana;
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$costo_tariffap_settimana_alt = (double) $costo_tariffap_sett * (double) ($numpersone - 1);
$costo_tariffa_settimana_tot_alt = $costo_tariffa_settimana + $costo_tariffap_settimana_alt;
for ($num2 = (($numpersone * $dati_r2['napp']["tariffa".$numtariffa]) - $numpersone_orig_tar) ; $num2 > 0 ; $num2--) {
$nr = $dati_r2['napp']["tariffa".$numtariffa] - $num2;
$costo_tariffa_nr[$numtariffa][$nr] += $costo_tariffa_settimana_tot_alt;
if (strcmp($tariffesettimanali_nr[$numtariffa][$nr],"")) $tariffesettimanali_nr[$numtariffa][$nr] .= ",";
$tariffesettimanali_nr[$numtariffa][$nr] .= $costo_tariffa_settimana_tot_alt;
if ($dati_tariffe[$tariffa]['moltiplica'] == "p") {
if (strcmp($tariffesettimanalip_nr[$numtariffa][$nr],"")) $tariffesettimanalip_nr[$numtariffa][$nr] .= ",";
$tariffesettimanalip_nr[$numtariffa][$nr] .= $costo_tariffap_settimana_alt;
} # fine if ($dati_tariffe[$tariffa]['moltiplica'] == "p")
if ($mostra_tariffa[$numtariffa] > 1 and $num1 == $idfineperiodo) {
for ($num3 = 1 ; $num3 < $mostra_tariffa[$numtariffa] ; $num3++) {
$costo_tariffa_nr[$numtariffa][($nr + ($dati_r2['napp']["tariffa".$numtariffa] * $num3))] = $costo_tariffa_nr[$numtariffa][$nr];
$tariffesettimanali_nr[$numtariffa][($nr + ($dati_r2['napp']["tariffa".$numtariffa] * $num3))] = $tariffesettimanali_nr[$numtariffa][$nr];
if ($tariffesettimanalip_nr[$numtariffa][$nr]) $tariffesettimanalip_nr[$numtariffa][($nr + ($dati_r2['napp']["tariffa".$numtariffa] * $num3))] = $tariffesettimanalip_nr[$numtariffa][$nr];
} # fine for $num3
} # fine if ($mostra_tariffa[$numtariffa] > 1 and $num1 == $idfineperiodo)
} # fine for $num2
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
} # fine else if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and...
if ($dati_tariffe[$tariffa]['chiusa'][$num1]) {
$continuare_vett[$numtariffa] = "NO";
break;
} # fine if ($dati_tariffe[$tariffa]['chiusa'][$num1])
} # fine for $num1
$tariffesettimanali_vett[$numtariffa] = substr($tariffesettimanali_vett[$numtariffa],1);
$tariffesettimanalip_vett[$numtariffa] = substr($tariffesettimanalip_vett[$numtariffa],1);

} # fine if ($continuare_vett[$numtariffa] != "NO")
} # fine for $numtariffa



# Se non sono richieste tariffe specifiche controllo per ognuna se ci sono appartamenti liberi
$tariffa_non_mostrata = 0;
unset($tariffa_non_disp);
unset($tariffa_occupa_reg1);
if (!$controlla_tariffe) {
unset($app_liberato_vett);
$num_tariffe_contr = 0;
unset($tariffe_contr);
$app_gia_liberati[0] = $app_liberato_vett;
if (@is_array($num_app_reg2_vett)) {
asort ($num_app_reg2_vett);
reset ($num_app_reg2_vett);
foreach ($num_app_reg2_vett as $numtariffa => $val) {
if ($continuare_vett[$numtariffa] != "NO") {
$controllare = "SI";
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) $num_apti_corr = $dati_r2['napp']["tariffa".$numtariffa];
else $num_apti_corr = 1;
if ($num_apti_corr == 1) {
for ($num1 = 0 ; $num1 <= $num_tariffe_contr ; $num1++) {
if ($continuare_vett[$tariffe_contr[$num1]] != "NO") {
if (str_replace(",".$app_gia_liberati[$num1].",","",",".$app_regola2_vett[$numtariffa].",") != ",".$app_regola2_vett[$numtariffa].",") $controllare = "NO";
} # fine if ($continuare_vett[$tariffe_contr[$num1]] != "NO")
} # fine for $num1
} # fine if ($num_apti_corr == 1)
for ($num1 = 1 ; $num1 <= $num_tariffe_contr ; $num1++) {
if ($dati_r2['napp']["tariffa".$tariffe_contr[$num1]] > 1) $num_apti_nt = $dati_r2['napp']["tariffa".$tariffe_contr[$num1]];
else $num_apti_nt = 1;
if ($num_apti_corr <= $num_apti_nt) {
if ($app_regola2_vett[$tariffe_contr[$num1]] == $app_regola2_vett[$numtariffa]) {
$continuare_vett[$numtariffa] = $continuare_vett[$tariffe_contr[$num1]];
$tariffa_non_disp[$numtariffa] = $tariffa_non_disp[$tariffe_contr[$num1]];
$tariffa_occupa_reg1[$numtariffa] = $tariffa_occupa_reg1[$tariffe_contr[$num1]];
$controllare = "NO";
} # fine if ($app_regola2_vett[$tariffe_contr[$num1]] == $app_regola2_vett[$numtariffa])
else {
# se vi è una lista precedente contenuta nella attuale che è libera, allora anche questa è libera
if (!$tariffa_non_disp[$tariffe_contr[$num1]] and !$tariffa_occupa_reg1[$tariffe_contr[$num1]]) {
$contenuto = "SI";
for ($num2 = 0 ; $num2 < $num_ar_prec[$tariffe_contr[$num1]] ; $num2++) {
if (str_replace(",".$ar_vett_prec[$tariffe_contr[$num1]][$num2].",","",",".$app_regola2_vett[$numtariffa].",") == ",".$app_regola2_vett[$numtariffa].",") {
$contenuto = "NO";
break;
} # fine if (str_replace(",".$ar_vett_prec[$tariffe_contr[$num1]][$num2].",","",",".$app_regola2_vett[$numtariffa].",") == ",".$app_regola2_vett[$numtariffa].",")
} # fine for $num2
if ($contenuto == "SI") $controllare = "NO";
} # fine if (!$tariffa_non_disp[$tariffe_contr[$num1]] and !$tariffa_occupa_reg1[$tariffe_contr[$num1]])
} # fine else if ($app_regola2_vett[$tariffe_contr[$num1]] == $app_regola2_vett[$numtariffa])
} # fine if ($num_apti_corr <= $num_apti_nt)
} # fine for $num1
if ($controllare == "SI") {
$num_tariffe_contr++;
$tariffe_contr[$num_tariffe_contr] = $numtariffa;
$ar_vett_prec[$numtariffa] = explode(",",$app_regola2_vett[$numtariffa]);
$num_ar_prec[$numtariffa] = count($ar_vett_prec[$numtariffa]);
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
$idinizioperiodo_vett[1] = $idinizioperiodo;
$idfineperiodo_vett[1] = $idfineperiodo;
unset($app_richiesti);
$app_richiesti[',numero,'] = 1;
$app_richiesti[1] = $app_regola2_vett[$numtariffa];
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$app_richiesti[',numero,'] = $dati_r2['napp']["tariffa".$numtariffa];
for ($num1 = 2 ; $num1 <= $app_richiesti[',numero,'] ; $num1++) {
$app_richiesti[$num1] = $app_richiesti[1];
$idinizioperiodo_vett[$num1] = $idinizioperiodo;
$idfineperiodo_vett[$num1] = $idfineperiodo;
} # fine for $num1
if ($dati_r2['napp']['v']["tariffa".$numtariffa]) $app_richiesti[',vicini,'] = "SI";
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
$fatto_libera = "";
$tariffa_occupa_reg1[$numtariffa] = 0;
if ($num_app_agenzia != 0 and $info_periodi_ag['numero']) {
$limiti_var2['t_limite'] = (time() + $sec_limite_libsett);
$app_prenota_id2 = $app_orig_prenota_id2;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var2,$anno,$fatto_libera,$app_liberato_vett,$profondita2,$app_richiesti,$app_prenota_id2,$app_orig_prenota_id2,$inizio_prenota_id2,$fine_prenota_id2,$app_assegnabili_id2,$prenota_in_app_sett2,$dati_app,$PHPR_TAB_PRE."prenota");
if ($fatto_libera != "SI") $tariffa_occupa_reg1[$numtariffa] = 1;
} # fine if ($num_app_agenzia != 0 and $info_periodi_ag['numero'])
if ($fatto_libera != "SI") {
$limiti_var['t_limite'] = (time() + $sec_limite_libsett);
$app_prenota_id = $app_orig_prenota_id;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera,$app_liberato_vett,$profondita,$app_richiesti,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");
} # fine if ($fatto_libera != "SI")
if ($fatto_libera != "SI") {
if (!$mostra_non_disp) {
$continuare_vett[$numtariffa] = "NO";
$tariffa_non_mostrata = 1;
} # fine if (!$mostra_non_disp)
$tariffa_non_disp[$numtariffa] = 1;
} # fine if ($fatto_libera != "SI")
elseif (!$tariffa_occupa_reg1[$numtariffa]) $app_gia_liberati[$num_tariffe_contr] = $app_liberato_vett[1];
} # fine if ($controllare == "SI")
} # fine if ($continuare_vett[$numtariffa] != "NO")
} # fine foreach ($num_app_reg2_vett as $numtariffa => $val)
} # fine if (@is_array($num_app_reg2_vett))
} # fine if (!$controlla_tariffe)


# Genero la lista delle tariffe
$continuare_totale = "SI";
$num_ripeti_contr = 0;
$caparra_totale = (double) 0;
$tariffa_mostrata = 0;

for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa = $numtariffa + 1) {
if ($continuare_totale != "NO") {

$tariffa = "tariffa".$numtariffa;
$nometariffa = $dati_tariffe[$tariffa]['nome'];
$nome_tariffa[$numtariffa] = $nometariffa;
if ($nometariffa == "") {
$nometariffa = $numtariffa;
$nome_tariffa[$numtariffa] = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
if ($numpersone_orig_nt[$numtariffa]) $numpersone_orig_tar = $numpersone_orig_nt[$numtariffa];
else $numpersone_orig_tar = $numpersone_orig;
if (!$numpersone_orig_tar) $numpersone_orig_tar = 0;
$numpersone = $numpersone_orig_tar;
if ($persone_tariffa[$numtariffa]) $numpersone = $persone_tariffa[$numtariffa];
if (!$numpersone and $num_pers_regola4[$numtariffa]) $numpersone = $num_pers_regola4[$numtariffa];
if ($costo_agg_letti_vett[$numtariffa] and $minoccupanti_con_cal_vett[$numtariffa] > $numpersone) $numpersone = $minoccupanti_con_cal_vett[$numtariffa];
$num_agg_letti = $numpersone_orig_tar - $numpersone;
$diff_letti = 0;
if ($continuare_vett[$numtariffa] == "NO") $continuare = "NO";
else {
$continuare = "SI";
$tariffesettimanali = $tariffesettimanali_vett[$numtariffa];
if (strcmp($tariffesettimanalip_vett[$numtariffa],"")) $tariffesettimanali .= ";".$tariffesettimanalip_vett[$numtariffa];
$costo_tariffa = $costo_tariffa_vett[$numtariffa];
} # fine else if ($continuare_vett[$numtariffa] == "NO")
if ($controlla_tariffe and !$mostra_tariffa[$numtariffa]) $continuare = "NO";

if ($continuare == "SI") {
$caparra = calcola_caparra($dati_tariffe,'tariffa'.$numtariffa,$idinizioperiodo,$idfineperiodo,$costo_tariffa,$tariffesettimanali);

$num_controlla_limite = 1;
if ($mostra_tariffa[$numtariffa] > 1) $num_controlla_limite = $mostra_tariffa[$numtariffa];
$numpersone_nr = array();
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$num_controlla_limite = $num_controlla_limite * $dati_r2['napp']["tariffa".$numtariffa];
if ($costo_agg_letti_vett[$numtariffa]) {
$num_agg_letti = ceil((double) ($numpersone_orig_tar - ($numpersone * $dati_r2['napp']["tariffa".$numtariffa])) / (double) $dati_r2['napp']["tariffa".$numtariffa]);
$diff_letti = ((($numpersone + $num_agg_letti) * $dati_r2['napp']["tariffa".$numtariffa]) - $numpersone_orig_tar);
if ($mostra_tariffa[$numtariffa] > 1) $diff_letti = $mostra_tariffa[$numtariffa] * $diff_letti;
} # fine if ($costo_agg_letti_vett[$numtariffa])
else {
for ($num1 = ($dati_r2['napp']["tariffa".$numtariffa] - ($numpersone * $dati_r2['napp']["tariffa".$numtariffa]) + $numpersone_orig_tar) ; $num1 < $dati_r2['napp']["tariffa".$numtariffa] ; $num1++) {
$numpersone_nr[$num1] = $numpersone - 1;
if ($mostra_tariffa[$numtariffa] > 1) {
for ($num2 = 1 ; $num2 < $mostra_tariffa[$numtariffa] ; $num2++) $numpersone_nr[($num1 + ($dati_r2['napp']["tariffa".$numtariffa] * $num2))] = $numpersone - 1;
} # fine if ($mostra_tariffa[$numtariffa] > 1)
} # fine for $num1
} # fine else if ($costo_agg_letti_vett[$numtariffa])
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
$lunghezza_periodo = $idfineperiodo - $idinizioperiodo + 1;


# costi aggiuntivi
$costi_agg_tot = (double) 0;
$costo_escludi_perc = (double) 0;
unset($num_letti_agg);
unset($settimane_costo);
unset($ca_associato);
unset($moltiplica_costo);
unset($moltiplica_costo_nr);
unset($num_letti_agg_nr);
unset($num_costi_presenti);
unset($num_ripetizioni_costo);
unset($num_app_reali_costo);
$asterisco = "NO";
$dett_costi = "";
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$numca]['id']] == "SI") {
if ($dati_ca[$numca]["tipo_associa_".$tariffa] == "r") $periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,1);
if ($dati_ca[$numca]["tipo_associa_".$tariffa] == "s" or ($dati_ca[$numca]["tipo_associa_".$tariffa] == "r" and $periodo_costo_trovato != "NO")) {
if (associa_costo_a_tariffa($dati_ca,$numca,$tariffa,$lunghezza_periodo) == "SI") $ca_associato[$numca] = "SI";
else {
if ($dati_ca[$numca]["tipo_associa_".$tariffa] == "r" and $dati_ca[$numca]['tipo'] == "s") {
$sett_costo = calcola_settimane_costo($tableperiodi,$dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"","");
if ($sett_costo) $continuare = "NO";
} # fine if ($dati_ca[$numca]["tipo_associa_".$tariffa] == "r" and...
else $continuare = "NO";
} # fine else if (associa_costo_a_tariffa($dati_ca,$numca,$tariffa,$lunghezza_periodo) == "SI")
} # fine if ($dati_ca[$numca]["tipo_associa_".$tariffa] == "s" or...
if ($dati_ca[$numca]['mostra'] == "s") {
$numcostoagg = "";
for ($num1 = 1 ; $num1 <= $numcostiagg ; $num1++) if (${"idcostoagg".$num1} == $dati_ca[$numca]['id']) $numcostoagg = $num1;
if ($numcostoagg) {
if (${"costoagg".$numcostoagg} == "SI") {
$ca_associato[$numca] = "SI";
$ca_associato['nca'][$numca] = $numcostoagg;
$costi_non_fissi = "SI";
} # fine if (${"costoagg".$numcostoagg} == "SI")
} # fine if ($numcostoagg)
if ($dati_ca[$numca]['id'] == $costo_agg_letti_vett[$numtariffa]) {
$numcostoagg = $numcostiagg + 1;
${"idcostoagg".$numcostoagg} = $dati_ca[$numca]['id'];
${"nummoltiplica_ca".$numcostoagg} = $num_agg_letti;
${"numsettimane".$numcostoagg} = $lunghezza_periodo;
$ca_associato[$numca] = "SI";
$ca_associato['nca'][$numca] = $numcostoagg;
if ($diff_letti) $num_app_reali_costo[$numca] = $num_controlla_limite - $diff_letti;
} # fine if ($dati_ca[$numca]['id'] == $costo_agg_letti_vett[$numtariffa])
} # fine if ($dati_ca[$num_costo][mostra] == "s")
else $numcostoagg = "NO";

if ($ca_associato[$numca] == "SI") {
$continuare_comb = "SI";
$numsettimane = "numsettimane".$numcostoagg;
$nummoltiplica_ca = "nummoltiplica_ca".$numcostoagg;
$id_periodi_costo = "id_periodi_costo".$numcostoagg;
if ($dati_ca[$numca]["incomp_".$tariffa] == "i") {
if ($dati_ca[$numca]['combina'] == "s") $continuare_comb = "NO";
else $continuare = "NO";
} # fine if ($dati_ca[$numca]["incomp_".$tariffa] == "i")
if (!$$numsettimane and $dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] != "s") $continuare = "NO";
$numsettimane_aux = $$numsettimane;
$nummoltiplica_ca_aux = $$nummoltiplica_ca;
if ($$numsettimane and ($$numsettimane > $lunghezza_periodo or controlla_num_pos($numsettimane_aux) == "NO")) $continuare = "NO";
if ($$nummoltiplica_ca and controlla_num_pos($nummoltiplica_ca_aux) == "NO") $continuare = "NO";
if (trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"") == "NO") {
if ($dati_ca[$numca]['combina'] == "s") $continuare_comb = "NO";
else $continuare = "NO";
} # fine if (trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"") == "NO")

if ($$id_periodi_costo == "inserire") {
$$id_periodi_costo = "";
for ($num1 = $idinizioperiodo ; $num1 <= $idfineperiodo ; $num1++) {
if (${"sett".$num1."costo".$numcostoagg} == "SI") $$id_periodi_costo .= ",".$num1;
} # fine for $num1
if ($$id_periodi_costo) $$id_periodi_costo .= ",";
else $$id_periodi_costo = "nessuno";
} # fine if ($$id_periodi_costo == "inserire")
if ($dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] == "s" and $continuare != "NO" and $continuare_comb != "NO" and !$$id_periodi_costo) {
$continuare_totale = "NO";
echo "<tr><td></td></tr></table></div></table></div></form><hr style=\"width: 30%; margin-left: 0; text-align: left;\">
".mex("Scegliere $parola_le $parola_settimane in cui applicare il costo aggiuntivo",$pag)." ".$dati_ca[$numca]['nome'].":<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"num_persone_casa\" value=\"$num_persone_casa\">
<input type=\"hidden\" name=\"molt_app_persone_casa\" value=\"$molt_app_persone_casa\">
<input type=\"hidden\" name=\"numpersone\" value=\"$numpersone_orig\">
$dati_email
<input type=\"hidden\" name=\"numcostiagg\" value=\"$numcostiagg\">";
if ($controlla_tariffe) {
echo "<input type=\"hidden\" name=\"controlla_tariffe\" value=\"$controlla_tariffe\">";
for ($numtariffa2 = 1 ; $numtariffa2 <= $dati_tariffe['num'] ; $numtariffa2++) {
echo "<input type=\"hidden\" name=\"reg2_tariffa$numtariffa2\" value=\"".${"reg2_tariffa".$numtariffa2}."\">
<input type=\"hidden\" name=\"molt_reg2_tariffa$numtariffa2\" value=\"".${"molt_reg2_tariffa".$numtariffa2}."\">
<input type=\"hidden\" name=\"pers_reg2_tariffa$numtariffa2\" value=\"".${"pers_reg2_tariffa".$numtariffa2}."\">";
} # fine for $numtariffa2
} # fine if ($controlla_tariffe)
for ($num1 = $idinizioperiodo ; $num1 <= $idfineperiodo ; $num1++) {
$periodo_costo_trovato = "NO";
if ($dati_ca[$numca]['periodipermessi'] == "p") {
for ($num2 = 0 ; $num2 < count($dati_ca[$numca]['sett_periodipermessi_ini']) ; $num2++) {
if ($dati_ca[$numca]['sett_periodipermessi_ini'][$num2] <= $num1 and $dati_ca[$numca]['sett_periodipermessi_fine'][$num2] >= $num1) $periodo_costo_trovato = "SI";
} # fine for $num2
} # fine if ($dati_ca[$num_costo]['periodipermessi'] == "p")
else $periodo_costo_trovato = "SI";
if ($periodo_costo_trovato == "SI") {
$date_sett_costo = esegui_query("select datainizio,datafine from $tableperiodi where idperiodi = '$num1'");
echo "<label><input type=\"checkbox\" name=\"sett$num1"."costo$numcostoagg\" value=\"SI\">".mex("dal",$pag)."
 ".formatta_data(risul_query($date_sett_costo,0,'datainizio'),$stile_data)." ".mex("al",$pag)." 
 ".formatta_data(risul_query($date_sett_costo,0,'datafine'),$stile_data)."</label><br>";
} # fine if ($periodo_costo_trovato == "SI")
} # fine for $num1
$$id_periodi_costo = "inserire";
for ($numca2 = 1 ; $numca2 <= $numcostiagg ; $numca2++) {
$idcostoagg_2 = "idcostoagg".$numca2;
$num_costo2 = $dati_ca['id'][$$idcostoagg_2];
if ($dati_ca[$num_costo2]['mostra'] == "s") {
echo "<input type=\"hidden\" name=\"id_periodi_costo$numca2\" value=\"".${"id_periodi_costo".$numca2}."\">
<input type=\"hidden\" name=\"costoagg$numca2\" value=\"".${"costoagg".$numca2}."\">
<input type=\"hidden\" name=\"idcostoagg$numca2\" value=\"".$$idcostoagg_2."\">
<input type=\"hidden\" name=\"numsettimane$numca2\" value=\"".${"numsettimane".$numca2}."\">
<input type=\"hidden\" name=\"nummoltiplica_ca$numca2\" value=\"".${"nummoltiplica_ca".$numca2}."\">";
} # fine if ($dati_ca[$num_costo2]['mostra'] == "s")
} # fine for $numca2
echo "<button class=\"cont\" type=\"submit\"><div>".mex("Continua",$pag)."</div></button>
</div></form><hr style=\"width: 30%; margin-left: 0; text-align: left;\">";
} # fine if ($dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] == "s" and...
else {
$id_periodi_costo_aux = $$id_periodi_costo;
$numsettimane_aux = $$numsettimane;
$nummoltiplica_ca_aux = $$nummoltiplica_ca;
$settimane_costo[$numca] = calcola_settimane_costo($tableperiodi,$dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$id_periodi_costo_aux,$numsettimane_aux);
if ($continuare != "NO" and $continuare_comb != "NO") {
aggiorna_letti_agg_in_periodi($dati_ca,$numca,$num_letti_agg,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],"",$nummoltiplica_ca_aux,$numpersone);
$num_letti_agg_nr[0] = $num_letti_agg;
if ($num_app_reali_costo[$numca]) $num_controlla_limite2 = $num_app_reali_costo[$numca];
else $num_controlla_limite2 = $num_controlla_limite;
for ($num1 = 1 ; $num1 < $num_controlla_limite2 ; $num1++) {
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
aggiorna_letti_agg_in_periodi($dati_ca,$numca,$num_letti_agg_nr[$num1],$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],"",$nummoltiplica_ca_aux,$numpersone_corr);
} # fine for $num1
} # fine if ($continuare != "NO" and $continuare_comb != "NO")
} # fine else if ($dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] == "s" and...

if (($dati_ca[$numca]['moltiplica'] == "p" or $dati_ca[$numca]['moltiplica'] == "t") and !$numpersone) $continuare = "NO";
if ($dati_ca[$numca]['mostra'] == "s" and ($continuare == "NO" or $continuare_comb == "NO") and $dati_ca[$numca]['id'] != $costo_agg_letti_vett[$numtariffa]) {
$ca_associato[$numca] = "";
if ($continuare == "NO") $asterisco = "SI";
$continuare = "SI";
} # fine if ($dati_ca[$numca]['mostra'] == "s" and $continuare == "NO" and...
if ($continuare_totale == "NO") $continuare = "NO";
} # fine if ($ca_associato[$numca] == "SI")
if ($continuare == "NO") break;
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine for $numca

if ($continuare == "SI") {
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($ca_associato[$numca] == "SI") {
$nummoltiplica_ca_aux = ${"nummoltiplica_ca".$ca_associato['nca'][$numca]};
calcola_moltiplica_costo($dati_ca,$numca,$moltiplica_costo[$numca],$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$nummoltiplica_ca_aux,$numpersone,$num_letti_agg);
if ($num_app_reali_costo[$numca]) $num_controlla_limite2 = $num_app_reali_costo[$numca];
else $num_controlla_limite2 = $num_controlla_limite;
for ($num1 = 0 ; $num1 < $num_controlla_limite2 ; $num1++) {
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
calcola_moltiplica_costo($dati_ca,$numca,$moltiplica_costo_nr[$numca][$num1],$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$nummoltiplica_ca_aux,$numpersone_corr,$num_letti_agg_nr[$num1]);
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo_nr[$numca][$num1]) == "NO") $continuare = "NO";
} # fine for $num1
if ($dati_ca[$numca]['tipo_beniinv'] == "mag") {
for ($num1 = 0 ; $num1 < $num_controlla_limite2 ; $num1++) {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca,$beniinv_presenti,$num_ripetizioni_costo[$numca],"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo_nr[$numca][$num1],"");
if ($risul != "SI") { $continuare = "NO"; break; }
} # fine for $num1
} # fine if ($dati_ca[$num1]['tipo_beniinv'] == "mag")
if ($dati_ca[$numca]['moltiplica'] == "c" and $dati_ca[$numca]['molt_max'] != "x") {
$num_max = 0;
if ($dati_ca[$numca]['molt_max'] == "n") $num_max = $dati_ca[$numca]['molt_max_num'];
if ($dati_ca[$numca]['molt_max'] != "n" and $numpersone) $num_max = $numpersone;
if ($dati_ca[$numca]['molt_max'] == "t" and $num_letti_agg['max']) $num_max += $num_letti_agg['max'];
if ($num_max) {
if ($dati_ca[$numca]['molt_max'] != "n" and $dati_ca[$numca]['molt_max_num']) $num_max = $num_max - $dati_ca[$numca]['molt_max_num'];
if ($nummoltiplica_ca_aux > $num_max) $continuare = "NO";
} # fine if ($num_max)
} # fine if ($dati_ca[$numca]['moltiplica'] == "c" and $dati_ca[$num1]['molt_max'] != "x")
if ($dati_ca[$numca]['mostra'] == "s" and $continuare == "NO") {
$continuare = "SI";
$ca_associato[$numca] = "";
$asterisco = "SI";
} # fine if ($dati_ca[$numca]['mostra'] == "s" and $continuare == "NO")
if ($continuare == "NO") break;
} # fine if ($ca_associato[$numca] == "SI")
} # fine for $numca
} # fine if ($continuare == "SI")


if ($continuare == "SI") {
$tariffa_mostrata = 1;

# costi opzionali associabili se possibile
$oggi_costo = date("Ymd",(time() + (C_DIFF_ORE * 3600)));
for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num_costo]['id']] == "SI") {
$associa_costo = "NO";
$associa_costo_tariffa = associa_costo_a_tariffa($dati_ca,$num_costo,$tariffa,$lunghezza_periodo);
if ($associa_costo_tariffa == "SI" and $dati_ca[$num_costo]["tipo_associa_".$tariffa] == "p") $associa_costo = "SI";
if ($associa_costo_tariffa != "SI" and !$dati_ca[$num_costo]["incomp_".$tariffa]) {
if ($dati_ca[$num_costo]['assegna_con_num_prenota'] and $num_prenota_tot >= $dati_ca[$num_costo]['assegna_con_num_prenota']) $associa_costo = "SI";
if ($dati_ca[$num_costo]['assegna_da_ini_prenota']) {
$giorni_lim = substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],1);
$limite = date("Ymd",mktime(0,0,0,substr($data_inizioperiodo,5,2),(substr($data_inizioperiodo,8,2) - $giorni_lim),substr($data_inizioperiodo,0,4)));
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) == ">" and $oggi_costo < $limite) $associa_costo = "SI";
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) == "<" and $oggi_costo > $limite) $associa_costo = "SI";
} # fine if ($dati_ca[$num_costo][assegna_da_ini_prenota])
} # fine if ($associa_costo_tariffa != "SI" and...
if ($associa_costo == "SI") {
$settimane_costo2 = calcola_settimane_costo($tableperiodi,$dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,"","");
$num_letti_agg_copia = $num_letti_agg_nr;
$beniinv_presenti_copia = $beniinv_presenti;
$num_app_reali_costo2 = "";

if ($dati_ca[$num_costo]['letto'] == "s") {
for ($num1 = 0 ; $num1 < $num_controlla_limite ; $num1++) {
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
aggiorna_letti_agg_in_periodi($dati_ca,$num_costo,$num_letti_agg_copia[$num1],$idinizioperiodo,$idfineperiodo,$settimane_costo2,"","",$numpersone_corr);
} # fine for $num1
unset($moltiplica_copia);
unset($num_costi_presenti_copia);
unset($num_ripetizioni_copia);
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($ca_associato[$numca] == "SI") {
if ($num_app_reali_costo[$numca]) $num_controlla_limite2 = $num_app_reali_costo[$numca];
else $num_controlla_limite2 = $num_controlla_limite;
for ($num1 = 0 ; $num1 < $num_controlla_limite2 ; $num1++) {
if ($dati_ca[$numca]['moltiplica'] != "t") $moltiplica_copia[$numca][$num1] = $moltiplica_costo_nr[$numca][$num1];
else {
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
calcola_moltiplica_costo($dati_ca,$numca,$moltiplica_copia[$numca][$num1],$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],"",$numpersone_corr,$num_letti_agg_copia[$num1]);
} # fine else if ($dati_ca[$numca]['moltiplica'] != "t")
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_copia[$numca][$num1]) == "NO") $associa_costo = "NO";
} # fine for $num1
if ($dati_ca[$numca]['moltiplica'] == "t") {
for ($num1 = 0 ; $num1 < $num_controlla_limite2 ; $num1++) {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca,$beniinv_presenti_copia,$num_ripetizioni_copia[$numca],"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_copia[$numca][$num1],"",$num_ripetizioni_costo[$numca]);
if ($risul != "SI") $associa_costo = "NO";
} # fine for $num1
} # fine if ($dati_ca[$numca]['moltiplica'] == "t")
} # fine if ($ca_associato[$numca] == "SI")
} # fine for $numca
} # fine if ($dati_ca[$num_costo]['letto'] == "s")
else $num_costi_presenti_copia = $num_costi_presenti;

if (trova_periodo_permesso_costo($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo2) == "NO") $associa_costo = "NO";
else {
$moltiplica_costo2 = array();
calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica_costo2[0],$idinizioperiodo,$idfineperiodo,$settimane_costo2,"",$numpersone,$num_letti_agg_copia[0]);
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo2,$moltiplica_costo2[0]) == "NO") $associa_costo = "NO";
else for ($num1 = 1 ; $num1 < $num_controlla_limite ; $num1++) {
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica_costo2[$num1],$idinizioperiodo,$idfineperiodo,$settimane_costo2,"",$numpersone_corr,$num_letti_agg_copia[$num1]);
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo2,$moltiplica_costo2[$num1]) == "NO") { $num_app_reali_costo2 = $num1; break; }
} # fine for $num1
} # fine else (trova_periodo_permesso_costo($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo2) == "NO")
if ($dati_ca[$num_costo]['tipo_beniinv'] and $associa_costo == "SI") {
$nrc = "";
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti_copia,$nrc,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo2,$moltiplica_costo2[0],"");
if ($risul != "SI") $associa_costo = "NO";
else {
if ($num_app_reali_costo2 and $num_app_reali_costo2 < $num_controlla_limite) $num_controlla_limite2 = $num_app_reali_costo2;
else $num_controlla_limite2 = $num_controlla_limite;
for ($num1 = 1 ; $num1 < $num_controlla_limite2 ; $num1++) {
$beniinv_presenti_copia2 = $beniinv_presenti_copia;
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti_copia,$nrc,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo2,$moltiplica_costo2[$num1],"");
if ($risul != "SI") {
$beniinv_presenti_copia = $beniinv_presenti_copia2;
if (!$num_app_reali_costo2 or $num1 < $num_app_reali_costo2) $num_app_reali_costo2 = $num1;
break;
} # fine if ($risul != "SI")
} # fine for $num1
} # fine else if ($risul != "SI")
} # fine if ($dati_ca[$num_costo]['tipo_beniinv'] and $associa_costo == "SI")

if ($associa_costo == "SI") {
$beniinv_presenti = $beniinv_presenti_copia;
if ($dati_ca[$num_costo]['letto'] == "s") {
$num_costi_presenti = $num_costi_presenti_copia;
$num_letti_agg = $num_letti_agg_copia[0];
$num_letti_agg_nr = $num_letti_agg_copia;
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($ca_associato[$numca] == "SI") {
$moltiplica_costo[$numca] = $moltiplica_copia[$numca][0];
$moltiplica_costo_nr[$numca] = $moltiplica_copia[$numca];
if ($dati_ca[$numca]['moltiplica'] == "t") $num_ripetizioni_costo[$numca] = $num_ripetizioni_copia[$numca];
} # fine if ($ca_associato[$numca] == "SI")
} # fine for $numca
} # fine if ($dati_ca[$num_costo][letto] == "s")
$ca_associato[$num_costo] = "SI";
$settimane_costo[$num_costo] = $settimane_costo2;
$moltiplica_costo[$num_costo] = $moltiplica_costo2[0];
$moltiplica_costo_nr[$num_costo] = $moltiplica_costo2;
if ($num_app_reali_costo2) $num_app_reali_costo[$num_costo] = $num_app_reali_costo2;
if ($dati_ca[$num_costo]['tipo_beniinv']) $num_ripetizioni_costo[$num_costo] = $nrc;
} # fine if ($associa_costo == "SI")
} # fine if ($associa_costo == "SI")
} # fine if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num_costo]['id']] == "SI")
} # fine for $num_costo


for ($num1 = 1 ; $num1 < $num_controlla_limite ; $num1++) {
$costi_agg_tot_vett[$num1] = (double) 0;
$costo_escludi_perc_vett[$num1] = (double) 0;
} # fine for $num1
unset($prezzo_costo);
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($ca_associato[$numca] == "SI") {
$prezzo_costo[$numca][0] = (double) calcola_prezzo_totale_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo[$numca],$costo_tariffa,$tariffesettimanali,($costo_tariffa + $costi_agg_tot),$caparra,$numpersone,$costo_escludi_perc);
$costi_agg_tot = $costi_agg_tot + $prezzo_costo[$numca][0];
if ($dati_ca[$numca]['escludi_tot_perc'] == "s") $costo_escludi_perc = $costo_escludi_perc + $prezzo_costo[$numca][0];
if ($num_app_reali_costo[$numca]) $num_controlla_limite2 = $num_app_reali_costo[$numca];
else $num_controlla_limite2 = $num_controlla_limite;
$prezzo_costo_corr = $prezzo_costo[$numca][0];
for ($num1 = 1 ; $num1 < $num_controlla_limite2 ; $num1++) {
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
if (strcmp($costo_tariffa_nr[$numtariffa][$num1],"")) {
$costo_tariffa_corr = $costo_tariffa_nr[$numtariffa][$num1];
$tariffesettimanali_corr = $tariffesettimanali_nr[$numtariffa][$num1];
if (strcmp($tariffesettimanalip_nr[$numtariffa][$num1],"")) $tariffesettimanali_corr .= ";".$tariffesettimanalip_nr[$numtariffa][$num1];
$caparra_corr = calcola_caparra($dati_tariffe,'tariffa'.$numtariffa,$idinizioperiodo,$idfineperiodo,$costo_tariffa_nr[$numtariffa][$num1],$tariffesettimanali_corr);
} # fine if (strcmp($costo_tariffa_nr[$numtariffa][$num1],""))
else {
$costo_tariffa_corr = $costo_tariffa;
$tariffesettimanali_corr = $tariffesettimanali;
$caparra_corr = $caparra;
} # fine else if (strcmp($costo_tariffa_nr[$numtariffa][$num1],""))
$prezzo_costo[$numca][$num1] = (double) calcola_prezzo_totale_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo_nr[$numca][$num1],$costo_tariffa_corr,$tariffesettimanali_corr,($costo_tariffa_corr + $costi_agg_tot_vett[$num1]),$caparra_corr,$numpersone_corr,$costo_escludi_perc_vett[$num1]);
$costi_agg_tot_vett[$num1] = $costi_agg_tot_vett[$num1] + $prezzo_costo[$numca][$num1];
if ($dati_ca[$numca]['escludi_tot_perc'] == "s") $costo_escludi_perc_vett[$num1] = $costo_escludi_perc_vett[$num1] + $prezzo_costo[$numca][$num1];
$prezzo_costo_corr += (double) $prezzo_costo[$numca][$num1];
} # fine for $num1
if ($controlla_tariffe and $mostra_tariffa[$numtariffa] > 1) $prezzo_costo_corr = $prezzo_costo_corr / $mostra_tariffa[$numtariffa];
$dett_costi .= "<em>".$dati_ca[$numca]['nome']."<\\/em>: ".punti_in_num($prezzo_costo_corr)." $Euro.<br>";
#if ($num_app_reali_costo[$numca]) $deduzione_costi = (double) $deduzione_costi + ($prezzo_costo[$numca][0] * ($num_controlla_limite - $num_app_reali_costo[$numca]));
} # fine if ($ca_associato[$numca] == "SI")
} # fine for $numca

$commissioni = calcola_commissioni($dati_tariffe,'tariffa'.$numtariffa,$idinizioperiodo,$idfineperiodo,$tariffesettimanali,0,$costi_agg_tot);


$costo_tariffa_tot = $costo_tariffa + $costi_agg_tot;
if ($controlla_tariffe) {
$costo_totale_tariffe = $costo_totale_tariffe + $costo_tariffa_tot;
for ($num1 = 1 ; $num1 < $num_controlla_limite ; $num1++) {
if (strcmp($costo_tariffa_nr[$numtariffa][$num1],"")) $costo_tariffa_corr = $costo_tariffa_nr[$numtariffa][$num1];
else $costo_tariffa_corr = $costo_tariffa;
$costo_totale_tariffe = $costo_totale_tariffe + $costo_tariffa_corr + $costi_agg_tot_vett[$num1];
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$costi_agg_tot += $costi_agg_tot_vett[$num1];
$costo_tariffa_tot += $costo_tariffa_corr + $costi_agg_tot_vett[$num1];
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
} # fine for $num1
if ($dati_r2['napp']["tariffa".$numtariffa] > 1) {
$costi_agg_tot = $costi_agg_tot / $mostra_tariffa[$numtariffa];
$costo_tariffa_tot = $costo_tariffa_tot / $mostra_tariffa[$numtariffa];
} # fine if ($dati_r2['napp']["tariffa".$numtariffa] > 1)
} # fine if ($controlla_tariffe)
else for ($num1 = 1 ; $num1 < $num_controlla_limite ; $num1++) {
if (strcmp($costo_tariffa_nr[$numtariffa][$num1],"")) $costo_tariffa_corr = $costo_tariffa_nr[$numtariffa][$num1];
else $costo_tariffa_corr = $costo_tariffa;
$costi_agg_tot += $costi_agg_tot_vett[$num1];
$costo_tariffa_tot += $costo_tariffa_corr + $costi_agg_tot_vett[$num1];
} # fine for $num1
$costo_tariffa_tot_p = punti_in_num($costo_tariffa_tot,$stile_soldi);
$costi_agg_tot_p = punti_in_num($costi_agg_tot,$stile_soldi);

if ($controlla_tariffe) {
if ($mostra_tariffa[$numtariffa]) {
$reg2_checkbox = "x <b>".$mostra_tariffa[$numtariffa]."</b>";
if ($numpersone) {
if ($numpersone_orig_nt[$numtariffa]) $numpersone_corr = $numpersone_orig_nt[$numtariffa];
else $numpersone_corr = $numpersone;
$reg2_checkbox .= " ".mex("per",$pag)." <b>$numpersone_corr</b>";
if (!$numpersone_orig_nt[$numtariffa] and $num_letti_agg['max']) {
$reg2_checkbox .= "+".$num_letti_agg['max'];
$numpersone_corr += $num_letti_agg['max'];
} # fine if (!$numpersone_orig_nt[$numtariffa] and $num_letti_agg['max'])
$reg2_checkbox .= " ";
if ($numpersone_corr != 1) $reg2_checkbox .= mex("persone",$pag);
else $reg2_checkbox .= mex("persona",$pag);
} # fine if ($numpersone)
} # fine if ($mostra_tariffa[$numtariffa])
} # fine if ($controlla_tariffe)
else {
if ($app_regola2_vett[$numtariffa] and $priv_ins_multiple != "n" and !$tariffa_non_disp[$numtariffa]) {
$form_regola2 = "SI";
if ($num_controlla_limite > 1) $numpersone_corr = $numpersone_orig_tar;
else $numpersone_corr = $numpersone + $num_letti_agg['max'];
$reg2_checkbox = "<label><input type=\"checkbox\" id=\"r2t_$numtariffa\" name=\"reg2_tariffa$numtariffa\" value=\"SI\">
x </label><input type=\"text\" name=\"molt_reg2_tariffa$numtariffa\" maxlength=\"2\" value =\"1\" style=\"width: 2em;\"
 onclick=\"document.getElementById('r2t_$numtariffa').checked='1';\"><label for=\"r2t_$numtariffa\">
".mex("per",$pag)." </label><input type=\"text\" name=\"pers_reg2_tariffa$numtariffa\" maxlength=\"2\" value =\"$numpersone_corr\"
 style=\"width: 2em;\" onclick=\"document.getElementById('r2t_$numtariffa').checked='1';\"><label for=\"r2t_$numtariffa\">
".mex("persone",$pag)."</label>";
} # fine if ($app_regola2_vett[$numtariffa] and $priv_ins_multiple != "n" and...
else $reg2_checkbox = "&nbsp;";
} # fine else if ($controlla_tariffe)

if ($tariffa_non_disp[$numtariffa]) $colred = " class=\"colred\"";
else {
$colred = "";
if ($tariffa_occupa_reg1[$numtariffa]) $colred = " class=\"colblu\"";
} # fine else if ($tariffa_non_disp[$numtariffa])
echo "<tr><td style=\"height: 5px;\"></td></tr>
<tr><td>".mex("Tariffa",$pag)." \"<b$colred><i>$nometariffa</i></b>\": </td><td style=\"width: 10px;\"></td><td><b>$costo_tariffa_tot_p</b> $Euro";
if ($costi_agg_tot != 0 or $costo_agg_letti_vett[$numtariffa]) {
echo " <small>(".mex("compresi",$pag)." <b>$costi_agg_tot_p</b> $Euro ";
if ($costi_non_fissi == "SI") echo mex("di costi aggiuntivi",$pag);
else echo mex("di costi aggiuntivi fissi",$pag);
if ($costo_agg_letti_vett[$numtariffa]) echo ", $nome_cal";
echo "<script type=\"text/javascript\">
<!--
function apri_costi$numtariffa () {
var bott = document.getElementById('bott_costi$numtariffa');
var elem_costi = document.getElementById('dett_costi$numtariffa');
var costi_vis = elem_costi.style.visibility;
if (costi_vis != 'visible') {
var testo = '<table cellspacing=0><tr><td style=\"width: 12px;\"><\/td><td class=\"smallfont\">$dett_costi<\/td><\/tr><\/table>';
elem_costi.style.visibility = 'visible';
bott.innerHTML = '<img style=\"display: block;\" src=\"./img/freccia_giu_marg.png\" alt=\"&gt;\">';
}
if (costi_vis == 'visible') {
var testo = '';
elem_costi.style.visibility = 'hidden';
bott.innerHTML = '<img style=\"display: block;\" src=\"./img/freccia_destra_marg.png\" alt=\"&gt;\">';
}
elem_costi.innerHTML = testo;
}
-->
</script>
<button type=\"button\" id=\"bott_costi$numtariffa\" onclick=\"apri_costi$numtariffa()\">
<img src=\"./img/freccia_destra_marg.png\" alt=\"&gt;\" style=\"display: block;\"></button>)</small>";
$div_costi = "<div id=\"dett_costi$numtariffa\" style=\"visibility: hidden;\"></div>";
} # fine if ($costi_agg_tot != 0 or $costo_agg_letti_vett[$numtariffa])
else $div_costi = "";
if ($asterisco == "SI") {
echo "<div style=\"display: inline; color: red;\"><b>*</b></div>";
$asterisco_totale = "SI";
} # fine if ($asterisco == "SI")
echo ".$div_costi</td><td style=\"width: 35px;\"></td><td style=\"width: 220px;\">$reg2_checkbox</td></tr>";

if (($caparra or $commissioni)) {
echo "<tr><td colspan=\"5\" align=\"left\">";
$caparra_tot = $caparra;
$commissioni_tot = $commissioni;
for ($num1 = 1 ; $num1 < $num_controlla_limite ; $num1++) {
if (!strcmp($costo_tariffa_nr[$numtariffa][$num1],"")) {
$caparra_tot += $caparra;
$tariffesettimanali_corr = $tariffesettimanali;
} # fine if (!strcmp($costo_tariffa_nr[$numtariffa][$num1],""))
else {
$tariffesettimanali_corr = $tariffesettimanali_nr[$numtariffa][$num1];
if (strcmp($tariffesettimanalip_nr[$numtariffa][$num1],"")) $tariffesettimanali_corr .= ";".$tariffesettimanalip_nr[$numtariffa][$num1];
$caparra_nr[$num1] = calcola_caparra($dati_tariffe,'tariffa'.$numtariffa,$idinizioperiodo,$idfineperiodo,$costo_tariffa_nr[$numtariffa][$num1],$tariffesettimanali_corr);
$caparra_tot += $caparra_nr[$num1];
} # fine else if (!strcmp($costo_tariffa_nr[$numtariffa][$num1],""))
$commissioni_nr[$num1] = calcola_commissioni($dati_tariffe,'tariffa'.$numtariffa,$idinizioperiodo,$idfineperiodo,$tariffesettimanali_corr,0,$costi_agg_tot_vett[$num1]);
$commissioni_tot += $commissioni_nr[$num1];
} # fine for $num1
} # fine if (($caparra or $commissioni))
if ($caparra) {
$caparra_totale += (double) $caparra_tot;
#if ($mostra_tariffa[$numtariffa] > 1) $caparra_tot = $caparra_tot / $mostra_tariffa[$numtariffa];
if (!$controlla_tariffe) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>".mex("Caparra",$pag).": ".punti_in_num($caparra_tot)." $Euro</small>";
} # fine if ($caparra)
if ($commissioni and !$controlla_tariffe) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>".mex("Commissioni",$pag).": ".punti_in_num($commissioni_tot)." $Euro</small>";
if (($caparra or $commissioni) and !$controlla_tariffe) echo "</td></tr>";

if ($controlla_tariffe) {
for ($num1 = 0 ; $num1 < $num_controlla_limite ; $num1++) {
if (strcmp($costo_tariffa_nr[$numtariffa][$num1],"")) {
$costo_tariffa_corr = $costo_tariffa_nr[$numtariffa][$num1];
$tariffesettimanali_corr = $tariffesettimanali_nr[$numtariffa][$num1];
$caparra_corr = $caparra_nr[$num1];
} # fine if (strcmp($costo_tariffa_nr[$numtariffa][($num1 - 1)],""))
else {
$costo_tariffa_corr = $costo_tariffa;
$tariffesettimanali_corr = $tariffesettimanali;
$caparra_corr = $caparra;
} # fine else if (strcmp($costo_tariffa_nr[$numtariffa][($num1 - 1)],""))
if (strcmp($numpersone_nr[$num1],"")) $numpersone_corr = $numpersone_nr[$num1];
else $numpersone_corr = $numpersone;
$num_ripeti_contr++;
$numpersone_rc[$num_ripeti_contr] = $numpersone_corr;
if (strcmp($commissioni_nr[$num1],"")) $commissioni_corr = $commissioni_nr[$num1];
else $commissioni_corr = $commissioni;
$costo_tariffa_tot = $costo_tariffa_corr + $costi_agg_tot_vett[$num1];
$dati_tutte_tariffe .= "<input type=\"hidden\" name=\"costo_tot_$num_ripeti_contr\" value=\"$costo_tariffa_tot\">
<input type=\"hidden\" name=\"costo_tariffa_$num_ripeti_contr\" value=\"$costo_tariffa_corr\">
<input type=\"hidden\" name=\"tariffesettimanali_$num_ripeti_contr\" value=\"$tariffesettimanali_corr\">
<input type=\"hidden\" name=\"percentuale_tasse_tariffa_$num_ripeti_contr\" value=\"".$dati_tariffe['tariffa'.$numtariffa]['tasseperc']."\">
<input type=\"hidden\" name=\"caparra_$num_ripeti_contr\" value=\"$caparra_corr\">
<input type=\"hidden\" name=\"commissioni_$num_ripeti_contr\" value=\"$commissioni_corr\">
<input type=\"hidden\" name=\"nome_tariffa_$num_ripeti_contr\" value=\"$nometariffa\">
<input type=\"hidden\" name=\"n_letti_agg_$num_ripeti_contr\" value=\"".$num_letti_agg['max']."\">";
$num_ca_tot = 0;
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($ca_associato[$numca] == "SI") {
if ($dati_ca[$numca]['associasett'] == "s") $giorni_costo_agg = $settimane_costo[$numca];
else $giorni_costo_agg = "";
$dati_tutte_tariffe .= "<input type=\"hidden\" name=\"nome_costo_agg$num_ca_tot"."_$num_ripeti_contr\" value=\"".$dati_ca[$numca]['nome']."\">
<input type=\"hidden\" name=\"val_costo_agg$num_ca_tot"."_$num_ripeti_contr\" value=\"".$prezzo_costo[$numca][$num1]."\">
<input type=\"hidden\" name=\"percentuale_tasse_costo_agg$num_ca_tot"."_$num_ripeti_contr\" value=\"".$dati_ca[$numca]['tasseperc']."\">
<input type=\"hidden\" name=\"moltiplica_max_costo_agg$num_ca_tot"."_$num_ripeti_contr\" value=\"".$moltiplica_costo_nr[$numca][$num1]."\">
<input type=\"hidden\" name=\"giorni_costo_agg$num_ca_tot"."_$num_ripeti_contr\" value=\"$giorni_costo_agg\">";
$num_ca_tot++;
} # fine if ($ca_associato[$numca] == "SI")
} # fine for $numca
$dati_tutte_tariffe .= "<input type=\"hidden\" name=\"num_costi_aggiuntivi_$num_ripeti_contr\" value=\"$num_ca_tot\">";
} # fine for $num1
} # fine if ($controlla_tariffe)
else {
$option_contratti .= "<option value=\"$numtariffa\">$nometariffa</option>";
$costo_tariffa = $costo_tariffa_tot - $costi_agg_tot;
$dati_tutte_tariffe .= "<input type=\"hidden\" name=\"c_tot_selez$numtariffa"."_1\" value=\"$costo_tariffa_tot\">
<input type=\"hidden\" name=\"c_tariffa_selez$numtariffa"."_1\" value=\"$costo_tariffa\">
<input type=\"hidden\" name=\"tarsett_tariffa_selez$numtariffa"."_1\" value=\"$tariffesettimanali\">
<input type=\"hidden\" name=\"perctas_tariffa_selez$numtariffa"."_1\" value=\"".$dati_tariffe['tariffa'.$numtariffa]['tasseperc']."\">
<input type=\"hidden\" name=\"cap_tariffa_selez$numtariffa"."_1\" value=\"$caparra_tot\">
<input type=\"hidden\" name=\"comm_tariffa_selez$numtariffa"."_1\" value=\"$commissioni_tot\">
<input type=\"hidden\" name=\"n_tariffa_selez$numtariffa"."_1\" value=\"$nometariffa\">
<input type=\"hidden\" name=\"n_letti_agg_tariffa_selez$numtariffa"."_1\" value=\"".$num_letti_agg['max']."\">
<input type=\"hidden\" name=\"numpers_tariffa_selez$numtariffa"."_1\" value=\"$numpersone\">";
$num_ca_tot = 0;
for ($num1 = 0 ; $num1 < $num_controlla_limite ; $num1++) {
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($ca_associato[$numca] == "SI") {
if ($dati_ca[$numca]['associasett'] == "s") $giorni_costo_agg = $settimane_costo[$numca];
else $giorni_costo_agg = "";
$dati_tutte_tariffe .= "<input type=\"hidden\" name=\"nome_costo_agg$num_ca_tot"."_tsel$numtariffa"."_1\" value=\"".$dati_ca[$numca]['nome']."\">
<input type=\"hidden\" name=\"val_costo_agg$num_ca_tot"."_tsel$numtariffa"."_1\" value=\"".$prezzo_costo[$numca][$num1]."\">
<input type=\"hidden\" name=\"percentuale_tasse_costo_agg$num_ca_tot"."_tsel$numtariffa"."_1\" value=\"".$dati_ca[$numca]['tasseperc']."\">
<input type=\"hidden\" name=\"moltiplica_max_costo_agg$num_ca_tot"."_tsel$numtariffa"."_1\" value=\"".$moltiplica_costo_nr[$numca][$num1]."\">
<input type=\"hidden\" name=\"giorni_costo_agg$num_ca_tot"."_tsel$numtariffa"."_1\" value=\"$giorni_costo_agg\">";
$num_ca_tot++;
} # fine if ($ca_associato[$numca] == "SI")
} # fine for $numca
} # fine for $num1
$dati_tutte_tariffe .= "<input type=\"hidden\" name=\"num_costi_aggiuntivi_tsel$numtariffa"."_1\" value=\"$num_ca_tot\">";
} # fine else if ($controlla_tariffe)

} # fine if ($continuare == "SI")
} # fine if ($continuare == "SI")

} # fine if ($continuare_totale != "NO")
} # fine for $numtariffa

echo "</table>";


$dati_costi_agg = "<input type=\"hidden\" name=\"numcostiagg\" value=\"$numcostiagg\">";
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
$idcostoagg = "idcostoagg".$numca;
$num_costo = $dati_ca['id'][$$idcostoagg];
if ($dati_ca[$num_costo]['mostra'] == "s") {
$dati_costi_agg .= "<input type=\"hidden\" name=\"id_periodi_costo$numca\" value=\"".${"id_periodi_costo".$numca}."\">
<input type=\"hidden\" name=\"costoagg$numca\" value=\"".${"costoagg".$numca}."\">
<input type=\"hidden\" name=\"idcostoagg$numca\" value=\"".$$idcostoagg."\">
<input type=\"hidden\" name=\"numsettimane$numca\" value=\"".${"numsettimane".$numca}."\">
<input type=\"hidden\" name=\"nummoltiplica_ca$numca\" value=\"".${"nummoltiplica_ca".$numca}."\">";
} # fine if ($dati_ca[$num_costo]['mostra'] == "s")
} # fine for $numca

$form_mostra_non_disp = "<form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"num_persone_casa\" value=\"$num_persone_casa\">
<input type=\"hidden\" name=\"molt_app_persone_casa\" value=\"$molt_app_persone_casa\">
<input type=\"hidden\" name=\"numpersone\" value=\"$numpersone_orig\">
<input type=\"hidden\" name=\"mostra_non_disp\" value=\"1\">
$dati_costi_agg
<button class=\"xavl\" type=\"submit\"><div>".mex("Mostra le tariffe non disponibili",$pag)."</div></button>
</div></form>";

$numpersone = $numpersone_orig;
if (!$tariffa_mostrata and $continuare_totale != "NO") {
$continuare_totale = "NO";
echo "<br>".mex("Non c'è nessuna tariffa disponibile in questo periodo",$pag).".<br>
</div></table></div></form></td></tr></table>";
if ($tariffa_non_mostrata) echo "<br>$form_mostra_non_disp<br>";
} # fine if (!$tariffa_mostrata and $continuare_totale != "NO")



if ($continuare_totale != "NO") {

echo "</div>";
if ($asterisco_totale == "SI") echo "<tr><td style=\"height: 5px;\"></td></tr><tr><td align=\"left\"><div style=\"display: inline; color: red;\"><b>*</b></div> ".mex("Non si sono potuti applicare alla tariffa uno o più costi",$pag).".</td></tr>";
if ($form_regola2 == "SI") {
if ($priv_ins_multiple == "s") echo "<tr><td style=\"height: 5px;\"></td></tr><tr><td>
<label><input type=\"checkbox\" name=\"prenota_vicine\" value=\"SI\"> ".mex("Appartamenti vicini",'unit.php').".</label></td></tr>";
echo "<tr><td style=\"height: 5px;\"></td></tr><tr><td align=\"center\">
$dati_costi_agg
<button class=\"xavl\" type=\"submit\"><div>".mex("Ricontrolla la disponibilità negli appartamenti selezionati",'unit.php')."</div></button>
</td></tr>";
} # fine if ($form_regola2 == "SI")
if ($controlla_tariffe) {
$costo_totale_tariffe_p = punti_in_num($costo_totale_tariffe,$stile_soldi);
echo "<tr><td style=\"height: 5px;\"></td></tr><tr><td>
".mex("TOTALE",$pag).": <b>$costo_totale_tariffe_p</b> $Euro</td></tr>";
if ($caparra_totale) {
echo "<tr><td align=\"left\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>
".mex("Caparra",$pag).": ".punti_in_num($caparra_totale,$stile_soldi)." $Euro
</small></td></tr>";
} # fine if ($caparra_totale)
} # fine if ($controlla_tariffe)
echo "</table></div></form>";

if ($tariffa_non_mostrata) echo "<br><div class=\"txtcenter\">$form_mostra_non_disp</div>";
echo "</td></tr></table>";

#if ($priv_ins_costi_agg == "s" and ($form_regola2 == "SI" or $controlla_tariffe)) {
if ($priv_ins_costi_agg == "s") {
$numcostiagg_prec = $numcostiagg;
$numcostiagg = 0;
$testo_costi_agg = "";
unset($costi_agg_raggr);
unset($chiedi_combina);
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($dati_ca[$num1]['mostra'] == "s") {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num1]['id']] == "SI") {
if ($dati_ca[$num1]['combina'] != "s") {
$testo_costo = "";
if ($dati_ca[$num1]['raggruppa'] != "s") {
$numcostiagg++;
$numcostiagg_v = $numcostiagg;
$nome_costo = $dati_ca[$num1]['nome'];
$id_costo = $dati_ca[$num1]['id'];
} # fine if ($dati_ca[$num1]['raggruppa'] != "s")
else {
$numcostiagg_v = "[nca]";
$nome_costo = "[nome]";
$id_costo = "[id]";
} # fine else if ($dati_ca[$num1]['raggruppa'] != "s")
$costoagg = "costoagg".$numcostiagg_v;
if ($dati_ca[$num1]['tipo'] == "u") $tipo_ca = "unico";
if ($dati_ca[$num1]['tipo'] == "s") $tipo_ca = "$parola_settimanale";
if ($$costoagg == "SI") {
$checked = " checked";
$b_check = "<b>";
$b_slash_check = "</b>";
} # fine if ($$costoagg == "SI")
else {
$checked = "";
$b_check = "";
$b_slash_check = "";
unset(${"id_periodi_costo".$numcostiagg_v});
unset(${"numsettimane".$numcostiagg_v});
unset(${"nummoltiplica_ca".$numcostiagg_v});
} # fine else if ($$costoagg == "SI")
$testo_costo .= "<input type=\"hidden\" name=\"idcostoagg$numcostiagg_v\" value=\"$id_costo\">
<input type=\"hidden\" name=\"id_periodi_costo$numcostiagg_v\" value=\"".${"id_periodi_costo".$numcostiagg_v}."\">
<label><input type=\"checkbox\" id=\"ca_$numcostiagg_v\" name=\"$costoagg\" value=\"SI\"$checked>
".mex("costo aggiuntivo $tipo_ca",$pag)." $b_check\"<em>$nome_costo</em>\"$b_slash_check";
if ($dati_ca[$num1]['numsett'] == "c" and $dati_ca[$num1]['associasett'] == "n") {
$numsettimane = "numsettimane".$numcostiagg_v;
if ($$numsettimane) $valnumsettimane = $$numsettimane;
else $valnumsettimane = 0;
$testo_costo .= ", ".mex("nº di $parola_settimane da applicare",$pag).":</label>
<input type=\"text\" name=\"$numsettimane\" value=\"$valnumsettimane\" size=\"3\" maxlength=\"3\"
 onclick=\"document.getElementById('ca_$numcostiagg_v').checked='1';\"><label for=\"ca_$numcostiagg_v\">";
} # fine if ($dati_ca[$num1]['numsett'] == "c" and...
if ($dati_ca[$num1]['moltiplica'] == "c") {
$nummoltiplica_ca = "nummoltiplica_ca".$numcostiagg_v;
if ($$nummoltiplica_ca) $valnummoltiplica_ca = $$nummoltiplica_ca;
else $valnummoltiplica_ca = 1;
$testo_costo .= ", ".mex("da moltiplicare per",$pag).":</label>";
if ($dati_ca[$num1]['molt_max'] != "n") $testo_costo .= "<input type=\"text\" name=\"$nummoltiplica_ca\" value=\"$valnummoltiplica_ca\" size=\"3\" maxlength=\"12\"
 onclick=\"document.getElementById('ca_$numcostiagg_v').checked='1';\">";
else {
$testo_costo .= "<select name=\"$nummoltiplica_ca\" onclick=\"document.getElementById('ca_$numcostiagg_v').checked='1';\">";
for ($num2 = 1 ; $num2 <= $dati_ca[$num1]['molt_max_num'] ; $num2++) {
if ($num2 == $valnummoltiplica_ca) $sel = " selected";
else $sel = "";
$testo_costo .= "<option value=\"$num2\"$sel>$num2</option>";
} # fine for $num2
$testo_costo .= "</select>";
} # fine else if ($dati_ca[$num1]['molt_max'] != "n")
$testo_costo .= "<label for=\"ca_$numcostiagg_v\">";
} # fine if ($dati_ca[$num1]['moltiplica'] == "c")
$testo_costo .= ".</label><br>";
} # fine if ($dati_ca[$num1]['combina'] != "s")
else {
$testo_costo = "combina";
$categ = $dati_ca[$num1]['categoria'];
if ($dati_ca[$num1]['numsett'] == "c" and $dati_ca[$num1]['associasett'] == "n") $chiedi_combina[$categ]['sett'] = 1;
if ($dati_ca[$num1]['moltiplica'] == "c") {
if (!$chiedi_combina[$categ]['molt']) $chiedi_combina[$categ]['molt_max_num'] = $dati_ca[$num1]['molt_max_num'];
if ($dati_ca[$num1]['molt_max'] != "n") $chiedi_combina[$categ]['molt_max_num'] = 0;
elseif ($chiedi_combina[$categ]['molt_max_num'] and $chiedi_combina[$categ]['molt_max_num'] < $dati_ca[$num1]['molt_max_num']) $chiedi_combina[$categ]['molt_max_num'] = $dati_ca[$num1]['molt_max_num'];
$chiedi_combina[$categ]['molt'] = 1;
} # fine if ($dati_ca[$num1]['moltiplica'] == "c")
} # fine else if ($dati_ca[$num1]['combina'] != "s")
if ($dati_ca[$num1]['raggruppa'] != "s") $testo_costi_agg .= $testo_costo;
else $costi_agg_raggr[$testo_costo."<>".$dati_ca[$num1]['categoria']] .= $dati_ca[$num1]['id'].",";
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine if ($dati_ca[$num1]['mostra'] == "s")
} # fine for $num1

if (@is_array($costi_agg_raggr)) {
foreach ($costi_agg_raggr as $testo_costo => $id_costi) {
$testo_costo = explode("<>",$testo_costo);
$numcostiagg++;
$id_costi_vett = explode(",",substr($id_costi,0,-1));
$num_id_costi = count($id_costi_vett);
if ($testo_costo[0] != "combina") {
$testo_costo = $testo_costo[0];
if (${"costoagg".$numcostiagg} == "SI") {
$testo_costo = str_replace("type=\"checkbox\"","type=\"checkbox\" checked",$testo_costo);
$b_check = "<b>";
$b_slash_check = "</b>";
} # fine if (${"costoagg".$numcostiagg} == "SI")
else {
$b_check = "";
$b_slash_check = "";
unset(${"id_periodi_costo".$numcostiagg});
unset(${"numsettimane".$numcostiagg});
unset(${"nummoltiplica_ca".$numcostiagg});
} # fine else if (${"costoagg".$numcostiagg} == "SI")
if (${"id_periodi_costo".$numcostiagg}) $testo_costo = str_replace("name=\"id_periodi_costo[nca]\" value=\"\"","name=\"id_periodi_costo[nca]\" value=\"".${"id_periodi_costo".$numcostiagg}."\"",$testo_costo);
if (${"numsettimane".$numcostiagg}) $testo_costo = str_replace("name=\"numsettimane[nca]\" value=\"0\"","name=\"numsettimane[nca]\" value=\"".${"numsettimane".$numcostiagg}."\"",$testo_costo);
if (${"nummoltiplica_ca".$numcostiagg}) $testo_costo = str_replace("name=\"nummoltiplica_ca[nca]\" value=\"1\"","name=\"nummoltiplica_ca[nca]\" value=\"".${"nummoltiplica_ca".$numcostiagg}."\"",$testo_costo);
$testo_costo = str_replace("[nca]",$numcostiagg,$testo_costo);
if ($num_id_costi == 1) {
$num_costo = $dati_ca['id'][$id_costi_vett[0]];
$testo_costo = str_replace(" \"<em>[nome]</em>\""," $b_check\"<em>".$dati_ca[$num_costo]['nome']."</em>\"$b_slash_check",$testo_costo);
$testo_costo = str_replace(" value=\"[id]\""," value=\"".$id_costi_vett[0]."\"",$testo_costo);
} # fine if ($num_id_costi == 1)
else {
$sel_costi = "</label><select name=\"idcostoagg$numcostiagg\" onclick=\"document.getElementById('ca_$numcostiagg').checked='1';\">";
for ($num1 = 0 ; $num1 < $num_id_costi ; $num1++) {
$num_costo = $dati_ca['id'][$id_costi_vett[$num1]];
if (${"idcostoagg".$numcostiagg} == $id_costi_vett[$num1]) {
$sel = " selected";
$opt_bg = " style=\"font-weight: bold;\"";
} # fine if (${"idcostoagg".$numcostiagg} == $id_costi_vett[$num1])
else {
$sel = "";
$opt_bg = "";
} # fine else if (${"idcostoagg".$numcostiagg} == $id_costi_vett[$num1])
$sel_costi .= "<option value=\"".$id_costi_vett[$num1]."\"$opt_bg$sel>".$dati_ca[$num_costo]['nome']."</option>";
} # fine for $num1
$sel_costi .= "</select><label for=\"ca_$numcostiagg\">";
$testo_costo = str_replace(" \"<em>[nome]</em>\""," $b_check\"$b_slash_check$sel_costi$b_check\"$b_slash_check",$testo_costo);
$testo_costo = str_replace("<input type=\"hidden\" name=\"idcostoagg$numcostiagg\" value=\"[id]\">","",$testo_costo);
} # fine (count($id_costi_vett) == 1)
$testo_costi_agg .= $testo_costo;
} # fine if ($testo_costo[0] != "combina")
else {
$categoria = $testo_costo[1];
if (${"costoagg".$numcostiagg} == "SI") {
$checked = " checked";
$b_check = "<b>";
$b_slash_check = "</b>";
} # fine if (${"costoagg".$numcostiagg} == "SI")
else {
$checked = "";
$b_check = "";
$b_slash_check = "";
unset(${"id_periodi_costo".$numcostiagg});
unset(${"numsettimane".$numcostiagg});
unset(${"nummoltiplica_ca".$numcostiagg});
} # fine else if (${"costoagg".$numcostiagg} == "SI")
$testo_costi_agg .= "<input type=\"hidden\" name=\"idcostoagg$numcostiagg\" value=\"c".htmlspecialchars($categoria)."\">
<input type=\"hidden\" name=\"id_periodi_costo$numcostiagg\" value=\"".${"id_periodi_costo".$numcostiagg}."\">
<label><input type=\"checkbox\" id=\"ca_$numcostiagg\" name=\"costoagg$numcostiagg\" value=\"SI\"$checked>
".mex("costo aggiuntivo",$pag)." $b_check\"<em>".htmlspecialchars($categoria)."</em>\"$b_slash_check";
if ($chiedi_combina[$categoria]['sett']) {
$numsettimane = "numsettimane".$numcostiagg;
if ($$numsettimane) $valnumsettimane = $$numsettimane;
else $valnumsettimane = 0;
$testo_costi_agg .= ", ".mex("nº di $parola_settimane da applicare",$pag).":</label>
<input type=\"text\" name=\"$numsettimane\" value=\"$valnumsettimane\" size=\"3\" maxlength=\"3\"
 onclick=\"document.getElementById('ca_$numcostiagg').checked='1';\"><label for=\"ca_$numcostiagg\">";
} # fine if ($chiedi_combina[$categoria]['sett'])
if ($chiedi_combina[$categoria]['molt']) {
$nummoltiplica_ca = "nummoltiplica_ca".$numcostiagg;
if ($$nummoltiplica_ca) $valnummoltiplica_ca = $$nummoltiplica_ca;
else $valnummoltiplica_ca = 1;
$testo_costi_agg .= ", ".mex("da moltiplicare per",$pag).":</label>";
if (!$chiedi_combina[$categoria]['molt_max_num']) $testo_costi_agg .= "<input type=\"text\" name=\"$nummoltiplica_ca\" value=\"$valnummoltiplica_ca\" size=\"3\" maxlength=\"12\"
 onclick=\"document.getElementById('ca_$numcostiagg').checked='1';\">";
else {
$testo_costi_agg .= "<select name=\"$nummoltiplica_ca\" onclick=\"document.getElementById('ca_$numcostiagg').checked='1';\">";
for ($num2 = 1 ; $num2 <= $chiedi_combina[$categoria]['molt_max_num'] ; $num2++) {
if ($num2 == $valnummoltiplica_ca) $sel = " selected";
else $sel = "";
$testo_costi_agg .= "<option value=\"$num2\"$sel>$num2</option>";
} # fine for $num2
$testo_costi_agg .= "</select>";
} # fine else if ($dati_ca[$num1]['molt_max'] != "n")
$testo_costi_agg .= "<label for=\"ca_$numcostiagg\">";
} # fine if ($chiedi_combina[$categoria]['molt'])
$testo_costi_agg .= ".</label><br>";
} # fine else if ($testo_costo[0] != "combina")
} # fine foreach ($costi_agg_raggr as $testo_costo => $id_costi)
} # fine if (@is_array($costi_agg_raggr))
for ($num1 = ($numcostiagg + 1) ; $num1 <= $numcostiagg_prec ; $num1++) if (${"costoagg".$num1} == "SI") echo "<input type=\"hidden\" name=\"id_periodi_costo$num1\" value=\"".${"id_periodi_costo".$num1}."\">";

if ($testo_costi_agg) {
echo "<br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div class=\"rbox\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"num_persone_casa\" value=\"$num_persone_casa\">
<input type=\"hidden\" name=\"molt_app_persone_casa\" value=\"$molt_app_persone_casa\">
<input type=\"hidden\" name=\"numpersone\" value=\"$numpersone\">
<input type=\"hidden\" name=\"mostra_non_disp\" value=\"$mostra_non_disp\">
<input type=\"hidden\" name=\"numcostiagg\" value=\"$numcostiagg\">
$dati_email";
if ($controlla_tariffe) {
echo "<input type=\"hidden\" name=\"controlla_tariffe\" value=\"$controlla_tariffe\">
<input type=\"hidden\" name=\"prenota_vicine\" value=\"$prenota_vicine\">";
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
echo "<input type=\"hidden\" name=\"reg2_tariffa$numtariffa\" value=\"".${"reg2_tariffa".$numtariffa}."\">
<input type=\"hidden\" name=\"molt_reg2_tariffa$numtariffa\" value=\"".${"molt_reg2_tariffa".$numtariffa}."\">
<input type=\"hidden\" name=\"pers_reg2_tariffa$numtariffa\" value=\"".${"pers_reg2_tariffa".$numtariffa}."\">";
} # fine for $numtariffa
} # fine if ($controlla_tariffe)
echo "<table><tr><td>$testo_costi_agg</td><td style=\"width: 25px;\"></td><td valign=\"middle\">
<button class=\"aexc\" type=\"submit\"><div>".mex("Aggiungi",$pag)."</div></button>
</td><td style=\"width: 20px;\"></td></tr></table></div></form>";
} # fine if ($testo_costi_agg)
} # fine if ($priv_ins_costi_agg == "s")

} # fine if ($continuare_totale != "NO")

} # fine if ($liberato == "SI")

unlock_tabelle($tabelle_lock);


if ($continuare_totale != "NO") {

if (!$testo_costi_agg) echo "<br>";
echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inizioperiodo\" value=\"$data_inizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo\" value=\"$data_fineperiodo\">
<input type=\"hidden\" name=\"mostra_non_disp\" value=\"$mostra_non_disp\">
$dati_email
 ".ucfirst(mex("disponibilità solo negli appartamenti da",'unit.php'))."
 <select name=\"num_persone_casa\">
<option value=\"\" selected>--</option>";
asort ($dati_app['maxocc']);
reset ($dati_app['maxocc']);
foreach ($dati_app['maxocc'] as $key => $val) {
$persone_casa = $val;
if ($persone_casa != $ultime_persone_casa) {
$ultime_persone_casa = $persone_casa;
echo "<option value=\"$persone_casa\">$persone_casa</option>";
} # fine if ($persone_casa != $ultimepersone_casa)
} # fine foreach ($dati_app['maxocc'] as $key => $val)
echo "</select> ".mex("persone",$pag);
if ($priv_ins_multiple != "n") echo "(x <input type=\"text\" name=\"molt_app_persone_casa\" size=\"2\" maxlength=\"2\" value =\"1\">)";
else echo "<input type=\"hidden\" name=\"molt_app_persone_casa\" value=\"1\">";
echo " <button class=\"xavl\" type=\"submit\"><div>".mex("Ricontrolla",$pag)."</div></button>
</div></form><br>";

$data_inizio = esegui_query("select * from $tableperiodi where idperiodi = $idinizioperiodo");
$data_inizio = risul_query($data_inizio,0,'datainizio');
$data_fine = esegui_query("select * from $tableperiodi where idperiodi = $idfineperiodo");
$data_fine = risul_query($data_fine,0,'datafine');
if ($numpersone_rc[1]) $num_persone_1 = $numpersone_rc[1];
else $num_persone_1 = $numpersone;
echo "<div style=\"text-align: center;\"><table style=\"margin-left: auto; margin-right: auto;\"><tr><td align=\"center\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_contratto.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_ripeti\" value=\"$num_ripeti_contr\">
<input type=\"hidden\" name=\"data_inizio_1\" value=\"$data_inizio\">
<input type=\"hidden\" name=\"data_fine_1\" value=\"$data_fine\">
<input type=\"hidden\" name=\"num_periodi_1\" value=\"$lunghezza_perioido\">
<input type=\"hidden\" name=\"num_persone_1\" value=\"$num_persone_1\">";
for ($num1 = 2 ; $num1 <= $num_ripeti_contr ; $num1++) {
echo "<input type=\"hidden\" name=\"data_inizio_$num1\" value=\"$data_inizio\">
<input type=\"hidden\" name=\"data_fine_$num1\" value=\"$data_fine\">
<input type=\"hidden\" name=\"num_periodi_$num1\" value=\"$lunghezza_perioido\">
<input type=\"hidden\" name=\"num_persone_$num1\" value=\"".$numpersone_rc[$num1]."\">";
} # fine for $num1
if ($origine) echo "<input type=\"hidden\" name=\"origine\" value=\"$origine\">";
echo "$dati_email
$dati_tutte_tariffe
".ucfirst(mex("documento di tipo",$pag))."
 <select name=\"numero_contratto\">";
$nomi_contratti = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '$id_utente'");
$nomi_contratti = risul_query($nomi_contratti,0,'valpersonalizza');
$nomi_contratti = explode("#@&",$nomi_contratti);
$num_nomi_contratti = count($nomi_contratti);
for ($num1 = 0 ; $num1 < $num_nomi_contratti ; $num1++) {
$dati_nome_contratto = explode("#?&",$nomi_contratti[$num1]);
$nome_contratto[$dati_nome_contratto[0]] = $dati_nome_contratto[1];
} # fine for $num1
$max_contr = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'contr%'");
$max_contr = risul_query($max_contr,0,0);
unset($contr_mln);
$contr_mln[0] = 1;
$dati_mln = esegui_query("select * from $tablecontratti where tipo $LIKE 'mln_%' ");
for ($num1 = 0 ; $num1 < numlin_query($dati_mln) ; $num1++) {
if (strcmp(risul_query($dati_mln,$num1,'testo'),"")) {
$num_contr = risul_query($dati_mln,$num1,'numero');
$contr_mln[$num_contr]['num']++;
$contr_mln[$num_contr][$contr_mln[$num_contr]['num']] = substr(risul_query($dati_mln,$num1,'tipo'),4);
} # fine if (strcmp(risul_query($dati_mln,$num1,'testo'),""))
} # fine for $num1
for ($num_contratto = 1 ; $num_contratto <= $max_contr ; $num_contratto++) {
if ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contratto] == "SI") {
if ($nome_contratto[$num_contratto]) $num_contratto_vedi = $nome_contratto[$num_contratto];
else $num_contratto_vedi = $num_contratto;
if (!$contr_mln[$num_contratto] or $contr_mln[$num_contratto]['num'] == 1) echo "<option value=\"$num_contratto\">$num_contratto_vedi</option>";
else {
$default_mln = esegui_query("select testo from $tablecontratti where tipo $LIKE 'contr%' and numero = '$num_contratto' ");
$default_mln = str_replace("#!mln!#","",risul_query($default_mln,0,'testo'));
for ($num1 = 1 ; $num1 <= $contr_mln[$num_contratto]['num'] ; $num1++) {
if ($contr_mln[$num_contratto][$num1] == $default_mln) echo "<option value=\"$num_contratto-".$contr_mln[$num_contratto][$num1]."\">$num_contratto_vedi (".$contr_mln[$num_contratto][$num1].")</option>";
} # fine for $num1
for ($num1 = 1 ; $num1 <= $contr_mln[$num_contratto]['num'] ; $num1++) {
if ($contr_mln[$num_contratto][$num1] != $default_mln) echo "<option value=\"$num_contratto-".$contr_mln[$num_contratto][$num1]."\">$num_contratto_vedi (".$contr_mln[$num_contratto][$num1].")</option>";
} # fine for $num1
} # fine else if (!$contr_mln[$num1] or...
} # fine if ($attiva_contratti_consentiti == "n" or...
} # fine for $num_contratto
echo "</select>
 <button class=\"vdoc\" type=\"submit\"><div>".ucfirst(mex("visualizza",$pag))."</div></button>";
if ($option_contratti) {
echo "<br><div class=\"doc_ec\">(".mex("con la tariffa",$pag)."
 <select name=\"tariffa_selezionata\">
<option value=\"\" selected>----</option>
$option_contratti
</select>)</div>";
} # fine if ($option_contratti)
echo "</div></form>";
if ($mostra_quadro_disp) echo "</td>";
else echo "<br><br></td></tr>";

if ($priv_ins_nuove_prenota == "s") {
if ($mostra_quadro_disp) echo "<td style=\"width: 50px;\"></td><td align=\"center\" valign=\"middle\">";
else echo "<tr><td align=\"center\">";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tipologie\" value=\"$num_tipologie\">
<input type=\"hidden\" name=\"mos_tut_dat\" value=\"SI\">
<input type=\"hidden\" name=\"prenota_vicine\" value=\"$prenota_vicine\">";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
echo "<input type=\"hidden\" name=\"inizioperiodo$n_t\" value=\"$idinizioperiodo\">
<input type=\"hidden\" name=\"fineperiodo$n_t\" value=\"$idfineperiodo\">
<input type=\"hidden\" name=\"numpersone$n_t\" value=\"".$num_persone_invia[$n_t]."\">
<input type=\"hidden\" name=\"num_persone_casa$n_t\" value=\"$num_persone_casa\">
<input type=\"hidden\" name=\"num_app_richiesti$n_t\" value=\"".$num_app_richiesti_invia[$n_t]."\">
<input type=\"hidden\" name=\"nometipotariffa$n_t\" value=\"".$tariffa_invia[$n_t]."\">";
} # fine for $n_t
echo "<button class=\"ires\" type=\"submit\"><div>".mex("Inserisci la prenotazione",$pag)."</div></button>
</div></form></td></tr>";
} # fine if ($priv_ins_nuove_prenota == "s")
echo "</table>";



if ($mostra_quadro_disp) {
include("./includes/funzioni_quadro_disp.php");
$c_sfondo_tab_disp = "#dddddd";
$c_inisett_tab_disp = "#bbbbbb";
$c_libero_tab_disp = "#0cc80c";
$c_occupato_tab_disp = "#f8011e";
$aper_font_tab_disp = "";
$chiu_font_tab_disp = "";
$fr_persone = mex("persone",$pag);
$fr_persona = mex("persona",$pag);
$nome_mese["01"] = mex("Gennaio","giorni_mesi.php");
$nome_mese["02"] = mex("Febbraio","giorni_mesi.php");
$nome_mese["03"] = mex("Marzo","giorni_mesi.php");
$nome_mese["04"] = mex("Aprile","giorni_mesi.php");
$nome_mese["05"] = mex("Maggio","giorni_mesi.php");
$nome_mese["06"] = mex("Giugno","giorni_mesi.php");
$nome_mese["07"] = mex("Luglio","giorni_mesi.php");
$nome_mese["08"] = mex("Agosto","giorni_mesi.php");
$nome_mese["09"] = mex("Settembre","giorni_mesi.php");
$nome_mese["10"] = mex("Ottobre","giorni_mesi.php");
$nome_mese["11"] = mex("Novembre","giorni_mesi.php");
$nome_mese["12"] = mex("Dicembre","giorni_mesi.php");
if ($priv_ins_multiple != "n") $mostra_num_liberi = "SI";
else $mostra_num_liberi = "NO";

if ($mostra_quadro_disp == "reg2") $tar_cons = "";
else $tar_cons = "priv";
trova_app_consentiti_per_tab_disp($app_consentito,$app_consentito_sett,$quadro_non_preciso,$dati_app,$dati_tariffe,$id_data_inizio_tab_disp,$num_colonne_tab_disp,$dati_r2,$attiva_regole1_consentite,0,"",$condizioni_regole1_consentite,$tar_cons,$attiva_tariffe_consentite,$tariffe_consentite_vett,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$tableregole);

$righe_tab_disp = crea_quadro_disp($id_data_inizio_tab_disp,$num_colonne_tab_disp,$mostra_quadro_disp,$mostra_num_liberi,$app_consentito,$app_consentito_sett,$app_regola2_orig,$tipo_periodi,$dati_tariffe['num'],$nome_tariffa,$dati_app,$prenota_in_app_sett,$app_orig_prenota_id,$tableperiodi,"",$dati_tariffe);

if ($righe_tab_disp) {
echo "<br><div style=\"text-align: center;\"><small>".mex("Quadro indicativo disponibilità",$pag)."";
if ($quadro_non_preciso == "SI") echo " <em>(".mex("potrebbe non essere preciso",$pag).")</em>";
echo "</small>
<table class=\"tab_disp\" border=1 cellspacing=0 cellpadding=1 style=\"background-color: $c_sfondo_tab_disp; font-size:70%; text-align: center; margin-left: auto;  margin-right: auto;\">
$righe_tab_disp
</table></div>";
} # fine if ($righe_tab_disp)

} # fine if ($mostra_quadro_disp)


} # fine if ($continuare_totale != "NO")
else echo "<br>
<div style=\"text-align: center;\">";


} # fine if ($verificare != "NO")


else unlock_tabelle($tabelle_lock);



if ($origine) {
$action = $origine;
$fr_torna_indietro = mex("Torna indietro",$pag);
} # fine if ($origine)
else {
$action = "inizio.php";
$fr_torna_indietro = mex("Torna al menù principale",$pag);
} # fine else if ($origine)
echo "<br><br><form accept-charset=\"utf-8\" method=\"post\" action=\"$action\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"bkmm\" type=\"submit\"><div>$fr_torna_indietro</div></button>
</div></form><br><br>";
if ($verificare != "NO") echo "</div>";


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and ($priv_ins_nuove_prenota == "s" or $priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n"))
} # fine if ($id_utente)



?>
