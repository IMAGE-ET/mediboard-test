<script type="text/javascript">
Main.add(function () {
  var oForm = getForm('edit-constantes-medicales'),
      cookie = new CookieJar();

  // Recuperation de la valeur du cookie, on masque les graphs qui ne sont pas selectionnés  
  $H(data).each(function(d){
    oForm["checkbox-constantes-medicales-"+d.key].checked = 
      cookie.getValue('graphsToShow', 'constantes-medicales-'+d.key) ||
      d.value.series.first().data.length;
      
    $('constantes-medicales-'+d.key).setVisible(oForm["checkbox-constantes-medicales-"+d.key].checked);
  });
});
</script>

{{if $constantes->_ref_context && $context_guid == $constantes->_ref_context->_guid && !$readonly}}
  {{assign var=real_context value=1}}
{{else}}
  {{assign var=real_context value=0}}
{{/if}}

<form name="edit-constantes-medicales" action="?" method="post" onsubmit="return {{if $real_context}}checkForm(this){{else}}false{{/if}}">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
  {{if !$constantes->datetime}}
  <input type="hidden" name="datetime" value="now" />
  <input type="hidden" name="_new_constantes_medicales" value="1" />
  {{else}}
  <input type="hidden" name="constantes_medicales_id" value="{{$constantes->_id}}" />
  <input type="hidden" name="_new_constantes_medicales" value="0" />
  {{/if}}
  {{mb_field object=$constantes field=context_class hidden=1}}
  {{mb_field object=$constantes field=context_id hidden=1}}
  {{mb_field object=$constantes field=patient_id hidden=1}}
  
  {{assign var=const value=$latest_constantes.0}}
  <table class="main form" style="width: 1%;">
    <tr>
      <th class="category">Constantes</th>
      {{if $real_context}}<th class="category">Nouvelles</th>{{/if}}
      <th class="category" colspan="2">Dernières</th>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=ta}} (cm Hg)</th>
      {{if $real_context}}
      <td>
        {{mb_field object=$constantes field=_ta_systole size="1" class="num min|0 max|50"}} /
        {{mb_field object=$constantes field=_ta_diastole size="1" class="num min|0 max|50"}}
      </td>
      {{/if}}
      <td style="text-align: center">
        {{if $const->ta}}
          {{mb_value object=$const field=_ta_systole}} /
          {{mb_value object=$const field=_ta_diastole}}
        {{/if}}
      </td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-ta"  onchange="toggleGraph('constantes-medicales-ta');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=poids}} (Kg)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=poids size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->poids}}{{mb_value object=$const field=poids size="4"}}{{/if}}</td>
      <td style="width: 0.1%;"><input type="checkbox" name="checkbox-constantes-medicales-poids" onchange="toggleGraph('constantes-medicales-poids');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=taille}} (cm)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=taille size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->taille}}{{mb_value object=$const field=taille size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-taille" onchange="toggleGraph('constantes-medicales-taille');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=pouls}} (/min)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=pouls size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->pouls}}{{mb_value object=$const field=pouls size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-pouls"  onchange="toggleGraph('constantes-medicales-pouls');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=temperature}} (°C)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=temperature size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->temperature}}{{mb_value object=$const field=temperature size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-temperature"  onchange="toggleGraph('constantes-medicales-temperature');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=spo2}} (%)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=spo2 size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->spo2}}{{mb_value object=$const field=spo2 size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-spo2" onchange="toggleGraph('constantes-medicales-spo2');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=score_sensibilite}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=score_sensibilite size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->score_sensibilite}}{{mb_value object=$const field=score_sensibilite size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_sensibilite"  onchange="toggleGraph('constantes-medicales-score_sensibilite');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=score_motricite}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=score_motricite size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->score_motricite}}{{mb_value object=$const field=score_motricite size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_motricite"  onchange="toggleGraph('constantes-medicales-score_motricite');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=score_sedation}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=score_sedation size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->score_sedation}}{{mb_value object=$const field=score_sedation size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_sedation"  onchange="toggleGraph('constantes-medicales-score_sedation');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=frequence_respiratoire}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=frequence_respiratoire size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->frequence_respiratoire}}{{mb_value object=$const field=frequence_respiratoire size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-frequence_respiratoire"  onchange="toggleGraph('constantes-medicales-frequence_respiratoire');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=EVA}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=EVA size="4"}}</td>{{/if}}
      <td style="text-align: center">{{if $const->EVA}}{{mb_value object=$const field=EVA size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-EVA"  onchange="toggleGraph('constantes-medicales-EVA');" /></td>
    </tr>
    {{if $real_context}}
      {{if $constantes->datetime}}
      <tr>
        <th>{{mb_label object=$constantes field=datetime}}</th>
        <td class="date" colspan="3">{{mb_field object=$constantes field=datetime form="edit-constantes-medicales" register=true}}</td>
      </tr>
      {{/if}}
      <tr>      
        <td colspan="4" class="button">
          <button class="modify" onclick="return submitConstantesMedicales(this.form);">
            {{if !$constantes->datetime}}
              {{tr}}Create{{/tr}}
            {{else}}
              {{tr}}Modify{{/tr}}
            {{/if}}
          </button>
          {{if $constantes->datetime}}
            <button class="new" type="button" onclick="$V(this.form.constantes_medicales_id, ''); $V(this.form._new_constantes_medicales, 1); return submitConstantesMedicales(this.form);">
              {{tr}}Create{{/tr}}
            </button>
            <button class="trash" type="button" onclick="if (confirm('Etes-vous sûr de vouloir supprimer ce relevé ?')) {$V(this.form.del, 1); return submitConstantesMedicales(this.form);}">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
        </td>
      </tr>
    {{/if}}
  </table>
</form>