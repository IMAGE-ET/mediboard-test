{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<fieldSet>
  <legend>Infos patient</legend>
  <div class="text" id="infoPat">
    <div class="empty">Aucun patient sélectionné</div>
  </div>
</fieldSet>
{{if $consult->_id}}
  <fieldset>
    <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}{{$object_consult->_class}}{{/tr}}</legend>
    <div id="documents">
      <script type="text/javascript">
        Document.register('{{$object_consult->_id}}','{{$object_consult->_class}}','{{$consult->_praticien_id}}','documents');
      </script>
    </div>
  </fieldset>
  <fieldset>
    <legend>{{tr}}CFile{{/tr}} - {{tr}}{{$consult->_class}}{{/tr}}</legend>
    <div id="files">
      <script type="text/javascript">
        File.register('{{$consult->_id}}','{{$consult->_class}}', 'files');
      </script>
    </div>
  </fieldset>
{{/if}}