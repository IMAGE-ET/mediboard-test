{{if @$modules.dPhospi->_can->read && $conf.dPhospi.show_uf}}
  {{assign var="unitefonctionnelle" value=$object}}

  {{mb_script module=hospi script=affectation_uf}}

  <a style="float: right;" href="#1" title=""
    onclick="AffectationUf.edit('{{$object->_guid}}')"
    onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'objectUFs')">
    <img src="images/icons/uf.png" width="16" height="16" />
  </a>
{{/if}}