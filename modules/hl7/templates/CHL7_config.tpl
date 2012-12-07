{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigHL7" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    {{mb_include module=system template=inc_config_str var=tag_default}}
    
  	{{mb_include module=system template=inc_config_str var=sending_application}}
  	{{mb_include module=system template=inc_config_str var=sending_facility}}
  	
    {{mb_include module=system template=inc_config_str var=assigning_authority_namespace_id}}
    {{mb_include module=system template=inc_config_str var=assigning_authority_universal_id}}
    {{mb_include module=system template=inc_config_str var=assigning_authority_universal_type_id}}
    
    {{assign var=hl7v2_versions value="CHL7v2"|static:versions}} 
    {{assign var=list_hl7v2_versions value='|'|implode:$hl7v2_versions}}
    {{mb_include module=system template=inc_config_enum var=default_version values=$list_hl7v2_versions}}
    
    <tr>
      <td colspan="2"> <hr /> </td>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=strictSejourMatch}}
    
    {{mb_include module=system template=inc_config_str var=indeterminateDoctor}}
    {{mb_include module=system template=inc_config_bool var=doctorActif}}
    
    {{mb_include module=system template=inc_config_str var=importFunctionName}}

    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>