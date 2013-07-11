{{if $subject|stripos:"Fwd:" !== false}}
  <span class="tag_head tag_head_fwd" title="{{tr}}CUserMail-fwd{{/tr}}">Fwd</span>
  {{assign var=subject value=$subject|replace:'Fwd:':''}}
  {{assign var=subject value=$subject|replace:'FWD:':''}}
{{/if}}
{{if $subject|stripos:"Re:" !== false}}
  <span class="tag_head tag_head_re" title="{{tr}}CUserMail-responded{{/tr}}">Re</span>
  {{assign var=subject value=$subject|replace:'Re:':''}}
  {{assign var=subject value=$subject|replace:'RE:':''}}
{{/if}}

{{$subject|truncate:100:"(...)"|smarty:nodefaults}}