<!-- $Id$ -->

<script language="JavaScript" type="text/javascript">

function affNaissance() {
  var oForm = document.find;
  var bNaissance = oForm.check_naissance.checked;
  oForm.naissance.value = bNaissance ? "on" : "off";
  Element.toggle(oForm.Date_Day, oForm.Date_Month, oForm.Date_Year);

}

function chgSoundex() {
  var oForm = document.find;
  oForm.soundex.value = oForm.check_soundex.value ? "on" : "off";
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