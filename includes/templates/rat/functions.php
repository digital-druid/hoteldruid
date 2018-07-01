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




$var_mod = array();

$var_mod[0] = "estendi_ultima_data";
$var_mod[1] = "mostra_date_passate";
$var_mod[2] = "apertura_tag_font";
$var_mod[3] = "chiusura_tag_font";
$var_mod[4] = "periodi_menu";
$var_mod[5] = "stile_soldi";
$var_mod[6] = "stile_data";
$var_mod[7] = "fr_Valuta_sing";
$var_mod[8] = "fr_Valuta_plur";
$var_mod[9] = "anteponi_nome_valuta";
$var_mod[10] = "tariffe_mostra";
$var_mod[11] = "nomi_tariffe_imposte";
$var_mod[12] = "costi_aggiuntivi_mostra";
$var_mod[13] = "nomi_costi_agg_imposti";
$var_mod[14] = "mostra_caparra";
$var_mod[15] = "file_css_frame";
$var_mod[16] = "tema_modello";

$num_var_mod = count($var_mod);




function recupera_var_modello_rat ($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,$var_per_crea_mod,$anno_modello,$PHPR_TAB_PRE) {

$linee_file = file("$percorso_cartella_modello/$nome_file");
$num_linee_file = count($linee_file);
if (substr($linee_file[0],0,70) == "<?php if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed']) { ?>") $linee_file[0] = substr($linee_file[0],70);
if (substr($linee_file[($num_linee_file - 1)],-31) == "<?php } # fine if (!\$framed) ?>") $linee_file[($num_linee_file - 1)] = substr($linee_file[($num_linee_file - 1)],0,-31);
$var_anno = "\$".mex("var_anno",$pag);
for ($num_va = 0 ; $num_va < $num_var_mod ; $num_va++) ${"var_".$var_mod[$num_va]} = "\$".mex("var_".$var_mod[$num_va],$pag);
$var_colore_tema = "\$".mex("var_colore_tema",$pag);
$var_valore_tema = "\$".mex("var_valore_tema",$pag);
$var_stile_tabella_tariffe = "\$".mext_rat("var_stile_tabella_tariffe",$pag);
if ($var_per_crea_mod == "SI") {
$var_tipo_db = "\$".mex("var_tipo_db",$pag);
$var_nome_db = "\$".mex("var_nome_db",$pag);
$var_computer_db = "\$".mex("var_computer_db",$pag);
$var_porta_db = "\$".mex("var_porta_db",$pag);
$var_utente_db = "\$".mex("var_utente_db",$pag);
$var_password_db = "\$".mex("var_password_db",$pag);
$var_carica_estensione_db = "\$".mex("var_carica_estensione_db",$pag);
$var_prefisso_tabelle_db = "\$".mex("var_prefisso_tabelle_db",$pag);
} # fine if ($var_per_crea_mod == "SI")
# FRASI
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) ${"var_".$fr_frase[$num_fr]} = "\$".mext_rat("var_".$fr_frase[$num_fr],$pag);
$fine_variabili = "# ".mex("FINE VARIABILI MODIFICABILI",$pag);
global $num_periodi_date;

for ($num1 = 0 ; $num1 < $num_linee_file ; $num1++) {
$linea = togli_acapo($linee_file[$num1]);
unset($variabile);
if (substr($linea,0,strlen($var_anno)) == $var_anno) $variabile = "anno_modello_presente";
for ($num_va = 0 ; $num_va < $num_var_mod ; $num_va++) {
if (substr($linea,0,strlen(${"var_".$var_mod[$num_va]})) == ${"var_".$var_mod[$num_va]}) $variabile = $var_mod[$num_va];
} # fine for $num_va
$vlen = strlen($var_colore_tema."_");
if (substr($linea,0,$vlen) == $var_colore_tema."_") {
$variabile = "colore_tema_";
while (controlla_num_pos(substr($linea,$vlen,1)) == "SI") {
$variabile .= substr($linea,$vlen,1);
$vlen++;
} # fine while (controlla_num_pos(substr($linea,$vlen,1)) == "SI")
} # fine if (substr($linea,0,$vlen) == $var_colore_tema."_")
$vlen = strlen($var_valore_tema."_");
if (substr($linea,0,$vlen) == $var_valore_tema."_") {
$variabile = "valore_tema_";
while (controlla_num_pos(substr($linea,$vlen,1)) == "SI") {
$variabile .= substr($linea,$vlen,1);
$vlen++;
} # fine while (controlla_num_pos(substr($linea,$vlen,1)) == "SI")
} # fine if (substr($linea,0,$vlen) == $var_valore_tema."_")
if (substr($linea,0,strlen($var_stile_tabella_tariffe)) == $var_stile_tabella_tariffe) $variabile = "stile_tabella_tariffe";
if ($var_per_crea_mod == "SI") {
if (substr($linea,0,strlen($var_tipo_db)) == $var_tipo_db) $variabile = "tipo_db";
if (substr($linea,0,strlen($var_nome_db)) == $var_nome_db) $variabile = "nome_db";
if (substr($linea,0,strlen($var_computer_db)) == $var_computer_db) $variabile = "computer_db";
if (substr($linea,0,strlen($var_porta_db)) == $var_porta_db) $variabile = "porta_db";
if (substr($linea,0,strlen($var_utente_db)) == $var_utente_db) $variabile = "utente_db";
if (substr($linea,0,strlen($var_password_db)) == $var_password_db) $variabile = "password_db";
if (substr($linea,0,strlen($var_carica_estensione_db)) == $var_carica_estensione_db) $variabile = "carica_estensione_db";
if (substr($linea,0,strlen($var_prefisso_tabelle_db)) == $var_prefisso_tabelle_db) $variabile = "prefisso_tabelle_db";
} # fine if ($var_per_crea_mod == "SI")
# FRASI
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) {
$len = strlen(${"var_".$fr_frase[$num_fr]});
if (substr($linea,0,$len) == ${"var_".$fr_frase[$num_fr]} and (substr($linea,$len,1) == " " or substr($linea,$len,1) == "=")) $variabile = $fr_frase[$num_fr];
} # fine for $num_fr

if (!$num_periodi_date) {
if (substr($linea,0,strlen($var_periodi_menu)) == $var_periodi_menu) {
if (substr($linee_file[($num1 + 1)],0,16) == "<option value=\\\"") {
global $inizioperiodo0,$fineperiodo0,$intervalloperiodo0;
$inizioperiodo0 = explode("<option value=\\\"",$linea);
$inizioperiodo0 = explode("\\\">",$inizioperiodo0[1]);
$inizioperiodo0 = $inizioperiodo0[0];
if ($tipo_periodi == "s") $intervallo_base = 604800;
else $intervallo_base = 86400;
$data_prec = explode("-",$inizioperiodo0);
$data_corr = explode("-",substr($linee_file[($num1 + 1)],16,10));
$intervallo_prec = round(((mktime(0,0,0,$data_corr[1],$data_corr[2],$data_corr[0]) - mktime(0,0,0,$data_prec[1],$data_prec[2],$data_prec[0])) / $intervallo_base),0);
$intervalloperiodo0 = $intervallo_prec;
$num_periodi_date = 0;
while (substr($linee_file[($num1 + 1)],0,16) == "<option value=\\\"") {
$num1++;
$data_corr = explode("-",substr($linee_file[$num1],16,10));
$intervallo_corr = round(((mktime(0,0,0,$data_corr[1],$data_corr[2],$data_corr[0]) - mktime(0,0,0,$data_prec[1],$data_prec[2],$data_prec[0])) / $intervallo_base),0);
if ($intervallo_corr != $intervallo_prec) {
$data_succ= explode("-",substr($linee_file[($num1 + 1)],16,10));
$intervallo_succ = round(((mktime(0,0,0,$data_succ[1],$data_succ[2],$data_succ[0]) - mktime(0,0,0,$data_corr[1],$data_corr[2],$data_corr[0])) / $intervallo_base),0);
${"fineperiodo".$num_periodi_date} = $data_prec[0]."-".$data_prec[1]."-".$data_prec[2];
$num_periodi_date++;
global ${"inizioperiodo".$num_periodi_date},${"fineperiodo".$num_periodi_date},${"intervalloperiodo".$num_periodi_date};
${"inizioperiodo".$num_periodi_date} = $data_corr[0]."-".$data_corr[1]."-".$data_corr[2];
${"intervalloperiodo".$num_periodi_date} = $intervallo_succ;
$intervallo_prec = $intervallo_succ;
} # fine if ($intervallo_corr != $intervallo_prec)
$data_prec = $data_corr;
} # fine while (substr($linee_file[($num1 + 1)],0,strlen("<option value=\\\"")) == "<option value=\\\"")
${"fineperiodo".$num_periodi_date} = substr($linee_file[$num1],16,10);
$num_periodi_date++;
} # fine if (substr($linee_file[($num1 + 1)],0,16) == "<option value=\\\"")
} # fine if (substr($linea,0,strlen($var_periodi_menu)) == $var_periodi_menu)
} # fine if (!$num_periodi_date)

if (substr($linea,0,strlen($fine_variabili)) == $fine_variabili) break;

if ($variabile) {
global $$variabile;
$$variabile = explode("=",$linea);
$$variabile = trim(str_replace(${$variabile}[0]."=","",$linea));
if (substr($$variabile,-1) == ";") $$variabile = substr($$variabile,0,-1);
$$variabile = trim($$variabile);
if (substr($$variabile,0,1) == "\"" and substr($$variabile,-1) == "\"") $$variabile =  substr($$variabile,1,-1);
if (substr(str_replace(" ","",$$variabile),0,6) == "array(") {
$vett = $$variabile;
$$variabile = array();
${$variabile}['array_esistente'] = "SI";
$vett = preg_replace("/^array[ ]*\(/","",$vett);
if (substr($vett,-1) == ")") $vett = substr($vett,0,-1);
if (strcmp(trim($vett),"")) {
$vett = str_replace("\\\"","#@%&",str_replace("\\\\","#@%^",$vett)).",";
$in_apici = "NO";
unset($val_in_apici);
unset($val_non_apici);
for ($num2 = 0 ; $num2 < strlen($vett) ; $num2++) {
if (substr($vett,$num2,1) == "\"") {
if ($in_apici == "NO") $in_apici = "SI";
else $in_apici = "NO";
} # fine if (substr($vett,$num2,1) == "\"")
else {
if ($in_apici == "SI") $val_in_apici .= substr($vett,$num2,1);
else {
if (substr($vett,$num2,1) == ",") {
if ($val_in_apici) $val = $val_in_apici;
else $val = trim($val_non_apici);
${$variabile}[$key] = str_replace("#@%^","\\",str_replace("#@%&","\"",$val));
unset($val_in_apici);
unset($val_non_apici);
} # fine if (substr($vett,$num2,1) == ",")
else {
if (substr($vett,$num2,2) == "=>") {
if ($val_in_apici) $key = $val_in_apici;
else $key = trim($val_non_apici);
$key = str_replace("#@%^","\\",str_replace("#@%&","\"",$key));
$num2++;
unset($val_in_apici);
unset($val_non_apici);
} # fine if (substr($vett,$num2,2) == "=>")
else $val_non_apici .= substr($vett,$num2,1);
} # fine else if (substr($vett,$num2,1) == ",")
} # fine else if ($in_apici == "SI")
} # fine else if (substr($vett,$num2,1) == "\"")
} # fine for $num2
} # fine if (strcmp(trim($vett),""))
} # fine if (substr($$variabile,0,5) == "array")
else $$variabile = str_replace("\\\"","\"",$$variabile);
} # fine if ($variabile)

} # fine for $num1

$linee_file = implode("",$linee_file);
global $prima_parte_html,$ultima_parte_html;
$prima_parte_html = explode("<!-- ".mex("FINE DELLA PRIMA PARTE DELL'HTML PERSONALE",$pag)."  -->",$linee_file);
$prima_parte_html = $prima_parte_html[0];
if (togli_acapo($prima_parte_html) != "") {
while (togli_acapo(substr($prima_parte_html,0,1)) == "") $prima_parte_html = substr($prima_parte_html,1);
while (togli_acapo(substr($prima_parte_html,-1)) == "") $prima_parte_html = substr($prima_parte_html,0,-1);
} # fine if (togli_acapo($prima_parte_html) != "")
$ultima_parte_html = explode("<!-- ".mex("INIZIO DELLA SECONDA PARTE DELL'HTML PERSONALE",$pag)."  -->",$linee_file);
$ultima_parte_html = explode("<!-- ".mex("FINE DELLA SECONDA PARTE DELL'HTML PERSONALE",$pag)."  -->",$ultima_parte_html[1]);
$ultima_parte_html = $ultima_parte_html[0];
if (togli_acapo($ultima_parte_html) != "") {
while (togli_acapo(substr($ultima_parte_html,0,1)) == "") $ultima_parte_html = substr($ultima_parte_html,1);
while (togli_acapo(substr($ultima_parte_html,-1)) == "") $ultima_parte_html = substr($ultima_parte_html,0,-1);
} # fine if (togli_acapo($ultima_parte_html) != "")


if ($var_per_crea_mod == "SI") {
if (!$anno_modello) $anno_modello = $anno_modello_presente;
$tableanni = $PHPR_TAB_PRE."anni";
if (controlla_anno($anno_modello) != "SI") {
$continua = "NO";
$anno_modello = "";
} # fine if (controlla_anno($anno_modello) != "SI")
else {
$anno_esistente = esegui_query("select * from $tableanni where idanni = '$anno_modello'");
if (numlin_query($anno_esistente) != 1) $continua = "NO";
} # fine else if (controlla_anno($anno_modello) != "SI")
if ($continua != "NO") {

$SI = mex("SI",$pag);
$NO = mex("NO",$pag);
global $M_PHPR_DB_TYPE,$M_PHPR_DB_NAME,$M_PHPR_DB_HOST,$M_PHPR_DB_PORT,$M_PHPR_DB_USER,$M_PHPR_DB_PASS,$M_PHPR_LOAD_EXT,$M_PHPR_TAB_PRE,$m_stile_soldi,$m_stile_data,$m_valuta_sing,$m_valuta_plur;
if ($tipo_db == "mysql" and @function_exists('mysqli_connect')) $tipo_db = "mysqli";
$M_PHPR_DB_TYPE = $tipo_db;
$M_PHPR_DB_NAME = $nome_db;
$M_PHPR_DB_HOST = $computer_db;
$M_PHPR_DB_PORT = $porta_db;
$M_PHPR_DB_USER = $utente_db;
$M_PHPR_DB_PASS = $password_db;
$M_PHPR_LOAD_EXT = $carica_estensione_db;
$M_PHPR_TAB_PRE = $prefisso_tabelle_db;
$m_stile_soldi = $stile_soldi;
$m_stile_data = $stile_data;
$m_valuta_sing = $fr_Valuta_sing;
$m_valuta_plur = $fr_Valuta_plur;

if (strtoupper($estendi_ultima_data) == $SI) $estendi_ultima_data = "SI";
else $estendi_ultima_data = "NO";
if (strtoupper($mostra_date_passate) == $NO) $mostra_date_passate = "NO";
else $mostra_date_passate = "SI";
$tablenometariffe_modello = $PHPR_TAB_PRE."ntariffe".$anno_modello;
$rigatariffe = esegui_query("select * from $tablenometariffe_modello where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
$tariffa = "tariffa".$numtariffa;
$nome_tariffa_imposto = "nome_tariffa_imposto".$numtariffa;
global $$tariffa,$$nome_tariffa_imposto;
$$tariffa = "";
$$nome_tariffa_imposto = "";
if (strtoupper($tariffe_mostra[$numtariffa]) == $SI) $$tariffa = "SI";
$$nome_tariffa_imposto = $nomi_tariffe_imposte[$numtariffa];
} # fine for $numtariffa
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe_modello,"NO");
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
$attiva_costo = "attiva_costo".$dati_ca[$numca]['id'];
$nome_costo_imposto = "nome_costo_imposto".$dati_ca[$numca]['id'];
global $$attiva_costo,$$nome_costo_imposto;
$$attiva_costo = "";
if (strtoupper($costi_aggiuntivi_mostra[$dati_ca[$numca]['id']]) == $SI) $$attiva_costo = "SI";
$$nome_costo_imposto = $nomi_costi_agg_imposti[$dati_ca[$numca]['id']];
} # fine for $numca
if (strtoupper($mostra_caparra) == $SI) $mostra_caparra = "SI";
else $mostra_caparra = "NO";

if (get_magic_quotes_gpc()) {
$prima_parte_html = addslashes($prima_parte_html);
$ultima_parte_html = addslashes($ultima_parte_html);
} # fine if (get_magic_quotes_gpc())

} # fine if ($continua != "NO")
} # fine if ($var_per_crea_mod == "SI")


} # fine function recupera_var_modello_rat







function crea_modello_rat ($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,$silenzio,$fr_frase,$frase,$num_frasi,$tipo_periodi) {
global $num_periodi_date,$M_PHPR_DB_TYPE,$M_PHPR_DB_NAME,$M_PHPR_DB_HOST,$M_PHPR_DB_PORT,$M_PHPR_DB_USER,$M_PHPR_DB_PASS,$M_PHPR_LOAD_EXT,$M_PHPR_TAB_PRE,$estendi_ultima_data,$mostra_date_passate;
global $m_stile_soldi,$m_stile_data,$anteponi_nome_valuta,$mostra_caparra,$modello_esistente,$cambia_frasi,$template_data_dir,$template_file_name,$parola_settimane;
global $apertura_tag_font,$chiusura_tag_font,$stile_tabella_tariffe,$m_valuta_sing,$m_valuta_plur,$prima_parte_html,$ultima_parte_html,$file_css_frame,$tema_modello;
$tablenometariffe_modello = $PHPR_TAB_PRE."ntariffe".$anno_modello;
$tableperiodi_modello = $PHPR_TAB_PRE."periodi".$anno_modello;
$tableanni = $PHPR_TAB_PRE."anni";

if (controlla_anno($anno_modello) != "SI") {
$continua = "NO";
$anno_modello = "";
} # fine if (controlla_anno($anno_modello) != "SI")
else {
$anno_esistente = esegui_query("select * from $tableanni where idanni = '$anno_modello'");
if (numlin_query($anno_esistente) != 1) $continua = "NO";
} # fine else if (controlla_anno($anno_modello) != "SI")
if ($continua != "NO") {

if ($estendi_ultima_data != "SI") $estendi_ultima_data = "NO";
if ($mostra_date_passate != "NO") $mostra_date_passate = "SI";

$date_in_menu = "";
$idfineperiodo_prec = -10;
if (!$num_periodi_date or controlla_num_pos($num_periodi_date) == "NO") $num_periodi_date = 1;
for ($num1 = 0 ; $num1 < $num_periodi_date ; $num1++) {
global ${"inizioperiodo".$num1},${"fineperiodo".$num1},${"intervalloperiodo".$num1};
$inizioperiodo = aggslashdb(${"inizioperiodo".$num1});
$fineperiodo = aggslashdb(${"fineperiodo".$num1});
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi_modello where datainizio = '$inizioperiodo' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo == 0) { $idinizioperiodo = 10000; }
else { $idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi'); }
$inizioperiodo = $idinizioperiodo;
if ($estendi_ultima_data == "SI" and $num1 == ($num_periodi_date - 1)) {
$idfineperiodo = esegui_query("select max(idperiodi) from $tableperiodi_modello");
$idfineperiodo = risul_query($idfineperiodo,0,0);
} # fine if ($estendi_ultima_data == "SI" and $num1 == ($num_periodi_date - 1))
else {
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi_modello where datafine = '$fineperiodo' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo == 0) { $idfineperiodo = -1; }
else { $idfineperiodo = risul_query($idfineperiodo,0,'idperiodi'); }
} # fine else if ($estendi_ultima_data == "SI" and $num1 == ($num_periodi_date - 1))
$fineperiodo = $idfineperiodo ;
${"inizioperiodo".$num1} = $inizioperiodo;
${"fineperiodo".$num1} = $fineperiodo;
if ($idfineperiodo < $idinizioperiodo) $continua = "NO";
if (($idfineperiodo_prec + 1) > $idinizioperiodo) $continua = "NO";
$idfineperiodo_prec = $idfineperiodo;
${"intervalloperiodo".$num1} = aggslashdb(${"intervalloperiodo".$num1});
if (!${"intervalloperiodo".$num1} or controlla_num_pos(${"intervalloperiodo".$num1}) == "NO" or ${"intervalloperiodo".$num1} > 99) $continua = "NO";
} # fine for $num1
if ($continua == "NO") {
if ($silenzio == "NO") echo mex("Le date sono sbagliate",$pag).". <br>";
} # fine if ($continua == "NO")
else {
$file_intero = file(C_DATI_PATH."/selectperiodi$anno_modello.1.php");
$num_linee_file_intero = count($file_intero);
$pag_gm = "giorni_mesi.php";
$m_tipo_periodi = esegui_query("select tipo_periodi from $tableanni where idanni = '$anno_modello'");
$m_tipo_periodi = risul_query($m_tipo_periodi,0,0);
for ($num1 = 0 ; $num1 < $num_periodi_date ; $num1++) {
$inizioperiodo = ${"inizioperiodo".$num1};
$fineperiodo = ${"fineperiodo".$num1};
$num_intervallo = 1;
for ($num2 = 0 ; $num2 < $num_linee_file_intero ; $num2++) {
if (substr($file_intero[$num2],0,7) == "<option") {
$data_option = substr($file_intero[$num2],16,10);
$id_data_option = esegui_query("select idperiodi from $tableperiodi_modello where datainizio = '$data_option' ");
$esiste_data_option = numlin_query($id_data_option);
if ($esiste_data_option == 1) $id_data_option = risul_query($id_data_option,0,'idperiodi');
else {
$id_data_option = esegui_query("select idperiodi from $tableperiodi_modello where datafine = '$data_option' ");
$id_data_option = risul_query($id_data_option,0,'idperiodi');
} # fine else if ($esiste_data_option == 1)
if ($id_data_option >= $inizioperiodo and $id_data_option <= ($fineperiodo + 1)) {
if ($num_intervallo == 1) {
$giorno_option = substr($data_option,8,2);
$mese_option = substr($data_option,5,2);
$anno_option = substr($data_option,0,4);
$nome_giorno = date("D" , mktime(0,0,0,$mese_option,$giorno_option,$anno_option));
$nome_mese = date("M" , mktime(0,0,0,$mese_option,$giorno_option,$anno_option));
if ($m_tipo_periodi == "g") {
if ($nome_giorno == "Sun") $nome_giorno = mex2(" Do",$pag_gm,$lingua_modello);
if ($nome_giorno == "Mon") $nome_giorno = mex2(" Lu",$pag_gm,$lingua_modello);
if ($nome_giorno == "Tue") $nome_giorno = mex2(" Ma",$pag_gm,$lingua_modello);
if ($nome_giorno == "Wed") $nome_giorno = mex2(" Me",$pag_gm,$lingua_modello);
if ($nome_giorno == "Thu") $nome_giorno = mex2(" Gi",$pag_gm,$lingua_modello);
if ($nome_giorno == "Fri") $nome_giorno = mex2(" Ve",$pag_gm,$lingua_modello);
if ($nome_giorno == "Sat") $nome_giorno = mex2(" Sa",$pag_gm,$lingua_modello);
} # fine if ($m_tipo_periodi == "g")
else $nome_giorno = "";
if ($nome_mese == "Jan") $nome_mese = mex2("Gen",$pag_gm,$lingua_modello);
if ($nome_mese == "Feb") $nome_mese = mex2("Feb",$pag_gm,$lingua_modello);
if ($nome_mese == "Mar") $nome_mese = mex2("Mar",$pag_gm,$lingua_modello);
if ($nome_mese == "Apr") $nome_mese = mex2("Apr",$pag_gm,$lingua_modello);
if ($nome_mese == "May") $nome_mese = mex2("Mag",$pag_gm,$lingua_modello);
if ($nome_mese == "Jun") $nome_mese = mex2("Giu",$pag_gm,$lingua_modello);
if ($nome_mese == "Jul") $nome_mese = mex2("Lug",$pag_gm,$lingua_modello);
if ($nome_mese == "Aug") $nome_mese = mex2("Ago",$pag_gm,$lingua_modello);
if ($nome_mese == "Sep") $nome_mese = mex2("Set",$pag_gm,$lingua_modello);
if ($nome_mese == "Oct") $nome_mese = mex2("Ott",$pag_gm,$lingua_modello);
if ($nome_mese == "Nov") $nome_mese = mex2("Nov",$pag_gm,$lingua_modello);
if ($nome_mese == "Dec") $nome_mese = mex2("Dic",$pag_gm,$lingua_modello);
$date_in_menu .= "<option value=\\\"$data_option\\\">$nome_mese $giorno_option$nome_giorno, $anno_option</option>
";
} # fine if ($num_intervallo == 1)
if ($num_intervallo == ${"intervalloperiodo".$num1}) $num_intervallo = 1;
else $num_intervallo++;
} # fine if ($id_data_option > $inizioperiodo and...
} # fine if (substr($file_intero[$num2],0,7) == "<option")
} # fine for $num2
} # fine for $num1

} # fine else if ($continua == "NO")

$dati_ca = dati_costi_agg_ntariffe($tablenometariffe_modello,"NO");

} # fine if ($continua != "NO")


if ($continua != "NO") {

$rigatariffe = esegui_query("select * from $tablenometariffe_modello where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
$tariffe_mostra = "";
$nomi_tariffe_imposte = "";
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
$tariffa = "tariffa".$numtariffa;
global $$tariffa;
if ($$tariffa == "SI") $tariffe_mostra .= " $numtariffa => \"".mex("SI",$pag)."\",";
$nome_tariffa_imposto = "nome_tariffa_imposto".$numtariffa;
global $$nome_tariffa_imposto;
$$nome_tariffa_imposto = formatta_input_var_x_file($$nome_tariffa_imposto);
if ($$nome_tariffa_imposto) $nomi_tariffe_imposte .= "$numtariffa => \"".$$nome_tariffa_imposto."\",";
} # fine for $numtariffa
if ($tariffe_mostra) $tariffe_mostra = substr($tariffe_mostra,0,-1);
if ($nomi_tariffe_imposte) $nomi_tariffe_imposte = substr($nomi_tariffe_imposte,0,-1);

unset($nomi_costi_imposti);
unset($costi_attivati);
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
global ${"attiva_costo".$dati_ca[$numca]['id']};
if (${"attiva_costo".$dati_ca[$numca]['id']} == "SI") {
$costi_attivati .= " ".$dati_ca[$numca]['id']." => \"".mex("SI",$pag)."\",";
$nome_costo_imposto = "nome_costo_imposto".$dati_ca[$numca]['id'];
global $$nome_costo_imposto;
$$nome_costo_imposto = formatta_input_var_x_file($$nome_costo_imposto);
if ($$nome_costo_imposto) $nomi_costi_imposti .= $dati_ca[$numca]['id']." => \"".$$nome_costo_imposto."\",";
} # fine if (${"attiva_costo".$dati_ca[$numca][id]} == "SI")
} # fine for $num1
if ($nomi_costi_imposti) $nomi_costi_imposti = substr($nomi_costi_imposti,0,-1);
if ($costi_attivati) $costi_attivati = substr($costi_attivati,0,-1);

if ($file_css_frame == "http://") $file_css_frame = "";

$num_colori = 0;
$extra_head_frame = "";
if (strcmp($tema_modello,"")) {
include("./includes/templates/$template_data_dir/themes.php");
$num_temi = count($template_theme_name);
for ($num1 = 1 ; $num1 <= $num_temi ; $num1++) {
if ($tema_modello == $template_theme_name[$num1]) {
$tema_trovato = 1;
$tema_sel = $num1;
} # fine if ($tema_modello == $template_theme_name[$num1])
} # fine for $num1
if ($tema_trovato) {
$prima_parte_html = $template_theme_html_pre[$tema_sel];
$ultima_parte_html = $template_theme_html_post[$tema_sel];
$extra_head_frame = $framed_mode_extra_head[$tema_sel];
$valori_tema = $template_theme_values[$tema_sel];
$num_valori = count($valori_tema);
for ($num1 = 1 ; $num1 <= $num_valori ; $num1++) {
global ${"valore_tema_".$num1};
${"valore_tema_".$num1} = formatta_input_var_x_file(${"valore_tema_".$num1});
$valore_sost = ${"valore_tema_".$num1};
if (!strcmp($valore_sost,"")) $valore_sost = $valori_tema[$num1]['null'];
elseif (strcmp($valori_tema[$num1]['replace'],"")) $valore_sost = str_replace("[theme_value_$num1]",$valore_sost,$valori_tema[$num1]['replace']);
$prima_parte_html = str_replace("[theme_value_$num1]",$valore_sost,$prima_parte_html);
$ultima_parte_html = str_replace("[theme_value_$num1]",$valore_sost,$ultima_parte_html);
$extra_head_frame = str_replace("[theme_value_$num1]",$valore_sost,$extra_head_frame);
} # fine for $num1
$colori_tema = $template_theme_colors[$tema_sel];
$num_colori = count($colori_tema);
for ($num1 = 1 ; $num1 <= $num_colori ; $num1++) {
global ${"colore_tema_".$num1};
if (!preg_match("/^#[0-9a-f]{3,3}$/i",${"colore_tema_".$num1}) and !preg_match("/^#[0-9a-f]{6,6}$/i",${"colore_tema_".$num1})) ${"colore_tema_".$num1} = $colori_tema[$num1]['default'];
$prima_parte_html = str_replace("[theme_color_$num1]",${"colore_tema_".$num1},$prima_parte_html);
$ultima_parte_html = str_replace("[theme_color_$num1]",${"colore_tema_".$num1},$ultima_parte_html);
$extra_head_frame = str_replace("[theme_color_$num1]",${"colore_tema_".$num1},$extra_head_frame);
} # fine for $num1
} # fine if ($tema_trovato)
else $tema_modello = "";
} # fine if (strcmp($tema_modello,""))

if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) {
$HOTELD_DB_TYPE = "";
$HOTELD_DB_NAME = "";
$HOTELD_DB_HOST = "";
$HOTELD_DB_PORT = "";
$HOTELD_DB_USER = "";
$HOTELD_DB_PASS = "";
$HOTELD_TAB_PRE = "";
include(C_EXT_DB_DATA_PATH);
if ($HOTELD_DB_TYPE) $M_PHPR_DB_TYPE = "";
if ($HOTELD_DB_NAME) $M_PHPR_DB_NAME = "";
if ($HOTELD_DB_HOST) $M_PHPR_DB_HOST = "";
if (strcmp($HOTELD_DB_PORT,"")) $M_PHPR_DB_PORT = "";
if ($HOTELD_DB_USER) $M_PHPR_DB_USER = "";
if (strcmp($HOTELD_DB_PASS,"")) $M_PHPR_DB_PASS = "";
if ($HOTELD_TAB_PRE) $M_PHPR_TAB_PRE = "";
} # fine if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH)

$M_PHPR_DB_TYPE = formatta_input_var_x_file($M_PHPR_DB_TYPE);
$M_PHPR_DB_NAME = formatta_input_var_x_file($M_PHPR_DB_NAME);
$M_PHPR_DB_HOST = formatta_input_var_x_file($M_PHPR_DB_HOST);
$M_PHPR_DB_PORT = formatta_input_var_x_file($M_PHPR_DB_PORT);
$M_PHPR_DB_USER = formatta_input_var_x_file($M_PHPR_DB_USER);
$M_PHPR_DB_PASS = formatta_input_var_x_file($M_PHPR_DB_PASS);
$M_PHPR_LOAD_EXT = formatta_input_var_x_file($M_PHPR_LOAD_EXT);
$M_PHPR_TAB_PRE = formatta_input_var_x_file($M_PHPR_TAB_PRE);
$m_stile_soldi = formatta_input_var_x_file($m_stile_soldi);
$m_stile_data = formatta_input_var_x_file($m_stile_data);
$anteponi_nome_valuta = formatta_input_var_x_file($anteponi_nome_valuta);
$mostra_caparra = formatta_input_var_x_file($mostra_caparra);
$apertura_tag_font = formatta_input_var_x_file($apertura_tag_font);
$chiusura_tag_font = formatta_input_var_x_file($chiusura_tag_font);
$stile_tabella_tariffe = formatta_input_var_x_file($stile_tabella_tariffe);
$m_valuta_sing = formatta_input_var_x_file($m_valuta_sing);
$m_valuta_plur = formatta_input_var_x_file($m_valuta_plur);
$file_css_frame = formatta_input_var_x_file($file_css_frame);
$extra_head_frame = formatta_input_var_x_file($extra_head_frame);
if (get_magic_quotes_gpc()) $prima_parte_html = stripslashes($prima_parte_html);
$prima_parte_html = str_replace("<?","ERROR",$prima_parte_html);
$prima_parte_html = str_replace("?>","ERROR",$prima_parte_html);
$prima_parte_html = str_replace("<%","ERROR",$prima_parte_html);
$prima_parte_html = str_replace("%>","ERROR",$prima_parte_html);
$prima_parte_html = preg_replace("/<script[^>]*php.*>/i","ERROR",$prima_parte_html);
if (get_magic_quotes_gpc()) $ultima_parte_html = stripslashes($ultima_parte_html);
$ultima_parte_html = str_replace("<?","ERROR",$ultima_parte_html);
$ultima_parte_html = str_replace("?>","ERROR",$ultima_parte_html);
$ultima_parte_html = str_replace("<%","ERROR",$ultima_parte_html);
$ultima_parte_html = str_replace("%>","ERROR",$ultima_parte_html);
$ultima_parte_html = preg_replace("/<script[^>]*php.*>/i","ERROR",$ultima_parte_html);
# FRASI
if ($cambia_frasi == "SI" or $modello_esistente == "SI") {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) {
global ${$fr_frase[$num_fr]};
${$fr_frase[$num_fr]} = formatta_input_var_x_file(${$fr_frase[$num_fr]});
} # fine for $num_fr
} # fine if ($cambia_frasi == "SI" or $modello_esistente == "SI")
else {
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) ${$fr_frase[$num_fr]} = mext_rat($frase[$num_fr],$pag,$lingua_modello);
} # fine else if ($cambia_frasi == "SI" or $modello_esistente == "SI")

$cost_percorso_a_dati = "";
if (function_exists("realpath")) {
if (realpath(C_DATI_PATH."/")) $cost_percorso_a_dati = realpath(C_DATI_PATH."/")."/";
} # fine if (function_exists("realpath"))
if ((string) $cost_percorso_a_dati == "") {
if (substr(C_DATI_PATH,0,1) == "/") $cost_percorso_a_dati = C_DATI_PATH;
else {
$dati_path = C_DATI_PATH;
if (substr($dati_path,0,2) == "./") $dati_path = substr($dati_path,1);
else $dati_path = "/".$dati_path;
if ($_SERVER["SCRIPT_FILENAME"]) $cost_percorso_a_dati = dirname($_SERVER["SCRIPT_FILENAME"]).$dati_path;
else {
if ($HTTP_SERVER_VARS["SCRIPT_FILENAME"]) $cost_percorso_a_dati = dirname($HTTP_SERVER_VARS["SCRIPT_FILENAME"]).$dati_path;
else {
if ($SCRIPT_FILENAME) $cost_percorso_a_dati = dirname($SCRIPT_FILENAME).$dati_path;
else $cost_percorso_a_dati = "./";
} # fine else if ($HTTP_SERVER_VARS["SCRIPT_FILENAME"])
} # fine else if ($_SERVER["SCRIPT_FILENAME"])
} # fine else if (substr(C_DATI_PATH,0,1) == "/")
} # fine if ((string) $cost_percorso_a_dati == "")

if ($template_file_name[$lingua_modello]) $nome_file = $template_file_name[$lingua_modello];
else {
$template_file_name_m = mext_rat($template_file_name['ita'],$pag,$lingua_modello);
if ($template_file_name_m != $template_file_name['ita'] and $template_file_name_m != $template_file_name['en'] and $template_file_name_m != $template_file_name['es']) $nome_file = $template_file_name_m;
else $nome_file = $lingua_modello."_".$template_file_name['en'];
} # fine else if ($template_file_name[$lingua_modello])
$file = @fopen("$percorso_cartella_modello/$nome_file","w+");
if ($file) {
flock($file,2);
fwrite($file,"<?php if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed']) { ?>$prima_parte_html




<!-- ".mex("FINE DELLA PRIMA PARTE DELL'HTML PERSONALE",$pag)."  -->


<?php
} # fine if (!@\$framed and !@\$_GET['framed'] and !@\$_POST['framed'])


# ".mex("INIZIO VARIABILI MODIFICABILI",$pag)." (".mex("modificare il valore sulla destra",$pag).")

# ".mex("Inserire in questa variabile il nome della pagina se \$PHP_SELF non è definita",$pag)."
\$".mex("var_nome_pagina",$pag)." = \"\";

\$".mex("var_anno",$pag)." = $anno_modello;
\$".mex("var_tipo_db",$pag)." = \"$M_PHPR_DB_TYPE\";
\$".mex("var_nome_db",$pag)." = \"$M_PHPR_DB_NAME\";
\$".mex("var_computer_db",$pag)." = \"$M_PHPR_DB_HOST\";
\$".mex("var_porta_db",$pag)." = \"$M_PHPR_DB_PORT\";
\$".mex("var_utente_db",$pag)." = \"$M_PHPR_DB_USER\";
\$".mex("var_password_db",$pag)." = \"$M_PHPR_DB_PASS\";
\$".mex("var_carica_estensione_db",$pag)." = \"".mex("$M_PHPR_LOAD_EXT",$pag)."\";
\$".mex("var_prefisso_tabelle_db",$pag)." = \"$M_PHPR_TAB_PRE\";
\$".mex("var_lingua_modello",$pag)." = \"$lingua_modello\";
\$".mex("var_stile_soldi",$pag)." = \"".mex("$m_stile_soldi",$pag)."\";
\$".mex("var_stile_data",$pag)." = \"".mex("$m_stile_data",$pag)."\";
\$".mex("var_anteponi_nome_valuta",$pag)." = \"".mex("$anteponi_nome_valuta",$pag)."\";
\$".mex("var_estendi_ultima_data",$pag)." = \"".mex("$estendi_ultima_data",$pag)."\";
\$".mex("var_mostra_date_passate",$pag)." = \"".mex("$mostra_date_passate",$pag)."\";
\$".mex("var_tariffe_mostra",$pag)." = array($tariffe_mostra);
\$".mex("var_nomi_tariffe_imposte",$pag)." = array($nomi_tariffe_imposte);
\$".mex("var_costi_aggiuntivi_mostra",$pag)." = array($costi_attivati);
\$".mex("var_nomi_costi_agg_imposti",$pag)." = array($nomi_costi_imposti);
\$".mex("var_mostra_caparra",$pag)." = \"".mex("$mostra_caparra",$pag)."\";

\$".mex("var_apertura_tag_font",$pag)." = \"$apertura_tag_font\";
\$".mex("var_chiusura_tag_font",$pag)." = \"$chiusura_tag_font\";
\$".mext_rat("var_stile_tabella_tariffe",$pag)." = \"$stile_tabella_tariffe\";
\$".mex("var_file_css_frame",$pag)." = \"$file_css_frame\";
\$".mex("var_tema_modello",$pag)." = \"$tema_modello\";
");
for ($num1 = 1 ; $num1 <= $num_colori ; $num1++) fwrite($file,"\$".mex("var_colore_tema",$pag)."_$num1 = \"".${"colore_tema_".$num1}."\";
");
for ($num1 = 1 ; $num1 <= $num_valori ; $num1++) fwrite($file,"\$".mex("var_valore_tema",$pag)."_$num1 = \"".${"valore_tema_".$num1}."\";
");
fwrite($file,"
# ".mex("FRASI",$pag)."
\$".mex("var_fr_Valuta_sing",$pag)." = \"".$m_valuta_sing."\";
\$".mex("var_fr_Valuta_plur",$pag)." = \"".$m_valuta_plur."\";
");
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) fwrite($file,"\$".mext_rat("var_".$fr_frase[$num_fr],$pag)." = \"".${$fr_frase[$num_fr]}."\";
");
fwrite($file,"
# ".mex("PERIODI NEI MENU",$pag)."
\$".mex("var_periodi_menu",$pag)." = \"$date_in_menu\";

# ".mex("FINE VARIABILI MODIFICABILI",$pag)."



############################################################################
###        ".mex("NON MODIFICARE NIENTE A PARTIRE DA QUI",$pag)."
############################################################################

error_reporting(E_ALL ^ E_NOTICE);
\$PHPR_LOG = \"NO\";
\$pag = \$".mex("var_nome_pagina",$pag).";
if (!\$pag) {
if (@\$PHP_SELF or @\$_SERVER[\"PHP_SELF\"] or @\$HTTP_SERVER_VARS[\"PHP_SELF\"]) {
if (@\$_SERVER[\"PHP_SELF\"]) \$PHP_SELF = \$_SERVER[\"PHP_SELF\"];
else if (@\$HTTP_SERVER_VARS[\"PHP_SELF\"]) \$PHP_SELF = \$HTTP_SERVER_VARS[\"PHP_SELF\"];
\$pag = explode(\"/\",\$PHP_SELF);
\$pag = \$pag[(count(\$pag)-1)];
} # fine if (@\$PHP_SELF or @\$_SERVER[\"PHP_SELF\"] or...
else echo \"".mex("La variabile \\\$PHP_SELF non è definita, si dovrà editare a mano questa pagina per inserirne il nome",$pag).".<br>\";
} # fine if (!\$pag)

define('C_PERCORSO_A_DATI',\"$cost_percorso_a_dati\");
define('C_PAGINA_WEB','1');

\$anno = \$".mex("var_anno",$pag).";
\$PHPR_DB_TYPE = \$".mex("var_tipo_db",$pag).";
\$PHPR_DB_NAME = \$".mex("var_nome_db",$pag).";
\$PHPR_DB_HOST = \$".mex("var_computer_db",$pag).";
\$PHPR_DB_PORT = \$".mex("var_porta_db",$pag).";
\$PHPR_DB_USER = \$".mex("var_utente_db",$pag).";
\$PHPR_DB_PASS = \$".mex("var_password_db",$pag).";
if (strtoupper(\$".mex("var_carica_estensione_db",$pag).") == \"".mex("SI",$pag)."\") \$PHPR_LOAD_EXT = \"SI\";
else \$PHPR_LOAD_EXT = \"NO\";
\$PHPR_TAB_PRE = \$".mex("var_prefisso_tabelle_db",$pag).";
");
if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) fwrite($file,"\$HOTELD_DB_TYPE = \"\";
\$HOTELD_DB_NAME = \"\";
\$HOTELD_DB_HOST = \"\";
\$HOTELD_DB_PORT = \"\";
\$HOTELD_DB_USER = \"\";
\$HOTELD_DB_PASS = \"\";
\$HOTELD_TAB_PRE = \"\";
require('".C_EXT_DB_DATA_PATH."');
if (\$HOTELD_DB_TYPE) \$PHPR_DB_TYPE = \$HOTELD_DB_TYPE;
if (\$HOTELD_DB_NAME) \$PHPR_DB_NAME = \$HOTELD_DB_NAME;
if (\$HOTELD_DB_HOST) \$PHPR_DB_HOST = \$HOTELD_DB_HOST;
if (strcmp(\$HOTELD_DB_PORT,\"\")) \$PHPR_DB_PORT = \$HOTELD_DB_PORT;
if (\$HOTELD_DB_USER) \$PHPR_DB_USER = \$HOTELD_DB_USER;
if (strcmp(\$HOTELD_DB_PASS,\"\")) \$PHPR_DB_PASS = \$HOTELD_DB_PASS;
if (\$HOTELD_TAB_PRE) \$PHPR_TAB_PRE = \$HOTELD_TAB_PRE;
");
fwrite($file,"\$lingua_modello = \$".mex("var_lingua_modello",$pag).";
if (\$".mex("var_stile_soldi",$pag)." == \"".mex("europa",$pag)."\") \$stile_soldi = \"europa\";
if (\$".mex("var_stile_soldi",$pag)." == \"".mex("usa",$pag)."\") \$stile_soldi = \"usa\";
if (\$".mex("var_stile_data",$pag)." == \"".mex("europa",$pag)."\") \$stile_data = \"europa\";
if (\$".mex("var_stile_data",$pag)." == \"".mex("usa",$pag)."\") \$stile_data = \"usa\";
if (strtoupper(\$".mex("var_anteponi_nome_valuta",$pag).") == \"".mex("SI",$pag)."\") \$anteponi_nome_valuta = \"SI\";
else \$anteponi_nome_valuta = \"NO\";
if (strtoupper(\$".mex("var_estendi_ultima_data",$pag).") == \"".mex("SI",$pag)."\") \$estendi_ultima_data = \"SI\";
else \$estendi_ultima_data = \"NO\";
if (strtoupper(\$".mex("var_mostra_date_passate",$pag).") == \"".mex("NO",$pag)."\") \$mostra_date_passate = \"NO\";
else \$mostra_date_passate = \"SI\";
unset(\$tariffe_mostra);
reset (\$".mex("var_tariffe_mostra",$pag).");
foreach (\$".mex("var_tariffe_mostra",$pag)." as \$key => \$val) {
if (strtoupper(\$val) == \"".mex("SI",$pag)."\") \$tariffe_mostra[\$key] = \"SI\";
if (strtoupper(\$val) == \"".mex("NO",$pag)."\") \$tariffe_mostra[\$key] = \"NO\";
} # fine foreach
\$n_tariffe_imposte = \$".mex("var_nomi_tariffe_imposte",$pag).";
unset(\$costi_agg_mostra);
reset (\$".mex("var_costi_aggiuntivi_mostra",$pag).");
foreach (\$".mex("var_costi_aggiuntivi_mostra",$pag)." as \$key => \$val) {
if (strtoupper(\$val) == \"".mex("SI",$pag)."\") \$costi_agg_mostra[\$key] = \"SI\";
if (strtoupper(\$val) == \"".mex("NO",$pag)."\") \$costi_agg_mostra[\$key] = \"NO\";
} # fine foreach
\$n_costi_agg_imposti = \$".mex("var_nomi_costi_agg_imposti",$pag).";
if (strtoupper(\$".mex("var_mostra_caparra",$pag).") == \"".mex("SI",$pag)."\") \$mostra_caparra = \"SI\";
else \$mostra_caparra = \"NO\";

\$apertura_tag_font = \$".mex("var_apertura_tag_font",$pag).";
\$chiusura_tag_font = \$".mex("var_chiusura_tag_font",$pag).";
\$stile_tabella_tariffe = \$".mext_rat("var_stile_tabella_tariffe",$pag).";
\$file_css_frame = \$".mex("var_file_css_frame",$pag).";
\$extra_head_frame = \"$extra_head_frame\";
\$tipo_periodi = \"$m_tipo_periodi\";

# FRASI
\$fr_Euro = \$".mex("var_fr_Valuta_sing",$pag).";
\$fr_Euros = \$".mex("var_fr_Valuta_plur",$pag).";
");
for ($num_fr = 0 ; $num_fr < $num_frasi ; $num_fr++) fwrite($file,"\$".$fr_frase[$num_fr]." = \$".mext_rat("var_".$fr_frase[$num_fr],$pag).";
");
fwrite($file,"
# PERIODI NEI MENU
\$menu_periodi = \$".mex("var_periodi_menu",$pag).";

function mex_data(\$messaggio) {
");
if ($lingua_modello != "ita") {
if (@is_file("./includes/lang/$lingua_modello/giorni_mesi.php")) includi_file("./includes/lang/$lingua_modello/giorni_mesi.php",$file);
else if (@is_file("./includes/lang/en/giorni_mesi.php")) includi_file("./includes/lang/en/giorni_mesi.php",$file);
} # fine if ($lingua_modello != "ita")
fwrite($file,"
return \$messaggio;
} # fine function mex_data

");
includi_file("./includes/funzioni_".$M_PHPR_DB_TYPE.".php",$file);
fwrite($file,"

\$numconnessione = connetti_db(\$PHPR_DB_NAME,\$PHPR_DB_HOST,\$PHPR_DB_PORT,\$PHPR_DB_USER,\$PHPR_DB_PASS,\$PHPR_LOAD_EXT);
");
includi_file(C_DATI_PATH."/versione.php",$file);
includi_file("./includes/funzioni.php",$file);
includi_file("./includes/funzioni_testo.php",$file);
includi_file("./includes/funzioni_tariffe.php",$file);
includi_file("./includes/funzioni_costi_agg.php",$file);
if (defined("C_FILE_SCADENZA_ACCOUNT") and C_FILE_SCADENZA_ACCOUNT != "") {
$f_scad_acc = explode("/",$percorso_cartella_modello);
$num_f_scad_acc = count($f_scad_acc);
$file_scad_acc = "";
for ($num1 = 0 ; $num1 < $num_f_scad_acc ; $num1++) if ($f_scad_acc[$num1] != ".") $file_scad_acc .= "../";
$file_scad_acc .= C_FILE_SCADENZA_ACCOUNT;
fwrite($file,"
\$disattivato = \"\";
\$scadenza = trim(@implode(@file(\"$file_scad_acc\")));
\$adesso = date(\"YmdHis\");
if (!\$scadenza or \$scadenza < \$adesso) {
\$disattivato = \"SI\";
echo \"Expired account.<br>\";
} # fine (!\$scadenza or \$scadenza < \$adesso)
if (!\$disattivato) {
");
} # fine if (defined("C_FILE_SCADENZA_ACCOUNT") and C_FILE_SCADENZA_ACCOUNT != "")

includi_file("./includes/templates/$template_data_dir/template.php",$file);

if (defined("C_FILE_SCADENZA_ACCOUNT") and C_FILE_SCADENZA_ACCOUNT != "") fwrite($file,"
} # fine if (!\$disattivato)
");
fwrite($file,"
if (!\$framed) {
?>


<!-- ".mex("INIZIO DELLA SECONDA PARTE DELL'HTML PERSONALE",$pag)."  -->




$ultima_parte_html
<!-- ".mex("FINE DELLA SECONDA PARTE DELL'HTML PERSONALE",$pag)."  --><?php } # fine if (!\$framed) ?>");
flock($file,3);
fclose($file);
$exec_crea_mod = substr(decoct(@fileperms('./crea_modelli.php')),-3,1);
if ((defined('C_CHMOD_EXEC_MODELLI') and C_CHMOD_EXEC_MODELLI == "SI") or $exec_crea_mod == "7" or $exec_crea_mod == "5") @chmod("$percorso_cartella_modello/$nome_file", 0750);
else @chmod("$percorso_cartella_modello/$nome_file", 0640);


$url_pagina = "";
if ($silenzio != "totale") {
$url_pagina = trova_url_pagina($nome_file,$percorso_cartella_modello,$pag);
if ($url_pagina) $url_pagina_link = $url_pagina;
else $url_pagina_link = "$percorso_cartella_modello/$nome_file";
} # fine if ($silenzio != "totale")

if ($silenzio == "NO") echo "<br>";
if ($silenzio != "totale") echo mex("Una pagina chiamata",$pag)." <b><a href=\"$url_pagina_link\" target=\"_blank\">$nome_file</a></b> ".mex("è stata creata nella directory",$pag)." \"$percorso_cartella_modello\".<br>";
if ($silenzio == "NO") {
if (defined("C_CARTELLA_CREA_MODELLI")) echo mex("Si può creare un link verso questa pagina dal proprio sito internet",$pag).".<br>";
else echo mex("Si può cambiare la directory dove vengono create le pagine da \"configura e personalizza\"",$pag).".<br>";

if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI")) {
$altri_domini = @file(C_FILE_DOMINIO);
if ($altri_domini) {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") $percorso_cartella_dominio = substr($percorso_cartella_modello,strlen(C_CARTELLA_CREA_MODELLI));
else $percorso_cartella_dominio = $percorso_cartella_modello;
if (substr($percorso_cartella_dominio,0,1) == "/") $percorso_cartella_dominio = substr($percorso_cartella_dominio,1);
if (strcmp($percorso_cartella_dominio,"") and substr($percorso_cartella_dominio,-1) != "/") $percorso_cartella_dominio .= "/";
if (substr($percorso_cartella_dominio,0,2) == "./") $percorso_cartella_dominio = substr($percorso_cartella_dominio,2);
$lista_altri_domini = "";
$num_altri_domini = count($altri_domini);
for ($num1 = 0 ; $num1 < $num_altri_domini ; $num1++) {
if ($num1 == 0) $altro_dominio = "https://";
else $altro_dominio = "http://";
$altro_dominio .= trim($altri_domini[$num1])."/$percorso_cartella_dominio$nome_file";
if ($altro_dominio != $url_pagina_link) {
$lista_altri_domini .= "<b><a href=\"$altro_dominio\" target=\"_blank\">$altro_dominio</a></b>";
if ($num1 == 0) $lista_altri_domini .= " (".mex("sicuro",$pag).")";
$lista_altri_domini .= "<br>";
} # fine if ($altro_dominio != $url_pagina_link)
} # fine for $num1
if ($lista_altri_domini) echo "<br><br>".mex("Indirizzi alternativi da cui la pagina è raggiungibile",$pag).":<br><div class=\"linhbox\">$lista_altri_domini</div>";
} # fine if ($altri_domini)
} # fine if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI"))

echo "<br>";
} # fine if ($silenzio == "NO")
} # fine if ($file)
else if ($silenzio == "NO") echo mex("Non ho il permesso di scrittura nella cartella",$pag)." $percorso_cartella_modello.<br>";

} # fine if ($continua != "NO")

} # fine function crea_modello_rat







function aggiorna_var_anno_modello_rat ($id_data_ini_periodi_prec,$tableperiodi_prec,$tableperiodi,$tabletransazioniweb,$tablemessaggi,$tipo_periodi) {

global $num_periodi_date,$LIKE,$anno,$estendi_ultima_data;
$n_num_periodi_date = 0;
if ($id_data_ini_periodi_prec) {
for ($num1 = 0 ; $num1 < $num_periodi_date ; $num1++) {
global ${"inizioperiodo".$num1},${"fineperiodo".$num1},${"intervalloperiodo".$num1};
$inizioperiodo = aggslashdb(${"inizioperiodo".$num1});
$fineperiodo = aggslashdb(${"fineperiodo".$num1});
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi_prec where datainizio = '$inizioperiodo' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo == 0) { $idinizioperiodo = 10000; }
else { $idinizioperiodo = risul_query($idinizioperiodo,0,idperiodi); }
$inizioperiodo = $idinizioperiodo;
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi_prec where datafine = '$fineperiodo' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo == 0) { $idfineperiodo = -1; }
else { $idfineperiodo = risul_query($idfineperiodo,0,idperiodi); }
$fineperiodo = $idfineperiodo;
$intervalloperiodo = aggslashdb(${"intervalloperiodo".$num1});
if ($estendi_ultima_data == "SI" and $num1 == ($num_periodi_date - 1)) $fineperiodo = $id_data_ini_periodi_prec + $intervalloperiodo;
if (($fineperiodo - $intervalloperiodo) >= $id_data_ini_periodi_prec) {
if ($inizioperiodo < $id_data_ini_periodi_prec) {
for ($num2 = $inizioperiodo ; $num2 <= $fineperiodo ; $num2 = $num2 + $intervalloperiodo) {
if ($num2 >= $id_data_ini_periodi_prec) {
$inizioperiodo = $num2;
break;
} # fine if ($num2 >= $id_data_ini_periodi_prec)
} # fine for $num2
} # fine if ($inizioperiodo < $id_data_ini_periodi_prec)
$n_inizioperiodo[$n_num_periodi_date] = $inizioperiodo - $id_data_ini_periodi_prec + 1;
$n_fineperiodo[$n_num_periodi_date] = $fineperiodo - $id_data_ini_periodi_prec + 1;
$n_intervalloperiodo[$n_num_periodi_date] = $intervalloperiodo;
$n_num_periodi_date++;
} # fine if (($fineperiodo - $intervalloperiodo) >= $id_data_ini_periodi_prec or...
} # fine for $num1
} # fine if ($id_data_ini_periodi_prec)

if ($estendi_ultima_data == "SI" and !$id_data_ini_periodi_prec) {
$n_num_periodi_date = 1;
global $inizioperiodo0,$fineperiodo0,$intervalloperiodo0;
if (($num_periodi_date - 1) != 0) global ${"fineperiodo".($num_periodi_date - 1)},${"intervalloperiodo".($num_periodi_date - 1)};
$n_intervalloperiodo[0] = ${"intervalloperiodo".($num_periodi_date - 1)};
$inizioperiodo = ${"fineperiodo".($num_periodi_date - 1)};
if ($tipo_periodi == "g") $aggiungi_giorni = 1;
else $aggiungi_giorni = 7;
$anno_inizio = substr($inizioperiodo,0,4);
$mese_inizio = substr($inizioperiodo,5,2);
$giorno_inizio = substr($inizioperiodo,8,2);
for ($num1 = 0 ; $num1 < 2000 ; $num1++) {
$datainizio = date("Y-m-d",mktime(0,0,0,$mese_inizio,$giorno_inizio,$anno_inizio));
$datainizio = esegui_query("select * from $tableperiodi where datainizio = '$datainizio'");
if (numlin_query($datainizio) == 1) {
$n_inizioperiodo[0] = risul_query($datainizio,0,idperiodi);
break;
} # fine if (numlin_query($datainizio) == 1)
$giorno_inizio = $giorno_inizio + ($n_intervalloperiodo[0] * $aggiungi_giorni);
} # fine for $num1
$n_fineperiodo[0] = $n_inizioperiodo[0];
} # fine if ($estendi_ultima_data == "SI" and !$id_data_ini_periodi_prec)

for ($num1 = 0 ; $num1 < $n_num_periodi_date ; $num1++) {
$inizioperiodo = $n_inizioperiodo[$num1];
$fineperiodo = $n_fineperiodo[$num1];
$inizioperiodo = esegui_query("select datainizio from $tableperiodi where idperiodi = '$inizioperiodo' ");
$inizioperiodo = @risul_query($inizioperiodo,0,datainizio);
$fineperiodo = esegui_query("select datafine from $tableperiodi where idperiodi = '$fineperiodo' ");
$fineperiodo = @risul_query($fineperiodo,0,datafine);
if (!$inizioperiodo or !$fineperiodo) $n_num_periodi_date = 0;
${"inizioperiodo".$num1} = $inizioperiodo;
${"fineperiodo".$num1} = $fineperiodo;
${"intervalloperiodo".$num1} = $n_intervalloperiodo[$num1];
} # fine for $num1
$num_periodi_date = $n_num_periodi_date;
if (!$num_periodi_date) $inizioperiodo0 = "";

} # fine funtcion aggiorna_var_anno_modello_rat







?>