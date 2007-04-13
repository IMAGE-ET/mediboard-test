<script type="text/javascript">

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(500, 500, "Patient");
}

function setPat( key, val ) {
  var oForm = document.patFrm;
  if (val != '') {
    oForm.patient_id.value = key;
    oForm.patNom.value = val;
  }
  oForm.submit();
}

function updatePrescriptions() {
  var iPatient_id = document.patFrm.patient_id.value;
  var url = new Url;
  url.setModuleAction("dPlabo", "httpreq_vw_prescriptions");
  url.addParam("patient_id", iPatient_id);
  url.requestUpdate('listPrescriptions', { waitingText: null });
}

function updateExamens() {
  
}

function main() {
  updatePrescriptions();
  updateExamens();
}

</script>

<table class="main">
  <tr>
    <th>
      <form name="patFrm" action="index.php" method="get">
      <table class="form">
        <tr>
          <th><label for="patNom" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label></th>
          <td class="readonly">
            <input type="hidden" name="m" value="dPlabo" />
            <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
            <input type="text" readonly="readonly" name="patNom" value="{{$patient->_view}}" />
            <button class="search" type="button" onclick="popPat()">Chercher</button>
          </td>
        </tr>
      </table>
      </form>
    </th>
    <th>
      <form name="typeListeFrm" action="index.php" method="get">
      <table class="form">
        <tr>
          <th><label for="typeListe" title="Choissisez le mode d'affichage des examens">Examens à afficher</label></th>
          <td class="readonly">
            <input type="hidden" name="m" value="dPlabo" />
            <select name="typeListe" onchange="this.form.submit()">
              <option value="pack">par packs</option>
              <option value="cat" {{if $typeListe == "cat"}}selected="selected"{{/if}}>par catalogues</option>
            </select>
          </td>
        </tr>
      </table>
      </form>
    </th>
  </tr>
  <tr>
    <td class="halfPane" id="listPrescriptions">
    </td>
    <td class="halfPane" id="listExamens">
    </td>
  </tr>
</table>