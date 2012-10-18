{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th>Nom</th>
  </tr>
  {{foreach from=$modeles_etiquettes item=_modele}}
    <tr>
      <td>
        <button type="button" class="tick notext"
          onclick="ModeleEtiquette.print('{{$object_class}}', '{{$object_id}}', '{{$_modele->_id}}');"></button>
      </td>
      <td>
        {{$_modele->nom}}
      </td>
    </tr>
  {{/foreach}}
</table>