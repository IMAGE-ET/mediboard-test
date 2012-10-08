{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  function showCreateSejour(input) {
    $(input).up('tr').next().setVisible($V(input) == 1);
    if ($V(input) == 0) {
      $V(input.form.elements["dPcabinet[CConsultation][create_consult_sejour]"] , "0");
    }
  }
  
  Main.add(function () {
    var nodeList = getForm('editConfig-CConsultation').elements["dPcabinet[CConsultation][attach_consult_sejour]"];
    showCreateSejour(nodeList[1-$V(nodeList)]);
    
    getForm("editConfig-CConsultation")["dPcabinet[CConsultation][minutes_before_consult_sejour]"].addSpinner({min:1, max:360});
    getForm("editConfig-CConsultation")["dPcabinet[CConsultation][hours_after_changing_prat]"].addSpinner({min:0, max:48});
  });
</script>


<form name="editConfig-CConsultation" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  
  <table class="form">    
    
    {{assign var="class" value="CConsultation"}}
  
    <tr>
      <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=use_last_consult}}
    
    {{mb_include module=system template=inc_config_bool var=show_examen}}
    
    {{mb_include module=system template=inc_config_bool var=show_histoire_maladie}}
    
    {{mb_include module=system template=inc_config_bool var=show_conclusion}}
    
    {{mb_include module=system template=inc_config_bool var=attach_consult_sejour onchange="showCreateSejour(this)"}}
    
    {{mb_include module=system template=inc_config_bool var=search_sejour_all_groups}}
    
    {{mb_include module=system template=inc_config_bool var=create_consult_sejour}}
    
    {{mb_include module=system template=inc_config_str var=minutes_before_consult_sejour size="3" suffix="min"}}
    
    {{mb_include module=system template=inc_config_str var=hours_after_changing_prat size="3" suffix="h"}}
    
    {{mb_include module=system template=inc_config_bool var=consult_readonly}}
    
    {{mb_include module=system template=inc_config_bool var=fix_doc_edit}}
    
    {{mb_include module=system template=inc_config_bool var=surbooking_readonly}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>