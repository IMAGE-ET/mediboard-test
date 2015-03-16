<?php

/**
 * $Id$
 *
 * @category eai
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSyslogAuditMessage (IHE ATNA)
 */
class CSyslogAuditMessage extends CSyslogMessage {
  const FACILITY = '10';
  const SEVERITY = '5';
  const MSGID    = 'IHE+RFC-3881';

  function __construct($encoding = "iso-8859-1") {
    parent::__construct($encoding);

    $this->setPri(self::FACILITY, self::SEVERITY);
    $this->msgid = self::MSGID;
  }
}
