{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function checkFormPrint() {
  var form = document.PrintFilter;
    
  if(!(checkForm(form))){
    return false;
  }
  
  var url = new Url("dPbloc", "print_materiel");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.popup(900, 750, 'Materiel');
}

</script>

<form name="PrintFilter" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">

<table class="form">
  <tr>
    <th>{{mb_label object=$filter field="_date_min"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_min" form="PrintFilter" canNull="false" register=true}} </td>
    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td class="date" >{{mb_field object=$filter field="_date_max" form="PrintFilter" canNull="false" register=true}}</td>
    <td class="button">
      <button type="button" onclick="checkFormPrint()" class="search">Afficher l'historique</button>
    </td>
  </tr>
</table>

</form>

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-commande_mat', true);
});
</script>

<ul id="tabs-commande_mat" class="control_tabs">
  <li>
    {{assign var=op_count value=$operations[0]|@count}}
    <a href="#commande_mat_0" {{if !$op_count}}class="empty"{{/if}}>
      {{tr}}COperation.commande_mat.0{{/tr}} 
      <small>({{$op_count}})</small>
    </a>
  </li>
  <li>
    {{assign var=op_count value=$operations[1]|@count}}
    <a href="#commande_mat_1" {{if !$op_count}}class="empty"{{/if}}>
      A annuler
      <small>({{$op_count}})</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

{{foreach from=$operations key=commande_mat item=_operations}}
{{mb_include template=inc_list_materiel}}
{{/foreach}}