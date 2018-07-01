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

$pag = "modifica_app.php";
$titolo = "HotelDruid: Modifica Appartamenti";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tabledescrizioni = $PHPR_TAB_PRE."descrizioni";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente and $id_utente == 1) {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");



if (!$url_enc) $idappartamenti = htmlspecialchars($idappartamenti);
$idappartamenti = aggslashdb($idappartamenti);

if ($cancella_app) {
$mostra_form_modifica = "NO";
$modificaappartamento = "";
$modificadescr = "";
$commentofoto = "";
$cancurlfoto = "";
$aggurlfoto = "";

$oggi = date("Y-m-d",(time() + (C_DIFF_ORE * 3600)));
$anno_attuale = date("Y",(time() + (C_DIFF_ORE * 3600)));

$anni_esistenti = esegui_query("select * from $tableanni order by idanni");
$num_anni_esistenti = numlin_query($anni_esistenti);
unset($tabelle_lock);
unset($altre_tab_lock);
$num_tab = 0;
$num_altre_tab = 0;
$tabelle_lock[$num_tab] = $tableanni;
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$tableprenota_lock = $PHPR_TAB_PRE."prenota".$anno_esistente;
$num_tab++;
$tabelle_lock[$num_tab] = $tableprenota_lock;
if ($anno_esistente >= $anno_attuale) {
$tableperiodi_lock = $PHPR_TAB_PRE."periodi".$anno_esistente;
$altre_tab_lock[$num_altre_tab] = $tableperiodi_lock;
$num_altre_tab++;
} # fine if ($anno_esistente >= $anno_attuale)
} # fine for $num1
$num_tab++;
$tabelle_lock[$num_tab] = $tableappartamenti;
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$tableregole_lock = $PHPR_TAB_PRE."regole".$anno_esistente;
$num_tab++;
$tabelle_lock[$num_tab] = $tableregole_lock;
} # fine for $num1
$num_tab++;
$tabelle_lock[$num_tab] = $tabledescrizioni;
$num_tab++;
$tabelle_lock[$num_tab] = $tablepersonalizza;
$num_tab++;
$tabelle_lock[$num_tab] = $tablerelinventario;
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$anni_esistenti2 = esegui_query("select * from $tableanni order by idanni");
$num_anni_esistenti2 = numlin_query($anni_esistenti2);
if ($num_anni_esistenti != $num_anni_esistenti2) $cancellare = "NO";
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$anno_esistente2 = risul_query($anni_esistenti2,$num1,'idanni');
if ($anno_esistente != $anno_esistente2) $cancellare = "NO";
} # fine for $num1
if ($cancellare == "NO") echo mex("<b>L'appartamento non è stato cancellato</b> perchè il database è cambiato nel frattempo",'unit.php').".<br>";

$anni_da_controllare = esegui_query("select * from $tableanni where idanni >= $anno_attuale");
$num_anni_da_controllare = numlin_query($anni_da_controllare);
include("./includes/liberasettimane.php");
unset($app_richiesti);
$app_richiesti[$idappartamenti] = "SI";
for ($num1 = 0 ;$num1 < $num_anni_da_controllare ; $num1++) {
$anno_controlla = risul_query($anni_da_controllare,$num1,'idanni');
$tableprenota_controlla = $PHPR_TAB_PRE."prenota".$anno_controlla;
$tableperiodi_controlla = $PHPR_TAB_PRE."periodi".$anno_controlla;
unset($limiti_var);
unset($profondita);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($app_liberato);
if ($anno_controlla == $anno_attuale) $min_periodo = (calcola_id_periodo_corrente($anno_attuale) + 1);
else $min_periodo = 1;
$max_periodo = esegui_query("select max(idperiodi) from $tableperiodi_controlla");
$max_periodo = risul_query($max_periodo,0,0);
liberasettimane ($min_periodo,$max_periodo,$limiti_var,$anno_controlla,$fatto_libera,$app_liberato,$profondita,$app_richiesti,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");
$prenota_presenti = esegui_query("select * from $tableprenota_controlla where idappartamenti = '$idappartamenti' and iddatainizio >= '$min_periodo'");
$num_prenota_presenti = numlin_query($prenota_presenti);
if ($num_prenota_presenti != 0) {
$cancellare = "NO";
echo mex("L'appartamento",'unit.php')." $idappartamenti ".mex("contiene prenotazione future, non si può cancellare",$pag).".<br>";
break;
} # fine if ($num_prenota_presenti != 0)
} # fine for $num1

$app_esiste = esegui_query("select * from $tableappartamenti where idappartamenti = '$idappartamenti'");
$num_app_esiste = numlin_query($app_esiste);
if ($num_app_esiste != 1) {
echo mex("L'appartamento",'unit.php')." $idappartamenti ".mex("è già stato cancellato",'unit.php').".<br>";
$cancellare = "NO";
} # fine if ($num_app_esiste != 1)

if ($cancellare != "NO") {
if ($cancella_sicuro != "SI") {
echo mex("Sei sicuro di voler <b>cancellare</b> l'appartamento",'unit.php')." <b>$idappartamenti</b>?<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_app.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"$idappartamenti\">
<input type=\"hidden\" name=\"cancella_sicuro\" value=\"SI\">
<button class=\"croo\" type=\"submit\" name=\"cancella_app\" value=\"".mex("SI",$pag)."\"><div>".mex("SI",$pag)."</div></button>
<button class=\"gobk\" type=\"submit\" name=\"non_cancellare\" value=\"".mex("NO",$pag)."\"><div>".mex("NO",$pag)."</div></button>
<br></div></form>";
} # fine if ($cancella_sicuro != "SI")

else {
$d_app_vicini = esegui_query("select app_vicini from $tableappartamenti where idappartamenti = '$idappartamenti' ");
$d_app_vicini = risul_query($d_app_vicini,0,'app_vicini');
$d_app_vicini_vett = explode(",",$d_app_vicini);
for ($num2 = 0 ; $num2 < count($d_app_vicini_vett) ; $num2++) {
$d_app_vicino = $d_app_vicini_vett[$num2];
$app_reciprici = esegui_query("select * from $tableappartamenti where idappartamenti = '".aggslashdb($d_app_vicino)."' ");
if (numlin_query($app_reciprici) == 1) {
$app_reciprici = risul_query($app_reciprici,0,'app_vicini');
$app_reciprici = substr(str_replace(",".$idappartamenti.",",",",",".$app_reciprici.","),1,-1);
esegui_query("update $tableappartamenti set app_vicini = '".aggslashdb($app_reciprici)."' where idappartamenti = '".aggslashdb($d_app_vicino)."' ");
} # fine if (numlin_query($app_reciprici) == 1)
} # fine for $num2
esegui_query("delete from $tableappartamenti where idappartamenti = '$idappartamenti'");
esegui_query("delete from $tablerelinventario where idappartamento = '$idappartamenti'");
esegui_query("delete from $tabledescrizioni where nome = '$idappartamenti' and (tipo = 'appdescr' or tipo = 'appfoto') ");
$id_appartamenti = esegui_query("select idappartamenti from $tableappartamenti order by idappartamenti ");
$num_appartamenti = numlin_query($id_appartamenti);
$fileaperto = fopen(C_DATI_PATH."/selectappartamenti.php","w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php \necho \"\n");
for ( $num = 0; $num < $num_appartamenti; $num = $num + 1) {
$numapp = risul_query($id_appartamenti,$num,'idappartamenti');
fwrite($fileaperto,"<option value=\\\"$numapp\\\">$numapp</option>
");
} # fine for $num
fwrite($fileaperto,"\"; \n?>");
flock($fileaperto,3);
fclose($fileaperto);

# Cancello l'appartamento dalle regole 2
for ($num_a = 0 ;$num_a < $num_anni_esistenti ; $num_a++) {
$anno_esistente = risul_query($anni_esistenti,$num_a,'idanni');
$tableregole = $PHPR_TAB_PRE."regole".$anno_esistente;
$regole2 = esegui_query("select * from $tableregole where tariffa_per_app != ''");
$num_regole2 = numlin_query($regole2);
for ($num1 = 0 ; $num1 < $num_regole2 ; $num1++) {
$idregole = risul_query($regole2,$num1,'idregole');
$lista_app = ",".risul_query($regole2,$num1,'motivazione').",";
if (str_replace(",$idappartamenti,","",$lista_app) != $lista_app) {
$lista_app = substr(str_replace(",$idappartamenti,",",",$lista_app),1,-1);
esegui_query("update $tableregole set motivazione = '".aggslashdb($lista_app)."' where idregole = '$idregole' ");
} # fine (str_replace(",$idappartamenti,","",$lista_app) != $lista_app)
$lista_app2 = ",".risul_query($regole2,$num1,'motivazione2').",";
if (str_replace(",$idappartamenti,","",$lista_app2) != $lista_app2) {
$lista_app2 = substr(str_replace(",$idappartamenti,",",",$lista_app2),1,-1);
esegui_query("update $tableregole set motivazione2 = '".aggslashdb($lista_app2)."' where idregole = '$idregole' ");
} # fine (str_replace(",$idappartamenti,","",$lista_app2) != $lista_app2)
} # fine for $num1
} # fine for $num_a

echo mex("L'appartamento",'unit.php')." <b>$idappartamenti</b> ".mex("è stato cancellato",'unit.php').".<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"appartamenti\">
<button class=\"cont\" type=\"submit\"><div>OK</div></button>
</div></form>";

$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unlock_tabelle($tabelle_lock);
unset($tabelle_lock);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($ic_present[$interconnection_name] == "SI") {
include("./includes/interconnect/$mod_ext/functions.php");
$funz_update_availability = "update_availability_".$interconnection_name;
$funz_update_availability($file_interconnessioni,$anno,$PHPR_TAB_PRE,1);
} # fine if ($ic_present[$interconnection_name] == "SI")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))

} # fine else if ($cancella_sicuro != "SI")
} # fine if ($cancellare != "NO")

else {
echo "
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_app.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"$idappartamenti\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
<br></div></form>";
} # fine else if ($cancellare != "NO")

if ($tabelle_lock) unlock_tabelle($tabelle_lock);
} # fine if ($cancella_app)



if ($modificaappartamento) {
$mostra_form_modifica = "NO";
$modificadescr = "";
$commentofoto = "";
$cancurlfoto = "";
$aggurlfoto = "";

if (!$num_app_modifica or controlla_num_pos($num_app_modifica) != "SI") $num_app_modifica = 1;

unset($cambia_nome_app);
for ($num1 = 0 ; $num1 < $num_app_modifica ; $num1++) {
if (${"num_unita_app".$num1} and strcmp(${"nome_unita_app".$num1},"")) {
$n_nome_app_unita = ${"n_nome_app".$num1};
$n_numc_app_unita = ${"n_numcasa".$num1};
$n_nump_app_unita = ${"n_numpiano".$num1};
if (($num1 + ${"num_unita_app".$num1}) <= $num_app_modifica) $num_fine_app = $num1 + ${"num_unita_app".$num1};
else $num_fine_app = $num_app_modifica;
for ($num2 = $num1 ; $num2 < $num_fine_app ; $num2++) {
if (substr(${"idappartamenti".$num2},0,strlen(${"nome_unita_app".$num1})) == ${"nome_unita_app".$num1}) {
${"n_nome_app".$num2} = $n_nome_app_unita.substr(${"idappartamenti".$num2},strlen(${"nome_unita_app".$num1}));
${"n_numcasa".$num2} = $n_numc_app_unita;
${"n_numpiano".$num2} = $n_nump_app_unita;
} # fine if (substr(${"idappartamenti".$num1},0,strlen(${"nome_unita_app".$num1})) == ${"nome_unita_app".$num1})
} # fine for $num2
} # fine if (${"num_unita_app".$num1} and strcmp(${"nome_unita_app".$num1},""))
${"idappartamenti".$num1} = htmlspecialchars(${"idappartamenti".$num1});
${"idappartamenti".$num1} = aggslashdb(${"idappartamenti".$num1});
if (get_magic_quotes_gpc()) {
${"n_commento".$num1} = stripslashes(${"n_commento".$num1});
${"d_commento".$num1} = stripslashes(${"d_commento".$num1});
} # fine if (get_magic_quotes_gpc())
${"n_nome_app".$num1} = str_replace(",","",${"n_nome_app".$num1});
if (str_replace (" ","",${"n_nome_app".$num1}) == "") ${"n_nome_app".$num1} = str_replace(" ","_",${"n_nome_app".$num1});
${"n_nome_app".$num1} = trim(${"n_nome_app".$num1});
${"n_nome_app".$num1} = elimina_caratteri_slash(${"n_nome_app".$num1});
${"n_nome_app".$num1} = htmlspecialchars(${"n_nome_app".$num1});
${"n_numcasa".$num1} = elimina_caratteri_slash(${"n_numcasa".$num1});
${"n_numcasa".$num1} = htmlspecialchars(${"n_numcasa".$num1});
${"n_numpiano".$num1} = elimina_caratteri_slash(${"n_numpiano".$num1});
${"n_numpiano".$num1} = htmlspecialchars(${"n_numpiano".$num1});
${"n_maxoccupanti".$num1} = elimina_caratteri_slash(${"n_maxoccupanti".$num1});
${"n_priorita".$num1} = elimina_caratteri_slash(${"n_priorita".$num1});
${"n_app_vicini".$num1} = elimina_caratteri_slash(${"n_app_vicini".$num1});
${"n_app_vicini".$num1} = htmlspecialchars(${"n_app_vicini".$num1});
if (controlla_num(${"n_maxoccupanti".$num1}) != "SI") unset(${"n_maxoccupanti".$num1});
if (controlla_num(${"n_priorita".$num1}) != "SI") unset(${"n_priorita".$num1});
${"n_commento".$num1} = htmlspecialchars(${"n_commento".$num1});
${"d_numcasa".$num1} = htmlspecialchars(${"d_numcasa".$num1});
${"d_numpiano".$num1} = htmlspecialchars(${"d_numpiano".$num1});
${"d_commento".$num1} = htmlspecialchars(${"d_commento".$num1});
if (($form_tabella and strcmp(${"idappartamenti".$num1},${"n_nome_app".$num1})) or (!$form_tabella and ${"n_nome_app".$num1})) $cambia_nome_app = "SI";
} # fine for $num1

$anni_esistenti = esegui_query("select * from $tableanni order by idanni");
$num_anni_esistenti = numlin_query($anni_esistenti);
unset($tabelle_lock);
unset($altre_tab_lock);
$num_tab = 0;
$num_altre_tab = 0;
if ($cambia_nome_app and $modificaappartamento == "Continua") {
$tabelle_lock[$num_tab] = $tableanni;
$num_tab++;
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$tableprenota_lock = $PHPR_TAB_PRE."prenota".$anno_esistente;
$tabelle_lock[$num_tab] = $tableprenota_lock;
$num_tab++;
} # fine for $num1
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$tablecostiprenota_lock = $PHPR_TAB_PRE."costiprenota".$anno_esistente;
$tabelle_lock[$num_tab] = $tablecostiprenota_lock;
$num_tab++;
} # fine for $num1
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$tablentariffe_lock = $PHPR_TAB_PRE."ntariffe".$anno_esistente;
$tabelle_lock[$num_tab] = $tablentariffe_lock;
$num_tab++;
} # fine for $num1
} # fine if ($cambia_nome_app and $modificaappartamento == "Continua")
$tabelle_lock[$num_tab] = $tableappartamenti;
$num_tab++;
if ($modificaappartamento == "Continua") {
if ($cambia_nome_app) {
for ($num1 = 0 ;$num1 < $num_anni_esistenti ; $num1++) {
$anno_esistente = risul_query($anni_esistenti,$num1,'idanni');
$tableregole_lock = $PHPR_TAB_PRE."regole".$anno_esistente;
$tabelle_lock[$num_tab] = $tableregole_lock;
$num_tab++;
} # fine for $num1
} # fine if ($cambia_nome_app)
$tabelle_lock[$num_tab] = $tabledescrizioni;
$num_tab++;
} # fine if ($modificaappartamento == "Continua")
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
unset($d_letto);

for ($num1 = 0 ;$num1 < $num_app_modifica ; $num1++) {
$idappartamenti = ${"idappartamenti".$num1};
$n_commento = ${"n_commento".$num1};
$n_nome_app = (string) ${"n_nome_app".$num1};
$n_numcasa = ${"n_numcasa".$num1};
$n_numpiano = ${"n_numpiano".$num1};
$n_maxoccupanti = ${"n_maxoccupanti".$num1};
$n_priorita = ${"n_priorita".$num1};
$n_app_vicini = ${"n_app_vicini".$num1};
${"d_app_vicini".$num1} = esegui_query("select app_vicini from $tableappartamenti where idappartamenti = '".aggslashdb($idappartamenti)."' ");
if (numlin_query(${"d_app_vicini".$num1})) ${"d_app_vicini".$num1} = risul_query(${"d_app_vicini".$num1},0,'app_vicini');

if ($cambia_nome_app and $modificaappartamento == "Continua") {
$anni_esistenti2 = esegui_query("select * from $tableanni order by idanni");
$num_anni_esistenti2 = numlin_query($anni_esistenti2);
if ($num_anni_esistenti != $num_anni_esistenti2) $modificare = "NO";
for ($num2 = 0 ; $num2 < $num_anni_esistenti ; $num2++) {
$anno_esistente = risul_query($anni_esistenti,$num2,'idanni');
$anno_esistente2 = risul_query($anni_esistenti2,$num2,'idanni');
if ($anno_esistente != $anno_esistente2) $modificare = "NO";
} # fine for $num2
if ($modificare == "NO") echo mex("<b>L'appartamento non è stato modificato</b> perchè il database è cambiato nel frattempo",'unit.php').".<br>";
} # fine if ($n_nome_app and $modificaappartamento == "Continua")

$app_esiste = esegui_query("select * from $tableappartamenti where idappartamenti = '".aggslashdb($idappartamenti)."'");
$num_app_esiste = numlin_query($app_esiste);
if ($num_app_esiste != 1) {
echo mex("L'appartamento",'unit.php')." $idappartamenti ".mex("non esiste più",$pag).".<br>";
$modificare = "NO";
$tornare_a = "inizio.php";
} # fine if ($num_app_esiste != 1)
else {
$tornare_a = $pag;
$d_letto[$idappartamenti] = risul_query($app_esiste,0,'letto');
} # fine else if ($num_app_esiste != 1)
if ($n_nome_app and strcmp($n_nome_app,$idappartamenti)) {
$app_esiste = esegui_query("select * from $tableappartamenti where idappartamenti = '".aggslashdb($n_nome_app)."'");
$num_app_esiste = numlin_query($app_esiste);
if ($num_app_esiste != 0) {
echo mex("L'appartamento",'unit.php')." $n_nome_app ".mex("esiste già",$pag).".<br>";
$modificare = "NO";
} # fine if ($num_app_esiste != 0)
} # fine if ($n_nome_app and strcmp($n_nome_app,$idappartamenti))

if ((string) $n_app_vicini != "") {
$n_app_vicini_vett = explode(",",$n_app_vicini);
for ($num2 = 0 ; $num2 < count($n_app_vicini_vett) ; $num2++) {
$app_vic_esist = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '".aggslashdb($n_app_vicini_vett[$num2])."' ");
if (numlin_query($app_vic_esist) != 1 or $n_app_vicini_vett[$num2] == $idappartamenti or !strcmp($n_app_vicini_vett[$num2],$n_nome_app)) {
echo mex("L'appartamento",'unit.php')." $n_app_vicini_vett[$num2] ".mex("non esiste più",$pag).".<br>";
$modificare = "NO";
} # fine if (numlin_query($app_vic_esist) != 1 or...
} # fine for $num2
} # fine if ((string) $n_app_vicini != "")
} # fine for $num1


if ($modificare != "NO") {

if ($modificaappartamento != "Continua") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_app.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"form_tabella\" value=\"$form_tabella\">
<input type=\"hidden\" name=\"num_app_modifica\" value=\"$num_app_modifica\">";
for ($num1 = 0 ;$num1 < $num_app_modifica ; $num1++) {
$idappartamenti = ${"idappartamenti".$num1};
$n_nome_app = (string) ${"n_nome_app".$num1};
$n_numcasa = ${"n_numcasa".$num1};
$n_numpiano = ${"n_numpiano".$num1};
$n_maxoccupanti = ${"n_maxoccupanti".$num1};
$n_priorita = ${"n_priorita".$num1};
$n_app_vicini = (string) ${"n_app_vicini".$num1};
$d_app_vicini = (string) ${"d_app_vicini".$num1};
$n_commento = ${"n_commento".$num1};
if ($form_tabella) {
$comp_nome_app = (string) ${"idappartamenti".$num1};
$comp_numcasa = ${"d_numcasa".$num1};
$comp_numpiano = ${"d_numpiano".$num1};
$comp_maxoccupanti = ${"d_maxoccupanti".$num1};
$comp_priorita = ${"d_priorita".$num1};
$comp_app_vicini = "";
$comp_commento = "";
} # fine if ($form_tabella)
else {
$comp_nome_app = "";
$comp_numcasa = "";
$comp_numpiano = "";
$comp_maxoccupanti = "";
$comp_priorita = "";
$comp_app_vicini = (string) $d_app_vicini;
$comp_commento = ${"d_commento".$num1};
} # fine else if ($form_tabella)
if ($d_letto[$idappartamenti]) $fr1 = "[1]";
else $fr1 = "";
$messaggi = "";
if (strcmp($n_nome_app,$comp_nome_app)) $messaggi .= mex($fr1."Il nome dell'appartamento verrà cambiato da",'unit.php')." $idappartamenti ".mex("a",$pag)." $n_nome_app.<br>";
if ($n_numcasa != $comp_numcasa) $messaggi .= mex("La casa verrà cambiata da",$pag)." \"".${"d_numcasa".$num1}."\" ".mex("a",$pag)." \"$n_numcasa\".<br>";
if ($n_numpiano != $comp_numpiano) $messaggi .= mex("Il piano verrà cambiato da",$pag)." \"".${"d_numpiano".$num1}."\" ".mex("a",$pag)." \"$n_numpiano\".<br>";
if ($n_maxoccupanti != $comp_maxoccupanti) $messaggi .= mex("Il massimo numero di occupanti verrà cambiato da",$pag)." \"".${"d_maxoccupanti".$num1}."\" ".mex("a",$pag)." \"$n_maxoccupanti\".<br>";
if ($n_priorita != $comp_priorita) $messaggi .= mex("La priorità verrà cambiata da",$pag)." \"".${"d_priorita".$num1}."\" ".mex("a",$pag)." \"$n_priorita\".<br>";
if ($n_app_vicini != $comp_app_vicini) $messaggi .= mex($fr1."Gli appartamenti vicini verranno cambiati",'unit.php').".<br>";
if ($n_commento != $comp_commento) $messaggi .= mex("Il commento verrà cambiato",$pag).".<br>";
if ($messaggi) echo mex($fr1."Appartamento",'unit.php')." $idappartamenti:<br>$messaggi<br>";
echo "<input type=\"hidden\" name=\"idappartamenti$num1\" value=\"$idappartamenti\">
<input type=\"hidden\" name=\"n_nome_app$num1\" value=\"$n_nome_app\">
<input type=\"hidden\" name=\"n_numcasa$num1\" value=\"$n_numcasa\">
<input type=\"hidden\" name=\"n_numpiano$num1\" value=\"$n_numpiano\">
<input type=\"hidden\" name=\"n_maxoccupanti$num1\" value=\"$n_maxoccupanti\">
<input type=\"hidden\" name=\"n_priorita$num1\" value=\"$n_priorita\">
<input type=\"hidden\" name=\"n_app_vicini$num1\" value=\"$n_app_vicini\">
<input type=\"hidden\" name=\"n_commento$num1\" value=\"$n_commento\">
<input type=\"hidden\" name=\"d_commento$num1\" value=\"".${"d_commento".$num1}."\">
<input type=\"hidden\" name=\"d_numcasa$num1\" value=\"".${"d_numcasa".$num1}."\">
<input type=\"hidden\" name=\"d_numpiano$num1\" value=\"".${"d_numpiano".$num1}."\">
<input type=\"hidden\" name=\"d_maxoccupanti$num1\" value=\"".${"d_maxoccupanti".$num1}."\">
<input type=\"hidden\" name=\"d_priorita$num1\" value=\"".${"d_priorita".$num1}."\">
<input type=\"hidden\" name=\"d_app_vicini$num1\" value=\"".${"d_app_vicini".$num1}."\">";
} # fine for $num1
echo "<input type=\"hidden\" name=\"modificaappartamento\" value=\"Continua\">
<button class=\"mroo\" type=\"submit\"><div>".mex("Continua",$pag)."</div></button>
<hr style=\"width: 95%\">
</div></form>";
} # fine if ($modificaappartamento != "Continua")

else {

for ($num1 = 0 ;$num1 < $num_app_modifica ; $num1++) {
$idappartamenti = ${"idappartamenti".$num1};
$n_nome_app = (string) ${"n_nome_app".$num1};
$n_numcasa = ${"n_numcasa".$num1};
$n_numpiano = ${"n_numpiano".$num1};
$n_maxoccupanti = ${"n_maxoccupanti".$num1};
$n_priorita = ${"n_priorita".$num1};
$n_app_vicini = ${"n_app_vicini".$num1};
$n_commento = ${"n_commento".$num1};
$d_app_vicini = ${"d_app_vicini".$num1};
if ($form_tabella) {
$comp_nome_app = ${"idappartamenti".$num1};
$comp_numcasa = ${"d_numcasa".$num1};
$comp_numpiano = ${"d_numpiano".$num1};
$comp_maxoccupanti = ${"d_maxoccupanti".$num1};
$comp_priorita = ${"d_priorita".$num1};
$comp_app_vicini = "";
$comp_commento = "";
} # fine if ($form_tabella)
else {
$comp_nome_app = "";
$comp_numcasa = "";
$comp_numpiano = "";
$comp_maxoccupanti = "";
$comp_priorita = "";
$comp_app_vicini = $d_app_vicini;
$comp_commento = ${"d_commento".$num1};
} # fine else if ($form_tabella)
if ($d_letto[$idappartamenti]) $fr1 = "[1]";
else $fr1 = "";
$modificato = "NO";

if (strcmp($n_nome_app,$comp_nome_app)) {
$modificato = "SI";
esegui_query("update $tableappartamenti set idappartamenti = '".aggslashdb($n_nome_app)."' where idappartamenti = '".aggslashdb($idappartamenti)."' ");
for ($num2 = 0 ; $num2 < $num_anni_esistenti ; $num2++) {
$anno_cambia = risul_query($anni_esistenti,$num2,'idanni');
$tableprenota_cambia = $PHPR_TAB_PRE."prenota".$anno_cambia;
esegui_query("update $tableprenota_cambia set idappartamenti = '".aggslashdb($n_nome_app)."' where idappartamenti = '".aggslashdb($idappartamenti)."' ");
$prenota_c = esegui_query("select * from $tableprenota_cambia where app_assegnabili != '' ");
$num_prenota_c = numlin_query($prenota_c);
for ($num3 = 0 ; $num3 < $num_prenota_c ; $num3++) {
$app_assegnabili = risul_query($prenota_c,$num3,'app_assegnabili');
$n_app_assegnabili = substr(str_replace(",$idappartamenti,",",$n_nome_app,",",$app_assegnabili,"),1,-1);
if ($app_assegnabili != $n_app_assegnabili) {
$idprenota = risul_query($prenota_c,$num3,'idprenota');
esegui_query("update $tableprenota_cambia set app_assegnabili = '".aggslashdb($n_app_assegnabili)."' where idprenota = '$idprenota' ");
} # fine if ($app_assegnabili != $n_app_assegnabili)
} # fine for $num3
$prenota_c = esegui_query("select * from $tableprenota_cambia where incompatibilita != '' ");
$num_prenota_c = numlin_query($prenota_c);
for ($num3 = 0 ; $num3 < $num_prenota_c ; $num3++) {
$incompatibilita = risul_query($prenota_c,$num3,'incompatibilita');
$n_incompatibilita = substr(str_replace(",$idappartamenti,",",$n_nome_app,",",$incompatibilita,"),1,-1);
if ($incompatibilita != $n_incompatibilita) {
$idprenota = risul_query($prenota_c,$num3,'idprenota');
esegui_query("update $tableprenota_cambia set incompatibilita = '".aggslashdb($n_incompatibilita)."' where idprenota = '$idprenota' ");
} # fine if ($incompatibilita != $n_incompatibilita)
} # fine for $num3
$tablecostiprenota_cambia = $PHPR_TAB_PRE."costiprenota".$anno_cambia;
$costiprenota_c = esegui_query("select * from $tablecostiprenota_cambia where varappincompatibili != '' ");
$num_costiprenota_c = numlin_query($costiprenota_c);
for ($num3 = 0 ; $num3 < $num_costiprenota_c ; $num3++) {
$varappincompatibili = risul_query($costiprenota_c,$num3,'varappincompatibili');
$n_varappincompatibili = substr(str_replace(",$idappartamenti,",",$n_nome_app,",",$varappincompatibili,"),1,-1);
if ($varappincompatibili != $n_varappincompatibili) {
$idcostiprenota = risul_query($costiprenota_c,$num3,'idcostiprenota');
esegui_query("update $tablecostiprenota_cambia set varappincompatibili = '".aggslashdb($n_varappincompatibili)."' where idcostiprenota = '$idcostiprenota' ");
} # fine if ($varappincompatibili != $n_varappincompatibili)
} # fine for $num3
$tablenometariffe_cambia = $PHPR_TAB_PRE."ntariffe".$anno_cambia;
$ntariffe_c = esegui_query("select * from $tablenometariffe_cambia where appincompatibili_ca != '' ");
$num_ntariffe_c = numlin_query($ntariffe_c);
for ($num3 = 0 ; $num3 < $num_ntariffe_c ; $num3++) {
$appincompatibili_ca = risul_query($ntariffe_c,$num3,'appincompatibili_ca');
$n_appincompatibili_ca = substr(str_replace(",$idappartamenti,",",$n_nome_app,",",$appincompatibili_ca,"),1,-1);
if ($appincompatibili_ca != $n_appincompatibili_ca) {
$idntariffe = risul_query($ntariffe_c,$num3,'idntariffe');
esegui_query("update $tablenometariffe_cambia set appincompatibili_ca = '".aggslashdb($n_appincompatibili_ca)."' where idntariffe = '$idntariffe' ");
} # fine if ($appincompatibili_ca != $n_appincompatibili_ca)
} # fine for $num3
$tableregole_cambia = $PHPR_TAB_PRE."regole".$anno_cambia;
esegui_query("update $tableregole_cambia set app_agenzia = '".aggslashdb($n_nome_app)."' where app_agenzia = '".aggslashdb($idappartamenti)."' ");
$regole2 = esegui_query("select * from $tableregole_cambia where tariffa_per_app != '' ");
$num_regole2 = numlin_query($regole2);
for ($num3 = 0 ; $num3 < $num_regole2 ; $num3++) {
$idregole = risul_query($regole2,$num3,'idregole');
$lista_app = ",".risul_query($regole2,$num3,'motivazione').",";
if (str_replace(",$idappartamenti,","",$lista_app) != $lista_app) {
$lista_app = substr(str_replace(",$idappartamenti,",",$n_nome_app,",$lista_app),1,-1);
esegui_query("update $tableregole_cambia set motivazione = '".aggslashdb($lista_app)."' where idregole = '$idregole' ");
} # fine (str_replace(",$idappartamenti,","",$lista_app) != $lista_app)
$lista_app2 = ",".risul_query($regole2,$num3,'motivazione2').",";
if (str_replace(",$idappartamenti,","",$lista_app2) != $lista_app2) {
$lista_app2 = substr(str_replace(",$idappartamenti,",",$n_nome_app,",$lista_app2),1,-1);
esegui_query("update $tableregole_cambia set motivazione2 = '".aggslashdb($lista_app2)."' where idregole = '$idregole' ");
} # fine (str_replace(",$idappartamenti,","",$lista_app2) != $lista_app2)
} # fine for $num3
} # fine for $num2
$d_app_vicini_vett = explode(",",$d_app_vicini);
for ($num2 = 0 ; $num2 < count($d_app_vicini_vett) ; $num2++) {
$d_app_vicino = $d_app_vicini_vett[$num2];
$app_reciprici = esegui_query("select * from $tableappartamenti where idappartamenti = '".aggslashdb($d_app_vicino)."' ");
if (numlin_query($app_reciprici) == 1) {
$app_reciprici = risul_query($app_reciprici,0,app_vicini);
$app_reciprici = substr(str_replace(",".$idappartamenti.",",",".$n_nome_app.",",",".$app_reciprici.","),1,-1);
esegui_query("update $tableappartamenti set app_vicini = '".aggslashdb($app_reciprici)."' where idappartamenti = '".aggslashdb($d_app_vicino)."' ");
} # fine if (numlin_query($app_reciprici) == 1)
} # fine for $num2
esegui_query("update $tabledescrizioni set nome = '$n_nome_app' where nome = '$idappartamenti' and (tipo = 'appdescr' or tipo = 'appfoto' or tipo = 'appcommfoto') ");
$idappartamenti = $n_nome_app;
$id_appartamenti = esegui_query("select idappartamenti from $tableappartamenti order by idappartamenti ");
$num_appartamenti = numlin_query($id_appartamenti);
$fileaperto = fopen(C_DATI_PATH."/selectappartamenti.php","w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php \necho \"\n");
for ( $num = 0; $num < $num_appartamenti; $num = $num + 1) {
$numapp = risul_query($id_appartamenti,$num,'idappartamenti');
fwrite($fileaperto,"<option value=\\\"$numapp\\\">$numapp</option>
");
} # fine for $num
fwrite($fileaperto,"\"; \n?>");
flock($fileaperto,3);
fclose($fileaperto);
} # fine if (strcmp($n_nome_app,$comp_nome_app))

if ($n_app_vicini != $comp_app_vicini) {
$modificato = "SI";
$n_app_vicini_vett = explode(",",$n_app_vicini);
for ($num2 = 0 ; $num2 < count($n_app_vicini_vett) ; $num2++) {
$n_app_vicino = $n_app_vicini_vett[$num2];
if (str_replace(",".$n_app_vicino.",",",",",".$d_app_vicini.",") == ",".$d_app_vicini.",") {
$app_reciprici = esegui_query("select app_vicini from $tableappartamenti where idappartamenti = '".aggslashdb($n_app_vicino)."' ");
if (numlin_query($app_reciprici) == 1) {
$app_reciprici = risul_query($app_reciprici,0,'app_vicini');
if ((string) $app_reciprici != "") $app_reciprici .= ",";
$app_reciprici .= $idappartamenti;
esegui_query("update $tableappartamenti set app_vicini = '".aggslashdb($app_reciprici)."' where idappartamenti = '".aggslashdb($n_app_vicino)."' ");
} # fine if (numlin_query($app_reciprici) == 1)
} # fine if (str_replace(",".$n_app_vicino.",","",",".$d_app_vicini.",") == ",".$d_app_vicini.",")
} # fine for $num2
$d_app_vicini_vett = explode(",",$d_app_vicini);
for ($num2 = 0 ; $num2 < count($d_app_vicini_vett) ; $num2++) {
$d_app_vicino = $d_app_vicini_vett[$num2];
if (str_replace(",".$d_app_vicino.",",",",",".$n_app_vicini.",") == ",".$n_app_vicini.",") {
$app_reciprici = esegui_query("select * from $tableappartamenti where idappartamenti = '".aggslashdb($d_app_vicino)."' ");
if (numlin_query($app_reciprici) == 1) {
$app_reciprici = risul_query($app_reciprici,0,'app_vicini');
$app_reciprici = substr(str_replace(",".$idappartamenti.",",",",",".$app_reciprici.","),1,-1);
esegui_query("update $tableappartamenti set app_vicini = '".aggslashdb($app_reciprici)."' where idappartamenti = '".aggslashdb($d_app_vicino)."' ");
} # fine if (numlin_query($app_reciprici) == 1)
} # fine if (str_replace(",".$d_app_vicino.",",",",",".$n_app_vicini.",") == ",".$n_app_vicini.",")
} # fine for $num2
esegui_query("update $tableappartamenti set app_vicini = '".aggslashdb($n_app_vicini)."' where idappartamenti = '".aggslashdb($idappartamenti)."' ");
} # fine if ($n_app_vicini != $comp_app_vicini)

if ($n_numcasa != $comp_numcasa) {
$modificato = "SI";
esegui_query("update $tableappartamenti set numcasa = '".aggslashdb($n_numcasa)."' where idappartamenti = '".aggslashdb($idappartamenti)."' ");
} # fine if ($n_numcasa != $comp_)
if ($n_numpiano != $comp_numpiano) {
$modificato = "SI";
esegui_query("update $tableappartamenti set numpiano = '".aggslashdb($n_numpiano)."' where idappartamenti = '".aggslashdb($idappartamenti)."' ");
} # fine if ($n_numpiano != $comp_numpiano)
if ($n_maxoccupanti != $comp_maxoccupanti) {
$letto = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '".aggslashdb($idappartamenti)."' and letto = '1' ");
if (!numlin_query($letto)) {
$modificato = "SI";
$n_maxoccupanti = (string) $n_maxoccupanti;
if ($n_maxoccupanti == "") $n_maxoccupanti = "NULL";
else $n_maxoccupanti = "'".aggslashdb($n_maxoccupanti)."'";
esegui_query("update $tableappartamenti set maxoccupanti = $n_maxoccupanti where idappartamenti = '".aggslashdb($idappartamenti)."' ");
} # fine if (!numlin_query($letto))
} # fine if ($n_maxoccupanti != $comp_maxoccupanti)
if ($n_priorita != $comp_priorita) {
$modificato = "SI";
$n_priorita = (string) $n_priorita;
if ($n_priorita == "") $n_priorita = "NULL";
else $n_priorita = "'".aggslashdb($n_priorita)."'";
esegui_query("update $tableappartamenti set priorita = $n_priorita where idappartamenti = '".aggslashdb($idappartamenti)."' ");
} # fine if ($n_priorita != $comp_priorita)
if ($n_commento != $comp_commento) {
$modificato = "SI";
$n_commento = aggslashdb($n_commento);
esegui_query("update $tableappartamenti set commento = '".aggslashdb($n_commento)."' where idappartamenti = '".aggslashdb($idappartamenti)."' ");
} # fine if ($n_commento != $comp_commento)
if ($modificato == "SI") echo mex($fr1."L'appartamento",'unit.php')." $idappartamenti ".mex("è stato modificato",'unit.php').".<br>";
} # fine for $num1

} # fine else if ($modificaappartamento != "Continua")

} # fine if ($modificare != "NO")
unlock_tabelle($tabelle_lock);

if ($form_tabella) $tornare_a = "visualizza_tabelle.php";
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$tornare_a\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"appartamenti\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"$idappartamenti\">";
echo "<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
<br></div></form><br></div>";
} # fine if ($modificaappartamento)




if ($modificadescr) {
$mostra_form_modifica = "NO";
$tabelle_lock = array($tabledescrizioni);
$altre_tab_lock = array($tableappartamenti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$app_esist = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '$idappartamenti' ");
if (numlin_query($app_esist)) {
if (strcmp($n_descrizione_ita,"")) {
if (get_magic_quotes_gpc()) $n_descrizione_ita = stripslashes($n_descrizione_ita);
$n_descrizione_ita = aggslashdb(htmlspecialchars($n_descrizione_ita));
$descr_esistente = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = 'ita' and numero = '1' ");
if (numlin_query($descr_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_descrizione_ita' where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = 'ita' and numero = '1' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('$idappartamenti','appdescr','ita','1','$n_descrizione_ita') ");
} # fine if (strcmp($n_descrizione_ita,""))
else esegui_query("delete from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = 'ita' and numero = '1' ");
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." and strlen($ini_lingua) <= 3 and preg_replace("/[a-z]/","",$ini_lingua) == "") {
$n_descrizione = ${"n_descrizione_".$ini_lingua};
if (strcmp($n_descrizione,"")) {
if (get_magic_quotes_gpc()) $n_descrizione = stripslashes($n_descrizione);
$n_descrizione = aggslashdb(htmlspecialchars($n_descrizione));
$descr_esistente = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = '$ini_lingua' and numero = '1' ");
if (numlin_query($descr_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_descrizione' where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = '$ini_lingua' and numero = '1' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('$idappartamenti','appdescr','$ini_lingua','1','$n_descrizione') ");
} # fine if (strcmp($n_descrizione,""))
else esegui_query("delete from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = '$ini_lingua' and numero = '1' ");
} # fine if ($file != "." && $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "<br>".ucfirst(mex("descrizione dell'appartamento",'unit.php'))." $idappartamenti ".mex("aggiornata",$pag).".<br>";
} # fine if (numlin_query($app_esist))
unlock_tabelle($tabelle_lock);
$mostra_torna_indietro = "SI";
} # fine if ($modificadescr)


if ($commentofoto) {
$mostra_form_modifica = "NO";
$app_esist = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '$idappartamenti' ");
if ($numfoto and controlla_num_pos($numfoto) == "SI" and numlin_query($app_esist)) {
$tabelle_lock = array($tabledescrizioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
$foto_esistente = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appfoto' and numero = '$numfoto' ");
if (numlin_query($foto_esistente)) {
if (strcmp($n_commento_ita,"")) {
if (get_magic_quotes_gpc()) $n_commento_ita = stripslashes($n_commento_ita);
$n_commento_ita = aggslashdb(htmlspecialchars($n_commento_ita));
$comm_esistente = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
if (numlin_query($comm_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_commento_ita' where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('$idappartamenti','appcommfoto','ita','$numfoto','$n_commento_ita') ");
} # fine if (strcmp($n_commento_ita,""))
else esegui_query("delete from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." and strlen($ini_lingua) <= 3 and preg_replace("/[a-z]/","",$ini_lingua) == "") {
$n_commento = ${"n_commento_".$ini_lingua};
if (strcmp($n_commento,"")) {
if (get_magic_quotes_gpc()) $n_commento = stripslashes($n_commento);
$n_commento = aggslashdb(htmlspecialchars($n_commento));
$comm_esistente = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
if (numlin_query($comm_esistente)) esegui_query("update $tabledescrizioni set testo = '$n_commento' where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
else esegui_query("insert into $tabledescrizioni (nome,tipo,lingua,numero,testo) values ('$idappartamenti','appcommfoto','$ini_lingua','$numfoto','$n_commento') ");
} # fine if (strcmp($n_commento,""))
else esegui_query("delete from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
} # fine if ($file != "." && $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "<br>".ucfirst(mex("commento della foto",$pag))." $numfoto ".mex("aggiornato",$pag).".<br>";
} # fine if (numlin_query($foto_esistente))
unlock_tabelle($tabelle_lock);
} # fine if ($numfoto and controlla_num_pos($numfoto) == "SI" and...
$mostra_torna_indietro = "SI";
} # fine if ($commentofoto)


if ($cancurlfoto) {
$mostra_form_modifica = "NO";
if ($numfoto and controlla_num_pos($numfoto) == "SI") {
esegui_query("delete from $tabledescrizioni where nome = '$idappartamenti' and (tipo = 'appfoto' or tipo = 'appcommfoto') and numero = '$numfoto' ");
echo "".ucfirst(mex("foto eliminata",$pag)).".<br>";
} # fine if ($numfoto and controlla_num_pos($numfoto) == "SI") 
$mostra_torna_indietro = "SI";
} # fine if ($aggurlfoto)


if ($aggurlfoto) {
$mostra_form_modifica = "NO";
if (get_magic_quotes_gpc()) $n_urlfoto = stripslashes($n_urlfoto);
$lowurl = strtolower($n_urlfoto);
if (substr($lowurl,-4) != ".jpg" and substr($lowurl,-5) != ".jpeg" and substr($lowurl,-4) != ".gif" and substr($lowurl,-4) != ".png") $errore = "SI";
if (str_replace("<","",$n_urlfoto) != $n_urlfoto or str_replace(">","",$n_urlfoto) != $n_urlfoto or str_replace("\"","",$n_urlfoto) != $n_urlfoto) $errore = "SI";
$app_esist = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '$idappartamenti' ");
if (!numlin_query($app_esist)) $errore = "SI";
if ($errore != "SI") {
$tabelle_lock = array($tabledescrizioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
$foto_esistenti = esegui_query("select * from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appfoto' order by numero desc ");
if (numlin_query($foto_esistenti)) $numfoto = (risul_query($foto_esistenti,0,'numero') + 1);
else $numfoto = 1;
esegui_query("insert into $tabledescrizioni (nome,tipo,numero,testo) values ('$idappartamenti','appfoto','$numfoto','".aggslashdb($n_urlfoto)."') ");
echo "<br>".ucfirst(mex("la nuova foto è stata aggiunta",$pag)).".<br>";
unlock_tabelle($tabelle_lock);
} # fine if ($errore != "SI")
else echo "".ucfirst(mex("l'url della foto è sbagliata",$pag)).".<br>";
$mostra_torna_indietro = "SI";
} # fine if ($aggurlfoto)



if ($mostra_torna_indietro == "SI") {
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"$idappartamenti\">";
echo "<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
<br></div></form><br></div>";
} # fine if ($mostra_torna_indietro == "SI")




if ($mostra_form_modifica != "NO") {


# Form per modificare l'appartamento.
$appartamento = esegui_query("select * from $tableappartamenti where idappartamenti = '$idappartamenti'");
if (numlin_query($appartamento)) {
$d_numcasa = risul_query($appartamento,0,'numcasa');
$d_numpiano = risul_query($appartamento,0,'numpiano');
$d_maxoccupanti = risul_query($appartamento,0,'maxoccupanti');
$d_priorita = risul_query($appartamento,0,'priorita');
$d_letto = risul_query($appartamento,0,'letto');
$d_app_vicini = risul_query($appartamento,0,'app_vicini');
$d_commento = risul_query($appartamento,0,'commento');
} # fine if (numlin_query($appartamento))

if ($d_letto) $fr1 = "[1]";
else $fr1 = "";

echo "<h3 id=\"h_mroo\"><span>".mex($fr1."Modifica l'appartamento",'unit.php')." $idappartamenti.</span></h3>";

echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_app.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti0\" value=\"$idappartamenti\">
<input type=\"hidden\" name=\"num_app_modifica\" value=\"1\">
<table cellspacing=2 cellpadding=5>
<tr><td>".mex("Nome",$pag).": <b>$idappartamenti</b></td>
<td>".mex("Cambia in",$pag)." <input type=\"text\" name=\"n_nome_app0\" size=\"10\"></td></tr>
<tr><td>".mex("Casa",$pag).": <b>$d_numcasa</b></td>
<td>".mex("Cambia in",$pag)." <input type=\"text\" name=\"n_numcasa0\" size=\"10\"></td></tr>
<tr><td>".mex("Piano",$pag).": <b>$d_numpiano</b></td>
<td>".mex("Cambia in",$pag)." <input type=\"text\" name=\"n_numpiano0\" size=\"10\" maxlength=\"10\"></td></tr>
<tr><td>".mex("Capienza",$pag).": <b>$d_maxoccupanti</b> ";
if ($d_maxoccupanti == 1) echo mex("Persona",$pag);
else echo mex("Persone",$pag);
echo "</td><td>";
if (!$d_letto) echo "".mex("Cambia in",$pag)." <input type=\"text\" name=\"n_maxoccupanti0\" size=\"2\" maxlength=\"2\">";
echo "</td></tr>
<tr><td>".mex("Priorità",$pag).": <b>$d_priorita</b></td>
<td>".mex("Cambia in",$pag)." <input type=\"text\" name=\"n_priorita0\" size=\"5\" maxlength=\"5\">
 <small>(".mex("più bassa viene assegnata prima",$pag).")</small></td></tr>
<tr><td>".mex($fr1."Appartamenti vicini",'unit.php').": </td>
<td> <input type=\"text\" name=\"n_app_vicini0\" size=\"25\" value=\"$d_app_vicini\"> <small>(".mex($fr1."lista di appartamenti separati da virgole",'unit.php').")</small></td></tr>
</table>
".mex("Commento",$pag).":<br>
<textarea name=\"n_commento0\" rows=3 cols=60 style=\"white-space: pre; overflow: auto;\">$d_commento</textarea><br>
<div style=\"text-align: center;\"><br>
".mex($fr1."<b>Attenzione</b>: le prenotazioni già inserite in questo appartamento <b>non</b> verranno spostate",'unit.php').",<br>
".mex("anche se le loro caratteristiche non sono più compatibili",$pag).".<br>
<button class=\"mroo\" id=\"modi\" type=\"submit\"><div>".mex($fr1."Modifica l'appartamento",'unit.php')." $idappartamenti</div></button>
<input type=\"hidden\" name=\"modificaappartamento\" value=\"1\">
<input type=\"hidden\" name=\"d_numcasa0\" value=\"$d_numcasa\">
<input type=\"hidden\" name=\"d_numpiano0\" value=\"$d_numpiano\">
<input type=\"hidden\" name=\"d_maxoccupanti0\" value=\"$d_maxoccupanti\">
<input type=\"hidden\" name=\"d_priorita0\" value=\"$d_priorita\">
<input type=\"hidden\" name=\"d_app_vicini0\" value=\"$d_app_vicini\">
<input type=\"hidden\" name=\"d_commento0\" value=\"$d_commento\">
</div><br></div></form>
<hr style=\"width: 95%\">";

$d_descrizione = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = 'ita' and numero = '1' ");
if (numlin_query($d_descrizione)) $d_descrizione = risul_query($d_descrizione,0,'testo');
else $d_descrizione = "";
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"".$idappartamenti."\">
<input type=\"hidden\" name=\"modificadescr\" value=\"SI\">
".ucfirst(mex($fr1."descrizione dell'appartamento",'unit.php'))." <em>$idappartamenti</em>:<br>
<table class=\"nomob\"><tr><td>Italiano:<br>
<textarea name=\"n_descrizione_ita\" rows=4 cols=60 style=\"white-space: pre; overflow: auto;\">$d_descrizione</textarea></td>";
$col = 0;
$max_col = 2;
unset($lingue_vett);
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." and strlen($ini_lingua) <= 3 and preg_replace("/[a-z]/","",$ini_lingua) == "") {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
$lingue_vett[$ini_lingua] = $nome_lingua;
$d_descrizione = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appdescr' and lingua = '$ini_lingua' and numero = '1' ");
if (numlin_query($d_descrizione)) $d_descrizione = risul_query($d_descrizione,0,'testo');
else $d_descrizione = "";
$col++;
if ($col == $max_col) {
$col = 0;
echo "</tr><tr>";
} # fine if ($col == $max_col)
else echo "<td style=\"width: 30px;\"></td>";
echo "<td>".ucfirst($nome_lingua).":<br>";
echo "<textarea name=\"n_descrizione_$ini_lingua\" rows=4 cols=60 style=\"white-space: pre; overflow: auto;\">$d_descrizione</textarea></td>";
} # fine if ($file != "." && $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
echo "</tr></table>
<button class=\"edit\" type=\"submit\"><div>".ucfirst(mex("modifica",$pag))."</div></button></div></form><br><br>";

$foto = esegui_query("select * from $tabledescrizioni where nome = '".$idappartamenti."' and tipo = 'appfoto' order by numero ");
$num_foto = numlin_query($foto);
echo "".ucfirst(mex($fr1."foto dell'appartamento",'unit.php'))." <em>".$idappartamenti."</em>:<br>";
for ($num1 = 1 ; $num1 <= $num_foto ; $num1++) {
$url_foto = risul_query($foto,($num1 - 1),'testo');
$numfoto = risul_query($foto,($num1 - 1),'numero');
echo "<table><tr><td valign=\"top\">$num1.</td><td>
<a href=\"$url_foto\"><img class=\"dphoto\" style=\"border: 0px none ; text-decoration: none;\" src=\"$url_foto\" alt=\"".htmlspecialchars($url_foto)."\"></a>
</td><td style=\"width: 20px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div class=\"linhbox\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"".$idappartamenti."\">
<input type=\"hidden\" name=\"commentofoto\" value=\"SI\">
<input type=\"hidden\" name=\"numfoto\" value=\"$numfoto\">
".ucfirst(mex("commento",$pag)).":<br>";
$d_commento = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = 'ita' and numero = '$numfoto' ");
if (numlin_query($d_commento)) $d_commento = risul_query($d_commento,0,'testo');
else $d_commento = "";
if ($priv_mod_tariffe != "n") echo "Italiano: <input type=\"text\" name=\"n_commento_ita\" value=\"$d_commento\" size=\"24\"><br>";
else echo "<div style=\"width: 300px;\">Italiano: \"<em>$d_commento</em>\"</div>";
reset($lingue_vett);
foreach ($lingue_vett as $ini_lingua => $nome_lingua) {
$d_commento = esegui_query("select testo from $tabledescrizioni where nome = '$idappartamenti' and tipo = 'appcommfoto' and lingua = '$ini_lingua' and numero = '$numfoto' ");
if (numlin_query($d_commento)) $d_commento = risul_query($d_commento,0,'testo');
else $d_commento = "";
echo "".ucfirst($nome_lingua).": <input type=\"text\" name=\"n_commento_$ini_lingua\" value=\"$d_commento\" size=\"24\"><br>";
} # fine foreach ($lingue_vett as $ini_lingua => $nome_lingua)
echo "<button class=\"edtm\" type=\"submit\"><div>".ucfirst(mex("modifica",$pag))."</div></button>
</div></form><br></td><td style=\"width: 20px;\"></td><td valign=\"middle\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"".$idappartamenti."\">
<input type=\"hidden\" name=\"cancurlfoto\" value=\"SI\">
<input type=\"hidden\" name=\"numfoto\" value=\"$numfoto\">
<button class=\"cpho\" type=\"submit\"><div>".ucfirst(mex("elimina",$pag))."</div></button>
</div></form></td></tr></table>";
} # fine for $num1

if (defined('C_RESTRIZIONI_DEMO_ADMIN') and C_RESTRIZIONI_DEMO_ADMIN == "SI") $readonly = " readonly=\"readonly\"";
else $readonly = "";
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"".$idappartamenti."\">
<input type=\"hidden\" name=\"aggurlfoto\" value=\"SI\">
".ucfirst(mex("url di una nuova foto",$pag)).":
<input type=\"text\" name=\"n_urlfoto\" size=\"30\" value=\"http://\"$readonly>
<button class=\"apho\" type=\"submit\"><div>".ucfirst(mex("aggiungi",$pag))."</div></button>
</div></form>";

echo "<hr style=\"width: 95%\"><br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_app.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idappartamenti\" value=\"$idappartamenti\">
<input type=\"hidden\" name=\"cancella_app\" value=\"1\">
<button class=\"croo\" id=\"canc\" type=\"submit\"><div>".mex($fr1."Cancella l'appartamento",'unit.php')." $idappartamenti</div></button>
</div><br></form></div>

<hr style=\"width: 95%\"><br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_tabelle.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"appartamenti\">
<button class=\"gobk\" id=\"indi\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";


} # fine if ($mostra_form_modifica != "NO")



if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($id_utente and $id_utente == 1)



?>
