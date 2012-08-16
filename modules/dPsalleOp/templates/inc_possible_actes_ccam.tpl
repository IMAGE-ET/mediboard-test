{{**
  * Affiche les actes CCAM potentiels et ceux réellement codés
  * A ne pas confondre avec inc_list_actes_ccam.
  *}}

{{assign var=can_view_tarif value=true}}
{{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif}}
  {{if !$app->_ref_user->isPraticien() && !$app->_ref_user->isSecretaire()}}
    {{assign var=can_view_tarif value=false}}
  {{/if}}
{{/if}}


<table class="form">
  <tr>
    <th class="category">
      {{mb_title class=CActeCCAM field=code_acte    }}
      {{mb_title class=CActeCCAM field=code_activite}}
      {{mb_title class=CActeCCAM field=code_phase   }}
    </th>
    
    <th class="category">{{mb_title class=CActeCCAM field=executant_id       }}</th>
    <th class="category">{{mb_title class=CActeCCAM field=modificateurs      }}</th>
    <th class="category">{{mb_title class=CActeCCAM field=code_association   }}</th>
    <th class="category">{{mb_title class=CActeCCAM field=execution          }}</th>
    <th class="category">{{mb_title class=CActeCCAM field=montant_base       }}</th>
    <th class="category">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th class="category">{{mb_title class=CActeCCAM field=_montant_facture   }}</th>
  </tr>

  {{foreach from=$subject->_ext_codes_ccam item=curr_code key=curr_key}}
    {{foreach from=$curr_code->activites item=curr_activite}}
      {{foreach from=$curr_activite->phases item=curr_phase}}
      <tr>
        {{assign var="acte" value=$curr_phase->_connected_acte}}
        {{assign var="view" value=$acte->_viewUnique}}
        {{assign var="key" value="$curr_key$view"}}
        
                  
        {{assign var=can_view_dh value=true}}
        {{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif && $acte->_id && ($acte->_ref_executant->function_id != $app->_ref_user->function_id)}}
          {{assign var=can_view_dh value=false}}
        {{/if}}
        
        {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
        <td style="{{if $acte->_id && $acte->code_association == $acte->_guess_association}}background-color: #9f9;{{elseif $acte->_id}}background-color: #fc9;{{else}}background-color: #f99;{{/if}}">{{$curr_code->code}}-{{$curr_activite->numero}}-{{$curr_phase->phase}}</td>
        {{if $acte->_id}}
        <td>
          {{assign var="executant_id" value=$acte->executant_id}}
          {{if array_key_exists($executant_id, $listExecutants)}}
            {{assign var="executant" value=$listExecutants.$executant_id}}
            <div class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
            {{$executant}}
          {{else}}
          <div class="small-info">
          L'exécutant de l'acte est désactivé.
          {{/if}}
          </div>
        </td>
        {{else}}
        <td></td>
        {{/if}}
        <td>{{$acte->modificateurs}}</td>
        <td>{{$acte->code_association}}</td>
        <td>{{mb_value object=$acte field=execution}}</td>
          <td style="text-align: right">
            {{if $can_view_tarif && ($conf.dPsalleOp.CActeCCAM.tarif || $subject->_class == "CConsultation")}}
              {{mb_value object=$acte field=montant_base}}
            {{/if}}
          </td>
          <td style="text-align: right">
            {{if $can_view_dh && ($conf.dPsalleOp.CActeCCAM.tarif || $subject->_class == "CConsultation")}}
              {{mb_value object=$acte field=montant_depassement}}
            {{/if}}
          </td>
          <td style="text-align: right">
            {{if $can_view_tarif && $can_view_dh && ($conf.dPsalleOp.CActeCCAM.tarif || $subject->_class == "CConsultation")}}
              {{mb_value object=$acte field=_montant_facture}}
            {{/if}}
          </td>
      </tr> 
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>