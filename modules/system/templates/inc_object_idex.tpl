{{mb_script module=dPsante400 script=Idex ajax=true}}

{{if array_key_exists('dPsante400', $modules) && $modules.dPsante400->_can->read}}
  <a style="float: right;" href="#1" title=""
     onclick="Idex.edit('{{$object->_guid}}', '{{$tag}}')"
     onmouseover="ObjectTooltip.createEx(this,'{{$object->_guid}}', 'identifiers')">
    <span class="texticon texticon-idext">ID</span>
  </a>
{{/if}}