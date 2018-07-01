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



$pag = "inizio.php";
$titolo = "HotelDruid";

include("./costanti.php");
include("./includes/funzioni.php");


unset($numconnessione);
$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente and $numconnessione and $logout == "SI") {
$tabelle_lock = array($PHPR_TAB_PRE."sessioni");
$tabelle_lock = lock_tabelle($tabelle_lock);
esegui_query("delete from $PHPR_TAB_PRE"."sessioni where idsessioni = '$id_sessione'");
unlock_tabelle($tabelle_lock);
unset($id_sessione);
$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
} # fine if ($id_utente and $numconnessione and $logout == "SI")
if ($id_utente) {




# Controllo se sono stati inseriti i dati permanenti.
if (@is_file(C_DATI_PATH."/dati_connessione.php") != true) {
$show_bar = "NO";
if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
if (@is_dir("./includes/lang/en")) $lingua_mex = "en";
else $lingua_mex = "ita";
if (@isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
$lingua_browser = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
foreach ($lingua_browser as $lang) {
if ($lang == "en") break;
if ($lang == "it") {
$lingua_mex = "ita";
break;
} # fine if ($lang == "it")
if (strlen($lang) == 2 and @is_dir("./includes/lang/$lang")) {
$lingua_mex = $lang;
break;
} # fine if (strlen($lang) == 2 and @is_dir("./includes/lang/$lang"))
} # fine foreach ($lingua_browser as $lang)
} # fine if (@isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
if (@is_file("./COPYING")) $file_copying = "file <a href=\"COPYING\">COPYING</a>";
else $file_copying = "<a href=\"http://www.gnu.org/licenses/agpl-3.0.html\">AGPLv3</a> License";
echo "<div style=\"text-align: center;\"><h3>".mex("Benvenuto a HOTELDRUID",$pag).".</h3><br><br>
HOTELDRUID version ".C_PHPR_VERSIONE_TXT.", Copyright (C) 2001-2018 Marco M. F. De Santis<br>
HotelDruid comes with ABSOLUTELY NO WARRANTY; <br>for details see the $file_copying.<br>
This is free software, and you are welcome to redistribute it<br>
 under certain conditions; see the $file_copying for details.<br>
</div><hr style=\"width: 95%\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"creadb.php\"><div>
<br><br>
".mex("Scegli la lingua",$pag).": <select name=\"lingua\">";
if ($lingua_mex == "ita") $sel = " selected";
else $sel = "";
echo "<option value=\"ita\"$sel>italiano</option>";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
if ($ini_lingua == $lingua_mex) $sel = " selected";
else $sel = "";
echo "<option value=\"$ini_lingua\"$sel>$nome_lingua</option>";
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "</select><br>
<input class=\"sbutton\" type=\"submit\" name=\"crealo\" value=\"".mex("crea il database",$pag)."\"><br>
</div></form>";
if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");
} # fine if (@is_file(C_DATI_PATH."/dati_connessione.php") != true)

else {

if (defined('C_CREA_ULTIMO_ACCESSO') and C_CREA_ULTIMO_ACCESSO == "SI") {
$fileaperto = @fopen(C_DATI_PATH."/ultimo_accesso","w+");
@fwrite($fileaperto,date("d-m-Y H:i:s"));
@fclose($fileaperto);
} # fine if (defined('C_CREA_ULTIMO_ACCESSO') and C_CREA_ULTIMO_ACCESSO == "SI")

if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else {
$anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
$priv_crea_backup = substr($priv_mod_pers,1,1);
$priv_crea_interconnessioni = substr($priv_mod_pers,3,1);
$priv_gest_pass_cc = substr($priv_mod_pers,5,1);
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
if (substr($priv_ins_clienti,0,1) != "s") $inserimento_nuovi_clienti = "NO";
if (substr($priv_ins_clienti,1,1) != "s" and substr($priv_ins_clienti,1,1) != "p" and substr($priv_ins_clienti,1,1) != "g") $modifica_clienti = "NO";
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$priv_messaggi = risul_query($privilegi_globali_utente,0,'priv_messaggi');
$priv_vedi_messaggi = substr($priv_messaggi,0,1);
$priv_inventario = risul_query($privilegi_globali_utente,0,'priv_inventario');
$priv_vedi_beni_inv = substr($priv_inventario,0,1);
$priv_vedi_inv_mag = substr($priv_inventario,2,1);
$priv_vedi_inv_app = substr($priv_inventario,6,1);
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
$priv_ins_costi = risul_query($privilegi_annuali_utente,0,'priv_ins_costi');
$priv_ins_spese = substr($priv_ins_costi,0,1);
$priv_ins_entrate = substr($priv_ins_costi,1,1);
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_costi_agg = substr($priv_mod_prenota,8,1);
$priv_mod_pagato = substr($priv_mod_prenota,10,1);
$priv_mod_prenota_iniziate = substr($priv_mod_prenota,11,1);
$priv_mod_prenota_ore = substr($priv_mod_prenota,12,3);
$priv_mod_checkin = substr($priv_mod_prenota,20,1);
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
if ($priv_vedi_tab_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_vedi_tab_costi = substr($priv_vedi_tab,2,1);
$priv_vedi_tab_periodi = substr($priv_vedi_tab,3,1);
$priv_vedi_tab_regole = substr($priv_vedi_tab,4,1);
$priv_vedi_tab_appartamenti = substr($priv_vedi_tab,5,1);
$priv_vedi_tab_stat = substr($priv_vedi_tab,6,1);
$priv_vedi_tab_doc = substr($priv_vedi_tab,7,1);
$priv_ins_tariffe = risul_query($privilegi_annuali_utente,0,'priv_ins_tariffe');
$priv_mod_tariffe = substr($priv_ins_tariffe,0,1);
$priv_ins_costi_agg = substr($priv_ins_tariffe,1,1);
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
$modifica_pers = "SI";
$priv_crea_backup = "s";
$priv_crea_interconnessioni = "s";
$priv_gest_pass_cc = "s";
$inserimento_nuovi_clienti = "SI";
$modifica_clienti = "SI";
$vedi_clienti = "SI";
$priv_vedi_messaggi = "s";
$priv_vedi_beni_inv = "s";
$priv_vedi_inv_mag = "s";
$priv_vedi_inv_app = "s";
$priv_ins_nuove_prenota = "s";
$priv_ins_spese = "s";
$priv_ins_entrate = "s";
$priv_mod_prenotazioni = "s";
$priv_mod_costi_agg = "s";
$priv_mod_pagato = "s";
$priv_mod_prenota_iniziate = "s";
$priv_mod_prenota_ore = "000";
$priv_mod_checkin = "s";
$priv_vedi_tab_mesi = "s";
$priv_vedi_tab_prenotazioni = "s";
$priv_vedi_tab_costi = "s";
$priv_vedi_tab_periodi = "s";
$priv_vedi_tab_regole = "s";
$priv_vedi_tab_appartamenti = "s";
$priv_vedi_tab_doc = "s";
$priv_vedi_tab_stat = "s";
$priv_mod_tariffe = "s";
$priv_ins_costi_agg = "s";
} # fine else if ($id_utente != 1)




if (@is_file(C_DATI_PATH."/dati_subordinazione.php")) {
include(C_DATI_PATH."/dati_subordinazione.php");
$installazione_subordinata = "SI";
if (!$numconnessione) {
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
} # fine if (!$numconnessione)
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$stile_data = stile_data();
$form_aggiorna_sub = "<form accept-charset=\"utf-8\" method=\"post\" action=\"interconnessioni.php\"><div>
<small>".mex("Ultimo aggiornamento",$pag).": ".formatta_data($ultimo_aggiornamento,$stile_data)."
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"azione_ic\" value=\"SI\">
<input type=\"hidden\" name=\"aggiorna_subordinazione\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Aggiorna",$pag)."\">
</small></div></form>";
$inserimento_nuovi_clienti = "NO";
$modifica_clienti = "NO";
$priv_ins_nuove_prenota = "n";
$priv_ins_spese = "n";
$priv_ins_entrate = "n";
$priv_mod_tariffe = "n";
$priv_ins_costi_agg = "n";
$priv_mod_costi_agg = "n";
$priv_mod_checkin = "n";
} # fine if (@is_file(C_DATI_PATH."/dati_subordinazione.php"))
else $form_aggiorna_sub = "";


$anno_esistente = "SI";
if (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php") or $anno_utente_attivato != "SI") {
$anno_esistente = "NO";
$anno_attuale = date("Y",(time() + (C_DIFF_ORE * 3600) - (C_GIORNI_NUOVO_ANNO * 86400)));
if ($anno == $anno_attuale) {
if (!$numconnessione) {
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
} # fine if (!$numconnessione)
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$auto_crea_anno = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'auto_crea_anno' and idutente = '1'");
$auto_crea_anno = risul_query($auto_crea_anno,0,'valpersonalizza');
if ($auto_crea_anno == "SI") {
$tableanni = $PHPR_TAB_PRE."anni";
$ultimi_anni = esegui_query("select * from $tableanni order by idanni desc");
$num_ultimi_anni = numlin_query($ultimi_anni);
if ($num_ultimi_anni) $ultimo_anno = risul_query($ultimi_anni,0,'idanni');
else {
$ultimo_anno = "-2";
include_once("./includes/costanti.php");
} # fine else if ($num_ultimi_anni)
if ($anno == ($ultimo_anno + 1) or (!$num_ultimi_anni and C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO")) {
if ($num_ultimi_anni) {
$tipo_periodi_prec = risul_query($ultimi_anni,0,'tipo_periodi');
$importa_anno_prec = "SI";
$mese_fine = 4;
} # fine if ($num_ultimi_anni)
else {
$tipo_periodi_prec = "g";
$importa_anno_prec = "NO";
if (date("n") > 8) $mese_fine = 24;
else $mese_fine = 12;
} # fine else if ($num_ultimi_anni)
if ($tipo_periodi_prec == "s") {
$tableperiodi_ua = $PHPR_TAB_PRE."periodi".$ultimo_anno;
$giorno_ini_fine = esegui_query("select datainizio from $tableperiodi_ua where idperiodi = '1'");
$giorno_ini_fine = risul_query($giorno_ini_fine,0,'datainizio');
$giorno_ini_fine = explode("-",$giorno_ini_fine);
$giorno_ini_fine = date("w",mktime(0,0,0,$giorno_ini_fine[1],$giorno_ini_fine[2],$giorno_ini_fine[0]));
} # fine if ($tipo_periodi_prec == "s")
include("./includes/funzioni_costi_agg.php");
include("./includes/funzioni_anno.php");
# metto l'utente come 1 per evitare rallentamenti per la scrittura dei log
$id_utente_orig = $id_utente;
$id_utente = 1;
crea_nuovo_anno($anno,$PHPR_TAB_PRE,$DATETIME,$tipo_periodi_prec,$giorno_ini_fine,"1",$mese_fine,$importa_anno_prec,"SI",$pag);
$id_utente = $id_utente_orig;
$anno_esistente = "SI";
} # fine if ($anno == ($ultimo_anno + 1) or (!$num_ultimi_anni and C_UTILIZZA_SEMPRE_DEFAULTS == "AUTO"))
} # fine if ($auto_crea_anno == "SI")
} # fine if ($anno == $anno_attuale)
} # fine if (!@is_file(C_DATI_PATH."/selectperiodi$anno.1.php") or $anno_utente_attivato != "SI")


if ($anno_esistente == "SI") {
//esiste l'anno richiesto

if ($numconnessione and $anno and $id_sessione and substr($id_sessione,0,4) != $anno) {
$n_id_sessione = $anno.substr($id_sessione,4);
esegui_query("update $PHPR_TAB_PRE"."sessioni set idsessioni = '$n_id_sessione' where idsessioni = '$id_sessione' ");
$id_sessione = $n_id_sessione;
} # fine if ($anno and $id_sessione and substr($id_sessione,0,4) != $anno)

if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");

if (!$numconnessione and (!defined('C_MOSTRA_COPYRIGHT') or C_MOSTRA_COPYRIGHT != "NO")) echo "<table style=\"background-color: #ffffff; width: 99%; height: 97%; margin-right: auto; margin-left: auto;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"vertical-align: top;\">";

echo "$form_aggiorna_sub";

if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/menu.php");
else $hide_default_menu = 0;
if (!$hide_default_menu) {

if (!defined('C_URL_LOGO') or C_URL_LOGO == "") echo "<div id=\"mmenu\">";
else echo "<div style=\"background: url(".C_URL_LOGO.") no-repeat right top;\">";

if ($nome_utente_login) {
echo "<div class=\"logout\">".mex("Utente",$pag).": $nome_utente_login".".
 <a href=\"inizio.php?id_sessione=$id_sessione&amp;logout=SI\">".mex("Esci",$pag)."</a></div>";
} # fine if ($nome_utente_login)

$anno_succ = $anno + 1;
echo "<br><div style=\"text-align: center;\"><h3 id=\"h_mm\"><span>".mex("Menù principale dell'anno",$pag)." $anno";
if ($commento_subordinazione) echo " ($commento_subordinazione)";
echo "</span></h3><div id=\"mm_sub0\"></div>";
if ($priv_ins_nuove_prenota == "s") {
echo "<table class=\"ires\" cellspacing=0 cellpadding=0>
<tr><td style=\"width: 10px;\"></td><td>
<form style=\"display: inline;\" accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"ires\" type=\"submit\"><div>".mex("Inserisci una nuova prenotazione",$pag)."</div></button><br>
</div></form></td><td style=\"width: 10px;\">
</td></tr></table><div id=\"mm_sub1\"></div>";
} # fine if ($priv_ins_nuove_prenota == "s")
if ($priv_vedi_tab_mesi != "n") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"tabella.php\"><div>
<table class=\"vmon\" cellspacing=0 cellpadding=0>
<tr><td>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
".str_replace(" ","&nbsp;",ucfirst(mex("prenotazioni del mese di",$pag)));
$mese_attuale = date("n",(time() + (C_DIFF_ORE * 3600)));
for ($num1 = 1 ; $num1 <= 12 ; $num1++) {
$mese_invia[$num1] = "\"$num1\"";
if ($num1 == $mese_attuale) $mese_invia[$num1] .= " selected";
$mese_invia[$num1] .= ">";
} # fine for $num1
echo " <select name=\"mese\">
<option value=".$mese_invia[1].mex("Gennaio",$pag)."</option>
<option value=".$mese_invia[2].mex("Febbraio",$pag)."</option>
<option value=".$mese_invia[3].mex("Marzo",$pag)."</option>
<option value=".$mese_invia[4].mex("Aprile",$pag)."</option>
<option value=".$mese_invia[5].mex("Maggio",$pag)."</option>
<option value=".$mese_invia[6].mex("Giugno",$pag)."</option>
<option value=".$mese_invia[7].mex("Luglio",$pag)."</option>
<option value=".$mese_invia[8].mex("Agosto",$pag)."</option>
<option value=".$mese_invia[9].mex("Settembre",$pag)."</option>
<option value=".$mese_invia[10].mex("Ottobre",$pag)."</option>
<option value=".$mese_invia[11].mex("Novembre",$pag)."</option>
<option value=".$mese_invia[12].mex("Dicembre",$pag)."</option>
</select></td><td>
<p style=\"margin: 0pt; padding: 0pt;\">
<label><input type=\"radio\" name=\"anno_succ\" value=\"NO\" checked>$anno</label><br>
<label><input type=\"radio\" name=\"anno_succ\" value=\"SI\">$anno_succ</label>
</p></td><td>&nbsp;
<button class=\"vmon\" type=\"submit\"><div>".ucfirst(mex("visualizza",$pag))."</div></button>
</td></tr></table></div></form>";
} # fine if ($priv_vedi_tab_mesi != "n")
echo "<div id=\"mm_sub2\"></div>";

if ($priv_vedi_beni_inv == "n" and $priv_vedi_inv_mag == "n" and $priv_vedi_inv_app == "n") $priv_vedi_tab_inventario = "n";
if ($priv_vedi_tab_prenotazioni != "n" or $vedi_clienti != "NO" or $priv_vedi_tab_costi != "n" or $priv_vedi_tab_periodi != "n" or $priv_vedi_tab_regole != "n" or $priv_vedi_tab_appartamenti != "n" or $priv_vedi_tab_stat != "n" or $priv_vedi_tab_doc != "n" or $priv_vedi_tab_inventario != "n") {
echo "<form id=\"vtab\" accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<table class=\"vtab\" cellspacing=0 cellpadding=0><tr><td>
".str_replace(" ","&nbsp;",ucfirst(mex("tabella con",$pag)))."
<select name=\"tipo_tabella\">";
if ($priv_vedi_tab_prenotazioni != "n") echo "<option value=\"prenotazioni\">".mex("tutte le prenotazioni",$pag)."</option>";
if ($priv_vedi_tab_costi != "n") echo "<option value=\"costi\">".mex("le spese e le entrate",$pag)."</option>";
if ($priv_vedi_tab_periodi != "n") echo "<option value=\"periodi\">".mex("i periodi e le tariffe",$pag)."</option>";
if ($vedi_clienti != "NO") echo "<option value=\"clienti\">".mex("tutti i clienti",$pag)."</option>";
if ($priv_vedi_tab_regole != "n") echo "<option value=\"regole\">".mex("le regole di assegnazione",$pag)."</option>";
if ($priv_vedi_tab_appartamenti != "n") echo "<option value=\"appartamenti\">".mex("tutti gli appartamenti",'unit.php')."</option>";
if ($priv_vedi_tab_inventario != "n") echo "<option value=\"inventario\">".mex("inventario e magazzini",$pag)."</option>";
if ($priv_vedi_tab_doc != "n") echo "<option value=\"documenti\">".mex("i documenti salvati",$pag)."</option>";
if ($priv_vedi_tab_stat != "n") echo "<option value=\"statistiche\">".mex("le statistiche",$pag)."</option>";
echo "</select></td><td style=\"width: 4px;\"></td><td>
<button class=\"vtab\" type=\"submit\"><div>".ucfirst(mex("visualizza",$pag))."</div></button>
</td></tr></table></div></form><div id=\"mm_sub3\"></div>";
} # fine if ($priv_vedi_tab_prenotazioni != "n" or...

echo "<table id=\"mm_act\" cellspacing=0 cellpadding=0><tr>";
if ($inserimento_nuovi_clienti != "NO" or ($modifica_clienti != "NO" and $vedi_clienti != "NO")) {
echo "<td style=\"width: 13px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"clienti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"icli\" type=\"submit\"><div>".mex("Inserisci un nuovo cliente",$pag)."</div></button><br>
</div></form></td><td style=\"width: 13px;\"></td>";
} # fine if ($inserimento_nuovi_clienti != "NO" or...
if ($priv_ins_spese == "s" or $priv_ins_entrate == "s") {
echo "<td style=\"width: 13px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"costi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"inex\" type=\"submit\"><div>".mex("Spese ed entrate",$pag)."</div></button><br>
</div></form></td><td style=\"width: 13px;\"></td>";
} # fine if ($priv_ins_spese == "s" or $priv_ins_entrate == "s")
if ($priv_mod_prenotazioni != "n" and $priv_mod_costi_agg == "s") {
echo "<td style=\"width: 13px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"punto_vendita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"vpos\" type=\"submit\"><div>".mex("Punto vendita",$pag)."</div></button><br>
</div></form></td><td style=\"width: 13px;\"></td>";
} # fine if ($priv_mod_prenotazioni != "n" and $priv_mod_costi_agg == "s")
if ($priv_vedi_messaggi == "s") {
if ($numconnessione) {
$nuovo_mess = 0;
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$messaggi = esegui_query("select idmessaggi from $tablemessaggi where datavisione < '$adesso' and idutenti_visto $LIKE '%,$id_utente,%'");
if (numlin_query($messaggi) > 0) {
$nuovo_mess = 1;
} # fine if 
} # fine if ($numconnessione)
if ($nuovo_mess) {
$gt = "<b style=\"color: red;\">&gt;</b>";
$lt = "<b style=\"color: red;\">&lt;</b>";
} # fine if ($nuovo_mess)
else {
$gt = "";
$lt = "";
} # fine else if ($nuovo_mess)
echo "<td style=\"width: 13px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"messaggi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$gt<button class=\"mess\" type=\"submit\"><div>".mex("Messaggi",$pag)."</div></button>$lt<br>
</div></form></td><td style=\"width: 13px;\"></td>";
} # fine if ($priv_vedi_messaggi == "s")
echo "</tr></table>";

if ($priv_ins_nuove_prenota == "s" or $priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n") {
echo "<table class=\"rbox\" style=\"margin-left: auto; margin-right: auto;\" cellspacing=0 cellpadding=0><tr><td>
<div id=\"mm_sub4\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"disponibilita.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\"><input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
".ucfirst(mex("disponibilità dal",$pag))." ";
$inizio_select = "";
$fine_select = "";
if ($numconnessione) {
$oggi = date("Y-m-d",(time() + (C_DIFF_ORE * 3600)));
$date_select = esegui_query("select datainizio,datafine from $PHPR_TAB_PRE"."periodi$anno where datainizio <= '$oggi' and datafine > '$oggi' ");
if (numlin_query($date_select) != 0) {
$inizio_select = risul_query($date_select,0,'datainizio');
$fine_select = risul_query($date_select,0,'datafine');
} # fine if (numlin_query($date_select) != 0)
} # fine if ($numconnessione)
mostra_menu_date(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php","inizioperiodo",$inizio_select,"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php","fineperiodo",$fine_select,"","",$id_utente,$tema);
echo " (".mex("per",$pag)."
 <input type=\"text\" name=\"numpersone\" size=\"2\" maxlength=\"2\">
 ".mex("persone",$pag).")
<button class=\"chav\" type=\"submit\"><div>".ucfirst(mex("controlla",$pag))."</div></button>
</div></form></div></td></tr></table>";
} # fine if ($priv_ins_nuove_prenota == "s" or $priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n")

echo "<hr id=\"mm_sub5\" style=\"width: 95%;\">";


if ($numconnessione and $priv_vedi_tab_prenotazioni != "n") {
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableclienti = $PHPR_TAB_PRE."clienti";
$tableanni = $PHPR_TAB_PRE."anni";

if ($id_utente == 1) {
if ($canc_mess_periodi) {
$tabelle_lock = array($tablepersonalizza);
$altre_tab_lock = array($tableanni,$tableperiodi);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
} # fine if ($canc_mess_periodi)
else {
$tabelle_lock = "";
$altre_tab_lock = array($tableanni,$tableperiodi,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
} # fine else if ($canc_mess_periodi)
$mesi_avviso = 4;
$limite_avviso_date = date("Y-m-d",mktime(0,0,0,((int) date("m") + $mesi_avviso),date("d"),date("Y")));
$limite_avviso_date = esegui_query("select * from $tableperiodi where datafine > '$limite_avviso_date' limit 1 ");
if (!numlin_query($limite_avviso_date)) {
$anni_succ = esegui_query("select * from $tableanni where idanni > '$anno' ");
if (!numlin_query($anni_succ)) {
$visto = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'visto_messaggio_periodi' and idutente = '$id_utente' ");
if (!numlin_query($visto)) {
if ($canc_mess_periodi) esegui_query("insert into $tablepersonalizza (idpersonalizza,valpersonalizza,idutente) values ('visto_messaggio_periodi','1','$id_utente')");
else {
echo "<b class=\"colwarn\">".mex("Avviso",$pag)."</b>: ".mex("nel database sono ancora disponibili periodi solo per meno di",$pag)." $mesi_avviso ".mex("mesi",$pag).".
<table style=\"margin-left: auto; margin-right: auto; margin-top: 6px;\"><tr><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"canc_mess_periodi\" value=\"1\">
<button class=\"canc\" type=\"submit\"><div>".mex("OK, ho capito",$pag)."</div></button>
</div></form></td><td style=\"width: 10px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"./visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"periodi\">
<input type=\"hidden\" name=\"mostra_form_agg_per\" value=\"1\">
<input type=\"hidden\" name=\"origine\" value=\"./creaprezzi.php\">
<button class=\"amon\" type=\"submit\"><div>".mex("Aggiungi periodi","visualizza_tabelle.php")."</div></button>
</div></form></td></tr></table>
<div class=\"mm_sub7\"></div><hr style=\"width: 95%;\">";
} # fine else if ($canc_mess_periodi) 
} # fine if (!numlin_query($visto))
} # fine if (!numlin_query($anni_succ))
} # fine if (!numlin_query($limite_avviso_date))
unlock_tabelle($tabelle_lock);
} # fine if ($id_utente == 1)

$attiva_checkin = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'attiva_checkin' and idutente = '$id_utente'");
$attiva_checkin = risul_query($attiva_checkin,0,'valpersonalizza');
if ($attiva_checkin == "SI") {
$tabelle_lock = "";
$altre_tab_lock = array($tableprenota,$tableperiodi,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$stile_soldi = stile_soldi();
if ($priv_vedi_tab_prenotazioni == "p" or $priv_vedi_tab_prenotazioni == "g") {
$condizione_prenota_proprie = "and ( utente_inserimento = '$id_utente'";
if ($priv_vedi_tab_prenotazioni == "g") {
reset($utenti_gruppi);
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_prenota_proprie .= " or utente_inserimento = '$idut_gr'";
} # fine if ($priv_vedi_tab_prenotazioni == "g")
$condizione_prenota_proprie .= " )";
} # fine if ($priv_vedi_tab_prenotazioni == "p" or $priv_vedi_tab_prenotazioni == "g")
else $condizione_prenota_proprie = "";
$id_periodo_corrente = calcola_id_periodo_corrente($anno,"");
$prenotazioni = esegui_query("select * from $tableprenota where ((iddatainizio <= '$id_periodo_corrente' and checkin is NULL) or (iddatafine < '$id_periodo_corrente' and checkout is NULL)) and idclienti != '0' $condizione_prenota_proprie order by checkin desc, iddatainizio, iddatafine");
$num_prenotazioni = numlin_query($prenotazioni);
$data_inizio_assoluta = esegui_query("select datainizio from $tableperiodi where idperiodi = 1");
$data_inizio_assoluta = risul_query($data_inizio_assoluta,0,'datainizio');
unlock_tabelle($tabelle_lock);

if ($num_prenotazioni > 0) {
if (!function_exists('dati_costi_agg_prenota')) include("./includes/funzioni_costi_agg.php");
$colori_tab_mesi = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'colori_tab_mesi' and idutente = '$id_utente'");
$colori_tab_mesi = explode(",",risul_query($colori_tab_mesi,0,'valpersonalizza'));
$colore_giallo = $colori_tab_mesi[1];
$colore_arancione = $colori_tab_mesi[2];
$colore_rosso = $colori_tab_mesi[3];
$fr_Appartamento = mex("Appartamento",'unit.php');
$stile_data = stile_data();
if (strlen($fr_Appartamento) > 4) $fr_Appartamento = substr($fr_Appartamento,0,3).".";
if ($mobile_device) {
$class_opt = " class=\"opt\"";
$class_opt2 = " class=\"opt2\"";
} # fine if ($mobile_device)
else {
$class_opt = "";
$class_opt2 = "";
} # fine else if ($mobile_device)
echo "<div class=\"tab_cont\">
<table class=\"t1 ckin\" style=\"background-color: $t1color; margin-left: auto; margin-right: auto;\" width=780 border=\"$t1border\" cellspacing=\"$t1cellspacing\">
<tr><td><span class=\"wsnw\">N&deg;</span></td>
<td><small>".str_replace(" ","&nbsp;",mex("Cognome del cliente",$pag))."</small></td>
<td>".str_replace(" ","&nbsp;",mex("Data iniziale",$pag));
if (!$mobile_device) echo "</td><td>";
else echo "&nbsp;/&nbsp;<span class=\"smlscr\"> </span>";
echo str_replace(" ","&nbsp;",mex("Data finale",$pag))."</td>
<td$class_opt><small><small>".mex("Tariffa completa",$pag)."</small></small></td>
<td><small><small>".str_replace(" ","&nbsp;",mex("Da pagare",$pag))."</small></small></td>
<td><small><small>$fr_Appartamento</small></small></td>
<td><small><small>".mex("Pers",$pag).".</small></small></td>
<td$class_opt2><small><small>".mex("Promemoria",$pag)."</small></small></td>
<td colspan=2><span>".mex("Registra",$pag)."</span></td></tr>";

for ($num1 = 0 ; $num1 < $num_prenotazioni ; $num1++) {
$utente_inserimento_prenota = risul_query($prenotazioni,$num1,'utente_inserimento');
$numero = risul_query($prenotazioni,$num1,'idprenota');
$appartamento = risul_query($prenotazioni,$num1,'idappartamenti');
$id_clienti = risul_query($prenotazioni,$num1,'idclienti');
$cognome = esegui_query("select cognome,utente_inserimento from $tableclienti where idclienti = $id_clienti");
$mostra_cliente = "SI";
if ($vedi_clienti == "NO") $mostra_cliente = "NO";
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$utente_inserimento = risul_query($cognome,0,'utente_inserimento');
if ($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) $mostra_cliente = "NO";
if ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento]) $mostra_cliente = "NO";
} # fine if ($vedi_clienti == "PROPRI" or...
if ($mostra_cliente == "NO") $cognome = mex("Cliente",$pag)." $id_clienti";
else $cognome = risul_query($cognome,0,'cognome');

$id_data_inizio = risul_query($prenotazioni,$num1,'iddatainizio');
if ($id_data_inizio == 0) { $data_inizio = "<".$data_inizio_assoluta; }
else {
$data_inizio = esegui_query("select * from $tableperiodi where idperiodi = $id_data_inizio");
$data_inizio = risul_query($data_inizio,0,'datainizio');
$data_inizio_f = formatta_data($data_inizio,$stile_data);
} # fine else if ($id_data_inizio == 0)
$id_data_fine = risul_query($prenotazioni,$num1,'iddatafine');
$data_fine = esegui_query("select * from $tableperiodi where idperiodi = $id_data_fine");
$data_fine = risul_query($data_fine,0,'datafine');
$data_fine_f = formatta_data($data_fine,$stile_data);
$mese = explode("-",$data_inizio);
$mese = $mese[1];

$num_persone = risul_query($prenotazioni,$num1,'num_persone');
if (!$num_persone or $num_persone == 0) { $num_persone = "?"; }
$n_letti_agg = 0;
$dati_cap = dati_costi_agg_prenota($tablecostiprenota,$numero);
unset($num_letti_agg);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) aggiorna_letti_agg_in_periodi($dati_cap,$numca,$num_letti_agg,$d_id_data_inizio,$d_id_data_fine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],"","");
$n_letti_agg = $num_letti_agg['max'];

$caparra = risul_query($prenotazioni,$num1,'caparra');
if (!$caparra) $caparra = 0;
$pagato = risul_query($prenotazioni,$num1,'pagato');
if (!$pagato) $pagato = 0;
$pagato_p = punti_in_num($pagato,$stile_soldi);
$costo_tot = risul_query($prenotazioni,$num1,'tariffa_tot');
if (!$costo_tot) $costo_tot = 0;
$costo_tot_p = punti_in_num($costo_tot,$stile_soldi);
$da_pagare = $costo_tot - $pagato;
$da_pagare_p = punti_in_num($da_pagare,$stile_soldi);
$confermato = risul_query($prenotazioni,$num1,'conferma');
$confermato = substr($confermato,0,1);
$colore = "";
if ($pagato < $costo_tot) {
$colore = $colore_giallo; #giallo
if ($pagato < $caparra) $colore = $colore_arancione; #arancione
if ($confermato != "S") $colore = $colore_rosso; # rosso
} # fine if ($pagato < $costo_tot)

$link_modifica = "SI";
if ($priv_mod_prenotazioni == "n") $link_modifica = "NO";
if ($priv_mod_prenotazioni == "p" and $utente_inserimento_prenota != $id_utente) $link_modifica = "NO";
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento_prenota]) $link_modifica = "NO";
if ($priv_mod_prenota_iniziate != "s" and $id_periodo_corrente >= $id_data_inizio) $link_modifica = "NO";
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = risul_query($prenotazioni,$num1,'datainserimento');
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $link_modifica = "NO";
} # fine if ($priv_mod_prenota_ore != "000")
$checkin = risul_query($prenotazioni,$num1,'checkin');
$checkout = risul_query($prenotazioni,$num1,'checkout');
if ($checkout and !$checkin) {
$stima_checkin = substr(str_replace(" ","&nbsp;",str_replace("$data_inizio_f ","",formatta_data($checkout))),0,-3);
if (strlen($stima_checkin) < 10) $stima_checkin = "&nbsp;<small><small>($stima_checkin)</small></small>";
else $stima_checkin = " <small><small>($stima_checkin)</small></small>";
$checkout = "";
} # fine if ($checkout and !$checkin)
else $stima_checkin = "";
$promemoria = "&nbsp;";
$commento = risul_query($prenotazioni,$num1,'commento');
if (strstr($commento,">")) {
$commento = explode(">",$commento);
if (!$checkin and strcmp($commento[1],"")) $promemoria = "<small><small>".$commento[1]."</small></small>";
if ($checkin and !$checkout and strcmp($commento[2],"")) $promemoria = "<small><small>".$commento[2]."</small></small>";
} # fine if (strstr($commento,">"))
$data_inserimento = risul_query($prenotazioni,$num1,'datainserimento');
$host_inserimento = risul_query($prenotazioni,$num1,'hostinserimento');

if ($link_modifica == "SI") {
echo "<tr><td><a href=\"modifica_prenota.php?id_prenota=$numero&amp;anno=$anno&amp;id_sessione=$id_sessione&amp;origine=inizio.php\">$numero</a></td>";
} # fine if ($link_modifica == "SI")
else echo "<tr><td>$numero</td>";
echo "<td>$cognome</td>
<td>$data_inizio_f"."$stima_checkin";
if (!$mobile_device) echo "</td><td>";
else echo "&nbsp;/&nbsp;<span class=\"smlscr\"> </span>";
echo "$data_fine_f</td>
<td$class_opt>$costo_tot_p</td>
<td";
if ($colore) echo " style=\"background-color: $colore;\"";
echo ">$da_pagare_p</td>";
if (strlen($appartamento) > 6) echo "<td><small>$appartamento</small></td>";
else echo "<td>$appartamento</td>";
echo "<td>$num_persone";
if ($n_letti_agg != 0) { echo "+$n_letti_agg"; }
echo "</td>
<td$class_opt2>$promemoria</td>";

if ($link_modifica == "SI" and $priv_mod_checkin == "s") {
echo "<td valign=\"middle\"";
if (!$colore or $priv_mod_pagato != "s") echo " colspan=2";
echo ">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"inizio.php\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$numero\">";
if (!$checkin) echo "<input class=\"sbutton\" type=\"submit\" name=\"ins_checkin\" value=\"".mex("Entrata",$pag)."\">";
if ($checkin and !$checkout) echo "<input id=\"cobutton\" class=\"sbutton\" type=\"submit\" name=\"ins_checkout\" value=\"".mex("Uscita",$pag)."\">";
echo "</div></form></td>";
if ($colore and $priv_mod_pagato == "s") {
echo "<td$class_opt valign=\"middle\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"inizio.php\">
<input type=\"hidden\" name=\"modificaprenotazione\" value=\"SI\">
<input type=\"hidden\" name=\"modo_aggiorna_pagato\" value=\"tutto\">
<input type=\"hidden\" name=\"non_modificare_costi_agg\" value=\"SI\">
<input type=\"hidden\" name=\"d_data_inserimento\" value=\"$data_inserimento\">
<input type=\"hidden\" name=\"d_host_inserimento\" value=\"$host_inserimento\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$numero\">";
if (!$checkin) echo "<input class=\"sbutton\" type=\"submit\" name=\"ins_checkin\" value=\"".mex("Entrata e pagato",$pag)."\">";
if ($checkin and !$checkout) echo "<input id=\"copbutton\" class=\"sbutton\" type=\"submit\" name=\"ins_checkout\" value=\"".mex("Uscita e pagato",$pag)."\">";
echo "</div></form></td>";
} # fine if ($colore and...
} # fine if ($link_modifica == "SI" and $priv_mod_checkin == "s")
else {
if (!$checkin) echo "<td colspan=2>".mex("Entrata",$pag)."</td>";
if ($checkin and !$checkout)  echo "<td colspan=2>".mex("Uscita",$pag)."</td>";
} # fine else if ($link_modifica == "SI" and $priv_mod_checkin == "s")
echo "</tr>";

} # fine for $num1
echo "</table></div>
<hr id=\"mm_sub6\" style=\"width: 95%;\">";
} # fine if ($num_prenotazioni > 0)
} # fine if ($attiva_checkin == "SI")
} # fine if ($numconnessione and $priv_vedi_tab_prenotazioni != "n")


if ($priv_mod_tariffe != "n" or $priv_ins_costi_agg != "n") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"creaprezzi.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"ipri\" type=\"submit\"><div>".mex("Inserisci o modifica i prezzi",$pag)."</div></button>
</div></form><div class=\"mm_sub7\"></div>";
} # fine if ($priv_mod_tariffe != "n" or $priv_ins_costi_agg != "n")
if ($id_utente == 1 and $installazione_subordinata != "SI") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"crearegole.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"irul\" type=\"submit\"><div>".mex("Inserisci o modifica le regole di assegnazione",$pag)."</div></button><br>
</div></form><div class=\"mm_sub7\"></div>";
} # fine if ($id_utente == 1 and $installazione_subordinata != "SI")
if ($modifica_pers != "NO" or $priv_crea_backup == "s" or $priv_crea_interconnessioni == "s" or $priv_gest_pass_cc == "s") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"conf\" type=\"submit\"><div>".mex("Configura e personalizza",$pag)."</div></button><br>
</div></form><div class=\"mm_sub7\"></div>";
} # fine if ($modifica_pers != "NO" or $priv_crea_backup == "s" or $priv_crea_interconnessioni == "s" or $priv_gest_pass_cc == "s")
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
".ucfirst(mex("richiedi l'anno",$pag))."
<input type=\"text\" name=\"anno\" size=\"4\" maxlength=\"4\">
<button class=\"gooo\" type=\"submit\"><div>".mex("vai",$pag)."</div></button>
</div></form><br></div>

</div>";

} # fine if (!$hide_default_menu)

# You are not authorized to remove the following copyright notice. Ask for permission info@digitaldruid.net
if (!$numconnessione and (!defined('C_MOSTRA_COPYRIGHT') or C_MOSTRA_COPYRIGHT != "NO")) {
echo "</td></tr>
<tr><td style=\"background-color: #ffffff; height: 57px; color: #000000; font-size: 11px; text-align: center; vertical-align: bottom;\">
Website <a style=\"color: #000000;\" href=\"./mostra_sorgente.php\">engine code</a> is copyright © by DigitalDruid.Net.
<a style=\"color: #000000;\" href=\"http://www.hoteldruid.com\">HotelDruid</a> is a free software released under the GNU/AGPL.
</td></tr></table>";
} # fine if (!$numconnessione and (!defined('C_MOSTRA_COPYRIGHT') or C_MOSTRA_COPYRIGHT != "NO"))

if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");




} # fine if ($anno_esistente == "SI")



else {
# Non esiste l'anno richiesto

$show_bar = "NO";
if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
echo "$form_aggiorna_sub";

if (controlla_anno($anno) == "NO" or $id_utente != 1 or $installazione_subordinata == "SI" or (defined('C_CREA_ANNO_NON_ATTUALE') and C_CREA_ANNO_NON_ATTUALE == "NO" and $anno != $anno_corrente)) {
if (controlla_anno($anno) == "SI" and $id_utente != 1) echo mex("Questo utente non ha i privilegi per creare nuovi anni",$pag).".<br>";
else echo mex("Il formato dell'anno richiesto è sbagliato",$pag).".<br>";
} # fine if (controlla_anno($anno) == "NO" or $id_utente != 1 or $installazione_subordinata == "SI" or...
else {

echo "<br> ".mex("Non esiste l'anno ",$pag).$anno.mex(" nel database",$pag).". <br>";
$anno_attuale = date("Y",(time() + (C_DIFF_ORE * 3600) - (C_GIORNI_NUOVO_ANNO * 86400)));
if ($anno > $anno_attuale and @is_file(C_DATI_PATH."/selectperiodi$anno_attuale.1.php")) {
$data_crea_anno = formatta_data(date("Y-m-d",mktime(0,0,0,1,(C_GIORNI_NUOVO_ANNO + 1),($anno_attuale + 1))),$stile_data);
echo "<br>".mex("<span class=\"colred\">Avviso</span>: è consigliabile attendere fino al",$pag)." $data_crea_anno ".mex("per creare il nuovo anno, nel frattempo si possono aggiungere periodi oltre il",$pag)." $anno_attuale ".mex("dalla",$pag)."
 <a href=\"./visualizza_tabelle.php?anno=$anno_attuale&amp;id_sessione=$id_sessione&amp;tipo_tabella=periodi#agg_per\">".mex("tabella con i periodi e le tariffe",$pag)."</a> ".mex("anche senza creare un nuovo anno",$pag).".<br>";
} # fine if ($anno > $anno_attuale and @is_file(C_DATI_PATH."/selectperiodi$anno_attuale.1.php"))
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"creaanno.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"nuovo_mess\" value=\"$nuovo_mess\">
<input class=\"sbutton\" type=\"submit\" name=\"creaanno\" value=\"".mex("Crea l'anno",$pag)." $anno \">
 ".mex("con periodi",$pag).":<br>";
unset($tipo_periodi_obbligati);
unset($checked_g);
unset($checked_s);
if (!$numconnessione) {
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
} # fine if (!$numconnessione)
$tableanni = $PHPR_TAB_PRE."anni";
$tipo_periodi_esistenti = esegui_query("select * from $tableanni order by idanni desc");
if (numlin_query($tipo_periodi_esistenti) != 0) $tipo_periodi_prec = risul_query($tipo_periodi_esistenti,0,'tipo_periodi');
if ($tipo_periodi_prec == "s") $checked_s = " checked";
else $checked_g = " checked";
if (defined('C_CAMBIA_TIPO_PERIODI') and C_CAMBIA_TIPO_PERIODI == "NO") $tipo_periodi_obbligati = $tipo_periodi_prec;
if (!$tipo_periodi_obbligati or $tipo_periodi_obbligati == "s") {
echo "<label><input type=\"radio\" name=\"tipo_periodi\" value=\"s\"$checked_s>
".mex("settimanali",$pag)."</label> (<em>".mex("obsoleti",$pag)."</em>): 
 <select name=\"giorno_ini_fine\">
<option value=\"0\">".mex("Domenica",$pag)."</option>
<option value=\"1\">".mex("Lunedì",$pag)."</option>
<option value=\"2\">".mex("Martedì",$pag)."</option>
<option value=\"3\">".mex("Mercoledì",$pag)."</option>
<option value=\"4\">".mex("Giovedì",$pag)."</option>
<option value=\"5\">".mex("Venerdì",$pag)."</option>
<option value=\"6\" selected>".mex("Sabato",$pag)."</option>
</select> ".mex("come giorno di inizio/fine locazione",$pag)."<br>";
} # fine if (!$tipo_periodi_obbligati or $tipo_periodi_obbligati == "s")
if (!$tipo_periodi_obbligati or $tipo_periodi_obbligati == "g") {
echo "<label><input type=\"radio\" name=\"tipo_periodi\" value=\"g\"$checked_g>
".mex("giornalieri",$pag)."</label><br>";
} # fine if (!$tipo_periodi_obbligati or $tipo_periodi_obbligati == "g")
$sel_12 = "";
$sel_24 = "";
if (date("n") > 8) $sel_24 = " selected";
else $sel_12 = " selected";
echo "".mex("e prenotazioni da",$pag)."
 <select name=\"mese_ini\">
<option value=\"1\" selected>".mex("Gennaio",$pag)."</option>
<option value=\"2\">".mex("Febbraio",$pag)."</option>
<option value=\"3\">".mex("Marzo",$pag)."</option>
<option value=\"4\">".mex("Aprile",$pag)."</option>
<option value=\"5\">".mex("Maggio",$pag)."</option>
<option value=\"6\">".mex("Giugno",$pag)."</option>
<option value=\"7\">".mex("Luglio",$pag)."</option>
<option value=\"8\">".mex("Agosto",$pag)."</option>
<option value=\"9\">".mex("Settembre",$pag)."</option>
<option value=\"10\">".mex("Ottobre",$pag)."</option>
<option value=\"11\">".mex("Novembre",$pag)."</option>
<option value=\"12\">".mex("Dicembre",$pag)."</option>
</select> ".mex("a",$pag)."
 <select name=\"mese_fine\">
<option value=\"1\">".mex("Gennaio",$pag)."</option>
<option value=\"2\">".mex("Febbraio",$pag)."</option>
<option value=\"3\">".mex("Marzo",$pag)."</option>
<option value=\"4\">".mex("Aprile",$pag)."</option>
<option value=\"5\">".mex("Maggio",$pag)."</option>
<option value=\"6\">".mex("Giugno",$pag)."</option>
<option value=\"7\">".mex("Luglio",$pag)."</option>
<option value=\"8\">".mex("Agosto",$pag)."</option>
<option value=\"9\">".mex("Settembre",$pag)."</option>
<option value=\"10\">".mex("Ottobre",$pag)."</option>
<option value=\"11\">".mex("Novembre",$pag)."</option>
<option value=\"12\"$sel_12>".mex("Dicembre",$pag)."</option>
<option value=\"13\">".mex("Gen",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"14\">".mex("Feb",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"15\">".mex("Mar",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"16\">".mex("Apr",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"17\">".mex("Mag",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"18\">".mex("Giu",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"19\">".mex("Lug",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"20\">".mex("Ago",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"21\">".mex("Set",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"22\">".mex("Ott",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"23\">".mex("Nov",$pag).". ".mex("anno successivo",$pag)."</option>
<option value=\"24\"$sel_24>".mex("Dic",$pag).". ".mex("anno successivo",$pag)."</option>";
$n_mese[1] = mex("Gen",$pag);
$n_mese[2] = mex("Feb",$pag);
$n_mese[3] = mex("Mar",$pag);
$n_mese[4] = mex("Apr",$pag);
$n_mese[5] = mex("Mag",$pag);
$n_mese[6] = mex("Giu",$pag);
$n_mese[7] = mex("Lug",$pag);
$n_mese[8] = mex("Ago",$pag);
$n_mese[9] = mex("Set",$pag);
$n_mese[10] = mex("Ott",$pag);
$n_mese[11] = mex("Nov",$pag);
$n_mese[12] = mex("Dic",$pag);
for ($num1 = 2 ; $num1 <= 3 ; $num1++) {
for ($num2 = 1 ; $num2 <= 12 ; $num2++) {
echo "<option value=\"".(($num1*12) + $num2)."\">".$n_mese[$num2].". $num1 ".mex("anni successivi",$pag)."</option>";
} # fine for $num2
} # fine for $num1
echo "</select>.<br>";
$anno_prec = $anno -1;
if (@is_file(C_DATI_PATH."/selectperiodi$anno_prec.1.php")) {
echo "<label><input type=\"checkbox\" name=\"importa_anno_prec\" value=\"SI\" checked>
".mex("Importa dall'anno precedente prenotazioni, tariffe (compresi costi aggiuntivi), privilegi degli utenti e regole d'assegnazione.",$pag)."</label>";
} # fine if (@is_file(C_DATI_PATH."/selectperiodi$anno_prec.1.php"))
echo "</div></form>";

} # fine else if (controlla_anno($anno) == "NO" or $id_utente != 1 or...
echo "<br><hr style=\"width: 95%\"><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div>
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<br> ".mex("richiedi l'anno",$pag)."
<input type=\"text\" name=\"anno\" size=\"4\" maxlength=\"4\">
<input class=\"sbutton\" type=\"submit\" name=\"vai\" value=\"".mex("vai",$pag)."\"><br>
</div></form>";
if ($tema[$id_utente] != "base") include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");

} # fine else if ($anno_esistente == "SI")

} # fine else if (@is_file(C_DATI_PATH."/dati_connessione.php") != true)






} # fine if ($id_utente)




?>


