<form name="intubation" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
<table class="form">
  <tr>
    {{foreach from=$consult->_ref_consult_anesth->_enums.mallampati item=curr_mallampati}}
    <td rowspan="4" class="button">
      <label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{tr}}{{$curr_mallampati}}{{/tr}}"><img src="modules/{{$m}}/images/mallampati/{{$curr_mallampati}}.png" alt="{{tr}}{{$curr_mallampati}}{{/tr}}" /></label>
      <br /><input type="radio" name="mallampati" value="{{$curr_mallampati}}" {{if $consult->_ref_consult_anesth->mallampati == $curr_mallampati}}checked="checked"{{/if}} /><label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{tr}}{{$curr_mallampati}}{{/tr}}">{{tr}}{{$curr_mallampati}}{{/tr}}</label>
    </td>
    {{/foreach}}

    <th><label for="bouche_m20" title="Ouverture de la bouche">Ouverture de la bouche</label></th>
    <td>
      {{foreach from=$consult->_ref_consult_anesth->_enums.bouche item=curr_bouche}}
      <input type="radio" name="bouche" value="{{$curr_bouche}}" {{if $consult->_ref_consult_anesth->bouche == $curr_bouche}}checked="checked"{{/if}} /><label for="bouche_{{$curr_bouche}}" title="{{tr}}{{$curr_bouche}}{{/tr}}">{{tr}}{{$curr_bouche}}{{/tr}}</label><br />
      {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <th><label for="distThyro_m65" title="Distance thyro-mentonnière">Distance thyro-mentonnière</label></th>
    <td>
      {{foreach from=$consult->_ref_consult_anesth->_enums.distThyro item=curr_distThyro}}
      <input type="radio" name="distThyro" value="{{$curr_distThyro}}" {{if $consult->_ref_consult_anesth->distThyro == $curr_distThyro}}checked="checked"{{/if}} /><label for="distThyro_{{$curr_distThyro}}" title="{{tr}}{{$curr_distThyro}}{{/tr}}">{{tr}}{{$curr_distThyro}}{{/tr}}</label><br />
      {{/foreach}}
    </td>
  </tr>

  <tr>
    <th><label for="etatBucco" title="Etat bucco-dentaire">Etat bucco-dentaire</label></th>
    <td>
      <input type="text" name="etatBucco" title="{{$consult->_ref_consult_anesth->_props.etatBucco}}"  value="{{$consult->_ref_consult_anesth->etatBucco}}" />
    </td>
  </tr>
  
  <tr>
    <th><label for="conclusion" title="Conclusion">Conclusion / Décision</label></th>
    <td>
      <input type="text" name="conclusion" title="{{$consult->_ref_consult_anesth->_props.conclusion}}"  value="{{$consult->_ref_consult_anesth->conclusion}}" />
    </td>
  </tr>
  <tr>
    <td colspan="6" class="button">
      <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg')">Sauver</button>
    </td>
  </tr>
</table>
</form>