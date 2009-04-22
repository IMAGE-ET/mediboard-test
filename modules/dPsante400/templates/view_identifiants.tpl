{{mb_include_script module="system" script="object_selector"}}

<table class="main">
  <tr>
    <td>
      {{if $canSante400->edit}}
      <a class="button new" href="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id=0{{if $dialog}}&amp;object_class={{$filter->object_class}}&amp;object_id={{$filter->object_id}}{{/if}}">
        Création d'un identifiant
      </a>
      {{/if}}
      {{if !$dialog}}
      {{include file="inc_filter_identifiants.tpl"}}
      {{/if}}
      {{include file="inc_list_identifiants.tpl"}}
    </td>
    {{if $canSante400->edit}}
    <td>
      {{include file="inc_edit_identifiant.tpl"}}
    </td>
    {{/if}}
  </tr>
</table>