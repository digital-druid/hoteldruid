<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2016 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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




function inserisci_prenotazione ($inizioperiodo,$fineperiodo,$lista_app,$tipotariffa,$numpersone,$prenota_vicine,$idclienti,$conferma,$pagato,$caparra,$met_paga_caparra,$commissioni,$origine_prenota,$sconto,$giorno_stima_checkin,$ora_stima_checkin,$min_stima_checkin,$commento,&$stato,$anno,$PHPR_TAB_PRE,$ins_passato="",$id_utente_imp="",$id_prenota_imp="",$tariffe_sett_imp="",$ins_costi_agg="",$costo_letto_imposto="NO") {
global $id_utente,$HOSTNAME;

$PHPR_TAB_PRE = aggslashdb($PHPR_TAB_PRE);
$anno = aggslashdb($anno);
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;

if (!function_exists('tab_a_var')) include("./includes/liberasettimane.php");
if (!function_exists('dati_tariffe')) include("./includes/funzioni_tariffe.php");

$inc = @include("./includes/sett_gio.php");
if (!$inc) {
$tipo_periodi = esegui_query("select * from $tableanni where idanni = $anno");
$tipo_periodi = risul_query($tipo_periodi,0,'tipo_periodi');
} # fine if (!$inc)
$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();

$num_tipologie = count($inizioperiodo);
$appartamenti = esegui_query("select * from $tableappartamenti ");
$numappartamenti = numlin_query($appartamenti);
$id_app_richiesti = 0;

$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
if ($ins_costi_agg or $costo_letto_imposto != "NO") {
if (!function_exists('dati_costi_agg_ntariffe')) include("./includes/funzioni_costi_agg.php");
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num'],"NO","",$tableappartamenti);
} # fine if ($ins_costi_agg or $costo_letto_imposto != "NO")

$app_eliminati_costi = array();


for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {

$inizioperiodo_orig[$n_t] = $inizioperiodo[$n_t];
$fineperiodo_orig[$n_t] = $fineperiodo[$n_t];
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '".aggslashdb($inizioperiodo[$n_t])."' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo == 0) $idinizioperiodo = 10000;
else $idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi');
$inizioperiodo[$n_t] = $idinizioperiodo;
if (!$idinizioperiodo_tot or $idinizioperiodo < $idinizioperiodo_tot) $idinizioperiodo_tot = $idinizioperiodo;
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '".aggslashdb($fineperiodo[$n_t])."' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo == 0) $idfineperiodo = -1;
else $idfineperiodo = risul_query($idfineperiodo,0,'idperiodi');
$fineperiodo[$n_t] = $idfineperiodo;
if (!$idfineperiodo_tot or $idfineperiodo > $idfineperiodo_tot) $idfineperiodo_tot = $idfineperiodo;

$id_periodo_corrente = calcola_id_periodo_corrente($anno);
if ($id_periodo_corrente > $idinizioperiodo and !$ins_passato) $data_sbagliata = "SI";
if ($idfineperiodo < $idinizioperiodo or $data_sbagliata == "SI") {
$inserire = "NO";
$mess_errore .= mex("Le date sono sbagliate","clienti.php");
if ($num_tipologie > 1) $mess_errore .= " (".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
} # fine if ($idfineperiodo < $idinizioperiodo or...

if (substr($tipotariffa[$n_t],0,7) != "tariffa" or controlla_num_pos(substr($tipotariffa[$n_t],7)) == "NO") $tariffa_sbagliata = "SI";
else {
$tar_sett_imp = $tariffe_sett_imp[$n_t];
for ($num1 = $inizioperiodo[$n_t] ; $num1 <= $fineperiodo[$n_t] ; $num1++) {
$rigasettimana = esegui_query("select * from $tableperiodi where idperiodi = '$num1' ");
$esistetariffa = risul_query($rigasettimana,0,$tipotariffa[$n_t]);
$esistetariffap = risul_query($rigasettimana,0,$tipotariffa[$n_t]."p");
if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and (!strcmp($esistetariffap,"") or $esistetariffap < 0) and !strcmp($tar_sett_imp[$num1],"")) $tariffa_sbagliata = "SI";
if ($dati_tariffe[$tipotariffa[$n_t]]['chiusa'][$num1] and !strcmp($tar_sett_imp[$num1],"")) $tariffa_sbagliata = "SI";
} # fine for $num1
} # fine else if (substr($tipotariffa[$n_t],0,7) != "tariffa" or...
if ($tariffa_sbagliata == "SI") {
$inserire = "NO";
$mess_errore .= mex("Si deve inserire il tipo di tariffa","clienti.php");
if ($num_tipologie > 1) $mess_errore .= "(".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
} # fine if ($tariffa_sbagliata == "SI")
else {

# se vi è una regola 2 per la tariffa
unset($app_regola2_predef);
if (!$lista_app[$n_t]) {
$lista_app[$n_t] = dati_regole2($dati_r2,$app_regola2_predef,$tipotariffa[$n_t],$idinizioperiodo,$idfineperiodo,$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
} # fine if (!$lista_app[$n_t])

$id_utente_ins = "";
# se vi è una regola 3 per la tariffa
if ($id_utente == 1) {
$regole3 = esegui_query("select * from $tableregole where tariffa_per_utente = '".aggslashdb($tipotariffa[$n_t])."'");
if (numlin_query($regole3) == 1) $id_utente_ins = risul_query($regole3,0,'iddatainizio');
} # fine if ($id_utente == 1)
if (!$id_utente_ins and $id_utente_imp) $id_utente_ins = $id_utente_imp;
if (!$id_utente_ins and $id_utente) $id_utente_ins = $id_utente;
if (!$id_utente_ins) $id_utente_ins = 1;

# se vi è una regola 4 per la tariffa
if (!$numpersone[$n_t]) {
$regole4 = esegui_query("select * from $tableregole where tariffa_per_persone = '".aggslashdb($tipotariffa[$n_t])."'");
if (numlin_query($regole4) == 1) $numpersone[$n_t] = risul_query($regole4,0,'iddatainizio');
} # fine if (!$numpersone[$n_t])

if (!$numpersone[$n_t]) {
if ($dati_tariffe[$tipotariffa[$n_t]]['moltiplica'] == "p") {
$inserire = "NO";
$mess_errore .= mex("Si deve inserire il numero delle persone per questa tariffa","clienti.php");
if ($num_tipologie > 1) $mess_errore .= "(".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
} # fine if ($dati_tariffe[$tipotariffa[$n_t]]['moltiplica'] == "p")
} # fine if (!$numpersone[$n_t])

} # fine else if ($tariffa_sbagliata == "SI")

if (controlla_num_pos($idclienti[$n_t]) == "NO") $inserire = "NO";
else {
$cliente_esistente = esegui_query("select cognome from $tableclienti where idclienti = '".$idclienti[$n_t]."' ");
if (numlin_query($cliente_esistente) != 1) $inserire = "NO";
else $cognome = risul_query($cliente_esistente,0,"cognome");
} # fine else if (controlla_num_pos($idclienti[$n_t]) == "NO")

$app_richiesti_id[$n_t] = array();
$assegnazioneapp[$n_t] = "v";
if ($lista_app[$n_t]) {
$assegnazioneapp[$n_t] = "c";
$vett_app = explode(",",$lista_app[$n_t]);
$num_app = count($vett_app);
if ($num_app == 1) $assegnazioneapp[$n_t] = "k";
for ($num1 = 0 ; $num1 < $num_app ; $num1++) {
$appo = aggslashdb($vett_app[$num1]);
$app_richiesti_id[$n_t][$appo] = "SI";
$appartamento_esistente = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '$appo' ");
if (numlin_query($appartamento_esistente) != 1) {
$inserire = "NO";
$mess_errore .= mex("L'appartamento",'unit.php')." <div style=\"display: inline; color: red;\">$appo</div> ".mex("contenuto nella lista non esiste","clienti.php");
if ($num_tipologie > 1) $mess_errore .= " (".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
} # fine if (numlin_query($appartamento_esistente) != 1)
} # fine for $num1
} # fine if ($lista_app[$n_t])

# se il costo letto imposto ha appartamenti incompatibili
if ($costo_letto_imposto != "NO" and $costo_letto_imposto[$n_t]['id'] and strcmp($dati_ca[$dati_ca['id'][$costo_letto_imposto[$n_t]['id']]]['appincompatibili'],"")) {
$app_incomp_costi[$n_t] = ",".$dati_ca[$dati_ca['id'][$costo_letto_imposto[$n_t]['id']]]['appincompatibili'].",";
$lista_app_corr = $lista_app[$n_t];
$lista_app[$n_t] = "";
if (strcmp($lista_app_corr,"")) $lista_app_corr = ",$lista_app_corr,";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if (!$lista_app_corr or str_replace(",$idapp,","",$lista_app_corr) != $lista_app_corr) {
if (str_replace(",$idapp,","",$app_incomp_costi[$n_t]) == $app_incomp_costi[$n_t]) $lista_app[$n_t] .= ",$idapp";
else $app_eliminati_costi[$n_t] .= ",$idapp";
} # fine if (!$lista_app_corr or str_replace(",$idapp,","",$lista_app_corr) != $lista_app_corr)
} # fine for $num1
if ($lista_app[$n_t]) {
$lista_app[$n_t] = substr($lista_app[$n_t],1);
$app_eliminati_costi[$n_t] = substr($app_eliminati_costi[$n_t],1);
if (str_replace(",","",$lista_app[$n_t]) != $lista_app[$n_t]) $assegnazioneapp[$n_t] = "c";
else $assegnazioneapp[$n_t] = "k";
$app_richiesti_id[$n_t] = array();
$vett_app = explode(",",$lista_app[$n_t]);
$num_app = count($vett_app);
for ($num1 = 0 ; $num1 < $num_app ; $num1++) $app_richiesti_id[$n_t][$vett_app[$num1]] = "SI";
} # fine if ($lista_app[$n_t])
else {
$mess_errore .= mex("Non c'è nessun appartamento tra quelli richiesti che sia compatibile con i costi aggiuntivi selezionati",'unit.php');
if ($num_tipologie > 1) $mess_errore .= " (".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
$inserire = "NO";
} # fine else if ($lista_app[$n_t])
} # fine if ($costo_letto_imposto != "NO" and $costo_letto_imposto[$n_t]['id'] and strcmp($dati_ca[$dati_ca['id'][$costo_letto_imposto[$n_t]['id']]]['appincompatibili'],""))

if ($numpersone[$n_t]) {
if (controlla_num_pos($numpersone[$n_t]) == "NO") {
$inserire = "NO";
$mess_errore .= mex("Il numero di persone è sbagliato","clienti.php");
if ($num_tipologie > 1) $mess_errore .= " (".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
} # fine if (controlla_num_pos($numpersone[$n_t]) == "NO")
$app_richiesti_copia = $app_richiesti_id[$n_t];
$posti = 0;
if ($app_regola2_predef) {
$app_regola2_predef = ",$app_regola2_predef,";
$posto_reg2_orig = 0;
} # fine if ($app_regola2_predef)
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if (!$app_richiesti_copia or $app_richiesti_copia[$idapp] == "SI") {
if ($maxoccupanti and $maxoccupanti < $numpersone[$n_t]) {
$app_richiesti_id[$n_t][$idapp] = "NO";
} # fine if ($maxoccupanti and $maxoccupanti < $numpersone[$n_t])
else {
$app_richiesti_id[$n_t][$idapp] = "SI";
$posti++;
} # fine else if ($maxoccupanti and $maxoccupanti < $numpersone[$n_t])
} # fine if (!$app_richiesti_copia or $app_richiesti_copia[$idapp] == "SI")
if ($app_regola2_predef) {
if (str_replace(",$idapp,","",$app_regola2_predef) != $app_regola2_predef) {
if (!$maxoccupanti or $maxoccupanti >= $numpersone[$n_t]) $posto_reg2_orig = 1;
} # fine if (str_replace(",$idapp,","",$app_regola2_predef) != $app_regola2_predef)
} # fine if ($app_regola2_predef)
} # fine for $num1
if ($posti == 0 or ($app_regola2_predef and !$posto_reg2_orig)) {
$mess_errore .= mex("Non c'è nessun appartamento tra quelli richiesti che possa ospitare",'unit.php')." ".$numpersone[$n_t]." ".mex("persone","clienti.php");
if ($num_tipologie > 1) $mess_errore .= " (".mex("tipologia","clienti.php")." $n_t)";
$mess_errore .= ".<br>";
$inserire = "NO";
} # fine if ($posti == 0 or...
} # fine if ($numpersone[$n_t])

unset($lista_app_richiesti);
if (!$app_richiesti_id[$n_t]) $lista_app_richiesti = ",tutti,,";
else foreach ($app_richiesti_id[$n_t] as $key => $val) if ($val == "SI") $lista_app_richiesti .= $key.",";
$lista_app_richiesti = substr($lista_app_richiesti,0,-1);
$id_app_richiesti++;
$app_richiesti[$id_app_richiesti] = $lista_app_richiesti;
$idinizioperiodo_vett[$id_app_richiesti] = $idinizioperiodo;
$idfineperiodo_vett[$id_app_richiesti] = $idfineperiodo;
$app_richiesti[",numero,"] = $id_app_richiesti;
if ($num_tipologie > 1) {
if (@is_array($prenota_vicine)) {
$app_richiesti[',vicini,'] = "SI";
$app_richiesti[',succ_non_vicino,'] = $prenota_vicine;
} # fine if (@is_array($prenota_vicine))
elseif ($prenota_vicine == "SI") $app_richiesti[',vicini,'] = "SI";
} # fine if ($num_tipologie > 1)

if ($id_prenota_imp) {
if (controlla_num_pos($id_prenota_imp[$n_t]) == "SI") {
$id_esistente = esegui_query("select idprenota from $tableprenota where idprenota = '".$id_prenota_imp[$n_t]."' ");
if (numlin_query($id_esistente)) unset($id_prenota_imp);
} # fine if (controlla_num_pos($id_prenota_imp[$n_t]) == "SI")
else unset($id_prenota_imp);
} # fine if ($id_prenota_imp)

} # fine for $n_t


if ($inserire != "NO") {

$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != ''$condizioni_regole1_non_sel");
$num_app_agenzia = numlin_query($app_agenzia);

$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$lim_prenota_temp = aggslashdb(date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60))));
esegui_query("delete from $tableprenota where idclienti = '0' and datainserimento < '".$lim_prenota_temp."' ");

#Se ci sono regole per $app_agenzia inserisco false prenotazioni fisse in $app_prenota_id2
if ($num_app_agenzia != 0) {
unset($limiti_var);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($profondita);
$limiti_var['n_ini'] = $idinizioperiodo_tot;
$limiti_var['n_fine'] = $idfineperiodo_tot;
$limiti_var['lim_prenota_temp'] = $lim_prenota_temp;
$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
if (numlin_query($max_prenota) != 0) $tot_prenota = risul_query($max_prenota,0,0);
else $tot_prenota = 0;
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
unset($info_periodi_ag);
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
if ($info_periodi_ag['numero']) inserisci_prenota_fittizie($info_periodi_ag,$profondita,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett,$app_assegnabili_id);
$limiti_var2 = $limiti_var;
$profondita2 = $profondita;
$prenota_in_app_sett2 = $prenota_in_app_sett;
$inizio_prenota_id2 = $inizio_prenota_id;
$fine_prenota_id2 = $fine_prenota_id;
$app_prenota_id2 = $app_prenota_id;
$app_assegnabili_id2 = $app_assegnabili_id;
unset($info_periodi_ag);
$info_periodi_ag['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 != "x") {
$info_periodi_ag['app'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi_ag['ini'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi_ag['fine'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$info_periodi_ag['numero']++;
} # fine if ($mot2 != "x")
} # fine for $num1
if ($info_periodi_ag['numero']) inserisci_prenota_fittizie($info_periodi_ag,$profondita2,$app_prenota_id2,$inizio_prenota_id2,$fine_prenota_id2,$prenota_in_app_sett2,$app_assegnabili_id2);
$occupare_app_agenzia_sempre = "NO";
for ($num1 = $idinizioperiodo_tot ; $num1 <= $idfineperiodo_tot ; $num1++) {
$numprenotazioni = 0;
for ($num2 = 0 ; $num2 < $dati_app['totapp'] ; $num2++) if ($prenota_in_app_sett2[$dati_app['posizione'][$num2]][$num1]) $numprenotazioni++;
if ($numprenotazioni >= $numappartamenti) $occupare_app_agenzia_sempre = "SI";
} # fine for $num1
if ($occupare_app_agenzia_sempre != "SI") {
$app_orig_prenota_id = $app_prenota_id2;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var2,$anno,$fatto_libera,$app_liberato,$profondita2,$app_richiesti,$app_prenota_id2,$app_orig_prenota_id,$inizio_prenota_id2,$fine_prenota_id2,$app_assegnabili_id2,$prenota_in_app_sett2,$dati_app,$PHPR_TAB_PRE."prenota");
} # fine if ($occupare_app_agenzia_sempre != "SI")
else $fatto_libera = "NO";
if ($fatto_libera == "SI") {
$risul_agg = aggiorna_tableprenota($app_prenota_id2,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $fatto_libera = "NO";
else {
$occupare_app_agenzia = "NO";
$appartamento = $app_liberato;
} # fine else if (!$risul_agg)
} # fine if ($fatto_libera == "SI")

# se ci sono app_agenzia e non si può non occuparli
else {
$occupare_app_agenzia = "SI";
$limiti_var['t_limite'] = (time() + $sec_limite_libsett);
$app_liberato = "";
$app_orig_prenota_id = $app_prenota_id;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera2,$app_liberato,$profondita,$app_richiesti,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");
if ($fatto_libera2 != "NO") {
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $fatto_libera2 = "NO";
} # fine if ($fatto_libera2 != "NO")
if ($fatto_libera2 == "NO") {
$inserire = "NO";
$mess_errore .= mex("Nel periodo selezionato non è possibile ospitare il cliente negli appartamenti richiesti",'unit.php');
if ($app_incomp_costi) $mess_errore .= " ".mex("con i costi aggiuntivi selezionati",'clienti.php');
$mess_errore .= ".<br>";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$mess_prenota .= mex("Prenotazione","prenota.php").mex("dal","prenota.php")." <b>".formatta_data($inizioperiodo_orig[$n_t],$stile_data)."</b> ".mex("al","prenota.php")." <b>".formatta_data($fineperiodo_orig[$n_t],$stile_data)."</b>";
$mess_prenota .= " ".mex("a nome di","prenota.php")." <b>$cognome</b> ";
if ($numpersone[$n_t]) $mess_prenota .= " ".mex("per","prenota.php")." <b>".$numpersone[$n_t]."</b> ".mex("persone","prenota.php")."";
if ($dati_tariffe[$tipotariffa[$n_t]]['nome']) $nometariffa_vedi = $dati_tariffe[$tipotariffa[$n_t]]['nome'];
else $nometariffa_vedi = mex("tariffa","prenota.php").substr($tipotariffa[$n_t],7);
$mess_prenota .= ", ".mex("tariffa","prenota.php")." \"$nometariffa_vedi\".<br><br>";
} # fine for $n_t
} # fine if ($fatto_libera2 == "NO")
else {
$appartamento = $app_liberato;
$mess_regola1 .= mex("Sono stati fatti i seguenti spostamenti nei periodi della <div style=\"display: inline; color: blue;\">regola di assegnazione 1</div>",'clienti.php').":<br>";
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 != "x") {
$id_app_agenzia = risul_query($app_agenzia,$num1,'app_agenzia');
$idinizio_app_agenzia = risul_query($app_agenzia,$num1,'iddatainizio');
$idfine_app_agenzia = risul_query($app_agenzia,$num1,'iddatafine');
$motivazione_app_agenzia = risul_query($app_agenzia,$num1,'motivazione');
$idprenota_cambiate_da_ag = esegui_query("select idprenota from $tableprenota where idappartamenti = '$id_app_agenzia' and iddatainizio <= '$idfine_app_agenzia' and iddatafine >= '$idinizio_app_agenzia'");
$num_idprenota_cambiate_da_ag = numlin_query($idprenota_cambiate_da_ag);
for ($num2 = 0 ; $num2 < $num_idprenota_cambiate_da_ag ; $num2 = $num2 + 1) {
$idprenota_cambiata = risul_query($idprenota_cambiate_da_ag,$num2,'idprenota');
if ($app_prenota_id[$idprenota_cambiata] and $app_prenota_id[$idprenota_cambiata] != $id_app_agenzia) {
$idapp_camb = $app_prenota_id[$idprenota_cambiata];
$dati_cambiati = esegui_query("select * from $tableprenota where idprenota = '$idprenota_cambiata'");
$idclienti_camb = risul_query($dati_cambiati,0,'idclienti');
$cliente_camb = esegui_query("select cognome from $tableclienti where idclienti = '$idclienti_camb'");
if ($idclienti_camb) $cliente_camb = risul_query($cliente_camb,0,'cognome');
else $cliente_camb = "?";
$iddatainizio_camb = $inizio_prenota_id[$idprenota_cambiata];
$datainizio_camb = esegui_query("select datainizio from $tableperiodi where idperiodi = '$iddatainizio_camb'");
$datainizio_camb = risul_query($datainizio_camb,0,'datainizio');
$datainizio_camb_f = formatta_data($datainizio_camb,$stile_data);
$iddatafine_camb = $fine_prenota_id[$idprenota_cambiata];
$datafine_camb = esegui_query("select datafine from $tableperiodi where idperiodi = '$iddatafine_camb'");
$datafine_camb = risul_query($datafine_camb,0,'datafine');
$datafine_camb_f = formatta_data($datafine_camb,$stile_data);
$mess_regola1 .= mex("La prenotazione dal","clienti.php")." $datainizio_camb_f ".mex("al","clienti.php")." $datafine_camb_f ".mex("a nome di","clienti.php")." $cliente_camb ".mex("verrà spostata dall'appartamento",'unit.php')." <div style=\"display: inline; color: blue;\"><b>$id_app_agenzia</b></div> (<b>$motivazione_app_agenzia</b>) ".mex("al","clienti.php")." $idapp_camb.<br>";
} # fine if ($app_prenota_id[$idprenota_cambiata] and $app_prenota_id[$idprenota_cambiata] != $id_app_agenzia)
} # fine for $num2
$idprenota_cambiate_a_ag = prenota_in_app_e_periodo($id_app_agenzia,$idinizio_app_agenzia,$idfine_app_agenzia,$prenota_in_app_sett,$fine_prenota_id,$num_pca);
for ($num2 = 1 ; $num2 <= $num_pca ; $num2++) {
$idprenota_cambiata = $idprenota_cambiate_a_ag[$num2];
if ($app_orig_prenota_id[$idprenota_cambiata] and $app_prenota_id[$idprenota_cambiata] != $app_orig_prenota_id[$idprenota_cambiata]) {
$dati_cambiati = esegui_query("select * from $tableprenota where idprenota = $idprenota_cambiata");
$idapp_camb = $app_orig_prenota_id[$idprenota_cambiata];
$idclienti_camb = risul_query($dati_cambiati,0,'idclienti');
$cliente_camb = esegui_query("select cognome from $tableclienti where idclienti = $idclienti_camb");
if ($idclienti_camb) $cliente_camb = risul_query($cliente_camb,0,'cognome');
else $cliente_camb = "?";
$iddatainizio_camb = $inizio_prenota_id[$idprenota_cambiata];
$datainizio_camb = esegui_query("select datainizio from $tableperiodi where idperiodi = $iddatainizio_camb");
$datainizio_camb = risul_query($datainizio_camb,0,'datainizio');
$datainizio_camb_f = formatta_data($datainizio_camb,$stile_data);
$iddatafine_camb = $fine_prenota_id[$idprenota_cambiata];
$datafine_camb = esegui_query("select datafine from $tableperiodi where idperiodi = $iddatafine_camb");
$datafine_camb = risul_query($datafine_camb,0,'datafine');
$datafine_camb_f = formatta_data($datafine_camb,$stile_data);
$mess_regola1 .= mex("La prenotazione dal","clienti.php")." $datainizio_camb_f ".mex("al","clienti.php")." $datafine_camb_f ".mex("a nome di","clienti.php")." $cliente_camb ".mex("verrà spostata dall'appartamento",'unit.php')." $idapp_camb ".mex("al","clienti.php")." <div style=\"display: inline; color: blue;\"><b>$id_app_agenzia</b></div> (<b>$motivazione_app_agenzia</b>).<br>";
} # fine if ($app_orig_prenota_id[$idprenota_cambiata] and...
} # fine for $num2
for ($num2 = 1 ; $num2 <= $id_app_richiesti ; $num2++) {
if ($id_app_richiesti > 1) $appartamento_controlla = $appartamento[$num2];
else $appartamento_controlla = $appartamento;
if ($id_app_agenzia == $appartamento_controlla and $idinizio_app_agenzia <= $idfineperiodo and $idfine_app_agenzia >= $idinizioperiodo) {
$mess_regola1 .= mex("La nuova prenotazione verrà inserita nell'appartamento",'unit.php')." <div style=\"display: inline; color: blue;\"><b>$id_app_agenzia</b></div> (<b>$motivazione_app_agenzia</b>).<br>";
} # fine if ($id_app_agenzia == $appartamento_controlla and ...
} # fine for $num2
} # fine if ($mot2 != "x")
} # fine for $num1
} # fine else if ($fatto_libera2 == "NO")
} # fine else if ($fatto_libera == "SI")
} # fine if ($num_app_agenzia != 0)

else {
# se non ci sono app_agenzia della regola di assegnazione 1
unset($limiti_var);
unset($profondita_);
unset($app_prenota_id_);
unset($app_orig_prenota_id_);
unset($inizio_prenota_id_);
unset($fine_prenota_id_);
unset($app_assegnabili_id_);
unset($prenota_in_app_sett_);
unset($dati_app_);
unset($app_liberato);
$limiti_var["lim_prenota_temp"] = $lim_prenota_temp;

liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera,$app_liberato,$profondita_,$app_richiesti,$app_prenota_id_,$app_orig_prenota_id_,$inizio_prenota_id_,$fine_prenota_id_,$app_assegnabili_id_,$prenota_in_app_sett_,$dati_app_,$PHPR_TAB_PRE."prenota");

if ($fatto_libera == "NO") {
$inserire = "NO";
$mess_errore .= mex("Nel periodo selezionato non è possibile ospitare il cliente in un appartamento tra quelli richiesti",'unit.php');
if ($app_incomp_costi) $mess_errore .= " ".mex("con i costi aggiuntivi selezionati",'clienti.php');
$mess_errore .= ".<br>";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$mess_prenota .= mex("Prenotazione","prenota.php").mex("dal","prenota.php")." <b>".formatta_data($inizioperiodo_orig[$n_t],$stile_data)."</b> ".mex("al","prenota.php")." <b>".formatta_data($fineperiodo_orig[$n_t],$stile_data)."</b>";
$mess_prenota .= " ".mex("a nome di","prenota.php")." <b>$cognome</b> ";
if ($numpersone[$n_t]) $mess_prenota .= " ".mex("per","prenota.php")." <b>".$numpersone[$n_t]."</b> ".mex("persone","prenota.php")."";
if ($dati_tariffe[$tipotariffa[$n_t]]['nome']) $nometariffa_vedi = $dati_tariffe[$tipotariffa[$n_t]]['nome'];
else $nometariffa_vedi = mex("tariffa","prenota.php").substr($tipotariffa[$n_t],7);
$mess_prenota .= ", ".mex("tariffa","prenota.php")." \"$nometariffa_vedi\".<br><br>";
} # fine for $n_t
} # fine if ($fatto_libera == "NO")
else $appartamento = $app_liberato;
} # fine else if ($num_app_agenzia != 0)

} # fine if ($inserire != "NO")



if ($inserire != "NO") {

$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '$id_utente'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
if ($metodi_pagamento) $metodi_pagamento = explode(",",$metodi_pagamento);
$origini_prenota = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'origini_prenota' and idutente = '$id_utente'");
$origini_prenota = risul_query($origini_prenota,0,'valpersonalizza');
if ($origini_prenota) $origini_prenota = explode(",",$origini_prenota);


for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$lunghezza_periodo = $fineperiodo[$n_t] - $inizioperiodo[$n_t] + 1;

$max_idprenota = esegui_query("select numlimite from $tablecostiprenota where idcostiprenota = '1'");
$max_idprenota = risul_query($max_idprenota,0,'numlimite');
if ($id_prenota_imp[$n_t]) $idprenota = $id_prenota_imp[$n_t];
else $idprenota = $max_idprenota;
if ($idprenota >= $max_idprenota) esegui_query("update $tablecostiprenota set numlimite = '".($idprenota + 1)."' where idcostiprenota = '1'");

$mess_prenota .= mex("Prenotazione","prenota.php")." $idprenota ".mex("dal","prenota.php")." <b>".formatta_data($inizioperiodo_orig[$n_t],$stile_data)."</b> ".mex("al","prenota.php")." <b>".formatta_data($fineperiodo_orig[$n_t],$stile_data)."</b> ($lunghezza_periodo ".mex("$parola_settiman","prenota.php");
if ($lunghezza_periodo == 1) $mess_prenota .= mex("$lettera_a","prenota.php");
else $mess_prenota .= mex("$lettera_e","prenota.php");
$mess_prenota .= ") ".mex("a nome di","prenota.php")." <b>$cognome</b> ";
if ($numpersone[$n_t]) $mess_prenota .= mex("per",'prenota.php')." <b>".$numpersone[$n_t]."</b> ".mex("persone",'prenota.php')." ";
$mess_prenota .= mex("nell'appartamento",'unit.php')." <b>".$appartamento[$n_t]."</b>";
if ($assegnazioneapp[$n_t] == "k") $mess_prenota .= " (".mex("fisso","unit.php").")";
else {
$mess_prenota .= " (".mex("mobile","prenota.php");
if ($lista_app[$n_t]) $mess_prenota .= " ".mex("in","prenota.php")." ".str_replace(",",", ",$lista_app[$n_t]);
$mess_prenota .= ")";
} # fine else if ($assegnazioneapp[$n_t] == "k")
$mess_prenota .= ":<br><br>";

$costo_tariffa_tot = (double) 0;
unset($lista_tariffe_sett);
unset($lista_tariffep_sett);
$tar_sett_imp = $tariffe_sett_imp[$n_t];
for ($num2 = $inizioperiodo[$n_t] ; $num2 <= $fineperiodo[$n_t] ; $num2++) {
$riga_tariffa = esegui_query("select * from  $tableperiodi where idperiodi = $num2");
if (strcmp($tar_sett_imp[$num2],"")) $costo_tariffa = $tar_sett_imp[$num2];
else $costo_tariffa = risul_query($riga_tariffa,0,$tipotariffa[$n_t]);
if ($dati_tariffe[$tipotariffa[$n_t]]['moltiplica'] == "p") {
if (!strcmp($costo_tariffa,"")) $costo_tariffa = 0;
if (strcmp($tar_sett_imp[$num2],"")) $costo_tariffap = 0;
else $costo_tariffap = risul_query($riga_tariffa,0,$tipotariffa[$n_t]."p");
if (!strcmp($costo_tariffap,"")) $costo_tariffap = 0;
$costo_tariffap = (double) $costo_tariffap * (double) $numpersone[$n_t];
$lista_tariffep_sett .= ",".$costo_tariffap;
$costo_tariffa = (double) $costo_tariffa + $costo_tariffap;
} # fine if ($dati_tariffe[$tipotariffa[$n_t]]['moltiplica'] == "p")
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $costo_tariffa;
$lista_tariffe_sett .= ",".$costo_tariffa;
} # fine for $num2
$lista_tariffe_sett = substr($lista_tariffe_sett,1);
if ($lista_tariffep_sett) {
$lista_tariffep_sett = substr($lista_tariffep_sett,1);
$lista_tariffe_sett .= ";$lista_tariffep_sett";
} # fine if ($lista_tariffep_sett)
$costo_tariffa = $costo_tariffa_tot;
$nometariffa = $dati_tariffe[$tipotariffa[$n_t]]['nome'];
if ($nometariffa == "") {
$nometariffa = $tipotariffa[$n_t];
$nometariffa_vedi = mex("tariffa","prenota.php").substr($tipotariffa[$n_t],7);
} # fine if ($nometariffa == "")
else $nometariffa_vedi = $nometariffa;
$nometariffa = aggslashdb($nometariffa);
$tariffa = $nometariffa."#@&".$costo_tariffa_tot;
if ($dati_tariffe[$tipotariffa[$n_t]]['moltiplica'] == "p") $tariffa .= "#@&p";
$costo_tariffa_tot_p = punti_in_num($costo_tariffa_tot,$stile_soldi);
$mess_prenota .= "$costo_tariffa_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".mex("tariffa","prenota.php")." \"$nometariffa_vedi\"<br>";

$valori = "abcdefghijkmnpqrstuvwxz";
srand((double)microtime() * 1000000);
unset($cod_prenota);
for ($num2 = 0 ; $num2 < 4 ; $num2++) $cod_prenota .= substr($valori,rand(0,22),1);
if ($conferma[$n_t] == "SI") $conf = "S";
else $conf = "N";
if ($pagato[$n_t] > 0) $conf = "S";
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));

esegui_query("insert into $tableprenota (idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,tariffa,tariffesettimanali,codice,conferma,datainserimento,hostinserimento,utente_inserimento) values ('$idprenota','".$idclienti[$n_t]."','".aggslashdb($appartamento[$n_t])."','".$inizioperiodo[$n_t]."','".$fineperiodo[$n_t]."','$tariffa','$lista_tariffe_sett','$cod_prenota','$conf','$datainserimento','$HOSTNAME','$id_utente_ins')");

if ($numpersone[$n_t]) {
esegui_query("update $tableprenota set num_persone = '".$numpersone[$n_t]."' where idprenota = '$idprenota' ");
} # fine if ($numpersone[$n_t])

if ($dati_tariffe[$tipotariffa[$n_t]]['tasse_percent']) {
esegui_query("update $tableprenota set tasseperc = '".$dati_tariffe[$tipotariffa[$n_t]]['tasse_percent']."' where idprenota = '$idprenota' ");
} # fine if ($dati_tariffe[$tipotariffa[$n_t]]['tasse_percent'])

$caparra[$n_t] = formatta_soldi($caparra[$n_t]);
if (controlla_soldi($caparra[$n_t],"pos") == "NO") $caparra[$n_t] = "";
if (!$caparra[$n_t]) $caparra[$n_t] = calcola_caparra($dati_tariffe,$tipotariffa[$n_t],$inizioperiodo[$n_t],$fineperiodo[$n_t],$costo_tariffa_tot,$lista_tariffe_sett);


# calcolo costi nulli, non letto e senza inventario da associare alla tariffa, tutti come se fossero "se possibile". Oppure il costo imposto per letti aggiuntivi
if ($ins_costi_agg or $costo_letto_imposto != "NO") {
unset($num_letti_agg);
unset($num_costi_presenti);
$oggi_costo = date("Ymd",(time() + (C_DIFF_ORE * 3600)));
for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
$associa_costo = "NO";
$nummoltiplica_ca = "";
if ($ins_costi_agg == "costi_nulli") {
if (!$dati_ca[$num_costo]['valore'] and !$dati_ca[$num_costo]['valore_perc'] and $dati_ca[$num_costo]['letto'] != "s" and !$dati_ca[$num_costo]['tipo_beniinv']) {
$associa_costo_tariffa = associa_costo_a_tariffa($dati_ca,$num_costo,$tipotariffa[$n_t],($fineperiodo[$n_t] - $inizioperiodo[$n_t] + 1));
if ($associa_costo_tariffa == "SI") $associa_costo = "SI";
if ($associa_costo_tariffa != "SI" and !$dati_ca[$num_costo]["incomp_".$tipotariffa[$n_t]]) {
if ($dati_ca[$num_costo]['assegna_con_num_prenota'] and count($inizioperiodo) >= $dati_ca[$num_costo]['assegna_con_num_prenota']) $associa_costo = "SI";
if ($dati_ca[$num_costo]['assegna_da_ini_prenota']) {
$giorni_lim = substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],1);
$limite = date("Ymd",mktime(0,0,0,substr($inizioperiodo_orig[$n_t],5,2),(substr($inizioperiodo_orig[$n_t],8,2) - $giorni_lim),substr($inizioperiodo_orig[$n_t],0,4)));
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) == ">" and $oggi_costo < $limite) $associa_costo = "SI";
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) == "<" and $oggi_costo > $limite) $associa_costo = "SI";
} # fine if ($dati_ca[$num_costo][assegna_da_ini_prenota])
} # fine if ($associa_costo_tariffa != "SI" and...
} # fine if (!$dati_ca[$num_costo]['valore'] and !$dati_ca[$num_costo]['valore_perc'] and...
} # fine if ($ins_costi_agg == "costi_nulli")
if ($costo_letto_imposto != "NO" and $costo_letto_imposto[$n_t]['id'] == $dati_ca[$num_costo]['id'] and $dati_ca[$num_costo]['letto'] == "s") {
$associa_costo = "SI";
$nummoltiplica_ca = $costo_letto_imposto[$n_t]['molt'];
} # fine if ($costo_letto_imposto != "NO" and $costo_letto_imposto[$n_t]['id'] == $dati_ca[$num_costo]['id'] and...

if ($associa_costo == "SI") {
#if ($dati_ca[$num_costo][tipo_val] == "q" and $costo_totale_presente = "SI") $associa_costo = "NO";
$settimane_costo = calcola_settimane_costo($tableperiodi,$dati_ca,$num_costo,$inizioperiodo[$n_t],$fineperiodo[$n_t],"","");
calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica,$inizioperiodo[$n_t],$fineperiodo[$n_t],$settimane_costo,$nummoltiplica_ca,$numpersone[$n_t],$num_letti_agg);
$num_costi_presenti_copia = $num_costi_presenti;
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num_costo,$inizioperiodo[$n_t],$fineperiodo[$n_t],$settimane_costo);
if ($periodo_costo_trovato == "NO") $associa_costo = "NO";
else if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti_copia,$inizioperiodo[$n_t],$fineperiodo[$n_t],$settimane_costo,$moltiplica) == "NO") $associa_costo = "NO";

if ($associa_costo == "SI") {
$num_costi_presenti = $num_costi_presenti_copia;
$idcostiprenota = esegui_query("select max(idcostiprenota) from $tablecostiprenota");
$idcostiprenota = risul_query($idcostiprenota,0,0) + 1;
$tipo_ca = $dati_ca[$num_costo]['tipo'].$dati_ca[$num_costo]['tipo_val'];
$valore_ca = $dati_ca[$num_costo]['valore'];
$valore_perc_ca = $dati_ca[$num_costo]['valore_perc'];
$prezzo_costo_tot = calcola_prezzo_totale_costo($dati_ca,$num_costo,$inizioperiodo[$n_t],$fineperiodo[$n_t],$settimane_costo,$moltiplica,$costo_tariffa,$lista_tariffe_sett,$costo_tariffa_tot,$caparra,$numpersone[$n_t]);
$associasett_ca = $dati_ca[$num_costo]['associasett'];
if ($dati_ca[$num_costo]['var_percentuale'] != "s" and $dati_ca[$num_costo]['tipo_val'] != "f") {
$tipo_ca = $dati_ca[$num_costo]['tipo']."f";
$moltiplica = 1;
if ($dati_ca[$num_costo]['tipo'] == "s") {
$settimane_costo = 1;
$associasett_ca = "n";
} # fine if ($dati_ca[$num_costo][tipo] == "s")
$valore_ca = $prezzo_costo_tot;
$valore_perc_ca = 0;
} # fine if ($dati_ca[$num_costo][var_percentuale] != "s" and...
if ($dati_ca[$num_costo]['var_moltiplica'] == "s") $varmoltiplica_ca = $dati_ca[$num_costo]['moltiplica'].$dati_ca[$num_costo]['molt_max'].$dati_ca[$num_costo]['molt_agg'].",".$dati_ca[$num_costo]['molt_max_num'];
else $varmoltiplica_ca = "cx0,";
if ($dati_ca[$num_costo]['var_numsett'] == "s") $varnumsett_ca = $dati_ca[$num_costo]['numsett_orig'];
else $varnumsett_ca = "c";
if ($dati_ca[$num_costo]['var_periodip'] == "s") $varperiodipermessi_ca = $dati_ca[$num_costo]['periodipermessi_orig'];
else $varperiodipermessi_ca = "";
$varbeniinv_ca = "";
if ($dati_ca[$num_costo]['var_appi'] == "s") $varappincompatibili_ca = $dati_ca[$num_costo]['appincompatibili'];
else $varappincompatibili_ca = "";
if ($dati_ca[$num_costo]['var_tariffea'] == "s") $vartariffeassociate_ca = "p".$dati_ca[$num_costo][$tipotariffa[$n_t]];
else $vartariffeassociate_ca = "";
$vartariffeincomp_ca = "";
if ($dati_ca[$num_costo]['var_tariffei'] == "s") {
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($dati_ca[$num_costo]["incomp_tariffa".$numtariffa] == "i") $vartariffeincomp_ca .= ",".$numtariffa;
} # fine for $numtariffa
if ($vartariffeincomp_ca) $vartariffeincomp_ca = substr($vartariffeincomp_ca,1);
} # fine if ($dati_ca[$num_costo][var_tariffei] == "s")
if ($dati_ca[$num_costo]['var_comb'] == "s") $variazione_ca = $dati_ca[$num_costo]['combina'];
else $variazione_ca = "n";
$variazione_ca .= $dati_ca[$num_costo]['escludi_tot_perc'];
esegui_query("insert into $tablecostiprenota (idcostiprenota,idprenota,tipo,nome,valore,associasett,settimane,moltiplica,letto,idntariffe,variazione,varmoltiplica,varnumsett,varperiodipermessi,varbeniinv,varappincompatibili,vartariffeassociate,vartariffeincomp,datainserimento,hostinserimento,utente_inserimento) values ('$idcostiprenota','$idprenota','$tipo_ca','".aggslashdb($dati_ca[$num_costo]['nome'])."','$valore_ca','$associasett_ca','$settimane_costo','$moltiplica','".$dati_ca[$num_costo]['letto']."','".$dati_ca[$num_costo]['id']."','$variazione_ca','$varmoltiplica_ca','$varnumsett_ca','$varperiodipermessi_ca','$varbeniinv_ca','$varappincompatibili_ca','$vartariffeassociate_ca','$vartariffeincomp_ca','$datainserimento','$HOSTNAME','$id_utente_ins')");
if (substr($tipo_ca,1,1) != "f") esegui_query("update $tablecostiprenota set valore_perc = '$valore_perc_ca', arrotonda = '".$dati_ca[$num_costo]['arrotonda']."' where idcostiprenota = '$idcostiprenota'");
if ($dati_ca[$num_costo]['tasseperc']) esegui_query("update $tablecostiprenota set tasseperc = '".$dati_ca[$num_costo]['tasseperc']."' where idcostiprenota = '$idcostiprenota'");
if (strcmp($dati_ca[$num_costo]['categoria'],"")) esegui_query("update $tablecostiprenota set categoria = '".$dati_ca[$num_costo]['categoria']."' where idcostiprenota = '$idcostiprenota'");
if ($prezzo_costo_tot) {
$prezzo_costo_tot_p = punti_in_num($prezzo_costo_tot,$stile_soldi);
$mess_prenota .= "$prezzo_costo_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
if ($dati_ca[$num_costo]['tipo'] == "u") $mess_prenota .= mex("Costo unico","prenota.php");
if ($dati_ca[$num_costo]['tipo'] == "s") $mess_prenota .= mex("Costo $parola_settimanale","prenota.php");
$mess_prenota .= " \"".$dati_ca[$num_costo]['nome']."\"";
if ($associasett_ca == "s") {
$moltiplica = explode(",",$moltiplica);
$valnummoltiplica_ca = $moltiplica[1];
for ($num2 = 2 ; $num2 < (count($moltiplica) - 1) ; $num2++) if ($moltiplica[$num2] != $valnummoltiplica_ca) $valnummoltiplica_ca = 1;
} # fine if ($associasett_ca == "s")
else $valnummoltiplica_ca = $moltiplica;
if ($valnummoltiplica_ca != 1) $mess_prenota .= " (".mex("moltiplicato per","prenota.php")." $valnummoltiplica_ca)";
$mess_prenota .= "<br>";
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $prezzo_costo_tot;
} # fine if ($prezzo_costo_tot)
} # fine if ($associa_costo == "SI")
} # fine if ($associa_costo == "SI")
} # fine for $num_costo
} # fine if ($ins_costi_agg or $costo_letto_imposto != "NO")


# Appartamenti eliminati dai costi aggiuntivi, verranno aggiunti di nuovo agli appartamenti
# assegnabili quando si modifica la prenotazione (se il costo mantiene gli appartamenti 
# incompatibili con la modifica della prenotazione, allora questi appartamenti verranno tolti 
# di nuovo dal costo quando si modifica la prenotazione, finchè il costo rimarrà associato)
if ($app_eliminati_costi[$n_t]) {
esegui_query("update $tableprenota set incompatibilita = '".aggslashdb($app_eliminati_costi[$n_t])."' where idprenota = '$idprenota' ");
} # fine if ($app_eliminati_costi[$n_t])

$sconto[$n_t] = (double) formatta_soldi($sconto[$n_t]);
if ($sconto[$n_t] and controlla_soldi($sconto[$n_t]) == "SI") {
if ($sconto[$n_t] > $costo_tariffa_tot) $sconto[$n_t] = $costo_tariffa_tot;
esegui_query("update $tableprenota set sconto = '".$sconto[$n_t]."' where idprenota = '$idprenota' ");
$sconto_p = punti_in_num($sconto[$n_t],$stile_soldi);
if (substr($sconto_p,0,1) == "-") $sconto_p = substr($sconto_p,1);
else $mess_prenota .= "-";
$mess_prenota .= "$sconto_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".mex("Sconto","prenota.php")."<br>";
$costo_tariffa_tot = (double) $costo_tariffa_tot - (double) $sconto[$n_t];
} # fine if ($sconto[$n_t] and...

$commissioni[$n_t] = formatta_soldi($commissioni[$n_t]);
if (controlla_soldi($commissioni[$n_t],"pos") == "NO") $commissioni[$n_t] = "";
if (!$commissioni[$n_t]) $commissioni[$n_t] = calcola_commissioni($dati_tariffe,$tipotariffa[$n_t],$inizioperiodo[$n_t],$fineperiodo[$n_t],$lista_tariffe_sett,$sconto[$n_t],0);

esegui_query("update $tableprenota set tariffa_tot = '$costo_tariffa_tot' where idprenota = '$idprenota' ");
$costo_tariffa_tot_p = punti_in_num($costo_tariffa_tot,$stile_soldi);
$mess_prenota .= "<b>$costo_tariffa_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".mex("TOTALE","prenota.php")."</b><br>";

if ($lista_app[$n_t]) {
esegui_query("update $tableprenota set app_assegnabili = '".$lista_app[$n_t]."' where idprenota = '$idprenota' ");
} # fine if ($lista_app[$n_t])

if ($assegnazioneapp[$n_t]) {
esegui_query("update $tableprenota set assegnazioneapp = '".$assegnazioneapp[$n_t]."' where idprenota = '$idprenota' ");
} # fine if ($assegnazioneapp[$n_t])

if ($caparra[$n_t]) {
if ($caparra[$n_t] > $costo_tariffa_tot) $caparra[$n_t] = $costo_tariffa_tot;
esegui_query("update $tableprenota set caparra = '".$caparra[$n_t]."' where idprenota = '$idprenota' ");
$da_pagare = $costo_tariffa_tot - $caparra[$n_t];
$caparra_p = punti_in_num($caparra[$n_t],$stile_soldi);
$da_pagare_p = punti_in_num($da_pagare,$stile_soldi);
$mess_prenota .= "<br>".mex("Caparra","prenota.php").": <b>$caparra_p</b> $Euro (".mex("resto da pagare","prenota.php").": $da_pagare_p $Euro).<br>";
} # fine if ($caparra[$n_t])

$met_trovato = "NO";
if ($met_paga_caparra[$n_t] and $metodi_pagamento) {
for ($num2 = 0 ; $num2 < count($metodi_pagamento) ; $num2++) {
if ($met_paga_caparra[$n_t] == $metodi_pagamento[$num2]) $met_trovato = "SI";
} # fine for $num2
if ($met_trovato == "SI") {
esegui_query("update $tableprenota set metodo_pagamento = '".aggslashdb($met_paga_caparra[$n_t])."' where idprenota = '$idprenota' ");
if (!$caparra) $mess_prenota .= "<br>";
$mess_prenota .= mex("Metodo pagamento caparra","prenota.php").": <b>".$met_paga_caparra[$n_t]."</b>.<br>";
} # fine if ($met_trovato == "SI")
} # fine if ($met_paga_caparra[$n_t] and $metodi_pagamento)

if ($commissioni[$n_t]) {
esegui_query("update $tableprenota set commissioni = '".$commissioni[$n_t]."' where idprenota = '$idprenota' ");
$resto_comm = $costo_tariffa_tot - $commissioni[$n_t];
$commissioni_p = punti_in_num($commissioni[$n_t],$stile_soldi);
$resto_comm_p = punti_in_num($resto_comm,$stile_soldi);
if (!$caparra[$n_t] and $met_trovato == "NO") $mess_prenota .= "<br>";
$mess_prenota .= mex("Commissioni","prenota.php").": <b>$commissioni_p</b> $Euro (".mex("resto commissioni","prenota.php").": $resto_comm_p $Euro).<br>";
} # fine if ($commissioni[$n_t])

if ($origine_prenota[$n_t] and $origini_prenota) {
$orig_trovata = "NO";
for ($num2 = 0 ; $num2 < count($origini_prenota) ; $num2++) {
if ($origine_prenota[$n_t] == $origini_prenota[$num2]) $orig_trovata = "SI";
} # fine for $num2
if ($orig_trovata == "SI") {
$origine_prenota[$n_t] = aggslashdb($origine_prenota[$n_t]);
esegui_query("update $tableprenota set origine = '".$origine_prenota[$n_t]."' where idprenota = '$idprenota' ");
if (!$caparra[$n_t] and $met_trovato == "NO" and !$commissioni[$n_t]) $mess_prenota .= "<br>";
$mess_prenota .= mex("Origine","prenota.php").": <b>".stripslashes($origine_prenota[$n_t])."</b>.<br>";
} # fine if ($orig_trovata == "SI")
} # fine if ($origine_prenota[$n_t] and $origini_prenota)

if ($giorno_stima_checkin[$n_t] and $ora_stima_checkin[$n_t] and $min_stima_checkin[$n_t]) {
if ($tipo_periodi == "g") $giorni_periodo = $lunghezza_periodo;
else $giorni_periodo = ($lunghezza_periodo * 7);
if (controlla_num_pos($giorno_stima_checkin[$n_t]) == "SI" and $giorno_stima_checkin[$n_t] >= 1 and $giorno_stima_checkin[$n_t] <= 7 and $giorno_stima_checkin[$n_t] <= ($giorni_periodo + 1)) {
$data_stima_checkin = esegui_query("select datainizio from $tableperiodi where idperiodi = '".$inizioperiodo[$n_t]."'");
$data_stima_checkin = risul_query($data_stima_checkin,0,'datainizio');
$data_ini_prenota_f = formatta_data($data_stima_checkin,$stile_data);
$anno_dts = substr($data_stima_checkin,0,4);
$mese_dts = substr($data_stima_checkin,5,2);
$giorno_dts = substr($data_stima_checkin,8,2);
$data_stima_checkin = date("Y-m-d",mktime(0,0,0,$mese_dts,($giorno_dts + $giorno_stima_checkin[$n_t] - 1),$anno_dts));
$stima_checkin = $data_stima_checkin." ".$ora_stima_checkin[$n_t].":".$min_stima_checkin[$n_t].":00";
if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/",$stima_checkin)) {
esegui_query("update $tableprenota set checkout = '$stima_checkin' where idprenota = '$idprenota' ");
$mess_prenota .= "<br>".mex("Orario stimato di entrata","prenota.php").": <b>".substr(str_replace($data_ini_prenota_f,"",formatta_data($stima_checkin,$stile_data)),0,-3)."</b>.<br>";
} # fine if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/",$stima_checkin))
} # fine if (controlla_num_pos($giorno_stima_checkin) == "SI" and...
} # fine if ($giorno_stima_checkin[$n_t] and $ora_stima_checkin[$n_t] and $min_stima_checkin[$n_t])

if ($commento[$n_t]) {
$commento[$n_t] = htmlspecialchars($commento[$n_t]);
$mess_prenota .= "<br>".mex("Commento","prenota.php").": ".$commento[$n_t]."<br>";
$commento[$n_t] = aggslashdb($commento[$n_t]);
esegui_query("update $tableprenota set commento = '".$commento[$n_t]."' where idprenota = '$idprenota' ");
} # fine if ($commento[$n_t])

$mess_prenota .= "<br>".mex("Prenotazione","prenota.php")." $idprenota ".mex("inserita","prenota.php")."!<br>";
if ($n_t != $num_tipologie) $mess_prenota .= "<hr style=\"width: 95%\">";

$lista_idprenota .= ",$idprenota";
$lista_idini .= ",".$inizioperiodo[$n_t];
$lista_idfine .= ",".$fineperiodo[$n_t];
#$costo_tot_mess = (double) $costo_tot_mess + (double) $costo_tariffa_tot;
#$caparra_mess = (double) $caparra_mess + (double) $caparra;

} # fine for $n_t

$lista_idprenota = substr($lista_idprenota,1);
$lista_idini = substr($lista_idini,1);
$lista_idfine = substr($lista_idfine,1);
if (@is_array($prenota_vicine)) {
$lista_idprenota_vett = explode(",",$lista_idprenota);
$num_idprenota_vett = count($lista_idprenota_vett);
$num_corr = 0;
for ($num1 = 0 ; $num1 < $num_idprenota_vett ; $num1++) {
$idprenota = $lista_idprenota_vett[$num1];
if ($lista_idprenota_corr) $lista_idprenota_corr .= ",";
$lista_idprenota_corr .= $idprenota;
$num_corr++;
if ($prenota_vicine[$num_corr] or $num1 == ($num_idprenota_vett - 1)) {
if (strstr($lista_idprenota_corr,",")) {
$lista_idprenota_corr_vett = explode(",",$lista_idprenota_corr);
for ($num2 = 0 ; $num2 < count($lista_idprenota_corr_vett) ; $num2++) {
$idprenota = $lista_idprenota_corr_vett[$num2];
$idprenota_vicine = substr(str_replace(",".$idprenota.",",",",",".$lista_idprenota_corr.","),1,-1);
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_vicine' where idprenota = '$idprenota' ");
} # fine for $num2
} # fine if (strstr($lista_idprenota_corr,","))
$lista_idprenota_corr = "";
} # fine if ($prenota_vicine[$num_corr] or $num1 == ($num_idprenota_vett - 1))
} # fine for $num1
} # fine if (@is_array($prenota_vicine))
elseif ($prenota_vicine == "SI") {
$lista_idprenota_vett = explode(",",$lista_idprenota);
for ($num1 = 0 ; $num1 < count($lista_idprenota_vett) ; $num1++) {
$idprenota = $lista_idprenota_vett[$num1];
$idprenota_vicine = substr(str_replace(",".$idprenota.",",",",",".$lista_idprenota.","),1,-1);
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_vicine' where idprenota = '$idprenota' ");
} # fine for $num1
} # fine if ($prenota_vicine == "SI")

$stato['inserita'] = "SI";

} # fine if ($inserire != "NO")

if (!@is_array($stato) or $stato['inserita'] != "SI") $stato['inserita'] = "NO";
$stato['mess_errore'] = $mess_errore;
$stato['mess_regola1'] = $mess_regola1;
$stato['mess_prenota'] = $mess_prenota;
$stato['lista_idprenota'] = $lista_idprenota;
$stato['idinizioperiodo'] = $lista_idini;
$stato['idfineperiodo'] = $lista_idfine;

return $lista_idprenota;

} # fine function inserisci_prenotazione




?>