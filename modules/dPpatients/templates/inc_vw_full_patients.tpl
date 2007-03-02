<script type="text/javascript">

viewFullPatientMain = function() {
  PairEffect.initGroup("patientEffect", { 
    bStoreInCookie: true,
    sEffect: "Appear"
  } );
}
  
</script>
  

<table class="form">

<tr>
  <th class="title">
    <a href="#" onclick="viewCompleteItem('CPatient', {{$patient->_id}})">
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

<!-- Séjours -->

<tr id="sejours-trigger">
  <th colspan="2" class="title">{{$patient->_ref_sejours|@count}} séjour(s)</th>
</tr>

<tbody class="patientEffect" style="display: none" id="sejours">

{{foreach from=$patient->_ref_sejours item=curr_sejour}}
<tr>
  <td>
    <a title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->sejour_id}}">
      <img src="images/icons/edit.png" alt="Planifier"/>
    </a>

    <a href="#"
      onmouseover="ObjectTooltip.create(this, 'CSejour', {{$curr_sejour->_id}})"
      onclick="viewCompleteItem('CSejour', {{$curr_sejour->_id}})">
      Du {{$curr_sejour->entree_prevue|date_format:"%d/%m/%Y"}}
      au {{$curr_sejour->sortie_prevue|date_format:"%d/%m/%Y"}}
      - Dr. {{$curr_sejour->_ref_praticien->_view}}
    </a>

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
  <td style="padding-left: 10px;">
    <a title="Modifier l'intervention" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->_id}}">
      <img src="images/icons/edit.png" alt="Planifier"/>
    </a>
  
    <a href="#"
      onmouseover="ObjectTooltip.create(this, 'COperation', {{$curr_op->_id}})"
      onclick="viewCompleteItem('COperation', {{$curr_op->_id}})">
      {{$curr_op->_datetime|date_format:"%d/%m/%Y"}} - Intervention du Dr. {{$curr_op->_ref_chir->_view}}
    </a>
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
  <td style="padding-left: 10px;">
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
      onclick="viewCompleteItem('CConsultAnesth', {{$consult_anesth->_id}},'op')">
      {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}} - Dr. {{$curr_consult->_ref_chir->_view}}
    </a>
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
<tr><td colspan="2"><em>Pas de séjours</em></td></tr>

{{/foreach}}
</tbody>
  
<!-- Consultations -->

<tr id="consultations-trigger">
  <th colspan="2" class="title">{{$patient->_ref_consultations|@count}} consultation(s)</th>
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
      onclick="viewCompleteItem('{{$object_class}}', {{$object_id}})">
      {{$curr_consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}} - Dr. {{$curr_consult->_ref_chir->_view}}
    </a>
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
  <tr>
    <td class="button">
      <a class="buttonnew" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
        Consultation
      </a>
    </td>
    <td class="button">
      <a class="buttonnew" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Intervention
      </a>
    </td>
  </tr>
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
  {{if $listPrat|@count && $canEditCabinet}}
  <tr><th class="category" colspan="2">Consultation immédiate</th></tr>
  <tr>
    <td class="button" colspan="2">
      <form name="addConsFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" title="notNull refMandatory" value="{{$patient->patient_id}}" />
      <label for="prat_id" title="Praticien pour la consultation immédiate. Obligatoire">Praticien</label>
      <select name="prat_id" title="notNull refMandatory">
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