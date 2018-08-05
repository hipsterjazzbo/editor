<?php

use Placemat\Editor\Editor;

if ( ! function_exists('s')) {
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