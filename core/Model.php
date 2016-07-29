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
     * @var string
     */
	protected static $pk = 'id';

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
            $this->loadData($data, true);
        }
	}

    /**
     * @var array
     */
	protected static $format = [
        'int' => '%d',
        'float' => '%f',
        'string' => '%s'
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return $GLOBALS['wpdb']->prefix . StringH::camel2id(get_called_class());
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
	public function loadData(array $data, $loadPk = false)
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

    /**
     * @param bool $validate
     * @return bool
     */
	public function save($validate = true)
	{
		if ($validate === true) {
			if ($this->validate() === false) {
				return false;
			}
		}
		
		$data = $this->getDataFormat();

		if (empty($this->{static::$pk})) {
			if ($GLOBALS['wpdb']->insert(
				static::tableName(),
				$data['data'],
				$data['format']) === false
			) {
				return false;
			}
			$this->{static::$pk} = $GLOBALS['wpdb']->insert_id;
			return true;
		}

		$update = $GLOBALS['wpdb']->update(
			$this->tableName(),
			$data['data'],
			[static::$pk => $this->{static::$pk}],
			$data['format']
		);
		return ($update === false) ? false : true;
	}

    /**
     * @return mixed
     * @throws \Exception
     */
	public function delete()
    {
    	if (empty($this->{static::$pk})) {
    		throw new \Exception("Error, not exists register");
    	}
		return $GLOBALS['wpdb']->delete(
			static::tableName(),
			[static::$pk => $this->{static::$pk}],
			['%d']
		);
    }

    /**
     * @return static
     */
    public static function firstOrModel()
    {
    	$data = $GLOBALS['wpdb']->get_row(
    		"SELECT * FROM {static::tableName()} LIMIT 1",
    		ARRAY_A
    	);

    	return ($data === null) ? new static() : new static($data);
    }

    /**
     * @param int $pk
     * @return static
     * @throws \Exception
     */
    public static function findOne($pk)
    {
    	$data = $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare(
    		"SELECT * FROM {static::tableName()} WHERE {static::$pk} = %d LIMIT 1",
    		$pk
    	), ARRAY_A);

    	if ($data === null) {
            throw new \Exception('not exists element '. static::$pk .' = '. $pk);
    	}

    	return new static($data);
    }

    /**
     * @return array
     */
    private function getDataFormat()
    {
        $return = [];
        foreach ($this->rules() as $attr => $rules) {
            $return['data'][$attr] = $this->$attr;
            $return['format'][] = (isset(self::$format[$rules[0]]))
            	? self::$format[$rules[0]]
            	: '%s';
        }
        return $return;
    }

}