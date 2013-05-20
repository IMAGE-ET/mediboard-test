{{foreach from=$list item=_doc_item}}
  <tr>
    <td class="{{cycle name=cellicon values="dark, light"}}">
      {{assign var="elementId" value=$_doc_item->_id}}
      {{if $_doc_item->_class=="CCompteRendu"}}
        {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
          {{assign var="file_id" value=$_doc_item->_ref_file->_id}}
          {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$file_id&phpThumb=1&w=64"}}
        {{else}}
          {{assign var="srcImg" value="images/pictures/medifile.png"}}
        {{/if}}
      {{else}}
        {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&wl=64&hp=64"}}
      {{/if}}
      
      <a href="#" onclick="ZoomAjax('{{$object->_class}}', '{{$object->_id}}', '{{$_doc_item->_class}}', '{{$elementId}}', '0');" title="Afficher l'aperçu">
        <img src="{{$srcImg}}" alt="-" width="64"/>
      </a>

    </td>
    <td class="text {{cycle name=celltxt values="dark, light"}}" style="vertical-align: middle;">
      <strong>
        {{$_doc_item}}
        {{if $_doc_item->private}}
          &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
        {{/if}}
      </strong>
      <hr />
      {{mb_include module=files template=inc_file_toolbar notext=notext}}
    </td>
  </tr>
{{foreachelse}}
<tr>
  <td colspan="2" class="button">
    {{tr}}CDocument-none{{/tr}}            
  </td>
</tr>
{{/foreach}}