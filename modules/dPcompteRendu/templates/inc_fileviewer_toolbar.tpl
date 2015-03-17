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

{{mb_script module=compteRendu script=document ajax=true}}

<form name="actionsDoc-{{$doc->_guid}}" method="post">
  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  {{mb_key object=$doc}}
  <button type="button" class="edit notext" onclick="Document.edit('{{$doc->_id}}')">{{tr}}Edit{{/tr}}</button>
  <button type="button" class="print notext" onclick="
         {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
           Document.printPDF({{$doc->_id}});
         {{else}}
           Document.print({{$doc->_id}});
         {{/if}}">{{tr}}Print{{/tr}}</button>
  <button type="button" class="trash notext" onclick="Document.del(this.form, '{{$doc->nom|smarty:nodefaults|JSAttribute}}')">{{tr}}Delete{{/tr}}</button>
</form>