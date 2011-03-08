
<table class="main tbl">
  <tr>
    <th colspan="4" class="title">
      {{tr}}CExList-back-list_items{{/tr}}
      
      <a class="button edit" href="?m=forms&amp;tab=view_ex_list&amp;object_guid={{$list_owner->_guid}}">
        {{tr}}CExList-title-modify{{/tr}}
      </a>
    </th>
  </tr>
	
  <tr>
    {{if $context instanceof CExClassField}}
      <th class="narrow"></th>
    {{/if}}
    <th class="narrow code">
      {{mb_title class=CExListItem field=code}}
    </th>
    <th>
      {{mb_title class=CExListItem field=name}}
    </th>
  </tr>
  
  {{foreach from=$list_owner->_back.list_items item=_item}}
    <tr data-id="{{$_item->_id}}" data-name="{{$_item->name}}" data-code="{{$_item->code}}">
      {{if $context instanceof CExClassField}}
      <td>
        <button class="trash notext" type="button" style="margin: -1px;" onclick="editListItem($(this).up('tr'))">
          {{tr}}Delete{{/tr}}
        </button>
      </td>
      {{/if}}
      <td class="code">{{mb_value object=$_item field=code}}</td>
      <td>{{mb_value object=$_item field=name}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="3">{{tr}}CExListItem.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

