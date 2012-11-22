{{unique_id var=uid}}

<script>
updateObjectId{{$uid}} = function(id){
  if (!id || id == 0) return;
  $V(getForm("configure-constantes-{{$entity->_guid}}").config_constantes_medicales_id, id);
}
</script>

<form name="configure-constantes-{{$entity->_guid}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="callback" value="updateObjectId{{$uid}}" />
  {{mb_class object=$entity->_conf_object}}
  {{mb_key object=$entity->_conf_object}}
	
	{{if $entity instanceof CService}}
    <input type="hidden" name="service_id" value="{{$entity->_id}}" />
	{{else}}
    <input type="hidden" name="group_id" value="{{$entity->_id}}" />
	{{/if}}
	
  <table class="main form">
    <tr>
      <th class="category"></th>
      <th class="category">Valeur</th>
    </tr>
    
    {{foreach from="CConfigConstantesMedicales"|static:"_conf_names" item=_conf}}
			{{unique_id var=uid_row}}
	    <tr class="config-row">
	      <th class="narrow">
	      	{{mb_label object=$entity->_conf_object field=$_conf}}
					<button class="cancel notext" type="button" onclick="$(this).up('tr.config-row').select('.values input, .values select').each(function(e){$V(e, '')})">
						{{tr}}Reset{{/tr}}
					</button>
				</th>
	      <td class="values {{if $entity->_conf_object->$_conf == null}} opacity-50 {{/if}}">
          {{assign var=typeEnum value=select}}

          {{if $entity->_conf_object->_specs.$_conf instanceof CSetSpec}}
            {{assign var=typeEnum value=checkbox}}
          {{/if}}

	      	{{mb_field object=$entity->_conf_object field=$_conf increment=true form="configure-constantes-`$entity->_guid`" emptyLabel=" " typeEnum=$typeEnum
					           onchange="\$(this).up('tr.config-row').down('.values').setClassName('opacity-50',!this.value); this.form.onsubmit()"}}
				</td>
	    </tr>
    {{/foreach}}
  </table>
</form>