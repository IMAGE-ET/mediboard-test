{{mb_script module=compteRendu script=document ajax=1}}

{{mb_script module=compteRendu script=document}}{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var="document" value=$object}}
{{if !$document->object_id}}
<table class="tbl">
  <tr>
    <th class="title">{{tr}}CCompteRendu-modele-one{{/tr}}</th>
  </tr>
</table>
{{/if}}

<table class="tbl">
  <tr>
  	{{assign var=file value=$document->_ref_file}}
    {{if $document->object_id && $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs && $file->_id}}
    <td id="thumbnail-{{$document->_id}}" style="text-align: center;">
     <a href="#1" onclick="new Url().ViewFilePopup('{{$file->object_class}}', '{{$file->object_id}}', 'CFile', '{{$file->_id}}')">
        <img class="thumbnail" style="width: 64px; height: 92px;"
          src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->_id}}&amp;phpThumb=1&amp;w=64&h=92" />
     </a>
      <br />
		  {{mb_value object=$file field=_file_size}}
    </td>

    {{else}}
    <td>
      <img src="images/pictures/medifile.png"/>
    </td>
    {{/if}}
  
    <td rowspan="2" style="vertical-align: top;">
      {{include file=CMbObject_view.tpl}}
		</td>
	</tr>
	
	<tr>
		<td class="button">
      <strong>{{$document->_source|count_words}} {{tr}}CCompteRendu-words{{/tr}}</strong>
      <br/>
      <button type="button" class="search" onclick="Document.edit('{{$document->_id}}')">{{tr}}Open{{/tr}}</button>
    </td>
  </tr>
</table>
