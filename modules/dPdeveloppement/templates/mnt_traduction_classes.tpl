<!-- Choix du module -->
<form action="?m={{$m}}" name="modlang" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="main">
  <tr>
    <th>
      <label for="module" title="Filtrer le module de traduction">{{tr}}CModule{{/tr}}</label>
      <select name="module" onchange="this.form.submit()">
      {{foreach from=$modules item=_module}}
      <option value="{{$_module}}" {{if $_module == $module}} selected="selected" {{/if}}>
        {{tr}}module-{{$_module}}-court{{/tr}}
      </option>
      {{/foreach}}
      </select>
    </th>
    <th>
      <label for="language">Language</label>
      <select name="language" onchange="this.form.submit();">
      {{foreach from=$locales item=langue}}
      <option value="{{$langue}}"{{if $langue == $language}}selected="selected"{{/if}}>{{tr}}language.{{$langue}}{{/tr}}</option>
      {{/foreach}}
      </select>
    </th>
  </tr>
</table>
</form>


<!-- Modification des items -->
<form action="?m=dPdeveloppement" name="translate" method="post">

<input type="hidden" name="m" value="dPdeveloppement" />
<input type="hidden" name="tab" value="mnt_traduction_classes" />
<input type="hidden" name="module" value="{{$module}}" />
<input type="hidden" name="trans[]" value="{{$trans}}" />
<input type="hidden" name="language" value="{{$language}}" />
<input type="hidden" name="dosql" value="do_translate_aed" />

<!-- Control tabs -->
<ul id="tab-classes" class="control_tabs">
  {{foreach from=$items key=class item=_item}}
  <li>
  	<a href="#class-{{$class}}" {{if $completions.$class.percent < 100}}class="wrong"{{/if}}>
  	  {{tr}}{{$class}}{{/tr}}
  	  <small>({{$completions.$class.percent}}%)</small>
  	</a>
	{{/foreach}}
	</li>
</ul>

<hr class="control_tabs" />

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create("tab-classes", true);
} );
</script>

<table class="form">
  <tr>
    <td>
      {{foreach from=$items key=class item=_item}}
            
      <table id="class-{{$class}}" class="tbl" style="display: none;">

				<!-- Encouragements -->
				<tr>
				  <td colspan="10">
			      {{if $completions.$class.percent == 0}}
			      <div class="big-error">
						  <strong>Attention...</strong>
						  <p>Cette classe n'est absolument pas traduite, on ne peut garantir un affichage utilisable pour cette classe. Merci d'y remédier au plus vite.</p>
						</div>
			      {{elseif $completions.$class.percent < 50}}
			      <div class="big-warning">
						  <strong>Important</strong>
						  <p>Cette classe est peu traduite, cela va probablement poser des problèmes d'affichage.</p>
						</div>
			      {{elseif $completions.$class.percent < 100}}
			      <div class="big-info">
						  <strong>Important</strong>
						  <p>Cette classe est en cours de traduction, c'est un bon début, il reste encore des efforts à faire !</p>
						</div>
			      {{else}}
			      <div class="big-success">
						  <strong>Félicitations !</strong>
						  <p>Cette classe est totalement traduite, ce qui est un gage de qualité manifeste !</p>
						</div>
			      {{/if}}
				  </td>
				</tr>
				
        <tr>
          <th>Nom</th>
          <th>Chaîne</th>
          <th>{{tr}}language.{{$language}}{{/tr}}</th>
          <th>{{tr}}Save{{/tr}}</th>
        </tr>
        
        <tr>
	        <th colspan="3" class="category">
	     		 {{$class}}
		    	</th>
		    	<th class="category">
 	    		 <button type="submit" class="modify notext">{{tr}}Save{{/tr}}</button>
 		   	  </th>
    		</tr>
    	
        {{foreach from=$_item key=nom item=tabTrad}}
        <tbody class="hoverable">
        {{foreach from=$tabTrad key=chaine item=trad name=trad}}
        <tr>
        	{{if $smarty.foreach.trad.first }} <td rowspan="{{$tabTrad|@count}}"> {{$nom}} </td> {{/if}}
        	<td> {{$chaine}} </td>
        	<td><input size="70" type="text" name="tableau[{{$chaine}}]" value="{{$trad}}" /></td>
        	<td />
        </tr>
        {{/foreach}}
        </tbody>	
        {{/foreach}}
       </table>

       {{foreachelse}}
       <div class="big-info">Aucune classe à traduire pour ce module</div>
       {{/foreach}}
	  </td>
  </tr>
</table>
</form>