{{* $Id: inc_vw_rpu.tpl 11346 2011-02-17 20:38:29Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 11346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  updateModeSortie = function(select) {
    var selected = select.options[select.selectedIndex];
    var form = select.form;
    $V(form.elements.mode_sortie, selected.get("mode"));
  }
</script>

<form name="editSejour" action="?" method="post"  onsubmit="return submitSejour()">

<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
<input type="hidden" name="type" value="{{$sejour->type}}" />

<table class="form" style="width: 100%">
  
  {{if $can->edit}}
  
  <tr {{if !$ajax}} style="display: none" {{/if}}>
    <th>{{mb_label object=$sejour field=entree_reelle}}</th>
    <td>
      {{mb_field object=$sejour field=entree_reelle hidden=true}}
      {{mb_value object=$sejour field=entree_reelle}}
    </td> 
  </tr>

  <tr>
    <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
    <td>
      {{mb_field object=$sejour field=sortie_reelle form=editSejour onchange="this.form.onsubmit();" register=true}}
    </td> 
  </tr>
  {{/if}}

  <tr>
    <th style="width: 120px;">{{mb_label object=$sejour field="mode_sortie"}}</th>
    <td>
      {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
        {{mb_field object=$sejour field=mode_sortie onchange="\$V(this.form._modifier_sortie, 0); Fields.init(this.value); this.form.onsubmit();" hidden=true}}
        <select name="mode_sortie_id" class="{{$sejour->_props.mode_sortie_id}}" onchange="updateModeSortie(this)">
          {{foreach from=$list_mode_sortie item=_mode}}
            <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" {{if $sejour->mode_sortie_id == $_mode->_id}}selected{{/if}}>
              {{$_mode}}
            </option>
          {{/foreach}}
        </select>
      {{else}}
        {{mb_field object=$sejour field="mode_sortie" onchange="Fields.init(this.value); this.form.onsubmit();"}}
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
    <td>{{mb_field object=$sejour field="etablissement_sortie_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}</td>
  </tr>
  
  {{if $conf.dPurgences.use_blocage_lit}}
    <tr id="lit_sortie_transfert" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
      <th>Lit</th>
      <td>
        <select name="lit_id" style="width: 15em;" onchange="Fields.modif(this.value);this.form.sortie_reelle.value = '';"  >
          <option value="0">&mdash; Choisir Lit </option>
          {{foreach from=$blocages_lit item=blocage_lit}}
            <option id="{{$blocage_lit->_ref_lit->_guid}}" value="{{$blocage_lit->lit_id}}"
             class="{{$blocage_lit->_ref_lit->_ref_chambre->_ref_service->_guid}}-{{$blocage_lit->_ref_lit->_ref_chambre->_ref_service->nom}}"
             {{if $blocage_lit->_ref_lit->_view|strpos:"indisponible"}}disabled{{/if}}
             {{if $blocage_lit->lit_id == $sejour->_ref_curr_affectation->lit_id}}selected{{/if}}>
              {{$blocage_lit->_ref_lit->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
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
            },
            callback: function(element, query){
              query += "&where[group_id]={{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}";
              var field = input.form.elements["cancelled"];
              if (field) {
                query += "&where[cancelled]=" + $V(field);  return query;
              }
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
