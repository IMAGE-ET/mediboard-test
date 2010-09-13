<script type="text/javascript">
  Main.add(function() {
    getForm("editConfig")["dPplanningOp[CSejour][max_cancel_time]"].addSpinner({min:0, max:24});
  });
  Main.add(function () {
    Control.Tabs.create('tabs-configure', true);
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#configure-mode_easy">Aff. DHE Simplifiée</a></li>
  <li><a href="#configure-COperation">{{tr}}COperation{{/tr}}</a></li>
  <li><a href="#configure-CSejour">{{tr}}CSejour{{/tr}}</a></li>
  <li><a href="#configure-blocage">Blocage</a></li>
  <li><a href="#configure-CIdSante400">{{tr}}CIdSante400-tag{{/tr}}</a></li>
  <li><a href="#configure-maintenance">{{tr}}Maintenance{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<div id="configure-mode_easy" style="display: none">
<table class="form">
  <tr>
    <th class="title" colspan="2">Affichage de la DHE simplifiée</th>
  </tr>
  
  {{assign var="class" value="CSejour"}}
  {{assign var="var"   value="easy_service"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var"   value="easy_chambre_simple"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  
  {{assign var="class" value="COperation"}}
  {{assign var="var"   value="easy_horaire_voulu"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var"   value="easy_materiel"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var"   value="easy_remarques"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var"   value="easy_regime"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</div>

<div id="configure-COperation" style="display: none;">
{{assign var="class" value="COperation"}}
<table class="form">
  <tr>
    <th class="title" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{assign var="var" value="verif_cote"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var" value="horaire_voulu"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var" value="delete_only_admin"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  
  {{assign var="var" value="duree_deb"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  {{assign var="var" value="duree_fin"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  {{assign var="var" value="hour_urgence_deb"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  {{assign var="var" value="hour_urgence_fin"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  
  {{assign var="var" value="min_intervalle"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$intervals_piped skip_locales=true}}
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</div>

<div id="configure-CSejour" style="display: none;">
{{assign var="class" value="CSejour"}}
<table class="form">
  <tr>
    <th class="title" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>

  {{assign var="var" value="patient_id"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var" value="modif_SHS"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}

  {{assign var="var" value="heure_deb"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  {{assign var="var" value="heure_fin"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  
  {{assign var="var" value="min_intervalle"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$intervals_piped skip_locales=true}}

  {{assign var="var" value="service_id_notNull"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
	{{assign var="var" value="delete_only_admin"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  {{assign var="var" value="max_cancel_time"}}
  {{mb_include module=system template=inc_config_str  var=$var class=$class size="2" suffix="h"}}
  
  <tr>
    <th class="title" colspan="2">Heure par defaut du séjour</th>
  </tr>
  <tr>
    <th class="category" colspan="2">Heure d'entree</th>
  </tr>
  {{assign var="var" value="heure_entree_veille"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  {{assign var="var" value="heure_entree_jour"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  <tr>
    <th class="category" colspan="2">Heure de sortie</th>
  </tr>
  {{assign var="var" value="heure_sortie_ambu"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  {{assign var="var" value="heure_sortie_autre"}}
  {{mb_include module=system template=inc_config_enum var=$var class=$class values=$hours_piped skip_locales=true}}
  
  {{assign var="var" value="sortie_prevue"}}
  <tr>
    <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
  </tr>
    
  {{foreach from=$dPconfig.$m.$class.$var key=type item=value}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}
      </label>     
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}][{{$type}}]">
        <option value="04"  {{if "04" == $dPconfig.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 04h</option>
        <option value="24" {{if "24" == $dPconfig.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 24h</option>
        <option value="48" {{if "48" == $dPconfig.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 48h</option>
        <option value="72" {{if "72" == $dPconfig.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 72h</option>
      </select>
    </td>
  </tr>
  {{/foreach}}
  <tr>
    <td class="button" colspan="100">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</div>

<div id="configure-blocage" style="display: none;">
<table class="form">
  <tr>
    <th class="title" colspan="2">Blocage des objets</th>
  </tr>
  
  {{assign var="class" value="COperation"}}
  {{assign var="var" value="locked"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}

  {{assign var="class" value="CSejour"}}
  {{assign var="var" value="locked"}}
  {{mb_include module=system template=inc_config_bool var=$var class=$class}}
  
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</div>

<div id="configure-CIdSante400" style="display: none;">
<table class="form">
  <tr>
    <th class="title" colspan="2">Tag pour les numéros de dossier</th>
  </tr>
  
  {{assign var="var" value="tag_dossier"}}
  {{mb_include module=system template=inc_config_str var=$var class=$class}}
  {{assign var="var" value="tag_dossier_pa"}}
  {{mb_include module=system template=inc_config_str var=$var class=$class}}
  {{assign var="var" value="tag_dossier_cancel"}}
  {{mb_include module=system template=inc_config_str var=$var class=$class}}
  {{assign var="var" value="tag_dossier_trash"}}
  {{mb_include module=system template=inc_config_str var=$var class=$class}}
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</div>
</form>

<div id="configure-maintenance" style="display:none">
  {{include file=inc_configure_actions.tpl}}
</div>