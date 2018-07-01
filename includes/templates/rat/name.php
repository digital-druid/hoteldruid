<?php

##################################################################################
#    HOTELDRUID
#    Copyright (C) 2001-2016 by Marco Maria Francesco De Santis (marco@digitaldruid.net)
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


global $template_name,$template_name_show,$template_file_name,$template_data_dir;
$template_name = "rates_table_template";
$template_name_show = array();
$template_name_show['en'] = "Rates table page";
$template_name_show['ita'] = "Pagina tabella tariffe";
$template_name_show['es'] = "Página tabla tarifas";
$template_class = "rate";
$template_file_name = array();
$template_file_name['en'] = "rates_table_tpl.php";
$template_file_name['ita'] = "mdl_tabella_tariffe.php";
$template_file_name['es'] = "mdl_tabla_tarifas.php";
$template_data_dir = "rat";



?>