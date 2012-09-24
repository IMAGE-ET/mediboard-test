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
    <th class="narrow">{{tr}}CIdSante400-last_update{{/tr}}</th>
    <th>{{tr}}CIdSante400-id400-court{{/tr}}</th> 
    <th>{{tr}}CIdSante400-tag{{/tr}}</th>
  </tr>
  
  {{assign var=href value="?m=sante400&$actionType=$action&dialog=$dialog"}}
  
  {{if $list_idSante400|@count}}
  <tr>
    <th colspan="6">
      <em>
        {{$list_idSante400|@count}} identifiants 
        {{if $list_idSante400|@count != $count_idSante400}}
        sur {{$count_idSante400}}
        {{/if}}
        trouvés
      </em>
    </th>
  </tr>
  {{/if}}
  
  {{foreach from=$list_idSante400 item=_idSante400}}
  <tr {{if $_idSante400->_id == $idSante400_id}}class="selected"{{/if}}>
    {{if !$dialog}}
    <td>{{$_idSante400->object_class}}</td>
    <td>{{$_idSante400->object_id}}</td>
    <td>
      {{assign var="object" value=$_idSante400->_ref_object}}
      {{if $object->_id}}
      <a href="#1" onclick="this.up('tr').addUniqueClassName('selected'); editId400('{{$_idSante400->_id}}');">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
        {{$object}}
      </span>
      </a>
      {{else}}
      <div class="warning">Objet supprimé</div>
      {{/if}}
    </td>
    {{/if}}
    <td>
      <a href="#1" onclick="this.up('tr').addUniqueClassName('selected'); editId400('{{$_idSante400->_id}}');">
        {{$_idSante400->last_update|date_format:$conf.datetime}}
      </a>
    </td>
    <td>{{$_idSante400->id400}}</td>
    <td>{{$_idSante400->tag}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="6" class="empty">
      {{tr}}CIdSante400.none{{/tr}}

    </td>
  </tr>
  {{/foreach}}
</table>
