<form name="editCFactureEtablissement-config" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="2">Afficher les champs dans l'assurance</th>
    </tr>
    
    {{assign var=class value=CFactureEtablissement}}
    {{mb_include module=system template=inc_config_bool var=use_facture_etab}}
    {{mb_include module=system template=inc_config_bool var=show_type_facture}}
    {{mb_include module=system template=inc_config_bool var=show_statut_pro}}
    {{mb_include module=system template=inc_config_bool var=show_assur_accident}}
    {{mb_include module=system template=inc_config_bool var=show_dialyse}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
    
  </table>
</form>