{{assign var=catok value="0"}}
<form action="?" name="selection" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
  <select name="category_id" onchange="this.form.submit()">
    <option value="-1" >&mdash; Choisir une catégorie &mdash;</option>
  {{foreach from=$list_categories item=curr_category}} 
    <option value="{{$curr_category->category_id}}" {{if $curr_category->category_id == $category->category_id}}{{assign var=catok value="1"}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
  {{/foreach}}
  </select>
</form>
{{if !$catok}}
<div class="big-info">Veuillez choisir une catégorie de produits</div>
{{/if}}