{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  testOperation = function() {
    new Url("files", "ajax_test_files").requestUpdate("test_create");
  };
  listFiles = function() {
    new Url("files", "ajax_repair_files").requestUpdate("list_files");
  };
  repairFiles = function() {
    new Url("files", "ajax_repair_files").
    addParam("nb_files", $V($("nb_files"))).
    requestUpdate("repair_files");
  };
  convertFiles = function() {
    new Url("files", "ajax_file_to_pdf").
    addParam("nb_files", $V($("nb_files"))).
    requestUpdate("convert_files");
  };
  purgeFiles = function() {
    var form = getForm("selectDateFiles");
    new Url("files", "ajax_repair_files").
    addParam("purge", $V(form.purge) ? 1 : 0).
    addParam("date_debut", $V(form.date_debut)).
    addParam("date_fin", $V(form.date_fin)).
    addParam("nb_files", $V($("nb_files"))).
    addParam("step_from", $V(form.step_from)).
    requestUpdate("purge_files");
  };
  shrinkPDF = function() {
    new Url("files", "ajax_test_shrink").
      requestUpdate("shrink_pdf");
  };
</script>
<table class="form">
  <tr>
    <th style="width: 50%;">
      <button type="button" class="button search" onclick="testOperation()">{{tr}}CFile-test_create{{/tr}}</button>
    </th>
    <td>
      <div id="test_create"></div>
    </td>
  </tr>
  <tr>
    <th>
      <button type="button" class="button search" onclick="listFiles()">{{tr}}CFile-test_no_size{{/tr}}</button>
    </th>
    <td>
      <div id="list_files"></div>
    </td>
  </tr>
  <tr>
    <th>
      <input type="text" id="nb_files" value="10" />
      <button type="button" class="button search" onclick="repairFiles()">{{tr}}CFile-repair_files{{/tr}}</button>
    </th>
    <td>
      <div id="repair_files"></div>
    </td>
  </tr>
  <tr>
    <th>
      <button type="button" class="button search" onclick="convertFiles()">{{tr}}CFile-convert_files{{/tr}}</button>
    </th>
    <td>
      <div id="convert_files"></div>
    </td>
  </tr>
  <tr>
    <th>
      <button type="button" class="button search" onclick="purgeFiles()">{{tr}}CFile-purge_files{{/tr}}</button>
      <br />
      <form name="selectDateFiles" method="get">
        <select name="step_from">
          {{foreach from=0|range:$nb_files item=i}}
            <option value="{{$i}}">{{$i}}</option>
          {{/foreach}}
        </select>

        Début : <input class="date notNull" type="hidden" name="date_debut" value="{{$today}}" />
        Fin :   <input class="date notNull" type="hidden" name="date_fin"   value="{{$today}}" />
        <label>
          <input type="checkbox" id="purge" /> Purge
        </label>
        <script>
          Main.add(function() {
            var oForm = getForm('selectDateFiles');
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
  <tr>
    <th>
      <button class="search" type="button" onclick="shrinkPDF()">Shrink de pdf</button>
    </th>
    <td>
      <div id="shrink_pdf"></div>
    </td>
  </tr>
</table>