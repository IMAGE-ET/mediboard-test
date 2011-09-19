/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var Action = {  
  module: "smp",
		
  doExport: function (sAction, type) {
    var url = new Url(this.module, "ajax_export_"+type);
    url.addParam("action", sAction);
    url.requestUpdate("export-"+type);
  },
		
  repair: function (sAction) {
    var url = new Url(this.module, "ajax_repair_sejour");
    url.addParam("action", sAction);
    url.requestUpdate("repair");
  },
  
  doDelete: function (sAction) {
    var url = new Url(this.module, "ajax_delete_mvt");
    url.addParam("action", sAction);
    url.requestUpdate("delete-mvt");
  },
}