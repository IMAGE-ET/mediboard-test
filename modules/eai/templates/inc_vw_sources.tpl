{{foreach from=$_sources name=boucle_source item=_source}}
  <tr {{if !$_source->active}}class="opacity-30"{{/if}}>
    <td class="text compact">
      <button title="Modifier {{$_source->_view}}" class="edit notext"
              onclick="editSource('{{$_source->_guid}}', '{{$name}}')"
              style="float: left">
        Modifier {{$_source->name}}
      </button>

      {{$_source->name}}
    </td>
    <td class="text compact">
      <em>{{$_source->libelle}}</em>

      {{if $_source instanceof CSourcePOP}}
        <br /><br />
        <strong>{{mb_label object=$_source field="object_id"}} :</strong>

        {{if $_source->_ref_mediuser}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_source->_ref_mediuser}}
        {{else}}
          <input type="text" readonly="readonly" name="_object_view"
                 value="{{$_source->_ref_metaobject->_view}}" size="50"/>
        {{/if}}
      {{/if}}
    </td>
    <td class="narrow">
      {{unique_id var=uid}}
      <img class="status" id="{{$uid}}" data-id="{{$_source->_id}}"
           data-guid="{{$_source->_guid}}" src="images/icons/status_grey.png"
           title="{{$_source->name}}"/>
    </td>
    <td class="narrow">
      {{$_source->_response_time}}
    </td>
    <td class="text compact">
      {{$_source->_message|smarty:nodefaults}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="5" class="empty">
      {{tr}}{{$name}}.none{{/tr}}
    </td>
  </tr>
{{/foreach}}