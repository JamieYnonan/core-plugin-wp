<?php
namespace CorePluginWp;

use Helper\StringH;

abstract class Model implements ModelInterface
{
	protected static $filter = [
		'%s' => FILTER_SANITIZE_STRING,
		'%d' => FILTER_SANITIZE_NUMBER_INT
	];

	public static function tableName()
    {
		return StringH::camel2id(get_called_class());
	}

	/**
	 * @param data an array argument, key = attribute of table, value = value.
	 */
	public static function getDataFormat(array $data)
	{
		global $wpdb;

		$attrs = static::$attributeForm;
		$return = [];
		foreach ($attrs as $attr => $formatLabel) {
			if (isset($data[$attr])) {
				$return['dataModel'][$attr] = filter_var(
					$data[$attr],
					self::$filter[$formatLabel['format']]
				);
				$return['formatModel'][] = $formatLabel['format'];
			}
		}
		return $return;
	}

	/**
	 * @param data an array argument, key = attribute of table, value = value.
	 * @param getDataFormat boolean.
	 */
	public function save(array $data, $getDataFormat = true)
	{
		global $wpdb;

		if ($getDataFormat === true) {
			$data = static::getDataFormat($data);
		}

		if (empty($this->id)) {
			$wpdb->insert(
				static::tableName(),
				$data['dataModel'],
				$data['formatModel']
			);
			if (!empty($wpdb->insert_id)) {
				$this->id = $wpdb->insert_id;

				self::loadRowOnModel($this, $data['dataModel']);
				return self::message();
			}
			return self::message(false);
		}
		$wpdb->update(
			static::tableName(),
			$data['dataModel'],
			['id' => $this->id],
			$data['formatModel']
		);
		self::loadRowOnModel($this, $data['dataModel']);
		return self::message();
	}

	public static function firstOrModel()
	{
		global $wpdb;

		$row = $wpdb->get_row(
			'SELECT *
			FROM '. static::tableName() .'
			LIMIT 1',
			ARRAY_A
		);

		$model = new static();

		if($row !== null) {
			self::loadRowOnModel($model, $row);
		}

		return $model;
	}

	/**
	 * @param model an instance of Model.
	 * @param row an array argument.
	 */
	private static function loadRowOnModel(Model &$model, array $row)
	{
		foreach ($row as $attr => $value) {
			$model->$attr = $value;
		}
	}

	public static function all()
	{
		global $wpdb;

		return $wpdb->get_results(
			'SELECT * FROM '. static::tableName(),
			OBJECT
		);
	}

	/**
	 * @param where an array argument, key = attribute of table, value = value.
	 * @param outputType a string argument.
	 */
	public static function where(array $where, $outputType  = 'OBJECT')
	{
		global $wpdb;

		$sql = 'SELECT * FROM '. static::tableName() .' WHERE';
		$attrs = static::$attributeForm;

		$parameters = [];
		foreach ($where as $attr => $parameter) {
            $sig = ' = ';
            if (is_array($parameter)) {
                $sig = " {$parameter[0]} ";
                $parameter = $parameter[1];
            }
            if (count($parameters) > 0) {
                $sql .= ' AND';
            }
			$sql .= ' '. $attr . $sig . $attrs[$attr]['format'];
			$parameters[] = $parameter;
		}

		return $wpdb->get_results(
			$wpdb->prepare($sql, $parameters), $outputType
		);
	}

	/**
	 * @param id integer.
	 */
	public static function findOne($id)
	{
		global $wpdb;

		$row = $wpdb->get_row(
			'SELECT *
			FROM '. static::tableName() .'
			WHERE id = "'. (int)$id .'"
			LIMIT 1',
			ARRAY_A
		);

		if ($row === null) {
			return null;
		}

		$model = new static();
		self::loadRowOnModel($model, $row);
		return $model;
	}

	/**
	 * @param state boolean, true = ok, false = fail.
	 */
	protected static function message($state = true)
	{
		$message = [];
		if ($state === true) {
			$message['text'] = 'Update!';
			$message['options'] = ['class' => 'bg bg-success'];
			$message['saved'] = true;
		} else {
			$message['text'] = 'Error!';
			$message['options'] = ['class' => 'bg bg-danger'];
			$message['saved'] = false;
		}
		return $message;
	}

	/**
	 * @param ids an array argument.
	 */
	public static function deleteIdIn(array $ids)
	{
		$idString = implode(',', $ids);
		global $wpdb;

		$num_rows = $wpdb->query(
			"DELETE FROM ". static::tableName() ."
			WHERE id IN (". $idString .")"
		);
		return $num_rows > 0;
	}

    public static function delete($id)
    {
        global $wpdb;

		return $wpdb->delete(static::tableName(), ['id' => $id], ['%d']);
    }
}
