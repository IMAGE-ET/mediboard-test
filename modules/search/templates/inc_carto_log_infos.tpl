{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main tbl">
  <tr>
    <th class="category" colspan="2"> État de l'index {{$conf.db.std.dbname}}_log</th>
  </tr>
  <tr>
    <td class="text">Nombre de journaux indexés</td>
    <td class="text">
      {{$infos_log.nbdocs_indexed}}
    </td>
  </tr>
</table>