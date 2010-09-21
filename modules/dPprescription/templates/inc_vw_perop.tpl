{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
submitPlanif = function(administration_id){
  var planif_id = $V(getForm("callbackForm").planif_id);

  var editPlanif = getForm("editPlanif-"+planif_id);
  $V(editPlanif.administration_id, administration_id);
	return onSubmitFormAjax(editPlanif, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}')});
}

</script>
{{assign var=images value="CPrescription"|static:"images"}}

<form name="callbackForm" action="?" method="">
  <input type="hidden" id="planif_id" />
</form>

<table class="tbl">
	<tr>
	  <th>Date</th>
		<th>Heure<br />prévue</th>
		<th>Soin</th>
		<th>Quantité<br /> prévue</th>
		<th>Unité</th>
		<th>Administration</th>
	</tr>	
	{{foreach from=$lines item=_planif}}
	  <tr>
	  	<td style="text-align: center;">{{mb_ditto name="date" value=$_planif->dateTime|date_format:$dPconfig.date}}</td>
			<td style="text-align: center;">{{mb_ditto name="time" value=$_planif->dateTime|date_format:$dPconfig.time}}</td>
			<td class="text">{{$_planif->_ref_object->_view}}</td>
			<td style="text-align: center;">
			  {{if $_planif->_ref_object instanceof CPrescriptionLineMixItem}}
				  {{assign var=quantite value=$_planif->_ref_object->quantite}}
				{{else}}
				  {{assign var=quantite value=$_planif->_ref_prise->_quantite_administrable}} 
				{{/if}}
				{{$quantite}}
			</td>
			<td>
				{{assign var=unite value=""}}
			  {{if $_planif->_ref_object instanceof CPrescriptionLineMedicament}}
				  {{assign var=unite value=$_planif->_ref_object->_unite_administration}}
        {{/if}}
				{{if $_planif->_ref_object instanceof CPrescriptionLineMixItem}}
          {{assign var=unite value=$_planif->_ref_object->unite}}
        {{/if}}
				{{$unite}}
			</td>	
			<td>
				{{assign var=can_adm value=false}}
				{{if ($_planif->_ref_object instanceof CPrescriptionLineMixItem && $_planif->_ref_object->_ref_prescription_line_mix->signature_prat)}}
				  {{assign var=can_adm value=true}}
       	{{elseif ($_planif->_ref_object instanceof CPrescriptionLineMedicament || $_planif->_ref_object instanceof CPrescriptionLineElement) && $_planif->_ref_object->signee}}
				  {{assign var=can_adm value=true}}
        {{/if}}
				
				{{if $can_adm}}
				<script type="text/javascript">
					{{if !$_planif->administration_id}}
						var oForm = getForm("addAdministrationPerop-{{$_planif->_id}}");
						//Calendar.regField(oForm.dateTime);
			      oForm.quantite.addSpinner({min:0});
					{{/if}}
				</script>
				
				<form name="addAdministrationPerop-{{$_planif->_id}}" action="?" method="post">
					<input type="hidden" name="m" value="dPprescription" />
					<input type="hidden" name="dosql" value="do_administration_aed" />
					
					{{if $_planif->administration_id}}
					  <input type="hidden" name="del" value="1" />
						
						<button type="button" class="cancel notext" onclick="return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') })">Annuler</button>
            {{$_planif->_ref_administration->quantite}} {{$unite}} à {{$_planif->_ref_administration->dateTime|date_format:$dPconfig.time}}
						<input type="hidden" name="administration_id" value="{{$_planif->administration_id}}" />
					{{else}}
						<input type="hidden" name="del" value="0" />
	          
	          <input type="hidden" name="object_id" value="{{$_planif->object_id}}" />
	          <input type="hidden" name="object_class" value="{{$_planif->object_class}}" />
	          <input type="hidden" name="unite_prise" value="{{$_planif->unite_prise}}" />
	          <input type="hidden" name="prise_id" value="{{$_planif->prise_id}}" />
	          <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
	          
	          <input type="hidden" name="dateTime" value="current" />
	          <input type="text" name="quantite" value="{{$quantite}}" size=3/>
	          <input type="hidden" name="callback" value="submitPlanif" />
	          <button type="button" class="tick notext" onclick="$V(getForm('callbackForm').planif_id, '{{$_planif->_id}}'); return onSubmitFormAjax(this.form);">Administrer</button>
					{{/if}}
				</form>
				
				<form name="editPlanif-{{$_planif->_id}}" action="?" method="post">
					<input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_planification_systeme_aed" />
          <input type="hidden" name="del" value="0" />
					<input type="hidden" name="planification_systeme_id" value="{{$_planif->_id}}" />
					<input type="hidden" name="administration_id" value="" />
				</form>
				{{else}}
				<div class="small-info">
					Ligne non signée
				</div>
				{{/if}}
			</td>
	  </tr>
	{{foreachelse}}
	<tr>
		<td colspan="6">Aucune planification per-opératoire</td>
	</tr>
	{{/foreach}}
</table>