{{assign var="color" value=#ccc}}
{{if $line->_nb_alertes}}
  {{if $line->_ref_alertes.IPC || $line->_ref_alertes.profil}}
    {{assign var="image" value="note_orange.png"}}
    {{assign var="color" value=#fff288}}
  {{/if}}  
  {{if $line->_ref_alertes.allergie || $line->_ref_alertes.interaction}}
    {{assign var="image" value="note_red.png"}}
    {{assign var="color" value=#ff7474}}
  {{/if}}  
  <img src="images/icons/{{$image}}" title="" alt="" 
       onmouseover="$('line-{{$line->_id}}').show();"
       onmouseout="$('line-{{$line->_id}}').hide();" />
{{/if}}
<div id="line-{{$line->_id}}" class="tooltip" style="display: none; background-color: {{$color}}; border-style: ridge; padding-right:5px; ">
{{foreach from=$line->_ref_alertes_text key=type item=curr_type}}
  {{if $curr_type|@count}}
    <ul>
    {{foreach from=$curr_type item=curr_alerte}}
      <li>
        <strong>{{tr}}CPrescriptionLineMedicament-alerte-{{$type}}-court{{/tr}} :</strong>
        {{$curr_alerte}}
      </li>
    {{/foreach}}
    </ul>
  {{/if}}
{{/foreach}}
</div>