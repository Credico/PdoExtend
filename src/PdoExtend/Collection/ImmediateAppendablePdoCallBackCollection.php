 <?php

namespace PdoExtend\Collection;
use iController\Platform\Exception\DeprecatedException;

/**
 * Because PdoCallBackCollection doesn't work properly, this class pretends to be a PdoCallBackCollection,
 * but in fact it's just a nice normal collection without any black magic.
 */
class ImmediateAppendablePdoCallBackCollection extends AppendablePdoCallBackCollection implements \Countable
{
	/** @var array */
	private $elements = array();

	/** int */
	private $position = 0;

	public function __construct(\PDOStatement $pdoStatement, $callBack)
	{
		while($record = $pdoStatement->fetch(\PDO::FETCH_ASSOC)) {
			$this->elements[] = call_user_func_array($callBack, array($record));
		}
	}

	public function append($value)
	{
		$this->elements[] = $value;
	}

	/**
	 * @deprecated The constructor does this
	 */
	protected function fetch()
	{
		throw new DeprecatedException("Deprecated, the constructor does this");
	}

	public function count()
	{
		return count($this->elements);
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function current()
	{
		return $this->elements[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function valid()
	{
		return isset($this->elements[$this->position]);
	}
}