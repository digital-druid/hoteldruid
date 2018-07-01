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

$pag = "gestione_utenti.php";
$titolo = "HotelDruid: Gestione Utenti";

include("./costanti.php");
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
include("./includes/funzioni.php");
$tableutenti = $PHPR_TAB_PRE."utenti";
$tableprivilegi = $PHPR_TAB_PRE."privilegi";
$tablegruppi = $PHPR_TAB_PRE."gruppi";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tablesessioni = $PHPR_TAB_PRE."sessioni";
$tablerelgruppi = $PHPR_TAB_PRE."relgruppi";
$tablerelutenti = $PHPR_TAB_PRE."relutenti";
$tablenazioni = $PHPR_TAB_PRE."nazioni";
$tableregioni = $PHPR_TAB_PRE."regioni";
$tablecitta = $PHPR_TAB_PRE."citta";
$tabledocumentiid = $PHPR_TAB_PRE."documentiid";
$tableparentele = $PHPR_TAB_PRE."parentele";
$tableanni = $PHPR_TAB_PRE."anni";
$tableclienti = $PHPR_TAB_PRE."clienti";

$id_utente = controlla_login($numconnessione,$PHPR_TAB_PRE,$id_sessione,$nome_utente_phpr,$password_phpr,$anno);
if ($id_utente and $id_utente == 1) {


if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");


if ($modifica_utenti) {
$mostra_tabella_iniziale = "NO";
$tabelle_lock = array($tableutenti,$tablesessioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
if (defined('C_RESTRIZIONI_DEMO_ADMIN') and C_RESTRIZIONI_DEMO_ADMIN == "SI") $cond_escludi_admin = " where idutenti != '1'";
else $cond_escludi_admin = "";
$lista_utenti = esegui_query("select idutenti,nome_utente,password,tipo_pass from $tableutenti$cond_escludi_admin order by idutenti");

if ($continua != "SI") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_utenti\" value=\"SI\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">";
for ($num1 = 0 ; $num1 < numlin_query($lista_utenti) ; $num1++) {
$cambiato = "NO";
$id = risul_query($lista_utenti,$num1,'idutenti');
if (get_magic_quotes_gpc()) {
${"nome".$id} = stripslashes(${"nome".$id});
${"prima_pass".$id} = stripslashes(${"prima_pass".$id});
} # fine if (get_magic_quotes_gpc())
${"nome".$id} = elimina_caratteri_slash (${"nome".$id});
${"tipo_pass".$id} = aggslashdb(${"tipo_pass".$id});
${"prima_pass".$id} = aggslashdb(${"prima_pass".$id});
$nome = risul_query($lista_utenti,$num1,'nome_utente');
if (htmlspecialchars(${"nome".$id}) != ${"nome".$id}) ${"nome".$id} = $nome;
$tipo_pass = risul_query($lista_utenti,$num1,'tipo_pass');
$cambia_nome = 0;
if ($nome != ${"nome".$id} and str_replace("&","",${"nome".$id}) == ${"nome".$id}) {
$cambiato = "SI";
$cambia_nome = 1;
echo mex("Il <b>nome</b> dell'utente",$pag)." <b>$id</b> ".mex("verrà cambiato da",$pag)." <i>$nome</i> ".mex("a",$pag)." <i>".${"nome".$id}."</i>.<br>";
echo "<input type=\"hidden\" name=\"nome$id\" value=\"".${"nome".$id}."\">";
} # fine if ($nome != ${"nome".$id} and...
$n_tipo_pass = ${"tipo_pass".$id};
if ($n_tipo_pass != "5" and $n_tipo_pass != "t") $n_tipo_pass = "n";
if (defined('C_DISABILITA_PASS_ADMIN') and C_DISABILITA_PASS_ADMIN == "NO" and $id == 1 and $tipo_pass != "n" and $n_tipo_pass == "n") $n_tipo_pass = $tipo_pass;
if ($tipo_pass != $n_tipo_pass) {
$cambiato = "SI";
echo mex("Il <b>login</b> dell'utente",$pag)." <b>$id</b>";
if (!$cambia_nome) echo " (<em>$nome</em>)";
echo " ".mex("verrà cambiato da",$pag)." <i>";
switch ($tipo_pass) {
case "t":	echo mex("password conservata in chiaro",$pag); break;
case "5":	echo mex("password conservata criptata con md5",$pag); break;
case "c":	echo mex("password conservata criptata con mcrypt",$pag); break;
case "h":	echo mex("password conservata criptata con mhash",$pag); break;
default:	echo mex("disabilitato",$pag);
} # fine switch ($tipo_pass)
echo "</i> ".mex("a",$pag)." <i>";
switch ($n_tipo_pass) {
case "t":	echo mex("password conservata in chiaro",$pag); break;
case "5":	echo mex("password conservata criptata con md5",$pag); break;
case "c":	echo mex("password conservata criptata con mcrypt",$pag); break;
case "h":	echo mex("password conservata criptata con mhash",$pag); break;
default:	echo mex("disabilitato",$pag);
} # fine switch (${"tipo_pass".$id})
echo "</i>.<br>";
if ($n_tipo_pass != "n") {
echo "".mex("Inserisci una nuova password",$pag).": <input type=\"password\" name=\"prima_pass$id\" size=\"12\"><br>
".mex("Ripeti la password",$pag).": <input type=\"password\" name=\"seconda_pass$id\" size=\"12\"><br>";
} # fine if ($n_tipo_pass != "n")
echo "<input type=\"hidden\" name=\"tipo_pass$id\" value=\"$n_tipo_pass\">";
} # fine if ($tipo_pass != ${"tipo_pass".$id})
if ($cambiato == "SI") echo "<hr style=\"width: 45%; margin-left: 0; text-align: left;\">";
} # fine for $num1
echo "<button class=\"musr\" type=\"submit\"><div>".mex("Continua",$pag)."</div></button>
</div></form><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form>";
} # fine if ($continua != "SI")

else {
for ($num1 = 0 ; $num1 < numlin_query($lista_utenti) ; $num1++) {
$id = risul_query($lista_utenti,$num1,'idutenti');
${"nome".$id} = elimina_caratteri_slash (${"nome".$id});
${"tipo_pass".$id} = aggslashdb(${"tipo_pass".$id});
${"prima_pass".$id} = aggslashdb(${"prima_pass".$id});
$nome = risul_query($lista_utenti,$num1,'nome_utente');
$tipo_pass = risul_query($lista_utenti,$num1,'tipo_pass');
$nome_esistente = esegui_query("select idutenti from $tableutenti where nome_utente = '".${"nome".$id}."'");
if (str_replace("&","",${"nome".$id}) != ${"nome".$id}) $continua = "NO";
if (numlin_query($nome_esistente) != 0) {
$continua = "NO";
echo mex("<div style=\"display: inline; color: red;\">Esiste già</div> un utente chiamato",$pag)." ".${"nome".$id}.".<br>";
} # fine if (numlin_query($nome_esistente) != 0)
$n_tipo_pass = ${"tipo_pass".$id};
if ($n_tipo_pass and $tipo_pass != $n_tipo_pass) {
if ($n_tipo_pass != "n" and (!${"prima_pass".$id} or ${"prima_pass".$id} != ${"seconda_pass".$id} or ${"prima_pass".$id} != str_replace("&","",${"prima_pass".$id}))) {
$continua = "NO";
echo mex("Nuova password dell'utente",$pag)." $id ".mex("<div style=\"display: inline; color: red;\">non</div> inserita correttamente",$pag).".<br>";
} # fine if ($n_tipo_pass != "n" and (!${"prima_pass".$id} or...
} # fine if ($n_tipo_pass and $tipo_pass != $n_tipo_pass)
} # fine for $num1
if ($continua == "NO") {
echo mex("<b>Non</b> è stato effettuato nessun cambiamento",$pag).".<br>";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form>";
} # fine if ($continua == "NO")
else {
$mostra_tabella_iniziale = "SI";
for ($num1 = 0 ; $num1 < numlin_query($lista_utenti) ; $num1++) {
$id = risul_query($lista_utenti,$num1,'idutenti');
$nome = risul_query($lista_utenti,$num1,'nome_utente');
$tipo_pass = risul_query($lista_utenti,$num1,'tipo_pass');
if (${"nome".$id} and $nome != ${"nome".$id}) {
esegui_query("update $tableutenti set nome_utente = '".aggslashdb(${"nome".$id})."' where idutenti = '$id'");
} # fine if (${"nome".$id} and $nome != ${"nome".$id})
$n_tipo_pass = ${"tipo_pass".$id};
if ($n_tipo_pass and $tipo_pass != $n_tipo_pass) {
esegui_query("update $tableutenti set tipo_pass = '$n_tipo_pass' where idutenti = '$id'");
if ($n_tipo_pass != "n") {
$n_pass = ${"prima_pass".$id};
$salt = "";
if ($n_tipo_pass == "5") {
srand((double) microtime() * 1000000);
$valori = "=?#@%abcdefghijkmnpqrstuvwxzABCDEFGHJKLMNPQRSTUVWXZ1234567890";
$salt = substr($valori,rand(0,4),1);
for ($num2 = 0 ; $num2 < 19 ; $num2++) $salt .= substr($valori,rand(0,60),1);
for ($num2 = 0 ; $num2 < 15 ; $num2++) $n_pass = md5($n_pass.substr($salt,0,(20 - $num2)));
} # fine if ($n_tipo_pass == "5")
esegui_query("update $tableutenti set password = '$n_pass', salt = '$salt' where idutenti = '$id'");
if ($id == 1) {
$fileaperto = fopen(C_DATI_PATH."/abilita_login","w+");
fclose($fileaperto);
} # fine if ($id == 1)
} # fine if ($n_tipo_pass != "n")
else {
esegui_query("update $tableutenti set password = '' where idutenti = '$id'");
esegui_query("delete from $tablesessioni where idutente = '$id'");
if ($id == 1 and @is_file(C_DATI_PATH."/abilita_login")) unlink(C_DATI_PATH."/abilita_login");
} # fine else if ($n_tipo_pass != "n")
} # fine if ($n_tipo_pass and $tipo_pass != $n_tipo_pass)
} # fine for $num1
} # fine else if ($continua == "NO")
} # fine else if ($continua != "SI")

unlock_tabelle($tabelle_lock);
} # fine if ($modifica_utenti)


if ($aggiungi_utente) {
if (htmlspecialchars($nome) != $nome) $nome = "";
if ($nome) {
$nome = elimina_caratteri_slash($nome);
$tabelle_lock = array($tablepersonalizza,$tableutenti,$tableprivilegi,$tablerelutenti);
$altre_tab_lock = array($tablenazioni,$tableregioni);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0) {
$num_utenti_esistenti = esegui_query("select idutenti from $tableutenti");
$num_utenti_esistenti = numlin_query($num_utenti_esistenti);
if ($num_utenti_esistenti >= C_MASSIMO_NUM_UTENTI) $continua = "NO";
} # fine if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0)
$nome_esistente = esegui_query("select idutenti from $tableutenti where nome_utente = '$nome'");
if (numlin_query($nome_esistente) != 0) {
$continua = "NO";
echo mex("Esiste già un utente chiamato",$pag)." $nome.<br>";
} # fine if (numlin_query($nome_esistente) != 0)
if ($nome != str_replace("&","",$nome)) $continua = "NO";
if ($continua == "NO") {
$mostra_tabella_iniziale = "NO";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form>";
unlock_tabelle($tabelle_lock);
} # fine if ($continua == "NO")
else {
$datainserimento = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$nuovo_id = esegui_query("select max(idutenti) from $tableutenti");
$nuovo_id = risul_query($nuovo_id,0,0) + 1;
$nomi_contr = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '1' ");
$nomi_contr = risul_query($nomi_contr,0,'valpersonalizza');
esegui_query("insert into $tableutenti (idutenti,nome_utente,tipo_pass,datainserimento,hostinserimento) values ('$nuovo_id','$nome','n','$datainserimento','$HOSTNAME')");
esegui_query("insert into $tableprivilegi (idutente,anno,casse_consentite,priv_mod_pers,priv_ins_clienti,prefisso_clienti,priv_messaggi,priv_inventario) values ('$nuovo_id','1','n,','nnnnnn','nnnss','n,','nn','nnnnnnnnn')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('col_tab_tutte_prenota','$nuovo_id','nu#@&cg#@&in#@&fi#@&tc#@&ca#@&pa#@&ap#@&pe#@&co')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('rig_tab_tutte_prenota','$nuovo_id','to#@&ta#@&ca#@&pc')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('dati_struttura','$nuovo_id','#@&#@&#@&#@&#@&#@&#@&#@&#@&#@&#@&')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('valuta','$nuovo_id','Euro')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('arrotond_predef','$nuovo_id','1')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('arrotond_tasse','$nuovo_id','0.01')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('stile_soldi','$nuovo_id','europa')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('costi_agg_in_tab_prenota','$nuovo_id','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('aggiunta_tronca_nomi_tab1','$nuovo_id','-2')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('linee_ripeti_date_tab_mesi','$nuovo_id','25')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('mostra_giorni_tab_mesi','$nuovo_id','SI')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('colori_tab_mesi','$nuovo_id','#70C6D4,#FFD800,#FF9900,#FF3115')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_linee_tab2_prenota','$nuovo_id','30')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('nomi_contratti','$nuovo_id','".aggslashdb($nomi_contr)."')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_tutte_prenota','$nuovo_id','200')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('selezione_tab_tutte_prenota','$nuovo_id','tutte')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_tutti_clienti','$nuovo_id','200')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_messaggi','$nuovo_id','80')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_casse','$nuovo_id','50')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('tot_giornalero_tab_casse','$nuovo_id','gior,mens,tab')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_doc_salvati','$nuovo_id','100')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('num_righe_tab_storia_soldi','$nuovo_id','200')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('stile_data','$nuovo_id','europa')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('ore_anticipa_periodo_corrente','$nuovo_id','0')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('metodi_pagamento','$nuovo_id','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('origini_prenota','$nuovo_id','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('attiva_checkin','$nuovo_id','NO')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('mostra_quadro_disp','$nuovo_id','')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('ordine_inventario','$nuovo_id','alf')");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza) values ('tasti_pos','$nuovo_id','x2;x10;s;+1;+2;+3;+4;+5;+6;+7;+8;+9;s;-1')");
$filelock = crea_lock_file(C_DATI_PATH."/lingua.php");
include(C_DATI_PATH."/lingua.php");
$file_lingua = @file(C_DATI_PATH."/lingua.php");
$linee = array();
$num_lin = 0;
for ($num1 = 0 ; $num1 < count($file_lingua) ; $num1++) {
if (substr($file_lingua[$num1],0,2) == "?>") {
$linee[$num_lin] = "\$lingua[".$nuovo_id."] = \"".$lingua[1]."\";
";
$num_lin++;
$linee[$num_lin] = $file_lingua[$num1];
} # fine if (substr($file_lingua[$num1],0,2) ==..
else $linee[$num_lin] = $file_lingua[$num1];
$num_lin++;
} # fine for $num1
scrivi_file ($linee,C_DATI_PATH."/lingua.php");
distruggi_lock_file($filelock,C_DATI_PATH."/lingua.php");
$filelock = crea_lock_file(C_DATI_PATH."/tema.php");
include(C_DATI_PATH."/tema.php");
$file_tema = @file(C_DATI_PATH."/tema.php");
$linee = array();
$num_lin = 0;
for ($num1 = 0 ; $num1 < count($file_tema) ; $num1++) {
if (substr($file_tema[$num1],0,2) == "?>") {
$linee[$num_lin] = "\$tema[".$nuovo_id."] = \"blu\";
";
$num_lin++;
$linee[$num_lin] = $file_tema[$num1];
} # fine if (substr($file_lingua[$num1],0,2) ==..
else $linee[$num_lin] = $file_tema[$num1];
$num_lin++;
} # fine for $num1
scrivi_file ($linee,C_DATI_PATH."/tema.php");
distruggi_lock_file($filelock,C_DATI_PATH."/tema.php");
include("./includes/funzioni_relutenti.php");
unlock_tabelle($tabelle_lock);
aggiorna_relutenti("","","","SI",$id_utente,$nuovo_id,"","","","","",1,"","nazione","nazioni",$tablenazioni,$tablerelutenti);
aggiorna_relutenti("","","","SI",$id_utente,$nuovo_id,"","","","","",1,"","regione","regioni",$tableregioni,$tablerelutenti);
#aggiorna_relutenti("","","","SI",$id_utente,$nuovo_id,"","","","","",1,"","citta","citta",$tablecitta,$tablerelutenti);
aggiorna_relutenti("","","","SI",$id_utente,$nuovo_id,"","","","","",1,"","documentoid","documentiid",$tabledocumentiid,$tablerelutenti);
aggiorna_relutenti("","","","SI",$id_utente,$nuovo_id,"","","","","",1,"","parentela","parentele",$tableparentele,$tablerelutenti);
} # fine else if ($continua == "NO")

} # fine if ($nome)
} # fine if ($aggiungi_utente)


if ($modifica_pass) {
$tabelle_lock = array($tableutenti);
$altre_tab_lock = "";
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$dati_utente = esegui_query("select * from $tableutenti where idutenti = '".aggslashdb($id_utente_pass)."' ");
if (numlin_query($dati_utente) != 1) $continua = "NO";
else $nome_utente = risul_query($dati_utente,0,'nome_utente');
if ($id_utente_pass == 1 and defined('C_RESTRIZIONI_DEMO_ADMIN') and C_RESTRIZIONI_DEMO_ADMIN == "SI") $continua = "NO";
if ($continua == "SI" and ($prima_pass != $seconda_pass or $prima_pass == "" or $prima_pass != str_replace("&","",$prima_pass))) {
unset($continua);
echo mex("Le nuove password non coincidono",$pag).".<br><br>";
} # fine if ($continua == "SI" and ($prima_pass != $seconda_pass or...
if (!$continua) {
$mostra_tabella_iniziale = "NO";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_pass\" value=\"SI\">
<input type=\"hidden\" name=\"id_utente_pass\" value=\"$id_utente_pass\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
".mex("Inserisci una nuova password per l'utente",$pag)." <b>$nome_utente</b>.<br><br>
".mex("Nuova password",$pag).": <input type=\"password\" name=\"prima_pass\" size=\"12\"><br>
".mex("Ripeti la password",$pag).": <input type=\"password\" name=\"seconda_pass\" size=\"12\"><br><br>";
echo "<input type=\"hidden\" name=\"tipo_pass$id\" value=\"$n_tipo_pass\">";
if ($cambiato == "SI") echo "<hr style=\"width: 45%; margin-left: 0; text-align: left;\">";
echo "<button class=\"cont\" type=\"submit\"><div>".mex("Continua",$pag)."</div></button>
</div></form><br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form>";
} # fine if (!$continua)
if ($continua == "SI") {
$tipo_pass = risul_query($dati_utente,0,'tipo_pass');
if ($tipo_pass != "n") {
$salt = "";
if ($tipo_pass == "5") {
srand((double) microtime() * 1000000);
$valori = "=?#@%abcdefghijkmnpqrstuvwxzABCDEFGHJKLMNPQRSTUVWXZ1234567890";
$salt = substr($valori,rand(0,4),1);
for ($num1 = 0 ; $num1 < 19 ; $num1++) $salt .= substr($valori,rand(0,60),1);
for ($num1 = 0 ; $num1 < 15 ; $num1++) $prima_pass = md5($prima_pass.substr($salt,0,(20 - $num1)));
} # fine if ($tipo_pass == "5")
esegui_query("update $tableutenti set password = '$prima_pass', salt = '$salt' where idutenti = '$id_utente_pass'");
} # fine if ($tipo_pass != "n")
} # fine if ($continua == "SI")
unlock_tabelle($tabelle_lock);
} # fine if ($modifica_pass)


if ($modifica_gruppi) {
if (htmlspecialchars($nuovo_gruppo) != $nuovo_gruppo) $nuovo_gruppo = "";
$nuovo_gruppo = elimina_caratteri_slash($nuovo_gruppo);
$tabelle_lock = array("$tableutenti","$tablegruppi","$tablerelgruppi");
$altre_tab_lock = "";
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$dati_utente = esegui_query("select * from $tableutenti where idutenti = '".aggslashdb($id_utente_mod)."' ");
if (numlin_query($dati_utente) != 1) $continua = "NO";
else $nome_utente = risul_query($dati_utente,0,"nome_utente");
unset($nome_gruppo);
$lista_gruppi = esegui_query("select idgruppi,nome_gruppo from $tablegruppi order by idgruppi");
$num_lista_gruppi = numlin_query($lista_gruppi);
for ($num1 = 0 ; $num1 < $num_lista_gruppi ; $num1++) {
$id_gruppo = risul_query($lista_gruppi,$num1,"idgruppi");
$nome_gruppo[$id_gruppo] = risul_query($lista_gruppi,$num1,"nome_gruppo");
} # fine for $num1
if ($nuovo_gruppo) {
if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0) {
$num_gruppi_esistenti = esegui_query("select idgruppi from $tablegruppi");
$num_gruppi_esistenti = numlin_query($num_gruppi_esistenti);
if ($num_gruppi_esistenti >= C_MASSIMO_NUM_UTENTI) $continua = "NO";
} # fine if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0)
$nome_esistente = esegui_query("select idgruppi from $tablegruppi where nome_gruppo = '".aggslashdb($nuovo_gruppo)."'");
if (numlin_query($nome_esistente) != 0) {
$continua = "NO";
echo mex("Esiste già un gruppo chiamato",$pag)." $nuovo_gruppo.<br>";
} # fine if (numlin_query($nome_esistente) != 0)
if ($nuovo_gruppo != str_replace("&","",$nuovo_gruppo)) $continua = "NO";
} # fine if ($nuovo_gruppo)
if ($continua != "NO") {
unset($gruppi_utente);
$gruppi = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id_utente_mod' and idgruppo is not NULL ");
$num_gruppi = numlin_query($gruppi);
for ($num1 = 0 ; $num1 < $num_gruppi ; $num1++) $gruppi_utente[risul_query($gruppi,$num1,'idgruppo')] = "SI";
} # fine if ($continua != "NO")
if (!$continua) {
$mostra_tabella_iniziale = "NO";
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"modifica_gruppi\" value=\"SI\">
<input type=\"hidden\" name=\"id_utente_mod\" value=\"$id_utente_mod\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
".mex("Gruppi dell'utente",$pag)." <b>$nome_utente</b>:<br><br>";
for ($num1 = 0 ; $num1 < $num_lista_gruppi ; $num1++) {
$id_gruppo = risul_query($lista_gruppi,$num1,'idgruppi');
if ($gruppi_utente[$id_gruppo] == "SI") { $checked = " checked"; $tag_b = "<b>"; $slash_b = "</b>"; }
else { $checked = ""; $tag_b = ""; $slash_b = ""; }
echo "<label><input type=\"checkbox\" name=\"gruppo$id_gruppo\" value=\"SI\"$checked> $tag_b".$nome_gruppo[$id_gruppo]."$slash_b</label><br>";
} # fine for $num1
echo "".mex("Nuovo gruppo",$pag)." <input type=\"text\" name=\"nuovo_gruppo\" size=\"12\"><br><br>
<button class=\"edit\" type=\"submit\"><div>".mex("Modifica",$pag)."</div></button>
</div></form><br><hr style=\"width: 45%; margin-left: 0; text-align: left;\"><br>";
} # fine if (!$continua)
if ($continua == "SI") {
$gruppo_log = "";
$n_phpr_log = "";
if (substr($PHPR_LOG,0,2) == "SI" and $PHPR_LOG != "SI" and substr($PHPR_LOG,2,1) != ",") {
$gruppo_log = explode(",",substr($PHPR_LOG,2));
$gruppo_log = $gruppo_log[0];
} # fine if (substr($PHPR_LOG,0,2) == "SI" and $PHPR_LOG != "SI" and substr($PHPR_LOG,2,1) != ",")
for ($num1 = 0 ; $num1 < $num_lista_gruppi ; $num1++) {
$id_gruppo = risul_query($lista_gruppi,$num1,'idgruppi');
if ($gruppi_utente[$id_gruppo] != "SI" and ${"gruppo".$id_gruppo} == "SI") {
esegui_query("insert into $tablerelgruppi (idutente,idgruppo) values ('$id_utente_mod','$id_gruppo')");
if ($id_gruppo == $gruppo_log) $n_phpr_log = $PHPR_LOG.",$id_utente_mod";
} # fine if ($gruppi_utente[$id_gruppo] != "SI" and ${"gruppo".$id_gruppo} == "SI")
if ($gruppi_utente[$id_gruppo] == "SI" and ${"gruppo".$id_gruppo} != "SI") {
esegui_query("delete from $tablerelgruppi where idutente = '$id_utente_mod' and idgruppo = '$id_gruppo' ");
$gruppo_presente = esegui_query("select idgruppo from $tablerelgruppi where idgruppo = '$id_gruppo'");
if (numlin_query($gruppo_presente) == 0) {
esegui_query("delete from $tablegruppi where idgruppi = '$id_gruppo' ");
if ($id_gruppo == $gruppo_log) $n_phpr_log = "NO";
} # fine if (numlin_query($gruppo_presente) == 0)
elseif ($id_gruppo == $gruppo_log) $n_phpr_log = substr(str_replace(",$id_utente_mod,",",","$PHPR_LOG,"),0,-1);
} # fine if ($gruppi_utente[$id_gruppo] == "SI" and ${"gruppo".$id_gruppo} != "SI") 
} # fine for $num1
if ($nuovo_gruppo) {
$nuovo_id = esegui_query("select max(idgruppi) from $tablegruppi");
$nuovo_id = risul_query($nuovo_id,0,0) + 1;
esegui_query("insert into $tablegruppi (idgruppi,nome_gruppo) values ('$nuovo_id','".aggslashdb($nuovo_gruppo)."') ");
esegui_query("insert into $tablerelgruppi (idutente,idgruppo) values ('$id_utente_mod','$nuovo_id')");
} # fine if ($nuovo_gruppo)
if ($n_phpr_log) {
$filelock = crea_lock_file(C_DATI_PATH."/dati_connessione.php");
if ($file_dati_conn = @file(C_DATI_PATH."/dati_connessione.php")) {
for ($num1 = 0 ; $num1 < count($file_dati_conn) ; $num1++) {
if (substr($file_dati_conn[$num1],0,9) == "\$PHPR_LOG") $file_dati_conn[$num1] = "\$PHPR_LOG = \"$n_phpr_log\";
";
} # fine for $num1
scrivi_file ($file_dati_conn,C_DATI_PATH."/dati_connessione.php");
if ($n_phpr_log == "NO" and @is_file(C_DATI_PATH."/log_utenti.php")) unlink(C_DATI_PATH."/log_utenti.php");
} # fine if ($file_dati_conn = @file(C_DATI_PATH."/dati_connessione.php"))
distruggi_lock_file($filelock,C_DATI_PATH."/dati_connessione.php");
} # fine if ($n_phpr_log)
echo "".mex("Aggiornati i gruppi dell'utente",$pag)." $nome_utente!<br>";
} # fine if ($continua == "SI")
unlock_tabelle($tabelle_lock);
if ($continua == "NO") $mostra_tabella_iniziale = "NO";
if ($mostra_tabella_iniziale == "NO") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form>";
} # fine if ($mostra_tabella_iniziale == "NO")
} # fine if ($modifica_gruppi)


if ($cancella or $importa_priv) {
function cancella_relazioni_utente ($tablerelutenti,$idrelutenti,$tablerel,$idrel) {
$relazioni = esegui_query("select $idrel from $tablerel");
$num_rel = numlin_query($relazioni);
for ($num1 = 0 ; $num1 < $num_rel ; $num1++) {
$relazione = aggslashdb(risul_query($relazioni,$num1,$idrel));
$rel_esist = esegui_query("select $idrelutenti from $tablerelutenti where $idrelutenti = '$relazione' ");
if (numlin_query($rel_esist) == 0) esegui_query("delete from $tablerel where $idrel = '$relazione' ");
} # fine for $num1
} # fine function cancella_relazioni_utente
} # fine if ($cancella or $importa_priv)


if ($cancella) {
if ($id_utente_canc == 1) $id_utente_canc = 0;
$id_utente_canc = aggslashdb($id_utente_canc);
$dati_collegati = "NO";
$clienti_esist = esegui_query("select idclienti from $tableclienti where utente_inserimento = '$id_utente_canc' ");
if (numlin_query($clienti_esist) > 0) $dati_collegati = "SI";
if ($dati_collegati != "SI") {
$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);
for ($num2 = 0 ; $num2 < $num_anni ; $num2++) {
$anno_mostra = risul_query($anni,$num2,'idanni');
$tableprenota_mostra = $PHPR_TAB_PRE."prenota".$anno_mostra;
$prenota_esist = esegui_query("select idprenota from $tableprenota_mostra where utente_inserimento = '$id_utente_canc' ");
if (numlin_query($prenota_esist) > 0) { $dati_collegati = "SI"; break; }
$tablecosti_mostra = $PHPR_TAB_PRE."costi".$anno_mostra;
$costo_esist = esegui_query("select idcosti from $tablecosti_mostra where utente_inserimento = '$id_utente_canc' ");
if (numlin_query($costo_esist) > 0) { $dati_collegati = "SI"; break; }
$tablesoldi_mostra = $PHPR_TAB_PRE."soldi".$anno_mostra;
$soldo_esist = esegui_query("select idsoldi from $tablesoldi_mostra where utente_inserimento = '$id_utente_canc' ");
if (numlin_query($soldo_esist) > 0) { $dati_collegati = "SI"; break; }
} # fine for $num2
} # fine if ($dati_collegati != "SI")
if ($dati_collegati == "SI") $continua = "NO";
$tabelle_lock = array($tablenazioni,$tableregioni,$tablecitta,$tableparentele,$tablepersonalizza,$tableutenti,$tablegruppi,$tableprivilegi,$tablerelutenti,$tablerelgruppi);
$altre_tab_lock = "";
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$dati_utente = esegui_query("select * from $tableutenti where idutenti = '$id_utente_canc' ");
if (numlin_query($dati_utente) != 1) $continua = "NO";
if ($continua != "NO") {
$nome_utente = risul_query($dati_utente,0,"nome_utente");
if ($continua != "SI") {
$mostra_tabella_iniziale = "NO";
echo "".mex("Si è sicuri di voler <b style=\"font-weight: normal; color: red;\">cancellare</b> l'utente",$pag)." <b>$nome_utente</b>?<br>
<table><tr><td style=\"height: 2px;\"></td></tr><tr><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"cancella\" value=\"SI\">
<input type=\"hidden\" name=\"id_utente_canc\" value=\"$id_utente_canc\">
<input type=\"hidden\" name=\"continua\" value=\"SI\">
<button class=\"cusr\" type=\"submit\"><div>".mex("SI",$pag)."</div></button>
</div></form></td><td>
<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("NO",$pag)."</div></button>
</div></form></td></tr></table><br>";
} # fine if ($continua != "SI")
else {
esegui_query("delete from $tableutenti where idutenti = '$id_utente_canc' ");
esegui_query("delete from $tableprivilegi where idutente = '$id_utente_canc' ");
esegui_query("delete from $tablerelutenti where idutente = '$id_utente_canc' ");
esegui_query("delete from $tablerelgruppi where idutente = '$id_utente_canc' ");
esegui_query("delete from $tablepersonalizza where idutente = '$id_utente_canc' ");
cancella_relazioni_utente($tablerelgruppi,"idgruppo",$tablegruppi,"idgruppi");
cancella_relazioni_utente($tablerelutenti,"idnazione",$tablenazioni,"idnazioni");
cancella_relazioni_utente($tablerelutenti,"idregione",$tableregioni,"idregioni");
cancella_relazioni_utente($tablerelutenti,"idcitta",$tablecitta,"idcitta");
cancella_relazioni_utente($tablerelutenti,"idparentela",$tableparentele,"idparentele");
$filelock = crea_lock_file(C_DATI_PATH."/lingua.php");
include(C_DATI_PATH."/lingua.php");
$file_lingua = @file(C_DATI_PATH."/lingua.php");
unset($linee);
$num_lin = 0;
$ini_lin = "\$lingua[$id_utente_canc]";
for ($num1 = 0 ; $num1 < count($file_lingua) ; $num1++) {
if (substr($file_lingua[$num1],0,strlen($ini_lin)) != $ini_lin) {
$linee[$num_lin] = $file_lingua[$num1];
$num_lin++;
} # fine if (substr($file_lingua[$num1],0,strlen($ini_lin)) != $ini_lin)
} # fine for $num1
scrivi_file ($linee,C_DATI_PATH."/lingua.php");
distruggi_lock_file($filelock,C_DATI_PATH."/lingua.php");
$filelock = crea_lock_file(C_DATI_PATH."/tema.php");
include(C_DATI_PATH."/tema.php");
$file_tema = @file(C_DATI_PATH."/tema.php");
unset($linee);
$num_lin = 0;
$ini_lin = "\$tema[$id_utente_canc]";
for ($num1 = 0 ; $num1 < count($file_tema) ; $num1++) {
if (substr($file_tema[$num1],0,strlen($ini_lin)) != $ini_lin) {
$linee[$num_lin] = $file_tema[$num1];
$num_lin++;
} # fine if (substr($file_tema[$num1],0,strlen($ini_lin)) != $ini_lin)
} # fine for $num1
scrivi_file($linee,C_DATI_PATH."/tema.php");
distruggi_lock_file($filelock,C_DATI_PATH."/tema.php");
} # fine else if ($continua != "SI")
} # fine if ($continua != "NO")
unlock_tabelle($tabelle_lock);
} # fine if ($cancella)


if ($importa_priv) {
$id_utente_importa = aggslashdb($id_utente_importa);
$id_utente_esporta = aggslashdb($id_utente_esporta);
include("./includes/funzioni_menu.php");
include(C_DATI_PATH."/lingua.php");
$tabelle_lock = array($tablepersonalizza,$tablegruppi,$tableprivilegi,$tablerelgruppi);
$altre_tab_lock = array($tableanni,$tableutenti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
unset($id_utenti_importa);
if (substr($id_utente_importa,0,2) == "gr") {
$id_gruppo = substr($id_utente_importa,2);
$ut_imp = esegui_query("select idutente from $tablerelgruppi where idgruppo = '$id_gruppo' ");
$num_ut_imp = numlin_query($ut_imp);
for ($num1 = 0 ; $num1 < $num_ut_imp ; $num1++) $id_utenti_importa[$num1] = risul_query($ut_imp,$num1,'idutente');
} # fine if (substr($id_utente_importa,0,2) == "gr")
else $id_utenti_importa[0] = $id_utente_importa;
for ($num_imp = 0 ; $num_imp < count($id_utenti_importa) ; $num_imp++) {
$id_utente_importa = $id_utenti_importa[$num_imp];
if ($id_utente_importa >= 2 and $id_utente_esporta >= 2 and $id_utente_importa != $id_utente_esporta) {
$utente_imp = esegui_query("select * from $tableutenti where idutenti = '$id_utente_importa' ");
$utente_esp = esegui_query("select * from $tableutenti where idutenti = '$id_utente_esporta' ");
if (numlin_query($utente_imp) == 1 and numlin_query($utente_esp) == 1) {

if ($tipo_importa == "pers" or $tipo_importa == "priv_pers" or $tipo_importa == "pers_grup" or $tipo_importa == "priv_pers_grup") {
esegui_query("delete from $tablepersonalizza where idutente = '$id_utente_importa' and idpersonalizza NOT $LIKE 'giorno_vedi_ini_sett%' ");
$pers = esegui_query("select * from $tablepersonalizza where idutente = '$id_utente_esporta' and idpersonalizza NOT $LIKE 'giorno_vedi_ini_sett%' ");
$num_pers = numlin_query($pers);
for ($num1 = 0 ; $num1 < $num_pers ; $num1++) {
$e_idpersonalizza = aggslashdb(risul_query($pers,$num1,'idpersonalizza'));
$e_valpersonalizza = aggslashdb(risul_query($pers,$num1,'valpersonalizza'));
$e_valpersonalizza_num = aggslashdb(risul_query($pers,$num1,'valpersonalizza_num'));
if (strcmp($e_valpersonalizza,"")) {
$valpersonalizza = ",valpersonalizza";
$e_valpersonalizza = ",'$e_valpersonalizza'";
} # fine if (strcmp($e_valpersonalizza,""))
else $valpersonalizza = "";
if (strcmp($e_valpersonalizza_num,"")) {
$valpersonalizza_num = ",valpersonalizza_num";
$e_valpersonalizza_num = ",'$e_valpersonalizza_num'";
} # fine if (strcmp($e_valpersonalizza_num,""))
else $valpersonalizza_num = "";
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente$valpersonalizza$valpersonalizza_num) values ('$e_idpersonalizza','$id_utente_importa'$e_valpersonalizza$e_valpersonalizza_num) ");
} # fine for $num1
$pers_importate = "SI";
} # fine if ($tipo_importa == "pers" or $tipo_importa == "priv_pers" or $tipo_importa == "pers_grup" or $tipo_importa == "pers_grup")

if ($tipo_importa == "priv" or $tipo_importa == "priv_pers" or $tipo_importa == "priv_grup" or $tipo_importa == "priv_pers_grup") {
esegui_query("delete from $tableprivilegi where idutente = '$id_utente_importa' ");
$priv = esegui_query("select * from $tableprivilegi where idutente = '$id_utente_esporta' ");
$num_priv = numlin_query($priv);
for ($num1 = 0 ; $num1 < $num_priv ; $num1++) {
$e_anno = aggslashdb(risul_query($priv,$num1,'anno'));
$e_regole1_consentite = aggslashdb(risul_query($priv,$num1,'regole1_consentite'));
$e_tariffe_consentite = aggslashdb(risul_query($priv,$num1,'tariffe_consentite'));
$e_costi_agg_consentiti = aggslashdb(risul_query($priv,$num1,'costi_agg_consentiti'));
$e_contratti_consentiti = aggslashdb(risul_query($priv,$num1,'contratti_consentiti'));
$e_casse_consentite = aggslashdb(risul_query($priv,$num1,'casse_consentite'));
$e_cassa_pagamenti = aggslashdb(risul_query($priv,$num1,'cassa_pagamenti'));
$e_priv_ins_prenota = aggslashdb(risul_query($priv,$num1,'priv_ins_prenota'));
$e_priv_mod_prenota = aggslashdb(risul_query($priv,$num1,'priv_mod_prenota'));
$e_priv_mod_pers = aggslashdb(risul_query($priv,$num1,'priv_mod_pers'));
$e_priv_ins_clienti = aggslashdb(risul_query($priv,$num1,'priv_ins_clienti'));
$e_prefisso_clienti = aggslashdb(risul_query($priv,$num1,'prefisso_clienti'));
$e_priv_ins_costi = aggslashdb(risul_query($priv,$num1,'priv_ins_costi'));
$e_priv_vedi_tab = aggslashdb(risul_query($priv,$num1,'priv_vedi_tab'));
$e_priv_ins_tariffe = aggslashdb(risul_query($priv,$num1,'priv_ins_tariffe'));
$e_priv_ins_regole = aggslashdb(risul_query($priv,$num1,'priv_ins_regole'));
$e_priv_messaggi = aggslashdb(risul_query($priv,$num1,'priv_messaggi'));
$e_priv_inventario = aggslashdb(risul_query($priv,$num1,'priv_inventario'));
esegui_query("insert into $tableprivilegi (idutente,anno,regole1_consentite,tariffe_consentite,costi_agg_consentiti,contratti_consentiti,casse_consentite,cassa_pagamenti,priv_ins_prenota,priv_mod_prenota,priv_mod_pers,priv_ins_clienti,prefisso_clienti,priv_ins_costi,priv_vedi_tab,priv_ins_tariffe,priv_ins_regole,priv_messaggi,priv_inventario)
 values ('$id_utente_importa','$e_anno','$e_regole1_consentite','$e_tariffe_consentite','$e_costi_agg_consentiti','$e_contratti_consentiti','$e_casse_consentite','$e_cassa_pagamenti','$e_priv_ins_prenota','$e_priv_mod_prenota','$e_priv_mod_pers','$e_priv_ins_clienti','$e_prefisso_clienti','$e_priv_ins_costi','$e_priv_vedi_tab','$e_priv_ins_tariffe','$e_priv_ins_regole','$e_priv_messaggi','$e_priv_inventario')");
if ($e_anno != 1) {
$tipo_periodi_cambia = esegui_query("select * from $tableanni where idanni = '$e_anno'");
$tipo_periodi_cambia = risul_query($tipo_periodi_cambia,0,'tipo_periodi');
$lingua_mex = $lingua[$id_utente_importa];
crea_menu_date(C_DATI_PATH."/selectperiodi$e_anno.$id_utente_esporta.php",C_DATI_PATH."/selectperiodi$e_anno.$id_utente_importa.php",$tipo_periodi_cambia);
crea_menu_date(C_DATI_PATH."/selperiodimenu$e_anno.$id_utente_esporta.php",C_DATI_PATH."/selperiodimenu$e_anno.$id_utente_importa.php",$tipo_periodi_cambia);
$lingua_mex = $lingua[$id_utente];
$giorno_vedi_ini_sett = esegui_query("select valpersonalizza_num from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$e_anno' and idutente = '$id_utente_esporta'");
if (numlin_query($giorno_vedi_ini_sett) == 1) {
$giorno_vedi_ini_sett = risul_query($giorno_vedi_ini_sett,0,'valpersonalizza_num');
esegui_query("delete from $tablepersonalizza where idpersonalizza = 'giorno_vedi_ini_sett$e_anno' and idutente = '$id_utente_importa'");
esegui_query("insert into $tablepersonalizza (idpersonalizza,idutente,valpersonalizza_num) values ('giorno_vedi_ini_sett$e_anno','$id_utente_importa','$giorno_vedi_ini_sett')");
} # fine if (numlin_query($giorno_vedi_ini_sett) == 1)
} # fine if ($e_anno != 1)
} # fine for $num1
$priv_importati = "SI";
} # fine if ($tipo_importa == "priv" or $tipo_importa == "priv_pers" or $tipo_importa == "priv_grup" or $tipo_importa == "priv_pers_grup")

if ($tipo_importa == "grup" or $tipo_importa == "priv_grup" or $tipo_importa == "pers_grup" or $tipo_importa == "priv_pers_grup") {
esegui_query("delete from $tablerelgruppi where idutente = '$id_utente_importa' ");
$grup= esegui_query("select * from $tablerelgruppi where idutente = '$id_utente_esporta' ");
$num_grup = numlin_query($grup);
for ($num1 = 0 ; $num1 < $num_grup ; $num1++) {
$idgruppo_imp = risul_query($grup,$num1,'idgruppo');
esegui_query("insert into $tablerelgruppi (idutente,idgruppo) values ('$id_utente_importa','$idgruppo_imp') ");
} # fine for $num1
cancella_relazioni_utente($tablerelgruppi,"idgruppo",$tablegruppi,"idgruppi");
$grup_importati = "SI";
} # fine if ($tipo_importa == "grup" or $tipo_importa == "priv_grup" or $tipo_importa == "pers_grup" or $tipo_importa == "priv_pers_grup")

} # fine if (numlin_query($utente_imp) == 1 and numlin_query($utente_esp) == 1)
} # fine if ($id_utente_importa >= 2 and...
} # fine for $num_imp
if ($priv_importati == "SI") echo "<b>".mex("Privilegi importati",$pag).".</b><br>";
if ($pers_importate == "SI") echo "<b>".mex("Personalizzazioni importate",$pag).".</b><br>";
if ($grup_importati == "SI") echo "<b>".mex("Gruppi importati",$pag).".</b><br>";
unlock_tabelle($tabelle_lock);
} # fine if ($importa_priv)



if ($mostra_tabella_iniziale != "NO") {

echo "<h4 id=\"h_usrs\"><span>".mex("Gestione degli utenti di hoteldruid",$pag)."</span></h4><br>";

$tabelle_lock = "";
$altre_tab_lock = array("$tableutenti","$tablegruppi","$tablerelgruppi");
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$lista_utenti = esegui_query("select idutenti,nome_utente,password,tipo_pass from $tableutenti order by idutenti");
$num_lista_utenti = numlin_query($lista_utenti);
$lista_gruppi = esegui_query("select idgruppi,nome_gruppo from $tablegruppi order by idgruppi");
$num_lista_gruppi = numlin_query($lista_gruppi);
unset($nome_gruppo);
unset($gruppi_utente);
unset($gruppi_utente_nome);
for ($num1 = 0 ; $num1 < $num_lista_gruppi ; $num1++) {
$id_gruppo = risul_query($lista_gruppi,$num1,'idgruppi');
$nome_gruppo[$id_gruppo] = risul_query($lista_gruppi,$num1,'nome_gruppo');
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_lista_utenti ; $num1++) {
$id = risul_query($lista_utenti,$num1,'idutenti');
$gruppi = esegui_query("select idgruppo from $tablerelgruppi where idutente = '$id' and idgruppo is not NULL ");
$num_gruppi = numlin_query($gruppi);
for ($num2 = 0 ; $num2 < $num_gruppi ; $num2++) {
$id_gruppo = risul_query($gruppi,$num2,'idgruppo');
$gruppi_utente[$id][$num2] = $id_gruppo;
$gruppi_utente_nome[$id][$num2] = $nome_gruppo[$id_gruppo];
} # fine for $num2
} # fine for $num1
unlock_tabelle($tabelle_lock);

echo "<div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<div class=\"tab_cont\">
<table class=\"usrs\" style=\"margin-left: auto; margin-right: auto;\" border=1 cellspacing=1 cellpadding=1>
<tr style=\"background-color: $t1color;\"><td align=\"center\">".mex("N°",$pag)."</td>
<td align=\"center\">".mex("nome",$pag)."</td>
<td align=\"center\">".mex("login",$pag)."</td>
<td align=\"center\">".mex("modifica",$pag)."</td></tr>";
$anni = esegui_query("select * from $tableanni order by idanni");
$num_anni = numlin_query($anni);

for ($num1 = 0 ; $num1 < $num_lista_utenti ; $num1++) {
$id = risul_query($lista_utenti,$num1,'idutenti');
$nome = risul_query($lista_utenti,$num1,'nome_utente');
$tipo_pass = risul_query($lista_utenti,$num1,'tipo_pass');
if ($tipo_pass == "n") $disab_sel = " selected";
else $disab_sel = "";
if ($tipo_pass == "t") $testo_sel = " selected";
else $testo_sel = "";
if ($tipo_pass == "5" or $tipo_pass == "t") $md5_sel = " selected";
else $md5_sel = "";
if ($tipo_pass == "c") $mcrypt_sel = " selected";
else $mcrypt_sel = "";
if ($tipo_pass == "h") $mhash_sel = " selected";
else $mhash_sel = "";
echo "<tr><td align=\"center\" valign=\"middle\">$id";
if ($id == 1) echo "*";
echo "</td>
<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"nome$id\" size=\"15\" value=\"$nome\"></td>
<td align=\"center\" valign=\"middle\"><select name=\"tipo_pass$id\">";
if (!defined('C_DISABILITA_PASS_ADMIN') or C_DISABILITA_PASS_ADMIN != "NO" or $id != 1 or $disab_sel) echo "<option value=\"n\"$disab_sel>".mex("disabilitato",$pag)."</option>";
#echo "<option value=\"t\"$testo_sel>".mex("password conservata in chiaro",$pag)."</option>";
echo "<option value=\"5\"$md5_sel>".mex("password criptata con md5",$pag)."</option>";
#echo "<option value=\"c\"$mcrypt_sel>".mex("password criptata con mcrypt",$pag)."</option>
#<option value=\"h\"$mhash_sel>".mex("password criptata con mhash",$pag)."</option>";
echo "</select>";
if ($id == 1) echo "**";
echo "</td>
<td align=\"center\" valign=\"middle\"><small>";
if ($tipo_pass != "n") echo "<a href=\"./gestione_utenti.php?anno=$anno&amp;id_sessione=$id_sessione&amp;modifica_pass=SI&amp;id_utente_pass=$id\">".mex("password",$pag)."</a>";
if ($tipo_pass != "n" and $id != 1) echo "<br>";
if ($id != 1) echo "<a href=\"./privilegi_utenti.php?anno=$anno&amp;id_sessione=$id_sessione&amp;id_utente_privilegi=$id\">".mex("privilegi",$pag)."</a>";
if (!is_array($gruppi_utente_nome[$id])) $titolo_gruppi = "";
else $titolo_gruppi = " title=\"".htmlspecialchars(implode(", ",$gruppi_utente_nome[$id]))."\"";
echo "<br><a$titolo_gruppi href=\"./gestione_utenti.php?anno=$anno&amp;id_sessione=$id_sessione&amp;modifica_gruppi=SI&amp;id_utente_mod=$id\">".mex("gruppi",$pag)."</a>";
if ($id != 1) {
$dati_collegati = "NO";
$clienti_esist = esegui_query("select idclienti from $tableclienti where utente_inserimento = '$id' ");
if (numlin_query($clienti_esist) > 0) $dati_collegati = "SI";
if ($dati_collegati != "SI") {
for ($num2 = 0 ; $num2 < $num_anni ; $num2++) {
$anno_mostra = risul_query($anni,$num2,'idanni');
$tableprenota_mostra = $PHPR_TAB_PRE."prenota".$anno_mostra;
$prenota_esist = esegui_query("select idprenota from $tableprenota_mostra where utente_inserimento = '$id' ");
if (numlin_query($prenota_esist) > 0) { $dati_collegati = "SI"; break; }
$tablecosti_mostra = $PHPR_TAB_PRE."costi".$anno_mostra;
$costo_esist = esegui_query("select idcosti from $tablecosti_mostra where utente_inserimento = '$id' ");
if (numlin_query($costo_esist) > 0) { $dati_collegati = "SI"; break; }
$tablesoldi_mostra = $PHPR_TAB_PRE."soldi".$anno_mostra;
$soldo_esist = esegui_query("select idsoldi from $tablesoldi_mostra where utente_inserimento = '$id' ");
if (numlin_query($soldo_esist) > 0) { $dati_collegati = "SI"; break; }
} # fine for $num2
} # fine if ($dati_collegati != "SI")
if ($dati_collegati != "SI") echo "<br><a href=\"./gestione_utenti.php?anno=$anno&amp;id_sessione=$id_sessione&amp;cancella=SI&amp;id_utente_canc=$id\">".mex("cancella",$pag)."</a>";
} # fine if ($id != 1)
echo "</small></td></tr>";
} # fine for $num1

echo "</table></div>
<small>* ".mex("Amministratore",$pag).".&nbsp;&nbsp;&nbsp;
** ".mex("Abilitare per usare altri utenti",$pag).".</small><br>
<input type=\"hidden\" name=\"modifica_utenti\" value=\"1\">
<button class=\"musr\" type=\"submit\"><div>".mex("Modifica gli utenti",$pag)."</div></button>
</div></form></div><br>";

if ($num_lista_utenti > 2) {
$option_select_utenti = "";
for ($num1 = 0 ; $num1 < $num_lista_utenti ; $num1++) {
$idutenti = risul_query($lista_utenti,$num1,'idutenti');
$nome_utente = risul_query($lista_utenti,$num1,'nome_utente');
if ($idutenti != 1) $option_select_utenti .= "<option value=\"$idutenti\">$nome_utente</option>";
} # fine for $num1
$option_select_utenti = "<option value=\"\" selected>----</option>".$option_select_utenti;
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"importa_priv\" value=\"SI\">
".mex("Importa",$pag)."
 <select name=\"tipo_importa\">
<option value=\"priv_pers_grup\"> ".mex("privilegi, personalizzazioni e gruppi",$pag)."</option>
<option value=\"priv_pers\"> ".mex("privilegi e personalizzazioni",$pag)."</option>
<option value=\"priv_grup\"> ".mex("privilegi e gruppi",$pag)."</option>
<option value=\"pers_grup\"> ".mex("personalizzazioni e gruppi",$pag)."</option>
<option value=\"priv\"> ".mex("solo i privilegi",$pag)."</option>
<option value=\"pers\"> ".mex("solo le personalizzazioni",$pag)."</option>
<option value=\"grup\"> ".mex("solo i gruppi",$pag)."</option>
</select>
 <select name=\"id_utente_importa\">";
echo str_replace("\">","\">".mex("dell'utente",$pag)." ",$option_select_utenti);
$gruppi = esegui_query("select * from $tablegruppi order by idgruppi ");
$num_gruppi = numlin_query($gruppi);
for ($num1 = 0 ; $num1 < $num_gruppi ; $num1++) {
$id_gruppo = risul_query($gruppi,$num1,'idgruppi');
$nome_gruppo = risul_query($gruppi,$num1,'nome_gruppo');
echo "<option value=\"gr$id_gruppo\">".mex("del gruppo",$pag)." $nome_gruppo</option>";
} # fine for $num1
echo "</select>
".mex("dall'utente",$pag)."
<select name=\"id_utente_esporta\">$option_select_utenti</select>
<button class=\"xusr\" type=\"submit\"><div>".mex("Importa",$pag)."</div></button>
</div></form><br>";
} # fine if ($num_lista_utenti > 2)

if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0) {
$num_utenti_esistenti = esegui_query("select idutenti from $tableutenti");
$num_utenti_esistenti = numlin_query($num_utenti_esistenti);
if ($num_utenti_esistenti >= C_MASSIMO_NUM_UTENTI) $aggiungi_utenti = "NO";
} # fine if (defined("C_MASSIMO_NUM_UTENTI") and C_MASSIMO_NUM_UTENTI != 0)
if ($aggiungi_utenti != "NO") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"gestione_utenti.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
".mex("Aggiungi",$pag)."
 ".mex("un nuovo utente chiamato",$pag)." <input type=\"text\" name=\"nome\" size=\"15\">
<input type=\"hidden\" name=\"aggiungi_utente\" value=\"1\">
<button class=\"ausr\" type=\"submit\"><div>".mex("Aggiungi",$pag)."</div></button>
</div></form>";
} # fine if ($aggiungi_utenti != "NO")

echo "<br><div style=\"text-align: center;\">
<form accept-charset=\"utf-8\" method=\"post\" action=\"personalizza.php\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<button class=\"gobk\" type=\"submit\"><div>".mex("Torna indietro",$pag)."</div></button>
</div></form><br></div>";

} # fine if ($mostra_tabella_iniziale != "NO")



if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/foot.php");
else include("./includes/foot.php");


} # fine if ($id_utente and $id_utente == 1)



?>
