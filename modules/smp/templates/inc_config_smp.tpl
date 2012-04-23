{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  ElementChecker.check.dontMatch = function(){
    this.assertMultipleArgs("dontMatch");
    if (this.sValue.match(new RegExp(this.oProperties["dontMatch"])))
      this.addError("dontMatch", "Doit pas contenir $g en mode SMP");
  }.bind(ElementChecker);
</script>

<form name="editConfigSMP" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="smp"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=configure_handler class_handler=CSmpObjectHandler}}
    
    <tr>
      <th class="category" colspan="10">{{tr}}config-traitement-{{$mod}}{{/tr}}</th>
    </tr>
        
    {{mb_include module=system template=inc_config_bool var=server}}
    
    {{mb_include module=system template=inc_config_str var=tag_nda cssClass="str dontMatch"}}
    
    {{mb_include module=system template=inc_config_str var=tag_visit_number}}
    
    {{mb_include module=system template=inc_config_bool var=create_object_by_vn}}

    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>