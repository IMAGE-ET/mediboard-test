{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage CCAM
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
refreshFraisDivers = function(){
  var url = new Url("dPccam", "ajax_refresh_add_frais_divers");
  url.addParam("object_guid", "{{$object->_guid}}");
  url.requestUpdate("editFraisDivers-{{$object->_guid}}");
}
Main.add(refreshFraisDivers);
</script>

<div id="editFraisDivers-{{$object->_guid}}"></div>
