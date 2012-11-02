{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include module=system template=CMbObject_view}}

<table class="tbl">
  <tr>
    <th class="category">Naissances</th>
  </tr>
  {{foreach from=$object->_ref_naissances item=_naissance}}
    {{assign var=sejour value=$_naissance->_ref_sejour_enfant}}
    {{assign var=patient value=$sejour->_ref_patient}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient}}
        </span> né(e) le
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_naissance->_guid}}')">
          {{mb_value object=$patient field=naissance}}
        </span>
      </td>
    </tr>
  {{/foreach}}
</table>