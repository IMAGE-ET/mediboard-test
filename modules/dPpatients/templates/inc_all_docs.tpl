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

{{foreach from=$patient->_all_docs item=_doc}}
  {{if $_doc instanceof CCompteRendu}}
    {{mb_include module=compteRendu template=CCompteRendu_fileviewer doc=$_doc}}
  {{elseif $_doc instanceof CFile}}
    {{mb_include module=files template=CFile_fileviewer file=$_doc}}
  {{elseif $_doc instanceof CExLink}}
    {{mb_include module=forms template=CExLink_fileviewer link=$_doc}}
  {{/if}}
{{foreachelse}}
  <div class="small-info">
    Aucun document
  </div>
{{/foreach}}