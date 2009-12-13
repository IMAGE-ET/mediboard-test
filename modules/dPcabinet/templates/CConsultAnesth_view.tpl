{{mb_include module=dPcabinet template=CConsultation_view object=$object->_ref_consultation}}

<table class="tbl">
  <tr>
    <th>{{tr}}{{$object->_class_name}}{{/tr}}</th>
  </tr>
  <tr>
    <td>
      {{foreach from=$object->_specs key=prop item=spec}}
      {{mb_include module=system template=inc_field_view}}
      {{/foreach}}
    </td>
  </tr>
</table>
