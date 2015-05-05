{{if @$modules.dPhospi->_can->read && $conf.dPhospi.show_uf}}
  {{assign var="unitefonctionnelle" value=$object}}

  {{mb_script module=hospi script=affectation_uf ajax=true}}

  <a style="float: right;" href="#1"
     onclick="AffectationUf.edit('{{$object->_guid}}')"
     onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'objectUFs')">
    <span class="texticon texticon-uf" title="Affecter les UF">UF</span>
  </a>
{{/if}}