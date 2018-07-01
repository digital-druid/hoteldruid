<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2013 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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


 
$file_interconnessioni = C_DATI_PATH."/dati_interconnessioni.php";
if (@is_file($file_interconnessioni)) {
unset($ic_present);
include($file_interconnessioni);
if (@is_array($ic_present)) {
global $PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT;
flush();
if (function_exists('ob_flush')) ob_flush();
if ($closed_on_arr_dep != "SI") $closed_on_arr_dep = "NO";
declare (ticks=1) {
$fork_pid = pcntl_fork();

# esecuzione originale dopo fork
if ($fork_pid and $fork_pid != -1) {
if ($tabelle_lock) {
unlock_tabelle($tabelle_lock);
unset($tabelle_lock);
} # fine if ($tabelle_lock)
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
} # fine if ($fork_pid and $fork_pid != -1)

# esecuzione del fork dopo il fork, o se non è avvenuto il fork
else {
if (!$fork_pid) {
# Creo nuova connessione dopo il fork per sicurezza, anche se non sembra necessario
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
$lock = 1;
} # fine if (!$fork_pid)
$interconn_dir = opendir("./includes/interconnect/");
while ($mod_ext = readdir($interconn_dir)) {
if ($mod_ext != "." and $mod_ext != ".." and @is_dir("./includes/interconnect/$mod_ext")) {
include("./includes/interconnect/$mod_ext/name.php");
if ($ic_present[$interconnection_name] == "SI") {
$funz_update_availability = "update_availability_".$interconnection_func_name;
$funz_update_rates = "update_rates_".$interconnection_func_name;
if (!function_exists($funz_update_availability)) include("./includes/interconnect/$mod_ext/functions.php");
if ($aggiorna_disp) $funz_update_availability($file_interconnessioni,$anno,$PHPR_TAB_PRE,$lock);
if ($aggiorna_tar) $funz_update_rates($file_interconnessioni,$anno,$PHPR_TAB_PRE,$lock,"NO",$closed_on_arr_dep);
} # fine if ($ic_present[$interconnection_name] == "SI")
} # fine if ($modello_ext != "." and $modello_ext != ".." and...
} # fine while ($mod_ext = readdir($interconn_dir))
closedir($interconn_dir);
if (!$fork_pid) exit();
} # fine else if ($fork_pid and $fork_pid != -1)

} # fine declare (ticks=1)
} # fine if (@is_array($ic_present))
} # fine if (@is_file($file_interconnessioni))



?>
