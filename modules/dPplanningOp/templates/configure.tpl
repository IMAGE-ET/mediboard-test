<script type="text/javascript">
Main.add(function() {
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
  <li><a href="#config-sae-base">{{tr}}config-sae-base{{/tr}}</a></li>
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
  
  {{assign var=class value=CSejour}}
  
  {{mb_include module=system template=inc_config_bool var=easy_cim10}}
  {{mb_include module=system template=inc_config_bool var=easy_service}}
  {{mb_include module=system template=inc_config_bool var=easy_chambre_simple}}
  {{mb_include module=system template=inc_config_bool var=easy_ald_cmu}}
    
  {{assign var=class value=COperation}}
  
  {{mb_include module=system template=inc_config_bool var=easy_horaire_voulu}}
  {{mb_include module=system template=inc_config_bool var=easy_materiel}}
  {{mb_include module=system template=inc_config_bool var=easy_remarques}}
  {{mb_include module=system template=inc_config_bool var=easy_regime}}
  {{mb_include module=system template=inc_config_bool var=easy_accident}}
  {{mb_include module=system template=inc_config_bool var=easy_assurances}}
  
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</div>

<div id="configure-COperation" style="display: none;">

{{assign var=class value=COperation}}

<table class="form">
  <tr>
    <th class="title" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=use_ccam}}
  {{mb_include module=system template=inc_config_bool var=verif_cote}}
  {{mb_include module=system template=inc_config_bool var=horaire_voulu}}
  {{mb_include module=system template=inc_config_bool var=delete_only_admin}}
  
  {{mb_include module=system template=inc_config_enum var=duree_deb  values=$hours skip_locales=true}}
  {{mb_include module=system template=inc_config_enum var=duree_fin  values=$hours skip_locales=true}}
  {{mb_include module=system template=inc_config_enum var=hour_urgence_deb  values=$hours skip_locales=true}}
  {{mb_include module=system template=inc_config_enum var=hour_urgence_fin  values=$hours skip_locales=true}}
  
  {{mb_include module=system template=inc_config_enum var=min_intervalle values=$intervals skip_locales=true}}
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>
</div>

<div id="configure-CSejour" style="display: none;">
{{assign var="class" value="CSejour"}}
<script type="text/javascript">
Main.add(function() {
  var form = getForm("editConfig");
  form["dPplanningOp[CSejour][max_cancel_time]"    ].addSpinner({min:0, max:24});
  form["dPplanningOp[CSejour][hours_sejour_proche]"].addSpinner({min:0, max:96});
});
</script>

<table class="form">
  <tr>
    <th class="title" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  {{mb_include module=system template=inc_config_enum var=patient_id values=$patient_ids}}
  {{mb_include module=system template=inc_config_enum var=check_collisions values="no|date|datetime"}}
  {{mb_include module=system template=inc_config_bool var=entree_modifiee}}

  {{mb_include module=system template=inc_config_enum var=heure_deb  values=$hours skip_locales=true}}
  {{mb_include module=system template=inc_config_enum var=heure_fin  values=$hours skip_locales=true}}
  
  {{mb_include module=system template=inc_config_enum var=min_intervalle  values=$intervals skip_locales=true}}

  {{mb_include module=system template=inc_config_bool var=blocage_occupation}}
  {{mb_include module=system template=inc_config_bool var=service_id_notNull}}
  {{mb_include module=system template=inc_config_bool var=consult_accomp}}
  {{mb_include module=system template=inc_config_bool var=accident}}
  {{mb_include module=system template=inc_config_bool var=assurances}}
  {{mb_include module=system template=inc_config_bool var=delete_only_admin}}
  {{mb_include module=system template=inc_config_str  var=max_cancel_time     size=2 suffix=h}}
  {{mb_include module=system template=inc_config_str  var=hours_sejour_proche size=2 suffix=h}}
  {{mb_include module=system template=inc_config_bool var=fix_doc_edit}}
  {{mb_include module=system template=inc_config_bool var=show_type_pec}}
  {{mb_include module=system template=inc_config_bool var=show_discipline_tarifaire }}
  
  <tr>
    <th class="title" colspan="2">Heure par defaut du séjour</th>
  </tr>
  
  <tr>
    <th class="category" colspan="2">Heure d'entree</th>
  </tr>
  {{mb_include module=system template=inc_config_enum var=heure_entree_veille values=$hours skip_locales=true}}
  {{mb_include module=system template=inc_config_enum var=heure_entree_jour   values=$hours skip_locales=true}}
  <tr>
    <th class="category" colspan="2">Heure de sortie</th>
  </tr>
  {{mb_include module=system template=inc_config_enum var=heure_sortie_ambu  values=$hours skip_locales=true}}
  {{mb_include module=system template=inc_config_enum var=heure_sortie_autre values=$hours skip_locales=true}}
  
  {{assign var="var" value="sortie_prevue"}}
  <tr>
    <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}</th>
  </tr>
    
  {{foreach from=$conf.$m.$class.$var key=type item=value}}
  <tr>
    <th>
      <label for="{{$m}}[{{$class}}][{{$var}}][{{$type}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$type}}{{/tr}}
      </label>     
    </th>
    <td>
      <select class="num" name="{{$m}}[{{$class}}][{{$var}}][{{$type}}]">
        <option value="04" {{if "04" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 04h</option>
        <option value="24" {{if "24" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 24h</option>
        <option value="48" {{if "48" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 48h</option>
        <option value="72" {{if "72" == $conf.$m.$class.$var.$type}}selected="selected"{{/if}}>+ 72h</option>
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
  
  {{mb_include module=system template=inc_config_bool class=COperation var=locked }}
  {{mb_include module=system template=inc_config_bool class=CSejour    var=locked }}
  
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
  
  {{mb_include module=system template=inc_config_str var=tag_dossier           }}
  {{mb_include module=system template=inc_config_str var=tag_dossier_group_idex}}
  {{mb_include module=system template=inc_config_str var=tag_dossier_pa        }}
  {{mb_include module=system template=inc_config_str var=tag_dossier_cancel    }}
  {{mb_include module=system template=inc_config_str var=tag_dossier_trash     }}
  {{mb_include module=system template=inc_config_bool var=show_modal_identifiant}}
  
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

<div id="config-sae-base" style="display: none;">
  <h2>Paramètres de la base SAE</h2>

  {{mb_include module=system template=configure_dsn dsn=sae}}
 
  <table class="main tbl">
    <tr>
      <th class="title" colspan="2">
        Import de la base
      </th>
    <tr>
      <td class="narrow">
        <button onclick="new Url('planningOp', 'ajax_import_sae_base').requestUpdate('import-log');" class="change">
          {{tr}}Import{{/tr}}
        </button>
      </td>
      <td id="import-log"></td>
    </tr>
  </table>
</div>