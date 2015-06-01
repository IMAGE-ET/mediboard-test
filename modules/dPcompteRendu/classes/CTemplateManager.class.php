<?php

/**
 * $Id$
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Gestion avancée de documents (destinataires, listes de choix, etc.)
 */
class CTemplateManager {
  public $editor = "ckeditor";

  public $sections      = array();
  public $helpers       = array();
  public $allLists      = array();
  public $lists         = array();
  public $graphs        = array();
  public $textes_libres = array();

  public $template;
  public $document;
  public $usedLists     = array();
  public $isCourrier;

  public $valueMode     = true; // @todo : changer en applyMode
  public $isModele      = true;
  public $printMode     = false;
  public $simplifyMode  = false;
  public $messageMode   = false;
  public $parameters    = array();
  public $font;
  public $size;
  public $destinataires = array();

  private static $barcodeCache = array();

  /**
   * Constructeur
   *
   * @param array $parameters [optional]
   *
   * @return void
   */
  function CTemplateManager($parameters = array()) {
    $user = CMediusers::get();
    $this->parameters = $parameters;

    if (!isset($parameters["isBody"]) || (isset($parameters["isBody"]) && $parameters["isBody"] == 1)) {
      $this->addProperty("Courrier - Formule de politesse - Début", "[Courrier - Formule de politesse - Début]");
      $this->addProperty("Courrier - Formule de politesse - Fin", "[Courrier - Formule de politesse - Fin]");
      $this->addProperty("Courrier - nom destinataire"     , "[Courrier - nom destinataire]");
      $this->addProperty("Courrier - adresse destinataire" , "[Courrier - adresse destinataire]");
      $this->addProperty("Courrier - cp ville destinataire", "[Courrier - cp ville destinataire]");
      $this->addProperty("Courrier - confraternite"        , "[Courrier - confraternite]");
      $this->addProperty("Courrier - copie à - simple"     , "[Courrier - copie à - simple]");
      $this->addProperty("Courrier - copie à - simple (multiligne)", "[Courrier - copie à - simple (multiligne)]");
      $this->addProperty("Courrier - copie à - complet", "[Courrier - copie à - complet]");
      $this->addProperty("Courrier - copie à - complet (multiligne)", "[Courrier - copie à - complet (multiligne)]");
    }

    $now = CMbDT::dateTime();
    $this->addDateProperty("Général - date du jour", $now);
    $this->addLongDateProperty("Général - date du jour (longue)", $now);
    $this->addLongDateProperty("Général - date du jour (longue, minuscule)", $now, true);
    $this->addTimeProperty("Général - heure courante", $now);

    if (isset($parameters["isModele"])) {
      $this->addProperty("Meta Données - Date de verrouillage - Date");
      $this->addProperty("Meta Données - Date de verrouillage - Heure");
      $this->addProperty("Meta Données - Verrouilleur - Nom");
      $this->addProperty("Meta Données - Verrouilleur - Prénom");
      $this->addProperty("Meta Données - Verrouilleur - Initiales");
    }

    // Connected user
    $user_complete = $user->_view;
    if ($user->isPraticien()) {
      if ($user->titres) {
        $user_complete .= "\n" . $user->titres;
      }
      if ($user->spec_cpam_id) {
        $spec_cpam = $user->loadRefSpecCPAM();
        $user_complete .= "\n" . $spec_cpam->text;
      }
      if ($user->adeli) {
        $user_complete .= "\nAdeli : " . $user->adeli;
      }
      if ($user->rpps) {
        $user_complete .= "\nRPPS : " . $user->rpps;
      }
      if ($user->_user_email) {
        $user_complete .= "\nE-mail : " . $user->_user_email;
      }
    }

    // Initials
    $elements_first_name = preg_split("/[ -]/", $user->_user_first_name);
    $initials_first_name = "";

    foreach ($elements_first_name as $_element) {
      $initials_first_name .= strtoupper(substr($_element, 0, 1));
    }

    $elements_last_name = preg_split("/[ -]/", $user->_user_last_name);
    $initials_last_name = "";

    foreach ($elements_last_name as $_element) {
      $initials_last_name .= strtoupper(substr($_element, 0, 1));
    }

    $this->addProperty("Général - rédacteur"        , $user->_shortview);
    $this->addProperty("Général - rédacteur - prénom", $user->_user_first_name);
    $this->addProperty("Général - rédacteur - nom"  , $user->_user_last_name);
    $this->addProperty("Général - rédacteur complet", $user_complete);
    $this->addProperty("Général - rédacteur (initiales) - prénom", $initials_first_name);
    $this->addProperty("Général - rédacteur (initiales) - nom", $initials_last_name);
    if (CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") && CAppUI::pref("pdf_and_thumbs")) {
      $this->addProperty("Général - numéro de page", "[Général - numéro de page]");
    }
  }

  /**
   * Retrouve un paramètre dans un tableau
   *
   * @param string $name    nom du paramètre
   * @param object $default [optional] valeur par défaut, si non retrouvé
   *
   * @return string
   */
  function getParameter($name, $default = null) {
    return CValue::read($this->parameters, $name, $default);
  }

  /**
   * Construit l'élément html pour les champs, listes de choix et textes libres.
   *
   * @param string $spanClass classe de l'élément
   * @param string $text      contenu de l'élément
   *
   * @return string
   */
  function makeSpan($spanClass, $text) {
    // Escape entities cuz CKEditor does so
    $text = CMbString::htmlEntities($text);

    // Keep backslashed double quotes instead of quotes
    // cuz CKEditor creates double quoted attributes
    return "<span class=\"{$spanClass}\">{$text}</span>";
  }

  /**
   * Ajoute un champ
   *
   * @param string  $field      nom du champ
   * @param string  $value      [optional]
   * @param array   $options    [optional]
   * @param boolean $htmlescape [optional]
   *
   * @return void
   */
  function addProperty($field, $value = null, $options = array(), $htmlescape = true) {
    if ($htmlescape) {
      $value = CMbString::htmlSpecialChars($value);
    }

    $sec = explode(' - ', $field, 3);
    switch (count($sec)) {
      case 3:
        $section  = $sec[0];
        $item     = $sec[1];
        $sub_item = $sec[2];
        break;
      case 2:
        $section  = $sec[0];
        $item     = $sec[1];
        $sub_item = '';
        break;
      default:
        trigger_error("Error while exploding the string", E_USER_ERROR);
        return;
    }

    if (!array_key_exists($section, $this->sections)) {
      $this->sections[$section] = array();
    }
    if ($sub_item != '' && !array_key_exists($item, $this->sections[$section])) {
      $this->sections[$section][$item] = array();
    }

    if ($sub_item == '') {
      $this->sections[$section][$field] = array (
        "view"      => CMbString::htmlEntities($item),
        "field"     => $field,
        "value"     => $value,
        "fieldHTML" => CMbString::htmlEntities("[{$field}]", ENT_QUOTES),
        "valueHTML" => $value,
        "shortview" => $section . " - " . $item,
        "options"   => $options
      );
    }
    else {
      $this->sections[$section][$item][$sub_item] = array (
        "view"      => CMbString::htmlEntities($sub_item),
        "field"     => $field,
        "value"     => $value,
        "fieldHTML" => CMbString::htmlEntities("[{$field}]", ENT_QUOTES),
        "valueHTML" => $value,
        "shortview" => $section . " - " . $item . " - " . $sub_item,
        "options"   => $options
      );
    }

    // Barcode
    if (isset($options["barcode"])) {
      if ($sub_item) {
        $_field = &$this->sections[$section][$item][$sub_item];
      }
      else {
        $_field = &$this->sections[$section][$field];
      }

      if ($this->valueMode) {
        $src = $this->getBarcodeDataUri($_field['value'], $options["barcode"]);
      }
      else {
        $src = $_field['fieldHTML'];
      }

      $_field["field"] = "";

      if ($options["barcode"]["title"]) {
        $_field["field"] .= $options["barcode"]["title"]."<br />";
      }

      $_field["field"] .= "<img alt=\"$field\" src=\"$src\" ";

      foreach ($options["barcode"] as $name => $attribute) {
        $_field["field"] .= " $name=\"$attribute\"";
      }

      $_field["field"] .= "/>";
    }

    // Custom data
    if (isset($options["data"])) {
      $_field = &$this->sections[$section][$item][$sub_item];
      $data = $options["data"];
      $view = $_field['field'];
      $_field["field"] = "[<span data-data=\"$data\">$view</span>]";
    }

    // Image (from a CFile object)
    if (isset($options["image"])) {
      $_field = &$this->sections[$section][$field];
      $data = "";

      if ($this->valueMode) {
        $file = new CFile();
        $file->load($_field['value']);
        if ($file->_id) {
          // Resize the image
          CAppUI::requireLibraryFile("phpThumb/phpthumb.class");
          include_once "lib/phpThumb/phpThumb.config.php";
          $thumbs = new phpthumb();
          $thumbs->setSourceFilename($file->_file_path);
          $thumbs->w = 640;
          $thumbs->f = "png";
          $thumbs->GenerateThumbnail();
          $data = "data:".$file->file_type.";base64,".urlencode(base64_encode($thumbs->IMresizedData));
        }
      }
      $src = $this->valueMode ? $data :$_field['fieldHTML'];
      $_field["field"] = "<img src=\"".$src."\" />";
    }
  }

  /**
   * Ajoute un champ de type date
   *
   * @param string $field nom du champ
   * @param string $value [optional]
   *
   * @return void
   */
  function addDateProperty($field, $value = null) {
    $value = $value ? CMbDT::format($value, CAppUI::conf("date")) : "";
    $this->addProperty($field, $value);
  }

  /**
   * Ajoute un champ de type date longue
   *
   * @param string  $field     Nom du champ
   * @param string  $value     Valeur du champ
   * @param boolean $lowercase Champ avec des minuscules
   *
   * @return void
   */
  function addLongDateProperty($field, $value, $lowercase = false) {
    $value = $value ? ucfirst(CMbDT::format($value, CAppUI::conf("longdate"))) : "";
    $this->addProperty($field, $lowercase ? CMbString::lower($value) : $value);
  }

  /**
   * Ajoute un champ de type heure
   *
   * @param string $field Nom du champ
   * @param string $value Valeur du champ
   *
   * @return void
   */
  function addTimeProperty($field, $value = null) {
    $value = $value ? CMbDT::format($value, CAppUI::conf("time")) : "";
    $this->addProperty($field, $value);
  }

  /**
   * Ajoute un champ de type date et heure
   *
   * @param string $field Nom du champ
   * @param string $value Valeur du champ
   *
   * @return void
   */
  function addDateTimeProperty($field, $value = null) {
    $value = $value ? CMbDT::format($value, CAppUI::conf("datetime")) : "";
    $this->addProperty($field, $value);
  }

  /**
   * Ajoute un champ de type liste
   *
   * @param string $field       Nom du champ
   * @param array  $items       Liste de valeurs
   * @param boolean $htmlescape [optional]
   * @return void
   */
  function addListProperty($field, $items = null, $htmlescape = true) {
    $this->addProperty($field, $this->makeList($items, $htmlescape), null, false);
  }

  /**
   * Ajoute un champ de type image
   *
   * @param string $field   Nom du champ
   * @param int    $file_id Identifiant du fichier
   *
   * @return void
   */
  function addImageProperty($field, $file_id) {
    $this->addProperty($field, $file_id, array("image" => 1), false);
  }

  /**
   * Génération de la source html pour la liste d'items
   *
   * @param array   $items      liste d'items
   * @param boolean $htmlescape [optional]
   *
   * @return string|null
   */
  function makeList($items, $htmlescape = true) {
    if (!$items) {
      return null;
    }

    // Make a list out of a string
    if (!is_array($items)) {
      $items = array($items);
    }

    // Escape content
    if ($htmlescape) {
      $items = array_map(array("CMbString", "htmlEntities"), $items);
    }

    // HTML production
    switch ($default = CAppUI::pref("listDefault")) {
      case "ulli":
        $html = "<ul>";
        foreach ($items as $item) {
          $html .= "<li>$item</li>";
        }
        $html.= "</ul>";
        break;

      case "br":
        $html = "";
        $prefix = CAppUI::pref("listBrPrefix");
        foreach ($items as $item) {
          $html .= "<br />$prefix $item";
        }
        break;

      case "inline":
        $separator = CAppUI::pref("listInlineSeparator");
        $html = implode(" $separator ", $items);
        break;

      default:
        $html = "";
        trigger_error("Default style for list is unknown '$default'", E_USER_WARNING);
        break;
    }

    return $html;
  }

  /**
   * Ajoute un champ de type graphique
   *
   * @param string $field   Champ
   * @param array  $data    Tableau de données
   * @param array  $options Options
   *
   * @return void
   */
  function addGraph($field, $data, $options = array()) {
    $this->graphs[utf8_encode($field)] = array(
      "data" => $data,
      "options" => $options,
      "name" => utf8_encode($field)
    );

    $this->addProperty($field, $field, null, false);
  }

  /**
   * Ajoute un champ de type code-barre
   *
   * @param string $field   Nom du champ
   * @param string $data    Code barre
   * @param array  $options Options
   *
   * @return void
   */
  function addBarcode($field, $data, $options = array()) {
    $options = array_replace_recursive(
      array(
        "barcode" => array(
          "width"  => 220,
          "height" => 60,
          "class"  => "barcode",
          "title"  => "",
        )
      ),
      $options
    );

    $this->addProperty($field, $data, $options, false);
  }

  /**
   * Ajoute un champ de type liste
   *
   * @param string $name Nom de la liste
   *
   * @return void
   */
  function addList($name) {
    $this->lists[$name] = array (
      "name" => $name,
      // @todo : passer en regexp
      //"nameHTML" => $this->makeSpan("name", "[Liste - {$name}]"));
      "nameHTML" => CMbString::htmlEntities("[Liste - {$name}]")
    );
  }

  /**
   * Ajoute une aide à la saisie au templateManager
   *
   * @param string $name Nom de l'aide à la saisie
   * @param string $text Texte de remplacement de l'aide
   *
   * @return void
   */
  function addHelper($name, $text) {
    $this->helpers[$name] = $text;
  }

  function addAdvancedData($name, $data, $value) {
    $options = array(
      "data" => $data
    );

    $this->addProperty($name, $value, $options, false);
  }

  /**
   * Applique les champs variable sur un document
   *
   * @param CCompteRendu|CPack $template TemplateManager sur lequel s'applique le document
   *
   * @return void
   */
  function applyTemplate($template) {
    assert($template instanceof CCompteRendu || $template instanceof CPack);

    if ($template instanceof CCompteRendu) {
      $this->font = $template->font ? CCompteRendu::$fonts[$template->font] : "";
      $this->size = $template->size;

      if (!$this->valueMode) {
        $this->setFields($template->object_class);
      }

      $this->renderDocument($template->_source);
    }
    else {
      $this->renderDocument($template->_source);
    }
  }

  /**
   * Affiche l'éditeur de texte avec le contenu du document
   *
   * @return void
   */
  function initHTMLArea () {
    // Don't use CValue::setSession which uses $m
    $_SESSION["dPcompteRendu"]["templateManager"] = gzcompress(serialize($this));

    $smarty = new CSmartyDP("modules/dPcompteRendu");
    $smarty->assign("templateManager", $this);
    $smarty->display("init_htmlarea.tpl");
  }

  /**
   * Applique les champs variable d'un objet
   *
   * @param string $modeleType classe de l'objet
   *
   * @return void
   */
  function setFields($modeleType) {
    if ($modeleType) {
      $object = new $modeleType;
      /** @var CMbObject $object */
      $object->fillTemplate($this);
    }
  }

  /**
   * Charge les listes de choix pour un utilisateur, ou la fonction et l'établissement de l'utilisateur connecté
   *
   * @param int $user_id         identifiant de l'utilisateur
   * @param int $compte_rendu_id identifiant du compte-rendu
   *
   * @return void
   */
  function loadLists($user_id, $compte_rendu_id = 0) {
    $where = array();
    $user = CMediusers::get($user_id);
    $user->loadRefFunction();
    if ($user_id) {
      $where[] = "(
        user_id = '$user->user_id' OR
        function_id = '$user->function_id' OR
        group_id = '{$user->_ref_function->group_id}'
      )";
    }
    else {
      $compte_rendu = new CCompteRendu();
      $compte_rendu->load($compte_rendu_id);
      $where[] = "(
        function_id IN('$user->function_id', '$compte_rendu->function_id') OR
        group_id IN('{$user->_ref_function->group_id}', '$compte_rendu->group_id')
      )";
    }

    $where[] = $user->getDS()->prepare("`compte_rendu_id` IS NULL OR compte_rendu_id = %", $compte_rendu_id);
    $order = "user_id, function_id, group_id, nom ASC";
    $lists = new CListeChoix();
    $this->allLists = $lists->loadList($where, $order);

    foreach ($this->allLists as $list) {
      /** @var CListeChoix $list */
      $this->addList($list->nom);
    }
  }

  /**
   * Charge les listes de choix d'une classe pour un utilisateur, sa fonction et son établissement
   *
   * @param int    $user_id           identifiant de l'utilisateur
   * @param string $modeleType        classe ciblée
   * @param string $other_function_id autre fonction
   *
   * @return void
   */
  function loadHelpers($user_id, $modeleType, $other_function_id = "") {
    $compte_rendu = new CCompteRendu();
    $ds = $compte_rendu->getDS();

    // Chargement de l'utilisateur courant
    $currUser = CMediusers::get($user_id);

    $order = "name";

    // Where user_id
    $whereUser = array();
    $whereUser["user_id"] = $ds->prepare("= %", $user_id);
    $whereUser["class"]   = $ds->prepare("= %", $compte_rendu->_class);

    // Where function_id
    $whereFunc = array();
    $whereFunc["function_id"] = $other_function_id ?
      "IN ($currUser->function_id, $other_function_id)" : $ds->prepare("= %", $currUser->function_id);
    $whereFunc["class"]       = $ds->prepare("= %", $compte_rendu->_class);

    // Where group_id
    $whereGroup = array();
    $group = CGroups::loadCurrent();
    $whereGroup["group_id"] = $ds->prepare("= %", $group->_id);
    $whereGroup["class"]       = $ds->prepare("= %", $compte_rendu->_class);

    // Chargement des aides
    $aide = new CAideSaisie();

    /** @var CAideSaisie $aidesUser */
    $aidesUser   = $aide->loadList($whereUser, $order, null, "aide_id");

    /** @var CAideSaisie $aidesFunc */
    $aidesFunc   = $aide->loadList($whereFunc, $order, null, "aide_id");

    /** @var CAideSaisie $aidesGroup */
    $aidesGroup  = $aide->loadList($whereGroup, $order, null, "aide_id");

    $this->helpers["Aide de l'utilisateur"] = array();
    foreach ($aidesUser as $aideUser) {
      if ($aideUser->depend_value_1 == $modeleType || $aideUser->depend_value_1 == "") {
        $this->helpers["Aide de l'utilisateur"][CMbString::htmlEntities($aideUser->name)] = CMbString::htmlEntities($aideUser->text);
      }
    }
    $this->helpers["Aide de la fonction"] = array();
    foreach ($aidesFunc as $aideFunc) {
      if ($aideFunc->depend_value_1 == $modeleType || $aideFunc->depend_value_1 == "") {
        $this->helpers["Aide de la fonction"][CMbString::htmlEntities($aideFunc->name)] = CMbString::htmlEntities($aideFunc->text);
      }
    }
    $this->helpers["Aide de l'&eacute;tablissement"] = array();
    foreach ($aidesGroup as $aideGroup) {
      if ($aideGroup->depend_value_1 == $modeleType || $aideGroup->depend_value_1 == "") {
        $this->helpers["Aide de l'&eacute;tablissement"][CMbString::htmlEntities($aideGroup->name)] =
          CMbString::htmlEntities($aideGroup->text);
      }
    }
  }

  /**
   * Get the data URI of a barcode
   *
   * @param string $code    Code
   * @param array  $options Options
   *
   * @return null|string
   */
  function getBarcodeDataUri($code, $options) {
    if (!$code) {
      return null;
    }

    $size = "{$options['width']}x{$options['width']}";

    if (isset(self::$barcodeCache[$code][$size])) {
      return self::$barcodeCache[$code][$size];
    }

    CAppUI::requireLibraryFile("tcpdf/barcode/barcode");
    CAppUI::requireLibraryFile("tcpdf/barcode/c128bobject");
    CAppUI::requireLibraryFile("tcpdf/barcode/cmb128bobject");

    $bc_options = (BCD_DEFAULT_STYLE | BCS_DRAW_TEXT) & ~BCS_BORDER;
    $barcode = new CMb128BObject($options["width"] * 2, $options["height"] * 2, $bc_options, $code);

    $barcode->SetFont(7);
    $barcode->DrawObject(2);

    ob_start();
    $barcode->FlushObject();
    $image = ob_get_contents();
    ob_end_clean();

    $barcode->DestroyObject();

    $image = "data:image/png;base64,".urlencode(base64_encode($image));

    return self::$barcodeCache[$code][$size] = $image;
  }

  /**
   * Get the regex to replace data
   *
   * @param string $data Data key
   *
   * @return string
   */
  protected function getDataRegex($data) {
    $data_re = preg_quote($data, "/");

    return '/(\[<span data-data=["\']'.$data_re.'["\']>[^<]+<\/span>\])/ms';
  }

  /**
   * Applique les champs variables sur une source html
   *
   * @param string $_source source html
   *
   * @return void
   */
  function renderDocument($_source) {
    $fields = array();
    $values = array();

    $fields_regex = array();
    $values_regex = array();

    foreach ($this->sections as $properties) {
      foreach ($properties as $key => $property) {
        if (strpos($key, ' - ') === false) {
          foreach ($property as $_property) {
            if ($_property["valueHTML"] && isset($_property["options"]["barcode"])) {
              $options = $_property["options"]["barcode"];
              $image = $this->getBarcodeDataUri($_property["valueHTML"], $options);
              $fields[] = utf8_decode(html_entity_decode("src=\"{$_property['fieldHTML']}\""));
              $values[] = "src=\"$image\"";
            }
            elseif (isset($_property["options"]["data"])) {
              $data = $_property["options"]["data"];
              $fields_regex[] = $this->getDataRegex($data);
              $values_regex[] = $_property["valueHTML"];
            }
            else {
              $fields[] = $_property["fieldHTML"];
              $values[] = nl2br($_property["valueHTML"]);
            }
          }
        }
        else if ($property["valueHTML"] && isset($property["options"]["barcode"])) {
          $options = $property["options"]["barcode"];

          $image = $this->getBarcodeDataUri($property["valueHTML"], $options);

          $fields[] = "src=\"{$property['fieldHTML']}\"";
          $values[] = "src=\"$image\"";
        }
        else if (isset($property["options"]["data"])) {
          $data = $property["options"]["data"];
          $fields_regex[] = $this->getDataRegex($data);
          $values_regex[] = $property["valueHTML"];
        }
        else if ($property["valueHTML"] && isset($property["options"]["image"])) {
          $file = new CFile();
          $file->load($property['value']);
          $src = $file->getDataURI();
          $fields[] = "src=\"{$property['fieldHTML']}\"";
          $values[] = "src=\"$src\"";
        }
        else {
          $property["fieldHTML"] = preg_replace("/'/", '&#039;', $property["fieldHTML"]);
          $fields[] = $property["fieldHTML"];
          $values[] =  nl2br($property["valueHTML"]);
        }
      }
    }

    if (count($fields_regex)) {
      $_source = preg_replace($fields_regex, $values_regex, $_source);
    }

    if (count($fields)) {
      $_source = str_ireplace($fields, $values, $_source);
    }

    if (count($fields_regex) || count($fields)) {
      $this->document = $_source;
    }
  }

  /**
   * Obtention des listes utilisées dans le document
   *
   * @param CListeChoix[] $lists Listes de choix
   *
   * @return CListeChoix[]
   */
  function getUsedLists($lists) {
    $this->usedLists = array();

    foreach ($lists as $value) {
      $nom = CMbString::htmlEntities(stripslashes("[Liste - $value->nom]"), ENT_QUOTES);
      $pos = strpos($this->document, $nom);
      if ($pos !== false) {
        $this->usedLists[$pos] = $value;
      }
    }

    ksort($this->usedLists);
    return $this->usedLists;
  }

  /**
   * Vérification s'il s'agit d'un courrier
   *
   * @return bool
   */
  function isCourrier() {
    $pos = strpos($this->document, "[Courrier -");
    if ($pos) {
      $this->isCourrier = true;
    }
    return $this->isCourrier;
  }
}
