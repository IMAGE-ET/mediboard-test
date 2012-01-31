<ul>
  {{foreach from=$items_by_prestation item=_items key=_prestation_id}}
    <div style="background: #fdd;">
      {{assign var=prestation value=$prestations.$_prestation_id}}
      {{$prestation->nom}}
    </div>
    {{foreach from=$_items item=_item_prestation}}
      <li data-id="{{$_item_prestation->_guid}}" style="margin-left: 1em;">
        <div>{{$_item_prestation}}</div>
      </li>
    {{/foreach}}
  {{/foreach}}
</ul>