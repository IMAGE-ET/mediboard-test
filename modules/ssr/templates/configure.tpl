{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Config" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <tr>
      <th class="title">Configuration générale
    </tr>
    {{assign var=class value=CPlateauTechnique}}
  	<tr>
  		<th class="category">{{tr}}{{$class}}{{/tr}}
  	</tr>
    {{* mb_include module=system template=inc_config_bool var=infinite_quantity *}}
    
    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=system template=configure_dsn dsn=cdarr}}

<script type="text/javascript">

function startCdARR() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("ssr", "httpreq_do_add_cdarr");
  CCAMUrl.requestUpdate("cdarr");
}

</script>

<h2>Import de la base de données CdARR</h2>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startCdARR()" >Importer la base de données CdARR</button></td>
    <td id="cdarr"></td>
  </tr>
</table>