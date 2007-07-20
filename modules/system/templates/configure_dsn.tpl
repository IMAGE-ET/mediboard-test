{{* $Id: $
 @param string $dsn
*}}

<!-- Configure dsn '{{$dsn}}' -->
{{assign var="section" value="db"}}
    
<tr>
  <th class="category" colspan="100">
    {{tr}}config-{{$section}}{{/tr}} '{{$dsn}}'
  </th>
</tr>

<tr>
  {{assign var="var" value="dbhost"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  {{assign var="var" value="dbname"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  {{assign var="var" value="dbuser"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>

<tr>
  {{assign var="var" value="dbpass"}}
  <th>
    <label for="{{$section}}[{{$dsn}}][{{$var}}]" title="{{tr}}config-{{$section}}-{{$var}}{{/tr}}">
      {{tr}}config-{{$section}}-{{$var}}{{/tr}}
    </label>  
  </th>
  <td>
    {{assign var=value value=$dPconfig.$section.$dsn.$var}}
    <input class="str" name="{{$section}}[{{$dsn}}][{{$var}}]" value="{{$value}}" />
  </td>
</tr>
