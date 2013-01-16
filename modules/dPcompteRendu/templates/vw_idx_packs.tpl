<!--  $Id$ -->

{{mb_script module=compteRendu script=pack}}

<script>
Main.add(Pack.refreshList);
</script>

<button class="new" onclick="Pack.edit('0');">
  {{tr}}CPack-title-create{{/tr}}
</button>

<form method="get" action="?" name="Filter" onsubmit="return Pack.filter();">
  
<table class="form">
  <tr>
     <th class="category" colspan="10">Filtrer les packs</th>
  </tr>
  
  <tr>
    <th>{{mb_label class=CPack field=user_id}}</th>
    <td>
      <select name="user_id" onchange="this.form.onsubmit();">
        <option value="">&mdash; Choisir </option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$user_id}}
      </select>
    </td>

    <th>{{mb_label class=CPack field=object_class}}</th>
    <td>
      <select name="object_class" onchange="this.form.onsubmit();">
        <option value="">&mdash; Tous</option>
        {{foreach from=$classes key=_class item=_locale}}
          <option value="{{$_class}}" {{if $_class == $object_class}} selected="selected" {{/if}}>
            {{$_locale}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

</form>

<div id="list-packs">
</div>
