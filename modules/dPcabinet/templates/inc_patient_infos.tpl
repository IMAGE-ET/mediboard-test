<script>
  function showSummary(patient_id) {
    var url = new Url('cabinet', 'vw_resume');
    url.addParam("patient_id", patient_id);
    url.popup(800, 500, 'Summary' + (Preferences.multi_popups_resume == '1' ? patient_id : null));
  }
</script>

<!-- Dossier complet -->
<a class="button search" href="{{$patient->_dossier_cabinet_url}}">
  Dossier complet
</a>

<!-- Dossier résumé -->
<button class="search" onclick="showSummary('{{$patient->_id}}')">
  Résumé
</button>
