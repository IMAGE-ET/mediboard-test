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
    <th><label for="keys" title="Un ou plusieurs mots clés, séparés par des espaces. Obligatoire">Mots clefs</label></th>
    <td><input type="text" title="str" name="keys" value="{{$keys|stripslashes}}" /></td>
  </tr>
  <tr>
    <td class="button" colspan="2">
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