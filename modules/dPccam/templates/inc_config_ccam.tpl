{{* $Id: configure.tpl 9306 2010-06-28 08:29:45Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 9306 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-ccam" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">
    {{assign var=class value=CCodeCCAM}}
    <tr>
      <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=use_cache}}
    
    {{assign var=class value=CCodable}}
    <tr>
      <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=use_getMaxCodagesActes}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=system template=configure_dsn dsn=ccamV2}}

<h2>Import de la base de données CCAM</h2>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startCCAM()" >Importer la base de données CCAM</button></td>
    <td id="ccam"></td>
  </tr>
</table>