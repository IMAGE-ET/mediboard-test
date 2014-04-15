<form name="edit-useragent" method="post" action="?" onsubmit="return onSubmitFormAjax(this, Control.Modal.close)">
  {{mb_class object=$ua}}
  {{mb_key object=$ua}}

  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$ua colspan=4}}

    <tr>
      <td colspan="4" class="text compact" style="padding: 6px; text-align: center;">
        {{$ua->user_agent_string}}
      </td>
    </tr>

    <tr>
      <th class="category" colspan="2">
        User agent
      </th>
      <th class="category">
        Sugg.
      </th>
      <th class="category">
        Detect.
      </th>
    </tr>

    <tr>
      <th>{{mb_label object=$ua field=browser_name}}</th>
      <td class="narrow">
        {{mb_field object=$ua field=browser_name}}
        {{mb_field object=$ua field=browser_version size=6}}
      </td>
      <td class="narrow">
        <select style="width: 50px;" onchange="UserAgent.updateName(this, 'browser_name')">
          <option value=""> &ndash; </option>
          {{foreach from="CUserAgent"|static:browser_names item=_item}}
            <option value="{{$_item}}">{{$_item}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>{{$detect.Browser}} &ndash; {{$detect.Version}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$ua field=platform_name}}</th>
      <td>
        {{mb_field object=$ua field=platform_name}}
        {{mb_field object=$ua field=platform_version size=6}}
      </td>
      <td>
        <select style="width: 50px;" onchange="UserAgent.updateName(this, 'platform_name')">
          <option value=""> &ndash; </option>
          {{foreach from="CUserAgent"|static:platform_names item=_item}}
            <option value="{{$_item}}">{{$_item}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>{{$detect.Platform}} &ndash; {{$detect.Platform_Version}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$ua field=device_name}}</th>
      <td>{{mb_field object=$ua field=device_name}}</td>
      <td class="narrow">
        <select style="width: 50px;" onchange="UserAgent.updateName(this, 'device_name')">
          <option value=""> &ndash; </option>
          {{foreach from="CUserAgent"|static:device_names item=_item}}
            <option value="{{$_item}}">{{$_item}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>{{$detect.Device_Name}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ua field=device_type}}</th>
      <td>{{mb_field object=$ua field=device_type}}</td>
      <td></td>
      <td>
        {{assign var=device_type value=$detect.Device_Type|lower}}
        {{$ua->_specs.device_type->_locales.$device_type}}
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$ua field=device_maker}}</th>
      <td>{{mb_field object=$ua field=device_maker}}</td>
      <td class="narrow">
        <select style="width: 50px;" onchange="UserAgent.updateName(this, 'device_maker')">
          <option value=""> &ndash; </option>
          {{foreach from="CUserAgent"|static:device_makers item=_item}}
            <option value="{{$_item}}">{{$_item}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>{{$detect.Device_Maker}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$ua field=pointing_method}}</th>
      <td>{{mb_field object=$ua field=pointing_method}}</td>
      <td></td>
      <td>
        {{assign var=pointing_method value=$detect.Device_Pointing_Method|lower}}
        {{$ua->_specs.pointing_method->_locales.$pointing_method}}
      </td>
    </tr>

    {{mb_include module=system template=inc_form_table_footer object=$ua colspan=4}}
  </table>
</form>