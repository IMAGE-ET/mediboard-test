/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CotationRHS = {
  refresh: function(sejour_id) {
		new Url('ssr', 'ajax_cotation_rhs') .
		  addParam('sejour_id', sejour_id) .
			requestUpdate('cotation-rhs');
		
  },
  	
  onSubmitRHS: function(form) {
    return onSubmitFormAjax(form, { 
		  onComplete: CotationRHS.refresh.curry($V(form.sejour_id))
		} );
  },
	
	updateTab: function(count) {
		var tab = $("tab-equipements");
    tab.down("a").setClassName("empty", !count);
    tab.down("a small").update("("+count+")");
	}
};