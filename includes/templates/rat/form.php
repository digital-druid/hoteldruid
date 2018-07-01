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



if (preg_replace("/[a-z]/","",$template_data_dir) != "") $template_data_dir = "";
include("./includes/templates/$template_data_dir/phrases.php");



esegui_query("delete from $tablepersonalizza where idpersonalizza = 'ultime_sel_crea_modelli' and idutente = '$id_utente'");
esegui_query("insert into $tablepersonalizza (idpersonalizza,valpersonalizza,idutente) values ('ultime_sel_crea_modelli','".aggslashdb($anno_modello).";;".aggslashdb($lingua_modello).";;".aggslashdb($perc_cart_mod_sel)."','$id_utente') ");


# Prendo i dati dal file se già esistente
if ($template_file_name[$lingua_modello]) $nome_file = $template_file_name[$lingua_modello];
else {
$nome_file = mext_rat($template_file_name["ita"],$pag,$lingua_modello);
if ($nome_file == $template_file_name["en"] or $nome_file == $template_file_name["ita"]) $nome_file = $lingua_modello."_".$template_file_name['en'];
} # fine else if ($template_file_name[$lingua_modello])
$SI = mex("SI",$pag);
$NO = mex("NO",$pag);
$modello_esistente = "NO";
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$modello_esistente = "SI";
include("./includes/templates/$template_data_dir/functions.php");
recupera_var_modello_rat($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"NO",$anno_modello,$PHPR_TAB_PRE);
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))


echo "<form method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"crea_modello\" value=\"SI\">
<input type=\"hidden\" name=\"fonte_dati_conn\" value=\"$fonte_dati_conn\">
<input type=\"hidden\" name=\"T_PHPR_DB_TYPE\" value=\"$T_PHPR_DB_TYPE\">
<input type=\"hidden\" name=\"T_PHPR_DB_NAME\" value=\"$T_PHPR_DB_NAME\">
<input type=\"hidden\" name=\"T_PHPR_DB_HOST\" value=\"$T_PHPR_DB_HOST\">
<input type=\"hidden\" name=\"T_PHPR_DB_PORT\" value=\"$T_PHPR_DB_PORT\">
<input type=\"hidden\" name=\"T_PHPR_DB_USER\" value=\"$T_PHPR_DB_USER\">
<input type=\"hidden\" name=\"T_PHPR_DB_PASS\" value=\"$T_PHPR_DB_PASS\">
<input type=\"hidden\" name=\"T_PHPR_LOAD_EXT\" value=\"$T_PHPR_LOAD_EXT\">
<input type=\"hidden\" name=\"T_PHPR_TAB_PRE\" value=\"$T_PHPR_TAB_PRE\">
<input type=\"hidden\" name=\"anno_modello\" value=\"$anno_modello\">
<input type=\"hidden\" name=\"lingua_modello\" value=\"$lingua_modello\">
<input type=\"hidden\" name=\"perc_cart_mod_sel\" value=\"$perc_cart_mod_sel\">

<div style=\"height: 4px;\"></div>
<h5 id=\"h_rat\"><span>".mext_rat("Pagina della tabella con le tariffe",$pag)."</span></h5><br><br>
<table><tr><td valign=\"top\">
".mext_rat("Mostra tariffe",$pag)."</td><td>";
if (!$num_periodi_date or controlla_num_pos($num_periodi_date) == "NO") $num_periodi_date = 1;
$numero_date_menu = $n_dates_menu;
$numero_data = 0;
for ($num1 = 0 ; $num1 < $num_periodi_date ; $num1++) {
echo mex("dal",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno_modello.1.php","inizioperiodo$num1",${"inizioperiodo".$num1},"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno_modello.1.php","fineperiodo$num1",${"fineperiodo".$num1},"","",$id_utente,$tema);
if (!${"intervalloperiodo".$num1}) ${"intervalloperiodo".$num1} = 1;
echo ",&nbsp;".str_replace(" ","&nbsp;",mex("$parola_settimane di intervallo",$pag)).":&nbsp;
<input type=\"text\" name=\"intervalloperiodo$num1\" value=\"".${"intervalloperiodo".$num1}."\" size=\"2\" maxlength=\"2\"><br>";
} # fine for $num1
if (!$estendi_ultima_data or strtoupper($estendi_ultima_data) == $SI or $estendi_ultima_data == "SI") { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
echo "</td><td style=\"width: 20px;\"></td><td valign=\"bottom\">";
if ($num_periodi_date > 1) echo "<input class=\"sbutton\" type=\"submit\" name=\"eliminadatemenu\" value=\"".mex("-",$pag)."\">&nbsp;";
echo "<input class=\"sbutton\" type=\"submit\" name=\"aggiungidatemenu\" value=\"".mex("+",$pag)."\">
<input type=\"hidden\" name=\"num_periodi_date\" value=\"$num_periodi_date\">
<input type=\"hidden\" name=\"nome_form_modello_passa\" value=\"form_$template_name\">
</td></tr></table>
".mex("Estendere l'ultima data fino a quella massima disponibile nel database?",$pag)."
<select name=\"estendi_ultima_data\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br>";
if (!$mostra_date_passate or strtoupper($mostra_date_passate) == $SI or $mostra_date_passate == "SI") { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
echo "".mex("Mostrare le date passate?",$pag)."
<select name=\"mostra_date_passate\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><br>";

if (!$stile_soldi) {
$stile_soldi = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'stile_soldi' and idutente = '$id_utente'");
$stile_soldi = risul_query($stile_soldi,0,'valpersonalizza');
} # fine if (!$stile_soldi)
else {
if ($stile_soldi == mex("usa",$pag)) $stile_soldi = "usa";
else $stile_soldi = "europa";
} # fine else if (!$stile_soldi)
if ($stile_soldi == "europa") $check_soldi_eu = " checked";
if ($stile_soldi == "usa") $check_soldi_usa = " checked";
echo "<table border=0 cellspacing=0 cellpadding=0><tr>
<td align=\"center\">".mex("Formato di visualizzazione dei soldi",$pag).":</td>
<td align=\"left\">
<label><input type=\"radio\" name=\"m_stile_soldi\" value=\"europa\"$check_soldi_eu>1.050.000,32</label><br>
<label><input type=\"radio\" name=\"m_stile_soldi\" value=\"usa\"$check_soldi_usa>1,050,000.32</label>
</td><td style=\"width: 50px;\"></td>";

if (!$stile_data) {
$stile_data = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'stile_data' and idutente = '$id_utente'");
$stile_data = risul_query($stile_data,0,'valpersonalizza');
} # fine if (!$stile_data)
else {
if ($stile_data == mex("usa",$pag)) $stile_data = "usa";
else $stile_data = "europa";
} # fine else if (!$stile_data)
if ($stile_data == "europa") $check_data_eu = " checked";
if ($stile_data == "usa") $check_data_usa = " checked";
echo "<td align=\"center\">".mex("Formato di visualizzazione delle date",$pag).":</td>
<td align=\"left\">
<label><input type=\"radio\" name=\"m_stile_data\" value=\"europa\"$check_data_eu>27-08-2002</label><br>
<label><input type=\"radio\" name=\"m_stile_data\" value=\"usa\"$check_data_usa>08-27-2002</label>
</td></tr><tr><td style=\"height: 2px;\" colspan=5></td></tr>";

if ($fr_Valuta_sing) $val_s = $fr_Valuta_sing;
else $val_s = $Euro;
if ($fr_Valuta_plur) $val_p = $fr_Valuta_plur;
else $val_p = $Euro;
if (!$anteponi_nome_valuta or strtoupper($anteponi_nome_valuta) != $SI) { $sel_NO = " selected"; $sel_SI = ""; }
else { $sel_SI = " selected"; $sel_NO = ""; }
echo "<tr><td colspan=2>
".mex("Nome della valuta al singolare",$pag).": <input type=\"text\" name=\"m_valuta_sing\" size=\"8\" value=\"$val_s\">
</td><td style=\"width: 50px;\"></td><td colspan=2>
".mex("Nome della valuta al plurale",$pag).": <input type=\"text\" name=\"m_valuta_plur\" size=\"8\" value=\"$val_p\"></td>
</tr><tr><td colspan=2>
".mex("Anteporre il nome della valuta?",$pag)."
<select name=\"anteponi_nome_valuta\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select>
</td></tr></table><br>";

echo mex("Tariffe (tipologie) da mostrare ed eventuali loro nomi sostitutivi con cui mostrarle",$pag).":<br>
<table style=\"margin-left: auto; margin-right: auto;\" border=\"1\" cellspacing=\"0\" cellpadding=\"4\">";
$celle = 1;
$num_colonne = 2;
$tablenometariffe_modello = $PHPR_TAB_PRE."ntariffe".$anno_modello;
$rigatariffe = esegui_query("select * from $tablenometariffe_modello where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($celle == 1) echo "<tr>";
$tariffa = "tariffa".$numtariffa;
$nometariffa=risul_query($rigatariffe,0,$tariffa);
if ($nometariffa == "") {
$nometariffa = $tariffa;
$nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
} # fine if ($nometariffa == "")
else $nometariffa_vedi = $nometariffa;
$nome_tariffa_imposto = "nome_tariffa_imposto".$numtariffa;
if (strtoupper($tariffe_mostra[$numtariffa]) == $SI) $checked = " checked";
else $checked = "";
if (!$tariffe_mostra["array_esistente"]) $checked = " checked";
$val = "";
if ($nomi_tariffe_imposte["array_esistente"]) $val = $nomi_tariffe_imposte[$numtariffa];
echo "<td><label><input type=\"checkbox\" name=\"$tariffa\" value=\"SI\"$checked> <b>$numtariffa</b>. $nometariffa_vedi</label>
 (".mex("col nome",$pag)." <input type=\"text\" name=\"$nome_tariffa_imposto\" value=\"$val\" size=\"22\">)</td>";
if ($celle == $num_colonne) {
echo "</tr>";
$celle = 0;
} # fine if ($celle == $num_colonne)
$celle++;
} # fine for $numtariffa
if ($celle != 1) {
for ($num1 = $celle ; $num1 <= $num_colonne ; $num1++) echo "<td>&nbsp;</td>";
echo "</tr>";
} # fine if ($celle != 1)
echo "</table><br>";

if (!$mostra_caparra or strtoupper($mostra_caparra) == $NO) { $sel_NO = " selected"; $sel_SI = ""; }
else { $sel_SI = " selected"; $sel_NO = ""; }
echo "".mex("Mostrare la caparra se presente?",$pag)."
<select name=\"mostra_caparra\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><br>";

$dati_ca = dati_costi_agg_ntariffe($tablenometariffe_modello,"NO");
echo "".mex("Costi aggiuntivi da mostrare ed eventuali loro nomi sostitutivi con cui mostrarli",$pag).":<br>
<table style=\"margin-left: auto; margin-right: auto;\" border=\"1\" cellspacing=\"0\" cellpadding=\"4\">";
$celle = 1;
$num_colonne = 2;
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($celle == 1) echo "<tr>";
if ($dati_ca[$numca]['tipo'] == "u") $tipo_costo = "Costo unico";
if ($dati_ca[$numca]['tipo'] == "s") $tipo_costo = "Costo $parola_settimanale";
$nome_costo_imposto = "nome_costo_imposto".$dati_ca[$numca]['id'];
if (!$costi_aggiuntivi_mostra["array_esistente"] or strtoupper($costi_aggiuntivi_mostra[$dati_ca[$numca]['id']]) == $SI) $checked = " checked";
else $checked = "";
if ($nomi_costi_agg_imposti[$dati_ca[$numca]['id']]) $val = htmlspecialchars($nomi_costi_agg_imposti[$dati_ca[$numca]['id']]);
else $val = "";
echo "<td><label><input type=\"checkbox\" name=\"attiva_costo".$dati_ca[$numca]['id']."\" value=\"SI\"$checked>".mex("$tipo_costo",$pag)." <em>".$dati_ca[$numca]['nome']."</em></label>:
 <input type=\"text\" name=\"$nome_costo_imposto\" value=\"$val\" size=\"22\"></td>";
if ($celle == $num_colonne) {
echo "</tr>";
$celle = 0;
} # fine if ($celle == $num_colonne)
$celle++;
} # fine for $numca
if ($celle != 1) {
for ($num1 = $celle ; $num1 <= $num_colonne ; $num1++) echo "<td>&nbsp;</td>";
echo "</tr>";
} # fine if ($celle != 1)
elseif ($dati_ca['num'] == 0) echo "<tr><td style=\"width: 250px;\">&nbsp;</td></tr>";
echo "</table><br>";

$dati_struttura = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'dati_struttura' and idutente = '$id_utente'");
$dati_struttura = explode("#@&",risul_query($dati_struttura,0,'valpersonalizza'));

if (!$apertura_tag_font and $modello_esistente != "SI") $apertura_tag_font = "";
else $apertura_tag_font = htmlspecialchars($apertura_tag_font);
if (!$chiusura_tag_font and $modello_esistente != "SI") $chiusura_tag_font = "";
else $chiusura_tag_font = htmlspecialchars($chiusura_tag_font);
if (!$stile_tabella_tariffe and $stile_tabella_tariffe != "SI") $stile_tabella_tariffe = htmlspecialchars("cellspacing=\"0\" cellpadding=\"2\" border=\"1\" style=\"text-align: center; margin-left: auto;  margin-right: auto;\"");
else $stile_tabella_tariffe = htmlspecialchars($stile_tabella_tariffe);
echo "".mex("Tag html di apertura per la formattazione delle font",$pag).":
 <input type=\"text\" name=\"apertura_tag_font\" size=\"25\" value=\"$apertura_tag_font\"><br>
".mex("Tag html di chiusura per la formattazione delle font",$pag).":
 <input type=\"text\" name=\"chiusura_tag_font\" size=\"25\" value=\"$chiusura_tag_font\"><br>
".mext_rat("Stile tabella tariffe",$pag).":
 <input type=\"text\" name=\"stile_tabella_tariffe\" size=\"75\" value=\"$stile_tabella_tariffe\"><br>";
if (!$file_css_frame) $file_css_frame = "http://";
else $file_css_frame = htmlspecialchars($file_css_frame);
echo "".mex("Url del file css per la modalità frame",$pag).":
 <input type=\"text\" name=\"file_css_frame\" size=\"35\" value=\"$file_css_frame\"><br>";

include("./includes/templates/$template_data_dir/themes.php");
$num_temi = count($template_theme_name);
$tema_sel = 0;
$js_opz_tema = "";
if (!$tema_modello and $modello_esistente != "SI") $tema_modello = "default";
if (!$tema_modello) $sel = " selected";
else $sel = "";
echo "<br>".mex("Utilizza per l'aspetto della pagina",$pag).":
 <select name=\"tema_modello\" id=\"tema_sel\" onchange=\"agg_tema_sel()\">
<option value=\"\"$sel>".mex("html personalizzato",$pag)."</option>";
for ($num1 = 1 ; $num1 <= $num_temi ; $num1++) {
if ($tema_modello == $template_theme_name[$num1]) {
$sel = " selected";
$tema_sel = $num1;
} # fine if ($tema_modello == $template_theme_name[$num1])
else $sel = "";
echo "<option value=\"".$template_theme_name[$num1]."\"$sel>".mex("il tema chiamato",$pag)." '".mex($template_theme_name[$num1],$pag)."'</option>";
$js_opz_tema .= "
if (tema_sel == '".$template_theme_name[$num1]."') {
opz_tema.innerHTML = '<table><tr><td style=\"width: 20px;\"><\/td><td class=\"linhbox\">";
$colori_tema = $template_theme_colors[$num1];
$num_colori = count($colori_tema);
for ($num2 = 1 ; $num2 <= $num_colori ; $num2++) {
$colore_corr = $colori_tema[$num2]['default'];
if ($sel and ${"colore_tema_".$num2}) $colore_corr = ${"colore_tema_".$num2};
$js_opz_tema .= "".mex("Colore del tema",$pag)." \"".mex($template_theme_name[$num1],$pag)."\" ".mex("per",$pag)." \"".$colori_tema[$num2]['name']."\":\\
 <input type=\"text\" name=\"colore_tema_$num2\" id=\"coltxt$num2\" size=\"8\" value=\"$colore_corr\" onchange=\"agg_colore_sel_txt($num2)\">\\
<select id=\"colsel$num2\" onchange=\"agg_colore_sel($num2)\" style=\"width: 80px; background-color: $colore_corr\"><option value=\"$colore_corr\" style=\"background-color: $colore_corr\">&nbsp;<\/option>'+options_colori+'<\/select><br>";
} # fine for $num2
$valori_tema = $template_theme_values[$num1];
$num_valori = count($valori_tema);
for ($num2 = 1 ; $num2 <= $num_valori ; $num2++) {
$valore_corr = $valori_tema[$num2]['default'];
if ($sel and $modello_esistente == "SI") $valore_corr = ${"valore_tema_".$num2};
$js_opz_tema .= "".mex("Valore del tema",$pag)." \"".mex($template_theme_name[$num1],$pag)."\" ".mex("per",$pag)." \"".$valori_tema[$num2]['name']."\":\\
 <input type=\"text\" name=\"valore_tema_$num2\" id=\"valtema$num2\" size=\"24\" value=\"$valore_corr\"><br>";
} # fine for $num2
$js_opz_tema .= "<\/td><\/tr><\/table>';
}";
} # fine for $num1
echo "</select><div id=\"opz_tema\"></div>";

$valori_tema = $template_theme_values[$tema_sel];
$num_valori = count($valori_tema);
for ($num1 = 1 ; $num1 <= $num_valori ; $num1++) {
if (!strcmp(${"valore_tema_".$num1},"")) ${"valore_tema_".$num1} = $valori_tema[$num1]['default'];
$valore_sost = ${"valore_tema_".$num1};
if (!strcmp($valore_sost,"")) $valore_sost = $valori_tema[$num1]['null'];
elseif (strcmp($valori_tema[$num1]['replace'],"")) $valore_sost = str_replace("[theme_value_$num1]",$valore_sost,$valori_tema[$num1]['replace']);
$template_theme_html_pre[$tema_sel] = str_replace("[theme_value_$num1]",$valore_sost,$template_theme_html_pre[$tema_sel]);
$template_theme_html_post[$tema_sel] = str_replace("[theme_value_$num1]",$valore_sost,$template_theme_html_post[$tema_sel]);
} # fine for $num1
$colori_tema = $template_theme_colors[$tema_sel];
$num_colori = count($colori_tema);
for ($num1 = 1 ; $num1 <= $num_colori ; $num1++) {
if (!${"colore_tema_".$num1}) ${"colore_tema_".$num1} = $colori_tema[$num1]['default'];
$template_theme_html_pre[$tema_sel] = str_replace("[theme_color_$num1]",${"colore_tema_".$num1},$template_theme_html_pre[$tema_sel]);
$template_theme_html_post[$tema_sel] = str_replace("[theme_color_$num1]",${"colore_tema_".$num1},$template_theme_html_post[$tema_sel]);
} # fine for $num1
if (!$prima_parte_html) $prima_parte_html = htmlspecialchars($template_theme_html_pre[$tema_sel]);
else $prima_parte_html = htmlspecialchars($prima_parte_html);
if (!$ultima_parte_html) $ultima_parte_html = htmlspecialchars($template_theme_html_post[$tema_sel]);
else $ultima_parte_html = htmlspecialchars($ultima_parte_html);
echo "<br>".mex("Parte html del file prima della form di disponibilità",$pag).":<br>
<span id=\"html_nota\" style=\"font-size: smaller;\"> (".mex("selezionare \"html personalizzato\" nell'aspetto della pagina per modificarla",$pag).")<br></span>
<textarea name=\"prima_parte_html\" id=\"html_pre\" rows=12 cols=110>$prima_parte_html
</textarea><br>
<br>".mex("Parte html del file dopo la form di disponibilità",$pag).":<br>
<textarea name=\"ultima_parte_html\" id=\"html_post\" rows=12 cols=110>
$ultima_parte_html
</textarea><br>
<script type=\"text/javascript\">
<!--
options_colori = '<option value=\"#ffffff\" style=\"background-color: #ffffff;\">&nbsp;<\/option>\\
<option value=\"#0000ff\" style=\"background-color: #0000ff;\">&nbsp;<\/option>\\
<option value=\"#000099\" style=\"background-color: #000099;\">&nbsp;<\/option>\\
<option value=\"#660099\" style=\"background-color: #660099;\">&nbsp;<\/option>\\
<option value=\"#ff00cc\" style=\"background-color: #ff00cc;\">&nbsp;<\/option>\\
<option value=\"#00ff00\" style=\"background-color: #00ff00;\">&nbsp;<\/option>\\
<option value=\"#009900\" style=\"background-color: #009900;\">&nbsp;<\/option>\\
<option value=\"#333300\" style=\"background-color: #333300;\">&nbsp;<\/option>\\
<option value=\"#ff0000\" style=\"background-color: #ff0000;\">&nbsp;<\/option>\\
<option value=\"#990000\" style=\"background-color: #990000;\">&nbsp;<\/option>\\
<option value=\"#ff9900\" style=\"background-color: #ff9900;\">&nbsp;<\/option>\\
<option value=\"#ffff00\" style=\"background-color: #ffff00;\">&nbsp;<\/option>\\
<option value=\"#dddddd\" style=\"background-color: #dddddd;\">&nbsp;<\/option>\\
<option value=\"#999999\" style=\"background-color: #999999;\">&nbsp;<\/option>\\
<option value=\"#000000\" style=\"background-color: #000000;\">&nbsp;<\/option>\\
';
function agg_tema_sel () {
var tema_sel = document.getElementById('tema_sel').selectedIndex;
tema_sel = document.getElementById('tema_sel').options[tema_sel].value;
var txt_html_pre = document.getElementById('html_pre');
var txt_html_post = document.getElementById('html_post');
var txt_html_nota = document.getElementById('html_nota');
var opz_tema = document.getElementById('opz_tema');
if (tema_sel) {
txt_html_pre.disabled = 1;
txt_html_post.disabled = 1;
txt_html_nota.innerHTML = ' (".str_replace("'","\\'",mex("selezionare \"html personalizzato\" nell'aspetto della pagina per modificarla",$pag)).")<br>';
$js_opz_tema
}
else {
txt_html_pre.disabled = 0;
txt_html_post.disabled = 0;
txt_html_nota.innerHTML = '';
opz_tema.innerHTML = '';
}
}
agg_tema_sel();
-->
</script>


<input type=\"hidden\" name=\"cambia_frasi\" value=\"$cambia_frasi\">
<input type=\"hidden\" name=\"modello_esistente\" value=\"$modello_esistente\">";
if ($cambia_frasi) {
echo "<br><table style=\"background-color: #000000; margin-left: auto; margin-right: auto;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
<tr><td><table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" width=\"100%\">
<tr><td bgcolor=\"#faffff\">
<b>".mex("Frasi predefinite",$pag)."</b><br>
<table><tr><td style=\"height: 2px;\"></td></tr></table>";
if ($modello_esistente == "SI") {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) echo "".mext_rat($frase[$num_fr],$pag).": <input type=\"text\" name=\"".$fr_frase[$num_fr]."\" size=\"45\" value=\"".str_replace("\"","&quot;",${$fr_frase[$num_fr]})."\"><br>";
} # fine if ($modello_esistente == "SI")
else {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) echo "".mext_rat($frase[$num_fr],$pag).": <input type=\"text\" name=\"".$fr_frase[$num_fr]."\" size=\"45\" value=\"".str_replace("\"","&quot;",stripslashes(mext_rat($frase[$num_fr],$pag,$lingua_modello)))."\"><br>";
} # fine else if ($modello_esistente == "SI")
echo "</td></tr></table>
</td></tr></table>";
} # fine if ($cambia_frasi)
elseif ($modello_esistente == "SI") {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) echo "<input type=\"hidden\" name=\"".$fr_frase[$num_fr]."\" value=\"".str_replace("\"","&quot;",${$fr_frase[$num_fr]})."\">";
} # fine elseif ($modello_esistente == "SI")

echo "<br><div style=\"text-align: center;\"><input type=\"hidden\" name=\"$template_name\" value=\"SI\">
<button class=\"rate\" type=\"submit\"><div>".mext_rat("Crea la pagina con la tabella delle tariffe",$pag)."</div></button>
</div></div></form><br>
<hr style=\"width: 95%\"><br><div style=\"text-align: center;\">
<form method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";



?>