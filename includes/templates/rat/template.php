<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2017 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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




$tableperiodi = $PHPR_TAB_PRE."periodi".$anno;
$tableprenota = $PHPR_TAB_PRE."prenota".$anno;
$tablecostiprenota = $PHPR_TAB_PRE."costiprenota".$anno;
$tablenometariffe = $PHPR_TAB_PRE."ntariffe".$anno;
$tableregole = $PHPR_TAB_PRE."regole".$anno;
$tableappartamenti = $PHPR_TAB_PRE."appartamenti";
$tablepersonalizza = $PHPR_TAB_PRE."personalizza";
$tableclienti = $PHPR_TAB_PRE."clienti";
$tabletransazioniweb = $PHPR_TAB_PRE."transazioniweb";
$tablesoldi = $PHPR_TAB_PRE."soldi".$anno;
$tableanni = $PHPR_TAB_PRE."anni";


if ($framed) {
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
        \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >
<title>$pag</title>
";
if ($file_css_frame) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$file_css_frame\" media=\"all\">
";
elseif ($extra_head_frame) echo "$extra_head_frame
";
echo "</head>
<body>
";
} # fine if ($framed)



if ($estendi_ultima_data == "SI") {
$ultima_data_menu_periodi = explode("<option value=\"",$menu_periodi);
$penultima_data_menu_periodi = substr($ultima_data_menu_periodi[(count($ultima_data_menu_periodi) - 2)],0,10);
$ultima_data_menu_periodi = substr($ultima_data_menu_periodi[(count($ultima_data_menu_periodi) - 1)],0,10);
$date_mancanti = esegui_query("select datafine from  $tableperiodi where datafine > '$ultima_data_menu_periodi' order by idperiodi");
$num_date_mancanti = numlin_query($date_mancanti);
if ($num_date_mancanti > 0) {
$id_ultima_data = esegui_query("select idperiodi from  $tableperiodi where datafine = '$ultima_data_menu_periodi'");
$id_ultima_data = risul_query($id_ultima_data,0,'idperiodi');
$id_penultima_data = esegui_query("select idperiodi from  $tableperiodi where datafine = '$penultima_data_menu_periodi'");
$id_penultima_data = risul_query($id_penultima_data,0,'idperiodi');
$intervalloperiodo = $id_ultima_data - $id_penultima_data;
if ($intervalloperiodo == 1) $num_intervallo = 1;
else $num_intervallo = 2;
for ($num1 = 0 ; $num1 < $num_date_mancanti ; $num1++) {
if ($num_intervallo == 1) {
$data_option = risul_query($date_mancanti,$num1,"datafine");
$menu_periodi .= "<option value=\"$data_option\">$data_option</option>
";
} # fine if ($num_intervallo == 1)
if ($num_intervallo == $intervalloperiodo) $num_intervallo = 1;
else $num_intervallo++;
} # fine for $num1
} # fine if ($num_date_mancanti > 0)
} # fine if ($estendi_ultima_data == "SI")

if ($mostra_date_passate == "NO") {
$oggi = date("Y-m-d",(time() + (C_DIFF_ORE * 3600)));
$date_menu_periodi = explode("<option value=\"",$menu_periodi);
$num_date_menu_periodi = (count($date_menu_periodi) - 2);
$nuova_prima_data = "";
for ($num1 = 1 ; $num1 < $num_date_menu_periodi ; $num1++) {
$data_corr = substr($date_menu_periodi[$num1],0,10);
if ($data_corr >= $oggi) break;
else $nuova_prima_data = $data_corr;
} # fine for $num1
if ($nuova_prima_data) $menu_periodi = strstr(strstr($menu_periodi,$nuova_prima_data),"<option value=");
} # fine if ($mostra_date_passate == "NO")

unset($inizioperiodi);
unset($fineperiodi);
unset($inizioperiodi_id);
unset($fineperiodi_id);
unset($intervalloperiodi);
$menu_periodi_vett = explode("<option value=\"",$menu_periodi);
$num_menu_periodi_vett = count($menu_periodi_vett);
$inizioperiodi[0] = substr($menu_periodi_vett[1],0,10);
if ($tipo_periodi == "s") $intervallo_base = 604800;
else $intervallo_base = 86400;
$data_prec = explode("-",$inizioperiodi[0]);
$data_corr = explode("-",substr($menu_periodi_vett[2],0,10));
$intervallo_prec = round(((mktime(0,0,0,$data_corr[1],$data_corr[2],$data_corr[0]) - mktime(0,0,0,$data_prec[1],$data_prec[2],$data_prec[0])) / $intervallo_base),0);
$intervalloperiodi[0] = $intervallo_prec;
$num_periodi_date = 0;
for ($num1 = 2 ; $num1 < $num_menu_periodi_vett ; $num1++) {
$data_corr = explode("-",substr($menu_periodi_vett[$num1],0,10));
$intervallo_corr = round(((mktime(0,0,0,$data_corr[1],$data_corr[2],$data_corr[0]) - mktime(0,0,0,$data_prec[1],$data_prec[2],$data_prec[0])) / $intervallo_base),0);
if ($intervallo_corr != $intervallo_prec) {
$data_succ = explode("-",substr($menu_periodi_vett[($num1 + 1)],0,10));
$intervallo_succ = round(((mktime(0,0,0,$data_succ[1],$data_succ[2],$data_succ[0]) - mktime(0,0,0,$data_corr[1],$data_corr[2],$data_corr[0])) / $intervallo_base),0);
$fineperiodi[$num_periodi_date] = $data_prec[0]."-".$data_prec[1]."-".$data_prec[2];
$num_periodi_date++;
$inizioperiodi[$num_periodi_date] = $data_corr[0]."-".$data_corr[1]."-".$data_corr[2];
$intervalloperiodi[$num_periodi_date] = $intervallo_succ;
$intervallo_prec = $intervallo_succ;
} # fine if ($intervallo_corr != $intervallo_prec)
$data_prec = $data_corr;
} # fine for $num1
$fineperiodi[$num_periodi_date] = substr($menu_periodi_vett[($num1 - 1)],0,10);
$num_periodi_date++;






$tabelle_lock = "";
$altre_tab_lock = array($tablenometariffe,$tableperiodi,$tableregole);
$tabelle_lock = lock_tabelle($tabelle_lock,$altre_tab_lock);

$periodi = esegui_query("select * from $tableperiodi order by idperiodi");
$rigatariffe = esegui_query("select * from $tablenometariffe where idntariffe = 1 or idntariffe = 2 order by idntariffe ");
$dati_tariffe = dati_tariffe($tablenometariffe,"","",$tableregole);
$dati_r2 = "";
dati_regole2($dati_r2,$app_regola2_predef,"","","",$id_periodo_corrente,$tipo_periodi,$anno,$tableregole);
$dati_ca = dati_costi_agg_ntariffe($tablenometariffe,$dati_tariffe['num'],"NO");

unlock_tabelle($tabelle_lock);


$num_tariffe_mostra = 0;

for ($num1 = 1 ; $num1 <= $dati_tariffe['num'] ; $num1++) {
if ($tariffe_mostra[$num1] == "SI") {
$num_tariffe_mostra++;
${"numtariffa".$num_tariffe_mostra} = $num1;
$tariffa[$num_tariffe_mostra] = "tariffa".${"numtariffa".$num_tariffe_mostra};
$nometariffa[$num_tariffe_mostra] = $dati_tariffe[$tariffa[$num_tariffe_mostra]]['nome'];
if ($nometariffa[$num_tariffe_mostra] == "") {
$nometariffa[$num_tariffe_mostra] = $tariffa[$num_tariffe_mostra];
$nometariffa_vedi[$num_tariffe_mostra] = $fr_tariffa.${"numtariffa".$num_tariffe_mostra};
} # fine if ($nometariffa == "")
else $nometariffa_vedi[$num_tariffe_mostra] = $nometariffa[$num_tariffe_mostra];
if ($n_tariffe_imposte[$num1]) $nometariffa_vedi[$num_tariffe_mostra] = $n_tariffe_imposte[$num1];
if (num_caratteri_testo($nometariffa_vedi[$num_tariffe_mostra]) > 10) $nometariffa_vedi[$num_tariffe_mostra] = "<small>".$nometariffa_vedi[$num_tariffe_mostra]."</small>";
if ($dati_tariffe[$tariffa[$num_tariffe_mostra]]['moltiplica'] == "p") $per_persona[$num_tariffe_mostra] = 1;
else $per_persona[$num_tariffe_mostra] = 0;
} # fine if ($tariffe_mostra[$num1] == "SI")
} # fine for $num1

$num_colonne_periodi = 0;
$num_periodi = numlin_query($periodi);
$tariffa_colonna_periodo = array();
$tariffap_colonna_periodo = array();
$tariffa_chiusa = array();
for ($num1 = 0 ; $num1 < $num_periodi ; $num1++) {
$datainizio_db[$num1] = risul_query($periodi,$num1,'datainizio');
$datafine_db[$num1] = risul_query($periodi,$num1,'datafine');
} # fine for $num1

for ($num_p = 0 ; $num_p < $num_periodi_date ; $num_p++) {
$nuova_colonna = "NO";
for ($num1 = 0 ; $num1 < $num_periodi ; $num1++) {
if ($datainizio_db[$num1] == $inizioperiodi[$num_p]) $inizioperiodi_id[$num_p] = $num1;
if ($datafine_db[$num1] == $fineperiodi[$num_p]) $fineperiodi_id[$num_p] = $num1;
} # fine for $num1
$ini_colonna_periodo[$num_colonne_periodi] = $inizioperiodi[$num_p];
$ini_colonna_periodo[$num_colonne_periodi] = formatta_data($ini_colonna_periodo[$num_colonne_periodi],$stile_data);
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if ($tariffe_mostra[${"numtariffa".$num1}] == "SI") {
$tariffa_colonna_periodo[$num1][$num_colonne_periodi] = (double) risul_query($periodi,$inizioperiodi_id[$num_p],$tariffa[$num1]);
$tariffap_colonna_periodo[$num1][$num_colonne_periodi] = (double) risul_query($periodi,$inizioperiodi_id[$num_p],$tariffa[$num1]."p");
if ($dati_r2['napp'][$tariffa[$num1]] > 1) {
$tariffa_colonna_periodo[$num1][$num_colonne_periodi] = $tariffa_colonna_periodo[$num1][$num_colonne_periodi] * (double) $dati_r2['napp'][$tariffa[$num1]];
#$tariffap_colonna_periodo[$num1][$num_colonne_periodi] = $tariffap_colonna_periodo[$num1][$num_colonne_periodi] * (double) $dati_r2['napp'][$tariffa[$num1]];
} # fine if ($dati_r2['napp'][$tariffa[$num1]] > 1)
if ($dati_tariffe[$tariffa[$num1]]['chiusa'][($inizioperiodi_id[$num_p] + 1)]) {
$tariffa_colonna_periodo[$num1][$num_colonne_periodi] = "c";
$tariffap_colonna_periodo[$num1][$num_colonne_periodi] = "c";
} # fine if ($dati_tariffe[$tariffa[$num1]]['chiusa'][($inizioperiodi_id[$num_p] + 1)])
for ($num_i = 1 ; $num_i < $intervalloperiodi[$num_p] ; $num_i++) {
if ($tariffa_colonna_periodo[$num1][$num_colonne_periodi] == "c") break;
$tar_corr = (double) risul_query($periodi,($inizioperiodi_id[$num_p] + $num_i),$tariffa[$num1]);
$tarp_corr = (double) risul_query($periodi,($inizioperiodi_id[$num_p] + $num_i),$tariffa[$num1]."p");
if ($dati_r2['napp'][$tariffa[$num1]] > 1) {
$tar_corr = $tar_corr * (double) $dati_r2['napp'][$tariffa[$num1]];
#$tarp_corr = $tarp_corr * (double) $dati_r2['napp'][$tariffa[$num1]];
} # fine if ($dati_r2['napp'][$tariffa[$num1]] > 1)
$tariffa_colonna_periodo[$num1][$num_colonne_periodi] += $tar_corr;
$tariffap_colonna_periodo[$num1][$num_colonne_periodi] += $tarp_corr;
if ($dati_tariffe[$tariffa[$num1]]['chiusa'][($inizioperiodi_id[$num_p] + $num_i + 1)]) {
$tariffa_colonna_periodo[$num1][$num_colonne_periodi] = "c";
$tariffap_colonna_periodo[$num1][$num_colonne_periodi] = "c";
} # fine if ($dati_tariffe[$tariffa[$num1]]['chiusa'][($inizioperiodi_id[$num_p] + $num_i + 1)])
} # fine for $num_i
} # fine if ($tariffe_mostra[${"numtariffa".$num1}] == "SI")
} # fine for $num1

$datafine = $datafine_db[$inizioperiodi_id[$num_p] + $intervalloperiodi[$num_p] - 1];
for ($num1 = ($inizioperiodi_id[$num_p] + $intervalloperiodi[$num_p]) ; $num1 <= $fineperiodi_id[$num_p] ; $num1++) {
for ($num2 = 1 ; $num2 <= $num_tariffe_mostra ; $num2++) {
if ($tariffe_mostra[${"numtariffa".$num2}] == "SI") {
${$tariffa[$num2]} = (double) risul_query($periodi,$num1,$tariffa[$num2]);
${$tariffa[$num2]."p"} = (double) risul_query($periodi,$num1,$tariffa[$num2]."p");
if ($dati_r2['napp'][$tariffa[$num2]] > 1) {
${$tariffa[$num2]} = ${$tariffa[$num2]} * (double) $dati_r2['napp'][$tariffa[$num2]];
#${$tariffa[$num2]."p"} = ${$tariffa[$num2]."p"} * (double) $dati_r2['napp'][$tariffa[$num2]];
} # fine if ($dati_r2['napp'][$tariffa[$num2]] > 1)
if ($dati_tariffe[$tariffa[$num2]]['chiusa'][($num1 + 1)]) {
${$tariffa[$num2]} = "c";
${$tariffa[$num2]."p"} = "c";
} # fine if ($dati_tariffe[$tariffa[$num2]]['chiusa'][($num1 + 1)])
for ($num_i = 1 ; $num_i < $intervalloperiodi[$num_p] ; $num_i++) {
if (${$tariffa[$num2]} == "c") break;
$tar_corr = (double) risul_query($periodi,($num1 + $num_i),$tariffa[$num2]);
$tarp_corr = (double) risul_query($periodi,($num1 + $num_i),$tariffa[$num2]."p");
if ($dati_r2['napp'][$tariffa[$num2]] > 1) {
$tar_corr = $tar_corr * (double) $dati_r2['napp'][$tariffa[$num2]];
#$tarp_corr = $tarp_corr * (double) $dati_r2['napp'][$tariffa[$num2]];
} # fine if ($dati_r2['napp'][$tariffa[$num2]] > 1)
${$tariffa[$num2]} += $tar_corr;
${$tariffa[$num2]."p"} += $tarp_corr;
if ($dati_tariffe[$tariffa[$num2]]['chiusa'][($num1 + $num_i + 1)]) {
${$tariffa[$num2]} = "c";
${$tariffa[$num2]."p"} = "c";
} # fine if ($dati_tariffe[$tariffa[$num2]]['chiusa'][($num1 + $num_i + 1)])
} # fine for $num_i
if (${$tariffa[$num2]} != $tariffa_colonna_periodo[$num2][$num_colonne_periodi] or ${$tariffa[$num2]."p"} != $tariffap_colonna_periodo[$num2][$num_colonne_periodi]) $nuova_colonna = "SI";
} # fine if ($tariffe_mostra[${"numtariffa".$num2}] == "SI")
} # fine for $num2
$datainizio = $datainizio_db[$num1];
for ($num_i = 1 ; $num_i < $intervalloperiodi[$num_p] ; $num_i++) $num1++;
$datafine = $datafine_db[$num1];
if ($nuova_colonna == "SI") {
$datainizio = formatta_data($datainizio,$stile_data);
$fine_colonna_periodo[$num_colonne_periodi] = $datainizio;
$num_colonne_periodi++;
$ini_colonna_periodo[$num_colonne_periodi] = $datainizio;
for ($num2 = 1 ; $num2 <= $num_tariffe_mostra ; $num2++) {
if ($tariffe_mostra[${"numtariffa".$num2}] == "SI") {
$tariffa_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]};
$tariffap_colonna_periodo[$num2][$num_colonne_periodi] = ${$tariffa[$num2]."p"};
} # fine if ($tariffe_mostra[${"numtariffa".$num2}] == "SI")
} # fine for $num2
$nuova_colonna = "NO";
} # fine if ($nuova_colonna == "SI")
} # fine for $num1
$datafine = formatta_data($datafine,$stile_data);
$fine_colonna_periodo[$num_colonne_periodi] = $datafine;
$num_colonne_periodi++;
} # fine for $num_p

echo "<div class=\"rates_cont\"><table class=\"rates\" $stile_tabella_tariffe>
<tr class=\"row_dates\"><td>&nbsp;</td>";
if ($fr_dal) $fr_dal_br = $fr_dal."<br>";
else $fr_dal_br = "";
for ($num1 = 0 ; $num1 < $num_colonne_periodi ; $num1++) {
echo "<td><small>$apertura_tag_font$fr_dal_br".$ini_colonna_periodo[$num1]."
<br>$fr_al<br>".$fine_colonna_periodo[$num1]."$chiusura_tag_font</small></td>";
} # fine for $num1
if ($mostra_caparra == "SI") echo "<td><small>$apertura_tag_font$fr_Caparra$chiusura_tag_font</small></td>";
echo "</tr>
";
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if ($tariffe_mostra[${"numtariffa".$num1}] == "SI") {
echo "<tr class=\"row$num1\"><td>$apertura_tag_font".$nometariffa_vedi[$num1];
$per_p_vedi = "";
if ($per_persona[$num1]) {
$fissotariffa = esegui_query("select idperiodi from $tableperiodi where tariffa$num1 is not NULL and tariffa$num1 != '' and tariffa$num1 != '0' ");
if (numlin_query($fissotariffa)) $per_p_vedi = $fr_per_p;
else echo "<br><small><small>($fr_a_persona)</small></small>";
} # fine if ($per_persona[$num1])
echo "$chiusura_tag_font</td>";
for ($num2 = 0 ; $num2 < $num_colonne_periodi ; $num2++) {
if ((!$tariffa_colonna_periodo[$num1][$num2] and !$tariffap_colonna_periodo[$num1][$num2]) or (string) $tariffa_colonna_periodo[$num1][$num2] == "c") $tariffa_vedi = "&nbsp;";
else {
if ($tariffa_colonna_periodo[$num1][$num2]) $tariffa_vedi = punti_in_num($tariffa_colonna_periodo[$num1][$num2],$stile_soldi);
if ($tariffap_colonna_periodo[$num1][$num2]) {
if ($tariffa_colonna_periodo[$num1][$num2]) $tariffa_vedi .= " + ";
else $tariffa_vedi = "";
$tariffa_vedi .= punti_in_num($tariffap_colonna_periodo[$num1][$num2],$stile_soldi).$per_p_vedi;
} # fine if ($tariffap_colonna_periodo[$num1][$num_colonne_periodi])
} # fine if (!$tariffa_colonna_periodo[$num1][$num_colonne_periodi] and !$tariffap_colonna_periodo[$num1][$num_colonne_periodi])
echo "<td>$apertura_tag_font".$tariffa_vedi."$chiusura_tag_font</td>";
} # fine for $num2
if ($mostra_caparra == "SI") {
$caparra_percent = $dati_tariffe["tariffa".${"numtariffa".$num1}]['caparra_percent'];
$caparra_arrotond = $dati_tariffe["tariffa".${"numtariffa".$num1}]['caparra_arrotond'];
if (!$caparra_percent) $caparra_percent = 0;
echo "<td>$caparra_percent";
if ($caparra_arrotond == "val") {
if ($caparra_percent != 1) echo " $fr_Euros";
else echo " $fr_Euro";
} # fine if ($caparra_arrotond == "val")
if ($caparra_arrotond == "gio") {
if ($caparra_percent != 1) echo " $fr_parola_settimane";
else echo " $fr_parola_settimana";
} # fine if ($caparra_arrotond == "gio")
if ($caparra_arrotond != "val" and $caparra_arrotond != "gio") echo "%";
echo "</td>";
} # fine if ($mostra_caparra == "SI")
echo "</tr>
";
} # fine if ($tariffe_mostra[${"numtariffa".$num1}] == "SI")
} # fine for $num1
echo "</table></div>";



$lista_tutte_tariffe = "";
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if ($tariffe_mostra[${"numtariffa".$num1}] == "SI") $lista_tutte_tariffe .= $nometariffa_vedi[$num1].", ";
} # fine for $num1
$lista_tutte_tariffe = substr($lista_tutte_tariffe,0,-2);

$testo_costi_agg = "";
for ($numca = 0 ; $numca < $dati_ca['num'] ; $numca++) {
$idcostoagg = $dati_ca[$numca]['id'];
if ($costi_agg_mostra[$idcostoagg] == "SI") {
if ($n_costi_agg_imposti[$idcostoagg]) $nomecosto_imposto = $n_costi_agg_imposti[$idcostoagg];
else $nomecosto_imposto = $dati_ca[$numca]['nome'];
$numcostoagg = "costoagg".($numca + 1);
$nome_idcostoagg = "idcostoagg".($numca + 1);
if (${$nome_idcostoagg."_".$n_t} == $idcostoagg and ${$numcostoagg."_".$n_t} == "SI") $checked = " checked";
else $checked = "";
$testo_costi_agg .= "<li>$apertura_tag_font<input type=\"hidden\" name=\"$nome_idcostoagg"."_$n_t\" value=\"$idcostoagg\">
 <b>$nomecosto_imposto</b>:";
if ($dati_ca[$numca]['tipo_val'] == "f" or $dati_ca[$numca]['valore']) {
if ($anteponi_nome_valuta != "SI") $testo_costi_agg .= " ".$dati_ca[$numca]['valore'];
if ($dati_ca[$numca]['valore'] != 1) $testo_costi_agg .= " $fr_Euros";
if ($dati_ca[$numca]['valore'] == 1) $testo_costi_agg .= " $fr_Euro";
if ($anteponi_nome_valuta == "SI") $testo_costi_agg .= $dati_ca[$numca]['valore'];
} # fine ($dati_ca[$numca]['tipo_val'] == "f" or $dati_ca[$numca]['valore'])
if ($dati_ca[$numca]['tipo_val'] != "f") {
if ($dati_ca[$numca]['valore']) $testo_costi_agg .= " +";
$testo_costi_agg .= " ".$dati_ca[$numca]['valore_perc'];
if ($dati_ca[$numca]['tipo_val'] == "p") $testo_costi_agg .= "% $fr_della_tariffa";
if ($dati_ca[$numca]['tipo_val'] == "q") $testo_costi_agg .= "% $fr_della_tariffa_senza_persone";
if ($dati_ca[$numca]['tipo_val'] == "s") $testo_costi_agg .= "% $fr_della_tariffa_di_una_persona";
if ($dati_ca[$numca]['tipo_val'] == "t") $testo_costi_agg .= "% $fr_del_prezzo_totale";
if ($dati_ca[$numca]['tipo_val'] == "c") $testo_costi_agg .= "% $fr_della_caparra";
if ($dati_ca[$numca]['tipo_val'] == "r") $testo_costi_agg .= "% $fr_del_resto_della_caparra";
} # fine if ($dati_ca[$numca]['tipo_val'] != "f")
if ($dati_ca[$numca]['tipo'] == "s") $testo_costi_agg .= " $fr_alla_settimana";
if ($dati_ca[$numca]['moltiplica'] == "p" or $dati_ca[$numca]['moltiplica'] == "t") $testo_costi_agg .= " $fr_a_persona";
$lista_tariffe_ass = "";
for ($num1 = 1 ; $num1 <= $num_tariffe_mostra ; $num1++) {
if ($tariffe_mostra[${"numtariffa".$num1}] == "SI") {
if ($dati_ca[$numca]["tariffa".${"numtariffa".$num1}]) $lista_tariffe_ass .= $nometariffa_vedi[$num1].", ";
} # fine if ($tariffe_mostra[${"numtariffa".$num1}] == "SI")
} # fine for $num1
$lista_tariffe_ass = substr($lista_tariffe_ass,0,-2);
if ($lista_tutte_tariffe and $lista_tariffe_ass == $lista_tutte_tariffe) $testo_costi_agg .= " ($fr_obbligatorio_con $fr_tutte_le_tariffe)";
else if ($lista_tariffe_ass) $testo_costi_agg .= " ($fr_obbligatorio_con $lista_tariffe_ass)";
$testo_costi_agg .= ".$chiusura_tag_font</li>";
} # fine if ($costi_agg_mostra[$idcostoagg] == "SI")
} # fine for $numca

if ($testo_costi_agg) {
echo "<br><div class=\"extra_costs\">
$fr_Costi_aggiuntivi:
<ul class=\"extra_costs\">
$testo_costi_agg
</ul></div>";
} # fine if ($testo_costi_agg)


if ($framed) {
echo "
</body>
</html>
";
} # fine if ($framed)



?>
