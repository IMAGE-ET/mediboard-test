{{if !$app->_is_intranet}}
<div class="big-info">
Pour des raisons de sécurité, l'affichage des résultats de laboratoire n'est pas disponible depuis l'accès distant.
<br />
Merci de réessayer ultérieurement depuis un <strong>accès sur site</strong>.
</div>

{{else}}
<script type="text/javascript">

Main.add(function() {
  var oForm = document.Imeds_patient_form;
  oForm.submit();
} );

</script>

<form target="Imeds-patient" name="Imeds_patient_form" action="{{$url}}?nameframe=Imeds-patient&amp;ctyp=p&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;dpat={{$patient->_IPP}}" method="post">
  <input type="hidden" name="login" value="{{$idImeds.login}}" />
  <input type="hidden" name="password" value="{{$idImeds.password}}" />
</form>
 
 
<iframe 
  id="Imeds-patient"
  name="Imeds-patient"
  onload="ViewPort.SetFrameHeight(this)"
  width="100%"
  >
  Serveur de résultats indisponible pour le patient '{{$patient->_view}}';
</iframe>
{{/if}}
