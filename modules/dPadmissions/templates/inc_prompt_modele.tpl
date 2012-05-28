<script type="text/javascript">
  Main.add(function() {
    var form = getForm('chooseDoc');
    var url = new Url('dPcompteRendu', 'ajax_modele_autocomplete');
    url.addParam('user_id', User.id);
    url.addParam('function_id', User["function"]["id"]);
    url.addParam('object_class', 'CSejour');
    url.addParam('object_id', '');
    url.autoComplete(form.modele_keywords, null, {
      minChars: 2,
      afterUpdateElement: function(input, selected) {
        var modele_id = selected.down('.id').innerHTML;
        var modele_name = selected.down('div').innerHTML;
        $V(form.modele_id, modele_id);
        $V(form.modele_keywords, modele_id != 0 ? modele_name : '');
      },
      dropdown: true,
      width: '250px'
    });
  });
</script>

<form name="chooseDoc" method="get" action="?" target="_blank">
  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="a" value="ajax_generate_docs_sejour" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="sejours_ids" value="" />
  <table class="form">
    <tr>
      <th>
        Choix du modèle :
      </th>
      <td>
        <input type="text" name="modele_keywords"  value="" class="autocomplete str" autocomplete="off" />
        <input type="hidden" name="modele_id" value="" />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="print"
          onclick="if (Admissions.printForSelection($V(this.form.modele_id), '{{$type}}')) {Admissions.afterPrint(); }">{{tr}}Print{{/tr}}</button>
        <button type="button" class="close" onclick="Admissions.afterPrint();">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>