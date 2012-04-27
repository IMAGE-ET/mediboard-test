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
  convert_files = function() {
    new Url("dPfiles", "ajax_file_to_pdf").
    addParam("nb_files", $V($("nb_files"))).
    requestUpdate("convert_files");
  }
  purge_files = function() {
    var oForm = getForm("selectDateFiles");
    new Url("dPfiles", "ajax_repair_files").
    addParam("purge", $V(oForm.purge) ? 1 : 0).
    addParam("date_debut", $V(oForm.date_debut)).
    addParam("date_fin", $V(oForm.date_fin)).
    addParam("nb_files", $V($("nb_files"))).
    addParam("step_from", $V(oForm.step_from)).
    requestUpdate("purge_files");
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
      <input type="text" id="nb_files" value="10" />
      <button type="button" class="button search" onclick="repair_files()">{{tr}}CFile-repair_files{{/tr}}</button>
    </th>
    <td>
      <div id="repair_files"></div>
    </td>
  </tr>
  <tr>
    <th>
      <button type="button" class="button search" onclick="convert_files()">{{tr}}CFile-convert_files{{/tr}}</button>
    </th>
    <td>
      <div id="convert_files"></div>
    </td>
  </tr>
  <tr>
    <th>
      <button type="button" class="button search" onclick="purge_files()">{{tr}}CFile-purge_files{{/tr}}</button>
      <br />
      <form name="selectDateFiles" method="get">
        <select name="step_from">
          {{foreach from=0|range:$nb_files item=i}}
            <option value="{{$i}}">{{$i}}</option>
          {{/foreach}}
        </select>
        
        Début : <input class="date notNull" type="hidden" name="date_debut" value="{{$today}}" />
        Fin : <input class="date notNull" type="hidden" name="date_fin" value="{{$today}}" />
        <label>
          <input type="checkbox" id="purge" /> Purge
        </label>
        <script type="text/javascript">
          Main.add(function () {
            var oForm = getForm('selectDateFiles')
            Calendar.regField(oForm.date_debut);
            Calendar.regField(oForm.date_fin);
          });
        </script>
      </form>
    </th>
    <td>
      <div id="purge_files"></div>
    </td>
  </tr>
</table>