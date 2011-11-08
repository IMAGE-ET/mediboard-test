{{assign var=class value=CConstantesMedicales}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  <col style="width: 50%" />
  
  <tr>
    {{mb_include module=system template=inc_config_enum var=unite_ta values=cmHg|mmHg}}
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
	
  <tr>
    <th class="category" colspan="2">
    	Configurations par service / établissement
    </th>
  </tr>
</table>

</form>

<div class="small-info">
	Les configurations de base sont à gauche, il est possible de définir des configurations par établissement, 
	et de rédefinir chaque élément aussi pour chaque service.
</div>

<table class="main layout">
  <col style="width: 33%;" />
  <col style="width: 33%;" />
  <col style="width: 33%;" />
	
	<tr>
    <td>
      <ul class="control_tabs small">
        <li><a href="#1" class="active">Base</a></li>
      </ul>
      <hr class="control_tabs" />
			
			<table class="main form">
		    <tr>
		      <th class="category"></th>
		      <th class="category">Valeur</th>
		    </tr>
		    
		    {{foreach from="CConfigConstantesMedicales"|static:"_conf_names" item=_conf}}
		    <tr>
		      <th class="narrow">
		        {{mb_label class=CConfigConstantesMedicales field=$_conf}}
		      </th>
		      <td class="text">
		      	{{mb_value object=$base field=$_conf}}
		      </td>
		    </tr>
		    {{/foreach}}
		  </table>
    </td>
		
    <td style="border-left: 2px solid #3;">
      <ul class="control_tabs small">
        <li><a href="#1" class="active">{{$group}}</a></li>
      </ul>
      <hr class="control_tabs" />
    	{{mb_include template=dPpatients template=inc_configure_constantes_medicales_service entity=$group}}
    </td>
		
    <td>
			<script type="text/javascript">
			Main.add(Control.Tabs.create.curry('service-constantes-tabs', true));
			</script>
			
			<ul class="control_tabs small" id="service-constantes-tabs">
			  {{foreach from=$services item=_service}}
			    <li><a href="#constantes-{{$_service->_guid}}">{{$_service}}</a></li>
			  {{/foreach}}
			</ul>
			<hr class="control_tabs" />
			
			{{foreach from=$services item=_service}}
			<div id="constantes-{{$_service->_guid}}" style="display: none;">
			  {{mb_include template=dPpatients template=inc_configure_constantes_medicales_service entity=$_service}}
			</div>
			{{/foreach}}
    </td>
	</tr>
</table>
