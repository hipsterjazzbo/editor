<?php

namespace Hipsterjazzbo\Editor\Inflectors;

abstract class Inflector
{
    /**
     * @var string
     */
    protected $str;

    /**
     * @var array
     */
    protected static $pluralRules = [];

    /**
     * @var array
     */
    protected static $singularRules = [];

    /**
     * @var array
     */
    protected static $irregularRules = [];

    /**
     * @var array
     */
    protected static $uncountableRules = [];

    /**
     * @var array
     */
    protected static $pluralCache = [];

    /**
     * @var array
     */
    protected static $singularCache = [];

    /**
     * Return the ISO-639 two-letter language code
     *
     * @return string
     */
    abstract public static function getLanguageCode(): string;

    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement
     *
     * @return array
     */
    abstract public static function pluralRules(): array;

    /**
     * Return an array of singularization rules, from most to least specific, in the form $rule => $replacement
     *
     *
     * @return array
     */
    abstract public static function singularRules(): array;

    /**
     * Return an array of irregular replacements, in the form singular => plural ('goose' => 'geese')
     *
     * @return array
     */
    abstract public static function irregularRules(): array;

    /**
     * Return an array of uncountable rules (sheep, police)
     *
     * @return array
     */
    abstract public static function uncountableRules(): array;
    /**
     * Inflector constructor.
     *
     * @param string $str
     * @param string $encoding
     */
    public function __construct(string $str, string $encoding = 'UTF-8')
    {
        $this->str = mb_strtolower($str, $encoding);
    }

    /**
     * @return bool
     */
    public function isCountable(): bool
    {
        return ! $this->isUncountable();
    }

    /**
     * @return bool
     */
    public function isUncountable(): bool
    {
        return in_array($this->str, static::getUncountableRules());
    }

    /**
     * @return string
     */
    public function pluralize(): string
    {
        return $this->inflect(static::$pluralCache, static::getPluralRules(), static::getIrregularRules());
    }

    /**
     * @return string
     */
    public function singularize(): string
    {
        return $this->inflect(static::$singularCache, static::getSingularRules(), array_flip(static::getIrregularRules()));
    }

    /**
     * @param $cache
     * @param $inflectionRules
     * @param $irregularRules
     *
     * @return mixed|null|string|string[]
     */
    protected function inflect(&$cache, $inflectionRules, $irregularRules)
    {
        //if (array_key_exists($this->str, $cache)) {
        //    return $cache[$this->str];
        //}

        if ($this->isUncountable()) {
            return $this->str;
        }

        if (array_key_exists($this->str, $irregularRules)) {
            return $irregularRules[$this->str];
        }

        foreach ($inflectionRules as $rule => $replacement) {
            if (preg_match($rule, $this->str)) {
                return self::$singularCache[$this->str] = preg_replace($rule, $replacement, $this->str);
            }
        }

        return $this->str;
    }

    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement,
     * merged with any custom rules that have been set.
     *
     * @return array
     */
    public static function getPluralRules(): array
    {
        return array_merge(static::pluralRules(), static::$pluralRules);
    }

    /**
     * Return an array of singularization rules, from most to least specific, in the form $rule => $replacement,
     * merged with any custom rules that have been set.
     *
     *
     * @return array
     */
    public static function getSingularRules(): array
    {
        return array_merge(static::singularRules(), static::$singularRules);
    }

    /**
     * Return an array of irregular replacements, in the form singular => plural ('goose' => 'geese'),
     * merged with any custom rules that have been set.
     *
     * @return array
     */
    public static function getIrregularRules(): array
    {
        return array_merge(static::irregularRules(), static::$irregularRules);
    }

    /**
     * Return an array of uncountable rules (sheep, police),
     * merged with any custom rules that have been set.
     *
     * @return array
     */
    public static function getUncountableRules(): array
    {
        return array_merge(static::uncountableRules(), static::$uncountableRules);
    }

    /**
     * Add custom pluralization rules, from most to least specific, in the form $rule => $replacement.
     *
     * @param array $rules
     *
     * @return void
     */
    public static function addPluralRules(array $rules): void
    {
        static::$pluralRules = array_merge(static::$pluralRules, $rules);
    }

    /**
     * Add custom singularization rules, from most to least specific, in the form $rule => $replacement.
     *
     *
     * @param array $rules
     *
     * @return void
     */
    public static function addSingularRules(array $rules): void
    {
        static::$singularRules = array_merge(static::$singularRules, $rules);
    }

    /**
     * Add custom irregular rules, in the form singular => plural ('goose' => 'geese').
     *
     * @param array $rules
     *
     * @return void
     */
    public static function addIrregularRules(array $rules): void
    {
        static::$irregularRules = array_merge(static::$irregularRules, $rules);
    }

    /**
     * Add custom uncountable rules (sheep, police).
     *
     * @param array $rules
     *
     * @return void
     */
    public static function addUncountableRules(array $rules): void
    {
        static::$uncountableRules = array_merge(static::$uncountableRules, $rules);
    }
}
