<style>
  ul, ol {
    line-height: normal;
    padding-left: 3em;
  }
</style>

<ul>
  {{foreach from=$messages item=_message key=_key}}
    <li>
    	<a href="#1" onclick="$('message-{{$_key}}').toggle()" {{if $_message->errors}} style="color:red" {{/if}}>
    	  {{$_message->name}} - {{$_message->version}} - {{$_message->filename}}
			</a>
		</li>
  {{/foreach}}
</ul>

{{foreach from=$messages item=_message key=_key}}
<div style="display: none;" id="message-{{$_key}}">
	<h1>{{$_message->name}} - {{$_message->version}} - {{$_message->filename}}</h1>
	<ul>
		{{$_message->errors|smarty:nodefaults}}
	  {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_message}}
	</ul>
</div>
{{/foreach}}