<form name="edit-constantes-medicales" action="?" method="post" onsubmit="return checkForm(this);">
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
  <table class="tbl" style="width: 1%;">
    <tr>
      <th class="title">Libelle</th>
      <th class="title">Nouvelles</th>
      <th class="title">Dernières</th>
      <th></th>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=poids}} (Kg)</th>
      <td>{{mb_field object=$constantes field=poids size="4"}}</td>
      <td style="text-align: center">{{if $const->poids}}{{mb_value object=$const field=poids size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-poids" onchange="toggleGraph('constantes-medicales-poids');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=taille}} (cm)</th>
      <td>{{mb_field object=$constantes field=taille size="4"}}</td>
      <td style="text-align: center">{{if $const->taille}}{{mb_value object=$const field=taille size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-taille" onchange="toggleGraph('constantes-medicales-taille');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=ta}} (cm Hg)</th>
      <td>
        {{mb_field object=$constantes field=_ta_systole size="1"}} /
        {{mb_field object=$constantes field=_ta_diastole size="1"}}
      </td>
      <td style="text-align: center">
	      {{if $const->ta}}
	        {{mb_value object=$const field=_ta_systole size="1"}} /
	        {{mb_value object=$const field=_ta_diastole size="1"}}
	      {{/if}}
      </td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-ta"  onchange="toggleGraph('constantes-medicales-ta');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=pouls}} (/min)</th>
      <td>{{mb_field object=$constantes field=pouls size="4"}}</td>
      <td style="text-align: center">{{if $const->pouls}}{{mb_value object=$const field=pouls size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-pouls"  onchange="toggleGraph('constantes-medicales-pouls');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=spo2}} (%)</th>
      <td>{{mb_field object=$constantes field=spo2 size="4"}}</td>
      <td style="text-align: center">{{if $const->spo2}}{{mb_value object=$const field=spo2 size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-spo2" onchange="toggleGraph('constantes-medicales-spo2');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=temperature}} (°C)</th>
      <td>{{mb_field object=$constantes field=temperature size="4"}}</td>
      <td style="text-align: center">{{if $const->temperature}}{{mb_value object=$const field=temperature size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-temperature"  onchange="toggleGraph('constantes-medicales-temperature');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=score_sensibilite}}</th>
      <td>{{mb_field object=$constantes field=score_sensibilite size="4"}}</td>
      <td style="text-align: center">{{if $const->score_sensibilite}}{{mb_value object=$const field=score_sensibilite size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_sensibilite"  onchange="toggleGraph('constantes-medicales-score_sensibilite');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=score_motricite}}</th>
      <td>{{mb_field object=$constantes field=score_motricite size="4"}}</td>
      <td style="text-align: center">{{if $const->score_motricite}}{{mb_value object=$const field=score_motricite size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_motricite"  onchange="toggleGraph('constantes-medicales-score_motricite');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=score_sedation}}</th>
      <td>{{mb_field object=$constantes field=score_sedation size="4"}}</td>
      <td style="text-align: center">{{if $const->score_sedation}}{{mb_value object=$const field=score_sedation size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_sedation"  onchange="toggleGraph('constantes-medicales-score_sedation');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=frequence_respiratoire}}</th>
      <td>{{mb_field object=$constantes field=frequence_respiratoire size="4"}}</td>
      <td style="text-align: center">{{if $const->frequence_respiratoire}}{{mb_value object=$const field=frequence_respiratoire size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-frequence_respiratoire"  onchange="toggleGraph('constantes-medicales-frequence_respiratoire');" /></td>
    </tr>
    <tr>
      <th>{{mb_label object=$constantes field=EVA}}</th>
      <td>{{mb_field object=$constantes field=EVA size="4"}}</td>
      <td style="text-align: center">{{if $const->EVA}}{{mb_value object=$const field=EVA size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-EVA"  onchange="toggleGraph('constantes-medicales-EVA');" /></td>
    </tr>
    <tr>
      {{if $constantes->datetime}}
        <th>{{mb_label object=$constantes field=datetime}}</th>
        <td class="date" colspan="3">{{mb_field object=$constantes field=datetime form="edit-constantes-medicales" register=true}}</td>
      {{/if}}
    </tr>
    <tr>      
      <th>Action</th>
      <td colspan="3">
        <button class="modify" onclick="return submitConstantesMedicales(this.form);">
          {{if !$constantes->datetime}}
            {{tr}}Create{{/tr}}
          {{else}}
            {{tr}}Modify{{/tr}}
          {{/if}}
        </button>
        {{if $constantes->datetime}}
          <button class="new" onclick="$V(this.form.constantes_medicales_id, null); $V(this.form._new_constantes_medicales, 1); return submitConstantesMedicales(this.form);">
            {{tr}}Create{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>