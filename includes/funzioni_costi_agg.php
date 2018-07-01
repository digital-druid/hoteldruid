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




function dati_costi_agg_ntariffe ($tablenometariffe,$num_tariffe,$solo_visibili="NO",$ordine_imposto="",$tableappartamenti="") {
global $LIKE;

if ($num_tariffe == "NO") $num_tariffe = 0;
else {
if (!$num_tariffe) {
$num_tariffe = esegui_query("select nomecostoagg from $tablenometariffe where idntariffe = 1 ");
$num_tariffe = risul_query($num_tariffe,0,'nomecostoagg');
} # fine if (!$num_tariffe)
} # fine else if ($num_tariffe == "NO")
if ($solo_visibili == "SI") $cond_visibili = " and mostra_ca $LIKE 's%'";
$ordine = "tipo_ca, idntariffe";
if ($ordine_imposto) $ordine = $ordine_imposto;
$costi = esegui_query("select * from $tablenometariffe where idntariffe > 10  and nomecostoagg != ''$cond_visibili order by $ordine");
$dati_ca['num'] = numlin_query($costi);
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
$dati_ca[$num1]['id'] = risul_query($costi,$num1,'idntariffe');
$dati_ca['id'][$dati_ca[$num1]['id']] = $num1;
$dati_ca[$num1]['nome'] = risul_query($costi,$num1,'nomecostoagg');
$dati_ca[$num1]['valore'] = risul_query($costi,$num1,'valore_ca');
$dati_ca[$num1]['tipo'] = risul_query($costi,$num1,'tipo_ca');
$dati_ca[$num1]['tipo_val'] = substr($dati_ca[$num1]['tipo'],1,1);
$dati_ca[$num1]['tipo'] = substr($dati_ca[$num1]['tipo'],0,1);
if ($dati_ca[$num1]['tipo_val'] != "f") {
$dati_ca[$num1]['valore_perc'] = risul_query($costi,$num1,'valore_perc_ca');
$dati_ca[$num1]['arrotonda'] = (double) risul_query($costi,$num1,'arrotonda_ca');
} # fine if ($dati_ca[$num1]['tipo_val'] != "f")
$dati_ca[$num1]['tasseperc'] = risul_query($costi,$num1,'tasseperc_ca');
for ($nt = 1 ; $nt <= $num_tariffe ; $nt++) {
$dati_ca[$num1]["tariffa".$nt] = risul_query($costi,$num1,"tariffa".$nt);
if ($dati_ca[$num1]["tariffa".$nt] != "i") {
$dati_ca[$num1]["tipo_associa_tariffa".$nt] = substr($dati_ca[$num1]["tariffa".$nt],0,1);
$dati_ca[$num1]["tariffa".$nt] = substr($dati_ca[$num1]["tariffa".$nt],1);
} # fine if ($dati_ca[$num1]["tariffa".$nt] != "i")
else {
$dati_ca[$num1]["tariffa".$nt] = "";
$dati_ca[$num1]["incomp_tariffa".$nt] = "i";
} # fine else if ($dati_ca[$num1]["tariffa".$nt] != "i")
} # fine for $nt
$dati_ca[$num1]['associasett'] = risul_query($costi,$num1,'associasett_ca');
$numsett = risul_query($costi,$num1,'numsett_ca');
$dati_ca[$num1]['numsett_orig'] = $numsett;
$dati_ca[$num1]['numsett'] = substr($numsett,0,1);
if ($dati_ca[$num1]['numsett'] == "m") $dati_ca[$num1]['sett_meno_una'] = substr($numsett,1,1);
if ($dati_ca[$num1]['numsett'] == "n" or $dati_ca[$num1]['numsett'] == "s") {
$sett_prime_seconde = explode(",",substr($numsett,1));
$dati_ca[$num1]['num_sett_prime'] = $sett_prime_seconde[0];
$dati_ca[$num1]['num_sett_seconde'] = $sett_prime_seconde[1];
} # fine if ($dati_ca[$num1]['numsett'] == "n" or $dati_ca[$num1]['numsett'] == "s")
if ($dati_ca[$num1]['numsett'] == "g") $dati_ca[$num1]['giornisett'] = substr($numsett,1);
$dati_ca[$num1]['mostra'] = risul_query($costi,$num1,'mostra_ca');
$dati_ca[$num1]['raggruppa'] = substr($dati_ca[$num1]['mostra'],1,1);
$dati_ca[$num1]['combina'] = substr($dati_ca[$num1]['mostra'],2,1);
$dati_ca[$num1]['escludi_tot_perc'] = substr($dati_ca[$num1]['mostra'],3,1);
$dati_ca[$num1]['mostra'] = substr($dati_ca[$num1]['mostra'],0,1);
$dati_ca[$num1]['moltiplica'] = risul_query($costi,$num1,'moltiplica_ca');
$dati_ca[$num1]['molt_max'] = substr($dati_ca[$num1]['moltiplica'],1,1);
$molt_agg = explode(",",substr($dati_ca[$num1]['moltiplica'],2));
$dati_ca[$num1]['molt_agg'] = $molt_agg[0];
$dati_ca[$num1]['molt_max_num'] = $molt_agg[1];
$dati_ca[$num1]['moltiplica'] = substr($dati_ca[$num1]['moltiplica'],0,1);
$dati_ca[$num1]['letto'] = risul_query($costi,$num1,'letto_ca');
$dati_ca[$num1]['var_periodip'] = risul_query($costi,$num1,'variazione_ca');
$dati_ca[$num1]['var_percentuale'] = substr($dati_ca[$num1]['var_periodip'],0,1);
$dati_ca[$num1]['var_numsett'] = substr($dati_ca[$num1]['var_periodip'],1,1);
$dati_ca[$num1]['var_moltiplica'] = substr($dati_ca[$num1]['var_periodip'],2,1);
$dati_ca[$num1]['var_tariffea'] = substr($dati_ca[$num1]['var_periodip'],4,1);
$dati_ca[$num1]['var_tariffei'] = substr($dati_ca[$num1]['var_periodip'],5,1);
$dati_ca[$num1]['var_beniinv'] = substr($dati_ca[$num1]['var_periodip'],6,1);
$dati_ca[$num1]['var_appi'] = substr($dati_ca[$num1]['var_periodip'],7,1);
$dati_ca[$num1]['var_comb'] = substr($dati_ca[$num1]['var_periodip'],8,1);
$dati_ca[$num1]['var_periodip'] = substr($dati_ca[$num1]['var_periodip'],3,1);
$dati_ca[$num1]['beniinv_orig'] = risul_query($costi,$num1,'beniinv_ca');
if ($dati_ca[$num1]['beniinv_orig']) {
$beniinv_vett = explode(";",$dati_ca[$num1]['beniinv_orig']);
$dati_ca[$num1]['tipo_beniinv'] = substr($beniinv_vett[0],0,3);
if ($dati_ca[$num1]['tipo_beniinv'] == "mag") $dati_ca[$num1]['mag_beniinv'] = substr($beniinv_vett[0],3);
$dati_ca[$num1]['num_beniinv'] = (count($beniinv_vett) - 1);
for ($num2 = 0 ; $num2 < $dati_ca[$num1]['num_beniinv'] ; $num2++) {
$bene_inv = explode(",",$beniinv_vett[($num2 + 1)]);
$dati_ca[$num1]['id_beneinv'][$num2] = $bene_inv[0];
$dati_ca[$num1]['molt_beneinv'][$num2] = $bene_inv[1];
} # fine for $num2
} # fine if ($dati_ca[$num1]['beniinv_orig'])
$dati_ca[$num1]['periodipermessi_orig'] = risul_query($costi,$num1,'periodipermessi_ca');
$dati_ca[$num1]['periodipermessi'] = substr($dati_ca[$num1]['periodipermessi_orig'],0,1);
if ($dati_ca[$num1]['periodipermessi']) {
$sett_periodipermessi = explode(",",substr($dati_ca[$num1]['periodipermessi_orig'],1));
$num_sett_periodipermessi = count($sett_periodipermessi);
$dati_ca[$num1]['sett_periodipermessi_ini'] = array();
$dati_ca[$num1]['sett_periodipermessi_fine'] = array();
for ($num2 = 0 ; $num2 < $num_sett_periodipermessi ; $num2++) {
$sett_periodipermesso = explode("-",$sett_periodipermessi[$num2]);
$dati_ca[$num1]['sett_periodipermessi_ini'][$num2] = $sett_periodipermesso[0];
$dati_ca[$num1]['sett_periodipermessi_fine'][$num2] = $sett_periodipermesso[1];
} # fine for $num2
} # fine if ($dati_ca[$num1][periodipermessi])
$dati_ca[$num1]['appincompatibili'] = risul_query($costi,$num1,'appincompatibili_ca');
if ($dati_ca[$num1]['letto'] == "s" and $tableappartamenti) {
if (!$app_letto) {
$app_letto = esegui_query("select idappartamenti from $tableappartamenti where letto = '1' ");
$num_app_letto = numlin_query($app_letto);
} # fine if (!$app_letto)
if ($num_app_letto) {
$dati_ca[$num1]['var_appi'] = "s";
for ($num2 = 0 ; $num2 < $num_app_letto ; $num2++) {
$idapp = risul_query($app_letto,$num2,'idappartamenti');
if (!strstr(",".$dati_ca[$num1]['appincompatibili'].",",",$idapp,")) $dati_ca[$num1]['appincompatibili'] .= ",$idapp";
$dati_ca[$num1]['appincompatibili_letto'] = 1;
} # fine for $num2
if (substr($dati_ca[$num1]['appincompatibili'],0,1) == ",") $dati_ca[$num1]['appincompatibili'] = substr($dati_ca[$num1]['appincompatibili'],1);
} # fine if ($num_app_letto)
} # fine if ($dati_ca[$num1]['letto'] == "s" and $tableappartamenti)
$dati_ca[$num1]['categoria'] = risul_query($costi,$num1,'categoria_ca');
$dati_ca[$num1]['numlimite'] = risul_query($costi,$num1,'numlimite_ca');
$regoleassegna_ca = explode(";",risul_query($costi,$num1,'regoleassegna_ca'));
$dati_ca[$num1]['assegna_da_ini_prenota'] = $regoleassegna_ca[0];
$dati_ca[$num1]['assegna_con_num_prenota'] = $regoleassegna_ca[1];
} # fine for $num1

return $dati_ca;

} # fine function dati_costi_agg_ntariffe



function num_costi_in_periodo ($tablecostiprenota,$tableprenota,$id_periodo,$id_costo,$nome_costo,$id_prenota,$tra_anni) {

if ($tra_anni) {
global $PHPR_TAB_PRE;
$tableperiodi_prec = $PHPR_TAB_PRE."periodi".$tra_anni;
$tableperiodi_succ = $PHPR_TAB_PRE."periodi".($tra_anni + 1);
$data_fine = esegui_query("select datainizio,datafine from $tableperiodi_prec where idperiodi = '$id_periodo'");
$data_inizio = aggslashdb(risul_query($data_fine,0,'datainizio'));
$data_fine = aggslashdb(risul_query($data_fine,0,'datafine'));
$periodo_succ = esegui_query("select idperiodi from $tableperiodi_succ where datainizio = '$data_inizio' and datafine = '$data_fine'");
if (numlin_query($periodo_succ) == 1) {
$id_periodo = risul_query($periodo_succ,0,'idperiodi');
$tableprenota = $PHPR_TAB_PRE."prenota".($tra_anni + 1);
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".($tra_anni + 1);
if ($id_prenota) {
$prenota_esistente = esegui_query("select idprenota from $tableprenota where iddatainizio = '0' and commento = '$id_prenota'");
if (numlin_query($prenota_esistente) == 1) $id_prenota = risul_query($prenota_esistente,0,'idprenota');
else $id_prenota = "";
} # fine if ($id_prenota)
} # fine if (numlin_query($periodo_succ) == 1)
} # fine if ($tra_anni)
if ($id_prenota) $cond_escludi_prenota = " and $tablecostiprenota.idprenota != '$id_prenota'";
$costi = esegui_query("select distinct $tablecostiprenota.idcostiprenota,$tablecostiprenota.moltiplica,$tablecostiprenota.associasett,$tablecostiprenota.settimane from $tablecostiprenota inner join $tableprenota on $tablecostiprenota.idprenota = $tableprenota.idprenota where $tablecostiprenota.idntariffe = '$id_costo' and $tablecostiprenota.nome = '$nome_costo' and $tableprenota.iddatainizio <= '$id_periodo' and $tableprenota.iddatafine >= '$id_periodo'$cond_escludi_prenota");
$num_costi = numlin_query($costi);
$num_costi_orig = $num_costi;
for ($num1 = 0 ; $num1 < $num_costi_orig ; $num1++) {
$associasett = risul_query($costi,$num1,'associasett',$tablecostiprenota);
$settimane_costo = risul_query($costi,$num1,'settimane',$tablecostiprenota);
if ($associasett == "s" and str_replace(",$id_periodo,","",$settimane_costo) == $settimane_costo) $num_costi--;
else {
$moltiplica = risul_query($costi,$num1,'moltiplica',$tablecostiprenota);
if ($associasett == "s") {
$settimane = explode(",",$settimane_costo);
$moltiplica = explode(",",$moltiplica);
for ($num2 = 0 ; $num2 < count($settimane) ; $num2++) if ($settimane[$num2] == $id_periodo) $moltiplica = $moltiplica[$num2];
} # fine if ($associasett == "s")
if ($moltiplica > 1) $num_costi = $num_costi + $moltiplica - 1;
} # fine else if ($associasett == "s" and str_replace("","",$settimane) == $settimane)
} # fine for $num1

return $num_costi;

} # fine function num_costi_in_periodo



function trova_periodo_permesso_costo ($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$num_settimane_costo) {

$periodo_costo_trovato = "NO";
if ($dati_ca[$num_costo]['periodipermessi']) {
for ($num1 = 0 ; $num1 < count($dati_ca[$num_costo]['sett_periodipermessi_ini']) ; $num1++) {
if ($dati_ca[$num_costo]['sett_periodipermessi_ini'][$num1] <= $idinizioperiodo and $dati_ca[$num_costo]['sett_periodipermessi_fine'][$num1] >= $idfineperiodo) $periodo_costo_trovato = "SI";
else {
if ($dati_ca[$num_costo]['sett_periodipermessi_ini'][$num1] <= $idfineperiodo and $dati_ca[$num_costo]['sett_periodipermessi_fine'][$num1] >= $idinizioperiodo) {
if ($dati_ca[$num_costo]['periodipermessi'] == "u") $periodo_costo_trovato = "SI";
if ($dati_ca[$num_costo]['periodipermessi'] == "p") {
if ($dati_ca[$num_costo]['associasett'] == "s" or $dati_ca[$num_costo]['numsett'] != "c") $periodo_costo_trovato = "SI";
else {
if ($dati_ca[$num_costo]['sett_periodipermessi_ini'][$num1] > $idinizioperiodo) $periodo_costo_ini = $dati_ca[$num_costo]['sett_periodipermessi_ini'][$num1];
else $periodo_costo_ini = $idinizioperiodo;
if ($dati_ca[$num_costo]['sett_periodipermessi_fine'][$num1] < $idfineperiodo) $periodo_costo_fine = $dati_ca[$num_costo]['sett_periodipermessi_fine'][$num1];
else $periodo_costo_ini = $idfineperiodo;
if ($num_settimane_costo <= ($periodo_costo_fine - $periodo_costo_ini + 1)) $periodo_costo_trovato = "SI";
} # fine else if ($dati_ca[$num_costo][associasett] == "s" or...
} # fine if ($dati_ca[$num_costo][periodipermessi] == "p")
} # fine if ($dati_ca[$num_costo][sett_periodipermessi_ini][$num1] <= $idfineperiodo and...
} # fine else if ($dati_ca[$num_costo][sett_periodipermessi_ini][$num1] <= $idinizioperiodo and...
} # fine for $num1
} # fine if ($dati_ca[$num_costo][periodipermessi])
else $periodo_costo_trovato = "SI";

return $periodo_costo_trovato;

} # fine function trova_periodo_permesso_costo



function calcola_prezzo_totale_costo ($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica,$costo_tariffa,$lista_tariffe,$costo_prenota_tot,$caparra,$numpersone,$costo_escludi_perc=0) {

$prezzo_costo_fisso = (double) $dati_ca[$num_costo]['valore'];
if ($dati_ca[$num_costo]['tipo_val'] != "f") $prezzo_costo_perc = (double) $dati_ca[$num_costo]['valore_perc'];
else $prezzo_costo_perc = 0;
if ($dati_ca[$num_costo]['associasett'] == "s" or $dati_ca[$num_costo]['tipo_val'] == "q" or $dati_ca[$num_costo]['tipo_val'] == "s") {
$lista_tariffe = explode(";",$lista_tariffe);
$lista_tariffep = $lista_tariffe[1];
$lista_tariffe = explode(",",$lista_tariffe[0]);
if ($dati_ca[$num_costo]['tipo_val'] == "q" or $dati_ca[$num_costo]['tipo_val'] == "s") {
$costo_tariffap = (double) 0;
if ($lista_tariffep) {
$lista_tariffep = explode(",",$lista_tariffep);
for ($num1 = 0 ; $num1 < count($lista_tariffep) ; $num1++) $costo_tariffap += (double) $lista_tariffep[$num1];
} # fine ($lista_tariffep)
else for ($num1 = 0 ; $num1 < count($lista_tariffe) ; $num1++) $lista_tariffep[$num1] = (double) 0;
} # fine if ($dati_ca[$num_costo]['tipo_val'] == "q" or $dati_ca[$num_costo]['tipo_val'] == "s")
} # fine if ($dati_ca[$num_costo]['associasett'] == "s" or...

if ($dati_ca[$num_costo]['tipo'] == "u") {
if ($dati_ca[$num_costo]['tipo_val'] == "p") $prezzo_costo_perc = ($costo_tariffa * $prezzo_costo_perc) / 100;
if ($dati_ca[$num_costo]['tipo_val'] == "q") $prezzo_costo_perc = (($costo_tariffa - $costo_tariffap) * $prezzo_costo_perc) / 100;
if ($dati_ca[$num_costo]['tipo_val'] == "s") {
if ($numpersone) $prezzo_costo_perc = (($costo_tariffap / (double) $numpersone) * $prezzo_costo_perc) / 100;
else $prezzo_costo_perc = 0;
} # fine if ($dati_ca[$num_costo]['tipo_val'] == "s")
if ($dati_ca[$num_costo]['tipo_val'] == "t") $prezzo_costo_perc = (($costo_prenota_tot - (double) $costo_escludi_perc) * $prezzo_costo_perc) / 100;
if ($dati_ca[$num_costo]['tipo_val'] == "c") $prezzo_costo_perc = ($caparra * $prezzo_costo_perc) / 100;
if ($dati_ca[$num_costo]['tipo_val'] == "r") $prezzo_costo_perc = ((($costo_prenota_tot - (double) $costo_escludi_perc) - $caparra) * $prezzo_costo_perc) / 100;
} # fine if ($dati_ca[$num_costo]['tipo'] == "u")
if ($dati_ca[$num_costo]['tipo'] == "s") {
if ($dati_ca[$num_costo]['associasett'] == "s") {
$prezzo_costo_fisso_tot = 0;
$prezzo_costo_perc_tot = 0;
$prezzo_costo_sett = 0;
$moltiplica = explode(",",$moltiplica);
$num_lista_tariffe = 0;
$num_sett = 1;
for ($num1 = $idinizioperiodo ; $num1 <= $idfineperiodo ; $num1++) {
if (str_replace(",".$num1.",","",$settimane_costo) != $settimane_costo) {
$prezzo_costo_fisso_tot = $prezzo_costo_fisso_tot + ($prezzo_costo_fisso * $moltiplica[$num_sett]);
if ($dati_ca[$num_costo]['tipo_val'] == "p") $prezzo_costo_sett = ($lista_tariffe[$num_lista_tariffe] * $prezzo_costo_perc) / 100;
if ($dati_ca[$num_costo]['tipo_val'] == "q") $prezzo_costo_sett = (($lista_tariffe[$num_lista_tariffe] - $lista_tariffep[$num_lista_tariffe]) * $prezzo_costo_perc) / 100;
if ($dati_ca[$num_costo]['tipo_val'] == "s") {
if ($numpersone) $prezzo_costo_sett = (($lista_tariffep[$num_lista_tariffe] / (double) $numpersone) * $prezzo_costo_perc) / 100;
else $prezzo_costo_sett = 0;
} # fine if ($dati_ca[$num_costo]['tipo_val'] == "s")
$prezzo_costo_perc_tot = $prezzo_costo_perc_tot + ($prezzo_costo_sett * $moltiplica[$num_sett]);
$num_sett++;
} # fine if (str_replace(",".$num1.",","",$settimane_costo) != $settimane_costo)
$num_lista_tariffe++;
} # fine for $num1
$prezzo_costo_fisso = $prezzo_costo_fisso_tot;
$prezzo_costo_perc = $prezzo_costo_perc_tot;
} # fine if ($dati_ca[$num_costo]['associasett'] == "s")
else $prezzo_costo_fisso = $prezzo_costo_fisso * $settimane_costo;
} # fine if ($dati_ca[$num_costo][tipo] == "s")

if ($dati_ca[$num_costo]['associasett'] != "s") {
$prezzo_costo_fisso = $prezzo_costo_fisso * $moltiplica;
$prezzo_costo_perc = $prezzo_costo_perc * $moltiplica;
} # fine if ($dati_ca[$num_costo]['associasett'] != "s")
if ($dati_ca[$num_costo]['tipo_val'] != "f") $prezzo_costo_perc = floor($prezzo_costo_perc / $dati_ca[$num_costo]['arrotonda']) * $dati_ca[$num_costo]['arrotonda'];
$prezzo_costo = $prezzo_costo_fisso + $prezzo_costo_perc;

return $prezzo_costo;

} # fine function calcola_prezzo_totale_costo



function calcola_settimane_costo ($tableperiodi,$dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$id_periodi_costo,$numsettimane) {

$settimane_costo = "";
if ($dati_ca[$num_costo]['tipo'] == "s") {
if ($dati_ca[$num_costo]['associasett'] == "s") {
$num_attuale = 0;
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
$periodo_costo_trovato = "NO";
if ($dati_ca[$num_costo]['periodipermessi'] == "p") {
for ($num2 = 0 ; $num2 < count($dati_ca[$num_costo]['sett_periodipermessi_ini']) ; $num2++) {
if ($dati_ca[$num_costo]['sett_periodipermessi_ini'][$num2] <= $num1 and $dati_ca[$num_costo]['sett_periodipermessi_fine'][$num2] >= $num1) $periodo_costo_trovato = "SI";
} # fine for $num2
} # fine if ($dati_ca[$num_costo][periodipermessi] == "p")
else $periodo_costo_trovato = "SI";
if ($periodo_costo_trovato == "SI") {
if ($dati_ca[$num_costo]['numsett'] == "t") $settimane_costo .= ",$num1";
if ($dati_ca[$num_costo]['numsett'] == "m" and (($dati_ca[$num_costo]['sett_meno_una'] == "p" and $num1 != $idinizioperiodo) or ($dati_ca[$num_costo]['sett_meno_una'] == "u" and $num1 != $idfineperiodo)) )  $settimane_costo .= ",$num1";
if ($dati_ca[$num_costo]['numsett'] == "c" and str_replace(",$num1,","",$id_periodi_costo) != $id_periodi_costo) $settimane_costo .= ",$num1";
if ($dati_ca[$num_costo]['numsett'] == "s" or $dati_ca[$num_costo]['numsett'] == "n") {
$num_attuale++;
if ($num_attuale <= $dati_ca[$num_costo]['num_sett_prime'] and $dati_ca[$num_costo]['numsett'] == "s") $settimane_costo .= ",$num1";
if ($num_attuale > $dati_ca[$num_costo]['num_sett_prime'] and $dati_ca[$num_costo]['numsett'] == "n") $settimane_costo .= ",$num1";
if ($num_attuale == ($dati_ca[$num_costo]['num_sett_prime'] + $dati_ca[$num_costo]['num_sett_seconde'])) $num_attuale = 0;
} # fine if ($dati_ca[$num_costo][numsett] == "s" or $dati_ca[$num_costo][numsett] == "n")
if ($dati_ca[$num_costo]['numsett'] == "g") {
$dataini_gio = esegui_query("select datainizio from $tableperiodi where idperiodi = '$num1'");
$dataini_gio = risul_query($dataini_gio,0,'datainizio');
$giorno = date("w", mktime(0,0,0,substr($dataini_gio,5,2),substr($dataini_gio,8,2),substr($dataini_gio,0,4)));
if ($giorno == 0) $giorno = 7;
if (str_replace($giorno,"",$dati_ca[$num_costo]['giornisett']) != $dati_ca[$num_costo]['giornisett']) $settimane_costo .= ",$num1";
} # fine if ($dati_ca[$num_costo][numsett] == "g")
} # fine if ($periodo_costo_trovato == "SI")
} # fine for $num1
if ($settimane_costo) $settimane_costo .= ",";
} # fine if ($dati_ca[$num_costo][associasett] == "s")
else {
if ($dati_ca[$num_costo]['numsett'] == "t") $settimane_costo = $idfineperiodo - $idinizioperiodo + 1;
if ($dati_ca[$num_costo]['numsett'] == "m") $settimane_costo = $idfineperiodo - $idinizioperiodo;
if ($dati_ca[$num_costo]['numsett'] == "c") $settimane_costo = $numsettimane;
} # fine else if ($dati_ca[$num_costo][associasett] == "s")
} # fine if ($dati_ca[$num_costo][tipo] == "s")
return $settimane_costo;

} # fine function calcola_settimane_costo



function calcola_moltiplica_costo ($dati_ca,$num_costo,&$moltiplica,$idinizioperiodo,$idfineperiodo,$settimane_costo,$nummoltiplica_ca,$numpersone,$num_letti_agg) {

$moltiplica = "";
if (!strcmp($numpersone,"")) $numpersone = 0;
if (!$dati_ca[$num_costo]['molt_agg']) $dati_ca[$num_costo]['molt_agg'] = 0;
if ($dati_ca[$num_costo]['moltiplica'] == "1") $moltiplica = 1;
if ($dati_ca[$num_costo]['moltiplica'] == "c") $moltiplica = $nummoltiplica_ca;
if ($dati_ca[$num_costo]['moltiplica'] == "p") $moltiplica = $numpersone;
if ($dati_ca[$num_costo]['moltiplica'] == "t") {
$letti_agg_max = 0;
if ($dati_ca[$num_costo]['tipo'] == "s" and $dati_ca[$num_costo]['associasett'] == "s") {
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
if ($num_letti_agg[$num1] > $letti_agg_max) $letti_agg_max = $num_letti_agg[$num1];
if ($settimane_costo != str_replace(",$num1,","",$settimane_costo)) $moltiplica .= ",".max(($numpersone + $num_letti_agg[$num1] + $dati_ca[$num_costo]['molt_agg']),0);
} # fine for $num1
$moltiplica .= ",";
$moltiplica_max = $numpersone + $letti_agg_max;
} # fine if ($dati_ca[$num_costo][tipo] == "s" and $dati_ca[$num_costo]['associasett'] == "s")
else {
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) if ($num_letti_agg[$num1] > $letti_agg_max) $letti_agg_max = $num_letti_agg[$num1];
$moltiplica = max(($numpersone + $letti_agg_max + $dati_ca[$num_costo]['molt_agg']),0);
$moltiplica_max = $moltiplica;
} # fine else if ($dati_ca[$num_costo][tipo] == "s" and $dati_ca[$num_costo]['associasett'] == "s")
} # fine if ($dati_ca[$num_costo][moltiplica] == "t")
else {
$moltiplica = max(($moltiplica + $dati_ca[$num_costo]['molt_agg']),0);
$moltiplica_max = $moltiplica;
if ($dati_ca[$num_costo]['tipo'] == "s" and $dati_ca[$num_costo]['associasett'] == "s") {
$moltiplica = "";
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
if ($settimane_costo != str_replace(",$num1,","",$settimane_costo))  $moltiplica .= ",$moltiplica_max";
} # fine for $num1
$moltiplica .= ",";
} # fine if ($dati_ca[$num_costo]['tipo'] == "s" and $dati_ca[$num_costo]['associasett'] == "s")
} # fine else if ($dati_ca[$num_costo]['moltiplica'] == "t")

return $moltiplica_max;

} # fine function calcola_moltiplica_costo



function aggiorna_letti_agg_in_periodi ($dati_ca,$num_costo,&$num_letti_agg,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica,$nummoltiplica_ca,$numpersone) {

if ($num_letti_agg['max'] == "") $num_letti_agg['max'] = 0;
if ($dati_ca[$num_costo]['letto'] == "s") {
if (!$moltiplica) calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica,$idinizioperiodo,$idfineperiodo,$settimane_costo,$nummoltiplica_ca,$numpersone,"");
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
if (!$num_letti_agg[$num1]) $num_letti_agg[$num1] = 0;
if ($dati_ca[$num_costo]['associasett'] == "s") {
if ($settimane_costo != str_replace(",$num1,","",$settimane_costo)) {
$settimane = explode(",",$settimane_costo);
$moltiplica_sett = explode(",",$moltiplica);
for ($num2 = 0 ; $num2 < count($settimane) ; $num2++) if ($settimane[$num2] == $num1) $moltiplica_sett = $moltiplica_sett[$num2];
$num_letti_agg[$num1] = $num_letti_agg[$num1] + $moltiplica_sett;
} # fine if ($settimane_costo != str_replace(",$num1,","",$settimane_costo))
} # fine if ($dati_ca[$num_costo]['associasett'] == "s")
else $num_letti_agg[$num1] = $num_letti_agg[$num1] + $moltiplica;
if ($num_letti_agg[$num1] > $num_letti_agg['max']) $num_letti_agg['max'] = $num_letti_agg[$num1];
} # fine for $num1
} # fine if ($dati_ca[$num_costo]['letto'] == "s")

} # fine function aggiorna_letti_agg_in_periodi



function controlla_num_limite_costo ($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,&$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica,$num_limite = "",$tra_anni = "") {

$limite_rispettato = "SI";
if ($num_limite == "") $num_limite = $dati_ca[$num_costo]['numlimite'];
if ($num_limite) {
if ($dati_ca[$num_costo]['idntariffe']) $idcostoagg = $dati_ca[$num_costo]['idntariffe'];
else $idcostoagg = $dati_ca[$num_costo]['id'];
$num_costi_presenti_copia = $num_costi_presenti;
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
$sett_attivata = "SI";
if ($dati_ca[$num_costo]['associasett'] == "s" and $settimane_costo == str_replace(",$num1,","",$settimane_costo)) $sett_attivata = "NO";
if ($sett_attivata == "SI") {
if ($num_costi_presenti[$idcostoagg][$num1] == "") $num_costi_presenti[$idcostoagg][$num1] = num_costi_in_periodo($tablecostiprenota,$tableprenota,$num1,$idcostoagg,$dati_ca[$num_costo]['nome'],$dati_ca[$num_costo]['idprenota'],$tra_anni);
if ($dati_ca[$num_costo]['associasett'] == "s") {
if ($settimane_costo != str_replace(",$num1,","",$settimane_costo)) {
$settimane = explode(",",$settimane_costo);
$moltiplica_sett = explode(",",$moltiplica);
for ($num2 = 0 ; $num2 < count($settimane) ; $num2++) if ($settimane[$num2] == $num1) $moltiplica_sett = $moltiplica_sett[$num2];
} # fine if ($settimane_costo != str_replace(",$num1,","",$settimane_costo))
} # fine if ($dati_ca[$num_costo]['associasett'] == "s")
else $moltiplica_sett = $moltiplica;
if ($moltiplica_sett > 1) $num_costi_presenti[$idcostoagg][$num1] = $num_costi_presenti[$idcostoagg][$num1] + $moltiplica_sett;
else $num_costi_presenti[$idcostoagg][$num1]++;
if ($num_costi_presenti[$idcostoagg][$num1] > $num_limite) $limite_rispettato = "NO";
} # fine if ($sett_attivata == "SI")
} # fine for $num1
} # fine if ($num_limite)
if ($limite_rispettato == "NO") $num_costi_presenti = $num_costi_presenti_copia;

return $limite_rispettato;

} # fine function controlla_num_limite_costo



function dati_costi_agg_prenota ($tablecostiprenota,$id_prenota) {

$costi = esegui_query("select * from $tablecostiprenota where idprenota = '$id_prenota' order by tipo, idcostiprenota");
$dati_cap['num'] = numlin_query($costi);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
$dati_cap[$numca]['tipo'] = risul_query($costi,$numca,'tipo');
$dati_cap[$numca]['tipo_val'] = substr($dati_cap[$numca]['tipo'],1,1);
$dati_cap[$numca]['tipo'] = substr($dati_cap[$numca]['tipo'],0,1);
$dati_cap[$numca]['id'] = risul_query($costi,$numca,'idcostiprenota');
$dati_cap['id'][$dati_cap[$numca]['id']] = $numca;
$dati_cap[$numca]['nome'] = risul_query($costi,$numca,'nome');
$dati_cap[$numca]['valore'] = risul_query($costi,$numca,'valore');
if ($dati_cap[$numca]['tipo_val'] != "f") {
$dati_cap[$numca]['valore_perc'] = risul_query($costi,$numca,'valore_perc');
$dati_cap[$numca]['arrotonda'] = (double) risul_query($costi,$numca,'arrotonda');
} # fine if ($dati_cap[$numca]['tipo_val'] != "f")
$dati_cap[$numca]['tasseperc'] = (double) risul_query($costi,$numca,'tasseperc');
$dati_cap[$numca]['associasett'] = risul_query($costi,$numca,'associasett');
$numsett = risul_query($costi,$numca,'varnumsett');
$dati_cap[$numca]['numsett_orig'] = $numsett;
$dati_cap[$numca]['numsett'] = substr($numsett,0,1);
if ($dati_cap[$numca]['numsett'] == "m") $dati_cap[$numca]['sett_meno_una'] = substr($numsett,1,1);
if ($dati_cap[$numca]['numsett'] == "n" or $dati_cap[$numca]['numsett'] == "s") {
$sett_prime_seconde = explode(",",substr($numsett,1));
$dati_cap[$numca]['num_sett_prime'] = $sett_prime_seconde[0];
$dati_cap[$numca]['num_sett_seconde'] = $sett_prime_seconde[1];
} # fine if ($dati_cap[$numca]['numsett'] == "n" or $dati_cap[$numca]['numsett'] == "s")
if ($dati_cap[$numca]['numsett'] == "g") $dati_cap[$numca]['giornisett'] = substr($numsett,1);
$dati_cap[$numca]['moltiplica'] = risul_query($costi,$numca,'varmoltiplica');
$dati_cap[$numca]['molt_max'] = substr($dati_cap[$numca]['moltiplica'],1,1);
$molt_agg = explode(",",substr($dati_cap[$numca]['moltiplica'],2));
$dati_cap[$numca]['molt_agg'] = $molt_agg[0];
$dati_cap[$numca]['molt_max_num'] = $molt_agg[1];
$dati_cap[$numca]['moltiplica'] = substr($dati_cap[$numca]['moltiplica'],0,1);
$dati_cap[$numca]['letto'] = risul_query($costi,$numca,'letto');
$dati_cap[$numca]['beniinv_orig'] = risul_query($costi,$numca,'varbeniinv');
if ($dati_cap[$numca]['beniinv_orig']) {
$beniinv_vett = explode(";",$dati_cap[$numca]['beniinv_orig']);
$dati_cap[$numca]['beniinv_ripeti'] = $beniinv_vett[0];
$dati_cap[$numca]['tipo_beniinv'] = substr($beniinv_vett[1],0,3);
if ($dati_cap[$numca]['tipo_beniinv'] == "mag") $dati_cap[$numca]['mag_beniinv'] = substr($beniinv_vett[1],3);
$dati_cap[$numca]['num_beniinv'] = (count($beniinv_vett) - 2);
for ($num1 = 0 ; $num1 < $dati_cap[$numca]['num_beniinv'] ; $num1++) {
$bene_inv = explode(",",$beniinv_vett[($num1 + 2)]);
$dati_cap[$numca]['id_beneinv'][$num1] = $bene_inv[0];
$dati_cap[$numca]['molt_beneinv'][$num1] = $bene_inv[1];
} # fine for $num1
} # fine if ($dati_cap[$numca]['beniinv_orig'])
$dati_cap[$numca]['periodipermessi_orig'] = risul_query($costi,$numca,'varperiodipermessi');
$dati_cap[$numca]['periodipermessi'] = substr($dati_cap[$numca]['periodipermessi_orig'],0,1);
if ($dati_cap[$numca]['periodipermessi']) {
$sett_periodipermessi = explode(",",substr($dati_cap[$numca]['periodipermessi_orig'],1));
for ($num1 = 0 ; $num1 < count($sett_periodipermessi) ; $num1++) {
$sett_periodipermesso = explode("-",$sett_periodipermessi[$num1]);
$dati_cap[$numca]['sett_periodipermessi_ini'][$num1] = $sett_periodipermesso[0];
$dati_cap[$numca]['sett_periodipermessi_fine'][$num1] = $sett_periodipermesso[1];
} # fine for $num1
} # fine if ($dati_cap[$numca]['periodipermessi'])
$dati_cap[$numca]['settimane'] = risul_query($costi,$numca,'settimane');
$dati_cap[$numca]['moltiplica_costo'] = risul_query($costi,$numca,'moltiplica');
if ($dati_cap[$numca]['associasett'] == "s") {
$sett = explode(",",$dati_cap[$numca]['settimane']);
$molt = explode(",",$dati_cap[$numca]['moltiplica_costo']);
for ($num1 = 1 ; $num1 < (count($sett) - 1) ; $num1++) $dati_cap[$numca]['moltiplica_costo_sett'][$sett[$num1]] = $molt[$num1];
} # fine if ($dati_cap[$numca]['associasett'] == "s")
$dati_cap[$numca]['idntariffe'] = risul_query($costi,$numca,'idntariffe');
$dati_cap[$numca]['appincompatibili'] = risul_query($costi,$numca,'varappincompatibili');
$dati_cap[$numca]['categoria'] = risul_query($costi,$numca,'categoria');
$dati_cap[$numca]['combina'] = risul_query($costi,$numca,'variazione');
$dati_cap[$numca]['escludi_tot_perc'] = substr($dati_cap[$numca]['combina'],1,1);
$dati_cap[$numca]['combina'] = substr($dati_cap[$numca]['combina'],0,1);
$dati_cap[$numca]['tariffeassociate'] = risul_query($costi,$numca,'vartariffeassociate');
$dati_cap[$numca]['tipo_tariffeassociate'] = substr($dati_cap[$numca]['tariffeassociate'],0,1);
$dati_cap[$numca]['tariffeassociate'] = substr($dati_cap[$numca]['tariffeassociate'],1);
$incomp_tariffe = risul_query($costi,$numca,'vartariffeincomp');
$incomp_tariffe = explode(",",$incomp_tariffe);
for ($num1 = 0 ; $num1 < count($incomp_tariffe) ; $num1++) $dati_cap[$numca]["incomp_tariffa".$incomp_tariffe[$num1]] = "i";
$dati_cap[$numca]['idprenota'] = $id_prenota;
$dati_cap[$numca]['datainserimento'] = risul_query($costi,$numca,'datainserimento');
$dati_cap[$numca]['utente_inserimento'] = risul_query($costi,$numca,'utente_inserimento');
} # fine for $numca

return $dati_cap;

} # fine function dati_costi_agg_prenota



function associa_costo_a_tariffa ($dati_ca,$num_costo,$tariffa,$lunghezza_periodo) {

if ($dati_ca[$num_costo][$tariffa]) {
$associa_costo = "SI";
if (substr($dati_ca[$num_costo][$tariffa],0,1) == "=" and $lunghezza_periodo != substr($dati_ca[$num_costo][$tariffa],1)) $associa_costo = "NO";
if (substr($dati_ca[$num_costo][$tariffa],0,1) == ">" and $lunghezza_periodo < substr($dati_ca[$num_costo][$tariffa],1)) $associa_costo = "NO";
if (substr($dati_ca[$num_costo][$tariffa],0,1) == "<" and $lunghezza_periodo > substr($dati_ca[$num_costo][$tariffa],1)) $associa_costo = "NO";
if (substr($dati_ca[$num_costo][$tariffa],0,1) == "|") {
$valminmax = explode("<",substr($dati_ca[$num_costo][$tariffa],1));
if ($lunghezza_periodo < $valminmax[0] or $lunghezza_periodo > $valminmax[1]) $associa_costo = "NO";
} # fine if (substr($dati_ca[$num_costo][$tariffa],0,1) == "|")
} # fine if ($dati_ca[$num_costo][$tariffa])
else $associa_costo = "NO";

return $associa_costo;

} # fine function associa_costo_a_tariffa



function comunica_aggiunta_costo ($dati_ca,$num_costo,$n_prezzo_costo_agg,$stile_soldi,$pag,$Euro,$associasett_ca,$moltiplica,$settimane_costo,$per_la_prenotazione="",$silenzio="") {

global $parola_settimane,$parola_settimanale;
$val_costoagg_p = punti_in_num($n_prezzo_costo_agg,$stile_soldi);
if ($dati_ca[$num_costo]['tipo'] == "u") $mess .= mex("Il costo aggiuntivo unico",$pag);
if ($dati_ca[$num_costo]['tipo'] == "s") $mess .= mex("Il costo aggiuntivo $parola_settimanale",$pag);
$mess .= " \"<b>".$dati_ca[$num_costo]['nome']."</b>\"";
if ($associasett_ca == "s") {
if (!@is_array($moltiplica)) $valnummoltiplica_ca = 1;
else {
$valnummoltiplica_ca = $moltiplica[1];
for ($num2 = 2 ; $num2 < (count($moltiplica) - 1) ; $num2++) if ($moltiplica[$num2] != $valnummoltiplica_ca) $valnummoltiplica_ca = 1;
} # fine else if (!@is_array($moltiplica))
} # fine if ($associasett_ca == "s")
else $valnummoltiplica_ca = $moltiplica;
if ($dati_ca[$num_costo]['tipo'] == "s") {
if ($associasett_ca == "n") $numsettimane = $settimane_costo;
else {
if ($settimane_costo) $numsettimane = count(explode(",",$settimane_costo)) - 2;
else $numsettimane = "0";
} # fine else if ($associasett_ca == "n")
} # fine if ($dati_ca[$num_costo]['tipo'] == "s")
else $numsettimane = "";
if ($valnummoltiplica_ca != 1 or strcmp($numsettimane,"")) $mess .= " (";
if (strcmp($numsettimane,"")) $mess .= "$numsettimane ".mex("$parola_settimane",$pag);
if ($valnummoltiplica_ca != 1 and strcmp($numsettimane,"")) $mess .= " ";
if ($valnummoltiplica_ca != 1) $mess .= mex("moltiplicato per",$pag)." $valnummoltiplica_ca";
if ($valnummoltiplica_ca != 1 or strcmp($numsettimane,"")) $mess .= ")";
$mess .= " ".mex("verrà aggiunto",$pag)."$per_la_prenotazione: <b>$val_costoagg_p</b> $Euro.<br>";

if (!$silenzio) echo $mess;
else return $mess;

} # fine function comunica_aggiunta_costo



function calcola_ripetizioni_costo ($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica) {

$num_ripetizioni_costo = 0;
if ($dati_ca[$num_costo]['tipo'] == "u") $num_ripetizioni_costo = 1;
if ($dati_ca[$num_costo]['tipo'] == "s") {
if ($dati_ca[$num_costo]['associasett'] == "s") {
$num_sett = 1;
$moltiplica_sett = explode(",",$moltiplica);
for ($num1 = $idinizioperiodo ; $num1 <= $idfineperiodo ; $num1++) {
if (str_replace(",".$num1.",","",$settimane_costo) != $settimane_costo) {
$num_ripetizioni_costo = $num_ripetizioni_costo + $moltiplica_sett[$num_sett];
$num_sett++;
} # fine if (str_replace(",".$num1.",","",$settimane_costo) != $settimane_costo)
} # fine for $num1
$prezzo_costo = $prezzo_costo_tot;
} # fine if ($dati_ca[$num_costo]['associasett'] == "s")
else $num_ripetizioni_costo = $settimane_costo;
} # fine if ($dati_ca[$num_costo][tipo] == "s")
if ($dati_ca[$num_costo]['associasett'] != "s") $num_ripetizioni_costo = $num_ripetizioni_costo * $moltiplica;

return $num_ripetizioni_costo;

} # fine function calcola_ripetizioni_costo



function controlla_beni_inventario_costo ($tablerelinventario,$dati_ca,$num_costo,&$beniinv_presenti,&$num_ripetizioni_costo,$sottrai,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica,$idapp,$beniinv_ripeti="") {

$beni_presenti = "SI";
if ($dati_ca[$num_costo]['tipo_beniinv']) {
if (!strcmp($num_ripetizioni_costo,"")) $num_ripetizioni_costo = calcola_ripetizioni_costo($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica);
if (!strcmp($beniinv_ripeti,"")) $beniinv_ripeti = $dati_ca[$num_costo]['beniinv_ripeti'];
if ($beniinv_ripeti) $num_ripetizioni_costo_diff = $num_ripetizioni_costo - $beniinv_ripeti;
else $num_ripetizioni_costo_diff = $num_ripetizioni_costo;
if ($num_ripetizioni_costo_diff) {
$tipo_beniinv = $dati_ca[$num_costo]['tipo_beniinv'];
if ($tipo_beniinv == "mag") {
$tipo_beniinv .= $dati_ca[$num_costo]['mag_beniinv'];
$cond_mag = "idmagazzino = '".$dati_ca[$num_costo]['mag_beniinv']."'";
} # fine if ($tipo_beniinv == "mag")
if ($tipo_beniinv == "app") {
$tipo_beniinv .= $idapp;
$cond_mag = "idappartamento = '$idapp'";
} # fine if ($tipo_beniinv == "app")
for ($num1 = 0 ; $num1 < $dati_ca[$num_costo]['num_beniinv'] ; $num1++) {
$id_beneinv = $dati_ca[$num_costo]['id_beneinv'][$num1];
if (!strcmp($beniinv_presenti[$tipo_beniinv][$id_beneinv],"")) {
$beniinv_presenti[$tipo_beniinv][$id_beneinv] = 0;
$bip = esegui_query("select quantita from $tablerelinventario where idbeneinventario = '$id_beneinv' and $cond_mag ");
if (numlin_query($bip)) $beniinv_presenti[$tipo_beniinv][$id_beneinv] = risul_query($bip,0,'quantita');
else $beni_presenti = "NO";
} # fine if (!strcmp($beniinv_presenti[$tipo_beniinv][$id_beneinv],""))
$num_beni_tot = $num_ripetizioni_costo_diff * $dati_ca[$num_costo]['molt_beneinv'][$num1];
if (($beniinv_presenti[$tipo_beniinv][$id_beneinv] - $num_beni_tot) < 0) {
$beni_presenti = "NO";
break;
} # fine if (($beniinv_presenti[$tipo_beniinv][$id_beneinv] - $num_beni_tot) < 0)
elseif ($sottrai == "SI") $beniinv_presenti[$tipo_beniinv][$id_beneinv] = $beniinv_presenti[$tipo_beniinv][$id_beneinv] - $num_beni_tot;
} # fine for $num1
} # fine if ($num_ripetizioni_costo_diff)
} # fine if ($dati_ca[$num_costo]['tipo_beniinv'])

return $beni_presenti;

} # fine function controlla_beni_inventario_costo



function aggiorna_beniinv_presenti ($tablerelinventario,$beniinv_presenti) {

if ($beniinv_presenti) {
foreach ($beniinv_presenti as $tipo_beniinv => $val) {
$idmag = substr($tipo_beniinv,3);
$tipo_beneinv = substr($tipo_beniinv,0,3);
if ($tipo_beneinv == "mag") $cond_mag = "idmagazzino = '$idmag'";
else $cond_mag = "idappartamento = '".aggslashdb($idmag)."'";
$id_beniinv = $val;
foreach ($id_beniinv as $id_beneinv => $n_num_bene) {
esegui_query("update $tablerelinventario set quantita = '$n_num_bene' where idbeneinventario = '$id_beneinv' and $cond_mag ");
} # fine foreach ($id_beniinv as $id_beneinv => $n_num_bene)
} # fine foreach ($beniinv_presenti as $tipo_beniinv => $val)
} # fine if ($beniinv_presenti)

} # fine function aggiorna_beniinv_presenti



function aggiorna_privilegi_ins_costo ($idntariffe,$tableprivilegi,$id_utente,$anno,$attiva_costi_agg_consentiti,$priv_ins_costi_agg,$utenti_gruppi,$q_utenti_costi_sel="") {

if ($attiva_costi_agg_consentiti != "n") {
$costi_agg_cons_int = esegui_query("select costi_agg_consentiti from $tableprivilegi where idutente = '$id_utente' and anno = '$anno' ");
$costi_agg_cons_int = risul_query($costi_agg_cons_int,0,"costi_agg_consentiti");
esegui_query("update $tableprivilegi set costi_agg_consentiti = '$costi_agg_cons_int,$idntariffe' where idutente = '$id_utente' and anno = '$anno' ");
} # fine if ($attiva_costi_agg_consentiti != "n")
if ($priv_ins_costi_agg == "g" or ($id_utente == 1 and $q_utenti_costi_sel)) {
if ($id_utente == 1 and !@is_array($utenti_gruppi)) {
if ($q_utenti_costi_sel == "q") {
global $LIKE;
$q_utenti_costi_sel = esegui_query("select * from $tableprivilegi where anno = '$anno' and costi_agg_consentiti $LIKE 's%' ");
} # fine if ($q_utenti_costi_sel == "q")
$utenti_gruppi = array();
for ($num1 = 0 ; $num1 < numlin_query($q_utenti_costi_sel) ; $num1++) $utenti_gruppi[risul_query($q_utenti_costi_sel,$num1,'idutente')] = 1;
} # fine if ($id_utente == 1 and !@is_array($utenti_gruppi))
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) {
if ($idut_gr != $id_utente) {
$costi_agg_cons_int = esegui_query("select costi_agg_consentiti from $tableprivilegi where idutente = '$idut_gr' and anno = '$anno' ");
if (numlin_query($costi_agg_cons_int) == 1) {
$costi_agg_cons_int = risul_query($costi_agg_cons_int,0,'costi_agg_consentiti');
if (substr($costi_agg_cons_int,0,1) != "n") {
esegui_query("update $tableprivilegi set costi_agg_consentiti = '$costi_agg_cons_int,$idntariffe' where idutente = '$idut_gr' and anno = '$anno' ");
} # fine if (substr($costi_agg_cons_int,0,1) != "n")
} # fine if (numlin_query($costi_agg_cons_int) == 1)
} # fine if ($idut_gr != $id_utente)
} # fine foreach ($utenti_gruppi as $idut_gr => $val)
} # fine if ($priv_ins_costi_agg == "g" or ($id_utente == 1 and $q_utenti_costi_sel))

} # fine function aggiorna_privilegi_ins_costo





?>