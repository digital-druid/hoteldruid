<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2018 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

#Funzioni per usare il database SQLITE




function esegui_query_unbuffered ($query,$silenzio = "") {

#return esegui_query($query,$silenzio,1);
global $numconnessione;
prepara_query_sqlite($query);
$risul['risul'] = $numconnessione->query($query);
$risul['punt'] = -1;
$risul['numcol'] = $risul['risul']->numColumns();
for ($num1 = 0 ; $num1 < $risul['numcol'] ; $num1++) $risul['col'][$num1] = $risul['risul']->columnName($num1);
return $risul;

} # fine function esegui_query_unbuffered


function risul_query_unbuffered (&$query,$riga,$colonna,$tab="") {

#if ($tab) $colonna = "$tab.$colonna";
if (is_integer($colonna)) $colonna = $query['col'][$colonna];

if ($query['punt'] != (int) $riga) {
$query['riga'] = $query['risul']->fetchArray(SQLITE3_ASSOC);
$query['punt'] = (int) $riga;
} # fine if ($query['punt'] != $riga)
$risul = $query['riga'][$colonna];

return $risul;

} # fine function risul_query_unbuffered


function numlin_query_unbuffered ($query) {

while ($risul = $query['risul']->fetchArray(SQLITE3_NUM)) $num1++;
$query['risul']->reset();
return $num1;

} # fine function numlin_query_unbuffered



function arraylin_query_unbuffered ($query,$num) {

$risul = $query['risul']->fetchArray(SQLITE3_NUM);
return $risul;

} # fine function arraylin_query_unbuffered


function chiudi_query_unbuffered (&$query) {

$query['risul']->finalize();
$query = array();

} # fine function chiudi_query_unbuffered




?>