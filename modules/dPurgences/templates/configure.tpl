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
  
  {{assign var=var value=old_rpu}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="bool" name="{{$m}}[{{$var}}]">
        <option value="0" {{if 0 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-0{{/tr}}</option>
        <option value="1" {{if 1 == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-1{{/tr}}</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="100">Niveau d'alerte de sortie des patients</th>
  </tr>
  
  {{assign var="var" value="rpu_warning_time"}}
  <tr>
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" size="40" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>
   
  <tr>
    {{assign var="var" value="rpu_alert_time"}}
    <th>
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td>
      <input class="str" size="40" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="100">Réglage d'affichage</th>
  </tr>
  
  {{assign var=var value=default_view}} 
  <tr>
   <th>
     <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
       {{tr}}config-{{$m}}-{{$var}}{{/tr}}
     </label>  
   </th>
   <td>
     <select name="{{$m}}[{{$var}}]">
       <option value="tous" {{if "tous" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-tous{{/tr}}</option>
       <option value="presents" {{if "presents" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-presents{{/tr}}</option>
     </select>
   </td>
  </tr>  

 {{mb_include module=system template=inc_config_bool var=motif_rpu_view}}
  
  {{mb_include module=system template=inc_config_bool var=age_patient_rpu_view}}

  {{mb_include module=system template=inc_config_bool var=responsable_rpu_view}}
  
  {{mb_include module=system template=inc_config_bool var=diag_prat_view}}
  
  <tr>
    <th class="category" colspan="100">Configuration des urgences</th>
  </tr>
  
  {{mb_include module=system template=inc_config_bool var=allow_change_patient}}
  
  {{assign var="var" value="sortie_prevue"}} 
  <tr>
   <th>
     <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
       {{tr}}config-{{$m}}-{{$var}}{{/tr}}
     </label>  
   </th>
   <td>
     <select name="{{$m}}[{{$var}}]">
       <option value="sameday" {{if "sameday" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-sameday{{/tr}}</option>
       <option value="h24" {{if "h24" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$var}}-h24{{/tr}}</option>
     </select>
   </td>
  </tr>
  
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
