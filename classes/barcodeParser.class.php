<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CBarcodeParser {
  public static $code128separator = "@";
  public static $code128prefixes = array(
    "00"  => "Serial Shipping Container Code",
    "01"  => "Shipping Container Code",
    "10"  => "Batch or Lot Number",
    "11"  => "Production Date (YYMMDD)",
    "13"  => "Packaging Date (YYMMDD)",
    "15"  => "Best Before/Sell By Date (YYMMDD)",
    "17"  => "Sell By/Expiration Date (YYMMDD)",
    "20"  => "Product Variant",
    "21"  => "Serial Number",
    "22"  => "HIBCC; quantity, date, batch, and link",
    "23"  => "Lot number",
    "240" => "Secondary product attributes",
    "250" => "Secondary Serial number",
    "30"  => "Quantity each",
    "310" => "Net Weight, kilograms",
    "311" => "Length or first dimension, meters",
    "312" => "Width, diameter, or 2nd dimension, meters",
    "313" => "Depth, thickness, height, or 3rd dimension, meters",
    "314" => "Area, square meters",
    "315" => "Volume, liters",
    "316" => "Volume, cubic meters",
    "320" => "Net weight, pounds",
    "330" => "Gross weight, kilograms",
    "331" => "Length or first dimension, meters logistics",
    "332" => "Width, diameter, or 2nd dimension, meters logistics",
    "333" => "Depth, thickness, height, or 3rd dimension, meters logistics",
    "334" => "Area, square meters logistics",
    "335" => "Gross volume, liters logistics",
    "336" => "Gross volume, cubic meters logistics",
    "340" => "Gross weight, pounds",
    "400" => "Customer purchase order number",
    "410" => "Ship to location code (EAN-13 or DUNS)",
    "411" => "Bill to location code (EAN-13 or DUNS)",
    "412" => "Purchase from location code (EAN-13 or DUNS)",
    "420" => "Ship to postal code",
    "421" => "Ship to postal code with 3-digit ISO country code",
    "8001"=> "Roll products => width, length, core diameter, direction, splices",
    "8002"=> "Electronic serial number for cellular telephones",
    "90"  => "FACT identifiers (internal applications)",
    "91"  => "Internal use (raw materials, packaging, components)",
    "92"  => "Internal use (raw materials, packaging, components)",
    "93"  => "Internal use (product manufacturers)",
    "94"  => "Internal use (product manufacturers)",
    "95"  => "SCAC+Carrier PRO number",
    "96"  => "SCAC+Carrier assigned container ID",
    "97"  => "Internal use (wholesalers)",
    "98"  => "Internal use (retailers)",
    "99"  => "Mutually defined text",
  );
  
  public static $code128table = array(
    "01" => "scc",
    "10" => "lot",
    "17" => "per",
    "21" => "sn",
  );
  
  private static $code39ext = array(
    '%U' => 0,
    
    '$A' => 1,
    '$B' => 2,
    '$C' => 3,
    '$D' => 4,
    '$E' => 5,
    '$F' => 6,
    '$G' => 7,
    '$H' => 8,
    '$I' => 6,
    '$J' => 10,
    '$K' => 11,
    '$L' => 12,
    '$M' => 13,
    '$N' => 14,
    '$O' => 15,
    '$P' => 16,
    '$Q' => 17,
    '$R' => 18,
    '$S' => 19,
    '$T' => 20,
    '$U' => 21,
    '$V' => 22,
    '$W' => 23,
    '$X' => 24,
    '$Y' => 25,
    '$Z' => 26,
    
    '%A' => 27,
    '%B' => 28,
    '%C' => 29,
    '%D' => 30,
    '%E' => 31,
    ' '  => 32,
    
    '/A' => 33,
    '/B' => 34,
    '/C' => 35,
    '/D' => 36,
    '/E' => 37,
    '/F' => 38,
    '/G' => 39,
    '/H' => 40,
    '/I' => 41,
    '/J' => 42,
    '/K' => 43,
    '/L' => 44,
    
    '-'  => 45,
    '.'  => 46,
    '/O' => 47,
    
    /* 0 to 9 */
   
    '/Z' => 58,
    
    '%F' => 59,
    '%G' => 60,
    '%H' => 61,
    '%I' => 62,
    '%J' => 63,
    
    '%V' => 64,
    
    /* A to Z */
    
    '%K' => 91,
    '%L' => 92,
    '%M' => 93,
    '%N' => 94,
    '%O' => 95,
    
    '%W' => 96,
    
    '+A' => 97,
    '+B' => 98,
    '+C' => 99,
    '+D' => 100,
    '+E' => 101,
    '+F' => 102,
    '+G' => 103,
    '+H' => 104,
    '+I' => 105,
    '+J' => 106,
    '+K' => 107,
    '+L' => 108,
    '+M' => 109,
    '+N' => 110,
    '+O' => 111,
    '+P' => 112,
    '+Q' => 113,
    '+R' => 114,
    '+S' => 115,
    '+T' => 116,
    '+U' => 117,
    '+V' => 118,
    '+W' => 119,
    '+X' => 120,
    '+Y' => 121,
    '+Z' => 122,
    
    '%P' => 123,
    '%Q' => 124,
    '%R' => 125,
    '%S' => 126,
    '%T' => 127,
    '%X' => 127,
    '%Y' => 127,
    '%Z' => 127,
  );
  
  static function decodeCode39($barcode) {
    $chars = array_map("chr", self::$code39ext);
    return strtr($barcode, $chars);
  }
  
  static function checksum($string) {
    $checksum = 0;
    $length   = strlen($string);
    $charset  = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-. $/+%';
   
    for($i = 0; $i < $length; ++$i) {
      $checksum += strpos($charset, $string[$i]);
    }
   
    return substr($charset, ($checksum % 43), 1);
  }
  
  static function checkCode39($barcode) {
    return self::checksum(substr($barcode, 0, -1)) == substr($barcode, -1);
  }
  
  static function parsePeremptionDate($date) {
    // dates du type 18304 >> 18 octobre (304 = jour dans l'année)
    
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
      return $date;
    }
    
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
      return mbDateFromLocale($date);
    }
    
    if (preg_match('/^(20\d{2})(\d{2})$/', $date, $parts)){
      $date = mbDate("+1 MONTH", $parts[1]."-".$parts[2]."-01");
      return mbDate("-1 DAY", $date);
    }
    
    if (preg_match('/^(\d{2})(\d{2})(\d{2})$/', $date, $parts)){
      $date = mbDate("+1 MONTH", "20".$parts[1]."-".$parts[2]."-01");
      return mbDate("-1 DAY", $date);
    }
    
    if (preg_match('/^(\d{2})(\d{2})$/', $date, $parts)){
      $date = mbDate("+1 MONTH", "20".$parts[2]."-".$parts[1]."-01");
      return mbDate("-1 DAY", $date);
    }
    
    return null;
  }
  
  static function parse($barcode) {
    $orig_barcode = $barcode;
    $comp = array();
    
    $type = "raw";

    if (!$barcode) {
      return array(
        "type" => $type,
        "comp" => $comp,
      );
    }
    
    // code 128 with sepataror char
    if (preg_match('/^[0-9a-z]+'.self::$code128separator.'[0-9a-z]+[0-9a-z\\'.self::$code128separator.']*$/ims', $barcode)) {
      $type = "code128";
      $parts = explode(self::$code128separator, $barcode);
      
      foreach($parts as $p) {
        foreach(self::$code128prefixes as $code => $text) {
          //if (strpos($p, $code) === 0) { // strpos won't work :(
          if (substr($p, 0, strlen($code)) == $code) {
            $comp[self::$code128table[$code]] = substr($p, strlen($code), strlen($p)-strlen($code));
            break;
          }
        }
      }
    }

    // code 128
    if (empty($comp) &&
        preg_match('/^(?:(01)(\d{14}))?(10)([a-z0-9]{7,20})(17)(\d{6})$/ims', $barcode, $parts) ||
        preg_match('/^(?:(01)(\d{14}))?(17)(\d{6})(10)([a-z0-9]{7,20})$/ims', $barcode, $parts) ||
        preg_match('/^(?:(01)(\d{14}))?(17)(\d{6})(21)([a-z0-9]{7,20})$/ims', $barcode, $parts) ||
        preg_match('/^(01)(\d{14})$/i', $barcode, $parts)) {
      $type = "code128";
      $prop = null;
      foreach($parts as $p){
        if (in_array($p, array("01", "10", "17", "21"))) {
          $prop = $p;
        }
        else if ($prop) {
          $comp[self::$code128table[$prop]] = $p;
        }
        else $prop = null;
      }
    }
    
    // EAN code (13 digits)
    if (empty($comp) && preg_match('/^(\d{13})$/ims', $barcode, $parts)) {
      $type = "ean13";
      $comp["scc"] = "0{$parts[1]}";
    }
    
    // 2016-08
    if (empty($comp) && preg_match('/^(20\d{2})-(\d{2})$/ms', $barcode, $parts)) {
      $type = "date";
      $date = mbDate("+1 MONTH", $parts[1]."-".$parts[2]."-01");
      $comp["per"] = mbDate("-1 DAY", $date);
    }
    
    // 130828
    /*if (empty($comp) && preg_match('/^(\d{2})(\d{2})(\d{2})$/ms', $barcode, $parts)){
      $type = "date";
      $comp = mbDate("+1 MONTH", "20".$parts[1]."-".$parts[2]."-01");
      $comp = mbDate("-1 DAY", $comp);
    }*/
    
    if (empty($comp) && $barcode[0] === "+") {
      $type = "code39";
      $barcode = self::decodeCode39($barcode);
      
      //      __REF__
      // +H920246020502   
      if (preg_match('/^h(\d{3})(.+).{2}$/ms', $barcode, $parts)) {
        $comp["ref"] = $parts[2];
      }
      
      //     _PER_ __LOT__
      // +$$31303313414899 .
      if (preg_match('/^\+?\$\$.(\d{6})(.+).{2}$/ms', $barcode, $parts)) {
        $comp["per"] = $parts[1];
        $comp["lot"] = $parts[2];
      }
      
      //       __REF___    PER_ __LOT__
      // $$m423104003921/$$081309091602Y
      if (preg_match('/^[a-z](\d{3})(\d+).\/\$\$(\d{4})(.+).$/ms', $barcode, $parts)) {
        $comp["ref"] = $parts[2];
        $comp["per"] = $parts[3];
        $comp["lot"] = $parts[4];
      }
      
      //      ___REF___  PER_ __LOT___
      // +H7036307002101/1830461324862J09C
      if (preg_match('/^[a-z](\d{3})(\d+)(\d)\/(\d{5})(\d+)(.{4})$/ms', $barcode, $parts)) {
        $comp["ref"] = $parts[2];
        $comp["lot"] = $parts[5];
      }
      
      //   __SN___
      // +$11393812M  // $ or \v
      if (empty($comp) && preg_match('/^\+.(.+).{2}$/ms', $barcode, $parts)) {
        $comp["lot"] = $parts[1];
      }
    }
    
    // __LOT___ _SN__
    // 09091602/00736
    if (preg_match('/^(\d{8})\/(\d{5})$/', $barcode, $parts)) {
      $type = "unkown";
      $comp["lot"] = $parts[1];
    }
     
    // CIP
    if (preg_match('/^(\d{7})$/', $barcode, $parts)) {
      $type = "cip";
      $comp["cip"] = $parts[1];
    }
     
    // Medicament
    if (preg_match('/^([2459])(\d{7})(\d{6})(0[01])$/', $barcode, $parts)) {
      $type = "med";
      $comp["remb"]  = $parts[1];
      $comp["cip"]   = $parts[2];
      $comp["price"] = $parts[3];
      $comp["key"]   = $parts[4];
    }
    
    // Arthrex specific
    // REF : PAR-1934BF-2   >>  AR-1934BF-2  (without leading P)
    if (preg_match('/^P(AR-[A-Z0-9-]+)$/', $barcode, $parts)) {
      $type = "arthrex";
      $comp["ref"] = $parts[1];
    }
    // LOT : T314998   >>  314998  (without leading T)
    if (preg_match('/^T(\d{4,9})$/', $barcode, $parts)) {
      $type = "arthrex";
      $comp["lot"] = $parts[1];
    }
    // QTY : Q1   >>  1  (without leading T)
    if (preg_match('/^Q(\d{1})$/', $barcode, $parts)) {
      $type = "arthrex";
      $comp["qty"] = $parts[1];
    }
    
    // Physiol
    // __REF___ __SN__ __STE_ _ __PER_ _
    // 28081230 053653 100609 1 130630 1
    if (empty($comp) && preg_match('/^(\d{4}[012]\d{3})(\d{6})([0123]\d[01]\d\d\d)\d([0123]\d[01]\d\d\d)\d$/', $barcode, $parts)) {
      $type = "physiol";
      $comp["ref"] = $parts[1];
      $comp["sn"]  = $parts[2];
      $comp["per"] = $parts[4];
    }
    
    if (isset($comp["per"])) {
      $comp["per"] = self::parsePeremptionDate($comp["per"]);
    }
    
    if (isset($comp["scc"])) {
      preg_match('/\d{3}(\d{5})(\d{5})\d/', $comp["scc"], $parts);
      $comp["scc_manuf"] = $parts[1];
      $comp["scc_part"]  = $parts[2];
      $comp["scc_prod"]  = $parts[1].$parts[2];
    }
    
    if (isset($comp["sn"]) && empty($comp["lot"])) {
      $comp["lot"] = $comp["sn"];
    }
    
    $comp["raw"] = $orig_barcode;
    
    $comp += array(
      "raw"   => null,
      "ref"   => null,
      "lot"   => null,
      "per"   => null,
      "sn"    => null,
      "scc"   => null,
      "scc_manuf" => null,
      "scc_prod"  => null,
      "scc_part"  => null,
      "remb"  => null,
      "cip"   => null,
      "price" => null,
      "key"   => null,
    );
    
    return array(
      "type" => $type,
      "comp" => $comp,
    );
  }
}
