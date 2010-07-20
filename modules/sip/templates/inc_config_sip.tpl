{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $dPconfig.sip.server}}
<script type="text/javascript">
ElementChecker.check.dontMatch = function(){
  this.assertMultipleArgs("dontMatch");
  if (this.sValue.match(new RegExp(this.oProperties["dontMatch"])))
    this.addError("dontMatch", "Doit pas contenir $g en mode SIP");
}.bind(ElementChecker);
</script>
{{/if}}

<form name="editConfigSip" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="sip"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=configure_handler class_handler=CSipObjectHandler}}
    
    <tr>
      <th class="category" colspan="10">{{tr}}config-traitement-{{$mod}}{{/tr}}</th>
    </tr>
        
    {{mb_include module=system template=inc_config_bool var=server}}
    
    {{if $dPconfig.sip.server}}
      {{mb_include module=system template=inc_config_str var=tag_ipp cssClass="str dontMatch"}}
      {{mb_include module=system template=inc_config_str var=tag_dossier cssClass="str dontMatch"}}
    {{/if}}
    
    {{mb_include module=system template=inc_config_bool var=send_all_patients}}
	    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>