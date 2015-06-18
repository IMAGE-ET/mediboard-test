{{assign var=appel value=$sejour->_ref_appels_by_type.$type}}
{{assign var=color value="gray"}}
{{if !$appel->_id}}
  {{assign var=color value="gray"}}
{{elseif $appel->etat == "realise"}}
  {{assign var=color value="green"}}
{{elseif $appel->etat == "echec"}}
  {{assign var=color value="orange"}}
{{/if}}

{{assign var=etat value="none"}}
{{if $appel->_id}}
  {{assign var=etat value=$appel->etat}}
{{/if}}

<button type="button" class="fa fa-phone notext" style="color:{{$color}} !important" title="{{tr}}CAppelSejour.etat.{{$etat}}{{/tr}}{{if $appel->commentaire}} - {{/if}}{{$appel->commentaire}}"
        onclick="Appel.edit(0, '{{$type}}', '{{$_sejour->_id}}');"></button>