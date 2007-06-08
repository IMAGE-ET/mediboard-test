{{if $subject->_ext_codes_ccam}}
<tr>
  <th class="category" colspan="2">{{tr}}{{$subject->_class_name}}-_ext_codes_ccam{{/tr}}</th>
</tr>
{{foreach from=$subject->_ext_codes_ccam item=currCode}}
<tr>
  <td class="text" colspan="2">
    <strong>{{$currCode->code}}</strong> :
    {{$currCode->libelleLong}}
  </td>
</tr>
{{/foreach}}
{{/if}}

{{if $vue=="complete" && $subject->_ref_actes_ccam}}
<tr>
  <th class="category" colspan="2">{{tr}}{{$subject->_class_name}}-_ref_actes_ccam{{/tr}}</th>
</tr> 
{{foreach from=$subject->_ref_actes_ccam item=curr_acte}}
<tr>
  <td>
    <strong>{{$curr_acte->_view}}</strong>
    par {{$curr_acte->_ref_executant->_view}} 
  </td>
  <td>
  {{$curr_acte->commentaire}}
  </td>
</tr>
{{/foreach}}
{{/if}}