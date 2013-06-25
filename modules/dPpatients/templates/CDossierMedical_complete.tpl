
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
      {{foreach from=$object->_ref_antecedents_by_type_appareil key=_type item=antecedents_by_appareil}}
        {{if $antecedents_by_appareil|@count}}
          {{foreach from=$antecedents_by_appareil key=_appareil item=antecedents name=foreach_atcd}}
            <strong>
              {{tr}}CAntecedent.type.{{$_type}}{{/tr}} &ndash;
              {{tr}}CAntecedent.appareil.{{$_appareil}}{{/tr}}
            </strong>
            <ul>
              {{foreach from=$antecedents item=_antecedent}}
                <li>
                  {{if $_antecedent->date}} 
                    {{mb_value object=$_antecedent field="date"}} :
                  {{/if}}
                  {{mb_value object=$_antecedent field=rques}}
                </li>
              {{/foreach}}
            </ul>
          {{/foreach}}
        {{/if}}
      {{/foreach}}
      {{if !count($object->_all_antecedents)}}
      <div class="empty">{{tr}}CAntecedent.none{{/tr}}</div>
      
      {{/if}}
    </td>

    {{if (is_array($object->_ref_traitements) && $object->_ref_traitements|@count) ||
      ($object->_ref_prescription && $object->_ref_prescription->_id && $object->_ref_prescription->_ref_prescription_lines|@count)}}
      <td class="text top">
        {{if is_array($object->_ref_traitements)}}
          {{if $object->_ref_traitements|@count}}<ul>{{/if}}
            {{foreach from=$object->_ref_traitements item=_traitement}}
              <li>
                {{mb_include module=system template=inc_interval_date_progressive object=$_traitement from_field=debut to_field=fin}}:
                {{mb_value object=$_traitement field=traitement}}
              </li>
            {{/foreach}}
          {{if $object->_ref_traitements|@count}}</ul>{{/if}}
        {{/if}}
        
        {{if $object->_ref_prescription}}
          {{assign var=prescription value=$object->_ref_prescription}}
          {{if $object->_ref_prescription->_id && $prescription->_ref_prescription_lines|@count}}
            {{mb_script module=prescription script=prescription ajax=true}}
            {{if (is_array($object->_ref_traitements) && $object->_ref_traitements|@count)}}
              <hr style="width: 50%" />
            {{/if}}
            <ul>
              {{foreach from=$prescription->_ref_prescription_lines item=_line}}
                <li>
                  {{if $_line->debut || $_line->fin}} 
                    {{mb_include module=system template=inc_interval_date from=$_line->debut to=$_line->fin}} :
                  {{/if}}
                  <a href="#1" onclick="Prescription.showMonographyMedicament(null,'{{$_line->code_ucd}}','{{$_line->code_cis}}');">
                    <span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}', 'objectView');">
                      {{$_line->_ucd_view}}
                    </span>
                  </a>
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
        {{/if}}
        
      </td>
    {{else}}
      <td class="top">
        <div class="empty">{{tr}}CTraitement.none{{/tr}}</div>
      </td>
    {{/if}}
    
    <td class="text top">
      {{if $object->_ext_codes_cim|@count}}<ul>{{/if}}
      {{foreach from=$object->_ext_codes_cim item=_code}}
        <li>
          <strong>{{$_code->code}}:</strong> {{$_code->libelle}}
        </li>
      {{foreachelse}}
        <div class="empty">{{tr}}CDiagnostic.none{{/tr}}</div>
      {{/foreach}}
      {{if $object->_ext_codes_cim|@count}}</ul>{{/if}}
    </td>
  </tr>
</table>
