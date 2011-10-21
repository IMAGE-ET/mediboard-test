<script type="text/javascript">

addAdministrationsPerf = function(){
  var submitForms = $('administrations_perf').select("form");
  
	for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];    
    if (!checkForm(f)) return false;
    if(i == (submitForms.length - 1)){
      submitFormAjax(f, 'systemMsg', { onComplete: function(){ 
        refreshAdministrations.delay(1);
      } } );
    } else {
      submitFormAjax(f, 'systemMsg');
    }
  }
  return true;
}

removeAdministrationPerf = function(oForm){
  if(confirm('Voulez vous supprimer également les administrations des autres produits ?')){
	  removeAllAdministrationsPerf();
	} else {
	  onSubmitFormAjax(oForm, { onComplete: refreshAdministrations } );
	}
}

removeAllAdministrationsPerf = function(){
  $$('form.removeAdmPerf').each(function(oForm){
	  if (oForm.hasClassName('lastFormPerf')){
		  onSubmitFormAjax(oForm, { onComplete: refreshAdministrations });
  	} else {
		  onSubmitFormAjax(oForm);
		}
	});
}

refreshAdministrations = function(){
  var oFormClick = window.opener.document.click;
  window.opener.PlanSoins.loadTraitement('{{$sejour_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}','{{$prescription_line_mix->_id}}','{{$prescription_line_mix->_class}}','');
  window.close();
}

</script>

{{if $mode_dossier == "planification" && !$conf.dPprescription.CPrescription.manual_planif}}
  <div class="small-info">
  	Il n'est pas possible de créer des planifications
  </div>
{{else}}
	
	
	<table class="form" id="administrations_perf">
	  <tr>
	    <th class="title">
	    	{{$mode_dossier|capitalize}} 
	    </th>
		</tr>	
		<tr>
	    <th class="category">
	      Perfusion {{$prescription_line_mix->_view}}</th>
	    </tr>
	  {{foreach from=$prescription_line_mix->_ref_lines item=_perf_line}}
	  
	  <tr>
		  <th class="title text" colspan="2">{{$_perf_line->_view}} à {{$dateTime|date_format:$conf.datetime}}</th>
		</tr>
	  {{assign var=perf_line_id value=$_perf_line->_id}}
	  {{if $administrations.$perf_line_id|@count}}
	  <tr>
		  <th class="title text" colspan="2">Deja effectuées</th>
	  </tr>
	  {{/if}}
	  {{foreach from=$administrations.$perf_line_id item=_administration name=list_adm}}
	  <tr>
	    <td colspan="2">
	    	{{if $_administration->administrateur_id == $app->user_id || $_administration->planification || $can->admin}}
	        <form name="removeAdministration-{{$_administration->_id}}" action="?" method="post" class="removeAdmPerf {{if $smarty.foreach.list_adm.last}}lastFormPerf{{/if}}">
				    <input type="hidden" name="dosql" value="do_administration_aed" />
				    <input type="hidden" name="m" value="dPprescription" />
				    <input type="hidden" name="del" value="1" />
				    <input type="hidden" name="administration_id" value="{{$_administration->_id}}" />
						<button class="trash notext" type="button" onclick="removeAdministrationPerf(this.form);"></button>
				  </form>
				{{/if}} {{$_administration->quantite}} ml: {{$_administration->_view}}
	    </td>
	  </tr>
	  {{/foreach}}
	  <tr>
	    <th class="category" colspan="2">Nouvelle {{$mode_dossier}}</th>
	  </tr>
		<tr>
	    <td>
	  	  {{if $_perf_line->_ref_produit_prescription->_id}}
	        {{assign var=unite_prise value=$_perf_line->_ref_produit_prescription->unite_prise}}
	      {{else}}
	        {{assign var=unite_prise value=$_perf_line->_unite_administration}}
	      {{/if}}     
				
				<form name="addAdministration-{{$_perf_line->_id}}" method="post" action="?">
				  <input type="hidden" name="dosql" value="do_administration_aed" />
				  <input type="hidden" name="m" value="dPprescription" />
				  <input type="hidden" name="del" value="0" />
				  <input type="hidden" name="administration_id" value="" />
				  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
				  <input type="hidden" name="object_id" value="{{$_perf_line->_id}}" />
				  <input type="hidden" name="object_class" value="{{$_perf_line->_class}}" />
				  <input type="hidden" name="unite_prise" value="{{$unite_prise}}" />
				  <input type="hidden" name="dateTime" value="{{$dateTime}}" />
				  <input type="hidden" name="prise_id" value="" />
				  <input type="hidden" name="_quantite_prevue" value="{{$_perf_line->_quantite_administration}}" />
					{{if $mode_dossier == "planification"}}
					<input type="hidden" name="planification" value="1" />
					{{/if}}
					{{mb_label object=$administration field=quantite}}
					{{mb_field object=$administration field=quantite min=0 increment=1 form=addAdministration-$perf_line_id value=$_perf_line->_quantite_administration}} {{$unite_prise}}
				</form>
	    </td>
	  </tr>
	  {{/foreach}}
	  <tr>
	    <td colspan="2" class="button">
	      <button class="submit oneclick" onclick="addAdministrationsPerf();" type="button">{{tr}}Validate{{/tr}}</button>
	    </td>
	  </tr>
	</table>
{{/if}}