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
    if (!$("area_docs") || !getForm("filterDisplay")) {
      return;
    }

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
        label.removeClassName("opacity-30");
        label.addClassName("opacity-100");
      }
      else {
        label.removeClassName("opacity-100");
        label.addClassName("opacity-30");
      }
    });
  };

  Main.add(function() {
    loadAllDocs();
  });
</script>

{{*
<div style="height: 400px; overflow-y: auto;">
  <div style="height: 30px; width: 100%;">
    <div style="position: absolute; width: 100%; z-index: 200;">*}}
      <form name="filterDisplay" method="get">
        <input type="hidden" name="patient_id"   value="{{$patient_id}}" />
        <input type="hidden" name="context_guid" value="{{$context_guid}}" />
        <table class="tbl">
          <tr>
            <th class="title" style="vertical-align: middle;">
              <input type="text" style="float: left;" class="search" onkeyup="filterResults(this.value)" />

              <span style="float: right;">
                {{mb_include module=patients template=inc_button_add_doc}}
              </span>

              <select name="tri" onchange="loadAllDocs()">
                <option value="date"    {{if $tri == "date"}}   selected{{/if}}>Date     </option>
                {{if preg_match("/^CPatient/", $context_guid)}}
                  <option value="context" {{if $tri == "context"}}selected{{/if}}>Contexte </option>
                {{/if}})
                <option value="cat"     {{if $tri == "cat"}}    selected{{/if}}>Catégorie</option>
              </select>

              <label style="font-family: FontAwesome; font-size: 13pt; font-weight: normal; margin-left: 10px; margin-right: 5px;"
                     class="{{if $display != "icon"}}opacity-30{{/if}}">
                <input type="radio" name="display" value="icon" {{if $display == "icon"}}checked{{/if}} onclick="toggleLabels(); loadAllDocs()"
                       style="display: none;" />&#xf00a;
              </label>

              <label style="font-family: FontAwesome; font-size: 13pt; font-weight: normal; margin-left: 5px; margin-right: 10px;"
                     class="{{if $display != "list"}}opacity-30{{/if}}">
                <input type="radio" name="display" value="list" {{if $display == "list"}}checked{{/if}} onclick="toggleLabels(); loadAllDocs()"
                       style="display: none;" />&#xf0ca;
              </label>
            </th>
          </tr>
        </table>

      </form>
{{*</div>
</div>*}}
  <div id="area_docs" style="width: 100%; position: relative;"></div>
{{*</div>*}}