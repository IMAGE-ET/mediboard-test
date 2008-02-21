<script type="text/javascript">

Main.add(function() {
  var oForm = document.Imeds_patient_form;
  oForm.submit();
} );

</script>


<form target="Imeds-patient" name="Imeds_patient_form" action="{{$url}}?ctyp=p&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;dpat={{$patient->_IPP}}" method="post">
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
