{{mb_default var=type value=""}}
{{mb_default var=readonly value=0}}
{{mb_default var=show_all value=false}}

{{assign var=antecedents value=$patient->_ref_dossier_medical->_ref_antecedents_by_type}}
{{if !$show_all && isset($antecedents.$type|smarty:nodefaults)}}
  {{if $readonly}}
    <img src="images/icons/{{$type}}.png" {{if !$antecedents.$type|@count}}class="opacity-40"{{/if}}
      onmouseover="ObjectTooltip.createDOM(this, 'div_atcd_{{$patient->_id}}');"/>
  {{else}}
    <button type="button" onclick="Antecedent.editAntecedents('{{$patient->_id}}', '{{$type}}', reloadAdmission)"
      class="{{$type}} notext {{if !$antecedents.$type|@count}}opacity-40{{/if}}"
      {{if $antecedents.$type|@count}}
        onmouseover="ObjectTooltip.createDOM(this, 'div_atcd_{{$patient->_id}}');"
      {{/if}}>
    </button>
  {{/if}}
{{/if}}

<div id="div_atcd_{{$patient->_id}}" {{if !$show_all}} style="display: none;" {{/if}}>
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
