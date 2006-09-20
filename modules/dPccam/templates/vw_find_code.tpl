<form  name="selection" action="?" method="get" >

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="{{$actionType}}" value="{{$action}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />

<table class="form">
  <tr>
    <th class="category" colspan="4">Critères de recherche</th>
  </tr>

  <tr>
    <th><label for="code" title="Code CCAM partiel ou complet">Code Partiel</label></th>
    <td><input tabindex="1" type="text" name="code" value="{{$code}}" maxlength="7" /></td>
    <th><label for="selacces" title="Voie d'accès concerné par le code CCAM">Voie d'accès</label></th>
    <td>
      <select tabindex="3" name="selacces" onchange="this.form.submit()">
        <option value="">&mdash; Choisir une voie d'accès</option>
        {{foreach from=$acces item=curr_acces}}
        <option value="{{$curr_acces.code}}" {{if $curr_acces.code == $selacces}} selected="selected" {{/if}}>
          {{$curr_acces.texte|escape|lower}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th><label for="clefs" title="mots clés séparés par des espaces">Mots clefs</label></th>
    <td><input tabindex="2" type="text" name="clefs" value="{{$clefs}}" /></td>
    <th><label for="seltopo1" title="Appareil concerné par le code CCAM">Appareil</label></th>
    <td>
      <select tabindex="4" name="seltopo1" onchange="this.form.submit()">
        <option value="">&mdash; Choisir un appareil</option>
        {{foreach from=$topo1 item=curr_topo1}}
        <option value="{{$curr_topo1.code}}" {{if $curr_topo1.code == $seltopo1}} selected="selected" {{/if}}>
          {{$curr_topo1.texte|escape|lower}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="2">
      <button class="search" tabindex="7" type="submit">Rechercher</button>
    </td>
    <th><label for="seltopo2" title="Système concerné par le code CCAM">Système</label></th>
    <td>
      <select tabindex="5" name="seltopo2" onchange="this.form.submit()">
        <option value="">&mdash; Choisir un système</option>
        {{foreach from=$topo2 item=curr_topo2}}
        <option value="{{$curr_topo2.code}}" {{if $curr_topo2.code == $seltopo2}} selected="selected" {{/if}}>{{$curr_topo2.texte|escape}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>

</table>

</form>

<table class="findCode">

  <tr>
    <th colspan="4">
      {{if $numcodes == 100}}
      Plus de {{$numcodes}} résultats trouvés, seuls les 100 premiers sont affichés :
      {{else}}
      {{$numcodes}} résultats trouvés :
      {{/if}}
    </th>
  </tr>

  {{foreach from=$codes item=curr_code key=curr_key}}
  {{if $curr_key is div by 4}}
  <tr>
  {{/if}}
    <td>
      <strong>
        <a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_full_code&amp;codeacte={{$curr_code.code}}">
          {{$curr_code.code}}
        </a>
      </strong>
      <br />
      {{$curr_code.texte}}
    </td>
  {{if ($curr_key+1) is div by 4 or ($curr_key+1) == $codes|@count}}
  </tr>
  {{/if}}
  {{/foreach}}

</table>
