{{if !isset($hypertext_links|smarty:nodefaults)}}
  {{mb_default var=hypertext_links value=$object->_ref_hypertext_links}}
  {{mb_default var=object_id       value=$object->_id}}
  {{mb_default var=object_class    value=$object->_class}}
{{/if}}

<div id="list-hypertext_links">
  <hr />
  {{mb_script module=sante400 script=hyperTextLink ajax=true}}
  {{foreach from=$hypertext_links item=_hypertext_link}}
    <tr>
      <td>
        <a href="{{$_hypertext_link->link}}" target="_blank" onmouseover="ObjectTooltip.createEx(this, '{{$_hypertext_link->_guid}}')">
          {{$_hypertext_link->name}} <i class="fa fa-external-link"></i>
        </a>
      </td>
    </tr>
  {{/foreach}}
  <button type="button" class="add notext" style="float: right" onclick="HyperTextLink.edit('{{$object_id}}', '{{$object_class}}', 0, 1)">Nouveau</button>
</div>