{{if !$url}}
{{include file=inc_nourl_notices.tpl}}
{{else}}

<script type="text/javascript">

Main.add(function() {
  var oForm = document.Imeds_prescription_form;
  oForm.submit();
} );

</script>

<form target="Imeds-prescription" name="Imeds_prescription_form" action="{{$url}}?nameframe=Imeds-prescription&amp;ctyp=s&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;ndos={{$numPresc}}" method="get">
  <input type="hidden" name="login" value="{{$idImeds.login}}" />
  <input type="hidden" name="password" value="{{$idImeds.password}}" />
</form>

<iframe 
  id="Imeds-prescription" 
  name="Imeds-prescription" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%" 
  >
  Serveur de résultats indisponible pour la prescription '{{$prescription->_view}}'
</iframe>
{{/if}}