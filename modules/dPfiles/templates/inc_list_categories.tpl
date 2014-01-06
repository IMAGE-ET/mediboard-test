<table class="tbl" id="list_file_categories">
  <tr>
    {{if $can->admin}}
      <th>
        <button class="merge notext button" onclick="FilesCategory.mergeSelected()">fusion</button>
      </th>
    {{/if}}
    <th {{if !$can->admin}}colspan="2"{{/if}}>{{mb_title class=CFilesCategory field=nom}}</th>
    <th>{{mb_title class=CFilesCategory field=class}}</th>
    <th>{{mb_title class=CFilesCategory field=send_auto}}</th>
  </tr>

  {{foreach from=$categories item=_category}}
    <tr id="category_line_{{$_category->_id}}">
      {{if $can->admin}}
        <td class="narrow" style="text-align: center;">
          <input type="checkbox" name="select_{{$_category->_id}}" data-id="{{$_category->_id}}" onclick="FilesCategory.checkMergeSelected(this)"/>
        </td>
        <td class="text">
      {{else}}
        <td class="text" colspan="2">
      {{/if}}
        <a href="#" onclick="FilesCategory.edit('{{$_category->_id}}');">
          {{mb_value object=$_category field=nom}}
        </a>
      </td>
      <td {{if !$_category->class}} class="empty" {{/if}}>
        {{tr}}{{$_category->class|default:'All'}}{{/tr}}
      </td>
      <td>{{mb_value object=$_category field=send_auto}}</td>
    </tr>
  {{/foreach}}        
</table>  