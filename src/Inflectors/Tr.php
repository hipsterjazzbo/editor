<?php

namespace Placemat\Editor\Inflectors;

/**
 * Turkish inflection rules.
 */
class Tr extends Inflector
{
    /**
     * Return the ISO-639 two-letter language code
     *
     * @return string
     */
    static public function getLanguageCode(): string
    {
        return 'tr';
    }

    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement
     *
     * @return array
     */
    public static function pluralRules(): array
    {
        return [
            '/([eöiü][^aoıueöiü]{0,6})$/u' => '\1ler',
            '/([aoıu][^aoıueöiü]{0,6})$/u' => '\1lar',
        ];
    }

    /**
     * Return an array of singularization rules, from most to least specific, in the form $rule => $replacement
     *
     *
     * @return array
     */
    public static function singularRules(): array
    {
        return [
            '/l[ae]r$/iu' => '',
        ];
    }

    /**
     * Return an array of irregular replacements, in the form singular => plural ('goose' => 'geese')
     *
     * @return array
     */
    public static function irregularRules(): array
    {
        return [
            'ben' => 'biz',
            'sen' => 'siz',
            'o'   => 'onlar',
        ];
    }

    /**
     * Return an array of uncountable rules (sheep, police)
     *
     * @return array
     */
    public static function uncountableRules(): array
    {
        return [];
    }
}
