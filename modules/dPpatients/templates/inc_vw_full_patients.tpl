{{mb_include_script module="dPImeds" script="Imeds_results_watcher"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">

var ViewFullPatient = {
  select: function(eLink) {
    // Unselect previous row
    if (this.idCurrent) {
      $(this.idCurrent).removeClassName("selected");
    }
    
    // Select current row
    this.idCurrent = $(eLink).up(1).identify();
    $(this.idCurrent).addClassName("selected");
  },
  
  main: function() {
    PairEffect.initGroup("patientEffect", {
      bStartAllVisible: true,
      bStoreInCookie: true
    } );
  }
}

function popEtatSejour(sejour_id) {
  var url = new Url("dPhospi", "vw_parcours");
  url.addParam("sejour_id",sejour_id);
  url.pop(1000, 550, 'Etat du S�jour');
}

{{if $isImedsInstalled}}
//Main.add(ImedsResultsWatcher.loadResults);
{{/if}}
 
</script>

<table class="tbl" style="vertical-align: middle;">

<tr>
  <th class="title" colspan="3">
    <a href="#" onclick="viewCompleteItem('{{$patient->_guid}}'); ViewFullPatient.select(this)">
      {{$patient->_view}} ({{$patient->_age}} ans)
    </a>
  </th>
  <th class="title">
    {{if $patient->_canRead}}
    <div style="float:right;">
      {{if $isImedsInstalled}}
      <a href="#" onclick="view_labo_patient()">
        <img align="top" src="images/icons/labo.png" title="R�sultats de laboratoire" />
      </a>
      {{/if}}
      {{mb_include module=dPpatients template=inc_form_docitems_button object=$patient}}
    </div>
    {{/if}} 
  </th>
</tr>

{{if !$app->user_prefs.simpleCabinet}}
<!-- S�jours -->
<tr id="sejours-trigger">
  <th colspan="4" class="title">S�jours</th>
</tr>
<tbody class="patientEffect" style="display: none" id="sejours">
{{foreach from=$patient->_ref_sejours item=_sejour}}
  {{if $_sejour->group_id == $g || $dPconfig.dPpatients.CPatient.multi_group == "full"}}
    <tr id="CSejour-{{$_sejour->_id}}">
      <td style="width: 0.1%;">
        <button class="lookup notext" onclick="popEtatSejour({{$_sejour->_id}});">{{tr}}Lookup{{/tr}}</button>
      </td>
			<td>   
        <a href="#" onclick="{{if @$can_view_dossier_medical}}viewDossierSejour('{{$_sejour->_id}}');{{else}}viewCompleteItem('{{$_sejour->_guid}}');{{/if}} ViewFullPatient.select(this)">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
            {{$_sejour->_shortview}} 
          </span>
        </a>
        <script type="text/javascript">
          ImedsResultsWatcher.addSejour('{{$_sejour->_id}}', '{{$_sejour->_num_dossier}}');
        </script>
      </td>
    
      {{assign var=praticien value=$_sejour->_ref_praticien}}
      <td {{if $_sejour->annule}}style="text-align: left;" class="cancelled"{{/if}}>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
      </td>
      
      <td style="text-align:right;">
      {{if $_sejour->_canRead}}
        {{if $isImedsInstalled}}
        <div id="labo_for_{{$_sejour->_id}}" style="display: none; float: left;">
          <a href="#" onclick="view_labo_sejour({{$_sejour->_id}})">
            <img src="images/icons/labo.png" title="R�sultats de laboratoire" />
          </a>
        </div>
        <div id="labo_hot_for_{{$_sejour->_id}}" style="display: none; float: left;">
          <a href="#" onclick="view_labo_sejour({{$_sejour->_id}})">
            <img src="images/icons/labo_hot.png" title="R�sultats de laboratoire"/>
          </a>
        </div>
        {{/if}}

        {{mb_include module=dPpatients template=inc_form_docitems_button object=$_sejour}}
      {{/if}}         
      </td>
    </tr>
    
    <!-- Parcours des consultation d'un s�jour -->
    {{foreach from=$_sejour->_ref_consultations item=_consult}}
    <tr>
      <td colspan="2">
        <a class="iconed-text {{$_consult->_type}}" style="margin-left: 20px" href="#"
          onclick="viewCompleteItem('{{$_consult->_guid}}'); ViewFullPatient.select(this)">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}');">
            Consult. le {{$_consult->_datetime|date_format:$dPconfig.date}}
          </span>
        </a>
      </td>
      
      {{assign var=praticien value=$_consult->_ref_chir}}
      
      {{if $_consult->annule}}
      <td style="text-align: left;" class="cancelled">
      {{else}}
      <td>
      {{/if}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
      </td>
    
      <td style="text-align: right;">
     
      {{if $_sejour->_canRead}}
        {{mb_include module=dPpatients template=inc_form_docitems_button object=$_consult}}
       {{/if}}
      </td>
    </tr>
    {{/foreach}}
  
    {{foreach from=$_sejour->_ref_operations item=_op}}
    <tr>
      <td colspan="2">
        <a href="#" class="iconed-text interv" style="margin-left: 20px" 
           onclick="viewCompleteItem('{{$_op->_guid}}'); ViewFullPatient.select(this)">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
            Interv. le {{$_op->_datetime|date_format:$dPconfig.date}}
          </span>
        </a>
      </td>
    
      {{assign var=praticien value=$_op->_ref_chir}}
      <td {{if $_op->annulee}}style="text-align: left;" class="cancelled"{{/if}}>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
      </td>
    
      <td style="text-align:right;">
      {{if $_op->_canRead}}
        {{mb_include module=dPpatients template=inc_form_docitems_button object=$_op}}
      {{/if}} 
      </td>
    </tr>
  
    {{assign var="consult_anesth" value=$_op->_ref_consult_anesth}}
    {{if $consult_anesth->_id}}
    {{assign var="_consult" value=$consult_anesth->_ref_consultation}}
    <tr>
      <td colspan="2" style="padding-left: 20px;">
        <span
          {{if $consult_anesth->_id}}class="iconed-text anesth"{{/if}} 
          onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}')"
          onclick="viewCompleteItem('{{$consult_anesth->_guid}}'); ViewFullPatient.select(this)">
          Consult le {{$_consult->_datetime|date_format:$dPconfig.date}}
        </span>
      </td>
    
      {{assign var=praticien value=$_consult->_ref_chir}}
      {{if $_consult->annule}}
      <td style="text-align: left;" class="cancelled">[Consult annul�e]</td>
      {{else}}
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
      </td>
      {{/if}}
      
      <td style="text-align:right;">
      {{if $_consult->_canRead}}
        {{mb_include module=dPpatients template=inc_form_docitems_button object=$_consult}}
      {{/if}}
      </td>
    </tr>
  {{/if}}
  {{/foreach}}
{{elseif $dPconfig.dPpatients.CPatient.multi_group == "limited" && !$_sejour->annule}}
  <tr>
    <td colspan="2">
      {{$_sejour->_shortview}}
    </td>
    <td colspan="2" style="background-color:#afa">
      {{$_sejour->_ref_group->text|upper}}
    </td>
  </tr>
{{/if}}
{{/foreach}}
</tbody>
{{/if}}  

<!-- Consultations -->

<tr id="consultations-trigger">
  <th colspan="4" class="title">Consultations</th>
</tr>

<tbody class="patientEffect" style="display: none" id="consultations">

{{foreach from=$patient->_ref_consultations item=_consult}}
  {{if $_consult->_ref_chir->_ref_function->group_id == $g || $dPconfig.dPpatients.CPatient.multi_group == "full"}}
  <tr>
    <td colspan="2">
      <a href="#" class="iconed-text {{$_consult->_type}}"  onclick="viewCompleteItem('{{$_consult->_guid}}'); ViewFullPatient.select(this)">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}');">
          Consult. le {{$_consult->_datetime|date_format:$dPconfig.date}}
        </span>
      </a>
    </td>
    
    {{assign var=praticien value=$_consult->_ref_chir}}
    {{if $_consult->annule}}
    <td style="text-align: left;" class="cancelled">
    {{else}}
    <td>
    {{/if}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
    </td>
  
    <td style="text-align:right;">
    {{if $_consult->_canRead}}
      {{mb_include module=dPpatients template=inc_form_docitems_button object=$_consult}}
    {{/if}}
    </td>
  </tr>
  {{elseif $dPconfig.dPpatients.CPatient.multi_group == "limited" && !$_consult->annule}}
  <tr>
    <td colspan="2">
      Le {{$_consult->_datetime|date_format:$dPconfig.datetime}}
    </td>
    <td style="background-color:#afa" colspan="2">
      {{$_consult->_ref_chir->_ref_function->_ref_group->text|upper}}
    </td>
  </tr>
  {{/if}}
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
  <tr><th class="category" colspan="2">Ev�nements</th></tr>
  {{if $app->user_prefs.simpleCabinet}}
  <tr>
    <td class="button" colspan="2">
      <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
        Consultation
      </a>
    </td>
  </tr>
  {{else}}
  <tr>
    <td class="button">
      <a class="button new" href="?m=dPcabinet&amp;tab=edit_planning&amp;pat_id={{$patient->patient_id}}&amp;consultation_id=0">
        Consultation
      </a>
    </td>
    <td class="button">
      {{if !@$modules.ecap->mod_active}}
      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Intervention
      </a>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="button">
    {{if @$modules.ecap->mod_active}}
      {{mb_include_script module=ecap script=dhe}}
      <div id="dhe"></div>
      <script type="text/javascript">DHE.register({{$patient->patient_id}}, null, "dhe");</script>
    {{else}}
      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;patient_id={{$patient->patient_id}}&amp;sejour_id=0">
        S�jour
      </a>
    </td>
    <td class="button">
      <a class="button new" href="?m=dPplanningOp&amp;tab=vw_edit_urgence&amp;pat_id={{$patient->patient_id}}&amp;operation_id=0&amp;sejour_id=0">
        Interv. hors plage
      </a>
    {{/if}}
    </td>
  </tr>
  {{/if}}

  {{if $listPrat|@count && $canCabinet->edit}}
  <tr><th class="category" colspan="2">Consultation imm�diate</th></tr>
  <tr>
    <td class="button" colspan="2">
      <form name="addConsFrm" action="?m=dPcabinet" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consult_now" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="patient_id" class="notNull ref" value="{{$patient->patient_id}}" />
      <label for="prat_id" title="Praticien pour la consultation imm�diate. Obligatoire">Praticien</label>
      <select name="prat_id" class="notNull ref"  style="width: 140px;">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$listPrat item=_prat}}
          <option value="{{$_prat->user_id}}" {{if $_prat->user_id == $app->user_id}} selected="selected" {{/if}}>
            {{$_prat->_view}}
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