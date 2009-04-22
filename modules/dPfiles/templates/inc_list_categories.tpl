<a class="button new" href="?m={{$m}}&amp;tab=vw_category&amp;file_category_id=0">
  {{tr}}CFilesCategory-title-create{{/tr}}
</a>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CFilesCategory field=nom}}</th>
    <th>{{mb_title class=CFilesCategory field=class}}</th>
    <th>{{mb_title class=CFilesCategory field=validation_auto}}</th>
  </tr>

  {{foreach from=$categories item=_category}}
  <tr {{if $_category->_id == $category->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="?m={{$m}}&amp;tab=vw_category&amp;file_category_id={{$_category->_id}}">
				{{mb_value object=$_category field=nom}}
      </a>
    </td>

    <td>{{tr}}{{$_category->class|default:'All'}}{{/tr}}</td>
    <td>{{mb_value object=$_category field=validation_auto}}</td>

  </tr>
  {{/foreach}}        
</table>  