
<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      Dossier médical du 
      {{tr}}{{$object->object_class}}{{/tr}}
    </th>
  </tr>
  <tr>
    <th>Antécédent(s)</th>
    {{if is_array($object->_ref_traitements)}}
    <th>Traitement(s)</th>
    {{/if}}
    <th>Diagnostic(s)</th>
  </tr>
  
  <tr>
    <td class="text top">
      {{foreach from=$object->_ref_antecedents key=curr_type item=list_antecedent}}
      {{if $list_antecedent|@count}}
      <strong>
        {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
      </strong>
      <ul>
        {{foreach from=$list_antecedent item=curr_antecedent}}
        <li>
          {{mb_value object=$curr_antecedent field="date"}}
          {{mb_value object=$curr_antecedent field="rques"}}
        </li>
        {{/foreach}}
      </ul>
      {{/if}}
      {{/foreach}}
      {{if !count($object->_all_antecedents)}}
      <div class="empty">{{tr}}CAntecedent.none{{/tr}}</div>
      
      {{/if}}
    </td>
    
    {{if is_array($object->_ref_traitements)}}
    <td class="text top">
      {{if $object->_ref_traitements|@count}}<ul>{{/if}}
      {{foreach from=$object->_ref_traitements item=curr_traitement}}
        <li>
          {{if $curr_traitement->fin}}
            Depuis {{mb_value object=$curr_traitement field="debut"}}
            jusqu'à {{mb_value object=$curr_traitement field="fin"}} :
          {{elseif $curr_traitement->debut}}
            Depuis {{mb_value object=$curr_traitement field="debut"}} :
          {{/if}}
          {{mb_value object=$curr_traitement field="traitement"}}
        </li>
      {{foreachelse}}
        <div class="empty">{{tr}}CTraitement.none{{/tr}}</div>
      {{/foreach}}
      {{if $object->_ref_traitements|@count}}</ul>{{/if}}
    </td>
    {{/if}}
    
    <td class="text top">
      {{if $object->_ext_codes_cim|@count}}<ul>{{/if}}
      {{foreach from=$object->_ext_codes_cim item=curr_code}}
        <li>
          <strong>{{$curr_code->code}}:</strong> {{$curr_code->libelle}}
        </li>
      {{foreachelse}}
        <div class="empty">{{tr}}Aucun diagnostic{{/tr}}</div>
      {{/foreach}}
      {{if $object->_ext_codes_cim|@count}}</ul>{{/if}}
    </td>
  </tr>
</table>
