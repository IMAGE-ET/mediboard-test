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

<script type="text/javascript">
Main.add(function () {
  getForm('modlang').elements.module.sortByLabel();
});

</script>

<!-- Control tabs -->
<ul id="tab-classes" class="control_tabs">
  {{foreach from=$items key=class item=_item}}
  <li>
    <a href="#class-{{$class}}" {{if $completions.$class.percent < 100}} class="wrong" {{/if}}>
      {{tr}}{{$class}}{{/tr}}
      <small>({{$completions.$class.percent}}%)</small>
    </a>
  </li>
  {{/foreach}}
  <li>
    <a href="#other-locales" {{if $other_locales|@count == 0}} class="empty" {{/if}}>
      Autres <small>({{$other_locales|@count}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create("tab-classes", true);
} );
</script>

<!-- Modification des items -->
{{foreach from=$items key=class item=_item}}

<form action="?m=developpement" name="translate-{{$class}}" method="post" class="prepared">

<input type="hidden" name="m" value="developpement" />
<input type="hidden" name="tab" value="mnt_traduction_classes" />
<input type="hidden" name="module" value="{{$module}}" />
<input type="hidden" name="language" value="{{$language}}" />
<input type="hidden" name="dosql" value="do_translate_aed" />

<table class="form" id="class-{{$class}}" class="tbl" style="display: none;">

  <!-- Encouragements -->
  <tr>
    <td colspan="10">
      {{if $completions.$class.percent == 0}}
      <div class="small-error">
        <strong>Attention...</strong>
        <p>Cette classe n'est absolument pas traduite, on ne peut garantir un affichage utilisable pour cette classe. Merci d'y remédier au plus vite.</p>
      </div>
      {{elseif $completions.$class.percent < 50}}
      <div class="small-warning">
        <strong>Important</strong>
        <p>Cette classe est peu traduite, cela va probablement poser des problèmes d'affichage.</p>
      </div>
      {{elseif $completions.$class.percent < 100}}
      <div class="small-info">
        <strong>Important</strong>
        <p>Cette classe est en cours de traduction, c'est un bon début, il reste encore des efforts à faire !</p>
      </div>
      {{else}}
      <div class="small-success">
        <strong>Félicitations !</strong>
        <p>Cette classe est totalement traduite, ce qui est un gage de qualité manifeste !</p>
      </div>
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <th colspan="3" class="title">
     {{$class}}
    </th>
    <th class="title">
     <button type="submit" class="modify notext">{{tr}}Save{{/tr}}</button>
    </th>
  </tr>

  <tr>
    <th>Nom</th>
    <th>Chaîne</th>
    <th>{{tr}}language.{{$language}}{{/tr}}</th>
    <th>{{tr}}Save{{/tr}}</th>
  </tr>
  
  {{foreach from=$_item key=nom item=tabTrad}}
  <tbody class="hoverable">
    {{foreach from=$tabTrad key=chaine item=trad name=trad}}
    <tr>
      {{if $smarty.foreach.trad.first }} <td rowspan="{{$tabTrad|@count}}"> {{$nom}} </td> {{/if}}
      <td>{{$chaine}}</td>
      <td>
        {{if $trad|strpos:"\n"}}
          <textarea name="s[{{$chaine}}]">{{$trad}}</textarea>
        {{else}}
          <input style="width: 100%" type="text" name="s[{{$chaine}}]" value="{{$trad}}" />
        {{/if}}
      </td>
      <td>
        <button type="button" class="down notext" tabIndex="1000" onclick="$(this).up().previous().down('input,textarea').switchMultiline()"></button>
      </td>
    </tr>
    {{/foreach}}
  </tbody>  
  {{/foreach}}

</table>

</form>

{{foreachelse}}
<div class="small-info">Aucune classe à traduire pour ce module</div>
{{/foreach}}

<!-- Modification des items -->
<form action="?m=developpement" name="translate-others" method="post" class="prepared">

<input type="hidden" name="m" value="developpement" />
<input type="hidden" name="tab" value="mnt_traduction_classes" />
<input type="hidden" name="module" value="{{$module}}" />
<input type="hidden" name="language" value="{{$language}}" />
<input type="hidden" name="dosql" value="do_translate_aed" />


<table id="other-locales" class="tbl" style="display: none;">
 <tr>
   <th colspan="2" class="title">
     Autres
   </th>
   <th class="title">
     <button type="submit" class="modify notext">{{tr}}Save{{/tr}}</button>
   </th>
 </tr>

 <tr>
   <th>Nom</th>
   <th>{{tr}}language.{{$language}}{{/tr}}</th>
   <th>{{tr}}Save{{/tr}}</th>
 </tr>

{{foreach from=$other_locales key=_key item=_trad}}
  <tr>
    <td>{{$_key}}</td>
    <td>
      {{if $_trad|strpos:"\n"}}
        <textarea name="s[{{$_key}}]">{{$_trad}}</textarea>
      {{else}}
        <input style="width: 100%" type="text" name="s[{{$_key}}]" value="{{$_trad}}" />
      {{/if}}
    </td>
    <td>
      <button type="button" class="down notext" tabIndex="1000" onclick="$(this).up().previous().down('input,textarea').switchMultiline()"></button>
    </td>
  </tr>
{{/foreach}}

{{foreach from=$empty_locales key=_key item=_trad}}
  <tr>
    <td><input type="text" name="empty_locales" value="" onkeypress="$(this).up().next().select('input,textarea').each(function(i){i.name='s['+this.value+']'}.bind(this))" /></td>
    <td>
      {{if $_trad|strpos:"\n"}}
        <textarea name="_"></textarea>
      {{else}}
        <input style="width: 100%" type="text" name="_" value="" />
      {{/if}}
    </td>
    <td>
      <button type="button" class="down notext" tabIndex="1000" onclick="$(this).up().previous().down('input,textarea').switchMultiline()"></button>
    </td>
  </tr>
{{/foreach}}
</table>

</form>