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

$pag = "modifica_var_contr.php";
$titolo = "HotelDruid: Modifica Variabili Documenti";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablecontratti = $PHPR_TAB_PRE."contratti";


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
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
if (substr($priv_mod_pers,0,1) != "s") $modifica_pers = "NO";
$priv_mod_doc = substr($priv_mod_pers,2,1);
$priv_mod_doc_api = substr($priv_mod_pers,4,1);
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
unset($contratti_consentiti_vett);
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1]) $contratti_consentiti_vett[$contratti_consentiti[$num1]] = "SI";
} # fine if ($attiva_contratti_consentiti == "s")
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$priv_mod_doc = "s";
$priv_mod_doc_api = "s";
$attiva_contratti_consentiti = "n";
} # fine else if ($id_utente != 1)

if ($anno_utente_attivato == "SI" and $priv_mod_doc == "s" and $modifica_pers != "NO") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();


if (controlla_num_pos($contr_cond) == "NO" or $contr_cond < 1) $contr_cond = "";
if ($attiva_contratti_consentiti != "n" and $contratti_consentiti_vett[$contr_cond] != "SI") $contr_cond = "";
if (strcmp($contr_cond,"")) {
$contr_cond_esist = esegui_query("select numero from $tablecontratti where numero = '".aggslashdb($contr_cond)."' and tipo $LIKE 'contr%' ");
if (numlin_query($contr_cond_esist) != 1) $contr_cond = "";
elseif ($priv_mod_doc_api != "s") {
$api_esistente = esegui_query("select * from $tablecontratti where numero = '".aggslashdb($contr_cond)."' and tipo = 'api'");
if (numlin_query($api_esistente)) $contr_cond = "";
} # fine elseif ($priv_mod_doc_api != "s")
} # fine if (strcmp($contr_cond,""))

unset($trad_var);
function mex2 ($messaggio) {
global $trad_var,$lingua_mex;
if (!$trad_var and $lingua_mex != "ita") include("./includes/lang/$lingua_mex/visualizza_contratto_var.php");
if ($trad_var[$messaggio]) $messaggio = $trad_var[$messaggio];
elseif (substr($messaggio,-1) == ")") {
$mess_vett = explode("(",substr($messaggio,0,-1));
if ($trad_var[$mess_vett[1]]) $messaggio = $mess_vett[0]."(".$trad_var[$mess_vett[1]].")";
} # fine elseif (substr($messaggio,-1) == ")")
return $messaggio;
} # fine function mex2

$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '$id_utente'");
if (numlin_query($campi_pers_comm) == 1) {
$campi_pers_comm = explode(">",risul_query($campi_pers_comm,0,'valpersonalizza'));
$num_commenti_pers = count($campi_pers_comm);
} # fine if (numlin_query($campi_pers_comm) == 1)
else $num_commenti_pers = 0;
$campi_pers_cliente = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente' ");
if (numlin_query($campi_pers_cliente)) {
$campi_pers_cliente = explode(">",risul_query($campi_pers_cliente,0,'valpersonalizza'));
$num_campi_pers_cliente = count($campi_pers_cliente);
} # fine if (numlin_query($campi_pers_cliente))
else $num_campi_pers_cliente = 0;
$commento_personalizzato_ = mex2("commento_personalizzato")."_";
$campo_personalizzato_ = mex2("campo_personalizzato")."_";
include("./includes/variabili_contratto.php");


if ($canc_se and $num_se > 1) {
$cambia_qualcosa = "";
$num_se--;
if ($num_se == 1) $cond_sempre = "NO";
} # fine if ($canc_se and $num_se > 1)
if ($agg_se) {
$cambia_qualcosa = "";
$num_se++;
} # fine if ($agg_se)
if ($num_se > 80) $num_se = 80;




if ($cambia_qualcosa) {

function elimina_array_pers ($arr_pers_elimina) {
global $tablecontratti,$LIKE,$pag;
$arr_pers_elimina = aggslashdb($arr_pers_elimina);
$continua = "SI";
if (substr($arr_pers_elimina,0,1) != "a") $continua = "NO";
$arr_pers_elimina = substr($arr_pers_elimina,1);
if (controlla_num_pos($arr_pers_elimina) != "SI") $continua = "NO";
if ($continua == "SI") {
$n_arr_pers_el = esegui_query("select * from $tablecontratti where numero = '$arr_pers_elimina' and tipo $LIKE 'vett%'");
if (numlin_query($n_arr_pers_el) != 1) $continua = "NO";
else {
$n_arr_pers_el = explode(";",risul_query($n_arr_pers_el,0,"testo"));
$n_arr_pers_el = $n_arr_pers_el[0]."(".$n_arr_pers_el[1].")";
$arr_pers_el = "a".$arr_pers_elimina;
$condizioni = esegui_query("select * from $tablecontratti where tipo $LIKE 'cond%' order by numero");
$num_condizioni = numlin_query($condizioni);
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione = explode("#@?",$condizione);
$elimina_cond_corr = "NO";
if ($condizione[0] == "rar$arr_pers_elimina") $elimina_cond_corr = "SI";
if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
for ($num2 = 1 ; $num2 < $num_se_cond ; $num2++) {
$se_cond_corr = explode("#%?",$se_cond[$num2]);
if ($se_cond_corr[0] == $n_arr_pers_el) $elimina_cond_corr = "SI";
if ($se_cond_corr[2] == "var" and $se_cond_corr[3] == $n_arr_pers_el) $elimina_cond_corr = "SI";
} # fine for $num2
} # fine if ($condizione[1])
$azione = explode("#%?",$condizione[2]);
if ($azione[0] == "set" and ($azione[1] == $arr_pers_el or ($azione[3] == "var" and $azione[4] == $n_arr_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "set" and (($azione[5] == "var" and $azione[6] == $n_arr_pers_el) or ($azione[7] == "var" and $azione[8] == $n_arr_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "trunc" and $azione[1] == $arr_pers_el) $elimina_cond_corr = "SI";
if ($azione[0] == "oper" and ($azione[1] == $arr_pers_el or $azione[2] == $n_arr_pers_el or ($azione[4] == "var" and $azione[5] == $n_arr_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "unset" and $azione[1] == $arr_pers_el) $elimina_cond_corr = "SI";
if ($azione[0] == "array" and $azione[1] == $arr_pers_el) $elimina_cond_corr = "SI";
if ($elimina_cond_corr == "SI") {
$num_cond = risul_query($condizioni,$num1,'numero');
$tipo_cond = risul_query($condizioni,$num1,'tipo');
esegui_query("delete from $tablecontratti where numero = '$num_cond' and tipo = '$tipo_cond' ");
} # fine ($elimina_cond_corr == "SI")
} # fine for $num1
esegui_query("delete from $tablecontratti where numero = '$arr_pers_elimina' and tipo $LIKE 'vett%'");
echo mex("Array personalizzato eliminato",$pag).".<br>";
} # fine else if (numlin_query($n_var_pers_el) != 1)
} # fine if ($continua == "SI")
return $continua;
} # fine function elimina_arr_pers


if ($aggiungi_var_pers) {
$mostra_form_iniziale = "NO";
$continua = "SI";
if (@get_magic_quotes_gpc()) $nuova_var_pers = stripslashes($nuova_var_pers);
$nuova_var_pers = str_replace("#%?","",$nuova_var_pers);
if (!$nuova_var_pers) $continua = "NO";
if (preg_replace("/[A-Za-z]/","",substr($nuova_var_pers,0,1)) != "") $continua = "NO";
if (preg_replace("/[A-Za-z0-9_]/","",$nuova_var_pers) != "") $continua = "NO";
$ultima_parte = explode("_",$nuova_var_pers);
$ultima_parte = (string) $ultima_parte[(count($ultima_parte) - 1)];
if ($ultima_parte != "" and preg_replace("/[0-9]/","",$ultima_parte) == "") $continua = "NO";
if ($var_riserv[$nuova_var_pers] or substr($nuova_var_pers,0,20) == "campo_personalizzato" or substr($nuova_var_pers,0,23) == "commento_personalizzato") $continua = "NO";
if ($continua == "SI") {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$var_esistente = esegui_query("select * from $tablecontratti where (tipo $LIKE 'var%' and testo $LIKE '$nuova_var_pers') or (tipo $LIKE 'vett%' and testo $LIKE '$nuova_var_pers;%') ");
if (numlin_query($var_esistente) != 0) $continua = "NO";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$file_trad = implode("",@file("./includes/lang/$ini_lingua/visualizza_contratto_var.php"));
if (str_replace("'".$nuova_var_pers."'","",$file_trad) != $file_trad) $continua = "NO";
if (str_replace("\"".$nuova_var_pers."\"","",$file_trad) != $file_trad) $continua = "NO";
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
if ($nuova_var_pers == $var_predef[$num1]) $continua = "NO";
} # fine for $num1
if ($solo_contr_cond and strcmp($contr_cond,"")) {
$tipo = "var$contr_cond";
$contr_cond_esist = esegui_query("select numero from $tablecontratti where numero = '".aggslashdb($contr_cond)."' and tipo $LIKE 'contr%' ");
if (numlin_query($contr_cond_esist) != 1) $continua = "NO";
} # fine if ($solo_contr_cond and strcmp($contr_cond,""))
else $tipo = "var";
if ($continua == "SI") {
$max_var = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'var%'");
if (numlin_query($max_var) != 0) $max_var = (risul_query($max_var,0,0) + 1);
else $max_var = 1;
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$max_var','$tipo','$nuova_var_pers')");
echo mex("Nuova variabile personalizzata inserita",$pag).".<br>";
} # fine if ($continua == "SI")
unlock_tabelle($tabelle_lock);
} # fine if ($continua == "SI")
if ($continua != "SI") echo mex("I dati inseriti sono <div style=\"display: inline; color: red;\">errati</div>",$pag).".<br>";
} # fine if ($aggiungi_var_pers)


if ($elimina_var_pers) {
$mostra_form_iniziale = "NO";
$var_pers_elimina = aggslashdb($var_pers_elimina);
$continua = "SI";
if (controlla_num_pos($var_pers_elimina) != "SI") $continua = "NO";
if ($continua == "SI") {
$tabelle_lock = array("$tablecontratti");
$tabelle_lock = lock_tabelle($tabelle_lock);
$n_var_pers_el = esegui_query("select * from $tablecontratti where numero = '$var_pers_elimina' and tipo $LIKE 'var%'");
if (numlin_query($n_var_pers_el) != 1) $continua = "NO";
else {
$n_var_pers_el = risul_query($n_var_pers_el,0,"testo");
$condizioni = esegui_query("select * from $tablecontratti where tipo $LIKE 'cond%' order by numero");
$num_condizioni = numlin_query($condizioni);
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione = explode("#@?",$condizione);
$elimina_cond_corr = "NO";
if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
for ($num2 = 1 ; $num2 < $num_se_cond ; $num2++) {
$se_cond_corr = explode("#%?",$se_cond[$num2]);
if ($se_cond_corr[0] == $n_var_pers_el) $elimina_cond_corr = "SI";
if ($se_cond_corr[2] == "var" and $se_cond_corr[3] == $n_var_pers_el) $elimina_cond_corr = "SI";
} # fine for $num2
} # fine if ($condizione[1])
$azione = explode("#%?",$condizione[2]);
if ($azione[0] == "set" and ($azione[1] == $var_pers_elimina or ($azione[3] == "var" and $azione[4] == $n_var_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "set" and (($azione[5] == "var" and $azione[6] == $n_var_pers_el) or ($azione[7] == "var" and $azione[8] == $n_var_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "trunc" and $azione[1] == $var_pers_elimina) $elimina_cond_corr = "SI";
if ($azione[0] == "oper" and ($azione[1] == $var_pers_elimina or $azione[2] == $n_var_pers_el or ($azione[4] == "var" and $azione[5] == $n_var_pers_el))) $elimina_cond_corr = "SI";
if ($azione[0] == "date" and ($azione[1] == $var_pers_elimina or $azione[2] == $n_var_pers_el)) $elimina_cond_corr = "SI";
if ($azione[0] == "opdat" and ($azione[1] == $var_pers_elimina or $azione[3] == $n_var_pers_el or $azione[4] == $n_var_pers_el)) $elimina_cond_corr = "SI";
if ($elimina_cond_corr == "SI") {
$num_cond = risul_query($condizioni,$num1,'numero');
$tipo_cond = risul_query($condizioni,$num1,'tipo');
esegui_query("delete from $tablecontratti where numero = '$num_cond' and tipo = '$tipo_cond' ");
} # fine ($elimina_cond_corr == "SI")
} # fine for $num1
$array = esegui_query("select * from $tablecontratti where tipo $LIKE 'vett%' and testo $LIKE '%;$n_var_pers_el;%' ");
$num_array = numlin_query($array);
for ($num1 = 0 ; $num1 < $num_array ; $num1++) {
$var_arr = explode(";",risul_query($array,$num1,'testo'));
$var_arr = $var_arr[1];
if ($var_arr == $n_var_pers_el) {
$num_arr = risul_query($array,$num1,'numero');
elimina_array_pers("a".$num_arr);
} # fine if ($var_arr == $n_var_pers_el)
} # fine for $num1
esegui_query("delete from $tablecontratti where numero = '$var_pers_elimina' and tipo $LIKE 'var%'");
echo mex("Variabile personalizzata eliminata",$pag).".<br>";
} # fine else if (numlin_query($n_var_pers_el) != 1)
unlock_tabelle($tabelle_lock);
} # fine if ($continua == "SI")
if ($continua != "SI") echo mex("I dati inseriti sono <div style=\"display: inline; color: red;\">errati</div>",$pag).".<br>";
} # fine if ($elimina_var_pers)


if ($aggiungi_arr_pers) {
$mostra_form_iniziale = "NO";
$continua = "SI";
if (@get_magic_quotes_gpc()) $nuovo_arr_pers = stripslashes($nuovo_arr_pers);
$nuovo_arr_pers = str_replace("#%?","",$nuovo_arr_pers);
if (!$nuovo_arr_pers) $continua = "NO";
if (preg_replace("/[A-Za-z]/","",substr($nuovo_arr_pers,0,1)) != "") $continua = "NO";
if (preg_replace("/[A-Za-z0-9_]/","",$nuovo_arr_pers) != "") $continua = "NO";
$ultima_parte = explode("_",$nuovo_arr_pers);
$ultima_parte = (string) $ultima_parte[(count($ultima_parte) - 1)];
if ($ultima_parte != "" and preg_replace("/[0-9]/","",$ultima_parte) == "") $continua = "NO";
if ($var_riserv[$nuovo_arr_pers]) $continua = "NO";
if ($continua == "SI") {
$tabelle_lock = array("$tablecontratti");
$tabelle_lock = lock_tabelle($tabelle_lock);
$arr_esistente = esegui_query("select * from $tablecontratti where (tipo $LIKE 'vett%' and testo $LIKE '$nuovo_arr_pers;%') or (tipo $LIKE 'var%' and testo $LIKE '$nuovo_arr_pers') ");
if (numlin_query($arr_esistente) != 0) $continua = "NO";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$file_trad = implode("",@file("./includes/lang/$ini_lingua/visualizza_contratto_var.php"));
if (str_replace("'".$nuovo_arr_pers."'","",$file_trad) != $file_trad) $continua = "NO";
if (str_replace("\"".$nuovo_arr_pers."\"","",$file_trad) != $file_trad) $continua = "NO";
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
if ($nuovo_arr_pers == $var_predef[$num1]) $continua = "NO";
} # fine for $num1
if ($solo_contr_cond and strcmp($contr_cond,"")) {
$tipo = "vett$contr_cond";
$contr_cond_esist = esegui_query("select numero from $tablecontratti where numero = '".aggslashdb($contr_cond)."' and tipo $LIKE 'contr%' ");
if (numlin_query($contr_cond_esist) != 1) $continua = "NO";
} # fine if ($solo_contr_cond and strcmp($contr_cond,""))
else {
$solo_contr_cond = "";
$tipo = "vett";
} # fine else if ($solo_contr_cond and strcmp($contr_cond,""))
if ($continua == "SI") {
if ($solo_contr_cond) $variabili_pers = esegui_query("select * from $tablecontratti where tipo = 'var' or tipo = 'var$contr_cond' order by tipo, numero");
else $variabili_pers = esegui_query("select * from $tablecontratti where tipo = 'var' order by numero");
$num_variabili_pers = numlin_query($variabili_pers);
for ($num1 = 0 ; $num1 < $num_variabili_pers ; $num1++) {
$var_pers = risul_query($variabili_pers,$num1,'testo');
$num_var_pers = risul_query($variabili_pers,$num1,'numero');
$var_predef[$num_var_predef] = $var_pers;
$num_var_predef++;
$nome_var[$num_var_pers] = $var_pers;
} # fine for $num1
if (!$var_arr) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"contr_cond\" value=\"$contr_cond\">
<input type=\"hidden\" name=\"solo_contr_cond\" value=\"$solo_contr_cond\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"nuovo_arr_pers\" value=\"$nuovo_arr_pers\">
<input type=\"hidden\" name=\"aggiungi_arr_pers\" value=\"SI\">
".mex("Array della variabile",$pag).": <select name=\"var_arr\">";
for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
echo "<option value=\"".$var_predef[$num1]."\">".mex2($var_predef[$num1])."</option>";
} # fine for $num1
echo "</select>
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Aggiungi",$pag)."\">
</div></form><br>";
} # fine if (!$var_arr)
else {
$var_arr_num = -1;
for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
if ($var_arr == $var_predef[$num1]) $var_arr_num = $num1;
} # fine for $num1
if ($var_arr_num < 0) $continua = "NO";
if ($continua == "SI") {
$max_arr = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'vett%' ");
if (numlin_query($max_arr) != 0) $max_arr = (risul_query($max_arr,0,0) + 1);
else $max_arr = 1;
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$max_arr','$tipo','$nuovo_arr_pers;$var_arr')");
echo mex("Nuovo array personalizzato inserito",$pag).".<br>";
} # fine if ($continua == "SI")
} # fine else if (!$var_arr)
} # fine if ($continua == "SI")
unlock_tabelle($tabelle_lock);
} # fine if ($continua == "SI")
if ($continua != "SI") echo mex("I dati inseriti sono <div style=\"display: inline; color: red;\">errati</div>",$pag).".<br>";
} # fine if ($aggiungi_arr_pers)


if ($elimina_arr_pers) {
$mostra_form_iniziale = "NO";
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$continua = elimina_array_pers($arr_pers_elimina);
unlock_tabelle($tabelle_lock);
if ($continua != "SI") echo mex("I dati inseriti sono <div style=\"display: inline; color: red;\">errati</div>",$pag).".<br>";
} # fine if ($elimina_arr_pers)


if ($mod_contr_importa and $contr_cond) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
if ($contr_imp) {
if (controlla_num_pos($contr_imp) == "NO" or $contr_imp < 1) $contr_imp = "";
if ($contr_imp == $contr_cond or ($attiva_contratti_consentiti != "n" and $contratti_consentiti_vett[$contr_imp] != "SI")) $contr_imp = "";
if (strcmp($contr_imp,"")) {
$contr_imp_esist = esegui_query("select numero from $tablecontratti where numero = '".aggslashdb($contr_imp)."' and tipo $LIKE 'contr%' ");
if (numlin_query($contr_imp_esist) != 1) $contr_imp = "";
else {
$contr_imp_esist = esegui_query("select numero from $tablecontratti where numero = '$contr_imp' and tipo = 'impor_vc' ");
if (numlin_query($contr_imp_esist)) $contr_imp = "";
} # fine else if (numlin_query($contr_imp_esist) != 1)
} # fine if (strcmp($contr_imp,""))
if ($contr_imp) {
esegui_query("delete from $tablecontratti where tipo = 'var$contr_cond' or tipo = 'cond$contr_cond' or tipo = 'vett$contr_cond' ");
if ($tipo_contr_imp != "importa") {
$contr_importa = esegui_query("select testo from $tablecontratti where numero = '$contr_cond' and tipo = 'impor_vc' ");
if (numlin_query($contr_importa)) esegui_query("update $tablecontratti set testo = '$contr_imp' where numero = '$contr_cond' and tipo = 'impor_vc' ");
else {
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$contr_cond','impor_vc','$contr_imp') ");
esegui_query("update $tablecontratti set testo = '$contr_imp' where testo = '$contr_cond' and tipo = 'impor_vc' ");
} # fine else if (numlin_query($contr_importa))
} # fine if ($tipo_contr_imp != "importa")
else {
$var_cond_importa = esegui_query("select * from $tablecontratti where tipo = 'var$contr_imp' or tipo = 'cond$contr_imp' or tipo = 'vett$contr_imp' ");
$num_var_cond = numlin_query($var_cond_importa);
for ($num1 = 0 ; $num1 < $num_var_cond ; $num1++) {
$num_imp = risul_query($var_cond_importa,$num1,'numero');
$tipo_imp = risul_query($var_cond_importa,$num1,'tipo');
$tipo_imp = preg_replace("/[0-9]/","",$tipo_imp).$contr_cond;
$testo_imp = aggslashdb(risul_query($var_cond_importa,$num1,'testo'));
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_imp','$tipo_imp','$testo_imp') ");
} # fine for $num1
} # fine else if ($tipo_contr_imp == "sempre")
} # fine if ($contr_imp)
} # fine if ($contr_imp)
else esegui_query("delete from $tablecontratti where numero = '$contr_cond' and tipo = 'impor_vc' ");
unlock_tabelle($tabelle_lock);
} # fine if ($mod_contr_importa and $contr_cond)


if ($aggiungi_cond) {
$mostra_form_iniziale = "NO";
$continua = "SI";
if (substr($inizializza,0,1) == "a") {
$num_arr_rip = substr($inizializza,1);
$inizializza = "rar$num_arr_rip";
$arr_esistente = esegui_query("select * from $tablecontratti where (tipo = 'vett' or tipo = 'vett$contr_cond') and numero = '".aggslashdb($num_arr_rip)."'");
if (numlin_query($arr_esistente) != 1) $continua = "NO";
} # fine if (substr($inizializza,0,1) == "a")
if ($inizializza != "rpt" and $inizializza != "inr" and $inizializza != "ind" and $inizializza != "ros" and $inizializza != "rca" and $inizializza != "rpa" and $inizializza != "run" and preg_replace("/rar[1-9][0-9]*/","",$inizializza) != "") $continua = "NO";
if (strcmp($cond_sempre,"") and $cond_sempre != "SI" and $cond_sempre != "NO") $continua = "NO";
if ($azione != "set" and $azione != "trunc" and $azione != "oper" and $azione != "date" and $azione != "unset" and $azione != "array" and $azione != "break" and $azione != "cont") $continua = "NO";
if ($oper_data and $azione = "date") $azione = "opdat";
if ($num_se < 1 or $num_se > 80) $continua = "NO";
if ($num_se > 1 and $and_or != "and" and $and_or != "or") $continua = "NO";
if ($continua == "SI") {

function controlla_var_allora ($var_allora,&$continua,$contr_cond) {
if ($var_allora != "-1" and $var_allora != "-2") {
global $tablecontratti,$LIKE;
$var_allora = aggslashdb($var_allora);
if (substr($var_allora,0,1) != "a") $var_esistente = esegui_query("select * from $tablecontratti where (tipo = 'var' or tipo = 'var$contr_cond') and numero = '$var_allora'");
else $var_esistente = esegui_query("select * from $tablecontratti where (tipo = 'vett' or tipo = 'vett$contr_cond') and numero = '".substr($var_allora,1)."'");
if (numlin_query($var_esistente) != 1) $continua = "NO";
} # fine ($var_allora != "-1" and $var_allora != "-2")
} # fine function controlla_var_allora
function controlla_var_se ($var_se,&$continua,$var_predef,$num_var_predef,$contr_cond) {
global $tablecontratti,$LIKE;
$var_se = aggslashdb($var_se);
$var_se_trovata = "NO";
if (str_replace("(","",$var_se) == $var_se) $var_se_contr = $var_se;
else {
$var_se_contr = explode("(",$var_se);
$var_se_contr = $var_se_contr[0];
} # fine else if (str_replace("(","",$var_se) == $var_se)
$var_esistente = esegui_query("select * from $tablecontratti where ((tipo = 'var' or tipo = 'var$contr_cond') and testo $LIKE '$var_se_contr') or ((tipo = 'vett' or tipo = 'vett$contr_cond') and testo $LIKE '$var_se_contr;%')");
if (numlin_query($var_esistente) == 1) $var_se_trovata = "SI";
for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
if ($var_predef[$num1] == $var_se) $var_se_trovata = "SI";
} # fine for $num1
if ($var_se_trovata != "SI") $continua = "NO";
return $var_se;
} # fine function controlla_var_se
function controlla_testo_input ($var_txt) {
if (@get_magic_quotes_gpc()) $var_txt = stripslashes($var_txt);
if (C_RESTRIZIONI_DEMO_ADMIN == "SI") $var_txt = htmlspecialchars($var_txt);
$var_txt = str_replace("#%?","#?",$var_txt);
$var_txt = str_replace("#@?","#?",$var_txt);
$var_txt = str_replace("#$?","#?",$var_txt);
$var_txt = str_replace("|","/",$var_txt);
$var_txt = aggslashdb($var_txt);
return $var_txt;
} # fine function controlla_testo_input

$testo = "$inizializza#@?";
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
if ($num_cond_mod) {
$cond_esist = @esegui_query("select numero from $tablecontratti where numero = '".aggslashdb($num_cond_mod)."' and tipo = 'cond$contr_cond' ");
if (!numlin_query($cond_esist)) $continua = "NO";
} # fine if ($num_cond_mod)
if ($cond_sempre != "SI") {
if ($num_se > 1) $testo .= $and_or;
for ($num1 = 1 ; $num1 <= $num_se ; $num1++) {
controlla_var_se(${"var_se".$num1},$continua,$var_predef,$num_var_predef,$contr_cond);
if (${"cond".$num1} != "=" and ${"cond".$num1} != "!=" and ${"cond".$num1} != "mag" and ${"cond".$num1} != "min" and ${"cond".$num1} != "con" and ${"cond".$num1} != "cmm") $continua = "NO";
if (${"cond".$num1} == "mag") ${"cond".$num1} = ">";
if (${"cond".$num1} == "min") ${"cond".$num1} = "<";
if (${"cond".$num1} == "con") ${"cond".$num1} = "{}";
if (${"cond".$num1} == "cmm") ${"cond".$num1} = "{A}";
if (${"tipo_val_se".$num1} != "txt" and ${"tipo_val_se".$num1} != "var") $continua = "NO";
if (${"tipo_val_se".$num1} == "txt") $val_se = controlla_testo_input(${"val_se_txt".$num1});
else $val_se = controlla_var_se(${"val_se_sel".$num1},$continua,$var_predef,$num_var_predef,$contr_cond);
$testo .= "#$?".${"var_se".$num1}."#%?".${"cond".$num1}."#%?".${"tipo_val_se".$num1}."#%?$val_se";
} # fine for $num1
} # fine if ($cond_sempre != "SI")
$testo .= "#@?";
if ($azione == "set") {
if ($oper_str != "=" and $oper_str != ".=") $continua = "NO";
controlla_var_allora($var_allora,$continua,$contr_cond);
if ($tipo_val_allora != "txt" and $tipo_val_allora != "var") $continua = "NO";
if ($tipo_val_allora == "txt") {
$val_allora = controlla_testo_input($val_allora_txt);
$oper_allora_sel = "";
} # fine if ($tipo_val_allora == "txt")
else $val_allora = controlla_var_se($val_allora_sel,$continua,$var_predef,$num_var_predef,$contr_cond);
if ($oper_allora_sel != "" and $oper_allora_sel != "low" and $oper_allora_sel != "upp" and $oper_allora_sel != "url" and $oper_allora_sel != "asc" and $oper_allora_sel != "eas" and $oper_allora_sel != "md5") $continua = "NO";
if ($tipo_val_sost1 != "txt" and $tipo_val_sost1 != "var") $continua = "NO";
if ($tipo_val_sost1 == "txt") $val_sost1 = controlla_testo_input($val_sost1_txt);
else $val_sost1 = controlla_var_se($val_sost1_sel,$continua,$var_predef,$num_var_predef,$contr_cond);
if ($tipo_val_sost2 == "txt") $val_sost2 = controlla_testo_input($val_sost2_txt);
else $val_sost2 = controlla_var_se($val_sost2_sel,$continua,$var_predef,$num_var_predef,$contr_cond);
$testo .= "set#%?$var_allora#%?$oper_str#%?$tipo_val_allora#%?$val_allora#%?$tipo_val_sost1#%?$val_sost1#%?$tipo_val_sost2#%?$val_sost2#%?$oper_allora_sel";
} # fine if ($azione == "set")
if ($azione == "trunc") {
controlla_var_allora($var_trunc,$continua,$contr_cond);
if (!$val_trunc or controlla_num($val_trunc) == "NO") $continua = "NO";
$val_trunc2 = controlla_testo_input($val_trunc2);
if ($pos_trunc2 != "ini" and $pos_trunc2 != "fin") $continua = "NO";
$testo .= "trunc#%?$var_trunc#%?$val_trunc#%?$val_trunc2#%?$pos_trunc2";
} # fine if ($azione == "trunc")
if ($azione == "oper") {
controlla_var_allora($var_oper,$continua,$contr_cond);
controlla_var_se($var_oper2,$continua,$var_predef,$num_var_predef,$contr_cond);
if ($operatore != "+" and $operatore != "-" and $operatore != "*" and $operatore != "/") $continua = "NO";
if ($tipo_val_oper != "txt" and $tipo_val_oper != "var") $continua = "NO";
if ($tipo_val_oper == "txt") {
$val_oper = formatta_soldi($val_oper_txt);
if (controlla_soldi($val_oper,"NO") == "NO") $continua = "NO";
if ($operatore == "/" and !$val_oper) $continua = "NO";
} # fine if ($tipo_val_oper == "txt")
else $val_oper = controlla_var_se($val_oper_sel,$continua,$var_predef,$num_var_predef,$contr_cond);
$val_arrotond = formatta_soldi($val_arrotond);
if (controlla_soldi($val_arrotond) == "NO") $continua = "NO";
if ($operatore == "/" and !$val_arrotond) $continua = "NO";
if (strcmp($val_arrotond,"") and !strcmp(str_replace("0","",$val_arrotond),"")) $continua = "NO";
$testo .= "oper#%?$var_oper#%?$var_oper2#%?$operatore#%?$tipo_val_oper#%?$val_oper#%?$val_arrotond";
} # fine if ($azione == "oper")
if ($azione == "date") {
controlla_var_allora($var_data,$continua,$contr_cond);
controlla_var_se($var_data2,$continua,$var_predef,$num_var_predef,$contr_cond);
if ($subdata != "gi" and $subdata != "me" and $subdata != "an" and $subdata != "gs" and $subdata != "is" and $subdata != "da") $continua = "NO";
if ($oper_giorni != "+" and $oper_giorni != "-") $continua = "NO";
if (!strcmp($num_giorni,"") or controlla_num_pos($num_giorni) == "NO") $continua = "NO";
if ($num_giorni and $oper_giorni == "-") $num_giorni = ($num_giorni * -1);
if ($tipo_giorni != "g" and $tipo_giorni != "m" and $tipo_giorni != "a") $continua = "NO";
$testo .= "date#%?$var_data#%?$var_data2#%?$subdata#%?$num_giorni#%?$tipo_giorni";
} # fine if ($azione == "date")
if ($azione == "opdat") {
controlla_var_allora($var_data,$continua,$contr_cond);
controlla_var_se($var_opdat2,$continua,$var_predef,$num_var_predef,$contr_cond);
controlla_var_se($var_opdat3,$continua,$var_predef,$num_var_predef,$contr_cond);
if ($tipo_int != "g" and $tipo_int != "m" and $tipo_int != "a") $continua = "NO";
$testo .= "opdat#%?$var_data#%?$tipo_int#%?$var_opdat2#%?$var_opdat3";
} # fine if ($azione == "opdat")
if ($azione == "unset") {
if (substr($arr_azz,0,1) != "a") $continua = "NO";
controlla_var_allora($arr_azz,$continua,$contr_cond);
$testo .= "unset#%?$arr_azz";
} # fine if ($azione == "unset")
if ($azione == "array") {
if (substr($array,0,1) != "a") $continua = "NO";
$arr_esistente = esegui_query("select * from $tablecontratti where (tipo = 'vett' or tipo = 'vett$contr_cond') and numero = '".aggslashdb(substr($array,1))."'");
if (numlin_query($arr_esistente) != 1) $continua = "NO";
else {
$arr_pers_vett = explode(";",risul_query($arr_esistente,0,'testo'));
$var_arr_pers = $arr_pers_vett[1];
$var_non_predef = esegui_query("select numero from $tablecontratti where (tipo = 'var' or tipo = 'var$contr_cond') and testo = '".aggslashdb($var_arr_pers)."' ");
if (!numlin_query($var_non_predef)) $continua = "NO";
} # fine if (numlin_query($arr_esistente) != 1)
if ($tipo_arr != "dat" and $tipo_arr != "dap" and $tipo_arr != "val" and $tipo_arr != "cop") $continua = "NO";
if ($tipo_arr == "val") $lista_val = controlla_testo_input($lista_val);
else $lista_val = "";
if ($tipo_arr == "cop") {
$lista_val = $array2;
if (substr($array2,0,1) != "a") $continua = "NO";
$arr_esistente = esegui_query("select * from $tablecontratti where (tipo = 'vett' or tipo = 'vett$contr_cond') and numero = '".aggslashdb(substr($array2,1))."'");
if (numlin_query($arr_esistente) != 1) $continua = "NO";
} # fine if ($tipo_arr == "cop")
$testo .= "array#%?$array#%?$tipo_arr#%?$lista_val";
} # fine if ($azione == "array")
if ($azione == "break") {
if ($break_cont != "" and $break_cont != "cont") $continua = "NO";
$testo .= "break#%?$break_cont";
} # fine if ($azione == "break")
if ($azione == "cont") $testo .= "cont";
if ($continua == "SI") {
if ($num_cond_mod) {
esegui_query("delete from $tablecontratti where numero = '$num_cond_mod' and tipo = 'cond$contr_cond' ");
$max_cond = $num_cond_mod;
} # fine if ($num_cond_mod)
else {
$max_cond = esegui_query("select max(numero) from $tablecontratti where tipo = 'cond$contr_cond' ");
if (numlin_query($max_cond) != 0) $max_cond = (risul_query($max_cond,0,0) + 1);
else $max_cond = 1;
} # fine else if ($num_cond_mod)
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$max_cond','cond$contr_cond','$testo')");
if (!$num_cond_mod) echo mex("Nuova condizione inserita",$pag).".<br>";
else echo ucfirst(mex("condizione modificata",$pag)).".<br>";
} # fine if ($continua == "SI")
unlock_tabelle($tabelle_lock);
} # fine if ($continua == "SI")
if ($continua != "SI") echo mex("I dati inseriti sono <div style=\"display: inline; color: red;\">errati</div>",$pag).".<br>";
} # fine if ($aggiungi_cond)


if ($elimina_cond) {
$mostra_form_iniziale = "NO";
$continua = "SI";
if (controlla_num_pos($num_cond) == "NO") $continua = "NO";
if ($continua == "SI") {
$tabelle_lock = array("$tablecontratti");
$tabelle_lock = lock_tabelle($tabelle_lock);
esegui_query("delete from $tablecontratti where numero = '$num_cond' and tipo = 'cond$contr_cond'");
echo mex("Condizione eliminata",$pag).".<br>";
unlock_tabelle($tabelle_lock);
} # fine if ($continua == "SI")
if ($continua != "SI") echo mex("I dati inseriti sono <div style=\"display: inline; color: red;\">errati</div>",$pag).".<br>";
} # fine if ($elimina_cond)


if ($mostra_form_iniziale == "NO") {
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"contr_cond\" value=\"$contr_cond\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Torna indietro",$pag)."\">
</div></form>";
} # fine if ($mostra_form_iniziale == "NO")

} # fine if ($cambia_qualcosa)




if (($sposta_giu or $sposta_su) and $num_cond and $salta_a) {
$tabelle_lock = array("$tablecontratti");
$tabelle_lock = lock_tabelle($tabelle_lock);
$condizioni = esegui_query("select * from $tablecontratti where tipo = 'cond$contr_cond' order by testo $LIKE 'ini%' desc, numero");
$num_condizioni = numlin_query($condizioni);
$errore = 0;
if ($num_cond == $salta_a) $errore = 1;
if (!$sposta_giu and !$sposta_su) $errore = 1;
unset($cond_vett);
$num_cond_trovato = 0;
$salta_a_trovato = 0;
$tipo_cond = "";
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$cond_vett[$num1] = risul_query($condizioni,$num1,'numero');
if ($cond_vett[$num1] == $num_cond) {
$num_cond_trovato = 1;
$pos_num_cond = $num1;
if (!$tipo_cond) $tipo_cond = substr(risul_query($condizioni,$num1,'testo'),0,3);
elseif ($tipo_cond != substr(risul_query($condizioni,$num1,'testo'),0,3)) $errore = 1;
} # fine if ($cond_vett[$num1] == $num_cond)
if ($cond_vett[$num1] == $salta_a) {
$salta_a_trovato = 1;
$pos_salta_a = $num1;
if (!$tipo_cond) $tipo_cond = substr(risul_query($condizioni,$num1,'testo'),0,3);
elseif ($tipo_cond != substr(risul_query($condizioni,$num1,'testo'),0,3)) $errore = 1;
} # fine if ($cond_vett[$num1] == $salta_a)
if ($sposta_giu and !$num_cond_trovato and $salta_a_trovato) $errore = 1;
if ($sposta_su and $num_cond_trovato and !$salta_a_trovato) $errore = 1;
} # fine for $num1
if (!$num_cond_trovato or !$salta_a_trovato) $errore = 1;
if (!$errore) {
esegui_query("update $tablecontratti set numero = '-1' where numero = '$num_cond' and tipo = 'cond$contr_cond' ");
$ultima_cond = $num_cond;
if ($sposta_giu) {
for ($num1 = ($pos_num_cond + 1) ; $num1 <= $pos_salta_a ; $num1++) {
esegui_query("update $tablecontratti set numero = '$ultima_cond' where numero = '".$cond_vett[$num1]."' and tipo = 'cond$contr_cond' ");
$ultima_cond = $cond_vett[$num1];
} # fine for $num1
} # fine if ($sposta_giu)
if ($sposta_su) {
for ($num1 = ($pos_num_cond - 1) ; $num1 >= $pos_salta_a ; $num1--) {
esegui_query("update $tablecontratti set numero = '$ultima_cond' where numero = '".$cond_vett[$num1]."' and tipo = 'cond$contr_cond' ");
$ultima_cond = $cond_vett[$num1];
} # fine for $num1
} # fine if ($sposta_su)
esegui_query("update $tablecontratti set numero = '$salta_a' where numero = '-1' and tipo = 'cond$contr_cond' ");
} # fine if (!$errore)
unlock_tabelle($tabelle_lock);
} # fine if (($sposta_giu or $sposta_su) and $num_cond and...





if ($mostra_form_iniziale != "NO") {


echo "<h3>".mex("Variabili personalizzate dei documenti",$pag)."</h3><br><br>";

$variabili_pers = esegui_query("select * from $tablecontratti where tipo = 'var' or tipo = 'var$contr_cond' order by tipo, numero");
$num_variabili_pers = numlin_query($variabili_pers);
$option_var_pers = "";
$nome_var['-1'] = mex2('messaggio_di_errore');
$nome_var['-2'] = mex2('errore_ripetizione');
$num_var_predef_orig = $num_var_predef;
for ($num1 = 0 ; $num1 < $num_variabili_pers ; $num1++) {
$var_pers = risul_query($variabili_pers,$num1,'testo');
$num_var_pers = risul_query($variabili_pers,$num1,'numero');
$option_var_pers .= "<option value=\"$num_var_pers\">$var_pers</option>";
$var_predef[$num_var_predef] = $var_pers;
$num_var_predef++;
$nome_var[$num_var_pers] = $var_pers;
} # fine for $num1

$contr_importa_vc = "";
if ($contr_cond) {
$c_imp_vc = esegui_query("select testo from $tablecontratti where numero = '$contr_cond' and tipo = 'impor_vc' ");
if (numlin_query($c_imp_vc)) $contr_importa_vc = risul_query($c_imp_vc,0,'testo');
} # fine if ($contr_cond)

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"contr_cond\" value=\"$contr_cond\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Variabili personalizzate",$pag).": ";
if ($option_var_pers) {
echo "<select name=\"var_pers_elimina\">
$option_var_pers
</select>
<input class=\"sbutton\" type=\"submit\" name=\"elimina_var_pers\" value=\"".mex("Elimina",$pag)."\">";
} # fine if ($option_var_pers)
echo "&nbsp;&nbsp;&nbsp;<input name=\"nuova_var_pers\" size=\"20\" maxlength=\"50\" type=\"text\">
<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_var_pers\" value=\"".mex("Aggiungi",$pag)."\">";
if ($contr_cond and !$contr_importa_vc) echo " (<label><input type=\"checkbox\" name=\"solo_contr_cond\" value=\"SI\" checked> ".mex("solo al documento",$pag)." $contr_cond</label>)";
echo "</div></form><br>";


$array_pers = esegui_query("select * from $tablecontratti where tipo = 'vett' or tipo = 'vett$contr_cond' order by tipo, numero");
$num_array_pers = numlin_query($array_pers);
$option_arr_pers = "";
$opt_arr_var_non_predef = "";
for ($num1 = 0 ; $num1 < $num_array_pers ; $num1++) {
$arr_pers_vett = explode(";",risul_query($array_pers,$num1,'testo'));
$arr_pers = $arr_pers_vett[0];
$var_arr_pers = $arr_pers_vett[1];
$num_arr_pers = risul_query($array_pers,$num1,'numero');
$option_arr_pers .= "<option value=\"a$num_arr_pers\">$arr_pers(".mex2($var_arr_pers).")</option>";
$var_non_predef = esegui_query("select numero from $tablecontratti where (tipo = 'var' or tipo = 'var$contr_cond') and testo = '".aggslashdb($var_arr_pers)."' ");
if (numlin_query($var_non_predef)) $opt_arr_var_non_predef .= "<option value=\"a$num_arr_pers\">$arr_pers($var_arr_pers)</option>";
$var_predef[$num_var_predef] = "$arr_pers($var_arr_pers)";
$num_var_predef++;
$nome_var["a$num_arr_pers"] = "$arr_pers(".mex2($var_arr_pers).")";
} # fine for $num1
$option_var_pers .= $option_arr_pers;

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"contr_cond\" value=\"$contr_cond\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Array personalizzati",$pag).": ";
if ($option_arr_pers) {
echo "<select name=\"arr_pers_elimina\">
$option_arr_pers
</select>
<input class=\"sbutton\" type=\"submit\" name=\"elimina_arr_pers\" value=\"".mex("Elimina",$pag)."\">";
} # fine if ($option_arr_pers)
echo "&nbsp;&nbsp;&nbsp;<input name=\"nuovo_arr_pers\" size=\"20\" maxlength=\"50\" type=\"text\">
<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_arr_pers\" value=\"".mex("Aggiungi",$pag)."\">";
if ($contr_cond and !$contr_importa_vc) echo " (<label><input type=\"checkbox\" name=\"solo_contr_cond\" value=\"SI\" checked> ".mex("solo al documento",$pag)." $contr_cond</label>)";
echo "</div></form><br>";


$contratti = esegui_query("select * from $tablecontratti where tipo $LIKE 'contr%' order by numero");
$num_contratti = numlin_query($contratti);
$nomi_contratti = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '$id_utente'");
$nomi_contratti = explode("#@&",risul_query($nomi_contratti,0,'valpersonalizza'));
$num_nomi_contratti = count($nomi_contratti);
for ($num1 = 0 ; $num1 < $num_nomi_contratti ; $num1++) {
$dati_nome_contratto = explode("#?&",$nomi_contratti[$num1]);
$nome_contratto[$dati_nome_contratto[0]] = $dati_nome_contratto[1];
} # fine for $num1
if (!$contr_cond) {
$sel = " selected";
$frase_cond_contr = mex("a tutti i documenti",$pag);
$input_cond_contr = "";
} # fine if (!$contr_cond)
else $sel = "";
$opt_contr = "<option value=\"\"$sel>".mex("a tutti i documenti",$pag)."</option>";
for ($num1 = 0 ; $num1 < $num_contratti ; $num1++) {
$num_contr = risul_query($contratti,$num1,'numero');
if ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contr] == "SI") {
$mod_doc_corr = 1;
if ($priv_mod_doc_api != "s") {
$api_esistente = esegui_query("select * from $tablecontratti where numero = '".aggslashdb($num_contr)."' and tipo = 'api'");
if (numlin_query($api_esistente)) $mod_doc_corr = 0;
} # fine if ($priv_mod_doc_api != "s")
if ($mod_doc_corr) {
if ($contr_cond == $num_contr) {
$sel = " selected";
$frase_cond_contr = mex("al documento",$pag)." $num_contr";
if ($nome_contratto[$num_contr]) $frase_cond_contr .= " (".$nome_contratto[$num_contr].")";
$input_cond_contr = "<input type=\"hidden\" name=\"contr_cond\" value=\"$num_contr\">";
} # fine if ($contr_cond == $num_contr)
else $sel = "";
$opt_contr .= "<option value=\"$num_contr\"$sel>".mex("al documento",$pag)." $num_contr";
if ($nome_contratto[$num_contr]) $opt_contr .= " (".$nome_contratto[$num_contr].")";
$opt_contr .= "</option>";
} # fine if ($mod_doc_corr)
} # fine if ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contr] == "SI")
} # fine for $num1
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
".mex("Modifica le condizioni applicate",$pag)."
<select name=\"contr_cond\">
$opt_contr</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Modifica",$pag)."\">
</div></form>";

if ($option_var_pers) {

function rowbgcolor () {
global $rowbgcolor,$t2row1color,$t2row2color;
if ($rowbgcolor == $t2row2color) $rowbgcolor = $t2row1color;
else $rowbgcolor = $t2row2color;
return $rowbgcolor;
} # fine function rowbgcolor

$num_rip_a = 0;
$num_cond_rip_a = array();
$num_cond_rip_a[1] = 0;
$ordine_rar = "";
$condizioni_rar = esegui_query("select * from $tablecontratti where tipo = 'cond$contr_cond' and testo $LIKE 'rar%' order by testo");
$num_condizioni_rar = numlin_query($condizioni_rar);
for ($num1 = 0 ; $num1 < $num_condizioni_rar ; $num1++) {
$cond = risul_query($condizioni_rar,$num1,'testo');
$num_arr_rip = substr($cond,3,(strlen(strstr($cond,"#@?")) * -1));
if (!$num_cond_rip_a['num'][$num_arr_rip]) {
$num_rip_a++;
$num_cond_rip_a['num'][$num_arr_rip] = $num_rip_a;
$num_cond_rip_a['arr'][$num_rip_a] = $num_arr_rip;
$ordine_rar .= ", testo $LIKE 'rar$num_arr_rip%' desc";
} # fine if (!$num_cond_rip_a['num'][$num_arr_rip])
} # fine for $num1

$azione_orig = $azione;
$num_cond_mod_vedi = "";
$condizioni = esegui_query("select * from $tablecontratti where tipo = 'cond$contr_cond' order by testo $LIKE 'ind%' desc, testo $LIKE 'inr%' desc, testo $LIKE 'rpt%' desc, testo $LIKE 'ros%' desc, testo $LIKE 'rca%' desc, testo $LIKE 'rpa%' desc, testo $LIKE 'run%' desc$ordine_rar, numero");
$num_condizioni = numlin_query($condizioni);
$num_cond_ini_d = 0;
$num_cond_ini_r = 0;
$num_cond_rpt = 0;
$num_cond_rip_o = 0;
$num_cond_rip_c = 0;
$num_cond_rip_p = 0;
$num_cond_rip_u = 0;
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$n_cond = risul_query($condizioni,$num1,'numero');
$cond = risul_query($condizioni,$num1,'testo');
if (substr($cond,0,3) == "ind") $num_cond_ini_d = $num1 + 1;
if (substr($cond,0,3) == "inr") $num_cond_ini_r = $num1 + 1;
if (substr($cond,0,3) == "rpt") $num_cond_rpt = $num1 + 1;
if (substr($cond,0,3) == "ros") $num_cond_rip_o = $num1 + 1;
if (substr($cond,0,3) == "rca") $num_cond_rip_c = $num1 + 1;
if (substr($cond,0,3) == "rpa") $num_cond_rip_p = $num1 + 1;
if (substr($cond,0,3) == "run") $num_cond_rip_u = $num1 + 1;
if (substr($cond,0,3) == "rar") {
$num_arr_rip = substr($cond,3,(strlen(strstr($cond,"#@?")) * -1));
$num_cond_rip_a[$num_cond_rip_a['num'][$num_arr_rip]] = $num1 + 1;
} # fine if (substr($cond,0,3) == "rar")
if ($n_cond == $num_cond) $num_cond_passa = $num1;
$cond_vett[$num1] = $n_cond;
} # fine for $num1
if ($num_cond_ini_r < $num_cond_ini_d) $num_cond_ini_r = $num_cond_ini_d;
if ($num_cond_rpt < $num_cond_ini_r) $num_cond_rpt = $num_cond_ini_r;
if ($num_cond_rip_o < $num_cond_rpt) $num_cond_rip_o = $num_cond_rpt;
if ($num_cond_rip_c < $num_cond_rip_o) $num_cond_rip_c = $num_cond_rip_o;
if ($num_cond_rip_p < $num_cond_rip_c) $num_cond_rip_p = $num_cond_rip_c;
if ($num_cond_rip_u < $num_cond_rip_p) $num_cond_rip_u = $num_cond_rip_p;
if ($num_cond_rip_a[1] < $num_cond_rip_u) $num_cond_rip_a[1] = $num_cond_rip_u;
for ($num1 = 1 ; $num1 < $num_rip_a ; $num1++) if ($num_cond_rip_a[($num1 + 1)] < $num_cond_rip_a[$num1]) $num_cond_rip_a[($num1 + 1)] = $num_cond_rip_a[$num1];
if ($num_cond and $num_cond_passa < 20) echo "<a name=\"condizioni\"></a>";
echo "<div class=\"rbox\">".mex("<b>Condizioni</b> applicate",$pag)." $frase_cond_contr:<br>
<table><tr><td style=\"height: 5px;\"></td></tr></table>";

if ($num_condizioni > 0) {
$tab_cond = "";
for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$num_cond = risul_query($condizioni,$num1,'numero');
$condizione = explode("#@?",$condizione);
$azione = explode("#%?",$condizione[2]);
if ($num_cond_mod == $num_cond) {
$num_cond_mod_vedi = ($num1 + 1);
$inizializza = $condizione[0];
$azione_orig = $azione[0];
$num_cond_mod_orig = $num_cond_mod;
if ($agg_se or $canc_se) $num_cond_mod = "";
} # fine if ($num_cond_mod == $num_cond)

if ($num1 == $num_cond_rpt and $condizione[0] == "ros") $tab_cond .= "</table><br>".mex("Condizioni applicate",$pag)." ".mex("solo nelle ripetizioni degli ospiti",$pag)." (<em>".mex("tutte le altre condizioni non verranno più applicate in queste ripetizioni",$pag)."</em>):<br><table>";
if ($num1 == $num_cond_rip_o and $condizione[0] == "rca") $tab_cond .= "</table><br>".mex("Condizioni applicate",$pag)." ".mex("solo nelle ripetizioni dei costi aggiuntivi",$pag)." (<em>".mex("tutte le altre condizioni non verranno più applicate in queste ripetizioni",$pag)."</em>):<br><table>";
if ($num1 == $num_cond_rip_c and $condizione[0] == "rpa") $tab_cond .= "</table><br>".mex("Condizioni applicate",$pag)." ".mex("solo nelle ripetizioni dei pagamenti",$pag)." (<em>".mex("tutte le altre condizioni non verranno più applicate in queste ripetizioni",$pag)."</em>):<br><table>";
if ($num1 == $num_cond_rip_p and $condizione[0] == "run") $tab_cond .= "</table><br>".mex("Condizioni applicate",$pag)." ".mex("solo nelle ripetizioni delle unità",$pag)." (<em>".mex("tutte le altre condizioni non verranno più applicate in queste ripetizioni",$pag)."</em>):<br><table>";
if ($num1 == $num_cond_rip_u and $condizione[0] == "rar".$num_cond_rip_a['arr'][1]) $tab_cond .= "</table><br>".mex("Condizioni applicate",$pag)." ".mex("solo nelle ripetizioni dell'array",$pag)." [".$nome_var["a".$num_cond_rip_a['arr'][1]]."] (<em>".mex("tutte le altre condizioni non verranno più applicate in queste ripetizioni",$pag)."</em>):<br><table>";
for ($num2 = 1 ; $num2 < $num_rip_a ; $num2++) if ($num1 == $num_cond_rip_a[$num2] and $condizione[0] == "rar".$num_cond_rip_a['arr'][($num2 + 1)]) $tab_cond .= "</table><br>".mex("Condizioni applicate",$pag)." ".mex("solo nelle ripetizioni dell'array",$pag)." [".$nome_var["a".$num_cond_rip_a['arr'][($num2 + 1)]]."] (<em>".mex("tutte le altre condizioni non verranno più applicate in queste ripetizioni",$pag)."</em>):<br><table>";
$tab_cond .= "<tr style=\"background-color: ".rowbgcolor().";\"><td><b>".($num1 + 1)."</b>. ";
if ($num_cond_passa == ($num1 + 20)) $tab_cond .= "<a name=\"condizioni\"></a>";
$str_cond = "";

if ($condizione[1]) {
$se_cond = explode("#$?",$condizione[1]);
$num_se_cond = count($se_cond);
if ($num_cond_mod == $num_cond) {
$cond_sempre = "NO";
$num_se = ($num_se_cond - 1);
$and_or = $se_cond[0];
} # fine if ($num_cond_mod == $num_cond)
for ($num2 = 1 ; $num2 < $num_se_cond ; $num2++) {
$se_cond_corr = explode("#%?",$se_cond[$num2]);
if ($num2 > 1) {
if ($se_cond[0] == "or") $str_cond .= "".mex("o",$pag)."";
else $str_cond .= "".mex("e",$pag)."";
} # fine if ($num2 > 1)
else $str_cond .= mex("se",$pag);
$str_cond .= " [".mex2($se_cond_corr[0])."] ";
if ($se_cond_corr[1] == "=") $str_cond .= mex("è uguale a",$pag);
if ($se_cond_corr[1] == "!=") $str_cond .= mex("è diverso da",$pag);
if ($se_cond_corr[1] == ">") $str_cond .= mex("è maggiore di",$pag);
if ($se_cond_corr[1] == "<") $str_cond .= mex("è minore di",$pag);
if ($se_cond_corr[1] == "{}") $str_cond .= mex("contiene",$pag);
if ($se_cond_corr[1] == "{A}") $str_cond .= mex("contiene",$pag)." (".mex("maiusc./minusc.",$pag).")";
if ($se_cond_corr[2] == "var") $str_cond .= " [".mex2($se_cond_corr[3])."] ";
else $str_cond .= " \"".htmlspecialchars($se_cond_corr[3])."\" ";
if ($num_cond_mod == $num_cond) {
${"var_se".$num2} = $se_cond_corr[0];
${"cond".$num2} = $se_cond_corr[1];
if (${"cond".$num2} == ">") ${"cond".$num2} = "mag";
if (${"cond".$num2} == "<") ${"cond".$num2} = "min";
if (${"cond".$num2} == "{}") ${"cond".$num2} = "con";
if (${"cond".$num2} == "{A}") ${"cond".$num2} = "cmm";
${"tipo_val_se".$num2} = $se_cond_corr[2];
if ($se_cond_corr[2] == "var") ${"val_se_sel".$num2} = $se_cond_corr[3];
else ${"val_se_txt".$num2} = htmlspecialchars($se_cond_corr[3]);
} # fine if ($num_cond_mod == $num_cond)
} # fine for $num2
$str_cond .= mex("allora",$pag)." ";
} # fine if ($condizione[1])

if ($azione[0] == "set") {
$str_cond .= mex("porre",$pag)." [".$nome_var[$azione[1]]."] ";
if ($azione[2] == "=") $str_cond .= mex("uguale a",$pag);
if ($azione[2] == ".=") $str_cond .= mex("concatenato con",$pag);
if ($azione[3] == "var") {
$str_cond .= " [".mex2($azione[4])."]";
if ($azione[9] == "low") $str_cond .= " ".mex("in minuscole",$pag);
if ($azione[9] == "upp") $str_cond .= " ".mex("in maiuscole",$pag);
if ($azione[9] == "url") $str_cond .= " ".mex("codificato per URL",$pag);
if ($azione[9] == "asc") $str_cond .= " ".mex("codificato in ASCII",$pag);
if ($azione[9] == "eas") $str_cond .= " ".mex("in ASCII esteso",$pag);
if ($azione[9] == "md5") $str_cond .= " ".mex("codificato con MD5",$pag);
} # fine if ($azione[3] == "var")
else $str_cond .= " \"".htmlspecialchars($azione[4])."\"";
if (strcmp($azione[6],"")) {
$str_cond .= " ".mex("sostituendo",$pag)." ";
if ($azione[5] == "var") $str_cond .= "[".mex2($azione[6])."]";
else $str_cond .= "\"".htmlspecialchars($azione[6])."\"";
$str_cond .= " ".mex("con",$pag)." ";
if ($azione[7] == "var") $str_cond .= "[".mex2($azione[8])."]";
else $str_cond .= "\"".htmlspecialchars($azione[8])."\"";
} # fine if (strcmp($azione[6],""))
if ($num_cond_mod == $num_cond) {
$oper_str = $azione[2];
$var_allora = $azione[1];
$tipo_val_allora = $azione[3];
if ($azione[3] == "var") $val_allora_sel = $azione[4];
else $val_allora_txt = htmlspecialchars($azione[4]);
$tipo_val_sost1 = $azione[5];
if ($azione[5] == "var") $val_sost1_sel = $azione[6];
else $val_sost1_txt = htmlspecialchars($azione[6]);
$tipo_val_sost2 = $azione[7];
if ($azione[7] == "var") $val_sost2_sel = $azione[8];
else $val_sost2_txt = htmlspecialchars($azione[8]);
$oper_allora_sel = $azione[9];
} # fine if ($num_cond_mod == $num_cond)
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
$str_cond .= mex("troncare",$pag)." [".$nome_var[$azione[1]]."] ";
if ($azione[2] < 0) $str_cond .= mex("prima di",$pag)." ".($azione[2] * -1);
else $str_cond .= mex("dopo",$pag)." ".$azione[2];
$str_cond .= " ".mex("caratteri",$pag);
if (strcmp($azione[3],"")) {
$str_cond .= " (".mex("riempiendo i mancanti con",$pag)." \"".htmlspecialchars($azione[3])."\"";
if ($azione[4] == "ini") $str_cond .= " ".mex("all'inizio",$pag);
if ($azione[4] == "fin") $str_cond .= " ".mex("alla fine",$pag);
$str_cond .= ")";
} # fine if (strcmp($azione[3],""))
if ($num_cond_mod == $num_cond) {
$var_trunc = $azione[1];
$val_trunc = $azione[2];
$val_trunc2 = htmlspecialchars($azione[3]);
$pos_trunc2 = $azione[4];
} # fine if ($num_cond_mod == $num_cond)
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$str_cond .= mex("porre",$pag)." [".$nome_var[$azione[1]]."] ".mex("uguale a",$pag);
$str_cond .= " [".mex2($azione[2])."] ";
$str_cond .= $azione[3]." ";
if ($azione[4] == "var") $str_cond .= "[".mex2($azione[5])."]";
else $str_cond .= $azione[5];
if (strcmp($azione[6],"")) $str_cond .= " (".mex("arrotondato a",$pag)." ".$azione[6].")";
if ($num_cond_mod == $num_cond) {
$var_oper = $azione[1];
$var_oper2 = $azione[2];
$operatore = $azione[3];
$tipo_val_oper = $azione[4];
if ($azione[4] == "var") $val_oper_sel = $azione[5];
else $val_oper_txt = $azione[5];
$val_arrotond = $azione[6];
} # fine if ($num_cond_mod == $num_cond)
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$str_cond .= mex("porre",$pag)." [".$nome_var[$azione[1]]."] ".mex("uguale a",$pag)." \"";
if ($azione[3] == "gi") $str_cond .= mex("il giorno",$pag);
if ($azione[3] == "me") $str_cond .= mex("il mese",$pag);
if ($azione[3] == "an") $str_cond .= mex("l'anno",$pag);
if ($azione[3] == "gs") $str_cond .= mex("il giorno della settimana",$pag);
if ($azione[3] == "is") $str_cond .= mex("la data",$pag)." (".mex("formato ISO",$pag).")";
if ($azione[3] == "da") $str_cond .= mex("la data",$pag)." (".mex("formato corrente",$pag).")";
$str_cond .= "\" ".mex("della data",$pag)." [".mex2($azione[2])."]";
if ($azione[4]) {
if ($azione[4] < 0) $str_cond .= " - ".($azione[4] * -1)." ";
else $str_cond .= " + ".$azione[4]." ";
if ($azione[5] == "g") $str_cond .= mex("giorni",$pag);
if ($azione[5] == "m") $str_cond .= mex("mesi",$pag);
if ($azione[5] == "a") $str_cond .= mex("anni",$pag);
} # fine if ($azione[4])
if ($num_cond_mod == $num_cond) {
$var_data = $azione[1];
$var_data2 = $azione[2];
$subdata = $azione[3];
$num_giorni = $azione[4];
if ($num_giorni < 0) {
$num_giorni = ($num_giorni * -1);
$oper_giorni = "-";
} # fine if ($num_giorni < 0)
else $oper_giorni = "+";
$tipo_giorni = $azione[5];
} # fine if ($num_cond_mod == $num_cond)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$str_cond .= mex("porre",$pag)." [".$nome_var[$azione[1]]."] ".mex("uguale al numero di",$pag)." \"";
if ($azione[2] == "g") $str_cond .= mex("giorni",$pag);
if ($azione[2] == "m") $str_cond .= mex("mesi",$pag);
if ($azione[2] == "a") $str_cond .= mex("anni",$pag);
$str_cond .= "\" ".mex("dalla data",$pag)." [".mex2($azione[3])."]";
$str_cond .= " ".mex("alla data",$pag)." [".mex2($azione[4])."]";
if ($num_cond_mod == $num_cond) {
$var_data = $azione[1];
$tipo_int = $azione[2];
$var_opdat2 = $azione[3];
$var_opdat3 = $azione[4];
} # fine if ($num_cond_mod == $num_cond)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
$str_cond .= mex("azzera l'array",$pag)." [".$nome_var[$azione[1]]."]";
if ($num_cond_mod == $num_cond) $arr_azz = $azione[1];
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$str_cond .= mex("assegna all'array",$pag)." [".$nome_var[$azione[1]]."] ";
if ($azione[2] == "dat") $str_cond .= mex("valori progressivi tra le date selezionate",$pag);
if ($azione[2] == "dap") $str_cond .= mex("valori progressivi tra il primo arrivo e l'ultima partenza",$pag);
if ($azione[2] == "val") $str_cond .= mex("i valori",$pag)." \"".htmlspecialchars(str_replace(",",", ",$azione[3]))."\"";
if ($azione[2] == "cop") $str_cond .= mex("i valori dell'array",$pag)." [".$nome_var[$azione[3]]."]";
if ($num_cond_mod == $num_cond) {
$array = $azione[1];
$tipo_arr = $azione[2];
if ($azione[2] == "val") $lista_val = htmlspecialchars($azione[3]);
if ($azione[2] == "cop") $array2 = $azione[3];
} # fine if ($num_cond_mod == $num_cond)
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
$str_cond .= mex("non processare le condizioni successive",$pag);
if ($azione[1] == "cont") $str_cond .= " ".mex("fino al prossimo &quot;riprendi&quot;",$pag);
if ($num_cond_mod == $num_cond) $break_cont = $azione[1];
} # fine if ($azione[0] == "array")

if ($azione[0] == "cont") $str_cond .= mex("riprendi a processare le condizioni successive",$pag);

if ($condizione[0] == "inr") $str_cond .= " ".mex("solo all'inizio delle ripetizioni delle prenotazioni",$pag);
if ($condizione[0] == "ind") $str_cond .= " ".mex("solo all'inizio del documento",$pag);
if ($condizione[0] == "ros") $str_cond .= " ".mex("solo nelle ripetizioni degli ospiti",$pag);
if ($condizione[0] == "rca") $str_cond .= " ".mex("solo nelle ripetizioni dei costi aggiuntivi",$pag);
if ($condizione[0] == "rpa") $str_cond .= " ".mex("solo nelle ripetizioni dei pagamenti",$pag);
if ($condizione[0] == "run") $str_cond .= " ".mex("solo nelle ripetizioni delle unità",$pag);
if (substr($condizione[0],0,3) == "rar") $str_cond .= " ".mex("solo nelle ripetizioni dell'array",$pag)." [".$nome_var["a".substr($condizione[0],3)]."]";
$tab_cond .= ucfirst($str_cond).".</td>";
$fine_cond_rip_a = 0;
for ($num2 = 1 ; $num2 <= $num_rip_a ; $num2++) if ($num1 == ($num_cond_rip_a[$num2] - 1)) $fine_cond_rip_a = 1;
if ($num1 != ($num_condizioni - 1) and $num1 != ($num_cond_ini_r - 1) and $num1 != ($num_cond_ini_d - 1) and $num1 != ($num_cond_rpt - 1) and $num1 != ($num_cond_rip_o - 1) and $num1 != ($num_cond_rip_c - 1) and $num1 != ($num_cond_rip_p - 1) and $num1 != ($num_cond_rip_u - 1) and !$fine_cond_rip_a) {
$opt_giu = "";
$fine_opt = $num_condizioni;
for ($num2 = $num_rip_a ; $num2 > 0 ; $num2--) if ($num1 < ($num_cond_rip_a[$num2] - 1)) $fine_opt = $num_cond_rip_a[$num2];
if ($num1 < ($num_cond_rip_u - 1)) $fine_opt = $num_cond_rip_u;
if ($num1 < ($num_cond_rip_p - 1)) $fine_opt = $num_cond_rip_p;
if ($num1 < ($num_cond_rip_c - 1)) $fine_opt = $num_cond_rip_c;
if ($num1 < ($num_cond_rip_o - 1)) $fine_opt = $num_cond_rip_o;
if ($num1 < ($num_cond_rpt - 1)) $fine_opt = $num_cond_rpt;
if ($num1 < ($num_cond_ini_r - 1)) $fine_opt = $num_cond_ini_r;
if ($num1 < ($num_cond_ini_d - 1)) $fine_opt = $num_cond_ini_d;
for ($num2 = ($num1 + 2) ; $num2 <= $fine_opt ; $num2++) $opt_giu .= "<option value=\"".$cond_vett[($num2 - 1)]."\">$num2</option>";
$tab_cond .= "<td><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag#condizioni\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<input type=\"hidden\" name=\"sposta_giu\" value=\"SI\">
<input type=\"hidden\" name=\"num_cond\" value=\"$num_cond\">
<table cellspacing=0><tr><td><select class=\"smallsel\" name=\"salta_a\">$opt_giu</select></td><td>
<button type=\"submit\">
<img style=\"display: block;\" src=\"./img/freccia_giu_marg.png\" alt=\"&lt;\">
</button></td></tr></table></div></form></td>";
} # fine if ($num1 != ($num_condizioni - 1) and $num1 != ($num_cond_ini_r - 1) and...
else $tab_cond .= "<td></td>";
$fine_cond_rip_a = 0;
for ($num2 = 1 ; $num2 <= $num_rip_a ; $num2++) if ($num1 == $num_cond_rip_a[$num2]) $fine_cond_rip_a = 1;
if ($num1 != 0 and $num1 != $num_cond_ini_r and $num1 != $num_cond_ini_d and $num1 != $num_cond_rpt and $num1 != $num_cond_rip_o and $num1 != $num_cond_rip_c and $num1 != $num_cond_rip_p and $num1 != $num_cond_rip_u and !$fine_cond_rip_a) {
$opt_su = "";
$fine_opt = 1;
if ($num1 > $num_cond_ini_d) $fine_opt = ($num_cond_ini_d + 1);
if ($num1 > $num_cond_ini_r) $fine_opt = ($num_cond_ini_r + 1);
if ($num1 > $num_cond_rpt) $fine_opt = ($num_cond_rpt + 1);
if ($num1 > $num_cond_rip_o) $fine_opt = ($num_cond_rip_o + 1);
if ($num1 > $num_cond_rip_c) $fine_opt = ($num_cond_rip_c + 1);
if ($num1 > $num_cond_rip_p) $fine_opt = ($num_cond_rip_p + 1);
if ($num1 > $num_cond_rip_u) $fine_opt = ($num_cond_rip_u + 1);
for ($num2 = 1 ; $num2 <= $num_rip_a ; $num2++) if ($num1 > $num_cond_rip_a[$num2]) $fine_opt = ($num_cond_rip_a[$num2] + 1);
for ($num2 = $num1 ; $num2 >= $fine_opt ; $num2--) $opt_su .= "<option value=\"".$cond_vett[($num2 - 1)]."\">$num2</option>";
$tab_cond .= "<td><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag#condizioni\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<input type=\"hidden\" name=\"sposta_su\" value=\"SI\">
<input type=\"hidden\" name=\"num_cond\" value=\"$num_cond\">
<table cellspacing=0><tr><td><button type=\"submit\">
<img style=\"display: block;\" src=\"./img/freccia_su_marg.png\" alt=\"&gt;\">
</button></td><td><select class=\"smallsel\" name=\"salta_a\">$opt_su</select>
</td></tr></table></div></form></td>";
} # fine if ($num1 != 0 and $num1 != $num_cond_ini_r and $num1 != $num_cond_ini_d and...
else $tab_cond .= "<td></td>";
$tab_cond .= "<td><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag#modcond\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<input type=\"hidden\" name=\"num_cond_mod\" value=\"$num_cond\">
<input class=\"smallsbutton\" type=\"submit\" value=\"".mex("Modifica",$pag)."\">
</div></form></td>
<td><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag#condizioni\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"num_cond\" value=\"$num_cond\">
<input class=\"smallsbutton\" type=\"submit\" name=\"elimina_cond\" value=\"".mex("Elimina",$pag)."\">
</div></form></td></tr>";
 if ($num_cond_mod_orig == $num_cond) $num_cond_mod = $num_cond_mod_orig;
} # fine for $num1
echo "<table>
$tab_cond
</table>";
} # fine if ($num_condizioni > 0)
else echo "<br>";
echo "</div><br>";

$azione = $azione_orig;
if (!$num_cond_mod_vedi) $num_cond_mod = "";

} # fine if ($option_var_pers)


if ($contr_cond) {
$opt_contr_imp = "";
for ($num1 = 0 ; $num1 < $num_contratti ; $num1++) {
$num_contr = risul_query($contratti,$num1,'numero');
if ($contr_cond != $num_contr and ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contr] == "SI" or $num_contr == $contr_importa_vc)) {
$contr_imp_esist = esegui_query("select numero from $tablecontratti where numero = '$num_contr' and tipo = 'impor_vc' ");
if (!numlin_query($contr_imp_esist)) {
if ($num_contr == $contr_importa_vc) $sel = " selected";
else $sel = "";
$opt_contr_imp .= "<option value=\"$num_contr\"$sel>$num_contr";
if ($nome_contratto[$num_contr]) $opt_contr_imp .= " (".$nome_contratto[$num_contr].")";
$opt_contr_imp .= "</option>";
} # fine if (!numlin_query($contr_imp_esist))
} # fine if ($contr_cond != $num_contr and...
} # fine for $num1
if ($opt_contr_imp) {
if (!$contr_importa_vc) $sel = " selected";
else $sel = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"mod_contr_importa\" value=\"SI\">";
if ($contr_importa_vc) echo "".mex("Utilizza sempre",$pag)."";
else echo "<select name=\"tipo_contr_imp\">
<option value=\"sempre\">".mex("Utilizza sempre",$pag)."</option>
<option value=\"importa\">".mex("Importa ora",$pag)."</option>
</select>";
echo " ".mex("variabili e condizioni del documento",$pag)."
<select name=\"contr_imp\">
<option value=\"\">----</option>
$opt_contr_imp</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Modifica",$pag)."\">
</div></form>";
if ($num_condizioni > 0) echo "<small>&nbsp;&nbsp;(".mex("le variabili e condizioni attuali verranno cancellate",$pag).")</small>";
if ($contr_importa_vc and ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$contr_importa_vc] == "SI")) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"contr_cond\" value=\"$contr_importa_vc\">
&nbsp;&nbsp;<input class=\"sbutton\" type=\"submit\" value=\"".mex("Visualizza le condizioni del documento",$pag)." $contr_importa_vc\">
</div></form>";
} # fine if ($contr_importa_vc and...
echo "<br><br>";
} # fine if ($opt_contr_imp)
} # fine if ($contr_cond)


if($option_var_pers and !$contr_importa_vc) {
$option_var_pers .= "<option value=\"-2\">".mex2('errore_ripetizione')."</option>
<option value=\"-1\">".mex2('messaggio_di_errore')."</option>";


$option_var_predef = "";
$option_var_predef_data = "";
for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
$option_var_predef .= "<option value=\"".$var_predef[$num1]."\">".mex2($var_predef[$num1])."</option>";
if ($var_predef_data[$var_predef[$num1]] or $num1 >= $num_var_predef_orig) $option_var_predef_data .= "<option value=\"".$var_predef[$num1]."\">".mex2($var_predef[$num1])."</option>";
} # fine for $num1


if (!$num_se) $num_se = 1;
if ($num_se > 2 and !$and_or) $num_se = 1;
$sel_rip = "";
$sel_inirip = "";
$sel_inidoc = "";
$sel_iniros = "";
$sel_inirca = "";
$sel_inirpa = "";
$sel_inirun = "";
if (!$inizializza or $inizializza == "rpt") $sel_rip = " selected";
if ($inizializza == "inr") $sel_inirip = " selected";
if ($inizializza == "ind") $sel_inidoc = " selected";
if ($inizializza == "ros") $sel_iniros = " selected";
if ($inizializza == "rca") $sel_inirca = " selected";
if ($inizializza == "rpa") $sel_inirpa = " selected";
if ($inizializza == "run") $sel_inirun = " selected";
$option_arr_rip = str_replace("\">","\">".mex("solo nelle ripetizioni dell'array",$pag)." ",$option_arr_pers);
if (substr($inizializza,0,3) == "rar") {
$num_arr_rip = substr($inizializza,3);
$option_arr_rip = str_replace("\"a".$num_arr_rip."\"","\"a".$num_arr_rip."\" selected",$option_arr_rip);
} # fine if (substr($inizializza,0,3) == "rar")
echo "<div class=\"rbox\" style=\"padding-top: 8px;\">
<a name=\"modcond\"></a>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag#modcond\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"aggiungi_cond\" value=\"SI\">
<input type=\"hidden\" name=\"num_se\" value=\"$num_se\">
<input type=\"hidden\" name=\"and_or\" value=\"$and_or\">
<input type=\"hidden\" name=\"num_cond_mod\" value=\"$num_cond_mod\">";
if (!$num_cond_mod) echo "<b>".ucfirst(mex("nuova condizione",$pag))."</b> ".mex("da applicare",$pag);
else echo "".ucfirst(mex("modifica la condizione",$pag))." <b>$num_cond_mod_vedi</b> ".mex("da applicare",$pag);
echo " <select name=\"inizializza\">
<option value=\"rpt\"$sel_rip>".mex("in tutte le ripetizioni delle prenotazioni",$pag)."</option>
<option value=\"inr\"$sel_inirip>".mex("solo all'inizio delle ripetizioni delle prenotazioni",$pag)."</option>
<option value=\"ind\"$sel_inidoc>".mex("solo all'inizio del documento",$pag)."</option>
<option value=\"ros\"$sel_iniros>".mex("solo nelle ripetizioni degli ospiti",$pag)."</option>
<option value=\"rca\"$sel_inirca>".mex("solo nelle ripetizioni dei costi aggiuntivi",$pag)."</option>
<option value=\"rpa\"$sel_inirpa>".mex("solo nelle ripetizioni dei pagamenti",$pag)."</option>
<option value=\"run\"$sel_inirun>".mex("solo nelle ripetizioni delle unità",$pag)."</option>
$option_arr_rip
</select>:<br>

<table>";

if ($num_se == 1) {
if ($cond_sempre != "NO") $checked = " checked";
else $checked = "";
echo "<tr><td>
<input type=\"radio\" id=\"cond_sem_si\" name=\"cond_sempre\" value=\"SI\"$checked></td>
<td colspan=\"2\" onclick=\"document.getElementById('cond_sem_si').checked='1'\">
".ucfirst(mex("sempre",$pag))."</td></tr>";
} # fine if ($num_se == 1)

for ($num1 = 1 ; $num1 <= $num_se ; $num1++) {
if ($num_se == 1) {
if ($cond_sempre == "NO") $checked = " checked";
else $checked = "";
echo "<tr onclick=\"document.getElementById('cond_sem_no').checked='1'\"><td>
<input type=\"radio\" id=\"cond_sem_no\" name=\"cond_sempre\" value=\"NO\"$checked></td>";
} # fine if ($num_se == 1)
else echo "<tr>";
if ($num1 > 1) {
$fr_se = mex("se",$pag);
if ($num_se == 2) {
echo "<td><select name=\"and_or\">
".str_replace("=\"$and_or\">","=\"$and_or\" selected>","
<option value=\"and\">".mex("e",$pag)."</option>
<option value=\"or\">".mex("o",$pag)."</option>
")."</select></td>";
} # fine if ($num_se == 2)
else {
echo "<td>";
if ($num1 == 2) echo "<input type=\"hidden\" name=\"and_or\" value=\"$and_or\">";
if ($and_or == "and") echo mex("e",$pag);
if ($and_or == "or") echo mex("o",$pag);
echo "</td>";
} # fine else if ($num_se == 2)
} # fine if ($num1 > 1)
else {
$fr_se = ucfirst(mex("se",$pag));
if ($num_se > 1) echo "<td></td>";
} # fine else if ($num1 > 1)

if (${"tipo_val_se".$num1} == "var") { $chk_var = " checked"; $chk_txt = ""; }
else { $chk_var = ""; $chk_txt = " checked"; }
$sel_ug = "";
$sel_div = "";
$sel_mag = "";
$sel_min = "";
if (${"cond".$num1} == "=") $sel_ug = " selected";
if (${"cond".$num1} == "!=") $sel_div = " selected";
if (${"cond".$num1} == "mag") $sel_mag = " selected";
if (${"cond".$num1} == "min") $sel_min = " selected";
if (${"cond".$num1} == "con") $sel_con = " selected";
if (${"cond".$num1} == "cmm") $sel_cmm = " selected";
echo "<td>$fr_se</td><td><select name=\"var_se$num1\">
".str_replace("\"".${"var_se".$num1}."\">","\"".${"var_se".$num1}."\" selected>",$option_var_predef)."
</select></td><td><select name=\"cond$num1\">
<option value=\"=\"$sel_ug>".mex("è uguale a",$pag)."</option>
<option value=\"!=\"$sel_div>".mex("è diverso da",$pag)."</option>
<option value=\"mag\"$sel_mag>".mex("è maggiore di",$pag)."</option>
<option value=\"min\"$sel_min>".mex("è minore di",$pag)."</option>
<option value=\"con\"$sel_con>".mex("contiene",$pag)."</option>
<option value=\"cmm\"$sel_cmm>".mex("contiene",$pag)." (".mex("maiusc./minusc.",$pag).")</option>
</select></td><td><b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\"><tr><td>
<input type=\"radio\" id=\"val_se_txt$num1\" name=\"tipo_val_se$num1\" value=\"txt\"$chk_txt></td><td>
<input type=\"text\" name=\"val_se_txt$num1\" value=\"".${"val_se_txt".$num1}."\" size=\"20\" maxlength=\"750\" onfocus=\"document.getElementById('val_se_txt$num1').checked='1'\">
</td></tr><tr><td><input type=\"radio\" id=\"val_se_sel$num1\" name=\"tipo_val_se$num1\" value=\"var\"$chk_var></td><td>
<select name=\"val_se_sel$num1\" onfocus=\"document.getElementById('val_se_sel$num1').checked='1'\">
".str_replace("\"".${"val_se_sel".$num1}."\">","\"".${"val_se_sel".$num1}."\" selected>",$option_var_predef)."
</select></td></tr></table></td>";
if ($num1 == $num_se) {
if ($num_se > 1) echo "<td><input name=\"canc_se\" class=\"sbutton\" type=\"submit\" value=\"".ucfirst(mex("elimina questo &quot;se&quot;",$pag))."\"></td></tr><tr><td colspan=\"6\"></td>";
echo "<td><input name=\"agg_se\" class=\"sbutton\" type=\"submit\" value=\"".ucfirst(mex("aggiungi un altro &quot;se&quot;",$pag))."\"></td>";
} # fine if ($num1 == $num_se)
echo "</tr>";
} # fine for $num1

echo "</table><br>
".ucfirst(mex("azione",$pag)).":<br>";

if (!$azione or $azione == "set") $chk_az = " checked";
else $chk_az = "";
if ($oper_str == ".=") { $sel_ug = ""; $sel_div = " selected"; }
else { $sel_ug = " selected"; $sel_div = ""; }
if ($tipo_val_allora == "var") { $chk_var = " checked"; $chk_txt = ""; }
else { $chk_var = ""; $chk_txt = " checked"; }
if ($tipo_val_sost1 == "var") { $chk_var_s1 = " checked"; $chk_txt_s1 = ""; }
else { $chk_var_s1 = ""; $chk_txt_s1 = " checked"; }
if ($tipo_val_sost2 == "var") { $chk_var_s2 = " checked"; $chk_txt_s2 = ""; }
else { $chk_var_s2 = ""; $chk_txt_s2 = " checked"; }
$sel_oper_no = "";
$sel_oper_low = "";
$sel_oper_upp = "";
$sel_oper_url = "";
$sel_oper_asc = "";
$sel_oper_eas = "";
$sel_oper_md5 = "";
if (!$oper_allora_sel) $sel_oper_no = " selected";
if ($oper_allora_sel == "low") $sel_oper_low = " selected";
if ($oper_allora_sel == "upp") $sel_oper_upp = " selected";
if ($oper_allora_sel == "url") $sel_oper_url = " selected";
if ($oper_allora_sel == "asc") $sel_oper_asc = " selected";
if ($oper_allora_sel == "eas") $sel_oper_eas = " selected";
if ($oper_allora_sel == "md5") $sel_oper_md5 = " selected";
echo "<table onclick=\"document.getElementById('az_set').checked='1'\"><tr><td>
<input type=\"radio\" id=\"az_set\" name=\"azione\" value=\"set\"$chk_az>
</td><td><select name=\"oper_str\">
<option value=\"=\"$sel_ug>".ucwords(mex("uguaglia",$pag))."</option>
<option value=\".=\"$sel_div>".ucwords(mex("concatena",$pag))."</option>
</select></td><td><select name=\"var_allora\">
".str_replace("\"$var_allora\">","\"$var_allora\" selected>",$option_var_pers)."
</select></td><td>".str_replace(" ","&nbsp;",mex("con",$pag))."</td>
<td><b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\"><tr><td>
<input type=\"radio\" id=\"val_all_txt\" name=\"tipo_val_allora\" value=\"txt\"$chk_txt></td><td>
<input type=\"text\" name=\"val_allora_txt\" value=\"$val_allora_txt\" size=\"20\" maxlength=\"750\" onfocus=\"document.getElementById('val_all_txt').checked='1'\">
</td></tr><tr><td><input type=\"radio\" id=\"val_all_sel\" name=\"tipo_val_allora\" value=\"var\"$chk_var></td><td>
<select name=\"val_allora_sel\" onfocus=\"document.getElementById('val_all_sel').checked='1'\">
".str_replace("\"$val_allora_sel\">","\"$val_allora_sel\" selected>",$option_var_predef)."
</select><select name=\"oper_allora_sel\" onfocus=\"document.getElementById('val_all_sel').checked='1'\">
<option value=\"\"$sel_oper_no></option>
<option value=\"low\"$sel_oper_low>".mex("in minuscole",$pag)."</option>
<option value=\"upp\"$sel_oper_upp>".mex("in maiuscole",$pag)."</option>
<option value=\"url\"$sel_oper_url>".mex("codificato per URL",$pag)."</option>
<option value=\"asc\"$sel_oper_asc>".mex("codificato in ASCII",$pag)."</option>
<option value=\"eas\"$sel_oper_eas>".mex("in ASCII esteso",$pag)."</option>
<option value=\"md5\"$sel_oper_md5>".mex("codificato con MD5",$pag)."</option>
</select></td></tr></table></td><td>
(".str_replace(" ","&nbsp;",mex("sostituendo",$pag))."</td><td>
<b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\"><tr><td>
<input type=\"radio\" id=\"val_s1_txt\" name=\"tipo_val_sost1\" value=\"txt\"$chk_txt_s1></td><td>
<input type=\"text\" name=\"val_sost1_txt\" value=\"$val_sost1_txt\" size=\"20\" maxlength=\"750\" onfocus=\"document.getElementById('val_s1_txt').checked='1'\">
</td></tr><tr><td><input type=\"radio\" id=\"val_s1_sel\" name=\"tipo_val_sost1\" value=\"var\"$chk_var_s1></td><td>
<select name=\"val_sost1_sel\" onfocus=\"document.getElementById('val_s1_sel').checked='1'\">
".str_replace("\"$val_sost1_sel\">","\"$val_sost1_sel\" selected>",$option_var_predef)."
</select></td></tr></table></td><td>
".str_replace(" ","&nbsp;",mex("con",$pag))."</td><td>
<b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\"><tr><td>
<input type=\"radio\" id=\"val_s2_txt\" name=\"tipo_val_sost2\" value=\"txt\"$chk_txt_s2></td><td>
<input type=\"text\" name=\"val_sost2_txt\" value=\"$val_sost2_txt\" size=\"20\" maxlength=\"750\" onfocus=\"document.getElementById('val_s2_txt').checked='1'\">
</td></tr><tr><td><input type=\"radio\" id=\"val_s2_sel\" name=\"tipo_val_sost2\" value=\"var\"$chk_var_s2></td><td>
<select name=\"val_sost2_sel\" onfocus=\"document.getElementById('val_s2_sel').checked='1'\">
".str_replace("\"$val_sost2_sel\">","\"$val_sost2_sel\" selected>",$option_var_predef)."
</select></td></tr></table>
</td><td>)</td></tr></table><br>";

if ($azione == "trunc") $chk_az = " checked";
else $chk_az = "";
if ($pos_trunc2 == "fin") { $sel_ini = ""; $sel_fin = " selected"; }
else { $sel_ini = " selected"; $sel_fin = ""; }
echo "<table onclick=\"document.getElementById('az_trunc').checked='1'\"><tr><td>
<input type=\"radio\" id=\"az_trunc\" name=\"azione\" value=\"trunc\"$chk_az>
</td><td>".ucfirst(mex("troncare",$pag))." </td><td><select name=\"var_trunc\">
".str_replace("\"$var_trunc\">","\"$var_trunc\" selected>",$option_var_pers)."
</select></td><td>".str_replace(" ","&nbsp;",mex("dopo",$pag))."
<small><small>(".str_replace(" ","&nbsp;",mex("prima di con valori negativi",$pag)).")</small></small></td><td>
<input type=\"text\" name=\"val_trunc\" value=\"$val_trunc\" size=\"3\" maxlength=\"12\"></td><td>
".str_replace(" ","&nbsp;",mex("caratteri",$pag))."</td><td>
<td>(".str_replace(" ","&nbsp;",mex("se mancanti riempire con",$pag))."</td><td>
<input type=\"text\" name=\"val_trunc2\" value=\"$val_trunc2\" size=\"10\" maxlength=\"750\"></td>
<td>".str_replace(" ","&nbsp;",mex("la parte",$pag))."</td><td>
<select name=\"pos_trunc2\">
<option value=\"ini\"$sel_ini>".mex("iniziale",$pag)."</option>
<option value=\"fin\"$sel_fin>".mex("finale",$pag)."</option>
</select></td><td>)
</td></tr></table><br>";

if ($azione == "oper") $chk_az = " checked";
else $chk_az = "";
$sel_piu = "";
$sel_men = "";
$sel_per = "";
$sel_div = "";
if ($operatore == "+") $sel_piu = " selected";
if ($operatore == "-") $sel_men = " selected";
if ($operatore == "*") $sel_per = " selected";
if ($operatore == "/") $sel_div = " selected";
if ($tipo_val_oper == "var") { $chk_var = " checked"; $chk_txt = ""; }
else { $chk_var = ""; $chk_txt = " checked"; }
if (!strcmp($val_oper_txt,"")) $val_oper_txt = "0";
echo "<table><tr onclick=\"document.getElementById('az_oper').checked='1'\"><td>
<input type=\"radio\" id=\"az_oper\" name=\"azione\" value=\"oper\"$chk_az>
</td><td>".ucfirst(mex("porre",$pag))." </td><td><select name=\"var_oper\">
".str_replace("\"$var_oper\">","\"$var_oper\" selected>",$option_var_pers)."
</select></td><td>".str_replace(" ","&nbsp;",mex("uguale a",$pag))."</td><td>
<select name=\"var_oper2\">
".str_replace("\"$var_oper2\">","\"$var_oper2\" selected>",$option_var_predef)."
</select></td><td>
<select name=\"operatore\">
<option value=\"+\"$sel_piu>+</option>
<option value=\"-\"$sel_men>-</option>
<option value=\"*\"$sel_per>*</option>
<option value=\"/\"$sel_div>/</option>
</select></td><td>
<b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\"><tr><td>
<input type=\"radio\" id=\"val_oper_txt\" name=\"tipo_val_oper\" value=\"txt\"$chk_txt></td><td>
<input type=\"text\" name=\"val_oper_txt\" value=\"$val_oper_txt\" size=\"15\" maxlength=\"750\" onfocus=\"document.getElementById('val_oper_txt').checked='1'\">
</td></tr><tr><td><input type=\"radio\" id=\"val_oper_sel\" name=\"tipo_val_oper\" value=\"var\"$chk_var></td><td>
<select name=\"val_oper_sel\" onfocus=\"document.getElementById('val_oper_sel').checked='1'\">
".str_replace("\"$val_oper_sel\">","\"$val_oper_sel\" selected>",$option_var_predef)."
</select></td></tr></table>
</td><td>(".str_replace(" ","&nbsp;",mex("arrotondato a",$pag))."</td><td>
<input type=\"text\" name=\"val_arrotond\" value=\"$val_arrotond\" size=\"15\" maxlength=\"50\"></td><td>)
</td></tr><tr><td></td><td colspan=\"9\"><small>
(".mex("Utilizzare una variabile personalizzata che finisce con \"_p\" per aggiungervi i separatori delle migliaia e 2 decimali dopo l'operazione",$pag).")
</small></td></tr></table><br>";

if ($azione == "date" or $azione == "opdat") $chk_az = " checked";
else $chk_az = "";
$chk_opdat0 = "";
$chk_opdat1 = "";
if ($azione == "opdat") $chk_opdat1 = " checked";
else $chk_opdat0 = " checked";
$sel_gi = "";
$sel_me = "";
$sel_an = "";
$sel_gs = "";
$sel_is = "";
$sel_co = "";
if ($subdata == "gi") $sel_gi = " selected";
if ($subdata == "me") $sel_me = " selected";
if ($subdata == "an") $sel_an = " selected";
if ($subdata == "gs") $sel_gs = " selected";
if ($subdata == "is") $sel_is = " selected";
if ($subdata == "da") $sel_da = " selected";
$sel_piu = "";
$sel_men = "";
if ($oper_giorni == "+") $sel_piu = " selected";
if ($oper_giorni == "-") $sel_men = " selected";
if (!strcmp($num_giorni,"")) $num_giorni = "0";
if ($tipo_giorni == "g") $sel_g = " selected";
if ($tipo_giorni == "m") $sel_m = " selected";
if ($tipo_giorni == "a") $sel_a = " selected";
$sel_ig = "";
$sel_im = "";
$sel_ia = "";
if ($tipo_int == "g") $sel_ig = " selected";
if ($tipo_int == "m") $sel_im = " selected";
if ($tipo_int == "a") $sel_ia = " selected";
echo "<table><tr onclick=\"document.getElementById('az_date').checked='1'\"><td>
<input type=\"radio\" id=\"az_date\" name=\"azione\" value=\"date\"$chk_az>
</td><td>".ucfirst(mex("porre",$pag))." </td><td><select name=\"var_data\">
".str_replace("\"$var_data\">","\"$var_data\" selected>",$option_var_pers)."
</select></td><td>".str_replace(" ","&nbsp;",mex("uguale a",$pag))."</td>
<td><b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\"><tr><td>
<table cellspacing=\"0\"><tr onclick=\"document.getElementById('opdat0').checked='1'\"><td>
<input type=\"radio\" id=\"opdat0\" name=\"oper_data\" value=\"\"$chk_opdat0>
</td><td><select name=\"subdata\">
<option value=\"gi\"$sel_gi>".mex("il giorno",$pag)."</option>
<option value=\"me\"$sel_me>".mex("il mese",$pag)."</option>
<option value=\"an\"$sel_an>".mex("l'anno",$pag)."</option>
<option value=\"gs\"$sel_gs>".mex("il giorno della settimana",$pag)."</option>
<option value=\"is\"$sel_is>".mex("la data",$pag)." (".mex("formato ISO",$pag).")</option>
<option value=\"da\"$sel_da>".mex("la data",$pag)." (".mex("formato corrente",$pag).")</option>
</select></td><td>
".str_replace(" ","&nbsp;",mex("della data",$pag))."</td><td>
<select name=\"var_data2\">
".str_replace("\"$var_data2\">","\"$var_data2\" selected>",$option_var_predef_data)."
</select></td><td>
<select name=\"oper_giorni\">
<option value=\"+\"$sel_piu>+</option>
<option value=\"-\"$sel_men>-</option>
</select></td><td>
<input type=\"text\" name=\"num_giorni\" value=\"$num_giorni\" size=\"3\" maxlength=\"150\">
</td><td>
<select name=\"tipo_giorni\">
<option value=\"g\"$sel_g>".mex("giorni",$pag)."</option>
<option value=\"m\"$sel_m>".mex("mesi",$pag)."</option>
<option value=\"a\"$sel_a>".mex("anni",$pag)."</option>
</select></td></tr></table></td></tr><tr><td>
<table cellspacing=\"0\"><tr onclick=\"document.getElementById('opdat1').checked='1'\"><td>
<input type=\"radio\" id=\"opdat1\" name=\"oper_data\" value=\"1\"$chk_opdat1>
</td><td>
".str_replace(" ","&nbsp;",mex("numero di",$pag))."</td><td>
<select name=\"tipo_int\">
<option value=\"g\"$seli_g>".mex("giorni",$pag)."</option>
<option value=\"m\"$seli_m>".mex("mesi",$pag)."</option>
<option value=\"a\"$seli_a>".mex("anni",$pag)."</option>
</select></td><td>
".str_replace(" ","&nbsp;",mex("dalla data",$pag))."</td><td>
<select name=\"var_opdat2\">
".str_replace("\"$var_opdat2\">","\"$var_opdat2\" selected>",$option_var_predef_data)."
</select></td><td>
".str_replace(" ","&nbsp;",mex("alla data",$pag))."</td><td>
<select name=\"var_opdat3\">
".str_replace("\"$var_opdat3\">","\"$var_opdat3\" selected>",$option_var_predef_data)."
</select></td></tr></table></td></tr></table>
</td></tr></table><br>";

if ($option_arr_pers) {
if ($azione == "unset") $chk_az = " checked";
else $chk_az = "";
echo "<table onclick=\"document.getElementById('az_azzera').checked='1'\"><tr><td>
<input type=\"radio\" id=\"az_azzera\" name=\"azione\" value=\"unset\"$chk_az>
</td><td>".str_replace(" ","&nbsp;",ucfirst(mex("azzera l'array",$pag)))."</td><td>
<select name=\"arr_azz\">
".str_replace("\"$arr_azz\">","\"$arr_azz\" selected>",$option_arr_pers)."
</select></td></tr></table><br>";
} # fine if ($option_arr_pers)

if ($opt_arr_var_non_predef) {
if ($azione == "array") $chk_az = " checked";
else $chk_az = "";
$chk_dat = "";
$chk_dap = "";
$chk_val = "";
$chk_cop = "";
if ($tipo_arr == "dat" or !$tipo_arr) $chk_dat = " checked";
if ($tipo_arr == "dap") $chk_dap = " checked";
if ($tipo_arr == "val") $chk_val = " checked";
if ($tipo_arr == "cop") $chk_cop = " checked";
echo "<table onclick=\"document.getElementById('az_array').checked='1'\"><tr><td>
<input type=\"radio\" id=\"az_array\" name=\"azione\" value=\"array\"$chk_az>
</td><td>".str_replace(" ","&nbsp;",ucfirst(mex("assegna all'array",$pag)))."</td><td>
<select name=\"array\">
".str_replace("\"$array\">","\"$array\" selected>",$opt_arr_var_non_predef)."
</select></td><td>
<b style=\"font-size: 260%;\">{</b></td><td>
<table cellspacing=\"0\">
<tr onclick=\"document.getElementById('arr_dat').checked='1'\"><td>
<input type=\"radio\" id=\"arr_dat\" name=\"tipo_arr\" value=\"dat\"$chk_dat></td><td>
".str_replace(" ","&nbsp;",mex("valori progressivi tra le date selezionate",$pag)." (".mex("nelle ripetizioni vengono considerate solo le prenotazioni nella data valutata",$pag)).").</td></tr>
<tr onclick=\"document.getElementById('arr_dap').checked='1'\"><td>
<input type=\"radio\" id=\"arr_dap\" name=\"tipo_arr\" value=\"dap\"$chk_dap></td><td>
".str_replace(" ","&nbsp;",mex("valori progressivi tra il primo arrivo e l'ultima partenza",$pag)." <small>(".mex("nelle ripetizioni vengono considerate solo le prenotazioni nella data valutata",$pag)).")</small>.</td></tr>
<tr onclick=\"document.getElementById('arr_val').checked='1'\"><td>
<input type=\"radio\" id=\"arr_val\" name=\"tipo_arr\" value=\"val\"$chk_val></td><td>
<table cellspacing=\"0\"><tr><td>
".str_replace(" ","&nbsp;",mex("una lista di valori predefiniti",$pag)).":</td>
<td><input type=\"text\" name=\"lista_val\" value=\"$lista_val\" size=\"26\"></td>
<td>(".str_replace(" ","&nbsp;",mex("separati da virgole",$pag)).").
</td></tr></table></td></tr>
<tr onclick=\"document.getElementById('arr_cop').checked='1'\"><td>
<input type=\"radio\" id=\"arr_cop\" name=\"tipo_arr\" value=\"cop\"$chk_cop></td><td>
<table cellspacing=\"0\"><tr><td>
".str_replace(" ","&nbsp;",mex("i valori dell'array",$pag)." ")."</td>
<td><select name=\"array2\">
".str_replace("\"$array2\">","\"$array2\" selected>",$option_arr_pers)."
</select>.</td></tr></table></td></tr></table>
</td></tr></table><br>";
} # fine if ($opt_arr_var_non_predef)

if ($azione == "break") $chk_az = " checked";
else $chk_az = "";
if ($break_cont == "cont") { $sel_fine = ""; $sel_cont = " selected"; }
else { $sel_fine = " selected"; $sel_cont = ""; }
echo "<table onclick=\"document.getElementById('az_break').checked='1'\"><tr><td>
<input type=\"radio\" id=\"az_break\" name=\"azione\" value=\"break\"$chk_az>
</td><td>".str_replace(" ","&nbsp;",ucfirst(mex("non processare le condizioni successive",$pag)))."
</td><td><select name=\"break_cont\">
<option value=\"\"$sel_fine>".mex("fino alla fine",$pag)."</option>
<option value=\"cont\"$sel_cont>".mex("fino al prossimo &quot;riprendi&quot;",$pag)."</option>
</select></td></tr></table><br>";

if ($azione == "cont") $chk_az = " checked";
else $chk_az = "";
echo "<table onclick=\"document.getElementById('az_cont').checked='1'\"><tr><td>
<input type=\"radio\" id=\"az_cont\" name=\"azione\" value=\"cont\"$chk_az>
</td><td>".str_replace(" ","&nbsp;",ucfirst(mex("riprendi a processare le condizioni successive",$pag)." ".mex("se precedentemente interrotte",$pag)))."
</td></tr></table><br>

<div class=\"txtcenter\">
<input class=\"sbutton\" type=\"submit\" value=\"";
if (!$num_cond_mod) echo ucfirst(mex("aggiungi la nuova condizione",$pag));
else echo ucfirst(mex("modifica la condizione",$pag))." $num_cond_mod_vedi";
echo "\"></div></div></form><br>";

if ($num_cond_mod) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$input_cond_contr
<div class=\"txtcenter\">
<input class=\"sbutton\" type=\"submit\" value=\"".ucfirst(mex("annulla",$pag))."\">
</div></div></form><br>";
} # fine if ($num_cond_mod)
echo "</div><br>";

} # fine if ($option_var_pers and !$contr_importa_vc)


echo "<br><hr style=\"width: 95%\"><br><div style=\"text-align: center;\">";

if ($contr_cond) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./modifica_contratto.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$contr_cond\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Modifica il documento",$pag)." $contr_cond\">
</div></form><br>";
} # fine if ($contr_cond)

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"personalizza.php#contratti\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Torna indietro",$pag)."\">
</div></form><br></div>";


} # fine if ($mostra_form_iniziale != "NO")




if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and $priv_mod_doc == "s" and $modifica_pers != "NO")
} # fine if ($id_utente)



?>
