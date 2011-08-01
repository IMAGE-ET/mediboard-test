<style>
	ul, ol {
	  line-height: normal;
		padding-left: 3em;
	}
</style>
{{foreach from=$messages item=_message}}
<h1>{{$_message->name}} - {{$_message->version}}</h1>
<ul>
	{{foreach from=$_message->segments item=_segment}}
	  <li>
	  	<strong>{{$_segment->name}}</strong>
			<ol start="0">
				{{foreach from=$_segment->fields item=_field}}
				  <li>
				  	<strong>{{$_field->datatype}}</strong>
						<ol start="0">
              {{foreach from=$_field->items key=_i item=_item}}
							  <li>
									<ol start="0">
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
		</li>
	{{/foreach}}
</ul>
{{/foreach}}

