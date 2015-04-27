{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{include file=CMbObject_view.tpl}}

{{if $object->_ref_dossier_medical->object_class == "CPatient"}}
  {{assign var=reload value="DossierMedical.reloadDossierPatient"}}
{{else}}
  {{assign var=reload value="DossierMedical.reloadDossierSejour"}}
{{/if}}

<table class="form">
  <tr>
    <td class="button">
      <form name="Del-{{$object->_guid}}" action="?m=dPcabinet" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_traitement_aed" />
        
        {{mb_key object=$object}}
        
        <input type="hidden" name="traitement_id" value="{{$object->_id}}" />
        <input type="hidden" name="annule" value="" />
        
        {{if $object->annule == 0}}
          <button title="{{tr}}Cancel{{/tr}}" class="cancel" type="button" onclick="Traitement.cancel(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up('.tooltip').remove();">
            Stopper
          </button>
        {{else}}
          <button title="{{tr}}Restore{{/tr}}" class="tick" type="button" onclick="Traitement.restore(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up('.tooltip').remove();">
            {{tr}}Restore{{/tr}}
          </button>
        {{/if}}
        
        {{if $object->owner_id == $app->user_id}}
          <button title="{{tr}}Delete{{/tr}}" class="trash" type="button" onclick="Traitement.remove(this.form, {{$reload}}); $('{{$object->_guid}}_tooltip').up('.tooltip').remove();">
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
