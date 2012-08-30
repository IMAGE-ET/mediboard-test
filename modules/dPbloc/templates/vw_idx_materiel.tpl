{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
checkFormPrint = function() {
  var form = document.PrintFilter;
    
  if(!(checkForm(form))){
    return false;
  }
  
  var url = new Url("dPbloc", "print_materiel");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.popup(900, 750, 'Materiel');
}

refreshLists = function() {
  var url = new Url("dPbloc", "ajax_vw_materiel");
  url.addFormData(getForm("selectBloc"));
  url.requestUpdate("list_materiel");
}

Main.add(function() {
  refreshLists();
});
</script>

<form name="PrintFilter" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">

<table class="form">
  <tr>
    <th>{{mb_label object=$filter field="_date_min"}}</th>
    <td>{{mb_field object=$filter field="_date_min" form="PrintFilter" canNull="false" register=true}} </td>
    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td>{{mb_field object=$filter field="_date_max" form="PrintFilter" canNull="false" register=true}}</td>
    <td class="button">
      <button type="button" onclick="checkFormPrint()" class="search">Afficher l'historique</button>
    </td>
  </tr>
</table>

</form>

<form action="?" name="selectBloc" method="get" onsubmit="refreshLists(); return false;">
  <select name="bloc_id" style="float: right;" onchange="this.form.onsubmit()">
    {{foreach from=$listBlocs item=curr_bloc}}
    <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc_id}}selected="selected"{{/if}}>
      {{$curr_bloc->nom}}
    </option>
    {{foreachelse}}
    <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
    {{/foreach}}
  </select>
</form>

<div id="list_materiel">
</div>
