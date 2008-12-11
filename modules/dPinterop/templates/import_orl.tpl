<script type="text/javascript">

function doAction(sAction) {
  var url = new Url();
  url.setModuleAction("dPinterop", sAction);
  url.addParam("u", "import/orl");
  url.popup(400, 400, sAction);
}  

</script>

<h1>Procédure</h1>
<ol>
  <li>Créer les tables qui accueilleront les données : fichier tables_import.sql dans le zip</li>
  <li>Placer les fichiers TXT au format CSV contenus dans le zip dans la racine du module dPinterop.</li>
  <li>
    Importer le CSV dans MySQL en créant un table telle quelle en suivant chaque lien :
    <ul>
      <li><a href="#" onclick="doAction('orl_patients')">Import des patients</a></li>
      <li><a href="#" onclick="doAction('orl_praticiens')">Import des praticiens</a></li>
      <li><a href="#" onclick="doAction('orl_medecins')">Import des médecins traitants</a></li>
      <li><a href="#" onclick="doAction('orl_consult1')">Import des consultations (horaire)</a></li>
      <li><a href="#" onclick="doAction('orl_consult2')">Import des consultations (examen)</a></li>
      <li><a href="#" onclick="doAction('orl_rdv');">Import RDV à venir</a></li>
    </ul>
  </li>
  <li>Ajouter un champ mb_id, indiquant la clé de l'objet correspondant dans la base Mediboard : utiliser le fichier mb_id.sql dans le zip</li>
  <li>Créer l'objet dans la base mediboard ou récupérer son ID:
    <ul>
      <li>S'il n'existe pas le créer avec les champs importés</li>
      <li>s'il existe proposer une itervention humaine ou compléter les champs NULL</li>
    </ul>
  <li>
    Voici les scripts à lancer :
    <ul>
      <li><a href="#" onclick="doAction('orl_put_praticiens')">Envoie des praticiens vers Mediboard</a></li>
      <li><a href="#" onclick="doAction('orl_put_medecins')">Envoie des medecins vers Mediboard</a></li>
      <li><a href="#" onclick="doAction('orl_put_patients')">Envoie des patients vers Mediboard</a></li>
      <li><a href="#" onclick="doAction('orl_put_consult')">Envoie des consultations vers Mediboard</a></li>
      <li><a href="#" onclick="doAction('orl_put_rdv')">Envoie des rdv à venir vers Mediboard</a></li>
    </ul>
  </li>

  <li>Inscrire l'ID dans le champ mb_id de la table d'import</li>
  <li>
    Ajout des fichiers liés :
    <ul>
      <li>Placer le dossier doc_recus et le fichier doc_recus.txt dans le module dPinterop</li>
      <li><a href="#" onclick="doAction('orl_files')">Import des fichiers</a></li>
      <li><a href="#" onclick="doAction('orl_put_files')">Envoie des fichiers vers Mediboard</a></li>
      <li>Les fichiers ainsi envoyés iront se placer (le cas échéant) dans la dernière consultation du patient lié</li>
      <li><a href="#" onclick="doAction('orl_put_correct_files')">Petite correction</a> (les fichiers allaient dans n'importe quelle consultation et pas eulement chez les ORL)</li>
      <li>Placer le dossier courriers et le fichier chemin_courrier.txt dans le module dPinterop</li>
      <li><a href="#" onclick="doAction('orl_courriers')">Import des courriers</a></li>
      <li><a href="#" onclick="doAction('orl_put_courriers')">Envoie des courriers vers Mediboard</a></li>
    </ul>
  </li>
  
</ol>