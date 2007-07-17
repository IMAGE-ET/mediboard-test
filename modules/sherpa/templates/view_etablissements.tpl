<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;sp_etab_id=0">
        {{tr}}{{$etablissement->_class_name}}.create{{/tr}}
      </a>
      {{include file="inc_list_etablissements.tpl"}}
    </td>
    <td class="halfPane">
      {{include file="inc_edit_etablissement.tpl"}}
    </td>
  </tr>
</table>