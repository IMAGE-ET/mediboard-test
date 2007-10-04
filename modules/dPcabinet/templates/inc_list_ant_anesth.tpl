<form name="frmCopyAntecedent" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_copy_antecedent" />
<input type="hidden" name="antecedent_id" value="" />
<input type="hidden" name="object_id" value="" />
<input type="hidden" name="object_class" value="" />
</form>

<form name="frmCopyAddiction" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_copy_addiction" />
<input type="hidden" name="addiction_id" value="" />
<input type="hidden" name="object_id" value="" />
<input type="hidden" name="object_class" value="" />
</form>

<form name="frmCopyTraitement" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_copy_traitement" />
<input type="hidden" name="traitement_id" value="" />
<input type="hidden" name="object_id" value="" />
<input type="hidden" name="object_class" value="" />
</form>

{{if $dPconfig.dPcabinet.addictions}}
  {{include file="inc_consult_anesth/inc_list_addiction_anesth.tpl}}    
{{/if}}

<strong>Antécédents significatifs</strong>
<ul>
  {{foreach from=$consult_anesth->_ref_antecedents key=curr_type item=list_antecedent}}
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
        
        <button class="trash notext" type="button" onclick="Antecedent.delete(this.form, reloadAntecedentsAnesth)">
         {{tr}}delete{{/tr}}
        </button>          
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
  {{foreachelse}}
  <li><em>Pas d'antécédents</em></li>
  {{/foreach}}
</ul>
      
<strong>Traitements significatifs</strong>
<ul>
  {{foreach from=$consult_anesth->_ref_traitements item=curr_trmt}}
  <li>
    <form name="delTrmtFrm-{{$curr_trmt->_id}}" action="?m=dPcabinet" method="post">
    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_traitement_aed" />
    <input type="hidden" name="traitement_id" value="{{$curr_trmt->_id}}" />
    <button class="trash notext" type="button" onclick="Traitement.delete(this.form, reloadAntecedentsAnesth)">
        {{tr}}delete{{/tr}}
    </button>
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

<strong>Diagnostics significatifs de l'opération</strong>
<ul>
  {{foreach from=$consult_anesth->_codes_cim10 item=curr_code}}
  <li>
    <button class="trash notext" type="button" onclick="oCimAnesthField.remove('{{$curr_code->code}}')">
      {{tr}}delete{{/tr}}
    </button>
    {{$curr_code->code}}: {{$curr_code->libelle}}
  </li>
  {{foreachelse}}
  <li><em>Pas de diagnostic</em></li>
  {{/foreach}}
</ul>