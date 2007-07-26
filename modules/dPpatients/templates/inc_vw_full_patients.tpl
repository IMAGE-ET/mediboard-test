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
      bStoreInCookie: true
    } );
  }
}

var urlDHEParams = {{$patient->_urlDHEParams|@json}};

function newDHE(oForm) {
  {{if !$codePraticienEc || !$etablissements|@count}}
    alert("Vous n'êtes pas autorisé à créer une DHE");
  {{elseif !$patient->naissance || $patient->naissance == "0000-00-00"}}
    alert("Le patient n'a pas une date de naissance valide");
  {{else}}
    var url = new Url;
    url.addParam("codeClinique", oForm.etablissement.value);
    for(param in urlDHEParams) {
      if(param != "extends") {
        url.addParam(param, urlDHEParams[param]);
      }
    }
    url.popDirect("900", "600", "eCap", "{{$patient->_urlDHE|smarty:nodefaults}}");
  {{/if}}
}
 
function popEtatSejour(sejour_id) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 550, 'Etat du Séjour');
}
 
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
      <a href="#" 
        onclick="setObject( {
          objClass: 'CPatient', 
          keywords: '', 
          id: {{$patient->patient_id|smarty:nodefaults|JSAttribute}}, 
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
  	<a href="#" onclick="popEtatSejour({{$curr_sejour->sejour_id}});">
    	<img src="images/icons/jumelle.png" alt="edit" title="Etat du Séjour" />
    </a>
    <a title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
      <img src="images/icons/edit.png" alt="Planifier"/>
    </a>

    <a href="#"
      onmouseover="ObjectTooltip.create(this, 'CSejour', {{$curr_sejour->_id}})"
      onclick="viewCompleteItem('CSejour', {{$curr_sejour->_id}}); ViewFullPatient.select(this)">
      Du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
      au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
    </a>
  </td>
  <td>
     {{assign var=praticien value=$curr_sejour->_ref_praticien}}
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
  </td>
  <td style="text-align:right;">
  {{if $curr_sejour->_canRead}}
    <a href="#" onclick="setObject( {
      objClass: 'CSejour', 
      keywords: '', 
      id: {{$curr_sejour->sejour_id|smarty:nodefaults|JSAttribute}}, 
      view:'{{$curr_sejour->_view|smarty:nodefaults|JSAttribute}}'} )"
      title="{{$curr_sejour->_nb_files_docs}} doc(s)">
      {{$curr_sejour->_nb_files_docs}}
      <img align="top" src="images/icons/next{{if !$curr_sejour->_nb_files_docs}}_red{{/if}}.png" title="{{$curr_sejour->_nb_files_docs}} doc(s)" alt="Afficher les documents"  />
    </a>
    {{/if}}         
  </td>
</tr>

{{foreach from=$curr_sejour->_ref_operations item=curr_op}}
<tr>
  <td style="padding-left: 20px;">
    <a title="Modifier l'intervention" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
      <img src="images/icons/edit.png" alt="Planifier"/>
    </a>
  
    <a href="#"
      onmouseover="ObjectTooltip.create(this, 'COperation', {{$curr_op->_id}})"
      onclick="viewCompleteItem('COperation', {{$curr_op->_id}}); ViewFullPatient.select(this)">
      Intervention du {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
    </a>
  </td>
  <td>
     {{assign var=praticien value=$curr_op->_ref_chir}}
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
  </td>
  <td style="text-align:right;">
  {{if $curr_op->_canRead}}
    <a href="#" onclick="setObject( {
      objClass: 'COperation', 
      keywords: '', 
      id: {{$curr_op->operation_id|smarty:nodefaults|JSAttribute}}, 
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
<tr>
  <td style="padding-left: 20px;">
    {{assign var="curr_consult" value=$consult_anesth->_ref_consultation}}
    {{if $curr_consult->annule}}
    [ANNULE]<br />
    {{else}}
    <a href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
      <img src="images/icons/planning.png" alt="modifier" title="rendez-vous" />
    </a>
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
      <img src="images/icons/edit.png" alt="modifier" title="modifier" />
    </a>
    {{/if}}
    <img src="images/icons/anesth.png" alt="Consultation d'anesthésie" title="Consultation d'anesthésie" />
    <a href="#"
      onmouseover="ObjectTooltip.create(this, 'CConsultAnesth', {{$consult_anesth->_id}})"
      onclick="viewCompleteItem('CConsultAnesth', {{$consult_anesth->_id}}); ViewFullPatient.select(this)">
      {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
    </a>
  </td>
  <td>
     {{assign var=praticien value=$curr_consult->_ref_chir}}
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
  </td>
  
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
    {{if $curr_consult->annule}}
    [ANNULE]<br />
    {{else}}
    <a href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->consultation_id}}">
      <img src="images/icons/planning.png" alt="modifier" title="rendez-vous" />
    </a>
    <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->consultation_id}}">
      <img src="images/icons/edit.png" alt="modifier" title="modifier" />
    </a>
    {{/if}}
    
    {{if $curr_consult->_ref_consult_anesth->consultation_anesth_id}}
      {{assign var="object_id" value=$curr_consult->_ref_consult_anesth->consultation_anesth_id}}
      {{assign var="object_class" value="CConsultAnesth"}}
      <img src="images/icons/anesth.png" alt="Consultation d'anesthésie" title="Consultation d'anesthésie" />
    {{else}}
      {{assign var="object_id" value=$curr_consult->_id}}
      {{assign var="object_class" value="CConsultation"}}
    {{/if}}
    
    <a href="#"
      onmouseover="ObjectTooltip.create(this, '{{$object_class}}', {{$object_id}})"
      onclick="viewCompleteItem('{{$object_class}}', {{$object_id}}); ViewFullPatient.select(this)">
      {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
    </a>
  </td>
  <td>
     {{assign var=praticien value=$curr_consult->_ref_chir}}
     <div class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};">
       {{$praticien->_view}}
     </div>
  </td>
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

{{if $diagnosticsInstall}}
<tr>
  <th colspan="4" class="title">
    <a href="#" onclick="view_labo()">
      Laboratoires
    </a>
  </th>
</tr>
{{/if}}

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
    <td class="button">
	    {{if $dPconfig.interop.mode_compat == "medicap"}}
	    <form name="newActionDHE" action="" method="get">
	    <button style="margin: 1px;" class="new" type="button" onclick="newDHE(this.form)">Nouvelle DHE</button>
	    <br />
	    <select name="etablissement">
	      {{foreach from=$etablissements item=currEtablissement key=keyEtablissement}}
	      <option value="{{$keyEtablissement}}" {{if $currEtablissement->group_id==$g}}selected="selected"{{/if}}>
	        {{$currEtablissement->_view}}
	      </option>
	      {{/foreach}}
	    </select>
	    </form>
	    {{else}}
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Intervention
      </a>
      {{/if}}
    </td>
  </tr>
  {{if $dPconfig.interop.mode_compat != "medicap"}}
  <tr>
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->patient_id}}&amp;sejour_id=0">
        Séjour
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Urgence
      </a>
    </td>
  </tr>
  {{/if}}
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