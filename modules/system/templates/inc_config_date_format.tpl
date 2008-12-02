{{* $Id: $ *}}

<tr>
  <th>
    <label for="{{$var}}" title="{{tr}}config-{{$var}}-desc{{/tr}}">
      {{tr}}config-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    <input type="text" name="{{$var}}" value="{{$dPconfig.$var}}" />
    {{$now|date_format:$dPconfig.$var}}
  </td>
</tr>
