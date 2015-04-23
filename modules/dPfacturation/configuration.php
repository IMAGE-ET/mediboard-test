<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

if (CAppUI::conf("ref_pays") == "2") {
  CConfiguration::register(
    array(
      "CGroups" => array(
        "dPfacturation" => array(
          "CEditPdf" => array(
            "use_bill_fct"  => "bool default|0",
            "use_bill_etab" => "bool default|0",
            "home_nom"      => "str",
            "home_adresse"  => "str",
            "home_cp"       => "str",
            "home_ville"    => "str",
            "home_EAN"      => "str",
            "home_RCC"      => "str",
            "home_tel"      => "str",
            "home_fax"      => "str",
            "etab_adresse1" => "str",
            "etab_adresse2" => "str",
            "use_date_consult_traitement" => "bool default|0",
            "see_diag_justificatif"       => "bool default|1",
            "canton"       => "enum list|AG|AI|AR|BE|BL|BS|FR|GE|GL|GR|JU|LU|NE|NW|OW|SG|SH|SO|SZ|TI|TG|UR|VD|VS|ZG|ZH|LI|A|D|F|I default|GE localize",
          ),
          "CEditBill" => array(
            "store_envoi_xml" => "bool default|0",
          ),
          "Other" => array(
            "see_reject_xml"        => "bool default|0",
            "delfile_read_reject"   => "bool default|1",
          ),
        )
      )
    )
  );
}
else {CConfiguration::register(
  array(
    "CGroups" => array(
      "dPfacturation" => array(
      )
    )
  )
);
}