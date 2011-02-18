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
      {{mb_field object=$sejour field="mode_sortie" emptyLabel="Choose" onchange="initFields(this.value); this.form.onsubmit();"}}
      {{if !$rpu->mutation_sejour_id}}
        <input type="hidden" name="group_id" value="{{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}" />
      {{else}}
        <strong>
          <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
            Hospitalisation dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$rpu->_ref_sejour_mutation->_num_dossier}}
           </a> 
         </strong>
      {{/if}}
    </td>
	</tr>
	
  <tr id="etablissement_sortie_transfert" {{if $sejour->mode_sortie != "transfert"}} style="display:none;" {{/if}}>
  	<th>{{mb_label object=$sejour field="etablissement_transfert_id"}}</th>
    <td>{{mb_field object=$sejour field="etablissement_transfert_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}</td>
  </tr>

  <tr id="service_sortie_transfert" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}}>
  	<th>{{mb_label object=$sejour field="service_mutation_id"}}</th>
		<td>{{mb_field object=$sejour field="service_mutation_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}</td>
  </tr>
	
  <tr id="commentaires_sortie" {{if $sejour->mode_sortie == "" || $sejour->mode_sortie == "normal"}} style="display:none;" {{/if}}>
    <th>{{mb_label object=$sejour field="commentaires_sortie"}}</th>
    <td>
      {{main}}
        var form = getForm("editSejour");
        var options = {
          objectClass: "{{$sejour->_class_name}}",
          contextUserId: "{{$userSel->_id}}",
          contextUserView: "{{$userSel->_view}}",
          timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
          validate: function() { form.onsubmit(); },
          resetSearchField: false,
          resetDependFields: false,
          validateOnBlur: false
        }
        
        new AideSaisie.AutoComplete(form.elements.commentaires_sortie, options);
      {{/main}}
    	
			{{mb_field object=$sejour field="commentaires_sortie" onchange="this.form.onsubmit();"}}
			
			</td>
  </tr>
  
  <!-- Diagnostic Principal -->
	{{if !$ajax}} 
  <tr id="dp_{{$sejour->_id}}">
    {{mb_include module=dPurgences template=inc_diagnostic_principal}}
  </tr>         
	{{/if}}
</table>

</form>
