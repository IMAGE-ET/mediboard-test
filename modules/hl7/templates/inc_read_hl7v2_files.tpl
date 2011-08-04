<style>
	ul, ol {
	  line-height: normal;
		padding-left: 3em;
	}
</style>

{{foreach from=$messages item=_message}}
<h1>{{$_message->name}} - {{$_message->version}}</h1>
<ul>
	{{mb_include module=hl7 template=inc_segment_group_children segment_group=$_message}}
</ul>
{{/foreach}}

