<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * CFile controller
 */
class CFileAddEdit extends CDoObjectAddEdit {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    $this->CDoObjectAddEdit("CFile", "file_id");

    global $m;    
    $this->redirect = "m=$m"; 

    if ($dialog = CValue::post("dialog")) {
      $this->redirect      .= "&a=upload_file&dialog=1";
      $this->redirectStore = "m=$m&a=upload_file&dialog=1&uploadok=1";
    }
  }

  /**
   * @see parent::doStore()
   */
  function doStore() {
    $upload     = null;

    if (CValue::POST("_from_yoplet") == 1) {
      /** @var CFile $obj */
      $obj = $this->_obj;
      $array_file_name = array();
      $path = CAppUI::conf("dPfiles yoplet_upload_path");

      if (!$path) {
        $path = "tmp";
      }

      // On retire les backslashes d'escape
      $file_name = stripslashes($this->request['_file_path']);

      // Récupération du nom de l'image en partant de la fin de la chaîne
      // et en rencontrant le premier \ ou /
      preg_match('@[\\\/]([^\\\/]*)$@i', $file_name, $array_file_name);
      $file_name = $array_file_name[1];

      $extension = strrchr($file_name, '.');
      $_rename = $this->request['_rename'] ? $this->request['_rename'] : 'upload';
      $file_path = "$path/". $this->request['_checksum'];

      $obj->file_name = $_rename == 'upload' ? $file_name : $_rename . $extension;
      $obj->_old_file_path = $this->request['_file_path'];
      $obj->doc_size = filesize($file_path);
      $obj->author_id = CAppUI::$user->_id;
      if (CModule::getActive("cda")) {
        $obj->type_doc = $this->request["type_doc"];
      }
      $obj->fillFields();
      $obj->updateFormFields();
      $obj->file_type = CMbPath::guessMimeType($file_name);

      if ($msg = $obj->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
      else {
        $obj->forceDir();
        $obj->moveFile($file_path);
      }
      return parent::doStore();
    }

    if (isset($_FILES["formfile"])) {
      $aFiles = array();
      $upload =& $_FILES["formfile"];
      $_file_category_id = CValue::post("_file_category_id");
      $language = CValue::post("language");
      $type_doc = CValue::post("type_doc");
      $named    = CValue::post("named");
      $rename   = CValue::post("_rename");

      CValue::setSession("_rename", $rename);

      foreach ($upload["error"] as $fileNumber => $etatFile) {
        if (!$named) {
          $rename = $rename ? $rename . strrchr($upload["name"][$fileNumber], '.') : "";
        }

        if ($upload["name"][$fileNumber]) {
          $aFiles[] = array(
            "name"             => $upload["name"][$fileNumber],
            "type"             => CMbPath::guessMimeType($upload["name"][$fileNumber]),
            "tmp_name"         => $upload["tmp_name"][$fileNumber],
            "error"            => $upload["error"][$fileNumber],
            "size"             => $upload["size"][$fileNumber],
            "language"         => $language,
            "type_doc"         => $type_doc,
            "file_category_id" => $_file_category_id,
            "object_id"        => CValue::post("object_id"),
            "object_class"     => CValue::post("object_class"),
            "_rename"          => $rename
          );
        }
      }

      $merge_files = CValue::post("_merge_files");

      if ($merge_files) {
        $pdf = new CMbPDFMerger();

        $this->_obj = new $this->_obj->_class;

        /** @var CFile $obj */
        $obj = $this->_obj;
        $file_name = "";
        $nb_converted = 0;

        foreach ($aFiles as $key => $file) {
          $converted = 0;
          if ($file["error"] == UPLOAD_ERR_NO_FILE) {
            continue;
          }

          if ($file["error"] != 0) {
            CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-".$file["error"]), UI_MSG_ERROR);
            continue;
          }

          // Si c'est un pdf, on le rajoute sans aucun traitement
          if (substr(strrchr($file["name"], '.'), 1) == "pdf") {
            $file_name .= substr($file["name"], 0, strpos($file["name"], '.'));
            $pdf->addPDF($file["tmp_name"], 'all');
            $nb_converted ++;
            $converted = 1;
          }
          // Si le fichier est convertible en pdf
          else if ($obj->isPDFconvertible($file["name"]) && $obj->convertToPDF($file["tmp_name"], $file["tmp_name"]."_converted")) {
            $pdf->addPDF($file["tmp_name"]."_converted", 'all'); 
            $file_name .= substr($file["name"], 0, strpos($file["name"], '.'));
            $nb_converted ++;
            $converted = 1;
          }
          // Sinon création d'un cfile
          else {
            $other_file = new CFile();
            $other_file->bind($file);
            $other_file->file_name = $file["name"];
            $other_file->file_type = $file["type"];
            $other_file->doc_size = $file["size"];
            $other_file->fillFields();
            $other_file->private = CValue::post("private");

            if (false == $res = $other_file->moveTemp($file)) {
              CAppUI::setMsg("Fichier non envoyé", UI_MSG_ERROR);
              continue;
            }
            $other_file->author_id = CAppUI::$user->_id;

            if ($msg = $other_file->store()) {
              CAppUI::setMsg("Fichier non enregistré: $msg", UI_MSG_ERROR);
              continue;
            }

            CAppUI::setMsg("Fichier enregistré", UI_MSG_OK);
          }
          // Pour le nom du pdf de fusion, on concatène les noms des fichiers
          if ($key != count($aFiles)-1 && $converted) {
            $file_name .= "-";
          }
        }

        // Si des fichiers ont été convertis et ajoutés à PDFMerger,
        // création du cfile.
        if ($nb_converted) {
          $obj->file_name = $file_name.".pdf";
          $obj->file_type = "application/pdf";
          $obj->author_id = CAppUI::$user->_id;
          $obj->private = CValue::post("private");
          $obj->object_id = CValue::post("object_id");
          $obj->object_class = CValue::post("object_class");
          $obj->updateFormFields();
          $obj->fillFields();
          $obj->forceDir();
          $tmpname = tempnam("/tmp", "pdf_");
          $pdf->merge('file', $tmpname);
          $obj->doc_size = strlen(file_get_contents($tmpname));
          $obj->moveFile($tmpname);
          //rename($tmpname, $obj->_file_path . "/" .$obj->file_real_filename);

          if ($msg = $obj->store()) {
            CAppUI::setMsg("Fichier non enregistré: $msg", UI_MSG_ERROR);
          }
          else {
            CAppUI::setMsg("Fichier enregistré", UI_MSG_OK);
          }
        }
      }
      else {
        foreach ($aFiles as $file) {
          if ($file["error"] == UPLOAD_ERR_NO_FILE) {
            continue;
          }

          if ($file["error"] != 0) {
            CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-".$file["error"]), UI_MSG_ERROR);
            continue;
          }

          // Reinstanciate

          $this->_obj = new $this->_obj->_class;

          /** @var CFile $obj */
          $obj = $this->_obj;
          $obj->bind($file);
          $obj->file_name = empty($file["_rename"]) ? $file["name"] : $file["_rename"];
          $obj->file_type = $file["type"];

          if ($obj->file_type == "application/x-download") {
            $obj->file_type = CMbPath::guessMimeType($obj->file_name);
          }

          $obj->doc_size = $file["size"];
          $obj->fillFields();
          $obj->private   = CValue::post("private");
          if (false == $res = $obj->moveTemp($file)) {
            CAppUI::setMsg("Fichier non envoyé", UI_MSG_ERROR);
            continue;
          }

          // File owner on creation
          if (!$obj->file_id) {
            $obj->author_id = CAppUI::$user->_id;
          }

          if ($msg = $obj->store()) {
            CAppUI::setMsg("Fichier non enregistré: $msg", UI_MSG_ERROR);
            continue;
          }

          CAppUI::setMsg("Fichier enregistré", UI_MSG_OK);
        }
      }
      // Redaction du message et renvoi
      if (CAppUI::isMsgOK() && $this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }

      if (!CAppUI::isMsgOK() && $this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }
    else {
      parent::doStore();
    }
  }  
}
