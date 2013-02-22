{{*
 * $Id$
 *
 * Affiche la vue pour la liste des dataTypes
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module=cda script=ccda}}

<script>
  Main.add(function(){
    var tree = new TreeView("datatype-list");
    tree.collapseAll();
  });
</script>

<table class="main">
  <tr>
    <td style="width: 20%" id="datatype-list">
      <ul style="font-family: monospace;">
        {{foreach from=$listTypes item=_type}}
          <li>
            <a href="#1" onclick="Ccda.showxml('{{$_type}}')">{{$_type}}</a><br/>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td>
      <div id="xmltype-view">
      </div>
    </td>
  </tr>
</table>