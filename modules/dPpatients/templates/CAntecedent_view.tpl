<table class="tbl" id="{{$object->_guid}}_tooltip">
  <tr>
    <th>{{mb_label object=$object field=type}}</th>
    <td>{{mb_value object=$object field=type}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field=appareil}}</th>
    <td>{{mb_value object=$object field=appareil}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field=date}}</th>
    <td>{{mb_value object=$object field=date}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object field=rques}}</th>
    <td class="text">{{mb_value object=$object field=rques}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$object->_ref_first_log field=user_id}}</th>
    <td>{{$object->_ref_first_log->_ref_user->_view}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$object->_ref_first_log field=date}}</th>
    <td>{{mb_value object=$object->_ref_first_log field=date}}
    </td>
  </tr>
  <tr>
    <th>Actions</th>
    <td>
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
          <button title="{{tr}}Cancel{{/tr}}" class="cancel notext" type="button" onclick="Antecedent.cancel(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up(2).remove();">
            {{tr}}Cancel{{/tr}}
          </button>
        {{else}}
          <button title="{{tr}}Restore{{/tr}}" class="tick notext" type="button" onclick="Antecedent.restore(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up(2).remove();">
            {{tr}}Restore{{/tr}}
          </button>
        {{/if}}
        
        {{if $object->_ref_first_log && $object->_ref_first_log->user_id == $app->user_id}}
		      <button title="{{tr}}Delete{{/tr}}" class="trash notext" type="button" onclick="Antecedent.remove(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up(2).remove();">
		        {{tr}}Delete{{/tr}}
		      </button>
	      {{/if}}
      </form>
    </td>
  </tr>
</table>