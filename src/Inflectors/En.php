<?php

namespace Placemat\Editor\Inflectors;

/**
 * English inflection rules.
 */
class En extends Inflector
{
    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement
     *
     * @return array
     */
    public static function pluralRules(): array
    {
        return [
            '/(quiz)$/iu'                           => '\1zes',
            '/^(oxen)$/iu'                          => '\1',
            '/^(ox)$/iu'                            => '\1en',
            '/^(m|l)ice$/iu'                        => '\1ice',
            '/^(m|l)ouse$/iu'                       => '\1ice',
            '/(matr|vert|ind)(?:ix|ex)$/iu'         => '\1ices',
            '/(x|ch|ss|sh)$/iu'                     => '\1es',
            '/([^aeiouy]|qu)y$/iu'                  => '\1ies',
            '/(hive)$/iu'                           => '\1s',
            '/(?:([^f])fe|([lr])f)$/iu'             => '\1\2ves',
            '/sis$/iu'                              => 'ses',
            '/([ti])a$/iu'                          => '\1a',
            '/([ti])um$/iu'                         => '\1a',
            '/(buffal|tomat|potat|volcan|her)o$/iu' => '\1oes',
            '/(bu)s$/iu'                            => '\1ses',
            '/(alias|status)$/iu'                   => '\1es',
            '/^(ax|test)is$/iu'                     => '\1es',
            '/s$/iu'                                => 's',
            '/$/'                                  => 's',
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
            '/(database)s$/iu'                                                        => '\1',
            '/(quiz)zes$/iu'                                                          => '\1',
            '/(matr)ices$/iu'                                                         => '\1ix',
            '/(vert|ind)ices$/iu'                                                     => '\1ex',
            '/^(ox)en/iu'                                                             => '\1',
            '/(alias|status)(es)?$/iu'                                                => '\1',
            '/^(a)x[ie]s$/iu'                                                         => '\1xis',
            '/(cris|test)(is|es)$/iu'                                                 => '\1is',
            '/(shoe)s$/iu'                                                            => '\1',
            '/(o)es$/iu'                                                              => '\1',
            '/(bus)(es)?$/iu'                                                         => '\1',
            '/^(m|l)ice$/iu'                                                          => '\1ouse',
            '/(x|ch|ss|sh)es$/iu'                                                     => '\1',
            '/(m)ovies$/iu'                                                           => '\1ovie',
            '/(s)eries$/iu'                                                           => '\1eries',
            '/([^aeiouy]|qu)ies$/iu'                                                  => '\1y',
            '/([lr])ves$/iu'                                                          => '\1f',
            '/(tive)s$/iu'                                                            => '\1',
            '/(hive)s$/iu'                                                            => '\1',
            '/([^f])ves$/iu'                                                          => '\1fe',
            '/(^analy)(sis|ses)$/iu'                                                  => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$/iu' => '\1sis',
            '/([ti])a$/iu'                                                            => '\1um',
            '/(n)ews$/iu'                                                             => '\1ews',
            '/(ss)$/iu'                                                               => '\1',
            '/s$/iu'                                                                  => '',
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
            'leaf'    => 'leaves',
            'loaf'    => 'loaves',
            'octopus' => 'octopuses',
            'virus'   => 'viruses',
            'person'  => 'people',
            'man'     => 'men',
            'child'   => 'children',
            'sex'     => 'sexes',
            'move'    => 'moves',
            'zombie'  => 'zombies',
            'goose'   => 'geese',
            'genus'   => 'genera',
        ];
    }

    /**
     * Return an array of uncountable rules (sheep, police)
     *
     * @return array
     */
    public static function uncountableRules(): array
    {
        return [
            'advice',
            'aircraft',
            'art',
            'baggage',
            'butter',
            'clothing',
            'coal',
            'cotton',
            'deer',
            'equipment',
            'experience',
            'feedback',
            'fish',
            'flour',
            'food',
            'furniture',
            'gas',
            'homework',
            'impatience',
            'information',
            'jeans',
            'knowledge',
            'leather',
            'love',
            'luggage',
            'management',
            'money',
            'moose',
            'music',
            'news',
            'oil',
            'patience',
            'police',
            'polish',
            'progress',
            'research',
            'rice',
            'salmon',
            'sand',
            'series',
            'sheep',
            'silk',
            'sms',
            'soap',
            'spam',
            'species',
            'staff',
            'sugar',
            'swine',
            'talent',
            'toothpaste',
            'traffic',
            'travel',
            'vinegar',
            'weather',
            'wood',
            'wool',
            'work',
        ];
    }
}
