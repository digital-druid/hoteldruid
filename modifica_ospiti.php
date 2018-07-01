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

$pag = "modifica_ospiti.php";
$titolo = "HotelDruid: Modifica Ospiti Prenotazione";

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
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tableparentele = $PHPR_TAB_PRE."parentele";
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";


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
$priv_vedi_tab = risul_query($privilegi_annuali_utente,0,'priv_vedi_tab');
$priv_vedi_tab_prenotazioni = substr($priv_vedi_tab,1,1);
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
$inserimento_nuovi_clienti = "SI";
$modifica_clienti = "SI";
$vedi_clienti = "SI";
$attiva_prefisso_clienti = "n";
$priv_vedi_tab_prenotazioni = "s";
$attiva_contratti_consentiti = "n";
$priv_mod_prenotazioni = "s";
$priv_mod_prenota_iniziate = "s";
$priv_mod_prenota_ore = "000";
} # fine else if ($id_utente != 1)

if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0) {
$num_clienti_esistenti = esegui_query("select idclienti from $tableclienti");
$num_clienti_esistenti = numlin_query($num_clienti_esistenti);
if ($num_clienti_esistenti >= C_MASSIMO_NUM_CLIENTI) $inserimento_nuovi_clienti = "NO";
} # fine if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0)


$id_prenota = aggslashdb($id_prenota);
$id_prenota_int = $id_prenota;
unset($id_prenota_idpr);
$id_prenota_idpr[0] = $id_prenota;
$num_id_prenota = 1;
if (str_replace(",","",$id_prenota) != $id_prenota) {
$id_prenota_idpr = explode(",",$id_prenota);
$num_id_prenota = count($id_prenota_idpr);
} # fine if (str_replace(",","",$id_prenota) != $id_prenota)

if ($priv_mod_prenota_iniziate != "s") $id_periodo_corrente = calcola_id_periodo_corrente($anno);
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g") {
$utente_inserimento = esegui_query("select utente_inserimento from $tableprenota where idprenota = '$id_prenota'");
if (numlin_query($utente_inserimento) == 1) $utente_inserimento = risul_query($utente_inserimento,0,'utente_inserimento');
else $utente_inserimento = "NO";
if ($priv_mod_prenotazioni == "p" and $utente_inserimento != $id_utente) $priv_mod_prenotazioni = "n";
if ($priv_mod_prenotazioni == "g" and !$utenti_gruppi[$utente_inserimento]) $priv_mod_prenotazioni = "n";
} # fine if ($priv_mod_prenotazioni == "p" or $priv_mod_prenotazioni == "g")
if ($priv_mod_prenota_iniziate != "s") {
$id_inizio_prenota = esegui_query("select iddatainizio from $tableprenota where idprenota = '$id_prenota'");
if (numlin_query($id_inizio_prenota) == 1) $id_inizio_prenota = risul_query($id_inizio_prenota,0,'iddatainizio');
else $id_inizio_prenota = -2;
if ($id_periodo_corrente >= $id_inizio_prenota) $priv_mod_prenotazioni = "n";
} # fine if ($priv_mod_prenota_iniziate != "s")
if ($priv_mod_prenota_ore != "000") {
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
$data_ins = esegui_query("select datainserimento from $tableprenota where idprenota = '$id_prenota'");
if (numlin_query($data_ins) == 1) $data_ins = risul_query($data_ins,0,'datainserimento');
else $data_ins = "1971-01-01 00:00:00";
$limite = date("YmdHis",mktime((substr($data_ins,11,2) + $priv_mod_prenota_ore),substr($data_ins,14,2),substr($data_ins,17,2),substr($data_ins,5,2),substr($data_ins,8,2),substr($data_ins,0,4)));
if ($adesso > $limite) $priv_mod_prenotazioni = "n";
} # fine if ($priv_mod_prenota_ore != "000")
} # fine for $num_idpr
if ($anno_utente_attivato == "SI" and $priv_mod_prenotazioni != "n") {

unset($idclienti_idpr);
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$idclienti = "";
$dati_prenota[$id_prenota] = esegui_query("select * from $tableprenota where  idprenota = '$id_prenota' ");
if (numlin_query($dati_prenota[$id_prenota]) == 1) $idclienti = risul_query($dati_prenota[$id_prenota],0,'idclienti');
if (($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI") and $idclienti) {
$cliente_proprio = esegui_query("select idclienti from $tableclienti where idclienti = '$idclienti' and utente_inserimento = '$id_utente'");
if (numlin_query($cliente_proprio) == 0) $modifica_clienti = "NO";
} # fine if ($modifica_clienti == "PROPRI" or $vedi_clienti == "PROPRI" and...
elseif (($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI") and $idclienti) {
$cliente_proprio = esegui_query("select utente_inserimento from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($cliente_proprio) == 0) $utente_inserimento = "0";
else $utente_inserimento = risul_query($cliente_proprio,0,"utente_inserimento");
if (!$utenti_gruppi[$utente_inserimento]) $modifica_clienti = "NO";
} # fine elseif ($modifica_clienti == "GRUPPI" or $vedi_clienti == "GRUPPI" and...
if (!$idclienti or controlla_num_pos($idclienti) != "SI") $vedi_clienti = "NO";
$idclienti_idpr[$id_prenota] = $idclienti;
} # fine for $num_idpr
if ($modifica_clienti != "NO" and $vedi_clienti != "NO") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


$stile_soldi = stile_soldi();
$stile_data = stile_data();

unset($num_persone_tot_idpr);
unset($app_letto_idpr);
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
unset($num_letti_agg);
$d_id_data_inizio = risul_query($dati_prenota[$id_prenota],0,'iddatainizio');
$d_id_data_fine = risul_query($dati_prenota[$id_prenota],0,'iddatafine');
$d_num_persone = risul_query($dati_prenota[$id_prenota],0,'num_persone');
$dati_cap = dati_costi_agg_prenota($tablecostiprenota,$id_prenota);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) aggiorna_letti_agg_in_periodi($dati_cap,$numca,$num_letti_agg,$d_id_data_inizio,$d_id_data_fine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],"","");
if ($d_num_persone) $num_persone_tot = $d_num_persone;
else $num_persone_tot = 0;
if ($num_letti_agg['max'] != 0) $num_persone_tot = $num_persone_tot + $num_letti_agg['max'];
$num_persone_tot_idpr[$num_idpr] = $num_persone_tot;
$d_idapp = risul_query($dati_prenota[$id_prenota],0,'idappartamenti');
$d_idapp = esegui_query("select idappartamenti from $tableappartamenti where idappartamenti = '".aggslashdb($d_idapp)."' and letto = '1' ");
if (numlin_query($d_idapp)) $app_letto_idpr[$num_idpr] = 1;
else $app_letto_idpr[$num_idpr] = 0;
} # fine for $num_idpr




if ($modifica == "SI") {
$form_modifica_ospiti = "NO";


$continua = 1;
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$num_ospiti_inviati = ${"num_ospiti_inviati".$id_prenota};
if (!strcmp($num_ospiti_inviati,"") or controlla_num_pos($num_ospiti_inviati) == "NO") $continua = 0;
} # fine for $num_idpr

if ($continua) {
$tabelle_lock = array($tablerclientiprenota,$tableclienti);
$altre_tab_lock = array($tableprenota,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$idclienti = $idclienti_idpr[$id_prenota];
$num_persone_tot = $num_persone_tot_idpr[$num_idpr];
$num_ospiti_inviati = ${"num_ospiti_inviati".$id_prenota};
$principale = ${"principale".$id_prenota};

if ($num_ospiti_inviati > $num_persone_tot + 30) $num_ospiti_inviati = $num_persone_tot + 30;

$utente_cliente = esegui_query("select utente_inserimento,idclienti_compagni from $tableclienti where idclienti = '$idclienti' ");
$compagni_cliente = risul_query($utente_cliente,0,'idclienti_compagni');
$utente_cliente = risul_query($utente_cliente,0,'utente_inserimento');
if ($id_utente == 1) $id_utente_ins = $utente_cliente;
else $id_utente_ins = $id_utente;

unset($osp_presente);
unset($lista_ospiti);
unset($presente_id);
$idclienti_compagni = "";
$num_ospiti = 0;
if (${"cliente_ospite".$id_prenota."_".$idclienti} == "SI") {
$num_ospiti++;
$lista_ospiti[$num_ospiti] = $idclienti;
$lista_ospiti['lista'] .= $idclienti.",";
$presente_id[$idclienti] = "SI";
$idclienti_compagni .= substr($compagni_cliente,1);
$principale = $idclienti;
} # fine if (${"cliente_ospite".$id_prenota."_".$idclienti} == "SI")
else if (${"id_osp_num".$id_prenota."_1"} == $idclienti) esegui_query("delete from $tablerclientiprenota where idprenota = '$id_prenota' and idclienti = '$idclienti' ");

$ospiti = esegui_query("select idclienti,num_ordine,parentela from $tablerclientiprenota where idprenota = '$id_prenota' order by num_ordine ");
$d_num_ospiti = numlin_query($ospiti);
for ($num1 = 0 ; $num1 < $d_num_ospiti ; $num1++) $osp_presente[risul_query($ospiti,$num1,'idclienti')] = ($num1 + 1);

if ($principale != $idclienti) {
if ($osp_presente[$principale] and ${"ospite".$id_prenota."_".$principale} == "SI") {
if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0) $mess_letto = 1;
else {
$num_ospiti++;
$lista_ospiti[$num_ospiti] = $principale;
$lista_ospiti['lista'] .= $principale.",";
$presente_id[$principale] = "SI";
} # fine else if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0)
} # fine if ($osp_presente[$principale] and ${"ospite".$id_prenota."_".$principale} == "SI")
else $principale = "";
} # fine if ($principale != $idclienti)

unset($n_parentela);
unset($lista_parentela_cambiata);
for ($num1 = 1 ; $num1 <= $num_ospiti_inviati ; $num1++) {
$id_clienti_osp = aggslashdb(${"id_osp_num".$id_prenota."_".$num1});
if ($id_clienti_osp and $id_clienti_osp != $principale) {
if ($osp_presente[$id_clienti_osp]) {
$dati_ospite = esegui_query("select idclienti_compagni from $tableclienti where idclienti = '$id_clienti_osp' ");
$idclienti_compagni .= substr(risul_query($dati_ospite,0,'idclienti_compagni'),1);
$canc_ospite = 0;
if (${"ospite".$id_prenota."_".$id_clienti_osp} == "SI") {
if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0) {
$mess_letto = 1;
$canc_ospite = 1;
} # fine if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0)
else {
$num_ospiti++;
$lista_ospiti[$num_ospiti] = $id_clienti_osp;
$lista_ospiti['lista'] .= $id_clienti_osp.",";
$presente_id[$id_clienti_osp] = "SI";
$parentela = ${"parentela_".$id_prenota."_".$id_clienti_osp};
if (@get_magic_quotes_gpc()) $parentela = stripslashes($parentela);
$parentela = htmlspecialchars($parentela);
$d_parentela = risul_query($ospiti,($osp_presente[$id_clienti_osp] - 1),'parentela');
if ($parentela != $d_parentela) {
esegui_query("update $tablerclientiprenota set parentela = '".aggslashdb($parentela)."' where idprenota = '$id_prenota' and idclienti = '$id_clienti_osp' ");
$lista_parentela_cambiata .= " and idclienti != '$id_clienti_osp'";
} # fine if ($parentela != $d_parentela)
} # fine else if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0)
} # fine if (${"ospite".$id_prenota."_".$id_clienti_osp} == "SI")
else $canc_ospite = 1;
if ($canc_ospite) esegui_query("delete from $tablerclientiprenota where idprenota = '$id_prenota' and idclienti = '$id_clienti_osp' ");
} # fine if ($osp_presente[$id_clienti_osp])
} # fine if ($id_clienti_osp and...
if (!$id_clienti_osp and ${"cognome".$id_prenota."_".$num1} and $inserimento_nuovi_clienti == "SI") {
if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0) $mess_letto = 1;
else {
$cognome_aux = ${"cognome".$id_prenota."_".$num1};
$nome_aux = ${"nome".$id_prenota."_".$num1};
$titolo_cli_aux = ${"titolo_cli".$id_prenota."_".$num1};
$sesso_aux = ${"sesso".$id_prenota."_".$num1};
$mesenascita_aux = ${"mesenascita".$id_prenota."_".$num1};
$giornonascita_aux = ${"giornonascita".$id_prenota."_".$num1};
$annonascita_aux = ${"annonascita".$id_prenota."_".$num1};
$nazionenascita_aux = ${"nazionenascita".$id_prenota."_".$num1};
$cittanascita_aux = ${"cittanascita".$id_prenota."_".$num1};
$regionenascita_aux = ${"regionenascita".$id_prenota."_".$num1};
$documento_aux = ${"documento".$id_prenota."_".$num1};
$tipodoc_aux = ${"tipodoc".$id_prenota."_".$num1};
$mesescaddoc_aux = ${"mesescaddoc".$id_prenota."_".$num1};
$giornoscaddoc_aux = ${"giornoscaddoc".$id_prenota."_".$num1};
$annoscaddoc_aux = ${"annoscaddoc".$id_prenota."_".$num1};
$cittadoc_aux = ${"cittadoc".$id_prenota."_".$num1};
$regionedoc_aux = ${"regionedoc".$id_prenota."_".$num1};
$nazionedoc_aux = ${"nazionedoc".$id_prenota."_".$num1};
$nazionalita_aux = ${"nazionalita".$id_prenota."_".$num1};
$lingua_cli_aux = ${"lingua_cli".$id_prenota."_".$num1};
$nazione_aux = ${"nazione".$id_prenota."_".$num1};
$citta_aux = ${"citta".$id_prenota."_".$num1};
$regione_aux = ${"regione".$id_prenota."_".$num1};
$via_aux = ${"via".$id_prenota."_".$num1};
$nomevia_aux = ${"nomevia".$id_prenota."_".$num1};
$numcivico_aux = ${"numcivico".$id_prenota."_".$num1};
$cap_aux = ${"cap".$id_prenota."_".$num1};
$telefono__aux = ${"telefono_".$id_prenota."_".$num1};
$telefono2__aux = ${"telefono2_".$id_prenota."_".$num1};
$telefono3__aux = ${"telefono3_".$id_prenota."_".$num1};
$fax_aux = ${"fax".$id_prenota."_".$num1};
$email_aux = ${"email".$id_prenota."_".$num1};
$id_clienti_ins = inserisci_dati_cliente($cognome_aux,$nome_aux,"",$titolo_cli_aux,$sesso_aux,$mesenascita_aux,$giornonascita_aux,$annonascita_aux,$nazionenascita_aux,$cittanascita_aux,$regionenascita_aux,$documento_aux,$tipodoc_aux,$mesescaddoc_aux,$giornoscaddoc_aux,$annoscaddoc_aux,$cittadoc_aux,$regionedoc_aux,$nazionedoc_aux,$nazionalita_aux,$lingua_cli_aux,$nazione_aux,$citta_aux,$regione_aux,$via_aux,$nomevia_aux,$numcivico_aux,$cap_aux,$telefono__aux,$telefono2__aux,$telefono3__aux,$fax_aux,$email_aux,"","",($num_ospiti_inviati + 1),$id_utente_ins,$attiva_prefisso_clienti,$prefisso_clienti);
$num_ospiti++;
$lista_ospiti[$num_ospiti] = $id_clienti_ins;
$n_parentela[$num_ospiti] = ${"parentela".$id_prenota."_".$num1};
$lista_ospiti['lista'] .= $id_clienti_ins.",";
$presente_id[$id_clienti_ins] = "SI";
} # fine else if ($app_letto_idpr[$num_idpr] and $num_ospiti > 0)
} # fine if (!$id_clienti_osp and ${"cognome".$id_prenota."_".$num1} and...
} # fine for $num1

$idclienti_compagni = explode(",",substr($idclienti_compagni,0,-1));
$num_idclienti_compagni = count($idclienti_compagni);
for ($num1 = 0 ; $num1 < $num_idclienti_compagni ; $num1++) {
$id_clienti_osp = $idclienti_compagni[$num1];
if (${"altro_ospite".$id_prenota."_".$id_clienti_osp} == "SI" and $presente_id[$id_clienti_osp] != "SI") {
$num_ospiti++;
$lista_ospiti[$num_ospiti] = $id_clienti_osp;
$lista_ospiti['lista'] .= $id_clienti_osp.",";
$presente_id[$id_clienti_osp] = "SI";
} # fine if (${"altro_ospite".$id_prenota."_".$id_clienti_osp} == "SI" and...
} # fine for $num1

$max_num_ordine = 2;
for ($num1 = 1 ; $num1 <= $num_ospiti ; $num1++) {
$id_clienti_osp = $lista_ospiti[$num1];
if ($id_clienti_osp == $idclienti) $n_num_ordine = 1;
else $n_num_ordine = $max_num_ordine;
$max_num_ordine++;
if (!$osp_presente[$id_clienti_osp]) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
if ($n_num_ordine > 2) {
if (@get_magic_quotes_gpc()) $n_parentela[$num1] = stripslashes($n_parentela[$num1]);
$parentela = htmlspecialchars($n_parentela[$num1]);
} # fine if ($n_num_ordine > 2)
else $parentela = "";
esegui_query("insert into $tablerclientiprenota (idprenota,idclienti,num_ordine,parentela,datainserimento,hostinserimento,utente_inserimento) values ('$id_prenota','$id_clienti_osp','$n_num_ordine','$parentela','$datainserimento','$HOSTNAME','$id_utente_ins') ");
} # fine if (!$osp_presente[$id_clienti_osp])
else {
$num_ordine = risul_query($ospiti,($osp_presente[$id_clienti_osp] - 1),'num_ordine');
if ($num_ordine != $n_num_ordine) {
esegui_query("update $tablerclientiprenota set num_ordine = '$n_num_ordine' where idprenota = '$id_prenota' and idclienti = '$id_clienti_osp' ");
esegui_query("update $tablerclientiprenota set parentela = '' where (idprenota = '$id_prenota'$lista_parentela_cambiata) or (idprenota = '$id_prenota' and num_ordine < 3) ");
} # fine if ($num_ordine != $n_num_ordine)
} # fine else if (numlin_query($osp_presente) == 0)
$d_max_num_ordine = esegui_query("select idclienti_compagni,max_num_ordine from $tableclienti where idclienti = '$id_clienti_osp' ");
$d_idclienti_compagni = risul_query($d_max_num_ordine,0,'idclienti_compagni');
$d_max_num_ordine = risul_query($d_max_num_ordine,0,'max_num_ordine');
if ($d_max_num_ordine < $n_num_ordine) esegui_query("update $tableclienti set max_num_ordine = '$n_num_ordine' where idclienti = '$id_clienti_osp'");
$n_idclienti_compagni = $d_idclienti_compagni;
for ($num2 = 1 ; $num2 <= $num_ospiti ; $num2++) {
if ($lista_ospiti[$num2] != $id_clienti_osp and str_replace(",".$lista_ospiti[$num2].",","",$n_idclienti_compagni) == $n_idclienti_compagni) $n_idclienti_compagni .= $lista_ospiti[$num2].",";
} # fine for $num2
if ($n_idclienti_compagni != $d_idclienti_compagni) esegui_query("update $tableclienti set idclienti_compagni = '$n_idclienti_compagni' where idclienti = '$id_clienti_osp'");
} # fine for $num1

} # fine for $num_idpr
unlock_tabelle($tabelle_lock);

if ($mess_letto) echo "".mex("[1]Ogni appartamento può contenere al <span class=\"colred\">massimo un ospite</span>",'unit.php').".<br><br>";
if ($num_id_prenota == 1) echo "".mex("Gli ospiti della prenotazione",$pag)." $id_prenota";
else echo "".mex("Gli ospiti delle prenotazioni",$pag)." ".str_replace(",",", ",$id_prenota_int);
echo " ".mex("sono stati modificati",$pag).".<br><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"".htmlspecialchars($origine)."\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"ospiti_visibili\" value=\"SI\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
<br></div></form>";
} # fine if ($continua)


} # fine if ($modifica == "SI")




if ($form_modifica_ospiti != "NO") {

# Form per modificare gli ospiti.
if ($num_id_prenota == 1) echo "<h3 id=\"h_mgst\"><span>".mex("Modifica gli ospiti della prenotazione",$pag)." $id_prenota</span></h3><br>";
else echo "<h3 id=\"h_mgst\"><span>".mex("Modifica gli ospiti delle prenotazioni",$pag)." ".str_replace(",",", ",$id_prenota_int).".</span></h3><br>";


echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_ospiti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$id_prenota_int\">
<input type=\"hidden\" name=\"modifica\" value=\"SI\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">";

if ($inserimento_nuovi_clienti == "SI") {
mostra_funzjs_cpval();
mostra_funzjs_dati_rel("","",$id_sessione,$anno);
} # fine ($inserimento_nuovi_clienti == "SI")


for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$idclienti = $idclienti_idpr[$id_prenota];
$num_persone_tot = $num_persone_tot_idpr[$num_idpr];

if ($num_idpr > 0) echo "<br><br>";
$dati_cliente = esegui_query("select * from $tableclienti where idclienti = '$idclienti' ");
echo "".mex("Cliente titolare",$pag);
if ($num_id_prenota > 1) echo " ".mex("della prenotazione",$pag)." <b>$id_prenota</b>";
echo ":<br>";
mostra_dati_cliente($dati_cliente,$dcognome,$dnome,$dsoprannome,$dtitolo_cli,$dsesso,$ddatanascita,$ddatanascita_f,$dnazionenascita,$dcittanascita,$dregionenascita,$ddocumento,$dscadenzadoc,$dscadenzadoc_f,$dtipodoc,$dnazionedoc,$dregionedoc,$dcittadoc,$dnazionalita,$dnazione,$dlingua_cli,$dregione,$dcitta,$dvia,$dnumcivico,$dtelefono,$dtelefono2,$dtelefono3,$dfax,$dcap,$demail,$dcod_fiscale,$dpartita_iva,"",$priv_ins_clienti);
$cognome_def = $dcognome;
if ($dnazionalita) $nazione_def = $dnazionalita;
elseif ($dnazione) $nazione_def = $dnazione;
elseif ($dnazionedoc) $nazione_def = $dnazionedoc;
elseif ($dnazionenascita) $nazione_def = $dnazionenascita;
$nazione_def = addslashes($nazione_def);
if ($dregionenascita) $regione_def = $dregionenascita;
elseif ($dregione) $regione_def = $dregione;
elseif ($dregionedoc) $regione_def = $dregionedoc;
elseif ($dregionenascita) $regione_def = $dregionenascita;
$regione_def = addslashes($regione_def);
if ($dcittanascita) $citta_def = $dcittanascita;
elseif ($dcitta) $citta_def = $dcitta;
elseif ($dcittadoc) $citta_def = $dcittadoc;
elseif ($dcittanascita) $citta_def = $dcittanascita;
$citta_def = addslashes($citta_def);

$ospiti = esegui_query("select * from $tablerclientiprenota where idprenota = '$id_prenota' order by num_ordine ");
$num_ospiti = numlin_query($ospiti);
$mostra_osp = "SI";
$agg_num = 1;
unset($presente_id);
$presente_id[$idclienti] = "SI";
$idclienti_compagni = "";
for ($num1 = 0 ; $num1 < $num_ospiti ; $num1++) {
$id_clienti_osp = risul_query($ospiti,$num1,'idclienti');
$parentela_osp = risul_query($ospiti,$num1,'parentela');
$url_mod_cli = "./modifica_cliente.php?tipo_tabella=$tipo_tabella&amp;anno=$anno&amp;id_sessione=$id_sessione&amp;idclienti=$id_clienti_osp&amp;origine=".str_replace("=","%3D",str_replace("?","%3F",str_replace("&","%26",$origine)));
if ($num_persone_tot and ($num1 + $agg_num) > $num_persone_tot) $stile = " style=\"color: red;\"";
else $stile = "";
$num_ord = risul_query($ospiti,$num1,'num_ordine');
if ($num1 == 0) {
if ($num_ord == 1 and $id_clienti_osp == $idclienti) {
echo "<br><a$stile href=\"$url_mod_cli\">".($num1 + 1).".</a> <label><input name=\"cliente_ospite".$id_prenota."_".$id_clienti_osp."\" value=\"SI\" type=\"checkbox\" checked> ".mex("Ospite della prenotazione",$pag)."</label>";
$mostra_osp = "NO";
$cliente_ospite = "SI";
$dati_ospite = $dati_cliente;
} # fine if ($num_ord == 1 and..
else echo "<br><label><input name=\"cliente_ospite".$id_prenota."_".$idclienti."\" value=\"SI\" type=\"checkbox\"> ".mex("Ospite della prenotazione",$pag)."</label><br>";
} # fine if ($num1 == 0)
if ($mostra_osp == "SI") {
$dati_ospite = esegui_query("select * from $tableclienti where idclienti = '$id_clienti_osp' ");
$utente_ospite = risul_query($dati_ospite,0,'utente_inserimento');
if (($modifica_clienti == "PROPRI" and $utente_ospite != $id_utente) or ($modifica_clienti == "GRUPPI" and !$utenti_gruppi[$utente_ospite])) echo "<br>".($num1 + $agg_num).".";
else echo "<br><a$stile href=\"$url_mod_cli\">".($num1 + $agg_num).".</a>";
echo " <label><input name=\"ospite$id_prenota"."_".$id_clienti_osp."\" value=\"SI\" type=\"checkbox\" checked>";
if (($vedi_clienti == "PROPRI" and $utente_ospite != $id_utente) or ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_ospite])) echo "".mex("Cliente",$pag)." $id_clienti_osp";
else mostra_dati_cliente($dati_ospite,$dcognome,$dnome,$dsoprannome,$dtitolo_cli,$dsesso,$ddatanascita,$ddatanascita_f,$dnazionenascita,$dcittanascita,$dregionenascita,$ddocumento,$dscadenzadoc,$dscadenzadoc_f,$dtipodoc,$dnazionedoc,$dregionedoc,$dcittadoc,$dnazionalita,$dlingua_cli,$dnazione,$dregione,$dcitta,$dvia,$dnumcivico,$dtelefono,$dtelefono2,$dtelefono3,$dfax,$dcap,$demail,$dcod_fiscale,$dpartita_iva,"",$priv_ins_clienti);
echo "</label>";
if ($num1 == 0) {
$checked = " checked";
$cognome_def = $dcognome;
if ($dnazionalita) $nazione_def = $dnazionalita;
elseif ($dnazione) $nazione_def = $dnazione;
elseif ($dnazionedoc) $nazione_def = $dnazionedoc;
elseif ($dnazionenascita) $nazione_def = $dnazionenascita;
$nazione_def = addslashes($nazione_def);
if ($dregionenascita) $regione_def = $dregionenascita;
elseif ($dregione) $regione_def = $dregione;
$regione_def = addslashes($regione_def);
if ($dcittanascita) $citta_def = $dcittanascita;
elseif ($dcitta) $citta_def = $dcitta;
$citta_def = addslashes($citta_def);
} # fine if ($num1 == 0)
else {
$checked = "";
echo "<br>".addslashes(mex("Parentela",$pag)).": ".mostra_lista_relutenti("parentela_$id_prenota"."_$id_clienti_osp",$parentela_osp,$id_utente,"nome_parentela","idparentele","idparentela",$tableparentele,$tablerelutenti)."";
} # fine else if ($num1 == 0)
if ($cliente_ospite != "SI") echo "<br><input name=\"principale$id_prenota\" value=\"$id_clienti_osp\" type=\"radio\"$checked>".mex("Ospite principale",$pag)." ";
} # fine if ($mostra_osp == "SI")
echo "<br>
<input type=\"hidden\" name=\"id_osp_num$id_prenota"."_".($num1 + 1)."\" value=\"$id_clienti_osp\">";
$presente_id[$id_clienti_osp] = "SI";
$idclienti_compagni .= substr(risul_query($dati_ospite,0,'idclienti_compagni'),1);
$mostra_osp = "SI";
} # fine for $num1
if ($num_ospiti == 0) {
echo "<br><label><input name=\"cliente_ospite".$id_prenota."_".$idclienti."\" value=\"SI\" type=\"checkbox\"> ".mex("Ospite della prenotazione",$pag)."</label><br>";
} # fine if ($num_ospiti == 0)

if ($num_persone_tot > $num1) $num_fine = $num_persone_tot;
else $num_fine = $num1 + 1;

if ($inserimento_nuovi_clienti == "SI") {
$n_ini = $num1 + $agg_num;
for ( ; $num1 < $num_fine ; $num1++) {
$n_o = $num1 + $agg_num;
if ($n_ini != 1) $n_ini = $n_o;
echo "<br>$n_o. ".mex("Cognome",$pag).": <input type=\"text\" id=\"cognome$id_prenota"."_$n_o\" name=\"cognome$id_prenota"."_$n_o\"><input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('cognome$id_prenota"."_$n_o','cognome$id_prenota"."_$n_ini','$cognome_def')\" value=\"#\">,
<span class=\"wsnw\">".mex("nome",$pag).": <input type=\"text\" name=\"nome$id_prenota"."_$n_o\">;</span>
<span class=\"wsnw\">".mex("sesso",$pag).": <select name=\"sesso$id_prenota"."_$n_o\">
<option value=\"\" selected>-</option>
<option value=\"m\">m</option>
<option value=\"f\">f</option>
</select>;</span><br>
<span class=\"wsnw\">".mex("cittadinanza",$pag).": ".mostra_lista_relutenti("nazionalita$id_prenota"."_$n_o","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti)."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazionalita$id_prenota"."_$n_o','nazionalita$id_prenota"."_$n_ini','$nazione_def')\" value=\"#\">;</span>
 <span class=\"wsnw\">".addslashes(mex("parentela",$pag)).": ".mostra_lista_relutenti("parentela$id_prenota"."_$n_o","",$id_utente,"nome_parentela","idparentele","idparentela",$tableparentele,$tablerelutenti)."</span><br>
<div style=\"height: 2px\"></div>
".mex("Data di nascita",$pag).": ";
$sel_gnascita = "<span class=\"wsnw\"><select name=\"giornonascita$id_prenota"."_$n_o\">
<option value=\"\" selected>--</option>";
for ($num = 1; $num <= 31; $num++) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gnascita .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_gnascita .= "</select>";
$sel_mnascita = "<select name=\"mesenascita$id_prenota"."_$n_o\">
<option value=\"\" selected>--</option>";
for ($num = 1; $num <= 12; $num++) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mnascita .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_mnascita .= "</select>";
if ($stile_data == "usa") echo "$sel_mnascita/$sel_gnascita";
else echo "$sel_gnascita/$sel_mnascita";
echo "/<input type=\"text\" name=\"annonascita$id_prenota"."_$n_o\" size=\"5\" maxlength=\"4\" value=\"19\"></span> (".mex("anno con 4 cifre",$pag)."),
 <span class=\"wsnw smlscrfnt\">".mex("nazione di nascita",$pag).": ".mostra_lista_relutenti("nazionenascita$id_prenota"."_$n_o","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","regionenascita$id_prenota"."_$n_o")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazionenascita$id_prenota"."_$n_o','nazionalita$id_prenota"."_$n_o','$nazione_def')\" value=\"#\">,</span><br>
<span class=\"wsnw smlscrfnt\">".mex("reg./prov. di nascita",$pag).": ".mostra_lista_relutenti("regionenascita$id_prenota"."_$n_o","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","cittanascita$id_prenota"."_$n_o","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('regionenascita$id_prenota"."_$n_o','regionenascita$id_prenota"."_$n_ini','$regione_def')\" value=\"#\">,</span>
 <span class=\"wsnw smlscrfnt\">".mex("città di nascita",$pag).": ".mostra_lista_relutenti("cittanascita$id_prenota"."_$n_o","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('cittanascita$id_prenota"."_$n_o','cittanascita$id_prenota"."_$n_ini','$citta_def')\" value=\"#\"></span><br>
<div style=\"height: 2px\"></div>";
if ($n_o == 1) {
echo "".mex("Nazione",$pag).": ".mostra_lista_relutenti("nazione$id_prenota"."_$n_o","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","regione$id_prenota"."_$n_o")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazione$id_prenota"."_$n_o','nazionalita$id_prenota"."_$n_o','$nazione_def')\" value=\"#\">,
 ".mex("reg./prov.",$pag).": ".mostra_lista_relutenti("regione$id_prenota"."_$n_o","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","citta$id_prenota"."_$n_o","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('regione$id_prenota"."_$n_o','regione$id_prenota"."_$n_ini','$regione_def')\" value=\"#\">,
 ".mex("città",$pag).": ".mostra_lista_relutenti("citta$id_prenota"."_$n_o","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('citta$id_prenota"."_$n_o','citta$id_prenota"."_$n_ini','$citta_def')\" value=\"#\"><br>
<div style=\"height: 2px\"></div>
".mex("Documento",$pag).": ".mostra_lista_relutenti("tipodoc$id_prenota"."_$n_o","",$id_utente,"nome_documentoid","iddocumentiid","iddocumentoid",$tabledocumentiid,$tablerelutenti,"","","SI");
echo "<input type=\"text\" name=\"documento$id_prenota"."_$n_o\">
 ".mex("scadenza",$pag).": ";
$sel_gscaddoc = "<select name=\"giornoscaddoc$id_prenota"."_$n_o\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 31; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_gscaddoc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_gscaddoc .= "</select>";
$sel_mscaddoc = "<select name=\"mesescaddoc$id_prenota"."_$n_o\">
<option value=\"\" selected>--</option>";
for ( $num = 1; $num <= 12; $num = $num + 1) {
if (strlen($num) == 1) $num = "0".$num;
$sel_mscaddoc .= "<option value=\"$num\">$num</option>";
} # fine for $num
$sel_mscaddoc .= "</select>";
if ($stile_data == "usa") echo "$sel_mscaddoc/$sel_gscaddoc";
else echo "$sel_gscaddoc/$sel_mscaddoc";
echo "/<select name=\"annoscaddoc$id_prenota"."_$n_o\">";
$anno_corr = date("Y",(time() + (C_DIFF_ORE * 3600)));
for ($num3 = 0 ; $num3 < 12 ; $num3++) {
$num = $anno_corr - 12 + $num3;
echo "<option value=\"$num\">$num</option>";
} # fine for $num3
echo "<option value=\"\" selected>--</option>";
for ($num3 = 0 ; $num3 < 16 ; $num3++) {
$num = $anno_corr + $num3;
echo "<option value=\"$num\">$num</option>";
} # fine for $num3
echo "</select>;<br>
".mex("nazione di rilascio",$pag).": ".mostra_lista_relutenti("nazionedoc$id_prenota"."_$n_o","",$id_utente,"nome_nazione","idnazioni","idnazione",$tablenazioni,$tablerelutenti,"","","","regione","regionedoc$id_prenota"."_$n_o")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('nazionedoc$id_prenota"."_$n_o','nazionalita$id_prenota"."_$n_o','$nazione_def')\" value=\"#\">,
 ".mex("reg./prov.",$pag).": ".mostra_lista_relutenti("regionedoc$id_prenota"."_$n_o","",$id_utente,"nome_regione","idregioni","idregione",$tableregioni,$tablerelutenti,"","","","citta","cittadoc$id_prenota"."_$n_o","nazione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('regionedoc$id_prenota"."_$n_o','regione$id_prenota"."_$n_o','$regione_def')\" value=\"#\">,
 ".mex("città",$pag).": ".mostra_lista_relutenti("cittadoc$id_prenota"."_$n_o","",$id_utente,"nome_citta","idcitta","idcitta",$tablecitta,$tablerelutenti,"","","","","","regione")."<input type=\"button\" class=\"cpbutton\" onclick=\"cp_val('cittadoc$id_prenota"."_$n_o','citta$id_prenota"."_$n_o','$citta_def')\" value=\"#\"><br>";
} # fine if ($n_o == 1)
} # fine for $num1
} # fine if ($inserimento_nuovi_clienti == "SI")
echo "<input type=\"hidden\" name=\"num_ospiti_inviati$id_prenota\" value=\"$num1\">";

$idclienti_compagni = substr($idclienti_compagni,0,-1);
if ($idclienti_compagni) {
$dati_osp = "";
$idclienti_compagni = explode(",",$idclienti_compagni);
$num_idclienti_compagni = count($idclienti_compagni);
for ($num1 = 0 ; $num1 < $num_idclienti_compagni ; $num1++) {
$id_clienti_osp = $idclienti_compagni[$num1];
if ($presente_id[$id_clienti_osp] != "SI") {
$presente_id[$id_clienti_osp] = "SI";
$dati_ospite = esegui_query("select cognome,nome,sesso,datanascita,utente_inserimento from $tableclienti where idclienti = '$id_clienti_osp' ");
$dati_osp .= "<input name=\"altro_ospite$id_prenota"."_$id_clienti_osp\" value=\"SI\" type=\"checkbox\">\
 <em>".addslashes(risul_query($dati_ospite,0,'cognome'))."</em> ";
$ccnome = addslashes(risul_query($dati_ospite,0,'nome'));
$ccsesso = risul_query($dati_ospite,0,'sesso');
$ccdatanascita = risul_query($dati_ospite,0,'datanascita');
$O = "o";
if ($ccsesso == "f") $O = "a";
if ($ccnome) $dati_osp .=  "$ccnome ";
if ($ccdatanascita) $dati_osp .= mex("nat$O il",$pag)." ".formatta_data($ccdatanascita,$stile_data)." ";
$dati_osp .= "<br>";
} # fine if ($presente_id[$id_clienti_osp] != "SI")
} # fine for $num1
if ($dati_osp) {
echo "
<script type=\"text/javascript\">
<!--
function apri_osp$id_prenota () {
var bott = document.getElementById('bott_osp$id_prenota');
var elem_cli = document.getElementById('osp_cli$id_prenota');
var osp_vis = elem_cli.style.visibility;
if (osp_vis != 'visible') {
var testo = '$dati_osp';
elem_cli.style.visibility = 'visible';
bott.innerHTML = '<img src=\"./img/freccia_giu_marg.png\" alt=\"&gt;\">';
}
if (osp_vis == 'visible') {
var testo = '';
elem_cli.style.visibility = 'hidden';
bott.innerHTML = '<img src=\"./img/freccia_destra_marg.png\" alt=\"&gt;\">';
}
elem_cli.innerHTML = testo;
} // fine function apri_osp$id_prenota
-->
</script>
<br>".mex("Altri ospiti",$pag).": 
<button type=\"button\" id=\"bott_osp$id_prenota\" onclick=\"apri_osp$id_prenota()\">
<img src=\"./img/freccia_destra_marg.png\" alt=\"&gt;\"></button><br>
<div id=\"osp_cli$id_prenota\" style=\"visibility: hidden;\"></div>";
} # fine if ($dati_osp)
} # fine if ($idclienti_compagni)


} # fine for $num_idpr


echo "<br><div style=\"text-align: center;\">
<button class=\"gsts\" id=\"modi\" type=\"submit\"><div>".mex("Modifica gli ospiti",$pag)."</div></button>
</div></div></form>";


echo "<hr style=\"width: 95%\"><br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"".htmlspecialchars($origine)."\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"tipo_tabella\" value=\"$tipo_tabella\">
<button class=\"gobk\" id=\"indi\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form></div><br>";

} # fine if ($form_modifica_ospiti != "NO")


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($modifica_clienti != "NO" and...
} # fine if ($anno_utente_attivato == "SI" and...
} # fine if ($id_utente)



?>
