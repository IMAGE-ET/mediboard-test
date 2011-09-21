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

viewTimingPerf = function(prescription_line_mix_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "edit_perf_dossier_soin");
  url.addParam("prescription_line_mix_id", prescription_line_mix_id);
  url.addParam("mode_refresh", "timing");
  url.popup(600, 600);
}

submitTimingPosePerf = function(oFormPerf){
  $V(oFormPerf.date_pose, 'current');
  $V(oFormPerf.time_pose, 'current');
  return onSubmitFormAjax(oFormPerf, { onComplete: function(){ 
    Prescription.updatePerop('{{$sejour_id}}');
  } } )
}

submitTimingRetraitPerf = function(oFormPerf){
  $V(oFormPerf.date_retrait, 'current');
  $V(oFormPerf.time_retrait, 'current');
  return onSubmitFormAjax(oFormPerf, { onComplete: function(){ 
    Prescription.updatePerop('{{$sejour_id}}');
  } } )
}

updateFormLinePerop = function(prescription_id){
  var oFormSignaturePerop = getForm("signaturePrescription");
	if(oFormSignaturePerop){
	  $V(oFormSignaturePerop.prescription_id, prescription_id);
  }
	var oFormProt = getForm("applyProtocolePerop");
  $V(oFormProt.prescription_id, prescription_id, $V(oFormProt.pack_protocole_id) ? true : false);
}

submitProtocolePerop = function(){
  return onSubmitFormAjax(getForm("applyProtocolePerop"), { 
	  onComplete: function(){
		  if(!getForm("applyProtocolePerop")){
		    Prescription.updatePerop('{{$sejour_id}}');
			}
		}
	});
}

signatureLinesPerop = function(){
  var oFormSignaturePerop = getForm("signaturePrescription");
  
	// Signature seulement present si le user est un prat
	if(oFormSignaturePerop){
	 return onSubmitFormAjax(oFormSignaturePerop, { onComplete:  Prescription.updatePerop.curry('{{$sejour_id}}') });
	} else {
	 Prescription.updatePerop('{{$sejour_id}}');
	}
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
				  var praticien_id = $V(getForm("applyProtocolePerop").praticien_id);
          return (queryString + "&prescription_id={{$prescription_id}}&praticien_id="+praticien_id+"&perop=true&type=sejour"); 
        }
    } );  
  }
} );

</script>

{{assign var=images value="CPrescription"|static:"images"}}

<div id="list-perop">

{{if $app->_ref_user->isPraticien()}}
<!-- formulaire de signature des prescriptions -->
<form name="signaturePrescription" method="post" action="?">
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
			
<table class="tbl">
  <tr>
    <td>
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
				
				{{if $app->_ref_user->isPraticien()}}
				  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
        {{else}}
				  <select name="praticien_id">
				  	{{foreach from=$anesths item=_anesth}}
						  <option value="{{$_anesth->_id}}" {{if $operation->anesth_id == $_anesth->_id}}selected="selected"{{/if}}>{{$_anesth->_view}}</option>
						{{/foreach}}
					</select>	
				{{/if}}
				
        <input type="hidden" name="pratSel_id" value="" />
        <input type="hidden" name="pack_protocole_id" value="" />
        <input type="hidden" name="advanced_prot" value="0" />
				<input type="hidden" name="perop" value="1" />
        <input type="text" name="libelle_protocole" value="&mdash; Choisir un protocole" class="autocomplete" style="font-weight: bold; font-size: 1.3em; width: 300px;"/>
        <div style="display:none; width: 350px;" class="autocomplete" id="protocole_perop_auto_complete"></div>
				<input type="hidden" name="operation_id" value="{{$operation_id}}" />
        <button type="submit" class="submit">Appliquer le protocole</button>
      </form>
	  </td>
	</tr>
</table>

{{assign var=images value="CPrescription"|static:"images"}}
	<table class="tbl">
		<tr>
			<th class="title">Produits / Soins</th>
			<th class="title">
				Planifications
			  <button class="tick notext" onclick="submitAllAdmPerop();">Valider toutes les administrations prévues</button>
			</th>
	    <th class="title">Administrations</th>
		</tr>

		{{foreach from=$lines key=guid item=_lines_by_type}}
			{{assign var=_line value=$_lines_by_type.object}}
			{{assign var=chapitre value=$_line->_chapitre}}
			<tr>
			  <td class="text" style="width: 30%;">
				  <img src="{{$images.$chapitre}}"  style="float: left;"/>
				  
					{{if $_line instanceof CPrescriptionLineMix}}
					  <a href="#" onclick="viewTimingPerf('{{$_line->_id}}');">
					{{/if}} 	
					
					<strong style="padding-left: 10px;" onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}');">
	          {{if $_line instanceof CPrescriptionLineElement}}
						  {{$_line->_view}}
            {{elseif $_line instanceof CPrescriptionLineMix}}
						  {{foreach from=$_line->_ref_lines item=_mix_item}}
							  {{$_mix_item->_ucd_view}} <br />
							{{/foreach}}
						{{else}}
					    {{$_line->_ucd_view}}
				    {{/if}}
					</strong>
					
					{{if $_line instanceof CPrescriptionLineMix}}
            </a>
          {{/if}}   
          
					{{if $_line instanceof CPrescriptionLineMix}}
						<div style="text-align: center;">
							<form name="editTimingPerf-{{$_line->_id}}" method="post" action="?" style="text-align: center;">
				        <input type="hidden" name="m" value="dPprescription" />
				        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
				        <input type="hidden" name="del" value="0" />
				        <input type="hidden" name="prescription_line_mix_id" value="{{$_line->_id}}" />
				        <input type="hidden" name="date_pose" value="{{$_line->date_pose}}" />
				        <input type="hidden" name="time_pose" value="{{$_line->time_pose}}" />
				        <input type="hidden" name="date_retrait" value="{{$_line->date_retrait}}" />
				        <input type="hidden" name="time_retrait" value="{{$_line->time_retrait}}" />
							</form>
							{{if !$_line->date_pose}}<a href="#1" style="display: inline; border: 0px;" onclick="submitTimingPosePerf(getForm('editTimingPerf-{{$_line->_id}}'));">{{/if}}
                <img src="images/icons/play.png" title="Pose de la perfusion" style="{{if $_line->date_pose}}opacity: 0.5{{/if}}" />
              {{if !$_line->date_pose}}</a>{{/if}}
    
              {{if !$_line->date_retrait}}<a href="#1" style="display: inline; border: 0px;" onclick="submitTimingRetraitPerf(getForm('editTimingPerf-{{$_line->_id}}'));">{{/if}}
                <img src="images/icons/stop.png" title="Retrait de la perfusion" style="{{if $_line->date_retrait}}opacity: 0.5{{/if}}" />
              {{if !$_line->date_retrait}}</a>{{/if}}
					  </div>
				  {{/if}}
				</td>
			  <td>
			  	{{if array_key_exists("planifications", $_lines_by_type)}}
				    {{foreach from=$_lines_by_type.planifications item=_planifs}}
						{{foreach from=$_planifs item=_planif name=planifs}}
						
		          {{assign var=can_adm value=false}}
		          {{if ($_line instanceof CPrescriptionLineMix && $_line->signature_prat)}}
		            {{assign var=can_adm value=true}}
		          {{elseif ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineElement) && $_line->signee}}
		            {{assign var=can_adm value=true}}
		          {{/if}}
		           
		          {{if $_planif->_ref_object instanceof CPrescriptionLineMixItem}}
		            {{assign var=quantite value=$_planif->_ref_object->_quantite_administration}}
		          {{else}}
		            {{assign var=quantite value=$_planif->_ref_prise->_quantite_administrable}} 
		          {{/if}}
		          
		          {{if $can_adm}}
		            <script type="text/javascript">
		              var oForm = getForm("addAdministrationPerop-{{$_planif->_id}}");
		              oForm.quantite.addSpinner({min:0});
		            </script>
		            
		            <form {{if $_line->_count_adm == 0}}class="admPerop"{{/if}} name="addAdministrationPerop-{{$_planif->_id}}" action="?" method="post">
		              <input type="hidden" name="m" value="dPprescription" />
		              <input type="hidden" name="dosql" value="do_administration_aed" />
		              <input type="hidden" name="del" value="0" />
		              <input type="hidden" name="object_id" value="{{$_planif->object_id}}" />
		              <input type="hidden" name="object_class" value="{{$_planif->object_class}}" />
		              <input type="hidden" name="unite_prise" value="{{$_planif->unite_prise}}" />
		              <input type="hidden" name="prise_id" value="{{$_planif->prise_id}}" />
		              <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
		              <input type="hidden" name="dateTime" value="current" />
		              <input type="text" name="quantite" value="{{$quantite}}" size=3/>
		              <button type="button" class="tick notext" onclick="return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });">Administrer</button>
								</form>
							
							{{else}}
		            {{$quantite}}
		          {{/if}}
		       
		          {{assign var=unite value=""}}
		          {{if $_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix}}
		            {{assign var=unite value=$_planif->_ref_object->_ref_produit->libelle_unite_presentation}}
		          {{else}}
		            {{assign var=unite value=$_line->_unite_prise}}
		          {{/if}}
		          {{$unite}}
		        
						  à {{$_planif->dateTime|date_format:$conf.time}}
							<br />
						{{/foreach}}
	          {{/foreach}}
					{{/if}}
			  </td>		
			  <td>
				{{foreach from=$_lines_by_type.administrations item=_adms}}
				{{foreach from=$_adms item=_adm}}
				  <script type="text/javascript">
	          var oForm = getForm("editAdm-{{$_adm->_id}}");
	          Calendar.regField(oForm.dateTime, null, {datePicker: false, timePicker: true});
	        </script>
	      
				  {{if $_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix}}
            {{assign var=unite value=$_adm->_ref_object->_ref_produit->libelle_unite_presentation}}
          {{else}}
            {{assign var=unite value=$_line->_unite_prise}}
          {{/if}}
					
	        <form name="editAdm-{{$_adm->_id}}" method="post" action="?">
	          <input type="hidden" name="m" value="dPprescription" />
	          <input type="hidden" name="dosql" value="do_administration_aed" />
	          <input type="hidden" name="del" value="0" />
	          <input type="hidden" name="administration_id" value="{{$_adm->_id}}" />
	          <button type="button" class="trash notext" onclick="$V(this.form.del, 1); return onSubmitFormAjax(this.form, { onComplete: Prescription.updatePerop.curry('{{$sejour_id}}') });"></button>
	        
	          {{mb_field object=$_adm field="dateTime" onchange="return onSubmitFormAjax(this.form);"}} <strong>{{$_adm->quantite}} {{$unite}}</strong>
	        </form>
	        <br />
				{{/foreach}}	
	    {{foreachelse}}
			   <div class="empty">Aucune administration</div>
			{{/foreach}}	
			</td> 
		</tr>
		{{foreachelse}}
		<tr>
			<td colspan="3" class="empty">Aucune prescription perop</td>
		</tr>
		{{/foreach}}
	</table>
</div>