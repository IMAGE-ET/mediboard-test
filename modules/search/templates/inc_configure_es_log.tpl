{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="form">
  <tr>
    <th class="category">Actions sur l'indexation des logs</th>
  </tr>
  <tr>
    <td class="button">
      <span> Nom de l'index : {{$conf.db.std.dbname}}_log</span>
    </td>
  </tr>
  <tr>
    <td class="button">
      <button class="new singleclick" type="submit" onclick="Search.createLogMapping();"> Créer le schéma Nosql</button>
    </td>
  </tr>
</table>