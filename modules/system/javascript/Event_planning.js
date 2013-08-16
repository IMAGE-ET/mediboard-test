/**
 * planning tool for view
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

EventPlanning = Class.create({

  initialize: function(guid, hour_min, hour_max) {
    this.guid = guid;
    this.hour_min = hour_min;
    this.hour_max = hour_max;

  },

  onMenuClick: function(event, data, elem){
    console.log(event, data);
  }
});