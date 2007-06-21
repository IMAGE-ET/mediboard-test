<script type="text/javascript">

function popPat() {
  var url = new Url();
  url.setModuleAction("dPpatients", "pat_selector");
  url.popup(750, 500, "Patient");
}

function setPat(key, val) {
  var oForm = document.patFrm;
  if (val != '') {
    oForm.patient_id.value = key;
    oForm._view.value = val;
  }
  oForm.submit();
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="patFrm" action="index.php" method="get">
      <table class="form">
        <tr>
          <th><label for="_view" title="Merci de choisir un patient pour voir ses résultats">Choix du patient</label></th>
          <td class="readonly">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
            <input type="text" readonly="readonly" name="_view" value="{{$patient->_view}}" />
          </td>
          <td class="button">
            <button class="search" type="button" onclick="popPat()">Chercher</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <form name="sejourFrm" action="index.php" method="get">
      <table class="form">
        <tr>
          <th><label for="_view" title="Merci de choisir un sejour pour voir ses résultats">Choix du sejour</label></th>
          <td>
            <input type="hidden" name="m" value="{{$m}}" />
            <select name="sejour_id" onchange="this.form.submit()">
              <option value="">&mdash; séjours disponibles</option>
              {{foreach from=$patient->_ref_sejours item=curr_sejour}}
              <option value="{{$curr_sejour->sejour_id}}" {{if $curr_sejour->sejour_id == $sejour->sejour_id}}selected="selected"{{/if}}>
                {{$curr_sejour->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td>
      {{if $patient400}}
      {{include file="inc_patient_results.tpl"}}
      {{/if}}
    </td>
    <td>
      {{if $sejour400}}
      {{include file="inc_sejour_results.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>