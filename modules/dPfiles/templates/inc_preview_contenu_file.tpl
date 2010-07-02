<!-- $Id$ -->

<!-- Dialog -->
{{if $dialog}}
<div style="height: 500px">
	<textarea id="htmlarea" name="_source">
	  {{$includeInfosFile}}
	</textarea>
</div>

<!-- Ajax -->
{{else}}
<div class="preview" style="white-space: normal; margin: 0 auto; font-size: 60%;  padding: 5px; width: 400px;">
	{{$includeInfosFile|smarty:nodefaults}}
</div>	

{{/if}}
