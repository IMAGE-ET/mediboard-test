<script type="text/javascript">
function showSummary(patient_id) {
  var url = new Url("dPcabinet", "vw_resume");
  url.addParam("patient_id", patient_id);
  url.popup(800, 500, "Resume");
}
</script>

<!-- Dossier complet -->
<button class="edit notext" type="button" onclick="window.location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}'">
  {{tr}}Modify{{/tr}}
</button>

<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$patient->_view}}</span>
<br />
Age : {{$patient->_age}} ans

<hr />

<a class="button search" href="{{$patient->_dossier_cabinet_url}}">
  Dossier complet
</a>

<!-- Dossier résumé -->
<button class="search" onclick="showSummary('{{$patient->_id}}')">
  {{tr}}Summary{{/tr}}
</button>