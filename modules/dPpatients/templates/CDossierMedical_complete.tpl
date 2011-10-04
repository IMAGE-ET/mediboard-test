
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
      {{foreach from=$object->_ref_antecedents_by_type_appareil key=curr_type item=antecedents_by_appareil}}
        {{if $antecedents_by_appareil|@count}}
          <strong>
            {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
          </strong>
          <div style="margin-left: 1em;">
            <ul>
              {{foreach from=$antecedents_by_appareil key=curr_appareil item=antecedents name=foreach_atcd}}
                <li><strong>{{tr}}CAntecedent.appareil.{{$curr_appareil}}{{/tr}}</strong></li>
                <ul>
                  {{foreach from=$antecedents item=_antecedent}}
                    <li>
                      {{mb_value object=$_antecedent field="date"}}
                      {{mb_value object=$_antecedent field="rques"}}
                    </li>
                  {{/foreach}}
                </ul>
              {{/foreach}}
            </ul>
          </div>
        {{/if}}
      {{/foreach}}
      {{if !count($object->_all_antecedents)}}
      <div class="empty">{{tr}}CAntecedent.none{{/tr}}</div>
      
      {{/if}}
    </td>

    {{if (is_array($object->_ref_traitements) && $object->_ref_traitements|@count) ||
      ($object->_ref_prescription->_id && $object->_ref_prescription->_ref_prescription_lines|@count)}}
      <td class="text top">
        {{if is_array($object->_ref_traitements)}}
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
            {{/foreach}}
          {{if $object->_ref_traitements|@count}}</ul>{{/if}}
        {{/if}}
        
        {{assign var=prescription value=$object->_ref_prescription}}
        {{if $object->_ref_prescription->_id && $prescription->_ref_prescription_lines|@count}}
          {{mb_script module=dPprescription script=prescription ajax=true}}
          {{if (is_array($object->_ref_traitements) && $object->_ref_traitements|@count)}}
            <hr style="width: 50%" />
          {{/if}}
          <ul>
            {{foreach from=$prescription->_ref_prescription_lines item=_line}}
              <li>
                {{if $_line->fin}}
                  Du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->fin|date_format:"%d/%m/%Y"}} :
                {{elseif $_line->debut}}
                  Depuis le {{$_line->debut|date_format:"%d/%m/%Y"}} :
                {{/if}}
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView')">
                  <a href=#1 onclick="Prescription.viewProduit(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');">
                    {{$_line->_ucd_view}}
                  </a>
                </span>
                {{if $_line->_ref_prises|@count}}
                  ({{foreach from=`$_line->_ref_prises` item=_prise name=foreach_prise}}
                    {{$_prise}}
                    {{if !$smarty.foreach.foreach_prise.last}},{{/if}}
                  {{/foreach}})
                {{/if}}
              </li>
            {{/foreach}}
          </ul>
        {{/if}}
      </td>
    {{else}}
      <td>
        <div class="empty">{{tr}}CTraitement.none{{/tr}}</div>
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
