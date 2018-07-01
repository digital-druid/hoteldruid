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


function agg_zero (c) {
r = "";
if (c < 10) {
r = "0";
} // fine if (c < 10)
return r;
} // fine function agg_zero


function update_selected_dates (id) {

var sel_opt=document.getElementById("id_sdm"+id);
var other_id = id;
if (Math.ceil(id/2) != Math.floor(id/2)) other_id++;
else other_id--;
var other_sel_opt = document.getElementById("id_sdm"+other_id);
var num_sel = sel_opt.selectedIndex;
var other_num_sel = other_sel_opt.selectedIndex;

if (other_sel_opt.options[other_num_sel].text == "----") {
var second_date_selected = window['second_date_selected'+other_id];
if (second_date_selected) {
var num_opz = other_sel_opt.length;
for (n1 = 0 ; n1 < num_opz ; n1++) {
val_cal = other_sel_opt.options[n1].value;
if (other_id < id) {
if (second_date_selected >= other_sel_opt.options[n1].value) other_num_sel = n1;
else break;
}
if (other_id > id && second_date_selected <= other_sel_opt.options[n1].value) {
other_num_sel = n1;
break;
}
}
}
}

if (other_sel_opt.options[other_num_sel].text != "----") {
var add_ns = 0;
var o_add_ns = 0;
var new_sel_opt = -1;
if (sel_opt.options[0].text == "----") add_ns = 1;
if (other_sel_opt.options[0].text == "----") o_add_ns = 1;
if ((other_id > id) && ((num_sel - add_ns) >= (other_num_sel - o_add_ns))) new_sel_opt = num_sel - add_ns + o_add_ns + 1;
if ((other_id < id) && ((num_sel - add_ns) <= (other_num_sel - o_add_ns))) new_sel_opt = num_sel - add_ns + o_add_ns - 1;
if (new_sel_opt >= 0) other_sel_opt.selectedIndex = new_sel_opt;
} // fine if (other_sel_opt.options[other_num_sel].text != "----")

} // fine function update_selected_dates


function nasc_cal (ncal) {
var lcal=document.getElementById('cal'+ncal);
lcal.style.visibility='hidden';
} // fine function nasc_cal


function mos_cal (ncal) {
var lcal = document.getElementById('cal'+ncal);
var elementoid=document.getElementById('bcal'+ncal);
var elementi = elementoid;
var contentbox = document.getElementById('contentbox');
var iTop = (contentbox.scrollTop * -1);
var prova = lcal.style.visibility;

if (prova != 'visible') {
var iLeft = (contentbox.scrollLeft * -1);
while (elementi.tagName != 'BODY') {
iTop += elementi.offsetTop;
iLeft += elementi.offsetLeft;
elementi = elementi.offsetParent;
}

lcal.style.left = (iLeft + 2) + 'px';
lcal.style.top = (iTop + elementoid.offsetHeight + 2) + 'px';
var data_sel = document.getElementById('id_sdm'+ncal);
if (!data_sel.selectedIndex) {
var second_date_selected = window['second_date_selected'+ncal];
if (second_date_selected) data_sel = second_date_selected;
else data_sel = data_sel.options[2].value;
}
else data_sel = data_sel.options[data_sel.selectedIndex].value;
mese = (data_sel.substring(5,7) - 1);
anno = data_sel.substring(0,4);
crea_cal_mese(ncal,mese,anno);
lcal.style.visibility='visible';
}

if (prova == 'visible') {
nasc_cal(ncal);
}
} // fine function mos_cal


function rendi_link (val_cal,n_lista_d,lista_d,ncal) {
var elem = document.getElementById('d'+val_cal+ncal);
if (!elem) return;
elem.bgColor = '#d8e1e6';
elem.onmouseover = function() {
this.bgColor = '#eeeeee';
}
elem.onmouseout = function() {
this.bgColor = '#d8e1e6';
}
elem.onmousedown = function() {
lista_d.selectedIndex = n_lista_d;
nasc_cal(ncal);
update_selected_dates(ncal);
}
} // fine function rendi_link


function crea_cal_mese (ncal,mese,anno) {
var n_giorni_l = 0;
var giorni_l = new Array();
var n1 = 0;
d = new Date(anno,mese,1,2);
anno = d.getFullYear();
mese = d.getMonth();
giorno = d.getDay() * -1;
giorno = giorno + 2;
if (giorno > 1) giorno = giorno - 7;
var mese_orig = mese;
var anno_orig = anno;
var testo_cal = ' '+mesi[mese]+'&nbsp;'+anno+'<br>\
<table cellspacing="0" cellpadding="0"><tr><td>\
<table cellspacing="0" cellpadding="0"><tr>\
<td><button type="button" class= "calbutton" onclick="crea_cal_mese(\''+ncal+'\',\''+mese+'\',\''+(anno - 1)+'\')">&lt;&lt;</button></td>\
<td><button type="button" class= "calbutton" onclick="crea_cal_mese(\''+ncal+'\',\''+(mese - 1)+'\',\''+anno+'\')">&lt;</button></td>\
<td><button type="button" class= "calbutton" onclick="crea_cal_mese(\''+ncal+'\',\''+(mese + 1)+'\',\''+anno+'\')">&gt;</button></td>\
<td><button type="button" class= "calbutton" onclick="crea_cal_mese(\''+ncal+'\',\''+mese+'\',\''+(anno + 1)+'\')">&gt;&gt;</button></td>\
</tr></table><table>\
<tr><td>'+giorni['1']+'</td><td>'+giorni['2']+'</td><td>'+giorni['3']+'</td><td>'+giorni['4']+'</td><td>'+giorni['5']+'</td><td>'+giorni['6']+'</td><td>'+giorni['0']+'</td></tr>';
d = new Date(anno,mese,giorno,2);
mese = d.getMonth();
anno = d.getFullYear();
giorno = d.getDate();
while (mese_orig == mese || n1 == 0) {
testo_cal += '<tr>';
for (n1 = 1 ; n1 <= 7 ; n1++) {
if (mese == mese_orig) {
testo_cal += '<td id="d'+anno+'-'+agg_zero((mese + 1))+(mese + 1)+'-'+agg_zero(giorno)+giorno+ncal+'">'+giorno+'</td>';
n_giorni_l = n_giorni_l + 1;
}
else testo_cal += '<td></td>';
giorno = giorno + 1;
d = new Date(anno,mese,giorno,2);
mese = d.getMonth();
anno = d.getFullYear();
giorno = d.getDate();
}
testo_cal += '</tr>';
}
testo_cal += '</table></td></tr></table>';
document.getElementById('cal'+ncal).innerHTML = testo_cal;
var lista_d = document.getElementById('id_sdm'+ncal);
var num_opz = lista_d.length;
var val_cal = 0;
var val_comp = anno_orig+'-'+agg_zero(mese_orig + 1)+(mese_orig + 1);
for (n1 = 0 ; n1 < num_opz ; n1++) {
val_cal = lista_d.options[n1].value;
if (val_cal.substring(0,7) == val_comp) {
rendi_link(val_cal,n1,lista_d,ncal);
}
}
} // fine function crea_cal_mese


function replica_tasti () {
var tasto_mod = document.getElementById('modi');
var tasto_ins = document.getElementById('inse');
var lista_con = document.getElementById('lcon');
if (tasto_mod || tasto_ins) {
var tasto_can = document.getElementById('canc');
var tasto_ind = document.getElementById('indi');
var topsp = document.getElementById('topsp');
topsp.style.height = '22px'

var elementi = topsp;
var iTop = 0;
var iLeft = 0;
while(elementi.tagName != 'BODY') {
iTop += elementi.offsetTop;
iLeft += elementi.offsetLeft;
elementi = elementi.offsetParent;
}

var nuovo_html = '<div id="aziobar" style="text-align: right; width: 96%; padding: 0; z-index: 2; position: absolute; top: '+(iTop + 1)+'px; left: '+iLeft+'px;"><div style="float: right; background-color: #ffffff; padding: 1px;">';
if (tasto_ins) {
if (tasto_ins.value) tasto_ins = tasto_ins.value;
else tasto_ins = tasto_ins.childNodes[0].innerHTML;
nuovo_html += '<input class="rbutton" type="submit" onclick="manda_form(\'inse\')" value="&nbsp;'+tasto_ins+'&nbsp;">';
}
if (tasto_mod) {
if (tasto_mod.value) tasto_mod = tasto_mod.value;
else tasto_mod = tasto_mod.childNodes[0].innerHTML;
nuovo_html += '<input class="rbutton" type="submit" onclick="manda_form(\'modi\')" value="&nbsp;'+tasto_mod+'&nbsp;">';
}
if (tasto_can) {
if ((tasto_ins || tasto_mod) && lista_con) nuovo_html += '<span class="canc_vsmlscr">';
if (tasto_can.value) tasto_can = tasto_can.value;
else tasto_can = tasto_can.childNodes[0].innerHTML;
nuovo_html += '&nbsp;&nbsp;<input class="rbutton" type="submit" onclick="manda_form(\'canc\')" value="&nbsp;'+tasto_can+'&nbsp;">';
if ((tasto_ins || tasto_mod) && lista_con) nuovo_html += '</span>';
}
if (lista_con) {
var tasto_con = document.getElementById('hcon');
nuovo_html += '&nbsp;&nbsp;<select id="lcon2" class="rselect" onchange="manda_select()">';
nuovo_html += '<option value="">'+tasto_con.value+'</option>';
for (n1 = 0 ; n1 < lista_con.length ; n1++) {
nuovo_html += '<option value="'+lista_con.options[n1].value+'">'+lista_con.options[n1].innerHTML+'</option>';
} // fine for n1
nuovo_html += '</select>';
} // fine if (lista_con)
if (tasto_ind) {
if ((tasto_ins || tasto_mod) && lista_con) nuovo_html += '<span class="canc_vsmlscr">';
if (tasto_ind.value) tasto_ind = tasto_ind.value;
else tasto_ind = tasto_ind.childNodes[0].innerHTML;
nuovo_html += '&nbsp;&nbsp;<input class="rbutton" type="submit" onclick="manda_form(\'indi\')" value="&nbsp;'+tasto_ind+'&nbsp;">';
if ((tasto_ins || tasto_mod) && lista_con) nuovo_html += '</span>';
}
nuovo_html += '</div></div>';
topsp.innerHTML = nuovo_html;
} // fine if (tasto_mod || tasto_ins)
} // fine function replica_tasti


function manda_form (tasto) {
var tasto_id = document.getElementById(tasto);
tasto_id.click();
} // fine function manda_form


function manda_select () {
var lista_con = document.getElementById('lcon');
var lista_con2 = document.getElementById('lcon2');
if (lista_con2.selectedIndex != 0) {
lista_con.selectedIndex = (lista_con2.selectedIndex - 1);
var tasto_id = document.getElementById('tcon');
tasto_id.click();
}
} // fine function manda_select


function aggiorna_prenota_sel () {
var n0 = 0;
var n1 = 0;
var cbox = '';
var numpren = '';
var nuova_lista_mod = '';
var nuova_lista_contr = '';
var tabelle = document.getElementsByTagName('table');
for (n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 't1') {
var righe = tabelle[n0].getElementsByTagName('tr');
for (n1 = 1 ; n1 < (righe.length - 1) ; n1++) {
cbox = righe[n1].getElementsByTagName('input');
if (cbox.length != 0) {
if (cbox[0].checked == true) {
numpren = cbox[0].name;
if (numpren.substr(0,6) == 'cambia') {
numpren = cbox[0].value;
if (lista_prenota_mod_orig.search(','+numpren+',') != -1) {
nuova_lista_mod += ','+numpren;
} // if
if (lista_prenota_contr_orig.search(','+numpren+',') != -1) {
nuova_lista_contr += ','+numpren;
} // if
} // if
} // if
} // if
} // for n1
} // if
} // for n0
if (nuova_lista_mod) {
nuova_lista_mod = nuova_lista_mod.substr(1)
document.getElementById('smt_prenota_mod').childNodes[0].innerHTML = document.getElementById('fsl_prenota_mod').value;
document.getElementById('lst_prenota_mod').value = nuova_lista_mod;
} // if
else {
document.getElementById('smt_prenota_mod').childNodes[0].innerHTML = document.getElementById('fms_prenota_mod').value;
document.getElementById('lst_prenota_mod').value = lista_prenota_mod_orig.substring(1,(lista_prenota_mod_orig.length -1));
} // else
if (nuova_lista_contr) document.getElementById('lst_prenota_contr').value = nuova_lista_contr+',';
else document.getElementById('lst_prenota_contr').value = lista_prenota_contr_orig;
} // fine function aggiorna_prenota_sel


function seleziona_uguali (cas,val) {
var n0 = 0;
var n1 = 0;
var caselle = '';
var cbox = '';
var tabelle = document.getElementsByTagName('table');
for (n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 't1') {
var righe = tabelle[n0].getElementsByTagName('tr');
for (n1 = 1 ; n1 < (righe.length - 1) ; n1++) {
cbox = righe[n1].getElementsByTagName('input');
if (cbox.length != 0) {
caselle = righe[n1].getElementsByTagName('td');
if (caselle[cas].innerHTML == val) {
cbox[0].checked = true;
} // if
else {
cbox[0].checked = false;
} // else
} // if
} // for n1
} // if
} // for n0
aggiorna_prenota_sel();
} // fine function seleziona_uguali


function attiva_seleziona_uguali () {
var n0 = 0;
var n1 = 0;
var n2 = 0;
var caselle = '';
var tabelle = document.getElementsByTagName('table');
for (n0 = 0 ; n0 < tabelle.length ; n0++) {
if (tabelle[n0].className == 't1') {
var righe = tabelle[n0].getElementsByTagName('tr');
for (n1 = 1 ; n1 < (righe.length - 1) ; n1++) {
caselle = righe[n1].getElementsByTagName('td');
for (n2 = 1 ; n2 < caselle.length ; n2++) {
cbox = caselle[n2].getElementsByTagName('input');
if (cbox.length == 0) {
caselle[n2].onclick = new Function("seleziona_uguali("+n2+",'"+caselle[n2].innerHTML.replace(/\'/g,'\\\'').replace(/\n/g,'\\\n')+"');");
} // if
else {
cbox[0].onchange = new Function("aggiorna_prenota_sel()");
} // else
} // for n2
} // for n1
} // if
} // for n0
} // fine function attiva_seleziona_uguali


function ridim_col_modres () {
var nWidth = document.documentElement.clientWidth;
nWidth = (nWidth - 40) / 2;
if (nWidth < 770) {
var tabs = document.getElementsByTagName('table');
var divs = document.getElementsByTagName('div');
for (n1 = 0 ; n1 < tabs.length ; n1++) {
if (tabs[n1].className.substring(0,6) == 'modres') {
if (nWidth >= 612) tabs[n1].style.width = nWidth+'px';
else tabs[n1].style.maxWidth = '840px';
}
} // for n1
for (n1 = 0 ; n1 < divs.length ; n1++) {
if (divs[n1].className.substring(0,6) == 'modres') {
if (nWidth >= 612) divs[n1].style.width = nWidth+'px';
else tabs[n1].style.maxWidth = '840px';
}
} // for n1
} // if (nWidth >= 612 && nWidth < 770)
} // fine function ridim_col_modres


function focus_elem (elem) {
var elem_id = document.getElementById(elem);
elem_id.focus();
} // fine function focus_elem


function blur_elem (elem) {
var elem_id = document.getElementById(elem);
elem_id.blur();
} // fine function blur_elem


function tab_in_container () {
var tabs = document.getElementsByTagName('table');
var maxtabwidth = tabs[0].offsetWidth;
var navbarwidth = 0;
if (tabs[0].className == 'nav_bar') navbarwidth = maxtabwidth;
for (n1 = 1 ; n1 < tabs.length ; n1++) {
if (tabs[n1].offsetWidth > maxtabwidth && tabs[n1].parentNode.className != 'tab_cont' && tabs[n1].parentNode.parentNode.className != 'tab_cont' && tabs[n1].className != 'm1') maxtabwidth = tabs[n1].offsetWidth;
} // for n1
if (maxtabwidth > 300) {
if (maxtabwidth != navbarwidth) maxtabwidth = maxtabwidth + 6;
document.getElementById('menubox').style.minWidth = maxtabwidth+'px';
maxtabwidth = maxtabwidth - 6;
document.getElementById('contentbox').style.minWidth = maxtabwidth+'px';
}
} // fine function tab_in_container

