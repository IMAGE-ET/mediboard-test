{{mb_default var=type value=""}}
{{mb_default var=readonly value=0}}
{{mb_default var=show_all value=false}}
{{mb_default var=force_show value=false}}
{{mb_default var=callback value=""}}

{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=antecedents value=$dossier_medical->_count_antecedents_by_type}}

{{if !$show_all}}
  {{if ($antecedents.$type > 0 || $force_show)}}
    <a src="images/icons/{{$type}}.png"
       class="{{$type}} notext button
       {{if !$antecedents.$type}}opacity-40{{/if}}"
      {{if $antecedents.$type}}
        onmouseover="ObjectTooltip.createEx(this, null, null,
          {'m': 'dPpatients',
          'a': 'ajax_tooltip_atcd',
          'dossier_medical_id': '{{$dossier_medical->_id}}',
          'type': '{{$type}}'});"
      {{/if}}
      {{if !$readonly}}
        onclick="Antecedent.editAntecedents('{{$patient->_id}}', '{{$type}}', {{$callback}})"
      {{/if}}></a>
  {{/if}}
{{else}}
  {{assign var=antecedents value=$patient->_ref_dossier_medical->_ref_antecedents_by_type}}
  <div>
    <ul>
      {{foreach from=$antecedents key=name item=cat}}
        {{if ($type == "" || ($type == $name) ) && $cat|@count}}
          <li>
            <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
            <ul>
              {{foreach from=$cat item=ant}}
                <li>
                  {{if $ant->date}}
                    {{mb_value object=$ant field=date}}:
                  {{/if}}
                  {{$ant->rques}}
                </li>
              {{/foreach}}
            </ul>
          </li>
        {{/if}}
      {{/foreach}}
    </ul>
  </div>
{{/if}}