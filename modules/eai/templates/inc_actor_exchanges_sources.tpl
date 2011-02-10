{{*
 * View Interop actor Exchange Sources EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{if count($actor->_ref_exchanges_sources) > 0}}
  <script type="text/javascript">
    Main.add(function () {
      Control.Tabs.create('tabs-evenements-{{$actor->_guid}}', true);
    });
  </script>
  
  {{mb_include module=eai template="`$actor->_parent_class_name`_exchanges_sources_inc"}}
{{/if}}