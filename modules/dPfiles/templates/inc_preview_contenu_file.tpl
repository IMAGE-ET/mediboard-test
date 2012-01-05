<!-- $Id$ -->

<!-- Dialog -->
{{if $dialog && $show_editor}}
<div class="greedyPane" style="height: 500px">
	<textarea id="htmlarea">
	  {{$includeInfosFile}}
	</textarea>
</div>

<!-- Ajax -->
{{else}}
<div class="preview greedyPane" style="white-space: normal; margin: 0 auto; font-size: 60%;  padding: 5px; width: 95%; max-width: 21cm;">
	{{$includeInfosFile|smarty:nodefaults}}
</div>	

{{/if}}
