{{*
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="EditConfig-drawing" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />

  <table class="form">
    {{mb_include module=system template=inc_config_bool var=edit_svg}}
    {{mb_include module=system template=inc_config_bool var=drawing_allow_external_ressource}}
    <tr>
      <td class="button" colspan="6">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<hr/>

<h2>Import de pack</h2>
<form name="import_pack" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" enctype="multipart/form-data">
  <input type="hidden" name="m" value="drawing" />
  <input type="hidden" name="dosql" value="do_import_image_pack" />
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />

  <table class="form">
    <tr>
      <th>Fichier (zip)</th>
      <td>
        <input type="file" name="zip" />
      </td>
    </tr>
    <tr>
      <th>Catégorie visée</th>
      <td>
        <select name="category" style="float: left;">
          <option value="">&mdash; Automatique</option>
          {{foreach from=$cats item=_cat}}
            <option value="{{$_cat->name}}">{{$_cat}}</option>
          {{/foreach}}
        </select>
        <div class="info" style="padding-left:20px;">Si automatique, le logiciel ajoutera les catégories en se basant sur le nom des dossiers</div>

      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button class="upload" type="submit">{{tr}}Upload{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>