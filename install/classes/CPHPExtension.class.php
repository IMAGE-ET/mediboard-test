<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

/**
 * PHP extension prerequisite
 */
class CPHPExtension  extends CPrerequisite {
  /**
   * Check extension load
   * 
   * @see parent::check
   * 
   * @return bool
   */
  function check($strict = true) {
    if ($strict) {
      return extension_loaded(strtolower($this->name));
    }
    
    return !$this->mandatory || extension_loaded(strtolower($this->name));
  }
  
  function getAll(){
    $extensions = array();
    
    $extension = new CPHPExtension;
    $extension->name = "MySQL";
    $extension->description = "Extension d'accès aux bases de données MySQL";
    $extension->mandatory = true;
    $extension->reasons[] = "Accès à la base de donnée de principale Mediboard";
    $extension->reasons[] = "Accès aux bases de données de codage CCAM, CIM et GHM";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "MBString";
    $extension->description = "Extension de gestion des chaînes de caractères multi-octets";
    $extension->mandatory = true;
    $extension->reasons[] = "Internationalisation de Mediboard";
    $extension->reasons[] = "Interopérabilité Unicode";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "ZLib";
    $extension->description = "Extension de compression au format GNU ZIP (gz)";
    $extension->mandatory = true;
    $extension->reasons[] = "Installation de Mediboard";
    $extension->reasons[] = "Accelération substancielle de l'application via une communication web compressée";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "Zip";
    $extension->description = "Extension de compression au format zip";
    $extension->mandatory = true;
    $extension->reasons[] = "Installation de Mediboard";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "JSON";
    $extension->description = "Extension de manipulation de données au format JSON. Inclus par défaut avec PHP 5.2+";
    $extension->mandatory = true;
    $extension->reasons[] = "Passage de données de PHP vers Javascript.";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "DOM";
    $extension->description = "Extension de manipulation de fichier XML avec l'API DOM";
    $extension->mandatory = true;
    $extension->reasons[] = "Import de base de données médecin";
    $extension->reasons[] = "Interopérabilité HPRIM XML, notamment pour le PMSI";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "SOAP";
    $extension->description = "Extension permettant d'effectuer des requetes";
    $extension->reasons[] = "Requetes vers des services web et exposition de service web";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "FTP";
    $extension->description = "Extension d'accès aux serveur FTP";
    $extension->reasons[] = "Dépôt et lecture fichiers distants pour l'interopérabilité";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "BCMath";
    $extension->description = "Extension de calculs sur des nombres de précision arbitraire";
    $extension->reasons[] = "Validation des codes INSEE et ADELI";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "CURL";
    $extension->description = 
      "Extension permettant de communiquer avec des serveurs distants, grâce à de nombreux protocoles";
    $extension->reasons[] = "Connexion au site web du Conseil National l'Ordre des Médecins";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "GD";
    $extension->description = "Extension de manipulation d'image.";
    $extension->reasons[] = "GD version 2 est recommandée car elle permet un meilleur rendu";
    $extension->reasons[] = "Module de statistiques graphiques";
    $extension->reasons[] = "Fonction d'audiogrammes";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "PDO";
    $extension->description = "Extension de connectivité aux bases de données";
    $extension->reasons[] = "Interopérabilité avec des systèmes tiers";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "PDO_ODBC";
    $extension->description = "Pilote ODBC pour PDO";
    $extension->reasons[] = "Interopérabilité avec des systèmes tiers";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "APC";
    $extension->description = "Extension d'optimsation d'OPCODE et de mémoire partagée";
    $extension->reasons[] = "Accélération globale du système";
    $extensions[] = $extension;
    
    $extension = new CPHPExtension;
    $extension->name = "GnuPG";
    $extension->description = "GNU Privacy Guard (GPG ou GnuPG)";
    $extension->reasons[] = "Transmettre des messages signés et/ou chiffrés";
    $extensions[] = $extension;
    
    return $extensions;
  }
}
