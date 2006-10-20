{{if !$tab}}
<form name="changeuser" action="./index.php?m=admin&amp;a={{$a}}" method="post" onsubmit="return checkForm(this)">
{{else}}
<form name="changeuser" action="./index.php?m=admin&amp;tab={{$tab}}" method="post" onsubmit="return checkForm(this)">
{{/if}}
<input type="hidden" name="dosql" value="do_preference_aed" />
<input type="hidden" name="pref_user" value="{{$user_id}}" />
<input type="hidden" name="del" value="0" />

{{if $tab && $canEdit && $user_id}}
<a href="index.php?m={{$m}}&amp;tab=edit_prefs&amp;user_id=0" class="buttonedit">
  Editer les Pr�f�rences par D�faut
</a>
{{/if}}
<table class="form">
  <tr>
    <th colspan="2" class="title">
      {{tr}}User Preferences{{/tr}}: 
      {{if $user_id}}
        {{$user->_view}}
      {{else}}
        {{tr}}Default{{/tr}}
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
      <label for="pref_name[DEFMODULE]" title="Veuillez choisir le module par d�faut � afficher">{{tr}}Module par d�faut{{/tr}}</label>
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
    <th>
      <label for="pref_name[DEPARTEMENT]" title="Veuillez choisir le num�ro du d�partement par d�faut � utiliser">{{tr}}N� du d�partement par D�faut{{/tr}}</label>
    </th>
    <td>
      <input type="text" name="pref_name[DEPARTEMENT]" value="{{$prefsUser.dPpatients.DEPARTEMENT}}" maxlength="3" size="4" title="num|minMax|0|999"/>
    </td>
  </tr>
  {{/if}}
  
  {{if $prefsUser.dPcabinet}}
  <tr>
    <th>
      <label for="pref_name[AFFCONSULT]" title="Type de vue par d�faut des consultations">{{tr}}Vue des Consultations par d�faut{{/tr}}</label>
    </th>
    <td>
      <select name="pref_name[AFFCONSULT]">
        <option value="0"{{if $prefsUser.dPcabinet.AFFCONSULT == "0"}}selected="selected"{{/if}}>Tout afficher</option>
        <option value="1"{{if $prefsUser.dPcabinet.AFFCONSULT == "1"}}selected="selected"{{/if}}>Cacher les Termin�es</option>
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
        <option value="1"{{if $prefsUser.dPcabinet.MODCONSULT == "1"}}selected="selected"{{/if}}>Avanc�e</option>
      </select>
    </td>
  </tr>
  {{/if}}

  {{if $prefsUser.system}}
  <tr>
    <th>
      <label for="pref_name[INFOSYSTEM]" title="Afficher les informations syst�me">{{tr}}Afficher les informations syst�me{{/tr}}</label>
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