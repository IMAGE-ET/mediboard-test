{{if !$app->_is_intranet}}
<div class="big-info">
Pour des raisons de sécurité, l'affichage des résultats de laboratoire n'est pas disponible depuis l'accès distant.
<br />
Merci de réessayer ultérieurement depuis un accès sur site.
</div>

{{else}}
<script type="text/javascript">

Main.add(function() {
  var oForm = document.Imeds_sejour_form;
  oForm.submit();
} );

</script>

<form target="Imeds-sejour" name="Imeds_sejour_form" action="{{$url}}?nameframe=Imeds-sejour&amp;ctyp=s&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;ndos={{$sejour->_num_dossier}}" method="post">
  <input type="hidden" name="login" value="{{$idImeds.login}}" />
  <input type="hidden" name="password" value="{{$idImeds.password}}" />
</form>

<iframe 
  id="Imeds-sejour" 
  name="Imeds-sejour" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%" 
  >
  Serveur de résultats indisponible pour le séjour '{{$sejour->_view}}'
</iframe>
{{/if}}