{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h1>Configuration du module {{tr}}module-{{$m}}-court{{/tr}}</h1>
<hr />

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Champs RPU -->  
  <tr>
    <th class="category" colspan="100">Mode RPU</th>
  </tr>
  
  {{mb_include module=system template=inc_config_enum var=old_rpu values="0|1"}}
  
  <tr>
    <th class="category" colspan="100">Niveau d'alerte de sortie des patients</th>
  </tr>
     
  {{mb_include module=system template=inc_config_str var=rpu_warning_time}}
  {{mb_include module=system template=inc_config_str var=rpu_alert_time}}
  
  <tr>
    <th class="category" colspan="100">Réglage d'affichage</th>
  </tr>

  {{mb_include module=system template=inc_config_enum var=default_view values="tous|presents"}}
  {{mb_include module=system template=inc_config_bool var=age_patient_rpu_view}}
  {{mb_include module=system template=inc_config_bool var=responsable_rpu_view}}
  {{mb_include module=system template=inc_config_bool var=diag_prat_view}}
  {{mb_include module=system template=inc_config_bool var=check_cotation}}
  
  <tr>
    <th class="category" colspan="100">Configuration des urgences</th>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=allow_change_patient}}
  {{mb_include module=system template=inc_config_enum var=sortie_prevue values="sameday|h24"}}
  {{mb_include module=system template=inc_config_bool var=only_prat_responsable}}
  
  <tr>
    <th class="category" colspan="100">Protocole d'envoi des RPUs</th>
  </tr>
  {{assign var="var" value="rpu_sender"}} 
  <tr>
   <th>
     <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
       {{tr}}config-{{$m}}-{{$var}}{{/tr}}
     </label>  
   </th>
   <td>
     <select name="{{$m}}[{{$var}}]">
       <option value="" {{if "" == $dPconfig.$m.$var}} selected="selected" {{/if}}>&mdash; Aucun</option>
       <option value="COscourSender" {{if "COscourSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}COscourSender{{/tr}}</option>
       <option value="COuralSender" {{if "COuralSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}COuralSender{{/tr}}</option>
     </select>
   </td>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=rpu_xml_validation}}
    
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>
