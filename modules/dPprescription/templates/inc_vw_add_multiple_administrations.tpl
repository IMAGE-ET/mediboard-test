{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var oFormClick = window.opener.document.forms.click;
var anyFormSubmitted = false;
var submittedAdministrationsGuids = [];

function submitAllAdministrations() {
  var submitForms = $('administrations').select('form');
  var transForm = getForm('editTrans');

  if (transForm._text_data.value || transForm._text_action.value || transForm._text_result.value) {
    onSubmitFormAjax(transForm, { onComplete: function() {
      // Pas de formulaire d'administration
      if (submitForms.length == 1) {
        if (window.opener.loadSuivi){
          window.opener.loadSuivi('{{$sejour->_id}}');
        }
        if (window.opener.updatePlanSoinsPatients) {
          window.opener.updatePlanSoinsPatients();
        }
        window.close();
        return true;
      }
    }});
  }
  
  for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];
    
    if (f != transForm) {
      if (!checkForm(f)) {
        return false;
      }
      
      if(i == (submitForms.length - 2)) {
        onSubmitFormAjax(f, { onComplete: function(){ 
          closeApplyAdministrations.delay(1);
        } } );
      }
      else {
        onSubmitFormAjax(f);
      }
      
      anyFormSubmitted = true;
      f.up().update('Soin effectué');
    }
  }
  
  return true;
}

function saveAdministrationGuid(id, obj) {
  submittedAdministrationsGuids.push("CAdministration-"+id);
}

function submitAllPlanifications(){
  var submitForms = $('planifications').select('form');

  for (var i = 0; i < submitForms.length; i++) {
    var f = submitForms[i];    
    if (!checkForm(f)) return false;
    if(i == (submitForms.length - 1)){
      submitFormAjax(f, 'systemMsg', { onComplete: function(){ 
        closeApplyAdministrations.delay(1);
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
  {{if ($administrations|@count || $prises_lines_mix|@count)&& $sejour->_id && $date_sel}}
	  if (anyFormSubmitted && window.opener) {
      // refresh des chapitres concernés
      {{foreach from=$tabs_refresh key=chapitre item=_tab_refresh}}
        window.opener.PlanSoins.loadTraitement('{{$sejour->_id}}','{{$date_sel}}', oFormClick.nb_decalage.value,'{{$mode_dossier}}', null, null, null, '{{$chapitre}}');
      {{/foreach}}
      if (window.opener.loadSuivi){
        window.opener.loadSuivi('{{$sejour->_id}}');
      }
      if (window.opener.updatePlanSoinsPatients) {
        window.opener.updatePlanSoinsPatients();
      }
    }
  {{/if}}
  
  {{if "forms"|module_active}}
    var options = {};
    if (!dontClose) {
      options.onTriggered = function(){ 
        window.close();
      }
    }
    
    ExObject.trigger(submittedAdministrationsGuids, "validation", options);
  {{else}}
    if (!dontClose) {
      window.close();
    }
  {{/if}}
}

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('administrations_multiple_tab_group', false);
  
  {{if $refresh_popup == 1}}
	  var oFormAdm         = getForm("all_adm");
		var oFormAdmMultiple = window.opener.getForm("adm_multiple");
    
		$V(oFormAdm.adm    , $V(oFormAdmMultiple._administrations));
		$V(oFormAdm.adm_mix, $V(oFormAdmMultiple._administrations_mix));
    oFormAdm.submit();
	{{/if}}
  
  if($("multiple_adm_button")){
   $("multiple_adm_button").focus();
  }
} );

</script>

<form name="all_adm" method='post' action='?m={{$m}}&a={{$a}}&dialog={{$dialog}}&refresh_popup=0&mode_dossier={{$mode_dossier}}'>
  <input type="hidden" name="adm" value="" />
	<input type="hidden" name="adm_mix" value="" />
</form>

{{if $sejour->_id}}
  {{if $nb_patients == 1}}
    <h2>
      Soins de {{$sejour->_ref_patient->_view}} 
      ({{if $sejour->_ref_patient->_ref_curr_affectation->_id}}
        {{$sejour->_ref_patient->_ref_curr_affectation->_view}}
      {{else}}
        Non placé actuellement
      {{/if}})
    </h2>
  {{else}}
    <h2>
      Soins multiples
    </h2>
  {{/if}}
{{/if}}

{{if $mode_dossier == "administration"}}
  {{if $sejour->_id}}    
    <table class="form" id="administrations">
    {{foreach from=$administrations item=adm key=line_id name=by_adm}}
      {{foreach from=$adm item=by_unite_prise key=unite_prise name=adm_by_unite_prise}}
        {{foreach from=$by_unite_prise item=by_date key=date}}
          {{foreach from=$by_date item=by_hour key=hour}}
            {{assign var=_unite value=$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}
            {{assign var=_prise value=$by_hour.prise_id|smarty:nodefaults|JSAttribute}}
            {{assign var=key value="$line_id-$_unite-$_prise-$date-$hour"|md5}}
            {{if $smarty.foreach.adm_by_unite_prise.first}}
            <tr>
              <th class="category" colspan="2">{{$by_hour.line->_view}}</th>
            </tr>
            {{/if}}
            <tr>
              <td id="adm_{{$key}}" style="vertical-align: middle;">
                {{if $by_hour.line->_class == "CPrescriptionLineMedicament"}}
                  {{if $by_hour.line->_ref_produit_prescription->_id}}
                    {{assign var=unite_prise value=$by_hour.line->_ref_produit_prescription->unite_prise}}
                  {{else}}
                    {{assign var=unite_prise value=$by_hour.line->_ref_produit->libelle_unite_presentation}}
                  {{/if}}
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
                    <input type="hidden" name="object_class" value="{{$by_hour.line->_class}}" />
                    <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise|smarty:nodefaults|JSAttribute}}" />
                    <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
                    <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
                    <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
                    <input type="hidden" name="_quantite_prevue" value="{{$by_hour.prise->quantite}}" />
                    <input type="hidden" name="callback" value="saveAdministrationGuid" />
                    
                    <strong>{{$date|date_format:"%d/%m/%Y"}}, {{$hour|date_format:"%Hh%M"}}</strong> : 
                    {{mb_label object=$by_hour.prise field=quantite}}
                    {{mb_field object=$by_hour.prise field=quantite min=0 increment=1 form="addAdministration_$key"}}
                    {{$unite_prise}}
                  </form>
                {{else}}
                  <div class="small-info">
                    Attention, cette prise de {{mb_value object=$by_hour.prise field=quantite}} {{$unite_prise}} est pour le {{$date|date_format:"%d/%m/%Y"}} à {{$hour|date_format:"%Hh%M"}}, 
                    or nous sommes le {{$smarty.now|date_format:"%d/%m/%Y"}}.
                  </div>
                {{/if}}
              </td>
              <td>
                {{mb_include module=pharmacie template=inc_chose_stock_location_administration adm=$new_adm line=$by_hour.line}}
              </td>
            </tr>
          {{/foreach}}
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
		
		{{foreach from=$prises_lines_mix item=line_mix}}
		  {{if $line_mix->_prises_prevues|@count}}
			<tr>
				<th class="category">
					
					{{foreach from=$line_mix->_ref_lines item=_line name=items}}
					 {{$_line->_ucd_view}} {{if !$smarty.foreach.items.last}},{{/if}}
					{{/foreach}}
					<br />
					<small>{{$line_mix->_view}}</small>
	    	</th>
			</tr>
			{{foreach from=$line_mix->_prises_prevues key=_date item=prises_by_date}}
			  {{foreach from=$prises_by_date key=_hour item=prises_by_hour}}
				
				  {{if array_key_exists("real_hour", $prises_by_hour)}}
				
			    {{foreach from=$prises_by_hour.real_hour item=prises_by_real_hour}}
	      	  {{foreach from=$line_mix->_ref_lines item=_line_mix_item}}
							<tr>
			          <td>
			          	 {{unique_id var="unique_id"}}
									 <form name="addAdministration_{{$unique_id}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
                    <input type="hidden" name="dosql" value="do_administration_aed" />
                    <input type="hidden" name="m" value="dPprescription" />
                    <input type="hidden" name="del" value="0" />
                    <input type="hidden" name="administration_id" value="" />
                    <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
                    <input type="hidden" name="object_id" value="{{$_line_mix_item->_id}}" />
                    <input type="hidden" name="object_class" value="{{$_line_mix_item->_class}}" />
                    <input type="hidden" name="unite_prise" value="{{$_line_mix_item->_unite_administration|smarty:nodefaults|JSAttribute}}" />
                    <input type="hidden" name="dateTime" value="{{$_date}} {{$prises_by_real_hour}}" />
                    <input type="hidden" name="prise_id" value="" />
                    <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$_line_mix_item->_quantite_administration}}" />
                    <input type="hidden" name="_quantite_prevue" value="{{$_line_mix_item->_quantite_administration}}" />
                    <input type="hidden" name="callback" value="saveAdministrationGuid" />
                    
                    <strong>{{$_date|date_format:$conf.date}}, {{$prises_by_real_hour|date_format:$conf.time}}</strong> : 
                    {{mb_label class="CAdministration" field="quantite"}}
								
										<input type="text" name="quantite" value="{{$_line_mix_item->_quantite_administration}}" size="5" />
                    {{$_line_mix_item->_unite_administration}}
                    
										<small>({{$_line_mix_item->_ucd_view}})</small>
										
                    <script type="text/javascript">
                      var oForm = getForm("addAdministration_{{$unique_id}}");
                      oForm.quantite.addSpinner({min:0});
                    </script>
                  </form>
			          </td>
			        </tr> 
		        {{/foreach}} 
			    {{/foreach}}
					
					{{elseif array_key_exists("manual", $prises_by_hour)}}
					  {{foreach from=$prises_by_hour.manual key=_mix_item_id item=_prise}}
						  {{assign var=_line_mix_item value=$lines_mix_item.$_mix_item_id}}
							<tr>
	              <td>
                 {{unique_id var="unique_id"}}
                 <form name="addAdministration_{{$unique_id}}" method="post" action="?" onsubmit="return checkForm(this)" style="float: left;">
                  <input type="hidden" name="dosql" value="do_administration_aed" />
                  <input type="hidden" name="m" value="dPprescription" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="administration_id" value="" />
                  <input type="hidden" name="administrateur_id" value="{{$app->user_id}}" />
                  <input type="hidden" name="object_id" value="{{$_line_mix_item->_id}}" />
                  <input type="hidden" name="object_class" value="CPrescriptionLineMixItem" />
                  <input type="hidden" name="unite_prise" value="{{$_line_mix_item->_unite_administration|smarty:nodefaults|JSAttribute}}" />
                  <input type="hidden" name="dateTime" value="{{$_date}} {{$_hour}}:00:00" />
                  <input type="hidden" name="prise_id" value="" />
                  <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$_line_mix_item->_quantite_administration}}" />
                  <input type="hidden" name="_quantite_prevue" value="{{$_line_mix_item->_quantite_administration}}" />
                  <input type="hidden" name="callback" value="saveAdministrationGuid" />
                  
                  <strong>{{$_date|date_format:$conf.date}}, {{$_hour}}h00</strong> : 
                  {{mb_label class="CAdministration" field="quantite"}}
              
                  <input type="text" name="quantite" value="{{$_prise}}" size="5" />
                  {{$_line_mix_item->_unite_administration}}
                  
									<small>({{$_line_mix_item->_ucd_view}})</small>
									
                  <script type="text/javascript">
                    var oForm = getForm("addAdministration_{{$unique_id}}");
                    oForm.quantite.addSpinner({min:0});
                  </script>
                  </form>
                </td>
              </tr> 
						{{/foreach}}
					{{/if}}
			  {{/foreach}}
			{{/foreach}}
			{{/if}}
		{{/foreach}}
      <tr>
        <th class="title" colspan="2">{{tr}}CTransmissionMedicale{{/tr}}</th>
      </tr>
      <tr>
        <td colspan="2">
          {{assign var=hide_button_add value=1}}
          {{mb_include module=dPhospi template=inc_transmission refreshTrans=0}}
        </td>
      </tr>
      <tr>
      	<td class="button" colspan="2">
      	  <button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
          <button type="button" class="tick oneclick" onclick="submitAllAdministrations()" id="multiple_adm_button">Tout valider</button>
      	</td>
      </tr>
    </table>
		
  {{else}}
    <div class="small-info">
      Veuillez sélectionner des prises prevues pour ajouter des administrations
    </div>
  {{/if}}
{{/if}}

{{if $mode_dossier == "planification"}}
  {{if $sejour->_id}}  
    <button type="button" class="cancel" onclick="closeApplyAdministrations()">{{tr}}Cancel{{/tr}}</button>
    <button type="button" class="tick oneclick" onclick="submitAllPlanifications()">Tout valider</button>
    
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
                {{if $by_hour.line->_class == "CPrescriptionLineMedicament"}}
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
                  <input type="hidden" name="object_class" value="{{$by_hour.line->_class}}" />
                  <input type="hidden" name="unite_prise" value="{{$by_hour.unite_prise}}" />
                  <input type="hidden" name="dateTime" value="{{$by_hour.dateTime}}" />
                  <input type="hidden" name="prise_id" value="{{$by_hour.prise_id}}" />
                  <input type="hidden" name="planification" value="1" />
                  <input type="hidden" name="quantite_prevue" disabled="disabled" value="{{$by_hour.prise->quantite}}" />
                  <strong>{{$date|date_format:"%d/%m/%Y"}}, {{$hour}}h</strong> : 
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
    <div class="small-info">
      Veuillez sélectionner des prises prevues pour ajouter des administrations
    </div>
  {{/if}}
{{/if}}