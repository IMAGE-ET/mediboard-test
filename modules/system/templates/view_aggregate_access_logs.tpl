{{*
 * $Id$
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

<table class="main">
  <tr>
    <td>
      <button class="search" type="button" onclick="AccessLog.aggregate(true)">{{tr}}DryRun{{/tr}}</button>
    </td>
    <td>
      <div id="dry_run"></div>
    </td>
  </tr>
  <tr>
    <td>
      <button class="search" type="button" onclick="AccessLog.aggregate(false)">{{tr}}Aggregate{{/tr}}</button>
    </td>
    <td>
      <div id="aggregate"></div>
    </td>
  </tr>
</table>