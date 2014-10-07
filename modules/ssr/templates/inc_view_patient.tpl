{{assign var=patient_guid value=$patient->_guid}}

{{mb_default var=link   value="#$patient_guid"}}
{{mb_default var=statut value="present"}}
{{mb_default var=onclick value=null}}

<a href="{{$link}}" {{if $onclick}} onclick="{{$onclick}}" {{/if}}>
  <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient_guid}};')"
    class="{{if $statut == "attente"}}patient-not-arrived{{/if}} {{if $statut == "septique"}}septique{{/if}}"
    {{if $statut == "sorti"}}style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"{{/if}}>
    <big class="CPatient-view">{{$patient}}</big> 
  </strong>
</a>
      
{{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
{{$patient->_age}}
