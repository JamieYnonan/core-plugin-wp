<?php
namespace CorePluginWp;

use Helpers\StringH;

/**
 * Class Model
 * @package CorePluginWp
 */
abstract class Model implements ModelInterface
{
    /**
     * @var bool
     */
    protected $_sanitize = true;

    /**
     * @var Validator
     */
	protected $_validator;

    /**
     * Model constructor.
     * @param array|null $data
     */
	public function __construct(array $data = null)
	{
        if (is_array($data)) {
            $this->loadData($data);
        }
	}

    /**
     * @return array
     */
    public function rules()
    {
    	return [];
    }

    /**
     * @return bool
     */
	public function validate()
	{
		$this->_validator = new Validator($this);
		return $this->_validator->validate();
	}

    /**
     * @param null $attr
     * @return array|mixed|null
     * @throws \Exception
     */
	public function getErrors($attr = null)
	{
		if ($this->_validator === null) {
			throw new \Exception("Error, first call the method validate");
		}
		return $this->_validator->getErrors($attr);
	}

    /**
     * @param array $data
     * @param bool $loadPk
     */
	public function loadData(array $data, $loadPk = true)
	{
		if ($loadPk === true && !empty((int)$data[static::$pk])) {
			$this->{static::$pk} = (int)$data[static::$pk];
		}
		foreach (array_keys($this->rules()) as $attr) {
			if (isset($data[$attr])) {
				$this->$attr = $data[$attr];
			}
		}
	}
}