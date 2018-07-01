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


$pag = "crea_backup.php";
$titolo = "HotelDruid: Backup";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
include("./includes/funzioni_backup.php");
$numconnessione = connetti_db_per_backup($PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata);
include("./includes/funzioni.php");
$tableanni = $PHPR_TAB_PRE."anni";
$tableversioni = $PHPR_TAB_PRE."versioni";
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablecache = $PHPR_TAB_PRE."cache";


if (defined("C_UTENTE_BACKUP_ESTERNO") and C_UTENTE_BACKUP_ESTERNO != "" and $utente_backup == C_UTENTE_BACKUP_ESTERNO) $id_utente = "b";
else $id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1 and $id_utente != "b") {
$privilegi_annuali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '$anno'");
if (numlin_query($privilegi_annuali_utente) == 0) $anno_utente_attivato = "NO";
else $anno_utente_attivato = "SI";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente' and anno = '1'");
$priv_mod_pers = risul_query($privilegi_globali_utente,0,'priv_mod_pers');
$priv_crea_backup = substr($priv_mod_pers,1,1);
chiudi_query($privilegi_annuali_utente);
chiudi_query($privilegi_globali_utente);
} # fine if ($id_utente != 1 and $id_utente != "b")
else {
$priv_crea_backup = "s";
$anno_utente_attivato = "SI";
} # fine else if ($id_utente != 1 and $id_utente != "b")

if ($priv_crea_backup == "s") {

if ($mostra_header != "NO") {
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
} # fine if ($mostra_header != "NO")


if ($id_utente == "b") {
$azione = "SI";
$upload_backup = "";
$cancella_backup = "";
$ripristina_backup = "";
$lingua_mex = "ita";
} # fine if ($id_utente == "b")



if ($azione == "SI") {



if ($crea_backup) {
include_once("./includes/funzioni_$PHPR_DB_TYPE"."_extra.php");

if ($backup_contratti != "SI") {
$raggiunto_limite = 0;
if (defined('C_LIMITE_BACKUP_GIORNALIERI') and C_LIMITE_BACKUP_GIORNALIERI > 0 and $id_utente != "b") {
$tabelle_lock = array($tablecache);
$tabelle_lock = lock_tabelle($tabelle_lock);
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$oggi = substr($adesso,0,10);
esegui_query("delete from $tablecache where tipo = 'lim_back' and datainserimento < '$oggi 00:00:00' ");
$lim_back = esegui_query("select * from $tablecache where tipo = 'lim_back' and datainserimento >= '$oggi 00:00:00' ");
if (!numlin_query($lim_back)) esegui_query("insert into $tablecache (numero,tipo,data_modifica,datainserimento) values ('1','lim_back','$adesso','$adesso') ");
else {
$num_lim_back = risul_query($lim_back,0,'numero');
if ($num_lim_back >= C_LIMITE_BACKUP_GIORNALIERI) $raggiunto_limite = 1;
else esegui_query("update $tablecache set numero = '".($num_lim_back + 1)."', data_modifica = '$adesso' where tipo = 'lim_back' and datainserimento >= '$oggi 00:00:00' ");
} # fine else if (!numlin_query($lim_back))
unlock_tabelle($tabelle_lock);
} # fine if (defined('C_LIMITE_BACKUP_GIORNALIERI') and C_LIMITE_BACKUP_GIORNALIERI > 0 and $id_utente != "b")
if ($raggiunto_limite) echo mex("Raggiunto il limite giornaliero di backup",$pag)." (".C_LIMITE_BACKUP_GIORNALIERI.").<br>";
else {

$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);

$cond_anni = "";
$cond_personalizza = "";
$cond_ic_e_priv = "";
if ($anni_backup) {
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_reg = risul_query($anni,$num1,'idanni');
if (${'anno_sel'.$anno_reg}) {
if (!$cond_anni) $cond_anni = "where ";
else $cond_anni .= " or ";
$cond_anni .= "idanni = '$anno_reg'";
} # fine if (${'anno_sel'.$anno_reg})
else {
if (!$cond_personalizza) $cond_personalizza = "where ";
else $cond_personalizza .= " and ";
$cond_personalizza .= "idpersonalizza != 'giorno_vedi_ini_sett$anno_reg'";
if (!$cond_ic_e_priv) $cond_ic_e_priv = "where ";
else $cond_ic_e_priv .= " and ";
$cond_ic_e_priv .= "anno != '$anno_reg'";

} # fine else if (${'anno_sel'.$anno_reg})
} # fine for $num1
if (!$cond_anni) $anni_backup = "";
} # fine if ($anni_backup)

$tabelle_lock = "";
$altre_tab_lock = array($tableanni);
$num_lock = 1;
$anno_trovato = 0;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."prenota".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."rclientiprenota".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."costiprenota".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."versioni";
$num_lock++;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."ntariffe".risul_query($anni,$num1,'idanni');
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."periodi".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."appartamenti";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."clienti";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."relclienti";
$num_lock++;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."costi".risul_query($anni,$num1,'idanni');
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."regole".risul_query($anni,$num1,'idanni');
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."soldi".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."descrizioni";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."nazioni";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."citta";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."regioni";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."documentiid";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."parentele";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."contratti";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."interconnessioni";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."messaggi";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."personalizza";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."utenti";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."gruppi";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."privilegi";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."relutenti";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."relgruppi";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."beniinventario";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."magazzini";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."relinventario";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."casse";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."sessioni";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."transazioni";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."transazioniweb";
$num_lock++;
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."cache";
$num_lock++;
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
if ($id_utente == "b") $nome_file_bk = C_DATI_PATH."/backup_ext.php";
else $nome_file_bk = C_DATI_PATH."/hoteld_backup.php";
$file = @fopen($nome_file_bk,"w");
if ($file) {
flock($file,2);
allunga_tempo_limite();
$versione_corrente = esegui_query("select * from $tableversioni where idversioni = 1");
$versione_corrente = risul_query($versione_corrente,0,'num_versione');
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
fwrite($file,"<?php exit(); ?>

<!--             $adesso             -->

<!--  **    SAVE THIS FILE AS hoteld_backup.php     **  -->

<!--  **  SALVA QUESTO FILE COME hoteld_backup.php  **  -->


<backup>
<versione>$versione_corrente</versione>
<log>$PHPR_LOG</log>
");
$utenti = esegui_query("select idutenti from $tableutenti order by idutenti");
#dump_testo("/dati_connessione.php",$file);
dump_testo("/lingua.php",$file);
dump_testo("/unit.php",$file);
dump_testo("/unit_single.php",$file);
dump_testo("/tema.php",$file);
dump_testo("/selectappartamenti.php",$file);
dump_testo("/versione.php",$file);
if (@is_file(C_DATI_PATH."/abilita_login")) dump_testo("/abilita_login",$file);
if (@is_file(C_DATI_PATH."/parole_sost.php")) dump_testo("/parole_sost.php",$file);
if (@is_file(C_DATI_PATH."/dati_interconnessioni.php")) dump_testo("/dati_interconnessioni.php",$file);
if (@is_file(C_DATI_PATH."/log_utenti.php")) dump_testo("/log_utenti.php",$file);
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_reg = risul_query($anni,$num1,'idanni');
if (!$anni_backup or ${'anno_sel'.$anno_reg}) {
for ($num2 = 0 ; $num2 < numlin_query($utenti) ; $num2++) {
$idutente_reg = risul_query($utenti,$num2,'idutenti');
if (@is_file(C_DATI_PATH."/selectperiodi$anno_reg.$idutente_reg.php")) dump_testo("/selectperiodi$anno_reg.$idutente_reg.php",$file);
if (@is_file(C_DATI_PATH."/selperiodimenu$anno_reg.$idutente_reg.php")) dump_testo("/selperiodimenu$anno_reg.$idutente_reg.php",$file);
} # fine for $num2
} # fine if (!$anni_backup or ${'anno_sel'.$anno_reg})
} # fine for $num1

fwrite($file,"<database>
");
dump_tabella("anni",$file,$cond_anni);
dump_tabella("appartamenti",$file);
dump_tabella("clienti",$file);
dump_tabella("relclienti",$file);
dump_tabella("personalizza",$file,$cond_personalizza);
dump_tabella("versioni",$file);
dump_tabella("utenti",$file);
dump_tabella("gruppi",$file);
dump_tabella("privilegi",$file,$cond_ic_e_priv);
dump_tabella("sessioni",$file);
dump_tabella("transazioni",$file);
dump_tabella("transazioniweb",$file);
dump_tabella("descrizioni",$file);
dump_tabella("nazioni",$file);
dump_tabella("regioni",$file);
dump_tabella("citta",$file);
dump_tabella("documentiid",$file);
dump_tabella("parentele",$file);
dump_tabella("relutenti",$file);
dump_tabella("relgruppi",$file);
dump_tabella("beniinventario",$file);
dump_tabella("magazzini",$file);
dump_tabella("relinventario",$file);
dump_tabella("casse",$file);
dump_tabella("contratti",$file);
dump_tabella("cache",$file);
dump_tabella("interconnessioni",$file,$cond_ic_e_priv);
dump_tabella("messaggi",$file);
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_reg = risul_query($anni,$num1,'idanni');
if (!$anni_backup or ${'anno_sel'.$anno_reg}) {
dump_tabella("prenota$anno_reg",$file);
dump_tabella("costiprenota$anno_reg",$file);
dump_tabella("rclientiprenota$anno_reg",$file);
dump_tabella("periodi$anno_reg",$file);
dump_tabella("ntariffe$anno_reg",$file);
dump_tabella("regole$anno_reg",$file);
dump_tabella("soldi$anno_reg",$file);
dump_tabella("costi$anno_reg",$file);
} # fine if (!$anni_backup or ${'anno_sel'.$anno_reg})
} # fine for $num1
fwrite($file,"</database>
");
echo "<br>";

if ($includi_modelli == "SI") {
include("./includes/templates/funzioni_modelli.php");
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/mdl_disponibilita.php")) dump_modello("mdl_disponibilita.php",$percorso_cartella_modello,$file);
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita","crea_modelli.php",$ini_lingua).".php";
if (@is_file("$percorso_cartella_modello/$nome_file")) dump_modello($nome_file,$percorso_cartella_modello,$file);
} # fine if ($ini_lingua != "." && $ini_lingua != "..")
} # fine while ($ini_lingua = readdir($lang_dir))
closedir($lang_dir);
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." && $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
if (@is_file("$percorso_cartella_modello/$nome_file")) dump_modello($nome_file,$percorso_cartella_modello,$file);
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name['en'];
if (@is_file("$percorso_cartella_modello/$nome_file")) dump_modello($nome_file,$percorso_cartella_modello,$file);
} # fine if ($ini_lingua != "." && $ini_lingua != "..")
} # fine while ($ini_lingua = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($modello_ext = readdir($templates_dir))
closedir($templates_dir);
} # fine for $num_cart
} # fine if ($includi_modelli == "SI")

fwrite($file,"</backup>");
flock($file,3);
fclose($file);
@chmod(C_DATI_PATH."/hoteld_backup.php", 0640);
echo mex("File creato",$pag).".<br>
<!-- Backup_created -->";
} # fine if ($file)
else echo mex("Non ho il permesso di scrittura sul file",$pag).".<br>";
unlock_tabelle($tabelle_lock);
} # fine else if ($raggiunto_limite)
} # fine if ($backup_contratti != "SI")


else {
$tabelle_lock = array($PHPR_TAB_PRE."contratti");
$altre_tab_lock = array($PHPR_TAB_PRE."versioni",$PHPR_TAB_PRE."personalizza");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$nomi_contratti = esegui_query("select valpersonalizza from $PHPR_TAB_PRE"."personalizza where idpersonalizza = 'nomi_contratti' and idutente = '1'");
$nomi_contratti = risul_query($nomi_contratti,0,'valpersonalizza');
esegui_query("insert into $PHPR_TAB_PRE"."contratti (numero,tipo,testo) values ('1','nomi_con','".aggslashdb($nomi_contratti)."')");
$file = @fopen(C_DATI_PATH."/hoteld_doc_backup.php","w");
if ($file) {
flock($file,2);
$versione_corrente = esegui_query("select * from $tableversioni where idversioni = 1");
$versione_corrente = risul_query($versione_corrente,0,'num_versione');
$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
fwrite($file,"<?php exit(); ?>

<!--             $adesso             -->

<!--  **    SAVE THIS FILE AS hoteld_doc_backup.php     **  -->

<!--  **  SALVA QUESTO FILE COME hoteld_doc_backup.php  **  -->


<backup>
<versione>$versione_corrente</versione>
");
fwrite($file,"<database>
");
dump_tabella("contratti",$file,"",1);
echo "<br>";
fwrite($file,"</database>
</backup>");
flock($file,3);
fclose($file);
@chmod(C_DATI_PATH."/hoteld_doc_backup.php", 0640);
esegui_query("delete from $PHPR_TAB_PRE"."contratti where numero = '1' and tipo = 'nomi_con' ");
unlock_tabelle($tabelle_lock);
echo mex("File creato",$pag).".<br>";
} # fine if ($file)
else echo mex("Non ho il permesso di scrittura sul file",$pag)." dati/hoteld_doc_backup.php.<br>";
} # fine else if ($backup_contratti != "SI")
} # fine if ($crea_backup)



if ($upload_backup and $id_utente == 1) {
$errore = "NO";
if ($backup_contratti != "SI") {
$file_tmp = C_DATI_PATH."/hoteld_backup.php.tmp";
$file_backup = C_DATI_PATH."/hoteld_backup.php";
} # fine if ($backup_contratti != "SI")
else {
$file_tmp = C_DATI_PATH."/hoteld_doc_backup.php.tmp";
$file_backup = C_DATI_PATH."/hoteld_doc_backup.php";
} # fine else if ($backup_contratti != "SI")
if (!$file_backup_upload) {
if ($HTTP_POST_FILES['file_backup_upload']['tmp_name']) $file_backup_upload = $HTTP_POST_FILES['file_backup_upload']['tmp_name'];
else if ($_FILES['file_backup_upload']['tmp_name']) $file_backup_upload = $_FILES['file_backup_upload']['tmp_name'];
} # fine if (!$file_backup_upload)
if (!move_uploaded_file($file_backup_upload,$file_tmp)) $errore = "SI";
if ($errore == "NO") {
if (defined("C_MASSIMO_NUM_BYTE_UPLOAD") and C_MASSIMO_NUM_BYTE_UPLOAD != 0 and filesize($file_tmp) > C_MASSIMO_NUM_BYTE_UPLOAD) $errore = "SI";
if ($compresso == "SI") {
if (rename($file_tmp,"$file_tmp.gz")) {
if ($cfp = gzopen("$file_tmp.gz","r") and $fileaperto = fopen($file_tmp,"w")) {
flock($fileaperto,2);
while (!feof($cfp)) {
$linee = gzread($cfp,524288);
fwrite($fileaperto,$linee);
} # fine while (!feof($cfp))
flock($fileaperto,3);
fclose($fileaperto);
gzclose($cfp);
} # fine if ($cfp = gzopen("$file_tmp.gz","r") and...
unlink("$file_tmp.gz");
} # fine if (rename($file_tmp,"$file_tmp.gz"))
} # fine if ($compresso == "SI")
unset($linee_file);
$fileaperto = fopen($file_tmp,"r");
$linee_file = trim(fread($fileaperto,25));
fclose($fileaperto);
if (substr($linee_file,0,16) != "<?php exit(); ?>" or $errore != "NO") {
echo mex("Il contenuto del file inviato non è corretto",$pag).".<br>";
$errore = "SI";
unlink($file_tmp);
} # fine if (substr($linee_file,0,16) != "<\?php exit(); ?\>" or...
else if (!rename($file_tmp,$file_backup)) $errore = "SI";
} # fine if ($errore == "NO")
if ($errore == "NO") echo mex("Ho fatto l'upload del file",$pag)." $file_backup.<br>";
else echo mex("Non ho potuto fare l'upload del file",$pag).".<br>";
} # fine if ($upload_backup and $id_utente == 1)



if ($salva_backup) {
$tasto_torna_indietro = "NO";
if ($backup_contratti != "SI") {
$nome_file = "hoteld_backup.php";
$nome_file_compresso = "hoteld_backup.php.gz";
if ($id_utente == "b") $file = C_DATI_PATH."/backup_ext.php";
else $file = C_DATI_PATH."/hoteld_backup.php";
} # fine if ($backup_contratti != "SI")
else {
$nome_file = "hoteld_doc_backup.php";
$nome_file_compresso = "hoteld_doc_backup.php.gz";
$file = C_DATI_PATH."/hoteld_doc_backup.php";
} # fine else if ($backup_contratti != "SI")
$filelock = @crea_lock_file($file);
if ($filelock) {
if ($compresso == "SI") {
mt_srand((float) $sec + ((float) $usec * 100000));
$file_compresso = C_DATI_PATH."/backup".mt_rand(10000,99999).".php.gz";
$cfp = gzopen($file_compresso,"wb9");
$fbackup = fopen($file,"r");
if ($fbackup) {
while (!feof($fbackup)) {
$linee = fread($fbackup,524288);
gzwrite($cfp,$linee);
} # fine while (!feof($fbackup))
fclose ($fbackup);
} # fine if ($fbackup)
gzclose($cfp);
$file = $file_compresso;
$nome_file = $nome_file_compresso;
} # fine if ($compresso == "SI")
$lunghezza_file = (int) filesize($file);
header("Pragma: public");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0");
header("Content-Transfer-Encoding: none");
if ($compresso == "SI") header("Content-Type: application/x-gzip; name=\"$nome_file\"");
else {
header("Content-Type: application/octetstream; name=\"$nome_file\"");
header("Content-Type: application/octet-stream; name=\"$nome_file\"");
} # fine else if ($compresso == "SI")
header("Content-Disposition: inline; filename=\"$nome_file\"");
header("Content-length: $lunghezza_file");
$fbackup = fopen($file,"r");
if ($fbackup) {
while (!feof($fbackup)) {
$linee = fread($fbackup,524288);
echo $linee;
} # fine while (!feof($fbackup))
fclose ($fbackup);
} # fine if ($fbackup)
if ($compresso == "SI") unlink($file_compresso);
distruggi_lock_file($filelock,$file);
if ($id_utente == "b") unlink(C_DATI_PATH."/backup_ext.php");
} # fine if ($filelock)
} # fine if ($salva_backup)



if ($cancella_backup) {
if ($backup_contratti != "SI") $file_backup = C_DATI_PATH."/hoteld_backup.php";
else $file_backup = C_DATI_PATH."/hoteld_doc_backup.php";
if (!@unlink($file_backup)) echo mex("Si è verificato un errore cancellando il file",$pag).".<br>";
else echo mex("File cancellato",$pag).".<br>";
} # fine if ($cancella_backup)



if ($copia_def_backup and $backup_contratti == "SI" and $id_utente == 1) {
$file_backup = C_DATI_PATH."/hoteld_doc_backup.php";
if (($lingua != "ita" and !is_dir("./includes/lang/$lingua")) or strlen($lingua) > 3 or str_replace("/","",$lingua) != $lingua) $lingua = "en";
if ($lingua != "ita" and !is_dir("./includes/lang/$lingua")) $lingua = "ita";
if ($lingua == "ita") $file_copia = "./includes/hoteld_doc_backup.php";
else $file_copia = "./includes/lang/$lingua/hoteld_doc_backup.php";
if (defined("C_CARTELLA_FILES_REALI")) $file_copia = C_CARTELLA_FILES_REALI.$file_copia;
if (!@copy($file_copia,$file_backup)) echo ucfirst(mex("si è verificato un errore copiando il file",$pag)).".<br>";
else echo ucfirst(mex("file copiato",$pag)).".<br>";
} # fine if ($copia_def_backup and $backup_contratti == "SI" and $id_utente == 1)



#if ($guarda_backup) {
#if ($linee_backup = @file(C_DATI_PATH."/hoteld_backup.php")) {
#for ($num1 = 0 ; $num1 < count($linee_backup) ; $num1++) {
#echo $linee_backup[$num1];
#} # fine ($num1 = 0 ; $num1 < $num_anni ; $num1++)
#} # fine if ($linee_backup = @file(C_DATI_PATH."/hoteld_backup.php"))
#else {
#echo mex("Non ho potuto leggere il file",$pag).".<br>";
#$tasto_torna_indietro = "SI";
#} # fine else if ($linee_backup = @file(C_DATI_PATH."/hoteld_backup.php"))
#} # fine if ($guarda_backup)



if ($ripristina_backup and $id_utente == 1) {
if ($backup_contratti != "SI") {
if (defined('C_BACKUP_E_MODELLI_CON_NUOVI_DATI') and C_BACKUP_E_MODELLI_CON_NUOVI_DATI == "NO") $dati_conn = "attuali";
$file = C_DATI_PATH."/hoteld_backup.php";
if (@is_file($file)) {
if ($fbackup = fopen($file,"r")) {
$versione_corrente = esegui_query("select * from $tableversioni where idversioni = 1");
$versione_corrente = risul_query($versione_corrente,0,'num_versione');
while (!feof($fbackup)) {
$linea = fgets($fbackup,524288);
$linea = togli_acapo($linea);
if (substr($linea,0,10) == "<versione>") {
$versione_file = substr($linea,10);
$versione_file = substr($versione_file,0,-11);
break;
} # fine if (substr($linea,0,10) == "<versione>")
} # fine while (!feof($fbackup))
fclose($fbackup);
if ($prova = @fopen(C_DATI_PATH."/prova","w")) {
fclose($prova);
@unlink(C_DATI_PATH."/prova");
} # fine if ($prova = @fopen(C_DATI_PATH."/prova","w"))
else $dati_scrivibile = "NO";
if ($versione_file and $versione_file == $versione_corrente) {
if ($dati_scrivibile != "NO") {

if ($continua != "SI") {
echo "<br><big>".mex("<b style=\"color: red;\">ATTENZIONE</b>: premendo su <b>\"<i>Continua</i>\"</b>, prima di ripristinare i dati dal <i>file</i>, tutti i dati del <i>database attuale</i> verranno <b>cancellati</b>",$pag)."!</big><br><br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"ripristina_backup\" value=\"$ripristina_backup\">
<input type=\"hidden\" name=\"mantieni_anni\" value=\"$mantieni_anni\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">";

if ($dati_conn == "attuali") echo "<input type=\"hidden\" name=\"dati_conn\" value=\"attuali\">";

if ($dati_conn == "nuovi") {

$HOTELD_DB_TYPE = "";
$HOTELD_DB_NAME = "";
$HOTELD_DB_HOST = "";
$HOTELD_DB_PORT = "";
$HOTELD_DB_USER = "";
$HOTELD_DB_PASS = "";
$HOTELD_TAB_PRE = "";
if (defined('C_EXT_DB_DATA_PATH') and C_EXT_DB_DATA_PATH) include(C_EXT_DB_DATA_PATH);

echo "".mex("Inserisci i nuovi dati per la connessione al database",$pag).".<br><br>";
if (!$HOTELD_DB_TYPE) echo "".mex("Tipo di database",$pag).": 
<select name=\"N_PHPR_DB_TYPE\">
<option value=\"postgresql\" selected>".mex("Postgresql",$pag)."</option>
<option value=\"mysql\">".mex("Mysql",$pag)."</option>
<option value=\"sqlite\">".mex("Sqlite",$pag)."</option>
</select><br>";
if (!$HOTELD_DB_NAME) echo "".mex("Nome del database da utilizzare",$pag).": 
<input type=\"text\" name=\"N_PHPR_DB_NAME\"><br>";
echo "".mex("Database già esistente",$pag)."?
<select name=\"database_esistente\">
<option value=\"SI\">".mex("Si",$pag)."</option>
<option value=\"NO\" selected>".mex("No",$pag)."</option></select><small>
(".mex("Se già esistente e non vuoto usare un prefisso non presente nel database per il nome delle tabelle",$pag).")</small><br>";
if (!$HOTELD_DB_HOST) echo "".mex("Nome del computer a cui collegarsi",$pag).":
<input type=\"text\" name=\"N_PHPR_DB_HOST\" value=\"localhost\"><br>";
if (!strcmp($HOTELD_DB_PORT,"")) echo "".mex("Numero della porta a cui collegarsi",$pag).": 
<input type=\"text\" name=\"N_PHPR_DB_PORT\" value=\"5432\">(".mex("Normalmete 5432 o 5433 per Postgresql o 3306 per Mysql",$pag).")<br>";
if (!$HOTELD_DB_USER) echo "".mex("Nome per l'autenticazione al database",$pag).": 
<input type=\"text\" name=\"N_PHPR_DB_USER\"><br>";
if (!strcmp($HOTELD_DB_PASS,"")) echo "".mex("Parola segreta per l'autenticazione al database",$pag).": 
<input type=\"text\" name=\"N_PHPR_DB_PASS\"><br>";
echo "".mex("Caricare la libreria dinamica \"pgsql.so\" o \"mysql.so\"",$pag)."?
<select name=\"N_PHPR_LOAD_EXT\">
<option value=\"SI\">".mex("Si",$pag)."</option>
<option value=\"NO\" selected>".mex("No",$pag)."</option>
</select> <small>(".mex("scegliere si se non viene caricata automaticamente da php",$pag).")</small><br>
".mex("Nome del database a cui collegarsi temporaneamente",$pag).":
<input type=\"text\" name=\"tempdatabase\" value=\"template1\"><small>
(".mex("solo per Postgresql con database non esistente",$pag).")</small><br>";
if (!$HOTELD_TAB_PRE) echo "".mex("Prefisso nel nome delle tabelle",$pag).":
<input type=\"text\" name=\"N_PHPR_TAB_PRE\" maxlength=\"8\" size=\"9\"><small>
(".mex("opzionale, utile per più installazioni di HotelDruid nello stesso database",$pag).")</small><br><br>";
} # fine if ($dati_conn == "nuovi")

echo "<div style=\"text-align: center;\">
<button class=\"rbkp\" type=\"submit\"><div>".mex("Continua",$pag)."</div></button>
</div></div></form><br>";

} # fine if ($continua != "SI")

if ($dati_conn == "attuali") {
$N_PHPR_DB_TYPE = $PHPR_DB_TYPE;
$N_PHPR_DB_NAME = $PHPR_DB_NAME;
$N_PHPR_DB_HOST = $PHPR_DB_HOST;
$N_PHPR_DB_PORT = $PHPR_DB_PORT;
$N_PHPR_DB_USER = $PHPR_DB_USER;
$N_PHPR_DB_PASS = $PHPR_DB_PASS;
$N_PHPR_LOAD_EXT = $PHPR_LOAD_EXT;
$N_PHPR_TAB_PRE = $PHPR_TAB_PRE;
} # fine if ($dati_conn == "attuali")

if ($continua == "SI" and ((!defined('C_RESTRIZIONI_DEMO_ADMIN') or C_RESTRIZIONI_DEMO_ADMIN != "SI") or (defined('C_PASS_DEMO_ADMIN') and C_PASS_DEMO_ADMIN == $pass_demo_admin))) ripristina_backup($file,"NO",$pag,$numconnessione,$database_esistente,$tempdatabase,$PHPR_DB_TYPE,$PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT,$PHPR_TAB_PRE,$N_PHPR_DB_TYPE,$N_PHPR_DB_NAME,$N_PHPR_DB_HOST,$N_PHPR_DB_PORT,$N_PHPR_DB_USER,$N_PHPR_DB_PASS,$N_PHPR_LOAD_EXT,$N_PHPR_TAB_PRE,$ext_pgsql_caricata,$ext_mysql_caricata,$mantieni_anni);

} # fine if ($dati_scrivibile != "NO")
else echo mex("Non ho i permessi di scrittura sulla cartella dati",$pag).".<br>";
} # fine if ($versione_file and $versione_file == $versione_corrente)
else echo mex("La versione attuale di HotelDruid e quella del file non coincidono",$pag).".<br>";
} # fine if ($fbackup = fopen($file,"r"))
else echo mex("Non ho potuto leggere il file",$pag).".<br>";
} # fine if (@is_file($file))
else echo mex("Non ho potuto leggere il file",$pag).".<br>";
} # fine if ($backup_contratti != "SI")


else {
if (@is_file(C_DATI_PATH."/hoteld_doc_backup.php")) {
if ($linee_backup = file(C_DATI_PATH."/hoteld_doc_backup.php")) {

if (!defined('C_RESTRIZIONI_DEMO_ADMIN') or C_RESTRIZIONI_DEMO_ADMIN != "SI") ripristina_backup_contr($linee_backup,"NO",$pag,$PHPR_TAB_PRE,$modalita,$contr_agg);

} # fine if ($linee_backup = file(C_DATI_PATH."/hoteld_doc_backup.php"))
else echo mex("Non ho potuto leggere il file",$pag).".<br>";
} # fine if (@is_file(C_DATI_PATH."/hoteld_doc_backup.php"))
else echo mex("Non ho potuto leggere il file",$pag).".<br>";

} # fine else if ($backup_contratti != "SI")
} # fine if ($ripristina_backup and $id_utente == 1)



if ($tasto_torna_indietro != "NO") {
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
if ($backup_contratti == "SI") echo "<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">";
echo "<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form></div>";
} # fine if ($tasto_torna_indietro != "NO")


} # fine if ($azione == "SI")


elseif ($anno_utente_attivato == "SI") {



if ($backup_contratti == "SI") {
echo "<h3 id=\"h_dcbk\"><span>".mex("Backup dei documenti",$pag).".</span></h3>
<hr style=\"width: 95%\">";

if (@is_file(C_DATI_PATH."/hoteld_doc_backup.php")) $file_esistente = "SI";
else $file_esistente = "NO";

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"abkp\" type=\"submit\" name=\"crea_backup\" value=\"1\"><div>";
if ($file_esistente == "NO") echo mex("Crea il file di backup dei documenti",$pag);
else echo mex("Crea un nuovo file di backup dei documenti",$pag);
echo "</div></button>";
if ($file_esistente == "SI") {
$fbackup = fopen(C_DATI_PATH."/hoteld_doc_backup.php","r");
$data_creazione = fread($fbackup,200);
fclose ($fbackup);
$data_creazione = explode("<!--",$data_creazione);
$data_creazione = explode("-->",$data_creazione[1]);
$data_creazione = formatta_data(trim($data_creazione[0]),$stile_data);
$data_creazione = str_replace(" "," ".mex("alle",$pag)." ",$data_creazione);
echo " (".mex("sovrascrivendo l'attuale",$pag)." ".mex("creato il",$pag)." $data_creazione)";
} # fine if ($file_esistente == "SI")
echo ".</div></form><table><tr><td style=\"height: 8px;\"></td></tr></table>";
if ($file_esistente == "SI") {
#echo "<a href=\"./crea_backup.php?azione=SI&tasto_torna_indietro=NO&anno=$anno&guarda_backup=SI\">".mex("Guarda il file dati/hoteld_doc_backup.php</a> per salvarlo (usa il bottone indietro del browser per tornare qui)",$pag).".<br>";
#echo "<a href=\C_DATI_PATH."/backup.txt\">".mex("Guarda il file dati/backup.txt</a> per salvarlo (usa il bottone indietro del browser per tornare qui)",$pag).".<br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"mostra_header\" value=\"NO\">
<button class=\"dbkp\" type=\"submit\" name=\"salva_backup\" value=\"1\"><div>".mex("Guarda il file",$pag)."</div></button>
 dati/hoteld_doc_backup.php ".mex("per salvarlo (eventualmente usa il bottone indietro del browser per tornare qui)",$pag).".<br><table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"compresso\" value=\"SI\" checked> ".mex("Compresso",$pag)."</label>
</td></tr></table></div></form><table><tr><td style=\"height: 4px;\"></td></tr></table>";
#if ($id_utente == 1) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"cbkp\" type=\"submit\" name=\"cancella_backup\" value=\"1\"><div>".mex("Cancella",$pag)."</div></button>
".mex(" l'attuale file di backup dei documenti",$pag).".
</div></form><table><tr><td style=\"height: 4px;\"></td></tr></table>";
#} # fine if ($id_utente == 1)
} # fine if ($file_esistente == "SI")

if ($id_utente == 1) {
echo "<hr style=\"width: 95%\"><table><tr><td style=\"height: 4px;\"></td></tr></table>
<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"ubkp\" type=\"submit\" name=\"copia_def_backup\" value=\"1\"><div>".ucfirst(mex("copia",$pag))."</div></button>
 ".mex("il file di backup dai documenti predefiniti in",$pag)."
 <select name=\"lingua\">
<option value=\"ita\">italiano</option>";
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$file_doc_backup = "./includes/lang/$ini_lingua/hoteld_doc_backup.php";
if (defined("C_CARTELLA_FILES_REALI")) $file_doc_backup = C_CARTELLA_FILES_REALI.$file_doc_backup;
if (@is_file($file_doc_backup)) {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
if ($ini_lingua == $lingua_mex) $selected = " selected";
else $selected = "";
echo "<option value=\"$ini_lingua\"$selected>$nome_lingua</option>";
} # fine if (@is_file($file_doc_backup))
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "</select>";
if ($file_esistente == "SI") echo " (".mex("sovrascrivendo l'attuale",$pag).")";
echo ".<br></div></form><table><tr><td style=\"height: 8px;\"></td></tr></table>
<form accept-charset=\"utf-8\" enctype=\"multipart/form-data\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"20000000\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"ubkp\" type=\"submit\" name=\"upload_backup\" value=\"1\"><div>".mex("Fai l'upload",$pag)."</div></button>";
if ($file_esistente == "NO") echo mex(" del file di backup dei documenti",$pag);
else echo mex(" di un nuovo file di backup dei documenti",$pag);
echo ": <input name=\"file_backup_upload\" type=\"file\">";
if ($file_esistente == "SI") echo " (".mex("sovrascrivendo l'attuale",$pag).")";
echo ".<br><table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"compresso\" value=\"SI\" checked> ".mex("Compresso",$pag)."</label>
</td></tr></table></div></form><table><tr><td style=\"height: 4px;\"></td></tr></table>";
if ($file_esistente == "SI") {
$linee_backup = file(C_DATI_PATH."/hoteld_doc_backup.php");
$info_contr = ripristina_backup_contr($linee_backup,"SI",$pag,$PHPR_TAB_PRE,"info");
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"rdbk\" type=\"submit\" name=\"ripristina_backup\" value=\"1\"><div>".mex("Ripristina i documenti dal file",$pag)."</div></button><br>
<table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"radio\" name=\"modalita\" value=\"aggiungi\" checked>".mex("aggiungi ai documenti attuali",$pag)."</label>";
if ($info_contr['max_contr'] > 1) {
echo " <select name=\"contr_agg\">
<option value=\"\" selected>".mex("tutti i documenti contenuti nel file",$pag)."</option>";
unset($nomi_contratti);
$nomi_con = explode("#@&",$info_contr['nomi_con']);
$num_nomi_con = count($nomi_con);
for ($num1 = 0 ; $num1 < $num_nomi_con ; $num1++) {
$nome_con = explode("#?&",$nomi_con[$num1]);
$nomi_contratti[$nome_con[0]] = $nome_con[1];
} # fine for $num1
for ($num1 = 1 ; $num1 <= $info_contr['max_contr'] ; $num1++) {
echo "<option value=\"$num1\">".mex("solo il documento",$pag)." $num1";
if (strcmp($nomi_contratti[$num1],"")) echo " (".$nomi_contratti[$num1].")";
echo "</option>";
} # fine for $num1
echo "</select>";
} # fine if ($info_contr['max_contr'] > 1)
echo "<br><label><input type=\"radio\" name=\"modalita\" value=\"rimpiazza\">".mex("rimpiazza i documenti attuali",$pag)."</label> (".mex("i documenti attuali verranno cancellati",$pag).")<br>";
echo "</td></tr></table></div></form><table><tr><td style=\"height: 1px;\"></td></tr></table>";
} # fine if ($file_esistente == "SI")
} # fine if ($id_utente == 1)

echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"bkup\" type=\"submit\"><div>".mex("Backup completo",$pag)."</div></button>
</div></form><br>";
} # fine if ($backup_contratti == "SI")

else {



# Pagina iniziale di backup
if (defined('C_NASCONDI_MARCA') and C_NASCONDI_MARCA == "SI") echo "<h3 id=\"h_bkup\"><span>".mex("Sistema di backup","personalizza.php").".</span></h3>";
else echo "<h3 id=\"h_bkup\"><span>".mex("Sistema di backup per HotelDruid",$pag).".</span></h3>";
echo "<hr style=\"width: 95%\">";

if (@is_file(C_DATI_PATH."/hoteld_backup.php")) $file_esistente = "SI";
else $file_esistente = "NO";

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"abkp\" type=\"submit\" name=\"crea_backup\" value=\"1\"><div>";
if ($file_esistente == "NO") echo mex("Crea il file di backup",$pag);
else echo mex("Crea un nuovo file di backup",$pag);
echo "</div></button>";
if ($file_esistente == "SI") {
$fbackup = fopen(C_DATI_PATH."/hoteld_backup.php","r");
$data_creazione = fread($fbackup,200);
fclose($fbackup);
$data_creazione = explode("<!--",$data_creazione);
$data_creazione = explode("-->",$data_creazione[1]);
$data_creazione = formatta_data(trim($data_creazione[0]),$stile_data);
$data_creazione = str_replace(" "," ".mex("alle",$pag)." ",$data_creazione);
echo " (".mex("sovrascrivendo l'attuale",$pag)." ".mex("creato il",$pag)." $data_creazione)";
} # fine if ($file_esistente == "SI")
echo ".<br><table><tr><td style=\"width: 30px;\"></td><td>";
$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);
if ($num_anni > 1) {
echo "<div><label><input type=\"radio\" name=\"anni_backup\" value=\"\" checked>".mex("Includi tutti gli anni",$pag)."</label></div>
<div onclick=\"document.getElementById('ab_sel').checked='1'\"><label><input type=\"radio\" name=\"anni_backup\" id=\"ab_sel\" value=\"sel\">".mex("Includi solo gli anni selezionati",$pag)."</label>: ";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_corr = risul_query($anni,$num1,'idanni');
echo "<label><input type=\"checkbox\" name=\"anno_sel$anno_corr\" value=\"1\"> $anno_corr</label>";
if ($num1 != ($num_anni - 1)) echo "; ";
} # fine for $num1
echo "</div>";
} # fine if ($num_anni > 1)
echo "<label><input type=\"checkbox\" name=\"includi_modelli\" value=\"SI\" checked> ".mex("Includi i modelli internet",$pag)."</label>
</td></tr></table></div></form>
<table><tr><td style=\"height: 8px;\"></td></tr></table>";

if ($file_esistente == "SI") {
#echo "<a href=\"./crea_backup.php?azione=SI&tasto_torna_indietro=NO&anno=$anno&guarda_backup=SI\">".mex("Guarda il file dati/hoteld_backup.php</a> per salvarlo (usa il bottone indietro del browser per tornare qui)",$pag).".<br>";
#echo "<a href=\C_DATI_PATH."/backup.txt\">".mex("Guarda il file dati/backup.txt</a> per salvarlo (usa il bottone indietro del browser per tornare qui)",$pag).".<br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<input type=\"hidden\" name=\"mostra_header\" value=\"NO\">
<button class=\"dbkp\" type=\"submit\" name=\"salva_backup\" value=\"1\"><div>".mex("Guarda il file",$pag)."</div></button>
 dati/hoteld_backup.php ".mex("per salvarlo (eventualmente usa il bottone indietro del browser per tornare qui)",$pag).".<br>
<table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"compresso\" value=\"SI\" checked> ".mex("Compresso",$pag)."</label>
</td></tr></table></div></form><table><tr><td style=\"height: 4px;\"></td></tr></table>";
#if ($id_utente == 1) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"cbkp\" type=\"submit\" name=\"cancella_backup\" value=\"1\"><div>".mex("Cancella",$pag)."</div></button>
".mex(" l'attuale file di backup",$pag).".
</div></form><table><tr><td style=\"height: 4px;\"></td></tr></table>";
#} # fine if ($id_utente == 1)
} # fine if ($file_esistente == "SI")

if ($id_utente == 1) {
echo "<hr style=\"width: 95%\"><table><tr><td style=\"height: 4px;\"></td></tr></table>
<form accept-charset=\"utf-8\" enctype=\"multipart/form-data\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"900000000\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"ubkp\" type=\"submit\" name=\"upload_backup\" value=\"1\"><div>".mex("Fai l'upload",$pag)."</div></button>";
if ($file_esistente == "NO") echo mex(" del file di backup",$pag);
else echo mex(" di un nuovo file di backup",$pag);
echo ": <input name=\"file_backup_upload\" type=\"file\">";
if ($file_esistente == "SI") echo " (".mex("sovrascrivendo l'attuale",$pag).")";
echo ".<br><table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"compresso\" value=\"SI\" checked> ".mex("Compresso",$pag)."</label>
</td></tr></table></div></form><table><tr><td style=\"height: 4px;\"></td></tr></table>";
if ($file_esistente == "SI") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"azione\" value=\"SI\">
<button class=\"rbkp\" type=\"submit\" name=\"ripristina_backup\" value=\"1\"><div>".mex("Ripristina i dati dal file",$pag)."</div></button>
".mex(" (i dati attuali verranno cancellati), utilizzando per la connessione al database:",$pag)."<br>
<table><tr><td style=\"width: 30px;\"></td><td>
<div><label><input type=\"radio\" name=\"dati_conn\" value=\"attuali\" checked>".mex("i dati dell'attuale connessione",$pag)."</label></div>";
echo "<table><tr><td style=\"width: 30px;\"></td><td>
<label><input type=\"checkbox\" name=\"mantieni_anni\" value=\"1\">".mex("Prova a mantenere i dati degli anni non contenuti nel backup",$pag)."</label>
 <small>(".mex("alcuni dati di questi anni potrebbero comunque venir persi",$pag).")</small>
</td></tr></table>";
if (!defined('C_BACKUP_E_MODELLI_CON_NUOVI_DATI') or C_BACKUP_E_MODELLI_CON_NUOVI_DATI != "NO") echo "<div><label><input type=\"radio\" name=\"dati_conn\" value=\"nuovi\">".mex("nuovi dati",$pag)."</label></div>";
echo "</td></tr></table></div></form><table><tr><td style=\"height: 1px;\"></td></tr></table>";
} # fine if ($file_esistente == "SI")
} # fine if ($id_utente == 1)

echo "<hr style=\"width: 95%\"><div style=\"text-align: center;\">
<br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"./crea_backup.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"backup_contratti\" value=\"SI\">
<button class=\"dcbk\" type=\"submit\"><div>".mex("Backup dei documenti",$pag)."</div></button>
</div></form><br>";

} # fine else if ($backup_contratti == "SI")



$action = "personalizza.php";
if ($backup_contratti == "SI") $action .= "#contratti";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$action\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";



} # fine elseif ($anno_utente_attivato == "SI")




if ($mostra_header != "NO") {
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");
} # fine if ($mostra_header != "NO")


} # fine if ($priv_crea_backup == "s")
} # fine if ($id_utente)



?>
