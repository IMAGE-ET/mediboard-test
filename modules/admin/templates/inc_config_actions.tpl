{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function checkSiblings() {
  var CCAMUrl = new Url("admin", "check_siblings");
  CCAMUrl.requestUpdate("check_siblings");
}
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="checkSiblings()">{{tr}}mod-admin-action-check_siblings{{/tr}}</button></td>
    <td id="check_siblings"></td>
  </tr>
</table>