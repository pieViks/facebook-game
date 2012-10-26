<?php


function printr($var, $return = false, $minimal = false, $bgcolor = "#000", $txtcolor = "#fff")
{
	if(!config('application', 'printr', false)) {
		return;
	}

	$debug_backtrace  	= debug_backtrace();
	$backtrace     		= $debug_backtrace[0];
	$filename      		= $backtrace['file'].':'.$backtrace['line'];
	if(file_exists($backtrace['file'])) {
		$filelinecontent	= get_filelinecontent($backtrace['file'], $backtrace['line']);
	} else {
		$filelinecontent = '';
		$minimal = true;
	}
	$id					= md5(rand(0, 10000));
	$out				= "";

	$out.= '<div style="font-family: \'Lucida Grande\', Arial; font-size: 11px; border: 2px solid #228; padding: 0; margin: 5px; background: #fff; color: #444; border-radius: 5px;" class="printr">';
	if($minimal == false)
	{
		$out.= '<div style="font-size: 12px; background: #228; padding: 4px; color: #fff;">';
		$out.= '<a style="display: block; text-decoration: none; color: #fff; margin: 0 10px 0 0;" href="javascript:toggleHideShow(\''.$id.'\');">[&plusmn;]';
		$out.= $filelinecontent;
		$out.= '</a></div>';
	}
	$out.= '<div id="'.$id.'" style="display:block;">';
	$out.= '<div style="margin: 0; padding: 5px; background: '.$bgcolor.'; color: '.$txtcolor.';"><pre style="font-family: Monaco, Arial; font-size: 10px;">';


	if(is_bool($var)) {
		$var = ($var) ? "TRUE" : "FALSE";
		$out.= 'BOOLEAN: '.$var."<br />";
	} else {
		$out.= print_r($var, true);
	}
	$out.= '</pre></div>'."\n";

	if($minimal == false)
	{
		$out.= '<a style="display: block; padding: 3px; background: #333; color: #eee; text-decoration: none;" href="javascript:toggleHideShow(\''.$id.'_details\')">[&plusmn;] details</a>';
		$out.= '<div style="padding: 3px; display: none;" id="'.$id.'_details">';
		$ms = explode('.', microtime(true));
		$ms = (isset($ms[1])) ? $ms[1] : '0';
		$out.= '<em>time: '.date('H:i:s.').$ms.'</em><br />';
		$out.= '<em>file: '.$filename.'</em><br />';
		$out.= '<br />';
		$out.= ($backtrace['line']-2).':'.get_filelinecontent($backtrace['file'], $backtrace['line']-2).'<br/>';
		$out.= ($backtrace['line']-1).':'.get_filelinecontent($backtrace['file'], $backtrace['line']-1).'<br/>';
		$out.= ($backtrace['line']).':'.'<strong>'.$filelinecontent.'</strong><br />';
		$out.= ($backtrace['line']+1).':'.get_filelinecontent($backtrace['file'], $backtrace['line']+1).'<br/>';
		$out.= '<br />';

		foreach($debug_backtrace as $k=>$v)
		{
			if(isset($v['file']) && isset($v['line'])) {
				$file = $v['file'].':'.$v['line'];
			} else {
				$file = '';
			}

	        if($v['function'] == "include" || $v['function'] == "include_once" || $v['function'] == "require_once" || $v['function'] == "require") {
	            $out.= '#'.$k.' '.$v['function'].'('.$v['args'][0].')<br /> <span style="font-size:8px;">called at ['.$file.']</span><br />';
	        } else {
	            $out.= '#'.$k.' '.$v['function'].'() <br /><span style="font-size:8px;">called at ['.$file.']</span><br />';
	        }
    	}
		$out.= '<br />';
		$out.= '</div>';
		$out.= '<script type="text/javascript">';
		$out.= 'function toggleHideShow(id) {  var ele = document.getElementById(id); if(ele.style.display == "block") { ele.style.display = "none"; } else { ele.style.display = "block"; } }';
		$out.= '</script>';
	}
	$out.= '</div>';
	$out.= '</div>';

	if($return === false) {
		print $out;
	} else {
		return $out;
	}
}

function get_filelinecontent($file, $line)
{
	$filecontent   	= file_get_contents($file);
	$startline		= strlinepos($filecontent, "<?php");
	$startline		= ($startline !== false) ? $startline+1 : 0;
	$filecontent   	= explode("\n", $filecontent);

	return $filecontent[$line - $startline];
}

function md5s($var, $salt = "", $length = false)
{
	$md5s = md5($salt.$var);

	if($length != false)
	{
		$md5s = substr($md5s, 0, $length);
	}

	return $md5s;
}

function strlinepos($haysteck, $needle, $caseinsensitive = true)
{
	$haysteck_lines = explode("\n", $haysteck);
	$i=0; foreach($haysteck_lines as $line)
	{
		if($caseinsensitive) {
			$res = stripos($line, $needle);
		} else {
			$res = strpos($line, $needle);
		}
		if($res !== false)
		{
			return $i;
		}
		$i++;
	}

	return false;
}

function printo($input, $return = false, $open = 10)
{
	if(config('application', 'printr', false) == false) {
		return;
	}

	$src 	= debug_backtrace();
	$src 	= (object)$src[0];
	$file 	= file($src->file);
	$line 	= $file[$src->line - 1];

	preg_match('/printo\((.+?)(?:,|\)(;|\?>))/', $line, $m);

	$res 	= printo::render(printo::struct($input), $m[1], $open);

	if($return) {
		return $res;
	}

	print $res;
}

class printo
{
	public static $initial	= true;
	public static $keyWidth	= 0;
	public static $css;
	public static $js;
	public static $hooks = array();
	public static $classy = null;

	public static function struct($inp, &$dict = array())
	{
		if (is_object($inp)) {
			$hash = spl_object_hash($inp);

			if (array_key_exists($hash, $dict)) {
				$o = self::tyobj();
				$o->disp	= '{r}';
				$o->type	= 'ref';
				$o->ref		= $dict[$hash];
			}
			else {
				$o = self::type($inp);
				$o->hash = $hash;
				$dict[$hash] = $o;
			}
		}

		if (!isset($o))
			$o = self::type($inp);

		if (empty($o->children))
			return $o;

		foreach ($o->children as $k => $v)
			$o->children[$k] = self::struct($v, $dict);

		return $o;
	}

	public static function render($struct, $key = 'root', $exp_lvls = 1000, $st = true, $ln = 1)
	{
		// track max key width (8px/char)
		self::$keyWidth = max(self::$keyWidth, strlen($key) * 8);

		$inject = '';
		if (self::$initial) {
			$inject = self::$css . self::$js;
			self::$initial = false;
		}

		$buf = '';
		$buf .= $st ? "{$inject}<pre class=\"printo\"><ul>" : '';
		$s = &$struct;
		$disp = htmlspecialchars($s->disp);

		// add jumps to referenced objects
		if (!empty($s->hash))
			$disp = "<a name=\"{$s->hash}\">{$disp}</a>";
		else if ($s->type == 'ref')
			$disp = "<a href=\"#{$s->ref->hash}\">{$disp}</a>";

		$len = !is_null($s->length) ? "<div class=\"len\">{$s->length}</div>" : '';
		$sub = !is_null($s->subtype) ? "<div class=\"sub\">{$s->subtype}</div>" : '';
		$excol = !empty($s->children) ? '<div class="excol"></div>' : '';
		$exp_state = $excol ? ($exp_lvls > 0 ? ' expanded' : ' collapsed') : '';
		$empty		= $s->empty		? ' empty'			: '';
		$numeric	= $s->numeric	? ' numeric'		: '';
		$subtype	= $s->subtype	? " $s->subtype"	: '';
		$classes	= $s->classes	? ' ' . implode(' ', $s->classes) : '';
		$buf .= "<li class=\"{$s->type}{$subtype}{$numeric}{$empty}{$classes}{$exp_state}\">{$excol}<div class=\"lbl\"><div class=\"key\">{$key}</div><div class=\"val\">{$disp}</div><div class=\"typ\">({$s->type})</div>{$sub}{$len}</div>";
		if ($s->children) {
			$buf .= '<ul>';
			foreach ($s->children as $k => $s2)
				$buf .= self::render($s2, $k, $exp_lvls - 1, false, $ln++);
			$buf .= '</ul>';
		}
		$buf .= '</li>';
		$buf .= $st ? '</ul><style>.printo .key {min-width: ' . self::$keyWidth . 'px;}</style></pre>' : '';

		return $buf;
	}

	public static function tyobj()
	{
		return (object)array(
			'type'			=> null,
			'disp'			=> null,
			'subtype'		=> null,
			'empty'			=> null,
			'numeric'		=> null,
			'length'		=> null,
			'children'		=> null,
			'classes'		=> null,
		);
	}

	public static function type($input)
	{
		$type = self::tyobj();
		$type->disp		= $input;
		$type->empty	= empty($input);
		$type->numeric	= is_numeric($input);

		// avoid detecting strings with names of global functions as callbacks
		if (is_callable($input) && !(is_string($input) && function_exists($input))) {
			$type->type		= 'function';
			$type->disp		= 'fn()';
		}
		else if (is_array($input)) {
			$type->type		= 'array';
			$type->disp		= '[ ]';
			$type->children	= $input;
			$type->length	= count($type->children);
		}
		else if (is_resource($input)) {
			$type->type		= 'resource';
			$type->subtype	= get_resource_type($input);
			preg_match('/#\d+/', (string)$input, $matches);
			$type->disp		= $matches[0];
		}
		else if (is_object($input)) {
			$type->type		= 'object';
			$type->disp		= '{ }';
			$type->subtype	= get_class($input);
			$type->children	= array();

			$childs	= (array)$input;		// hacks access to protected and private props
			foreach ($childs as $k => $v) {
				// clean up odd chars left in private/protected names
				$k = preg_replace("/[^\w]?(?:{$type->subtype})?[^\w]?/", '', $k);
				$type->children[$k] = $v;
			}
		}
		else if (is_int($input))
			$type->type		= 'integer';
		else if (is_float($input))
			$type->type		= 'float';
		else if (is_string($input)) {
			$type->type		= 'string';
			$type->length	= strlen($input);
		}
		else if (is_bool($input)) {
			$type->type		= 'boolean';
			$type->disp		= $input ? 'true' : 'false';
		}
		else if (is_null($input)) {
			$type->type		= 'null';
			$type->disp		= 'null';
		}
		else
			$type->type		= gettype($input);

		if (array_key_exists($type->type, self::$hooks))
			self::proc_hooks($type->type, $input, $type);

		if (is_callable(self::$classy)) {
			$classes = call_user_func(self::$classy, $input);
			if (is_string($classes))
				$classes = explode(' ', $classes);
			if (is_array($classes))
				$type->classes = $classes;
		}

		return $type;
	}

	public static function proc_hooks($key, $input, $type)
	{
		foreach(self::$hooks[$key] as $fn) {
			if ($fn($input, $type))
				return true;
		}
		return false;
	}

	// hook_string, hook_resource
	public static function __callStatic($name, $args)
	{
		if (substr($name, 0, 5) == 'hook_') {
			$hookey = substr($name, 5);
			if (count($args) == 2)
				self::$hooks[$hookey][$args[1]] = $args[0];
			else
				self::$hooks[$hookey][] = $args[0];
		}
	}
}

// util functions for hooks
class printo_lib
{
	public static function rel_date($datetime) {
		$rel_date = '';
		$timestamp = is_string($datetime) ? strtotime($datetime) : $datetime;
		$diff = time()-$timestamp;
		$dir = '-';
		if ($diff < 0) {
			$diff *= -1;
			$dir = '+';
		}
		$yrs = floor($diff/31557600);
		$diff -= $yrs*31557600;
		$mhs = floor($diff/2592000);
		$diff -= $mhs*2419200;
		$wks = floor($diff/604800);
		$diff -= $wks*604800;
		$dys = floor($diff/86400);
		$diff -= $dys*86400;
		$hrs = floor($diff/3600);
		$diff -= $hrs*3600;
		$mins = floor($diff/60);
		$diff -= $mins*60;
		$secs = $diff;

		if		($yrs > 0)	$rel_date .= $yrs.'y' . ($mhs > 0 ? ' '.$mhs.'m' : '');
		elseif	($mhs > 0)	$rel_date .= $mhs.'m' . ($wks > 0 ? ' '.$wks.'w' : '');
		elseif	($wks > 0)	$rel_date .= $wks.'w' . ($dys > 0 ? ' '.$dys.'d' : '');
		elseif	($dys > 0)	$rel_date .= $dys.'d' . ($hrs > 0 ? ' '.$hrs.'h' : '');
		elseif	($hrs > 0)	$rel_date .= $hrs.'h' . ($mins > 0 ? ' '.$mins.'m' : '');
		elseif	($mins > 0)	$rel_date .= $mins.'m';
		else				$rel_date .= $secs.'s';

		return $dir . $rel_date;
	}
}

printo::hook_string(function($input, $type) {
	if (substr($input, 0, 5) == '<?xml') {
		// strip namespaces
		$input = preg_replace('/<(\/?)[\w-]+?:/', '<$1', preg_replace('/\s+xmlns:.*?=".*?"/', '', $input));

		if ($xml = simplexml_load_string($input)) {
			$type->subtype	= 'XML';
			$type->children = (array)$xml;
			// dont show length, or find way to detect uniform subnodes and treat as XML [] vs XML {}
			$type->length = null;

			return true;
		}

		return false;
	}

	return false;
}, 'is_xml');

printo::hook_string(function($input, $type) {
	if ($type->length > 0 && ($input{0} == '{' || $input{0} == '[') && ($json = json_decode($input))) {
		// maybe set subtype as JSON [] or JSON {}, will screw up classname
		$type->subtype	= 'JSON';
		$type->children = (array)$json;
		// dont show length of objects, only arrays
		$type->length = $input{0} == '[' ? count($type->children) : null;

		return true;
	}

	return false;
}, 'is_json');

printo::hook_string(function($input, $type) {
	if (strlen($input) > 5 && preg_match('#[:/-]#', $input) && ($ts = strtotime($input)) !== false) {
		$type->subtype = 'datetime';
		$type->length = printo_lib::rel_date($ts);

		return true;
	}

	return false;
}, 'is_datetime');

// css
ob_start();
?>
<style>
.printo {
		clear: both;
		font-family: Arial;
		font-size: 12px;

		background: #fff;
		border: 2px solid #228;
		border-radius: 5px;
		padding: 5px;
	}

	.printo ul {
		list-style: none;
		padding: 0 0 0 15px;
		margin: 0;

	}

	.printo ul ul {
		margin-top: 2px;
	}

	.printo li {
		position: relative;
		margin-bottom:1px;
	}

	.printo .excol {
		font-size: 8pt;
		position: absolute;
		margin: 1px 0 0 -15px;
		cursor: pointer;

	}

	.printo .expanded > .excol			{font-size: 10pt;}		/* for FF */
	.printo .expanded > .excol:after	{font-size: 8pt; content: "\25BC";}
	.printo .collapsed > .excol:after	{content: "\25B6";}
	.printo .collapsed > ul				{display: none;}

	.printo .lbl						{position: relative; padding: 2px 4}
	.printo .lbl > *					{display: inline-block;}


	.printo li > .lbl					{background-color: #ddd;}
	.printo li:nth-child(odd) > .lbl	{background-color: #eee;}

	.printo .key						{font-weight: bold;}
	.printo .val						{margin: 0 5px 0 30px; min-width: 5px; vertical-align: top; font-size: 14px; }
	.printo .typ,
	.printo .sub,
	.printo .len						{color: #666666; margin-right: 5px; font-size: 10px; }

	.printo .typ						{font-size: 10px;  }

	.printo .array			> .lbl .typ {background-color: #C0BCFF;}
	.printo .object			> .lbl .typ {background-color: #98FB98;}
	.printo .function		> .lbl .typ {background-color: #FAFF5C;}
	.printo .boolean		> .lbl .typ {background-color: #08F200;}
	.printo .boolean.empty	> .lbl .typ {background-color: #FF8C8C;}
	.printo .null			> .lbl .typ {background-color: #FFD782;}
	.printo .integer		> .lbl .typ {background-color: #EAB2EA;}
	.printo .float			> .lbl .typ {background-color: #EB65EB;}
	.printo .string			> .lbl .typ {background-color: #FFBFBF;}
	.printo .resource		> .lbl .typ {background-color: #E2FF8C;}
	.printo .numeric		> .lbl .typ {}
	.printo .ref			> .lbl .typ {background-color: #CEFBF3;}
	.printo .datetime		> .lbl .typ {}

	.printo .stdClass .sub,
	.printo .datetime .sub {
		display: none;
	}

	/* hide length of empty stuff except numeric eg '0' strings */
	.printo .empty:not(.numeric) > .lbl .len {
		display: none;
	}

	/* display empty strings as a gray middle dot */
	.printo .empty.string:not(.numeric) > .lbl .val:after {
		content: "\0387";
		color: #BBBBBB;
	}

	/* hide empty strings completely
	.printo .empty.string:not(.numeric) > .lbl .val {
		display: none;
	}
	*/
</style>
<?php
printo::$css = ob_get_contents();
ob_end_clean();

// js
ob_start();
?>
<script>
	(function(){
		function toggle(e) {
			if (e.which != 1) return;

			if (e.target.className.indexOf("excol") !== -1) {
				e.target.parentNode.className = e.target.parentNode.className.replace(/\bexpanded\b|\bcollapsed\b/, function(m) {
					return m == "collapsed" ? "expanded" : "collapsed";
				});
			}
		}
		document.addEventListener("click", toggle, false);
	})();
</script>
<?php
printo::$js = ob_get_contents();
ob_end_clean();

