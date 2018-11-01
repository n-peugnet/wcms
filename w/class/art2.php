<?php

class Art2
{
	protected $id;
	protected $title;
	protected $description;
	protected $tag;
	protected $date;
	protected $datecreation;
	protected $datemodif;
	protected $daterender;
	protected $css;
	protected $quickcss;
	protected $javascript;
	protected $html;
	protected $header;
	protected $section;
	protected $nav;
	protected $aside;
	protected $footer;
	protected $render;
	protected $secure;
	protected $invitepassword;
	protected $interface;
	protected $linkfrom;
	protected $linkto;
	protected $template;
	protected $affcount;
	protected $editcount;


	const LEN = 255;
	const LENTEXT = 20000;
	const SECUREMAX = 2;
	const LENCOULEUR = 7;
	const DEBUT = '(?id=';
	const FIN = ')';
	const TABS = ['section', 'css', 'header', 'html', 'nav', 'aside', 'footer', 'javascript'];
	const VAR_DATE = ['date', 'datecreation', 'datemodif', 'daterender'];

	  
	  

// _____________________________________________________ F U N ____________________________________________________

	public function __construct($donnees)
	{
		$this->hydrate($donnees);
	}

	public function hydrate($donnees)
	{
		foreach ($donnees as $key => $value) {
			$method = 'set' . $key;

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	public function reset()
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		$this->settitle($this->id());
		$this->setdescription('');
		$this->settag([]);
		$this->setdate($now);
		$this->setdatecreation($now);
		$this->setdatecreation($now);
		$this->setdatemodif($now);
		$this->setdaterender($now);
		$this->setcss('');
		$this->setquickcss([]);
		$this->setjavascript('');
		$this->sethtml('');
		$this->setheader('');
		$this->setsection('');
		$this->setnav('');
		$this->setaside('');
		$this->setfooter('');
		$this->setrender('');
		$this->setsecure(2);
		$this->setinvitepassword('invitepassword');
		$this->setinterface('section');
		$this->setlinkfrom([]);
		$this->setlinkto([]);
		$this->settemplate([]);
		$this->setaffcount(0);
		$this->seteditcount(0);
	}

	public function updatelinkfrom()
	{
		$this->linkfrom = [];
		$this->linkfrom = array_unique(search($this->md(true), self::DEBUT, self::FIN));

	}

	public static function classvarlist()
	{
		$classvarlist = [];
		foreach (get_class_vars(__class__) as $var => $default) {
			$classvarlist[] = $var;
		}
		return ['artvarlist' => $classvarlist];
	}




	public function calclinkto($getlist)
	{
		$linkto = [];
		if (!empty($getlist)) {
			foreach ($getlist as $link) {
				if (in_array($this->id(), $link->linkfrom('array'))) {
					$linkto[] = $link->id();
				}
			}
			$this->setlinkto($linkto);
		}
	}


	public function autotaglist()
	{
		$pattern = "/%%(\w*)%%/";
		preg_match_all($pattern, $this->md(), $out);
		return $out[1];

	}

	public function autotaglistupdate($taglist)
	{
		foreach ($taglist as $tag => $artlist) {
			$replace = '<ul>';
			foreach ($artlist as $art) {
				$replace .= '<li><a href="?id=' . $art->id() . '" title="' . $art->description() . '">' . $art->title() . '</a></li>';
			}
			$replace .= '</ul>';
			$this->section = str_replace('%%' . $tag . '%%', $replace, $this->section);
		}
	}

	public function autotaglistcalc($taglist)
	{
		foreach ($taglist as $tag => $artlist) {
			foreach ($artlist as $art) {
				if (!in_array($art->id(), $this->linkfrom('array')) && $art->id() != $this->id()) {
					$this->linkfrom[] = $art->id();
				}
			}
		}
	}

	public function templaterender(array $vars)
	{
		$datas = [];
		foreach ($vars as $var) {
			if (method_exists($this, $var))
				$datas[$var] = $this->$var();
		}
		return $datas;
	}

	public function dry()
	{
		$array = [];
		foreach (get_class_vars(__class__) as $var => $value) {
			if(in_array($var, self::VAR_DATE)) {
				$array[$var] = $this->$var('string');
			} else {
				$array[$var] = $this->$var();
			}
		}
		return $array;
	}


		// _____________________________________________________ G E T ____________________________________________________

	public function id($type = 'string')
	{
		return $this->id;
	}

	public function title($type = 'string')
	{
		return $this->title;
	}

	public function description($type = 'string')
	{
		return $this->description;
	}

	public function tag($option = 'array')
	{
		if ($option == 'string') {
			return implode(", ", $this->tag);
		} elseif ($option == 'array') {
			return $this->tag;
		} elseif ($option == 'sort') {
			return count($this->tag);
		}
	}

	public function date($option = 'date')
	{
		if ($option == 'string') {
			return $this->date->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->date;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->date->diff($now));
		}


	}

	public function datecreation($option = 'date')
	{
		if ($option == 'string') {
			return $this->datecreation->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->datecreation;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->datecreation->diff($now));
		}
	}


	public function datemodif($option = 'date')
	{
		if ($option == 'string') {
			return $this->datemodif->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->datemodif;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->datemodif->diff($now));
		}
	}

	public function daterender($option = 'date')
	{
		if ($option == 'string') {
			return $this->daterender->format('Y-m-d H:i:s');
		} elseif ($option == 'date' || $option == 'sort') {
			return $this->daterender;
		} elseif ($option == 'hrdi') {
			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
			return hrdi($this->daterender->diff($now));
		}
	}

	public function css($type = 'string')
	{
		return $this->css;
	}

	public function quickcss($type = 'array')
	{
		if ($type == 'json') {
			return json_encode($this->quickcss);
		} elseif ($type == 'array') {
			return $this->quickcss;
		}
	}


	public function csstemplate(App $app)
	{
		$data = [];
		$temp = '';
		if (!empty($this->template())) {
			if ($app->exist($this->template()) and !in_array($this->template(), $data)) {
				$template = $app->get($this->template());
				$temp = $temp . $template->css($app);
				$data[] = $template->id();

			}

		}
		$cssprint = str_replace('url(/', 'url(' . $app::MEDIA_DIR, $temp . $this->css);
		return $cssprint;
	}


	public function javascript($type = 'string')
	{
		return $this->javascript;
	}

	public function html($type = 'string')
	{
		return $this->html;
	}

	public function header($type = 'string')
	{
		return $this->header;
	}

	public function md($expand = false)
	{
		if ($expand == true) {
			$md = str_replace('](=', '](?id=', $this->section);
		} else {
			$md = $this->section;
		}
		return $md;
	}

	public function section($type = 'string')
	{
		return $this->section;
	}

	public function section888(App $app)
	{

		// %%%% TITLE & DESCIPTION
		$section = str_replace('%TITLE%', $this->title(), $this->section);
		$section = str_replace('%DESCRIPTION%', $this->description(), $section);

		$parser = new MarkdownExtra;

		// id in headers
		$parser->headerid_func = function ($header) {
			return preg_replace('/[^\w]/', '', strtolower($header));
		};
		$section = $parser->transform($section);

		// replace = > ?id=
		$section = str_replace('href="=', 'href="?id=', $section);


		// infobulles tooltip
		foreach ($this->linkfrom('array') as $id) {
			$title = "Cet article n'existe pas encore";
			foreach ($app->getlister(['id', 'description']) as $item) {
				if ($item->id() == $id) {
					$title = $item->description();
				}
			}
			$linkfrom = 'href="?id=' . $id . '"';
			$titlelinkfrom = ' title="' . $title . '" ' . $linkfrom;
			$section = str_replace($linkfrom, $titlelinkfrom, $section);
		}

		if (!empty(strstr($section, '%SUMMARY%'))) {



			$section = str_replace('%SUMMARY%', sumparser($section), $section);
		}


		$section = str_replace('href="./media/', ' class="file" target="_blank" href="./media/', $section);
		$section = str_replace('href="http', ' class="external" target="_blank" href="http', $section);
		$section = str_replace('<img src="/', '<img src="./media/', $section);
		$section = str_replace('<iframe', '<div class="iframe"><div class="container"><iframe class="video" ', $section);
		$section = str_replace('</iframe>', '</iframe></div></div>', $section);
		return $section;


	}

	public function nav($type = "string")
	{
		return $this->nav;
	}

	public function aside($type = "string")
	{
		return $this->aside;
	}

	public function footer($type = "string")
	{
		return $this->footer;
	}

	public function render($type = 'string')
	{
		return $this->render;
	}

	public function secure($type = 'int')
	{
		if ($type == 'string') {
			if ($this->secure == 0) $secure = 'public';
			if ($this->secure == 1) $secure = 'private';
			if ($this->secure == 2) $secure = 'not published';
			return $secure;
		} else {
			return $this->secure;
		}
	}

	public function invitepassword($type = 'string')
	{
		return $this->invitepassword;
	}

	public function interface($type = 'string')
	{
		return $this->interface;
	}

	public function linkfrom($option = 'array')
	{
		if ($option == 'json') {
			$linkfrom = json_encode($this->linkfrom);
		} elseif ($option == 'array') {
			$linkfrom = $this->linkfrom;
		} elseif ($option == 'sort') {
			return count($this->linkfrom);
		}
		return $linkfrom;

	}

	public function linkto($option = 'array')
	{
		if ($option == 'json') {
			$linkto = json_encode($this->linkto);
		} elseif ($option == 'array') {
			$linkto = $this->linkto;
		} elseif ($option == 'sort') {
			return count($this->linkto);
		}
		return $linkto;

	}

	public function template($type = 'array')
	{
		if ($type == 'json') {
			return json_encode($this->template);
		} elseif ($type = 'array') {
			return $this->template;
		}
	}

	public function affcount($type = 'int')
	{
		return $this->affcount;
	}

	public function editcount($type = 'int')
	{
		return $this->editcount;
	}





		// _____________________________________________________ S E T ____________________________________________________

	public function setid($id)
	{
		if (strlen($id) < self::LEN and is_string($id)) {
			$this->id = strip_tags(strtolower(str_replace(" ", "", $id)));
		}
	}

	public function settitle($title)
	{
		if (strlen($title) < self::LEN and is_string($title)) {
			$this->title = strip_tags(trim($title));
		}
	}

	public function setdescription($description)
	{
		if (strlen($description) < self::LEN and is_string($description)) {
			$this->description = strip_tags(trim($description));
		}
	}

	public function settag($tag)
	{
		if (is_string($tag)) {

			if (strlen($tag) < self::LEN and is_string($tag)) {
				$tag = strip_tags(trim(strtolower($tag)));
				$tag = str_replace('*', '', $tag);
				$tag = str_replace(' ', '', $tag);

				$taglist = explode(",", $tag);
				$taglist = array_filter($taglist);
				$this->tag = $taglist;
			}
		} elseif (is_array($tag)) {
			$this->tag = $tag;
		}
	}

	public function setdate($date)
	{
		if ($date instanceof DateTimeImmutable) {
			$this->date = $date;
		} else {
			$this->date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdatecreation($datecreation)
	{
		if ($datecreation instanceof DateTimeImmutable) {
			$this->datecreation = $datecreation;
		} else {
			$this->datecreation = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datecreation, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdatemodif($datemodif)
	{
		if ($datemodif instanceof DateTimeImmutable) {
			$this->datemodif = $datemodif;
		} else {
			$this->datemodif = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datemodif, new DateTimeZone('Europe/Paris'));
		}
	}

	public function setdaterender($daterender)
	{
		if ($daterender instanceof DateTimeImmutable) {
			$this->daterender = $daterender;
		} else {
			$this->daterender = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $daterender, new DateTimeZone('Europe/Paris'));
		}
	}


	public function setcss($css)
	{
		if (strlen($css) < self::LENTEXT and is_string($css)) {
			$this->css = strip_tags(trim(strtolower($css)));
		}
	}


	public function setquickcss($quickcss)
	{
		if (is_string($quickcss)) {
			$quickcss = json_decode($quickcss, true);
		}
		if (is_array($quickcss)) {
			$this->quickcss = $quickcss;
		}
	}

	public function setjavascript($javascript)
	{
		if (strlen($javascript < self::LENTEXT && is_string($javascript))) {
			$this->javascript = $javascript;
		}
	}


	public function sethtml($html)
	{
		if (strlen($html < self::LENTEXT && is_string($html))) {
			$this->html = $html;
		}
	}

	public function setheader($header)
	{
		if (strlen($header < self::LENTEXT && is_string($header))) {
			$this->header = $header;
		}
	}

	public function setsection($section)
	{
		if (strlen($section) < self::LENTEXT and is_string($section)) {
			$this->section = $section;
		}
	}

	public function setnav($nav)
	{
		if (strlen($nav) < self::LENTEXT and is_string($nav)) {
			$this->nav = $nav;
		}
	}

	public function setaside($aside)
	{
		if (strlen($aside) < self::LENTEXT and is_string($aside)) {
			$this->aside = $aside;
		}
	}

	public function setfooter($footer)
	{
		if (strlen($footer) < self::LENTEXT and is_string($footer)) {
			$this->footer = $footer;
		}
	}

	public function setrender($render)
	{
		$this->render = $render;
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 and $secure <= self::SECUREMAX) {
			$this->secure = intval($secure);
		}
	}

	public function setinvitepassword($invitepassword)
	{
		if (is_string($invitepassword) && strlen($invitepassword) < self::LEN) {
			$this->invitepassword = $invitepassword;
		}
	}

	public function setinterface($interface)
	{
		if (in_array($interface, self::TABS)) {
			$this->interface = $interface;
		}
	}

	public function setlinkfrom($linkfrom)
	{
		if(is_array($linkfrom)) {
			$this->linkfrom = $linkfrom;
		} elseif(is_string($linkfrom)) {
			$linkfromjson = json_decode($linkfrom);
			if(is_array($linkfromjson)) {
				$this->linkfrom = $linkfromjson;
			}
		} elseif ($linkfrom === null) {
			$this->linkfrom = [];
		}
	}

	public function setlinkto($linkto)
	{
		if(is_array($linkto)) {
			$this->linkto = $linkto;
		} elseif(is_string($linkto)) {
			$linktojson = json_decode($linkto);
			if(is_array($linktojson)) {
				$this->linkto = $linktojson;
			}
		} elseif ($linkto === null) {
			$this->linkto = [];
		}
	}

	public function settemplate($template)
	{
		if (is_string($template)) {
			$template = json_decode($template, true);
		}
		if (is_array($template)) {
			$this->template = $template;
		}
	}

	public function setaffcount($affcount)
	{
		if (is_int($affcount)) {
			$this->affcount = $affcount;
		} elseif (is_numeric($affcount)) {
			$this->affcount = intval($affcount);
		}
	}

	public function seteditcount($editcount)
	{
		if (is_int($editcount)) {
			$this->editcount = $editcount;
		} elseif (is_numeric($editcount)) {
			$this->editcount = intval($editcount);
		}
	}


	// __________________________________ C O U N T E R S ______________________________


	public function addeditcount()
	{
		$this->editcount ++;
	}

	public function addaffcount()
	{
		$this->affcount ++;
	}

	public function updateedited()
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));
		$this->setdatemodif($now);
		$this->addeditcount();
	}


}


?>