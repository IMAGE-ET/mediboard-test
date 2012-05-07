<form name="editConfigEtiquettes" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  {{assign var="class" value="CSejour"}}
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
</form>