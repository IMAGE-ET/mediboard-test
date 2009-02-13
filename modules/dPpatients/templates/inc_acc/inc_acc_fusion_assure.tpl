{{assign var=i value=400}}
<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2ème patient</th>
    <th width="30%" class="category">Résultat</th>
  </tr>
  
  <tr>
    <th class="title" colspan="4">Identité</th>
  </tr>
  {{include field="assure_nom"                      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_prenom"                   file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_prenom_2"                 file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_prenom_3"                 file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_prenom_4"                 file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_nom_jeune_fille"          file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_sexe"                     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_naissance"                file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_lieu_naissance"           file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_cp_naissance"             file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="_assure_pays_naissance_insee"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_nationalite"              file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_profession"               file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_matricule"                file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  
  <tr>
    <th class="title" colspan="4">Coordonnées</th>
  </tr>
  {{include field="assure_adresse" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_cp"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_ville"   file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_pays"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="assure_tel"     file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+5}}
  {{include field="assure_tel2"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+5}}
  {{include field="assure_rques"   file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
</table>