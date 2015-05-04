{{*
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main layout">
  <tr>
    <td style="vertical-align: middle !important;">
      {{if ($object->_nb_files_docs + $object->_nb_forms == 0)}}
        <span class="empty">{{tr}}{{$object->_class}}{{/tr}} : Aucun document</span>
      {{else}}
        {{tr}}{{$object->_class}}{{/tr}}
        <button type="button" class="search"
                onclick="DocumentV2.viewDocs('{{$patient_id}}', '{{$object->_id}}', '{{$object->_class}}')">
        {{if $object->_nb_docs}}
          {{$object->_nb_docs}} document{{if $object->_nb_docs > 1}}s{{/if}}
        {{/if}}
        {{if $object->_nb_files}}
          {{if $object->_nb_docs}}-{{/if}} {{$object->_nb_files}} fichier{{if $object->_nb_files > 1}}s{{/if}}
        {{/if}}
        {{if $object->_nb_forms}}
          {{if $object->_nb_docs || $object->_nb_files}}-{{/if}} {{$object->_nb_forms}} formulaire{{if $object->_nb_forms > 1}}s{{/if}}
        {{/if}}
        </button>
      {{/if}}
    </td>
  </tr>
</table>