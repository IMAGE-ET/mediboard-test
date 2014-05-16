<form name="editConfigModeEasy" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="2">Affichage de la DHE simplifiée</th>
    </tr>
    
    {{assign var=class value=CSejour}}

    <tr>
      <th class="category" colspan="2">{{tr}}CSejour{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=easy_cim10}}
    {{mb_include module=system template=inc_config_bool var=easy_service}}
    {{mb_include module=system template=inc_config_bool var=easy_chambre_simple}}
    {{mb_include module=system template=inc_config_bool var=easy_ald_cmu}}
    {{mb_include module=system template=inc_config_bool var=easy_isolement}}
    {{mb_include module=system template=inc_config_bool var=easy_atnc}}
      
    {{assign var=class value=COperation}}

    <tr>
      <th class="category" colspan="2">{{tr}}COperation{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=easy_materiel}}
    {{mb_include module=system template=inc_config_bool var=easy_remarques}}
    {{mb_include module=system template=inc_config_bool var=easy_regime}}
    {{mb_include module=system template=inc_config_bool var=easy_accident}}
    {{mb_include module=system template=inc_config_bool var=easy_assurances}}
    {{mb_include module=system template=inc_config_bool var=easy_type_anesth}}
    {{mb_include module=system template=inc_config_str var=easy_length_input_label size=5 numeric=true spinner_min=40 spinner_max=88}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
    
  </table>
</form>