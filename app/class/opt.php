<?php
class Opt
{
	protected $sortby = 'id';
	protected $order = 1;
	protected $tagfilter = [];
	protected $tagcompare = 'OR';
	protected $authorfilter = [];
	protected $authorcompare = 'OR';
	protected $secure = 4;
	protected $linkto = ['min' => '0', 'max' => '0'];
	protected $linkfrom = ['min' => '0', 'max' => '0'];
	protected $col = ['id'];
	protected $taglist = [];
	protected $authorlist = [];
	protected $invert = 0;
	protected $limit= 0;

	protected $artvarlist;

	public function __construct(array $donnees = [])
	{
		$this->hydrate($donnees);
	}

	public function hydrate(array $donnees)
	{
		foreach ($donnees as $key => $value) {
			$method = 'set' . $key;

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}


	public function resetall()
	{
		$varlist = get_class_vars(__class__);

		foreach ($varlist as $var => $default) {
			$method = 'set' . $var;
			$this->$method($default);
		}
	}

	public function reset($var)
	{
		$varlist = get_class_vars(__class__);
		if (in_array($var, $varlist)) {
			$this->$var = $varlist[$var];
		}
	}

	public function submit()
	{
		if (isset($_GET['submit'])) {
			if ($_GET['submit'] == 'reset') {
				$_SESSION['opt'] = [];
			} elseif ($_GET['submit'] == 'filter') {
				$this->getall();
			}
		} else {
			$this->sessionall();
		}
	}

	public function getall()
	{
		$optlist = ['sortby', 'order', 'secure', 'tagcompare', 'tagfilter', 'authorcompare', 'authorfilter', 'limit','invert'];

		foreach ($optlist as $method) {
			if (method_exists($this, $method)) {
				if (isset($_GET[$method])) {
					$setmethod = 'set' . $method;
					$this->$setmethod($_GET[$method]);
				} else {
					$this->reset($method);
				}
				$_SESSION['opt'][$method] = $this->$method();
			}
		}
	}

	public function sessionall()
	{
		if (isset($_SESSION['opt'])) {
			$this->hydrate($_SESSION['opt']);
		}
	}

	public function getadress(string $sortby = '')
	{
		if ($this->sortby === $sortby) {
			$order = $this->order * -1;
		} else {
			$order = $this->order;
		}
		if(empty($sortby)) {
			$sortby = $this->sortby;
		}
		$adress = '?sortby=' . $sortby;
		$adress .= '&order=' . $order;
		$adress .= '&secure=' . $this->secure;
		$adress .= '&tagcompare=' . $this->tagcompare;
		foreach ($this->tagfilter as $tag) {
			$adress .= '&tagfilter[]=' . $tag;
		}
		$adress .= '&authorcompare=' . $this->authorcompare;
		foreach ($this->authorfilter as $author) {
			$adress .= '&authorfilter[]=' . $author;
		}
		if ($this->invert == 1) {
			$adress .= '&invert=1';
		}
		$adress.= '&limit=' .$this->limit;
		$adress .= '&submit=filter';

		return $adress;
	}



	/**
	 * Get the query as http string
	 * 
	 * @return string The resulted query
	 */
	public function getquery(): string
	{
		$class = get_class_vars(get_class($this));
		$object = get_object_vars($this);
		$class['artvarlist'] = $object['artvarlist'];
		$class['taglist'] = $object['taglist'];
		$class['authorlist'] = $object['authorlist'];
		$query = array_diff_assoc_recursive($object, $class);

		return urldecode(http_build_query($query));
	}




	// _______________________________________________ G E T _______________________________________________

	public function sortby()
	{
		return $this->sortby;
	}

	public function order()
	{
		return $this->order;
	}

	public function secure()
	{
		return $this->secure;
	}

	public function tagfilter($type = 'array')
	{
		return $this->tagfilter;
	}

	public function tagcompare()
	{
		return $this->tagcompare;
	}

	public function authorfilter($type = 'array')
	{
		return $this->authorfilter;
	}

	public function authorcompare()
	{
		return $this->authorcompare;
	}

	public function linkto($type = 'array')
	{
		return $this->linkto;
	}

	public function linkfrom($type = 'array')
	{
		return $this->linkfrom;
	}

	public function col($type = 'array')
	{
		if ($type == 'string') {
			return implode(', ', $this->col);
		} else {
			return ($this->col);
		}
	}

	public function taglist()
	{
		return $this->taglist;
	}

	public function authorlist()
	{
		return $this->authorlist;
	}

	public function invert()
	{
		return $this->invert;
	}

	public function artvarlist()
	{
		return $this->artvarlist;
	}

	public function limit()
	{
		return $this->limit;
	}


	// __________________________________________________ S E T _____________________________________________

	public function setsortby($sortby)
	{
		if (is_string($sortby) && in_array($sortby, $this->artvarlist)) {
			$this->sortby = strtolower(strip_tags($sortby));
		}
	}

	public function setorder($order)
	{
		$order = intval($order);
		if (in_array($order, [-1, 0, 1])) {
			$this->order = $order;
		}
	}

	public function settagfilter($tagfilter)
	{
		if (!empty($tagfilter) && is_array($tagfilter)) {
			$tagfilterverif = [];
			foreach ($tagfilter as $tag) {
				if (array_key_exists($tag, $this->taglist)) {
					$tagfilterverif[] = $tag;
				}
			}
			$this->tagfilter = $tagfilterverif;
		}
	}

	public function settagcompare($tagcompare)
	{
		if (in_array($tagcompare, ['OR', 'AND'])) {
			$this->tagcompare = $tagcompare;
		}
	}

	public function setauthorfilter($authorfilter)
	{
		if (!empty($authorfilter) && is_array($authorfilter)) {
			$authorfilterverif = [];
			foreach ($authorfilter as $author) {
				if (array_key_exists($author, $this->authorlist)) {
					$authorfilterverif[] = $author;
				}
			}
			$this->authorfilter = $authorfilterverif;
		}
	}

	public function setauthorcompare($authorcompare)
	{
		if (in_array($authorcompare, ['OR', 'AND'])) {
			$this->authorcompare = $authorcompare;
		}
	}

	public function setsecure($secure)
	{
		if ($secure >= 0 && $secure <= 5) {
			$this->secure = intval($secure);
		}
	}

	public function setlinkto($range)
	{
		$this->linkto = $range;
	}

	public function setlinkfrom($range)
	{
		$this->linkfrom = $range;
	}

	public function setlinktomin($min)
	{
		$this->linkto['min'] = intval($min);
	}

	public function setlinktomax($max)
	{
		$this->linkto['max'] = intval($max);
	}

	public function setlinkfrommin($min)
	{
		$this->linkfrom['min'] = intval($min);
	}

	public function setlinkfrommax($max)
	{
		$this->linkfrom['max'] = intval($max);
	}

	public function setcol($col)
	{
		if (is_array($col)) {
			$this->col = array_intersect($this->artvarlist(), $col);
		}
	}

	public function settaglist(array $artlist)
	{
		$taglist = [];
		foreach ($artlist as $art) {
			foreach ($art->tag('array') as $tag) {
				if (!array_key_exists($tag, $taglist)) {
					$taglist[$tag] = 1;
				} else {
					$taglist[$tag]++;
				}
			}
		}
		$taglistsorted = arsort($taglist);
		$this->taglist = $taglist;
	}

	public function setauthorlist(array $artlist)
	{
		$authorlist = [];
		foreach ($artlist as $art) {
			foreach ($art->authors('array') as $author) {
				if (!array_key_exists($author, $authorlist)) {
					$authorlist[$author] = 1;
				} else {
					$authorlist[$author]++;
				}
			}
		}
		$authorlistsorted = arsort($authorlist);
		$this->authorlist = $authorlist;
	}

	public function setinvert(int $invert)
	{
		if ($invert == 0 || $invert == 1) {
			$this->invert = $invert;
		} else {
			$this->invert = 0;
		}
	}

	public function setlimit($limit)
	{
		$limit = intval($limit);
		if($limit < 0) {
			$limit = 0;
		} elseif ($limit >= 10000) {
			$limit = 9999;
		}
		$this->limit = $limit;
	}


	public function setartvarlist(array $artvarlist)
	{
		$this->artvarlist = $artvarlist;
	}
}
