{{*
 * $Id$
 *
 * Affiche la structure du CDA
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
<ul>
  <span class="rootname">{{$treecda->contain.name}}</span>
{{foreach from=$treecda->contain.child item=_node}}
  <li>
    {{mb_include template=inc_node_treecda node=$_node}}
  </li>
{{/foreach}}
</ul>