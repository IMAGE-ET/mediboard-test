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
    new Url("dPfiles", "ajax_test_files").requestUpdate("test_create");
  }
  list_files = function() {
    new Url("dPfiles", "ajax_repair_files").requestUpdate("list_files");
  }
  repair_files = function() {
    new Url("dPfiles", "ajax_repair_files").
    addParam("nb_files", $V($("nb_files"))).
    requestUpdate("repair_files");
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
  <tr>
    <th>
      <button type="button" class="button search" onclick="list_files()">{{tr}}CFile-test_no_size{{/tr}}</button>
    </th>
    <td>
      <div id="list_files"></div>
    </td>
  </tr>
  <tr>
    <th>
      <input type="text" id="nb_files" value="10"></input>
      <button type="button" class="button search" onclick="repair_files()">{{tr}}CFile-repair_files{{/tr}}</button>
    </th>
    <td>
      <div id="repair_files"></div>
    </td>
  </tr>
</table>