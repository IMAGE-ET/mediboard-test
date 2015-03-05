<script type="text/javascript">
  Main.add(function() {
    var form = getForm('chooseDoc');
    var url = new Url('dPcompteRendu', 'ajax_modele_autocomplete');
    url.addParam('user_id', User.id);
    url.addParam('function_id', User["function"]["id"]);
    url.addParam('object_class', 'CSejour');
    url.addParam('object_id', '');
    url.autoComplete(form.keywords_modele, null, {
      minChars: 2,
      afterUpdateElement: function(input, selected) {
        var modele_id = selected.down('.id').innerHTML;
        var modele_name = selected.down('div').innerHTML;
        $V(form.modele_id, modele_id);
        $V(form.keywords_modele, modele_id != 0 ? modele_name : '');
      },
      dropdown: true,
      width: '250px'
    });
  });
</script>

<form name="download_etiqs" style="display: none;" action="?m=hospi&a=ajax_print_etiquettes_sejours" target="_blank" method="post" class="prepared">
  <input type="hidden" name="sejours_ids" value="" />
  <input type="hidden" name="modele_etiquette_id" />
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="dialog" value="1" />
</form>

<form name="chooseDoc" method="post" action="?m=compteRendu&a=ajax_generate_docs_sejour" target="_blank">
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="sejours_ids" value="" />
  <table class="form">
    <tr>
      <th>
        Choix du modèle :
      </th>
      <td>
        <input type="text" name="keywords_modele"  value="" class="autocomplete str" autocomplete="off" />
        <button type="button" class="print"
                onclick="if (Admissions.printForSelection($V(this.form.modele_id))) { Admissions.afterPrint(); }">{{tr}}Print{{/tr}}</button>
        <input type="hidden" name="modele_id" value="" />
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="print" onclick="Admissions.printFichesAnesth()">Imprimer les fiches d'anesthésie</button>
        {{if "soins dossier_soins show_bouton_plan_soins"|conf:"CGroups-$g"}}
          <button type="button" class="print" onclick="Admissions.printPlanSoins()">Imprimer les plans de soins</button>
        {{/if}}
        <button type="button" class="print" onclick="Admissions.chooseEtiquette()">Imprimer les étiquettes</button>
        <br />
        <button type="button" class="close" onclick="Admissions.afterPrint();">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>