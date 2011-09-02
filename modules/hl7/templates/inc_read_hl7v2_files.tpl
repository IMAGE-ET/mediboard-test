<style>
  ul, ol {
    line-height: normal;
    padding-left: 3em;
  }
</style>

<script>
	Main.add(function(){
	  Control.Tabs.create("mesaages-tab");
	});
</script>

<table class="main layout">
	<tr>
		<td style="vertical-align: top; white-space: nowrap;" class="narrow">
			<ul id="mesaages-tab" class="control_tabs_vertical small">
			  {{foreach from=$messages item=_message key=_key}}
			    <li>
			    	<a href="#message-{{$_key}}" {{if $_message->errors}} class="wrong" {{/if}} title="{{$_message->filename}}">
			    	  <strong style="float: left; margin-right: 1em;">{{$_message->name}}</strong> {{$_message->version}}
						</a>
					</li>
			  {{/foreach}}
			</ul>
		</td>

    <td class="text">
			{{foreach from=$messages item=_message key=_key}}
			<div style="display: none;" id="message-{{$_key}}">
				<h1>{{$_message->name}} - {{$_message->version}} - {{$_message->filename}}</h1>
				<ul>
					{{$_message->errors|smarty:nodefaults}}
				  {{mb_include module=hl7 template=inc_segment_group_children segment_group=$_message}}
				</ul>
			</div>
			{{/foreach}}
		</td>
  </tr>
</table>