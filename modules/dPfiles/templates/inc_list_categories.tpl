<table class="tbl">
  <tr>
    <th>
      <button class="merge notext button">fusion</button>
    </th>
    <th>{{mb_title class=CFilesCategory field=nom}}</th>
    <th>{{mb_title class=CFilesCategory field=class}}</th>
    <th>{{mb_title class=CFilesCategory field=send_auto}}</th>
  </tr>

  {{foreach from=$categories item=_category}}
    <tr>
      <td class="narrow">
        <input type="checkbox" name="select_{{$_category->_id}}"/>
      </td>
      <td class="text">
        <a href="#" onclick="Category.edit('{{$_category->_id}}');">
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