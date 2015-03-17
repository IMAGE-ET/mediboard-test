{{*
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  loadAllDocs = function() {
    new Url("patients", "ajax_all_docs")
    .addFormData(getForm("filterDisplay"))
    .requestUpdate("area_docs");
  };

  Main.add(function() {
    loadAllDocs();
  });
</script>

<form name="filterDisplay" method="get">
  <input type="hidden" name="patient_id" value="{{$patient_id}}" />
  <table class="tbl">
    <tr>
      <th>
        <select name="tri" onchange="loadAllDocs()">
          <option value="date"  {{if $tri == "date"}}selected{{/if}}>Date</option>
          <option value="event" {{if $tri == "event"}}selected{{/if}}>Evénement</option>
        </select>
        &mdash;
        <label>
          <input type="radio" name="display" value="icon" {{if $display == "icon"}}checked{{/if}} onclick="loadAllDocs()" /> Icône
        </label>
        <label>
          <input type="radio" name="display" value="list" {{if $display == "list"}}checked{{/if}} onclick="loadAllDocs()" /> Liste
        </label>
        &mdash;
        <button type="button" class="add">Ajouter un élément</button>
      </th>
    </tr>
  </table>
</form>

<div id="area_docs" style="width: 100%"></div>