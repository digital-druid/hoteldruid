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

$pag = "punto_vendita.php";
$titolo = "HotelDruid: Punto Vendita";
$base_js = 1;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include(C_DATI_PATH."/lingua.php");
include("./includes/sett_gio.php");
include("./includes/funzioni_tariffe.php");
include("./includes/funzioni_costi_agg.php");
include("./includes/sett_gio.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablecosti = $PHPR_TAB_PRE."costi".$anno;
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tablecasse = $PHPR_TAB_PRE."casse";
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";


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
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$casse_consentite = risul_query($privilegi_globali_utente,0,'casse_consentite');
$attiva_casse_consentite = substr($casse_consentite,0,1);
if ($attiva_casse_consentite == "s") {
$casse_consentite = explode(",",substr($casse_consentite,2));
unset($casse_consentite_vett);
for ($num1 = 0 ; $num1 < count($casse_consentite) ; $num1++) if ($casse_consentite[$num1]) $casse_consentite_vett[$casse_consentite[$num1]] = "SI";
} # fine if ($attiva_casse_consentite == "s")
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
unset($contratti_consentiti_vett);
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1]) $contratti_consentiti_vett[$contratti_consentiti[$num1]] = "SI";
} # fine if ($attiva_contratti_consentiti == "s")
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_costi_agg = substr($priv_mod_prenota,8,1);
$priv_mod_prenota_iniziate = substr($priv_mod_prenota,11,1);
$priv_mod_prenota_ore = substr($priv_mod_prenota,12,3);
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
$priv_ins_costi = risul_query($privilegi_annuali_utente,0,'priv_ins_costi');
$priv_ins_entrate = substr($priv_ins_costi,1,1);
$priv_persona_ins_costi = substr($priv_ins_costi,3,1);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)

unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) $utenti_gruppi[risul_query($utenti_gruppo,$num2,'idutente')] = 1;
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($id_utente != 1)

else {
$anno_utente_attivato = "SI";
$priv_vedi_tab_prenotazioni = "s";
$vedi_clienti = "SI";
$attiva_casse_consentite = "n";
$attiva_contratti_consentiti = "n";
$priv_mod_prenotazioni = "s";
$priv_mod_costi_agg = "s";
$priv_mod_prenota_iniziate = "s";
$priv_mod_prenota_ore = "000";
$attiva_costi_agg_consentiti = "n";
$attiva_contratti_consentiti = "n";
$priv_ins_entrate = "s";
$priv_persona_ins_costi = "c";
} # fine else if ($id_utente != 1)


if (defined("C_MASSIMO_NUM_COSTI") and C_MASSIMO_NUM_COSTI != 0 and $priv_ins_entrate == "s") {
$num_costi_esistenti = esegui_query("select idcosti from $tablecosti");
$num_costi_esistenti = numlin_query($num_costi_esistenti);
if ($num_costi_esistenti >= (C_MASSIMO_NUM_COSTI + 1)) $priv_ins_entrate = "n";
} # fine if (defined("C_MASSIMO_NUM_COSTI") and C_MASSIMO_NUM_COSTI != 0 and...


if ($anno_utente_attivato == "SI" and $priv_mod_prenotazioni != "n" and $priv_mod_costi_agg == "s") {


if ($priv_ins_nuove_prenota == "n") $show_bar = "NO";
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();

$ordine_inventario = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'ordine_inventario' and idutente = '$id_utente' ");
$ordine_inventario = risul_query($ordine_inventario,0,'valpersonalizza');
if ($ordine_inventario == "ins") $ordine_costi = "";
else $ordine_costi = "nomecostoagg";


$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num'],"NO",$ordine_costi,$tableappartamenti);
$id_periodo_corrente = calcola_id_periodo_corrente($anno);
$attiva_checkin = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'attiva_checkin' and idutente = '$id_utente'");
$attiva_checkin = risul_query($attiva_checkin,0,'valpersonalizza');

function rowbgcolor () {
global $rowbgcolor,$t2row1color,$t2row2color;
if ($rowbgcolor == $t2row2color) $rowbgcolor = $t2row1color;
else $rowbgcolor = $t2row2color;
return $rowbgcolor;
} # fine function rowbgcolor



if ($inprenota and $id_prenota) {
$tabelle_lock = array($tableprenota,$tablecostiprenota,$tablerelinventario);
$altre_tab_lock = array($tableperiodi,$tablecasse);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
} # fine if ($inprenota and $id_prenota)


$d_caparra = 0;
$d_num_persone = 0;


if ($canc_idprenota or $azzera) $id_prenota = "";
if ($id_prenota) {
$id_prenota = aggslashdb($id_prenota);
$dati_prenota = esegui_query("select * from $tableprenota where idprenota = '$id_prenota'");
if (!numlin_query($dati_prenota)) $id_prenota = "";
else {
if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g") {
$utente_inserimento = risul_query($dati_prenota,0,'utente_inserimento');
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $id_prenota = "";
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $id_prenota = "";
} # fine if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g")
if ($priv_mod_prenota_iniziate != "s") {
$id_inizio_prenota = risul_query($dati_prenota,0,'iddatainizio');
if ($id_periodo_corrente >= $id_inizio_prenota) $id_prenota = "";
} # fine if ($priv_mod_prenota_iniziate != "s")
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = risul_query($dati_prenota,0,'datainserimento');
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $id_prenota = "";
} # fine if ($priv_mod_prenota_ore != "000")
if ($attiva_checkin == "SI") {
$checkin = risul_query($dati_prenota,0,'checkin');
$checkout = risul_query($dati_prenota,0,'checkout');
if (!$checkin or $checkout) $id_prenota = "";
} # fine if ($attiva_checkin == "SI")
else {
$id_inizio_prenota = risul_query($dati_prenota,0,'iddatainizio');
$id_fine_prenota = risul_query($dati_prenota,0,'iddatafine');
if ($id_inizio_prenota > $id_periodo_corrente or $id_fine_prenota < ($id_periodo_corrente - 1)) $id_prenota = "";
} # fine else if ($attiva_checkin == "SI")
} # fine else if (!numlin_query($dati_prenota))
if ($id_prenota) {
$idinizioperiodo = risul_query($dati_prenota,0,'iddatainizio');
$idfineperiodo = risul_query($dati_prenota,0,'iddatafine');
$app_prenota = risul_query($dati_prenota,0,'idappartamenti');
$d_tariffa = risul_query($dati_prenota,0,'tariffa');
$d_tariffa = explode("#@&",$d_tariffa);
$d_nome_tariffa = $d_tariffa[0];
$d_costo_tariffa = (double) $d_tariffa[1];
$d_sconto = (double) risul_query($dati_prenota,0,'sconto');
if (!$d_sconto) $d_sconto = (double) 0;
$d_costo_tot = (double) risul_query($dati_prenota,0,'tariffa_tot');
if (!$d_costo_tot) $d_costo_tot = (double) 0;
$d_caparra = risul_query($dati_prenota,0,'caparra');
$d_num_persone = risul_query($dati_prenota,0,'num_persone');
if (!$d_num_persone) $d_num_persone = 0;
$d_tariffesettimanali = risul_query($dati_prenota,0,'tariffesettimanali');
unset($num_letti_agg);
$dati_cap = dati_costi_agg_prenota($tablecostiprenota,$id_prenota);
$d_costo_agg_tot = (double) 0;
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
aggiorna_letti_agg_in_periodi($dati_cap,$numca,$num_letti_agg,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],"","");
$d_prezzo_costo_agg[$numca] = (double) calcola_prezzo_totale_costo($dati_cap,$numca,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],$d_costo_tariffa,$d_tariffesettimanali,($d_costo_tariffa + $d_costo_agg_tot - $d_sconto),$d_caparra,$d_num_persone);
$d_costo_agg_tot = (double) $d_costo_agg_tot + $d_prezzo_costo_agg[$numca];
} # fine for $numca
$d_pagato = risul_query($dati_prenota,0,'pagato');
if (!$d_pagato) $d_pagato = 0;
$tipotariffa = "";
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
$tariffa = "tariffa".$numtariffa;
if ($d_nome_tariffa == $tariffa or $d_nome_tariffa == $dati_tariffe[$tariffa]['nome']) $tipotariffa = $tariffa;
} # fine for $numtariffa
} # fine if ($id_prenota)
else unlock_tabelle($tabelle_lock);
} # fine if ($id_prenota)



$azione = 0;
$testo_azione = "";
$mostra_contr = 0;

if ($azzera) {
$azione = 1;
$id_costi = "";
} # fine if ($azzera)

if ($canc_incassa) {
$azione = 1;
$incassa = 0;
} # fine if ($canc_incassa)

if ($agg_costo and (strcmp($dati_ca['id'][$agg_costo],"") or substr($agg_costo,0,1) == "c") and !$azione) {
$azione = 1;
if (substr($agg_costo,0,1) != "c") {
if ($id_costi) $id_costi .= ",";
$id_costi .= $agg_costo;
} # fine if (substr($agg_costo,0,1) != "c")
else {
$categ = substr($agg_costo,1);
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($dati_ca[$num1]['combina'] == "s" and $dati_ca[$num1]['categoria'] == $categ) {
if ($id_costi) $id_costi .= ",";
$id_costi .= $dati_ca[$num1]['id'];
} # fine if ($dati_ca[$num1]['combina'] == "s" and...
} # fine for $num1
} # fine else if (substr($agg_costo,0,1) != "c")
} # fine if ($agg_costo and (strcmp($dati_ca['id'][$agg_costo],"") or...

if (strcmp($canc_costo,"") and !$azione) {
$azione = 1;
$costi = explode(",",$id_costi);
$num_costi = count($costi);
$id_costi = "";
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) if ($num1 != $canc_costo) $id_costi .= $costi[$num1].",";
$id_costi = substr($id_costi,0,-1);
} # fine if (strcmp($canc_costo,"") and !$azione)

if ((($moltiplica and controlla_num_pos($moltiplica) != "NO") or ($aggiungi and controlla_num_pos($aggiungi) != "NO") or ($sottrai and controlla_num_pos($sottrai) != "NO")) and $id_costi and !$azione) {
$azione = 1;
$costi = explode(",",$id_costi);
$ultimo_costo = $costi[(count($costi) - 1)];
$id_costi = substr($id_costi,0,(-1 * (strlen($ultimo_costo) + 1)));
$ultimo_costo = explode("x",$ultimo_costo);
$molt_ultimo_costo = $ultimo_costo[1];
$ultimo_costo = $ultimo_costo[0];
$num_uc = $dati_ca['id'][$ultimo_costo];
if ($dati_ca[$num_uc]['moltiplica'] == "c") {
if (!$molt_ultimo_costo) $molt_ultimo_costo = 1;
if ($moltiplica) $moltiplica = $molt_ultimo_costo * $moltiplica;
if ($aggiungi) $moltiplica = $molt_ultimo_costo + $aggiungi;
if ($sottrai) {
$moltiplica = $molt_ultimo_costo - $sottrai;
if ($moltiplica < 0) $moltiplica = 0;
} # fine if ($sottrai)
if ($moltiplica) {
if ($id_costi) $id_costi .= ",";
$id_costi .= $ultimo_costo;
if ($moltiplica > 1) $id_costi .= "x$moltiplica";
} # fine if ($moltiplica)
} # fine if ($dati_ca[$num_uc]['moltiplica'] == "c")
else {
if ($aggiungi) $moltiplica = $aggiungi + 1;
for ($num1 = 1 ; $num1 <= $moltiplica ; $num1++) $id_costi .= ",".$ultimo_costo;
if (substr($id_costi,0,1) == ",") $id_costi = substr($id_costi,1);
} # fine else if ($dati_ca[$num_uc]['moltiplica'] == "c")
} # fine if ((($moltiplica and controlla_num_pos($moltiplica) != "NO") or ($aggiungi and...



$val_tot = 0;
$errore_cassa = 0;
$errore_prenota = 0;
unset($errori_costi);
$errori_costi[-1] = 0;
unset($val_costo);
unset($settimane_costo);
unset($n_moltiplica_costo);
unset($moltiplica_costo);
unset($moltiplica_max);
unset($beniinv_presenti);
unset($num_costi_presenti);
unset($calcolabile_js);
$calcolabile_js['nessuno'] = 0;
unset($moltiplicabile_js);

if ($id_costi)  {

$costi = explode(",",$id_costi);
$num_costi = count($costi);
$num_costo = 0;
$testo_costi = "";
$id_costi_orig = $id_costi;
$id_costi = "";

for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$molt_costo = $id_costo[1];
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
if ($dati_ca[$numca]['moltiplica'] != "c") $molt_costo = "";
if (strcmp($numca,"")) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$id_costo] == "SI") {

$molt_costo_orig = $molt_costo;
if (!$molt_costo) $molt_costo = 1;

if ($id_prenota) {
if (str_replace(",$app_prenota,","",",".$dati_ca[$numca]['appincompatibili'].",") != ",".$dati_ca[$numca]['appincompatibili'].",") {
$errori_costi[$num_costo] .= ", ".mex("appartamento incompatibile",'unit.php');
$errore_prenota = 1;
} # fine if (str_replace(",$app_prenota,","",",".$dati_ca[$numca]['appincompatibili'].",") != ",".$dati_ca[$numca]['appincompatibili'].",")
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"1");
if ($periodo_costo_trovato == "NO") {
if ($dati_ca[$numca]['combina'] == "s") continue;
$errori_costi[$num_costo] .= ", ".mex("periodo non permesso",$pag);
$errore_prenota = 1;
} # fine if ($periodo_costo_trovato == "NO")
if ($dati_ca[$numca]["incomp_".$tipotariffa] == "i") {
if ($dati_ca[$numca]['combina'] == "s") continue;
$errori_costi[$num_costo] .= ", ".mex("tariffa incompatibile",$pag);
$errore_prenota = 1;
} # fine if ($dati_ca[$numca]["incomp_".$tipotariffa] == "i")

$id_periodi_costo = "id_periodi_costo".$num_costo;
if ($$id_periodi_costo == "inserire") {
$$id_periodi_costo = "";
for ($num2 = $idinizioperiodo; $num2 <= $idfineperiodo; $num2++) {
if (${"sett".$num2."costo".$num_costo} == "SI") $$id_periodi_costo .= ",".$num2;
} # fine for $num2
if ($$id_periodi_costo) $$id_periodi_costo .= ",";
else $$id_periodi_costo = "nessuno";
} # fine if ($$id_periodi_costo == "inserire")
if ($dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] == "s" and !$$id_periodi_costo) {
echo "<hr style=\"width: 30%; margin-left: 0; text-align: left;\">
".mex("Scegliere $parola_le $parola_settimane in cui applicare il costo aggiuntivo",$pag)." ".$dati_ca[$numca]['nome']."$per_la_prenotazione:<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"punto_vendita.php#finetab\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<table style=\"text-align: center;\">";
for ($num2 = $idinizioperiodo; $num2 <= $idfineperiodo; $num2++) {
$periodo_costo_trovato = "NO";
if ($dati_ca[$numca]['periodipermessi'] == "p") {
for ($num3 = 0 ; $num3 < count($dati_ca[$numca]['sett_periodipermessi_ini']) ; $num3++) {
if ($dati_ca[$numca]['sett_periodipermessi_ini'][$num3] <= $num2 and $dati_ca[$numca]['sett_periodipermessi_fine'][$num3] >= $num2) $periodo_costo_trovato = "SI";
} # fine for $num3
} # fine if ($dati_ca[$num_costo]['periodipermessi'] == "p")
else $periodo_costo_trovato = "SI";
if ($periodo_costo_trovato == "SI") {
$date_sett_costo = esegui_query("select datainizio,datafine from $tableperiodi where idperiodi = '$num2'");
echo "<tr><td style=\"height: 50px; width: 30px\">
<input type=\"checkbox\" name=\"sett$num2"."costo$num_costo\" id=\"sett$num2\" value=\"SI\"></td><td><label for=\"sett$num2\">
".mex("dal",$pag)." ".formatta_data(risul_query($date_sett_costo,0,'datainizio'),$stile_data)." ".mex("al",$pag)." 
 ".formatta_data(risul_query($date_sett_costo,0,'datafine'),$stile_data)."</label></td></tr>";
} # fine if ($periodo_costo_trovato == "SI")
} # fine for $num2
echo "</table>";
$$id_periodi_costo = "inserire";
for ($numca2 = 0 ; $numca2 <= $num_costo ; $numca2++) if (${"id_periodi_costo".$numca2}) echo "<input type=\"hidden\" name=\"id_periodi_costo$numca2\" value=\"".${"id_periodi_costo".$numca2}."\">";
echo "<input type=\"hidden\" name=\"id_costi\" value=\"$id_costi_orig\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$id_prenota\">
<input type=\"hidden\" name=\"categoria\" value=\"$categoria\">
<button class=\"pos\" type=\"submit\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("Continua",$pag)."</b></button>
</div></form><hr align=\"left\" style=\"width: 30%; margin-left: 0; text-align: left;\">";
$blocca_schermo = 1;
$id_costi = "";
break;
} # fine if ($dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] == "s" and...
else {
$id_periodi_costo_aux = $$id_periodi_costo;
$settimane_costo[$num_costo] = calcola_settimane_costo($tableperiodi,$dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$id_periodi_costo_aux,"1");
aggiorna_letti_agg_in_periodi($dati_ca,$numca,$num_letti_agg,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num_costo],"",$molt_costo,$d_num_persone);
} # fine else if ($dati_ca[$numca]['numsett'] == "c" and $dati_ca[$numca]['associasett'] == "s" and...

if (($dati_ca[$numca]['moltiplica'] == "p" or $dati_ca[$numca]['moltiplica'] == "t") and !$d_num_persone) {
$errori_costi[$num_costo] .= ", ".mex("manca numero di persone",$pag);
$errore_prenota = 1;
$errore_cassa = 1;
} # fine if (($dati_ca[$numca][moltiplica] == "p" or $dati_ca[$numca][moltiplica] == "t") and !$d_num_persone)


} # fine if ($id_prenota)

else {
$idinizioperiodo = $id_periodo_corrente;
$idfineperiodo = $id_periodo_corrente;
$settimane_costo[$num_costo] = calcola_settimane_costo($tableperiodi,$dati_ca,$numca,$idinizioperiodo,$idfineperiodo,",$id_periodo_corrente,","1");
calcola_moltiplica_costo($dati_ca,$numca,$moltiplica_costo[$num_costo],$idinizioperiodo,$idfineperiodo,$settimane_costo[$num_costo],$molt_costo,"1","");
if (trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"1") == "NO") {
if ($dati_ca[$numca]['combina'] == "s") continue;
$errori_costi[$num_costo] .= ", ".mex("periodo non permesso",$pag);
$errore_prenota = 1;
$errore_cassa = 1;
} # fine if  (trova_periodo_permesso_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,"1") == "NO")
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num_costo],$moltiplica_costo[$num_costo],"","") == "NO") {
$errori_costi[$num_costo] .= ", ".mex("numero massimo raggiunto",$pag);
$errore_prenota = 1;
$errore_cassa = 1;
} # fine if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num_costo],$moltiplica_costo[$num_costo],"","") == "NO")
if ($dati_ca[$numca]['tipo_beniinv'] == "mag") {
$nrc = "";
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca,$beniinv_presenti,$nrc,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo[$num_costo],$moltiplica_costo[$num_costo],"");
if ($risul != "SI") {
$errori_costi[$num_costo] .= ", ".mex("bene non presente in inventario",$pag);
$errore_prenota = 1;
$errore_cassa = 1;
} # fine if ($risul != "SI")
} # fine if ($dati_ca[$numca]['tipo_beniinv'] == "mag")
if ($dati_ca[$numca]['molt_max'] == "n" and $molt_costo > $dati_ca[$numca]['molt_max_num']) {
$errori_costi[$num_costo] .= ", ".mex("moltiplicato per più di",$pag)." ".$dati_ca[$numca]['molt_max_num'];
$errore_prenota = 1;
$errore_cassa = 1;
} # fine if ($dati_ca[$numca]['molt_max'] == "n" and $molt_costo > $dati_ca[$numca]['molt_max_num'])
} # fine else if ($id_prenota)

if ($dati_ca[$numca]['tipo_val'] == "f" and $dati_ca[$numca]['tipo'] == "u" and $dati_ca[$numca]['moltiplica'] != "p" and $dati_ca[$numca]['moltiplica'] != "t") {
$val_costo[$num_costo] = $dati_ca[$numca]['valore'];
if ($molt_costo) $val_costo[$num_costo] = $val_costo[$num_costo] * $molt_costo;
$val_tot += $val_costo[$num_costo];
} # fine if ($dati_ca[$numca]['tipo_val'] == "f" and $dati_ca[$numca]['tipo'] == "u" and...
else {
if (!$id_prenota) {
$val_costo[$num_costo] = "?";
$errore_cassa = 1;
} # fine if (!$id_prenota)
} # fine else if ($dati_ca[$numca]['tipo_val'] == "f" and $dati_ca[$numca]['tipo'] == "u" and...

if ($dati_ca[$numca]['tipo_val'] == "t" or $dati_ca[$numca]['tipo_val'] == "r") $calcolabile_js['nessuno'] = 1;
if (!$calcolabile_js['nessuno']) {
$calcolabile_js[$num_costo] = 1;
if ($errori_costi[$num_costo]) $calcolabile_js[$num_costo] = 0;
if ($dati_ca[$numca]['tipo'] != "u" or $dati_ca[$num1]['tipo_val'] != "f") $calcolabile_js[$num_costo] = 0;
if ($dati_ca[$numca]['letto'] == "s" or $dati_ca[$numca]['numlimite'] or $dati_ca[$numca]['tipo_beniinv'] or $dati_ca[$numca]['periodipermessi'] or $dati_ca[$numca]['appincompatibili']) $calcolabile_js[$num_costo] = 0;
if ($dati_ca[$numca]['moltiplica'] != "1" and $dati_ca[$numca]['moltiplica'] != "c") $calcolabile_js[$num_costo] = 0;
if ($dati_ca[$numca]['moltiplica'] == "c") $moltiplicabile_js[$num_costo] = 1;
else $moltiplicabile_js[$num_costo] = 0;
} # fine if (!$calcolabile_js['nessuno'])

if ($id_costi) $id_costi .= ",";
$id_costi .= $id_costo;
if ($molt_costo_orig) $id_costi .= "x$molt_costo_orig";
if ($testo_costi) $testo_costi .= ", ";
$testo_costi .= $dati_ca[$numca]['nome'];
if ($molt_costo_orig) $testo_costi .= " x$molt_costo_orig";

$num_costo++;
} # fine if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$id_costo] == "SI")
} # fine if (strcmp($numca,""))
} # fine for $num1


if ($id_prenota and $id_costi) {
$costi = explode(",",$id_costi);
$num_costi = count($costi);
$n_costo_agg_tot = 0;
unset($num_ripetizioni_costo);

for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
if ($dati_cap[$numca]['moltiplica'] == "t") {
calcola_moltiplica_costo($dati_cap,$numca,$n_moltiplica_costo[$numca],$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],"",$d_num_persone,$num_letti_agg);
$num_costo = $dati_ca['id'][$dati_cap[$numca]['idntariffe']];
if ($dati_ca[$num_costo]['id'] == $dati_cap[$numca]['idntariffe'] and $dati_cap[$numca]['nome'] == $dati_ca[$num_costo]['nome'] and $dati_ca[$num_costo]['numlimite']) $num_limite = (string) $dati_ca[$num_costo]['numlimite'];
else $num_limite = (string) 0;
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_cap,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$n_moltiplica_costo[$numca],$num_limite,"") == "NO") {
$errori_costi[-1] .= ", ".mex("numero massimo raggiunto",$pag);
$errore_prenota = 1;
} # fine if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_cap,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$n_moltiplica_costo[$numca],$num_limite,"") == "NO")
if ($dati_cap[$numca]['tipo_beniinv'] == "mag") {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_cap,$numca,$beniinv_presenti,$num_ripetizioni_costo['cap'][$numca],"SI",$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$n_moltiplica_costo[$numca],"");
if ($risul != "SI") {
$errori_costi[-1] .= ", ".mex("bene non presente in inventario",$pag);
$errore_prenota = 1;
$errore_cassa = 1;
} # fine if ($risul != "SI")
} # fine if ($dati_cap[$numca]['tipo_beniinv'] == "mag")
} # fine if ($dati_cap[$numca]['moltiplica'] == "t")
else $n_moltiplica_costo[$numca] = $dati_cap[$numca]['moltiplica_costo'];
} # fine for $numca

for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$molt_costo = $id_costo[1];
if (!$molt_costo) $molt_costo = 1;
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
$moltiplica_max[$num1] = calcola_moltiplica_costo($dati_ca,$numca,$moltiplica_costo[$num1],$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$molt_costo,$d_num_persone,$num_letti_agg);
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$moltiplica_costo[$num1],"","") == "NO") {
$errori_costi[$num1] .= ", ".mex("numero massimo raggiunto",$pag);
$errore_prenota = 1;
} # fine if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$numca,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$moltiplica_costo[$num1],"","") == "NO")
if ($dati_ca[$numca]['tipo_beniinv']) {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$numca,$beniinv_presenti,$num_ripetizioni_costo[$num1],"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$moltiplica_costo[$num1],"");
if ($risul != "SI") {
$errori_costi[$num1] .= ", ".mex("bene non presente in inventario",$pag);
$errore_prenota = 1;
if ($dati_ca[$numca]['tipo_beniinv'] != "app") $errore_cassa = 1;
} # fine if ($risul != "SI")
} # fine if ($dati_ca[$numca]['tipo_beniinv'])
if ($dati_ca[$numca]['moltiplica'] == "c" and $dati_ca[$numca]['molt_max'] != "x") {
$num_max = 0;
if ($dati_ca[$numca]['molt_max'] == "n") $num_max = $dati_ca[$numca]['molt_max_num'];
if ($dati_ca[$numca]['molt_max'] != "n" and $d_num_persone) $num_max = $d_num_persone;
if ($dati_ca[$numca]['molt_max'] == "t" and $num_letti_agg['max']) $num_max += $num_letti_agg['max'];
if ($num_max) {
if ($dati_ca[$numca]['molt_max'] != "n" and $dati_ca[$numca]['molt_max_num']) $num_max = $num_max - $dati_ca[$numca]['molt_max_num'];
if ($molt_costo > $num_max) {
$errori_costi[$num_costo] .= ", ".mex("moltiplicato per più di",$pag)." $num_max";
$errore_prenota = 1;
if ($dati_ca[$numca]['molt_max'] == "n") $errore_cassa = 1;
} # fine if ($molt_costo > $num_max)
} # fine if ($num_max)
} # fine if ($dati_ca[$numca]['moltiplica'] == "c" and $dati_ca[$num1]['molt_max'] != "x")
} # fine for $num1

# calcolo prezzo per costi già presenti
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
if ($dati_cap[$numca]['tipo_val'] != "t" and $dati_cap[$numca]['tipo_val'] != "r") {
if ($dati_cap[$numca]['moltiplica'] == "t") {
$n_prezzo_costo_agg[$numca] = (double) calcola_prezzo_totale_costo($dati_cap,$numca,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$n_moltiplica_costo[$numca],$d_costo_tariffa,$d_tariffesettimanali,1,$d_caparra,$d_num_persone);
if ($n_prezzo_costo_agg[$numca] != $d_prezzo_costo_agg[$numca]) $val_costo[-1] += $n_prezzo_costo_agg[$numca] - $d_prezzo_costo_agg[$numca];
} # fine if ($dati_cap[$numca]['moltiplica'] == "t")
else $n_prezzo_costo_agg[$numca] = $d_prezzo_costo_agg[$numca];
$n_costo_agg_tot = $n_costo_agg_tot + $n_prezzo_costo_agg[$numca];
} # fine if $dati_cap[$numca][tipo_val] != "t" and $dati_cap[$numca][tipo_val] != "r")
} # fine for $numca
# calcolo prezzo per costi nuovi
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$molt_costo = $id_costo[1];
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
if ($dati_ca[$numca]['tipo_val'] != "f" or $dati_ca[$numca]['tipo'] != "u" or $dati_ca[$numca]['moltiplica'] == "p" or $dati_ca[$numca]['moltiplica'] == "t") {
if ($dati_ca[$numca]['tipo_val'] != "t" and $dati_ca[$numca]['tipo_val'] != "r") {
$val_costo[$num1] = (double) calcola_prezzo_totale_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$moltiplica_costo[$num1],$d_costo_tariffa,$d_tariffesettimanali,1,$d_caparra,$d_num_persone);
$n_costo_agg_tot = $n_costo_agg_tot + $val_costo[$num1];
$val_tot += $val_costo[$num1];
} # fine if ($dati_ca[$numca]['tipo_val'] != "t" and $dati_ca[$numca]['tipo_val'] != "r")
} # fine if ($dati_ca[$numca]['tipo_val'] != "f" or $dati_ca[$numca]['tipo'] != "u" or...
else $n_costo_agg_tot = $n_costo_agg_tot + $val_costo[$num1];
} # fine for $num1

# calcolo prezzo per costi percentuali sul resto della caparra già presenti
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
if ($dati_cap[$numca]['tipo_val'] == "r") {
$calcolabile_js['nessuno'] = 1;
$costo_totale_provvisorio = $d_costo_tariffa + $n_costo_agg_tot - $d_sconto;
$n_prezzo_costo_agg[$numca] = (double) calcola_prezzo_totale_costo($dati_cap,$numca,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$n_moltiplica_costo[$numca],$d_costo_tariffa,$d_tariffesettimanali,$costo_totale_provvisorio,$d_caparra,$d_num_persone);
if ($d_prezzo_costo_agg[$numca] != $n_prezzo_costo_agg[$numca]) $val_costo[-1] += $n_prezzo_costo_agg[$numca] - $d_prezzo_costo_agg[$numca];
$n_costo_agg_tot = $n_costo_agg_tot + $n_prezzo_costo_agg[$numca];
} # fine if ($dati_cap[$numca][tipo_val] == "r")
} # fine for $numca
# calcolo prezzo per costi percentuali sul resto della caparra nuovi
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
if ($dati_ca[$numca]['tipo_val'] == "r") {
$costo_totale_provvisorio = $d_costo_tariffa + $n_costo_agg_tot - $d_sconto;
$val_costo[$num1] = (double) calcola_prezzo_totale_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$moltiplica_costo[$num1],$d_costo_tariffa,$d_tariffesettimanali,$costo_totale_provvisorio,$d_caparra,$d_num_persone);
$n_costo_agg_tot = $n_costo_agg_tot + $val_costo[$num1];
$val_tot += $val_costo[$num1];
} # fine if ($dati_ca[$numca][tipo_val] == "r")
} # fine for $num1

# calcolo prezzo per costi percentuali sul totale già presenti
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
if ($dati_cap[$numca]['tipo_val'] == "t") {
$calcolabile_js['nessuno'] = 1;
$costo_totale_provvisorio = $d_costo_tariffa + $n_costo_agg_tot - $d_sconto;
$n_prezzo_costo_agg[$numca] = (double) calcola_prezzo_totale_costo($dati_cap,$numca,$idinizioperiodo,$idfineperiodo,$dati_cap[$numca]['settimane'],$n_moltiplica_costo[$numca],$d_costo_tariffa,$d_tariffesettimanali,$costo_totale_provvisorio,$d_caparra,$d_num_persone);
if ($d_prezzo_costo_agg[$numca] != $n_prezzo_costo_agg[$numca]) $val_costo[-1] += $n_prezzo_costo_agg[$numca] - $d_prezzo_costo_agg[$numca];
$n_costo_agg_tot = $n_costo_agg_tot + $n_prezzo_costo_agg[$numca];
} # fine if ($dati_cap[$numca][tipo_val] == "t")
} # fine for $numca
# calcolo prezzo per costi percentuali sul totale nuovi
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
if ($dati_ca[$numca]['tipo_val'] == "t") {
$costo_totale_provvisorio = $d_costo_tariffa + $n_costo_agg_tot - $d_sconto;
$val_costo[$num1] = (double) calcola_prezzo_totale_costo($dati_ca,$numca,$idinizioperiodo,$idfineperiodo,$settimane_costo[$num1],$moltiplica_costo[$num1],$d_costo_tariffa,$d_tariffesettimanali,$costo_totale_provvisorio,$d_caparra,$d_num_persone);
$n_costo_agg_tot = $n_costo_agg_tot + $val_costo[$num1];
$val_tot += $val_costo[$num1];
} # fine if ($dati_ca[$numca][tipo_val] == "t")
} # fine for $num1


} # fine if ($id_prenota and $id_costi)

} # fine if ($id_costi)
elseif ($id_prenota) $calcolabile_js['nessuno'] = 1;




if (!$blocca_schermo) {


if ($id_costi and !$errore_cassa) {
unset($tabelle_lock);
if ($incassa and $priv_ins_entrate == "s" and !$azione) {
$tabelle_lock = array($tablecosti,$tablerelinventario);
$altre_tab_lock = array($tablepersonalizza,$tableutenti,$tablecasse);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
} # fine if ($incassa and $priv_ins_entrate == "s" and !$azione)
$casse = esegui_query("select * from $tablecasse order by idcasse");
$num_casse = numlin_query($casse);
$num_casse_attive = 0;
unset($id_casse);
unset($nomi_casse);
$cassa_trovata = 0;
for ($num1 = 0 ; $num1 < $num_casse ; $num1++) {
$id_cassa = risul_query($casse,$num1,'idcasse');
if ($attiva_casse_consentite == "n" or $casse_consentite_vett[$id_cassa] == "SI") {
if ($id_cassa == 1) $nome_cassa = "";
else $nome_cassa = risul_query($casse,$num1,'nome_cassa');
$num_casse_attive++;
$id_casse[$num_casse_attive] = $id_cassa;
$nomi_casse[$id_cassa] = $nome_cassa;
if ($id_cassa_sel == $id_cassa) $cassa_trovata = 1;
} # fine if ($attiva_casse_consentite == "n" or $casse_consentite_vett[$id_cassa] == "SI")
} # fine for $num1
if (!$num_casse_attive) {
$errore_cassa = 1;
if ($tabelle_lock) unlock($tabelle_lock);
} # fine if (!$num_casse_attive)
if (!$cassa_trovata) $id_cassa_sel = "";
} # fine if ($id_costi and !$errore_cassa)


if ($incassa and !$errore_cassa and $id_costi and $priv_ins_entrate == "s" and !$azione) {
$azione = 1;

$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '$id_utente'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
if (($num_casse_attive > 1 or $metodi_pagamento) and !$id_cassa_sel) {
$testo_azione = "<input type=\"hidden\" name=\"id_costi\" value=\"$id_costi\">
<input type=\"hidden\" name=\"incassa\" value=\"1\">
<input type=\"hidden\" name=\"categoria\" value=\"$categoria\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$id_prenota\">";
if ($metodi_pagamento) {
$testo_azione .= "<br>".mex("Metodo pagamento",$pag).":<br><br>
<select name=\"metodo_pagamento_sel\" style=\"font-size: xx-large;\">
<option value=\"\">----</option>";
$metodi_pagamento = explode(",",$metodi_pagamento);
for ($num1 = 0 ; $num1 < count($metodi_pagamento) ; $num1++) $testo_azione .= "<option value=\"".$metodi_pagamento[$num1]."\"$sel>".$metodi_pagamento[$num1]."</option>";
$testo_azione .= "</select>";
} # fine if ($metodi_pagamento)
for ($num1 = 1 ; $num1 <= $num_casse_attive ; $num1++) {
$testo_azione .= "<button class=\"pos\" type=\"submit\" name=\"id_cassa_sel\" value=\"".$id_casse[$num1]."\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>";
if ($id_casse[$num1] == 1) $testo_azione .= mex("in cassa principale",$pag)."</b></button>";
else $testo_azione .= mex("in cassa",$pag)." \"".$nomi_casse[$id_casse[$num1]]."\"</b></button>";
} # fine for $num1
$testo_azione .= "<button class=\"pos\" type=\"submit\" name=\"canc_incassa\" value=\"1\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("Annulla",$pag)."</b></button>";
} # fine if (($num_casse_attive > 1 or $metodi_pagamento) and !$id_cassa_sel)

else {
if ($num_casse_attive == 1) $id_cassa = $id_casse[1];
else $id_cassa = $id_cassa_sel;
$nome_cassa = $nomi_casse[$id_cassa];
if (strcmp($metodo_pagamento_sel,"")) {
if (get_magic_quotes_gpc()) $metodo_pagamento_sel = stripslashes($metodo_pagamento_sel);
$metodo_pagamento_sel = htmlspecialchars($metodo_pagamento_sel);
if (str_replace(",$metodo_pagamento_sel,",",",",$metodi_pagamento,") == ",$metodi_pagamento,") $metodo_pagamento_sel = "";
} # fine if (strcmp($metodo_pagamento_sel,""))
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$val_entrata = formatta_soldi($val_tot);
$persona_costo = "";
if ($priv_persona_ins_costi == "u") {
$nome_utente = esegui_query("select * from $tableutenti where idutenti = '$id_utente'");
$persona_costo = risul_query($nome_utente,0,'nome_utente');
} # fine if ($priv_persona_ins_costi == "u")
$provenienza_costo = "";
$idcosti = esegui_query("select max(idcosti) from $tablecosti");
$idcosti = risul_query($idcosti,0,0) + 1;
esegui_query("insert into $tablecosti (idcosti,nome_costo,val_costo,tipo_costo,nome_cassa,persona_costo,provenienza_costo,metodo_pagamento,datainserimento,hostinserimento,utente_inserimento) values ('$idcosti','".aggslashdb($testo_costi)."','$val_entrata','e','".aggslashdb($nome_cassa)."','".aggslashdb($persona_costo)."','".aggslashdb($provenienza_costo)."','".aggslashdb($metodo_pagamento_sel)."','$datainserimento','$HOSTNAME','$id_utente') ");
$testo_azione = "<br>".mex("I costi sono stati inseriti",$pag)." <b>";
if ($id_cassa == 1) $testo_azione .= mex("nella cassa principale",$pag);
else $testo_azione .= mex("nella cassa",$pag)." \"$nome_cassa\"";
$testo_azione .= "</b>.<br><br>
<button class=\"pos\" type=\"submit\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("OK",$pag)."</b></button>";
$mostra_contr = 1;

$costi = explode(",",$id_costi);
$num_costi = count($costi);
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$molt_costo = $id_costo[1];
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
if ($dati_ca[$numca]['tipo_beniinv'] == "mag") {
$idmag = $dati_ca[$numca]['mag_beniinv'];
for ($num2 = 0 ; $num2 < $dati_ca[$numca]['num_beniinv'] ; $num2++) {
$id_beneinv = $dati_ca[$numca]['id_beneinv'][$num2];
esegui_query("update $tablerelinventario set quantita = '".$beniinv_presenti["mag".$idmag][$id_beneinv]."' where idbeneinventario = '$id_beneinv' and idmagazzino = '$idmag' ");
} # fine for $num2
} # fine ($dati_ca[$numca]['tipo_beniinv'] == "mag")
} # fine for $num1
} # fine else if (($num_casse_attive > 1 or $metodi_pagamento) and !$nome_cassa_sel)

unlock_tabelle($tabelle_lock);
} # fine if ($incassa and !$errore_cassa and...


if ($inprenota and !$errore_prenota and !$azione) {
$azione = 1;
if (!$id_prenota) {
if ($attiva_checkin == "SI") $dati_prenota = esegui_query("select * from $tableprenota where checkin is not NULL and checkout is NULL order by idappartamenti");
else $dati_prenota = esegui_query("select * from $tableprenota where iddatainizio <= '$id_periodo_corrente' and iddatafine >= '".($id_periodo_corrente - 1)."' order by idappartamenti ");
$num_prenota = numlin_query($dati_prenota);
$select_prenota = "";
for ($num1 = 0 ; $num1 < $num_prenota ; $num1++) {
$modifica_pren = 1;
if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g") {
$utente_inserimento = risul_query($dati_prenota,$num1,'utente_inserimento');
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $modifica_pren = 0;
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $modifica_pren = 0;
} # fine if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g")
if ($priv_mod_prenota_iniziate != "s") {
$id_inizio_prenota = risul_query($dati_prenota,$num1,'iddatainizio');
if ($id_periodo_corrente >= $id_inizio_prenota) $modifica_pren = 0;
} # fine if ($priv_mod_prenota_iniziate != "s")
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = risul_query($dati_prenota,$num1,'datainserimento');
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $modifica_pren = 0;
} # fine if ($priv_mod_prenota_ore != "000")
if ($modifica_pren) {
$id_prenota = risul_query($dati_prenota,$num1,'idprenota');
$app_prenota = risul_query($dati_prenota,$num1,'idappartamenti');
$cliente_vedi = "";
if ($vedi_clienti != "NO") {
$ospiti = esegui_query("select idclienti from $tablerclientiprenota where idprenota = '$id_prenota' order by num_ordine ");
if (numlin_query($ospiti)) $id_clienti = risul_query($ospiti,0,'idclienti');
else $id_clienti = risul_query($dati_prenota,$num1,'idclienti');
$dati_cliente = esegui_query("select * from $tableclienti where idclienti = '$id_clienti' ");
$mostra_cliente = "SI";
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$utente_inserimento = risul_query($dati_cliente,0,'utente_inserimento');
if ($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) $mostra_cliente = "NO";
if ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento]) $mostra_cliente = "NO";
} # fine elseif ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI")
if ($mostra_cliente != "NO") {
$cliente_vedi = " (".risul_query($dati_cliente,0,'cognome');
if (risul_query($dati_cliente,0,'nome')) $cliente_vedi .= ", ".risul_query($dati_cliente,0,'nome');
$cliente_vedi .= ")";
} # fine if ($mostra_cliente != "NO")
} # fine if ($vedi_clienti != "NO")
$select_prenota .= "<option value=\"$id_prenota\">$app_prenota$cliente_vedi</option>";
} # fine if ($modifica_pren)
} # fine for $num1
if ($select_prenota) {
$testo_azione = "<input type=\"hidden\" name=\"id_costi\" value=\"$id_costi\">
<input type=\"hidden\" name=\"categoria\" value=\"$categoria\">";
/*if ($id_costi) {
$costi = explode(",",$id_costi);
$num_costi = count($costi);
for ($numca2 = 0 ; $numca2 < $num_costi ; $numca2++) if (${"id_periodi_costo".$numca2}) $testo_azione .= "<input type=\"hidden\" name=\"id_periodi_costo$numca2\" value=\"".${"id_periodi_costo".$numca2}."\">";
} # fine if ($id_costi)*/
$testo_azione .= "<br>".mex("Calcola i costi aggiuntivi sulla prenotazione dell'appartamento",'unit.php').":<br><br>
<select name=\"id_prenota\" style=\"font-size: xx-large;\">
$select_prenota</select>
<button class=\"pos\" type=\"submit\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("Calcola",$pag)."</b></button>";
if ($id_costi) $testo_azione .= "<button class=\"pos\" type=\"submit\" name=\"inprenota\" value=\"1\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("Inserisci i costi ora",$pag)."</b></button>";
$testo_azione .= "<button class=\"pos\" type=\"submit\" name=\"canc_idprenota\" value=\"1\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("Annulla",$pag)."</b></button>";
} # fine if ($select_prenota)
else {
if ($attiva_checkin == "SI") $testo_azione = "<br>".mex("Attualmente non ci sono prenotazioni che abbiano registrato l'entrata",$pag).".<br><br>";
else $testo_azione = "<br>".mex("Oggi non ci sono prenotazioni",$pag).".<br><br>";
$testo_azione .= "<input type=\"hidden\" name=\"id_costi\" value=\"$id_costi\">
<input type=\"hidden\" name=\"categoria\" value=\"$categoria\">
<button class=\"pos\" type=\"submit\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("OK",$pag)."</b></button>";
} # fine else if ($select_prenota)
} # fine if (!$id_prenota)

elseif ($id_costi) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$idcostiprenota = esegui_query("select max(idcostiprenota) from $tablecostiprenota");
$idcostiprenota = risul_query($idcostiprenota,0,0);

aggiorna_beniinv_presenti($tablerelinventario,$beniinv_presenti);

for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
if ($n_moltiplica_costo[$numca] != $dati_cap[$numca]['moltiplica_costo']) {
esegui_query("update $tablecostiprenota set moltiplica = '".$n_moltiplica_costo[$numca]."' where idcostiprenota = '".$dati_cap[$numca]['id']."' and idprenota = '$id_prenota' ");
} # fine if ($n_moltiplica_costo[$numca] != $dati_cap[$numca]['moltiplica_costo'])
} # fine for $numca

for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$id_costo = explode("x",$costi[$num1]);
$id_costo = $id_costo[0];
$numca = $dati_ca['id'][$id_costo];
$sett_costo = $settimane_costo[$num1];
$moltiplica = $moltiplica_costo[$num1];
$idcostiprenota++;
$tipo_ca = $dati_ca[$numca]['tipo'].$dati_ca[$numca]['tipo_val'];
$valore_ca = $dati_ca[$numca]['valore'];
$valore_perc_ca = $dati_ca[$numca]['valore_perc'];
$associasett_ca = $dati_ca[$numca]['associasett'];
if ($dati_ca[$numca]['var_percentuale'] != "s" and $dati_ca[$numca]['tipo_val'] != "f") {
$tipo_ca = $dati_ca[$numca]['tipo']."f";
$moltiplica = 1;
if ($dati_ca[$numca]['tipo'] == "s") {
$sett_costo = 1;
$associasett_ca = "n";
} # fine if ($dati_ca[$numca][tipo] == "s")
$valore_ca = $val_costo[$num1];
$valore_perc_ca = 0;
} # fine if ($dati_ca[$numca][var_percentuale] != "s" and...
if ($dati_ca[$numca]['var_moltiplica'] == "s") $varmoltiplica_ca = $dati_ca[$numca]['moltiplica'].$dati_ca[$numca]['molt_max'].$dati_ca[$numca]['molt_agg'].",".$dati_ca[$numca]['molt_max_num'];
else $varmoltiplica_ca = "cx0,";
if ($dati_ca[$numca]['var_numsett'] == "s") $varnumsett_ca = $dati_ca[$numca]['numsett_orig'];
else $varnumsett_ca = "c";
if ($dati_ca[$numca]['var_periodip'] == "s") $varperiodipermessi_ca = $dati_ca[$numca]['periodipermessi_orig'];
else $varperiodipermessi_ca = "";
if ($dati_ca[$numca]['var_beniinv'] == "s") $varbeniinv_ca = $num_ripetizioni_costo[$num1].";".$dati_ca[$numca]['beniinv_orig'];
else $varbeniinv_ca = "";
if ($dati_ca[$numca]['var_appi'] == "s") $varappincompatibili_ca = $dati_ca[$numca]['appincompatibili'];
else $varappincompatibili_ca = "";
if ($dati_ca[$numca]['var_tariffea'] == "s") $vartariffeassociate_ca = $dati_ca[$numca]["tipo_associa_".$tipotariffa].$dati_ca[$numca][$tipotariffa];
else $vartariffeassociate_ca = "";
$vartariffeincomp_ca = "";
if ($dati_ca[$numca]['var_tariffei'] == "s") {
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($dati_ca[$numca]["incomp_tariffa".$numtariffa] == "i") $vartariffeincomp_ca .= ",".$numtariffa;
} # fine for $numtariffa
if ($vartariffeincomp_ca) $vartariffeincomp_ca = substr($vartariffeincomp_ca,1);
} # fine if ($dati_ca[$num_costo][var_tariffei] == "s")
esegui_query("insert into $tablecostiprenota (idcostiprenota,idprenota,tipo,nome,valore,associasett,settimane,moltiplica,letto,idntariffe,varmoltiplica,varnumsett,varperiodipermessi,varbeniinv,varappincompatibili,vartariffeassociate,vartariffeincomp,datainserimento,hostinserimento,utente_inserimento) values ('$idcostiprenota','$id_prenota','$tipo_ca','".aggslashdb($dati_ca[$numca]['nome'])."','$valore_ca','$associasett_ca','$sett_costo','$moltiplica','".$dati_ca[$numca]['letto']."','$id_costo','$varmoltiplica_ca','$varnumsett_ca','$varperiodipermessi_ca','$varbeniinv_ca','$varappincompatibili_ca','$vartariffeassociate_ca','$vartariffeincomp_ca','$datainserimento','$HOSTNAME','$id_utente')");
if (substr($tipo_ca,1,1) != "f") esegui_query("update $tablecostiprenota set valore_perc = '$valore_perc_ca', arrotonda = '".$dati_ca[$numca]['arrotonda']."' where idcostiprenota = '$idcostiprenota'");
if ($dati_ca[$numca]['tasseperc']) esegui_query("update $tablecostiprenota set tasseperc = '".$dati_ca[$numca]['tasseperc']."' where idcostiprenota = '$idcostiprenota'");
} # fine for $num1

$n_costo_tot = $d_costo_tot + $n_costo_agg_tot - $d_costo_agg_tot;
esegui_query("update $tableprenota set tariffa_tot = '$n_costo_tot', data_modifica = '$datainserimento' where idprenota = '$id_prenota' ");

$testo_azione = "<br>".mex("I costi sono stati inseriti sulla prenotazione dell'appartamento",'unit.php')." <b>$app_prenota</b>.<br><br>
<button class=\"pos\" type=\"submit\" style=\"height: 60px; width: 140px; margin: 2px;\"><b>".mex("OK",$pag)."</b></button>";
$mostra_contr = 1;
} # fine elseif ($id_costi)
} # fine if ($inprenota and !$errore_prenota and !$azione)


if ($inprenota and $id_prenota) unlock_tabelle($tabelle_lock);



$testo_costi = "";
$tot_indef = 0;
$vett_js_id_c = "";
$vett_js_nomi_c = "";
$vett_js_molt_c = "";
$vett_js_val_c = "";
$id_js = "";
$onclick = "";
if ($val_costo[-1]) {
$testo_costi .= "<tr style=\"background-color: ".rowbgcolor().";\"><td style=\"width: 34px; height: 34px;\">&nbsp;</td>
<td>".mex("Altri costi prenotazione",$pag)."</td>
<td style=\"width: 40px; text-align: right;\">(".punti_in_num($val_costo[-1],$stile_soldi,"2").")</td></tr>";
$val_tot_pren = $val_tot + $val_costo[-1];
} # fine if ($val_costo[-1])
else $val_tot_pren = $val_tot;
if (!$id_costi) $num_costi = 0;
else {
$costi = explode(",",$id_costi);
$num_costi = count($costi);
} # fine else if (!$id_costi)
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$numca = explode("x",$costi[$num1]);
$molt_costo = $numca[1];
$id_costo = $numca[0];
$numca = $dati_ca['id'][$id_costo];
if ($moltiplica_max[$num1]) $molt_costo = $moltiplica_max[$num1];
elseif ($dati_ca[$numca]['moltiplica'] == "p" or $dati_ca[$numca]['moltiplica'] == "t") $molt_costo = "?";
$onclick = "";
if ($calcolabile_js[$num1]) {
$vett_js_id_c .= ",'$id_costo'";
$vett_js_nomi_c .= ",'".str_replace("'","\\'",str_replace("\\","\\\\",$dati_ca[$numca]['nome']))."'";
$vett_js_molt_c .= ",'$molt_costo'";
$vett_js_val_c .= ",'".$val_costo[$num1]."'";
if (!$calcolabile_js['nessuno']) $onclick = " onclick=\"return canc_cos_pv($num1);\"";
} # fine if ($calcolabile_js[$num1])
else {
$vett_js_id_c .= ",''";
$vett_js_nomi_c .= ",''";
$vett_js_molt_c .= ",''";
$vett_js_val_c .= ",''";
} # fine else if ($calcolabile_js[$num1])
if ($molt_costo and $molt_costo != 1) $molt_costo = " <b>x$molt_costo</b>";
else $molt_costo = "";
if ($errori_costi[$num1]) $redclass = " class=\"colred\" title=\"".substr($errori_costi[$num1],2)."\"";
else $redclass = "";
if ($val_costo[$num1] != "?") $val_costo_vedi = punti_in_num($val_costo[$num1],$stile_soldi,"2");
else {
$tot_indef = 1;
$val_costo_vedi = "?";
} # fine else if ($val_costo[$num1] != "?")
$testo_costi .= "<tr style=\"background-color: ".rowbgcolor().";\"><td style=\"width: 34px; height: 34px;\">";
if ($testo_azione) $testo_costi .= "&nbsp;";
else $testo_costi .= "<button class=\"pos\" type=\"submit\" name=\"canc_costo\" value=\"$num1\"$onclick style=\"padding: 0;\"><img style=\"display: block; padding: 0; border: 0; margin: 0;\" src=\"./img/croce.gif\" alt=\"X\"></button>";
$testo_costi .= "</td><td$redclass>".$dati_ca[$numca]['nome']."$molt_costo</td>
<td style=\"width: 40px; text-align: right;\">$val_costo_vedi</td></tr>";
} # fine for $num1

if (strcmp($n_categoria,"")) $categoria = $n_categoria;
if ($no_categoria) $categoria = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"punto_vendita.php#finetab\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" id=\"id_costi\" name=\"id_costi\" value=\"$id_costi\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$id_prenota\">
<input type=\"hidden\" id=\"nomecat\" name=\"categoria\" value=\"$categoria\">";
for ($numca2 = 0 ; $numca2 < $num_costi ; $numca2++) if (${"id_periodi_costo".$numca2}) echo "<input type=\"hidden\" name=\"id_periodi_costo$numca2\" value=\"".${"id_periodi_costo".$numca2}."\">";
echo "<div style=\"float: left; border: 1px solid black; width: 300px; height: 385px; margin: 0 2px 1px 1px;\">
<div style=\" height: 276px; overflow: auto;\">
<table id=\"tab_costi\" cellspacing=0 style=\"width: 100%; padding: 2px;\">
$testo_costi
</table><a name=\"finetab\"></a></div>";
$disabled_cassa = "";
$disabled_prenota = "";
if (!$errore_cassa or $id_prenota) $totale = punti_in_num($val_tot,$stile_soldi,"2");
else {
if (!$val_tot) $totale = "?";
else $totale = punti_in_num($val_tot,$stile_soldi,"2")." + ?";
$disabled_cassa = " disabled=\"disabled\"";
} # fine else if (!$errore_cassa)
if ($id_prenota and $val_tot_pren != $val_tot) $totale .= " (".punti_in_num($val_tot_pren,$stile_soldi,"2").")";
if ($priv_ins_entrate != "s" or !$id_costi or $testo_azione or $errore_cassa) $disabled_cassa = " disabled=\"disabled\"";
if (($id_prenota and !$id_costi) or $testo_azione or $errore_prenota) $disabled_prenota = " disabled=\"disabled\"";
if (!$id_prenota) $testo_app = "";
else $testo_app = " (".mex("appartamento",'unit.php')." $app_prenota)";
echo "<div style=\"text-align: center;\"><table cellspacing=0 style=\"width: 100%; padding: 2px;\">
<tr><td style=\"width: 34px; height: 34px;\">";
if ($testo_azione) echo "&nbsp;";
else echo "<button class=\"pos\" type=\"submit\" name=\"azzera\" value=\"1\" style=\"padding: 0;\"><img style=\"display: block; padding: 0; border: 0; margin: 0;\" src=\"./img/croce.gif\" alt=\"X\"></button>";
if (!$calcolabile_js['nessuno']) $id_js = " id=\"incassa\"";
echo "</td><td style=\"text-align: left;\"><em>".mex("TOTALE",$pag)."</em></td><td style=\"text-align: right;\"><b id=\"tot_costi\">$totale</b> $Euro</td>
</tr></table></div><table cellspacing=0 style=\"padding: 1px; width: 100%; text-align: center\"><tr>
<td><button$id_js class=\"pos\" type=\"submit\"$disabled_cassa name=\"incassa\" value=\"1\" style=\"height: 60px; width: 140px; margin: 0;\"><b>".mex("in cassa",$pag)."</b></button></td>
<td><button class=\"pos\" type=\"submit\"$disabled_prenota name=\"inprenota\" value=\"1\" style=\"height: 60px; width: 140px; margin: 0;\"><b>".mex("su prenotazione",$pag)."$testo_app</b></button></td>
</tr></table></div>";


if ($testo_azione) {
echo "</div></form>
<form accept-charset=\"utf-8\" method=\"post\" action=\"punto_vendita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$testo_azione
</div></form>";

if ($mostra_contr) {
echo "<br><hr class=\"pos\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_contratto.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"punto_vendita.php\">";
if ($incassa) {
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$numca = explode("x",$costi[$num1]);
$molt_costo = $numca[1];
if (!$molt_costo) $molt_costo = 1;
$id_costo = $numca[0];
$numca = $dati_ca['id'][$id_costo];
echo "<input type=\"hidden\" name=\"nome_costo_agg$num1"."_1\" value=\"".$dati_ca[$numca]['nome']."\">
<input type=\"hidden\" name=\"val_costo_agg$num1"."_1\" value=\"".$val_costo[$num1]."\">
<input type=\"hidden\" name=\"percentuale_tasse_costo_agg$num1"."_1\" value=\"".$dati_ca[$numca]['tasseperc']."\">
<input type=\"hidden\" name=\"moltiplica_max_costo_agg$num1"."_1\" value=\"$molt_costo\">
<input type=\"hidden\" name=\"data_inserimento_costo_agg$num1"."_1\" value=\"".substr($datainserimento,0,10)."\">
<input type=\"hidden\" name=\"utente_inserimento_costo_agg$num1"."_1\" value=\"$id_utente\">";
} # fine for $num1
echo "<input type=\"hidden\" name=\"num_costi_aggiuntivi_1\" value=\"$num_costi\">
<input type=\"hidden\" name=\"data_paga0_1\" value=\"".substr($datainserimento,0,10)."\">
<input type=\"hidden\" name=\"utente_paga0_1\" value=\"$id_utente\">
<input type=\"hidden\" name=\"metodo_paga0_1\" value=\"$metodo_pagamento_sel\">
<input type=\"hidden\" name=\"saldo_paga0_1\" value=\"$val_tot\">
<input type=\"hidden\" name=\"num_pagamenti_1\" value=\"1\">
<input type=\"hidden\" name=\"num_ripeti\" value=\"1\">";
} # fine if ($incassa)
if ($inprenota) echo "<input type=\"hidden\" name=\"lista_prenota\" value=\",$id_prenota,\">";
unset($nome_contratto);
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
for ($num_contratto = 1 ; $num_contratto <= $max_contr ; $num_contratto++) {
if ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contratto] == "SI") {
if ($nome_contratto[$num_contratto]) $num_contratto_vedi = $nome_contratto[$num_contratto];
else $num_contratto_vedi = mex("documento",$pag)." ".$num_contratto;
echo "<div style=\"float: left; padding: 2px;\">
<button class=\"pos\" type=\"submit\" name=\"numero_contratto\" value=\"$num_contratto\" style=\"min-height: 60px; min-width: 70px; max-width: 110px;\">$num_contratto_vedi</button>
</div>";
} # fine if ($attiva_contratti_consentiti == "n" or...
} # fine for $num_contratto
echo "</div></form>";
} # fine if ($mostra_contr)

} # fine if ($testo_azione)

else {


$tasti_pos = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'tasti_pos' and idutente = '$id_utente' ");
$tasti_pos = risul_query($tasti_pos,0,'valpersonalizza');
if ($tasti_pos) {
$tasti_pos = explode(";",$tasti_pos);
$num_tasti_pos = count($tasti_pos);
for ($num1 = 0 ; $num1 < $num_tasti_pos ; $num1++) {
if ($num1 == ($num_tasti_pos - 1)) $stile_float = "clear: right;";
else $stile_float = "float: left;";
if ($tasti_pos[$num1] == "s") echo "<div style=\"$stile_float height: 64px; width: 8px;\">&nbsp;</div>";
else {
$oper = substr($tasti_pos[$num1],0,1);
$val_oper = substr($tasti_pos[$num1],1);
if ($oper == "+") {
if (!$calcolabile_js['nessuno']) $onclick = " onclick=\"return aggiungi_costi_pv('$val_oper')\"";
echo "<div style=\"$stile_float\">
<button class=\"pos\" type=\"submit\" name=\"aggiungi\" value=\"$val_oper\"$onclick style=\"height: 60px; width: 64px; margin: 2px;\"><b>+ $val_oper</b></button>
</div>";
} # fine if ($oper == "+")
if ($oper == "-") {
if (!$calcolabile_js['nessuno']) $onclick = " onclick=\"return sottrai_costi_pv('$val_oper')\"";
echo "<div style=\"$stile_float\">
<button class=\"pos\" type=\"submit\" name=\"sottrai\" value=\"$val_oper\"$onclick style=\"height: 60px; width: 64px; margin: 2px;\"><b>- $val_oper</b></button>
</div>";
} # fine if ($oper == "-")
if ($oper == "x") {
if (!$calcolabile_js['nessuno']) $onclick = " onclick=\"return moltiplica_costi_pv('$val_oper')\"";
echo "<div style=\"$stile_float\">
<button class=\"pos\" type=\"submit\" name=\"moltiplica\" value=\"$val_oper\"$onclick style=\"height: 60px; width: 64px; margin: 2px;\"><b>X $val_oper</b></button>
</div>";
} # fine if ($oper == "x")
} # fine else if ($tasti_pos[$num1] == "s")
} # fine for $num1
echo "<hr class=\"pos\">";
} # fine if ($tasti_pos)

if (!$calcolabile_js['nessuno']) {
$vett_calcolabile_js = "";
$vett_moltiplicabile_js = "";
for ($num1 = 0 ; $num1 < $num_costi ; $num1++) {
$vett_calcolabile_js .= ",'".$calcolabile_js[$num1]."'";
$vett_moltiplicabile_js .= ",'".$moltiplicabile_js[$num1]."'";
} # fine for $num1
if ($stile_soldi == "usa") {
$virgola = ".";
$punto = ",";
} # fine if ($stile_soldi == "usa")
else {
$virgola = ",";
$punto = ".";
} # fine else if ($stile_soldi == "usa")
echo "<script type=\"text/javascript\">
<!--
var ultimo_costo = ".($num_costi - 1).";
var colore_corr = '".rowbgcolor()."';
var totale = $val_tot;
var tot_indef = $tot_indef;
var id_costi = '$id_costi';
var calcolabile = new Array(".substr($vett_calcolabile_js,1).");
var moltiplicabile = new Array(".substr($vett_moltiplicabile_js,1).");
var id_costo = new Array(".substr($vett_js_id_c,1).");
var nome_costo = new Array(".substr($vett_js_nomi_c,1).");
var molt_costo = new Array(".substr($vett_js_molt_c,1).");
var val_costo = new Array(".substr($vett_js_val_c,1).");
var punto = '$punto';
var virgola = '$virgola';
var t2row1color = '$t2row1color';
var t2row2color = '$t2row2color';
-->
</script>";
} # fine if (!$calcolabile_js['nessuno'])


$testo_no_categoria = "";
unset($testo_categoria);
unset($costi_agg_raggr);
unset($onclick_ins_js);
unset($combina_mostrato);
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num1]['id']] == "SI") {
$testo_costo = "";
$nome_costo = $dati_ca[$num1]['nome'];
$id_costo = $dati_ca[$num1]['id'];

if (!$calcolabile_js['nessuno']) {
if ($dati_ca[$num1]['tipo'] == "u" and $dati_ca[$num1]['tipo_val'] == "f") {
if ($dati_ca[$num1]['letto'] != "s" and !$dati_ca[$num1]['numlimite'] and !$dati_ca[$num1]['tipo_beniinv'] and !$dati_ca[$num1]['periodipermessi'] and !$dati_ca[$num1]['appincompatibili']) {
if ($dati_ca[$num1]['moltiplica'] == "1" or $dati_ca[$num1]['moltiplica'] == "c") {
if ($dati_ca[$num1]['moltiplica'] == "c") $moltiplicab = 1;
else $moltiplicab = 0;
$onclick_ins_js[$id_costo] = " onclick=\"return ins_cos_pv('$id_costo','$nome_costo','1','".$dati_ca[$num1]['valore']."','1','$moltiplicab');\"";
} # fine if ($dati_ca[$num1]['moltiplica'] == "1" or $dati_ca[$num1]['moltiplica'] == "c")
} # fine if ($dati_ca[$num1]['letto'] != "s" and !$dati_ca[$num1]['numlimite'] and !$dati_ca[$num1]['tipo_beniinv'] and...
} # fine if ($dati_ca[$num1]['tipo'] == "u" and $dati_ca[$num1]['tipo_val'] == "f")
} # fine if (!$calcolabile_js['nessuno'])

$categ = $dati_ca[$num1]['categoria'];
if (strcmp(trim($categ),"")) {
#if (!$costi_agg_raggr[$categ]) $ultima_categoria = $categ;
$costi_agg_raggr[$categ] .= $id_costo.",";
} # fine if (strcmp(trim($categ),""))
else $testo_no_categoria .= "<div style=\"float: left; padding: 2px;\">
<button class=\"pos\" type=\"submit\" name=\"agg_costo\" value=\"$id_costo\"".$onclick_ins_js[$id_costo]." style=\"min-height: 60px; min-width: 70px; max-width: 110px;\">$nome_costo</button>
</div>";
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine for $num1

if (@is_array($costi_agg_raggr)) {
if ($testo_no_categoria) echo "<div style=\"float: left; padding: 2px;\">
<button class=\"pos\" type=\"submit\" name=\"no_categoria\" value=\"1\" onclick=\"return mostra_cat('0');\" style=\"min-height: 60px; min-width: 70px; max-width: 110px;\">".mex("senza categoria",$pag)."</button>
</div>";
if ($ordine_inventario != "ins") ksort($costi_agg_raggr);
reset($costi_agg_raggr);
$num_categorie = count($costi_agg_raggr);
$num_cat = 1;
foreach ($costi_agg_raggr as $categ => $id_costi_cat) {
if ($num_cat == $num_categorie) $float = "clear: right;";
else $float = "float: left;";
echo "<div style=\"$float\">
<button class=\"pos\" type=\"submit\" name=\"n_categoria\" value=\"$categ\" onclick=\"return mostra_cat('$num_cat');\" style=\"min-height: 60px; min-width: 70px; max-width: 110px; margin: 2px;\">$categ</button>
</div>";
$id_costi_vett = explode(",",substr($id_costi_cat,0,-1));
$num_id_costi = count($id_costi_vett);
for ($num1 = 0 ; $num1 < $num_id_costi ; $num1++) {
$id_costo = $id_costi_vett[$num1];
$num_costo = $dati_ca['id'][$id_costo];
if ($dati_ca[$num_costo]['combina'] != "s" or !$combina_mostrato[$categ]) {
$nome_costo = $dati_ca[$num_costo]['nome'];
if ($dati_ca[$num_costo]['combina'] == "s") {
$nome_costo = htmlspecialchars($categ);
$id_costo = "c".$nome_costo;
$combina_mostrato[$categ] = 1;
} # fine if ($dati_ca[$num_costo]['combina'] == "s")
if (!$calcolabile_js['nessuno']) $id_js = " id=\"ins_c$id_costo\"";
$testo_categoria[$categ] .= "<div style=\"float: left; padding: 2px;\">
<button$id_js class=\"pos\" type=\"submit\" name=\"agg_costo\" value=\"$id_costo\"".$onclick_ins_js[$id_costo]." style=\"min-height: 60px; min-width: 70px; max-width: 110px;\">$nome_costo</button>
</div>";
} # fine if ($dati_ca[$num_costo]['combina'] != "s" or !$combina_mostrato[$categ])
} # fine for $num1
$num_cat++;
} # fine foreach ($costi_agg_raggr as $categ => $id_costi_cat)
echo "<hr class=\"pos\">";
} # fine if (@is_array($costi_agg_raggr))

echo "<div id=\"cattxt\">";
if (strcmp($categoria,"") and $testo_categoria[$categoria]) echo $testo_categoria[$categoria];
else echo $testo_no_categoria;
echo "</div>";


echo "<script type=\"text/javascript\">
<!--
function mostra_cat (categoria) {
if (categoria == 0) {
document.getElementById('cattxt').innerHTML = '".str_replace("'","\\'",str_replace("\n","\\\n",str_replace("/","\\/",$testo_no_categoria)))."';
document.getElementById('nomecat').value = '';
}
";
if (@is_array($costi_agg_raggr)) {
reset($costi_agg_raggr);
$num_cat = 1;
foreach ($costi_agg_raggr as $categ => $id_costi_cat) {
echo "if (categoria == $num_cat) {
document.getElementById('cattxt').innerHTML = '".str_replace("'","\\'",str_replace("\n","\\\n",str_replace("/","\\/",$testo_categoria[$categ])))."';
document.getElementById('nomecat').value = '$categ';
}
";
$num_cat++;
} # fine foreach ($costi_agg_raggr as $categ => $id_costi_cat)
} # fine if (@is_array($costi_agg_raggr))
echo "
return false;
}
-->
</script>";


echo "</div></form>";


} # fine else if ($testo_azione)

echo "<div style=\"text-align: center; clear: both;\"><br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"bkmm\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
</div></form></div><div style=\"height: 20px\"></div>";

} # fine if (!$blocca_schermo)




if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and...
} # fine if ($id_utente)



?>
