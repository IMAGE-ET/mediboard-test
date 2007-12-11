<iframe 
  id="Imeds-patient"
  name="Imeds-patient"
  src="{{$url}}?ctyp=p&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;dpat={{$patient->_IPP}}" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%"
  >
  Serveur de résultats indisponible pour le patient '{{$patient->_view}}';
</iframe>