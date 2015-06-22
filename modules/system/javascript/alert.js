/**
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Alert = {
  object_guid: null,
  tag: null,
  level: null,
  element: null,
  callback: null,

  showAlerts: function(object_guid, tag, level, callback, element) {
    Alert.object_guid = object_guid;
    Alert.tag = tag;
    Alert.level = level;
    Alert.callback = callback || Prototype.emptyFunction;
    Alert.element = element;

    var div_id = 'tooltip-alerts-'+level+'-'+object_guid;
    var url = new Url('system', 'ajax_vw_alertes');
    url.addParam('object_guid', object_guid);
    url.addParam('level'      , level);
    url.addParam("tag"        , tag);
    url.requestUpdate(div_id, function() {
      ObjectTooltip.createDOM(element, div_id, {duration: 0});
    });
  }
};