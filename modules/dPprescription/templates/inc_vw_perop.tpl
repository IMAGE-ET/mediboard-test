{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">

submitAllAdmPerop = function(){
  var cpt = 0;
  $('list-perop').select("form.admPerop").each(function(e){
	  onSubmitFormAjax(e, {
		  onComplete: function(){
        cpt++;
				if (cpt == ($('list-perop').select("form.admPerop")).length){
				  Prescription.updatePerop('{{$sejour_id}}');
				}
			}
		});
	});
}

updateFormLinePerop = function(prescription_id){
  var oFormProt = getForm("applyProtocolePerop");
  $V(oFormProt.prescription_id, prescription_id, $V(oFormProt.pack_protocole_id) ? true : false);
}


submitProtocolePerop = function(){
  return onSubmitFormAjax(getForm("applyProtocolePerop"), { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });
}


Main.add( function(){
  var oFormProtocole = getForm("applyProtocolePerop");
  if(oFormProtocole){
    var url = new Url("dPprescription", "httpreq_vw_select_protocole");
    var autocompleter = url.autoComplete(oFormProtocole.libelle_protocole, "protocole_perop_auto_complete", {
      dropdown: true,
      minChars: 2,
      valueElement: oFormProtocole.elements.pack_protocole_id,
      updateElement: function(selectedElement) {
        var node = $(selectedElement).down('.view');
        $V(oFormProtocole.libelle_protocole, node.innerHTML.replace("&lt;", "<").replace("&gt;",">"));
        if (autocompleter.options.afterUpdateElement)
          autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
      },
      callback: 
        function(input, queryString){
          return (queryString + "&prescription_id={{$prescription_id}}&praticien_id={{$app->user_id}}&perop=true&type=sejour"); 
        }
    } );  
  }
} );


</script>

{{assign var=images value="CPrescription"|static:"images"}}

<table class="tbl" id="list-perop">
	{{if $app->_ref_user->isPraticien()}}
  <tr>
    <td colspan="6">
    	
			{{if $prescription_id}}
			<form name="signaturePrescription" method="post" action="">
        <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
        <input type="hidden" name="chapitre" value="all" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
				<input type="hidden" name="only_perop" value="true" />
        <button type="button" class="tick" onclick="return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });" 
				        style="float: right;">Tout signer</button>
      </form>
			{{/if}}		
								
    	<!-- Formulaire d'ajout de prescription -->
			<form action="?" method="post" name="addPrescriptionPerop" onsubmit="return checkForm(this);">
			  <input type="hidden" name="m" value="dPprescription" />
			  <input type="hidden" name="dosql" value="do_prescription_aed" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="prescription_id" value=""/>
			  <input type="hidden" name="object_id" value="{{$sejour_id}}" />
			  <input type="hidden" name="object_class" value="CSejour" />
			  <input type="hidden" name="type" value="sejour" />
			  <input type="hidden" name="callback" value="updateFormLinePerop" />
			</form>

    	<form name="applyProtocolePerop" method="post" action="?" onsubmit="if(!this.prescription_id.value){ return onSubmitFormAjax(getForm('addPrescriptionPerop'))} else { return submitProtocolePerop() };">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_apply_protocole_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription_id}}" onchange="this.form.onsubmit();"/>
        <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
        <input type="hidden" name="pratSel_id" value="" />
        <input type="hidden" name="pack_protocole_id" value="" />
        <input type="hidden" name="advanced_prot" value="0" />
        <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete" style="font-weight: bold; font-size: 1.3em; width: 300px;"/>
        <div style="display:none; width: 350px;" class="autocomplete" id="protocole_perop_auto_complete"></div>
				<input type="hidden" name="operation_id" value="{{$operation_id}}" />
        <button type="submit" class="submit">Appliquer le protocole</button>
      </form>
		
    </td>
	</tr>
	{{/if}}
	<tr>
	  <th>Date</th>
		<th>Heure<br />prévue</th>
		<th>Soin</th>
		<th>Quantité<br /> prévue</th>
		<th>Unité</th>
		<th>Administration <button type="button" class="tick notext" onclick="submitAllAdmPerop();"></button></th>
	</tr>	
	{{foreach from=$lines item=_planif}}
	  <tr>
	  	<td style="text-align: center;">{{mb_ditto name="date" value=$_planif->dateTime|date_format:$conf.date}}</td>
			<td style="text-align: center;">{{mb_ditto name="time" value=$_planif->dateTime|date_format:$conf.time}}</td>
			<td class="text">{{$_planif->_ref_object->_view}}</td>
			<td style="text-align: center;">
			  {{if $_planif->_ref_object instanceof CPrescriptionLineMixItem}}
				  {{assign var=quantite value=$_planif->_ref_object->_quantite_administration}}
				{{else}}
				  {{assign var=quantite value=$_planif->_ref_prise->_quantite_administrable}} 
				{{/if}}
				{{$quantite}}
			</td>
			<td>
				{{assign var=unite value=""}}
			  {{if $_planif->_ref_object instanceof CPrescriptionLineMedicament || $_planif->_ref_object instanceof CPrescriptionLineMixItem}}
				  {{assign var=unite value=$_planif->_ref_object->_ref_produit->libelle_unite_presentation}}
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
					<form {{if $_planif->_ref_administrations|@count == 0}}class="admPerop"{{/if}} name="addAdministrationPerop-{{$_planif->_id}}" action="?" method="post">
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
					     <button type="button" class="cancel notext" onclick="$V(this.form.del, 1); return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });"></button>
            
							{{mb_field object=$_adm field="dateTime" onchange="return onSubmitFormAjax(this.form);"}} <strong>{{$_adm->quantite}} {{$unite}}</strong>
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