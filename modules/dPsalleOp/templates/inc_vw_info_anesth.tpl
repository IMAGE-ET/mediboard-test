{{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
{{if !@$modeles_prat_id}}
  {{assign var="modeles_prat_id" value=$selOp->_ref_anesth->_id}}
{{/if}}

<script type="text/javascript">
refreshAidesPreAnesth = function(user_id) {
  if(!document.visiteAnesth._helpers_rques_visite_anesth){
	  return;
	}
  
  var url = new Url('dPcompteRendu', 'httpreq_vw_select_aides');
  url.addParam('object_class', 'COperation');
  url.addParam('field', 'rques_visite_anesth');
  url.addParam('user_id', user_id);
  url.addParam('no_enum', 1);
  url.requestUpdate(document.visiteAnesth._helpers_rques_visite_anesth);
}

reloadDocumentsAnesth = function () {
  if(document.anesthTiming) {
    var sAnesth_id = document.anesthTiming.anesth_id.value;
  } else {
    var sAnesth_id = document.visiteAnesth.prat_anesth_id.value;
  }
  $$('.documents-CConsultAnesth-{{$consult_anesth->_id}}').each(function(doc){
    Document.refresh(doc, {praticien_id: sAnesth_id });
  });    
}

refreshAnesthPerops = function(operation_id){
  var url = new Url;
	url.setModuleAction("dPsalleOp", "httpreq_vw_anesth_perop");
	url.addParam("operation_id", operation_id);
	url.requestUpdate("list_perops_"+operation_id);
}

{{if $dialog}}
reloadAnesth = function() {
  window.opener.location.reload(true);
  window.location.reload(true);
}
{{/if}}

Main.add(function(){
  refreshAidesPreAnesth($V(getForm('visiteAnesth').prat_visite_anesth_id));
	refreshAnesthPerops('{{$selOp->_id}}');
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
	      {{mb_label object=$anesth_perop field="libelle"}}
	      <select name="_helpers_libelle" size="1" onchange="pasteHelperContent(this)">
	        <option value="">&mdash; Choisir une aide</option>
	        {{html_options options=$anesth_perop->_aides.libelle.no_enum}}
	      </select>
	      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CAnesthPerop', this.form._hidden_libelle, 'libelle')">{{tr}}New{{/tr}}</button><br />
	      <input type="hidden" name="_hidden_libelle" value="" />
	      <textarea name="libelle" onblur="if(!$(this).emptyValue()){this.form.onsubmit();}"></textarea>
	      <button class="submit" type="button">Ajouter</button>
      </form>
		</td>
		<td id="list_perops_{{$selOp->_id}}"></td>
	</tr>
	{{if $selOp->_ref_consult_anesth->_ref_techniques|@count}}
  <tr>
    <th colspan="2" class="title">Techniques complémentaires</th>
  </tr>
  <tr>
    <td colspan="2">
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

{{elseif $selOp->_ref_sejour->_ref_consult_anesth->_id}}
{{assign var="consult_anesth" value=$selOp->_ref_sejour->_ref_consult_anesth}}

<form name="linkConsultAnesth" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->_id}}" />
<input type="hidden" name="sejour_id" value="" />
<input type="hidden" name="operation_id" value="{{$selOp->_id}}" />
<table class="form">
  <tr>
    <td class="text">
      <div class="big-info">
        Une consultation d'anesthésie a été effectuée pour le séjour de ce patient
        le <strong>{{$consult_anesth->_date_consult|date_format:"%d/%m/%Y"}}</strong>
        par le <strong>Dr {{$consult_anesth->_ref_consultation->_ref_chir->_view}}</strong>.
        Vous devez <strong>relier cette consultation à l'intervention courante</strong> si vous désirez y accéder.
      </div>
    </td>
  </tr>
  <tr>
    <td class="button">
      <button type="submit" class="submit">Relier</button>
    </td>
  </tr>
</table>
</form>

{{else}}

<div class="big-info">
  Aucun dossier d'anesthésie n'a été associé à cette intervention ou ce séjour
  <br />
  Vour pouvez :
  <ul>
    <li>Soit <strong>associer un dossier d'anesthésie</strong> d'une consultation passée,</li>
    <li>Soit <strong>créer un nouveau dossier d'anesthésie</strong>.</li>
  </ul>
</div>


<table class="form">
	<tr>
	  <th colspan="3" class="category">Associer un dossier existant</th>
	</tr>

	{{foreach from=$patient->_ref_consultations item=_consultation}}
	{{assign var=consult_anesth value=$_consultation->_ref_consult_anesth}}
	{{if $consult_anesth->_id}}
	<tr>
	  <th>
	  	{{tr}}CConsultation{{/tr}} 
	  	du {{$_consultation->_date|date_format:$dPconfig.date}}
	 	</th>
	 	
	  {{if $_consultation->annule}}
	  <td colspan="2" class="cancelled">[Consultation annulée]</td>
		{{else}}	 	
	 	<td style="width: 1%;">
	  	Dr {{$_consultation->_ref_chir->_view}} 
	  </td> 
	  <td>
	    {{if $consult_anesth->_ref_operation->_id}}
		    Déjà associé :
		    <strong>{{$consult_anesth->_ref_operation->_view}}</strong>
			{{elseif $consult_anesth->_ref_sejour->_id}}
		    Déjà associé :
		    <strong>{{$consult_anesth->_ref_sejour->_view}}</strong>
		  {{else}}
		  
		  <form name="addOpFrm" action="?m={{$m}}" method="post">

		  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
		  <input type="hidden" name="del" value="0" />
		  <input type="hidden" name="m" value="dPcabinet" />
		  <input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->_id}}" />
		  <input type="hidden" name="operation_id" value="{{$selOp->_id}}" />

	    <button class="tick">{{tr}}Associate{{/tr}}</button>
	    
	    </form>
			{{/if}}
	  </td>
	  {{/if}}
	</tr>
	{{/if}}
	{{foreachelse}}
	<tr>
	  <td><em>Aucun dossier d'anesthésie existant pour ce patient</em></td>
	</tr>
	{{/foreach}}

	<tr>
	  <th colspan="3" class="category">Créer un nouveau dossier</th>
	</tr>
  <tr>
    <td colspan="3" class="button">
			<form name="createConsult" action="?m={{$m}}" method="post" onsubmit="{{$onSubmit}}">
			
			<input type="hidden" name="dosql" value="do_consult_now" />
			<input type="hidden" name="m" value="dPcabinet" />
			<input type="hidden" name="del" value="0" />
			<input type="hidden" name="consultation_id" value="" />
			<input type="hidden" name="_operation_id" value="{{$selOp->_id}}" />
			<input type="hidden" name="_redirect" value="?" />
			<input type="hidden" name="patient_id" value="{{$selOp->_ref_sejour->patient_id}}" />
      <select name="prat_id">
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>

      <button type="submit" class="new">{{tr}}Create{{/tr}}</button>

			</form>

    </td>
  </tr>
</table>

{{/if}}

<form name="visiteAnesth" action="?m={{$m}}" method="post" onsubmit="{{$onSubmit}}">
<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$selOp}}
{{if !$selOp->date_visite_anesth}}
<input name="date_visite_anesth" type="hidden" value="current" />
{{else}}
<input name="prat_visite_anesth_id" type="hidden" value="{{$selOp->prat_visite_anesth_id}}" />
<input name="date_visite_anesth"    type="hidden" value="" />
{{/if}}

<table class="form">
  <tr>
    <th class="title" colspan="2">Visite de pré-anesthésie</th>
  </tr>
  {{if $selOp->date_visite_anesth}}
  <tr>
    <th>{{mb_label object=$selOp field="date_visite_anesth"}}</th>
    <td>{{mb_value object=$selOp field="date_visite_anesth"}}</td>
  </tr>
  {{/if}}
  <tr>
    <th>{{mb_label object=$selOp field="prat_visite_anesth_id"}}</th>
    <td>
      {{if $selOp->date_visite_anesth}}
      Dr {{mb_value object=$selOp field="prat_visite_anesth_id"}}
      {{elseif $currUser->_is_anesth}}
      <input name="prat_visite_anesth_id" type="hidden" value="{{$currUser->_id}}" />
      Dr {{$currUser->_view}}
      {{else}}
      <select name="prat_visite_anesth_id" class="notNull" onchange="refreshAidesPreAnesth($V(this))">
        <option value="">&mdash; Anesthésiste</option>
        {{foreach from=$listAnesths item=curr_anesth}}
        <option value="{{$curr_anesth->user_id}}" {{if $selOp->prat_visite_anesth_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
          {{$curr_anesth->_view}}
        </option>
        {{/foreach}}
      </select>
    {{/if}}
    </td>
  <tr>
  </tr>
    <th>
      {{mb_label object=$selOp field="rques_visite_anesth"}}
      {{if !$selOp->date_visite_anesth}}
      <!-- Ajout des aides à la saisie : mais il faut les charger selon le praticien qu'on indique !! -->
      <select name="_helpers_rques_visite_anesth" style="width: 7em;" onchange="pasteHelperContent(this)">
      </select>
			<button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('COperation', this.form.rques_visite_anesth)">
			  {{tr}}New{{/tr}}
			</button>
      {{/if}}
    </th>
    <td class="text">
      {{if $selOp->date_visite_anesth}}
        {{mb_value object=$selOp field="rques_visite_anesth"}}
      {{else}}
        {{mb_field object=$selOp field="rques_visite_anesth"}}
      {{/if}}
    </td>
  <tr>
  </tr>
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
        {{tr}}Valider{{/tr}}
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

{{if $consult_anesth->_id}}
<table class="form">
  <tr>
    <th colspan="2" class="title">Fichiers / Documents</th>
  </tr>
  <tr>
    <td>
      {{mb_include_script module="dPcabinet" script="file"}}
      <div id="files-anesth">
      <script type="text/javascript">
        File.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class_name}}', 'files-anesth');
      </script>
      </div>
    </td>
    <td>
      <div id="documents-anesth">
        {{mb_include_script module="dPcompteRendu" script="document"}}
        <script type="text/javascript">
          Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class_name}}','{{$modeles_prat_id}}','documents-anesth');
        </script>
			</div>
    </td>
  </tr>
</table>
{{/if}}