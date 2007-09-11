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
  {{foreach from=$list_idSante400 item=_idSante400}}
  <tr {{if $_idSante400->_id == $idSante400->_id}}class="selected"{{/if}}>
    {{if !$dialog}}
    <td>{{$_idSante400->object_class}}</td>
    <td>{{$_idSante400->object_id}}</td>
    <td>
      {{assign var="object" value=$_idSante400->_ref_object}}
      {{if $object->_id}}
      <div onmouseover="ObjectTooltip.create(this, '{{$_idSante400->object_class}}', {{$_idSante400->object_id}})">
        {{$object->_view}}
      </div>
      {{else}}
      <div class="warning">Objet supprimé</div>
      {{/if}}
    </td>
    {{/if}}
    <td>
      <a href="?m=dPsante400&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id={{$_idSante400->_id}}" >
        {{$_idSante400->last_update|date_format:"%d/%m/%Y à %Hh%M"}}
      </a>
    </td>
    <td>{{$_idSante400->id400}}</td>
    <td>{{$_idSante400->tag|replace:' ':'<br/>'}}</td>
  </tr>
  {{/foreach}}
</table>
