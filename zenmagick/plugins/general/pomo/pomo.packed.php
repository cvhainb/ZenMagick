<?php  class Translation_Entry { var $is_plural = false; var $context = null; var $singular = null; var $plural = null; var $translations = array(); var $translator_comments = ''; var $extracted_comments = ''; var $references = array(); var $flags = array(); function Translation_Entry($args=array()) { if (!isset($args['singular'])) { return; } $object_varnames = array_keys(get_object_vars($this)); foreach ($args as $varname => $value) { $this->$varname = $value; } if (isset($args['plural'])) $this->is_plural = true; if (!is_array($this->translations)) $this->translations = array(); if (!is_array($this->references)) $this->references = array(); if (!is_array($this->flags)) $this->flags = array(); } function key() { if (is_null($this->singular)) return false; return is_null($this->context)? $this->singular : $this->context.chr(4).$this->singular; } }   class MO extends Gettext_Translations { var $_nplurals = 2; function import_from_file($filename) { $reader = new POMO_CachedIntFileReader($filename); if (isset($reader->error)) { return false; } return $this->import_from_reader($reader); } function export_to_file($filename) { $fh = fopen($filename, 'wb'); if ( !$fh ) return false; $entries = array_filter($this->entries, create_function('$e', 'return !empty($e->translations);')); ksort($entries); $magic = 0x950412de; $revision = 0; $total = count($entries) + 1; $originals_lenghts_addr = 28; $translations_lenghts_addr = $originals_lenghts_addr + 8 * $total; $size_of_hash = 0; $hash_addr = $translations_lenghts_addr + 8 * $total; $current_addr = $hash_addr; fwrite($fh, pack('V*', $magic, $revision, $total, $originals_lenghts_addr, $translations_lenghts_addr, $size_of_hash, $hash_addr)); fseek($fh, $originals_lenghts_addr); fwrite($fh, pack('VV', 0, $current_addr)); $current_addr++; $originals_table = chr(0); foreach($entries as $entry) { $originals_table .= $this->export_original($entry) . chr(0); $length = strlen($this->export_original($entry)); fwrite($fh, pack('VV', $length, $current_addr)); $current_addr += $length + 1; } $exported_headers = $this->export_headers(); fwrite($fh, pack('VV', strlen($exported_headers), $current_addr)); $current_addr += strlen($exported_headers) + 1; $translations_table = $exported_headers . chr(0); foreach($entries as $entry) { $translations_table .= $this->export_translations($entry) . chr(0); $length = strlen($this->export_translations($entry)); fwrite($fh, pack('VV', $length, $current_addr)); $current_addr += $length + 1; } fwrite($fh, $originals_table); fwrite($fh, $translations_table); fclose($fh); } function export_original($entry) { $exported = $entry->singular; if ($entry->is_plural) $exported .= chr(0).$entry->plural; if (!is_null($entry->context)) $exported = $entry->context . chr(4) . $exported; return $exported; } function export_translations($entry) { return implode(chr(0), $entry->translations); } function export_headers() { $exported = ''; foreach($this->headers as $header => $value) { $exported.= "$header: $value\n"; } return $exported; } function get_byteorder($magic) { $magic_little = (int) - 1794895138; $magic_little_64 = (int) 2500072158; $magic_big = ((int) - 569244523) && 0xFFFFFFFF; if ($magic_little == $magic || $magic_little_64 == $magic) { return 'little'; } else if ($magic_big == $magic) { return 'big'; } else { return false; } } function import_from_reader($reader) { $reader->setEndian('little'); $endian = MO::get_byteorder($reader->readint32()); if (false === $endian) { return false; } $reader->setEndian($endian); $revision = $reader->readint32(); $total = $reader->readint32(); $originals_lenghts_addr = $reader->readint32(); $translations_lenghts_addr = $reader->readint32(); $reader->seekto($originals_lenghts_addr); $originals_lenghts = $reader->readint32array($total * 2); $reader->seekto($translations_lenghts_addr); $translations_lenghts = $reader->readint32array($total * 2); $length = create_function('$i', 'return $i * 2 + 1;'); $offset = create_function('$i', 'return $i * 2 + 2;'); for ($i = 0; $i < $total; ++$i) { $reader->seekto($originals_lenghts[$offset($i)]); $original = $reader->read($originals_lenghts[$length($i)]); $reader->seekto($translations_lenghts[$offset($i)]); $translation = $reader->read($translations_lenghts[$length($i)]); if ('' == $original) { $this->set_headers($this->make_headers($translation)); } else { $this->add_entry($this->make_entry($original, $translation)); } } return true; } function make_entry($original, $translation) { $args = array(); $parts = explode(chr(4), $original); if (isset($parts[1])) { $original = $parts[1]; $args['context'] = $parts[0]; } $parts = explode(chr(0), $original); $args['singular'] = $parts[0]; if (isset($parts[1])) { $args['plural'] = $parts[1]; } $args['translations'] = explode(chr(0), $translation); $entry = new Translation_Entry($args); return $entry; } function select_plural_form($count) { return $this->gettext_select_plural_form($count); } function get_plural_forms_count() { return $this->_nplurals; } }   define('PO_MAX_LINE_LEN', 79); ini_set('auto_detect_line_endings', 1); class PO extends Gettext_Translations { function export_headers() { $header_string = ''; foreach($this->headers as $header => $value) { $header_string.= "$header: $value\n"; } $poified = PO::poify($header_string); return rtrim("msgid \"\"\nmsgstr $poified"); } function export_entries() { return implode("\n\n", array_map(array('PO', 'export_entry'), $this->entries)); } function export($include_headers = true) { $res = ''; if ($include_headers) { $res .= $this->export_headers(); $res .= "\n\n"; } $res .= $this->export_entries(); return $res; } function export_to_file($filename, $include_headers = true) { $fh = fopen($filename, 'w'); if (false === $fh) return false; $export = $this->export($include_headers); $res = fwrite($fh, $export); if (false === $res) return false; return fclose($fh); } function poify($string) { $quote = '"'; $slash = '\\'; $newline = "\n"; $replaces = array( "$slash" => "$slash$slash", "$quote" => "$slash$quote", "\t" => '\t', ); $string = str_replace(array_keys($replaces), array_values($replaces), $string); $po = $quote.implode("${slash}n$quote$newline$quote", explode($newline, $string)).$quote; if (false !== strpos($string, $newline) && (substr_count($string, $newline) > 1 || !($newline === substr($string, -strlen($newline))))) { $po = "$quote$quote$newline$po"; } $po = str_replace("$newline$quote$quote", '', $po); return $po; } function unpoify($string) { $escapes = array('t' => "\t", 'n' => "\n", '\\' => '\\'); $lines = array_map('trim', explode("\n", $string)); $lines = array_map(array('PO', 'trim_quotes'), $lines); $unpoified = ''; $previous_is_backslash = false; foreach($lines as $line) { preg_match_all('/./u', $line, $chars); $chars = $chars[0]; foreach($chars as $char) { if (!$previous_is_backslash) { if ('\\' == $char) $previous_is_backslash = true; else $unpoified .= $char; } else { $previous_is_backslash = false; $unpoified .= isset($escapes[$char])? $escapes[$char] : $char; } } } return $unpoified; } function prepend_each_line($string, $with) { $php_with = var_export($with, true); $lines = explode("\n", $string); if ("\n" == substr($string, -1)) unset($lines[count($lines) - 1]); $res = implode("\n", array_map(create_function('$x', "return $php_with.\$x;"), $lines)); if ("\n" == substr($string, -1)) $res .= "\n"; return $res; } function comment_block($text, $char=' ') { $text = wordwrap($text, PO_MAX_LINE_LEN - 3); return PO::prepend_each_line($text, "#$char "); } function export_entry($entry) { if (is_null($entry->singular)) return false; $po = array(); if (!empty($entry->translator_comments)) $po[] = PO::comment_block($entry->translator_comments); if (!empty($entry->extracted_comments)) $po[] = PO::comment_block($entry->extracted_comments, '.'); if (!empty($entry->references)) $po[] = PO::comment_block(implode(' ', $entry->references), ':'); if (!empty($entry->flags)) $po[] = PO::comment_block(implode(", ", $entry->flags), ','); if (!is_null($entry->context)) $po[] = 'msgctxt '.PO::poify($entry->context); $po[] = 'msgid '.PO::poify($entry->singular); if (!$entry->is_plural) { $translation = empty($entry->translations)? '' : $entry->translations[0]; $po[] = 'msgstr '.PO::poify($translation); } else { $po[] = 'msgid_plural '.PO::poify($entry->plural); $translations = empty($entry->translations)? array('', '') : $entry->translations; foreach($translations as $i => $translation) { $po[] = "msgstr[$i] ".PO::poify($translation); } } return implode("\n", $po); } function import_from_file($filename) { $f = fopen($filename, 'r'); if (!$f) return false; $lineno = 0; while (true) { $res = $this->read_entry($f, $lineno); if (!$res) break; if ($res['entry']->singular == '') { $this->set_headers($this->make_headers($res['entry']->translations[0])); } else { $this->add_entry($res['entry']); } } PO::read_line($f, 'clear'); return $res !== false; } function read_entry($f, $lineno = 0) { $entry = new Translation_Entry(); $context = ''; $msgstr_index = 0; $is_final = create_function('$context', 'return $context == "msgstr" || $context == "msgstr_plural";'); while (true) { $lineno++; $line = PO::read_line($f); if (!$line) { if (feof($f)) { if ($is_final($context)) break; elseif (!$context) return null; else return false; } else { return false; } } if ($line == "\n") continue; $line = trim($line); if (preg_match('/^#/', $line, $m)) { if ($is_final($context)) { PO::read_line($f, 'put-back'); $lineno--; break; } if ($context && $context != 'comment') { return false; } $this->add_comment_to_entry($entry, $line);; } elseif (preg_match('/^msgctxt\s+(".*")/', $line, $m)) { if ($is_final($context)) { PO::read_line($f, 'put-back'); $lineno--; break; } if ($context && $context != 'comment') { return false; } $context = 'msgctxt'; $entry->context .= PO::unpoify($m[1]); } elseif (preg_match('/^msgid\s+(".*")/', $line, $m)) { if ($is_final($context)) { PO::read_line($f, 'put-back'); $lineno--; break; } if ($context && $context != 'msgctxt' && $context != 'comment') { return false; } $context = 'msgid'; $entry->singular .= PO::unpoify($m[1]); } elseif (preg_match('/^msgid_plural\s+(".*")/', $line, $m)) { if ($context != 'msgid') { return false; } $context = 'msgid_plural'; $entry->is_plural = true; $entry->plural .= PO::unpoify($m[1]); } elseif (preg_match('/^msgstr\s+(".*")/', $line, $m)) { if ($context != 'msgid') { return false; } $context = 'msgstr'; $entry->translations = array(PO::unpoify($m[1])); } elseif (preg_match('/^msgstr\[(\d+)\]\s+(".*")/', $line, $m)) { if ($context != 'msgid_plural' && $context != 'msgstr_plural') { return false; } $context = 'msgstr_plural'; $msgstr_index = $m[1]; $entry->translations[$m[1]] = PO::unpoify($m[2]); } elseif (preg_match('/^".*"$/', $line)) { $unpoified = PO::unpoify($line); switch ($context) { case 'msgid': $entry->singular .= $unpoified; break; case 'msgctxt': $entry->context .= $unpoified; break; case 'msgid_plural': $entry->plural .= $unpoified; break; case 'msgstr': $entry->translations[0] .= $unpoified; break; case 'msgstr_plural': $entry->translations[$msgstr_index] .= $unpoified; break; default: return false; } } else { return false; } } if (array() == array_filter($entry->translations)) $entry->translations = array(); return array('entry' => $entry, 'lineno' => $lineno); } function read_line($f, $action = 'read') { static $last_line = ''; static $use_last_line = false; if ('clear' == $action) { $last_line = ''; return true; } if ('put-back' == $action) { $use_last_line = true; return true; } $line = $use_last_line? $last_line : fgets($f); $last_line = $line; $use_last_line = false; return $line; } function add_comment_to_entry($entry, $po_comment_line) { $first_two = substr($po_comment_line, 0, 2); $comment = trim(substr($po_comment_line, 2)); if ('#:' == $first_two) { $entry->references = array_merge($entry->references, preg_split('/\s+/', $comment)); } elseif ('#.' == $first_two) { $entry->extracted_comments = trim($entry->extracted_comments . "\n" . $comment); } elseif ('#,' == $first_two) { $entry->flags = array_merge($entry->flags, preg_split('/,\s*/', $comment)); } else { $entry->translator_comments = trim($entry->translator_comments . "\n" . $comment); } } function trim_quotes($s) { if ( substr($s, 0, 1) == '"') $s = substr($s, 1); if ( substr($s, -1, 1) == '"') $s = substr($s, 0, -1); return $s; } }   class POMO_StringReader { var $_pos; var $_str; function POMO_StringReader($str = '') { $this->_str = $str; $this->_pos = 0; $this->is_overloaded = ((ini_get("mbstring.func_overload") & 2) != 0) && function_exists('mb_substr'); } function _substr($string, $start, $length) { if ($this->is_overloaded) { return mb_substr($string,$start,$length,'ascii'); } else { return substr($string,$start,$length); } } function _strlen($string) { if ($this->is_overloaded) { return mb_strlen($string,'ascii'); } else { return strlen($string); } } function read($bytes) { $data = $this->_substr($this->_str, $this->_pos, $bytes); $this->_pos += $bytes; if ($this->_strlen($this->_str) < $this->_pos) $this->_pos = $this->_strlen($this->_str); return $data; } function seekto($pos) { $this->_pos = $pos; if ($this->_strlen($this->_str) < $this->_pos) $this->_pos = $this->_strlen($this->_str); return $this->_pos; } function pos() { return $this->_pos; } function length() { return $this->_strlen($this->_str); } } class POMO_CachedFileReader extends POMO_StringReader { function POMO_CachedFileReader($filename) { parent::POMO_StringReader(); $this->_str = file_get_contents($filename); if (false === $this->_str) return false; $this->_pos = 0; } } class POMO_CachedIntFileReader extends POMO_CachedFileReader { var $endian = 'little'; function POMO_CachedIntFileReader($filename, $endian = 'little') { $this->endian = $endian; parent::POMO_CachedFileReader($filename); } function setEndian($endian) { $this->endian = $endian; } function readint32() { $bytes = $this->read(4); if (4 != $this->_strlen($bytes)) return false; $endian_letter = ('big' == $this->endian)? 'N' : 'V'; $int = unpack($endian_letter, $bytes); return array_shift($int); } function readint32array($count) { $bytes = $this->read(4 * $count); if (4*$count != $this->_strlen($bytes)) return false; $endian_letter = ('big' == $this->endian)? 'N' : 'V'; return unpack($endian_letter.$count, $bytes); } }   class Translations { var $entries = array(); var $headers = array(); function add_entry($entry) { if (is_array($entry)) { $entry = new Translation_Entry($entry); } $key = $entry->key(); if (false === $key) return false; $this->entries[$key] = $entry; return true; } function set_header($header, $value) { $this->headers[$header] = $value; } function set_headers($headers) { foreach($headers as $header => $value) { $this->set_header($header, $value); } } function get_header($header) { return isset($this->headers[$header])? $this->headers[$header] : false; } function translate_entry($entry) { $key = $entry->key(); return isset($this->entries[$key])? $this->entries[$key] : false; } function translate($singular, $context=null) { $entry = new Translation_Entry(array('singular' => $singular, 'context' => $context)); $translated = $this->translate_entry($entry); return ($translated && !empty($translated->translations))? $translated->translations[0] : $singular; } function select_plural_form($count) { return 1 == $count? 0 : 1; } function get_plural_forms_count() { return 2; } function translate_plural($singular, $plural, $count, $context = null) { $entry = new Translation_Entry(array('singular' => $singular, 'plural' => $plural, 'context' => $context)); $translated = $this->translate_entry($entry); $index = $this->select_plural_form($count); $total_plural_forms = $this->get_plural_forms_count(); if ($translated && 0 <= $index && $index < $total_plural_forms && is_array($translated->translations) && isset($translated->translations[$index])) return $translated->translations[$index]; else return 1 == $count? $singular : $plural; } function merge_with($other) { $this->entries = array_merge($this->entries, $other->entries); } } class Gettext_Translations extends Translations { function gettext_select_plural_form($count) { if (!isset($this->_gettext_select_plural_form) || is_null($this->_gettext_select_plural_form)) { $plural_header = $this->get_header('Plural-Forms'); $this->_gettext_select_plural_form = $this->_make_gettext_select_plural_form($plural_header); } return call_user_func($this->_gettext_select_plural_form, $count); } function _make_gettext_select_plural_form($plural_header) { $res = create_function('$count', 'return 1 == $count? 0 : 1;'); if ($plural_header && (preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $plural_header, $matches))) { $nplurals = (int)$matches[1]; $this->_nplurals = $nplurals; $plural_expr = trim($this->_parenthesize_plural_exression($matches[2])); $plural_expr = str_replace('n', '$n', $plural_expr); $func_body = "
				\$index = (int)($plural_expr);
				return (\$index < $nplurals)? \$index : $nplurals - 1;"; $res = create_function('$n', $func_body); } return $res; } function _parenthesize_plural_exression($expression) { $expression .= ';'; $res = ''; $depth = 0; for ($i = 0; $i < strlen($expression); ++$i) { $char = $expression[$i]; switch ($char) { case '?': $res .= ' ? ('; $depth++; break; case ':': $res .= ') : ('; break; case ';': $res .= str_repeat(')', $depth) . ';'; $depth= 0; break; default: $res .= $char; } } return rtrim($res, ';'); } function make_headers($translation) { $headers = array(); $translation = str_replace('\n', "\n", $translation); $lines = explode("\n", $translation); foreach($lines as $line) { $parts = explode(':', $line, 2); if (!isset($parts[1])) continue; $headers[trim($parts[0])] = trim($parts[1]); } return $headers; } function set_header($header, $value) { parent::set_header($header, $value); if ('Plural-Forms' == $header) $this->_gettext_select_plural_form = $this->_make_gettext_select_plural_form($value); } } ?>