<span style="float: right;">
{{mb_include module=system template=inc_opened_interval_date from=$_line->debut to=$_line->date_arret}}
</span>

<button class="edit notext" type="button" onclick="updateListLines('{{$category_id}}', '{{$_line->prescription_id}}', '{{$_line->_id}}');">Edit</button>

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


{{if $_line->commentaire}}
<div style="margin-left: 2em;" class="warning">{{$_line->commentaire}}</div>
{{/if}}