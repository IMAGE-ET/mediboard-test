{{* $Id$ *}}

<script type="text/javascript">
  // Explicit form preparation for Ajax loading
  prepareForm(document.addEditPrescription);
</script>

<form name="addEditPrescription" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPlabo" />
<input type="hidden" name="dosql" value="do_prescription_aed" />
<input type="hidden" name="prescription_labo_id" value="{{$prescription->_id}}" />
<input type="hidden" name="callback" value="Prescription.select" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if !$prescription->_id}}
    <th class="title" colspan="2">
      Création d'une prescription
    </th>
    {{else}}
    <th class="title modify" colspan="2">
      Modification de {{$prescription->_view}}
    </th>
    {{/if}}
  </tr>
  <tr>
    <th>{{mb_label object=$prescription field="patient_id"}}</th>
    <td>{{mb_field object=$prescription field="patient_id" hidden="hidden"}}{{$prescription->_ref_patient->_view}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$prescription field="date"}}</th>
    <td>{{mb_field object=$prescription field="date" form="addEditPrescription" register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$prescription field="praticien_id"}}</th>
    <td>
      <select name="praticien_id">
        {{foreach from=$listPrats item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $prescription->praticien_id == $curr_prat->user_id}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th class="title" colspan="2">Autres Informations</th>
  </tr>
  <tr>
    <td>Urgent:</td>
    <td>
        Non<input type="radio" name="urgence" value="0" checked="checked" />
        Oui<input type="radio" name="urgence" value="1" />
    </td>
  </tr>
  <tr>
  
  </tr>
  
  
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg');">
        Valider
      </button>
    </td>
  </tr>
</table>

</form>