{{mb_include_script module="system" script="object_selector"}}

<script type="text/javascript">

function pageMain() {
  regFieldCalendar("editFrm", "last_update", true);
}

</script>

<table class="main">
  <tr>
    <td>
      <a class="buttonnew" href="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}&amp;id_sante400_id=0{{if $dialog}}&amp;object_class={{$filter->object_class}}&amp;object_id={{$filter->object_id}}{{/if}}">
        Création d'un identifiant
      </a>
      {{if !$dialog}}
      {{include file="inc_filter_identifiants.tpl"}}
      {{/if}}
      {{include file="inc_list_identifiants.tpl"}}
    </td>
    <td>
      {{include file="inc_edit_identifiant.tpl"}}
    </td>
  </tr>
</table>