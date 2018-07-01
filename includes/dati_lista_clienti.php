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




$tabelle_lock = "";
$altre_tab_lock = array($tableclienti,$tablerelclienti);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);
$num_c = 0;
$lista_c = array();
for ($num1 = 1 ; $num1 <= $num_ripeti ; $num1++) {
$id_clienti = aggslashdb($lista_clienti[$num1]);
$dati_cliente = esegui_query("select * from $tableclienti where idclienti = '$id_clienti' ");
if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI") {
$mostra_cliente = "SI";
$utente_inserimento = risul_query($dati_cliente,0,'utente_inserimento');
if ($vedi_clienti == "PROPRI" and $utente_inserimento != $id_utente) $mostra_cliente = "NO";
if ($vedi_clienti == "GRUPPI" and !$utenti_gruppi[$utente_inserimento]) $mostra_cliente = "NO";
} # fine if ($vedi_clienti == "PROPRI" or $vedi_clienti == "GRUPPI")
if (($vedi_clienti != "PROPRI" and $vedi_clienti != "GRUPPI") or $mostra_cliente != "NO") {
$num_c++;
$lista_c[$num_c] = $lista_clienti[$num1];
${"cognome_".$num_c} = risul_query($dati_cliente,0,'cognome');
# I controlli di non vuoto servono a risparmiare memoria, creando solo le variabili necessarie
if (strcmp(risul_query($dati_cliente,0,'nome'),"")) ${"nome_".$num_c} = risul_query($dati_cliente,0,'nome');
if (strcmp(risul_query($dati_cliente,0,'soprannome'),"")) ${"soprannome_".$num_c} = risul_query($dati_cliente,0,'soprannome');
if (strcmp(risul_query($dati_cliente,0,'titolo'),"")) ${"titolo_".$num_c} = risul_query($dati_cliente,0,'titolo');
if (strcmp(risul_query($dati_cliente,0,'sesso'),"")) ${"sesso_".$num_c} = risul_query($dati_cliente,0,'sesso');
if (strcmp(risul_query($dati_cliente,0,'datanascita'),"")) ${"data_nascita_".$num_c} = risul_query($dati_cliente,0,'datanascita');
if (strcmp(risul_query($dati_cliente,0,'cittanascita'),"")) ${"citta_nascita_".$num_c} = risul_query($dati_cliente,0,'cittanascita');
if (strcmp(risul_query($dati_cliente,0,'regionenascita'),"")) ${"regione_nascita_".$num_c} = risul_query($dati_cliente,0,'regionenascita');
if (strcmp(risul_query($dati_cliente,0,'nazionenascita'),"")) ${"nazione_nascita_".$num_c} = risul_query($dati_cliente,0,'nazionenascita');
if (strcmp(risul_query($dati_cliente,0,'nazionalita'),"")) ${"cittadinanza_".$num_c} = risul_query($dati_cliente,0,'nazionalita');
if (strcmp(risul_query($dati_cliente,0,'lingua'),"")) ${"codice_lingua_".$num_c} = risul_query($dati_cliente,0,'lingua');
if (strcmp(risul_query($dati_cliente,0,'nazione'),"")) ${"nazione_".$num_c} = risul_query($dati_cliente,0,'nazione');
if (strcmp(risul_query($dati_cliente,0,'regione'),"")) ${"regione_".$num_c} = risul_query($dati_cliente,0,'regione');
if (strcmp(risul_query($dati_cliente,0,'citta'),"")) ${"citta_".$num_c} = risul_query($dati_cliente,0,'citta');
if ($priv_vedi_indirizzo == "s") {
if (strcmp(risul_query($dati_cliente,0,'via'),"")) ${"via_".$num_c} = risul_query($dati_cliente,0,'via');
if (strcmp(risul_query($dati_cliente,0,'numcivico'),"")) ${"numcivico_".$num_c} = risul_query($dati_cliente,0,'numcivico');
if (strcmp(risul_query($dati_cliente,0,'cap'),"")) ${"cap_".$num_c} = risul_query($dati_cliente,0,'cap');
} # fine if ($priv_vedi_indirizzo == "s")
if (strcmp(risul_query($dati_cliente,0,'documento'),"")) ${"documento_".$num_c} = risul_query($dati_cliente,0,'documento');
if (strcmp(risul_query($dati_cliente,0,'tipodoc'),"")) ${"tipo_documento_".$num_c} = risul_query($dati_cliente,0,'tipodoc');
if (strcmp(risul_query($dati_cliente,0,'cittadoc'),"")) ${"citta_documento_".$num_c} = risul_query($dati_cliente,0,'cittadoc');
if (strcmp(risul_query($dati_cliente,0,'regionedoc'),"")) ${"regione_documento_".$num_c} = risul_query($dati_cliente,0,'regionedoc');
if (strcmp(risul_query($dati_cliente,0,'nazionedoc'),"")) ${"nazione_documento_".$num_c} = risul_query($dati_cliente,0,'nazionedoc');
if (strcmp(risul_query($dati_cliente,0,'scadenzadoc'),"")) ${"scadenza_documento_".$num_c} = risul_query($dati_cliente,0,'scadenzadoc');
if ($priv_vedi_telefoni == "s") {
if (strcmp(risul_query($dati_cliente,0,'telefono'),"")) ${"telefono_".$num_c} = risul_query($dati_cliente,0,'telefono');
if (strcmp(risul_query($dati_cliente,0,'telefono2'),"")) ${"telefono2_".$num_c} = risul_query($dati_cliente,0,'telefono2');
if (strcmp(risul_query($dati_cliente,0,'telefono3'),"")) ${"telefono3_".$num_c} = risul_query($dati_cliente,0,'telefono3');
if (strcmp(risul_query($dati_cliente,0,'fax'),"")) ${"fax_".$num_c} = risul_query($dati_cliente,0,'fax');
if (strcmp(risul_query($dati_cliente,0,'email'),"")) ${"email_".$num_c} = risul_query($dati_cliente,0,'email');
} # fine if ($priv_vedi_telefoni == "s")
if (strcmp(risul_query($dati_cliente,0,'cod_fiscale'),"")) ${"codice_fiscale_".$num_c} = risul_query($dati_cliente,0,'cod_fiscale');
if (strcmp(risul_query($dati_cliente,0,'partita_iva'),"")) ${"partita_iva_".$num_c} = risul_query($dati_cliente,0,'partita_iva');
$dati_relcliente = esegui_query("select * from $tablerelclienti where idclienti = '$id_clienti' and tipo = 'campo_pers' ");
$num_dati_relcliente = numlin_query($dati_relcliente);
for ($num2 = 0 ; $num2 < $num_dati_relcliente ; $num2++) {
${"campo_personalizzato_".risul_query($dati_relcliente,$num2,'testo1')."_".$num_c} = risul_query($dati_relcliente,$num2,'testo3');
} # fine for $num2
chiudi_query($dati_relcliente);
} # fine if (($vedi_clienti != "PROPRI" and...
chiudi_query($dati_cliente);
} # fine for $num1

$lista_clienti = $lista_c;
$num_ripeti = $num_c;
unset($lista_c);
unset($num_c);
if ($tabelle_lock) unlock_tabelle($tabelle_lock);



?>