{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_script module=compteRendu script=document ajax=1}}

{{mb_script module=compteRendu script=document}}{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=document value=$object}}

<table class="tbl">
  <tr>
    <th class="title text">
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      {{$object}}
    </th>
  </tr>
</table>

<table class="tbl">
  <tbody class="hoverable">
  <tr>
    {{assign var=file value=$document->_ref_file}}
    {{if $document->object_id && $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs && $file->_id}}
    <td id="thumbnail-{{$document->_id}}" style="text-align: center;">
     <a href="#1" onclick="new Url().ViewFilePopup('{{$document->object_class}}', '{{$document->object_id}}', 'CCompteRendu', '{{$document->_id}}')">
        <img class="thumbnail" style="width: 64px; height: 92px; background: white;" src="?m=files&a=fileviewer&suppressHeaders=1&file_id={{$file->_id}}&phpThumb=1&w=64&h=92" />
     </a>
      <br />
      {{mb_value object=$file field=_file_size}}
    </td>

    {{else}}
    <td>
      <img src="images/pictures/medifile.png" />
    </td>
    {{/if}}
  
    <td rowspan="2" style="vertical-align: top;">
      {{foreach from=$object->_specs key=prop item=spec}}
        {{mb_include module=system template=inc_field_view}}
      {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <td class="button">
      <strong>{{$document->_source|count_words}} {{tr}}CCompteRendu-words{{/tr}}</strong>
      <br/>
      {{if $document->_can->edit}}
        {{if !$document->object_id}}
          <a class="button search" href="?m=compteRendu&tab=addedit_modeles&compte_rendu_id={{$document->_id}}">
            {{tr}}Open{{/tr}}
          </a>
        {{else}}
          <button type="button" class="search" onclick="Document.edit('{{$document->_id}}')">{{tr}}Open{{/tr}}</button>
        {{/if}}
      {{/if}}
    </td>
  </tr>
  </tbody>
</table>