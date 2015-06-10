{{mb_include module=system template=inc_pagination total=$total_categories current=$page change_page='FilesCategory.changePage'}}

<table class="tbl" id="list_file_categories">
  <tr>
    {{if $can->admin}}
      <th>
        <button class="merge notext button" onclick="FilesCategory.mergeSelected()">fusion</button>
      </th>
    {{/if}}
    <th></th>
    <th {{if !$can->admin}}colspan="2"{{/if}}>{{mb_title class=CFilesCategory field=nom}}</th>
    <th>{{mb_title class=CFilesCategory field=class}}</th>
    <th>{{mb_title class=CFilesCategory field=send_auto}}</th>
    <th>{{mb_title class=CFilesCategory field=eligible_file_view}}</th>
  </tr>

  {{foreach from=$categories item=_category}}
    <tr id="category_line_{{$_category->_id}}">
      {{if $can->admin}}
        <td class="narrow" style="text-align: center;">
          <input type="checkbox" name="select_{{$_category->_id}}" data-id="{{$_category->_id}}" onclick="FilesCategory.checkMergeSelected(this)"/>
        </td>
      {{/if}}
      <td class="narrow button">
        <button class="edit notext" onclick="FilesCategory.edit('{{$_category->_id}}', this);">{{tr}}Edit{{/tr}}</button>
      </td>
      <td class="text">
        {{mb_value object=$_category field=nom}}
      </td>
      <td {{if !$_category->class}} class="empty" {{/if}}>
        {{tr}}{{$_category->class|default:'All'}}{{/tr}}
      </td>
      <td>{{mb_value object=$_category field=send_auto}}</td>
      <td>{{mb_value object=$_category field=eligible_file_view}}</td>
    </tr>
  {{/foreach}}        
</table>  