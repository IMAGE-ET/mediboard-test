/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

SIP = {
  module: "sip",
		
  doExport: function (sAction, type) {
    var url = new Url(this.module, "ajax_export_"+type);
    url.addParam("action", sAction);
    url.requestUpdate("export-"+type);
  },

  findCandidates: function(form) {
    return Url.update(form, "find_candidates");
  }
}