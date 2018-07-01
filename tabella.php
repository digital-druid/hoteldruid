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

$pag = "tabella.php";
$titolo = "HotelDruid: Tabella Mese";
$base_js = 1;
$drag_drop = 1;

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
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;

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
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_periodi_passati = substr($priv_ins_prenota,8,1);
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_date = substr($priv_mod_prenota,1,1);
$priv_mod_assegnazione_app = substr($priv_mod_prenota,2,1);
$priv_mod_commento = substr($priv_mod_prenota,5,1);
$priv_mod_sconto = substr($priv_mod_prenota,6,1);
$priv_mod_caparra = substr($priv_mod_prenota,7,1);
$priv_mod_pagato = substr($priv_mod_prenota,10,1);
$priv_mod_prenota_iniziate = substr($priv_mod_prenota,11,1);
$priv_mod_prenota_ore = substr($priv_mod_prenota,12,3);
$priv_mod_checkin = substr($priv_mod_prenota,20,1);
$priv_mod_prenota_comp = substr($priv_mod_prenota,23,1);
$priv_mod_orig_prenota = substr($priv_mod_prenota,24,1);
$priv_vedi_commento = substr($priv_mod_prenota,25,1);
$priv_vedi_commenti_pers = substr($priv_mod_prenota,26,1);
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
$priv_ins_nuove_prenota_v = substr($priv_ins_prenota,0,1);
$priv_ins_assegnazione_app_v = substr($priv_ins_prenota,1,1);
$priv_mod_assegnazione_app_v = substr($priv_mod_prenota,2,1);
$priv_mod_prenotazioni_v = $priv_mod_prenotazioni;
} # fine if ($priv_vedi_tab_mesi == "p")
$priv_oscura_tab_mesi = substr($priv_vedi_tab,8,1);
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
unset($contratti_consentiti_vett);
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1]) $contratti_consentiti_vett[$contratti_consentiti[$num1]] = "SI";
} # fine if ($attiva_contratti_consentiti == "s")
$cassa_pagamenti = risul_query($privilegi_annuali_utente,0,'cassa_pagamenti');
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
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) {
$idutente_gruppo = risul_query($utenti_gruppo,$num2,'idutente');
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
$priv_ins_periodi_passati = "s";
$priv_mod_prenotazioni = "s";
$priv_mod_date = "s";
$priv_mod_assegnazione_app = "s";
$priv_mod_commento = "s";
$priv_mod_sconto = "s";
$priv_mod_caparra = "s";
$priv_mod_pagato = "s";
$priv_mod_prenota_iniziate = "s";
$priv_mod_prenota_ore = "000";
$priv_mod_checkin = "s";
$priv_mod_prenota_comp = "s";
$priv_mod_orig_prenota = "s";
$priv_vedi_commento = "s";
$priv_vedi_commenti_pers = "s";
$priv_vedi_tab_mesi = "s";
$attiva_contratti_consentiti = "n";
$cassa_pagamenti = "";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI" and $priv_vedi_tab_mesi != "n") {

if ($priv_vedi_commenti_pers = "s") $priv_mod_commenti_pers = "s";
else $priv_mod_commenti_pers = "n";

if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
$installazione_subordinata = "SI";
$inserimento_nuovi_clienti = "NO";
$modifica_clienti = "NO";
$priv_ins_nuove_prenota = "n";
$priv_mod_date = "n";
$priv_mod_assegnazione_app = "n";
$priv_mod_commento = "n";
$priv_mod_commenti_pers = "n";
$priv_mod_sconto = "n";
$priv_mod_caparra = "n";
$priv_mod_pagato = "n";
$priv_mod_checkin = "n";
$priv_mod_prenota_comp = "n";
$priv_mod_orig_prenota = "n";
$priv_ins_spese = "n";
$priv_ins_entrate = "n";
$priv_ins_costi_agg = "n";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))



$tipo_periodi = esegui_query("select * from $tableanni where idanni = '$anno'");
$tipo_periodi = risul_query($tipo_periodi,0,'tipo_periodi');
$attiva_checkin = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'attiva_checkin' and idutente = '$id_utente'");
$attiva_checkin = risul_query($attiva_checkin,0,'valpersonalizza');

$colori_tab_mesi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'colori_tab_mesi' and idutente = '$id_utente'");
$colori_tab_mesi = explode(",",risul_query($colori_tab_mesi,0,'valpersonalizza'));
$colore_trasp = $colori_tab_mesi[0];
$colore_giallo = $colori_tab_mesi[1];
$colore_arancione = $colori_tab_mesi[2];
$colore_rosso = $colori_tab_mesi[3];

$mostra_giorni_tab_mesi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'mostra_giorni_tab_mesi' and idutente = '$id_utente'");
$mostra_giorni_tab_mesi = risul_query($mostra_giorni_tab_mesi,0,'valpersonalizza');
if ($tipo_periodi == "g") {
$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$anno' and idutente = '$id_utente'");
if (numlin_query($giorno_vedi_ini_sett) == 1) $giorno_vedi_ini_sett = risul_query($giorno_vedi_ini_sett,0,'valpersonalizza_num');
else $giorno_vedi_ini_sett = 0;
} # fine if ($tipo_periodi == "g")

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

function crea_data_per_linea ($n_col,$num_colonne,$id_settimana,&$d_inizio_settimana,&$d_fine_settimana,&$m_inizio_settimana,&$m_fine_settimana,&$array_date_col_js,$tipo_periodi,$mostra_giorni_tab_mesi,$giorno_vedi_ini_sett) {
global $tableperiodi;
$riga_setimana = esegui_query("select * from $tableperiodi where idperiodi = '$id_settimana'");
if (numlin_query($riga_setimana)) {
$inizio_settimana = risul_query($riga_setimana,0,'datainizio');
$array_date_col_js .= "ArDaCo[".($n_col + 1)."] = '$inizio_settimana';
";
$inizio_settimana = explode("-",$inizio_settimana);
$g_inizio_settimana = $inizio_settimana[2];
$m_inizio_settimana = $inizio_settimana[1];
$fine_settimana = risul_query($riga_setimana,0,'datafine');
if ($n_col == ($num_colonne - 1)) $array_date_col_js .= "ArDaCo[".($n_col + 2)."] = '$fine_settimana';
";
$fine_settimana = explode("-",$fine_settimana);
$g_fine_settimana = $fine_settimana[2];
$m_fine_settimana = $fine_settimana[1];
$d_inizio_settimana = "$g_inizio_settimana-$m_inizio_settimana";
if ($tipo_periodi == "g") {
$d_inizio_settimana = $g_inizio_settimana;
$a_inizio_settimana = $inizio_settimana[0];
$giorno_sett_corr = date("w",mktime(0,0,0,$m_inizio_settimana,$g_inizio_settimana,$a_inizio_settimana));
if ($mostra_giorni_tab_mesi == "SI") ins_nome_giorno($d_inizio_settimana,$giorno_sett_corr);
if ($giorno_sett_corr == $giorno_vedi_ini_sett) $d_inizio_settimana = "<b style=\"color: red;\">".$d_inizio_settimana."</b>";
} # fine if ($tipo_periodi == "g")
$d_fine_settimana = "$g_fine_settimana-$m_fine_settimana";
if ($tipo_periodi == "g") {
$d_fine_settimana = $g_fine_settimana;
$a_fine_settimana = $fine_settimana[0];
$giorno_sett_corr = date("w",mktime(0,0,0,$m_fine_settimana,$g_fine_settimana,$a_fine_settimana));
if ($mostra_giorni_tab_mesi == "SI") ins_nome_giorno($d_fine_settimana,$giorno_sett_corr);
if ($giorno_sett_corr == $giorno_vedi_ini_sett) $d_fine_settimana = "<b style=\"color: red;\">".$d_fine_settimana."</b>";
} # fine if ($tipo_periodi == "g")
} # fine (numlin_query($riga_setimana))
else $d_inizio_settimana = 0;
} # fine function crea_data_per_linea

function colore_prenotazione ($prenota,$num_pren) {
global $tableprenota,$colore_trasp,$colore_giallo,$colore_arancione,$colore_rosso;
$pagato = risul_query($prenota,$num_pren,'pagato',$tableprenota);
$confermato = risul_query($prenota,$num_pren,'conferma',$tableprenota);
$confermato = substr($confermato,0,1);
if (!$pagato) $pagato = 0;
$caparra = risul_query($prenota,$num_pren,'caparra',$tableprenota);
if (!$caparra) $caparra = 0;
$costo_tot = risul_query($prenota,$num_pren,'tariffa_tot',$tableprenota);
$colore = $colore_trasp; #celeste
if ($pagato < $costo_tot) {
$colore = $colore_giallo; #giallo
if ($pagato < $caparra) $colore = $colore_arancione; # arancione
if ($confermato != "S") $colore = $colore_rosso; # rosso
} # fine if ($pagato < $costo_tot)
return $colore;
} # fine function colore_prenotazione



$manda_xml = 0;
$dati_xml = "";

if ($idg_agg and controlla_num_pos(substr($idg_agg,2)) == "SI") {
$manda_xml = 1;
$tabelle_lock = "";
if ($priv_vedi_tab_mesi != "p") $altre_tab_lock = array($tableprenota,$tableperiodi,$tableappartamenti,$tableclienti,$tableregole,$tablepersonalizza);
else $altre_tab_lock = array($tableprenota,$tablenometariffe,$tableperiodi,$tableappartamenti,$tableclienti,$tableregole,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

$appartamenti = esegui_query("select * from $tableappartamenti order by idappartamenti");
$num_appartamenti = numlin_query($appartamenti);
if ($priv_vedi_tab_mesi == "p") {
include("./includes/funzioni_appartamenti.php");
if ($priv_app_gruppi != "SI") $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,$id_utente,$tableregole,$tablenometariffe);
else $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr,$id_utente,$tableregole,$tablenometariffe);
} # fine if ($priv_vedi_tab_mesi == "p")

$ncol = 0;
if (substr($idg_agg,0,1) == "7") $loop = 7;
else $loop = 1;
$idg_agg = (substr($idg_agg,1));
if (substr($idg_agg,0,1) == "d") $direz = "dx";
else $direz = "sx";
$idg_agg = (substr($idg_agg,1));
if ($priv_mod_prenota_iniziate != "s" or $priv_oscura_tab_mesi == "f") $id_periodo_corrente = calcola_id_periodo_corrente($anno);

if ($direz == "dx") {
$idg_ini = $idg_agg;
$idg_fine = $idg_agg + $loop - 1;
} # fine if ($direz == "dx")
else {
$idg_ini = $idg_agg - $loop + 1;
$idg_fine = $idg_agg;
} # fine else if ($direz == "dx")
unset($reg1_chiuso);
$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != '' and motivazione2 = 'x' and iddatainizio <= '$idg_fine' and iddatafine >= '$idg_ini' ");
$num_app_agenzia = numlin_query($app_agenzia);
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$ini_chiuso = risul_query($app_agenzia,$num1,'iddatainizio');
$fine_chiuso = risul_query($app_agenzia,$num1,'iddatafine');
$app_chiuso = risul_query($app_agenzia,$num1,'app_agenzia');
for ($num2 = $ini_chiuso ; $num2 <= $fine_chiuso ; $num2++) $reg1_chiuso[$app_chiuso][$num2] = 1;
} # fine for $num1

for ($ncol = 1 ; $ncol <= $loop ; $ncol++) {

if ($ncol > 1) {
if ($direz == "dx") $idg_agg++;
else $idg_agg--;
} # fine if ($ncol > 1)
$array_date_col_js = "";
crea_data_per_linea(1,2,$idg_agg,$d_inizio_settimana,$d_fine_settimana,$m_inizio_settimana,$m_fine_settimana,$array_date_col_js,$tipo_periodi,$mostra_giorni_tab_mesi,$giorno_vedi_ini_sett);
if ($d_inizio_settimana) {
$d_inizio_settimana = str_replace("\"","&quot;",str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&","&amp;",$d_inizio_settimana))));
$d_fine_settimana = str_replace("\"","&quot;",str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&","&amp;",$d_fine_settimana))));
$djs = explode("'",$array_date_col_js);
if ($direz == "sx") $djs = $djs[1];
else $djs = $djs[(count($djs) - 2)];
$prenota_ini_fine = esegui_query("select * from $tableprenota where iddatainizio = '$idg_agg' or iddatafine = '$idg_agg' ");
$num_prenota_if = numlin_query($prenota_ini_fine);
$iddatainizio = "";
$iddatafine = "";
$dati_prenota = "";
for ($num1 = 0 ; $num1 < $num_prenota_if ; $num1++) {
$idprenota = "";
$dati_prenota_extra = "";
$id_appartamento = risul_query($prenota_ini_fine,$num1,'idappartamenti');
if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO") {

$prenota_consentita = 1;
$utente_inserimento = risul_query($prenota_ini_fine,$num1,'utente_inserimento');
if ($priv_vedi_tab_mesi == "p" and ($utente_inserimento != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento]))) {
$prenota_consentita = 0;
$cond_per_corr = "";
if ($priv_oscura_tab_mesi == "v") continue;
if ($priv_oscura_tab_mesi == "f") {
$iddatafine = risul_query($prenota_ini_fine,$num1,'iddatafine');
if ($id_periodo_corrente > $iddatafine) continue;
$cond_per_corr = " and iddatafine >= '$id_periodo_corrente'";
} # fine if ($priv_oscura_tab_mesi == "f")
} # fine if ($priv_vedi_tab_mesi == "p" and ($utente_inserimento != $id_utente or...

# depending on direction, if it is a new reservation we send also the id and surname
$iddatainizio = "";
$iddatafine = "";
if ($direz == "dx") $iddatainizio = risul_query($prenota_ini_fine,$num1,'iddatainizio');
else $iddatafine = risul_query($prenota_ini_fine,$num1,'iddatafine');
if ($iddatainizio == $idg_agg or $iddatafine == $idg_agg) {
$idprenota = risul_query($prenota_ini_fine,$num1,'idprenota');
if ($iddatainizio) $iddatafine = risul_query($prenota_ini_fine,$num1,'iddatafine');
else $iddatainizio = risul_query($prenota_ini_fine,$num1,'iddatainizio');
$ln1 = 0;
if (($iddatafine - $iddatainizio) == 0) {
$ln1 = 1;
if (!$prenota_consentita) {
if ($direz == "dx") $pren_succ = esegui_query("select utente_inserimento from $tableprenota where iddatainizio = '".($idg_agg + 1)."' and idappartamenti = '$id_appartamento' ");
else $pren_succ = esegui_query("select utente_inserimento from $tableprenota where iddatafine = '".($idg_agg - 1)."' and idappartamenti = '$id_appartamento'$cond_per_corr ");
if (numlin_query($pren_succ)) {
$ut_ins_succ = risul_query($pren_succ,0,'utente_inserimento');
if ($ut_ins_succ != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$ut_ins_succ])) $ln1 = 0;
} # fine if (numlin_query($pren_succ))
} # fine if (!$prenota_consentita)
if ($ln1) $dati_prenota_extra .= "<ln>1</ln>";
} # fine if (($iddatafine - $iddatainizio) == 0)

if ($prenota_consentita) {
$idclienti = risul_query($prenota_ini_fine,$num1,'idclienti');
$cognome = esegui_query("select cognome from $tableclienti where idclienti = '$idclienti' ");
$cognome = risul_query($cognome,0,'cognome');
$colore = colore_prenotazione($prenota_ini_fine,$num1);
$assegnazioneapp = substr(risul_query($prenota_ini_fine,$num1,'assegnazioneapp'),0,1);
if ($assegnazioneapp == "c") $app_ass_js = risul_query($prenota_ini_fine,$num1,'app_assegnabili');
else $app_ass_js = $assegnazioneapp;
$data_ins_js = risul_query($prenota_ini_fine,$num1,'datainserimento');
$link_modifica = 1;
if ($priv_mod_prenotazioni == "n") $link_modifica = 0;
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $link_modifica = 0;
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $link_modifica = 0;
if ($priv_mod_prenota_iniziate != "s" and $id_periodo_corrente >= $iddatainizio) $link_modifica = 0;
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$limite = date("YmdHis",mktime((substr($data_ins_js,11,2) + $priv_mod_prenota_ore),substr($data_ins_js,14,2),substr($data_ins_js,17,2),substr($data_ins_js,5,2),substr($data_ins_js,8,2),substr($data_ins_js,0,4)));
if ($adesso > $limite) $link_modifica = 0;
} # fine if ($priv_mod_prenota_ore != "000")
if (!$link_modifica) $dati_prenota_extra .= "<lm>1</lm>";
} # fine if ($prenota_consentita)

else {
$cognome = "&amp;nbsp;";
$colore = "#777777";
$app_ass_js = "k";
$data_ins_js = "1";
if ($direz == "dx") $pren_prec = esegui_query("select utente_inserimento from $tableprenota where iddatafine = '".($idg_agg - 1)."' and idappartamenti = '$id_appartamento'$cond_per_corr ");
else $pren_prec = esegui_query("select utente_inserimento from $tableprenota where iddatainizio = '".($idg_agg + 1)."' and idappartamenti = '$id_appartamento' ");
if (numlin_query($pren_prec)) {
$ut_ins_prec = risul_query($pren_prec,0,'utente_inserimento');
if ($ut_ins_prec != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$ut_ins_prec])) {
if ($ln1) $dati_prenota .= "<app id=\"$id_appartamento\"></app>";
continue;
} # fine if ($ut_ins_prec != $id_utente and ($priv_prenota_gruppi != "SI" or...
} # fine if (numlin_query($pren_prec))
} # fine else if ($prenota_consentita)

$dati_prenota_extra .= "<idpr>$idprenota</idpr>
<cogn>$cognome</cogn>
<clr>$colore</clr>
<apas>$app_ass_js</apas>
<dain>$data_ins_js</dain>";
if ($attiva_checkin == "SI") {
$checkin = risul_query($prenota_ini_fine,$num1,'checkin');
$checkout = risul_query($prenota_ini_fine,$num1,'checkout');
if ($checkin and !$checkout) $dati_prenota_extra .= "<cki>1</cki>";
} # fine if ($attiva_checkin == "SI")
} # fine if ($iddatainizio == $idg_agg or $iddatafine == $idg_agg)
elseif (!$prenota_consentita) {
if ($direz == "dx") $pren_succ = esegui_query("select utente_inserimento from $tableprenota where iddatainizio = '".($idg_agg + 1)."' and idappartamenti = '$id_appartamento' ");
else $pren_succ = esegui_query("select utente_inserimento from $tableprenota where iddatafine = '".($idg_agg - 1)."' and idappartamenti = '$id_appartamento'$cond_per_corr ");
if (numlin_query($pren_succ)) {
$ut_ins_succ = risul_query($pren_succ,0,'utente_inserimento');
if ($ut_ins_succ != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$ut_ins_succ])) continue;
} # fine if (numlin_query($pren_succ))
} # fine elseif (!$prenota_consentita)
$dati_prenota .= "<app id=\"$id_appartamento\">$dati_prenota_extra</app>";
} # fine if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO")
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO") {
if (!strstr($dati_prenota,"<app id=\"$id_appartamento\">")) {
if ($reg1_chiuso and $reg1_chiuso[$id_appartamento][$idg_agg]) {
$dati_prenota .= "<app id=\"$id_appartamento\">
<ln>1</ln><idpr>-1</idpr>
<cogn>&amp;nbsp;</cogn>
<clr>#777777</clr>
<apas>k</apas>
<dain>1</dain>
</app>";
} # fine if ($reg1_chiuso and $reg1_chiuso[$id_appartamento][$idg_agg])
} # fine if (!strstr($dati_prenota,"<app id=\"$id_appartamento\">"))
} # fine if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO")
} # fine for $num1

$dati_xml .= "<col num=\"$idg_agg\">
<dini>$d_inizio_settimana</dini>
<dfine>$d_fine_settimana</dfine>
<djs>$djs</djs>
$dati_prenota
</col>
";

} # fine if ($d_inizio_settimana)
} # fine for $ncol
unlock_tabelle($tabelle_lock);
} # fine if ($idg_agg and controlla_num_pos(substr($idg_agg,2)) == "SI")

if ($dati_prn and substr($dati_prn,0,3) == "prn" and controlla_num_pos(substr($dati_prn,3)) == "SI" and !$manda_xml) {
$manda_xml = 1;
$id_prn = substr($dati_prn,3);
$tabelle_lock = array();
$altre_tab_lock = array($tableprenota,$tablecostiprenota,$tableperiodi,$tableclienti,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$dati_prn = esegui_query("select * from $tableprenota where idprenota = '$id_prn' ");
if (numlin_query($dati_prn)) {
$utente_inserimento = risul_query($dati_prn,0,'utente_inserimento');
$iddatainizio = risul_query($dati_prn,0,'iddatainizio');
$link_modifica = 1;
if ($priv_mod_prenotazioni == "n") $link_modifica = 0;
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $link_modifica = 0;
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $link_modifica = 0;
if ($priv_mod_prenota_iniziate != "s") {
$id_periodo_corrente = calcola_id_periodo_corrente($anno);
if ($id_periodo_corrente >= $iddatainizio) $link_modifica = 0;
} # fine if ($priv_mod_prenota_iniziate != "s")
$data_ins = risul_query($dati_prn,0,'datainserimento');
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $link_modifica = 0;
} # fine if ($priv_mod_prenota_ore != "000")
if ($link_modifica) {
$iddatafine = risul_query($dati_prn,0,'iddatafine');
$dati_xml = "<txt>";
$id_cli = risul_query($dati_prn,0,'idclienti');
$dati_cliente = esegui_query("select * from $tableclienti where idclienti = '$id_cli' ");
$utente_inserimento = risul_query($dati_cliente,0,'utente_inserimento');
$pag_orig = $pag;
$pag = "modifica_prenota.php";
if (($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) or ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento])) $dati_xml .= mex("Cliente",$pag)." $id_cli ";
else {
include_once("./includes/funzioni_clienti.php");
$txt_cli = mostra_dati_cliente($dati_cliente,$dcognome,$dnome,$dsoprannome,$dtitolo_cli,$dsesso,$ddatanascita,$ddatanascita_f,$dnazionenascita,$dcittanascita,$dregionenascita,$ddocumento,$dscadenzadoc,$dscadenzadoc_f,$dtipodoc,$dnazionedoc,$dregionedoc,$dcittadoc,$dnazionalita,$dlingua_cli,$dnazione,$dregione,$dcitta,$dvia,$dnumcivico,$dtelefono,$dtelefono2,$dtelefono3,$dfax,$dcap,$demail,$dcod_fiscale,$dpartita_iva,"",$priv_ins_clienti,"1");
$txt_cli = str_replace("<br>"," \n",$txt_cli);
$txt_cli = preg_replace("/<[^<]*>/","",$txt_cli);
$dati_xml .= "$id_prn - ".$txt_cli." ";
} # fine else if (($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) or...
$num_persone = risul_query($dati_prn,0,'num_persone');
include_once("./includes/funzioni_costi_agg.php");
$dati_cap = dati_costi_agg_prenota($tablecostiprenota,$id_prn);
unset($num_letti_agg);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) aggiorna_letti_agg_in_periodi($dati_cap,$numca,$num_letti_agg,$iddatainizio,$iddatafine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],"","");
if ($num_persone or $num_letti_agg['max']) {
$dati_xml .= "\n-".mex("Nº di persone",$pag).": $num_persone ";
if ($num_letti_agg['max']) $dati_xml .= "+ ".$num_letti_agg['max']." ";
} # fine if ($num_persone or $num_letti_agg['max'])
$tariffa = risul_query($dati_prn,0,'tariffa');
if ($tariffa) {
$tariffa = explode("#@&",$tariffa);
$tariffa = $tariffa[0];
$dati_xml .= "\n-".mex("Tipo di tariffa",$pag).": ".str_replace("\"","&quot;",str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&","&amp;",$tariffa))))." ";
} # fine if ($tariffa)
$origine = risul_query($dati_prn,0,'origine');
if ($origine) $dati_xml .= "\n-".mex("Origine",$pag).": ".str_replace("\"","&quot;",str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&","&amp;",$origine))))." ";
$data_inserimento = formatta_data(substr($data_ins,0,-3),$stile_data);
$dati_xml .= "\n-".mex("Data inserimento",$pag).": ".str_replace("\"","&quot;",str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&","&amp;",$data_inserimento))))." ";
if ($priv_vedi_commento == "s") {
$commento = risul_query($dati_prn,0,'commento');
if (strstr($commento,">")) {
$commento = explode(">",$commento);
$commento = $commento[0];
} # fine if (strstr($commento,">"))
if ($commento) $dati_xml .= "\n-".mex("Commento",$pag).": ".str_replace("\"","&quot;",str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&","&amp;",$commento))))." ";
} # fine if ($priv_vedi_commento == "s")
$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$tariffa_tot = risul_query($dati_prn,0,'tariffa_tot');
$dati_xml .= "\n-".mex("Totale da pagare",$pag).": ".punti_in_num($tariffa_tot,$stile_soldi)." $Euro ";
$pagato = risul_query($dati_prn,0,'pagato');
$dati_xml .= "\n-".mex("Pagato",$pag).": ".punti_in_num($pagato,$stile_soldi)." $Euro ";
$da_pagare = $tariffa_tot - $pagato;
if ($da_pagare and $da_pagare != $tariffa_tot) $dati_xml .= "\n-".mex("Ancora da pagare",$pag).": ".punti_in_num($da_pagare,$stile_soldi)." $Euro ";
$dati_xml .= "</txt>";
$pag = $pag_orig;
} # fine if ($link_modifica)
} # fine if (numlin_query($dati_prn))
unlock_tabelle($tabelle_lock);
} # fine if ($dati_prn and substr($dati_prn,0,3) == "prn" and controlla_num_pos(substr($dati_prn,3)) == "SI" and...

if ($manda_xml) {
header('Content-type: text/xml');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<data>
$dati_xml
</data>
";
} # fine if ($manda_xml)


else {




if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");



if ($modificaprenotazione == "Continua") {
include_once("./includes/funzioni_mod_prenota.php");
controlla_id_prenota($id_prenota,$id_prenota_idpr,$num_id_prenota,$id_prenota_int,$priv_mod_prenotazioni,$anno,$PHPR_TAB_PRE);
if ($priv_mod_prenotazioni != "n") {
$inserire = "SI";
$cancellata = "NO";
prepara_modifiche_prenotazione($id_prenota_idpr,$num_id_prenota,$prenota_in_anno_succ,$dati_da_anno_prec,$tra_anni,$anno,$PHPR_TAB_PRE);
esegui_modifiche_prenotazione($inserire,$cancellata,$id_prenota_int,$id_prenota_idpr,$num_id_prenota,$id_transazione,$id_sessione,$anno,$id_nuovo_utente_inserimento,$n_stima_checkin,$n_met_paga_caparra,$n_origine_prenota,$n_pagato,$n_confermato,$tipo_commento,$n_commento,$n_cancella_commento,$tableprenota_da_aggiornare,$tipo_sposta,$dati_da_anno_prec,$prenota_in_anno_succ,$tra_anni,$PHPR_TAB_PRE);
} # fine if ($priv_mod_prenotazioni != "n")
} # fine if ($modificaprenotazione == "Continua")



$tabelle_lock = array($tableprenota);
$altre_tab_lock = array($tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$lim_prenota_temp = aggslashdb(date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60))));
esegui_query("delete from $tableprenota where idclienti = '0' and datainserimento < '$lim_prenota_temp'","",1);
unlock_tabelle($tabelle_lock);


unset($tabelle_lock);
#$tabelle_lock = array($tableprenota);
$altre_tab_lock = array($tableanni,$tableprenota,$tablenometariffe,$tableperiodi,$tableappartamenti,$tableclienti,$tableregole,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

if ($priv_mod_prenota_iniziate != "s" or $priv_oscura_tab_mesi == "f") $id_periodo_corrente = calcola_id_periodo_corrente($anno);

$oggi = date("j/n/Y",(time() + (C_DIFF_ORE * 3600)));
$ora = date("H:i",(time() + (C_DIFF_ORE * 3600)));
echo "<small><small>".mex("situazione alle",$pag)." $ora ".mex("del",$pag)." $oggi</small></small><br>";

unset($lista_prenota_contr);

if ($anno_succ == "SI") $mese = $mese + 12;

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

if ($mese > 48) {
$mese_mostra = $mese - 48;
$anno_mostra = $anno + 4;
} # fine if ($mese > 48)
else {
if ($mese > 36) {
$mese_mostra = $mese - 36;
$anno_mostra = $anno + 3;
} # fine if ($mese > 36)
else {
if ($mese > 24) {
$mese_mostra = $mese - 24;
$anno_mostra = $anno + 2;
} # fine if ($mese > 24)
else {
if ($mese > 12) {
$mese_mostra = $mese - 12;
$anno_mostra = $anno + 1;
} # fine if ($mese > 12)
else {
$mese_mostra = $mese;
$anno_mostra = $anno;
} # fine else if ($mese > 12)
} # fine else if ($mese > 24)
} # fine else if ($mese > 36)
} # fine else if ($mese > 48)

$cellpadding = 5;
$cellspacing = 1;
if ($tipo_periodi == "g") {
$cellpadding = 3;
$cellspacing = 0;
} # fine if ($tipo_periodi == "g")
$aggiunta_tronca = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'aggiunta_tronca_nomi_tab1' and idutente = '$id_utente'");
$aggiunta_tronca = risul_query($aggiunta_tronca,0,'valpersonalizza_num');
if ($aggiunta_tronca < -4) $aggiunta_tronca = -4;
if ($tipo_periodi == "g" and $aggiunta_tronca < -3) $aggiunta_tronca = -3;

if ($mese != 1 and $tipo_periodi != "g") $mese_inizio_tab = $mese - 1;
else $mese_inizio_tab = $mese;
if ($mese != 48 and $tipo_periodi != "g") $mese_fine_tab = $mese + 1;
else $mese_fine_tab = $mese;

echo "<table style=\"margin-left: auto; margin-right: auto;\"><tr><td style=\"width: 100px;\" align=\"right\">";
if ($mese != 1) {
if ($tipo_periodi == "g") $mese_freccia = ($mese_inizio_tab - 1);
else  $mese_freccia = $mese_inizio_tab;
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"m_prec_su\" name=\"mese\" value=\"$mese_freccia\">
<button type=\"submit\" class=\"wbutton\"><img src=\"./img/dir3_sin.png\" alt=\"&lt;--\"></button>
&nbsp;&nbsp;&nbsp;&nbsp;</div></form>";
} # fine if ($mese != 1)
echo "</td><td align=\"center\">
<h3 id=\"h_mon\">".mex("Tabella prenotazioni del",$pag)." <span id=\"m_corr_su\">$mese_mostra-$anno_mostra</span>.</h3>
</td><td style=\"width: 100px;\" align=\"left\">";
if ($mese != $mese_fine_periodi) {
if ($tipo_periodi == "g") $mese_freccia = ($mese_fine_tab + 1);
else  $mese_freccia = $mese_fine_tab;
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"m_succ_su\" name=\"mese\" value=\"$mese_freccia\">
&nbsp;&nbsp;&nbsp;&nbsp;
<button type=\"submit\" class=\"wbutton\"><img src=\"./img/dir3_des.png\" alt=\"--&gt;\"></button>
</div></form>";
} # fine if ($mese != $mese_fine_periodi)
else {
if ($id_utente == 1) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php#agg_per\"><div style=\"white-space: nowrap;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<input type=\"hidden\" id=\"m_succ_su\" name=\"mese\" value=\"$mese_fine_periodi\">
&nbsp;&nbsp;&nbsp;&nbsp;
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Aggiungi periodi",'visualizza_tabelle.php')."\">
</div></form>";
} # fine if ($id_utente == 1)
else echo "&nbsp;";
} # fine else if ($mese != $mese_fine_periodi)
echo "</td></tr></table>";

$data_inizio_tab = date("Y-m-d" , mktime(0,0,0,$mese_inizio_tab,1,$anno));
$id_data_inizio_tab = esegui_query("select * from $tableperiodi where datainizio >= '$data_inizio_tab' order by idperiodi");
$id_data_inizio_tab = risul_query($id_data_inizio_tab,0,'idperiodi');
if ($mese != $mese_inizio_periodi) {
$id_data_inizio_tab = $id_data_inizio_tab - 1;
$data_inizio_tab = date("Y-m-d" , mktime(0,0,0,$mese_inizio_tab,0,$anno));
$data_inizio_selezione = $data_inizio_tab;
} # fine if ($mese != $mese_inizio_periodi)
else $data_inizio_selezione = date("Y-m-d" , mktime(0,0,0,$mese_inizio_tab,0,$anno));
$data_fine_tab = date("Y-m-d" , mktime(0,0,0,$mese_fine_tab,31,$anno));
$data_fine_tab = esegui_query("select * from $tableperiodi where datainizio <= '$data_fine_tab' order by idperiodi");
$num_date = numlin_query($data_fine_tab);
$num_date = $num_date - 1;
$id_data_fine_tab = risul_query($data_fine_tab,$num_date,'idperiodi');
$data_fine_tab = risul_query($data_fine_tab,$num_date,'datafine');
$g_fine_tab = explode("-",$data_fine_tab);
$g_fine_tab = $g_fine_tab[2];
if ($g_fine_tab == 8) $id_data_fine_tab = $id_data_fine_tab - 1;
if ($tipo_periodi == "g" and $g_fine_tab > 1) $id_data_fine_tab = $id_data_fine_tab - $g_fine_tab + 1;
$num_colonne = $id_data_fine_tab - $id_data_inizio_tab + 1;
if ($mese != $mese_inizio_periodi) $data_fine_tab = date("Y-m-d",mktime(0,0,0,$mese_inizio_tab,$num_colonne,$anno));
else $data_fine_tab = date("Y-m-d",mktime(0,0,0,$mese_inizio_tab,($num_colonne + 1),$anno));
$appartamenti = esegui_query("select * from $tableappartamenti order by idappartamenti");
$num_appartamenti = numlin_query($appartamenti);

if ($priv_vedi_tab_mesi == "p") {
include("./includes/funzioni_appartamenti.php");
if ($priv_app_gruppi != "SI") $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,$id_utente,$tableregole,$tablenometariffe);
else $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr,$id_utente,$tableregole,$tablenometariffe);
} # fine if ($priv_vedi_tab_mesi == "p")

# regole 1 di chiusura
unset($reg1_chiuso);
$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != '' and motivazione2 = 'x' ");
$num_app_agenzia = numlin_query($app_agenzia);
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$ini_chiuso = risul_query($app_agenzia,$num1,'iddatainizio');
$fine_chiuso = risul_query($app_agenzia,$num1,'iddatafine');
$app_chiuso = risul_query($app_agenzia,$num1,'app_agenzia');
for ($num2 = $ini_chiuso ; $num2 <= $fine_chiuso ; $num2++) $reg1_chiuso[$app_chiuso][$num2] = 1;
} # fine for $num1

# controllo se vi sono prenotazioni in appartamenti cancellati o non mostrati
$num_appartamenti_cancellati = 0;
$cond_app_canc = "";
$cond_app_perm = "";
$query_prenota_app_canc = "select * from $tableprenota left outer join $tableclienti on $tableprenota.idclienti = $tableclienti.idclienti where $tableprenota.iddatainizio <= '$id_data_fine_tab' and $tableprenota.iddatafine >= '$id_data_inizio_tab'";
$app_query = " and ($tableprenota.idappartamenti is NULL or (";
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1 = $num1 + 1) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
$app_query .= "$tableprenota.idappartamenti != '$id_appartamento' and ";
if ($priv_vedi_tab_mesi == "p" and $appartamenti_consentiti[$id_appartamento] == "NO") $cond_app_perm .= " and $tableprenota.idappartamenti NOT $LIKE '$id_appartamento'";
} # fine for $num1
if (substr($app_query,-5) == " and ") $app_query = substr($app_query,0,-5);
else $app_query = " and (($tableprenota.idappartamenti is NULL ";
$query_prenota_app_canc .= $app_query."))";
if ($priv_vedi_tab_mesi == "p") {
$query_prenota_app_canc .= " and ($tableprenota.utente_inserimento = '$id_utente'";
if ($priv_prenota_gruppi == "SI") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $query_prenota_app_canc .= " or $tableprenota.utente_inserimento = '$idut_gr'";
} # fine if ($priv_prenota_gruppi == "SI")
$query_prenota_app_canc .= ")";
} # fine if ($priv_vedi_tab_mesi == "p")
$query_prenota_app_canc .= " order by $tableprenota.idappartamenti,$tableprenota.iddatainizio";
$prenota_app_canc = esegui_query($query_prenota_app_canc);
$num_prenota_app_canc = numlin_query($prenota_app_canc);
$num_app_canc = 0;
unset($app_canc_trovato);
for ($num1 = 0 ; $num1 < $num_prenota_app_canc; $num1 = $num1 + 1) {
$cond_app_canc .= " and $tableprenota.idprenota != '".risul_query($prenota_app_canc,$num1,'idprenota')."' ";
$idapp_prenota_app_canc = risul_query($prenota_app_canc,$num1,'idappartamenti');
if ($app_canc_trovato[$idapp_prenota_app_canc] != "SI") {
$app_canc_trovato[$idapp_prenota_app_canc] = "SI";
$app_canc[$num_app_canc] = $idapp_prenota_app_canc;
$num_app_canc++;
} # fine if ($app_canc_trovato[$idapp_prenota_app_canc] != "SI")
} # fine for $num1

$allinea_tab_mesi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'allinea_tab_mesi' and idutente = '$id_utente'");
if (numlin_query($allinea_tab_mesi) == 1) $allinea_tab_mesi = risul_query($allinea_tab_mesi,0,'valpersonalizza');
else $allinea_tab_mesi = "NO";

$linea_date = "<tr class=\"rd_r\" style=\"background-color: $t1dates;\"><td style=\"background-color: $t1color; padding: 0 2px 0 2px\">";
if ($inserire != "NO") $linea_date .= "<a name=\"\"></a>";
$linea_date .= "<button type=\"button\" onclick=\"scorri_date('sx')\">
<img src=\"./img/dir1_sin.png\" alt=\"&lt;\"></button><br>
<button type=\"button\" onclick=\"scorri_date('7sx')\">
<img src=\"./img/dir2_sin.png\" alt=\"&lt;\"></button></td>";
for ($num1 = 0 ; $num1 < $num_colonne ; $num1 = $num1 + 1) {
$id_settimana = $id_data_inizio_tab + $num1;
crea_data_per_linea($num1,$num_colonne,$id_settimana,$d_inizio_settimana,$d_fine_settimana,$m_inizio_settimana,$m_fine_settimana,$array_date_col_js,$tipo_periodi,$mostra_giorni_tab_mesi,$giorno_vedi_ini_sett);
$linea_date .= "<td class=\"rd_$num1\"";
if ($num1 != 0 or $allinea_tab_mesi == "SI") $linea_date .= " colspan=\"2\"";
if ($m_inizio_settimana != $mese_mostra) $linea_date .= " style=\"background-color: $t1datesout;\"";
$linea_date .= ">$d_inizio_settimana</td>";
} # fine for $num1
if ($allinea_tab_mesi != "SI") {
$linea_date .= "<td class=\"rd_$num1\"";
if ($m_fine_settimana != $mese_mostra) $linea_date .= " style=\"background-color: $t1datesout;\"";
$linea_date .= ">$d_fine_settimana</td>";
} # fine if ($allinea_tab_mesi != "SI")
$linea_date .= "<td style=\"background-color: $t1color; padding: 0 2px 0 2px\">
<button type=\"button\" onclick=\"scorri_date('dx')\">
<img src=\"./img/dir1_des.png\" alt=\"&gt;\"></button><br>
<button type=\"button\" onclick=\"scorri_date('7dx')\">
<img src=\"./img/dir2_des.png\" alt=\"&gt;\"></button></td></tr>";
$num_linea_date = 1;
echo "<script type=\"text/javascript\">
<!--
var colore_date_norm = '$t1dates';
var colore_date_altre = '$t1datesout';
var colore_date_sel = '$t1seldate';
var colore_drp1 = '$t1dropin';
var colore_drp2 = '$t1dropout';

var id_ini_tab = $id_data_inizio_tab;
var id_fine_tab = $id_data_fine_tab;
var agg_tronca = $aggiunta_tronca;
var riduci_font = 0;
var tipo_periodi = '$tipo_periodi';
var id_sessione = '$id_sessione';
var anno = $anno;
var allinea_tab_mesi = '$allinea_tab_mesi';
var tab_spostata = 0;
var sel_start_date = 0;
var sel_start_col = 0;
var sel_stop_date = 0;
var curr_sel_row = 0;
-->
</script>

<div class=\"tab_cont\">
<div style=\"position: relative;\" ondragstart=\"drg(event)\">
<table class=\"m1ext\" cellspacing=0 cellpadding=0><tr><td>
<table class=\"m1\" style=\"background-color: $t1color;\" cellpadding=\"$cellpadding\" ><tbody>";

echo str_replace("<a name=\"\"></a>","<a name=\"rd_n$num_linea_date\"></a>",$linea_date);


$linee_ripeti_date_tab_mesi = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'linee_ripeti_date_tab_mesi' and idutente = '$id_utente'");
$linee_ripeti_date_tab_mesi = risul_query($linee_ripeti_date_tab_mesi,0,'valpersonalizza_num');
$num_ripeti = 1;
$num_app_js = 0;
$array_app_js = "";
$array_app_ass_js = "";
$array_data_ins_js = "";

$prenotazione_presente = esegui_query("select
 $tableprenota.idprenota,
 $tableprenota.idclienti,
 $tableprenota.idappartamenti,
 $tableprenota.iddatainizio,
 $tableprenota.iddatafine,
 $tableprenota.assegnazioneapp,
 $tableprenota.app_assegnabili,
 $tableprenota.tariffa_tot,
 $tableprenota.caparra,
 $tableprenota.pagato,
 $tableprenota.conferma,
 $tableprenota.checkin,
 $tableprenota.checkout,
 $tableprenota.datainserimento,
 $tableprenota.utente_inserimento,
 $tableclienti.cognome,
 $tableclienti.utente_inserimento as utente_inserimento_cli
 from $tableprenota left outer join $tableclienti on $tableprenota.idclienti = $tableclienti.idclienti where $tableprenota.iddatainizio <= '".($id_data_inizio_tab + $num_colonne - 1)."' and $tableprenota.iddatafine >= '$id_data_inizio_tab'$cond_app_canc$cond_app_perm order by $tableprenota.idappartamenti,$tableprenota.iddatainizio");
$num_prenotazione_presente = numlin_query($prenotazione_presente);
$prenota_succ = 0;

for ($num1 = 0 ; $num1 < ($num_appartamenti + $num_app_canc) ; $num1 = $num1 + 1) {
if ($num1 < $num_appartamenti) $id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
else {
$id_appartamento = $app_canc[($num1 - $num_appartamenti)];
if ($num1 == $num_appartamenti) {
$prenotazione_presente = $prenota_app_canc;
$num_prenotazione_presente = $num_prenota_app_canc;
$prenota_succ = 0;
} # fine if ($num1 == $num_appartamenti)
} # fine else if ($num1 < $num_appartamenti)
if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO" or $num1 >= $num_appartamenti) {

$casella_app = "<td>";
if (num_caratteri_testo($id_appartamento) > 2) $casella_app .= "<small><small>";
if ($num1 >= $num_appartamenti) $casella_app .= "<div style=\"display: inline; color: red;\">";
if (strcmp($id_appartamento,"")) $casella_app .= "$id_appartamento";
else $casella_app .= mex("ERRORE",$pag);
if ($num1 >= $num_appartamenti) $casella_app .= "</div>";
if (num_caratteri_testo($id_appartamento) > 2) $casella_app .= "</small></small>";
$casella_app .= "</td>";
echo "<tr id=\"app$num_app_js\">$casella_app";
$array_app_js .= "\"$id_appartamento\",";
$num_app_js++;

$ini_prenota_succ = $id_data_inizio_tab + $num_colonne + 1;
if ($num_prenotazione_presente > $prenota_succ) {
$app_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'idappartamenti',$tableprenota);
if ($app_prenota_succ == $id_appartamento) $ini_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'iddatainizio',$tableprenota);
} # fine if ($num_prenotazione_presente > $prenota_succ)

for ($num2 = 0 ; $num2 < $num_colonne ; $num2 = $num2 + 1) {
$id_settimana = $id_data_inizio_tab + $num2;
if ($id_settimana >= $ini_prenota_succ) {
$esiste = 1;
$prenota_corr = $prenota_succ;
$prenota_succ++;
$ini_prenota_succ = "";
if ($num_prenotazione_presente > $prenota_succ) {
$app_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'idappartamenti',$tableprenota);
if ($app_prenota_succ == $id_appartamento) $ini_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'iddatainizio',$tableprenota);
} # fine if ($num_prenotazione_presente > $prenota_succ)
if (!$ini_prenota_succ) {
$ini_prenota_succ = (risul_query($prenotazione_presente,$prenota_corr,'iddatafine',$tableprenota) + 1);
if (($id_data_inizio_tab + $num_colonne + 1) > $ini_prenota_succ) $ini_prenota_succ = $id_data_inizio_tab + $num_colonne + 1;
} # fine if (!$ini_prenota_succ)
} # fine if ($id_settimana >= $ini_prenota_succ)
else $esiste = 0;

if ($esiste == 1) $utente_inserimento = risul_query($prenotazione_presente,$prenota_corr,'utente_inserimento',$tableprenota);
else $utente_inserimento = $id_utente;

if ($priv_vedi_tab_mesi == "p" and (($utente_inserimento != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento])) or ($periodo_consentito_app[$id_appartamento][$id_settimana] == "NO" and $esiste != 1))) {
$freccia_sx = "";
$freccia_dx = "";
if ($utente_inserimento != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento])) {
$id_inizio_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatainizio',$tableprenota);
$id_fine_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatafine',$tableprenota);
if ($id_inizio_prenota > $id_data_inizio_tab) $id_inizio = $id_inizio_prenota;
else $id_inizio = $id_data_inizio_tab;
if ($id_inizio_prenota < $id_data_inizio_tab) $freccia_sx = "&lt;- ";
if ($id_inizio_prenota == $id_data_inizio_tab) {
$pren_prec = esegui_query("select utente_inserimento from $tableprenota where iddatafine = '".($id_data_inizio_tab - 1)."' and idappartamenti = '$id_appartamento' ");
if (numlin_query($pren_prec)) {
$ut_ins_prec = risul_query($pren_prec,0,'utente_inserimento');
if ($ut_ins_prec != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$ut_ins_prec])) $freccia_sx = "&lt;- ";
} # fine if (numlin_query($pren_prec))
} # fine if ($id_inizio_prenota == $id_data_inizio_tab)
if ($id_fine_prenota < $id_data_fine_tab) $id_fine = $id_fine_prenota;
else $id_fine = $id_data_fine_tab;
if ($id_fine_prenota > $id_data_fine_tab) $freccia_dx = " -&gt;";
if ($id_fine_prenota == $id_data_fine_tab) $controlla_pren_succ = 1;
else $controlla_pren_succ = 0;
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
$utente_inserimento2 = risul_query($prenotazione_presente,$prenota_succ,'utente_inserimento',$tableprenota);
if ($utente_inserimento2 != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$utente_inserimento2])) {
$id_fine_prenota = risul_query($prenotazione_presente,$prenota_succ,'iddatafine',$tableprenota);
if ($id_fine_prenota > $id_data_fine_tab) $freccia_dx = " -&gt;";
if ($id_fine_prenota == $id_data_fine_tab) $controlla_pren_succ = 1;
if ($id_fine_prenota < $id_data_fine_tab) $id_fine = $id_fine_prenota;
else {
$id_fine = $id_data_fine_tab;
$id_fine_prenota = $id_fine;
$fatto = "SI";
} # fine else if ($id_fine_prenota < $id_data_fine_tab)
$colonne = $id_fine - $id_inizio + 1;
$prenota_corr = $prenota_succ;
$prenota_succ++;
$ini_prenota_succ = "";
if ($num_prenotazione_presente > $prenota_succ) {
$app_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'idappartamenti',$tableprenota);
if ($app_prenota_succ == $id_appartamento) $ini_prenota_succ = risul_query($prenotazione_presente,$prenota_succ,'iddatainizio',$tableprenota);
}  # fine if ($num_prenotazione_presente > $prenota_succ)
if (!$ini_prenota_succ) {
$ini_prenota_succ = (risul_query($prenotazione_presente,$prenota_corr,'iddatafine',$tableprenota) + 1);
if (($id_data_inizio_tab + $num_colonne + 1) > $ini_prenota_succ) $ini_prenota_succ = $id_data_inizio_tab + $num_colonne + 1;
} # fine if (!$ini_prenota_succ)
} # fine if ($utente_inserimento2 != $id_utente and...
else $fatto = "SI";
} # fine if (numlin_query($prenotazione_successiva) == 1)
else {
if ($periodo_consentito_app[$id_appartamento][$id_inizio2] == "NO") {
$id_fine++;
if ($id_fine == $id_data_fine_tab) $fatto = "SI";
$colonne++;
} # fine if ($periodo_consentito_app[$id_appartamento][$$id_inizio2] == "NO")
else $fatto = "SI";
} # fine else if (numlin_query($prenotazione_successiva) == 1)
} # fine while ($fatto == "NO")
if ($controlla_pren_succ) {
$pren_succ = esegui_query("select utente_inserimento from $tableprenota where iddatainizio = '".($id_data_fine_tab + 1)."' and idappartamenti = '$id_appartamento' ");
if (numlin_query($pren_succ)) {
$ut_ins_succ = risul_query($pren_succ,0,'utente_inserimento');
if ($ut_ins_succ != $id_utente and ($priv_prenota_gruppi != "SI" or !$utenti_gruppi[$ut_ins_succ])) $freccia_dx = " -&gt;";
} # fine if (numlin_query($pren_succ))
} # fine if ($controlla_pren_succ)
if ($priv_oscura_tab_mesi != "v" and $priv_oscura_tab_mesi != "f") {
$colonne_s = $colonne * 2;
echo "<td style=\"background-color: #777777;\" colspan=\"$colonne_s\"><div style=\"visibility: hidden;\">$freccia_sx<a>&nbsp;</a>$freccia_dx</div></td>";
} # fine if ($priv_oscura_tab_mesi != "v" and $priv_oscura_tab_mesi != "f")
if ($priv_oscura_tab_mesi == "v") {
for ($num3 = 0 ; $num3 < $colonne ; $num3++) echo "<td colspan=\"2\">&nbsp;</td>";
} # fine if ($priv_oscura_tab_mesi == "v")
if ($priv_oscura_tab_mesi == "f") {
for ($num3 = 0 ; $num3 < $colonne ; $num3++) {
if ($id_periodo_corrente > ($id_settimana + $num3)) echo "<td colspan=\"2\">&nbsp;</td>";
else {
if ($num3 > 0) $freccia_sx = "";
$colonne_s = ($colonne - $num3) * 2;
echo "<td style=\"background-color: #777777;\" colspan=\"$colonne_s\"><div style=\"visibility: hidden;\">$freccia_sx<a>&nbsp;</a>$freccia_dx</div></td>";
break;
} # fine else if ($id_periodo_corrente > ($id_settimana + $num3))
} # fine for $num3
} # fine if ($priv_oscura_tab_mesi == "f")
$num2 = $num2 + $colonne - 1;
} # fine if ($priv_vedi_tab_mesi == "p" and ($utente_inserimento != $id_utente or...
else {

$link_modifica = "SI";
$tit_prx = "";
if ($esiste == 1) {
$id_prenota = risul_query($prenotazione_presente,$prenota_corr,'idprenota',$tableprenota);
$lista_prenota_contr .= ",".$id_prenota;
$id_clienti = risul_query($prenotazione_presente,$prenota_corr,'idclienti',$tableprenota);
if ($id_clienti) {
$mostra_cliente = "SI";
if ($vedi_clienti == "NO") $mostra_cliente = "NO";
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$utente_inserimento_cli = risul_query($prenotazione_presente,$prenota_corr,'utente_inserimento_cli',$tableclienti);
if ($vedi_clienti == "PROPRI" and $utente_inserimento_cli != $id_utente) $mostra_cliente = "NO";
if ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento_cli]) $mostra_cliente = "NO";
} # fine if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI")
if ($mostra_cliente == "NO") $cognome = $id_prenota;
else $cognome = risul_query($prenotazione_presente,$prenota_corr,'cognome',$tableclienti);
} # fine if ($id_clienti)
else {
$cognome = "?";
$link_modifica = "NO";
$tit_prx = mex("Prenotazione temporanea per bloccare l'appartamento",'unit.php')." ".mex("durante l'inserimento dei dati del cliente quando si inserisce una nuova prenotazione",$pag).".\n
".mex("Utilizzando il tasto 'cancella' dalla pagina di inserimento dei dati del cliente anche questa prenotazione verrà cancellata",$pag).".\n
".mex("L'utente amministratore può disabilitare o cambiare la durata di queste prenotazioni da 'configura e personalizza'",$pag).".";
} # fine if ($id_clienti)

$colore = colore_prenotazione($prenotazione_presente,$prenota_corr);

$stile_checkin = "";
if ($attiva_checkin == "SI") {
$checkin = risul_query($prenotazione_presente,$prenota_corr,'checkin',$tableprenota);
$checkout = risul_query($prenotazione_presente,$prenota_corr,'checkout',$tableprenota);
if ($checkin and !$checkout) $stile_checkin = "background-image:url(img/fr_sx_checkin.gif); background-repeat:no-repeat; background-position: right center;";
} # fine if ($attiva_checkin == "SI")

$id_inizio_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatainizio',$tableprenota);
$id_fine_prenota = risul_query($prenotazione_presente,$prenota_corr,'iddatafine',$tableprenota);
if ($id_inizio_prenota > $id_data_inizio_tab) { $id_inizio = $id_inizio_prenota; }
else { $id_inizio = $id_data_inizio_tab; }
if ($id_fine_prenota < $id_data_fine_tab) { $id_fine = $id_fine_prenota; }
else { $id_fine = $id_data_fine_tab; }
$colonne = $id_fine - $id_inizio + 1;
if ($id_utente == 1 and $ini_prenota_succ <= $id_fine_prenota) $cognome = mex("ERRORE",$pag);

if ($priv_mod_prenotazioni == "n") $link_modifica = "NO";
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $link_modifica = "NO";
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $link_modifica = "NO";
if ($priv_mod_prenota_iniziate != "s" and $id_periodo_corrente >= $id_inizio_prenota) $link_modifica = "NO";
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = risul_query($prenotazione_presente,$prenota_corr,'datainserimento',$tableprenota);
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $link_modifica = "NO";
} # fine if ($priv_mod_prenota_ore != "000")
if ($link_modifica == "SI" and $id_clienti) {
$link_modifica_inizio = "<a href=\"modifica_prenota.php?id_prenota=$id_prenota&amp;anno=$anno&amp;id_sessione=$id_sessione&amp;mese=$mese\">";
$link_modifica_fine = "</a>";
} # fine if ($link_modifica == "SI" and $id_clienti)
else {
$link_modifica_inizio = "<a class=\"noho\">";
$link_modifica_fine = "</a>";
} # fine else if ($link_modifica == "SI" and $id_clienti)
if ($link_modifica == "SI") {
$id_prn = " id=\"prn$id_prenota\"";
$assegnazioneapp = substr(risul_query($prenotazione_presente,$prenota_corr,'assegnazioneapp',$tableprenota),0,1);
if ($assegnazioneapp == "c") $array_app_ass_js .= "ApAs[$id_prenota] = ',".risul_query($prenotazione_presente,$prenota_corr,'app_assegnabili',$tableprenota).",';
";
else $array_app_ass_js .= "ApAs[$id_prenota] = '$assegnazioneapp';
";
$array_data_ins_js .= "DaIn[$id_prenota] = '".risul_query($prenotazione_presente,$prenota_corr,'datainserimento',$tableprenota)."';
";
} # fine if ($link_modifica == "SI")
else {
$id_prn = " id=\"prx$id_prenota\"";
if ($tit_prx) $id_prn .= " title=\"$tit_prx\"";
} # fine else if ($link_modifica == "SI")

$riduci_font = "";
$lung_cognome = num_caratteri_testo($cognome);
$lung_freccia = 0;
if ($id_fine_prenota > $id_data_fine_tab or $id_inizio_prenota < $id_data_inizio_tab) $lung_freccia = 3;
$lung_non_ridotta = (7+$aggiunta_tronca)*$colonne - $lung_freccia;
if ($tipo_periodi == "g") $lung_non_ridotta = (3+$aggiunta_tronca)*$colonne - $lung_freccia;
if ($lung_cognome > $lung_non_ridotta) $riduci_font = "SI";
$lung_non_tronca = (9+$aggiunta_tronca)*$colonne;
if ($tipo_periodi == "g") $lung_non_tronca = (5+$aggiunta_tronca)*$colonne;
if ($lung_freccia == 3) $lung_non_tronca = $lung_non_tronca - 1;
if ($lung_cognome > ($lung_non_tronca+1) and $cognome != "&nbsp;") {
if ($link_modifica_inizio == "<a class=\"noho\">") $link_modifica_inizio = "<a class=\"noho\" title=\"".htmlspecialchars($cognome)."\">";
else $link_modifica_inizio = str_replace("<a href","<a title=\"".htmlspecialchars($cognome)."\" href",$link_modifica_inizio);
$cognome = tronca_testo($cognome,0,$lung_non_tronca).".";
} # fine if ($lung_cognome > ($lung_non_tronca+1) and...
$colonne_s = $colonne * 2;
echo "<td class=\"pren\" colspan=\"$colonne_s\">
<table$id_prn style=\"background-color: $colore;";
if ($id_inizio_prenota < $id_data_inizio_tab) echo " border-top-left-radius: 0; border-bottom-left-radius: 0;";
if ($id_fine_prenota > $id_data_fine_tab) echo " border-top-right-radius: 0; border-bottom-right-radius: 0;";
echo "\">
<tr><td";
if ($stile_checkin) echo " style=\"$stile_checkin\"";
echo "></td><td>";
if ($riduci_font) echo "<small><small>";
if ($id_inizio_prenota < $id_data_inizio_tab) echo "&lt;- ";
echo "$link_modifica_inizio$cognome$link_modifica_fine";
if ($id_fine_prenota > $id_data_fine_tab) echo " -&gt;";
if ($riduci_font) echo "</small></small>";
echo "</td><td></td></tr></table></td>";
$num2 = $num2 + $colonne - 1;
} # fine if ($esiste == 1)
else {
if ($reg1_chiuso and $reg1_chiuso[$id_appartamento][$id_settimana]) echo "<td style=\"background-color: #777777;\" colspan=\"2\">&nbsp;</td>";
else echo "<td colspan=\"2\">&nbsp;</td>";
} # fine else if ($esiste == 1)

} # fine else if ($priv_vedi_tab_mesi == "p" and ($utente_inserimento != $id_utente or...
} # fine for $num2
echo "$casella_app</tr>";

if ($num_ripeti == $linee_ripeti_date_tab_mesi) {
$num_linea_date++;
echo str_replace("<a name=\"\"></a>","<a name=\"rd_n$num_linea_date\"></a>",$linea_date);
$num_ripeti = 1;
} # fine if ($num_ripeti == $linee_ripeti_date_tab_mesi)
else $num_ripeti++;
} # fine if ($priv_vedi_tab_mesi != "p" or $appartamenti_consentiti[$id_appartamento] != "NO")
} # fine for $num1

unlock_tabelle($tabelle_lock);

echo "</tbody></table></td></tr></table></div></div>
<form id=\"mod_pren\" accept-charset=\"utf-8\" method=\"post\" action=\"modifica_prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"mese_orig\" name=\"mese\" value=\"$mese\">
<input type=\"hidden\" name=\"modificaprenotazione\" value=\"1\">
<input type=\"hidden\" id=\"orig\" name=\"origine\" value=\"tab_mese_drop##\">
<input type=\"hidden\" id=\"id_pren\" name=\"id_prenota\" value=\"\">
<input type=\"hidden\" id=\"n_appart\" name=\"n_appartamento\" value=\"\">
<input type=\"hidden\" id=\"s_appart\" name=\"sposta_appartamento\" value=\"\">
<input type=\"hidden\" id=\"d_data_ins\" name=\"d_data_inserimento\" value=\"\">
<input type=\"hidden\" id=\"n_ini_per\" name=\"n_inizioperiodo\" value=\"\">
<input type=\"hidden\" id=\"n_fin_per\" name=\"n_fineperiodo\" value=\"\">
</div></form>
<script type=\"text/javascript\">
<!--
var priv_mod_aa = '$priv_mod_assegnazione_app';
var priv_mod_da = '$priv_mod_date';
var arr_app = new Array(".substr($array_app_js,0,-1).");
var ApAs = new Array();
$array_app_ass_js
var DaIn = new Array();
$array_data_ins_js
var ArDaCo = new Array();
$array_date_col_js
var ArTiOr = new Array();
var ArTiPr = new Array();

attiva_drag_drop();
attiva_colora_date(allinea_tab_mesi);
-->
</script>
<form id=\"ins_pren\" accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_tipologie\" value=\"1\">
<input type=\"hidden\" name=\"mos_tut_dat\" value=\"SI\">
<input type=\"hidden\" id=\"ins_ini_per\" name=\"inizioperiodo1\" value=\"\">
<input type=\"hidden\" id=\"ins_fin_per\" name=\"fineperiodo1\" value=\"\">
<input type=\"hidden\" id=\"ins_app\" name=\"appartamento1\" value=\"\">
</div></form>
<table><tr><td style=\"height: 2px;\"></td></tr></table>
<table style=\"margin-left: auto; margin-right: auto;\"><tr><td style=\"width: 100px;\" align=\"right\">";
if ($mese != 1) {
if ($tipo_periodi == "g") $mese_inizio_tab--; 
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"m_prec_giu\" name=\"mese\" value=\"$mese_inizio_tab\">
<button type=\"submit\" class=\"wbutton\"><img src=\"./img/dir3_sin.png\" alt=\"&lt;--\"></button>
&nbsp;&nbsp;&nbsp;&nbsp;</div></form>";
} # fine if ($mese != 1)
echo "</td><td align=\"center\">";
if ($tipo_periodi == "g") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"tabella3.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"m_corr_giu\" name=\"mese\" value=\"$mese\">
<button class=\"prnt\" type=\"submit\"><div>".mex("Visualizza la tabella per la stampa",$pag)."</div></button>
</div></form>";
} # fine if ($tipo_periodi == "g")
else {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"tabella2.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"m_corr_giu\" name=\"mese\" value=\"$mese\">
<button class=\"prnt\" type=\"submit\"><div>".mex("Visualizza la tabella con i giorni",$pag)."</div></button>
</div></form>";
} # fine else if ($tipo_periodi == "g")
echo "</td><td style=\"width: 100px;\" align=\"left\">";
if ($mese != $mese_fine_periodi) {
if ($tipo_periodi == "g") $mese_fine_tab++; 
echo "<form id=\"mes_succ\" accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"m_succ_giu\" name=\"mese\" value=\"$mese_fine_tab\">
&nbsp;&nbsp;&nbsp;&nbsp;
<button type=\"submit\" class=\"wbutton\"><img src=\"./img/dir3_des.png\" alt=\"--&gt;\"></button>
</div></form>";
} # fine if ($mese != $mese_fine_periodi)
else {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php#agg_per\"><div style=\"white-space: nowrap;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<input type=\"hidden\" id=\"m_succ_su\" name=\"mese\" value=\"$mese_fine_periodi\">
&nbsp;&nbsp;&nbsp;&nbsp;
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Aggiungi periodi",'visualizza_tabelle.php')."\">
</div></form>";
} # fine else if ($mese != $mese_fine_periodi)

if ($lista_prenota_contr) $lista_prenota_contr .= ",";
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
$option_num_contr = "";
for ($num_contratto = 1 ; $num_contratto <= $max_contr ; $num_contratto++) {
if ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contratto] == "SI") {
if ($nome_contratto[$num_contratto]) $num_contratto_vedi = $nome_contratto[$num_contratto];
else $num_contratto_vedi = $num_contratto;
$option_num_contr .= "<option value=\"$num_contratto\">$num_contratto_vedi</option>";
} # fine if ($attiva_contratti_consentiti == "n" or...
} # fine for $num_contratto
if ($option_num_contr and $show_bar != "NO") {
echo "</td></tr><tr><td style=\"height: 2px;\"></td></tr><tr><td></td><td align=\"center\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_contratto.php\" onsubmit=\"contr_da_tab_mese()\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"$pag?id_sessione=$id_sessione&amp;anno=$anno&amp;mese=$mese\">
<input type=\"hidden\" id=\"lpren_contr\" name=\"lista_prenota\" value=\"$lista_prenota_contr\">
<input type=\"hidden\" id=\"dini_contr\" name=\"data_inizio_selezione\" value=\"$data_inizio_selezione\">
<input type=\"hidden\" id=\"dfine_contr\" name=\"data_fine_selezione\" value=\"$data_fine_tab\">
".ucfirst(mex("documento di tipo",$pag))."
 <select name=\"numero_contratto\">$option_num_contr</select>
 <button class=\"vdoc\" type=\"submit\"><div>".ucfirst(mex("visualizza",$pag))."</div></button>
</div></form>";
} # fine if ($option_num_contr and $show_bar != "NO")

echo "</td></tr><tr><td style=\"height: 12px;\"></td></tr></table>";

if ($show_bar != "NO") {
if (!$mobile_device) echo "<div style=\"text-align: center; font-size: 80%; padding-bottom: 6px;\">".mex("Premere su una prenotazione e trascinarla per spostarla in un nuovo appartamento",'unit.php').".
 ".mex("Trascinare l'inizio o la fine di una prenotazione per cambiare la data di arrivo o partenza",$pag).".</div>";
echo "<table style=\"margin-left: auto; margin-right: auto;\" cellspacing=\"0\" cellpadding=\"1\"><tr>
<td>&nbsp;</td><td style=\"background-color: $colore_rosso; width: 60px; border: solid black 1px; border-radius: 10px;\">&nbsp;</td><td>&nbsp;</td>
<td style=\"width: 50px;\"></td>
<td>&nbsp;</td><td style=\"background-color: $colore_arancione; width: 60px; border: solid black 1px; border-radius: 10px;\">&nbsp;</td><td>&nbsp;</td>
<td style=\"width: 50px;\"></td>
<td>&nbsp;</td><td style=\"background-color: $colore_giallo; width: 60px; border: solid black 1px; border-radius: 10px;\">&nbsp;</td><td>&nbsp;</td>
<td style=\"width: 50px;\"></td>
<td>&nbsp;</td><td style=\"background-color: $colore_trasp; width: 60px; border: solid black 1px; border-radius: 10px;\">&nbsp;</td><td>&nbsp;</td>
</tr><tr><td colspan=\"3\" align=\"center\" style=\"font-size: x-small; max-width: 180px; vertical-align: top;\">
".mex("Prenotazione non confermata",$pag)."</td>
<td></td><td colspan=\"3\" align=\"center\" style=\"font-size: x-small; max-width: 180px; vertical-align: top;\">
".mex("Prenotazione confermata, caparra non pagata",$pag)."</td>
<td></td><td colspan=\"3\" align=\"center\" style=\"font-size: x-small; max-width: 180px; vertical-align: top;\">
".mex("Caparra pagata",$pag)."</td>
<td></td><td colspan=\"3\" align=\"center\" style=\"font-size: x-small; max-width: 180px; vertical-align: top;\">
".mex("Tutto pagato",$pag)."</td>
</tr></table><br>";
} # fine if ($show_bar != "NO")

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"bkmm\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
</div></form><br>";


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine else if ($manda_xml)

} # fine if ($anno_utente_attivato == "SI" and $priv_vedi_tab_mesi != "n")
} # fine if ($id_utente)



?>

