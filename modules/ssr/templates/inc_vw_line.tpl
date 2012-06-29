<span style="float: right;">
{{mb_include module=system template=inc_opened_interval_date from=$_line->debut to=$_line->date_arret}}
</span>

{{assign var=element value=$_line->_ref_element_prescription}}
{{assign var=category value=$element->_ref_category_prescription}}

{{if !@$only_comment}}
	<strong onmouseover="ObjectTooltip.createDOM(this, 'details-{{$element->_guid}}')">
		<span class="mediuser" style="border-left-color: #{{$element->_color}}; margin-left: 10px;">
		{{$element}}
	  </span>
	</strong>
{{/if}}

<div id="details-{{$element->_guid}}" style="display: none;">
	<strong>{{mb_label object=$element field=description}}</strong>: 
	{{$element->description|default:'Aucune'|nl2br}}
</div>

{{if $_line->commentaire}}
<div style="{{if @$only_comment}}display: inline; margin-left: 10px;{{else}}margin-left: 25px;{{/if}}" class="text message">{{$_line->commentaire}}</div>
{{/if}}