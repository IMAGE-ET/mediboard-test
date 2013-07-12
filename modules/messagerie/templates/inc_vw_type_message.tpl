{{if $subject|stripos:"Fwd:" !== false}}
  <span class="tag_head tag_head_fwd" title="{{tr}}CUserMail-fwd{{/tr}}">Fwd</span>
  {{assign var=subject value=$subject|ireplace:"FwD:":""|smarty:nodefaults}}
{{/if}}
{{if $subject|stripos:"Re:" !== false}}
  <span class="tag_head tag_head_re" title="{{tr}}CUserMail-responded{{/tr}}">Re</span>
  {{assign var=subject value=$subject|ireplace:'RE:':''|smarty:nodefaults}}
{{/if}}

{{$subject|smarty:nodefaults|truncate:100:"(...)"}}