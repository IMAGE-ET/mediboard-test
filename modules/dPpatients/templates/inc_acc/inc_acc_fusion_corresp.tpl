{{assign var=i value=300}}

<tr>
  <th class="title" colspan="4">Personne à prévenir</th>
</tr>
{{include field="prevenir_nom"     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="prevenir_prenom"  file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="prevenir_adresse" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="prevenir_cp"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="prevenir_ville"   file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="prevenir_tel"     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+5}}
{{include field="prevenir_parente" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}

<tr>
  <th class="title" colspan="4">Employeur</th>
</tr>
{{include field="employeur_nom"     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="employeur_adresse" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="employeur_cp"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="employeur_ville"   file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
{{include field="employeur_tel"     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+5}}
{{include field="employeur_urssaf"  file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
