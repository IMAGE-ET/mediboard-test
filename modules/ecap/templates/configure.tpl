{{* $id: $ *}}

<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-ecap-source', true);
  });
</script>

<table class="main">  
  <tr>
    <th class="title">{{tr}}config-exchange-source{{/tr}}</th>
  </tr>
</table>

<ul id="tabs-ecap-source" class="control_tabs">
  <li><a href="#{{$ecap_files_source->name}}">{{$ecap_files_source->name}}</a></li>
  <li><a href="#{{$ecap_ssr_source->name}}">{{$ecap_ssr_source->name}}</a></li>
</ul>
  
<hr class="control_tabs" />  

<div id="{{$ecap_files_source->name}}" style="display:none">
  {{mb_include module=system template=inc_config_exchange_source source=$ecap_files_source}}
</div>

<div id="{{$ecap_ssr_source->name}}" style="display:none">
  {{mb_include module=system template=inc_config_exchange_source source=$ecap_ssr_source}}
</div>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <tr>
    <th class="category" colspan="10">DHE e-Cap</th>
  </tr>

  {{assign var=m value=interop}}
  {{assign var="var" value="base_url"}}

  {{mb_include module=system template=inc_config_str var=base_url}}
	
  <tr>
    <th />
    <td>
      <div class="small-info">
        Il s'agit de l'ancienne variable de configuration.
        <br/>
        Elle restera utilisée si la nouvelle variable (ci-desosus) n'est pas renseignée.
      </div>
    </td>
  </tr> 

  {{assign var=m value=ecap}}
	
  {{assign var=class value=dhe}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=rooturl}}
  <tr>
    <th />
    <td>{{$paths.dhe}}</td>
  </tr> 
   
  {{assign var=class value=soap}}
  <tr>
    <th class="category" colspan="10">{{tr}}config-{{$m}}-{{$class}}{{/tr}} (Obsolète)</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=rooturl}}
  <tr>
    <th />
    <td>{{$paths.soap.documents}}</td>
  </tr>
	
  {{mb_include module=system template=inc_config_str var=user}}
  {{mb_include module=system template=inc_config_str var=pass}}

	<tr>
	  <th class="category" colspan="2">Tags d'identifications</th>
	</tr>
  
  {{mb_include module=sip template=inc_config_tags pat=$tags.PA sej=$tags.SJ}} 

  <tr>
    <td class="button" colspan="10">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

{{include file=inc_configure_actions.tpl}}