<ul style="text-align: left;">
  {{foreach from=$packs item=_pack}}
    <li>
      {{if $_pack->_owner == "user"}}
        {{assign var=owner_icon value="user"}}
      {{elseif $_pack->_owner == "func"}}
        {{assign var=owner_icon value="user-function"}}
      {{else}}
        {{assign var=owner_icon value="group"}}
      {{/if}}
      <img style="float:right; clear: both;" 
        src="images/icons/{{$owner_icon}}.png" />
      <div>{{$_pack->nom|emphasize:$keywords}}</div>
      <div style="display: none;" class="id">{{$_pack->_id}}</div>
    </li>
  {{/foreach}}
</ul>