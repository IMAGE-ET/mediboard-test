<script type="text/javascript">
var oFormClick = window.opener.document.forms.click;
var anyFormSubmitted = false;

function submitAllAdministrations() {
  var submitForms = $('administrations').select('form');
  var transForm = getForm('editTrans');

	if (transForm.text.value) {
	  submitFormAjax(transForm, 'systemMsg');
	}
	
	for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];  
    if (f != transForm) {
	      if (($V(f.quantite_prevue)-0 != $V(f.quantite)-0) && !transForm.text.value) {
	        alert('Veuillez ajouter une transmission');
	        (transForm.text).focus();
	        return false;
	      }
      if (!checkForm(f)) return false;
      if(i == (submitForms.length - 2)) {
        submitFormAjax(f, 'systemMsg', { onComplete: function(){ 
          closeApplyAdministrations();
        } } );
      } else {
        submitFormAjax(f, 'systemMsg'); 
      }
      anyFormSubmitted = true;
      f.up().update('Soin effectué');
    }
  }
  return true;
}

function submitAllPlanifications(){
  var submitForms = $('planifications').select('form');

  for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];    
    if (!checkForm(f)) return false;
    if(i == (submitForms.length - 1)){
      submitFormAjax(f, 'systemMsg', { onComplete: function(){ 
        closeApplyAdministrations();
      } } );
    } else {
      submitFormAjax(f, 'systemMsg');
    }
    anyFormSubmitted = true;
    f.up().update('Soin effectué');
  }
  return true;
}

function closeApplyAdministrations(dontClose) {
  {{if $administrations|@count && $sejour->_id && $date_sel}}
    if (anyFormSubmitted && window.opener) {
      // refresh des chapitres concernés
      {{foreach from=$tabs_refresh key=chapitre item=_tab_refresh}}
        window.opener.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}', null, null, null, '{{$chapitre}}');
      {{/foreach}}
      window.opener.loadSuivi('{{$sejour->_id}}');
    }
  {{/if}}
  if (!dontClose) {
    window.close();
  }
}

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('administrations_multiple_tab_group', false);
} );

</script>

{{if $sejour->_id}}
		<h2>
		  Soins de {{$sejour->_ref_patient->_view}} 
		  ({{if $sejour->_ref_patient->_ref_curr_affectation->_id}}
		    {{$sejour->_ref_patient->_ref_curr_affectation->_view}}
		  {{else}}
		    Non placé actuellement
		  {{/if}})
		</h2>
{{/if}}

{{if $mode_dossier == "administration"}}
	{{if $sejour->_id}}
		<button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
		<button type="button" class="tick" onclick="submitAllAdministrations()">Tout valider</button>
		
		<table class="form" id="administrations">
		{{foreach from=$administrations item=adm key=line_id name=by_adm}}
		  {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
		    {{foreach from=$by_unite_prise item=by_date key=date}}
		      {{foreach from=$by_date item=by_hour key=hour}}
	          {{assign var=_unite value=$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}
	          {{assign var=_prise value=$by_hour.prise_id|smarty:nodefaults|JSAttribute}}
	          {{assign var=key value="$line_id-$_unite-$_prise-$date-$hour"|smarty:nodefaults|JSAttribute}}
	          {{if $smarty.foreach.adm_by_unite_prise.first}}
		        <tr>
		          <th class="title" colspan="2">{{$by_hour.line->_view}}</th>
		        </tr>
		        {{/if}}
		        <tr>
		          <td id="adm_{{$key}}">
		            {{if $by_hour.line->_class_name == "CPrescriptionLineMedicament"}}
		              {{assign var=unite_prise value=$by_hour.line->_ref_produit->libelle_unite_presentation}}
		            {{else}}
		              {{assign var=unite_prise value=$by_hour.line->_unite_prise}}
		            {{/if}}
		            {{if !$by_hour.notToday}}
		              <form name="addAdministration_{{$key}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
		                <input type="hidden" name="dosql" value="do_administration_aed" />
		                <input type="hidden" name="m" value="dPprescription" />
		                <input type="hidden" name="del" value="0" />
		                <input type="hidden" name="administration_id" value="" />
		                <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
		                <input type="hidden" name="object_id" value="{{$by_hour.line->_id}}" />
		                <input type="hidden" name="object_class" value="{{$by_hour.line->_class_name}}" />
		                <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise}}" />
		                <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
		                <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
		                <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
		                
		                <b>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</b> : 
		                {{mb_label object=$by_hour.prise field=quantite}}
		                {{mb_field object=$by_hour.prise field=quantite min=1 increment=1 form="addAdministration_$key"}}
		                {{$unite_prise}}
		              </form>
		            {{else}}
		              <div class="small-info">
		                Attention, cette prise de {{mb_value object=$by_hour.prise field=quantite}} {{$unite_prise}} est pour le {{$date|date_format:"%d/%m/%Y"}} à {{$hour}}h, 
		                or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
		              </div>
		            {{/if}}
		          </td>
		        </tr>
		      {{/foreach}}
		    {{/foreach}}
		  {{/foreach}}
		{{foreachelse}}
		  <tr><td>Veuillez choisir au moins un soin à effectuer</td></tr>
		{{/foreach}}
		  <tr>
		    <th class="title" colspan="2">{{tr}}CTransmissionMedicale{{/tr}}</th>
		  </tr>
		  <tr>
		    <td>
		      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		        <input type="hidden" name="dosql" value="do_transmission_aed" />
		        <input type="hidden" name="del" value="0" />
		        <input type="hidden" name="m" value="dPhospi" />
		        <input type="hidden" name="object_class" value="" />
		        <input type="hidden" name="object_id" value="" />
		        <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
		        <input type="hidden" name="user_id" value="{{$app->user_id}}" />
		        <input type="hidden" name="date" value="now" />
		        {{mb_label object=$transmission field="degre"}}
		        {{mb_field object=$transmission field="degre"}}<br />
		        {{mb_field object=$transmission field="text"}}
		      </form>
		    </td>
		  </tr>
		</table>
	{{else}}
	  <div class="big-info">
	    Veuillez sélectionner des prises prevues pour ajouter des administrations
	  </div>
	{{/if}}
{{/if}}

{{if $mode_dossier == "planification"}}
	{{if $sejour->_id}}	
		<button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
		<button type="button" class="tick" onclick="submitAllPlanifications()">Tout valider</button>
		
		<table class="form" id="planifications">
		{{foreach from=$administrations item=adm key=line_id name=by_adm}}
		  {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
		    {{foreach from=$by_unite_prise item=by_date key=date}}
		      {{foreach from=$by_date item=by_hour key=hour}}
	          {{assign var=_unite value=$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}
	          {{assign var=_prise value=$by_hour.prise_id|smarty:nodefaults|JSAttribute}}
	          {{assign var=key value="$line_id-$_unite-$_prise-$date-$hour"|smarty:nodefaults|JSAttribute}}
	          {{if $smarty.foreach.adm_by_unite_prise.first}}
		        <tr>
		          <th class="title" colspan="2">{{$by_hour.line->_view}}</th>
		        </tr>
		        {{/if}}
		        {{assign var=planification value=$by_hour.prise}}
		        {{if $planification->quantite}}
		        <tr>
		          <td>
		            <div class="small-info">
		              Il est impossible de planifier sur cette case car elle possède déjà une prise prévue.
		            </div>
		          </td>
		        </tr>
		        {{else}}
		        <tr>
		          <td id="adm_{{$key}}">
		            {{if $by_hour.line->_class_name == "CPrescriptionLineMedicament"}}
		              {{assign var=unite_prise value=$by_hour.line->_ref_produit->libelle_unite_presentation}}
		            {{else}}
		              {{assign var=unite_prise value=$by_hour.line->_unite_prise}}
		            {{/if}}
	              <form name="addPlanification_{{$key}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
	                <input type="hidden" name="dosql" value="do_administration_aed" />
	                <input type="hidden" name="m" value="dPprescription" />
	                <input type="hidden" name="del" value="0" />
	                <input type="hidden" name="administration_id" value="" />
	                <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
	                <input type="hidden" name="object_id" value="{{$by_hour.line->_id}}" />
	                <input type="hidden" name="object_class" value="{{$by_hour.line->_class_name}}" />
	                <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise}}" />
	                <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
	                <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
	                <input type="hidden" name="planification" value="1" />
	                <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
	                <b>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</b> : 
	                {{mb_label object=$by_hour.prise field=quantite}}
	                {{mb_field object=$by_hour.prise field=quantite min=1 increment=1 form="addPlanification_$key"}}
	                {{$unite_prise}}
	              </form>
		          </td>
		        </tr>
		        {{/if}}
		      {{/foreach}}
		    {{/foreach}}
		  {{/foreach}}
		{{foreachelse}}
		  <tr><td>Veuillez choisir au moins un soin à planifier</td></tr>
		{{/foreach}}
		</table>
	{{else}}
	  <div class="big-info">
	    Veuillez sélectionner des prises prevues pour ajouter des administrations
	  </div>
	{{/if}}
{{/if}}