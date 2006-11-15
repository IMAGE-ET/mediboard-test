<form name="editConfigdPplanningOp" action="./index.php?m={{$m}}&amp;a=configure" method="post" onSubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />
<table class="form">
  <tr>
    <th class="category" colspan="6">{{tr}}COperation{{/tr}}</th>
  </tr>
  
  <tr>
    <th>
      <label for="dPplanningOp[operation][duree_deb]" title="{{tr}}config-dPplanningOp-COperation-duree_deb{{/tr}}">{{tr}}config-dPplanningOp-COperation-duree_deb{{/tr}}</label>  
    </th>
    <td>
      <select title="num" name="dPplanningOp[operation][duree_deb]">
      {{foreach from=$listHours item=currHours}}
        <option value="{{$currHours}}"{{if $currHours==$configOper.duree_deb}}selected="selected"{{/if}}>{{$currHours}}</option>
      {{/foreach}}
      </select>
    </td>
    <th>
      <label for="dPplanningOp[operation][hour_urgence_deb]" title="{{tr}}config-dPplanningOp-COperation-hour_urgence_deb{{/tr}}">{{tr}}config-dPplanningOp-COperation-hour_urgence_deb{{/tr}}</label>  
    </th>
    <td>
      <select title="num" name="dPplanningOp[operation][hour_urgence_deb]">
      {{foreach from=$listHours item=currHours}}
        <option value="{{$currHours}}"{{if $currHours==$configOper.hour_urgence_deb}}selected="selected"{{/if}}>{{$currHours}}</option>
      {{/foreach}}
      </select>
    </td>
    <th>
      <label for="dPplanningOp[operation][min_intervalle]" title="{{tr}}config-dPplanningOp-COperation-min_intervalle{{/tr}}">{{tr}}config-dPplanningOp-COperation-min_intervalle{{/tr}}</label>  
    </th>
    <td>
      <select title="num" name="dPplanningOp[operation][min_intervalle]">
      {{html_options options=$listInterval selected=$configOper.min_intervalle}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th>
      <label for="dPplanningOp[operation][duree_fin]" title="{{tr}}config-dPplanningOp-COperation-duree_fin{{/tr}}">{{tr}}config-dPplanningOp-COperation-duree_fin{{/tr}}</label>  
    </th>
    <td>
      <select title="num|moreEquals|dPplanningOp[operation][duree_deb]" name="dPplanningOp[operation][duree_fin]">
      {{foreach from=$listHours item=currHours}}
        <option value="{{$currHours}}"{{if $currHours==$configOper.duree_fin}}selected="selected"{{/if}}>{{$currHours}}</option>
      {{/foreach}}
      </select>
    </td>
    <th>
      <label for="dPplanningOp[operation][hour_urgence_fin]" title="{{tr}}config-dPplanningOp-COperation-hour_urgence_fin{{/tr}}">{{tr}}config-dPplanningOp-COperation-hour_urgence_fin{{/tr}}</label>  
    </th>
    <td>
      <select title="num|moreEquals|dPplanningOp[operation][hour_urgence_deb]" name="dPplanningOp[operation][hour_urgence_fin]">
      {{foreach from=$listHours item=currHours}}
        <option value="{{$currHours}}"{{if $currHours==$configOper.hour_urgence_fin}}selected="selected"{{/if}}>{{$currHours}}</option>
      {{/foreach}}
      </select>
    </td>
    <td colspan="2"></td>
  </tr>

  <tr>
    <th class="category" colspan="6">{{tr}}CSejour{{/tr}}</th>
  </tr>
  
  <tr>
    <th>
      <label for="dPplanningOp[sejour][heure_deb]" title="{{tr}}config-dPplanningOp-CSejour-heure_deb{{/tr}}">{{tr}}config-dPplanningOp-CSejour-heure_deb{{/tr}}</label>  
    </th>
    <td>
      <select title="num" name="dPplanningOp[sejour][heure_deb]">
      {{foreach from=$listHours item=currHours}}
        <option value="{{$currHours}}"{{if $currHours==$configSejour.heure_deb}}selected="selected"{{/if}}>{{$currHours}}</option>
      {{/foreach}}
      </select>
    </td>
    <th>
      <label for="dPplanningOp[sejour][heure_fin]" title="{{tr}}config-dPplanningOp-CSejour-heure_fin{{/tr}}">{{tr}}config-dPplanningOp-CSejour-heure_fin{{/tr}}</label>  
    </th>
    <td>
      <select title="num|moreEquals|dPplanningOp[sejour][heure_deb]" name="dPplanningOp[sejour][heure_fin]">
      {{foreach from=$listHours item=currHours}}
        <option value="{{$currHours}}"{{if $currHours==$configSejour.heure_fin}}selected="selected"{{/if}}>{{$currHours}}</option>
      {{/foreach}}
      </select>
    </td>
    <th>
      <label for="dPplanningOp[sejour][min_intervalle]" title="{{tr}}config-dPplanningOp-CSejour-min_intervalle{{/tr}}">{{tr}}config-dPplanningOp-CSejour-min_intervalle{{/tr}}</label>  
    </th>
    <td>
      <select title="num" name="dPplanningOp[sejour][min_intervalle]">
      {{html_options options=$listInterval selected=$configSejour.min_intervalle}}
      </select>
    </td>
  </tr>  
    
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
</table>
</form>