{{if $patient->_id}}
<!-- Footer du tableau -->
 <tbody class="hoverable {{if !$no_class}}footer{{/if}} {{if $last_screen_footer}}last_footer{{/if}}">
   <tr>
  <th class="title" colspan="2">Remarque</th>
  <th class="title" colspan="2">Pharmacien</th>
  <th class="title" colspan="{{$tabHours.$date|@count}}">Signature IDE</th>
  <th class="title" colspan="{{$tabHours.$date|@count}}">Signature IDE</th>
  <th class="title" colspan="{{$tabHours.$date|@count}}">Signature IDE</th>
</tr>
<tr>
  <td style="border: 1px solid #ccc; height: 1.5cm" colspan="2" rowspan="3"></td>
  <td class="text" style="border: 1px solid #ccc; text-align: center" colspan="2" rowspan="3">
  {{if $pharmacien->_id}}
    {{$pharmacien->_view}} {{$last_log->date|date_format:$dPconfig.datetime}}
  {{/if}}  
  </td>
  <td class="signature_ide" colspan="{{$tabHours.$date|@count}}" ></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td>
</tr>
<tr><td class="signature_ide" colspan="{{$tabHours.$date|@count}}" ></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td></tr>
<tr><td class="signature_ide" colspan="{{$tabHours.$date|@count}}" ></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td></tr>
 </tbody>
 {{/if}}