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



function trova_app_consentiti (&$appartamenti,$num_appartamenti,$attiva_regole1_consentite,$regole1_consentite,$priv_mod_assegnazione_app,$priv_mod_prenotazioni,$priv_ins_assegnazione_app,$priv_ins_nuove_prenota,$attiva_tariffe_consentite,$tariffe_consentite_vett,$id_utente,$tableregole,$tablenometariffe) {

if (@is_array($priv_ins_nuove_prenota)) {
$attiva_regole1_consentite_gr = $attiva_regole1_consentite;
$regole1_consentite_gr = $regole1_consentite;
$priv_mod_assegnazione_app_gr = $priv_mod_assegnazione_app;
$priv_mod_prenotazioni_gr = $priv_mod_prenotazioni;
$priv_ins_assegnazione_app_gr = $priv_ins_assegnazione_app;
$priv_ins_nuove_prenota_gr = $priv_ins_nuove_prenota;
$attiva_tariffe_consentite_gr = $attiva_tariffe_consentite;
$tariffe_consentite_vett_gr = $tariffe_consentite_vett;
} # fine if (@is_array($priv_ins_nuove_prenota))
else {
$attiva_regole1_consentite_gr[$id_utente] = $attiva_regole1_consentite;
$regole1_consentite_gr[$id_utente] = $regole1_consentite;
$priv_mod_assegnazione_app_gr[$id_utente] = $priv_mod_assegnazione_app;
$priv_mod_prenotazioni_gr[$id_utente] = $priv_mod_prenotazioni;
$priv_ins_assegnazione_app_gr[$id_utente] = $priv_ins_assegnazione_app;
$priv_ins_nuove_prenota_gr[$id_utente] = $priv_ins_nuove_prenota;
$attiva_tariffe_consentite_gr[$id_utente] = $attiva_tariffe_consentite;
$tariffe_consentite_vett_gr[$id_utente] = $tariffe_consentite_vett;
} # fine else if (@is_array($priv_ins_nuove_prenota))

unset($appartamenti_consentiti);
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
$appartamenti_consentiti[$id_appartamento] = "NO";
} # fine for $num1


foreach ($priv_ins_nuove_prenota_gr as $idut_gr => $val) {

unset($condizioni_regole1_consentite);
unset($appartamenti_consentiti_regola1);
unset($appartamenti_consentiti_regola2);

$attiva_regole1_consentite = $attiva_regole1_consentite_gr[$idut_gr];
$regole1_consentite = $regole1_consentite_gr[$idut_gr];
$priv_mod_assegnazione_app = $priv_mod_assegnazione_app_gr[$idut_gr];
$priv_mod_prenotazioni = $priv_mod_prenotazioni_gr[$idut_gr];
$priv_ins_assegnazione_app = $priv_ins_assegnazione_app_gr[$idut_gr];
$priv_ins_nuove_prenota = $priv_ins_nuove_prenota_gr[$idut_gr];
$attiva_tariffe_consentite = $attiva_tariffe_consentite_gr[$idut_gr];
$tariffe_consentite_vett = $tariffe_consentite_vett_gr[$idut_gr];

if ($attiva_regole1_consentite != "n") {
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
$appartamenti_consentiti_regola1[$id_appartamento] = "NO";
} # fine for $num1
for ($num1 = 0 ; $num1 < count($regole1_consentite) ; $num1++) if ($regole1_consentite[$num1]) $condizioni_regole1_consentite .= "motivazione = '".$regole1_consentite[$num1]."' or ";
if ($condizioni_regole1_consentite) {
$condizioni_regole1_consentite = "(".str_replace("motivazione = ' '","motivazione = '' or motivazione is null",substr($condizioni_regole1_consentite,0,-4)).")";
$appartamenti_regola1 = esegui_query("select idregole,iddatainizio,iddatafine,app_agenzia from $tableregole where $condizioni_regole1_consentite order by app_agenzia");
for ($num1 = 0 ; $num1 < numlin_query($appartamenti_regola1) ; $num1++) {
$id_appartamento = risul_query($appartamenti_regola1,$num1,'app_agenzia');
$appartamenti_consentiti_regola1[$id_appartamento] = "SI";
} # fine for $num1
} # fine if ($condizioni_regole1_consentite)
} # fine if ($attiva_regole1_consentite != "n")

if (($priv_mod_assegnazione_app != "s" or $priv_mod_prenotazioni != "s") and ($priv_ins_assegnazione_app != "s" or $priv_ins_nuove_prenota != "s")) {
$tutti_app_consentiti = "NO";
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 ");
$numero_tariffe = risul_query($rigatariffe,0,'nomecostoagg');
for ($numtariffa = 1 ; $numtariffa <= $numero_tariffe ; $numtariffa++) {
if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI") {
$tariffa = "tariffa".$numtariffa;
$regola2 = esegui_query("select motivazione from $tableregole where tariffa_per_app = '$tariffa'");
if (numlin_query($regola2) == 0) {
$tutti_app_consentiti = "SI";
break;
} # fine if (numlin_query($regola2) == 0)
else {
$appartamenti_regola2 = explode(",",risul_query($regola2,0,"motivazione"));
for ($num1 = 0 ; $num1 < count($appartamenti_regola2) ; $num1++) $appartamenti_consentiti_regola2[$appartamenti_regola2[$num1]] = "SI";
} # fine else if (numlin_query($regola2) == 0)
} # fine if ($attiva_tariffe_consentite == "n" or $tariffe_consentite_vett[$numtariffa] == "SI")
} # fine for $numtariffa
if ($tutti_app_consentiti != "SI") {
for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
if ($appartamenti_consentiti_regola2[$id_appartamento] != "SI") $appartamenti_consentiti_regola2[$id_appartamento] = "NO";
} # fine for $num1
} # fine if ($tutti_app_consentiti != "SI")
} # fine if (($priv_mod_assegnazione_app != "s" or...

for ($num1 = 0 ; $num1 < $num_appartamenti ; $num1++) {
$id_appartamento = risul_query($appartamenti,$num1,'idappartamenti');
if ($appartamenti_consentiti_regola1[$id_appartamento] != "NO" and $appartamenti_consentiti_regola2[$id_appartamento] != "NO") $appartamenti_consentiti[$id_appartamento] = "SI";
} # fine for $num1

} # fine foreach ($priv_ins_nuove_prenota_gr as $idut_gr => $val)


return $appartamenti_consentiti;

} # fine function trova_app_consentiti



?>