{{if !$tab}}
<form name="changeuser" action="?m=admin&amp;a={{$a}}" method="post" onsubmit="return checkForm(this)">
{{else}}
<form name="changeuser" action="?m=admin&amp;tab={{$tab}}" method="post" onsubmit="return checkForm(this)">
{{/if}}
<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="pref_user" value="{{$user_id}}" />
<input type="hidden" name="del" value="0" />

{{if $tab && $can->edit && $user_id}}
<a href="index.php?m={{$m}}&amp;tab=edit_prefs&amp;user_id=0" class="buttonedit">
  Editer les Préférences par Défaut
</a>
{{/if}}
<table class="form">
  <tr>
    <th colspan="2" class="title">
      {{if $user_id}}
      {{tr}}User Preferences{{/tr}} : {{$user->_view}}
      {{else}}
      {{tr}}User Preferences{{/tr}} : {{tr}}Default{{/tr}}
      {{/if}}
    </th>
  </tr>

  <tr>
    <th>
      <label for="pref_name[LOCALE]" title="Veuillez choisir le langage que vous souhaiter utiliser">{{tr}}Locale{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[LOCALE]" class="text" size="1">
        {{foreach from=$locales|smarty:nodefaults item=currLocale key=keyLocale}}
        <option value="{{$keyLocale}}" {{if $keyLocale==$prefsUser.GENERALE.LOCALE}}selected="selected"{{/if}}>
          {{tr}}{{$currLocale}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="pref_name[UISTYLE]" title="Veuillez choisir l'apparence que vous souhaiter utiliser">{{tr}}User Interface Style{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[UISTYLE]" class="text" size="1">
        {{foreach from=$styles|smarty:nodefaults item=currStyles key=keyStyles}}
        <option value="{{$keyStyles}}" {{if $keyStyles==$prefsUser.GENERALE.UISTYLE}}selected="selected"{{/if}}>
          {{tr}}{{$currStyles}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="pref_name[MenuPosition]" title="{{tr}}pref-MenuPosition-desc{{/tr}}">{{tr}}pref-MenuPosition{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[MenuPosition]">
        <option value="top"  {{if $prefsUser.GENERALE.MenuPosition == "top"  }}selected="selected"{{/if}}>{{tr}}pref-MenuPosition-top{{/tr}}</option>
        <option value="left" {{if $prefsUser.GENERALE.MenuPosition == "left" }}selected="selected"{{/if}}>{{tr}}pref-MenuPosition-left{{/tr}}</option>
      </select>
    </td>
  </tr>
    
  <tr>
    <th>
      <label for="pref_name[DEFMODULE]" title="Veuillez choisir le module par défaut à afficher">{{tr}}Module par défaut{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[DEFMODULE]" class="text" size="1">
        {{foreach from=$modules|smarty:nodefaults item=currModule key=keyModule}}
        <option value="{{$currModule->mod_name}}" {{if $currModule->mod_name==$prefsUser.GENERALE.DEFMODULE}}selected="selected"{{/if}}>
          {{tr}}module-{{$currModule->mod_name}}-court{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>  
  
  {{if $prefsUser.dPpatients}}  
  <tr>
    <th class="category" colspan="2">{{tr}}module-dPpatients-long{{/tr}}</th>
  </tr>
  <tr>
    <th>
      <label for="pref_name[DEPARTEMENT]" title="Veuillez choisir le numéro du département par défaut à utiliser">{{tr}}N° du département par défaut{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="pref_name[DEPARTEMENT]" value="{{$prefsUser.dPpatients.DEPARTEMENT}}" maxlength="3" size="4" class="num minMax|0|999"/>
    </td>
  </tr>
  {{/if}}
  
  {{if $prefsUser.dPcabinet}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-dPcabinet-long{{/tr}}</th>
  </tr>

  <tr>
    <th>
      <label for="pref_name[AFFCONSULT]" title="Type de vue par défaut des consultations">{{tr}}Vue des Consultations par défaut{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[AFFCONSULT]">
        <option value="0"{{if $prefsUser.dPcabinet.AFFCONSULT == "0"}}selected="selected"{{/if}}>Tout afficher</option>
        <option value="1"{{if $prefsUser.dPcabinet.AFFCONSULT == "1"}}selected="selected"{{/if}}>Cacher les Terminées</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[MODCONSULT]" title="Mode d'affichage des consultations">{{tr}}Mode d'affichage des Consultations{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[MODCONSULT]">
        <option value="0"{{if $prefsUser.dPcabinet.MODCONSULT == "0"}}selected="selected"{{/if}}>Classique</option>
        <option value="1"{{if $prefsUser.dPcabinet.MODCONSULT == "1"}}selected="selected"{{/if}}>Avancée</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[GestionFSE]" title="{{tr}}pref-GestionFSE-desc{{/tr}}">{{tr}}pref-GestionFSE{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[GestionFSE]">
        <option value="0"{{if $prefsUser.dPcabinet.GestionFSE == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1"{{if $prefsUser.dPcabinet.GestionFSE == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[DossierCabinet]" title="{{tr}}pref-DossierCabinet-desc{{/tr}}">{{tr}}pref-DossierCabinet{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[DossierCabinet]">
        <option value="dPcabinet" {{if $prefsUser.dPcabinet.DossierCabinet == "dPcabinet" }}selected="selected"{{/if}}>{{tr}}module-dPcabinet-court {{/tr}}</option>
        <option value="dPpatients"{{if $prefsUser.dPcabinet.DossierCabinet == "dPpatients"}}selected="selected"{{/if}}>{{tr}}module-dPpatients-court{{/tr}}</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[InterMaxDir]" title="{{tr}}pref-InterMaxDir-desc{{/tr}}">{{tr}}pref-InterMaxDir{{/tr}}</label>
    </th>
    <td>
      <input class="str" type="text" size="40" name="pref_name[InterMaxDir]" value="{{$prefsUser.dPcabinet.InterMaxDir}}" />
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[DefaultPeriod]" title="{{tr}}pref-DefaultPeriod-desc{{/tr}}">{{tr}}pref-DefaultPeriod{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[DefaultPeriod]">
        <option value="day"   {{if $prefsUser.dPcabinet.DefaultPeriod == "day"  }}selected="selected"{{/if}}>{{tr}}Period.day{{/tr}}</option>
        <option value="week"  {{if $prefsUser.dPcabinet.DefaultPeriod == "week" }}selected="selected"{{/if}}>{{tr}}Period.week{{/tr}}</option>
        <option value="month" {{if $prefsUser.dPcabinet.DefaultPeriod == "month"}}selected="selected"{{/if}}>{{tr}}Period.month{{/tr}}</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[simpleCabinet]" title="{{tr}}pref-simpleCabinet-desc{{/tr}}">{{tr}}pref-simpleCabinet{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[simpleCabinet]">
        <option value="0"   {{if $prefsUser.dPcabinet.simpleCabinet == 0 }}selected="selected"{{/if}}>avec hospitalisation</option>
        <option value="1"  {{if $prefsUser.dPcabinet.simpleCabinet == 1 }}selected="selected"{{/if}}>sans hospitalisation</option>
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="pref_name[ccam]" title="{{tr}}pref-ccam-desc{{/tr}}">{{tr}}pref-ccam{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[ccam]">
        <option value="0"   {{if $prefsUser.dPcabinet.ccam == 0 }}selected="selected"{{/if}}>Cacher</option>
        <option value="1"  {{if $prefsUser.dPcabinet.ccam == 1 }}selected="selected"{{/if}}>Visible</option>
      </select>
    </td>
  </tr>


  <tr>
    <th class="category" colspan="2">{{tr}}module-dPcabinet-long{{/tr}} - Anesth</th>
  </tr>

  <tr>
    <th>
      <label for="pref_name[AUTOADDSIGN]" title="Ajout automatique des éléments significatifs">{{tr}}Ajout automatique des éléments significatifs{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[AUTOADDSIGN]">
        <option value="0"{{if $prefsUser.dPcabinet.AUTOADDSIGN == "0"}}selected="selected"{{/if}}>Non</option>
        <option value="1"{{if $prefsUser.dPcabinet.AUTOADDSIGN == "1"}}selected="selected"{{/if}}>Oui</option>
      </select>
    </td>
  </tr>
  {{/if}}

  {{if $prefsUser.system}}
  <tr>
    <th class="category" colspan="2">{{tr}}module-system-long{{/tr}}</th>
  </tr>

  <tr>
    <th>
      <label for="pref_name[INFOSYSTEM]" title="Afficher les informations système">{{tr}}Afficher les informations système{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[INFOSYSTEM]">
        <option value="0"{{if $prefsUser.system.INFOSYSTEM == "0"}}selected="selected"{{/if}}>Cacher</option>
        <option value="1"{{if $prefsUser.system.INFOSYSTEM == "1"}}selected="selected"{{/if}}>Visible</option>
      </select>
    </td>
  </tr>
  {{/if}}
  
  
  <tr>
    <td class="button" colspan="2">
      <button type="submit" class="submit">
        {{tr}}submit{{/tr}}
      </button>
    </td>
  </tr>
</table>
</form>