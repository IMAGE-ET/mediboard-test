{{assign var=show_modal_identifiant value=$conf.dPplanningOp.CSejour.show_modal_identifiant}}
{{assign var=nda                    value=$nda_obj->_NDA_view}}
{{assign var=_doss_id               value=$nda_obj->_id}}

{{if @$hide_empty}}
  {{if $nda}}
    {{if $show_modal_identifiant && $_doss_id}}
      <a href="#1" onclick="new Url('dPsante400', 'ajax_show_id400').addParam('id400', '{{$nda}}').addParam('object_id', '{{$_doss_id}}')requestModal(400);">    
    {{/if}}
    [{{$nda}}]
    {{if $show_modal_identifiant}}
    </a>    
    {{/if}}
  {{/if}}
{{else}}
  {{if $show_modal_identifiant&& $_doss_id}}
    <a href="#1" onclick="new Url('dPsante400', 'ajax_show_id400').addParam('id400', '{{$nda}}').addParam('object_id', '{{$_doss_id}}').requestModal(400);">    
  {{/if}}
  [{{$nda|default:"-"}}]
  {{if $show_modal_identifiant}}
    </a>    
  {{/if}}
{{/if}}