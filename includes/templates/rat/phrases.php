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



function mext_rat ($messaggio,$pag,$lingua="") {
if ($lingua) $lingua_mex = $lingua;
else global $lingua_mex;

if ($lingua_mex != "en" and $lingua_mex != "ita" and $lingua_mex != "es") {
if (@is_file("./includes/lang/$lingua_mex/modt_rat.php")) include("./includes/lang/$lingua_mex/modt_rat.php");
} # fine if ($lingua_mex != "en" and...

if ($tr != 1) {

# messaggi in inglese di default
if ($lingua_mex != "es" and $lingua_mex != "ita") {
switch ($messaggio) {
case "Pagina della tabella con le tariffe":	$messaggio = "Page of the rates table"; $tr = 1; break;
case "Crea la pagina con la tabella delle tariffe":	$messaggio = "Create the rates table page"; $tr = 1; break;
case "Mostra tariffe":  		$messaggio = "Show rates"; $tr = 1; break;
case "Tabella Tariffe":  		$messaggio = "Rates Table"; $tr = 1; break;
case "Stile tabella tariffe":  		$messaggio = "Rates table style"; $tr = 1; break;
case "Costi aggiuntivi":  		$messaggio = "Extra costs"; $tr = 1; break;
case "della tariffa":  			$messaggio = "of the rate"; $tr = 1; break;
case "della tariffa senza persone":	$messaggio = "of the rate without persons"; $tr = 1; break;
case "della tariffa di una persona":	$messaggio = "of the rate of one person"; $tr = 1; break;
case "del prezzo totale":  		$messaggio = "of total price"; $tr = 1; break;
case "della caparra":  			$messaggio = "of deposit"; $tr = 1; break;
case "del resto della caparra":  	$messaggio = "of deposit rest"; $tr = 1; break;
case "alla settimana":			$messaggio = "per week"; $tr = 1; break;
case "al giorno":			$messaggio = "per day"; $tr = 1; break;
case "a persona":  			$messaggio = "per person"; $tr = 1; break;
case "obbligatorio con":  		$messaggio = "obligatory with"; $tr = 1; break;
case "tutte le tariffe":  		$messaggio = "all rates"; $tr = 1; break;
case "*p":  				$messaggio = "*p"; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;

case "var_stile_tabella_tariffe":  	$messaggio = "var_rates_table_style"; $tr = 1; break;
case "var_fr_Costi_aggiuntivi":  	$messaggio = "var_phr_Extra_costs"; $tr = 1; break;
case "var_fr_della_tariffa":  		$messaggio = "var_phr_of_the_rate"; $tr = 1; break;
case "var_fr_della_tariffa_senza_persone":	$messaggio = "var_phr_of_the_rate_without_persons"; $tr = 1; break;
case "var_fr_della_tariffa_di_una_persona":	$messaggio = "var_phr_of_the_rate_of_one_person"; $tr = 1; break;
case "var_fr_del_prezzo_totale":  	$messaggio = "var_phr_of_total_price"; $tr = 1; break;
case "var_fr_della_caparra":  		$messaggio = "var_phr_of_deposit"; $tr = 1; break;
case "var_fr_del_resto_della_caparra":	$messaggio = "var_phr_of_deposit_rest"; $tr = 1; break;
case "var_fr_alla_settimana":  		$messaggio = "var_phr_per_week"; $tr = 1; break;
case "var_fr_a_persona":  		$messaggio = "var_phr_per_person"; $tr = 1; break;
case "var_fr_obbligatorio_con":  	$messaggio = "var_phr_obligatory_with"; $tr = 1; break;
case "var_fr_tutte_le_tariffe":  	$messaggio = "var_phr_all_rates"; $tr = 1; break;
case "var_fr_per_p":  			$messaggio = "var_phr_per_p"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_phr_"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_phr_"; $tr = 1; break;
} # fine switch ($messaggio)
} # fine if ($lingua_mex != "es" and $lingua_mex != "ita")

if ($lingua_mex == "es") {
switch ($messaggio) {
case "Pagina della tabella con le tariffe":	$messaggio = "Página de la tabla con las tarifas"; $tr = 1; break;
case "Crea la pagina con la tabella delle tariffe":	$messaggio = "Crear la página con la tabla de las tarifas"; $tr = 1; break;
case "Mostra tariffe":  		$messaggio = "Enseñar las tarifas"; $tr = 1; break;
case "Tabella Tariffe":  		$messaggio = "Tabla de las Tarifas"; $tr = 1; break;
case "Stile tabella tariffe":  		$messaggio = "Estilo de la tabla de las tarifas"; $tr = 1; break;
case "Costi aggiuntivi":  		$messaggio = "Costes añadidos"; $tr = 1; break;
case "della tariffa":  			$messaggio = "de la tarifa"; $tr = 1; break;
case "della tariffa senza persone":	$messaggio = "de la tarifa sin personas"; $tr = 1; break;
case "della tariffa di una persona":	$messaggio = "de la tarifa de una persona"; $tr = 1; break;
case "del prezzo totale":  		$messaggio = "del precio total"; $tr = 1; break;
case "della caparra":  			$messaggio = "de la fianza"; $tr = 1; break;
case "del resto della caparra":  	$messaggio = "del resto de la fianza"; $tr = 1; break;
case "alla settimana":			$messaggio = "a la semana"; $tr = 1; break;
case "al giorno":			$messaggio = "al día"; $tr = 1; break;
case "a persona":  			$messaggio = "por persona"; $tr = 1; break;
case "obbligatorio con":  		$messaggio = "obligatorio con"; $tr = 1; break;
case "tutte le tariffe":  		$messaggio = "todas las tarifas"; $tr = 1; break;
case "*p":  				$messaggio = "*p"; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;
case "":  				$messaggio = ""; $tr = 1; break;

case "var_stile_tabella_tariffe":  	$messaggio = "var_estilo_tabla_reservas"; $tr = 1; break;
case "var_fr_Costi_aggiuntivi":  	$messaggio = "var_fr_Costes_agnadidos"; $tr = 1; break;
case "var_fr_della_tariffa":  		$messaggio = "var_fr_de_la_tarifa"; $tr = 1; break;
case "var_fr_della_tariffa_senza_persone":	$messaggio = "var_fr_de_la_tarifa_sin_personas"; $tr = 1; break;
case "var_fr_della_tariffa_di_una_persona":	$messaggio = "var_fr_de_la_tarifa_de_una_persona"; $tr = 1; break;
case "var_fr_del_prezzo_totale":  	$messaggio = "var_fr_del_precio_total"; $tr = 1; break;
case "var_fr_della_caparra":  		$messaggio = "var_fr_de_la_fianza"; $tr = 1; break;
case "var_fr_del_resto_della_caparra":	$messaggio = "var_fr_del_resto_de_la_fianza"; $tr = 1; break;
case "var_fr_alla_settimana":  		$messaggio = "var_fr_a_la_semana"; $tr = 1; break;
case "var_fr_a_persona":  		$messaggio = "var_fr_por_persona"; $tr = 1; break;
case "var_fr_obbligatorio_con":  	$messaggio = "var_fr_obligatorio_con"; $tr = 1; break;
case "var_fr_tutte_le_tariffe":  	$messaggio = "var_fr_todas_las_tarifas"; $tr = 1; break;
case "var_fr_per_p":  			$messaggio = "var_fr_por_p"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_fr_"; $tr = 1; break;
case "var_fr_":  			$messaggio = "var_fr_"; $tr = 1; break;
} # fine switch ($messaggio)
} # fine if ($lingua_mex == "es")

if ($tr != 1) {
if ($lingua) $messaggio = mex2($messaggio,$pag,$lingua);
else $messaggio = mex($messaggio,$pag);
} # fine if ($tr != 1)

} # fine if ($tr != 1)

return $messaggio;
} # fine function mext_rat



$fr_frase = array();
$frase = array();

$fr_frase[0] = "fr_dal";
$fr_frase[1] = "fr_al";
$fr_frase[2] = "fr_tariffa";
$fr_frase[3] = "fr_per";
$fr_frase[4] = "fr_parola_settimana";
$fr_frase[5] = "fr_parola_settimane";
$fr_frase[6] = "fr_Prezzo";
$fr_frase[7] = "fr_Caparra";
$fr_frase[8] = "fr_di";
$fr_frase[9] = "fr_persone";
$fr_frase[10] = "fr_persona";
$fr_frase[11] = "fr_per_ogni";
$fr_frase[12] = "fr_Costi_aggiuntivi";
$fr_frase[13] = "fr_della_tariffa";
$fr_frase[14] = "fr_della_tariffa_senza_persone";
$fr_frase[15] = "fr_della_tariffa_di_una_persona";
$fr_frase[16] = "fr_del_prezzo_totale";
$fr_frase[17] = "fr_della_caparra";
$fr_frase[18] = "fr_del_resto_della_caparra";
$fr_frase[19] = "fr_alla_settimana";
$fr_frase[20] = "fr_a_persona";
$fr_frase[21] = "fr_obbligatorio_con";
$fr_frase[22] = "fr_tutte_le_tariffe";
$fr_frase[23] = "fr_per_p";

$frase[0] = "dal";
$frase[1] = "al";
$frase[2] = "tariffa";
$frase[3] = "per";
$frase[4] = "$parola_settimana";
$frase[5] = "$parola_settimane";
$frase[6] = "Prezzo";
$frase[7] = "Caparra";
$frase[8] = "di";
$frase[9] = "persone";
$frase[10] = "persona";
$frase[11] = "per ogni";
$frase[12] = "Costi aggiuntivi";
$frase[13] = "della tariffa";
$frase[14] = "della tariffa senza persone";
$frase[15] = "della tariffa di una persona";
$frase[16] = "del prezzo totale";
$frase[17] = "della caparra";
$frase[18] = "del resto della caparra";
$frase[19] = "$parola_alla $parola_settimana";
$frase[20] = "a persona";
$frase[21] = "obbligatorio con";
$frase[22] = "tutte le tariffe";
$frase[23] = "*p";

$num_frasi = count($fr_frase);



?>