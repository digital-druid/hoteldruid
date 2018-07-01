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





if (!$tablepersonalizza) $tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$percorso_cartella_modello = esegui_query("select valpersonalizza from $tablepersonalizza where idpersonalizza = 'percorso_cartella_modello' and idutente = '1'");
$percorso_cartella_modello = risul_query($percorso_cartella_modello,0,'valpersonalizza');
if (defined('C_CARTELLA_CREA_MODELLI') and C_CARTELLA_CREA_MODELLI != "") {
$c_cartella_crea_mod = C_CARTELLA_CREA_MODELLI;
if (substr($c_cartella_crea_mod,-1) == "/") $c_cartella_crea_mod = substr($c_cartella_crea_mod,0,-1);
if (substr($percorso_cartella_modello,0,strlen($c_cartella_crea_mod)) != $c_cartella_crea_mod) $percorso_cartella_modello = $c_cartella_crea_mod;
} # fine if (defined("C_CARTELLA_CREA_MODELLI") and C_CARTELLA_CREA_MODELLI != "")
else $c_cartella_crea_mod = "";
$perc_cart_mod_int = $percorso_cartella_modello;
$perc_cart_mod_vett = explode(",",$percorso_cartella_modello);
$num_perc_cart_mod_vett = count($perc_cart_mod_vett);
$vett_tmp = array();
$num_vett_tmp = 0;
for ($num_cart = 0 ; $num_cart < $num_perc_cart_mod_vett ; $num_cart++) {
if (!$c_cartella_crea_mod or substr($perc_cart_mod_vett[$num_cart]."/",0,strlen($c_cartella_crea_mod."/")) == $c_cartella_crea_mod."/") {
if (@is_dir($perc_cart_mod_vett[$num_cart])) {
$percorso_cartella_modello = $perc_cart_mod_vett[$num_cart];
$vett_tmp[$num_vett_tmp] = $percorso_cartella_modello;
$num_vett_tmp++;
if ($percorso_cartella_modello == $perc_cart_mod_sel) break;
} # fine if (@is_dir($perc_cart_mod_vett[$num_cart]))
} # fine if (!$c_cartella_crea_mod or...
} # fine for $num_cart
$perc_cart_mod_vett = $vett_tmp;
$num_perc_cart_mod_vett = $num_vett_tmp;



function mex2 ($messaggio,$pagina,$lingua) {

if ($lingua != "ita") {
include("./includes/lang/$lingua/$pagina");
} # fine if ($lingua != "ita")
elseif ($pagina == "unit.php") include("./includes/unit.php");
return $messaggio;

} # fine function mex2





function includi_file ($file_incluso,$file) {

if (defined('C_CARTELLA_FILES_REALI') and substr($file_incluso,0,(strlen(C_DATI_PATH) + 1)) != C_DATI_PATH."/") $linee_file = file(C_CARTELLA_FILES_REALI.$file_incluso);
else $linee_file = file($file_incluso);
fwrite($file,"
###########################################
###  INIZIO $file_incluso 
###########################################
");
for ($num1 = 0 ; $num1 < count($linee_file) ; $num1++) {
if (!preg_match("/^<\?/i",$linee_file[$num1]) and !preg_match("/^\?>/i",$linee_file[$num1])) fwrite($file,$linee_file[$num1]);
} # fine for $num1
fwrite($file,"
###########################################
###  FINE $file_incluso 
###########################################
");

} # fine function includi_file





function formatta_input_var_x_file ($input_utente) {

if (@get_magic_quotes_gpc()) $input_utente = stripslashes($input_utente);
$input_utente = str_replace("\\","\\\\",$input_utente);
$input_utente = str_replace("\"","\\\"",$input_utente);
$input_utente = str_replace("\\\\n","\\n",$input_utente);
return $input_utente;

} # fine function formatta_input_var_x_file





function trova_url_pagina ($nome_file,$percorso_cartella_modello,$pag) {

$url_pagina = "";
if (defined('C_URL_CREA_MODELLI') and (strtolower(substr(C_URL_CREA_MODELLI,0,6)) == "http:/" or strtolower(substr(C_URL_CREA_MODELLI,0,7)) == "https:/")) {
$url_pagina = C_URL_CREA_MODELLI;
if (substr($url_pagina,0,-1) != "/") $url_pagina .= "/";
$url_pagina .= $nome_file;
} # fine if (defined('C_URL_CREA_MODELLI') and (strtolower(substr(C_URL_CREA_MODELLI,0,6)) == "http:/" or...
else {

$url_dir = "";
global $PHP_SELF,$SERVER_NAME,$HTTP_SERVER_VARS,$HTTPS,$SERVER_PORT;
if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or @$HTTP_SERVER_VARS['SERVER_NAME']) {
if (@$PHP_SELF or @$_SERVER['PHP_SELF']) {
if ($_SERVER['SERVER_NAME']) $SERVER_NAME = $_SERVER['SERVER_NAME'];
elseif ($HTTP_SERVER_VARS['SERVER_NAME']) $SERVER_NAME = $HTTP_SERVER_VARS['SERVER_NAME'];
if ($_SERVER['PHP_SELF']) $PHP_SELF = $_SERVER['PHP_SELF'];
if ($HTTPS == "on" or $_SERVER['HTTPS'] == "on" or $SERVER_PORT == "443" or $_SERVER['SERVER_PORT'] == "443") $url_server = "https://".$SERVER_NAME;
else $url_server = "http://".$SERVER_NAME;
if (substr($PHP_SELF,0,1) != "/") $PHP_SELF = "/".$PHP_SELF;
$url_dir = $url_server.$PHP_SELF;
if (substr($url_dir,(strlen($pag) * -1)) == $pag) $url_dir = substr($url_dir,0,(strlen($pag) * -1));
if (substr($url_dir,-4) == ".php") {
$url_vett1 = explode("/",$url_dir);
$url_dir = substr($url_dir,0,(strlen($url_vett1[(count($url_vett1) - 1)]) * -1));
} # fine if (substr($url_dir,-4) == ".php")
} # fine if (@$PHP_SELF or @$_SERVER['PHP_SELF'])
} # fine if (@$SERVER_NAME or @$_SERVER['SERVER_NAME'] or...

if ($url_dir) {
if (defined('C_DOMINIO_CREA_MODELLI') and C_DOMINIO_CREA_MODELLI and defined('C_CARTELLA_CREA_MODELLI') and C_CARTELLA_CREA_MODELLI != "" and stristr($url_server,C_DOMINIO_CREA_MODELLI)) {
$c_cartella_crea_mod = C_CARTELLA_CREA_MODELLI;
if (substr($c_cartella_crea_mod,-1) == "/") $c_cartella_crea_mod = substr($c_cartella_crea_mod,0,-1);
$perc_cart_mod = substr($percorso_cartella_modello,strlen($c_cartella_crea_mod));
$url_pagina = $url_server.$perc_cart_mod."/".$nome_file;
} # fine if (defined('C_DOMINIO_CREA_MODELLI') and C_DOMINIO_CREA_MODELLI and..
else {
if (defined('C_URL_CREA_MODELLI') and C_URL_CREA_MODELLI) {
if (substr(C_URL_CREA_MODELLI,0,2) == "./" or substr(C_URL_CREA_MODELLI,0,3) == "../") $url_pagina = $url_dir.C_URL_CREA_MODELLI;
else $url_pagina = $url_server.C_URL_CREA_MODELLI;
if (defined('C_CARTELLA_CREA_MODELLI') and C_CARTELLA_CREA_MODELLI != "") {
$sub_cartella = C_CARTELLA_CREA_MODELLI;
if (substr($sub_cartella,-1) == "/") $sub_cartella = substr($sub_cartella,0,-1);
$sub_cartella = substr($percorso_cartella_modello,strlen($sub_cartella));
if (substr($sub_cartella,0,1) == "/") $sub_cartella = substr($sub_cartella,1);
if (substr($url_pagina,-1) != "/") $url_pagina .= "/";
$url_pagina .= $sub_cartella;
} # fine if (defined('C_CARTELLA_CREA_MODELLI') and C_CARTELLA_CREA_MODELLI != "")
if (substr($url_pagina,-1) != "/") $url_pagina .= "/";
$url_pagina .= $nome_file;
} # fine if (defined('C_URL_CREA_MODELLI') and C_URL_CREA_MODELLI)
else $url_pagina = "$url_dir$percorso_cartella_modello/$nome_file";
} # fine else if (defined('C_DOMINIO_CREA_MODELLI') and C_DOMINIO_CREA_MODELLI and..

$url_pagina = str_replace("/./","/",$url_pagina);
while (str_replace("/../","",$url_pagina) != $url_pagina) {
$url_vett1 = explode("/../",$url_pagina);
$url_vett2 = explode("/",$url_vett1[0]);
$prima_parte_url = substr($url_vett1[0],0,(strlen($url_vett2[(count($url_vett2) - 1)]) * -1));
$url_pagina = $prima_parte_url.substr($url_pagina,(strlen($url_vett1[0]) + 4));
} # fine while (str_replace("/../","",$url_pagina) != $url_pagina)
} # fine if ($url_dir)
} # fine else if (defined('C_URL_CREA_MODELLI') and strtolower(substr(C_URL_CREA_MODELLI,0,6)) == "http:/" or...

return $url_pagina;

} # fine function trova_url_pagina





?>