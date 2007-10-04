<script type="text/javascript">

var Antecedent = {
  delete: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'cet antécédent',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  }
}

var Traitement = {
  delete: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'ce traitement',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  }
}

</script>

     {{if $dPconfig.dPcabinet.addictions}}
        {{include file="inc_consult_anesth/inc_list_addiction.tpl}}    
      {{/if}}
      
      <strong>Antécédents du patient</strong>
      <ul>
      {{if $patient->_ref_antecedents}}
        {{foreach from=$patient->_ref_antecedents key=curr_type item=list_antecedent}}
        {{if $list_antecedent|@count}}
        <li>
          {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
          {{foreach from=$list_antecedent item=curr_antecedent}}
          <ul>
            <li>
              <form name="delAntFrm-{{$curr_antecedent->_id}}" action="?m=dPcabinet" method="post">

              <input type="hidden" name="m" value="dPpatients" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_antecedent_aed" />
              <input type="hidden" name="antecedent_id" value="{{$curr_antecedent->_id}}" />
              
              <button class="trash notext" type="button" onclick="Antecedent.delete(this.form, reloadAntecedents)">
                {{tr}}delete{{/tr}}
              </button> 
              {{if $_is_anesth}}
              <button class="add notext" type="button" onclick="copyAntecedent({{$curr_antecedent->_id}})">
                {{tr}}add{{/tr}}
              </button>
              {{/if}}         
              {{if $curr_antecedent->date}}
                {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
              {{/if}}
              {{$curr_antecedent->rques}}
            </form>
            </li>
          </ul>
          {{/foreach}}
        </li>
        {{/if}}
        {{/foreach}}
      {{else}}
        <li><em>Pas d'antécédents</em></li>
      {{/if}}
      </ul>
      <strong>Traitements du patient</strong>
      <ul>
        {{foreach from=$patient->_ref_traitements item=curr_trmt}}
        <li>
          <form name="delTrmtFrm" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_traitement_aed" />
          <input type="hidden" name="traitement_id" value="{{$curr_trmt->traitement_id}}" />
          <button class="trash notext" type="button" onclick="Traitement.delete(this.form, reloadAntecedents)">
            {{tr}}delete{{/tr}}
          </button>
          {{if $_is_anesth}}
          <button class="add notext" type="button" onclick="copyTraitement({{$curr_trmt->traitement_id}})">
            {{tr}}add{{/tr}}
          </button>
          {{/if}}
          {{if $curr_trmt->fin}}
            Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
          {{elseif $curr_trmt->debut}}
            Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
          {{/if}}
          {{$curr_trmt->traitement}}
          </form>
        </li>
        {{foreachelse}}
        <li><em>Pas de traitements</em></li>
        {{/foreach}}
      </ul>
      <strong>Diagnostics du patient</strong>
      {{$patient->listCim10}}
      <ul>
        {{foreach from=$patient->_codes_cim10 item=curr_code}}
        <li>
          <button class="trash notext" type="button" onclick="oCimField.remove('{{$curr_code->code}}')">
            {{tr}}delete{{/tr}}
          </button>
          {{if $_is_anesth}}
          <button class="add notext" type="button" onclick="oCimAnesthField.add('{{$curr_code->code}}')">
            {{tr}}add{{/tr}}
          </button>
          {{/if}}
          {{$curr_code->code}}: {{$curr_code->libelle}}
        </li>
        {{foreachelse}}
        <li><em>Pas de diagnostic</em></li>
        {{/foreach}}
      </ul>