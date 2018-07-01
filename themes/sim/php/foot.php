<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2015 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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


if ($pag != "visualizza_contratto.php" or $show_bar != "NO") {
if ($show_bar == "NO") echo "</td></tr></table>";
else echo "
</div>

<script type=\"text/javascript\">
<!--
replica_tasti();
-->
</script>
";
} # fine if ($pag != "visualizza_contratto.php" or $show_bar != "NO")

if ($pag == "visualizza_tabelle.php" and $tipo_tabella == "prenotazioni") {
echo "
<script type=\"text/javascript\">
<!--
var lista_prenota_contr_orig = '$lista_prenota_contr';
var lista_prenota_mod_orig = ',$lista_prenota_mod,';
attiva_seleziona_uguali('');
-->
</script>
";
} # fine if ($pag == "visualizza_tabelle.php" and...

if ($pag == "modifica_prenota.php" or $pag == "modifica_cliente.php") {
echo "
<script type=\"text/javascript\">
<!--
ridim_col_modres();
-->
</script>
";
} # fine if ($pag == "modifica_prenota.php" or $pag == "modifica_cliente.php")

if ($mobile_device) {
echo "
<script type=\"text/javascript\">
<!--
tab_in_container();
-->
</script>
";
} # fine if ($mobile_device)



if ($start_time) {
$start_time = explode(" ",$start_time);
$start_time = (double) $start_time[0] + (double) $start_time[1];
$end_time = explode(" ",microtime());
$end_time = (double) $end_time[0] + (double) $end_time[1];
echo "<small><small>seconds: ".round(($end_time - $start_time),2)."</small></small>";
} # fine if ($start_time)

echo "
</body>
</html>";





?>