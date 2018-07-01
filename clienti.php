<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2016 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

$pag = "clienti.php";
$titolo = "HotelDruid: Clienti";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include(C_DATI_PATH."/lingua.php");
include("./includes/funzioni_clienti.php");
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tableparentele = $PHPR_TAB_PRE."parentele";
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";

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
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
if (substr($priv_ins_clienti,0,1) == "s") $inserimento_nuovi_clienti = "SI";
else $inserimento_nuovi_clienti = "NO";
$modifica_clienti = "NO";
if (substr($priv_ins_clienti,1,1) == "s") $modifica_clienti = "SI";
if (substr($priv_ins_clienti,1,1) == "p") $modifica_clienti = "PROPRI";
if (substr($priv_ins_clienti,1,1) == "g") { $modifica_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$prefisso_clienti = risul_query($privilegi_globali_utente,0,'prefisso_clienti');
$attiva_prefisso_clienti = substr($prefisso_clienti,0,1);
if ($attiva_prefisso_clienti != "n") {
$prefisso_clienti = explode(",",$prefisso_clienti);
$prefisso_clienti = $prefisso_clienti[1];
} # fine if ($prefisso_clienti != "n")
$regole1_consentite = risul_query($privilegi_annuali_utente,0,'regole1_consentite');
$attiva_regole1_consentite = substr($regole1_consentite,0,1);
$applica_regole1 = substr($regole1_consentite,1,1);
if ($attiva_regole1_consentite != "n" or $applica_regole1 == "n") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
$tariffe_consentite = risul_query($privilegi_annuali_utente,0,'tariffe_consentite');
$attiva_tariffe_consentite = substr($tariffe_consentite,0,1);
if ($attiva_tariffe_consentite == "s") {
$tariffe_consentite = explode(",",substr($tariffe_consentite,2));
unset($tariffe_consentite_vett);
for ($num1 = 0 ; $num1 < count($tariffe_consentite) ; $num1++) if ($tariffe_consentite[$num1]) $tariffe_consentite_vett[$tariffe_consentite[$num1]] = "SI";
} # fine if ($attiva_tariffe_consentite == "s")
$costi_agg_consentiti = risul_query($privilegi_annuali_utente,0,'costi_agg_consentiti');
$attiva_costi_agg_consentiti = substr($costi_agg_consentiti,0,1);
if ($attiva_costi_agg_consentiti == "s") {
$costi_agg_consentiti = explode(",",substr($costi_agg_consentiti,2));
unset($costi_agg_consentiti_vett);
for ($num1 = 0 ; $num1 < count($costi_agg_consentiti) ; $num1++) if ($costi_agg_consentiti[$num1]) $costi_agg_consentiti_vett[$costi_agg_consentiti[$num1]] = "SI";
} # fine if ($attiva_costi_agg_consentiti == "s")
$priv_ins_prenota = risul_query($privilegi_annuali_utente,0,'priv_ins_prenota');
$priv_ins_nuove_prenota = substr($priv_ins_prenota,0,1);
$priv_ins_assegnazione_app = substr($priv_ins_prenota,1,1);
$priv_ins_costi_agg = substr($priv_ins_prenota,5,1);
$priv_ins_num_persone = substr($priv_ins_prenota,7,1);
$priv_ins_periodi_passati = substr($priv_ins_prenota,8,1);
$priv_ins_multiple = substr($priv_ins_prenota,9,1);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
$tableutenti = $PHPR_TAB_PRE."utenti";
$nome_utente = esegui_query("select * from $tableutenti where idutenti = '$id_utente'");
$nome_utente = risul_query($nome_utente,0,'nome_utente');
unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,"idgruppo");
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) $utenti_gruppi[risul_query($utenti_gruppo,$num2,"idutente")] = 1;
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$modifica_pers = "SI";
$inserimento_nuovi_clienti = "SI";
$modifica_clienti = "SI";
$vedi_clienti = "SI";
$attiva_prefisso_clienti = "n";
$attiva_regole1_consentite = "n";
$applica_regole1 = "s";
$attiva_tariffe_consentite = "n";
$attiva_costi_agg_consentiti = "n";
$priv_ins_nuove_prenota = "s";
$priv_ins_assegnazione_app = "s";
$priv_ins_costi_agg = "s";
$priv_ins_num_persone = "s";
$priv_ins_periodi_passati = "s";
$priv_ins_multiple = "s";
} # fine else if ($id_utente != 1)

if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0) {
$tableclienti = $PHPR_TAB_PRE."clienti";
$num_clienti_esistenti = esegui_query("select idclienti from $tableclienti");
$num_clienti_esistenti = numlin_query($num_clienti_esistenti);
if ($num_clienti_esistenti >= C_MASSIMO_NUM_CLIENTI) $inserimento_nuovi_clienti = "NO";
} # fine if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0)

if ($anno_utente_attivato == "SI") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


if ($id_utente != 1 or controlla_num_pos($id_utente_ins) == "NO" or $id_utente_ins == "") $id_utente_ins = $id_utente;

$campi_pers = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente'");
if (numlin_query($campi_pers) == 1) {
$campi_pers = explode(">",risul_query($campi_pers,0,'valpersonalizza'));
$num_campi_pers = count($campi_pers);
} # fine if (numlin_query($campi_pers) == 1)
else $num_campi_pers = 0;

if (@get_magic_quotes_gpc()) {
$cognome = stripslashes($cognome);
$nome = stripslashes($nome);
$soprannome = stripslashes($soprannome);
$titolo_cli = stripslashes($titolo_cli);
$documento = stripslashes($documento);
$tipodoc = stripslashes($tipodoc);
$cittadoc = stripslashes($cittadoc);
$regionedoc = stripslashes($regionedoc);
$nazionedoc = stripslashes($nazionedoc);
$cittanascita = stripslashes($cittanascita);
$regionenascita = stripslashes($regionenascita);
$nazionenascita = stripslashes($nazionenascita);
$nazionalita = stripslashes($nazionalita);
$nazione = stripslashes($nazione);
$regione = stripslashes($regione);
$citta = stripslashes($citta);
$nomevia = stripslashes($nomevia);
$numcivico = stripslashes($numcivico);
$cap = stripslashes($cap);
$telefono = stripslashes($telefono);
$telefono2 = stripslashes($telefono2);
$telefono3 = stripslashes($telefono3);
$fax = stripslashes($fax);
$email = stripslashes($email);
$cod_fiscale = stripslashes($cod_fiscale);
$partita_iva = stripslashes($partita_iva);
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) ${"campo_pers".$num1} = stripslashes(${"campo_pers".$num1});
} # fine if (@get_magic_quotes_gpc())
$cognome = htmlspecialchars($cognome);
$nome = htmlspecialchars($nome);
$soprannome = htmlspecialchars($soprannome);
$titolo_cli = htmlspecialchars($titolo_cli);
if ($sesso and $sesso != "f") $sesso = "m";
$documento = htmlspecialchars($documento);
$tipodoc = htmlspecialchars($tipodoc);
$cittadoc = htmlspecialchars($cittadoc);
$regionedoc = htmlspecialchars($regionedoc);
$nazionedoc = htmlspecialchars($nazionedoc);
$cittanascita = htmlspecialchars($cittanascita);
$regionenascita = htmlspecialchars($regionenascita);
$nazionenascita = htmlspecialchars($nazionenascita);
$nazionalita = htmlspecialchars($nazionalita);
$nazione = htmlspecialchars($nazione);
$regione = htmlspecialchars($regione);
$citta = htmlspecialchars($citta);
$nomevia = htmlspecialchars($nomevia);
$numcivico = htmlspecialchars($numcivico);
$cap = htmlspecialchars($cap);
$telefono = htmlspecialchars($telefono);
$telefono2 = htmlspecialchars($telefono2);
$telefono3 = htmlspecialchars($telefono3);
$fax = htmlspecialchars($fax);
$email = htmlspecialchars($email);
$cod_fiscale = htmlspecialchars($cod_fiscale);
$partita_iva = htmlspecialchars($partita_iva);
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) ${"campo_pers".$num1} = htmlspecialchars(${"campo_pers".$num1});

$stile_data = stile_data();

unset($manda_cognome);
unset($lista_app_copia);
unset($assegnazioneapp_copia);
unset($spostamenti_transazione);

$dati_transazione = recupera_dati_transazione($id_transazione,$id_sessione,$anno,"SI",$tipo_transazione);



# cose da fare se si viene da prenota.php
if ($nuovaprenotazione) {

if ($priv_ins_multiple == "n") {
$num_tipologie = 1;
$num_app_richiesti1 = 1;
} # fine if ($priv_ins_multiple == "n")
if ($priv_ins_multiple != "s") $prenota_vicine = "";
if (!$num_tipologie or controlla_num_pos($num_tipologie) == "NO" or $num_tipologie == 0 or $num_tipologie > 999) $num_tipologie = 1;
include("./includes/liberasettimane.php");


include("./includes/sett_gio.php");
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableversioni = $PHPR_TAB_PRE."versioni";
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;

if ($aggiungi_tipologie) {
$manda_dati_assegnazione = "SI";
$mostra_form_dati_cliente = "NO";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
echo "<input type=\"hidden\" name=\"num_piano$n_t\" value=\"".${"num_piano".$n_t}."\">
<input type=\"hidden\" name=\"num_casa$n_t\" value=\"".${"num_casa".$n_t}."\">
<input type=\"hidden\" name=\"num_persone_casa$n_t\" value=\"".${"num_persone_casa".$n_t}."\">";
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '".aggslashdb(${"inizioperiodo".$n_t})."' ");
if (numlin_query($idinizioperiodo) == 1) ${"inizioperiodo".$n_t} = risul_query($idinizioperiodo,0,'idperiodi');
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '".aggslashdb(${"fineperiodo".$n_t})."' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if (numlin_query($idfineperiodo) == 1) ${"fineperiodo".$n_t} = risul_query($idfineperiodo,0,'idperiodi');
} # fine for $n_t
include("./includes/dati_form_prenotazione.php");
echo "<br>".mex("Nº di tipologie da aggiungere",$pag).":";
echo "<input type=\"text\" name=\"num_tipologie_da_aggiungere\" size=\"2\" maxlength=\"2\" value =\"1\">
<button class=\"plus\" type=\"submit\"><div>".mex("Aggiungi",$pag)."</div></button>
</div></form><br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
echo "<input type=\"hidden\" name=\"num_piano$n_t\" value=\"".${"num_piano".$n_t}."\">
<input type=\"hidden\" name=\"num_casa$n_t\" value=\"".${"num_casa".$n_t}."\">
<input type=\"hidden\" name=\"num_persone_casa$n_t\" value=\"".${"num_persone_casa".$n_t}."\">";
} # fine for $n_t
include("./includes/dati_form_prenotazione.php");
echo "<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br><br>";
} # fine if ($aggiungi_tipologie)


else {

$inseriscicliente = "SI";

function ins_prenota_temp_in_tab ($tableprenota,$tablecostiprenota,$num_tipologie,$limiti_var,&$dati_transazione13,$prenota_vicine,$id_utente,$HOSTNAME) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
if ($limiti_var["lim_prenota_temp"] < $datainserimento) {
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
if ($n_t != 1) $dati_transazione13 .= ", ,";
global ${"appartamento".$n_t},${"num_app_richiesti".$n_t},${"inizioperiodo".$n_t},${"fineperiodo".$n_t},${"lista_app".$n_t},${"assegnazioneapp".$n_t},${"numpersone".$n_t},${"prenota_vicine".$n_t},${"interrompi_vicine_ogni".$n_t};
$appartamento_vett = explode(",",${"appartamento".$n_t});
$iniper = ${"inizioperiodo".$n_t};
if (str_replace(",","",$iniper) != $iniper) {
$iniper = explode(",",$iniper);
$iniper = $iniper[0];
} # fine if (str_replace(",","",$iniper) != $iniper)
$fineper = ${"fineperiodo".$n_t};
if (str_replace(",","",$fineper) != $fineper) {
$fineper = explode(",",$fineper);
$fineper = $fineper[0];
} # fine if (str_replace(",","",$fineper) != $fineper)
$lista_idprenota = "";
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
$idprenota = esegui_query("select numlimite from $tablecostiprenota where idcostiprenota = '1'");
$idprenota = risul_query($idprenota,0,'numlimite');
esegui_query("update $tablecostiprenota set numlimite = '".($idprenota + 1)."' where idcostiprenota = '1'","",1);
esegui_query("insert into $tableprenota (idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,tariffa,tariffa_tot,caparra,conferma,datainserimento,hostinserimento,utente_inserimento) values ('$idprenota','0','".$appartamento_vett[($num1 - 1)]."','".$iniper."','".$fineper."','a#@&1','1','1','N','$datainserimento','$HOSTNAME','$id_utente')","",1);
if (${"lista_app".$n_t}) esegui_query("update $tableprenota set app_assegnabili = '".${"lista_app".$n_t}."' where idprenota = '$idprenota' ","",1);
if (${"assegnazioneapp".$n_t}) esegui_query("update $tableprenota set assegnazioneapp = '".${"assegnazioneapp".$n_t}."' where idprenota = '$idprenota' ","",1);
if (${"numpersone".$n_t}) esegui_query("update $tableprenota set num_persone = '".${"numpersone".$n_t}."' where idprenota = '$idprenota' ","",1);
if ($num1 != 1) $lista_idprenota .= ",";
$lista_idprenota .= $idprenota;
} # fine for $num1
$dati_transazione13 .= $lista_idprenota;
if ($prenota_vicine != "SI" and ${"prenota_vicine".$n_t}) {
$lista_idprenota_vett = explode(",",$lista_idprenota);
for ($num1 = 0 ; $num1 < count($lista_idprenota_vett) ; $num1++) {
$idprenota = $lista_idprenota_vett[$num1];
$idprenota_vicine = $lista_idprenota;
if (${"interrompi_vicine_ogni".$n_t}) {
for ($num2 = 0 ; $num2 < count($lista_idprenota_vett) ; $num2 += ${"interrompi_vicine_ogni".$n_t}) {
if ($num1 >= $num2 and $num1 < ($num2 + ${"interrompi_vicine_ogni".$n_t})) {
$idprenota_vicine = "";
for ($num3 = 0 ; $num3 < ${"interrompi_vicine_ogni".$n_t} ; $num3++) $idprenota_vicine .= $lista_idprenota_vett[($num2 + $num3)].",";
$idprenota_vicine = substr($idprenota_vicine,0,-1);
break;
} # fine if ($num1 >= $num2 and $num1 < ($num2 + ${"interrompi_vicine_ogni".$n_t}))
} # fine for $num2
} # fine if (${"interrompi_vicine_ogni".$n_t})
$idprenota_vicine = substr(str_replace(",".$idprenota.",",",",",".$idprenota_vicine.","),1,-1);
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_vicine' where idprenota = '$idprenota' ","",1);
} # fine for $num1
} # fine if ($prenota_vicine != "SI" and ${"prenota_vicine".$n_t})
} # fine for $n_t
if ($prenota_vicine == "SI") {
$lista_idprenota = str_replace(", ,",",",$dati_transazione13);
$lista_idprenota_vett = explode(",",$lista_idprenota);
for ($num1 = 0 ; $num1 < count($lista_idprenota_vett) ; $num1++) {
$idprenota = $lista_idprenota_vett[$num1];
$idprenota_vicine = substr(str_replace(",".$idprenota.",",",",",".$lista_idprenota.","),1,-1);
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_vicine' where idprenota = '$idprenota' ","",1);
} # fine for $num1
} # fine if ($prenota_vicine == "SI")
} # fine if ($limiti_var["lim_prenota_temp"] < $datainserimento)
} # fine function ins_prenota_temp_in_tab


$form_orig = "";
$manda_dati_assegnazione = "SI";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$form_orig .=  "<input type=\"hidden\" name=\"num_piano$n_t\" value=\"".${"num_piano".$n_t}."\">
<input type=\"hidden\" name=\"num_casa$n_t\" value=\"".${"num_casa".$n_t}."\">
<input type=\"hidden\" name=\"num_persone_casa$n_t\" value=\"".${"num_persone_casa".$n_t}."\">";
} # fine for $n_t
$echo_dati_form = "NO";
include("./includes/dati_form_prenotazione.php");
$echo_dati_form = "";
$form_orig .= $mess_dati_form;



if ($nuovaprenotazione != "Continua lo stesso") {

$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
unset($interconnection_name);
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($ic_present[$interconnection_name] == "SI") {
include("./includes/interconnect/$mod_ext/functions_import.php");
$funz_import_reservations = "import_reservations_".$interconnection_func_name;
$id_utente_origi = $id_utente;
$id_utente = 1;
$funz_import_reservations("","",$file_interconnessioni,$anno,$PHPR_TAB_PRE,1,$id_utente,$HOSTNAME);
$id_utente = $id_utente_origi;
} # fine if ($ic_present[$interconnection_name] == "SI")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))

if ($prenota_vicine == "SI") {
$manda_dati_assegnazione = "SI";
$form_riprova_no_vicini = "";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$form_riprova_no_vicini .=  "<input type=\"hidden\" name=\"num_piano$n_t\" value=\"".${"num_piano".$n_t}."\">
<input type=\"hidden\" name=\"num_casa$n_t\" value=\"".${"num_casa".$n_t}."\">
<input type=\"hidden\" name=\"num_persone_casa$n_t\" value=\"".${"num_persone_casa".$n_t}."\">";
} # fine for $n_t
$prenota_vicine = "";
$echo_dati_form = "NO";
include("./includes/dati_form_prenotazione.php");
$prenota_vicine = "SI";
$echo_dati_form = "";
$form_riprova_no_vicini .= $mess_dati_form;
} # fine if ($prenota_vicine == "SI")

# la versione da utilizzare per la transazione che rimane se si devono usare gli app_agenzia
$tabelle_lock = array("$tableversioni");
$tabelle_lock = lock_tabelle($tabelle_lock);
$versione_transazione = prendi_numero_versione($tableversioni);
unlock_tabelle($tabelle_lock);

# Controllo che non si inseriscano prenotazioni nell'anno successivo se esistente
unset($data_in_anno_succ);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$fineperiodo = aggslashdb(${"fineperiodo".$n_t});
if (substr($fineperiodo,0,4) != $anno) {
$anno_succ_esistente = esegui_query("select idanni from $tableanni where idanni = '".($anno + 1)."'");
if (numlin_query($anno_succ_esistente) == 1) {
$data_succ_esistente = esegui_query("select idperiodi from $PHPR_TAB_PRE"."periodi".($anno + 1)." where datafine <= '$fineperiodo'");
if (numlin_query($data_succ_esistente) >= 1) $data_in_anno_succ[$n_t] = "SI";
} # fine if (numlin_query($anno_succ_esistente) == 1)
} # fine if (substr($fineperiodo,0,4) != $anno)
} # fine for $n_t

# inizio il blocco dei controlli per l'assegnazione
$tabelle_lock = array($tableprenota,$tablecostiprenota,$tabletransazioni);
$altre_tab_lock = array($tablenometariffe,$tableperiodi,$tableappartamenti,$tableclienti,$tableregole,$tablepersonalizza,$tablemessaggi,$tablerelinventario);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

if ($inserire) {
$inseriscicliente = "";
} # fine if ($inserire)

$appartamenti = esegui_query(" select * from $tableappartamenti order by idappartamenti");
$numappartamenti = numlin_query($appartamenti);
$max_maxoccupanti = 0;
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if (!$maxoccupanti) {
$max_maxoccupanti = 0;
break;
} # fine if (!$maxoccupanti)
elseif ($maxoccupanti > $max_maxoccupanti) $max_maxoccupanti = $maxoccupanti;
} # fine for $num1

$id_app_richiesti = 0;
unset($assegnazioneapp);
unset($app_richiesti);
unset($idinizioperiodo_vett);
unset($idfineperiodo_vett);
unset($idinizioperiodo_tot);
unset($idfineperiodo_tot);
unset($id_periodo_corrente);

unset($num_costi_presenti);
unset($beniinv_presenti);
unset($app_incomp_costi);
unset($app_eliminati_costi);
unset($dati_r2);

if (!function_exists('dati_tariffe')) include("./includes/funzioni_tariffe.php");
if (!function_exists('dati_costi_agg_ntariffe')) include("./includes/funzioni_costi_agg.php");
$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,"","NO","",$tableappartamenti);
$numcostiagg_orig = $numcostiagg;

dati_regole2($dati_r2,$app_regola2_predef,"","","",$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$nometipotariffa = ${"nometipotariffa".$n_t};
if (${"prenota_vicine".$n_t} or $dati_r2['napp']['v'][$nometipotariffa]) $app_richiesti[',vicini,'] = "SI";
} # fine for $n_t


for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {

${"inizioperiodo".$n_t} = aggslashdb(${"inizioperiodo".$n_t});
${"fineperiodo".$n_t} = aggslashdb(${"fineperiodo".$n_t});
${"nometipotariffa".$n_t} = aggslashdb(${"nometipotariffa".$n_t});
${"appartamento".$n_t} = htmlspecialchars(${"appartamento".$n_t});
${"appartamento".$n_t} = aggslashdb(${"appartamento".$n_t});
${"lista_app".$n_t} = htmlspecialchars(${"lista_app".$n_t});
${"lista_app".$n_t} = aggslashdb(${"lista_app".$n_t});
${"num_piano".$n_t} = aggslashdb(${"num_piano".$n_t});
${"num_casa".$n_t} = aggslashdb(${"num_casa".$n_t});
${"num_persone_casa".$n_t} = aggslashdb(${"num_persone_casa".$n_t});
$inizioperiodo = ${"inizioperiodo".$n_t};
$fineperiodo = ${"fineperiodo".$n_t};
$nometipotariffa = ${"nometipotariffa".$n_t};
$numpersone = ${"numpersone".$n_t};
$appartamento = ${"appartamento".$n_t};
$num_app_richiesti = ${"num_app_richiesti".$n_t};
$lista_app = ${"lista_app".$n_t};
$num_piano = ${"num_piano".$n_t};
$num_casa = ${"num_casa".$n_t};
$num_persone_casa = ${"num_persone_casa".$n_t};
$numcostiagg = $numcostiagg_orig;
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {

# Espando le variabili dei costi combinabili (aumentando $numcostiagg per ogni $n_t, alla fine saranno tutti uguali)
if (substr(${"idcostoagg".$numca."_".$n_t},0,1) == "c") {
$categoria = substr(${"idcostoagg".$numca."_".$n_t},1);
$num_in_cat = 0;
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($dati_ca[$num1]['mostra'] == "s" and $dati_ca[$num1]['combina'] == "s" and $dati_ca[$num1]['categoria'] == $categoria) {
$num_in_cat++;
if ($num_in_cat != 1) {
$numcostiagg++;
${"costoagg".$numcostiagg."_".$n_t} = ${"costoagg".$numca."_".$n_t};
${"idcostoagg".$numcostiagg."_".$n_t} = $dati_ca[$num1]['id'];
${"numsettimane".$numcostiagg."_".$n_t} = ${"numsettimane".$numca."_".$n_t};
${"nummoltiplica_ca".$numcostiagg."_".$n_t} = ${"nummoltiplica_ca".$numca."_".$n_t};
} # fine else if ($num_in_cat == 1)
else ${"idcostoagg".$numca."_".$n_t} = $dati_ca[$num1]['id'];
} # fine if ($dati_ca[$num1]['mostra'] == "s" and $dati_ca[$num1]['combina'] == "s" and...
} # fine for $num1
if (!$num_in_cat) $inserire = "NO";
} # fine if (substr(${"idcostoagg".$numca."_".$n_t},0,1) == "c")

${"costoagg".$numca} = aggslashdb(${"costoagg".$numca."_".$n_t});
${"idcostoagg".$numca} = aggslashdb(${"idcostoagg".$numca."_".$n_t});
${"numsettimane".$numca} = aggslashdb(${"numsettimane".$numca."_".$n_t});
${"nummoltiplica_ca".$numca} = aggslashdb(${"nummoltiplica_ca".$numca."_".$n_t});
${"id_periodi_costo".$numca} = aggslashdb(${"id_periodi_costo".$numca."_".$n_t});
} # fine for $numca
unset(${"spezzetta".$n_t});


$inizioperiodo_orig[$n_t] = $inizioperiodo;
$fineperiodo_orig[$n_t] = $fineperiodo;
$idinizioperiodo = esegui_query("select idperiodi from $tableperiodi where datainizio = '$inizioperiodo' ");
$num_idinizioperiodo = numlin_query($idinizioperiodo);
if ($num_idinizioperiodo == 0) $idinizioperiodo = 100000;
else $idinizioperiodo = risul_query($idinizioperiodo,0,'idperiodi');
${"inizioperiodo".$n_t} = $idinizioperiodo;
if (!$idinizioperiodo_tot or $idinizioperiodo < $idinizioperiodo_tot) $idinizioperiodo_tot = $idinizioperiodo;
$idfineperiodo = esegui_query("select idperiodi from $tableperiodi where datafine = '$fineperiodo' ");
$num_idfineperiodo = numlin_query($idfineperiodo);
if ($num_idfineperiodo == 0) $idfineperiodo = -1;
else $idfineperiodo = risul_query($idfineperiodo,0,'idperiodi');
${"fineperiodo".$n_t} = $idfineperiodo;
if (!$idfineperiodo_tot or $idfineperiodo > $idfineperiodo_tot) $idfineperiodo_tot = $idfineperiodo;
$app_diversi_occ = 0;

if ($priv_ins_periodi_passati != "s") {
if (!$id_periodo_corrente) $id_periodo_corrente = calcola_id_periodo_corrente($anno);
if ($id_periodo_corrente >= $idinizioperiodo) $data_sbagliata = "SI";
} # fine if ($priv_ins_periodi_passati != "s")
if ($data_in_anno_succ[$n_t] == "SI") $data_sbagliata = "SI";

if ($modifica_pers == "NO") {
@include(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php");
if (!strpos($dates_options_list,$inizioperiodo)) $data_sbagliata = "SI";
if (!strpos($dates_options_list,$fineperiodo)) $data_sbagliata = "SI";
} # fine if ($modifica_pers == "NO")

if ($idfineperiodo < $idinizioperiodo or $data_sbagliata == "SI") {
$inserire = "NO";
echo mex("Le date sono sbagliate",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($idfineperiodo < $idinizioperiodo or...

else {
if ($priv_ins_num_persone != "s") {
unset($numpersone);
unset(${"numpersone".$n_t});
} # fine if ($priv_ins_num_persone != "s")

if ($nometipotariffa == "") {
$inserire = "NO";
echo mex("Si deve inserire il tipo di tariffa",$pag);
if ($num_tipologie > 1) echo "(".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($nometipotariffa == "")

else {
if ($priv_ins_nuove_prenota != "s") $inserire = "NO";
$tipotariffa = $nometipotariffa;
$tipotariffa_vedi = mex("tariffa",$pag).substr($tipotariffa,7);
if (($attiva_tariffe_consentite == "s" and $tariffe_consentite_vett[substr($tipotariffa,7)] != "SI") or substr($tipotariffa,0,7) != "tariffa") $inserire = "NO";

if ($priv_ins_assegnazione_app != "s") {
unset($appartamento);
unset($lista_app);
unset($num_casa);
unset($num_piano);
unset($num_persone_casa);
} # fine if ($priv_ins_assegnazione_app != "s")

# se vi è una regola 4 per la tariffa
if (!$numpersone) {
$regole4 = esegui_query("select * from $tableregole where tariffa_per_persone = '$tipotariffa'");
if (numlin_query($regole4) == 1) {
$numpersone = risul_query($regole4,0,'iddatainizio');
${"numpersone".$n_t} = $numpersone;
} # fine if numlin_query($regole4) == 1)
} # fine if (!$numpersone)

# se vi è una regola 2 per la tariffa
unset($app_regola2_predef);
${"interrompi_vicine_ogni".$n_t} = "";
${"diff_persone".$n_t} = "";
if (!$appartamento and !$lista_app and !$num_casa and !$num_piano and !$num_persone_casa) {
$lista_app = dati_regole2($dati_r2,$app_regola2_predef,$tipotariffa,$idinizioperiodo,$idfineperiodo,$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
${"lista_app".$n_t} = $lista_app;
if ($lista_app and $dati_r2['napp'][$tipotariffa]) {
if ($numpersone) {
$numpersone_orig = $numpersone;
$numpersone = ceil((double) $numpersone / (double) $dati_r2['napp'][$tipotariffa]);
${"diff_persone".$n_t} = ((int) $numpersone * (int) $dati_r2['napp'][$tipotariffa]) - (int) $numpersone_orig;
} # fine if ($numpersone)
if ($num_app_richiesti > 1) {
if (${"diff_persone".$n_t}) ${"diff_persone".$n_t} = ${"diff_persone".$n_t} * $num_app_richiesti;
$num_app_richiesti = $num_app_richiesti * $dati_r2['napp'][$tipotariffa];
# Se gli appartamenti sono vicini solo per la regola 2 devo introdurre dei separatori di vicinanza
if (!${"prenota_vicine".$n_t} and $prenota_vicine != "SI" and $dati_r2['napp']['v'][$tipotariffa]) ${"interrompi_vicine_ogni".$n_t} = $dati_r2['napp'][$tipotariffa];
} # fine if ($num_app_richiesti > 1)
else $num_app_richiesti = $dati_r2['napp'][$tipotariffa];
if ($dati_r2['napp']['v'][$tipotariffa]) ${"prenota_vicine".$n_t} = 1;
${"numpersone".$n_t} = $numpersone;
${"num_app_richiesti".$n_t} = $num_app_richiesti;
} # fine if ($lista_app and $dati_r2['napp'][$tipotariffa])
} # fine if (!$appartamento and !$lista_app and !$num_casa and !$num_piano and !$num_persone_casa)

# se vi è una regola 3 per la tariffa
if ($id_utente == 1) {
$regole3 = esegui_query("select * from $tableregole where tariffa_per_utente = '$tipotariffa'");
if (numlin_query($regole3) == 1) {
$id_utente_ins_tariffa = risul_query($regole3,0,'iddatainizio');
if ($n_t == 1) $id_utente_ins = $id_utente_ins_tariffa;
else if ($id_utente_ins != $id_utente_ins_tariffa) unset($id_utente_ins);
} # fine if numlin_query($regole3) == 1)
} # fine if ($id_utente == 1)

if (!$numpersone) {
if ($dati_tariffe[$tipotariffa]['moltiplica'] == "p") {
$inserire = "NO";
echo mex("Si deve inserire il numero delle persone per questa tariffa",$pag);
if ($num_tipologie > 1) echo "(".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($dati_tariffe[$tipotariffa]['moltiplica'] == "p")
} # fine if (!$numpersone)

$tariffa_chiusa = 0;
for ($num = $idinizioperiodo; $num <= $idfineperiodo; $num = $num + 1) {
$prenotazioni = esegui_query("select * from $tableprenota where iddatainizio <= $num and iddatafine >= $num");
$numprenotazioni = numlin_query($prenotazioni);
$rigasettimana = esegui_query("select * from $tableperiodi where idperiodi = '$num' ");
$esistetariffa = risul_query($rigasettimana,0,$tipotariffa);
$esistetariffap = risul_query($rigasettimana,0,$tipotariffa."p");
if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and (!strcmp($esistetariffap,"") or $esistetariffap < 0)) {
$inserire = "NO";
$inizioperiodotariffa = risul_query($rigasettimana,0,'datainizio');
$inizioperiodotariffa_f = formatta_data($inizioperiodotariffa,$stile_data);
$fineperiodotariffa = risul_query($rigasettimana,0,'datafine');
$fineperiodotariffa_f = formatta_data($fineperiodotariffa,$stile_data);
echo mex("Non è stato ancora inserito il prezzo della",$pag)." $tipotariffa_vedi ".mex("per $parola_la $parola_settimana dal",$pag)." $inizioperiodotariffa_f ".mex("al",$pag)." $fineperiodotariffa_f";
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (($esistetariffa == "" or $esistetariffa < 0) and
if ($numprenotazioni >= $numappartamenti) {
$inserire = "NO";
$inizioperiodopieno = risul_query($rigasettimana,0,'datainizio');
$inizioperiodopieno_f = formatta_data($inizioperiodopieno,$stile_data);
$fineperiodopieno = risul_query($rigasettimana,0,'datafine');
$fineperiodopieno_f = formatta_data($fineperiodopieno,$stile_data);
echo mex("$parola_La $parola_settimana dal",$pag)." $inizioperiodopieno_f ".mex("al",$pag)." $fineperiodopieno_f ".mex("è pien$lettera_a",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($numprenotazioni >= $numappartamenti)
if ($dati_tariffe[$tipotariffa]['chiusa'][$num]) $tariffa_chiusa = 1;
} # fine for $num
if ($tariffa_chiusa) {
$inserire = "NO";
echo mex("La tariffa richiesta è chiusa in questo periodo",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine ($tariffa_chiusa)

${"sconto".$n_t} = formatta_soldi(${"sconto".$n_t});
if (controlla_soldi(${"sconto".$n_t}) == "NO" or (strcmp(${"sconto".$n_t},"") and ${"tipo_sconto".$n_t} != "sconto" and ${"tipo_val_sconto".$n_t}) or (${"tipo_val_sconto".$n_t} and ${"sconto".$n_t} > 100)) {
$inserire = "NO";
echo ucfirst(mex("lo sconto è sbagliato",$pag));
if ($num_tipologie > 1) echo "(".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (controlla_soldi(${"sconto".$n_t}) == "NO" or...
${"caparra".$n_t} = formatta_soldi(${"caparra".$n_t});
if (controlla_soldi(${"caparra".$n_t}) == "NO") {
$inserire = "NO";
echo ucfirst(mex("la caparra è sbagliata",$pag));
if ($num_tipologie > 1) echo "(".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (controlla_soldi(${"caparra".$n_t}) == "NO")
${"commissioni".$n_t} = formatta_soldi(${"commissioni".$n_t});
if (controlla_soldi(${"commissioni".$n_t}) == "NO") {
$inserire = "NO";
echo ucfirst(mex("le commissioni sono sbagliate",$pag));
if ($num_tipologie > 1) echo "(".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (controlla_soldi(${"commissioni".$n_t}) == "NO")

} # fine else if ($tipotariffa == "")

if ($cognome == "") {
$inserire = "NO";
} # fine if ($cognome == "")


unset(${"num_letti_agg".$n_t});
${"num_letti_agg".$n_t}['max'] = 0;
unset($settimane_costo);
unset($moltiplica_costo);
unset($costo_aggiungi_letti);
$num_costi_associati = 0;
$lunghezza_periodo = $idfineperiodo - $idinizioperiodo + 1;
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num1]['id']] == "SI") {
if ($dati_ca[$num1]["tipo_associa_".$nometipotariffa] == "r") $periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num1,$idinizioperiodo,$idfineperiodo,1);
if ($dati_ca[$num1]["tipo_associa_".$nometipotariffa] == "s" or ($dati_ca[$num1]["tipo_associa_".$nometipotariffa] == "r" and $periodo_costo_trovato != "NO")) {
if (associa_costo_a_tariffa($dati_ca,$num1,$nometipotariffa,$lunghezza_periodo) == "SI") {
$num_costi_associati++;
${"costoagg".($numcostiagg + $num_costi_associati)} = "SI";
${"idcostoagg".($numcostiagg + $num_costi_associati)} = $dati_ca[$num1]['id'];
} # fine if (associa_costo_a_tariffa($dati_ca,$num1,$nometipotariffa,$lunghezza_periodo) == "SI")
else {
if ($dati_ca[$num1]["tipo_associa_".${"nometipotariffa".$n_t}] == "r" and $dati_ca[$num1]['tipo'] == "s") $sett_costo = calcola_settimane_costo($tableperiodi,$dati_ca,$num1,$idinizioperiodo,$idfineperiodo,"","");
else $sett_costo = 1;
if ($sett_costo) {
$inserire = "NO";
echo mex("Il costo aggiuntivo",$pag)." \"".$dati_ca[$num1]['nome']."\" ".mex("non può essere applicato",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($sett_costo)
} # fine else if (associa_costo_a_tariffa($dati_ca,$num1,$nometipotariffa,$lunghezza_periodo) == "SI")
} # fine if ($dati_ca[$num1]["tipo_associa_".$nometipotariffa] == "s" or...
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine for $num1

for ($numca = 1 ; $numca <= ($numcostiagg + $num_costi_associati) ; $numca++) {
$costoagg = "costoagg".$numca;
if ($$costoagg == "SI") {
$idcostoagg = "idcostoagg".$numca;
$numsettimane = "numsettimane".$numca;
$nummoltiplica_ca = "nummoltiplica_ca".$numca;
$id_periodi_costo = "id_periodi_costo".$numca;
if ($$numsettimane) {
if ($$numsettimane > $lunghezza_periodo) {
$inserire = "NO";
echo mex("Il numero di $parola_settimane del costo aggiuntivo $parola_settimanale nº",$pag)." $numca ".mex("supera il numero totale di $parola_settimane della prenotazione",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($$numsettimane > $lunghezza_periodo)
} # fine if ($$numsettimane)
if (($$numsettimane and controlla_num_pos($$numsettimane) == "NO") or ($$nummoltiplica_ca and controlla_num_pos($$nummoltiplica_ca) == "NO")) {
$inserire = "NO";
echo mex("I dati di un costo aggiuntivo sono errati",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (controlla_num_pos($$numsettimane) == "NO" or...
$num_costo = $dati_ca['id'][$$idcostoagg];
if ($dati_ca[$num_costo]["incomp_".$nometipotariffa] == "i") {
if ($dati_ca[$num_costo]['combina'] == "s") $$costoagg = "";
else {
$inserire = "NO";
echo mex("Il costo aggiuntivo",$pag)." \"".$dati_ca[$num_costo]['nome']."\" ".mex("è incompatibile con la tariffa selezionata",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine else if ($dati_ca[$num_costo]['combina'] == "s")
} # fine if ($dati_ca[$num_costo]["incompat_".$nometipotariffa] == "i")
$numsettimane_aux = $$numsettimane;
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$numsettimane_aux);
if ($periodo_costo_trovato == "NO") {
if ($dati_ca[$num_costo]['combina'] == "s") $$costoagg = "";
else {
$inserire = "NO";
echo mex("Non si puo inserire il costo aggiuntivo",$pag)." \"".$dati_ca[$num_costo]['nome']."\" ".mex("in questo periodo",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine else if ($dati_ca[$num_costo]['combina'] == "s")
} # fine if ($periodo_costo_trovato == "NO")
${"costoagg".$numca."_".$n_t} = $$costoagg;

if ($$costoagg == "SI") {
if ($$id_periodi_costo == "inserire") {
$$id_periodi_costo = "";
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
if (${"sett".$num1."costo".$numca."_".$n_t} == "SI") $$id_periodi_costo .= ",".$num1;
} # fine for $num1
if ($$id_periodi_costo) $$id_periodi_costo .= ",";
else $$id_periodi_costo = "nessuno";
${"id_periodi_costo".$numca."_".$n_t} = $$id_periodi_costo;
} # fine if ($$id_periodi_costo == "inserire")
if ($dati_ca[$num_costo]['numsett'] == "c" and $dati_ca[$num_costo]['associasett'] == "s" and $inserire != "NO" and !$$id_periodi_costo) {
$inserire = "NO";
echo "<hr align=\"left\" width=\"30%\">
".mex("Scegliere $parola_le $parola_settimane in cui applicare il costo aggiuntivo",$pag)." ".$dati_ca[$num_costo]['nome'];
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ":<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"clienti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"$origine\">";
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) ${"ck_sett".$num1} = "";
if ($idmessaggi) {
$id_periodi_mess = esegui_query("select dati_messaggio9,dati_messaggio10,dati_messaggio14 from $tablemessaggi where tipo_messaggio = 'rprenota' and idutenti $LIKE '%,$id_utente,%' and idmessaggi = '".aggslashdb($idmessaggi)."' ");
if (numlin_query($id_periodi_mess) == 1) {
$numcostiagg_mess = explode(",",risul_query($id_periodi_mess,0,'dati_messaggio9'));
$idcostoagg_mess = explode(";",risul_query($id_periodi_mess,0,'dati_messaggio10'));
$id_periodi_mess = explode(";",risul_query($id_periodi_mess,0,'dati_messaggio14'));
$numcostiagg_mess = $numcostiagg_mess[($n_t - 1)];
$idcostoagg_mess = explode(",",$idcostoagg_mess[($n_t - 1)]);
$id_periodi_mess = explode(":",$id_periodi_mess[($n_t - 1)]);
for ($numca_m = 1 ; $numca_m <= $numcostiagg_mess ; $numca_m++) {
if ($$idcostoagg == $idcostoagg_mess[($numca_m - 1)]) {
$id_periodi_mess = explode(",",$id_periodi_mess[($numca_m - 1)]);
for ($num_pm = 0 ; $num_pm < count($id_periodi_mess) ; $num_pm++) ${"ck_sett".$id_periodi_mess[$num_pm]} = " checked";
break;
} # fine ($$idcostoagg == $idcostoagg_mess[($numca_m - 1)])
} # fine for $numca_m
} # fine if (numlin_query($id_periodi_mess) == 1)
} # fine if ($idmessaggi)
for ($num1 = $idinizioperiodo; $num1 <= $idfineperiodo; $num1++) {
$periodo_costo_trovato = "NO";
if ($dati_ca[$num_costo]['periodipermessi'] == "p") {
for ($num2 = 0 ; $num2 < count($dati_ca[$num_costo]['sett_periodipermessi_ini']) ; $num2++) {
if ($dati_ca[$num_costo]['sett_periodipermessi_ini'][$num2] <= $num1 and $dati_ca[$num_costo]['sett_periodipermessi_fine'][$num2] >= $num1) $periodo_costo_trovato = "SI";
} # fine for $num2
} # fine if ($dati_ca[$num_costo][periodipermessi] == "p")
else $periodo_costo_trovato = "SI";
if ($periodo_costo_trovato == "SI") {
$date_sett_costo = esegui_query("select datainizio,datafine from $tableperiodi where idperiodi = '$num1'");
echo "<label><input type=\"checkbox\" name=\"sett$num1"."costo$numca"."_$n_t\" value=\"SI\"".${"ck_sett".$num1}.">".mex("dal",$pag)."
 ".formatta_data(risul_query($date_sett_costo,0,'datainizio'),$stile_data)." ".mex("al",$pag)." 
 ".formatta_data(risul_query($date_sett_costo,0,'datafine'),$stile_data)."</label><br>";
} # fine if ($periodo_costo_trovato == "SI")
} # fine for $num1
${"id_periodi_costo".$numca."_".$n_t} = "inserire";
for ($n_t2 = 1 ; $n_t2 <= $n_t ; $n_t2++) {
${"inizioperiodo".$n_t2} = $inizioperiodo_orig[$n_t2];
${"fineperiodo".$n_t2} = $fineperiodo_orig[$n_t2];
} # fine for $n_t2
include("./includes/dati_form_prenotazione.php");
for ($n_t2 = 1 ; $n_t2 <= $n_t ; $n_t2++) {
${"inizioperiodo".$n_t2} = $idinizioperiodo;
${"fineperiodo".$n_t2} = $idfineperiodo;
} # fine for $n_t2
echo "<input type=\"hidden\" name=\"nuovaprenotazione\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" name=\"ins_nuova_prenota\" value=\"".mex("Continua",$pag)."\">
</div></form><hr align=\"left\" width=\"30%\">";
} # fine if ($dati_ca[$num_costo][numsett] == "c" and $dati_ca[$num_costo][associasett] == "s" and...
else {
$id_periodi_costo_aux = $$id_periodi_costo;
$numsettimane_aux = $$numsettimane;
$num_letti_agg_aux = ${"num_letti_agg".$n_t};
$nummoltiplica_ca_aux = $$nummoltiplica_ca;
$settimane_costo[$numca] = calcola_settimane_costo($tableperiodi,$dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$id_periodi_costo_aux,$numsettimane_aux);
aggiorna_letti_agg_in_periodi($dati_ca,$num_costo,$num_letti_agg_aux,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],"",$nummoltiplica_ca_aux,$numpersone);
} # fine else if ($dati_ca[$num_costo][numsett] == "c" and $dati_ca[$num_costo][associasett] == "s" and...
} # fine if ($$costoagg == "SI")

if (($dati_ca[$num_costo]['moltiplica'] == "p" or $dati_ca[$num_costo]['moltiplica'] == "t") and !$numpersone) {
$inserire = "NO";
echo mex("Si deve inserire il numero delle persone per il costo aggiuntivo",$pag)." ".$dati_ca[$num_costo]['nome'];
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (($dati_ca[$num1]['moltiplica'] == "p" or $dati_ca[$num1]['moltiplica'] == "t") and !$numpersone)
} # fine if ($$costoagg == "SI")
} # fine for $numca


if ($inserire != "NO") {
if ($num_app_richiesti > 1) $num_controlla_limite = $num_app_richiesti;
else $num_controlla_limite = 1;
for ($numca = 1 ; $numca <= ($numcostiagg + $num_costi_associati) ; $numca++) {
$costoagg = "costoagg".$numca;
if ($$costoagg == "SI") {
$nummoltiplica_ca = "nummoltiplica_ca".$numca;
$idcostoagg = "idcostoagg".$numca;
$num_costo = $dati_ca['id'][$$idcostoagg];
$nummoltiplica_ca_aux = $$nummoltiplica_ca;
$num_letti_agg_aux = ${"num_letti_agg".$n_t};
calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica_costo[$numca],$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$nummoltiplica_ca_aux,$numpersone,$num_letti_agg_aux);
if ($dati_ca[$num_costo]['mostra'] == "s" and $dati_ca[$num_costo]['letto'] == "s") $costo_aggiungi_letti = 1;
$limite_costo_raggiunto = "NO";
for ($num1 = 0 ; $num1 < $num_controlla_limite ; $num1++) if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo[$numca]) == "NO") $limite_costo_raggiunto = "SI";
if ($limite_costo_raggiunto == "SI") {
$inserire = "NO";
if ($messaggio_costo_lim[$$idcostoagg] != "SI") {
$messaggio_costo_lim[$$idcostoagg] = "SI";
echo mex("Non si possono inserire altri costi",$pag)." ".$dati_ca[$num_costo]['nome']." ".mex("in questo periodo, già",$pag)." ".$dati_ca[$num_costo]['numlimite']." ".mex("presenti",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($messaggio_costo_lim[$$idcostoagg] != "SI")
} # fine if ($limite_costo_raggiunto == "SI")
if ($dati_ca[$num_costo]['tipo_beniinv'] == "mag" and $inserire != "NO") {
$nrc = "";
for ($num1 = 0 ; $num1 < $num_controlla_limite ; $num1++) {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti,$nrc,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo[$numca],"");
if ($risul != "SI") break;
} # fine for $num1
if ($risul != "SI") {
$inserire = "NO";
echo mex("I beni richiesti dal costo",$pag)." \"".$dati_ca[$num_costo]['nome']."\" ".mex("non sono disponibili nell'inventario",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($risul != "SI")
} # fine if ($dati_ca[$num1]['tipo_beniinv'] == "mag" and $inserire != "NO")
if ($dati_ca[$num_costo]['moltiplica'] == "c" and $dati_ca[$num_costo]['molt_max'] != "x") {
$num_max = 0;
if ($dati_ca[$num_costo]['molt_max'] == "n") $num_max = $dati_ca[$num_costo]['molt_max_num'];
if ($dati_ca[$num_costo]['molt_max'] != "n" and $numpersone) $num_max = $numpersone;
if ($dati_ca[$num_costo]['molt_max'] == "t" and $num_letti_agg_aux['max']) $num_max += $num_letti_agg_aux['max'];
if ($num_max) {
if ($dati_ca[$num_costo]['molt_max'] != "n" and $dati_ca[$num_costo]['molt_max_num']) $num_max = $num_max - $dati_ca[$num_costo]['molt_max_num'];
if ($$nummoltiplica_ca > $num_max) {
$inserire = "NO";
echo mex("Il costo aggiuntivo",$pag)." \"".$dati_ca[$num_costo]['nome']."\" ".mex("non può essere moltiplicato per più di",$pag)." $num_max";
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($$nummoltiplica_ca > $num_max)
} # fine if ($num_max)
} # fine if ($dati_ca[$num_costo]['moltiplica'] == "c" and $dati_ca[$num1]['molt_max'] != "x")
} # fine if ($$costoagg == "SI")
} # fine for $numca
} # fine if ($inserire != "NO")


${"app_richiesti".$n_t} = array();
if ($appartamento) {
$appartamento = aggslashdb($appartamento);
${"assegnazioneapp".$n_t} = "k";
${"app_richiesti".$n_t}[$appartamento] = "SI";
${"lista_app".$n_t} = $appartamento;
#$inserire = "NO";
#echo "L'appartamento $appartamento è già occupato nel periodo richiesto.<br>";
} # fine if ($appartamento)
else {
if ($lista_app) {
${"assegnazioneapp".$n_t} = "c";
$vett_app = explode(",",$lista_app);
$num_app = count($vett_app);
for ($num1 = 0 ; $num1 < $num_app ; $num1 = $num1 + 1) {
$appo = aggslashdb($vett_app[$num1]);
${"app_richiesti".$n_t}[$appo] = "SI";
$appartamento_esistente = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '$appo'");
if (numlin_query($appartamento_esistente) != 1) {
$inserire = "NO";
echo mex("L'appartamento",'unit.php')." <div style=\"display: inline; color: red;\">$appo</div> ".mex("contenuto nella lista non esiste",'unit.php');
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (numlin_query($appartamento_esistente) != 1)
} # fine for $num1
} # fine if ($lista_app)
else {
if ($num_piano or $num_casa or $num_persone_casa) {
$where_immesso = "";
${"assegnazioneapp".$n_t} = "c";
$query = "select idappartamenti from $tableappartamenti ";
if ($num_piano) {
$num_piano = aggslashdb($num_piano);
$query = $query."where numpiano = '$num_piano' ";
$where_immesso = "SI";
} # fine if ($num_piano)
if ($num_casa) {
$num_casa = aggslashdb($num_casa);
if ($where_immesso) $query = $query."and numcasa = '$num_casa' ";
else $query = $query."where numcasa = '$num_casa' ";
$where_immesso = "SI";
} # fine if ($num_casa)
if ($num_persone_casa) {
$num_persone_casa = aggslashdb($num_persone_casa);
if ($where_immesso) $query .= "and maxoccupanti = '$num_persone_casa' ";
else $query .= "where maxoccupanti = '$num_persone_casa' ";
} # fine if ($num_persone_casa)
$list_idapp_ric = esegui_query($query);
$num_app_ric = numlin_query($list_idapp_ric);
for ($num1 = 0 ; $num1 < $num_app_ric ; $num1 = $num1 + 1) {
$idapp_ric = risul_query($list_idapp_ric,$num1,'idappartamenti');
${"app_richiesti".$n_t}[$idapp_ric] = "SI";
if (!${"lista_app".$n_t}) ${"lista_app".$n_t} = $idapp_ric;
else ${"lista_app".$n_t} = ${"lista_app".$n_t}.",".$idapp_ric;
} # fine for $num1
if ($num_app_ric == 0) {
$inserire = "NO";
echo mex("Non ci sono appartamenti con le caratteristiche richieste",'unit.php');
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($num_app_ric == 0)
} # fine if ($num_piano or $num_casa or $num_persone_casa)
else ${"assegnazioneapp".$n_t} = "v";
} # fine else if ($lista_app)
} # fine else if ($appartamento)


# se vi sono costi con appartamenti incompatibili
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
$costoagg = "costoagg".$numca;
$idcostoagg = "idcostoagg".$numca;
if ($priv_ins_costi_agg != "s" or ($attiva_costi_agg_consentiti != "n" and $costi_agg_consentiti_vett[$$idcostoagg] != "SI")) $$costoagg = "";
if ($$costoagg == "SI" and $dati_ca[$dati_ca['id'][$$idcostoagg]]['appincompatibili']) $app_incomp_costi[$n_t] .= ",".$dati_ca[$dati_ca['id'][$$idcostoagg]]['appincompatibili'];
} # fine for $numca
if ($app_incomp_costi[$n_t]) {
$app_incomp_costi[$n_t] .= ",";
$lista_app = ${"lista_app".$n_t};
${"lista_app".$n_t} = "";
if ($lista_app) $lista_app = ",$lista_app,";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if (!$lista_app or str_replace(",$idapp,","",$lista_app) != $lista_app) {
if (str_replace(",$idapp,","",$app_incomp_costi[$n_t]) == $app_incomp_costi[$n_t]) ${"lista_app".$n_t} .= ",$idapp";
else $app_eliminati_costi[$n_t] .= ",$idapp";
} # fine if (!$lista_app or..
} # fine for $num1
if (${"lista_app".$n_t}) {
${"lista_app".$n_t} = substr(${"lista_app".$n_t},1);
$app_eliminati_costi[$n_t] = substr($app_eliminati_costi[$n_t],1);
$lista_app = ${"lista_app".$n_t};
if (str_replace(",","",$lista_app) != $lista_app) ${"assegnazioneapp".$n_t} = "c";
else ${"assegnazioneapp".$n_t} = "k";
${"app_richiesti".$n_t} = array();
$vett_app = explode(",",$lista_app);
$num_app = count($vett_app);
for ($num1 = 0 ; $num1 < $num_app ; $num1++) ${"app_richiesti".$n_t}[$vett_app[$num1]] = "SI";
} # fine if (${"lista_app".$n_t})
else {
echo mex("Non c'è nessun appartamento tra quelli richiesti che sia compatibile con i costi aggiuntivi selezionati",'unit.php');
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
$inserire = "NO";
} # fine else if (${"lista_app".$n_t})
} # fine if ($app_incomp_costi[$n_t])

# se vi sono costi con beni inventario dall'appartamento
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
$costoagg = "costoagg".$numca;
$idcostoagg = "idcostoagg".$numca;
$num_costo = $dati_ca['id'][$$idcostoagg];
if ($$costoagg == "SI" and $dati_ca[$num_costo]['tipo_beniinv'] == "app") {
$app_richiesti_copia = ${"app_richiesti".$n_t};
$num_ripetizioni_costo = "";
$posto = "NO";
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI") {
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti,$num_ripetizioni_costo,"",$idinizioperiodo,$idfineperiodo,$settimane_costo[$numca],$moltiplica_costo[$numca],$idapp);
if ($risul != "SI") {
${"app_richiesti".$n_t}[$idapp] = "NO";
$app_incomp_costi = "SI";
} # fine if ($risul != "SI")
else {
${"app_richiesti".$n_t}[$idapp] = "SI";
$posto = "SI";
} # fine else if ($risul != "SI")
} # fine if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI")
} # fine for $num1
if ($posto != "SI") {
$inserire = "NO";
echo mex("I beni richiesti dal costo",$pag)." \"".$dati_ca[$num_costo]['nome']."\" ".mex("non sono disponibili nell'inventario",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if ($posto != "SI")
} # fine if ($$costoagg == "SI" and $dati_ca[$num_costo]['tipo_beniinv'] == "app")
} # fine for $numca


if (controlla_num_pos($num_app_richiesti) == "NO" or $num_app_richiesti == 0 or strlen($num_app_richiesti) > 3) {
$inserire = "NO";
echo mex("Il numero di prenotazioni è sbagliato",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (controlla_num_pos($num_app_richiesti) == "NO" or $num_app_richiesti == 0 or strlen($num_app_richiesti) > 3))

if ($attiva_regole1_consentite == "s") {
unset($condizioni_regole1_consentite);
unset($app_richiesti_copia);
unset(${"lista_app".$n_t});
if (${"assegnazioneapp".$n_t} == "v") ${"assegnazioneapp".$n_t} = "c";
for ($num1 = 0 ; $num1 < count($regole1_consentite) ; $num1++) if ($regole1_consentite[$num1]) $condizioni_regole1_consentite .= "motivazione = '".$regole1_consentite[$num1]."' or ";
if (!$condizioni_regole1_consentite) {
echo mex("Non c'è nussun periodo delle regole 1 in cui sia consentito inserire prenotazioni per l'utente",$pag)." $nome_utente.<br>";
$inserire = "NO";
} # fine if (!$condizioni_regole1_consentite)
else {
$condizioni_regole1_consentite = "(".str_replace("motivazione = ' '","motivazione = '' or motivazione is null",substr($condizioni_regole1_consentite,0,-4)).")";
$app_richiesti_copia = ${"app_richiesti".$n_t};
$posti = 0;
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1++) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI") {
$appartamento_consentito = esegui_query("select idregole,iddatainizio,iddatafine from $tableregole where app_agenzia = '$idapp' and (motivazione2 != 'x' or motivazione2 is NULL) and iddatainizio <= '$idfineperiodo' and iddatafine >= '$idinizioperiodo' and $condizioni_regole1_consentite order by iddatainizio");
unset($iddatainizio_regole_tot);
unset($iddatafine_regole_tot);
for ($num2 = 0 ; $num2 < numlin_query($appartamento_consentito) ; $num2++) {
$iddatainizio_regola = risul_query($appartamento_consentito,$num2,'iddatainizio');
$iddatafine_regola = risul_query($appartamento_consentito,$num2,'iddatafine');
if ($num2 == 0) {
$iddatainizio_regole_tot = $iddatainizio_regola;
$iddatafine_regole_tot = $iddatafine_regola;
} # fine if ($num2 == 0)
else {
if ($iddatainizio_regola == ($iddatafine_regole_tot + 1)) $iddatafine_regole_tot = $iddatafine_regola;
else break;
} # fine else if ($num2 == 0)
} # fine for $num2
if (numlin_query($appartamento_consentito) > 0 and $iddatainizio_regole_tot <= $idinizioperiodo and $iddatafine_regole_tot >= $idfineperiodo) {
${"app_richiesti".$n_t}[$idapp] = "SI";
$posti++;
${"lista_app".$n_t} .= $idapp.",";
} # fine if (numlin_query($appartamento_consentito) > 0 and...)
else ${"app_richiesti".$n_t}[$idapp] = "NO";
} # fine if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI")
} # fine for $num1
${"lista_app".$n_t} = substr(${"lista_app".$n_t},0,-1);
if ($posti == 0) {
echo mex("Non c'è nessun appartamento tra quelli richiesti in cui sia consentito inserire prenotazioni per l'utente",'unit.php')." $nome_utente";
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
$inserire = "NO";
} # fine if ($posti == 0)
else {
if ($posti < $num_app_richiesti) {
echo mex("Non ci sono",'unit.php')." $num_app_richiesti ".mex("appartamenti tra quelli richiesti in cui sia consentito inserire prenotazioni per l'utente",'unit.php')." $nome_utente";
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
$inserire = "NO";
} # fine if ($posti < $num_app_richiesti)
} # fine else if ($posti == 0)
} # fine else if (!$condizioni_regole1_consentite)
if ($inserire == "NO" and $app_richiesti_copia) ${"app_richiesti".$n_t} = $app_richiesti_copia;
} # fine if ($attiva_regole1_consentite == "s")

if ($numpersone) {
if (controlla_num_pos($numpersone) == "NO") {
$inserire = "NO";
echo mex("Il numero di persone è sbagliato",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
} # fine if (controlla_num_pos($numpersone) == "NO")
$app_richiesti_copia = ${"app_richiesti".$n_t};
$posti = 0;
if ($app_regola2_predef) {
$app_regola2_predef = ",$app_regola2_predef,";
$posto_reg2_orig = 0;
} # fine if ($app_regola2_predef)
for ($num1 = 0 ; $num1 < $numappartamenti ; $num1 = $num1 + 1) {
$idapp = risul_query($appartamenti,$num1,'idappartamenti');
$maxoccupanti = risul_query($appartamenti,$num1,'maxoccupanti');
if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI") {
if ($maxoccupanti and $maxoccupanti < $numpersone) {
${"app_richiesti".$n_t}[$idapp] = "NO";
} # fine if ($maxoccupanti and $maxoccupanti < $numpersone)
else {
${"app_richiesti".$n_t}[$idapp] = "SI";
$posti++;
} # fine else if ($maxoccupanti and $maxoccupanti < $numpersone)
} # fine if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI")
if ($app_regola2_predef) {
if (str_replace(",$idapp,","",$app_regola2_predef) != $app_regola2_predef) {
if (!$maxoccupanti or $maxoccupanti >= $numpersone) $posto_reg2_orig = 1;
} # fine if (str_replace(",$idapp,","",$app_regola2_predef) != $app_regola2_predef)
} # fine if ($app_regola2_predef)
} # fine for $num1

# Se non c'è posto per questo numero di persone provo ad aggiungere un costo con un letto aggiuntivo
if (($posti < $num_app_richiesti or ($app_regola2_predef and !$posto_reg2_orig)) and !$costo_aggiungi_letti) {
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($dati_ca[$num1]['mostra'] == "s" and $dati_ca[$num1]['letto'] == "s") {
if ($priv_ins_costi_agg == "s" and ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num1]['id']] == "SI")) {
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
if (${"idcostoagg".$numca} == $dati_ca[$num1]['id']) {
if (($dati_ca[$num1]['numsett'] != "c" or $dati_ca[$num1]['associasett'] != "s") and $dati_ca[$num1]["incomp_".$nometipotariffa] != "i") {
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num1,$idinizioperiodo,$idfineperiodo,($idfineperiodo - $idinizioperiodo + 1));
if ($periodo_costo_trovato != "NO") {
$num_aggiungi_letti = 1;
if ($dati_ca[$num1]['moltiplica'] == "c" and $max_maxoccupanti and $numpersone > $max_maxoccupanti) {
$num_aggiungi_letti = $numpersone - $max_maxoccupanti;
if ($dati_ca[$num1]['molt_max'] == "n" and $num_aggiungi_letti > $dati_ca[$num1]['molt_max_num']) $num_aggiungi_letti = $dati_ca[$num1]['molt_max_num'];
} # fine if ($dati_ca[$num1]['moltiplica'] == "c" and $max_maxoccupanti and $numpersone > $max_maxoccupanti)
if ($dati_ca[$num1]['numlimite'] and $num_aggiungi_letti > $dati_ca[$num1]['numlimite']) $num_aggiungi_letti = $dati_ca[$num1]['numlimite'];
$settimane_costo_cal = calcola_settimane_costo($tableperiodi,$dati_ca,$num1,$idinizioperiodo,$idfineperiodo,"",$lunghezza_periodo);
calcola_moltiplica_costo($dati_ca,$num1,$moltiplica_costo_cal,$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$num_aggiungi_letti,"","");
$limite_costo_raggiunto = "NO";
$num_costi_presenti_copia = $num_costi_presenti;
for ($num2 = 0 ; $num2 < $num_controlla_limite ; $num2++) if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num1,$num_costi_presenti_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal) == "NO") $limite_costo_raggiunto = "SI";
if ($dati_ca[$num1]['tipo_beniinv'] == "mag") {
$nrc = "";
$beniinv_presenti_copia = $beniinv_presenti;
for ($num2 = 0 ; $num2 < $num_controlla_limite ; $num2++) {
$risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num1,$beniinv_presenti_copia,$nrc,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,"");
if ($risul_beniinv != "SI") break;
} # fine for $num2
} # fine if ($dati_ca[$num1]['tipo_beniinv'] == "mag")
else $risul_beniinv = "SI";
if ($limite_costo_raggiunto != "SI" and $risul_beniinv == "SI") {
$altri_costi_compatibili = 1;
for ($numca2 = 1 ; $numca2 <= $numcostiagg ; $numca2++) {
$num_costo2 = $dati_ca['id'][${"idcostoagg".$numca2}];
if (${"costoagg".$numca2} == "SI" and $dati_ca[$num_costo2]['moltiplica'] == "c" and $dati_ca[$num_costo2]['molt_max'] == "p") {
$num_max = $numpersone - $num_aggiungi_letti;
if ($dati_ca[$num_costo2]['molt_max_num']) $num_max = $num_max - $dati_ca[$num_costo2]['molt_max_num'];
if (${"nummoltiplica_ca".$numca2} > $num_max) $altri_costi_compatibili = 0;
} # fine if (${"costoagg".$numca2} == "SI" and $dati_ca[$num_costo2]['moltiplica'] == "c" and $dati_ca[$num_costo2]['molt_max'] == "p")
} # fine for $numca2
if ($altri_costi_compatibili) {
$costo_aggiungi_letti = $dati_ca[$num1]['id'];
$app_incomp_cal = $dati_ca[$num1]['appincompatibili'];
if ($dati_ca[$num1]['tipo_beniinv'] == "app") $nrc = "";
$posti = 0;
$posto_reg2_orig = 0;
$numpersone = $numpersone - $num_aggiungi_letti;
for ($num2 = 0 ; $num2 < $numappartamenti ; $num2++) {
$idapp = risul_query($appartamenti,$num2,'idappartamenti');
$maxoccupanti = risul_query($appartamenti,$num2,'maxoccupanti');
if ($dati_ca[$num1]['tipo_beniinv'] == "app") $risul_beniinv = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num1,$beniinv_presenti,$nrc,"",$idinizioperiodo,$idfineperiodo,$settimane_costo_cal,$moltiplica_costo_cal,$idapp);
else $risul_beniinv = "SI";
if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI") {
if (($maxoccupanti and $maxoccupanti < $numpersone) or str_replace(",$idapp,","",",$app_incomp_cal,") != ",$app_incomp_cal," or $risul_beniinv != "SI") ${"app_richiesti".$n_t}[$idapp] = "NO";
else {
${"app_richiesti".$n_t}[$idapp] = "SI";
$posti++;
} # fine else if (($maxoccupanti and $maxoccupanti < $numpersone) or...
} # fine if (empty($app_richiesti_copia) or $app_richiesti_copia[$idapp] == "SI")
if ($app_regola2_predef) {
if (str_replace(",$idapp,","",$app_regola2_predef) != $app_regola2_predef) {
if (str_replace(",$idapp,","",",$app_incomp_cal,") == ",$app_incomp_cal," and $risul_beniinv == "SI") {
if (!$maxoccupanti or $maxoccupanti >= $numpersone) $posto_reg2_orig = 1;
} # fine if (str_replace(",$idapp,","",",$app_incomp_cal,") == ",$app_incomp_cal," and $risul_beniinv == "SI")
} # fine if (str_replace(",$idapp,","",$app_regola2_predef) != $app_regola2_predef)
} # fine if ($app_regola2_predef)
} # fine for $num2
if ($posti >= $num_app_richiesti and (!$app_regola2_predef or $posto_reg2_orig)) {
${"numpersone".$n_t} = $numpersone;
${"costoagg".$numca."_".$n_t} = "SI";
${"nummoltiplica_ca".$numca."_".$n_t} = $num_aggiungi_letti;
${"numsettimane".$numca."_".$n_t} = $lunghezza_periodo;
} # fine if ($posti >= $num_app_richiesti and (!$app_regola2_predef or $posto_reg2_orig))
} # fine if ($altri_costi_compatibili)
} # fine if ($limite_costo_raggiunto != "SI" and $risul_beniinv == "SI")
} # fine if ($periodo_costo_trovato != "NO")
} # fine if (($dati_ca[$num1]['numsett'] != "c" or $dati_ca[$num1]['associasett'] != "s") and...
break;
} # fine if (${"idcostoagg".$numca} == $dati_ca[$num1]['id'])
} # fine for $numca
if ($costo_aggiungi_letti) break;
} # fine if ($priv_ins_costi_agg == "s" and ($attiva_costi_agg_consentiti == "n" or...
} # fine if ($dati_ca[$num_costo]['mostra'] == "s" and $dati_ca[$num1]['letto'] == "s")
} # fine for $num1
} # fine if (($posti < $num_app_richiesti or ($app_regola2_predef and !$posto_reg2_orig)) and !$costo_aggiungi_letti)

if ($posti == 0 or ($app_regola2_predef and !$posto_reg2_orig)) {
echo mex("Non c'è nessun appartamento tra quelli richiesti che possa ospitare",'unit.php')." $numpersone ".mex("persone",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
$inserire = "NO";
} # fine if ($posti == 0 or...
else {
if ($posti < $num_app_richiesti) {
echo mex("Non ci sono",'unit.php')." $num_app_richiesti ".mex("appartamenti tra quelli richiesti che possano ospitare",'unit.php')." $numpersone ".mex("persone",$pag);
if ($num_tipologie > 1) echo " (".mex("tipologia",$pag)." $n_t)";
echo ".<br>";
$inserire = "NO";
} # fine if ($posti < $num_app_richiesti)
} # fine else if ($posti == 0)
if ($inserire == "NO" and $app_richiesti_copia) ${"app_richiesti".$n_t} = $app_richiesti_copia;
} # fine if ($numpersone)

if ($num_app_richiesti != 1 or $num_tipologie > 1) {
unset($lista_app_richiesti);
if (empty(${"app_richiesti".$n_t})) $lista_app_richiesti = ",tutti,,";
else foreach (${"app_richiesti".$n_t} as $key => $val) if ($val == "SI") $lista_app_richiesti .= $key.",";
$lista_app_richiesti = substr($lista_app_richiesti,0,-1);
$id_app_richiesti2 = $id_app_richiesti;
for ($num1 = $id_app_richiesti2 ; $num1 < ($id_app_richiesti2 + $num_app_richiesti) ; $num1++) {
$id_app_richiesti++;
$app_richiesti[$id_app_richiesti] = $lista_app_richiesti;
$idinizioperiodo_vett[$id_app_richiesti] = $idinizioperiodo;
$idfineperiodo_vett[$id_app_richiesti] = $idfineperiodo;
} # fine for $num1
$app_richiesti[',numero,'] = $id_app_richiesti;
if ($prenota_vicine == "SI") $app_richiesti[',vicini,'] = "SI";
elseif ($app_richiesti[',vicini,'] == "SI") {
if ($n_t != $num_tipologie) $app_richiesti[',succ_non_vicino,'][$id_app_richiesti] = 1;
if (!${"prenota_vicine".$n_t}) {
for ($num1 = ($id_app_richiesti2 + 1) ; $num1 < $id_app_richiesti ; $num1++) $app_richiesti[',succ_non_vicino,'][$num1] = 1;
} # fine if (!${"prenota_vicine".$n_t})
elseif (${"interrompi_vicine_ogni".$n_t}) {
for ($num1 = ($id_app_richiesti2 + ${"interrompi_vicine_ogni".$n_t}) ; $num1 < $id_app_richiesti ; $num1 += ${"interrompi_vicine_ogni".$n_t}) $app_richiesti[',succ_non_vicino,'][$num1] = 1;
} # fine elseif (${"interrompi_vicine_ogni".$n_t})
} # fine elseif ($app_richiesti[',vicini,'] == "SI")
} # fine if ($num_app_richiesti != 1 or $num_tipologie > 1)
else {
$id_app_richiesti = 1;
$app_richiesti = $app_richiesti1;
$idinizioperiodo_vett = $idinizioperiodo;
$idfineperiodo_vett = $idfineperiodo;
} # fine else if ($num_app_richiesti != 1 or $num_tipologie > 1)

} # fine else if ($idfineperiodo < $idinizioperiodo)

} # fine for $n_t


if ($inserire != "NO") {

unset($condizioni_regole1_non_sel);
if ($applica_regole1 == "n" or ($applica_regole1 == "f" and $attiva_regole1_consentite != "n")) {
for ($num1 = 0 ; $num1 < count($regole1_consentite) ; $num1++) if ($regole1_consentite[$num1]) $condizioni_regole1_non_sel .= "motivazione != '".$regole1_consentite[$num1]."' and ";
if ($condizioni_regole1_non_sel) $condizioni_regole1_non_sel = " and (motivazione2 = 'x' or (".str_replace("motivazione != ' '","motivazione != '' and motivazione is not null",substr($condizioni_regole1_non_sel,0,-5))."))";
} # fine if ($applica_regole1 == "n" or ($applica_regole1 == "f" and...
if (!$condizioni_regole1_non_sel and ($applica_regole1 == "m" or $applica_regole1 == "f")) $condizioni_regole1_non_sel = " and motivazione2 = 'x'";
$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != ''$condizioni_regole1_non_sel");
$num_app_agenzia = numlin_query($app_agenzia);

$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$lim_prenota_temp = aggslashdb(date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60))));
esegui_query("delete from $tableprenota where idclienti = '0' and ( datainserimento < '".$lim_prenota_temp."' or utente_inserimento = '$id_utente' ) ","",1);

#Se ci sono regole per $app_agenzia inserisco false prenotazioni fisse in $app_prenota_id2
if ($num_app_agenzia != 0) {
unset($limiti_var);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($profondita);
$limiti_var['n_ini'] = $idinizioperiodo_tot;
$limiti_var['n_fine'] = $idfineperiodo_tot;
$limiti_var['lim_prenota_temp'] = $lim_prenota_temp;
$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
if (numlin_query($max_prenota) != 0) $tot_prenota = risul_query($max_prenota,0,0);
else $tot_prenota = 0;
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
unset($info_periodi_ag);
$info_periodi_ag['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 == "x") {
$info_periodi_ag['app'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi_ag['ini'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi_ag['fine'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$info_periodi_ag['numero']++;
} # fine if ($mot2 == "x")
} # fine for $num1
if ($info_periodi_ag['numero']) inserisci_prenota_fittizie($info_periodi_ag,$profondita,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett,$app_assegnabili_id);
$limiti_var2 = $limiti_var;
$profondita2 = $profondita;
$prenota_in_app_sett2 = $prenota_in_app_sett;
$inizio_prenota_id2 = $inizio_prenota_id;
$fine_prenota_id2 = $fine_prenota_id;
$app_prenota_id2 = $app_prenota_id;
$app_assegnabili_id2 = $app_assegnabili_id;
unset($info_periodi_ag);
$info_periodi_ag['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 != "x") {
$info_periodi_ag['app'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi_ag['ini'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi_ag['fine'][$info_periodi_ag['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$info_periodi_ag['numero']++;
} # fine if ($mot2 != "x")
} # fine for $num1
if ($info_periodi_ag['numero']) inserisci_prenota_fittizie($info_periodi_ag,$profondita2,$app_prenota_id2,$inizio_prenota_id2,$fine_prenota_id2,$prenota_in_app_sett2,$app_assegnabili_id2);
$occupare_app_agenzia_sempre = "NO";
for ($num1 = $idinizioperiodo_tot ; $num1 <= $idfineperiodo_tot ; $num1++) {
$numprenotazioni = 0;
for ($num2 = 0 ; $num2 < $dati_app['totapp'] ; $num2++) if ($prenota_in_app_sett2[$dati_app['posizione'][$num2]][$num1]) $numprenotazioni++;
if ($numprenotazioni >= $numappartamenti) $occupare_app_agenzia_sempre = "SI";
} # fine for $num1
if ($occupare_app_agenzia_sempre != "SI") {
$app_orig_prenota_id = $app_prenota_id2;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var2,$anno,$fatto_libera,$app_liberato,$profondita2,$app_richiesti,$app_prenota_id2,$app_orig_prenota_id,$inizio_prenota_id2,$fine_prenota_id2,$app_assegnabili_id2,$prenota_in_app_sett2,$dati_app,$PHPR_TAB_PRE."prenota");
} # fine if ($occupare_app_agenzia_sempre != "SI")
else $fatto_libera = "NO";
if ($fatto_libera == "SI") {
$risul_agg = aggiorna_tableprenota($app_prenota_id2,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $fatto_libera = "NO";
else {
$occupare_app_agenzia = "NO";
$appartamento = $app_liberato;
} # fine else if (!$risul_agg)
} # fine if ($fatto_libera == "SI")

# se ci sono app_agenzia e non si può non occuparli
else {
$occupare_app_agenzia = "SI";
$limiti_var['t_limite'] = (time() + $sec_limite_libsett);
$app_liberato = "";
$inserire = "SI_NO";
$app_orig_prenota_id = $app_prenota_id;
liberasettimane($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera2,$app_liberato,$profondita,$app_richiesti,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");
if ($applica_regole1 == "f" and $attiva_regole1_consentite != "n") $fatto_libera2 = "NO";
if ($fatto_libera2 == "NO") {
echo mex("Nel periodo selezionato non è possibile ospitare il cliente negli appartamenti richiesti",'unit.php');
if ($app_incomp_costi) echo " ".mex("con i costi aggiuntivi selezionati",$pag);
echo ".<br>";
if ($occupare_app_agenzia_sempre == "SI" and (!@is_array($app_richiesti) or !$app_richiesti[',numero,'])) {
echo mex("Si dovrà occupare almeno un periodo della regola di assegnazione 1",$pag).".<br>";
} # fine if ($occupare_app_agenzia_sempre == "SI" and (!@is_array($app_richiesti) or...
} # fine if ($fatto_libera2 == "NO")
else {
$appartamento = $app_liberato;
echo mex("Si devono fare i seguenti spostamenti nei periodi della <div style=\"display: inline; color: blue;\">regola di assegnazione 1</div>",$pag).":<br>";
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$id_app_agenzia = risul_query($app_agenzia,$num1,'app_agenzia');
$idinizio_app_agenzia = risul_query($app_agenzia,$num1,'iddatainizio');
$idfine_app_agenzia = risul_query($app_agenzia,$num1,'iddatafine');
$motivazione_app_agenzia = risul_query($app_agenzia,$num1,'motivazione');
$idprenota_cambiate_da_ag = esegui_query("select idprenota from $tableprenota where idappartamenti = '$id_app_agenzia' and iddatainizio <= '$idfine_app_agenzia' and iddatafine >= '$idinizio_app_agenzia'");
$num_idprenota_cambiate_da_ag = numlin_query($idprenota_cambiate_da_ag);
for ($num2 = 0 ; $num2 < $num_idprenota_cambiate_da_ag ; $num2 = $num2 + 1) {
$idprenota_cambiata = risul_query($idprenota_cambiate_da_ag,$num2,'idprenota');
if ($app_prenota_id[$idprenota_cambiata] and $app_prenota_id[$idprenota_cambiata] != $id_app_agenzia) {
$idapp_camb = $app_prenota_id[$idprenota_cambiata];
$dati_cambiati = esegui_query("select * from $tableprenota where idprenota = '$idprenota_cambiata'");
$idclienti_camb = risul_query($dati_cambiati,0,'idclienti');
$cliente_camb = esegui_query("select cognome from $tableclienti where idclienti = '$idclienti_camb'");
if ($idclienti_camb) $cliente_camb = risul_query($cliente_camb,0,'cognome');
else $cliente_camb = "?";
$iddatainizio_camb = $inizio_prenota_id[$idprenota_cambiata];
$datainizio_camb = esegui_query("select datainizio from $tableperiodi where idperiodi = '$iddatainizio_camb'");
$datainizio_camb = risul_query($datainizio_camb,0,'datainizio');
$datainizio_camb_f = formatta_data($datainizio_camb,$stile_data);
$iddatafine_camb = $fine_prenota_id[$idprenota_cambiata];
$datafine_camb = esegui_query("select datafine from $tableperiodi where idperiodi = '$iddatafine_camb'");
$datafine_camb = risul_query($datafine_camb,0,'datafine');
$datafine_camb_f = formatta_data($datafine_camb,$stile_data);
echo mex("La prenotazione dal",$pag)." $datainizio_camb_f ".mex("al",$pag)." $datafine_camb_f ".mex("a nome di",$pag)." $cliente_camb ".mex("verrà spostata dall'appartamento",'unit.php')." <b style=\"color: blue;\">$id_app_agenzia</b> (<b>$motivazione_app_agenzia</b>) ".mex("al",'unit.php')." $idapp_camb.<br>";
} # fine if ($app_prenota_id[$idprenota_cambiata] and $app_prenota_id[$idprenota_cambiata] != $id_app_agenzia)
} # fine for $num2
$idprenota_cambiate_a_ag = prenota_in_app_e_periodo($id_app_agenzia,$idinizio_app_agenzia,$idfine_app_agenzia,$prenota_in_app_sett,$fine_prenota_id,$num_pca);
for ($num2 = 1 ; $num2 <= $num_pca ; $num2++) {
$idprenota_cambiata = $idprenota_cambiate_a_ag[$num2];
if ($app_orig_prenota_id[$idprenota_cambiata] and $app_prenota_id[$idprenota_cambiata] != $app_orig_prenota_id[$idprenota_cambiata]) {
$dati_cambiati = esegui_query("select * from $tableprenota where idprenota = $idprenota_cambiata");
$idapp_camb = $app_orig_prenota_id[$idprenota_cambiata];
$idclienti_camb = risul_query($dati_cambiati,0,'idclienti');
$cliente_camb = esegui_query("select cognome from $tableclienti where idclienti = $idclienti_camb");
if ($idclienti_camb) $cliente_camb = risul_query($cliente_camb,0,'cognome');
else $cliente_camb = "?";
$iddatainizio_camb = $inizio_prenota_id[$idprenota_cambiata];
$datainizio_camb = esegui_query("select datainizio from $tableperiodi where idperiodi = $iddatainizio_camb");
$datainizio_camb = risul_query($datainizio_camb,0,'datainizio');
$datainizio_camb_f = formatta_data($datainizio_camb,$stile_data);
$iddatafine_camb = $fine_prenota_id[$idprenota_cambiata];
$datafine_camb = esegui_query("select datafine from $tableperiodi where idperiodi = $iddatafine_camb");
$datafine_camb = risul_query($datafine_camb,0,'datafine');
$datafine_camb_f = formatta_data($datafine_camb,$stile_data);
echo mex("La prenotazione dal",$pag)." $datainizio_camb_f ".mex("al",$pag)." $datafine_camb_f ".mex("a nome di",$pag)." $cliente_camb ".mex("verrà spostata dall'appartamento",'unit.php')." $idapp_camb ".mex("al",'unit.php')." <b style=\"color: blue;\">$id_app_agenzia</b> (<b>$motivazione_app_agenzia</b>).<br>";
} # fine if ($app_orig_prenota_id[$idprenota_cambiata] and...
} # fine for $num2
for ($num2 = 1 ; $num2 <= $id_app_richiesti ; $num2++) {
if ($id_app_richiesti > 1) $appartamento_controlla = $appartamento[$num2];
else $appartamento_controlla = $appartamento;
if ($id_app_agenzia == $appartamento_controlla and $idinizio_app_agenzia <= $idfineperiodo and $idfine_app_agenzia >= $idinizioperiodo) {
echo mex("La nuova prenotazione verrà inserita nell'appartamento",'unit.php')." <b class=\"colblu\">$id_app_agenzia</b> (<b>$motivazione_app_agenzia</b>).<br>";
} # fine if ($id_app_agenzia == $appartamento_controlla and ...
} # fine for $num2
} # fine for $num1
if (@is_array($app_orig_prenota_id)) {
reset($app_orig_prenota_id);
foreach ($app_orig_prenota_id as $idprenota => $app_prenota) {
if ($app_prenota_id[$idprenota] != $app_prenota) {
$app_cambiato = $app_prenota_id[$idprenota];
$spostamenti_transazione .= "$idprenota,$app_cambiato,";
} # fine if ($app_prenota_id[$idprenota] != $app_prenota)
} # fine foreach ($app_orig_prenota_id as $idprenota => $app_prenota)
$spostamenti_transazione = substr($spostamenti_transazione,0,-1);
} # fine if (@is_array($app_orig_prenota_id))
} # fine else if ($fatto_libera2 == "NO")
} # fine else if ($fatto_libera == "SI")
} # fine if ($num_app_agenzia != 0)

else {
# se non ci sono app_agenzia della regola di assegnazione 1
unset($limiti_var);
unset($profondita_);
unset($app_prenota_id_);
unset($app_orig_prenota_id_);
unset($inizio_prenota_id_);
unset($fine_prenota_id_);
unset($app_assegnabili_id_);
unset($prenota_in_app_sett_);
unset($dati_app_);
unset($app_liberato);
$limiti_var['lim_prenota_temp'] = $lim_prenota_temp;
liberasettimane ($idinizioperiodo_vett,$idfineperiodo_vett,$limiti_var,$anno,$fatto_libera,$app_liberato,$profondita_,$app_richiesti,$app_prenota_id_,$app_orig_prenota_id_,$inizio_prenota_id_,$fine_prenota_id_,$app_assegnabili_id_,$prenota_in_app_sett_,$dati_app_,$PHPR_TAB_PRE."prenota");
if ($fatto_libera == "NO") {
$inserire = "SI_NO";
echo mex("Nel periodo selezionato non è possibile ospitare il cliente in un appartamento tra quelli richiesti",'unit.php');
if ($app_incomp_costi) echo " ".mex("con i costi aggiuntivi selezionati",$pag);
echo ".<br>";
} # fine if ($fatto_libera == "NO")
else $appartamento = $app_liberato;
} # fine else if ($num_app_agenzia != 0)

if (@is_array($app_richiesti) and $app_richiesti[",numero,"]) {
$id_app_richiesti = 0;
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
unset($appartamento_u);
unset($inizioperiodo_u);
unset($fineperiodo_u);
$id_app_richiesti2 = $id_app_richiesti;
for ($num1 = $id_app_richiesti2 ; $num1 < ($id_app_richiesti2 + ${"num_app_richiesti".$n_t}) ; $num1++) {
$id_app_richiesti++;
$appartamento_u .= $appartamento[$id_app_richiesti].",";
$inizioperiodo_u .= $idinizioperiodo_vett[$id_app_richiesti].",";
$fineperiodo_u .= $idfineperiodo_vett[$id_app_richiesti].",";
} # fine for $num1
${"appartamento".$n_t} = substr($appartamento_u,0,-1);
${"inizioperiodo".$n_t} = substr($inizioperiodo_u,0,-1);
${"fineperiodo".$n_t} = substr($fineperiodo_u,0,-1);
if (${"num_app_richiesti".$n_t} > 1) ${"spezzetta".$n_t} = "SI";
} # fine for $n_t
} # fine if (@is_array($app_richiesti) and $app_richiesti[",numero,"])
else $appartamento1 = $appartamento;

} # fine if ($inserire != "NO")


if ($inserire == "NO") {
$inseriscicliente = "";
} # fine if ($inserire == "NO")

else {
unset($dati_transazione1);
unset($dati_transazione2);
unset($dati_transazione3);
unset($dati_transazione4);
unset($dati_transazione5);
unset($dati_transazione6);
unset($dati_transazione7);
unset($dati_transazione8);
unset($dati_transazione9);
unset($dati_transazione10);
unset($dati_transazione13);
$dati_transazione12 = $prenota_vicine;
unset($dati_transazione14);
$dati_transazione1 = $num_tipologie;
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$dati_transazione2 .= ";".${"inizioperiodo".$n_t};
$dati_transazione3 .= ";".${"fineperiodo".$n_t};
$dati_transazione4 .= ", ,".${"appartamento".$n_t};
$dati_transazione5 .= ",".${"nometipotariffa".$n_t};
$dati_transazione6 .= ",".${"numpersone".$n_t};
$dati_transazione7 .= ",".${"assegnazioneapp".$n_t};
$dati_transazione8 .= ",".${"num_app_richiesti".$n_t};
$dati_transazione9 .= ", ,".${"lista_app".$n_t};
$dati_transazione10 .= ",".${"spezzetta".$n_t};
$dati_transazione12 .= ",".${"prenota_vicine".$n_t};
$dati_transazione14 .= ",".${"num_letti_agg".$n_t}["max"];
$dati_transazione19 .= ";".${"diff_persone".$n_t}.",".${"interrompi_vicine_ogni".$n_t};
} # fine for $n_t
$dati_transazione2 = substr($dati_transazione2,1);
$dati_transazione3 = substr($dati_transazione3,1);
$dati_transazione4 = substr($dati_transazione4,3);
$dati_transazione5 = substr($dati_transazione5,1);
$dati_transazione6 = substr($dati_transazione6,1);
$dati_transazione7 = substr($dati_transazione7,1);
$dati_transazione8 = substr($dati_transazione8,1);
$dati_transazione9 = substr($dati_transazione9,3);
$dati_transazione10 = substr($dati_transazione10,1);
$dati_transazione14 = substr($dati_transazione14,1);
$dati_transazione19 = substr($dati_transazione19,1);
$dati_transazione11 = $fatto_libera2;
$dati_transazione18 = aggslashdb(serialize($app_eliminati_costi));

if ($inserire != "SI_NO") ins_prenota_temp_in_tab($tableprenota,$tablecostiprenota,$num_tipologie,$limiti_var,$dati_transazione13,$prenota_vicine,$id_utente,$HOSTNAME);

$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
list($usec, $sec) = explode(' ', microtime());
mt_srand((float) $sec + ((float) $usec * 100000));
$val_casuale = mt_rand(100000,999999);
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$id_transazione = $adesso.$val_casuale.$versione_transazione;
esegui_query("insert into $tabletransazioni (idtransazioni,idsessione,tipo_transazione,anno,spostamenti,dati_transazione1,dati_transazione2,dati_transazione3,dati_transazione4,dati_transazione5,dati_transazione6,dati_transazione7,dati_transazione8,dati_transazione9,dati_transazione10,dati_transazione11,dati_transazione12,dati_transazione13,dati_transazione14,dati_transazione18,dati_transazione19,ultimo_accesso) values ('$id_transazione','$id_sessione','ins_p','$anno','$spostamenti_transazione','$dati_transazione1','$dati_transazione2','$dati_transazione3','$dati_transazione4','$dati_transazione5','$dati_transazione6','$dati_transazione7','$dati_transazione8','$dati_transazione9','$dati_transazione10','$dati_transazione11','$dati_transazione12','$dati_transazione13','$dati_transazione14','$dati_transazione18','$dati_transazione19','$ultimo_accesso')");
} # fine else if ($inserire == "NO")

unlock_tabelle($tabelle_lock);

} # fine if ($nuovaprenotazione != "Continua lo stesso")


else {

$tabelle_lock = array($tableprenota,$tablecostiprenota,$tabletransazioni);
$altre_tab_lock = array($tableappartamenti,$tableperiodi,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

if ($tipo_transazione != "ins_p") $inserire = "NO";

else {
$fatto_libera2 = risul_query($dati_transazione,0,'dati_transazione11');
$num_tipologie = risul_query($dati_transazione,0,'dati_transazione1');
$numpersone = explode(",",risul_query($dati_transazione,0,'dati_transazione6'));
$num_app_richiesti = explode(",",risul_query($dati_transazione,0,'dati_transazione8'));
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
${"num_app_richiesti".$n_t} = $num_app_richiesti[($n_t - 1)];
${"numpersone".$n_t} = $numpersone[($n_t - 1)];
} # fine for $n_t

if ($fatto_libera2 == "SI") {
unset($limiti_var);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($profondita);
$limiti_var['n_ini'] = 0;
$max_periodo = esegui_query("select max(idperiodi) from $tableperiodi");
$max_periodo = risul_query($max_periodo,0,0);
$limiti_var['n_fine'] = $max_periodo;
$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$limiti_var['lim_prenota_temp'] = aggslashdb(date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60))));
esegui_query("delete from $tableprenota where idclienti = '0' and ( datainserimento < '".$limiti_var['lim_prenota_temp']."' or utente_inserimento = '$id_utente' ) ","",1);
$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
if (numlin_query($max_prenota) != 0) $tot_prenota = risul_query($max_prenota,0,0);
else $tot_prenota = 0;
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
$nuovo_app = "";
$spostamenti = risul_query($dati_transazione,0,'spostamenti');
$spostamenti = explode(",",$spostamenti);

# Effettuo gli spostamenti e faccio i controlli (appartamento di destinazione assegnabile, esistente e vuoto)
$num_spostamenti = count($spostamenti);
for ($num1 = 0 ; $num1 < $num_spostamenti ; $num1++) {
$idprenota_cambiata = $spostamenti[$num1];
for ($num2 = $inizio_prenota_id[$idprenota_cambiata] ; $num2 <= $fine_prenota_id[$idprenota_cambiata] ; $num2++) {
$prenota_in_app_sett[$app_prenota_id[$idprenota_cambiata]][$num2] = "";
} # fine for $num2
$num1++;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_spostamenti ; $num1++) {
$idprenota_cambiata = $spostamenti[$num1];
$app_cambiato = $spostamenti[($num1 + 1)];
if ($inizio_prenota_id[$idprenota_cambiata]) {
if (!$app_assegnabili_id[$idprenota_cambiata]) $inserire = "NO";
if ($app_assegnabili_id[$idprenota_cambiata] and $app_assegnabili_id[$idprenota_cambiata] != "v") {
$lista_app_assegnabili = ",".$app_assegnabili_id[$idprenota_cambiata].",";
if (str_replace(",".$app_cambiato.",","",$lista_app_assegnabili) == $lista_app_assegnabili) $inserire = "NO";
} # fine if ($app_assegnabili_id[$idprenota_cambiata] and...
if (str_replace(",".$app_cambiato.",","",$dati_app['lista']) == $dati_app['lista']) $inserire = "NO";
$app_prenota_id[$idprenota_cambiata] = $app_cambiato;
for ($num2 = $inizio_prenota_id[$idprenota_cambiata] ; $num2 <= $fine_prenota_id[$idprenota_cambiata] ; $num2++) {
if ($prenota_in_app_sett[$app_cambiato][$num2]) $inserire = "NO";
else $prenota_in_app_sett[$app_cambiato][$num2] = $idprenota_cambiata;
} # fine for $num2
} # fine if ($inizio_prenota_id[$idprenota_cambiata])
if ($inserire == "NO") break;
$num1++;
} # fine for $num1
$inizioperiodo = explode(";",risul_query($dati_transazione,0,'dati_transazione2'));
$fineperiodo = explode(";",risul_query($dati_transazione,0,'dati_transazione3'));
$appartamento = explode(", ,",risul_query($dati_transazione,0,'dati_transazione4'));
$assegnazioneapp = explode(",",risul_query($dati_transazione,0,'dati_transazione7'));
$lista_app = explode(", ,",risul_query($dati_transazione,0,'dati_transazione9'));
$prenota_vicine_vett = explode(",",risul_query($dati_transazione,0,'dati_transazione12'));
$prenota_vicine = $prenota_vicine_vett[0];
$dati_extra = explode(";",risul_query($dati_transazione,0,'dati_transazione19'));
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
${"inizioperiodo".$n_t} = $inizioperiodo[($n_t - 1)];
${"fineperiodo".$n_t} = $fineperiodo[($n_t - 1)];
${"appartamento".$n_t} = $appartamento[($n_t - 1)];
${"assegnazioneapp".$n_t} = $assegnazioneapp[($n_t - 1)];
${"lista_app".$n_t} = $lista_app[($n_t - 1)];
${"prenota_vicine".$n_t} = $prenota_vicine_vett[$n_t];
$dati_extra_corr = explode(",",$dati_extra[($n_t - 1)]);
${"diff_persone".$n_t} = $dati_extra_corr[0];
${"interrompi_vicine_ogni".$n_t} = $dati_extra_corr[1];
$inizioperiodo = explode(",",${"inizioperiodo".$n_t});
$fineperiodo = explode(",",${"fineperiodo".$n_t});
$appartamento = explode(",",${"appartamento".$n_t});
for ($num1 = 0 ; $num1 < ${"num_app_richiesti".$n_t} ; $num1++) {
for ($num2 = $inizioperiodo[$num1] ; $num2 <= $fineperiodo[$num1] ; $num2++) {
if ($prenota_in_app_sett[$appartamento[$num1]][$num2]) $inserire = "NO";
} # fine for $num2
} # fine for $num1
} # fine for $n_t
} # fine if ($fatto_libera2 == "SI")

else {
unset($dati_transazione10);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) $dati_transazione10 .= ",SI";
$dati_transazione10 = substr($dati_transazione10,1);
esegui_query("update $tabletransazioni set dati_transazione10 = '$dati_transazione10' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
if ($num_tipologie = 1 and $num_app_richiesti1 = 1 and $id_utente == 1) esegui_query("update $tabletransazioni set dati_transazione7 = 'v', dati_transazione9 = '' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
} # fine else if ($fatto_libera2 == "SI")

} # fine else if ($tipo_transazione != "ins_p")

if ($inserire != "NO" and $fatto_libera2 == "SI") {
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $inserire = "NO";
else {
unset($dati_transazione13);
ins_prenota_temp_in_tab($tableprenota,$tablecostiprenota,$num_tipologie,$limiti_var,$dati_transazione13,$prenota_vicine,$id_utente,$HOSTNAME);
esegui_query("update $tabletransazioni set dati_transazione13 = '$dati_transazione13' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
} # fine else if (!$risul_agg)
} # fine if ($inserire != "NO" and $fatto_libera2 == "SI")

if ($inserire == "NO") {
echo mex("Non si può procedere perchè la tabella prenotazioni è cambiata nel frattempo",$pag).".<br>";
$inseriscicliente = "";
} # fine if ($inserire == "NO")

unlock_tabelle($tabelle_lock);
} # fine else if ($nuovaprenotazione != "Continua lo stesso")


} # fine else if ($aggiungi_tipologie)

} # fine if ($nuovaprenotazione)




if ($inserire == "SI_NO") {
$mostra_form_dati_cliente = "NO";
if ($prenota_vicine == "SI" and $app_richiesti[",numero,"] and $fatto_libera2 != "SI") {
echo "<br><form accept-charset=\"utf-8\" method=\"post\" action=\"clienti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$form_riprova_no_vicini
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"nuovaprenotazione\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" name=\"nuovaprenota_vedi\" value=\"".mex("Riprova senza cercare appartamenti vicini",'unit.php')."\">
</div></form><br>";
} # fine if ($prenota_vicine == "SI" and...
if ((!$app_richiesti[',numero,'] and $id_utente == 1) or $fatto_libera2 == "SI") {
if ($fatto_libera2 != "SI") {
echo "<b>".mex("Se si continua l'assegnazione dell'appartamento <span class=\"colred\">non terrà conto degli appartamenti richiesti</span>",'unit.php')."</b>";
if ($lista_app1) echo " (<b>$lista_app1</b>)";
echo ".<br>";
} # fine if ($fatto_libera2 != "SI")
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"clienti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
$manda_dati_assegnazione = "NO";
include("./includes/dati_form_prenotazione.php");
echo "
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"nuovaprenotazione\" value=\"Continua lo stesso\">
<input class=\"sbutton\" type=\"submit\" name=\"nuovaprenota_vedi\" value=\"".mex("Continua lo stesso",$pag)."\">
</div></form><br>";
} # fine if ((!$app_richiesti[',numero,'] and $id_utente == 1) or $fatto_libera2 == "SI")
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
$form_orig
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br><br>";
$inseriscicliente = "";
$inserire = "NO";
} # fine if ($inserire == "SI_NO")

else {



if ($inseriscicliente or $inserire) {

$mostra_form_dati_cliente = "NO";
$Modifica_i_dati_del_cliente = "Modifica i dati del cliente";

if ($nuovaprenotazione == "") {

$tableclienti = $PHPR_TAB_PRE."clienti";

} # fine if ($nuovaprenotazione == "")

if ($inseriscicliente) {
if ($cognome) {
$condizione_utente = "";
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI" or (!$nuovaprenotazione and ($modifica_clienti == "PROPRI" or $modifica_clienti == "GRUPPI"))) {
$condizione_utente = "and ( utente_inserimento = '$id_utente'";
if ($vedi_clienti == "GRUPPI" or (!$nuovaprenotazione and $modifica_clienti == "GRUPPI")) {
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_utente .= " or utente_inserimento = '$idut_gr'";
} # fine if ($vedi_clienti == "GRUPPI" or...
$condizione_utente .= " )";
} # fine if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI" or...
if ($vedi_clienti == "NO" or (!$nuovaprenotazione and $modifica_clienti == "NO")) $condizione_utente = "and utente_inserimento = '-1'";
if (!$nome) $esistecognome = esegui_query("select * from $tableclienti where ( cognome $ILIKE '%".aggslashdb($cognome)."%' ) $condizione_utente order by max_num_ordine");
else $esistecognome = esegui_query("select * from $tableclienti where ( cognome $ILIKE '%".aggslashdb($cognome)."%' and nome $ILIKE '%".aggslashdb($nome)."%' ) $condizione_utente order by max_num_ordine");
$numrighe = numlin_query($esistecognome);
$cognome = stripslashes($cognome);

# I cognomi inseriti non esistono nel database
if ($numrighe == 0) {
$inserire = "SI";
if ($nuovaprenotazione) {
if ($inserimento_nuovi_clienti == "SI") {
$mostra_form_dati_cliente = "SI";
$datiprenota = mex("Inserisci i dati di un nuovo cliente",$pag);
$titolo_form_dati_cliente = $datiprenota;
$inserire = "";
} # fine if ($inserimento_nuovi_clienti == "SI")
else {
echo mex("Non si è trovato nessun cliente chiamato",$pag)." $cognome";
if ($nome) echo " $nome";
echo ".<br>";
$inserire = "NO";
} # fine else if ($inserimento_nuovi_clienti == "SI")
} # fine if ($nuovaprenotazione)
} # fine if ($numrighe == 0)

# Esiste almeno uno dei cognomi inseriti
else {
if (!$nome) echo mex("Esistono i seguenti clienti con un cognome uguale o simile",$pag);
else echo mex("Esistono i seguenti clienti con un cognome e nome uguale o simile",$pag);
echo ":<br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
$select_prenotazioni = "";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
$select_prenotazioni .= "<option value=\"p$num1"."_$n_t\">$num1";
if ($num_tipologie > 1) $select_prenotazioni .= " ".mex("tipologia",$pag)." $n_t";
$select_prenotazioni .= "</option>";
} # fine for $num1
} # fine for $n_t
for ($num = 0; $num < $numrighe ; $num = $num + 1) {
$idcognome[$num] = risul_query($esistecognome,$num,'idclienti');
$dati = esegui_query("select * from $tableclienti where idclienti = '".$idcognome[$num]."' ");

mostra_dati_cliente($dati,$dcognome,$dnome,$dsoprannome,$dtitolo_cli,$dsesso,$ddatanascita,$ddatanascita_f,$dnazionenascita,$dcittanascita,$dregionenascita,$ddocumento,$dscadenzadoc,$dscadenzadoc_f,$dtipodoc,$dnazionedoc,$dregionedoc,$dcittadoc,$dnazionalita,$dlingua_cli,$dnazione,$dregione,$dcitta,$dvia,$dnumcivico,$dtelefono,$dtelefono2,$dtelefono3,$dfax,$dcap,$demail,$dcod_fiscale,$dpartita_iva,"SI",$priv_ins_clienti,0,1);
echo "<br>";

if ($nuovaprenotazione) {
echo "<label><input name=\"cliente_ospite_".$idcognome[$num]."\" value=\"SI\" type=\"checkbox\" checked>
".mex("Ospite della prenotazione",$pag)."</label>";
if ($num_tipologie > 1 or $num_app_richiesti1 > 1) {
echo " <select name=\"prenota_cli_osp_".$idcognome[$num]."\">
$select_prenotazioni
</select>";
} # fine if ($num_tipologie > 1 or $num_app_richiesti1 > 1)
else echo "<input type=\"hidden\" name=\"prenota_cli_osp_".$idcognome[$num]."\" value=\"p1_1\">";
$clienti_compagni = esegui_query("select * from $tableclienti where idclienti_compagni $LIKE '%,".$idcognome[$num].",%' $condizione_utente order by max_num_ordine");
$num_clienti_compagni = numlin_query($clienti_compagni);
if (!$num_clienti_compagni) echo "<br>";
else {
$dati_osp = "";
for ($num1 = 0 ; $num1 < $num_clienti_compagni ; $num1++) {
$id_clienti_comp = risul_query($clienti_compagni,$num1,'idclienti');
$dati_osp .= "<label><input name=\"ospite_".$idcognome[$num]."_".$id_clienti_comp."\" value=\"SI\" type=\"checkbox\">\
 <em>".addslashes(risul_query($clienti_compagni,$num1,'cognome'))."</em> ";
$ccnome = addslashes(risul_query($clienti_compagni,$num1,'nome'));
$ccsesso = risul_query($clienti_compagni,$num1,'sesso');
$ccdatanascita = risul_query($clienti_compagni,$num1,'datanascita');
$O = "o";
if ($ccsesso == "f") $O = "a";
if ($ccnome) $dati_osp .=  "$ccnome ";
if ($ccdatanascita) $dati_osp .= mex("nat$O il",$pag)." ".formatta_data($ccdatanascita,$stile_data)." ";
if ($num_tipologie > 1 or $num_app_richiesti1 > 1) {
$dati_osp .= "".addslashes(mex("nella prenotazione",$pag))."\
 </label><select name=\"pren_osp_".$idcognome[$num]."_".$id_clienti_comp."\">\
$select_prenotazioni\
</select><br>";
} # fine if ($num_tipologie > 1 or $num_app_richiesti1 > 1)
else $dati_osp .= "</label><input type=\"hidden\" name=\"pren_osp_".$idcognome[$num]."_".$id_clienti_comp."\" value=\"p1_1\"><br>";
} # fine for $num1
echo ".
<script type=\"text/javascript\">
<!--
function apri_osp".$idcognome[$num]." () {
var bott = document.getElementById('bott".$idcognome[$num]."');
bott.style.visibility = 'hidden';
var elem_cli = document.getElementById('osp_cli".$idcognome[$num]."');
elem_cli.style.visibility = 'visible';
var testo = '$dati_osp';
elem_cli.innerHTML = testo;
} // fine function apri_osp".$idcognome[$num]."
-->
</script>
 ".mex("Altri ospiti",$pag).": 
<button type=\"button\" id=\"bott".$idcognome[$num]."\" onclick=\"apri_osp".$idcognome[$num]."()\">
<img style=\"display: block;\" src=\"./img/freccia_destra_marg.png\" alt=\"&gt;\"></button><br>
<div id=\"osp_cli".$idcognome[$num]."\" style=\"visibility: hidden;\"></div>";
} # fine else if (!$num_clienti_compagni)
$fr_utilizza_cliente = mex("Utilizza il cliente",$pag)." ".$idcognome[$num]." ".mex("per la prenotazione",$pag);
echo "<br><div style=\"text-align: center;\">
<button class=\"cli\" type=\"submit\" name=\"idclienti\" value=\"$fr_utilizza_cliente\"><div>$fr_utilizza_cliente</div></button>";
} # fine if ($nuovaprenotazione)
else {

echo "<br><div style=\"text-align: center;\">
<button class=\"mcli\" type=\"submit\" name=\"inserire\" value=\"".mex($Modifica_i_dati_del_cliente,$pag)." ".$idcognome[$num]."\"><div>".mex($Modifica_i_dati_del_cliente,$pag)." ".$idcognome[$num]."</div></button>";
if (preg_replace("/".str_replace("/","\\/",$cognome)."/i","",$dcognome) != $dcognome) {
echo "<input type=\"hidden\" name=\"inserire_dato_cognome".$idcognome[$num]."\" value=\"NO\">";
} # fine if (preg_replace("/".str_replace("/","\\/",$cognome)."/i","",$dcognome) != $dcognome)
if (preg_replace("/".str_replace("/","\\/",$nome)."/i","",$dnome) != $dnome) {
echo "<input type=\"hidden\" name=\"inserire_dato_nome".$idcognome[$num]."\" value=\"NO\">";
} # fine if (preg_replace("/".str_replace("/","\\/",$nome)."/i","",$dnome) != $dnome)

} # fine else if ($nuovaprenotazione)

echo "</div><hr style=\"width: 95%\">";
} # fine for $num
echo "<div style=\"text-align: center;\">";

if ($nuovaprenotazione) {
$manda_dati_assegnazione = "NO";
include("./includes/dati_form_prenotazione.php");
echo "</div></div></form>";

if ($inserimento_nuovi_clienti == "SI") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"clienti.php\"><div>
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
include("./includes/dati_form_prenotazione.php");
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
echo "<input type=\"hidden\" name=\"numpersone$n_t\" value=\"".${"numpersone".$n_t}."\">
<input type=\"hidden\" name=\"num_letti_agg_max$n_t\" value=\"".${"num_letti_agg".$n_t}["max"]."\">
<input type=\"hidden\" name=\"num_app_richiesti$n_t\" value=\"".${"num_app_richiesti".$n_t}."\">";
} # fine for $n_t
echo "<div style=\"text-align: center;\"><br>
<button id=\"inse\" class=\"icli\" type=\"submit\"><div>".mex("Inserisci i dati di un nuovo cliente",$pag)."</div></button>
<input type=\"hidden\" name=\"datiprenota\" value=\"1\">
</div></div></form><br>
<hr style=\"width: 95%\">";
} # fine if ($inserimento_nuovi_clienti == "SI")

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
<input type=\"hidden\" name=\"annulla\" value=\"SI\">
$form_orig
<div style=\"text-align: center;\"><br>
<button class=\"gobk\" type=\"submit\" id=\"indi\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></div></form><br><br>";
} # fine if ($nuovaprenotazione)

else {
echo "<input type=\"hidden\" name=\"cognome\" value=\"$cognome\">
<input type=\"hidden\" name=\"nome\" value=\"$nome\">
<input type=\"hidden\" name=\"soprannome\" value=\"$soprannome\">
<input type=\"hidden\" name=\"titolo_cli\" value=\"$titolo_cli\">
<input type=\"hidden\" name=\"sesso\" value=\"$sesso\">
<input type=\"hidden\" name=\"giornonascita\" value=\"$giornonascita\">
<input type=\"hidden\" name=\"mesenascita\" value=\"$mesenascita\">
<input type=\"hidden\" name=\"annonascita\" value=\"$annonascita\">
<input type=\"hidden\" name=\"documento\" value=\"$documento\">
<input type=\"hidden\" name=\"giornoscaddoc\" value=\"$giornoscaddoc\">
<input type=\"hidden\" name=\"mesescaddoc\" value=\"$mesescaddoc\">
<input type=\"hidden\" name=\"annoscaddoc\" value=\"$annoscaddoc\">
<input type=\"hidden\" name=\"tipodoc\" value=\"$tipodoc\">
<input type=\"hidden\" name=\"cittadoc\" value=\"$cittadoc\">
<input type=\"hidden\" name=\"regionedoc\" value=\"$regionedoc\">
<input type=\"hidden\" name=\"nazionedoc\" value=\"$nazionedoc\">
<input type=\"hidden\" name=\"cittanascita\" value=\"$cittanascita\">
<input type=\"hidden\" name=\"regionenascita\" value=\"$regionenascita\">
<input type=\"hidden\" name=\"nazionenascita\" value=\"$nazionenascita\">
<input type=\"hidden\" name=\"nazionalita\" value=\"$nazionalita\">
<input type=\"hidden\" name=\"lingua_cli\" value=\"$lingua_cli\">
<input type=\"hidden\" name=\"nazione\" value=\"$nazione\">
<input type=\"hidden\" name=\"citta\" value=\"$citta\">
<input type=\"hidden\" name=\"regione\" value=\"$regione\">
<input type=\"hidden\" name=\"via\" value=\"$via\">
<input type=\"hidden\" name=\"nomevia\" value=\"$nomevia\">
<input type=\"hidden\" name=\"numcivico\" value=\"$numcivico\">
<input type=\"hidden\" name=\"cap\" value=\"$cap\">
<input type=\"hidden\" name=\"telefono\" value=\"$telefono\">
<input type=\"hidden\" name=\"telefono2\" value=\"$telefono2\">
<input type=\"hidden\" name=\"telefono3\" value=\"$telefono3\">
<input type=\"hidden\" name=\"fax\" value=\"$fax\">
<input type=\"hidden\" name=\"email\" value=\"$email\">
<input type=\"hidden\" name=\"cod_fiscale\" value=\"$cod_fiscale\">
<input type=\"hidden\" name=\"partita_iva\" value=\"$partita_iva\">";
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) echo "<input type=\"hidden\" name=\"campo_pers$num1\" value=\"".${"campo_pers".$num1}."\">";
if ($inserimento_nuovi_clienti == "SI") {
echo "<button class=\"icli\" type=\"submit\" name=\"inserire\" value=\"1\"><div>".mex("Inserisci un nuovo cliente",$pag)."</div></button>
<br><hr style=\"width: 95%\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></div></form><br><br>";
} # fine if ($inserimento_nuovi_clienti == "SI")

} # fine else if ($nuovaprenotazione)

} # fine else if ($numrighe == 0)
} # fine if ($cognome)

} # fine if ($inseriscicliente)


if ($cognome == "") {
echo mex("É necessario inserire il cognome del cliente",$pag).".<br>";
$inserire = "NO";
} # fine if ($cognome == "")



if ($inserire == "NO") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
if ($nuovaprenotazione) echo $form_orig;
echo "<br>
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br><br>";
} # fine if ($inserire == "NO")
else {
if ($inserire) {

$tabelle_lock = array($tableclienti,$tablerelclienti);
$altre_tab_lock = array($tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

$inserire = htmlentities($inserire);
if (str_replace(mex($Modifica_i_dati_del_cliente,$pag),"",$inserire) != $inserire or str_replace(htmlentities(mex($Modifica_i_dati_del_cliente,$pag)),"",$inserire) != $inserire) {
if (str_replace(mex($Modifica_i_dati_del_cliente,$pag),"",$inserire) != $inserire) $idclienti = str_replace(mex($Modifica_i_dati_del_cliente,$pag),"",$inserire);
else $idclienti = str_replace(htmlentities(mex($Modifica_i_dati_del_cliente,$pag)),"",$inserire);
$idclienti = str_replace(" ","",$idclienti);
$idclienti = aggslashdb($idclienti);
$dati_cliente = esegui_query("select cognome,utente_inserimento from $tableclienti where idclienti = '$idclienti'");
$cognome = risul_query($dati_cliente,0,'cognome');
$cliente_modificato = "SI";
$inserire_dato_cognome = "inserire_dato_cognome".$idclienti;
$inserire_dato_nome = "inserire_dato_nome".$idclienti;
$utente_inserimento = risul_query($dati_cliente,0,'utente_inserimento');
if ($vedi_clienti == "NO" or ($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) or ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento])) $inserire = "NO";
if ($modifica_clienti == "NO" or ($modifica_clienti == "PROPRI" and $utente_inserimento != $id_utente) or ($modifica_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento])) $inserire = "NO";
} # fine if (str_replace(mex($Modifica_i_dati_del_cliente,$pag),"",$inserire) != $inserire or...

if ($idclienti == "") {
if ($inserimento_nuovi_clienti == "NO") $inserire = "NO";
$cognome_agg = $cognome;
$max_num_ordine = 1;
} # fine if ($idclienti == "")
else {
$cognome_agg = "";
$max_num_ordine = "";
} # fine else if ($idclienti == "")

if ($inserire != "NO") {
if ($$inserire_dato_nome == "NO") $nome = "";
$campi_pers_vett = array();
$campi_pers_vett['num'] = $num_campi_pers;
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
$opt = explode("<",$campi_pers[$num1]);
$campi_pers_vett[$num1] = $opt[0];
$campi_pers_vett['tipo'][$num1] = $opt[1];
$campi_pers_vett['val'][$num1] = ${"campo_pers".$num1};
} # fine for $num1

$idclienti = inserisci_dati_cliente($cognome_agg,$nome,$soprannome,$titolo_cli,$sesso,$mesenascita,$giornonascita,$annonascita,$nazionenascita,$cittanascita,$regionenascita,$documento,$tipodoc,$mesescaddoc,$giornoscaddoc,$annoscaddoc,$cittadoc,$regionedoc,$nazionedoc,$nazionalita,$lingua_cli,$nazione,$citta,$regione,$via,$nomevia,$numcivico,$cap,$telefono,$telefono2,$telefono3,$fax,$email,$cod_fiscale,$partita_iva,$max_num_ordine,$id_utente_ins,$attiva_prefisso_clienti,$prefisso_clienti,$idclienti,"NO",$campi_pers_vett);

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_cliente.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"origine\" value=\"inizio.php\">";
if ($cliente_modificato == "SI") echo mex("I dati del cliente",$pag)." $cognome ".mex("sono stati modificati",$pag).". ";
else echo mex("I dati del cliente",$pag)." $cognome ".mex("sono stati inseriti",$pag).". ";
echo "<button class=\"mcli\" type=\"submit\"><div>".mex("Modifica i dati del cliente",$pag)." $idclienti</div></button>
</div></form>";
} # fine if ($inserire != "NO")
else echo mex("Non si è trovato nessun cliente chiamato",$pag)." $cognome.<br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna all'inserimento clienti",$pag)."</div></button>
</div></form>";

unlock_tabelle($tabelle_lock);

} # fine if ($inserire)


} # fine else if ($inserire == "NO")

} # fine if ($inseriscicliente or $inserire)


} # fine else if ($inserire == "SI_NO")




if ($mostra_form_dati_cliente != "NO") {


if (!$titolo_form_dati_cliente) $titolo_form_dati_cliente = mex("Inserisci o modifica i dati di un cliente",$pag);
echo "<h4 id=\"h_icli\"><span>$titolo_form_dati_cliente.</span></h4>";

# Questa è la form di inserimento

if ($origine == "") {
$origine = "clienti.php";
} # fine if ($origine == "")

$annonascita = 19;

$checked_cpc = array();
if ($idmessaggi) {
$dati_mess = esegui_query("select dati_messaggio15 from $tablemessaggi where idmessaggi = '".aggslashdb($idmessaggi)."' and idutenti $LIKE '%,$id_utente,%' ");
if (numlin_query($dati_mess) == 1) {
$dati_mess = explode("<d>",risul_query($dati_mess,0,'dati_messaggio15'));
$cognome_richiedente = $dati_mess[0];
#$nome = $dati_mess[1];
$email = $dati_mess[2];
$sesso = $dati_mess[3];
if ($sesso == "F") $sel_f = " selected";
if ($sesso == "M") $sel_m = " selected";
$datanascita = $dati_mess[4];
$annonascita = substr($datanascita,0,4);
$mesenascita = substr($datanascita,5,2);
$giornonascita = substr($datanascita,8,2);
$tipodoc = $dati_mess[18];
$documento = $dati_mess[5];
$nazione = $dati_mess[6];
$regione = $dati_mess[7];
$citta = $dati_mess[8];
$via = $dati_mess[9];
$numcivico = $dati_mess[10];
$cap = $dati_mess[11];
$lingua_cli = $dati_mess[19];
$telefono = $dati_mess[12];
$telefono2 = $dati_mess[13];
$telefono3 = $dati_mess[14];
$fax = $dati_mess[15];
$campi_pers_clienti = explode(">",$dati_mess[22]);
$num_campi_pers_clienti = count($campi_pers_clienti);
for ($num1 = 1 ; $num1 < $num_campi_pers_clienti ; $num1++) {
$campo_pers_cliente = explode("<",$campi_pers_clienti[$num1]);
for ($num2 = 0 ; $num2 < $num_campi_pers ; $num2++) {
$opt = explode("<",$campi_pers[$num2]);
if ($campo_pers_cliente[0] == $opt[0]) {
if ($opt[1] == "txt") ${"campo_pers".$num2} = $campo_pers_cliente[1];
else $checked_cpc[$num2] = " checked";
break;
} # fine if ($campo_pers_cliente[0] == $opt[0])
} # fine for $num2
} # fine for $num1
$cod_fiscale = $dati_mess[23];
$partita_iva = $dati_mess[24];
} # fine if (numlin_query($dati_mess) == 1)
} # fine if ($idmessaggi)

if ($datiprenota) echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"98%\"><tr><td style=\"font-size: 80%;\" align=\"right\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
<input type=\"hidden\" name=\"annulla\" value=\"SI\">
<input class=\"sbutton\" type=\"submit\" style=\"font-size: 80%;\" value=\"".mex("Annulla",$pag)."\">
</div></form></td></tr></table>";
else echo "<br>";
mostra_funzjs_cpval();
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div class=\"linhbox\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<hr style=\"width: 95%\">";
$titoli_cliente = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'titoli_cliente' and idutente = '$id_utente'");
if (numlin_query($titoli_cliente) == 1) $titoli_cliente = risul_query($titoli_cliente,0,"valpersonalizza");
else $titoli_cliente = "";
if ($titoli_cliente) {
echo "<select name=\"titolo_cli\">
<option value=\"\">--</option>";
$titoli_cliente = explode(">",$titoli_cliente);
for ($num1 = 0 ; $num1 < count($titoli_cliente) ; $num1++) {
$opt = explode("<",$titoli_cliente[$num1]);
echo "<option value=\"".$opt[0]."\">".$opt[0]."</option>";
} # fine for $num1
echo "</select> ";
} # fine if ($titoli_cliente)
echo "<span class=\"wsnw\">".mex("Cognome",$pag).": ";
if ($id_utente == 1 and $id_utente_ins != 1) {
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente_ins' and anno = '1'");
$prefisso_clienti = risul_query($privilegi_globali_utente,0,'prefisso_clienti');
$attiva_prefisso_clienti = substr($prefisso_clienti,0,1);
if ($attiva_prefisso_clienti != "n") {
$prefisso_clienti = explode(",",$prefisso_clienti);
$prefisso_clienti = $prefisso_clienti[1];
} # fine if ($prefisso_clienti != "n")
} # fine if ($id_utente == 1 and $id_utente_ins != 1)
if ($attiva_prefisso_clienti == "p") echo $prefisso_clienti;
echo "<input type=\"text\" id=\"cognome\" name=\"cognome\" value=\"$cognome\">";
if ($attiva_prefisso_clienti == "s") echo $prefisso_clienti;
echo ",</span> <span class=\"wsnw\">".mex("nome",$pag).": <input type=\"text\" name=\"nome\" value=\"$nome\">;</span>
 <span class=\"wsnw\">".mex("soprannome",$pag).": <input type=\"text\" name=\"soprannome\" value=\"$soprannome\">;</span><br>
".mex("sesso",$pag).": <select name=\"sesso\">
<option value=\"\" selected>-</option>
<option value=\"m\"$sel_m>m</option>
<option value=\"f\"$sel_f>f</option>
</select>;
 <span class=\"wsnw\">".mex("cittadinanza",$pag).": ".mostra_lista_relutenti("nazionalita",$nazionalita,$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti)."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazionalita','nazione','')\" value=\"#\">;</span>
 <span class=\"wsnw\">".mex("lingua",$pag).": <select name=\"lingua_cli\">";
if ($lingua_cli == "ita") $sel = " selected";
else $sel = "";
$opt_lingue = "<option value=\"ita\"$sel>Italiano</option>";
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if (@is_file("./includes/lang/$ini_lingua/l_n")) {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
if ($lingua_cli == $ini_lingua) $sel = " selected";
else $sel = "";
if ($ini_lingua == $lingua[$id_utente]) $opt_lingue = "<option value=\"$ini_lingua\"$sel>".ucfirst($nome_lingua)."</option>".$opt_lingue;
else $opt_lingue .= "<option value=\"$ini_lingua\"$sel>".ucfirst($nome_lingua)."</option>";
} # fine if (@is_file("./includes/lang/$ini_lingua/l_n"))
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$opt_lingue = "<option value=\"\">------</option>".$opt_lingue;
echo "$opt_lingue</select></span>";
if ($datiprenota) {
echo ".&nbsp;&nbsp; <span class=\"wsnw\"><label><input name=\"cliente_ospite\" value=\"SI\" type=\"checkbox\" checked>
".mex("Ospite della prenotazione",$pag)."</label>";
if ($num_tipologie > 1 or $num_app_richiesti1 > 1) {
$selected = " selected";
echo " <select name=\"prenota_cli_osp\"$selected>";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
echo "<option value=\"p$num1"."_$n_t\">$num1";
if ($num_tipologie > 1) echo " ".mex("tipologia",$pag)." $n_t";
echo "</option>";
$selected = "";
} # fine for $num1
} # fine for $n_t
echo "</select>";
} # fine if ($num_tipologie > 1 or $num_app_richiesti1 > 1)
else echo "<input type=\"hidden\" name=\"prenota_cli_osp\" value=\"p1_1\">";
echo "</span>";
} # fine if ($datiprenota)

mostra_funzjs_dati_rel("","",$id_sessione,$anno);
echo "<hr style=\"width: 95%\">
".mex("Data di nascita",$pag)." <span class=\"wsnw\">";
$sel_gnascita = "<select name=\"giornonascita\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
if ($giornonascita == $num) $sel = " selected";
else $sel = "";
$sel_gnascita .= "<option value=\"$num\"$sel>$num</option>";
} # fine for $num
$sel_gnascita .= "</select>";
$sel_mnascita = "<select name=\"mesenascita\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
if ($mesenascita == $num) $sel = " selected";
else $sel = "";
$sel_mnascita .= "<option value=\"$num\"$sel>$num</option>";
} # fine for $num
$sel_mnascita .= "</select>";
if ($stile_data == "usa") echo "$sel_mnascita/$sel_gnascita";
else echo "$sel_gnascita/$sel_mnascita";
echo "/<input type=\"text\" name=\"annonascita\" size=\"5\" maxlength=\"4\" value=\"$annonascita\"></span> (".mex("anno con 4 cifre",$pag)."),
<span class=\"wsnw smlscrfnt\">".mex("nazione di nascita",$pag).": ".mostra_lista_relutenti("nazionenascita",$nazionenascita,$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","regionenascita")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazionenascita','nazionalita','')\" value=\"#\">,</span><br>
<span class=\"wsnw smlscrfnt\">".mex("reg./prov. di nascita",$pag).": ".mostra_lista_relutenti("regionenascita",$regionenascita,$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","cittanascita","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('regionenascita','regione','')\" value=\"#\">,</span>
<span class=\"wsnw smlscrfnt\">".mex("città di nascita",$pag).": ".mostra_lista_relutenti("cittanascita",$cittanascita,$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('cittanascita','citta','')\" value=\"#\"></span>";
echo "<hr style=\"width: 95%\">
".mex("Residenza",$pag).": <span class=\"wsnw\"><select name=\"via\">
<option value=\"".mex("Via",$pag)."\">".mex("Via",$pag)."</option>
<option value=\"".mex("Piazza",$pag)."\">".mex("Piazza",$pag)."</option>
<option value=\"".mex("Viale",$pag)."\">".mex("Viale",$pag)."</option>
<option value=\"".mex("Piazzale",$pag)."\">".mex("Piazzale",$pag)."</option>
<option value=\"".mex("Vicolo",$pag)."\">".mex("Vicolo",$pag)."</option>";
if ($via) $sel = " selected";
else $sel = "";
echo "<option value=\"\"$sel>-----</option>
</select>
<input type=\"text\" name=\"nomevia\" value=\"$via\"></span>
<span class=\"wsnw\">Nº<input type=\"text\" name=\"numcivico\" size=\"4\" value=\"$numcivico\">,</span>
  <span class=\"wsnw\">".mex("CAP",$pag)."<input type=\"text\" name=\"cap\" size=\"6\" value=\"$cap\">,</span><br>
<span class=\"wsnw\">".mex("nazione",$pag).": ".mostra_lista_relutenti("nazione",$nazione,$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazione','nazionalita','')\" value=\"#\">,</span>
<span class=\"wsnw\">".mex("reg./prov.",$pag).": ".mostra_lista_relutenti("regione",$regione,$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","citta","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('regione','regionenascita','')\" value=\"#\">,</span>
<span class=\"wsnw\">".mex("città",$pag).": ".mostra_lista_relutenti("citta",$citta,$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('citta','cittanascita','')\" value=\"#\"></span>";
echo "<hr style=\"width: 95%\">
".mex("Documento",$pag).": ".mostra_lista_relutenti("tipodoc",$tipodoc,$id_utente,"nome_documentoid","iddocumentiid","iddocumentoid",$tabledocumentiid,$tablerelutenti,"","","SI");
echo "<input type=\"text\" name=\"documento\" value=\"$documento\">
<span class=\"wsnw\">".mex("scadenza",$pag).": ";
$sel_gscaddoc = "<select name=\"giornoscaddoc\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gscaddoc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_gscaddoc .= "</select>";
$sel_mscaddoc = "<select name=\"mesescaddoc\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mscaddoc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_mscaddoc .= "</select>";
if ($stile_data == "usa") echo "$sel_mscaddoc/$sel_gscaddoc";
else echo "$sel_gscaddoc/$sel_mscaddoc";
echo "/<select name=\"annoscaddoc\">";
$anno_corr = date("Y",(time() + (C_DIFF_ORE * 3600)));
for ($num1 = 0; $num1 < 12; $num1++) {
$num = $anno_corr - 12 + $num1;
echo "<option value=\"$num\">$num</option>";
} # fine for $num1
echo "<option value=\"\" selected>--</option>";
for ($num1 = 0; $num1 < 16; $num1++) {
$num = $anno_corr + $num1;
echo "<option value=\"$num\">$num</option>";
} # fine for $num1
echo "</select>;</span><br>
<span class=\"wsnw smlscrfnt\">".mex("nazione di rilascio",$pag).": ".mostra_lista_relutenti("nazionedoc",$nazionedoc,$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","regionedoc")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazionedoc','nazionalita','')\" value=\"#\">,</span>
<span class=\"wsnw\">".mex("reg./prov.",$pag).": ".mostra_lista_relutenti("regionedoc",$regionedoc,$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","cittadoc","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('regionedoc','regione','')\" value=\"#\">,</span>
<span class=\"wsnw\">".mex("città",$pag).": ".mostra_lista_relutenti("cittadoc",$cittadoc,$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('cittadoc','citta','')\" value=\"#\"></span>
<hr style=\"width: 95%\">
<span class=\"wsnw smlscrfnt\">".mex("Numero di telefono",$pag).": <input type=\"text\" name=\"telefono\" value=\"$telefono\"></span>
<span class=\"wsnw smlscrfnt\">".mex("Secondo telefono",$pag).": <input type=\"text\" name=\"telefono2\" value=\"$telefono2\"></span><br>
<span class=\"wsnw smlscrfnt\">".mex("Terzo telefono",$pag).": <input type=\"text\" name=\"telefono3\" value=\"$telefono3\"></span>
<span class=\"wsnw\">".mex("Numero di fax",$pag).": <input type=\"text\" name=\"fax\" value=\"$fax\"></span><br>
<span class=\"wsnw smlscrfnt\">E-mail: <input type=\"text\" name=\"email\" size=\"30\" value=\"$email\"></span>
<hr style=\"width: 95%\">
<span class=\"wsnw smlscrfnt\">".mex("Codice fiscale",$pag).": <input type=\"text\" name=\"cod_fiscale\" value=\"$cod_fiscale\"></span>
<span class=\"wsnw smlscrfnt\">".mex("Partita iva",$pag).": <input type=\"text\" name=\"partita_iva\" value=\"$partita_iva\"></span><br>";

if ($num_campi_pers) {
echo "<hr style=\"width: 95%\">
<table class=\"nomob\" cellspacing=0 cellpadding=0><tr>";
$pari = 0;
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
$opt = explode("<",$campi_pers[$num1]);
if ($opt[1] == "txt") echo "<td>".$opt[0].": <input type=\"text\" name=\"campo_pers$num1\" value=\"".${"campo_pers".$num1}."\"></td>";
else echo "<td><label><input type=\"checkbox\" name=\"campo_pers$num1\" value=\"1\"".$checked_cpc[$num1].">".$opt[0]."</label></td>";
if ($pari) {
$pari = 0;
echo "</tr><tr>";
} # fine if ($pari)
else {
$pari = 1;
echo "<td style=\"width: 50px;\">&nbsp;</td>";
} # fine else if ($pari)
} # fine for $num1
echo "</tr></table>";
} # fine if ($num_campi_pers)

if ($datiprenota) {
echo "<script type=\"text/javascript\">
<!--
function apri_cli (n_t,num_ar,num_pers) {
var bott_cli = document.getElementById('bott_cli'+num_ar+'_'+n_t);
bott_cli.style.visibility = 'hidden';
var elem_cli = document.getElementById('dati_cli'+num_ar+'_'+n_t);
elem_cli.style.visibility = 'visible';
var testo = '';
var suff = '';
var suffcp = '';
for (n1 = 1 ; n1 <= num_pers ; n1++) {
suff = '_'+n1+'_'+num_ar+'_'+n_t;
if (n1 == 1) suffcp = '';
else suffcp = '_1_'+num_ar+'_'+n_t;
testo += '<hr width=\"35%\">\
'+n1+'. ".addslashes(mex("Cognome",$pag)).": <input type=\"text\" id=\"cognome'+suff+'\" name=\"cognome'+suff+'\"><input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'cognome'+suff+'\',\'cognome'+suffcp+'\',\'\')\" value=\"#\">,\
 ".addslashes("<span class=\"wsnw\">".mex("nome",$pag)).": <input type=\"text\" name=\"nome'+suff+'\">;<\/span>\
 ".addslashes("<span class=\"wsnw\">".mex("sesso",$pag)).": <select name=\"sesso'+suff+'\">\
<option value=\"\" selected>-<\/option>\
<option value=\"m\">m<\/option>\
<option value=\"f\">f<\/option>\
<\/select>;<\/span><br>\
".addslashes(mex("cittadinanza",$pag)).": ".mostra_lista_relutenti("nazionalita'+suff+'","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","JS")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'nazionalita'+suff+'\',\'nazionalita'+suffcp+'\',\'\')\" value=\"#\">;\
 ".addslashes(mex("parentela",$pag)).": ".mostra_lista_relutenti("parentela'+suff+'","",$id_utente,"nome_parentela","idparentele","idparentela",$tableparentele,$tablerelutenti,"","JS")."<br>\
<div style=\"height: 4px\"><\/div>\
".addslashes(mex("Data di nascita",$pag)).": ";
$sel_gnascita = "<span class=\"wsnw\"><select name=\"giornonascita'+suff+'\">\
<option value=\"\" selected>--<\/option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gnascita .= "<option value=\"$num\">$num<\/option>";
} # fine for $num
$sel_gnascita .= "<\/select>";
$sel_mnascita = "<select name=\"mesenascita'+suff+'\">\
<option value=\"\" selected>--<\/option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mnascita .= "<option value=\"$num\">$num<\/option>";
} # fine for $num
$sel_mnascita .= "<\/select>";
if ($stile_data == "usa") echo "$sel_mnascita/$sel_gnascita";
else echo "$sel_gnascita/$sel_mnascita";
echo "/<input type=\"text\" name=\"annonascita'+suff+'\" size=\"5\" maxlength=\"4\" value=\"19\"><\/span> (".addslashes(mex("anno con 4 cifre",$pag))."),\
 <span class=\"wsnw smlscrfnt\">".mex("nazione di nascita",$pag).": ".mostra_lista_relutenti("nazionenascita'+suff+'","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","JS","","regione","regionenascita'+suff+'")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'nazionenascita'+suff+'\',\'nazionalita'+suff+'\',\'\')\" value=\"#\">,<\/span><br>\
<span class=\"wsnw smlscrfnt\">".mex("reg./prov. di nascita",$pag).": ".mostra_lista_relutenti("regionenascita'+suff+'","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","JS","","citta","cittanascita'+suff+'","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'regionenascita'+suff+'\',\'regionenascita'+suffcp+'\',\'\')\" value=\"#\">,<\/span>\
 <span class=\"wsnw smlscrfnt\">".mex("città di nascita",$pag).": ".mostra_lista_relutenti("cittanascita'+suff+'","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","JS","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'cittanascita'+suff+'\',\'cittanascita'+suffcp+'\',\'\')\" value=\"#\"><\/span><br>\
<div style=\"height: 4px\"><\/div>'
if (n1 == 1) {
testo += '".mex("nazione",$pag).": ".mostra_lista_relutenti("nazione'+suff+'","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","JS","","regione","regione'+suff+'")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'nazione'+suff+'\',\'nazionalita'+suff+'\',\'\')\" value=\"#\">,\
 ".mex("reg./prov.",$pag).": ".mostra_lista_relutenti("regione'+suff+'","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","JS","","citta","citta'+suff+'","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'regione'+suff+'\',\'regione\',\'\')\" value=\"#\">,\
 ".mex("città",$pag).": ".mostra_lista_relutenti("citta'+suff+'","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","JS","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'citta'+suff+'\',\'citta\',\'\')\" value=\"#\"><br>\
<div style=\"height: 4px\"><\/div>\
".addslashes(mex("Documento",$pag)).": ".mostra_lista_relutenti("tipodoc'+suff+'","",$id_utente,"nome_documentoid","iddocumentiid","iddocumentoid",$tabledocumentiid,$tablerelutenti,"","JS","SI");
echo "<input type=\"text\" name=\"documento'+suff+'\">\
 <span class=\"wsnw\">".addslashes(mex("scadenza",$pag)).": ";
$sel_gscaddoc = "<select name=\"giornoscaddoc'+suff+'\">\
<option value=\"\" selected>--<\/option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gscaddoc .= "<option value=\"$num\">$num<\/option>";
} # fine for $num
$sel_gscaddoc .= "<\/select>";
$sel_mscaddoc = "<select name=\"mesescaddoc'+suff+'\">\
<option value=\"\" selected>--<\/option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mscaddoc .= "<option value=\"$num\">$num<\/option>";
} # fine for $num
$sel_mscaddoc .= "<\/select>";
if ($stile_data == "usa") echo "$sel_mscaddoc/$sel_gscaddoc";
else echo "$sel_gscaddoc/$sel_mscaddoc";
echo "/<select name=\"annoscaddoc'+suff+'\">";
$anno_corr = date("Y",(time() + (C_DIFF_ORE * 3600)));
for ($num3 = 0 ; $num3 < 12 ; $num3++) {
$num = $anno_corr - 12 + $num3;
echo "<option value=\"$num\">$num<\/option>";
} # fine for $num3
echo "<option value=\"\" selected>--<\/option>";
for ($num3 = 0 ; $num3 < 12 ; $num3++) {
$num = $anno_corr + $num3;
echo "<option value=\"$num\">$num<\/option>";
} # fine for $num3
echo "<\/select>;<\/span><br>\
<span class=\"wsnw smlscrfnt\">".mex("nazione di rilascio",$pag).": ".mostra_lista_relutenti("nazionedoc'+suff+'","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","JS","","regione","regionedoc'+suff+'")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'nazionedoc'+suff+'\',\'nazionalita'+suff+'\',\'\')\" value=\"#\">,<\/span>\
 <span class=\"wsnw\">".mex("reg./prov.",$pag).": ".mostra_lista_relutenti("regionedoc'+suff+'","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","JS","","citta","cittadoc'+suff+'","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'regionedoc'+suff+'\',\'regione'+suff+'\',\'\')\" value=\"#\">,<\/span>\
 <span class=\"wsnw\">".mex("città",$pag).": ".mostra_lista_relutenti("cittadoc'+suff+'","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","JS","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val(\'cittadoc'+suff+'\',\'citta'+suff+'\',\'\')\" value=\"#\"><\/span><br>\
<div style=\"height: 4px\"><\/div>';
} // fine if (n1 == 1)
} // fine for n1
elem_cli.innerHTML = testo;
} // fine function apri_cli
-->
</script>
";
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
if (${"numpersone".$n_t}) {
$num_persone_tot = ${"numpersone".$n_t};
if (${"num_letti_agg".$n_t}["max"]) $num_persone_tot = $num_persone_tot + ${"num_letti_agg".$n_t}["max"];
elseif (${"num_letti_agg_max".$n_t}) $num_persone_tot = $num_persone_tot + ${"num_letti_agg_max".$n_t};
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
$num_persone_corr = $num_persone_tot;
if (${"diff_persone".$n_t} and ($num1 + ${"diff_persone".$n_t}) > ${"num_app_richiesti".$n_t}) $num_persone_corr = $num_persone_corr - 1;
echo "<hr style=\"width: 95%\">
".mex("Ospiti della prenotazione",$pag);
if (${"num_app_richiesti".$n_t} > 1) echo " $num1";
if ($num_tipologie > 1) echo " ".mex("tipologia",$pag)." $n_t";
echo " <button type=\"button\" id=\"bott_cli$num1"."_$n_t\" onclick=\"apri_cli($n_t,$num1,$num_persone_corr)\">
<img style=\"display: block;\" src=\"./img/freccia_destra_marg.png\" alt=\"&gt;\"></button>
<div id=\"dati_cli$num1"."_$n_t\" style=\"visibility: hidden;\"></div>";
} # fine for $num1
} # fine if (${"numpersone".$n_t})
} # fine for $n_t
} # fine if ($datiprenota)

echo "<hr style=\"width: 95%\">
<div style=\"text-align: center;\">";

if ($datiprenota) {
echo "<button id=\"inse\" class=\"icli\" type=\"submit\"><div>".mex("Inserisci i dati",$pag)."</div></button><br><br>
<input type=\"hidden\" name=\"inserire\" value=\"1\">
<input type=\"hidden\" name=\"inserire_dati_cliente\" value=\"SI\">";
$manda_cognome = "NO";
$manda_dati_assegnazione = "NO";
include("./includes/dati_form_prenotazione.php");
$manda_cognome = "";
} # fine if ($datiprenota)
else {

echo "<button id=\"inse\" class=\"icli\" type=\"submit\"><div>".mex("Inserisci i dati",$pag)."</div></button>
<input type=\"hidden\" name=\"inseriscicliente\" value=\"1\">
";

} # fine else if ($datiprenota)

echo "</div></div></form>";

if (!$datiprenota) {
echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button id=\"indi\" class=\"bkmm\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
</div></form><br>";
} # fine if (!$datiprenota)


} # fine if ($mostra_form_dati_cliente != "NO")


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI")
} # fine if ($id_utente)



?>
