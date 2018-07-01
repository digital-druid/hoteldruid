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


$tableanni = $PHPR_TAB_PRE."anni";
$tipo_periodi = esegui_query("select * from $tableanni where idanni = $anno");
$tipo_periodi = risul_query($tipo_periodi,0,'tipo_periodi');
if ($tipo_periodi == "g") {
$parola_settimanale = "giornaliero";
$parola_settimanali = "giornalieri";
$parola_settimane = "giorni";
$parola_Settimane = "Giorni";
$parola_settimana = "giorno";
$parola_settiman = "giorn";
$parola_sett = "gio";
$parola_le = "i";
$parola_Le = "I";
$parola_la = "il";
$parola_La = "Il";
$parola_alla = "al";
$lettera_e = "i";
$lettera_a = "o";
$lettera_a2 = "";
$lettera_s = "g";
$sillaba_che = "ci";
} # fine if ($tipo_periodi == "g")
else {
$parola_settimanale = "settimanale";
$parola_settimanali = "settimanali";
$parola_settimane = "settimane";
$parola_Settimane = "Settimane";
$parola_settimana = "settimana";
$parola_settiman = "settiman";
$parola_sett = "sett";
$parola_le = "le";
$parola_Le = "Le";
$parola_la = "la";
$parola_La = "La";
$parola_alla = "alla";
$lettera_e = "e";
$lettera_a = "a";
$lettera_a2 = "a";
$lettera_s = "s";
$sillaba_che = "che";
} # fine else if ($tipo_periodi == "g")


?>