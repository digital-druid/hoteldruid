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

#Funzioni per usare il database MYSQL



function esegui_query_unbuffered ($query,$silenzio = "") {

return esegui_query($query,$silenzio,1);

} # fine function esegui_query_unbuffered



function risul_query_unbuffered ($query,$riga,$colonna,$tab="") {

return risul_query($query,$riga,$colonna,$tab);

} # fine function risul_query_unbuffered



function numlin_query_unbuffered ($query) {

return numlin_query($query);

} # fine function numlin_query_unbuffered



function arraylin_query_unbuffered ($query,$num) {

return arraylin_query($query,$num);

} # fine function arraylin_query_unbuffered



function chiudi_query_unbuffered (&$query) {

chiudi_query($query);

} # fine function chiudi_query_unbuffered



?>