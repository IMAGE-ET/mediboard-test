{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  DicomSession = {
    refreshSession : function() {
      
    },
    
    refreshSessionsList : function(form) {
      var url = new Url("dicom", "ajax_refresh_session_list");
      url.addFormData(form);
      url.requestUpdate("sessionsList");
      return false;
    },
    
    viewSession : function(session_guid) {
      var url = new Url("dicom", "ajax_vw_session_details");
      url.addParam("session_guid", session_guid);
      url.requestModal(1000,1000);
    }
  }
</script>

<table class="main layout">
  <tr>
    <td id="search">
      {{mb_include template="inc_filter_sessions"}}
    </td>
  </tr>
  <tr>
    <td id="sessionsList">
      
    </td>
  </tr>
</table>
