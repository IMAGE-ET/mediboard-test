
{{foreach from=$segment_group->children item=_child}}
  <li>
  	{{if $_child instanceof CHL7v2Segment}}
	  	<strong>{{$_child->name}}</strong> - <span style="color: #999">{{$_child->description}}</span>
			<ol start="1">
				{{foreach from=$_child->fields item=_field}}
				  <li>
				  	<strong>{{$_field->datatype}}</strong> - <span style="color: #999">{{$_field->description}}</span>
						<ol start="1">
	            {{foreach from=$_field->items key=_i item=_item}}
							  <li value="{{$_i}}">
									<ol start="1">
									{{foreach from=$_item->components key=_name item=_part}}
									  <li>
									  	{{$_part}}
										</li>
									{{/foreach}}
									</ol>
								</li>
							{{/foreach}}
						</ol>
						 
					</li>
				{{/foreach}}
			</ol>
		{{else}}
		  <ul>
		  	{{$_child->name}}
		    {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_child}}
      </ul>
		{{/if}}
	</li>
{{/foreach}}
