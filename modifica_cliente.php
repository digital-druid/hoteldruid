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

$pag = "modifica_cliente.php";
$titolo = "HotelDruid: Modifica Cliente";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include(C_DATI_PATH."/lingua.php");
include("./includes/funzioni_costi_agg.php");
include("./includes/funzioni_clienti.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablecontratti = $PHPR_TAB_PRE."contratti";
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tableversioni = $PHPR_TAB_PRE."versioni";


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
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
$modifica_clienti = "NO";
if (substr($priv_ins_clienti,1,1) == "s") $modifica_clienti = "SI";
if (substr($priv_ins_clienti,1,1) == "p") $modifica_clienti = "PROPRI";
if (substr($priv_ins_clienti,1,1) == "g") { $modifica_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$vedi_clienti = "NO";
if (substr($priv_ins_clienti,2,1) == "s") $vedi_clienti = "SI";
if (substr($priv_ins_clienti,2,1) == "p") $vedi_clienti = "PROPRI";
if (substr($priv_ins_clienti,2,1) == "g") { $vedi_clienti = "GRUPPI"; $prendi_gruppi = "SI"; }
$priv_vedi_telefoni = substr($priv_ins_clienti,3,1);
$priv_vedi_indirizzo = substr($priv_ins_clienti,4,1);
$prefisso_clienti = risul_query($privilegi_globali_utente,0,'prefisso_clienti');
$attiva_prefisso_clienti = substr($prefisso_clienti,0,1);
if ($attiva_prefisso_clienti != "n") {
$prefisso_clienti = explode(",",$prefisso_clienti);
$prefisso_clienti = $prefisso_clienti[1];
} # fine if ($prefisso_clienti != "n")
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
if ($priv_vedi_tab_prenotazioni == "g") $prendi_gruppi = "SI";
$contratti_consentiti = risul_query($privilegi_annuali_utente,0,'contratti_consentiti');
$attiva_contratti_consentiti = substr($contratti_consentiti,0,1);
if ($attiva_contratti_consentiti == "s") {
$contratti_consentiti = explode(",",$contratti_consentiti);
unset($contratti_consentiti_vett);
for ($num1 = 1 ; $num1 < count($contratti_consentiti) ; $num1++) if ($contratti_consentiti[$num1]) $contratti_consentiti_vett[$contratti_consentiti[$num1]] = "SI";
} # fine if ($attiva_contratti_consentiti == "s")
$priv_mod_prenota = risul_query($privilegi_annuali_utente,0,'priv_mod_prenota');
$priv_mod_prenotazioni = substr($priv_mod_prenota,0,1);
if ($priv_mod_prenotazioni == "g") $prendi_gruppi = "SI";
$priv_mod_prenota_iniziate = substr($priv_mod_prenota,11,1);
$priv_mod_prenota_ore = substr($priv_mod_prenota,12,3);
} # fine else if (numlin_query($privilegi_annuali_utente) == 0)
unset($utenti_gruppi);
$utenti_gruppi[$id_utente] = 1;
if ($prendi_gruppi == "SI") {
$gruppi_utente = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente' and idgruppo is not NULL ");
$num_gruppi_utente = numlin_query($gruppi_utente);
for ($num1 = 0 ; $num1 < $num_gruppi_utente ; $num1++) {
$idgruppo = risul_query($gruppi_utente,$num1,'idgruppo');
$utenti_gruppo = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$idgruppo' ");
$num_utenti_gruppo = numlin_query($utenti_gruppo);
for ($num2 = 0 ; $num2 < $num_utenti_gruppo ; $num2++) $utenti_gruppi[risul_query($utenti_gruppo,$num2,'idutente')] = 1;
} # fine for $num1
} # fine if ($prendi_gruppi == "SI")
} # fine if ($id_utente != 1)
else {
$anno_utente_attivato = "SI";
$modifica_clienti = "SI";
$vedi_clienti = "SI";
$priv_vedi_telefoni = "s";
$priv_vedi_indirizzo = "s";
$attiva_prefisso_clienti = "n";
$priv_vedi_tab_prenotazioni = "s";
$attiva_contratti_consentiti = "n";
$priv_mod_prenotazioni = "s";
$priv_mod_prenota_iniziate = "s";
$priv_mod_prenota_ore = "000";
} # fine else if ($id_utente != 1)

$idclienti = aggslashdb($idclienti);

if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI") {
$cliente_proprio = esegui_query("select idclienti from $tableclienti where idclienti = '$idclienti' and utente_inserimento = '$id_utente'");
if (numlin_query($cliente_proprio) == 0) $modifica_clienti = "NO";
} # fine if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI")
elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI") {
$cliente_proprio = esegui_query("select utente_inserimento from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($cliente_proprio) == 0) $utente_inserimento = "0";
else $utente_inserimento = risul_query($cliente_proprio,0,'utente_inserimento');
if (!$utenti_gruppi[$utente_inserimento]) $modifica_clienti = "NO";
} # fine elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI")
if ($anno_utente_attivato == "SI" and $modifica_clienti != "NO" and $vedi_clienti != "NO" and $idclienti and controlla_num_pos($idclienti) == "SI") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$stile_soldi = stile_soldi();
$stile_data = stile_data();

$campi_pers = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente'");
if (numlin_query($campi_pers) == 1) {
$campi_pers = explode(">",risul_query($campi_pers,0,'valpersonalizza'));
$num_campi_pers = count($campi_pers);
} # fine if (numlin_query($campi_pers) == 1)
else $num_campi_pers = 0;



if ($cancella_cliente == "SI") {
if ($continua == "SI") {
# La carta di credito esterna va cancellata prima dei lock, altrimenti il modulo non può controllare i privilegi sul cliente
if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "") {
if (substr(C_URL_MOD_EXT_CARTE_CREDITO,0,17) == "https://localhost") $ext_html = file(C_URL_MOD_EXT_CARTE_CREDITO."modifica_cliente.php?id_sessione=$id_sessione&idclienti=$idclienti&cancella_cliente=SI",false,stream_context_create(array("ssl" => array("verify_peer" => true,"allow_self_signed" => true))));
else $ext_html = @file(C_URL_MOD_EXT_CARTE_CREDITO."modifica_cliente.php?id_sessione=$id_sessione&idclienti=$idclienti&cancella_cliente=SI");
} # fine if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
} # fine if ($continua == "SI")
$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);
$altre_tab_lock = array($tableanni);
$num_lock = 1;
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."prenota".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."rclientiprenota".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$altre_tab_lock[$num_lock] = $PHPR_TAB_PRE."soldi".risul_query($anni,$num1,'idanni');
$num_lock++;
} # fine for $num1
$tabelle_lock = array($tableclienti,$tablerelclienti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$cliente_esistente = esegui_query("select cognome,idclienti_compagni from $tableclienti where idclienti = '$idclienti' ");
if (numlin_query($cliente_esistente) == 1) {
$prenota_cliente_esistente = "NO";
for ($num1 = 0 ; $num1 < $num_anni ; $num1++) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tableprenota_mostra = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tablerclientiprenota_mostra = $PHPR_TAB_PRE."rclientiprenota".$anno_mostra;
$tablesoldi_mostra = $PHPR_TAB_PRE."soldi".$anno_mostra;
$prenotazioni = esegui_query("select * from $tableprenota_mostra where idclienti = '$idclienti'");
$ospiti = esegui_query("select * from $tablerclientiprenota_mostra where idclienti = '$idclienti'");
$soldi = esegui_query("select * from $tablesoldi_mostra where motivazione $LIKE '$idclienti".";%'");
if (numlin_query($prenotazioni) != 0 or numlin_query($ospiti) != 0 or numlin_query($soldi) != 0) $prenota_cliente_esistente = "SI";
} # fine for $num1
if ($prenota_cliente_esistente == "SI") echo mex("Questo cliente non si può cancellare perchè ancora associato a delle prenotazioni",$pag).".<br>";
else {
if ($continua != "SI") {
$cognome = risul_query($cliente_esistente,0,'cognome');
echo "".mex("Si è sicuri di voler <div style=\"display: inline; color: red;\">cancellare</div> il cliente",$pag)." <b>$cognome</b>?<br>
<table><tr><td height=2></td></tr><tr><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"cancella_cliente\" value=\"SI\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
<button class=\"ccli\" type=\"submit\"><div>".mex("SI",$pag)."</div></button>
</div></form></td><td style=\"width: 4px;\"></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<button class=\"gobk\" type=\"submit\"><div>".mex("NO",$pag)."</div></button>
</div></form></td></tr></table>";
} # fine if ($continua != "SI")
else {
$clienti_compagni = substr(risul_query($cliente_esistente,0,'idclienti_compagni'),1,-1);
if ($clienti_compagni) {
$clienti_compagni = explode(",",$clienti_compagni);
for ($num1 = 0 ; $num1 < count($clienti_compagni) ; $num1++) {
$idclienti_compagni = esegui_query("select idclienti_compagni from $tableclienti where idclienti = '".$clienti_compagni[$num1]."' ");
$idclienti_compagni = str_replace(",".$idclienti.",",",",risul_query($idclienti_compagni,0,'idclienti_compagni'));
esegui_query("update $tableclienti set idclienti_compagni = '$idclienti_compagni' where idclienti = '".$clienti_compagni[$num1]."' ");
} # fine for $num1
} # fine if ($clienti_compagni)
esegui_query("delete from $tableclienti where idclienti = '$idclienti' ");
esegui_query("delete from $tablerelclienti where idclienti = '$idclienti' ");
echo mex("Cliente cancellato",$pag)."!<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"".htmlspecialchars($origine)."\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form>";
} # fine else if ($continua != "SI")
} # fine else if ($prenota_cliente_esistente == "SI")
} # fine if (numlin_query($cliente_esistente) == 1)
else echo "<b>".mex("Cliente cancellato",$pag)."!</b><br>";
unlock_tabelle($tabelle_lock);
} # fine if ($cancella_cliente == "SI")

else {


function rowbgcolor () {
global $rowbgcolor,$t2row1color,$t2row2color;
if ($rowbgcolor == $t2row2color) $rowbgcolor = $t2row1color;
else $rowbgcolor = $t2row2color;
return $rowbgcolor;
} # fine function rowbgcolor



if ($modifica_cliente) {
$mostra_form_modifica_cliente = "NO";

if ($modifica_cliente != "Continua") {

if (@get_magic_quotes_gpc()) {
$n_cognome = stripslashes($n_cognome);
$n_nome = stripslashes($n_nome);
$n_soprannome = stripslashes($n_soprannome);
$n_titolo_cli = stripslashes($n_titolo_cli);
$n_documento = stripslashes($n_documento);
$n_tipodoc = stripslashes($n_tipodoc);
$n_nazionedoc = stripslashes($n_nazionedoc);
$n_regionedoc = stripslashes($n_regionedoc);
$n_cittadoc = stripslashes($n_cittadoc);
$n_cittanascita = stripslashes($n_cittanascita);
$n_regionenascita = stripslashes($n_regionenascita);
$n_nazionenascita = stripslashes($n_nazionenascita);
$n_nazionalita = stripslashes($n_nazionalita);
$n_nazione = stripslashes($n_nazione);
$n_regione = stripslashes($n_regione);
$n_citta = stripslashes($n_citta);
$n_nomevia = stripslashes($n_nomevia);
$n_numcivico = stripslashes($n_numcivico);
$n_cap = stripslashes($n_cap);
$n_telefono = stripslashes($n_telefono);
$n_telefono2 = stripslashes($n_telefono2);
$n_telefono3 = stripslashes($n_telefono3);
$n_cod_fiscale = stripslashes($n_cod_fiscale);
$n_partita_iva = stripslashes($n_partita_iva);
$n_email = stripslashes($n_email);
$n_fax = stripslashes($n_fax);
$n_commento = stripslashes($n_commento);
} # fine if (@get_magic_quotes_gpc())
$n_cognome = htmlspecialchars($n_cognome);
$n_nome = htmlspecialchars($n_nome);
$n_soprannome = htmlspecialchars($n_soprannome);
$n_titolo_cli = htmlspecialchars($n_titolo_cli);
if ($n_sesso and $n_sesso != "f") $n_sesso = "m";
$n_documento = htmlspecialchars($n_documento);
$n_tipodoc = htmlspecialchars($n_tipodoc);
$n_nazionedoc = htmlspecialchars($n_nazionedoc);
$n_regionedoc = htmlspecialchars($n_regionedoc);
$n_cittadoc = htmlspecialchars($n_cittadoc);
$n_cittanascita = htmlspecialchars($n_cittanascita);
$n_regionenascita = htmlspecialchars($n_regionenascita);
$n_nazionenascita = htmlspecialchars($n_nazionenascita);
$n_nazionalita = htmlspecialchars($n_nazionalita);
$n_nazione = htmlspecialchars($n_nazione);
$n_regione = htmlspecialchars($n_regione);
$n_citta = htmlspecialchars($n_citta);
$n_nomevia = htmlspecialchars($n_nomevia);
$n_numcivico = htmlspecialchars($n_numcivico);
$n_cap = htmlspecialchars($n_cap);
$n_telefono = htmlspecialchars($n_telefono);
$n_telefono2 = htmlspecialchars($n_telefono2);
$n_telefono3 = htmlspecialchars($n_telefono3);
$n_cod_fiscale = htmlspecialchars($n_cod_fiscale);
$n_partita_iva = htmlspecialchars($n_partita_iva);
$n_fax = htmlspecialchars($n_fax);
$n_email = htmlspecialchars($n_email);
$n_commento = htmlspecialchars($n_commento);

if (!$d_cognome) $d_cognome = "---";
if (!$d_nome) $d_nome = "---";
if (!$d_soprannome) $d_soprannome = "---";
if (!$d_titolo_cli) $d_titolo_cli = "---";
if (!$d_datanascita) $d_datanascita_f = "---";
else $d_datanascita_f = formatta_data($d_datanascita,$stile_data);
if (!$d_documento) $d_documento = "---";
if (!$d_scadenzadoc) $d_scadenzadoc_f = "---";
else $d_scadenzadoc_f = formatta_data($d_scadenzadoc,$stile_data);
if (!$d_nazionedoc) $d_nazionedoc = "---";
if (!$d_regionedoc) $d_regionedoc = "---";
if (!$d_cittadoc) $d_cittadoc = "---";
if (!$d_cittanascita) $d_cittanascita = "---";
if (!$d_regionenascita) $d_regionenascita = "---";
if (!$d_nazionenascita) $d_nazionenascita = "---";
if (!$d_nazionalita) $d_nazionalita = "---";
if (!$d_nazione) $d_nazione = "---";
if (!$d_regione) $d_regione = "---";
if (!$d_citta) $d_citta= "---";
if (!$d_via) $d_via = "---";
if (!$d_numcivico) $d_numcivico = "---";
if (!$d_telefono) $d_telefono = "---";
if (!$d_telefono2) $d_telefono2 = "---";
if (!$d_telefono3) $d_telefono3 = "---";
if (!$d_cod_fiscale) $d_cod_fiscale = "---";
if (!$d_partita_iva) $d_partita_iva = "---";
if (!$d_fax) $d_fax = "---";
if (!$d_cap) $d_cap = "---";
if (!$d_email) $d_email = "---";
if (!$d_nome_lingua) $d_nome_lingua = "---";

if (@get_magic_quotes_gpc()) {
$d_cognome = stripslashes($d_cognome);
$d_nome = stripslashes($d_nome);
$d_soprannome = stripslashes($d_soprannome);
$d_titolo_cli = stripslashes($d_titolo_cli);
$d_documento = stripslashes($d_documento);
$d_nazionedoc = stripslashes($d_nazionedoc);
$d_regionedoc = stripslashes($d_regionedoc);
$d_cittadoc = stripslashes($d_cittadoc);
$d_cittanascita = stripslashes($d_cittanascita);
$d_regionenascita = stripslashes($d_regionenascita);
$d_nazionenascita = stripslashes($d_nazionenascita);
$d_nazionalita = stripslashes($d_nazionalita);
$d_nazione = stripslashes($d_nazione);
$d_regione = stripslashes($d_regione);
$d_citta = stripslashes($d_citta);
$d_nomevia = stripslashes($d_nomevia);
$d_numcivico = stripslashes($d_numcivico);
$d_cap = stripslashes($d_cap);
$d_telefono = stripslashes($d_telefono);
$d_telefono2 = stripslashes($d_telefono2);
$d_telefono3 = stripslashes($d_telefono3);
$d_cod_fiscale = stripslashes($d_cod_fiscale);
$d_partita_iva = stripslashes($d_partita_iva);
$d_fax = stripslashes($d_fax);
$d_email = stripslashes($d_email);
$d_nome_lingua = stripslashes($d_nome_lingua);
$d_commento = stripslashes($d_commento);
} # fine if (@get_magic_quotes_gpc())
$d_cognome = htmlspecialchars($d_cognome);
$d_nome = htmlspecialchars($d_nome);
$d_soprannome = htmlspecialchars($d_soprannome);
$d_titolo_cli = htmlspecialchars($d_titolo_cli);
$d_documento = htmlspecialchars($d_documento);
$d_nazionedoc = htmlspecialchars($d_nazionedoc);
$d_regionedoc = htmlspecialchars($d_regionedoc);
$d_cittadoc = htmlspecialchars($d_cittadoc);
$d_cittanascita = htmlspecialchars($d_cittanascita);
$d_regionenascita = htmlspecialchars($d_regionenascita);
$d_nazionenascita = htmlspecialchars($d_nazionenascita);
$d_nazionalita = htmlspecialchars($d_nazionalita);
$d_nazione = htmlspecialchars($d_nazione);
$d_regione = htmlspecialchars($d_regione);
$d_citta = htmlspecialchars($d_citta);
$d_nomevia = htmlspecialchars($d_nomevia);
$d_numcivico = htmlspecialchars($d_numcivico);
$d_cap = htmlspecialchars($d_cap);
$d_telefono = htmlspecialchars($d_telefono);
$d_telefono2 = htmlspecialchars($d_telefono2);
$d_telefono3 = htmlspecialchars($d_telefono3);
$d_cod_fiscale = htmlspecialchars($d_cod_fiscale);
$d_partita_iva = htmlspecialchars($d_partita_iva);
$d_fax = htmlspecialchars($d_fax);
$d_email = htmlspecialchars($d_email);
$d_nome_lingua = htmlspecialchars($d_nome_lingua);
$d_commento = htmlspecialchars($d_commento);

if ($n_cognome) {
echo mex("Il cognome verrà cambiato da",$pag)." <b>$d_cognome</b> ".mex("a",$pag)." <b>";
if ($attiva_prefisso_clienti == "p") echo $prefisso_clienti;
echo $n_cognome;
if ($attiva_prefisso_clienti == "s") echo $prefisso_clienti;
echo "</b>.<br>";
} # fine if ($n_cognome)
if ($n_nome) { echo mex("Il nome verrà cambiato da",$pag)." <b>$d_nome</b> ".mex("a",$pag)." <b>$n_nome</b>.<br>"; }
if ($n_soprannome) { echo mex("Il soprannome verrà cambiato da",$pag)." <b>$d_soprannome</b> ".mex("a",$pag)." <b>$n_soprannome</b>.<br>"; }
if ($n_titolo_cli) { echo mex("Il titolo verrà cambiato da",$pag)." <b>$d_titolo_cli</b> ".mex("a",$pag)." <b>$n_titolo_cli</b>.<br>"; }
if ($n_sesso) { echo mex("Il sesso verrà cambiato a",$pag)." <b>$n_sesso</b>.<br>"; }

if ($id_nuovo_utente_inserimento != "" and $id_utente == 1) {
$id_nuovo_utente_inserimento = aggslashdb($id_nuovo_utente_inserimento);
$verifica_utente = esegui_query("select * from $tableutenti where idutenti = '$id_nuovo_utente_inserimento'");
if (numlin_query($verifica_utente) == 1) {
$nome_utente_nuovo = risul_query($verifica_utente,0,'nome_utente');
echo mex("Si considererà l'utente",$pag)." <b>$nome_utente_nuovo</b> ".mex("come colui che ha inserito il cliente",$pag).".<br>";
} # fine if (numlin_query($verifica_utente) == 1)
else unset($id_nuovo_utente_inserimento);
} # fine if ($id_nuovo_utente_inserimento != "" and $id_utente == 1)

if ($n_nazionalita) { echo mex("La cittadinanza verrà cambiata da",$pag)." <b>$d_nazionalita</b> ".mex("a",$pag)." <b>$n_nazionalita</b>.<br>"; }
if ($n_telefono) { echo mex("Il telefono verrà cambiato da",$pag)." <b>$d_telefono</b> ".mex("a",$pag)." <b>$n_telefono</b>.<br>"; }
if ($n_mesenascita and $n_giornonascita and $n_annonascita) {
#$n_datanascita = date("M d, Y" , mktime(0,0,0,$n_mesenascita,$n_giornonascita,$n_annonascita));
$n_datanascita = $n_annonascita."-".$n_mesenascita."-".$n_giornonascita;
$n_datanascita_f = formatta_data($n_datanascita,$stile_data);
echo mex("La data di nascita  verrà cambiata da",$pag)." <b>$d_datanascita_f</b> ".mex("a",$pag)." <b>$n_datanascita_f</b>.<br>";
} # fine if ($mesenascita and $giornonascita and $annonascita)
if ($n_nazionenascita) echo mex("La nazione di nascita verrà cambiata da",$pag)." <b>$d_nazionenascita</b> ".mex("a",$pag)." <b>$n_nazionenascita</b>.<br>";
if ($n_regionenascita) echo mex("La regione di nascita verrà cambiata da",$pag)." <b>$d_regionenascita</b> ".mex("a",$pag)." <b>$n_regionenascita</b>.<br>";
if ($n_cittanascita) echo mex("La città di nascita verrà cambiata da",$pag)." <b>$d_cittanascita</b> ".mex("a",$pag)." <b>$n_cittanascita</b>.<br>";
if ($n_nazione) echo mex("La nazione di residenza verrà cambiata da",$pag)." <b>$d_nazione</b> ".mex("a",$pag)." <b>$n_nazione</b>.<br>";
if ($n_regione) echo mex("La regione di residenza verrà cambiata da",$pag)." <b>$d_regione</b> ".mex("a",$pag)." <b>$n_regione</b>.<br>";
if ($n_citta) echo mex("La città di residenza verrà cambiata da",$pag)." <b>$d_citta</b> ".mex("a",$pag)." <b>$n_citta</b>.<br>";
if ($n_nomevia) {
include(C_DATI_PATH."/lingua.php");
if ($lingua_mex != "ita") include("./includes/lang/$lingua_mex/ordine_frasi.php");
if ($ordine_strada == 2) $n_nvia = $n_nomevia . " " . $n_via;
else $n_nvia = $n_via . " " . $n_nomevia;
echo mex("La via  verrà cambiata da",$pag)." <b>$d_via</b> ".mex("a",$pag)." <b>$n_nvia</b>.<br>";
} # fine if ($n_nomevia)
if ($n_numcivico) echo mex("Il numero civico verrà cambiato da",$pag)." <b>$d_numcivico</b> ".mex("a",$pag)." <b>$n_numcivico</b>.<br>";
if ($n_cap) echo mex("Il CAP verrà cambiato da",$pag)." <b>$d_cap</b> ".mex("a",$pag)." <b>$n_cap</b>.<br>";
if ($n_email) echo mex("L' email verrà cambiata da",$pag)." <b>$d_email</b> ".mex("a",$pag)." <b>$n_email</b>.<br>";

if ($n_lingua_cli) {
$n_nome_lingua = "";
if (preg_replace("/[a-z]{2,3}/","",$n_lingua_cli) == "") {
if ($n_lingua_cli == "ita") $n_nome_lingua = "italiano";
elseif (@is_file("./includes/lang/$n_lingua_cli/l_n")) {
$n_nome_lingua = file("./includes/lang/$n_lingua_cli/l_n");
$n_nome_lingua = togli_acapo($n_nome_lingua[0]);
} # fine elseif (@is_file("./includes/lang/$n_lingua_cli/l_n"))
} # fine if (preg_replace("/[a-z]{2,3}/","",$n_lingua_cli) == "")
if ($n_nome_lingua) echo mex("La lingua verrà cambiata da",$pag)." <b>".ucfirst($d_nome_lingua)."</b> ".mex("a",$pag)." <b>".ucfirst($n_nome_lingua)."</b>.<br>";
else $n_lingua_cli = "";
} # fine if ($n_lingua_cli)

if ($n_documento) {
if ($n_tipodoc) $n_doc = $n_tipodoc." ".$n_documento;
else $n_doc = $n_documento;
echo mex("Il documento verrà cambiato da",$pag)." <b>$d_documento</b> ".mex("a",$pag)." <b>$n_doc</b>.<br>";
} # fine if ($n_documento)
if ($n_nazionedoc) echo mex("La nazione di rilascio del documento verrà cambiata da",$pag)." <b>$d_nazionedoc</b> ".mex("a",$pag)." <b>$n_nazionedoc</b>.<br>";
if ($n_regionedoc) echo mex("La regione/provincia di rilascio del documento verrà cambiata da",$pag)." <b>$d_regionedoc</b> ".mex("a",$pag)." <b>$n_regionedoc</b>.<br>";
if ($n_cittadoc) echo mex("La città di rilascio del documento verrà cambiata da",$pag)." <b>$d_cittadoc</b> ".mex("a",$pag)." <b>$n_cittadoc</b>.<br>";
if ($n_mesescaddoc and $n_giornoscaddoc and $n_annoscaddoc) {
$n_scadenzadoc = $n_annoscaddoc."-".$n_mesescaddoc."-".$n_giornoscaddoc;
$n_scadenzadoc_f = formatta_data($n_scadenzadoc,$stile_data);
echo mex("La data di scadenza del documento  verrà cambiata da",$pag)." <b>$d_scadenzadoc_f</b> ".mex("a",$pag)." <b>$n_scadenzadoc_f</b>.<br>";
} # fine if ($mesescaddoc and $giornoscaddoc and $annoscaddoc)

if ($n_fax) echo mex("Il fax verrà cambiato da",$pag)." <b>$d_fax</b> ".mex("a",$pag)." <b>$n_fax</b>.<br>";
if ($n_telefono2) echo mex("Il 2° telefono verrà cambiato da",$pag)." <b>$d_telefono2</b> ".mex("a",$pag)." <b>$n_telefono2</b>.<br>";
if ($n_telefono3) echo mex("Il 3° telefono verrà cambiato da",$pag)." <b>$d_telefono3</b> ".mex("a",$pag)." <b>$n_telefono3</b>.<br>";
if ($n_cod_fiscale) echo mex("Il codice fiscale verrà cambiato da",$pag)." <b>$d_cod_fiscale</b> ".mex("a",$pag)." <b>$n_cod_fiscale</b>.<br>";
if ($n_partita_iva) echo mex("La partita iva verrà cambiata da",$pag)." <b>$d_partita_iva</b> ".mex("a",$pag)." <b>$n_partita_iva</b>.<br>";

if ($n_commento != $d_commento) echo mex("Il <b>commento</b> verrà cambiato",$pag).".<br>";

if ($c_nome) echo mex("Il <b>nome</b> verrà cancellato",$pag).".<br>";
if ($c_soprannome) echo mex("Il <b>soprannome</b> verrà cancellato",$pag).".<br>";
if ($c_titolo_cli) echo mex("Il <b>titolo</b> verrà cancellato",$pag).".<br>";
if ($c_nazionalita) echo mex("La <b>cittadinanza</b> verrà cancellata",$pag).".<br>";
if ($c_telefono) echo mex("Il <b>telefono</b> verrà cancellato",$pag).".<br>";
if ($c_datanascita) echo mex("La <b>data di nascita</b> verrà cancellata",$pag).".<br>";
if ($c_nazionenascita) echo mex("La <b>nazione di nascita</b> verrà cancellata",$pag).".<br>";
if ($c_regionenascita) echo mex("La <b>regione di nascita</b> verrà cancellata",$pag).".<br>";
if ($c_cittanascita) echo mex("La <b>città di nascita</b> verrà cancellata",$pag).".<br>";
if ($c_nazione) echo mex("La <b>nazione di residenza</b> verrà cancellata",$pag).".<br>";
if ($c_regione) echo mex("La <b>regione di residenza</b> verrà cancellata",$pag).".<br>";
if ($c_citta) echo mex("La <b>città di residenza</b> verrà cancellata",$pag).".<br>";
if ($c_via) echo mex("La <b>via</b> verrà cancellata",$pag).".<br>";
if ($c_numcivico) echo mex("Il <b>n° civico</b> verrà cancellato",$pag).".<br>";
if ($c_cap) echo mex("Il <b>CAP</b> verrà cancellato",$pag).".<br>";
if ($c_email) echo mex("L' <b>email</b> verrà cancellata",$pag).".<br>";
if ($c_lingua_cli) echo mex("La <b>lingua</b> verrà cancellata",$pag).".<br>";
if ($c_documento) echo mex("Il <b>documento</b> verrà cancellato",$pag).".<br>";
if ($c_nazionedoc) echo mex("La <b>nazione di rilascio del documento</b> verrà cancellata",$pag).".<br>";
if ($c_regionedoc) echo mex("La <b>regione/provincia di rilascio del documento</b> verrà cancellata",$pag).".<br>";
if ($c_cittadoc) echo mex("La <b>città di rilascio del documento</b> verrà cancellata",$pag).".<br>";
if ($c_scadenzadoc) echo mex("La <b>data di scadenza del documento</b> verrà cancellata",$pag).".<br>";
if ($c_fax) echo mex("Il <b>fax</b> verrà cancellato",$pag).".<br>";
if ($c_telefono2) echo mex("Il <b>2° telefono</b> verrà cancellato",$pag).".<br>";
if ($c_telefono3) echo mex("Il <b>3° telefono</b> verrà cancellato",$pag).".<br>";
if ($c_cod_fiscale) echo mex("Il <b>codice fiscale</b> verrà cancellato",$pag).".<br>";
if ($c_partita_iva) echo mex("La <b>partita iva</b> verrà cancellata",$pag).".<br>";

unset($d_campi_pers);
for ($num1 = 0 ; $num1 < $d_num_campi_pers ; $num1++) {
if (!${"n_campo_pers".$num1}) echo mex("Il campo",$pag)." \"<b>".${"d_campo_pers_nome".$num1}."</b>\" ".mex("verrà tolto",$pag).".<br>";
else {
if (${"n_campo_pers".$num1} != ${"d_campo_pers".$num1}) echo mex("Il campo",$pag)." \"<b>".${"d_campo_pers_nome".$num1}."</b>\" ".mex("verrà modificato",$pag).".<br>";
$d_campi_pers['esist'][${"d_campo_pers_nome".$num1}] = 1;
} # fine else if (!${"d_campo_pers".$num1})
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
if (${"campo_pers".$num1}) {
$opt = explode("<",$campi_pers[$num1]);
if (!$d_campi_pers['esist'][$opt[0]]) echo mex("Il campo",$pag)." \"<b>".$opt[0]."</b>\" ".mex("verrà aggiunto",$pag).".<br>";
} # fine if (${"campo_pers".$num1})
} # fine for $num1

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_cliente.php\"><div><br>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">

<input type=\"hidden\" name=\"n_cognome\" value=\"$n_cognome\">
<input type=\"hidden\" name=\"n_nome\" value=\"$n_nome\">
<input type=\"hidden\" name=\"n_soprannome\" value=\"$n_soprannome\">
<input type=\"hidden\" name=\"n_titolo_cli\" value=\"$n_titolo_cli\">
<input type=\"hidden\" name=\"n_sesso\" value=\"$n_sesso\">
<input type=\"hidden\" name=\"n_giornonascita\" value=\"$n_giornonascita\">
<input type=\"hidden\" name=\"n_mesenascita\" value=\"$n_mesenascita\">
<input type=\"hidden\" name=\"n_annonascita\" value=\"$n_annonascita\">
<input type=\"hidden\" name=\"id_nuovo_utente_inserimento\" value=\"$id_nuovo_utente_inserimento\">
<input type=\"hidden\" name=\"n_tipodoc\" value=\"$n_tipodoc\">
<input type=\"hidden\" name=\"n_documento\" value=\"$n_documento\">
<input type=\"hidden\" name=\"n_giornoscaddoc\" value=\"$n_giornoscaddoc\">
<input type=\"hidden\" name=\"n_mesescaddoc\" value=\"$n_mesescaddoc\">
<input type=\"hidden\" name=\"n_annoscaddoc\" value=\"$n_annoscaddoc\">
<input type=\"hidden\" name=\"n_nazionedoc\" value=\"$n_nazionedoc\">
<input type=\"hidden\" name=\"n_regionedoc\" value=\"$n_regionedoc\">
<input type=\"hidden\" name=\"n_cittadoc\" value=\"$n_cittadoc\">
<input type=\"hidden\" name=\"n_cittanascita\" value=\"$n_cittanascita\">
<input type=\"hidden\" name=\"n_regionenascita\" value=\"$n_regionenascita\">
<input type=\"hidden\" name=\"n_nazionenascita\" value=\"$n_nazionenascita\">
<input type=\"hidden\" name=\"n_nazionalita\" value=\"$n_nazionalita\">
<input type=\"hidden\" name=\"n_telefono\" value=\"$n_telefono\">
<input type=\"hidden\" name=\"n_via\" value=\"$n_via\">
<input type=\"hidden\" name=\"n_nomevia\" value=\"$n_nomevia\">
<input type=\"hidden\" name=\"n_numcivico\" value=\"$n_numcivico\">
<input type=\"hidden\" name=\"n_citta\" value=\"$n_citta\">
<input type=\"hidden\" name=\"n_nazione\" value=\"$n_nazione\">
<input type=\"hidden\" name=\"n_regione\" value=\"$n_regione\">
<input type=\"hidden\" name=\"n_cap\" value=\"$n_cap\">
<input type=\"hidden\" name=\"n_fax\" value=\"$n_fax\">
<input type=\"hidden\" name=\"n_email\" value=\"$n_email\">
<input type=\"hidden\" name=\"n_lingua_cli\" value=\"$n_lingua_cli\">
<input type=\"hidden\" name=\"n_telefono2\" value=\"$n_telefono2\">
<input type=\"hidden\" name=\"n_telefono3\" value=\"$n_telefono3\">
<input type=\"hidden\" name=\"n_cod_fiscale\" value=\"$n_cod_fiscale\">
<input type=\"hidden\" name=\"n_partita_iva\" value=\"$n_partita_iva\">
<input type=\"hidden\" name=\"n_commento\" value=\"$n_commento\">
<input type=\"hidden\" name=\"d_commento\" value=\"$d_commento\">
<input type=\"hidden\" name=\"d_num_campi_pers\" value=\"$d_num_campi_pers\">";
for ($num1 = 0 ; $num1 < $d_num_campi_pers ; $num1++) {
echo "<input type=\"hidden\" name=\"n_campo_pers$num1\" value=\"".${"n_campo_pers".$num1}."\">
<input type=\"hidden\" name=\"d_campo_pers_nome$num1\" value=\"".${"d_campo_pers_nome".$num1}."\">";
} # fine for for $num1
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) echo "<input type=\"hidden\" name=\"campo_pers$num1\" value=\"".${"campo_pers".$num1}."\">";


echo "<input type=\"hidden\" name=\"c_nome\" value=\"$c_nome\">
<input type=\"hidden\" name=\"c_soprannome\" value=\"$c_soprannome\">
<input type=\"hidden\" name=\"c_titolo_cli\" value=\"$c_titolo_cli\">
<input type=\"hidden\" name=\"c_datanascita\" value=\"$c_datanascita\">
<input type=\"hidden\" name=\"c_documento\" value=\"$c_documento\">
<input type=\"hidden\" name=\"c_scadenzadoc\" value=\"$c_scadenzadoc\">
<input type=\"hidden\" name=\"c_nazionedoc\" value=\"$c_nazionedoc\">
<input type=\"hidden\" name=\"c_regionedoc\" value=\"$c_regionedoc\">
<input type=\"hidden\" name=\"c_cittadoc\" value=\"$c_cittadoc\">
<input type=\"hidden\" name=\"c_cittanascita\" value=\"$c_cittanascita\">
<input type=\"hidden\" name=\"c_regionenascita\" value=\"$c_regionenascita\">
<input type=\"hidden\" name=\"c_nazionenascita\" value=\"$c_nazionenascita\">
<input type=\"hidden\" name=\"c_nazionalita\" value=\"$c_nazionalita\">
<input type=\"hidden\" name=\"c_telefono\" value=\"$c_telefono\">
<input type=\"hidden\" name=\"c_via\" value=\"$c_via\">
<input type=\"hidden\" name=\"c_numcivico\" value=\"$c_numcivico\">
<input type=\"hidden\" name=\"c_citta\" value=\"$c_citta\">
<input type=\"hidden\" name=\"c_nazione\" value=\"$c_nazione\">
<input type=\"hidden\" name=\"c_regione\" value=\"$c_regione\">
<input type=\"hidden\" name=\"c_cap\" value=\"$c_cap\">
<input type=\"hidden\" name=\"c_fax\" value=\"$c_fax\">
<input type=\"hidden\" name=\"c_email\" value=\"$c_email\">
<input type=\"hidden\" name=\"c_lingua_cli\" value=\"$c_lingua_cli\">
<input type=\"hidden\" name=\"c_telefono2\" value=\"$c_telefono2\">
<input type=\"hidden\" name=\"c_telefono3\" value=\"$c_telefono3\">
<input type=\"hidden\" name=\"c_cod_fiscale\" value=\"$c_cod_fiscale\">
<input type=\"hidden\" name=\"c_partita_iva\" value=\"$c_partita_iva\">
<input type=\"hidden\" name=\"modifica_cliente\" value=\"Continua\">
<button class=\"mcli\" type=\"submit\"><div>".mex("Continua",$pag)."</div></button>
<hr style=\"width: 95%\">
</div></form>";
} # fine if ($modifica_cliente != "Continua")


else {
$tabelle_lock = array($tableclienti,$tablerelclienti);
$altre_tab_lock = array($tablepersonalizza,$tableutenti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$cliente_esistente = esegui_query("select idclienti from $tableclienti where idclienti = '$idclienti' ");
if (numlin_query($cliente_esistente) == 1) {

if ($priv_vedi_telefoni != "s") {
$n_telefono = "";
$n_email = "";
$n_telefono2 = "";
$n_telefono3 = "";
$n_fax = "";
} # fine if ($priv_vedi_telefoni != "s")
if ($priv_vedi_indirizzo != "s") {
$n_via = "";
$n_nomevia = "";
$n_numcivico = "";
$n_cap = "";
} # fine if ($priv_vedi_indirizzo != "s")

unset($d_campi_pers);
$d_cam_per = esegui_query("select * from $tablerelclienti where idclienti = '$idclienti' and tipo = 'campo_pers' ");
$d_num_cam_per = numlin_query($d_cam_per);
for ($num1 = 0 ; $num1 < $d_num_cam_per ; $num1++) {
$nome_campo_pers = risul_query($d_cam_per,$num1,'testo1');
$cancella_campo_pers = 1;
for ($num2 = 0 ; $num2 < $d_num_campi_pers ; $num2++) {
if ($nome_campo_pers == ${"d_campo_pers_nome".$num2}) {
if (${"n_campo_pers".$num2}) {
$cancella_campo_pers = 0;
$tipo_campo_pers = risul_query($d_cam_per,$num1,'testo2');
$val_campo_pers = risul_query($d_cam_per,$num1,'testo3');
if ($tipo_campo_pers == "txt" and $val_campo_pers != ${"n_campo_pers".$num2}) esegui_query("update $tablerelclienti set testo3 = '".aggslashdb(${"n_campo_pers".$num2})."' where idclienti = '$idclienti' and testo1 = '".aggslashdb($nome_campo_pers)."' and tipo = 'campo_pers' ");
} # fine if (${"n_campo_pers".$num2})
break;
} # fine if ($nome_campo_pers == ${"d_campo_pers_nome".$num2})
} # fine for $num2
if ($cancella_campo_pers) esegui_query("delete from $tablerelclienti where idclienti = '$idclienti' and testo1 = '".aggslashdb($nome_campo_pers)."' and tipo = 'campo_pers' ");
else $d_campi_pers['esist'][$nome_campo_pers] = 1;
} # fine for $num1

$campi_pers_vett = array();
$campi_pers_vett['num'] = $num_campi_pers;
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
$opt = explode("<",$campi_pers[$num1]);
$campi_pers_vett[$num1] = $opt[0];
$campi_pers_vett['tipo'][$num1] = $opt[1];
if ($d_campi_pers['esist'][$opt[0]]) ${"campo_pers".$num1} = "";
$campi_pers_vett['val'][$num1] = ${"campo_pers".$num1};
} # fine for $num1

inserisci_dati_cliente($n_cognome,$n_nome,$n_soprannome,$n_titolo_cli,$n_sesso,$n_mesenascita,$n_giornonascita,$n_annonascita,$n_nazionenascita,$n_cittanascita,$n_regionenascita,$n_documento,$n_tipodoc,$n_mesescaddoc,$n_giornoscaddoc,$n_annoscaddoc,$n_cittadoc,$n_regionedoc,$n_nazionedoc,$n_nazionalita,$n_lingua_cli,$n_nazione,$n_citta,$n_regione,$n_via,$n_nomevia,$n_numcivico,$n_cap,$n_telefono,$n_telefono2,$n_telefono3,$n_fax,$n_email,$n_cod_fiscale,$n_partita_iva,"",$id_utente,$attiva_prefisso_clienti,$prefisso_clienti,$idclienti,"",$campi_pers_vett);

if ($id_nuovo_utente_inserimento != "" and $id_utente == 1) {
$id_nuovo_utente_inserimento = aggslashdb($id_nuovo_utente_inserimento);
$verifica_utente = esegui_query("select * from $tableutenti where idutenti = '$id_nuovo_utente_inserimento'");
if (numlin_query($verifica_utente) == 1) esegui_query("update $tableclienti set utente_inserimento = '$id_nuovo_utente_inserimento' where idclienti = '$idclienti' ");
} # fine if ($id_nuovo_utente_inserimento != "" and $id_utente == 1)

if (@get_magic_quotes_gpc()) $n_commento = stripslashes($n_commento);
$n_commento = htmlspecialchars($n_commento);
if ($n_commento != $d_commento) {
$n_commento = aggslashdb($n_commento);
esegui_query("update $tableclienti set commento = '$n_commento' where idclienti = '$idclienti' ");
} # fine if ($n_commento != $d_commento)

if ($c_nome) {
esegui_query("update $tableclienti set nome = '' where idclienti = '$idclienti' ");
} # fine if ($c_nome)
if ($c_soprannome) {
esegui_query("update $tableclienti set soprannome = '' where idclienti = '$idclienti' ");
} # fine if ($c_soprannome)
if ($c_titolo_cli) {
esegui_query("update $tableclienti set titolo = '' where idclienti = '$idclienti' ");
} # fine if ($c_titolo_cli)
if ($c_datanascita) {
esegui_query("update $tableclienti set datanascita = null where idclienti = '$idclienti' ");
} # fine if ($c_datanascita)
if ($c_nazionenascita) {
esegui_query("update $tableclienti set nazionenascita = '' where idclienti = '$idclienti' ");
} # fine if ($c_nazionenascita)
if ($c_regionenascita) {
esegui_query("update $tableclienti set regionenascita = '' where idclienti = '$idclienti' ");
} # fine if ($c_regionenascita)
if ($c_cittanascita) {
esegui_query("update $tableclienti set cittanascita = '' where idclienti = '$idclienti' ");
} # fine if ($c_cittanascita)
if ($c_nazionalita) {
esegui_query("update $tableclienti set nazionalita = '' where idclienti = '$idclienti' ");
} # fine if ($c_nazionalita)
if ($c_nazione) {
esegui_query("update $tableclienti set nazione = '' where idclienti = '$idclienti' ");
} # fine if ($c_nazione)
if ($c_regione) {
esegui_query("update $tableclienti set regione = '' where idclienti = '$idclienti' ");
} # fine if ($c_regione)
if ($c_citta) {
esegui_query("update $tableclienti set citta = '' where idclienti = '$idclienti' ");
} # fine if ($c_citta)
if ($priv_vedi_indirizzo == "s") {
if ($c_via) {
esegui_query("update $tableclienti set via = '' where idclienti = '$idclienti' ");
} # fine if ($c_via)
if ($c_numcivico) {
esegui_query("update $tableclienti set numcivico = '' where idclienti = '$idclienti' ");
} # fine if ($c_numcivico)
if ($c_cap) {
esegui_query("update $tableclienti set cap = '' where idclienti = '$idclienti' ");
} # fine if ($c_cap)
} # fine if ($priv_vedi_indirizzo == "s")
if ($priv_vedi_telefoni == "s") {
if ($c_telefono) {
esegui_query("update $tableclienti set telefono = '' where idclienti = '$idclienti' ");
} # fine if ($c_telefono)
if ($c_telefono2) {
esegui_query("update $tableclienti set telefono2 = '' where idclienti = '$idclienti' ");
} # fine if ($c_telefono2)
if ($c_telefono3) {
esegui_query("update $tableclienti set telefono3 = '' where idclienti = '$idclienti' ");
} # fine if ($c_telefono3)
if ($c_email) {
esegui_query("update $tableclienti set email = '' where idclienti = '$idclienti' ");
} # fine if ($c_email)
if ($c_fax) {
esegui_query("update $tableclienti set fax = '' where idclienti = '$idclienti' ");
} # fine if ($c_fax)
} # fine if ($priv_vedi_telefoni == "s")
if ($c_lingua_cli) {
esegui_query("update $tableclienti set lingua = '' where idclienti = '$idclienti' ");
} # fine if ($c_lingua_cli)
if ($c_documento) {
esegui_query("update $tableclienti set documento = '', tipodoc = '' where idclienti = '$idclienti' ");
} # fine if ($c_documento)
if ($c_nazionedoc) {
esegui_query("update $tableclienti set nazionedoc = '' where idclienti = '$idclienti' ");
} # fine if ($c_nazionedoc)
if ($c_regionedoc) {
esegui_query("update $tableclienti set regionedoc = '' where idclienti = '$idclienti' ");
} # fine if ($c_regionedoc)
if ($c_cittadoc) {
esegui_query("update $tableclienti set cittadoc = '' where idclienti = '$idclienti' ");
} # fine if ($c_cittadoc)
if ($c_scadenzadoc) {
esegui_query("update $tableclienti set scadenzadoc = null where idclienti = '$idclienti' ");
} # fine if ($c_scadenzadoc)
if ($c_cod_fiscale) {
esegui_query("update $tableclienti set cod_fiscale = '' where idclienti = '$idclienti' ");
} # fine if ($c_cod_fiscale)
if ($c_partita_iva) {
esegui_query("update $tableclienti set partita_iva = '' where idclienti = '$idclienti' ");
} # fine if ($c_partita_iva)
#echo mex("Il cliente",$pag)." $idclienti ".mex("è stato modificato",$pag).".<br>";
$mostra_form_modifica_cliente = "SI";
} # fine if (numlin_query($cliente_esistente) == 1)
else echo "<b>".mex("Cliente cancellato",$pag)."!</b><br>";
unlock_tabelle($tabelle_lock);
} # fine else if ($modifica_cliente != "Continua")

if ($mostra_form_modifica_cliente == "NO") {
echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_cliente.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">";
echo "<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
<br></div></form></div>";
} # fine if ($mostra_form_modifica_cliente == "NO")

} # fine if ($modifica_cliente)





if ($vedi_cc) {
if (function_exists('openssl_pkey_new')) {
if ($_SERVER["HTTPS"] == "on" or $_SERVER["SERVER_PORT"] == "443" or $_SERVER['SERVER_NAME'] == "localhost") {

$cert_cc = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'cert_cc' and idutente = '1'");
if (numlin_query($cert_cc)) {
$mostra_form_modifica_cliente = "NO";


$pass_scaduta = 0;
if (defined('C_GIORNI_SCADENZA_PASS_CC') and C_GIORNI_SCADENZA_PASS_CC > 0) {
$creazione_pass = risul_query($cert_cc,0,'valpersonalizza_num');
$limite_pass_vecchie = date("YmdH",(time() - (C_GIORNI_SCADENZA_PASS_CC * 86400) + (C_DIFF_ORE * 3600)));
if ($limite_pass_vecchie > $creazione_pass) {
$pass_scaduta = 1;
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"./personalizza.php\" target=\"_top\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_utente_mod\" value=\"$id_utente_mod\">
<input type=\"hidden\" name=\"aggiorna_qualcosa\" value=\"SI\">
<input type=\"hidden\" name=\"cambia_pass_cc\" value=\"1\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
".ucfirst(mex("password per la visualizzazione dei dati delle carte di credito",'personalizza.php'))." <b class=\"colwarn\">".mex("scaduta",$pag)."</b>. ";
if ($id_utente == 1) echo mex("Le carte di credito saranno visualizzabili di nuovo dopo aver cambiato la password",$pag).". <button class=\"edtm\" type=\"submit\"><div>".ucfirst(mex("cambia la password",'personalizza.php'))."</div></button>";
else echo mex("Le carte di credito saranno visualizzabili di nuovo dopo che l'utente amministratore avrà cambiato la password",$pag).".";
echo "</div></form><br><br>";
} # fine if ($limite_pass_vecchie > $creazione_pass)
} # fine if (defined('C_GIORNI_SCADENZA_PASS_CC') and C_GIORNI_SCADENZA_PASS_CC > 0)
if (!$pass_scaduta) {


$gest_cvc = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'gest_cvc' and idutente = '1'");
$gest_cvc = risul_query($gest_cvc,0,'valpersonalizza');

$tipo_transazione = "";
if ($id_transazione) recupera_dati_transazione($id_transazione,$id_sessione,$anno,"SI",$tipo_transazione);
if ($tipo_transazione == "mo_cc") {
$tabelle_lock = array($tablerelclienti);
$tabelle_lock = lock_tabelle($tabelle_lock);

if ($cancella_cc) {
esegui_query("delete from $tablerelclienti where idclienti = '$idclienti' and tipo = 'cc' ");
echo ucfirst(mex("i dati della carta di credito sono stati cancellati",$pag)).".<br>";
} # fine if ($cancella_cc)
else {

if ($delcvc and $gest_cvc == "SI") {
esegui_query("update $tablerelclienti set testo5 = NULL, testo6 = NULL where idclienti = '$idclienti' and tipo = 'cc'");
echo mex("CVC cancellato",$pag).".<br>";
} # fine if ($delcvc and $gest_cvc == "SI")
else {

if ($n_num_cc) {
$n_num_cc = str_replace(" ","",$n_num_cc);
if (preg_replace("/[0-9]*/","",$n_num_cc) != "") {
$errore = "SI";
echo ucfirst(mex("il numero di carta di credito è <span class=\"colred\">sbagliato</span>",$pag));
} # fine if (preg_replace("/[0-9]*/","",$n_num_cc) != "")
} # fine if ($n_num_cc)
if ($n_cvc_cc) {
$n_cvc_cc = str_replace(" ","",$n_cvc_cc);
if (preg_replace("/[0-9]*/","",$n_cvc_cc) != "") {
$errore = "SI";
echo ucfirst(mex("il numero CVC è <span class=\"colred\">sbagliato</span>",$pag));
} # fine if (preg_replace("/[0-9]*/","",$n_cvc_cc) != "")
} # fine if ($n_cvc_cc)
if ($gest_cvc != "SI") $n_cvc_cc = "";

if ($errore != "SI") {
$cert_cc = risul_query($cert_cc,0,'valpersonalizza');
$pub_key = openssl_pkey_get_public($cert_cc);
if ($n_annoscadcc and $n_mesescadcc) $n_datascadcc = date("Y-m-d",mktime(0,0,0,($n_mesescadcc + 1),0,$n_annoscadcc));
else $n_datascadcc = "";
$dati_carta = esegui_query("select * from $tablerelclienti where idclienti = '$idclienti' and tipo = 'cc'");
if (!numlin_query($dati_carta) and ($n_tipo_cc or $n_num_cc  or $n_nome_cc or $n_datascadcc or $n_cvc_cc)) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tablerelclienti (idclienti,numero,tipo,datainserimento,hostinserimento,utente_inserimento) values ('$idclienti','1','cc','$datainserimento','$HOSTNAME','$id_utente') ");
} # fine if (!numlin_query($dati_carta) and ($n_tipo_cc or...
if ($n_tipo_cc) esegui_query("update $tablerelclienti set testo1 = '".aggslashdb($n_tipo_cc)."' where idclienti = '$idclienti' and tipo = 'cc' ");
if ($n_num_cc) {
openssl_public_encrypt($n_num_cc,$val_crypt,$pub_key);
$val_crypt = base64_encode($val_crypt);
esegui_query("update $tablerelclienti set testo2 = '".aggslashdb($val_crypt)."' where idclienti = '$idclienti' and tipo = 'cc' ");
} # fine if ($n_num_cc)
if ($n_nome_cc) esegui_query("update $tablerelclienti set testo3 = '".aggslashdb($n_nome_cc)."' where idclienti = '$idclienti' and tipo = 'cc' ");
if ($n_datascadcc) esegui_query("update $tablerelclienti set testo4 = '".aggslashdb($n_datascadcc)."' where idclienti = '$idclienti' and tipo = 'cc' ");
if ($n_cvc_cc) {
openssl_public_encrypt($n_cvc_cc,$val_crypt,$pub_key);
$val_crypt = base64_encode($val_crypt);
esegui_query("update $tablerelclienti set testo5 = '".aggslashdb($val_crypt)."' where idclienti = '$idclienti' and tipo = 'cc' ");
} # fine if ($n_cvc_cc)
echo ucfirst(mex("i dati della carta di credito sono stati inseriti",$pag));
} # fine if ($errore != "SI")

} # fine else if ($delcvc and $gest_cvc == "SI")
} # fine esle if ($cancella_cc)
unlock_tabelle($tabelle_lock);
} # fine if ($tipo_transazione == "mo_cc")
else {

$tabelle_lock = array($tableversioni,$tabletransazioni);
$altre_tab_lock = array($tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$minuti_durata_sessione = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_sessione' and idutente = '1'");
$minuti_durata_sessione = risul_query($minuti_durata_sessione,0,'valpersonalizza_num');
if ($minuti_durata_sessione > 15) $minuti_durata_sessione = 15;
$limite_transazioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_sessione * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tabletransazioni where ultimo_accesso <= '$limite_transazioni_vecchie' and (tipo_transazione = 'mo_cc' or tipo_transazione = 'lo_cc') ");
$minuti_durata_blocco = 30;
$limite_transazioni_vecchie = date("Y-m-d H:i:s",(time() - ($minuti_durata_blocco * 60) + (C_DIFF_ORE * 3600)));
esegui_query("delete from $tabletransazioni where ultimo_accesso <= '$limite_transazioni_vecchie' and tipo_transazione = 'er_cc' ");
$err_transazioni = esegui_query("select * from $tabletransazioni where tipo_transazione = 'er_cc' and dati_transazione2 = '$id_utente' ");
$err_transazioni2 = esegui_query("select * from $tabletransazioni where tipo_transazione = 'er_cc' ");
if (numlin_query($err_transazioni) < 7 and numlin_query($err_transazioni2) < 12) {

if (!$pass_cc) {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
list($usec, $sec) = explode(' ', microtime());
mt_srand((float) $sec + ((float) $usec * 100000));
$val_casuale = mt_rand(100000,999999);
$versione_transazione = prendi_numero_versione($tableversioni);
$id_transazione = $adesso.$val_casuale.$versione_transazione;
esegui_query("insert into $tabletransazioni (idtransazioni,idsessione,tipo_transazione,anno,dati_transazione1,dati_transazione2,ultimo_accesso) 
values ('$id_transazione','$id_sessione','lo_cc','$anno','$idclienti','$id_utente','$ultimo_accesso')");
unlock_tabelle($tabelle_lock);
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"vedi_cc\" value=\"SI\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
".ucfirst(mex("password per la visualizzazione delle carte di credito",$pag)).":
 <input type=\"password\" name=\"pass_cc\" value=\"\">
 <button class=\"login\" type=\"submit\"><div>".ucfirst(mex("invia",$pag))."</div></button>
</div></form>";
} # fine if (!$pass_cc)
elseif ($tipo_transazione == "lo_cc") {

esegui_query("update $tabletransazioni set tipo_transazione = 'er_cc', ultimo_accesso = '$ultimo_accesso' where idtransazioni = '$id_transazione' ");
unlock_tabelle($tabelle_lock);
$cert_cc = risul_query($cert_cc,0,'valpersonalizza');
$priv_key_cc = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'priv_key_cc' and idutente = '1'");
$priv_key_cc = risul_query($priv_key_cc,0,'valpersonalizza');
$pub_key = openssl_pkey_get_public($cert_cc);
openssl_public_encrypt('prova',$val_crypt,$pub_key);
$val_decrypt = "";
$res_pk = openssl_get_privatekey($priv_key_cc,$pass_cc);
if ($res_pk) openssl_private_decrypt($val_crypt,$val_decrypt,$res_pk);
if ($val_decrypt == 'prova') {

esegui_query("update $tabletransazioni set tipo_transazione = 'mo_cc' where idtransazioni = '$id_transazione' ");
$dati_carta = esegui_query("select * from $tablerelclienti where idclienti = '$idclienti' and tipo = 'cc'");
if (numlin_query($dati_carta)) {
$d_tipo_cc = risul_query($dati_carta,0,'testo1');
$d_num_cc = risul_query($dati_carta,0,'testo2');
if ($d_num_cc) openssl_private_decrypt(base64_decode($d_num_cc),$d_num_cc,$res_pk);
$d_nome_cc = risul_query($dati_carta,0,'testo3');
$d_scad_cc = risul_query($dati_carta,0,'testo4');
if ($d_scad_cc) $d_scad_cc_f = substr($d_scad_cc,5,2)."-".substr($d_scad_cc,0,4);
$d_cvc_cc = risul_query($dati_carta,0,'testo5');
$d_cvc_visto_cc = risul_query($dati_carta,0,'testo6');
if (strcmp($d_cvc_cc,"")) {
openssl_private_decrypt(base64_decode($d_cvc_cc),$d_cvc_cc,$res_pk);
if ($d_cvc_visto_cc) esegui_query("update $tablerelclienti set testo5 = NULL, testo6 = NULL where idclienti = '$idclienti' and tipo = 'cc'");
else esegui_query("update $tablerelclienti set testo6 = '1' where idclienti = '$idclienti' and tipo = 'cc'");
} # fine if (strcmp($d_cvc_cc,""))
} # fine if (numlin_query($dati_carta))
$cliente = esegui_query("select * from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($cliente)) {
$d_cognome = risul_query($cliente,0,'cognome');
$d_nome = risul_query($cliente,0,'nome');
} # fine if (numlin_query($cliente))

echo "<h4 id=\"h_crc\"><span>".ucfirst(mex("carta di credito del cliente",$pag))." $idclienti ($d_cognome";
if ($d_nome) echo " $d_nome";
echo ")</span></h4><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"vedi_cc\" value=\"SI\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
<table cellspacing=0 cellpadding=6>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Tipo",$pag).": <b>$d_tipo_cc</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_tipo_cc\" size=\"16\"></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Numero",$pag).": <b>$d_num_cc</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_num_cc\" size=\"20\"></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Titolare",$pag).": <b>$d_nome_cc</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_nome_cc\" size=\"28\"></td></tr>";

if (date("Ymd",(time() + (C_DIFF_ORE * 3600))) <= str_replace("-","",$d_scad_cc)) echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Scadenza",$pag).": <b>$d_scad_cc_f</b></td>";
else echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Scadenza",$pag).": <b class=\"colred\">$d_scad_cc_f</b></td>";
echo "<td>".mex("cambia in",$pag).": ";
$sel_mscadcc = "<select name=\"n_mesescadcc\">
<option value=\"\" selected>--</option>";
for ($num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mscadcc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_mscadcc .= "</select>";
echo "$sel_mscadcc/<select name=\"n_annoscadcc\">";
$anno_corr = date("Y",(time() + (C_DIFF_ORE * 3600)));
echo "<option value=\"\" selected>--</option>";
for ($num1 = 0; $num1 < 15; $num1++) {
$num = $anno_corr + $num1;
echo "<option value=\"$num\">$num</option>";
} # fine for $num1
echo "</select></td></tr>";

if ($gest_cvc == "SI") {
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("CVC",$pag).": <b>$d_cvc_cc</b></td><td>";
if (strcmp($d_cvc_cc,"")) {
if ($d_cvc_visto_cc) echo "<b class=\"colwarn\">".mex("NOTA",$pag)."</b>: ".mex("questo valore è gia stato cancellato, questa è l'ultima volta che viene visualizzato",$pag).".";
else echo mex("Questo valore verrà visualizzato solo un'altra volta",$pag).". <input class=\"sbutton\" type=\"submit\" name=\"delcvc\" value=\"".mex("Cancellalo ora",$pag)."\">";
} # fine if (strcmp($d_cvc_cc,""))
else echo mex("cambia in",$pag).": <input type=\"text\" name=\"n_cvc_cc\" size=\"6\">";
echo "</td></tr>
<tr><td style=\"height: 1px;\"></td></tr>";
} # fine if ($gest_cvc == "SI")

echo "</table>
<div style=\"text-align: center;\">
<button class=\"mcrc\" type=\"submit\"><div>".ucfirst(mex("modifica i dati della carta di credito",$pag))."</div></button>
</div></div></form>";
if (numlin_query($dati_carta)) {
echo "<br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"vedi_cc\" value=\"SI\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
<input type=\"hidden\" name=\"cancella_cc\" value=\"SI\">
<button class=\"canc\" type=\"submit\"><div>".ucfirst(mex("cancella questa carta di credito",$pag))."</div></button>
</div></form>";
} # fine if (numlin_query($dati_carta))
} # fine if ($val_decrypt == 'prova')
else echo "".ucfirst(mex("password errata",$pag)).".<br>";
} # fine elseif ($tipo_transazione == "lo_cc")

} # fine if (numlin_query($err_transazioni) < 7 and numlin_query($err_transazioni2) < 12)
else {
echo "<br>".mex("Login temporaneamente bloccato",$pag).".<br><br>";
unlock_tabelle($tabelle_lock);
} # fine else if (numlin_query($err_transazioni) < 7 and numlin_query($err_transazioni2) < 12)
} # fine else if ($tipo_transazione == "mo_cc")


} # fine if (!$pass_scaduta)
echo "<br><br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></div></form>";
} # fine if (numlin_query($cert_cc))
} # fine if ($_SERVER["HTTPS"] == "on" or $_SERVER["SERVER_PORT"] == "443" or...
else echo "<span class=\"colred\">".mex("I dati della carta di credito possono essere visti solo su una connessione sicura",$pag)."</span>.<br><br>";
} # fine if (function_exists('openssl_pkey_new'))
} # fine if ($vedi_cc)





if ($mostra_form_modifica_cliente != "NO") {

# Form per modificare il cliente.
echo "<h3 id=\"h_mcli\"><span>".mex("Modifica i dati del cliente",$pag)." $idclienti</span></h3><br>
<hr style=\"width: 95%;\">";

$cliente = esegui_query("select * from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($cliente) != 0) {
$d_cognome = risul_query($cliente,0,'cognome');
$d_nome = risul_query($cliente,0,'nome');
$d_soprannome = risul_query($cliente,0,'soprannome');
$d_titolo_cli = risul_query($cliente,0,'titolo');
$d_sesso = risul_query($cliente,0,'sesso');
$d_scadenzadoc = risul_query($cliente,0,'scadenzadoc');
if ($d_scadenzadoc) $d_scadenzadoc_f = formatta_data($d_scadenzadoc,$stile_data);
if ($priv_vedi_indirizzo == "s") {
$d_via = risul_query($cliente,0,'via');
$d_numcivico = risul_query($cliente,0,'numcivico');
$d_cap = risul_query($cliente,0,'cap');
} # fine if ($priv_vedi_indirizzo == "s")
$d_citta = risul_query($cliente,0,'citta');
$d_nazione = risul_query($cliente,0,'nazione');
$d_regione = risul_query($cliente,0,'regione');
if ($priv_vedi_telefoni == "s") {
$d_telefono = risul_query($cliente,0,'telefono');
$d_fax = risul_query($cliente,0,'fax');
$d_email = risul_query($cliente,0,'email');
$d_telefono2 = risul_query($cliente,0,'telefono2');
$d_telefono3 = risul_query($cliente,0,'telefono3');
} # fine if ($priv_vedi_telefoni == "s")

$d_datanascita = risul_query($cliente,0,'datanascita');
if ($d_datanascita) $d_datanascita_f = formatta_data($d_datanascita,$stile_data);
$d_documento = risul_query($cliente,0,'documento');
$d_tipodoc = risul_query($cliente,0,'tipodoc');
$d_nazionedoc = risul_query($cliente,0,'nazionedoc');
$d_regionedoc = risul_query($cliente,0,'regionedoc');
$d_cittadoc = risul_query($cliente,0,'cittadoc');
$d_cittanascita = risul_query($cliente,0,'cittanascita');
$d_regionenascita = risul_query($cliente,0,'regionenascita');
$d_nazionenascita = risul_query($cliente,0,'nazionenascita');
$d_nazionalita = risul_query($cliente,0,'nazionalita');
$d_lingua_cli = risul_query($cliente,0,'lingua');
$d_cod_fiscale = risul_query($cliente,0,'cod_fiscale');
$d_partita_iva = risul_query($cliente,0,'partita_iva');
$d_commento = risul_query($cliente,0,'commento');
$d_data_inserimento = risul_query($cliente,0,'datainserimento');
$d_data_inserimento_vedi = substr($d_data_inserimento,0,-3);
$id_utente_inserimento = risul_query($cliente,0,'utente_inserimento');
if ($d_nazionalita) $nazione_def = $d_nazionalita;
elseif ($d_nazione) $nazione_def = $d_nazione;
elseif ($d_nazionedoc) $nazione_def = $d_nazionedoc;
elseif ($d_nazionenascita) $nazione_def = $d_nazionenascita;
$nazione_def = addslashes($nazione_def);
if ($d_regionenascita) $regione_def = $d_regionenascita;
elseif ($d_regione) $regione_def = $d_regione;
elseif ($d_regionedoc) $regione_def = $d_regionedoc;
elseif ($d_regionenascita) $regione_def = $d_regionenascita;
$regione_def = addslashes($regione_def);
if ($d_cittanascita) $citta_def = $d_cittanascita;
elseif ($d_citta) $citta_def = $d_citta;
elseif ($d_cittadoc) $citta_def = $d_cittadoc;
elseif ($d_cittanascita) $citta_def = $d_cittanascita;
$citta_def = addslashes($citta_def);

$d_cam_per = esegui_query("select * from $tablerelclienti where idclienti = '$idclienti' and tipo = 'campo_pers' ");
$d_num_campi_pers = numlin_query($d_cam_per);
for ($num1 = 0 ; $num1 < $d_num_campi_pers ; $num1++) {
$d_campi_pers[$num1]['nome'] = risul_query($d_cam_per,$num1,'testo1');
$d_campi_pers[$num1]['tipo'] = risul_query($d_cam_per,$num1,'testo2');
$d_campi_pers[$num1]['val'] = risul_query($d_cam_per,$num1,'testo3');
$d_campi_pers['esist'][$d_campi_pers[$num1]['nome']] = 1;
} # fine for $num1

echo "<table cellspacing=0 cellpadding=0 border=0 width=\"97%\">
<tr><td align=\"right\" style=\"font-size: 80%;\">";
$pass_cc = 0;
$orig_hd = "";
if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "") {
$action_cc = C_URL_MOD_EXT_CARTE_CREDITO.$pag;
$d_cliente = "<input type=\"hidden\" name=\"d_cognome\" value=\"".htmlspecialchars($d_cognome)."\">
<input type=\"hidden\" name=\"d_nome\" value=\"".htmlspecialchars($d_nome)."\">";
if (substr(C_URL_MOD_EXT_CARTE_CREDITO,0,17) == "https://localhost") $ext_html = file(C_URL_MOD_EXT_CARTE_CREDITO."personalizza.php?id_sessione=$id_sessione&stato_cc=1",false,stream_context_create(array("ssl" => array("verify_peer" => true,"allow_self_signed" => true))));
else $ext_html = @file(C_URL_MOD_EXT_CARTE_CREDITO."personalizza.php?id_sessione=$id_sessione&stato_cc=1");
if ($ext_html and strstr(implode("",$ext_html),"pass_cc_attiva")) {
$pass_cc = 1;
if (@$_SERVER['SERVER_NAME'] and $_SERVER['PHP_SELF']) {
if ($_SERVER['SERVER_PORT'] == "443" or ($_SERVER['HTTPS'] and $_SERVER['HTTPS'] != "off")) $orig_hd = "https://";
else $orig_hd = "http://";
$orig_hd = "<input type=\"hidden\" name=\"orig_hd\" value=\"".$orig_hd.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."\">";
} # fine if (@$_SERVER['SERVER_NAME'] and $_SERVER['PHP_SELF'])
} # fine if ($ext_html and strstr(implode("",$ext_html),"pass_cc_attiva"))
} # fine if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
else {
$action_cc = $pag;
$d_cliente = "";
if (function_exists('openssl_pkey_new')) {
$cert_cc = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'cert_cc' and idutente = '1'");
if (numlin_query($cert_cc)) $pass_cc = 1;
} # fine if (function_exists('openssl_pkey_new'))
} # fine else if (defined('C_URL_MOD_EXT_CARTE_CREDITO') and C_URL_MOD_EXT_CARTE_CREDITO != "")
if ($pass_cc) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$action_cc\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
$orig_hd
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"vedi_cc\" value=\"SI\">
$d_cliente";
} # fine if ($pass_cc)
echo "<small>".ucfirst(mex("data inserimento",$pag)).": ".str_replace("--","",formatta_data($d_data_inserimento_vedi,$stile_data))."</small>";
if ($pass_cc) {
echo "&nbsp;&nbsp;&nbsp;&nbsp;
<button class=\"crcm\" type=\"submit\" style=\"font-size: 80%;\"><div>".ucfirst(mex("carte di credito",$pag))."</div></button>
</div></form>";
} # fine if ($pass_cc)
echo "</td></tr></table>";

} # fine if (numlin_query($cliente) != 0)
else echo "<b>".mex("Cliente cancellato",$pag)."!</b><br>";

echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_cliente.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<table class=\"modres floatleft\">
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Cognome",$pag).": <b>$d_cognome</b></td><td>".mex("cambia in",$pag).": ";
if ($attiva_prefisso_clienti == "p") echo $prefisso_clienti;
echo "<input type=\"text\" name=\"n_cognome\" size=\"20\">";
if ($attiva_prefisso_clienti == "s") echo $prefisso_clienti;
echo "</td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Nome",$pag).": <b>$d_nome</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_nome\" size=\"20\">
<br><label><input type=\"checkbox\" name=\"c_nome\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".ucfirst(mex("soprannome",$pag)).": <b>$d_soprannome</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_soprannome\" size=\"20\">
<br><label><input type=\"checkbox\" name=\"c_soprannome\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
$titoli_cliente = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'titoli_cliente' and idutente = '$id_utente'");
if (numlin_query($titoli_cliente) == 1) $titoli_cliente = risul_query($titoli_cliente,0,"valpersonalizza");
else $titoli_cliente = "";
if ($titoli_cliente) {
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Titolo",$pag).": <b>$d_titolo_cli</b></td>
<td>".mex("cambia in",$pag).": 
<select name=\"n_titolo_cli\">
<option value=\"\">--</option>";
$titoli_cliente = explode(">",$titoli_cliente);
for ($num1 = 0 ; $num1 < count($titoli_cliente) ; $num1++) {
$opt = explode("<",$titoli_cliente[$num1]);
echo "<option value=\"".$opt[0]."\">".$opt[0]."</option>";
} # fine for $num1
echo "</select>
<br><label><input type=\"checkbox\" name=\"c_titolo_cli\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
} # fine if ($titoli_cliente)
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Sesso",$pag).": <b>$d_sesso</b></td>
<td>".mex("cambia in",$pag).": <select name=\"n_sesso\">
<option value=\"\" selected>-</option>
<option value=\"m\">m</option>
<option value=\"f\">f</option>
</select></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Cittadinanza",$pag).": <b>$d_nazionalita</b></td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_nazionalita","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti)."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_nazionalita','n_nazionenascita','$nazione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_nazionalita\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Data di nascita",$pag).": <b>$d_datanascita_f</b></td>
<td>".mex("cambia in",$pag).": ";
$sel_gnascita = "<select name=\"n_giornonascita\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gnascita .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_gnascita .= "</select>";
$sel_mnascita = "<select name=\"n_mesenascita\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mnascita .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_mnascita .= "</select>";
if ($stile_data == "usa") echo "$sel_mnascita/$sel_gnascita";
else echo "$sel_gnascita/$sel_mnascita";
echo "/<input type=\"text\" name=\"n_annonascita\" size=\"5\" maxlength=\"4\" value=\"19\">
<br><label><input type=\"checkbox\" name=\"c_datanascita\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Nazione di nascita",$pag).": <b>$d_nazionenascita</b>";
mostra_funzjs_cpval();
mostra_funzjs_dati_rel("","",$id_sessione,$anno);
echo "</td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_nazionenascita","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","n_regionenascita")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_nazionenascita','n_nazionalita','$nazione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_nazionenascita\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Reg./Prov. di nascita",$pag).": <b>$d_regionenascita</b></td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_regionenascita","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","n_cittanascita","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_regionenascita','n_regione','$regione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_regionenascita\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Città di nascita",$pag).": <b>$d_cittanascita</b></td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_cittanascita","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_cittanascita','n_citta','$citta_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_cittanascita\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Nazione di residenza",$pag).": <b>$d_nazione</b></td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_nazione","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","n_regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_nazione','n_nazionalita','$nazione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_nazione\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Reg./Prov. di residenza",$pag).": <b>$d_regione</b></td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_regione","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","n_citta","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_regione','n_regionenascita','$regione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_regione\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Città di residenza",$pag).": <b>$d_citta</b></td>
<td>".mex("cambia in",$pag).": 
".mostra_lista_relutenti("n_citta","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_citta','n_cittanascita','$citta_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_citta\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
if ($priv_vedi_indirizzo == "s") {
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Via",$pag).": <b>$d_via</b></td>
<td>".mex("cambia in",$pag).": 
<select name=\"n_via\">
<option value=\"".mex("Via",$pag)."\" selected>".mex("Via",$pag)."</option>
<option value=\"".mex("Piazza",$pag)."\">".mex("Piazza",$pag)."</option>
<option value=\"".mex("Viale",$pag)."\">".mex("Viale",$pag)."</option>
<option value=\"".mex("Piazzale",$pag)."\">".mex("Piazzale",$pag)."</option>
<option value=\"".mex("Vicolo",$pag)."\">".mex("Vicolo",$pag)."</option>
<option value=\"\">-----</option>
</select>
<input type=\"text\" name=\"n_nomevia\">
<br><label><input type=\"checkbox\" name=\"c_via\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Numero civico",$pag).": <b>$d_numcivico</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_numcivico\" size=\"4\">
<br><label><input type=\"checkbox\" name=\"c_numcivico\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("CAP",$pag).": <b>$d_cap</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_cap\" size=\"6\">
<br><label><input type=\"checkbox\" name=\"c_cap\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
} # fine if ($priv_vedi_indirizzo == "s")
echo "</table>

<table class=\"modres floatleft\" cellspacing=0 cellpadding=6>";
if ($priv_vedi_telefoni == "s") {
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Telefono",$pag).": <b>$d_telefono</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_telefono\">
<br><label><input type=\"checkbox\" name=\"c_telefono\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
$d_email_vedi = str_replace(",",", ",$d_email);
$d_email_vedi = "<b><a href=\"mailto:$d_email\">$d_email_vedi</a></b>";
if (strlen($d_email) > 22) $d_email_vedi = "<small>$d_email_vedi</small>";
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Email",$pag).": $d_email_vedi</td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_email\" size=\"30\">
<br><label><input type=\"checkbox\" name=\"c_email\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
} # fine if ($priv_vedi_telefoni == "s")

$d_nome_lingua = "";
if ($d_lingua_cli) {
if ($d_lingua_cli != "ita" and !@is_dir("./includes/lang/$d_lingua_cli")) $d_lingua_cli = "";
else {
if ($d_lingua_cli == "ita") $d_nome_lingua = "Italiano";
else {
$d_nome_lingua = file("./includes/lang/$d_lingua_cli/l_n");
$d_nome_lingua = ucfirst(togli_acapo($d_nome_lingua[0]));
} # fine else if ($d_lingua_cli == "ita")
} # fine else if ($d_lingua_cli != "ita" and !@is_dir("./includes/lang/$d_lingua_cli"))
} # fine if ($d_lingua_cli)
$opt_lingue = "<option value=\"ita\">Italiano</option>";
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." and $ini_lingua != ".." and $ini_lingua != $d_lingua_cli) {
$nome_lingua = file("./includes/lang/$ini_lingua/l_n");
$nome_lingua = togli_acapo($nome_lingua[0]);
if ($ini_lingua == $lingua[$id_utente]) $opt_lingue = "<option value=\"$ini_lingua\">".ucfirst($nome_lingua)."</option>".$opt_lingue;
else $opt_lingue .= "<option value=\"$ini_lingua\">".ucfirst($nome_lingua)."</option>";
} # fine if ($file != "." and $file != ".." and...
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$opt_lingue = "<option value=\"\">------</option>".$opt_lingue;
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Lingua",$pag).": <b>$d_nome_lingua</b></td>
<td>".mex("cambia in",$pag).": <select name=\"n_lingua_cli\">
$opt_lingue</select>
<br><label><input type=\"checkbox\" name=\"c_lingua_cli\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";

if ($id_utente == 1) {
$tutti_utenti = esegui_query("select * from $tableutenti order by idutenti");
$num_tutti_utenti = numlin_query($tutti_utenti);
if ($num_tutti_utenti > 1) {
unset($option_select_utenti);
for ($num1 = 0 ; $num1 < $num_tutti_utenti ; $num1++) {
$idutenti = risul_query($tutti_utenti,$num1,'idutenti');
if ($idutenti != $id_utente_inserimento) {
$nome_utente_option = risul_query($tutti_utenti,$num1,'nome_utente');
$option_select_utenti .= "<option value=\"$idutenti\">$nome_utente_option</option>";
} # fine if ($idutenti != $id_utente_inserimento)
else $nome_utente_inserimento = risul_query($tutti_utenti,$num1,'nome_utente');
} # fine for $num1
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>
".mex("Inserito da",$pag)." <b>$nome_utente_inserimento</b>
</td><td>
 ".mex("cambia in",$pag).": <select name=\"id_nuovo_utente_inserimento\">
<option value=\"\" selected>----</option>
$option_select_utenti
</select></td></tr>";
} # fine if ($num_tutti_utenti > 1)
} # fine if ($id_utente == 1)

echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Documento",$pag).": <b>";
if ($d_tipodoc) echo "$d_tipodoc ";
echo "$d_documento</b></td>
<td>".mex("cambia in",$pag).": ".mostra_lista_relutenti("n_tipodoc","",$id_utente,"nome_documentoid","iddocumentiid","iddocumentoid",$tabledocumentiid,$tablerelutenti,"","","SI");;
echo "<input type=\"text\" name=\"n_documento\"><br>
<label><input type=\"checkbox\" name=\"c_documento\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Nazione di rilascio",$pag).": <b>$d_nazionedoc</b></td>
<td>".mex("cambia in",$pag).":
 ".mostra_lista_relutenti("n_nazionedoc","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","n_regionedoc")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_nazionedoc','n_nazionalita','$nazione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_nazionedoc\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Reg./Prov. di rilascio",$pag).": <b>$d_regionedoc</b></td>
<td>".mex("cambia in",$pag).":
 ".mostra_lista_relutenti("n_regionedoc","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","n_cittadoc","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_regionedoc','n_regione','$regione_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_regionedoc\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Città di rilascio",$pag).": <b>$d_cittadoc</b></td>
<td>".mex("cambia in",$pag).":
 ".mostra_lista_relutenti("n_cittadoc","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('n_cittadoc','n_citta','$citta_def')\" value=\"#\">
<br><label><input type=\"checkbox\" name=\"c_cittadoc\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
if (date("Ymd",(time() + (C_DIFF_ORE * 3600))) <= str_replace("-","",$d_scadenzadoc)) echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Scadenza documento",$pag).": <b>$d_scadenzadoc_f</b></td>";
else echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Scadenza documento",$pag).": <div style=\"display: inline; color: red;\"><b>$d_scadenzadoc_f</b></div></td>";
echo "<td>".mex("cambia in",$pag).": ";
$sel_gscaddoc = "<select name=\"n_giornoscaddoc\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gscaddoc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_gscaddoc .= "</select>";
$sel_mscaddoc = "<select name=\"n_mesescaddoc\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mscaddoc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_mscaddoc .= "</select>";
if ($stile_data == "usa") echo "$sel_mscaddoc/$sel_gscaddoc";
else echo "$sel_gscaddoc/$sel_mscaddoc";
echo "/<select name=\"n_annoscaddoc\">";
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
echo "</select>
<br><label><input type=\"checkbox\" name=\"c_scadenzadoc\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";

if ($priv_vedi_telefoni == "s") {
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Fax",$pag).": <b>$d_fax</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_fax\">
<br><label><input type=\"checkbox\" name=\"c_fax\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("2° telefono",$pag).": <b>$d_telefono2</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_telefono2\">
<br><label><input type=\"checkbox\" name=\"c_telefono2\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("3° telefono",$pag).": <b>$d_telefono3</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_telefono3\">
<br><label><input type=\"checkbox\" name=\"c_telefono3\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>";
} # fine if ($priv_vedi_telefoni == "s")
echo "<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Codice fiscale",$pag).": <b>$d_cod_fiscale</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_cod_fiscale\">
<br><label><input type=\"checkbox\" name=\"c_cod_fiscale\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
<tr style=\"background-color: ".rowbgcolor().";\"><td>".mex("Partita iva",$pag).": <b>$d_partita_iva</b></td>
<td>".mex("cambia in",$pag).": 
<input type=\"text\" name=\"n_partita_iva\">
<br><label><input type=\"checkbox\" name=\"c_partita_iva\" value=\"SI\">".mex("cancella",$pag)."</label></td></tr>
</table>
<div style=\"clear: both; padding: 6px 0 0 0;\">";

if ($d_num_campi_pers or $num_campi_pers) {
echo "<table cellspacing=0 cellpadding=0><tr>";
$pari = 0;
for ($num1 = 0 ; $num1 < $d_num_campi_pers ; $num1++) {
if ($d_campi_pers[$num1]['tipo'] == "txt") echo "<td><b>".$d_campi_pers[$num1]['nome']."</b>: <input type=\"text\" name=\"n_campo_pers$num1\" value=\"".$d_campi_pers[$num1]['val']."\"></td>";
else echo "<td><label><input type=\"checkbox\" name=\"n_campo_pers$num1\" value=\"1\" checked><b>".$d_campi_pers[$num1]['nome']."</b></label></td>";
if ($pari) {
$pari = 0;
echo "</tr><tr>";
} # fine if ($pari)
else {
$pari = 1;
echo "<td style=\"width: 50px;\">&nbsp;</td>";
} # fine else if ($pari)
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
$opt = explode("<",$campi_pers[$num1]);
if (!$d_campi_pers['esist'][$opt[0]]) {
if ($opt[1] == "txt") echo "<td>".$opt[0].": <input type=\"text\" name=\"campo_pers$num1\" value=\"".${"campo_pers".$num1}."\"></td>";
else echo "<td><label><input type=\"checkbox\" name=\"campo_pers$num1\" value=\"1\">".$opt[0]."</label></td>";
if ($pari) {
$pari = 0;
echo "</tr><tr>";
} # fine if ($pari)
else {
$pari = 1;
echo "<td style=\"width: 50px;\">&nbsp;</td>";
} # fine else if ($pari)
} # fine if (!$d_campi_pers['esist'][$opt[0]])
} # fine for $num1
echo "</tr></table><div style=\"height: 6px;\"></div>";
} # fine if ($d_num_campi_pers or $num_campi_pers)

echo "".mex("Commento",$pag).":<div>
<textarea name=\"n_commento\" rows=3 cols=45 style=\"white-space: pre; overflow: auto;\">$d_commento</textarea><br>";


echo "<div style=\"text-align: center;\">

<input type=\"hidden\" name=\"d_cognome\" value=\"$d_cognome\">
<input type=\"hidden\" name=\"d_nome\" value=\"$d_nome\">
<input type=\"hidden\" name=\"d_soprannome\" value=\"$d_soprannome\">
<input type=\"hidden\" name=\"d_sesso\" value=\"$d_sesso\">
<input type=\"hidden\" name=\"d_titolo_cli\" value=\"$d_titolo_cli\">
<input type=\"hidden\" name=\"d_datanascita\" value=\"$d_datanascita\">
<input type=\"hidden\" name=\"d_documento\" value=\"$d_documento\">
<input type=\"hidden\" name=\"d_scadenzadoc\" value=\"$d_scadenzadoc\">
<input type=\"hidden\" name=\"d_tipodoc\" value=\"$d_tipodoc\">
<input type=\"hidden\" name=\"d_nazionedoc\" value=\"$d_nazionedoc\">
<input type=\"hidden\" name=\"d_regionedoc\" value=\"$d_regionedoc\">
<input type=\"hidden\" name=\"d_cittadoc\" value=\"$d_cittadoc\">
<input type=\"hidden\" name=\"d_cittanascita\" value=\"$d_cittanascita\">
<input type=\"hidden\" name=\"d_regionenascita\" value=\"$d_regionenascita\">
<input type=\"hidden\" name=\"d_nazionenascita\" value=\"$d_nazionenascita\">
<input type=\"hidden\" name=\"d_nazionalita\" value=\"$d_nazionalita\">
<input type=\"hidden\" name=\"d_nazione\" value=\"$d_nazione\">
<input type=\"hidden\" name=\"d_regione\" value=\"$d_regione\">
<input type=\"hidden\" name=\"d_citta\" value=\"$d_citta\">
<input type=\"hidden\" name=\"d_via\" value=\"$d_via\">
<input type=\"hidden\" name=\"d_numcivico\" value=\"$d_numcivico\">
<input type=\"hidden\" name=\"d_telefono\" value=\"$d_telefono\">
<input type=\"hidden\" name=\"d_telefono2\" value=\"$d_telefono2\">
<input type=\"hidden\" name=\"d_telefono3\" value=\"$d_telefono3\">
<input type=\"hidden\" name=\"d_cod_fiscale\" value=\"$d_cod_fiscale\">
<input type=\"hidden\" name=\"d_partita_iva\" value=\"$d_partita_iva\">
<input type=\"hidden\" name=\"d_fax\" value=\"$d_fax\">
<input type=\"hidden\" name=\"d_cap\" value=\"$d_cap\">
<input type=\"hidden\" name=\"d_email\" value=\"$d_email\">
<input type=\"hidden\" name=\"d_nome_lingua\" value=\"$d_nome_lingua\">
<input type=\"hidden\" name=\"d_commento\" value=\"$d_commento\">
<input type=\"hidden\" name=\"d_num_campi_pers\" value=\"$d_num_campi_pers\">";
for ($num1 = 0 ; $num1 < $d_num_campi_pers ; $num1++) {
echo "<input type=\"hidden\" name=\"d_campo_pers$num1\" value=\"".$d_campi_pers[$num1]['val']."\">
<input type=\"hidden\" name=\"d_campo_pers_nome$num1\" value=\"".$d_campi_pers[$num1]['nome']."\">";
} # fine for $num1


echo "<input type=\"hidden\" name=\"modifica_cliente\" value=\"1\">
<button class=\"mcli\" id=\"modi\" type=\"submit\"><div>".mex("Modifica i dati del cliente",$pag)."</div></button>
</div></div></form>";

if ($origine_vecchia) $origine = $origine_vecchia;
if ($tipo_tabella) $origine = "visualizza_tabelle.php";
echo "<div style=\"text-align: center;\"><hr style=\"width: 95%\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"visualizza_contratto.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\"><input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"origine_vecchia\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"origine\" value=\"modifica_cliente.php?tipo_tabella=$tipo_tabella&amp;idclienti=$idclienti\">

<input type=\"hidden\" name=\"cognome_1\" value=\"$d_cognome\">
<input type=\"hidden\" name=\"nome_1\" value=\"$d_nome\">
<input type=\"hidden\" name=\"soprannome_1\" value=\"$d_soprannome\">
<input type=\"hidden\" name=\"sesso_1\" value=\"$d_sesso\">
<input type=\"hidden\" name=\"titolo_1\" value=\"$d_titolo_cli\">
<input type=\"hidden\" name=\"data_nascita_1\" value=\"$d_datanascita\">
<input type=\"hidden\" name=\"documento_1\" value=\"$d_documento\">
<input type=\"hidden\" name=\"tipo_documento_1\" value=\"$d_tipodoc\">
<input type=\"hidden\" name=\"nazione_documento_1\" value=\"$d_nazionedoc\">
<input type=\"hidden\" name=\"regione_documento_1\" value=\"$d_regionedoc\">
<input type=\"hidden\" name=\"citta_documento_1\" value=\"$d_cittadoc\">
<input type=\"hidden\" name=\"scadenza_documento_1\" value=\"$d_scadenzadoc\">
<input type=\"hidden\" name=\"citta_nascita_1\" value=\"$d_cittanascita\">
<input type=\"hidden\" name=\"regione_nascita_1\" value=\"$d_regionenascita\">
<input type=\"hidden\" name=\"nazione_nascita_1\" value=\"$d_nazionenascita\">
<input type=\"hidden\" name=\"cittadinanza_1\" value=\"$d_nazionalita\">
<input type=\"hidden\" name=\"nazione_1\" value=\"$d_nazione\">
<input type=\"hidden\" name=\"regione_1\" value=\"$d_regione\">
<input type=\"hidden\" name=\"citta_1\" value=\"$d_citta\">
<input type=\"hidden\" name=\"via_1\" value=\"$d_via\">
<input type=\"hidden\" name=\"numcivico_1\" value=\"$d_numcivico\">
<input type=\"hidden\" name=\"telefono_1\" value=\"$d_telefono\">
<input type=\"hidden\" name=\"telefono2_1\" value=\"$d_telefono2\">
<input type=\"hidden\" name=\"telefono3_1\" value=\"$d_telefono3\">
<input type=\"hidden\" name=\"codice_fiscale_1\" value=\"$d_cod_fiscale\">
<input type=\"hidden\" name=\"partita_iva_1\" value=\"$d_partita_iva\">
<input type=\"hidden\" name=\"fax_1\" value=\"$d_fax\">
<input type=\"hidden\" name=\"cap_1\" value=\"$d_cap\">
<input type=\"hidden\" name=\"email_1\" value=\"$d_email\">
<input type=\"hidden\" name=\"codice_lingua_1\" value=\"$d_lingua_cli\">";
for ($num1 = 0 ; $num1 < $d_num_campi_pers ; $num1++) {
echo "<input type=\"hidden\" name=\"campo_personalizzato_".$d_campi_pers[$num1]['nome']."_1\" value=\"".$d_campi_pers[$num1]['val']."\">";
} # fine for $num1

echo "".ucfirst(mex("documento di tipo",$pag))."
 <select id=\"lcon\" name=\"numero_contratto\">";
$nomi_contratti = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '$id_utente'");
$nomi_contratti = risul_query($nomi_contratti,0,'valpersonalizza');
$nomi_contratti = explode("#@&",$nomi_contratti);
$num_nomi_contratti = count($nomi_contratti);
for ($num1 = 0 ; $num1 < $num_nomi_contratti ; $num1++) {
$dati_nome_contratto = explode("#?&",$nomi_contratti[$num1]);
$nome_contratto[$dati_nome_contratto[0]] = $dati_nome_contratto[1];
} # fine for $num1
$max_contr = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'contr%'");
$max_contr = risul_query($max_contr,0,0);
for ($num_contratto = 1 ; $num_contratto <= $max_contr ; $num_contratto++) {
if ($attiva_contratti_consentiti == "n" or $contratti_consentiti_vett[$num_contratto] == "SI") {
if ($nome_contratto[$num_contratto]) $num_contratto_vedi = $nome_contratto[$num_contratto];
else $num_contratto_vedi = $num_contratto;
echo "<option value=\"$num_contratto\">$num_contratto_vedi</option>";
} # fine if ($attiva_contratti_consentiti == "n" or...
} # fine for $num1
echo "</select>
 <button class=\"vdoc\" id=\"tcon\" type=\"submit\"><div>".ucfirst(mex("visualizza",$pag))."</div></button>
<input type=\"hidden\" id=\"hcon\" value=\"".ucfirst(mex("visualizza il documento",$pag))."\"><br>
</div></form><hr style=\"width: 95%\">";



if ($priv_mod_prenota_iniziate != "s") $id_periodo_corrente = calcola_id_periodo_corrente($anno);
$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);
$prenota_cliente_esistente = "NO";
for ($num1 = 0 ; $num1 < $num_anni ; $num1 = $num1 + 1) {
$anno_mostra = risul_query($anni,$num1,'idanni');
$tablepren_m = $PHPR_TAB_PRE."prenota".$anno_mostra;
$tablerclipren_m = $PHPR_TAB_PRE."rclientiprenota".$anno_mostra;
$tablecostiprenota_mostra = $PHPR_TAB_PRE."costiprenota".$anno_mostra;
$tableperiodi_mostra = $PHPR_TAB_PRE."periodi".$anno_mostra;
$tablesoldi_mostra = $PHPR_TAB_PRE."soldi".$anno_mostra;
$prenotazioni = esegui_query("select distinct $tablepren_m.idprenota,$tablepren_m.idclienti,$tablepren_m.utente_inserimento,$tablepren_m.idappartamenti,$tablepren_m.commento,$tablepren_m.iddatainizio,$tablepren_m.iddatafine,$tablepren_m.num_persone,$tablepren_m.caparra,$tablepren_m.pagato,$tablepren_m.tariffa_tot,$tablepren_m.datainserimento from $tablepren_m left outer join $tablerclipren_m on $tablepren_m.idprenota = $tablerclipren_m.idprenota where $tablepren_m.idclienti = '$idclienti' or $tablerclipren_m.idclienti = '$idclienti' ");
$soldi = esegui_query("select * from $tablesoldi_mostra where motivazione $LIKE '$idclienti".";%'");
if (numlin_query($soldi) != 0) $prenota_cliente_esistente = "SI";
$num_prenotazioni = numlin_query($prenotazioni);
if ($num_prenotazioni != 0) {
$prenota_cliente_esistente = "SI";
$fr_Appartamento = mex("Appartamento",'unit.php');
if (strlen($fr_Appartamento) > 4) $fr_Appartamento = substr($fr_Appartamento,0,3).".";
if ($mobile_device) {
$class_opt = " class=\"opt\"";
$class_opt2 = " class=\"opt2\"";
} # fine if ($mobile_device)
else {
$class_opt = "";
$class_opt2 = "";
} # fine else if ($mobile_device)
echo "<br><small id=\"h_clre\"><b>".mex("Prenotazioni del cliente",$pag)." $d_cognome ".mex("nel",$pag)." $anno_mostra</b></small>
<table class=\"t1\" style=\"background-color: $t1color; margin-left: auto; margin-right: auto;\" width=3 border=\"$t1border\" cellspacing=\"$t1cellspacing\" cellpadding=\"$t1cellpadding\">
<tr><td>N°</td>
<td>".mex("Cognome_del_cliente",$pag)."</td>
<td>".mex("Data_iniziale",$pag);
if (!$mobile_device) echo "</td><td>";
else echo "&nbsp;/&nbsp;<span class=\"smlscr\"> </span>";
echo mex("Data_finale",$pag)."</td>
<td><small>".mex("Tariffa_completa",$pag)."</small></td>
<td$class_opt>".mex("Caparra",$pag)."</td>
<td>".mex("Pagato",$pag)."</td>
<td><small><small>$fr_Appartamento</small></small></td>
<td><small><small>".mex("Pers",$pag).".</small></small></td>
<td$class_opt2>".mex("Commento",$pag)."</td></tr>";

$data_inizio_assoluta = esegui_query("select datainizio from $tableperiodi_mostra where idperiodi = 1");
$data_inizio_assoluta = risul_query($data_inizio_assoluta,0,'datainizio');
$costo_tot_TOT = 0;
$caparra_TOT = 0;
$pagato_TOT = 0;

for ($num2 = 0 ; $num2 < $num_prenotazioni ; $num2 = $num2 + 1) {
$utente_inserimento_prenota = risul_query($prenotazioni,$num2,'utente_inserimento',$tablepren_m);
if ($priv_vedi_tab_prenotazioni == "s" or ($priv_vedi_tab_prenotazioni == "p" and $utente_inserimento_prenota == $id_utente) or ($priv_vedi_tab_prenotazioni == "g" and $utenti_gruppi[$utente_inserimento_prenota])) {

$numero = risul_query($prenotazioni,$num2,'idprenota',$tablepren_m);
$appartamento = risul_query($prenotazioni,$num2,'idappartamenti',$tablepren_m);
$commento = risul_query($prenotazioni,$num2,'commento',$tablepren_m);
if (strstr($commento,">")) {
$commento = explode(">",$commento);
$commento = $commento[0];
} # fine if (strstr($commento,">"))
if (!$commento) { $commento = "&nbsp;"; }

$id_data_inizio = risul_query($prenotazioni,$num2,'iddatainizio',$tablepren_m);
if ($id_data_inizio == 0) { $data_inizio = "<".$data_inizio_assoluta; }
else {
$data_inizio = esegui_query("select * from $tableperiodi_mostra where idperiodi = $id_data_inizio");
$data_inizio = risul_query($data_inizio,0,'datainizio');
$data_inizio_f = formatta_data($data_inizio,$stile_data);
} # fine else if ($id_data_inizio == 0)
$id_data_fine = risul_query($prenotazioni,$num2,'iddatafine',$tablepren_m);
$data_fine = esegui_query("select * from $tableperiodi_mostra where idperiodi = $id_data_fine");
$data_fine = risul_query($data_fine,0,'datafine');
$data_fine_f = formatta_data($data_fine,$stile_data);
$mese = explode("-",$data_inizio);
$mese = $mese[1];

$num_persone = risul_query($prenotazioni,$num2,'num_persone',$tablepren_m);
if (!$num_persone or $num_persone == 0) { $num_persone = "?"; }
$n_letti_agg = 0;
$dati_cap = dati_costi_agg_prenota($tablecostiprenota_mostra,$numero);
unset($num_letti_agg);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) aggiorna_letti_agg_in_periodi($dati_cap,$numca,$num_letti_agg,$d_id_data_inizio,$d_id_data_fine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],"","");
$n_letti_agg = $num_letti_agg['max'];

$caparra = risul_query($prenotazioni,$num2,'caparra',$tablepren_m);
if (!$caparra) { $caparra = 0; }
$caparra_p = punti_in_num($caparra,$stile_soldi);
$pagato = risul_query($prenotazioni,$num2,'pagato',$tablepren_m);
if (!$pagato) { $pagato = 0; }
$pagato_p = punti_in_num($pagato,$stile_soldi);
$costo_tot = risul_query($prenotazioni,$num2,'tariffa_tot',$tablepren_m);
if (!$costo_tot) { $costo_tot = 0; }
$costo_tot_p = punti_in_num($costo_tot,$stile_soldi);
$colore = "";
if ($pagato < $caparra) { $colore = "#CC0000"; }
else { if ($pagato < $costo_tot) { $colore = "#FFCC00"; } }

$link_modifica = "SI";
if ($priv_mod_prenotazioni == "n") $link_modifica = "NO";
if ($priv_mod_prenotazioni == "p" and $utente_inserimento_prenota != $id_utente) $link_modifica = "NO";
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento_prenota]) $link_modifica = "NO";
if ($priv_mod_prenota_iniziate != "s" and $id_periodo_corrente >= $id_data_inizio) $link_modifica = "NO";
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = risul_query($prenotazioni,$num2,'datainserimento',$tablepren_m);
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $link_modifica = "NO";
} # fine if ($priv_mod_prenota_ore != "000")

$id_cli_pren = risul_query($prenotazioni,$num2,'idclienti',$tablepren_m);
if ($id_cli_pren != $idclienti) {
$cognome_pren = esegui_query("select cognome from $tableclienti where idclienti = '$id_cli_pren' ");
$cognome_pren = risul_query($cognome_pren,0,'cognome');
} # fine if ($id_cli_pren != $idclienti)
else $cognome_pren = $d_cognome;

if ($anno == $anno_mostra and $link_modifica == "SI") {
echo "<tr><td><a href=\"modifica_prenota.php?id_prenota=$numero&amp;anno=$anno&amp;id_sessione=$id_sessione&amp;origine=visualizza_tabelle.php\">$numero</a></td>";
} # fine if ($anno = $anno_mostra)
else { echo "<tr><td>$numero</td>"; }
echo "<td>$cognome_pren</td>
<td>$data_inizio_f";
if (!$mobile_device) echo "</td><td>";
else echo "&nbsp;/&nbsp;<span class=\"smlscr\"> </span>";
echo "$data_fine_f</td>
<td>$costo_tot_p</td>
<td$class_opt>$caparra_p</td>
<td";
if ($colore) echo " style=\"background-color: $colore;\"";
echo ">$pagato_p</td>
<td>$appartamento</td>
<td>$num_persone";
if ($n_letti_agg != 0) echo "+$n_letti_agg";
echo "</td>
<td$class_opt2><small><small>$commento</small></small></td></tr>";

$costo_tot_TOT = $costo_tot_TOT + $costo_tot;
$caparra_TOT = $caparra_TOT + $caparra;
$pagato_TOT = $pagato_TOT + $pagato;
} # fine if ($priv_vedi_tab_prenotazioni == "s" or...
} # fine for $num2

if ($num_prenotazioni > 1) {
$costo_tot_TOT_p = punti_in_num($costo_tot_TOT,$stile_soldi);
$caparra_TOT_p = punti_in_num($caparra_TOT,$stile_soldi);
$pagato_TOT_p = punti_in_num($pagato_TOT,$stile_soldi);
if (!$mobile_device) $totcol = 4;
else $totcol = 3;
echo "<tr><td colspan=\"$totcol\"><b><i>".mex("TOTALE",$pag)."</i></b></td>
<td>$costo_tot_TOT_p</td>
<td$class_opt>$caparra_TOT_p</td>
<td>$pagato_TOT_p</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td$class_opt2>&nbsp;</td></tr>";
} # fine if ($num_prenotazioni > 1)

echo "</table><br>";
} # fine if ($num_prenotazioni != 0)
} # fine for $num1

if ($prenota_cliente_esistente != "SI") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"cancella_cliente\" value=\"SI\">
<button class=\"ccli\" id=\"canc\" type=\"submit\"><div>".mex("Cancella il cliente",$pag)." $idclienti</div></button>
</div></form>";
} # fine if ($prenota_cliente_esistente != "SI")

echo "<hr style=\"width: 95%\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"".htmlspecialchars($origine)."\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<button class=\"gobk\" id=\"indi\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form></div>
<table><tr><td style=\"height: 20px;\"></td></tr></table>";


} # fine if ($mostra_form_modifica_cliente != "NO")
} # fine else if ($cancella_cliente == "SI")


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and...
} # fine if ($id_utente)



?>