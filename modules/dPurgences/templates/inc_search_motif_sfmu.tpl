{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="title">{{tr}}CMotifSFMU.search{{/tr}}</th>
  </tr>
  <tr>
    <td class="button">
      <select onchange="CCirconstance.displayMotifFromCategorie(this.value)">
        <option value="">{{tr}}CMotifSFMU.Select-categorie{{/tr}}</option>
        {{foreach from=$categories item=_categorie}}
          <option value="{{$_categorie.categorie}}">{{$_categorie.categorie}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>
<br/>
<div id="motif_sfmu_by_category">
</div>