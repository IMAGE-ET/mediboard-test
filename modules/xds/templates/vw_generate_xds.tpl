{{*
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=xds script=cxds}}

<form name="CDA-form" onsubmit="return Cxds.showXds(this)" method="post" class="prepared" action="?m=cda&a=ajax_show_highlightCDA">
  <input type="hidden" name="accept_utf8" value="1"/>
  <pre style="padding: 0; max-height: none;"><textarea name="message" rows="12" style="width: 100%;
      border: none; -webkit-box-sizing: border-box; -moz-box-sizing: border-box;
      margin: 0; resize: vertical;"></textarea></pre>
  <button type="submit" class="change">{{tr}}Validate{{/tr}}</button>
</form>

<div id="highlighted" style="width: 50%;"></div>
<div id="enteteXds" style="width: 49%;"></div>