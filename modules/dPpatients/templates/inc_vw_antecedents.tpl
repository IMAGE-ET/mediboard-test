{{mb_default var=type value=""}}
{{mb_default var=readonly value=0}}

{{assign var=antecedents value=$patient->_ref_dossier_medical->_ref_antecedents_by_type}}
{{if $readonly}}
  {{if $antecedents.deficience|@count}}
    <img src="images/icons/deficience.png" onmouseover="ObjectTooltip.createDOM(this, 'div_atcd');"/>
  {{/if}}
{{else}}
  <button type="button" onclick="Antecedent.editAntecedents('{{$patient->_id}}', 'deficience', reloadAdmission)"
      class="deficience notext {{if !$antecedents.deficience|@count}}opacity-40{{/if}}"
      {{if $antecedents.deficience|@count}}
        onmouseover="ObjectTooltip.createDOM(this, 'div_atcd');"
      {{/if}}></button>
{{/if}}

<div id="div_atcd" style="display: none;">
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
