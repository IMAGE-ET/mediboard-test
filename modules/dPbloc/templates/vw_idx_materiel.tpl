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
  var form = document.paramFrm;
    
  if(!(checkForm(form))){
    return false;
  }
  
  popMateriel();
}

function popMateriel() {
  form = document.paramFrm;
  var url = new Url("dPbloc", "print_materiel");
  url.addElement(form._date_min);
  url.addElement(form._date_max);
  url.popup(900, 750, 'Materiel');
}
</script>

<form name="paramFrm" action="?m=dPbloc" method="post" onsubmit="return checkFormPrint()">

<table class="form">
  <tr>
    <th colspan="2" class="category">Imprimer l'historique</th>
  </tr>
  <tr>
    <td>{{mb_label object=$filter field="_date_min"}}</td>
    <td class="date">{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" register=true}} </td>
  </tr>
  <tr>
    <td>{{mb_label object=$filter field="_date_max"}}</td>
    <td class="date" >{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" register=true}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" onclick="checkFormPrint()" class="search">Afficher</button>
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