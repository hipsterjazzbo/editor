<?php

namespace Hipsterjazzbo\Editor\Inflectors;

/**
 * Spanish inflection rules.
 */
class Es extends Inflector
{
    /**
     * Return the ISO-639 two-letter language code
     *
     * @return string
     */
    static public function getLanguageCode(): string
    {
        return 'es';
    }

    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement
     *
     * @return array
     */
    public static function pluralRules(): array
    {
        return [
            '/ú([sn])$/iu'     => 'u\1es',
            '/ó([sn])$/iu'     => 'o\1es',
            '/í([sn])$/iu'     => 'i\1es',
            '/é([sn])$/iu'     => 'e\1es',
            '/á([sn])$/iu'     => 'a\1es',
            '/z$/iu'           => 'ces',
            '/([aeiou]s)$/iu'  => '\1',
            '/([^aeéiou])$/iu' => '\1es',
            '/$/'             => 's',
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
            '/ereses$/' => 'erés',
            '/iones$/'  => 'ión',
            '/ces$/'    => 'z',
            '/es$/'     => '',
            '/s$/'      => '',
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
            'el'           => 'los',
            'lunes'        => 'lunes',
            'rompecabezas' => 'rompecabezas',
            'crisis'       => 'crisis',
            'papá'         => 'papás',
            'mamá'         => 'mamás',
            'sofá'         => 'sofás',
            // because 'mes' is considered already a plural
            'mes'          => 'meses',
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
