{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{assign var=images value="CPrescription"|static:"images"}}

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
						var oForm = getForm("addAdministrationPerop-{{$_planif->_id}}");
	          oForm.quantite.addSpinner({min:0});
					</script>
					
					<!-- Formulaire de creation d'une administration -->
					<form name="addAdministrationPerop-{{$_planif->_id}}" action="?" method="post">
						<input type="hidden" name="m" value="dPprescription" />
						<input type="hidden" name="dosql" value="do_administration_aed" />
						<input type="hidden" name="del" value="0" />
	          <input type="hidden" name="planification_systeme_id" value="{{$_planif->_id}}" />
						<input type="hidden" name="object_id" value="{{$_planif->object_id}}" />
	          <input type="hidden" name="object_class" value="{{$_planif->object_class}}" />
	          <input type="hidden" name="unite_prise" value="{{$_planif->unite_prise}}" />
	          <input type="hidden" name="prise_id" value="{{$_planif->prise_id}}" />
	          <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
	          <input type="hidden" name="dateTime" value="current" />
						
						{{if $quantite > $_planif->_quantite_adm}}
						  {{assign var=qte value=$quantite-$_planif->_quantite_adm}}
						{{else}}
						  {{assign var=qte value=0}}
	          {{/if}}
	          <input type="text" name="quantite" value="{{$qte}}" size=3/>
	          <button type="button" class="tick notext" onclick="return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });">Administrer</button>
					</form>
		
					<!-- Parcours des administrations -->
					<br />
					{{foreach from=$_planif->_ref_administrations item=_adm}}
					  <script type="text/javascript">
	          var oForm = getForm("editAdm-{{$_adm->_id}}");
	          Calendar.regField(oForm.dateTime);
	          </script>
	        
					  <form name="editAdm-{{$_adm->_id}}" method="post" action="?">
					    <input type="hidden" name="m" value="dPprescription" />
	            <input type="hidden" name="dosql" value="do_administration_aed" />
	            <input type="hidden" name="del" value="0" />
						  <input type="hidden" name="administration_id" value="{{$_adm->_id}}" />
					    {{$_adm->quantite}} {{$unite}} {{mb_field object=$_adm field="dateTime" onchange="return onSubmitFormAjax(this.form);"}}
							<button type="button" class="cancel notext" onclick="$V(this.form.del, 1); return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });"></button>
						</form>
						<br />
					{{/foreach}}
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