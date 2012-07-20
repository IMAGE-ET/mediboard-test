<!--  $Id$ -->

{{mb_script module=compteRendu script=liste_choix}}

<script>
  Main.add(ListeChoix.filter);
</script>

<button class="new singleclick" onclick="ListeChoix.edit(0);">
  {{tr}}CListeChoix-title-create{{/tr}}
</button>
    
<form name="Filter" action="?" method="get" onsubmit="return ListeChoix.filter();">

<input type="hidden" name="m" value="{{$m}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">{{tr}}Filter{{/tr}}</th>
  </tr>
  <tr>
    <th><label for="user_id">Utilisateur</label></th>
    <td>
      <select name="user_id" onchange="this.form.onsubmit()">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$user->_id}}
      </select>
    </td>
  </tr>
</table>

</form>

<div id="list-listes_choix">
</div>
 
