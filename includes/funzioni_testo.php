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



function num_caratteri_testo ($testo) {

return strlen(utf8_decode($testo));

} # fine function num_caratteri_testo



function tronca_testo ($testo,$inizio,$lunghezza = "NO") {

$num_caratteri = 0;
$num_byte = strlen($testo);
for ($num1 = 0 ; $num1 < $num_byte ; $num1++) {
$num_caratteri++;
$byte_car = 1;
$byte = ord($testo[$num1]);
if ($byte & 128) {
$byte = $byte << 1;
while ($byte & 128) {
$num1++;
$byte_car++;
$byte = $byte << 1;
} # fine while ($byte & 128)
} # fine if ($byte & 128)
$num_byte_car[$num_caratteri] = $byte_car;
} # fine for $num1

$n_ini = 0;
while ($inizio < 0) $inizio = $num_caratteri + $inizio;
for ($num1 = 1 ; $num1 <= $inizio ; $num1++) $n_ini = $n_ini + $num_byte_car[$num1];
if ($lunghezza == "NO") $testo = substr($testo,$n_ini);
else {
$n_lun = 0;
if ($lunghezza < 0) {
$lunghezza = $num_caratteri + $lunghezza - $inizio;
if ($lunghezza < 0) $lunghezza = 0;
} # fine if ($lunghezza < 0)
for ($num1 = ($inizio + 1) ; $num1 <= ($inizio + $lunghezza) ; $num1++) $n_lun = $n_lun + $num_byte_car[$num1];
$testo = substr($testo,$n_ini,$n_lun);
} # fine else if ($lunghezza == "NO")

return $testo;

} # fine function tronca_testo



function trova_prima_data ($testo,$stile_data,$lingua,&$lung_resto) {
global $lingua_mex;

$prima_data = "";
$lung_prima_data = strlen($testo);
$lung_resto = "";

if (preg_match("|[^0-9][0-9]{1,2}[-/ ][0-9]{1,2}[-/ ][0-9]{4,4}[^0-9]|",$testo)) {
$prima_data_vett = preg_split("|[0-9]{1,2}[-/ ][0-9]{1,2}[-/ ][0-9]{4,4}|",$testo);
$lung_prima_data_corr = strlen($prima_data_vett[0]);
if ($lung_prima_data_corr < $lung_prima_data) {
$lung_prima_data = $lung_prima_data_corr;
$prima_data = substr($testo,$lung_prima_data);
if ($prima_data_vett[1]) {
$prima_data = explode($prima_data_vett[1],$prima_data);
$prima_data = $prima_data[0];
} # fine if ($prima_data_vett[1])
$lung_resto = (int) $lung_prima_data + strlen($prima_data);
$prima_data_corr = preg_split("|[-/ ]|",$prima_data);
if (strlen($prima_data_corr[0]) < 2) $prima_data_corr[0] = "0".$prima_data_corr[0];
if (strlen($prima_data_corr[1]) < 2) $prima_data_corr[1] = "0".$prima_data_corr[1];
if ((integer) $prima_data_corr[0] > 12) $prima_data = $prima_data_corr[2]."-".$prima_data_corr[1]."-".$prima_data_corr[0];
else {
if ((integer) $prima_data_corr[1] > 12) $prima_data = $prima_data_corr[2]."-".$prima_data_corr[0]."-".$prima_data_corr[1];
else {
if ($stile_data == "usa") $prima_data = $prima_data_corr[2]."-".$prima_data_corr[0]."-".$prima_data_corr[1];
else $prima_data = $prima_data_corr[2]."-".$prima_data_corr[1]."-".$prima_data_corr[0];
} # fine else if ($prima_data_corr[1] > 12)
} # fine else if ($prima_data_corr[0] > 12)
} # fine if ($lung_prima_data_corr < $lung_prima_data)
} # fine if (preg_match("|[^0-9][0-9]{1,2}[-/ ][0-9]{1,2}[-/ ][0-9]{4,4}|",$testo))

if (preg_match("|[^0-9][0-9]{4,4}[-/ ][0-9]{1,2}[-/ ][0-9]{1,2}[^0-9]|",$testo)) {
$prima_data_vett = preg_split("|[0-9]{4,4}[-/ ][0-9]{1,2}[-/ ][0-9]{1,2}|",$testo);
$lung_prima_data_corr = strlen($prima_data_vett[0]);
if ($lung_prima_data_corr < $lung_prima_data) {
$lung_prima_data = $lung_prima_data_corr;
$prima_data = substr($testo,$lung_prima_data);
if ($prima_data_vett[1]) {
$prima_data = explode($prima_data_vett[1],$prima_data);
$prima_data = $prima_data[0];
} # fine if ($prima_data_vett[1])
$lung_resto = (int) $lung_prima_data + strlen($prima_data);
$prima_data_corr = preg_split("|[-/ ]|",$prima_data);
if (strlen($prima_data_corr[1]) < 2) $prima_data_corr[1] = "0".$prima_data_corr[1];
if (strlen($prima_data_corr[2]) < 2) $prima_data_corr[2] = "0".$prima_data_corr[2];
$prima_data = $prima_data_corr[0]."-".$prima_data_corr[1]."-".$prima_data_corr[2];
} # fine if ($lung_prima_data_corr < $lung_prima_data)
} # fine if (preg_match("|[^0-9][0-9]{4,4}[-/ ][0-9]{1,2}[-/ ][0-9]{1,2}[^0-9]|",$testo))

$lingua_orig = $lingua_mex;
$lingua_mex = $lingua;
$Gen = strtolower(mex("Gen",'giorni_mesi.php'));
$Feb = strtolower(mex("Feb",'giorni_mesi.php'));
$Mar = strtolower(mex("Mar",'giorni_mesi.php'));
$Apr = strtolower(mex("Apr",'giorni_mesi.php'));
$Mag = strtolower(mex("Mag",'giorni_mesi.php'));
$Giu = strtolower(mex("Giu",'giorni_mesi.php'));
$Lug = strtolower(mex("Lug",'giorni_mesi.php'));
$Ago = strtolower(mex("Ago",'giorni_mesi.php'));
$Set = strtolower(mex("Set",'giorni_mesi.php'));
$Ott = strtolower(mex("Ott",'giorni_mesi.php'));
$Nov = strtolower(mex("Nov",'giorni_mesi.php'));
$Dic = strtolower(mex("Dic",'giorni_mesi.php'));
$Gennaio = strtolower(mex("Gennaio",'giorni_mesi.php'));
$Febbraio = strtolower(mex("Febbraio",'giorni_mesi.php'));
$Marzo = strtolower(mex("Marzo",'giorni_mesi.php'));
$Aprile = strtolower(mex("Aprile",'giorni_mesi.php'));
$Maggio = strtolower(mex("Maggio",'giorni_mesi.php'));
$Giugno = strtolower(mex("Giugno",'giorni_mesi.php'));
$Luglio = strtolower(mex("Luglio",'giorni_mesi.php'));
$Agosto = strtolower(mex("Agosto",'giorni_mesi.php'));
$Settembre = strtolower(mex("Settembre",'giorni_mesi.php'));
$Ottobre = strtolower(mex("Ottobre",'giorni_mesi.php'));
$Novembre = strtolower(mex("Novembre",'giorni_mesi.php'));
$Dicembre = strtolower(mex("Dicembre",'giorni_mesi.php'));
$al = strtolower(mex("al",'prenota.php'));
$lingua_mex = $lingua_orig;
$mesi_alternativi = "$Gen|$Feb|$Mar|$Apr|$Mag|$Giu|$Lug|$Ago|$Set|$Ott|$Nov|$Dic|$Gennaio|$Febbraio|$Marzo|$Aprile|$Maggio|$Giugno|$Luglio|$Agosto|$Settembre|$Ottobre|$Novembre|$Dicembre";

if (preg_match("=[^0-9a-z]($mesi_alternativi)[, -/]+[0-9]{1,2}[, -/]+[0-9]{4,4}[^0-9a-z]=i",$testo)) {
$prima_data_vett = preg_split("=($mesi_alternativi)[, -/]+[0-9]{1,2}[, -/]+[0-9]{4,4}=i",$testo);
$lung_prima_data_corr = strlen($prima_data_vett[0]);
if ($lung_prima_data_corr < $lung_prima_data) {
$lung_prima_data = $lung_prima_data_corr;
$prima_data = substr($testo,$lung_prima_data);
if ($prima_data_vett[1]) {
$prima_data = explode($prima_data_vett[1],$prima_data);
$prima_data = $prima_data[0];
} # fine if ($prima_data_vett[1])
$lung_resto = (int) $lung_prima_data + strlen($prima_data);
$prima_data_corr = preg_split("|[, -/]+|",$prima_data);
$mese_corr = strtolower($prima_data_corr[0]);
if ($mese_corr == $Gen or $mese_corr == $Gennaio) $mese = "01";
if ($mese_corr == $Feb or $mese_corr == $Febbraio) $mese = "02";
if ($mese_corr == $Mar or $mese_corr == $Marzo) $mese = "03";
if ($mese_corr == $Apr or $mese_corr == $Aprile) $mese = "04";
if ($mese_corr == $Mag or $mese_corr == $Maggio) $mese = "05";
if ($mese_corr == $Giu or $mese_corr == $Giugno) $mese = "06";
if ($mese_corr == $Lug or $mese_corr == $Luglio) $mese = "07";
if ($mese_corr == $Ago or $mese_corr == $Agosto) $mese = "08";
if ($mese_corr == $Set or $mese_corr == $Settembre) $mese = "09";
if ($mese_corr == $Ott or $mese_corr == $Ottobre) $mese = "10";
if ($mese_corr == $Nov or $mese_corr == $Novembre) $mese = "11";
if ($mese_corr == $Dic or $mese_corr == $Dicembre) $mese = "12";
if (strlen($prima_data_corr[1]) < 2) $prima_data_corr[1] = "0".$prima_data_corr[1];
$prima_data = $prima_data_corr[2]."-".$mese."-".$prima_data_corr[1];
} # fine if ($lung_prima_data_corr < $lung_prima_data)
} # fine if (preg_match("=[^0-9a-z]($mesi_alternativi)[, -/]+[0-9]{1,2}[, -/]+[0-9]{4,4}[^0-9a-z]=i",$testo))

if (preg_match("=[^0-9a-z][0-9]{1,2}[, -/]+($mesi_alternativi)[, -/]+[0-9]{4,4}[^0-9a-z]=i",$testo)) {
$prima_data_vett = preg_split("=[^0-9a-z]{1,1}[0-9]{1,2}[, -/]+($mesi_alternativi)[, -/]+[0-9]{4,4}=i",$testo);
$lung_prima_data_corr = (strlen($prima_data_vett[0]) + 1);
if ($lung_prima_data_corr < $lung_prima_data) {
$lung_prima_data = $lung_prima_data_corr;
$prima_data = substr($testo,$lung_prima_data);
if ($prima_data_vett[1]) {
$prima_data = explode($prima_data_vett[1],$prima_data);
$prima_data = $prima_data[0];
} # fine if ($prima_data_vett[1])
$lung_resto = (int) $lung_prima_data + strlen($prima_data);
$prima_data_corr = preg_split("|[, -/]+|",$prima_data);
# Se le date sono nel formato 19-26 Agosto 2017 o simili
$prima_parte = str_replace("\n","",str_replace("\r","",$prima_data_vett[0]));
$prima_parte_no_data = preg_replace("= [0-9]{1,2} *(-|$al|/) *$=i"," ",$prima_parte);
if ($prima_parte != $prima_parte_no_data) {
$lung_prima_data = strlen($prima_parte_no_data);
$nuova_data = substr($prima_parte,$lung_prima_data);
if (preg_match("/[0-9]{2,2}/",substr($nuova_data,0,2))) $prima_data_corr[0] = substr($nuova_data,0,2);
else $prima_data_corr[0] = substr($nuova_data,0,1);
$lung_resto = strlen($prima_data_vett[0]);
} # fine if ($prima_parte != $prima_parte_no_data)
else {
# Se le date sono nel formato 26 Agosto - 2 Settempre 2017 o simili
$prima_parte_no_data = preg_replace("= [0-9]{1,2} *($mesi_alternativi) *(-|$al|/) *$=i"," ",$prima_parte);
if ($prima_parte != $prima_parte_no_data) {
$lung_prima_data = strlen($prima_parte_no_data);
$nuova_data = substr($prima_parte,$lung_prima_data);
if (preg_match("/[0-9]{2,2}/",substr($nuova_data,0,2))) $prima_data_corr[0] = substr($nuova_data,0,2);
else $prima_data_corr[0] = substr($nuova_data,0,1);
$nuova_data = preg_replace("=^[0-9]{1,2} *=i","",$nuova_data);
$prima_parte_no_data = preg_replace("=($mesi_alternativi)=i","",$nuova_data);
$prima_data_corr[1] = substr($nuova_data,0,(strlen($prima_parte_no_data) * -1));
$lung_resto = strlen($prima_data_vett[0]);
} # fine if ($prima_parte != $prima_parte_no_data)
} # fine else if ($prima_parte != $prima_parte_no_data)
$mese_corr = strtolower($prima_data_corr[1]);
if ($mese_corr == $Gen or $mese_corr == $Gennaio) $mese = "01";
if ($mese_corr == $Feb or $mese_corr == $Febbraio) $mese = "02";
if ($mese_corr == $Mar or $mese_corr == $Marzo) $mese = "03";
if ($mese_corr == $Apr or $mese_corr == $Aprile) $mese = "04";
if ($mese_corr == $Mag or $mese_corr == $Maggio) $mese = "05";
if ($mese_corr == $Giu or $mese_corr == $Giugno) $mese = "06";
if ($mese_corr == $Lug or $mese_corr == $Luglio) $mese = "07";
if ($mese_corr == $Ago or $mese_corr == $Agosto) $mese = "08";
if ($mese_corr == $Set or $mese_corr == $Settembre) $mese = "09";
if ($mese_corr == $Ott or $mese_corr == $Ottobre) $mese = "10";
if ($mese_corr == $Nov or $mese_corr == $Novembre) $mese = "11";
if ($mese_corr == $Dic or $mese_corr == $Dicembre) $mese = "12";
if (strlen($prima_data_corr[0]) < 2) $prima_data_corr[0] = "0".$prima_data_corr[0];
$prima_data = $prima_data_corr[2]."-".$mese."-".$prima_data_corr[0];
} # fine if ($lung_prima_data_corr < $lung_prima_data)
} # fine if (preg_match("=[^0-9a-z][0-9]{1,2}[, -/]+($mesi_alternativi)[, -/]+[0-9]{4,4}[^0-9a-z]=i",$testo))

return $prima_data;

} # fine function trova_prima_data



function trova_numero_vicino ($testo,$parola) {

$numero = 0;
$dist_dopo = 0;
$dist_prima = 0;
$testo_vett = preg_split("/$parola"."[^0-9a-z]/i",$testo);
if ($testo_vett[1]) {
$testo_vett[1] = " ".$testo_vett[1];
# Numero dopo la parola cercata
$num = preg_split("/[^0-9a-z][0-9]{1,2}[^0-9a-z]/i",substr($testo_vett[1],0,16));
if (count($num) > 1) {
$dist_dopo = (strlen($num[0]) + 1);
$numero = substr($testo_vett[1],$dist_dopo,1);
if (preg_match("/[0-9]/",substr($testo_vett[1],($dist_dopo + 1),1))) $numero .= substr($testo_vett[1],($dist_dopo + 1),1);
} # fine if (count($num) > 1)
# Numero prima della parola cercata
$num = preg_split("/[^0-9a-z][0-9]{1,2}[^0-9a-z]/i",substr($testo_vett[0],-16));
if (count($num) > 1) {
$dist_prima = (strlen($num[(count($num) - 1)]) + 1);
if (!$numero or $dist_prima < $dist_dopo) {
$numero = substr($testo_vett[0],(($dist_prima * -1) - 1),1);
if (preg_match("/[0-9]/",substr($testo_vett[0],(($dist_prima * -1) - 2),1))) $numero = substr($testo_vett[0],(($dist_prima * -1) - 2),1).$numero;
} # fine if (!$numero or $dist_prima < $dist_dopo)
} # fine if (count($num) > 1)
} # fine if ($testo_vett[1])

return $numero;

} # fine function trova_numero_vicino



?>