<?php

namespace Hipsterjazzbo\Editor;

use InvalidArgumentException;
use Hipsterjazzbo\Editor\Inflectors;

class Editor
{
    /**
     * @var string
     */
    protected $str;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @var array
     */
    protected static $inflectors = [
        'en' => Inflectors\En::class,
        'es' => Inflectors\Es::class,
        'fr' => Inflectors\Fr::class,
        'nb' => Inflectors\Nb::class,
        'pt' => Inflectors\Pt::class,
        'tr' => Inflectors\Tr::class,
    ];

    /**
     * Editor constructor.
     *
     * @param string $str
     * @param string $encoding
     */
    public function __construct(string $str, string $encoding = 'UTF-8')
    {
        $this->str = $str;

        $this->encoding = $encoding;
    }

    /**
     * Create a new instance of Editor
     *
     * @param string $str
     * @param string $encoding
     *
     * @return Editor
     */
    public static function create(string $str, string $encoding = 'UTF-8'): self
    {
        return new static($str, $encoding);
    }

    /**
     * Create a new instance of Editor from an array of strings, joined by $separator and trimmed.
     *
     * @param array $strs
     * @param string $joinedBy
     * @param string $encoding
     *
     * @return Editor
     */
    public static function createFromArray(array $strs, string $joinedBy = ' ', string $encoding = 'UTF-8'): self
    {
        $strs = array_map(function ($str) use ($encoding) {
            return static::create($str, $encoding)->trim();
        }, $strs);

        $str = implode($joinedBy, $strs);

        return static::create($str, $encoding)->trim();
    }

    /**
     * Get the encoding that's being used
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return Editor
     */
    public function setEncoding(string $encoding = 'UTF-8'): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->str();
    }

    /**
     * @return string
     */
    public function str()
    {
        return $this->str;
    }

    /////////////////////////////////////////
    /// Slice and Manipulate
    /////////////////////////////////////////

    /**
     * Get the contents of $str after $search
     *
     * @param string $search
     *
     * @return Editor
     */
    public function after(string $search): self
    {
        if ($search === '') {
            return $this;
        }

        $str = array_reverse(explode($search, $this->str, 2))[0];

        return static::create($str);
    }

    /**
     * Get the contents of $str before $search
     *
     * @param string $search
     *
     * @return Editor
     */
    public function before(string $search): self
    {
        if ($search === '') {
            return $this;
        }

        $str = explode($search, $this->str)[0];

        return static::create($str);
    }

    /**
     * Prepend $str
     *
     * @param string $str
     * @param string $joinedBy
     *
     * @return $this
     */
    public function prepend(string $str, string $joinedBy = ''): self
    {
        return new static($str.$joinedBy.$this->str);
    }

    /**
     * Append $str
     *
     * @param string $str
     * @param string $joinedBy
     *
     * @return $this
     */
    public function append(string $str, string $joinedBy = ''): self
    {
        return new static($this->str.$joinedBy.$str);
    }

    /**
     * Prepend $prefix to $str
     *
     * @param string $prefix
     *
     * @return Editor
     */
    public function startWith(string $prefix): self
    {
        $quoted = preg_quote($prefix, '/');

        $str = $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $this->str);

        return static::create($str);
    }

    /**
     * Append $suffix to $str
     *
     * @param string $suffix
     *
     * @return Editor
     */
    public function finishWith(string $suffix): self
    {
        $quoted = preg_quote($suffix, '/');

        $str = preg_replace('/(?:'.$quoted.')+$/u', '', $this->str).$suffix;

        return static::create($str);
    }

    /**
     * Limit the length of $str to N $characters, including $suffix
     *
     * @param int $characters
     * @param string $suffix
     *
     * @return Editor
     */
    public function limitCharacters(int $characters, string $suffix = '…'): self
    {
        if (mb_strwidth($this->str, 'UTF-8') <= $characters) {
            return $this;
        }

        // Make sure the final string will fit within the limit even with the suffix
        $trimLength = $characters - static::create($suffix)->length();

        $str = rtrim(mb_strimwidth($this->str, 0, $trimLength, '', 'UTF-8')).$suffix;

        return static::create($str);
    }

    /**
     * Limit the length of $str to N $words
     *
     * @param int $words
     * @param string $suffix
     *
     * @return Editor
     */
    public function limitWords(int $words, string $suffix = '…'): self
    {
        $matches = [];

        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $this->str, $matches);

        if (! isset($matches[0]) || mb_strlen($this->str) === mb_strlen($matches[0])) {
            return $this;
        }

        $str = rtrim($matches[0]).$suffix;

        return static::create($str);
    }

    /**
     * Pluralize $str
     *
     * @param float $count
     * @param string $language
     *
     * @return Editor
     * @throws \Exception
     */
    public function plural(float $count = 2, string $language = 'en'): self
    {
        if ($count == 1) {
            return $this->singular($language);
        }

        return static::create($this->getInflector($language)->pluralize());
    }

    /**
     * Singularize $str
     *
     * @param string $language
     *
     * @return Editor
     * @throws \Exception
     */
    public function singular(string $language = 'en'): self
    {
        return static::create($this->getInflector($language)->singularize());
    }

    /**
     * Replace occurrences of $search with $replacement, up to $count times
     *
     * @param string $search
     * @param string $replacement
     * @param int|null $count
     *
     * @return Editor
     */
    public function replace(string $search, string $replacement, int &$count = null): self
    {
        $str = str_replace($search, $replacement, $this->str, $count);

        return static::create($str);
    }

    /**
     * Replace only the first occurrence of $search with $replacement
     *
     * @param string $search
     * @param string $replacement
     *
     * @return Editor
     */
    public function replaceFirst(string $search, string $replacement): self
    {
        if ($search === '') {
            return $this;
        }

        $start = mb_strpos($this->str, $search);

        if ($start === false) {
            return $this;
        }

        $str = $this->replaceSub($replacement, $start, static::create($search)->length());

        return static::create($str);
    }

    /**
     * Replace only the last occurrence of $search with $replacement
     *
     * @param string $search
     * @param string $replacement
     *
     * @return Editor
     */
    public function replaceLast(string $search, string $replacement): self
    {
        $start = mb_strrpos($this->str, $search);

        if ($search === '' || $start === false) {
            return $this;
        }

        $str = $this->replaceSub($replacement, $start, static::create($search)->length());

        return static::create($str);
    }

    /**
     * Replace within $str from the $start character plus $length characters
     *
     * @param string $replacement
     * @param int $start
     * @param int|null $length
     *
     * @return Editor
     */
    public function replaceSub(string $replacement, int $start, int $length = null): self
    {
        preg_match_all('/./us', $this->str, $strMatches);

        preg_match_all('/./us', $replacement, $replacementMatches);

        if ($length === null) {
            $length = $this->length();
        }

        array_splice($strMatches[0], $start, $length, $replacementMatches[0]);

        $str = join($strMatches[0]);

        return static::create($str);
    }

    /**
     * Remove occurrences of $search, up to $count times
     *
     * @param string $search
     * @param int|null $count
     *
     * @return Editor
     */
    public function remove(string $search, int &$count = null): self
    {
        return $this->replace($search, '', $count);
    }

    /**
     * Remove only the first occurrence of $search
     *
     * @param string $search
     *
     * @return Editor
     */
    public function removeFirst(string $search): self
    {
        return $this->replaceFirst($search, '');
    }

    /**
     * Remove only the last occurrence of $search
     *
     * @param string $search
     *
     * @return Editor
     */
    public function removeLast(string $search): self
    {
        return $this->replaceLast($search, '');
    }

    /**
     * Transform $str into a url-safe slug
     *
     * @param string $separator
     * @param string $language
     *
     * @return Editor
     */
    public function slug(string $separator = '-', string $language = 'en'): self
    {
        $str = $this->ascii($language);

        // Convert all dashes/underscores into $separator
        $flip = $separator === '-' ? '_' : '-';

        $str = preg_replace('!['.preg_quote($flip).']+!u', $separator, $str);

        // Replace @ with the word 'at'
        $str = str_replace('@', $separator.'at'.$separator, $str);

        // Lowercase it
        $str = static::create($str)->lowerCase()->str();

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $str = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', $str);

        // Replace all separator characters and whitespace by a single separator
        $str = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $str);

        return static::create($str)->trim($separator);
    }

    /**
     * Break $str into chunks
     *
     * @param int $chunkSize
     *
     * @return Editor[]
     * @throws \Exception
     */
    public function chunk(int $chunkSize = 1): array
    {
        if ($chunkSize <= 0) {
            throw new \Exception(
                'The length of each segment must be greater than zero'
            );
        }

        $result = [];

        $length = $this->length();

        for ($ii = 0; $ii < $length; $ii += $chunkSize) {
            $result[] = $this->slice($ii, $chunkSize);
        }

        if (empty($result)) {
            return [static::create('')];
        }

        return $result;
    }

    /**
     * Unicode safely split $str into an array of words.
     *
     * @see https://stackoverflow.com/a/8422356/714260
     *
     * @return Editor[]
     */
    public function splitWords(): array
    {
        preg_match_all('/[\p{L}\p{M}]+/u', $this->str, $result, PREG_PATTERN_ORDER);

        return array_map(function ($str) {
            return static::create($str);
        }, $result[0]);
    }

    /**
     * Get the first word of $str
     *
     * @return Editor
     */
    public function firstWord(): self
    {
        return static::create($this->splitWords()[0]);
    }

    /**
     * Get the last word of $str
     *
     * @return Editor
     */
    public function lastWord(): self
    {
        $words = $this->splitWords();

        return static::create(end($words));
    }

    /**
     * Slice $str from $start, with optional $length
     *
     * @param int $start
     * @param int|null $length
     *
     * @return Editor
     */
    public function slice(int $start, int $length = null): self
    {
        $str = mb_substr($this->str, $start, $length, $this->encoding);

        return static::create($str);
    }

    /**
     * Trim characters from $charList from the beginning and end of $str
     *
     * @param string $charList
     *
     * @return Editor
     */
    public function trim($charList = " \t\n\r\0\x0B"): self
    {
        $str = trim($this->str, $charList);

        return static::create($str);
    }

    public function ltrim($charList = " \t\n\r\0\x0B"): self
    {
        $str = ltrim($this->str, $charList);

        return static::create($str);
    }

    public function rtrim($charList = " \t\n\r\0\x0B"): self
    {
        $str = rtrim($this->str, $charList);

        return static::create($str);
    }

    public function base64encode(): self
    {
        $str = base64_encode($this->str);

        return static::create($str);
    }

    /////////////////////////////////////////
    /// Search and Query
    /////////////////////////////////////////

    /**
     * Determine whether $str contains $search
     *
     * @param string $search
     *
     * @return bool
     */
    public function contains(string $search): bool
    {
        return $search !== '' && mb_strpos($this->str, $search) !== false;
    }

    /**
     * Determine whether $str starts with $search
     *
     * @param string $search
     *
     * @return bool
     */
    public function startsWith(string $search): bool
    {
        $searchLength = static::create($search)->length();

        return $search !== '' && mb_substr($this->str, 0, $searchLength) === $search;
    }

    /**
     * Determine whether $str end with $search
     *
     * @param string $search
     *
     * @return bool
     */
    public function endsWith(string $search): bool
    {
        $searchLength = static::create($search)->length();

        return $search !== '' && mb_substr($this->str, -$searchLength) === $search;
    }

    /**
     * Determine whether $str matches a regular expression
     *
     * @param string $pattern
     *
     * @return bool
     */
    public function matches(string $pattern): bool
    {
        // If the given value is an exact match we can of course return true right
        // from the beginning. Otherwise, we will translate asterisks and do an
        // actual pattern match against the two strings to see if they match.
        if ($pattern == $this->str) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return preg_match('#^'.$pattern.'\z#u', $this->str) === 1;
    }

    /**
     * Get length of $str
     *
     * @return int
     */
    public function length(): int
    {
        return mb_strlen($this->str, $this->encoding);
    }

    /////////////////////////////////////////
    /// Casing
    /////////////////////////////////////////

    /**
     * Transform $str to lower case
     *
     * @return Editor
     */
    public function lowerCase(): self
    {
        $str = mb_strtolower($this->str, $this->encoding);

        return static::create($str);
    }

    /**
     * Transform the first character of $str to lower case
     *
     * @return Editor
     */
    public function lowerCaseFirst(): self
    {
        $str = $this->slice(0, 1)->lowerCase().$this->slice(1);

        return static::create($str);
    }

    /**
     * Transform the first character of each word of $str to lower case
     *
     * @param string $delimiters
     *
     * @return Editor
     * @throws \Exception
     */
    public function lowerCaseWords($delimiters = " \t\r\n\f\v")
    {
        $delimitersArray = static::create($delimiters)->chunk(1);

        $upper = true;

        $str = '';

        for ($ii = 0; $ii < $this->length(); $ii++) {
            $char = $this->slice($ii, 1);

            if ($upper) {
                $char = mb_convert_case($char, MB_CASE_LOWER, $this->encoding);

                $upper = false;
            } elseif (in_array($char, $delimitersArray)) {
                $upper = true;
            }

            $str .= $char;
        }

        return static::create($str);
    }

    /**
     * Transform $str ot upper case
     *
     * @return Editor
     */
    public function upperCase(): self
    {
        $str = mb_strtoupper($this->str, $this->encoding);

        return static::create($str);
    }

    /**
     * Transform the first character of $str to upper case
     *
     * @return Editor
     */
    public function upperCaseFirst(): self
    {
        $str = $this->slice(0, 1)->upperCase().$this->slice(1);

        return static::create($str);
    }

    /**
     * Transform the first character of each word of $str to upper case
     *
     * @param array $delimiters
     *
     * @return Editor
     * @throws \Exception
     */
    public function upperCaseWords($delimiters = [" ", "\t", "\r", "\n", "\f", "\v"]): self
    {
        $upper = true;

        $str = '';

        for ($ii = 0; $ii < $this->length(); $ii++) {
            $char = $this->slice($ii, 1);

            if ($upper) {
                $char = mb_convert_case($char, MB_CASE_UPPER, $this->encoding);

                $upper = false;
            } elseif (in_array($char, $delimiters)) {
                $upper = true;
            }

            $str .= $char;
        }

        return static::create($str);
    }

    /**
     * Transform $str to proper title case
     *
     * @param array $ignore
     *
     * @return Editor
     */
    public function titleCase(array $ignore = []): self
    {
        $smallWords = array_merge(
            [
                '(?<!q&)a',
                'an',
                'and',
                'as',
                'at(?!&t)',
                'but',
                'by',
                'en',
                'for',
                'if',
                'in',
                'of',
                'on',
                'or',
                'the',
                'to',
                'v[.]?',
                'via',
                'vs[.]?',
                'with',
            ],
            $ignore
        );

        $smallWordsRx = implode('|', $smallWords);

        $apostropheRx = '(?x: [\'’] [[:lower:]]* )?';

        $str = $this->trim();

        if (preg_match('/[[:lower:]]/', $str) === 0) {
            $str = $str->lowerCase()->str();
        }

        // The main substitutions
        $str = preg_replace_callback(
            '~\b (_*) (?:                                                          # 1. Leading underscore and
                        ( (?<=[ ][/\\\\]) [[:alpha:]]+ [-_[:alpha:]/\\\\]+ |              # 2. file path or 
                          [-_[:alpha:]]+ [@.:] [-_[:alpha:]@.:/]+ '.$apostropheRx.' ) #    URL, domain, or email
                        |
                        ( (?i: '.$smallWordsRx.' ) '.$apostropheRx.' )             # 3. or small word (case-insensitive)
                        |
                        ( [[:alpha:]] [[:lower:]\'’()\[\]{}]* '.$apostropheRx.' )     # 4. or word w/o internal caps
                        |
                        ( [[:alpha:]] [[:alpha:]\'’()\[\]{}]* '.$apostropheRx.' )     # 5. or some other word
                      ) (_*) \b                                                            # 6. With trailing underscore
                    ~ux',
            function ($matches) {
                // Preserve leading underscore
                $tmpStr = $matches[1];
                if ($matches[2]) {
                    // Preserve URLs, domains, emails and file paths
                    $tmpStr .= $matches[2];
                } elseif ($matches[3]) {
                    // Lower-case small words
                    $tmpStr .= static::create($matches[3])->lowerCase();
                } elseif ($matches[4]) {
                    // Capitalize word w/o internal caps
                    $tmpStr .= static::create($matches[4])->upperCaseFirst();
                } else {
                    // Preserve other kinds of word (iPhone)
                    $tmpStr .= $matches[5];
                }
                // Preserve trailing underscore
                $tmpStr .= $matches[6];

                return $tmpStr;
            },
            $str
        );

        // Exceptions for small words: capitalize at start of title...
        $str = preg_replace_callback(
            '~(  \A [[:punct:]]*           # start of title...
                      |  [:.;?!][ ]+               # or of sub-sentence...
                      |  [ ][\'"“‘(\[][ ]* )       # or of inserted sub-phrase...
                      ( '.$smallWordsRx.' ) \b # ...followed by small word
                     ~uxi',
            function ($matches) {
                return $matches[1].static::create($matches[2])->upperCaseFirst();
            },
            $str
        );

        // ...and end of title
        $str = preg_replace_callback(
            '~\b ( '.$smallWordsRx.' ) # small word...
                      (?= [[:punct:]]* \Z          # ...at the end of the title...
                      |   [\'"’”)\]] [ ] )         # ...or of an inserted sub-phrase?
                     ~uxi',
            function ($matches) {
                return static::create($matches[1])->upperCaseFirst();
            },
            $str
        );

        // Exceptions for small words in hyphenated compound words
        // e.g. "in-flight" -> In-Flight
        $str = preg_replace_callback(
            '~\b
                        (?<! -)                   # Negative lookbehind for a hyphen; we do not want to match man-in-the-middle but do want (in-flight)
                        ( '.$smallWordsRx.' )
                        (?= -[[:alpha:]]+)        # lookahead for "-someword"
                       ~uxi',
            function ($matches) {
                return static::create($matches[1])->upperCaseFirst();
            },
            $str
        );

        // e.g. "Stand-in" -> "Stand-In" (Stand is already capped at this point)
        $str = preg_replace_callback(
            '~\b
                      (?<!…)                    # Negative lookbehind for a hyphen; we do not want to match man-in-the-middle but do want (stand-in)
                      ( [[:alpha:]]+- )         # $1 = first word and hyphen, should already be properly capped
                      ( '.$smallWordsRx.' ) # ...followed by small word
                      (?!	- )                 # Negative lookahead for another -
                     ~uxi',
            function ($matches) {
                return $matches[1].static::create($matches[2])->upperCaseFirst();
            },
            $str
        );

        return static::create($str);
    }

    /**
     * Transform $str to camelCase
     *
     * @return Editor
     * @throws \Exception
     */
    public function camelCase(): self
    {
        return $this->studlyCase()->lowerCaseFirst();
    }

    /**
     * Transform $str to StudlyCase
     *
     * @return Editor
     * @throws \Exception
     */
    public function studlyCase(): self
    {
        return $this->replace('-', ' ')
            ->replace('_', ' ')
            ->upperCaseWords()
            ->remove(' ');
    }

    /**
     * Transform $str to snake_case
     *
     * @param string $delimiter
     *
     * @return Editor
     * @throws \Exception
     */
    public function snakeCase(string $delimiter = '_'): self
    {
        $str = preg_replace('/\s+/u', '', $this->upperCaseWords());

        $str = preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $str);

        return static::create($str)->lowerCase();
    }

    /**
     * Transform $str to kebab-case
     *
     * @return Editor
     * @throws \Exception
     */
    public function kebabCase(): self
    {
        return $this->snakeCase('-');
    }

    /////////////////////////////////////////
    /// Utilities
    /////////////////////////////////////////

    /**
     * Transform $str to be ascii-safe
     *
     * @param string $languageCode
     *
     * @return Editor
     */
    public function ascii(string $languageCode = 'en'): self
    {
        $str = $this;

        $languageCode = static::processLanguageCode($languageCode);

        $languageSpecific = static::languageSpecificCharsArray($languageCode);

        if (! is_null($languageSpecific)) {
            foreach ($languageSpecific as $search => $replace) {
                $str = $str->replace($search, $replace);
            }
        }

        foreach (static::charsArray() as $safeChar => $unsafeChars) {
            foreach ($unsafeChars as $unsafeChar) {
                $str = $str->replace($unsafeChar, $safeChar);
            }
        }

        $str = preg_replace('/[^\x20-\x7E]/u', '', $str->str());

        return static::create($str);
    }

    /**
     * @param string $format
     * @param mixed ...$substitutions
     * @return \Hipsterjazzbo\Editor\Editor
     */
    public static function sprintf(string $format, ...$substitutions): self
    {
        return static::vsprintf($format, $substitutions);
    }

    /**
     * @param string $format
     * @param array $substitutions
     * @param string|null $encoding
     * @return \Hipsterjazzbo\Editor\Editor
     */
    public static function vsprintf(string $format, array $substitutions, ?string $encoding = null): self
    {
        if (is_null($encoding)) {
            $encoding = mb_internal_encoding();
        }

        // Use UTF-8 in the format so we can use the u flag in preg_split
        $format = mb_convert_encoding($format, 'UTF-8', $encoding);

        // build a new format in UTF-8
        $newFormat = "";

        // unhandled args in unchanged encoding
        $newSubstitutions = [];

        while ($format !== "") {
            // Split the format in two parts: $pre and $post by the first %-directive
            // We get also the matched groups
            [
                $pre,
                $sign,
                $filler,
                $align,
                $size,
                $precision,
                $type,
                $post,
            ] = preg_split("!\%(\+?)('.|[0 ]|)(-?)([1-9][0-9]*|)(\.[1-9][0-9]*|)([%a-zA-Z])!u", $format, 2, PREG_SPLIT_DELIM_CAPTURE);

            $newFormat .= mb_convert_encoding($pre, $encoding, 'UTF-8');

            switch ($type) {
                case '':
                    // didn't match. do nothing. this is the last iteration.
                    break;

                case '%':
                    // an escaped %
                    $newFormat .= '%%';
                    break;

                case 's':
                    $substitution = array_shift($substitutions);

                    $substitution = mb_convert_encoding($substitution, 'UTF-8', $encoding);

                    $paddingPre = '';

                    $paddingPost = '';

                    // truncate $substitution
                    if ($precision !== '') {
                        $precision = intval(substr($precision, 1));

                        if ($precision > 0 && mb_strlen($substitution, $encoding) > $precision) {
                            $substitution = mb_substr($precision, 0, $precision, $encoding);
                        }
                    }

                    // define padding
                    if ($size > 0) {
                        $substitutionLength = mb_strlen($substitution, $encoding);

                        if ($substitutionLength < $size) {
                            if ($filler === '') {
                                $filler = ' ';
                            }

                            if ($align == '-') {
                                $paddingPost = str_repeat($filler, $size - $substitutionLength);
                            } else {
                                $paddingPre = str_repeat($filler, $size - $substitutionLength);
                            }
                        }
                    }

                    // escape % and pass it forward
                    $newFormat .= $paddingPre.str_replace('%', '%%', $substitution).$paddingPost;
                    break;

                default:
                    // another type, pass forward
                    $newFormat .= "%$sign$filler$align$size$precision$type";

                    $newSubstitutions[] = array_shift($substitutions);
                    break;
            }

            $format = strval($post);
        }

        // Convert new format back from UTF-8 to the original encoding
        $newFormat = mb_convert_encoding($newFormat, $encoding, 'UTF-8');

        return new static(vsprintf($newFormat, $newSubstitutions));
    }

    /**
     * Gets the Inflector for $language, if one exists.
     *
     * @param string $languageCode
     *
     * @return Inflectors\Inflector
     * @throws \Exception
     */
    protected function getInflector($languageCode): Inflectors\Inflector
    {
        $languageCode = static::processLanguageCode($languageCode);

        if (! array_key_exists($languageCode, static::$inflectors)) {
            throw new \InvalidArgumentException('No such language "'.$languageCode.'"');
        }

        if ($inflector = static::$inflectors[$languageCode]) {
            if (class_exists($inflector)) {
                $inflector = new $inflector($this->str, $this->encoding);

                return $inflector;
            }
        }

        throw new InvalidArgumentException($languageCode.' is an unsupported language');
    }

    /**
     * Registers a new inflector
     *
     * @param Inflectors\Inflector $inflector
     */
    public static function registerInflector(Inflectors\Inflector $inflector): void
    {
        $languageCode = static::processLanguageCode($inflector::getLanguageCode());

        static::$inflectors[$languageCode] = get_class($inflector);
    }

    /**
     * Returns the replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/3.1.0/LICENSE.txt
     *
     * @return array
     */
    protected static function charsArray(): array
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        // @formatter:off
        return $charsArray = [
            '0'    => ['°', '₀', '۰', '０'],
            '1'    => ['¹', '₁', '۱', '１'],
            '2'    => ['²', '₂', '۲', '２'],
            '3'    => ['³', '₃', '۳', '３'],
            '4'    => ['⁴', '₄', '۴', '٤', '４'],
            '5'    => ['⁵', '₅', '۵', '٥', '５'],
            '6'    => ['⁶', '₆', '۶', '٦', '６'],
            '7'    => ['⁷', '₇', '۷', '７'],
            '8'    => ['⁸', '₈', '۸', '８'],
            '9'    => ['⁹', '₉', '۹', '９'],
            'a'    => ['à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ā', 'ą', 'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ', 'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ', 'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', 'အ', 'ာ', 'ါ', 'ǻ', 'ǎ', 'ª', 'ა', 'अ', 'ا', 'ａ', 'ä'],
            'b'    => ['б', 'β', 'ب', 'ဗ', 'ბ', 'ｂ'],
            'c'    => ['ç', 'ć', 'č', 'ĉ', 'ċ', 'ｃ'],
            'd'    => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض', 'ဍ', 'ဒ', 'დ', 'ｄ'],
            'e'    => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ', 'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э', 'є', 'ə', 'ဧ', 'ေ', 'ဲ', 'ე', 'ए', 'إ', 'ئ', 'ｅ'],
            'f'    => ['ф', 'φ', 'ف', 'ƒ', 'ფ', 'ｆ'],
            'g'    => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ဂ', 'გ', 'گ', 'ｇ'],
            'h'    => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه', 'ဟ', 'ှ', 'ჰ', 'ｈ'],
            'i'    => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į', 'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ', 'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'і', 'ї', 'и', 'ဣ', 'ိ', 'ီ', 'ည်', 'ǐ', 'ი', 'इ', 'ی', 'ｉ'],
            'j'    => ['ĵ', 'ј', 'Ј', 'ჯ', 'ج', 'ｊ'],
            'k'    => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك', 'က', 'კ', 'ქ', 'ک', 'ｋ'],
            'l'    => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل', 'လ', 'ლ', 'ｌ'],
            'm'    => ['м', 'μ', 'م', 'မ', 'მ', 'ｍ'],
            'n'    => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن', 'န', 'ნ', 'ｎ'],
            'o'    => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'و', 'θ', 'ို', 'ǒ', 'ǿ', 'º', 'ო', 'ओ', 'ｏ', 'ö'],
            'p'    => ['п', 'π', 'ပ', 'პ', 'پ', 'ｐ'],
            'q'    => ['ყ', 'ｑ'],
            'r'    => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر', 'რ', 'ｒ'],
            's'    => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს', 'ｓ'],
            't'    => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ဋ', 'တ', 'ŧ', 'თ', 'ტ', 'ｔ'],
            'u'    => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ဉ', 'ု', 'ူ', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'უ', 'उ', 'ｕ', 'ў', 'ü'],
            'v'    => ['в', 'ვ', 'ϐ', 'ｖ'],
            'w'    => ['ŵ', 'ω', 'ώ', 'ဝ', 'ွ', 'ｗ'],
            'x'    => ['χ', 'ξ', 'ｘ'],
            'y'    => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي', 'ယ', 'ｙ'],
            'z'    => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز', 'ဇ', 'ზ', 'ｚ'],
            'aa'   => ['ع', 'आ', 'آ'],
            'ae'   => ['æ', 'ǽ'],
            'ai'   => ['ऐ'],
            'ch'   => ['ч', 'ჩ', 'ჭ', 'چ'],
            'dj'   => ['ђ', 'đ'],
            'dz'   => ['џ', 'ძ'],
            'ei'   => ['ऍ'],
            'gh'   => ['غ', 'ღ'],
            'ii'   => ['ई'],
            'ij'   => ['ĳ'],
            'kh'   => ['х', 'خ', 'ხ'],
            'lj'   => ['љ'],
            'nj'   => ['њ'],
            'oe'   => ['ö', 'œ', 'ؤ'],
            'oi'   => ['ऑ'],
            'oii'  => ['ऒ'],
            'ps'   => ['ψ'],
            'sh'   => ['ш', 'შ', 'ش'],
            'shch' => ['щ'],
            'ss'   => ['ß'],
            'sx'   => ['ŝ'],
            'th'   => ['þ', 'ϑ', 'ث', 'ذ', 'ظ'],
            'ts'   => ['ц', 'ც', 'წ'],
            'ue'   => ['ü'],
            'uu'   => ['ऊ'],
            'ya'   => ['я'],
            'yu'   => ['ю'],
            'zh'   => ['ж', 'ჟ', 'ژ'],
            '(c)'  => ['©'],
            'A'    => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ā', 'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ', 'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǻ', 'Ǎ', 'Ａ', 'Ä'],
            'B'    => ['Б', 'Β', 'ब', 'Ｂ'],
            'C'    => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ', 'Ｃ'],
            'D'    => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ', 'Ｄ'],
            'E'    => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ', 'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э', 'Є', 'Ə', 'Ｅ'],
            'F'    => ['Ф', 'Φ', 'Ｆ'],
            'G'    => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ', 'Ｇ'],
            'H'    => ['Η', 'Ή', 'Ħ', 'Ｈ'],
            'I'    => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į', 'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ', 'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', 'Ǐ', 'ϒ', 'Ｉ'],
            'J'    => ['Ｊ'],
            'K'    => ['К', 'Κ', 'Ｋ'],
            'L'    => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ', 'ल', 'Ｌ'],
            'M'    => ['М', 'Μ', 'Ｍ'],
            'N'    => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν', 'Ｎ'],
            'O'    => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ø', 'Ō', 'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ', 'Ὸ', 'Ό', 'О', 'Θ', 'Ө', 'Ǒ', 'Ǿ', 'Ｏ', 'Ö'],
            'P'    => ['П', 'Π', 'Ｐ'],
            'Q'    => ['Ｑ'],
            'R'    => ['Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ', 'Ｒ'],
            'S'    => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ', 'Ｓ'],
            'T'    => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ', 'Ｔ'],
            'U'    => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Û', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ', 'Ｕ', 'Ў', 'Ü'],
            'V'    => ['В', 'Ｖ'],
            'W'    => ['Ω', 'Ώ', 'Ŵ', 'Ｗ'],
            'X'    => ['Χ', 'Ξ', 'Ｘ'],
            'Y'    => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ', 'Ｙ'],
            'Z'    => ['Ź', 'Ž', 'Ż', 'З', 'Ζ', 'Ｚ'],
            'AE'   => ['Æ', 'Ǽ'],
            'Ch'   => ['Ч'],
            'Dj'   => ['Ђ'],
            'Dz'   => ['Џ'],
            'Gx'   => ['Ĝ'],
            'Hx'   => ['Ĥ'],
            'Ij'   => ['Ĳ'],
            'Jx'   => ['Ĵ'],
            'Kh'   => ['Х'],
            'Lj'   => ['Љ'],
            'Nj'   => ['Њ'],
            'Oe'   => ['Œ'],
            'Ps'   => ['Ψ'],
            'Sh'   => ['Ш'],
            'Shch' => ['Щ'],
            'Ss'   => ['ẞ'],
            'Th'   => ['Þ'],
            'Ts'   => ['Ц'],
            'Ya'   => ['Я'],
            'Yu'   => ['Ю'],
            'Zh'   => ['Ж'],
            ' '    => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", "\xEF\xBE\xA0"],
        ];
        // @formatter:off
    }

    /**
     * Returns the language specific replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/3.1.0/LICENSE.txt
     *
     * @param  string $languageCode
     *
     * @return array|null
     */
    protected static function languageSpecificCharsArray($languageCode): ?array
    {
        static $languageSpecific;

        if ( ! isset($languageSpecific)) {
            $languageSpecific = [
                'bg' => [
                    'х' => 'h',
                    'Х' => 'H',
                    'щ' => 'sht',
                    'Щ' => 'SHT',
                    'ъ' => 'a',
                    'Ъ' => 'А',
                    'ь' => 'y',
                    'Ь' => 'Y',
                ],
                'de' => [
                    'ä' => 'ae',
                    'ö' => 'oe',
                    'ü' => 'ue',
                    'Ä' => 'AE',
                    'Ö' => 'OE',
                    'Ü' => 'UE',
                ],
            ];
        }

        $languageCode = static::processLanguageCode($languageCode);

        return $languageSpecific[$languageCode] ?? null;
    }

    /**
     * @param string $languageCode
     *
     * @return string
     */
    protected static function processLanguageCode(string $languageCode): string
    {
        return static::create($languageCode)
            ->lowerCase()
            ->str();
    }
}
