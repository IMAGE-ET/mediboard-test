{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
{{if !@$modeles_prat_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_anesth->_id}}
{{/if}}

<script type="text/javascript">

reloadDocumentsAnesth = function () {
  if(oForm = getForm("anesthInterv")) {
    var sAnesth_id = $V(oForm.anesth_id);
  } else {
    var sAnesth_id = $V(getForm("visiteAnesth").prat_anesth_id);
  }
  $$('.documents-CConsultAnesth-{{$consult_anesth->_id}}').each(function(doc){
    Document.refresh(doc, {praticien_id: sAnesth_id });
  });    
}

refreshAnesthPerops = function(operation_id){
  var url = new Url("dPsalleOp", "httpreq_vw_anesth_perop");
	url.addParam("operation_id", operation_id);
	url.requestUpdate("list_perops_"+operation_id);
}

printIntervAnesth = function(){
  var url = new Url("dPsalleOp", "print_intervention_anesth");
	url.addParam("operation_id", "{{$selOp->_id}}");
	url.popup(800, 600, "Intervention anesthésiste");
}

refreshVisite = function(operation_id) {
  var url = new Url("dPsalleOp", "ajax_refresh_visite_pre_anesth");
  url.addParam("operation_id", operation_id);
  url.addParam("callback", "refreshVisite");
  url.requestUpdate("visite_pre_anesth");
}

{{if $dialog}}
reloadAnesth = function() {
  window.opener.location.reload(true);
  window.location.reload(true);
}
{{/if}}

Main.add(function(){
  
  if ($('anesth_tab_group')){
    Control.Tabs.create('anesth_tab_group', true);
  }
	
  // Refresh tab perop
  if($("tab_perop").visible()){
    refreshAnesthPerops('{{$selOp->_id}}');
  }
    
  {{if "dPprescription"|module_active}}
    if($('perop').visible()){
      Prescription.updatePerop('{{$selOp->sejour_id}}');
    }
  {{/if}}

});
</script>

{{if $dialog}}
  {{assign var=onSubmit value="return onSubmitFormAjax(this, {onComplete: reloadAnesth})"}}
{{else}}
  {{assign var=onSubmit value="return checkForm(this)"}}
{{/if}}

{{if $consult_anesth->_id}}
  <table class="form">
    <tr>
      <td class="halfPane">
        <fieldset>
          {{mb_script module="dPcabinet" script="file"}}
          <legend>{{tr}}CFile{{/tr}} - {{tr}}CConsultAnesth{{/tr}}</legend>            
          <div id="files-anesth">
            <script type="text/javascript">
              File.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class}}', 'files-anesth');
            </script>
          </div>
        </fieldset>
      </td>
      <td class="halfPane">
        {{mb_script module="dPcompteRendu" script="document"}}
        {{mb_script module="dPcompteRendu" script="modele_selector"}}
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CConsultAnesth{{/tr}}</legend>            
          <div id="documents-anesth">
            <script type="text/javascript">
              Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class}}','{{$modeles_prat_id}}','documents-anesth');
            </script>
          </div>
        </fieldset>
      </td>
    </tr>
  </table>
{{/if}}
	
	
<ul id="anesth_tab_group" class="control_tabs">
	<li><a href="#tab_preanesth">Pré-anesthésie</a></li>
	<li onmousedown="refreshAnesthPerops('{{$selOp->_id}}');"><a href="#tab_perop">Evenements per-opératoire</a></li>
	<li onmousedown="if(window.Prescription){ Prescription.updatePerop('{{$selOp->sejour_id}}'); }"><a href="#perop">Administrations per-opératoires</a></li>
  <!--<li style="float: right"><button type="button" class="print" onclick="printIntervAnesth();">Fiche d'intervention anesthésie</button></li>-->
</ul>
<hr class="control_tabs" />

<div id="tab_preanesth" style="display: none;">
  {{if $consult_anesth->_id}}
		<table class="form">
			{{if $selOp->_ref_consult_anesth->_ref_techniques|@count}}
		  <tr>
		    <th colspan="2" class="title">Techniques complémentaires</th>
		  </tr>
		  <tr>
		    <td colspan="2" class="text">
					<ul>
		      {{foreach from=$selOp->_ref_consult_anesth->_ref_techniques item=_technique}}
					  <li>{{$_technique->technique}}</li>
					{{/foreach}}
					</ul>
		    </td>
		  </tr>
			{{/if}}
		</table>
		<table class="tbl">
		  <!-- Affichage d'information complementaire pour l'anestesie -->
		  <tr>
		    <th class="title">Consultation de pré-anesthésie</th>
		  </tr>
		  <tr>
		    <td class="text">
		      <button type="button" class="print" onclick="printFicheAnesth('{{$consult_anesth->_ref_consultation->_id}}')" style="float: right">
		        Consulter la fiche
		      </button>
		      {{if $dialog}}
		        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult_anesth->_ref_consultation->_ref_chir}}
		        -
		        <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
		        le {{mb_value object=$consult_anesth->_ref_consultation field="_date"}}
		      </span>
		      {{else}}
		      <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$consult_anesth->_ref_consultation->_id}}">
		        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult_anesth->_ref_consultation->_ref_chir}}
		        -
		        <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')">
		        le {{mb_value object=$consult_anesth->_ref_consultation field="_date"}}
		        </span>
		      </a>
		      {{/if}}
		    </td>
		  </tr>
		</table>
	{{else}}
	  {{mb_include module=cabinet template=inc_choose_dossier_anesth}}
	{{/if}}
  {{assign var=callback value=refreshVisite}}
  <div id="visite_pre_anesth">
    {{mb_include module=salleOp template=inc_visite_pre_anesth}}
  </div>
</div>

<div id="tab_perop" style="display: none;">
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Per-operatoire</th>
	  </tr>
	  <tr>
	    <td style="width: 50%">
	      <!-- Affichage et gestion des gestes perop -->
	      <form name="addAnesthPerop" action="?" method="post" 
	            onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshAnesthPerops('{{$selOp->_id}}'); $V(this.libelle, ''); $V(this.incident, ''); $V(this.__incident, '')}.bind(this)  } )">
	
	        <input type="hidden" name="m" value="dPsalleOp" />
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="dosql" value="do_anesth_perop_aed" />
	        <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
	        <input type="hidden" name="datetime" value="now" />
          {{mb_key object=$anesth_perop}}
	        
	        <table class="main layout">
	          <tr>
              <td>
                {{mb_label object=$anesth_perop field="libelle"}}
              </td>
            </tr>
            <tr>  
              <td>
                {{if $selOp->_ref_anesth->_id}}
                  {{assign var=contextUserId value=$selOp->_ref_anesth->_id}}
                  {{assign var=contextUserView value=$selOp->_ref_anesth}}
                {{else}}
                  {{assign var=contextUserId value=$app->_ref_user->_id}}
                  {{assign var=contextUserView value=$app->_ref_user}}
                {{/if}}
                {{mb_field object=$anesth_perop field="libelle" form="addAnesthPerop"
                  aidesaisie="contextUserId: '`$contextUserId`', contextUserView: '`$contextUserView`'"}}
              </td>
	          </tr>
	          <tr>
              <td>
                {{mb_field object=$anesth_perop field="incident" typeEnum="checkbox"}}
                {{mb_label object=$anesth_perop field="incident"}}
              </td>
	          </tr>
	          <tr>
	            <td colspan="2" class="button">
	              <button type="submit" class="submit">Ajouter</button>
	            </td>
	          </tr>
	        </table>
	      </form>
	    </td>
	    <td id="list_perops_{{$selOp->_id}}"></td>
	  </tr>
	</table>
</div>

<div id="perop"></div>