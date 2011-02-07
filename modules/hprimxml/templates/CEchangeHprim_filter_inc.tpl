{{* $Id: vw_idx_echange_hprim.tpl 10195 2010-09-28 15:58:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 10195 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th colspan="2">Choix du statut d'acquittement </th>
  <td colspan="2">
    <select class="str" name="statut_acquittement" onchange="$V(this.form.page, 0)">
      <option value="">&mdash; Liste des statuts &mdash;</option>
      <option value="OK">Ok</option>
      <option value="avertissement">Avertissement </option>
      <option value="erreur">Erreur</option>
    </select>
  </td>
</tr>