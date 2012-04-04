<tr>
  <th style="width: 20%;"><strong>{{tr}}{{$object->_class}}{{/tr}}</strong></th>
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
