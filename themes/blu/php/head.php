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




# tables background color and dimensions
$t1color = "#b9ccd4";
$t1border = "2";
$t1cellspacing = "0";
$t1cellpadding = "3";
$t2row1color = "#ffffff";
$t2row2color = "#f7f7f7";
$t1dates = "#daedff";
$t1datesout = "#b7dcff";
$t1seldate = "#ffffff";
$t1dropin = "#05e105";
$t1dropout = "#297929";
#$start_time = microtime();


# head
$body_bgcolor = "#dddddd";
if (!$tema_corr) $tema_corr = $tema[$id_utente];
if ($senza_colori == "SI" or ($pag == "visualizza_contratto.php" and $show_bar == "NO")) $body_bgcolor = "#ffffff";
if ($pag == "punto_vendita.php") $iscale = "0.8";
else $iscale = "1.0";
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") $titolo = "";
if (defined('C_FILE_TITOLO_PERS') and C_FILE_TITOLO_PERS != "" and @is_file(C_FILE_TITOLO_PERS)) $titolo = trim(substr(implode("",file(C_FILE_TITOLO_PERS)),0,40))." - $titolo";
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"
        \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" >
<meta name=\"viewport\" content=\"initial-scale=$iscale\">
<title> $titolo </title>";
if ($pag == "visualizza_contratto.php" and $extra_head) echo $extra_head;
if (defined('C_URL_FAVICON') and C_URL_FAVICON != "" and @is_file(C_URL_FAVICON)) echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"".C_URL_FAVICON."\">
";
elseif (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA != "SI") echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"./img/favicon.ico\">
";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./base.css$vers_hinc\" media=\"all\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"./themes/$tema_corr/inc/stylesheet.css$vers_hinc\" media=\"all\">
";
if (!$mobile_device) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./themes/$tema_corr/inc/screen.css$vers_hinc\" media=\"screen\">
";
else echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./themes/$tema_corr/inc/mobile.css$vers_hinc\" media=\"screen\">
";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./themes/$tema_corr/inc/paper.css$vers_hinc\" media=\"print\">
<script type=\"text/javascript\" src=\"./themes/$tema_corr/inc/functions.js$vers_hinc\">
</script>
";
if ($base_js) echo "<script type=\"text/javascript\" src=\"./base.js$vers_hinc\">
</script>
";
if ($show_bar == "NO") echo "<style type=\"text/css\">
html { overflow-y: auto; }
#contentbox { overflow: visible; width:99%; height: 98%; margin-right: auto; margin-left: auto; padding-top: 4px; }
</style>
";
if ($start_time) echo "<style type=\"text/css\">#contentbox { height: 95%; }</style>
";
if (mex("insert-ddw","head.php") != "100px") echo "<style type=\"text/css\">.nb_ins ul ul a, .nb_ins ul ul a:visited, .nb_ins ul ul a:hover { min-width: ".mex("insert-ddw","head.php")."; }</style>
";
if (mex("tables-ddw","head.php") != "100px") echo "<style type=\"text/css\">.nb_tab ul ul a, .nb_tab ul ul a:visited, .nb_tab ul ul a:hover { min-width: ".mex("tables-ddw","head.php")."; }</style>
";
if (mex("months-ddw","head.php") != "86px") echo "<style type=\"text/css\">.nb_m0 ul ul a, .nb_m0 ul ul a:visited, .nb_m0 ul ul a:hover { min-width: ".mex("months-ddw","head.php")."; }</style>
";
if (mex("reservations-ddw","head.php") != "150px") echo "<style type=\"text/css\">.nb_res ul ul a, .nb_res ul ul a:visited, .nb_res ul ul a:hover, .nb_res ul ul div { min-width: ".mex("reservations-ddw","head.php")."; }</style>
";
if (mex("clients-ddw","head.php") != "15em") echo "<style type=\"text/css\">.fdrop { min-width: ".mex("clients-ddw","head.php")."; }</style>
";
if (mex("rates-ddw","head.php") != "150px") echo "<style type=\"text/css\">.nb_rat ul ul a, .nb_rat ul ul a:visited, .nb_rat ul ul a:hover { min-width: ".mex("rates-ddw","head.php")."; }</style>
";
if (mex("configure-ddw","head.php") != "86px") echo "<style type=\"text/css\">.nb_con ul ul a, .nb_con ul ul a:visited, .nb_con ul ul a:hover { min-width: ".mex("configure-ddw","head.php")."; }</style>
";
if (defined('C_FILE_CSS_PERS') and C_FILE_CSS_PERS != "" and @is_file(C_FILE_CSS_PERS)) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".C_FILE_CSS_PERS."\" media=\"all\">
";
if ($mobile_device and defined('C_FILE_MOB_CSS_PERS') and C_FILE_MOB_CSS_PERS != "" and @is_file(C_FILE_MOB_CSS_PERS)) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".C_FILE_MOB_CSS_PERS."\" media=\"all\">
";
echo "</head>
<body";
if ($drag_drop) echo " ondragover=\"event.preventDefault();\" ondragenter=\"event.preventDefault();\" ondrop=\"event.preventDefault();drp_out();\"";
echo " style=\"background-color: $body_bgcolor;\">";

if ($pag != "visualizza_contratto.php" or $show_bar != "NO") echo "<div id=\"menubox\">";




if ($show_bar != "NO") {


if ($id_utente != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
if (!$anno_utente_attivato) {
if (!$privilegi_annuali_utente) $privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else $anno_utente_attivato = "SI";
} # fine if (!$anno_utente_attivato)
if ($anno_utente_attivato == "SI") {
if (!$privilegi_globali_utente) $privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
if (!$priv_mod_pers) $priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s" and !$modifica_pers) $modifica_pers = "NO";
if (!$priv_crea_backup) $priv_crea_backup = substr($priv_mod_pers,1,1);
if (!$priv_crea_interconnessioni) $priv_crea_interconnessioni = substr($priv_mod_pers,3,1);
if (!$priv_gest_pass_cc) $priv_gest_pass_cc = substr($priv_mod_pers,5,1);
if (!$priv_ins_clienti) $priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
if (substr($priv_ins_clienti,0,1) != "s" and !$inserimento_nuovi_clienti) $inserimento_nuovi_clienti = "NO";
if (substr($priv_ins_clienti,1,1) != "s" and substr($priv_ins_clienti,1,1) != "p" and !$modifica_clienti) $modifica_clienti = "NO";
if (substr($priv_ins_clienti,2,1) != "s" and substr($priv_ins_clienti,2,1) != "p" and !$vedi_clienti) $vedi_clienti = "NO";
if (!$priv_vedi_messaggi) {
$priv_messaggi = risul_query($privilegi_globali_utente,0,'priv_messaggi');
$priv_vedi_messaggi = substr($priv_messaggi,0,1);
} # fine if (!$priv_vedi_messaggi)
if (!$priv_inventario) $priv_inventario = risul_query($privilegi_globali_utente,0,'priv_inventario');
if (!$priv_vedi_beni_inv) $priv_vedi_beni_inv = substr($priv_inventario,0,1);
if (!$priv_vedi_inv_mag) $priv_vedi_inv_mag = substr($priv_inventario,2,1);
if (!$priv_vedi_inv_app) $priv_vedi_inv_app = substr($priv_inventario,6,1);
if (!$priv_ins_prenota) $priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
if (!$priv_ins_nuove_prenota) $priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
if (!$priv_ins_costi) $priv_ins_costi = risul_query($privilegi_annuali_utente,0,'priv_ins_costi');
if (!$priv_ins_spese) $priv_ins_spese = substr($priv_ins_costi,0,1);
if (!$priv_ins_entrate) $priv_ins_entrate = substr($priv_ins_costi,1,1);
if (!$priv_mod_prenota) $priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
if (!$priv_mod_prenotazioni) $priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if (!$priv_mod_costi_agg) $priv_mod_costi_agg = substr($priv_mod_prenota,8,1);
if (!$priv_vedi_tab) $priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
if (!$priv_vedi_tab_mesi) $priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
if (!$priv_vedi_tab_prenotazioni) $priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
if (!$priv_vedi_tab_costi) $priv_vedi_tab_costi = substr($priv_vedi_tab,2,1);
if (!$priv_vedi_tab_periodi) $priv_vedi_tab_periodi = substr($priv_vedi_tab,3,1);
if (!$priv_vedi_tab_regole) $priv_vedi_tab_regole = substr($priv_vedi_tab,4,1);
if (!$priv_vedi_tab_appartamenti) $priv_vedi_tab_appartamenti = substr($priv_vedi_tab,5,1);
if (!$priv_vedi_tab_stat) $priv_vedi_tab_stat = substr($priv_vedi_tab,6,1);
if (!$priv_vedi_tab_doc) $priv_vedi_tab_doc = substr($priv_vedi_tab,7,1);
if (!$priv_ins_tariffe) $priv_ins_tariffe = risul_query($privilegi_annuali_utente,0,'priv_ins_tariffe');
if (!$priv_mod_tariffe) $priv_mod_tariffe = substr($priv_ins_tariffe,0,1);
if (!$priv_ins_costi_agg) $priv_ins_costi_agg = substr($priv_ins_tariffe,1,1);
} # fine if ($anno_utente_attivato == "SI")
} # fine if ($id_utente != 1)
else {
if (!$anno_utente_attivato) $anno_utente_attivato = "SI";
if (!$modifica_pers) $modifica_pers = "SI";
if (!$priv_crea_backup) $priv_crea_backup = "s";
if (!$priv_crea_interconnessioni) $priv_crea_interconnessioni = "s";
if (!$priv_gest_pass_cc) $priv_gest_pass_cc = "s";
if (!$inserimento_nuovi_clienti) $inserimento_nuovi_clienti = "SI";
if (!$modifica_clienti) $modifica_clienti = "SI";
if (!$vedi_clienti) $vedi_clienti = "SI";
if (!$priv_vedi_messaggi) $priv_vedi_messaggi = "s";
if (!$priv_vedi_beni_inv) $priv_vedi_beni_inv = "s";
if (!$priv_vedi_inv_mag) $priv_vedi_inv_mag = "s";
if (!$priv_vedi_inv_app) $priv_vedi_inv_app = "s";
if (!$priv_ins_nuove_prenota) $priv_ins_nuove_prenota = "s";
if (!$priv_ins_spese) $priv_ins_spese = "s";
if (!$priv_ins_entrate) $priv_ins_entrate = "s";
if (!$priv_mod_prenotazioni) $priv_mod_prenotazioni = "s";
if (!$priv_mod_costi_agg) $priv_mod_costi_agg = "s";
if (!$priv_vedi_tab_mesi) $priv_vedi_tab_mesi = "s";
if (!$priv_vedi_tab_prenotazioni) $priv_vedi_tab_prenotazioni = "s";
if (!$priv_vedi_tab_costi) $priv_vedi_tab_costi = "s";
if (!$priv_vedi_tab_periodi) $priv_vedi_tab_periodi = "s";
if (!$priv_vedi_tab_regole) $priv_vedi_tab_regole = "s";
if (!$priv_vedi_tab_appartamenti) $priv_vedi_tab_appartamenti = "s";
if (!$priv_vedi_tab_stat) $priv_vedi_tab_stat = "s";
if (!$priv_vedi_tab_doc) $priv_vedi_tab_doc = "s";
if (!$priv_mod_tariffe) $priv_mod_tariffe = "s";
if (!$priv_ins_costi_agg) $priv_ins_costi_agg = "s";
} # fine else if ($id_utente != 1)

if ($anno_utente_attivato == "SI") {

$mese_attuale = date("n",(time() + (C_DIFF_ORE * 3600)));
function nome_mese_menu ($mese_attuale) {
if ($mese_attuale > 12) $mese_attuale = $mese_attuale - 12;
if ($mese_attuale == 1) $MESE_ATT = "GENNAIO";
if ($mese_attuale == 2) $MESE_ATT = "FEBBRAIO";
if ($mese_attuale == 3) $MESE_ATT = "MARZO";
if ($mese_attuale == 4) $MESE_ATT = "APRILE";
if ($mese_attuale == 5) $MESE_ATT = "MAGGIO";
if ($mese_attuale == 6) $MESE_ATT = "GIUGNO";
if ($mese_attuale == 7) $MESE_ATT = "LUGLIO";
if ($mese_attuale == 8) $MESE_ATT = "AGOSTO";
if ($mese_attuale == 9) $MESE_ATT = "SETTEMBRE";
if ($mese_attuale == 10) $MESE_ATT = "OTTOBRE";
if ($mese_attuale == 11) $MESE_ATT = "NOVEMBRE";
if ($mese_attuale == 12) $MESE_ATT = "DICEMBRE";
return $MESE_ATT;
} # fine function nome_mese_menu
function num_mese_menu ($mese_attuale) {
if ($mese_attuale > 12) $mese_attuale = $mese_attuale - 12;
return $mese_attuale;
} # fine function num_mese_menu




if (($pag == "tabella.php" or $pag == "tabella2.php" or $pag == "tabella3.php" or $pag == "visualizza_tabelle.php" or $pag == "storia_soldi.php") and !$mobile_device) $mostra_X = "SI";


echo "<table class=\"nav_bar\" style=\"background-color: $t1color;\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
<tr>";

if ($mostra_X == "SI") echo "<td style=\"width: 10px;\"></td>";
if ($idprenota_origine) $anno = $anno + 1;
if ($id_sessione) {
$sessione_anno_var = "id_sessione=$id_sessione";
if (substr($id_sessione,0,4) != $anno) $sessione_anno_var .= "&amp;anno=$anno";
} # fine if ($id_sessione)
else $sessione_anno_var = "anno=$anno";
if ($mobile_device and strstr($_SERVER['HTTP_USER_AGENT'],'(iP')) $selfref = " href=\"#\"";
else $selfref = "";

echo "<td>
<table class=\"nav\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>
<a class=\"nav\" id=\"nb_men\" href=\"./inizio.php?$sessione_anno_var\">&nbsp;<b>".mex("MENU","head.php")."</b>&nbsp;</a></td><td>";
if ($priv_ins_nuove_prenota == "s" or $priv_ins_spese == "s" or $priv_ins_entrate == "s" or $inserimento_nuovi_clienti != "NO" or ($modifica_clienti != "NO" and $vedi_clienti != "NO")) {
echo "|</td><td><div class=\"drop nb_ins\"><ul>
<li><a class=\"nonav\" id=\"nb_ins\"$selfref>&nbsp;<b>".mex("INSERIRE","head.php").":</b>&nbsp;</a>";
if ($priv_ins_spese == "s" or $priv_ins_entrate == "s" or $priv_mod_tariffe != "n" or $priv_ins_costi_agg != "n" or ($id_utente == 1 and $installazione_subordinata != "SI") or ($priv_mod_prenotazioni != "n" and $priv_mod_costi_agg == "s") or $inserimento_nuovi_clienti != "NO" or ($modifica_clienti != "NO" and $vedi_clienti != "NO") or ($mobile_device and $priv_ins_nuove_prenota == "s")) {
echo "<ul>";
if ($mobile_device and $priv_ins_nuove_prenota == "s") echo "<li><a id=\"nb_ires\" href=\"./prenota.php?$sessione_anno_var\">&nbsp;<b>".mex("PRENOTAZIONE","head.php")."</b>&nbsp;</a></li>";
if ($inserimento_nuovi_clienti != "NO" or ($modifica_clienti != "NO" and $vedi_clienti != "NO")) echo "<li><a id=\"nb_icli\" href=\"./clienti.php?$sessione_anno_var\">&nbsp;<b>".mex("CLIENTE","head.php")."</b>&nbsp;</a></li>";
if ($priv_ins_spese == "s" or $priv_ins_entrate == "s") echo "<li><a id=\"nb_iexp\" href=\"./costi.php?$sessione_anno_var\">&nbsp;<b>".mex("SPESE","head.php")."</b>&nbsp;</a></li>";
if ($priv_mod_tariffe != "n" or $priv_ins_costi_agg != "n") echo "<li><a id=\"nb_ipri\" href=\"./creaprezzi.php?$sessione_anno_var\">&nbsp;<b>".mex("PREZZI","head.php")."</b>&nbsp;</a></li>";
if ($id_utente == 1 and $installazione_subordinata != "SI") echo "<li><a id=\"nb_irul\" href=\"./crearegole.php?$sessione_anno_var\">&nbsp;<b>".mex("REGOLE","head.php")."</b>&nbsp;</a></li>";
if ($priv_mod_prenotazioni != "n" and $priv_mod_costi_agg == "s") echo "<li><a id=\"nb_pos\" href=\"./punto_vendita.php?$sessione_anno_var\">&nbsp;<b>".mex("P.TO&nbsp;VENDITA","head.php")."</b>&nbsp;</a></li>";
if ($mobile_device and $modifica_pers != "NO") echo "<li><a id=\"nb_con\" href=\"./personalizza.php?$sessione_anno_var\">&nbsp;<b>".mex("CONFIGURA","head.php")."</b>&nbsp;</a></li>";
echo "</ul>";
} # fine if ($priv_ins_spese == "s" or $priv_ins_entrate == "s" or $priv_mod_tariffe != "n" or...
echo "</li></ul></div></td><td>";
if ($priv_ins_nuove_prenota == "s" and !$mobile_device) echo "<a class=\"nav\" id=\"nb_ires\" href=\"./prenota.php?$sessione_anno_var\">&nbsp;<b>".mex("PRENOTAZIONE","head.php")."</b>&nbsp;</a></td><td>";
} # fine if ($priv_ins_nuove_prenota == "s" or...
if ($priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n" or $vedi_clienti != "NO" or $priv_vedi_tab_costi != "n" or $priv_vedi_tab_periodi != "n" or $priv_vedi_tab_appartamenti != "n" or $priv_vedi_tab_regole != "n" or $priv_vedi_messaggi == "s") {
if ($priv_vedi_tab_mesi != "n") {
if ($anno_corrente == ($anno + 1)) $mese_attuale = $mese_attuale + 12;
if ($anno < $anno_corrente and @is_file(C_DATI_PATH."/selectperiodi$anno_corrente.1.php")) $mese_attuale = 1;
} # fine if ($priv_vedi_tab_mesi != "n")
if ($priv_vedi_messaggi == "s" and $numconnessione) {
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$messaggi = esegui_query("select idmessaggi from $tablemessaggi where datavisione < '$adesso' and idutenti_visto $LIKE '%,$id_utente,%'");
if (numlin_query($messaggi) > 0) {
$star = "<b style=\"color: red;\">*</b>";
$gt = "<b style=\"color: red; text-decoration: none;\">&gt;</b>";
$lt = "<b style=\"color: red; text-decoration: none;\">&lt;</b>";
} # fine if (numlin_query($messaggi) > 0)
} # fine if ($priv_vedi_messaggi == "s" and $numconnessione)
echo "|</td><td><div class=\"drop nb_tab\"><ul>
<li><a class=\"nonav\" id=\"nb_tab\"$selfref>&nbsp;<b>".mex("TABELLE","head.php")."$star:</b>&nbsp;</a>";
if ($priv_vedi_beni_inv == "n" and $priv_vedi_inv_mag == "n" and $priv_vedi_inv_app == "n") $priv_vedi_tab_inventario = "n";
if ($priv_vedi_tab_costi != "n" or $priv_vedi_tab_regole != "n" or $priv_vedi_tab_doc != "n" or $priv_vedi_tab_stat != "n" or $priv_vedi_tab_inventario != "n" or $priv_vedi_messaggi == "s" or $priv_vedi_tab_appartamenti != "n" or ($mobile_device and ($priv_vedi_tab_mesi != "n" or $vedi_clienti != "NO"))) {
echo "<ul>";
if ($mobile_device) {
if ($priv_vedi_tab_mesi != "n") echo "<li><a id=\"nb_m0\" href=\"./tabella.php?$sessione_anno_var&amp;mese=$mese_attuale\">&nbsp;<b>".mex(nome_mese_menu($mese_attuale),"head.php")."</b>&nbsp;</a></li>";
if ($vedi_clienti != "NO") echo "<li><a id=\"nb_cli\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=clienti\">&nbsp;<b>".mex("CLIENTI","head.php")."</b>&nbsp;</a></li>";
if ($priv_vedi_tab_periodi != "n") echo "<li><a id=\"nb_rat\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=periodi\">&nbsp;<b>".mex("TARIFFE","head.php")."</b>&nbsp;</a></li>";
} # fine if ($mobile_device)
if ($priv_vedi_messaggi == "s") echo "<li><a id=\"nb_mes\" href=\"./messaggi.php?$sessione_anno_var\">&nbsp;$gt<b>".mex("MESSAGGI","head.php")."</b>$lt&nbsp;</a></li>";
if ($priv_vedi_tab_appartamenti != "n") {
$fr_APPARTAMENTI = mex("APPARTAMENTI","unit.php");
if (strlen($fr_APPARTAMENTI) > 12) $fr_APPARTAMENTI = substr($fr_APPARTAMENTI,0,10).".";
echo "<li><a class=\"nav\" id=\"nb_roo\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=appartamenti\">&nbsp;<b>$fr_APPARTAMENTI</b>&nbsp;</a></li>";
} # fine if ($priv_vedi_tab_appartamenti != "n")
if ($priv_vedi_tab_costi != "n") echo "<li><a id=\"nb_cas\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=costi\">&nbsp;<b>".mex("CASSE","head.php")."</b>&nbsp;</a></li>";
if ($priv_vedi_tab_regole != "n") echo "<li><a id=\"nb_rul\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=regole\">&nbsp;<b>".mex("REGOLE","head.php")."</b>&nbsp;</a></li>";
if ($priv_vedi_tab_inventario != "n") echo "<li><a id=\"nb_inv\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=inventario\">&nbsp;<b>".mex("INVENTARIO","head.php")."</b>&nbsp;</a></li>";
if ($priv_vedi_tab_doc != "n") echo "<li><a id=\"nb_doc\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=documenti\">&nbsp;<b>".mex("DOCUMENTI","head.php")."</b>&nbsp;</a></li>";
if ($priv_vedi_tab_stat != "n") echo "<li><a id=\"nb_sta\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=statistiche\">&nbsp;<b>".mex("STATISTICHE","head.php")."</b>&nbsp;</a></li>";
if ($priv_vedi_tab_costi != "n") echo "<li><a id=\"nb_rpa\" href=\"./storia_soldi.php?$sessione_anno_var\">&nbsp;<b>".mex("ENTRATE&nbsp;PREN.","head.php")."</b>&nbsp;</a></li>";
echo "</ul>";
} # fine if ($priv_vedi_tab_costi != "n" or...
echo "</li></ul></div></td><td>";
if ($priv_vedi_tab_mesi != "n" and !$mobile_device) {
echo "</td><td><div class=\"drop nb_m0\"><ul>
<li><a class=\"nav\" id=\"nb_m0\" href=\"./tabella.php?$sessione_anno_var&amp;mese=$mese_attuale\">&nbsp;<b>".mex(nome_mese_menu($mese_attuale),"head.php")."</b>&nbsp;</a><ul>";
for ($num1 = $mese_attuale + 1 ; $num1 < $mese_attuale + 12 ; $num1++) echo "<li><a id=\"nb_m".num_mese_menu($num1)."\" href=\"./tabella.php?$sessione_anno_var&amp;mese=".$num1."\">&nbsp;<b>".mex(nome_mese_menu($num1),"head.php")."</b>&nbsp;</a></li>";
echo "</ul></li></ul></div></td><td>";
} # fine if ($priv_vedi_tab_mesi != "n" and !$mobile_device)
if ($priv_vedi_tab_prenotazioni != "n") {
echo "</td><td><div class=\"drop nb_res\"><ul onmouseover=\"focus_elem('ressearch')\" onmouseout=\"blur_elem('ressearch')\"><li>";
if ($mobile_device) echo "<a class=\"nonav\" id=\"nb_res\"$selfref>";
else echo "<a class=\"nav\" id=\"nb_res\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni\">";
echo "&nbsp;<b>".mex("PRENOTAZIONI","head.php")."</b>&nbsp;</a><ul>
<li><a id=\"nb_rall\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni&amp;sel_tab_prenota=tutte\">&nbsp;<b>".mex("TUTTE","head.php")."</b>&nbsp;</a></li>
<li><a id=\"nb_rcur\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni&amp;sel_tab_prenota=correnti\">&nbsp;<b>".mex("CORRENTI","head.php")."</b>&nbsp;</a></li>
<li><a id=\"nb_rfut\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni&amp;sel_tab_prenota=future\">&nbsp;<b>".mex("FUTURE","head.php")."</b>&nbsp;</a></li>
<li><a id=\"nb_rarr\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni&amp;sel_tab_prenota=correnti&amp;opz_cerc_pren=arr\">&nbsp;<b>".mex("ARRIVI","head.php")."</b>&nbsp;</a></li>
<li><a id=\"nb_rdep\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni&amp;sel_tab_prenota=correnti&amp;opz_cerc_pren=part\">&nbsp;<b>".mex("PARTENZE","head.php")."</b>&nbsp;</a></li>
<li><a id=\"nb_rdec\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni&amp;sel_tab_prenota=partcorr\">&nbsp;<b>".mex("PARTENZE E CORRENTI","head.php")."</b>&nbsp;</a></li>
<li><form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div class=\"lifdrop\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"prenotazioni\">
<input type=\"hidden\" name=\"subtotale_selezionate\" value=\"1\">
<input type=\"hidden\" name=\"num_cambia_pren\" value=\"1\">
<input type=\"hidden\" name=\"cerca_id_passati\" value=\"1\">
<b>&nbsp;".mex("NUMERO","head.php")."</b> <input id=\"ressearch\" type=\"text\" name=\"cambia1\" class=\"smallsel85\" size=\"5\">
<button type=\"submit\"><span>".mex("VAI","head.php")."</span></button>
&nbsp;</div></form></li></ul></li></ul></div></td><td>";
} # fine if ($priv_vedi_tab_prenotazioni != "n")
if ($vedi_clienti != "NO" and !$mobile_device) {
echo "</td><td><div class=\"drop nb_cli\"><ul onmouseover=\"focus_elem('clisearch')\" onmouseout=\"blur_elem('clisearch')\">
<li><a class=\"nav\" id=\"nb_cli\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=clienti\">&nbsp;<b>".mex("CLIENTI","head.php")."</b>&nbsp;</a><ul>
<li><form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div class=\"fdrop\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"clienti\">
<input id=\"clisearch\" type=\"text\" name=\"cognome_cerca\" size=\"8\">
<button type=\"submit\"><span>".mex("CERCA","head.php")."</span></button>
&nbsp;</div></form></li>
</ul></li></ul></div></td><td>";
} # fine if ($vedi_clienti != "NO" and !$mobile_device)
if ($priv_vedi_tab_periodi != "n" and !$mobile_device) {
echo "<div class=\"drop nb_rat\"><div class=\"nb_rat\"><ul>
<li><a class=\"nav\" id=\"nb_rat\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=periodi\">&nbsp;<b>".mex("TARIFFE","head.php")."</b>&nbsp;</a><ul>
<li><a  id=\"nb_exc\"href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=periodi#tab_costi_agg\">&nbsp;<b>".mex("COSTI AGGIUNTIVI","head.php")."</b>&nbsp;</a></li>
<li><a  id=\"nb_tax\"href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=periodi#tab_caparre\">&nbsp;<b>".mex("CAPARRE E TASSE","head.php")."</b>&nbsp;</a></li>
</ul></li></ul></div></div></td><td>";
} # fine if ($priv_vedi_tab_periodi != "n" and !$mobile_device)
} # fine if ($priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n" or...
if (($modifica_pers != "NO" or ($priv_crea_backup == "s" and $installazione_subordinata != "SI") or ($priv_crea_interconnessioni == "s" and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO")) or $priv_gest_pass_cc == "s") and !$mobile_device) {
echo "|</td><td><div class=\"drop nb_con\"><ul>
<li><a class=\"nav\" id=\"nb_con\" href=\"./personalizza.php?$sessione_anno_var\">&nbsp;<b>".mex("CONFIGURA","head.php")."</b>&nbsp;</a>";
if ($modifica_pers != "NO" or ($priv_crea_backup == "s" and $installazione_subordinata != "SI") or ($priv_crea_interconnessioni == "s" and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO"))) {
echo "<ul>";
if ($id_utente == 1 and $installazione_subordinata != "SI") {
echo "<li><a id=\"nb_use\" href=\"./gestione_utenti.php?$sessione_anno_var\">&nbsp;<b>".mex("UTENTI","head.php")."</b>&nbsp;</a></li>
<li><a id=\"nb_web\" href=\"./crea_modelli.php?$sessione_anno_var\">&nbsp;<b>".mex("SITO WEB","head.php")."</b>&nbsp;</a></li>";
} # fine if ($id_utente == 1 and $installazione_subordinata != "SI")
if ($priv_crea_interconnessioni == "s" and (!defined('C_CREA_SUBORDINAZIONI') or C_CREA_SUBORDINAZIONI != "NO")) echo "<li><a id=\"nb_int\" href=\"./interconnessioni.php?$sessione_anno_var\">&nbsp;<b>".mex("INTERCONN.","head.php")."</b>&nbsp;</a></li>";
if ($priv_crea_backup == "s" and $installazione_subordinata != "SI") echo "<li><a id=\"nb_bac\" href=\"./crea_backup.php?$sessione_anno_var\">&nbsp;<b>".mex("BACKUP","head.php")."</b>&nbsp;</a></li>";
if ($modifica_pers != "NO") echo "<li><a id=\"nb_cdoc\" href=\"./personalizza.php?$sessione_anno_var#contratti\">&nbsp;<b>".mex("DOCUMENTI","head.php")."</b>&nbsp;</a></li>";
echo "</ul>";
} # fine if ($modifica_pers != "NO" or ($priv_crea_backup == "s" and $installazione_subordinata != "SI") or
echo "</li></ul></div></td><td>";
} # fine if (($modifica_pers != "NO" or ($priv_crea_backup == "s" and $installazione_subordinata != "SI") or...
echo "</td></tr></table></td>";

if ($idprenota_origine) $anno = $anno - 1;

if ($mostra_X == "SI") {
if (@is_array($_POST)) reset($_POST);
for ($num1 = 0 ; $num1 < count($_POST) ; $num1++) {
$lista_var_X .= "&amp;".key($_POST)."=".$_POST[key($_POST)];
next($_POST);
} # fine for $num1
if (@is_array($_GET)) reset($_GET);
for($num1 = 0 ; $num1 < count($_GET); $num1++) {
$lista_var_X .= "&amp;".key($_GET)."=".$_GET[key($_GET)];
next($_GET);
} # fine for $num1
if ($lista_var_X) {
$lista_var_X = "?show_bar=NO".$lista_var_X;
echo "<td style=\"width: 10px;\">
<b><a class=\"nav\" style=\"color: #666666;\" href=\"$pag$lista_var_X\"><b>X</b></a></b></td>";
} # fine if ($lista_var_X)
else echo "<td style=\"width: 10px;\"></td>";
} # fine if ($mostra_X == "SI")

echo "</tr>
</table>
";


} # fine if ($anno_utente_attivato == "SI")

} # fine if ($show_bar != "NO")

if ($pag != "visualizza_contratto.php" or $show_bar != "NO") {
if ($show_bar != "NO") echo "</div><div id=\"contentbox\">";
else echo "</div><table id=\"contentbox\" cellspacing=0 cellpadding=0><tr><td valign=\"top\">";
} # fine if ($pag != "visualizza_contratto.php" or $show_bar != "NO")


if ($pag != "inizio.php" and $show_bar != "NO") echo "<div id=\"topsp\" style=\"height: 7px;\"></div>
";






?>