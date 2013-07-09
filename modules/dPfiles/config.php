<?php

/**
 * Onglet de configuration
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$dPconfig["dPfiles"] = array (
  "extensions_yoplet" => "gif jpeg jpg pdf png",
  "upload_max_filesize" => "2M",
  "yoplet_upload_url" => "",
  "yoplet_upload_path" => "",
  "CFile" => array(
    "upload_directory"  => "files",
    "ooo_active"        => "0",
    "ooo_path"          => "",
    "python_path"       => "",
    "merge_to_pdf"      => "0"
  ),

  "CFilesCategory" => array(
    "show_empty" => "1",
  ),
  
	
  "system_sender" => "",
  "CDocumentSender" => array(
    "auto_max_load" => "50",
    "auto_max_send" => "10",
  ),
);
