<script type="text/javascript">

function viewCode(code, object_class){
  var url = new Url;
  url.setModuleAction("dPccam", "vw_full_code");
  url.addParam("codeacte", code);
  url.addParam("object_class", object_class);
  url.addParam("hideSelect", "1");
  url.popup(700, 550, "Code CCAM");
}

function setToNow(element) {
  element.value = "now";
  element.form.elements[element.name+"_da"].value = "Maintenant";
}

</script>

<table class="main">
{{foreach from=$subject->_ext_codes_ccam item=curr_code key=curr_key}}
  <tr>
  <td class="text" {{if $dPconfig.dPsalleOp.CActeCCAM.contraste}}style="border: outset 3px #000; background-color: #444"{{/if}}>
	<!-- Codes d'associations -->
  {{if count($curr_code->assos) < 15}}
  {{if $can->edit || $modif_operation}}
  <select style="float:right" name="asso" onchange="setCodeTemp(this.value)">
    <option value="">&mdash; Choisir un code associé</option>
    {{foreach from=$curr_code->assos item=curr_asso}}
    <option value="{{$curr_asso.code}}">
	    {{$curr_asso.code}}
	    ({{$curr_asso.texte|truncate:35:"...":true}})
    </option>
    {{/foreach}}
  </select>
  {{/if}}
  {{/if}}

  <a href="#" {{if $dPconfig.dPsalleOp.CActeCCAM.contraste}}style="color: #fff;"{{/if}} onclick="viewCode('{{$curr_code->code}}', '{{$subject->_class_name}}')">
    <strong>{{$curr_code->code}} :</strong> 
    {{$curr_code->libelleLong}}
  </a>
  
  </td>
  </tr>
  <tr>
  <td class="text">

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
  {{if !$dPconfig.dPsalleOp.CActeCCAM.tarif && $subject->_class_name != "CConsultation"}}
  <input type="hidden" name="montant_depassement" class="{{$acte->_props.montant_depassement}}" value="{{$acte->montant_depassement}}" />
  {{/if}}

	<!-- Couleur de l'acte -->
  {{if $acte->_id && ($acte->code_association == $acte->_guess_association || !$dPconfig.dPsalleOp.CActeCCAM.alerte_asso)}}
  {{assign var=bg_color value=9f9}}
  {{elseif $acte->_id}}
  {{assign var=bg_color value=fc9}}
  {{else}}
  {{assign var=bg_color value=f99}}
  {{/if}}
	
	{{assign var=newButtons value=true}}
  {{if $newButtons}}
  <div style="position: absolute; right: 24px; margin-top: 4px;">
  {{if $can->edit || $modif_operation}}
    {{if !$acte->_id}}
    <button class="add" type="button" onclick="
      {{if $acte->_anesth_associe && $subject->_class_name == "COperation"}}
      if(confirm('Cet acte ne comporte pas l\'activité d\'anesthésie.\nVoulez-vous ajouter le code d\'anesthésie complémentaire {{$acte->_anesth_associe}} ?')) {
        document.manageCodes._newCode.value = '{{$acte->_anesth_associe}}';
        ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');
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
      <td colspan="4" style="width: 100%; background-color: #{{$bg_color}}; border: outset 4px {{if $curr_activite->numero == 4}}#44f{{else}}#ff0{{/if}};">
        Activité {{$curr_activite->numero}} ({{$curr_activite->type}}) &mdash; 
        Phase {{$curr_phase->phase}} 
        <!-- {{$curr_phase->libelle}} -->
      </td>
    </tr>
  
    <tbody class="acteEffect" id="acte{{$key}}" {{if !$dPconfig.dPsalleOp.CActeCCAM.openline}}style="display: none;"{{/if}}>
    
    <!-- Ligne cosmétique -->
    <tr class="{{$key}}">
      <td style="width: 1%;" />
      <td style="width: 1%;" />
      <td style="width: 1%;" />
      <td style="width: 100%;" />
    </tr>
    
    <!-- Execution -->
    {{if $can->edit}}
    <tr>
      <th>{{mb_label object=$acte field=execution}}</th>
      <td class="date" colspan="3">
	      {{mb_field object=$acte field=execution form="formActe-$view" register=true}}
        <button type="button" class="tick" onclick="setToNow(this.form.execution);">
          Maintenant
        </button>
      </td>
    </tr>
    {{/if}}
    
    <!-- Exécutant -->
    <tr class="{{$key}}">
      <th>{{mb_label object=$acte field=executant_id}}</th>
      <td colspan="3">
      
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
          {{$acte->_ref_executant->_view}}
        {{else}}-{{/if}}
      </td>
    </tr>
    
		<!-- Modificateurs -->
		{{assign var=modifs_compacts value=$dPconfig.dPsalleOp.CActeCCAM.modifs_compacts}}  
    <tr class="{{$view}}">
      <th>{{mb_label object=$acte field=modificateurs}}</th>
      <td{{if !$modifs_compacts}} class="text" colspan="3"{{/if}}>
        {{foreach from=$curr_phase->_modificateurs item=curr_mod name=modificateurs}}
          {{if $can->edit || $modif_operation}}
          <input type="checkbox" name="modificateur_{{$curr_mod->code}}" {{if $curr_mod->_value}}checked="checked"{{/if}} />
          <label for="modificateur_{{$curr_mod->code}}" title="{{$curr_mod->libelle}}">
            {{$curr_mod->code}} 
            {{if !$modifs_compacts}} : {{$curr_mod->libelle}}{{/if}}
          </label>
          {{elseif $curr_mod->_value}}
            {{$curr_mod->code}}
            {{if !$modifs_compacts}} : {{$curr_mod->libelle}}{{/if}}
          {{/if}}
          {{if !$modifs_compacts}}<br />{{/if}}          
        {{foreachelse}}
        <em>Pas de modificateurs pour cette activité</em>
        {{/foreach}}
      </td>
      {{if $modifs_compacts}}
      <td>
        {{mb_label object=$acte field=rembourse}}
        {{assign var=disabled value=""}}
        {{if $curr_code->remboursement == 1}}{{assign var=disabled value=0}}{{/if}}
        {{if $curr_code->remboursement == 2}}{{assign var=disabled value=1}}{{/if}}

        {{assign var=default value="1"}}
        {{if $curr_code->remboursement == 1}}{{assign var=default value=1}}{{/if}}
        {{if $curr_code->remboursement == 2}}{{assign var=default value=0}}{{/if}}
        
        {{if $can->edit || $modif_operation}}
          {{mb_field object=$acte field=rembourse disabled=$disabled default=$default}}
        {{else}}
          {{mb_value object=$acte field=rembourse}}
        {{/if}}
      </td>
      <td>
        {{if $dPconfig.dPsalleOp.CActeCCAM.tarif || $subject->_class_name == "CConsultation"}}
        {{mb_label object=$acte field=montant_depassement}}
        {{if $can->edit || $modif_operation}}
          {{mb_field object=$acte field=montant_depassement}}
        {{else}}
          {{mb_value object=$acte field=montant_depassement}}
        {{/if}}
        {{/if}}
      </td>
      {{/if}}
    </tr>
    
    {{if !$modifs_compacts}}
    <!-- Remboursable + Dépassement -->
    <tr class="{{$view}}">
      <th>
        {{mb_label object=$acte field=rembourse}}<br />
        <small><em>({{tr}}CCodeCCAM.remboursement.{{$curr_code->remboursement}}{{/tr}})</em></small>
      </th>
      <td>
        {{assign var=disabled value=""}}
        {{if $curr_code->remboursement == 1}}{{assign var=disabled value=0}}{{/if}}
        {{if $curr_code->remboursement == 2}}{{assign var=disabled value=1}}{{/if}}

        {{assign var=default value="1"}}
        {{if $curr_code->remboursement == 1}}{{assign var=default value=1}}{{/if}}
        {{if $curr_code->remboursement == 2}}{{assign var=default value=0}}{{/if}}
        
        {{if $can->edit || $modif_operation}}
          {{mb_field object=$acte field=rembourse disabled=$disabled default=$default}}
        {{else}}
          {{mb_value object=$acte field=rembourse}}
        {{/if}}
      </td>

      {{if $dPconfig.dPsalleOp.CActeCCAM.tarif || $subject->_class_name == "CConsultation"}}
      <th>{{mb_label object=$acte field=montant_depassement}}</th>
      <td>
        {{if $can->edit || $modif_operation}}
          {{mb_field object=$acte field=montant_depassement}}
        {{else}}
          {{mb_value object=$acte field=montant_depassement}}
        {{/if}}
      </td>
      {{/if}}
    </tr>
    {{/if}}
		
		<!-- Commentaire -->
    {{if $dPconfig.dPsalleOp.CActeCCAM.commentaire}}
    <tr class="{{$view}}">
      <th>{{mb_label object=$acte field=commentaire}}</th>
      <td class="text" colspan="3">
        {{if $can->edit || $modif_operation}}
          {{mb_field object=$acte field=commentaire}}
        {{else}}
          {{mb_value object=$acte field=commentaire}}
        {{/if}}
      </td>
    </tr>
    {{/if}}
    
    <!-- Buttons -->
    {{if $newButtons && $acte->_id && !$dPconfig.dPsalleOp.CActeCCAM.openline}}
    {{if $can->edit || $modif_operation}}
    <tr>
      <td class="button" colspan="4">
        <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {
        		onComplete: function() {
        	    ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})
        	} 
        })">
          {{tr}}Modify{{/tr}} cet acte
        </button>
      </td>
		</tr>
    {{/if}}
    {{/if}}
    
    </tbody>
    
    <!-- Code d'association -->
    <tr>
      <td colspan="10" class="text">
        {{if $acte->_id}}
        {{if $newButtons && $dPconfig.dPsalleOp.CActeCCAM.openline}}
        {{if $can->edit || $modif_operation}}
        <div style="float: right;">
        <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {
        		onComplete: function() {
        	    ActesCCAM.refreshList({{$subject->_id}},{{$subject->_praticien_id}})
        	} 
        })">
          {{tr}}Modify{{/tr}} cet acte
        </button>
        </div>
        {{/if}}
        {{/if}}
        
        {{if $can->edit || $modif_operation}}
        <select name="{{$view}}"
          onchange="setAssociation(this.value, document.forms['formActe-{{$view}}'], {{$subject->_id}}, {{$subject->_praticien_id}})">
          <option value="" {{if !$acte->code_association}}selected="selected"{{/if}}>Aucun (100%)</option>
          <option value="1" {{if $acte->code_association == 1}}selected="selected"{{/if}}>1 (100%)</option>
          <option value="2" {{if $acte->code_association == 2}}selected="selected"{{/if}}>2 (50%)</option>
          <option value="3" {{if $acte->code_association == 3}}selected="selected"{{/if}}>3 (75%)</option>
          <option value="4" {{if $acte->code_association == 4}}selected="selected"{{/if}}>4 (100%)</option>
          <option value="5" {{if $acte->code_association == 5}}selected="selected"{{/if}}>5 (100%)</option>
        </select>
        {{else}}
        {{$acte->code_association}}
        {{/if}} 
        Association pour le Dr {{$acte->_ref_executant->_view}}
        
        <strong>
        {{if $dPconfig.dPsalleOp.CActeCCAM.tarif || $subject->_class_name == "CConsultation"}}
          &mdash; {{$acte->_tarif|string_format:"%.2f"}} {{$dPconfig.currency_symbol}}
        {{/if}}
        </strong>

        {{if $acte->code_association != $acte->_guess_association}}
        <span class="tooltip-trigger" onmouseover="ObjectTooltip.createDOM(this, 'association-{{$acte->_guid}}')">
          <strong>
            {{if $acte->_guess_association}}
              ({{$acte->_guess_association}}
            {{else}}
              (Aucun
            {{/if}}
            conseillé)
          </strong>
        </span>
        <div id="association-{{$acte->_guid}}" style="display:none">
          {{tr}}CActeCCAM-regle-association-{{$acte->_guess_regle_asso}}{{/tr}}
        </div>
        {{/if}}
        
        {{if $dPconfig.dPsalleOp.CActeCCAM.tarif || $subject->_class_name == "CConsultation"}}
        &mdash;  {{mb_label object=$acte field=montant_depassement}} : {{mb_value object=$acte field=montant_depassement}}
        {{/if}}
        {{/if}}
      </td>
    </tr>    
  </table>
</form>

{{if $ajax}}
<script type="text/javascript">
  oElement = $('acte{{$key}}');
  oForm = getSurroundingForm(oElement);
  prepareForm(oForm);
</script>
{{/if}}

{{/foreach}}
{{/foreach}}
</td>
</tr>

{{foreachelse}}
<tr>
<td>
<em>Pas d'acte à coder</em>
</td>
</tr>
{{/foreach}}
</table>

{{if !$dPconfig.dPsalleOp.CActeCCAM.openline}}
<script type="text/javascript">
PairEffect.initGroup("acteEffect");
</script>
{{/if}}
