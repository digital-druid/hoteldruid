//    HOTELDRUID
//    Copyright (C) 2001-2018 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU Affero General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    any later version accepted by Marco Maria Francesco De Santis, which
//    shall act as a proxy as defined in Section 14 of version 3 of the
//    license.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU Affero General Public License for more details.
//
//    You should have received a copy of the GNU Affero General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.



function punti_in_num (num) {
var n = String(num);
var lung = n.length;
var lett;
var prima = '';
var dopo = '';
var pun = 0;
for (var n1 = 0 ; n1 < lung ; n1++) {
lett = ''+n.substr(n1,1);
if (lett == '.') pun = 1;
else {
if (pun) dopo += lett;
else prima += lett;
}
}
n = prima;
prima = '';
var n2 = 0;
for (n1 = (n.length - 1) ; n1 >= 0 ; n1--) {
lett = ''+n.substr(n1,1);
n2++;
if (n2 > 3) {
prima = punto+prima;
n2 = 0;
}
prima = lett+prima;
}
dopo = dopo.substr(0,2);
if (dopo.length == 0) dopo = '00';
if (dopo.length == 1) dopo += '0';
return prima+virgola+dopo;
}



function elimina_pos_vett (pos,vett) {
var n_vett = new Array();
var n1 = 0;
for (var n2 = 0 ; n2 < vett.length ; n2++) {
if (n2 != pos) {
n_vett[n1] = vett[n2];
n1++;
}
}
return n_vett
}



function agg_colore_sel (ncol) {
var sel = document.getElementById('colsel'+ncol);
var colsel = sel.options[sel.selectedIndex].value;
document.getElementById('coltxt'+ncol).value = colsel;
sel.style.backgroundColor = colsel;
}



function agg_colore_sel_txt (ncol) {
var sel = document.getElementById('colsel'+ncol);
var colsel = document.getElementById('coltxt'+ncol).value;
var colre1 = new RegExp('^#[0-9a-f]{3,3}$','i');
var colre2 = new RegExp('^#[0-9a-f]{6,6}$','i');
if (colre1.test(colsel) || colre2.test(colsel)) sel.style.backgroundColor = colsel;
}



// punto_vendita.php functions

function aggiungi_linea_pv (id,nome,molt,val,calcolab,moltiplicab) {
ultimo_costo++;
calcolabile[ultimo_costo] = calcolab;
moltiplicabile[ultimo_costo] = moltiplicab;
id_costo[ultimo_costo] = id;
nome_costo[ultimo_costo] = nome;
molt_costo[ultimo_costo] = molt;
val_costo[ultimo_costo] = val;
var tab_costi = document.getElementById('tab_costi');
var riga = tab_costi.insertRow(-1);
riga.style.backgroundColor = colore_corr;
if (colore_corr == t2row1color) colore_corr = t2row2color;
else colore_corr = t2row1color;
var cell1 = riga.insertCell(-1);
var cell2 = riga.insertCell(-1);
var cell3 = riga.insertCell(-1);
cell1.width = '34px';
cell1.height = '34px';
var html_button = '<button class=\"pos\" type=\"submit\" name=\"canc_costo\" value=\"'+ultimo_costo+'\" onclick=\"return canc_cos_pv('+ultimo_costo;
html_button += ');\" style=\"padding: 0;\"><img style=\"display: block; padding: 0; border: 0; margin: 0;\" src=\".\/img\/croce.gif\" alt=\"X\"><\/button>';
cell1.innerHTML = html_button;
cell2.innerHTML = nome;
if (molt && molt != 1) cell2.innerHTML += ' <b>x'+molt+'<\/b>';
cell3.width = '40px';
cell3.style.textAlign = 'right';
cell3.innerHTML = punti_in_num(val);
totale = Math.round((Number(totale) + Number(val)) * 1000) / 1000;
var txt_tot = punti_in_num(totale);
if (tot_indef) {
if (totale > 0)  txt_tot += ' + ?';
else txt_tot = '?';
}
document.getElementById('tot_costi').innerHTML = txt_tot;
if (id_costi != '') id_costi += ',';
else document.getElementById('incassa').disabled = 0;
id_costi += ''+id;
if (molt && molt > 1) id_costi += 'x'+molt;
document.getElementById('id_costi').value = id_costi;
}



function elimina_linea_pv (num_costo) {
document.getElementById('tab_costi').deleteRow(num_costo);
if (colore_corr == t2row1color) colore_corr = t2row2color;
else colore_corr = t2row1color;
totale = Number(totale) - Number(val_costo[num_costo]);
var txt_tot = punti_in_num(totale);
if (tot_indef) {
if (totale > 0)  txt_tot += ' + ?';
else txt_tot = '?';
}
document.getElementById('tot_costi').innerHTML = txt_tot;
var id_costi_vett = id_costi.split(',');
id_costi_vett = elimina_pos_vett(num_costo,id_costi_vett);
id_costi = id_costi_vett.join(',');
document.getElementById('id_costi').value = id_costi;
if (id_costi == '') document.getElementById('incassa').disabled = 1;
calcolabile = elimina_pos_vett(num_costo,calcolabile);
moltiplicabile = elimina_pos_vett(num_costo,moltiplicabile);
id_costo = elimina_pos_vett(num_costo,id_costo);
nome_costo = elimina_pos_vett(num_costo,nome_costo);
molt_costo = elimina_pos_vett(num_costo,molt_costo);
val_costo = elimina_pos_vett(num_costo,val_costo);
ultimo_costo--;
var col_lin = colore_corr;
for (var n1 = ultimo_costo ; n1 >= num_costo ; n1--) {
var linea = document.getElementById('tab_costi').rows[n1].firstChild.innerHTML;
linea = linea.replace('value=\"'+(n1 + 1)+'\"','value=\"'+n1+'\"');
linea = linea.replace('canc_cos_pv('+(n1 + 1)+')','canc_cos_pv('+n1+')');
document.getElementById('tab_costi').rows[n1].firstChild.innerHTML = linea;
if (col_lin == t2row1color) col_lin = t2row2color;
else col_lin = t2row1color;
document.getElementById('tab_costi').rows[n1].style.backgroundColor = col_lin;
}
}



function ins_cos_pv (id,nome,molt,val,calcolab,moltiplicab) {
aggiungi_linea_pv(id,nome,molt,val,calcolab,moltiplicab);
window.location.hash = 'finetab'; 
return false;
}



function canc_cos_pv (num) {
elimina_linea_pv(num);
return false;
}



function aggiungi_costi_pv (num_agg) {
if (calcolabile[ultimo_costo] != 1) return true;
var costo_corr = ultimo_costo;
if (moltiplicabile[costo_corr] != 1) {
for (var n1 = 0 ; n1 < num_agg ; n1++) {
aggiungi_linea_pv(id_costo[costo_corr],nome_costo[costo_corr],molt_costo[costo_corr],val_costo[costo_corr],calcolabile[costo_corr],moltiplicabile[costo_corr]);
}
}
else {
var id = id_costo[costo_corr];
var nome = nome_costo[costo_corr];
var val = Number(val_costo[costo_corr]);
var molt = molt_costo[costo_corr];
if (!molt) molt = 1;
molt = Number(molt);
var val_base = Math.round((val / molt) * 100) / 100;
molt += Number(num_agg);
val = val_base * molt;
var calcolab = calcolabile[costo_corr];
var moltiplicab = moltiplicabile[costo_corr];
elimina_linea_pv(ultimo_costo);
aggiungi_linea_pv(id,nome,molt,val,calcolab,moltiplicab);
}
window.location.hash = 'finetab'; 
return false;
}



function moltiplica_costi_pv (num_molt) {
if (calcolabile[ultimo_costo] != 1) return true;
if (moltiplicabile[ultimo_costo] != 1) aggiungi_costi_pv(num_molt - 1);
else {
var molt = molt_costo[ultimo_costo];
if (!molt) molt = 1;
molt = Number(molt);
var n_molt = Number(num_molt) * molt;
aggiungi_costi_pv(n_molt - molt);
}
return false;
}



function sottrai_costi_pv (num_sott) {
if (calcolabile[ultimo_costo] != 1) return true;
if (moltiplicabile[ultimo_costo] != 1) elimina_linea_pv(ultimo_costo);
else {
var id = id_costo[ultimo_costo];
var nome = nome_costo[ultimo_costo];
var val = Number(val_costo[ultimo_costo]);
var molt = molt_costo[ultimo_costo];
if (!molt) molt = 1;
molt = Number(molt);
var val_base = Math.round((val / molt) * 100) / 100;
molt = molt - Number(num_sott);
var calcolab = calcolabile[ultimo_costo];
var moltiplicab = moltiplicabile[ultimo_costo];
elimina_linea_pv(ultimo_costo);
if (molt > 0) {
val = val_base * molt;
aggiungi_linea_pv(id,nome,molt,val,calcolab,moltiplicab);
}
}
return false;
}


// funzioni per checkbox, radiobox, ecc.

function agg_ckbx (id_cb) {
var cb = document.getElementById(id_cb);
if (cb.checked) cb.checked = false;
else cb.checked = true;
if (cb.onchange) cb.onchange();
}



function agn_ckbx (n_cb) {
var cb = document.getElementsByName(n_cb);
if (cb[0].checked) cb[0].checked = false;
else cb[0].checked = true;
if (cb[0].onchange) cb[0].onchange();
}



function asso_rdbx (n_cb,id_rb,id_cond) {
var cb = document.getElementsByName(n_cb);
if (cb[0].checked) {
if (id_cond === undefined) document.getElementById(id_rb).checked = '1';
else if (document.getElementById(id_cond).checked) document.getElementById(id_rb).checked = '1';
}
}



// funzioni tabella mese
var ArCoOr = new Array();



function colora_date (c_ini,c_fine,colore) {
var n1 = 0;
var n2 = 0;
var n3 = 0;
var caselle = '';
var righe = document.getElementsByTagName('tr');
for (n1 = 0 ; n1 < righe.length ; n1++) {
if (righe[n1].className == 'rd_r') {
caselle = righe[n1].getElementsByTagName('td');
for (n2 = 0 ; n2 < caselle.length ; n2++) {
if (caselle[n2].className == 'rd_'+c_ini) {
for (n3 = 0 ; n3 <= (c_fine - c_ini) ; n3++) {
if (ArCoOr[(c_ini + n3)] == null) ArCoOr[(c_ini + n3)] = caselle[(n2 + n3)].style.backgroundColor;
if (colore == colore_date_norm) caselle[(n2 + n3)].style.backgroundColor = ArCoOr[(c_ini + n3)];
else caselle[(n2 + n3)].style.backgroundColor = colore;
} // for n3
break;
} // if
} // for n2
} // if
} // for n1
} // fine function colora_date



function attiva_colora_date (allinea_tab_mesi) {
var n0 = 0;
var n1 = 0;
var n2 = 0;
ArCoOr.length = 0;
var caselle = '';
var tabelle = document.getElementsByTagName('table');
for (n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 'm1') {
var righe = tabelle[n0].childNodes[0].childNodes;
for (n1 = 0 ; n1 < righe.length ; n1++) {
if (righe[n1].className != 'rd_r') {
caselle = righe[n1].childNodes;
c_fine = 0;
for (n2 = 1 ; n2 < (caselle.length - 1) ; n2++) {
c_ini = c_fine;
c_fine = (caselle[n2].colSpan / 2);
c_fine = c_ini + c_fine;
if (allinea_tab_mesi == 'SI') c_fine = c_fine - 1;
caselle[n2].onmouseover = new Function("colora_date("+c_ini+","+c_fine+",'"+colore_date_sel+"');");
caselle[n2].onmouseout = new Function("colora_date("+c_ini+","+c_fine+",'"+colore_date_norm+"');");
if (allinea_tab_mesi == 'SI') c_fine = c_fine + 1;
} // for n2
} // if
} // for n1
} // if
} // for n0
} // fine function attiva_colora_date



var entrato = 0;
var caselle_col = '';
var pren_ini = '';
var pren_fine = '';
var pren_drag = '';
var id_pren_drag = '';
var pren_start = '';
var pren_end = '';
var app_drag = '';
var colore_orig0 = '';
var colore_orig1 = '';
var colore_orig2 = '';
var ArInPr = new Array();
var ArFiPr = new Array();
var ArLiDa = new Array();
var ArObDa = new Array();



function drg (ev) {
ev.dataTransfer.setData('Text',ev.target.id);
id_pren_drag = ev.target.id;
pren_drag = ev.target.id.substr(3);
app_drag = ev.target.parentNode;
var first_ch = 0;
if (app_drag.firstChild == ev.target) first_ch = 1;
while (app_drag.id.substr(0,3) != 'app') {
if (app_drag.id.substr(0,3) == 'prn') {
if (first_ch) pren_start = app_drag.id.substr(3);
else pren_end = app_drag.id.substr(3);
id_pren_drag = app_drag.id;
}
app_drag = app_drag.parentNode;
}
app_drag = app_drag.id;
colore_orig0 = document.getElementById(id_pren_drag).style.backgroundColor;
document.getElementById(id_pren_drag).style.backgroundColor = colore_drp1;
if (pren_start || pren_end) {
ev.target.style.position = 'relative';
ev.target.style.zIndex = '100';
for (n2 = 1 ; n2 < (caselle_col.length - 1) ; n2++) {
document.getElementById(caselle_col[n2].className).style.width = caselle_col[n2].offsetWidth+'px';
}
}
pren_ini = ArInPr[id_pren_drag.substr(3)] + 1;
pren_fine = ArFiPr[id_pren_drag.substr(3)] + 1;
}



function drag_over_tr (id) {
if (pren_drag) {
var cas1 = document.getElementById(id).firstChild;
var cas2 = document.getElementById(id).lastChild;
if (!entrato) {
colore_orig1 = cas1.style.backgroundColor;
colore_orig2 = cas2.style.backgroundColor;
}
entrato = 1;
if (app_drag != id && priv_mod_aa != 'n') {
if (ApAs[pren_drag] == 'v' || ApAs[pren_drag].search(','+arr_app[id.substr(3)]+',') >= 0) {
cas1.style.backgroundColor = colore_drp1;
cas2.style.backgroundColor = colore_drp1;
}
else if (priv_mod_aa == 's') {
cas1.style.backgroundColor = colore_drp2;
cas2.style.backgroundColor = colore_drp2;
}
}
}
}



function drag_out_tr (id) {
if (pren_drag) {
document.getElementById(id).firstChild.style.backgroundColor = colore_orig1;
document.getElementById(id).lastChild.style.backgroundColor = colore_orig2;
entrato = 0;
}
}



function drag_over_td (ncol) {
if (pren_start || pren_end) {
if (!entrato) {
for (n1 = 0 ; n1 < ArObDa.length ; n1++) {
var caselle = ArObDa[n1].childNodes;
for (n2 = 1 ; n2 < (caselle.length - 1) ; n2++) {
var colore = colore_date_norm;
if (pren_start && ((n2 <= pren_fine && n2 >= ncol) || n2 == (pren_fine - 1) || n2 == pren_fine)) colore = colore_drp1;
if (pren_end && ((n2 >= pren_ini && n2 <= ncol) || n2 == pren_ini || n2 == (pren_ini + 1))) colore = colore_drp1;
if (colore == colore_drp1 && (n2 < pren_ini || n2 > pren_fine)) colore = colore_drp2;
if (colore == colore_date_norm && n2 >= pren_ini && n2 <= pren_fine) colore = colore_date_sel;
caselle[n2].style.backgroundColor = colore;
}
} // for n1
}
entrato = 1;
}
}



function drag_out_td (ncol) {
if (pren_start || pren_end) {
for (n1 = 0 ; n1 < ArObDa.length ; n1++) {
var caselle = ArObDa[n1].childNodes;
for (n2 = 1 ; n2 < (caselle.length - 1) ; n2++) caselle[n2].style.backgroundColor = colore_date_norm;
} // for n1
entrato = 0;
}
}



function drp_out () {
document.getElementById(id_pren_drag).style.backgroundColor = colore_orig0;
if (pren_start || pren_end) {
for (n1 = 1 ; n1 < (caselle_col.length - 1) ; n1++) {
document.getElementById(caselle_col[n1].className).style.width = '0';
}
for (n1 = 0 ; n1 < ArObDa.length ; n1++) {
var caselle = ArObDa[n1].childNodes;
for (n2 = 1 ; n2 < (caselle.length - 1) ; n2++) caselle[n2].style.backgroundColor = colore_date_norm;
} // for n1
}
pren_drag = '';
pren_start = '';
pren_end = '';
}



function drp (id) {
var invia = 1;
var m_corr = document.getElementById('m_corr_su').innerHTML;
var m_ini = m_corr.substr(-4);
m_corr = (m_corr.replace('-'+m_ini,'') * 1);
if (pren_drag) {
var app = arr_app[id.substr(3)];
if (priv_mod_aa == 'n') invia = 0;
else if (priv_mod_aa != 's' && ApAs[pren_drag] != 'v' && ApAs[pren_drag].search(','+app+',') < 0) invia = 0;
if (!pren_drag || app_drag == id) invia = 0;
if (invia) {
if (ApAs[pren_drag] == 'v' || ApAs[pren_drag].search(','+app+',') >= 0) {
document.getElementById('s_appart').value = app;
document.getElementById('n_appart').value = '';
}
else {
document.getElementById('n_appart').value = app;
document.getElementById('s_appart').value = '';
}
document.getElementById('n_ini_per').value = '';
document.getElementById('n_fin_per').value = '';
document.getElementById('id_pren').value = pren_drag;
document.getElementById('d_data_ins').value = DaIn[pren_drag];
document.getElementById('orig').value = 'tab_mese_drop#'+ArLiDa[app_drag]+'#'+ArLiDa[id];
m_ini = (ArDaCo[pren_ini].substr(5,2) * 1);
var m_fine = (ArDaCo[pren_fine].substr(5,2) * 1);
if (m_ini == 1 && m_corr == 12) m_ini = 13;
if (m_fine == 1 && m_corr == 12) m_fine = 13;
if (m_ini == 12 && m_corr == 1) m_ini = 0;
if (m_fine == 12 && m_corr == 1) m_fine = 0;
if (m_ini > m_corr) {
m_ini = (document.getElementById('mese_orig').value * 1) + 1;
document.getElementById('mese_orig').value = m_ini;
}
if (m_fine < m_corr) {
m_fine = (document.getElementById('mese_orig').value * 1) - 1;
document.getElementById('mese_orig').value = m_fine;
}
document.getElementById('mod_pren').submit();
}
}
if (pren_start || pren_end) {
if (pren_start && id >= pren_fine) id = pren_fine - 1;
if (pren_end && id <= pren_ini) id = pren_ini + 1;
if (pren_start && id == pren_ini) invia = 0;
if (pren_end && id == pren_fine) invia = 0;
if (invia) {
var pren = '';
if (pren_start) {
pren = pren_start;
document.getElementById('n_ini_per').value = ArDaCo[id];
document.getElementById('n_fin_per').value = '';
}
if (pren_end) {
pren = pren_end;
if (allinea_tab_mesi == 'SI') id = id + 1;
document.getElementById('n_fin_per').value = ArDaCo[id];
document.getElementById('n_ini_per').value = '';
}
document.getElementById('n_appart').value = '';
document.getElementById('s_appart').value = '';
document.getElementById('id_pren').value = pren;
document.getElementById('d_data_ins').value = DaIn[pren];
document.getElementById('orig').value = 'tab_mese_drop#'+ArLiDa[app_drag]+'#'+ArLiDa[app_drag];
document.getElementById('mod_pren').submit();
}
}
if (!invia) {
drag_out_tr(id);
drp_out();
}
}



function prn_title_over (id_prn) {
var link_elem = document.getElementById(id_prn).getElementsByTagName('a')[0];
if (!ArTiOr[id_prn]) ArTiOr[id_prn] = link_elem.getAttribute('title');
if (!ArTiPr[id_prn]) {
var xmlhttp = new XMLHttpRequest();
xmlhttp.open('GET','tabella.php?id_sessione='+id_sessione+'&dati_prn='+id_prn,false);
xmlhttp.send();
var dati_prn = xmlhttp.responseXML.getElementsByTagName('txt');
if (dati_prn.length) {
ArTiPr[id_prn] = dati_prn[0].childNodes[0].nodeValue;
}
}
if (ArTiPr[id_prn]) link_elem.setAttribute('title',ArTiPr[id_prn]);
}



function prn_title_out (id_prn) {
var link_elem = document.getElementById(id_prn).getElementsByTagName('a')[0];
if (ArTiOr[id_prn] == 'null' || ArTiOr[id_prn] == '' || ArTiOr[id_prn] == null) link_elem.removeAttribute('title')
else link_elem.setAttribute('title',ArTiOr[id_prn]);
}



function res_color (n_col,on) {
var boxes = document.getElementById(curr_sel_row).childNodes;
for (var n1 = ((sel_start_date * 1) + 1) ; n1 <= n_col ; n1++) {
if (on) boxes[n1].style.backgroundColor = colore_drp2;
else boxes[n1].style.backgroundColor = '';
}
}



function reserve_sel_dates (row_id,n_col) {
var ins_res = 0;
if (sel_start_date) {
if (row_id == curr_sel_row) ins_res = 1;
row_id = curr_sel_row;
var curr_stop_date = n_col;
n_col = sel_start_date;
}
else curr_sel_row = row_id;
var boxes = document.getElementById(row_id).childNodes;
if (sel_start_date) boxes[n_col].style.backgroundColor = '';
else boxes[n_col].style.backgroundColor = colore_drp2;
var c_fine = 0;
for (n1 = 1 ; n1 < ((n_col * 1) + 1) ; n1++) {
var c_ini = c_fine;
c_fine = (boxes[n1].colSpan / 2);
c_fine = c_ini + c_fine;
} // for n2
var c_ini1 = c_ini;
var c_fine1 = c_fine;
for (var n1 = ((n_col * 1) + 1) ; n1 < (boxes.length - 1) ; n1++) {
var prenota = boxes[n1].getElementsByTagName('table');
if (prenota.length) break;
if (!sel_start_date && boxes[n1].style.backgroundColor != '' && boxes[n1].style.backgroundColor != 'null' && boxes[n1].style.backgroundColor != null) break;
if (sel_start_date && n1 > sel_stop_date) break;
c_ini = c_fine;
c_fine = c_ini + 1;
if (allinea_tab_mesi == 'SI') c_fine = c_fine - 1;
if (sel_start_date) {
res_color(n1,0);
colora_date(c_ini1,c_fine,colore_date_norm);
boxes[n1].onmouseover = new Function("colora_date("+c_ini+","+c_fine+",'"+colore_date_sel+"');");
boxes[n1].onmouseout = new Function("colora_date("+c_ini+","+c_fine+",'"+colore_date_norm+"');");
}
else {
boxes[n1].onmouseover = new Function("res_color("+n1+",1);colora_date("+c_ini1+","+c_fine+",'"+colore_date_sel+"');");
boxes[n1].onmouseout = new Function("res_color("+n1+",0);colora_date("+c_ini1+","+c_fine+",'"+colore_date_norm+"');");
}
if (allinea_tab_mesi == 'SI') c_fine = c_fine + 1;
if (curr_stop_date == n1) c_fine1 = c_fine;
} // for n1
if (sel_start_date) sel_start_date = 0;
else {
sel_start_date = n_col;
sel_stop_date = (n1 - 1);
}
if (ins_res) {
document.getElementById('ins_ini_per').value = ArDaCo[(c_ini1 + 1)];
document.getElementById('ins_fin_per').value = ArDaCo[(c_fine1 + 1)];
document.getElementById('ins_app').value = arr_app[row_id.substr(3)];
document.getElementById('ins_pren').submit();
}
}



function attiva_drag_drop () {
var n0 = 0;
var n1 = 0;
var n2 = 0;
var nlin_date = 0;
var prenota = '';
var caselle = '';
var caselle_prn = '';
var tabelle = document.getElementsByTagName('TABLE');
for (n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 'm1') {
var righe = tabelle[n0].childNodes[0].childNodes;
for (n1 = 0 ; n1 < righe.length ; n1++) {
  
if (righe[n1].className != 'rd_r') {
righe[n1].ondragover = new Function("drag_over_tr('"+righe[n1].id+"');");
righe[n1].ondragleave = new Function("drag_out_tr('"+righe[n1].id+"');");
righe[n1].ondrop = new Function("drp('"+righe[n1].id+"');");
ArLiDa[righe[n1].id] = nlin_date;
caselle = righe[n1].childNodes;
var c_fine = 0;
for (n2 = 1 ; n2 < (caselle.length - 1) ; n2++) {
var c_ini = c_fine;
c_fine = (caselle[n2].colSpan / 2);
c_fine = c_ini + c_fine;
if (allinea_tab_mesi == 'SI') c_fine = c_fine - 1;
prenota = caselle[n2].getElementsByTagName('table');
for (n3 = 0 ; n3 < prenota.length ; n3++) {
if (prenota[n3].id.substr(0,3) == 'prn') {
prenota[n3].draggable = true;
if (prenota[n3].draggable) prenota[n3].style.cursor = 'move';
var link_elem = prenota[n3].getElementsByTagName('a')[0];
link_elem.draggable = false;
link_elem.onmouseover = new Function("prn_title_over('"+prenota[n3].id+"');");
link_elem.onmouseout = new Function("prn_title_out('"+prenota[n3].id+"');");
if (priv_mod_da == 's') {
caselle_prn = prenota[n3].getElementsByTagName('td');
caselle_prn[0].draggable = true;
caselle_prn[0].style.cursor = 'e-resize';
var ult = (caselle_prn.length - 1);
caselle_prn[ult].draggable = true;
caselle_prn[ult].style.cursor = 'w-resize';
}
ArInPr[prenota[n3].id.substr(3)] = c_ini;
ArFiPr[prenota[n3].id.substr(3)] = c_fine;
}
} // for n3
if (prenota.length == 0 && (caselle[n2].style.backgroundColor == 'null' || caselle[n2].style.backgroundColor == '' || caselle[n2].style.backgroundColor == null)) caselle[n2].onclick = new Function("reserve_sel_dates('"+righe[n1].id+"','"+n2+"');");
if (allinea_tab_mesi == 'SI') c_fine = c_fine + 1;
}
} // if

else {
ArObDa[nlin_date] = righe[n1];
if (!nlin_date) {
caselle_col = righe[n1].childNodes;
for (n2 = 1 ; n2 < (caselle_col.length - 1) ; n2++) {
var div_el = caselle_col[n2].childNodes[0];
if (div_el.tagName == 'DIV') div_el.parentNode.removeChild(div_el);
caselle_col[n2].innerHTML = '<div id="'+caselle_col[n2].className+'" style="position: absolute; top: 0; height: '+tabelle[n0].offsetHeight+'px;"></div>'+caselle_col[n2].innerHTML;
var col_drop = document.getElementById(caselle_col[n2].className);
col_drop.ondragover = new Function("drag_over_td("+n2+")");
col_drop.ondragleave = new Function("drag_out_td("+n2+")");
col_drop.ondrop = new Function("drp("+n2+")");
}
}
nlin_date++;
} // else
} // for n1
} // if
} // for n0

pren_drag = '';
pren_start = '';
pren_end = '';

} // fine function attiva_drag_drop



function formatta_cognome (cognome,colonne,freccia) {
var cognome_f  = cognome;
var cognome_opt = '';
if (cognome === undefined) var lung_cognome = 0;
else var lung_cognome = cognome.length;
var lung_freccia = 0;
if (freccia) lung_freccia = 3;
var lung_non_ridotta = (7+agg_tronca)*colonne - lung_freccia;
if (tipo_periodi == 'g') lung_non_ridotta = (3+agg_tronca)*colonne - lung_freccia;
riduci_font = 0;
if (lung_cognome > lung_non_ridotta) riduci_font = 1;
lung_non_tronca = (9+agg_tronca)*colonne;
if (tipo_periodi == 'g') lung_non_tronca = (5+agg_tronca)*colonne;
if (lung_freccia == 3) lung_non_tronca = lung_non_tronca - 1;
if (lung_cognome > (lung_non_tronca+1) && cognome != '&nbsp;') cognome_f = cognome.substr(0,lung_non_tronca)+'.';
return cognome_f;
}



function scorri_date (direz) {

var ncol = 0;
var n0 = 0;
var n1 = 0;
var n2 = 0;
var caselle = '';
var cas_canc = 0;
var cas_agg = 0;
var tab_pren = '';
var cognome = '';
var idcognome = '';
var cognome_int = '';
var idcognome_int = '';
var idapp = '';
var freccia = 0;
var stile_checkin = '';
var titolo = '';
tab_spostata = 1;

var loop = 1;
if (direz.substr(0,1) == '7') {
loop = 7;
direz = direz.substr(1);
}

if (direz == 'sx') {
var idg_agg = loop+'s'+(id_ini_tab - 1);
var ddata = 'dini';
}
else {
var idg_agg = loop+'d'+(id_fine_tab + 1);
if (allinea_tab_mesi != 'SI') var ddata = 'dfine';
else ddata = 'dini';
}

var xmlhttp = new XMLHttpRequest();
xmlhttp.open('GET','tabella.php?id_sessione='+id_sessione+'&idg_agg='+idg_agg,false);
xmlhttp.send();
var g_agg_xml = xmlhttp.responseXML.getElementsByTagName('col');
if (g_agg_xml.length) {
for (ncol = 0 ; ncol < g_agg_xml.length ; ncol++) {

if (direz == 'sx') {
id_ini_tab = id_ini_tab - 1;
id_fine_tab = id_fine_tab - 1;
}
else {
id_ini_tab = id_ini_tab + 1;
id_fine_tab = id_fine_tab + 1;
}

var Ap_IF = g_agg_xml[ncol].getElementsByTagName('app');
var ApIF = new Array();
var PrIF = new Array();
var CoIF = new Array();
var ClIF = new Array();
var LnIF = new Array();
var LmIF = new Array();
var CkIF = new Array();
for (n0 = 0 ; n0 < Ap_IF.length ; n0++) {
idapp = Ap_IF[n0].getAttribute('id');
ApIF[idapp] = 1;
if (Ap_IF[n0].childNodes.length) {
PrIF[idapp] = Ap_IF[n0].getElementsByTagName('idpr')[0].childNodes[0].nodeValue;
CoIF[idapp] = Ap_IF[n0].getElementsByTagName('cogn')[0].childNodes[0].nodeValue;
ClIF[idapp] = Ap_IF[n0].getElementsByTagName('clr')[0].childNodes[0].nodeValue;
ApAs[PrIF[idapp]] = Ap_IF[n0].getElementsByTagName('apas')[0].childNodes[0].nodeValue;
DaIn[PrIF[idapp]] = Ap_IF[n0].getElementsByTagName('dain')[0].childNodes[0].nodeValue;
if (Ap_IF[n0].getElementsByTagName('ln').length) LnIF[idapp] = 1;
if (Ap_IF[n0].getElementsByTagName('lm').length) LmIF[idapp] = 1;
if (Ap_IF[n0].getElementsByTagName('cki').length) CkIF[idapp] = 1;
}
}

var tabelle = document.getElementsByTagName('table');
for (n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 'm1') {

var djs = g_agg_xml[ncol].getElementsByTagName('djs')[0].childNodes[0].nodeValue;
var gjs = djs.substr(8,2);
n1 = djs.substr(0,4);
n1 = (n1 - anno) * 12;
var mese = djs.substr(5,2);
mese = (mese * 1) + n1;

var righe = tabelle[n0].childNodes[0].childNodes;
for (n1 = 0 ; n1 < righe.length ; n1++) {

caselle = righe[n1].childNodes;
if (caselle.length > 2) {

if (direz == 'sx') cas_canc = caselle.length - 2;
else cas_canc = 1;
if (caselle[cas_canc].colSpan <= 2) {
righe[n1].removeChild(caselle[cas_canc]);
if (direz == 'sx') cas_canc = cas_canc - 1;
if (righe[n1].className == 'rd_r' && allinea_tab_mesi != 'SI') caselle[cas_canc].colSpan = 1;
}
else {
caselle[cas_canc].colSpan = caselle[cas_canc].colSpan - 2;
cognome = caselle[cas_canc].getElementsByTagName('a');
if (cognome.length) {
idcognome_int = cognome[0];
cognome_int = idcognome_int.innerHTML;
if (idcognome_int.getAttribute('title') != null) cognome_int = idcognome_int.getAttribute('title');
idcognome = idcognome_int.parentNode;
cognome = idcognome.innerHTML;
freccia = 1;
idcognome_int.innerHTML = formatta_cognome(cognome_int,(caselle[cas_canc].colSpan / 2),freccia);
if (idcognome_int.innerHTML != cognome_int) idcognome_int.setAttribute('title',cognome_int);
else idcognome_int.removeAttribute('title');
tab_pren = caselle[cas_canc].getElementsByTagName('table');
if (direz == 'sx' && cognome.substr(cognome.length - 6) != ' -&gt;') {
idcognome.innerHTML = idcognome.innerHTML+' -&gt;';
if (tab_pren.length) {
tab_pren[0].style.borderTopRightRadius = 0;
tab_pren[0].style.borderBottomRightRadius = 0;
}
}
if (direz == 'dx' && cognome.substr(0,6) != '&lt;- ') {
idcognome.innerHTML = '&lt;- '+idcognome.innerHTML;
if (tab_pren.length) {
tab_pren[0].style.borderTopLeftRadius = 0;
tab_pren[0].style.borderBottomLeftRadius = 0;
}
}
if (riduci_font && idcognome.tagName != 'SMALL') idcognome.innerHTML = '<small><small>'+idcognome.innerHTML+'</small></small>';
}
}

if (direz == 'sx') cas_agg = 1;
else cas_agg = caselle.length - 2;
if (righe[n1].className != 'rd_r') {
cognome = caselle[cas_agg].getElementsByTagName('a');
if (cognome.length) {
idcognome_int = cognome[0];
cognome_int = idcognome_int.innerHTML;
if (idcognome_int.getAttribute('title') != null) cognome_int = idcognome_int.getAttribute('title');
idcognome = idcognome_int.parentNode;
cognome = idcognome.innerHTML;
if ((cognome.substr(cognome.length - 6) == ' -&gt;' && direz != 'sx') || (cognome.substr(0,6) == '&lt;- ' && direz == 'sx')) {
caselle[cas_agg].colSpan = caselle[cas_agg].colSpan + 2;
idapp = arr_app[righe[n1].id.substr(3)];
if (ApIF[idapp]) freccia = 0;
else freccia = 1;
idcognome_int.innerHTML = formatta_cognome(cognome_int,(caselle[cas_agg].colSpan / 2),freccia);
if (idcognome_int.innerHTML != cognome_int) idcognome_int.setAttribute('title',cognome_int);
else idcognome_int.removeAttribute('title');
if (ApIF[idapp]) {
tab_pren = caselle[cas_agg].getElementsByTagName('table');
if (cognome.substr(0,6) == '&lt;- ' && direz == 'sx') {
idcognome.innerHTML = idcognome.innerHTML.substr(6);
if (tab_pren.length) {
tab_pren[0].style.borderTopLeftRadius = null;
tab_pren[0].style.borderBottomLeftRadius = null;
}
}
else {
idcognome.innerHTML = idcognome.innerHTML.substr(0,idcognome.innerHTML.length - 6);
if (tab_pren.length) {
tab_pren[0].style.borderTopRightRadius = null;
tab_pren[0].style.borderBottomRightRadius = null;
}
}
}
if (!riduci_font && idcognome.tagName == 'SMALL') idcognome.parentNode.parentNode.innerHTML = idcognome.innerHTML;
// If reservation doesn't have an arrow in current direction don't add a new cell (continue to next in loop)
continue;
}
}
}

if (direz == 'dx') cas_agg = cas_agg + 1;
var ncas = document.createElement('TD');
ncas.innerHTML = '&nbsp;';
ncas.colSpan = 2;
if (righe[n1].className == 'rd_r') {
if (allinea_tab_mesi != 'SI') ncas.colSpan = 1;
if (direz == 'sx') {
caselle[cas_agg].colSpan = 2;
if (gjs == '31' || gjs == '30' || gjs == '29' || gjs == '28') ncas.style.backgroundColor = colore_date_altre;
else ncas.style.backgroundColor = caselle[cas_agg].style.backgroundColor;
}
else {
caselle[(cas_agg - 1)].colSpan = 2;
if ((allinea_tab_mesi != 'SI' && gjs == '01') || (allinea_tab_mesi == 'SI' && gjs == '02')) ncas.style.backgroundColor = colore_date_altre;
else ncas.style.backgroundColor = caselle[(cas_agg - 1)].style.backgroundColor;
}
var dagg = g_agg_xml[ncol].getElementsByTagName(ddata);
ncas.innerHTML = dagg[0].childNodes[0].nodeValue;
}
else {
idapp = arr_app[righe[n1].id.substr(3)];
if (ApIF[idapp]) {
ncas.className = 'pren';
if (LnIF[idapp]) freccia = 0;
else freccia = 1;
if (CoIF[idapp] != '&nbsp;') {
cognome = formatta_cognome(CoIF[idapp],1,freccia);
if (cognome == CoIF[idapp]) titolo = '';
else titolo = ' title=\"'+CoIF[idapp]+'\"';
if (LmIF[idapp]) cognome = '<a class=\"noho\"'+titolo+'>'+cognome+'</a>';
else cognome = '<a'+titolo+' href=\"modifica_prenota.php?id_prenota='+PrIF[idapp]+'&amp;anno='+anno+'&amp;id_sessione='+id_sessione+'&amp;mese='+mese+'\">'+cognome+'</a>';
}
else cognome = '<a>&nbsp;</a>';
var bordi = '';
if (!LnIF[idapp]) {
if (direz == 'dx') {
cognome += ' -&gt;';
bordi = ' border-top-right-radius: 0; border-bottom-right-radius: 0;';
}
else {
cognome = '&lt;- '+cognome;
bordi = ' border-top-left-radius: 0; border-bottom-left-radius: 0;';
}
}
if (!CkIF[idapp]) stile_checkin = '';
else stile_checkin = ' style=\"background-image:url(img/fr_sx_checkin.gif); background-repeat:no-repeat; background-position: right center;\"';
if (CoIF[idapp] == '&nbsp;') {
ncas.innerHTML = '<div style=\"visibility: hidden;\">'+cognome+'</div>';
ncas.style.backgroundColor = ClIF[idapp];
}
else {
var idprn = ' id=\"prn'+PrIF[idapp]+'\"';
if (LmIF[idapp]) idprn = ' id=\"prx'+PrIF[idapp]+'\"';
ncas.innerHTML = '<table style=\"background-color: '+ClIF[idapp]+';'+bordi+'\"'+idprn+'><tr><td'+stile_checkin+'/><td><small><small>'+cognome+'</small></small></td><td/></tr></table>';
}
}
}

righe[n1].insertBefore(ncas,caselle[cas_agg]);
if (righe[n1].className == 'rd_r') {
caselle = righe[n1].getElementsByTagName('td');
var colore1 = caselle[1].style.backgroundColor;
for (n2 = 1 ; n2 < (caselle.length - 1) ; n2++) {
caselle[n2].className = 'rd_'+(n2 - 1);
if (gjs == '16' && direz == 'dx') {
if (caselle[n2].style.backgroundColor == colore1) caselle[n2].style.backgroundColor = colore_date_altre;
else caselle[n2].style.backgroundColor = colore_date_norm;
}
if (gjs == '14' && direz == 'sx') {
if (caselle[n2].style.backgroundColor == colore1) caselle[n2].style.backgroundColor = colore_date_norm;
else caselle[n2].style.backgroundColor = colore_date_altre;
}
}
}

}
} // for n1

if (direz == 'dx') {
for (n1 = 1 ; n1 < (ArDaCo.length - 1) ; n1++) ArDaCo[n1] = ArDaCo[(n1 + 1)];
ArDaCo[(ArDaCo.length - 1)] = djs;
if (gjs == '16') {
var d_corr = document.getElementById('m_corr_su').innerHTML;
var a_corr = d_corr.substr(-4);
var m_corr = d_corr.replace('-'+a_corr,'');
m_corr++;
if (m_corr > 12)  {
m_corr = 1;
a_corr++;
}
document.getElementById('m_corr_su').innerHTML = m_corr+'-'+a_corr;
var m_prec = document.getElementById('m_prec_su').value;
m_prec++;
document.getElementById('m_prec_su').value = m_prec;
document.getElementById('m_prec_giu').value = m_prec;
var m_succ = document.getElementById('m_succ_su').value;
m_succ++;
document.getElementById('m_succ_su').value = m_succ;
document.getElementById('m_succ_giu').value = m_succ;
var m_orig = document.getElementById('mese_orig').value;
m_orig++;
document.getElementById('mese_orig').value = m_orig;
}
}
else {
for (n1 = (ArDaCo.length - 1) ; n1 > 1 ; n1--) ArDaCo[n1] = ArDaCo[(n1 - 1)];
ArDaCo[1] = djs;
if (gjs == '14') {
var d_corr = document.getElementById('m_corr_su').innerHTML;
var a_corr = d_corr.substr(-4);
var m_corr = d_corr.replace('-'+a_corr,'');
m_corr--;
if (m_corr < 1)  {
m_corr = 12;
a_corr--;
}
document.getElementById('m_corr_su').innerHTML = m_corr+'-'+a_corr;
var m_prec = document.getElementById('m_prec_su').value;
m_prec--;
document.getElementById('m_prec_su').value = m_prec;
document.getElementById('m_prec_giu').value = m_prec;
var m_succ = document.getElementById('m_succ_su').value;
m_succ--;
document.getElementById('m_succ_su').value = m_succ;
document.getElementById('m_succ_giu').value = m_succ;
var m_orig = document.getElementById('mese_orig').value;
m_orig--;
document.getElementById('mese_orig').value = m_orig;
}
}

} // if
} // for n0
} // for ncol
} // if

attiva_drag_drop();
attiva_colora_date(allinea_tab_mesi);

} // function scorri_date



function contr_da_tab_mese () {
if (tab_spostata) {
document.getElementById('dini_contr').value = ArDaCo[1];
document.getElementById('dfine_contr').value = ArDaCo[(ArDaCo.length - 1)];
var lpren_contr = '';
var tabelle = document.getElementsByTagName('table');
for (var n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 'm1') {
var righe = tabelle[n0].childNodes[0].childNodes;
for (var n1 = 0 ; n1 < righe.length ; n1++) {
if (righe[n1].className != 'rd_r') {
var caselle = righe[n1].childNodes;
if (caselle.length > 2) {
var c_fine = 0;
for (var n2 = 1 ; n2 < (caselle.length - 1) ; n2++) {
var prenota = caselle[n2].getElementsByTagName('table');
for (n3 = 0 ; n3 < prenota.length ; n3++) {
if (prenota[n3].id.substr(0,3) == 'prn' || prenota[n3].id.substr(0,3) == 'prx') {
lpren_contr += ','+prenota[n3].id.substr(3);
}
} // for n3
} // for n2
}
}
} // for n1
}
} // for n0
if (lpren_contr) lpren_contr += ',';
document.getElementById('lpren_contr').value = lpren_contr;
}
} // function contr_da_tab_mese



// modifica_prenota.php functions

function apri_vis (bott_vis,div_vis,testo_vis) {
var bott = document.getElementById(bott_vis);
var cont_vis = document.getElementById(div_vis);
var elem_vis = cont_vis.style.visibility;
if (elem_vis != 'visible') {
var testo = testo_vis;
cont_vis.style.visibility = 'visible';
bott.innerHTML = '<img style=\"display: block;\" src=\"./img/freccia_giu_marg.png\" alt=\"".mex("nascondi",$pag)." &gt;\">';
}
if (elem_vis == 'visible') {
var testo = '';
cont_vis.style.visibility = 'hidden';
bott.innerHTML = '<img style=\"display: block;\" src=\"./img/freccia_destra_marg.png\" alt=\"".mex("mostra",$pag)." &gt;\">';
}
cont_vis.innerHTML = testo;
return false;
} // fine function apri_vis



// funzioni tabella periodi e tariffe

function append_inputs_from_form (idform_from,idform_to,app_hidden) {
var form1 = document.getElementById(idform_from);
var form2 = document.getElementById(idform_to);
if (form1 && form2) {
var el = new Array();
for (var i=0 ; i < form1.length ; i++) {
if (form1.elements[i].type != 'hidden' || app_hidden) {
el[i] = document.createElement('input');
el[i].type = 'hidden';
el[i].name = form1.elements[i].name;
el[i].value = form1.elements[i].value;
form2.appendChild(el[i]);
}
} // for i
}
} // fine function append_inputs_from_form



function mod_prezzo_cella (idc) {
var cella = document.getElementById(idc);
var prez1 = '';
var prez2 = '';
var mos_p = 0;
var molt_p = 0;
var val_pr = cella.innerHTML;
cella.onclick = '';
cella.title = '';
var size_p1 = 8;
if (idc.substr(-4) == 'pmin') size_p1 = 3;
if (val_pr != '&nbsp;') {
if (size_p1 == 3) prez1 = val_pr.split('&')[0];
else {
var val_pr_v = val_pr.split(' + ');
if (val_pr_v.length == 1 && val_pr_v[0].indexOf('*p') >= 0) {
val_pr_v[1] = val_pr_v[0];
val_pr_v[0] = '';
}
var prez1_v = val_pr_v[0].split('*');
prez1 = prez1_v[0];
if (val_pr_v[1]) {
var prez2_v = val_pr_v[1].split('*');
prez2 = prez2_v[0];
}
}
}
if (prez1 == '&nbsp;') prez1 = '';
if (prez1.match(/\.[0-9]{3,3}/)) prez1 = prez1.replace('.','');
if (prez1.match(/\,[0-9]{3,3}/)) prez1 = prez1.replace(',','');
var tar = idc.split('tar');
if (prez1 && tab_tariffe && tar_per_app[tar[1]] && size_p1 == 8) prez1 = (prez1 / tar_per_app[tar[1]].substr(1));
var nval = '<input id="p1'+idc+'" type="text" name="'+idc+'" value="'+prez1+'" onfocus="this.value = this.value;" size="'+size_p1+'">';
if (tar_per_app[tar[1]]) nval += tar_per_app[tar[1]];
if (prez2 == '') if (tar_per_pers[tar[1]]) prez2 = '&nbsp;';
if (prez2 != '') {
if (prez2 == '&nbsp;') prez2 = '';
if (prez2.match(/\.[0-9]{3,3}/)) prez2 = prez2.replace('.','');
if (prez2.match(/\,[0-9]{3,3}/)) prez2 = prez2.replace(',','');
nval += ' + <input type="text" name="'+idc+'p" value="'+prez2+'" size="6">*p';
}
cella.innerHTML = nval;
cella = document.getElementById('p1'+idc)
cella.selectionStart = cella.selectionEnd = cella.value.length;
cella.focus();

var form_tpt = document.getElementById('f_tpt');
var mel = document.createElement('input');
mel.type = 'hidden';
mel.name = "mod_"+idc;
mel.value = '1';
form_tpt.appendChild(mel);

if (!subm_tpt) {
subm_tpt = 1;
mel = document.createElement('input');
mel.type = 'hidden';
mel.name = 'ins_form_tabella';
mel.value = 'SI';
form_tpt.appendChild(mel);
form_tpt.onsubmit = new Function("append_inputs_from_form('f_tpt0','f_tpt',0);");
var form_tpt0 = document.getElementById('f_tpt0');
if (form_tpt0) form_tpt0.onsubmit = new Function("append_inputs_from_form('f_tpt','f_tpt0',1);");
var subm_b_tpt = document.getElementById('but_tpt');
if (document.getElementById('modi')) subm_b_tpt.innerHTML = frase_mod_prezzi_tpt;
else {
subm_b_tpt.innerHTML = '<div>'+frase_mod_prezzi_tpt+'</div>';
subm_b_tpt.id = 'modi';
}
if (tab_tariffe) form_tpt.action = 'visualizza_tabelle.php';
if (typeof replica_tasti === "function") replica_tasti();
} // if
} // fine function mod_prezzo_cella



function attiva_mod_prezzi_cella () {
var n0 = 0;
var n1 = 0;
var n2 = 0;
var caselle = '';
var tabella = '';
var righe = '';
for (n0 = 1 ; n0 <= num_tab_per_tar ; n0++) {
tabella = document.getElementById('t_pertar'+n0);
righe = tabella.childNodes[0].childNodes;
for (n1 = 0 ; n1 < righe.length ; n1++) {
caselle = righe[n1].childNodes;
for (n2 = 1 ; n2 < caselle.length ; n2++) {
if (caselle[n2].id && caselle[n2].id.substr(0,3) == 'per') {
caselle[n2].onclick = new Function("mod_prezzo_cella('"+caselle[n2].id+"');");
caselle[n2].title = fr_premere_per_modificare;
} // if
} // for n2
} // for n1
} // for n0
} // fine function attiva_mod_prezzi_cella


