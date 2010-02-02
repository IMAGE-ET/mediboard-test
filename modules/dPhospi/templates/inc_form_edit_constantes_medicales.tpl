<script type="text/javascript">
calculImcVst = function(form) {
  var imcInfo, imc, vst,
      poids  = parseFloat($V(form.poids)),
      taille = parseFloat($V(form.taille));
  
  if (poids && !isNaN(poids) && poids > 0) {
    vst = {{if $constantes->_ref_patient->sexe=="m"}}70{{else}}65{{/if}} * poids;
  
    if (taille && !isNaN(taille) && taille > 0) {
      imc = Math.round(100 * 100 * 100 * poids / (taille * taille))/100; // Math.round(x*100)/100 == round(x, 2)
      
           if (imc < 15)   imcInfo = "Inanition";
      else if (imc < 18.5) imcInfo = "Maigreur";
      else if (imc > 40)   imcInfo = "Ob�sit� morbide";
      else if (imc > 35)   imcInfo = "Ob�sit� s�v�re";
      else if (imc > 30)   imcInfo = "Ob�sit� mod�r�e";
      else if (imc > 25)   imcInfo = "Surpoids";
    }
  }
  
  $V(form._vst, vst);
  $V(form._imc, imc);
  
  $('constantes_medicales_imc').update(imcInfo);
  if(typeof(calculPSA) == 'function' && typeof(calculClairance) == 'function') {
    calculPSA(); 
    calculClairance();
  }
}

Main.add(function () {
  var oForm = getForm('edit-constantes-medicales');

  $H(data).each(function(d){
    oForm["checkbox-constantes-medicales-"+d.key].checked = !!d.value.series.first().data.length;
    $('constantes-medicales-'+d.key).setVisible(oForm["checkbox-constantes-medicales-"+d.key].checked);
  });
  
  calculImcVst(oForm);
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
  {{assign var=dates value=$latest_constantes.1}}
  
  <input type="hidden" name="_poids" value="{{$const->poids}}" />
  
  <table class="main form" style="width: 1%;">
    <tr>
      <th class="category">Constantes</th>
      {{if $real_context}}<th class="category">Nouvelles</th>{{/if}}
      <th class="category" colspan="2">Derni�res</th>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=poids}} (Kg)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=poids size="4" onchange="calculImcVst(this.form)"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.poids|date_format:$dPconfig.datetime}}">{{if $const->poids}}{{mb_value object=$const field=poids size="4"}}{{/if}}</td>
      <td style="width: 0.1%;"><input type="checkbox" name="checkbox-constantes-medicales-poids" onchange="toggleGraph('constantes-medicales-poids');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=taille}} (cm)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=taille size="4" onchange="calculImcVst(this.form)"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.taille|date_format:$dPconfig.datetime}}">{{if $const->taille}}{{mb_value object=$const field=taille size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-taille" onchange="toggleGraph('constantes-medicales-taille');" tabIndex="100" /></td>
    </tr>
		<tr>
      <th>{{mb_title object=$constantes field=pouls}} (/min)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=pouls size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.pouls|date_format:$dPconfig.datetime}}">{{if $const->pouls}}{{mb_value object=$const field=pouls size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-pouls"  onchange="toggleGraph('constantes-medicales-pouls');" tabIndex="100" /></td>
    </tr>
		<tr>
      <th>{{mb_title object=$constantes field=ta}} (cm Hg)</th>
      {{if $real_context}}
      <td>
        {{mb_field object=$constantes field=_ta_systole size="1"}} /
        {{mb_field object=$constantes field=_ta_diastole size="1"}}
      </td>
      {{/if}}
      <td style="text-align: center" title="{{$dates.ta|date_format:$dPconfig.datetime}}">
        {{if $const->ta}}
          {{mb_value object=$const field=_ta_systole}} /
          {{mb_value object=$const field=_ta_diastole}}
        {{/if}}
      </td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-ta"  onchange="toggleGraph('constantes-medicales-ta');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=_vst}} (ml)</th>
      {{if $real_context}}<td>{{mb_field object=$const field=_vst size="4" readonly="readonly" tabIndex="100"}}</td>{{/if}}
      <td>{{mb_value object=$const field=_vst}}{{if $const->_vst}}{{/if}}</td>
      <td />
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=_imc}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=_imc size="4" readonly="readonly" tabIndex="100"}}</td>{{/if}}
      <td>{{mb_value object=$const field=_imc}}</td>
      <td />
    </tr>
    <tr>
      <td colspan="4" id="constantes_medicales_imc" style="color:#F00; text-align: center;"></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=temperature}} (�C)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=temperature size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.temperature|date_format:$dPconfig.datetime}}">{{if $const->temperature}}{{mb_value object=$const field=temperature size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-temperature"  onchange="toggleGraph('constantes-medicales-temperature');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=spo2}} (%)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=spo2 size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.spo2|date_format:$dPconfig.datetime}}">{{if $const->spo2}}{{mb_value object=$const field=spo2 size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-spo2" onchange="toggleGraph('constantes-medicales-spo2');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=score_sensibilite}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=score_sensibilite size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.score_sensibilite|date_format:$dPconfig.datetime}}">{{if $const->score_sensibilite}}{{mb_value object=$const field=score_sensibilite size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_sensibilite"  onchange="toggleGraph('constantes-medicales-score_sensibilite');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=score_motricite}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=score_motricite size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.score_motricite|date_format:$dPconfig.datetime}}">{{if $const->score_motricite}}{{mb_value object=$const field=score_motricite size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_motricite"  onchange="toggleGraph('constantes-medicales-score_motricite');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=score_sedation}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=score_sedation size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.score_sedation|date_format:$dPconfig.datetime}}">{{if $const->score_sedation}}{{mb_value object=$const field=score_sedation size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-score_sedation"  onchange="toggleGraph('constantes-medicales-score_sedation');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=frequence_respiratoire}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=frequence_respiratoire size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.frequence_respiratoire|date_format:$dPconfig.datetime}}">{{if $const->frequence_respiratoire}}{{mb_value object=$const field=frequence_respiratoire size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-frequence_respiratoire"  onchange="toggleGraph('constantes-medicales-frequence_respiratoire');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=EVA}}</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=EVA size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.EVA|date_format:$dPconfig.datetime}}">{{if $const->EVA}}{{mb_value object=$const field=EVA size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-EVA"  onchange="toggleGraph('constantes-medicales-EVA');" tabIndex="100" /></td>
    </tr>
    <tr>
      <th>{{mb_title object=$constantes field=glycemie}} (g/l)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=glycemie size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.glycemie|date_format:$dPconfig.datetime}}">{{if $const->glycemie}}{{mb_value object=$const field=glycemie size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-glycemie"  onchange="toggleGraph('constantes-medicales-glycemie');" tabIndex="100" /></td>
    </tr>

    <tr>
      <th>{{mb_title object=$constantes field=diurese}} (ml)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=diurese size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.diurese|date_format:$dPconfig.datetime}}">{{if $const->diurese}}{{mb_value object=$const field=diurese size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-diurese"  onchange="toggleGraph('constantes-medicales-diurese');" tabIndex="100" /></td>
    </tr>
		
    <tr>
      <th>{{mb_title object=$constantes field=redon}} (ml)</th>
      {{if $real_context}}<td>{{mb_field object=$constantes field=redon size="4"}}</td>{{/if}}
      <td style="text-align: center" title="{{$dates.redon|date_format:$dPconfig.datetime}}">{{if $const->redon}}{{mb_value object=$const field=redon size="4"}}{{/if}}</td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-redon"  onchange="toggleGraph('constantes-medicales-redon');" tabIndex="100" /></td>
    </tr>
    
		<tr>
      <th>{{mb_title object=$constantes field=injection}}</th>
      {{if $real_context}}
      <td>
        {{mb_field object=$constantes field=_inj size="1"}} /
        {{mb_field object=$constantes field=_inj_essai size="1"}}
      </td>
      {{/if}}
      <td style="text-align: center" title="{{$dates.injection|date_format:$dPconfig.datetime}}">
        {{if $const->injection}}
          {{mb_value object=$const field=_inj}} /
          {{mb_value object=$const field=_inj_essai}}
        {{/if}}
      </td>
      <td><input type="checkbox" name="checkbox-constantes-medicales-injection"  onchange="toggleGraph('constantes-medicales-injection');" tabIndex="100" /></td>
    </tr>
		
		
		{{if $real_context}}
      {{if $constantes->datetime}}
      <tr>
        <th>{{mb_title object=$constantes field=datetime}}</th>
        <td colspan="3">{{mb_field object=$constantes field=datetime form="edit-constantes-medicales" register=true}}</td>
      </tr>
      {{/if}}
      <tr>      
        <td colspan="4" class="button">
          <button class="modify" onclick="return submitConstantesMedicales(this.form);">
            {{if !$constantes->datetime}}
              {{tr}}Create{{/tr}}
            {{else}}
              {{tr}}Save{{/tr}}
            {{/if}}
          </button>
          {{if $constantes->datetime}}
            <button class="new" type="button" onclick="$V(this.form.constantes_medicales_id, ''); $V(this.form._new_constantes_medicales, 1); return submitConstantesMedicales(this.form);">
              {{tr}}Create{{/tr}}
            </button>
            <button class="trash" type="button" onclick="if (confirm('Etes-vous s�r de vouloir supprimer ce relev� ?')) {$V(this.form.del, 1); return submitConstantesMedicales(this.form);}">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
        </td>
      </tr>
    {{/if}}
  </table>
</form>