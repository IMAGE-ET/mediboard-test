<script type="text/javascript">
  
function selectCode(code) {
  window.opener.setCode(code, "cim10");
  window.close();
}

</script>

<table class="fullCode">
  <tr>
    <th colspan="2">
      <form action="index.php" name="selectLang" method="get" >

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />
      <input type="hidden" name="code" value="{{$cim10->code}}" />

      <select name="lang" style="float:right;" onchange="this.form.submit()">
        <option value="{{$cim10|const:'LANG_FR'}}" {{if $lang == $cim10|const:'LANG_FR'}}selected="selected"{{/if}}>
          Français
        </option>
        <option value="{{$cim10|const:'LANG_EN'}}" {{if $lang == $cim10|const:'LANG_EN'}}selected="selected"{{/if}}>
          English
        </option>
        <option value="{{$cim10|const:'LANG_DE'}}" {{if $lang == $cim10|const:'LANG_DE'}}selected="selected"{{/if}}>
          Deutsch
        </option>
      </select>


      <h1>&ldquo;{{$cim10->libelle}}&rdquo;</h1>

      </form>
    </th>
  </tr>
  
  <tr>
    <td class="leftPane">
      <form action="?" name="selection" method="get" onsubmit="return checkForm(this)">
      
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />

      <table class="form">
        <tr>
          <th><label for="code" title="Code total ou partiel de l'acte">Code de l'acte</label></th>
          <td>
            <input tabindex="1" type="text" title="str" name="code" value="{{$cim10->code|stripslashes}}" />
            <button tabindex="2" class="search" type="submit">Afficher</button>
          </td>
        </tr>
      </table>

      </form>
    </td>
     
    <td class="rightPane">
    {{if $canEdit}}
      <form name="addFavoris" action="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}" method="post">
      
      <input type="hidden" name="dosql" value="do_favoris_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="favoris_code" value="{{$cim10->code}}" />
      <input type="hidden" name="favoris_user" value="{{$user}}" />
      <button class="submit" type="submit" name="btnFuseAction">
        Ajouter à mes favoris
      </button>
      
      </form>
    {{/if}}
 
    {{if $dialog}}
    <button class="tick" type="button" onclick="selectCode('{{$cim10->code}}')">
      Sélectionner ce code
    </button>
    {{/if}}
    </td>
  </tr>

  {{if $cim10->_isInfo}}
  <tr>
    <td class="pane" colspan="2">
      <strong>Informations sur ce code:</strong>
      <ul>
        {{if $cim10->descr|@count}}
        <li>
          Description:
          <ul>
            {{foreach from=$cim10->descr|smarty:nodefaults item=curr_descr}}
            <li>{{$curr_descr}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->_exclude|@count}}
        <li>
          Exclusions:
          <ul>
            {{foreach from=$cim10->_exclude item=curr_exclude}}
            <li><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_full_code&amp;code={{$curr_exclude->code}}"><strong>{{$curr_exclude->code}}</strong></a>: {{$curr_exclude->libelle}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->glossaire|@count}}
        <li>
          Glossaire:
          <ul>
            {{foreach from=$cim10->glossaire|smarty:nodefaults item=curr_glossaire}}
            <li>{{$curr_glossaire}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->include|@count}}
        <li>
          Inclusions:
          <ul>
            {{foreach from=$cim10->include|smarty:nodefaults item=curr_include}}
            <li>{{$curr_include}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->indir|@count}}
        <li>
          Exclusions indirectes:
          <ul>
            {{foreach from=$cim10->indir|smarty:nodefaults item=curr_indir}}
            <li>{{$curr_indir}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
        {{if $cim10->notes|@count}}
        <li>
          Notes:
          <ul>
            {{foreach from=$cim10->notes|smarty:nodefaults item=curr_note}}
            <li>{{$curr_note}}</li>
            {{/foreach}}
          </ul>
        </li>
        {{/if}}
      </ul>
    </td>
  </tr>
  {{/if}}

  <tr>
    {{if $cim10->_levelsSup|@count}}
    <td class="pane">
      <strong>Codes de niveau supérieur:</strong>
      <ul>
        {{foreach from=$cim10->_levelsSup item=curr_level}}
        {{if $curr_level->sid != 0}}
        <li><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_full_code&amp;code={{$curr_level->code}}"><strong>{{$curr_level->code}}</strong></a>: {{$curr_level->libelle}}</li>
        {{/if}}
        {{/foreach}}
      </ul>
    </td>
    {{/if}}
    {{if $cim10->_levelsInf|@count}}
    <td class="pane">
      <strong>Codes de niveau inferieur :</strong>
      <ul>
        {{foreach from=$cim10->_levelsInf item=curr_level}}
        {{if $curr_level->sid != 0}}
        <li><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}=vw_full_code&amp;code={{$curr_level->code}}"><strong>{{$curr_level->code}}</strong></a>: {{$curr_level->libelle}}</li>
        {{/if}}
        {{/foreach}}
      </ul>
    </td>
    {{/if}}
  </tr>
</table>