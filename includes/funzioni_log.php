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



function inserisci_log ($query,$idlog = "") {
global $id_utente,$PHPR_TAB_PRE,$PHPR_LOG;
if (!$idlog) $idutente_log = $id_utente;
else $idutente_log = $idlog;

if ($idutente_log and $idutente_log != 1) {
if ($PHPR_LOG == "SI" or str_replace(",$idutente_log,","","$PHPR_LOG,") != "$PHPR_LOG,") {
$query = trim(str_replace(" ".$PHPR_TAB_PRE," ",togli_acapo($query)));
if (substr($query,0,7) != "select ") {
$query2 = $query;
if (substr($query,0,12) == "insert into ") { $query2 = trim(substr($query,12)); $insert = 1; }
elseif (substr($query,0,7) == "update ") $query2 = trim(substr($query,7));
elseif (substr($query,0,12) == "delete from ") $query2 = trim(substr($query,12));
if ($query != $query2) {
if (substr($query2,0,11) != "transazioni" and (substr($query2,0,8) != "sessioni" or $insert) and substr($query2,0,8) != "versioni" and substr($query2,0,5) != "cache") {

if ($insert and substr($query2,0,8) == "sessioni") $query = "LOGIN";
$query = nl2br(htmlspecialchars(substr($query,0,1400)));
$file_log = C_DATI_PATH."/log_utenti.php";
$filelock = crea_lock_file($file_log);
if (@is_file($file_log)) $dati_file = file($file_log);
$dati_file[0] = "<?php exit(); ?>\n";
$num_lin = count($dati_file);
$limite = date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600) - 2592000));
for ($n = 1 ; $n < $num_lin ; $n++) {
$data = explode(">",$dati_file[$n]);
$data = $data[1];
if (strcmp($limite,$data) > 0) $dati_file[$n] = "";
else break;
} # fine for $n
if ($num_lin >= 12000) $dati_file[1] = "";
$dati_file[$num_lin] = $idutente_log.">".date("Y-m-d H:i:s",(time() + (C_DIFF_ORE * 3600))).">".$query."\n";
$fp_log = @fopen($file_log,"w");
if ($fp_log) {
flock($fp_log,2);
fwrite($fp_log,implode("",$dati_file));
flock($fp_log,3);
fclose($fp_log);
} # fine if ($fp_log)
distruggi_lock_file($filelock,$file_log);

} # fine if (substr($query2,0,11) != "transazioni" and...
} # fine if ($query != $query2)
} # fine if (substr($query,0,7) != "select ")
} # fine if ($PHPR_LOG == "SI" or str_replace(",$idutente_log,","","$PHPR_LOG,") != "$PHPR_LOG,")
} # fine if ($idutente_log and $idutente_log != 1)

} # fine function inserisci_log



?>