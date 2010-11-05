{{if $type == "DA"}}
  {{assign var="type" value="_added_code_cim"}}
{{/if}}
<ul style="text-align: left;">
  {{foreach from=$codes item=_code}}
    <li onclick="
      {{if $type == 'urg'}}
        $V(getForm('editSejour').DP, '{{$_code.code}}'); submitSejour(); reloadDiagnostic('{{$sejour_id}}', 1);
      {{elseif $type == 'cab'}}
        $V(getForm('addDiagFrm').code_diag, '{{$_code.code}}');
      {{elseif $type == "edit_sejour"}}
        $V(getForm('editSejour').DP, '{{$_code.code}}');
      {{elseif $type == "edit_protocole"}}
        $V(getForm('editFrm').DP, '{{$_code.code}}');
      {{elseif $type == "editDP"}}
        $V(getForm('editDP').DP, '{{$_code.code}}');
      {{elseif $type == "editDR"}}
        $V(getForm('editDR').DR, '{{$_code.code}}');
      {{elseif $type == "editDA"}}
        $V(getForm('editDA')._added_code_cim, '{{$_code.code}}');
      {{/if}}">{{$_code.code}}<br/>
      <div style="margin-left: 15px; color: #888">
        {{$_code.text|spancate:40}}
      </div>
    </li>
  {{/foreach}}
</ul>
