{{mb_default var=all_content value=0}}
<table class="tbl">
  <tr>
    {{if $all_content}}
      <th class="category text">
        {{tr}}CTransmissionMedicale-object_id{{/tr}}
      </th>
    {{/if}}
    <th class="category text">
      {{tr}}CTransmissionMedicale-user_id{{/tr}}
    </th>
    <th class="category text">
      {{tr}}CTransmissionMedicale-date{{/tr}}
    </th>
    <th class="category text">
      {{tr}}CTransmissionMedicale._heure{{/tr}}
    </th>
    <th class="category">
      {{tr}}CTransmissionMedicale-text{{/tr}}
    </th>
  </tr>
  <tbody>
    {{foreach from=$transmissions item=_transmission}}
      <tr>
        {{if $all_content}}
          <td>
            {{if $_transmission->_ref_object instanceof CAdministration}}
              Administration le {{$_transmission->_ref_object->dateTime|date_format:"%d/%m/%Y"}} à {{$_transmission->_ref_object->dateTime|date_format:"%Hh%M"}}

            {{else}}
              {{$_transmission->_ref_object}}
            {{/if}}
          </td>
        {{/if}}
        <td>
          {{$_transmission->_ref_user}}
        </td>
        <td>
          {{mb_ditto name=date value=$_transmission->date|date_format:$conf.date}}
        </td>
        <td>
          {{$_transmission->date|date_format:$conf.time}}
        </td>
        <td class="text {{if $_transmission->type}}trans-{{$_transmission->type}}{{/if}} libelle_trans" {{if $_transmission->degre == "high"}} style="background-color: #faa" {{/if}}>
          {{if !$all_content}}
            <button class="add notext" type="button" data-text="{{$_transmission->text}}" onclick="completeTrans('{{$_transmission->type}}',this);" style="float: right;">{{tr}}Add{{/tr}}</button>
          {{/if}}
          {{mb_value object=$_transmission field=text}}
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="4" class="empty">
          {{tr}}CTransmissionMedicale.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  </tbody>
</table>

