{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sa
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigSA" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="sa"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=configure_handler class_handler=CSaObjectHandler}}
    {{mb_include module=system template=configure_handler class_handler=CSaEventObjectHandler}}
    
    <tr>
      <th class="category" colspan="10">{{tr}}config-traitement-{{$mod}}{{/tr}}</th>
    </tr>
        
    {{mb_include module=system template=inc_config_bool var=server}}
    
    {{mb_include module=system template=inc_config_enum var=trigger_sejour       values=facture|sortie_reelle|testCloture}}
    {{mb_include module=system template=inc_config_bool var=send_actes_consult}}
    {{mb_include module=system template=inc_config_bool var=send_actes_interv}}
    
    {{mb_include module=system template=inc_config_enum var=trigger_operation    values=facture|testCloture|sortie_reelle}}
    
    {{mb_include module=system template=inc_config_enum var=trigger_consultation values=valide|facture|sortie_reelle}}
    
    {{mb_include module=system template=inc_config_bool var=send_only_with_ipp_nda}}
    {{assign var=list_types_sejour value='|'|implode:$sejour_types}}
    {{mb_include module=system template=inc_config_enum var=send_only_with_type values=|$list_types_sejour}}

    {{mb_include module=system template=inc_config_bool var=send_diags_with_actes}}

    {{mb_include module=system template=inc_config_bool var=facture_codable_with_sejour}}
    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>