<?php

namespace Hipsterjazzbo\Editor\Inflectors;

/**
 * Portuguese inflection rules.
 */
class Pt extends Inflector
{
    /**
     * Return the ISO-639 two-letter language code
     *
     * @return string
     */
    static public function getLanguageCode(): string
    {
        return 'pt';
    }

    /**
     * Return an array of pluralization rules, from most to least specific, in the form $rule => $replacement
     *
     * @return array
     */
    public static function pluralRules(): array
    {
        return [
            '/^(alem|c|p)ao$/iu'                                 => '\1aes',
            '/^(irm|m)ao$/iu'                                    => '\1aos',
            '/ao$/iu'                                            => 'oes',
            '/^(alem|c|p)ão$/iu'                                 => '\1ães',
            '/^(irm|m)ão$/iu'                                    => '\1ãos',
            '/ão$/iu'                                            => 'ões',
            '/^(|g)ás$/iu'                                       => '\1ases',
            '/^(japon|escoc|ingl|dinamarqu|fregu|portugu)ês$/iu' => '\1eses',
            '/m$/iu'                                             => 'ns',
            '/([^aeou])il$/iu'                                   => '\1is',
            '/ul$/iu'                                            => 'uis',
            '/ol$/iu'                                            => 'ois',
            '/el$/iu'                                            => 'eis',
            '/al$/iu'                                            => 'ais',
            '/(z|r)$/iu'                                         => '\1es',
            '/(s)$/iu'                                           => '\1',
            '/$/'                                               => 's',
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
            '/^(g|)ases$/iu'                                      => '\1ás',
            '/(japon|escoc|ingl|dinamarqu|fregu|portugu)eses$/iu' => '\1ês',
            '/(ae|ao|oe)s$/'                                     => 'ao',
            '/(ãe|ão|õe)s$/'                                     => 'ão',
            '/^(.*[^s]s)es$/iu'                                   => '\1',
            '/sses$/iu'                                           => 'sse',
            '/ns$/iu'                                             => 'm',
            '/(r|t|f|v)is$/iu'                                    => '\1il',
            '/uis$/iu'                                            => 'ul',
            '/ois$/iu'                                            => 'ol',
            '/eis$/iu'                                            => 'ei',
            '/éis$/iu'                                            => 'el',
            '/([^p])ais$/iu'                                      => '\1al',
            '/(r|z)es$/iu'                                        => '\1',
            '/^(á|gá)s$/iu'                                       => '\1s',
            '/([^ê])s$/iu'                                        => '\1',
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
            'abdomen'   => 'abdomens',
            'alemão'    => 'alemães',
            'artesã'    => 'artesãos',
            'álcool'    => 'álcoois',
            "árvore"    => "árvores",
            'bencão'    => 'bencãos',
            'cão'       => 'cães',
            'campus'    => 'campi',
            "cadáver"   => "cadáveres",
            'capelão'   => 'capelães',
            'capitão'   => 'capitães',
            'chão'      => 'chãos',
            'charlatão' => 'charlatães',
            'cidadão'   => 'cidadãos',
            'consul'    => 'consules',
            'cristão'   => 'cristãos',
            'difícil'   => 'difíceis',
            'email'     => 'emails',
            'escrivão'  => 'escrivães',
            'fóssil'    => 'fósseis',
            'gás'       => 'gases',
            'germens'   => 'germen',
            'grão'      => 'grãos',
            'hífen'     => 'hífens',
            'irmão'     => 'irmãos',
            'liquens'   => 'liquen',
            'mal'       => 'males',
            'mão'       => 'mãos',
            'orfão'     => 'orfãos',
            'país'      => 'países',
            'pai'       => 'pais',
            'pão'       => 'pães',
            'projétil'  => 'projéteis',
            'réptil'    => 'répteis',
            'sacristão' => 'sacristães',
            'sotão'     => 'sotãos',
            'tabelião'  => 'tabeliães',
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
            'tórax',
            'tênis',
            'ônibus',
            'lápis',
            'fênix',
        ];
    }
}
