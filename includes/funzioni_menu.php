<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2011 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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


function crea_menu_date ($file_menu,$nuovo_file_menu,$tipo_periodi) {
$pag = "giorni_mesi.php";
#if ($tipo_periodi == "g") $aggiungi_giorni = 1;
#else $aggiungi_giorni = 7;
include($file_menu);
$file_intero = file("$file_menu");
$fileaperto = fopen("$nuovo_file_menu","w+");
flock($fileaperto,2);

$date_option = "";
#$n_date_menu = 0;
$fr_Sun = mex(" Do",$pag);
$fr_Mon = mex(" Lu",$pag);
$fr_Tue = mex(" Ma",$pag);
$fr_Wed = mex(" Me",$pag);
$fr_Thu = mex(" Gi",$pag);
$fr_Fri = mex(" Ve",$pag);
$fr_Sat = mex(" Sa",$pag);
$fr_Jan = mex("Gen",$pag);
$fr_Feb = mex("Feb",$pag);
$fr_Mar = mex("Mar",$pag);
$fr_Apr = mex("Apr",$pag);
$fr_May = mex("Mag",$pag);
$fr_Jun = mex("Giu",$pag);
$fr_Jul = mex("Lug",$pag);
$fr_Aug = mex("Ago",$pag);
$fr_Sep = mex("Set",$pag);
$fr_Oct = mex("Ott",$pag);
$fr_Nov = mex("Nov",$pag);
$fr_Dec = mex("Dic",$pag);
$num_file_intero = count($file_intero);

for ($num1 = 0 ; $num1 < $num_file_intero ; $num1++) {
if (substr($file_intero[$num1],0,7) == "<option") {
$data_option = aggiungi_slash(substr($file_intero[$num1],16,10));
$giorno_option = substr($data_option,8,2);
$mese_option = substr($data_option,5,2);
$anno_option = substr($data_option,0,4);
$nome_giorno = date("D" , mktime(0,0,0,$mese_option,$giorno_option,$anno_option));
$nome_mese = date("M" , mktime(0,0,0,$mese_option,$giorno_option,$anno_option));
if ($tipo_periodi == "g") {
if ($nome_giorno == "Sun") $nome_giorno = $fr_Sun;
if ($nome_giorno == "Mon") $nome_giorno = $fr_Mon;
if ($nome_giorno == "Tue") $nome_giorno = $fr_Tue;
if ($nome_giorno == "Wed") $nome_giorno = $fr_Wed;
if ($nome_giorno == "Thu") $nome_giorno = $fr_Thu;
if ($nome_giorno == "Fri") $nome_giorno = $fr_Fri;
if ($nome_giorno == "Sat") $nome_giorno = $fr_Sat;
} # fine if ($tipo_periodi == "g")
else $nome_giorno = "";
if ($nome_mese == "Jan") $nome_mese = $fr_Jan;
if ($nome_mese == "Feb") $nome_mese = $fr_Feb;
if ($nome_mese == "Mar") $nome_mese = $fr_Mar;
if ($nome_mese == "Apr") $nome_mese = $fr_Apr;
if ($nome_mese == "May") $nome_mese = $fr_May;
if ($nome_mese == "Jun") $nome_mese = $fr_Jun;
if ($nome_mese == "Jul") $nome_mese = $fr_Jul;
if ($nome_mese == "Aug") $nome_mese = $fr_Aug;
if ($nome_mese == "Sep") $nome_mese = $fr_Sep;
if ($nome_mese == "Oct") $nome_mese = $fr_Oct;
if ($nome_mese == "Nov") $nome_mese = $fr_Nov;
if ($nome_mese == "Dec") $nome_mese = $fr_Dec;
#if (!$date_option) {
#$a_ini_menu = substr($data_option,0,4);
#$m_ini_menu = (substr($data_option,5,2) - 1);
#$g_ini_menu = substr($data_option,8,2);
#} # fine if (!$date_option)
#$n_date_menu++;
$date_option .= "<option value=\\\"$data_option\\\">$nome_mese $giorno_option$nome_giorno, $anno_option</option>
";
} # fine if (substr($file_intero[$num1],0,7) == "<option")
} # fine for $num1

fwrite($fileaperto,"<?php 

");
fwrite($fileaperto,"\$y_ini_menu = array();
\$m_ini_menu = array();
\$d_ini_menu = array();
\$n_dates_menu = array();
\$d_increment = array();
");
for ($num1 = 0 ; $num1 < count($d_increment) ; $num1++) {
fwrite($fileaperto,"\$y_ini_menu[$num1] = \"".$y_ini_menu[$num1]."\";
\$m_ini_menu[$num1] = \"".$m_ini_menu[$num1]."\";
\$d_ini_menu[$num1] = \"".$d_ini_menu[$num1]."\";
\$n_dates_menu[$num1] = \"".$n_dates_menu[$num1]."\";
\$d_increment[$num1] = \"".$d_increment[$num1]."\";
");
} # fine for $num1
if ($partial_dates) fwrite($fileaperto,"\$partial_dates = 1;
");
fwrite($fileaperto,"\$d_names = \"\\\"".mex(" Do","inizio.php")."\\\",\\\"".mex(" Lu","inizio.php")."\\\",\\\"".mex(" Ma","inizio.php")."\\\",\\\"".mex(" Me","inizio.php")."\\\",\\\"".mex(" Gi","inizio.php")."\\\",\\\"".mex(" Ve","inizio.php")."\\\",\\\"".mex(" Sa","inizio.php")."\\\"\";
\$m_names = \"\\\"".mex("Gen","inizio.php")."\\\",\\\"".mex("Feb","inizio.php")."\\\",\\\"".mex("Mar","inizio.php")."\\\",\\\"".mex("Apr","inizio.php")."\\\",\\\"".mex("Mag","inizio.php")."\\\",\\\"".mex("Giu","inizio.php")."\\\",\\\"".mex("Lug","inizio.php")."\\\",\\\"".mex("Ago","inizio.php")."\\\",\\\"".mex("Set","inizio.php")."\\\",\\\"".mex("Ott","inizio.php")."\\\",\\\"".mex("Nov","inizio.php")."\\\",\\\"".mex("Dic","inizio.php")."\\\"\";

\$dates_options_list = \"

$date_option
\";

?>");
flock($fileaperto,3);
fclose($fileaperto);
} # fine function crea_menu_date




function aggiorna_menu_date ($file_menu,$tipo_periodi) {
$pag = "giorni_mesi.php";
#if ($tipo_periodi == "g") $aggiungi_giorni = 1;
#else $aggiungi_giorni = 7;
include($file_menu);
$file_intero = file("$file_menu");
$fileaperto = fopen("$file_menu","w+");
flock($fileaperto,2);
$date_option = "";
#$n_date_menu = 0;
for ($num1 = 0 ; $num1 < count($file_intero) ; $num1++) {
if (substr($file_intero[$num1],0,7) == "<option") {
$data_option = aggiungi_slash(substr($file_intero[$num1],16,10));
$giorno_option = substr($data_option,8,2);
$mese_option = substr($data_option,5,2);
$anno_option = substr($data_option,0,4);
$nome_giorno = date("D" , mktime(0,0,0,$mese_option,$giorno_option,$anno_option));
$nome_mese = date("M" , mktime(0,0,0,$mese_option,$giorno_option,$anno_option));
if ($tipo_periodi == "g") {
if ($nome_giorno == "Sun") $nome_giorno = mex(" Do",$pag);
if ($nome_giorno == "Mon") $nome_giorno = mex(" Lu",$pag);
if ($nome_giorno == "Tue") $nome_giorno = mex(" Ma",$pag);
if ($nome_giorno == "Wed") $nome_giorno = mex(" Me",$pag);
if ($nome_giorno == "Thu") $nome_giorno = mex(" Gi",$pag);
if ($nome_giorno == "Fri") $nome_giorno = mex(" Ve",$pag);
if ($nome_giorno == "Sat") $nome_giorno = mex(" Sa",$pag);
} # fine if ($tipo_periodi == "g")
else $nome_giorno = "";
if ($nome_mese == "Jan") $nome_mese = mex("Gen",$pag);
if ($nome_mese == "Feb") $nome_mese = mex("Feb",$pag);
if ($nome_mese == "Mar") $nome_mese = mex("Mar",$pag);
if ($nome_mese == "Apr") $nome_mese = mex("Apr",$pag);
if ($nome_mese == "May") $nome_mese = mex("Mag",$pag);
if ($nome_mese == "Jun") $nome_mese = mex("Giu",$pag);
if ($nome_mese == "Jul") $nome_mese = mex("Lug",$pag);
if ($nome_mese == "Aug") $nome_mese = mex("Ago",$pag);
if ($nome_mese == "Sep") $nome_mese = mex("Set",$pag);
if ($nome_mese == "Oct") $nome_mese = mex("Ott",$pag);
if ($nome_mese == "Nov") $nome_mese = mex("Nov",$pag);
if ($nome_mese == "Dec") $nome_mese = mex("Dic",$pag);
#if (!$date_option) {
#$a_ini_menu = substr($data_option,0,4);
#$m_ini_menu = (substr($data_option,5,2) - 1);
#$g_ini_menu = substr($data_option,8,2);
#} # fine if (!$date_option)
#$n_date_menu++;
$date_option .= "<option value=\\\"$data_option\\\">$nome_mese $giorno_option$nome_giorno, $anno_option</option>
";
} # fine if (substr($file_intero[$num1],0,7) == "<option")
} # fine for $num1
fwrite($fileaperto,"<?php 

");
fwrite($fileaperto,"\$y_ini_menu = array();
\$m_ini_menu = array();
\$d_ini_menu = array();
\$n_dates_menu = array();
\$d_increment = array();
");
for ($num1 = 0 ; $num1 < count($d_increment) ; $num1++) {
fwrite($fileaperto,"\$y_ini_menu[$num1] = \"".$y_ini_menu[$num1]."\";
\$m_ini_menu[$num1] = \"".$m_ini_menu[$num1]."\";
\$d_ini_menu[$num1] = \"".$d_ini_menu[$num1]."\";
\$n_dates_menu[$num1] = \"".$n_dates_menu[$num1]."\";
\$d_increment[$num1] = \"".$d_increment[$num1]."\";
");
} # fine for $num1
if ($partial_dates) fwrite($fileaperto,"\$partial_dates = 1;
");
fwrite($fileaperto,"\$d_names = \"\\\"".mex(" Do","inizio.php")."\\\",\\\"".mex(" Lu","inizio.php")."\\\",\\\"".mex(" Ma","inizio.php")."\\\",\\\"".mex(" Me","inizio.php")."\\\",\\\"".mex(" Gi","inizio.php")."\\\",\\\"".mex(" Ve","inizio.php")."\\\",\\\"".mex(" Sa","inizio.php")."\\\"\";
\$m_names = \"\\\"".mex("Gen","inizio.php")."\\\",\\\"".mex("Feb","inizio.php")."\\\",\\\"".mex("Mar","inizio.php")."\\\",\\\"".mex("Apr","inizio.php")."\\\",\\\"".mex("Mag","inizio.php")."\\\",\\\"".mex("Giu","inizio.php")."\\\",\\\"".mex("Lug","inizio.php")."\\\",\\\"".mex("Ago","inizio.php")."\\\",\\\"".mex("Set","inizio.php")."\\\",\\\"".mex("Ott","inizio.php")."\\\",\\\"".mex("Nov","inizio.php")."\\\",\\\"".mex("Dic","inizio.php")."\\\"\";

\$dates_options_list = \"

$date_option
\";

?>");
flock($fileaperto,3);
fclose($fileaperto);
} # fine function aggiorna_menu_date




function estendi_menu_date ($file_menu,$nuovo_file_menu,$tipo_periodi,$data_ini,$data_ini_agg,$data_fine,$anno,$pag) {

$mese_fine = substr($data_fine,5,2);
$anno_fine = substr($data_fine,0,4);
$giorno_fine = substr($data_fine,8,2);
if ((integer) substr($data_fine,8,2) == 1) $mese_fine--;
if ($anno_fine > $anno) {
$diff = $anno_fine - $anno;
$mese_fine = ($diff * 12) + $mese_fine;
} # fine if ($anno_fine > $anno)
if (!$data_ini_agg or $data_ini_agg < $data_ini) $data_ini_agg = $data_ini;
$mese_ini_agg = substr($data_ini_agg,5,2);
$anno_ini_agg = substr($data_ini_agg,0,4);
$numgiorno = substr($data_ini_agg,8,2);
if ($anno_ini_agg > $anno) {
$diff = $anno_ini_agg - $anno;
$mese_ini_agg = ($diff * 12) + $mese_ini_agg;
} # fine if ($anno_fine > $anno)

if ($tipo_periodi == "g") $aggiungi_giorni = 1;
else $aggiungi_giorni = 7;

$date_option_agg = "";
$n_date_menu_agg = 0;
$mesecreato = $mese_ini_agg;
while ($mesecreato <= $mese_fine) {
if ($date_option_agg) $numgiorno = $numgiorno + $aggiungi_giorni;
$datafine = date("Y-m-d",mktime(0,0,0,$mese_ini_agg,$numgiorno,$anno));
$annocreato = date("Y",mktime(0,0,0,$mese_ini_agg,$numgiorno,$anno));
$mesecreato = date("n",mktime(0,0,0,$mese_ini_agg,$numgiorno,$anno));
if ($annocreato > $anno) {
$diff = $annocreato - $anno;
$mesecreato = ($diff * 12) + $mesecreato;
} # fine if ($annocreato > $anno)
$nome_giorno = date("D",mktime(0,0,0,$mese_ini_agg,$numgiorno,$anno));
if ($tipo_periodi == "g") $nome_giorno = "[$nome_giorno]";
else $nome_giorno = "";
$nome_mese = substr($datafine,5,2);
$numero_giorno = substr($datafine,8,2);
$numero_anno = substr($datafine,0,4);
if (!$date_option_agg) {
$a_ini_menu_agg = substr($datafine,0,4);
$m_ini_menu_agg = (substr($datafine,5,2) - 1);
$g_ini_menu_agg = substr($datafine,8,2);
} # fine if (!$date_option_agg)
$n_date_menu_agg++;
$date_option_agg .= "<option value=\\\"$datafine\\\">[$nome_mese] $numero_giorno$nome_giorno, $numero_anno</option>
";
} # fine while ($mesecreato <= $mese_fine)


if ($tipo_periodi == "g") {
$date_option_agg = str_replace("[Sun]",mex(" Do","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[Mon]",mex(" Lu","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[Tue]",mex(" Ma","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[Wed]",mex(" Me","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[Thu]",mex(" Gi","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[Fri]",mex(" Ve","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[Sat]",mex(" Sa","inizio.php"),$date_option_agg);
} # fine if ($tipo_periodi == "g")
$date_option_agg = str_replace("[01]",mex("Gen","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[02]",mex("Feb","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[03]",mex("Mar","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[04]",mex("Apr","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[05]",mex("Mag","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[06]",mex("Giu","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[07]",mex("Lug","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[08]",mex("Ago","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[09]",mex("Set","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[10]",mex("Ott","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[11]",mex("Nov","inizio.php"),$date_option_agg);
$date_option_agg = str_replace("[12]",mex("Dic","inizio.php"),$date_option_agg);
unset($y_ini_menu);
unset($m_ini_menu);
unset($d_ini_menu);
unset($n_dates_menu);
unset($d_increment);
include($file_menu);
$num_periodi_vecchi = count($y_ini_menu);
$fileaperto = fopen($nuovo_file_menu,"w+");
flock($fileaperto,2);
fwrite($fileaperto,"<?php 

");
fwrite($fileaperto,"\$y_ini_menu = array();
\$m_ini_menu = array();
\$d_ini_menu = array();
\$n_dates_menu = array();
\$d_increment = array();
");
$num_menu = 0;
for ($num1 = 0 ; $num1 < $num_periodi_vecchi ; $num1++) {
$scrivi = 0;
if ($y_ini_menu[$num1]."-".$m_ini_menu[$num1]."-".$d_ini_menu[$num1] > $data_ini) $scrivi = 1;
else {
$numgiorno = $d_ini_menu[$num1];
for ($num2 = 0 ; $num2 < $n_dates_menu[$num1] ; $num2++) {
$data_corr = date("Y-m-d",mktime(0,0,0,($m_ini_menu[$num1] + 1),$numgiorno,$y_ini_menu[$num1]));
if ($data_corr >= $data_ini and $data_corr != $data_ini_agg) {
$y_ini_menu[$num1] = substr($data_corr,0,4);
$m_ini_menu[$num1] = (substr($data_corr,5,2) - 1);
$d_ini_menu[$num1] = substr($data_corr,8,2);
$n_dates_menu[$num1] = $n_dates_menu[$num1] - $num2;
if ($n_dates_menu[$num1]) $scrivi = 1;
break;
} # fine if ($data_corr >= $data_ini and...
$numgiorno = $numgiorno + $d_increment[$num1];
} # fine for $num2
} # fine else if ($y_ini_menu[$num1]."-".$m_ini_menu[$num1]."-".$d_ini_menu[$num1] > $data_ini)
if ($scrivi) {
fwrite($fileaperto,"\$y_ini_menu[$num_menu] = \"".$y_ini_menu[$num1]."\";
\$m_ini_menu[$num_menu] = \"".$m_ini_menu[$num1]."\";
\$d_ini_menu[$num_menu] = \"".$d_ini_menu[$num1]."\";
\$n_dates_menu[$num_menu] = \"".$n_dates_menu[$num1]."\";
\$d_increment[$num_menu] = \"".$d_increment[$num1]."\";
");
$num_menu++;
} # fine if ($scrivi)
} # fine for $num1
$dates_options_list_vett = explode("<option value=\"",$dates_options_list);
$num_dates_options_list = count($dates_options_list_vett);
for ($num1 = 1 ; $num1 < $num_dates_options_list ; $num1++) {
$data_corr = substr($dates_options_list_vett[$num1],0,10);
if ($data_corr >= $data_ini and ($data_corr != $data_ini_agg or !$date_option_agg)) $n_dates_options_list .= "<option value=\"".$dates_options_list_vett[$num1];
} # fine for $num1
if ($n_date_menu_agg) {
fwrite($fileaperto,"\$y_ini_menu[$num_menu] = \"$a_ini_menu_agg\";
\$m_ini_menu[$num_menu] = \"$m_ini_menu_agg\";
\$d_ini_menu[$num_menu] = \"$g_ini_menu_agg\";
\$n_dates_menu[$num_menu] = \"$n_date_menu_agg\";
\$d_increment[$num_menu] = \"$aggiungi_giorni\";
");
} # fine if ($n_date_menu_agg)
if ($partial_dates) fwrite($fileaperto,"\$partial_dates = 1;
");
fwrite($fileaperto,"\$d_names = \"\\\"".mex(" Do","inizio.php")."\\\",\\\"".mex(" Lu","inizio.php")."\\\",\\\"".mex(" Ma","inizio.php")."\\\",\\\"".mex(" Me","inizio.php")."\\\",\\\"".mex(" Gi","inizio.php")."\\\",\\\"".mex(" Ve","inizio.php")."\\\",\\\"".mex(" Sa","inizio.php")."\\\"\";
\$m_names = \"\\\"".mex("Gen","inizio.php")."\\\",\\\"".mex("Feb","inizio.php")."\\\",\\\"".mex("Mar","inizio.php")."\\\",\\\"".mex("Apr","inizio.php")."\\\",\\\"".mex("Mag","inizio.php")."\\\",\\\"".mex("Giu","inizio.php")."\\\",\\\"".mex("Lug","inizio.php")."\\\",\\\"".mex("Ago","inizio.php")."\\\",\\\"".mex("Set","inizio.php")."\\\",\\\"".mex("Ott","inizio.php")."\\\",\\\"".mex("Nov","inizio.php")."\\\",\\\"".mex("Dic","inizio.php")."\\\"\";

\$dates_options_list = \"

".trim(addslashes($n_dates_options_list))."
$date_option_agg
\";

?>");
flock($fileaperto,3);
fclose($fileaperto);

} # fine function estendi_menu_date




?>