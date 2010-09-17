{{* $id: $ *}}

<form name="Config-Sources" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  {{assign var=m value=ecap}}
  {{assign var=class value=WebServices}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=user_login_prefix}}

  <tr>
    <td class="button" colspan="10">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-ecap-source', true));
</script>
<ul id="tabs-ecap-source" class="control_tabs">
  <li><a href="#{{$ecap_files_source->name}}">{{$ecap_files_source->name}}</a></li>
  <li><a href="#{{$ecap_ssr_source->name}}"  >{{$ecap_ssr_source->name}}  </a></li>
</ul>
  
<hr class="control_tabs" />  

<div id="{{$ecap_files_source->name}}" style="display:none">
  {{mb_include module=system template=inc_config_exchange_source source=$ecap_files_source}}
</div>

<div id="{{$ecap_ssr_source->name}}" style="display:none">
  {{mb_include module=system template=inc_config_exchange_source source=$ecap_ssr_source}}
</div>

