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



function trova_prenota_succ ($anno,$d_id_data_fine,$PHPR_TAB_PRE,$id_prenota,&$fineperiodo_succ) {
$fineperiodo_succ = "";
$d_data_fine = esegui_query("select datafine from ".$PHPR_TAB_PRE."periodi".$anno." where idperiodi = '$d_id_data_fine'");
$d_data_fine = risul_query($d_data_fine,0,'datafine');
if (substr($d_data_fine,0,4) != $anno) {
$anno_succ_esistente = esegui_query("select idanni from ".$PHPR_TAB_PRE."anni where idanni = '".($anno + 1)."'");
if (numlin_query($anno_succ_esistente) == 1) {
$id_prenota_succ = "soloanno";
$data_succ_esistente = esegui_query("select idperiodi from $PHPR_TAB_PRE"."periodi".($anno + 1)." where datafine <= '$d_data_fine'");
if (numlin_query($data_succ_esistente) >= 1) {
$prenota_succ = esegui_query("select idprenota,iddatafine from $PHPR_TAB_PRE"."prenota".($anno + 1)." where iddatainizio = '0' and commento = '$id_prenota'");
if (numlin_query($prenota_succ) == 1) {
$id_prenota_succ = risul_query($prenota_succ,0,'idprenota');
$fineperiodo_succ = risul_query($prenota_succ,0,'iddatafine');
} # fine if (numlin_query($prenota_in_anno_succ) == 1)
} # fine if (numlin_query($data_succ_esistente) >= 1)
} # fine if (numlin_query($anno_succ_esistente) == 1)
} # fine if (substr($d_data_fine,0,4) != $anno)
return $id_prenota_succ;
} # fine function trova_prenota_succ




function trova_prenota_prec ($anno,$d_id_data_inizio,$PHPR_TAB_PRE,$dati_prenota_modifica,&$n_dati_prenota_modifica) {
$n_dati_prenota_modifica = "";
if ($d_id_data_inizio == 0) {
$id_prenota_prec = "SI";
$id_prenota_prec_esist = aggslashdb(risul_query($dati_prenota_modifica,0,'commento'));
if ($id_prenota_prec_esist) {
$anno_prec_esistente = esegui_query("select idanni from ".$PHPR_TAB_PRE."anni where idanni = '".($anno - 1)."'");
if (numlin_query($anno_prec_esistente) == 1) {
$n_dati_prenota_modifica = esegui_query("select * from $PHPR_TAB_PRE"."prenota".($anno - 1)." where idprenota = '$id_prenota_prec_esist'");
if (numlin_query($n_dati_prenota_modifica) == 1) $id_prenota_prec = $id_prenota_prec_esist;
} # fine if (numlin_query($anno_prec_esistente) == 1)
} # fine if ($id_prenota_prec_esist)
} # fine if ($d_id_data_inizio == 0)
return $id_prenota_prec;
} # fine function trova_prenota_prec




function cancella_prenotazione ($id_prenota,$cancella_pagato,$metodo_pagamento,$priv_mod_pagato,$lock,$anno,$PHPR_TAB_PRE,$id_utente,&$stato_canc,$metodo_pagamento_imp="",$dettagli_prenota="") {
$pag = "modifica_prenota.php";
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$tableanni = $PHPR_TAB_PRE."anni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$tableinterconnessioni  = $PHPR_TAB_PRE."interconnessioni";
$tableclienti = $PHPR_TAB_PRE."clienti";

$continua_canc = "SI";
$tabelle_lock = array($tableprenota,$tablecostiprenota,$tablerclientiprenota,$tablesoldi,$tableinterconnessioni);
if ($dettagli_prenota) $altre_tab_lock = array($tableanni,$tableperiodi,$tableclienti,$tablepersonalizza);
else $altre_tab_lock = array($tableanni,$tableperiodi,$tablepersonalizza);
$dati_prenota = esegui_query("select * from $tableprenota where idprenota = '$id_prenota' and idclienti != '0' ");
$num_dati_prenota = numlin_query($dati_prenota);
if ($num_dati_prenota == 0) {
$testo .= mex("La prenotazione",$pag)." $id_prenota ".mex("era già stata cancellata",$pag)."!<br>";
$continua_canc = "NO";
} # fine if ($num_dati_prenota == 0)

# Controlli per vedere se la prenotazione è anche nell'anno precedente o successivo
if ($continua_canc != "NO") {
$id_prenota_succ = "";
$id_prenota_orig = "";
$d_id_data_inizio = risul_query($dati_prenota,0,'iddatainizio');
$d_id_data_fine = risul_query($dati_prenota,0,'iddatafine');
$id_prenota_succ = trova_prenota_succ($anno,$d_id_data_fine,$PHPR_TAB_PRE,$id_prenota,$fineperiodo_succ);
if ($id_prenota_succ == "soloanno") $id_prenota_succ = "";
if ($id_prenota_succ) {
$tableprenota_succ = $PHPR_TAB_PRE."prenota".($anno + 1);
$tablecostiprenota_succ = $PHPR_TAB_PRE."costiprenota".($anno + 1);
$tablerclientiprenota_succ = $PHPR_TAB_PRE."rclientiprenota".($anno + 1);
} # fine if ($id_prenota_succ)
$id_prenota_prec = trova_prenota_prec($anno,$d_id_data_inizio,$PHPR_TAB_PRE,$dati_prenota,$n_dati_prenota_modifica);
if ($id_prenota_prec == "SI") $id_prenota_prec = "";
if ($id_prenota_prec) {
$id_prenota_succ = $id_prenota;
$id_prenota = $id_prenota_prec;
$tableprenota_succ = $tableprenota;
$tablecostiprenota_succ = $tablecostiprenota;
$tablerclientiprenota_succ = $tablerclientiprenota;
$tableprenota = $PHPR_TAB_PRE."prenota".($anno - 1);
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".($anno - 1);
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".($anno - 1);
$tablesoldi = $PHPR_TAB_PRE."soldi".($anno - 1);
} # fine if ($id_prenota_prec)
if ($id_prenota_succ) {
$tabelle_lock = array($tableprenota,$tableprenota_succ,$tablecostiprenota,$tablecostiprenota_succ,$tablerclientiprenota,$tablerclientiprenota_succ,$tablesoldi,$tableinterconnessioni);
} # fine if ($id_prenota_succ)
} # fine if ($continua_canc != "NO")

if ($lock) $tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
else $tabelle_lock = "";
$dati_prenota = esegui_query("select * from $tableprenota where idprenota = '$id_prenota' and idclienti != '0'");
$num_dati_prenota = numlin_query($dati_prenota);
if ($num_dati_prenota == 0 and $continua_canc != "NO") {
$testo .= mex("La prenotazione",$pag)." $id_prenota ".mex("era già stata cancellata",$pag)."!<br>";
$continua_canc = "NO";
} # fine if ($num_dati_prenota == 0 and $continua_canc != "NO")

if ($continua_canc != "NO") {
$d_pagato = risul_query($dati_prenota,0,'pagato');
if ($d_pagato != 0) {
if ($cancella_pagato and $priv_mod_pagato == "s") {
if (get_magic_quotes_gpc()) $metodo_pagamento = (string) stripslashes($metodo_pagamento);
$metodo_pagamento = htmlspecialchars($metodo_pagamento);
if ($metodo_pagamento != "") {
$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '$id_utente'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
$metodi_pagamento = explode(",",$metodi_pagamento);
$metodo_trovato = "NO";
for ($num1 = 0 ; $num1 < count($metodi_pagamento) ; $num1++) if ($metodi_pagamento[$num1] == $metodo_pagamento) $metodo_trovato = "SI";
if ($metodo_trovato == "NO") $metodo_pagamento = "";
} # fine if ($metodo_pagamento != "")
if (strcmp($metodo_pagamento_imp,"")) $metodo_pagamento = $metodo_pagamento_imp;
$metodo_pagamento = aggslashdb($metodo_pagamento);
$datainserimento_saldo = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$motivazione_saldo = risul_query($dati_prenota,0,'idclienti');
$d_idinizioprenota = risul_query($dati_prenota,0,'iddatainizio');
$d_idfineprenota = risul_query($dati_prenota,0,'iddatafine');
$motivazione_saldo .= ";$d_idinizioprenota;$d_idfineprenota;$id_prenota";
$saldo = 0 - $d_pagato;
$idsoldi = esegui_query("select max(idsoldi) from $tablesoldi");
$idsoldi = risul_query($idsoldi,0,0) + 1;
esegui_query("insert into $tablesoldi (idsoldi,motivazione,saldo_prenota,soldi_prima,data_inserimento,metodo_pagamento,utente_inserimento) values ('$idsoldi','$motivazione_saldo','$saldo','$d_pagato','$datainserimento_saldo','$metodo_pagamento','$id_utente')");
} # fine if ($cancella_pagato and $priv_mod_pagato == "s")
else {
$soldi_prenota_cancellate = esegui_query("select * from $tablesoldi where idsoldi = 1");
$soldi_prenota_cancellate = risul_query($soldi_prenota_cancellate,0,'soldi_prima');
$soldi_prenota_cancellate = $soldi_prenota_cancellate + $d_pagato;
esegui_query("update $tablesoldi set soldi_prima = '$soldi_prenota_cancellate' where idsoldi = 1");
} # fine else if ($cancella_pagato and $priv_mod_pagato == "s")
} # fine if ($d_pagato != 0)
$d_prenota_comp = aggslashdb(risul_query($dati_prenota,0,'idprenota_compagna'));
if ($d_prenota_comp) {
$d_prenota_comp_vett = explode(",",$d_prenota_comp);
for ($num1 = 0 ; $num1 < count($d_prenota_comp_vett) ; $num1++) {
$idprenota_compagna = substr(str_replace(",".$d_prenota_comp_vett[$num1].",",",",",$d_prenota_comp,"),1,-1);
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_compagna' where idprenota = '".$d_prenota_comp_vett[$num1]."' ");
} # fine for $num1
} # fine if ($d_prenota_comp)
esegui_query("delete from $tableprenota where idprenota = '$id_prenota'");
esegui_query("delete from $tablecostiprenota where idprenota = '$id_prenota'");
esegui_query("delete from $tablerclientiprenota where idprenota = '$id_prenota'");
if ($id_prenota_succ) {
esegui_query("delete from $tableprenota_succ where idprenota = '$id_prenota_succ'");
esegui_query("delete from $tablecostiprenota_succ where idprenota = '$id_prenota_succ'");
esegui_query("delete from $tablerclientiprenota_succ where idprenota = '$id_prenota_succ'");
if ($d_id_data_inizio == 0 and $id_prenota_prec) {
esegui_query("delete from $tableinterconnessioni where idlocale = '$id_prenota' and tipoid = 'prenota' and anno = '".($anno - 1)."' ");
esegui_query("delete from $tableinterconnessioni where idlocale = '$id_prenota_succ' and tipoid = 'prenota' and anno = '$anno' ");
$id_prenota = $id_prenota_succ;
} # fine if ($d_id_data_inizio == 0 and $id_prenota_prec)
else {
esegui_query("delete from $tableinterconnessioni where idlocale = '$id_prenota' and (tipoid = 'prenota' or tipoid = 'conferma') and anno = '$anno' ");
esegui_query("delete from $tableinterconnessioni where idlocale = '$id_prenota_succ' and tipoid = 'prenota' and anno = '".($anno + 1)."' ");
} # fine else if ($d_id_data_inizio == 0 and $id_prenota_prec)
} # fine if ($id_prenota_succ)
else esegui_query("delete from $tableinterconnessioni where idlocale = '$id_prenota' and tipoid = 'prenota' and anno = '$anno' ");
if ($dettagli_prenota) {
$cognome_cliente = risul_query($dati_prenota,0,'idclienti');
$cognome_cliente = esegui_query("select cognome from $tableclienti where idclienti = '$cognome_cliente' ");
$cognome_cliente = risul_query($cognome_cliente,0,'cognome');
$data_inizio = risul_query($dati_prenota,0,'iddatainizio');
$data_fine = risul_query($dati_prenota,0,'iddatafine');
$data_inizio = esegui_query("select datainizio from $tableperiodi where idperiodi = '$data_inizio' ");
$data_fine = esegui_query("select datafine from $tableperiodi where idperiodi = '$data_fine' ");
if (numlin_query($data_inizio)) $data_inizio = risul_query($data_inizio,0,'datainizio');
else $data_inizio = "";
if (numlin_query($data_fine)) $data_fine = risul_query($data_fine,0,'datafine');
else $data_fine = "";
$stile_data = stile_data($id_utente);
$data_inizio = formatta_data($data_inizio,$stile_data);
$data_fine = formatta_data($data_fine,$stile_data);
$testo .= mex("Prenotazione",$pag)." $id_prenota ".mex("a nome di",$pag)." <b>$cognome_cliente</b> ".mex("dal",$pag)." $data_inizio ".mex("al",$pag)." $data_fine <em>".mex("cancellata",$pag)."</em>!<br>";
} # fine if ($dettagli_prenota)
else $testo .= mex("Prenotazione",$pag)." $id_prenota ".mex("cancellata",$pag)."!<br>";
} # fine if ($continua_canc != "NO")
$stato_canc = $continua_canc;
if ($tabelle_lock) unlock_tabelle($tabelle_lock);

return $testo;

} # fine function cancella_prenotazione




function controlla_id_prenota(&$id_prenota,&$id_prenota_idpr,&$num_id_prenota,&$id_prenota_int,&$priv_mod_prenotazioni,$anno,$PHPR_TAB_PRE) {
global $priv_mod_prenota_comp,$priv_mod_prenota_iniziate,$priv_mod_prenota_ore,$priv_mod_pagato,$id_utente,$utenti_gruppi;

$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;

$id_prenota = aggslashdb($id_prenota);
$id_prenota_int = $id_prenota;
$id_prenota_idpr = array();
$id_prenota_idpr[0] = $id_prenota;
$num_id_prenota = 1;
if (str_replace(",","",$id_prenota) != $id_prenota) {
$id_prenota_idpr = explode(",",$id_prenota);
$num_id_prenota = count($id_prenota_idpr);
$priv_mod_prenota_comp = "n";
} # fine if (str_replace(",","",$id_prenota) != $id_prenota)

if ($idprenota_origine) $anno_origine = $anno + 1;
else $anno_origine = $anno;
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

if (defined("C_MASSIMO_NUM_STORIA_SOLDI") and C_MASSIMO_NUM_STORIA_SOLDI != 0) {
$num_soldi_esistenti = esegui_query("select idsoldi from $tablesoldi");
$num_soldi_esistenti = numlin_query($num_soldi_esistenti);
if ($num_soldi_esistenti >= (C_MASSIMO_NUM_STORIA_SOLDI + 1)) $priv_mod_pagato = "n";
} # fine if (defined("C_MASSIMO_NUM_STORIA_SOLDI") and C_MASSIMO_NUM_STORIA_SOLDI != 0)

} # fine function controlla_id_prenota




function prepara_modifiche_prenotazione ($id_prenota_idpr,$num_id_prenota,&$prenota_in_anno_succ,&$dati_da_anno_prec,&$tra_anni,$anno,$PHPR_TAB_PRE) {
global $d_id_utente_inserimento_idpr,$d_id_clienti_idpr,$d_id_data_inizio_idpr,$d_id_data_fine_idpr,$d_appartamento_idpr,$d_assegnazione_app_idpr,$d_app_assegnabili_idpr,$d_nome_tipotariffa_idpr,$d_app_eliminati_costi_idpr,$d_sconto_idpr,$d_caparra_idpr,$d_met_paga_caparra_idpr,$d_commissioni_idpr,$d_num_persone_idpr,$d_nome_tariffa_idpr,$d_costo_tariffa_idpr,$d_molt_tariffa_idpr,$d_tariffesettimanali_idpr,$d_costo_agg_tot_idpr,$d_prezzo_costo_agg_idpr,$d_costo_tot_idpr,$d_pagato_idpr,$d_confermato_idpr,$d_checkin_idpr,$d_checkout_idpr,$d_commento,$d_prenota_comp_idpr;
global $dati_tariffe,$dati_ca,$dati_prenota_modifica,$id_prenota_orig,$tableperiodi_orig,$tableprenota_orig,$tablecostiprenota_orig,$tableperiodi_prec,$tableprenota_prec,$tablecostiprenota_prec,$fineperiodo_orig,$stile_data,$d_data_inizio_f,$d_data_fine_f,$n_host_inserimento_idpr;
if (!function_exists('dati_tariffe')) include("./includes/funzioni_tariffe.php");
if (!function_exists('dati_costi_agg_ntariffe')) include("./includes/funzioni_costi_agg.php");

$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;

$d_id_utente_inserimento_idpr = array();
$d_id_clienti_idpr = array();
$d_id_data_inizio_idpr = array();
$d_id_data_fine_idpr = array();
$d_appartamento_idpr = array();
$d_assegnazione_app_idpr = array();
$d_app_assegnabili_idpr = array();
$d_nome_tipotariffa_idpr = array();
$d_costo_tariffa_idpr = array();
$d_molt_tariffa_idpr = array();
$d_app_eliminati_costi_idpr = array();
$d_sconto_idpr = array();
$d_caparra_idpr = array();
$d_met_paga_caparra_idpr = array();
$d_commissioni_idpr = array();
$d_num_persone_idpr = array();
$d_nome_tariffa_idpr = array();
$d_tariffesettimanali_idpr = array();
$d_costo_agg_tot_idpr = array();
$d_prezzo_costo_agg_idpr = array();
$d_costo_tot_idpr = array();
$d_pagato_idpr = array();
$d_confermato_idpr = array();
$d_checkin_idpr = array();
$d_checkout_idpr = array();
$d_prenota_comp_idpr = array();
$n_host_inserimento_idpr = array();

$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num'],"NO","",$tableappartamenti);


$dati_prenota_modifica = esegui_query("select * from $tableprenota where idprenota = '".$id_prenota_idpr[0]."' and idclienti != '0'");
$d_id_data_inizio0 = risul_query($dati_prenota_modifica,0,'iddatainizio');
if ($d_id_data_inizio0 == 0) {
$d_data_inizio_f = "<";
$d_id_data_inizio = 1;
} # fine if ($d_id_data_inizio0 == 0)
else {
$d_data_inizio_f = "";
$d_id_data_inizio = $d_id_data_inizio0;
} # fine if ($d_id_data_inizio0 == 0)
$d_data_inizio = esegui_query("select datainizio from $tableperiodi where idperiodi = '$d_id_data_inizio' ");
$d_data_inizio = risul_query($d_data_inizio,0,'datainizio');
$d_data_inizio_f .= formatta_data($d_data_inizio,$stile_data);
$d_id_data_fine0 = risul_query($dati_prenota_modifica,0,'iddatafine');
$d_data_fine = esegui_query("select datafine from $tableperiodi where idperiodi = '$d_id_data_fine0' ");
$d_data_fine = risul_query($d_data_fine,0,'datafine');
$d_data_fine_f = formatta_data($d_data_fine,$stile_data);

for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
global ${"dati_cap".$id_prenota};

if ($num_idpr > 0) $dati_prenota_modifica = esegui_query("select * from $tableprenota where idprenota = '$id_prenota' and idclienti != '0'");
$d_id_utente_inserimento_idpr[$id_prenota] = risul_query($dati_prenota_modifica,0,'utente_inserimento');
$d_id_clienti_idpr[$id_prenota] = risul_query($dati_prenota_modifica,0,'idclienti');
$d_id_data_inizio = risul_query($dati_prenota_modifica,0,'iddatainizio');
$d_id_data_inizio_idpr[$id_prenota] = $d_id_data_inizio;
if ($d_id_data_inizio != $d_id_data_inizio0) $d_data_inizio_f = "~~~~";
$d_id_data_fine = risul_query($dati_prenota_modifica,0,'iddatafine');
$d_id_data_fine_idpr[$id_prenota] = $d_id_data_fine;
if ($d_id_data_fine != $d_id_data_fine0) $d_data_fine_f = "~~~~";
$d_appartamento = risul_query($dati_prenota_modifica,0,'idappartamenti');
$d_appartamento_idpr[$id_prenota] = $d_appartamento;
$d_assegnazione_app = risul_query($dati_prenota_modifica,0,'assegnazioneapp');
$d_assegnazione_app_idpr[$id_prenota] = $d_assegnazione_app;
$d_app_assegnabili = risul_query($dati_prenota_modifica,0,'app_assegnabili');
$d_app_assegnabili_idpr[$id_prenota] = $d_app_assegnabili;

$tra_anni = "";
$prenota_in_anno_succ = trova_prenota_succ($anno,$d_id_data_fine,$PHPR_TAB_PRE,$id_prenota,$fineperiodo_orig);
if ($prenota_in_anno_succ) {
$tra_anni = $anno;
$tableperiodi_orig = $PHPR_TAB_PRE."periodi".($anno + 1);
$tableprenota_orig = $PHPR_TAB_PRE."prenota".($anno + 1);
$tablecostiprenota_orig = $PHPR_TAB_PRE."costiprenota".($anno + 1);
$tableperiodi_prec = $tableperiodi;
$tableprenota_prec = $tableprenota;
$tablecostiprenota_prec = $tablecostiprenota;
if ($prenota_in_anno_succ != "soloanno") $id_prenota_orig = $prenota_in_anno_succ;
else $prenota_in_anno_succ = "";
} # fine if ($prenota_in_anno_succ)

$dati_da_anno_prec = "NO";
if ($num_id_prenota == 1) $id_prenota_prec = trova_prenota_prec($anno,$d_id_data_inizio,$PHPR_TAB_PRE,$dati_prenota_modifica,$n_dati_prenota_modifica);
else $id_prenota_prec = "";
if ($id_prenota_prec == "SI") $id_prenota_prec = "";
if ($id_prenota_prec) {
$dati_da_anno_prec = "SI";
$tra_anni = $anno - 1;
$dati_prenota_modifica = $n_dati_prenota_modifica;
$id_prenota_orig = $id_prenota;
$tableperiodi_orig = $tableperiodi;
$tableprenota_orig = $tableprenota;
$tablecostiprenota_orig = $tablecostiprenota;
$tableperiodi_prec = $PHPR_TAB_PRE."periodi".($anno - 1);
$tableprenota_prec = $PHPR_TAB_PRE."prenota".($anno - 1);
$tablecostiprenota_prec = $PHPR_TAB_PRE."costiprenota".($anno - 1);
$dati_tariffe = dati_tariffe($PHPR_TAB_PRE."ntariffe".($anno - 1),"","",$PHPR_TAB_PRE."regole".($anno - 1));
$dati_ca = dati_costi_agg_ntariffe($PHPR_TAB_PRE."ntariffe".($anno - 1),$dati_tariffe['num'],"NO","",$tableappartamenti);
$dati_cap = dati_costi_agg_prenota($tablecostiprenota_prec,$id_prenota_prec);
${"dati_cap".$id_prenota} = $dati_cap;
function passa_var_a_anno_prec (&$id_prenota,&$tableperiodi,&$tableprenota,&$tablecostiprenota,&$d_id_data_inizio,&$d_id_data_fine,&$n_fineperiodo,&$inizioperiodo,&$fineperiodo,&$lunghezza_periodo,$dati_prenota_modifica) {
global $id_prenota_prec,$tableperiodi_prec,$tableprenota_prec,$tablecostiprenota_prec,$d_id_data_inizio_orig,$d_id_data_fine_orig,$n_fineperiodo_orig,$inizioperiodo_orig,$fineperiodo_orig;
$id_prenota = $id_prenota_prec;
$tableperiodi = $tableperiodi_prec;
$tableprenota = $tableprenota_prec;
$tablecostiprenota = $tablecostiprenota_prec;
$d_id_data_inizio_orig = $d_id_data_inizio;
$d_id_data_fine_orig = $d_id_data_fine;
$n_fineperiodo_orig = $n_fineperiodo;
$inizioperiodo_orig = $inizioperiodo;
$fineperiodo_orig = $fineperiodo;
$d_id_data_inizio = risul_query($dati_prenota_modifica,0,'iddatainizio');
$inizioperiodo = $d_id_data_inizio;
if ($n_fineperiodo) $diff_fineperiodo = $n_fineperiodo - $d_id_data_fine;
$d_id_data_fine = risul_query($dati_prenota_modifica,0,'iddatafine');
if ($n_fineperiodo) {
$n_fineperiodo = $d_id_data_fine + $diff_fineperiodo;
$fineperiodo = $n_fineperiodo;
} # fine if ($n_fineperiodo)
else $fineperiodo = $d_id_data_fine;
$lunghezza_periodo = $fineperiodo - $inizioperiodo + 1;
} # fine function passa_var_a_anno_prec
function passa_var_a_anno_corr (&$id_prenota,&$tableperiodi,&$tableprenota,&$tablecostiprenota,&$d_id_data_inizio,&$d_id_data_fine,&$n_fineperiodo,&$inizioperiodo,&$fineperiodo,&$lunghezza_periodo) {
global $id_prenota_orig,$tableperiodi_orig,$tableprenota_orig,$tablecostiprenota_orig,$d_id_data_inizio_orig,$d_id_data_fine_orig,$n_fineperiodo_orig,$inizioperiodo_orig,$fineperiodo_orig;
$id_prenota = $id_prenota_orig;
$tableperiodi = $tableperiodi_orig;
$tableprenota = $tableprenota_orig;
$tablecostiprenota = $tablecostiprenota_orig;
$d_id_data_inizio = $d_id_data_inizio_orig;
$d_id_data_fine = $d_id_data_fine_orig;
$n_fineperiodo = $n_fineperiodo_orig;
$inizioperiodo = $inizioperiodo_orig;
$fineperiodo = $fineperiodo_orig;
$lunghezza_periodo = $fineperiodo - $inizioperiodo + 1;
} # fine function passa_var_a_anno_corr
} # fine if ($id_prenota_prec)

$d_tariffa = risul_query($dati_prenota_modifica,0,'tariffa');
$d_tariffa = explode("#@&",$d_tariffa);
$d_nome_tariffa = $d_tariffa[0];
$d_nome_tariffa_idpr[$id_prenota] = $d_nome_tariffa;
$d_costo_tariffa = (double) $d_tariffa[1];
$d_costo_tariffa_idpr[$id_prenota] = $d_costo_tariffa;
$d_molt_tariffa = $d_tariffa[2];
$d_molt_tariffa_idpr[$id_prenota] = $d_molt_tariffa;
if ($dati_da_anno_prec != "SI") {
$dati_cap = dati_costi_agg_prenota($tablecostiprenota,$id_prenota);
${"dati_cap".$id_prenota} = $dati_cap;
} # fine if ($dati_da_anno_prec != "SI")
$d_nome_tipotariffa = "";
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
$nometariffa = $dati_tariffe["tariffa$numtariffa"]['nome'];
if ($d_nome_tariffa == $nometariffa or $d_nome_tariffa == "tariffa".$numtariffa) {
$d_nome_tipotariffa = "tariffa".$numtariffa;
} # fine if ($d_nome_tariffa == $nometariffa or $d_nome_tariffa == "tariffa".$numtariffa
} # fine for $numtariffa
$d_nome_tipotariffa_idpr[$id_prenota] = $d_nome_tipotariffa;
$d_app_eliminati_costi = risul_query($dati_prenota_modifica,0,'incompatibilita');
$d_app_eliminati_costi_idpr[$id_prenota] = $d_app_eliminati_costi;
$d_sconto = (double) risul_query($dati_prenota_modifica,0,'sconto');
if (!$d_sconto) $d_sconto = (double) 0;
$d_sconto_idpr[$id_prenota] = $d_sconto;
$d_commento = risul_query($dati_prenota_modifica,0,'commento');
$d_caparra = risul_query($dati_prenota_modifica,0,'caparra');
$d_caparra_idpr[$id_prenota] = $d_caparra;
$d_met_paga_caparra = risul_query($dati_prenota_modifica,0,'metodo_pagamento');
$d_met_paga_caparra_idpr[$id_prenota] = $d_met_paga_caparra;
$d_commissioni = risul_query($dati_prenota_modifica,0,'commissioni');
$d_commissioni_idpr[$id_prenota] = $d_commissioni;
$d_origine_prenota = risul_query($dati_prenota_modifica,0,'origine');
$d_num_persone = risul_query($dati_prenota_modifica,0,'num_persone');
$d_num_persone_idpr[$id_prenota] = $d_num_persone;
if (!$d_num_persone) $d_num_persone = 0;
$d_tariffesettimanali = risul_query($dati_prenota_modifica,0,'tariffesettimanali');
$d_tariffesettimanali_idpr[$id_prenota] = $d_tariffesettimanali;
$d_costo_agg_tot = (double) 0;
$costo_escludi_perc = (double) 0;
if ($dati_da_anno_prec == "SI") passa_var_a_anno_prec($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo,$dati_prenota_modifica);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
$d_prezzo_costo_agg[$numca] = (double) calcola_prezzo_totale_costo($dati_cap,$numca,$d_id_data_inizio,$d_id_data_fine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],$d_costo_tariffa,$d_tariffesettimanali,($d_costo_tariffa + $d_costo_agg_tot - $d_sconto),$d_caparra,$d_num_persone,$costo_escludi_perc);
$d_costo_agg_tot = (double) $d_costo_agg_tot + $d_prezzo_costo_agg[$numca];
if ($dati_cap[$numca]['escludi_tot_perc'] == "s") $costo_escludi_perc = (double) $costo_escludi_perc + $d_prezzo_costo_agg[$numca];
} # fine for $numca
$d_costo_agg_tot_idpr[$id_prenota] = $d_costo_agg_tot;
$d_prezzo_costo_agg_idpr[$id_prenota] = $d_prezzo_costo_agg;
if ($dati_da_anno_prec == "SI") passa_var_a_anno_corr($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo);
$d_costo_tot = $d_costo_tariffa + $d_costo_agg_tot - $d_sconto;
$d_costo_tot_idpr[$id_prenota] = $d_costo_tot;
$d_pagato = risul_query($dati_prenota_modifica,0,'pagato');
if (!$d_pagato) $d_pagato = 0;
$d_pagato_idpr[$id_prenota] = $d_pagato;
$d_conferma = risul_query($dati_prenota_modifica,0,'conferma');
$d_confermato_idpr[$id_prenota] = substr($d_conferma,0,1);
$d_checkin = risul_query($dati_prenota_modifica,0,'checkin');
$d_checkin_idpr[$id_prenota] = $d_checkin;
$d_checkout = risul_query($dati_prenota_modifica,0,'checkout');
$d_checkout_idpr[$id_prenota] = $d_checkout;
$d_prenota_comp = risul_query($dati_prenota_modifica,0,'idprenota_compagna');
$d_prenota_comp_idpr[$id_prenota] = $d_prenota_comp;
$n_host_inserimento_idpr[$id_prenota] = risul_query($dati_prenota_modifica,0,'hostinserimento');

} # fine for $num_idpr

} # fine function prepara_modifiche_prenotazione




function esegui_modifiche_prenotazione (&$inserire,&$cancellata,$id_prenota_int,$id_prenota_idpr,$num_id_prenota,$id_transazione,$id_sessione,$anno,$id_nuovo_utente_inserimento,$n_stima_checkin,$n_met_paga_caparra,$n_origine_prenota,$n_pagato,$n_confermato,$tipo_commento,$n_commento,$n_cancella_commento,$tableprenota_da_aggiornare,$tipo_sposta,$dati_da_anno_prec,$prenota_in_anno_succ,$tra_anni,$PHPR_TAB_PRE) {
global $id_utente,$priv_mod_checkin,$attiva_regole1_consentite,$regole1_consentite,$attiva_tariffe_consentite,$tariffe_consentite_vett,$priv_mod_date,$priv_ins_periodi_passati,$priv_mod_commento,$priv_mod_commenti_pers,$priv_mod_sconto,$priv_mod_caparra,$priv_mod_pagato,$priv_mod_orig_prenota,$d_commento,$cassa_pagamenti,$nome_utente;
global $dati_ca,$d_id_utente_inserimento_idpr,$d_appartamento_idpr,$d_id_data_inizio_idpr,$d_nome_tariffa_idpr,$d_app_eliminati_costi_idpr,$d_checkin_idpr,$d_checkout_idpr,$d_prenota_comp_idpr,$fineperiodo_orig,$comm_pers_presenti;
global $id_prenota_orig,$tableperiodi_orig,$tableprenota_orig,$tablecostiprenota_orig,$tableperiodi_prec,$tableprenota_prec,$tablecostiprenota_prec,$stile_data,$tipo_n_app;

$pag = "modifica_prenota.php";
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tableutenti = $PHPR_TAB_PRE."utenti";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tablecache = $PHPR_TAB_PRE."cache";
$tablecosti = $PHPR_TAB_PRE."costi".$anno;


$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if ($tableprenota_da_aggiornare == "SI") {
if (@is_file($file_interconnessioni)) {
include($file_interconnessioni);
if (@is_array($ic_present)) {
$dati_transaz = recupera_dati_transazione($id_transazione,$id_sessione,$anno,"SI",$tipo_transaz);
if ($tipo_transaz == "mod_p" and risul_query($dati_transaz,0,'dati_transazione11') == "SI") {
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
$funz_import_reservations("","",$file_interconnessioni,$anno,$PHPR_TAB_PRE,2,$id_utente,$HOSTNAME);
$id_utente = $id_utente_origi;
} # fine if ($ic_present[$interconnection_name] == "SI")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if ($tipo_transaz == "mod_p" and risul_query($dati_transazione,0,'dati_transazione11') == "SI")
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))
$tableprenota_da_aggiornare = "";
} # fine if ($tableprenota_da_aggiornare == "SI")


if ($dati_da_anno_prec == "SI" or $prenota_in_anno_succ) {
if (@is_file($file_interconnessioni)) $tabelle_lock = array($tableprenota_prec,$tableprenota_orig,$tablecostiprenota_prec,$tablecostiprenota_orig,$tablerelinventario,$tabletransazioni,$tablecache);
else $tabelle_lock = array($tableprenota_prec,$tableprenota_orig,$tablecostiprenota_prec,$tablecostiprenota_orig,$tablerelinventario,$tabletransazioni);
$altre_tab_lock = array($tablenometariffe,$tableperiodi_prec,$tableperiodi_orig,$tableappartamenti,$tableregole,$tablepersonalizza,$tableutenti);
} # fine if ($dati_da_anno_prec == "SI" or $prenota_in_anno_succ)
else {
if (@is_file($file_interconnessioni)) $tabelle_lock = array($tableprenota,$tablecostiprenota,$tablerelinventario,$tabletransazioni,$tablecache);
else $tabelle_lock = array($tableprenota,$tablecostiprenota,$tablerelinventario,$tabletransazioni);
$altre_tab_lock = array($tablenometariffe,$tableperiodi,$tableappartamenti,$tableregole,$tablepersonalizza,$tableutenti);
} # fine else if ($dati_da_anno_prec == "SI")
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

$dati_transazione = recupera_dati_transazione($id_transazione,$id_sessione,$anno,"NO",$tipo_transazione);
if ($tipo_transazione != "mod_p") $inserire = "NO";
else if (",".$id_prenota_int."," != risul_query($dati_transazione,0,'dati_transazione1')) $inserire = "NO";

if ($inserire != "NO") {

$d_data_inserimento = risul_query($dati_transazione,0,'dati_transazione2');
$d_host_inserimento = risul_query($dati_transazione,0,'dati_transazione3');
$n_costo_tot_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione4'));
$n_inizioperiodo = risul_query($dati_transazione,0,'dati_transazione5');
$n_fineperiodo = risul_query($dati_transazione,0,'dati_transazione6');
$n_appartamento_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione7'));
$n_assegnazioneapp_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione8'));
$n_lista_app_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione9'));
$n_numpersone_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione10'));
$tableprenota_da_aggiornare = risul_query($dati_transazione,0,'dati_transazione11');
$n_nometipotariffa_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione12'));
$n_costo_tariffa_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione13'));
$costi_aggiuntivi_modificati_int_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione14'));
$costi_aggiuntivi_da_inserire_int_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione15'));
$n_sconto_caparra_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione16'));
unset($n_sconto_idpr);
unset($n_caparra_idpr);
unset($n_commissioni_idpr);
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
if ($tipo_sposta == "fisso" and strcmp($n_appartamento_idpr[$id_prenota],"")) $n_assegnazioneapp_idpr[$id_prenota] = "k";
$n_sconto = explode("#@&",$n_sconto_caparra_idpr[$id_prenota]);
if (strcmp($n_sconto[1],"")) $n_caparra = (double) $n_sconto[1];
else $n_caparra = "";
if (strcmp($n_sconto[2],"")) $n_commissioni = (double) $n_sconto[2];
else $n_commissioni = "";
if (strcmp($n_sconto[0],"")) $n_sconto = (double) $n_sconto[0];
else $n_sconto = "";
$n_sconto_idpr[$id_prenota] = $n_sconto;
$n_caparra_idpr[$id_prenota] = $n_caparra;
$n_commissioni_idpr[$id_prenota] = $n_commissioni;
} # fine for $num_idpr
$n_tariffesettimanali_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione17'));
$id_per_corr_finto = (string) risul_query($dati_transazione,0,'dati_transazione18');
$prenota_comp_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione19'));
$n_app_eliminati_costi_idpr = unserialize(risul_query($dati_transazione,0,'dati_transazione20'));

if (get_magic_quotes_gpc()) {
$n_pagato = stripslashes($n_pagato);
$n_confermato = stripslashes($n_confermato);
} # fine if (get_magic_quotes_gpc())
$n_pagato_idpr = unserialize($n_pagato);
$n_confermato_idpr = unserialize($n_confermato);

esegui_query("delete from $tabletransazioni where  idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");

if ($tableprenota_da_aggiornare) {
$spostamenti = risul_query($dati_transazione,0,'spostamenti');
$spostamenti = explode(",",$spostamenti);
} # fine if ($tableprenota_da_aggiornare)


unset($dati_prenota_idpr);
unset($beniinv_presenti);
unset($num_ripetizioni_costo_mod_idpr);
unset($num_ripetizioni_costo_ins_idpr);
unset($n_tipotariffa_idpr);
$d_data_inserimento_idpr = explode(",",$d_data_inserimento);
$d_host_inserimento_idpr = explode(",",$d_host_inserimento);
if (get_magic_quotes_gpc()) $n_met_paga_caparra = stripslashes($n_met_paga_caparra);
$n_met_paga_caparra = htmlspecialchars($n_met_paga_caparra);

for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$d_checkin = $d_checkin_idpr[$id_prenota];
$d_id_data_inizio = $d_id_data_inizio_idpr[$id_prenota];
$n_costo_tot = $n_costo_tot_idpr[$id_prenota];
$n_appartamento = $n_appartamento_idpr[$id_prenota];
$n_assegnazioneapp = $n_assegnazioneapp_idpr[$id_prenota];
$n_lista_app = $n_lista_app_idpr[$id_prenota];
$n_numpersone = $n_numpersone_idpr[$id_prenota];
$n_nometipotariffa = $n_nometipotariffa_idpr[$id_prenota];
$n_costo_tariffa = $n_costo_tariffa_idpr[$id_prenota];
$costi_aggiuntivi_modificati_int = $costi_aggiuntivi_modificati_int_idpr[$id_prenota];
$costi_aggiuntivi_da_inserire_int = $costi_aggiuntivi_da_inserire_int_idpr[$id_prenota];
$n_sconto = $n_sconto_idpr[$id_prenota];
$n_caparra = $n_caparra_idpr[$id_prenota];
$n_commissioni = $n_commissioni_idpr[$id_prenota];
$n_tariffesettimanali = $n_tariffesettimanali_idpr[$id_prenota];
$prenota_comp = $prenota_comp_idpr[$id_prenota];
$n_pagato = $n_pagato_idpr[$id_prenota];
$n_confermato = $n_confermato_idpr[$id_prenota];
global ${"dati_cap".$id_prenota},${"metodo_pagamento".$id_prenota};
$dati_cap = ${"dati_cap".$id_prenota};
unset($num_costi_presenti);


$dati_prenota_idpr[$id_prenota] = esegui_query("select * from $tableprenota where idprenota = '$id_prenota' and idclienti != '0'");
$num_dati_prenota = numlin_query($dati_prenota_idpr[$id_prenota]);
if ($num_dati_prenota == 0) {
$inserire = "NO";
$cancellata = "SI";
} # fine if ($num_dati_prenota == 0)
else {
$n_data_inserimento = risul_query($dati_prenota_idpr[$id_prenota],0,'datainserimento');
$n_host_inserimento = risul_query($dati_prenota_idpr[$id_prenota],0,'hostinserimento');
if ($n_data_inserimento != $d_data_inserimento_idpr[$num_idpr] or $n_host_inserimento != $d_host_inserimento_idpr[$num_idpr]) {
$inserire = "NO";
$cancellata = "SI";
$inizioperiodo = "";
$fineperiodo = "";
} # fine if ($n_data_inserimento != $d_data_inserimento_idpr[$num_idpr] or ...
if (strcmp($n_appartamento,"")) $appartamento = $n_appartamento;
else $appartamento = risul_query($dati_prenota_idpr[$id_prenota],0,'idappartamenti');
if ($n_numpersone != "") $numpersone = $n_numpersone;
else $numpersone = risul_query($dati_prenota_idpr[$id_prenota],0,'num_persone');
if (!$numpersone) $numpersone = 0;
if ($n_assegnazioneapp != "") $assegnazioneapp = $n_assegnazioneapp;
else $assegnazioneapp = risul_query($dati_prenota_idpr[$id_prenota],0,'assegnazioneapp');
if ($tipo_n_app == "aggiungi" and strcmp($n_appartamento,"") and $assegnazioneapp == "k") {
$n_assegnazioneapp = "c";
$assegnazioneapp = "c";
$n_lista_app = risul_query($dati_prenota_idpr[$id_prenota],0,'app_assegnabili');
if (!strstr(",$n_lista_app,",",$n_appartamento,")) {
if (!strcmp($n_lista_app,"")) $n_lista_app = risul_query($dati_prenota_idpr[$id_prenota],0,'idappartamenti');
$n_lista_app .= ",$n_appartamento";
} # fine if (!strstr(",$n_lista_app,",",$n_appartamento,"))
$n_assegnazioneapp_idpr[$id_prenota] = $n_assegnazioneapp;
$n_lista_app_idpr[$id_prenota] = $n_lista_app;
} # fine if ($tipo_n_app == "aggiungi" and strcmp($n_appartamento,"" and $n_assegnazioneapp == "k")
if ($n_lista_app != "") $lista_app = $n_lista_app;
else $lista_app = risul_query($dati_prenota_idpr[$id_prenota],0,'app_assegnabili');
if ($n_inizioperiodo != "") $inizioperiodo = $n_inizioperiodo;
else $inizioperiodo = risul_query($dati_prenota_idpr[$id_prenota],0,'iddatainizio');
if ($n_fineperiodo != "") $fineperiodo = $n_fineperiodo;
else $fineperiodo = risul_query($dati_prenota_idpr[$id_prenota],0,'iddatafine');
} # fine else if ($num_dati_prenota == 0)

if ($n_stima_checkin) {
$n_stima_checkin = aggslashdb($n_stima_checkin);
if ($priv_mod_checkin != "s") $inserire = "NO";
if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/",$n_stima_checkin)) $inserire = "NO";
else {
$id_stima_checkin = esegui_query("select idperiodi from $tableperiodi where datainizio <= '".substr($n_stima_checkin,0,10)."' and datafine > '".substr($n_stima_checkin,0,10)."' ");
if (numlin_query($id_stima_checkin) == 1) {
$id_stima_checkin = risul_query($id_stima_checkin,0,'idperiodi');
if ($id_stima_checkin > ($fineperiodo + 1)) $inserire = "NO";
if ($id_stima_checkin < $inizioperiodo) $inserire = "NO";
} # fine if (numlin_query($id_stima_checkin) == 1)
else $inserire = "NO";
if ($d_checkin) $inserire = "NO";
} # fine else if (!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/",$n_stima_checkin))
} # fine if ($n_stima_checkin)

$appartamento_esistente = esegui_query("select idappartamenti,maxoccupanti from $tableappartamenti where idappartamenti = '$appartamento'");
if (numlin_query($appartamento_esistente) != 1 and $n_appartamento) $inserire = "NO";
else {
$maxoccupanti = risul_query($appartamento_esistente,0,'maxoccupanti');
if ($maxoccupanti and $numpersone > $maxoccupanti and $n_numpersone) $inserire = "NO";
} # fine else if (numlin_query($appartamento_esistente) != 1)

$appartamento_chiuso = esegui_query("select idregole from $tableregole where iddatainizio <= '$fineperiodo' and iddatafine >= '$inizioperiodo' and app_agenzia = '$appartamento' and motivazione2 = 'x' ");
if (numlin_query($appartamento_chiuso)) $inserire = "NO";

if ($assegnazioneapp == "c" and str_replace(",".$appartamento.",","",",".$lista_app.",") == ",".$lista_app."," and ($n_appartamento or $n_assegnazioneapp or $n_lista_app)) $inserire = "NO";

if ($attiva_regole1_consentite == "s" and ($n_appartamento != "" or $n_assegnazioneapp != "" or $n_lista_app != "")) {
unset($appartameti_in_lista);
if ($assegnazioneapp != "k" and $assegnazioneapp != "c") $inserire = "NO";
if ($assegnazioneapp == "k") $appartameti_in_lista[0] = $appartamento;
if ($assegnazioneapp == "c") $appartameti_in_lista = explode(",",$lista_app);
for ($n_lista = 0 ; $n_lista < count($appartameti_in_lista) ; $n_lista++) {
$appartamento_lista = $appartameti_in_lista[$n_lista];
$motivazioni_regola1 = esegui_query("select motivazione,iddatainizio,iddatafine from $tableregole where iddatainizio <= '$fineperiodo' and iddatafine >= '$inizioperiodo' and app_agenzia = '$appartamento_lista' and (motivazione2 != 'x' or motivazione2 is NULL) order by iddatainizio");
if (numlin_query($motivazioni_regola1) == 0) $inserire = "NO";
else {
unset($motivazioni_consentite);
for ($num2 = 0 ; $num2 < count($regole1_consentite) ; $num2++) $motivazioni_consentite[$regole1_consentite[$num2]] = "SI";
$iddatainizio_regole_tot = risul_query($motivazioni_regola1,0,'iddatainizio');
$iddatafine_regole_tot = risul_query($motivazioni_regola1,0,'iddatafine');
$motivazione = risul_query($motivazioni_regola1,0,'motivazione');
if (!$motivazione) $motivazione = " ";
if (!$motivazioni_consentite[$motivazione]) $inserire = "NO";
for ($num2 = 1 ; $num2 < numlin_query($motivazioni_regola1) ; $num2++) {
$motivazione = risul_query($motivazioni_regola1,$num2,'motivazione');
if (!$motivazione) $motivazione = " ";
if (!$motivazioni_consentite[$motivazione]) $inserire = "NO";
$iddatainizio_regola = risul_query($motivazioni_regola1,$num2,'iddatainizio');
if ($iddatainizio_regola == ($iddatafine_regole_tot + 1)) $iddatafine_regole_tot = risul_query($motivazioni_regola1,$num2,'iddatafine');
else $inserire = "NO";
} # fine for $num2
if ($iddatainizio_regole_tot > $inizioperiodo or $iddatafine_regole_tot < $fineperiodo) $inserire = "NO";
} # fine else if (numlin_query($motivazioni_regola1) == 0)
} # fine for $n_lista
} # fine if ($attiva_regole1_consentite == "s" and...

if ($dati_da_anno_prec == "SI") passa_var_a_anno_prec($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo,$dati_prenota_modifica);
if ($n_nometipotariffa != "") {
$trovata_tariffa = "NO";
$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
$tariffa = "tariffa".$numtariffa;
$nometariffa = $dati_tariffe[$tariffa]['nome'];
if ($n_nometipotariffa == $tariffa or $n_nometipotariffa == $nometariffa) {
$trovata_tariffa = "SI";
$n_tipotariffa = $tariffa;
$n_tipotariffa_idpr[$id_prenota] = $n_tipotariffa;
} # fine if ($n_nometipotariffa == $tariffa or $n_nometipotariffa == $nometariffa)
} # fine for $numtariffa
if (($attiva_tariffe_consentite == "s" and $tariffe_consentite_vett[substr($n_tipotariffa,7)] != "SI" and $n_nometipotariffa != $d_nome_tariffa_idpr[$id_prenota]) or $trovata_tariffa == "NO") $inserire = "NO";
for ($num2 = $inizioperiodo; $num2 <= $fineperiodo; $num2++) {
$rigasettimana = esegui_query("select * from $tableperiodi where idperiodi = '$num2' ");
$esistetariffa = risul_query($rigasettimana,0,$n_tipotariffa);
$esistetariffap = risul_query($rigasettimana,0,$n_tipotariffa."p");
if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and (!strcmp($esistetariffap,"") or $esistetariffap < 0)) $inserire = "NO";
if ($n_nometipotariffa != $d_nome_tariffa_idpr[$id_prenota] and $dati_tariffe[$n_tipotariffa]['chiusa'][$num2]) $inserire = "NO";
} # fine for $num2
} # fine if ($n_nometipotariffa != "")

if ($priv_mod_date != "s" and ($n_inizioperiodo != "" or $n_fineperiodo != "")) $inserire = "NO";
if ($priv_ins_periodi_passati != "s" and $inizioperiodo < $d_id_data_inizio) {
$id_periodo_corrente = calcola_id_periodo_corrente ($anno);
if ($id_periodo_corrente >= $inizioperiodo) $inserire = "NO";
} # fine if ($priv_ins_periodi_passati != "s" and $inizioperiodo < $d_id_data_inizio)

#if ($n_numpersone != "" and $priv_mod_num_persone != "s") $inserire = "NO";
if ($n_numpersone and controlla_num_pos($n_numpersone) != "SI") $inserire = "NO";

if ($priv_mod_commenti_pers != "s" and $tipo_commento != "checkin" and $tipo_commento != "checkout") $tipo_commento = "";
if ($priv_mod_commento != "s" and !strcmp($tipo_commento,"")) $n_commento = "";
if ($priv_mod_commento != "s" and ((strcmp($n_commento,"") and (!strcmp($tipo_commento,"") or $tipo_commento == "checkin" or $tipo_commento == "checkout")) or $n_cancella_commento)) $inserire = "NO";
if ($priv_mod_commenti_pers == "s" and strcmp($tipo_commento,"") and !$comm_pers_presenti[$tipo_commento] and $tipo_commento != "checkin" and $tipo_commento != "checkout") $inserire = "NO";

if (strcmp($n_sconto,"") and ($priv_mod_sconto != "s" or controlla_soldi($n_sconto) == "NO")) $inserire = "NO";

if (strcmp($n_caparra,"")) {
if ($priv_mod_caparra != "s" or controlla_soldi($n_caparra,"SI") == "NO") $inserire = "NO";
if ($n_caparra > $n_costo_tot) $inserire = "NO";
} # fine if (strcmp($n_caparra,""))

if (strcmp($n_commissioni,"") and ($priv_mod_caparra != "s" or controlla_soldi($n_commissioni,"SI") == "NO")) $inserire = "NO";

if ($costi_aggiuntivi_modificati_int) {
$costi_aggiuntivi_modificati = explode("#@&",$costi_aggiuntivi_modificati_int);
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
$num_costo_mod = "NO";
for ($num2 = 0 ; $num2 < count($costi_aggiuntivi_modificati) ; $num2++) {
$costo_agg_modificato = explode("#?&",$costi_aggiuntivi_modificati[$num2]);
if ($costo_agg_modificato[0] == $dati_cap[$numca]['id']) $num_costo_mod = $num2;
} # fine for $num2
$costo_agg_modificato = explode("#?&",$costi_aggiuntivi_modificati[$num_costo_mod]);
if ($costo_agg_modificato[1] != "cancella") {
$num_costo = $dati_ca['id'][$dati_cap[$numca]['idntariffe']];
if ($dati_ca[$num_costo]['id'] == $dati_cap[$numca]['idntariffe'] and $dati_cap[$numca]['nome'] == $dati_ca[$num_costo]['nome'] and $dati_ca[$num_costo]['numlimite']) $num_limite = (string) $dati_ca[$num_costo]['numlimite'];
else $num_limite = (string) 0;
if ((string) $num_costo_mod != (string) "NO") {
$settimane_costo = $costo_agg_modificato[1];
$moltiplica = $costo_agg_modificato[2];
} # fine if ((string) $num_costo_mod != (string) "NO")
else {
$settimane_costo = $dati_cap[$numca]['settimane'];
$moltiplica = $dati_cap[$numca]['moltiplica_costo'];
} # fine else if ((string) $num_costo_mod != (string) "NO")
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_cap,$numca,$num_costi_presenti,$inizioperiodo,$fineperiodo,$settimane_costo,$moltiplica,$num_limite,$tra_anni) == "NO") $inserire = "NO";
if (str_replace(",$appartamento,","",",".$dati_cap[$numca]['appincompatibili'].",") != ",".$dati_cap[$numca]['appincompatibili'].",") $inserire = "NO";
} # fine if ($costo_agg_modificato[1] != "cancella")
if ($dati_cap[$numca]['tipo_beniinv']) {
if ($costo_agg_modificato[1] != "cancella") $num_ripetizioni_costo = "";
else $num_ripetizioni_costo = "0";
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_cap,$numca,$beniinv_presenti,$num_ripetizioni_costo,"SI",$inizioperiodo,$fineperiodo,$settimane_costo,$moltiplica,$appartamento);
if ($risul != "SI") $inserire = "NO";
if ($num_ripetizioni_costo != $dati_cap[$numca]['beniinv_ripeti']) {
$beniinv_vett = explode(";",$dati_cap[$numca]['beniinv_orig']);
$num_ripetizioni_costo_mod_idpr[$id_prenota][$num_costo_mod] = $num_ripetizioni_costo.substr($dati_cap[$numca]['beniinv_orig'],strlen($beniinv_vett[0]));;
} # fine if ($num_ripetizioni_costo != $dati_cap[$numca]['beniinv_ripeti'])
} # fine if ($dati_cap[$numca]['tipo_beniinv'])
} # fine for $numca
} # fine if ($costi_aggiuntivi_modificati_int)

if ($costi_aggiuntivi_da_inserire_int) {
$costi_aggiuntivi_da_inserire = explode("#@&",$costi_aggiuntivi_da_inserire_int);
for ($num2 = 0 ; $num2 < count($costi_aggiuntivi_da_inserire) ; $num2++) {
$costo_agg_da_inserire = explode("#?&",$costi_aggiuntivi_da_inserire[$num2]);
$num_costo = $dati_ca['id'][$costo_agg_da_inserire[9]];
if ($costo_agg_da_inserire[9] != $dati_ca[$num_costo]['id'] and $costo_agg_da_inserire[9] != "-1") $inserire = "NO";
elseif (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti,$inizioperiodo,$fineperiodo,$costo_agg_da_inserire[6],$costo_agg_da_inserire[7],"",$tra_anni) == "NO") $inserire = "NO";
if (str_replace(",$appartamento,","",",".$dati_ca[$num_costo]['appincompatibili'].",") != ",".$dati_ca[$num_costo]['appincompatibili'].",") $inserire = "NO";
if ($dati_ca[$num_costo]['tipo_beniinv']) {
$num_ripetizioni_costo_ins_idpr[$id_prenota][$num2] = "";
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti,$num_ripetizioni_costo_ins_idpr[$id_prenota][$num2],"SI",$inizioperiodo,$fineperiodo,$costo_agg_da_inserire[6],$costo_agg_da_inserire[7],$appartamento);
if ($risul != "SI") $inserire = "NO";
} # fine if ($dati_ca[$num_costo]['tipo_beniinv'])
} # fine for $num2
} # fine if ($costi_aggiuntivi_da_inserire_int)
if ($dati_da_anno_prec == "SI") passa_var_a_anno_corr($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo);

if ($priv_mod_caparra != "s" and $n_met_paga_caparra) $inserire = "NO";
if ($n_met_paga_caparra or ${"metodo_pagamento".$id_prenota}) {
if (get_magic_quotes_gpc()) ${"metodo_pagamento".$id_prenota} = stripslashes(${"metodo_pagamento".$id_prenota});
${"metodo_pagamento".$id_prenota} = htmlspecialchars(${"metodo_pagamento".$id_prenota});
$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '$id_utente'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
$metodi_pagamento = explode(",",$metodi_pagamento);
if ($n_met_paga_caparra) {
$metodo_trovato = "NO";
for ($num1 = 0 ; $num1 < count($metodi_pagamento) ; $num1++) if ($metodi_pagamento[$num1] == $n_met_paga_caparra) $metodo_trovato = "SI";
if ($metodo_trovato == "NO") $inserire = "NO";
} # fine if ($n_met_paga_caparra)
if (${"metodo_pagamento".$id_prenota}) {
$metodo_trovato = "NO";
for ($num1 = 0 ; $num1 < count($metodi_pagamento) ; $num1++) if ($metodi_pagamento[$num1] == ${"metodo_pagamento".$id_prenota}) $metodo_trovato = "SI";
if ($metodo_trovato == "NO") $inserire = "NO";
} # fine if (${"metodo_pagamento".$id_prenota})
} # fine if ($n_met_paga_caparra or ${"metodo_pagamento".$id_prenota})

if ($priv_mod_pagato == "n" and $n_confermato != "") $inserire = "NO";
if ($n_confermato and $n_confermato != "S" and $n_confermato != "N") $inserire = "NO";

if ($priv_mod_pagato != "s" and $n_pagato != "") $inserire = "NO";
if ($n_pagato and controlla_soldi($n_pagato,"SI") == "NO" or $n_pagato > $n_costo_tot) $inserire = "NO";

if ($inserire == "NO") break;
} # fine for $num_idpr


if ($priv_mod_orig_prenota != "s" and $n_origine_prenota) $inserire = "NO";
if ($n_origine_prenota) {
if (get_magic_quotes_gpc()) $n_origine_prenota = stripslashes($n_origine_prenota);
$n_origine_prenota = htmlspecialchars($n_origine_prenota);
$origini_prenota = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'origini_prenota' and idutente = '$id_utente'");
$origini_prenota = risul_query($origini_prenota,0,'valpersonalizza');
$origini_prenota = explode(",",$origini_prenota);
$origine_trovata = "NO";
for ($num1 = 0 ; $num1 < count($origini_prenota) ; $num1++) if ($origini_prenota[$num1] == $n_origine_prenota) $origine_trovata = "SI";
if ($origine_trovata == "NO") $inserire = "NO";
} # fine if ($n_origine_prenota)

if ($id_nuovo_utente_inserimento != "" and $id_utente == 1) {
$id_nuovo_utente_inserimento = aggslashdb($id_nuovo_utente_inserimento);
$verifica_utente = esegui_query("select * from $tableutenti where idutenti = '$id_nuovo_utente_inserimento'");
if (numlin_query($verifica_utente) != 1) $inserire = "NO";
} # fine if ($id_nuovo_utente_inserimento != "" and $id_utente == 1)

if ($n_commento != "") {
if (get_magic_quotes_gpc()) $n_commento = stripslashes($n_commento);
$n_commento = htmlspecialchars($n_commento);
} # fine if ($n_commento != "")

} # fine if ($inserire != "NO")


if ($inserire != "NO") {

# funzione: se ritorna $prenota_comp vuota sono tutte vicine
function controlla_prenota_vicine (&$prenota_comp,$dati_app,$app_prenota_id) {
if ($prenota_comp) {
$prenota_comp_vett = explode(",",$prenota_comp);
$num_prenota_comp = count($prenota_comp_vett);
$prenota_comp = substr(str_replace(",".$prenota_comp_vett[0].",",",",",$prenota_comp,"),1,-1);
$app_corr = $app_prenota_id[$prenota_comp_vett[0]];
$num_vic = 0;
for ($num1 = 1 ; $num1 < $num_prenota_comp ; $num1++) {
$app_vicini = $dati_app["vicini"][$app_prenota_id[$prenota_comp_vett[$num1]]];
if (str_replace(",$app_corr,","",",$app_vicini,") != ",$app_vicini,") {
$prenota_comp = substr(str_replace(",".$prenota_comp_vett[$num1].",",",",",$prenota_comp,"),1,-1);
$pren_vicina[$num_vic] = $prenota_comp_vett[$num1];
$num_vic++;
} # fine (str_replace(",$app_corr,","",",$app_vicini,") != ",$app_vicini,")
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_vic ; $num1++) {
if (!$prenota_comp) break;
$prenota_comp = $pren_vicina[$num1].",".$prenota_comp;
controlla_prenota_vicine($prenota_comp,$dati_app,$app_prenota_id);
} # fine for $num1
} # fine if ($prenota_comp)
} # fine function controlla_prenota_vicine

# aggiorno le posizioni e faccio i controlli.
if ($tableprenota_da_aggiornare) {
if (!function_exists("tab_a_var")) include("./includes/liberasettimane.php");
unset($limiti_var);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($profondita);
if ($id_per_corr_finto != "") $limiti_var['idperiodocorrente'] = $id_per_corr_finto;
$limiti_var['n_ini'] = 0;
$max_periodo = esegui_query("select max(idperiodi) from $tableperiodi");
$max_periodo = risul_query($max_periodo,0,0);
$limiti_var['n_fine'] = $max_periodo;
$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$limiti_var['lim_prenota_temp'] = aggslashdb(date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60))));
esegui_query("delete from $tableprenota where idclienti = '0' and datainserimento < '".$limiti_var['lim_prenota_temp']."'","",1);
$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
if (numlin_query($max_prenota) != 0) $tot_prenota = risul_query($max_prenota,0,0);
else $tot_prenota = 0;
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
# Sposto gli appartamenti presenti in $spostamenti se possibile
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
for ($num1 = $inizio_prenota_id[$id_prenota] ; $num1 <= $fine_prenota_id[$id_prenota] ; $num1++) {
$prenota_in_app_sett[$app_prenota_id[$id_prenota]][$num1] = "";
} # fine for $num1
} # fine for $num_idpr
$num_spostamenti = count($spostamenti);
for ($num1 = 0 ; $num1 < $num_spostamenti ; $num1++) {
$idprenota_cambiata = $spostamenti[$num1];
$app_cambiato = $spostamenti[($num1 + 1)];
$applicare_cambio[$num1] = "NO";
if ($inizio_prenota_id[$idprenota_cambiata]) {
$applicare_cambio[$num1] = "SI";
if (!$app_assegnabili_id[$idprenota_cambiata]) $applicare_cambio[$num1] = "NO";
if ($app_assegnabili_id[$idprenota_cambiata] and $app_assegnabili_id[$idprenota_cambiata] != "v") {
$lista_app_assegnabili = ",".$app_assegnabili_id[$idprenota_cambiata].",";
if (str_replace(",".$app_cambiato.",","",$lista_app_assegnabili) == $lista_app_assegnabili) $applicare_cambio[$num1] = "NO";
} # fine if ($app_assegnabili_id[$idprenota_cambiata] and...
if (str_replace(",".$app_cambiato.",","",$dati_app['lista']) == $dati_app['lista']) $applicare_cambio[$num1] = "NO";
if ($applicare_cambio[$num1] == "SI") {
for ($num2 = $inizio_prenota_id[$idprenota_cambiata] ; $num2 <= $fine_prenota_id[$idprenota_cambiata] ; $num2++) {
$prenota_in_app_sett[$app_prenota_id[$idprenota_cambiata]][$num2] = "";
} # fine for $num2
$app_prenota_id[$idprenota_cambiata] = $app_cambiato;
} # fine if ($applicare_cambio[$num1] == "SI")
} # fine if ($inizio_prenota_id[$idprenota_cambiata])
$num1++;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_spostamenti ; $num1++) {
$idprenota_cambiata = $spostamenti[$num1];
if ($applicare_cambio[$num1] == "SI") {
for ($num2 = $inizio_prenota_id[$idprenota_cambiata] ; $num2 <= $fine_prenota_id[$idprenota_cambiata] ; $num2++) {
if ($prenota_in_app_sett[$app_prenota_id[$idprenota_cambiata]][$num2]) $inserire = "NO";
else $prenota_in_app_sett[$app_prenota_id[$idprenota_cambiata]][$num2] = $idprenota_cambiata;
} # fine for $num2
if ($inserire == "NO") break;
} # fine if ($applicare_cambio[$num1] == "SI")
$num1++;
} # fine for $num1

# Aggiorno la prenotazione e faccio i primi controlli sulla prenotazione modificata
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$n_appartamento = $n_appartamento_idpr[$id_prenota];
$n_assegnazioneapp = $n_assegnazioneapp_idpr[$id_prenota];
$n_lista_app = $n_lista_app_idpr[$id_prenota];
$n_numpersone = $n_numpersone_idpr[$id_prenota];
if ($n_appartamento != "") $app_prenota_id[$id_prenota] = $n_appartamento;
if ($n_inizioperiodo != "") $inizio_prenota_id[$id_prenota] = $n_inizioperiodo;
if ($n_fineperiodo != "") $fine_prenota_id[$id_prenota] = $n_fineperiodo;
for ($num1 = $inizio_prenota_id[$id_prenota] ; $num1 <= $fine_prenota_id[$id_prenota] ; $num1++) {
if ($prenota_in_app_sett[$app_prenota_id[$id_prenota]][$num1]) $inserire = "NO";
else $prenota_in_app_sett[$app_prenota_id[$id_prenota]][$num1] = $id_prenota;
} # fine for $num1
if ($n_assegnazioneapp == "v") $app_assegnabili_id[$id_prenota] = $dati_app['lista'];
if ($n_assegnazioneapp == "k") $app_assegnabili_id[$id_prenota] = "";
if ($n_assegnazioneapp == "c" and $n_lista_app) $app_assegnabili_id[$id_prenota] = ",".$n_lista_app.",";
$assegnazione_app = risul_query($dati_prenota_idpr[$id_prenota],0,'assegnazioneapp');
if (!$n_assegnazioneapp and $assegnazione_app == "c" and $n_lista_app) $app_assegnabili_id[$id_prenota] = ",".$n_lista_app.",";
if ($n_numpersone) $num_persone_prenota = $n_numpersone;
else $num_persone_prenota = risul_query($dati_prenota_idpr[$id_prenota],0,'num_persone');
if ($num_persone_prenota and $dati_app['maxocc'][$app_prenota_id[$id_prenota]] and $dati_app['maxocc'][$app_prenota_id[$id_prenota]] < $num_persone_prenota) $inserire = "NO";
if (str_replace(",".$app_prenota_id[$id_prenota].",","",$dati_app['lista']) == $dati_app['lista']) $inserire = "NO";
if ($app_assegnabili_id[$id_prenota] and $app_assegnabili_id[$id_prenota] != "v") {
$lista_app_assegnabili = ",".$app_assegnabili_id[$id_prenota].",";
if (str_replace(",".$app_prenota_id[$id_prenota].",","",$lista_app_assegnabili) == $lista_app_assegnabili) $inserire = "NO";
} # fine if ($app_assegnabili_id[$id_prenota] and...
if (str_replace(",".$app_prenota_id[$id_prenota].",","",$dati_app['lista']) == $dati_app['lista']) $inserire = "NO";
} # fine for $num_idpr

for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$d_appartamento = $d_appartamento_idpr[$id_prenota];
$d_checkin = $d_checkin_idpr[$id_prenota];
$d_checkout = $d_checkout_idpr[$id_prenota];
$d_prenota_comp = $d_prenota_comp_idpr[$id_prenota];
$prenota_comp = $prenota_comp_idpr[$id_prenota];
$prenota_comp_copia = $prenota_comp;
if ($prenota_comp_copia != "") $prenota_comp_copia = $id_prenota.",".$prenota_comp_copia;
controlla_prenota_vicine($prenota_comp_copia,$dati_app,$app_prenota_id);
if ($prenota_comp_copia) $inserire = "NO";
$prenota_comp_control = $prenota_comp;
if ($d_prenota_comp and $prenota_comp_control) $prenota_comp_control .= ",";
$prenota_comp_control .= $d_prenota_comp;
for ($num1 = 0 ; $num1 < $num_spostamenti ; $num1++) {
$idprenota_cambiata = $spostamenti[$num1];
if ($app_assegnabili_id[0][$idprenota_cambiata]) {
if (str_replace(",$idprenota_cambiata,","",",$prenota_comp_control,") == ",$prenota_comp_control,") {
$prenota_comp_copia = $app_assegnabili_id[0][$idprenota_cambiata];
if ($prenota_comp_copia != "") $prenota_comp_copia = $idprenota_cambiata.",".$prenota_comp_copia;
controlla_prenota_vicine($prenota_comp_copia,$dati_app,$app_prenota_id);
if ($prenota_comp_copia) $inserire = "NO";
else $prenota_comp_control .= ",".$app_assegnabili_id[0][$idprenota_cambiata];
if (substr($prenota_comp_control,0,1) == ",") $prenota_comp_control = substr($prenota_comp_control,1);
} # fine if (str_replace(",$idprenota_cambiata,","",",$prenota_comp_control,") == ",$prenota_comp_control,")
} # fine if ($app_assegnabili_id[0][$idprenota_cambiata])
} # fine for $num1
if ($d_checkin and !$d_checkout and $app_prenota_id[$id_prenota] != $d_appartamento) {
$checkin_presente = esegui_query("select idprenota from $tableprenota where idappartamenti = '".aggslashdb($app_prenota_id[$id_prenota])."' and checkin is not NULL and checkout is NULL");
if (numlin_query($checkin_presente) >= 1) {
$inserire = "NO";
echo mex("Non si è potuto spostare la prenotazione per la registrazione dell'entrata",$pag).".<br>";
} # fine if (numlin_query($checkin_presente) >= 1)
} # fine if ($d_checkin and !$d_checkout and...
} # fine for $num_idpr

} # fine if ($tableprenota_da_aggiornare)

} # fine if ($inserire != "NO")

if ($inserire != "NO" and $tableprenota_da_aggiornare) {
for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
if (strcmp($app_prenota_id[$id_prenota],"") and !strcmp($app_orig_prenota_id[$id_prenota],"")) $app_orig_prenota_id[$id_prenota] = $app_prenota_id[$id_prenota]."x";
} # fine for $num_idpr
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $inserire = "NO";
} # fine if ($inserire != "NO" and $tableprenota_da_aggiornare)

if ($inserire == "NO") {
echo mex("La prenotazione",$pag)." $id_prenota ".mex("<b>non è stata modificata</b> perchè la tabella prenotazioni è cambiata nel frattempo",$pag).".<br>";
unlock_tabelle($tabelle_lock);
} # fine if ($inserire == "NO")

else {



for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$d_id_utente_inserimento = $d_id_utente_inserimento_idpr[$id_prenota];
$d_app_eliminati_costi = $d_app_eliminati_costi_idpr[$id_prenota];
$d_prenota_comp = $d_prenota_comp_idpr[$id_prenota];
$n_costo_tot = $n_costo_tot_idpr[$id_prenota];
$n_appartamento = $n_appartamento_idpr[$id_prenota];
$n_assegnazioneapp = $n_assegnazioneapp_idpr[$id_prenota];
$n_lista_app = $n_lista_app_idpr[$id_prenota];
$n_app_eliminati_costi = $n_app_eliminati_costi_idpr[$id_prenota];
$n_numpersone = $n_numpersone_idpr[$id_prenota];
$n_nometipotariffa = $n_nometipotariffa_idpr[$id_prenota];
$n_costo_tariffa = $n_costo_tariffa_idpr[$id_prenota];
$costi_aggiuntivi_modificati_int = $costi_aggiuntivi_modificati_int_idpr[$id_prenota];
$costi_aggiuntivi_da_inserire_int = $costi_aggiuntivi_da_inserire_int_idpr[$id_prenota];
$n_sconto = $n_sconto_idpr[$id_prenota];
$n_caparra = $n_caparra_idpr[$id_prenota];
$n_commissioni = $n_commissioni_idpr[$id_prenota];
$n_tariffesettimanali = $n_tariffesettimanali_idpr[$id_prenota];
$prenota_comp = $prenota_comp_idpr[$id_prenota];
$n_pagato = $n_pagato_idpr[$id_prenota];
$n_confermato = $n_confermato_idpr[$id_prenota];


$id_utente_inserimento = $d_id_utente_inserimento;
if ($id_nuovo_utente_inserimento != "" and $id_utente == 1) {
esegui_query("update $tableprenota set utente_inserimento = '$id_nuovo_utente_inserimento' where idprenota = '$id_prenota' ");
esegui_query("update $tablecostiprenota set utente_inserimento = '$id_nuovo_utente_inserimento' where idprenota = '$id_prenota' ");
$id_utente_inserimento = $id_nuovo_utente_inserimento;
if ($prenota_in_anno_succ) {
esegui_query("update $tableprenota_orig set utente_inserimento = '$id_nuovo_utente_inserimento' where idprenota = '$id_prenota_orig' ");
esegui_query("update $tablecostiprenota_orig set utente_inserimento = '$id_nuovo_utente_inserimento' where idprenota = '$id_prenota_orig' ");
} # fine if ($prenota_in_anno_succ)
} # fine if ($id_nuovo_utente_inserimento != "" and $id_utente == 1)

if ($n_inizioperiodo != "") {
esegui_query("update $tableprenota set iddatainizio = '$n_inizioperiodo' where idprenota = '$id_prenota' ");
} # fine if ($n_inizioperiodo != "")

if ($n_fineperiodo != "") {
esegui_query("update $tableprenota set iddatafine = '$n_fineperiodo' where idprenota = '$id_prenota' ");
if ($dati_da_anno_prec == "SI") {
passa_var_a_anno_prec($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo,$dati_prenota_modifica);
esegui_query("update $tableprenota set iddatafine = '$n_fineperiodo' where idprenota = '$id_prenota' ");
passa_var_a_anno_corr($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo);
} # fine if ($dati_da_anno_prec == "SI")
if ($prenota_in_anno_succ) {
$n_data_fine = esegui_query("select datafine from $tableperiodi where idperiodi = '$n_fineperiodo'");
$n_data_fine = risul_query($n_data_fine,0,'datafine');
$data_succ_esistente = esegui_query("select idperiodi from $tableperiodi_orig where datafine <= '$n_data_fine'");
if (numlin_query($data_succ_esistente) < 1) {
esegui_query("delete from $tableprenota_orig where idprenota = '$id_prenota_orig'");
esegui_query("delete from $tablecostiprenota_orig where idprenota = '$id_prenota_orig'");
} # fine if (numlin_query($data_succ_esistente) < 1)
} # fine if ($prenota_in_anno_succ)
} # fine if ($n_fineperiodo != "")

if ($n_stima_checkin) {
esegui_query("update $tableprenota set checkout = '$n_stima_checkin' where idprenota = '$id_prenota' ");
} # fine if ($n_stima_checkin)

#if ($n_appartamento != "") {
#esegui_query("update $tableprenota set idappartamenti = '$n_appartamento' where idprenota = '$id_prenota' ");
#} # fine if ($n_appartamento != "")

if ($n_assegnazioneapp != "") {
esegui_query("update $tableprenota set assegnazioneapp = '$n_assegnazioneapp' where idprenota = '$id_prenota' ");
if ($n_assegnazioneapp == "v" or $n_assegnazioneapp == "k") {
esegui_query("update $tableprenota set app_assegnabili = '' where idprenota = '$id_prenota' ");
} # fine if ($n_assegnazioneapp == "v" ...)
} # fine if ($n_assegnazioneapp != "")

if ($n_lista_app != "") {
esegui_query("update $tableprenota set app_assegnabili = '$n_lista_app' where idprenota = '$id_prenota' ");
} # fine if ($n_lista_app != "")

if ($d_prenota_comp != $prenota_comp) {
esegui_query("update $tableprenota set idprenota_compagna = '$prenota_comp' where idprenota = '$id_prenota' ");
if ($d_prenota_comp) {
$d_prenota_comp_vett = explode(",",$d_prenota_comp);
$num_d_prenota_comp = count($d_prenota_comp_vett);
for ($num1 = 0 ; $num1 < $num_d_prenota_comp ; $num1++) {
$d_pren_comp = aggslashdb($d_prenota_comp_vett[$num1]);
if (str_replace(",$d_pren_comp,","",",$prenota_comp,") == ",$prenota_comp,") esegui_query("update $tableprenota set idprenota_compagna = '' where idprenota = '$d_pren_comp' ");
} # fine for $num1
} # fine if ($d_prenota_comp)
if ($prenota_comp) {
$prenota_comp_vett = explode(",",$prenota_comp);
$num_prenota_comp = count($prenota_comp_vett);
for ($num1 = 0 ; $num1 < $num_prenota_comp ; $num1++) {
$pren_comp = aggslashdb($prenota_comp_vett[$num1]);
$prenota_comp2 = substr(str_replace(",$pren_comp,",",",",$prenota_comp,"),1).$id_prenota;
esegui_query("update $tableprenota set idprenota_compagna = '$prenota_comp2' where idprenota = '$pren_comp' ");
} # fine for $num1
} # fine if ($prenota_comp)
} # fine if ($d_prenota_comp != $prenota_comp)

if ($dati_da_anno_prec == "SI") passa_var_a_anno_prec($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo,$dati_prenota_modifica);

if ($n_numpersone != "") {
esegui_query("update $tableprenota set num_persone = '$n_numpersone' where idprenota = '$id_prenota' ");
} # fine if ($n_numpersone != "")

if ($n_nometipotariffa != "") {
if (substr($n_costo_tariffa,-1) == "p") $n_costo_tariffa = substr($n_costo_tariffa,0,-1)."#@&p";
$n_tariffa = aggslashdb($n_nometipotariffa)."#@&".$n_costo_tariffa;
esegui_query("update $tableprenota set tariffa = '$n_tariffa' where idprenota = '$id_prenota' ");
if (!strcmp($dati_tariffe[$n_tipotariffa_idpr[$id_prenota]]['tasse_percent'],"")) esegui_query("update $tableprenota set tasseperc = NULL where idprenota = '$id_prenota' ");
else esegui_query("update $tableprenota set tasseperc = '".$dati_tariffe[$n_tipotariffa_idpr[$id_prenota]]['tasse_percent']."' where idprenota = '$id_prenota' ");
} # fine if ($n_nometipotariffa != "")

if ($n_tariffesettimanali != "") {
$n_tariffesettimanali = aggslashdb($n_tariffesettimanali);
esegui_query("update $tableprenota set tariffesettimanali = '$n_tariffesettimanali' where idprenota = '$id_prenota' ");
} # fine if ($n_tariffesettimanali != "")

if ($costi_aggiuntivi_modificati_int) {
$costi_aggiuntivi_modificati = explode("#@&",$costi_aggiuntivi_modificati_int);
for ($num2 = 0 ; $num2 < count($costi_aggiuntivi_modificati) ; $num2++) {
$costo_agg_modificato = explode("#?&",$costi_aggiuntivi_modificati[$num2]);
if ($costo_agg_modificato[1] == "cancella") esegui_query("delete from $tablecostiprenota where idcostiprenota = '".$costo_agg_modificato[0]."' and idprenota = '$id_prenota' ");
else {
esegui_query("update $tablecostiprenota set settimane = '".$costo_agg_modificato[1]."', moltiplica = '".$costo_agg_modificato[2]."' where idcostiprenota = '".$costo_agg_modificato[0]."' and idprenota = '$id_prenota' ");
if ($num_ripetizioni_costo_mod_idpr[$id_prenota][$num2]) esegui_query("update $tablecostiprenota set varbeniinv = '".$num_ripetizioni_costo_mod_idpr[$id_prenota][$num2]."' where idcostiprenota = '".$costo_agg_modificato[0]."' and idprenota = '$id_prenota' ");
} # fine else if ($costo_agg_modificato[1] == "cancella")
} # fine for $num2
} # fine if ($costi_aggiuntivi_modificati_int)

if ($costi_aggiuntivi_da_inserire_int) {
$costi_aggiuntivi_da_inserire = explode("#@&",$costi_aggiuntivi_da_inserire_int);
for ($num2 = 0 ; $num2 < count($costi_aggiuntivi_da_inserire) ; $num2++) {
$costo_agg_da_inserire = explode("#?&",$costi_aggiuntivi_da_inserire[$num2]);
$num_costo = $dati_ca['id'][$costo_agg_da_inserire[9]];
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$idcostiprenota = esegui_query("select max(idcostiprenota) from $tablecostiprenota");
$idcostiprenota = risul_query($idcostiprenota,0,0) + 1;
if ($costo_agg_da_inserire[15]) $costo_agg_da_inserire[15] = $num_ripetizioni_costo_ins_idpr[$id_prenota][$num2].";".$costo_agg_da_inserire[15];
esegui_query("insert into $tablecostiprenota (idcostiprenota,idprenota,tipo,nome,valore,associasett,settimane,moltiplica,letto,idntariffe,varmoltiplica,varnumsett,varperiodipermessi,vartariffeassociate,vartariffeincomp,varbeniinv,varappincompatibili,variazione,categoria,datainserimento,hostinserimento,utente_inserimento) values ('$idcostiprenota','$id_prenota','".$costo_agg_da_inserire[0]."','".aggslashdb($costo_agg_da_inserire[1])."','".$costo_agg_da_inserire[2]."','".$costo_agg_da_inserire[5]."','".$costo_agg_da_inserire[6]."','".$costo_agg_da_inserire[7]."','".$costo_agg_da_inserire[8]."','".$costo_agg_da_inserire[9]."','".$costo_agg_da_inserire[10]."','".$costo_agg_da_inserire[11]."','".$costo_agg_da_inserire[12]."','".$costo_agg_da_inserire[13]."','".$costo_agg_da_inserire[14]."','".$costo_agg_da_inserire[15]."','".$costo_agg_da_inserire[16]."','".$costo_agg_da_inserire[17]."','".$costo_agg_da_inserire[18]."','$datainserimento','$HOSTNAME','$id_utente')");
if (substr($costo_agg_da_inserire[0],1,1) != "f") esegui_query("update $tablecostiprenota set valore_perc = '".$costo_agg_da_inserire[3]."', arrotonda = '".$costo_agg_da_inserire[4]."' where idcostiprenota = '$idcostiprenota'");
if ($dati_ca[$num_costo]['tasseperc']) esegui_query("update $tablecostiprenota set tasseperc = '".$dati_ca[$num_costo]['tasseperc']."' where idcostiprenota = '$idcostiprenota'");
} # fine for $num2
} # fine if ($costi_aggiuntivi_da_inserire_int)

# Se prenotazione da anno precedente copio i costi aggiuntivi anche nell'anno 
# corrente per mantenere il limite dei costi in stesso periodo
if (($dati_da_anno_prec == "SI" or $prenota_in_anno_succ) and ($costi_aggiuntivi_modificati_int or $costi_aggiuntivi_da_inserire_int)) {
$idcostiprenota_orig = esegui_query("select max(idcostiprenota) from $tablecostiprenota_orig");
$idcostiprenota_orig = risul_query($idcostiprenota_orig,0,0) + 1;
$idcostiprenota_prec = esegui_query("select max(idcostiprenota) from $tablecostiprenota_prec");
$idcostiprenota_prec = risul_query($idcostiprenota_prec,0,0) + 1;
if ($idcostiprenota_orig > $idcostiprenota_prec) $max_idcostiprenota = $idcostiprenota_orig;
else $max_idcostiprenota = $idcostiprenota_prec;
esegui_query("delete from $tablecostiprenota_orig where idprenota = '$id_prenota_orig'");
$costiprenota_prec = esegui_query("select * from $tablecostiprenota_prec where idprenota = '$id_prenota_prec'");
$num_costiprenota_prec = numlin_query($costiprenota_prec);
$max_idcostiprenota = $max_idcostiprenota + $num_costiprenota_prec + 1;
$diff_settimane = $fineperiodo - $fineperiodo_orig;
for ($num1 = 0 ; $num1 < $num_costiprenota_prec ; $num1++) {
$idcostiprenota = risul_query($costiprenota_prec,$num1,'idcostiprenota');
esegui_query("update $tablecostiprenota_prec set idcostiprenota = '$max_idcostiprenota' where idcostiprenota = '$idcostiprenota'");
esegui_query("insert into $tablecostiprenota_orig select * from $tablecostiprenota_prec where idcostiprenota = '$max_idcostiprenota' ");
esegui_query("update $tablecostiprenota_prec set idcostiprenota = '$idcostiprenota' where idcostiprenota = '$max_idcostiprenota'");
esegui_query("update $tablecostiprenota_orig set idcostiprenota = '$idcostiprenota_orig' where idcostiprenota = '$max_idcostiprenota'");
$settimane = risul_query($costiprenota_prec,$num1,'settimane');
if (str_replace(",","",$settimane) != $settimane) {
$settimane = explode(",",$settimane);
$settimane_nuove = ",";
for ($num2 = 1 ; $num2 < (count($settimane) - 1) ; $num2++) $settimane_nuove .= ($settimane[$num2] - $diff_settimane).",";
esegui_query("update $tablecostiprenota_orig set settimane = '$settimane_nuove' where idcostiprenota = '$idcostiprenota_orig'");
} # fine if (str_replace(",","",$settimane) != $settimane)
$idcostiprenota_orig++;
} # fine for $num1
} # fine if (($dati_da_anno_prec == "SI" or $prenota_in_anno_succ) and...

if ((string) $n_app_eliminati_costi != (string) $d_app_eliminati_costi) {
esegui_query("update $tableprenota set incompatibilita = '".aggslashdb($n_app_eliminati_costi)."' where idprenota = '$id_prenota' ");
} # fine if ((string) $n_app_eliminati_costi != (string) $d_app_eliminati_costi)

if (strcmp($n_sconto,"")) {
esegui_query("update $tableprenota set sconto = '$n_sconto' where idprenota = '$id_prenota' ");
} # fine if (strcmp($n_sconto,""))

if (strcmp($n_caparra,"")) {
esegui_query("update $tableprenota set caparra = '$n_caparra' where idprenota = '$id_prenota' ");
} # fine if (strcmp($n_caparra,""))

if ($n_met_paga_caparra != "") {
esegui_query("update $tableprenota set metodo_pagamento = '".aggslashdb($n_met_paga_caparra)."' where idprenota = '$id_prenota' ");
} # fine if ($n_met_paga_caparra != "")

if (strcmp($n_commissioni,"")) {
esegui_query("update $tableprenota set commissioni = '$n_commissioni' where idprenota = '$id_prenota' ");
} # fine if (strcmp($n_commissioni,""))

if ($n_origine_prenota != "") {
esegui_query("update $tableprenota set origine = '".aggslashdb($n_origine_prenota)."' where idprenota = '$id_prenota' ");
} # fine if ($n_origine_prenota != "")

if ($n_pagato != "") {
esegui_query("update $tableprenota set pagato = '$n_pagato' where idprenota = '$id_prenota' ");
} # fine if ($n_pagato != "")

if ($n_confermato != "") {
esegui_query("update $tableprenota set conferma = '$n_confermato' where idprenota = '$id_prenota' ");
} # fine if ($n_confermato != "")

if (strcmp($n_commento,"") and ($tipo_commento == "checkin" or $tipo_commento == "checkout")) {
$n_comm_checkin = "";
$n_comm_checkout = "";
if ($tipo_commento == "checkin" and !$d_checkin_idpr[$id_prenota]) $n_comm_checkin = $n_commento;
if ($tipo_commento == "checkout" and !$d_checkout_idpr[$id_prenota]) $n_comm_checkout = $n_commento;
$n_comm = $d_commento;
$altri_comm = "";
if (strstr($n_comm,">")) {
$n_comm = explode(">",$n_comm);
if (strcmp($n_comm[1],"")) {
if ($n_comm_checkin) $n_comm_checkin = $n_comm[1]." - $n_comm_checkin";
else $n_comm_checkin = $n_comm[1];
} # fine if (strcmp($n_comm[1],""))
if (strcmp($n_comm[2],"")) {
if ($n_comm_checkout) $n_comm_checkout = $n_comm[2]." - $n_comm_checkout";
else $n_comm_checkout = $n_comm[2];
} # fine if (strcmp($n_comm[2],""))
for ($num1 = 3 ; $num1 < count($n_comm) ; $num1++) {
$altri_comm .= ">".$n_comm[$num1];
} # fine for $num1
$n_comm = $n_comm[0];
} # fine if (strstr($n_comm,">"))
if (strcmp($n_comm_checkin,"") or strcmp($n_comm_checkout,"")) $n_comm .= ">$n_comm_checkin>$n_comm_checkout";
elseif ($altri_comm) $n_comm .= ">>";
if ($altri_comm) $n_comm .= $altri_comm;
esegui_query("update $tableprenota set commento = '".aggslashdb($n_comm)."' where idprenota = '$id_prenota' ");
} # fine if (strcmp($n_commento,"") and ($tipo_commento == "checkin" or...

if ((strcmp($n_commento,"") and !strcmp($tipo_commento,"")) or $n_cancella_commento == "SI") {
$n_comm = $n_commento;
if ($n_cancella_commento == "SI") $n_comm = "";
if (strstr($d_commento,">")) $n_comm = $n_comm.strstr($d_commento,">");
esegui_query("update $tableprenota set commento = '".aggslashdb($n_comm)."' where idprenota = '$id_prenota' ");
} # fine if ((strcmp($n_commento,"") and !strcmp($tipo_commento,"")) or $n_cancella_commento == "SI")

if (strcmp($tipo_commento,"") and $comm_pers_presenti[$tipo_commento]) {
$n_comm = $d_commento;
if (strstr($d_commento,">")) {
$d_comm = explode(">",$d_commento);
$num_comm = count($d_comm);
if ($num_comm < 4) {
for ($num1 = $num_comm ; $num1 < 3 ; $num1++) $n_comm .= ">";
$n_comm .= ">".$tipo_commento."<".$n_commento;
} # fine if ($num_comm < 4)
else {
$n_comm = $d_comm[0].">".$d_comm[1].">".$d_comm[2];
$n_comm_inserito = 0;
for ($num1 = 3 ; $num1 < $num_comm ; $num1++) {
if (substr($d_comm[$num1],0,(strlen($tipo_commento) + 1)) == "$tipo_commento<") {
$n_comm_inserito = 1;
if (strcmp($n_commento,"")) $n_comm .= ">".$tipo_commento."<".$n_commento;
} # fine if (substr($d_comm[$num1],0,(strlen($tipo_commento) + 1)) == "$tipo_commento<")
else $n_comm .= ">".$d_comm[$num1];
} # fine for $num1
if (!$n_comm_inserito and strcmp($n_commento,"")) $n_comm .= ">".$tipo_commento."<".$n_commento;
} # fine else if ($num_comm < 4)
} # fine if (strstr($d_commento,">"))
elseif (strcmp($n_commento,"")) $n_comm .= ">>>".$tipo_commento."<".$n_commento;
if ($n_comm != $d_commento) esegui_query("update $tableprenota set commento = '".aggslashdb($n_comm)."' where idprenota = '$id_prenota' ");
} # fine if (strcmp($tipo_commento,"") and $comm_pers_presenti[$tipo_commento])

$adesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("update $tableprenota set tariffa_tot = '$n_costo_tot', data_modifica = '$adesso' where idprenota = '$id_prenota' ");

if ($dati_da_anno_prec == "SI") passa_var_a_anno_corr($id_prenota,$tableperiodi,$tableprenota,$tablecostiprenota,$d_id_data_inizio,$d_id_data_fine,$n_fineperiodo,$inizioperiodo,$fineperiodo,$lunghezza_periodo);

} # fine for $num_idpr


aggiorna_beniinv_presenti($tablerelinventario,$beniinv_presenti);

if ($tabelle_lock) unlock_tabelle($tabelle_lock);


if ($tableprenota_da_aggiornare or $n_inizioperiodo != "" or $n_fineperiodo != "") {
$lock = 1;
$aggiorna_disp = 1;
$aggiorna_tar = 0;
if (@function_exists('pcntl_fork')) include("./includes/interconnect/aggiorna_ic_fork.php");
else include("./includes/interconnect/aggiorna_ic.php");
} # fine if ($tableprenota_da_aggiornare or...



for ($num_idpr = 0 ; $num_idpr < $num_id_prenota ; $num_idpr++) {
$id_prenota = $id_prenota_idpr[$num_idpr];
$n_pagato = $n_pagato_idpr[$id_prenota];
if ($n_pagato != "") {
$datainserimento_saldo = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$idcliente_saldo = risul_query($dati_prenota_idpr[$id_prenota],0,'idclienti');
$d_idinizioprenota = risul_query($dati_prenota_idpr[$id_prenota],0,'iddatainizio');
$d_idfineprenota = risul_query($dati_prenota_idpr[$id_prenota],0,'iddatafine');
if ($n_inizioperiodo != "") $idinizioperiodo_saldo = $n_inizioperiodo;
else $idinizioperiodo_saldo = $d_idinizioprenota;
if ($n_fineperiodo != "") $idfineperiodo_saldo = $n_fineperiodo;
else $idfineperiodo_saldo = $d_idfineprenota;
$motivazione_saldo = "$idcliente_saldo;$idinizioperiodo_saldo;$idfineperiodo_saldo;$id_prenota";
$pagato_attuale = risul_query($dati_prenota_idpr[$id_prenota],0,'pagato');
if (!$pagato_attuale) $pagato_attuale = 0;
$saldo = $n_pagato - $pagato_attuale;
$metodo_pagamento = ${"metodo_pagamento".$id_prenota};
$metodo_pagamento = aggslashdb($metodo_pagamento);
$tabelle_lock = array($tablesoldi);
$tabelle_lock = lock_tabelle($tabelle_lock);
$idsoldi = esegui_query("select max(idsoldi) from $tablesoldi");
$idsoldi = risul_query($idsoldi,0,0) + 1;
esegui_query("insert into $tablesoldi (idsoldi,motivazione,metodo_pagamento,saldo_prenota,soldi_prima,data_inserimento,utente_inserimento) values ('$idsoldi','$motivazione_saldo','$metodo_pagamento','$saldo','$pagato_attuale','$datainserimento_saldo','$id_utente')");
unlock_tabelle($tabelle_lock);

if (strcmp($cassa_pagamenti,"")) {
$tabelle_lock = array($tablecosti);
$altre_tab_lock = array($tableperiodi,$tableclienti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
if ($anno >= substr($datainserimento_saldo,0,4)) {
$costo0 = esegui_query("select * from $tablecosti where idcosti = '0' ");
$costo0 = risul_query($costo0,0,'val_costo');
$costo0 = (double) $costo0 + (double) $saldo;
esegui_query("update $tablecosti set val_costo = '$costo0' where idcosti = '0' ");
} # fine if ($anno_saldo >= substr($datainserimento_saldo,0,4))
if (!$idinizioperiodo_saldo) $data_inizio_costo = "?";
else {
$data_inizio_costo = esegui_query("select datainizio from $tableperiodi where idperiodi = '$idinizioperiodo_saldo' ");
$data_inizio_costo = formatta_data(risul_query($data_inizio_costo,0,'datainizio'),$stile_data);
} # fine else if (!$idinizioperiodo_saldo)
$data_fine_costo = esegui_query("select datafine from $tableperiodi where idperiodi = '$idfineperiodo_saldo' ");
$data_fine_costo = formatta_data(risul_query($data_fine_costo,0,'datafine'),$stile_data);
$cognome_saldo = esegui_query("select cognome from $tableclienti where idclienti = '$idcliente_saldo' ");
$cognome_saldo = risul_query($cognome_saldo,0,'cognome');
$nome_costo = "".mex("Prenotazione",$pag)." $id_prenota ".mex("dal",$pag)." $data_inizio_costo ".mex("al",$pag)." $data_fine_costo ".mex("per",$pag)." $cognome_saldo";
$idcosti = esegui_query("select max(idcosti) from $tablecosti");
$idcosti = risul_query($idcosti,0,0) + 1;
if ($cassa_pagamenti == "#1#") $nome_cassa = "";
else $nome_cassa = $cassa_pagamenti;
esegui_query("insert into $tablecosti (idcosti,nome_costo,val_costo,tipo_costo,nome_cassa,persona_costo,provenienza_costo,metodo_pagamento,datainserimento,hostinserimento,utente_inserimento) values ('$idcosti','".aggslashdb($nome_costo)."','$saldo','e','".aggslashdb($nome_cassa)."','".aggslashdb($nome_utente)."','p','$metodo_pagamento','$datainserimento_saldo','$HOSTNAME','$id_utente') ");
unlock_tabelle($tabelle_lock);
} # fine if (strcmp($cassa_pagamenti,""))

} # fine if ($n_pagato != "")
} # fine for $num_idpr

} # fine else if ($inserire == "NO")

} # fine function esegui_modifiche_prenotazione




?>