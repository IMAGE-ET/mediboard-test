{{mb_script module="dPccam" script="code_ccam" ajax="true"}}
<script type="text/javascript">

  function setToNow(element) {
    element.value = "now";
    element.form.elements[element.name+"_da"].value = "Maintenant";
  }

</script>

{{assign var=confCCAM value=$conf.dPsalleOp.CActeCCAM}}

{{assign var=can_view_tarif value=true}}
{{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif}}
  {{if !$app->_ref_user->isPraticien() && !$app->_ref_user->isSecretaire()}}
    {{assign var=can_view_tarif value=false}}
  {{/if}}
{{/if}}

{{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
{{assign var=actes_ids value=$subject->_associationCodesActes.$_key.ids}}
{{unique_id var=uid_autocomplete_asso}}
<fieldset>
  <legend class="text" style="width: 95%; height: 20px; line-height: 100%;">
    {{assign var=can_delete value=1}}
    {{foreach from=$_code->activites item=_activite}}
      {{foreach from=$_activite->phases item=_phase}}
        {{if $can_delete && $_phase->_connected_acte->signe && !$can->admin}}
          {{assign var=can_delete value=0}}
        {{/if}}
      {{/foreach}}
    {{/foreach}}
    
    {{if $can_delete}}
      <button type="button" class="notext trash" style="float: right;" onclick="changeCodeToDel('{{$subject->_id}}', '{{$_code->code}}', '{{$actes_ids}}')">
        {{tr}}Delete{{/tr}}
      </button>
    {{/if}}
    <!-- Codes d'associations -->
    {{if count($_code->assos) > 0}}
      <div class="small" style="float:right;">
        <form name="addAssoCode{{$uid_autocomplete_asso}}" method="get">
          <input type="text" size="12" name="keywords" value="&mdash; {{$_code->assos|@count}} codes asso." onclick="$V(this, '');"/>
        </form>
      </div>
      <script>
        Main.add(function() {
          var form = getForm("addAssoCode{{$uid_autocomplete_asso}}");
          var url = new Url("dPccam", "ajax_autocomplete_ccam_asso");
          url.addParam("code", "{{$_code->code}}");
          url.autoComplete(form.keywords, null, {
            minChars: 2,
            dropdown: true,
            width: "250px",
            updateElement: function(selected) {
              setCodeTemp(selected.down("strong").innerHTML);
            }
          });
        });
      </script>
    {{/if}}
    <!-- Forfait spécifique -->
    <a href="#" {{if $confCCAM.contraste}}style="color: #fff;"{{/if}} onclick="CodeCCAM.show('{{$_code->code}}', '{{$subject->_class}}')">
      {{$_code->code}}
    </a>
    <span style="font-weight: normal;">
      {{$_code->libelleLong}}
    </span>
    {{if $_code->forfait}}
      <small style="color: #f00">({{tr}}CCodeCCAM.remboursement.{{$_code->forfait}}{{/tr}})</small>
    {{/if}}
  </legend>
  <table class="main">
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
          {{if $acte->signe && !$can->admin}}
          <input type="hidden" name="_locked" value="1" />
          {{/if}}
          <!-- Variable _calcul_montant_base permettant de la lancer la sauvegarde du montant de base dans le store de l'acte ccam -->
          <input type="hidden" name="_calcul_montant_base" value="1" />
          <input type="hidden" name="object_id" class="{{$acte->_props.object_id}}" value="{{$subject->_id}}" />
          <input type="hidden" name="object_class" class="{{$acte->_props.object_class}}" value="{{$subject->_class}}" />
          <input type="hidden" name="code_acte" class="{{$acte->_props.code_acte}}" value="{{$acte->code_acte}}" />
          <input type="hidden" name="code_activite" class="{{$acte->_props.code_activite}}" value="{{$acte->code_activite}}" />
          <input type="hidden" name="code_phase" class="{{$acte->_props.code_phase}}" value="{{$acte->code_phase}}" />
          <input type="hidden" name="code_association" class="{{$acte->_props.code_association}}" value="{{$acte->code_association}}" />
          {{if !$confCCAM.tarif && $subject->_class != "CConsultation" && !($subject->_class == "COperation" && $subject->_ref_salle->dh == 1)}}
            <input type="hidden" name="montant_depassement" class="{{$acte->_props.montant_depassement}}" value="{{$acte->montant_depassement}}" />
          {{/if}}
          
          {{assign var=can_view_dh value=true}}
          {{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif && $acte->_id && ($acte->_ref_executant->function_id != $app->_ref_user->function_id)}}
            {{assign var=can_view_dh value=false}}
          {{/if}}
          
          <!-- Couleur de l'acte -->
          {{if $acte->_id && ($acte->code_association == $acte->_guess_association || !$confCCAM.alerte_asso)}}
            {{assign var=bg_color value=9f9}}
          {{elseif $acte->_id}}
            {{assign var=bg_color value=fc9}}
          {{else}}
            {{assign var=bg_color value=f99}}
          {{/if}}
    
          <table class="form">
            <tr id="acte{{$key}}-trigger">
              <td colspan="10" style="width: 100%; background-color: #{{$bg_color}}; border: 2px solid {{if $_activite->numero == 4}}#44f{{else}}#ff0{{/if}};">
                <div style="float: right;">
                  {{if !$acte->_id && (!$conf.dPsalleOp.CActeCCAM.signature ||
                     ($conf.dPsalleOp.CActeCCAM.signature &&
                     ($subject instanceof CConsultation ||
                     ( ($_activite->numero == 1 && !$subject->cloture_activite_1) ||
                       ($_activite->numero == 4 && !$subject->cloture_activite_4) ))))}}
                  <button class="add" type="button" onclick="Event.stop(event);
                    {{if $acte->_anesth_associe && $subject->_class == "COperation"}}
                    if(confirm('Cet acte ne comporte pas l\'activité d\'anesthésie.\nVoulez-vous ajouter le code d\'anesthésie complémentaire {{$acte->_anesth_associe}} ?')) {
                      document.manageCodes._codes_ccam.value = '{{$acte->_anesth_associe}}';
                      ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');
                    }
                    {{/if}}
                    submitFormAjax(this.form, 'systemMsg',{onComplete: function() {
                      ActesCCAM.notifyChange({{$subject->_id}},{{$subject->_praticien_id}});
                      ActesNGAP.refreshList();}});"> Coter cet acte
                  </button>
              
                  {{else}}
                  
                  {{if $acte->signe && !$can->admin}}
                    <div class="info">Acte signé</div>
                  {{else}}
                  {{if $acte->signe}}
                    <div class="info">Acte signé</div>
                  {{/if}}
                  <button class="remove" type="button" onclick="Event.stop(event); confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}',ajax:'1'}, { onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) } )">
                    {{tr}}Delete{{/tr}} cet acte
                  </button>
                  {{/if}}
                  {{/if}}
                </div>
                Activité {{$_activite->numero}} ({{$_activite->type}}) &mdash; 
                Phase {{$_phase->phase}}
                {{if $can_view_tarif && ($confCCAM.tarif || $subject->_class == "CConsultation")}}
                <div style="font-weight: normal;">
                  <span title="Tarif de l'acte avant association">{{$acte->_tarif_sans_asso|currency}}</span>
                </div>
                {{/if}}

                <!-- {{$_phase->libelle}} -->
              </td>
            </tr>
    
            <tbody class="acteEffect" id="acte{{$key}}" {{if !$confCCAM.openline}}style="display: none;"{{/if}}>
              <!-- Ligne cosmétique -->
              <tr class="{{$key}}">
                <td class="narrow"></td>
                <td class="narrow"></td>
                <td class="narrow"></td>
                <td style="width: 100%;"></td>
              </tr>
      
              <!-- Execution -->
              <tr {{if !$can->edit}}style="display: none;"{{/if}}>
                <th>{{mb_label object=$acte field=execution}}</th>
                <td colspan="10">
                  {{if $acte->commentaire == ""}}
                    <button type="button" class="edit" style="float: right;" onclick="this.up('tbody').down('tr.commentaire').toggle()">Commentaire</button>
                  {{/if}}
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
                    {{mb_include module=mediusers template=inc_options_mediuser selected=$acte->executant_id list=$listExecutants}}
                  </select>
                </td>
              </tr>
      
              <!-- Modificateurs -->
              {{assign var=modifs_compacts value=$confCCAM.modifs_compacts}}  
              <tr class="{{$view}}">
                <th>{{mb_label object=$acte field=modificateurs}}</th>
                <td{{if !$modifs_compacts}} class="text" colspan="10"{{/if}}>
                  {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
                    <input type="checkbox" name="modificateur_{{$_mod->code}}" {{if $_mod->_checked}}checked="checked"{{/if}} />
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

                <!-- Facturable -->
                <th>
                  {{mb_label object=$acte field=facturable}}
                </th>
                <td>
                  {{mb_field object=$acte field=facturable}}
                </td>

                {{if ($acte->facturable || !$acte->_id)  && $can_view_dh && ($confCCAM.tarif || $subject->_class == "CConsultation" || ($subject->_class == "COperation" && $subject->_ref_salle->dh == 1))}}
                <th>{{mb_label object=$acte field=montant_depassement}}</th>
                <td>{{mb_field object=$acte field=montant_depassement}}</td>
                {{/if}}
                 
                 {{if $_phase->charges}}
                 <th />
                <td>
                  {{mb_field object=$acte field=charges_sup typeEnum="checkbox"}}
                  {{mb_label object=$acte field=charges_sup}}
                  ({{$_phase->charges|currency}})
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
                </tr>

                <!-- Facturable -->
                <tr class="{{$view}}">
                  <th>
                    {{mb_label object=$acte field=facturable}}
                  </th>
                  <td>
                    {{mb_field object=$acte field=facturable}}
                  </td>
                </tr>

                {{if ($acte->facturable || !$acte->_id) && $can_view_dh && ($confCCAM.tarif || $subject->_class == "CConsultation" || ($subject->_class == "COperation" && $subject->_ref_salle->dh == 1))}}
                <tr class="{{$view}}">
                  <th>{{mb_label object=$acte field=montant_depassement}}</th>
                  <td>
                    {{mb_field object=$acte field=montant_depassement}}
                    {{mb_field object=$acte field=motif_depassement emptyLabel="CActeCCAM-motif_depassement"}}
                  </td>
                </tr>
                {{/if}}
              {{/if}}

              <!-- Codes d'exonération -->
              <tr class="{{$view}}">
                <th>{{mb_label object=$acte field=exoneration}}</th>
                <td>{{mb_field object=$acte field=exoneration}}</td>
              </tr>


              <!-- ALD -->
              {{if $object->_ref_patient && $object->_ref_patient->ald}}
                <tr class="{{$view}}">
                  <th>{{mb_label object=$acte field=ald}}</th>
                  <td>{{mb_field object=$acte field=ald}}</td>
                </tr>
              {{/if}}

              <!-- Commentaire -->
              {{if $confCCAM.commentaire}}
              <tr class="{{$view}} commentaire" {{if !$acte->commentaire}}style="display: none;"{{/if}}>
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
            {{if $acte->facturable}}
            <tr>
              <td colspan="10" class="text">
                {{if $acte->_id}}
                  {{if $confCCAM.openline}}
                    <div style="float: right;">
                    <button class="modify" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {
                      onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}})
                    })">
                      {{tr}}Modify{{/tr}} cet acte
                    </button>
                    </div>
                {{/if}}
                {{if !$conf.dPsalleOp.CActeCCAM.codage_strict || ($acte->code_association != $acte->_guess_association)}}
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
                  <strong>
                  {{if !$acte->code_association}}
                    Aucun (100%)
                  {{elseif $acte->code_association == 1}}
                    1 (100%)
                  {{elseif $acte->code_association == 2}}
                    2 (50%)
                  {{elseif $acte->code_association == 3}}
                    3 (75%)
                  {{elseif $acte->code_association == 4}}
                    4 (100%)
                  {{elseif $acte->code_association == 5}}
                    5 (100%)
                  {{/if}}
                  </strong>
                {{/if}}
   
        
                <span onmouseover="ObjectTooltip.createDOM(this, 'association-{{$acte->_guid}}')">
                Association pour le Dr {{$acte->_ref_executant->_view}}
                {{if $acte->code_association != $acte->_guess_association}}
                  <strong>
                    {{if $acte->_guess_association && $acte->_guess_association != "X"}}
                      ({{$acte->_guess_association}} conseillé)
                    {{elseif !$acte->_guess_association}}
                      (Aucun conseillé)
                    {{else}}
                      (Aucune règle d'association trouvée)
                    {{/if}}
                  </strong>
                {{/if}}
                </span>
                
                <div id="association-{{$acte->_guid}}" style="display: none;">
                  {{tr}}CActeCCAM-regle-association-{{$acte->_guess_regle_asso}}{{/tr}}
                </div>
                
                {{if $can_view_tarif && ($confCCAM.tarif || $subject->_class == "CConsultation")}}
                  <strong>&mdash; {{$acte->_tarif|currency}}</strong>
                {{/if}}
                
                {{if $can_view_dh && ($confCCAM.tarif || $subject->_class == "CConsultation"  || ($subject->_class == "COperation" && $subject->_ref_salle->dh == 1))}}
                  <br />  {{mb_label object=$acte field=montant_depassement}} : {{mb_value object=$acte field=montant_depassement}}
                {{/if}}
                {{/if}}
                </td>
              </tr>
              {{/if}}
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
  </table>
</fieldset>
{{foreachelse}}
<div class="empty">
  Pas d'acte à coder
</div>
{{/foreach}}

{{if !$confCCAM.openline}}
  <script type="text/javascript">
  PairEffect.initGroup("acteEffect");
  </script>
{{/if}}
