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



function mext_cal ($messaggio,$pag,$lingua="") {
if ($lingua) $lingua_mex = $lingua;
else global $lingua_mex;

if ($lingua_mex != "en" and $lingua_mex != "ita" and $lingua_mex != "es") {
if (@is_file("./includes/lang/$lingua_mex/modt_cal.php")) include("./includes/lang/$lingua_mex/modt_cal.php");
} # fine if ($lingua_mex != "en" and...

if ($tr != 1) {

# messaggi in inglese di default
if ($lingua_mex != "es" and $lingua_mex != "ita") {
switch ($messaggio) {
case "Pagina del calendario della disponibilità":	$messaggio = "Page of the availability calendar"; $tr = 1; break;
case "Mostra le date":  		$messaggio = "Show dates"; $tr = 1; break;
case "Stile tabella":  			$messaggio = "Table style"; $tr = 1; break;
case "-->":  				$messaggio = "-->"; $tr = 1; break;
case "<--":  				$messaggio = "<--"; $tr = 1; break;
case "Prima data selezionaza":  	$messaggio = "First selected date"; $tr = 1; break;
case "data attuale":  			$messaggio = "current date"; $tr = 1; break;
case "data fissa":  			$messaggio = "fixed date"; $tr = 1; break;
case "Numero di settimane della tabella":	$messaggio = "Number of table weeks"; $tr = 1; break;
case "Numero di giorni della tabella":	$messaggio = "Number of table days"; $tr = 1; break;
case "Crea la pagina con il calendario della disponibilità":	$messaggio = "Create the page with the availability calendar"; $tr = 1; break;
case "è stato selezionato di raggruppare con le regole 2, ma non ne è stata inserita nessuna, quindi la tabella non verrà mostrata":	$messaggio = "you have selected grouping with rules 2, but none has been inserted, so the table will not be shown"; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;

case "var_stile_tabella_cal":  		$messaggio = "var_cal_table_style"; $tr = 1; break;
case "var_data_preselezionata":  	$messaggio = "var_default_date"; $tr = 1; break;
case "var_numero_giorni":  		$messaggio = "var_days_number"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_phr_"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_phr_"; $tr = 1; break;
} # fine switch ($messaggio)
} # fine if ($lingua_mex != "es" and $lingua_mex != "ita")

if ($lingua_mex == "es") {
switch ($messaggio) {
case "Pagina del calendario della disponibilità":	$messaggio = "Página del calendario de la disponibilidad"; $tr = 1; break;
case "Mostra le date":  		$messaggio = "Enseñar las fechas"; $tr = 1; break;
case "Stile tabella":  			$messaggio = "Estilo de la tabla"; $tr = 1; break;
case "-->":  				$messaggio = "-->"; $tr = 1; break;
case "<--":  				$messaggio = "<--"; $tr = 1; break;
case "Prima data selezionaza":  	$messaggio = "Primera fecha seleccionada"; $tr = 1; break;
case "data attuale":  			$messaggio = "fecha actual"; $tr = 1; break;
case "data fissa":  			$messaggio = "fech fija"; $tr = 1; break;
case "Numero di settimane della tabella":	$messaggio = "Número de semanas de la tabla"; $tr = 1; break;
case "Numero di giorni della tabella":	$messaggio = "Número de dias dela tabla"; $tr = 1; break;
case "Crea la pagina con il calendario della disponibilità":	$messaggio = "Crear la página con el calendario de la disponibilidad"; $tr = 1; break;
case "è stato selezionato di raggruppare con le regole 2, ma non ne è stata inserita nessuna, quindi la tabella non verrà mostrata":	$messaggio = "se ha seleccionado agrupar con las reglas 2, pero ninguna ha sido insertada, así que no se mostrará la tabla"; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;

case "var_stile_tabella_cal":  		$messaggio = "var_estilo_tabla_cal"; $tr = 1; break;
case "var_data_preselezionata":  	$messaggio = "var_fecha_preseleccionada"; $tr = 1; break;
case "var_numero_giorni":  		$messaggio = "var_numero_dias"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_fr_"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_phr_"; $tr = 1; break;
} # fine switch ($messaggio)
} # fine if ($lingua_mex == "es")

if ($tr != 1) {
if ($lingua) $messaggio = mex2($messaggio,$pag,$lingua);
else $messaggio = mex($messaggio,$pag);
} # fine if ($tr != 1)

} # fine if ($tr != 1)

return $messaggio;
} # fine function mext_cal



$fr_frase = array();
$frase = array();

$fr_frase[0] = "fr_tariffa";
$fr_frase[1] = "fr_persona";
$fr_frase[2] = "fr_persone";
$fr_frase[3] = "fr_Gennaio";
$fr_frase[4] = "fr_Febbraio";
$fr_frase[5] = "fr_Marzo";
$fr_frase[6] = "fr_Aprile";
$fr_frase[7] = "fr_Maggio";
$fr_frase[8] = "fr_Giugno";
$fr_frase[9] = "fr_Luglio";
$fr_frase[10] = "fr_Agosto";
$fr_frase[11] = "fr_Settembre";
$fr_frase[12] = "fr_Ottobre";
$fr_frase[13] = "fr_Novembre";
$fr_frase[14] = "fr_Dicembre";
$fr_frase[15] = "fr_Quadro_indicativo_disponibilita";
$fr_frase[16] = "fr_freccia_destra";
$fr_frase[17] = "fr_freccia_sinistra";

$frase[0] = "tariffa";
$frase[1] = "persona";
$frase[2] = "persone";
$frase[3] = "Gennaio";
$frase[4] = "Febbraio";
$frase[5] = "Marzo";
$frase[6] = "Aprile";
$frase[7] = "Maggio";
$frase[8] = "Giugno";
$frase[9] = "Luglio";
$frase[10] = "Agosto";
$frase[11] = "Settembre";
$frase[12] = "Ottobre";
$frase[13] = "Novembre";
$frase[14] = "Dicembre";
$frase[15] = "Quadro indicativo disponibilità";
$frase[16] = "-->";
$frase[17] = "<--";

$num_frasi = count($fr_frase);



?>