<?php
namespace CorePluginWp;

/**
 * Class Validator
 * @package CorePluginWp
 */
class Validator
{
    /**
     * @var array
     */
    protected static $filterSanitize = [
        'string' => FILTER_SANITIZE_STRING,
        'int' => FILTER_SANITIZE_NUMBER_INT,
        'float' => [FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION],
        'validate_email' => FILTER_SANITIZE_EMAIL,
        'validate_url' => FILTER_SANITIZE_URL
    ];

    /**
     * @var array
     */
    protected static $filterValidate = [
        'boolean' => FILTER_VALIDATE_BOOLEAN,
        'validate_email' => FILTER_VALIDATE_EMAIL,
        'float' => FILTER_VALIDATE_FLOAT,
        'int' => FILTER_VALIDATE_INT,
        'validate_ip' => FILTER_VALIDATE_IP,
        'validate_mac_address' => FILTER_VALIDATE_MAC,
        'validate_regexp' => FILTER_VALIDATE_REGEXP,
        'validate_url' => FILTER_VALIDATE_URL,
        'callback' => FILTER_CALLBACK
    ];

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var array
     */
    private $error = [];

    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * Validator constructor.
     * @param ModelInterface $model
     */
    public function __construct(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if ($this->model->sanitizeData() === true) {
            $this->sanitize();
        }
        $this->validateData();
        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @param null $attr
     * @return array|mixed|null
     */
    public function getErrors($attr = null)
    {
        if ($attr === null) {
            return $this->error;
        }
        return (isset($this->error[$attr])) ? $this->error[$attr] : null;
    }

    /**
     * @return void
     */
    private function validateData()
    {
        foreach ($this->model->rules() as $attr => $rules) {
            if (isset($rules[2])
                && ($this->model->$attr === null || $this->model->$attr == '')
            ) {
                $this->noValid($attr);
            } elseif (array_key_exists($rules[0], self::$filterValidate)) {
                $valueValid = filter_var(
                    $this->model->$attr,
                    self::$filterValidate[$rules[0]],
                    $rules[1]
                );
                if ($valueValid === null
                    || (self::$filterValidate[$rules[0]] != 'boolean'
                        && ($valueValid === false 
                            || (is_array($valueValid)
                                && in_array(false, $valueValid, true)
                            )
                        )
                    )
                ) {
                    $this->noValid($attr);
                } else {
                    $this->model->$attr = $valueValid;
                }
            }
        }
        if ($this->isValid === null) {
            $this->isValid = true;
        }
    }

    /**
     * @param $attr
     * @return void
     */
    private function noValid($attr)
    {
        $this->isValid = false;
        $this->error[$attr] = $attr .' is not valid!';
    }

    /**
     * @return void
     */
    private function sanitize()
    {
        foreach ($this->model->rules() as $attr => $rules) {
            $filterBy = (isset(self::$filterSanitize[$rules[0]]))
                ? self::$filterSanitize[$rules[0]]
                : self::$filterSanitize['string'];
            $this->model->$attr = filter_var(
                $this->model->$attr,
                $filterBy
            );
        }
    }
}