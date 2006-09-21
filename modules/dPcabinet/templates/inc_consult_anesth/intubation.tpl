<script language="Javascript" type="text/javascript">
function verifIntubDifficileAndSave(oForm){
  if(oForm.mallampati[2].checked || oForm.mallampati[3].checked
    || oForm.bouche[0].checked || oForm.bouche[1].checked
    || oForm.distThyro[0].checked){
  
    // Avertissement d'intubatino difficile
    $('divAlertIntubDiff').style.visibility = "visible";
  }else{
    $('divAlertIntubDiff').style.visibility = "hidden";
  }
  submitFormAjax(oForm, 'systemMsg')
}
</script>
<form name="editFrmIntubation" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
<input type="hidden" name="consultation_anesth_id" value="{{$consult_anesth->consultation_anesth_id}}" />
<table class="form">
  <tr>
    <th colspan="6" class="category">Condition d'intubation</th>
  </tr>
  <tr>
    {{foreach from=$consult_anesth->_enums.mallampati item=curr_mallampati}}
    <td rowspan="4" class="button">
      <label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{tr}}{{$curr_mallampati}}{{/tr}}"><img src="modules/{{$m}}/images/mallampati/{{$curr_mallampati}}.gif" alt="{{tr}}{{$curr_mallampati}}{{/tr}}" /></label>
      <br /><input type="radio" name="mallampati" value="{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}checked="checked"{{/if}} onclick="verifIntubDifficileAndSave(this.form);" /><label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{tr}}{{$curr_mallampati}}{{/tr}}">{{tr}}{{$curr_mallampati}}{{/tr}}</label>
    </td>
    {{/foreach}}

    <th><label for="bouche_m20" title="Ouverture de la bouche">Ouverture de la bouche</label></th>
    <td>
      {{foreach from=$consult_anesth->_enums.bouche item=curr_bouche}}
      <input type="radio" name="bouche" value="{{$curr_bouche}}" {{if $consult_anesth->bouche == $curr_bouche}}checked="checked"{{/if}} onclick="verifIntubDifficileAndSave(this.form);" /><label for="bouche_{{$curr_bouche}}" title="{{tr}}{{$curr_bouche}}{{/tr}}">{{tr}}{{$curr_bouche}}{{/tr}}</label><br />
      {{/foreach}}
    </td>
  </tr>
  
  <tr>
    <th><label for="distThyro_m65" title="Distance thyro-mentonnière">Distance thyro-mentonnière</label></th>
    <td>
      {{foreach from=$consult_anesth->_enums.distThyro item=curr_distThyro}}
      <input type="radio" name="distThyro" value="{{$curr_distThyro}}" {{if $consult_anesth->distThyro == $curr_distThyro}}checked="checked"{{/if}} onclick="verifIntubDifficileAndSave(this.form);" /><label for="distThyro_{{$curr_distThyro}}" title="{{tr}}{{$curr_distThyro}}{{/tr}}">{{tr}}{{$curr_distThyro}}{{/tr}}</label><br />
      {{/foreach}}
    </td>
  </tr>

  <tr>
    <th><label for="etatBucco" title="Etat bucco-dentaire">Etat bucco-dentaire</label></th>
    <td>
      <select name="_helpers_etatBucco" size="1" onchange="pasteHelperContent(this);this.form.etatBucco.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.etatBucco}}
      </select><br />
      <textarea name="etatBucco" onchange="submitFormAjax(this.form, 'systemMsg')" title="{{$consult_anesth->_props.etatBucco}}">{{$consult_anesth->etatBucco}}</textarea>
    </td>
  </tr>
  
  <tr>
    <th><label for="conclusion" title="Remarques et Conclusion sur les conditions d'intubation">Remarques / Conclusion</label></th>
    <td>
      <select name="_helpers_conclusion" size="1" onchange="pasteHelperContent(this);this.form.conclusion.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.conclusion}}
      </select><br />
      <textarea name="conclusion" onchange="submitFormAjax(this.form, 'systemMsg')" title="{{$consult_anesth->_props.conclusion}}">{{$consult_anesth->conclusion}}</textarea>
    </td>
  </tr>
  <tr>
    <td colspan="6" class="button">
      <div id="divAlertIntubDiff" style="float:right;color:#F00;{{if !$consult_anesth->_intub_difficile}}visibility:hidden;{{/if}}"><strong>Intubation Difficile Prévisible</strong></div>
    </td>
  </tr>
</table>
</form>