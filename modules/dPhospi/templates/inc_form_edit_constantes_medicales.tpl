{{mb_default var=hide_save_button value=0}}
{{mb_default var=callback_administration value=0}}
{{mb_default var=display_graph value=1}}
{{mb_default var=tri value=""}}

{{assign var=show_cat_tabs value=false}}
{{if "CConstantesMedicales::getConfig"|static_call:"show_cat_tabs"}}
  {{assign var=show_cat_tabs value=true}}
{{/if}}

{{assign var=show_enable_all_button value=false}}
{{if "CConstantesMedicales::getConfig"|static_call:"show_enable_all_button"}}
  {{assign var=show_enable_all_button value=true}}
{{/if}}

<script type="text/javascript">
submitConstantesMedicales = function(oForm) {
  return onSubmitFormAjax(oForm, {
    onComplete: function () {
      {{if $display_graph}}
        refreshConstantesMedicales($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
      {{/if}}
      {{if $conf.ref_pays == 2}}
        refreshConstantesMedicalesTri($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
      {{/if}}
    }
  });
}

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

emptyAndSubmit = function(const_name) {
  var form = getForm("edit-constantes-medicales");
  const_name.each(function(elem) {$V(form[elem], '');});
  return submitConstantesMedicales(form);
}

Main.add(function () {
  var oForm = getForm('edit-constantes-medicales{{$tri}}');
  calculImcVst(oForm);
  if (window.toggleAllGraphs) {
    toggleAllGraphs();
  }
  
  {{if $show_cat_tabs}}
    Control.Tabs.create("constantes-by-type{{$tri}}");
  {{/if}}
});
</script>

{{if ($constantes->_ref_context && $context_guid == $constantes->_ref_context->_guid && !$readonly) || isset($real_context|smarty:nodefaults)}}
  {{assign var=real_context value=1}}
{{else}}
  {{assign var=real_context value=0}}
{{/if}}

<form name="edit-constantes-medicales{{$tri}}" action="?" method="post" onsubmit="return {{if $real_context}}checkForm(this){{else}}false{{/if}}">
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
  {{if $callback_administration}}
    <input type="hidden" name="callback" value="submitAdmission" />
  {{/if}}
  {{assign var=const value=$latest_constantes.0}}
  {{assign var=dates value=$latest_constantes.1}}
  {{assign var=all_constantes value="CConstantesMedicales"|static:"list_constantes_type"}}
  
  <input type="hidden" name="_poids" value="{{$const->poids}}" />
  
  {{if $show_cat_tabs}}
  <ul id="constantes-by-type{{$tri}}" class="control_tabs small" style="min-width: 200px;">
    {{foreach from=$all_constantes key=_type item=_list}}
      <li>
        <a href="#type{{$tri}}-{{$_type}}">{{tr}}CConstantesMedicales.type.{{$_type}}{{/tr}}</a>
      </li>
    {{/foreach}}
  </ul>
  <hr class="control_tabs" />
  {{/if}}
  
  <table class="main form constantes">
    <tr>
      <th class="category"></th>
      {{if $real_context}}<th class="category">Saisie</th>{{/if}}
      <th class="category" colspan="{{if $display_graph}}2{{else}}1{{/if}}">Dernières</th>
      <th class="category"></th>
    </tr>
    
    {{assign var=at_least_one_hidden value=false}}
    
    {{foreach from=$all_constantes key=_type item=_list}}
      <tbody id="type{{$tri}}-{{$_type}}" {{if $show_cat_tabs}} {{if $_type != "vital"}} style="display: none;" {{/if}} {{/if}}>
      {{foreach from=$_list key=_constante item=_params}}
        <tr {{if !array_key_exists($_constante, $selection) && ($const->$_constante == "" || !$display_graph)}}
          style="display: none;" class="secondary"
          {{assign var=at_least_one_hidden value=true}}
        {{/if}}>
            <th style="text-align: left;">
              {{* a mb_title doesn't have a "for" attribute which speeds up prepareForm in IE
              {{mb_title object=$constantes field=$_constante for=$_constante}} 
              *}}
              <label for="{{$_constante}}" title="{{tr}}CConstantesMedicales-{{$_constante}}-desc{{/tr}}">
                {{tr}}CConstantesMedicales-{{$_constante}}-court{{/tr}}
                
                {{if $_params.unit}}
                  <small class="opacity-50">
                    {{if in_array($_constante, " "|explode:"ta ta_gauche ta_droit")}}
                      ({{$conf.dPpatients.CConstantesMedicales.unite_ta}})
                    {{else}}
                      ({{$_params.unit}})
                    {{/if}}
                  </small>
                {{/if}}
              </label>
            </th>
            
            {{if array_key_exists("formfields", $_params)}}
              {{if $real_context}}
                <td>
                  {{mb_field object=$constantes field=$_params.formfields.0 size="2" style="width:1.7em;"}} / 
                  {{mb_field object=$constantes field=$_params.formfields.1 size="2" style="width:1.7em;"}}
                  {{*  increment=true form="edit-constantes-medicales" *}}
                </td>
              {{/if}}
              <td style="text-align: center" title="{{$dates.$_constante|date_format:$conf.datetime}}">
                {{if $const->$_constante}}
                  {{mb_value object=$const field=$_params.formfields.0}} /
                  {{mb_value object=$const field=$_params.formfields.1}}
                {{/if}}
              </td>
            {{else}}
              {{assign var=_hidden value=false}}
                
              {{if $_constante.0 == "_"}}
                {{assign var=_readonly value="readonly"}}
                
                {{if array_key_exists("formula", $_params)}}
                  {{assign var=_hidden value=true}}
                {{/if}}
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
                  
                  {{mb_field object=$constantes field=$_constante size="3" onchange=$_callback|ternary:"$_callback(this.form)":null readonly=$_readonly hidden=$_hidden}}
                  {{* increment=$_readonly|ternary:false:true form="edit-constantes-medicales" *}}
          
                  {{if $_constante == "_imc"}}
                    <div id="constantes_medicales_imc" style="color:#F00;"></div>
                  {{/if}}
                </td>
              {{/if}}
              <td style="text-align: center" title="{{$dates.$_constante|date_format:$conf.datetime}}">
                {{mb_value object=$const field=$_constante}}
                <input type="hidden" name="_last_{{$_constante}}" value="{{$const->$_constante}}" />
              </td>
            {{/if}}
            {{if $display_graph}}
              <td class="narrow">
                {{if $_constante.0 != "_" || !empty($_params.plot|smarty:nodefaults)}}
                  <input type="checkbox" name="checkbox-constantes-medicales-{{$_constante}}" onclick="toggleGraph('{{$_constante}}', this.checked)" tabIndex="100" />
                {{/if}}
              </td>
            {{/if}}
            <td>
              {{if $_readonly !="readonly" && $real_context == 1 && $constantes->$_constante != ""}}
                {{if array_key_exists("formfields", $_params)}}
                  <button type="button" class="cancel notext compact" onclick="emptyAndSubmit(['{{$_params.formfields.0}}', '{{$_params.formfields.1}}']);"></button>
                {{else}}
                  <button type="button" class="cancel notext compact" onclick="emptyAndSubmit(['{{$_constante}}']);"></button>
                {{/if}} 
              {{/if}}
            </td>
          </tr>
        {{/foreach}}
      </tbody>
    {{/foreach}}
    
    {{if $real_context}}
      {{if $constantes->datetime}}
      <tr>
        <th>{{mb_title object=$constantes field=datetime}}</th>
        <td colspan="4">{{mb_field object=$constantes field=datetime form="edit-constantes-medicales" register=true}}</td>
      </tr>
      {{/if}}
      <tr>
        <td colspan="5" style="text-align: center;">{{mb_label object=$constantes field=comment}}</td>
      </tr>
      <tr>
        <td colspan="5">{{mb_field object=$constantes field=comment}}</td>
      </tr>
      <tr>
        {{if !$hide_save_button}}
          <td colspan="5" class="button">
            <button class="modify singleclick" onclick="return submitConstantesMedicales(this.form);">
              {{tr}}Save{{/tr}}
            </button>
            {{if $constantes->datetime}}
              <button class="new singleclick" type="button" onclick="$V(this.form.constantes_medicales_id, ''); $V(this.form._new_constantes_medicales, 1); return submitConstantesMedicales(this.form);">
                {{tr}}Create{{/tr}}
              </button>
              <br />
              <button class="trash" type="button" onclick="if (confirm('Etes-vous sûr de vouloir supprimer ce relevé ?')) {$V(this.form.del, 1); return submitConstantesMedicales(this.form);}">
                {{tr}}CConstantesMedicales.delete_all{{/tr}}
              </button>
            {{/if}}
          {{/if}}
        </td>
      </tr>
    {{/if}}
    
    {{if $show_enable_all_button && $at_least_one_hidden}}
    <tr>
      <td colspan="5" class="button">
        <button class="down" type="button" onclick="$$('.constantes .secondary').invoke('toggle')">Afficher toutes les valeurs</button>
      </td>
    </tr>
    {{/if}}
  </table>
</form>