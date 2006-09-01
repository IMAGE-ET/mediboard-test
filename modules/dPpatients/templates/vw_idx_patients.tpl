<!-- $Id$ -->

<script language="JavaScript" type="text/javascript">

function affNaissance() {
  var oForm      = document.find;
  var oCheckNaissance = oForm.check_naissance;
  var oNaissance = oForm.naissance;
  var oDay       = oForm.Date_Day;
  var oMonth     = oForm.Date_Month;
  var oYear      = oForm.Date_Year;
  if (oCheckNaissance.checked) {
    oDay.style.display   = "inline";
    oMonth.style.display = "inline";
    oYear.style.display  = "inline";
    oNaissance.value     = "on";
  } else {
    oDay.style.display   = "none";
    oMonth.style.display = "none";
    oYear.style.display  = "none";
    oNaissance.value     = "off";
  }
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      {{include file="inc_list_patient.tpl"}}
    </td>

    {{if $patient->patient_id}}
    <td class="halfPane" id="vwPatient">
      {{include file="inc_vw_patient.tpl"}}
    </td>
    {{/if}}
  </tr>
</table>