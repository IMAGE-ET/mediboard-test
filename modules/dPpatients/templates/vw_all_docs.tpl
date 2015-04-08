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

  toggleLabels = function() {
    var form = getForm("filterDisplay");
    var elts = form.elements["display"];
    var display = $V(form.display);

    $A(elts).each(function(elt) {
      var label = elt.up("label");
      if (elt.value == display) {
        label.setStyle({"color": ""});
        label.removeClassName("opacity-70");
        label.addClassName("opacity-100");
      }
      else {
        label.setStyle({"color": "#AAA"});
        label.removeClassName("opacity-100");
        label.addClassName("opacity-70");
      }
    });
  };

  Main.add(function() {
    loadAllDocs();
  });
</script>

<form name="filterDisplay" method="get">
  <input type="hidden" name="patient_id" value="{{$patient_id}}" />
  <table class="tbl">
    <tr>
      <th class="title">
        <input type="text" style="float: left;" class="search" onkeyup="filterResults(this.value)" />
        <button type="button" style="float: right;" class="add">Ajouter un document</button>
        <select name="tri" onchange="loadAllDocs()">
          <option value="date"  {{if $tri == "date"}}selected{{/if}}>Date</option>
          <option value="context" {{if $tri == "context"}}selected{{/if}}>Contexte</option>
          <option value="cat" {{if $tri == "cat"}}selected{{/if}}>Catégorie</option>
        </select>
        &nbsp; &nbsp;
        <label style="font-family: FontAwesome; font-size: 13pt; font-weight: normal; {{if $display != "icon"}}color: #AAA;{{/if}}"
               class="{{if $display != "icon"}}opacity-70{{/if}}">
          <input type="radio" name="display" value="icon" {{if $display == "icon"}}checked{{/if}} onclick="toggleLabels(); loadAllDocs()"
                 style="display: none;" /> &#xf00a;
        </label>
        &nbsp; &nbsp;
        <label style="font-family: FontAwesome; font-size: 13pt; font-weight: normal; {{if $display != "list"}}color: #AAA;{{/if}}"
               class="{{if $display != "list"}}opacity-70{{/if}}">
          <input type="radio" name="display" value="list" {{if $display == "list"}}checked{{/if}} onclick="toggleLabels(); loadAllDocs()"
                 style="display: none;" /> &#xf0ca;
        </label>
      </th>
    </tr>
  </table>
</form>

<div id="area_docs" style="width: 100%;"></div>