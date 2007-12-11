<iframe 
  src="{{$url}}?ctyp=s&amp;cidc={{$idImeds.cidc}}&amp;cdiv={{$idImeds.cdiv}}&amp;csdv={{$idImeds.csdv}}&amp;ndos={{$sejour->_num_dossier}}" 
  id="Imeds-sejour" 
  name="Imeds-sejour" 
  onload="ViewPort.SetFrameHeight(this)"
  width="100%" 
  >
  Serveur de résultats indisponible pour le séjour '{{$sejour->_view}}'
</iframe>