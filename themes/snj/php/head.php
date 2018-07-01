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


# head
$body_bgcolor = "#dddddd";
if ($senza_colori == "SI" or $pag == "visualizza_contratto.php") $body_bgcolor = "#ffffff";
if ($pag == "punto_vendita.php") $iscale = "0.8";
else $iscale = "1.0";
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") $titolo = "";
if (defined('C_FILE_TITOLO_PERS') and C_FILE_TITOLO_PERS != "" and @is_file(C_FILE_TITOLO_PERS)) $titolo = trim(substr(implode("",file(C_FILE_TITOLO_PERS)),0,40))." - $titolo";
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"
        \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >
<meta name=\"viewport\" content=\"initial-scale=$iscale\">
<title> $titolo </title>";
if ($pag == "visualizza_contratto.php" and $extra_head) echo $extra_head;
if (defined('C_URL_FAVICON') and C_URL_FAVICON != "" and @is_file(C_URL_FAVICON)) echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"".C_URL_FAVICON."\">
";
elseif (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI") echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"./img/favicon.ico\">
";
if ($base_js) echo "<script type=\"text/javascript\" src=\"./base.js$vers_hinc\">
</script>
";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./base.css$vers_hinc\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"./themes/snj/inc/stylesheet.css$vers_hinc\" media=\"all\">
";
if (defined('C_FILE_CSS_PERS') and C_FILE_CSS_PERS != "" and @is_file(C_FILE_CSS_PERS)) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".C_FILE_CSS_PERS."\" media=\"all\">
";
if ($mobile_device and defined('C_FILE_MOB_CSS_PERS') and C_FILE_MOB_CSS_PERS != "" and @is_file(C_FILE_MOB_CSS_PERS)) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".C_FILE_MOB_CSS_PERS."\" media=\"all\">
";
echo "</head>
<body";
if ($drag_drop) echo " ondragover=\"event.preventDefault();\" ondragenter=\"event.preventDefault();\" ondrop=\"event.preventDefault();drp_out();\"";
echo " style=\"background-color: $body_bgcolor;\">";

if ($pag != "visualizza_contratto.php") echo "<table style=\"height: 97%; width: 99%; background-color: #000000; margin-right: auto; margin-left: auto; margin-top: 5px; margin-bottom: 5px; border-width: 1px; border-color: black;\" cellpadding=\"0\" cellspacing=\"1\">
<tr><td style=\"background-color: #ffffff; vertical-align: top; margin:0; padding:0;\">
";



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
if (substr($priv_mod_pers,0,1) == "s" and !$modifica_pers) $modifica_pers = "SI";
if (!$priv_crea_backup) $priv_crea_backup = substr($priv_mod_pers,1,1);
if (!$priv_ins_clienti) $priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
if (substr($priv_ins_clienti,0,1) != "s" and !$inserimento_nuovi_clienti) $inserimento_nuovi_clienti = "NO";
if (substr($priv_ins_clienti,1,1) != "s" and substr($priv_ins_clienti,1,1) != "p" and !$modifica_clienti) $modifica_clienti = "NO";
if (substr($priv_ins_clienti,2,1) != "s" and substr($priv_ins_clienti,2,1) != "p" and !$vedi_clienti) $vedi_clienti = "NO";
if (!$priv_ins_prenota) $priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
if (!$priv_ins_nuove_prenota) $priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
if (!$priv_ins_costi) $priv_ins_costi = risul_query($privilegi_annuali_utente,0,'priv_ins_costi');
if (!$priv_ins_spese) $priv_ins_spese = substr($priv_ins_costi,0,1);
if (!$priv_ins_entrate) $priv_ins_entrate = substr($priv_ins_costi,1,1);
if (!$priv_vedi_tab) $priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
if (!$priv_vedi_tab_mesi) $priv_vedi_tab_mesi = substr($priv_vedi_tab,0,1);
if (!$priv_vedi_tab_prenotazioni) $priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
if (!$priv_vedi_tab_costi) $priv_vedi_tab_costi = substr($priv_vedi_tab,2,1);
if (!$priv_vedi_tab_periodi) $priv_vedi_tab_periodi = substr($priv_vedi_tab,3,1);
if (!$priv_vedi_tab_regole) $priv_vedi_tab_regole = substr($priv_vedi_tab,4,1);
if (!$priv_vedi_tab_appartamenti) $priv_vedi_tab_appartamenti = substr($priv_vedi_tab,5,1);
} # fine if ($anno_utente_attivato == "SI")
} # fine if ($id_utente != 1)
else {
if (!$anno_utente_attivato) $anno_utente_attivato = "SI";
if (!$modifica_pers) $modifica_pers = "SI";
if (!$priv_crea_backup) $priv_crea_backup = "s";
if (!$inserimento_nuovi_clienti) $inserimento_nuovi_clienti = "SI";
if (!$modifica_clienti) $modifica_clienti = "SI";
if (!$vedi_clienti) $vedi_clienti = "SI";
if (!$priv_ins_nuove_prenota) $priv_ins_nuove_prenota = "s";
if (!$priv_ins_spese) $priv_ins_spese = "s";
if (!$priv_ins_entrate) $priv_ins_entrate = "s";
if (!$priv_vedi_tab_mesi) $priv_vedi_tab_mesi = "s";
if (!$priv_vedi_tab_prenotazioni) $priv_vedi_tab_prenotazioni = "s";
if (!$priv_vedi_tab_costi) $priv_vedi_tab_costi = "s";
if (!$priv_vedi_tab_periodi) $priv_vedi_tab_periodi = "s";
if (!$priv_vedi_tab_regole) $priv_vedi_tab_regole = "s";
if (!$priv_vedi_tab_appartamenti) $priv_vedi_tab_appartamenti = "s";
} # fine else if ($id_utente != 1)

if ($anno_utente_attivato == "SI") {

$mese_attuale = date("n",(time() + (C_DIFF_ORE * 3600)));
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



if ($pag == "tabella.php" or $pag == "tabella2.php" or $pag == "tabella3.php" or $pag == "visualizza_tabelle.php" or $pag == "storia_soldi.php") $mostra_X = "SI";


echo "<table class=\"nav_bar\" style=\"text-align: center; background: $t1color url(./themes/snj/img/bar_bg_top.png) repeat-x left top;\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
<tr style=\"background: url(./themes/snj/img/bar_bg_bot.png) repeat-x left bottom;\">";

if ($mostra_X == "SI") echo "<td style=\"width: 10px;\"></td>";
if ($idprenota_origine) $anno = $anno + 1;
if ($id_sessione) {
$sessione_anno_var = "id_sessione=$id_sessione";
if (substr($id_sessione,0,4) != $anno) $sessione_anno_var .= "&amp;anno=$anno";
} # fine if ($id_sessione)
else $sessione_anno_var = "anno=$anno";

echo "<td style=\"height: 18px; color: #666666; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small;\">
<a id=\"nb_men\" href=\"./inizio.php?$sessione_anno_var\"><b>".mex("MENU","head.php")."</b></a>";
if ($priv_ins_nuove_prenota == "s" or $inserimento_nuovi_clienti != "NO" or ($modifica_clienti != "NO" and $vedi_clienti != "NO")) {
echo "&nbsp;|&nbsp;<b id=\"nb_ins\">".mex("INSERIRE","head.php").":</b>";
if ($priv_ins_nuove_prenota == "s") echo "&nbsp;&nbsp;<a id=\"nb_ires\" href=\"./prenota.php?$sessione_anno_var\"><b>".mex("PRENOTAZIONE","head.php")."</b></a>";
if ($inserimento_nuovi_clienti != "NO" or ($modifica_clienti != "NO" and $vedi_clienti != "NO")) echo "&nbsp;&nbsp;<a id=\"nb_icli\" href=\"./clienti.php?$sessione_anno_var\"><b>".mex("CLIENTE","head.php")."</b></a>";
} # fine if ($priv_ins_nuove_prenota == "s" or...
if ($priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n" or $vedi_clienti != "NO" or $priv_vedi_tab_periodi != "n" or $priv_vedi_tab_appartamenti != "n") {
echo "&nbsp;|&nbsp;<b id=\"nb_tab\">".mex("TABELLE","head.php").":</b>";
if ($priv_vedi_tab_mesi != "n") echo "&nbsp;&nbsp;<a id=\"nb_m0\" href=\"./tabella.php?$sessione_anno_var&amp;mese=$mese_attuale\"><b>".mex("$MESE_ATT","head.php")."</b></a>";
if ($priv_vedi_tab_prenotazioni != "n") echo "&nbsp;&nbsp;<a id=\"nb_res\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=prenotazioni\"><b>".mex("PRENOTAZIONI","head.php")."</b></a>";
if ($vedi_clienti != "NO") echo "&nbsp;&nbsp;<a id=\"nb_cli\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=clienti\"><b>".mex("CLIENTI","head.php")."</b></a>";
if ($priv_vedi_tab_periodi != "n") echo "&nbsp;&nbsp;<a id=\"nb_rat\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=periodi\"><b>".mex("TARIFFE","head.php")."</b></a>";
if ($priv_vedi_tab_appartamenti != "n") {
$fr_APPARTAMENTI = mex("APPARTAMENTI","unit.php");
if (strlen($fr_APPARTAMENTI) > 11) $fr_APPARTAMENTI = substr($fr_APPARTAMENTI,0,6).".";
echo "&nbsp;&nbsp;<a id=\"nb_roo\" href=\"./visualizza_tabelle.php?$sessione_anno_var&amp;tipo_tabella=appartamenti\"><b>$fr_APPARTAMENTI</b></a>";
} # fine if ($priv_vedi_tab_appartamenti != "n")
} # fine if ($priv_vedi_tab_mesi != "n" or $priv_vedi_tab_prenotazioni != "n" or...
echo "</td>";

if ($idprenota_origine) $anno = $anno - 1;

if ($mostra_X == "SI") {
if (@is_array($_POST)) reset($_POST);
for($num1 = 0 ; $num1 < count($_POST); $num1++) {
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
echo "<td style=\"width: 10px; color: #666666; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small;\">
<b><a style=\"color: #666666;\" href=\"$pag$lista_var_X\">X</a></b></td>";
} # fine if ($lista_var_X)
else echo "<td style=\"width: 10px;\"></td>";
} # fine if ($mostra_X == "SI")

echo "</tr>
</table>
";

if ($pag != "inizio.php") echo "<div style=\"height: 7px;\"></div>
";

} # fine if ($anno_utente_attivato == "SI")

} # fine if ($show_bar != "NO")

if ($pag != "visualizza_contratto.php") echo "<table style=\"height: 100%;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
<tr><td style=\"width: 5px;\"></td><td valign=\"top\">

";






?>