{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}

<form name="Edit-CPack" action="?" method="post" onsubmit="return onSubmitFormAjax(this)" class="{{$pack->_spec}}">
{{mb_class object=$pack}}
{{mb_key   object=$pack}}

{{if (!$pdf_thumbnails || !$pdf_and_thumbs)}}
<input type="hidden" name="fast_edit_pdf" value="{{$pack->fast_edit_pdf}}" />
{{/if}}

<table class="form">
  {{mb_include module=system template=inc_form_table_header object=$pack}}
  
  <tr>
    <th style="width: 40%;">{{mb_label object=$pack field=user_id}}</th>
    <td style="width: 60%;">
      <select name="user_id" class="{{$pack->_props.user_id}}" style="width: 16em;" onchange="reloadListModele(this);">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$pack->user_id}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$pack field=function_id}}</th>
    <td>
      <select name="function_id" class="{{$pack->_props.function_id}}" style="width: 16em;" onchange="reloadListModele(this);">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_function list=$functions selected=$pack->function_id}}
      </select>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$pack field=group_id}}</th>
    <td>
      <select name="group_id" class="{{$pack->_props.group_id}}" style="width: 16em;" onchange="reloadListModele(this);">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$groups item=_group}}
          <option value="{{$_group->_id}}" {{if $_group->_id == $pack->group_id}} selected="selected" {{/if}}>
            {{$_group}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$pack field=nom}}</th>
    <td>{{mb_field object=$pack field=nom style="width: 16em;"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$pack field=object_class}}</th>
    <td>
      <select name="object_class" style="width: 16em;" {{if $pack->_id}} onchange="Pack.changeClass(this);" {{/if}}>
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$pack->_specs.object_class->_list item=object_class}}
          <option value="{{$object_class}}" {{if $object_class == $pack->object_class}} selected = "selected" {{/if}}>
            {{tr}}{{$object_class}}{{/tr}}  
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$pack field=merge_docs}}</th>
    <td>{{mb_field object=$pack field=merge_docs}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$pack field=fast_edit}}</th>
    <td>{{mb_field object=$pack field=fast_edit}}</td>
  </tr>
  
  {{if $pdf_thumbnails && $pdf_and_thumbs}}
  <tr>
    <th>{{mb_label object=$pack field=fast_edit_pdf}}</th>
    <td>{{mb_field object=$pack field=fast_edit_pdf canNull=false}}</td>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="2">
      {{if $pack->_id}}
      <button class="modify" type="submit">
        {{tr}}Save{{/tr}}
      </button>
      <button class="trash" type="button" onclick="Pack.confirmDeletion(this);">
        {{tr}}Delete{{/tr}}
      </button>
      {{else}}
      <button class="submit" type="submit">
        {{tr}}Create{{/tr}}
      </button>
      {{/if}}
    </td>
  </tr>

</table>
  
</form>

