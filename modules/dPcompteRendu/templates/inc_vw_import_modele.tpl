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

<h2>Import de modèles pour {{$owner}}</h2>

<form name="editImport" method="post" enctype="multipart/form-data"
      action="?m=compteRendu&a=ajax_import_modele&dialog=1">
  <input type="hidden" name="owner_guid" value="{{$owner->_guid}}" />
  <input type="file" name="datafile" size="40" />
  <button class="tick">Importer</button>
</form>
