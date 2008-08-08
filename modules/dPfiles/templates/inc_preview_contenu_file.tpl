<!-- $Id: $ -->

<!-- Dialog -->
{{if $dialog}}
<div style="height: 500px">
	<textarea id="htmlarea" name="source">
	  {{$includeInfosFile}}
	</textarea>
</div>

<!-- Ajax -->
{{else}}
<div style="margin: 0 auto;	font-size: 70%; width: 400px; background: #fff;">
	{{$includeInfosFile|smarty:nodefaults}}
</div>	

{{/if}}
