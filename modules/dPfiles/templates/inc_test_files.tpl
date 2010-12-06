{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  test_operation = function() {
    var url = new Url("dPfiles", "ajax_test_files");
    url.requestUpdate("test_create");
  }
</script>
<table class="form">
  <tr>
    <th style="width: 50%;">
      <button type="button" class="button search" onclick="test_operation()">{{tr}}CFile-test_create{{/tr}}</button>
    </th>
    <td>
      <div id="test_create"/>
    </td>
  </tr>
</table>