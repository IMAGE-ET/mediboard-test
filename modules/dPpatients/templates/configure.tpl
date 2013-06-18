<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CPatient"             >{{tr}}CPatient{{/tr}}             </a></li>
  <li><a href="#CAntecedent"          >{{tr}}CAntecedent{{/tr}}          </a></li>
  <li><a href="#CTraitement"          >{{tr}}CTraitement{{/tr}}          </a></li>
  <li><a href="#CConstantesMedicales" >{{tr}}CConstantesMedicales{{/tr}} </a></li>
  <li><a href="#CMedecin"             >{{tr}}CMedecin{{/tr}}             </a></li>
  <li><a href="#CCorrespondantPatient">{{tr}}CCorrespondantPatient{{/tr}}</a></li>
  <li><a href="#INSEE"                >{{tr}}INSEE{{/tr}}                </a></li>
  <li><a href="#Purge"                >{{tr}}Purge{{/tr}}                </a></li>
</ul>

<hr class="control_tabs" />

<div id="CPatient" style="display: none;">
  {{mb_include template=CPatient_configure}}
  {{mb_include template=inc_configure_actions}}
</div>

<div id="CAntecedent" style="display: none;">
  {{mb_include template=CAntecedent_configure}}
</div>

<div id="CTraitement" style="display: none;">
	{{mb_include template=CTraitement_configure}}
</div>

<div id="CConstantesMedicales" style="display: none;">
  {{mb_include template=CConstantesMedicales_configure}}
</div>

<div id="CMedecin" style="display: none;">
  {{mb_include template=CMedecin_configure}}
</div>

<div id="CCorrespondantPatient" style="display: none;">
  {{mb_include template=CCorrespondantPatient_configure}}
</div>

<div id="INSEE" style="display: none;">
  {{mb_include template=inc_configure_insee}}
</div>

<div id="Purge" style="display: none;">
  {{mb_include template=inc_configure_purge_patients}}
</div>

