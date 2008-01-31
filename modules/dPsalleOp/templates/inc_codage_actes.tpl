<script language="Javascript" type="text/javascript">

function viewCode(code, class){
  var url = new Url;
  url.setModuleAction("dPccam", "vw_full_code");
  url.addParam("codeacte", code);
  url.addParam("object_class", class);
  url.addParam("hideSelect", "1");
  url.popup(700, 550, "Code CCAM");
}

	PairEffect.initGroup("acteEffect");
</script>

{{foreach from=$subject->_ext_codes_ccam item=curr_code key=curr_key}}
  <a href="#" onclick="viewCode('{{$curr_code->code}}', '{{$subject->_class_name}}')">
    <strong>{{$curr_code->code}} :</strong> 
    {{$curr_code->libelleLong}}
  </a>
  
  {{if $can->edit || $modif_operation}}
  
  <br />
  <select name="asso" onchange="setCodeTemp(this.value)">
    <option value="">&mdash; Choisir un code associé</option>
    {{foreach from=$curr_code->assos item=curr_asso}}
    <option value="{{$curr_asso.code}}">{{$curr_asso.code}}({{$curr_asso.texte|truncate:40:"...":true}})</option>
    {{/foreach}}
  </select>
  {{/if}}
  
  {{foreach from=$curr_code->activites item=curr_activite}}
  {{foreach from=$curr_activite->phases item=curr_phase}}
  {{assign var="acte" value=$curr_phase->_connected_acte}}
  {{assign var="view" value=$acte->_viewUnique}}
  {{assign var="key" value="$curr_key$view"}}
  
  <form name="formActe-{{$view}}" action="?m={{$module}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPsalleOp" />
  <input type="hidden" name="dosql" value="do_acteccam_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="acte_id" value="{{$acte->_id}}" />
  <!-- Variable _calcul_montant_base permettant de la lancer la sauvegarde du montant de base dans le store de l'acte ccam -->
  <input type="hidden" name="_calcul_montant_base" value="1" />
  <input type="hidden" name="object_id" class="{{$acte->_props.object_id}}" value="{{$subject->_id}}" />
  <input type="hidden" name="object_class" class="{{$acte->_props.object_class}}" value="{{$subject->_class_name}}" />
  <input type="hidden" name="code_acte" class="{{$acte->_props.code_acte}}" value="{{$acte->code_acte}}" />
  <input type="hidden" name="code_activite" class="{{$acte->_props.code_activite}}" value="{{$acte->code_activite}}" />
  <input type="hidden" name="code_phase" class="{{$acte->_props.code_phase}}" value="{{$acte->code_phase}}" />
  <input type="hidden" name="code_association" class="{{$acte->_props.code_association}}" value="{{$acte->code_association}}" />
  {{if !$dPconfig.dPsalleOp.CActeCCAM.tarif}}
  <input type="hidden" name="montant_depassement" class="{{$acte->_props.montant_depassement}}" value="{{$acte->montant_depassement}}" />
  {{/if}}

	<!-- Couleur de l'acte -->
  {{if $acte->_id && $acte->code_association == $acte->_guess_association}}
  {{assign var=bg_color value=9f9}}
  {{elseif $acte->_id}}
  {{assign var=bg_color value=fc9}}
  {{else}}
  {{assign var=bg_color value=f99}}
  {{/if}}

			{{assign var=newButtons value=true}}
      {{if $newButtons}}
<!--  <div style="float: right;">-->
      <div style="position: absolute; right: 10px;">
      {{if $can->edit || $modif_operation}}
        {{if !$acte->acte_id}}
        <button class="add" type="button" onclick="
          {{if $acte->_anesth_associe && $subject->_class_name == "COperation"}}
          if(confirm('Cet acte ne comporte pas l\'activité d\'anesthésie.\nVoulez-vous ajouter le code d\'anesthésie complémentaire {{$acte->_anesth_associe}} ?')) {
            document.manageCodes._newCode.value = '{{$acte->_anesth_associe}}';
            ActesCCAM.add({{$subject->_id}}, {{$subject->_praticien_id}}, { onComplete: null });
          }
          {{/if}}
          submitFormAjax(this.form, 'systemMsg',{onComplete: function(){ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})} });">
          Coder cet acte
        </button>

        {{else}}
        
        <button class="remove" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}',ajax:'1'}, {onComplete: function(){ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})} })">
          {{tr}}Delete{{/tr}} cet acte
        </button>
        {{/if}}
      {{/if}}
      </div>
      {{/if}}

  <table class="form">
    
    <tr id="acte{{$key}}-trigger">
      <td colspan="3" style="width: 100%; background-color: #{{$bg_color}}">
        Activité {{$curr_activite->numero}} ({{$curr_activite->type}}) &mdash; 
        Phase {{$curr_phase->phase}} 
        <!-- {{$curr_phase->libelle}} -->
      </td>
    </tr>
  
    <tbody class="acteEffect" id="acte{{$key}}" style="display: none;">
    
    <tr style="display: none;">
      <th><label for="execution" title="Date et heure d'exécution de l'acte">Exécution</label></th>
      <td colspan="2">
        <input type="text" name="execution" class="{{$acte->_props.execution}}" readonly="readonly" value="{{$acte->execution}}" />
        <button class="tick" onclick="this.form.execution.value = new Date().toDATETIME());">Maintenant</button><br />
      </td>
    </tr>
    
    <tr class="{{$key}}">
      <td style="width: 1%;" />
      <td style="width: 100%;" />
      <td style="width: 100%;" />
    </tr>
    
    <tr class="{{$key}}">
      <th><label for="executant_id" title="Professionnel de santé exécutant l'acte">Exécutant</label></th>
      <td colspan="2">
      
      {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
     
        {{if $can->edit || $modif_operation}}
        <select name="executant_id" class="{{$acte->_props.executant_id}}">
          <option value="">&mdash; Choisir un professionnel de santé</option>
          {{foreach from=$listExecutants item=curr_executant}}
          <option class="mediuser" style="border-color: #{{$curr_executant->_ref_function->color}};" value="{{$curr_executant->user_id}}" {{if $acte->executant_id == $curr_executant->user_id}} selected="selected" {{/if}}>
            {{$curr_executant->_view}}
          </option>
          {{/foreach}}
        </select>
        {{elseif $acte->executant_id}}
          {{assign var="keyActe" value=$acte->executant_id}}
          {{assign var="selActe" value=$listExecutants.$keyActe}}
          {{$selActe->_view}}
        {{else}}-{{/if}}
      </td>
    </tr>
    <tr class="{{$view}}">
      <th><label for="modificateurs" title="Modificateurs associés à l'acte">Modificateur(s)</label></th>
      <td class="text" colspan="2">
        {{foreach from=$curr_phase->_modificateurs item=curr_mod}}
          {{if $can->edit || $modif_operation}}
          <input type="checkbox" name="modificateur_{{$curr_mod->code}}" {{if $curr_mod->_value}}checked="checked"{{/if}} />
          <label for="modificateur_{{$curr_mod->code}}" title="{{$curr_mod->libelle}}">
            {{$curr_mod->code}} : {{$curr_mod->libelle}}
          </label>
          <br />
          {{elseif $curr_mod->_value}}
            {{$curr_mod->code}} : {{$curr_mod->libelle}}
          {{/if}}
        {{/foreach}}
      </td>
    </tr>
    
    {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}
    <tr class="{{$view}}">
      <th><label for="montant_depassement" title="Dépassement d'honoraire">Dépassement</label></th>
      <td class="text" colspan="2">
        {{if $can->edit || $modif_operation}}
          {{mb_field object=$acte field="montant_depassement"}}
        {{elseif $acte->montant_depassement}}
          {{mb_value object=$acte field="montant_depassement"}}
        {{else}}-{{/if}}
      </td>
    </tr>
    {{/if}}

    <tr>
      <th><label for="commentaire" title="Commentaires sur l'acte">Commentaire</label></th>
      <td class="text" colspan="2">
        {{if $can->edit || $modif_operation}}
        <textarea name="commentaire" class="{{$acte->_props.commentaire}}">{{$acte->commentaire}}</textarea>
        {{elseif $acte->commentaire}}
          {{$acte->commentaire|nl2br}}
        {{else}}-{{/if}}
      </td>
    </tr>

    {{if $newButtons && $acte->_id}}
    <tr>
      <td class="button" colspan="3">
        <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: function(){ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})} })">
          {{tr}}Modify{{/tr}} cet acte
        </button>
      </td>
		</tr>
    {{/if}}
    
    </tbody>
    
    <tr>
      <td colspan="3" class="text">
        {{if $acte->_id}}
        
        <select name="{{$view}}"
        onchange="setAssociation(this.value, document.forms['formActe-{{$view}}'], {{$subject->_id}}, {{$subject->_praticien_id}})">
          <option value="" {{if !$acte->code_association}}selected="selected"{{/if}}>Aucun (100%)</option>
          <option value="1" {{if $acte->code_association == 1}}selected="selected"{{/if}}>1 (100%)</option>
          <option value="2" {{if $acte->code_association == 2}}selected="selected"{{/if}}>2 (50%)</option>
          <option value="3" {{if $acte->code_association == 3}}selected="selected"{{/if}}>3 (75%)</option>
          <option value="4" {{if $acte->code_association == 4}}selected="selected"{{/if}}>4 (100%)</option>
          <option value="5" {{if $acte->code_association == 5}}selected="selected"{{/if}}>5 (100%)</option>
        </select>
        
        <strong>
        asso. pour {{$curr_activite->type}}
        {{if $dPconfig.dPsalleOp.CActeCCAM.tarif}}
          &mdash; {{$acte->_tarif|string_format:"%.2f"}} €
        {{/if}}
        </strong>
        {{if $acte->code_association != $acte->_guess_association}}
          <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'translate', params: { text: 'CActeCCAM-regle-association-{{$acte->_guess_regle_asso}}' } })">
            <strong>
              {{if $acte->_guess_association}}
                ({{$acte->_guess_association}}
              {{else}}
                (Aucun
              {{/if}}
              conseillé)
            </strong>
          </span>
        {{/if}}
        
        
        {{if $acte->montant_depassement && $dPconfig.dPsalleOp.CActeCCAM.tarif}}
        &mdash; dépassement : {{$acte->montant_depassement|string_format:"%.2f"}} €
        {{/if}}
        {{/if}}
      </td>
    </tr>
    {{if !$newButtons}}
    <tr>
      <td class="button" colspan="3">
      {{if $can->edit || $modif_operation}}
        {{if !$acte->acte_id}}
        <button class="new" type="button" onclick="
          {{if $acte->_anesth_associe && $subject->_class_name == "COperation"}}
          if(confirm('Cet acte ne comporte pas l\'activité d\'anesthésie.\nVoulez-vous ajouter le code d\'anesthésie complémentaire {{$acte->_anesth_associe}} ?')) {
            document.manageCodes._newCode.value = '{{$acte->_anesth_associe}}';
            ActesCCAM.add({{$subject->_id}}, {{$subject->_praticien_id}}, { onComplete: null });
          }
          {{/if}}
          submitFormAjax(this.form, 'systemMsg',{onComplete: function(){ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})} });">
          Coder cet acte
        </button>

        {{else}}
        
        <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: function(){ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})} })">
          {{tr}}Modify{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}',ajax:'1'}, {onComplete: function(){ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})} })">
          {{tr}}Delete{{/tr}}
        </button>
        
        {{/if}}
      {{/if}}
      </td>
    </tr>
    {{/if}}
    
  </table>
</form>
{{/foreach}}

{{if $ajax}}

<script type="text/javascript">
  oElement = $('acte{{$key}}');
  oForm = getBoundingForm(oElement);
  prepareForm(oForm);
</script>

{{/if}}

{{/foreach}}
<hr/>

{{foreachelse}}
<em>Pas d'acte à coder</em>
{{/foreach}}
