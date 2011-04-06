{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
{{if !@$modeles_prat_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_anesth->_id}}
{{/if}}

<script type="text/javascript">

refreshAidesPreAnesth = function(user_id, user_view) {
  aideRquesAnesth.options.contextUserId = user_id;
  aideRquesAnesth.options.contextUserView = user_view;
  aideRquesAnesth.init();
}

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

{{if $dialog}}
reloadAnesth = function() {
  window.opener.location.reload(true);
  window.location.reload(true);
}
{{/if}}

Main.add(function(){
  var oFormVisiteAnesth = getForm("visiteAnesth");
  aideRquesAnesth = new AideSaisie.AutoComplete(oFormVisiteAnesth.rques_visite_anesth, {
            objectClass: "COperation",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            {{if $selOp->prat_visite_anesth_id}}
            contextUserId: {{$selOp->prat_visite_anesth_id}},
            {{/if}}
            validateOnBlur:0
          });
  var oFormAnestPerop = getForm("addAnesthPerop");
  aidePeropAnesth = new AideSaisie.AutoComplete(oFormAnestPerop.libelle, {
            objectClass: "CAnesthPerop",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            {{if $selOp->_ref_anesth->_id}}
            contextUserId: {{$selOp->_ref_anesth->_id}},
            {{/if}}
            validateOnBlur:1
          });
	
  {{if !$selOp->date_visite_anesth}}
	  var dates = {};
	  /*dates.limit = {
	    start: '{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}',
	    stop: '{{$smarty.now|@date_format:"%Y-%m-%d %H:%M:%S"}}'
	  };*/
	  Calendar.regField(oFormVisiteAnesth.date_visite_anesth, dates);
	  
	  // Initialisation du champ date
	  $("visiteAnesth_date_visite_anesth_da").value = "Heure actuelle";
	  $V(oFormVisiteAnesth.date_visite_anesth, "current");
  {{/if}}
	
	if ($('anesth_tab_group')){
    Control.Tabs.create('anesth_tab_group', true);
  }
	
  // Refresh tab perop
  if($("tab_perop").visible()){
    refreshAnesthPerops('{{$selOp->_id}}');
    Prescription.updatePerop('{{$selOp->sejour_id}}');
  }
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
              File.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class_name}}', 'files-anesth');
            </script>
          </div>
        </fieldset>
      </td>
      <td class="halfPane">
        {{mb_script module="dPcompteRendu" script="document"}}
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CConsultAnesth{{/tr}}</legend>            
          <div id="documents-anesth">
            <script type="text/javascript">
              Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class_name}}','{{$modeles_prat_id}}','documents-anesth');
            </script>
          </div>
        </fieldset>
      </td>
    </tr>
  </table>
{{/if}}
	
	
<ul id="anesth_tab_group" class="control_tabs">
	<li><a href="#tab_preanesth">Pré-anesthésie</a></li>
	<li onmousedown="refreshAnesthPerops('{{$selOp->_id}}'); Prescription.updatePerop('{{$selOp->sejour_id}}');"><a href="#tab_perop">Per-opératoire</a></li>
	<li style="float: right"><button type="button" class="print" onclick="printIntervAnesth();">Fiche d'intervention anesthésie</button></li>
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
	  {{mb_include module=dPcabinet template=inc_choose_dossier_anesth}}
	{{/if}}
	
	<form name="visiteAnesth" action="?m={{$m}}" method="post" onsubmit="{{$onSubmit}}">
		<input type="hidden" name="dosql" value="do_planning_aed" />
		<input type="hidden" name="m" value="dPplanningOp" />
		<input type="hidden" name="del" value="0" />
		{{mb_key object=$selOp}}
		{{if $selOp->date_visite_anesth}}
		  <input name="prat_visite_anesth_id" type="hidden" value="{{$selOp->prat_visite_anesth_id}}" />
		  <input name="date_visite_anesth"    type="hidden" value="" />
		{{/if}}
	
	<table class="form">
	  <tr>
	    <th class="title" colspan="2">Visite de pré-anesthésie</th>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$selOp field="date_visite_anesth"}}</th>
	    <td>
	    	{{if $selOp->date_visite_anesth}}
	  		  {{mb_value object=$selOp field="date_visite_anesth"}}
			  {{else}}
				  {{mb_field object=$selOp field="date_visite_anesth"}}
				{{/if}}
			</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$selOp field="prat_visite_anesth_id"}}</th>
	    <td>
	      {{if $selOp->date_visite_anesth}}
	      Dr {{mb_value object=$selOp field="prat_visite_anesth_id"}}
	      {{elseif $currUser->_is_anesth}}
	      <input name="prat_visite_anesth_id" type="hidden" value="{{$currUser->_id}}" />
	      Dr {{$currUser->_view}}
	      {{else}}
	      <select name="prat_visite_anesth_id" class="notNull" onchange="refreshAidesPreAnesth($V(this), this.options[this.selectedIndex].innerHTML.trim())">
	        <option value="">&mdash; Anesthésiste</option>
	        {{foreach from=$listAnesths item=curr_anesth}}
	        <option value="{{$curr_anesth->user_id}}" {{if $selOp->prat_visite_anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
	          {{$curr_anesth->_view}}
	        </option>
	        {{/foreach}}
	      </select>
	    {{/if}}
	    </td>
	  </tr>
	  <tr>
	    <th>
	      {{mb_label object=$selOp field="rques_visite_anesth"}}
	    </th>
	    <td class="text">
	      {{if $selOp->date_visite_anesth}}
	        {{mb_value object=$selOp field="rques_visite_anesth"}}
	      {{else}}
	        {{mb_field object=$selOp field="rques_visite_anesth"}}
	      {{/if}}
	    </td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$selOp field="autorisation_anesth"}}</th>
	    <td>
	      {{if $selOp->date_visite_anesth}}
	        {{mb_value object=$selOp field="autorisation_anesth"}}
	      {{else}}
	        {{mb_field object=$selOp field="autorisation_anesth"}}
	      {{/if}}
	    </td>
	  </tr>
	  {{if !$selOp->date_visite_anesth && !$currUser->_is_anesth}}
	  <tr>
	    <th>{{mb_label object=$selOp field="_password_visite_anesth"}}</th>
	    <td>{{mb_field object=$selOp field="_password_visite_anesth"}}</td>
	  </tr>
	  {{/if}}
	  {{if !$selOp->date_visite_anesth}}
	  <tr>
	    <td class="button" colspan="2">
	      <button class="submit" type="submit">
	        {{tr}}Validate{{/tr}}
	      </button>
	    </td>
	  </tr>
	  {{else}}
	  <tr>
	    <th>{{mb_label object=$selOp field="_password_visite_anesth"}}</th>
	    <td>{{mb_field object=$selOp field="_password_visite_anesth"}}</td>
	  </tr>
	  <tr>
	    <td class="button" colspan="2">
	      {{mb_field class="COperation" hidden="hidden" field="date_visite_anesth"}}
	      {{mb_field class="COperation" hidden="hidden" field="rques_visite_anesth"}}
	      {{mb_field class="COperation" hidden="hidden" field="autorisation_anesth"}}
	      <button class="trash" type="submit">
	        {{tr}}Cancel{{/tr}}
	      </button>
	    </td>
	  </tr>
	  {{/if}}
	</table>	
	</form>
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
	            onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshAnesthPerops('{{$selOp->_id}}'); $V(this.libelle, ''); }.bind(this)  } )">
	
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
                {{mb_field object=$anesth_perop field="libelle"}}
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
	<div id="perop"></div>
</div>