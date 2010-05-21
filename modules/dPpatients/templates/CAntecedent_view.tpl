<table class="tbl">
  <tr>
    <th class="category">{{tr}}CAntecedent{{/tr}}</th>
  </tr>
  
	{{if $object->type}}
  <tr>
    <td>
			<strong>{{mb_label object=$object field=type}}</strong>
			{{mb_value object=$object field=type}}
			<br/>
		</td>
  </tr>
	{{/if}}

	{{if $object->appareil}}
  <tr>
    <td>
			<strong>{{mb_label object=$object field=appareil}}</strong>
			{{mb_value object=$object field=appareil}}
			<br/>
		</td>
  </tr>
	{{/if}}

	{{if $object->date}}
  <tr>
    <td>
			<strong>{{mb_label object=$object field=date}}</strong>
			{{mb_value object=$object field=date}}
			<br/>
		</td>
  </tr>
	{{/if}}

	{{if $object->rques}}
  <tr>
    <td class="text">
			<strong>{{mb_label object=$object field=rques}}</strong>
			{{mb_value object=$object field=rques}}
			<br/>
		</td>
  </tr>
	{{/if}}
</table>

<table class="form">
  <tr>
    <td class="button">
      {{if $object->_ref_dossier_medical->object_class == "CPatient"}}
        {{assign var=reload value="DossierMedical.reloadDossierPatient"}}
      {{else}}
        {{assign var=reload value="DossierMedical.reloadDossierSejour"}}
      {{/if}}
      
      <form name="delAntFrm-{{$object->_id}}" action="?m=dPcabinet" method="post">
	      <input type="hidden" name="m" value="dPpatients" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="dosql" value="do_antecedent_aed" />
	      <input type="hidden" name="antecedent_id" value="{{$object->_id}}" />
	      <input type="hidden" name="annule" value="" />
        
        {{if $object->annule == 0}}
          <button title="{{tr}}Cancel{{/tr}}" class="cancel" type="button" onclick="Antecedent.cancel(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up('.tooltip').remove();">
            {{tr}}Cancel{{/tr}}
          </button>
        {{else}}
          <button title="{{tr}}Restore{{/tr}}" class="tick" type="button" onclick="Antecedent.restore(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up('.tooltip').remove();">
            {{tr}}Restore{{/tr}}
          </button>
        {{/if}}
        
        {{if $object->_ref_first_log && $object->_ref_first_log->user_id == $app->user_id}}
		      <button title="{{tr}}Delete{{/tr}}" class="trash" type="button" onclick="Antecedent.remove(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up('.tooltip').remove();">
		        {{tr}}Delete{{/tr}}
		      </button>
	      {{/if}}
      </form>
		</td>
  </tr>

</table>
  
<table class="tbl" id="{{$object->_guid}}_tooltip">

  <tr>
    <th colspan="2">Historique</th>
  </tr>
  
  {{foreach from=$object->_ref_logs item=_log}}
  <tr>
    <td>{{$_log->_ref_user->_view}}
    <td>{{mb_value object=$_log field=date}}</td>
  </tr>
	{{/foreach}}
  
</table>