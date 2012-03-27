{{* $Id$ *}}

{{mb_script module=pmsi script=PMSI ajax=true}}
{{mb_default var=confirmCloture value=0}}

<div>
  {{if $object->facture}}
    {{if $m == "dPpmsi" || $can->admin}}
    <button class="cancel " onclick="PMSI.deverouilleDossier('{{$object->_id}}', '{{$object->_class}}', '{{$confirmCloture}}', '{{$m}}')">
      Déverouiller le dossier
    </button>
    {{else}}
    <div class="small-info">
      Veuillez contacter le PMSI pour déverouiller le dossier
    </div>
    {{/if}}
  {{else}}
    <button class="tick singleclick" onclick="PMSI.exportActes('{{$object->_id}}', '{{$object->_class}}', null, '{{$confirmCloture}}', '{{$m}}')">
      {{if $object->_class == "CSejour"}}
        Export des diagnostics et actes du séjour
      {{else}}
        Export des actes de l'intervention
      {{/if}}
    </button>
  {{/if}}
</div>
<div class="text">
  {{if $object->_nb_exchanges}}
    <div class="small-success">
      Export déjà effectué {{$object->_nb_exchanges}} fois
    </div>
  {{else}}
    <div class="small-info">
      Pas d'export effectué
    </div>
  {{/if}}
</div>