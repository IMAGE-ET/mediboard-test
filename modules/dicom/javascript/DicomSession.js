/* $:Id$ */

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $:Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

DicomSession = {

  refreshSessionsList : function(form) {
    var url = new Url("dicom", "ajax_refresh_session_list");
    url.addFormData(form);
    url.requestUpdate("sessionsList");
    return false;
  },

  viewSession : function(session_guid) {
    var url = new Url("dicom", "ajax_vw_session_details");
    url.addParam("session_guid", session_guid);
    url.requestModal(1000,500);
  },

  changePage : function(page) {
    $V(getForm('sessionsFilters').page,page);
  }
}