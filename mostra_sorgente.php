<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2009 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

$pag = "mostra_sorgente.php";
$titolo = "HotelDruid: Source Code";

include("./costanti.php");
include("./includes/funzioni.php");
include("./includes/files_sorgente.php");


if ($raw != "SI") {
$show_bar = "NO";
$tema_corr = $tema[1];
@include(C_DATI_PATH."/lingua.php");
if ($lingua[1] and @is_dir("./includes/lang/".$lingua[1])) $lingua_mex = $lingua[1];
else $lingua_mex = "ita";
if ($tema[1] and $tema[1] != "base" and @is_dir("./themes/".$tema[1]."/php")) include("./themes/".$tema[1]."/php/head.php");
else include("./includes/head.php");
} # fine if ($raw != "SI")


if ($file_sorgente and substr($file_sorgente,-1) != "/") {
$file_trovato = "NO";
reset($files_sorgente);
foreach ($files_sorgente as $f_s) if ($file_sorgente == $f_s) $file_trovato = "SI";
if ($file_trovato == "SI") {
$mostra_lista = "NO";
$file_sorgente_orig = $file_sorgente;
if (defined("C_CARTELLA_FILES_REALI")) {
if ($file_sorgente == "includes/costanti.php") $file_sorgente = "";
else $file_sorgente = C_CARTELLA_FILES_REALI.$file_sorgente;
} # fine if (defined("C_CARTELLA_FILES_REALI"))
if (@is_file($file_sorgente)) {
if (substr($file_sorgente,-4) == ".png" or substr($file_sorgente,-4) == ".gif" or substr($file_sorgente,-4) == ".jpg" or substr($file_sorgente,-5) == ".jpeg" or substr($file_sorgente,-4) == ".ico") {
if ($raw == "SI") header("Location: $file_sorgente_orig");
else echo "<br><img style=\"display: block;\" src=\"$file_sorgente_orig\" alt=\"$file_sorgente_orig\">";
} # fine if (substr($file_sorgente,-4) == ".png" or...
else {
$file_sorgente = implode("",file($file_sorgente));
if ($raw == "SI") echo $file_sorgente;
else echo nl2br(htmlspecialchars($file_sorgente));
} # fine else if (substr($file_sorgente,-4) == ".png" or...
} # fine if (@is_file($file_sorgente))
} # fine if ($file_trovato == "SI")
} # fine if ($file_sorgente and...



if ($mostra_lista != "NO") {

if ($raw != "SI") echo "<div id=\"flogin\"><small>";
else echo "v".C_PHPR_VERSIONE_NUM;

reset($files_sorgente);
foreach ($files_sorgente as $idf => $file_sorgente) {
if (defined("C_CARTELLA_FILES_REALI")) $file_sorgente_reale = C_CARTELLA_FILES_REALI.$file_sorgente;
else $file_sorgente_reale = $file_sorgente;
if (@is_file($file_sorgente_reale) or @is_dir($file_sorgente_reale)) {
if ($raw != "SI") {
if (substr($file_sorgente,-1) == "/") echo "$file_sorgente<br>";
else echo "<a style=\"color: #000000;\" href=\"./$pag?file_sorgente=$file_sorgente\">$file_sorgente</a><br>";
} # fine ($raw != "SI")
else echo "#$file_sorgente";
} # fine if (@is_file($file_sorgente_reale) or @is_dir($file_sorgente_reale))
} # fine foreach ($files_sorgente as $idf => $file_sorgente)

if ($raw != "SI") echo "</small></div>";

} # fine if ($mostra_lista != "NO")


if ($raw != "SI") {
if ($tema[1] and $tema[1] != "base" and @is_dir("./themes/".$tema[1]."/php")) include("./themes/".$tema[1]."/php/foot.php");
else include("./includes/foot.php");
} # fine ($raw != "SI")




?>
