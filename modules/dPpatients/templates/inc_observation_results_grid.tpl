{{mb_default var=in_compte_rendu value=false}}

<table class="main tbl print">
  {{if !$in_compte_rendu}}
    <tr>
      <th class="title" colspan="20">Constantes pérop</th>
    </tr>
  {{/if}}

  <tr>
    <th>{{mb_title class=CObservationResultSet field=datetime}}</th>

    {{foreach from=$observation_labels item=_label}}
      <th>{{$_label}}</th>
    {{/foreach}}
  </tr>
  {{foreach from=$observation_grid item=_row key=_datetime name=_observation_grid}}
    <tr>
      <td class="narrow" style="white-space: nowrap;">
        {{$_datetime|date_format:$conf.datetime}}<br />

        {{if !$in_compte_rendu}}
          {{assign var=_obr value=$observation_list.$_datetime}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_obr->_ref_first_log->_ref_user->_ref_mediuser}}
        {{/if}}
      </td>

      {{foreach from=$_row item=_cell}}
        <td>
          {{if $_cell}}
            {{if $_cell->file_id}}
              {{if !$in_compte_rendu}}
              <img style="width: 50px;"
                   src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$_cell->file_id}}&amp;phpThumb=1&amp;w=100&amp;q=95" />
              {{else}}
                <img style="width: 50px;" src="{{$_cell->_ref_file->_data_uri}}" />
              {{/if}}
            {{elseif $_cell->label_id}}
              {{mb_value object=$_cell field=label_id}}
            {{else}}
              {{$_cell->value}}
              {{if $_cell->unit_id}}
                {{$_cell->_ref_value_unit->label}}
              {{/if}}
            {{/if}}
          {{/if}}
        </td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>