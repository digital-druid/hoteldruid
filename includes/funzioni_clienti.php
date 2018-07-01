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




function inserisci_dati_cliente (&$cognome,&$nome,$soprannome,$titolo_cli,$sesso,$mesenascita,$giornonascita,$annonascita,&$nazionenascita,&$cittanascita,&$regionenascita,$documento,$tipodoc,$mesescaddoc,$giornoscaddoc,$annoscaddoc,&$cittadoc,&$regionedoc,&$nazionedoc,&$nazionalita,&$lingua_cli,&$nazione,&$citta,&$regione,&$via,$nomevia,$numcivico,$cap,$telefono,$telefono2,$telefono3,$fax,$email,$cod_fiscale,$partita_iva,$max_num_ordine,$id_utente_ins,$attiva_prefisso_clienti,$prefisso_clienti,$idclienti="",$valida="",$campi_pers_vett="") {
global $lingua_mex,$HOSTNAME,$id_utente,$PHPR_TAB_PRE;
$tableclienti = $PHPR_TAB_PRE."clienti";
$tablerelclienti = $PHPR_TAB_PRE."relclienti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";

if (@is_array($campi_pers_vett) and $campi_pers_vett['num']) $num_campi_pers = $campi_pers_vett['num'];
else $num_campi_pers = 0;

if ($valida != "NO") {
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
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) $campi_pers_vett['val'][$num1] = stripslashes($campi_pers_vett['val'][$num1]);
} # fine if (@get_magic_quotes_gpc())
$cognome = htmlspecialchars($cognome);
$nome = htmlspecialchars($nome);
$soprannome = htmlspecialchars($soprannome);
$titolo_cli = htmlspecialchars($titolo_cli);
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
for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) $campi_pers_vett['val'][$num1] = htmlspecialchars($campi_pers_vett['val'][$num1]);
} # fine if ($valida != "NO")

if (!$idclienti) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$idclienti = esegui_query("select idclienti from $tableclienti where idclienti = '1'");
if (numlin_query($idclienti) == 0) $idclienti = 1;
else {
$idclienti = esegui_query("select max(idclienti) from $tableclienti");
$idclienti = risul_query($idclienti,0,0) + 1;
} # fine else if (numlin_query($idclienti) == 0)
$cognome_maius = trim(ucwords($cognome));
if ($attiva_prefisso_clienti == "p") $cognome_maius = $prefisso_clienti.$cognome_maius;
if ($attiva_prefisso_clienti == "s") $cognome_maius = $cognome_maius.$prefisso_clienti;
$cognome_maius = aggslashdb($cognome_maius);
esegui_query("insert into $tableclienti (idclienti,cognome,idclienti_compagni,datainserimento,hostinserimento,utente_inserimento) values ('$idclienti','$cognome_maius',',','$datainserimento','$HOSTNAME','$id_utente_ins')");
} # fine if (!$idclienti)
elseif ($cognome) {
$cognome_maius = trim(ucwords($cognome));
if ($attiva_prefisso_clienti == "p") $cognome_maius = $prefisso_clienti.$cognome_maius;
if ($attiva_prefisso_clienti == "s") $cognome_maius = $cognome_maius.$prefisso_clienti;
$cognome_maius = aggslashdb($cognome_maius);
esegui_query("update $tableclienti set cognome = '$cognome_maius' where idclienti = '$idclienti' ");
} # fine elseif ($cognome)
if ($nome) {
$nome_maius = trim(ucwords($nome));
$nome_maius = aggslashdb($nome_maius);
esegui_query("update $tableclienti set nome = '$nome_maius' where idclienti = '$idclienti' ");
} # fine if ($nome)
if ($soprannome) {
esegui_query("update $tableclienti set soprannome = '".aggslashdb($soprannome)."' where idclienti = '$idclienti' ");
} # fine if ($soprannome)
if ($titolo_cli) {
$titoli_cliente = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'titoli_cliente' and idutente = '$id_utente'");
if (numlin_query($titoli_cliente) == 1) {
$titoli_cliente = risul_query($titoli_cliente,0,'valpersonalizza');
$titoli_cliente = explode(">",$titoli_cliente);
for ($num1 = 0 ; $num1 < count($titoli_cliente) ; $num1++) {
$tito = explode("<",$titoli_cliente[$num1]);
if ($titolo_cli == $tito[0]) {
esegui_query("update $tableclienti set titolo = '".aggslashdb($titolo_cli)."' where idclienti = '$idclienti' ");
if ($tito[1] and !$sesso) $sesso = $tito[1];
break;
} # fine if ($titolo_cli == $opt[0])
} # fine for $num1
} # fine if (numlin_query($titoli_cliente) == 1)
} # fine if ($titolo_cli)
if ($sesso) {
if ($sesso != "f") $sesso = "m";
esegui_query("update $tableclienti set sesso = '$sesso' where idclienti = '$idclienti' ");
} # fine if ($sesso)
if ($mesenascita and $giornonascita and strlen($annonascita) == 4) {
if ($annonascita > 1970) $datanascita = date("Y-m-d",mktime(0,0,0,$mesenascita,$giornonascita,$annonascita));
else $datanascita = $annonascita."-".$mesenascita."-".$giornonascita;
$datanascita = aggslashdb($datanascita);
esegui_query("update $tableclienti set datanascita = '$datanascita' where idclienti = '$idclienti' ");
} # fine if ($mesenascita and ...
if ($nazionenascita) {
$nazionenascita_maius = trim(ucwords($nazionenascita));
$nazionenascita_maius = aggslashdb($nazionenascita_maius);
esegui_query("update $tableclienti set nazionenascita = '$nazionenascita_maius' where idclienti = '$idclienti' ");
} # fine if ($nazionenascita)
if ($cittanascita) {
$cittanascita_maius = trim(ucwords($cittanascita));
$cittanascita_maius = aggslashdb($cittanascita_maius);
esegui_query("update $tableclienti set cittanascita = '$cittanascita_maius' where idclienti = '$idclienti' ");
} # fine if ($cittanascita)
if ($regionenascita) {
$regionenascita_maius = trim(ucwords($regionenascita));
$regionenascita_maius = aggslashdb($regionenascita_maius);
esegui_query("update $tableclienti set regionenascita = '$regionenascita_maius' where idclienti = '$idclienti' ");
} # fine if ($regionenascita)
if ($documento) {
if ($tipodoc) esegui_query("update $tableclienti set tipodoc = '".aggslashdb($tipodoc)."' where idclienti = '$idclienti' ");
$documento = aggslashdb($documento);
esegui_query("update $tableclienti set documento = '$documento' where idclienti = '$idclienti' ");
} # fine if ($documento)
if ($mesescaddoc and $giornoscaddoc and $annoscaddoc) {
$datascaddoc = date("Y-m-d",mktime(0,0,0,$mesescaddoc,$giornoscaddoc,$annoscaddoc));
$datascaddoc = aggslashdb($datascaddoc);
esegui_query("update $tableclienti set scadenzadoc = '$datascaddoc' where idclienti = '$idclienti' ");
} # fine if ($mesescaddoc and ...
if ($nazionedoc) {
$nazionedoc_maius = trim(ucwords($nazionedoc));
$nazionedoc_maius = aggslashdb($nazionedoc_maius);
esegui_query("update $tableclienti set nazionedoc = '$nazionedoc_maius' where idclienti = '$idclienti' ");
} # fine if ($nazionedoc)
if ($cittadoc) {
$cittadoc_maius = trim(ucwords($cittadoc));
$cittadoc_maius = aggslashdb($cittadoc_maius);
esegui_query("update $tableclienti set cittadoc = '$cittadoc_maius' where idclienti = '$idclienti' ");
} # fine if ($cittadoc)
if ($regionedoc) {
$regionedoc_maius = trim(ucwords($regionedoc));
$regionedoc_maius = aggslashdb($regionedoc_maius);
esegui_query("update $tableclienti set regionedoc = '$regionedoc_maius' where idclienti = '$idclienti' ");
} # fine if ($regionedoc)
if ($nazionalita) {
$nazionalita_maius = trim(ucwords($nazionalita));
$nazionalita_maius = aggslashdb($nazionalita_maius);
esegui_query("update $tableclienti set nazionalita = '$nazionalita_maius' where idclienti = '$idclienti' ");
} # fine if ($nazionalita)
if ($lingua_cli) {
if (preg_replace("/[a-z]{2,3}/","",$lingua_cli) == "") {
if ($lingua_cli == $lingua_mex or $lingua_cli == "ita" or @is_dir("./includes/lang/$lingua_cli")) {
esegui_query("update $tableclienti set lingua = '".aggslashdb($lingua_cli)."' where idclienti = '$idclienti' ");
} # fine if ($lingua == $lingua_mex or $lingua_cli == "ita" or...
else $lingua_cli = "";
} # fine if (preg_replace("/[a-z]{2,3}/","",$lingua_cli) == "")
else $lingua_cli = "";
} # fine if ($lingua_cli)
if ($nazione) {
$nazione_maius = trim(ucwords($nazione));
$nazione_maius = aggslashdb($nazione_maius);
esegui_query("update $tableclienti set nazione = '$nazione_maius' where idclienti = '$idclienti' ");
} # fine if ($nazione)
if ($citta) {
$citta_maius = trim(ucwords($citta));
$citta_maius = aggslashdb($citta_maius);
esegui_query("update $tableclienti set citta = '$citta_maius' where idclienti = '$idclienti' ");
} # fine if ($citta)
if ($regione) {
$regione_maius = trim(ucwords($regione));
$regione_maius = aggslashdb($regione_maius);
esegui_query("update $tableclienti set regione = '$regione_maius' where idclienti = '$idclienti' ");
} # fine if ($regione)
if ($nomevia) {
if (strcmp($via,"")) {
if ($lingua_mex != "ita") include("./includes/lang/$lingua_mex/ordine_frasi.php");
if ($ordine_strada == 2) $via = $nomevia." ".$via;
else $via = $via." ".$nomevia;
} # fine if (strcmp($via,""))
else $via = $nomevia;
$via_maius = trim(ucwords($via));
$via_maius = aggslashdb($via_maius);
esegui_query("update $tableclienti set via = '$via_maius' where idclienti = '$idclienti' ");
} # fine if ($nomevia)
$numcivico = aggslashdb($numcivico);
if ($numcivico) esegui_query("update $tableclienti set numcivico = '$numcivico' where idclienti = '$idclienti' ");
$cap = aggslashdb($cap);
if ($cap) esegui_query("update $tableclienti set cap = '$cap' where idclienti = '$idclienti' ");
$telefono = aggslashdb($telefono);
if ($telefono) esegui_query("update $tableclienti set telefono = '$telefono' where idclienti = '$idclienti' ");
$telefono2 = aggslashdb($telefono2);
if ($telefono2) esegui_query("update $tableclienti set telefono2 = '$telefono2' where idclienti = '$idclienti' ");
$telefono3 = aggslashdb($telefono3);
if ($telefono3) esegui_query("update $tableclienti set telefono3 = '$telefono3' where idclienti = '$idclienti' ");
$fax = aggslashdb($fax);
if ($fax) esegui_query("update $tableclienti set fax = '$fax' where idclienti = '$idclienti' ");
$email = aggslashdb($email);
if ($email) esegui_query("update $tableclienti set email = '$email' where idclienti = '$idclienti' ");
$cod_fiscale = aggslashdb($cod_fiscale);
if ($cod_fiscale) esegui_query("update $tableclienti set cod_fiscale = '$cod_fiscale' where idclienti = '$idclienti' ");
$partita_iva = aggslashdb($partita_iva);
if ($partita_iva) esegui_query("update $tableclienti set partita_iva = '$partita_iva' where idclienti = '$idclienti' ");
if ($max_num_ordine) esegui_query("update $tableclienti set max_num_ordine = '".aggslashdb($max_num_ordine)."' where idclienti = '$idclienti' ");

for ($num1 = 0 ; $num1 < $num_campi_pers ; $num1++) {
if (strcmp($campi_pers_vett['val'][$num1],"")) {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
esegui_query("insert into $tablerelclienti (idclienti,numero,tipo,testo1,testo2,testo3,datainserimento,hostinserimento,utente_inserimento) values ('$idclienti','1','campo_pers','".$campi_pers_vett[$num1]."','".$campi_pers_vett['tipo'][$num1]."','".$campi_pers_vett['val'][$num1]."','$datainserimento','$HOSTNAME','$id_utente_ins') ");
} # fine if (strcmp($campi_pers_vett['val'][$num1],""))
} # fine for $num1

return $idclienti;

} # fine function inserisci_dati_cliente





function mostra_dati_cliente (&$dati_cliente,&$dcognome,&$dnome,&$dsoprannome,&$dtitolo_cli,&$dsesso,&$ddatanascita,&$ddatanascita_f,&$dnazionenascita,&$dcittanascita,&$dregionenascita,&$ddocumento,&$dscadenzadoc,&$dscadenzadoc_f,&$dtipodoc,&$dnazionedoc,&$dregionedoc,&$dcittadoc,&$dnazionalita,&$dlingua_cli,&$dnazione,&$dregione,&$dcitta,&$dvia,&$dnumcivico,&$dtelefono,&$dtelefono2,&$dtelefono3,&$dfax,&$dcap,&$demail,&$dcod_fiscale,&$dpartita_iva,$mostra_num="",$priv_ins_clienti="",$silenzio="",$mostra_commento="") {
global $pag,$id_utente,$PHPR_TAB_PRE;

if ($id_utente == 1 or !$id_utente) {
$priv_vedi_telefoni = "s";
$priv_vedi_indirizzo = "s";
} # fine if ($id_utente == 1 or !$id_utente)
else {
if (!$priv_ins_clienti) {
$privilegi_globali_utente = esegui_query("select * from $PHPR_TAB_PRE"."privilegi where idutente = '$id_utente' and anno = '1'");
$priv_ins_clienti = risul_query($privilegi_globali_utente,0,'priv_ins_clienti');
} # fine if (!$priv_ins_clienti)
$priv_vedi_telefoni = substr($priv_ins_clienti,3,1);
$priv_vedi_indirizzo = substr($priv_ins_clienti,4,1);
} # fine else if ($id_utente == 1 or !$id_utente)

$didclienti = risul_query($dati_cliente,0,'idclienti');
$dcognome = risul_query($dati_cliente,0,'cognome');
$dnome = risul_query($dati_cliente,0,'nome');
$dsoprannome = risul_query($dati_cliente,0,'soprannome');
$dtitolo_cli = risul_query($dati_cliente,0,'titolo');
$dsesso = risul_query($dati_cliente,0,'sesso');
$ddatanascita = risul_query($dati_cliente,0,'datanascita');
$ddatanascita_f = formatta_data($ddatanascita,$stile_data);
$ddocumento = risul_query($dati_cliente,0,'documento');
$dtipodoc = risul_query($dati_cliente,0,'tipodoc');
$dscadenzadoc = risul_query($dati_cliente,0,'scadenzadoc');
$dscadenzadoc_f = formatta_data($dscadenzadoc,$stile_data);
$dcittadoc = risul_query($dati_cliente,0,'cittadoc');
$dregionedoc = risul_query($dati_cliente,0,'regionedoc');
$dnazionedoc = risul_query($dati_cliente,0,'nazionedoc');
$dcittanascita = risul_query($dati_cliente,0,'cittanascita');
$dregionenascita = risul_query($dati_cliente,0,'regionenascita');
$dnazionenascita = risul_query($dati_cliente,0,'nazionenascita');
$dnazionalita = risul_query($dati_cliente,0,'nazionalita');
$dlingua_cli = risul_query($dati_cliente,0,'lingua');
$dnazione = risul_query($dati_cliente,0,'nazione');
$dregione = risul_query($dati_cliente,0,'regione');
$dcitta = risul_query($dati_cliente,0,'citta');
if ($priv_vedi_indirizzo == "s") {
$dvia = risul_query($dati_cliente,0,'via');
$dnumcivico = risul_query($dati_cliente,0,'numcivico');
$dcap = risul_query($dati_cliente,0,'cap');
} # fine if ($priv_vedi_indirizzo == "s")
if ($priv_vedi_telefoni == "s") {
$dtelefono = risul_query($dati_cliente,0,'telefono');
$dtelefono2 = risul_query($dati_cliente,0,'telefono2');
$dtelefono3 = risul_query($dati_cliente,0,'telefono3');
$dfax = risul_query($dati_cliente,0,'fax');
$demail = risul_query($dati_cliente,0,'email');
} # fine if ($priv_vedi_telefoni == "s")
$dcod_fiscale = risul_query($dati_cliente,0,'cod_fiscale');
$dpartita_iva = risul_query($dati_cliente,0,'partita_iva');
if ($mostra_commento) $dcommento = risul_query($dati_cliente,0,'commento');
else $dcommento = "";

if ($dlingua_cli) {
if ($dlingua_cli == "ita") $d_nome_lingua = "Italiano";
elseif (preg_replace("/[a-z]{2,3}/","",$dlingua_cli) == "") {
if (@is_file("./includes/lang/$dlingua_cli/l_n")) {
$d_nome_lingua = file("./includes/lang/$dlingua_cli/l_n");
$d_nome_lingua = ucfirst(togli_acapo($d_nome_lingua[0]));
} # fine if (@is_file("./includes/lang/$dlingua_cli/l_n"))
} # fine elseif (preg_replace("/[a-z]{2,3}/","",$dlingua_cli) == "")
if (!$d_nome_lingua) $dlingua_cli = "";
} # fine if ($dlingua_cli)

$output = "";

$O = "o"; $O2 = "o";
if ($dsesso == "f") $O = "a";
if ($dsesso2 == "f") $O2 = "a";
if ($mostra_num == "SI") {
$output .= "$didclienti. <em>$dcognome</em> ";
if ($dnome) $output .= "<em>$dnome</em> ";
} # fine if ($mostra_num == "SI")
else {
if ($dtitolo_cli) $output .= "$dtitolo_cli ";
$output .= "<b>$dcognome</b>";
if ($dnome) $output .= " $dnome";
if ($dsoprannome) $output .= " ($dsoprannome)";
} # fine else if ($mostra_num == "SI")
if ($ddatanascita or $dcittanascita) $output .= " ".mex("nat$O",$pag);
if ($ddatanascita) $output .= " ".mex("il",$pag)." $ddatanascita_f";
if ($dcittanascita) $output .= mex(" a",$pag)." $dcittanascita";
if ($dregionenascita or $dnazionenascita) {
$output .= " ($dregionenascita";
if ($dregionenascita and $dnazionenascita) $output .= ", ";
$output .= "$dnazionenascita)";
} # fine if ($dregionenascita or $dnazionenascita)

$lin = "";
if ($dnazionalita) $lin .= "$dnazionalita";
if ($dnazionalita and $d_nome_lingua) $lin .= " ";
if ($d_nome_lingua) $lin .= "(".mex("ln.",$pag)." <em>$d_nome_lingua</em>)";
if ($lin and $ddocumento) {
if (!$dnazionedoc or $dnazionedoc == $dnazionalita) $lin .= " - ";
else {
$output .= "<br>$lin";
$lin = "";
} # fine else if (!$dnazionedoc or...
} # fine if ($lin and $ddocumento)
if ($ddocumento) {
if ($dtipodoc) $lin .= "$dtipodoc ";
$lin .= "$ddocumento";
if ($dscadenzadoc) {
if ($dcittadoc or ($dnazionedoc and $dnazionedoc != $dnazionalita)) {
$lin .= " ($dcittadoc";
if ($dcittadoc and $dnazionedoc and $dnazionedoc != $dnazionalita) $lin .= ", ";
if ($dnazionedoc and $dnazionedoc != $dnazionalita) $lin .= "$dnazionedoc";
$lin .= ")";
} # fine if ($dcittadoc or...
if (date("Ymd",(time() + (C_DIFF_ORE * 3600))) <= str_replace("-","",$dscadenzadoc)) $lin .= " ".mex("scade",$pag)." $dscadenzadoc_f";
else $lin .= " ".mex("scade",$pag)." <font color=\"red\">$dscadenzadoc_f</font>";
} # fine if ($dscadenzadoc)
} # fine if ($ddocumento)
if ($lin) $output .= "<br>$lin";

$lin = "";
if ($dcitta) {
$lin .= "$dcitta";
if ($dvia or $dnumcivico or $dcap) $lin .= ",";
$lin .= " ";
} # fine if ($dcitta)
if ($dvia) $lin .= "$dvia ";
if ($dnumcivico) $lin .= "nº $dnumcivico ";
if ($dcap) $lin .= mex("CAP",$pag)." $dcap ";
if ($dnazione or $dregione) $lin .= "(";
if ($dregione) $lin .= $dregione;
if ($dnazione and $dregione) $lin .= ", ";
if ($dnazione) $lin .= $dnazione;
if ($dnazione or $dregione) $lin .= ") ";
if ($lin) $output .= "<br>$lin";

$lin = "";
if ($dtelefono) $lin .= mex("Telefono",$pag).": $dtelefono ";
if ($dtelefono2) $lin .= mex("2º telefono",$pag).": $dtelefono2 ";
if ($dtelefono3) $lin .= mex("3º telefono",$pag).": $dtelefono3, ";
if ($dfax) $lin .= "fax: $dfax, ";
if ($demail) $lin .= "email: <a href=\"mailto:$demail\">$demail</a> ";
if ($lin) $output .= "<br>$lin";

$lin = "";
if ($dcod_fiscale) $lin .= mex("Codice fiscale",$pag).": $dcod_fiscale ";
if ($dcod_fiscale and $dpartita_iva) $lin .= ", ";
if ($dpartita_iva) $lin .= mex("Partita iva",$pag).": $dpartita_iva ";
if ($lin) $output .= "<br>$lin";

if ($dcommento) $output = "<div style=\"float: left; padding: 0 60px 0 0;\">$output</div>
<div style=\"float: left; max-width: 400px;\"><br>".mex("Commento",$pag).": $dcommento</div><div style=\"clear: both;\"></div>";

if (!$silenzio) echo $output;
else return $output;

} # fine function mostra_dati_cliente



function mostra_funzjs_cpval () {
echo "
<script type=\"text/javascript\">
<!--

function cp_val (id,idcp,def) {
var elem = document.getElementById(id);
var elemcp = document.getElementById(idcp);
var valcp = elemcp.value;
if (valcp != '') elem.value = valcp;
else elem.value = def;
if (elem.onchange) elem.onchange();
} // fine function cp_val

-->
</script>
";
} # fine function mostra_funzjs_cpval



function mostra_funzjs_dati_rel ($mostra_cod,$pieno,$id_sessione,$anno,$var_extra = "",$pag_relutenti = "") {
if (!$pag_relutenti) $pag_relutenti = "./dati_relutenti.php";
echo "
<script type=\"text/javascript\">
<!--
var campo_agg = '';
function agg_dati_remoti (ifrm,campo_agg) {
if (campo_agg) {
var docfrm = '';
if (document.getElementById(ifrm).contentDocument) {
docfrm = document.getElementById(ifrm).contentDocument; 
}
else docfrm = document.frames[ifrm].document;
var dati_remoti = docfrm.getElementById(ifrm);
var inner_elem = document.getElementById(campo_agg.substr(2));
var funz_oc = inner_elem.onchange;
var val_oc = inner_elem.value;
document.getElementById(campo_agg).innerHTML = dati_remoti.innerHTML;
inner_elem = document.getElementById(campo_agg.substr(2));
if (funz_oc) {
inner_elem.onchange = funz_oc;
inner_elem.onchange();
}
if (val_oc) {
var val_orig = inner_elem.value;
inner_elem.value = val_oc;
if (inner_elem.value != val_oc) inner_elem.value = val_orig;
}
}
}
function ricarica_ifrm (ifrm,campo,sel,rel,rel_sup,id_ut) {
campo_agg = 'b_'+campo;
var sele = document.getElementById(sel);
var sel_ind = sele.selectedIndex;
if (sel_ind) { var val_sel = sele.options[sel_ind].value; }
else { var val_sel = sele.value }
//document.getElementById(ifrm).src = '$pag_relutenti?id_sessione=...
top.frames[ifrm].location.replace('$pag_relutenti?id_sessione=$id_sessione&anno=$anno&id='+val_sel+'&rel='+rel+'&rel_sup='+rel_sup+'&id_ut_sel='+id_ut+'&cmp='+campo+'&mostra_cod=$mostra_cod&pieno=$pieno&d=".date("siHd")."$var_extra');
}
-->
</script>
<iframe name=\"dati_rel\" id=\"dati_rel\" onload=\"agg_dati_remoti('dati_rel',campo_agg);\" style=\"width:0px; height:0px; border: 0px; visibility: hidden;\"></iframe>
";
} # fine function mostra_funzjs_dati_rel



unset($liste_relutente);
function mostra_lista_relutenti ($nome,$sel,$id_utente,$nomelista,$idlista,$idrelutenti,$tablelista,$tablerelutenti,$size="",$javascript="",$campo_opzionale="",$rel_inf_sing="",$id_rel_inf="",$rel_sup_sing="",$id_sup_sel="") {
if (!$id_sup_sel) $id_sup_sel = 0;
global $liste_relutente;
if (!$liste_relutente[$id_sup_sel][$nomelista]) {
if (!$rel_sup_sing and !$id_sup_sel) $lista_utente = esegui_query("select distinct $tablelista.$nomelista from $tablerelutenti inner join $tablelista on $tablerelutenti.$idrelutenti = $tablelista.$idlista where $tablerelutenti.idutente = '$id_utente' order by $tablelista.$nomelista");
else {
if ($id_sup_sel) $is_id = "= '$id_sup_sel'";
else $is_id = "is NULL";
$lista_utente = esegui_query("select distinct $tablelista.$nomelista from $tablerelutenti inner join $tablelista on $tablerelutenti.$idrelutenti = $tablelista.$idlista where $tablerelutenti.idutente = '$id_utente' and $tablerelutenti.idsup $is_id order by $tablelista.$nomelista");
} # fine else if (!$rel_sup_sing and !$id_sup_sel)
$num_lista_utente = numlin_query($lista_utente);
if (!$num_lista_utente) {
if (!$campo_opzionale) {
if ($size) $size = " size=\"$size\"";
else $size = "";
$liste_relutente[$id_sup_sel][$nomelista] = "<input type=\"text\" name=\"\"$size>";
} # fine if (!$campo_opzionale)
} # fine if (!$num_lista_utente)
else {
$liste_relutente[$id_sup_sel][$nomelista] = "<select name=\"\">\n<option value=\"\">------</option>";
for ($num1 = 0 ; $num1 < $num_lista_utente ; $num1++) {
$opzione = risul_query($lista_utente,$num1,$nomelista,$tablelista);
$liste_relutente[$id_sup_sel][$nomelista] .= "<option value=\"$opzione\">$opzione</option>";
} # fine for $num1
$liste_relutente[$id_sup_sel][$nomelista] .= "</select>";
} # fine else if (!$num_lista_utente)
} # fine if (!$liste_relutente[$id_sup_sel][$nomelista])
$lista_return = $liste_relutente[$id_sup_sel][$nomelista];
if ($sel) {
if (substr($lista_return,0,7) == "<select") $lista_return = str_replace("<option value=\"$sel\">","<option value=\"$sel\" selected>",$lista_return);
else $lista_return = str_replace(" name=\"\""," name=\"\" value=\"$sel\"",$lista_return);
} # fine if ($sel)
if ($rel_inf_sing) {
$inf_esist = esegui_query("select idsup from $tablerelutenti where idutente = '$id_utente' and id$rel_inf_sing is not NULL and idsup is not NULL limit 1 ");
if (!numlin_query($inf_esist)) $rel_inf_sing = "";
} # fine if ($rel_inf_sing)
if ($javascript) {
$lista_return = str_replace("\\","\\\\'",$lista_return);
$lista_return = str_replace("\n","\\\n",$lista_return);
$lista_return = str_replace("'","\'",$lista_return);
$lista_return = str_replace("</","<\/",$lista_return);
if ($rel_inf_sing) $lista_return = str_replace(" name=\"\""," name=\"\" onchange=\"ricarica_ifrm(\'dati_rel\',\'$id_rel_inf\',\'$nome\',\'$rel_inf_sing\',\'".substr($idrelutenti,2)."\',\'$id_utente\')\"",$lista_return);
if ($rel_sup_sing) $lista_return = "<b id=\"b_$nome\" style=\"font-weight: normal;\">".$lista_return."<\/b>";
} # fine if ($javascript)
else {
if ($rel_inf_sing) $lista_return = str_replace(" name=\"\""," name=\"\" onchange=\"ricarica_ifrm('dati_rel','$id_rel_inf','$nome','$rel_inf_sing','".substr($idrelutenti,2)."','$id_utente')\"",$lista_return);
if ($rel_sup_sing) $lista_return = "<b id=\"b_$nome\" style=\"font-weight: normal;\">".$lista_return."</b>";
} # fine else if ($javascript)
return str_replace(" name=\"\""," name=\"$nome\" id=\"$nome\"",$lista_return);
} # fine function mostra_lista_relutenti


?>