{{mb_include_script module="dPImeds" script="Imeds_results_watcher"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">

var ViewFullPatient = {
  select: function(eLink) {
    // Select current row
    if (this.eCurrent) {
      Element.classNames(this.eCurrent).remove("selected");
    }
    this.eCurrent = eLink.parentNode.parentNode;
    Element.classNames(this.eCurrent).add("selected");
  },
  
  main: function() {
    PairEffect.initGroup("patientEffect", {
      idStartVisible: true,
      bStoreInCookie: true
    } );
  }
}

function popEtatSejour(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 550, 'Etat du Séjour');
}

Main.add(function () {
  {{if $isImedsInstalled}}
    ImedsResultsWatcher.loadResults();
  {{/if}}
});
 
</script>
  

<table class="form">

<tr>
  <th class="title" colspan="2">
    <a href="#" onclick="viewCompleteItem('CPatient', {{$patient->_id}}); ViewFullPatient.select(this)">
      {{$patient->_view}} ({{$patient->_age}} ans)
    </a>
  </th>
  <th class="title">
    {{if $patient->_canRead}}
    <div style="float:right;">
      {{if $isImedsInstalled}}
      <a href="#" onclick="view_labo_patient()">
        <img align="top" src="images/icons/labo.png" title="Résultats de laboratoire" alt="Résultats de laboratoire"  />
      </a>
      {{/if}}
      <a href="#" 
        onclick="setObject( {
          objClass: 'CPatient', 
          keywords: '', 
          id: {{$patient->patient_id}}, 
          view: '{{$patient->_view|smarty:nodefaults|JSAttribute}}' })"
        title="{{$patient->_nb_files_docs}} doc(s)">
        {{$patient->_nb_files_docs}}
        <img align="top" src="images/icons/{{if !$patient->_nb_files_docs}}next_red.png{{else}}next.png{{/if}}" title="{{$patient->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />                
      </a>
    </div>
    {{/if}} 
  </th>
</tr>

{{if !$app->user_prefs.simpleCabinet}}
<!-- Séjours -->

<tr id="sejours-trigger">
  <th colspan="3" class="title">{{$patient->_ref_sejours|@count}} séjour(s)</th>
</tr>

<tbody class="patientEffect" style="display: none" id="sejours">

{{foreach from=$patient->_ref_sejours item=curr_sejour}}
<tr id="CSejour-{{$curr_sejour->_id}}">
  <td>
  	<a href="#" onclick="popEtatSejour({{$curr_sejour->_id}});">
    	<img src="images/icons/jumelle.png" alt="edit" title="Etat du Séjour" />
    </a>
    <a title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->_id}}">
      <img src="images/icons/edit.png" alt="Planifier"/>
    </a>

    <span class="tooltip-trigger"
      onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CSejour', object_id: {{$curr_sejour->_id}} } })"
      onclick="viewCompleteItem('CSejour', {{$curr_sejour->_id}}); ViewFullPatient.select(this)">
      Du {{$curr_sejour->_entree|date_format:$dPconfig.date}} 
      au {{$curr_sejour->_sortie|date_format:$dPconfig.date}}
    </span>
    <script language="Javascript" type="text/javascript">
      ImedsResultsWatcher.addSejour('{{$curr_sejour->_id}}', '{{$curr_sejour->_num_dossier}}');
    </script>
  </td>

	{{assign var=praticien value=$curr_sejour->_ref_praticien}}
	{{if $curr_sejour->annule}}
	<td {{if $curr_sejour->group_id != $g}}style="background-color:#afa"{{else}}class="cancelled"{{/if}}>
	  <strong>SEJOUR ANNULE</strong>
	</td>
	{{else}}
		{{if $curr_sejour->group_id == $g}}
		<td>
		 <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
		   {{$praticien->_view}}
		 </div>
		</td>
		{{else}}
		<td style="background-color:#afa">
		  {{$curr_sejour->_ref_group->text|upper}}
		</td>
		{{/if}}
	{{/if}}
  
  <td style="text-align:right;">
  {{if $curr_sejour->_canRead}}
    {{if $isImedsInstalled}}
    <div id="labo_for_{{$curr_sejour->_id}}" style="display: none; float: left;">
    <a href="#" onclick="view_labo_sejour({{$curr_sejour->_id}})">
      <img align="top" src="images/icons/labo.png" title="Résultats de laboratoire" alt="Résultats de laboratoire"  />
    </a>
    </div>
    <div id="labo_hot_for_{{$curr_sejour->_id}}" style="display: none; float: left;">
    <a href="#" onclick="view_labo_sejour({{$curr_sejour->_id}})">
      <img align="top" src="images/icons/labo_hot.png" title="Résultats de laboratoire" alt="Résultats de laboratoire"  />
    </a>
    </div>
    {{/if}}
    <a href="#" onclick="setObject( {
      objClass: 'CSejour', 
      keywords: '', 
      id: {{$curr_sejour->_id}}, 
      view:'{{$curr_sejour->_view|smarty:nodefaults|JSAttribute}}'} )"
      title="{{$curr_sejour->_nb_files_docs}} doc(s)">
      {{$curr_sejour->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$curr_sejour->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_sejour->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
    {{/if}}         
  </td>
</tr>

{{if $curr_sejour->_ref_rpu && $curr_sejour->_ref_rpu->_id}}
{{assign var=rpu value=$curr_sejour->_ref_rpu}}
<tr>
  <td style="padding-left: 20px;" colspan="2">
    Passage aux urgences
  </td>
  <td style="text-align:right;">
  {{if $rpu->_canRead}}
    <a href="#" onclick="setObject( {
      objClass: 'CRPU', 
      keywords: '', 
      id: {{$rpu->_id}}, 
      view:'{{$rpu->_view|smarty:nodefaults|JSAttribute}}'} )"
      title="{{$rpu->_nb_files_docs}} doc(s)">
      {{$rpu->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$rpu->_nb_files_docs}}_red{{/if}}.png" title="{{$rpu->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
    {{/if}} 
  </td>
</tr>
{{/if}}

<!-- Si le sejour a un RPU et une consultation associée-->
{{if $curr_sejour->_ref_rpu && $curr_sejour->_ref_rpu->_id && $curr_sejour->_ref_rpu->_ref_consult->_id}}
{{assign var="curr_consult" value=$curr_sejour->_ref_rpu->_ref_consult}}
<tr>
  <td>
    <a href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
      <img src="images/icons/planning.png" alt="modifier" title="rendez-vous" />
    </a>
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
      <img src="images/icons/edit.png" alt="modifier" title="modifier" />
    </a>

    {{assign var="object_id" value=$curr_consult->_id}}    
    {{assign var="object_class" value="CConsultation"}}
    <a href="#"
      onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$object_class}}', object_id: {{$object_id}} } })"
      onclick="viewCompleteItem('{{$object_class}}', {{$object_id}}); ViewFullPatient.select(this)">
      Le {{$curr_consult->_datetime|date_format:$dPconfig.date}}
    </a>
  </td>
  
  {{assign var=praticien value=$curr_consult->_ref_chir}}
  {{if $curr_consult->annule}}
  <td class="cancelled">[Consult annulée]</td>
  {{else}}
  <td>
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
	</td>
  {{/if}}

  <td style="text-align: right;">
 
  {{if $curr_sejour->_canRead}}
    <a href="#" title="{{$curr_consult->_nb_files_docs}} doc(s)"
      onclick="setObject( {
        objClass: 'CConsultation', 
        keywords: '', 
        id: {{$curr_consult->consultation_id}}, 
        view: '{{$curr_consult->_view|smarty:nodefaults|JSAttribute}}'} )">
      {{$curr_consult->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$curr_consult->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_consult->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
   {{/if}}
  </td>
</tr>

{{/if}}

{{foreach from=$curr_sejour->_ref_operations item=curr_op}}
<tr>
  <td style="padding-left: 20px;">
    <a title="Modifier l'intervention" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
      <img src="images/icons/edit.png" alt="Planifier"/>
    </a>
  
    <span class="tooltip-trigger"
      onmouseover="ObjectTooltip.create(this, { params: { object_class: 'COperation', object_id: {{$curr_op->_id}} } })"
      onclick="viewCompleteItem('COperation', {{$curr_op->_id}}); ViewFullPatient.select(this)">
      Intervention le {{$curr_op->_datetime|date_format:$dPconfig.date}}
    </span>
  </td>

  {{assign var=praticien value=$curr_op->_ref_chir}}
  {{if $curr_op->annulee}}
  <td {{if $curr_sejour->group_id != $g}}style="background-color:#afa"{{else}}class="cancelled"{{/if}}>
    <strong>OPERATION ANNULEE</strong>
  </td>
  {{else}}
	  {{if $curr_sejour->group_id != $g}}
	  <td style="background-color:#afa">
	    {{$curr_sejour->_ref_group->_view|upper}}
	  </td>
	  {{else}}
	  <td>
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
	  </td>
	  {{/if}}
  {{/if}}

  <td style="text-align:right;">
  {{if $curr_op->_canRead}}
    <a href="#" onclick="setObject( {
      objClass: 'COperation', 
      keywords: '', 
      id: {{$curr_op->operation_id}}, 
      view:'{{$curr_op->_view|smarty:nodefaults|JSAttribute}}'} )"
      title="{{$curr_op->_nb_files_docs}} doc(s)">
      {{$curr_op->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$curr_op->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_op->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
    {{/if}} 
  </td>
</tr>

{{assign var="consult_anesth" value=$curr_op->_ref_consult_anesth}}
{{if $consult_anesth->_id}}
{{assign var="curr_consult" value=$consult_anesth->_ref_consultation}}
<tr>
  <td style="padding-left: 20px;">
    <a href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
      <img src="images/icons/planning.png" alt="modifier" title="rendez-vous" />
    </a>
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
      <img src="images/icons/edit.png" alt="modifier" title="modifier" />
    </a>

    <img src="images/icons/anesth.png" alt="Consultation d'anesthésie" title="Consultation d'anesthésie" />
    <span class="tooltip-trigger"
      onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CConsultAnesth', object_id: {{$consult_anesth->_id}} } })"
      onclick="viewCompleteItem('CConsultAnesth', {{$consult_anesth->_id}}); ViewFullPatient.select(this)">
      Le {{$curr_consult->_datetime|date_format:$dPconfig.date}}
    </span>
  </td>

  {{assign var=praticien value=$curr_consult->_ref_chir}}
  {{assign var=praticien value=$curr_consult->_ref_chir}}
  {{if $curr_consult->annule}}
  <td class="cancelled">[Consult annulée]</td>
  {{else}}
  <td>
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
	</td>
  {{/if}}
  
  <td style="text-align:right;">
  {{if $curr_consult->_canRead}}
    <a href="#" title="{{$curr_consult->_nb_files_docs}} doc(s)"
      onclick="setObject( {
        objClass: 'CConsultation', 
        keywords: '', 
        id: {{$curr_consult->consultation_id}}, 
        view: '{{$curr_consult->_view|smarty:nodefaults|JSAttribute}}'} )">
      {{$curr_consult->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$curr_consult->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_consult->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
    {{/if}}
  </td>
</tr>
{{/if}}
{{/foreach}}

{{foreachelse}}
<tr><td colspan="3"><em>Pas de séjours</em></td></tr>
{{/foreach}}
</tbody>

{{/if}}  

<!-- Consultations -->

<tr id="consultations-trigger">
  <th colspan="3" class="title">{{$patient->_ref_consultations|@count}} consultation(s)</th>
</tr>

<tbody class="patientEffect" style="display: none" id="consultations">

{{foreach from=$patient->_ref_consultations item=curr_consult}}
<tr>
  <td>
    <a href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
      <img src="images/icons/planning.png" alt="modifier" title="rendez-vous" />
    </a>
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
      <img src="images/icons/edit.png" alt="modifier" title="modifier" />
    </a>
    
    {{if $curr_consult->_ref_consult_anesth->consultation_anesth_id}}
      {{assign var="object_id" value=$curr_consult->_ref_consult_anesth->consultation_anesth_id}}
      {{assign var="object_class" value="CConsultAnesth"}}
      <img src="images/icons/anesth.png" alt="Consultation d'anesthésie" title="Consultation d'anesthésie" />
    {{else}}
      {{assign var="object_id" value=$curr_consult->_id}}
      {{assign var="object_class" value="CConsultation"}}
    {{/if}}
    
    <span class="tooltip-trigger"
      onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$object_class}}', object_id: {{$object_id}} } })"
      onclick="viewCompleteItem('{{$object_class}}', {{$object_id}}); ViewFullPatient.select(this)">
      Le {{$curr_consult->_datetime|date_format:$dPconfig.date}}
    </span>
  </td>
  
  {{assign var=praticien value=$curr_consult->_ref_chir}}
  {{if $curr_consult->annule}}
  <td class="cancelled">[Consult annulée]</td>
  {{else}}
  <td>
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
	</td>
  {{/if}}

  <td style="text-align:right;">
  {{if $curr_consult->_canRead}}
    <a href="#" title="{{$curr_consult->_nb_files_docs}} doc(s)"
      onclick="setObject( {
        objClass: 'CConsultation', 
        keywords: '', 
        id: {{$curr_consult->consultation_id}}, 
        view: '{{$curr_consult->_view|smarty:nodefaults|JSAttribute}}'} )">
      {{$curr_consult->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$curr_consult->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_consult->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
    {{/if}}
  </td>
</tr>
{{foreachelse}}
<tr><td colspan="3"><em>Pas de consultations</em></td></tr>
{{/foreach}}

</tbody>

</table>
  
<hr/>
  
<!-- Planifier -->

<table class="form">

<tr id="planifier-trigger">
  <th colspan="2" class="title">Planifier</th>
</tr>

<tbody class="patientEffect" style="display: none" id="planifier">
  <tr><th class="category" colspan="2">Evènements</th></tr>
  {{if $app->user_prefs.simpleCabinet}}
  <tr>
    <td class="button" colspan="2">
      <a class="buttonnew" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
        Consultation
      </a>
    </td>
  </tr>
  {{else}}
  <tr>
    <td class="button">
      <a class="buttonnew" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
        Consultation
      </a>
    </td>
  </tr>
  <tr>
    <td class="button">
    {{if @$modules.ecap->mod_active}}
	    {{mb_include_script module=ecap script=dhe}}
	    <div id="dhe"></div>
	    <script type="text/javascript">DHE.register({{$patient->patient_id}}, null, "dhe");</script>
	  {{else}}
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->patient_id}}&amp;sejour_id=0">
        Séjour
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Urgence
      </a>
    {{/if}}
    </td>
  </tr>
  {{/if}}

  {{if $listPrat|@count && $canCabinet->edit}}
  <tr><th class="category" colspan="2">Consultation immédiate</th></tr>
  <tr>
    <td class="button" colspan="2">
      <form name="addConsFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" class="notNull ref" value="{{$patient->patient_id}}" />
      <label for="prat_id" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>
      <select name="prat_id" class="notNull ref">
        <option value="">&mdash; Choisir un praticien</option>
        {{foreach from=$listPrat item=curr_prat}}
          <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
            {{$curr_prat->_view}}
          </option>
        {{/foreach}}
      </select>
      <button class="new" type="submit">Consulter</button>
      </form>
    </td>
  </tr>
  {{/if}}
</tbody>        

</table>