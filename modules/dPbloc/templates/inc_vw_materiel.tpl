{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
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