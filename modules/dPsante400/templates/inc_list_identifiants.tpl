<table class="tbl">
  {{if $dialog}}
  <tr>
    <th colspan="4" class="title">
      {{if $target}}
      Identifiants 400 pour '{{$target->_view}}'
      {{else}}
      Identifiants 400
      {{/if}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    {{if !$dialog}}
    <th>{{tr}}CIdSante400-object_class{{/tr}}</th>  
    <th>{{tr}}CIdSante400-object_id-court{{/tr}}</th> 
    <th>{{tr}}CIdSante400-object{{/tr}}</th>
    {{/if}}
    <th>{{tr}}CIdSante400-last_update{{/tr}}</th>
    <th>{{tr}}CIdSante400-id400-court{{/tr}}</th> 
    <th>{{tr}}CIdSante400-tag{{/tr}}</th>
  </tr>
  
  {{assign var=href value="?m=dPsante400&$actionType=$action&dialog=$dialog"}}
  
  {{foreach from=$list_idSante400 item=_idSante400}}
  <tr {{if $_idSante400->_id == $idSante400->_id}}class="selected"{{/if}}>
    {{if !$dialog}}
    <td>{{$_idSante400->object_class}}</td>
    <td>{{$_idSante400->object_id}}</td>
    <td>
      {{assign var="object" value=$_idSante400->_ref_object}}
      {{if $object->_id}}
      <a href="{{$href}}&amp;object_class={{$object->_class_name}}&amp;object_id={{$object->_id}}">
      <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$object->_class_name}}', object_id: {{$object->_id}} } })">
        {{$object->_view}}
      </span>
      </a>
      {{else}}
      <div class="warning">Objet supprimé</div>
      {{/if}}
    </td>
    {{/if}}
    <td>
      <a href="{{$href}}&amp;id_sante400_id={{$_idSante400->_id}}" >
        {{$_idSante400->last_update|date_format:$dPconfig.datetime}}
      </a>
    </td>
    <td>{{$_idSante400->id400}}</td>
    <td>{{$_idSante400->tag|replace:' ':'<br/>'}}</td>
  </tr>
  {{/foreach}}
</table>
