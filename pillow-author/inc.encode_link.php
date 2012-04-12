<?php

// NOTE: I "classified" these functions into the plugin namespace to prevent collisions with other plugins

/*
 * URL Encoder script.
 * Version 1.0 - February 2002
 * Version 1.1 - December 2007 - added encodelink_delayed - a delayed action on decoding
 * Version 1.2 - August 2008 - updated encodelink_delayed to allow it to be used multiple times in a page
 * (c) 2002-2008, Paul Gregg <pgregg@pgregg.com>
 * http://www.pgregg.com
 *
 * Function: Take an href link and the visible text and make an obfuscated
 * link to prevent search engines (or spam harvesters) from picking it up.
 *
 * Open Source Code:   If you use this code on your site for public
 * access (i.e. on the Internet) then you must attribute the author and
 * source web site: http://www.pgregg.com/projects/
 * You must also make this original source code available for download
 * unmodified or provide a link to the source.  Additionally you must provide
 * the source to any modified or translated versions or derivatives.
 *
 * PHP function-ised version.
 */

Class IS_Pillow_Author_URL_Encoder {
  Function transpose($str) {
    # function takes the string and swaps the order of each group of 2 chars
    $len = strlen($str);
    $ret = "";
    for ($i=0; $i<$len; $i=$i+2) {
      if ($i+1 == $len)
        $ret .= substr($str, $i, 1);
      else
        $ret .= sprintf("%s%s", substr($str, $i+1, 1), substr($str, $i, 1));
    }
    return $ret;
  }
  
  Function escapeencode ($str) {
    $ret = "";
    $arr = unpack("C*", $str);
    foreach ($arr as $char)
      $ret .= sprintf("%%%X", $char);
    return $ret;
  }

  public static Function encodehash($href, $text) {
    $prepend = "";
    if (preg_match("/^mailto:/", $href)) {
      $href = preg_replace("/^mailto:/", "", $href);
      $prepend = "mailto:";
    }
    if (preg_match("/^http:\/\//", $href)) {
      $href = preg_replace("/^http:\/\//", "", $href);
      list($server,$url) = split("/", $href, 2);
      $href = $url;
      $prepend = "http://$server/";
    }
    $UserCode = sprintf("<a href=\"%s%s\">%s</a>",
        $prepend,
        self::escapeencode($href), $text);
    return $UserCode;
  }
  
  public static Function encodelink($href, $text) {
    $code = sprintf("var s='%s';var r='';for(var i=0;i<s.length;i++,i++){r=r+s.substring(i+1,i+2)+s.substring(i,i+1)}document.write('<a href=\"'+r+'\">%s</a>');", self::transpose($href), $text);
    $UserCode = sprintf("%s%s%s",
    "<SCRIPT type=\"text/javascript\">eval(unescape('",
    self::escapeencode($code),
    "'))</SCRIPT>");
    return $UserCode;
  }

  public static Function encodelink_delayed($href, $text) {
    static $usecount = 0;
    $usecount++;
    $code = sprintf("function pgregg_transpose%d(h) {var s='%s';var r='';for(var i=0;i<s.length;i++,i++){r=r+s.substring(i+1,i+2)+s.substring(i,i+1)}h.href=r;}document.write('<a href=\"#\" onMouseOver=\"javascript:pgregg_transpose%d(this)\" onFocus=\"javascript:pgregg_transpose%d(this)\">%s</a>');", $usecount, self::transpose($href), $usecount, $usecount, $text);
    $UserCode = sprintf("%s%s%s",
        "<SCRIPT type=\"text/javascript\">eval(unescape('",
        self::escapeencode($code),
        "'))</SCRIPT>");
    return $UserCode;
  }
}