{{*
 * $Id:$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 *}}
 
<script>
Main.add(function () {
  Control.Tabs.create('tabs-commande_mat', true);
});
</script>

<ul id="tabs-commande_mat" class="control_tabs">
  {{foreach from=$operations key=commande_mat item=_operations}}
    <li>
      <a href="#commande_mat_{{$commande_mat}}" {{if !$_operations|@count}}class="empty"{{/if}}>
        {{tr}}CCommandeMaterielOp.etat.{{$commande_mat}}.title{{/tr}}
        <small>({{$_operations|@count}})</small>
      </a>
    </li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$operations key=commande_mat item=_operations}}
  {{mb_include template=inc_list_materiel}}
{{/foreach}}