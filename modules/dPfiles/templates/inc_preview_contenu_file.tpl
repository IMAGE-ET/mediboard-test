<!-- $Id$ -->

<!-- Dialog -->
{{if $dialog}}
<div class="greedyPane" style="height: 500px">
	<textarea id="htmlarea">
	  {{$includeInfosFile}}
	</textarea>
</div>

<!-- Ajax -->
{{else}}
<div class="preview greedyPane" style="white-space: normal; margin: 0 auto; font-size: 60%;  padding: 5px; width: 400px;">
	{{$includeInfosFile|smarty:nodefaults}}
</div>	

{{/if}}
