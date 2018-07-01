<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2015 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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



function cancella_relutente ($num_rel,$id_utente_mod,$rel_sing,$rel_plur,$tablerel,$tablerelutenti,$rel_inf_sing = "",$rel_inf_plur = "",$tablerel_inf = "",$gia_canc = "",$relutente_usate = "",$nolog = "") {
if ($rel_inf_sing) esegui_query("update $tablerelutenti set idsup = NULL where idutente = '$id_utente_mod' and idsup = '$num_rel' and id$rel_inf_sing is not NULL ","",$nolog);
if (!$gia_canc) {
esegui_query("delete from $tablerelutenti where idutente = '$id_utente_mod' and id$rel_sing = '$num_rel'","",$nolog);
$relutente_usata = esegui_query("select idutente from $tablerelutenti where id$rel_sing = '$num_rel'");
$num_relutente_usata = numlin_query($relutente_usata);
} # fine if (!$gia_canc)
else $num_relutente_usata = $relutente_usate[$num_rel];
if (!$num_relutente_usata) esegui_query("delete from $tablerel where id$rel_plur = '$num_rel' ","",$nolog);
} # fine function cancella_relutente


function aggiorna_relutenti ($aggiungi_rel,$rel_predefinite,$elimina_tutte_rel,$importa_rel,$id_utente,$id_utente_mod,$nuova_rel,$sup_n_rel,$cod_n_rel,$cod2_n_rel,$cod3_n_rel,$utente_importa_rel,$pag,$rel_sing,$rel_plur,$tablerel,$tablerelutenti,$rel_sup_sing="",$rel_sup_plur="",$tablerel_sup="",$rel_inf_sing="",$rel_inf_plur="",$tablerel_inf="") {
if ($id_utente != 1 or $id_utente_mod == $utente_importa_rel) $importa_rel = "";
if ($aggiungi_rel or $rel_predefinite or $elimina_tutte_rel or $importa_rel) {
if (get_magic_quotes_gpc()) {
$nuova_rel = stripslashes($nuova_rel);
$cod_n_rel = stripslashes($cod_n_rel);
$cod2_n_rel = stripslashes($cod2_n_rel);
$cod3_n_rel = stripslashes($cod3_n_rel);
$sup_n_rel = stripslashes($sup_n_rel);
} # fine if (get_magic_quotes_gpc())
$nuova_rel = htmlspecialchars($nuova_rel);
$cod_n_rel = htmlspecialchars($cod_n_rel);
$cod2_n_rel = htmlspecialchars($cod2_n_rel);
$cod3_n_rel = htmlspecialchars($cod3_n_rel);
$sup_n_rel = htmlspecialchars($sup_n_rel);
if (!$aggiungi_rel or str_replace(" ","",$nuova_rel)) {
echo "<div id=\"avanz_$rel_plur\"><br>";
if ($tablerel_inf) $tabelle_lock = array($tablerel,$tablerel_inf,$tablerelutenti);
else $tabelle_lock = array($tablerel,$tablerelutenti);
if ($tablerel_sup) $altre_tab_lock = array($tablerel_sup);
else $altre_tab_lock = "";
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
unset($lista_rel);
unset($cod_rel);
unset($cod2_rel);
unset($cod3_rel);
unset($cod_rel_sup);
if ($aggiungi_rel) {
$lista_rel[0] = $nuova_rel;
$cod_rel[0] = $cod_n_rel;
$cod2_rel[0] = $cod2_n_rel;
$cod3_rel[0] = $cod3_n_rel;
if ($rel_sup_sing and $sup_n_rel) {
$sup_n_rel_esist = esegui_query("select distinct $tablerel_sup.id$rel_sup_plur from $tablerelutenti inner join $tablerel_sup on $tablerelutenti.id$rel_sup_sing = $tablerel_sup.id$rel_sup_plur where $tablerelutenti.idutente = '$id_utente_mod' and $tablerel_sup.nome_$rel_sup_sing = '".aggslashdb($sup_n_rel)."' ");
if (numlin_query($sup_n_rel_esist) > 0) $id_rel_sup_vett[0] = risul_query($sup_n_rel_esist,0,"id$rel_sup_plur",$tablerel_sup);
} # fine if ($rel_sup_sing and $sup_n_rel)
} # fine if ($aggiungi_rel)
else {
$rel_canc = esegui_query("select id$rel_sing from $tablerelutenti where idutente = '$id_utente_mod' and id$rel_sing is not NULL ");
$num_rel_canc = numlin_query($rel_canc);
esegui_query("delete from $tablerelutenti where idutente = '$id_utente_mod' and id$rel_sing is not NULL ");
$rel_usate = esegui_query("select distinct id$rel_sing from $tablerelutenti where id$rel_sing is not NULL");
$num_rel_usate = numlin_query($rel_usate);
for ($num1 = 0 ; $num1 < $num_rel_usate ; $num1++) {
$relutente_usate[(int) risul_query($rel_usate,$num1,"id$rel_sing")] = 1;
} # fine for $num1
for ($num1 = 0 ; $num1 < $num_rel_canc ; $num1++) {
if (substr($num1,-3) == "000") http_keep_alive(". ");
if ($num1 > 20) $nolog = 1;
else $nolog = 0;
cancella_relutente(risul_query($rel_canc,$num1,"id$rel_sing"),$id_utente_mod,$rel_sing,$rel_plur,$tablerel,$tablerelutenti,$rel_inf_sing,$rel_inf_plur,$tablerel_inf,"SI",$relutente_usate,$nolog);
} # fine for $num1
unset($rel_canc);
unset($rel_usate);
unset($relutente_usate);
if ($rel_predefinite) {
include(C_DATI_PATH."/lingua.php");
global ${"lista_".$rel_plur},${"cod_".$rel_plur},${"cod2_".$rel_plur},${"cod3_".$rel_plur},${"cod_".$rel_sup_sing."_".$rel_plur};
if ($lingua[$id_utente_mod] == "ita") include_once("./includes/lista_$rel_plur.php");
else if (@is_file("./includes/lang/".$lingua[$id_utente_mod]."/lista_$rel_plur.php")) include_once("./includes/lang/".$lingua[$id_utente_mod]."/lista_$rel_plur.php");
$lista_rel = ${"lista_".$rel_plur};
$cod_rel = ${"cod_".$rel_plur};
$cod2_rel = ${"cod2_".$rel_plur};
$cod3_rel = ${"cod3_".$rel_plur};
if ($rel_sup_sing) $cod_rel_sup = ${"cod_".$rel_sup_sing."_".$rel_plur};
if (@is_array($lista_rel)) $num_rel = count($lista_rel);
else $num_rel = 0;
for ($num1 = 0 ; $num1 < $num_rel ; $num1++) {
$lista_rel[$num1] = htmlspecialchars($lista_rel[$num1]);
$cod_rel[$num1] = htmlspecialchars($cod_rel[$num1]);
$cod2_rel[$num1] = htmlspecialchars($cod2_rel[$num1]);
$cod3_rel[$num1] = htmlspecialchars($cod3_rel[$num1]);
$cod_rel_sup[$num1] = htmlspecialchars($cod_rel_sup[$num1]);
} # fine for $num1
} # fine if ($rel_predefinite)
else if ($importa_rel) {
$utente_importa_rel = aggslashdb($utente_importa_rel);
$rel_ut_imp = esegui_query("select distinct $tablerel.nome_$rel_sing,$tablerelutenti.idsup from $tablerelutenti inner join $tablerel on $tablerelutenti.id$rel_sing = $tablerel.id$rel_plur where $tablerelutenti.idutente = '$utente_importa_rel' order by $tablerel.nome_$rel_sing");
$num_rel_ut_imp = numlin_query($rel_ut_imp);
for ($num1 = 0 ; $num1 < $num_rel_ut_imp ; $num1++) {
$rel = risul_query($rel_ut_imp,$num1,"nome_$rel_sing",$tablerel);
$lista_rel[$num1] = $rel;
if ($rel_sup_sing) $id_rel_sup_vett[$num1] = risul_query($rel_ut_imp,$num1,'idsup',$tablerelutenti);
} # fine for $num1
unset($rel_ut_imp);
} # fine if ($importa_rel)
} # fine else if ($aggiungi_rel)


$num_passa_a_var = 2;
if (@is_array($lista_rel)) $num_rel_agg = count($lista_rel);
else $num_rel_agg = 0;

if ($num_rel_agg >= $num_passa_a_var) {
$rel_esistenti = esegui_query("select id$rel_plur,nome_$rel_sing,codice_$rel_sing from $tablerel where nome_$rel_sing is not NULL ");
$num_rel_esistenti = numlin_query($rel_esistenti);
for ($num1 = 0 ; $num1 < $num_rel_esistenti ; $num1++) {
$nome_rel = risul_query($rel_esistenti,$num1,"nome_$rel_sing");
$rel_esistente[$nome_rel] = 1;
$id_esistente[$nome_rel] = risul_query($rel_esistenti,$num1,"id$rel_plur");
$cod_esistente[$nome_rel] = risul_query($rel_esistenti,$num1,"codice_$rel_sing");
} # fine for $num1
unset($rel_esistenti);
$rel_esistenti2 = esegui_query("select id$rel_sing,idsup from $tablerelutenti where idutente = '$id_utente_mod' and id$rel_sing is not NULL ");
$num_rel_esistenti2 = numlin_query($rel_esistenti2);
for ($num1 = 0 ; $num1 < $num_rel_esistenti2 ; $num1++) {
$id_rel = risul_query($rel_esistenti2,$num1,"id$rel_sing");
$rel_esistente2[$id_rel] = 1;
$idsup_esistente2[$id_rel] = risul_query($rel_esistenti2,$num1,'idsup');
} # fine for $num1
unset($rel_esistenti2);
} # fine if ($num_rel_agg > $num_passa_a_var)

unset($num_max);
for ($num1 = 0 ; $num1 < $num_rel_agg ; $num1++) {
if (substr($num1,-3) == "000") http_keep_alive(". ");

if ($num1 > 20) $nolog = 1;
else $nolog = 0;

$id_rel_sup = "";
if ($rel_sup_sing and $cod_rel_sup[$num1]) {
if ($cod_sup_esist[$cod_rel_sup[$num1]]) $id_rel_sup = $cod_sup_esist[$cod_rel_sup[$num1]];
else {
$rel_sup = esegui_query("select id$rel_sup_plur from $tablerel_sup where codice_$rel_sup_sing = '".aggslashdb($cod_rel_sup[$num1])."' ");
if (numlin_query($rel_sup) > 0) {
$id_rel_sup = risul_query($rel_sup,0,"id$rel_sup_plur");
$cod_sup_esist[$cod_rel_sup[$num1]] = $id_rel_sup;
} # fine if (numlin_query($rel_sup) > 0)
} # fine else if ($cod_sup_esistente[$cod_rel_sup[$num1]])
} # fine if ($rel_sup_sing and $cod_rel_sup[$num1])
if ($rel_sup_sing and $id_rel_sup_vett[$num1]) {
if ($id_sup_esist[$id_rel_sup_vett[$num1]]) $id_rel_sup = $id_sup_esist[$id_rel_sup_vett[$num1]];
else {
$rel_sup = esegui_query("select id$rel_sup_plur from $tablerel_sup where id$rel_sup_plur = '".aggslashdb($id_rel_sup_vett[$num1])."' ");
if (numlin_query($rel_sup) > 0) {
$id_rel_sup = $id_rel_sup_vett[$num1];
$id_sup_esist[$id_rel_sup_vett[$num1]] = $id_rel_sup;
} # fine if (numlin_query($rel_sup) > 0)
} # fine else if ($cod_sup_esistente[$cod_rel_sup[$num1]])
} # fine if ($rel_sup_sing and $id_rel_sup_vett[$num1])

if ($num_rel_agg < $num_passa_a_var) {
$rel_esistenti = esegui_query("select id$rel_plur,nome_$rel_sing,codice_$rel_sing from $tablerel where nome_$rel_sing = '".aggslashdb($lista_rel[$num1])."' ");
if (numlin_query($rel_esistenti)) {
$nome_rel = risul_query($rel_esistenti,0,"nome_$rel_sing");
$rel_esistente[$nome_rel] = 1;
$id_esistente[$nome_rel] = risul_query($rel_esistenti,0,"id$rel_plur");
$cod_esistente[$nome_rel] = risul_query($rel_esistenti,0,"codice_$rel_sing");
} # fine if (numlin_query($rel_esistenti))
} # fine if ($num_rel_agg <= $num_passa_a_var)

if (!$rel_esistente[$lista_rel[$num1]]) {
if (!$num_max) {
$num_max = esegui_query("select max(id$rel_plur) from $tablerel ");
$num_max = risul_query($num_max,0,0) + 1;
} # fine if (!$num_max)
else $num_max++;
$num_n_rel = $num_max;
esegui_query("insert into $tablerel (id$rel_plur,nome_$rel_sing,codice_$rel_sing,codice2_$rel_sing,codice3_$rel_sing) values ('$num_n_rel','".aggslashdb($lista_rel[$num1])."','".aggslashdb($cod_rel[$num1])."','".aggslashdb($cod2_rel[$num1])."','".aggslashdb($cod3_rel[$num1])."') ","",$nolog);
$rel_esistente[$lista_rel[$num1]] = 1;
$id_esistente[$lista_rel[$num1]] = $num_n_rel;
$cod_esistente[$lista_rel[$num1]] = $cod_rel[$num1];
} # fine if (!$rel_esistente[$lista_rel[$num1]])
else {
$num_n_rel = $id_esistente[$lista_rel[$num1]];
$r_cod = $cod_esistente[$lista_rel[$num1]];
if (!$r_cod and $cod_rel[$num1]) esegui_query("update $tablerel set codice_$rel_sing = '".aggslashdb($cod_rel[$num1])."' where id$rel_plur = '$num_n_rel' ");
if ($cod2_rel[$num1]) esegui_query("update $tablerel set codice2_$rel_sing = '".aggslashdb($cod2_rel[$num1])."' where id$rel_plur = '$num_n_rel' ");
if ($cod3_rel[$num1]) esegui_query("update $tablerel set codice3_$rel_sing = '".aggslashdb($cod3_rel[$num1])."' where id$rel_plur = '$num_n_rel' ");
if ($num_rel_agg < $num_passa_a_var) {
$rel_esistenti2 = esegui_query("select idsup from $tablerelutenti where idutente = '$id_utente_mod' and id$rel_sing = '$num_n_rel' ");
if (numlin_query($rel_esistenti2)) {
$rel_esistente2[$num_n_rel] = 1;
$idsup_esistente2[$num_n_rel] = risul_query($rel_esistenti2,0,'idsup');
} # fine if (numlin_query($rel_esistenti2))
} # fine if ($num_rel_agg < $num_passa_a_var)
} # fine else if (!$rel_esistente[$lista_rel[$num1]])

if (!$rel_esistente2[$num_n_rel]) {
if ($id_rel_sup) esegui_query("insert into $tablerelutenti (idutente,id$rel_sing,idsup) values ('$id_utente_mod','$num_n_rel','$id_rel_sup') ","",$nolog);
else esegui_query("insert into $tablerelutenti (idutente,id$rel_sing) values ('$id_utente_mod','$num_n_rel') ","",$nolog);
$rel_esistente2[$num_n_rel] = 1;
$idsup_esistente2[$num_n_rel] = $id_rel_sup;
} # fine (!$rel_esistente2[$num_n_rel])
elseif ($id_rel_sup) {
$idsup = $idsup_esistente2[$num_n_rel];
if (!$idsup) esegui_query("update $tablerelutenti set idsup = '$id_rel_sup' where idutente = '$id_utente_mod' and id$rel_sing = '$num_n_rel' ");
} # fine elseif ($id_rel_sup)
} # fine for $num1

unlock_tabelle($tabelle_lock);
echo "<br></div>
<script type=\"text/javascript\">
<!--
var avanz = document.getElementById('avanz_$rel_plur');
avanz.style.display = 'none';
-->
</script>
";
switch ($rel_sing) {
case "regione":
if ($aggiungi_rel and $pag) echo mex("Nuova regione/provincia aggiunta",$pag).".<br>";
if ($rel_predefinite and $pag) echo mex("Regioni/provincie predefinite ripristinate",$pag).".<br>";
if ($elimina_tutte_rel and $pag) echo mex("Regioni/provincie cancellate",$pag).".<br>";
if ($importa_rel and $pag) echo mex("Regioni/provincie importate",$pag).".<br>";
break;
case "documentoid":
if ($aggiungi_rel and $pag) echo mex("Nuovo tipo di documento di identità aggiunto",$pag).".<br>";
if ($rel_predefinite and $pag) echo mex("Tipi di documento di identità predefiniti ripristinati",$pag).".<br>";
if ($elimina_tutte_rel and $pag) echo mex("Tipi di documento di identità cancellati",$pag).".<br>";
if ($importa_rel and $pag) echo mex("Tipi di documento di identità importati",$pag).".<br>";
break;
case "citta":
$rel_sing = "città";
$rel_plur = "città";
default:
if ($aggiungi_rel and $pag) echo mex("Nuova $rel_sing aggiunta",$pag).".<br>";
if ($rel_predefinite and $pag) echo mex(ucfirst($rel_plur)." predefinite ripristinate",$pag).".<br>";
if ($elimina_tutte_rel and $pag) echo mex(ucfirst($rel_plur)." cancellate",$pag).".<br>";
if ($importa_rel and $pag) echo mex(ucfirst($rel_plur)." importate",$pag).".<br>";
} # fine switch ($rel_sing)

unset($lista_rel);
unset($cod_rel);
unset($cod2_rel);
unset($cod3_rel);
unset($cod_rel_sup);
unset($rel_esistente);
unset($id_esistente);
unset($cod_esistente);
unset($rel_esistente2);
unset($idsup_esistente2);
} # fine if (!$aggiungi_rel or str_replace(" ","",$nuova_rel))
} # fine if ($aggiungi_rel or $rel_predefinite or...
} # fine function aggiorna_relutenti




?>