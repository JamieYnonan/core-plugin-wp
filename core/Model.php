<?php
namespace CorePluginWp;

abstract class Model implements ModelInterface
{
	protected static $filter = [
		'%s' => FILTER_SANITIZE_STRING,
		'%d' => FILTER_SANITIZE_NUMBER_INT
	];

	public static function tableName(){
		return General::camel2id(get_called_class());
	}

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

	public function save(array $data, $getDataFormat = true)
	{
		global $wpdb;

		if ($getDataFormat === true) {
			$data = static::getDataFormat($data);
		}
		extract($data);

		$wpdb->show_errors();
		if (empty($this->id)) {
			$wpdb->insert(
				static::tableName(),
				$dataModel,
				$formatModel
			);
			if (!empty($wpdb->insert_id)) {
				$this->id = $wpdb->insert_id;

				self::loadRowOnModel($this, $dataModel);
				return self::message();
			}
			return self::message(false);
		}
		$wpdb->update(
			static::tableName(),
			$dataModel,
			['id' => $this->id],
			$formatModel
		);
		self::loadRowOnModel($this, $dataModel);
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

	public static function where(array $where, $outputObject = true)
	{
		global $wpdb;

		$sql = 'SELECT * FROM '. static::tableName() .' WHERE';
		$attrs = static::$attributeForm;

		$parameters = [];
		foreach ($where as $attr => $parameter) {
			$sql .= ' '. $attr .' = '. $attrs[$attr]['format'];
			$parameters[] = $parameter;
		}

		$output = ($outputObject === true) ? OBJECT : ARRAY_A;

		return $wpdb->get_results(
			$wpdb->prepare($sql, $parameters), $output
		);
	}

	public static function find($value = '', $id = 'id')
	{
		if (empty($id) || empty($value)) {
			return false;
		}

		global $wpdb;

		$row = $wpdb->get_row(
			'SELECT * 
			FROM '. static::tableName() .' 
			WHERE '. $id .' = "'. $value .'" 
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
}