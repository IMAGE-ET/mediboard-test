{{mb_default var=_doss_id value=0}}
{{assign var=show_modal_identifiant value=$conf.sip.show_modal_identifiant}}
{{if "sip"|module_active && array_key_exists('CSipObjectHandler', $conf.object_handlers)}}
  {{assign var=sip_handler value=$conf.object_handlers.CSipObjectHandler}}
{{else}}
  {{assign var=sip_handler value=0}}
{{/if}}

{{if @$hide_empty}}
  {{if $num_dossier}}
    {{if $show_modal_identifiant && $sip_handler && $_doss_id}}
      <a href="#1" onclick="new Url('dPsante400', 'ajax_show_id400').addParam('id400', '{{$num_dossier}}').addParam('object_id', '{{$_doss_id}}')requestModal(400);">    
    {{/if}}
    [{{$num_dossier}}]
    {{if $show_modal_identifiant && $sip_handler }}
    </a>    
    {{/if}}
  {{/if}}
{{else}}
  {{if $show_modal_identifiant && $sip_handler && $_doss_id}}
    <a href="#1" onclick="new Url('dPsante400', 'ajax_show_id400').addParam('id400', '{{$num_dossier}}').addParam('object_id', '{{$_doss_id}}').requestModal(400);">    
  {{/if}}
  [{{$num_dossier|default:"-"}}]
  {{if $show_modal_identifiant && $sip_handler }}
    </a>    
  {{/if}}
{{/if}}