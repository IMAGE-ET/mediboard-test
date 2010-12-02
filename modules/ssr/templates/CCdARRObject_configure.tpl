{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{mb_include module=system template=configure_dsn dsn=cdarr}}


<h2>Import de la base de données CdARR</h2>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
    	<button class="tick" onclick="new Url('ssr', 'httpreq_do_add_cdarr').requestUpdate('cdarr');" >
    		Importer la base de données CdARR</button>
			</td>
    <td id="cdarr"></td>
  </tr>
</table>
