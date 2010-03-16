{{assign var=class value=CPatient}}
<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">  
  {{mb_include module=system template=inc_config_str var=tag_ipp      }}
  {{mb_include module=system template=inc_config_str var=tag_ipp_trash}}
  
  {{mb_include module=system template=inc_config_bool var=merge_only_admin}}
  {{mb_include module=system template=inc_config_bool var=extended_print  }}
  {{mb_include module=system template=inc_config_str  var=adult_age       }}

  {{mb_include module=system template=inc_config_enum var=identitovigilence values="nodate|date|doublons"}}
  {{mb_include module=system template=inc_config_enum var=multi_group       values="full|limited|hidden"}}
  {{mb_include module=system template=inc_config_enum var=limit_char_search values="0|3|4|6|8"}}

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>

</table>

</form>