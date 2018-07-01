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


function crea_quadro_disp ($id_data_inizio_tab_disp,$num_colonne_tab_disp,$mostra_quadro_disp,$mostra_num_liberi,$app_consentito,$app_consentito_sett,$app_regola2_orig,$tipo_periodi,$numero_tariffe,$nome_tariffa,$dati_app,$prenota_in_app_sett,$app_orig_prenota_id,$tableperiodi,$allinea_disp="",$dati_tariffe="",$return_var=0) {
global $c_sfondo_tab_disp,$c_inisett_tab_disp,$c_libero_tab_disp,$c_occupato_tab_disp,$aper_font_tab_disp,$chiu_font_tab_disp,$fr_persone,$fr_persona,$nome_mese,$colonna_destra_tab_disp,$tablepersonalizza,$id_utente,$anno;

if ($tipo_periodi == "s") $colspan = 14;
else $colspan = 2;
$num_raggr = 0;
unset($nome_raggr);
unset($app_ric_raggr);
unset($tipotariffa);
$napp_ric_raggr = array();

if ($mostra_quadro_disp == "app") {
$mostra_num_liberi = "NO";
ksort ($dati_app['maxocc']);
reset ($dati_app['maxocc']);
foreach ($dati_app['maxocc'] as $key => $val) {
if ($app_consentito[$key] == "SI") {
$app_ric_raggr[$num_raggr] = $key;
$nome_raggr[$num_raggr] = $key;
$num_raggr++;
} # fine if ($app_consentito[$numapp] == "SI")
} # fine foreach ($dati_app['maxocc'] as $key => $val)
} # fine if ($mostra_quadro_disp == "app")

if ($mostra_quadro_disp == "reg2") {
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($app_regola2_orig[$numtariffa]) {
$app_regola2 = explode(",",$app_regola2_orig[$numtariffa]);
for ($num1 = 0 ; $num1 < count($app_regola2) ; $num1++) if ($app_consentito[$app_regola2[$num1]] == "SI") $app_ric_raggr[$num_raggr] .= "$app_regola2[$num1],";
if ($app_ric_raggr[$num_raggr]) {
$app_ric_raggr[$num_raggr] = substr($app_ric_raggr[$num_raggr],0,-1);
$nome_raggr[$num_raggr] = str_replace(" ","&nbsp;",$nome_tariffa[$numtariffa]);
$tipotariffa[$num_raggr] = "tariffa$numtariffa";
$napp_ric_raggr[$num_raggr] = $app_regola2_orig['napp'][$numtariffa];
$num_raggr++;
} # fine if ($app_ric_raggr[$num_raggr])
} # fine if ($app_regola2_orig[$numtariffa])
} # fine for $numtariffa
} # fine if ($mostra_quadro_disp == "reg2")

if ($mostra_quadro_disp == "pers") {
asort ($dati_app['maxocc']);
reset ($dati_app['maxocc']);
$ultime_persone_casa = "vuoto";
foreach ($dati_app['maxocc'] as $key => $val) {
$persone_casa = $val;
if ($persone_casa != $ultime_persone_casa) {
if ($app_ric_raggr[$num_raggr]) {
$app_ric_raggr[$num_raggr] = substr($app_ric_raggr[$num_raggr],0,-1);
if ($ultime_persone_casa) {
if ($ultime_persone_casa == 1) $nome_raggr[$num_raggr] = $ultime_persone_casa."&nbsp;".$fr_persona;
else $nome_raggr[$num_raggr] = $ultime_persone_casa."&nbsp;".$fr_persone;
} # fine if ($ultime_persone_casa)
else $nome_raggr[$num_raggr] = "?&nbsp;$fr_persone";
$num_raggr++;
} # fine if ($app_ric_raggr[$num_raggr])
$ultime_persone_casa = $persone_casa;
} # fine if ($persone_casa != $ultimepersone_casa)
if ($app_consentito[$key] == "SI") $app_ric_raggr[$num_raggr] .= "$key,";
} # fine foreach ($dati_app['maxocc'] as $key => $val)
if ($app_ric_raggr[$num_raggr]) {
$app_ric_raggr[$num_raggr] = substr($app_ric_raggr[$num_raggr],0,-1);
if ($ultime_persone_casa) {
if ($ultime_persone_casa == 1) $nome_raggr[$num_raggr] = $ultime_persone_casa."&nbsp;".$fr_persona;
else $nome_raggr[$num_raggr] = $ultime_persone_casa."&nbsp;".$fr_persone;
} # fine if ($ultime_persone_casa)
else $nome_raggr[$num_raggr] = "?&nbsp;$fr_persone";
$num_raggr++;
} # fine if ($app_ric_raggr[$num_raggr])
} # fine if ($mostra_quadro_disp == "pers")

$righe_tab_disp = "";
for ($num1 = 0 ; $num1 < $num_raggr ; $num1++) {
if ($app_ric_raggr[$num1]) {
$righe_tab_disp .= "<tr><td style=\"text-align: right;\">$aper_font_tab_disp".$nome_raggr[$num1]."$chiu_font_tab_disp</td>";
$max_app_liberi = explode(",",$app_ric_raggr[$num1]);
$max_app_liberi = count($max_app_liberi);
$app_ric_colonna = ",".$app_ric_raggr[$num1].",";
for ($num2 = 0 ; $num2 < $num_colonne_tab_disp ; $num2++) {
$id_periodo = $id_data_inizio_tab_disp + $num2;
$num_app_liberi = $max_app_liberi;
if ($app_consentito_sett[',attivo,'] == "SI") {
if ($num2 == 0) {
$app_ric_col_vett = explode(",",$app_ric_raggr[$num1]);
$num_app_ric_col = count($app_ric_col_vett);
} # fine if ($num2 == 0)
$app_ric_colonna = ",".$app_ric_raggr[$num1].",";
for ($num3 = 0 ; $num3 < $num_app_ric_col ; $num3++) {
if ($app_consentito_sett[$app_ric_col_vett[$num3]][$id_periodo] != "SI") {
$app_ric_colonna = str_replace(",".$app_ric_col_vett[$num3].",",",",$app_ric_colonna);
$num_app_liberi--;
} # fine if ($app_consentito_sett[$app_ric_col_vett[$num3]][$id_periodo] != "SI")
} # fine for $num3
} # fine if ($app_consentito_sett[',attivo,'] == "SI")
$pren_pres_in_lista = array();
$lista_prenota_periodo = array();
$num_lista_pren_per = 0;
lista_prenota_periodo($id_periodo,$id_periodo,$dati_app,$prenota_in_app_sett,$pren_pres_in_lista,$lista_prenota_periodo,$num_lista_pren_per);
for ($num3 = 0 ; $num3 < $num_lista_pren_per ; $num3++) {
if (str_replace(",".$app_orig_prenota_id[$lista_prenota_periodo[$num3]].",","",$app_ric_colonna) != $app_ric_colonna) $num_app_liberi--;
} # fine for $num3
if ($napp_ric_raggr[$num1]) $num_app_liberi = floor($num_app_liberi / $napp_ric_raggr[$num1]);
if ($mostra_quadro_disp == "reg2" and $dati_tariffe and $dati_tariffe[$tipotariffa[$num1]]['chiusa'][$id_periodo]) $num_app_liberi = 0;
if ($num_app_liberi > 0) $color = $c_libero_tab_disp;
else $color = $c_occupato_tab_disp;
if ($num_app_liberi > 0 and $mostra_num_liberi == "SI") $val_liberi = $num_app_liberi;
else $val_liberi = "&nbsp;";
if ($num2 == 0 and $allinea_disp == "SI") $colspan_v = $colspan / 2;
else $colspan_v = $colspan;
$righe_tab_disp .= "<td style=\"background-color: $color;\" colspan=\"$colspan_v\">$aper_font_tab_disp$val_liberi$chiu_font_tab_disp</td>";
if ($return_var) $tab_liberi[$nome_raggr[$num1]][$id_periodo] = $num_app_liberi;
} # fine for $num2

if ($colonna_destra_tab_disp != "NO") $righe_tab_disp .= "<td>$aper_font_tab_disp".$nome_raggr[$num1]."$chiu_font_tab_disp</td></tr>";
else $righe_tab_disp .= "</tr>";
} # fine if ($app_ric_raggr[$num1])
} # fine for $num1

if ($return_var) return $tab_liberi;


if ($righe_tab_disp) {

if ($tipo_periodi == "s") $ripeti_giorni = 7;
else {
$ripeti_giorni = 1;
if (!$id_utente) $id_utente_gio = 1;
else $id_utente_gio = $id_utente;
$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$anno' and idutente = '$id_utente_gio'");
if (numlin_query($giorno_vedi_ini_sett) == 1) $giorno_vedi_ini_sett = risul_query($giorno_vedi_ini_sett,0,'valpersonalizza_num');
else $giorno_vedi_ini_sett = 0;
} # fine else if ($tipo_periodi == "s")
$ultimo_mese = "";
$num_col_mese = 0;
$riga_mese = "<tr><td rowspan=\"2\">$aper_font_tab_disp&nbsp;$chiu_font_tab_disp</td>";
$riga_giorni = "<tr>";
for ($num1 = 0 ; $num1 < ($num_colonne_tab_disp + 1) ; $num1++) {
if ($allinea_disp != "SI" or $num1 != $num_colonne_tab_disp) {
if ($num1 != $num_colonne_tab_disp) $id_periodo = $id_data_inizio_tab_disp + $num1;
else $id_periodo = $id_data_inizio_tab_disp + $num1 - 1;
$riga_periodo = esegui_query("select * from $tableperiodi where idperiodi = '$id_periodo'");
if ($num1 != $num_colonne_tab_disp) $inizio_periodo = risul_query($riga_periodo,0,'datainizio');
else $inizio_periodo = risul_query($riga_periodo,0,'datafine');
$inizio_periodo = explode("-",$inizio_periodo);
$g_inizio_periodo = $inizio_periodo[2];
$m_inizio_periodo = $inizio_periodo[1];
$a_inizio_periodo = $inizio_periodo[0];
if ($num1 == $num_colonne_tab_disp) $ripeti_giorni = 1;
for ($num2 = 0 ; $num2 < $ripeti_giorni ; $num2++) {
$timestamp_periodo = mktime(0,0,0,$m_inizio_periodo,($g_inizio_periodo + $num2),$a_inizio_periodo);
$g_mostra = date("d",$timestamp_periodo);
$m_mostra = date("m",$timestamp_periodo);
if ($ultimo_mese != $m_mostra) {
if ($ultimo_mese) {
$a_mostra = date("Y",mktime(0,0,0,$m_inizio_periodo,($g_inizio_periodo + $num2 -1),$a_inizio_periodo));
$riga_mese .= "<td colspan=\"$num_col_mese\">$aper_font_tab_disp";
if ($num_col_mese > 7) $riga_mese .= $nome_mese[$ultimo_mese]."&nbsp;$a_mostra";
else $riga_mese .= "&nbsp;";
$riga_mese .= "$chiu_font_tab_disp</td>";
$num_col_mese = 0;
} # fine if ($ultimo_mese)
$ultimo_mese = $m_mostra;
} # fine if ($ultimo_mese != $m_mostra)
if (($num1 != 0 or $num2 != 0) and $num1 != $num_colonne_tab_disp) $num_col_mese = $num_col_mese + 2;
else $num_col_mese++;
$bgcolor = "";
if ($tipo_periodi != "s") {
$giorno_sett_corr = date("w",$timestamp_periodo);
if ($giorno_sett_corr == $giorno_vedi_ini_sett) $bgcolor = "$c_inisett_tab_disp";
} # fine ($tipo_periodi != "s")
else if ($num2 == 0) $bgcolor = "$c_inisett_tab_disp";
$riga_giorni .= "<td";
if ($bgcolor) $riga_giorni .= " style=\"background-color: $bgcolor;\"";
if (($num1 != 0 or $num2 != 0) and $num1 != $num_colonne_tab_disp) $riga_giorni .= " colspan=2";
$riga_giorni .= ">$aper_font_tab_disp$g_mostra$chiu_font_tab_disp</td>";
} # fine for $num2
} # fine if ($allinea_disp != "SI" or $num1 != $num_colonne_tab_disp)
} # fine for $num1
$a_mostra = date("Y",mktime(0,0,0,$m_inizio_periodo,($g_inizio_periodo + $num2 -1),$a_inizio_periodo));
$riga_mese .= "<td colspan=\"$num_col_mese\">$aper_font_tab_disp";
if ($num_col_mese > 6) $riga_mese .= $nome_mese[$ultimo_mese]."&nbsp;$a_mostra";
else $riga_mese .= "&nbsp;";
if ($colonna_destra_tab_disp != "NO") $riga_mese .= "$chiu_font_tab_disp</td><td rowspan=\"2\">$aper_font_tab_disp&nbsp;$chiu_font_tab_disp</td></tr>";
else $riga_mese .= "$chiu_font_tab_disp</td></tr>";
$riga_giorni .= "</tr>";

$righe_tab_disp = "$riga_mese
$riga_giorni
$righe_tab_disp";

} # fine if ($righe_tab_disp)

return $righe_tab_disp;

} # fine function crea_quadro_disp




function trova_app_consentiti_per_tab_disp (&$app_consentito,&$app_consentito_sett,&$quadro_non_preciso,$dati_app,$dati_tariffe,$id_data_inizio_tab_disp,$num_colonne_tab_disp,$dati_r2,$attiva_regole1_consentite,$fuori_da_regole1,$regole1_consentite,$condizioni_regole1_consentite,$tariffe_mostra,$attiva_tariffe_consentite,$tariffe_consentite_vett,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$tableregole) {

$app_consentito = array();
$app_consentito_sett = array();

# Calcolo gli appartamenti consentiti dalla regola 1
if ($attiva_regole1_consentite == "s") {
$app_consentito_sett[',attivo,'] = "SI";
if ($fuori_da_regole1) {
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$app_consentito[$dati_app['posizione'][$num1]] = "SI";
for ($num2 = $id_data_inizio_tab_disp ; $num2 <= ($id_data_inizio_tab_disp + $num_colonne_tab_disp - 1) ; $num2++) {
$app_consentito_sett[$dati_app['posizione'][$num1]][$num2] = "SI";
} # fine for $num2
} # fine for $num1
} # fine if ($fuori_da_regole1)
$quadro_non_preciso = "SI";
if (!$regole1_consentite) $regole1_consentite = esegui_query("select idregole,app_agenzia,iddatainizio,iddatafine from $tableregole where app_agenzia != '' and $condizioni_regole1_consentite");
$num_regole1_consentite = numlin_query($regole1_consentite);
for ($num1 = 0 ; $num1 < $num_regole1_consentite ; $num1++) {
$idapp = risul_query($regole1_consentite,$num1,'app_agenzia');
if (!$fuori_da_regole1) $app_consentito[$idapp] = "SI";
$iddatainizio_reg1 = risul_query($regole1_consentite,$num1,'iddatainizio');
$iddatafine_reg1 = risul_query($regole1_consentite,$num1,'iddatafine');
for ($num2 =  $iddatainizio_reg1; $num2 <= $iddatafine_reg1 ; $num2++) {
if (!$fuori_da_regole1) $app_consentito_sett[$idapp][$num2] = "SI";
else $app_consentito_sett[$idapp][$num2] = "NO";
} # fine for $num2
} # fine for $num1
} # fine if ($attiva_regole1_consentite == "s")
else for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) $app_consentito[$dati_app['posizione'][$num1]] = "SI";


# Calcolo gli appartamenti consentiti dalla regola 2
if ($tariffe_mostra == "priv" and ($priv_mod_assegnazione_app != "s" or $priv_mod_prenotazioni != "s") and ($priv_ins_assegnazione_app != "s" or $priv_ins_nuove_prenota != "s")) {
if ($attiva_tariffe_consentite != "n") $tariffe_mostra = $tariffe_consentite_vett;
else {
$tariffe_mostra = array();
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) $tariffe_mostra[$numtariffa] = "SI";
} # fine else if ($attiva_tariffe_consentite != "n")
} # fine if ($tariffe_mostra == "priv" and ($priv_mod_assegnazione_app != "s" or $priv_mod_prenotazioni != "s") and...
if (is_array($tariffe_mostra)) {
$tutti_consentiti = "NO";
$appartamenti_consentiti_regola2 = array();
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($tariffe_mostra[$numtariffa] == "SI") {
if (!$dati_r2["tariffa$numtariffa"]) {
echo "$numtariffa QUI<br>";
$tutti_consentiti = "SI";
break;
} # fine if (!$dati_r2["tariffa$numtariffa"])
else {
$appartamenti_regola2 = explode(",",$dati_r2["tariffa$numtariffa"]);
for ($num1 = 0 ; $num1 < count($appartamenti_regola2) ; $num1++) $appartamenti_consentiti_regola2[$appartamenti_regola2[$num1]] = "SI";
} # fine else if (!$dati_r2["tariffa$numtariffa"])
} # fine if ($tariffe_mostra[$numtariffa] == "SI")
} # fine for $numtariffa
if ($tutti_consentiti != "SI") {
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$idapp = $dati_app['posizione'][$num1];
if ($appartamenti_consentiti_regola2[$idapp] != "SI") {
$app_consentito[$idapp] = "NO";
$quadro_non_preciso = "SI";
} # fine if ($appartamenti_consentiti_regola2[$idapp] != "SI")
} # fine for $num1
} # fine if ($tutti_consentiti != "SI")
} # fine if (is_array($tariffe_mostra))

} # fine function trova_app_consentiti_per_tab_disp




?>