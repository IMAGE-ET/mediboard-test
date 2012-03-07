{{mb_include module=cabinet template=CConsultation_view object=$object->_ref_consultation}}

<table class="tbl">
  <tr>
    <th>
      {{mb_include module=system template=inc_object_idsante400 object=$object}}
      {{mb_include module=system template=inc_object_history    object=$object}}
      {{tr}}{{$object->_class}}{{/tr}}
    </th>
  </tr>
  <tr>
    <td>
      {{foreach from=$object->_specs key=prop item=spec}}
        {{mb_include module=system template=inc_field_view}}
      {{/foreach}}
    </td>
  </tr>
</table>
