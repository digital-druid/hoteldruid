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

$pag = "tabella3.php";
$titolo = "HotelDruid: Tabelle Mesi";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/funzioni_testo.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableregole = $PHPR_TAB_PRE."regole".$anno;


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_prenota_iniziate = substr($priv_mod_prenota,11,1);
$priv_mod_prenota_ore = substr($priv_mod_prenota,12,3);
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
$priv_prenota_gruppi = "NO";
$priv_app_gruppi = "NO";
if ($priv_vedi_tab_mesi == "q" or $priv_vedi_tab_mesi == "g") $priv_prenota_gruppi = "SI";
if ($priv_vedi_tab_mesi == "r" or $priv_vedi_tab_mesi == "g") $priv_app_gruppi = "SI";
if ($priv_vedi_tab_mesi == "q" or $priv_vedi_tab_mesi == "r" or $priv_vedi_tab_mesi == "g") { $priv_vedi_tab_mesi = "p"; $prendi_gruppi = "SI"; }

if ($priv_vedi_tab_mesi == "p") {
$regole1_consentite = risul_query($privilegi_annuali_utente,0,'regole1_consentite');
$attiva_regole1_consentite = substr($regole1_consentite,0,1);
if ($attiva_regole1_consentite != "n") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
$tariffe_consentite = risul_query($privilegi_annuali_utente,0,'tariffe_consentite');
$attiva_tariffe_consentite = substr($tariffe_consentite,0,1);
if ($attiva_tariffe_consentite == "s") {
$tariffe_consentite = explode(",",substr($tariffe_consentite,2));
unset($tariffe_consentite_vett);
for ($num1 = 0 ; $num1 < count($tariffe_consentite) ; $num1++) if ($tariffe_consentite[$num1]) $tariffe_consentite_vett[$tariffe_consentite[$num1]] = "SI";
} # fine if ($attiva_tariffe_consentite == "s")
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota_v = substr($priv_ins_prenota,0,1);
$priv_ins_assegnazione_app_v = substr($priv_ins_prenota,1,1);
$priv_mod_assegnazione_app_v = substr($priv_mod_prenota,2,1);
$priv_mod_prenotazioni_v = $priv_mod_prenotazioni;
} # fine if ($priv_vedi_tab_mesi == "p")
$priv_oscura_tab_mesi = substr($priv_vedi_tab,8,1);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)

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
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,"idgruppo");
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) {
$idutente_gruppo = risul_query($utenti_gruppo,$num2,"idutente");
if ($idutente_gruppo != $id_utente and !$utenti_gruppi[$idutente_gruppo]) {
$utenti_gruppi[$idutente_gruppo] = 1;

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
$costi_agg_consentiti_tmp = risul_query($priv_anno_ut_gr,0,"costi_agg_consentiti");
$attiva_costi_agg_consentiti_tmp = substr($costi_agg_consentiti_tmp,0,1);
if ($attiva_costi_agg_consentiti_tmp == "n") $attiva_costi_agg_consentiti_gr = "n";
if ($attiva_costi_agg_consentiti_gr == "s") {
$costi_agg_consentiti_tmp = explode(",",substr($costi_agg_consentiti_tmp,2));
for ($num3 = 0 ; $num3 < count($costi_agg_consentiti_tmp) ; $num3++) if ($costi_agg_consentiti_tmp[$num3]) $costi_agg_consentiti_vett_gr[$costi_agg_consentiti_tmp[$num3]] = "SI";
} # fine if ($attiva_costi_agg_consentiti_gr == "s")
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

} # fine if ($idutente_gruppo != $id_utente)
} # fine for $num2
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$vedi_clienti = "SI";
$priv_mod_prenotazioni = "s";
$priv_mod_prenota_iniziate = "s";
$priv_mod_prenota_ore = "000";
$priv_vedi_tab_mesi = "s";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI" and $priv_vedi_tab_mesi != "n") {

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
$installazione_subordinata = "SI";
$inserimento_nuovi_clienti = "NO";
$modifica_clienti = "NO";
$priv_ins_nuove_prenota = "n";
$priv_ins_spese = "n";
$priv_ins_entrate = "n";
$priv_ins_costi_agg = "n";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


#$tabelle_lock = array("$tableprenota");
$altre_tab_lock = array($tableprenota,$tablenometariffe,$tableperiodi,$tableappartamenti,$tableclienti,$tableregole,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

if ($priv_mod_prenota_iniziate != "s" or $priv_oscura_tab_mesi == "f") $id_periodo_corrente = calcola_id_periodo_corrente($anno);

$oggi = date("j/n/Y",(time() + (C_DIFF_ORE * 3600)));
$ora = date("H:i",(time() + (C_DIFF_ORE * 3600)));
echo "<small><small>".mex("situazione alle",$pag)." $ora ".mex("del",$pag)." $oggi</small></small><br>";

if ($anno_succ == "SI") { $mese = $mese + 12; }

$data_inizio_periodi = esegui_query("select * from $tableperiodi where idperiodi = 1");
$data_inizio_periodi = risul_query($data_inizio_periodi,0,'datainizio');
$data_inizio_periodi = explode("-",$data_inizio_periodi);
$mese_inizio_periodi = $data_inizio_periodi[1];
if ($mese < $mese_inizio_periodi) { $mese = $mese_inizio_periodi; }
$data_fine_periodi = esegui_query("select max(idperiodi) from $tableperiodi");
$id_data_fine_periodi = risul_query($data_fine_periodi,0,0);
$data_fine_periodi = esegui_query("select * from $tableperiodi where idperiodi = '$id_data_fine_periodi'");
$data_fine_periodi = risul_query($data_fine_periodi,0,'datainizio');
$data_fine_periodi = explode("-",$data_fine_periodi);
$mese_fine_periodi = $data_fine_periodi[1] + (($data_fine_periodi[0] - $anno) * 12);
if ($mese > $mese_fine_periodi) { $mese = $mese_fine_periodi; }

$tipo_periodi = "g";
$aggiunta_tronca = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'aggiunta_tronca_nomi_tab1' and idutente = '$id_utente'");
$aggiunta_tronca = risul_query($aggiunta_tronca,0,'valpersonalizza_num');
if ($aggiunta_tronca < -2) $aggiunta_tronca = -2;

$appartamenti = esegui_query("select * from $tableappartamenti order by idappartamenti");
$num_appartamenti = numlin_query($appartamenti);

if ($priv_vedi_tab_mesi == "p") {
include("./includes/funzioni_appartamenti.php");
if ($priv_app_gruppi != "SI") $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,$id_utente,$tableregole,$tablenometariffe);
else $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr,$id_utente,$tableregole,$tablenometariffe);
} # fine if ($priv_vedi_tab_mesi == "p")

$linee_ripeti_date_tab_mesi = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'linee_ripeti_date_tab_mesi' and idutente = '$id_utente'");
$linee_ripeti_date_tab_mesi = risul_query($linee_ripeti_date_tab_mesi,0,'valpersonalizza_num');
$mostra_giorni_tab_mesi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'mostra_giorni_tab_mesi' and idutente = '$id_utente'");
$mostra_giorni_tab_mesi = risul_query($mostra_giorni_tab_mesi,0,'valpersonalizza');

function ins_nome_giorno (&$var,$g_corr) {
$var .= "<small>";
if ($g_corr == "0") $var .= mex(" Do","giorni_mesi.php");
if ($g_corr == "1") $var .= mex(" Lu","giorni_mesi.php");
if ($g_corr == "2") $var .= mex(" Ma","giorni_mesi.php");
if ($g_corr == "3") $var .= mex(" Me","giorni_mesi.php");
if ($g_corr == "4") $var .= mex(" Gi","giorni_mesi.php");
if ($g_corr == "5") $var .= mex(" Ve","giorni_mesi.php");
if ($g_corr == "6") $var .= mex(" Sa","giorni_mesi.php");
$var = str_replace(" ","<br>",$var);
$var .= "</small>";
} # fine function ins_nome_giorno

$num_righe_app_max = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'num_linee_tab2_prenota' and idutente = '$id_utente'");
$num_righe_app_max = risul_query($num_righe_app_max,0,'valpersonalizza_num');
$num_tabelle = floor($num_righe_app_max/$num_appartamenti);
$mese_ini_for = $mese;
$mese_fine_for = $mese;
$mese_da_aggiungere = "dopo";
for ($num1 = 2 ; $num1 <= $num_tabelle ; $num1++) {
if ($mese_fine_for >= $mese_fine_periodi) $mese_da_aggiungere = "prima";
if ($mese_da_aggiungere == "prima") {
if ($mese_ini_for <= $mese_inizio_periodi) $mese_da_aggiungere = "dopo";
else $mese_ini_for--;
} # fine if ($mese_da_aggiungere == "prima")
if ($mese_da_aggiungere == "dopo" and $mese_fine_for < $mese_fine_periodi) $mese_fine_for++;
if ($mese_da_aggiungere == "prima") $mese_da_aggiungere = "dopo";
else $mese_da_aggiungere = "prima";
} # fine for $num1

if ($tutti_mesi) {
$mese_ini_for = $mese_inizio_periodi;
$mese_fine_for = $mese_fine_periodi;
$orig_tutti_mesi = "&amp;tutti_mesi=SI";
} # fine if ($tutti_mesi)

for ($mese2 = $mese_ini_for ; $mese2 <= $mese_fine_for ; $mese2 = $mese2 + 1) {

if ($mese2 > 48) {
$mese_mostra = $mese2 - 48;
$anno_mostra = $anno + 4;
} # fine if ($mese2 > 48)
else {
if ($mese2 > 36) {
$mese_mostra = $mese2 - 36;
$anno_mostra = $anno + 3;
} # fine if ($mese2 > 36)
else {
if ($mese2 > 24) {
$mese_mostra = $mese2 - 24;
$anno_mostra = $anno + 2;
} # fine if ($mese2 > 24)
else {
if ($mese2 > 12) {
$mese_mostra = $mese2 - 12;
$anno_mostra = $anno + 1;
} # fine if ($mese2 > 12)
else {
$mese_mostra = $mese2;
$anno_mostra = $anno;
} # fine else if ($mese2 > 12)
} # fine else if ($mese2 > 24)
} # fine else if ($mese2 > 36)
} # fine else if ($mese2 > 48)

if ($mese2 != $mese_ini_for) echo "<table><tr><td style=\"height: 6px;\"></td></tr></table>";
echo "<table style=\"margin-left: auto; margin-right: auto;\"><tr><td style=\"width: 100px;\" align=\"right\">";
if ($mese != 1) {
$mese_indietro = $mese - 1; 
echo "<a href=\"tabella3.php?anno=$anno&amp;id_sessione=$id_sessione&amp;mese=$mese_indietro\"><--</a>&nbsp;&nbsp;&nbsp;&nbsp;";
} # fine if ($mese != 1)
echo "</td><td align=\"center\">
<big><b>".mex("Tabella prenotazioni del",$pag)." $mese_mostra-$anno_mostra.</b></big>
</td><td style=\"width: 100px;\" align=\"left\">";
if ($mese != 25) {
$mese_avanti = $mese + 1; 
echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"tabella3.php?anno=$anno&amp;id_sessione=$id_sessione&amp;mese=$mese_avanti\">--></a>";
} # fine if ($mese != 25)
echo "</td></tr></table>
<div class=\"tab_cont\">
<table class=\"m2\" style=\"margin-left: auto; margin-right: auto;\" border=2 cellspacing=0 cellpadding=1>";
if ($mese2 != 1 and $tipo_periodi != "g") { $mese_inizio_tab = $mese2 - 1; }
else { $mese_inizio_tab = $mese2; }
$data_inizio_tab = date("Y-m-d" , mktime(0,0,0,$mese_inizio_tab,1,$anno));
$data_inizio_tab = esegui_query("select * from $tableperiodi where datainizio >= '$data_inizio_tab' order by idperiodi");
$id_data_inizio_tab = risul_query($data_inizio_tab,0,'idperiodi');
#if ($mese != $mese_inizio_periodi and $mese != ($mese_inizio_periodi + 1)) $id_data_inizio_tab = $id_data_inizio_tab - 1;
if ($mese2 != 25 and $tipo_periodi != "g") { $mese_fine_tab = $mese2 + 1; }
else {$mese_fine_tab = $mese2; }
$data_fine_tab = date("Y-m-d" , mktime(0,0,0,$mese_fine_tab,31,$anno));
$data_fine_tab = esegui_query("select * from $tableperiodi where datainizio <= '$data_fine_tab' order by idperiodi");
$num_date = numlin_query($data_fine_tab);
$num_date = $num_date - 1;
$id_data_fine_tab = risul_query($data_fine_tab,$num_date,'idperiodi');
$g_fine_tab = risul_query($data_fine_tab,$num_date,'datafine');
$g_fine_tab = explode("-",$g_fine_tab);
$g_fine_tab = $g_fine_tab[2];
if ($g_fine_tab >= 2) { $id_data_fine_tab = $id_data_fine_tab - $g_fine_tab + 1; }
$num_colonne = $id_data_fine_tab - $id_data_inizio_tab + 1;

# controllo se vi sono prenotazioni in appartamenti cancellati o non mostrati
$num_appartamenti_cancellati = 0;
$query_prenota_app_canc = "select * from $tableprenota where iddatainizio <= '$id_data_fine_tab' and iddatafine >= '$id_data_inizio_tab'";
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1 = $num1 + 1) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO") $query_prenota_app_canc .= " and idappartamenti != '$id_appartamento'";
} # fine for $num1
if ($priv_vedi_tab_mesi == "p") {
$query_prenota_app_canc .= " and ( utente_inserimento = '$id_utente'";
if ($priv_prenota_gruppi == "SI") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $query_prenota_app_canc .= " or utente_inserimento = '$idut_gr'";
} # fine if ($priv_prenota_gruppi == "SI")
$query_prenota_app_canc .= " )";
} # fine if ($priv_vedi_tab_mesi == "p")
$prenota_app_canc = esegui_query($query_prenota_app_canc);
$num_prenota_app_canc = numlin_query($prenota_app_canc);
$num_app_canc = 0;
unset($app_canc_trovato);
for ($num1 = 0 ; $num1 < $num_prenota_app_canc; $num1 = $num1 + 1) {
$idapp_prenota_app_canc = risul_query($prenota_app_canc,$num1,'idappartamenti');
if ($app_canc_trovato[$idapp_prenota_app_canc] != "SI") {
$app_canc_trovato[$idapp_prenota_app_canc] = "SI";
$app_canc[$num_app_canc] = $idapp_prenota_app_canc;
$num_app_canc++;
} # fine if ($app_canc_trovato[$idapp_prenota_app_canc] != "SI")
} # fine for $num1

$linea_date = "<tr><td>&nbsp;</td>";
$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$anno' and idutente = '$id_utente'");
if (numlin_query($giorno_vedi_ini_sett) == 1) $giorno_vedi_ini_sett = risul_query($giorno_vedi_ini_sett,0,'valpersonalizza_num');
else $giorno_vedi_ini_sett = 0;
for ($num1 = 0 ; $num1 < $num_colonne ; $num1 = $num1 + 1) {
$id_settimana = $id_data_inizio_tab + $num1;
$riga_setimana = esegui_query("select * from $tableperiodi where idperiodi = '$id_settimana'");
$inizio_settimana = risul_query($riga_setimana,0,'datainizio');
$inizio_settimana = explode("-",$inizio_settimana);
$g_inizio_settimana = $inizio_settimana[2];
$m_inizio_settimana = $inizio_settimana[1];
$a_inizio_settimana = $inizio_settimana[0];
if ($g_inizio_settimana == "01") $g_inizio_settimana = "1";
$giorno_sett_corr = date("w" , mktime(0,0,0,$m_inizio_settimana,$g_inizio_settimana,$a_inizio_settimana));
if ($mostra_giorni_tab_mesi == "SI") ins_nome_giorno($g_inizio_settimana,$giorno_sett_corr);
if ($giorno_sett_corr == $giorno_vedi_ini_sett) $g_inizio_settimana = "<div style=\"display: inline; color: red;\"><b>".$g_inizio_settimana."</b></div>";
$linea_date .= "<td align=\"center\"";
#if ($num1 != 0 and $num1 != ($num_colonne - 1)) echo " colspan=2";
if ($num1 != 0) $linea_date .= " colspan=2";
#if ($m_inizio_settimana == $mese_mostra or $m_fine_settimana == $mese_mostra) { echo " bgcolor =\"#DAEDFF\""; }
$linea_date .= ">$g_inizio_settimana</td>";
} # fine for $num1

$fine_settimana = risul_query($riga_setimana,0,'datafine');
$fine_settimana = explode("-",$fine_settimana);
$g_fine_settimana = $fine_settimana[2];
$m_fine_settimana = $fine_settimana[1];
if ($g_fine_settimana == "01") $g_fine_settimana = "1";
$a_fine_settimana = $fine_settimana[0];
$giorno_sett_corr = date("w" , mktime(0,0,0,$m_fine_settimana,$g_fine_settimana,$a_fine_settimana));
if ($mostra_giorni_tab_mesi == "SI") ins_nome_giorno($g_fine_settimana,$giorno_sett_corr);
if ($giorno_sett_corr == $giorno_vedi_ini_sett) $g_fine_settimana = "<div style=\"display: inline; color: red;\"><b>".$g_fine_settimana."</b></div>";
$linea_date .= "<td align=\"center\">$g_fine_settimana</td>";
$linea_date .= "<td>&nbsp;</td></tr>";
echo $linea_date;

$num_ripeti = 1;

for ($num1 = 0 ; $num1 < ($num_appartamenti + $num_app_canc) ; $num1 = $num1 + 1) {
if ($num1 < $num_appartamenti) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
$condizione_prenota_propria = "";
} # fine if ($num1 < $num_appartamenti)
else {
$id_appartamento = $app_canc[($num1 - $num_appartamenti)];
if ($priv_vedi_tab_mesi == "p") {
$condizione_prenota_propria = " and ( utente_inserimento = '$id_utente'";
if ($priv_prenota_gruppi == "SI") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_prenota_propria .= " or utente_inserimento = '$idut_gr'";
} # fine if ($priv_prenota_gruppi == "SI")
$condizione_prenota_propria .= " )";
} # fine if ($priv_vedi_tab_mesi == "p")
else $condizione_prenota_propria = "";
} # fine else if ($num1 < $num_appartamenti)
if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO" or $num1 >= $num_appartamenti) {

if ($num1 < $num_appartamenti) echo "<tr><td>";
else echo "<tr><td style=\"color: red;\">";
if (num_caratteri_testo($id_appartamento) > 2) echo "<small><small>";
else echo "<small>";
echo "$id_appartamento";
if (num_caratteri_testo($id_appartamento) > 2) echo "</small></small></td>";
else echo "</small></td>";
$prenotazione_presente = esegui_query("select * from $tableprenota where idappartamenti = '$id_appartamento' and iddatainizio <= '".($id_data_inizio_tab + $num_colonne - 1)."' and iddatafine >= '$id_data_inizio_tab'$condizione_prenota_propria order by iddatainizio");
$num_prenotazione_presente = numlin_query($prenotazione_presente);
$prenota_succ = 0;
if ($num_prenotazione_presente > 0) $ini_prenota_succ = risul_query($prenotazione_presente,0,'iddatainizio');
else $ini_prenota_succ = $id_data_inizio_tab + $num_colonne + 1;

for ($num2 = 0 ; $num2 < $num_colonne ; $num2 = $num2 + 1) {
$id_settimana = $id_data_inizio_tab + $num2;
if ($id_settimana >= $ini_prenota_succ) {
$esiste = 1;
$prenota_corr = $prenota_succ;
$prenota_succ++;
if ($num_prenotazione_presente > $prenota_succ) $ini_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'iddatainizio');
else {
$ini_prenota_succ = (risul_query($prenotazione_presente,$prenota_corr,'iddatafine') + 1);
if (($id_data_inizio_tab + $num_colonne + 1) > $ini_prenota_succ) $ini_prenota_succ = $id_data_inizio_tab + $num_colonne + 1;
} # fine else if ($num_prenotazione_presente > $prenota_succ)
} # fine if ($id_settimana >= $ini_prenota_succ)
else $esiste = 0;

if ($esiste == 1) $utente_inserimento = risul_query($prenotazione_presente,$prenota_corr,'utente_inserimento');
else $utente_inserimento = $id_utente;

if ($priv_vedi_tab_mesi == "p" and (($utente_inserimento != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento])) or ($periodo_consentito_app[$id_appartamento][$id_settimana] == "NO" and $esiste != 1))) {
if ($utente_inserimento != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento])) {
$id_inizio_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatainizio');
$id_fine_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatafine');
if ($id_inizio_prenota > $id_data_inizio_tab) $id_inizio = $id_inizio_prenota;
else $id_inizio = $id_data_inizio_tab;
if ($id_fine_prenota < $id_data_fine_tab) $id_fine = $id_fine_prenota;
else $id_fine = $id_data_fine_tab;
$colonne = $id_fine - $id_inizio + 1;
} # fine if ($utente_inserimento != $id_utente and...
else {
$id_inizio = $id_settimana;
$id_fine = $id_settimana;
$colonne = 1;
} # fine else if ($utente_inserimento != $id_utente)
$fatto = "NO";
if ($id_fine == $id_data_fine_tab) $fatto = "SI";
while ($fatto == "NO") {
$id_inizio2 = $id_fine + 1;
if ($id_inizio2 >= $ini_prenota_succ) {
$utente_inserimento2 = risul_query($prenotazione_presente,$prenota_succ,'utente_inserimento');
if ($utente_inserimento2 != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento2])) {
$id_fine_prenota = risul_query($prenotazione_presente,$prenota_succ,'iddatafine');
if ($id_fine_prenota < $id_data_fine_tab) $id_fine = $id_fine_prenota;
else {
$id_fine = $id_data_fine_tab;
$id_fine_prenota = $id_fine;
$fatto = "SI";
} # fine else if ($id_fine_prenota < $id_data_fine_tab)
$colonne = $id_fine - $id_inizio + 1;
$prenota_corr = $prenota_succ;
$prenota_succ++;
if ($num_prenotazione_presente > $prenota_succ) $ini_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'iddatainizio');
else {
$ini_prenota_succ = (risul_query($prenotazione_presente,$prenota_corr,'iddatafine') + 1);
if (($id_data_inizio_tab + $num_colonne + 1) > $ini_prenota_succ) $ini_prenota_succ = $id_data_inizio_tab + $num_colonne + 1;
} # fine else if ($num_prenotazione_presente > $prenota_succ)
} # fine if ($utente_inserimento2 != $id_utente)
else $fatto = "SI";
} # fine if ($id_inizio2 >= $ini_prenota_succ)
else {
if ($periodo_consentito_app[$id_appartamento][$id_inizio2] == "NO") {
$id_fine++;
if ($id_fine == $id_data_fine_tab) $fatto = "SI";
$colonne++;
} # fine if ($periodo_consentito_app[$id_appartamento][$$id_inizio2] == "NO")
else $fatto = "SI";
} # fine else if (numlin_query($prenotazione_successiva) == 1)
} # fine while ($fatto == "NO")
if ($priv_oscura_tab_mesi != "v" and $priv_oscura_tab_mesi != "f") {
$colonne_s = $colonne * 2;
echo "<td align=\"center\" colspan=\"$colonne_s\"><b>-</b></td>";
} # fine if ($priv_oscura_tab_mesi != "v" and $priv_oscura_tab_mesi != "f")
if ($priv_oscura_tab_mesi == "v") {
for ($num3 = 0 ; $num3 < $colonne ; $num3++) echo "<td colspan=\"2\">&nbsp;</td>";
} # fine if ($priv_oscura_tab_mesi == "v")
if ($priv_oscura_tab_mesi == "f") {
for ($num3 = 0 ; $num3 < $colonne ; $num3++) {
if ($id_periodo_corrente > ($id_settimana + $num3)) echo "<td colspan=\"2\">&nbsp;</td>";
else {
$colonne_s = ($colonne - $num3) * 2;
echo "<td align=\"center\" colspan=\"$colonne_s\"><b>-</b></td>";
break;
} # fine else if ($id_periodo_corrente > ($id_settimana + $num3))
} # fine for $num3
} # fine if ($priv_oscura_tab_mesi == "f")
$num2 = $num2 + $colonne - 1;
} # fine if ($priv_vedi_tab_mesi == "p" and ($utente_inserimento != $id_utente or...
else {

if ($esiste == 1) {
$id_prenota = risul_query($prenotazione_presente,$prenota_corr,'idprenota');
$id_clienti = risul_query($prenotazione_presente,$prenota_corr,'idclienti');
if ($id_clienti) {
$cognome = esegui_query("select cognome,utente_inserimento from $tableclienti where idclienti = '$id_clienti'");
$mostra_cliente = "SI";
if ($vedi_clienti == "NO") $mostra_cliente = "NO";
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$utente_inserimento_cli = risul_query($cognome,0,'utente_inserimento');
if ($vedi_clienti == "PROPRI" and $utente_inserimento_cli != $id_utente) $mostra_cliente = "NO";
if ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento_cli]) $mostra_cliente = "NO";
} # fine if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI")
if ($mostra_cliente == "NO") $cognome = $id_prenota;
else $cognome = risul_query($cognome,0,'cognome');
} # fine if ($id_clienti)
else $cognome = "?";

$colore = "";
$pagato = risul_query($prenotazione_presente,$prenota_corr,'pagato');
if (!$pagato) { $pagato = 0; }
$caparra = risul_query($prenotazione_presente,$prenota_corr,'caparra');
if (!$caparra) { $caparra = 0; }
$costo_tot = risul_query($prenotazione_presente,$prenota_corr,'tariffa_tot');
if ($pagato < $caparra) { $colore = "#CC0000"; }
else { if ($pagato < $costo_tot) { $colore = "#FFCC00"; } }

$id_inizio_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatainizio');
$id_fine_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatafine');
if ($id_inizio_prenota > $id_data_inizio_tab) { $id_inizio = $id_inizio_prenota; }
else { $id_inizio = $id_data_inizio_tab; }
if ($id_fine_prenota < $id_data_fine_tab) { $id_fine = $id_fine_prenota; }
else { $id_fine = $id_data_fine_tab; }
$colonne = $id_fine - $id_inizio + 1;

$link_modifica = "SI";
if ($priv_mod_prenotazioni == "n") $link_modifica = "NO";
if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g") {
$utente_inserimento = risul_query($prenotazione_presente,$prenota_corr,'utente_inserimento');
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $link_modifica = "NO";
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $link_modifica = "NO";
} # fine if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g")
if ($priv_mod_prenota_iniziate != "s" and $id_periodo_corrente >= $id_inizio_prenota) $link_modifica = "NO";
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = risul_query($prenotazione_presente,$prenota_corr,'datainserimento');
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $link_modifica = "NO";
} # fine if ($priv_mod_prenota_ore != "000")
if ($link_modifica == "SI" and $id_clienti) {
$link_modifica_inizio = "<a href=\"modifica_prenota.php?id_prenota=$id_prenota&amp;anno=$anno&amp;id_sessione=$id_sessione&amp;mese=$mese&amp;origine=tabella3.php$orig_tutti_mesi\">";
$link_modifica_fine = "</a>";
} # fine if ($link_modifica == "SI" and $id_clienti)
else {
unset($link_modifica_inizio);
unset($link_modifica_fine);
} # fine else if ($link_modifica == "SI" and $id_clienti)

$riduci_font = "";
$lung_cognome = num_caratteri_testo($cognome);
$lung_freccia = 0;
if ($id_fine_prenota > $id_data_fine_tab or $id_inizio_prenota < $id_data_inizio_tab) $lung_freccia = 3;
$lung_non_ridotta = (3+$aggiunta_tronca)*$colonne - $lung_freccia;
if ($lung_cognome > $lung_non_ridotta) $riduci_font = "SI";
$lung_non_tronca = (3+$aggiunta_tronca)*$colonne;
if ($lung_freccia == 3) $lung_non_tronca = $lung_non_tronca - 1;
if ($lung_non_tronca < 1) $lung_non_tronca = 1;
if ($lung_cognome > ($lung_non_tronca+1) and $cognome != "&nbsp;") {
$link_modifica_inizio = str_replace("<a href","<a title=\"".htmlspecialchars($cognome)."\" href",$link_modifica_inizio);
$cognome = tronca_testo($cognome,0,$lung_non_tronca).".";
} # fine if ($lung_cognome > ($lung_non_tronca+1) and $cognome != "&nbsp;")
echo "<td";
#if ($colore) { echo " bgcolor =\"$colore\""; }
if ($riduci_font) echo " style=\"padding: 0;\"";
$colonne_mostra = $colonne * 2;
echo " align=\"center\" colspan=\"$colonne_mostra\">";
if ($riduci_font) echo "<small><small>";
else echo "<small>";
if ($id_inizio_prenota < $id_data_inizio_tab) { echo "<- "; }
echo "$link_modifica_inizio$cognome$link_modifica_fine";
if ($id_fine_prenota > $id_data_fine_tab) { echo " ->"; }
if ($riduci_font) echo "</small></small>";
else echo "</small>";
echo "</td>";
$num2 = $num2 + $colonne - 1;
} # fine if ($esiste == 1)
else {
if ($esiste == 0) { echo "<td colspan=2>&nbsp;</td>"; }
else { echo "<td>".mex("ERRORE",$pag)."</td>"; }
} # fine else if ($esiste == 1)

} # fine else if ($priv_vedi_tab_mesi == "p" and ($utente_inserimento != $id_utente or...
} # fine for $num2

if ($num1 < $num_appartamenti) echo "<td>";
else echo "<td style=\"color: red;\">";
if (num_caratteri_testo($id_appartamento) > 2) echo "<small><small>";
else echo "<small>";
echo "$id_appartamento";
if (num_caratteri_testo($id_appartamento) > 2) echo "</small></small></td>";
else echo "</small></td>";
echo "</tr>";

if ($num_ripeti == $linee_ripeti_date_tab_mesi) {
echo $linea_date;
$num_ripeti = 1;
} # fine if ($num_ripeti == $linee_ripeti_date_tab_mesi)
else $num_ripeti++;
} # fine if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO")
} # fine for $num1

echo "</table></div>";
} # fine for $mese2


unlock_tabelle($tabelle_lock);

echo "<table><tr><td style=\"height: 2px;\"></td></tr></table>
<table style=\"margin-left: auto; margin-right: auto;\"><tr><td style=\"width: 100px;\" align=\"right\">";
if ($mese != 1) {
$mese_indietro = $mese - 1; 
echo "<a href=\"tabella3.php?anno=$anno&amp;id_sessione=$id_sessione&amp;mese=$mese_indietro\"><--</a>&nbsp;&nbsp;&nbsp;&nbsp;";
} # fine if ($mese != 1)
echo "</td><td align=\"center\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"tabella.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"mese\" value=\"$mese\">
<button class=\"vmon\" type=\"submit\"><div>".mex("Visualizza la tabella normale",$pag)."</div></button>
</div></form></td><td style=\"width: 100px;\" align=\"left\">";
if ($mese != 25) {
$mese_avanti = $mese + 1; 
echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"tabella3.php?anno=$anno&amp;id_sessione=$id_sessione&amp;mese=$mese_avanti\">--></a>";
} # fine if ($mese != 25)
echo "</td></tr><tr><td style=\"height: 2px;\"></td></tr>";

if (!$tutti_mesi) echo "<tr><td></td><td align=\"center\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"tabella3.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"mese\" value=\"$mese\">
<input type=\"hidden\" name=\"tutti_mesi\" value=\"1\">
<button class=\"amon\" type=\"submit\"><div>".mex("Visualizza tutti i mesi",$pag)."</div></button>
</div></form></td><td></td></tr>";

echo "<tr><td style=\"height: 2px;\"></td></tr><tr><td></td><td align=\"center\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"bkmm\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
</div></form></td><td></td></tr><tr><td style=\"height: 20px;\"></td></tr></table>";




if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and $priv_vedi_tab_mesi != "n")
} # fine if ($id_utente)



?>
