{{* $id: $ *}}

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

