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

#Funzioni per usare il database MYSQL con estensioni mysqli



function esegui_query_unbuffered ($query,$silenzio = "") {

global $link_mysqli;
$risul[-1] = mysqli_query($link_mysqli,$query,MYSQLI_USE_RESULT);
$risul[-2] = -1;
$risul[-3] = $query;
if (!$risul[-1] and !$silenzio) {
global $PHPR_TAB_PRE;
echo "<br>ERROR IN: ".str_replace(" ".$PHPR_TAB_PRE," ",$query)." <br>".mysqli_errno($link_mysqli).": ".mysqli_error($link_mysqli)."<br>";
} # fine if (!$risul and !$silenzio)

return $risul;

} # fine function esegui_query_unbuffered



function risul_query_unbuffered (&$query,$riga,$colonna,$tab="") {

return risul_query($query,$riga,$colonna,$tab);

} # fine function risul_query_unbuffered



function numlin_query_unbuffered (&$query) {

global $link_mysqli;
while (mysqli_fetch_row($query[-1])) $num1++;
mysqli_free_result($query[-1]);
$query[-1] = mysqli_query($link_mysqli,$query[-3],MYSQLI_USE_RESULT);
return $num1;

} # fine function numlin_query_unbuffered



function arraylin_query_unbuffered (&$query,$num) {

return arraylin_query($query,$num);

} # fine function arraylin_query_unbuffered



function chiudi_query_unbuffered (&$query) {

chiudi_query($query);

} # fine function chiudi_query_unbuffered




?>