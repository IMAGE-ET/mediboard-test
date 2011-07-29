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
			<ol>
				{{foreach from=$_segment->fields item=_field}}
				  <li>
				  	<strong>{{$_field->datatype}}{{*  <span style="color: #ccc; font-weight: normal;">{{$_field->value}}</span> *}} </strong>
						<ul>
							{{foreach from=$_field->parts key=_name item=_part}}
							  <li>
							  	<span style="display: inline-block; width: 4em;">{{$_name}}</span> {{$_part}}
								</li>
							{{/foreach}}
						</ul>
					</li>
				{{/foreach}}
			</ol>
		</li>
	{{/foreach}}
</ul>
{{/foreach}}