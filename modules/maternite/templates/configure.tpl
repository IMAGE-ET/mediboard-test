<script type="text/javascript">
  Main.add(function() {
    getForm("editConfig")["maternite[days_terme]"].addSpinner({min:0});
    getForm("editConfig")["maternite[duree_sejour]"].addSpinner({min:0});
  });
</script>
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <tr>
      <th colspan="2" class="category">{{tr}}CGrossesse{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_str var=days_terme size=2 suffix="jours"}}
    {{mb_include module=system template=inc_config_str var=duree_sejour size=2 suffix="jours"}}

    {{assign var=class value="CGrossesse"}}
    {{mb_include module=system template=inc_config_bool var=date_regles_obligatoire size=2 suffix="jours"}}

    {{mb_include module=system template=configure_handler class_handler=CAffectationHandler}}

    <tr>
      <td colspan="2" class="button">
        <button class="save">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>