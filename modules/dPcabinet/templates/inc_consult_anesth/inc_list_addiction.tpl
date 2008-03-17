<script type="text/javascript">

Addiction = {
  remove: function(oForm, onComplete) {
    var oOptions = {
      typeName: 'cette addiction',
      ajax: 1,
      target: 'systemMsg'
    };
    
    var oOptionsAjax = {
      onComplete: onComplete
    };
    
    confirmDeletion(oForm, oOptions, oOptionsAjax);
  }
}

</script>

<strong>Addictions du patient</strong>

<ul>
{{if $patient->_ref_dossier_medical->_ref_addictions}}
  {{foreach from=$patient->_ref_dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
  {{if $list_addiction|@count}}
  {{foreach from=$list_addiction item=curr_addiction}}
  <li>
    <form name="delAddictionFrm-{{$curr_addiction->_id}}" action="?m=dPcabinet" method="post">

    <input type="hidden" name="m" value="dPpatients" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_addiction_aed" />
    
    {{mb_field object=$curr_addiction field="addiction_id" hidden=1 prop=""}}         

    <button class="trash notext" type="button" onclick="Addiction.remove(this.form, reloadDossierMedicalPatient)">
      {{tr}}Delete{{/tr}}        
    </button> 
    
    {{if $_is_anesth && $sejour->_id}}
    <button class="add notext" type="button" onclick="copyAddiction({{$curr_addiction->_id}})">
      {{tr}}Add{{/tr}}        
    </button>
    {{/if}}
            
    </form>

    <strong>{{tr}}CAddiction.type.{{$curr_type}}{{/tr}}</strong> :
    <!-- Ajout d'un affichage d'historique de la creation de l'addiction -->
    <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CAddiction', object_id: {{$curr_addiction->_id}} } })">
      {{$curr_addiction->addiction}}
    </span>

  </li>
  {{/foreach}}
  {{/if}}
  {{/foreach}}
  {{else}}
  <li><em>Pas d'addictions</em></li>
  {{/if}}
</ul>
