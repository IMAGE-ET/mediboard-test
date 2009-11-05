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

{{assign var=confCCAM value=$dPconfig.dPsalleOp.CActeCCAM}}

<table class="main">
  {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
  <tr>
    <td class="text" {{if $confCCAM.contraste}}style="border: outset 3px #000; background-color: #444"{{/if}}>
    	<!-- Codes d'associations -->
      {{if count($_code->assos) < 15}}
        <select style="float:right; width: 200px;" name="asso" onchange="setCodeTemp(this.value)">
          <option value="">&mdash; Choisir un code associé</option>
          {{foreach from=$_code->assos item=_asso}}
          <option value="{{$_asso.code}}">
            {{$_asso.code}}
            ({{$_asso.texte|truncate:35:"...":true}})
          </option>
          {{/foreach}}
        </select>
      {{/if}}
      <a href="#" {{if $confCCAM.contraste}}style="color: #fff;"{{/if}} onclick="viewCode('{{$_code->code}}', '{{$subject->_class_name}}')">
        <strong>{{$_code->code}} :</strong> 
        {{$_code->libelleLong}}
      </a>
    </td>
  </tr>
  <tr>
    <td class="text">
    {{foreach from=$_code->activites item=_activite}}
      {{foreach from=$_activite->phases item=_phase}}
        {{assign var="acte" value=$_phase->_connected_acte}}
        {{assign var="view" value=$acte->_viewUnique}}
        {{assign var="key" value="$_key$view"}}
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
        {{if !$confCCAM.tarif && $subject->_class_name != "CConsultation"}}
          <input type="hidden" name="montant_depassement" class="{{$acte->_props.montant_depassement}}" value="{{$acte->montant_depassement}}" />
        {{/if}}
  
      	<!-- Couleur de l'acte -->
        {{if $acte->_id && ($acte->code_association == $acte->_guess_association || !$confCCAM.alerte_asso)}}
          {{assign var=bg_color value=9f9}}
        {{elseif $acte->_id}}
          {{assign var=bg_color value=fc9}}
        {{else}}
          {{assign var=bg_color value=f99}}
        {{/if}}
  	
        <div style="position: absolute; right: 24px; margin-top: 4px;">
          {{if !$acte->_id}}
          <button class="add" type="button" onclick="
            {{if $acte->_anesth_associe && $subject->_class_name == "COperation"}}
            if(confirm('Cet acte ne comporte pas l\'activité d\'anesthésie.\nVoulez-vous ajouter le code d\'anesthésie complémentaire {{$acte->_anesth_associe}} ?')) {
              document.manageCodes._newCode.value = '{{$acte->_anesth_associe}}';
              ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');
            }
            {{/if}}
            submitFormAjax(this.form, 'systemMsg',{onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) });">
            Coder cet acte
          </button>
      
          {{else}}
          
          <button class="remove" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}',ajax:'1'}, { onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) } )">
            {{tr}}Delete{{/tr}} cet acte
          </button>
          {{/if}}
        </div>

        <table class="form">
          <tr id="acte{{$key}}-trigger">
            <td colspan="10" style="width: 100%; background-color: #{{$bg_color}}; border: 2px solid {{if $_activite->numero == 4}}#44f{{else}}#ff0{{/if}};">
              Activité {{$_activite->numero}} ({{$_activite->type}}) &mdash; 
              Phase {{$_phase->phase}} 
              <!-- {{$_phase->libelle}} -->
            </td>
          </tr>
  
          <tbody class="acteEffect" id="acte{{$key}}" {{if !$confCCAM.openline}}style="display: none;"{{/if}}>
            <!-- Ligne cosmétique -->
            <tr class="{{$key}}">
              <td style="width: 1%;" />
              <td style="width: 1%;" />
              <td style="width: 1%;" />
              <td style="width: 100%;" />
            </tr>
    
            <!-- Execution -->
            <tr {{if !$can->edit}}style="display: none;"{{/if}}>
              <th>{{mb_label object=$acte field=execution}}</th>
              <td class="date" colspan="10">
        	      {{mb_field object=$acte field=execution form="formActe-$view" register=true}}
              </td>
            </tr>
    
            <!-- Exécutant -->
            <tr class="{{$key}}">
              <th>{{mb_label object=$acte field=executant_id}}</th>
              <td colspan="10">
              
                {{mb_ternary var=listExecutants test=$acte->_anesth value=$listAnesths other=$listChirs}}
                <select name="executant_id" class="{{$acte->_props.executant_id}}">
                  <option value="">&mdash; Choisir un professionnel de santé</option>
                  {{foreach from=$listExecutants item=_executant}}
                  <option class="mediuser" style="border-color: #{{$_executant->_ref_function->color}};" value="{{$_executant->user_id}}" {{if $acte->executant_id == $_executant->user_id}} selected="selected" {{/if}}>
                    {{$_executant->_view}}
                  </option>
                  {{/foreach}}
                </select>
              </td>
            </tr>
    
        		<!-- Modificateurs -->
        		{{assign var=modifs_compacts value=$confCCAM.modifs_compacts}}  
            <tr class="{{$view}}">
              <th>{{mb_label object=$acte field=modificateurs}}</th>
              <td{{if !$modifs_compacts}} class="text" colspan="10"{{/if}}>
                {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
                  <input type="checkbox" name="modificateur_{{$_mod->code}}" {{if $_mod->_value}}checked="checked"{{/if}} />
                  <label for="modificateur_{{$_mod->code}}" title="{{$_mod->libelle}}">
                    {{$_mod->code}} 
                    {{if !$modifs_compacts}} : {{$_mod->libelle}}{{/if}}
                  </label>
                  {{if !$modifs_compacts}}<br />{{/if}}          
                {{foreachelse}}
                <em>{{tr}}None{{/tr}}</em>
                {{/foreach}}
              </td>
              
              {{if $modifs_compacts}}
              <th>
                {{mb_label object=$acte field=rembourse}}
              </th>
              <td>
                {{assign var=disabled value=""}}
                {{if $_code->remboursement == 1}}{{assign var=disabled value=0}}{{/if}}
                {{if $_code->remboursement == 2}}{{assign var=disabled value=1}}{{/if}}
        
                {{assign var=default value="1"}}
                {{if $_code->remboursement == 1}}{{assign var=default value=1}}{{/if}}
                {{if $_code->remboursement == 2}}{{assign var=default value=0}}{{/if}}
                
                {{mb_field object=$acte field=rembourse disabled=$disabled default=$default}}
              </td>
              
              {{if $confCCAM.tarif || $subject->_class_name == "CConsultation"}}
              <th>{{mb_label object=$acte field=montant_depassement}}</th>
              <td>{{mb_field object=$acte field=montant_depassement}}</td>
              {{/if}}
               
             	{{if $_phase->charges}}
             	<th />
              <td>
                {{mb_field object=$acte field=charges_sup typeEnum="checkbox"}}
                {{mb_label object=$acte field=charges_sup}}
                ({{$_phase->charges}}{{$dPconfig.currency_symbol}})
              </td>
              {{/if}}
            {{/if}}
            </tr>
    
            {{if !$modifs_compacts}}
            <!-- Remboursable + Dépassement -->
            <tr class="{{$view}}">
              <th>
                {{mb_label object=$acte field=rembourse}}<br />
                <small><em>({{tr}}CCodeCCAM.remboursement.{{$_code->remboursement}}{{/tr}})</em></small>
              </th>
              <td>
                {{assign var=disabled value=""}}
                {{if $_code->remboursement == 1}}{{assign var=disabled value=0}}{{/if}}
                {{if $_code->remboursement == 2}}{{assign var=disabled value=1}}{{/if}}
        
                {{assign var=default value="1"}}
                {{if $_code->remboursement == 1}}{{assign var=default value=1}}{{/if}}
                {{if $_code->remboursement == 2}}{{assign var=default value=0}}{{/if}}
                
                {{mb_field object=$acte field=rembourse disabled=$disabled default=$default}}
              </td>
              
              {{if $confCCAM.tarif || $subject->_class_name == "CConsultation"}}
              <th>{{mb_label object=$acte field=montant_depassement}}</th>
              <td>{{mb_field object=$acte field=montant_depassement}}</td>
              {{/if}}
            </tr>
            {{/if}}
		
        		<!-- Commentaire -->
            {{if $confCCAM.commentaire}}
            <tr class="{{$view}}">
              <th>{{mb_label object=$acte field=commentaire}}</th>
              <td class="text" colspan="10">{{mb_field object=$acte field=commentaire}}</td>
            </tr>
            {{/if}}
    
            <!-- Buttons -->
            {{if $acte->_id && !$confCCAM.openline}}
            <tr>
              <td class="button" colspan="10">
                <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {
                		onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}})
                })">
                  {{tr}}Modify{{/tr}} cet acte
                </button>
              </td>
        		</tr>
            {{/if}}
          </tbody>
    
          <!-- Code d'association -->
          <tr>
            <td colspan="10" class="text">
              {{if $acte->_id}}
                {{if $confCCAM.openline}}
                  <div style="float: right;">
                  <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {
                  	    ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}})
                  })">
                    {{tr}}Modify{{/tr}} cet acte
                  </button>
                  </div>
              {{/if}}
              
              <select name="{{$view}}"
                onchange="setAssociation(this.value, document.forms['formActe-{{$view}}'], {{$subject->_id}}, {{$subject->_praticien_id}})">
                <option value="" {{if !$acte->code_association}}selected="selected"{{/if}}>Aucun (100%)</option>
                <option value="1" {{if $acte->code_association == 1}}selected="selected"{{/if}}>1 (100%)</option>
                <option value="2" {{if $acte->code_association == 2}}selected="selected"{{/if}}>2 (50%)</option>
                <option value="3" {{if $acte->code_association == 3}}selected="selected"{{/if}}>3 (75%)</option>
                <option value="4" {{if $acte->code_association == 4}}selected="selected"{{/if}}>4 (100%)</option>
                <option value="5" {{if $acte->code_association == 5}}selected="selected"{{/if}}>5 (100%)</option>
              </select>
 
              Association pour le Dr {{$acte->_ref_executant->_view}}
              
              <strong>
              {{if $confCCAM.tarif || $subject->_class_name == "CConsultation"}}
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
              
              {{if $confCCAM.tarif || $subject->_class_name == "CConsultation"}}
              &mdash;  {{mb_label object=$acte field=montant_depassement}} : {{mb_value object=$acte field=montant_depassement}}
              {{/if}}
              {{/if}}
              </td>
            </tr>    
          </table>
        </form>

        {{if $ajax}}
        <script type="text/javascript">
          prepareForm($('acte{{$key}}').getSurroundingForm());
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

{{if !$confCCAM.openline}}
  <script type="text/javascript">
  PairEffect.initGroup("acteEffect");
  </script>
{{/if}}
