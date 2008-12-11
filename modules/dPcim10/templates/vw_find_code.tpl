<script type="text/javascript">
  function clearChapter() {
    document.selection.level1.value = "";
    document.selection.level2.value = "";
  }
  function clearChapter2() {
    document.selection.level2.value = "";
  }
  function clearCodeAndKeys() {
    document.selection.code.value = "";
    document.selection.keys.value = "";
  }
</script>

<form action="?" name="selectLang" method="get" >

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />
<input type="hidden" name="keys" value="{{$keys}}" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{include file="inc_select_lang.tpl"}}
      Critères de recherche
    </th>
  </tr>
</table>

</form>

<form action="?" name="selection" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />

<table class="form">
  <tr>
    <th><label for="code" title="Début du code">Code partiel</label></th>
    <td><input type="text" class="str" name="code" value="{{$code}}" onchange="clearChapter();" /></td>
    <th><label for="level1" title="Chapitre">Chapitre</label></th>
    <td>
      <select name="level1" onchange="clearCodeAndKeys(); clearChapter2(); this.form.submit();">
        <option value="">&mdash; Recherche par chapitres</option>
        {{foreach from=$listLevel1 item=curr_chap}}
        <option value="{{$curr_chap.code}}" {{if $level1 == $curr_chap.code}}selected="selected"{{/if}}>
          {{$curr_chap.text}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="keys" title="Un ou plusieurs mots clés, séparés par des espaces">Mots clefs</label></th>
    <td><input type="text" class="str" name="keys" value="{{$keys|stripslashes}}" onchange="clearChapter();" /></td>
    <th><label for="level2" title="Sous-chapitres">Sous-chapitre</label></th>
    <td>
      <select name="level2" onchange="clearCodeAndKeys(); this.form.submit();">
        <option value="">&mdash; Recherche par sous-chapitres</option>
        {{foreach from=$listLevel2 item=curr_chap}}
        <option value="{{$curr_chap.code}}" {{if $level2 == $curr_chap.code}}selected="selected"{{/if}}>
          {{$curr_chap.text}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="search" type="submit">Rechercher</button>
    </td>
  </tr>
</table>

</form>

<table class="findCode">

  <tr>
    <th colspan="4">
      {{if $numresults == 100}}
      Plus de {{$numresults}} résultats trouvés, seuls les 100 premiers sont affichés:
      {{else}}
      {{$numresults}} résultats trouvés:
      {{/if}}
    </th>
  </tr>

  {{foreach from=$master item=curr_master key=curr_key}}
  {{if $curr_key is div by 4}}
  <tr>
  {{/if}}
    <td>
      <strong>
        <a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_full_code&amp;code={{$curr_master.code}}">{{$curr_master.code}}</a>
      </strong>
      <br />
      {{$curr_master.text}}
    </td>
  {{if ($curr_key+1) is div by 4 or ($curr_key+1) == $master|@count}}
  </tr>
  {{/if}}
  {{/foreach}}

</table>