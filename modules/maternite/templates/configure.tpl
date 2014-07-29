{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs-configure', true);
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CGrossesse">{{tr}}CGrossesse{{/tr}}</a></li>
</ul>

<div id="CGrossesse" style="display: none">
  {{mb_include template=CGrossesse_configure}}
</div>
