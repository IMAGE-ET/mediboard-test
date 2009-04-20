<script type="text/javascript">

function updateId(id){
  var oForm = document.forms[document.selService.refresh_form.value];
  $V(oForm.config_service_id, id);
}

function checkSHM(name, action){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_check_conf");
  url.addParam("name", name);
  url.addParam("action", action);
  url.requestUpdate("shm", { waitingText: null } );
}

Main.add(function () {
  checkSHM('conf-service', 'check');
});

</script>

<table class="form">
  <tr>
    <td>
			<form name="selService" method="get" action="?">
			  <input type="hidden" name="m" value="{{$m}}" />
			  <input type="hidden" name="tab" value="{{$tab}}" />
			  <select name="service_id" onchange="this.form.submit();">
			    <option value="">&mdash; Choix d'un service</option>
			  {{foreach from=$services item=_service}}
			    <option value="{{$_service->_id}}" {{if $service_id == $_service->_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
			  {{/foreach}}
			  </select>
			  <input type="hidden" name="refresh_form" value="" />
			</form>
    </td>
    <td>
      <button class="tick" type="button" onclick="checkSHM('conf-service','check')">Vérifier la mémoire partagée</button>
    </td>
    <td id="shm">
    </td>
  </tr>
</table>

<table class="tbl">
<tr>
	<th class="category">{{mb_label object=$config_service field=name}}</th>
  <th class="category">Valeur par defaut</th>
  <th class="category">Valeur de l'etablissement</th>
  {{if $service_id}}
  <th class="category">Valeur du service</th>
  {{/if}}
</tr>
{{foreach from=$all_configs key=config_id item=configs}}
  {{assign var=config_group value=$all_configs.$config_id.group}}
  {{assign var=config_service value=$all_configs.$config_id.service}}
	<tr>
		<td>{{$all_configs.$config_id.name}}</td>
		<td>{{$all_configs.$config_id.default}}</td>
		<td>
		  <form name="editConfigService-group-{{$config_id}}">
		    <input type="hidden" name="m" value="{{$m}}" />
		    <input type="hidden" name="dosql" value="do_config_service_aed" />
		    <input type="hidden" name="name" value="{{$config_group->name}}" />
		    <input type="hidden" name="group_id" value="{{$group_id}}" />
		    <input type="hidden" name="config_service_id" value="{{$config_group->_id}}" />
		    <input type="hidden" name="callback" value="updateId" />
		    <input type="text" name="value" value="{{$config_group->value}}" onchange="$V(document.selService.refresh_form, 'editConfigService-group-{{$config_id}}'); submitFormAjax(this.form, 'systemMsg');"/>
		    <button type="button" class="tick notext" onclick="" />
		  </form>
		</td>
		{{if $service_id}}
		<td>
		  <form name="editconfigService-service-{{$config_id}}">
		    <input type="hidden" name="m" value="{{$m}}" />
		    <input type="hidden" name="dosql" value="do_config_service_aed" />
		    <input type="hidden" name="name" value="{{$config_service->name}}" />
		    <input type="hidden" name="service_id" value="{{$service_id}}" />
		    <input type="hidden" name="config_service_id" value="{{$config_service->_id}}" />
		    <input type="hidden" name="callback" value="updateId" />
		    <input type="text" name="value" value="{{$config_service->value}}" onchange="$V(document.selService.refresh_form, 'editconfigService-service-{{$config_id}}'); submitFormAjax(this.form, 'systemMsg');"/>
		    <button type="button" class="tick notext" onclick="" />
		  </form>
  	</td>
  	{{/if}}
	</tr>
{{/foreach}}
</table>