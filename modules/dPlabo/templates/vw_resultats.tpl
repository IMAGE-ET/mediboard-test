<script type="text/javascript">

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(600, 500, "Patient");
}

function setPat( key, val ) {
  var oForm = document.Patient;
  if (val != '') {
    oForm.patient_id.value = key;
    oForm._view.value = val;
  }
  oForm.submit();
}


var Prescription = {
  select : function(prescription_id) {
  }
}

</script>

<table class="main">
  <tr>
  
    <td class="halfPane">
      <form name="Patient" action="?" method="get">

      <input type="hidden" name="m" value="dPlabo" />
      <input type="hidden" name="patient_id" value="{{$patient->_id}}" />

      <table class="form">
        <tr>
          <th>
            <label for="_view" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label>
          </th>
          <td class="readonly">
            <input type="text" readonly="readonly" name="_view" value="{{$patient->_view}}" />
            <button class="search" type="button" onclick="popPat()">Chercher</button>
          </td>
        </tr>
      </table>

      </form>

    </td>
    
    {{if $patient->_id}}
    <td class="halfPane">
      
      <form name="Prescription" action="?" method="get">

      <input type="hidden" name="m" value="dPlabo" />
      
      <table class="form">
        <tr>
          <th>
            <label for="prescription_id" title="Merci de choisir une prescription à afficher">Prescription</label>
          </th>
          <td>
            <select name="prescription_id" onchange="this.form.submit()">
              <option value="">&mdash; Choisir une prescription</option>
              {{foreach from=$patient->_ref_prescriptions item="_prescription"}}
              <option value="{{$_prescription->_id}}" {{if $_prescription->_id == $prescription->_id}}selected="selected"{{/if}}>
                {{$_prescription->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      
      </form>

    </td>
    {{/if}}
    
  </tr>
  
</table>