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

$pag = "modifica_costi.php";
$titolo = "HotelDruid: Modifica Costi";
$base_js = 1;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
include("./includes/funzioni_costi_agg.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tablebeniinventario = $PHPR_TAB_PRE."beniinventario";
$tablemagazzini = $PHPR_TAB_PRE."magazzini";
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$tablegruppi = $PHPR_TAB_PRE."gruppi";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_periodi = substr($priv_vedi_tab,3,1);
$priv_vedi_tab_appartamenti = substr($priv_vedi_tab,5,1);
if ($priv_vedi_tab_appartamenti == "g") $prendi_gruppi = "SI";
$priv_inventario = risul_query($privilegi_globali_utente,0,'priv_inventario');
$priv_vedi_beni_inv = substr($priv_inventario,0,1);
$priv_vedi_inv_mag = substr($priv_inventario,2,1);
$priv_mod_beni_in_mag = substr($priv_inventario,5,1);
if ($priv_mod_beni_in_mag == "g") $prendi_gruppi = "SI";
$priv_vedi_inv_app = substr($priv_inventario,6,1);
$priv_mod_beni_in_app = substr($priv_inventario,8,1);
if ($priv_mod_beni_in_app == "g") $prendi_gruppi = "SI";
if ($priv_vedi_beni_inv == "g" or $priv_vedi_inv_mag == "g" or $priv_vedi_inv_app == "g") $prendi_gruppi = "SI";
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
$priv_mod_costo_agg = substr($priv_ins_tariffe,2,1);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)

$priv_app_gruppi = "NO";
if ($priv_vedi_tab_appartamenti == "g") $priv_app_gruppi = "SI";
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
for ($num1 = 0 ; $num1 < count($tariffe_consentite_tmp) ; $num1++) if ($tariffe_consentite_tmp[$num1]) $tariffe_consentite_vett_gr[$idutente_gruppo][$tariffe_consentite_tmp[$num1]] = "SI";
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

} # fine if ($idutente_gruppo != $id_utente)
} # fine for $num2
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")

} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$priv_vedi_tab_periodi = "s";
$priv_vedi_tab_appartamenti = "s";
$priv_vedi_beni_inv = "s";
$priv_vedi_inv_mag = "s";
$priv_mod_beni_in_mag = "s";
$priv_vedi_inv_app = "s";
$priv_mod_beni_in_app = "s";
$attiva_regole1_consentite = "n";
$attiva_tariffe_consentite = "n";
$attiva_costi_agg_consentiti = "n";
$priv_mod_costo_agg = "s";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI" and $priv_mod_costo_agg != "n") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();


$tabelle_lock = array("$tablenometariffe");
$altre_tab_lock = array("$tableperiodi","$tableappartamenti","$tableregole","$tablebeniinventario","$tablemagazzini");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$numero_tariffe);
$numcaselle_max = 120;


if ($priv_vedi_beni_inv == "p" or $priv_vedi_beni_inv == "g") {
$condizione_beni_propri = "where ( utente_inserimento = '$id_utente'";
if ($priv_vedi_beni_inv == "g") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_beni_propri .= " or utente_inserimento = '$idut_gr'";
} # fine if ($priv_vedi_beni_inv == "g")
$condizione_beni_propri .= " )";
} # fine if ($priv_vedi_beni_inv == "p" or $priv_vedi_beni_inv == "g")
else $condizione_beni_propri = "";
if ($priv_vedi_inv_mag == "p" or $priv_vedi_inv_mag == "g") {
$condizione_mag_propri = "where ( utente_inserimento = '$id_utente'";
if ($priv_vedi_inv_mag == "g") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_mag_propri .= " or utente_inserimento = '$idut_gr'";
} # fine if ($priv_vedi_inv_mag == "g")
$condizione_mag_propri .= " )";
} # fine if ($priv_vedi_inv_mag == "p" or $priv_vedi_inv_mag == "g")
else $condizione_mag_propri = "";

if ($priv_vedi_tab_appartamenti != "n") {
$appartamenti = esegui_query("select * from $tableappartamenti order by idappartamenti");
$num_appartamenti = numlin_query($appartamenti);
if ($priv_vedi_tab_appartamenti != "s") {
if (!function_exists("trova_app_consentiti")) include("./includes/funzioni_appartamenti.php");
if ($priv_vedi_tab_appartamenti != "g") $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,$id_utente,$tableregole,$tablenometariffe);
else $appartamenti_consentiti = trova_app_consentiti($appartamenti,$num_appartamenti,$attiva_regole1_consentite_gr,$regole1_consentite_gr,$priv_mod_assegnazione_app_gr,$priv_mod_prenotazioni_gr,$priv_ins_assegnazione_app_gr,$priv_ins_nuove_prenota_gr,$attiva_tariffe_consentite_gr,$tariffe_consentite_vett_gr,$id_utente,$tableregole,$tablenometariffe);
} # fine if ($priv_vedi_tab_appartamenti != "s")
} # fine if ($priv_vedi_tab_appartamenti != "n")
else $num_appartamenti = 0;


$num_costo = (string) $dati_ca['id'][$idntariffe];
if ($attiva_costi_agg_consentiti != "n" and $costi_agg_consentiti_vett[$idntariffe] != "SI") $num_costo = "";
if ($num_costo != "") {



if ($modifica_costo) {
$mostra_form_iniziale = "NO";
$conflitti = "";
$note_mantenere = "";

if (get_magic_quotes_gpc()) $n_nome = (string) stripslashes($n_nome);
if ($n_nome != "") $nomecostoagg = (string) htmlspecialchars($n_nome);
else $nomecostoagg = (string) $dati_ca[$num_costo]['nome'];
$nomecostoagg = str_replace("#?&","",$nomecostoagg);
$nomecostoagg = str_replace("#@&","",$nomecostoagg);
$nomecostoagg = substr($nomecostoagg,0,40);
if (!$nomecostoagg) {
$messaggio_errore = mex("Si deve inserire il nome del costo aggiuntivo",$pag).".<br>";
$errore = "SI";
} # fine if (!$nomecostoagg)
if (get_magic_quotes_gpc()) $categoria_ca = stripslashes($categoria_ca);
$categoria_ca = htmlspecialchars($categoria_ca);
if ($tipo_ca != "u" and $tipo_ca != "s") $errore = "SI";
if ($errore != "SI" and (($n_nome != "" and $n_nome != $dati_ca[$num_costo]['nome']) or $tipo_ca != $dati_ca[$num_costo]['tipo'])) {
$esiste_costo = esegui_query("select idntariffe from $tablenometariffe where nomecostoagg = '".aggslashdb($nomecostoagg)."' and tipo_ca $LIKE '".$tipo_ca."_'");
if (numlin_query($esiste_costo) > 0) {
$messaggio_errore = mex("Costo aggiuntivo già esistente",$pag).".<br>";
$errore = "SI";
} # fine if (numlin_query($esiste_costo) > 0)
} # fine if ($errore != "SI" and...

$valore_f_ca = formatta_soldi($valore_f_ca);
$valore_p_ca = formatta_soldi($valore_p_ca);
$arrotonda_ca = formatta_soldi($arrotonda_ca);
$tasseperc_ca = formatta_soldi($tasseperc_ca);
if (!strcmp($valore_f_ca,"") and !strcmp($valore_p_ca,"")) $errore = "SI";
if (strcmp($valore_f_ca,"") and controlla_soldi($valore_f_ca) == "NO") $errore = "SI";
if (strcmp($valore_p_ca,"") and (controlla_soldi($valore_p_ca) == "NO" or !strcmp($arrotonda_ca,"") or controlla_soldi($arrotonda_ca,"SI") == "NO")) $errore = "SI";
if ($valore_p_ca and $tipo_percentuale != "tariffa" and $tipo_percentuale != "tariffafissa" and $tipo_percentuale != "tariffapers" and $tipo_percentuale != "totale" and $tipo_percentuale != "caparra" and $tipo_percentuale != "resto") $errore = "SI";
if ($valore_p_ca and $tipo_ca == "s" and substr($tipo_percentuale,0,7) != "tariffa") { $errore = "SI"; $conflitti .= "2-3;"; }
if (($tasseperc_ca and controlla_soldi($tasseperc_ca) == "NO") or $tasseperc_ca > 100 or $tasseperc_ca < 0) $errore = "SI";
if ($tipo_tasse != "p" and $tipo_tasse != "t") $errore = "SI";

$associa_tariffe_prec = 0;
if ($tipo_ca != "s") $associasett = "";
if ($tipo_ca == "s" and $associasett == "") $errore = "SI";
if ($valore_p_ca and $associasett == "n") { $errore = "SI"; $conflitti .= "3-10;"; }
if ($associasett != "" and $associasett != "n" and $associasett != "s") $errore = "SI";
$associa_tariffe = "n";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
if (${"sel".$tariffa} != "" and ${"sel".$tariffa} != "s") $errore = "SI";
if (${"minmax".$tariffa} != "" and ${"minmax".$tariffa} != "min" and ${"minmax".$tariffa} != "max" and ${"minmax".$tariffa} != "eq" and ${"minmax".$tariffa} != "tra") $errore = "SI";
if (${"valminmax".$tariffa} and (controlla_num_pos(${"valminmax".$tariffa}) == "NO" or ${"valminmax".$tariffa} == 0)) $errore = "SI";
if (${"sel".$tariffa} and ${"minmax".$tariffa} and !${"valminmax".$tariffa}) $errore = "SI";
if (${"valminmax2".$tariffa} and (controlla_num_pos(${"valminmax2".$tariffa}) == "NO" or ${"valminmax2".$tariffa} == 0)) $errore = "SI";
if (${"sel".$tariffa} and ${"minmax".$tariffa} == "tra" and (!${"valminmax2".$tariffa} or ${"valminmax2".$tariffa} <= ${"valminmax".$tariffa})) $errore = "SI";
if (${"sel".$tariffa} == "s" and ${"tipo_associa_".$tariffa} != "p" and ${"tipo_associa_".$tariffa} != "r" and ${"tipo_associa_".$tariffa} != "s") $errore = "SI";
if (${"sel".$tariffa} == "s") $associa_tariffe = "s";
} # fine if ($attiva_tariffe_consentite == "n" or...
if ($dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa]) $associa_tariffe_prec = 1;
} # fine for $numtariffa
if ($assegna_da_giorno_ini != "s") $assegna_da_giorno_ini = "";
if ($assegna_da_giorno_ini == "s" and $ass_gio_ini_segno != "meno" and $ass_gio_ini_segno != "piu") $errore = "SI";
if ($assegna_da_giorno_ini == "s" and (!$ass_gio_ini_num or controlla_num_pos($ass_gio_ini_num) == "NO")) $errore = "SI";
if ($assegna_prenota_contemp != "s") $assegna_prenota_contemp = "";
if ($assegna_prenota_contemp == "s" and (!$ass_pren_cont_num or controlla_num_pos($ass_pren_cont_num) == "NO")) $errore = "SI";

if ($tipo_ca == "s") {
if ($numsett_ca != "t" and $numsett_ca != "m" and $numsett_ca != "c" and $numsett_ca != "s" and $numsett_ca != "n" and $numsett_ca != "g") $errore = "SI";
if ($tipo_periodi != "g" and $numsett_ca == "g") $errore = "SI";
if (!$valore_p_ca and $associasett != "s") $pos_menouna = "";
if (($pos_menouna != "p" and $pos_menouna != "u") and ($valore_p_ca or $associasett == "s")) $errore = "SI";
if ($associa_tariffe != "n" and $numsett_ca == "c") { $errore = "SI"; $conflitti .= "5-9;"; }
if ($assegna_da_giorno_ini and $numsett_ca == "c") { $errore = "SI"; $conflitti .= "6-9;"; }
if ($assegna_prenota_contemp and $numsett_ca == "c") { $errore = "SI"; $conflitti .= "7-9;"; }
if ($associasett != "s" and ($numsett_ca == "s" or $numsett_ca == "n" or $numsett_ca == "g")) { $errore = "SI"; $conflitti .= "9-10;"; }
if ($numsett_ca == "s") {
if (controlla_num_pos($val1_prime_si) == "NO" or $val1_prime_si ==  0) $errore = "SI";
if (controlla_num_pos($val2_prime_si) == "NO" or $val2_prime_si ==  0) $errore = "SI";
} # fine if ($numsett_ca == "s")
if ($numsett_ca == "n") {
if (controlla_num_pos($val1_prime_no) == "NO" or $val1_prime_no ==  0) $errore = "SI";
if (controlla_num_pos($val2_prime_no) == "NO" or $val2_prime_no ==  0) $errore = "SI";
} # fine if ($numsett_ca == "n")
if ($lun_sel != "" and $lun_sel != "s") $errore = "SI";
if ($mar_sel != "" and $mar_sel != "s") $errore = "SI";
if ($mer_sel != "" and $mer_sel != "s") $errore = "SI";
if ($gio_sel != "" and $gio_sel != "s") $errore = "SI";
if ($ven_sel != "" and $ven_sel != "s") $errore = "SI";
if ($sab_sel != "" and $sab_sel != "s") $errore = "SI";
if ($dom_sel != "" and $dom_sel != "s") $errore = "SI";
} # fine if ($tipo_ca == "s")
else $numsett_ca = "";

if ($moltiplica_ca == "1") $agg_moltiplica = $agg_moltiplica_1;
if ($moltiplica_ca == "p") $agg_moltiplica = $agg_moltiplica_p;
if ($moltiplica_ca == "t") $agg_moltiplica = $agg_moltiplica_t;
if ($moltiplica_ca == "c") $agg_moltiplica = 0;
if ($moltiplica_ca != "1" and $moltiplica_ca != "c" and $moltiplica_ca != "p" and $moltiplica_ca != "t") $errore = "SI";
if ($moltiplica_ca == "c") {
if ($associa_tariffe != "n") { $errore = "SI"; $conflitti .= "5-11;"; }
if ($assegna_da_giorno_ini) { $errore = "SI"; $conflitti .= "6-11;"; }
if ($assegna_prenota_contemp) { $errore = "SI"; $conflitti .= "7-11;"; }
if ($tipo_moltmax != "n" and $tipo_moltmax != "p" and $tipo_moltmax != "t") $errore = "SI";
if ($tipo_moltmax == "n" and controlla_num_pos($moltmax) == "NO") $errore = "SI";
if ($tipo_moltmax == "p" and controlla_num_pos($meno_moltmax_p) == "NO") $errore = "SI";
if ($tipo_moltmax == "t" and controlla_num_pos($meno_moltmax_t) == "NO") $errore = "SI";
} # fine if ($moltiplica_ca == "c")
else $tipo_moltmax = "n";
if (!strcmp($agg_moltiplica,"")) $agg_moltiplica = 0;
if (controlla_num($agg_moltiplica) == "NO") $errore = "SI";

if (!$beni_inv_elimina) $beni_inv_elimina = "nessuno";
if ($beni_inv_elimina != "nessuno" and $beni_inv_elimina != "sel") $errore = "SI";
if ($beni_inv_elimina != "nessuno" and ($priv_vedi_beni_inv == "n" or (($priv_vedi_inv_mag == "n" or $priv_mod_beni_in_mag == "n") and ($priv_vedi_inv_app == "n" or $priv_mod_beni_in_app == "n")))) $errore = "SI";
if ($beni_inv_elimina == "sel") {
if (controlla_num_pos($num_beni_inv_elimina_sel) == "NO" or $num_beni_inv_elimina_sel == 0) $errore = "SI";
else {
unset($bene_gia_sel);
for ($num1 = 1 ; $num1 <= $num_beni_inv_elimina_sel ; $num1++) {
$bene_esist = esegui_query("select * from $tablebeniinventario where idbeniinventario = '".aggslashdb(${"bene_inv_sel".$num1})."' ".str_replace("where","and",$condizione_beni_propri)." ");
if (numlin_query($bene_esist) != 1) $errore = "SI";
if ($bene_gia_sel[${"bene_inv_sel".$num1}]) $errore = "SI";
else $bene_gia_sel[${"bene_inv_sel".$num1}] = 1;
if (controlla_num_pos(${"molt_bene_inv_sel".$num1}) == "NO" or ${"molt_bene_inv_sel".$num1} == 0) $errore = "SI";
} # fine for $num1
if ($tipo_bie == "a") {
if ($priv_vedi_inv_app == "n" or $priv_mod_beni_in_app == "n") $errore = "SI";
if ($associa_tariffe != "n") { $errore = "SI"; $conflitti .= "5-12;"; }
if ($assegna_da_giorno_ini) { $errore = "SI"; $conflitti .= "6-12;"; }
if ($assegna_prenota_contemp) { $errore = "SI"; $conflitti .= "7-12;"; }
if ($associasett == "s") { $errore = "SI"; $conflitti .= "10-12;"; }
if ($moltiplica_ca == "t" or $tipo_moltmax == "t") { $errore = "SI"; $conflitti .= "11-12;"; }
} # fine if ($tipo_bie == "a")
} # fine else if (controlla_num_pos($num_beni_inv_elimina_sel) == "NO" or...
} # fine if ($beni_inv_elimina == "sel")
if ($beni_inv_elimina != "nessuno" and $tipo_bie != "a" and $tipo_bie != "m") $errore = "SI";
if (($priv_vedi_inv_mag == "n" or $priv_mod_beni_in_mag == "n") and $tipo_bie == "m") $errore = "SI";
if ($beni_inv_elimina != "nessuno" and $tipo_bie == "m" and !$mag_bie_sel) $errore = "SI";
if ($mag_bie_sel) {
$mag_esist = esegui_query("select * from $tablemagazzini where idmagazzini = '".aggslashdb($mag_bie_sel)."' ".str_replace("where","and",$condizione_mag_propri)." ");
if (numlin_query($mag_esist) != 1) $errore = "SI";
} # fine if ($mag_bie_sel)

if ($periodi_permessi != "tutti" and $periodi_permessi != "sel") $errore = "SI";
if ($periodi_permessi == "sel") {
if (controlla_num_pos($num_periodi_permessi_sel) == "NO" or $num_periodi_permessi_sel == 0) $errore = "SI";
else {
$file_date_int = implode("",file(C_DATI_PATH."/selectperiodi$anno.1.php"));
$ultima_data = 0;
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
if (str_replace("\\\"".${"pp_dal".$num1}."\\\">","",$file_date_int) == $file_date_int) { $errore = "SI"; $conflitti .= "13;"; }
if (str_replace("\\\"".${"pp_al".$num1}."\\\">","",$file_date_int) == $file_date_int) { $errore = "SI"; $conflitti .= "13;"; }
$nuova_ultima_data = str_replace("-","",${"pp_dal".$num1});
if ($nuova_ultima_data <= $ultima_data) { $errore = "SI"; $conflitti .= "13;"; }
else $ultima_data = $nuova_ultima_data;
$nuova_ultima_data = str_replace("-","",${"pp_al".$num1});
if ($nuova_ultima_data <= $ultima_data) { $errore = "SI"; $conflitti .= "13;"; }
else $ultima_data = $nuova_ultima_data;
} # fine for $num1
} # fine else if (controlla_num_pos($num_periodi_permessi_sel) == "NO" or...
} # fine if ($periodi_permessi == "sel")
if ($tipo_pp != "t" and $tipo_pp != "u" and $tipo_pp != "p") $errore = "SI";
if ($tipo_ca != "s" and $tipo_pp == "p") $errore = "SI";

for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
if (${"app_incomp_".$num1} != "" and ${"app_incomp_".$num1} != "i") $errore = "SI";
if (${"app_incomp_".$num1} == "i") {
$app_incompatibili = "SI";
if ($priv_vedi_tab_appartamenti != "s" and $appartamenti_consentiti[${"idapp_incomp_".$num1}] == "NO") $errore = "SI";
} # fine if (${"app_incomp_".$num1} == "i")
} # fine for $num1
if ($app_incompatibili == "SI") {
if ($associa_tariffe != "n") { $errore = "SI"; $conflitti .= "5-14;"; }
if ($assegna_da_giorno_ini) { $errore = "SI"; $conflitti .= "6-14;"; }
if ($assegna_prenota_contemp) { $errore = "SI"; $conflitti .= "7-14;"; }
} # fine ($app_incompatibili == "SI")

$tariffe_incompatibili = "NO";
$tariffe_incompatibili_prec = 0;
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
if (${"incomp_".$tariffa} != "" and ${"incomp_".$tariffa} != "i") $errore = "SI";
if ((${"sel".$tariffa} == "s" and $associa_tariffe != "n") and ${"incomp_".$tariffa} != "") {
$errore = "SI";
if (!strstr(";".$conflitti,";5-8;")) $conflitti .= "5-8;";
} # fine if ((${"sel".$tariffa} == "s" and $associa_tariffe != "n") and ${"incomp_".$tariffa} != "")
if (${"incomp_".$tariffa} == "i") $tariffe_incompatibili = "SI";
} # fine if ($attiva_tariffe_consentite == "n" or...
if ($dati_ca[$num_costo]["incomp_".$tariffa]) $tariffe_incompatibili_prec = 1;
} # fine for $numtariffa

if ($combina_ca != "s" or $raggruppa_ca != "s") $combina_ca = "n";
if (($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) and $mostra_ca != "n") { 
$errore = "SI";
$messaggio_errore .= "".mex("Non si può <b style=\"font-weight: normal; color: red;\">contemporaneamente</b> mostrare il costo nella pagina di inserimento delle prenotazioni ed assegnarlo automaticamente con una tariffa",$pag).".<br>";
if ($associa_tariffe != "n") $conflitti .= "5-15;";
if ($assegna_da_giorno_ini) $conflitti .= "6-15;";
if ($assegna_prenota_contemp) $conflitti .= "7-15;";
} # fine if (($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) and $mostra_ca != "n")
if (($associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp) and $mostra_ca != "s" and $mostra_ca != "n") $errore = "SI";
if ($raggruppa_ca != "s" and $raggruppa_ca != "n") $errore = "SI";
if ($combina_ca == "s") {
if (trim($categoria_ca) == "") { $errore = "SI"; $conflitti .= "1-16;"; }
if ($associa_tariffe != "n") { $errore = "SI"; $conflitti .= "5-16;"; }
if ($assegna_da_giorno_ini) { $errore = "SI"; $conflitti .= "6-16;"; }
if ($assegna_prenota_contemp) { $errore = "SI"; $conflitti .= "7-16;"; }
if ($beni_inv_elimina != "nessuno" and $tipo_bie == "a") { $errore = "SI"; $conflitti .= "12-16;"; }
if ($app_incompatibili == "SI") { $errore = "SI"; $conflitti .= "14-16;"; }
} # fine if ($combina_ca == "s")
if ($letto_ca != "n" and $letto_ca != "s") $errore = "SI";
if ($letto_ca != "n") {
if ($valore_p_ca and substr($tipo_percentuale,0,7) != "tariffa") { $errore = "SI"; $conflitti .= "3-17;"; }
if ($moltiplica_ca == "p" or $moltiplica_ca == "t" or $tipo_moltmax == "p" or $tipo_moltmax == "t") { $errore = "SI"; $conflitti .= "11-17;"; }
} # fine if ($letto_ca != "n")
if ($escludi_da_tot != "n" and $escludi_da_tot != "s") $errore = "SI";
if ($limite_ca != "n" and $limite_ca != "s") $errore = "SI";
if ($limite_ca != "n") {
if ($tipo_ca == "s" and $associasett != "s" and $numsett_ca != "t") { $errore = "SI"; $conflitti .= "9,10-19;"; }
if ($tipo_ca == "s" and $associasett != "s" and $periodi_permessi != "tutti" and $tipo_pp == "p") { $errore = "SI"; $conflitti .= "9,13-19;"; }
if (controlla_num_pos($numlimite_ca) == "NO" or $numlimite_ca == 0) $errore = "SI";
} # fine if ($limite_ca != "n")

if ($mantenere_percentuale != "s" or !$valore_p_ca) $mantenere_percentuale = "n";
if ($mantenere_percentuale == "n" and $valore_p_ca and $dati_ca[$num_costo]['tipo_val'] == "f") $note_mantenere .= "3;";
if (($mantenere_percentuale == "n" and $valore_p_ca) and ($tipo_ca == "s" and $numsett_ca != "c")) $mantenere_numsett = "n";
if ($mantenere_numsett != "s" or ($tipo_ca != "s" or $numsett_ca == "c")) $mantenere_numsett = "n";
if ($mantenere_numsett == "n" and $tipo_ca == "s" and $numsett_ca != "c" and $dati_ca[$num_costo]['tipo'] != "s") $note_mantenere .= "2;";
if ($mantenere_numsett == "n" and $tipo_ca == "s" and $numsett_ca != "c" and $dati_ca[$num_costo]['var_numsett'] == "c") $note_mantenere .= "9;";
if (($mantenere_percentuale == "n" and $valore_p_ca) and ($moltiplica_ca != "c" and $moltiplica_ca != "1")) $mantenere_moltiplica = "n";
if ($mantenere_moltiplica != "s" or ($moltiplica_ca == "c" and $tipo_moltmax == "n" and !$moltmax)) $mantenere_moltiplica = "n";
if ($mantenere_moltiplica == "n" and ($moltiplica_ca != "c" or $tipo_moltmax != "n" or $moltmax) and $dati_ca[$num_costo]['moltiplica'] == "c" and $dati_ca[$num_costo]['molt_max'] == "x") $note_mantenere .= "11;";
if ($mantenere_beniinv != "s" or $beni_inv_elimina == "nessuno") $mantenere_beniinv = "n";
if ($mantenere_beniinv == "n" and $beni_inv_elimina != "nessuno" and !$dati_ca[$num_costo]['beniinv_orig']) $note_mantenere .= "12;";
if ($mantenere_periodip != "s" or $periodi_permessi == "tutti") $mantenere_periodip = "n";
if ($mantenere_periodip == "n" and $periodi_permessi != "tutti" and !$dati_ca[$num_costo]['periodipermessi_orig']) $note_mantenere .= "13;";
if ($mantenere_appi != "s" or $app_incompatibili != "SI") $mantenere_appi = "n";
if ($mantenere_appi == "n" and $app_incompatibili == "SI" and !$dati_ca[$num_costo]['appincompatibili']) $note_mantenere .= "14;";
if ($mantenere_tariffea != "s" or $associa_tariffe == "n") $mantenere_tariffea = "n";
if ($mantenere_tariffea == "n" and $associa_tariffe != "n" and !$associa_tariffe_prec) $note_mantenere .= "5;";
if ($mantenere_tariffei != "s" or $tariffe_incompatibili != "SI") $mantenere_tariffei = "n";
if ($mantenere_tariffei == "n" and $tariffe_incompatibili == "SI" and !$tariffe_incompatibili_prec) $note_mantenere .= "8;";
if ($mantenere_comb != "s" or $combina_ca != "s") $mantenere_comb = "n";
if ($mantenere_comb == "n" and $combina_ca == "s" and $dati_ca[$num_costo]['combina'] != "s") $note_mantenere .= "16;";



if ($errore != "SI") {

if ($note_mantenere) {
echo "<span class=\"colblu\">".mex("Attenzione",$pag)."</span>, ".mex("le seguenti opzioni sono state attivate senza essere mantenute quando la prenotazione viene modificata",$pag).": ";
$note_mantenere = explode(";",substr($note_mantenere,0,-1));
for ($num1 = 0 ; $num1 < count($note_mantenere) ; $num1++) {
if ($num1) echo ", ";
echo "<em>".$note_mantenere[$num1]."</em>";
} # fine for $num1
echo ".<br><br>";
} # fine if ($note_mantenere)


$modificato = "NO";
if ($nomecostoagg != $dati_ca[$num_costo]['nome']) {
$nomecostoagg = aggslashdb($nomecostoagg);
esegui_query("update $tablenometariffe set nomecostoagg = '$nomecostoagg' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($nomecostoagg != $dati_ca[$num_costo]['nome'])
if ($valore_p_ca) $tipo_valore_ca = "p";
else $tipo_valore_ca = "f";
if ($tipo_valore_ca == "f") $valore_p_ca = "";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "tariffafissa") $tipo_valore_ca = "q";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "tariffapers") $tipo_valore_ca = "s";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "totale") $tipo_valore_ca = "t";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "caparra") $tipo_valore_ca = "c";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "resto") $tipo_valore_ca = "r";
if ($tipo_ca != $dati_ca[$num_costo]['tipo'] or $tipo_valore_ca != $dati_ca[$num_costo]['tipo_val']) {
esegui_query("update $tablenometariffe set tipo_ca = '$tipo_ca$tipo_valore_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($tipo_ca != $dati_ca[$num_costo]['tipo'] or $tipo_valore_ca != $dati_ca[$num_costo]['tipo_val'])
$valore_ca = $valore_f_ca;
if (!strcmp($valore_ca,"")) $valore_ca = 0;
$valore_ca = formatta_soldi($valore_ca);
if ($valore_ca != $dati_ca[$num_costo]['valore']) {
esegui_query("update $tablenometariffe set valore_ca = '$valore_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($valore_ca != $dati_ca[$num_costo]['valore'])
$valore_perc_ca = formatta_soldi($valore_p_ca);
if ($valore_perc_ca != $dati_ca[$num_costo]['valore_perc']) {
esegui_query("update $tablenometariffe set valore_perc_ca = '$valore_perc_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($valore_perc_ca != $dati_ca[$num_costo]['valore_perc'])
if ($moltiplica_ca != "c" or ($tipo_moltmax == "n" and !$moltmax)) {
$tipo_moltmax = "x";
$moltmax = "";
} # fine if ($moltiplica_ca != "c" or ($tipo_moltmax == "n" and !$moltmax))
else {
if ($tipo_moltmax == "p") $moltmax = $meno_moltmax_p;
if ($tipo_moltmax == "t") $moltmax = $meno_moltmax_t;
} # fine if ($moltiplica_ca != "c" or ($tipo_moltmax == "n" and !$moltmax))
$moltiplica_ca .= $tipo_moltmax.$agg_moltiplica.",".$moltmax;
if ($moltiplica_ca != $dati_ca[$num_costo]['moltiplica'].$dati_ca[$num_costo]['molt_max'].$dati_ca[$num_costo]['molt_agg'].",".$dati_ca[$num_costo]['molt_max_num']) {
esegui_query("update $tablenometariffe set moltiplica_ca = '$moltiplica_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($moltiplica_ca != $dati_ca[$num_costo]['moltiplica'].$dati_ca[$num_costo]['molt_max'].$dati_ca[$num_costo]['molt_agg'].",".$dati_ca[$num_costo]['molt_max_num'])
$variazione_ca = $mantenere_percentuale.$mantenere_numsett.$mantenere_moltiplica.$mantenere_periodip.$mantenere_tariffea.$mantenere_tariffei.$mantenere_beniinv.$mantenere_appi.$mantenere_comb;
if ($variazione_ca != $dati_ca[$num_costo]['var_percentuale'].$dati_ca[$num_costo]['var_numsett'].$dati_ca[$num_costo]['var_moltiplica'].$dati_ca[$num_costo]['var_periodip'].$dati_ca[$num_costo]['var_tariffea'].$dati_ca[$num_costo]['var_tariffei'].$dati_ca[$num_costo]['var_beniinv'].$dati_ca[$num_costo]['var_appi'].$dati_ca[$num_costo]['var_comb']) {
esegui_query("update $tablenometariffe set variazione_ca = '$variazione_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($variazione_ca != $dati_ca[$num_costo]['var_percentuale'].$dati_ca[$num_costo]['var_numsett'].$dati_ca[$num_costo]['var_moltiplica'].$dati_ca[$num_costo]['var_periodip'].$dati_ca[$num_costo]['var_tariffea'].$dati_ca[$num_costo]['var_tariffei'].$dati_ca[$num_costo]['var_beniinv'].$dati_ca[$num_costo]['var_appi'].$dati_ca[$num_costo]['var_comb'])
$mostra_ca = $mostra_ca.$raggruppa_ca.$combina_ca.$escludi_da_tot;
if ($mostra_ca != $dati_ca[$num_costo]['mostra'].$dati_ca[$num_costo]['raggruppa'].$dati_ca[$num_costo]['combina'].$dati_ca[$num_costo]['escludi_tot_perc']) {
esegui_query("update $tablenometariffe set mostra_ca = '$mostra_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($mostra_ca != $dati_ca[$num_costo]['mostra'].$dati_ca[$num_costo]['raggruppa'].$dati_ca[$num1]['combina'].$dati_ca[$num_costo]['escludi_tot_perc'])
if ($categoria_ca != $dati_ca[$num_costo]['categoria']) {
esegui_query("update $tablenometariffe set categoria_ca = '".aggslashdb($categoria_ca)."' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($categoria_ca != $dati_ca[$num_costo]['categoria'])
if ($letto_ca != $dati_ca[$num_costo]['letto']) {
esegui_query("update $tablenometariffe set letto_ca = '$letto_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($letto_ca != $dati_ca[$num_costo]['letto'])
$arrotonda_ca = formatta_soldi($arrotonda_ca);
if ($tipo_valore_ca != "f" and $arrotonda_ca != $dati_ca[$num_costo]['arrotonda']) {
esegui_query("update $tablenometariffe set arrotonda_ca = '$arrotonda_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($tipo_valore_ca != "f" and $arrotonda_ca != $dati_ca[$num_costo]['arrotonda'])
if ($tipo_tasse == "t") $tasseperc_ca = -1;
if ($tasseperc_ca != $dati_ca[$num_costo]['tasseperc']) {
if (!strcmp($tasseperc_ca,"")) esegui_query("update $tablenometariffe set tasseperc_ca = NULL where idntariffe = '$idntariffe'");
else esegui_query("update $tablenometariffe set tasseperc_ca = '$tasseperc_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($tasseperc_ca != $dati_ca[$num_costo]['tasseperc'])
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
if (${"sel".$tariffa} == "s") {
$valtariffa = "s";
if (${"minmax".$tariffa} != "") {
if (${"minmax".$tariffa} == "min") $valtariffa = ">";
if (${"minmax".$tariffa} == "max") $valtariffa = "<";
if (${"minmax".$tariffa} == "eq") $valtariffa = "=";
if (${"minmax".$tariffa} == "tra") $valtariffa = "|";
$valtariffa .= ${"valminmax".$tariffa};
if (${"minmax".$tariffa} == "tra") $valtariffa .= "<".${"valminmax2".$tariffa};
} # fine if (${"minmax".$tariffa} != "")
$valtariffa = ${"tipo_associa_".$tariffa}.$valtariffa;
} # fine if (${"sel".$tariffa} == "s")
else {
$valtariffa = "";
if (${"incomp_".$tariffa} == "i") $valtariffa = "i";
} # fine else if (${"sel".$tariffa} == "s")
$valtariffa_prec = $dati_ca[$num_costo]["tipo_associa_".$tariffa].$dati_ca[$num_costo][$tariffa];
if ($dati_ca[$num_costo]["incomp_".$tariffa]) $valtariffa_prec = "i";
if ($valtariffa != $valtariffa_prec) {
esegui_query("update $tablenometariffe set $tariffa = '$valtariffa' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($valtariffa != $valtariffa_prec)
} # fine if ($attiva_tariffe_consentite == "n" or...
} # fine for $numtariffa
$regoleassegna_ca = "";
if ($assegna_da_giorno_ini) {
if ($ass_gio_ini_segno == "piu") $regoleassegna_ca .= ">".$ass_gio_ini_num;
else $regoleassegna_ca .= "<".$ass_gio_ini_num;
} # fine if ($assegna_da_giorno_ini)
$regoleassegna_ca .= ";";
if ($assegna_prenota_contemp) $regoleassegna_ca .= $ass_pren_cont_num;
if ($regoleassegna_ca != $dati_ca[$num_costo]['assegna_da_ini_prenota'].";".$dati_ca[$num_costo]['assegna_con_num_prenota']) {
esegui_query("update $tablenometariffe set regoleassegna_ca = '$regoleassegna_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($regoleassegna_ca != $dati_ca[$num_costo]['assegna_da_ini_prenota'].";".$dati_ca[$num_costo]['assegna_con_num_prenota'])
if ($associasett != $dati_ca[$num_costo]['associasett']) {
esegui_query("update $tablenometariffe set associasett_ca = '$associasett' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($associasett != $dati_ca[$num_costo]['associasett'])
$valnumsett_ca = $numsett_ca;
if ($numsett_ca == "m" and $pos_menouna != "") $valnumsett_ca .= $pos_menouna;
if ($numsett_ca == "s") $valnumsett_ca .= $val1_prime_si.",".$val2_prime_si;
if ($numsett_ca == "n") $valnumsett_ca .= $val1_prime_no.",".$val2_prime_no;
if ($numsett_ca == "g") {
$giorni_sel = "";
if ($lun_sel == "s") $giorni_sel .= ",1";
if ($mar_sel == "s") $giorni_sel .= ",2";
if ($mer_sel == "s") $giorni_sel .= ",3";
if ($gio_sel == "s") $giorni_sel .= ",4";
if ($ven_sel == "s") $giorni_sel .= ",5";
if ($sab_sel == "s") $giorni_sel .= ",6";
if ($dom_sel == "s") $giorni_sel .= ",7";
if ($giorni_sel) $giorni_sel = substr($giorni_sel,1);
$valnumsett_ca .= $giorni_sel;
} # fine if ($numsett_ca == "g")
if ($valnumsett_ca != $dati_ca[$num_costo]['numsett_orig']) {
esegui_query("update $tablenometariffe set numsett_ca = '$valnumsett_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($valnumsett_ca != $dati_ca[$num_costo]['numsett_orig'])
$beniinv_ca = "";
if ($beni_inv_elimina == "sel") {
if ($tipo_bie == "m") $beniinv_ca = "mag$mag_bie_sel";
else $beniinv_ca = "app";
for ($num1 = 1 ; $num1 <= $num_beni_inv_elimina_sel ; $num1++) $beniinv_ca .= ";".${"bene_inv_sel".$num1}.",".${"molt_bene_inv_sel".$num1};
} # fine if ($beni_inv_elimina == "sel")
if ($beniinv_ca != $dati_ca[$num_costo]['beniinv_orig']) {
esegui_query("update $tablenometariffe set beniinv_ca = '$beniinv_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($beniinv_ca != $dati_ca[$num_costo]['beniinv_orig'])
$periodipermessi_ca = "";
if ($periodi_permessi == "sel") {
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '".${"pp_dal".$num1}."' ");
$idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi');
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '".${"pp_al".$num1}."' ");
$idfineperiodo = risul_query($idfineperiodo,0,'idperiodi');
$periodipermessi_ca .= ",".$idinizioperiodo."-".$idfineperiodo;
} # fine for $num1
$periodipermessi_ca = $tipo_pp.substr($periodipermessi_ca,1);
} # fine if ($periodi_permessi == "sel")
if ($periodipermessi_ca != $dati_ca[$num_costo]['periodipermessi_orig']) {
esegui_query("update $tablenometariffe set periodipermessi_ca = '$periodipermessi_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($periodipermessi_ca != $dati_ca[$num_costo]['periodipermessi_orig'])
$appincompatibili_ca = "";
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
if (${"app_incomp_".$num1} == "i") {
$appincompatibili_ca .= ${"idapp_incomp_".$num1}.",";
} # fine if (${"app_incomp_".$num1} == "i")
} # fine for $num1
$appincompatibili_ca = substr($appincompatibili_ca,0,-1);
if ($appincompatibili_ca != $dati_ca[$num_costo]['appincompatibili']) {
esegui_query("update $tablenometariffe set appincompatibili_ca = '$appincompatibili_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($appincompatibili_ca != $dati_ca[$num_costo]['appincompatibili'])
if ($limite_ca != "s") $numlimite_ca = "";
if (!$dati_ca[$num_costo]['numlimite']) $dati_ca[$num_costo]['numlimite'] = "";
if ($numlimite_ca != $dati_ca[$num_costo]['numlimite']) {
esegui_query("update $tablenometariffe set numlimite_ca = '$numlimite_ca' where idntariffe = '$idntariffe'");
$modificato = "SI";
} # fine if ($numlimite_ca != $dati_ca[$num_costo]['numlimite'])

if ($modificato == "SI") echo mex("Il costo aggiuntivo",$pag)." ".($idntariffe - 10)." ".mex("è stato modificato",$pag).".<br><br>";
else echo mex("Niente da modificare",$pag).".<br><br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"idntariffe\" value=\"$idntariffe\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<button class=\"cont\" type=\"submit\"><div>OK</div></button><br>
</div></form>";

} # fine if ($errore != "SI")

else {
if ($messaggio_errore) echo $messaggio_errore;
else echo mex("I valori inseriti sono <div style=\"display: inline; color: red;\">errati</div> o incongruenti",$pag).".<br>";
if ($conflitti) {
echo "<br>".mex("Opzioni in conflitto",$pag).":<br>";
$conflitti = explode(";",substr($conflitti,0,-1));
for ($num1 = 0 ; $num1 < count($conflitti) ; $num1++) {
if (strstr($conflitti[$num1],"-")) {
$opz = explode("-",$conflitti[$num1]);
echo "&nbsp;<em>".$opz[0]."</em> ".mex("e",$pag)." <em>".$opz[1]."</em><br>";
} # fine if (strstr($conflitti[$num1],"-"))
else echo "&nbsp;<em>$conflitti[$num1]</em><br>";
} # fine for $num1
echo "<br>";
} # fine if ($conflitti)
echo "<br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_costi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idntariffe\" value=\"$idntariffe\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
<br></div></form></div>";
} # fine else if ($errore != "SI")

} # fine if ($modifica_costo)
unlock_tabelle($tabelle_lock);

if ($modifica_costo and $errore != "SI" and $modificato == "SI") {
$lock = 1;
$aggiorna_disp = 0;
$aggiorna_tar = 1;
if (@function_exists('pcntl_fork')) include("./includes/interconnect/aggiorna_ic_fork.php");
else include("./includes/interconnect/aggiorna_ic.php");
} # fine if ($modifica_costo and $errore != "SI" and $modificato == "SI")




if ($mostra_form_iniziale != "NO") {

echo "<h3 id=\"h_exc\"><span>".mex("Modifica il costo aggiuntivo",$pag)." ".($idntariffe - 10).".</span></h3>";

$d_nome = $dati_ca[$num_costo]['nome'];
if ($dati_ca[$num_costo]['mostra'] == "n") $selected_mostra_no = " selected";
if ($dati_ca[$num_costo]['mostra'] == "s") $selected_mostra_si = " selected";
if ($dati_ca[$num_costo]['tipo'] == "u") { $checked_uni = " checked"; $b_uni = "<b>"; $slash_b_uni = "</b>"; }
if ($dati_ca[$num_costo]['tipo'] == "s") { $checked_set = " checked"; $b_set = "<b>"; $slash_b_set = "</b>"; }
$d_prezzo_fisso = $dati_ca[$num_costo]['valore'];
if ($d_prezzo_fisso) {
$b_fiss = "<b>";
$slash_b_fiss = "</b>";
} # fine if ($d_prezzo_fisso)
$d_prezzo_percentuale = $dati_ca[$num_costo]['valore_perc'];
if (!$d_prezzo_percentuale) $d_prezzo_percentuale = 0;
if ($dati_ca[$num_costo]['tipo_val'] != "f") {
$d_tipo_percentuale = $dati_ca[$num_costo]['tipo_val'];
$d_arrotond = $dati_ca[$num_costo]['arrotonda'];
$b_perc = "<b>";
$slash_b_perc = "</b>";
} # fine if ($dati_ca[$num_costo]['tipo_val'] != "f")
$d_tasseperc = $dati_ca[$num_costo]['tasseperc'];
if ($d_prezzo_settimanale != "") {
$d_tipo_costo = "settimanale";
$d_prezzo_costo = $d_prezzo_settimanale;
$checked_set = " checked";
$b_set = "<b>";
$slash_b_set = "</b>";
$d_regolad = substr($d_regole,3,1);
if ($d_regolab == "t") {
$checked_tutte_sett = " checked";
$b_tutte_sett = "<b>";
$slash_b_tutte_sett = "</b>";
} # fine if ($d_regolab == "t")
if ($d_regolab == "m") {
$checked_meno_una_sett = " checked";
$b_meno_una_sett = "<b>";
$slash_b_meno_una_sett = "</b>";
} # fine if ($d_regolab == "m")
if ($d_regolab == "c") {
$checked_chiedi_sett = " checked";
$b_chiedi_sett = "<b>";
$slash_b_chiedi_sett = "</b>";
} # fine if ($d_regolab == "c")
if ($d_regolac == "s") $selected_chiedi_moltiplica_si = " selected";
else $selected_chiedi_moltiplica_no = " selected";
if ($d_regolad == "s") $selected_considera_letto_si = " selected";
else $selected_considera_letto_no = " selected";
} # fine if ($d_prezzo_settimanale != "")
else {
$checked_tutte_sett = " checked";
if ($d_regolab == "s") $selected_chiedi_moltiplica_si = " selected";
else $selected_chiedi_moltiplica_no = " selected";
if ($d_regolac == "s") $selected_considera_letto_si = " selected";
else $selected_considera_letto_no = " selected";
} # fine else if ($d_prezzo_settimanale != "")
/*
$d_tariffe_abbinate = "";
$d_tariffa_abbinata = "";
for ($numtariffa = 1 ; $numtariffa <= ($numero_tariffe-1000) ; $numtariffa = $numtariffa + 1) {
$tariffa = "tariffa".$numtariffa;
if (risul_query($costo_agg,0,$tariffa) == "1") {
$d_tariffe_abbinate .= "$numtariffa,";
$d_tariffa_abbinata[$numtariffa] = "SI";
} # fine if (risul_query($costo_agg,0,$tariffa) == "1")
} # fine for $numtariffa
if ($d_tariffe_abbinate) $d_tariffe_abbinate = substr($d_tariffe_abbinate,0,-1);
*/

echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_costi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idntariffe\" value=\"$idntariffe\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<table cellspacing=2 cellpadding=5>
<tr><td><small>1. </small>".mex("Nome",$pag).": <b>".$dati_ca[$num_costo]['nome']."</b></td>
<td>".mex("Cambia in",$pag)." <input type=\"text\" name=\"n_nome\" size=\"25\"></td></tr>
<tr><td></td><td>".mex("Categoria",$pag).": <input type=\"text\" name=\"categoria_ca\" value=\"".$dati_ca[$num_costo]['categoria']."\" size=\"18\"></td></tr>
<tr><td valign=\"top\"><small>2. </small>".mex("Tipo",$pag).":</td>
<td><label><input type=\"radio\" name=\"tipo_ca\" value=\"u\"$checked_uni>$b_uni".mex("unico",$pag)."$slash_b_uni</label><br>
<label><input type=\"radio\" name=\"tipo_ca\" value=\"s\"$checked_set>$b_set".mex("$parola_settimanale",$pag)."$slash_b_set</label>
</td></tr>
<tr><td valign=\"top\"><small>3. </small>".mex("Prezzo",$pag).":</td>
<td>$b_fiss".mex("fisso",$pag)."$slash_b_fiss:
<input type=\"text\" name=\"valore_f_ca\" size=\"10\" value=\"$d_prezzo_fisso\">$Euro <b>+</b><br>
$b_perc".mex("percentuale",$pag)."$slash_b_perc:
<input type=\"text\" name=\"valore_p_ca\" size=\"5\" maxlength=\"5\" value=\"$d_prezzo_percentuale\">
 ".mex("% su",$pag)."
 <select name=\"tipo_percentuale\">";
if ($d_tipo_percentuale == "p") $selected = " selected";
else $selected = "";
echo "<option value=\"tariffa\"$selected>".mex("la tariffa",$pag)."</option>";
if ($d_tipo_percentuale == "q") $selected = " selected";
else $selected = "";
echo "<option value=\"tariffafissa\"$selected>".mex("parte fissa della tariffa",$pag)."</option>";
if ($d_tipo_percentuale == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"tariffapers\"$selected>".mex("parte per una persona della tariffa",$pag)."</option>";
if ($d_tipo_percentuale == "t") $selected = " selected";
else $selected = "";
echo "<option value=\"totale\"$selected>".mex("il prezzo totale",$pag)."</option>";
if ($d_tipo_percentuale == "c") $selected = " selected";
else $selected = "";
echo "<option value=\"caparra\"$selected>".mex("la caparra",$pag)."</option>";
if ($d_tipo_percentuale == "r") $selected = " selected";
else $selected = "";
echo "<option value=\"resto\"$selected>".mex("totale meno caparra",$pag)."</option>";
if (!strcmp($d_arrotond,"")) {
$d_arrotond = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'arrotond_predef' and idutente = '$id_utente'");
$d_arrotond = risul_query($d_arrotond,0,'valpersonalizza');
} # fine if (!strcmp($d_arrotond,""))
echo "</select> ".mex("arrotondato a",$pag)."
 <input type=\"text\" name=\"arrotonda_ca\" value=\"$d_arrotond\" size=\"5\">$Euro</td></tr>";

if ($d_tasseperc == -1) { $checked_p = ""; $checked_t = " checked"; $d_tasseperc = 0; }
else { $checked_p = " checked"; $checked_t = ""; }
 echo "<tr><td style=\"height: 2px;\"></td></tr>
<tr><td valign=\"top\"><small>4. </small>".mex("Tasse",$pag).":</td>
<td><input type=\"radio\" id=\"t_tas\" name=\"tipo_tasse\" value=\"p\"$checked_p>
<input type=\"text\" name=\"tasseperc_ca\" size=\"5\" maxlength=\"5\" value=\"$d_tasseperc\" onclick=\"document.getElementById('t_tas').checked='1';\"><label for=\"t_tas\">%
 (".mex("il valore del costo si intente con tasse già incluse",'creaprezzi.php').")</label><br>
 <label><input type=\"radio\" name=\"tipo_tasse\" value=\"t\"$checked_t>
".mex("considerare l'intero costo come tasse",'creaprezzi.php')."</label>
</td></tr>


<tr><td style=\"height: 2px;\"></td></tr>
<tr><td valign=\"top\">
<small>5. </small>".mex("Assegna automaticamente con le tariffe",$pag).":</td><td>
<script type=\"text/javascript\">
<!--
function agg_sel_giorni_associa_tariffa (tariffa,valminmax2) {
var sel_corr = document.getElementById('minmax'+tariffa);
var elem_valminmax2 = document.getElementById('valminmax2'+tariffa);
if (sel_corr.options[sel_corr.selectedIndex].value != 'tra') elem_valminmax2.innerHTML = '';
else elem_valminmax2.innerHTML = ' ".mex("e",$pag)." <input type=\"text\" name=\"valminmax2'+tariffa+'\" value=\"'+valminmax2+'\" size=\"3\"> ';
}
-->
</script>
";
$default_tipo_associa = "p";
$minmax_trovato = 0;
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($dati_ca[$num_costo]["tipo_associa_tariffa$numtariffa"]) $default_tipo_associa = $dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa];
$minmax = substr($dati_ca[$num_costo]["tariffa$numtariffa"],0,1);
if (!$default_minmax and $minmax and !$minmax_trovato) {
$default_minmax = $minmax;
$default_valminmax = substr($dati_ca[$num_costo]["tariffa$numtariffa"],1);
if ($default_minmax == "|") {
$default_valminmax = explode("<",$default_valminmax);
$default_valminmax2 = $default_valminmax[1];
$default_valminmax = $default_valminmax[0];
} # fine if ($default_minmax == "|") 
else $default_valminmax2 = "";
$minmax_trovato = 1;
} # fine if (!$default_minmax and $minmax and !$minmax_trovato)
if ($minmax and $default_minmax != $minmax) $default_minmax = "";
if ($default_minmax) {
$valminmax = substr($dati_ca[$num_costo]["tariffa".$numtariffa],1);
if ($default_minmax == "|") {
$valminmax = explode("<",$valminmax);
$valminmax2 = $valminmax[1];
$valminmax = $valminmax[0];
} # fine if ($default_minmax == "|")
if (($valminmax and $valminmax != $default_valminmax) or ($valminmax2 and $valminmax2 != $default_valminmax2)) {
$default_minmax = "";
$default_valminmax = "";
$default_valminmax2 = "";
} # fine if (($valminmax and $valminmax != $default_valminmax) or ($valminmax2 and $valminmax2 != $default_valminmax2))
} # fine if ($default_minmax)
else {
$default_valminmax = "";
$default_valminmax2 = "";
} # fine else if ($default_minmax)
} # fine for $numtariffa

for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$nometariffa = risul_query($rigatariffe,0,$tariffa);
if ($nometariffa == "") {
$nometariffa = $tariffa;
$nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
else $nometariffa_vedi = mex("tariffa",$pag)."$numtariffa $nometariffa";
if ($dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa]) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<table cellspacing=0 cellpadding=0><tr><td valign=\"middle\"><label>
<input type=\"checkbox\" name=\"sel$tariffa\" value=\"s\"$checked>$b"."$nometariffa_vedi$b_slash</label></td>";
echo "<td>&nbsp;(<select name=\"minmax$tariffa\" id=\"minmax$tariffa\" onchange=\"agg_sel_giorni_associa_tariffa('$tariffa','')\">";
$minmax = substr($dati_ca[$num_costo]["tariffa".$numtariffa],0,1);
if (!$minmax and $default_minmax) $minmax = $default_minmax;
if (!$minmax) $selected = " selected";
else $selected = "";
echo "<option value=\"\"$selected>----</option>";
if ($minmax == ">") $selected = " selected";
else $selected = "";
echo "<option value=\"min\"$selected>".mex("minimo",$pag)."</option>";
if ($minmax == "<") $selected = " selected";
else $selected = "";
echo "<option value=\"max\"$selected>".mex("massimo",$pag)."</option>";
if ($minmax == "=") $selected = " selected";
else $selected = "";
echo "<option value=\"eq\"$selected>".mex("esattamente",$pag)."</option>
</select>";
$valminmax = substr($dati_ca[$num_costo]["tariffa".$numtariffa],1);
if (substr($dati_ca[$num_costo]["tariffa".$numtariffa],0,1) == "|") {
$valminmax = explode("<",$valminmax);
$valminmax2 = $valminmax[1];
$valminmax = $valminmax[0];
} # fine if (substr($dati_ca[$num_costo]["tariffa".$numtariffa],0,1) == "|")
if (!$valminmax and $default_valminmax) $valminmax = $default_valminmax;
if (!$valminmax2 and $default_valminmax2) $valminmax2 = $default_valminmax2;
echo "<input type=\"text\" name=\"valminmax$tariffa\" value=\"$valminmax\" size=\"3\">
<span id=\"valminmax2$tariffa\"></span>".mex("$parola_settimane",$pag).")
<script type=\"text/javascript\">
<!--
var sel_corr = document.getElementById('minmax$tariffa');
var n_opt_sel_corr = document.createElement('option');
n_opt_sel_corr.text = '".mex("tra",$pag)."';
n_opt_sel_corr.value = 'tra';
sel_corr.add(n_opt_sel_corr,null);
";
if ($minmax == "|") echo "sel_corr.selectedIndex = 4;
agg_sel_giorni_associa_tariffa('$tariffa','$valminmax2');
";
echo "-->
</script>
</td>";
if ($dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa] == "p") { $checked = " checked"; $b2 = $b; $b_slash2 = $b_slash; }
else { $checked = ""; $b2 = ""; $b_slash2 = ""; }
if (!$dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa] and $default_tipo_associa == "p") $checked = " checked";
echo "<td>&nbsp;<label><input type=\"radio\" name=\"tipo_associa_tariffa$numtariffa\" value=\"p\"$checked> <small>$b2".mex("Se possibile",$pag)."$b_slash2</small></label></td>";
if ($dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa] == "r") { $checked = " checked"; $b2 = $b; $b_slash2 = $b_slash; }
else { $checked = ""; $b2 = ""; $b_slash2 = ""; }
if (!$dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa] and $default_tipo_associa == "r") $checked = " checked";
echo "<td>&nbsp;<label><input type=\"radio\" name=\"tipo_associa_tariffa$numtariffa\" value=\"r\"$checked> <small>$b2".mex("Sempre in periodi permessi",$pag)."$b_slash2</small></label></td>";
if ($dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa] == "s") { $checked = " checked"; $b2 = $b; $b_slash2 = $b_slash; }
else { $checked = ""; $b2 = ""; $b_slash2 = ""; }
if (!$dati_ca[$num_costo]["tipo_associa_tariffa".$numtariffa] and $default_tipo_associa == "s") $checked = " checked";
echo "<td>&nbsp;<label><input type=\"radio\" name=\"tipo_associa_tariffa$numtariffa\" value=\"s\"$checked> <small>$b2".mex("Sempre",$pag)."$b_slash2</small></label></td>";
echo "</tr><tr><td style=\"height: 5px;\"></td></tr></table>";
} # fine if ($attiva_tariffe_consentite == "n" or...
} # fine for $numtariffa
echo "</td></tr>


<tr><td colspan=\"2\"><small>6. ";
if ($dati_ca[$num_costo]['assegna_da_ini_prenota']) $checked = " checked";
else $checked = "";
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) != ">") { $sel_m = " selected"; $sel_p = ""; }
else { $sel_m = ""; $sel_p = " selected"; }
echo "<label><input type=\"checkbox\" id=\"ass_g_i\" name=\"assegna_da_giorno_ini\" value=\"s\"$checked>
".mex("Quando possibile assegna automaticamente se mancano",$pag)."
</label><select name=\"ass_gio_ini_segno\">
<option value=\"meno\"$sel_m>".mex("meno di",$pag)."</option>
<option value=\"piu\"$sel_p>".mex("più di",$pag)."</option>
</select>
<input type=\"text\" name=\"ass_gio_ini_num\" value=\"".substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],1)."\" size=\"3\"><label for=\"ass_g_i\">
".mex("giorni dalla data iniziale della prenotazione quando viene inserita",$pag)."</label>.<br>";
if ($dati_ca[$num_costo]['assegna_con_num_prenota']) $checked = " checked";
else $checked = "";
echo "7. <label><input type=\"checkbox\" id=\"ass_p_c\" name=\"assegna_prenota_contemp\" value=\"s\"$checked>
".mex("Quando possibile assegna automaticamente se si inseriscono",$pag)."
</label><input type=\"text\" name=\"ass_pren_cont_num\" value=\"".$dati_ca[$num_costo]['assegna_con_num_prenota']."\" size=\"3\"><label for=\"ass_p_c\">
".mex("o più prenotazioni contemporaneamente",$pag).".</small></td></tr>

<tr><td valign=\"top\">
<small>8. </small>".mex("Tariffe incompatibili",$pag).":</td><td>";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$nometariffa = risul_query($rigatariffe,0,$tariffa);
if ($nometariffa == "") {
$nometariffa = $tariffa;
$nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
else $nometariffa_vedi = mex("tariffa",$pag)."$numtariffa $nometariffa";
if ($dati_ca[$num_costo]["incomp_tariffa".$numtariffa] == "i") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"incomp_$tariffa\" value=\"i\"$checked>$b"."$nometariffa_vedi$b_slash</label><br>";
} # fine if ($attiva_tariffe_consentite == "n" or...
} # fine for $numtariffa
echo "</td></tr>

<tr><td valign=\"top\">
<small>9. </small>".mex("Numero di $parola_settimane",$pag).": <br><small>(".mex("solo per costi $parola_settimanali",$pag).")</small></td><td>
<table><tr><td>";
$numsett_ca = $dati_ca[$num_costo]['numsett'];
if ($numsett_ca == "t") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
if (!$numsett_ca) $checked = " checked";
echo "<label><input type=\"radio\" name=\"numsett_ca\" value=\"t\"$checked> $b".mex("Tutt$lettera_e",$pag)."$b_slash</label></td></tr>
<tr><td>";
if ($numsett_ca == "m") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" id=\"ns_m\" name=\"numsett_ca\" value=\"m\"$checked> $b".mex("Tutt$lettera_e meno",$pag)."$b_slash";
echo "</label> <select name=\"pos_menouna\" onchange=\"document.getElementById('ns_m').checked='1';\">";
if ($dati_ca[$num_costo]['sett_meno_una'] == "p") $selected = " selected";
else $selected = "";
echo "<option value=\"p\"$selected>".mex("$parola_la prim$lettera_a",$pag)."</option>";
if ($dati_ca[$num_costo]['sett_meno_una'] == "u") $selected = " selected";
else $selected = "";
echo "<option value=\"u\"$selected>".mex("l'ultim$lettera_a",$pag)."</option>";
echo "</select></td></tr>";
echo "<tr><td>";
if ($numsett_ca == "c") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" name=\"numsett_ca\" value=\"c\"$checked> $b".mex("Chiedere",$pag)."$b_slash</label></td></tr>
<tr><td>";
if ($numsett_ca == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
if ($numsett_ca == "s") {
$val1_prime_si = $dati_ca[$num_costo]['num_sett_prime'];
$val2_prime_si = $dati_ca[$num_costo]['num_sett_seconde'];
} # fine if ($numsett_ca == "s")
if (!$val1_prime_si) $val1_prime_si = "1";
if (!$val2_prime_si) $val2_prime_si = "1";
echo "<input type=\"radio\" id=\"ns_s\" name=\"numsett_ca\" value=\"s\"$checked>
 <input type=\"text\" name=\"val1_prime_si\" value=\"$val1_prime_si\" size=\"3\" onfocus=\"document.getElementById('ns_s').checked='1';\">
 <label for=\"ns_s\">$b".mex("$parola_settimane sì",$pag)." ".mex("e",$pag)."$b_slash</label>
 <input type=\"text\" name=\"val2_prime_si\" value=\"$val2_prime_si\" size=\"3\" onfocus=\"document.getElementById('ns_s').checked='1';\">
 <label for=\"ns_s\">$b".mex("$parola_settimane no",$pag)."$b_slash</label></td></tr>
<tr><td>";
if ($numsett_ca == "n") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
if ($numsett_ca == "n") {
$val1_prime_no = $dati_ca[$num_costo]['num_sett_prime'];
$val2_prime_no = $dati_ca[$num_costo]['num_sett_seconde'];
} # fine if ($numsett_ca == "n")
if (!$val1_prime_no) $val1_prime_no = "1";
if (!$val2_prime_no) $val2_prime_no = "1";
echo "<input type=\"radio\" id=\"ns_n\" name=\"numsett_ca\" value=\"n\"$checked>
 <input type=\"text\" name=\"val1_prime_no\" value=\"$val1_prime_no\" size=\"3\" onfocus=\"document.getElementById('ns_n').checked='1';\">
 <label for=\"ns_n\">$b".mex("$parola_settimane no",$pag)." ".mex("e",$pag)."$b_slash</label>
 <input type=\"text\" name=\"val2_prime_no\" value=\"$val2_prime_no\" size=\"3\" onfocus=\"document.getElementById('ns_n').checked='1';\">
 <label for=\"ns_n\">$b".mex("$parola_settimane sì",$pag)."$b_slash</label></td></tr>
<tr><td>";
if ($tipo_periodi == "g") {
if ($numsett_ca == "g") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" id=\"ns_g\" name=\"numsett_ca\" value=\"g\"$checked>
$b".mex("Giorni della settimana selezionati",$pag)."$b_slash:</label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$gio = $dati_ca[$num_costo]['giornisett'];
if (str_replace("1","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"lun_sel\" value=\"s\" onchange=\"asso_rdbx('lun_sel','ns_g');\"$checked>$b".mex("Lunedì",$pag)."$b_slash</label>&nbsp;&nbsp;&nbsp;";
if (str_replace("2","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"mar_sel\" value=\"s\" onchange=\"asso_rdbx('mar_sel','ns_g');\"$checked>$b".mex("Martedì",$pag)."$b_slash</label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if (str_replace("3","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"mer_sel\" value=\"s\" onchange=\"asso_rdbx('mer_sel','ns_g');\"$checked>$b".mex("Mercoledì",$pag)."$b_slash</label>&nbsp;&nbsp;&nbsp;";
if (str_replace("4","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"gio_sel\" value=\"s\" onchange=\"asso_rdbx('gio_sel','ns_g');\"$checked>$b".mex("Giovedì",$pag)."$b_slash</label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if (str_replace("5","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"ven_sel\" value=\"s\" onchange=\"asso_rdbx('ven_sel','ns_g');\"$checked>$b".mex("Venerdì",$pag)."$b_slash</label>&nbsp;&nbsp;&nbsp;";
if (str_replace("6","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"sab_sel\" value=\"s\" onchange=\"asso_rdbx('sab_sel','ns_g');\"$checked>$b".mex("Sabato",$pag)."$b_slash</label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if (str_replace("7","",$gio) != $gio) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"dom_sel\" value=\"s\" onchange=\"asso_rdbx('dom_sel','ns_g');\"$checked>$b".mex("Domenica",$pag)."$b_slash</label></td></tr>";
} # fine if ($tipo_periodi == "g")
echo "</table></td></tr>

<tr><td colspan=\"2\">
<small>10. </small>".mex("Associare a specifi$sillaba_che $parola_settimane della prenotazione",$pag)."?";
echo " <select name=\"associasett\">";
if ($dati_ca[$num_costo]['associasett'] == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>";
if ($dati_ca[$num_costo]['associasett'] == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>
</select></td></tr>

<tr><td style=\"height: 1px;\"></td></tr><tr><td valign=\"top\">
<small>11. </small>".mex("Moltiplicare il costo per",$pag).":<br>
<small>(".mex("il costo viene moltiplicato per<br> zero se la somma è negativa",$pag).")</small>
</td><td><table><tr><td colspan=\"2\">";
$moltiplica_ca = $dati_ca[$num_costo]['moltiplica'];
$agg_moltiplica = $dati_ca[$num_costo]['molt_agg'];
if ($moltiplica_ca == "1") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
$agg_moltiplica_1 = 0;
if ($moltiplica_ca == "1" and $agg_moltiplica) $agg_moltiplica_1 = $agg_moltiplica;
echo "<label><input type=\"radio\" id=\"mo_1\" name=\"moltiplica_ca\" value=\"1\"$checked>
$b".mex("Uno",$pag)."$b_slash, ".mex("aggiungendo",$pag)." </label>
<input type=\"text\" name=\"agg_moltiplica_1\" value=\"$agg_moltiplica_1\" size=\"3\" onclick=\"document.getElementById('mo_1').checked='1';\">
</td></tr><tr><td colspan=\"2\">";
if ($moltiplica_ca == "p") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
$agg_moltiplica_p = 0;
if ($moltiplica_ca == "p" and $agg_moltiplica) $agg_moltiplica_p = $agg_moltiplica;
echo "<label><input type=\"radio\" id=\"mo_p\" name=\"moltiplica_ca\" value=\"p\"$checked>
$b".mex("Numero di persone",$pag)."$b_slash, ".mex("aggiungendo",$pag)." </label>
<input type=\"text\" name=\"agg_moltiplica_p\" value=\"$agg_moltiplica_p\" size=\"3\" onclick=\"document.getElementById('mo_p').checked='1';\">
<small>(".mex("escluse quelle dei costi con letti aggiuntivi",$pag).")</small>
</td></tr><tr><td colspan=\"2\">";
if ($moltiplica_ca == "t") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
$agg_moltiplica_t = 0;
if ($moltiplica_ca == "t" and $agg_moltiplica) $agg_moltiplica_t = $agg_moltiplica;
echo "<label><input type=\"radio\" id=\"mo_t\" name=\"moltiplica_ca\" value=\"t\"$checked>
$b".mex("Numero di persone totale",$pag)."$b_slash, ".mex("aggiungendo",$pag)." </label>
<input type=\"text\" name=\"agg_moltiplica_t\" value=\"$agg_moltiplica_t\" size=\"3\" onclick=\"document.getElementById('mo_t').checked='1';\">
</td></tr><tr><td style=\"width: 10px; white-space: nowrap;\">";
$moltmax = 0;
$meno_moltmax_p = 0;
$meno_moltmax_t = 0;
$checked_tipo_n = " checked";
if ($moltiplica_ca == "c") {
$checked = " checked";
$b = "<b>";
$b_slash = "</b>";
if ($dati_ca[$num_costo]['molt_max'] != "n" and $dati_ca[$num_costo]['molt_max'] != "x") $checked_tipo_n = "";
if ($dati_ca[$num_costo]['molt_max'] == "n") $moltmax = $dati_ca[$num_costo]['molt_max_num'];
if ($dati_ca[$num_costo]['molt_max'] == "p") {
$checked_tipo_p = " checked";
$b_p = "<b>";
$b_slash_p = "</b>";
$meno_moltmax_p = $dati_ca[$num_costo]['molt_max_num'];
} # fine if ($dati_ca[$num_costo]['molt_max'] == "p")
else { $checked_tipo_p = ""; $b_p = ""; $b_slash_p = ""; }
if ($dati_ca[$num_costo]['molt_max'] == "t") {
$checked_tipo_t = " checked";
$b_t = "<b>";
$b_slash_t = "</b>";
$meno_moltmax_t = $dati_ca[$num_costo]['molt_max_num'];
} # fine if ($dati_ca[$num_costo]['molt_max'] == "t")
else { $checked_tipo_t = ""; $b_t = ""; $b_slash_t = ""; }
} # fine if ($moltiplica_ca == "c")
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" id=\"mo_c\" name=\"moltiplica_ca\" value=\"c\"$checked> $b".mex("Chiedere",$pag)."$b_slash. ".mex("Numero massimo",$pag).":</label></td>
<td onclick=\"document.getElementById('mo_c').checked='1';\">
<input type=\"radio\" id=\"mm_n\" name=\"tipo_moltmax\" value=\"n\"$checked_tipo_n>
 <input type=\"text\" name=\"moltmax\" value=\"$moltmax\" size=\"3\" onfocus=\"document.getElementById('mm_n').checked='1';\">
 <label for=\"mm_n\">(".mex("0 se illimitato",$pag).")</label></td></tr>
<tr><td></td><td onclick=\"document.getElementById('mo_c').checked='1';\">
<label><input type=\"radio\" id=\"mm_p\" name=\"tipo_moltmax\" value=\"p\"$checked_tipo_p>
 $b_p".mex("Numero di persone",$pag)."$b_slash_p ".mex("meno",$pag)."</label>
 <input type=\"text\" name=\"meno_moltmax_p\" value=\"$meno_moltmax_p\" size=\"2\" onfocus=\"document.getElementById('mm_p').checked='1';\"></td></tr>
<tr><td></td><td onclick=\"document.getElementById('mo_c').checked='1';\">
<label><input type=\"radio\" id=\"mm_t\" name=\"tipo_moltmax\" value=\"t\"$checked_tipo_t>
 $b_t".mex("Numero di persone totale",$pag)."$b_slash_t ".mex("meno",$pag)."</label>
 <input type=\"text\" name=\"meno_moltmax_t\" value=\"$meno_moltmax_t\" size=\"2\" onfocus=\"document.getElementById('mm_t').checked='1';\"></td></tr>
</table></td></tr>";

unset($opt_beni_inv);
if ($priv_vedi_beni_inv != "n" and (($priv_vedi_inv_mag != "n" and $priv_mod_beni_in_mag != "n") or ($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n"))) {
$beni_inv = esegui_query("select * from $tablebeniinventario $condizione_beni_propri order by idbeniinventario");
$num_beni_inv = numlin_query($beni_inv);
for ($num1 = 0 ; $num1 < $num_beni_inv ; $num1++) {
$idinv = risul_query($beni_inv,$num1,'idbeniinventario');
$nome_bene = risul_query($beni_inv,$num1,'nome_bene');
$codice_bene = risul_query($beni_inv,$num1,'codice_bene');
$opt_beni_inv .= "<option value=\"$idinv\">$nome_bene";
if ($codice_bene) $opt_beni_inv .= " ($codice_bene)";
$opt_beni_inv .= "</option>";
} # fine for $num1
} # fine if ($priv_vedi_beni_inv != "n" and...
unset($opt_mag);
if ($priv_vedi_inv_mag != "n" and $priv_mod_beni_in_mag != "n") {
$magazzini = esegui_query("select * from $tablemagazzini $condizione_mag_propri order by idmagazzini");
$num_mag = numlin_query($magazzini);
for ($num1 = 0 ; $num1 < $num_mag ; $num1++) {
$idmag = risul_query($magazzini,$num1,"idmagazzini");
$nome_mag = risul_query($magazzini,$num1,'nome_magazzino');
$opt_mag .= "<option value=\"$idmag\">$nome_mag</option>";
} # fine for $num1
} # fine if ($priv_vedi_inv_mag != "n" and $priv_mod_beni_in_mag != "n")
if ($opt_beni_inv and (($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n") or $opt_mag)) {
echo "<tr><td style=\"height: 1px;\"></td></tr><tr><td valign=\"top\">
<small>12. </small>".mex("Beni dell'inventario da<br> eliminare quando si<br> inserisce il costo",$pag).":</td><td>
<table id=\"tab_beni_inv\"><tr><td>";
if ($dati_ca[$num_costo]['num_beniinv']) $beni_inv_elimina = "sel";
else $beni_inv_elimina = "nessuno";
if ($beni_inv_elimina == "nessuno") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" name=\"beni_inv_elimina\" value=\"nessuno\"$checked> $b".mex("Nessuno",$pag)."$b_slash</label></td></tr>
<tr><td>";
if ($beni_inv_elimina == "sel") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
if (!$num_beni_inv_elimina_sel) $num_beni_inv_elimina_sel = $dati_ca[$num_costo]['num_beniinv'];
if (!$num_beni_inv_elimina_sel) $num_beni_inv_elimina_sel = 1;
if ($aggiungi_bene_inv_elimina) $num_beni_inv_elimina_sel++;
if ($elimina_bene_inv_elimina) $num_beni_inv_elimina_sel--;
echo "<label><input type=\"radio\" id=\"bi_s\" name=\"beni_inv_elimina\" value=\"sel\"$checked> $b".mex("Beni selezionati",$pag)."$b_slash:</label></td>
<td onclick=\"document.getElementById('bi_s').checked='1'\">
<script type=\"text/javascript\">
<!--
var numcaselle_inv = $num_beni_inv_elimina_sel;
function agg_lin_bene_inv () {
if (numcaselle_inv < $numcaselle_max) {
var tab_beni_inv = document.getElementById('tab_beni_inv');
var minus_prec = document.getElementById('minus'+numcaselle_inv);
if (numcaselle_inv > 1) tab_beni_inv.deleteRow(-1);
numcaselle_inv++;
var nlinea = tab_beni_inv.insertRow(-1);
var cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella.innerHTML = '<select name=\"bene_inv_sel'+numcaselle_inv+'\">\
<option value=\"\">----<\/option>\
".str_replace("'","\\'",str_replace("/","\\/",str_replace("\n","\\\n",str_replace("\\","\\\\",$opt_beni_inv))))."\
<\/select> x <input type=\"text\" name=\"molt_bene_inv_sel'+numcaselle_inv+'\" value=\"1\" size=\"4\">';
cella = nlinea.insertCell(2);
cella.id = 'minus'+numcaselle_inv;
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"elimina_bene_inv_elimina\" value=\"".str_replace("'","\\'",mex("Elimina un bene",$pag))."\" onclick=\"elim_lin_bene_inv();\">';
minus_prec.innerHTML = '';
nlinea = tab_beni_inv.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella.id = 'minus'+(numcaselle_inv + 1);
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_bene_inv_elimina\" value=\"".str_replace("'","\\'",mex("Aggiungi un bene",$pag))."\" onclick=\"agg_lin_bene_inv();\">';
document.getElementById('numcaselle_inv').value = numcaselle_inv;
}
return false;
} // fine function agg_lin_bene_inv
function elim_lin_bene_inv () {
if (numcaselle_inv > 1) {
var tab_beni_inv = document.getElementById('tab_beni_inv');
var minus_post = document.getElementById('minus'+(numcaselle_inv+1));
var minus_corr = document.getElementById('minus'+numcaselle_inv);
numcaselle_inv--;
var minus_prec = document.getElementById('minus'+numcaselle_inv);
if (numcaselle_inv > 1) {
minus_prec.innerHTML = minus_corr.innerHTML;
var minus_html = minus_post.innerHTML;
tab_beni_inv.deleteRow(-1);
tab_beni_inv.deleteRow(-1);
var nlinea = tab_beni_inv.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella.id = 'minus'+(numcaselle_inv + 1);
cella.innerHTML = minus_html;
}
else {
minus_prec.innerHTML = minus_post.innerHTML;
tab_beni_inv.deleteRow(-1);
tab_beni_inv.deleteRow(-1);
}
document.getElementById('numcaselle_inv').value = numcaselle_inv;
}
return false;
} // fine function elim_lin_bene_inv
-->
</script>";
for ($num1 = 1 ; $num1 <= $num_beni_inv_elimina_sel ; $num1++) {
if ($num1 > 1) echo "<tr><td></td><td onclick=\"document.getElementById('bi_s').checked='1'\">";
if (!${"molt_bene_inv_sel".$num1}) ${"molt_bene_inv_sel".$num1} = $dati_ca[$num_costo]['molt_beneinv'][($num1 - 1)];
if (!${"molt_bene_inv_sel".$num1}) ${"molt_bene_inv_sel".$num1} = 1;
if (!${"bene_inv_sel".$num1}) ${"bene_inv_sel".$num1} = $dati_ca[$num_costo]['id_beneinv'][($num1 - 1)];
echo "<select name=\"bene_inv_sel$num1\">
<option value=\"\">----</option>".str_replace("\"".${"bene_inv_sel".$num1}."\">","\"".${"bene_inv_sel".$num1}."\" selected>",$opt_beni_inv)."
</select> x <input type=\"text\" name=\"molt_bene_inv_sel$num1\" value=\"".${"molt_bene_inv_sel".$num1}."\" size=\"4\">";
if ($num1 == $num_beni_inv_elimina_sel) {
$id_minus = " id=\"minus".($num1 + 1)."\"";
if ($num1 > 1) echo "</td><td id=\"minus$num1\"><input class=\"sbutton\" type=\"submit\" name=\"elimina_bene_inv_elimina\" value=\"".mex("Elimina un bene",$pag)."\" onclick=\"elim_lin_bene_inv();\"></td></tr><tr><td></td><td>";
else $id_minus = " id=\"minus$num1\"";
echo "</td><td$id_minus><input class=\"sbutton\" type=\"submit\" name=\"aggiungi_bene_inv_elimina\" value=\"".mex("Aggiungi un bene",$pag)."\" onclick=\"agg_lin_bene_inv();\">";
} # fine if ($num1 == $num_beni_inv_elimina_sel)
else echo "</td><td id=\"minus$num1\">";
echo "</td></tr>";
} # fine for $num1
echo "</table>
<input type=\"hidden\" id=\"numcaselle_inv\" name=\"num_beni_inv_elimina_sel\" value=\"$num_beni_inv_elimina_sel\">
<table>";
if ($dati_ca[$num_costo]['tipo_beniinv'] == "app") $tipo_bie = "a";
if ($dati_ca[$num_costo]['tipo_beniinv'] == "mag") $tipo_bie = "m";
if ($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n") {
if ($tipo_bie == "a") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
if (!$tipo_bie) $checked = " checked";
echo "<tr><td style=\"width: 50px;\"></td><td><label>
<input type=\"radio\" name=\"tipo_bie\" value=\"a\"$checked> $b".mex("elimina i beni dall'inventario dell'appartamento occupato dalla prenotazione",'unit.php')."$b_slash</label></td></tr>";
} # fine if ($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n")
if ($opt_mag) {
$mag_bie_sel = $dati_ca[$num_costo]['mag_beniinv'];
if ($tipo_bie == "m") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td style=\"width: 50px;\"></td><td><label>
<input type=\"radio\" id=\"tbi_m\" name=\"tipo_bie\" value=\"m\"$checked> $b".mex("elimina i beni dall'inventario del magazzino",$pag)."$b_slash
</label><select name=\"mag_bie_sel\" onfocus=\"document.getElementById('tbi_m').checked='1'\">
<option value=\"\">----</option>".str_replace("\"$mag_bie_sel\">","\"$mag_bie_sel\" selected>",$opt_mag)."
</select></td></tr>";
} # fine if ($opt_mag)
echo "</table></td></tr>";
} # fine if ($opt_beni_inv and (($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n") or $opt_mag))

echo "<tr><td style=\"height: 1px;\"></td></tr><tr><td valign=\"top\">
<small>13. </small>".mex("Periodi in cui è permesso<br> inserire il costo",$pag).":</td><td>
<table id=\"tab_per_perm\"><tr><td>";
$periodi_permessi = $dati_ca[$num_costo]['periodipermessi'];
if (!$periodi_permessi) {
$num_periodi_permessi = 0;
$checked = " checked";
$b = "<b>";
$b_slash = "</b>";
} # fine if (!$periodi_permessi)
else {
$num_periodi_permessi = count($dati_ca[$num_costo]['sett_periodipermessi_ini']);
$checked = "";
$b = "";
$b_slash = "";
} # fine else if (!$periodi_permessi)
echo "<label><input type=\"radio\" name=\"periodi_permessi\" value=\"tutti\"$checked> $b".mex("In tutti",$pag)."$b_slash</label></td></tr>
<tr><td style=\"white-space: nowrap;\">";
if ($periodi_permessi) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" id=\"pp_s\" name=\"periodi_permessi\" value=\"sel\"$checked> $b".mex("Solo nei periodi selezionati",$pag)."$b_slash:</label></td>
<td onclick=\"document.getElementById('pp_s').checked='1'\">";
if (!$num_periodi_permessi_sel) $num_periodi_permessi_sel = $num_periodi_permessi;
if (!$num_periodi_permessi_sel) $num_periodi_permessi_sel = 1;
if ($aggiungi_periodo_permesso) $num_periodi_permessi_sel++;
if ($elimina_periodo_permesso) $num_periodi_permessi_sel--;
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
if ($num1 > 1) echo "<tr><td></td><td>";
if (!${"pp_dal".$num1} and $num1 <= $num_periodi_permessi) {
${"pp_dal".$num1} = esegui_query("select datainizio from $tableperiodi where idperiodi = '".$dati_ca[$num_costo]['sett_periodipermessi_ini'][($num1 - 1)]."'");
if (numlin_query(${"pp_dal".$num1}) == 1) ${"pp_dal".$num1} = risul_query(${"pp_dal".$num1},0,'datainizio');
} # fine if (!${"pp_dal".$num1} and...
echo mex("dal",$pag)." ";
$pp_dal = ${"pp_dal".$num1};
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.1.php","pp_dal$num1",$pp_dal,"","",$id_utente,$tema);
if (!${"pp_al".$num1} and $num1 <= $num_periodi_permessi) {
${"pp_al".$num1} = esegui_query("select datafine from $tableperiodi where idperiodi = '".$dati_ca[$num_costo]['sett_periodipermessi_fine'][($num1 - 1)]."'");
if (numlin_query(${"pp_al".$num1}) == 1) ${"pp_al".$num1} = risul_query(${"pp_al".$num1},0,'datafine');
} # fine if (!${"pp_al".$num1} and...
echo mex("al",$pag)." ";
$pp_al = ${"pp_al".$num1};
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.1.php","pp_al$num1",$pp_al,"","",$id_utente,$tema);
if ($num1 == $num_periodi_permessi_sel) {
$id_minus = " id=\"minus_pp".($num1 + 1)."\"";
if ($num1 > 1) echo "</td><td id=\"minus_pp$num1\"><input class=\"sbutton\" type=\"submit\" name=\"elimina_periodo_permesso\" value=\"".mex("Elimina un periodo",$pag)."\" onclick=\"elim_lin_per_perm();\"></td></tr><tr><td></td><td>";
else $id_minus = " id=\"minus_pp$num1\"";
echo "</td><td$id_minus><input class=\"sbutton\" type=\"submit\" name=\"aggiungi_periodo_permesso\" value=\"".mex("Aggiungi un periodo",$pag)."\" onclick=\"agg_lin_per_perm();\">";
} # fine if ($num1 == $num_periodi_permessi_sel)
else echo "</td><td id=\"minus_pp$num1\">";
echo "</td></tr>";
} # fine for $num1
echo "</table>
<script type=\"text/javascript\">
<!--
var numcaselle = $num_periodi_permessi_sel;
function agg_lin_per_perm () {
if (numcaselle < $numcaselle_max) {
var tab_per_perm = document.getElementById('tab_per_perm');
var minus_prec = document.getElementById('minus_pp'+numcaselle);
if (numcaselle > 1) tab_per_perm.deleteRow(-1);
numcaselle++;
var nlinea = tab_per_perm.insertRow(-1);
var cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
var cell_html = '".str_replace("'","\\'",mex("dal",$pag))." ';
var pp_dal = 'pp_dal'+numcaselle;
";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","pp_dal","","","",$id_utente,$tema,"","","cell_html");
echo "
cell_html += ' ".mex("al",$pag)." ';
var pp_al = 'pp_al'+numcaselle;
";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","pp_al","","","",$id_utente,$tema,"","","cell_html");
echo "
cella.innerHTML = cell_html;
cella = nlinea.insertCell(2);
cella.id = 'minus_pp'+numcaselle;
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"elimina_periodo_permesso\" value=\"".str_replace("'","\\'",mex("Elimina un periodo",$pag))."\" onclick=\"elim_lin_per_perm();\">';
minus_prec.innerHTML = '';
nlinea = tab_per_perm.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella.id = 'minus_pp'+(numcaselle + 1);
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_periodo_permesso\" value=\"".str_replace("'","\\'",mex("Aggiungi un periodo",$pag))."\" onclick=\"agg_lin_per_perm();\">';
document.getElementById('numcaselle').value = numcaselle;
}
return false;
} // fine function agg_lin_per_perm
function elim_lin_per_perm () {
if (numcaselle > 1) {
var tab_per_perm = document.getElementById('tab_per_perm');
var minus_post = document.getElementById('minus_pp'+(numcaselle+1));
var minus_corr = document.getElementById('minus_pp'+numcaselle);
numcaselle--;
var minus_prec = document.getElementById('minus_pp'+numcaselle);
if (numcaselle > 1) {
minus_prec.innerHTML = minus_corr.innerHTML;
var minus_html = minus_post.innerHTML;
tab_per_perm.deleteRow(-1);
tab_per_perm.deleteRow(-1);
var nlinea = tab_per_perm.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella.id = 'minus_pp'+(numcaselle + 1);
cella.innerHTML = minus_html;
}
else {
minus_prec.innerHTML = minus_post.innerHTML;
tab_per_perm.deleteRow(-1);
tab_per_perm.deleteRow(-1);
}
document.getElementById('numcaselle').value = numcaselle;
}
return false;
} // fine function elim_lin_per_perm
-->
</script>
<input type=\"hidden\" id=\"numcaselle\" name=\"num_periodi_permessi_sel\" value=\"$num_periodi_permessi_sel\">
<table><tr><td style=\"width: 50px;\"></td><td>";
if ($periodi_permessi == "t") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
if (!$periodi_permessi) $checked = " checked";
echo "<label><input type=\"radio\" name=\"tipo_pp\" value=\"t\"$checked> $b".mex("se tutt$lettera_e $parola_le $parola_settimane della prenotazione sono all'interno dei periodi selezionati",$pag)."$b_slash</label></td></tr>
<tr><td></td><td>";
if ($periodi_permessi == "u") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" name=\"tipo_pp\" value=\"u\"$checked> $b".mex("se anche un$lettera_a2 sol$lettera_a $parola_settimana della prenotazione è all'interno dei periodi selezionati",$pag)."$b_slash</label></td></tr>";
echo "<tr><td></td><td>";
if ($periodi_permessi == "p") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" name=\"tipo_pp\" value=\"p\"$checked> $b".mex("applicare il costo solo in $parola_settimane permess$lettera_e all'interno della prenotazione",$pag)."$b_slash</label></td></tr>
</table></td></tr>";

if ($num_appartamenti) {
echo "<tr><td style=\"height: 1px;\"></td></tr><tr><td valign=\"top\">
<small>14. </small>".mex("Appartamenti incompatibili",'unit.php').":</td><td>
<table><tr><td>";
$num_col = 1;
$app_incomp = ",".$dati_ca[$num_costo]['appincompatibili'].",";
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$idappartamenti = risul_query($appartamenti,$num1,'idappartamenti');
if ($priv_vedi_tab_appartamenti == "s" or $appartamenti_consentiti[$idappartamenti] != "NO") {
if (str_replace(",$idappartamenti,","",$app_incomp) != $app_incomp) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"checkbox\" name=\"app_incomp_$num1\" value=\"i\"$checked>$b$idappartamenti$b_slash</label>
<input type=\"hidden\" name=\"idapp_incomp_$num1\" value=\"$idappartamenti\">";
if ($num_col == 4) {
echo "</td></tr><tr><td>";
$num_col = 0;
} # fine if ($num_col == 4)
else echo "<td style=\"width: 30px;\"></td><td>";
$num_col++;
} # fine if ($priv_vedi_tab_appartamenti == "s" or $appartamenti_consentiti[$idappartamenti] != "NO")
} # fine for $num1
echo "</td></tr></table></td></tr>";
} # fine if ($num_appartamenti)

echo "<tr><td style=\"height: 1px;\"></td></tr><tr><td colspan=\"2\">
<small>15. </small>".mex("Mostrare nella pagina di inserimento delle prenotazioni",$pag)."?
 <select name=\"mostra_ca\">";
if ($dati_ca[$num_costo]['mostra'] == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>";
if ($dati_ca[$num_costo]['mostra'] == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>
</select><br>
<small>16. </small>".mex("Mostrare raggruppato con costi simili della stessa categoria in inserimento",$pag)."?
 <select name=\"raggruppa_ca\">";
if ($dati_ca[$num_costo]['raggruppa'] == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>";
if ($dati_ca[$num_costo]['raggruppa'] == "n") $selected = " selected";
else $selected = "";
if ($dati_ca[$num_costo]['combina'] == "s") $checked = " checked";
else $checked = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>
</select><br>
<table cellspacing=0 cellpadding=0><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"combina_ca\" value=\"s\"$checked>
".mex("Combina con altri costi",$pag)."</label>
 <small>(".mex("i costi combinabili vengono mostrati tutti assieme con il nome della categoria ed ognuno è inserito se possibile",$pag).")</small>
</td></tr></table><br>
<small>17. </small>".mex("Considerare il costo come letto/i aggiuntivo/i",$pag)."?
 <select name=\"letto_ca\">";
if ($dati_ca[$num_costo]['letto'] == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>";
if ($dati_ca[$num_costo]['letto'] == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>
</select><br><br>";
echo "<small>18. </small>".mex("Escludere questo costo dal totale per altri costi percentuali",'creaprezzi.php')."?
 <select name=\"escludi_da_tot\">";
if ($dati_ca[$num_costo]['escludi_tot_perc'] == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>";
if ($dati_ca[$num_costo]['escludi_tot_perc'] == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>
</select><br><br>";
echo "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\">
<small>19. </small>".mex("Limitarne il numero che è possibile avere contemporaneamente in uno stesso periodo",$pag)."?</td>
<td style=\"width: 130px;\">";
if (!$dati_ca[$num_costo]['numlimite']) $numlimite_ca = "1";
else $numlimite_ca = $dati_ca[$num_costo]['numlimite'];
if (!$dati_ca[$num_costo]['numlimite']) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" name=\"limite_ca\" value=\"n\"$checked> $b".mex("No",$pag)."$b_slash</label><br>";
if ($dati_ca[$num_costo]['numlimite']) { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<label><input type=\"radio\" id=\"li_s\" name=\"limite_ca\" value=\"s\"$checked> $b".mex("Si",$pag)."$b_slash:</label>
<input type=\"text\" name=\"numlimite_ca\" value=\"$numlimite_ca\" size=\"4\" onclick=\"document.getElementById('li_s').checked='1';\">
</td></tr></table></td></tr>";

echo "<tr><td style=\"height: 1px;\"></td></tr><tr><td valign=\"top\">
<small>20. </small>".mex("Caratteristiche del costo<br> da mantenere quando si<br> modifica una prenotazione",$pag).":</td><td>
<table>";
if ($dati_ca[$num_costo]['var_percentuale'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_percentuale\" value=\"s\"$checked>
$b".mex("Valore percentuale",$pag)."$b_slash (".mex("e settimane associate",$pag).")</label></td></tr>";
if ($dati_ca[$num_costo]['var_numsett'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_numsett\" value=\"s\"$checked>
$b".mex("Assegnazione $parola_settimane",$pag)."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_moltiplica'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_moltiplica\" value=\"s\"$checked>
$b".mex("Numero per cui viene moltiplicato",$pag)."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_beniinv'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_beniinv\" value=\"s\"$checked>
$b".mex("Beni dell'inventario da eliminare",$pag)."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_periodip'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_periodip\" value=\"s\"$checked>
$b".mex("Periodi permessi",$pag)."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_tariffea'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_tariffea\" value=\"s\"$checked>
$b".mex("Associazione alle tariffe",$pag)."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_tariffei'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_tariffei\" value=\"s\"$checked>
$b".mex("Tariffe incompatibili",$pag)."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_appi'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_appi\" value=\"s\"$checked>
$b".mex("Appartamenti incompatibili",'unit.php')."$b_slash</label></td></tr>";
if ($dati_ca[$num_costo]['var_comb'] == "s") { $checked = " checked"; $b = "<b>"; $b_slash = "</b>"; }
else { $checked = ""; $b = ""; $b_slash = ""; }
echo "<tr><td><label><input type=\"checkbox\" name=\"mantenere_comb\" value=\"s\"$checked>
$b".mex("Costi combinati",$pag)."$b_slash</label></td></tr>";
echo "</table></td></tr>

</table>";

if ($origine) $action = $origine;
else $action = "visualizza_tabelle.php#tab_costi_agg";
echo "<br><div style=\"text-align: center;\">
<button class=\"exco\" id=\"modi\" type=\"submit\" name=\"modifica_costo\" value=\"".mex("Modifica il costo",$pag)."\"><div>".mex("Modifica il costo",$pag)."</div></button>
</div></div></form><br>
<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$action\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<button class=\"gobk\" id=\"indi\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form></div>
<table><tr><td style=\"height: 20px;\"></td></tr></table>";

} # fine if ($mostra_form_iniziale != "NO")


} # fine if ($num_costo != "")
else {
echo mex("Il costo è stato cancellato",$pag).".<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php#tab_costi_agg\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form></div>
<table><tr><td style=\"height: 20px;\"></td></tr></table>";
} # fine else if ($dati_ca['id'][$idntariffe] != "")



if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");



} # fine if ($anno_utente_attivato == "SI" and $priv_mod_costo_agg != "n")
} # fine if ($id_utente)



?>
