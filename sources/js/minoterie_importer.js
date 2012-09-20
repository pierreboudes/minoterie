/* -*- coding: utf-8 -*-*/
/* Minoterie - outil de gestion des services d'enseignement        
 *
 * Copyright 2009 Pierre Boudes, département d'informatique de l'institut Galilée.
 *
 * This file is part of Minoterie.
 *
 * Minoterie is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Minoterie is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Minoterie.  If not, see <http://www.gnu.org/licenses/>.
 */

"use strict"; /* From now on, lets pretend we are strict */


$(document).ready(function(){
    $('#skel').fadeOut(0);
    var listdept = $('#departements').find('li.departement');
    /* TODO faire plutôt un tabs ou un truc du genre */
    if (listdept.length > 1) {
	listdept.each(function (i,e) {	    
	    $(this).click(function(e) {
		$(this).siblings('li.selected').removeClass('selected');
		$('#vuecourante').empty();
		$('#dialog_importer').remove();
		$('#userdept').empty();
		$(this).clone(false, false).appendTo('#userdept');
		$(this).addClass('selected');
		ajouterTableCategories($("#vuecourante"));
		ajouterBoutonsImportation($("#vuecourante"));
		return false;
	    });
	});
    } 

    ajouterTableCategories($("#vuecourante"));
    ajouterBoutonsImportation($("#vuecourante"));
});
