<tr>
  <th style="width: 20%;">
    <strong>
      {{if $object->_class != "CMediusers"}}
        {{tr}}{{$object->_class}}{{/tr}}
      {{else}}
        {{$name}}
      {{/if}}
    </strong>
  </th>
  <th style="width: 20%;">{{mb_value object=$object}}</th>
            
  <td>
    {{foreach from=$ufs item=_uf name=ufs}}                 
      {{$_uf}}
      {{if !$smarty.foreach.ufs.last}}&mdash;{{/if}}
    {{foreachelse}}
    <div class="empty">{{tr}}CUniteFonctionnelle.none{{/tr}}</div>
    {{/foreach}}
  </td>
</tr>
