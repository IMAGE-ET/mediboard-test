{{mb_default var=colspan value=2}}

<tr>
  <th>
    <label for="_bind_tag_view">Tags</label>
  </th>
  <td style="white-space: normal;" colspan="{{$colspan-1}}">
    {{mb_include module=system template=inc_tag_binder_widget}}
  </td>
</tr>