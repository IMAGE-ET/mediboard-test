{{assign var="unitefonctionnelle" value=$object}}

{{mb_script module=dPhospi script=affectation_uf}}

<a style="float: right;" href="#1" title=""
  onclick="AffectationUf.edit('{{$object->_guid}}')"  
  onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'objectUFs')">
  <img src="images/icons/uf.png" width="16" height="16" />
</a>
