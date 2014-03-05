{{mb_default var=hide_save_button value=0}}
{{mb_default var=callback_administration value=0}}
{{mb_default var=display_graph value=1}}
{{mb_default var=tri_rpu value=""}}
{{mb_default var=can_create value=0}}
{{mb_default var=show_cat_tabs value="CConstantesMedicales::getConfig"|static_call:"show_cat_tabs"}}
{{mb_default var=show_enable_all_button value="CConstantesMedicales::getConfig"|static_call:"show_enable_all_button"}}
{{mb_default var=modif_timeout value=0}}

<script type="text/javascript">
submitConstantesMedicales = function(oForm) {
  return onSubmitFormAjax(oForm, {
    onComplete: function () {
      {{if $display_graph}}
        refreshConstantesMedicales($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
      {{/if}}
      {{if $conf.ref_pays == 2}}
        refreshConstantesMedicalesTri($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
        refreshConstantesMedicales($V(oForm.context_class)+'-'+$V(oForm.context_id), 1);
      {{/if}}
    }
  });
};

toggleConstantesecondary = function(element) {
  var secondary = $$('.constantes .secondary');
  secondary.invoke('toggle');
  if (secondary[0].visible()) {
    element.removeClassName("down");
    element.addClassName("up");
    element.innerHTML = "Cacher les scd.";
  }
  else {
    element.removeClassName("up");
    element.addClassName("down");
    element.innerHTML = "Aff. tout";
  }
};

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
  
  var element = $('constantes_medicales_imc{{$tri_rpu}}');
  if (element) {
    element.update(imcInfo);
  }
  
  if(typeof(calculPSA) == 'function' && typeof(calculClairance) == 'function') {
    calculPSA(); 
    calculClairance();
  }
};

emptyAndSubmit = function(const_name) {
  var form = getForm("edit-constantes-medicales{{$tri_rpu}}");
  const_name.each(function(elem) {$V(form[elem], '');});
  return submitConstantesMedicales(form);
};

Main.add(function () {
  var oForm = getForm('edit-constantes-medicales{{$tri_rpu}}');
  calculImcVst(oForm);
  if (window.toggleAllGraphs) {
    toggleAllGraphs();
  }
  
  {{if $show_cat_tabs}}
    Control.Tabs.create("constantes-by-type{{$tri_rpu}}");
  {{/if}}
  {{if $tri_rpu == '' && !$callback_administration}}
  ViewPort.SetAvlHeight('constant_form',1);
  {{/if}}
  ViewPort.SetAvlHeight('graphs', 1);
});
</script>

<div id="constant_form{{$tri_rpu}}" style="position:relative; min-height: 290px; width: 100%;">
  <form name="edit-constantes-medicales{{$tri_rpu}}" action="?" method="post" onsubmit="return {{if $can_edit}}checkForm(this){{else}}false{{/if}}">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_constantes_medicales_aed" />
    {{if !$constantes->_id}}
      <input type="hidden" name="_new_constantes_medicales" value="1" />
    {{else}}
      <input type="hidden" name="constantes_medicales_id" value="{{$constantes->_id}}" />
      <input type="hidden" name="_new_constantes_medicales" value="0" />
    {{/if}}
    {{mb_field object=$constantes field=_unite_ta hidden=1}}
    {{mb_field object=$constantes field=_unite_glycemie hidden=1}}
    {{mb_field object=$constantes field=_unite_cetonemie hidden=1}}
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
    <ul id="constantes-by-type{{$tri_rpu}}" class="control_tabs small" style="min-width: 200px;">
      {{foreach from=$all_constantes key=_type item=_list}}
        {{if array_key_exists($_type, $selection)}}
          <li>
            <a href="#type-{{$_type}}{{$tri_rpu}}">{{tr}}CConstantesMedicales.type.{{$_type}}{{/tr}}</a>
          </li>
        {{/if}}
      {{/foreach}}
    </ul>
    {{/if}}

    <div id="constantes_{{$constantes->_id}}{{$tri_rpu}}" style="height: 72%; overflow-y: auto; width: 100%;">
      {{if $modif_timeout}}
        <div class="small-warning">
          {{tr var1=$modif_timeout}}CConstantes-Medicales-msg-modif-timeout-%s{{/tr}}
        </div>
      {{/if}}
      <table class="main form constantes" style="margin-right:20px;">
        <tr>
          <th class="category"></th>
          {{if $can_edit}}<th class="category">Saisie</th>{{/if}}
          <th class="category" colspan="{{if $display_graph}}2{{else}}1{{/if}}">Dernières</th>
          <th class="category">
            {{if $constantes->_id}}
              {{mb_include module=system template=inc_object_history object=$constantes}}
            {{/if}}
          </th>
          <th style="width:10px;"></th>
        </tr>

        {{assign var=at_least_one_hidden value=false}}
        {{assign var=constants_list value="CConstantesMedicales"|static:"list_constantes"}}

        {{foreach from=$selection key=_type item=_ranks}}
          <tbody id="type-{{$_type}}{{$tri_rpu}}" {{if $show_cat_tabs}} {{if $_type != "vital"}} style="display: none;" {{/if}} {{/if}}>
            {{foreach from=$_ranks key=_rank item=_constants}}
              {{foreach from=$_constants item=_constant}}
                <tr {{if $_rank == "hidden" && ($const->$_constant == "" || !$display_graph)}}
                  style="display: none;" class="secondary"
                  {{assign var=at_least_one_hidden value=true}}
                  {{/if}}>
                  <th style="text-align: left;" class="text">
                    <label for="{{$_constant}}" title="{{tr}}CConstantesMedicales-{{$_constant}}-desc{{/tr}}">
                      {{tr}}CConstantesMedicales-{{$_constant}}-court{{/tr}}

                      {{assign var=_params value=$constants_list.$_constant}}
                      {{if $_params.unit}}
                        <small class="opacity-50">
                          ({{$_params.unit}})
                        </small>
                      {{/if}}
                    </label>
                  </th>

                  {{assign var=_readonly value=null}}
                  {{if array_key_exists("formfields", $_params)}}
                    {{if $can_edit}}
                      <td>
                        {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                          {{assign var=_style value="width:1.7em;"}}
                          {{assign var=_size value=2}}
                          {{if $_params.formfields|@count == 1}}
                            {{assign var=_style value=""}}
                            {{assign var=_size value=3}}
                          {{/if}}

                          {{if !$smarty.foreach._formfield.first}}/{{/if}}
                          {{mb_field object=$constantes field=$_params.formfields.$_key size=$_size style=$_style}}
                        {{/foreach}}
                      </td>
                    {{/if}}
                    <td style="text-align: center" title="{{$dates.$_constant|date_format:$conf.datetime}}">
                      {{if $const->$_constant}}
                        {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                          {{if !$smarty.foreach._formfield.first}}/{{/if}}
                          {{mb_value object=$const field=$_params.formfields.$_key}}
                        {{/foreach}}
                      {{/if}}
                    </td>
                  {{else}}
                    {{assign var=_hidden value=false}}

                    {{if $_constant.0 == "_"}}
                      {{assign var=_readonly value="readonly"}}

                      {{if array_key_exists("formula", $_params)}}
                        {{assign var=_hidden value=true}}
                      {{/if}}
                    {{/if}}

                    {{if $can_edit}}
                      <td>
                        {{if array_key_exists("callback", $_params)}}
                          {{assign var=_callback value=$_params.callback}}
                        {{else}}
                          {{assign var=_callback value=null}}
                        {{/if}}

                        {{mb_field object=$constantes field=$_constant size="3" onchange=$_callback|ternary:"$_callback(this.form)":null readonly=$_readonly hidden=$_hidden}}

                        {{if $_constant == "_imc"}}
                          <div id="constantes_medicales_imc{{$tri_rpu}}" style="color:#F00;"></div>
                        {{/if}}
                      </td>
                    {{/if}}
                    <td style="text-align: center" title="{{$dates.$_constant|date_format:$conf.datetime}}">
                      {{mb_value object=$const field=$_constant}}
                      <input type="hidden" name="_last_{{$_constant}}" value="{{$const->$_constant}}" />
                    </td>
                  {{/if}}

                  {{if $display_graph}}
                    <td class="narrow">
                      {{if $_constant.0 != "_" || !empty($_params.plot|smarty:nodefaults)}}
                        <input type="checkbox" class="checkbox-constant" name="checkbox-constantes-medicales-{{$_constant}}" onclick="toggleGraph(this)" tabIndex="100" />
                      {{/if}}
                    </td>
                  {{/if}}
                  <td>
                    {{if $_readonly !="readonly" && $can_edit && $constantes->$_constant != ""}}
                      {{if array_key_exists("formfields", $_params)}}
                        <button type="button" class="cancel notext compact" onclick="emptyAndSubmit({{$_params.formfields|@json|smarty:nodefaults|JSAttribute}});"></button>
                      {{else}}
                        <button type="button" class="cancel notext compact" onclick="emptyAndSubmit(['{{$_constant}}']);"></button>
                      {{/if}}
                    {{/if}}
                  </td>
                </tr>
              {{/foreach}}
            {{/foreach}}
          </tbody>
        {{/foreach}}
      </table>
    </div>

    <div style="{{if $tri_rpu == ''}}position: absolute; bottom:0;{{/if}} text-align:center; height:27%; width: 100%;" id="buttons_form_const{{$tri_rpu}}">
      {{if $can_edit && !$modif_timeout}}
        {{mb_field object=$constantes field=datetime form="edit-constantes-medicales$tri_rpu" register=true}}
        {{if $constantes->_id}}
          <button style="display:inline-block;" class="trash notext" type="button" onclick="if (confirm('Etes-vous sûr de vouloir supprimer ce relevé ?')) {$V(this.form.del, 1); return submitConstantesMedicales(this.form);}">
            {{tr}}CConstantesMedicales.delete_all{{/tr}}
          </button>
        {{/if}}
        {{mb_field object=$constantes field=comment placeholder="Commentaire" rows=2}}
        {{if !$hide_save_button}}
          <button class="modify singleclick" onclick="return submitConstantesMedicales(this.form);">
            {{tr}}Save{{/tr}}
          </button>
        {{/if}}
      {{elseif $can_create}}
        <button class="new singleclick" type="button" onclick="newConstants('{{$context_guid}}');">
          {{tr}}New{{/tr}}
        </button>
      {{/if}}

      {{if $show_enable_all_button && $at_least_one_hidden}}
        <button class="down" type="button" onclick="toggleConstantesecondary(this);">Aff. tout</button>
      {{/if}}
    </div>
  </form>
</div>