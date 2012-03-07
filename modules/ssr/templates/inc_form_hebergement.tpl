{{mb_script module="dPpatients" script="medecin"}}


<form name="editSejourHebergement" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="adresse_par_prat_id" value="{{$sejour->adresse_par_prat_id}}" />

  {{mb_key object=$sejour}}
  
  <table class="form">
    <tr>
      <th>{{mb_label object=$sejour field=etablissement_entree_id}}</th>
      <td colspan="3">
        {{mb_field object=$sejour field=etablissement_entree_id form="editSejourHebergement" autocomplete="true,1,50,true,true"}}
      </td>
    </tr>
    <tr id="correspondant_medical">
      {{assign var="object" value=$sejour}}
      <script type="text/javascript">
        Medecin.sFormName = "editSejourHebergement";
      </script>
      {{mb_include module=planningOp template=inc_check_correspondant_medical}}
    </tr>
    <tr>
      <td></td>
      <td colspan="3">
        <div id="_adresse_par_prat" style="{{if !$medecin_adresse_par}}display:none{{/if}}; width: 300px;">
          {{if $medecin_adresse_par}}Autres : {{$medecin_adresse_par->_view}}{{/if}}
        </div>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field="recuse"}}</th>
      <td>{{mb_field object=$sejour field="recuse"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
      <td>{{mb_field object=$sejour field="chambre_seule"}}</td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$sejour field="service_id"}}
      </th>
      <td colspan="3">
        <select name="service_id" class="{{$sejour->_props.service_id}}" style="width: 15em">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $sejour->service_id == $_service->_id}} selected="selected" {{/if}}>
            {{$_service->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$sejour field=rques}}</th>
      <td>
        {{mb_field object=$sejour field=rques form="editSejourHebergement"}}
      </td>
    </tr>
    <tr>
      <td class="button" colspan="6">
        <button class="submit" type="submit">
          {{tr}}Save{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>