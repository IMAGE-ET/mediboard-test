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
      else if (imc > 40)   imcInfo = "Obésité morbide";
      else if (imc > 35)   imcInfo = "Obésité sévère";
      else if (imc > 30)   imcInfo = "Obésité modérée";
      else if (imc > 25)   imcInfo = "Surpoids";
    }
  }
  
  $V(form._vst, vst);
  $V(form._imc, imc);
  
  var element = $('constantes_medicales_imc');
  if (element) {
    element.update(imcInfo);
  }
  
  if(typeof(calculPSA) == 'function' && typeof(calculClairance) == 'function') {
    calculPSA(); 
    calculClairance();
  }
}

Main.add(function () {
  var oForm = getForm('edit-constantes-medicales');

  /*$H(data).each(function(d){
    var checkbox = oForm["checkbox-constantes-medicales-"+d.key];
    checkbox.checked = (d.value.series.last().data.length > 1); // Not the first as it could be the "grey" line
    $('constantes-medicales-'+d.key).setVisible(checkbox.checked);
  });*/
  
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
  
  <table class="main form constantes" style="width: 1%;">
    <tr>
      <th class="category">Constantes</th>
      {{if $real_context}}<th class="category">Nouvelles</th>{{/if}}
      <th class="category" colspan="2">Dernières</th>
    </tr>
    
    {{assign var=all_constantes value="CConstantesMedicales"|static:"list_constantes"}}
    {{assign var=at_least_one_hidden value=false}}
    
    {{foreach from=$all_constantes key=_constante item=_params}}
    <tbody {{if !array_key_exists($_constante, $selection) && $const->$_constante == ""}}
      style="display: none;" class="secondary"
      {{assign var=at_least_one_hidden value=true}}
    {{/if}}>
      <tr>
        <th>
          {{mb_title object=$constantes field=$_constante}} {{if $_params.unit}}({{$_params.unit}}){{/if}}
        </th>
        
        {{if array_key_exists("formfields", $_params)}}
          {{if $real_context}}
            <td>
              {{mb_field object=$constantes field=$_params.formfields.0 size="2" increment=true form="edit-constantes-medicales"}} /
              {{mb_field object=$constantes field=$_params.formfields.1 size="2" increment=true form="edit-constantes-medicales"}}
            </td>
          {{/if}}
          <td style="text-align: center" title="{{$dates.$_constante|date_format:$dPconfig.datetime}}">
            {{if $const->$_constante}}
              {{mb_value object=$const field=$_params.formfields.0}} /
              {{mb_value object=$const field=$_params.formfields.1}}
            {{/if}}
          </td>
        {{else}}
          {{if $_constante.0 == "_"}}
            {{assign var=_readonly value="readonly"}}
          {{else}}
            {{assign var=_readonly value=null}}
          {{/if}}
          {{if $real_context}}
            <td>
              {{if array_key_exists("callback", $_params)}}
                {{assign var=_callback value=$_params.callback}}
              {{else}}
                {{assign var=_callback value=null}}
              {{/if}}
              
              {{mb_field object=$constantes field=$_constante size="4" 
                         onchange=$_callback|ternary:"$_callback(this.form)":null readonly=$_readonly 
                         increment=$_readonly|ternary:false:true form="edit-constantes-medicales"}}
            </td>
          {{/if}}
          <td style="text-align: center" title="{{$dates.$_constante|date_format:$dPconfig.datetime}}">
            {{mb_value object=$const field=$_constante}}
            <input type="hidden" name="_last_{{$_constante}}" value="{{$const->$_constante}}" />
          </td>
        {{/if}}
        <td style="width: 0.1%;">
				  {{if $_constante.0 != "_"}}
            <input type="checkbox" name="checkbox-constantes-medicales-{{$_constante}}" onchange="$('constantes-medicales-{{$_constante}}').setVisible(this.checked)" tabIndex="100" />
          {{/if}}
				</td>
      </tr>
      
      {{if $_constante == "_imc"}}
        <tr>
          <td colspan="4" id="constantes_medicales_imc" style="color:#F00; text-align: center;"></td>
        </tr>
      {{/if}}
    </tbody>
    {{/foreach}}
		
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
            <button class="trash" type="button" onclick="if (confirm('Etes-vous sûr de vouloir supprimer ce relevé ?')) {$V(this.form.del, 1); return submitConstantesMedicales(this.form);}">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
        </td>
      </tr>
    {{/if}}
    
    {{if $at_least_one_hidden}}
    <tr>
      <td colspan="4" class="button">
        <button class="down" type="button" onclick="$$('.constantes .secondary').invoke('toggle')">Afficher toutes les valeurs</button>
      </td>
    </tr>
    {{/if}}
  </table>
</form>