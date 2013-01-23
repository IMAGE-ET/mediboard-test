<table class="tbl">
  {{if $dialog}}
  <tr>
    <th colspan="4" class="title">
      {{if $target}}
        Identifiants pour '{{$target->_view}}'
      {{else}}
        Identifiants
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
      <th>{{tr}}CIdSante400-_type{{/tr}}</th>
  </tr>
  
  {{assign var=href value="?m=sante400&$actionType=$action&dialog=$dialog"}}
  
  {{foreach from=$idexs item=_idex}}
    <tr {{if $_idex->_id == $idSante400_id}}class="selected"{{/if}}>
      {{if !$dialog}}
        <td>{{$_idex->object_class}}</td>
        <td>{{$_idex->object_id}}</td>
        <td>
          {{assign var="object" value=$_idex->_ref_object}}
          {{if $object->_id}}
            <a href="#1" onclick="this.up('tr').addUniqueClassName('selected'); editId400('{{$_idex->_id}}');">
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
        <a href="#1" onclick="this.up('tr').addUniqueClassName('selected'); editId400('{{$_idex->_id}}');">
          {{$_idex->last_update|date_format:$conf.datetime}}
        </a>
      </td>
      <td>{{$_idex->id400}}</td>
      <td>{{$_idex->tag}}</td>
      <td>
        {{if $_idex->_type}}
        <span class="idex-special idex-special-{{$_idex->_type}}">
          {{$_idex->_type}}
        </span>
        {{/if}}
     </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6" class="empty">
        {{tr}}CIdSante400.none{{/tr}}

      </td>
    </tr>
  {{/foreach}}
</table>
