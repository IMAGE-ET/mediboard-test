<?php

/**
 * Configuration de dompdf
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


ini_set("memory_limit", "256M");

// Le type de @media accepté par DOMPDF  
define("DOMPDF_DEFAULT_MEDIA_TYPE", "print");

// Urls pour les images acceptées
define("DOMPDF_ENABLE_REMOTE", true);

// Backend de DOMPDF
define("DOMPDF_PDF_BACKEND", "CPDF");

// Police par défaut
define("DOMPDF_DEFAULT_FONT", "sans-serif");

// PHP inline desactivé
define("DOMPDF_ENABLE_PHP", false);

// Hauteur de ligne
define("DOMPDF_FONT_HEIGHT_RATIO", 1.0);
