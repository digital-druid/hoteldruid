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


function spezzaprenota ($idinizio,$idfine,$anno,&$limiti_var,$profondita,&$n_tronchi,&$vet_appartamenti,&$vett_idinizio,&$vett_idfine,$num_persone,&$app_prenota_id,&$app_orig_prenota_id,&$inizio_prenota_id,&$fine_prenota_id,&$app_assegnabili_id,&$prenota_in_app_sett,&$dati_app,$nome_tab_prenota = "prenota") {
global $debug;

$limiti_var['n_ini'] = $idinizio;
$limiti_var['n_fine'] = $idfine;
if (!is_array($profondita)) {
$primo_ciclo = "SI";
$prof_copia = $profondita;
$profondita = "";
$profondita['iniziale'] = $prof_copia;
$profondita['attuale'] = $prof_copia;
$tableprenota = $nome_tab_prenota . $anno . $profondita['iniziale'];
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
$tot_prenota = risul_query($max_prenota,0,0);
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$nome_tab_prenota);
} # fine if (!is_array($profondita))

if (!$n_tronchi) $n_tronchi = 0;

# ciclo da ripetere per ogni appartamento che non inizia con una
# prenotazione fissa, non spostabile o con $maxoccupanti troppo basso
$numsettimane = $idfine - $idinizio + 1;
$n_tronchi3 = -1;
if ($limiti_var['tutti_fissi'] != "SI" and $limiti_var['t_limite'] >= time()) {

for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if ($dati_app['maxocc'][$numapp] >= $num_persone) {
$prima_prenota = prenota_in_app_e_periodo($numapp,$idinizio,$idinizio,$prenota_in_app_sett,$fine_prenota_id,$num_pp);
if ($num_pp != 0) $idprima_prenota = $prima_prenota[1];
else $idprima_prenota = "";
if (!$idprima_prenota or $app_assegnabili_id[$idprima_prenota]) {
$limiti_var_orig = $limiti_var;
$nuova_profondita = $profondita;
$nuova_profondita['attuale'] = $profondita['attuale'] + 1;
$app_prenota_id2 = $app_prenota_id;
$prenota_in_app_sett2 = $prenota_in_app_sett;
$ap_ric = "";
$ap_ric[$numapp] = "SI";
$n_tronchi2 = $n_tronchi;
$vet_appartamenti2 = $vet_appartamenti;
$vett_idinizio2 = $vett_idinizio;
$vett_idfine2 = $vett_idfine;

for ($num2 = 0 ; $num2 < $numsettimane ; $num2 = $num2 + 1) {
$id_settimana = $idinizio + $num2;
$fatto_libera = "";
if ($debug == "on") {
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> <em>".date("H:i:s")."</em> spezz_libera  da $id_settimana a $id_settimana negli app ".implode(",",array_keys($ap_ric))."<br>";
} # fine if ($debug == "on")
liberasettimane($id_settimana,$id_settimana,$limiti_var,$anno,$fatto_libera,$app_liber,$nuova_profondita,$ap_ric,$app_prenota_id2,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett2,$dati_app,$nome_tab_prenota);
if ($debug == "on") {
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> finito <em>".date("H:i:s")."</em> spezz_lib $fatto_libera $app_liber<br>";
} # fine if ($debug == "on")
if ($fatto_libera == "SI") {
$nuova_profondita['tot_prenota_attuale']++;
$app_prenota_id2[$nuova_profondita['tot_prenota_attuale']] = $numapp;
$inizio_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $id_settimana;
$fine_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $id_settimana;
$prenota_in_app_sett2[$numapp][$id_settimana] = $nuova_profondita['tot_prenota_attuale'];
} # fine if ($fatto_libera == "SI")
else break;
} # fine for $num2

if ($fatto_libera == "SI" or $id_settimana != $idinizio) {

$n_tronchi2 = $n_tronchi2 + 1;
$vet_appartamenti2[$n_tronchi2] = $numapp;
$vett_idinizio2[$n_tronchi2] = $idinizio;
$vett_idfine2[$n_tronchi2] = $idfine;
if ($fatto_libera != "SI") {
$idfine2 = $id_settimana - 1;
$vett_idfine2[$n_tronchi2] = $idfine2;
if ($debug == "on") {
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> <em>".date("H:i:s")."</em> spezza  da $id_settimana a $idfine<br>";
} # fine if ($debug == "on")
spezzaprenota($id_settimana,$idfine,$anno,$limiti_var,$nuova_profondita,$n_tronchi2,$vet_appartamenti2,$vett_idinizio2,$vett_idfine2,$num_persone,$app_prenota_id2,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett2,$dati_app,$nome_tab_prenota);
if ($debug == "on") {
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> finito <em>".date("H:i:s")."</em> spezza $fatto_libera $app_liber<br>";
} # fine if ($debug == "on")
if ($n_tronchi2 == -1) { break; }
} # fine if ($fatto_libera != "SI")

if ($n_tronchi3 == -1 or $n_tronchi2 < $n_tronchi3) {
$n_tronchi3 = $n_tronchi2;
$vet_appartamenti3 = $vet_appartamenti2;
$vett_idinizio3 = $vett_idinizio2;
$vett_idfine3 = $vett_idfine2;
for ($num2 = ($profondita['tot_prenota_attuale'] + 1) ; $num2 <= $nuova_profondita['tot_prenota_attuale'] ; $num2++) {
$app_prenota_id2[$num2] = "";
for ($num3 = $inizio_prenota_id[$num2] ; $num3 <= $fine_prenota_id[$num2] ; $num3++) {
$prenota_in_app_sett2[$numapp][$num3] = "";
} # fine for $num3
} # fine for $num2
$app_prenota_id_mod = $app_prenota_id2;
$prenota_in_app_sett_mod = $prenota_in_app_sett2;
} # fine if (!$n_tronchi3 or $n_tronchi2 < $n_tronchi3)

if ($n_tronchi3 == 1) break;

} # fine if ($fatto_libera == "SI" or $idsettimana != $idinizio)

for ($num2 = ($profondita['tot_prenota_attuale'] + 1) ; $num2 <= $nuova_profondita['tot_prenota_attuale'] ; $num2++) {
$inizio_prenota_id[$num2] = "";
$fine_prenota_id[$num2] = "";
} # fine for $num2
aggiorna_app_aggiunti($limiti_var,$limiti_var_orig,$app_orig_prenota_id,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett);

} # fine if (!$idprima_prenota or $app_assegnabili_id[$idprima_prenota])
} # fine if ($dati_app['maxocc'][$numapp] >= $num_persone)
} # fine for $num1

} # fine if ($limiti_var['tutti_fissi'] != "SI" and...

$n_tronchi = $n_tronchi3;
if ($n_tronchi != -1) {
$vet_appartamenti = $vet_appartamenti3;
$vett_idinizio = $vett_idinizio3;
$vett_idfine = $vett_idfine3;
$app_prenota_id = $app_prenota_id_mod;
$prenota_in_app_sett = $prenota_in_app_sett_mod;
if ($primo_ciclo == "SI") {
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $n_tronchi = -1;
} # fine if ($primo_ciclo == "SI")
} # fine if ($n_tronchi != -1)

} # fine function spezzaprenota



?>
