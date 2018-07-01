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

$pag = "creaprezzi.php";
$titolo = "HotelDruid: Crea Prezzi";
$base_js = 1;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
include("./includes/funzioni_tariffe.php");
include("./includes/funzioni_costi_agg.php");
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tablebeniinventario = $PHPR_TAB_PRE."beniinventario";
$tablemagazzini = $PHPR_TAB_PRE."magazzini";
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tableutenti = $PHPR_TAB_PRE."utenti";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_periodi = substr($priv_vedi_tab,3,1);
$priv_vedi_tab_appartamenti = substr($priv_vedi_tab,5,1);
if ($priv_vedi_tab_appartamenti == "g") $prendi_gruppi = "SI";
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
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
$priv_mod_tariffe = substr($priv_ins_tariffe,0,1);
$priv_ins_costi_agg = substr($priv_ins_tariffe,1,1);
if ($priv_ins_costi_agg == "g") $prendi_gruppi = "SI";
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
$modifica_pers = "SI";
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
$priv_mod_tariffe = "s";
$priv_ins_costi_agg = "s";
} # fine else if ($id_utente != 1)
if ($anno_utente_attivato == "SI" and ($priv_mod_tariffe == "s" or $priv_ins_costi_agg != "n")) {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");




/*
STRUTTURA TABELLA NOMI TARIFFE

RIGHE TARIFFE (IDNTARIFFE <= 10)
idntariffe 1						nome tariffa
idntariffe 2			x			valore caparra
idntariffe 3			x-val-gio		arrotondamento caparra percentuale ; caparra in Euro ; caparra in giorni
idntariffe 4			s-p			moltiplica: prezzi singoli ; per persona
idntariffe 5			x			percentuale tasse
idntariffe 6			x;f-p-2;p-g-s;x;x	numero tariffa da cui importare i prezzi ; parte del prezzo a cui aggiungere: fissa - per persona - entrambe ; importazione a percentuale - aggiungendo a giorno - aggiungendo a settimana ; valore aggiunta ; arrotondamento percentuale 

COSTI AGGIUNTIVI
tipo_ca(varchar2)		u-s			1  unico - settimanale
				f-p-q-s-t-c-r		2  fisso - percentuale su tariffa - perc. su fisso tariffa - perc. su persona tariffa - perc. su totale - perc. su caparra - perc. su resto caparra
associasett_ca(varchar1)	s-n			1  NULL o vuoto se costo unico, associa a specifiche settimane della prenotazione: si-no
numsett_ca(varchar20)		t-m-c-n-s-g,		1  tutte - tutte meno una - chiedere - x settimane no e y si - x settimane si e y no - solo giorni della settimana selezionati
				;p-u;;x,y;x,y;1-7,...	2- niente se 1=t ; prima-ultima se 1=m ; ...
moltiplica_ca(text)		1-c-p-t			1  moltiplica per: 1 - chiedere - persone - persnone totali
				x;x-n-p-t;x;x		2  x se 1=1 ; numero massimo se 1=c: nessuno - fisso - persone - persone totali ;...
				NNN,NNN			3- numero da aggiungere a moltiplica , numero massimo se 1=c e 2=n o numero da sottrarre se 1=c e 2=p/t
periodipermessi_ca(text)	t-u-p			NULL o vuoto se periodi tutti permessi, altrimenti: tutta la prenotazione dentro i periodi - anche un solo periodo - solo periodi permessi
				idperiodo-idperiodo,...	
beniinv_ca(text)		mag$idmag-app;		NULL o vuoto se nessun bene da eliminare dall'inventario, altimenti: elimina dal magazzino $idmag - elimina dall'appartamento della prenotazione
				idbene,x;idbene,x;...	idbene del bene da eliminare e numero da eliminare
appincomapibili_ca(text)	idapp,idapp,...		NULL o vuoto se nessun appartamento incompatibile
variazione_ca(varchar20)	s-n			1 modifica con costo della prenotazione il costo percentuale: si-no
				s-n			2 modifica numero settimane con prenotazione: si-no
				s-n			3 modifica numero con cui moltiplicare con prenotazione: si-no
				s-n			4 modifica in periodi permessi con prenotazione: si-no
				s-n			5 togli/aggiungi il costo se modificando una prenotazione cambia una tariffa associata: si-no
				s-n			6 togli il costo se modificando una prenotazione si passa ad una tariffa incompatibile: si-no
				s-n			7 modifica i beni inventario con prenotazione: si-no
				s-n			8 modifica gli appartamenti incompatibili con prenotazione: si-no
mostra_ca(varchar10)		s-n			1 mostra costo quando si inserisce prenotazione: si-no
				s-n			2 raggruppa con costi simili in inserimento: si-no
				s-n			3 combina con costi simili in inserimento (se anche raggruppa = si): si-no
				s-n			4 escludere il costo dal totale per costi percentuali: si-no
categoria_ca(text)		testo			nome della categoria del costo
letto_ca(varchar1)		s-n			1 considera come letto aggiuntivo: si-no
numlimite_ca(integer)		numero			NULL o vuoto o zero se non ci sono limiti al numero di costi nello stesso periodo
regoleassegna_ca(varchar30)	->x-<x;			se non vuoto associa il costo (se possibile) se la prenotazione: inizia dopo x giorni o più - inizia prima di x giorni o meno
				x			se non vuoto associa il costo (se possibile) se si inseriscono x o più prenotazioni contemporaneamente
tariffe(text)			s-r-p-i			1 NULL o vuoto se costo non associato alla tariffa e non incompatibile, altrimenti: associare sempre - sempre in periodi permessi - solo se possibile - incompatibile
				s-<x->x-=x-|x<y		per qualsiasi numero di settimane - meno di x - più di x - per x settimane - compreso tra x e y settimane
tasseperc_ca(float)		x|-1			percentuale tasse - il costo è una tassa
*/




$Euro = nome_valuta();
$stile_data = stile_data();
$stile_soldi = stile_soldi();

$aggiorna_tariffe_interconn = "NO";

$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";

$tipo_periodi = esegui_query("select * from $tableanni where idanni = $anno");
$tipo_periodi = risul_query($tipo_periodi,0,'tipo_periodi');
$numcaselle_max = 200;



if (($importa_tariffa or $tar_importa_canc) and $priv_mod_tariffe != "n") {

function controlla_imp_tar1 ($tariffa_a,$tariffa_da,$tipo_importa,&$num_tariffa_a,&$num_tariffa_da,&$importa_arrotond,&$tipo_percent,&$parte_prezzo,$importa_percent,$periodi_importa,$iniper_imp,$fineper_imp,$attiva_tariffe_consentite,$tariffe_consentite_vett) {
$errore = "";
if ($tariffa_a == $tariffa_da and $tipo_importa == "sempre") $errore = "SI";
$num_tariffa_a = substr($tariffa_a,7);
$num_tariffa_da = substr($tariffa_da,7);
if (substr($tariffa_a,0,7) != "tariffa" or controlla_num_pos($num_tariffa_a) != "SI") $errore = "SI";
if (substr($tariffa_da,0,7) != "tariffa" or controlla_num_pos($num_tariffa_da) != "SI") $errore = "SI";
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$num_tariffa_a] != "SI") $errore = "SI";
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$num_tariffa_da] != "SI") $errore = "SI";
$importa_arrotond = formatta_soldi($importa_arrotond);
if (!$importa_arrotond) $importa_arrotond = 1;
if ($tipo_percent != "euro_g" and $tipo_percent != "euro_s") $tipo_percent = "perc";
if ($parte_prezzo != "f" and $parte_prezzo != "p") $parte_prezzo = "2";
if (controlla_num($importa_percent) == "NO" or ($tipo_percent == "perc" and $importa_percent < -100)) $errore = "SI";
if ($periodi_importa == "s" and $fineper_imp <= $iniper_imp) $errore = "SI";
return $errore;
} # fine function controlla_imp_tar1

function controlla_imp_tar2 ($tariffa_a,$tariffa_da,$tipo_importa,$num_tariffa_a,$num_tariffa_da,$periodi_importa,$iniper_imp,$fineper_imp,&$idiniper_imp,&$idfineper_imp,$dati_tariffe,$importa_tariffa,&$mostra_ok,&$ancora,$tableperiodi) {
global $pag;
$errore = "";
if ($periodi_importa == "s") {
$idiniper_imp = esegui_query("select idperiodi from $tableperiodi where datainizio = '".aggslashdb($iniper_imp)."' ");
$idfineper_imp = esegui_query("select idperiodi from $tableperiodi where datafine = '".aggslashdb($fineper_imp)."' ");
if (!numlin_query($idiniper_imp) or !numlin_query($idfineper_imp)) $errore = "SI";
else {
$idiniper_imp = risul_query($idiniper_imp,0,'idperiodi');
$idfineper_imp = risul_query($idfineper_imp,0,'idperiodi');
} # fine else if (!numlin_query($idiniper_imp) or !numlin_query($idfineper_imp))
} # fine if ($periodi_importa == "s")
$frase_errore = "";
if ($dati_tariffe[$tariffa_a]['imp_prezzi_int']) {
if ($periodi_importa == "s" and ($tipo_importa == "sempre" or !$dati_tariffe[$tariffa_a]['importa_prezzi'][0] or $importa_tariffa == "canc")) {
for ($num1 = 1 ; $num1 < $dati_tariffe[$tariffa_a]['num_per_importa'] ; $num1++) {
if ($dati_tariffe[$tariffa_a]['periodo_importa_f'][$num1] >= $idiniper_imp and $dati_tariffe[$tariffa_a]['periodo_importa_i'][$num1] <= $idfineper_imp) $frase_errore = mex("La tariffa",$pag)." <b class=\"colred\">$num_tariffa_a</b> ".mex("importa già i prezzi da altre tariffe in questi periodi, non può importare altri prezzi",$pag).".<br>";
} # fine for $num1
} # fine if ($periodi_importa == "s" and ($tipo_importa == "sempre" or...
elseif ($dati_tariffe[$tariffa_a]['importa_prezzi'][0]) $frase_errore = mex("La tariffa",$pag)." <b class=\"colred\">$num_tariffa_a</b> ".mex("importa già i prezzi da altre tariffe in questi periodi, non può importare altri prezzi",$pag).".<br>";
} # fine if ($dati_tariffe[$tariffa_a]['imp_prezzi_int'])
if ($dati_tariffe[$tariffa_da]['imp_prezzi_int']) $frase_errore = mex("La tariffa",$pag)." <b class=\"colred\">$num_tariffa_da</b> ".mex("importa i prezzi da altre tariffe, non può esportare i prezzi",$pag).".<br>";
if ($dati_tariffe[$tariffa_a]['esporta_prezzi'] and $tipo_importa == "sempre") $frase_errore = mex("La tariffa",$pag)." <b class=\"colred\">$num_tariffa_a</b> ".mex("esporta i prezzi ad altre tariffe, non può importare i prezzi in modo permanente",$pag).".<br>";
if ($frase_errore) {
$errore = "SI";
echo $frase_errore;
$mostra_ok = 1;
$ancora = "imp_pre";
} # fine if ($frase_errore)
return $errore;
} # fine function controlla_imp_tar2

function aggiorna_imp_tar ($tariffa_a,$tariffa_da,$tipo_importa,&$importa_arrotond,$tipo_percent,$parte_prezzo,&$importa_percent,$periodi_importa,$idiniper_imp,$idfineper_imp,$dati_tariffe,$tableperiodi,$tablenometariffe) {
if ($periodi_importa == "s") $prezzi_da = esegui_query("select idperiodi,$tariffa_da,$tariffa_da"."p from $tableperiodi where ($tariffa_da is not NULL or $tariffa_da"."p is not NULL) and idperiodi >= '$idiniper_imp' and idperiodi <= '$idfineper_imp' ");
else {
if (!$dati_tariffe[$tariffa_a]['importa_prezzi'][1]) $prezzi_da = esegui_query("select idperiodi,$tariffa_da,$tariffa_da"."p from $tableperiodi where $tariffa_da is not NULL or $tariffa_da"."p is not NULL");
else {
$periodi_esclusi = "";
for ($num1 = 1 ; $num1 < $dati_tariffe[$tariffa_a]['num_per_importa'] ; $num1++) {
$periodi_esclusi .= " and (idperiodi < '".$dati_tariffe[$tariffa_a]['periodo_importa_i'][$num1]."' or idperiodi > '".$dati_tariffe[$tariffa_a]['periodo_importa_f'][$num1]."')";
} # fine for $num1
$prezzi_da = esegui_query("select idperiodi,$tariffa_da,$tariffa_da"."p from $tableperiodi where ($tariffa_da is not NULL or $tariffa_da"."p is not NULL)$periodi_esclusi ");
} # fine else if (!$dati_tariffe[$tariffa_a]['importa_prezzi'][1])
} # fine else if ($periodi_importa == "s")
$num_prezzi_da = numlin_query($prezzi_da);
if ($tipo_percent != "perc") $importa_percent = formatta_soldi($importa_percent);
settype($importa_percent,'double');
settype($importa_arrotond,'double');
if ($tipo_percent == "euro_s") {
unset($agg_vett);
$agg_int = floor($importa_percent);
$resto_int = $importa_percent - (double) $agg_int;
$agg_gio = floor($agg_int / 7);
for ($num1 = 1 ; $num1 <= 7 ; $num1++) $agg_vett[$num1] = $agg_gio;
$resto = $agg_int - ($agg_gio * 7);
if ($resto >= 1) {
$agg_vett[1]++;
$resto--;
} # fine if ($resto >= 1)
for ($num1 = 7 ; $num1 > (7 - $resto) ; $num1--) $agg_vett[$num1]++;
$agg_vett[1] += $resto_int;
$num_agg = 0;
} # fine if ($tipo_percent == "euro_s")
if ($tipo_percent == "euro_g") $perc = $importa_percent;
$agg_vett_tar_esp = array();
unset($num_agg_tar_esp);
for ($num1 = 0 ; $num1 < $num_prezzi_da ; $num1++) {
$id_periodo = risul_query($prezzi_da,$num1,'idperiodi');
$prezzo_a = (double) risul_query($prezzi_da,$num1,$tariffa_da);
$prezzo_a_p = (double) risul_query($prezzi_da,$num1,$tariffa_da."p");
if ($tipo_percent == "euro_s") {
$num_agg++;
$perc = $agg_vett[$num_agg];
if ($num_agg == 7) $num_agg = 0;
} # fine if ($tipo_percent == "euro_s")
if ($parte_prezzo != "p") {
if ($tipo_percent == "perc") $perc = (double) (($prezzo_a / 100.0) * $importa_percent);
if ($perc) {
if ($tipo_percent == "perc") $perc = (round(($perc / $importa_arrotond),0) * $importa_arrotond);
$prezzo_a = $prezzo_a + $perc;
} # fine if ($perc)
} # fine if ($parte_prezzo != "p")
if ($parte_prezzo != "f") {
if ($tipo_percent == "perc") $perc = (double) (($prezzo_a_p / 100.0) * $importa_percent);
if ($perc) {
if ($tipo_percent == "perc") $perc = (round(($perc / $importa_arrotond),0) * $importa_arrotond);
$prezzo_a_p = $prezzo_a_p + $perc;
} # fine if ($perc)
} # fine if ($parte_prezzo != "f")
if ($prezzo_a or !$prezzo_a_p) esegui_query("update $tableperiodi set $tariffa_a = '$prezzo_a' where idperiodi = '$id_periodo'");
else esegui_query("update $tableperiodi set $tariffa_a = NULL where idperiodi = '$id_periodo'");
if ($prezzo_a_p) esegui_query("update $tableperiodi set $tariffa_a"."p = '$prezzo_a_p' where idperiodi = '$id_periodo'");
else esegui_query("update $tableperiodi set $tariffa_a"."p = NULL where idperiodi = '$id_periodo'");
aggiorna_tariffe_esporta($dati_tariffe,$tariffa_a,$id_periodo,$prezzo_a,$prezzo_a_p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine for $num1
if ($tipo_importa == "sempre") {
$val_impor = substr($tariffa_da,7).";$parte_prezzo;";
if ($tipo_percent == "perc") $val_impor .= "p";
if ($tipo_percent == "euro_g") $val_impor .= "g";
if ($tipo_percent == "euro_s") $val_impor .= "s";
$val_impor .= ";$importa_percent;";
if ($tipo_percent == "perc") $val_impor .= "$importa_arrotond";
if ($periodi_importa == "s") {
if ($dati_tariffe[$tariffa_a]['imp_prezzi_int']) $val_impor = $dati_tariffe[$tariffa_a]['imp_prezzi_int'].">$val_impor";
else $val_impor = ">$val_impor";
$val_impor .= ";$idiniper_imp-$idfineper_imp";
} # fine if ($periodi_importa == "s")
elseif ($dati_tariffe[$tariffa_a]['imp_prezzi_int']) $val_impor .= $dati_tariffe[$tariffa_a]['imp_prezzi_int'];
esegui_query("update $tablenometariffe set $tariffa_a = '".aggslashdb($val_impor)."' where idntariffe = '6' ");
} # fine if ($tipo_importa == "sempre")
$opztariffa = esegui_query("select * from $tableperiodi where $tariffa_a"."p is not NULL and $tariffa_a"."p != '0' ");
if (numlin_query($opztariffa)) $opztariffa = "p";
else $opztariffa = "s";
esegui_query("update $tablenometariffe set $tariffa_a = '$opztariffa' where idntariffe = '4' ");
aggiorna_tariffe_esporta($dati_tariffe,$tariffa_a,"opztariffa","",$tablenometariffe,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine function aggiorna_imp_tar


if ($importa_tariffa == "canc" or $importa_tariffa == "mod") $importa_tariffa = "";
if ($tar_importa_canc and !$mod_importa) {
$errore = 0;
if (controlla_num_pos($tar_importa_canc) != "SI") $errore = 1;
$tar_imp_canc = "tariffa$tar_importa_canc";
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$tar_imp_canc] != "SI") $errore = 1;
if (!$errore) {
$tabelle_lock = array($tablenometariffe,$tableperiodi);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dati_tariffe = dati_tariffe($tablenometariffe);
if ($dati_tariffe[$tar_imp_canc]['imp_prezzi_int']) {
$mostra_form_iniziale = "NO";
$ripristina_importa_default = 0;

# Se si modifica una tariffa controllo che i dati della modifica siano giusti prima di cancellarla e reinserirla
if ($tar_importa_mod) {
$tipo_importa = "sempre";
$errore = controlla_imp_tar1($tariffa_a,$tariffa_da,$tipo_importa,$num_tariffa_a,$num_tariffa_da,$importa_arrotond,$tipo_percent,$parte_prezzo,$importa_percent,$periodi_importa,$iniper_imp,$fineper_imp,$attiva_tariffe_consentite,$tariffe_consentite_vett);
if ($errore != "SI") {
$dati_tariffe2 = $dati_tariffe;
if (!$per_importa_canc) {
$dati_tariffe2[$tar_imp_canc]['importa_prezzi'][0] = "";
if ($dati_tariffe2[$tar_imp_canc]['num_per_importa'] < 2) $dati_tariffe2[$tar_imp_canc]['imp_prezzi_int'] = "";
} # fine if (!$per_importa_canc)
else {
$dati_tariffe2[$tar_imp_canc]['num_per_importa']--;
$num_per_imp = $dati_tariffe2[$tar_imp_canc]['num_per_importa'];
if ($num_per_imp < 2 and !$dati_tariffe2[$tar_imp_canc]['importa_prezzi'][0]) $dati_tariffe2[$tar_imp_canc]['imp_prezzi_int'] = "";
for ($num1 = 1 ; $num1 < $num_per_imp ; $num1++) {
if ($per_importa_canc == $dati_tariffe2[$tar_imp_canc]['periodo_importa_i'][$num1]."-".$dati_tariffe2[$tar_imp_canc]['periodo_importa_f'][$num1]) {
$dati_tariffe2[$tar_imp_canc]['importa_prezzi'][$num1] = $dati_tariffe2[$tar_imp_canc]['importa_prezzi'][$num_per_imp];
$dati_tariffe2[$tar_imp_canc][''][$num1] = $dati_tariffe2[$tar_imp_canc][''][$num_per_imp];
$dati_tariffe2[$tar_imp_canc]['periodo_importa_i'][$num1] = $dati_tariffe2[$tar_imp_canc]['periodo_importa_i'][$num_per_imp];
$dati_tariffe2[$tar_imp_canc]['periodo_importa_f'][$num1] = $dati_tariffe2[$tar_imp_canc]['periodo_importa_f'][$num_per_imp];
} # fine if ($per_importa_canc == $dati_tariffe2[$tar_imp_canc]['periodo_importa_i'][$num1]."-".$dati_tariffe2[$tar_imp_canc]['periodo_importa_f'][$num1])
} # fine for $num1
} # fine else if (!$per_importa_canc)
$errore = controlla_imp_tar2($tariffa_a,$tariffa_da,$tipo_importa,$num_tariffa_a,$num_tariffa_da,$periodi_importa,$iniper_imp,$fineper_imp,$idiniper_imp,$idfineper_imp,$dati_tariffe2,"mod",$mostra_ok,$ancora,$tableperiodi);
if ($errore != "SI") $importa_tariffa = "mod";
} # fine if ($errore != "SI")
if ($errore == "SI") $importa_tariffa = "";
} # fine if ($tar_importa_mod)

if (!$tar_importa_mod or $importa_tariffa == "mod") {
if (!$dati_tariffe[$tar_imp_canc]['importa_prezzi'][1]) esegui_query("update $tablenometariffe set tariffa$tar_importa_canc = NULL where idntariffe = '6' ");
else {
$val_impor = $dati_tariffe[$tar_imp_canc]['imp_prezzi_int'];
$val_impor_vett = explode(">",$val_impor);
if (!$per_importa_canc) $val_impor = substr($val_impor,strlen($val_impor_vett[0]));
else {
for ($num1 = 1 ; $num1 < count($val_impor_vett) ; $num1++) {
if (substr($val_impor_vett[$num1],(-1 * strlen(";$per_importa_canc"))) == ";$per_importa_canc") {
$val_impor = substr(str_replace(">".$val_impor_vett[$num1].">",">",">$val_impor>"),1,-1);
if ($dati_tariffe[$tar_imp_canc]['importa_prezzi'][0]) $ripristina_importa_default = $num1;
} # fine if (substr($val_impor_vett[$num1],(-1 * strlen(";$per_importa_canc"))) == ";$per_importa_canc")
} # fine for $num1
} # fine else if (!$per_importa_canc)
if ($val_impor) esegui_query("update $tablenometariffe set tariffa$tar_importa_canc = '".aggslashdb($val_impor)."' where idntariffe = '6' ");
else esegui_query("update $tablenometariffe set tariffa$tar_importa_canc = NULL where idntariffe = '6' ");
} # fine else if (!$dati_tariffe[$tar_imp_canc]['importa_prezzi'][1])
echo mex("L'importatazione dei prezzi è stata cancellata",$pag).".<br>";
$mostra_ok = 1;
$ancora = "imp_pre";

# Se si cancella un periodo importato e c'è una importazione predefinita in tutti i periodi allora devo aggiornare il periodo cancellato
if ($ripristina_importa_default) {
$tariffa_a2 = $tar_imp_canc;
$tariffa_da2 = "tariffa".$dati_tariffe[$tariffa_a2]['importa_prezzi'][0];
$importa_percent2 = $dati_tariffe[$tariffa_a2]['val_importa'][0];
$tipo_percent2 = "perc";
if ($dati_tariffe[$tariffa_a2]['tipo_importa'][0] == "g") $tipo_percent2 = "euro_g";
if ($dati_tariffe[$tariffa_a2]['tipo_importa'][0] == "s") $tipo_percent2 = "euro_s";
$importa_arrotond2 = $dati_tariffe[$tariffa_a2]['arrotond_importa'][0];
$parte_prezzo2 = $dati_tariffe[$tariffa_a2]['parte_prezzo'][0];
$iniper_imp2 = esegui_query("select datainizio from $tableperiodi where idperiodi = '".aggslashdb($dati_tariffe[$tariffa_a2]['periodo_importa_i'][$ripristina_importa_default])."' ");
$iniper_imp2 = risul_query($iniper_imp2,0,'datainizio');
$fineper_imp2 = esegui_query("select datafine from $tableperiodi where idperiodi = '".aggslashdb($dati_tariffe[$tariffa_a2]['periodo_importa_f'][$ripristina_importa_default])."' ");
$fineper_imp2 = risul_query($fineper_imp2,0,'datafine');
$errore = controlla_imp_tar1($tariffa_a2,$tariffa_da2,"ora",$num_tariffa_a,$num_tariffa_da,$importa_arrotond2,$tipo_percent2,$parte_prezzo2,$importa_percent2,"s",$iniper_imp2,$fineper_imp2,$attiva_tariffe_consentite,$tariffe_consentite_vett);
if ($errore != "SI") {
$dati_tariffe = dati_tariffe($tablenometariffe);
$errore = controlla_imp_tar2($tariffa_a2,$tariffa_da2,"ora",$num_tariffa_a,$num_tariffa_da,"s",$iniper_imp2,$fineper_imp2,$idiniper_imp,$idfineper_imp,$dati_tariffe,"canc",$mostra_ok,$ancora,$tableperiodi);
if ($errore != "SI") {
$aggiorna_tariffe_interconn = "SI";
aggiorna_imp_tar($tariffa_a2,$tariffa_da2,"ora",$importa_arrotond2,$tipo_percent2,$parte_prezzo2,$importa_percent2,"s",$idiniper_imp,$idfineper_imp,$dati_tariffe,$tableperiodi,$tablenometariffe);
} # fine if ($errore != "SI")
} # fine if ($errore != "SI")
} # fine if ($ripristina_importa_default)

} # fine if (!$tar_importa_mod or $importa_tariffa == "mod")
} # fine if ($dati_tariffe[$tar_imp_canc]['imp_prezzi_int'])
unlock_tabelle($tabelle_lock);
} # fine if (!$errore)
} # fine if ($tar_importa_canc and !$mod_importa)


if ($importa_tariffa) {
$errore = controlla_imp_tar1($tariffa_a,$tariffa_da,$tipo_importa,$num_tariffa_a,$num_tariffa_da,$importa_arrotond,$tipo_percent,$parte_prezzo,$importa_percent,$periodi_importa,$iniper_imp,$fineper_imp,$attiva_tariffe_consentite,$tariffe_consentite_vett);
if ($errore != "SI") {
$mostra_form_iniziale = "NO";
$tabelle_lock = array($tablenometariffe,$tableperiodi);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dati_tariffe = dati_tariffe($tablenometariffe);
$errore = controlla_imp_tar2($tariffa_a,$tariffa_da,$tipo_importa,$num_tariffa_a,$num_tariffa_da,$periodi_importa,$iniper_imp,$fineper_imp,$idiniper_imp,$idfineper_imp,$dati_tariffe,$importa_tariffa,$mostra_ok,$ancora,$tableperiodi);

if ($errore != "SI") {
$aggiorna_tariffe_interconn = "SI";
aggiorna_imp_tar($tariffa_a,$tariffa_da,$tipo_importa,$importa_arrotond,$tipo_percent,$parte_prezzo,$importa_percent,$periodi_importa,$idiniper_imp,$idfineper_imp,$dati_tariffe,$tableperiodi,$tablenometariffe);
if ($importa_tariffa != "canc") echo mex("I prezzi della tariffa selezionata sono stati importati",$pag).".<br>";
$mostra_ok = 1;
$ancora = "imp_pre";
} # fine if ($errore != "SI")
unlock_tabelle($tabelle_lock);
} # fine if ($errore != "SI")
} # fine if ($importa_tariffa)

} # fine if (($importa_tariffa or $tar_importa_canc) and $priv_mod_tariffe != "n")



if ($importa_costo and $priv_ins_costi_agg != "n") {
$mostra_form_iniziale = "NO";
if (get_magic_quotes_gpc()) {
$nomecostoagg = stripslashes($nomecostoagg);
$costo_importa = stripslashes($costo_importa);
} # fine if if (get_magic_quotes_gpc())
$nomecostoagg = htmlspecialchars($nomecostoagg);
$nomecostoagg = str_replace("#?&","",$nomecostoagg);
$nomecostoagg = str_replace("#@&","",$nomecostoagg);
if (!$nomecostoagg) {
echo mex("Si deve inserire il nome del costo aggiuntivo",$pag).".<br>";
$errore = "SI";
} # fine if (!$nomecostoagg)
if ($id_utente != 1) $tabelle_lock = array($tablenometariffe,$tableprivilegi);
else $tabelle_lock = array($tablenometariffe);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dati_costo_importa = esegui_query("select * from $tablenometariffe where idntariffe = '".aggslashdb(htmlspecialchars($costo_importa))."' ");
if (numlin_query($dati_costo_importa) != 1) $errore = "SI";
if ($attiva_costi_agg_consentiti != "n" and $costi_agg_consentiti_vett[$costo_importa] != "SI") $errore = "SI";
if ($errore != "SI") {
$tipo_ca = substr(risul_query($dati_costo_importa,0,'tipo_ca'),0,1);
$esiste_costo = esegui_query("select idntariffe from $tablenometariffe where nomecostoagg = '".aggslashdb($nomecostoagg)."' and tipo_ca $LIKE '".$tipo_ca."_'");
if (numlin_query($esiste_costo) > 0) {
echo mex("Costo aggiuntivo già esistente",$pag).".<br>";
$errore = "SI";
} # fine if (numlin_query($esiste_costo) > 0)
} # fine if ($errore != "SI")
if ($errore != "SI") {
$tipo_ca = aggslashdb(risul_query($dati_costo_importa,0,'tipo_ca'));
$valore_ca = aggslashdb(risul_query($dati_costo_importa,0,'valore_ca'));
$valore_perc_ca = aggslashdb(risul_query($dati_costo_importa,0,'valore_perc_ca'));
$arrotonda_ca = aggslashdb(risul_query($dati_costo_importa,0,'arrotonda_ca'));
$tasseperc_ca = aggslashdb(risul_query($dati_costo_importa,0,'tasseperc_ca'));
$associasett_ca = aggslashdb(risul_query($dati_costo_importa,0,'associasett_ca'));
$numsett_ca = aggslashdb(risul_query($dati_costo_importa,0,'numsett_ca'));
$moltiplica_ca = aggslashdb(risul_query($dati_costo_importa,0,'moltiplica_ca'));
$periodipermessi_ca = aggslashdb(risul_query($dati_costo_importa,0,'periodipermessi_ca'));
$beniinv_ca = aggslashdb(risul_query($dati_costo_importa,0,'beniinv_ca'));
$appincompatibili_ca = aggslashdb(risul_query($dati_costo_importa,0,'appincompatibili_ca'));
$variazione_ca = aggslashdb(risul_query($dati_costo_importa,0,'variazione_ca'));
$mostra_ca = aggslashdb(risul_query($dati_costo_importa,0,'mostra_ca'));
$categoria_ca = aggslashdb(risul_query($dati_costo_importa,0,'categoria_ca'));
$letto_ca = aggslashdb(risul_query($dati_costo_importa,0,'letto_ca'));
$numlimite_ca = aggslashdb(risul_query($dati_costo_importa,0,'numlimite_ca'));
$regoleassegna_ca = aggslashdb(risul_query($dati_costo_importa,0,'regoleassegna_ca'));
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
$idntariffe = esegui_query("select numlimite_ca from $tablenometariffe where idntariffe = 1");
$idntariffe = risul_query($idntariffe,0,0);
esegui_query("insert into $tablenometariffe (idntariffe,nomecostoagg,tipo_ca,valore_ca,moltiplica_ca,variazione_ca,mostra_ca,letto_ca,utente_inserimento) values ('$idntariffe','".aggslashdb($nomecostoagg)."','$tipo_ca','$valore_ca','$moltiplica_ca','$variazione_ca','$mostra_ca','$letto_ca','$id_utente')");
if (strcmp($valore_perc_ca,"")) esegui_query("update $tablenometariffe set valore_perc_ca = '$valore_perc_ca' where idntariffe = '$idntariffe' ");
if (strcmp($arrotonda_ca,"")) esegui_query("update $tablenometariffe set arrotonda_ca = '$arrotonda_ca' where idntariffe = '$idntariffe' ");
if (strcmp($tasseperc_ca,"")) esegui_query("update $tablenometariffe set tasseperc_ca = '$tasseperc_ca' where idntariffe = '$idntariffe' ");
if (strcmp($associasett_ca,"")) esegui_query("update $tablenometariffe set associasett_ca = '$associasett_ca' where idntariffe = '$idntariffe' ");
if (strcmp($numsett_ca,"")) esegui_query("update $tablenometariffe set numsett_ca = '$numsett_ca' where idntariffe = '$idntariffe' ");
if (strcmp($periodipermessi_ca,"")) esegui_query("update $tablenometariffe set periodipermessi_ca = '$periodipermessi_ca' where idntariffe = '$idntariffe' ");
if (strcmp($beniinv_ca,"")) esegui_query("update $tablenometariffe set beniinv_ca = '$beniinv_ca' where idntariffe = '$idntariffe' ");
if (strcmp($appincompatibili_ca,"")) esegui_query("update $tablenometariffe set appincompatibili_ca = '$appincompatibili_ca' where idntariffe = '$idntariffe' ");
if (strcmp($categoria_ca,"")) esegui_query("update $tablenometariffe set categoria_ca = '$categoria_ca' where idntariffe = '$idntariffe' ");
if (strcmp($numlimite_ca,"")) esegui_query("update $tablenometariffe set numlimite_ca = '$numlimite_ca' where idntariffe = '$idntariffe' ");
if (strcmp($regoleassegna_ca,"")) esegui_query("update $tablenometariffe set regoleassegna_ca = '$regoleassegna_ca' where idntariffe = '$idntariffe' ");
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$val = aggslashdb(risul_query($dati_costo_importa,0,$tariffa));
if ((string) $val != "") esegui_query("update $tablenometariffe set $tariffa = '$val' where idntariffe = '$idntariffe' ");
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_costi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idntariffe\" value=\"$idntariffe\">
<input type=\"hidden\" name=\"origine\" value=\"$pag#ins_costi_agg\">
".mex("Il costo aggiuntivo",$pag)." \"$nomecostoagg\" ".mex("è stato inserito",$pag).".
 <button class=\"exco\" type=\"submit\"><div>".mex("Modifica il costo",$pag)."</div></button>
</div></form><br><br>";
aggiorna_privilegi_ins_costo($idntariffe,$tableprivilegi,$id_utente,$anno,$attiva_costi_agg_consentiti,$priv_ins_costi_agg,$utenti_gruppi);
$idntariffe++;
esegui_query("update $tablenometariffe set numlimite_ca = '$idntariffe' where idntariffe = 1");
} # fine if ($errore != "SI")
unlock_tabelle($tabelle_lock);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div>OK</div></button><br>
</div></form>";
} # fine if ($importa_costo and $priv_ins_costi_agg != "n")


# Inserisco caparra, commissioni o tasse
if (($modificacaparra or $modificacommissioni or $modificacommper or $modificatasse) and $priv_mod_tariffe != "n") {

if ($tipotariffa != "tutte" or $id_utente != 1 or $modificacaparra or $modificacommissioni or $modificacommper) {
$num_tariffa = substr($tipotariffa,7);
if (substr($tipotariffa,0,7) != "tariffa" or controlla_num_pos($num_tariffa) != "SI") $tipotariffa = "tariffa1";
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$num_tariffa] != "SI") $inserire = "NO";
} # fine if ($tipotariffa != "tutte" or $id_utente != 1 or $modificacaparra or...
if ($modificacommissioni or $modificacommper) $tabelle_lock = array($tablenometariffe,$tableregole);
else $tabelle_lock = array($tablenometariffe);
if ($modificacommper) $altre_tab_lock = array($tableperiodi);
else $altre_tab_lock = array();
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$dati_tariffe = dati_tariffe($tablenometariffe);
if ($num_tariffa > $dati_tariffe['num'] and $tipotariffa != "tutte") $inserire = "NO";

if ($modificacaparra) {
if ($tipo_caparra != "perc" and $tipo_caparra != "val" and $tipo_caparra != "gio") $inserire = "NO";
if ($tipo_caparra == "perc") {
$caparra_arrotond = formatta_soldi($caparra_arrotond);
if ((double) $caparra_arrotond == 0) $caparra_arrotond = 1;
if (controlla_soldi($caparra_arrotond) == "NO") $caparra_arrotond = 1;
if (controlla_num_pos($caparra_percent) != "SI" or $caparra_percent > 100) {
echo mex("Si deve inserire la percentuale",$pag).".<br>";
$inserire = "NO";
} # fine if (controlla_num_pos($caparra_percent) != "SI")
} # fine if ($tipo_caparra == "perc")
if ($tipo_caparra == "val") {
$caparra_val = formatta_soldi($caparra_val);
if (controlla_soldi($caparra_val) == "NO") $inserire = "NO";
$caparra_percent = $caparra_val;
$caparra_arrotond = "val";
} # fine if ($tipo_caparra == "val")
if ($tipo_caparra == "gio") {
if ($caparra_gio < 1 or $caparra_gio > 10) $inserire = "NO";
$caparra_percent = $caparra_gio;
$caparra_arrotond = "gio";
} # fine if ($tipo_caparra == "gio")
if ($inserire != "NO") {
esegui_query("update $tablenometariffe set $tipotariffa = '".aggslashdb($caparra_percent)."' where idntariffe = '2'");
esegui_query("update $tablenometariffe set $tipotariffa = '".aggslashdb($caparra_arrotond)."' where idntariffe = '3'");
echo mex("La caparra è stata modificata",$pag).".<br>";
} # fine if ($inserire != "NO")
else echo mex("La caparra è errata",$pag).".<br>";
} # fine if ($modificacaparra)

if ($modificacommissioni or $modificacommper) {
if ($tipo_commissioni != "perc" and $tipo_commissioni != "val") $inserire = "NO";
$comm_base = "";
if ($tipo_commissioni == "perc") {
$commissioni_arrotond = formatta_soldi($commissioni_arrotond);
if ((double) $commissioni_arrotond == 0) $commissioni_arrotond = 1;
if (controlla_soldi($commissioni_arrotond) == "NO") $commissioni_arrotond = 1;
if (controlla_num_pos($commissioni_percent) != "SI" or $commissioni_percent > 100) {
echo mex("Si deve inserire la percentuale",$pag).".<br>";
$inserire = "NO";
} # fine if (controlla_num_pos($commissioni_percent) != "SI")
if ($commissioni_base == "ts") $comm_base = "s";
if ($commissioni_base == "tsc") $comm_base = "c";
} # fine if ($tipo_commissioni == "perc")
if ($tipo_commissioni == "val") {
$commissioni_val = formatta_soldi($commissioni_val);
if (controlla_soldi($commissioni_val) == "NO") $inserire = "NO";
$commissioni_percent = $commissioni_val;
$commissioni_arrotond = "val";
} # fine if ($tipo_commissioni == "val")
if ($modificacommper) {
$iddataini = @esegui_query("select idperiodi from $tableperiodi where datainizio = '".aggslashdb($comm_dal)."' ");
if (!numlin_query($iddataini)) $inserire = "NO";
else $iddataini = risul_query($iddataini,0,'idperiodi');
$iddatafine = @esegui_query("select idperiodi from $tableperiodi where datafine = '".aggslashdb($comm_al)."' ");
if (!numlin_query($iddatafine)) $inserire = "NO";
else $iddatafine = risul_query($iddatafine,0,'idperiodi');
if ($iddatafine < $iddataini) $inserire = "NO";
} # fine if ($modificacommper)
if ($inserire != "NO") {
if ($modificacommper) $cond_def = 'and iddatainizio is not NULL';
else $cond_def = 'and iddatainizio is NULL';
$comm = esegui_query("select * from $tableregole where tariffa_commissioni = '$num_tariffa' $cond_def order by iddatainizio ");
$num_comm = numlin_query($comm);
$idregole = esegui_query("select max(idregole) from $tableregole");
$idregole = risul_query($idregole,0,0);
$idregole++;
if ($modificacommissioni) {
if ($num_comm) esegui_query("update $tableregole set motivazione = '$comm_base".aggslashdb($commissioni_percent)."', motivazione2 = '".aggslashdb($commissioni_arrotond)."' where tariffa_commissioni = '$num_tariffa' $cond_def");
else esegui_query("insert into $tableregole (idregole,tariffa_commissioni,motivazione,motivazione2) values ($idregole,'$num_tariffa','$comm_base".aggslashdb($commissioni_percent)."','".aggslashdb($commissioni_arrotond)."') ");
} # fine if ($modificacommissioni)
if ($modificacommper) {
for ($num1 = 0 ; $num1 < $num_comm ; $num1++) {
$iddini = risul_query($comm,$num1,'iddatainizio');
$iddfine = risul_query($comm,$num1,'iddatafine');
if ($iddini <= $iddatafine and $iddfine >= $iddataini) {
$idreg = risul_query($comm,$num1,'idregole');
if ($iddini < $iddataini and $iddfine > $iddatafine) {
$motiv = risul_query($comm,$num1,'motivazione');
$motiv2 = risul_query($comm,$num1,'motivazione2');
esegui_query("update $tableregole set iddatafine = '".($iddataini - 1)."' where idregole = '$idreg' ");
esegui_query("insert into $tableregole (idregole,tariffa_commissioni,iddatainizio,iddatafine,motivazione,motivazione2) values ($idregole,'$num_tariffa','".($iddatafine + 1)."','$iddfine','$motiv','$motiv2') ");
$idregole++;
} # fine if ($iddini < $iddataini and $iddfine > $iddatafine)
else {
if ($iddini >= $iddataini) $iddini = $iddatafine + 1;
if ($iddfine <= $iddatafine) $iddfine = $iddataini - 1;
if ($iddfine < $iddini) esegui_query("delete from $tableregole where idregole = '$idreg' ");
else esegui_query("update $tableregole set iddatainizio = '$iddini', iddatafine = '$iddfine' where idregole = '$idreg' ");
} # fine else if ($iddini < $iddataini and $iddfine > $iddatafine)
} # fine if ($iddini <= $iddatafine and $iddfine >= $iddataini)
} # fine for $num1
esegui_query("insert into $tableregole (idregole,tariffa_commissioni,iddatainizio,iddatafine,motivazione,motivazione2) values ($idregole,'$num_tariffa','$iddataini','$iddatafine','$comm_base".aggslashdb($commissioni_percent)."','".aggslashdb($commissioni_arrotond)."') ");
} # fine if ($modificacommper)
echo mex("Le commissioni sono state modificate",$pag).".<br>";
} # fine if ($inserire != "NO")
else echo mex("Le commissioni sono errate",$pag).".<br>";
} # fine if ($modificacommissioni or $modificacommper)

if ($modificatasse) {
$tasse_percent = formatta_soldi($tasse_percent);
if (controlla_soldi($tasse_percent,"SI") != "SI" or $tasse_percent > 100) {
echo mex("Si deve inserire la percentuale",$pag).".<br>";
$inserire = "NO";
} # fine if (controlla_soldi($tasse_percent,"SI") != "SI" or $tasse_percent > 100)
if ($inserire != "NO") {
if ($tipotariffa == "tutte") {
$ini_for = 1;
$fine_for = $dati_tariffe['num'];
} # fine if ($tipotariffa == "tutte")
else {
$ini_for = $num_tariffa;
$fine_for = $num_tariffa;
} # fine else if ($tipotariffa == "tutte")
for ($num1 = $ini_for ; $num1 <= $fine_for ; $num1++) esegui_query("update $tablenometariffe set tariffa$num1 = '".aggslashdb($tasse_percent)."' where idntariffe = '5'");
echo mex("Le tasse sono state modificate",$pag).".<br>";
} # fine if ($inserire != "NO")
else echo mex("Le tasse sono errate",$pag).".<br>";
} # fine if ($modificatasse)

unlock_tabelle($tabelle_lock);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div>OK</div></button><br>
</div></form>";

} # fine if (($modificacaparra or $modificacommissioni or $modificacommper or $modificatasse) and $priv_mod_tariffe != "n")

else {




# Inserimento rapido costi aggiuntivi
if ($ins_rapido_costo and $priv_ins_costi_agg != "n") {
$inserisci_settimanalmente = "";
$inserisci = "";
$modifica = "";

if ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec") {
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
if ($inseriscicosti) {
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) if (${"seltariffa".$numtariffa}) $tar_attiva = 1;
if (!$tar_attiva) $tar_err = 1;
if ($aggiungi_periodo_permesso or $elimina_periodo_permesso) $inseriscicosti = "";
} # fine if ($inseriscicosti)
} # fine if ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec")

if ($tipocostoagg == "perm_min") {
$nomecostoagg_orig = mex("permanenza minima",$pag);
$tipo_ca = "u";
if ($inseriscicosti and (!$valminmax_pm or controlla_num_pos($valminmax_pm) == "NO")) $vmm_err = 1;
} # fine if ($tipocostoagg == "perm_min")

if ($tipocostoagg == "num_bamb") {
$nomecostoagg_orig = mex("numero di bambini",$pag);
$tipo_ca = "u";
} # fine if ($tipocostoagg == "num_bamb")

if ($tipocostoagg == "letto_agg") {
$nomecostoagg_orig = mex("letto aggiuntivo",$pag);
$tipo_ca = "u";
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
if ($inseriscicosti) {
if ($limite_ca != "n" and $limite_ca != "s") $lim_err = 1;
if ($limite_ca == "s" and (controlla_num_pos($numlimite_ca) == "NO" or $numlimite_ca == 0)) $lim_err = 1;
if (controlla_num_pos($moltmax) == "NO" or !$moltmax) $moltmax_err = 1;
} # fine if ($inseriscicosti)
} # fine if ($tipocostoagg == "letto_agg")

if ($tipocostoagg == "off_spec") {
$nomecostoagg_orig = mex("offerta speciale",$pag);
$tipo_ca = "s";
if ($inseriscicosti) {
if ($assegna_giorni_min and (!$valminmax_pm or controlla_num_pos($valminmax_pm) == "NO")) $agm_err = 1;
if ($assegna_da_giorno_ini == "s" and $ass_gio_ini_segno != "meno" and $ass_gio_ini_segno != "piu") $adg_err = 1;
if ($assegna_da_giorno_ini == "s" and (!$ass_gio_ini_num or controlla_num_pos($ass_gio_ini_num) == "NO")) $adg_err = 1;
if ($assegna_prenota_contemp == "s" and (!$ass_pren_cont_num or $ass_pren_cont_num < 2 or controlla_num_pos($ass_pren_cont_num) == "NO")) $apc_err = 1;
} # fine if ($inseriscicosti)
} # fine if ($tipocostoagg == "off_spec")

$valore_p_ca = "";
if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg" or $tipocostoagg == "off_spec") {
if ($inseriscicosti) {
$tipo_percentuale = "tariffa";
if ($tipo_val_rapido == "t") {
$valore_p_ca = $valore_p_ca_t;
$arrotonda_ca = $arrotonda_ca_t;
} # fine if ($tipo_val_rapido == "t")
if ($tipo_val_rapido == "p") {
$valore_p_ca = $valore_p_ca_p;
$arrotonda_ca = $arrotonda_ca_p;
$tipo_percentuale = "tariffapers";
} # fine if ($tipo_val_rapido == "p")
if ($tipo_val_rapido == "pt") {
$valore_p_ca = $valore_p_ca_pt;
$arrotonda_ca = $arrotonda_ca_pt;
$tipo_percentuale = "totale";
$tipo_ca = "u";
} # fine if ($tipo_val_rapido == "pt")
$valore_f_ca = formatta_soldi($valore_f_ca);
$valore_p_ca = formatta_soldi($valore_p_ca);
$arrotonda_ca = formatta_soldi($arrotonda_ca);
$tasseperc_ca = formatta_soldi($tasseperc_ca);
if ($tipo_val_rapido != "f") {
$valore_f_ca = 0;
if (!strcmp($valore_p_ca,"")) $val_err = 1;
if (controlla_soldi($valore_p_ca) == "NO" or !strcmp($arrotonda_ca,"") or controlla_soldi($arrotonda_ca,"SI") == "NO") $val_err = 1;
if ($valore_p_ca < 0) $val_err = 1;
if ($valore_p_ca > 100 and $tipocostoagg == "num_bamb") $val_err = 1;
} # fine if ($tipo_val_rapido != "f")
if ($valore_f_ca < 0) $val_err = 1;
if (strcmp($valore_f_ca,"") and controlla_soldi($valore_f_ca) == "NO") $val_err = 1;
if ($tipo_val_rapido != "f" and $tipo_val_rapido != "t" and $tipo_val_rapido != "p" and $tipo_val_rapido != "pt") $val_err = 1;
if ($tipocostoagg == "off_spec" and $tipo_val_rapido == "p") $val_err = 1;
if (($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg") and $tipo_val_rapido == "pt") $val_err = 1;
} # fine if ($inseriscicosti)
} # fine if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg" or...
if (($tasseperc_ca and controlla_soldi($tasseperc_ca) == "NO") or $tasseperc_ca > 100 or $tasseperc_ca < 0) $tas_err = 1;

if ($inseriscicosti and ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec")) {
if ($periodi_permessi != "tutti" and $periodi_permessi != "sel") $per_err = 1;
if ($periodi_permessi == "sel") {
if (controlla_num_pos($num_periodi_permessi_sel) == "NO" or $num_periodi_permessi_sel == 0) $per_err = 1;
else {
$file_date_int = implode("",file(C_DATI_PATH."/selectperiodi$anno.1.php"));
$ultima_data = 0;
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
if (str_replace("\\\"".${"pp_dal".$num1}."\\\">","",$file_date_int) == $file_date_int) $per_err = 1;
if (str_replace("\\\"".${"pp_al".$num1}."\\\">","",$file_date_int) == $file_date_int) $per_err = 1;
$nuova_ultima_data = str_replace("-","",${"pp_dal".$num1});
if ($nuova_ultima_data <= $ultima_data) $per_err = 1;
else $ultima_data = $nuova_ultima_data;
$nuova_ultima_data = str_replace("-","",${"pp_al".$num1});
if ($nuova_ultima_data <= $ultima_data) $per_err = 1;
else $ultima_data = $nuova_ultima_data;
} # fine for $num1
} # fine else if (controlla_num_pos($num_periodi_permessi_sel) == "NO" or...
} # fine if ($periodi_permessi == "sel")
} # fine if ($inseriscicosti and ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec"))

if ($inseriscicosti and !strcmp($nomecostoagg,"")) $nom_err = 1;
if ($nomecostoagg) {
$nomecostoagg_contr = $nomecostoagg;
if (get_magic_quotes_gpc()) $nomecostoagg_contr = stripslashes($nomecostoagg_contr);
$nomecostoagg_contr = htmlspecialchars($nomecostoagg_contr);
$esiste_costo = esegui_query("select idntariffe from $tablenometariffe where nomecostoagg = '".aggslashdb($nomecostoagg_contr)."' and tipo_ca $LIKE '".$tipo_ca."_'");
if (numlin_query($esiste_costo)) {
$nom_err = 1;
$nomecostoagg = $nomecostoagg_contr;
} # fine if (numlin_query($esiste_costo))
} # fine if ($nomecostoagg)
if ($tar_err or $vmm_err or $nom_err or $tas_err or $lim_err or $moltmax_err or $val_err or $agm_err or $adg_err or $apc_err or $per_err) $inseriscicosti = "";


if (!$inseriscicosti) {
$mostra_form_iniziale = "NO";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"ins_rapido_costo\" value=\"SI\">
<input type=\"hidden\" name=\"tipocostoagg\" value=\"$tipocostoagg\">
<input type=\"hidden\" name=\"inseriscicosti\" value=\"SI\">
<input type=\"hidden\" name=\"origine\" value=\"$origine\">";
if (!$nomecostoagg) {
$num_nome = 1;
while ($num_nome) {
if ($num_nome == 1) $nomecostoagg = $nomecostoagg_orig;
else $nomecostoagg = $nomecostoagg_orig." $num_nome";
$esiste_costo = esegui_query("select idntariffe from $tablenometariffe where nomecostoagg = '".aggslashdb($nomecostoagg)."' and tipo_ca $LIKE '".$tipo_ca."_'");
if (numlin_query($esiste_costo)) $num_nome++;
else $num_nome = 0;
} # fine while ($num_nome)
} # fine if (!$nomecostoagg)
echo "<h4>".ucfirst($nomecostoagg_orig)."</h4>";
if ($tipocostoagg == "num_bamb") echo "<div style=\"text-align: center; font-size: smaller;\">(".mex("con questo costo si deve includere il numero di bambini anche nel numero di persone",$pag).")</div>";
echo "<br>";
if ($nom_err) echo "<b class=\"colred\">&gt;</b>";
echo mex("Nome del nuovo costo aggiuntivo",$pag).":
<input type=\"text\" name=\"nomecostoagg\" size=\"24\" value=\"$nomecostoagg\">.<br><br>";

if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg" or $tipocostoagg == "off_spec") {
if ($val_err) echo "<b class=\"colred\">&gt;</b>";
if ($tipocostoagg == "num_bamb") echo "".mex("Valore dello sconto per ogni bambino",$pag).":<br>";
if ($tipocostoagg == "letto_agg") echo "".mex("Valore del letto aggiuntivo",$pag).":<br>";
if ($tipocostoagg == "off_spec") echo "".mex("Valore dello sconto",$pag).":<br>";
if (!strcmp($valore_f_ca,"")) $valore_f_ca = 0;
if (!strcmp($valore_p_ca_p,"")) $valore_p_ca_p = 0;
if (!strcmp($valore_p_ca_t,"")) $valore_p_ca_t = 0;
if (!strcmp($valore_p_ca_pt,"")) $valore_p_ca_pt = 0;
if (!strcmp($arrotonda_ca_t,"") or !strcmp($arrotonda_ca_p,"") or !strcmp($arrotonda_ca_pt,"")) {
$arrotonda_ca = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'arrotond_predef' and idutente = '$id_utente'");
$arrotonda_ca = risul_query($arrotonda_ca,0,'valpersonalizza');
if (!strcmp($arrotonda_ca_t,"")) $arrotonda_ca_t = $arrotonda_ca;
if (!strcmp($arrotonda_ca_p,"")) $arrotonda_ca_p = $arrotonda_ca;
if (!strcmp($arrotonda_ca_pt,"")) $arrotonda_ca_pt = $arrotonda_ca;
} # fine if (!strcmp($arrotonda_ca_t,"") or...
if ($tipo_val_rapido != "t" and $tipo_val_rapido != "p") $checked = " checked";
else $checked = "";
echo "<table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"radio\" id=\"tsb_f\" name=\"tipo_val_rapido\" value=\"f\"$checked> ".mex("fisso di",$pag)."
</label><input type=\"text\" name=\"valore_f_ca\" value=\"$valore_f_ca\" size=\"12\"
 onclick=\"document.getElementById('tsb_f').checked='1'\"><label for=\"tsb_f\">$Euro";
if ($tipocostoagg == "off_spec" or $tipocostoagg == "letto_agg") echo " ".mex("al giorno",$pag);
echo "</label></td></tr>";
if ($tipo_val_rapido == "t") $checked = " checked";
else $checked = "";
echo "<tr><td></td><td><label><input type=\"radio\" id=\"tsb_t\" name=\"tipo_val_rapido\" value=\"t\"$checked>
</label><input type=\"text\" name=\"valore_p_ca_t\" value=\"$valore_p_ca_t\" size=\"4\"
 onclick=\"document.getElementById('tsb_t').checked='1'\"><label for=\"tsb_t\">% 
".mex("della tariffa",$pag)." ".mex("arrotondato a",$pag)."
</label><input type=\"text\" name=\"arrotonda_ca_t\" value=\"$arrotonda_ca_t\" size=\"6\"
 onclick=\"document.getElementById('tsb_t').checked='1'\"><label for=\"tsb_t\">$Euro</label></td></tr>";
if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg") {
if ($tipo_val_rapido == "p") $checked = " checked";
else $checked = "";
echo "<tr><td></td><td><label><input type=\"radio\" id=\"tsb_p\" name=\"tipo_val_rapido\" value=\"p\"$checked>
</label><input type=\"text\" name=\"valore_p_ca_p\" value=\"$valore_p_ca_p\" size=\"4\"
 onclick=\"document.getElementById('tsb_p').checked='1'\"><label for=\"tsb_p\">% 
".mex("del prezzo di una persona",$pag)." ".mex("arrotondato a",$pag)."
</label><input type=\"text\" name=\"arrotonda_ca_p\" value=\"$arrotonda_ca_p\" size=\"6\"
 onclick=\"document.getElementById('tsb_p').checked='1'\"><label for=\"tsb_p\">$Euro</label>
 <small>(".mex("solo per tariffe con prezzi a persona",$pag).")</small></td></tr>";
} # fine if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg")
if ($tipocostoagg == "off_spec") {
if ($tipo_val_rapido == "pt") $checked = " checked";
else $checked = "";
echo "<tr><td></td><td><label><input type=\"radio\" id=\"tsb_pt\" name=\"tipo_val_rapido\" value=\"pt\"$checked>
</label><input type=\"text\" name=\"valore_p_ca_pt\" value=\"$valore_p_ca_pt\" size=\"4\"
 onclick=\"document.getElementById('tsb_pt').checked='1'\"><label for=\"tsb_pt\">% 
".mex("del prezzo totale",$pag)." ".mex("arrotondato a",$pag)."
</label><input type=\"text\" name=\"arrotonda_ca_pt\" value=\"$arrotonda_ca_pt\" size=\"6\"
 onclick=\"document.getElementById('tsb_pt').checked='1'\"><label for=\"tsb_pt\">$Euro</label></td></tr>";
} # fine if ($tipocostoagg == "off_spec")
echo "</table><br>";
} # fine if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg" or...

if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg" or $tipocostoagg == "off_spec") {
if (!$tasseperc_ca) $tasseperc_ca = "0";
if ($tas_err) echo "<b class=\"colred\">&gt;</b>";
echo "".mex("Tasse",$pag).": <input type=\"text\" name=\"tasseperc_ca\" value=\"$tasseperc_ca\" size=\"4\">%
 <small>(".mex("il valore del costo si intente con tasse già incluse",$pag).")</small><br><br>";
} # fine if ($tipocostoagg == "num_bamb" or $tipocostoagg == "letto_agg")

if ($tipocostoagg == "perm_min") {
if (!$valminmax_pm) $valminmax_pm = 3;
echo "<br>";
if ($vmm_err) echo "<b class=\"colred\">&gt;</b>";
echo mex("Numero minimo di giorni",$pag).":
<input type=\"text\" name=\"valminmax_pm\" size=\"3\" value=\"$valminmax_pm\">.<br><br>";
} # fine if ($tipocostoagg == "perm_min")

if ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec") {
echo "<table><tr><td>";
if ($tar_err) echo "<b class=\"colred\">&gt;</b>";
echo mex("Applicato alle tariffe",$pag).":</td><td>";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$nometariffa = risul_query($rigatariffe,0,$tariffa);
if ($nometariffa == "") {
$nometariffa = $tariffa;
$nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
else $nometariffa_vedi = mex("tariffa",$pag)."$numtariffa $nometariffa";
if (${"sel".$tariffa} == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"sel$tariffa\" value=\"s\"$checked>
$nometariffa_vedi</label></td></tr>
<tr><td></td><td>";
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
echo "</td></tr></table><br>";
} # fine if ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec")

if ($tipocostoagg == "off_spec") {
if (!strcmp($valminmax_pm,"")) $valminmax_pm = 5;
if (!strcmp($ass_gio_ini_num,"")) $ass_gio_ini_num = 7;
if (!strcmp($ass_pren_cont_num,"")) $ass_pren_cont_num = 2;
if ($assegna_giorni_min == "s") $checked = " checked";
else $checked = "";
if ($agm_err) echo "<b class=\"colred\">&gt;</b>";
echo "<label><input type=\"checkbox\" id=\"ass_g_m\" name=\"assegna_giorni_min\" value=\"s\"$checked>
".mex("Se la prenotazione ha una durata di",$pag)."
 </label><input type=\"text\" name=\"valminmax_pm\" size=\"3\" value=\"$valminmax_pm\"
onclick=\"document.getElementById('ass_g_m').checked='1'\"><label for=\"ass_g_m\">
 ".mex("o più giorni",$pag).".</label><br>";
if ($assegna_da_giorno_ini == "s") $checked = " checked";
else $checked = "";
if ($ass_gio_ini_segno != "piu") { $sel_m = " selected"; $sel_p = ""; }
else { $sel_m = ""; $sel_p = " selected"; }
if ($adg_err) echo "<b class=\"colred\">&gt;</b>";
echo "<label><input type=\"checkbox\" id=\"ass_g_i\" name=\"assegna_da_giorno_ini\" value=\"s\"$checked>
".mex("Se",$pag)."</label>
<select name=\"ass_gio_ini_segno\" onclick=\"document.getElementById('ass_g_i').checked='1'\">
<option value=\"meno\"$sel_m>".mex("meno di",$pag)."</option>
<option value=\"piu\"$sel_p>".mex("più di",$pag)."</option>
</select>
<input type=\"text\" name=\"ass_gio_ini_num\" value=\"".$ass_gio_ini_num."\" size=\"3\"
 onclick=\"document.getElementById('ass_g_i').checked='1'\">
<label for=\"ass_g_i\">".mex("giorni dalla data iniziale della prenotazione quando viene inserita",$pag).".</label><br>";
if ($assegna_prenota_contemp == "s") $checked = " checked";
else $checked = "";
if ($apc_err) echo "<b class=\"colred\">&gt;</b>";
echo "<label><input type=\"checkbox\" id=\"ass_p_c\" name=\"assegna_prenota_contemp\" value=\"s\"$checked>
".mex("Se si inseriscono",$pag)."</label>
<input type=\"text\" name=\"ass_pren_cont_num\" value=\"".$ass_pren_cont_num."\" size=\"3\"
 onclick=\"document.getElementById('ass_p_c').checked='1'\">
<label for=\"ass_p_c\">".mex("o più prenotazioni contemporaneamente",$pag).".</label><br><br><br>";
} # fine if ($tipocostoagg == "off_spec")


if ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec") {
if ($per_err) echo "<b class=\"colred\">&gt;</b>";
echo mex("Periodi in cui viene applicato il costo",$pag).":<br>
<table id=\"tab_per_perm\"><tr><td style=\"width: 20px;\"></td><td>";
if (!$periodi_permessi) $periodi_permessi = "tutti";
if ($periodi_permessi == "tutti") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"periodi_permessi\" value=\"tutti\"$checked> ".mex("In tutti",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($periodi_permessi == "sel") $checked = " checked";
else $checked = "";
if (!$num_periodi_permessi_sel) $num_periodi_permessi_sel = 1;
if ($aggiungi_periodo_permesso) $num_periodi_permessi_sel++;
if ($elimina_periodo_permesso) $num_periodi_permessi_sel--;
echo "<label><input type=\"radio\" id=\"pp_s\" name=\"periodi_permessi\" value=\"sel\"$checked> ".mex("Solo nei periodi selezionati",$pag).":</label></td>
<td onclick=\"document.getElementById('pp_s').checked='1'\">";
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
if ($num1 > 1) echo "<tr><td></td><td></td><td>";
echo mex("dal",$pag)." ";
$pp_dal = ${"pp_dal".$num1};
$pp_al = ${"pp_al".$num1};
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","pp_dal$num1",$pp_dal,"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","pp_al$num1",$pp_al,"","",$id_utente,$tema);
if ($num1 == $num_periodi_permessi_sel) {
$id_minus = " id=\"minus_pp".($num1 + 1)."\"";
if ($num1 > 1) echo "</td><td id=\"minus_pp$num1\"><input class=\"sbutton\" type=\"submit\" name=\"elimina_periodo_permesso\" value=\"".mex("Elimina un periodo",$pag)."\" onclick=\"elim_lin_per_perm();\"></td></tr><tr><td></td><td></td><td>";
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
cella = nlinea.insertCell(2);
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
cella = nlinea.insertCell(3);
cella.id = 'minus_pp'+numcaselle;
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"elimina_periodo_permesso\" value=\"".str_replace("'","\\'",mex("Elimina un periodo",$pag))."\" onclick=\"elim_lin_per_perm();\">';
minus_prec.innerHTML = '';
nlinea = tab_per_perm.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella = nlinea.insertCell(3);
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
cella = nlinea.insertCell(3);
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
<input type=\"hidden\" id=\"numcaselle\" name=\"num_periodi_permessi_sel\" value=\"$num_periodi_permessi_sel\"><br>";
} # fine if ($tipocostoagg == "perm_min" or $tipocostoagg == "off_spec")

if ($tipocostoagg == "letto_agg") {
echo mex("Appartamenti incompatibili con il costo",'unit.php').":<br>
<table><tr><td style=\"height: 2px;\"></td></tr><tr><td style=\"width: 10px;\"></td><td>";
$num_col = 1;
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$idappartamenti = risul_query($appartamenti,$num1,'idappartamenti');
if ($priv_vedi_tab_appartamenti == "s" or $appartamenti_consentiti[$idappartamenti] != "NO") {
if (${"app_incomp_".$num1} == "i") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"app_incomp_$num1\" value=\"i\"$checked>$idappartamenti</label>
<input type=\"hidden\" name=\"idapp_incomp_$num1\" value=\"$idappartamenti\">";
if ($num_col == 4) {
echo "</td></tr><tr><td></td><td>";
$num_col = 0;
} # fine if ($num_col == 4)
else echo "<td style=\"width: 30px;\"></td><td>";
$num_col++;
} # fine if ($priv_vedi_tab_appartamenti == "s" or $appartamenti_consentiti[$idappartamenti] != "NO")
} # fine for $num1
echo "</td></tr></table><br>";
} # fine if ($tipocostoagg == "letto_agg")

if ($tipocostoagg == "letto_agg") {
if ($moltmax_err) echo "<b class=\"colred\">&gt;</b>";
if (!$moltmax) $moltmax = 1;
echo "".mex("Numero massimo",$pag)." ".mex("per appartamento",'unit.php').":
 <input type=\"text\" name=\"moltmax\" value=\"$moltmax\" size=\"3\">.<br><br>";
} # fine if ($tipocostoagg == "letto_agg")

if ($tipocostoagg == "letto_agg") {
echo "<table><tr><td valign=\"top\">";
if ($lim_err) echo "<b class=\"colred\">&gt;</b>";
echo mex("Limitarne il numero che è possibile avere contemporaneamente in uno stesso periodo",$pag)."?<br>
&nbsp;&nbsp;<small>(".mex("limite non considerato per le persone aggiuntive nelle interconnessioni",$pag).")</small>
</td>
<td style=\"width: 130px;\">";
if (!$limite_ca) $limite_ca = "n";
if (!$numlimite_ca) $numlimite_ca = "1";
if ($limite_ca == "n") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"limite_ca\" value=\"n\"$checked> ".mex("No",$pag)."</label><br>";
if ($limite_ca == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" id=\"li_s\" name=\"limite_ca\" value=\"s\"$checked> ".mex("Si",$pag).":</label>
<input type=\"text\" name=\"numlimite_ca\" value=\"$numlimite_ca\" size=\"4\" onclick=\"document.getElementById('li_s').checked='1';\">
</td></tr></table><br>";
} # fine if ($tipocostoagg == "letto_agg")

if ($tipocostoagg == "off_spec") {
if ($id_utente == 1) {
echo "<br><label><input type=\"checkbox\" name=\"agg_modelli\" value=\"s\" checked>
".mex("Aggiungi adesso questo costo a tutte le pagine create per il sito web",$pag).".</label><br><br>";
} # fine if ($id_utente == 1)
} # fine if ($tipocostoagg == "off_spec")

if ($origine) $action = $origine;
else $action = $pag;
echo "<div style=\"text-align: center;\">
<button class=\"aexc\" type=\"submit\"><div>".mex("Inserisci il costo aggiuntivo",$pag)."</div></button>
</div></div></form>
<br><hr style=\"width: 95%\"><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$action\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button><br>
</div></form><br><br><br>";
} # fine if (!$inseriscicosti)


else {
$passo = 12;
if ($tipocostoagg == "perm_min") {
$valore_f_ca = 0;
$categoria_ca = mex("permanenza minima",$pag);
$associa_tariffe = "r";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if (${"seltariffa".$numtariffa}) {
${"minmaxtariffa".$numtariffa} = "min";
${"valminmaxtariffa".$numtariffa} = $valminmax_pm;
} # fine if (${"seltariffa".$numtariffa})
} # fine for $numtariffa
$moltiplica_ca = "1";
$tipo_pp = "u";
$mostra_ca = "n";
$raggruppa_ca = "s";
$letto_ca = "n";
$escludi_da_tot = "n";
$limite_ca = "n";
$mantenere_moltiplica = "s";
$mantenere_periodip = "s";
$mantenere_tariffea = "s";
$agg_utenti = "s";
$agg_modelli = "s";
} # fine if ($tipocostoagg == "perm_min")
if ($tipocostoagg == "num_bamb") {
if ($valore_f_ca) $valore_f_ca = (-1 * $valore_f_ca);
if ($valore_p_ca) $valore_p_ca = (-1 * $valore_p_ca);
$associa_tariffe = "n";
$moltiplica_ca = "c";
$tipo_moltmax = "t";
$meno_moltmax_t = 1;
$periodi_permessi = "tutti";
$tipo_pp = "t";
$mostra_ca = "s";
$raggruppa_ca = "n";
$letto_ca = "n";
$escludi_da_tot = "n";
$limite_ca = "n";
$mantenere_percentuale = "s";
$mantenere_moltiplica = "s";
$agg_utenti = "n";
$agg_modelli = "n";
} # fine if ($tipocostoagg == "num_bamb")
if ($tipocostoagg == "letto_agg") {
if ($tipo_val_rapido == "f") {
$tipo_ca = "s";
$numsett_ca = "t";
$associasett = "n";
} # fine if ($tipo_val_rapido == "f")
$associa_tariffe = "n";
$moltiplica_ca = "1";
$tipo_moltmax = "t";
if ($moltmax > 1) {
$moltiplica_ca = "c";
$tipo_moltmax = "n";
} # fine if ($moltmax > 1)
$periodi_permessi = "tutti";
$tipo_pp = "t";
$mostra_ca = "s";
$raggruppa_ca = "n";
$letto_ca = "s";
$escludi_da_tot = "n";
$mantenere_percentuale = "s";
$mantenere_appi = "s";
$agg_utenti = "n";
$agg_modelli = "n";
} # fine if ($tipocostoagg == "letto_agg")
if ($tipocostoagg == "off_spec") {
if ($valore_f_ca) $valore_f_ca = (-1 * $valore_f_ca);
if ($valore_p_ca) $valore_p_ca = (-1 * $valore_p_ca);
$associa_tariffe = "p";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if (${"seltariffa".$numtariffa}) {
if ($assegna_giorni_min == "s") {
${"minmaxtariffa".$numtariffa} = "min";
${"valminmaxtariffa".$numtariffa} = $valminmax_pm;
} # fine if ($assegna_giorni_min == "s")
elseif ($assegna_da_giorno_ini == "s" or $assegna_prenota_contemp == "s") ${"seltariffa".$numtariffa} = "";
} # fine if (${"seltariffa".$numtariffa})
else ${"incomp_tariffa".$numtariffa} = "i";
} # fine for $numtariffa
$associasett = "s";
$numsett_ca = "t";
$pos_menouna = "p";
$moltiplica_ca = "1";
$tipo_pp = "p";
if ($tipo_ca != "s") {
$associasett = "";
$numsett_ca = "";
$tipo_pp = "t";
} # fine if ($tipo_ca != "s")
$mostra_ca = "n";
$raggruppa_ca = "n";
$letto_ca = "n";
$escludi_da_tot = "n";
$limite_ca = "n";
$mantenere_percentuale = "s";
$mantenere_numsett = "s";
$mantenere_moltiplica = "s";
$mantenere_periodip = "s";
$mantenere_tariffea = "s";
$mantenere_tariffei = "s";
$agg_utenti = "n";
} # fine if ($tipocostoagg == "off_spec")
} # fine else if (!$inseriscicosti)
} # fine if ($ins_rapido_costo and $priv_ins_costi_agg != "n")




# Inserisco i costi aggiuntivi
if ($inseriscicosti and $priv_ins_costi_agg != "n") {
$inserisci_settimanalmente = "";
$inserisci = "";
$modifica = "";
if ($avanti) $passo++;
if ($avanti and $tipo_ca != "s" and $passo == 4) $passo++;
if ($indietro) $passo--;
if ($indietro and $tipo_ca != "s" and $passo == 4) $passo--;
if ($passo > 1) {
$mostra_form_iniziale = "NO";
if ($passo >= 12) {
$tabelle_lock = array($tablenometariffe,$tableprivilegi);
$altre_tab_lock = array($tableanni,$tableperiodi,$tableappartamenti,$tableregole,$tablepersonalizza,$tableutenti,$tablebeniinventario,$tablemagazzini);
} # fine if ($passo >= 12)
else {
$tabelle_lock = array();
$altre_tab_lock = array($tablenometariffe,$tableperiodi,$tableappartamenti,$tableregole,$tablepersonalizza,$tableprivilegi,$tablebeniinventario,$tablemagazzini);
} # fine if ($passo >= 12)
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

if (defined("C_MASSIMO_NUM_COSTI_AGG") and C_MASSIMO_NUM_COSTI_AGG != 0) {
$num_costi_agg_esistenti = esegui_query("select idntariffe from $tablenometariffe where idntariffe > 10");
$num_costi_agg_esistenti = numlin_query($num_costi_agg_esistenti);
if ($num_costi_agg_esistenti >= C_MASSIMO_NUM_COSTI_AGG) $errore = "SI";
} # fine if (defined("C_MASSIMO_NUM_COSTI_AGG") and C_MASSIMO_NUM_COSTI_AGG != 0)

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

if (get_magic_quotes_gpc()) $nomecostoagg = stripslashes($nomecostoagg);
$nomecostoagg = htmlspecialchars($nomecostoagg);
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
if ($errore != "SI") {
$esiste_costo = esegui_query("select idntariffe from $tablenometariffe where nomecostoagg = '".aggslashdb($nomecostoagg)."' and tipo_ca $LIKE '".$tipo_ca."_'");
if (numlin_query($esiste_costo) > 0) {
$messaggio_errore = mex("Costo aggiuntivo già esistente",$pag).".<br>";
$errore = "SI";
} # fine if (numlin_query($esiste_costo) > 0)
} # fine if ($errore != "SI")

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"inseriscicosti\" value=\"SI\">
<input type=\"hidden\" name=\"nomecostoagg\" value=\"$nomecostoagg\">
<input type=\"hidden\" name=\"categoria_ca\" value=\"$categoria_ca\">
<input type=\"hidden\" name=\"tipo_ca\" value=\"$tipo_ca\">";

if ($passo >= 3) {
$valore_f_ca = formatta_soldi($valore_f_ca);
$valore_p_ca = formatta_soldi($valore_p_ca);
$arrotonda_ca = formatta_soldi($arrotonda_ca);
$tasseperc_ca = formatta_soldi($tasseperc_ca);
if (!strcmp($valore_f_ca,"") and !strcmp($valore_p_ca,"")) $errore = "SI";
if (strcmp($valore_f_ca,"") and controlla_soldi($valore_f_ca) == "NO") $errore = "SI";
if (strcmp($valore_p_ca,"") and (controlla_soldi($valore_p_ca) == "NO" or !strcmp($arrotonda_ca,"") or controlla_soldi($arrotonda_ca,"SI") == "NO")) $errore = "SI";
if ($valore_p_ca and $tipo_percentuale != "tariffa" and $tipo_percentuale != "tariffafissa" and $tipo_percentuale != "tariffapers" and $tipo_percentuale != "totale" and $tipo_percentuale != "caparra" and $tipo_percentuale != "resto") $errore = "SI";
if ($valore_p_ca and $tipo_ca == "s" and substr($tipo_percentuale,0,7) != "tariffa") $errore = "SI";
if (($tasseperc_ca and controlla_soldi($tasseperc_ca) == "NO") or $tasseperc_ca > 100 or $tasseperc_ca < 0) $errore = "SI";
if ($tipo_tasse != "t") $tipo_tasse = "p";
echo "<input type=\"hidden\" name=\"valore_f_ca\" value=\"$valore_f_ca\">
<input type=\"hidden\" name=\"tipo_percentuale\" value=\"$tipo_percentuale\">
<input type=\"hidden\" name=\"valore_p_ca\" value=\"$valore_p_ca\">
<input type=\"hidden\" name=\"arrotonda_ca\" value=\"$arrotonda_ca\">
<input type=\"hidden\" name=\"tipo_tasse\" value=\"$tipo_tasse\">
<input type=\"hidden\" name=\"tasseperc_ca\" value=\"$tasseperc_ca\">";
} # fine if ($passo >= 3)

if ($passo >= 4) {
if ($associa_tariffe != "n" and $associa_tariffe != "r" and $associa_tariffe != "s" and $associa_tariffe != "p") $errore = "SI";
if ($tipo_ca != "s" and $associasett != "") $errore = "SI";
if ($tipo_ca == "s" and $associasett == "") $errore = "SI";
if ($valore_p_ca and $associasett == "n") $errore = "SI";
if ($associasett != "" and $associasett != "n" and $associasett != "s") $errore = "SI";
echo "<input type=\"hidden\" name=\"associa_tariffe\" value=\"$associa_tariffe\">
<input type=\"hidden\" name=\"associasett\" value=\"$associasett\">";
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
if ($associa_tariffe != "n") {
#if ($valore_p_ca and $tipo_percentuale == "totale" and ${"sel".$tariffa} == "s") {
#$costo_agg_totale_presente = esegui_query("select * from $tablenometariffe  where idntariffe > 10 and $tariffa != '' and tipo_ca $LIKE '_q' ");
#if (numlin_query($costo_agg_totale_presente) > 0) $errore = "SI";
#} # fine if ($valore_p_ca and $tipo_percentuale == "totale")
if (${"sel".$tariffa} != "" and ${"sel".$tariffa} != "s") $errore = "SI";
} # fine if ($associa_tariffe != "n")
echo "<input type=\"hidden\" name=\"sel$tariffa\" value=\"".${"sel".$tariffa}."\">";
if (${"minmax".$tariffa} != "" and ${"minmax".$tariffa} != "min" and ${"minmax".$tariffa} != "max" and ${"minmax".$tariffa} != "eq" and ${"minmax".$tariffa} != "tra") $errore = "SI";
if (${"valminmax".$tariffa} and (controlla_num_pos(${"valminmax".$tariffa}) == "NO" or ${"valminmax".$tariffa} == 0)) $errore = "SI";
if (${"sel".$tariffa} and ${"minmax".$tariffa} and !${"valminmax".$tariffa}) $errore = "SI";
if (${"valminmax2".$tariffa} and (controlla_num_pos(${"valminmax2".$tariffa}) == "NO" or ${"valminmax2".$tariffa} == 0)) $errore = "SI";
if (${"sel".$tariffa} and ${"minmax".$tariffa} == "tra" and (!${"valminmax2".$tariffa} or ${"valminmax2".$tariffa} <= ${"valminmax".$tariffa})) $errore = "SI";
echo "<input type=\"hidden\" name=\"minmax$tariffa\" value=\"".${"minmax".$tariffa}."\">
<input type=\"hidden\" name=\"valminmax$tariffa\" value=\"".${"valminmax".$tariffa}."\">
<input type=\"hidden\" name=\"valminmax2$tariffa\" value=\"".${"valminmax2".$tariffa}."\">";
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
if ($assegna_da_giorno_ini != "s") $assegna_da_giorno_ini = "";
if ($assegna_da_giorno_ini == "s" and $ass_gio_ini_segno != "meno" and $ass_gio_ini_segno != "piu") $errore = "SI";
if ($assegna_da_giorno_ini == "s" and (!$ass_gio_ini_num or controlla_num_pos($ass_gio_ini_num) == "NO")) $errore = "SI";
if ($assegna_prenota_contemp != "s") $assegna_prenota_contemp = "";
if ($assegna_prenota_contemp == "s" and (!$ass_pren_cont_num or controlla_num_pos($ass_pren_cont_num) == "NO")) $errore = "SI";
echo "<input type=\"hidden\" name=\"assegna_da_giorno_ini\" value=\"$assegna_da_giorno_ini\">
<input type=\"hidden\" name=\"ass_gio_ini_segno\" value=\"$ass_gio_ini_segno\">
<input type=\"hidden\" name=\"ass_gio_ini_num\" value=\"$ass_gio_ini_num\">
<input type=\"hidden\" name=\"assegna_prenota_contemp\" value=\"$assegna_prenota_contemp\">
<input type=\"hidden\" name=\"ass_pren_cont_num\" value=\"$ass_pren_cont_num\">";
} # fine if ($passo >= 4)

if ($passo >= 5) {
echo "<input type=\"hidden\" name=\"numsett_ca\" value=\"$numsett_ca\">
<input type=\"hidden\" name=\"pos_menouna\" value=\"$pos_menouna\">
<input type=\"hidden\" name=\"val1_prime_si\" value=\"$val1_prime_si\">
<input type=\"hidden\" name=\"val2_prime_si\" value=\"$val2_prime_si\">
<input type=\"hidden\" name=\"val1_prime_no\" value=\"$val1_prime_no\">
<input type=\"hidden\" name=\"val2_prime_no\" value=\"$val2_prime_no\">
<input type=\"hidden\" name=\"lun_sel\" value=\"$lun_sel\">
<input type=\"hidden\" name=\"mar_sel\" value=\"$mar_sel\">
<input type=\"hidden\" name=\"mer_sel\" value=\"$mer_sel\">
<input type=\"hidden\" name=\"gio_sel\" value=\"$gio_sel\">
<input type=\"hidden\" name=\"ven_sel\" value=\"$ven_sel\">
<input type=\"hidden\" name=\"sab_sel\" value=\"$sab_sel\">
<input type=\"hidden\" name=\"dom_sel\" value=\"$dom_sel\">";
if ($tipo_ca == "s") {
if ($numsett_ca != "t" and $numsett_ca != "m" and $numsett_ca != "c" and $numsett_ca != "s" and $numsett_ca != "n" and $numsett_ca != "g") $errore = "SI";
if ($tipo_periodi != "g" and $numsett_ca == "g") $errore = "SI";
if ($pos_menouna != "" and !$valore_p_ca and $associasett != "s") $errore = "SI";
if (($pos_menouna != "p" and $pos_menouna != "u") and ($valore_p_ca or $associasett == "s")) $errore = "SI";
if (($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) and $numsett_ca == "c") $errore = "SI";
if ($associasett != "s" and ($numsett_ca == "s" or $numsett_ca == "n" or $numsett_ca == "g")) $errore = "SI";
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
else if ($numsett_ca != "") $errore = "SI";
} # fine if ($passo >= 5)

if ($passo >= 6) {
if ($moltiplica_ca == "1" and strcmp($agg_moltiplica_1,"")) $agg_moltiplica = $agg_moltiplica_1;
if ($moltiplica_ca == "p" and strcmp($agg_moltiplica_p,"")) $agg_moltiplica = $agg_moltiplica_p;
if ($moltiplica_ca == "t" and strcmp($agg_moltiplica_t,"")) $agg_moltiplica = $agg_moltiplica_t;
if ($moltiplica_ca == "c") $agg_moltiplica = 0;
echo "<input type=\"hidden\" name=\"moltiplica_ca\" value=\"$moltiplica_ca\">
<input type=\"hidden\" name=\"tipo_moltmax\" value=\"$tipo_moltmax\">
<input type=\"hidden\" name=\"moltmax\" value=\"$moltmax\">
<input type=\"hidden\" name=\"meno_moltmax_p\" value=\"$meno_moltmax_p\">
<input type=\"hidden\" name=\"meno_moltmax_t\" value=\"$meno_moltmax_t\">
<input type=\"hidden\" name=\"agg_moltiplica\" value=\"$agg_moltiplica\">";
if ($moltiplica_ca != "1" and $moltiplica_ca != "c" and $moltiplica_ca != "p" and $moltiplica_ca != "t") $errore = "SI";
if ($moltiplica_ca == "c") {
if ($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) $errore = "SI";
if ($tipo_moltmax != "n" and $tipo_moltmax != "p" and $tipo_moltmax != "t") $errore = "SI";
if ($tipo_moltmax == "n" and controlla_num_pos($moltmax) == "NO") $errore = "SI";
if ($tipo_moltmax == "p" and controlla_num_pos($meno_moltmax_p) == "NO") $errore = "SI";
if ($tipo_moltmax == "t" and controlla_num_pos($meno_moltmax_t) == "NO") $errore = "SI";
} # fine if ($moltiplica_ca == "c")
else $tipo_moltmax = "n";
if (!strcmp($agg_moltiplica,"")) $agg_moltiplica = 0;
if (controlla_num($agg_moltiplica) == "NO") $errore = "SI";
} # fine if ($passo >= 6)

if ($passo >= 7) {
if (!$beni_inv_elimina) $beni_inv_elimina = "nessuno";
echo "<input type=\"hidden\" name=\"beni_inv_elimina\" value=\"$beni_inv_elimina\">
<input type=\"hidden\" name=\"num_beni_inv_elimina_sel\" value=\"$num_beni_inv_elimina_sel\">
<input type=\"hidden\" name=\"tipo_bie\" value=\"$tipo_bie\">
<input type=\"hidden\" name=\"mag_bie_sel\" value=\"$mag_bie_sel\">";
for ($num1 = 1 ; $num1 <= $num_beni_inv_elimina_sel ; $num1++) {
echo "<input type=\"hidden\" name=\"bene_inv_sel$num1\" value=\"".${"bene_inv_sel".$num1}."\">
<input type=\"hidden\" name=\"molt_bene_inv_sel$num1\" value=\"".${"molt_bene_inv_sel".$num1}."\">";
} # fine for $num1
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
if ($tipo_bie == "a" and ($priv_vedi_inv_app == "n" or $priv_mod_beni_in_app == "n" or $associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp or $associasett == "s" or $moltiplica_ca == "t" or $tipo_moltmax == "t")) $errore = "SI";
} # fine else if (controlla_num_pos($num_beni_inv_elimina_sel) == "NO" or...
} # fine if ($beni_inv_elimina == "sel")
if ($beni_inv_elimina != "nessuno" and $tipo_bie != "a" and $tipo_bie != "m") $errore = "SI";
if (($priv_vedi_inv_mag == "n" or $priv_mod_beni_in_mag == "n") and $tipo_bie == "m") $errore = "SI";
if ($beni_inv_elimina != "nessuno" and $tipo_bie == "m" and !$mag_bie_sel) $errore = "SI";
if ($mag_bie_sel) {
$mag_esist = esegui_query("select * from $tablemagazzini where idmagazzini = '".aggslashdb($mag_bie_sel)."' ".str_replace("where","and",$condizione_mag_propri)." ");
if (numlin_query($mag_esist) != 1) $errore = "SI";
} # fine if ($mag_bie_sel)
} # fine if ($passo >= 7)

if ($passo >= 8) {
echo "<input type=\"hidden\" name=\"periodi_permessi\" value=\"$periodi_permessi\">
<input type=\"hidden\" name=\"num_periodi_permessi_sel\" value=\"$num_periodi_permessi_sel\">
<input type=\"hidden\" name=\"tipo_pp\" value=\"$tipo_pp\">";
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
echo "<input type=\"hidden\" name=\"pp_dal$num1\" value=\"".${"pp_dal".$num1}."\">
<input type=\"hidden\" name=\"pp_al$num1\" value=\"".${"pp_al".$num1}."\">";
} # fine for $num1
if ($periodi_permessi != "tutti" and $periodi_permessi != "sel") $errore = "SI";
if ($periodi_permessi == "sel") {
if (controlla_num_pos($num_periodi_permessi_sel) == "NO" or $num_periodi_permessi_sel == 0) $errore = "SI";
else {
$file_date_int = implode("",file(C_DATI_PATH."/selectperiodi$anno.1.php"));
$ultima_data = 0;
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
if (str_replace("\\\"".${"pp_dal".$num1}."\\\">","",$file_date_int) == $file_date_int) $errore = "SI";
if (str_replace("\\\"".${"pp_al".$num1}."\\\">","",$file_date_int) == $file_date_int) $errore = "SI";
$nuova_ultima_data = str_replace("-","",${"pp_dal".$num1});
if ($nuova_ultima_data <= $ultima_data) $errore = "SI";
else $ultima_data = $nuova_ultima_data;
$nuova_ultima_data = str_replace("-","",${"pp_al".$num1});
if ($nuova_ultima_data <= $ultima_data) $errore = "SI";
else $ultima_data = $nuova_ultima_data;
} # fine for $num1
} # fine else if (controlla_num_pos($num_periodi_permessi_sel) == "NO" or...
} # fine if ($periodi_permessi == "sel")
if ($tipo_pp != "t" and $tipo_pp != "u" and $tipo_pp != "p") $errore = "SI";
if ($tipo_ca != "s" and $tipo_pp == "p") $errore = "SI";
} # fine if ($passo >= 8)

if ($passo >= 9) {
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
if (${"app_incomp_".$num1} != "" and ${"app_incomp_".$num1} != "i") $errore = "SI";
if (${"app_incomp_".$num1} == "i") {
$app_incompatibili = "SI";
if ($priv_vedi_tab_appartamenti != "s" and $appartamenti_consentiti[${"idapp_incomp_".$num1}] == "NO") $errore = "SI";
if ($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) $errore = "SI";
echo "<input type=\"hidden\" name=\"app_incomp_$num1\" value=\"".${"app_incomp_".$num1}."\">
<input type=\"hidden\" name=\"idapp_incomp_$num1\" value=\"".${"idapp_incomp_".$num1}."\">";
} # fine if (${"app_incomp_".$num1} == "i")
} # fine for $num1
} # fine if ($passo >= 9)

if ($passo >= 10) {
$tariffe_incompatibili = "NO";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
if (${"incomp_".$tariffa} != "" and ${"incomp_".$tariffa} != "i") $errore = "SI";
if ((${"sel".$tariffa} == "s" and $associa_tariffe != "n") and ${"incomp_".$tariffa} != "") $errore = "SI";
if (${"incomp_".$tariffa} == "i") {
$tariffe_incompatibili = "SI";
echo "<input type=\"hidden\" name=\"incomp_$tariffa\" value=\"".${"incomp_".$tariffa}."\">";
} # fine if (${"incomp_".$tariffa} == "i")
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
} # fine if ($passo >= 10)

if ($passo >= 11) {
if ($combina_ca != "s" or $raggruppa_ca != "s") $combina_ca = "n";
echo "<input type=\"hidden\" name=\"mostra_ca\" value=\"$mostra_ca\">
<input type=\"hidden\" name=\"raggruppa_ca\" value=\"$raggruppa_ca\">
<input type=\"hidden\" name=\"combina_ca\" value=\"$combina_ca\">
<input type=\"hidden\" name=\"letto_ca\" value=\"$letto_ca\">
<input type=\"hidden\" name=\"escludi_da_tot\" value=\"$escludi_da_tot\">
<input type=\"hidden\" name=\"limite_ca\" value=\"$limite_ca\">
<input type=\"hidden\" name=\"numlimite_ca\" value=\"$numlimite_ca\">";
if (($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) and $mostra_ca != "n") $errore = "SI";
if (($associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp) and $mostra_ca != "s" and $mostra_ca != "n") $errore = "SI";
if ($raggruppa_ca != "s" and $raggruppa_ca != "n") $errore = "SI";
if (($associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) and $combina_ca == "s") $errore = "SI";
if ($combina_ca == "s" and (!strcmp(trim($categoria_ca),"") or $app_incompatibili == "SI" or ($beni_inv_elimina != "nessuno" and $tipo_bie == "a")))  $errore = "SI";
if ($letto_ca != "n" and $letto_ca != "s" and ($moltiplica_ca != "p" and $moltiplica_ca != "t" and $tipo_moltmax != "p" and $tipo_moltmax != "t" and (!$valore_p_ca or substr($tipo_percentuale,0,7) == "tariffa"))) $errore = "SI";
if ($letto_ca != "n" and ($moltiplica_ca == "p" or $moltiplica_ca == "t" or $tipo_moltmax == "p" or $tipo_moltmax == "t" or ($valore_p_ca and substr($tipo_percentuale,0,7) != "tariffa"))) $errore = "SI";
if ($escludi_da_tot != "n" and $escludi_da_tot != "s") $errore = "SI";
if ($limite_ca != "n" and $limite_ca != "s" and ($tipo_ca != "s" or $associasett == "s" or ($numsett_ca == "t" and ($periodi_permessi == "tutti" or $tipo_pp != "p")))) $errore = "SI";
if ($tipo_ca == "s" and $associasett != "s" and ($numsett_ca != "t" or ($periodi_permessi != "tutti" and $tipo_pp == "p"))) $limite_ca = "";
if ($limite_ca == "s" and (controlla_num_pos($numlimite_ca) == "NO" or $numlimite_ca == 0)) $errore = "SI";
if ($id_utente == 1) $utenti_attiva_costi_sel = esegui_query("select * from $tableprivilegi where anno = '$anno' and costi_agg_consentiti $LIKE 's%' ");
} # fine if ($passo >= 11)

if ($passo >= 12) {
if ($mantenere_percentuale != "s" or !$valore_p_ca) $mantenere_percentuale = "n";
if (($mantenere_percentuale == "n" and $valore_p_ca) and ($tipo_ca == "s" and $numsett_ca != "c")) $mantenere_numsett = "n";
if ($mantenere_numsett != "s" or ($tipo_ca != "s" or $numsett_ca == "c")) $mantenere_numsett = "n";
if (($mantenere_percentuale == "n" and $valore_p_ca) and ($moltiplica_ca != "c" and $moltiplica_ca != "1")) $mantenere_moltiplica = "n";
if ($mantenere_moltiplica != "s" or ($moltiplica_ca == "c" and $tipo_moltmax == "n" and !$moltmax)) $mantenere_moltiplica = "n";
if ($mantenere_beniinv != "s" or $beni_inv_elimina == "nessuno") $mantenere_beniinv = "n";
if ($mantenere_periodip != "s" or $periodi_permessi == "tutti") $mantenere_periodip = "n";
if ($mantenere_appi != "s" or $app_incompatibili != "SI") $mantenere_appi = "n";
if ($mantenere_tariffea != "s" or $associa_tariffe == "n") $mantenere_tariffea = "n";
if ($mantenere_tariffei != "s" or $tariffe_incompatibili != "SI") $mantenere_tariffei = "n";
if ($mantenere_comb != "s" or $combina_ca != "s") $mantenere_comb = "n";
if ($agg_utenti != "s" or $id_utente != 1) $agg_utenti = "n";
if ($agg_modelli != "s" or $id_utente != 1) $agg_modelli = "n";
echo "<input type=\"hidden\" name=\"mantenere_percentuale\" value=\"$mantenere_percentuale\">
<input type=\"hidden\" name=\"mantenere_numsett\" value=\"$mantenere_numsett\">
<input type=\"hidden\" name=\"mantenere_moltiplica\" value=\"$mantenere_moltiplica\">
<input type=\"hidden\" name=\"mantenere_beniinv\" value=\"$mantenere_beniinv\">
<input type=\"hidden\" name=\"mantenere_periodip\" value=\"$mantenere_periodip\">
<input type=\"hidden\" name=\"mantenere_tariffea\" value=\"$mantenere_tariffea\">
<input type=\"hidden\" name=\"mantenere_appi\" value=\"$mantenere_appi\">
<input type=\"hidden\" name=\"mantenere_tariffei\" value=\"$mantenere_tariffei\">
<input type=\"hidden\" name=\"mantenere_comb\" value=\"$mantenere_comb\">
<input type=\"hidden\" name=\"agg_utenti\" value=\"$agg_utenti\">
<input type=\"hidden\" name=\"agg_modelli\" value=\"$agg_modelli\">";
} # fine if ($passo >= 12)

if ($passo >= 13) $errore = "SI";

if ($errore != "SI") {

# Preprocesso di alcuni passi

if ($passo == 6) {
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
if (!$opt_beni_inv or (($priv_vedi_inv_app == "n" or $priv_mod_beni_in_app == "n" or $associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp or $associasett == "s" or $moltiplica_ca == "t" or $tipo_moltmax == "t") and !$opt_mag)) {
if ($indietro) $passo--;
else $passo++;
} # fine if (!$opt_beni_inv or...
} # fine if ($passo == 6)

if ($passo == 8) {
if ($priv_vedi_tab_appartamenti == "n" or $associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) {
if ($indietro) $passo--;
else $passo++;
} # fine if ($priv_vedi_tab_appartamenti == "n" or $associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp)
} # fine if ($passo == 8)

if ($passo == 9) {
unset($lista_tariffe_p9);
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$nometariffa = risul_query($rigatariffe,0,$tariffa);
if ($nometariffa == "") {
$nometariffa = $tariffa;
$nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
else $nometariffa_vedi = mex("tariffa",$pag)."$numtariffa $nometariffa";
if (${"sel".$tariffa} != "s" or $associa_tariffe == "n") {
if (${"incomp_".$tariffa} == "i") $checked = " checked";
else $checked = "";
$lista_tariffe_p9 .= "<label><input type=\"checkbox\" name=\"incomp_$tariffa\" value=\"i\"$checked>$nometariffa_vedi
</label></td></tr><tr><td></td><td>";
} # fine if (${"sel".$tariffa} != "s" or $associa_tariffe == "n")
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
if (!$lista_tariffe_p9) {
if ($indietro) {
$passo--;
if ($priv_vedi_tab_appartamenti == "n" or $associa_tariffe != "n" or $assegna_da_giorno_ini or $assegna_prenota_contemp) $passo--;
} # fine if ($indietro)
else $passo++;
} # fine if (!$lista_tariffe_p9)
} # fine if ($passo == 9)

if ($passo == 11) {
if (!$valore_p_ca and ($tipo_ca != "s" or $numsett_ca == "c") and $moltiplica_ca == "c" and $beni_inv_elimina == "nessuno" and $periodi_permessi == "tutti" and $associa_tariffe == "n" and $app_incompatibili != "SI" and $tariffe_incompatibili != "SI" and $combina_ca != "s") {
if ($indietro) $passo--;
else $passo++;
$mantenere_percentuale = "n";
$mantenere_numsett = "n";
$mantenere_moltiplica = "n";
$mantenere_beniinv = "n";
$mantenere_periodip = "n";
$mantenere_tariffea = "n";
$mantenere_appi = "n";
$mantenere_tariffei = "n";
$mantenere_comb = "n";
} # fine if (!$valore_p_ca and ($tipo_ca != "s" or $numsett_ca == "c") and $moltiplica_ca == "c" and...
} # fine if ($passo == 11)

# Processo dei passi

if ($passo == 2) {
echo "<table><tr><td valign=\"top\">".mex("Valore del costo",$pag).":</td><td>";
if (!strcmp($valore_f_ca,"")) $valore_f_ca = 0;
echo " ".mex("fisso di",$pag)."
<input type=\"text\" name=\"valore_f_ca\" value=\"$valore_f_ca\" size=\"12\">$Euro <b>+</b></td></tr>
<tr><td></td><td>";
echo " ".mex("percentuale su",$pag)."
<select name=\"tipo_percentuale\">";
if ($tipo_percentuale == "tariffa") $selected = " selected";
else $selected = "";
echo "<option value=\"tariffa\"$selected>".mex("la tariffa",$pag)."</option>";
if ($tipo_percentuale == "tariffafissa") $selected = " selected";
else $selected = "";
echo "<option value=\"tariffafissa\"$selected>".mex("parte fissa della tariffa",$pag)."</option>";
if ($tipo_percentuale == "tariffapers") $selected = " selected";
else $selected = "";
echo "<option value=\"tariffapers\"$selected>".mex("parte per una persona della tariffa",$pag)."</option>";
if ($tipo_ca != "s") {
if ($tipo_percentuale == "totale") $selected = " selected";
else $selected = "";
echo "<option value=\"totale\"$selected>".mex("il prezzo totale",$pag)."</option>";
if ($tipo_percentuale == "caparra") $selected = " selected";
else $selected = "";
echo "<option value=\"caparra\"$selected>".mex("la caparra",$pag)."</option>";
if ($tipo_percentuale == "resto") $selected = " selected";
else $selected = "";
echo "<option value=\"resto\"$selected>".mex("totale meno caparra",$pag)."</option>";
} # fine if ($tipo_ca != "s")
if (!strcmp($valore_p_ca,"")) $valore_p_ca = 0;
if (!strcmp($arrotonda_ca,"")) {
$arrotonda_ca = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'arrotond_predef' and idutente = '$id_utente'");
$arrotonda_ca = risul_query($arrotonda_ca,0,'valpersonalizza');
} # fine if (!strcmp($arrotonda_ca,""))
echo "</select> ".mex("del",$pag)."
<input type=\"text\" name=\"valore_p_ca\" value=\"$valore_p_ca\" size=\"4\">% ".mex("arrotondato a",$pag)."
<input type=\"text\" name=\"arrotonda_ca\" value=\"$arrotonda_ca\" size=\"6\">$Euro</td></tr>
</table><br>";
if (!$tasseperc_ca) $tasseperc_ca = "0";
if ($tipo_tasse == "t") { $checked_p = ""; $checked_t = " checked"; }
else { $checked_p = " checked"; $checked_t = ""; }
echo "<table><tr><td>
".mex("Tasse",$pag).":</td><td><input type=\"radio\" id=\"t_tas\" name=\"tipo_tasse\" value=\"p\"$checked_p>
<input type=\"text\" name=\"tasseperc_ca\" value=\"$tasseperc_ca\" size=\"4\" onclick=\"document.getElementById('t_tas').checked='1';\"><label for=\"t_tas\">%
 (".mex("il valore del costo si intente con tasse già incluse",$pag).")</label></td></tr>
<tr><td></td><td>
<label><input type=\"radio\" name=\"tipo_tasse\" value=\"t\"$checked_t>
".mex("considerare l'intero costo come tasse",$pag)."</label>
</td></tr></table><br><br>";
} # fine if ($passo == 2)

if ($passo == 3) {
echo mex("Assegnare automaticamente con le tariffe selezionate",$pag)."? ";
echo "<br>
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
<table><tr><td style=\"width: 10px;\"></td><td>";
if (!$associa_tariffe) $associa_tariffe = "n";
if ($associa_tariffe == "n") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" id=\"at_n\" name=\"associa_tariffe\" value=\"n\"$checked> ".mex("No",$pag)."</label> ";
if ($associa_tariffe == "p") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" id=\"at_p\" name=\"associa_tariffe\" value=\"p\"$checked> ".mex("Se possibile",$pag)."</label> ";
if ($associa_tariffe == "r") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"associa_tariffe\" value=\"r\"$checked> ".mex("Sempre in periodi permessi",$pag)."</label> ";
if ($associa_tariffe == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"associa_tariffe\" value=\"s\"$checked> ".mex("Sempre",$pag)."</label> ";
echo "</td></tr></table>
<table><tr><td></td><td>".mex("Tariffe",$pag).":</td><td>";
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
#if ($valore_p_ca and $tipo_percentuale == "totale") {
#$costo_agg_totale_presente = esegui_query("select * from $tablenometariffe  where idntariffe > 10 and $tariffa != '' and tipo_ca $LIKE '_q' ");
#if (numlin_query($costo_agg_totale_presente) > 0) $mostra_tariffa_corrente = "NO";
#else $mostra_tariffa_corrente = "SI";
#} # fine if ($valore_p_ca and $tipo_percentuale == "totale")
#else $mostra_tariffa_corrente = "SI";
#if ($mostra_tariffa_corrente == "SI") {
$nometariffa = risul_query($rigatariffe,0,$tariffa);
if ($nometariffa == "") {
$nometariffa = $tariffa;
$nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
else $nometariffa_vedi = mex("tariffa",$pag)."$numtariffa $nometariffa";
if (${"sel".$tariffa} == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"sel$tariffa\" value=\"s\" onchange=\"asso_rdbx('sel$tariffa','at_p','at_n');\"$checked>
$nometariffa_vedi</label>";
echo " (<select name=\"minmax$tariffa\" id=\"minmax$tariffa\" onchange=\"agg_sel_giorni_associa_tariffa('$tariffa','');\">";
if (!${"minmax".$tariffa}) $selected = " selected";
else $selected = "";
echo "<option value=\"\"$selected>----</option>";
if (${"minmax".$tariffa} == "min") $selected = " selected";
else $selected = "";
echo "<option value=\"min\"$selected>".mex("minimo",$pag)."</option>";
if (${"minmax".$tariffa} == "max") $selected = " selected";
else $selected = "";
echo "<option value=\"max\"$selected>".mex("massimo",$pag)."</option>";
if (${"minmax".$tariffa} == "eq") $selected = " selected";
else $selected = "";
echo "<option value=\"eq\"$selected>".mex("esattamente",$pag)."</option>
</select>
<input type=\"text\" name=\"valminmax$tariffa\" value=\"".${"valminmax".$tariffa}."\" size=\"3\">
<span id=\"valminmax2$tariffa\"></span>".mex("$parola_settimane",$pag).")";
echo "<script type=\"text/javascript\">
<!--
var sel_corr = document.getElementById('minmax$tariffa');
var n_opt_sel_corr = document.createElement('option');
n_opt_sel_corr.text = '".mex("tra",$pag)."';
n_opt_sel_corr.value = 'tra';
sel_corr.add(n_opt_sel_corr,null);
";
if (${"minmax".$tariffa} == "tra") echo "sel_corr.selectedIndex = 4;
agg_sel_giorni_associa_tariffa('$tariffa','".${"valminmax2".$tariffa}."');
";
echo "-->
</script>
</td></tr>
<tr><td></td><td></td><td>";
#} # fine if ($mostra_tariffa_corrente == "SI")
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
echo "</td></tr></table><br>";
if ($assegna_da_giorno_ini == "s") $checked = " checked";
else $checked = "";
if ($ass_gio_ini_segno != "piu") { $sel_m = " selected"; $sel_p = ""; }
else { $sel_m = ""; $sel_p = " selected"; }
echo "<small><label><input type=\"checkbox\" id=\"ass_g_i\" name=\"assegna_da_giorno_ini\" value=\"s\"$checked>
".mex("Quando possibile assegna automaticamente se mancano",$pag)."</label>
<select name=\"ass_gio_ini_segno\">
<option value=\"meno\"$sel_m>".mex("meno di",$pag)."</option>
<option value=\"piu\"$sel_p>".mex("più di",$pag)."</option>
</select>
<input type=\"text\" name=\"ass_gio_ini_num\" value=\"".$ass_gio_ini_num."\" size=\"3\">
<label for=\"ass_g_i\">".mex("giorni dalla data iniziale della prenotazione quando viene inserita",$pag).".</label><br>";
if ($assegna_prenota_contemp == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" id=\"ass_p_c\" name=\"assegna_prenota_contemp\" value=\"s\"$checked>
".mex("Quando possibile assegna automaticamente se si inseriscono",$pag)."</label>
<input type=\"text\" name=\"ass_pren_cont_num\" value=\"".$ass_pren_cont_num."\" size=\"3\">
<label for=\"ass_p_c\">".mex("o più prenotazioni contemporaneamente",$pag).".</label><br></small><br>";
if ($tipo_ca == "s") {
echo mex("Associare a specifi$sillaba_che $parola_settimane della prenotazione",$pag)."?";
echo " <select name=\"associasett\">";
if (!$valore_p_ca) {
if ($associasett == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>";
} # fine if (!$valore_p_ca)
if ($associasett == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>
</select><br><br>";
} # fine if ($tipo_ca == "s")
} # fine if ($passo == 3)

if ($passo == 4) {
echo mex("$parola_Le $parola_settimane a cui applicare il costo",$pag).":<br>
<table><tr><td style=\"width: 20px;\"></td><td>";
if (!$numsett_ca) $numsett_ca = "t";
if ($numsett_ca == "t") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"numsett_ca\" value=\"t\"$checked> ".mex("Tutt$lettera_e",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($numsett_ca == "m") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" id=\"ns_m\" name=\"numsett_ca\" value=\"m\"$checked> ".mex("Tutt$lettera_e meno",$pag);
if ($valore_p_ca or $associasett == "s") {
echo "</label> <select name=\"pos_menouna\" onchange=\"document.getElementById('ns_m').checked='1';\">";
if ($pos_menouna == "p") $selected = " selected";
else $selected = "";
echo "<option value=\"p\"$selected>".mex("$parola_la prim$lettera_a",$pag)."</option>";
if ($pos_menouna == "u") $selected = " selected";
else $selected = "";
echo "<option value=\"u\"$selected>".mex("l'ultim$lettera_a",$pag)."</option>";
echo "</select></td></tr>";
} # fine if ($valore_p_ca or $associasett == "s")
else echo " ".mex("un$lettera_a",$pag)."</label></td></tr>";
echo "<tr><td></td><td>";
if ($associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp) {
if ($numsett_ca == "c") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"numsett_ca\" value=\"c\"$checked> ".mex("Chiedere",$pag)."</label></td></tr>
<tr><td></td><td>";
} # fine if ($associa_tariffe == "n" and...
if ($associasett == "s") {
if ($numsett_ca == "s") $checked = " checked";
else $checked = "";
if (!$val1_prime_si) $val1_prime_si = "1";
if (!$val2_prime_si) $val2_prime_si = "1";
echo "<input type=\"radio\" id=\"ns_s\" name=\"numsett_ca\" value=\"s\"$checked>
 <input type=\"text\" name=\"val1_prime_si\" value=\"$val1_prime_si\" size=\"3\" onfocus=\"document.getElementById('ns_s').checked='1';\">
 <label for=\"ns_s\">".mex("$parola_settimane sì",$pag)." ".mex("e",$pag)."</label>
 <input type=\"text\" name=\"val2_prime_si\" value=\"$val2_prime_si\" size=\"3\" onfocus=\"document.getElementById('ns_s').checked='1';\">
 <label for=\"ns_s\">".mex("$parola_settimane no",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($numsett_ca == "n") $checked = " checked";
else $checked = "";
if (!$val1_prime_no) $val1_prime_no = "1";
if (!$val2_prime_no) $val2_prime_no = "1";
echo "<input type=\"radio\" id=\"ns_n\" name=\"numsett_ca\" value=\"n\"$checked>
 <input type=\"text\" name=\"val1_prime_no\" value=\"$val1_prime_no\" size=\"3\" onfocus=\"document.getElementById('ns_n').checked='1';\">
 <label for=\"ns_n\">".mex("$parola_settimane no",$pag)." ".mex("e",$pag)."</label>
 <input type=\"text\" name=\"val2_prime_no\" value=\"$val2_prime_no\" size=\"3\" onfocus=\"document.getElementById('ns_n').checked='1';\">
 <label for=\"ns_n\">".mex("$parola_settimane sì",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($tipo_periodi == "g") {
if ($numsett_ca == "g") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" id=\"ns_g\" name=\"numsett_ca\" value=\"g\"$checked> 
".mex("Giorni della settimana selezionati",$pag).":</label><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if ($lun_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"lun_sel\" value=\"s\" onchange=\"asso_rdbx('lun_sel','ns_g');\"$checked>".mex("Lunedì",$pag)."</label>&nbsp;&nbsp;&nbsp;";
if ($mar_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"mar_sel\" value=\"s\" onchange=\"asso_rdbx('mar_sel','ns_g');\"$checked>".mex("Martedì",$pag)."</label><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if ($mer_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"mer_sel\" value=\"s\" onchange=\"asso_rdbx('mer_sel','ns_g');\"$checked>".mex("Mercoledì",$pag)."</label>&nbsp;&nbsp;&nbsp;";
if ($gio_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"gio_sel\" value=\"s\" onchange=\"asso_rdbx('gio_sel','ns_g');\"$checked>".mex("Giovedì",$pag)."</label><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if ($ven_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"ven_sel\" value=\"s\" onchange=\"asso_rdbx('ven_sel','ns_g');\"$checked>".mex("Venerdì",$pag)."</label>&nbsp;&nbsp;&nbsp;";
if ($sab_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"sab_sel\" value=\"s\" onchange=\"asso_rdbx('sab_sel','ns_g');\"$checked>".mex("Sabato",$pag)."</label><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if ($dom_sel == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"dom_sel\" value=\"s\" onchange=\"asso_rdbx('dom_sel','ns_g');\"$checked>".mex("Domenica",$pag)."</label></td></tr>";
} # fine if ($tipo_periodi == "g")
} # fine if ($associasett == "s")
echo "</table><br>";
} # fine if ($passo == 4)

if ($passo == 5) {
echo mex("Moltiplicare il costo per",$pag).":<br>
<table><tr><td style=\"width: 20px;\"></td>";
if (!$moltiplica_ca) $moltiplica_ca = "1";
if ($moltiplica_ca == "1") $checked = " checked";
else $checked = "";
$agg_moltiplica_1 = 0;
if ($moltiplica_ca == "1" and $agg_moltiplica) $agg_moltiplica_1 = $agg_moltiplica;
echo "<td colspan=\"2\"><label><input type=\"radio\" id=\"mo_1\" name=\"moltiplica_ca\" value=\"1\"$checked>
".mex("Uno",$pag).", ".mex("aggiungendo",$pag)." </label>
<input type=\"text\" name=\"agg_moltiplica_1\" value=\"$agg_moltiplica_1\" size=\"3\" onclick=\"document.getElementById('mo_1').checked='1';\"></td></tr>";
if ($moltiplica_ca == "p") $checked = " checked";
else $checked = "";
$agg_moltiplica_p = 0;
if ($moltiplica_ca == "p" and $agg_moltiplica) $agg_moltiplica_p = $agg_moltiplica;
echo "<tr><td></td>
<td colspan=\"2\"><label><input type=\"radio\" id=\"mo_p\" name=\"moltiplica_ca\" value=\"p\"$checked>
".mex("Numero di persone",$pag).", ".mex("aggiungendo",$pag)." </label>
<input type=\"text\" name=\"agg_moltiplica_p\" value=\"$agg_moltiplica_p\" size=\"3\" onclick=\"document.getElementById('mo_p').checked='1';\">
<small>(".mex("escluse quelle dei costi con letti aggiuntivi",$pag).")</small></td></tr>";
if ($moltiplica_ca == "t") $checked = " checked";
else $checked = "";
$agg_moltiplica_t = 0;
if ($moltiplica_ca == "t" and $agg_moltiplica) $agg_moltiplica_t = $agg_moltiplica;
echo "<tr><td></td>
<td colspan=\"2\"><label><input type=\"radio\" id=\"mo_t\" name=\"moltiplica_ca\" value=\"t\"$checked>
".mex("Numero di persone totale",$pag).", ".mex("aggiungendo",$pag)." </label>
<input type=\"text\" name=\"agg_moltiplica_t\" value=\"$agg_moltiplica_t\" size=\"3\" onclick=\"document.getElementById('mo_t').checked='1';\"></td></tr>";
if ($associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp) {
if ($moltiplica_ca == "c") $checked = " checked";
else $checked = "";
if (!$tipo_moltmax) $tipo_moltmax = "n";
if ($tipo_moltmax == "n") $checked_tipo_n = " checked";
else $checked_tipo_n = "";
if ($tipo_moltmax == "p") $checked_tipo_p = " checked";
else $checked_tipo_p = "";
if ($tipo_moltmax == "t") $checked_tipo_t = " checked";
else $checked_tipo_t = "";
if (!strcmp($moltmax,"")) $moltmax = 0;
if (!strcmp($meno_moltmax_p,"")) $meno_moltmax_p = 0;
if (!strcmp($meno_moltmax_t,"")) $meno_moltmax_t = 0;
echo "<tr><td></td>
<td style=\"width: 10px; white-space: nowrap;\"><label><input type=\"radio\" id=\"mo_c\" name=\"moltiplica_ca\" value=\"c\"$checked>
".mex("Chiedere",$pag).". ".mex("Numero massimo",$pag).":</label></td>
<td onclick=\"document.getElementById('mo_c').checked='1';\">
<input type=\"radio\" id=\"mm_n\" name=\"tipo_moltmax\" value=\"n\"$checked_tipo_n>
 <input type=\"text\" name=\"moltmax\" value=\"$moltmax\" size=\"3\" onclick=\"document.getElementById('mm_n').checked='1';\">
 <label for=\"mm_n\">(".mex("0 se illimitato",$pag).")</label></td></tr>
<tr><td colspan=\"2\"></td><td onclick=\"document.getElementById('mo_c').checked='1';\">
<label><input type=\"radio\" id=\"mm_p\" name=\"tipo_moltmax\" value=\"p\"$checked_tipo_p>
 ".mex("Numero di persone",$pag)." ".mex("meno",$pag)."</label>
 <input type=\"text\" name=\"meno_moltmax_p\" value=\"$meno_moltmax_p\" size=\"2\" onclick=\"document.getElementById('mm_p').checked='1';\"></td></tr>
<tr><td colspan=\"2\"></td><td onclick=\"document.getElementById('mo_c').checked='1';\">
<label><input type=\"radio\" id=\"mm_t\" name=\"tipo_moltmax\" value=\"t\"$checked_tipo_t>
 ".mex("Numero di persone totale",$pag)." ".mex("meno",$pag)."</label>
 <input type=\"text\" name=\"meno_moltmax_t\" value=\"$meno_moltmax_t\" size=\"2\" onclick=\"document.getElementById('mm_t').checked='1';\"></td></tr>";
} # fine if ($associa_tariffe == "n" and...
echo "</table><small>(".mex("il costo viene moltiplicato per zero se la somma è negativa",$pag).")</small><br><br>";
} # fine if ($passo == 5)

if ($passo == 6) {
echo mex("Beni dell'inventario da eliminare quando si inserisce il costo",$pag).":<br>
<table id=\"tab_beni_inv\"><tr><td style=\"width: 20px;\"></td><td>";
if (!$beni_inv_elimina) $beni_inv_elimina = "nessuno";
if ($beni_inv_elimina == "nessuno") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"beni_inv_elimina\" value=\"nessuno\"$checked> ".mex("Nessuno",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($beni_inv_elimina == "sel") $checked = " checked";
else $checked = "";
if (!$num_beni_inv_elimina_sel) $num_beni_inv_elimina_sel = 1;
if ($aggiungi_bene_inv_elimina) $num_beni_inv_elimina_sel++;
if ($elimina_bene_inv_elimina) $num_beni_inv_elimina_sel--;
echo "<label><input type=\"radio\" id=\"bi_s\" name=\"beni_inv_elimina\" value=\"sel\"$checked> ".mex("Beni selezionati",$pag).":</label></td>
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
cella = nlinea.insertCell(2);
cella.innerHTML = '<select name=\"bene_inv_sel'+numcaselle_inv+'\">\
<option value=\"\">----<\/option>\
".str_replace("'","\\'",str_replace("/","\\/",str_replace("\n","\\\n",str_replace("\\","\\\\",$opt_beni_inv))))."\
<\/select> x <input type=\"text\" name=\"molt_bene_inv_sel'+numcaselle_inv+'\" value=\"1\" size=\"4\">';
cella = nlinea.insertCell(3);
cella.id = 'minus'+numcaselle_inv;
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"elimina_bene_inv_elimina\" value=\"".str_replace("'","\\'",mex("Elimina un bene",$pag))."\" onclick=\"elim_lin_bene_inv();\">';
minus_prec.innerHTML = '';
nlinea = tab_beni_inv.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella = nlinea.insertCell(3);
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
cella = nlinea.insertCell(3);
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
if ($num1 > 1) echo "<tr><td></td><td></td><td onclick=\"document.getElementById('bi_s').checked='1'\">";
if (!${"molt_bene_inv_sel".$num1}) ${"molt_bene_inv_sel".$num1} = 1;
echo "<select name=\"bene_inv_sel$num1\">
<option value=\"\">----</option>".str_replace("\"".${"bene_inv_sel".$num1}."\">","\"".${"bene_inv_sel".$num1}."\" selected>",$opt_beni_inv)."
</select> x <input type=\"text\" name=\"molt_bene_inv_sel$num1\" value=\"".${"molt_bene_inv_sel".$num1}."\" size=\"4\">";
if ($num1 == $num_beni_inv_elimina_sel) {
$id_minus = " id=\"minus".($num1 + 1)."\"";
if ($num1 > 1) echo "</td><td id=\"minus$num1\"><input class=\"sbutton\" type=\"submit\" name=\"elimina_bene_inv_elimina\" value=\"".mex("Elimina un bene",$pag)."\" onclick=\"elim_lin_bene_inv();\"></td></tr><tr><td></td><td></td><td>";
else $id_minus = " id=\"minus$num1\"";
echo "</td><td$id_minus><input class=\"sbutton\" type=\"submit\" name=\"aggiungi_bene_inv_elimina\" value=\"".mex("Aggiungi un bene",$pag)."\" onclick=\"agg_lin_bene_inv();\">";
} # fine if ($num1 == $num_beni_inv_elimina_sel)
else echo "</td><td id=\"minus$num1\">";
echo "</td></tr>";
} # fine for $num1
echo "</table>
<input type=\"hidden\" id=\"numcaselle_inv\" name=\"num_beni_inv_elimina_sel\" value=\"$num_beni_inv_elimina_sel\">
<table>";
if ($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n" and $associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp and $associasett != "s" and $moltiplica_ca != "t" and $tipo_moltmax != "t") {
if (!$tipo_bie) $tipo_bie = "a";
if ($tipo_bie == "a") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 50px;\"></td><td>
<label><input type=\"radio\" name=\"tipo_bie\" value=\"a\"$checked>
".mex("elimina i beni dall'inventario dell'appartamento occupato dalla prenotazione",'unit.php')."</label></td></tr>";
} # fine if ($priv_vedi_inv_app != "n" and $priv_mod_beni_in_app != "n" and...
if ($opt_mag) {
if (!$tipo_bie) $tipo_bie = "m";
if ($tipo_bie == "m") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 50px;\"></td><td><label>
<input type=\"radio\" id=\"tbi_m\" name=\"tipo_bie\" value=\"m\"$checked> ".mex("elimina i beni dall'inventario del magazzino",$pag)."
</label><select name=\"mag_bie_sel\" onfocus=\"document.getElementById('tbi_m').checked='1'\">
<option value=\"\">----</option>".str_replace("\"$mag_bie_sel\">","\"$mag_bie_sel\" selected>",$opt_mag)."
</select></td></tr>";
} # fine if ($opt_mag)
echo "</table><br>";
} # fine if ($passo == 6)

if ($passo == 7) {
echo mex("Periodi in cui è permesso inserire il costo",$pag).":<br>
<table id=\"tab_per_perm\"><tr><td style=\"width: 20px;\"></td><td>";
if (!$periodi_permessi) $periodi_permessi = "tutti";
if ($periodi_permessi == "tutti") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"periodi_permessi\" value=\"tutti\"$checked> ".mex("In tutti",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($periodi_permessi == "sel") $checked = " checked";
else $checked = "";
if (!$num_periodi_permessi_sel) $num_periodi_permessi_sel = 1;
if ($aggiungi_periodo_permesso) $num_periodi_permessi_sel++;
if ($elimina_periodo_permesso) $num_periodi_permessi_sel--;
echo "<label><input type=\"radio\" id=\"pp_s\" name=\"periodi_permessi\" value=\"sel\"$checked> ".mex("Solo nei periodi selezionati",$pag).":</label></td>
<td onclick=\"document.getElementById('pp_s').checked='1'\">";
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
if ($num1 > 1) echo "<tr><td></td><td></td><td>";
echo mex("dal",$pag)." ";
$pp_dal = ${"pp_dal".$num1};
$pp_al = ${"pp_al".$num1};
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","pp_dal$num1",$pp_dal,"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","pp_al$num1",$pp_al,"","",$id_utente,$tema);
if ($num1 == $num_periodi_permessi_sel) {
$id_minus = " id=\"minus_pp".($num1 + 1)."\"";
if ($num1 > 1) echo "</td><td id=\"minus_pp$num1\"><input class=\"sbutton\" type=\"submit\" name=\"elimina_periodo_permesso\" value=\"".mex("Elimina un periodo",$pag)."\" onclick=\"elim_lin_per_perm();\"></td></tr><tr><td></td><td></td><td>";
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
cella = nlinea.insertCell(2);
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
cella = nlinea.insertCell(3);
cella.id = 'minus_pp'+numcaselle;
cella.innerHTML = '<input class=\"sbutton\" type=\"submit\" name=\"elimina_periodo_permesso\" value=\"".str_replace("'","\\'",mex("Elimina un periodo",$pag))."\" onclick=\"elim_lin_per_perm();\">';
minus_prec.innerHTML = '';
nlinea = tab_per_perm.insertRow(-1);
cella = nlinea.insertCell(0);
cella = nlinea.insertCell(1);
cella = nlinea.insertCell(2);
cella = nlinea.insertCell(3);
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
cella = nlinea.insertCell(3);
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
if (!$tipo_pp) $tipo_pp = "t";
if ($tipo_pp == "t") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"tipo_pp\" value=\"t\"$checked> ".mex("se tutt$lettera_e $parola_le $parola_settimane della prenotazione sono all'interno dei periodi selezionati",$pag)."</label></td></tr>
<tr><td></td><td>";
if ($tipo_pp == "u") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"tipo_pp\" value=\"u\"$checked> ".mex("se anche un$lettera_a2 sol$lettera_a $parola_settimana della prenotazione è all'interno dei periodi selezionati",$pag)."</label></td></tr>";
if ($tipo_ca == "s") {
echo "<tr><td></td><td>";
if ($tipo_pp == "p") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"tipo_pp\" value=\"p\"$checked> ".mex("applicare il costo solo in $parola_settimane permess$lettera_e all'interno della prenotazione",$pag)."</label></td></tr>";
} # fine if ($tipo_ca == "s")
echo "</table><br>";
} # fine if ($passo == 7)

if ($passo == 8) {
echo mex("Appartamenti incompatibili con il costo",'unit.php').":<br>
<table><tr><td style=\"height: 2px;\"></td></tr><tr><td style=\"width: 10px;\"></td><td>";
$num_col = 1;
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$idappartamenti = risul_query($appartamenti,$num1,'idappartamenti');
if ($priv_vedi_tab_appartamenti == "s" or $appartamenti_consentiti[$idappartamenti] != "NO") {
if (${"app_incomp_".$num1} == "i") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"app_incomp_$num1\" value=\"i\"$checked>$idappartamenti</label>
<input type=\"hidden\" name=\"idapp_incomp_$num1\" value=\"$idappartamenti\">";
if ($num_col == 4) {
echo "</td></tr><tr><td></td><td>";
$num_col = 0;
} # fine if ($num_col == 4)
else echo "<td style=\"width: 30px;\"></td><td>";
$num_col++;
} # fine if ($priv_vedi_tab_appartamenti == "s" or $appartamenti_consentiti[$idappartamenti] != "NO")
} # fine for $num1
echo "</td></tr></table><br>";
} # fine if ($passo == 8)

if ($passo == 9) {
if ($lista_tariffe_p9) {
echo mex("Tariffe incompatibili con il costo",$pag).":<br>
<table><tr><td style=\"height: 2px;\"></td></tr><tr><td style=\"width: 10px;\"></td><td>
$lista_tariffe_p9
</td></tr></table><br>";
} # fine if ($lista_tariffe_p9)
} # fine if ($passo == 9)

if ($passo == 10) {
if ($associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp) {
echo mex("Mostrare nella pagina di inserimento delle prenotazioni",$pag)."?
 <select name=\"mostra_ca\">";
if ($mostra_ca == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>";
if ($mostra_ca == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>
</select><br>";
} # fine if ($associa_tariffe == "n" and...
else echo "<input type=\"hidden\" name=\"mostra_ca\" value=\"n\">";
echo mex("Mostrare raggruppato con costi simili della stessa categoria in inserimento",$pag)."?
 <select name=\"raggruppa_ca\">";
if (!$raggruppa_ca) $raggruppa_ca = "n";
if ($raggruppa_ca == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>";
if ($raggruppa_ca == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>
</select><br>";
if ($associa_tariffe == "n" and !$assegna_da_giorno_ini and !$assegna_prenota_contemp and strcmp(trim($categoria_ca),"") and $app_incompatibili != "SI" and ($beni_inv_elimina == "nessuno" or $tipo_bie != "a")) {
if ($combina_ca == "s") $checked = " checked";
else $checked = "";
echo "<table cellspacing=0 cellpadding=0><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"combina_ca\" value=\"s\"$checked>
".mex("Combina con altri costi",$pag)."</label>
 <small>(".mex("i costi combinabili vengono mostrati tutti assieme con il nome della categoria ed ognuno è inserito se possibile",$pag).")</small>
</td></tr></table>";
} # fine if ($associa_tariffe == "n" and...
echo "<br>";
if ($moltiplica_ca != "p" and $moltiplica_ca != "t" and $tipo_moltmax != "p" and $tipo_moltmax != "t" and (!$valore_p_ca or substr($tipo_percentuale,0,7) == "tariffa")) {
echo mex("Considerare il costo come letto/i aggiuntivo/i",$pag)."?
 <select name=\"letto_ca\">";
if ($letto_ca == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>";
if ($letto_ca == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>
</select><br><br>";
} # fine if ($moltiplica_ca != "p" and...
else echo "<input type=\"hidden\" name=\"letto_ca\" value=\"n\">";
echo mex("Escludere questo costo dal totale per altri costi percentuali",$pag)."?
 <select name=\"escludi_da_tot\">";
if (!$escludi_da_tot) {
if ($tipo_tasse == "t" or ($valore_p_ca and ($tipo_percentuale == "totale" or $tipo_percentuale == "resto"))) $escludi_da_tot = "s";
else $escludi_da_tot = "n";
}  # fine if (!$escludi_da_tot)
if ($escludi_da_tot == "n") $selected = " selected";
else $selected = "";
echo "<option value=\"n\"$selected>".mex("NO",$pag)."</option>";
if ($escludi_da_tot == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("SI",$pag)."</option>
</select><br><br>";
if ($tipo_ca != "s" or $associasett == "s" or ($numsett_ca == "t" and ($periodi_permessi == "tutti" or $tipo_pp != "pp"))) {
echo "<table><tr><td valign=\"top\">".mex("Limitarne il numero che è possibile avere contemporaneamente in uno stesso periodo",$pag)."?</td>
<td style=\"width: 130px;\">";
if (!$limite_ca) $limite_ca = "n";
if (!$numlimite_ca) $numlimite_ca = "1";
if ($limite_ca == "n") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" name=\"limite_ca\" value=\"n\"$checked> ".mex("No",$pag)."</label><br>";
if ($limite_ca == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"radio\" id=\"li_s\" name=\"limite_ca\" value=\"s\"$checked> ".mex("Si",$pag).":</label>
<input type=\"text\" name=\"numlimite_ca\" value=\"$numlimite_ca\" size=\"4\" onclick=\"document.getElementById('li_s').checked='1';\">
</td></tr></table><br>";
} # fine if ($tipo_ca != "s" or...
} # fine if ($passo == 10)

if ($passo == 11) {
echo mex("Caratteristiche del costo da mantenere quando si modifica una prenotazione",$pag).":
<table>";
if ($valore_p_ca) {
if (!$mantenere_percentuale) $mantenere_percentuale = "s";
if ($mantenere_percentuale == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label>
<input type=\"checkbox\" name=\"mantenere_percentuale\" value=\"s\"$checked>".mex("Valore percentuale",$pag);
if ($tipo_ca == "s" and $associasett == "s") echo " ".mex("e settimane associate",$pag);
echo "</label>";
if (($tipo_ca == "s" and $numsett_ca != "c") or ($moltiplica_ca != "c" and $moltiplica_ca != "1")) echo ", ".mex("se selezionato mantenere anche",$pag).":";
echo "</td></tr>";
} # fine if ($valore_p_ca)
if ($tipo_ca == "s" and $numsett_ca != "c") {
if (!$mantenere_numsett) $mantenere_numsett = "s";
if ($mantenere_numsett == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td>";
if ($valore_p_ca) echo "<td style=\"width: 50px;\"></td>";
echo "<td><label><input type=\"checkbox\" name=\"mantenere_numsett\" value=\"s\"$checked>
".mex("Assegnazione $parola_settimane",$pag)."</label></td></tr>";
} # fine if ($tipo_ca == "s" and $numsett_ca != "c")
if ($moltiplica_ca != "c" or $tipo_moltmax != "n" or $moltmax) {
if (!$mantenere_moltiplica) $mantenere_moltiplica = "s";
if ($mantenere_moltiplica == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\">";
if ($valore_p_ca and $moltiplica_ca != "1") echo "<td style=\"width: 50px;\"></td>";
echo "</td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_moltiplica\" value=\"s\"$checked>
".mex("Numero per cui viene moltiplicato",$pag)."</label></td></tr>";
} # fine if ($moltiplica_ca != "c" or $tipo_moltmax != "n" or $moltmax)
if ($beni_inv_elimina != "nessuno") {
if (!$mantenere_beniinv) $mantenere_beniinv = "s";
if ($mantenere_beniinv == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_beniinv\" value=\"s\"$checked>
".mex("Beni dell'inventario da eliminare",$pag)."</label></td></tr>";
} # fine if ($beni_inv_elimina != "nessuno")
if ($periodi_permessi != "tutti") {
if (!$mantenere_periodip) $mantenere_periodip = "s";
if ($mantenere_periodip == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_periodip\" value=\"s\"$checked>
".mex("Periodi permessi",$pag)."</label></td></tr>";
} # fine if ($periodi_permessi != "tutti")
if ($associa_tariffe != "n") {
if (!$mantenere_tariffea) $mantenere_tariffea = "s";
if ($mantenere_tariffea == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_tariffea\" value=\"s\"$checked>
".mex("Associazione alle tariffe",$pag)."</td></tr>";
} # fine if ($associa_tariffe != "n")
if ($tariffe_incompatibili == "SI") {
if (!$mantenere_tariffei) $mantenere_tariffei = "s";
if ($mantenere_tariffei == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_tariffei\" value=\"s\"$checked>
".mex("Tariffe incompatibili",$pag)."</label></td></tr>";
} # fine if ($tariffe_incompatibili == "SI")
if ($app_incompatibili == "SI") {
if (!$mantenere_appi) $mantenere_appi = "s";
if ($mantenere_appi == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_appi\" value=\"s\"$checked>
".mex("Appartamenti incompatibili",'unit.php')."</label></td></tr>";
} # fine if ($app_incompatibili == "SI")
if ($combina_ca == "s") {
if (!$mantenere_comb) $mantenere_comb = "s";
if ($mantenere_comb == "s") $checked = " checked";
else $checked = "";
echo "<tr><td style=\"width: 30px;\"></td><td colspan=2><label><input type=\"checkbox\" name=\"mantenere_comb\" value=\"s\"$checked>
".mex("Costi combinati",$pag)."</label></td></tr>";
} # fine if ($combina_ca == "s")
echo "</table><br>";
if ($id_utente == 1) {
echo "<br>";
if (numlin_query($utenti_attiva_costi_sel)) {
if (!$agg_utenti and $associa_tariffe != "n") $agg_utenti = "s";
if ($agg_utenti == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"agg_utenti\" value=\"s\"$checked>
".mex("Seleziona adesso questo costo anche per tutti gli utenti che non hanno privilegi per usare tutti i costi",$pag).".</label><br>
<div style=\"height: 4px\"></div>";
} # fine if (numlin_query($utenti_attiva_costi_sel))
if (!$agg_modelli and $associa_tariffe != "n") $agg_modelli = "s";
if ($agg_modelli == "s") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"agg_modelli\" value=\"s\"$checked>
".mex("Aggiungi adesso questo costo a tutte le pagine create per il sito web",$pag).".</label><br><br>";
} # fine if ($id_utente == 1)
} # fine if ($passo == 11)

if ($passo == 12) {
$aggiorna_tariffe_interconn = "SI";
$idntariffe = esegui_query("select numlimite_ca from $tablenometariffe where idntariffe = 1");
$idntariffe = risul_query($idntariffe,0,0);
if ($valore_p_ca) $tipo_valore_ca = "p";
else $tipo_valore_ca = "f";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "tariffafissa") $tipo_valore_ca = "q";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "tariffapers") $tipo_valore_ca = "s";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "totale") $tipo_valore_ca = "t";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "caparra") $tipo_valore_ca = "c";
if ($tipo_valore_ca == "p" and $tipo_percentuale == "resto") $tipo_valore_ca = "r";
$valore_ca = $valore_f_ca;
if (!strcmp($valore_ca,"")) $valore_ca = 0;
$valore_ca = formatta_soldi($valore_ca);
$nomecostoagg = aggslashdb($nomecostoagg);
if ($moltiplica_ca != "c" or ($tipo_moltmax == "n" and !$moltmax)) {
$tipo_moltmax = "x";
$moltmax = "";
} # fine if ($moltiplica_ca != "c" or ($tipo_moltmax == "n" and !$moltmax))
else {
if ($tipo_moltmax == "p") $moltmax = $meno_moltmax_p;
if ($tipo_moltmax == "t") $moltmax = $meno_moltmax_t;
} # fine if ($moltiplica_ca != "c" or ($tipo_moltmax == "n" and !$moltmax))
$moltiplica_ca .= $tipo_moltmax.$agg_moltiplica.",".$moltmax;
$variazione_ca = $mantenere_percentuale.$mantenere_numsett.$mantenere_moltiplica.$mantenere_periodip.$mantenere_tariffea.$mantenere_tariffei.$mantenere_beniinv.$mantenere_appi.$mantenere_comb;
$mostra_ca = $mostra_ca.$raggruppa_ca.$combina_ca.$escludi_da_tot;
esegui_query("insert into $tablenometariffe (idntariffe,nomecostoagg,tipo_ca,valore_ca,moltiplica_ca,variazione_ca,mostra_ca,letto_ca,utente_inserimento) values ('$idntariffe','$nomecostoagg','$tipo_ca$tipo_valore_ca','$valore_ca','$moltiplica_ca','$variazione_ca','$mostra_ca','$letto_ca','$id_utente')");
if ($valore_p_ca) {
$valore_perc_ca = formatta_soldi($valore_p_ca);
esegui_query("update $tablenometariffe set arrotonda_ca = '$arrotonda_ca' where idntariffe = '$idntariffe'");
} # fine if ($valore_p_ca)
if ($tipo_valore_ca != "f") {
$valore_perc_ca = formatta_soldi($valore_p_ca);
$arrotonda_ca = formatta_soldi($arrotonda_ca);
esegui_query("update $tablenometariffe set valore_perc_ca = '$valore_perc_ca', arrotonda_ca = '$arrotonda_ca' where idntariffe = '$idntariffe'");
} # fine if ($tipo_valore_ca != "f")
if ($tasseperc_ca or $tipo_tasse == "t") {
if ($tipo_tasse == "t") $tasseperc_ca = -1;
esegui_query("update $tablenometariffe set tasseperc_ca = '$tasseperc_ca' where idntariffe = '$idntariffe'");
} # fine if ($tasseperc_ca or $tipo_tasse == "t")
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
unset($valtariffa);
if ($associa_tariffe != "n") {
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
$valtariffa = $associa_tariffe.$valtariffa;
} # fine if (${"sel".$tariffa} == "s")
} # fine if ($associa_tariffe != "n")
if (${"incomp_".$tariffa} == "i") $valtariffa = "i";
if ($valtariffa) esegui_query("update $tablenometariffe set $tariffa = '$valtariffa' where idntariffe = '$idntariffe'");
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
$regoleassegna_ca = "";
if ($assegna_da_giorno_ini) {
if ($ass_gio_ini_segno == "piu") $regoleassegna_ca .= ">".$ass_gio_ini_num;
else $regoleassegna_ca .= "<".$ass_gio_ini_num;
} # fine if ($assegna_da_giorno_ini)
$regoleassegna_ca .= ";";
if ($assegna_prenota_contemp) $regoleassegna_ca .= $ass_pren_cont_num;
esegui_query("update $tablenometariffe set regoleassegna_ca = '$regoleassegna_ca' where idntariffe = '$idntariffe'");
if ($tipo_ca == "s") {
esegui_query("update $tablenometariffe set associasett_ca = '$associasett' where idntariffe = '$idntariffe'");
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
esegui_query("update $tablenometariffe set numsett_ca = '$valnumsett_ca' where idntariffe = '$idntariffe'");
} # fine if ($tipo_ca == "s")
if ($beni_inv_elimina == "sel") {
if ($tipo_bie == "m") $beniinv_ca = "mag$mag_bie_sel";
else $beniinv_ca = "app";
for ($num1 = 1 ; $num1 <= $num_beni_inv_elimina_sel ; $num1++) $beniinv_ca .= ";".${"bene_inv_sel".$num1}.",".${"molt_bene_inv_sel".$num1};
esegui_query("update $tablenometariffe set beniinv_ca = '$beniinv_ca' where idntariffe = '$idntariffe'");
} # fine if ($beni_inv_elimina == "sel")
if ($periodi_permessi == "sel") {
$periodipermessi_ca = "";
for ($num1 = 1 ; $num1 <= $num_periodi_permessi_sel ; $num1++) {
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '".${"pp_dal".$num1}."' ");
$idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi');
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '".${"pp_al".$num1}."' ");
$idfineperiodo = risul_query($idfineperiodo,0,'idperiodi');
$periodipermessi_ca .= ",".$idinizioperiodo."-".$idfineperiodo;
} # fine for $num1
$periodipermessi_ca = $tipo_pp.substr($periodipermessi_ca,1);
esegui_query("update $tablenometariffe set periodipermessi_ca = '$periodipermessi_ca' where idntariffe = '$idntariffe'");
} # fine if ($periodi_permessi == "sel")
$appincompatibili_ca = "";
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
if (${"app_incomp_".$num1} == "i") {
$appincompatibili_ca .= ${"idapp_incomp_".$num1}.",";
} # fine (${"app_incomp_".$num1} == "i")
} # fine for $num1
$appincompatibili_ca = substr($appincompatibili_ca,0,-1);
if (strcmp($appincompatibili_ca,"")) esegui_query("update $tablenometariffe set appincompatibili_ca = '$appincompatibili_ca' where idntariffe = '$idntariffe'");
if (strcmp($categoria_ca,"")) esegui_query("update $tablenometariffe set categoria_ca = '".aggslashdb($categoria_ca)."' where idntariffe = '$idntariffe'");
if ($limite_ca == "s") esegui_query("update $tablenometariffe set numlimite_ca = '$numlimite_ca' where idntariffe = '$idntariffe'");
if ($agg_utenti == "s") $q_utenti_costi_sel = $utenti_attiva_costi_sel;
else $q_utenti_costi_sel = "";
aggiorna_privilegi_ins_costo($idntariffe,$tableprivilegi,$id_utente,$anno,$attiva_costi_agg_consentiti,$priv_ins_costi_agg,$utenti_gruppi,$q_utenti_costi_sel);
$idntariffe++;
esegui_query("update $tablenometariffe set numlimite_ca = '$idntariffe' where idntariffe = 1");
if ($origine) $action = $origine;
else $action = $pag;
echo "</div></form>
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_costi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idntariffe\" value=\"".($idntariffe - 1)."\">
<input type=\"hidden\" name=\"origine\" value=\"$pag#ins_costi_agg\">
".mex("Il costo aggiuntivo",$pag)." \"".stripslashes($nomecostoagg)."\" ".mex("è stato inserito",$pag).".
 <button class=\"exco\" type=\"submit\"><div>".mex("Modifica il costo",$pag)."</div></button>
</div></form><br>";
if ($agg_modelli == "s") {
function aggiorna_var_modello () {
global $idntariffe,$var_mod,$num_var_mod,$crea_modello;
$crea_modello = 0;
for ($num1 = 0 ; $num1 < $num_var_mod ; $num1++) {
if ($var_mod[$num1] == 'costi_aggiuntivi_mostra') {
$crea_modello = 1;
$idnt = ($idntariffe - 1);
$attiva_costo = "attiva_costo".$idnt;
$nome_costo_imposto = "nome_costo_imposto".$idnt;
$nome_cat_imp = "nome_cat_imp".$idnt;
global $$attiva_costo,$$nome_costo_imposto,$$nome_cat_imp;
$$attiva_costo = "SI";
$$nome_costo_imposto = "";
$$nome_cat_imp = "";
break;
} # fine if ($var_mod[$num1] == 'costi_aggiuntivi_mostra')
} # fine for $num1
} # fine function aggiorna_var_modello
include('./includes/templates/aggiorna_modelli.php');
} # fine if ($agg_modelli == "s")
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$action\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div>".mex("OK",$pag)."</div></button><br>
</div></form>";
} # fine if ($passo == 12)

} # fine if ($errore != "SI")
else {
if ($messaggio_errore) echo $messaggio_errore;
else echo mex("Errore nei valori inseriti",$pag).".<br>";
} # fine else if ($errore != "SI")

if ($passo != 12 or $errore == "SI") {
echo "<input type=\"hidden\" name=\"passo\" value=\"$passo\">
<button class=\"prev\" type=\"submit\" name=\"indietro\" value=\"1\"><div>".mex("indietro",$pag)."</div></button>";
if ($errore != "SI") echo "<button class=\"next\" type=\"submit\" name=\"avanti\" value=\"1\"><div>".mex("avanti",$pag)."</div></button>";
echo "</div></form><br>";
} # fine if ($passo != 12 or $errore == "SI")

unlock_tabelle($tabelle_lock);
} # fine if ($passo > 1)
} # fine if ($inseriscicosti and $priv_ins_costi_agg != "n")



if ($inserisci_settimanalmente and $priv_mod_tariffe != "n") {
$mostra_form_iniziale = "NO";
$inserisci = "";
$modifica = "";
$num_tariffa = substr($tipotariffa,7);
if (substr($tipotariffa,0,7) != "tariffa" or controlla_num_pos($num_tariffa) != "SI") $tipotariffa = "tariffa1";
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$num_tariffa] != "SI") $inserire_prezzi = "NO";
$tabelle_lock = array($tablenometariffe,$tableperiodi,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock);

$inizioperiodo = aggslashdb($inizioperiodosett1);
$fineperiodo = aggslashdb($fineperiodosett1);
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$inizioperiodo' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo == 0) { $idinizioperiodo = 10000; }
else { $idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi'); }
$inizioperiodo = $idinizioperiodo;
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '$fineperiodo' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo == 0) { $idfineperiodo = -1; }
else { $idfineperiodo = risul_query($idfineperiodo,0,'idperiodi'); }
$fineperiodo = $idfineperiodo ;
if ($idfineperiodo < $idinizioperiodo) {
$inserire_prezzi = "NO";
echo mex("Le date sono sbagliate",$pag).". <br>";
} # fine if ($idfineperiodo < $idinizioperiodo)

if ($tipo_prezzo == "sett") {
$prezzosett = formatta_soldi($prezzosett);
$prezzosett_int = floor($prezzosett);
$resto_int = $prezzosett - (double) $prezzosett_int;
$prezzo_gio = floor($prezzosett_int / 7);
for ($num1 = 1 ; $num1 <= 7 ; $num1++) ${"prezzoperiodo".$num1} = $prezzo_gio;
$resto = $prezzosett_int - ($prezzo_gio * 7);
if ($resto >= 1) {
$prezzoperiodo1++;
$resto--;
} # fine if ($resto >= 1)
for ($num1 = 7 ; $num1 > (7 - $resto) ; $num1--) ${"prezzoperiodo".$num1}++;
$prezzoperiodo1 += $resto_int;
$prezzosettp = formatta_soldi($prezzosettp);
$prezzosettp_int = floor($prezzosettp);
$restop_int = $prezzosettp - (double) $prezzosettp_int;
$prezzop_gio = floor($prezzosettp_int / 7);
for ($num1 = 1 ; $num1 <= 7 ; $num1++) ${"prezzoperiodo".$num1."p"} = $prezzop_gio;
$restop = $prezzosettp_int - ($prezzop_gio * 7);
if ($restop >= 1) {
$prezzoperiodo1p++;
$restop--;
} # fine if ($restop >= 1)
for ($num1 = 7 ; $num1 > (7 - $restop) ; $num1--) ${"prezzoperiodo".$num1."p"}++;
$prezzoperiodo1p += $restop_int;
$checked_sett = "checked=\"checked\"";
$checked_gio = "";
} # fine if ($tipo_prezzo == "sett")
else {
$checked_sett = "";
$checked_gio = "checked=\"checked\"";
} # fine else if ($tipo_prezzo == "sett")

for ($num1 = 1 ; $num1 <= 7 ; $num1++) {
${"prezzoperiodo".$num1} = formatta_soldi(${"prezzoperiodo".$num1});
if (controlla_soldi(${"prezzoperiodo".$num1}) == "NO") $soldi_sbagliati = "SI";
${"prezzoperiodo".$num1."p"} = formatta_soldi(${"prezzoperiodo".$num1."p"});
if (controlla_soldi(${"prezzoperiodo".$num1."p"}) == "NO") $soldi_sbagliati = "SI";
} # fine for $num1
if ($soldi_sbagliati == "SI") {
$inserire_prezzi = "NO";
echo mex("Il formato dei soldi è sbagliato",$pag).". <br>";
} # fine if ($soldi_sbagliati == "SI")

if ($inserire_prezzi != "NO") {
$aggiorna_tariffe_interconn = "SI";
$dati_tariffe = dati_tariffe($tablenometariffe);

function periodo_modificabile ($tipotariffa,$idperiodo,&$per_imp,$dati_tariffe) {
$ins_periodo = 1;
if ($dati_tariffe[$tipotariffa]['imp_prezzi_int']) {
if ($dati_tariffe[$tipotariffa]['importa_prezzi'][0]) $ins_periodo = 0;
for ($num1 = 1 ; $num1 < $dati_tariffe[$tipotariffa]['num_per_importa'] ; $num1++) {
if ($dati_tariffe[$tipotariffa]['periodo_importa_f'][$num1] >= $idperiodo and $dati_tariffe[$tipotariffa]['periodo_importa_i'][$num1] <= $idperiodo) {
$ins_periodo = 0;
break;
} # fine if ($dati_tariffe[$tipotariffa]['periodo_importa_f'][$num1] >= $idinizioperiodo and...
} # fine for $num1
} # fine if ($dati_tariffe[$tipotariffa]['imp_prezzi_int'])
if (!$ins_periodo) $per_imp = 1;
return $ins_periodo;
} # fine function periodo_modificabile

$agg_vett_tar_esp = array();
unset($num_agg_tar_esp);
$per_imp = 0;
for ($num1 = $inizioperiodo ; $num1 < $fineperiodo ; $num1++) {
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo1) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo1' where idperiodi = '$num1' ");
if ($prezzoperiodo1p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo1p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo1,$prezzoperiodo1p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
$num1++;
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo2) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo2' where idperiodi = '$num1' ");
if ($prezzoperiodo2p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo2p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo2,$prezzoperiodo2p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
$num1++;
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo3) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo3' where idperiodi = '$num1' ");
if ($prezzoperiodo3p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo3p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo3,$prezzoperiodo3p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
$num1++;
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo4) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo4' where idperiodi = '$num1' ");
if ($prezzoperiodo4p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo4p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo4,$prezzoperiodo4p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
$num1++;
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo5) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo5' where idperiodi = '$num1' ");
if ($prezzoperiodo5p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo5p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo5,$prezzoperiodo5p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
$num1++;
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo6) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo6' where idperiodi = '$num1' ");
if ($prezzoperiodo6p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo6p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo6,$prezzoperiodo6p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
$num1++;
if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe)) {
if ($prezzoperiodo7) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo7' where idperiodi = '$num1' ");
if ($prezzoperiodo7p) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodo7p' where idperiodi = '$num1' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$num1,$prezzoperiodo7,$prezzoperiodo7p,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if (periodo_modificabile ($tipotariffa,$num1,$per_imp,$dati_tariffe))
} # fine for $num1
$ultime_sel_ins_prezzi_s = aggslashdb("$anno,$inizioperiodosett1,$fineperiodosett1,$checked_sett,$checked_gio");
esegui_query("delete from $tablepersonalizza where idpersonalizza = 'ultime_sel_ins_prezzi_s' and idutente = '$id_utente' ");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('ultime_sel_ins_prezzi_s','$id_utente','$ultime_sel_ins_prezzi_s') ");
$opztariffa = esegui_query("select * from $tableperiodi where $tipotariffa"."p is not NULL and $tipotariffa"."p != '' and $tipotariffa"."p != '0' ");
if (numlin_query($opztariffa)) $opztariffa = "p";
else $opztariffa = "s";
esegui_query("update $tablenometariffe set $tipotariffa = '$opztariffa' where idntariffe = '4' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,"opztariffa","",$tablenometariffe,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
$inizioperiodosett1_f = formatta_data($inizioperiodosett1,$stile_data);
$fineperiodosett1_f = formatta_data($fineperiodosett1,$stile_data);
echo mex("I prezzi per le settimane dal",$pag)." <b>$inizioperiodosett1_f</b> ".mex("al",$pag)." <b>$fineperiodosett1_f</b> ".mex("della <b>tariffa",$pag)."$num_tariffa</b> ".mex("sono stati inseriti",$pag).".";
if ($per_imp) echo " <small>(".mex("i prezzi di alcuni periodi <em>non sono stati inseriti</em> perchè importati da altre tariffe",$pag).")</small>";
echo "<br>";
} # fine if ($inserire_prezzi != "NO")
unlock_tabelle($tabelle_lock);

echo "
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tariffa_selected\" value=\"$num_tariffa\">
<button class=\"cont\" type=\"submit\"><div>OK</div></button><br>
</div></form>";
} # fine if ($inserisci_settimanalmente and $priv_mod_tariffe != "n")



if ($cambia_nome_tariffa and strcmp($nometariffa,"") and $priv_mod_tariffe != "n") {
$mostra_form_iniziale = "NO";
$mostra_ok = 1;
$inserire = "SI";
$tabelle_lock = array($tablenometariffe);
$tabelle_lock = lock_tabelle($tabelle_lock);
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
if (substr($tipotariffa,0,7) != "tariffa" or controlla_num_pos(substr($tipotariffa,7)) != "SI") $tipotariffa = "tariffa1";
$num_tariffa = substr($tipotariffa,7);
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$num_tariffa] == "SI") {
$tipotariffa_vedi = mex("tariffa",$pag).$num_tariffa;
if (get_magic_quotes_gpc()) $nometariffa = stripslashes($nometariffa);
$nometariffa = htmlspecialchars($nometariffa);
for ($num1 = 1 ; $num1 <= $numero_tariffe ; $num1++) {
$tariffa_controlla = "tariffa".$num1;
if ($tariffa_controlla != $tipotariffa) {
$nome_tariffa_controlla = risul_query($rigatariffe,0,$tariffa_controlla);
if ($nome_tariffa_controlla == $nometariffa) {
echo mex("Il soprannome",$pag)." $nometariffa ".mex("già esiste, ne devi usare un'altro",$pag).".<br>";
$inserire = "NO";
} # fine if ($nome_tariffa_controlla == $nometariffa)
} # fine if ($tariffa_controlla != $tipotariffa)
} # fine for $num1
if ($inserire == "SI") {
esegui_query("update $tablenometariffe set $tipotariffa = '".aggslashdb($nometariffa)."' where idntariffe = '1' ");
echo mex("Il soprannome della",$pag)." $tipotariffa_vedi ".mex("è stato cambiato",$pag).".<br>";
} # fine if ($inserire == "SI")
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$num_tariffa] == "SI")
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_nome_tariffa and strcmp($nometariffa,"") and $priv_mod_tariffe != "n")



# Inserisco i prezzi (della form da-a)

if (($inserisci or $modifica) and $priv_mod_tariffe != "n") {
$mostra_form_iniziale = "NO";
$mostra_ok = 1;
$tabelle_lock = array($tablenometariffe,$tableperiodi,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock);
$inserire = "SI";
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');

# Controllo se i campi inseriti sono già presenti
if (substr($tipotariffa,0,7) != "tariffa" or controlla_num_pos(substr($tipotariffa,7)) != "SI") $tipotariffa = "tariffa1";
$num_tariffa = substr($tipotariffa,7);
$tipotariffa_vedi = mex("tariffa",$pag).$num_tariffa;
if ($attiva_tariffe_consentite != "n" and $tariffe_consentite_vett[$num_tariffa] != "SI") $inserire = "NO";
if ($num_tariffa > $numero_tariffe) $inserire = "NO";
if ($numcaselle < 1 or $numcaselle > $numcaselle_max) $numcaselle = 8;
$ultime_sel_ins_prezzi = "$numcaselle,$anno";
for ($numperiodo = 1 ; $numperiodo <= $numcaselle ; $numperiodo = $numperiodo + 1) {
$prezzoperiodo = "prezzoperiodo$numperiodo";
$prezzoperiodop = "prezzoperiodo$numperiodo"."p";
$ultime_sel_ins_prezzi .= ",".${"inizioperiodo".$numperiodo}.",".${"fineperiodo".$numperiodo};
if (strcmp($$prezzoperiodo,"") or strcmp($$prezzoperiodop,"")) {
$$prezzoperiodo = formatta_soldi($$prezzoperiodo);
$$prezzoperiodop = formatta_soldi($$prezzoperiodop);
$inizioperiodo = "inizioperiodo" . $numperiodo;
$inizioperiodo = aggslashdb($$inizioperiodo);
$fineperiodo = "fineperiodo" . $numperiodo;
$fineperiodo = aggslashdb($$fineperiodo);
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$inizioperiodo' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo != 0) {
$idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi');
} # fine if ($num_idinizioperiodo != 0)
else $idinizioperiodo = 9999999;
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '$fineperiodo' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo != 0) {
$idfineperiodo = risul_query($idfineperiodo,0,'idperiodi');
} # fine if ($num_idfineperiodo != 0)
else $idfineperiodo = -9999999;
$IDinizioperiodo[$numperiodo] = $idinizioperiodo;
$IDfineperiodo[$numperiodo] = $idfineperiodo;
if ($idfineperiodo < $idinizioperiodo) {
$inserire = "NO";
echo mex("Le date del periodo numero",$pag)." $numperiodo ".mex("sono sbagliate",$pag).". <br>";
} # fine if ($idfineperiodo < $idinizioperiodo)
else {
for ( ; $idfineperiodo >= $idinizioperiodo ; $idinizioperiodo = $idinizioperiodo + 1) {
if ($inserirepp[$idinizioperiodo] == "SI") {
echo mex("Il periodo",$pag)." $numperiodo ".mex("si sovrappone ad un periodo precedente",$pag).". <br>";
$inserire = "NO";
} # fine if (inserirepp[$idinizioperiodo] == "SI")
else {
$inserirepp[$idinizioperiodo] = "SI";
$vecchioprezzoperiodo = esegui_query("select $tipotariffa from $tableperiodi where idperiodi = '$idinizioperiodo' and ($tipotariffa is not NULL or $tipotariffa"."p is not NULL)");
$esisteprezzoperiodo = numlin_query($vecchioprezzoperiodo);
if ($esisteprezzoperiodo > 0 and $inserisci) {
echo mex("Un prezzo nel periodo numero",$pag)." $numperiodo ".mex("esiste già, usa il tasto \"modifica i prezzi già inseriti\" per cambiarlo",$pag).". <br>";
$inserire = "NO";
} # fine if ($esisteprezzoperiodo > 0 and $inserisci)
} # fine else if (inserirepp[$idinizioperiodo] == "SI")
} # fine for $idinizioperiodo
if ((strcmp($$prezzoperiodo,"") and controlla_soldi($$prezzoperiodo) == "NO") or (strcmp($$prezzoperiodop,"") and controlla_soldi($$prezzoperiodop) == "NO")) {
echo mex("Il prezzo del periodo numero",$pag)." $numperiodo ".mex("è sbagliato",$pag).". <br>";
$inserire = "NO";
} # fine if ((strcmp($$prezzoperiodo,"") and controlla_soldi($$prezzoperiodo) == "NO") or...
} # fine else if ($idfineperiodo < $idinizioperiodo)
} # fine if (strcmp($$prezzoperiodo,"") or strcmp($$prezzoperiodop,""))
} # fine for $numperiodo

# Inserisco i nuovi dati
if ($inserire == "SI") {
$aggiorna_tariffe_interconn = "SI";
esegui_query("delete from $tablepersonalizza where idpersonalizza = 'ultime_sel_ins_prezzi' and idutente = '$id_utente' ");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('ultime_sel_ins_prezzi','$id_utente','$ultime_sel_ins_prezzi') ");
$dati_tariffe = dati_tariffe($tablenometariffe);
$agg_vett_tar_esp = array();
unset($num_agg_tar_esp);
for ($numperiodo = 1; $numperiodo <= $numcaselle; $numperiodo = $numperiodo + 1) {
$prezzoperiodo = "prezzoperiodo$numperiodo";
$prezzoperiodo = $$prezzoperiodo;
$prezzoperiodop = "prezzoperiodo$numperiodo"."p";
$prezzoperiodop = $$prezzoperiodop;
if (strcmp($prezzoperiodo,"") or strcmp($prezzoperiodop,"")) {
$periodi_importati = 0;
$idinizioperiodo = $IDinizioperiodo[$numperiodo];
$idfineperiodo = $IDfineperiodo[$numperiodo];
for ( ; $idfineperiodo >= $idinizioperiodo ; $idinizioperiodo = $idinizioperiodo + 1) {
$ins_periodo = 1;
if ($dati_tariffe[$tipotariffa]['imp_prezzi_int']) {
if ($dati_tariffe[$tipotariffa]['importa_prezzi'][0]) $ins_periodo = 0;
for ($num1 = 1 ; $num1 < $dati_tariffe[$tipotariffa]['num_per_importa'] ; $num1++) {
if ($dati_tariffe[$tipotariffa]['periodo_importa_f'][$num1] >= $idinizioperiodo and $dati_tariffe[$tipotariffa]['periodo_importa_i'][$num1] <= $idinizioperiodo) {
$ins_periodo = 0;
break;
} # fine if ($dati_tariffe[$tipotariffa]['periodo_importa_f'][$num1] >= $idinizioperiodo and...
} # fine for $num1
} # fine if ($dati_tariffe[$tipotariffa]['imp_prezzi_int'])
if ($ins_periodo) {
if (strcmp($prezzoperiodo,"")) esegui_query("update $tableperiodi set $tipotariffa = '$prezzoperiodo' where idperiodi = '$idinizioperiodo' ");
else esegui_query("update $tableperiodi set $tipotariffa = NULL where idperiodi = '$idinizioperiodo' ");
if (strcmp($prezzoperiodop,"")) esegui_query("update $tableperiodi set $tipotariffa"."p = '$prezzoperiodop' where idperiodi = '$idinizioperiodo' ");
else esegui_query("update $tableperiodi set $tipotariffa"."p = NULL where idperiodi = '$idinizioperiodo' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,$idinizioperiodo,$prezzoperiodo,$prezzoperiodop,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if ($ins_periodo)
else $periodi_importati = 1;
} # fine for $idinizioperiodo
echo mex("Il prezzo del periodo",$pag)." $numperiodo ".mex("è stato inserito",$pag)."!";
if ($periodi_importati) echo " <small>(".mex("i prezzi di alcuni periodi <em>non sono stati inseriti</em> perchè importati da altre tariffe",$pag).")</small>";
echo "<br>";
} # fine if (strcmp($prezzoperiodo,"") or strcmp($prezzoperiodop,""))
} # fine for $numperiodo
$opztariffa = esegui_query("select * from $tableperiodi where $tipotariffa"."p is not NULL and $tipotariffa"."p != '0' ");
if (numlin_query($opztariffa)) $opztariffa = "p";
else $opztariffa = "s";
esegui_query("update $tablenometariffe set $tipotariffa = '$opztariffa' where idntariffe = '4' ");
aggiorna_tariffe_esporta($dati_tariffe,$tipotariffa,"opztariffa","",$tablenometariffe,$tableperiodi,$agg_vett_tar_esp,$num_agg_tar_esp);
} # fine if ($inserire == "SI")
else {
echo mex("Nessun dato è stato inserito",$pag).".<br>";
} # fine else if ($inserire == "SI")
unlock_tabelle($tabelle_lock);
} # fine if (($inserisci or $modifica) and $priv_mod_tariffe != "n")



if ($mostra_ok) {
$action = $pag;
if ($ancora) $action .= "#$ancora";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$action\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div>".mex("OK",$pag)."</div></button><br>
</div></form>";
} # fine if ($mostra_ok)





if ($mostra_form_iniziale != "NO") {


echo "<h4 id=\"h_ipri\"><span>".mex("Inserisci i prezzi per l'anno",$pag)." $anno</span></h4>
<br><hr style=\"width: 95%\">";

$dati_tariffe = dati_tariffe($tablenometariffe);
if ($id_utente == 1) {
echo "<div style=\"height: 6px;\"></div><div style=\"float: left;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"aggiorna_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"origine\" value=\"./creaprezzi.php\">
<input type=\"hidden\" name=\"cambianumerotariffe\" value=\"1\">
".mex("Cambia il numero delle tariffe","personalizza.php")." ".mex("per l'anno","personalizza.php")." $anno
 ".mex("a","personalizza.php")." <input type=\"text\" name=\"nuovo_numero_tariffe\" size=\"5\" value=\"".$dati_tariffe['num']."\">
<button class=\"ipri\" type=\"submit\"><div>".mex("Cambia","personalizza.php")."</div></button>
</div></form></div><div style=\"float: left; width: 50px;\">&nbsp;</div><div style=\"float: left;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"aggiorna_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_ord_tariffe\" value=\"SI\">
<input type=\"hidden\" name=\"origine\" value=\"./creaprezzi.php\">
<button class=\"xpri\" type=\"submit\"><div>".ucfirst(mex("cambia l'ordine delle tariffe","personalizza.php"))."</div></button>
</div></form></div><div style=\"float: left; width: 50px;\">&nbsp;</div><div style=\"float: left;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<input type=\"hidden\" name=\"mostra_form_agg_per\" value=\"1\">
<input type=\"hidden\" name=\"origine\" value=\"./creaprezzi.php\">
<button class=\"amon\" type=\"submit\"><div>".mex("Aggiungi periodi","visualizza_tabelle.php")."</div></button>
</div></form></div>
<div style=\"clear: both; height: 6px;\"></div>
<hr style=\"width: 95%\">";
} # fine if ($id_utente == 1)

if ($priv_mod_tariffe != "n") {
$lista_opt_tariffe = "";
$lista_opt_tariffe_cambia = "";
$lista_opt_tariffe_cambia_tutti = "";
$lista_opt_tariffe_no_esporta = "";
$dati_r2 = "";
dati_regole2($dati_r2,$app_regola2_predef,"","","",$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
for ($num1 = 1 ; $num1 <= $dati_tariffe['num'] ; $num1++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$num1] == "SI") {
$tariffa = "tariffa".$num1;
$nometariffa_vedi = mex("tariffa",$pag).$num1;
if ($dati_tariffe[$tariffa]['nome'] != "") {
$nometariffa_vedi .= " (".$dati_tariffe[$tariffa]['nome'];
#if ($dati_tariffe[$tariffa]['moltiplica'] == "p") $nometariffa_vedi .= ", ".mex("per persona",$pag);
$nometariffa_vedi .= ")";
} # fine if ($dati_tariffe[$tariffa]['nome'] != "")
elseif ($dati_tariffe[$tariffa]['moltiplica'] == "p") $nometariffa_vedi .= " (".mex("per persona",$pag).")";
if ($dati_r2['napp'][$tariffa] > 1) $nometariffa_vedi .= " (x".$dati_r2['napp'][$tariffa].")";
$lista_opt_tariffe .= "<option value=\"$tariffa\">$nometariffa_vedi</option>";
if (!$dati_tariffe[$tariffa]['importa_prezzi'][0]) $lista_opt_tariffe_cambia .= "<option value=\"$tariffa\">$nometariffa_vedi</option>";
if (!$dati_tariffe[$tariffa]['imp_prezzi_int']) $lista_opt_tariffe_cambia_tutti .= "<option value=\"$tariffa\">$nometariffa_vedi</option>";
if (!$dati_tariffe[$tariffa]['esporta_prezzi']) $lista_opt_tariffe_no_esporta .= "<option value=\"$tariffa\">$nometariffa_vedi</option>";
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$num1] == "SI")
} # fine for $num1
if ($lista_opt_tariffe) {

echo "<table><tr><td style=\"height: 5px;\"></td></tr></table>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"cambia_nome_tariffa\" value=\"1\">
<script type=\"text/javascript\">
<!--
function aggiorna_nome_tariffa () {
var sel_tar = document.getElementById('tariffa_cambia_nome');
var num_sel = sel_tar.selectedIndex;
var tariffa = sel_tar.options[num_sel].innerHTML;
tariffa = tariffa.substr(sel_tar.options[num_sel].value.length - 5 + ".strlen(mex("tariffa",$pag)).");
tariffa = tariffa.substr(0,(tariffa.length - 1));
var brks = tariffa.indexOf(') (');
if (brks > 0) tariffa = tariffa.substr(0,brks);
if (!num_sel) tariffa = '';
document.getElementById('nometariffa').value = tariffa;
} // fine function aggiorna_nome_tariffa
-->
</script>
".mex("Nome della",$pag)." <select name=\"tipotariffa\" id=\"tariffa_cambia_nome\" onchange=\"aggiorna_nome_tariffa()\">
<option value=\"\">----</option>
$lista_opt_tariffe</select>: <input type=\"text\" id=\"nometariffa\" name=\"nometariffa\" size=\"30\">
<button class=\"edit\" type=\"submit\"><div>".mex("Cambia",$pag)."</div></button>
</div></form><table><tr><td style=\"height: 5px;\"></td></tr></table>
<hr style=\"width: 95%\">";


# Questa è la form con da-a (inserzione per giorni)

$ultime_sel_ins_prezzi = "";
if (!$numcaselle) {
$ultime_sel_ins_prezzi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'ultime_sel_ins_prezzi' and idutente = '$id_utente'");
if (numlin_query($ultime_sel_ins_prezzi) == 1) {
$ultime_sel_ins_prezzi = explode(",",risul_query($ultime_sel_ins_prezzi,0,'valpersonalizza'));
$numcaselle = $ultime_sel_ins_prezzi[0];
} # fine if (numlin_query($ultime_sel_ins_prezzi) == 1)
else $numcaselle = 8;
} # fine if (!$numcaselle)
else {
if ($elimina_casella) $numcaselle--;
if ($aggiungi_casella) $numcaselle++;
} # fine else if (!$numcaselle)
if ($numcaselle < 1 or $numcaselle > $numcaselle_max) $numcaselle = 8;

echo "<div style=\"text-align: center;\">
<h5>".mex("Inserzione per $parola_settimane",$pag)."</h5><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"numcaselle\" id=\"numcaselle\" value=\"$numcaselle\">";

echo "<div>".mex("Prezzi della",$pag)." 
<select name=\"tipotariffa\">
$lista_opt_tariffe_cambia
</select><br>
<table class=\"linhbox\" id=\"tab_prezzi\" style=\"margin-left: auto; margin-right: auto;\"><tr style=\"line-height: 1; vertical-align: bottom;\"><td></td>
<td style=\"width: 10px; font-size: smaller;\">".mex("prezzo per $parola_settimana",$pag)."</td><td></td>
<td style=\"width: 10px; font-size: smaller;\">".mex("prezzo per persona per $parola_settimana",$pag)."</td></tr>";

$arrotond_predef = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'arrotond_predef' and idutente = '$id_utente'");
$arrotond_predef = risul_query($arrotond_predef,0,'valpersonalizza');

$date_selected = "";
$p_pers = mex("p",$pag);
for ($numperiodo = 1 ; $numperiodo <= $numcaselle ; $numperiodo = $numperiodo + 1) {
$inizioperiodo = "inizioperiodo".$numperiodo;
$fineperiodo = "fineperiodo".$numperiodo;
$prezzoperiodo = "prezzoperiodo".$numperiodo;
echo "<tr><td>$numperiodo. ".mex("Dal",$pag)." ";
if ($ultime_sel_ins_prezzi[1] == $anno) $date_selected = $ultime_sel_ins_prezzi[($numperiodo * 2)];
else $date_selected = $$inizioperiodo;
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php",$inizioperiodo,$date_selected,"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
if ($ultime_sel_ins_prezzi[1] == $anno) $date_selected = $ultime_sel_ins_prezzi[(($numperiodo * 2) + 1)];
else $date_selected = $$fineperiodo;
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php",$fineperiodo,$date_selected,"","",$id_utente,$tema);
echo " : </td><td><input type=\"text\" name=\"$prezzoperiodo\" value=\"".$$prezzoperiodo."\" size=\"12\"></td>
<td> + </td><td style=\"white-space: nowrap;\"><input type=\"text\" name=\"$prezzoperiodo"."p\" value=\"".${$prezzoperiodo."p"}."\" size=\"10\">*$p_pers </td><td>$Euro</td>
<td id=\"minus$numperiodo\">";
if ($numperiodo == $numcaselle and $numcaselle > 1) echo "<input class=\"sbutton\" type=\"submit\" name=\"elimina_casella\" onclick=\"return elim_lin_tar();\" value=\"-\">";
echo "</td><td id=\"plus$numperiodo\">";
if ($numperiodo == $numcaselle and $numcaselle < $numcaselle_max) echo "<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_casella\" onclick=\"return agg_lin_tar();\" value=\"+\">";
echo "</td></tr>";
} # fine for $numperiodo

echo "</table></div><table><tr><td style=\"height: 2px;\"></td></tr></table>
<script type=\"text/javascript\">
<!--
var numcaselle = $numcaselle;
function agg_lin_tar () {
if (numcaselle < $numcaselle_max) {
var tab_prezzi = document.getElementById('tab_prezzi');
var minus_prec = document.getElementById('minus'+numcaselle);
var plus_prec = document.getElementById('plus'+numcaselle);
numcaselle++;
var nlinea = tab_prezzi.insertRow(-1);
var cella = nlinea.insertCell(0);
cell_html = numcaselle+'. ".mex("Dal",$pag)." ';
var inizioperiodo = 'inizioperiodo'+numcaselle;
";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","inizioperiodo","","","",$id_utente,$tema,"","","cell_html");
echo "
cell_html += ' ".mex("al",$pag)." ';
var fineperiodo = 'fineperiodo'+numcaselle;
";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","fineperiodo","","","",$id_utente,$tema,"","","cell_html");
echo "
cell_html += ' : ';
cella.innerHTML = cell_html;
cella = nlinea.insertCell(1);
cella.innerHTML = '<input type=\"text\" name=\"prezzoperiodo'+numcaselle+'\" value=\"\" size=\"12\">';
cella = nlinea.insertCell(2);
cella.innerHTML = ' + ';
cella = nlinea.insertCell(3);
cella.style.whiteSpace = 'nowrap';
cella.innerHTML = '<input type=\"text\" name=\"prezzoperiodo'+numcaselle+'p\" value=\"\" size=\"10\">*$p_pers ';
cella = nlinea.insertCell(4);
cella.innerHTML = '$Euro';
cella = nlinea.insertCell(5);
cella.id = 'minus'+numcaselle;
cella.innerHTML = minus_prec.innerHTML;
minus_prec.innerHTML = '';
cella = nlinea.insertCell(6);
cella.id = 'plus'+numcaselle;
cella.innerHTML = plus_prec.innerHTML;
plus_prec.innerHTML = '';
document.getElementById('numcaselle').value = numcaselle;
}
return false;
} // fine function agg_lin_tar
function elim_lin_tar () {
if (numcaselle > 1) {
var tab_prezzi = document.getElementById('tab_prezzi');
var minus_prec = document.getElementById('minus'+numcaselle);
var plus_prec = document.getElementById('plus'+numcaselle);
numcaselle--;
var cella = document.getElementById('minus'+numcaselle);
cella.innerHTML = minus_prec.innerHTML;
cella = document.getElementById('plus'+numcaselle);
cella.innerHTML = plus_prec.innerHTML;
tab_prezzi.deleteRow(-1);
document.getElementById('numcaselle').value = numcaselle;
}
return false;
} // fine function elim_lin_tar
-->
</script>
<input type=\"hidden\" name=\"modifica\" value=\"1\">
<button class=\"ipri\" type=\"submit\"><div>".mex("inserisci o modifica i prezzi",$pag)."</div></button>
</div></form><br>
<hr style=\"width: 95%\">";
/*
echo "</div><table><tr><td style=\"height: 2px;\"></td></tr></table>
<input class=\"sbutton\" type=\"submit\" name=\"inserisci\" value=\"".mex("inserisci i nuovi prezzi",$pag)."\">
<input class=\"sbutton\" type=\"submit\" name=\"modifica\" value=\"".mex("modifica i prezzi già inseriti",$pag)."\">
</div></form><br>
<hr style=\"width: 95%\">";
*/


if ($tipo_periodi == "g") {
echo "<h5>".mex("Inserzione per settimane",$pag)."</h5><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";

echo mex("Prezzi della",$pag)." 
<select name=\"tipotariffa\">";
echo str_replace("tariffa$tariffa_selected\">","tariffa$tariffa_selected\" selected>",$lista_opt_tariffe_cambia);
echo "</select><br><br> ";

$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$anno' and idutente = '$id_utente'");
if (numlin_query($giorno_vedi_ini_sett) == 1) $giorno_vedi_ini_sett = risul_query($giorno_vedi_ini_sett,0,'valpersonalizza_num');
else $giorno_vedi_ini_sett = 0;

$file_date = file(C_DATI_PATH."/selectperiodi$anno.$id_utente.php");
$num_linee_file_date = count($file_date);
for ($num1 = 0 ; $num1 < $num_linee_file_date ; $num1++) {
if (substr($file_date[$num1],0,7) == "<option") {
$mese_data = substr($file_date[$num1],21,2);
$giorno_data = substr($file_date[$num1],24,2);
$anno_data = substr($file_date[$num1],16,4);
#if ($mese_data == "Jan") $mese_data = 1;
#if ($mese_data == "Feb") $mese_data = 2;
#if ($mese_data == "Mar") $mese_data = 3;
#if ($mese_data == "Apr") $mese_data = 4;
#if ($mese_data == "May") $mese_data = 5;
#if ($mese_data == "Jun") $mese_data = 6;
#if ($mese_data == "Jul") $mese_data = 7;
#if ($mese_data == "Aug") $mese_data = 8;
#if ($mese_data == "Sep") $mese_data = 9;
#if ($mese_data == "Oct") $mese_data = 10;
#if ($mese_data == "Nov") $mese_data = 11;
#if ($mese_data == "Dec") $mese_data = 12;
if (date("w", mktime(0,0,0,$mese_data,$giorno_data,$anno_data)) == $giorno_vedi_ini_sett) {
$file_date[$num1]= str_replace("\\","",$file_date[$num1]);
$option_domeniche .= $file_date[$num1];
} # fine if (date("w", mktime(0,0,0,$mese_data,$giorno_data,$anno_data)) == 0)
} # fine if (substr($file_date[$num1],0,7) == "<option")
} # fine for $num1

$ultime_sel_ins_prezzi_s = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'ultime_sel_ins_prezzi_s' and idutente = '$id_utente'");
if (numlin_query($ultime_sel_ins_prezzi_s) == 1) $ultime_sel_ins_prezzi_s = explode(",",risul_query($ultime_sel_ins_prezzi_s,0,'valpersonalizza'));
else $ultime_sel_ins_prezzi_s = "";
if ($ultime_sel_ins_prezzi_s[0] == $anno) {
$option_domeniche1 = str_replace("\"".$ultime_sel_ins_prezzi_s[1]."\">","\"".$ultime_sel_ins_prezzi_s[1]."\" selected>",$option_domeniche);
$option_domeniche2 = str_replace("\"".$ultime_sel_ins_prezzi_s[2]."\">","\"".$ultime_sel_ins_prezzi_s[2]."\" selected>",$option_domeniche);
$checked_sett = $ultime_sel_ins_prezzi_s[3];
$checked_gio = $ultime_sel_ins_prezzi_s[4];
} # fine if ($ultime_sel_ins_prezzi_s[0] == $anno)
else {
$option_domeniche1 = $option_domeniche;
$option_domeniche2 = $option_domeniche;
$checked_sett = "checked=\"checked\"";
$checked_gio = "";
} # fine else if ($ultime_sel_ins_prezzi_s[0] == $anno)
echo mex("Settimane dal",$pag)."
 <select name=\"inizioperiodosett1\" id=\"id_sdm149\" onChange=\"update_selected_dates('149')\">$option_domeniche1</select>
".mex("al",$pag)." <select name=\"fineperiodosett1\" id=\"id_sdm150\" onChange=\"update_selected_dates('150')\">$option_domeniche2</select><br><br>
<label><input name=\"tipo_prezzo\" value=\"sett\" id=\"tipo_prezzo_sett\" $checked_sett type=\"radio\">
".mex("Prezzo dell'intera settimana",$pag).":</label> 
<input type=\"text\" name=\"prezzosett\" size=\"12\" onfocus=\"document.getElementById('tipo_prezzo_sett').checked='1'\"> +
 <input type=\"text\" name=\"prezzosettp\" size=\"10\" onfocus=\"document.getElementById('tipo_prezzo_sett').checked='1'\">*p $Euro<br>
<br><div class=\"linhbox\">
<label><input name=\"tipo_prezzo\" value=\"gio\" id=\"tipo_prezzo_gio\" $checked_gio type=\"radio\">
".mex("Prezzi dei giorni",$pag).":</label> ";
for ($num1 = 1 ; $num1 <= 7 ; $num1++) {
if ($giorno_vedi_ini_sett == 0) echo mex("Dom/Lun",$pag);
if ($giorno_vedi_ini_sett == 1) echo mex("Lun/Mar",$pag);
if ($giorno_vedi_ini_sett == 2) echo mex("Mar/Mer",$pag);
if ($giorno_vedi_ini_sett == 3) echo mex("Mer/Gio",$pag);
if ($giorno_vedi_ini_sett == 4) echo mex("Gio/Ven",$pag);
if ($giorno_vedi_ini_sett == 5) echo mex("Ven/Sab",$pag);
if ($giorno_vedi_ini_sett == 6) echo mex("Sab/Dom",$pag);
echo ": <input type=\"text\" name=\"prezzoperiodo$num1\" size=\"10\" onfocus=\"document.getElementById('tipo_prezzo_gio').checked='1'\"> +
 <input type=\"text\" name=\"prezzoperiodo$num1"."p\" size=\"8\" onfocus=\"document.getElementById('tipo_prezzo_gio').checked='1'\">*p $Euro;";
if ($num1 == 1 or $num1 == 3 or $num1 == 5) echo "<br>";
else echo " ";
$giorno_vedi_ini_sett++;
if ($giorno_vedi_ini_sett == 7) $giorno_vedi_ini_sett = 0;
} # fine for $num1

echo "</div><table><tr><td style=\"height: 2px;\"></td></tr></table>
<input type=\"hidden\" name=\"inserisci_settimanalmente\" value=\"1\">
<button class=\"ipri\" type=\"submit\"><div>".mex("inserisci o modifica i prezzi",$pag)."</div></button>
</div></form><br>
<hr style=\"width: 95%\">";
} # fine if ($tipo_periodi == "g")


echo "</div><a name=\"imp_pre\"></a>
<div style=\"height: 8px;\"></div>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<script type=\"text/javascript\">
<!--
function att_dis_arrotond () {
var select = document.getElementById('tip_per');
var arrotond = document.getElementById('imp_arr');
if (select.selectedIndex == 0) arrotond.disabled = false;
else arrotond.disabled = true;
}
-->
</script>
<table cellspacing=\"0\" cellpadding=\"0\"><tr><td colspan=\"2\">";
$importa_percent = "0";
$tipo_percent = "p";
$importa_arrotond = $arrotond_predef;
$parte_prezzo = "f";
$periodi_importa = "t";
$iniper_imp = "";
$fineper_imp = "";
if ($tar_importa_canc) {
$tar_imp_mod = "tariffa$tar_importa_canc";
$tariffa_trovata = 0;
if (!$per_importa_canc) $per_importa_canc = "-";
for ($num1 = 0 ; $num1 < $dati_tariffe[$tar_imp_mod]['num_per_importa'] ; $num1++) {
if ($per_importa_canc == $dati_tariffe[$tar_imp_mod]['periodo_importa_i'][$num1]."-".$dati_tariffe[$tar_imp_mod]['periodo_importa_f'][$num1]) {
$tariffa_trovata = 1;
$ord_imp_mod = $num1;
$importa_percent = $dati_tariffe[$tar_imp_mod]['val_importa'][$num1];
$tipo_percent = $dati_tariffe[$tar_imp_mod]['tipo_importa'][$num1];
if (strcmp($dati_tariffe[$tar_imp_mod]['arrotond_importa'][$num1],"")) $importa_arrotond = $dati_tariffe[$tar_imp_mod]['arrotond_importa'][$num1];
$parte_prezzo = $dati_tariffe[$tar_imp_mod]['parte_prezzo'][$num1];
if ($dati_tariffe[$tar_imp_mod]['periodo_importa_i'][$num1]) {
$periodi_importa = "s";
$iniper_imp = esegui_query("select datainizio from $tableperiodi where idperiodi = '".aggslashdb($dati_tariffe[$tar_imp_mod]['periodo_importa_i'][$num1])."' ");
$iniper_imp = risul_query($iniper_imp,0,'datainizio');
$fineper_imp = esegui_query("select datafine from $tableperiodi where idperiodi = '".aggslashdb($dati_tariffe[$tar_imp_mod]['periodo_importa_f'][$num1])."' ");
$fineper_imp = risul_query($fineper_imp,0,'datafine');
} # fine if ($dati_tariffe[$tar_imp_mod]['periodo_importa_i'][$num1])
if ($per_importa_canc == "-") $per_importa_canc = "";
echo "<input type=\"hidden\" name=\"tar_importa_mod\" value=\"1\">
<input type=\"hidden\" name=\"tar_importa_canc\" value=\"$tar_importa_canc\">
<input type=\"hidden\" name=\"per_importa_canc\" value=\"$per_importa_canc\">";
break;
} # fine if ($per_importa_canc == $dati_tariffe[$tar_imp_mod]['periodo_importa_i'][$num1]."-".$dati_tariffe[$tar_imp_mod]['periodo_importa_f'][$num1])
} # fine for $num1
if (!$tariffa_trovata) $tar_imp_mod = "";
} # fine if ($tar_importa_canc)
else $tar_imp_mod = "";
if ($tar_imp_mod) echo mex("Importa sempre",$pag);
else echo "<select name=\"tipo_importa\">
<option value=\"ora\" selected>".mex("Importa ora",$pag)."</option>
<option value=\"sempre\">".mex("Importa sempre",$pag)."</option>
</select>";
echo " ".mex("i prezzi della",$pag)." ";
$select_nomi_tariffe = "";
echo "<select name=\"tariffa_a\">";
if ($tar_imp_mod) echo str_replace("$tar_imp_mod\">","$tar_imp_mod\" selected>",$lista_opt_tariffe_no_esporta);
echo $lista_opt_tariffe_no_esporta;
echo "</select> ".mex("dalla",$pag)." <select name=\"tariffa_da\">";
if ($tar_imp_mod) echo str_replace("tariffa".$dati_tariffe[$tar_imp_mod]['importa_prezzi'][$ord_imp_mod]."\">","tariffa".$dati_tariffe[$tar_imp_mod]['importa_prezzi'][$ord_imp_mod]."\" selected>",$lista_opt_tariffe_cambia_tutti);
else echo $lista_opt_tariffe_cambia_tutti;
$sel_p = "";
$sel_g = "";
$sel_s = "";
if ($tipo_percent == "p") $sel_p = " selected";
if ($tipo_percent == "g") $sel_g = " selected";
if ($tipo_percent == "s") $sel_s = " selected";
echo "</select> ".mex("aggiungendo",$pag)." <span style=\"white-space: nowrap;\">
<input type=\"text\" name=\"importa_percent\" size=\"3\" value=\"$importa_percent\"><select name=\"tipo_percent\" id=\"tip_per\" onchange=\"att_dis_arrotond()\"> 
<option value=\"perc\"$sel_p>%</option>
<option value=\"euro_g\"$sel_g>$Euro ".mex("$parola_alla $parola_settimana",$pag)."</option>";
if ($tipo_periodi == "g") echo "<option value=\"euro_s\"$sel_s>$Euro ".mex("alla settimana",$pag)."</option>";
$sel_f = "";
$sel_p = "";
$sel_2 = "";
if ($parte_prezzo == "f") $sel_f = " selected";
if ($parte_prezzo == "p") $sel_p = " selected";
if ($parte_prezzo == "2") $sel_2 = " selected";
echo "</select></span> (".mex("arrotondato a",$pag)."
<input type=\"text\" name=\"importa_arrotond\" id=\"imp_arr\" value=\"$importa_arrotond\" size=\"4\">)
 <select name=\"parte_prezzo\">
<option value=\"f\"$sel_f>".mex("al prezzo fisso",$pag)."</option>
<option value=\"p\"$sel_p>".mex("al prezzo per persona",$pag)."</option>
<option value=\"2\"$sel_2>".mex("ad entrambi i prezzi",$pag)."</option>
</select>
<input type=\"hidden\" name=\"importa_tariffa\" value=\"1\"></td><td>
&nbsp;<button class=\"xpri\" type=\"submit\"><div>";
if ($tar_imp_mod) echo mex("modifica",$pag);
else echo mex("importa",$pag);
if ($periodi_importa == "t") { $ckd_t = " checked"; $ckd_s = ""; }
if ($periodi_importa == "s") { $ckd_t = ""; $ckd_s = " checked"; }
echo "</div></button>&nbsp;&nbsp;</td></tr>
<tr><td style=\"width: 24px;\"></td><td><label><input type=\"radio\" name=\"periodi_importa\" value=\"t\"$ckd_t> ".mex("in tutti i periodi in modo predefinito",$pag)."</label></td></tr>
<tr><td></td><td style=\"padding-top: 2px;\"><span onclick=\"document.getElementById('perimp_s').checked='1'\">
<label><input type=\"radio\" id=\"perimp_s\" name=\"periodi_importa\" value=\"s\"$ckd_s> ".mex("dal",$pag)." </label>";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","iniper_imp",$iniper_imp,"","",$id_utente,$tema);
echo "<label for=\"perimp_s\"> ".mex("al",$pag)." </label>";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.$id_utente.php","fineper_imp",$fineper_imp,"","",$id_utente,$tema);
echo "</span></td></tr></table>
</div></form><table><tr><td style=\"height: 6px;\"></td></tr></table>";

function rowbgcolor () {
global $rowbgcolor,$t2row1color,$t2row2color;
if ($rowbgcolor == $t2row2color) $rowbgcolor = $t2row1color;
else $rowbgcolor = $t2row2color;
return $rowbgcolor;
} # fine function rowbgcolor
for ($num1 = 1 ; $num1 <= $dati_tariffe['num'] ; $num1++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$num1] == "SI") {
$tariffa = "tariffa".$num1;
if ($dati_tariffe[$tariffa]['imp_prezzi_int']) {
$vett_ord = array();
$vett_ord[0] = 0;
if ($dati_tariffe[$tariffa]['importa_prezzi'][1]) {
$periodi_ord = array();
for ($num2 = 1 ; $num2 < $dati_tariffe[$tariffa]['num_per_importa'] ; $num2++) $periodi_ord[$dati_tariffe[$tariffa]['periodo_importa_i'][$num2]] = $num2;
ksort($periodi_ord);
reset($periodi_ord);
$num_ord = 1;
foreach ($periodi_ord as $per => $num2) {
$vett_ord[$num_ord] = $num2;
$num_ord++;
} # fine foreach ($periodi_ord as $per => $num2)
} # fine if ($dati_tariffe[$tariffa]['importa_prezzi'][1])
for ($num_ord = 0 ; $num_ord < $dati_tariffe[$tariffa]['num_per_importa'] ; $num_ord++) {
$num2 = $vett_ord[$num_ord];
if ($dati_tariffe[$tariffa]['importa_prezzi'][$num2]) {
echo "<table cellspacing=0 cellpadding=0 style=\"background-color: ";
if ($tar_imp_mod == $tariffa and $num2 == $ord_imp_mod) echo $t1dates;
else echo rowbgcolor();
echo ";\"><tr>";
if ($num2 > 0 and $dati_tariffe[$tariffa]['importa_prezzi'][0]) echo "<td style=\"width: 12px;\"></td>";
echo "<td><form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php#imp_pre\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tar_importa_canc\" value=\"$num1\">";
if ($dati_tariffe[$tariffa]['periodo_importa_i'][$num2]) {
echo "<input type=\"hidden\" name=\"per_importa_canc\" value=\"".$dati_tariffe[$tariffa]['periodo_importa_i'][$num2]."-".$dati_tariffe[$tariffa]['periodo_importa_f'][$num2]."\">";
$ini_imp = esegui_query("select * from $tableperiodi where idperiodi = '".$dati_tariffe[$tariffa]['periodo_importa_i'][$num2]."' ");
$ini_imp = formatta_data(risul_query($ini_imp,0,'datainizio'),$stile_data);
$fine_imp = esegui_query("select * from $tableperiodi where idperiodi = '".$dati_tariffe[$tariffa]['periodo_importa_f'][$num2]."' ");
$fine_imp = formatta_data(risul_query($fine_imp,0,'datafine'),$stile_data);
echo " ".ucfirst(mex("dal",$pag))." $ini_imp ".mex("al",$pag)." $fine_imp ".mex("importa",$pag);
} # fine if ($dati_tariffe[$tariffa]['periodo_importa_i'][$num2])
else {
if (!$dati_tariffe[$tariffa]['importa_prezzi'][1]) echo mex("Importa sempre",$pag);
else echo mex("In modo predefinito importa",$pag);
} # fine else if ($dati_tariffe[$tariffa]['periodo_importa_i'][$num2])
echo " ".mex("i prezzi della",$pag)." <b>".mex("tariffa",$pag)."$num1";
if ($dati_tariffe[$tariffa]['nome']) echo " (".$dati_tariffe[$tariffa]['nome'].")";
echo "</b> ".mex("dalla",$pag)." <em>".mex("tariffa",$pag).$dati_tariffe[$tariffa]['importa_prezzi'][$num2];
if ($dati_tariffe['tariffa'.$dati_tariffe[$tariffa]['importa_prezzi'][$num2]]['nome']) echo " (".$dati_tariffe['tariffa'.$dati_tariffe[$tariffa]['importa_prezzi'][$num2]]['nome'].")";
echo "</em>";
if ($dati_tariffe[$tariffa]['val_importa'][$num2]) {
echo " ".mex("aggiungendo",$pag)." ".$dati_tariffe[$tariffa]['val_importa'][$num2];
if ($dati_tariffe[$tariffa]['tipo_importa'][$num2] == "p") echo "% (".mex("arrotondato a",$pag)." ".$dati_tariffe[$tariffa]['arrotond_importa'][$num2]." $Euro)";
if ($dati_tariffe[$tariffa]['tipo_importa'][$num2] == "g") echo " $Euro ".mex("$parola_alla $parola_settimana",$pag)."";
if ($dati_tariffe[$tariffa]['tipo_importa'][$num2] == "s") echo " $Euro ".mex("alla settimana",$pag)."";
if ($dati_tariffe[$tariffa]['parte_prezzo'][$num2] == "f") echo " ".mex("al prezzo fisso",$pag)."";
if ($dati_tariffe[$tariffa]['parte_prezzo'][$num2] == "p") echo " ".mex("al prezzo per persona",$pag)."";
if ($dati_tariffe[$tariffa]['parte_prezzo'][$num2] == "2") echo " ".mex("ad entrambi i prezzi",$pag)."";
} # fine if ($dati_tariffe[$tariffa]['val_importa'][$num2])
echo " <button class=\"edtm\" type=\"submit\" name=\"mod_importa\" value=\"1\"><div>".mex("modifica",$pag)."</div></button>
 <button class=\"cncm\" type=\"submit\"><div>".mex("cancella",$pag)."</div></button>
&nbsp;</div></form></td></tr></table>";
} # fine if ($dati_tariffe[$tariffa]['importa_prezzi'][$num2])
} # fine for $num_ord
} # fine if ($dati_tariffe[$tariffa]['imp_prezzi_int'])
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$num1] == "SI")
} # fine for $num1

echo "<hr style=\"width: 95%\">";

} # fine if ($lista_opt_tariffe)
} # fine if ($priv_mod_tariffe != "n")

if ($priv_ins_costi_agg != "n") {

# form per i costi aggiuntivi e per la caparra.
if (defined("C_MASSIMO_NUM_COSTI_AGG") and C_MASSIMO_NUM_COSTI_AGG != 0) {
$num_costi_agg_esistenti = esegui_query("select idntariffe from $tablenometariffe where idntariffe > 10");
$num_costi_agg_esistenti = numlin_query($num_costi_agg_esistenti);
if ($num_costi_agg_esistenti >= C_MASSIMO_NUM_COSTI_AGG) $aggiungi_costi_aggiuntivi = "NO";
} # fine if (defined("C_MASSIMO_NUM_COSTI_AGG") and C_MASSIMO_NUM_COSTI_AGG != 0)
if ($aggiungi_costi_aggiuntivi != "NO") {

echo "<a name=\"ins_costi_agg\"></a>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"passo\" value=\"1\">
<input type=\"hidden\" name=\"avanti\" value=\"SI\">
<h5>".mex("Costi aggiuntivi",$pag)."</h5><br><br>
<div class=\"linhbox\">
".mex("Nome del nuovo costo aggiuntivo",$pag).":
<input type=\"text\" name=\"nomecostoagg\" size=\"24\" value=\"$nomecostoagg\">.<br>
".mex("Categoria",$pag).": <input type=\"text\" name=\"categoria_ca\" value=\"$categoria_ca\" size=\"18\"> (".mex("opzionale",$pag).").<br>
".mex("Tipo di costo aggiuntivo",$pag).":
<select name=\"tipo_ca\">";
if ($tipo_ca == "u") $selected = " selected";
else $selected = "";
echo "<option value=\"u\"$selected>".mex("unico",$pag)."</option>";
if ($tipo_ca == "s") $selected = " selected";
else $selected = "";
echo "<option value=\"s\"$selected>".mex("$parola_settimanale",$pag)."</option>
</select>.
</div><br><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"inseriscicosti\" value=\"1\">
<button class=\"aexc\" type=\"submit\"><div>".mex("Procedi nell'inserimento del nuovo costo aggiuntivo",$pag)."</div></button>
</div></div></form><br>
<hr style=\"width: 95%\">";

$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,"NO");
$opt_costi_agg = "";
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$numca]['id']] == "SI") {
$opt_costi_agg .= "<option value=\"".$dati_ca[$numca]['id']."\">".$dati_ca[$numca]['nome']."</option>";
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine for $numca
if ($opt_costi_agg) {
echo "<table><tr><td style=\"height: 6px;\"></td></tr></table>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"importa_costo\" value=\"SI\">
".mex("Inserisci un nuovo costo aggiuntivo chiamato",$pag)."
 <input type=\"text\" name=\"nomecostoagg\" size=\"20\">
 ".mex("importando le caratteristiche da",$pag)."
 <select name=\"costo_importa\">
$opt_costi_agg
</select>
<button class=\"xexc\" type=\"submit\"><div>".mex("importa",$pag)."</div></button>.
<table><tr><td style=\"height: 6px;\"></td></tr></table>
</div></form><hr style=\"width: 95%\">";
} # fine if ($opt_costi_agg)

echo "<table><tr><td style=\"height: 6px;\"></td></tr></table>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"ins_rapido_costo\" value=\"SI\">
".mex("Inserimento rapido di un nuovo costo aggiuntivo per",$pag)."
 <select name=\"tipocostoagg\">
<option value=\"perm_min\">".mex("permanenza minima",$pag)."</option>
<option value=\"num_bamb\">".mex("numero di bambini",$pag)."</option>
<option value=\"letto_agg\">".mex("letto aggiuntivo",$pag)."</option>
<option value=\"off_spec\">".mex("offerta speciale",$pag)."</option>
</select>
<button class=\"aexc\" type=\"submit\"><div>".mex("inserisci",$pag)."</div></button>.
<table><tr><td style=\"height: 6px;\"></td></tr></table>
</div></form><hr style=\"width: 95%\">";

} # fine if ($aggiungi_costi_aggiuntivi != "NO")
} # fine if ($priv_ins_costi_agg != "n")

if ($priv_mod_tariffe != "n" and $lista_opt_tariffe) {
$arrotond_cap = ($arrotond_predef * 10);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<h5>".mex("Caparra",$pag)."</h5><br>
<table cellspacing=\"0\"><tr><td>"
.mex("La caparra normale per la",$pag)."
 <select name=\"tipotariffa\">
$lista_opt_tariffe
</select> ".mex("è",$pag).":</td><td onclick=\"document.getElementById('tipo_cap_perc').checked='1'\">
<label><input type=\"radio\" id=\"tipo_cap_perc\" name=\"tipo_caparra\" value=\"perc\" checked>
 ".mex("il",$pag)." </label><input type=\"text\" name=\"caparra_percent\" size=\"2\" maxlength=\"3\"><label for=\"tipo_cap_perc\">%
 ".mex("della tariffa arrotondato a",$pag)."
 </label><input type=\"text\" name=\"caparra_arrotond\" value=\"$arrotond_cap\" size=\"5\"><label for=\"tipo_cap_perc\">
 $Euro.</label></td></tr>
<tr><td></td><td onclick=\"document.getElementById('tipo_cap_val').checked='1'\">
<input type=\"radio\" id=\"tipo_cap_val\" name=\"tipo_caparra\" value=\"val\">
<input type=\"text\" name=\"caparra_val\" size=\"5\"><label for=\"tipo_cap_val\"> $Euro.</label></td></tr>
<tr><td></td><td onclick=\"document.getElementById('tipo_cap_gio').checked='1'\">
<label><input type=\"radio\" id=\"tipo_cap_gio\" name=\"tipo_caparra\" value=\"gio\">
".mex("il prezzo della tariffa per $parola_le prim$lettera_e",$pag)." </label><select name=\"caparra_gio\">";
for ($num1 = 1 ; $num1 <= 10 ; $num1++) echo "<option value=\"$num1\">$num1</option>";
echo "</select><label for=\"tipo_cap_gio\"> ".mex("$parola_settimane",$pag).".</label></td></tr>
</table>
<table><tr><td style=\"height: 2px;\"></td></tr></table><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"modificacaparra\" value=\"1\">
<button class=\"taxs\" type=\"submit\"><div>".mex("Inserisci o modifica la caparra",$pag)."</div></button>
</div></div></form><br>
<hr style=\"width: 95%\">";
} # fine if ($priv_mod_tariffe != "n" and $lista_opt_tariffe)

if ($priv_mod_tariffe != "n" and $lista_opt_tariffe) {
$arrotond_com = ($arrotond_predef * 1);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modificacommissioni\" value=\"SI\">
<h5>".mex("Commissioni",$pag)."</h5><br>
<table cellspacing=\"0\"><tr><td>"
.mex("Le commissioni normali sulla",$pag)."
 <select name=\"tipotariffa\">
$lista_opt_tariffe
</select> ".mex("sono",$pag).":</td><td onclick=\"document.getElementById('tipo_com_perc').checked='1'\">
<label><input type=\"radio\" id=\"tipo_com_perc\" name=\"tipo_commissioni\" value=\"perc\" checked>
 ".mex("il",$pag)." </label><input type=\"text\" name=\"commissioni_percent\" size=\"2\" maxlength=\"3\"><label for=\"tipo_com_perc\">%
 ".mex("della",$pag)." </label><select name=\"commissioni_base\">
<option value=\"t\">".mex("tariffa",$pag)."</option>
<option value=\"ts\">".mex("tariffa + sconto",$pag)."</option>
<option value=\"tsc\">".mex("tariffa + sconto + costi agg.",$pag)."</option>
</select><label for=\"tipo_com_perc\"> ".mex("arrotondato a",$pag)."
</label><input type=\"text\" name=\"commissioni_arrotond\" value=\"$arrotond_com\" size=\"5\"><label for=\"tipo_com_perc\"> $Euro.</label>
</td></tr><tr><td></td><td onclick=\"document.getElementById('tipo_com_val').checked='1'\">
<input type=\"radio\" id=\"tipo_com_val\" name=\"tipo_commissioni\" value=\"val\">
<input type=\"text\" name=\"commissioni_val\" size=\"5\"><label for=\"tipo_com_val\"> $Euro ".mex("$parola_alla $parola_settimana",$pag).".
</label></td></tr></table>
<table><tr><td style=\"height: 2px;\"></td></tr></table><div style=\"text-align: center;\">
<button class=\"taxs\" type=\"submit\"><div>".mex("Inserisci o modifica le commissioni normali",$pag)."</div></button>
</div></div></form><br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modificacommper\" value=\"SI\">
<table cellspacing=\"0\"><tr><td>"
.mex("Sulla",$pag)."
 <select name=\"tipotariffa\">
$lista_opt_tariffe
</select> ".mex("dal",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.1.php","comm_dal",$comm_dal,"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno.1.php","comm_al",$comm_al,"","",$id_utente,$tema);
echo ":</td><td onclick=\"document.getElementById('tipo_com_percp').checked='1'\"><label>
<input type=\"radio\" id=\"tipo_com_percp\" name=\"tipo_commissioni\" value=\"perc\" checked>
 ".mex("il",$pag)." </label><input type=\"text\" name=\"commissioni_percent\" size=\"2\" maxlength=\"3\"><label for=\"tipo_com_percp\">%
 ".mex("della",$pag)." </label><select name=\"commissioni_base\">
<option value=\"t\">".mex("tariffa",$pag)."</option>
<option value=\"ts\">".mex("tariffa + sconto",$pag)."</option>
<option value=\"tsc\">".mex("tariffa + sconto + costi agg.",$pag)."</option>
</select><label for=\"tipo_com_percp\"> ".mex("arrotondato a",$pag)."
</label><input type=\"text\" name=\"commissioni_arrotond\" value=\"$arrotond_com\" size=\"5\"><label for=\"tipo_com_percp\"> $Euro.
</label></td></tr><tr><td></td><td onclick=\"document.getElementById('tipo_com_valp').checked='1'\">
<input type=\"radio\" id=\"tipo_com_valp\" name=\"tipo_commissioni\" value=\"val\">
<input type=\"text\" name=\"commissioni_val\" size=\"5\"><label for=\"tipo_com_valp\"> $Euro ".mex("$parola_alla $parola_settimana",$pag).".
</label></td></tr></table>
<table><tr><td style=\"height: 2px;\"></td></tr></table><div style=\"text-align: center;\">
<button class=\"txpr\" type=\"submit\"><div>".mex("Inserisci o modifica le commissioni in questo periodo",$pag)."</div></button>
</div></div></form><br>
<hr style=\"width: 95%\">";
} # fine if ($priv_mod_tariffe != "n" and $lista_opt_tariffe)

if ($priv_mod_tariffe != "n" and $lista_opt_tariffe) {
$arrotond_tas = ($arrotond_predef * 1);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modificatasse\" value=\"SI\">
<h5>".mex("Tasse",$pag)."</h5><br>
".mex("Tasse applicate su",$pag)."
 <select name=\"tipotariffa\">";
if ($id_utente == 1) echo "<option value=\"tutte\">".mex("tutte le tariffe",$pag)."</option>";
echo "$lista_opt_tariffe
</select>:
 <input type=\"text\" name=\"tasse_percent\" size=\"3\" maxlength=\"6\">%
 <button class=\"taxs\" type=\"submit\"><div>".mex("Inserisci o modifica le tasse",$pag)."</div></button>
</div></form>
<small>(".mex("tutti i prezzi delle tariffe si intendono con tasse già incluse",$pag).")</small><br><br>";

if ($modifica_pers != "NO") {
if ($id_utente == 1) $id_utente_mod = "tutti";
else $id_utente_mod = $id_utente;
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_utente_mod\" value=\"$id_utente_mod\">
<input type=\"hidden\" name=\"aggiorna_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"origine\" value=\"./creaprezzi.php\">
<input type=\"hidden\" name=\"cambiaarrtasse\" value=\"SI\">";
$arrotond_tasse = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'arrotond_tasse' and idutente = '$id_utente'");
$arrotond_tasse = virgola_in_num(risul_query($arrotond_tasse,0,'valpersonalizza'),$stile_soldi);
echo ucfirst(mex("valore a cui arrotondare le percentuali delle tasse","personalizza.php")).":
 <input type=\"text\" name=\"nuovo_arrotond_tasse\" size=\"4\" value=\"$arrotond_tasse\">
<button class=\"edit\" type=\"submit\"><div>".mex("Cambia","personalizza.php")."</div></button>
</div></form><br>";
} # fine if ($modifica_pers != "NO")

echo "<hr style=\"width: 95%\">";
} # fine if ($priv_mod_tariffe != "n" and $lista_opt_tariffe)

if ($priv_vedi_tab_periodi != "n") {
echo "<div style=\"text-align: center;\"><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<button class=\"rate\" type=\"submit\"><div>".mex("Vedi i prezzi già inseriti",$pag)."</div></button>
</div></form><br>";
} # fine if ($priv_vedi_tab_periodi != "n")

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
</div></form></div><br>";



} # fine if ($mostra_form_iniziale != "NO")

} # fine else if (($modificacaparra or $modificacommissioni or $modificacommper or $modificatasse) and $priv_mod_tariffe != "n")



if ($aggiorna_tariffe_interconn == "SI") {
$lock = 1;
$aggiorna_disp = 0;
$aggiorna_tar = 1;
if (@function_exists('pcntl_fork')) include("./includes/interconnect/aggiorna_ic_fork.php");
else include("./includes/interconnect/aggiorna_ic.php");
} # fine if ($aggiorna_tariffe_interconn == "SI")


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and ($priv_mod_tariffe == "s" or $priv_ins_costi_agg != "n"))
} # fine if ($id_utente)




?>