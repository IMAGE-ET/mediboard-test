{{mb_script module=dPccam script=CCodageCCAM ajax=true}}
<script>
  Main.add(Control.Tabs.create.curry('tabs-infos-ccam', true));
</script>

<ul id="tabs-infos-ccam" class="control_tabs">
  <li><a href="#affichage_ccam_informations_generales">Informations générales</a></li>
  <li><a href="#affichage_ccam_prise_en_charge">Prise en charge</a></li>
  {{if $numberAssociations > 0}}
    <li>
      <a href="#affichage_ccam_associations">Associations
        <small>({{$numberAssociations}})</small>
      </a>
    </li>
  {{else}}
    <li>
      <a class="empty" href="#affichage_ccam_associations">Associations ({{$numberAssociations}})</a>
    </li>
  {{/if}}
  {{if $acte_voisins|@count > 0}}
    <li>
      <a href="#affichage_ccam_actes_voisins">Actes voisins
        <small>({{$acte_voisins|@count}})</small>
      </a>
    </li>
  {{else}}
    <li>
      <a class="empty" href="#affichage_ccam_actes_voisins">Actes voisins
        <small>({{$acte_voisins|@count}})</small>
      </a>
    </li>
  {{/if}}
  {{if $code_complet->incomps|@count > 0}}
    <li>
      <a href="#affichage_ccam_incompatibilites">Incompatibilités
        <small>({{$code_complet->incomps|@count}})</small>
      </a>
    </li>
  {{else}}
    <li><a class="empty" href="#affichage_ccam_incompatibilites">Incompatibilités
        <small>({{$code_complet->incomps|@count}})</small>
      </a></li>
  {{/if}}
</ul>
<hr class="control_tabs" />
<br />
{{mb_include module=ccam template=inc_code_from_date}}