{{assign var="file" value=$object}}
<table class="tbl">
  <tr>
    <td style="text-align: center;">
      <div style="width: 64px;"> 
        <img class="thumbnail" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->_id}}&amp;phpThumb=1&amp;w=64" />
      </div>
    </td>
    <td>
      {{include file=CMbObject_view.tpl}}
    </td>
  </tr>
</table>