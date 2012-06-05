{{* $Id: inc_main_courante.tpl 8462 2010-04-05 09:55:09Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8462 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient_guid value=$patient->_guid}}

{{mb_default var=link   value="#$patient_guid"}}
{{mb_default var=statut value="present"}}
{{mb_default var=onclick value=null}}

<a href="{{$link}}" {{if $onclick}} onclick="{{$onclick}}" {{/if}}>
  <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient_guid}};')"
    {{if $statut == "attente"}}class="patient-not-arrived"{{/if}}
    {{if $statut == "sorti"}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
    <big class="CPatient-view">{{$patient}}</big> 
  </strong>
</a>
      
{{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
{{$patient->_age}}
