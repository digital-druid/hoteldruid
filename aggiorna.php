<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<title>HotelDruid: Aggiorna Database</title>
<link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico">
<link rel="stylesheet" type="text/css" href="./base.css?v=new">
</head>
<body style="background-color: #ffffff;">
<div>

<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2018 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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

$pag = "aggiorna.php";
error_reporting(E_ALL ^ E_NOTICE);

include("./costanti.php");


$molto_vecchio = @is_file("./datipermanenti/connessione_db.inc");
$vecchio = @is_file("./dati/connessione_db.php");
if ($molto_vecchio) {
$numconnessione = "SI";
include ("./datipermanenti/connessione_db.inc");
$PHPR_DB_TYPE = "postgresql";
$PHPR_TAB_PRE = "";
include("./includes/funzioni_$PHPR_DB_TYPE.php");
} # fine if ($molto_vecchio)
else {
if ($vecchio) {
$numconnessione = "SI";
include ("./dati/connessione_db.php");
$PHPR_DB_TYPE = "postgresql";
$PHPR_TAB_PRE = "";
include("./includes/funzioni_$PHPR_DB_TYPE.php");
} # fine if ($vecchio)
else {
include(C_DATI_PATH."/dati_connessione.php");
include("./includes/funzioni_$PHPR_DB_TYPE.php");
$numconnessione = connetti_db($PHPR_DB_NAME,$PHPR_DB_HOST,$PHPR_DB_PORT,$PHPR_DB_USER,$PHPR_DB_PASS,$PHPR_LOAD_EXT);
} # fine else if ($vecchio)
} # fine else if ($molto_vecchio)


include("./includes/funzioni.php");
include("./includes/funzioni_aggiorna.php");



aggiorna_versione_phpr($numconnessione,"",$id_sessione,$nome_utente_phpr,$password_phpr,$anno);




?>


</div>
</body>
</html>
