<?php
namespace CorePluginWp;

/**
 * Class Entity
 * @package CorePluginWp
 */
abstract class Entity extends Model implements EntityInterface
{
    /**
     * @var string
     */
    protected static $pk = 'id';

    /**
     * @return string
     */
    public static function tableName()
    {
        return $GLOBALS['wpdb']->prefix . StringH::camel2id(get_called_class());
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