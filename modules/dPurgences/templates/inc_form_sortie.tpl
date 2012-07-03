{{* $Id: inc_vw_rpu.tpl 11346 2011-02-17 20:38:29Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 11346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
      {{mb_field object=$sejour field="mode_sortie" emptyLabel="Choose" onchange="Fields.init(this.value); this.form.onsubmit();"}}
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

  <tr id="lit_sortie_transfert" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
    <th>Lit</th>
    <td>
      <select name="lit_id" style="width: 15em;" onchange="Fields.modif(this.value);this.form.sortie_reelle.value = '';"  >
        <option value="0">&mdash; Choisir Lit </option>
        {{foreach from=$blocages_lit item=blocage_lit}}
          <option id="{{$blocage_lit->_ref_lit->_guid}}" value="{{$blocage_lit->lit_id}}"
           class="{{$blocage_lit->_ref_lit->_ref_chambre->_ref_service->_guid}}-{{$blocage_lit->_ref_lit->_ref_chambre->_ref_service->nom}}"
           {{if $blocage_lit->_ref_lit->_view|strpos:"indisponible"}}disabled{{/if}}>
            {{$blocage_lit->_ref_lit->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr id="service_sortie_transfert" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
    <th>{{mb_label object=$sejour field="service_sortie_id"}}</th>
    <td>
      {{mb_field object=$sejour field="service_sortie_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}
      <input type="hidden" name="cancelled" value="0" />
    </td>
  </tr>
  
  <tr id="date_deces" {{if $sejour->mode_sortie != "deces"}}style="display: none"{{/if}}>
    <th>{{mb_label class="CPatient" field="deces"}}</th>
    <td>
      <input type="hidden" name="_date_deces" value="{{$sejour->_ref_patient->deces}}" onchange="this.form.onsubmit()"
          class="date progressive {{if $sejour->mode_sortie == "deces"}}notNull{{/if}}" />
      <script type="text/javascript">
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
