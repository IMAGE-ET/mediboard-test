<table class="form">
  {{if $compte_rendu->valide ||$compte_rendu->_is_auto_locked}}
    <tr>
      <th class="title" colspan="2">Verrouillé</th>
    </tr>
    {{if $compte_rendu->valide}}
      <tr>
        <th>Par</th>
        <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$compte_rendu->_ref_locker}}</td>
      </tr>
    {{/if}}
    {{if $compte_rendu->_is_auto_locked}}
      <tr>
        <td colspan="2">
          Document verrouillé automatiquement
          (veuillez contacter un administrateur pour connaitre la configuration du verrouillage automatique)
        </td>
      </tr>
    {{/if}}
  {{else}}
    <tr>
      <th class="title" colspan="2">Document non verrouillé</th>
    </tr>
  {{/if}}
</table>
