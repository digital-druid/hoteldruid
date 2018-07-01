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



$debug = "off";
$sec_limite_libsett = "";
#apd_set_pprof_trace();
#if (function_exists('ini_set')) @ini_set('xdebug.max_nesting_level',1000000);


# Function per passare dalla tabella prenota alle variabili
function tab_a_var (&$limiti_var,&$app_prenota_id,&$app_orig_prenota_id,&$inizio_prenota_id,&$fine_prenota_id,&$app_assegnabili_id,&$prenota_in_app_sett,$anno,&$dati_app,$profondita,$nome_tab_prenota = "prenota") {
global $PHPR_TAB_PRE,$sec_limite_libsett,$debug;
$tableprenota = $nome_tab_prenota . $anno . $profondita['iniziale'];
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";

if (!$limiti_var['idperiodocorrente']) $limiti_var['idperiodocorrente'] = calcola_id_periodo_corrente($anno);
if (!$limiti_var['tutti_fissi']) {
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$prenota_tutte_fisse = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'tutti_fissi' and idutente = '1'");
$limiti_var['tutti_fissi'] = risul_query($prenota_tutte_fisse,0,'valpersonalizza');
if ($limiti_var['tutti_fissi'] != "SI") {
if (defined("C_SEC_LIMITE_LIBERA_APP") and C_SEC_LIMITE_LIBERA_APP != "") $sec_limite_libsett = C_SEC_LIMITE_LIBERA_APP;
else $sec_limite_libsett = $limiti_var['tutti_fissi'];
$limiti_var['t_limite'] = (time() + $sec_limite_libsett);
} # fine if ($limiti_var['tutti_fissi'] != "SI")
} # fine if (!$limiti_var['tutti_fissi'])
if (!$limiti_var['lim_prenota_temp']) {
$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$limiti_var['lim_prenota_temp'] = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60)));
} # fine if (!$limiti_var['lim_prenota_temp'])

# metto i dati della tabella appartamenti in $dati_app
if (!$dati_app) {
$idapp = esegui_query("select idappartamenti,maxoccupanti,app_vicini from $tableappartamenti order by priorita");
$numappartamenti = numlin_query($idapp);
$grp_vicini = array();
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$numapp = risul_query($idapp,$num1,'idappartamenti');
$maxocc = risul_query($idapp,$num1,'maxoccupanti');
$dati_app['posizione'][$num1] = $numapp;
$dati_app['maxocc'][$numapp] = $maxocc;
$dati_app['vicini'][$numapp] = risul_query($idapp,$num1,'app_vicini');
if ($dati_app['vicini'][$numapp]) {
$dati_app['vicini'][$numapp] .= ",$numapp";
$gruppo_corr = -1;
$lst_vicini = explode(",",$dati_app['vicini'][$numapp]);
for ($num2 = 0 ; $num2 < count($grp_vicini) ; $num2++) {
for ($num3 = 0 ; $num3 < count($lst_vicini) ; $num3++) {
if (strstr(",".$grp_vicini[$num2].",",",".$lst_vicini[$num3].",")) {
if ($gruppo_corr < 0) {
$gruppo_corr = $num2;
$grp_vicini[$num2] .= ",".$dati_app['vicini'][$numapp];
} # fine if ($gruppo_corr < 0)
elseif ($gruppo_corr != $num2) {
$grp_vicini[$gruppo_corr] .= ",".$grp_vicini[$num2];
$grp_vicini[$num2] = "";
} # fine elseif ($gruppo_corr != $num2)
} # fine if (strstr(",".$grp_vicini[$num2].",",",".$lst_vicini[$num3].","))
} # fine for $num3
} # fine for $num2
if ($gruppo_corr < 0) $grp_vicini[count($grp_vicini)] = $dati_app['vicini'][$numapp];
} # fine if ($dati_app['vicini'][$numapp])
else $dati_app['vicini'][$numapp] = "$numapp";
} # fine for $num1
asort ($dati_app['maxocc']);
reset ($dati_app['maxocc']);
$dati_app['minocc'] = current($dati_app['maxocc']);
$lista_tutti_app = ",";
foreach ($dati_app['posizione'] as $val) $lista_tutti_app .= "$val,";
$dati_app['lista'] = $lista_tutti_app;
$dati_app['totapp'] = $numappartamenti;
$dati_app['grp_vicini_num'] = array();
$dati_app['grp_vicini'] = array();
for ($num1 = 0 ; $num1 < count($grp_vicini) ; $num1++) {
if ($grp_vicini[$num1]) {
$lst_vicini = explode(",",$grp_vicini[$num1]);
$val_vicini = array();
for ($num2 = 0 ; $num2 < count($lst_vicini) ; $num2++) $val_vicini[$lst_vicini[$num2]] = 1;
$val_vicini = array_keys($val_vicini);
$dati_app['grp_vicini_num'][count($dati_app['grp_vicini_num'])] = count($val_vicini);
$dati_app['grp_vicini'][count($dati_app['grp_vicini'])] = ",".implode(",",$val_vicini).",";
} # fine if ($grp_vicini[$num1])
} # fine for $num1
} # fine if (!$dati_app)

if ((string) $limiti_var['ini'] == "") $limiti_var['ini'] = ($limiti_var['n_fine'] + 1);
if ((string) $limiti_var['fine'] == "") $limiti_var['fine'] = ($limiti_var['n_ini'] - 1);
for ($num_rip = 0 ; $num_rip < 2 ; $num_rip++) {
if ($num_rip) {
if ($lista_comp) {
$lista_comp = explode(",",substr($lista_comp,1));
$num_lista_comp = count($lista_comp);
for ($num1 = 0 ; $num1 < $num_lista_comp ; $num1++) {
if (!$app_prenota_id[$lista_comp[$num1]]) {
$query_comp .= "idprenota = '".$lista_comp[$num1]."' or ";
} # fine if (!$app_prenota_id[$lista_comp[$num1]])
} # fine for $num1
$query_comp = substr($query_comp,0,-3);
} # fine if ($lista_comp)
if ($query_comp) $prenota_in_var = esegui_query("select idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,app_assegnabili,num_persone,idprenota_compagna,checkin,datainserimento from $tableprenota where $query_comp ");
else break;
} # fine if ($num_rip)
else $prenota_in_var = esegui_query("select idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,assegnazioneapp,app_assegnabili,num_persone,idprenota_compagna,checkin,datainserimento from $tableprenota where (iddatainizio < '".$limiti_var['ini']."' and iddatafine >= '".$limiti_var['n_ini']."') or (iddatainizio <= '".$limiti_var['n_fine']."' and iddatafine > '".$limiti_var['fine']."')");
$num_prenota_in_var = numlin_query($prenota_in_var);
for ($num1 = 0 ; $num1 < $num_prenota_in_var ; $num1++) {
$ins_prenota = 1;
if (risul_query($prenota_in_var,$num1,'idclienti') == "0") {
if ($limiti_var['lim_prenota_temp'] > risul_query($prenota_in_var,$num1,'datainserimento')) $ins_prenota = 0;
} # fine if (risul_query($prenota_in_var,$num1,'idclienti') == "0")
$idprenota = risul_query($prenota_in_var,$num1,'idprenota');
if (!$app_prenota_id[$idprenota] and $ins_prenota) {
$app_prenota = risul_query($prenota_in_var,$num1,'idappartamenti');
if (strstr($dati_app['lista'],",".$app_prenota.",")) {
$app_prenota_id[$idprenota] = $app_prenota;
$app_orig_prenota_id[$idprenota] = $app_prenota_id[$idprenota];
$inizio_prenota_id[$idprenota] = risul_query($prenota_in_var,$num1,'iddatainizio');
$fine_prenota_id[$idprenota] = risul_query($prenota_in_var,$num1,'iddatafine');
$app_assegnabili_id[0][$idprenota] = risul_query($prenota_in_var,$num1,'idprenota_compagna');
if ($app_assegnabili_id[0][$idprenota]) $lista_comp .= ",".$app_assegnabili_id[0][$idprenota];
for ($num2 = $inizio_prenota_id[$idprenota] ; $num2 <= $fine_prenota_id[$idprenota] ; $num2++) {
$prenota_in_app_sett[$app_prenota_id[$idprenota]][$num2] = $idprenota;
} # fine for $num2
$assegnazione_app = risul_query($prenota_in_var,$num1,'assegnazioneapp');
$checkin = risul_query($prenota_in_var,$num1,'checkin');
if ($inizio_prenota_id[$idprenota] > $limiti_var['idperiodocorrente'] and $assegnazione_app != "k" and $limiti_var['tutti_fissi'] != "SI" and !$checkin) {
$num_persone = risul_query($prenota_in_var,$num1,'num_persone');
$app_assegnabili = risul_query($prenota_in_var,$num1,'app_assegnabili');
if (!$num_persone or $num_persone <= $dati_app['minocc']) {
if ($assegnazione_app == "v") $app_assegnabili_id[$idprenota] = "v";
if ($assegnazione_app == "c") $app_assegnabili_id[$idprenota] = $app_assegnabili;
} # fine if (!$num_persone or $num_persone <= $dati_app['minocc'])
else {
if ($assegnazione_app == "v") $lista_app = $dati_app['lista'];
if ($assegnazione_app == "c") $lista_app = ",".$app_assegnabili.",";
$app_in_lista = explode (",", $lista_app);
$num_app_in_lista = count($app_in_lista) - 1;
for ($num2 = 1 ; $num2 < $num_app_in_lista ; $num2++) {
if ($dati_app['maxocc'][$app_in_lista[$num2]] and $dati_app['maxocc'][$app_in_lista[$num2]] < $num_persone) {
$lista_app = str_replace(",".$app_in_lista[$num2].",",",",$lista_app);
} # fine if ($dati_app['maxocc'][$app_in_lista[$num2]] and $dati_app['maxocc'][$app_in_lista[$num2]] < $num_persone)
} # fine for $num2
$lista_app = substr($lista_app,1,-1);
$app_assegnabili_id[$idprenota] = $lista_app;
} # fine else if (!$num_persone or $num_persone <= $dati_app[minocc])
} # fine if ($inizio_prenota_id[$idprenota] > $limiti_var['idperiodocorrente'] and $assegnazione_app != "k" and...
if ($debug == "on") echo "Aggiunta prenota $idprenota dal ".$inizio_prenota_id[$idprenota]." al ".$fine_prenota_id[$idprenota]." in ".$app_prenota_id[$idprenota]." assegnabile in ".$app_assegnabili_id[$idprenota]."<br>";
} # fine if (strstr($dati_app['lista'],",".$app_prenota.","))
} # fine if (!$app_prenota_id[$idprenota] and $ins_prenota)
} # fine for $num1
} # fine for $num_rip
if ($limiti_var['n_ini'] < $limiti_var['ini']) $limiti_var['ini'] = $limiti_var['n_ini'];
if ($limiti_var['n_fine'] > $limiti_var['fine']) $limiti_var['fine'] = $limiti_var['n_fine'];

} # fine function tab_a_var




# Function per trovare tutte le prenotazioni in un appartamento in un certo periodo
function prenota_in_app_e_periodo (&$app,&$ini_periodo,&$fine_periodo,&$prenota_in_app_sett,&$fine_prenota_id,&$num_pp) {
$num_pp = 0;
for ($num1 = $ini_periodo ; $num1 <= $fine_periodo ; $num1++) {
if ($prenota_in_app_sett[$app][$num1]) {
$num_pp++;
$prenota_presente[$num_pp] = $prenota_in_app_sett[$app][$num1];
$num1 = $fine_prenota_id[$prenota_in_app_sett[$app][$num1]];
} # fine if ($prenota_in_app_sett[$app][$num1])
} # fine for $num1
return $prenota_presente;
} # fine function prenota_in_app_e_periodo




function inserisci_prenota_fittizie ($info_periodi,&$profondita,&$app_prenota_id,&$inizio_prenota_id,&$fine_prenota_id,&$prenota_in_app_sett,&$app_assegnabili_id) {
for ($num1 = 0 ; $num1 < $info_periodi['numero'] ; $num1++) {
$id_app_periodo = $info_periodi['app'][$num1];
$idinizio_periodo = $info_periodi['ini'][$num1];
$idfine_periodo = $info_periodi['fine'][$num1];
$idinizio_prenota_falsa = $idinizio_periodo;
$prenota_falsa_da_inserire = "NO";
for ($num2 = $idinizio_periodo ; $num2 <= ($idfine_periodo + 1) ; $num2++) {
$prenota_presente = prenota_in_app_e_periodo($id_app_periodo,$num2,$num2,$prenota_in_app_sett,$fine_prenota_id,$num_pp);
if ($num_pp or $num2 == ($idfine_periodo + 1)) {
if ($prenota_falsa_da_inserire == "SI") {
$profondita['tot_prenota_attuale']++;
$app_prenota_id[$profondita['tot_prenota_attuale']] = $id_app_periodo;
$inizio_prenota_id[$profondita['tot_prenota_attuale']] = $idinizio_prenota_falsa;
$fine_prenota_id[$profondita['tot_prenota_attuale']] = $idfine_prenota_falsa;
for ($num3 = $idinizio_prenota_falsa ; $num3 <= $idfine_prenota_falsa ; $num3++) {
$prenota_in_app_sett[$id_app_periodo][$num3] = $profondita['tot_prenota_attuale'];
} # fine for $num3
} # fine if ($prenota_falsa_da_inserire == "SI")
$prenota_falsa_da_inserire = "NO";
$idinizio_prenota_falsa = $num2 + 1;
if ($num2 != ($idfine_periodo + 1)) {
reset($prenota_presente);
for ($num3 = 1 ; $num3 <= $num_pp ; $num3++) $app_assegnabili_id[$prenota_presente[$num3]] = "";
} # fine if ($num2 != ($idfine_periodo + 1))
} # fine if ($num_pp or $num2 == ($idfine_periodo + 1))
else {
$prenota_falsa_da_inserire = "SI";
$idfine_prenota_falsa = $num2;
} # fine else if ($num_pp or $num2 == ($idfine_periodo + 1))
} # fine for $num2
} # fine for $num1
} # fine function inserisci_prenota_fittizie




function incrocia_app_richiesti ($lista_app1,$lista_app2) {
if ((string) $lista_app1 != "" and (string) $lista_app2 != "") {
if ($lista_app1 == ",tutti,") $lista_app = $lista_app2;
if ($lista_app2 == ",tutti,") $lista_app = $lista_app1;
if ($lista_app1 != ",tutti," and $lista_app2 != ",tutti,") {
$lista_app1 = explode(",",$lista_app1);
$num_la1 = count($lista_app1);
for ($num1 = 0 ; $num1 < $num_la1 ; $num1++) {
if (str_replace(",".$lista_app1[$num1].",","",",".$lista_app2.",") != ",".$lista_app2.",") $lista_app .= $lista_app1[$num1].",";
} # fine for $num1
$lista_app = substr($lista_app,0,-1);
} # fine ($lista_app1 != ",tutti," and $lista_app2 != ",tutti,")
} # fine ((string) $lista_app1 != "" and (string) $lista_app2 != "")
return $lista_app;
} # fine function incrocia_app_richiesti




if (!function_exists('array_keys')) {
function array_keys($arr,$term="") {
$t = array();
foreach ($arr as $k => $v) {
if ($term && $v != $term) continue;
$t[] = $k;
} # fine foreach ($arr as $k => $v)
return $t;
} # fine function array_keys
} # fine if (!function_exists('array_keys'))




function aggiorna_tableprenota ($app_prenota_id,$app_orig_prenota_id,$nome_tableprenota) {
$fatto = 1;
if (@is_array($app_orig_prenota_id)) {
reset($app_orig_prenota_id);
foreach ($app_orig_prenota_id as $idprenota => $app_prenota) if (!strcmp($app_prenota_id[$idprenota],"")) $fatto = 0;
if ($fatto) {
reset($app_orig_prenota_id);
foreach ($app_orig_prenota_id as $idprenota => $app_prenota) {
if ($app_prenota_id[$idprenota] != $app_prenota) {
esegui_query("update $nome_tableprenota set idappartamenti = '".aggslashdb($app_prenota_id[$idprenota])."' where idprenota = '$idprenota'");
#$adesso = date("Y-M-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
#esegui_query("update $tableprenota set data_modifica = '$adesso' where idprenota = '$idprenota'");
} # fine if ($app_prenota_id[$idprenota] != $app_prenota)
} # fine foreach ($app_orig_prenota_id as $idprenota => $app_prenota)
} # fine if ($fatto)
else echo "<span class=\"colred\">ERROR in assignment engine: please report this bug to <a href=\"mailto:info@digitaldruid.net\">info@digitaldruid.net</a></span><br>";
} # fine if (@is_array($app_orig_prenota_id))
return $fatto;
} # fine function aggiorna_tableprenota




# function che controlla che gli app. richiesti non siano tutti occupati
function controlla_tutti_occupati ($idinizio,$idfine,$app_richiesti,&$prenota_in_app_sett,&$app_prenota_id,&$app_assegnabili_id,&$dati_app,&$fine_prenota_id) {
$tutti_occupati = "NO";
if (!empty($app_richiesti)) foreach ($app_richiesti as $key => $val) if ($val != "SI") unset($app_richiesti[$key]);
if (empty($app_richiesti)) $num_app_controlla_orig = $dati_app['totapp'];
else $num_app_controlla_orig = count($app_richiesti);
for ($num1 = $idinizio ; $num1 <= $idfine ; $num1++) {
$num_prenota_presenti_in_settimana = 0;
for ($num2 = 0 ; $num2 < $dati_app['totapp'] ; $num2++) {
if ($prenota_in_app_sett[$dati_app['posizione'][$num2]][$num1]) $num_prenota_presenti_in_settimana++;
} # fine for $num2
if ($num_prenota_presenti_in_settimana >= $num_app_controlla_orig) {
if (empty($app_richiesti)) { $tutti_occupati = "SI"; break; }
else {
$app_controlla = $app_richiesti;
$uno_libero = "NO";
reset($app_controlla);
foreach ($app_controlla as $key => $val) {
$prenotainperiodo = prenota_in_app_e_periodo($key,$num1,$num1,$prenota_in_app_sett,$fine_prenota_id,$num_pp);
if (!$num_pp) { $uno_libero = "SI"; break; }
$app_assegnabili_prenota = $app_assegnabili_id[$prenotainperiodo[1]];
if ($app_assegnabili_prenota == "v") {
if ($num_prenota_presenti_in_settimana < $dati_app['totapp']) $uno_libero = "SI";
break;
} # fine if ($app_assegnabili_prenota == "v")
if ($app_assegnabili_prenota) {
$app_assegnabili_prenota = explode (",", $app_assegnabili_prenota);
$n_app_assegnabili_prenota = count($app_assegnabili_prenota);
for ($num2 = 0 ; $num2 < $n_app_assegnabili_prenota ; $num2++) if (!$app_controlla[$app_assegnabili_prenota[$num2]]) $app_controlla[$app_assegnabili_prenota[$num2]] = "SI";
} # fine if ($app_assegnabili_prenota)
} # fine foreach ($app_controlla as $key => $val)
if ($uno_libero == "NO") { $tutti_occupati = "SI"; break; }
} # fine else if (empty($app_richiesti))
} # fine if ($num_prenota_presenti_in_settimana >= $num_app_controlla_orig)
} # fine for $num1
return $tutti_occupati;
} # fine function controlla_tutti_occupati




# function che trova tutte le prenotazioni in un periodo
function lista_prenota_periodo ($idinizio,$idfine,&$dati_app,&$prenota_in_app_sett,&$pren_pres_in_lista,&$lista_prenota_periodo,&$num_lista_pren_per) {
for ($num1 = $idinizio ; $num1 <= $idfine ; $num1++) {
for ($num2 = 0 ; $num2 < $dati_app['totapp'] ; $num2++) {
$pren_in_sett = $prenota_in_app_sett[$dati_app['posizione'][$num2]][$num1];
if ($pren_in_sett and !$pren_pres_in_lista[$pren_in_sett]) {
$pren_pres_in_lista[$pren_in_sett] = 1;
$lista_prenota_periodo[$num_lista_pren_per] = $pren_in_sett;
$num_lista_pren_per++;
} # fine if ($pren_in_sett and !$pren_pres_in_lista[$pren_in_sett])
} # fine for $num2
} # fine for $num1
} # fine function lista_prenota_periodo




# function che cancella le prenotazioni vicine e prepara le variabili per liberasettimane
function cancella_prenota_compagne ($idprenota_comp,$num_idprenota_comp,&$idinizioprenota,&$idfineprenota,&$app_richiesti,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$app_prenota_id,&$prenota_in_app_sett) {
$id_ric = 1;
$idinizioprenota_vett = array();
$idfineprenota_vett = array();
$idinizioprenota_vett[$id_ric] = $idinizioprenota;
$idfineprenota_vett[$id_ric] = $idfineprenota;
$idinizioprenota = $idinizioprenota_vett;
$idfineprenota = $idfineprenota_vett;
$app_richiesti_vett = array();
if (empty($app_richiesti)) $app_richiesti_vett[$id_ric] = ",tutti,";
else {
foreach ($app_richiesti as $key => $val) if ($val == "SI") $app_richiesti_vett[$id_ric] .= $key.",";
$app_richiesti_vett[$id_ric] = substr($app_richiesti_vett[$id_ric],0,-1);
} # fine else if (empty($app_richiesti)) 
$app_richiesti = $app_richiesti_vett;
$app_richiesti[',vicini,'] = "SI";
$app_richiesti[',numero,'] = $num_idprenota_comp + 1;
for ($num1 = 0 ; $num1 < $num_idprenota_comp ; $num1++) {
$id_comp = $idprenota_comp[$num1];
if ($fine_prenota_id[$id_comp]) {
$id_ric++;
$idinizioprenota[$id_ric] = $inizio_prenota_id[$id_comp];
$idfineprenota[$id_ric] = $fine_prenota_id[$id_comp];
$app_richiesti[$id_ric] = $app_assegnabili_id[$id_comp];
if (!$app_assegnabili_id[$id_comp]) $app_richiesti[$id_ric] = $app_prenota_id[$id_comp];
if ($app_assegnabili_id[$id_comp] == "v") $app_richiesti[$id_ric] = ",tutti,";
for ($num2 = $idinizioprenota[$id_ric] ; $num2 <= $idfineprenota[$id_ric] ; $num2++) {
$prenota_in_app_sett[$app_prenota_id[$id_comp]][$num2] = "";
} # fine for $num2
#$app_prenota_id[$id_comp] = "";
} # fine if ($fine_prenota_id[$id_comp])
} # fine for $num1
} # fine function cancella_prenota_compagne




function ripristina_prenota_compagne ($idprenota_comp,$num_idprenota_comp,&$idinizioprenota,&$idfineprenota,&$appartamento,&$app_prenota_id,&$prenota_in_app_sett,$fine_prenota_id,$profondita) {
global $debug;
for ($num1 = 0 ; $num1 < $num_idprenota_comp ; $num1++) {
$id_comp = $idprenota_comp[$num1];
if ($fine_prenota_id[$id_comp]) {
$id_ric = $num1 + 2;
$app_prenota_id[$id_comp] = $appartamento[$id_ric];
if ($debug == "on") {
echo "&nbsp;";
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> spostata pren $id_comp in  ".$appartamento[$id_ric]."<br>";
} # fine if ($debug == "on")
for ($num2 = $idinizioprenota[$id_ric] ; $num2 <= $idfineprenota[$id_ric] ; $num2++) {
$prenota_in_app_sett[$appartamento[$id_ric]][$num2] = $id_comp;
} # fine for $num2
} # fine if ($fine_prenota_id[$id_comp])
} # fine for $num1
$idinizioprenota = $idinizioprenota[1];
$idfineprenota = $idfineprenota[1];
$appartamento = $appartamento[1];
} # fine function ripristina_prenota_compagne




function aggiorna_app_aggiunti (&$limiti_var,&$limiti_var_orig,&$app_orig_prenota_id,&$app_prenota_id,&$inizio_prenota_id,&$fine_prenota_id,&$prenota_in_app_sett) {
if ($limiti_var['ini'] < $limiti_var_orig['ini'] or $limiti_var['fine'] > $limiti_var_orig['fine']) {
if (@is_array($app_orig_prenota_id)) {
reset($app_orig_prenota_id);
foreach ($app_orig_prenota_id as $idp => $app) {
if (!strcmp($app_prenota_id[$idp],"")) {
$app_prenota_id[$idp] = $app;
for ($num1 = $inizio_prenota_id[$idp] ; $num1 <= $fine_prenota_id[$idp] ; $num1++) {
$prenota_in_app_sett[$app][$num1] = $idp;
} # fine for $num1
} # fine if (!strcmp($app_prenota_id[$key2],""))
} # fine foreach ($app_orig_prenota_id as $idp => $app)
} # fine if (@is_array($app_orig_prenota_id))
} # fine if ($limiti_var['ini'] < $limiti_var_orig['ini'] or...
} # fine function aggiorna_app_aggiunti




# Struttura delle variabili sulle prenotazioni create con tab_a_var() :
#
# $prenota_in_app_sett[id_app][id_settimana] = id_prenota ("" se vuoto)
# $inizio_prenota_id[id_prenota] = "id_settimana"
# $fine_prenota_id[id_prenota] = "id_settimana"
# $app_prenota_id[id_prenota] = "id_app"
# $app_assegnabili_id[id_prenota] = {"id_app,id_app,...,id_app" | "v" | ""} (app. fisso se vuoto)
# $app_assegnabili_id[0][id_prenota] = {"id_prenota,...,id_prenota" | ""} (nessuna prenota compagna se vuoto)
#
# Aggiunto dopo fuori da tab_a_var() :
# $app_assegnabili_id[0][0][id_richiesto] = "id_app,...,id_app" (copia originale app_richiesti)





#
# LIBERASETTIMANE: function ricorsiva per l'assegnazione degli appartamenti.
#
function liberasettimane ($idinizio,$idfine,&$limiti_var,$anno,&$fatto,&$appartamento,$profondita,$app_richiesti,&$app_prenota_id,&$app_orig_prenota_id,&$inizio_prenota_id,&$fine_prenota_id,&$app_assegnabili_id,&$prenota_in_app_sett,&$dati_app,$nome_tab_prenota = "prenota") {
global $debug;

if (@is_array($app_richiesti) and $app_richiesti[',numero,']) {
$app_richiesti_vett = $app_richiesti;
$idinizio_vett = $idinizio;
$idfine_vett = $idfine;
if (@is_array($appartamento)) $appartamento_vett = $appartamento;
if (!$app_richiesti_vett['id']) {
$app_richiesti_vett['id'] = 1;
$limiti_var['n_ini'] = "";
$limiti_var['n_fine'] = "";
for ($num1 = 1 ; $num1 <= $app_richiesti_vett[',numero,'] ; $num1++) {
if (!$limiti_var['n_ini'] or $limiti_var['n_ini'] > $idinizio_vett[$num1]) $limiti_var['n_ini'] = $idinizio_vett[$num1];
if (!$limiti_var['n_fine'] or $limiti_var['n_fine'] < $idfine_vett[$num1]) $limiti_var['n_fine'] = $idfine_vett[$num1];
} # fine for $num1
} # fine if (!$app_richiesti_vett['id'])
else {
$app_richiesti_vett['id']++;
$limiti_var['n_ini'] = $idinizio_vett[$app_richiesti_vett['id']];
$limiti_var['n_fine'] = $idfine_vett[$app_richiesti_vett['id']];
} # fine else if (!$app_richiesti_vett['id'])
unset($app_richiesti);
if ($app_richiesti_vett[$app_richiesti_vett['id']] != ",tutti,") {
$vett_app = explode(",",$app_richiesti_vett[$app_richiesti_vett['id']]);
$n_vett_app = count($vett_app);
for ($num1 = 0 ; $num1 < $n_vett_app ; $num1++) $app_richiesti[$vett_app[$num1]] = "SI";
} # fine if ($app_richiesti_vett[$app_richiesti_vett[id]] != ",tutti,")
$idinizio = $idinizio_vett[$app_richiesti_vett['id']];
$idfine = $idfine_vett[$app_richiesti_vett['id']];
$appartamento = "";
} # fine if (@is_array($app_richiesti) and $app_richiesti[',numero,'])
else {
$limiti_var['n_ini'] = $idinizio;
$limiti_var['n_fine'] = $idfine;
} # fine else if (@is_array($app_richiesti) and $app_richiesti[',numero,'])

if (!@is_array($profondita)) {
$primo_ciclo = "SI";
$prof_copia = $profondita;
$profondita = array();
$profondita['iniziale'] = $prof_copia;
$profondita['attuale'] = $prof_copia;
$tableprenota = $nome_tab_prenota . $anno . $profondita['iniziale'];
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
$tot_prenota = risul_query($max_prenota,0,0);
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$nome_tab_prenota);
} # fine if (!@is_array($profondita))
if ($profondita['controllato_tutti_occupati'] != "SI") {
$tutti_occupati = controlla_tutti_occupati($idinizio,$idfine,$app_richiesti,$prenota_in_app_sett,$app_prenota_id,$app_assegnabili_id,$dati_app,$fine_prenota_id);
$profondita['controllato_tutti_occupati'] = "SI";
} # fine if ($profondita[controllato_tutti_occupati] != "SI")
if ($tutti_occupati == "SI") $fatto = "SI";
else $fatto = "NO";
$num_da_liberare = 1;
$num_da_liberare2 = 1;
$app_non_liberabili = ",";


# Se si cerca il primo di una serie di appartamenti vicini controllo che nel gruppo di appartamenti vicini, meno
# quelli occupati da prenotazioni fisse o assegnabili solo nel gruppo, ci sia un numero maggiore o uguale a quelli
# richiesti. Controllo anche che ci siano abbastanza appartamenti nei gruppi disponibili per i successivi gruppi
# di appartamenti richiesti, rimuovendo anche qui gli appartamenti con prenotazioni assegnabili solo nei gruppi. 
if ($app_richiesti_vett[',vicini,'] == "SI") {
if (!$app_assegnabili_id[0][0]) {
$app_assegnabili_id[0][0] = $app_richiesti_vett;
$ass00 = 1;
} # fine if (!$app_assegnabili_id[0][0])
if (!$app_richiesti_vett[',succ_non_vicino,'][$app_richiesti_vett['id']]) {
$app_controllati = ",";
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) if ($dati_app['vicini'][$numapp] == $numapp and (empty($app_richiesti) or $app_richiesti[$numapp] == "SI")) $app_non_liberabili .= "$numapp,";
if ($app_richiesti_vett['id'] == 1 or $app_richiesti_vett[',succ_non_vicino,'][($app_richiesti_vett['id'] - 1)]) {
$num_gruppo = 0;
$num_vicini_tot = 0;
$num_vicini_gruppo[0] = 1;
$idinizio_corr = $idinizio;
$idfine_corr = $idfine;
for ($num1 = ($app_richiesti_vett['id'] + 1) ; $num1 <= $app_richiesti_vett[',numero,'] ; $num1++) {
if ($idinizio_vett[$num1] < $idfine_corr and $idfine_vett[$num1] > $idinizio_corr) {
if ($idinizio_vett[$num1] > $idinizio_corr) $idinizio_corr = $idinizio_vett[$num1];
if ($idfine_vett[$num1] < $idfine_corr) $idfine_corr = $idfine_vett[$num1];
$num_vicini_gruppo[$num_gruppo]++;
} # fine if ($idinizio_vett[$num1] < $idfine_corr and $idfine_vett[$num1] > $idinizio_corr)
if ($app_richiesti_vett[',succ_non_vicino,'][$num1] and $num1 != $app_richiesti_vett[',numero,']) {
if ($num_vicini_gruppo[$num_gruppo] >= $num_vicini_gruppo[0]) $num_vicini_tot += $num_vicini_gruppo[$num_gruppo];
$num_gruppo++;
$num_vicini_gruppo[$num_gruppo] = 0;
} # fine if ($app_richiesti_vett[',succ_non_vicino,'][$num1] and $num1 != $app_richiesti_vett[',numero,'])
} # fine for $num1
if ($num_vicini_gruppo[$num_gruppo] >= $num_vicini_gruppo[0]) $num_vicini_tot += $num_vicini_gruppo[$num_gruppo];
$num_vicini = $num_vicini_gruppo[0];
$num_vicini_disp_tot = 0;
$prenota_ricontrolla_tot = array();
$app_gruppi_disp = ",";
$num_app_gruppi_disp = 0;
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if ((empty($app_richiesti) or $app_richiesti[$numapp] == "SI") and !strstr($app_controllati,",$numapp,") and !strstr($app_non_liberabili,",$numapp,")) {
for ($num2 = 0 ; $num2 < count($dati_app['grp_vicini']) ; $num2++) {
if (strstr($dati_app['grp_vicini'][$num2],",$numapp,")) {
$num_vicini_disp = $dati_app['grp_vicini_num'][$num2];
if ($num_vicini > $num_vicini_disp) $app_non_liberabili .= substr($dati_app['grp_vicini'][$num2],1);
else {
$grp_vicini = explode(",",substr($dati_app['grp_vicini'][$num2],1,-1));
for ($num3 = 0 ; $num3 < $dati_app['grp_vicini_num'][$num2] ; $num3++) {
$prenotainperiodo = prenota_in_app_e_periodo($grp_vicini[$num3],$idinizio_corr,$idfine_corr,$prenota_in_app_sett,$fine_prenota_id,$num_pp);
for ($num4 = 1 ; $num4 <= $num_pp ; $num4++) {
if (!$app_assegnabili_id[$prenotainperiodo[$num4]]) {
$num_vicini_disp--;
break;
} # fine if (!$app_assegnabili_id[$prenotainperiodo[$num4]])
elseif ($app_assegnabili_id[$prenotainperiodo[$num4]] != "v") {
$vett_assegnabili = explode(",",$app_assegnabili_id[$prenotainperiodo[$num4]]);
$num_assegnabili = count($vett_assegnabili);
if ($num_assegnabili <= $dati_app['grp_vicini_num'][$num2]) {
$contenuto = 1;
for ($num5 = 0 ; $num5 < $num_assegnabili ; $num5++) {
if (!strstr($dati_app['grp_vicini'][$num2],",".$vett_assegnabili[$num5].",")) {
$contenuto = 0;
break;
} # fine if (!strstr($dati_app['grp_vicini'][$num2],",".$vett_assegnabili[$num5].","))
} # fine for $num5
if ($contenuto) {
$num_vicini_disp--;
$prenota_ricontrolla_tot[$grp_vicini[$num3]] = "";
break;
} # fine if ($contenuto)
else $prenota_ricontrolla_tot[$grp_vicini[$num3]][$num4] = $vett_assegnabili;
} # fine if ($num_assegnabili <= $dati_app['grp_vicini_num'][$num2])
else $prenota_ricontrolla_tot[$grp_vicini[$num3]][$num4] = $vett_assegnabili;
} # fine elseif ($app_assegnabili_id[$prenotainperiodo[$num4]] != "v")
} # fine for $num4
} # fine for $num3
if ($num_vicini > $num_vicini_disp) $app_non_liberabili .= substr($dati_app['grp_vicini'][$num2],1);
else {
$num_vicini_disp_tot += $num_vicini_disp;
$app_gruppi_disp .= substr($dati_app['grp_vicini'][$num2],1);
$num_app_gruppi_disp += $dati_app['grp_vicini_num'][$num2];
} # fine else if ($num_vicini > $num_vicini_disp)
} # fine else if ($num_vicini > $num_vicini_disp)
$app_controllati .= substr($dati_app['grp_vicini'][$num2],1);
break;
} # fine if (strstr($dati_app['grp_vicini'][$num2],",$numapp,"))
} # fine for $num2
} # fine if ((empty($app_richiesti) or $app_richiesti[$numapp] == "SI") and !strstr($app_controllati,",$numapp,"))
} # fine for $num1
if ($num_vicini_tot > $num_vicini_disp_tot) $app_non_liberabili .= substr($app_gruppi_disp,1);
elseif ($num_vicini_tot > $num_vicini and $app_gruppi_disp) {
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if (@is_array($prenota_ricontrolla_tot[$numapp])) {
foreach ($prenota_ricontrolla_tot[$numapp] as $num_pp => $vett_assegnabili) {
$num_assegnabili = count($vett_assegnabili);
if ($num_assegnabili <= $num_app_gruppi_disp) {
$contenuto = 1;
for ($num2 = 0 ; $num2 < $num_assegnabili ; $num2++) {
if (!strstr($app_gruppi_disp,",".$vett_assegnabili[$num2].",")) {
$contenuto = 0;
break;
} # fine if (!strstr($dati_app['grp_vicini'][$num2],",".$vett_assegnabili[$num2].","))
} # fine for $num2
if ($contenuto) {
$num_vicini_disp_tot--;
break;
} # fine if ($contenuto)
} # fine if ($num_assegnabili <= $num_app_gruppi_disp)
} # fine foreach ($prenota_ricontrolla_tot[$numapp] as $num_pp => $vett_assegnabili)
if ($num_vicini_tot > $num_vicini_disp_tot) {
$app_non_liberabili .= substr($app_gruppi_disp,1);
break;
} # fine if ($num_vicini_tot > $num_vicini_disp_tot)
} # fine if (@is_array($prenota_ricontrolla_tot[$numapp]))
} # fine for $num1
} # fine elseif ($num_vicini_tot > $num_vicini and $app_gruppi_disp)
} # fine if ($app_richiesti_vett['id'] == 1 or $app_richiesti_vett[',succ_non_vicino,'][($app_richiesti_vett['id'] - 1)])
} # fine if (!$app_richiesti_vett[',succ_non_vicino,'][$app_richiesti_vett['id']])
} # fine if ($app_richiesti_vett[',vicini,'] == "SI")


# cicli da ripetere per ogni appartamento fino a che $fatto = SI

# primo ciclo che controlla se c'é già un appartamento libero
if ($fatto != "SI") {
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if (empty($app_richiesti) or $app_richiesti[$numapp] == "SI") {
if (!strstr($app_non_liberabili,",$numapp,")) {
$prenotainperiodo = prenota_in_app_e_periodo($numapp,$idinizio,$idfine,$prenota_in_app_sett,$fine_prenota_id,$num_pp);
if (!$num_pp) {
$appartamento = $numapp;
$fatto = "SI";
if ($debug == "on") echo "LIBERATO1 $appartamento<br>";
break;
} # fine if (!$num_pp)
} # fine if (!strstr($app_non_liberabili,",$numapp,"))
} # fine if (empty($app_richiesti) or $app_richiesti[$numapp] == "SI")
} # fine for $num1
} # fine if ($fatto != "SI")

if ($fatto != "SI") {

# allargo il periodo passato dalla tabella alle variabili se necessario
$lista_prenota_periodo = array();
$pren_pres_in_lista = "";
$num_lista_pren_per = 0;
if (!$limiti_var[s_ini]) {
lista_prenota_periodo($idinizio,$idfine,$dati_app,$prenota_in_app_sett,$pren_pres_in_lista,$lista_prenota_periodo,$num_lista_pren_per);
$limiti_var[s_ini] = $idinizio;
$limiti_var[s_fine] = $idfine;
} # fine if (!$limiti_var[s_ini])
if ($limiti_var['s_ini'] > $idinizio) {
lista_prenota_periodo($idinizio,($limiti_var['s_ini'] - 1),$dati_app,$prenota_in_app_sett,$pren_pres_in_lista,$lista_prenota_periodo,$num_lista_pren_per);
$limiti_var['s_ini'] = $idinizio;
} # fine if ($limiti_var['s_ini'] > $idinizio)
if ($limiti_var['s_fine'] < $idfine) {
lista_prenota_periodo(($limiti_var['s_fine'] + 1),$idfine,$dati_app,$prenota_in_app_sett,$pren_pres_in_lista,$lista_prenota_periodo,$num_lista_pren_per);
$limiti_var['s_fine'] = $idfine;
} # fine if ($limiti_var['s_ini'] > $idinizio)
for ($num1 = 0 ; $num1 < $num_lista_pren_per ; $num1++) {
if ($inizio_prenota_id[$lista_prenota_periodo[$num1]] < $limiti_var['n_ini']) $limiti_var['n_ini'] = $inizio_prenota_id[$lista_prenota_periodo[$num1]];
if ($fine_prenota_id[$lista_prenota_periodo[$num1]] > $limiti_var['n_fine']) $limiti_var['n_fine'] = $fine_prenota_id[$lista_prenota_periodo[$num1]];
} # fine for $num1
if ($limiti_var['n_ini'] < $limiti_var['ini'] or $limiti_var['n_fine'] > $limiti_var['fine']) {
tab_a_var ($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$nome_tab_prenota);
} # fine if ($limiti_var['n_ini'] < $limiti_var['ini'] or $limiti_var['n_fine'] > $limiti_var['fine'])

# ciclo che prova a spostare le prime prenotazioni ed eventualmente le
# mette in $da_liberare o $da_scambiare
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if (empty($app_richiesti) or $app_richiesti[$numapp] == "SI") {
$prenotainperiodo = prenota_in_app_e_periodo($numapp,$idinizio,$idfine,$prenota_in_app_sett,$fine_prenota_id,$num_pp);
$idiniziop = "";
$idfinep = "";
unset($app_assegnabili);
$mobile = "SI";
$tutti_liberabili = "SI";

# Trovo le prenotazioni del periodo per l'appartamento $numapp, controllo
# che si posssano muovere e trovo le settimane iniziale e finale, e i 
# vincoli sull'appartamento di destinazione ($num_min_persome, $numcasa e
# $num_piano).
for ($num2 = 1 ; $num2 <= $num_pp ; $num2++) {
$idprenotainperiodo = $prenotainperiodo[$num2];
if ($app_assegnabili_id[$idprenotainperiodo] and $app_assegnabili_id[$idprenotainperiodo] != "v") {
$vett_assegnabili = explode(",",$app_assegnabili_id[$idprenotainperiodo]);
$num_assegnabili = count($vett_assegnabili);
for ($num3 = 0 ; $num3 < $num_assegnabili ; $num3++) {
$app_ = $vett_assegnabili[$num3];
$app_assegnabili[$idprenotainperiodo][$app_] = "SI";
} # fine for $num3
} # fine if ($app_assegnabili_id[$idprenotainperiodo] and $app_assegnabili_id[$idprenotainperiodo] != "v")
if ($app_assegnabili_id[$idprenotainperiodo]) {
if (!$idiniziop or $idiniziop > $inizio_prenota_id[$idprenotainperiodo]) {
$idiniziop = $inizio_prenota_id[$idprenotainperiodo];
} # fine if (!$idiniziop or $idiniziop > $inizio_prenota_id[$idprenotainperiodo])
if (!$idfinep or $idfinep < $fine_prenota_id[$idprenotainperiodo]) {
$idfinep = $fine_prenota_id[$idprenotainperiodo];
} # fine if (!$idfinep or $idfinep < $fine_prenota_id[$idprenotainperiodo])
} # fine if ($app_assegnabili_id[$idprenotainperiodo])
else $mobile = "NO";
} # fine for $num2

if (strstr($app_non_liberabili,",$numapp,")) $mobile = "NO";


if ($mobile == "SI") {
# Provo a spostare le prenotazioni dell'appartamento $numapp.
$mobile2 = "SI";
$prenota_comp_presente = "NO";
#$liberabile = "";
#$scambiabile = "";
#$da_scambiare = "";
for ($num2 = 1 ; $num2 <= $num_pp ; $num2++) {
$idprenotainperiodo = $prenotainperiodo[$num2];
for ($num3 = 0 ; $num3 < $dati_app['totapp'] ; $num3++) {
if ($num3 != $num1) {
$numapp2 = $dati_app['posizione'][$num3];
if ($app_assegnabili_id[$idprenotainperiodo] == "v" or $app_assegnabili[$idprenotainperiodo][$numapp2] == "SI") {
$prenotainperiodo2 = prenota_in_app_e_periodo($numapp2,$inizio_prenota_id[$idprenotainperiodo],$fine_prenota_id[$idprenotainperiodo],$prenota_in_app_sett,$fine_prenota_id,$num_pp2);
if (!$num_pp2 and !$app_assegnabili_id[0][$idprenotainperiodo]) {
$nuovo_app[$idprenotainperiodo] = $numapp2;
$liberabile[$idprenotainperiodo] = "SI";
$scambiabile[$idprenotainperiodo] = "NO";
break;
} # fine (!$num_pp2 and...
else {
# Se non si possono spostare vedere se sono da scambiare
if ($app_assegnabili_id[0][$idprenotainperiodo]) $prenota_comp_presente = "SI";
$prenota_fissa_presente = "NO";
for ($num4 = 1 ; $num4 <= $num_pp2 ; $num4++) {
$idprenotainperiodo2 = $prenotainperiodo2[$num4];
if (!$app_assegnabili_id[$idprenotainperiodo2]) $prenota_fissa_presente = "SI";
else {
# se $idprenotainperiodo è uguale o minore di $idprenotainperiodo2 e ne contiene gli app. assegnabili è inutile fare lo scambio
if ($inizio_prenota_id[$idprenotainperiodo2] <= $inizio_prenota_id[$idprenotainperiodo] and $fine_prenota_id[$idprenotainperiodo2] >= $fine_prenota_id[$idprenotainperiodo]) {
if ($app_assegnabili_id[$idprenotainperiodo] == "v") $prenota_fissa_presente = "SI";
else {
if ($app_assegnabili_id[$idprenotainperiodo2] != "v" and !$app_assegnabili_id[0][$idprenotainperiodo]) {
$vett_assegnabili = explode (",",$app_assegnabili_id[$idprenotainperiodo2]);
$num_assegnabili = count($vett_assegnabili);
$lista_app_contenuta = "SI";
for ($num5 = 0 ; $num5 < $num_assegnabili ; $num5++) {
if ($app_assegnabili[$idprenotainperiodo][$vett_assegnabili[$num5]] != "SI") { $lista_app_contenuta = "NO"; break; }
} # fine for $num5
if ($lista_app_contenuta == "SI") $prenota_fissa_presente = "SI";
} # fine if ($app_assegnabili_id[$idprenotainperiodo2] != "v" and !$app_assegnabili_id[0][$idprenotainperiodo])
} # fine else if ($app_assegnabili_id[$idprenotainperiodo] == "v")
} # fine if ($inizio_prenota_id[$idprenotainperiodo2] <= $inizio_prenota_id[$idprenotainperiodo] and...
} # fine else if (!$app_assegnabili_id[$idprenotainperiodo2])
} # fine fine for $num4
if ($prenota_fissa_presente == "NO") {
$scambiabile[$idprenotainperiodo] = "SI";
$da_scambiare[$idprenotainperiodo][$numapp2] = "SI";
} # fine if ($prenota_fissa_presente == "NO")
} # fine else if (!$num_pp2)
} # fine if ($app_assegnabili_id[$idprenotainperiodo] == "v" or ...
} # fine if ($num3 != $num1)
} # fine for $num3

if ($liberabile[$idprenotainperiodo] != "SI") $tutti_liberabili = "NO";
if ($liberabile[$idprenotainperiodo] != "SI" and $scambiabile[$idprenotainperiodo] != "SI") $mobile2 = "NO";
} # fine for $num2

# Applico gli eventuali spostamenti, altrimenti metto i dati in $da_liberare
if ($tutti_liberabili != "NO") {
for ($num2 = 1 ; $num2 <= $num_pp ; $num2++) {
$idprenotainperiodo = $prenotainperiodo[$num2];
$n_app = $nuovo_app[$idprenotainperiodo];
for ($num3 = $inizio_prenota_id[$idprenotainperiodo] ; $num3 <= $fine_prenota_id[$idprenotainperiodo] ; $num3++) {
$prenota_in_app_sett[$numapp][$num3] = "";
$prenota_in_app_sett[$n_app][$num3] = $idprenotainperiodo;
} # fine for $num3
$app_prenota_id[$idprenotainperiodo] = $n_app;
} # fine for $num2
$appartamento = $numapp;
$fatto = "SI";
if ($debug == "on") echo "LIBERATO2 $appartamento<br>";
break;
} # fine if ($tutti_liberabili != "NO")

if ($mobile2 == "SI") {
if ($prenota_comp_presente == "NO") {
$idinizio_da_liberare[$num_da_liberare] = $idiniziop;
$idfine_da_liberare[$num_da_liberare] = $idfinep;
$app_da_liberare[$num_da_liberare] = $numapp;
$prenotainperiodo_da_liberare[$num_da_liberare] = $prenotainperiodo;
$num_pp_da_liberare[$num_da_liberare] = $num_pp;
$num_da_liberare++;
} # fine if ($prenota_comp_presente == "NO")
else {
$idinizio_da_liberare2[$num_da_liberare2] = $idiniziop;
$idfine_da_liberare2[$num_da_liberare2] = $idfinep;
$app_da_liberare2[$num_da_liberare2] = $numapp;
$prenotainperiodo_da_liberare2[$num_da_liberare2] = $prenotainperiodo;
$num_pp_da_liberare2[$num_da_liberare2] = $num_pp;
$num_da_liberare2++;
} # fine else if ($prenota_comp_presente == "NO")
} # fine if ($mobile2 == "SI")
else $riprova_app[$numapp] = "NO";

if ($fatto == "SI") { break; }
} # fine if ($mobile == "SI")
else $riprova_app[$numapp] = "NO";
} # fine if (empty($app_richiesti) or $app_richiesti[$numapp] == "SI")
} # fine for $num1

} # fine if ($fatto != "SI")




# Se non è ancora $fatto chiamo ricorsivamente liberasettimane con i dati
# in $da_liberare e $da_scambiare, operando su una variabile temporanea.
if ($fatto == "NO" and $limiti_var['t_limite'] >= time()) {
$nuova_profondita = $profondita;
$nuova_profondita['attuale'] = $profondita['attuale'] + 1;
$num_da_lib_no_comp = $num_da_liberare;
for ($num1 = 1 ; $num1 < $num_da_liberare2 ; $num1++) {
$idinizio_da_liberare[$num_da_liberare] = $idinizio_da_liberare2[$num1];
$idfine_da_liberare[$num_da_liberare] = $idfine_da_liberare2[$num1];
$app_da_liberare[$num_da_liberare] = $app_da_liberare2[$num1];
$prenotainperiodo_da_liberare[$num_da_liberare] = $prenotainperiodo_da_liberare2[$num1];
$num_pp_da_liberare[$num_da_liberare] = $num_pp_da_liberare2[$num1];
$num_da_liberare++;
} # fine for $num1

for ($num1 = 1 ; $num1 < $num_da_liberare ; $num1++) {
if ($app_da_liberare[$num1] != ",,NO") {
$n_app_prenota_id = $app_prenota_id;
$n_prenota_in_app_sett = $prenota_in_app_sett;
$nuova_profondita['tot_prenota_attuale'] = $profondita['tot_prenota_attuale'];
$idiniziop = $idinizio_da_liberare[$num1];
$idfinep = $idfine_da_liberare[$num1];
$numapp = $app_da_liberare[$num1];
$prenotainperiodo = $prenotainperiodo_da_liberare[$num1];
$num_pp = $num_pp_da_liberare[$num1];

# Muovo le prenotazioni liberabili
for ($num2 = 1 ; $num2 <= $num_pp ; $num2++) {
$idprenotainperiodo = $prenotainperiodo[$num2];
if ($liberabile[$idprenotainperiodo] == "SI") {
$n_app = $nuovo_app[$idprenotainperiodo];
for ($num3 = $inizio_prenota_id[$idprenotainperiodo] ; $num3 <= $fine_prenota_id[$idprenotainperiodo] ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = "";
$n_prenota_in_app_sett[$n_app][$num3] = $idprenotainperiodo;
} # fine for $num3
$n_app_prenota_id[$idprenotainperiodo] = $n_app;
} # fine if ($liberabile[$idpenotainperiodo] == "SI")
} # fine for $num2

# Riempio gli spazi vuoti con prenotazioni kostanti.
$idinizio_prenota_falsa = $idinizio;
$prenota_falsa_da_inserire = "NO";
for ($num2 = $idinizio ; $num2 <= ($idfine+1) ; $num2++) {
if ($n_prenota_in_app_sett[$numapp][$num2] or $num2 == ($idfine+1)) {
if ($prenota_falsa_da_inserire == "SI") {
$nuova_profondita['tot_prenota_attuale']++;
$n_app_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $numapp;
$inizio_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $idinizio_prenota_falsa;
$fine_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $idfine_prenota_falsa;
for ($num3 = $idinizio_prenota_falsa ; $num3 <= $idfine_prenota_falsa ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = $nuova_profondita['tot_prenota_attuale'];
} # fine for $num3
} # fine if ($prenota_falsa_da_inserire == "SI")
$prenota_falsa_da_inserire = "NO";
$idinizio_prenota_falsa = $num2 + 1;
} # fine if ($n_prenota_in_app_sett[$numapp][$num2] or $num2 == ($idfine+1))
else {
$prenota_falsa_da_inserire = "SI";
$idfine_prenota_falsa = $num2;
} # fine else if ($n_prenota_in_app_sett[$numapp][$num2] or $num2 == ($idfine+1))
} # fine for $num2


#chiamo ricorsivamente liberasettimane con gli app. $da_scambiare
$fatto3 = "SI";
$limiti_var_orig = $limiti_var;
$num_pp_s = $num_pp;
$prenotainperiodo_s = $prenotainperiodo;
$scambiabile_s = $scambiabile;
unset($prenotainperiodo_agg);
for ($num2 = 1 ; $num2 <= $num_pp_s ; $num2++) {
$idprenotainperiodo = $prenotainperiodo_s[$num2];
if ($scambiabile_s[$idprenotainperiodo] == "SI") {
$idinizioprenota2 = $inizio_prenota_id[$idprenotainperiodo];
$idfineprenota2 = $fine_prenota_id[$idprenotainperiodo];
if ($idinizioprenota2 > $idinizio) { $idinizio_rimpicciolito = $idinizioprenota2; }
else { $idinizio_rimpicciolito = $idinizio; }
if ($idfineprenota2 < $idfine) { $idfine_rimpicciolito = $idfineprenota2; }
else { $idfine_rimpicciolito = $idfine; }

# Se la prenotazione scambiabile che c'era prima è stata spostata in un altro appartamento
# riempio gli spazi vuoti ed aggiungo eventuali nuove prenotazioni a $prenotainperiodo_s
if ($numapp != $n_app_prenota_id[$idprenotainperiodo]) {
$idinizio_prenota_falsa = $idinizio_rimpicciolito;
$prenota_falsa_da_inserire = "NO";
for ($num3 = $idinizio_rimpicciolito ; $num3 <= ($idfine_rimpicciolito + 1) ; $num3++) {
if ($n_prenota_in_app_sett[$numapp][$num3] and !$prenotainperiodo_agg[$n_prenota_in_app_sett[$numapp][$num3]] and $num3 != ($idfine_rimpicciolito + 1)) {
$prenotainperiodo_agg[$n_prenota_in_app_sett[$numapp][$num3]] = 1;
$num_pp_s++;
$prenotainperiodo_s[$num_pp_s] = $n_prenota_in_app_sett[$numapp][$num3];
$scambiabile_s[$n_prenota_in_app_sett[$numapp][$num3]] = "SI";
} # fine ($n_prenota_in_app_sett[$numapp][$num3] and !$prenotainperiodo_agg[$n_prenota_in_app_sett[$numapp][$num3]] and...
if ($n_prenota_in_app_sett[$numapp][$num3] or $num3 == ($idfine_rimpicciolito + 1)) {
if ($prenota_falsa_da_inserire == "SI") {
$nuova_profondita['tot_prenota_attuale']++;
$n_app_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $numapp;
$inizio_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $idinizio_prenota_falsa;
$fine_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $idfine_prenota_falsa;
for ($num4 = $idinizio_prenota_falsa ; $num4 <= $idfine_prenota_falsa ; $num4++) {
$n_prenota_in_app_sett[$numapp][$num4] = $nuova_profondita['tot_prenota_attuale'];
} # fine for $num4
} # fine if ($prenota_falsa_da_inserire == "SI")
$prenota_falsa_da_inserire = "NO";
$idinizio_prenota_falsa = $num3 + 1;
} # fine if ($n_prenota_in_app_sett[$numapp][$num2] or $num2 == ($idfine+1))
else {
$prenota_falsa_da_inserire = "SI";
$idfine_prenota_falsa = $num3;
} # fine else if ($n_prenota_in_app_sett[$numapp][$num2] or $num2 == ($idfine+1))
} # fine for $num3
} # fine if ($numapp != $n_app_prenota_id[$idprenotainperiodo])

else {
$inizio_prenota_id[$idprenotainperiodo] = $idinizio_rimpicciolito;
$fine_prenota_id[$idprenotainperiodo] = $idfine_rimpicciolito;
for ($num3 = $idinizioprenota2 ; $num3 < $idinizio_rimpicciolito ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = "";
} # fine for $num3
for ($num3 = ($idfine_rimpicciolito + 1) ; $num3 <= $idfineprenota2 ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = "";
} # fine for $num3
$app_assegnabili_id2 = $app_assegnabili_id[$idprenotainperiodo];
$app_assegnabili_id[$idprenotainperiodo] = "";
$ap_ric = "";
for ($num3 = 0 ; $num3 < $dati_app['totapp'] ; $num3++) {
$numapp2 = $dati_app['posizione'][$num3];
if ($da_scambiare[$idprenotainperiodo][$numapp2] == "SI") {
$ap_ric[$numapp2] = "SI";
} # fine if ($da_scambiare[$idprenotainperiodo][$numapp2] == "SI")
} # fine for $num3

# Se la prenotazione da scambiare ne ha altre compagne
if ($app_assegnabili_id[0][$idprenotainperiodo]) {
$idprenota_comp = explode(",",$app_assegnabili_id[0][$idprenotainperiodo]);
$num_idprenota_comp = count($idprenota_comp);
cancella_prenota_compagne($idprenota_comp,$num_idprenota_comp,$idinizioprenota2,$idfineprenota2,$ap_ric,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$n_app_prenota_id,$n_prenota_in_app_sett);
$app_richiesti_orig = $app_assegnabili_id[0][0];
$app_assegnabili_id[0][0] = "";
} # fine if ($app_assegnabili_id[0][$idprenotainperiodo])

$appartamento2 = "";
$fatto2 = "";
if ($debug == "on") {
global $passo;
$passo++;
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
if ($ap_ric[',numero,']) {
$ap_ric_ved = $ap_ric[1]." (pren $idprenotainperiodo + ".$app_assegnabili_id[0][$idprenotainperiodo].")";
$idinizioprenota2_ved = $idinizioprenota2[1];
$idfineprenota2_ved = $idfineprenota2[1];
} # fine if ($ap_ric[',numero,']) 
else {
$ap_ric_ved = implode(",",array_keys($ap_ric))." (pren $idprenotainperiodo)";;
$idinizioprenota2_ved = $idinizioprenota2;
$idfineprenota2_ved = $idfineprenota2;
} # fine else if ($ap_ric[',numero,']) 
echo "<b>".$profondita['attuale']."</b> <em>".date("H:i:s")."</em> libera il $numapp da $idinizioprenota2_ved a $idfineprenota2_ved negli app $ap_ric_ved<br>";
} # fine if ($debug == "on")
liberasettimane($idinizioprenota2,$idfineprenota2,$limiti_var,$anno,$fatto2,$appartamento2,$nuova_profondita,$ap_ric,$n_app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$n_prenota_in_app_sett,$dati_app,$nome_tab_prenota);
if ($debug == "on") {
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> finito <em>".date("H:i:s")."</em> $fatto2 (app $appartamento2)<br>";
} # fine if ($debug == "on")

if ($app_assegnabili_id[0][$idprenotainperiodo]) {
$inizio_prenota_id[$idprenotainperiodo] = $idinizioprenota2[1];
$fine_prenota_id[$idprenotainperiodo] = $idfineprenota2[1];
if ($fatto2 == "SI") ripristina_prenota_compagne($idprenota_comp,$num_idprenota_comp,$idinizioprenota2,$idfineprenota2,$appartamento2,$n_app_prenota_id,$n_prenota_in_app_sett,$fine_prenota_id,$profondita);
$app_assegnabili_id[0][0] = $app_richiesti_orig;
} # fine if ($app_assegnabili_id[0][$idprenotainperiodo])
else {
$inizio_prenota_id[$idprenotainperiodo] = $idinizioprenota2;
$fine_prenota_id[$idprenotainperiodo] = $idfineprenota2;
} # fine else if ($app_assegnabili_id[0][$idprenotainperiodo])
$app_assegnabili_id[$idprenotainperiodo] = $app_assegnabili_id2;

if ($fatto2 == "SI") {
for ($num3 = $idinizio_rimpicciolito ; $num3 <= $idfine_rimpicciolito ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = "";
} # fine for $num3
for ($num3 = $idinizioprenota2 ; $num3 <= $idfineprenota2 ; $num3++) {
$n_prenota_in_app_sett[$appartamento2][$num3] = $idprenotainperiodo;
} # fine for $num3
$n_app_prenota_id[$idprenotainperiodo] = $appartamento2;
if ($debug == "on") {
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> spostata pren $idprenotainperiodo in ".$appartamento2."<br>";
} # fine if ($debug == "on")
$nuova_profondita['tot_prenota_attuale']++;
$n_app_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $numapp;
$inizio_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $idinizio_rimpicciolito;
$fine_prenota_id[$nuova_profondita['tot_prenota_attuale']] = $idfine_rimpicciolito;
for ($num3 = $idinizio_rimpicciolito ; $num3 <= $idfine_rimpicciolito ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = $nuova_profondita['tot_prenota_attuale'];
} # fine for $num3
} # fine if ($fatto2 == "SI")
else {
$fatto3 = "NO";
aggiorna_app_aggiunti($limiti_var,$limiti_var_orig,$app_orig_prenota_id,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett);
break;
} # fine else if ($fatto2 == "SI")
} # fine else if ($numapp != $n_app_prenota_id[$idprenotainperiodo])
} # fine if ($scambiabile_s[$idprenotainperiodo] == "SI")
} # fine for $num2


# cancello le prenotazioni kostanti
for ($num2 = ($profondita['tot_prenota_attuale'] + 1) ; $num2 <= $nuova_profondita['tot_prenota_attuale'] ; $num2++) {
for ($num3 = $inizio_prenota_id[$num2] ; $num3 <= $fine_prenota_id[$num2] ; $num3++) {
$n_prenota_in_app_sett[$numapp][$num3] = "";
} # fine for $num3
$n_app_prenota_id[$num2] = "";
$inizio_prenota_id[$num2] = "";
$fine_prenota_id[$num2] = "";
} # fine for $num2

if ($fatto3 == "SI") {
$app_prenota_id = $n_app_prenota_id;
$prenota_in_app_sett = $n_prenota_in_app_sett;
$fatto = "SI";
$appartamento = $numapp;
break;
} # fine if ($fatto3 == "SI")
else $riprova_app[$numapp] = "NO";

# Elimino gli appartamenti successivi che si possono scambiare con questo
if ($num1 != ($num_da_liberare - 1) and $num1 < $num_da_lib_no_comp) {
$lista_app_assegnabili = "";
for ($num2 = 1 ; $num2 <= $num_pp ; $num2++) {
$idprenotainperiodo = $prenotainperiodo[$num2];
if (!$lista_app_assegnabili or $lista_app_assegnabili == "v") $lista_app_assegnabili = $app_assegnabili_id[$idprenotainperiodo];
else {
$app_ass_num2 = $app_assegnabili_id[$idprenotainperiodo];
if ($app_ass_num2 != "v" and $app_ass_num2 != $lista_app_assegnabili) {
$app_ass_num2 = explode(",",$app_ass_num2);
$num_app_ass_num2 = (count($app_ass_num2) - 1);
$n_lista_app_assegnabili = "";
for ($num3 = 1 ; $num3 < $num_app_ass_num2 ; $num3++) {
if (str_replace(",".$app_ass_num2[$num3].",","",$lista_app_assegnabili) != $lista_app_assegnabili) $n_lista_app_assegnabili .= ",".$app_ass_num2[$num3];
} # fine for $num3
$lista_app_assegnabili = $n_lista_app_assegnabili;
if (!$lista_app_assegnabili) break;
else $lista_app_assegnabili .= ",";
} # fine if ($app_ass_num2 != "v" and $app_ass_num2 != $lista_app_assegnabili)
} # fine else if (!$lista_app_assegnabili or...
} # fine for $num2
if ($lista_app_assegnabili) {
for ($num2 = ($num1 + 1) ; $num2 < $num_da_liberare ; $num2++) {
$numapp2 = $app_da_liberare[$num2];
if (($lista_app_assegnabili == "v" or str_replace(",$numapp2,","",$lista_app_assegnabili) != $lista_app_assegnabili) and $numapp2 != ",,NO") {
if ($prenota_in_app_sett[$numapp2][$idiniziop]) $ini_blocco2 = $inizio_prenota_id[$prenota_in_app_sett[$numapp2][$idiniziop]];
else $ini_blocco2 = $idiniziop;
if ($prenota_in_app_sett[$numapp2][$idfinep]) $fine_blocco2 = $fine_prenota_id[$prenota_in_app_sett[$numapp2][$idfinep]];
else $fine_blocco2 = $idfinep;
if ($ini_blocco2 == $idiniziop and $fine_blocco2 == $idfinep) {
$scambiabili = "SI";
for ($num3 = $idiniziop ; $num3 <= $idfinep ; $num3++) {
if ($prenota_in_app_sett[$numapp2][$num3]) {
$app_ass_num3 = $app_assegnabili_id[$prenota_in_app_sett[$numapp2][$num3]];
if ($app_ass_num3 != "v" and str_replace(",$numapp,","",$app_ass_num3) == $app_ass_num3) {
$scambiabili = "NO";
break;
} # fine if ($app_ass_num5 != "v" and...
$num3 = $fine_prenota_id[$prenota_in_app_sett[$numapp2][$num3]];
} # fine if ($prenota_in_app_sett[$numapp3][$num3])
} # fine for $num3
if ($scambiabili == "SI") {
$app_da_liberare[$num2] = ",,NO";
$riprova_app[$numapp2] = "NO";
} # fine if ($scambiabili == "SI")
} # fine if ($ini_blocco2 == $ini_blocco1 and...
} # fine if (($lista_app_assegnabili == "v" or...
} # fine for $num2
} # fine if ($lista_app_assegnabili)
} # fine if ($num1 != ($num_da_liberare - 1) and...

} # fine if ($app_da_liberare[$num1] != ",,NO")
} # fine for $num1

} # fine if ($fatto == "NO")



if ($tutti_occupati == "SI") $fatto = "NO";


# Se bisogna liberare altri appartamenti oltre a quello appena liberato
if ($fatto == "SI" and $app_richiesti_vett[',numero,']) {
if ($app_richiesti_vett[',numero,'] > $app_richiesti_vett['id']) {
$nuova_profondita = $profondita;
$nuova_profondita['attuale']++;
$nuova_profondita['controllato_tutti_occupati'] = "NO";
$n_app_prenota_id = $app_prenota_id;
$n_prenota_in_app_sett = $prenota_in_app_sett;
$nuova_profondita['tot_prenota_attuale']++;
$id_prenota_lib = $nuova_profondita['tot_prenota_attuale'];
if ($debug == "on") echo "inserita ".$nuova_profondita['tot_prenota_attuale']." in $appartamento da $idinizio a $idfine<br>";
$n_app_prenota_id[$id_prenota_lib] = $appartamento;
$inizio_prenota_id[$id_prenota_lib] = $idinizio;
$fine_prenota_id[$id_prenota_lib] = $idfine;
for ($num1 = $idinizio ; $num1 <= $idfine ; $num1++) {
$n_prenota_in_app_sett[$appartamento][$num1] = $id_prenota_lib;
} # fine for $num1

# Se gli app devono essere vicini mantengo l'app liberato fisso e provo i restanti da liberare
if ($app_richiesti_vett[',vicini,'] == "SI" and !$app_richiesti_vett[',succ_non_vicino,'][$app_richiesti_vett['id']]) {
$fatto = "NO";
$lim_for = $app_richiesti_vett[',numero,'];
if ($lim_for != ($app_richiesti_vett['id'] + 1) and $limiti_var['t_limite'] < time()) {
$lim_for = ($app_richiesti_vett['id'] + 1);
if (($limiti_var['t_limite'] + 20) < time()) $lim_for = 0;
if ($debug == "on") echo "timeout<br>";
} # fine if ($lim_for != ($app_richiesti_vett['id'] + 1) and $limiti_var['t_limite'] < time())
for ($num1 = ($app_richiesti_vett['id'] + 1) ; $num1 <= $lim_for ; $num1++) {
if ($provato[$idinizio_vett[$num1]][$idfine_vett[$num1]][$app_richiesti_vett[$num1]] != "SI") {
$provato[$idinizio_vett[$num1]][$idfine_vett[$num1]][$app_richiesti_vett[$num1]] = "SI";
$n_app_prenota_id2 = $n_app_prenota_id;
$n_prenota_in_app_sett2 = $n_prenota_in_app_sett;
$n_app_richiesti_vett = $app_richiesti_vett;
if ((string) $n_app_richiesti_vett[',app_vicini,'] == "") $n_app_richiesti_vett[',app_vicini,'] = $dati_app['vicini'][$appartamento];
else {
$app_vicini = explode(",",$dati_app['vicini'][$appartamento]);
$num_app_vicini = count($app_vicini);
for ($num2 = 0 ; $num2 < $num_app_vicini ; $num2++) {
if (str_replace(",".$app_vicini[$num2].",","",",".$n_app_richiesti_vett[',app_vicini,'].",") == ",".$n_app_richiesti_vett[',app_vicini,'].",") $n_app_richiesti_vett[',app_vicini,'] .= ",".$app_vicini[$num2];
} # fine for $num2
} # fine else if ((string) $app_richiesti_vett[',app_vicini,'] == "")
$n_app_richiesti_vett[$num1] = incrocia_app_richiesti($n_app_richiesti_vett[$num1],$n_app_richiesti_vett[',app_vicini,']);
if ((string) $n_app_richiesti_vett[$num1] != "") {
$n_idinizio_vett = $idinizio_vett;
$n_idfine_vett = $idfine_vett;
$val = $n_app_richiesti_vett[($app_richiesti_vett['id'] + 1)];
$n_app_richiesti_vett[($app_richiesti_vett['id'] + 1)] = $n_app_richiesti_vett[$num1];
$n_app_richiesti_vett[$num1] = $val;
$n_idinizio_vett[($app_richiesti_vett['id'] + 1)] = $idinizio_vett[$num1];
$n_idinizio_vett[$num1] = $idinizio_vett[($app_richiesti_vett['id'] + 1)];
$n_idfine_vett[($app_richiesti_vett['id'] + 1)] = $idfine_vett[$num1];
$n_idfine_vett[$num1] = $idfine_vett[($app_richiesti_vett['id'] + 1)];
if ($debug == "on") {
global $passo;
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> libera vicini a $appartamento da ".$n_idinizio_vett[($app_richiesti_vett['id'] + 1)]." a ".$n_idfine_vett[($app_richiesti_vett['id'] + 1)]." in ".$n_app_richiesti_vett[($app_richiesti_vett['id'] + 1)]."<br>";
} # fine if ($debug == "on")
liberasettimane($n_idinizio_vett,$n_idfine_vett,$limiti_var,$anno,$fatto,$appartamento_vett,$nuova_profondita,$n_app_richiesti_vett,$n_app_prenota_id2,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$n_prenota_in_app_sett2,$dati_app,$nome_tab_prenota);
if ($debug == "on") {
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> fatto libera vicini a $appartamento $fatto<br>";
} # fine if ($debug == "on")
if ($fatto == "SI") {
$n_app_prenota_id = $n_app_prenota_id2;
$n_prenota_in_app_sett = $n_prenota_in_app_sett2;
$val = $appartamento_vett[($app_richiesti_vett['id'] + 1)];
$appartamento_vett[($app_richiesti_vett['id'] + 1)] = $appartamento_vett[$num1];
$appartamento_vett[$num1] = $val;
break;
} # fine if ($fatto == "SI")
} # fine if ((string) $n_app_richiesti_vett[$num1] != "")
} # fine if ($provato[$idinizio_vett[$num1]][$idfine_vett[$num1]][$app_richiesti_vett[$num1]] != "SI")
} # fine for $num1
if ($fatto != "SI") $riprova_senza_app_liberato = $appartamento;
} # fine if ($app_richiesti_vett[',vicini,'] == "SI" and !$app_richiesti_vett[',succ_non_vicino,'][$app_richiesti_vett['id']])

else {
if ($app_richiesti_vett[$app_richiesti_vett['id']] == ",tutti,") $app_assegnabili_id[$id_prenota_lib] = "v";
else if (str_replace(",","",$app_richiesti_vett[$app_richiesti_vett['id']]) != $app_richiesti_vett[$app_richiesti_vett['id']]) $app_assegnabili_id[$id_prenota_lib] = $app_richiesti_vett[$app_richiesti_vett['id']];
liberasettimane($idinizio_vett,$idfine_vett,$limiti_var,$anno,$fatto,$appartamento_vett,$nuova_profondita,$app_richiesti_vett,$n_app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$n_prenota_in_app_sett,$dati_app,$nome_tab_prenota);
} # fine else if ($app_richiesti[',vicini,'] == "SI" and !$app_richiesti_vett[',succ_non_vicino,'][$app_richiesti_vett['id']])

if ($fatto == "SI") {
$app_prenota_id = $n_app_prenota_id;
$prenota_in_app_sett = $n_prenota_in_app_sett;
for ($num1 = $idinizio ; $num1 <= $idfine ; $num1++) {
$prenota_in_app_sett[$app_prenota_id[$id_prenota_lib]][$num1] = "";
} # fine for $num1
$appartamento_vett[$app_richiesti_vett['id']] = $app_prenota_id[$id_prenota_lib];
$app_prenota_id[$id_prenota_lib] = "";
} # fine if ($fatto == "SI")
else {
$appartamento_vett[$app_richiesti_vett['id']] = "";
aggiorna_app_aggiunti($limiti_var,$limiti_var_orig,$app_orig_prenota_id,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett);
} # fine else if ($fatto == "SI")
$inizio_prenota_id[$id_prenota_lib] = "";
$fine_prenota_id[$id_prenota_lib] = "";
$app_assegnabili_id[$id_prenota_lib] = "";
} # fine if ($app_richiesti_vett[',numero,'] > $app_richiesti_vett['id'])
else $appartamento_vett[$app_richiesti_vett['id']] = $appartamento;
$appartamento = $appartamento_vett;
} # fine if ($fatto == "SI" and $app_richiesti_vett[',numero,'])


# Se si era liberato un appartamento ma non se ne sono trovati altri vicini riprovo
if ($riprova_senza_app_liberato and ($limiti_var['t_limite'] + 20) >= time()) {
$n_lista_app_ric = "";
for ($num1 = 0 ; $num1 < $dati_app['totapp'] ; $num1++) {
$numapp = $dati_app['posizione'][$num1];
if ($numapp != $riprova_senza_app_liberato and $riprova_app[$numapp] != "NO") $n_lista_app_ric .= $numapp.",";
} # fine for $num1
$n_lista_app_ric = substr($n_lista_app_ric,0,-1);
$app_richiesti_vett[$app_richiesti_vett['id']] = incrocia_app_richiesti($app_richiesti_vett[$app_richiesti_vett['id']],$n_lista_app_ric);
if ((string) $app_richiesti_vett[$app_richiesti_vett['id']] != "") {
for ($num1 = ($app_richiesti_vett['id'] + 1) ; $num1 <= $app_richiesti_vett[',numero,'] ; $num1++) {
if ($app_assegnabili_id[0][0][$num1] == $app_assegnabili_id[0][0][$app_richiesti_vett['id']] and $idinizio_vett[$num1] == $idinizio and $idfine_vett[$num1] == $idfine and !$app_richiesti_vett[',succ_non_vicino,'][($num1 - 1)]) {
$app_richiesti_vett[$num1] = $app_richiesti_vett[$app_richiesti_vett['id']];
} # fine if ($app_assegnabili_id[0][0][$num1] == $app_assegnabili_id[0][0][$app_richiesti_vett['id']] and $idinizio_vett[$num1] == $idinizio and...
else break;
} # fine for $num1
$nuova_profondita = $profondita;
$nuova_profondita['attuale']++;
$nuova_profondita['controllato_tutti_occupati'] = "NO";
$n_app_prenota_id = $app_prenota_id;
$n_prenota_in_app_sett = $prenota_in_app_sett;
$app_richiesti_vett['id'] = $app_richiesti_vett['id'] - 1;
if ($debug == "on") {
global $passo;
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> <em>".date("H:i:s")."</em> riprova ".$app_richiesti_vett['id']." negli app ".$app_richiesti_vett[$app_richiesti_vett['id']]."<br>";
} # fine if ($debug == "on")
liberasettimane($idinizio_vett,$idfine_vett,$limiti_var,$anno,$fatto,$appartamento_vett,$nuova_profondita,$app_richiesti_vett,$n_app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$n_prenota_in_app_sett,$dati_app,$nome_tab_prenota);
if ($debug == "on") {
echo $passo;
for ($i = 0 ; $i < $profondita['attuale'] ; $i++) echo "&nbsp;&nbsp;";
echo "<b>".$profondita['attuale']."</b> fatto riprova ".$app_richiesti_vett['id']."<br>";
} # fine if ($debug == "on")
if ($fatto == "SI") {
$app_prenota_id = $n_app_prenota_id;
$prenota_in_app_sett = $n_prenota_in_app_sett;
$appartamento = $appartamento_vett;
} # fine if ($fatto == "SI")
else aggiorna_app_aggiunti($limiti_var,$limiti_var_orig,$app_orig_prenota_id,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett);
} # fine if ((string) $app_richiesti_vett[$app_richiesti_vett['id']] != "")
} # fine if ($riprova_senza_app_liberato and ($limiti_var['t_limite'] + 20) >= time())


if ($primo_ciclo == "SI" and $fatto == "SI") {
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $fatto = "NO";
} # fine if ($primo_ciclo == "SI" and $fatto == "SI")
if ($ass00) $app_assegnabili_id[0][0] = "";

} # fine function liberasettimane




?>
