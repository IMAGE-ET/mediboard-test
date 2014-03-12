{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPurgences
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    {{if !$conf.dPplanningOp.CSejour.use_custom_mode_sortie}}
      changeOrientation(getForm("editSejour").elements.mode_sortie);
    {{/if}}
  });

  updateModeSortie = function(select) {
    var selected = select.options[select.selectedIndex];
    var form = select.form;
    $V(form.elements.mode_sortie, selected.get("mode"));
  };
  updateLitMutation = function(element) {
    {{if $conf.dPurgences.use_blocage_lit}}
      var form = getForm('editSejour');
      if (form.mode_sortie.value == "mutation") {
        var url = new Url('urgences', 'ajax_refresh_lit');
        url.addParam('rpu_id'  , '{{$rpu->_id}}');
        url.addParam('sortie_reelle'  , element.value);
        url.requestUpdate("lit_sortie_transfert");
      }
    {{/if}}
  };
  //@todo a factoriser avec contraintes_RPU
  //Changement de l'orientation en fonction du mode sortie
  changeOrientation = function(element) {
    var orientation = getForm("editRPU").elements.orientation;
    var destination = getForm("editRPU").elements._destination;
      if (!orientation) {
      orientation = getForm("editRPUDest").elements.orientation;
      destination = getForm("editRPUDest").elements._destination;
    }
    var option_orientation = $A(orientation.options);
    var option_destination = $A(destination.options);
    var exclude = ["SCAM","PSA","REO"];
    var exclude_destination = ["6", "7"];
    switch ($V(element)) {
      case "normal":
        option_orientation.each(function(option) {
          //Si les options sont exclues on les désactive sinon on les réactive
          if (exclude.indexOf(option.value) === -1 && option.value !== "") {
            //Si l'option est sélectionnée et que dans ce cas, il n'est pas disponible, on met le sélectionne par défaut
            if (option.selected) {
              orientation.selectedIndex = 0;
            }
            option.disabled = true;
          }
          else {
            option.disabled = false;
          }
        });
        option_destination.each(function(option) {
          //Si les options sont exclues on les désactive sinon on les réactive
          if (exclude_destination.indexOf(option.value) === -1 && option.value !== "") {
            //Si l'option est sélectionnée et que dans ce cas, il n'est pas disponible, on met le sélectionne par défaut
            if (option.selected) {
              destination.selectedIndex = 0;
            }
            option.disabled = true;
          }
          else {
            option.disabled = false;
          }
        });
        break;
      case "mutation":
      case "transfert":
        option_orientation.each(function(option) {
          if (exclude.indexOf(option.value) !== -1) {
            if (option.selected) {
              orientation.selectedIndex = 0;
            }
            option.disabled = true;
          }
          else {
            option.disabled = false;
          }
        });
        option_destination.each(function(option) {
          if (exclude_destination.indexOf(option.value) !== -1) {
            if (option.selected) {
              destination.selectedIndex = 0;
            }
            option.disabled = true;
          }
          else {
            option.disabled = false;
          }
        });
        break;
      case "deces":
        option_orientation.each(function(option) {
          option.disabled = true;
        });
        option_destination.each(function(option) {
          option.disabled = true;
        });
      break;
      default:
        option_orientation.each(function(option) {
          option.disabled = false;
        });
        option_destination.each(function(option) {
          option.disabled = false;
        });
    }
  };
</script>

<form name="editSejour" action="?" method="post"  onsubmit="return submitSejour()">
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="type" value="{{$sejour->type}}" />

  <table class="form">
    {{if $can->edit}}
      <tr {{if !$ajax}} style="display: none" {{/if}}>
        <th>{{mb_label object=$sejour field=entree_reelle}}</th>
        <td>
          {{mb_field object=$sejour field=entree_reelle hidden=true}}
          {{mb_value object=$sejour field=entree_reelle}}
        </td>
      </tr>

      {{if $rpu->sejour_id !== $rpu->mutation_sejour_id}}
        <tr>
          <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
          <td>
            {{mb_field object=$sejour field=sortie_reelle form=editSejour onchange="this.form.onsubmit();updateLitMutation(this);" register=true}}
          </td>
        </tr>
      {{/if}}
    {{/if}}

    <tr>
      <th style="width: 30%">{{mb_label object=$sejour field="mode_sortie"}}</th>
      <td>
        {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
          {{mb_field object=$sejour field=mode_sortie onchange="\$V(this.form._modifier_sortie, 0); Fields.init(this.value); this.form.onsubmit();" hidden=true}}
          <select name="mode_sortie_id" class="{{$sejour->_props.mode_sortie_id}}" style="width: 16em;" onchange="updateModeSortie(this)">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$list_mode_sortie item=_mode}}
              <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" {{if $sejour->mode_sortie_id == $_mode->_id}}selected{{/if}}>
                {{$_mode}}
              </option>
            {{/foreach}}
          </select>
        {{elseif "CAppUI::conf"|static_call:"dPurgences CRPU impose_create_sejour_mutation":"CGroups-$g"}}
          <select name="mode_sortie" onchange="changeOrientation(this);Fields.init(this.value); this.form.onsubmit();">
            {{foreach from=$sejour->_specs.mode_sortie->_list item=_mode}}
              <option value="{{$_mode}}" {{if $sejour->mode_sortie == $_mode}}selected{{/if}}
                {{if $_mode == "mutation" && !$rpu->mutation_sejour_id}}disabled{{/if}}>
                {{tr}}CSejour.mode_sortie.{{$_mode}}{{/tr}}
              </option>
            {{/foreach}}
          </select>
        {{else}}
          {{mb_field object=$sejour field="mode_sortie" onchange="changeOrientation(this);Fields.init(this.value); this.form.onsubmit();"}}
        {{/if}}
        {{if !$rpu->mutation_sejour_id}}
          <input type="hidden" name="group_id" value="{{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}" />
        {{else}}
          <strong>
            <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
              Hospitalisation dossier {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$rpu->_ref_sejour_mutation}}
             </a>
           </strong>
        {{/if}}
      </td>
    </tr>

    <tr id="etablissement_sortie_transfert" {{if $sejour->mode_sortie != "transfert"}} style="display:none;" {{/if}}>
      <th>{{mb_label object=$sejour field="etablissement_sortie_id"}}</th>
      <td>{{mb_field object=$sejour field="etablissement_sortie_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="getObjectService(this);this.form.onsubmit();"}}</td>
    </tr>

    {{if $conf.dPurgences.use_blocage_lit}}
      {{mb_include module=urgences template=inc_form_sortie_lit}}
    {{/if}}

    <tr id="service_sortie_transfert" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
      <th>{{mb_label object=$sejour field="service_sortie_id"}}</th>
      <td>
        <input type="hidden" name="service_sortie_id" value="{{$sejour->service_sortie_id}}"
          class="autocomplete" onchange="this.form.onsubmit();" size="25"  />
        <input type="text" name="service_sortie_id_autocomplete_view" value="{{$sejour->_ref_service_mutation}}"
          class="autocomplete" onchange='if(!this.value){this.form["service_sortie_id"].value=""}'size="25"  />

        <script>
          Main.add(function(){
            var form = getForm("editSejour");
            var input = form.service_sortie_id_autocomplete_view;
            var url = new Url("system", "httpreq_field_autocomplete");
            url.addParam("class", "CSejour");
            url.addParam("field", "service_sortie_id");
            url.addParam("limit", 50);
            url.addParam("view_field", "nom");
            url.addParam("show_view", false);
            url.addParam("input_field", "service_sortie_id_autocomplete_view");
            url.addParam("wholeString", true);
            url.addParam("min_occurences", 1);
            url.autoComplete(input, "service_sortie_id_autocomplete_view", {
              minChars: 1,
              method: "get",
              select: "view",
               dropdown: true,
              afterUpdateElement: function(field,selected){
                $V(field.form["service_sortie_id"], selected.getAttribute("id").split("-")[2]);
                var elementFormRPU = getForm("editRPU").elements;
                if (!elementFormRPU) {
                  elementFormRPU = getForm("editRPUDest").elements;
                }
                var selectedData = selected.down(".data");
                if (!elementFormRPU._destination.value) {
                  $V(elementFormRPU._destination, selectedData.get("default_destination"));
                }
                if (!elementFormRPU.orientation.value) {
                  $V(elementFormRPU.orientation, selectedData.get("default_orientation"));
                }
              },
              callback: function(element, query){
                query += "&where[group_id]={{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}";
                var field = input.form.elements["cancelled"];
                if (field) {
                  query += "&where[cancelled]=" + $V(field);  return query;
                }
                return null;
              }
            });
          });
        </script>

        <input type="hidden" name="cancelled" value="0" />
      </td>
    </tr>

    <tr id="date_deces" {{if $sejour->mode_sortie != "deces"}}style="display: none"{{/if}}>
      <th>{{mb_label class="CPatient" field="deces"}}</th>
      <td>
        <input type="hidden" name="_date_deces" value="{{$sejour->_ref_patient->deces}}" onchange="this.form.onsubmit()"
            class="date progressive {{if $sejour->mode_sortie == "deces"}}notNull{{/if}}" />
        <script>
          Main.add(function() {
            Calendar.regProgressiveField(getForm('editSejour')._date_deces);
          });
        </script>
      </td>
    </tr>

    {{if $rpu->sejour_id !== $rpu->mutation_sejour_id}}
      <tr>
        <th>{{mb_label object=$sejour field=transport_sortie}}</th>
        <td>
          {{mb_field object=$sejour field=transport_sortie form=editSejour onchange="this.form.onsubmit();" register=true}}
        </td>
      </tr>

      <tr>
        <th>{{mb_label object=$sejour field=rques_transport_sortie}}</th>
        <td>
          {{mb_field object=$sejour field=rques_transport_sortie form=editSejour onchange="this.form.onsubmit();" register=true}}
        </td>
      </tr>
    {{/if}}

    <tr id="commentaires_sortie">
      <th>{{mb_label object=$sejour field="commentaires_sortie"}}</th>
      <td>
        {{mb_field object=$sejour field="commentaires_sortie" onchange="this.form.onsubmit();" form="editSejour"
          aidesaisie="validate: function() { form.onsubmit();},
                      resetSearchField: 0,
                      resetDependFields: 0,
                      validateOnBlur: 0" }}
        </td>
    </tr>

    <!-- Diagnostic Principal -->
    {{if !$ajax}}
    <tr id="dp_{{$sejour->_id}}">
      {{mb_include module=urgences template=inc_diagnostic_principal}}
    </tr>
    {{/if}}
  </table>
</form>
