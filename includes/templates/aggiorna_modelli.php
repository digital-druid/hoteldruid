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



if (function_exists('aggiorna_var_modello')) {


$pag_orig = $pag;
$pag = "crea_modelli.php";
include("./includes/templates/funzioni_modelli.php");
$modello_esistente = "SI";
$cambia_frasi = "NO";
include("./includes/templates/frasi_mod_disp.php");
include("./includes/templates/funzioni_mod_disp.php");
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/mdl_disponibilita.php")) {
$lingua_modello = "ita";
$nome_file = mex2("mdl_disponibilita",$pag,$lingua_modello).".php";
$num_periodi_date = "";
$anno_modello = "";
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno) {
$anno_modello = $anno_modello_presente;
$crea_modello = 1;
aggiorna_var_modello();
if ($crea_modello) crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno)
} # fine if (@is_file("$percorso_cartella_modello/mdl_disponibilita.php"))
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
include(C_DATI_PATH."/lingua.php");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
$nome_file = mex2("mdl_disponibilita",$pag,$ini_lingua).".php";
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = $ini_lingua;
$num_periodi_date = "";
$anno_modello = "";
recupera_var_modello_disponibilita($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno) {
$anno_modello = $anno_modello_presente;
$crea_modello = 1;
aggiorna_var_modello();
if ($crea_modello) crea_modello_disponibilita($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno)
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dig))
closedir($lang_dir);
$templates_dir = opendir("./includes/templates/");
while ($modello_ext = readdir($templates_dir)) {
if ($modello_ext != "." and $modello_ext != ".." and @is_dir("./includes/templates/$modello_ext")) {
include("./includes/templates/$modello_ext/name.php");
include("./includes/templates/$modello_ext/phrases.php");
include("./includes/templates/$modello_ext/functions.php");
$funz_recupera_var_modello = "recupera_var_modello_".$modello_ext;
$funz_crea_modello = "crea_modello_".$modello_ext;
$funz_mext = "mext_".$modello_ext;
if ($template_file_name['ita']) $nome_file = $template_file_name['ita'];
else $nome_file = "ita_".$template_file_name['en'];
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = "ita";
$num_periodi_date = "";
$anno_modello = "";
$funz_recupera_var_modello($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno) {
$anno_modello = $anno_modello_presente;
$crea_modello = 1;
aggiorna_var_modello();
if ($crea_modello) $funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno)
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
$lang_dir = opendir("./includes/lang/");
while ($ini_lingua = readdir($lang_dir)) {
if ($ini_lingua != "." && $ini_lingua != "..") {
if ($template_file_name[$ini_lingua]) $nome_file = $template_file_name[$ini_lingua];
else $nome_file = $ini_lingua."_".$template_file_name['en'];
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
if (@is_file("$percorso_cartella_modello/$nome_file")) {
$lingua_modello = $ini_lingua;
$num_periodi_date = "";
$anno_modello = "";
$funz_recupera_var_modello($nome_file,$percorso_cartella_modello,$pag,$fr_frase,$num_frasi,$var_mod,$num_var_mod,$tipo_periodi,"SI",$anno_modello,$PHPR_TAB_PRE);
if ($anno_modello_presente == $anno) {
$anno_modello = $anno_modello_presente;
$crea_modello = 1;
aggiorna_var_modello();
if ($crea_modello) $funz_crea_modello($percorso_cartella_modello,$anno_modello,$PHPR_TAB_PRE,$pag,$lingua_modello,"SI",$fr_frase,$frase,$num_frasi,$tipo_periodi);
} # fine if ($anno_modello_presente == $anno)
} # fine if (@is_file("$percorso_cartella_modello/$nome_file"))
} # fine for $num_cart
} # fine if ($file != "." && $file != "..")
} # fine while ($file = readdir($lang_dir))
closedir($lang_dir);
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($modello_ext = readdir($templates_dir))
closedir($templates_dir);
$pag = $pag_orig;


} # fine if (function_exists('aggiorna_var_modello'))


