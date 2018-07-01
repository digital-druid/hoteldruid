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

$pag = "prenota.php";
$titolo = "HotelDruid: Prenota";


include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
include("./includes/sett_gio.php");
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableclienti = $PHPR_TAB_PRE."clienti";
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tabletransazioni = $PHPR_TAB_PRE."transazioni";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablemessaggi = $PHPR_TAB_PRE."messaggi";
$tablerclientiprenota = $PHPR_TAB_PRE."rclientiprenota".$anno;
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablerelinventario = $PHPR_TAB_PRE."relinventario";
$tablecache = $PHPR_TAB_PRE."cache";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";


$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente) {

if ($id_utente != 1) {
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
if ($attiva_regole1_consentite != "n") $regole1_consentite = explode("#@^",substr($regole1_consentite,3));
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
$priv_ins_conferma = substr($priv_ins_prenota,2,1);
$priv_ins_sconto = substr($priv_ins_prenota,3,1);
$priv_ins_caparra = substr($priv_ins_prenota,4,1);
$priv_ins_costi_agg = substr($priv_ins_prenota,5,1);
$priv_ins_commento = substr($priv_ins_prenota,6,1);
$priv_ins_num_persone = substr($priv_ins_prenota,7,1);
$priv_ins_periodi_passati = substr($priv_ins_prenota,8,1);
$priv_ins_multiple = substr($priv_ins_prenota,9,1);
$priv_ins_checkin = substr($priv_ins_prenota,10,1);
$priv_ins_orig_prenota = substr($priv_ins_prenota,11,1);
$priv_ins_commenti_pers = substr($priv_ins_prenota,12,1);
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
$modifica_pers = "SI";
$inserimento_nuovi_clienti = "SI";
$vedi_clienti = "SI";
$attiva_prefisso_clienti = "n";
$attiva_regole1_consentite = "n";
$attiva_tariffe_consentite = "n";
$attiva_costi_agg_consentiti = "n";
$priv_ins_nuove_prenota = "s";
$priv_ins_assegnazione_app = "s";
$priv_ins_conferma = "s";
$priv_ins_sconto = "s";
$priv_ins_caparra = "s";
$priv_ins_costi_agg = "s";
$priv_ins_commento = "s";
$priv_ins_num_persone = "s";
$priv_ins_periodi_passati = "s";
$priv_ins_multiple = "s";
$priv_ins_checkin = "s";
$priv_ins_orig_prenota = "s";
$priv_ins_commenti_pers = "s";
} # fine else if ($id_utente != 1)

if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0) {
$num_clienti_esistenti = esegui_query("select idclienti from $tableclienti");
$num_clienti_esistenti = numlin_query($num_clienti_esistenti);
if ($num_clienti_esistenti >= C_MASSIMO_NUM_CLIENTI) $inserimento_nuovi_clienti = "NO";
} # fine if (defined("C_MASSIMO_NUM_CLIENTI") and C_MASSIMO_NUM_CLIENTI != 0)

if ($anno_utente_attivato == "SI" and $priv_ins_nuove_prenota == "s") {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


if ($id_utente != 1 or controlla_num_pos($id_utente_ins) == "NO" or $id_utente_ins == "") $id_utente_ins = $id_utente;
$inserito_nuovo_cliente = "NO";



/*
STRUTTURA TABELLA COSTI AGGIUNTIVI DELLE PRENOTAZIONI

tipo(varchar2)			u-s			1 unico - settimanale
				f-p-q			2 fisso - percentuale su tariffa - percentuale su totale
nome(varchar40)
valore(float8)
valore_orig(float8)
arrotonda(float4)
associasett(varchar1)		s-n			1 associa a specifiche settimane della prenotazione: si-no
settimane(text)		X;,idperiodi,idperiodi,...	1 numero di settimane se associasett=n, lista periodi se associasett=s
moltiplica(text)		X;X,X,X...		1 moltiplica per X se no sett. o no associa sett. ; altrimenti moltiplica ogni sett. attivata per X corrispondente
categoria(text)
letto(varchar1)			s-n			1 considera come letto aggiuntivo: si-no
#numlimite(integer)		X			NULL o vuoto se non ci sono limiti al numero di costi nello stesso periodo
idntariffe(integer)		idntariffa		id del costo aggiuntivo
variazione(varchar10)		s-n			1 mantenere costi combinabili della categoria: si-no
				s-n			2 escludere il costo dal totale per costi percentuali: si-no
varmoltiplica(text)		1-c-p-t			1 moltiplica per: 1 - chiedere - persone - persnone totali
				x;x-n-p-t-m-n;x;x	2 x se 1=1 ; numero massimo: nessuno - fisso - persone - persone totali - persone meno una - persone totali meno una ;...
				NNN,NNN			3- numero da aggiungere a moltiplica , numero massimo se 1=c e 2=n o numero da sottrarre se 1=c e 2=p/t
varnumsett(varchar20)		t-m-c-n-s-g,		1 tutte - tutte meno una - chiedere - x settimane si e y no - x settimane si e y no - solo giorni della settimana selezionati
varperiodipermessi(text)	t-u-p			NULL o vuoto se periodi tutti permessi, altrimenti: tutta la prenotazione dentro i periodi - anche un solo periodo - solo periodi permessi
				idperiodo-idperiodo,...	
varbeniinv(text)		X;			numero di ripetizioni del costo
				mag$idmag-app;		NULL o vuoto se nessun bene da eliminare dall'inventario, altimenti: elimina dal magazzino $idmag - elimina dall'appartamento della prenotazione
				idbene,x;idbene,x;...	idbene del bene da eliminare e numero da eliminare
varappincomapibili(text)	idapp,idapp,...		NULL o vuoto se nessun appartamento incompatibile
vartariffeassociate(varchar10)	s-r-p			1 associare a tariffa: sempre - sempre in periodi permessi - se possibile
				s-<x->x-=x-|x<y		2- NULL o vuoto se costo non associato alla tariffa, altrimenti: per qualsiasi numero di settimane - meno di x - più di x - per x settimane - compreso tra x e y settimane
vartariffeincomp(text)	X,X,...				NULL o vuoto se costo compatibile con tutte le tariffe, antrimenti lista coi numeri delle tariffe incompatibili

*/



$Euro = nome_valuta();
$stile_soldi = stile_soldi();
$stile_data = stile_data();


if ($annulla == "SI") {
$tabelle_lock = array($tableprenota,$tabletransazioni);
$altre_tab_lock = array($tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$id_transazione = aggslashdb($id_transazione);
$dati_transazione = recupera_dati_transazione($id_transazione,$id_sessione,$anno,"NO",$tipo_transazione);
if ($tipo_transazione == "ins_p") {
$prenota_temp = risul_query($dati_transazione,0,'dati_transazione13');
if ($prenota_temp) {
$num_tipologie = risul_query($dati_transazione,0,'dati_transazione1');
$prenota_temp = explode(", ,",$prenota_temp);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$prenota_temp2 = explode(",",$prenota_temp[($n_t - 1)]);
for ($num1 = 1 ; $num1 <= count($prenota_temp2) ; $num1++) {
esegui_query("delete from $tableprenota where idprenota = '".aggslashdb($prenota_temp2[($num1 - 1)])."'","",1);
} # fine for $num1
} # fine for $n_t
} # fine if ($prenota_temp)
esegui_query("delete from $tabletransazioni where idtransazioni = '$id_transazione' ");
} # fine if ($tipo_transazione == "ins_p")
unlock_tabelle($tabelle_lock);
$id_transazione = "";
$num_tipologie = "";
} # fine if ($annulla == "SI")

if (!$num_tipologie or controlla_num_pos($num_tipologie) == "NO" or $num_tipologie == 0 or $num_tipologie > 999) $num_tipologie = 1;
if ($num_tipologie_da_aggiungere and controlla_num_pos($num_tipologie_da_aggiungere) == "SI" and ($num_tipologie + $num_tipologie_da_aggiungere) <= 999) {
for ($n_t = ($num_tipologie + 1) ; $n_t <= ($num_tipologie + $num_tipologie_da_aggiungere) ; $n_t++) {
${"inizioperiodo".$n_t} = ${"inizioperiodo".$num_tipologie};
${"fineperiodo".$n_t} = ${"fineperiodo".$num_tipologie};
${"appartamento".$n_t} = ${"appartamento".$num_tipologie};
${"nometipotariffa".$n_t} = ${"nometipotariffa".$num_tipologie};
${"num_app_richiesti".$n_t} = ${"num_app_richiesti".$num_tipologie};
${"numpersone".$n_t} = ${"numpersone".$num_tipologie};
${"assegnazioneapp".$n_t} = ${"assegnazioneapp".$num_tipologie};
${"tipo_sconto".$n_t} = ${"tipo_sconto".$num_tipologie};
${"sconto".$n_t} = ${"sconto".$num_tipologie};
${"tipo_val_sconto".$n_t} = ${"tipo_val_sconto".$num_tipologie};
${"conferma_prenota".$n_t} = ${"conferma_prenota".$num_tipologie};
${"num_commenti".$n_t} = ${"num_commenti".$num_tipologie};
for ($num_comm = 1 ; $num_comm <= ${"num_commenti".$n_t} ; $num_comm++) {
${"tipo_commento".$num_comm."_".$n_t} = ${"tipo_commento".$num_comm."_".$num_tipologie};
${"commento".$num_comm."_".$n_t} = ${"commento".$num_comm."_".$num_tipologie};
} # fine for $num_comm
${"lista_app".$n_t} = ${"lista_app".$num_tipologie};
${"caparra".$n_t} = ${"caparra".$num_tipologie};
${"tipo_val_caparra".$n_t} = ${"tipo_val_caparra".$num_tipologie};
${"commissioni".$n_t} = ${"commissioni".$num_tipologie};
${"tipo_val_commissioni".$n_t} = ${"tipo_val_commissioni".$num_tipologie};
${"giorno_stima_checkin".$n_t} = ${"giorno_stima_checkin".$num_tipologie};
${"ora_stima_checkin".$n_t} = ${"ora_stima_checkin".$num_tipologie};
${"min_stima_checkin".$n_t} = ${"min_stima_checkin".$num_tipologie};
${"met_paga_caparra".$n_t} = ${"met_paga_caparra".$num_tipologie};
${"origine_prenota".$n_t} = ${"origine_prenota".$num_tipologie};
${"num_piano".$n_t} = ${"num_piano".$num_tipologie};
${"num_casa".$n_t} = ${"num_casa".$num_tipologie};
${"num_persone_casa".$n_t} = ${"num_persone_casa".$num_tipologie};
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
${"costoagg".$numca."_".$n_t} = ${"costoagg".$numca."_".$num_tipologie};
${"numsettimane".$numca."_".$n_t} = ${"numsettimane".$numca."_".$num_tipologie};
${"nummoltiplica_ca".$numca."_".$n_t} = ${"nummoltiplica_ca".$numca."_".$num_tipologie};
} # fine for $numca
} # fine for $n_t
$num_tipologie = $num_tipologie + $num_tipologie_da_aggiungere;
} # fine if ($num_tipologie_da_aggiungere and...



# Se si viene da clienti.php e si devono ancora inserire i dati del cliente
if ($inserire_dati_cliente == "SI") {
if ($cognome == "") {
echo mex("É necessario inserire il cognome del cliente",$pag).".<br>";
$inserire = "NO";
} # fine if ($cognome == "")
if ($inserimento_nuovi_clienti == "NO") $inserire = "NO";

if ($inserire == "NO") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$origine\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<br><input class=\"sbutton\" type=\"submit\" name=\"torna\" value=\"".mex("Torna indietro",$pag)."\">
</div></form>";
$mostra_form_inserisci_prenota = "NO";
} # fine if ($inserire == "NO")
else {
if ($inserire) {

include("./includes/funzioni_clienti.php");

$tabelle_lock = array($tableclienti,$tablerelclienti);
$altre_tab_lock = array($tablepersonalizza,$tableprivilegi);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

if ($idclienti == "") {
$inserito_nuovo_cliente = "SI";
if ($id_utente == 1 and $id_utente_ins != 1) {
$privilegi_globali_utente = esegui_query("select * from $tableprivilegi where idutente = '$id_utente_ins' and anno = '1'");
$prefisso_clienti = risul_query($privilegi_globali_utente,0,'prefisso_clienti');
$attiva_prefisso_clienti = substr($prefisso_clienti,0,1);
if ($attiva_prefisso_clienti != "n") {
$prefisso_clienti = explode(",",$prefisso_clienti);
$prefisso_clienti = $prefisso_clienti[1];
} # fine if ($prefisso_clienti != "n")
} # fine if ($id_utente == 1 and $id_utente_ins != 1)

$campi_pers_vett = array();
$campi_pers = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_cliente' and idutente = '$id_utente'");
if (numlin_query($campi_pers) == 1) {
$campi_pers = explode(">",risul_query($campi_pers,0,'valpersonalizza'));
$campi_pers_vett['num'] = count($campi_pers);
for ($num1 = 0 ; $num1 < $campi_pers_vett['num'] ; $num1++) {
$opt = explode("<",$campi_pers[$num1]);
$campi_pers_vett[$num1] = $opt[0];
$campi_pers_vett['tipo'][$num1] = $opt[1];
$campi_pers_vett['val'][$num1] = ${"campo_pers".$num1};
} # fine for $num1
} # fine if (numlin_query($campi_pers) == 1)

$idclienti = inserisci_dati_cliente($cognome,$nome,$soprannome,$titolo_cli,$sesso,$mesenascita,$giornonascita,$annonascita,$nazionenascita,$cittanascita,$regionenascita,$documento,$tipodoc,$mesescaddoc,$giornoscaddoc,$annoscaddoc,$cittadoc,$regionedoc,$nazionedoc,$nazionalita,$lingua_cli,$nazione,$citta,$regione,$via,$nomevia,$numcivico,$cap,$telefono,$telefono2,$telefono3,$fax,$email,$cod_fiscale,$partita_iva,"1",$id_utente_ins,$attiva_prefisso_clienti,$prefisso_clienti,"","",$campi_pers_vett);
} # fine if ($idclienti == "")
else unset($idclienti);
unlock_tabelle($tabelle_lock);
} # fine if ($inserire)
} # fine else if ($inserire == "NO")

$inserire = "";
} # fine if ($inserire_dati_cliente == "SI")




# Se vi è $idclienti siamo già nella fase di inserimento.
if ($idclienti) {

unset($idospiti);
unset($num_ordine);
unset($parentela);
unset($idclienti_compagni);
unset($num_ospiti);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) $num_ospiti[$n_t][$num1] = 0;


$tabelle_lock = array($tableprenota);
$altre_tab_lock = array($tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$minuti_durata_insprenota = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'minuti_durata_insprenota' and idutente = '1'");
$minuti_durata_insprenota = risul_query($minuti_durata_insprenota,0,'valpersonalizza_num');
$lim_prenota_temp = aggslashdb(date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - ($minuti_durata_insprenota * 60))));
esegui_query("delete from $tableprenota where idclienti = '0' and datainserimento < '$lim_prenota_temp'","",1);
unlock_tabelle($tabelle_lock);


$dati_transazione = recupera_dati_transazione($id_transazione,$id_sessione,$anno,"SI",$tipo_transazione);
if ($tipo_transazione != "ins_p") {
$torna_invece_di_ok = "SI";
$num_tipologie = 0;
echo "<div style=\"display: inline; color: red;\">".mex("Transazione scaduta",$pag)."</div>.<br>";
} # fine if ($tipo_transazione != "ins_p")
else {
$num_tipologie = risul_query($dati_transazione,0,'dati_transazione1');
$inizioperiodo = explode(";",risul_query($dati_transazione,0,'dati_transazione2'));
$fineperiodo = explode(";",risul_query($dati_transazione,0,'dati_transazione3'));
$appartamento = explode(", ,",risul_query($dati_transazione,0,'dati_transazione4'));
$nometipotariffa = explode(",",risul_query($dati_transazione,0,'dati_transazione5'));
$numpersone = explode(",",risul_query($dati_transazione,0,'dati_transazione6'));
$assegnazioneapp = explode(",",risul_query($dati_transazione,0,'dati_transazione7'));
$num_app_richiesti = explode(",",risul_query($dati_transazione,0,'dati_transazione8'));
$lista_app = explode(", ,",risul_query($dati_transazione,0,'dati_transazione9'));
$spezzetta = explode(",",risul_query($dati_transazione,0,'dati_transazione10'));
$prenota_vicine_vett = explode(",",risul_query($dati_transazione,0,'dati_transazione12'));
$prenota_vicine = $prenota_vicine_vett[0];
$num_letti_agg_max = explode(",",risul_query($dati_transazione,0,'dati_transazione14'));
$idospiti_transazione = risul_query($dati_transazione,0,'dati_transazione15');
$numordine_transazione = risul_query($dati_transazione,0,'dati_transazione16');
$parentela_transazione = risul_query($dati_transazione,0,'dati_transazione17');
$app_eliminati_costi = unserialize(risul_query($dati_transazione,0,'dati_transazione18'));
$dati_extra = explode(";",risul_query($dati_transazione,0,'dati_transazione19'));
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
${"inizioperiodo".$n_t} = $inizioperiodo[($n_t - 1)];
${"fineperiodo".$n_t} = $fineperiodo[($n_t - 1)];
${"appartamento".$n_t} = $appartamento[($n_t - 1)];
${"nometipotariffa".$n_t} = $nometipotariffa[($n_t - 1)];
${"numpersone".$n_t} = $numpersone[($n_t - 1)];
${"assegnazioneapp".$n_t} = $assegnazioneapp[($n_t - 1)];
${"num_app_richiesti".$n_t} = $num_app_richiesti[($n_t - 1)];
${"lista_app".$n_t} = $lista_app[($n_t - 1)];
${"spezzetta".$n_t} = $spezzetta[($n_t - 1)];
${"prenota_vicine".$n_t} = $prenota_vicine_vett[$n_t];
${"num_letti_agg_max".$n_t} = $num_letti_agg_max[($n_t - 1)];
$dati_extra_corr = explode(",",$dati_extra[($n_t - 1)]);
${"diff_persone".$n_t} = $dati_extra_corr[0];
${"interrompi_vicine_ogni".$n_t} = $dati_extra_corr[1];
} # fine for $n_t
unset($id_prenota_temp);
$prenota_temp = risul_query($dati_transazione,0,'dati_transazione13');
if ($prenota_temp) {
$prenota_temp = explode(", ,",$prenota_temp);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$prenota_temp2 = explode(",",$prenota_temp[($n_t - 1)]);
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
$id_prenota_temp[$n_t][$num1] = aggslashdb($prenota_temp2[($num1 - 1)]);
} # fine for $num1
} # fine for $n_t
} # fine if ($prenota_temp)
if ($occ_app_agenzia == "SI") $spezzetta1 = "occ_app_agenzia";
else {
$dati_transazione20 = explode(", ,",risul_query($dati_transazione,0,'dati_transazione20'));
$n_tronchi1 = $dati_transazione20[0];
$vet_appartamenti_u = $dati_transazione20[1];
$vett_idinizio_u = $dati_transazione20[2];
$vett_idfine_u = $dati_transazione20[3];
} # fine else if ($occ_app_agenzia == "SI")
if ($idospiti_transazione) $idospiti = unserialize($idospiti_transazione);
if ($numordine_transazione) $num_ordine = unserialize($numordine_transazione);
if ($parentela_transazione) $parentela = unserialize($parentela_transazione);
unset($inizioperiodo);
unset($fineperiodo);
unset($appartamento);
unset($nometipotariffa);
unset($numpersone);
unset($assegnazioneapp);
unset($num_app_richiesti);
unset($lista_app);
unset($spezzetta);

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
$funz_import_reservations("","",$file_interconnessioni,$anno,$PHPR_TAB_PRE,2,$id_utente,$HOSTNAME);
$id_utente = $id_utente_origi;
} # fine if ($ic_present[$interconnection_name] == "SI")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))

} # fine else if ($tipo_transazione != "ins_p")


if ($priv_ins_multiple == "n") {
if ($num_tipologie) $num_tipologie = 1;
$num_app_richiesti1 = 1;
} # fine if ($priv_ins_multiple == "n")


if ($inserito_nuovo_cliente == "SI") {
$tabelle_lock = array("$tableclienti");
$altre_tab_lock = array("$tablepersonalizza");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
if (${"numpersone".$n_t}) {
$num_persone_tot[$n_t] = ${"numpersone".$n_t};
if (${"num_letti_agg_max".$n_t}) $num_persone_tot[$n_t] = $num_persone_tot[$n_t] + ${"num_letti_agg_max".$n_t};
} # fine if (${"numpersone".$n_t})
else $num_persone_tot[$n_t] = 0;
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
$max_num_ordine = 2;
$idclienti_compagni[$n_t][$num1] = ",";
if ($cliente_ospite == "SI" and $prenota_cli_osp == "p".$num1."_".$n_t) {
$max_num_ordine = 3;
$idclienti_compagni[$n_t][$num1] = ",$idclienti,";
$num_ospiti[$n_t][$num1]++;
$idospiti[$n_t][$num1][$num_ospiti[$n_t][$num1]] = $idclienti;
$num_ordine[$n_t][$num1][$num_ospiti[$n_t][$num1]] = "1";
} # fine if ($cliente_ospite == "SI" and $prenota_cli_osp == "p".$num1."_".$n_t)
for ($num2 = 1 ; $num2 <= $num_persone_tot[$n_t] ; $num2++) {
$suff = "_".$num2."_".$num1."_".$n_t;
if (${"cognome".$suff}) {
$num_ospiti[$n_t][$num1]++;
$num_osp = $num_ospiti[$n_t][$num1];
$cognome_aux = ${"cognome".$suff};
$nome_aux = ${"nome".$suff};
$sesso_aux = ${"sesso".$suff};
$mesenascita_aux = ${"mesenascita".$suff};
$giornonascita_aux = ${"giornonascita".$suff};
$annonascita_aux = ${"annonascita".$suff};
$nazionenascita_aux = ${"nazionenascita".$suff};
$cittanascita_aux = ${"cittanascita".$suff};
$regionenascita_aux = ${"regionenascita".$suff};
$documento_aux = ${"documento".$suff};
$tipodoc_aux = ${"tipodoc".$suff};
$mesescaddoc_aux = ${"mesescaddoc".$suff};
$giornoscaddoc_aux = ${"giornoscaddoc".$suff};
$annoscaddoc_aux = ${"annoscaddoc".$suff};
$cittadoc_aux = ${"cittadoc".$suff};
$regionedoc_aux = ${"regionedoc".$suff};
$nazionedoc_aux = ${"nazionedoc".$suff};
$nazionalita_aux = ${"nazionalita".$suff};
$lingua_cli_aux = ${"lingua_cli".$suff};
$nazione_aux = ${"nazione".$suff};
$citta_aux = ${"citta".$suff};
$regione_aux = ${"regione".$suff};
$via_aux = ${"via".$suff};
$nomevia_aux = ${"nomevia".$suff};
$numcivico_aux = ${"numcivico".$suff};
$cap_aux = ${"cap".$suff};
$telefono_aux = ${"telefono".$suff};
$telefono2_aux = ${"telefono2".$suff};
$telefono3_aux = ${"telefono3".$suff};
$fax_aux = ${"fax".$suff};
$email_aux = ${"email".$suff};
$cod_fiscale_aux = ${"cod_fiscale".$suff};
$partita_iva_aux = ${"partita_iva".$suff};
$idospiti[$n_t][$num1][$num_osp] = inserisci_dati_cliente($cognome_aux,$nome_aux,"",$titolo_cli_aux,$sesso_aux,$mesenascita_aux,$giornonascita_aux,$annonascita_aux,$nazionenascita_aux,$cittanascita_aux,$regionenascita_aux,$documento_aux,$tipodoc_aux,$mesescaddoc_aux,$giornoscaddoc_aux,$annoscaddoc_aux,$cittadoc_aux,$regionedoc_aux,$nazionedoc_aux,$nazionalita_aux,$lingua_cli_aux,$nazione_aux,$citta_aux,$regione_aux,$via_aux,$nomevia_aux,$numcivico_aux,$cap_aux,$telefono_aux,$telefono2_aux,$telefono3_aux,$fax_aux,$email_aux,$cod_fiscale_aux,$partita_iva_aux,$max_num_ordine,$id_utente_ins,$attiva_prefisso_clienti,$prefisso_clienti);
$num_ordine[$n_t][$num1][$num_osp] = $max_num_ordine;
if ($max_num_ordine > 2) {
if (@get_magic_quotes_gpc()) ${"parentela".$suff} = stripslashes(${"parentela".$suff});
$parentela[$n_t][$num1][$num_osp] = htmlspecialchars(${"parentela".$suff});
} # fine if ($max_num_ordine > 2)
$idclienti_compagni[$n_t][$num1] .= $idospiti[$n_t][$num1][$num_osp].",";
$max_num_ordine++;
} # fine if (${"cognome".$suff})
} # fine for $num2
} # fine for $num1
} # fine for $n_t
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
for ($num2 = 1 ; $num2 <= $num_ospiti[$n_t][$num1] ; $num2++) {
esegui_query("update $tableclienti set idclienti_compagni = '".str_replace(",".$idospiti[$n_t][$num1][$num2].",",",",$idclienti_compagni[$n_t][$num1])."' where idclienti = '".$idospiti[$n_t][$num1][$num2]."' ");
} # fine for $num2
} # fine for $num1
} # fine for $n_t
unlock_tabelle($tabelle_lock);
if ($idospiti) esegui_query("update $tabletransazioni set dati_transazione15 = '".aggslashdb(serialize($idospiti))."', dati_transazione16 = '".aggslashdb(serialize($num_ordine))."', dati_transazione17 = '".aggslashdb(serialize($parentela))."' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
} # fine if ($inserito_nuovo_cliente == "SI")

# Se si viene da clienti.php e si è selezionato un cliente esistente
if (@get_magic_quotes_gpc()) $idclienti = stripslashes($idclienti);
$idclienti = htmlentities($idclienti);
$fr_idclienti = mex("Utilizza il cliente","clienti.php");
if (str_replace(htmlentities($fr_idclienti)." ","",$idclienti) != $idclienti or str_replace($fr_idclienti." ","",$idclienti) != $idclienti) {
$idclienti = str_replace(htmlentities($fr_idclienti)." ","",$idclienti);
$idclienti = str_replace($fr_idclienti." ","",$idclienti);
$idclienti = str_replace(" ".htmlentities(mex("per la prenotazione","clienti.php")),"",$idclienti);
$idclienti = str_replace(" ".mex("per la prenotazione","clienti.php"),"",$idclienti);
$idclienti = aggslashdb($idclienti);
$cliente_ospite = ${"cliente_ospite_".$idclienti};
$prenota_cli_osp = ${"prenota_cli_osp_".$idclienti};
unset($max_num_ordine);
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
for ($num1 = 1 ; $num1 <= ${"num_app_richiesti".$n_t} ; $num1++) {
if ($cliente_ospite == "SI" and $prenota_cli_osp == "p".$num1."_".$n_t) {
$max_num_ordine[$n_t][$num1] = 3;
$num_ospiti[$n_t][$num1]++;
$idospiti[$n_t][$num1][$num_ospiti[$n_t][$num1]] = $idclienti;
$num_ordine[$n_t][$num1][$num_ospiti[$n_t][$num1]] = "1";
} # fine ($cliente_ospite == "SI" and $prenota_cli_osp == "p".$num1."_".$n_t)
else $max_num_ordine[$n_t][$num1] = 2;
} # fine for $num1
} # fine for $n_t
$tabelle_lock = array($tableclienti);
$tabelle_lock = lock_tabelle($tabelle_lock);
esegui_query("update $tableclienti set max_num_ordine = '1' where idclienti = '$idclienti'  ");
$condizione_utente = "";
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$condizione_utente = "and ( utente_inserimento = '$id_utente'";
if ($vedi_clienti == "GRUPPI") {
foreach ($utenti_gruppi as $idut_gr => $val) if ($idut_gr != $id_utente) $condizione_utente .= " or utente_inserimento = '$idut_gr'";
} # fine if ($vedi_clienti == "GRUPPI")
$condizione_utente .= " )";
} # fine if ($vedi_clienti == "PROPRI" or...
if ($vedi_clienti == "NO") $condizione_utente = "and utente_inserimento = '-1'";
$clienti_compagni = esegui_query("select * from $tableclienti where idclienti_compagni $LIKE '%,".$idclienti.",%' $condizione_utente order by max_num_ordine");
$num_clienti_compagni = numlin_query($clienti_compagni);
for ($num1 = 0 ; $num1 < $num_clienti_compagni ; $num1++) {
$id_clienti_comp = risul_query($clienti_compagni,$num1,'idclienti');
if (${"ospite_".$idclienti."_".$id_clienti_comp} == "SI") {
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
for ($num2 = 1 ; $num2 <= ${"num_app_richiesti".$n_t} ; $num2++) {
if (${"pren_osp_".$idclienti."_".$id_clienti_comp} == "p$num2"."_$n_t") {
$num_ospiti[$n_t][$num2]++;
$num_osp = $num_ospiti[$n_t][$num2];
$idospiti[$n_t][$num2][$num_osp] = $id_clienti_comp;
$num_ordine[$n_t][$num2][$num_osp] = $max_num_ordine[$n_t][$num2];
if ($num_ordine[$n_t][$num2][$num_osp] < risul_query($clienti_compagni,$num1,'max_num_ordine')) {
esegui_query("update $tableclienti set max_num_ordine = '".aggslashdb($num_ordine[$n_t][$num2][$num_osp])."' where idclienti = '$id_clienti_comp' ");
} # fine if ($num_ordine[$n_t][$num2][$num_osp] < risul_query($clienti_compagni,$num1,'max_num_ordine'))
$max_num_ordine[$n_t][$num2]++;
} # fine if (${"pren_osp_".$idclienti."_".$id_clienti_comp} == "p$num2"."_$n_t")
} # fine for $num2
} # fine for $n_t
} # fine if (${"ospite_".$idclienti."_".$id_clienti_comp} == "SI")
} # fine for $num1
unlock_tabelle($tabelle_lock);
if ($idospiti) esegui_query("update $tabletransazioni set dati_transazione15 = '".aggslashdb(serialize($idospiti))."', dati_transazione16 = '".aggslashdb(serialize($num_ordine))."' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
} # fine if (str_replace(htmlentities($fr_idclienti)." ","",$idclienti) != $idclienti or...


for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$inizioperiodo = ${"inizioperiodo".$n_t};
$fineperiodo = ${"fineperiodo".$n_t};
$appartamento = ${"appartamento".$n_t};
$num_app_richiesti = ${"num_app_richiesti".$n_t};
$spezzetta = ${"spezzetta".$n_t};

if ($spezzetta) {

# Se si inseriscono più prenotazioni dello stesso tipo
if ($num_app_richiesti > 1 or $num_tipologie > 1) {
${"n_tronchi".$n_t} = $num_app_richiesti;
$vet_appartamenti_u = ",".$appartamento;
$vett_idinizio_u = ",".$inizioperiodo;
$vett_idfine_u = ",".$fineperiodo;
} # fine if ($num_app_richiesti > 1 or $num_tipologie > 1)

# Se esiste $n_tronchi si è deciso di andare avanti
if (${"n_tronchi".$n_t}) {
$vet_appartamenti[$n_t] = explode(",",$vet_appartamenti_u);
$vett_idinizio[$n_t] = explode(",",$vett_idinizio_u);
$vett_idfine[$n_t] = explode(",",$vett_idfine_u);
} # fine if (${"n_tronchi".$n_t})

else {
# Tento di spezzare la prenotazione solo se ne è stata richiesta una sola. Quindi
# n_tronchi = num_app_richiesti se num_app_richiesti != 1 e se, dopo questo else,
# num_app_richiesti = 1 e n_tronchi != 1 allora la prenotazione è stata spezzata.

${"assegnazioneapp".$n_t} = "v";
${"lista_app".$n_t} = "";
include("./includes/liberasettimane.php");
include("./includes/spezzaprenota.php");

$tabelle_lock = array("$tableprenota","$tabletransazioni");
$altre_tab_lock = array("$tableappartamenti","$tableperiodi","$tableregole","$tablepersonalizza");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

# Se ci sono regole per $app_agenzia inserisco prenotazioni fisse in $app_prenota_id
# e controllo la situazione con spezzaprenota
$app_agenzia = esegui_query("select * from $tableregole where app_agenzia != ''");
$num_app_agenzia = numlin_query($app_agenzia);
if ($num_app_agenzia != 0 and $spezzetta != "occ_app_agenzia") {
unset($limiti_var);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($profondita);
$limiti_var['n_ini'] = $inizioperiodo;
$limiti_var['n_fine'] = $fineperiodo;
$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
if (numlin_query($max_prenota) != 0) $tot_prenota = risul_query($max_prenota,0,0);
else $tot_prenota = 0;
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var ($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
$info_periodi['numero'] = $num_app_agenzia;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$info_periodi['app'][$num1] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi['ini'][$num1] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi['fine'][$num1] = risul_query($app_agenzia,$num1,'iddatafine');
} # fine for $num1
inserisci_prenota_fittizie($info_periodi,$profondita,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett,$app_assegnabili_id);

spezzaprenota($inizioperiodo,$fineperiodo,$anno,$limiti_var,$profondita,$n_tronchi,$vet_appartamenti,$vett_idinizio,$vett_idfine,$numpersone1,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");

if ($n_tronchi != -1) {
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $n_tronchi = -1;
} # fine if ($n_tronchi != -1)

if ($n_tronchi != 1) {
$torna_invece_di_ok = "SI";
echo mex("Non si può inserire la prenozione senza utilizzare gli appartamenti della regola di assegnazione 1",'unit.php');
if ($n_tronchi > 1) echo mex(" o spezzarla",$pag);
echo ".<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"occ_app_agenzia\" value=\"SI\">";
$manda_dati_assegnazione = "NO";
include ("./includes/dati_form_prenotazione.php");
echo "
<input class=\"sbutton\" type=\"submit\" name=\"utilizza\" value=\"".mex("Utilizza anche gli appartamenti della regola 1",'unit.php')."\"><br>
</div></form>";
if ($n_tronchi > 1) {
unset($vet_appartamenti_u);
unset($vett_idinizio_u);
unset($vett_idfine_u);
for ($num1 = 1 ; $num1 <= $n_tronchi ; $num1 = $num1 + 1) {
$vet_appartamenti_u = $vet_appartamenti_u . "," . $vet_appartamenti[$num1];
$vett_idinizio_u = $vett_idinizio_u . "," . $vett_idinizio[$num1];
$vett_idfine_u = $vett_idfine_u . "," . $vett_idfine[$num1];
} # fine for $num1
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
esegui_query("update $tabletransazioni set dati_transazione20 = '$n_tronchi, ,$vet_appartamenti_u, ,$vett_idinizio_u, ,$vett_idfine_u' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
$manda_dati_assegnazione = "NO";
include ("./includes/dati_form_prenotazione.php");
echo "
<input class=\"sbutton\" type=\"submit\" name=\"spezza_senza_app_agenzia\" value=\"".mex("Spezza la prenotazione in",$pag)." $n_tronchi ".mex("parti",$pag)."\">
(".mex("senza utilizzare gli appartamenti della regola 1",'unit.php').").<br>
</div></form>";
} # fine if ($n_tronchi > 1)
} # fine if ($n_tronchi != 1)
} # fine if ($num_app_agenzia != 0 and $spezzetta != "occ_app_agenzia")

# Se non vi sono regole per $app_agenzia o si è deciso di ignorarle.
if ($spezzetta == "occ_app_agenzia" or $num_app_agenzia == 0) {
unset($limiti_var);
unset($app_prenota_id);
unset($app_orig_prenota_id);
unset($inizio_prenota_id);
unset($fine_prenota_id);
unset($app_assegnabili_id);
unset($prenota_in_app_sett);
unset($dati_app);
unset($profondita);
$limiti_var['n_ini'] = $inizioperiodo;
$limiti_var['n_fine'] = $fineperiodo;
$profondita['iniziale'] = "";
$profondita['attuale'] = 1;
$max_prenota = esegui_query("select max(idprenota) from $tableprenota");
if (numlin_query($max_prenota) != 0) $tot_prenota = risul_query($max_prenota,0,0);
else $tot_prenota = 0;
$profondita['tot_prenota_ini'] = $tot_prenota;
$profondita['tot_prenota_attuale'] = $tot_prenota;
tab_a_var($limiti_var,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$anno,$dati_app,$profondita,$PHPR_TAB_PRE."prenota");
unset($info_periodi);
$info_periodi['numero'] = 0;
for ($num1 = 0 ; $num1 < $num_app_agenzia ; $num1++) {
$mot2 = risul_query($app_agenzia,$num1,'motivazione2');
if ($mot2 == "x") {
$info_periodi['app'][$info_periodi['numero']] = risul_query($app_agenzia,$num1,'app_agenzia');
$info_periodi['ini'][$info_periodi['numero']] = risul_query($app_agenzia,$num1,'iddatainizio');
$info_periodi['fine'][$info_periodi['numero']] = risul_query($app_agenzia,$num1,'iddatafine');
$info_periodi['numero']++;
} # fine if ($mot2 == "x")
} # fine for $num1
if ($info_periodi['numero']) inserisci_prenota_fittizie($info_periodi,$profondita,$app_prenota_id,$inizio_prenota_id,$fine_prenota_id,$prenota_in_app_sett,$app_assegnabili_id);

spezzaprenota($inizioperiodo,$fineperiodo,$anno,$limiti_var,$profondita,$n_tronchi,$vet_appartamenti,$vett_idinizio,$vett_idfine,$numpersone1,$app_prenota_id,$app_orig_prenota_id,$inizio_prenota_id,$fine_prenota_id,$app_assegnabili_id,$prenota_in_app_sett,$dati_app,$PHPR_TAB_PRE."prenota");

if ($n_tronchi != -1) {
$risul_agg = aggiorna_tableprenota($app_prenota_id,$app_orig_prenota_id,$tableprenota);
if (!$risul_agg) $n_tronchi = -1;
} # fine if ($n_tronchi != -1)

if ($n_tronchi == -1) {
echo mex("Non é stato possibile dividere la prenotazione a causa del numero di persone",$pag).".<br>";
} # fine else if ($n_tronchi == -1)

if ($n_tronchi == 1) {
${"n_tronchi".$n_t} = 1;
$appartamento = $vet_appartamenti[1];
unset($vet_appartamenti);
unset($vett_idinizio);
unset($vett_idfine);
$vet_appartamenti[$n_t][1] = $appartamento;
$vett_idinizio[$n_t][1] = $inizioperiodo;
$vett_idfine[$n_t][1] = $fineperiodo;
} # fine if ($n_tronchi == 1)

if ($n_tronchi > 1) {
$torna_invece_di_ok = "SI";
for ($num1 = 1 ; $num1 <= $n_tronchi ; $num1 = $num1 + 1) {
$vet_appartamenti_u = $vet_appartamenti_u . "," . $vet_appartamenti[$num1];
$vett_idinizio_u = $vett_idinizio_u . "," . $vett_idinizio[$num1];
$vett_idfine_u = $vett_idfine_u . "," . $vett_idfine[$num1];
} # fine for $num1
echo mex("Non è possibile inserire la prenotazione senza",$pag)." <div style=\"display: inline; color: red;\">".mex("dividerla",$pag)."</div> ".mex("in",$pag)." <b>$n_tronchi</b> ".mex("parti",$pag).".<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"idclienti\" value=\"$idclienti\">
<input type=\"hidden\" name=\"origine\" value=\"$origine\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
esegui_query("update $tabletransazioni set dati_transazione20 = '$n_tronchi, ,$vet_appartamenti_u, ,$vett_idinizio_u, ,$vett_idfine_u' where idtransazioni = '$id_transazione' and idsessione = '$id_sessione'");
$manda_dati_assegnazione = "NO";
include ("./includes/dati_form_prenotazione.php");
echo "
<input class=\"sbutton\" type=\"submit\" name=\"spezza_senza_app_agenzia\" value=\"".mex("Spezza la prenotazione in",$pag)." $n_tronchi ".mex("parti",$pag)."\"><br>
</div></form>";
} # fine if ($n_tronchi > 1)
} # fine if ($spezzetta == "occ_app_agenzia" or $num_app_agenzia == 0)

unlock_tabelle($tabelle_lock);

} # fine else if (${"n_tronchi".$n_t})
} # fine if ($spezzetta)


else {
${"n_tronchi".$n_t} = 1;
$vet_appartamenti[$n_t][1] = $appartamento;
$vett_idinizio[$n_t][1] = $inizioperiodo;
$vett_idfine[$n_t][1] = $fineperiodo;
} # fine else if ($spezzetta)

} # fine for $n_t



if (!$torna_invece_di_ok) {


$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if ($idmessaggi) $tabelle_lock = array($tableprenota,$tablecostiprenota,$tablerclientiprenota,$tablemessaggi,$tablerelinventario);
else $tabelle_lock = array($tableprenota,$tablecostiprenota,$tablerclientiprenota,$tablerelinventario);
#if (@is_file($file_interconnessioni)) $tabelle_lock[count($tabelle_lock)] = $tablecache;
$altre_tab_lock = array($tablenometariffe,$tableperiodi,$tableappartamenti,$tableclienti,$tableregole,$tablepersonalizza);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$continuare = "SI";

$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$idclienti = aggslashdb($idclienti);
$dati_cliente = esegui_query("select * from $tableclienti where idclienti = '$idclienti'");
if (numlin_query($dati_cliente) == 0) $continuare = "NO";
else {
$cognome = risul_query($dati_cliente,0,'cognome');
$utente_inserimento_cliente = risul_query($dati_cliente,0,'utente_inserimento');
if ($inserito_nuovo_cliente == "NO" and ($vedi_clienti == "NO" or ($vedi_clienti == "PROPRI" and $utente_inserimento_cliente != $id_utente) or ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento_cliente]))) $continuare = "NO";
} # fine else if (numlin_query($dati_cliente) == 0)

unset($num_costi_presenti);
unset($beniinv_presenti);
if (!function_exists('dati_tariffe')) include("./includes/funzioni_tariffe.php");
if (!function_exists('dati_costi_agg_ntariffe')) include("./includes/funzioni_costi_agg.php");
$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num'],"NO","",$tableappartamenti);
$num_prenota_tot = 0;
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) $num_prenota_tot = $num_prenota_tot + ${"n_tronchi".$n_t};


for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$numpersone = ${"numpersone".$n_t};
$n_tronchi = ${"n_tronchi".$n_t};
for ($numca = 1 ; $numca <= $numcostiagg ; $numca++) {
${"costoagg".$numca} = aggslashdb(${"costoagg".$numca."_".$n_t});
${"idcostoagg".$numca} = aggslashdb(${"idcostoagg".$numca."_".$n_t});
${"numsettimane".$numca} = aggslashdb(${"numsettimane".$numca."_".$n_t});
${"nummoltiplica_ca".$numca} = aggslashdb(${"nummoltiplica_ca".$numca."_".$n_t});
${"id_periodi_costo".$numca} = aggslashdb(${"id_periodi_costo".$numca."_".$n_t});
} # fine for $numca

for ($num1 = 1 ; $num1 <= $n_tronchi ; $num1 = $num1 + 1) {
$appartamento = $vet_appartamenti[$n_t][$num1];
$inizioperiodo = $vett_idinizio[$n_t][$num1];
$idinizioperiodo = $inizioperiodo;
$fineperiodo = $vett_idfine[$n_t][$num1];
$idfineperiodo = $fineperiodo;
$data_inizio = esegui_query("select * from  $tableperiodi where idperiodi = '$inizioperiodo'");
$data_inizio = risul_query($data_inizio,0,'datainizio');
$data_inizio_f[$n_t] = formatta_data($data_inizio,$stile_data);
$data_fine = esegui_query("select * from  $tableperiodi where idperiodi = '$fineperiodo'");
$data_fine = risul_query($data_fine,0,'datafine');
$data_fine_f[$n_t] = formatta_data($data_fine,$stile_data);
$lunghezza_periodo = $fineperiodo - $inizioperiodo + 1;

if ($id_prenota_temp[$n_t][$num1]) {
$prenota_temp_esistente = esegui_query("select idappartamenti from $tableprenota where idprenota = '".$id_prenota_temp[$n_t][$num1]."' and idclienti = '0' and assegnazioneapp = '".aggslashdb(${"assegnazioneapp".$n_t})."' ");
if (numlin_query($prenota_temp_esistente) == 1) {
$vet_appartamenti[$n_t][$num1] = risul_query($prenota_temp_esistente,0,'idappartamenti');
$appartamento = $vet_appartamenti[$n_t][$num1];
esegui_query("delete from $tableprenota where idprenota = '".$id_prenota_temp[$n_t][$num1]."' ","",1);
} # fine if (numlin_query($prenota_temp_esistente) == 1)
} # fine ($id_prenota_temp[$n_t][$num1])

$prenota_gia_esistente = esegui_query("select * from $tableprenota where idappartamenti = '$appartamento' and iddatainizio <= $fineperiodo and iddatafine >= $inizioperiodo");
$prenota_gia_esistente = numlin_query($prenota_gia_esistente);
if ($prenota_gia_esistente != 0) {
echo "<br><div style=\"display: inline; color: red;\"><b>".mex("Non si è potuto inserire la prenotazione a nome di",$pag)." $cognome ".mex("dal",$pag)." ".$data_inizio_f[$n_t]." ".mex("al",$pag)." ".$data_fine_f[$n_t]."
 ".mex("perchè il database è stato modificato nel frattempo",$pag).".</b></div><br><hr style=\"width: 95%\">";
$continuare = "NO";
} # fine if ($prenota_gia_esistente != 0)

if ($priv_ins_periodi_passati != "s") {
$id_periodo_corrente = calcola_id_periodo_corrente($anno);
if ($id_periodo_corrente >= $inizioperiodo) $continuare = "NO";
} # fine if ($priv_ins_periodi_passati != "s")
if ($fineperiodo < $inizioperiodo) $continuare = "NO";

#if ($priv_ins_num_persone != "s") unset($numpersone);
if ($numpersone and controlla_num_pos($numpersone) != "SI") $continuare = "NO";
${"numpersone".$n_t} = $numpersone;

$appartamento_esistente = esegui_query("select idappartamenti,maxoccupanti from $tableappartamenti where idappartamenti = '$appartamento'");
if (numlin_query($appartamento_esistente) != 1) {
echo "<br><div style=\"display: inline; color: red;\"><b>".mex("Non si è potuto inserire la prenotazione a nome di",$pag)." $cognome ".mex("dal",$pag)." ".$data_inizio_f[$n_t]." ".mex("al",$pag)." ".$data_fine_f[$n_t]."
 ".mex("perchè l'appartamento assegnato non esiste più",'unit.php').".</b></div><br><hr style=\"width: 95%\">";
$continuare = "NO";
} # fine if (numlin_query($appartamento_esistente) != 1)
else {
$maxoccupanti = risul_query($appartamento_esistente,0,'maxoccupanti');
if ($maxoccupanti and $numpersone > $maxoccupanti) $continuare = "NO";
} # fine else if (numlin_query($appartamento_esistente) != 1)

$appartamento_chiuso = esegui_query("select idregole from $tableregole where iddatainizio <= '$fineperiodo' and iddatafine >= '$inizioperiodo' and app_agenzia = '$appartamento' and motivazione2 = 'x' ");
if (numlin_query($appartamento_chiuso)) $continuare = "NO";

if (${"assegnazioneapp".$n_t} == "c" and str_replace(",".$appartamento.",","",",".${"lista_app".$n_t}.",") == ",".${"lista_app".$n_t}.",") $continuare = "NO";

if ($attiva_regole1_consentite == "s") {
if (${"assegnazioneapp".$n_t} != "k" and ${"assegnazioneapp".$n_t} != "c") $continuare = "NO";
if (${"assegnazioneapp".$n_t} == "k") $appartameti_in_lista[0] = $appartamento;
if (${"assegnazioneapp".$n_t} == "c") $appartameti_in_lista = explode(",",${"lista_app".$n_t});
for ($n_lista = 0 ; $n_lista < count($appartameti_in_lista) ; $n_lista++) {
$appartamento_lista = $appartameti_in_lista[$n_lista];
$motivazioni_regola1 = esegui_query("select motivazione,iddatainizio,iddatafine from $tableregole where iddatainizio <= '$fineperiodo' and iddatafine >= '$inizioperiodo' and app_agenzia = '$appartamento_lista' and (motivazione2 != 'x' or motivazione2 is NULL) order by iddatainizio");
if (numlin_query($motivazioni_regola1) == 0) $continuare = "NO";
else {
unset($motivazioni_consentite);
for ($num2 = 0 ; $num2 < count($regole1_consentite) ; $num2++) $motivazioni_consentite[$regole1_consentite[$num2]] = "SI";
$iddatainizio_regole_tot = risul_query($motivazioni_regola1,0,'iddatainizio');
$iddatafine_regole_tot = risul_query($motivazioni_regola1,0,'iddatafine');
$motivazione = risul_query($motivazioni_regola1,0,'motivazione');
if (!$motivazione) $motivazione = " ";
if (!$motivazioni_consentite[$motivazione]) $continuare = "NO";
for ($num2 = 1 ; $num2 < numlin_query($motivazioni_regola1) ; $num2++) {
$motivazione = risul_query($motivazioni_regola1,$num2,'motivazione');
if (!$motivazione) $motivazione = " ";
if (!$motivazioni_consentite[$motivazione]) $continuare = "NO";
$iddatainizio_regola = risul_query($motivazioni_regola1,$num2,'iddatainizio');
if ($iddatainizio_regola == ($iddatafine_regole_tot + 1)) $iddatafine_regole_tot = risul_query($motivazioni_regola1,$num2,'iddatafine');
else $continuare = "NO";
} # fine for $num2
if ($iddatainizio_regole_tot > $inizioperiodo or $iddatafine_regole_tot < $fineperiodo) $continuare = "NO";
} # fine else if (numlin_query($motivazioni_regola1) == 0)
} # fine for $n_lista
} # fine if ($attiva_regole1_consentite == "s")

if (!$numpersone and $dati_tariffe[${"nometipotariffa".$n_t}]['moltiplica'] == "p") $continuare = "NO";

if (($attiva_tariffe_consentite == "s" and $tariffe_consentite_vett[substr(${"nometipotariffa".$n_t},7)] != "SI") or substr(${"nometipotariffa".$n_t},0,7) != "tariffa") $continuare = "NO";
for ($num2 = $inizioperiodo; $num2 <= $fineperiodo; $num2++) {
$rigasettimana = esegui_query("select * from $tableperiodi where idperiodi = '$num2' ");
$nometipotariffa_aux = ${"nometipotariffa".$n_t};
$esistetariffa = risul_query($rigasettimana,0,$nometipotariffa_aux);
$nometipotariffa_aux = ${"nometipotariffa".$n_t}."p";
$esistetariffap = risul_query($rigasettimana,0,$nometipotariffa_aux);
if ((!strcmp($esistetariffa,"") or $esistetariffa < 0) and (!strcmp($esistetariffap,"") or $esistetariffap < 0)) $continuare = "NO";
if ($dati_tariffe[${"nometipotariffa".$n_t}]['chiusa'][$num2]) $continuare = "NO";
} # fine for $num2

$costi_aggiuntivi_sbagliati = "NO";
unset($id_costi_presenti);
unset($num_letti_agg);
${"numcostiagg_".$n_t."t".$num1} = $numcostiagg;
for ($num2 = 0 ; $num2 < $dati_ca['num'] ; $num2++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num2]['id']] == "SI") {
if ($dati_ca[$num2]["tipo_associa_".${"nometipotariffa".$n_t}] == "r") $periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num2,$idinizioperiodo,$idfineperiodo,1);
if ($dati_ca[$num2]["tipo_associa_".${"nometipotariffa".$n_t}] == "s" or ($dati_ca[$num2]["tipo_associa_".${"nometipotariffa".$n_t}] == "r" and $periodo_costo_trovato != "NO")) {
$nometipotariffa_aux = ${"nometipotariffa".$n_t};
if (associa_costo_a_tariffa($dati_ca,$num2,$nometipotariffa_aux,$lunghezza_periodo) == "SI") {
${"numcostiagg_".$n_t."t".$num1}++;
${"costoagg".(${"numcostiagg_".$n_t."t".$num1})} = "SI";
${"idcostoagg".(${"numcostiagg_".$n_t."t".$num1})} = $dati_ca[$num2]['id'];
} # fine if (associa_costo_a_tariffa($dati_ca,$num2,${"nometipotariffa".$n_t},$lunghezza_periodo) == "SI")
else {
if ($dati_ca[$num2]["tipo_associa_".${"nometipotariffa".$n_t}] == "r" and $dati_ca[$num2]['tipo'] == "s") {
$sett_costo = calcola_settimane_costo($tableperiodi,$dati_ca,$num2,$idinizioperiodo,$idfineperiodo,"","");
if ($sett_costo) $costi_aggiuntivi_sbagliati = "SI";
} # fine if ($dati_ca[$num2]["tipo_associa_".${"nometipotariffa".$n_t}] == "r" and...
else $costi_aggiuntivi_sbagliati = "SI";
} # fine else if (associa_costo_a_tariffa($dati_ca,$num2,${"nometipotariffa".$n_t},$lunghezza_periodo) == "SI")
} # fine if ($dati_ca[$num2]["tipo_associa_".$nometipotariffa] == "s" or...
} # fine if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num2]['id']] == "SI")
} # fine for $num2
for ($numca = 1 ; $numca <= ${"numcostiagg_".$n_t."t".$num1} ; $numca++) {
$costoagg = "costoagg".$numca;
$idcostoagg = "idcostoagg".$numca;
if (($priv_ins_costi_agg != "s" and $numca <= $numcostiagg) or ($attiva_costi_agg_consentiti != "n" and $costi_agg_consentiti_vett[$$idcostoagg] != "SI")) $$costoagg = "";
${"costoagg".$numca."_".$n_t."t".$num1} = $$costoagg;
if ($$costoagg == "SI") {
$numsettimane = "numsettimane".$numca;
$nummoltiplica_ca = "nummoltiplica_ca".$numca;
${"idcostoagg".$numca."_".$n_t."t".$num1} = $$idcostoagg;
$id_periodi_costo = "id_periodi_costo".$numca;
$num_costo = $dati_ca['id'][$$idcostoagg];
if ($$idcostoagg != $dati_ca[$num_costo]['id']) $costi_aggiuntivi_sbagliati = "SI";
if ($id_costi_presenti[$idcostoagg] == "SI" or ($dati_ca[$num_costo]['mostra'] != "s" and $numca <= $numcostiagg)) $costi_aggiuntivi_sbagliati = "SI";
$id_costi_presenti[$idcostoagg] = "SI";
if ($dati_ca[$num_costo]["incomp_".${"nometipotariffa".$n_t}] == "i") $costi_aggiuntivi_sbagliati = "SI";
if (($$numsettimane and controlla_num_pos($$numsettimane) == "NO") or ($$nummoltiplica_ca and controlla_num_pos($$nummoltiplica_ca) == "NO")) $costi_aggiuntivi_sbagliati = "SI";
else {
if ($$numsettimane) {
$totsettimane = $idfineperiodo - $idinizioperiodo + 1 ;
if ($$numsettimane > $totsettimane) {
if ($num1 == $n_tronchi) $costi_aggiuntivi_sbagliati = "SI";
else $numsettimane_tronco = $totsettimane;
} # fine if ($$numsettimane > $totsettimane)
else $numsettimane_tronco = $$numsettimane;
$$numsettimane = $$numsettimane - $numsettimane_tronco;
} # fine if ($$numsettimane)
} # fine else if (($$numsettimane and controlla_num_pos($$numsettimane) == "NO") or...
#if ($dati_ca[$num_costo][tipo_val] == "q") {
#if ($costo_totale_presente == "SI") $costi_aggiuntivi_sbagliati = "SI";
#$costo_totale_presente = "SI";
#} # fine if ($dati_ca[$num_costo][tipo_val] == "q")
$id_periodi_costo_aux = $$id_periodi_costo;
$settimane_costo_aux = ${"settimane_costo".$numca."_".$n_t."t".$num1};
$nummoltiplica_ca_aux = $$nummoltiplica_ca;
${"settimane_costo".$numca."_".$n_t."t".$num1} = calcola_settimane_costo($tableperiodi,$dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$id_periodi_costo_aux,$numsettimane_tronco);
if (!${"settimane_costo".$numca."_".$n_t."t".$num1} and $dati_ca[$num_costo]['tipo'] == "s" and $dati_ca[$num_costo]['var_numsett'] == "n") ${"costoagg".$numca."_".$n_t."t".$num1} = "";
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo_aux);
if ($periodo_costo_trovato == "NO") $costi_aggiuntivi_sbagliati = "SI";
if (($dati_ca[$num_costo]['moltiplica'] == "p" or $dati_ca[$num_costo]['moltiplica'] == "t") and !$numpersone) $costi_aggiuntivi_sbagliati = "SI";
aggiorna_letti_agg_in_periodi($dati_ca,$num_costo,$num_letti_agg,$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,"",$nummoltiplica_ca_aux,$numpersone);
} # fine if ($$costoagg == "SI")
} # fine for $numca

for ($numca = 1 ; $numca <= ${"numcostiagg_".$n_t."t".$num1} ; $numca++) {
if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI") {
$idcostoagg = "idcostoagg".$numca;
$num_costo = $dati_ca['id'][$$idcostoagg];
$settimane_costo_aux = ${"settimane_costo".$numca."_".$n_t."t".$num1};
$nummoltiplica_ca_aux = ${"nummoltiplica_ca".$numca};
calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica_aux,$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,$nummoltiplica_ca_aux,$numpersone,$num_letti_agg);
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti,$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,$moltiplica_aux) == "NO") $costi_aggiuntivi_sbagliati = "SI";
if (str_replace(",$appartamento,","",",".$dati_ca[$num_costo]['appincompatibili'].",") != ",".$dati_ca[$num_costo]['appincompatibili'].",") $costi_aggiuntivi_sbagliati = "SI";
if ($dati_ca[$num_costo]['tipo_beniinv']) {
$nrc_aux = "";
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti,$nrc_aux,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,$moltiplica_aux,$appartamento);
${"num_ripetizioni_costo".$numca."_".$n_t."t".$num1} = $nrc_aux;
if ($risul != "SI") $costi_aggiuntivi_sbagliati = "SI";
} # fine if ($dati_ca[$num_costo]['tipo_beniinv'])
if ($dati_ca[$num_costo]['moltiplica'] == "c" and $dati_ca[$num_costo]['molt_max'] != "x") {
$num_max = 0;
if ($dati_ca[$num_costo]['molt_max'] == "n") $num_max = $dati_ca[$num_costo]['molt_max_num'];
if ($dati_ca[$num_costo]['molt_max'] != "n" and $numpersone) $num_max = $numpersone;
if ($dati_ca[$num_costo]['molt_max'] == "t" and $num_letti_agg['max']) $num_max += $num_letti_agg['max'];
if ($num_max) {
if ($dati_ca[$num_costo]['molt_max'] != "n" and $dati_ca[$num_costo]['molt_max_num']) $num_max = $num_max - $dati_ca[$num_costo]['molt_max_num'];
if ($nummoltiplica_ca_aux > $num_max) $costi_aggiuntivi_sbagliati = "SI";
} # fine if ($num_max)
} # fine if ($dati_ca[$num_costo]['moltiplica'] == "c" and $dati_ca[$num1]['molt_max'] != "x")
${"moltiplica".$numca."_".$n_t."t".$num1} = $moltiplica_aux;
} # fine if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI")
} # fine for $numca


if ($costi_aggiuntivi_sbagliati == "SI") {
echo "<br><div style=\"display: inline; color: red;\"><b>".mex("Non si è potuto inserire un costo aggiuntivo della prenotazione a nome di",$pag)." $cognome ".mex("dal",$pag)." ".$data_inizio_f[$n_t]." ".mex("al",$pag)." ".$data_fine_f[$n_t]."
 ".mex("perchè il database è stato modificato nel frattempo",$pag).".</b></div><br><hr style=\"width: 95%\">";
$continuare = "NO";
} # fine if ($costi_aggiuntivi_sbagliati == "SI")

else {

if (!$numpersone) $numpersone_costi_poss = 0;
else $numpersone_costi_poss = $numpersone;
$oggi_costo = date("Ymd",(time() + (C_DIFF_ORE * 3600)));
if ($idmessaggi) {
$dati_mess = esegui_query("select datainserimento from $tablemessaggi where tipo_messaggio = 'rprenota' and idutenti $LIKE '%,$id_utente,%' and idmessaggi = '".aggslashdb($idmessaggi)."' and dati_messaggio1 = 'da_inserire' ");
if (numlin_query($dati_mess) == 1) $oggi_costo = str_replace("-","",substr(risul_query($dati_mess,0,'datainserimento'),0,10));
} # fine if ($idmessaggi)

# calcolo costi da associare se possibile
for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num_costo]['id']] == "SI") {
$associa_costo = "NO";
$nometipotariffa_aux = ${"nometipotariffa".$n_t};
$associa_costo_tariffa = associa_costo_a_tariffa($dati_ca,$num_costo,$nometipotariffa_aux,$lunghezza_periodo);
if ($associa_costo_tariffa == "SI" and $dati_ca[$num_costo]["tipo_associa_".${"nometipotariffa".$n_t}] == "p") $associa_costo = "SI";
if ($associa_costo_tariffa != "SI" and !$dati_ca[$num_costo]["incomp_".${"nometipotariffa".$n_t}]) {
if ($dati_ca[$num_costo]['assegna_con_num_prenota'] and $num_prenota_tot >= $dati_ca[$num_costo]['assegna_con_num_prenota']) $associa_costo = "SI";
if ($dati_ca[$num_costo]['assegna_da_ini_prenota']) {
$giorni_lim = substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],1);
$limite = date("Ymd",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) - $giorni_lim),substr($data_inizio,0,4)));
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) == ">" and $oggi_costo < $limite) $associa_costo = "SI";
if (substr($dati_ca[$num_costo]['assegna_da_ini_prenota'],0,1) == "<" and $oggi_costo > $limite) $associa_costo = "SI";
} # fine if ($dati_ca[$num_costo][assegna_da_ini_prenota])
} # fine if ($associa_costo_tariffa != "SI" and...
if ($associa_costo == "SI") {
#if ($dati_ca[$num_costo][tipo_val] == "q" and $costo_totale_presente = "SI") $associa_costo = "NO";
$settimane_costo = calcola_settimane_costo($tableperiodi,$dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,"","");
$num_letti_agg_copia = $num_letti_agg;
$beniinv_presenti_copia = $beniinv_presenti;

if ($dati_ca[$num_costo]['letto'] == "s") {
aggiorna_letti_agg_in_periodi($dati_ca,$num_costo,$num_letti_agg_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo,"","",$numpersone_costi_poss);
unset($moltiplica_copia);
unset($num_costi_presenti_copia);
unset($num_ripetizioni_copia);
for ($numca = 1 ; $numca <= ${"numcostiagg_".$n_t."t".$num1} ; $numca++) {
if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI") {
$num_costo2 = $dati_ca['id'][${"idcostoagg".$numca}];
$settimane_costo_aux = ${"settimane_costo".$numca."_".$n_t."t".$num1};
if ($dati_ca[$num_costo2]['moltiplica'] != "t") $moltiplica_copia[$numca] = ${"moltiplica".$numca."_".$n_t."t".$num1};
else calcola_moltiplica_costo($dati_ca,$num_costo2,$moltiplica_copia[$numca],$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,"",$numpersone,$num_letti_agg_copia);
if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo2,$num_costi_presenti_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,$moltiplica_copia[$numca]) == "NO") $associa_costo = "NO";
if ($dati_ca[$num_costo2]['moltiplica'] == "t") {
$nrc_aux = ${"num_ripetizioni_costo".$numca."_".$n_t."t".$num1};
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo2,$beniinv_presenti_copia,$num_ripetizioni_copia[$numca],"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo_aux,$moltiplica_copia[$numca],$appartamento,$nrc_aux);
if ($risul != "SI") $associa_costo = "NO";
} # fine if ($dati_ca[$num_costo2]['moltiplica'] == "t")
} # fine if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI")
} # fine for $numca
} # fine if ($dati_ca[$num_costo][letto] == "s")
else $num_costi_presenti_copia = $num_costi_presenti;

calcola_moltiplica_costo($dati_ca,$num_costo,$moltiplica,$idinizioperiodo,$idfineperiodo,$settimane_costo,"",$numpersone_costi_poss,$num_letti_agg_copia);
$periodo_costo_trovato = trova_periodo_permesso_costo($dati_ca,$num_costo,$idinizioperiodo,$idfineperiodo,$settimane_costo);
if ($periodo_costo_trovato == "NO") $associa_costo = "NO";
else if (controlla_num_limite_costo($tablecostiprenota,$tableprenota,$dati_ca,$num_costo,$num_costi_presenti_copia,$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica) == "NO") $associa_costo = "NO";
if ($dati_ca[$num_costo]['tipo_beniinv'] and $associa_costo == "SI") {
$num_ripetizioni_costo = "";
$risul = controlla_beni_inventario_costo($tablerelinventario,$dati_ca,$num_costo,$beniinv_presenti_copia,$num_ripetizioni_costo,"SI",$idinizioperiodo,$idfineperiodo,$settimane_costo,$moltiplica,$appartamento);
if ($risul != "SI") $associa_costo = "NO";
} # fine if ($dati_ca[$num_costo]['tipo_beniinv'] and $associa_costo == "SI")

if ($associa_costo == "SI") {
$beniinv_presenti = $beniinv_presenti_copia;
$num_costi_presenti = $num_costi_presenti_copia;
if ($dati_ca[$num_costo]['letto'] == "s") {
$num_letti_agg = $num_letti_agg_copia;
for ($numca = 1 ; $numca <= ${"numcostiagg_".$n_t."t".$num1} ; $numca++) {
if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI") {
$num_costo2 = $dati_ca['id'][${"idcostoagg".$numca}];
${"moltiplica".$numca."_".$n_t."t".$num1} = $moltiplica_copia[$numca];
if ($dati_ca[$num_costo2]['moltiplica'] == "t") ${"num_ripetizioni_costo".$numca."_".$n_t."t".$num1} = $num_ripetizioni_copia[$numca];
} # fine if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI")
} # fine for $numca
} # fine if ($dati_ca[$num_costo][letto] == "s")
${"numcostiagg_".$n_t."t".$num1}++;
$numca = ${"numcostiagg_".$n_t."t".$num1};
${"costoagg".$numca."_".$n_t."t".$num1} = "SI";
${"idcostoagg".$numca."_".$n_t."t".$num1} = $dati_ca[$num_costo]['id'];
${"settimane_costo".$numca."_".$n_t."t".$num1} = $settimane_costo;
${"moltiplica".$numca."_".$n_t."t".$num1} = $moltiplica;
if ($dati_ca[$num_costo]['tipo_beniinv']) ${"num_ripetizioni_costo".$numca."_".$n_t."t".$num1} = $num_ripetizioni_costo;
} # fine if ($associa_costo == "SI")
} # fine if ($associa_costo == "SI")
} # fine if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num_costo]['id']] == "SI")
} # fine for $num_costo

} # fine else if ($costi_aggiuntivi_sbagliati == "SI")


} # fine for $num1
} # fine for $n_t


if ($idmessaggi) {
$mess_esistente = esegui_query("select dati_messaggio2 from $tablemessaggi where tipo_messaggio = 'rprenota' and idutenti $LIKE '%,$id_utente,%' and idmessaggi = '".aggslashdb($idmessaggi)."' and dati_messaggio1 = 'da_inserire' ");
if (numlin_query($mess_esistente) != 1) {
echo "<br><div style=\"display: inline; color: red;\"><b>".mex("Le prenotazioni richieste nel messaggio sono già state inserite",$pag).".</b></div><br>";
$continuare = "NO";
} # fine if (numlin_query($mess_esistente) != 1)
else {
$dati_mess_calc = risul_query($mess_esistente,0,'dati_messaggio2');
$dati_mess_calc = explode(",",$dati_mess_calc);
} # fine else if (numlin_query($mess_esistente) != 1)
} # fine if ($idmessaggi)

if ($continuare == "NO") {
echo "<br>".mex("Nessuna nuova prenotazione è stata inserita",$pag).".<br><br>";
$torna_invece_di_ok = "SI";
} # fine if ($continuare == "NO")



if ($continuare != "NO") {
$lista_idprenota = "";
$lista_idprenota_t = array();
$costo_tot_mess = (double) 0;
$caparra_mess = (double) 0;
$arrotond_predef = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'arrotond_predef' and idutente = '$id_utente'");
$arrotond_predef = risul_query($arrotond_predef,0,'valpersonalizza');
$comm_pers_presenti = array();
$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '$id_utente'");
if (numlin_query($campi_pers_comm) == 1) {
$campi_pers_comm = explode(">",risul_query($campi_pers_comm,0,'valpersonalizza'));
for ($num1 = 0 ; $num1 < count($campi_pers_comm) ; $num1++) $comm_pers_presenti[$campi_pers_comm[$num1]] = 1;
} # fine if (numlin_query($campi_pers_comm) == 1)

for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$nometipotariffa = ${"nometipotariffa".$n_t};
$numpersone = ${"numpersone".$n_t};
$assegnazioneapp = ${"assegnazioneapp".$n_t};
$tipo_sconto = ${"tipo_sconto".$n_t};
$sconto = ${"sconto".$n_t};
$tipo_val_sconto = ${"tipo_val_sconto".$n_t};
$giorno_stima_checkin = ${"giorno_stima_checkin".$n_t};
$ora_stima_checkin = ${"ora_stima_checkin".$n_t};
$min_stima_checkin = ${"min_stima_checkin".$n_t};
$met_paga_caparra = ${"met_paga_caparra".$n_t};
$origine_prenota = ${"origine_prenota".$n_t};
$conferma_prenota = ${"conferma_prenota".$n_t};
$num_commenti = ${"num_commenti".$n_t};
for ($num1 = 1 ; $num1 <= $num_commenti ; $num1++) {
$tipo_commento[$num1] = ${"tipo_commento".$num1."_".$n_t};
$commento[$num1] = ${"commento".$num1."_".$n_t};
} # fine for $num1
$lista_app = ${"lista_app".$n_t};
$caparra = ${"caparra".$n_t};
$tipo_val_caparra = ${"tipo_val_caparra".$n_t};
$commissioni = ${"commissioni".$n_t};
$tipo_val_commissioni = ${"tipo_val_commissioni".$n_t};
$n_tronchi = ${"n_tronchi".$n_t};

if (@get_magic_quotes_gpc()) {
$met_paga_caparra = stripslashes($met_paga_caparra);
$origine_prenota = stripslashes($origine_prenota);
for ($num1 = 1 ; $num1 <= $num_commenti ; $num1++) $commento[$num1] = stripslashes($commento[$num1]);
} # fine if (@get_magic_quotes_gpc())
$met_paga_caparra = htmlspecialchars($met_paga_caparra);
$origine_prenota = htmlspecialchars($origine_prenota);
for ($num1 = 1 ; $num1 <= $num_commenti ; $num1++) $commento[$num1] = htmlspecialchars($commento[$num1]);

for ($num1 = 1 ; $num1 <= $n_tronchi ; $num1 = $num1 + 1) {
$appartamento = $vet_appartamenti[$n_t][$num1];
$inizioperiodo = $vett_idinizio[$n_t][$num1];
$fineperiodo = $vett_idfine[$n_t][$num1];
$lunghezza_periodo = $fineperiodo - $inizioperiodo + 1;
if (${"diff_persone".$n_t} and ($num1 + ${"diff_persone".$n_t}) > $n_tronchi) $numpersone_corr = $numpersone - 1;
else $numpersone_corr = $numpersone;

if ($id_prenota_temp[$n_t][$num1]) $idprenota = $id_prenota_temp[$n_t][$num1];
else {
$idprenota = esegui_query("select numlimite from $tablecostiprenota where idcostiprenota = '1'");
$idprenota = risul_query($idprenota,0,'numlimite');
esegui_query("update $tablecostiprenota set numlimite = '".($idprenota + 1)."' where idcostiprenota = '1'");
} # fine else if ($id_prenota_temp[$n_t][$num1])

echo mex("Prenotazione",$pag)." $idprenota ".mex("dal",$pag)." <b>".$data_inizio_f[$n_t]."</b> ".mex("al",$pag)." <b>".$data_fine_f[$n_t]."</b> ($lunghezza_periodo ".mex("$parola_settiman",$pag);
if ($lunghezza_periodo == 1) echo mex("$lettera_a",$pag);
else echo mex("$lettera_e",$pag);
echo ") ".mex("a nome di",$pag)." <b>$cognome</b> ";
if ($numpersone_corr) echo mex("per",$pag)." <b>$numpersone_corr</b> ".mex("persone",$pag)." ";
echo mex("nell'appartamento",'unit.php')." <b>$appartamento</b>";
if ($assegnazioneapp == "k") echo " (".mex("fisso",'unit.php').")";
else {
echo " (".mex("mobile",'unit.php');
if ($lista_app) echo " ".mex("in",$pag)." ".str_replace(",",", ",$lista_app);
echo ")";
} # fine else if ($assegnazioneapp == "K")
echo ":<br><br>";

$costo_tariffa_tot = (double) 0;
unset($lista_tariffe_sett);
unset($lista_tariffep_sett);
for ($num2 = $inizioperiodo ; $num2 <= $fineperiodo ; $num2++) {
$riga_tariffa = esegui_query("select * from  $tableperiodi where idperiodi = $num2");
$costo_tariffa = risul_query($riga_tariffa,0,$nometipotariffa);
if ($dati_tariffe[$nometipotariffa]['moltiplica'] == "p") {
if (!strcmp($costo_tariffa,"")) $costo_tariffa = 0;
$costo_tariffap = risul_query($riga_tariffa,0,$nometipotariffa."p");
if (!strcmp($costo_tariffap,"")) $costo_tariffap = 0;
$costo_tariffap = (double) $costo_tariffap * $numpersone_corr;
$lista_tariffep_sett .= ",".$costo_tariffap;
$costo_tariffa = (double) $costo_tariffa + $costo_tariffap;
} # fine if ($dati_tariffe[$nometipotariffa]['moltiplica'] == "p")
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $costo_tariffa;
$lista_tariffe_sett .= ",".$costo_tariffa;
} # fine for $num2
$lista_tariffe_sett = substr($lista_tariffe_sett,1);
if ($lista_tariffep_sett) {
$lista_tariffep_sett = substr($lista_tariffep_sett,1);
$lista_tariffe_sett .= ";$lista_tariffep_sett";
} # fine if ($lista_tariffep_sett)
$costo_tariffa = $costo_tariffa_tot;
$nometariffa = $dati_tariffe[$nometipotariffa]['nome'];
if ($nometariffa == "") {
$nometariffa = $nometipotariffa;
$nometariffa_vedi = mex("tariffa",$pag).substr($nometipotariffa,7);
} # fine if ($nometariffa == "")
else $nometariffa_vedi = $nometariffa;
$nometariffa = aggslashdb($nometariffa);
$tariffa = $nometariffa."#@&".$costo_tariffa_tot;
if ($dati_tariffe[$nometipotariffa]['moltiplica'] == "p") $tariffa .= "#@&p";
$costo_tariffa_tot_p = punti_in_num($costo_tariffa_tot,$stile_soldi);
echo "$costo_tariffa_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".mex("tariffa",$pag)." \"$nometariffa_vedi\"<br>";
$valori = "abcdefghijkmnpqrstuvwxz";
srand((double)microtime() * 1000000);
unset($cod_prenota);
for ($num2 = 0 ; $num2 < 4 ; $num2++) $cod_prenota .= substr($valori,rand(0,22),1);

esegui_query("insert into $tableprenota (idprenota,idclienti,idappartamenti,iddatainizio,iddatafine,tariffa,tariffesettimanali,codice,conferma,datainserimento,hostinserimento,utente_inserimento) values ('$idprenota','$idclienti','$appartamento','$inizioperiodo','$fineperiodo','$tariffa','$lista_tariffe_sett','$cod_prenota','N','$datainserimento','$HOSTNAME','$id_utente_ins')");

if ($numpersone_corr) {
esegui_query("update $tableprenota set num_persone = '$numpersone_corr' where idprenota = '$idprenota' ");
} # fine if ($numpersone_corr)

if (${"num_app_richiesti".$n_t} == 1) $num_prenota_tipo = 1;
else $num_prenota_tipo = $num1;
if (is_array($idospiti[$n_t][$num_prenota_tipo])) $num_ospiti = count($idospiti[$n_t][$num_prenota_tipo]);
else $num_ospiti = 0;
for ($num2 = 1 ; $num2 <= $num_ospiti ; $num2++) {
if ($idospiti[$n_t][$num_prenota_tipo][$num2]) esegui_query("insert into $tablerclientiprenota (idprenota,idclienti,num_ordine,parentela,datainserimento,hostinserimento,utente_inserimento) values ('$idprenota','".$idospiti[$n_t][$num_prenota_tipo][$num2]."','".$num_ordine[$n_t][$num_prenota_tipo][$num2]."','".aggslashdb($parentela[$n_t][$num_prenota_tipo][$num2])."','$datainserimento','$HOSTNAME','$id_utente_ins') ");
} # fine for $num2

if ($dati_tariffe[$nometipotariffa]['tasse_percent']) {
esegui_query("update $tableprenota set tasseperc = '".$dati_tariffe[$nometipotariffa]['tasse_percent']."' where idprenota = '$idprenota' ");
} # fine if ($dati_tariffe[$nometipotariffa]['tasse_percent'])

$caparra = formatta_soldi($caparra);
if ($priv_ins_caparra != "s" or controlla_soldi($caparra,"pos") == "NO") unset($caparra);
if (!$caparra) $caparra = calcola_caparra($dati_tariffe,$nometipotariffa,$inizioperiodo,$fineperiodo,$costo_tariffa_tot,$lista_tariffe_sett);
elseif ($tipo_val_caparra == "tar") {
$caparra_arrotond = $dati_tariffe[$nometipotariffa]['caparra_arrotond'];
if (!strcmp($caparra_arrotond,"") or $caparra_arrotond == "val") $caparra_arrotond = $arrotond_predef;
$caparra = ($costo_tariffa_tot * (double) $caparra) / 100;
$caparra = $caparra / $caparra_arrotond;
$caparra = floor($caparra);
$caparra = $caparra * $caparra_arrotond;
} # fine elseif ($tipo_val_caparra == "tar")

# costi aggiuntivi da calcolare prima dello sconto
unset($costi_dopo_sconto);
$costo_escludi_perc = (double) 0;
for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
$idcostoagg = $dati_ca[$num_costo]['id'];
$costo_trovato = "NO";
for ($numca = 1 ; $numca <= ${"numcostiagg_".$n_t."t".$num1} ; $numca++) if ($idcostoagg == ${"idcostoagg".$numca."_".$n_t."t".$num1}) $costo_trovato = $numca;
if ($costo_trovato != "NO") {
$numca = $costo_trovato;
if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI") {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$idcostoagg] == "SI") {
if ($dati_ca[$num_costo]['tipo_val'] == "r" or $dati_ca[$num_costo]['tipo_val'] == "t") $costi_dopo_sconto[$num_costo] = $numca;
else {
$settimane_costo = ${"settimane_costo".$numca."_".$n_t."t".$num1};
$moltiplica = ${"moltiplica".$numca."_".$n_t."t".$num1};
$idcostiprenota = esegui_query("select max(idcostiprenota) from $tablecostiprenota");
$idcostiprenota = risul_query($idcostiprenota,0,0) + 1;
$tipo_ca = $dati_ca[$num_costo]['tipo'].$dati_ca[$num_costo]['tipo_val'];
$valore_ca = $dati_ca[$num_costo]['valore'];
$valore_perc_ca = $dati_ca[$num_costo]['valore_perc'];
$prezzo_costo_tot = calcola_prezzo_totale_costo($dati_ca,$num_costo,$inizioperiodo,$fineperiodo,$settimane_costo,$moltiplica,$costo_tariffa,$lista_tariffe_sett,$costo_tariffa_tot,$caparra,$numpersone_corr);
$associasett_ca = $dati_ca[$num_costo]['associasett'];
if ($dati_ca[$num_costo]['var_percentuale'] != "s" and $dati_ca[$num_costo]['tipo_val'] != "f") {
$tipo_ca = $dati_ca[$num_costo]['tipo']."f";
$moltiplica = 1;
if ($dati_ca[$num_costo]['tipo'] == "s") {
$settimane_costo = 1;
$associasett_ca = "n";
} # fine if ($dati_ca[$num_costo][tipo] == "s")
$valore_ca = $prezzo_costo_tot;
$valore_perc_ca = 0;
} # fine if ($dati_ca[$num_costo][var_percentuale] != "s" and...
if ($dati_ca[$num_costo]['var_moltiplica'] == "s") $varmoltiplica_ca = $dati_ca[$num_costo]['moltiplica'].$dati_ca[$num_costo]['molt_max'].$dati_ca[$num_costo]['molt_agg'].",".$dati_ca[$num_costo]['molt_max_num'];
else $varmoltiplica_ca = "cx0,";
if ($dati_ca[$num_costo]['var_numsett'] == "s") $varnumsett_ca = $dati_ca[$num_costo]['numsett_orig'];
else $varnumsett_ca = "c";
if ($dati_ca[$num_costo]['var_periodip'] == "s") $varperiodipermessi_ca = $dati_ca[$num_costo]['periodipermessi_orig'];
else $varperiodipermessi_ca = "";
if ($dati_ca[$num_costo]['var_beniinv'] == "s") $varbeniinv_ca = ${"num_ripetizioni_costo".$numca."_".$n_t."t".$num1}.";".$dati_ca[$num_costo]['beniinv_orig'];
else $varbeniinv_ca = "";
if ($dati_ca[$num_costo]['var_appi'] == "s") $varappincompatibili_ca = $dati_ca[$num_costo]['appincompatibili'];
else $varappincompatibili_ca = "";
if ($dati_ca[$num_costo]['var_tariffea'] == "s") $vartariffeassociate_ca = $dati_ca[$num_costo]["tipo_associa_".${"nometipotariffa".$n_t}].$dati_ca[$num_costo][${"nometipotariffa".$n_t}];
else $vartariffeassociate_ca = "";
$vartariffeincomp_ca = "";
if ($dati_ca[$num_costo]['var_tariffei'] == "s") {
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($dati_ca[$num_costo]["incomp_tariffa".$numtariffa] == "i") $vartariffeincomp_ca .= ",".$numtariffa;
} # fine for $numtariffa
if ($vartariffeincomp_ca) $vartariffeincomp_ca = substr($vartariffeincomp_ca,1);
} # fine if ($dati_ca[$num_costo][var_tariffei] == "s")
if ($dati_ca[$num_costo]['var_comb'] == "s") $variazione_ca = $dati_ca[$num_costo]['combina'];
else $variazione_ca = "n";
$variazione_ca .= $dati_ca[$num_costo]['escludi_tot_perc'];
esegui_query("insert into $tablecostiprenota (idcostiprenota,idprenota,tipo,nome,valore,associasett,settimane,moltiplica,letto,idntariffe,variazione,varmoltiplica,varnumsett,varperiodipermessi,varbeniinv,varappincompatibili,vartariffeassociate,vartariffeincomp,datainserimento,hostinserimento,utente_inserimento) values ('$idcostiprenota','$idprenota','$tipo_ca','".aggslashdb($dati_ca[$num_costo]['nome'])."','$valore_ca','$associasett_ca','$settimane_costo','$moltiplica','".$dati_ca[$num_costo]['letto']."','$idcostoagg','$variazione_ca','$varmoltiplica_ca','$varnumsett_ca','$varperiodipermessi_ca','$varbeniinv_ca','$varappincompatibili_ca','$vartariffeassociate_ca','$vartariffeincomp_ca','$datainserimento','$HOSTNAME','$id_utente_ins')");
if (substr($tipo_ca,1,1) != "f") esegui_query("update $tablecostiprenota set valore_perc = '$valore_perc_ca', arrotonda = '".$dati_ca[$num_costo]['arrotonda']."' where idcostiprenota = '$idcostiprenota'");
if ($dati_ca[$num_costo]['tasseperc']) esegui_query("update $tablecostiprenota set tasseperc = '".$dati_ca[$num_costo]['tasseperc']."' where idcostiprenota = '$idcostiprenota'");
if (strcmp($dati_ca[$num_costo]['categoria'],"")) esegui_query("update $tablecostiprenota set categoria = '".$dati_ca[$num_costo]['categoria']."' where idcostiprenota = '$idcostiprenota'");
$id_costo_inserito[$idcostoagg] = $idcostiprenota;
$prezzo_costo_tot_p = punti_in_num($prezzo_costo_tot,$stile_soldi);
echo "$prezzo_costo_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
if ($dati_ca[$num_costo]['tipo'] == "u") echo mex("Costo unico",$pag);
if ($dati_ca[$num_costo]['tipo'] == "s") echo mex("Costo $parola_settimanale",$pag);
echo " \"".$dati_ca[$num_costo]['nome']."\"";
if ($associasett_ca == "s") {
$moltiplica = explode(",",$moltiplica);
$valnummoltiplica_ca = $moltiplica[1];
for ($num2 = 2 ; $num2 < (count($moltiplica) - 1) ; $num2++) if ($moltiplica[$num2] != $valnummoltiplica_ca) $valnummoltiplica_ca = 1;
} # fine if ($associasett_ca == "s")
else $valnummoltiplica_ca = $moltiplica;
if ($valnummoltiplica_ca != 1) echo " (".mex("moltiplicato per",$pag)." $valnummoltiplica_ca)";
echo "<br>";
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $prezzo_costo_tot;
if ($dati_ca[$num_costo]['escludi_tot_perc'] == "s") $costo_escludi_perc = (double) $costo_escludi_perc + (double) $prezzo_costo_tot;
} # fine else if ($dati_ca[$num_costo]['tipo_val'] == "r" or $dati_ca[$num_costo]['tipo_val'] == "t")
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine if (${"costoagg".$numca."_".$n_t."t".$num1} == "SI")
} # fine if ($costo_trovato != "NO")
} # fine for $num_costo

$sconto_orig = $sconto;
$prezzo_totale_imposto = "";
$sconto = formatta_soldi($sconto);
if (strcmp($sconto,"") and $priv_ins_sconto == "s" and controlla_soldi($sconto) == "SI") {
$sconto = (double) $sconto;
if ($tipo_sconto == "tot") {
$prezzo_totale_imposto = (double) $sconto;
$sconto = (double) $costo_tariffa_tot - (double) $sconto;
} # fine if ($tipo_sconto == "tot")
if ($tipo_sconto == "tot_sett") {
$prezzo_totale_imposto = (double) $sconto * (double) $lunghezza_periodo;
$sconto = (double) $costo_tariffa_tot - ((double) $sconto * (double) $lunghezza_periodo);
} # fine if ($tipo_sconto == "tot_sett")
if ($tipo_sconto == "tar") $sconto = (double) $costo_tariffa - (double) $sconto;
if ($tipo_sconto == "tar_sett") $sconto = (double) $costo_tariffa - ((double) $sconto * (double) $lunghezza_periodo);
if ($tipo_sconto == "sconto" and ($tipo_val_sconto == "tot" or $tipo_val_sconto == "tar")) {
if ($tipo_val_sconto == "tot") $sconto = ($costo_tariffa_tot * (double) $sconto) / 100;
if ($tipo_val_sconto == "tar") $sconto = ($costo_tariffa * (double) $sconto) / 100;
$sconto = $sconto / (double) $arrotond_predef;
$sconto = floor((string) $sconto);
$sconto = $sconto * (double) $arrotond_predef;
} # fine if ($tipo_sconto == "sconto" and ($tipo_val_sconto == "tot" or...
if ($sconto > $costo_tariffa_tot) $sconto = (double) $costo_tariffa_tot;
$costo_tariffa_tot = (double) $costo_tariffa_tot - (double) $sconto;
} # fine if (strcmp($sconto,"") and $priv_ins_sconto == "s" and...

# costi aggiuntivi da calcolare dopo lo sconto (prima calcolo prezzi e 
# approssimazione per sconto con costi sul totale con totale imposto)
$passo = 1;
$dir_sconto = "";
$costo_tariffa_tot_orig = $costo_tariffa_tot;
$costo_escludi_perc_orig = $costo_escludi_perc;
$prezzo_esatto = 0;
while (!$prezzo_esatto) {
$costo_tariffa_tot = $costo_tariffa_tot_orig;
$costo_escludi_perc = $costo_escludi_perc_orig;
unset($prezzo_costo_vett);

for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
if ($costi_dopo_sconto[$num_costo] and $dati_ca[$num_costo]['tipo_val'] == "r") {
$numca = $costi_dopo_sconto[$num_costo];
$settimane_costo = ${"settimane_costo".$numca."_".$n_t."t".$num1};
$moltiplica = ${"moltiplica".$numca."_".$n_t."t".$num1};
$prezzo_costo_vett[$num_costo] = calcola_prezzo_totale_costo($dati_ca,$num_costo,$inizioperiodo,$fineperiodo,$settimane_costo,$moltiplica,$costo_tariffa,$lista_tariffe_sett,$costo_tariffa_tot,$caparra,$numpersone_corr,$costo_escludi_perc);
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $prezzo_costo_vett[$num_costo];
if ($dati_ca[$num_costo]['escludi_tot_perc'] == "s") $costo_escludi_perc = (double) $costo_escludi_perc + (double) $prezzo_costo_vett[$num_costo];
} # fine if ($costi_dopo_sconto[$num_costo] and $dati_ca[$num_costo]['tipo_val'] == "r")
} # fine for $num_costo
for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
if ($costi_dopo_sconto[$num_costo] and $dati_ca[$num_costo]['tipo_val'] == "t") {
$numca = $costi_dopo_sconto[$num_costo];
$settimane_costo = ${"settimane_costo".$numca."_".$n_t."t".$num1};
$moltiplica = ${"moltiplica".$numca."_".$n_t."t".$num1};
$prezzo_costo_vett[$num_costo] = calcola_prezzo_totale_costo($dati_ca,$num_costo,$inizioperiodo,$fineperiodo,$settimane_costo,$moltiplica,$costo_tariffa,$lista_tariffe_sett,$costo_tariffa_tot,$caparra,$numpersone_corr,$costo_escludi_perc);
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $prezzo_costo_vett[$num_costo];
if ($dati_ca[$num_costo]['escludi_tot_perc'] == "s") $costo_escludi_perc = (double) $costo_escludi_perc + (double) $prezzo_costo_vett[$num_costo];
} # fine if ($costi_dopo_sconto[$num_costo] and $dati_ca[$num_costo]['tipo_val'] == "t")
} # fine for $num_costo

if (strcmp($prezzo_totale_imposto,"")) {
if (round($costo_tariffa_tot,2) != round($prezzo_totale_imposto,2)) {
if ($costo_tariffa_tot > $prezzo_totale_imposto) {
if ($dir_sconto and $dir_sconto != "crescente") {
if ($passo == 1) $passo = 0.01;
else break;
} # fine if ($dir_sconto and $dir_sconto != "crescente")
$dir_sconto = "crescente";
$sconto = $sconto + (double) $passo;
$costo_tariffa_tot_orig = $costo_tariffa_tot_orig - (double) $passo;
} # fine if ($costo_tariffa_tot > $prezzo_totale_imposto)
else {
if ($dir_sconto and $dir_sconto != "decrescente") {
if ($passo == 1) $passo = 0.01;
else break;
} # fine if ($dir_sconto and $dir_sconto != "decrescente")
$dir_sconto = "decrescente";
$sconto = $sconto - (double) $passo;
$costo_tariffa_tot_orig = $costo_tariffa_tot_orig + (double) $passo;
} # fine else if ($costo_tariffa_tot > $prezzo_totale_imposto)
} # fine if (round($costo_tariffa_tot,2) != round($prezzo_totale_imposto,2))
else $prezzo_esatto = 1;
} # fine if (strcmp($prezzo_totale_imposto,""))
else $prezzo_esatto = 1;
} # fine while (!$prezzo_esatto)
$costo_tariffa_tot = $costo_tariffa_tot_orig;
$costo_escludi_perc = $costo_escludi_perc_orig;

for ($num_costo = 0 ; $num_costo < $dati_ca['num'] ; $num_costo++) {
if ($costi_dopo_sconto[$num_costo]) {
$numca = $costi_dopo_sconto[$num_costo];
$settimane_costo = ${"settimane_costo".$numca."_".$n_t."t".$num1};
$moltiplica = ${"moltiplica".$numca."_".$n_t."t".$num1};
$idcostiprenota = esegui_query("select max(idcostiprenota) from $tablecostiprenota");
$idcostiprenota = risul_query($idcostiprenota,0,0) + 1;
$tipo_ca = $dati_ca[$num_costo]['tipo'].$dati_ca[$num_costo]['tipo_val'];
$valore_ca = $dati_ca[$num_costo]['valore'];
$valore_perc_ca = $dati_ca[$num_costo]['valore_perc'];
$prezzo_costo_tot = (double) $prezzo_costo_vett[$num_costo];
$associasett_ca = $dati_ca[$num_costo]['associasett'];
if ($dati_ca[$num_costo]['var_percentuale'] != "s" and $dati_ca[$num_costo]['tipo_val'] != "f") {
$tipo_ca = $dati_ca[$num_costo]['tipo']."f";
$moltiplica = 1;
if ($dati_ca[$num_costo]['tipo'] == "s") {
$settimane_costo = 1;
$associasett_ca = "n";
} # fine if ($dati_ca[$num_costo][tipo] == "s")
$valore_ca = $prezzo_costo_tot;
$valore_perc_ca = 0;
} # fine if ($dati_ca[$num_costo][var_percentuale] != "s" and...
if ($dati_ca[$num_costo]['var_moltiplica'] == "s") $varmoltiplica_ca = $dati_ca[$num_costo]['moltiplica'].$dati_ca[$num_costo]['molt_max'].$dati_ca[$num_costo]['molt_agg'].",".$dati_ca[$num_costo]['molt_max_num'];
else $varmoltiplica_ca = "cx0,";
if ($dati_ca[$num_costo]['var_numsett'] == "s") $varnumsett_ca = $dati_ca[$num_costo]['numsett_orig'];
else $varnumsett_ca = "c";
if ($dati_ca[$num_costo]['var_periodip'] == "s") $varperiodipermessi_ca = $dati_ca[$num_costo]['periodipermessi_orig'];
else $varperiodipermessi_ca = "";
if ($dati_ca[$num_costo]['var_beniinv'] == "s") $varbeniinv_ca = ${"num_ripetizioni_costo".$numca."_".$n_t."t".$num1}.";".$dati_ca[$num_costo]['beniinv_orig'];
else $varbeniinv_ca = "";
if ($dati_ca[$num_costo]['var_appi'] == "s") $varappincompatibili_ca = $dati_ca[$num_costo]['appincompatibili'];
else $varappincompatibili_ca = "";
if ($dati_ca[$num_costo]['var_tariffea'] == "s") $vartariffeassociate_ca = $dati_ca[$num_costo]["tipo_associa_".${"nometipotariffa".$n_t}].$dati_ca[$num_costo][${"nometipotariffa".$n_t}];
else $vartariffeassociate_ca = "";
$vartariffeincomp_ca = "";
if ($dati_ca[$num_costo]['var_tariffei'] == "s") {
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($dati_ca[$num_costo]["incomp_tariffa".$numtariffa] == "i") $vartariffeincomp_ca .= ",".$numtariffa;
} # fine for $numtariffa
if ($vartariffeincomp_ca) $vartariffeincomp_ca = substr($vartariffeincomp_ca,1);
} # fine if ($dati_ca[$num_costo][var_tariffei] == "s")
if ($dati_ca[$num_costo]['var_comb'] == "s") $variazione_ca = $dati_ca[$num_costo]['combina'];
else $variazione_ca = "n";
$variazione_ca .= $dati_ca[$num_costo]['escludi_tot_perc'];
esegui_query("insert into $tablecostiprenota (idcostiprenota,idprenota,tipo,nome,valore,associasett,settimane,moltiplica,letto,idntariffe,variazione,varmoltiplica,varnumsett,varperiodipermessi,varbeniinv,varappincompatibili,vartariffeassociate,vartariffeincomp,datainserimento,hostinserimento,utente_inserimento) values ('$idcostiprenota','$idprenota','$tipo_ca','".aggslashdb($dati_ca[$num_costo]['nome'])."','$valore_ca','$associasett_ca','$settimane_costo','$moltiplica','".$dati_ca[$num_costo]['letto']."','$idcostoagg','$variazione_ca','$varmoltiplica_ca','$varnumsett_ca','$varperiodipermessi_ca','$varbeniinv_ca','$varappincompatibili_ca','$vartariffeassociate_ca','$vartariffeincomp_ca','$datainserimento','$HOSTNAME','$id_utente_ins')");
if (substr($tipo_ca,1,1) != "f") esegui_query("update $tablecostiprenota set valore_perc = '$valore_perc_ca', arrotonda = '".$dati_ca[$num_costo]['arrotonda']."' where idcostiprenota = '$idcostiprenota'");
if ($dati_ca[$num_costo]['tasseperc']) esegui_query("update $tablecostiprenota set tasseperc = '".$dati_ca[$num_costo]['tasseperc']."' where idcostiprenota = '$idcostiprenota'");
$id_costo_inserito[$idcostoagg] = $idcostiprenota;
$prezzo_costo_tot_p = punti_in_num($prezzo_costo_tot,$stile_soldi);
echo "$prezzo_costo_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
if ($dati_ca[$num_costo]['tipo'] == "u") echo mex("Costo unico",$pag);
if ($dati_ca[$num_costo]['tipo'] == "s") echo mex("Costo $parola_settimanale",$pag);
echo " \"".$dati_ca[$num_costo]['nome']."\"";
if ($associasett_ca == "s") {
$moltiplica = explode(",",$moltiplica);
$valnummoltiplica_ca = $moltiplica[1];
for ($num2 = 2 ; $num2 < (count($moltiplica) - 1) ; $num2++) if ($moltiplica[$num2] != $valnummoltiplica_ca) $valnummoltiplica_ca = 1;
} # fine if ($associasett_ca == "s")
else $valnummoltiplica_ca = $moltiplica;
if ($valnummoltiplica_ca != 1) echo " (".mex("moltiplicato per",$pag)." $valnummoltiplica_ca)";
echo "<br>";
$costo_tariffa_tot = (double) $costo_tariffa_tot + (double) $prezzo_costo_tot;
} # fine if ($costi_dopo_sconto[$num_costo])
} # fine for $num_costo
$prezzo_costi_tot = (double) $costo_tariffa_tot - (double) $costo_tariffa + (double) $sconto;

# Appartamenti eliminati dai costi aggiuntivi, verranno aggiunti di nuovo agli appartamenti
# assegnabili quando si modifica la prenotazione (se il costo mantiene gli appartamenti 
# incompatibili con la modifica della prenotazione, allora questi appartamenti verranno tolti 
# di nuovo dal costo quando si modifica la prenotazione, finchè il costo rimarrà associato)
if ($app_eliminati_costi[$n_t]) {
esegui_query("update $tableprenota set incompatibilita = '".aggslashdb($app_eliminati_costi[$n_t])."' where idprenota = '$idprenota' ");
} # fine if ($app_eliminati_costi[$n_t])

if (strcmp($sconto,"") and $priv_ins_sconto == "s" and controlla_soldi($sconto) == "SI") {
esegui_query("update $tableprenota set sconto = '$sconto' where idprenota = '$idprenota' ");
$sconto_p = punti_in_num($sconto,$stile_soldi);
if (substr($sconto_p,0,1) == "-") $sconto_p = substr($sconto_p,1);
else echo "-";
echo "$sconto_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".mex("Sconto",$pag)."<br>";
} # fine if (strcmp($sconto,"") and $priv_ins_sconto == "s" and...

$commissioni = formatta_soldi($commissioni);
if ($priv_ins_caparra != "s" or controlla_soldi($commissioni,"pos") == "NO") unset($commissioni);
if (!$commissioni) $commissioni = calcola_commissioni($dati_tariffe,$nometipotariffa,$inizioperiodo,$fineperiodo,$lista_tariffe_sett,$sconto,$prezzo_costi_tot);
elseif ($tipo_val_commissioni == "tar" or $tipo_val_commissioni == "ts" or $tipo_val_commissioni == "tsc") {
$commissioni_arrotond = $dati_tariffe[$nometipotariffa]['commissioni_arrotond']['def'];
if (!strcmp($commissioni_arrotond,"") or $commissioni_arrotond == "val") $commissioni_arrotond = $arrotond_predef;
$costo_base = (double) $costo_tariffa;
if ($tipo_val_commissioni == "ts") $costo_base = $costo_base - (double) $sconto;
if ($tipo_val_commissioni == "tsc") $costo_base = $costo_base - (double) $sconto + (double) $prezzo_costi_tot;
$commissioni = ($costo_base * (double) $commissioni) / 100;
$commissioni = $commissioni / $commissioni_arrotond;
$commissioni = floor(round($commissioni));
$commissioni = $commissioni * $commissioni_arrotond;
} # fine elseif ($tipo_val_commissioni == "tar" or...
$sconto = $sconto_orig;

esegui_query("update $tableprenota set tariffa_tot = '$costo_tariffa_tot' where idprenota = '$idprenota' ");
$costo_tariffa_tot_p = punti_in_num($costo_tariffa_tot,$stile_soldi);
echo "<b>$costo_tariffa_tot_p $Euro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".mex("TOTALE",$pag)."</b><br>";

if ($lista_app) {
esegui_query("update $tableprenota set app_assegnabili = '$lista_app' where idprenota = '$idprenota' ");
} # fine if ($lista_app)

if ($assegnazioneapp) {
esegui_query("update $tableprenota set assegnazioneapp = '$assegnazioneapp' where idprenota = '$idprenota' ");
} # fine if ($assegnazioneapp)

if ($caparra) {
if ($caparra > $costo_tariffa_tot) $caparra = $costo_tariffa_tot;
esegui_query("update $tableprenota set caparra = '$caparra' where idprenota = '$idprenota' ");
$da_pagare = $costo_tariffa_tot - $caparra;
$caparra_p = punti_in_num($caparra,$stile_soldi);
$da_pagare_p = punti_in_num($da_pagare,$stile_soldi);
echo "<br>".mex("Caparra",$pag).": <b>$caparra_p</b> $Euro (".mex("resto da pagare",$pag).": $da_pagare_p $Euro).<br>";
} # fine if ($caparra)

$met_trovato = "NO";
if ($met_paga_caparra and $priv_ins_caparra == "s") {
$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '$id_utente'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
if ($metodi_pagamento) {
$metodi_pagamento = explode(",",$metodi_pagamento);
for ($num2 = 0 ; $num2 < count($metodi_pagamento) ; $num2++) {
if ($met_paga_caparra == $metodi_pagamento[$num2]) $met_trovato = "SI";
} # fine for $num2
if ($met_trovato == "SI") {
$met_paga_caparra = aggslashdb($met_paga_caparra);
esegui_query("update $tableprenota set metodo_pagamento = '$met_paga_caparra' where idprenota = '$idprenota' ");
if (!$caparra) echo "<br>";
echo mex("Metodo pagamento caparra",$pag).": <b>".stripslashes($met_paga_caparra)."</b>.<br>";
} # fine if ($met_trovato == "SI")
} # fine if ($metodi_pagamento)
} # fine if ($met_paga_caparra and $priv_ins_caparra == "s")

if ($commissioni) {
#if ($commissioni > $costo_tariffa_tot) $commissioni = $costo_tariffa_tot;
esegui_query("update $tableprenota set commissioni = '$commissioni' where idprenota = '$idprenota' ");
$resto_comm = $costo_tariffa_tot - $commissioni;
$commissioni_p = punti_in_num($commissioni,$stile_soldi);
$resto_comm_p = punti_in_num($resto_comm,$stile_soldi);
echo "<br>".mex("Commissioni",$pag).": <b>$commissioni_p</b> $Euro (".mex("resto commissioni",$pag).": $resto_comm_p $Euro).<br>";
} # fine if ($commissioni)

if ($origine_prenota and $priv_ins_orig_prenota == "s") {
$origini_prenota = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'origini_prenota' and idutente = '$id_utente'");
$origini_prenota = risul_query($origini_prenota,0,'valpersonalizza');
if ($origini_prenota) {
$orig_trovata = "NO";
$origini_prenota = explode(",",$origini_prenota);
for ($num2 = 0 ; $num2 < count($origini_prenota) ; $num2++) {
if ($origine_prenota == $origini_prenota[$num2]) $orig_trovata = "SI";
} # fine for $num2
if ($orig_trovata == "SI") {
$origine_prenota = aggslashdb($origine_prenota);
esegui_query("update $tableprenota set origine = '$origine_prenota' where idprenota = '$idprenota' ");
if (!$caparra and !$commissioni and $met_trovato == "NO") echo "<br>";
echo mex("Origine",$pag).": <b>".stripslashes($origine_prenota)."</b>.<br>";
} # fine if ($orig_trovata == "SI")
} # fine if ($origini_prenota)
} # fine if ($origine_prenota and $priv_ins_orig_prenota == "s")

if ($giorno_stima_checkin and $ora_stima_checkin and $min_stima_checkin and $priv_ins_checkin == "s") {
if ($inizioperiodo == $vett_idinizio[$n_t][1]) {
if ($tipo_periodi == "g") $giorni_periodo = $lunghezza_periodo;
else $giorni_periodo = ($lunghezza_periodo * 7);
if (controlla_num_pos($giorno_stima_checkin) == "SI" and $giorno_stima_checkin >= 1 and $giorno_stima_checkin <= 7 and $giorno_stima_checkin <= ($giorni_periodo + 1)) {
$data_stima_checkin = esegui_query("select datainizio from $tableperiodi where idperiodi = '$inizioperiodo'");
$data_stima_checkin = risul_query($data_stima_checkin,0,'datainizio');
$data_ini_prenota_f = formatta_data($data_stima_checkin,$stile_data);
$anno_dts = substr($data_stima_checkin,0,4);
$mese_dts = substr($data_stima_checkin,5,2);
$giorno_dts = substr($data_stima_checkin,8,2);
$data_stima_checkin = date("Y-m-d",mktime(0,0,0,$mese_dts,($giorno_dts + $giorno_stima_checkin - 1),$anno_dts));
$stima_checkin = $data_stima_checkin." ".$ora_stima_checkin.":".$min_stima_checkin.":00";
if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/",$stima_checkin)) {
esegui_query("update $tableprenota set checkout = '$stima_checkin' where idprenota = '$idprenota' ");
echo "<br>".mex("Orario stimato di entrata",$pag).": <b>".substr(str_replace($data_ini_prenota_f,"",formatta_data($stima_checkin,$stile_data)),0,-3)."</b>.<br>";
} # fine if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:00/",$stima_checkin))
} # fine if (controlla_num_pos($giorno_stima_checkin) == "SI" and...
} # fine if ($inizioperiodo == $vett_idinizio[$n_t][1])
} # fine if ($giorno_stima_checkin and $ora_stima_checkin and $min_stima_checkin and $priv_ins_checkin == "s")

if ($conferma_prenota == "SI" and $priv_ins_conferma == "s") {
echo "<br>".mex("Confermata",$pag).".<br>";
esegui_query("update $tableprenota set conferma = 'S' where idprenota = '$idprenota' ");
} # fine if ($conferma_prenota == "SI" and $priv_ins_conferma == "s")

if ($priv_ins_commento == "s" or $priv_ins_commenti_pers == "s") {
$commento_arr = array();
for ($num2 = 1 ; $num2 <= $num_commenti ; $num2++) {
if (strcmp($commento[$num2],"")) {
if (($priv_ins_commento == "s" and (!strcmp($tipo_commento[$num2],"") or $tipo_commento[$num2] == "checkin" or $tipo_commento[$num2] == "checkout")) or ($priv_ins_commenti_pers == "s" and $comm_pers_presenti[$tipo_commento[$num2]])) {
echo "<br>".mex("Commento",$pag)."";
if ($tipo_commento[$num2] == "checkin") echo " <em>".mex("per un promemoria all'entrata",$pag)."</em>";
if ($tipo_commento[$num2] == "checkout") echo " <em>".mex("per un promemoria all'uscita",$pag)."</em>";
if (strcmp($tipo_commento[$num2],"") and $tipo_commento[$num2] != "checkin" and $tipo_commento[$num2] != "checkout") echo " \"<em>".$tipo_commento[$num2]."</em>\"";
echo ": ".$commento[$num2]."<br>";
$commento[$num2] = aggslashdb($commento[$num2]);
if (!strcmp($tipo_commento[$num2],"")) $commento_arr['prenota'] = $commento[$num2];
if ($tipo_commento[$num2] == "checkin" or $tipo_commento[$num2] == "checkout") $commento_arr[$tipo_commento[$num2]] = $commento[$num2];
if (strcmp($tipo_commento[$num2],"") and $tipo_commento[$num2] != "checkin" and $tipo_commento[$num2] != "checkout") $commento_arr['pers'] .= ">".$tipo_commento[$num2]."<".$commento[$num2];
} # fine if (($priv_ins_commento == "s" and (!strcmp($tipo_commento[$num2],"") or $tipo_commento[$num2] == "checkin" or...
} # fine if (strcmp($commento[$num2],""))
} # fine for $num2
$commento_corr = $commento_arr['prenota'].">".$commento_arr['checkin'].">".$commento_arr['checkout'].$commento_arr['pers'];
if ($commento_corr != ">>") esegui_query("update $tableprenota set commento = '$commento_corr' where idprenota = '$idprenota' ");
} # fine if ($priv_ins_commento == "s" or $priv_ins_commenti_pers == "s")

echo "<br>".mex("Prenotazione",$pag)." $idprenota ".mex("inserita",$pag)."!<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$idprenota\">
<input type=\"hidden\" name=\"origine\" value=\"inizio.php\">
<button class=\"mres\" type=\"submit\"><div>".mex("Modifica la prenotazione",$pag)." $idprenota</div></button>
</div></form></div>
<hr style=\"width: 95%\">";

$lista_idprenota .= ",$idprenota";
$lista_idprenota_t[$n_t] .= ",$idprenota";
$costo_tot_mess = (double) $costo_tot_mess + (double) $costo_tariffa_tot;
$caparra_mess = (double) $caparra_mess + (double) $caparra;

} # fine for $num1
} # fine for $n_t


aggiorna_beniinv_presenti($tablerelinventario,$beniinv_presenti);

$lista_idprenota = substr($lista_idprenota,1);
if ($prenota_vicine == "SI") {
$lista_idprenota_vett = explode(",",$lista_idprenota);
for ($num1 = 0 ; $num1 < count($lista_idprenota_vett) ; $num1++) {
$idprenota = $lista_idprenota_vett[$num1];
$idprenota_vicine = substr(str_replace(",".$idprenota.",",",",",".$lista_idprenota.","),1,-1);
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_vicine' where idprenota = '$idprenota' ");
} # fine for $num1
} # fine if ($prenota_vicine == "SI")
else {
for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
$lista_idprenota_t[$n_t] = substr($lista_idprenota_t[$n_t],1);
if (${"prenota_vicine".$n_t} and str_replace(",","",$lista_idprenota_t[$n_t]) != $lista_idprenota_t[$n_t]) {
$lista_idprenota_vett = explode(",",$lista_idprenota_t[$n_t]);
for ($num1 = 0 ; $num1 < count($lista_idprenota_vett) ; $num1++) {
$idprenota = $lista_idprenota_vett[$num1];
$idprenota_vicine = $lista_idprenota_t[$n_t];
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
esegui_query("update $tableprenota set idprenota_compagna = '$idprenota_vicine' where idprenota = '$idprenota' ");
} # fine for $num1
} # fine if (${"prenota_vicine".$n_t} and...
} # fine for $n_t
} # fine else if ($prenota_vicine == "SI")

if (str_replace(",","",$lista_idprenota) != $lista_idprenota) {
echo "<br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"modifica_prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"inizio.php\">
<input type=\"hidden\" name=\"id_prenota\" value=\"$lista_idprenota\">
<button class=\"mress\" type=\"submit\"><div>".mex("Modifica come gruppo le prenotazioni inserite",$pag)."</div></button>
</div></form></div><br>
<hr style=\"width: 95%\">";
} # fine if (str_replace(",","",$lista_idprenota) != $lista_idprenota)
if ($idmessaggi) {
esegui_query("update $tablemessaggi set stato = 'ins', dati_messaggio1 = '$lista_idprenota' where tipo_messaggio = 'rprenota' and idutenti $LIKE '%,$id_utente,%' and idmessaggi = '$idmessaggi' ");
if (strcmp($dati_mess_calc[0],"") and strcmp($dati_mess_calc[0],$costo_tot_mess)) echo "<br><em><b>".mex("Attenzione",$pag)."</b></em>: ".mex("il prezzo totale delle prenotazioni inserite",$pag)." (<em>$costo_tot_mess</em> $Euro) ".mex("è diverso da quello contenuto nel messaggio di richiesta di prenotazione",$pag)." (<em>".$dati_mess_calc[0]."</em> $Euro).<br><hr style=\"width: 95%\">";
if (strcmp($dati_mess_calc[1],"") and strcmp($dati_mess_calc[1],$caparra_mess)) echo "<br><em><b>".mex("Attenzione",$pag)."</b></em>: ".mex("il prezzo totale delle caparre inserite",$pag)." (<em>$caparra_mess</em> $Euro) ".mex("è diverso da quello contenuto nel messaggio di richiesta di prenotazione",$pag)." (<em>".$dati_mess_calc[1]."</em> $Euro).<br><hr style=\"width: 95%\">";
} # fine if ($idmessaggi)

} # fine if ($continuare != "NO")

if ($tabelle_lock) unlock_tabelle($tabelle_lock);
if ($continuare != "NO") {
$lock = 1;
$aggiorna_disp = 1;
$aggiorna_tar = 0;
if (@function_exists('pcntl_fork')) include("./includes/interconnect/aggiorna_ic_fork.php");
else include("./includes/interconnect/aggiorna_ic.php");
} # fine if ($continuare != "NO")

} # fine if (!$torna_invece_di_ok)

echo "
<form accept-charset=\"utf-8\" method=\"post\" action=\"prenota.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">";
if ($torna_invece_di_ok == "SI") {
echo "<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."\"></div></button><br></form>";
} # fine if ($torna_invece_di_ok == "SI")
else {
echo "<br><div style=\"text-align: center;\">
<button class=\"ires\" type=\"submit\"><div>".mex("Inserisci una nuova prenotazione",$pag)."</div></button></div>
<br></div></form>
<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"bkmm\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
<br></div></form><div style=\"height: 20px;\"></div>";
} # fine else if ($torna_invece_di_ok == "SI")

} # fine if ($idclienti)



else {


if ($mostra_form_inserisci_prenota != "NO") {

# Inizio della pagina.
echo "<h4 id=\"h_ires\"><span>".mex("Inserisci una nuova prenotazione",$pag).".</span></h4>";
if (@get_magic_quotes_gpc()) {
$cognome = stripslashes($cognome);
$nome = stripslashes($nome);
} # fine if (@get_magic_quotes_gpc())

# Form per nuova prenotazione.
echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"clienti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"origine\" value=\"prenota.php\">
<hr style=\"width: 95%\"><div class=\"linhbox\">
".mex("Cliente titolare",$pag).": <span class=\"wsnw\">".mex("cognome",$pag).": ";
if ($attiva_prefisso_clienti == "p") echo $prefisso_clienti;
echo "<input type=\"text\" name=\"cognome\" value=\"$cognome\">";
if ($attiva_prefisso_clienti == "s") echo $prefisso_clienti." ";
echo ",</span> <span class=\"wsnw\">".mex("nome",$pag).": <input type=\"text\" name=\"nome\" value=\"$nome\"></span><br>";
if ($prenota_vicine == "SI") $checked = " checked";
else $checked = "";
$mess_app_vicini = "<label><input type=\"checkbox\" name=\"prenota_vicine\" value=\"SI\"$checked> ".mex("Appartamenti vicini",'unit.php').".</label><br>";
if ($num_tipologie > 1 and $priv_ins_multiple == "s") echo $mess_app_vicini;

include("./includes/funzioni_tariffe.php");
include("./includes/funzioni_costi_agg.php");
$dati_tariffe = dati_tariffe($tablenometariffe);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,"NO","SI");

if ($priv_ins_checkin == "s") {
$attiva_checkin = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'attiva_checkin' and idutente = '$id_utente'");
$attiva_checkin = risul_query($attiva_checkin,0,'valpersonalizza');
} # fine ($priv_ins_checkin == "s")
else $attiva_checkin = "";

if ($priv_ins_assegnazione_app == "s") {
$comb_app = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'comb_app' and idutente = '$id_utente'");
if (numlin_query($comb_app) == 1) {
$comb_app = explode("<>",risul_query($comb_app,0,'valpersonalizza'));
$num_comb_app = count($comb_app) - 1;
$opt_comb_app = "";
for ($num1 = 0 ; $num1 < $num_comb_app ; $num1++) {
$nome_comb_app = explode(",",$comb_app[$num1]);
$nome_comb_app = $nome_comb_app[(count($nome_comb_app) - 1)];
$lista_comb_app = substr($comb_app[$num1],0,((strlen($nome_comb_app) + 1) * -1));
$opt_comb_app .= "<option value=\"$lista_comb_app\">$nome_comb_app</option>";
} # fine for $num1
echo "<script type=\"text/javascript\">
<!--
function agg_comb_app (nt) {
var sel_comb=document.getElementById('comb_ap'+nt);
var ind_sc = sel_comb.selectedIndex;
var txt_lista_app=document.getElementById('list_ap'+nt);
txt_lista_app.value = sel_comb.options[ind_sc].value;
}
-->
</script>";
} # fine if (numlin_query($comb_app) == 1)
else $opt_comb_app = "";
} # fine if ($priv_ins_assegnazione_app == "s")
if ($priv_ins_sconto == "s") {
echo "<script type=\"text/javascript\">
<!--
function agg_tsc (nt) {
var sel_tsc = document.getElementById('tsc'+nt);
var ind_tsc = sel_tsc.selectedIndex;
var sel_tvsc = document.getElementById('tvsc'+nt);
if (sel_tsc.options[ind_tsc].value != 'sconto') {
sel_tvsc.selectedIndex = 0;
sel_tvsc.disabled = 1;
}
else sel_tvsc.disabled = 0;
}
-->
</script>";
} # fine if ($priv_ins_sconto == "s")

$campi_pers_comm = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'campi_pers_comm' and idutente = '$id_utente'");
if (numlin_query($campi_pers_comm) == 1) $campi_pers_comm = risul_query($campi_pers_comm,0,'valpersonalizza');
else $campi_pers_comm = "";
if ($mobile_device) $cols_textarea = "36";
else $cols_textarea = "45";
if ($attiva_checkin == "SI" or $campi_pers_comm) {
echo "<script type=\"text/javascript\">
<!--
function agg_comm (nt) {
var sel = document.getElementById('s_comm'+nt);
var colsel = sel.options[sel.selectedIndex].value;
sel.remove(sel.selectedIndex);
sel.selectedIndex = 0;
var num_comm_agg = document.getElementById('num_comm'+nt);
num_commenti_agg = parseInt(num_comm_agg.value)+1;
num_comm_agg.value = num_commenti_agg;
var n_comm = document.getElementById('n_comm'+nt);
var colsel_vedi = '\"'+colsel+'\"';
if (colsel_vedi == '\"checkin\"') colsel_vedi = '".str_replace("'","\\'",mex("per un promemoria all'entrata",$pag))."';
if (colsel_vedi == '\"checkout\"') colsel_vedi = '".str_replace("'","\\'",mex("per un promemoria all'uscita",$pag))."';
n_comm_node = document.createElement('div');
n_comm_node.style.cssFloat = 'left';
n_comm_node.style.padding = '3px 12px 3px 0';
n_comm_node.innerHTML = '".mex("Commento",$pag)." '+colsel_vedi+':<br><textarea name=\"commento'+num_commenti_agg+'_'+nt+'\" rows=3 cols=$cols_textarea style=\"white-space: pre; overflow: auto;\"></textarea><input type=\"hidden\" name=\"tipo_commento'+num_commenti_agg+'_'+nt+'\" value=\"'+colsel+'\">';
n_comm.appendChild(n_comm_node);
}
-->
</script>";
} # fine if ($attiva_checkin == "SI" or $campi_pers_comm)

if ($num_tipologie > 1) echo "<table><tr><td style=\"height: 3px\"></td></tr></table><table bgcolor=\"#000000\" border=\"0\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">";
$bgcolor_tipologia = $t2row1color;

for ($n_t = 1 ; $n_t <= $num_tipologie ; $n_t++) {
if ($num_tipologie > 1) {
echo "<tr><td bgcolor=\"$bgcolor_tipologia\"><b>".mex("Tipologia",$pag)." $n_t</b>: ";
if ($bgcolor_tipologia == $t2row1color) $bgcolor_tipologia = $t2row2color;
else $bgcolor_tipologia = $t2row1color;
} # fine if ($num_tipologie > 1)
echo "<span class=\"wsnw\">".mex("Dal",$pag)." ";
if (${"num_app_richiesti".$n_t} != 1) {
${"inizioperiodo".$n_t} = explode(",",${"inizioperiodo".$n_t});
${"inizioperiodo".$n_t} = ${"inizioperiodo".$n_t}[0];
${"fineperiodo".$n_t} = explode(",",${"fineperiodo".$n_t});
${"fineperiodo".$n_t} = ${"fineperiodo".$n_t}[0];
} # fine if (${"num_app_richiesti".$n_t} != 1)
$oggi = date("Y-m-d",(time() + (C_DIFF_ORE * 3600)));
$date_select = esegui_query("select datainizio,datafine from $tableperiodi where datainizio <= '$oggi' and datafine > '$oggi' ");
if (numlin_query($date_select) != 0) {
$inizio_select = risul_query($date_select,0,'datainizio');
$fine_select = risul_query($date_select,0,'datafine');
} # fine if (numlin_query($date_select) != 0)
if (${"inizioperiodo".$n_t}) {
$date_selected = ${"inizioperiodo".$n_t};
if (controlla_num($date_selected) == "SI") {
$date_selected = esegui_query("select datainizio from $tableperiodi where idperiodi = '".aggslashdb($date_selected)."'");
$date_selected = risul_query($date_selected,0,'datainizio');
} # fine if (controlla_num($date_selected) == "SI")
} # fine if (${"inizioperiodo".$n_t})
else $date_selected = $inizio_select;
mostra_menu_date(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php","inizioperiodo$n_t",$date_selected,"","",$id_utente,$tema);
echo "</span> <span class=\"wsnw\">".mex("al",$pag)." ";
if (${"fineperiodo".$n_t}) {
$date_selected = ${"fineperiodo".$n_t};
if (controlla_num($date_selected) == "SI") {
$date_selected = esegui_query("select datafine from $tableperiodi where idperiodi = '".aggslashdb(${"fineperiodo".$n_t})."'");
$date_selected = risul_query($date_selected,0,'datafine');
} # fine if (controlla_num($date_selected) == "SI")
} # fine if (${"fineperiodo".$n_t})
else $date_selected = $fine_select;
mostra_menu_date(C_DATI_PATH."/selperiodimenu$anno.$id_utente.php","fineperiodo$n_t",$date_selected,"","",$id_utente,$tema);
if (!$nometipotariffa) $sel = " selected";
else $sel = "";
echo "</span><br>
<table id=\"ir_dat\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td>
".mex("Tipo di tariffa",$pag)." :
<select name=\"nometipotariffa$n_t\">
<option value=\"\"$sel>----</option>";
$num_tariffe = 0;
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") $num_tariffe++;
for ($numtariffa = 1 ; $numtariffa <= $dati_tariffe['num'] ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
if ($dati_tariffe[$tariffa]['nome'] == "") $nometariffa_vedi = mex("tariffa",$pag).$numtariffa;
else $nometariffa_vedi = $dati_tariffe[$tariffa]['nome'];
if (${"nometipotariffa".$n_t} == $tariffa or $num_tariffe == 1) $sel = " selected";
else $sel = "";
echo "
<option value=\"$tariffa\"$sel>$nometariffa_vedi</option>";
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
echo "</select>;</td>";
if ($priv_ins_sconto == "s") {
$sel_sconto = "";
$sel_tot = "";
$sel_tot_sett = "";
$sel_tar = "";
$sel_tar_sett = "";
if (${"tipo_sconto".$n_t} == "sconto" or !${"tipo_sconto".$n_t}) $sel_sconto = " selected";
if (${"tipo_sconto".$n_t} == "tot") $sel_tot = " selected";
if (${"tipo_sconto".$n_t} == "tot_sett") $sel_tot_sett = " selected";
if (${"tipo_sconto".$n_t} == "tar") $sel_tar = " selected";
if (${"tipo_sconto".$n_t} == "tar_sett") $sel_tar_sett = " selected";
echo "<td style=\"width: 30px;\"></td><td><select name=\"tipo_sconto$n_t\" id=\"tsc$n_t\" onchange=\"agg_tsc('$n_t')\">
<option value=\"sconto\"$sel_sconto>".mex("sconto",$pag)."</option>
<option value=\"tot\"$sel_tot>".mex("prezzo totale",$pag)."</option>
<option value=\"tot_sett\"$sel_tot_sett>".mex("prezzo totale $parola_settimanale",$pag)."</option>
<option value=\"tar\"$sel_tar>".mex("prezzo tariffa",$pag)."</option>
<option value=\"tar_sett\"$sel_tar_sett>".mex("prezzo tariffa $parola_settimanale",$pag)."</option>
</select>: <span class=\"wsnw\"><input type=\"text\" name=\"sconto$n_t\" size=\"7\" value =\"".${"sconto".$n_t}."\">";
$sel_val = "";
$sel_tot = "";
$sel_tar = "";
if (!${"tipo_val_sconto".$n_t}) $sel_val = " selected";
if (${"tipo_val_sconto".$n_t} == "tot") $sel_tot = " selected";
if (${"tipo_val_sconto".$n_t} == "tar") $sel_tar = " selected";
echo " <select name=\"tipo_val_sconto$n_t\" id=\"tvsc$n_t\">
<option value=\"\"$sel_val>$Euro</option>
<option value=\"tot\"$sel_tot>".mex("% del totale",$pag)."</option>
<option value=\"tar\"$sel_tar>".mex("% della tariffa",$pag)."</option>
</select>;</span></td>";
} # fine if ($priv_ins_sconto == "s")
echo "</tr><tr><td>";
if ($priv_ins_num_persone == "s") {
echo " ".mex("nº di persone",$pag).":
<input type=\"text\" name=\"numpersone$n_t\" size=\"2\" maxlength=\"2\" value =\"".${"numpersone".$n_t}."\">";
$punto = ".";
} # fine if ($priv_ins_num_persone == "s")
if ($priv_ins_caparra == "s") {
$sel_val = "";
$sel_tar = "";
if (!${"tipo_val_caparra".$n_t}) $sel_val = " selected";
if (${"tipo_val_caparra".$n_t} == "tar") $sel_tar = " selected";
echo ";</td><td style=\"width: 30px;\"></td><td>".mex("caparra",$pag).": <input type=\"text\" name=\"caparra$n_t\" size=\"7\" value =\"".${"caparra".$n_t}."\">
<select name=\"tipo_val_caparra$n_t\">
<option value=\"\"$sel_val>$Euro</option>
<option value=\"tar\"$sel_tar>".mex("% della tariffa",$pag)."</option>
</select> (".mex("se diversa dalla normale",$pag).")";
} # fine if ($priv_ins_caparra == "s")
echo "$punto</td></tr></table></div>";

if ($priv_ins_assegnazione_app == "s") {
echo "<br><div class=\"linhbox\">".mex("Metodo per l'assegnazione dell'appartamento",'unit.php').":<br>
·".mex("Nº fisso di appartamento",'unit.php').": 
<select name=\"appartamento$n_t\">
<option value=\"\">--</option>";
unset($condizioni_regole1_consentite);
if ($attiva_regole1_consentite == "s") {
for ($num1 = 0 ; $num1 < count($regole1_consentite) ; $num1++) {
if ($regole1_consentite[$num1]) {
if ($regole1_consentite[$num1] == " ") $appartamenti_agenzia = esegui_query("select app_agenzia from $tableregole where (motivazione = '' or motivazione is null) and app_agenzia is not null and (motivazione2 != 'x' or motivazione2 is NULL) ");
else $appartamenti_agenzia = esegui_query("select app_agenzia from $tableregole where motivazione = '".$regole1_consentite[$num1]."' and app_agenzia is not null and (motivazione2 != 'x' or motivazione2 is NULL) ");
for ($num2 = 0 ; $num2 < numlin_query($appartamenti_agenzia) ; $num2++) {
$app_agenzia = risul_query($appartamenti_agenzia,$num2,'app_agenzia');
if (str_replace(" '$app_agenzia' ","",$condizioni_regole1_consentite) == $condizioni_regole1_consentite) $condizioni_regole1_consentite .= "idappartamenti = '$app_agenzia' or ";
} # fine for $num2
} # fine if ($regole1_consentite[$num1])
} # fine for $num1
if ($condizioni_regole1_consentite) $condizioni_regole1_consentite = "where ".substr($condizioni_regole1_consentite,0,-4);
else $condizioni_regole1_consentite = "where idappartamenti is null";
} # fine if ($attiva_regole1_consentite == "s")
$appart = esegui_query("select idappartamenti from $tableappartamenti $condizioni_regole1_consentite order by idappartamenti");
for ($num1 = 0 ; $num1 < numlin_query($appart) ; $num1++) {
$idapp = risul_query($appart,$num1,'idappartamenti');
if ((!$assegnazioneapp or $assegnazioneapp == "k") and $idapp == ${"appartamento".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"$idapp\"$sel>$idapp</option>";
} # fine for $num1
if (${"lista_app".$n_t} and ${"nometipotariffa".$n_t}) {
$regola2_sel = esegui_query("select * from $tableregole where tariffa_per_app = '".aggslashdb(${"nometipotariffa".$n_t})."'");
if (numlin_query($regola2_sel) == 1) if (${"lista_app".$n_t} == risul_query($regola2_sel,0,'motivazione')) ${"lista_app".$n_t} = "";
} # fine if (${"lista_app".$n_t} and ${"nometipotariffa".$n_t})
echo "</select><br>
·".mex("Lista di appartamenti",'unit.php').":
<input type=\"text\" id=\"list_ap$n_t\" name=\"lista_app$n_t\" size=\"30\" value =\"".${"lista_app".$n_t}."\"> ";
if ($opt_comb_app) {
echo "(<select id=\"comb_ap$n_t\" onchange=\"agg_comb_app($n_t)\">
<option value=\"\" selected>--</option>$opt_comb_app</select>)";
} # fine if ($opt_comb_app)
else echo "(".mex("separati da virgole",'unit.php').")";
echo ".<br>
·".mex("Nº di piano",$pag).": <select name=\"num_piano$n_t\">";
if (!${"num_piano".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"\"$sel>--</option>";
$appart = esegui_query("select numpiano from $tableappartamenti $condizioni_regole1_consentite order by numpiano");
$num_appart = numlin_query($appart);
for ($num1 = 0 ; $num1 < $num_appart ; $num1 = $num1 + 1) {
$piano = risul_query($appart,$num1,'numpiano');
if ($piano != $ultimopiano) {
$ultimopiano = $piano;
if ($piano == ${"num_piano".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"$piano\"$sel>$piano</option>";
} # fine if ($piano != $ultimopiano)
} # fine for $num1
if (!${"num_casa".$n_t}) $sel = " selected";
else $sel = "";
echo "</select>
 ".mex("e/o di casa",$pag).": <select name=\"num_casa$n_t\">
<option value=\"\"$sel>--</option>";
$appart = esegui_query("select numcasa from $tableappartamenti $condizioni_regole1_consentite order by numcasa");
for ($num1 = 0 ; $num1 < $num_appart ; $num1 = $num1 + 1) {
$casa = risul_query($appart,$num1,'numcasa');
if ($casa != $ultimacasa) {
$ultimacasa = $casa;
if ($casa == ${"num_casa".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"$casa\"$sel>$casa</option>";
} # fine if ($piano != $ultimopiano)
} # fine for $num1
if (!${"num_persone_casa".$n_t}) $sel = " selected";
else $sel = "";
echo "</select> ".mex("e/o di persone",$pag).": <select name=\"num_persone_casa$n_t\">
<option value=\"\"$sel>--</option>";
$appart = esegui_query("select maxoccupanti from $tableappartamenti $condizioni_regole1_consentite order by maxoccupanti");
for ($num1 = 0 ; $num1 < $num_appart ; $num1 = $num1 + 1) {
$persone_casa = risul_query($appart,$num1,'maxoccupanti');
if ($persone_casa != $ultime_persone_casa) {
$ultime_persone_casa = $persone_casa;
if ($persone_casa == ${"num_persone_casa".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"$persone_casa\"$sel>$persone_casa</option>";
} # fine if ($persone_casa != $ultimepersone_casa)
} # fine for $num1
echo "</select></div>";
} # fine if ($priv_ins_assegnazione_app == "s")

echo "<br><div class=\"linhbox\">";
if ($priv_ins_checkin == "s") {
if (!${"giorno_stima_checkin".$n_t}) ${"giorno_stima_checkin".$n_t} = 1;
${"g_ckn_sel".${"giorno_stima_checkin".$n_t}."_".$n_t} = " selected";
echo mex("Orario stimato di entrata",$pag).": <span class=\"wsnw\">".mex("giorno",$pag)."
 <select name=\"giorno_stima_checkin$n_t\">
<option value=\"1\"".${"g_ckn_sel1_".$n_t}.">1</option>
<option value=\"2\"".${"g_ckn_sel2_".$n_t}.">2</option>
<option value=\"3\"".${"g_ckn_sel3_".$n_t}.">3</option>
<option value=\"4\"".${"g_ckn_sel4_".$n_t}.">4</option>
<option value=\"5\"".${"g_ckn_sel5_".$n_t}.">5</option>
<option value=\"6\"".${"g_ckn_sel6_".$n_t}.">6</option>
<option value=\"7\"".${"g_ckn_sel7_".$n_t}.">7</option>
</select>&nbsp;&nbsp;&nbsp;&nbsp;";
if (!${"ora_stima_checkin".$n_t}) $sel = " selected";
else $sel = "";
echo "<select name=\"ora_stima_checkin$n_t\">
<option value=\"\"$sel>--</option>";
for ($num1 = 0 ; $num1 < 24 ; $num1++) {
if (strlen($num1) == 1) $num1 = "0".$num1;
if ($num1 == ${"ora_stima_checkin".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"$num1\"$sel>$num1</option>";
} # fine for $num1
if (!${"min_stima_checkin".$n_t}) $sel = " selected";
else $sel = "";
echo "</select>:<select name=\"min_stima_checkin$n_t\">
<option value=\"\"$sel>--</option>";
for ($num1 = 0 ; $num1 < 60 ; $num1 = $num1 + 15) {
if (strlen($num1) == 1) $num1 = "0".$num1;
if ($num1 == ${"min_stima_checkin".$n_t}) $sel = " selected";
else $sel = "";
echo "<option value=\"$num1\"$sel>$num1</option>";
} # fine for $num1
echo "</select></span>.<br>";
} # fine if ($priv_ins_checkin == "s")
if ($priv_ins_caparra == "s") {
$metodi_pagamento = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'metodi_pagamento' and idutente = '$id_utente'");
$metodi_pagamento = risul_query($metodi_pagamento,0,'valpersonalizza');
if ($metodi_pagamento) {
if (!${"met_paga_caparra".$n_t}) $sel = " selected";
else $sel = "";
echo mex("Metodo pagamento caparra",$pag).": <select name=\"met_paga_caparra$n_t\">
<option value=\"\"$sel>----</option>";
$metodi_pagamento = explode(",",$metodi_pagamento);
for ($num1 = 0 ; $num1 < count($metodi_pagamento) ; $num1++) {
if (${"met_paga_caparra".$n_t} == $metodi_pagamento[$num1]) $sel = " selected";
else $sel = "";
echo "<option value=\"".$metodi_pagamento[$num1]."\"$sel>".$metodi_pagamento[$num1]."</option>";
} # fine for $num1
echo "</select>.<br>";
} # fine if ($metodi_pagamento)
} # fine if ($priv_ins_caparra == "s")
$origini_prenota = "";
if ($priv_ins_orig_prenota == "s") {
$origini_prenota = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'origini_prenota' and idutente = '$id_utente'");
$origini_prenota = risul_query($origini_prenota,0,'valpersonalizza');
if ($origini_prenota) {
if ($priv_ins_caparra == "s") echo "<table class=\"nomob\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td>";
if (!${"origine_prenota".$n_t}) $sel = " selected";
else $sel = "";
echo mex("Origine",$pag).": <select name=\"origine_prenota$n_t\">
<option value=\"\"$sel>----</option>";
$origini_prenota = explode(",",$origini_prenota);
for ($num1 = 0 ; $num1 < count($origini_prenota) ; $num1++) {
if (${"origine_prenota".$n_t} == $origini_prenota[$num1]) $sel = " selected";
else $sel = "";
echo "<option value=\"".$origini_prenota[$num1]."\"$sel>".$origini_prenota[$num1]."</option>";
} # fine for $num1
echo "</select>.<br>";
if ($priv_ins_caparra == "s") echo "</td><td style=\"width: 30px;\"></td><td>";
} # fine if ($origini_prenota)
} # fine if ($priv_ins_orig_prenota == "s")
if ($priv_ins_caparra == "s") {
$sel_val = "";
$sel_tar = "";
$sel_ts = "";
$sel_tsc = "";
if (!${"tipo_val_commissioni".$n_t}) $sel_val = " selected";
if (${"tipo_val_commissioni".$n_t} == "tar") $sel_tar = " selected";
if (${"tipo_val_commissioni".$n_t} == "ts") $sel_ts = " selected";
if (${"tipo_val_commissioni".$n_t} == "tsc") $sel_tsc = " selected";
echo "".mex("Commissioni",$pag).": <span class=\"wsnw\"><input type=\"text\" name=\"commissioni$n_t\" size=\"5\" value =\"".${"commissioni".$n_t}."\">
<select name=\"tipo_val_commissioni$n_t\">
<option value=\"\"$sel_val>$Euro</option>
<option value=\"tar\"$sel_tar>".mex("% della tariffa",$pag)."</option>
<option value=\"ts\"$sel_ts>".mex("% della tariffa",$pag)."+".strtolower(mex("Sconto",$pag))."</option>
<option value=\"tsc\"$sel_tsc>".mex("% del prezzo totale",$pag)."</option>
</select></span>.";
if ($origini_prenota) echo "</td></tr></table>";
} # fine if ($priv_ins_caparra == "s")
echo "</div>";
if ($priv_ins_multiple != "n") {
echo "<table class=\"nomob\"><tr><td>".mex("Nº di prenotazioni di questa tipologia",$pag).":";
if (!${"num_app_richiesti".$n_t}) ${"num_app_richiesti".$n_t} = 1;
echo "<input type=\"text\" name=\"num_app_richiesti$n_t\" size=\"2\" maxlength=\"3\" value =\"".${"num_app_richiesti".$n_t}."\">.";
if ($num_tipologie == $n_t and $num_tipologie < 999) echo "</td><td style=\"width: 80px;\"></td><td><button class=\"plum\" type=\"submit\" name=\"aggiungi_tipologie\" value =\"1\"><div>".mex("Aggiungi altre tipologie",$pag)."</div></button>";
echo "</td></tr></table>";
if ($num_tipologie == 1 and $priv_ins_multiple == "s") echo $mess_app_vicini;
elseif ($priv_ins_multiple == "s") {
if (${"prenota_vicine".$n_t} == "SI") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"prenota_vicine$n_t\" value=\"SI\"$checked> ".mex("Appartamenti vicini",'unit.php').".</label><br>";
} # fine if ($priv_ins_multiple == "s")
echo "<br>";
} # fine if ($priv_ins_multiple != "n")
else echo "<input type=\"hidden\" name=\"num_app_richiesti1\" value=\"1\">";
if ($priv_ins_conferma == "s") {
if (${"conferma_prenota".$n_t} == "SI") $checked = " checked";
else $checked = "";
echo "<label><input type=\"checkbox\" name=\"conferma_prenota$n_t\" value=\"SI\"$checked>
".ucfirst(mex("confermata",$pag)).".</label><br>";
} # fine if ($priv_ins_conferma == "s")

$numcostiagg = 0;
if ($priv_ins_costi_agg == "s") {
unset($costi_agg_raggr);
unset($chiedi_combina);
for ($num1 = 0 ; $num1 < $dati_ca['num'] ; $num1++) {
if ($attiva_costi_agg_consentiti == "n" or $costi_agg_consentiti_vett[$dati_ca[$num1]['id']] == "SI") {
if ($dati_ca[$num1]['combina'] != "s") {
$testo_costo = "";
if ($dati_ca[$num1]['raggruppa'] != "s") {
$numcostiagg++;
$numcostiagg_v = $numcostiagg;
$nome_costo = $dati_ca[$num1]['nome'];
$id_costo = $dati_ca[$num1]['id'];
} # fine if ($dati_ca[$num1]['raggruppa'] != "s")
else {
$numcostiagg_v = "[nca]";
$nome_costo = "[nome]";
$id_costo = "[id]";
} # fine else if ($dati_ca[$num1]['raggruppa'] != "s")
$costoagg = "costoagg".$numcostiagg_v;
if ($dati_ca[$num1]['tipo'] == "u") $tipo_ca = "unico";
if ($dati_ca[$num1]['tipo'] == "s") $tipo_ca = "$parola_settimanale";
if (${$costoagg."_".$n_t} == "SI") $checked = " checked";
else $checked = "";
$testo_costo .= "<input type=\"hidden\" name=\"idcostoagg$numcostiagg_v"."_$n_t\" value=\"$id_costo\">
<label><input type=\"checkbox\" id=\"ca_$numcostiagg_v"."_$n_t\" name=\"$costoagg"."_$n_t\" value=\"SI\"$checked>
".mex("costo aggiuntivo $tipo_ca",$pag)." \"<em>$nome_costo</em>\"";
if ($dati_ca[$num1]['numsett'] == "c" and $dati_ca[$num1]['associasett'] == "n") {
$numsettimane = "numsettimane".$numcostiagg_v;
if (${$numsettimane."_".$n_t}) $valnumsettimane = ${$numsettimane."_".$n_t};
else $valnumsettimane = 0;
$testo_costo .= ", ".mex("nº di $parola_settimane da applicare",$pag).":</label>
<input type=\"text\" name=\"$numsettimane"."_$n_t\" value=\"$valnumsettimane\" size=\"3\" maxlength=\"3\"
 onclick=\"document.getElementById('ca_$numcostiagg_v"."_$n_t').checked='1';\"><label for=\"ca_$numcostiagg_v"."_$n_t\">";
} # fine if ($dati_ca[$num1]['numsett'] == "c" and...
if ($dati_ca[$num1]['moltiplica'] == "c") {
$nummoltiplica_ca = "nummoltiplica_ca".$numcostiagg_v;
if (${$nummoltiplica_ca."_".$n_t}) $valnummoltiplica_ca = ${$nummoltiplica_ca."_".$n_t};
else $valnummoltiplica_ca = 1;
$testo_costo .= ", ".mex("da moltiplicare per",$pag).":</label>";
if ($dati_ca[$num1]['molt_max'] != "n") $testo_costo .= "<input type=\"text\" name=\"$nummoltiplica_ca"."_$n_t\" value=\"$valnummoltiplica_ca\" size=\"3\" maxlength=\"12\"
 onclick=\"document.getElementById('ca_$numcostiagg_v"."_$n_t').checked='1';\">";
else {
$testo_costo .= "<select name=\"$nummoltiplica_ca"."_$n_t\" onclick=\"document.getElementById('ca_$numcostiagg_v"."_$n_t').checked='1';\">";
for ($num2 = 1 ; $num2 <= $dati_ca[$num1]['molt_max_num'] ; $num2++) {
if ($num2 == $valnummoltiplica_ca) $sel = " selected";
else $sel = "";
$testo_costo .= "<option value=\"$num2\"$sel>$num2</option>";
} # fine for $num2
$testo_costo .= "</select>";
} # fine else if ($dati_ca[$num1]['molt_max'] != "n")
$testo_costo .= "<label for=\"ca_$numcostiagg_v"."_$n_t\">";
} # fine if ($dati_ca[$num1]['moltiplica'] == "c")
$testo_costo .= ".</label><br>";
} # fine if ($dati_ca[$num1]['combina'] != "s")
else {
$testo_costo = "combina";
$categ = $dati_ca[$num1]['categoria'];
if ($dati_ca[$num1]['numsett'] == "c" and $dati_ca[$num1]['associasett'] == "n") $chiedi_combina[$categ]['sett'] = 1;
if ($dati_ca[$num1]['moltiplica'] == "c") {
if (!$chiedi_combina[$categ]['molt']) $chiedi_combina[$categ]['molt_max_num'] = $dati_ca[$num1]['molt_max_num'];
if ($dati_ca[$num1]['molt_max'] != "n") $chiedi_combina[$categ]['molt_max_num'] = 0;
elseif ($chiedi_combina[$categ]['molt_max_num'] and $chiedi_combina[$categ]['molt_max_num'] < $dati_ca[$num1]['molt_max_num']) $chiedi_combina[$categ]['molt_max_num'] = $dati_ca[$num1]['molt_max_num'];
$chiedi_combina[$categ]['molt'] = 1;
} # fine if ($dati_ca[$num1]['moltiplica'] == "c")
} # fine else if ($dati_ca[$num1]['combina'] != "s")
if ($dati_ca[$num1]['raggruppa'] != "s") echo $testo_costo;
else $costi_agg_raggr[$testo_costo."<>".$dati_ca[$num1]['categoria']] .= $dati_ca[$num1]['id'].",";
} # fine if ($attiva_costi_agg_consentiti == "n" or...
} # fine for $num1

if (@is_array($costi_agg_raggr)) {
foreach ($costi_agg_raggr as $testo_costo => $id_costi) {
$testo_costo = explode("<>",$testo_costo);
$numcostiagg++;
$id_costi_vett = explode(",",substr($id_costi,0,-1));
$num_id_costi = count($id_costi_vett);
for ($num1 = 0 ; $num1 < $num_id_costi ; $num1++) {
if (${"gr_idcostoagg".$id_costi_vett[$num1]."_$n_t"} == "SI") {
${"costoagg".$numcostiagg."_".$n_t} = "SI";
${"nummoltiplica_ca".$numcostiagg."_".$n_t} = ${"gr_nummoltiplica_ca".$id_costi_vett[$num1]."_$n_t"};
${"numsettimane".$numcostiagg."_".$n_t} = ${"gr_numsettimane".$id_costi_vett[$num1]."_$n_t"};
break;
} # fine if (${"gr_idcostoagg".$id_costi_vett[$num1]."_$n_t"} == "SI")
} # fine for $num1
if ($testo_costo[0] != "combina") {
$testo_costo = $testo_costo[0];
if (${"costoagg".$numcostiagg."_".$n_t} == "SI") $testo_costo = str_replace("type=\"checkbox\"","type=\"checkbox\" checked",$testo_costo);
if (${"nummoltiplica_ca".$numcostiagg."_".$n_t}) $testo_costo = str_replace("name=\"nummoltiplica_ca[nca]_$n_t\" value=\"1\"","name=\"nummoltiplica_ca[nca]_$n_t\" value=\"".${"nummoltiplica_ca".$numcostiagg."_".$n_t}."\"",$testo_costo);
if (${"numsettimane".$numcostiagg."_".$n_t}) $testo_costo = str_replace("name=\"numsettimane[nca]_$n_t\" value=\"0\"","name=\"numsettimane[nca]_$n_t\" value=\"".${"numsettimane".$numcostiagg."_".$n_t}."\"",$testo_costo);
$testo_costo = str_replace("[nca]_$n_t",$numcostiagg."_$n_t",$testo_costo);
if ($num_id_costi == 1) {
$num_costo = $dati_ca['id'][$id_costi_vett[0]];
$testo_costo = str_replace(" \"<em>[nome]</em>\""," \"<em>".$dati_ca[$num_costo]['nome']."</em>\"",$testo_costo);
$testo_costo = str_replace(" value=\"[id]\""," value=\"".$id_costi_vett[0]."\"",$testo_costo);
} # fine (count($id_costi_vett) == 1)
else {
$sel_costi = "</label><select name=\"idcostoagg$numcostiagg"."_$n_t\" onclick=\"document.getElementById('ca_$numcostiagg"."_$n_t').checked='1';\">";
for ($num1 = 0 ; $num1 < $num_id_costi ; $num1++) {
$num_costo = $dati_ca['id'][$id_costi_vett[$num1]];
if (${"idcostoagg".$numcostiagg."_".$n_t} == $id_costi_vett[$num1]) $sel = " selected";
else $sel = "";
$sel_costi .= "<option value=\"".$id_costi_vett[$num1]."\"$sel>".$dati_ca[$num_costo]['nome']."</option>";
} # fine for $num1
$sel_costi .= "</select><label for=\"ca_$numcostiagg"."_$n_t\">";
$testo_costo = str_replace(" \"<em>[nome]</em>\""," \"$sel_costi\"",$testo_costo);
$testo_costo = str_replace("<input type=\"hidden\" name=\"idcostoagg$numcostiagg"."_$n_t\" value=\"[id]\">","",$testo_costo);
} # fine (count($id_costi_vett) == 1)
echo $testo_costo;
} # fine if ($testo_costo[0] != "combina")
else {
$categoria = $testo_costo[1];
if (${"costoagg".$numcostiagg."_".$n_t} == "SI") $checked = " checked";
else $checked = "";
echo "<input type=\"hidden\" name=\"idcostoagg$numcostiagg"."_$n_t\" value=\"c".htmlspecialchars($categoria)."\">
<label><input type=\"checkbox\" id=\"ca_$numcostiagg"."_$n_t\" name=\"costoagg$numcostiagg"."_$n_t\" value=\"SI\"$checked>
".mex("costo aggiuntivo",$pag)." \"<em>".htmlspecialchars($categoria)."</em>\"";
if ($chiedi_combina[$categoria]['sett']) {
$numsettimane = "numsettimane".$numcostiagg;
if (${$numsettimane."_".$n_t}) $valnumsettimane = ${$numsettimane."_".$n_t};
else $valnumsettimane = 0;
echo ", ".mex("nº di $parola_settimane da applicare",$pag).":</label>
<input type=\"text\" name=\"$numsettimane"."_$n_t\" value=\"$valnumsettimane\" size=\"3\" maxlength=\"3\"
 onclick=\"document.getElementById('ca_$numcostiagg"."_$n_t').checked='1';\"><label for=\"ca_$numcostiagg"."_$n_t\">";
} # fine if ($chiedi_combina[$categoria]['sett'])
if ($chiedi_combina[$categoria]['molt']) {
$nummoltiplica_ca = "nummoltiplica_ca".$numcostiagg;
if (${$nummoltiplica_ca."_".$n_t}) $valnummoltiplica_ca = ${$nummoltiplica_ca."_".$n_t};
else $valnummoltiplica_ca = 1;
echo ", ".mex("da moltiplicare per",$pag).":</label>";
if (!$chiedi_combina[$categoria]['molt_max_num']) echo "<input type=\"text\" name=\"$nummoltiplica_ca"."_$n_t\" value=\"$valnummoltiplica_ca\" size=\"3\" maxlength=\"12\"
 onclick=\"document.getElementById('ca_$numcostiagg"."_$n_t').checked='1';\">";
else {
echo "<select name=\"$nummoltiplica_ca"."_$n_t\" onclick=\"document.getElementById('ca_$numcostiagg"."_$n_t').checked='1';\">";
for ($num2 = 1 ; $num2 <= $chiedi_combina[$categoria]['molt_max_num'] ; $num2++) {
if ($num2 == $valnummoltiplica_ca) $sel = " selected";
else $sel = "";
echo "<option value=\"$num2\"$sel>$num2</option>";
} # fine for $num2
echo "</select>";
} # fine else if ($dati_ca[$num1]['molt_max'] != "n")
echo "<label for=\"ca_$numcostiagg"."_$n_t\">";
} # fine if ($chiedi_combina[$categoria]['molt'])
echo ".</label><br>";
} # fine else if ($testo_costo[0] != "combina")
} # fine foreach ($costi_agg_raggr as $testo_costo => $id_costi)
} # fine if (@is_array($costi_agg_raggr))
} # fine if ($priv_ins_costi_agg == "s")

if ($priv_ins_commento == "s" or ($priv_ins_commenti_pers == "s" and $campi_pers_comm)) {
if (@get_magic_quotes_gpc()) ${"commento".$n_t} = stripslashes(${"commento".$n_t});
${"commento".$n_t} = htmlspecialchars(${"commento".$n_t});
if (!${"num_commenti".$n_t} or ($attiva_checkin != "SI" and !$campi_pers_comm)) ${"num_commenti".$n_t} = 1;
$commenti_presenti = array();
for ($num1 = 2 ; $num1 <= ${"num_commenti".$n_t} ; $num1++) if (${"tipo_commento".$num1."_".$n_t}) $commenti_presenti[${"tipo_commento".$num1."_".$n_t}] = 1;
echo "<br>".mex("Commento",$pag)."";
if ($attiva_checkin == "SI" or $campi_pers_comm) {
$sel_null = "";
if (${"tipo_commento1_".$n_t} == "") $sel_null = " selected";
echo " <select name=\"tipo_commento1_$n_t\"t id=\"s_comm$n_t\" onchange=\"agg_comm($n_t)\">";
if ($priv_ins_commento == "s") echo"<option value=\"\"$sel_null>".mex("per la prenotazione",$pag)."</option>";
if ($priv_ins_commento == "s" and $attiva_checkin == "SI") {
$sel_checkin = "";
$sel_checkout = "";
if (${"tipo_commento1_".$n_t} == "checkin") $sel_checkin = " selected";
if (${"tipo_commento1_".$n_t} == "checkout") $sel_checkout = " selected";
if (!$commenti_presenti['checkin']) echo "<option value=\"checkin\"$sel_checkin>".mex("per un promemoria all'entrata",$pag)."</option>";
if (!$commenti_presenti['checkout']) echo "<option value=\"checkout\"$sel_checkout>".mex("per un promemoria all'uscita",$pag)."</option>";
} # fine if ($priv_ins_commento == "s" and $attiva_checkin == "SI")
if ($campi_pers_comm) {
$c_pers_comm = explode(">",$campi_pers_comm);
for ($num1 = 0 ; $num1 < count($c_pers_comm) ; $num1++) {
$sel_comm_pers = "";
if (${"tipo_commento1_".$n_t} == $c_pers_comm[$num1]) $sel_comm_pers = " selected";
if (!$commenti_presenti[$c_pers_comm[$num1]]) echo "<option value=\"".$c_pers_comm[$num1]."\"$sel_comm_pers>\"".$c_pers_comm[$num1]."\"</option>";
} # fine for $num1
} # fine if ($campi_pers_comm)
echo "</select>";
} # fine if ($attiva_checkin == "SI" or $campi_pers_comm)
echo ":<br>
 <textarea name=\"commento1_$n_t\" rows=3 cols=$cols_textarea style=\"white-space: pre; overflow: auto;\">".${"commento1_".$n_t}."</textarea><br>
<div id=\"n_comm$n_t\">";
for ($num1 = 2 ; $num1 <= ${"num_commenti".$n_t} ; $num1++) {
$nome_comm = "\"".${"tipo_commento$num1"."_".$n_t}."\"";
if ($nome_comm == "\"checkin\"") $nome_comm = mex("per un promemoria all'entrata",$pag);
if ($nome_comm == "\"checkout\"") $nome_comm = mex("per un promemoria all'uscita",$pag);
echo "<div style=\"float: left; padding: 3px 12px 3px 0;\">".mex("Commento",$pag)." $nome_comm:<br>
<textarea name=\"commento$num1"."_$n_t\" rows=3 cols=$cols_textarea style=\"white-space: pre; overflow: auto;\">".${"commento$num1"."_".$n_t}."</textarea></div>
<input type=\"hidden\" name=\"tipo_commento$num1"."_$n_t\" value=\"".${"tipo_commento$num1"."_".$n_t}."\">";
} # fine for $num1
echo "</div><div style=\"clear: both;\"></div>
<input type=\"hidden\" id=\"num_comm$n_t\" name=\"num_commenti$n_t\" value=\"1\">";
} # fine if ($priv_ins_commento == "s" or ($priv_ins_commenti_pers == "s" and $campi_pers_comm))
if ($num_tipologie > 1) echo "</td></tr>";
} # fine for $n_t

if ($num_tipologie > 1) echo "</table><br>";
if ($idmessaggi) echo "<input type=\"hidden\" name=\"idmessaggi\" value=\"$idmessaggi\">";
echo "<div style=\"text-align: center;\"><input type=\"hidden\" name=\"numcostiagg\" value=\"$numcostiagg\">
<input type=\"hidden\" name=\"num_tipologie\" value=\"$num_tipologie\">
<input type=\"hidden\" name=\"mos_tut_dat\" value=\"$mos_tut_dat\">
<input type=\"hidden\" name=\"nuovaprenotazione\" value=\"SI\">
<button id=\"inse\" class=\"ires\" type=\"submit\"><div>".mex("Inserisci la prenotazione",$pag)."</div></button>
<hr style=\"width: 95%\">
</div></div></form><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"inizio.php\"><div style=\"text-align: center;\">
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button id=\"indi\" class=\"bkmm\" type=\"submit\"><div>".mex("Torna al menù principale",$pag)."</div></button>
<br></div></form><div style=\"height: 20px;\"></div>";

} # fine if ($mostra_form_inserisci_prenota != "NO")


} # fine else if ($idclienti)



if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($anno_utente_attivato == "SI" and $priv_ins_nuove_prenota == "s")
} # fine if ($id_utente)



?>

