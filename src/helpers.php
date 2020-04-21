<?php

use Hipsterjazzbo\Editor\Editor;

if (! function_exists('s')) {
    /**
     * @param string $str
     * @param string $encoding
     *
     * @return Editor
     */
    function s(string $str, string $encoding = 'UTF-8'): Editor
    {
        return Editor::create($str, $encoding);
    }
}

if (! function_exists('mb_sprintf')) {
    /**
     * @param string $format
     * @param mixed ...$substitutions
     * @return string
     */
    function mb_sprintf(string $format, ...$substitutions): string
    {
        return Editor::sprintf($format, ...$substitutions)->str();
    }
}
if (! function_exists('mb_vsprintf')) {
    /**
     * Works with all encodings in format and arguments.
     * Supported: Sign, padding, alignment, width and precision.
     * Not supported: Argument swapping.
     *
     * @param string $format
     * @param array $substitutions
     * @param string|null $encoding
     * @return string
     */
    function mb_vsprintf(string $format, array $substitutions, ?string $encoding = null): string
    {
        return Editor::vsprintf($format, $substitutions, $encoding)->str();
    }
}
