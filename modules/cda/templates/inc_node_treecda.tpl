{{*
 * $Id$
 *
 * Vue pour afficher la structure du document
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<span class="child">{{$node.name}}</span>
<ul>
  {{if $node.attribute|@count !=0}}
    <li>
      <span class="attribute">{{tr}}Attribute{{/tr}}</span>
      <ul>
        {{foreach from=$node.attribute key=_node_key item=_attribute}}
        <span class="attribute">{{$_node_key}}</span>
        <li>
          <span class="value">{{$_attribute}}</span>
        </li>
        {{/foreach}}
      </ul>
    </li>
  {{/if}}
  {{if $node.child|@count === 0 && $node.data != ""}}
    <li>
      <span class="data">{{tr}}Data{{/tr}}</span>
      <ul>
        <span class="value">{{$node.data}}</span>
      </ul>
    </li>
  {{/if}}
  {{foreach from=$node.child item=_child}}
    <li>
      {{mb_include template=inc_node_treecda node=$_child}}
    </li>
  {{/foreach}}
</ul>