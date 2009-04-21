/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

var prescriptionMed = {
 refresh: function(sejour_id) {
   var url = new Url;
   url.setModuleAction("dPprescription", "httpreq_vw_prescription_meds");
   url.addParam("sejour_id", sejour_id);
   url.requestUpdate("vw-prescription-meds", { waitingText: null } );
 },
 
 register: function(sejour_id, container) {
   var div = document.createElement("div");
   div.style.minWidth = "200px";
   div.style.minHeight = "30px";
   div.id = "vw-prescription-meds";
   $(container).insert(div);
   
   Main.add( function() {
     prescriptionMed.refresh(sejour_id);
   } );
 }
};