{{assign var=class value=CPatient}}

<form name="EditConfig-{{$class}}" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />

  <table class="form">
    {{assign var=class value=CPatient}}
    {{mb_include module=system template=inc_config_str var=adult_age}}
    {{mb_include module=system template=inc_config_enum var=multi_group values="full|limited|hidden"}}
    {{mb_include module=system template=inc_config_bool var=function_distinct}}
    {{mb_include module=system template=inc_config_enum var=limit_char_search values="0|3|4|5|6|8|10"}}

    {{mb_include module=system template=inc_config_bool var=check_code_insee}}

    {{mb_include module=system template=inc_config_bool var=extended_print}}

    <tr>
      <th colspan="2" class="category">{{tr}}CPatient-config-interoperability{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_str var=tag_ipp      }}
    {{mb_include module=system template=inc_config_str var=tag_ipp_group_idex}}
    {{mb_include module=system template=inc_config_str var=tag_ipp_trash}}
    {{mb_include module=system template=inc_config_str var=tag_conflict_ipp}}

    {{assign var=class value=CMedecin}}

    <tr>
      <th colspan="2" class="category">{{tr}}CMedecin{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_bool var=medecin_strict}}

    {{assign var=class value=INSEE}}

    <tr>
      <th colspan="2" class="category">{{tr}}INSEE{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_bool var=france}}
    {{mb_include module=system template=inc_config_bool var=suisse}}

    <tr>
      <td class="button" colspan="6">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>

  </table>
</form>