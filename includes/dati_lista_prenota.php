<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2018 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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




$tabelle_lock = "";
$altre_tab_lock = array($tableanni,$tableprenota,$tablecostiprenota,$tableperiodi,$tableclienti,$tablerelclienti,$tablesoldi,$tableutenti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
include("./includes/funzioni_costi_agg.php");
$num_r = 0;
$lista_p = array();
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) {
$id_prenota = aggslashdb($lista_prenota[$num1]);
$dati_prenota = esegui_query("select * from $tableprenota where idprenota = '$id_prenota'");
if (numlin_query($dati_prenota) == 1) {
$cont = "SI";
$utente_inserimento = risul_query($dati_prenota,0,'utente_inserimento');
if ($priv_vedi_tab_prenotazioni != "s" and ($priv_vedi_tab_mesi != "s" or $priv_mod_prenotazioni != "s")) {
if ($priv_vedi_tab_prenotazioni == "g" or (($priv_vedi_tab_mesi == "g" or $priv_vedi_tab_mesi == "q") and $priv_mod_prenotazioni == "g")) {
if (!$utenti_gruppi[$utente_inserimento]) $cont = "NO";
} # fine if ($priv_vedi_tab_prenotazioni == "g" or...
elseif ($utente_inserimento != $id_utente) $cont = "NO";
} # fine if ($priv_vedi_tab_prenotazioni != "s" and ($priv_vedi_tab_mesi != "s" or $priv_mod_prenotazioni != "s"))
if ($cont == "SI") {
$num_r++;
$lista_p[$num_r] = $lista_prenota[$num1];
${"utente_inserimento_prenotazione_".$num_r} = $utente_inserimento;
${"data_inserimento_prenotazione_".$num_r} = substr(risul_query($dati_prenota,0,'datainserimento'),0,16);
if ($vedi_clienti != "NO") {
$id_clienti = risul_query($dati_prenota,0,'idclienti');
$dati_cliente = esegui_query("select * from $tableclienti where idclienti = '$id_clienti' ");
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$mostra_cliente = "SI";
$utente_inserimento = risul_query($dati_cliente,0,'utente_inserimento');
if ($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) $mostra_cliente = "NO";
if ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento]) $mostra_cliente = "NO";
} # fine if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI")
if (($vedi_clienti != "PROPRI" and $vedi_clienti != "GRUPPI") or $mostra_cliente != "NO") {
${"cognome_".$num_r} = risul_query($dati_cliente,0,'cognome');
# I controlli di non vuoto servono a risparmiare memoria, creando solo le variabili necessarie
if (strcmp(risul_query($dati_cliente,0,'nome'),"")) ${"nome_".$num_r} = risul_query($dati_cliente,0,'nome');
if (strcmp(risul_query($dati_cliente,0,'soprannome'),"")) ${"soprannome_".$num_r} = risul_query($dati_cliente,0,'soprannome');
if (strcmp(risul_query($dati_cliente,0,'titolo'),"")) ${"titolo_".$num_r} = risul_query($dati_cliente,0,'titolo');
if (strcmp(risul_query($dati_cliente,0,'sesso'),"")) ${"sesso_".$num_r} = risul_query($dati_cliente,0,'sesso');
if (strcmp(risul_query($dati_cliente,0,'datanascita'),"")) ${"data_nascita_".$num_r} = risul_query($dati_cliente,0,'datanascita');
if (strcmp(risul_query($dati_cliente,0,'cittanascita'),"")) ${"citta_nascita_".$num_r} = risul_query($dati_cliente,0,'cittanascita');
if (strcmp(risul_query($dati_cliente,0,'regionenascita'),"")) ${"regione_nascita_".$num_r} = risul_query($dati_cliente,0,'regionenascita');
if (strcmp(risul_query($dati_cliente,0,'nazionenascita'),"")) ${"nazione_nascita_".$num_r} = risul_query($dati_cliente,0,'nazionenascita');
if (strcmp(risul_query($dati_cliente,0,'nazionalita'),"")) ${"cittadinanza_".$num_r} = risul_query($dati_cliente,0,'nazionalita');
if (strcmp(risul_query($dati_cliente,0,'lingua'),"")) ${"codice_lingua_".$num_r} = risul_query($dati_cliente,0,'lingua');
if (strcmp(risul_query($dati_cliente,0,'nazione'),"")) ${"nazione_".$num_r} = risul_query($dati_cliente,0,'nazione');
if (strcmp(risul_query($dati_cliente,0,'regione'),"")) ${"regione_".$num_r} = risul_query($dati_cliente,0,'regione');
if (strcmp(risul_query($dati_cliente,0,'citta'),"")) ${"citta_".$num_r} = risul_query($dati_cliente,0,'citta');
if ($priv_vedi_indirizzo == "s") {
if (strcmp(risul_query($dati_cliente,0,'via'),"")) ${"via_".$num_r} = risul_query($dati_cliente,0,'via');
if (strcmp(risul_query($dati_cliente,0,'numcivico'),"")) ${"numcivico_".$num_r} = risul_query($dati_cliente,0,'numcivico');
if (strcmp(risul_query($dati_cliente,0,'cap'),"")) ${"cap_".$num_r} = risul_query($dati_cliente,0,'cap');
} # fine if ($priv_vedi_indirizzo == "s")
if (strcmp(risul_query($dati_cliente,0,'documento'),"")) ${"documento_".$num_r} = risul_query($dati_cliente,0,'documento');
if (strcmp(risul_query($dati_cliente,0,'tipodoc'),"")) ${"tipo_documento_".$num_r} = risul_query($dati_cliente,0,'tipodoc');
if (strcmp(risul_query($dati_cliente,0,'cittadoc'),"")) ${"citta_documento_".$num_r} = risul_query($dati_cliente,0,'cittadoc');
if (strcmp(risul_query($dati_cliente,0,'regionedoc'),"")) ${"regione_documento_".$num_r} = risul_query($dati_cliente,0,'regionedoc');
if (strcmp(risul_query($dati_cliente,0,'nazionedoc'),"")) ${"nazione_documento_".$num_r} = risul_query($dati_cliente,0,'nazionedoc');
if (strcmp(risul_query($dati_cliente,0,'scadenzadoc'),"")) ${"scadenza_documento_".$num_r} = risul_query($dati_cliente,0,'scadenzadoc');
if ($priv_vedi_telefoni == "s") {
if (strcmp(risul_query($dati_cliente,0,'telefono'),"")) ${"telefono_".$num_r} = risul_query($dati_cliente,0,'telefono');
if (strcmp(risul_query($dati_cliente,0,'telefono2'),"")) ${"telefono2_".$num_r} = risul_query($dati_cliente,0,'telefono2');
if (strcmp(risul_query($dati_cliente,0,'telefono3'),"")) ${"telefono3_".$num_r} = risul_query($dati_cliente,0,'telefono3');
if (strcmp(risul_query($dati_cliente,0,'fax'),"")) ${"fax_".$num_r} = risul_query($dati_cliente,0,'fax');
if (strcmp(risul_query($dati_cliente,0,'email'),"")) ${"email_".$num_r} = risul_query($dati_cliente,0,'email');
} # fine if ($priv_vedi_telefoni == "s")
if (strcmp(risul_query($dati_cliente,0,'cod_fiscale'),"")) ${"codice_fiscale_".$num_r} = risul_query($dati_cliente,0,'cod_fiscale');
if (strcmp(risul_query($dati_cliente,0,'partita_iva'),"")) ${"partita_iva_".$num_r} = risul_query($dati_cliente,0,'partita_iva');
$dati_relcliente = esegui_query("select * from $tablerelclienti where idclienti = '$id_clienti' and tipo = 'campo_pers' ");
$num_dati_relcliente = numlin_query($dati_relcliente);
for ($num2 = 0 ; $num2 < $num_dati_relcliente ; $num2++) {
${"campo_personalizzato_".risul_query($dati_relcliente,$num2,'testo1')."_".$num_r} = risul_query($dati_relcliente,$num2,'testo3');
} # fine for $num2
chiudi_query($dati_relcliente);
} # fine if (($vedi_clienti != "PROPRI" and...
chiudi_query($dati_cliente);
} # fine if ($vedi_clienti != "NO")
${"numero_prenotazione_".$num_r} = $id_prenota;
if ($priv_mod_codice == "s") {
$cod_prenota = risul_query($dati_prenota,0,'codice');
${"codice_prenotazione_".$num_r} = substr($cod_prenota,0,2).$id_clienti.substr($cod_prenota,2,1).$id_prenota.substr($anno,-1).substr($cod_prenota,-1);
} # fine if ($priv_mod_codice == "s")
$id_data_inizio = risul_query($dati_prenota,0,'iddatainizio');
$id_data_fine = risul_query($dati_prenota,0,'iddatafine');
${"data_fine_".$num_r} = esegui_query("select * from $tableperiodi where idperiodi = '$id_data_fine'");
${"data_fine_".$num_r} = risul_query(${"data_fine_".$num_r},0,'datafine');
if ($id_data_inizio) {
${"data_inizio_".$num_r} = esegui_query("select * from $tableperiodi where idperiodi = '$id_data_inizio'");
${"data_inizio_".$num_r} = risul_query(${"data_inizio_".$num_r},0,'datainizio');
${"num_periodi_".$num_r} = $id_data_fine - $id_data_inizio + 1;
$tariffa = risul_query($dati_prenota,0,'tariffa');
$tariffa = explode("#@&",$tariffa);
${"nome_tariffa_".$num_r} = $tariffa[0];
$costo_tariffa = (double) $tariffa[1];
${"costo_tariffa_".$num_r} = $costo_tariffa;
$sconto = (double) risul_query($dati_prenota,0,'sconto');
if (strcmp($sconto,"")) ${"sconto_".$num_r} = $sconto;
if (strcmp(risul_query($dati_prenota,0,'tasseperc'),"")) ${"percentuale_tasse_tariffa_".$num_r} = risul_query($dati_prenota,0,'tasseperc');
else ${"percentuale_tasse_tariffa_".$num_r} = 0;
if (strcmp(risul_query($dati_prenota,0,'commento'),"")) ${"commento_".$num_r} = risul_query($dati_prenota,0,'commento');
if (strstr(${"commento_".$num_r},">")) {
$comm = explode(">",${"commento_".$num_r});
${"commento_".$num_r} = $comm[0];
if (strcmp($comm[1],"")) ${"promemoria_entrata_".$num_r} = $comm[1];
if (strcmp($comm[2],"")) ${"promemoria_uscita_".$num_r} = $comm[2];
for ($num2 = 3 ; $num2 < count($comm) ; $num2++) {
$comm_pers = explode("<",$comm[$num2]);
if (strcmp($comm_pers[1],"")) ${"commento_personalizzato_".$comm_pers[0]."_".$num_r} = $comm_pers[1];
} # fine for $num2
} # fine if (strstr(${"commento_".$num_r},">"))
if (strcmp(risul_query($dati_prenota,0,'origine'),"")) ${"origine_prenotazione_".$num_r} = risul_query($dati_prenota,0,'origine');
$caparra = risul_query($dati_prenota,0,'caparra');
if (strcmp($caparra,"")) ${"caparra_".$num_r} = $caparra;
if (strcmp(risul_query($dati_prenota,0,'commissioni'),"")) ${"commissioni_".$num_r} = risul_query($dati_prenota,0,'commissioni');
$numpersone = risul_query($dati_prenota,0,'num_persone');
if (strcmp($numpersone,"")) ${"num_persone_".$num_r} = $numpersone;
${"unita_occupata_".$num_r} = risul_query($dati_prenota,0,'idappartamenti');
${"unita_assegnabili_".$num_r} = risul_query($dati_prenota,0,'app_assegnabili');
${"pagato_".$num_r} = risul_query($dati_prenota,0,'pagato');
$tariffesettimanali = risul_query($dati_prenota,0,'tariffesettimanali');
${"tariffesettimanali_".$num_r} = $tariffesettimanali;
$dati_cap = dati_costi_agg_prenota($tablecostiprenota,$id_prenota);
unset($num_letti_agg);
$costo_agg_tot = (double) 0;
for ($numca = 0 ; $numca < $dati_cap['num'] ; $numca++) {
aggiorna_letti_agg_in_periodi($dati_cap,$numca,$num_letti_agg,$id_data_inizio,$id_data_fine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],"","");
$costo_agg_parziale = (double) calcola_prezzo_totale_costo($dati_cap,$numca,$id_data_inizio,$id_data_fine,$dati_cap[$numca]['settimane'],$dati_cap[$numca]['moltiplica_costo'],$costo_tariffa,$tariffesettimanali,($costo_tariffa + $costo_agg_tot - $sconto),$caparra,$numpersone);
$costo_agg_tot = (double) $costo_agg_tot + $costo_agg_parziale;
${"nome_costo_agg".$numca."_".$num_r} = $dati_cap[$numca]['nome'];
${"val_costo_agg".$numca."_".$num_r} = $costo_agg_parziale;
if (strcmp($dati_cap[$numca]['tasseperc'],"")) ${"percentuale_tasse_costo_agg".$numca."_".$num_r} = $dati_cap[$numca]['tasseperc'];
else ${"percentuale_tasse_costo_agg".$numca."_".$num_r} = 0;
${"moltiplica_max_costo_agg".$numca."_".$num_r} = $dati_cap[$numca]['moltiplica_costo'];
if ($dati_cap[$numca]['associasett'] == "s") {
if ($dati_cap[$numca]['settimane']) ${"giorni_costo_agg".$numca."_".$num_r} = $dati_cap[$numca]['settimane'];
else ${"giorni_costo_agg".$numca."_".$num_r} = ",";
} # fine if ($dati_cap[$numca]['associasett'] == "s")
else ${"giorni_costo_agg".$numca."_".$num_r} = "";
${"data_inserimento_costo_agg".$numca."_".$num_r} = substr($dati_cap[$numca]['datainserimento'],0,10);
${"utente_inserimento_costo_agg".$numca."_".$num_r} = $dati_cap[$numca]['utente_inserimento'];
} # fine for $numca
${"num_costi_aggiuntivi_".$num_r} = $dati_cap['num'];
${"n_letti_agg_".$num_r} = $num_letti_agg['max'];
${"costo_tot_".$num_r} = $costo_tariffa + $costo_agg_tot - $sconto;
${"orario_entrata_stimato_".$num_r} = risul_query($dati_prenota,0,'checkin');
if (!${"orario_entrata_stimato_".$num_r}) ${"orario_entrata_stimato_".$num_r} = risul_query($dati_prenota,0,'checkout');
else ${"orario_entrata_stimato_".$num_r} = "";
${"id_anni_prec_".$num_r} = risul_query($dati_prenota,0,'id_anni_prec');
if ($priv_vedi_tab_costi != "n") {
$num_pagamenti = 0;
if (${"id_anni_prec_".$num_r}) {
if ($tabelle_lock) {
unlock_tabelle($tabelle_lock);
$tabelle_lock = "";
} # fine if ($tabelle_lock)
$id_anni_prec_vett = explode(";",${"id_anni_prec_".$num_r});
for ($num2 = 1 ; $num2 < (count($id_anni_prec_vett) - 1) ; $num2++) {
$id_anno_prec = explode(",",$id_anni_prec_vett[$num2]);
$anno_prec_esistente = esegui_query("select idanni from $tableanni where idanni = '".aggslashdb($id_anno_prec[0])."'");
if (numlin_query($anno_prec_esistente)) {
$pagamenti = esegui_query("select metodo_pagamento,saldo_prenota,data_inserimento,utente_inserimento from $PHPR_TAB_PRE"."soldi".$id_anno_prec[0]." where saldo_prenota is not NULL and motivazione $LIKE '%;".$id_anno_prec[1]."' order by data_inserimento");
$num_pagamenti2 = numlin_query($pagamenti);
for ($num3 = 0 ; $num3 < $num_pagamenti2 ; $num3++) {
${"data_paga".$num_pagamenti."_".$num_r} = substr(risul_query($pagamenti,$num3,'data_inserimento'),0,10);
${"utente_paga".$num_pagamenti."_".$num_r} = risul_query($pagamenti,$num3,'utente_inserimento');
if (strcmp(risul_query($pagamenti,$num3,'metodo_pagamento'),"")) ${"metodo_paga".$num_pagamenti."_".$num_r} = risul_query($pagamenti,$num3,'metodo_pagamento');
${"saldo_paga".$num_pagamenti."_".$num_r} = risul_query($pagamenti,$num3,'saldo_prenota');
$num_pagamenti++;
} # fine for $num3
} # fine (numlin_query($anno_prec_esistente))
} # fine for $num2
} # fine if (${"id_anni_prec_".$num_r})
$pagamenti = esegui_query("select metodo_pagamento,saldo_prenota,data_inserimento,utente_inserimento from $tablesoldi where saldo_prenota is not NULL and motivazione $LIKE '%;$id_prenota' order by data_inserimento");
$num_pagamenti2 = numlin_query($pagamenti);
for ($num2 = 0 ; $num2 < $num_pagamenti2 ; $num2++) {
${"data_paga".$num_pagamenti."_".$num_r} = substr(risul_query($pagamenti,$num2,'data_inserimento'),0,10);
${"utente_paga".$num_pagamenti."_".$num_r} = risul_query($pagamenti,$num2,'utente_inserimento');
if (strcmp(risul_query($pagamenti,$num2,'metodo_pagamento'),"")) ${"metodo_paga".$num_pagamenti."_".$num_r} = risul_query($pagamenti,$num2,'metodo_pagamento');
${"saldo_paga".$num_pagamenti."_".$num_r} = risul_query($pagamenti,$num2,'saldo_prenota');
$num_pagamenti++;
} # fine for $num2
${"num_pagamenti_".$num_r} = $num_pagamenti;
} # fine if ($priv_vedi_tab_costi != "n")
} # fine if ($id_data_inizio)
} # fine if ($cont == "SI")
} # fine if (numlin_query($dati_prenota) == 1)
chiudi_query($dati_prenota);
} # fine for $num1

$lista_prenota = $lista_p;
$num_ripeti = $num_r;
unset($lista_p);
unset($num_r);
if ($tabelle_lock) unlock_tabelle($tabelle_lock);



?>