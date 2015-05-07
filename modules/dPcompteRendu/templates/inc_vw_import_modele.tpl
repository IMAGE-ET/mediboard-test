{{*
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="editImport" method="post" enctype="multipart/form-data"
      action="?m=compteRendu&a=ajax_import_modele&dialog=1">
  <input type="hidden" name="owner_guid" value="{{$owner_guid}}" />

  <table class="form">
    <tr>
      <th class="category">{{tr}}CCompteRendu.choose_file{{/tr}}</th>
    </tr>
    <tr>
      <td>
        <input type="file" name="datafile" size="40" />
      </td>
    </tr>
    <tr>
      <td>
        <button class="tick">Importer</button>
      </td>
    </tr>
  </table>
</form>
