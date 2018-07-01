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



function trova_codice_rel ($rel,&$rel_esist,$rel_sing,$rel_plur,&$cod2,&$cod3,$tablerel,$tablerelutenti,$id_utente) {
$cod2 = "";
$cod3 = "";
if (strcmp($rel,"")) {
if (!$rel_esist['e'][$rel_sing][$rel]) {
$cod = esegui_query("select distinct $tablerel.codice_$rel_sing,$tablerel.codice2_$rel_sing,$tablerel.codice3_$rel_sing from $tablerelutenti inner join $tablerel on $tablerelutenti.id$rel_sing = $tablerel.id$rel_plur where $tablerelutenti.idutente = '$id_utente' and $tablerel.nome_$rel_sing = '".aggslashdb($rel)."'");
if (numlin_query($cod)) {
$cod2 = risul_query($cod,0,"codice2_$rel_sing",$tablerel);
$cod3 = risul_query($cod,0,"codice3_$rel_sing",$tablerel);
$cod = risul_query($cod,0,"codice_$rel_sing",$tablerel);
} # fine if (numlin_query($cod))
else {
$cod = esegui_query("select codice_$rel_sing,codice2_$rel_sing,codice3_$rel_sing from $tablerel where nome_$rel_sing = '".aggslashdb($rel)."'");
if (numlin_query($cod)) {
$cod2 = risul_query($cod,0,"codice2_$rel_sing");
$cod3 = risul_query($cod,0,"codice3_$rel_sing");
$cod = risul_query($cod,0,"codice_$rel_sing");
} # fine if (numlin_query($cod))
else {
$cod = "";
$cod2 = "";
$cod3 = "";
} # fine else if (numlin_query($cod))
} # fine else if (numlin_query($cod))
$rel_esist['e'][$rel_sing][$rel] = 1;
$rel_esist[$rel_sing][$rel][1] = $cod;
$rel_esist[$rel_sing][$rel][2] = $cod2;
$rel_esist[$rel_sing][$rel][3] = $cod3;
} # fine if (!$rel_esist['e'][$rel_sing][$rel])
else {
$cod = $rel_esist[$rel_sing][$rel][1];
$cod2 = $rel_esist[$rel_sing][$rel][2];
$cod3 = $rel_esist[$rel_sing][$rel][3];
} # fine else if (!$rel_esist['e'][$rel_sing][$rel])
} # fine if (strcmp($rel,""))
return $cod;
} # fine function trova_codice_rel




function formatta_data_contr ($data,$stile_data) {
global $cache_date_contr;
if (!$cache_date_contr[$data]) $cache_date_contr[$data] = formatta_data($data,$stile_data);
return $cache_date_contr[$data];
} # fine function formatta_data_contr




function formatta_dir_salva_doc ($dir_salva) {
if ($dir_salva == "~") {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") {
if (C_CARTELLA_DOC != "" and @is_dir(C_CARTELLA_CREA_MODELLI."/".C_CARTELLA_DOC)) $dir_salva = C_CARTELLA_DOC;
else $dir_salva = "";
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else $dir_salva = C_DATI_PATH;
} # fine if ($dir_salva == "~")
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") $dir_salva = C_CARTELLA_CREA_MODELLI."/".str_replace("..","",$dir_salva);
if (!@is_dir($dir_salva)) $dir_salva = "";
return $dir_salva;
} # fine function formatta_dir_salva_doc




function trova_nomi_contratti (&$max_contr,$id_utente,$tablecontratti,$tablepersonalizza,$LIKE,$pag) {
unset($nomi_contratti);
$max_contr = esegui_query("select max(numero) from $tablecontratti where tipo $LIKE 'contr%'");
$max_contr = risul_query($max_contr,0,0);
for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) $nomi_contratti[$num1] = mex("documento",$pag)."$num1";
$nomi_contr = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'nomi_contratti' and idutente = '$id_utente'");
$nomi_contr = risul_query($nomi_contr,0,'valpersonalizza');
$nomi_contr = explode("#@&",$nomi_contr);
$num_nomi_contr = count($nomi_contr);
for ($num1 = 0 ; $num1 < $num_nomi_contr ; $num1++) {
$dati_nome_contr = explode("#?&",$nomi_contr[$num1]);
$nome_contr = str_replace("..","",str_replace("/","_",str_replace("\\","_",$dati_nome_contr[1])));
if (strcmp(trim($nome_contr),"")) {
$nomi_contratti[$dati_nome_contr[0]] = $nome_contr;
$nomi_contratti['pers'][$dati_nome_contr[0]] = $nome_contr;
} # fine if (strcmp(trim($nome_contr),""))
} # fine for $num1
for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) {
$nomefile_esistente = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'nomefile' ");
if (numlin_query($nomefile_esistente)) $nomi_contratti['salv'][$num1] = risul_query($nomefile_esistente,0,'testo');
else $nomi_contratti['salv'][$num1] = $nomi_contratti[$num1];
$nomi_contratti['salv'][$num1] = str_replace("\\","_",str_replace("/","_",str_replace(" ","_",$nomi_contratti['salv'][$num1])));
$dir_salva = esegui_query("select * from $tablecontratti where numero = '$num1' and tipo = 'dir'");
if (numlin_query($dir_salva)) $nomi_contratti['dir'][$num1] = formatta_dir_salva_doc(risul_query($dir_salva,0,'testo'));
} # fine for $num1
for ($num1 = 1 ; $num1 <= $max_contr ; $num1++) {
if ($nomi_contratti['dir'][$num1]) {
$num_prog = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'num_prog' ");
if (numlin_query($num_prog)) {
$num_prog = risul_query($num_prog,0,'testo');
if ($nomi_contratti['dir'][$num_prog]) {
$nomi_contr_np[$nomi_contratti['dir'][$num1]."/".$nomi_contratti['salv'][$num1]][$nomi_contratti['dir'][$num_prog]."/".$nomi_contratti['salv'][$num_prog]] = 1;
$nomi_contr_np[$nomi_contratti['dir'][$num_prog]."/".$nomi_contratti['salv'][$num_prog]][$nomi_contratti['dir'][$num1]."/".$nomi_contratti['salv'][$num1]] = 1;
} # fine if($nomi_contratti['dir'][$num_prog])
} # fine if (numlin_query($num_prog))
$compress = esegui_query("select testo from $tablecontratti where numero = '$num1' and tipo = 'compress' ");
if (numlin_query($compress)) $nomi_contratti['compress'][$num1] = risul_query($compress,0,'testo');
} # fine if ($nomi_contratti['dir'][$num1])
} # fine for $num1
if (!empty($nomi_contr_np)) {
$num_contr_np = 0;
foreach ($nomi_contr_np as $contr => $arr_contr) {
$key_contr_np[$num_contr_np] = $contr;
$num_contr_np++;
} # fine foreach ($nomi_contr_np as $contr => $arr_contr)
for ($num1 = 0 ; $num1 < $num_contr_np ; $num1++) {
$arr_contr = $nomi_contr_np[$key_contr_np[$num1]];
$arr_contr2 = $arr_contr;
reset($arr_contr);
foreach ($arr_contr as $contr2 => $val_contr2) {
reset($arr_contr2);
foreach ($arr_contr2 as $altro_contr2 => $val_altro) {
if ($contr2 != $altro_contr2) {
$nomi_contr_np[$contr2][$altro_contr2] = 1;
$nomi_contr_np[$altro_contr2][$contr2] = 1;
} # fine if ($contr2 != $altro_contr2)
} # fine foreach ($arr_contr2 as $altro_contr2 => $val_altro)
} # fine foreach ($arr_contr as $contr2 => $val_contr2)
} # fine for $num1
$nomi_contratti['num_prog'] = $nomi_contr_np;
} # fine if (!empty($nomi_contr_np))

return $nomi_contratti;

} # fine function trova_nomi_contratti




function bottone_submit_contr ($val,$id="",$name="",$class="") {
global $origine;
if ($id) $id = " id=\"$id\"";
if ($name) $name = " name=\"$name\"";
if ($class) $class = " class=\"$class\"";
if (substr($origine,0,17) == "punto_vendita.php") $risul = "<button class=\"pos\"$id type=\"submit\"$name value=\"$val\" style=\"min-height: 60px; min-width: 70px; max-width: 110px;\">$val</button>";
else $risul = "<button $class$id type=\"submit\"$name value=\"$val\"><div>$val</div></button>";
return $risul;
} # fine function bottone_submit_contr




function calcola_tasse_contr ($prezzo,$perc_tasse,$arrotond_tasse,&$tasse,&$tasse_p,&$resto_tasse,&$resto_tasse_p,$stile_soldi) {
if (!$perc_tasse) $tasse = 0;
else {
$prezzo = (double) $prezzo;
if ($perc_tasse == -1) $tasse = $prezzo;
else {
$perc_tasse = (double) $perc_tasse;
$arrotond_tasse = (double) $arrotond_tasse;
$tasse = ($prezzo / ($perc_tasse + 100)) * $perc_tasse;
$tasse = $tasse / $arrotond_tasse;
$tasse = round($tasse);
$tasse = $tasse * $arrotond_tasse;
} # fine else if ($perc_tasse == -1)
} # fine else if (!$perc_tasse)
$resto_tasse = $prezzo - $tasse;
$tasse_p = punti_in_num($tasse,$stile_soldi);
$resto_tasse_p = punti_in_num($resto_tasse,$stile_soldi);
} # fine function calcola_tasse_contr




function trasforma_id_in_date ($stringa_date,&$date_id,$tableperiodi) {

if ($stringa_date) {
$stringa_date_vett = explode(",",$stringa_date);
$n_date = count($stringa_date_vett);
for ($num1 = 0 ; $num1 < $n_date ; $num1++) {
if ($stringa_date_vett[$num1]) {
if (!$date_id[$stringa_date_vett[$num1]] and controlla_num_pos($stringa_date_vett[$num1]) == "SI") {
$data = esegui_query("select datainizio from $tableperiodi where idperiodi = '".$stringa_date_vett[$num1]."' ");
if (numlin_query($data)) $date_id[$stringa_date_vett[$num1]] = risul_query($data,0,'datainizio');
} # fine if (!$date_id[$stringa_date_vett[$num1]] and controlla_num_pos($stringa_date_vett[$num1]) == "SI")
if ($date_id[$stringa_date_vett[$num1]]) $stringa_date_vett[$num1] = $date_id[$stringa_date_vett[$num1]];
} # fine if ($stringa_date_vett[$num1])
} # fine for $num1
$stringa_date = implode(",",$stringa_date_vett);
} # fine if ($stringa_date)

return $stringa_date;

} # fine function trasforma_id_in_date




function conv_ascii ($val,$extended="") {

$val = str_replace("&quot;","\"",$val);
$val = str_replace("&#039;","'",$val);
$val = str_replace("&lt;","<",$val);
$val = str_replace("&gt;",">",$val);
$val = str_replace("&amp;","&",$val);
if (!$extended) {
$val = str_replace("ñ","n",$val);
$val = str_replace("à","a",$val);
$val = str_replace("è","e",$val);
$val = str_replace("ì","i",$val);
$val = str_replace("ò","o",$val);
$val = str_replace("ù","u",$val);
$val = str_replace("á","a",$val);
$val = str_replace("é","e",$val);
$val = str_replace("í","i",$val);
$val = str_replace("ó","o",$val);
$val = str_replace("ú","u",$val);
$val = str_replace("ä","a",$val);
$val = str_replace("ö","o",$val);
$val = str_replace("ü","u",$val);
$val = str_replace("ß","s",$val);
$val = str_replace("ç","c",$val);
$val = str_replace("ã","a",$val);
$val = str_replace("õ","o",$val);
$val = str_replace("Ñ","N",$val);
$val = str_replace("À","A",$val);
$val = str_replace("È","E",$val);
$val = str_replace("Ì","I",$val);
$val = str_replace("Ò","O",$val);
$val = str_replace("Ù","U",$val);
$val = str_replace("Á","A",$val);
$val = str_replace("É","E",$val);
$val = str_replace("Í","I",$val);
$val = str_replace("Ó","O",$val);
$val = str_replace("Ú","U",$val);
$val = str_replace("Ä","A",$val);
$val = str_replace("Ö","O",$val);
$val = str_replace("Ü","U",$val);
$val = str_replace("Ç","C",$val);
$val = str_replace("Ã","A",$val);
$val = str_replace("Õ","O",$val);
$val = str_replace("ø","o",$val);
$val = str_replace("Ø","O",$val);
$val = str_replace("°","o",$val);
$val = str_replace("ý","y",$val);
$val = str_replace("Ý","Y",$val);
} # fine if (!$extended)
$val = str_replace("€","E",$val);
$val = str_replace("Α","A",$val);
$val = str_replace("α","a",$val);
$val = str_replace("Β","B",$val);
$val = str_replace("β","b",$val);
$val = str_replace("Γ","G",$val);
$val = str_replace("γ","g",$val);
$val = str_replace("Δ","D",$val);
$val = str_replace("δ","d",$val);
$val = str_replace("Ε","E",$val);
$val = str_replace("ε","e",$val);
$val = str_replace("Ζ","Z",$val);
$val = str_replace("ζ","z",$val);
$val = str_replace("Η","H",$val);
$val = str_replace("η","n",$val);
$val = str_replace("Θ","O",$val);
$val = str_replace("θ","O",$val);
$val = str_replace("Ι","I",$val);
$val = str_replace("ι","i",$val);
$val = str_replace("Κ","K",$val);
$val = str_replace("κ","k",$val);
$val = str_replace("Λ","L",$val);
$val = str_replace("λ","l",$val);
$val = str_replace("Μ","M",$val);
$val = str_replace("μ","m",$val);
$val = str_replace("Ν","N",$val);
$val = str_replace("ν","v",$val);
$val = str_replace("Ξ","E",$val);
$val = str_replace("ξ","e",$val);
$val = str_replace("Ο","O",$val);
$val = str_replace("ο","o",$val);
$val = str_replace("Π","N",$val);
$val = str_replace("π","p",$val);
$val = str_replace("Ρ","P",$val);
$val = str_replace("ρ","p",$val);
$val = str_replace("Σ","S",$val);
$val = str_replace("σ","s",$val);
$val = str_replace("ς","c",$val);
$val = str_replace("Τ","T",$val);
$val = str_replace("τ","t",$val);
$val = str_replace("Υ","Y",$val);
$val = str_replace("υ","v",$val);
$val = str_replace("Φ","F",$val);
$val = str_replace("φ","f",$val);
$val = str_replace("Χ","x",$val);
$val = str_replace("χ","X",$val);
$val = str_replace("Ψ","P",$val);
$val = str_replace("ψ","p",$val);
$val = str_replace("Ω","O",$val);
$val = str_replace("ω","o",$val);
$val = str_replace("Ά","A",$val);
$val = str_replace("ά","a",$val);
$val = str_replace("Ό","O",$val);
$val = str_replace("ό","o",$val);
$val = str_replace("Ή","H",$val);
$val = str_replace("ή","n",$val);
$val = str_replace("Ί","I",$val);
$val = str_replace("ί","i",$val);
$val = str_replace("Ύ","Y",$val);
$val = str_replace("ύ","v",$val);
$val = str_replace("Ώ","O",$val);
$val = str_replace("ώ","o",$val);
$val = str_replace("Έ","E",$val);
$val = str_replace("έ","e",$val);
$val = str_replace("ž","z",$val);
$val = str_replace("ř","r",$val);
$val = str_replace("č","c",$val);
$val = str_replace("š","s",$val);
$val = str_replace("ě","e",$val);
$val = str_replace("ů","u",$val);
$val = str_replace("Ž","Z",$val);
$val = str_replace("Ř","R",$val);
$val = str_replace("Č","C",$val);
$val = str_replace("Š","S",$val);
$val = str_replace("Ě","E",$val);
$val = str_replace("Ů","U",$val);

if (function_exists('mb_convert_encoding')) {
if (function_exists('mb_substitute_character')) mb_substitute_character("none");
$val = mb_convert_encoding($val,"UTF-8","UTF-8");
} # fine if (function_exists('mb_convert_encoding'))
if (!$extended) $val = iconv("UTF-8","ASCII//TRANSLIT",$val);
else $val = iconv("UTF-8","CP437//TRANSLIT",$val);

return $val;

} # fine function conv_ascii




function crea_contratto ($numero_contratto,&$tipo_contratto,$id_utente,$id_sessione,$origine,$origine_vecchia,$trad_var_vett=array(),$set_glob="") {

global $var_predef,$num_var_predef,$num_var_predef_ripeti,$num_ripeti,$tariffa_selezionata,$num_costo_agg_sel,$anno,$pag,$lingua_mex,$LIKE,$ILIKE,$modifica_pers,$vedi_clienti,$dir_salva,$nome_file_contr,$utenti_gruppi;
global $tablecontratti,$tableclienti,$tablerclientiprenota,$tablepersonalizza,$tableutenti,$tablerelutenti,$tablenazioni,$tableregioni,$tablecitta,$tabledocumentiid,$tableparentele,$tableappartamenti,$tableperiodi;
global $data_inizio_selezione,$data_fine_selezione,$var_riserv,$var_predef_data,$messaggio_di_errore,$testo_email_richiesta,$num_commenti_pers,$numero_ins_comm_pers,$num_campi_pers_cliente,$numero_inserimento_pers;

$n_utenti = esegui_query("select idutenti,nome_utente from $tableutenti ");
for ($num1 = 0 ; $num1 < numlin_query($n_utenti) ; $num1++) $n_utente_contr[risul_query($n_utenti,$num1,'idutenti')] = risul_query($n_utenti,$num1,'nome_utente');

if (!$set_glob and $n_r > 100) $unset_glob = 1;
$data_inizio_selezione_orig = $data_inizio_selezione;
$data_fine_selezione_orig = $data_fine_selezione;
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {
for ($num1 = 0 ; $num1 < $num_var_predef_ripeti ; $num1++) {
if (strcmp($GLOBALS[$var_predef[$num1]."_".$n_r],"")) ${$var_predef[$num1]."_".$n_r} = $GLOBALS[$var_predef[$num1]."_".$n_r];
if ($unset_glob) unset($GLOBALS[$var_predef[$num1]."_".$n_r]);
} # fine for $num1
if (!$data_primo_arrivo or ${"data_inizio_".$n_r} < $data_primo_arrivo) $data_primo_arrivo = ${"data_inizio_".$n_r};
if (!$data_ultima_partenza or ${"data_fine_".$n_r} > $data_ultima_partenza) $data_ultima_partenza = ${"data_fine_".$n_r};
${"utente_inserimento_prenotazione"."_".$n_r} = $n_utente_contr[${"utente_inserimento_prenotazione"."_".$n_r}];
} # fine for $n_r
if (!$data_inizio_selezione_orig) $data_inizio_selezione = $data_primo_arrivo;
if (!$data_fine_selezione_orig) $data_fine_selezione = $data_ultima_partenza;
if ($unset_glob) for ($num1 = $num_var_predef_ripeti ; $num1 < $num_var_predef ; $num1++) unset($var_predef[$num1]);
$messaggio_di_errore = "";


$stile_soldi = stile_soldi($id_utente);
$stile_data = stile_data($id_utente);

$dati_app = esegui_query("select * from $tableappartamenti order by idappartamenti ");
$num_unita = numlin_query($dati_app);
for ($num1 = 1 ; $num1 <= $num_unita ; $num1++) {
$dati_app_contr[$num1]['nome'] = risul_query($dati_app,($num1 - 1),'idappartamenti');
$dati_app_contr[$num1]['casa'] = risul_query($dati_app,($num1 - 1),'numcasa');
$dati_app_contr[$num1]['piano'] = risul_query($dati_app,($num1 - 1),'numpiano');
$dati_app_contr[$num1]['capacita'] = risul_query($dati_app,($num1 - 1),'maxoccupanti');
$dati_app_contr[$num1]['priorita'] = risul_query($dati_app,($num1 - 1),'priorita');
} # fine for $num1
unset($dati_app);

$data_inizio_selezione_orig = $data_inizio_selezione;
$data_fine_selezione_orig = $data_fine_selezione;
if ($data_inizio_selezione) $data_inizio_selezione_f = formatta_data_contr($data_inizio_selezione,$stile_data);
if ($data_fine_selezione) $data_fine_selezione_f = formatta_data_contr($data_fine_selezione,$stile_data);
$ritorno_a_capo = "\r";
$avanzamento_riga = "\n";
$valore_nullo = "";
$nome_valuta = nome_valuta($id_utente);
$oggi = date("Y-m-d",(time() + (C_DIFF_ORE * 3600)));
$oggi_orig = $oggi;
$oggi_f = formatta_data_contr($oggi,$stile_data);
if ($lingua_mex) $fr_via = mex("via",$pag);
if ($testo_email_richiesta) {
if (get_magic_quotes_gpc()) $testo_email_richiesta = stripslashes($testo_email_richiesta);
$testo_quotato_email_richiesta_orig = "> ".str_replace("\n","\n> ",$testo_email_richiesta);
if ($lingua_mex) $testo_quotato_email_richiesta_orig = "<email_richiesta> ".mex("ha scritto",$pag).":\n".$testo_quotato_email_richiesta_orig;
} # fine if ($testo_email_richiesta)
else $testo_quotato_email_richiesta_orig = "";
$arrotond_tasse = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'arrotond_tasse' and idutente = '$id_utente'");
$arrotond_tasse = risul_query($arrotond_tasse,0,'valpersonalizza');
$nome_struttura = esegui_query("select * from $tablepersonalizza where idpersonalizza = 'dati_struttura' and idutente = '$id_utente'");
$nome_struttura = explode("#@&",risul_query($nome_struttura,0,'valpersonalizza'));
$tipo_struttura = $nome_struttura[1];
$email_struttura = $nome_struttura[2];
$ragione_sociale_struttura = $nome_struttura[3];
$sito_web_struttura = $nome_struttura[4];
$nome_contatto_struttura = $nome_struttura[5];
$nazione_struttura = $nome_struttura[6];
$comune_struttura = $nome_struttura[7];
$indirizzo_struttura = $nome_struttura[8];
$CAP_struttura = $nome_struttura[9];
$telefono_struttura = $nome_struttura[10];
$fax_struttura = $nome_struttura[11];
$codice_fiscale_struttura = $nome_struttura[12];
$partita_iva_struttura = $nome_struttura[13];
$numero_stelle_struttura = $nome_struttura[14];
$logo_struttura = $nome_struttura[15];
$nome_struttura = $nome_struttura[0];

$url_base_pagine_web = "";
if (defined('C_PAGINA_WEB') and C_PAGINA_WEB == '1') {
global $PHP_SELF,$SERVER_NAME,$HTTP_SERVER_VARS,$HTTPS,$SERVER_PORT;
if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or @$HTTP_SERVER_VARS['SERVER_NAME']) {
if (@$PHP_SELF or @$_SERVER['PHP_SELF']) {
if ($_SERVER['SERVER_NAME']) $SERVER_NAME = $_SERVER['SERVER_NAME'];
elseif ($HTTP_SERVER_VARS['SERVER_NAME']) $SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
if ($_SERVER['PHP_SELF']) $PHP_SELF = $_SERVER['PHP_SELF'];
if ($HTTPS == "on" or $_SERVER['HTTPS'] == "on" or $SERVER_PORT == "443" or $_SERVER['SERVER_PORT'] == "443") $url_base_pagine_web = "https://".$SERVER_NAME;
else $url_base_pagine_web = "http://".$SERVER_NAME;
if (substr($PHP_SELF,0,1) != "/") $url_base_pagine_web .= "/".$PHP_SELF;
else $url_base_pagine_web .= $PHP_SELF;
$val_then = explode("/",$url_base_pagine_web);
$url_base_pagine_web = substr($url_base_pagine_web,0,(strlen($val_then[(count($val_then) - 1)]) * -1));
$url_base_pagine_web = str_replace("/./","/",$url_base_pagine_web);
while (str_replace("/../","",$url_base_pagine_web) != $url_base_pagine_web) {
$val_then = explode("/../",$url_base_pagine_web);
$val_if = explode("/",$val_then[0]);
$txt_sost1 = substr($val_then[0],0,(strlen($val_if[(count($val_if) - 1)]) * -1));
$url_base_pagine_web = $txt_sost1.substr($url_base_pagine_web,(strlen($val_then[0]) + 4));
} # fine while (str_replace("/../","",$url_base_pagine_web) != $url_base_pagine_web)
} # fine if (@$PHP_SELF or @$_SERVER['PHP_SELF'])
} # fine if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or...
} # fine if (defined('C_PAGINA_WEB') and C_PAGINA_WEB == '1')
if (!$url_base_pagine_web) {
if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI")) {
$percorso_cartella_modello = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'percorso_cartella_modello' and idutente = '1'");
$percorso_cartella_modello = explode(",",risul_query($percorso_cartella_modello,0,'valpersonalizza'));
$percorso_cartella_modello = $percorso_cartella_modello[0];
$altri_domini = @file(C_FILE_DOMINIO);
if ($altri_domini) {
if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "") $percorso_cartella_modello = substr($percorso_cartella_modello,strlen(C_CARTELLA_CREA_MODELLI));
if (substr($percorso_cartella_modello,0,1) == "/") $percorso_cartella_modello = substr($percorso_cartella_modello,1);
if (strcmp($percorso_cartella_modello,"") and substr($percorso_cartella_modello,-1) != "/") $percorso_cartella_modello .= "/";
if (substr($percorso_cartella_modello,0,2) == "./") $percorso_cartella_modello = substr($percorso_cartella_modello,2);
$url_base_pagine_web = "https://".trim($altri_domini[0])."/$percorso_cartella_modello";
} # fine if ($altri_domini)
unset($altri_domini);
} # fine if (defined("C_FILE_DOMINIO") and C_FILE_DOMINIO != "" and (!defined('C_NASCONDI_MARCA') or C_NASCONDI_MARCA != "SI"))
} # fine if (!$url_base_pagine_web)
if (!$url_base_pagine_web and (!defined('C_PAGINA_WEB') or C_PAGINA_WEB != '1')) {
if (!function_exists('trova_url_pagina')) include("./includes/templates/funzioni_modelli.php");
$url_base_pagine_web = trova_url_pagina("",$percorso_cartella_modello,"");
} # fine if (!$url_base_pagine_web and (!defined('C_PAGINA_WEB') or C_PAGINA_WEB != '1'))
unset($percorso_cartella_modello);

$num_contr_vc = $numero_contratto;
$contr_imp_vc = esegui_query("select testo from $tablecontratti where numero = '$numero_contratto' and tipo = 'impor_vc' ");
if (numlin_query($contr_imp_vc)) $num_contr_vc = risul_query($contr_imp_vc,0,'testo');
$variabili = esegui_query("select * from $tablecontratti where tipo = 'var' or tipo = 'var$num_contr_vc' order by tipo, numero");
$num_variabili = numlin_query($variabili);
$arrays = esegui_query("select * from $tablecontratti where tipo = 'vett' or tipo = 'vett$num_contr_vc' order by tipo, numero");
$num_arrays = numlin_query($arrays);
$condizioni_ini_d = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'ind%' order by tipo, numero");
$num_condizioni_ini_d = numlin_query($condizioni_ini_d);
$condizioni_ini_r = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'inr%' order by tipo, numero");
$num_condizioni_ini_r = numlin_query($condizioni_ini_r);
$condizioni = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rpt%' order by tipo, numero");
$num_condizioni = numlin_query($condizioni);
$condizioni_rip_o = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'ros%' order by tipo, numero");
$num_condizioni_rip_o = numlin_query($condizioni_rip_o);
$condizioni_rip_c = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rca%' order by tipo, numero");
$num_condizioni_rip_c = numlin_query($condizioni_rip_c);
$condizioni_rip_p = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rpa%' order by tipo, numero");
$num_condizioni_rip_p = numlin_query($condizioni_rip_p);
$condizioni_rip_u = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'run%' order by tipo, numero");
$num_condizioni_rip_u = numlin_query($condizioni_rip_u);
$condizioni_rip_a = esegui_query("select * from $tablecontratti where (tipo = 'cond' or tipo = 'cond$num_contr_vc') and testo $LIKE 'rar%' order by tipo, numero");
$num_condizioni_rip_a = numlin_query($condizioni_rip_a);
$dir_salva = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'dir'");
if (numlin_query($dir_salva) == 1) {
$dir_salva = formatta_dir_salva_doc(risul_query($dir_salva,0,'testo'));
} # fine if (numlin_query($dir_salva) == 1)
else $dir_salva = "";
$dati_contratto = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo $LIKE 'contr%'");
$tipo_contratto = risul_query($dati_contratto,0,'tipo');
$contratto_orig = risul_query($dati_contratto,0,'testo');

$contr_multilingua = 0;
unset($contratti_orig_mln);
unset($lingue_contr);
$num_contr_mln = 1;
if (substr($contratto_orig,0,7) == "#!mln!#") {
$contr_multilingua = 1;
$contratti_orig_mln['predef'] = substr($contratto_orig,7);
$dati_contratti_mln = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo $LIKE 'mln_%'");
$num_contr_mln = numlin_query($dati_contratti_mln);
for ($num1 = 0 ; $num1 < $num_contr_mln ; $num1++) {
$lingua_contr = substr(risul_query($dati_contratti_mln,$num1,'tipo'),4);
$lingue_contr[$num1] = $lingua_contr;
$contratti_orig_mln[$lingua_contr] = risul_query($dati_contratti_mln,$num1,'testo');
} # fine for $num1
$contratto_orig = $contratti_orig_mln[$contratti_orig_mln['predef']];
} # fine if (substr($contratto_orig,0,7) == "#!mln!#")

if ($tipo_contratto == "contrhtm") {
$tag_b = "<b>";
$tag_no_b = "</b>";
$tag_spazio = "&nbsp;";
$tag_acapo = "<br>";
} # fine if ($tipo_contratto == "contrhtm")
if ($tipo_contratto == "contreml") {
$tag_spazio = " ";
$tag_acapo = "\n";
$oggetto_email = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'oggetto'");
$oggetto_email = risul_query($oggetto_email,0,'testo');
$allegato_email = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'allegato'");
$allegato_email = risul_query($allegato_email,0,'testo');
if ($allegato_email) {
$allegato_email = esegui_query("select * from $tablecontratti where numero = '$allegato_email' and tipo = 'file_all'");
$allegato_email = risul_query($allegato_email,0,'testo');
$allegato_email = explode(",",$allegato_email);
$allegato_email = $allegato_email[0];
} # fine if ($allegato_email)
$nome_mittente_email = $nome_contatto_struttura;
$mittente_email = $email_struttura;
if ($nome_mittente_email) $mittente_email = "$nome_mittente_email &lt;$mittente_email&gt;";
} # fine if ($tipo_contratto == "contreml")
if ($tipo_contratto == "contrrtf") {
$tag_spazio = " ";
$tag_acapo = "}
\\par \\pard\\plain \\ltrpar\\s1\\cf0{\\*\\hyphen2\\hyphlead2\\hyphtrail2\\hyphmax0}\\rtlch\\af3\\afs24\\lang255\\ltrch\\dbch\\af3\\langfe255\\hich\\f0\\fs24\\lang1040\\loch\\f0\\fs24\\lang1040 {\\rtlch \\ltrch\\loch\\f0\\fs24\\lang1040\\i0\\b0 ";
} # fine if ($tipo_contratto == "contrrtf")
if ($tipo_contratto == "contrtxt") {
$tag_spazio = " ";
$tag_acapo = "\n";
} # fine if ($tipo_contratto == "contrtxt")


if (str_replace("[r]","",$contratto_orig) == $contratto_orig or $contr_multilingua) $ripeti_tutto = 1;
else $ripeti_tutto = 0;


$nome_file_contr = array();
$filecontr = array();
$num_prog_contr = "";
$num_prog_contr_max = "";
$incr_np = 0;
if ($dir_salva) {
global $nomi_contratti,$num_contr_esist,$nome_file_contr_esist,$cont_salva,$lista_var_form,$num_file_salva,$sovrascrivi,$priv_cancella_contratti;
$nome_contratto = $nomi_contratti['salv'][$numero_contratto];
if (get_magic_quotes_gpc()) $sovrascrivi = stripslashes($sovrascrivi);
if ($priv_cancella_contratti == "n") $sovrascrivi = "";
if ($ripeti_tutto) $num_file_salva = $num_ripeti;
else $num_file_salva = 1;
$anno_corr = date("Y");
if ($anno_corr != ($anno + 1) or @is_file(C_DATI_PATH."/selectperiodi$anno_corr.1.php")) $anno_corr = $anno;
$incrementa_num_prog = esegui_query("select * from $tablecontratti where numero = '$numero_contratto' and tipo = 'incr_np'");
if (numlin_query($incrementa_num_prog)) $incr_np = risul_query($incrementa_num_prog,0,'testo');

$lista_var_form = "";
if (@is_array($_POST)) reset($_POST);
for($num1 = 0 ; $num1 < count($_POST); $num1++) {
$lista_var_form .= "<input type=\"hidden\" name=\"".htmlspecialchars(key($_POST))."\" value=\"".htmlspecialchars(strip_magic_slashs($_POST[key($_POST)]))."\">";
next($_POST);
} # fine for $num1
if (@is_array($_GET)) reset($_GET);
for($num1 = 0 ; $num1 < count($_GET); $num1++) {
$lista_var_form .= "<input type=\"hidden\" name=\"".htmlspecialchars(key($_GET))."\" value=\"".htmlspecialchars(strip_magic_slashs($_GET[key($_GET)]))."\">";
next($_GET);
} # fine for $num1

$filelock = fopen(C_DATI_PATH."/crea_contr.lock","w+");
flock($filelock,2);
if ($tipo_contratto == "contrrtf") $suff_file = "rtf";
if ($tipo_contratto == "contrhtm") $suff_file = "html";
if ($tipo_contratto == "contrtxt") $suff_file = "txt";
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) {
global ${"id_anni_prec_".$num1};
$id_pren[$num1] =  ${"numero_prenotazione_".$num1};
} # fine for $num1
$num_contr_esist = 0;
$nome_file_contr_esist = array();
$n_prog_contr = 0;
$num_sovrascrivi = 0;
$num_sovrascrivi_max = 0;
$contr_dir = opendir($dir_salva."/");
while ($contr_corr = readdir($contr_dir)) {
if ($contr_corr != "." and $contr_corr != ".." and is_file($dir_salva."/".$contr_corr)) {
if (substr($contr_corr,0,strlen($nome_contratto)) == $nome_contratto) {
$contr_corr_orig = $contr_corr;
if (substr($contr_corr,-3) == ".gz") $contr_corr = substr($contr_corr,0,-3);
$suff_file_corr = "";
if (substr($contr_corr,-4) == ".rtf") $suff_file_corr = "rtf";
if (substr($contr_corr,-5) == ".html") $suff_file_corr = "html";
if (substr($contr_corr,-4) == ".txt") $suff_file_corr = "txt";
if ($suff_file_corr) {
$resto_nome_contr = substr($contr_corr,strlen($nome_contratto));
if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+)?)*\.$suff_file_corr/","",$resto_nome_contr) == "") {
$anno_contr = substr($resto_nome_contr,1,4);
$n_contr_corr = explode("_",substr($resto_nome_contr,0,(-1 * (strlen($suff_file_corr) + 1))));
$n_contr_corr = $n_contr_corr[2];
$resto_nome_contr = substr($resto_nome_contr,(7 + strlen($n_contr_corr)));
if (str_replace("-","",$n_contr_corr) != $n_contr_corr) {
$n_contr_corr = explode("-",$n_contr_corr);
$n_contr_ini_corr = $n_contr_corr[0];
$n_contr_corr = $n_contr_corr[1];
} # fine if (str_replace("-","",$n_contr_corr) != $n_contr_corr)
else $n_contr_ini_corr = 0;
if ($n_contr_corr > $n_prog_contr and $anno_contr == $anno_corr) $n_prog_contr = $n_contr_corr;

if (!$cont_salva and $lista_var_form) {
$num_pren_esist = substr($resto_nome_contr,0,(-1 * (strlen($suff_file_corr) + 1)));
if ($num_pren_esist) {
$num_pren_esist = explode("_",$num_pren_esist);
for ($num1 = 0 ; $num1 < count($num_pren_esist) ; $num1++) {
$num_pren_esist2 = explode("-",$num_pren_esist[$num1]);
$fine_for = $num_pren_esist2[(count($num_pren_esist2) - 1)];
for ($num2 = $num_pren_esist2[0] ; $num2 <= $fine_for ; $num2++) {
for ($num3 = 1 ; $num3 <= $num_ripeti ; $num3++) {
if (($num2 == $id_pren[$num3] and ($anno_contr == $anno or $anno_contr == $anno_corr)) or (str_replace(";$anno_contr,$num2;","",${"id_anni_prec_".$num3}) != ${"id_anni_prec_".$num3} and $anno_contr != $anno and $anno_contr != $anno_corr)) {
$num_contr_esist++;
$nome_file_contr_esist[$num_contr_esist] = $contr_corr_orig;
if ($contr_corr_orig == $sovrascrivi and $num_file_salva == 1) {
$num_sovrascrivi_max = $n_contr_corr;
if ($n_contr_ini_corr) $num_sovrascrivi = $n_contr_ini_corr;
else $num_sovrascrivi = $n_contr_corr;
$anno_corr = $anno_contr;
unlink($dir_salva."/".$contr_corr_orig);
if ($id_pren[$num3] != $num2) ${"numero_prenotazione_".$num3} = $num2;
} # fine if ($contr_corr_orig == $sovrascrivi and...
break 3;
} # fine if (($num2 == $id_prenota[$num3] and...
} # fine for $num3
} # fine for $num2
} # fine for $num1
} # fine if ($num_pren_esist)
} # fine if (!$cont_salva and $lista_var_form)

} # fine if (preg_replace("/_[0-9]{4,4}_[0-9]{5,5}\.$suff_file_corr/","",$resto_nome_contr) == "")
} # fine if ($suff_file_corr)
} # fine if (substr($contr_corr,0,strlen($nome_contratto)) == $nome_contratto)
} # fine if ($contr_corr != "." and $contr_corr != ".." and...
} # fine while ($contr_corr = readdir($contr_dir))
closedir($contr_dir);

if ($num_sovrascrivi) {
$num_contr_esist = 0;
if ($incr_np and $num_sovrascrivi_max < $n_prog_contr) $num_prog_contr_max = $num_sovrascrivi_max;
$n_prog_contr = ($num_sovrascrivi - 1);
} # fine if ($num_sovrascrivi)

if (!$num_sovrascrivi or ($incr_np and !$num_prog_contr_max)) {
# se il contratto condivide il numero progressivo con altri contratti
if (@is_array($nomi_contratti['num_prog'])) {
$altri_contr_np = $nomi_contratti['num_prog'][$dir_salva."/".$nome_contratto];
if (@is_array($altri_contr_np)) {
reset($altri_contr_np);
foreach ($altri_contr_np as $contr_np => $val_contr) {
$nome_contratto_np = explode("/",$contr_np);
$nome_contratto_np = $nome_contratto_np[(count($nome_contratto_np) - 1)];
$dir_salva_np = substr($contr_np,0,(-1 * (strlen($nome_contratto_np) + 1)));
$contr_dir = opendir($dir_salva_np."/");
while ($contr_corr = readdir($contr_dir)) {
if ($contr_corr != "." and $contr_corr != ".." and is_file($dir_salva_np."/".$contr_corr)) {
if (substr($contr_corr,0,strlen($nome_contratto_np)) == $nome_contratto_np) {
if (substr($contr_corr,-3) == ".gz") $contr_corr = substr($contr_corr,0,-3);
$suff_file_corr = "";
if (substr($contr_corr,-4) == ".rtf") $suff_file_corr = "rtf";
if (substr($contr_corr,-5) == ".html") $suff_file_corr = "html";
if (substr($contr_corr,-4) == ".txt") $suff_file_corr = "txt";
if ($suff_file_corr) {
$resto_nome_contr = substr($contr_corr,strlen($nome_contratto_np));
if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+)?)*\.$suff_file_corr/","",$resto_nome_contr) == "") {
$anno_contr = substr($resto_nome_contr,1,4);
$n_contr_corr = explode("_",$resto_nome_contr);
$n_contr_corr = $n_contr_corr[2];
if (str_replace("-","",$n_contr_corr) != $n_contr_corr) {
$n_contr_corr = explode("-",$n_contr_corr);
$n_contr_corr = $n_contr_corr[1];
} # fine if (str_replace("-","",$n_contr_corr) != $n_contr_corr)
if ($n_contr_corr > $n_prog_contr and $anno_contr == $anno_corr) {
if (!$num_sovrascrivi) $n_prog_contr = $n_contr_corr;
else {
$num_prog_contr_max = $num_sovrascrivi_max;
break;
} # fine else if (!$num_sovrascrivi)
} # fine if ($n_contr_corr > $n_prog_contr and $anno_contr == $anno_corr)
} # fine if (preg_replace("/_[0-9]{4,4}_[0-9]{5,8}(-[0-9]{5,8})?(_[0-9]+(-[0-9]+)?)*\.$suff_file_corr/","",$resto_nome_contr) == "")
} # fine if ($suff_file_corr)
} # fine if (substr($contr_corr,0,strlen($nome_contratto_np)) == $nome_contratto_np)
} # fine if ($contr_corr != "." and $contr_corr != ".." and...
} # fine while ($contr_corr = readdir($contr_dir))
closedir($contr_dir);
} # fine foreach ($altri_contr_np as $contr_np => $val_contr)
} # fine if (@is_array($altri_contr_np))
} # fine if (@is_array($nomi_contratti['num_prog']))
} # fine if (!$num_sovrascrivi or ($incr_np and !$num_prog_contr_max))

if ($num_contr_esist) return 0;

for ($num1 = 1 ; $num1 <= $num_file_salva ; $num1++) {
$n_prog_contr++;
$num_prog_contr[$num1] = $n_prog_contr;
$nome_file_contr[$num1] = $n_prog_contr;
for ($num2 = strlen($nome_file_contr[$num1]) ; $num2 < 5 ; $num2++) $nome_file_contr[$num1] = "0".$nome_file_contr[$num1];
if ($incr_np) $nome_file_contr[$num1] .= " ";

if ($ripeti_tutto) {
if (${"numero_prenotazione_".$num1} and preg_replace("/[0-9]+/","",${"numero_prenotazione_".$num1}) == "") $nome_file_contr[$num1] .= "_".${"numero_prenotazione_".$num1};
} # fine if ($ripeti_tutto)
else {
unset($lista_num_prenota);
for ($num2 = 1 ; $num2 <= $num_ripeti ; $num2++) {
if (${"numero_prenotazione_".$num1} and preg_replace("/[0-9]+/","",${"numero_prenotazione_".$num1}) == "") $lista_num_prenota[$num2] = ${"numero_prenotazione_".$num2};
} # fine for $num2
if (@is_array($lista_num_prenota)) {
asort($lista_num_prenota);
reset($lista_num_prenota);
$ultimo_num_prenota = -2;
foreach ($lista_num_prenota as $num2 => $num_prenota) {
if ($ultimo_num_prenota != $num_prenota) {
if ($ultimo_num_prenota < 0) {
$nome_file_contr[$num1] .= "_".$num_prenota."_";
$ultimo_num_prenota = ($num_prenota - 1);
} # fine if ($ultimo_num_prenota < 0)
else {
if (($num_prenota - 1) != $ultimo_num_prenota) {
if (substr($nome_file_contr[$num1],-1) != "_") $nome_file_contr[$num1] .= "-".$ultimo_num_prenota."_";
$nome_file_contr[$num1] .= $num_prenota."_";
} # fine (($num_prenota - 1) != $ultimo_num_prenota)
elseif (substr($nome_file_contr[$num1],-1) == "_") $nome_file_contr[$num1] = substr($nome_file_contr[$num1],0,-1);
} # fine else if ($ultimo_num_prenota < 0)
$ultimo_num_prenota = $num_prenota;
} # fine if ($ultimo_num_prenota != $num_prenota)
} # fine foreach ($lista_num_prenota as $num2 => $num_prenota)
if (substr($nome_file_contr[$num1],-1) != "_") $nome_file_contr[$num1] .= "-".$ultimo_num_prenota;
else $nome_file_contr[$num1] = substr($nome_file_contr[$num1],0,-1);
} # fine (@is_array($lista_num_prenota))
} # fine else if ($ripeti_tutto)

$nome_file_contr[$num1] = $nome_contratto."_".$anno_corr."_".$nome_file_contr[$num1].".$suff_file";
if (!$incr_np) {
if ($nomi_contratti['compress'][$numero_contratto]) {
$nome_file_contr[$num1] .= ".gz";
$lock_compress[$num1] = crea_lock_file($dir_salva."/".$nome_file_contr[$num1]);
$filecontr[$num1] = gzopen($dir_salva."/".$nome_file_contr[$num1],"wb9");
} # fine if ($nomi_contratti['compress'][$numero_contratto])
else {
$filecontr[$num1] = fopen($dir_salva."/".$nome_file_contr[$num1],"w+");
flock($filecontr[$num1],2);
} # fine else if ($nomi_contratti['compress'][$numero_contratto])
} # fine if (!$incr_np)
else $filecontr['esist'] = 1;
} # fine for $num1

if (!$incr_np) {
flock($filelock,3);
fclose($filelock);
unlink(C_DATI_PATH."/crea_contr.lock");
} # fine if (!$incr_np)
} # fine if ($dir_salva)


unset($tablepersonalizza);
unset($tablecontratti);
if (!defined('C_ID_UTENTE_CONTR')) define('C_ID_UTENTE_CONTR',$id_utente);
$utente_attuale = $n_utente_contr[C_ID_UTENTE_CONTR];
unset($id_utente);


for ($num1 = 0 ; $num1 < $num_variabili ; $num1++) {
$nome_var = risul_query($variabili,$num1,'testo');
$num_var = risul_query($variabili,$num1,'numero');
if (!$var_riserv[$nome_var]) $variabile[$num_var] = $nome_var;
} # fine for $num1
$variabile['-1'] = "messaggio_di_errore";
$variabile['-2'] = "errore_ripetizione";

for ($num1 = 0 ; $num1 < $num_arrays ; $num1++) {
$nome_arr = explode(";",risul_query($arrays,$num1,'testo'));
$var_array = $nome_arr[1];
$tipo_arr = $nome_arr[2];
$nome_arr = $nome_arr[0];
if (!$var_riserv[$nome_arr]) {
$num_arr = risul_query($arrays,$num1,'numero');
$array[$num_arr] = $nome_arr;
$var_arr[$num_arr] = $var_array;
$var_arr_nome[$nome_arr] = $var_array;
$arr_var_esist[$var_array] = "SI";
} # fine if (!$var_riserv[$nome_arr])
} # fine for $num1
unset($var_riserv);

for ($num1 = 0 ; $num1 < $num_condizioni_ini_d ; $num1++) {
$condizione = risul_query($condizioni_ini_d,$num1,'testo');
$condizione_ini_d_vett[$num1] = explode("#@?",$condizione);
$azione_ini_d_vett[$num1] = explode("#%?",$condizione_ini_d_vett[$num1][2]);
if ($condizione_ini_d_vett[$num1][1]) {
$condizione_ini_d_vett[$num1] = explode("#$?",$condizione_ini_d_vett[$num1][1]);
$num_cond_ini_d_vett[$num1] = count($condizione_ini_d_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_ini_d_vett[$num1] ; $num2++) $condizione_ini_d_vett[$num1][$num2] = explode("#%?",$condizione_ini_d_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_ini_d_vett[$num1] = "";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_ini_r ; $num1++) {
$condizione = risul_query($condizioni_ini_r,$num1,'testo');
$condizione_ini_r_vett[$num1] = explode("#@?",$condizione);
$azione_ini_r_vett[$num1] = explode("#%?",$condizione_ini_r_vett[$num1][2]);
if ($condizione_ini_r_vett[$num1][1]) {
$condizione_ini_r_vett[$num1] = explode("#$?",$condizione_ini_r_vett[$num1][1]);
$num_cond_ini_r_vett[$num1] = count($condizione_ini_r_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_ini_r_vett[$num1] ; $num2++) $condizione_ini_r_vett[$num1][$num2] = explode("#%?",$condizione_ini_r_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_ini_r_vett[$num1] = "";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni ; $num1++) {
$condizione = risul_query($condizioni,$num1,'testo');
$condizione_vett[$num1] = explode("#@?",$condizione);
$azione_vett[$num1] = explode("#%?",$condizione_vett[$num1][2]);
if ($condizione_vett[$num1][1]) {
$condizione_vett[$num1] = explode("#$?",$condizione_vett[$num1][1]);
$num_cond_vett[$num1] = count($condizione_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_vett[$num1] ; $num2++) $condizione_vett[$num1][$num2] = explode("#%?",$condizione_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_vett[$num1] = "";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_o ; $num1++) {
$condizione = risul_query($condizioni_rip_o,$num1,'testo');
$condizione_rip_o_vett[$num1] = explode("#@?",$condizione);
$azione_rip_o_vett[$num1] = explode("#%?",$condizione_rip_o_vett[$num1][2]);
if ($condizione_rip_o_vett[$num1][1]) {
$condizione_rip_o_vett[$num1] = explode("#$?",$condizione_rip_o_vett[$num1][1]);
$num_cond_rip_o_vett[$num1] = count($condizione_rip_o_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_o_vett[$num1] ; $num2++) $condizione_rip_o_vett[$num1][$num2] = explode("#%?",$condizione_rip_o_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_rip_o_vett[$num1] = "";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_c ; $num1++) {
$condizione = risul_query($condizioni_rip_c,$num1,'testo');
$condizione_rip_c_vett[$num1] = explode("#@?",$condizione);
$azione_rip_c_vett[$num1] = explode("#%?",$condizione_rip_c_vett[$num1][2]);
if ($condizione_rip_c_vett[$num1][1]) {
$condizione_rip_c_vett[$num1] = explode("#$?",$condizione_rip_c_vett[$num1][1]);
$num_cond_rip_c_vett[$num1] = count($condizione_rip_c_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_c_vett[$num1] ; $num2++) $condizione_rip_c_vett[$num1][$num2] = explode("#%?",$condizione_rip_c_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_rip_c_vett[$num1] = "";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_p ; $num1++) {
$condizione = risul_query($condizioni_rip_p,$num1,'testo');
$condizione_rip_p_vett[$num1] = explode("#@?",$condizione);
$azione_rip_p_vett[$num1] = explode("#%?",$condizione_rip_p_vett[$num1][2]);
if ($condizione_rip_p_vett[$num1][1]) {
$condizione_rip_p_vett[$num1] = explode("#$?",$condizione_rip_p_vett[$num1][1]);
$num_cond_rip_p_vett[$num1] = count($condizione_rip_p_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_p_vett[$num1] ; $num2++) $condizione_rip_p_vett[$num1][$num2] = explode("#%?",$condizione_rip_p_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_rip_p_vett[$num1] = "";
} # fine for $num1

for ($num1 = 0 ; $num1 < $num_condizioni_rip_u ; $num1++) {
$condizione = risul_query($condizioni_rip_u,$num1,'testo');
$condizione_rip_u_vett[$num1] = explode("#@?",$condizione);
$azione_rip_u_vett[$num1] = explode("#%?",$condizione_rip_u_vett[$num1][2]);
if ($condizione_rip_u_vett[$num1][1]) {
$condizione_rip_u_vett[$num1] = explode("#$?",$condizione_rip_u_vett[$num1][1]);
$num_cond_rip_u_vett[$num1] = count($condizione_rip_u_vett[$num1]);
for ($num2 = 1 ; $num2 < $num_cond_rip_u_vett[$num1] ; $num2++) $condizione_rip_u_vett[$num1][$num2] = explode("#%?",$condizione_rip_u_vett[$num1][$num2]);
} # fine if ($condizione_vett[$num1][1])
else $condizione_rip_u_vett[$num1] = "";
} # fine for $num1

$num_condizioni_rip_a_vett = array();
for ($num1 = 0 ; $num1 < $num_condizioni_rip_a ; $num1++) {
$condizione = explode("#@?",risul_query($condizioni_rip_a,$num1,'testo'));
$nome_arr_rip = $array[substr($condizione[0],3)];
if (!$num_condizioni_rip_a_vett[$nome_arr_rip]) $num_condizioni_rip_a_vett[$nome_arr_rip] = 1;
else $num_condizioni_rip_a_vett[$nome_arr_rip]++;
$num2 = ($num_condizioni_rip_a_vett[$nome_arr_rip] - 1);
$condizione_rip_a_vett[$nome_arr_rip][$num2] = $condizione;
$azione_rip_a_vett[$nome_arr_rip][$num2] = explode("#%?",$condizione_rip_a_vett[$nome_arr_rip][$num2][2]);
if ($condizione_rip_a_vett[$nome_arr_rip][$num2][1]) {
$condizione_rip_a_vett[$nome_arr_rip][$num2] = explode("#$?",$condizione_rip_a_vett[$nome_arr_rip][$num2][1]);
$num_cond_rip_a_vett[$nome_arr_rip][$num2] = count($condizione_rip_a_vett[$nome_arr_rip][$num2]);
for ($num3 = 1 ; $num3 < $num_cond_rip_a_vett[$nome_arr_rip][$num2] ; $num3++) $condizione_rip_a_vett[$nome_arr_rip][$num2][$num3] = explode("#%?",$condizione_rip_a_vett[$nome_arr_rip][$num2][$num3]);
} # fine if ($condizione_rip_a_vett[$nome_arr_rip][$num2][1])
else $condizione_rip_a_vett[$nome_arr_rip][$num2] = "";
} # fine for $num1

if (empty($trad_var_vett)) {
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != ".." && $ini_lingua != $lingua_mex) {
$trad_var = array();
include("./includes/lang/$ini_lingua/visualizza_contratto_var.php");
foreach ($trad_var as $var_trad_ita => $var_trad_ext) $trad_var_vett[$var_trad_ext] = $var_trad_ita;
} # fine if ($file != "." && $file != ".." && $ini_lingua != $lingua)
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
if ($lingua_mex != "ita") {
$trad_var = array();
include("./includes/lang/".$lingua_mex."/visualizza_contratto_var.php");
foreach ($trad_var as $var_trad_ita => $var_trad_ext) $trad_var_vett[$var_trad_ext] = $var_trad_ita;
} # fine if ($lingua_mex != "ita")
unset($trad_var);
} # fine if (empty($trad_var_vett))
foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita) {
if ($var_trad_ita == "commento_personalizzato") {
for ($num1 = 0 ; $num1 < $num_commenti_pers ; $num1++) {
$var_trad_ita = $var_predef[($numero_ins_comm_pers + $num1)];
$trad_var_vett[$var_trad_ext."_".substr($var_trad_ita,24)] = $var_trad_ita;
} # fine for $num1
} # fine if ($var_trad_ita == "commento_personalizzato")
if ($var_trad_ita == "campo_personalizzato") {
for ($num1 = 0 ; $num1 < $num_campi_pers_cliente ; $num1++) {
$var_trad_ita = $var_predef[($numero_inserimento_pers + $num1)];
$trad_var_vett[$var_trad_ext."_".substr($var_trad_ita,21)] = $var_trad_ita;
} # fine for $num1
} # fine if ($var_trad_ita == "campo_personalizzato")
} # fine foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita)
for ($num_mln = 0 ; $num_mln < $num_contr_mln ; $num_mln++) {
if ($contr_multilingua) $contratto_orig = $contratti_orig_mln[$lingue_contr[$num_mln]];
reset($trad_var_vett);
foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita) {
$contratto_orig = str_replace("[".$var_trad_ext."]","[".$var_trad_ita."]",$contratto_orig);
if ($arr_var_esist[$var_trad_ita] == "SI") {
for ($num1 = 0 ; $num1 < $num_arrays ; $num1++) {
$num_arr = risul_query($arrays,$num1,'numero');
if ($var_arr[$num_arr] == $var_trad_ita) {
$contratto_orig = str_replace("[".$array[$num_arr]."(".$var_trad_ext.")]","[".$array[$num_arr]."(".$var_trad_ita.")]",$contratto_orig);
} # fine if ($var_arr[$num_arr] == $var_trad_ita)
} # fine for $num1
} # fine if ($arr_var_esist[$var_trad_ita] == "SI")
} # fine foreach ($trad_var_vett as $var_trad_ext => $var_trad_ita)
while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_orig)) {
$contr_vett = preg_split("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_orig,2);
$contr_parziale = substr($contratto_orig,strlen($contr_vett[0]));
$contratto_orig = $contr_vett[0];
$condizione = preg_split("/\" *\\]/",$contr_parziale,2);
$condizione = $condizione[0];
$contr_parziale = substr($contr_parziale,(strlen($condizione) + 1));
while (substr($contr_parziale,0,1) == " ") $contr_parziale = substr($contr_parziale,1);
$contr_parziale = substr($contr_parziale,1);
$val_if = preg_split("/ *!?= *\"/",preg_replace("/^\\[c +/","",$condizione));
$var_if = trim($val_if[0]);
if (str_replace("(","",$var_if) != $var_if) {
$parti_arr = explode("(",substr($var_if,0,-1));
$val_var_if = ${$parti_arr[0]}[${$parti_arr[1]}];
if ($trad_var_vett[$parti_arr[1]]) $var_if = $parti_arr[0]."(".$trad_var_vett[$parti_arr[1]].")";
} # fine if (str_replace("(","",$var_if) != $var_if)
elseif ($trad_var_vett[$var_if]) $var_if = $trad_var_vett[$var_if];
$val_if = $val_if[1];
if (preg_match("/!= *\"/",$condizione)) $condizione = "[[c] $var_if!=\"$val_if\"]";
else $condizione = "[[c] $var_if=\"$val_if\"]";
$contratto_orig .= $condizione;
$contratto_orig .= $contr_parziale;
} # fine while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]* *!?= *\"[^\"]*\" *\\]/",$contratto_orig))
$contratto_orig = str_replace("[[c] ","[c ",$contratto_orig);
if ($contr_multilingua) $contratti_orig_mln[$lingue_contr[$num_mln]] = $contratto_orig;
} # fine for $num_mln
unset($trad_var_vett);


if ($tipo_contratto == "contreml") {
$contratto .= "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div><br>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"numero_contratto\" value=\"$numero_contratto\">
<input type=\"hidden\" name=\"origine\" value=\"".htmlspecialchars($origine)."\">
<input type=\"hidden\" name=\"origine_vecchia\" value=\"".htmlspecialchars($origine_vecchia)."\">
<input type=\"hidden\" name=\"manda_mail\" value=\"SI\">";
if ($ripeti_tutto) $contratto .= "<input type=\"hidden\" name=\"numero_email\" value=\"$num_ripeti\">";
else {
$email_gia_inviata = 0;
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {
if (${"email_".$n_r}) {
$cliente = esegui_query("select idclienti from $tableclienti where email = '".aggslashdb(${"email_".$n_r})."' and cognome = '".aggslashdb(${"cognome_".$n_r})."' and doc_inviati $ILIKE '%#@?".aggslashdb($oggetto_email)."#@?%' ");
if (numlin_query($cliente) >= 1) {
$email_gia_inviata = 1;
$contratto .= "".mex("<span class=\"colblu\">Attenzione</span>: una email con lo stesso oggetto è già stata inviata al cliente",$pag)." ".${"cognome_".$n_r}."<br>";
} # fine if (numlin_query($cliente) >= 1)
} # fine if (${"email_".$n_r})
} # fine for $n_r
if ($email_gia_inviata) $contratto .= "<br>";
$contratto .= "<input type=\"hidden\" name=\"numero_email\" value=\"1\">
<table><tr><td align=\"right\">".mex("Da",$pag).":</td><td>";
if ($modifica_pers != "NO") $contratto .= "<input type=\"text\" name=\"mittente_email1\" size=\"60\" value=\"$mittente_email\">";
else $contratto .= "<b>$mittente_email</b>";
$contratto .= "</td></tr><tr><td align=\"right\">
".mex("A",$pag).":</td><td>
<input type=\"text\" name=\"destinatario_email1\" size=\"60\" value=\"";
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) if (${"email_".$n_r}) $contratto .= ${"email_".$n_r}.",";
if (substr($contratto,-1) == ",") $contratto = substr($contratto,0,-1);
$contratto .= "\"></td></tr><tr><td align=\"right\">
".mex("Oggetto",$pag).":</td><td>
<input type=\"text\" name=\"oggetto_email1\" size=\"60\" value=\"$oggetto_email\"></td></tr>";
if ($allegato_email) {
$contratto .= "<tr><td></td><td><label><input type=\"checkbox\" name=\"allega1\" value=\"SI\" checked>
".mex("Allega",$pag)." <b>$allegato_email</b></label></td></tr>";
} # fine if ($allegato_email)
$contratto .= "<tr><td style=\"height: 3px;\"></td></tr></table>
&nbsp;&nbsp;<textarea name=\"testo_email1\" rows=32 cols=90>";
} # fine else if ($ripeti_tutto)
} # fine if ($tipo_contratto == "contreml")
unset($id_sessione);
unset($origine);


# Condizioni applicate all'inizio del documento
$break_cont = 0;
for ($num1 = 0 ; $num1 < $num_condizioni_ini_d ; $num1++) {
$condizione = $condizione_ini_d_vett[$num1];
$num_se = $num_cond_ini_d_vett[$num1];
$azione = $azione_ini_d_vett[$num1];
$cond_verificata = 1;

if ($break_cont and $azione[0] != "cont") {
$condizione = "";
$cond_verificata = 0;
} # fine if ($break_cont and $azione[0] != "cont")

if ($condizione) {
if ($condizione[0] == "or") $cond_verificata = 0;
for ($num2 = 1 ; $num2 < $num_se ; $num2++) {
$se_cond_corr = $condizione[$num2];
$var_if = $se_cond_corr[0];
if (substr($var_if,-1) != ")") $var_if = $$var_if;
else {
$var_if = explode("(",substr($var_if,0,-1));
$var_if = ${$var_if[0]}[${$var_if[1]}];
} # fine else if (substr($var_if,-1) != ")")
$val_if = $se_cond_corr[3];
if ($se_cond_corr[2] == "var") {
if (substr($val_if,-1) != ")") $val_if = $$val_if;
else {
$val_if = explode("(",substr($val_if,0,-1));
$val_if = ${$val_if[0]}[${$val_if[1]}];
} # fine else if (substr($val_if,-1) != ")")
} # fine if ($se_cond_corr[2] == "var")
$cond_verificata = 0;
if (($se_cond_corr[1] == "=" and $var_if == $val_if) or ($se_cond_corr[1] == "!=" and $var_if != $val_if) or ($se_cond_corr[1] == ">" and $var_if > $val_if) or ($se_cond_corr[1] == "<" and $var_if < $val_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) != strtolower($var_if)) or ($se_cond_corr[1] == "{A}" and str_replace($val_if,"",$var_if) != $var_if)) $cond_verificata = 1;
if ($condizione[0] == "or" and $cond_verificata) break;
if ($condizione[0] == "and" and !$cond_verificata) break;
} # fine for $num2
} # fine if ($condizione)

if ($cond_verificata) {

if ($azione[0] == "set") {
$val_then = $azione[4];
if ($azione[3] == "var") {
if (substr($val_then,-1) != ")") {
if ($var_predef_data[$val_then] and $val_then != "data_inizio_selezione" and $val_then != "data_fine_selezione" and $val_then != "oggi") $val_then = formatta_data_contr($$val_then,$stile_data);
else $val_then = $$val_then;
} # fine if (substr($val_then,-1) != ")")
else {
$val_then = explode("(",substr($val_then,0,-1));
$val_then = ${$val_then[0]}[${$val_then[1]}];
} # fine else if (substr($val_then,-1) != ")")
if ($azione[9] == "low") $val_then = strtolower($val_then);
if ($azione[9] == "upp") $val_then = strtoupper($val_then);
if ($azione[9] == "url" and function_exists('urlencode')) $val_then = urlencode($val_then);
if ($azione[9] == "asc") $val_then = conv_ascii($val_then);
if ($azione[9] == "eas") $val_then = conv_ascii($val_then,"e");
if ($azione[9] == "md5") $val_then = md5($val_then);
} # fine if ($azione[3] == "var")
if (strcmp($azione[6],"")) {
$txt_sost1 = $azione[6];
if ($azione[5] == "var") {
if (substr($txt_sost1,-1) != ")") $txt_sost1 = $$txt_sost1;
else {
$txt_sost1 = explode("(",substr($txt_sost1,0,-1));
$txt_sost1 = ${$txt_sost1[0]}[${$txt_sost1[1]}];
} # fine else if (substr($txt_sost1,-1) != ")")
} # fine if ($azione[5] == "var")
$txt_sost2 = $azione[8];
if ($azione[7] == "var") {
if (substr($txt_sost2,-1) != ")") $txt_sost2 = $$txt_sost2;
else {
$txt_sost2 = explode("(",substr($txt_sost2,0,-1));
$txt_sost2 = ${$txt_sost2[0]}[${$txt_sost2[1]}];
} # fine else if (substr($txt_sost2,-1) != ")")
} # fine if ($azione[7] == "var")
$val_then = str_replace($txt_sost1,$txt_sost2,$val_then);
} # fine if (strcmp($azione[6],""))
if ($azione[2] == ".=") {
if (substr($azione[1],0,1) != "a") $var_then_orig = ${$variabile[$azione[1]]};
else $var_then_orig = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
} # fine if ($azione[2] == ".=")
else $var_then_orig = "";
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = $var_then_orig.$val_then;
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_then_orig.$val_then;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
if (substr($azione[1],0,1) != "a") $var_da_assegnare = ${$variabile[$azione[1]]};
else $var_da_assegnare = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
if (strcmp($azione[3],"")) {
while (num_caratteri_testo($var_da_assegnare) < $azione[2]) {
if ($azione[4] == "ini") $var_da_assegnare = $azione[3].$var_da_assegnare;
if ($azione[4] == "fin") $var_da_assegnare .= $azione[3];
} # fine while (num_caratteri_testo($var_da_assegnare) < $azione[2])
} # fine if (strcmp($azione[3],""))
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = tronca_testo($var_da_assegnare,0,$azione[2]);
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = tronca_testo($var_da_assegnare,0,$azione[2]);
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$cont_oper = "SI";
$var_con_punti = "NO";
$var_da_oper = $azione[2];
if (substr($var_da_oper,-1) != ")") {
if (substr($var_da_oper,-2) != "_p" or !isset(${substr($var_da_oper,0,-2)})) $var_da_oper = ${$var_da_oper};
else $var_da_oper = ${substr($var_da_oper,0,-2)};
} # fine if (substr($var_da_oper,-1) != ")")
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
if (substr($var_da_oper[0],-2) != "_p" or !isset(${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}])) $var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
else $var_da_oper = ${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = formatta_soldi($var_da_oper);
if (controlla_soldi($var_da_oper) == "NO") $cont_oper = "NO";
$var_da_oper2 = $azione[5];
if ($azione[4] == "var") {
if (substr($var_da_oper2,-1) != ")") {
if (substr($var_da_oper2,-2) != "_p" or !isset(${substr($var_da_oper2,0,-2)})) $var_da_oper2 = ${$var_da_oper2};
else $var_da_oper2 = ${substr($var_da_oper2,0,-2)};
} # fine if (substr($var_da_oper2,-1) != ")")
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
if (substr($var_da_oper2[0],-2) != "_p" or !isset(${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}])) $var_da_oper2 = ${$var_da_oper2[0]}[${$var_da_oper2[1]}];
else $var_da_oper2 = ${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
} # fine if ($azione[4] == "var")
$var_da_oper2 = formatta_soldi($var_da_oper2);
if (controlla_soldi($var_da_oper2) == "NO") $cont_oper = "NO";
if ($cont_oper != "NO") {
if ($azione[3] == "+") $var_da_assegnare = (double) $var_da_oper + (double) $var_da_oper2;
if ($azione[3] == "-") $var_da_assegnare = (double) $var_da_oper - (double) $var_da_oper2;
if ($azione[3] == "*") $var_da_assegnare = (double) $var_da_oper * (double) $var_da_oper2;
if ($azione[3] == "/") $var_da_assegnare = @((double) $var_da_oper / (double) $var_da_oper2);
if ($azione[6]) {
$var_da_assegnare = $var_da_assegnare / (double) $azione[6];
$var_da_assegnare = round($var_da_assegnare);
$var_da_assegnare = $var_da_assegnare * (double) $azione[6];
} # fine if ($azione[6])
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper != "NO")
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$cont_oper = 1;
$var_da_oper = $azione[2];
if (substr($var_da_oper,-1) != ")") $var_da_oper = ${$var_da_oper};
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[3] == "gi") $var_da_assegnare = "d";
if ($azione[3] == "me") $var_da_assegnare = "m";
if ($azione[3] == "an") $var_da_assegnare = "Y";
if ($azione[3] == "gs") $var_da_assegnare = "w";
if ($azione[3] == "is") $var_da_assegnare = "Y-m-d";
if ($azione[3] == "da") {
if ($stile_data == "usa") $var_da_assegnare = "m-d-Y";
else $var_da_assegnare = "d-m-Y";
} # fine if ($azione[3] == "da")
$txt_sost1 = 0;
$num2 = 0;
$num3 = 0;
if ($azione[5] == "g") $txt_sost1 = $azione[4];
if ($azione[5] == "m") $num2 = $azione[4];
if ($azione[5] == "a") $num3 = $azione[4];
$var_da_assegnare = date($var_da_assegnare,mktime(0,0,0,(substr($var_da_oper,5,2) + $num2),(substr($var_da_oper,8,2) + $txt_sost1),(substr($var_da_oper,0,4) + $num3)));
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = $var_da_assegnare;
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
if ($azione[3] != "is") $array_date_contr[$array[substr($azione[1],1)]] = "";
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$cont_oper = 1;
$var_da_oper = $azione[3];
if (substr($var_da_oper,-1) != ")") $var_da_oper = ${$var_da_oper};
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
$var_da_oper2 = $azione[4];
if (substr($var_da_oper2,-1) != ")") $var_da_oper2 = ${$var_da_oper2};
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
$var_da_oper2 = ${$var_da_oper2[0]}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
$var_da_oper2 = substr($var_da_oper2,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2)) {
if ($stile_data == "usa") $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,0,2)."-".substr($var_da_oper2,3,2);
else $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,3,2)."-".substr($var_da_oper2,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper2)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[2] == "g") {
$var_da_assegnare = mktime(2,0,0,substr($var_da_oper2,5,2),substr($var_da_oper2,8,2),substr($var_da_oper2,0,4)) - mktime(0,0,0,substr($var_da_oper,5,2),substr($var_da_oper,8,2),substr($var_da_oper,0,4));
$var_da_assegnare = floor((double) $var_da_assegnare / 86400);
} # fine if ($azione[2] == "g")
else {
$txt_sost1 = (substr($var_da_oper2,5,2) - substr($var_da_oper,5,2));
$txt_sost2 = (substr($var_da_oper2,0,4) - substr($var_da_oper,0,4));
if (($txt_sost1 > 0 or $txt_sost2 > 0) and substr($var_da_oper2,8,2) < substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 - 1;
if (($txt_sost1 < 0 or $txt_sost2 < 0) and substr($var_da_oper2,8,2) > substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 + 1;
if ($azione[2] == "m") $var_da_assegnare = ($txt_sost2 * 12) + $txt_sost1;
if ($azione[2] == "a") {
$var_da_assegnare = $txt_sost2;
if ($txt_sost2 > 0 and $txt_sost1 < 0) $var_da_assegnare = $txt_sost2 - 1;
if ($txt_sost2 < 0 and $txt_sost1 > 0) $var_da_assegnare = $txt_sost2 + 1;
} # fine if ($azione[2] == "a")
} # fine else if ($azione[2] == "g")
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
unset(${$array[substr($azione[1],1)]});
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$nome_arr = $array[substr($azione[1],1)];
if ($azione[2] == "val") {
unset($$nome_arr);
$array_date_contr[$nome_arr] = "";
$lista_val = explode(",",$azione[3]);
$num_lista_val = count($lista_val);
for ($num2 = 1 ; $num2 <= $num_lista_val ; $num2++) ${$nome_arr}[$num2] = $lista_val[($num2 - 1)];
} # fine if ($azione[2] == "val")
if ($azione[2] == "dat" or $azione[2] == "dap") {
unset($$nome_arr);
if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or ($azione[2] == "dap" and $data_primo_arrivo and $data_ultima_partenza)) {
$array_date_contr[$nome_arr] = "SI";
if ($azione[2] == "dat") {
$data_corr_arr = $data_inizio_selezione_orig;
$txt_sost1 = $data_fine_selezione_orig;
} # fine if ($azione[2] == "dat")
if ($azione[2] == "dap") {
$data_corr_arr = $data_primo_arrivo;
$txt_sost1 = $data_ultima_partenza;
} # fine if ($azione[2] == "dap")
$num2 = 1;
${$nome_arr}[$num2] = $data_corr_arr;
while ($data_corr_arr != $txt_sost1) {
$num2++;
$data_corr_arr = date("Y-m-d",mktime(0,0,0,substr($data_corr_arr,5,2),(substr($data_corr_arr,8,2) + 1),substr($data_corr_arr,0,4)));
${$nome_arr}[$num2] = $data_corr_arr;
} # fine while ($data_corr_arr != $txt_sost1)
} # fine if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or...
} # fine if ($azione[2] == "dat" or $azione[2] == "dap")
if ($azione[2] == "cop") {
$lista_val = ${$array[substr($azione[3],1)]};
$$nome_arr = $lista_val;
$array_date_contr[$nome_arr] = $array_date_contr[$array[substr($azione[3],1)]];
} # fine if ($azione[2] == "cop")
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
if ($azione[1] == "cont") $break_cont = 1;
else break;
} # fine if ($azione[0] == "break")

if ($azione[0] == "cont") $break_cont = 0;

} # fine if ($cond_verificata)
} # fine for $num1
unset($condizione_ini_d_vett);
unset($num_cond_ini_d_vett);
unset($azione_ini_d_vett);



if ($ripeti_tutto) {
$contratto_parte0[1] = $contratto_orig;
$ripeti_parte0[1] = "NO";
$tipo_parte0 = "";
$num_parti0_contr = 1;
} # fine if ($ripeti_tutto)
else {
if ($dir_salva) $numero_progressivo_documento = $num_prog_contr[1];
$num_parti0_contr = 0;
$contratto_vett = explode("[",$contratto_orig);
$num_contratto_vett = count($contratto_vett);
$livello = 0;
$contratto_presente = $contratto_vett[0];
for ($num1 = 1 ; $num1 < $num_contratto_vett ; $num1++) {
$parte = $contratto_vett[$num1];
$apertura = "";
$chiusura = "";
if (substr($parte,0,2) == "r]") $apertura = "r";
if (substr($parte,0,10) == "r4 array=\"") {
$parte_vett = explode("\"]",substr($parte,10));
if (strcmp($parte_vett[0],"")) {
if (!strcmp(preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parte_vett[0]),"")) $apertura = "r4 array=\"".$parte_vett[0]."\"";
} # fine if (strcmp($parte_vett[0],""))
} # fine if (substr($parte,0,10) == "r4 array=\"")
if (substr($parte,0,3) == "r6]") $apertura = "r6";
if (substr($parte,0,3) == "/r]") $chiusura = "r";
if (substr($parte,0,4) == "/r4]") $chiusura = "r4";
if (substr($parte,0,4) == "/r6]") $chiusura = "r6";
if ($apertura) {
$livello++;
if ($livello == 1 and (substr($apertura,0,2) == "r4" or $apertura == "r6")) {
if (strcmp($contratto_presente,"")) {
$num_parti0_contr++;
$contratto_parte0[$num_parti0_contr] = $contratto_presente;
$ripeti_parte0[$num_parti0_contr] = "NO";
} # fine if (strcmp($contratto_presente,""))
$contratto_presente = substr($contratto_vett[$num1],(strlen($apertura) + 1));
$var_arr_presente = substr($apertura,10,-1);
} # fine if ($livello == 1 and (substr($apertura,0,2) == "r4" or $apertura == "r6"))
else $apertura = "";
} # fine ($apertura)
if ($chiusura) {
if ($livello == 1 and ($chiusura == "r4" or $chiusura == "r6")) {
if (strcmp($contratto_presente,"")) {
$num_parti0_contr++;
$contratto_parte0[$num_parti0_contr] = $contratto_presente;
$ripeti_parte0[$num_parti0_contr] = "SI";
if ($chiusura == "r4") {
$tipo_parte0[$num_parti0_contr] = "4";
$arr_parte0[$num_parti0_contr] = $var_arr_presente;
} # fine if ($chiusura == "r4")
if ($chiusura == "r6") $tipo_parte0[$num_parti0_contr] = "6";
} # fine if (strcmp($contratto_presente,""))
$contratto_presente = substr($contratto_vett[$num1],(strlen($chiusura) + 2));
} # fine if ($livello == 1 and ($chiusura == "r4" or $chiusura == "r6"))
else $chiusura = "";
$livello--;
} # fine if ($chiusura)
if (!$apertura and !$chiusura) $contratto_presente .= "[".$contratto_vett[$num1];
} # fine for $num1
if (strcmp($contratto_presente,"")) {
$num_parti0_contr++;
$contratto_parte0[$num_parti0_contr] = $contratto_presente;
$ripeti_parte0[$num_parti0_contr] = "NO";
} # fine if (strcmp($contratto_presente,""))
} # fine else if ($ripeti_tutto)


# parti n_p0: parti del contratto se c'è una ripetizione di array o unità esternamente
for ($n_p0 = 1 ; $n_p0 <= $num_parti0_contr ; $n_p0++) {

$contratto_orig0 = $contratto_parte0[$n_p0];
unset($contratto_parte);
if (str_replace("[r]","",$contratto_orig0) == $contratto_orig0 or $contr_multilingua) {
$contratto_parte[1] = $contratto_orig0;
if ($ripeti_tutto) $ripeti_parte[1] = "SI";
else $ripeti_parte[1] = "NO";
$num_parti_contr = 1;
} # fine if (str_replace("[r]","",$contratto_orig0) == $contratto_orig0 or $contr_multilingua)
else {
$num_parti_contr = 0;
$contratto_restante = $contratto_orig0;
while (str_replace("[r]","",$contratto_restante) != $contratto_restante) {
$contr_vett = explode("[r]",$contratto_restante);
if ($contr_vett[0] != "") {
$num_parti_contr++;
$contratto_parte[$num_parti_contr] = $contr_vett[0];
$ripeti_parte[$num_parti_contr] = "NO";
} # fine if ($contr_vett[0] != "")
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 3));
$contr_vett = explode("[/r]",$contratto_restante);
$num_parti_contr++;
$contratto_parte[$num_parti_contr] = $contr_vett[0];
$ripeti_parte[$num_parti_contr] = "SI";
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 4));
} # fine while (str_replace("[r]","",$contratto_restante) != $contratto_restante)
if ($contratto_restante != "") {
$num_parti_contr++;
$contratto_parte[$num_parti_contr] = $contratto_restante;
$ripeti_parte[$num_parti_contr] = "NO";
} # fine if ($contratto_restante != "")
} # fine else if (str_replace("[r]","",$contratto_orig0) == $contratto_orig0 or $contr_multilingua)


if ($ripeti_parte0[$n_p0] == "NO") $num_ripeti0 = 1;
else {
$num_ripeti0 = 0;
if ($tipo_parte0[$n_p0] == "6") $num_ripeti0 = $num_unita;
if ($tipo_parte0[$n_p0] == "4" and @is_array(${$arr_parte0[$n_p0]})) {
$num_ripeti0 = count(${$arr_parte0[$n_p0]});
reset(${$arr_parte0[$n_p0]});
} # fine if ($tipo_parte0[$n_p0] == "4" and @is_array(${$arr_parte0[$n_p0]}))
} # fine else if ($ripeti_parte0[$n_p0] == "NO")

# ripetizione n_r0: ripetere parte del contratto se esternamente c'è una ripetizione di array o unità
for ($n_r0 = 1 ; $n_r0 <= $num_ripeti0 ; $n_r0++) {

$ripeti_prenota_data = "";
$condizioni_alternative0 = 0;
if ($ripeti_parte0[$n_p0] != "NO") {
if ($tipo_parte0[$n_p0] == "6") {
$nome_unita = $dati_app_contr[$n_r0]['nome'];
$casa_unita = $dati_app_contr[$n_r0]['casa'];
$piano_unita = $dati_app_contr[$n_r0]['piano'];
$capacita_unita = $dati_app_contr[$n_r0]['capacita'];
$priorita_unita = $dati_app_contr[$n_r0]['priorita'];
# Se questa ripetizione di unità è senza ripetizione di prenotazioni ma ci sono condizioni solo per unità, allora
# uso le condizioni applicate all'inizio delle ripetizioni come condizioni per le unità
if ($num_condizioni_rip_u) $condizioni_alternative0 = "u";
} # fine if ($tipo_parte0[$n_p0] == "6")
else {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
} # fine else if ($tipo_parte0[$n_p0] == "6")

if ($tipo_parte0[$n_p0] == "4") {
${$var_arr_nome[$arr_parte0[$n_p0]]} = key(${$arr_parte0[$n_p0]});
if ($array_date_contr[$arr_parte0[$n_p0]] == "SI") $ripeti_prenota_data = current(${$arr_parte0[$n_p0]});
next(${$arr_parte0[$n_p0]});
if ($num_condizioni_rip_a_vett[$arr_parte0[$n_p0]]) $condizioni_alternative0 = "a";
} # fine if ($tipo_parte0[$n_p0] == "4")
} # fine if ($ripeti_parte0[$n_p0] != "NO")



# parti n_p: parti del contratto se ci sono ripetizioni prenotazioni
for ($n_p = 1 ; $n_p <= $num_parti_contr ; $n_p++) {
$numero_ripetizione_prenotazioni = 1;
if ($ripeti_parte[$n_p] == "SI" or $condizioni_alternative0) {


if ($ripeti_parte[$n_p] == "SI") {
$costo_tot_somma_ripetizioni = 0;
$caparra_somma_ripetizioni = 0;
$resto_caparra_somma_ripetizioni = 0;
$pagato_somma_ripetizioni = 0;
$resto_da_pagare_somma_ripetizioni = 0;
$num_persone_tot_somma_ripetizioni = 0;
$numero_ripetizione_prenotazioni_orig = 0;

$num_condizioni_corr = $num_condizioni_ini_r;
$condizione_vett_corr = $condizione_ini_r_vett;
$num_cond_vett_corr = $num_cond_ini_r_vett;
$azione_vett_corr = $azione_ini_r_vett;
} # fine if ($ripeti_parte[$n_p] == "SI")

else {
if ($condizioni_alternative0 == "u") {
$num_condizioni_corr = $num_condizioni_rip_u;
$condizione_vett_corr = $condizione_rip_u_vett;
$num_cond_vett_corr = $num_cond_rip_u_vett;
$azione_vett_corr = $azione_rip_u_vett;
} # fine ($condizioni_alternative0 == "u")
else {
$num_condizioni_corr = $num_condizioni_rip_a_vett[$arr_parte0[$n_p0]];
$condizione_vett_corr = $condizione_rip_a_vett[$arr_parte0[$n_p0]];
$num_cond_vett_corr = $num_cond_rip_a_vett[$arr_parte0[$n_p0]];
$azione_vett_corr = $azione_rip_a_vett[$arr_parte0[$n_p0]];
} # fine ($condizioni_alternative0 == "u")
} # fine else if ($ripeti_parte[$n_p] == "SI")

# Condizioni applicate all'inizio di ogni ripetizione di prenotazioni (o solo per ripetizioni di unità)
$break_cont = 0;
for ($num1 = 0 ; $num1 < $num_condizioni_corr ; $num1++) {
$condizione = $condizione_vett_corr[$num1];
$num_se = $num_cond_vett_corr[$num1];
$azione = $azione_vett_corr[$num1];
$cond_verificata = 1;

if ($break_cont and $azione[0] != "cont") {
$condizione = "";
$cond_verificata = 0;
} # fine if ($break_cont and $azione[0] != "cont")

if ($condizione) {
if ($condizione[0] == "or") $cond_verificata = 0;
for ($num2 = 1 ; $num2 < $num_se ; $num2++) {
$se_cond_corr = $condizione[$num2];
$var_if = $se_cond_corr[0];
if (substr($var_if,-1) != ")") $var_if = $$var_if;
else {
$var_if = explode("(",substr($var_if,0,-1));
$var_if = ${$var_if[0]}[${$var_if[1]}];
} # fine else if (substr($var_if,-1) != ")")
$val_if = $se_cond_corr[3];
if ($se_cond_corr[2] == "var") {
if (substr($val_if,-1) != ")") $val_if = $$val_if;
else {
$val_if = explode("(",substr($val_if,0,-1));
$val_if = ${$val_if[0]}[${$val_if[1]}];
} # fine else if (substr($val_if,-1) != ")")
} # fine if ($se_cond_corr[2] == "var")
$cond_verificata = 0;
if (($se_cond_corr[1] == "=" and $var_if == $val_if) or ($se_cond_corr[1] == "!=" and $var_if != $val_if) or ($se_cond_corr[1] == ">" and $var_if > $val_if) or ($se_cond_corr[1] == "<" and $var_if < $val_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) != strtolower($var_if)) or ($se_cond_corr[1] == "{A}" and str_replace($val_if,"",$var_if) != $var_if)) $cond_verificata = 1;
if ($condizione[0] == "or" and $cond_verificata) break;
if ($condizione[0] == "and" and !$cond_verificata) break;
} # fine for $num2
} # fine if ($condizione)

if ($cond_verificata) {

if ($azione[0] == "set") {
$val_then = $azione[4];
if ($azione[3] == "var") {
if (substr($val_then,-1) != ")") {
if ($var_predef_data[$val_then] and $val_then != "data_inizio_selezione" and $val_then != "data_fine_selezione" and $val_then != "oggi") $val_then = formatta_data_contr($$val_then,$stile_data);
else $val_then = $$val_then;
} # fine if (substr($val_then,-1) != ")")
else {
$val_then = explode("(",substr($val_then,0,-1));
$val_then = ${$val_then[0]}[${$val_then[1]}];
} # fine else if (substr($val_then,-1) != ")")
if ($azione[9] == "low") $val_then = strtolower($val_then);
if ($azione[9] == "upp") $val_then = strtoupper($val_then);
if ($azione[9] == "url" and function_exists('urlencode')) $val_then = urlencode($val_then);
if ($azione[9] == "asc") $val_then = conv_ascii($val_then);
if ($azione[9] == "eas") $val_then = conv_ascii($val_then,"e");
if ($azione[9] == "md5") $val_then = md5($val_then);
} # fine if ($azione[3] == "var")
if (strcmp($azione[6],"")) {
$txt_sost1 = $azione[6];
if ($azione[5] == "var") {
if (substr($txt_sost1,-1) != ")") $txt_sost1 = $$txt_sost1;
else {
$txt_sost1 = explode("(",substr($txt_sost1,0,-1));
$txt_sost1 = ${$txt_sost1[0]}[${$txt_sost1[1]}];
} # fine else if (substr($txt_sost1,-1) != ")")
} # fine if ($azione[5] == "var")
$txt_sost2 = $azione[8];
if ($azione[7] == "var") {
if (substr($txt_sost2,-1) != ")") $txt_sost2 = $$txt_sost2;
else {
$txt_sost2 = explode("(",substr($txt_sost2,0,-1));
$txt_sost2 = ${$txt_sost2[0]}[${$txt_sost2[1]}];
} # fine else if (substr($txt_sost2,-1) != ")")
} # fine if ($azione[7] == "var")
$val_then = str_replace($txt_sost1,$txt_sost2,$val_then);
} # fine if (strcmp($azione[6],""))
if ($azione[2] == ".=") {
if (substr($azione[1],0,1) != "a") $var_then_orig = ${$variabile[$azione[1]]};
else $var_then_orig = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
} # fine if ($azione[2] == ".=")
else $var_then_orig = "";
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = $var_then_orig.$val_then;
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_then_orig.$val_then;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
if (substr($azione[1],0,1) != "a") $var_da_assegnare = ${$variabile[$azione[1]]};
else $var_da_assegnare = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
if (strcmp($azione[3],"")) {
while (num_caratteri_testo($var_da_assegnare) < $azione[2]) {
if ($azione[4] == "ini") $var_da_assegnare = $azione[3].$var_da_assegnare;
if ($azione[4] == "fin") $var_da_assegnare .= $azione[3];
} # fine while (num_caratteri_testo($var_da_assegnare) < $azione[2])
} # fine if (strcmp($azione[3],""))
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = tronca_testo($var_da_assegnare,0,$azione[2]);
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = tronca_testo($var_da_assegnare,0,$azione[2]);
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$cont_oper = "SI";
$var_con_punti = "NO";
$var_da_oper = $azione[2];
if (substr($var_da_oper,-1) != ")") {
if (substr($var_da_oper,-2) != "_p" or !isset(${substr($var_da_oper,0,-2)})) $var_da_oper = ${$var_da_oper};
else $var_da_oper = ${substr($var_da_oper,0,-2)};
} # fine if (substr($var_da_oper,-1) != ")")
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
if (substr($var_da_oper[0],-2) != "_p" or !isset(${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}])) $var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
else $var_da_oper = ${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = formatta_soldi($var_da_oper);
if (controlla_soldi($var_da_oper) == "NO") $cont_oper = "NO";
$var_da_oper2 = $azione[5];
if ($azione[4] == "var") {
if (substr($var_da_oper2,-1) != ")") {
if (substr($var_da_oper2,-2) != "_p" or !isset(${substr($var_da_oper2,0,-2)})) $var_da_oper2 = ${$var_da_oper2};
else $var_da_oper2 = ${substr($var_da_oper2,0,-2)};
} # fine if (substr($var_da_oper2,-1) != ")")
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
if (substr($var_da_oper2[0],-2) != "_p" or !isset(${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}])) $var_da_oper2 = ${$var_da_oper2[0]}[${$var_da_oper2[1]}];
else $var_da_oper2 = ${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
} # fine if ($azione[4] == "var")
$var_da_oper2 = formatta_soldi($var_da_oper2);
if (controlla_soldi($var_da_oper2) == "NO") $cont_oper = "NO";
if ($cont_oper != "NO") {
if ($azione[3] == "+") $var_da_assegnare = (double) $var_da_oper + (double) $var_da_oper2;
if ($azione[3] == "-") $var_da_assegnare = (double) $var_da_oper - (double) $var_da_oper2;
if ($azione[3] == "*") $var_da_assegnare = (double) $var_da_oper * (double) $var_da_oper2;
if ($azione[3] == "/") $var_da_assegnare = @((double) $var_da_oper / (double) $var_da_oper2);
if ($azione[6]) {
$var_da_assegnare = $var_da_assegnare / (double) $azione[6];
$var_da_assegnare = round($var_da_assegnare);
$var_da_assegnare = $var_da_assegnare * (double) $azione[6];
} # fine if ($azione[6])
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper != "NO")
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$cont_oper = 1;
$var_da_oper = $azione[2];
if (substr($var_da_oper,-1) != ")") $var_da_oper = ${$var_da_oper};
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[3] == "gi") $var_da_assegnare = "d";
if ($azione[3] == "me") $var_da_assegnare = "m";
if ($azione[3] == "an") $var_da_assegnare = "Y";
if ($azione[3] == "gs") $var_da_assegnare = "w";
if ($azione[3] == "is") $var_da_assegnare = "Y-m-d";
if ($azione[3] == "da") {
if ($stile_data == "usa") $var_da_assegnare = "m-d-Y";
else $var_da_assegnare = "d-m-Y";
} # fine if ($azione[3] == "da")
$txt_sost1 = 0;
$num2 = 0;
$num3 = 0;
if ($azione[5] == "g") $txt_sost1 = $azione[4];
if ($azione[5] == "m") $num2 = $azione[4];
if ($azione[5] == "a") $num3 = $azione[4];
$var_da_assegnare = date($var_da_assegnare,mktime(0,0,0,(substr($var_da_oper,5,2) + $num2),(substr($var_da_oper,8,2) + $txt_sost1),(substr($var_da_oper,0,4) + $num3)));
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = $var_da_assegnare;
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
if ($azione[3] != "is") $array_date_contr[$array[substr($azione[1],1)]] = "";
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$cont_oper = 1;
$var_da_oper = $azione[3];
if (substr($var_da_oper,-1) != ")") $var_da_oper = ${$var_da_oper};
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
$var_da_oper2 = $azione[4];
if (substr($var_da_oper2,-1) != ")") $var_da_oper2 = ${$var_da_oper2};
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
$var_da_oper2 = ${$var_da_oper2[0]}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
$var_da_oper2 = substr($var_da_oper2,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2)) {
if ($stile_data == "usa") $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,0,2)."-".substr($var_da_oper2,3,2);
else $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,3,2)."-".substr($var_da_oper2,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper2)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[2] == "g") {
$var_da_assegnare = mktime(2,0,0,substr($var_da_oper2,5,2),substr($var_da_oper2,8,2),substr($var_da_oper2,0,4)) - mktime(0,0,0,substr($var_da_oper,5,2),substr($var_da_oper,8,2),substr($var_da_oper,0,4));
$var_da_assegnare = floor((double) $var_da_assegnare / 86400);
} # fine if ($azione[2] == "g")
else {
$txt_sost1 = (substr($var_da_oper2,5,2) - substr($var_da_oper,5,2));
$txt_sost2 = (substr($var_da_oper2,0,4) - substr($var_da_oper,0,4));
if (($txt_sost1 > 0 or $txt_sost2 > 0) and substr($var_da_oper2,8,2) < substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 - 1;
if (($txt_sost1 < 0 or $txt_sost2 < 0) and substr($var_da_oper2,8,2) > substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 + 1;
if ($azione[2] == "m") $var_da_assegnare = ($txt_sost2 * 12) + $txt_sost1;
if ($azione[2] == "a") {
$var_da_assegnare = $txt_sost2;
if ($txt_sost2 > 0 and $txt_sost1 < 0) $var_da_assegnare = $txt_sost2 - 1;
if ($txt_sost2 < 0 and $txt_sost1 > 0) $var_da_assegnare = $txt_sost2 + 1;
} # fine if ($azione[2] == "a")
} # fine else if ($azione[2] == "g")
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
unset(${$array[substr($azione[1],1)]});
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$nome_arr = $array[substr($azione[1],1)];
if ($azione[2] == "val") {
unset($$nome_arr);
$array_date_contr[$nome_arr] = "";
$lista_val = explode(",",$azione[3]);
$num_lista_val = count($lista_val);
for ($num2 = 1 ; $num2 <= $num_lista_val ; $num2++) ${$nome_arr}[$num2] = $lista_val[($num2 - 1)];
} # fine if ($azione[2] == "val")
if ($azione[2] == "dat" or $azione[2] == "dap") {
unset($$nome_arr);
if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or ($azione[2] == "dap" and $data_primo_arrivo and $data_ultima_partenza)) {
$array_date_contr[$nome_arr] = "SI";
if ($azione[2] == "dat") {
$data_corr_arr = $data_inizio_selezione_orig;
$txt_sost1 = $data_fine_selezione_orig;
} # fine if ($azione[2] == "dat")
if ($azione[2] == "dap") {
$data_corr_arr = $data_primo_arrivo;
$txt_sost1 = $data_ultima_partenza;
} # fine if ($azione[2] == "dap")
$num2 = 1;
${$nome_arr}[$num2] = $data_corr_arr;
while ($data_corr_arr != $txt_sost1) {
$num2++;
$data_corr_arr = date("Y-m-d",mktime(0,0,0,substr($data_corr_arr,5,2),(substr($data_corr_arr,8,2) + 1),substr($data_corr_arr,0,4)));
${$nome_arr}[$num2] = $data_corr_arr;
} # fine while ($data_corr_arr != $txt_sost1)
} # fine if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or...
} # fine if ($azione[2] == "dat" or $azione[2] == "dap")
if ($azione[2] == "cop") {
$lista_val = ${$array[substr($azione[3],1)]};
$$nome_arr = $lista_val;
$array_date_contr[$nome_arr] = $array_date_contr[$array[substr($azione[3],1)]];
} # fine if ($azione[2] == "cop")
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
if ($azione[1] == "cont") $break_cont = 1;
else break;
} # fine if ($azione[0] == "break")

if ($azione[0] == "cont") $break_cont = 0;

} # fine if ($cond_verificata)
} # fine for $num1

} # fine if ($ripeti_parte[$n_p] == "SI" or $condizioni_alternative0)
if ($ripeti_parte[$n_p] == "SI") {



# ripetizione n_r: se c'è da ripetere la parte del contratto per ogni prenotazione
for ($n_r = 1 ; $n_r <= $num_ripeti ; $n_r++) {

if (!$ripeti_prenota_data or (${"data_fine"."_".$n_r} >= $ripeti_prenota_data and ${"data_inizio"."_".$n_r} <= $ripeti_prenota_data)) {


for ($num1 = 0 ; $num1 < $num_var_predef_ripeti ; $num1++) {
${$var_predef[$num1]} = ${$var_predef[$num1]."_".$n_r};
} # fine for $num1

$numero_ripetizione_prenotazioni_orig++;
$numero_ripetizione_prenotazioni = $numero_ripetizione_prenotazioni_orig;

if ($dir_salva and $ripeti_tutto and (!$numero_progressivo_documento or $numero_progressivo_documento < $num_prog_contr[$n_r])) $numero_progressivo_documento = $num_prog_contr[$n_r];

if ($tariffa_selezionata) {
$c_tot_selez = "c_tot_selez".$tariffa_selezionata."_".$n_r;
global $$c_tot_selez;
if ($$c_tot_selez) $costo_tot = $$c_tot_selez;
$c_tariffa_selez = "c_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$c_tariffa_selez;
if ($$c_tariffa_selez) $costo_tariffa = $$c_tariffa_selez;
$tarsett_tariffa_selez = "tarsett_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$tarsett_tariffa_selez;
if ($$tarsett_tariffa_selez) {
global ${"tariffesettimanali_".$n_r};
${"tariffesettimanali_".$n_r} = $$tarsett_tariffa_selez;
} # fine if ($$tarsett_tariffa_selez)
$n_tariffa_selez = "n_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$n_tariffa_selez;
if ($$n_tariffa_selez) $nome_tariffa = $$n_tariffa_selez;
$perctas_tariffa_selez = "perctas_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$perctas_tariffa_selez;
if ($$perctas_tariffa_selez) $percentuale_tasse_tariffa = $$perctas_tariffa_selez;
$cap_tariffa_selez = "cap_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$cap_tariffa_selez;
if ($$cap_tariffa_selez) $caparra = $$cap_tariffa_selez;
$comm_tariffa_selez = "comm_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$comm_tariffa_selez;
if ($$comm_tariffa_selez) $commissioni = $$comm_tariffa_selez;
$n_letti_agg_tariffa_selez = "n_letti_agg_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$n_letti_agg_tariffa_selez;
if ($$n_letti_agg_tariffa_selez) $n_letti_agg = $$n_letti_agg_tariffa_selez;
$numpers_tariffa_selez = "numpers_tariffa_selez".$tariffa_selezionata."_".$n_r;
global $$numpers_tariffa_selez;
if ($$numpers_tariffa_selez) $num_persone = $$numpers_tariffa_selez;
$num_costi_aggiuntivi_tsel = "num_costi_aggiuntivi_tsel".$tariffa_selezionata."_".$n_r;
global $$num_costi_aggiuntivi_tsel;
if ($$num_costi_aggiuntivi_tsel) {
$num_costi_aggiuntivi = $$num_costi_aggiuntivi_tsel;
for ($numca = 0 ; $numca < $num_costi_aggiuntivi ; $numca++) {
$nome_costo_agg_tsel = "nome_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$val_costo_agg_tsel = "val_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$perc_tasse_costo_agg_tsel = "percentuale_tasse_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$molt_max_costo_agg_tsel = "moltiplica_max_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
$giorni_costo_agg_tsel = "giorni_costo_agg".$numca."_tsel".$tariffa_selezionata."_".$n_r;
global $$nome_costo_agg_tsel,$$val_costo_agg_tsel,$$perc_tasse_costo_agg_tsel,$$molt_max_costo_agg_tsel,$$giorni_costo_agg_tsel;
${"nome_costo_agg".$numca."_".$n_r} = $$nome_costo_agg_tsel;
${"val_costo_agg".$numca."_".$n_r} = $$val_costo_agg_tsel;
${"percentuale_tasse_costo_agg".$numca."_".$n_r} = $$perc_tasse_costo_agg_tsel;
${"moltiplica_max_costo_agg".$numca."_".$n_r} = $$molt_max_costo_agg_tsel;
${"giorni_costo_agg".$numca."_".$n_r} = trasforma_id_in_date($$giorni_costo_agg_tsel,$date_id,$tableperiodi);
} # fine for $numca
} # fine if ($$num_costi_aggiuntivi_tsel)
} # fine if ($tariffa_selezionata)

if ($ripeti_prenota_data) {
if (!strcmp($tariffesettimanali[$n_r][$ripeti_prenota_data],"")) {
global ${"tariffesettimanali_".$n_r};
$var_if = explode(";",${"tariffesettimanali_".$n_r});
$var_if = explode(",",$var_if[0]);
$num2 = count($var_if);
for ($num1 = 0 ; $num1 <= $num2 ; $num1++) {
if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data) {
if (!$var_if[$num1]) $tariffesettimanali[$n_r][$ripeti_prenota_data] = 0;
else $tariffesettimanali[$n_r][$ripeti_prenota_data] = $var_if[$num1];
break;
} # fine if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data)
} # fine for $num1
} # fine if (!strcmp($tariffesettimanali[$n_r][$ripeti_prenota_data],""))
$costo_tariffa_giorno_array = $tariffesettimanali[$n_r][$ripeti_prenota_data];
$costo_tariffa_giorno_array_p = punti_in_num($costo_tariffa_giorno_array,$stile_soldi);
} # fine if ($ripeti_prenota_data)

$tutti_i_costi_agg = "";
$tutti_i_costi_agg_p = "";
$valore_tutti_costi_agg = (double) 0;
for ($numca = 0 ; $numca < $num_costi_aggiuntivi ; $numca++) {
$nome_costo_agg = "nome_costo_agg".$numca."_".$n_r;
$val_costo_agg = "val_costo_agg".$numca."_".$n_r;
$percentuale_tasse_costo_agg = "percentuale_tasse_costo_agg".$numca."_".$n_r;
$moltiplica_max_costo_agg = "moltiplica_max_costo_agg".$numca."_".$n_r;
$giorni_costo_agg = "giorni_costo_agg".$numca."_".$n_r;
$data_inserimento_costo_agg = "data_inserimento_costo_agg".$numca."_".$n_r;
$utente_inserimento_costo_agg = "utente_inserimento_costo_agg".$numca."_".$n_r;
if (!strcmp($$nome_costo_agg,"") and !strcmp($$val_costo_agg,"")) {
$$nome_costo_agg = $GLOBALS[$nome_costo_agg];
$$val_costo_agg = $GLOBALS[$val_costo_agg];
$$percentuale_tasse_costo_agg = $GLOBALS[$percentuale_tasse_costo_agg];
$$moltiplica_max_costo_agg = $GLOBALS[$moltiplica_max_costo_agg];
$$giorni_costo_agg = $GLOBALS[$giorni_costo_agg];
$$data_inserimento_costo_agg = $GLOBALS[$data_inserimento_costo_agg];
$$utente_inserimento_costo_agg = $n_utente_contr[$GLOBALS[$utente_inserimento_costo_agg]];
if ($unset_glob) {
unset($GLOBALS[$nome_costo_agg]);
unset($GLOBALS[$val_costo_agg]);
unset($GLOBALS[$percentuale_tasse_costo_agg]);
unset($GLOBALS[$moltiplica_max_costo_agg]);
unset($GLOBALS[$giorni_costo_agg]);
unset($GLOBALS[$data_inserimento_costo_agg]);
unset($GLOBALS[$utente_inserimento_costo_agg]);
} # fine if ($unset_glob)
} # fine if (!strcmp($$nome_costo_agg,"") and !strcmp($$val_costo_agg,""))
$nome_costo_agg = $$nome_costo_agg;
$val_costo_agg = $$val_costo_agg;
$percentuale_tasse_costo_agg = $$percentuale_tasse_costo_agg;
$moltiplica_max_costo_agg = $$moltiplica_max_costo_agg;
$$giorni_costo_agg = trasforma_id_in_date($$giorni_costo_agg,$date_id,$tableperiodi);
$giorni_costo_agg = $$giorni_costo_agg;
$data_inserimento_costo_agg = $$data_inserimento_costo_agg;
$utente_inserimento_costo_agg = $$utente_inserimento_costo_agg;
$val_costo_agg_p = punti_in_num($val_costo_agg,$stile_soldi);
$tutti_i_costi_agg .= "$nome_costo_agg: $val_costo_agg$tag_acapo";
$tutti_i_costi_agg_p .= "$nome_costo_agg: $val_costo_agg_p$tag_acapo";
$valore_tutti_costi_agg = (double) $valore_tutti_costi_agg + (double) $val_costo_agg;
calcola_tasse_contr($val_costo_agg,$percentuale_tasse_costo_agg,$arrotond_tasse,$tasse_costo_agg,$tasse_costo_agg_p,$val_costo_agg_senza_tasse,$val_costo_agg_senza_tasse_p,$stile_soldi);
if (str_replace(",","",$moltiplica_max_costo_agg) != $moltiplica_max_costo_agg) {
$moltiplica_max_costo_agg = explode(",",$moltiplica_max_costo_agg);
rsort($moltiplica_max_costo_agg);
$moltiplica_max_costo_agg = $moltiplica_max_costo_agg[0];
} # fine if (str_replace(",","",$moltiplica_max_costo_agg) != $moltiplica_max_costo_agg)
if ($num_costo_agg_sel == $numca) {
$nome_costo_agg_sel = $nome_costo_agg;
$valore_costo_agg_sel = $val_costo_agg;
$valore_costo_agg_sel_p = $val_costo_agg_p;
$percentuale_tasse_costo_agg_sel = $percentuale_tasse_costo_agg;
$tasse_costo_agg_sel = $tasse_costo_agg;
$tasse_costo_agg_sel_p = $tasse_costo_agg_p;
$moltiplica_max_costo_agg_sel = $moltiplica_max_costo_agg;
} # fine if ($num_costo_agg_sel == $numca)
} # fine for $numca
$valore_tutti_costi_agg_p = punti_in_num($valore_tutti_costi_agg,$stile_soldi);

$tutti_i_pagamenti = "";
$tutti_i_pagamenti_p = "";
for ($num1 = 0 ; $num1 < $num_pagamenti ; $num1++) {
$saldo_paga = "saldo_paga".$num1."_".$n_r;
$data_paga = "data_paga".$num1."_".$n_r;
$utente_paga = "utente_paga".$num1."_".$n_r;
$metodo_paga = "metodo_paga".$num1."_".$n_r;
if (!strcmp($$saldo_paga,"") and !strcmp($$data_paga,"") and !strcmp($$metodo_paga,"")) {
$$saldo_paga = $GLOBALS[$saldo_paga];
$$data_paga = $GLOBALS[$data_paga];
$$utente_paga = $n_utente_contr[$GLOBALS[$utente_paga]];
if (strcmp($GLOBALS[$metodo_paga],"")) $$metodo_paga = $GLOBALS[$metodo_paga];
if ($unset_glob) {
unset($GLOBALS[$saldo_paga]);
unset($GLOBALS[$data_paga]);
unset($GLOBALS[$utente_paga]);
unset($GLOBALS[$metodo_paga]);
} # fine if ($unset_glob)
} # fine if (!strcmp($$saldo_paga,"") and !strcmp($$data_paga,"") and !strcmp($$metodo_paga,""))
$data_paga_f = formatta_data_contr($$data_paga,$stile_data);
$saldo_paga_p = punti_in_num($$saldo_paga,$stile_soldi);
$tutti_i_pagamenti .= str_replace(" ","$tag_spazio",$data_paga_f."  ".$$saldo_paga." $nome_valuta  ".$$metodo_paga);
$tutti_i_pagamenti_p .= str_replace(" ","$tag_spazio",$data_paga_f."  ".$saldo_paga_p." $nome_valuta  ".$$metodo_paga);
if (($num1 + 1) != $num_pagamenti) {
$tutti_i_pagamenti .= $tag_acapo;
$tutti_i_pagamenti_p .= $tag_acapo;
} # fine if (($num1 + 1) != $num_pagamenti)
} # fine for $num1
$valore_ultimo_pagamento = ${"saldo_paga".($num_pagamenti - 1)."_".$n_r};
$valore_ultimo_pagamento_p = punti_in_num($valore_ultimo_pagamento,$stile_soldi);
$data_ultimo_pagamento = formatta_data_contr(${"data_paga".($num_pagamenti - 1)."_".$n_r},$stile_data);
$utente_ultimo_pagamento = ${"utente_paga".($num_pagamenti - 1)."_".$n_r};
$metodo_ultimo_pagamento = ${"metodo_paga".($num_pagamenti - 1)."_".$n_r};


if ($costo_tot) $costo_tot_somma_ripetizioni = $costo_tot_somma_ripetizioni + $costo_tot;
if ($caparra) $caparra_somma_ripetizioni = $caparra_somma_ripetizioni + $caparra;
if ($costo_tot) $resto_caparra_somma_ripetizioni = $resto_caparra_somma_ripetizioni + $costo_tot;
if ($caparra) $resto_caparra_somma_ripetizioni = $resto_caparra_somma_ripetizioni - $caparra;
if ($pagato) $pagato_somma_ripetizioni = $pagato_somma_ripetizioni + $pagato;
if ($costo_tot) $resto_da_pagare_somma_ripetizioni = $resto_da_pagare_somma_ripetizioni + $costo_tot;
if ($pagato) $resto_da_pagare_somma_ripetizioni = $resto_da_pagare_somma_ripetizioni - $pagato;
if ($num_persone != "non specificato" and $num_persone != "") $num_persone_tot_somma_ripetizioni = $num_persone_tot_somma_ripetizioni + $num_persone;
if ($n_letti_agg) $num_persone_tot_somma_ripetizioni = $num_persone_tot_somma_ripetizioni + $n_letti_agg;

if ($costo_tot and $caparra) {
$resto_caparra = $costo_tot - $caparra;
$resto_caparra_p = punti_in_num($resto_caparra,$stile_soldi);
} # fine if ($costo_tot and $caparra)
if ($costo_tot and $commissioni) {
$resto_commissioni = $costo_tot - $commissioni;
$resto_commissioni_p = punti_in_num($resto_commissioni,$stile_soldi);
} # fine if ($costo_tot and $commissioni)
if ($costo_tot) {
$resto_da_pagare = $costo_tot - $pagato;
$resto_da_pagare_p = punti_in_num($resto_da_pagare,$stile_soldi);
} # fine if ($costo_tot)

$nome_orig = $nome;
$soprannome_orig = $soprannome;
$cognome_orig = $cognome;
$data_nascita_orig = $data_nascita;
$documento_orig = $documento;
$nazione_orig = $nazione;
$regione_orig = $regione;
$citta_orig = $citta;
$via_orig = $via;
$numcivico_orig = $numcivico;
$telefono_orig = $telefono;
$telefono2_orig = $telefono2;
$telefono3_orig = $telefono3;
$fax_orig = $fax;
$email_orig = $email;
$cap_orig = $cap;
$codice_fiscale_orig = $codice_fiscale;
$partita_iva_orig = $partita_iva;
$num_persone_orig = $num_persone;
$caparra_orig = $caparra;
$commissioni_orig = $commissioni;
$data_inizio_orig = $data_inizio;
$data_fine_orig = $data_fine;
$num_periodi_orig = $num_periodi;
$orario_entrata_stimato_orig = $orario_entrata_stimato;
$nome_tariffa_orig = $nome_tariffa;
$costo_tariffa_orig = $costo_tariffa;
$sconto_orig = $sconto;
$percentuale_tasse_tariffa_orig = $percentuale_tasse_tariffa;
$commento_orig = $commento;
$origine_prenotazione_orig = $origine_prenotazione;
$unita_occupata_orig = $unita_occupata;
$unita_assegnabili_orig = $unita_assegnabili;
$pagato_orig = $pagato;
$costo_tot_orig = $costo_tot;
$n_letti_agg_orig = $n_letti_agg;
$numero_prenotazione_orig = $numero_prenotazione;
$data_inserimento_prenotazione_orig = $data_inserimento_prenotazione;

$codice_cittadinanza = trova_codice_rel($cittadinanza,$rel_esist,"nazione","nazioni",$codice2_cittadinanza,$codice3_cittadinanza,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_nascita = trova_codice_rel($nazione_nascita,$rel_esist,"nazione","nazioni",$codice2_nazione_nascita,$codice3_nazione_nascita,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_nascita = trova_codice_rel($regione_nascita,$rel_esist,"regione","regioni",$codice2_regione_nascita,$codice3_regione_nascita,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_nascita = trova_codice_rel($citta_nascita,$rel_esist,"citta","citta",$codice2_citta_nascita,$codice3_citta_nascita,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione = trova_codice_rel($nazione,$rel_esist,"nazione","nazioni",$codice2_nazione,$codice3_nazione,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione = trova_codice_rel($regione,$rel_esist,"regione","regioni",$codice2_regione,$codice3_regione,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta = trova_codice_rel($citta,$rel_esist,"citta","citta",$codice2_citta,$codice3_citta,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_tipo_documento = trova_codice_rel($tipo_documento,$rel_esist,"documentoid","documentiid",$codice2_tipo_documento,$codice3_tipo_documento,$tabledocumentiid,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_citta_documento = trova_codice_rel($citta_documento,$rel_esist,"citta","citta",$codice2_citta_documento,$codice3_citta_documento,$tablecitta,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_regione_documento = trova_codice_rel($regione_documento,$rel_esist,"regione","regioni",$codice2_regione_documento,$codice3_regione_documento,$tableregioni,$tablerelutenti,C_ID_UTENTE_CONTR);
$codice_nazione_documento = trova_codice_rel($nazione_documento,$rel_esist,"nazione","nazioni",$codice2_nazione_documento,$codice3_nazione_documento,$tablenazioni,$tablerelutenti,C_ID_UTENTE_CONTR);

calcola_tasse_contr($costo_tariffa,$percentuale_tasse_tariffa,$arrotond_tasse,$tasse_tariffa,$tasse_tariffa_p,$costo_tariffa_senza_tasse,$costo_tariffa_senza_tasse_p,$stile_soldi);
calcola_tasse_contr($sconto,$percentuale_tasse_tariffa,$arrotond_tasse,$tasse_sconto,$tasse_sconto_p,$sconto_senza_tasse,$sconto_senza_tasse_p,$stile_soldi);

$apertura_rip_contr = "";
$chiusura_rip_contr = "";
$contratto_ripetizione = "";
$errore_ripetizione = "";
if ($ripeti_tutto) $email_gia_inviata = 0;

if ($tipo_contratto == "contreml" and $ripeti_tutto) {
if (${"email_".$n_r}) {
$cliente = esegui_query("select idclienti from $tableclienti where email = '".aggslashdb(${"email_".$n_r})."' and cognome = '".aggslashdb(${"cognome_".$n_r})."' and doc_inviati $ILIKE '%#@?".aggslashdb($oggetto_email)."#@?%' ");
if (numlin_query($cliente) >= 1) {
$email_gia_inviata = 1;
$apertura_rip_contr .= "".mex("<span class=\"colblu\">Attenzione</span>: una email con lo stesso oggetto è già stata inviata al cliente",$pag)." ".${"cognome_".$n_r}."<br><br>";
} # fine if (numlin_query($cliente) >= 1)
} # fine if (${"email_".$n_r})
$apertura_rip_contr .= "<table><tr><td align=\"right\">".mex("Da",$pag).":</td><td>";
if ($modifica_pers != "NO") $apertura_rip_contr .= "<input type=\"text\" name=\"mittente_email$n_r\" size=\"60\" value=\"$mittente_email\">";
else $apertura_rip_contr .= "<b>$mittente_email</b>";
$apertura_rip_contr .= "</td></tr><tr><td align=\"right\">
".mex("A",$pag).":</td><td>
<input type=\"text\" name=\"destinatario_email$n_r\" size=\"60\" value=\"".${"email_".$n_r}."\">
</td></tr><tr><td align=\"right\">
".mex("Oggetto",$pag).":</td><td>
<input type=\"text\" name=\"oggetto_email$n_r\" size=\"60\" value=\"$oggetto_email\"></td></tr>";
if ($allegato_email) {
$apertura_rip_contr .= "<tr><td></td><td><label><input type=\"checkbox\" name=\"allega$n_r\" value=\"SI\" checked>
".mex("Allega",$pag)." <b>$allegato_email</b></label></td></tr>";
} # fine if ($allegato_email)
$apertura_rip_contr .= "<tr><td style=\"height: 3px;\"></td></tr></table>
&nbsp;&nbsp;<textarea name=\"testo_email$n_r\" rows=32 cols=90>";
$chiusura_rip_contr .= "</textarea><br>
<table><tr><td style=\"height: 3px;\"></td></tr></table>
<hr style=\"width: 95%; margin-left: 6px; text-align: left;\">";
} # fine if ($tipo_contratto == "contreml" and $ripeti_tutto)

if ($contr_multilingua) {
if (!$codice_lingua) $contratto_parte[1] = $contratti_orig_mln[$contratti_orig_mln['predef']];
else {
if (!strcmp($contratti_orig_mln[$codice_lingua],"")) $contratto_parte[1] = $contratti_orig_mln[$contratti_orig_mln['predef']];
else $contratto_parte[1] = $contratti_orig_mln[$codice_lingua];
} # fine else if (!$codice_lingua)
} # fine if ($contr_multilingua)


# Ripetizioni per gli ospiti, costi aggiuntivi, array e unità all'interno di ogni prenotazione
unset($contratto_parte2);
if (str_replace("[r2]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and str_replace("[r3]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and !preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and str_replace("[r5]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and str_replace("[r6]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p]) {
$contratto_parte2[1] = $contratto_parte[$n_p];
$ripeti_parte2[1] = "NO";
$num_parti2_contr = 1;
} # fine if (str_replace("[r2]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and...
else {
$num_parti2_contr = 0;
$contratto_restante = $contratto_parte[$n_p];
while (str_replace("[r2]","",$contratto_restante) != $contratto_restante or str_replace("[r3]","",$contratto_restante) != $contratto_restante or preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante) or str_replace("[r5]","",$contratto_restante) != $contratto_restante or str_replace("[r6]","",$contratto_restante) != $contratto_restante) {
$contr_vett2 = explode("[r2]",$contratto_restante);
$contr_vett3 = explode("[r3]",$contratto_restante);
$contr_vett4 = preg_split("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante);
$contr_vett5 = explode("[r5]",$contratto_restante);
$contr_vett6 = explode("[r6]",$contratto_restante);
$l0_cv2 = strlen($contr_vett2[0]);
$l0_cv3 = strlen($contr_vett3[0]);
$l0_cv4 = strlen($contr_vett4[0]);
$l0_cv5 = strlen($contr_vett5[0]);
$l0_cv6 = strlen($contr_vett6[0]);
if ($l0_cv2 > $l0_cv6 and $l0_cv3 > $l0_cv6 and $l0_cv4 > $l0_cv6 and $l0_cv5 > $l0_cv6) {
$contr_vett = $contr_vett6;
$tipo_contr_vett = 6;
} # fine if ($l0_cv2 > $l0_cv6 and $l0_cv3 > $l0_cv6 and $l0_cv4 > $l0_cv6 and $l0_cv5 > $l0_cv6)
else {
if ($l0_cv2 > $l0_cv5 and $l0_cv3 > $l0_cv5 and $l0_cv4 > $l0_cv5) {
$contr_vett = $contr_vett5;
$tipo_contr_vett = 5;
} # fine if ($l0_cv2 > $l0_cv5 and $l0_cv3 > $l0_cv5 and $l0_cv4 > $l0_cv5)
else {
if ($l0_cv2 > $l0_cv4 and $l0_cv3 > $l0_cv4) {
$contr_vett = $contr_vett4;
$tipo_contr_vett = 4;
} # fine if ($l0_cv2 > $l0_cv4 and $l0_cv3 > $l0_cv4)
else {
if ($l0_cv2 > $l0_cv3) {
$contr_vett = $contr_vett3;
$tipo_contr_vett = 3;
} # fine if ($l0_cv2 > $l0_cv3)
else {
$contr_vett = $contr_vett2;
$tipo_contr_vett = 2;
} # fine else if ($l0_cv2 > $l0_cv3)
} # fine else if ($l0_cv2 > $l0_cv4 and $l0_cv3 > $l0_cv4)
} # fine else if ($l0_cv2 > $l0_cv5 and $l0_cv3 > $l0_cv5 and $l0_cv4 > $l0_cv5)
} # fine else if ($l0_cv2 > $l0_cv6 and $l0_cv3 > $l0_cv6 and $l0_cv4 > $l0_cv6 and $l0_cv5 > $l0_cv6)
if ($contr_vett[0] != "") {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if ($contr_vett[0] != "")
$contratto_restante = substr($contratto_restante,strlen($contr_vett[0]));
if ($tipo_contr_vett == 4) {
$arr_ripeti = explode("\"]",$contratto_restante,2);
$arr_ripeti = str_replace("[r4 array=\"","",$arr_ripeti[0]);
$contratto_restante = substr($contratto_restante,strlen("[r4 array=\"$arr_ripeti\"]"));
} # fine if ($tipo_contr_vett == 4)
else $contratto_restante = substr($contratto_restante,4);
$contr_vett = explode("[/r".$tipo_contr_vett."]",$contratto_restante);
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "SI";
$tipo_parte2[$num_parti2_contr] = $tipo_contr_vett;
if ($tipo_contr_vett == 4) $arr_parte2[$num_parti2_contr] = $arr_ripeti;
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 5));
} # fine while (str_replace("[r2]","",$contratto_restante) != $contratto_restante or...
if ($contratto_restante != "") {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contratto_restante;
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if ($contratto_restante != "")
} # fine else if (str_replace("[r2]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p] and...

# parti n_p2: parti del contratto se ci sono ripetizioni ospiti, costi agg o array interni
for ($n_p2 = 1 ; $n_p2 <= $num_parti2_contr ; $n_p2++) {
if ($ripeti_parte2[$n_p2] != "SI") $num_ripeti2 = 1;
else {
if ($tipo_parte2[$n_p2] == 2) {
$ospiti = esegui_query("select idclienti,parentela from $tablerclientiprenota where idprenota = '".aggslashdb($numero_prenotazione_orig)."' order by num_ordine ");
$num_ripeti2 = numlin_query($ospiti);
$num_ospiti_tot = $num_ripeti2;
} # fine if ($tipo_parte2[$n_p2] == 2)
if ($tipo_parte2[$n_p2] == 3) $num_ripeti2 = $num_costi_aggiuntivi;
if ($tipo_parte2[$n_p2] == 4) {
if (@is_array(${$arr_parte2[$n_p2]})) {
$num_ripeti2 = count(${$arr_parte2[$n_p2]});
reset(${$arr_parte2[$n_p2]});
} # fine if (@is_array(${$arr_parte2[$n_p2]}))
else $num_ripeti2 = 0;
} # fine if ($tipo_parte2[$n_p2] == 4)
if ($tipo_parte2[$n_p2] == 5) $num_ripeti2 = $num_pagamenti;
if ($tipo_parte2[$n_p2] == 6) $num_ripeti2 = $num_unita;
} # fine else if ($ripeti_parte2[$n_p2] != "SI")

# ripetizione n_r2: se c'è da ripetere la parte del contratto per ospiti, costi agg, array o unità interni
for ($n_r2 = 1 ; $n_r2 <= $num_ripeti2 ; $n_r2++) {
$mostra_ripetizione = 1;
$condizioni_alternative = 0;

if ($ripeti_parte2[$n_p2] == "SI") {

if ($tipo_parte2[$n_p2] == 2) {
$numero_ospite = $n_r2;
$idospite = risul_query($ospiti,($n_r2 - 1),'idclienti');
$parentela_ospite = risul_query($ospiti,($n_r2 - 1),'parentela');
$dati_osp = esegui_query("select * from $tableclienti where idclienti = '$idospite' ");
$utente_ospite = risul_query($dati_osp,0,'utente_inserimento');
$cognome_ospite = "";
$nome_ospite = "";
$soprannome_ospite = "";
$titolo_ospite = "";
$sesso_ospite = "";
$data_nascita_ospite = "";
$citta_nascita_ospite = "";
$regione_nascita_ospite = "";
$nazione_nascita_ospite = "";
$cittadinanza_ospite = "";
$nazione_ospite = "";
$regione_ospite = "";
$citta_ospite = "";
$via_ospite = "";
$numcivico_ospite = "";
$cap_ospite = "";
$documento_ospite = "";
$tipo_documento_ospite = "";
$citta_documento_ospite = "";
$regione_documento_ospite = "";
$nazione_documento_ospite = "";
$scadenza_documento_ospite = "";
$telefono_ospite = "";
$telefono2_ospite = "";
$telefono3_ospite = "";
$fax_ospite = "";
$email_ospite = "";
$codice_fiscale_ospite = "";
$partita_iva_ospite = "";
if (numlin_query($dati_osp) == 1 and $vedi_clienti != "NO" and ($vedi_clienti != "PROPRI" or $utente_ospite == C_ID_UTENTE) and ($vedi_clienti != "GRUPPI" or $utenti_gruppi[$utente_ospite])) {
$cognome_ospite = risul_query($dati_osp,0,'cognome');
$nome_ospite = risul_query($dati_osp,0,'nome');
$soprannome_ospite = risul_query($dati_osp,0,'soprannome');
$titolo_ospite = risul_query($dati_osp,0,'titolo');
$sesso_ospite = risul_query($dati_osp,0,'sesso');
$data_nascita_ospite = risul_query($dati_osp,0,'datanascita');
$citta_nascita_ospite = risul_query($dati_osp,0,'cittanascita');
$regione_nascita_ospite = risul_query($dati_osp,0,'regionenascita');
$nazione_nascita_ospite = risul_query($dati_osp,0,'nazionenascita');
$cittadinanza_ospite = risul_query($dati_osp,0,'nazionalita');
$nazione_ospite = risul_query($dati_osp,0,'nazione');
$regione_ospite = risul_query($dati_osp,0,'regione');
$citta_ospite = risul_query($dati_osp,0,'citta');
$via_ospite = risul_query($dati_osp,0,'via');
$numcivico_ospite = risul_query($dati_osp,0,'numcivico');
$cap_ospite = risul_query($dati_osp,0,'cap');
$documento_ospite = risul_query($dati_osp,0,'documento');
$tipo_documento_ospite = risul_query($dati_osp,0,'tipodoc');
$citta_documento_ospite = risul_query($dati_osp,0,'cittadoc');
$regione_documento_ospite = risul_query($dati_osp,0,'regionedoc');
$nazione_documento_ospite = risul_query($dati_osp,0,'nazionedoc');
$scadenza_documento_ospite = risul_query($dati_osp,0,'scadenzadoc');
$telefono_ospite = risul_query($dati_osp,0,'telefono');
$telefono2_ospite = risul_query($dati_osp,0,'telefono2');
$telefono3_ospite = risul_query($dati_osp,0,'telefono3');
$fax_ospite = risul_query($dati_osp,0,'fax');
$email_ospite = risul_query($dati_osp,0,'email');
$codice_fiscale_ospite = risul_query($dati_osp,0,'cod_fiscale');
$partita_iva_ospite = risul_query($dati_osp,0,'partita_iva');
} # fine if (numlin_query($dati_osp) == 1 and...
$codice_cittadinanza_ospite = trova_codice_rel($cittadinanza_ospite,$rel_esist,"nazione","nazioni",$codice2_cittadinanza_ospite,$codice3_cittadinanza_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE);
$codice_parentela_ospite = trova_codice_rel($parentela_ospite,$rel_esist,"parentela","parentele",$codice2_parentela_ospite,$codice3_parentela_ospite,$tableparentele,$tablerelutenti,C_ID_UTENTE);
$codice_nazione_nascita_ospite = trova_codice_rel($nazione_nascita_ospite,$rel_esist,"nazione","nazioni",$codice2_nazione_nascita_ospite,$codice3_nazione_nascita_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE);
$codice_regione_nascita_ospite = trova_codice_rel($regione_nascita_ospite,$rel_esist,"regione","regioni",$codice2_regione_nascita_ospite,$codice3_regione_nascita_ospite,$tableregioni,$tablerelutenti,C_ID_UTENTE);
$codice_citta_nascita_ospite = trova_codice_rel($citta_nascita_ospite,$rel_esist,"citta","citta",$codice2_citta_nascita_ospite,$codice3_citta_nascita_ospite,$tablecitta,$tablerelutenti,C_ID_UTENTE);
$codice_nazione_ospite = trova_codice_rel($nazione_ospite,$rel_esist,"nazione","nazioni",$codice2_nazione_ospite,$codice3_nazione_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE);
$codice_regione_ospite = trova_codice_rel($regione_ospite,$rel_esist,"regione","regioni",$codice2_regione_ospite,$codice3_regione_ospite,$tableregioni,$tablerelutenti,C_ID_UTENTE);
$codice_citta_ospite = trova_codice_rel($citta_ospite,$rel_esist,"citta","citta",$codice2_citta_ospite,$codice3_citta_ospite,$tablecitta,$tablerelutenti,C_ID_UTENTE);
$codice_tipo_documento_ospite = trova_codice_rel($tipo_documento_ospite,$rel_esist,"documentoid","documentiid",$codice2_tipo_documento_ospite,$codice3_tipo_documento_ospite,$tabledocumentiid,$tablerelutenti,C_ID_UTENTE);
$codice_citta_documento_ospite = trova_codice_rel($citta_documento_ospite,$rel_esist,"citta","citta",$codice2_citta_documento_ospite,$codice3_citta_documento_ospite,$tablecitta,$tablerelutenti,C_ID_UTENTE);
$codice_regione_documento_ospite = trova_codice_rel($regione_documento_ospite,$rel_esist,"regione","regioni",$codice2_regione_documento_ospite,$codice3_regione_documento_ospite,$tableregioni,$tablerelutenti,C_ID_UTENTE);
$codice_nazione_documento_ospite = trova_codice_rel($nazione_documento_ospite,$rel_esist,"nazione","nazioni",$codice2_nazione_documento_ospite,$codice3_nazione_documento_ospite,$tablenazioni,$tablerelutenti,C_ID_UTENTE);
if ($num_condizioni_rip_o) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_o;
$condizione_vett_corr = $condizione_rip_o_vett;
$num_cond_vett_corr = $num_cond_rip_o_vett;
$azione_vett_corr = $azione_rip_o_vett;
} # fine if ($num_condizioni_rip_o)
} # fine if ($tipo_parte2[$n_p2] == 2)
else $numero_ospite = 0;

if ($tipo_parte2[$n_p2] == 3) {
$numca = ($n_r2 - 1);
$nome_costo_agg = "nome_costo_agg".$numca."_".$n_r;
$val_costo_agg = "val_costo_agg".$numca."_".$n_r;
$percentuale_tasse_costo_agg = "percentuale_tasse_costo_agg".$numca."_".$n_r;
$moltiplica_max_costo_agg = "moltiplica_max_costo_agg".$numca."_".$n_r;
$giorni_costo_agg = "giorni_costo_agg".$numca."_".$n_r;
$data_inserimento_costo_agg = "data_inserimento_costo_agg".$numca."_".$n_r;
$utente_inserimento_costo_agg = "utente_inserimento_costo_agg".$numca."_".$n_r;
$nome_costo_agg = $$nome_costo_agg;
$valore_costo_agg = $$val_costo_agg;
$percentuale_tasse_costo_agg = $$percentuale_tasse_costo_agg;
$moltiplica_max_costo_agg = $$moltiplica_max_costo_agg;
$giorni_costo_agg = $$giorni_costo_agg;
if ($ripeti_prenota_data and $giorni_costo_agg and !strstr($giorni_costo_agg,",$ripeti_prenota_data,")) $mostra_ripetizione = 0;
$data_inserimento_costo_agg = $$data_inserimento_costo_agg;
$utente_inserimento_costo_agg = $$utente_inserimento_costo_agg;
$valore_costo_agg_p = punti_in_num($valore_costo_agg,$stile_soldi);
calcola_tasse_contr($valore_costo_agg,$percentuale_tasse_costo_agg,$arrotond_tasse,$tasse_costo_agg,$tasse_costo_agg_p,$valore_costo_agg_senza_tasse,$valore_costo_agg_senza_tasse_p,$stile_soldi);
if (str_replace(",","",$moltiplica_max_costo_agg) != $moltiplica_max_costo_agg and $mostra_ripetizione) {
$moltiplica_max_costo_agg = explode(",",$moltiplica_max_costo_agg);
if ($ripeti_prenota_data and $giorni_costo_agg) {
$var_if = explode(",",$giorni_costo_agg);
$num2 = count($var_if);
for ($num1 = 1 ; $num1 < $num2 ; $num1++) {
if ($var_if[$num1] == $ripeti_prenota_data) {
$moltiplica_max_costo_agg = $moltiplica_max_costo_agg[$num1];
break;
} # fine if ($var_if[$num1] == $ripeti_prenota_data)
} # fine for $num1
} # fine if ($ripeti_prenota_data and $giorni_costo_agg)
else {
rsort($moltiplica_max_costo_agg);
$moltiplica_max_costo_agg = $moltiplica_max_costo_agg[0];
} # fine else if ($ripeti_prenota_data and $giorni_costo_agg)
} # fine if (str_replace(",","",$moltiplica_max_costo_agg) != $moltiplica_max_costo_agg and $mostra_ripetizione)
if ($num_condizioni_rip_c) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_c;
$condizione_vett_corr = $condizione_rip_c_vett;
$num_cond_vett_corr = $num_cond_rip_c_vett;
$azione_vett_corr = $azione_rip_c_vett;
} # fine if ($num_condizioni_rip_c)
} # fine if ($tipo_parte2[$n_p2] == 3)
else {
$nome_costo_agg = "";
$valore_costo_agg = 0;
$valore_costo_agg_p = 0;
$percentuale_tasse_costo_agg = 0;
$tasse_costo_agg = 0;
$resto_tasse_costo_agg = 0;
$moltiplica_max_costo_agg = 0;
$giorni_costo_agg = "";
$data_inserimento_costo_agg = "";
$utente_inserimento_costo_agg = "";
} # fine else if ($tipo_parte2[$n_p2] == 3)

if ($tipo_parte2[$n_p2] == 4) {
$ripeti_prenota_data2 = "";
${$var_arr_nome[$arr_parte2[$n_p2]]} = key(${$arr_parte2[$n_p2]});
if ($array_date_contr[$arr_parte2[$n_p2]] == "SI") $ripeti_prenota_data2 = current(${$arr_parte2[$n_p2]});
next(${$arr_parte2[$n_p2]});
if ($ripeti_prenota_data2) {
if (!strcmp($tariffesettimanali[$n_r][$ripeti_prenota_data2],"")) {
global ${"tariffesettimanali_".$n_r};
$var_if = explode(";",${"tariffesettimanali_".$n_r});
$var_if = explode(",",$var_if[0]);
$num2 = count($var_if);
for ($num1 = 0 ; $num1 <= $num2 ; $num1++) {
if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data2) {
if (!$var_if[$num1]) $tariffesettimanali[$n_r][$ripeti_prenota_data2] = 0;
else $tariffesettimanali[$n_r][$ripeti_prenota_data2] = $var_if[$num1];
break;
} # fine if (date("Y-m-d",mktime(0,0,0,substr($data_inizio,5,2),(substr($data_inizio,8,2) + $num1),(int) substr($data_inizio,0,4))) == $ripeti_prenota_data2)
} # fine for $num1
} # fine if (!strcmp($tariffesettimanali[$n_r][$ripeti_prenota_data2],""))
$costo_tariffa_giorno_array = $tariffesettimanali[$n_r][$ripeti_prenota_data2];
$costo_tariffa_giorno_array_p = punti_in_num($costo_tariffa_giorno_array,$stile_soldi);
} # fine if ($ripeti_prenota_data2)
if ($num_condizioni_rip_a_vett[$arr_parte2[$n_p2]]) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_a_vett[$arr_parte2[$n_p2]];
$condizione_vett_corr = $condizione_rip_a_vett[$arr_parte2[$n_p2]];
$num_cond_vett_corr = $num_cond_rip_a_vett[$arr_parte2[$n_p2]];
$azione_vett_corr = $azione_rip_a_vett[$arr_parte2[$n_p2]];
} # fine if ($num_condizioni_rip_a_vett[$arr_parte2[$n_p2]])
} # fine if ($tipo_parte2[$n_p2] == 4)

if ($tipo_parte2[$n_p2] == 5) {
$valore_pagamento = ${"saldo_paga".($n_r2 - 1)."_".$n_r};
$valore_pagamento_p = punti_in_num($valore_pagamento,$stile_soldi);
$data_pagamento = ${"data_paga".($n_r2 - 1)."_".$n_r};
$utente_pagamento = ${"utente_paga".($n_r2 - 1)."_".$n_r};
$metodo_pagamento = ${"metodo_paga".($n_r2 - 1)."_".$n_r};
if ($num_condizioni_rip_p) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_p;
$condizione_vett_corr = $condizione_rip_p_vett;
$num_cond_vett_corr = $num_cond_rip_p_vett;
$azione_vett_corr = $azione_rip_p_vett;
} # fine if ($num_condizioni_rip_p)
} # fine if ($tipo_parte2[$n_p2] == 5)
else {
$valore_pagamento = 0;
$valore_pagamento_p = 0;
$data_pagamento = "";
$utente_pagamento = "";
$metodo_pagamento = "";
} # fine else if ($tipo_parte2[$n_p2] == 5)

if ($tipo_parte2[$n_p2] == 6) {
$nome_unita = $dati_app_contr[$n_r2]['nome'];
$casa_unita = $dati_app_contr[$n_r2]['casa'];
$piano_unita = $dati_app_contr[$n_r2]['piano'];
$capacita_unita = $dati_app_contr[$n_r2]['capacita'];
$priorita_unita = $dati_app_contr[$n_r2]['priorita'];
if ($num_condizioni_rip_u) {
$condizioni_alternative = 1;
$num_condizioni_corr = $num_condizioni_rip_u;
$condizione_vett_corr = $condizione_rip_u_vett;
$num_cond_vett_corr = $num_cond_rip_u_vett;
$azione_vett_corr = $azione_rip_u_vett;
} # fine if ($num_condizioni_rip_u)
} # fine if ($tipo_parte2[$n_p2] == 6)
elseif ($tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
} # fine elseif ($tipo_parte0[$n_p0] != "6")

} # fine if ($ripeti_parte2[$n_p2] == "SI")

else {
$nome_costo_agg = "";
$valore_costo_agg = 0;
$valore_costo_agg_p = 0;
$percentuale_tasse_costo_agg = 0;
$tasse_costo_agg = 0;
$resto_tasse_costo_agg = 0;
$moltiplica_max_costo_agg = 0;
$giorni_costo_agg = "";
$data_inserimento_costo_agg = "";
$utente_inserimento_costo_agg = "";
$numero_ospite = 0;
$valore_pagamento = 0;
$valore_pagamento_p = 0;
$data_pagamento = "";
$utente_pagamento = "";
$metodo_pagamento = "";
if ($tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
} # fine if ($tipo_parte0[$n_p0] != "6")
} # fine if else ($ripeti_parte2[$n_p2] == "SI")

if ($mostra_ripetizione) {

#for ($num1 = 0 ; $num1 < $num_variabili ; $num1++) ${$variabile[$num_var]} = "";
if ($num_persone != "non specificato" and $num_persone != "") $num_persone_tot = $num_persone + $n_letti_agg;
else $num_persone_tot = "";


if (!$condizioni_alternative) {
$num_condizioni_corr = $num_condizioni;
$condizione_vett_corr = $condizione_vett;
$num_cond_vett_corr = $num_cond_vett;
$azione_vett_corr = $azione_vett;
} # fine if (!$condizioni_alternative)

# Condizioni applicate ad ogni ripetizione di prenotazione
$break_cont = 0;
for ($num1 = 0 ; $num1 < $num_condizioni_corr ; $num1++) {
$condizione = $condizione_vett_corr[$num1];
$num_se = $num_cond_vett_corr[$num1];
$azione = $azione_vett_corr[$num1];
$cond_verificata = 1;

if ($break_cont and $azione[0] != "cont") {
$condizione = "";
$cond_verificata = 0;
} # fine if ($break_cont and $azione[0] != "cont")

if ($condizione) {
if ($condizione[0] == "or") $cond_verificata = 0;
for ($num2 = 1 ; $num2 < $num_se ; $num2++) {
$se_cond_corr = $condizione[$num2];
$var_if = $se_cond_corr[0];
if (substr($var_if,-1) != ")") $var_if = $$var_if;
else {
$var_if = explode("(",substr($var_if,0,-1));
$var_if = ${$var_if[0]}[${$var_if[1]}];
} # fine else if (substr($var_if,-1) != ")")
$val_if = $se_cond_corr[3];
if ($se_cond_corr[2] == "var") {
if (substr($val_if,-1) != ")") $val_if = $$val_if;
else {
$val_if = explode("(",substr($val_if,0,-1));
$val_if = ${$val_if[0]}[${$val_if[1]}];
} # fine else if (substr($val_if,-1) != ")")
} # fine if ($se_cond_corr[2] == "var")
$cond_verificata = 0;
if (($se_cond_corr[1] == "=" and $var_if == $val_if) or ($se_cond_corr[1] == "!=" and $var_if != $val_if) or ($se_cond_corr[1] == ">" and $var_if > $val_if) or ($se_cond_corr[1] == "<" and $var_if < $val_if)) $cond_verificata = 1;
if (($se_cond_corr[1] == "{}" and str_replace(strtolower($val_if),"",strtolower($var_if)) != strtolower($var_if)) or ($se_cond_corr[1] == "{A}" and str_replace($val_if,"",$var_if) != $var_if)) $cond_verificata = 1;
if ($condizione[0] == "or" and $cond_verificata) break;
if ($condizione[0] == "and" and !$cond_verificata) break;
} # fine for $num2
} # fine if ($condizione)

if ($cond_verificata) {

if ($azione[0] == "set") {
$val_then = $azione[4];
if ($azione[3] == "var") {
if (substr($val_then,-1) != ")") {
if ($var_predef_data[$val_then] and $val_then != "data_inizio_selezione" and $val_then != "data_fine_selezione" and $val_then != "oggi") $val_then = formatta_data_contr($$val_then,$stile_data);
else $val_then = $$val_then;
} # fine if (substr($val_then,-1) != ")")
else {
$val_then = explode("(",substr($val_then,0,-1));
$val_then = ${$val_then[0]}[${$val_then[1]}];
} # fine else if (substr($val_then,-1) != ")")
if ($azione[9] == "low") $val_then = strtolower($val_then);
if ($azione[9] == "upp") $val_then = strtoupper($val_then);
if ($azione[9] == "url" and function_exists('urlencode')) $val_then = urlencode($val_then);
if ($azione[9] == "asc") $val_then = conv_ascii($val_then);
if ($azione[9] == "eas") $val_then = conv_ascii($val_then,"e");
if ($azione[9] == "md5") $val_then = md5($val_then);
} # fine if ($azione[3] == "var")
if (strcmp($azione[6],"")) {
$txt_sost1 = $azione[6];
if ($azione[5] == "var") {
if (substr($txt_sost1,-1) != ")") $txt_sost1 = $$txt_sost1;
else {
$txt_sost1 = explode("(",substr($txt_sost1,0,-1));
$txt_sost1 = ${$txt_sost1[0]}[${$txt_sost1[1]}];
} # fine else if (substr($txt_sost1,-1) != ")")
} # fine if ($azione[5] == "var")
$txt_sost2 = $azione[8];
if ($azione[7] == "var") {
if (substr($txt_sost2,-1) != ")") $txt_sost2 = $$txt_sost2;
else {
$txt_sost2 = explode("(",substr($txt_sost2,0,-1));
$txt_sost2 = ${$txt_sost2[0]}[${$txt_sost2[1]}];
} # fine else if (substr($txt_sost2,-1) != ")")
} # fine if ($azione[7] == "var")
$val_then = str_replace($txt_sost1,$txt_sost2,$val_then);
} # fine if (strcmp($azione[6],""))
if ($azione[2] == ".=") {
if (substr($azione[1],0,1) != "a") $var_then_orig = ${$variabile[$azione[1]]};
else $var_then_orig = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
} # fine if ($azione[2] == ".=")
else $var_then_orig = "";
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = $var_then_orig.$val_then;
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_then_orig.$val_then;
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($azione[0] == "set")

if ($azione[0] == "trunc") {
if (substr($azione[1],0,1) != "a") $var_da_assegnare = ${$variabile[$azione[1]]};
else $var_da_assegnare = ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}];
if (strcmp($azione[3],"")) {
while (num_caratteri_testo($var_da_assegnare) < $azione[2]) {
if ($azione[4] == "ini") $var_da_assegnare = $azione[3].$var_da_assegnare;
if ($azione[4] == "fin") $var_da_assegnare .= $azione[3];
} # fine while (num_caratteri_testo($var_da_assegnare) < $azione[2])
} # fine if (strcmp($azione[3],""))
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = tronca_testo($var_da_assegnare,0,$azione[2]);
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = tronca_testo($var_da_assegnare,0,$azione[2]);
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($azione[0] == "trunc")

if ($azione[0] == "oper") {
$cont_oper = "SI";
$var_con_punti = "NO";
$var_da_oper = $azione[2];
if (substr($var_da_oper,-1) != ")") {
if (substr($var_da_oper,-2) != "_p" or !isset(${substr($var_da_oper,0,-2)})) $var_da_oper = ${$var_da_oper};
else $var_da_oper = ${substr($var_da_oper,0,-2)};
} # fine if (substr($var_da_oper,-1) != ")")
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
if (substr($var_da_oper[0],-2) != "_p" or !isset(${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}])) $var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
else $var_da_oper = ${substr($var_da_oper[0],0,-2)}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = formatta_soldi($var_da_oper);
if (controlla_soldi($var_da_oper) == "NO") $cont_oper = "NO";
$var_da_oper2 = $azione[5];
if ($azione[4] == "var") {
if (substr($var_da_oper2,-1) != ")") {
if (substr($var_da_oper2,-2) != "_p" or !isset(${substr($var_da_oper2,0,-2)})) $var_da_oper2 = ${$var_da_oper2};
else $var_da_oper2 = ${substr($var_da_oper2,0,-2)};
} # fine if (substr($var_da_oper2,-1) != ")")
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
if (substr($var_da_oper2[0],-2) != "_p" or !isset(${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}])) $var_da_oper2 = ${$var_da_oper2[0]}[${$var_da_oper2[1]}];
else $var_da_oper2 = ${substr($var_da_oper2[0],0,-2)}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
} # fine if ($azione[4] == "var")
$var_da_oper2 = formatta_soldi($var_da_oper2);
if (controlla_soldi($var_da_oper2) == "NO") $cont_oper = "NO";
if ($cont_oper != "NO") {
if ($azione[3] == "+") $var_da_assegnare = (double) $var_da_oper + (double) $var_da_oper2;
if ($azione[3] == "-") $var_da_assegnare = (double) $var_da_oper - (double) $var_da_oper2;
if ($azione[3] == "*") $var_da_assegnare = (double) $var_da_oper * (double) $var_da_oper2;
if ($azione[3] == "/") $var_da_assegnare = @((double) $var_da_oper / (double) $var_da_oper2);
if ($azione[6]) {
$var_da_assegnare = $var_da_assegnare / (double) $azione[6];
$var_da_assegnare = round($var_da_assegnare);
$var_da_assegnare = $var_da_assegnare * (double) $azione[6];
} # fine if ($azione[6])
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper != "NO")
} # fine if ($azione[0] == "oper")

if ($azione[0] == "date") {
$cont_oper = 1;
$var_da_oper = $azione[2];
if (substr($var_da_oper,-1) != ")") $var_da_oper = ${$var_da_oper};
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[3] == "gi") $var_da_assegnare = "d";
if ($azione[3] == "me") $var_da_assegnare = "m";
if ($azione[3] == "an") $var_da_assegnare = "Y";
if ($azione[3] == "gs") $var_da_assegnare = "w";
if ($azione[3] == "is") $var_da_assegnare = "Y-m-d";
if ($azione[3] == "da") {
if ($stile_data == "usa") $var_da_assegnare = "m-d-Y";
else $var_da_assegnare = "d-m-Y";
} # fine if ($azione[3] == "da")
$txt_sost1 = 0;
$num2 = 0;
$num3 = 0;
if ($azione[5] == "g") $txt_sost1 = $azione[4];
if ($azione[5] == "m") $num2 = $azione[4];
if ($azione[5] == "a") $num3 = $azione[4];
$var_da_assegnare = date($var_da_assegnare,mktime(0,0,0,(substr($var_da_oper,5,2) + $num2),(substr($var_da_oper,8,2) + $txt_sost1),(substr($var_da_oper,0,4) + $num3)));
if (substr($azione[1],0,1) != "a") ${$variabile[$azione[1]]} = $var_da_assegnare;
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
if ($azione[3] != "is") $array_date_contr[$array[substr($azione[1],1)]] = "";
${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "date")

if ($azione[0] == "opdat") {
$cont_oper = 1;
$var_da_oper = $azione[3];
if (substr($var_da_oper,-1) != ")") $var_da_oper = ${$var_da_oper};
else {
$var_da_oper = explode("(",substr($var_da_oper,0,-1));
$var_da_oper = ${$var_da_oper[0]}[${$var_da_oper[1]}];
} # fine else if (substr($var_da_oper,-1) != ")")
$var_da_oper = substr($var_da_oper,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper)) {
if ($stile_data == "usa") $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,0,2)."-".substr($var_da_oper,3,2);
else $var_da_oper = substr($var_da_oper,6,4)."-".substr($var_da_oper,3,2)."-".substr($var_da_oper,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper)) $cont_oper = 0;
$var_da_oper2 = $azione[4];
if (substr($var_da_oper2,-1) != ")") $var_da_oper2 = ${$var_da_oper2};
else {
$var_da_oper2 = explode("(",substr($var_da_oper2,0,-1));
$var_da_oper2 = ${$var_da_oper2[0]}[${$var_da_oper2[1]}];
} # fine else if (substr($var_da_oper2,-1) != ")")
$var_da_oper2 = substr($var_da_oper2,0,10);
if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2)) {
if ($stile_data == "usa") $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,0,2)."-".substr($var_da_oper2,3,2);
else $var_da_oper2 = substr($var_da_oper2,6,4)."-".substr($var_da_oper2,3,2)."-".substr($var_da_oper2,0,2);
} # fine if (preg_match("/[0-9]{2,2}-[0-9]{2,2}-[0-9]{4,4}/",$var_da_oper2))
if (!preg_match("/[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}/",$var_da_oper2)) $cont_oper = 0;
if ($cont_oper) {
if ($azione[2] == "g") {
$var_da_assegnare = mktime(2,0,0,substr($var_da_oper2,5,2),substr($var_da_oper2,8,2),substr($var_da_oper2,0,4)) - mktime(0,0,0,substr($var_da_oper,5,2),substr($var_da_oper,8,2),substr($var_da_oper,0,4));
$var_da_assegnare = floor((double) $var_da_assegnare / 86400);
} # fine if ($azione[2] == "g")
else {
$txt_sost1 = (substr($var_da_oper2,5,2) - substr($var_da_oper,5,2));
$txt_sost2 = (substr($var_da_oper2,0,4) - substr($var_da_oper,0,4));
if (($txt_sost1 > 0 or $txt_sost2 > 0) and substr($var_da_oper2,8,2) < substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 - 1;
if (($txt_sost1 < 0 or $txt_sost2 < 0) and substr($var_da_oper2,8,2) > substr($var_da_oper,8,2)) $txt_sost1 = $txt_sost1 + 1;
if ($azione[2] == "m") $var_da_assegnare = ($txt_sost2 * 12) + $txt_sost1;
if ($azione[2] == "a") {
$var_da_assegnare = $txt_sost2;
if ($txt_sost2 > 0 and $txt_sost1 < 0) $var_da_assegnare = $txt_sost2 - 1;
if ($txt_sost2 < 0 and $txt_sost1 > 0) $var_da_assegnare = $txt_sost2 + 1;
} # fine if ($azione[2] == "a")
} # fine else if ($azione[2] == "g")
if (substr($azione[1],0,1) != "a") {
if (substr($variabile[$azione[1]],-2) != "_p") ${$variabile[$azione[1]]} = $var_da_assegnare;
else ${$variabile[$azione[1]]} = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine if (substr($azione[1],0,1) != "a")
elseif (strcmp(${$var_arr[substr($azione[1],1)]},"")) {
$array_date_contr[$array[substr($azione[1],1)]] = "";
if (substr($array[substr($azione[1],1)],-2) != "_p") ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = $var_da_assegnare;
else ${$array[substr($azione[1],1)]}[${$var_arr[substr($azione[1],1)]}] = punti_in_num($var_da_assegnare,$stile_soldi,2);
} # fine elseif (strcmp(${$var_arr[substr($azione[1],1)]},""))
} # fine if ($cont_oper)
} # fine if ($azione[0] == "opdat")

if ($azione[0] == "unset") {
unset(${$array[substr($azione[1],1)]});
$array_date_contr[$array[substr($azione[1],1)]] = "";
} # fine if ($azione[0] == "unset")

if ($azione[0] == "array") {
$nome_arr = $array[substr($azione[1],1)];
if ($azione[2] == "val") {
unset($$nome_arr);
$array_date_contr[$nome_arr] = "";
$lista_val = explode(",",$azione[3]);
$num_lista_val = count($lista_val);
for ($num2 = 1 ; $num2 <= $num_lista_val ; $num2++) ${$nome_arr}[$num2] = $lista_val[($num2 - 1)];
} # fine if ($azione[2] == "val")
if ($azione[2] == "dat" or $azione[2] == "dap") {
unset($$nome_arr);
if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or ($azione[2] == "dap" and $data_primo_arrivo and $data_ultima_partenza)) {
$array_date_contr[$nome_arr] = "SI";
if ($azione[2] == "dat") {
$data_corr_arr = $data_inizio_selezione_orig;
$txt_sost1 = $data_fine_selezione_orig;
} # fine if ($azione[2] == "dat")
if ($azione[2] == "dap") {
$data_corr_arr = $data_primo_arrivo;
$txt_sost1 = $data_ultima_partenza;
} # fine if ($azione[2] == "dap")
$num2 = 1;
${$nome_arr}[$num2] = $data_corr_arr;
while ($data_corr_arr != $txt_sost1) {
$num2++;
$data_corr_arr = date("Y-m-d",mktime(0,0,0,substr($data_corr_arr,5,2),(substr($data_corr_arr,8,2) + 1),substr($data_corr_arr,0,4)));
${$nome_arr}[$num2] = $data_corr_arr;
} # fine while ($data_corr_arr != $txt_sost1)
} # fine if (($azione[2] == "dat" and $data_inizio_selezione and $data_fine_selezione) or...
} # fine if ($azione[2] == "dat" or $azione[2] == "dap")
if ($azione[2] == "cop") {
$lista_val = ${$array[substr($azione[3],1)]};
$$nome_arr = $lista_val;
$array_date_contr[$nome_arr] = $array_date_contr[$array[substr($azione[3],1)]];
} # fine if ($azione[2] == "cop")
} # fine if ($azione[0] == "array")

if ($azione[0] == "break") {
if ($azione[1] == "cont") $break_cont = 1;
else break;
} # fine if ($azione[0] == "break")

if ($azione[0] == "cont") $break_cont = 0;

} # fine if ($cond_verificata)
} # fine for $num1


$contratto_corr = $contratto_parte2[$n_p2];
while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_corr)) {
$contr_vett = preg_split("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_corr,2);
$contr_parziale = substr($contratto_corr,strlen($contr_vett[0]));
$contratto_corr = $contr_vett[0];
$condizione = preg_split("/\" *\\]/",$contr_parziale,2);
$condizione = $condizione[0];
$val_if = preg_split("/ *!?= *\"/",preg_replace("/^\\[c +/","",$condizione));
$var_if = trim($val_if[0]);
$val_if = $val_if[1];
if (str_replace("(","",$var_if) != $var_if) {
$parti_arr = explode("(",substr($var_if,0,-1));
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = ${$parti_arr[1]};
$val_var_if = ${$parti_arr[0]}[$val_var_if];
} # fine if (str_replace("(","",$var_if) != $var_if)
else $val_var_if = $$var_if;
if (preg_match("/!= *\"/",$condizione)) $cond = "!=";
else $cond = "=";
$contr_parziale = substr($contr_parziale,(strlen($condizione) + 1));
while (substr($contr_parziale,0,1) == " ") $contr_parziale = substr($contr_parziale,1);
$contr_parziale = substr($contr_parziale,1);
$contr_vett = explode("[/c]",$contr_parziale,2);
$contr_parziale = substr($contr_parziale,(strlen($contr_vett[0]) + 4));
if ($cond == "=" and $val_var_if == $val_if) $contratto_corr .= $contr_vett[0];
if ($cond == "!=" and $val_var_if != $val_if) $contratto_corr .= $contr_vett[0];
$contratto_corr .= $contr_parziale;
} # fine while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[A-Za-z]+[A-Za-z0-9_]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_corr))


if ($data_inizio) $data_inizio = formatta_data_contr($data_inizio,$stile_data);
if ($data_fine) $data_fine = formatta_data_contr($data_fine,$stile_data);
if ($data_nascita) $data_nascita = formatta_data_contr($data_nascita,$stile_data);
if ($data_nascita_ospite) $data_nascita_ospite = formatta_data_contr($data_nascita_ospite,$stile_data);
if ($data_inserimento_costo_agg) $data_inserimento_costo_agg = formatta_data_contr($data_inserimento_costo_agg,$stile_data);
if ($data_pagamento) $data_pagamento = formatta_data_contr($data_pagamento,$stile_data);
if ($data_inserimento_prenotazione) $data_inserimento_prenotazione = formatta_data_contr($data_inserimento_prenotazione,$stile_data);
$data_inizio_selezione = $data_inizio_selezione_f;
$data_fine_selezione = $data_fine_selezione_f;
$oggi = $oggi_f;
$testo_quotato_email_richiesta = str_replace("<email_richiesta> ","<$email> ",$testo_quotato_email_richiesta_orig);

if (!$nome) $nome = "$tag_b"."____________________$tag_no_b";
if (!$soprannome) $soprannome = "$tag_b"."____________________$tag_no_b";
if (!$cognome) $cognome = "$tag_b"."_________________$tag_no_b";
if (!$data_nascita) $data_nascita = "$tag_b"."______________$tag_no_b";
if (!$documento) $documento = " $tag_b"."________________________________$tag_no_b";
if (!$nazione) $nazione = "$tag_b"."_____________$tag_no_b";
if (!$regione) $regione = "$tag_b"."_____________$tag_no_b";
if (!$citta) $citta = "$tag_b"."___________________$tag_no_b";
if (!$via and $lingua_mex) $via2 = "$fr_via $tag_b"."________________________________$tag_no_b";
else $via2 = $via;
if (!$via) $via = "$tag_b"."________________________________$tag_no_b";
if (!$numcivico) $numcivico = "$tag_b"."_____$tag_no_b";
if (!$telefono) $telefono = "$tag_b"."__________________$tag_no_b";
if (!$telefono2) $telefono2 = "$tag_b"."__________________$tag_no_b";
if (!$telefono3) $telefono3 = "$tag_b"."__________________$tag_no_b";
if (!$fax) $fax = "$tag_b"."__________________$tag_no_b";
if (!$email) $email = "$tag_b"."__________________$tag_no_b";
if (!$cap) $cap = "$tag_b"."________$tag_no_b";
if (!$codice_fiscale) $codice_fiscale = "$tag_b"."_____________$tag_no_b";
if (!$partita_iva) $partita_iva = "$tag_b"."_____________$tag_no_b";
if ($num_persone == "non specificato" or $num_persone == "") {
$num_persone = "$tag_b"."_____$tag_no_b";
$num_persone_tot = "$tag_b"."_____$tag_no_b";
} # fine if ($num_persone == "non specificato" or $num_persone == "")
if (!$costo_tot or !$caparra) {
$resto_caparra = "$tag_b"."_____________$tag_no_b";
$resto_caparra_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot or !$caparra)
if (!$caparra) {
$caparra = "$tag_b"."___________$tag_no_b";
$caparra_p = "$tag_b"."___________$tag_no_b";
} # fine if (!$caparra)
else $caparra_p = punti_in_num($caparra,$stile_soldi);
if (!$costo_tot or !$commissioni) {
$resto_commissioni = "$tag_b"."_____________$tag_no_b";
$resto_commissioni_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot or !$commissioni)
if (!$commissioni) {
$commissioni = "$tag_b"."___________$tag_no_b";
$commissioni_p = "$tag_b"."___________$tag_no_b";
} # fine if (!$commissioni)
else $commissioni_p = punti_in_num($commissioni,$stile_soldi);
if (!$costo_tot) {
$resto_da_pagare = "$tag_b"."_____________$tag_no_b";
$resto_da_pagare_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot)
if (!$data_inizio) $data_inizio = "$tag_b"."______________$tag_no_b";
if (!$data_fine) $data_fine = "$tag_b"."______________$tag_no_b";
if (!$num_periodi or $num_periodi == "?") $num_periodi = "$tag_b"."____$tag_no_b";
if ($orario_entrata_stimato) $orario_entrata_stimato = substr(str_replace("$data_inizio ","",formatta_data_contr($orario_entrata_stimato,$stile_data)),0,-3);
if (!$nome_tariffa) $nome_tariffa = "$tag_b"."____________________$tag_no_b";
if (!$costo_tariffa) {
$costo_tariffa = "$tag_b"."____________________$tag_no_b";
$costo_tariffa_p = "$tag_b"."____________________$tag_no_b";
} # fine if (!$costo_tariffa)
else $costo_tariffa_p = punti_in_num($costo_tariffa,$stile_soldi);
if (!$sconto) {
$sconto = "$tag_b"."____________$tag_no_b";
$sconto_p = "$tag_b"."____________$tag_no_b";
} # fine if (!$sconto)
else $sconto_p = punti_in_num($sconto,$stile_soldi);
if (!$commento) $commento = "$tag_b"."______________________$tag_no_b";
if (!$origine_prenotazione) $origine_prenotazione = "$tag_b"."_________________$tag_no_b";
if (!$unita_occupata) $unita_occupata = "$tag_b"."____$tag_no_b";
$appartamento = $unita_occupata;
$apartment = $unita_occupata;
$apartamento = $unita_occupata;
if (!$unita_assegnabili) $unita_assegnabili = "$tag_b"."_________________$tag_no_b";
$app_assegnabili = $unita_assegnabili;
$apartment_list = $unita_assegnabili;
$lista_apartamentos = $unita_assegnabili;
if (!$pagato) {
$pagato = "$tag_b"."_____________$tag_no_b";
$pagato_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$pagato)
else $pagato_p = punti_in_num($pagato,$stile_soldi);
if (!$costo_tot) {
$costo_tot = "$tag_b"."_____________$tag_no_b";
$costo_tot_p = "$tag_b"."_____________$tag_no_b";
} # fine if (!$costo_tot)
else $costo_tot_p = punti_in_num($costo_tot,$stile_soldi);
if (!$n_letti_agg) $n_letti_agg = "$tag_b"."____$tag_no_b";
if (!$numero_prenotazione) $numero_prenotazione = "$tag_b"."____$tag_no_b";
if (!$nome_ospite) $nome_ospite = "$tag_b"."____________________$tag_no_b";
if (!$soprannome_ospite) $soprannome_ospite = "$tag_b"."____________________$tag_no_b";
if (!$cognome_ospite) $cognome_ospite = "$tag_b"."_________________$tag_no_b";
if (!$data_nascita_ospite) $data_nascita_ospite = "$tag_b"."______________$tag_no_b";
if (!$documento_ospite) $documento_ospite = " $tag_b"."________________________________$tag_no_b";
if (!$nazione_ospite) $nazione_ospite = "$tag_b"."_____________$tag_no_b";
if (!$regione_ospite) $regione_ospite = "$tag_b"."_____________$tag_no_b";
if (!$citta_ospite) $citta_ospite = "$tag_b"."___________________$tag_no_b";
if (!$via_ospite and $lingua_mex) $via2_ospite = "$fr_via $tag_b"."________________________________$tag_no_b";
else $via2_ospite = $via_ospite;
if (!$via_ospite) $via_ospite = "$tag_b"."________________________________$tag_no_b";
if (!$numcivico_ospite) $numcivico_ospite = "$tag_b"."_____$tag_no_b";
if (!$telefono_ospite) $telefono_ospite = "$tag_b"."__________________$tag_no_b";
if (!$telefono2_ospite) $telefono2_ospite = "$tag_b"."__________________$tag_no_b";
if (!$telefono3_ospite) $telefono3_ospite = "$tag_b"."__________________$tag_no_b";
if (!$fax_ospite) $fax_ospite = "$tag_b"."__________________$tag_no_b";
if (!$email_ospite) $email_ospite = "$tag_b"."__________________$tag_no_b";
if (!$cap_ospite) $cap_ospite = "$tag_b"."________$tag_no_b";
if (!$codice_fiscale_ospite) $codice_fiscale_ospite = "$tag_b"."_____________$tag_no_b";
if (!$partita_iva_ospite) $partita_iva_ospite = "$tag_b"."_____________$tag_no_b";


$contratto_r = $contratto_corr;
$contr_vett = explode("[",$contratto_r);
$num_contr_vett = count($contr_vett);
$contr_parziale = "";
for ($num1 = 0 ; $num1 < $num_contr_vett ; $num1++) {
$contr_parziale .= $contr_vett[$num1]."[";
$resto_contr = substr($contratto_corr,strlen($contr_parziale));
$lettera = (string) substr($resto_contr,0,1);
if ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "") {
$num2 = 1;
$lettere_var = $lettera;
$cond = 0;
while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and ($cond != 1 or ($lettera != "]" and $lettera != "("))) {
if ($lettera == "'") {
if (!$cond) $cond = 1;
else $cond = 2;
} # fine if ($lettera == "'")
$lettera = (string) substr($resto_contr,$num2,1);
if ($lettera == "]") {
if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var) {
if ($incr_np and $lettere_var == "numero_progressivo_documento") {
if ($num_prog_contr_max and $numero_progressivo_documento > $num_prog_contr_max) $numero_progressivo_documento--;
$val_if = strstr($contratto_r,"[numero_progressivo_documento]");
$contratto_r = substr($contratto_r,0,(strlen($val_if) * -1)).$numero_progressivo_documento.substr($val_if,30);
$numero_progressivo_documento++;
} # fine if ($incr_np and $lettere_var == "numero_progressivo_documento")
else $contratto_r = str_replace("[".$lettere_var."]",${$lettere_var},$contratto_r);
} # fine if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
else {
$parti_arr = explode("(",substr($lettere_var,0,-1));
if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and preg_replace("/(('[^']*')|([A-Za-z]+[A-Za-z0-9_]*))/","",$parti_arr[1]) == "") {
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = ${$parti_arr[1]};
if ($array_date_contr[$parti_arr[0]] == "SI") $contratto_r = str_replace("[".$lettere_var."]",formatta_data_contr(${$parti_arr[0]}[$val_var_if],$stile_data),$contratto_r);
else $contratto_r = str_replace("[".$lettere_var."]",${$parti_arr[0]}[$val_var_if],$contratto_r);
} # fine if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and...
} # fine else if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
} # fine if ($lettera == "]")
$lettere_var .= $lettera;
$num2++;
} # fine while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and...
} # fine ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "")
} # fine for $num1
$contratto_ripetizione .= $contratto_r;


$nome = $nome_orig;
$soprannome = $soprannome_orig;
$cognome = $cognome_orig;
$data_nascita = $data_nascita_orig;
$documento = $documento_orig;
$nazione = $nazione_orig;
$regione = $regione_orig;
$citta = $citta_orig;
$via = $via_orig;
$numcivico = $numcivico_orig;
$telefono = $telefono_orig;
$telefono2 = $telefono2_orig;
$telefono3 = $telefono3_orig;
$fax = $fax_orig;
$email = $email_orig;
$cap = $cap_orig;
$codice_fiscale = $codice_fiscale_orig;
$partita_iva = $partita_iva_orig;
$num_persone = $num_persone_orig;
$caparra = $caparra_orig;
$commissioni = $commissioni_orig;
$data_inizio = $data_inizio_orig;
$data_fine = $data_fine_orig;
$num_periodi = $num_periodi_orig;
$orario_entrata_stimato = $orario_entrata_stimato_orig;
$nome_tariffa = $nome_tariffa_orig;
$costo_tariffa = $costo_tariffa_orig;
$sconto = $sconto_orig;
$percentuale_tasse_tariffa = $percentuale_tasse_tariffa_orig;
$commento = $commento_orig;
$origine_prenotazione = $origine_prenotazione_orig;
$unita_occupata = $unita_occupata_orig;
$unita_assegnabili = $unita_assegnabili_orig;
$pagato = $pagato_orig;
$costo_tot = $costo_tot_orig;
$n_letti_agg = $n_letti_agg_orig;
$numero_prenotazione = $numero_prenotazione_orig;
$data_inizio_selezione = $data_inizio_selezione_orig;
$data_fine_selezione = $data_fine_selezione_orig;
$data_inserimento_prenotazione = $data_inserimento_prenotazione_orig;
$oggi = $oggi_orig;

$cognome_ospite = "";

} # fine if ($mostra_ripetizione)
} # fine for $n_r2
} # fine for $n_p2


if (!$errore_ripetizione) $contratto .= $apertura_rip_contr.$contratto_ripetizione.$chiusura_rip_contr;

if (!empty($filecontr) and $ripeti_tutto and !$messaggio_di_errore) {
if ($tipo_contratto == "contrrtf") {
$contratto = str_replace("&quot;","\"",$contratto);
$contratto = str_replace("&#039;","'",$contratto);
$contratto = str_replace("&lt;","<",$contratto);
$contratto = str_replace("&gt;",">",$contratto);
$contratto = str_replace("&amp;","&",$contratto);
$contratto = str_replace("ñ","\u241\'f1",$contratto);
$contratto = str_replace("à","\u224\'e0",$contratto);
$contratto = str_replace("è","\u232\'e8",$contratto);
$contratto = str_replace("ì","\u236\'ec",$contratto);
$contratto = str_replace("ò","\u242\'f2",$contratto);
$contratto = str_replace("ù","\u249\'f9",$contratto);
$contratto = str_replace("á","\u225\'e1",$contratto);
$contratto = str_replace("é","\u233\'e9",$contratto);
$contratto = str_replace("í","\u237\'ed",$contratto);
$contratto = str_replace("ó","\u243\'f3",$contratto);
$contratto = str_replace("ú","\u250\'fa",$contratto);
$contratto = str_replace("ä","\u228\'e4",$contratto);
$contratto = str_replace("ö","\u246\'f6",$contratto);
$contratto = str_replace("ü","\u252\'fc",$contratto);
$contratto = str_replace("ß","\u223\'df",$contratto);
$contratto = str_replace("ç","\u231\'e7",$contratto);
$contratto = str_replace("ã","\u227\'e3",$contratto);
$contratto = str_replace("õ","\u245\'f5",$contratto);
$contratto = str_replace("ø","\u248\'f8",$contratto);
$contratto = str_replace("€","\u8364\'80",$contratto);
$contratto = str_replace("°","\u176\'b0",$contratto);
$contratto = str_replace("’","\u8217\'92",$contratto);
$contratto = str_replace("Ñ","\u209\'d1",$contratto);
$contratto = str_replace("À","\u192\'c0",$contratto);
$contratto = str_replace("È","\u200\'c8",$contratto);
$contratto = str_replace("Ì","\u204\'cc",$contratto);
$contratto = str_replace("Ò","\u210\'d2",$contratto);
$contratto = str_replace("Ù","\u217\'d9",$contratto);
$contratto = str_replace("Á","\u193\'c1",$contratto);
$contratto = str_replace("É","\u201\'c9",$contratto);
$contratto = str_replace("Í","\u205\'cd",$contratto);
$contratto = str_replace("Ó","\u211\'d3",$contratto);
$contratto = str_replace("Ú","\u218\'da",$contratto);
$contratto = str_replace("Ä","\u196\'c4",$contratto);
$contratto = str_replace("Ö","\u214\'d6",$contratto);
$contratto = str_replace("Ü","\u220\'dc",$contratto);
$contratto = str_replace("Ç","\u199\'c7",$contratto);
$contratto = str_replace("Ã","\u195\'c3",$contratto);
$contratto = str_replace("Õ","\u213\'d5",$contratto);
$contratto = str_replace("Ø","\u216\'d8",$contratto);
$contratto = str_replace("Α","\u913\'91",$contratto);
$contratto = str_replace("α","\u945\'b1",$contratto);
$contratto = str_replace("Β","\u914\'92",$contratto);
$contratto = str_replace("β","\u946\'b2",$contratto);
$contratto = str_replace("Γ","\u915\'93",$contratto);
$contratto = str_replace("γ","\u947\'b3",$contratto);
$contratto = str_replace("Δ","\u916\'94",$contratto);
$contratto = str_replace("δ","\u948\'b4",$contratto);
$contratto = str_replace("Ε","\u917\'95",$contratto);
$contratto = str_replace("ε","\u949\'b5",$contratto);
$contratto = str_replace("Ζ","\u918\'96",$contratto);
$contratto = str_replace("ζ","\u950\'b6",$contratto);
$contratto = str_replace("Η","\u919\'97",$contratto);
$contratto = str_replace("η","\u951\'b7",$contratto);
$contratto = str_replace("Θ","\u920\'98",$contratto);
$contratto = str_replace("θ","\u952\'b8",$contratto);
$contratto = str_replace("Ι","\u921\'99",$contratto);
$contratto = str_replace("ι","\u953\'b9",$contratto);
$contratto = str_replace("Κ","\u922\'9a",$contratto);
$contratto = str_replace("κ","\u954\'ba",$contratto);
$contratto = str_replace("Λ","\u923\'9b",$contratto);
$contratto = str_replace("λ","\u955\'bb",$contratto);
$contratto = str_replace("Μ","\u924\'9c",$contratto);
$contratto = str_replace("μ","\u956\'bc",$contratto);
$contratto = str_replace("Ν","\u925\'9d",$contratto);
$contratto = str_replace("ν","\u957\'bd",$contratto);
$contratto = str_replace("Ξ","\u926\'9e",$contratto);
$contratto = str_replace("ξ","\u958\'be",$contratto);
$contratto = str_replace("Ο","\u927\'9f",$contratto);
$contratto = str_replace("ο","\u959\'bf",$contratto);
$contratto = str_replace("Π","\u928\'a0",$contratto);
$contratto = str_replace("π","\u960\'c0",$contratto);
$contratto = str_replace("Ρ","\u929\'a1",$contratto);
$contratto = str_replace("ρ","\u961\'c1",$contratto);
$contratto = str_replace("Σ","\u931\'a3",$contratto);
$contratto = str_replace("σ","\u963\'c3",$contratto);
$contratto = str_replace("ς","\u962\'c2",$contratto);
$contratto = str_replace("Τ","\u932\'a4",$contratto);
$contratto = str_replace("τ","\u964\'c4",$contratto);
$contratto = str_replace("Υ","\u933\'a5",$contratto);
$contratto = str_replace("υ","\u965\'c5",$contratto);
$contratto = str_replace("Φ","\u934\'a6",$contratto);
$contratto = str_replace("φ","\u966\'c6",$contratto);
$contratto = str_replace("Χ","\u935\'a7",$contratto);
$contratto = str_replace("χ","\u967\'c7",$contratto);
$contratto = str_replace("Ψ","\u936\'a8",$contratto);
$contratto = str_replace("ψ","\u968\'c8",$contratto);
$contratto = str_replace("Ω","\u937\'a9",$contratto);
$contratto = str_replace("ω","\u969\'c9",$contratto);
$contratto = str_replace("Ά","\u902\'86",$contratto);
$contratto = str_replace("ά","\u940\'ce",$contratto);
$contratto = str_replace("Ό","\u908\'8c",$contratto);
$contratto = str_replace("ό","\u972\'cf",$contratto);
$contratto = str_replace("Ή","\u905\'89",$contratto);
$contratto = str_replace("ή","\u942\'ce",$contratto);
$contratto = str_replace("Ί","\u906\'8a",$contratto);
$contratto = str_replace("ί","\u943\'ce",$contratto);
$contratto = str_replace("Ύ","\u910\'8e",$contratto);
$contratto = str_replace("ύ","\u973\'cf",$contratto);
$contratto = str_replace("Ώ","\u911\'8f",$contratto);
$contratto = str_replace("ώ","\u974\'cf",$contratto);
$contratto = str_replace("Έ","\u904\'88",$contratto);
$contratto = str_replace("έ","\u941\'ce",$contratto);
$contratto = str_replace("ý","\u253\'fd",$contratto);
$contratto = str_replace("ž","\u382\'9e",$contratto);
$contratto = str_replace("ř","\u345\'3f",$contratto);
$contratto = str_replace("č","\u269\'3f",$contratto);
$contratto = str_replace("š","\u353\'9a",$contratto);
$contratto = str_replace("ě","\u283\'3f",$contratto);
$contratto = str_replace("ů","\u367\'3f",$contratto);
$contratto = str_replace("Ý","\u221\'dd",$contratto);
$contratto = str_replace("Ž","\u381\'8e",$contratto);
$contratto = str_replace("Ř","\u344\'3f",$contratto);
$contratto = str_replace("Č","\u268\'3f",$contratto);
$contratto = str_replace("Š","\u352\'8a",$contratto);
$contratto = str_replace("Ě","\u282\'3f",$contratto);
$contratto = str_replace("Ů","\u366\'3f",$contratto);
} # fine if ($tipo_contratto == "contrrtf")

if ($incr_np) {
if ($numero_progressivo_documento > ($num_prog_contr[$n_r] + 1)) {
$val_if = $numero_progressivo_documento - 1;
for ($num1 = strlen($val_if) ; $num1 < 5 ; $num1++) $val_if = "0".$val_if;
$nome_file_contr[$n_r] = str_replace(" ","-$val_if",$nome_file_contr[$n_r]);
for ($num1 = ($n_r + 1) ; $num1 <= $num_ripeti ; $num1++) {
$val_if = $num_prog_contr[$num1] + $numero_progressivo_documento - 1 - $num_prog_contr[$n_r];
if (strlen($val_if) > strlen($num_prog_contr[$num1]) and strlen($val_if) <= 5) $num_prog_contr[$num1] = substr("00000",0,(strlen($val_if) - strlen($num_prog_contr[$num1]))).$num_prog_contr[$num1];
$nome_file_contr[$num1] = str_replace($num_prog_contr[$num1]." ",$val_if." ",$nome_file_contr[$num1]);
$num_prog_contr[$num1] = $val_if;
} # fine for $num1
} # fine if ($numero_progressivo_documento > ($num_prog_contr[$n_r] + 1))
else $nome_file_contr[$n_r] = str_replace(" ","",$nome_file_contr[$n_r]);
if ($nomi_contratti['compress'][$numero_contratto]) {
$nome_file_contr[$n_r] .= ".gz";
$lock_compress[$n_r] = crea_lock_file($dir_salva."/".$nome_file_contr[$n_r]);
$filecontr[$n_r] = gzopen($dir_salva."/".$nome_file_contr[$n_r],"wb9");
} # fine if ($nomi_contratti['compress'][$numero_contratto])
else {
$filecontr[$n_r] = fopen($dir_salva."/".$nome_file_contr[$n_r],"w+");
flock($filecontr[$n_r],2);
} # fine else if ($nomi_contratti['compress'][$numero_contratto])
} # fine if ($incr_np)

if ($nomi_contratti['compress'][$numero_contratto]) {
gzwrite($filecontr[$n_r],$contratto);
gzclose($filecontr[$n_r]);
distruggi_lock_file($lock_compress[$n_r],$dir_salva."/".$nome_file_contr[$n_r]);
} # fine if ($nomi_contratti['compress'][$numero_contratto])
else {
fwrite($filecontr[$n_r],$contratto);
flock($filecontr[$n_r],3);
fclose($filecontr[$n_r]);
} # fine else if ($nomi_contratti['compress'][$numero_contratto])
$contratto = "";
} # fine if (!empty($filecontr) and $ripeti_tutto and !$messaggio_di_errore)

} # fine if (!$ripeti_prenota_data or...
} # fine for $n_r

} # fine if ($ripeti_parte[$n_p] == "SI")


# Parti del documento non ripetute con le prenotazioni
else {

$costo_tot_somma_ripetizioni_p = punti_in_num($costo_tot_somma_ripetizioni,$stile_soldi);
$caparra_somma_ripetizioni_p = punti_in_num($caparra_somma_ripetizioni,$stile_soldi);
$resto_caparra_somma_ripetizioni_p = punti_in_num($resto_caparra_somma_ripetizioni,$stile_soldi);
$pagato_somma_ripetizioni_p = punti_in_num($pagato_somma_ripetizioni,$stile_soldi);
$resto_da_pagare_somma_ripetizioni_p = punti_in_num($resto_da_pagare_somma_ripetizioni,$stile_soldi);

if ($dir_salva and $ripeti_tutto and (!$numero_progressivo_documento or $numero_progressivo_documento < $num_prog_contr[$n_r])) $numero_progressivo_documento = $num_prog_contr[$n_r];


# Ripetizioni degli array all'interno delle parti non ripetute con le prenotazioni
unset($contratto_parte2);
if (!preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and str_replace("[r6]","",$contratto_parte[$n_p]) == $contratto_parte[$n_p]) {
$contratto_parte2[1] = $contratto_parte[$n_p];
$ripeti_parte2[1] = "NO";
$num_parti2_contr = 1;
} # fine if (!preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and...
else {
$num_parti2_contr = 0;
$contratto_restante = $contratto_parte[$n_p];
while (preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante) or str_replace("[r6]","",$contratto_restante) != $contratto_restante) {
$contr_vett4 = preg_split("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante);
$contr_vett6 = explode("[r6]",$contratto_restante);
$l0_cv4 = strlen($contr_vett4[0]);
$l0_cv6 = strlen($contr_vett6[0]);
if ($l0_cv4 > $l0_cv6) {
$contr_vett = $contr_vett6;
$tipo_contr_vett = 6;
} # fine if ($l0_cv4 > $l0_cv6)
else {
$contr_vett = $contr_vett4;
$tipo_contr_vett = 4;
} # fine else if ($l0_cv4 > $l0_cv6)
if ($contr_vett[0] != "") {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if ($contr_vett[0] != "")
$contratto_restante = substr($contratto_restante,strlen($contr_vett[0]));
if ($tipo_contr_vett == 4) {
$arr_ripeti = explode("\"]",$contratto_restante,2);
$arr_ripeti = str_replace("[r4 array=\"","",$arr_ripeti[0]);
$contratto_restante = substr($contratto_restante,strlen("[r4 array=\"$arr_ripeti\"]"));
} # fine if ($tipo_contr_vett == 4)
else $contratto_restante = substr($contratto_restante,4);
$contr_vett = explode("[/r".$tipo_contr_vett."]",$contratto_restante);
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contr_vett[0];
$ripeti_parte2[$num_parti2_contr] = "SI";
$tipo_parte2[$num_parti2_contr] = $tipo_contr_vett;
if ($tipo_contr_vett == 4) $arr_parte2[$num_parti2_contr] = $arr_ripeti;
$contratto_restante = substr($contratto_restante,(strlen($contr_vett[0]) + 5));
} # fine while (preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_restante) or...
if ($contratto_restante != "") {
$num_parti2_contr++;
$contratto_parte2[$num_parti2_contr] = $contratto_restante;
$ripeti_parte2[$num_parti2_contr] = "NO";
} # fine if ($contratto_restante != "")
} # fine else if (!preg_match("/\\[r4 array=\"[A-Za-z]+[A-Za-z0-9_]*\"\\]/",$contratto_parte[$n_p]) and...

for ($n_p2 = 1 ; $n_p2 <= $num_parti2_contr ; $n_p2++) {
if ($ripeti_parte2[$n_p2] != "SI") $num_ripeti2 = 1;
else {
if ($tipo_parte2[$n_p2] == 4) {
if (@is_array(${$arr_parte2[$n_p2]})) {
$num_ripeti2 = count(${$arr_parte2[$n_p2]});
reset(${$arr_parte2[$n_p2]});
} # fine if (@is_array(${$arr_parte2[$n_p2]}))
else $num_ripeti2 = 0;
} # fine if ($tipo_parte2[$n_p2] == 4)
if ($tipo_parte2[$n_p2] == 6) $num_ripeti2 = $num_unita;
} # fine else if ($ripeti_parte2[$n_p2] != "SI")

for ($n_r2 = 1 ; $n_r2 <= $num_ripeti2 ; $n_r2++) {

if ($ripeti_parte2[$n_p2] == "SI") {

if ($tipo_parte2[$n_p2] == 4) {
${$var_arr_nome[$arr_parte2[$n_p2]]} = key(${$arr_parte2[$n_p2]});
next(${$arr_parte2[$n_p2]});
} # fine if ($tipo_parte2[$n_p2] == 4)

if ($tipo_parte2[$n_p2] == 6) {
$nome_unita = $dati_app_contr[$n_r2]['nome'];
$casa_unita = $dati_app_contr[$n_r2]['casa'];
$piano_unita = $dati_app_contr[$n_r2]['piano'];
$capacita_unita = $dati_app_contr[$n_r2]['capacita'];
$priorita_unita = $dati_app_contr[$n_r2]['priorita'];
} # fine if ($tipo_parte2[$n_p2] == 6)
elseif ($tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
} # fine else if ($tipo_parte2[$n_p2] == 6)

} # fine if ($ripeti_parte2[$n_p2] == "SI")

elseif ($tipo_parte0[$n_p0] != "6") {
$nome_unita = "";
$casa_unita = "";
$piano_unita = "";
$capacita_unita = "";
$priorita_unita = "";
} # fine elseif ($tipo_parte0[$n_p0] != "6")


$contratto_corr = $contratto_parte2[$n_p2];
while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_corr)) {
$contr_vett = preg_split("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[^'\\]\\(]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_corr,2);
$contr_parziale = substr($contratto_corr,strlen($contr_vett[0]));
$contratto_corr = $contr_vett[0];
$condizione = preg_split("/\" *\\]/",$contr_parziale,2);
$condizione = $condizione[0];
$val_if = preg_split("/ *!?= *\"/",preg_replace("/^\\[c +/","",$condizione));
$var_if = trim($val_if[0]);
$val_if = $val_if[1];
if (str_replace("(","",$var_if) != $var_if) {
$parti_arr = explode("(",substr($var_if,0,-1));
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = ${$parti_arr[1]};
$val_var_if = ${$parti_arr[0]}[$val_var_if];
} # fine if (str_replace("(","",$var_if) != $var_if)
else $val_var_if = $$var_if;
if (preg_match("/!= *\"/",$condizione)) $cond = "!=";
else $cond = "=";
$contr_parziale = substr($contr_parziale,(strlen($condizione) + 1));
while (substr($contr_parziale,0,1) == " ") $contr_parziale = substr($contr_parziale,1);
$contr_parziale = substr($contr_parziale,1);
$contr_vett = explode("[/c]",$contr_parziale,2);
$contr_parziale = substr($contr_parziale,(strlen($contr_vett[0]) + 4));
if ($cond == "=" and $val_var_if == $val_if) $contratto_corr .= $contr_vett[0];
if ($cond == "!=" and $val_var_if != $val_if) $contratto_corr .= $contr_vett[0];
$contratto_corr .= $contr_parziale;
} # fine while (preg_match("/\\[c +[A-Za-z]+[A-Za-z0-9_]*(\\((('[A-Za-z]+[A-Za-z0-9_]*')|([A-Za-z]+[A-Za-z0-9_]*))\\))? *!?= *\"[^\"]*\" *\\]/",$contratto_corr))


$data_inizio_selezione = $data_inizio_selezione_f;
$data_fine_selezione = $data_fine_selezione_f;
$oggi = $oggi_f;
$testo_quotato_email_richiesta = str_replace("<email_richiesta> ","<$email> ",$testo_quotato_email_richiesta_orig);


$contratto_r = $contratto_corr;
$contr_vett = explode("[",$contratto_r);
$num_contr_vett = count($contr_vett);
$contr_parziale = "";
for ($num1 = 0 ; $num1 < $num_contr_vett ; $num1++) {
$contr_parziale .= $contr_vett[$num1]."[";
$resto_contr = substr($contratto_corr,strlen($contr_parziale));
$lettera = (string) substr($resto_contr,0,1);
if ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "") {
$num2 = 1;
$lettere_var = $lettera;
$cond = 0;
while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and ($cond != 1 or ($lettera != "]" and $lettera != "("))) {
if ($lettera == "'") {
if (!$cond) $cond = 1;
else $cond = 2;
} # fine if ($lettera == "'")
$lettera = (string) substr($resto_contr,$num2,1);
if ($lettera == "]") {
if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var) {
if ($incr_np and $lettere_var == "numero_progressivo_documento") {
if ($num_prog_contr_max and $numero_progressivo_documento > $num_prog_contr_max) $numero_progressivo_documento--;
$val_if = strstr($contratto_r,"[numero_progressivo_documento]");
$contratto_r = substr($contratto_r,0,(strlen($val_if) * -1)).$numero_progressivo_documento.substr($val_if,30);
$numero_progressivo_documento++;
} # fine if ($incr_np and $lettere_var == "numero_progressivo_documento")
else $contratto_r = str_replace("[".$lettere_var."]",${$lettere_var},$contratto_r);
} # fine if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
else {
$parti_arr = explode("(",substr($lettere_var,0,-1));
if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and preg_replace("/(('[^']*')|([A-Za-z]+[A-Za-z0-9_]*))/","",$parti_arr[1]) == "") {
if (substr($parti_arr[1],0,1) == "'") $val_var_if = substr($parti_arr[1],1,-1);
else $val_var_if = ${$parti_arr[1]};
if ($array_date_contr[$parti_arr[0]] == "SI") $contratto_r = str_replace("[".$lettere_var."]",formatta_data_contr(${$parti_arr[0]}[$val_var_if],$stile_data),$contratto_r);
else $contratto_r = str_replace("[".$lettere_var."]",${$parti_arr[0]}[$val_var_if],$contratto_r);
} # fine if (preg_replace("/[A-Za-z]+[A-Za-z0-9_]*/","",$parti_arr[0]) == "" and...
} # fine else if (str_replace("(","",str_replace(")","",str_replace("'","",$lettere_var))) == $lettere_var)
} # fine if ($lettera == "]")
$lettere_var .= $lettera;
$num2++;
} # fine while ((preg_replace("/[A-Za-z0-9\(\)'_]/","SI",$lettera) == "SI" or $cond == 1) and...
} # fine ($lettera and preg_replace("/[A-Za-z]/","",$lettera) == "")
} # fine for $num1
$contratto .= $contratto_r;


$data_inizio_selezione = $data_inizio_selezione_orig;
$data_fine_selezione = $data_fine_selezione_orig;
$oggi = $oggi_orig;

} # fine for $n_r2
} # fine for $n_p2

} # fine else if ($ripeti_parte[$n_p] == "SI")

} # fine for $n_p


} # fine for $n_r0
} # fine for $n_p0


if ($tipo_contratto == "contreml") {
if (!$ripeti_tutto) {
$contratto .= "</textarea><br>
</table><table><tr><td style=\"height: 3px;\"></td></tr></table>
<hr style=\"width: 95%; margin-left: 6px; text-align: left;\">";
} # fine if (!$ripeti_tutto)
$contratto .= "&nbsp;&nbsp;".bottone_submit_contr(mex("Spedisci",$pag),"inse","","snml")."
</div></form>";
} # fine if ($tipo_contratto == "contreml")

if ($tipo_contratto == "contrrtf") {
$contratto = str_replace("&quot;","\"",$contratto);
$contratto = str_replace("&#039;","'",$contratto);
$contratto = str_replace("&lt;","<",$contratto);
$contratto = str_replace("&gt;",">",$contratto);
$contratto = str_replace("&amp;","&",$contratto);
$contratto = str_replace("ñ","\u241\'f1",$contratto);
$contratto = str_replace("à","\u224\'e0",$contratto);
$contratto = str_replace("è","\u232\'e8",$contratto);
$contratto = str_replace("ì","\u236\'ec",$contratto);
$contratto = str_replace("ò","\u242\'f2",$contratto);
$contratto = str_replace("ù","\u249\'f9",$contratto);
$contratto = str_replace("á","\u225\'e1",$contratto);
$contratto = str_replace("é","\u233\'e9",$contratto);
$contratto = str_replace("í","\u237\'ed",$contratto);
$contratto = str_replace("ó","\u243\'f3",$contratto);
$contratto = str_replace("ú","\u250\'fa",$contratto);
$contratto = str_replace("ä","\u228\'e4",$contratto);
$contratto = str_replace("ö","\u246\'f6",$contratto);
$contratto = str_replace("ü","\u252\'fc",$contratto);
$contratto = str_replace("ß","\u223\'df",$contratto);
$contratto = str_replace("ç","\u231\'e7",$contratto);
$contratto = str_replace("ã","\u227\'e3",$contratto);
$contratto = str_replace("õ","\u245\'f5",$contratto);
$contratto = str_replace("ø","\u248\'f8",$contratto);
$contratto = str_replace("€","\u8364\'80",$contratto);
$contratto = str_replace("°","\u176\'b0",$contratto);
$contratto = str_replace("’","\u8217\'92",$contratto);
$contratto = str_replace("Ñ","\u209\'d1",$contratto);
$contratto = str_replace("À","\u192\'c0",$contratto);
$contratto = str_replace("È","\u200\'c8",$contratto);
$contratto = str_replace("Ì","\u204\'cc",$contratto);
$contratto = str_replace("Ò","\u210\'d2",$contratto);
$contratto = str_replace("Ù","\u217\'d9",$contratto);
$contratto = str_replace("Á","\u193\'c1",$contratto);
$contratto = str_replace("É","\u201\'c9",$contratto);
$contratto = str_replace("Í","\u205\'cd",$contratto);
$contratto = str_replace("Ó","\u211\'d3",$contratto);
$contratto = str_replace("Ú","\u218\'da",$contratto);
$contratto = str_replace("Ä","\u196\'c4",$contratto);
$contratto = str_replace("Ö","\u214\'d6",$contratto);
$contratto = str_replace("Ü","\u220\'dc",$contratto);
$contratto = str_replace("Ç","\u199\'c7",$contratto);
$contratto = str_replace("Ã","\u195\'c3",$contratto);
$contratto = str_replace("Õ","\u213\'d5",$contratto);
$contratto = str_replace("Ø","\u216\'d8",$contratto);
$contratto = str_replace("Α","\u913\'91",$contratto);
$contratto = str_replace("α","\u945\'b1",$contratto);
$contratto = str_replace("Β","\u914\'92",$contratto);
$contratto = str_replace("β","\u946\'b2",$contratto);
$contratto = str_replace("Γ","\u915\'93",$contratto);
$contratto = str_replace("γ","\u947\'b3",$contratto);
$contratto = str_replace("Δ","\u916\'94",$contratto);
$contratto = str_replace("δ","\u948\'b4",$contratto);
$contratto = str_replace("Ε","\u917\'95",$contratto);
$contratto = str_replace("ε","\u949\'b5",$contratto);
$contratto = str_replace("Ζ","\u918\'96",$contratto);
$contratto = str_replace("ζ","\u950\'b6",$contratto);
$contratto = str_replace("Η","\u919\'97",$contratto);
$contratto = str_replace("η","\u951\'b7",$contratto);
$contratto = str_replace("Θ","\u920\'98",$contratto);
$contratto = str_replace("θ","\u952\'b8",$contratto);
$contratto = str_replace("Ι","\u921\'99",$contratto);
$contratto = str_replace("ι","\u953\'b9",$contratto);
$contratto = str_replace("Κ","\u922\'9a",$contratto);
$contratto = str_replace("κ","\u954\'ba",$contratto);
$contratto = str_replace("Λ","\u923\'9b",$contratto);
$contratto = str_replace("λ","\u955\'bb",$contratto);
$contratto = str_replace("Μ","\u924\'9c",$contratto);
$contratto = str_replace("μ","\u956\'bc",$contratto);
$contratto = str_replace("Ν","\u925\'9d",$contratto);
$contratto = str_replace("ν","\u957\'bd",$contratto);
$contratto = str_replace("Ξ","\u926\'9e",$contratto);
$contratto = str_replace("ξ","\u958\'be",$contratto);
$contratto = str_replace("Ο","\u927\'9f",$contratto);
$contratto = str_replace("ο","\u959\'bf",$contratto);
$contratto = str_replace("Π","\u928\'a0",$contratto);
$contratto = str_replace("π","\u960\'c0",$contratto);
$contratto = str_replace("Ρ","\u929\'a1",$contratto);
$contratto = str_replace("ρ","\u961\'c1",$contratto);
$contratto = str_replace("Σ","\u931\'a3",$contratto);
$contratto = str_replace("σ","\u963\'c3",$contratto);
$contratto = str_replace("ς","\u962\'c2",$contratto);
$contratto = str_replace("Τ","\u932\'a4",$contratto);
$contratto = str_replace("τ","\u964\'c4",$contratto);
$contratto = str_replace("Υ","\u933\'a5",$contratto);
$contratto = str_replace("υ","\u965\'c5",$contratto);
$contratto = str_replace("Φ","\u934\'a6",$contratto);
$contratto = str_replace("φ","\u966\'c6",$contratto);
$contratto = str_replace("Χ","\u935\'a7",$contratto);
$contratto = str_replace("χ","\u967\'c7",$contratto);
$contratto = str_replace("Ψ","\u936\'a8",$contratto);
$contratto = str_replace("ψ","\u968\'c8",$contratto);
$contratto = str_replace("Ω","\u937\'a9",$contratto);
$contratto = str_replace("ω","\u969\'c9",$contratto);
$contratto = str_replace("Ά","\u902\'86",$contratto);
$contratto = str_replace("ά","\u940\'ce",$contratto);
$contratto = str_replace("Ό","\u908\'8c",$contratto);
$contratto = str_replace("ό","\u972\'cf",$contratto);
$contratto = str_replace("Ή","\u905\'89",$contratto);
$contratto = str_replace("ή","\u942\'ce",$contratto);
$contratto = str_replace("Ί","\u906\'8a",$contratto);
$contratto = str_replace("ί","\u943\'ce",$contratto);
$contratto = str_replace("Ύ","\u910\'8e",$contratto);
$contratto = str_replace("ύ","\u973\'cf",$contratto);
$contratto = str_replace("Ώ","\u911\'8f",$contratto);
$contratto = str_replace("ώ","\u974\'cf",$contratto);
$contratto = str_replace("Έ","\u904\'88",$contratto);
$contratto = str_replace("έ","\u941\'ce",$contratto);
$contratto = str_replace("ý","\u253\'fd",$contratto);
$contratto = str_replace("ž","\u382\'9e",$contratto);
$contratto = str_replace("ř","\u345\'3f",$contratto);
$contratto = str_replace("č","\u269\'3f",$contratto);
$contratto = str_replace("š","\u353\'9a",$contratto);
$contratto = str_replace("ě","\u283\'3f",$contratto);
$contratto = str_replace("ů","\u367\'3f",$contratto);
$contratto = str_replace("Ý","\u221\'dd",$contratto);
$contratto = str_replace("Ž","\u381\'8e",$contratto);
$contratto = str_replace("Ř","\u344\'3f",$contratto);
$contratto = str_replace("Č","\u268\'3f",$contratto);
$contratto = str_replace("Š","\u352\'8a",$contratto);
$contratto = str_replace("Ě","\u282\'3f",$contratto);
$contratto = str_replace("Ů","\u366\'3f",$contratto);
} # fine if ($tipo_contratto == "contrrtf")


if (!empty($filecontr) and !$ripeti_tutto and !$messaggio_di_errore) {
if ($incr_np) {
if ($numero_progressivo_documento > ($num_prog_contr[1] + 1)) {
$val_if = $numero_progressivo_documento - 1;
for ($num1 = strlen($val_if) ; $num1 < 5 ; $num1++) $val_if = "0".$val_if;
$nome_file_contr[1] = str_replace(" ","-$val_if",$nome_file_contr[1]);
} # fine if ($numero_progressivo_documento > ($num_prog_contr[1] + 1))
else $nome_file_contr[1] = str_replace(" ","",$nome_file_contr[1]);
if ($nomi_contratti['compress'][$numero_contratto]) {
$nome_file_contr[1] .= ".gz";
$lock_compress[1] = crea_lock_file($dir_salva."/".$nome_file_contr[1]);
$filecontr[1] = gzopen($dir_salva."/".$nome_file_contr[1],"wb9");
} # fine if ($nomi_contratti['compress'][$numero_contratto])
else {
$filecontr[1] = fopen($dir_salva."/".$nome_file_contr[1],"w+");
flock($filecontr[1],2);
} # fine else if ($nomi_contratti['compress'][$numero_contratto])
} # fine if ($incr_np)

if ($nomi_contratti['compress'][$numero_contratto]) {
gzwrite($filecontr[1],$contratto);
gzclose($filecontr[1]);
distruggi_lock_file($lock_compress[1],$dir_salva."/".$nome_file_contr[1]);
} # fine if ($nomi_contratti['compress'][$numero_contratto])
else {
fwrite($filecontr[1],$contratto);
flock($filecontr[1],3);
fclose($filecontr[1]);
} # fine else if ($nomi_contratti['compress'][$numero_contratto])
} # fine if (!empty($filecontr) and !$ripeti_tutto and !$messaggio_di_errore)

if ($incr_np) {
flock($filelock,3);
fclose($filelock);
unlink(C_DATI_PATH."/crea_contr.lock");
} # fine if ($incr_np)


return $contratto;

} # fine function crea_contratto





function crea_messaggio_contr_salva ($nome_file_contr,$num_file_salva,$num_contr_esist,$nome_file_contr_esist,$numero_contratto,$nomi_contratti,$dir_salva,$tipo_contratto,$num_ripeti,$origine,$origine_vecchia,$lista_var_form,$mostra_headers,$anno,$id_sessione,$id_utente,$tema,$tableversioni,$tabletransazioni,$pag) {
global $PHPR_TAB_PRE,$priv_cancella_contratti;

$nome_file_contr_orig = $nome_file_contr;
$num_file_salva_orig = $num_file_salva;
if ($num_contr_esist) {
$nome_file_contr = $nome_file_contr_esist;
$num_file_salva = count($nome_file_contr);
} # fine if ($num_contr_esist)
$tabelle_lock = array($tableversioni,$tabletransazioni);
$tabelle_lock = lock_tabelle($tabelle_lock);
$adesso = date("YmdHis",(time() + (C_DIFF_ORE * 3600)));
list($usec, $sec) = explode(' ',microtime());
mt_srand((float) $sec + ((float) $usec * 100000));
$val_casuale = mt_rand(100000,999999);
$versione_transazione = prendi_numero_versione($tableversioni);
$ultimo_accesso = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600)));
$id_transazione = $adesso.$val_casuale.$versione_transazione;
esegui_query("insert into $tabletransazioni (idtransazioni,idsessione,tipo_transazione,anno,dati_transazione1,dati_transazione2,dati_transazione3,dati_transazione4,dati_transazione5,ultimo_accesso) values ('$id_transazione','$id_sessione','con_s','$anno','".aggslashdb($dir_salva)."','".aggslashdb(serialize($nome_file_contr))."','$tipo_contratto','".aggslashdb($origine)."','".aggslashdb($origine_vecchia)."','$ultimo_accesso')");
unlock_tabelle($tabelle_lock);
if ($tipo_contratto == "contrrtf") $sec_aspetta = 2;
else $sec_aspetta = 6;
$url_reload = "./$pag?id_sessione=$id_sessione&amp;anno=$anno&amp;id_transazione=$id_transazione&amp;numero_contratto=$numero_contratto";
if ($num_file_salva == 1) $target = "";
else $target = " target=\"_blank\"";
if ($mostra_headers == "NO") {
if ($num_file_salva == 1) $extra_head = "<meta http-equiv=\"refresh\" content=\"$sec_aspetta; url=$url_reload&n_file=1\">
";
if ($tema[$id_utente] and $tema[$id_utente] != "base" and @is_dir("./themes/".$tema[$id_utente]."/php")) include("./themes/".$tema[$id_utente]."/php/head.php");
else include("./includes/head.php");
} # fine if ($mostra_headers == "NO")
if ($num_contr_esist and $num_file_salva_orig == 1) echo "<div style=\"line-height: 180%\"><br>";
else echo "<div style=\"line-height: 130%\"><br>";
if ($num_contr_esist) {
if ($num_ripeti > 1) echo mex("Documenti già esistenti riguardanti queste prenotazioni",$pag).":<br>";
else echo mex("Documenti già esistenti riguardanti questa prenotazione",$pag).":<br>";
} # fine if ($num_contr_esist)
for ($num1 = 1 ; $num1 <= $num_file_salva ; $num1++) {
if ($num_contr_esist and $num_file_salva_orig == 1) {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
$lista_var_form
<input type=\"hidden\" name=\"sovrascrivi\" value=\"".htmlspecialchars($nome_file_contr[$num1])."\">";
} # fine if ($num_contr_esist and $num_file_salva_orig == 1)
if (!$num_contr_esist) {
echo ucfirst(mex("documento",$pag))." ";
if ($nomi_contratti['salv'][$numero_contratto] != $nomi_contratti[$numero_contratto]) echo "\"".$nomi_contratti[$numero_contratto]."\" ";
echo mex("<span class=\"colblu\">salvato</span> come",$pag);
} # fine if (!$num_contr_esist)
echo " <b><a style=\"color: #000000;\" href=\"$url_reload&n_file=$num1\"$target>".$nome_file_contr[$num1]."</a></b>";
if ($num_contr_esist and $num_file_salva_orig == 1 and $priv_cancella_contratti != "n") {
echo " ".bottone_submit_contr(mex("Sovrascrivi",$pag),"","","xdoc")."
.</div></form>";
} # fine if ($num_contr_esist and $num_file_salva_orig == 1 and...
else echo ".<br>";
} # fine for $num1
echo "<br></div>";
if (!$num_contr_esist) {
if ($priv_cancella_contratti != "n") {
echo "<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
<input type=\"hidden\" name=\"anno\" value=\"$anno\">
<input type=\"hidden\" name=\"id_sessione\" value=\"$id_sessione\">
<input type=\"hidden\" name=\"numero_contratto\" value=\"$numero_contratto\">
<input type=\"hidden\" name=\"id_transazione\" value=\"$id_transazione\">
<input type=\"hidden\" name=\"cancella\" value=\"SI\">";
if ($num_file_salva == 1) echo "&nbsp;".bottone_submit_contr(mex("Cancella il documento",$pag),"","","cdoc");
else echo bottone_submit_contr(mex("Cancella i documenti",$pag),"","","cdoc");
echo "</div></form><br>";
} # fine if ($priv_cancella_contratti != "n")
} # fine if (!$num_contr_esist)
else {
echo "<br>
<form accept-charset=\"utf-8\" method=\"post\" action=\"$pag\"><div>
$lista_var_form
<input type=\"hidden\" name=\"cont_salva\" value=\"SI\">";
if ($num_file_salva_orig == 1) echo "&nbsp;".bottone_submit_contr(mex("Salva un nuovo documento",$pag),"","","adoc");
else echo bottone_submit_contr(mex("Salva dei nuovi documenti",$pag),"","","adoc");
echo "</div></form><br>";
} # fine else if (!$num_contr_esist)

} # fine function crea_messaggio_contr_salva





?>