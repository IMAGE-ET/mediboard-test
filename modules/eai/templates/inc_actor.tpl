<tr id="line_{{$_actor->_guid}}" {{if !$_actor->actif}} class="opacity-30" {{/if}}>
  <td>
    <button title="Modifier {{$_actor->_view}}" class="edit notext compact" onclick="InteropActor.editActor('{{$_actor->_guid}}');">
      {{tr}}Edit{{/tr}} {{$_actor->_view}}
    </button>
  </td>
  <td class="text">
    <a href="#" onclick="InteropActor.viewActor('{{$_actor->_guid}}', null, this);" title="Afficher l'acteur d'intégration">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_actor->_guid}}');">

      {{if $_actor instanceof CInteropReceiver}}
        <i class="fa {{if $_actor->synchronous}}fa-exchange{{else}}fa-long-arrow-right{{/if}}"></i>
      {{/if}}

         {{$_actor->_view}}
      </span>
    </a>
  </td>
  <td class="compact">{{$_actor->_ref_group->_view}}</td>
  <td>
    {{foreach from=$_actor->_ref_exchanges_sources item=_exchange_source}}
      {{if !$_actor instanceof CSenderSOAP && !$_actor instanceof CSenderMLLP && !$_actor instanceof CDicomSender}}
        {{mb_include module=system template=inc_img_status_source exchange_source=$_exchange_source
          actor_actif=$_actor->actif actor_parent_class=$_actor->_parent_class}}
      {{elseif !$_actor->actif}}
        <img class="status" src="images/icons/status_grey.png" title="{{$_exchange_source->name}}"/>
      {{/if}}
    {{/foreach}}
  </td>
</tr>
