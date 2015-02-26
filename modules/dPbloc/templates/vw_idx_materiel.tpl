{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=dPplanningOp script=commande_mat}}
<script>
checkFormPrint = function() {
  var form = document.PrintFilter;
    
  if(!(checkForm(form))){
    return false;
  }
  
  var url = new Url("dPbloc", "print_materiel");
  url.addFormData(form);
  url.popup(900, 750, 'Materiel');
};

refreshLists = function() {
  var url = new Url("dPbloc", "ajax_vw_materiel");
  url.addFormData(getForm("PrintFilter"));
  url.requestUpdate("list_materiel");
};

Main.add(function() {
  refreshLists();
});
</script>

<form name="PrintFilter" action="?m=dPbloc" method="post">
  <table class="form">
    <tr>
      <th>{{mb_label object=$filter field="_date_min"}}</th>
      <td>{{mb_field object=$filter field="_date_min" form="PrintFilter" canNull="false" register=true onchange="refreshLists();"}} </td>
      <th>{{tr}}CBlocOperatoire{{/tr}}</th>
      <td>
        <select name="bloc_id" onchange="this.form.onsubmit()">
          {{foreach from=$listBlocs item=curr_bloc}}
            <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc_id}}selected="selected"{{/if}}>
              {{$curr_bloc->nom}}
            </option>
            {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
          {{/foreach}}
        </select>
      </td>

      <th>Praticien</th>
      <td>
        <select name="praticien_id" onchange="this.form.function_id.value = '';refreshLists();">
          <option value="">&mdash; Choix d'un praticien</option>
          {{mb_include module=mediusers template=inc_options_mediuser selected=$praticien_id list=$praticiens}}
        </select>
      </td>
      <td class="button">
        <button type="button" onclick="checkFormPrint()" class="search">Afficher l'historique</button>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$filter field="_date_max"}}</th>
      <td>{{mb_field object=$filter field="_date_max" form="PrintFilter" canNull="false" register=true onchange="refreshLists();"}}</td>
      <td colspan="2"></td>

      <th>Cabinet médical</th>
      <td>
        <select name="function_id" onchange="this.form.praticien_id.value = '';refreshLists();" style="width: 23em !important">
          <option value="">&mdash; Choix d'un cabinet</option>
          {{mb_include module=mediusers template=inc_options_function selected=$function_id list=$functions}}
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td colspan="7" class="button">
        <button type="button" class="search" onclick="refreshLists();" >{{tr}}Filter{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
<div id="list_materiel"></div>
