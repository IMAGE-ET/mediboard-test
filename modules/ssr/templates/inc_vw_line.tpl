{{if $_line->debut}}
  A partir du {{mb_value object=$_line field="debut"}}
{{/if}}

{{assign var=element value=$_line->_ref_element_prescription}}
{{assign var=category value=$element->_ref_category_prescription}}
<strong onmouseover="ObjectTooltip.createDOM(this, 'details-{{$element->_guid}}')">
	<span class="mediuser" style="border-left-color: #{{$element->_color}};">
	{{$element}}
  </span>
</strong>
<div id="details-{{$element->_guid}}" style="display: none;">
<strong>{{mb_label object=$element field=description}}</strong>: 
{{$element->description|default:'Aucune'|nl2br}}
</div>

{{if $_line->date_arret}}
  jusqu'au {{mb_value object=$_line field="date_arret"}}
{{/if}}

{{if $_line->commentaire}}
<div style="margin-left: 2em;" class="warning">{{$_line->commentaire}}</div>
{{/if}}