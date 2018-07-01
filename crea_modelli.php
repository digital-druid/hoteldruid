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

$pag = "crea_modelli.php";
$titolo = "HotelDruid: Crea Pagine Web";
$base_js = 1;

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
include("./includes/funzioni_costi_agg.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);

if ($id_utente != 1) {
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else $anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
$priv_crea_interconnessioni = substr($priv_mod_pers,3,1);
} # fine if ($id_utente != )
else {
$priv_crea_interconnessioni = "s";
$anno_utente_attivato = "SI";
} # fine else if ($id_utente != 1)


if ($priv_crea_interconnessioni == "s" and $anno_utente_attivato == "SI") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$Euro = nome_valuta();

include("./includes/templates/funzioni_modelli.php");

if (strlen($lingua_modello) > 3 or (!@is_dir("./includes/lang/".$lingua_modello) and $lingua_modello != "ita") or str_replace(".","",$lingua_modello) != $lingua_modello) $lingua_modello = $lingua_mex;


if ($aggiungidatemenu or $eliminadatemenu) {
unset($crea_modello);
$$nome_form_modello_passa = "SI";
if ($aggiungidatemenu) $num_periodi_date++;
if ($eliminadatemenu) $num_periodi_date--;
if (@get_magic_quotes_gpc()) {
$stile_riquadro_calendario = stripslashes($stile_riquadro_calendario);
$stile_tabella_calendario = stripslashes($stile_tabella_calendario);
$stile_bottoni_calendario = stripslashes($stile_bottoni_calendario);
$stile_bottone_apertura_calendario = stripslashes($stile_bottone_apertura_calendario);
$apertura_tag_font = stripslashes($apertura_tag_font);
$chiusura_tag_font = stripslashes($chiusura_tag_font);
$apertura_tag_font_rosse = stripslashes($apertura_tag_font_rosse);
$chiusura_tag_font_rosse = stripslashes($chiusura_tag_font_rosse);
$apertura_font_quadro_disponibilita = stripslashes($apertura_font_quadro_disponibilita);
$chiusura_font_quadro_disponibilita = stripslashes($chiusura_font_quadro_disponibilita);
$prima_parte_html = stripslashes($prima_parte_html);
$ultima_parte_html = stripslashes($ultima_parte_html);
} # fine if (@get_magic_quotes_gpc())
} # fine if ($aggiungidatemenu or $eliminadatemenu)

if ($eliminacampipers or $aggiungicampipers or $eliminacampicond or $aggiungicampicond or $eliminacodpromo or $aggiungicodpromo) {
unset($crea_modello);
$$nome_form_modello_passa = "SI";
if ($aggiungicampipers) $num_campi_pers++;
if ($eliminacampipers) $num_campi_pers--;
if ($aggiungicampicond) $num_campi_doc_cond++;
if ($eliminacampicond) $num_campi_doc_cond--;
if ($aggiungicodpromo) $num_codici_promo++;
if ($eliminacodpromo) $num_codici_promo--;
if (@get_magic_quotes_gpc()) {
$stile_riquadro_calendario = stripslashes($stile_riquadro_calendario);
$stile_tabella_calendario = stripslashes($stile_tabella_calendario);
$stile_bottoni_calendario = stripslashes($stile_bottoni_calendario);
$stile_bottone_apertura_calendario = stripslashes($stile_bottone_apertura_calendario);
$apertura_tag_font = stripslashes($apertura_tag_font);
$chiusura_tag_font = stripslashes($chiusura_tag_font);
$apertura_tag_font_rosse = stripslashes($apertura_tag_font_rosse);
$chiusura_tag_font_rosse = stripslashes($chiusura_tag_font_rosse);
$apertura_font_quadro_disponibilita = stripslashes($apertura_font_quadro_disponibilita);
$chiusura_font_quadro_disponibilita = stripslashes($chiusura_font_quadro_disponibilita);
$prima_parte_html = stripslashes($prima_parte_html);
$ultima_parte_html = stripslashes($ultima_parte_html);
} # fine if (@get_magic_quotes_gpc())
} # fine if ($eliminacampipers or $aggiungicampipers or...




if ($crea_modello) {
$mostra_form_creazione = "NO";
$lingua_orig = $lingua_mex;
include(C_DATI_PATH."/lingua.php");
$lingua_mex = $lingua[1];

if (defined('C_BACKUP_E_MODELLI_CON_NUOVI_DATI') and C_BACKUP_E_MODELLI_CON_NUOVI_DATI == "NO") $fonte_dati_conn = "attuali";
if ($fonte_dati_conn == "attuali") {
$M_PHPR_DB_TYPE = $PHPR_DB_TYPE;
$M_PHPR_DB_NAME = $PHPR_DB_NAME;
$M_PHPR_DB_HOST = $PHPR_DB_HOST;
$M_PHPR_DB_PORT = $PHPR_DB_PORT;
$M_PHPR_DB_USER = $PHPR_DB_USER;
$M_PHPR_DB_PASS = $PHPR_DB_PASS;
$M_PHPR_LOAD_EXT = $PHPR_LOAD_EXT;
$M_PHPR_TAB_PRE = $PHPR_TAB_PRE;
} # fine if ($fonte_dati_conn == "attuali")
if ($fonte_dati_conn == "nuovi") {
$M_PHPR_DB_TYPE = $T_PHPR_DB_TYPE;
$M_PHPR_DB_NAME = $T_PHPR_DB_NAME;
$M_PHPR_DB_HOST = $T_PHPR_DB_HOST;
$M_PHPR_DB_PORT = $T_PHPR_DB_PORT;
$M_PHPR_DB_USER = $T_PHPR_DB_USER;
$M_PHPR_DB_PASS = $T_PHPR_DB_PASS;
$M_PHPR_LOAD_EXT = $T_PHPR_LOAD_EXT;
$M_PHPR_TAB_PRE = $T_PHPR_TAB_PRE;
} # fine if ($fonte_dati_conn == "attuali")



if ($modello_disponibilita) {

include("./includes/templates/frasi_mod_disp.php");
include("./includes/templates/funzioni_mod_disp.php");
crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"NO",$fr_frase,$frase,$num_frasi,$tipo_periodi);

} # fine if ($modello_disponibilita)


else {
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if ($$template_name) {
$mostra_form_creazione = "NO";
include("./includes/templates/$modello_ext/phrases.php");
include("./includes/templates/$modello_ext/functions.php");
$funz_crea_modello = "crea_modello_".$modello_ext;
$funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"NO",$fr_frase,$frase,$num_frasi,$tipo_periodi);
break;
} # fine if ($$template_name)
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
} # fine else if ($modello_disponibilita)

$lingua_mex = $lingua_orig;
if ($origine) $azione = $origine;
else $azione = $pag;
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$azione\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div>".mex("OK",$pag)."</div></button>
</div></form>";

} # fine if ($crea_modello)



if ($id_utente == 1) {


if ($form_modello_disponibilita) {
$mostra_form_creazione = "NO";
include("./includes/templates/frasi_mod_disp.php");


esegui_query("delete from $tablepersonalizza where idpersonalizza = 'ultime_sel_crea_modelli' and idutente = '$id_utente'");
esegui_query("insert into $tablepersonalizza (idpersonalizza,valpersonalizza,idutente) values ('ultime_sel_crea_modelli','".aggslashdb($anno_modello).";;".aggslashdb($lingua_modello).";;".aggslashdb($perc_cart_mod_sel)."','$id_utente') ");

# Prendo i dati dal file se già esistente
$nome_file = mex2("mdl_disponibilita",$pag,$lingua_modello).".php";
$SI = mex("SI",$pag);
$NO = mex("NO",$pag);
$modello_esistente = "NO";
if (@is_file("$percorso_cartella_modello/$nome_file") and (!defined('C_RESTRIZIONI_DEMO_ADMIN') or C_RESTRIZIONI_DEMO_ADMIN != "SI")) {
$modello_esistente = "SI";
include("./includes/templates/funzioni_mod_disp.php");
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"NO",$anno_modello,$PHPR_TAB_PRE);
} # fine if (@is_file("$percorso_cartella_modello/$nome_file") and (!defined('C_RESTRIZIONI_DEMO_ADMIN') or C_RESTRIZIONI_DEMO_ADMIN != "SI"))


echo "<form id=\"fcrmod\" accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
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
<h5 id=\"h_chav\"><span>".mex("Pagina per controllare la disponibilità",$pag)."</span></h5><br><br>
<table><tr><td valign=\"top\">
".mex("Date nei menù a tendina",$pag).":</td><td>";
if (!$num_periodi_date or controlla_num_pos($num_periodi_date) == "NO") $num_periodi_date = 1;
$numero_date_menu = $n_dates_menu;
$numero_data = 0;
for ($num1 = 0 ; $num1 < $num_periodi_date ; $num1++) {
echo mex("dal",$pag)." ";
# variabili ausiliari per possibile bug php 5.3 su windows
$iniper = ${"inizioperiodo".$num1};
$fineper = ${"fineperiodo".$num1};
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno_modello.1.php","inizioperiodo$num1",$iniper,"","",$id_utente,$tema);
echo " ".mex("al",$pag)." ";
mostra_menu_date(C_DATI_PATH."/selectperiodi$anno_modello.1.php","fineperiodo$num1",$fineper,"","",$id_utente,$tema);
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
<input type=\"hidden\" name=\"nome_form_modello_passa\" value=\"form_modello_disponibilita\">
</td></tr></table>
".mex("Estendere l'ultima data fino a quella massima disponibile nel database?",$pag)."
<select name=\"estendi_ultima_data\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br>";

if ( (string) $periodi_no_richieste != "") $val = $periodi_no_richieste;
else $val = 0;
echo "".mex("Accetta solo richieste che cominciano almeno dopo",$pag)."
<input type=\"text\" name=\"sett_no_prenota\" size=\"3\" value=\"$val\">
".mex("$parola_settimane",$pag).".<br><br>";

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
</tr><tr><td style=\"height: 2px;\" colspan=5></td></tr><tr><td colspan=5>
".mex("Anteporre il nome della valuta?",$pag)."
<select name=\"anteponi_nome_valuta\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select></td></tr><tr><td style=\"height: 2px;\" colspan=5></td></tr>";

$tutti_utenti = esegui_query("select idutenti,nome_utente from $tableutenti order by idutenti");
$num_tutti_utenti = numlin_query($tutti_utenti);
$option_utenti = "";
for ($num1 = 0 ; $num1 < $num_tutti_utenti ; $num1++) {
$nome_utente = risul_query($tutti_utenti,$num1,'nome_utente');
$option_utenti .= "<option value=\"$nome_utente\">$nome_utente</option>";
$num_utente = risul_query($tutti_utenti,$num1,'idutenti');
$num_utenti[$nome_utente] = $num_utente;
} # fine for $num1
if (!strcmp($utente_lis,"")) $utente_lis = $utente_liste;
echo "<tr><td colspan=5>
".mex("Utilizzare le liste di nazioni, regioni, etc. dell'utente",$pag)."
<select name=\"utente_lis\">
".str_replace("\"$utente_lis\">","\"$utente_lis\" selected>",$option_utenti)."
</select></td></tr></table><br>";

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

if (!$chiedi_numero_appartamenti_per_tipologia or strtoupper($chiedi_numero_appartamenti_per_tipologia) == $SI) { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
if ($massimo_numero_appartamenti_per_tipologia) $val = $massimo_numero_appartamenti_per_tipologia;
else $val = 0;
if ($fr_appartamenti) $parola_appartamenti = $fr_appartamenti;
else $parola_appartamenti = mex2("appartamenti",'unit.php',$lingua_modello);
if ($fr_appartamento) $parola_appartamento = $fr_appartamento;
else $parola_appartamento = mex2("appartamento",'unit.php',$lingua_modello);
echo "".mex("Chiedere il numero di appartamenti per ogni tipologia?",'unit.php')."
<select name=\"chiedi_num_app_tipologia\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>
".mex("Numero massimo di appartamenti",'unit.php').":
 <input type=\"text\" name=\"max_num_app_tipologia\" size=\"3\" value=\"$val\">
(".mex("0 per scelta libera",$pag).").<br>
".mex("Parola da utilizzare per indicare gli appartamenti",'unit.php').":
 <input type=\"text\" name=\"parola_appartamenti\" size=\"15\" value=\"$parola_appartamenti\">;
 ".mex("singolare",$pag).": <input type=\"text\" name=\"parola_appartamento\" size=\"15\" value=\"$parola_appartamento\">
</td></tr></table><br>";

if (!$aggiungi_altre_tipologie or strtoupper($aggiungi_altre_tipologie) == $SI) { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
if ($massimo_numero_altre_tipologie) $val = $massimo_numero_altre_tipologie;
else $val = 3;
echo "".mex("Possibilità di aggiungere più tipologie da controllare contemporaneamente?",$pag)."
<select name=\"aggiungi_tipologie\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>
".mex("Numero massimo di tipologie",$pag).":
 <input type=\"text\" name=\"max_num_tipologie\" size=\"3\" value=\"$val\">
</td></tr></table><br>";

$sel_NO = " selected";
$sel_SI = "";
$sel_chied = "";
$sel_poss = "";
if (strtoupper($cerca_appartamenti_vicini) == $SI) { $sel_SI = " selected"; $sel_NO = ""; }
if (strtoupper($cerca_appartamenti_vicini) == strtoupper(mex("se possibile",$pag))) { $sel_poss = " selected"; $sel_NO = ""; }
if (strtoupper($cerca_appartamenti_vicini) == strtoupper(mex("chiedere",$pag))) { $sel_chied = " selected"; $sel_NO = ""; }
echo "".mex("Quando si richiedono più appartamenti o tipologie, cercare appartamenti vicini",'unit.php').":
<select name=\"cerca_app_vicini\">
<option value=\"NO\"$sel_NO>".mex("mai",$pag)."</option>
<option value=\"se possibile\"$sel_poss>".mex("se possibile",$pag)."</option>
<option value=\"SI\"$sel_SI>".mex("sempre",$pag)."</option>
<option value=\"chiedere\"$sel_chied>".mex("chiedere",$pag)."</option>
</select><br><br>";

if (!$chiedi_numero_persone or strtoupper($chiedi_numero_persone) == $NO) { $check_NO = " checked"; $check_SI = ""; }
else { $check_SI = " checked"; $check_NO = ""; }
if ($massimo_numero_persone) $val = $massimo_numero_persone;
else $val = 0;
echo "".mex("Chiedere il numero di persone?",$pag)."
 <label><input type=\"radio\" name=\"chiedi_num_persone\" value=\"NO\"$check_NO>".mex("No",$pag)."</label>
 <label><input type=\"radio\" name=\"chiedi_num_persone\" value=\"SI\"$check_SI>".mex("Si",$pag)."</label><br>
<table><tr><td style=\"width: 25px;\"></td><td>
".mex("Numero massimo di persone",$pag).":
 <input type=\"text\" name=\"max_num_persone\" size=\"3\" value=\"$val\">
(".mex("0 per scelta libera",$pag).").";
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe_modello,"NO");
$select_costi_letto = "";
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($costo_aggiungi_letti == $dati_ca[$numca]['id']) $sel = " selected";
else $sel = "";
if ($dati_ca[$numca]['letto'] == "s" and $dati_ca[$numca]['numsett'] != "c" and $dati_ca[$numca]['mostra'] == "s" and $dati_ca[$numca]['combina'] != "s") $select_costi_letto .= "<option value=\"".$dati_ca[$numca]['id']."\"$sel>".$dati_ca[$numca]['nome']."</option>";
} # fine for $numca
if ($select_costi_letto) {
if ($costo_aggiungi_letti == "") $sel = " selected";
else $sel = "";
if ($massimo_numero_letti_aggiuntivi) $val = $massimo_numero_letti_aggiuntivi;
else $val = 2;
echo "<br>".mex("Se le persone superano la capienza massima utilizzare il costo aggiuntivo",$pag)."
<select name=\"costo_aggiungi_letti\">
<option value=\"\"$sel>----</option>
$select_costi_letto
</select><br>
<table><tr><td style=\"width: 25px;\"></td><td>".mex("Se il costo può essere moltiplicato aggiungere al massimo",$pag)."
 <input type=\"text\" name=\"max_num_aggiungi_letti\" size=\"3\" value=\"$val\">
 ".mex("letti aggiuntivi",$pag).".</td></tr></table>";
} # fine if ($select_costi_letto)

if (!$chiedi_costi_aggiuntivi_di_pag_inserzione or strtoupper($chiedi_costi_aggiuntivi_di_pag_inserzione) == $SI) { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
if ($numero_colonne_costi_aggiuntivi) $val = $numero_colonne_costi_aggiuntivi;
else $val = 2;
echo "</td></tr></table><br>
".mex("Chiedere se aggiungere i costi presenti nella pagina di inserzione prenotazioni?",$pag)."
 <select name=\"mostra_costi_aggiuntivi\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>
".mex("Numero di colonne dei costi aggiuntivi",$pag).":
 <input type=\"text\" name=\"num_colonne_costi_agg\" size=\"3\" value=\"$val\">
</td></tr></table>";
$sel_SI = "";
$sel_NO = "";
$sel_opz = " selected";
if (strtoupper($aggiungi_costi_fissi) == $NO) { $sel_NO = " selected"; $sel_opz = ""; }
if (strtoupper($aggiungi_costi_fissi) == $SI) { $sel_SI = " selected"; $sel_opz = ""; }
echo "".mex("Aggiungere al prezzo i costi aggiuntivi fissi associati alle tariffe?",$pag)."
<select name=\"aggiungi_costi_fissi\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag).", ".mex("tutti",$pag)."</option>
<option value=\"opzionale\"$sel_opz>".mex("Solo quelli selezionati per essere mostrati",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><br>

".mex("Costi aggiuntivi da mostrare ed eventuali loro nomi sostitutivi con cui mostrarli",$pag)."
 <small>(".mex("usare uno spazio per non mostrare il costo nei dettagli",$pag).")</small>:<br>
<table style=\"margin-left: auto; margin-right: auto;\" border=\"1\" cellspacing=\"0\" cellpadding=\"4\">";
$celle = 1;
$num_colonne = 2;
unset($categorie_combina);
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($celle == 1) echo "<tr>";
if ($dati_ca[$numca]['tipo'] == "u") $tipo_costo = "Costo unico";
if ($dati_ca[$numca]['tipo'] == "s") $tipo_costo = "Costo $parola_settimanale";
$nome_costo_imposto = "nome_costo_imposto".$dati_ca[$numca]['id'];
if ($dati_ca[$numca]['combina'] == "s") $categorie_combina[$dati_ca[$numca]['categoria']] = $dati_ca[$numca]['id'];
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
if (@is_array($categorie_combina)) {
reset($categorie_combina);
foreach ($categorie_combina as $categoria => $id_costo_cat) {
$val = htmlspecialchars($categorie_costi_agg_imposte[$categoria]);
$categoria = htmlspecialchars($categoria);
echo "<td>".mex("Categoria",$pag)." <em>$categoria</em>:
 <input type=\"text\" name=\"nome_cat_imp$id_costo_cat\" value=\"$val\" size=\"22\"></td>";
if ($celle == $num_colonne) {
echo "</tr>";
$celle = 0;
} # fine if ($celle == $num_colonne)
$celle++;
} # fine foreach ($categorie_combina as $categoria => $id_costo_cat)
} # fine if (@is_array($categorie_combina))
if ($celle != 1) {
for ($num1 = $celle ; $num1 <= $num_colonne ; $num1++) echo "<td>&nbsp;</td>";
echo "</tr>";
} # fine if ($celle != 1)
elseif ($dati_ca['num'] == 0) echo "<tr><td style=\"width: 250px;\">&nbsp;</td></tr>";
echo "</table><br>";

echo "<a name=\"codprom\"></a>".mex("Codici promozionali",$pag).":<br>
<table><tr><td style=\"width: 25px;\"></td><td valign=\"top\">";
if (!$num_codici_promo and $campi_codici_promo['array_esistente']) $num_codici_promo = (count($campi_codici_promo) - 1);
if (!$num_codici_promo or controlla_num_pos($num_codici_promo) == "NO") $num_codici_promo = 1;
for ($num1 = 1 ; $num1 <= $num_codici_promo ; $num1++) {
if (!${"codice_promo".$num1}) ${"codice_promo".$num1} = $campi_codici_promo[$num1];
if (!${"tipo_codice_promo".$num1}) ${"tipo_codice_promo".$num1} = substr($costi_campi_codici_promo[$num1],0,1);
if (${"tipo_codice_promo".$num1} != "-") ${"tipo_codice_promo".$num1} = "+";
if (${"tipo_codice_promo".$num1} == "+") { $sel_agg = " selected"; $sel_rim = ""; }
else { $sel_agg = ""; $sel_rim = " selected"; }
echo "$num1".". ".mex("Il codice",$pag)." <input type=\"text\" name=\"codice_promo$num1\" value=\"".${"codice_promo".$num1}."\" size=\"12\">
 <select name=\"tipo_codice_promo$num1\">
<option value=\"+\"$sel_agg>".mex("aggiunge",$pag)."</option>
<option value=\"-\"$sel_rim>".mex("rimuove",$pag)."</option>
</select>
 ".mex("il",$pag)." <select name=\"costo_codice_promo$num1\">";
if (!${"costo_codice_promo".$num1}) ${"costo_codice_promo".$num1} = substr($costi_campi_codici_promo[$num1],1);
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
if ($dati_ca[$numca]['id'] != $costo_aggiungi_letti) {
if ($dati_ca[$numca]['tipo'] == "u") $tipo_costo = "Costo unico";
if ($dati_ca[$numca]['tipo'] == "s") $tipo_costo = "Costo $parola_settimanale";
if (${"costo_codice_promo".$num1} == $dati_ca[$numca]['id']) $sel = " selected";
else $sel = "";
echo "<option value=\"".$dati_ca[$numca]['id']."\"$sel>".strtolower(mex("$tipo_costo",$pag))." \"".$dati_ca[$numca]['nome']."\"</option>";
} # fine if ($dati_ca[$numca]['id'] != $costo_aggiungi_letti)
} # fine for $numca
echo "</select><br>";
} # fine for $num1
echo "</td><td style=\"width: 20px;\"></td><td valign=\"bottom\">";
if ($num_codici_promo > 1) echo "<input class=\"sbutton\" type=\"submit\" name=\"eliminacodpromo\" onclick=\"document.getElementById('fcrmod').action += '#codprom'\" value=\"".mex("-",$pag)."\">&nbsp;";
echo "<input class=\"sbutton\" type=\"submit\" name=\"aggiungicodpromo\" onclick=\"document.getElementById('fcrmod').action += '#codprom'\" value=\"".mex("+",$pag)."\">
<input type=\"hidden\" name=\"num_codici_promo\" value=\"$num_codici_promo\"></td></tr></table><br>";

if (!$assegna_con_regola2 or strtoupper($assegna_con_regola2) == $SI) echo "<input type=\"hidden\" name=\"assegna_con_regola2\" value=\"SI\">";
else echo "<input type=\"hidden\" name=\"assegna_con_regola2\" value=\"NO\">";
/*
if (!$assegna_con_regola2 or strtoupper($assegna_con_regola2) == $SI) { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
echo "".mex("Assegnare gli appartamenti in base alla tariffa scelta con la regola 2?",'unit.php')."
<select name=\"assegna_con_regola2\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><br>";
*/

echo "".mex("Motivazioni delle regole di assegnazone 1 per disponibilità condizionata da tenere in conto",$pag).":<br>
<table style=\"margin-left: auto; margin-right: auto;\" border=\"1\" cellspacing=\"0\" cellpadding=\"4\">";
$celle = 1;
$num_colonne = 3;
$tableregole_modello = $PHPR_TAB_PRE."regole".$anno_modello;
$regole = esegui_query("select * from $tableregole_modello where app_agenzia != '' and (motivazione2 != 'x' or motivazione2 is NULL) order by app_agenzia");
$num_regole = numlin_query($regole);
unset($motivazioni_presenti);
$num_motivazioni = 0;
for ($num1 = 0 ; $num1 < $num_regole ; $num1 = $num1 + 1) {
if ($celle == 1) echo "<tr>";
$idregole = risul_query($regole,$num1,'idregole');
$motivazione = risul_query($regole,$num1,'motivazione');
if (!$motivazione) {
$motivazione = " ";
$motivazione_vedi = mex("nessuna",$pag);
} # fine if (!$motivazione)
else $motivazione_vedi = $motivazione;
if ($motivazioni_presenti[$motivazione] != "SI") {
$motivazioni_presenti[$motivazione] = "SI";
$var_motivazione = "var_mot_".$num_motivazioni;
$num_motivazioni++;
if (!$considera_motivazioni_regola1["array_esistente"] or strtoupper($considera_motivazioni_regola1[$motivazione]) == $SI) $checked = " checked";
else $checked = "";
echo "<td><label><input type=\"checkbox\" name=\"$var_motivazione\" value=\"$motivazione\"$checked> $motivazione_vedi</label></td>";
if ($celle == $num_colonne) {
echo "</tr>";
$celle = 0;
} # fine if ($celle == 3)
$celle++;
} # fine if ($motivazioni_presenti[$motivazione] != "SI")
} # fine for $num1
if ($celle != 1) {
for ($num1 = $celle ; $num1 <= $num_colonne ; $num1++) echo "<td>&nbsp;</td>";
echo "</tr>";
} # fine if ($celle != 1)
elseif ($num_regole == 0) echo "<tr><td style=\"width: 250px;\">&nbsp;</td></tr>";
echo "</table>
<div style=\"font-size: smaller; text-align: center;\">(".mex("le regole 1 di chiusura vengono sempre applicate",$pag).")</div>";
if (!$mostra_frase_alternativa_regola1 or strtoupper($mostra_frase_alternativa_regola1) == $NO) { $check_NO = " checked"; $check_SI = ""; }
else { $check_SI = " checked"; $check_NO = ""; }
if ($fr_alternativa_regola1) $val = htmlspecialchars($fr_alternativa_regola1);
else $val = mex2("Disponibilità incerta per la tipologia scelta, per ulteriori informazioni contattateci via email",$pag,$lingua_modello);
echo "<input type=\"hidden\" name=\"num_motivazioni\" value=\"$num_motivazioni\">
".mex("Se si deve occupare un appartamento della regola 1 con una delle motivazioni selezionate",'unit.php').":
<table border=0 cellspacing=0 cellpadding=0><tr>
<td style=\"width: 20px;\"></td><td align=\"left\">
<label><input type=\"radio\" name=\"mostra_frase_alternativa_regola1\" value=\"NO\"$check_NO> ".mex("comportati come se non vi fosse più disponibilità",$pag)."</label><br>
<label><input type=\"radio\" name=\"mostra_frase_alternativa_regola1\" value=\"SI\"$check_SI> ".mex("dai ancora disponibilità con questa frase alternativa",$pag)."</label>:
 <input type=\"text\" name=\"frase_alternativa_regola1\" size=\"65\" value=\"$val\">
</td></tr></table><br>";

if (!$mostra_caparra or strtoupper($mostra_caparra) == $SI) { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
echo "".mex("Mostrare la caparra se presente?",$pag)."
<select name=\"mostra_caparra\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br>";
if (!$mostra_giorni_pieni or strtoupper($mostra_giorni_pieni) == $NO) { $sel_SI = ""; $sel_NO = " selected"; }
else { $sel_NO = ""; $sel_SI = " selected"; }
echo "".mex("Mostrare quali sono i giorni pieni all'interno dei periodi dove non c'è più disponibilità?",$pag)."
<select name=\"mostra_giorni_pieni\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><br>";

$dati_struttura = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'dati_struttura' and idutente = '$id_utente'");
$dati_struttura = explode("#@&",risul_query($dati_struttura,0,'valpersonalizza'));
if (!$mostra_richiesta_via_mail or strtoupper($mostra_richiesta_via_mail) == $SI) { $sel_NO = ""; $sel_SI = " selected"; }
else { $sel_SI = ""; $sel_NO = " selected"; }
if (!$indirizzo_email and $modello_esistente != "SI") $indirizzo_email = $dati_struttura[2];
if (!$ind_email and !$form_ricaricata) $ind_email = $indirizzo_email;
if (defined('C_RESTRIZIONI_DEMO_ADMIN') and C_RESTRIZIONI_DEMO_ADMIN == "SI") { $ind_email = C_EMAIL_DEMO_ADMIN; $readonly = " readonly=\"readonly\""; }
else $readonly = "";
echo "".mex("Mostrare la form di richiesta prenotazione?",$pag)."
<select name=\"mostra_richiesta_via_mail\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>
".mex("Inviare la richiesta di prenotazione come messaggio a",$pag)."
 <select name=\"utente_mess\">";
if ($modello_esistente != "SI") {
$utente_messaggio = esegui_query("select nome_utente from $tableutenti where idutenti = '1'");
$utente_messaggio = risul_query($utente_messaggio,0,'nome_utente');
} # fine if ($modello_esistente != "SI")
if (!$utente_mess and !$form_ricaricata) $utente_mess = $utente_messaggio;
if (!$utente_mess) $sel_nessuno = " selected";
else $sel_nessuno = "";
if ($utente_messaggio == mex("tutti",$pag)) $sel_tutti = " selected";
else $sel_tutti = "";
echo "<option value=\"\"$sel_nessuno>".mex("----",$pag)."</option>
<option value=\"tutti\"$sel_tutti>".mex("tutti",$pag)."</option>";
echo str_replace("\"$utente_mess\">","\"$utente_mess\" selected>",$option_utenti)."
</select><br>";

$origini_prenota = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'origini_prenota' and idutente = '1'");
$origini_prenota = risul_query($origini_prenota,0,'valpersonalizza');
if ($origini_prenota) {
if ($orig_prenota) $origine_prenotazione = $orig_prenota;
echo "<table><tr><td style=\"width: 25px;\"></td><td>
".mex("Origine della prenotazione",$pag).":
<select name=\"orig_prenota\">
<option value=\"\">----</option>";
$origini_prenota = explode(",",$origini_prenota);
$num_origini_prenota = count($origini_prenota);
for ($num1 = 0 ; $num1 < $num_origini_prenota ; $num1++) {
$origine_p = $origini_prenota[$num1];
if ($origine_p == $origine_prenotazione) $sel = " selected";
else $sel = "";
echo "<option value=\"".htmlspecialchars($origine_p)."\"$sel>$origine_p</option>";
} # fine for $num1
echo "</select></td></tr></table>";
} # fine if ($origini_prenota)

echo "".mex("Indirizzo email a cui inviare le richieste di prenotazione",$pag).":
 <input type=\"text\"$readonly name=\"ind_email\" value=\"$ind_email\" size=\"25\"><br>";
if (strtoupper($manda_copia_richiesta_email) == $SI) { $sel_NO = ""; $sel_SI = " selected"; }
else { $sel_SI = ""; $sel_NO = " selected"; }
echo "".mex("Inviare una copia della email di richiesta prenotazione al richiedente?",$pag)."
<select name=\"manda_copia_richiesta_email\">";
if (!defined('C_RESTRIZIONI_DEMO_ADMIN') or C_RESTRIZIONI_DEMO_ADMIN != "SI") echo "<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>";
echo "<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br>";
if (!defined("C_MASCHERA_EMAIL") or C_MASCHERA_EMAIL == "") {
if (!$maschera_email) {
$maschera_email = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'maschera_email' and idutente = '1'");
$maschera_email = risul_query($maschera_email,0,'valpersonalizza');
if ($maschera_email == "NO") $maschera_email = $NO;
else $maschera_email = $SI;
} # fine if (!$maschera_email)
if (strtoupper($maschera_email) == $NO) { $sel_SI = ""; $sel_NO = " selected"; }
else { $sel_NO = ""; $sel_SI = " selected"; }
echo "".mex("Mascherare la provenienza dell'email sull'envelope?",$pag)."
<select name=\"maschera_envelope\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select> (".mex("provare a cambiare se non si riescono a spedire le email",$pag).")<br>";
} # fine if (!defined("C_MASCHERA_EMAIL") or C_MASCHERA_EMAIL == "")
echo "".mex("Campi della form da chiedere",$pag).":<br>
<table>";
$f_necessario = mex("necessario",$pag);
$f_opzionale = mex("opzionale",$pag);
$f_non_chiedere = mex("non chiedere",$pag);
if (!$chiedi_cognome) $chiedi_cognome = $SI;
if ($chiedi_cognome == "SI") $chiedi_cognome = $SI;
if ($chiedi_cognome == "NO") $chiedi_cognome = $NO;
$chiedi_cognome = strtoupper($chiedi_cognome);
if ($chiedi_cognome != $SI and $chiedi_cognome != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_cognome == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_cognome == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td style=\"width: 25px;\"></td><td>".mex("Cognome",$pag)." <select name=\"chiedi_cognome\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_nome) $chiedi_nome = $SI;
if ($chiedi_nome == "SI") $chiedi_nome = $SI;
if ($chiedi_nome == "NO") $chiedi_nome = $NO;
$chiedi_nome = strtoupper($chiedi_nome);
if ($chiedi_nome != $SI and $chiedi_nome != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_nome == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_nome == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td style=\"width: 15px;\"></td><td>".mex("Nome",$pag)." <select name=\"chiedi_nome\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_email) $chiedi_email = $SI;
if ($chiedi_email == "SI") $chiedi_email = $SI;
if ($chiedi_email == "NO") $chiedi_email = $NO;
$chiedi_email = strtoupper($chiedi_email);
if ($chiedi_email != $SI and $chiedi_email != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_email == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_email == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Email",$pag)." <select name=\"chiedi_email\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_sesso) $chiedi_sesso = $NO;
if ($chiedi_sesso == "SI") $chiedi_sesso = $SI;
if ($chiedi_sesso == "NO") $chiedi_sesso = $NO;
$chiedi_sesso = strtoupper($chiedi_sesso);
if ($chiedi_sesso != $SI and $chiedi_sesso != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_sesso == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_sesso == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Genere",$pag)." <select name=\"chiedi_sesso\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_datanascita) $chiedi_datanascita = $NO;
if ($chiedi_datanascita == "SI") $chiedi_datanascita = $SI;
if ($chiedi_datanascita == "NO") $chiedi_datanascita = $NO;
$chiedi_datanascita = strtoupper($chiedi_datanascita);
if ($chiedi_datanascita != $SI and $chiedi_datanascita != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_datanascita == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_datanascita == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Data di nascita",$pag)." <select name=\"chiedi_datanascita\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_documento) $chiedi_documento = $NO;
if ($chiedi_documento == "SI") $chiedi_documento = $SI;
if ($chiedi_documento == "NO") $chiedi_documento = $NO;
$chiedi_documento = strtoupper($chiedi_documento);
if ($chiedi_documento != $SI and $chiedi_documento != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_documento == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_documento == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Documento",$pag)." <select name=\"chiedi_documento\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_nazione) $chiedi_nazione = "opzionale";
if ($chiedi_nazione == "SI") $chiedi_nazione = $SI;
if ($chiedi_nazione == "NO") $chiedi_nazione = $NO;
$chiedi_nazione = strtoupper($chiedi_nazione);
if ($chiedi_nazione != $SI and $chiedi_nazione != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_nazione == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_nazione == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Nazione",$pag)." <select name=\"chiedi_nazione\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_regione) $chiedi_regione = $NO;
if ($chiedi_regione == "SI") $chiedi_regione = $SI;
if ($chiedi_regione == "NO") $chiedi_regione = $NO;
$chiedi_regione = strtoupper($chiedi_regione);
if ($chiedi_regione != $SI and $chiedi_regione != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_regione == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_regione == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Regione",$pag)." <select name=\"chiedi_regione\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_citta) $chiedi_citta = $NO;
if ($chiedi_citta == "SI") $chiedi_citta = $SI;
if ($chiedi_citta == "NO") $chiedi_citta = $NO;
$chiedi_citta = strtoupper($chiedi_citta);
if ($chiedi_citta != $SI and $chiedi_citta != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_citta == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_citta == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Città",$pag)." <select name=\"chiedi_citta\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_via) $chiedi_via = $NO;
if ($chiedi_via == "SI") $chiedi_via = $SI;
if ($chiedi_via == "NO") $chiedi_via = $NO;
$chiedi_via = strtoupper($chiedi_via);
if ($chiedi_via != $SI and $chiedi_via != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_via == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_via == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Via",$pag)." <select name=\"chiedi_via\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_numcivico) $chiedi_numcivico = $NO;
if ($chiedi_numcivico == "SI") $chiedi_numcivico = $SI;
if ($chiedi_numcivico == "NO") $chiedi_numcivico = $NO;
$chiedi_numcivico = strtoupper($chiedi_numcivico);
if ($chiedi_numcivico != $SI and $chiedi_numcivico != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_numcivico == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_numcivico == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Numero civico",$pag)." <select name=\"chiedi_numcivico\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_cap) $chiedi_cap = $NO;
if ($chiedi_cap == "SI") $chiedi_cap = $SI;
if ($chiedi_cap == "NO") $chiedi_cap = $NO;
$chiedi_cap = strtoupper($chiedi_cap);
if ($chiedi_cap != $SI and $chiedi_cap != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_cap == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_cap == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Codice postale",$pag)." <select name=\"chiedi_cap\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_telefono) $chiedi_telefono = "opzionale";
if ($chiedi_telefono == "SI") $chiedi_telefono = $SI;
if ($chiedi_telefono == "NO") $chiedi_telefono = $NO;
$chiedi_telefono = strtoupper($chiedi_telefono);
if ($chiedi_telefono != $SI and $chiedi_telefono != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_telefono == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_telefono == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Telefono",$pag)." <select name=\"chiedi_telefono\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_telefono2) $chiedi_telefono2 = $NO;
if ($chiedi_telefono2 == "SI") $chiedi_telefono2 = $SI;
if ($chiedi_telefono2 == "NO") $chiedi_telefono2 = $NO;
$chiedi_telefono2 = strtoupper($chiedi_telefono2);
if ($chiedi_telefono2 != $SI and $chiedi_telefono2 != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_telefono2 == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_telefono2 == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Secondo telefono",$pag)." <select name=\"chiedi_telefono2\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_telefono3) $chiedi_telefono3 = $NO;
if ($chiedi_telefono3 == "SI") $chiedi_telefono3 = $SI;
if ($chiedi_telefono3 == "NO") $chiedi_telefono3 = $NO;
$chiedi_telefono3 = strtoupper($chiedi_telefono3);
if ($chiedi_telefono3 != $SI and $chiedi_telefono3 != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_telefono3 == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_telefono3 == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Terzo telefono",$pag)." <select name=\"chiedi_telefono3\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_fax) $chiedi_fax = $NO;
if ($chiedi_fax == "SI") $chiedi_fax = $SI;
if ($chiedi_fax == "NO") $chiedi_fax = $NO;
$chiedi_fax = strtoupper($chiedi_fax);
if ($chiedi_fax != $SI and $chiedi_fax != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_fax == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_fax == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Fax",$pag)." <select name=\"chiedi_fax\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_codfiscale) $chiedi_codfiscale = $NO;
if ($chiedi_codfiscale == "SI") $chiedi_codfiscale = $SI;
if ($chiedi_codfiscale == "NO") $chiedi_codfiscale = $NO;
$chiedi_codfiscale = strtoupper($chiedi_codfiscale);
if ($chiedi_codfiscale != $SI and $chiedi_codfiscale != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_codfiscale == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_codfiscale == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Codice fiscale",$pag)." <select name=\"chiedi_codfiscale\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_partitaiva) $chiedi_partitaiva = $NO;
if ($chiedi_partitaiva == "SI") $chiedi_partitaiva = $SI;
if ($chiedi_partitaiva == "NO") $chiedi_partitaiva = $NO;
$chiedi_partitaiva = strtoupper($chiedi_partitaiva);
if ($chiedi_partitaiva != $SI and $chiedi_partitaiva != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_partitaiva == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_partitaiva == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td>".mex("Partita iva",$pag)." <select name=\"chiedi_partitaiva\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_commento) $chiedi_commento = "opzionale";
if ($chiedi_commento == "SI") $chiedi_commento = $SI;
if ($chiedi_commento == "NO") $chiedi_commento = $NO;
$chiedi_commento = strtoupper($chiedi_commento);
if ($chiedi_commento != $SI and $chiedi_commento != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_commento == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_commento == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td>".mex("Commento",$pag)." <select name=\"chiedi_commento\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td>";
if (!$chiedi_oracheckin) $chiedi_oracheckin = $NO;
if ($chiedi_oracheckin == "SI") $chiedi_oracheckin = $SI;
if ($chiedi_oracheckin == "NO") $chiedi_oracheckin = $NO;
$chiedi_oracheckin = strtoupper($chiedi_oracheckin);
if ($chiedi_oracheckin != $SI and $chiedi_oracheckin != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_oracheckin == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_oracheckin == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<td></td><td><small>".mex("Orario stimato di arrivo",$pag)."</small> <select name=\"chiedi_oracheckin\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td></tr>";
if (!$chiedi_metodopagamento) $chiedi_metodopagamento = $NO;
if ($chiedi_metodopagamento == "SI") $chiedi_metodopagamento = $SI;
if ($chiedi_metodopagamento == "NO") $chiedi_metodopagamento = $NO;
$chiedi_metodopagamento = strtoupper($chiedi_metodopagamento);
if ($chiedi_metodopagamento != $SI and $chiedi_metodopagamento != $NO) { $sel_SI = ""; $sel_opz = " selected"; $sel_NO = ""; }
if ($chiedi_metodopagamento == $SI) { $sel_SI = " selected"; $sel_opz = ""; $sel_NO = ""; }
if ($chiedi_metodopagamento == $NO) { $sel_SI = ""; $sel_opz = ""; $sel_NO = " selected"; }
echo "<tr><td></td><td><small>".mex("Metodo di pagamento della caparra",$pag)."</small> <select name=\"chiedi_metodopagamento\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
<option value=\"NO\"$sel_NO>$f_non_chiedere</option>
</select></td><td></td><td></td></tr>";

$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '1'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
if ($num_metodi_pagamento) $utilizza_var_passate = "SI";
if ($metodi_pagamento) {
echo "<tr><td></td><td colspan=\"3\">".mex("Metodi di pagamento della caparra da chiedere e loro eventuali nomi sostitutivi",$pag).":<br>";
$metodi_pagamento = explode(",",$metodi_pagamento);
$num_metodi_pagamento = count($metodi_pagamento);
echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"4\">";
$celle = 1;
$num_colonne = 2;
for ($num1 = 0 ; $num1 < $num_metodi_pagamento ; $num1++) {
if ($celle == 1) echo "<tr>";
$metodo = $metodi_pagamento[$num1];
if ($utilizza_var_passate == "SI") {
if (${"var_met_paga_".$num1}) $checked = " checked";
else $checked = "";
if (${"nome_met_paga_imposto_".$num1}) $val = ${"nome_met_paga_imposto_".$num1};
else $val = "";
} # fine if ($utilizza_var_passate == "SI")
else {
if (!$metodi_pagamento_da_chiedere['array_esistente'] or strtoupper($metodi_pagamento_da_chiedere[$metodo]) == $SI) $checked = " checked";
else $checked = "";
if ($nomi_metodi_pagamento_imposti[$metodo]) $val = $nomi_metodi_pagamento_imposti[$metodo];
else $val = "";
} # fine else if ($utilizza_var_passate == "SI")
echo "<td><label><input type=\"checkbox\" name=\"var_met_paga_"."$num1\" value=\"$metodo\"$checked>$metodo"."</label>:
 <input type=\"text\" name=\"nome_met_paga_imposto_"."$num1\" value=\"$val\" size=\"22\"></td>";
if ($celle == $num_colonne) {
echo "</tr>";
$celle = 0;
} # fine if ($celle == 3)
$celle++;
} # fine for $num1
if ($celle != 1) {
for ($num1 = $celle ; $num1 <= $num_colonne ; $num1++) echo "<td>&nbsp;</td>";
echo "</tr>";
} # fine if ($celle != 1)
echo "</table>
<input type=\"hidden\" name=\"num_metodi_pagamento\" value=\"$num_metodi_pagamento\">
</td></tr>";
} # fine if ($metodi_pagamento)
echo "<tr><td style=\"height: 5px;\"></td></tr></table>";

$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '$id_utente'");
if (numlin_query($campi_pers_comm) == 1) $campi_pers_comm = explode(">",risul_query($campi_pers_comm,0,'valpersonalizza'));
else $campi_pers_comm = "";
echo "<a name=\"campers\"></a>".mex("Campi della form personalizzati",$pag).":<br>
<table><tr><td style=\"width: 25px;\"></td><td valign=\"top\">";
if (!$num_campi_pers and $campi_form_personalizzati['array_esistente']) $num_campi_pers = (count($campi_form_personalizzati) - 1);
if (!$num_campi_pers or controlla_num_pos($num_campi_pers) == "NO") $num_campi_pers = 1;
for ($num1 = 1 ; $num1 <= $num_campi_pers ; $num1++) {
if (!${"campo_pers".$num1}) ${"campo_pers".$num1} = $campi_form_personalizzati[$num1];
if (!${"chiedi_campo_pers".$num1}) ${"chiedi_campo_pers".$num1} = $chiedi_campi_form_personalizzati[$num1];
if (${"chiedi_campo_pers".$num1} == "SI") ${"chiedi_campo_pers".$num1} = $SI;
if (${"chiedi_campo_pers".$num1} == $SI) { $sel_SI = " selected"; $sel_opz = ""; }
else { $sel_SI = ""; $sel_opz = " selected"; }
echo "$num1".". <input type=\"text\" name=\"campo_pers$num1\" value=\"".${"campo_pers".$num1}."\" size=\"25\">
 <select name=\"chiedi_campo_pers$num1\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale</option>
</select>";
if ($campi_pers_comm) {
echo " <select name=\"ins_campo_pers$num1\">
<option value=\"\">".mex("aggiunto al commento",$pag)."</option>";
if (!${"ins_campo_pers".$num1}) ${"ins_campo_pers".$num1} = $ins_campi_form_personalizzati[$num1];
for ($num2 = 0 ; $num2 < count($campi_pers_comm) ; $num2++) {
if (${"ins_campo_pers".$num1} == $campi_pers_comm[$num2]) $sel = " selected";
else $sel = "";
echo "<option value=\"".$campi_pers_comm[$num2]."\"$sel>".mex("aggiunto al commento",$pag)." \"".$campi_pers_comm[$num2]."\"</option>";
} # fine for $num2
echo "</select><br>";
} # fine if ($campi_pers_comm)
else echo " (".mex("aggiunto al commento",$pag).")<br>";
} # fine for $num1
echo "</td><td style=\"width: 20px;\"></td><td valign=\"bottom\">";
if ($num_campi_pers > 1) echo "<input class=\"sbutton\" type=\"submit\" name=\"eliminacampipers\" onclick=\"document.getElementById('fcrmod').action += '#campers'\" value=\"".mex("-",$pag)."\">&nbsp;";
echo "<input class=\"sbutton\" type=\"submit\" name=\"aggiungicampipers\" onclick=\"document.getElementById('fcrmod').action += '#campers'\" value=\"".mex("+",$pag)."\">
<input type=\"hidden\" name=\"num_campi_pers\" value=\"$num_campi_pers\"></td></tr></table>";

$lista_contr = "";
$nomi_contratti = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '1'");
$nomi_contratti = risul_query($nomi_contratti,0,'valpersonalizza');
$nomi_contratti = explode("#@&",$nomi_contratti);
$num_nomi_contratti = count($nomi_contratti);
for ($num1 = 0 ; $num1 < $num_nomi_contratti ; $num1++) {
$dati_nome_contratto = explode("#?&",$nomi_contratti[$num1]);
$nome_contratto[$dati_nome_contratto[0]] = $dati_nome_contratto[1];
} # fine for $num1
# nomi dei contratti dell'utente delle liste
$nome_contratto_ut = $nome_contratto;
if (controlla_num_pos($num_utenti[$utente_lis]) == "SI" and $num_utenti[$utente_lis] != "1") {
$nomi_contratti = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '".$num_utenti[$utente_lis]."'");
if (numlin_query($nomi_contratti)) {
$nomi_contratti = risul_query($nomi_contratti,0,'valpersonalizza');
$nomi_contratti = explode("#@&",$nomi_contratti);
$num_nomi_contratti = count($nomi_contratti);
for ($num1 = 0 ; $num1 < $num_nomi_contratti ; $num1++) {
$dati_nome_contratto = explode("#?&",$nomi_contratti[$num1]);
if (strcmp($dati_nome_contratto[1],"")) $nome_contratto_ut[$dati_nome_contratto[0]] = $dati_nome_contratto[1];
} # fine for $num1
} # fine if (numlin_query($nomi_contratti))
} # fine if (controlla_num_pos($num_utenti[$utente_lis]) == "SI" and $num_utenti[$utente_lis] != "1")
$contr_txt = esegui_query("select * from $tablecontratti where tipo = 'contrtxt' or tipo = 'contrhtm' order by numero ");
for ($num1 = 0 ; $num1 < numlin_query($contr_txt) ; $num1++) {
$num_contr = risul_query($contr_txt,$num1,'numero');
$salva_contr = esegui_query("select * from $tablecontratti where numero = '$num_contr' and tipo = 'dir' ");
if (!numlin_query($salva_contr)) {
$nome_contr = mex("documento",$pag).$num_contr;
if (strcmp($nome_contratto_ut[$num_contr],"")) $nome_contr .= " (".$nome_contratto_ut[$num_contr].")";
$lista_contr .= "<option value=\"$num_contr\">$nome_contr</option>";
#if (risul_query($contr_txt,$num1,'tipo') == "contrtxt") $lista_contr_txt .= "<option value=\"$num_contr\">$nome_contr</option>";
} # fine if (!numlin_query($salva_contr))
} # fine for $num1
if ($lista_contr) {
$campi_pers_cliente = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente'");
if (numlin_query($campi_pers_cliente) == 1) $campi_pers_cliente = explode(">",risul_query($campi_pers_cliente,0,'valpersonalizza'));
else $campi_pers_cliente = array();
echo "<table><tr><td style=\"height: 5px;\"></td></tr></table>
<a name=\"doccond\"></a>".mex("Condizioni da accettare nella form",$pag)." (".mex("nomi dall'utente delle liste",$pag)."):<br>
<table><tr><td style=\"width: 25px;\"></td><td valign=\"top\">";
if (!$num_campi_doc_cond and $campi_form_doc_condizioni['array_esistente']) $num_campi_doc_cond = (count($campi_form_doc_condizioni) - 1);
if (!$num_campi_doc_cond or controlla_num_pos($num_campi_doc_cond) == "NO") $num_campi_doc_cond = 1;
for ($num1 = 1 ; $num1 <= $num_campi_doc_cond ; $num1++) {
$num_doc_cond = "num_doc_cond".$num1;
if (!$$num_doc_cond) $$num_doc_cond = $campi_form_doc_condizioni[$num1];
if (!$$num_doc_cond) $sel = " selected";
else $sel = "";
if (!${"chiedi_num_doc_cond".$num1}) ${"chiedi_num_doc_cond".$num1} = $chiedi_campi_form_doc_condizioni[$num1];
if (${"chiedi_num_doc_cond".$num1} == "SI") ${"chiedi_num_doc_cond".$num1} = $SI;
if (!${"chiedi_num_doc_cond".$num1} or ${"chiedi_num_doc_cond".$num1} == $SI) $sel_SI = " selected";
else $sel_SI = "";
if (${"chiedi_num_doc_cond".$num1} == "opzionale" or ${"chiedi_num_doc_cond".$num1} == $f_opzionale) $sel_opz = " selected";
else $sel_opz = "";
echo " $num1. <select name=\"num_doc_cond$num1\">
<option value=\"\"$sel>----</option>
".str_replace("\"".$$num_doc_cond."\">","\"".$$num_doc_cond."\" selected>",$lista_contr)."
</select> <select name=\"chiedi_num_doc_cond$num1\">
<option value=\"SI\"$sel_SI>$f_necessario</option>
<option value=\"opzionale\"$sel_opz>$f_opzionale, ".mex("aggiunto al commento",$pag)."</option>";
for ($num2 = 0 ; $num2 < count($campi_pers_cliente) ; $num2++) {
$campo_pers_cliente = explode("<",$campi_pers_cliente[$num2]);
$campo_pers_cliente = $campo_pers_cliente[0];
if (${"chiedi_num_doc_cond".$num1} == "op_".$campo_pers_cliente) $sel = " selected";
else $sel = "";
echo "<option value=\"op_".$campo_pers_cliente."\"$sel>$f_opzionale, ".mex("aggiunto al campo",$pag)." \"$campo_pers_cliente\" ".mex("del cliente",$pag)."</option>";
} # fine for $num2
echo "</select><br>";
} # fine for $num1
echo "</td><td style=\"width: 14px;\"></td><td valign=\"bottom\">";
if ($num_campi_doc_cond > 1) echo "&nbsp;<input class=\"sbutton\" type=\"submit\" name=\"eliminacampicond\" onclick=\"document.getElementById('fcrmod').action += '#doccond'\" value=\"".mex("-",$pag)."\">";
echo "&nbsp;<input class=\"sbutton\" type=\"submit\" name=\"aggiungicampicond\" onclick=\"document.getElementById('fcrmod').action += '#doccond'\" value=\"".mex("+",$pag)."\">
<input type=\"hidden\" name=\"num_campi_doc_cond\" value=\"$num_campi_doc_cond\"></td></tr></table>";
} # fine if ($lista_contr)

echo "</td></tr></table><br>";

if (@is_dir("./includes/templates/pay")) {
if (!$mostra_bottone_paypal or strtoupper($mostra_bottone_paypal) == $NO) { $sel_SI = ""; $sel_NO = " selected"; }
else { $sel_NO = ""; $sel_SI = " selected"; }
if ($nome_modello_paypal) $val = $nome_modello_paypal;
else {
$template_name_orig = $template_name;
$template_name_show_orig = $template_name_show;
$template_file_name_orig = $template_file_name;
$template_data_dir_orig = $template_data_dir;
@include("./includes/templates/pay/name.php");
if ($template_file_name[$lingua_modello]) $val = $template_file_name[$lingua_modello];
else $val = $lingua_modello."_".$template_file_name['en'];
$template_name = $template_name_orig;
$template_name_show = $template_name_show_orig;
$template_file_name = $template_file_name_orig;
$template_data_dir = $template_data_dir_orig;
} # fine else if ($nome_modello_paypal)
echo "".mex("Mostrare il bottone per prenotare con la pagina di prenotazione immediata se presente?",$pag)."
<select name=\"mostra_bottone_paypal\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br>
&nbsp;&nbsp;&nbsp;&nbsp;".mex("Nome file della pagina di prenotazione immediata",$pag).":
<input type=\"text\" name=\"nome_modello_paypal\" size=\"65\" value=\"$val\"><br><br>";
} # fine if (@is_dir("./includes/templates/pay"))
else echo "<input type=\"hidden\" name=\"mostra_bottone_paypal\" value=\"NO\">";

$sel_NO = "";
$sel_APP = "";
$sel_REG2 = "";
$sel_PERS = "";
if (!$mostra_quadro_disponibilita or strtoupper($mostra_quadro_disponibilita) == $NO) $sel_NO = " selected";
else {
if (strtoupper($raggruppa_quadro_disponibilita_con_regola_2) == $SI) $sel_REG2 = " selected";
else {
if (strtoupper($raggruppa_quadro_disponibilita_con_persone) == $SI) $sel_PERS = " selected";
else $sel_APP = " selected";
} # fine else if ($raggruppa_quadro_disponibilita_con_regola_2 == $SI)
} # fine else if ($raggruppa_quadro_disponibilita_con_regola_2 == $SI)
echo "".mex("Mostrare il quadro indicativo della disponibilità?",$pag)."
<select name=\"mostra_quadro_disp\">
<option value=\"\"$sel_NO>".mex("Non mostrare",$pag)."</option>
<option value=\"app\"$sel_APP>".mex("Senza raggruppare gli appartamenti",'unit.php')."</option>
<option value=\"reg2\"$sel_REG2>".mex("Raggruppando gli appartamenti con la regola di assegnazione 2",'unit.php')."</option>
<option value=\"pers\"$sel_PERS>".mex("Raggruppando gli appartamenti per numero di persone",'unit.php')."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>";
if (!$mostra_quadro_disponibilita and !$colore_sfondo_quadro_disponibilita) $colore_sfondo_quadro_disponibilita = "#dddddd";
else $colore_sfondo_quadro_disponibilita = htmlspecialchars($colore_sfondo_quadro_disponibilita);
if (!$mostra_quadro_disponibilita and !$colore_inizio_settimana_quadro_disponibilita) $colore_inizio_settimana_quadro_disponibilita = "#bbbbbb";
else $colore_inizio_settimana_quadro_disponibilita = htmlspecialchars($colore_inizio_settimana_quadro_disponibilita);
if (!$mostra_quadro_disponibilita and !$colore_libero_quadro_disponibilita) $colore_libero_quadro_disponibilita = "#0cc80c";
else $colore_libero_quadro_disponibilita = htmlspecialchars($colore_libero_quadro_disponibilita);
if (!$mostra_quadro_disponibilita and !$colore_occupato_quadro_disponibilita) $colore_occupato_quadro_disponibilita = "#f8011e";
else $colore_occupato_quadro_disponibilita = htmlspecialchars($colore_occupato_quadro_disponibilita);
#if (!$mostra_quadro_disponibilita and !$apertura_font_quadro_disponibilita) $apertura_font_quadro_disponibilita = "<font size=&quot;-2&quot;>";
if (!$mostra_quadro_disponibilita and !$apertura_font_quadro_disponibilita) $apertura_font_quadro_disponibilita = "";
else $apertura_font_quadro_disponibilita = htmlspecialchars($apertura_font_quadro_disponibilita);
#if (!$mostra_quadro_disponibilita and !$chiusura_font_quadro_disponibilita) $chiusura_font_quadro_disponibilita = "</font>";
if (!$mostra_quadro_disponibilita and !$chiusura_font_quadro_disponibilita) $chiusura_font_quadro_disponibilita = "";
else $chiusura_font_quadro_disponibilita = htmlspecialchars($chiusura_font_quadro_disponibilita);
echo "".mex("Colore di sfondo della tabella",$pag).":
 <input type=\"text\" name=\"colore_sfondo_quadro_disponibilita\" value=\"$colore_sfondo_quadro_disponibilita\" size=\"10\"><br>
".mex("Colore del giorno di inizio settimana",$pag).":
 <input type=\"text\" name=\"colore_inizio_settimana_quadro_disponibilita\" value=\"$colore_inizio_settimana_quadro_disponibilita\" size=\"10\"><br>
".mex("Colore dei periodi liberi",$pag).":
 <input type=\"text\" name=\"colore_libero_quadro_disponibilita\" value=\"$colore_libero_quadro_disponibilita\" size=\"10\"><br>
".mex("Colore dei periodi occupati",$pag).":
 <input type=\"text\" name=\"colore_occupato_quadro_disponibilita\" value=\"$colore_occupato_quadro_disponibilita\" size=\"10\"><br>
".mex("Tag di apertura dei font della tabella",$pag).":
 <input type=\"text\" name=\"apertura_font_quadro_disponibilita\" value=\"$apertura_font_quadro_disponibilita\" size=\"25\"><br>
".mex("Tag di chiusura dei font della tabella",$pag).":
 <input type=\"text\" name=\"chiusura_font_quadro_disponibilita\" value=\"$chiusura_font_quadro_disponibilita\" size=\"25\"><br>";
if (!$mostra_numero_liberi_quadro_disponibilita or strtoupper($mostra_numero_liberi_quadro_disponibilita) == $NO) { $sel_SI = ""; $sel_NO = " selected"; }
else { $sel_NO = ""; $sel_SI = " selected"; }
echo "".mex("Mostrare il numero di appartamenti liberi?",'unit.php')."
<select name=\"mostra_numero_liberi_quadro_disponibilita\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br>";
if (!$allinea_disponibilita_con_arrivo and strtoupper($allinea_disponibilita_con_arrivo) != $SI and $allinea_disponibilita_con_arrivo != "SI") { $sel_SI = ""; $sel_NO = " selected"; }
else { $sel_NO = ""; $sel_SI = " selected"; }
echo mex("Allineare la disponibilità con la data di arrivo?",$pag)."
<select name=\"allinea_disponibilita_con_arrivo\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select>
</td></tr></table><br>";

if (!$mostra_calendario_scelta_date or strtoupper($mostra_calendario_scelta_date) != $NO) { $sel_SI = " selected"; $sel_NO = ""; }
else { $sel_NO = " selected"; $sel_SI = ""; }
echo "".mex("Mostrare i calendari per la scelta delle date?",$pag)."
<select name=\"mostra_calendario_scelta_date\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>";
if ($stile_riquadro_calendario or $modello_esistente == "SI") $val = $stile_riquadro_calendario;
else $val = "style=\"z-index: 1; visibility: hidden; position: absolute; top: 0px; left: 0px; background: #FFFFFF; padding: 2px; border: 1px solid #000000; font: bold 10px Verdana, Arial, Helvetica, sans-serif; color: #000000; text-align: center;\"";
echo "".mex("Stile riquadro calendario",$pag).":
<input type=\"text\" name=\"stile_riquadro_calendario\" size=\"75\" value=\"".htmlspecialchars($val)."\"><br>";
if ($stile_tabella_calendario or $modello_esistente == "SI") $val = $stile_tabella_calendario;
else $val = "style=\"border-collapse: collapse; font-size: 10px; margin-left: auto; margin-right: auto; cursor: default; text-align: center; padding: 2px\"";
echo "".mex("Stile tabella calendario",$pag).":
<input type=\"text\" name=\"stile_tabella_calendario\" size=\"75\" value=\"".htmlspecialchars($val)."\"><br>";
if ($stile_bottoni_calendario or $modello_esistente == "SI") $val = $stile_bottoni_calendario;
else $val = "style=\"font-size: 9px; padding: 0 3px 0 3px; border-color: #333333; border-width: 1px;\"";
echo "".mex("Stile bottoni interni",$pag).":
<input type=\"text\" name=\"stile_bottoni_calendario\" size=\"75\" value=\"".htmlspecialchars($val)."\"><br>";
if ($stile_bottone_apertura_calendario or $modello_esistente == "SI") $val = $stile_bottone_apertura_calendario;
else $val = "style=\"padding: 0; border-color: #333333; border-width: 1px;\"";
echo "".mex("Stile bottone apertura",$pag).":
<input type=\"text\" name=\"stile_bottone_apertura_calendario\" size=\"75\" value=\"".htmlspecialchars($val)."\"><br>";
if ($spostamento_orizzontale_calendario or $modello_esistente == "SI") $val = $spostamento_orizzontale_calendario;
else $val = "-108";
echo "".mex("Spostamento orizzontale della posizione",$pag).":
<input type=\"text\" name=\"spostamento_orizzontale_calendario\" size=\"5\" value=\"".htmlspecialchars($val)."\">px<br>";
if ($colore_data_attiva_calendario or $modello_esistente == "SI") $val = $colore_data_attiva_calendario;
else $val = "#d8e1e6";
echo "".mex("Colore date attive",$pag).":
<input type=\"text\" name=\"colore_data_attiva_calendario\" size=\"10\" value=\"".htmlspecialchars($val)."\"><br>";
if ($colore_data_selezionata_calendario or $modello_esistente == "SI") $val = $colore_data_selezionata_calendario;
else $val = "#eeeeee";
echo "".mex("Colore data selezionata",$pag).":
<input type=\"text\" name=\"colore_data_selezionata_calendario\" size=\"10\" value=\"".htmlspecialchars($val)."\"><br>";
echo "</td></tr></table><br>";

#if (!$apertura_tag_font and $modello_esistente != "SI") $apertura_tag_font = "<font color=&quot;#000000&quot;>";
if (!$apertura_tag_font and $modello_esistente != "SI") $apertura_tag_font = "";
else $apertura_tag_font = htmlspecialchars($apertura_tag_font);
#if (!$chiusura_tag_font and $modello_esistente != "SI") $chiusura_tag_font = "</font>";
if (!$chiusura_tag_font and $modello_esistente != "SI") $chiusura_tag_font = "";
else $chiusura_tag_font = htmlspecialchars($chiusura_tag_font);
echo "".mex("Tag html di apertura per la formattazione delle font",$pag).":
 <input type=\"text\" name=\"apertura_tag_font\" size=\"25\" value=\"$apertura_tag_font\"><br>
".mex("Tag html di chiusura per la formattazione delle font",$pag).":
 <input type=\"text\" name=\"chiusura_tag_font\" size=\"25\" value=\"$chiusura_tag_font\"><br>";
if (!$apertura_tag_font_rosse and $modello_esistente != "SI") $apertura_tag_font_rosse = "<b style=&quot;color: red;&quot;>";
else $apertura_tag_font_rosse = htmlspecialchars($apertura_tag_font_rosse);
if (!$chiusura_tag_font_rosse and $modello_esistente != "SI") $chiusura_tag_font_rosse = "</b>";
else $chiusura_tag_font_rosse = htmlspecialchars($chiusura_tag_font_rosse);
echo "".mex("Tag html di apertura per la formattazione delle font rosse",$pag).":
 <input type=\"text\" name=\"apertura_tag_font_rosse\" size=\"25\" value=\"$apertura_tag_font_rosse\"><br>
".mex("Tag html di chiusura per la formattazione delle font rosse",$pag).":
 <input type=\"text\" name=\"chiusura_tag_font_rosse\" size=\"25\" value=\"$chiusura_tag_font_rosse\"><br>";
if (!$stile_tabella_prenotazione and $modello_esistente != "SI") $stile_tabella_prenotazione = "border=1 cellpadding=5 cellspacing=1";
else $stile_tabella_prenotazione = htmlspecialchars($stile_tabella_prenotazione);
echo "".mex("Stile della tabella che racchiude la form di prenotazione",$pag).":
 <input type=\"text\" name=\"stile_tabella_prenotazione\" size=\"25\" value=\"$stile_tabella_prenotazione\"><br>";

if (!$file_css_frame) $file_css_frame = "http://";
else $file_css_frame = htmlspecialchars($file_css_frame);
echo "<br>".mex("Url del file css per la modalità frame",$pag).":
 <input type=\"text\" name=\"file_css_frame\" size=\"35\" value=\"$file_css_frame\"><br>";
if (!$apri_nuova_finestra_da_frame or strtoupper($apri_nuova_finestra_da_frame) != $SI) { $sel_SI = ""; $sel_NO = " selected"; }
else { $sel_NO = ""; $sel_SI = " selected"; }
if ($larghezza_finestra_da_frame) $val_larghezza = $larghezza_finestra_da_frame;
else $val_larghezza = "700";
if ($altezza_finestra_da_frame) $val_altezza = $altezza_finestra_da_frame;
else $val_altezza = "620";
echo "".mex("Dalla modalidà frame, aprire il secondo passo in una nuova finestra?",$pag)."
<select name=\"apri_nuova_finestra_da_frame\">
<option value=\"SI\"$sel_SI>".mex("SI",$pag)."</option>
<option value=\"NO\"$sel_NO>".mex("NO",$pag)."</option>
</select><br><table><tr><td style=\"width: 25px;\"></td><td>";
echo "".mex("Geometria della nuova finestra",$pag).":
 ".mex("larghezza",$pag)." <input type=\"text\" name=\"larghezza_finestra_da_frame\" size=\"4\" value=\"$val_larghezza\">px,
 ".mex("altezza",$pag)." <input type=\"text\" name=\"altezza_finestra_da_frame\" size=\"4\" value=\"$val_altezza\">px.
</td></tr></table>";

include("./includes/templates/temi_mod_disp.php");
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
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) echo "".mex($frase[$num_fr],$pag).": <input type=\"text\" name=\"".$fr_frase[$num_fr]."\" size=\"45\" value=\"".str_replace("\"","&quot;",${$fr_frase[$num_fr]})."\"><br>";
} # fine if ($modello_esistente == "SI")
else {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) echo "".mex($frase[$num_fr],$pag).": <input type=\"text\" name=\"".$fr_frase[$num_fr]."\" size=\"45\" value=\"".str_replace("\"","&quot;",stripslashes(mex2($frase[$num_fr],$pag,$lingua_modello)))."\"><br>";
} # fine else if ($modello_esistente == "SI")
echo "</td></tr></table>
</td></tr></table>";
} # fine if ($cambia_frasi)
elseif ($modello_esistente == "SI") {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) echo "<input type=\"hidden\" name=\"".$fr_frase[$num_fr]."\" value=\"".str_replace("\"","&quot;",${$fr_frase[$num_fr]})."\">";
} # fine elseif ($modello_esistente == "SI")

echo "<br><div style=\"text-align: center;\"><input type=\"hidden\" name=\"modello_disponibilita\" value=\"SI\">
<input type=\"hidden\" name=\"form_ricaricata\" value=\"SI\">
<button class=\"chav\" type=\"submit\"><div>".mex("Crea la pagina per la disponibilità",$pag)."</div></button>
</div></div></form><br>
<hr style=\"width: 95%\"><br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";
} # fine if ($form_modello_disponibilita)



else {
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if (${"form_".$template_name} and $template_name_show['tpl_type'] != "interconnection") {
$mostra_form_creazione = "NO";
include("./includes/templates/$modello_ext/form.php");
break;
} # fine if (${"form_".$template_name} and $template_name_show['tpl_type'] != "interconnection")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
} # fine else if ($form_modello_disponibilita)



if ($cancella_modelli == "SI") {
if (get_magic_quotes_gpc()) $perc_mod_elimina = stripslashes($perc_mod_elimina);
$mod_presente = "NO";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
$perc_mod = "$percorso_cartella_modello/mdl_disponibilita.php";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) $mod_presente = "SI";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) $mod_presente = "SI";
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
if ($mod_presente != "SI") {
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." && $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if ($template_name_show['tpl_type'] != "interconnection") {
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) $mod_presente = "SI";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name['en'];
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) $mod_presente = "SI";
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($template_name_show['tpl_type'] != "interconnection")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
} # fine if ($mod_presente != "SI")
} # fine for $num_cart
if ($mod_presente == "SI") {
$mostra_form_creazione = "NO";
if (!$continua) {
echo "".mex("Si è sicuri di voler <b style=\"color: red;\">cancellare</b>",$pag)." ";
if ($perc_mod_elimina) echo mex("la pagina",$pag)." \"$perc_mod_elimina\"";
else {
if (str_replace(",","",$perc_cart_mod_int) != $perc_cart_mod_int) echo mex("tutte le pagine create nelle directory",$pag)." \"$perc_cart_mod_int\"";
else echo mex("tutte le pagine create nella directory",$pag)." \"$perc_cart_mod_int\"";
} # fine else if ($perc_mod_elimina)
echo "?<br>
<table><tr><td style=\"height: 2px;\"></td></tr><tr><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"cancella_modelli\" value=\"SI\">
<input type=\"hidden\" name=\"perc_mod_elimina\" value=\"$perc_mod_elimina\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
<button class=\"cnrd\" type=\"submit\"><div>".mex("SI",$pag)."</div></button>
</div></form></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("NO",$pag)."</div></button>
</div></form></td></tr></table>";
} # fine if (!$continua)
else {
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
$perc_mod = "$percorso_cartella_modello/mdl_disponibilita.php";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) unlink($perc_mod);
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) unlink($perc_mod);
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if ($template_name_show['tpl_type'] != "interconnection") {
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) {
unlink($perc_mod);
$perc_inc = "$percorso_cartella_modello/".str_replace(".php","_inc.php",$nome_file);
if (@is_file($perc_inc)) unlink($perc_inc);
} # fine if (@is_file($perc_mod) and...
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name['en'];
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod) and (!$perc_mod_elimina or $perc_mod_elimina == $perc_mod)) {
unlink($perc_mod);
$perc_inc = "$percorso_cartella_modello/".str_replace(".php","_inc.php",$nome_file);
if (@is_file($perc_inc)) unlink($perc_inc);
} # fine if (@is_file($perc_mod) and...
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($template_name_show['tpl_type'] != "interconnection")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
} # fine for $num_cart
if (!$perc_mod_elimina) esegui_query("delete from $tablepersonalizza where idpersonalizza = 'ultime_sel_crea_modelli' and idutente = '$id_utente'");
if ($perc_mod_elimina) echo mex("Pagina cancellata",$pag).".<br>";
else echo mex("Cancellate tutte le pagine",$pag).".<br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div>".mex("OK",$pag)."</div></button>
</div></form>";
} # fine else if (!$continua)
} # fine if ($mod_presente == "SI")
} # fine if ($cancella_modelli == "SI")



if ($importa_modelli == "SI") {
if (get_magic_quotes_gpc()) {
$cartella_da = stripslashes($cartella_da);
$cartella_a = stripslashes($cartella_a);
} # fine if (get_magic_quotes_gpc())
$errore = "NO";
if (!@is_dir($cartella_da) or !@is_dir($cartella_a)) $errore = "SI";
if ($cartella_da == $cartella_a) $errore = "SI";
$cart_da_trovata = "NO";
$cart_a_trovata = "NO";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if ($cartella_da == $perc_cart_mod_vett[$num_cart]) $cart_da_trovata = "SI";
if ($cartella_a == $perc_cart_mod_vett[$num_cart]) $cart_a_trovata = "SI";
} # fine for $num_cart
if ($cart_da_trovata != "SI" or $cart_a_trovata != "SI") $errore = "SI";
if ($errore != "SI") {
$mostra_form_creazione = "NO";
if (!$continua) {
echo "".mex("Si è sicuri di voler importare le pagine dalla cartella",$pag)." \"<b>$cartella_da</b>\" ".mex("alla cartella",$pag)." \"<b>$cartella_a</b>\"?<br>
(".mex("eventuali pagine già presenti nella cartella",$pag)." \"<b>$cartella_a</b>\" ".mex("verranno <b style=\"font-weight: normal; color: red;\">sovrascritte</b>",$pag).")<br>
<table><tr><td style=\"height: 2px;\"></td></tr><tr><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"importa_modelli\" value=\"SI\">
<input type=\"hidden\" name=\"cartella_da\" value=\"$cartella_da\">
<input type=\"hidden\" name=\"cartella_a\" value=\"$cartella_a\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" value=\"".mex("SI",$pag)."\">
</div></form></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input class=\"sbutton\" type=\"submit\" value=\"".mex("NO",$pag)."\">
</div></form></td></tr></table>";
} # fine if (!$continua)
else {
$percorso_cartella_modello = $cartella_da;
$perc_mod = "$percorso_cartella_modello/mdl_disponibilita.php";
if (@is_file($perc_mod)) copy($perc_mod,"$cartella_a/mdl_disponibilita.php");
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod)) copy($perc_mod,"$cartella_a/$nome_file");
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if ($template_name_show['tpl_type'] != "interconnection") {
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod)) {
copy($perc_mod,"$cartella_a/$nome_file");
$nome_file_inc = str_replace(".php","_inc.php",$nome_file);
$perc_inc = "$percorso_cartella_modello/$nome_file_inc";
if (@is_file($perc_inc)) copy($perc_inc,"$cartella_a/$nome_file_inc");
} # fine if (@is_file($perc_mod))
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name["en"];
$perc_mod = "$percorso_cartella_modello/$nome_file";
if (@is_file($perc_mod)) {
copy($perc_mod,"$cartella_a/$nome_file");
$nome_file_inc = str_replace(".php","_inc.php",$nome_file);
$perc_inc = "$percorso_cartella_modello/$nome_file_inc";
if (@is_file($perc_inc)) copy($perc_inc,"$cartella_a/$nome_file_inc");
} # fine if (@is_file($perc_mod))
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($template_name_show['tpl_type'] != "interconnection")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);
echo mex("Pagine importate",$pag).".<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"cont\" type=\"submit\"><div<".mex("OK",$pag)."</div></button>
</div></form>";
} # fine else if (!$continua)
} # fine if ($errore != "SI")
} # fine if ($importa_modelli == "SI")




if ($mostra_form_creazione != "NO") {

echo "<h4 id=\"h_webs\"><span>".mex("Crea pagine per il sito web",$pag).".</span></h4>";

echo "<br><div style=\"text-align: center;\">".mex("Dati comuni",$pag)."</div><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<label><input type=\"radio\" name=\"fonte_dati_conn\" value=\"attuali\" checked> ".mex("Utilizza i dati attuali per la connessione al database",$pag)."</label><br>";
if (!defined('C_BACKUP_E_MODELLI_CON_NUOVI_DATI') or C_BACKUP_E_MODELLI_CON_NUOVI_DATI != "NO") {
echo "<label><input type=\"radio\" name=\"fonte_dati_conn\" value=\"nuovi\"> ".mex("Utilizza altri dati per la connessione al database",$pag)."</label>:<br>
<table><tr><td style=\"width: 25px;\"></td><td>
".mex("Tipo di database",$pag).": 
<select name=\"T_PHPR_DB_TYPE\">
<option value=\"postgresql\" selected>".mex("Postgresql",$pag)."</option>
<option value=\"mysql\">".mex("Mysql",$pag)."</option>
<option value=\"sqlite\">".mex("Sqlite",$pag)."</option>
</select><br>
".mex("Nome del database da utilizzare",$pag).": 
<input type=\"text\" name=\"T_PHPR_DB_NAME\"><br>
".mex("Nome del computer a cui collegarsi",$pag).":
<input type=\"text\" name=\"T_PHPR_DB_HOST\" value=\"localhost\"><br>
".mex("Numero della porta a cui collegarsi",$pag).": 
<input type=\"text\" name=\"T_PHPR_DB_PORT\" value=\"5432\">(".mex("Normalmete 5432 per Postgresql o 3306 per Mysql",$pag).")<br>
".mex("Nome per l'autenticazione al database",$pag).": 
<input type=\"text\" name=\"T_PHPR_DB_USER\"><br>
".mex("Parola segreta per l'autenticazione al database",$pag).": 
<input type=\"text\" name=\"T_PHPR_DB_PASS\"><br>
".mex("Caricare la libreria dinamica \"pgsql.so\" o \"mysql.so\"",$pag)."?
<select name=\"T_PHPR_LOAD_EXT\">
<option value=\"SI\">".mex("Si",$pag)."</option>
<option value=\"NO\" selected>".mex("No",$pag)."</option>
</select> <small>(".mex("scegliere si se non viene caricata automaticamente da php",$pag).")</small><br>
".mex("Prefisso nel nome delle tabelle",$pag).":
<input type=\"text\" name=\"T_PHPR_TAB_PRE\" maxlength=\"8\" size=\"9\">
</td></tr></table>";
} # fine if (!defined('C_BACKUP_E_MODELLI_CON_NUOVI_DATI') or C_BACKUP_E_MODELLI_CON_NUOVI_DATI != "NO")

$ultime_selezioni = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'ultime_sel_crea_modelli' and idutente = '$id_utente'");
if (numlin_query($ultime_selezioni) == 1) {
$ultime_selezioni = risul_query($ultime_selezioni,0,'valpersonalizza');
$ultime_selezioni = explode(";;",$ultime_selezioni);
$anno_usel = $ultime_selezioni[0];
$lingua_usel = $ultime_selezioni[1];
$cartella_usel = $ultime_selezioni[2];
} # fine if (numlin_query($ultime_selezioni) == 1)

echo "<br>".mex("Anno",$pag).": <select name=\"anno_modello\">";
$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);
if ($anno_usel < $anno) $anno_usel = $anno;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_selezione = risul_query($anni,$num1,'idanni');
if ((!$anno_usel and $anno_selezione == $anno) or $anno_selezione == $anno_usel) $selected = " selected";
else $selected = "";
echo "<option value=\"$anno_selezione\"$selected>$anno_selezione</option>";
} # fine for $num1
echo "</select><br><br>

".mex("Lingua",$pag).":
 <select name=\"lingua_modello\">
<option value=\"ita\">italiano</option>";
$mod_presenti_vett = array();
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if (@is_file($perc_cart_mod_vett[$num_cart]."/mdl_disponibilita.php")) $mod_presenti_vett[$num_cart]['mdl_disponibilita.php'] = 1;
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
if ((!$lingua_usel and $ini_lingua == $lingua[$id_utente]) or $ini_lingua == $lingua_usel) $selected = " selected";
else $selected = "";
echo "<option value=\"$ini_lingua\"$selected>$nome_lingua</option>";
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if (@is_file($perc_cart_mod_vett[$num_cart]."/$nome_file")) $mod_presenti_vett[$num_cart][$nome_file] = 1;
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "</select><br><br>";

if ($num_perc_cart_mod_vett > 1) {
echo "".mex("Cartella",$pag).":
 <select name=\"perc_cart_mod_sel\">";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if ($perc_cart_mod_vett[$num_cart] == $cartella_usel) $selected = " selected";
else $selected = "";
echo "<option value=\"".$perc_cart_mod_vett[$num_cart]."\"$selected>".$perc_cart_mod_vett[$num_cart]."</option>";
} # fine for $num_cart
echo "</select><br><br>";
} # fine if ($num_perc_cart_mod_vett > 1)

echo "<label><input type=\"checkbox\" name=\"cambia_frasi\" value=\"SI\">".mex("Modifica le frasi predefinite",$pag)."</label><br>
<hr style=\"width: 85%\"><div style=\"text-align: center;\">
<button class=\"chav\" type=\"submit\" name=\"form_modello_disponibilita\" value=\"1\"><div>".mex("Pagina per la disponibilità",$pag)."</div></button>
</div>";

$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if ($template_name_show['tpl_type'] != "interconnection") {
if ($template_name_show[$lingua_mex]) $nome_modello_ext = $template_name_show[$lingua_mex];
else {
$messaggio = "";
if (@is_file("./includes/lang/$lingua_mex/modt_$modello_ext.php")) {
$messaggio = $template_name_show['ita'];
include("./includes/lang/$lingua_mex/modt_$modello_ext.php");
if ($messaggio == $template_name_show['ita']) $messaggio = "";
} # fine if (@is_file("./includes/lang/$lingua_mex/modt_rat.php"))
if ($messaggio) $nome_modello_ext = $messaggio;
else $nome_modello_ext = $template_name_show['en'];
} # fine else if ($template_name_show[$lingua_mex])
echo "<hr style=\"width: 85%\"><div style=\"text-align: center;\">
<button class=\"$template_class\" type=\"submit\" name=\"form_$template_name\" value=\"1\"><div>".$nome_modello_ext."</div></button>
</div>";
} # fine if ($template_name_show['tpl_type'] != "interconnection")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($templates_dir);

echo "</div></form>";


$templates_dir = opendir("./includes/templates/");
$modelli = array();
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) $modelli[$modello_ext] = 1;
} # fine while ($file = readdir($lang_dig))
ksort($modelli);
reset($modelli);
foreach ($modelli as $modello_ext => $val_i) {
include("./includes/templates/$modello_ext/name.php");
if ($template_name_show['tpl_type'] != "interconnection") {
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if (@is_file($perc_cart_mod_vett[$num_cart]."/$nome_file")) $mod_presenti_vett[$num_cart][$nome_file] = 1;
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else {
$funz_trad = "mext_$modello_ext";
if (!function_exists($funz_trad)) include("./includes/templates/$modello_ext/phrases.php");
$nome_file = $funz_trad($template_file_name["ita"],$pag,$ini_lingua);
if ($nome_file == $template_file_name['en'] or $nome_file == $template_file_name['ita']) $nome_file = $ini_lingua."_".$template_file_name['en'];
} # fine else if ($template_file_name[$ini_lingua])
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if (@is_file($perc_cart_mod_vett[$num_cart]."/$nome_file")) $mod_presenti_vett[$num_cart][$nome_file] = 1;
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($template_name_show['tpl_type'] != "interconnection")
} # fine foreach ($modelli as $modello_ext => $val_i)
closedir($templates_dir);

if ($num_cart > 1 and !empty($mod_presenti_vett)) {
echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"importa_modelli\" value=\"SI\">
".mex("Importa le pagine dalla cartella",$pag)."
 <select name=\"cartella_da\">
<option value=\"\" selected>----</option>";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
echo "<option value=\"".$perc_cart_mod_vett[$num_cart]."\">".$perc_cart_mod_vett[$num_cart]."</option>";
} # fine for $num_cart
echo "</select> ".mex("alla cartella",$pag)."
 <select name=\"cartella_a\">
<option value=\"\" selected>----</option>";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
echo "<option value=\"".$perc_cart_mod_vett[$num_cart]."\">".$perc_cart_mod_vett[$num_cart]."</option>";
} # fine for $num_cart
echo "</select>
<button class=\"xdoc\" type=\"submit\"><div>".mex("Importa",$pag)."</div></button>
</div></form></div>";
} # fine if ($num_cart > 1 and !empty($mod_presenti_vett))

if (!empty($mod_presenti_vett)) {
echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"crea_modelli.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"cancella_modelli\" value=\"SI\">
".mex("Cancella",$pag)." <select name=\"perc_mod_elimina\">
<option value=\"\">".mex("tutte le pagine create",$pag)."</option>";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$mod_presenti_subvett = $mod_presenti_vett[$num_cart];
if (@is_array($mod_presenti_subvett)) {
reset($mod_presenti_subvett);
foreach ($mod_presenti_subvett as $nome_file => $val) {
$percorso_opt = $perc_cart_mod_vett[$num_cart]."/$nome_file";
echo "<option value=\"$percorso_opt\">$percorso_opt</option>";
} # fine foreach ($mod_presenti_subvett as $nome_file => $val)
} # fine if (@is_array($mod_presenti_subvett))
} # fine for $num_cart
echo "</select>
<button class=\"canc\" type=\"submit\"><div>".mex("Cancella",$pag)."</div></button>
</div></form></div>";
} # fine if (!empty($mod_presenti_vett))


echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";

} # fine if ($mostra_form_creazione != "NO")



} # fine if ($id_utente == 1)

if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($priv_crea_interconnessioni == "s" and $anno_utente_attivato == "SI")



?>