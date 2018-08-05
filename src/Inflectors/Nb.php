<?php

namespace Placemat\Editor\Inflectors;

/**
 * Norwegian Bokmal inflection rules.
 */
class Nb extends Inflector
{
    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement
     *
     * @return array
     */
    public function pluralRules(): array
    {
        return [
            '/e$/iu' => 'er',
            '/r$/iu' => 're',
            '/$/' => 'er',
        ];
    }

    /**
     * Return an array of singularization rules, from most to least specific, in the form $rule => $replacement
     *
     *
     * @return array
     */
    public function singularRules(): array
    {
        return [
            '/re$/iu' => 'r',
            '/er$/iu' => '',
        ];
    }

    /**
     * Return an array of irregular replacements, in the form singular => plural ('goose' => 'geese')
     *
     * @return array
     */
    public function irregularRules(): array
    {
        return [
            'konto' => 'konti',
        ];
    }

    /**
     * Return an array of uncountable rules (sheep, police)
     *
     * @return array
     */
    public function uncountableRules(): array
    {
        return [
            'barn',
            'fjell',
            'hus',
        ];
    }
}
