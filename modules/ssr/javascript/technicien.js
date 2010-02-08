/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Technicien = {
  edit: function(plateau_id, technicien_id) {
		new Url("ssr", "ajax_edit_technicien") .
		  addParam("technicien_id", technicien_id) .
			addParam("plateau_id", plateau_id) .
			requestUpdate("techniciens");
		
  },
  	
  onSubmit: function(form) {
    return onSubmitFormAjax(form, { 
		  onComplete: Technicien.edit.curry($V(form.plateau_id), '0')
		} );
  },
	
	updateTab: function(count) {
		var tab = $("tab-techniciens");
    tab.down("a").setClassName("empty", !count);
    tab.down("a small").update("("+count+")");
	}
} 