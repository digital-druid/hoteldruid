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

$pag = "modifica_contratto.php";
$titolo = "HotelDruid: Modifica Documento";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
include("./includes/funzioni_costi_agg.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tableutenti = $PHPR_TAB_PRE."utenti";


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

if ($anno_utente_attivato == "SI" and $priv_mod_doc == "s" and $modifica_pers != "NO" and ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[(integer) $num_contratto] == "SI")) {

if ($priv_mod_doc_api != "s") {
$api_esistente = esegui_query("select * from $tablecontratti where numero = '".aggslashdb($num_contratto)."' and tipo = 'api'");
if (numlin_query($api_esistente)) $priv_mod_doc = "n";
} # fine if ($priv_mod_doc_api != "s")
if ($priv_mod_doc == "s") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();

if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
if (C_CARTELLA_DOC != "" and @is_dir(C_CARTELLA_CREA_MODELLI."/".C_CARTELLA_DOC)) $dir_salva_home = C_CARTELLA_DOC;
else $dir_salva_home = "";
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else $dir_salva_home = C_DATI_PATH;


if (controlla_num_pos($num_contratto) == "NO") $num_contratto = "1";
if ($num_contratto < 1 or controlla_num_pos($num_contratto) == "NO") $num_contratto = "1";
if (strlen($num_contratto) == 1) $num_contratto = "0".$num_contratto;
$num_contratto_int = (integer) $num_contratto;




if ($cambia_qualcosa) {
$anchor = "";


function formatta_input_var_x_file ($input_utente) {
if (@get_magic_quotes_gpc()) $input_utente = stripslashes($input_utente);
$input_utente = str_replace("\\","\\\\",$input_utente);
$input_utente = str_replace("\"","\\\"",$input_utente);
return $input_utente;
} # fine function formatta_input_var_x_file

if ($cambia_formato) {
$formato = "";
if ($nuovo_formato == "HTML") $formato = "contrhtm";
if ($nuovo_formato == "EMAIL") $formato = "contreml";
if ($nuovo_formato == "RTF") $formato = "contrrtf";
if ($nuovo_formato == "TXT") $formato = "contrtxt";
if ($multi_lingua) $multi_lingua = 1;
else $multi_lingua = 0;
if ($formato) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$vecchio_formato = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%' ");
if (numlin_query($vecchio_formato) == 1) {
$mostra_form_iniziale = "NO";
$vecchio_multi_lingua = 0;
if (substr(risul_query($vecchio_formato,0,'testo'),0,7) == "#!mln!#") $vecchio_multi_lingua = 1;
else $vecchio_multi_lingua = 0;
$vecchio_formato = risul_query($vecchio_formato,0,'tipo');
if ($vecchio_formato != $formato) {
if ($vecchio_formato == "contreml") {
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'oggetto' ");
$num_all = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'allegato'");
$num_all = risul_query($num_all,0,'testo');
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'allegato'");
if ($num_all) {
$altri_all = esegui_query("select * from $tablecontratti where testo = '$num_all' and tipo = 'allegato'");
if (!numlin_query($altri_all)) {
esegui_query("delete from $tablecontratti where numero = '$num_all' and tipo = 'file_all' ");
} # fine if (!numlin_query($altri_all))
echo "".mex("Allegato eliminato",$pag).".<br>";
} # fine if ($num_all)
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'opzeml'");
} # fine if ($vecchio_formato == "contreml")
if ($vecchio_formato == "contrtxt") esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'est_txt' ");
if ($vecchio_formato == "contrhtm") esegui_query("delete from $tablecontratti where numero = '$num_contratto' and (tipo = 'headhtm' or tipo = 'foothtm') ");
if ($formato == "contreml") {
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','oggetto','') ");
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','allegato','') ");
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','opzeml',';;') ");
} # fine if ($formato == "contreml")
esegui_query("update $tablecontratti set tipo = '$formato' where numero = '$num_contratto' and tipo $LIKE 'contr%'");
} # fine if ($vecchio_formato != $formato)
if ($vecchio_multi_lingua != $multi_lingua) {
if ($vecchio_multi_lingua) {
$lingua_def = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$lingua_def = substr(risul_query($lingua_def,0,'testo'),7);
$testo_contr = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'mln_$lingua_def' ");
if (numlin_query($testo_contr)) $testo_contr = risul_query($testo_contr,0,'testo');
else $testo_contr = "";
esegui_query("update $tablecontratti set testo = '".aggslashdb($testo_contr)."' where numero = '$num_contratto' and tipo $LIKE 'contr%' ");
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'mln_%' ");
} # fine if ($vecchio_multi_lingua)
if ($multi_lingua) {
$testo_contr = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$testo_contr = risul_query($testo_contr,0,'testo');
$testo_contr = str_replace("[r]","",str_replace("[/r]","",$testo_contr));
if (str_replace("[r]","",$testo_contr) != $testo_contr or str_replace("[/r]","",$testo_contr) != $testo_contr) $testo_contr = "";
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','mln_$lingua_mex','".aggslashdb($testo_contr)."') ");
esegui_query("update $tablecontratti set testo = '#!mln!#$lingua_mex' where numero = '$num_contratto' and tipo $LIKE 'contr%' ");
echo "<b class=\"colblu\">".mex("Avviso",$pag)."</b>: ".mex("nei documenti in formato multi-lingua non è possibile ripetere una sola parte con i tag [r] e [/r], il documento viene ripetuto interamente per ogni prenotazione",$pag).".<br><br>";
} # fine if ($multi_lingua)
} # fine if ($vecchio_multi_lingua != $multi_lingua)
echo "".mex("Formato del documento cambiato",$pag).".<br>";
} # fine if (numlin_query($vecchio_formato) == 1)
unlock_tabelle($tabelle_lock);
} # fine if ($formato)
} # fine if ($cambia_formato)

if ($cambia_dir_salva) {
if (@get_magic_quotes_gpc()) $nuova_dir_salva = stripslashes($nuova_dir_salva);
$nuova_dir_salva = htmlspecialchars($nuova_dir_salva);
$nuova_dir_salva = aggslashdb($nuova_dir_salva);
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contrrtf" or $tipo_contratto == "contrhtm" or $tipo_contratto == "contrtxt") {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
$nuova_dir_salva = str_replace("..","",$nuova_dir_salva);
$dir_salva = C_CARTELLA_CREA_MODELLI."/$nuova_dir_salva";
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else $dir_salva = $nuova_dir_salva;
if ($dir_salva) {
if (substr($nuova_dir_salva,-1) == "/") $nuova_dir_salva = substr($nuova_dir_salva,0,-1);
$mostra_form_iniziale = "NO";
if (!@is_dir($dir_salva)) echo mex("La cartella inserita <div style=\"display: inline; color: red;\">non esiste</div>","personalizza.php").".<br>";
else {
$fileaperto = @fopen("$dir_salva/prova.tmp","a+");
if (!$fileaperto) echo mex("Non ho i permessi di scrittura sulla cartella","personalizza.php")." <div style=\"display: inline; color: red;\">$dir_salva/</div>.<br>";
else {
fclose($fileaperto);
unlink("$dir_salva/prova.tmp");
if ($salva_contr == "SI") {
if ($nuova_dir_salva == $dir_salva_home) $nuova_dir_salva = "~";
$dir_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_esistente) >= 1) esegui_query("update $tablecontratti set testo = '$nuova_dir_salva' where numero = '$num_contratto' and tipo = 'dir'");
else {
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','dir','$nuova_dir_salva')");
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','compress','gz')");
} # fine else if (numlin_query($dir_esistente) >= 1)
echo "".mex("I documenti verranno salvati nella cartella",$pag)." $dir_salva.<br>";
} # fine if ($salva_contr == "SI")
else {
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and (tipo = 'dir' or tipo = 'num_prog' or tipo = 'nomefile' or tipo = 'compress' or tipo = 'autosalv' or tipo = 'incr_np') ");
esegui_query("delete from $tablecontratti where testo = '$num_contratto_int' and tipo = 'num_prog' ");
echo "".mex("I documenti non verranno salvati",$pag).".<br>";
} # fine else if ($salva_contr == "SI")
} # fine else if (!$fileaperto)
} # fine else if (!@is_dir($nuova_dir_salva))
} # fine if ($dir_salva)
} # fine if ($tipo_contratto == "contrrtf" or..
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_dir_salva)

if ($cambia_compress) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dir_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_esistente)) {
if (!$compress) {
$mostra_form_iniziale = "NO";
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'compress' ");
echo "".mex("Il documento non verrà salvato compresso su disco",$pag).".<br>";
} # fine if (!$compress)
else {
$mostra_form_iniziale = "NO";
$compress_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'compress' ");
if (numlin_query($compress_esistente)) esegui_query("update $tablecontratti set testo = 'gz' where numero = '$num_contratto' and tipo = 'compress' ");
else esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','compress','gz') ");
echo "".mex("Il documento verrà salvato compresso su disco",$pag).".<br>";
} # fine else if (!$compress)
} # fine if (numlin_query($dir_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_compress)

if ($cambia_autosalv) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dir_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_esistente)) {
if (!$n_autosalv) {
$mostra_form_iniziale = "NO";
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'autosalv' ");
echo "".mex("Il documento non verrà salvato automaticamente",$pag).".<br>";
} # fine if (!$n_autosalv)
else {
if ($n_autosalv == "checkin" or $n_autosalv == "checkout") {
$mostra_form_iniziale = "NO";
$autosalv_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'autosalv' ");
if (numlin_query($autosalv_esistente)) esegui_query("update $tablecontratti set testo = '$n_autosalv' where numero = '$num_contratto' and tipo = 'autosalv' ");
else esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','autosalv','$n_autosalv') ");
echo "".mex("Il documento verrà salvato automaticamente",$pag)." ";
if ($n_autosalv == "checkin") echo mex("alla registrazione dell'entrata",$pag);
if ($n_autosalv == "checkout") echo mex("alla registrazione dell'uscita",$pag);
echo ".<br>";
} # fine if ($n_autosalv == "checkin" or $n_autosalv == "checkout")
} # fine else if (!$n_autosalv)
} # fine if (numlin_query($dir_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_autosalv)

if ($cambia_incr_np) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dir_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_esistente)) {
$mostra_form_iniziale = "NO";
if (!$incr_np) {
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'incr_np' ");
echo "".mex("Ogni documento avrà un unico numero progressivo",$pag).".<br>";
} # fine if (!$incr_np)
else {
$incr_np_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'incr_np' ");
if (numlin_query($incr_np_esistente)) esegui_query("update $tablecontratti set testo = '1' where numero = '$num_contratto' and tipo = 'incr_np' ");
else esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','incr_np','1') ");
echo "".mex("Il numero progressivo verrà incrementato ogni volta che compare nel documento",$pag).".<br>";
} # fine else if (!$incr_np)
} # fine if (numlin_query($dir_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_incr_np)

if ($cambia_num_prog) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dir_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_esistente)) {
if (!strcmp($contr_num_prog,"")) {
$mostra_form_iniziale = "NO";
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'num_prog' ");
echo ucfirst(mex("il documento",$pag))." $num_contratto ".mex("non condividerà il numero progressivo con nessun documento",$pag).".<br>";
} # fine if (!strcmp($contr_num_prog,""))
else {
if ($contr_num_prog >= 1 and controlla_num_pos($contr_num_prog) != "NO") {
if ($contr_num_prog != $num_contratto_int and ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$contr_num_prog] == "SI")) {
$dir_esistente = esegui_query("select * from $tablecontratti where numero = '$contr_num_prog' and tipo = 'dir'");
if (numlin_query($dir_esistente)) {
$num_prog_esistente = esegui_query("select * from $tablecontratti where numero = '$contr_num_prog' and tipo = 'num_prog' ");
if (!numlin_query($num_prog_esistente)) {
$mostra_form_iniziale = "NO";
$num_prog_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'num_prog' ");
if (numlin_query($num_prog_esistente)) esegui_query("update $tablecontratti set testo = '$contr_num_prog' where numero = '$num_contratto' and tipo = 'num_prog' ");
else {
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','num_prog','$contr_num_prog') ");
esegui_query("update $tablecontratti set testo = '$contr_num_prog' where testo = '$num_contratto_int' and tipo = 'num_prog' ");
} # fine else if (numlin_query($num_prog_esistente))
echo ucfirst(mex("il documento",$pag))." $num_contratto ".mex("condividerà il numero progressivo con il documento",$pag)." $contr_num_prog.<br>";
} # fine if (!numlin_query($num_prog_esistente))
} # fine if (numlin_query($dir_esistente))
} # fine if ($contr_num_prog != $num_contratto_int and...
} # fine if ($contr_num_prog >= 1 and controlla_num_pos($contr_num_prog) != "NO")
} # fine else if (!strcmp($contr_num_prog,""))
} # fine if (numlin_query($dir_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_num_prog)

if ($cambia_nome_file) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$dir_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_esistente)) {
if ($tipo_nome_file != "pers") {
$mostra_form_iniziale = "NO";
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'nomefile' ");
echo "".mex("I file verranno salvati con il nome del documento",$pag).".<br>";
} # fine if ($tipo_nome_file != "pers")
else {
if (strcmp($nome_file_salva,"")) {
$mostra_form_iniziale = "NO";
$nome_file_salva = htmlspecialchars($nome_file_salva);
$nomefile_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'nomefile' ");
if (numlin_query($nomefile_esistente)) esegui_query("update $tablecontratti set testo = '".aggslashdb($nome_file_salva)."' where numero = '$num_contratto' and tipo = 'nomefile' ");
else esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','nomefile','".aggslashdb($nome_file_salva)."') ");
echo "".mex("I file verranno salvati con il nome",$pag)." \"$nome_file_salva\".<br>";
} # fine if (strcmp($nome_file_salva,""))
} # fine else if ($tipo_nome_file != "pers")
} # fine if (numlin_query($dir_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_nome_file)

if ($priv_mod_doc_api == "s" and ($cambia_cons_api or strcmp($pass_api,"") or $id_utente_api)) {
$tabelle_lock = array($tablecontratti);
$altre_tab_lock = array($tableutenti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contrrtf" or $tipo_contratto == "contrhtm" or $tipo_contratto == "contrtxt") {
$api_esistente = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'api' ");
if (strcmp($pass_api,"") or $id_utente_api) {
if (numlin_query($api_esistente)) {
$pass_api_esist = risul_query($api_esistente,0,'testo');
$id_utente_api_esist = explode(";",$pass_api_esist);
$id_utente_api_esist = $id_utente_api_esist[0];
$pass_api_esist = substr($pass_api_esist,(strlen($id_utente_api_esist) + 1));
if (strcmp($pass_api,"")) {
$mostra_form_iniziale = "NO";
if (@get_magic_quotes_gpc()) $pass_api = stripslashes($pass_api);
$pass_api = aggslashdb($pass_api);
esegui_query("update $tablecontratti set testo = '$id_utente_api_esist;$pass_api' where numero = '$num_contratto' and tipo = 'api' ");
echo mex("La password per la API del documento è stata cambiata",$pag).".<br>";
} # fine if (strcmp($pass_api,""))
if ($id_utente_api and ($id_utente == 1 or $id_utente_api == $id_utente)) {
$utente_esistente = esegui_query("select nome_utente from $tableutenti where idutenti = '".aggslashdb($id_utente_api)."' ");
if (numlin_query($utente_esistente)) {
$mostra_form_iniziale = "NO";
esegui_query("update $tablecontratti set testo = '$id_utente_api;$pass_api_esist' where numero = '$num_contratto' and tipo = 'api' ");
echo mex("L'utente per la API del documento è stato cambiato",$pag).".<br>";
} # fine if (numlin_query($utente_esistente))
} # fine if ($id_utente_api and ($id_utente == 1 or $id_utente_api == $id_utente))
} # fine if (numlin_query($api_esistente))
} # fine if (strcmp($pass_api,"") or $id_utente_api)
else {
if ($cons_api == "SI") {
if (!numlin_query($api_esistente)) {
$mostra_form_iniziale = "NO";
$valori = "23456789ABCDEFGHJKLMNPQRSTUVWXZabcdefghijkmnpqrstuvwxyz";
srand((double)microtime() * 1000000);
unset($pass_generata);
for ($num1 = 0 ; $num1 < 8 ; $num1++) $pass_generata .= substr($valori,rand(0,54),1);
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','api','1;$pass_generata')");
echo "".mex("Il documento verrà considerato come una API",$pag)." $dir_salva.<br>";
} # fine if (!numlin_query($api_esistente))
} # fine if ($cons_api == "SI")
else {
if (numlin_query($api_esistente)) {
$mostra_form_iniziale = "NO";
esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'api' ");
echo mex("Il documento non verrà più considerato come una API",$pag).".<br>";
} # fine if (numlin_query($api_esistente))
} # fine else if ($cons_api == "SI")
} # fine else if (strcmp($pass_api,"") or $id_utente_api)
} # fine if ($tipo_contratto == "contrrtf" or..
unlock_tabelle($tabelle_lock);
} # fine if ($priv_mod_doc_api == "s" and ($cambia_cons_api or strcmp($pass_api,"") or $id_utente_api))

if ($cambia_intestazione_pers) {
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contrhtm") {
$intestazione_html = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'headhtm'");
if (numlin_query($intestazione_html) == 1) $intestazione_html = 1;
else $intestazione_html = 0;
if (($intestazione_html and !$intestazione_pers) or (!$intestazione_html and $intestazione_pers)) {
$mostra_form_iniziale = "NO";
if ($intestazione_html) esegui_query("delete from $tablecontratti where numero = '$num_contratto' and (tipo = 'headhtm' or tipo = 'foothtm') ");
else {
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','headhtm','<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" >\n<title>".mex("documento",$pag)." $num_contratto</title>\n</head>\n<body style=\"background-color: #ffffff;\">\n') ");
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','foothtm','</body>\n</html>\n') ");
} # fine else if ($intestazione_html)
echo "".mex("Intestazione html cambiata",$pag).".<br>";
} # fine if (($intestazione_html and !$intestazione_pers) or...
} # fine if ($tipo_contratto == "contrhtm")
} # fine if ($cambia_intestazione_pers)

if ($cambia_estenstione_pers) {
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contrtxt") {
if (@get_magic_quotes_gpc()) $estensione_pers_txt = stripslashes($estensione_pers_txt);
$estensione_pers_txt = strtolower($estensione_pers_txt);
if (preg_replace("/[a-z]/","",$estensione_pers_txt) != "" or strlen($estensione_pers_txt) > 10 or strlen($estensione_pers_txt) < 2) $estensione_pers_txt = "";
$estensione_txt = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'est_txt'");
if (numlin_query($estensione_txt) == 1) $estensione_txt = risul_query($estensione_txt,0,'testo');
else $estensione_txt = "";
if (($tipo_estensione_txt == "pers" and $estensione_pers_txt and $estensione_txt != $estensione_pers_txt) or ($tipo_estensione_txt == "predef" and $estensione_txt)) {
$mostra_form_iniziale = "NO";
if ($tipo_estensione_txt == "predef") esegui_query("delete from $tablecontratti where numero = '$num_contratto' and tipo = 'est_txt' ");
else {
if (!$estensione_txt) esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','est_txt','$estensione_pers_txt') ");
else esegui_query("update $tablecontratti set testo = '$estensione_pers_txt' where numero = '$num_contratto' and tipo = 'est_txt'");
} # fine else if ($tipo_estensione_txt == "predef")
echo "".mex("Estensione cambiata",$pag).".<br>";
} # fine if (($tipo_estensione_txt == "pers" and $estensione_pers_txt and...
} # fine if ($tipo_contratto == "contrtxt")
} # fine if ($cambia_estenstione_pers)

if ($cambia_oggetto) {
if (@get_magic_quotes_gpc()) $nuovo_oggetto = stripslashes($nuovo_oggetto);
if (strlen($nuovo_oggetto) > 120) $nuovo_oggetto = substr($nuovo_oggetto,0,120);
$nuovo_oggetto = htmlspecialchars($nuovo_oggetto);
$nuovo_oggetto = aggslashdb($nuovo_oggetto);
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contreml") {
$mostra_form_iniziale = "NO";
esegui_query("update $tablecontratti set testo = '$nuovo_oggetto' where numero = '$num_contratto' and tipo = 'oggetto'");
echo "".mex("Oggetto cambiato",$pag).".<br>";
} # fine if ($tipo_contratto == "contreml")
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_oggetto)

if ($upload_allegato) {
$errore = "NO";
$file_tmp = C_DATI_PATH."/allegato_contr$num_contratto.tmp";
if (!$file_allegato_upload) {
if ($HTTP_POST_FILES['file_allegato_upload']['tmp_name']) {
$file_allegato_upload = $HTTP_POST_FILES['file_allegato_upload']['tmp_name'];
$file_allegato_upload_name = $HTTP_POST_FILES['file_allegato_upload']['name'];
$file_allegato_upload_type = $HTTP_POST_FILES['file_allegato_upload']['type'];
} # fine if ($HTTP_POST_FILES['file_allegato_upload']['tmp_name'])
else {
if ($_FILES['file_allegato_upload']['tmp_name']) {
$file_allegato_upload = $_FILES['file_allegato_upload']['tmp_name'];
$file_allegato_upload_name = $_FILES['file_allegato_upload']['name'];
$file_allegato_upload_type = $_FILES['file_allegato_upload']['type'];
} # fine if ($_FILES['file_allegato_upload']['tmp_name'])
} # fine else if ($HTTP_POST_FILES['file_allegato_upload']['tmp_name'])
} # fine if (!$file_allegato_upload)
if (!move_uploaded_file($file_allegato_upload,$file_tmp)) $errore = "SI";
if ($errore == "NO") {
if (!defined("C_MASSIMO_NUM_BYTE_UPLOAD") or C_MASSIMO_NUM_BYTE_UPLOAD == 0 or filesize($file_tmp) <= C_MASSIMO_NUM_BYTE_UPLOAD) {
$allegato = "";
$f_allegato = fopen($file_tmp, 'r');
while (true) {
$dati = fread($f_allegato, 8192);
if (strlen($dati) == 0) break;
$allegato .= $dati;
} # fine while (true)
fclose($f_allegato);
unlink($file_tmp);
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contreml") {
$mostra_form_iniziale = "NO";
$allegato = chunk_split(base64_encode($allegato));
if (!$file_allegato_upload_type) $file_allegato_upload_type = "application/unknown";
if (!$file_allegato_upload_name) $file_allegato_upload_name = mex("Allegato",$pag);
$allegato = htmlspecialchars($file_allegato_upload_name).",".$file_allegato_upload_type.",".$allegato;
$max_fa = esegui_query("select max(numero) from $tablecontratti where tipo = 'file_all' ");
if (numlin_query($max_fa)) $max_fa = risul_query($max_fa,0,0) + 1;
else $max_fa = 1;
esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$max_fa','file_all','".aggslashdb($allegato)."')");
esegui_query("update $tablecontratti set testo = '$max_fa' where numero = '$num_contratto' and tipo = 'allegato'");
echo "".mex("Allegato salvato",$pag).".<br>";
} # fine if ($tipo_contratto == "contreml")
unlock_tabelle($tabelle_lock);
} # fine if (!defined("C_MASSIMO_NUM_BYTE_UPLOAD") or...
else unlink($file_tmp);
} # fine if ($errore == "NO")
} # fine if ($upload_allegato)

if ($aggiungi_allegato) {
$all_esistente = @esegui_query("select * from $tablecontratti where numero = '".aggslashdb($num_allegato)."' and tipo = 'file_all' ");
if (numlin_query($all_esistente)) {
$mostra_form_iniziale = "NO";
esegui_query("update $tablecontratti set testo = '$num_allegato' where numero = '$num_contratto' and tipo = 'allegato'");
echo "".mex("Allegato salvato",$pag).".<br>";
} # fine if(numlin_query($all_esistente))
} # fine if ($aggiungi_allegato)

if ($elimina_allegato) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$tipo_contratto = esegui_query("select tipo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($tipo_contratto,0,'tipo');
if ($tipo_contratto == "contreml") {
$mostra_form_iniziale = "NO";
$num_all = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'allegato'");
$num_all = risul_query($num_all,0,'testo');
if ($num_all) {
esegui_query("update $tablecontratti set testo = '' where numero = '$num_contratto' and tipo = 'allegato'");
$altri_all = esegui_query("select * from $tablecontratti where testo = '$num_all' and tipo = 'allegato'");
if (!numlin_query($altri_all)) {
esegui_query("delete from $tablecontratti where numero = '$num_all' and tipo = 'file_all' ");
} # fine if (!numlin_query($altri_all))
echo "".mex("Allegato eliminato",$pag).".<br>";
} # fine if ($num_all)
} # fine if ($tipo_contratto == "contreml")
unlock_tabelle($tabelle_lock);
} # fine if ($elimina_allegato)

if ($cambia_formato_email or $cambia_bcc) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$contratto = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($contratto,0,'tipo');
if ($tipo_contratto == "contreml") {
$mostra_form_iniziale = "NO";
$opz_eml = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'opzeml'");
$opz_eml = explode(";",risul_query($opz_eml,0,'testo'));
if ($cambia_formato_email) {
if ($formato_email == "html") {
$n_opz = "html;";
if ($opz_eml[0] != "html") {
$testo_eml = risul_query($contratto,0,'testo');
if (!strcmp(trim($testo_eml),"")) esegui_query("update $tablecontratti set testo = '<html><body>\n\n\n\n</body></html>' where numero = '$num_contratto' and tipo = 'contreml' ");
} # fine if ($opz_eml[0] != "html")
} # fine if ($formato_email == "html")
else $n_opz = ";";
$n_opz .= $opz_eml[1].";".$opz_eml[2];
esegui_query("update $tablecontratti set testo = '$n_opz' where numero = '$num_contratto' and tipo = 'opzeml' ");
echo "".mex("Formato dell'email cambiato",$pag).".<br>";
} # fine if ($cambia_formato_email)
if ($cambia_bcc) {
$n_opz = $opz_eml[0].";";
if ($bcc_mittente == "SI") $n_opz .= "SI";
$n_opz .= ";";
if ($bcc_indirizzo and preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/i',$email_bcc)) $n_opz .= "$email_bcc";
esegui_query("update $tablecontratti set testo = '$n_opz' where numero = '$num_contratto' and tipo = 'opzeml' ");
echo "".mex("Copie bcc da spedire cambiate",$pag).".<br>";
} # fine if ($cambia_bcc)
} # fine if ($tipo_contratto == "contreml")
unlock_tabelle($tabelle_lock);
} # fine if ($cambia_formato_email or $cambia_bcc)

if ($lingua_predef) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$testo_contr = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
$testo_contr = risul_query($testo_contr,0,'testo');
if (substr($testo_contr,0,7) == "#!mln!#") {
if ($lingua_contr == "ita" or (preg_replace("/[a-z]{2,3}/","",$lingua_contr) == "" and @is_file("./includes/lang/$lingua_contr/l_n"))) {
esegui_query("update $tablecontratti set testo = '#!mln!#$lingua_contr' where numero = '$num_contratto' and tipo $LIKE 'contr%'");
} # fine if ($lingua_contr == "ita" or...
} # fine if (substr($testo_contr,0,7) == "#!mln!#")
unlock_tabelle($tabelle_lock);
} # fine if ($lingua_predef)

if ($salva_head) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$vecchio_head = esegui_query("select numero from $tablecontratti where numero = '$num_contratto' and tipo = 'headhtm' ");
if (numlin_query($vecchio_head)) {
$mostra_form_iniziale = "NO";
if (@get_magic_quotes_gpc()) $n_head = stripslashes($n_head);
$n_head = aggslashdb($n_head);
esegui_query("update $tablecontratti set testo = '$n_head' where numero = '$num_contratto' and tipo = 'headhtm' ");
echo "".mex("Intestazione html cambiata",$pag).".<br>";
} # fine if (numlin_query($vecchio_head))
unlock_tabelle($tabelle_lock);
} # fine if ($salva_head)

if ($salva_modifiche) {
$mostra_form_iniziale = "NO";
$errore = "NO";
if (@get_magic_quotes_gpc()) $n_contratto = stripslashes($n_contratto);
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$vecchio_contr = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
if (numlin_query($vecchio_contr)) {
$vecchio_contr = risul_query($vecchio_contr,0,'testo');
if (substr($vecchio_contr,0,7) == "#!mln!#") {
$multilingua = 1;
if ($lingua_contr != "ita" and (preg_replace("/[a-z]{2,3}/","",$lingua_contr) != "" or !@is_file("./includes/lang/$lingua_contr/l_n"))) $errore = "SI";
$n_contratto = str_replace("[r]","",str_replace("[/r]","",$n_contratto));
if (str_replace("[r]","",$n_contratto) != $n_contratto or str_replace("[/r]","",$n_contratto) != $n_contratto) $errore = "SI";
$anchor = "#contr_txtbox$lingua_contr";
} # fine if (substr($vecchio_contr,0,7) == "#!mln!#")
else $multilingua = 0;
if (C_RESTRIZIONI_DEMO_ADMIN == "SI") $n_contratto = htmlspecialchars($n_contratto);
$contr_controlla_int = str_replace("[r4]","[r4\\]",$n_contratto);
$contr_controlla_int = preg_replace("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/","[r4]",$contr_controlla_int);
$contr_controlla = explode("[",$contr_controlla_int);
$num_contr_controlla = count($contr_controlla);
$livello = 0;
unset($tipo_livello);
if (str_replace("[r]","",$contr_controlla_int) == $contr_controlla_int) $tipo_livello[0] = "r";
for ($num1 = 1 ; $num1 < $num_contr_controlla ; $num1++) {
$parte = $contr_controlla[$num1];
$apertura = "";
$chiusura = "";
if (substr($parte,0,2) == "r]") $apertura = "r";
if (substr($parte,0,3) == "r2]") $apertura = "r2";
if (substr($parte,0,3) == "r3]") $apertura = "r3";
if (substr($parte,0,3) == "r4]") $apertura = "r4";
if (substr($parte,0,3) == "r5]") $apertura = "r5";
if (substr($parte,0,3) == "r6]") $apertura = "r6";
if (substr($parte,0,3) == "/r]") $chiusura = "r";
if (substr($parte,0,4) == "/r2]") $chiusura = "r2";
if (substr($parte,0,4) == "/r3]") $chiusura = "r3";
if (substr($parte,0,4) == "/r4]") $chiusura = "r4";
if (substr($parte,0,4) == "/r5]") $chiusura = "r5";
if (substr($parte,0,4) == "/r6]") $chiusura = "r6";
if ($apertura) {
if ($apertura == "r" and $livello != 0 and ($livello != 1 or ($tipo_livello[1] != "r4" and $tipo_livello[1] != "r6") or $tipo_livello[0] == "r")) $errore = "SI";
if (($apertura == "r2" or $apertura == "r3" or $apertura == "r5") and $tipo_livello[$livello] != "r") $errore = "SI";
if (($apertura == "r4" or $apertura == "r6") and $tipo_livello[$livello] != "r" and $livello != 0 and ($livello != 1 or ($tipo_livello[1] != "r4" and $tipo_livello[1] != "r6") or $tipo_livello[0] == "r")) $errore = "SI";
if ($apertura == "r6" and $tipo_livello[1] == "r6") $errore = "SI";
$livello++;
$tipo_livello[$livello] = $apertura;
} # fine ($apertura)
if ($chiusura) {
if ($tipo_livello[$livello] != $chiusura) $errore = "SI";
$tipo_livello[$livello] = "";
$livello--;
if ($livello < 0) $errore = "SI";
} # fine if ($chiusura)
if ($errore == "SI") break;
} # fine for $num1
if ($livello != 0) $errore = "SI";
$contr_controlla_int = str_replace("[c]","[c\\]",$contr_controlla_int);
$contr_controlla_int = preg_replace("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/","[c]",$contr_controlla_int);
$contr_controlla = explode("[c]",$contr_controlla_int);
$num_contr_controlla = count($contr_controlla);
for ($num1 = 1 ; $num1 < $num_contr_controlla ; $num1++) {
$parte = explode("[/c]",$contr_controlla[$num1]);
if (count($parte) != 2) $errore = "SI";
if (preg_replace("|\\[/?r[1234]\\]|","",$parte[0]) != $parte[0]) $errore = "SI";
} # fine for $num1
if ($errore == "SI") echo "<span style=\"color: red;\">".mex("Errore nelle ripetizioni e condizioni annidate",$pag)."</span>.<br>";
else {
$n_contratto = aggslashdb($n_contratto);
if (!$multilingua) esegui_query("update $tablecontratti set testo = '$n_contratto' where numero = '$num_contratto' and tipo $LIKE 'contr%'");
else {
$lingua_esist = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'mln_".aggslashdb($lingua_contr)."' ");
if (numlin_query($lingua_esist)) esegui_query("update $tablecontratti set testo = '$n_contratto' where numero = '$num_contratto' and tipo = 'mln_".aggslashdb($lingua_contr)."' ");
else esegui_query("insert into $tablecontratti (numero,tipo,testo) values ('$num_contratto','mln_".aggslashdb($lingua_contr)."','$n_contratto') ");
} # fine else if (!$multilingua)
echo "".mex("Documento salvato",$pag).".<br>";
} # fine else if ($errore == "SI")
} # fine if (numlin_query($vecchio_contr))
unlock_tabelle($tabelle_lock);
} # fine if ($salva_modifiche)

if ($aggiungi_var or $aggiungi_var2) {
if ($aggiungi_var2) $var_agg = $var_agg2;
if ($var_agg and preg_replace("/[A-Za-z0-9\(\)_]/","",$var_agg) == "") {
if (@get_magic_quotes_gpc()) $n_contratto = stripslashes($n_contratto);
if (!strcmp($pos_curs,"")) $testo_contratto_mod = $n_contratto."[".$var_agg."]";
else {

$pos_curs = strlen(utf8_encode(substr(utf8_decode($n_contratto),0,$pos_curs)));
$pos_curs += (2 * substr_count(substr($n_contratto,0,$pos_curs),"€"));
$pos_curs += (2 * substr_count(substr($n_contratto,0,$pos_curs),"–"));
$testo_contratto_mod = substr($n_contratto,0,$pos_curs)."[".$var_agg."]".substr($n_contratto,$pos_curs);
} # fine else if (!strcmp($pos_curs,""))
} # fine if ($var_agg and preg_replace("/[A-Za-z0-9\(\)_]/","",$var_agg) == "")
} # fine if ($aggiungi_var or $aggiungi_var2)

if ($aggiungi_ripetizione or $aggiungi_ripetizione2) {
if (@get_magic_quotes_gpc()) $n_contratto = stripslashes($n_contratto);
if ($aggiungi_ripetizione2) $tipo_rip = $tipo_rip2;
$testo_agg1 = "";
$testo_agg2 = "";
$testo_agg3 = "";
if ($tipo_rip == 1) {
$testo_agg1 = "[r]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON PARTE DA RIPETERE PER OGNI PRENOTAZIONE",$pag)."\n";
$testo_agg3 = "[/r]";
} # fine if ($tipo_rip == 1)
if ($tipo_rip == 2) {
$testo_agg1 = "[r2]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON LISTA OSPITI IN PRENOTAZIONE",$pag)."\n";
$testo_agg3 = "[/r2]";
} # fine if ($tipo_rip == 2)
if ($tipo_rip == 3) {
$testo_agg1 = "[r3]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON LISTA COSTI IN PRENOTAZIONE",$pag)."\n";
$testo_agg3 = "[/r3]";
} # fine if ($tipo_rip == 3)
if (substr($tipo_rip,0,1) == "a") {
$testo_agg1 = "[r4 array=\"".substr($tipo_rip,1)."\"]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON PARTE DA RIPETERE PER OGNI VALORE DELL'ARRAY",$pag)."\n";
$testo_agg3 = "[/r4]";
} # fine if (substr($tipo_rip,0,1) == "a")
if ($tipo_rip == 5) {
$testo_agg1 = "[r5]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON LISTA PAGAMENTI",$pag)."\n";
$testo_agg3 = "[/r5]";
} # fine if ($tipo_rip == 5)
if ($tipo_rip == 6) {
$testo_agg1 = "[r6]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON LISTA APPARTAMENTI",'unit.php')."\n";
$testo_agg3 = "[/r6]";
} # fine if ($tipo_rip == 6)
if ($tipo_rip == 7) {
$testo_agg1 = "[c ".mex("nome_variabile",$pag)."=\"".mex("valore",$pag)."\"]";
$testo_agg2 = "\n".mex("SOSTITUISCI CON PARTE DA MOSTRARE SOLO SE E' SODDISFATTA LA CONDIZIONE PRECEDENTE",$pag)." (".mex("nome_variabile",$pag)." = ".mex("OPPURE",$pag)." != ".mex("valore",$pag).")\n";
$testo_agg3 = "[/c]";
} # fine if ($tipo_rip == 7)
if (!strcmp($pos_curs,"")) $testo_contratto_mod = $n_contratto.$testo_agg1.$testo_agg2.$testo_agg3;
else {
$pos_curs = strlen(utf8_encode(substr(utf8_decode($n_contratto),0,$pos_curs)));
if (!strcmp($pos_fine_sel,"")) $testo_contratto_mod = substr($n_contratto,0,$pos_curs).$testo_agg1.$testo_agg2.$testo_agg3.substr($n_contratto,$pos_curs);
else {
$pos_fine_sel = strlen(utf8_encode(substr(utf8_decode($n_contratto),0,$pos_fine_sel)));
$testo_contratto_mod = substr($n_contratto,0,$pos_curs).$testo_agg1.substr($n_contratto,$pos_curs,($pos_fine_sel - $pos_curs)).$testo_agg3.substr($n_contratto,$pos_fine_sel);
} # fine else if (!strcmp($pos_fine_sel,""))
} # fine else if (!strcmp($pos_curs,""))
} # fine if ($aggiungi_ripetizione or $aggiungi_ripetizione2)

if ($salva_foot) {
$tabelle_lock = array($tablecontratti);
$tabelle_lock = lock_tabelle($tabelle_lock);
$vecchio_foot = esegui_query("select numero from $tablecontratti where numero = '$num_contratto' and tipo = 'foothtm' ");
if (numlin_query($vecchio_foot)) {
$mostra_form_iniziale = "NO";
if (@get_magic_quotes_gpc()) $n_foot = stripslashes($n_foot);
$n_foot = aggslashdb($n_foot);
esegui_query("update $tablecontratti set testo = '$n_foot' where numero = '$num_contratto' and tipo = 'foothtm' ");
echo "".mex("Chiusura html cambiata",$pag).".<br>";
} # fine if (numlin_query($vecchio_foot))
unlock_tabelle($tabelle_lock);
} # fine if ($salva_foot)


if ($mostra_form_iniziale == "NO") {
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag$anchor\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Torna indietro",$pag)."\">
</div></form>";
} # fine if ($mostra_form_iniziale == "NO")

} # fine if ($cambia_qualcosa)





if ($mostra_form_iniziale != "NO") {

include("./includes/funzioni_contratti.php");
$nomi_contratti = trova_nomi_contratti($max_contr,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag);
if (!strcmp($nomi_contratti['pers'][$num_contratto_int],"")) $nome_contratto = "";
else $nome_contratto = " (".$nomi_contratti['pers'][$num_contratto_int].")";

unset($trad_var);
function mex2 ($messaggio) {
global $trad_var,$lingua_mex;
if (!$trad_var and $lingua_mex != "ita") include("./includes/lang/$lingua_mex/visualizza_contratto_var.php");
if ($trad_var[$messaggio]) $messaggio = $trad_var[$messaggio];
return $messaggio;
} # fine function mex2


echo "<h3>".ucfirst(mex("documento",$pag))." $num_contratto$nome_contratto</h3><br>
<br>";
$dati_contratto = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo $LIKE 'contr%'");
if (numlin_query($dati_contratto) != 1) exit();

echo "<table><tr><td><form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_contratto.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"numero_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"origine\" value=\"./$pag?num_contratto=$num_contratto\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Visualizza il documento",$pag)." $num_contratto_int\">
</div></form></td><td style=\"width: 30px;\"></td>";
if ($max_contr > 1) {
echo "<td><form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_utente_mod\" value=\"$id_utente_mod\">
<input type=\"hidden\" name=\"aggiorna_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"elimina_contratto\" value=\"SI\">
<input type=\"hidden\" name=\"num_contr_elimina\" value=\"$num_contratto_int\">
<input type=\"hidden\" name=\"origine\" value=\"./$pag?num_contratto=$num_contratto\">
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Elimina il documento",'personalizza.php')." $num_contratto_int\">
</div></form></td>";
} # fine if ($max_contr > 1)
echo "</tr></table><br>";

$tipo_contratto = substr(risul_query($dati_contratto,0,'tipo'),5);
$testo_contratto = risul_query($dati_contratto,0,'testo');
$sel_HTML = "";
$sel_EMAIL = "";
$sel_RTF = "";
$sel_TXT = "";
if ($tipo_contratto == "htm") $sel_HTML = " selected";
if ($tipo_contratto == "eml") $sel_EMAIL = " selected";
if ($tipo_contratto == "rtf") $sel_RTF = " selected";
if ($tipo_contratto == "txt") $sel_TXT = " selected";
$multilingua = 0;
if (substr($testo_contratto,0,7) == "#!mln!#") {
$multilingua = 1;
$lingua_default = substr($testo_contratto,7);
} # fine if (substr($testo_contratto,0,7) == "#!mln!#")
if ($multilingua) { $checked = " checked"; $b = "<b>"; $slash_b = "</b>"; }
else { $checked = ""; $b = ""; $slash_b = ""; }
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_formato\" value=\"SI\">
".mex("Formato del documento",$pag).":
 <select name=\"nuovo_formato\">
<option value=\"HTML\"$sel_HTML>HTML</option>
<option value=\"EMAIL\"$sel_EMAIL>EMAIL</option>
<option value=\"RTF\"$sel_RTF>RTF</option>
<option value=\"TXT\"$sel_TXT>TXT</option>
</select>
(<label><input type=\"checkbox\" name=\"multi_lingua\" value=\"1\"$checked> $b".mex("multi-lingua",$pag)."$slash_b</label>)
 <input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form><br>";


if ($tipo_contratto == "rtf" or $tipo_contratto == "htm" or $tipo_contratto == "txt") {
$dir_salva = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'dir'");
if (numlin_query($dir_salva) == 1) {
$dir_salva = risul_query($dir_salva,0,'testo');
if ($dir_salva == "~") $dir_salva = $dir_salva_home;
$checked = " checked";
} # fine if (numlin_query($dir_salva) == 1)
else {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") $dir_salva = "";
else $dir_salva = $dir_salva_home;
$checked = "";
} # fine else if (numlin_query($dir_salva) == 1)
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<label><input type=\"checkbox\" id=\"sacon\" name=\"salva_contr\" value=\"SI\"$checked>
".mex("Salva i documenti con numero progressivo nella cartella",$pag)." ";
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") echo C_CARTELLA_CREA_MODELLI."/";
echo "</label><input type=\"text\" name=\"nuova_dir_salva\" size=\"20\" value=\"$dir_salva\" onclick=\"document.getElementById('sacon').checked='1'\">
<input class=\"sbutton\" type=\"submit\" name=\"cambia_dir_salva\" value=\"".mex("Cambia",$pag)."\">
</div></form>";

if ($checked) {
$salv_contr = 1;
echo "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"width: 50px;\"></td><td>";

$compress = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'compress' ");
if (!numlin_query($compress)) $compress = "";
else $compress = risul_query($compress,0,'testo');
if ($compress) $checked = " checked";
else $checked = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_compress\" value=\"SI\">
<label><input type=\"checkbox\" name=\"compress\" value=\"SI\"$checked>
".mex("Comprimi i file",$pag)."</label>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form>";

$autosalv = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'autosalv' ");
if (!numlin_query($autosalv)) $autosalv = "";
else $autosalv = risul_query($autosalv,0,'testo');
if (!$autosalv) $sel_mai = " selected";
else ${"sel_".$autosalv} = " selected";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_autosalv\" value=\"SI\">
".mex("Salva automaticamente il documento",$pag).":
 <select name=\"n_autosalv\">
<option value=\"\"$sel_mai>".mex("mai",$pag)."</option>
<option value=\"checkin\"$sel_checkin>".mex("alla registrazione dell'entrata",$pag)."</option>
<option value=\"checkout\"$sel_checkout>".mex("alla registrazione dell'uscita",$pag)."</option>
</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form>";

$incr_np = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'incr_np' ");
if (!numlin_query($incr_np)) $incr_np = "";
else $incr_np = risul_query($incr_np,0,'testo');
if ($incr_np) $checked = " checked";
else $checked = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_incr_np\" value=\"SI\">
<label><input type=\"checkbox\" name=\"incr_np\" value=\"SI\"$checked>
".mex("Incrementa il numero progressivo ad ogni sua apparizione",$pag)."</label>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form>";

$num_prog = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'num_prog' ");
if (!numlin_query($num_prog)) $num_prog = "";
else $num_prog = risul_query($num_prog,0,'testo');
$contr_salva = esegui_query("select * from $tablecontratti where tipo = 'dir'");
$num_contr_salva = numlin_query($contr_salva);
$opt_num_prog = "";
for ($num_c = 0 ; $num_c < $num_contr_salva ; $num_c++) {
$num_contr = risul_query($contr_salva,$num_c,'numero');
if ($num_contr != $num_contratto_int and ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contr] == "SI" or $num_prog == $num_contr)) {
$num_prog_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contr' and tipo = 'num_prog' ");
if (!numlin_query($num_prog_esistente)) {
$nome_contr = mex("il documento",$pag)." ".$num_contr;
if (strcmp($nomi_contratti['pers'][$num_contr],"")) $nome_contr .= " (".$nomi_contratti['pers'][$num_contr].")";
if ($num_prog != $num_contr) $sel = "";
else $sel = " selected";
$opt_num_prog .= "<option value=\"$num_contr\"$sel>$nome_contr</option>";
} # fine if (!numlin_query($num_prog_esistente))
} # fine if ($num_contr != $num_contratto and...
} # fine for $num_c
if ($opt_num_prog) {
if ($num_prog) $sel = "";
else $sel = " selected";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_num_prog\" value=\"SI\">
".mex("Condividi il numero progressivo con",$pag)."
 <select name=\"contr_num_prog\">
<option value=\"\"$sel>----</option>
$opt_num_prog</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form>";
} # fine if ($opt_num_prog)

$nome_file_salva = esegui_query("select testo from $tablecontratti where numero = '$num_contratto' and tipo = 'nomefile' ");
if (numlin_query($nome_file_salva)) {
$nome_file_salva = risul_query($nome_file_salva,0,'testo');
$checked_ncontr = "";
$checked_npers = " checked";
} # fine if (numlin_query($nome_file_salva))
else {
$nome_file_salva = "";
$checked_ncontr = " checked";
$checked_npers = "";
} # fine else if (numlin_query($nome_file_salva))
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_nome_file\" value=\"SI\">
<table cellspacing=0 cellpadding=0><tr><td valign=\"top\">".mex("Usa come nome del file",$pag).":
</td><td onclick=\"document.getElementById('fncontr').checked='1'\">
<input type=\"radio\" name=\"tipo_nome_file\" id=\"fncontr\" value=\"contr\"$checked_ncontr>".mex("il nome del documento",$pag)."
</td></tr><tr><td></td><td onclick=\"document.getElementById('fnpers').checked='1'\">
<input type=\"radio\" name=\"tipo_nome_file\" id=\"fnpers\" value=\"pers\"$checked_npers>".mex("un altro nome",$pag).":
 <input type=\"text\" name=\"nome_file_salva\" size=\"30\" maxlength=\"60\" value=\"$nome_file_salva\">
</td></tr><tr><td></td><td>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</td></tr></table></div></form>
</td></tr></table>";
} # fine if ($checked)
else $salv_contr = 0;
echo "<br>";


if ($priv_mod_doc_api == "s") {
$api_esistente = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'api'");
if (numlin_query($api_esistente)) $checked = " checked";
else $checked = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<label><input type=\"checkbox\" id=\"capi\" name=\"cons_api\" value=\"SI\"$checked>
".mex("Considera questo documento come una API",$pag)."</label>
<input class=\"sbutton\" type=\"submit\" name=\"cambia_cons_api\" value=\"".mex("Cambia",$pag)."\">
</div></form>";
if ($checked) {
$pass_api = risul_query($api_esistente,0,'testo');
$id_utente_api = explode(";",$pass_api);
$id_utente_api = $id_utente_api[0];
$pass_api = substr($pass_api,(strlen($id_utente_api) + 1));
echo "<table cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"width: 50px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div style=\"padding-top: 6px;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Password",$pag).": <input type=\"text\" name=\"pass_api\" size=\"12\" maxlength=\"60\" value=\"$pass_api\">
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div style=\"padding-top: 6px;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Mostra il documento come visto dall'utente",$pag).": <select name=\"id_utente_api\">";
$lista_utenti = esegui_query("select idutenti,nome_utente from $tableutenti order by idutenti");
for ($num1 = 0 ; $num1 < numlin_query($lista_utenti) ; $num1++) {
$id = risul_query($lista_utenti,$num1,'idutenti');
if ($id_utente == 1 or $id == $id_utente or $id == $id_utente_api) {
$nome = risul_query($lista_utenti,$num1,'nome_utente');
if ($id == $id_utente_api) $sel = " selected";
else $sel = "";
echo "<option value=\"$id\"$sel>$nome</option>";
} # fine if ($id_utente == 1 or $id == $id_utente or $id == $id_utente_api)
} # fine for $num1
echo "</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Cambia",$pag)."\">
</div></form>";
if ($salv_contr) echo "<div style=\"padding-top: 6px;\"><em class=\"colblu\">".mex("Nota",$pag)."</em>: ".mex("i documenti esistenti non verranno mai sovrascritti, ogni volta che si chiama la API verrà salvato un nuovo documento",$pag).".</div>";
$link_api = "";
if (defined('C_FILE_DOMINIO') and C_FILE_DOMINIO != "" and C_NASCONDI_MARCA != "SI") {
$altri_domini = @file(C_FILE_DOMINIO);
if ($altri_domini) {
$link_api = "https://".trim($altri_domini[0])."/";
if (defined('C_DOMINIO_ADMIN_DIR') and C_DOMINIO_ADMIN_DIR != "") $link_api .= C_DOMINIO_ADMIN_DIR."/";
} # fine if ($altri_domini)
} # fine if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and C_NASCONDI_MARCA != "SI")
if (!$link_api) {
if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or @$HTTP_SERVER_VARS['SERVER_NAME']) {
if (@$PHP_SELF or @$_SERVER['PHP_SELF']) {
if ($_SERVER['SERVER_NAME']) $SERVER_NAME = $_SERVER['SERVER_NAME'];
elseif ($HTTP_SERVER_VARS['SERVER_NAME']) $SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
if ($_SERVER['PHP_SELF']) $PHP_SELF = $_SERVER['PHP_SELF'];
if (substr($PHP_SELF,0,1) != "/") $PHP_SELF = "/".$PHP_SELF;
$link_api = "https://".$SERVER_NAME.$PHP_SELF;
if (substr($link_api,(strlen($pag) * -1)) == $pag) $link_api = substr($link_api,0,(strlen($pag) * -1));
if (substr($link_api,-4) == ".php") {
$url_vett1 = explode("/",$link_api);
$link_api = substr($link_api,0,(strlen($url_vett1[(count($url_vett1) - 1)]) * -1));
} # fine if (substr($link_api,-4) == ".php")
} # fine if (@$PHP_SELF or @$_SERVER['PHP_SELF'])
} # fine if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or...
} # fine if (!$link_api)
$link_api .= "api.php";
$sec_oggi = time() + (C_DIFF_ORE * 3600);
$oggi = date("Y-m-d",$sec_oggi);
$dopodomani = date("Y-m-d",($sec_oggi + 172800));
$altroieri = date("Y-m-d",($sec_oggi - 172800));
$link1 = "$link_api?doc=$num_contratto&pass=$pass_api&res_year=$anno";
$link2 = "$link_api?doc=$num_contratto&pass=$pass_api&res_from=$oggi&res_to=$dopodomani";
$link3 = "$link_api?doc=$num_contratto&pass=$pass_api&res_num=2-5";
$link4 = "$link_api?doc=$num_contratto&pass=$pass_api&res_ins_from=$altroieri&res_ins_to=$oggi";
$link5 = "$link_api?doc=$num_contratto&pass=$pass_api&res_arr=$oggi";
$link6 = "$link_api?doc=$num_contratto&pass=$pass_api&res_dep=today";
$link7 = "$link_api?doc=$num_contratto&pass=$pass_api&clients=all";
$link8 = "$link_api?doc=$num_contratto&pass=$pass_api&clients=guests";
echo "<div style=\"padding-top: 4px;\"></div>
<table class=\"rbox\" cellspacing=0 cellpadding=0><tr><td>
<div style=\"padding-top: 4px;\"><em>".mex("Esempi di URL per chiamare il documento",$pag)."</em>:</div>
<div style=\"padding-top: 4px;\">".mex("Tutte le prenotazioni dell'anno",$pag)." $anno: <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link1\" size=\"".(strlen($link1) + 1)."\"></div>
<div style=\"padding-top: 6px;\"><em>".mex("Nota",$pag)."</em>: ".mex("se la variabile",$pag)." <em>res_year</em> ".mex("non è presente nella URL allora verranno usate le prenotazioni dell'anno corrente",$pag).".</div>
<div style=\"padding-top: 6px;\">".mex("Tutte le prenotazioni presenti dal",$pag)." ".formatta_data($oggi,$stile_data)." ".mex("al",$pag)." ".formatta_data($dopodomani,$stile_data).": <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link2\" size=\"".(strlen($link2) + 1)."\"></div>
<div style=\"padding-top: 6px;\"><em>".mex("Nota",$pag)."</em>: ".mex("si possono usare anche le parole",$pag)." <em>today</em>, <em>tomorrow</em> ".mex("e",$pag)." <em>yesterday</em> ".mex("come date",$pag).".</div>
<div style=\"padding-top: 6px;\">".mex("Tutte le prenotazioni con numero dal",$pag)." 2 ".mex("al",$pag)." 5: <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link3\" size=\"".(strlen($link3) + 1)."\"></div>
<div style=\"padding-top: 6px;\">".mex("Altri esempi dell'uso della variabile",$pag)." <em>res_num</em>: <em>res_num=3</em> / <em>res_num=3-</em> / <em>res_num=3,7</em> / <em>res_num=3,6-9</em></div>
<div style=\"padding-top: 6px;\">".mex("Tutte le prenotazioni inserite dal",$pag)." ".formatta_data($altroieri,$stile_data)." ".mex("al",$pag)." ".formatta_data($oggi,$stile_data).": <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link4\" size=\"".(strlen($link4) + 1)."\"></div>
<div style=\"padding-top: 6px;\">".mex("Tutti gli arrivi del",$pag)." ".formatta_data($oggi,$stile_data).": <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link5\" size=\"".(strlen($link5) + 1)."\"></div>
<div style=\"padding-top: 6px;\">".mex("Tutti le partenze del",$pag)." ".formatta_data($oggi,$stile_data).": <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link6\" size=\"".(strlen($link6) + 1)."\"></div>
<div style=\"padding-top: 6px;\">".mex("Tutti i clienti",$pag)." (".mex("escludendo gli ospiti",$pag)."): <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link7\" size=\"".(strlen($link7) + 1)."\"></div>
<div style=\"padding-top: 6px;\">".mex("Tutti i clienti",$pag)." (".mex("inclusi gli ospiti",$pag)."): <input type=\"text\" readonly onClick=\"this.select();\" value=\"$link8\" size=\"".(strlen($link8) + 1)."\"></div>
</td></tr></table>
</td></tr></table>";
} # fine if ($checked)
echo "<br>";
} # fine if ($priv_mod_doc_api == "s")

} # fine if ($tipo_contratto == "rtf" or $tipo_contratto == "htm" or...


$contr_head = "";
$contr_foot = "";
if ($tipo_contratto == "htm") {
$intestazione_html = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'headhtm'");
if (numlin_query($intestazione_html) == 1) $checked = " checked";
else $checked = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<label><input type=\"checkbox\" name=\"intestazione_pers\" value=\"SI\"$checked>
".mex("Intestazione html personalizzata",$pag)."</label>
<input class=\"sbutton\" type=\"submit\" name=\"cambia_intestazione_pers\" value=\"".mex("Cambia",$pag)."\">
</div></form><br>";
if ($checked) {
$contr_head = risul_query($intestazione_html,0,'testo');
$contr_foot = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'foothtm'");
$contr_foot = risul_query($contr_foot,0,'testo');
} # fine if ($checked)
} # fine if ($tipo_contratto == "htm")


if ($tipo_contratto == "txt") {
$estensione_txt = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'est_txt'");
if (numlin_query($estensione_txt) == 1) {
$checked_predef = "";
$checked_pers = " checked";
$estensione_pers_txt = risul_query($estensione_txt,0,'testo');
} # fine if (numlin_query($estensione_txt) == 1)
else {
$checked_predef = " checked";
$checked_pers = "";
$estensione_pers_txt = "txt";
} # fine else if (numlin_query($estensione_txt) == 1)
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<table cellspacing=0 cellpadding=0 style=\"line-height: 1;\"><tr><td style=\"vertical-align: top;\">
".mex("Estensione con cui vedere e scaricare il documento",$pag).": </td>
<td><label><input type=\"radio\" name=\"tipo_estensione_txt\" value=\"predef\"$checked_predef>".mex("predefinita",$pag).": .txt</label><br>
<label><input type=\"radio\" name=\"tipo_estensione_txt\" id=\"te_pers\" value=\"pers\"$checked_pers>".mex("personalizzata",$pag).":
 .</label><input type=\"text\" name=\"estensione_pers_txt\" size=\"8\" maxlength=\"10\" value=\"$estensione_pers_txt\" onclick=\"document.getElementById('te_pers').checked='1'\"><br>
<input class=\"sbutton\" type=\"submit\" name=\"cambia_estenstione_pers\" value=\"".mex("Cambia",$pag)."\">
</td></tr></table>
</div></form><br>";
if ($checked) {
$contr_head = risul_query($intestazione_html,0,'testo');
$contr_foot = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'foothtm'");
$contr_foot = risul_query($contr_foot,0,'testo');
} # fine if ($checked)
} # fine if ($tipo_contratto == "txt")


if ($tipo_contratto == "eml") {
$oggetto = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'oggetto'");
$oggetto = risul_query($oggetto,0,'testo');
echo "<div class=\"linhbox\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Oggetto",$pag).":
<input type=\"text\" name=\"nuovo_oggetto\" size=\"50\" maxlength=\"60\" value=\"$oggetto\">
<input class=\"sbutton\" type=\"submit\" name=\"cambia_oggetto\" value=\"".mex("Cambia",$pag)."\">
</div></form>";
$opz_eml = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'opzeml'");
$opz_eml = risul_query($opz_eml,0,'testo');
$opz_eml = explode(";",$opz_eml);
if (!$opz_eml[0]) $sel = " selected";
else $sel = "";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Formato dell'email",$pag).":
 <select name=\"formato_email\">
<option value=\"\"$sel>".mex("testo semplice",$pag)."</option>";
if ($opz_eml[0] == "html") $sel = " selected";
else $sel = "";
echo "<option value=\"html\"$sel>".mex("html",$pag)."</option>
</select>
<input class=\"sbutton\" type=\"submit\" name=\"cambia_formato_email\" value=\"".mex("Cambia",$pag)."\">
</div></form>";
$allegato = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'allegato'");
$allegato = risul_query($allegato,0,'testo');
if ($allegato) {
$allegato = esegui_query("select * from $tablecontratti where numero = '$allegato' and tipo = 'file_all'");
$allegato = risul_query($allegato,0,'testo');
$allegato = explode(",",$allegato);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
".mex("Allegato",$pag).": <b>".$allegato[0]."</b>
<input class=\"sbutton\" type=\"submit\" name=\"elimina_allegato\" value=\"".mex("Elimina",$pag)."\">
</div></form>";
} # fine if ($allegato)
else {
echo "<table cellspacing=\"0\"><tr><td>
".mex("Allegato",$pag).":</td><td>
<form accept-charset=\"utf-8\" enctype=\"multipart/form-data\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"50000000\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
  <input name=\"file_allegato_upload\" type=\"file\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input class=\"sbutton\" type=\"submit\" name=\"upload_allegato\" value=\"".mex("Aggiungi",$pag)."\">
</div></form></td></tr>";
$allegati = esegui_query("select * from $tablecontratti where tipo = 'file_all'");
$num_allegati = numlin_query($allegati);
for ($num1 = 0 ; $num1 < $num_allegati ; $num1++) {
$num_all = risul_query($allegati,$num1,'numero');
$nome_all = explode(",",risul_query($allegati,$num1,'testo'));
$opt_allegati .= "<option value=\"$num_all\">".$nome_all[0]."</option>";
} # fine for $num1
if ($opt_allegati) {
echo "<tr><td></td><td>
<form accept-charset=\"utf-8\" enctype=\"multipart/form-data\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"aggiungi_allegato\" value=\"SI\">
<select name=\"num_allegato\">
$opt_allegati</select>
<input class=\"sbutton\" type=\"submit\" value=\"".mex("Aggiungi",$pag)."\">
</div></form></td></tr>";
} # fine if ($opt_allegati)
echo "</table>";
} # fine else if ($allegato)
if ($opz_eml[1] == "SI") $checked_mittente = " checked";
else $checked_mittente = "";
if ($opz_eml[2]) {
$checked_email = " checked";
$email_bcc = $opz_eml[2];
} # fine if ($opz_eml[2])
else {
$checked_email = "";
$email_bcc = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'dati_struttura' and idutente = '$id_utente'");
$email_bcc = risul_query($email_bcc,0,'valpersonalizza');
$email_bcc = explode("#@&",$email_bcc);
$email_bcc = $email_bcc[2];
} # fine else if ($bcc[1])
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<table cellspacing=0 cellpadding=0 style=\"line-height: 1;\"><tr><td valign=\"top\">".mex("Mandare una copia bcc",$pag).":</td><td>
<label><input type=\"checkbox\" name=\"bcc_mittente\" value=\"SI\"$checked_mittente>".mex("al mittente",$pag)."</label><br>
<label><input type=\"checkbox\" id=\"bcc_ind\" name=\"bcc_indirizzo\" value=\"SI\"$checked_email>".mex("all'indirizzo",$pag)."
 </label><input type=\"text\" name=\"email_bcc\" size=\"50\" maxlength=\"60\" value=\"$email_bcc\" onclick=\"document.getElementById('bcc_ind').checked='1'\"><br>
<input class=\"sbutton\" type=\"submit\" name=\"cambia_bcc\" value=\"".mex("Cambia",$pag)."\">
</td></tr></table></div></form></div><br>";
} # fine ($tipo_contratto == "eml")


if ($multilingua) {
unset($lingue);
$num_lingue = 0;
if ($lingua_default != "ita" and (preg_replace("/[a-z]{2,3}/","",$lingua_default) != "" or !@is_file("./includes/lang/$lingua_default/l_n"))) $lingua_default = $lingua_mex;
$lingue[$num_lingue] = $lingua_default;
if ($lingua_default != "ita") {
$lingue['nome'][$num_lingue] = ucfirst(trim(implode("",file("./includes/lang/$lingua_default/l_n"))));
$num_lingue++;
$lingue[$num_lingue] = "ita";
} # fine else if ($lingua_default != "ita")
$lingue['nome'][$num_lingue] = "Italiano";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." and $ini_lingua != ".." and $ini_lingua != $lingua_default) {
$num_lingue++;
$lingue[$num_lingue] = $ini_lingua;
$lingue['nome'][$num_lingue] = ucfirst(trim(implode("",file("./includes/lang/$ini_lingua/l_n"))));
} # fine if ($file != "." and $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$num_lingue++;
} # fine if ($multilingua)
else {
$num_lingue = 1;
echo mex("Modifica il documento",$pag).":<br>";
} # fine else if ($multilingua) 


if ($contr_head) {
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" name=\"salva_head\" value=\"".mex("Modifica l'intestazione html",$pag)."\"><br>
<textarea id=\"contr_head\" name=\"n_head\" rows=8 cols=135 style=\"white-space: pre; overflow: auto;\">".htmlspecialchars($contr_head)."</textarea>
</div></form><br>";
} # fine if ($contr_head)


for ($num_lingua = 0 ; $num_lingua < $num_lingue ; $num_lingua++) {

if ($num_lingua > 0) echo "<br><br>";
if ($multilingua) {
$testo_contratto = esegui_query("select * from $tablecontratti where numero = '$num_contratto' and tipo = 'mln_".$lingue[$num_lingua]."'");
if (numlin_query($testo_contratto)) $testo_contratto = risul_query($testo_contratto,0,'testo');
else $testo_contratto = "";
if ($num_lingua) echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"lingua_contr\" value=\"".$lingue[$num_lingua]."\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"lingua_predef\" value=\"SI\">";
echo mex("Modifica il documento",$pag)."";
echo " ".mex("in",$pag)." <b>".$lingue['nome'][$num_lingua]."</b> (";
if (!$num_lingua) echo "".mex("predefinito",$pag)."):<br>";
else echo "<input class=\"smallsbutton\" type=\"submit\" value=\"".mex("fai predefinito",$pag)."\">):</div></form>";
} # fine if ($multilingua)

echo "<table><tr><td style=\"height: 3px;\"></td></tr></table>";
if ($testo_contratto_mod and (!$multilingua or $lingua_contr == $lingue[$num_lingua])) $testo_contratto = $testo_contratto_mod;
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag#contr_txtbox".$lingue[$num_lingua]."\" onSubmit=\"agg_pos_curs($num_lingua)\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">";
if ($multilingua) echo "<input type=\"hidden\" name=\"lingua_contr\" value=\"".$lingue[$num_lingua]."\">";
echo "<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"pos_curs\" value=\"\" id=\"pos_curs$num_lingua\">
<input type=\"hidden\" name=\"pos_fine_sel\" value=\"\" id=\"pos_fine_sel$num_lingua\">
<table style=\"margin-left: auto; margin-right: auto;\">";
$linea_mod = "<tr><td><a name=\"contr_txtbox".$lingue[$num_lingua]."\"></a>
".mex("Variabili",$pag).":
 <select name=\"var_agg\">";

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
$num_var_predef--;
$num_int_contr = $num_contratto;
while ((string) substr($num_int_contr,0,1) == (string) "0") $num_int_contr = substr($num_int_contr,1);
$variabili_pers = esegui_query("select * from $tablecontratti where tipo = 'var' or tipo = 'var$num_int_contr' order by tipo, numero");
$num_variabili_pers = numlin_query($variabili_pers);
for ($num1 = 0 ; $num1 < $num_variabili_pers ; $num1++) {
$var_pers = risul_query($variabili_pers,$num1,'testo');
$num_var_pers = risul_query($variabili_pers,$num1,'numero');
$var_predef[$num_var_predef] = $var_pers;
$num_var_predef++;
} # fine for $num1
$array_pers = esegui_query("select * from $tablecontratti where tipo = 'vett' or tipo = 'vett$num_int_contr' order by tipo, numero");
$num_array_pers = numlin_query($array_pers);
for ($num1 = 0 ; $num1 < $num_array_pers ; $num1++) {
$arr_pers_vett = explode(";",risul_query($array_pers,$num1,'testo'));
$arr_pers = $arr_pers_vett[0];
$var_arr_pers = $arr_pers_vett[1];
$num_arr_pers = risul_query($array_pers,$num1,'numero');
$var_predef[$num_var_predef] = "$arr_pers(".mex2($var_arr_pers).")";
$num_var_predef++;
$option_array .= "<option value=\"a$arr_pers\">".mex("ripetizione array",$pag)." $arr_pers [r4]...[/r4]</option>";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_var_predef ; $num1++) {
$linea_mod .= "<option value=\"".mex2($var_predef[$num1])."\">".mex2($var_predef[$num1])."</option>";
} # fine for $num1
$linea_mod .= "</select>
<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_var\" value=\"".mex("Aggiungi",$pag)."\">
</td><td style=\"width: 30px;\"></td><td>
<input class=\"sbutton\" type=\"submit\" name=\"salva_modifiche\" value=\"".mex("Salva le modifiche",$pag)."\">
</td><td style=\"width: 30px;\"></td><td>".mex("Limiti",$pag)." <select name=\"tipo_rip\">";
if (!$multilingua) $linea_mod .= "<option value=\"1\">".mex("ripetizione prenotazioni",$pag)." [r]...[/r]</option>";
$linea_mod .= "<option value=\"2\">".mex("ripetizione ospiti",$pag)." [r2]...[/r2]</option>
<option value=\"3\">".mex("ripetizione costi aggiuntivi",$pag)." [r3]...[/r3]</option>
$option_array
<option value=\"5\">".mex("ripetizione pagamenti",$pag)." [r5]...[/r5]</option>
<option value=\"6\">".mex("ripetizione unità",$pag)." [r6]...[/r6]</option>
<option value=\"7\">".mex("parte sotto condizione",$pag)." [c]...[/c]</option>
</select>
<input class=\"sbutton\" type=\"submit\" name=\"aggiungi_ripetizione\" value=\"".mex("Aggiungi",$pag)."\">
</td></tr>";
if ($tipo_contratto == "rtf" or $tipo_contratto == "txt") $wrap = "wrap=\"off\"";
else $wrap = "style=\"white-space: pre; overflow: auto;\"";
# htmlspecialchars su $testo_contratto necessario perchè altrimenti per esempio &nbsp; diventa uno spazio all'interno della textarea
echo "$linea_mod<tr><td colspan=\"5\" align=\"center\">
<textarea id=\"contr_txta$num_lingua\" name=\"n_contratto\" rows=120 cols=135 $wrap>".htmlspecialchars($testo_contratto)."</textarea></td></tr>";
$linea_mod = str_replace("<a name=\"contr_txtbox".$lingue[$num_lingua]."\"></a>","",$linea_mod);
$linea_mod = str_replace("<select name=\"var_agg\">","<select name=\"var_agg2\">",$linea_mod);
$linea_mod = str_replace("type=\"submit\" name=\"aggiungi_var\"","type=\"submit\" name=\"aggiungi_var2\"",$linea_mod);
$linea_mod = str_replace("<select name=\"tipo_rip\">","<select name=\"tipo_rip2\">",$linea_mod);
$linea_mod = str_replace("type=\"submit\" name=\"aggiungi_ripetizione\"","type=\"submit\" name=\"aggiungi_ripetizione2\"",$linea_mod);
echo "$linea_mod</table>
</div></form></div><br>";

} # fine for $num_lingua

if ($contr_foot) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"num_contratto\" value=\"$num_contratto\">
<input type=\"hidden\" name=\"cambia_qualcosa\" value=\"SI\">
<textarea id=\"contr_foot\" name=\"n_foot\" rows=4 cols=135 style=\"white-space: pre; overflow: auto;\">".htmlspecialchars($contr_foot)."</textarea>
<input class=\"sbutton\" type=\"submit\" name=\"salva_foot\" value=\"".mex("Modifica la chiusura html",$pag)."\"><br>
</div></form><br>";
} # fine if ($contr_foot)

echo "<script type=\"text/javascript\">
<!--
function resize_contr_txta(numlingue) {
var height = Math.round(document.body.clientHeight - 80);
var width = Math.round(document.body.clientWidth - 60);
if (height < 300) height = 300;
if (width < 300) width = 300;
for (n1 = 0 ; n1 < $num_lingua ; n1++) {
document.getElementById('contr_txta'+n1).style.height = height + 'px';
document.getElementById('contr_txta'+n1).style.width = width + 'px';
}
";
if ($contr_head) {
echo "document.getElementById('contr_head').style.width = width + 'px';
document.getElementById('contr_foot').style.width = width + 'px';
";
} # fine if ($contr_head)
echo "}
resize_contr_txta();
window.onresize = new Function(\"resize_contr_txta()\");

function agg_pos_curs(numlingua) {
var pos_curs = 0;
var txtbox = document.getElementById('contr_txta'+numlingua);
if (txtbox.selectionStart) {
pos_curs = txtbox.selectionStart;
var len1 = txtbox.value.substring(0,pos_curs).length;
var len2 = txtbox.value.substring(0,pos_curs).replace(/(\\n|\\r)/g,'').length;
pos_curs = pos_curs + len1 - len2;
if (txtbox.selectionEnd && txtbox.selectionStart != txtbox.selectionEnd) {
var pos_fine_sel = txtbox.selectionEnd;
len1 = txtbox.value.substring(0,pos_fine_sel).length;
len2 = txtbox.value.substring(0,pos_fine_sel).replace(/(\\n|\\r)/g,'').length;
pos_fine_sel = pos_fine_sel + len1 - len2;
document.getElementById('pos_fine_sel'+numlingua).value = pos_fine_sel;
}
}
else if (document.selection) {
txtbox.focus(); 
var sel = document.selection.createRange();
var dup = sel.duplicate();
dup.moveToElementText(txtbox);
sel.text = \"\\001\";
pos_curs = dup.text.indexOf(\"\\001\");
sel.moveStart('character',-1);
sel.text = '';
}
document.getElementById('pos_curs'+numlingua).value = pos_curs;
}
-->
</script>";


echo "<hr style=\"width: 95%\"><br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./modifica_var_contr.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"contr_cond\" value=\"$num_int_contr\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Variabili personalizzate e condizioni del documento",$pag)." $num_contratto\">
</div></form><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"personalizza.php#contratti\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Torna indietro",$pag)."\">
</div></form><br></div>";


} # fine if ($mostra_form_iniziale != "NO")




if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($priv_mod_doc == "s")
} # fine if ($anno_utente_attivato == "SI" and $priv_mod_doc == "s" and...
} # fine if ($id_utente)



?>
