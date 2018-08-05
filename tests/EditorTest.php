<?php

namespace Placemat\Editor\Tests;

use PHPUnit\Framework\TestCase;
use Placemat\Editor\Editor;

class EditorTest extends TestCase
{
    public $str = 'hellö world';

    public function testCreate()
    {
        $str = Editor::create($this->str);

        $this->assertInstanceOf(Editor::class, $str);
    }

    public function testStr()
    {
        $str = Editor::create($this->str)->str();

        $this->assertInternalType('string', $str);
    }

    public function testAfter()
    {
        $after = Editor::create($this->str)->after('ö')->str();

        $this->assertEquals(' world', $after);
    }

    public function testBefore()
    {
        $before = Editor::create($this->str)->before('ö')->str();

        $this->assertEquals('hell', $before);
    }

    public function testStartWith()
    {
        $prefixed = Editor::create($this->str)->startWith('123á')->str();

        $this->assertEquals('123áhellö world', $prefixed);
    }

    public function testFinishWith()
    {
        $suffixed = Editor::create($this->str)->finishWith('123á')->str();

        $this->assertEquals('hellö world123á', $suffixed);
    }

    public function testLimitCharacters()
    {
        $limited = Editor::create($this->str)->limitCharacters(6)->str();

        $this->assertEquals('hellö…', $limited);
    }

    public function testLimitWords()
    {
        $limited = Editor::create($this->str)->limitWords(1)->str();

        $this->assertEquals('hellö…', $limited);
    }

    public function testPlural()
    {
        $this->assertTrue(true);
    }

    public function testSingular()
    {
        $this->assertTrue(true);
    }

    public function testReplace()
    {
        $replaced = Editor::create($this->str)->replace('hellö', 'göödbye')->str();

        $this->assertEquals('göödbye world', $replaced);
    }

    public function testReplaceFirst()
    {
        $replaced = Editor::create($this->str)->replaceFirst('l', 'w')->str();

        $this->assertEquals('hewlö world', $replaced);
    }

    public function testReplaceLast()
    {
        $replaced = Editor::create($this->str)->replaceLast('l', 'w')->str();

        $this->assertEquals('hellö worwd', $replaced);
    }

    public function testReplaceSub()
    {
        $replaced = Editor::create($this->str)->replaceSub('💩', 0, 2)->str();

        $this->assertEquals('💩llö world', $replaced);
    }

    public function testRemove()
    {
        $removed = Editor::create($this->str)->remove('ö')->str();

        $this->assertEquals('hell world', $removed);
    }

    public function testRemoveFirst()
    {
        $removed = Editor::create($this->str)->removeFirst('l')->str();

        $this->assertEquals('helö world', $removed);
    }

    public function testRemoveLast()
    {
        $removed = Editor::create($this->str)->removeLast('l')->str();

        $this->assertEquals('hellö word', $removed);
    }

    public function testSlugify()
    {
        $slug = Editor::create($this->str)->slug()->str();

        $this->assertEquals('hello-world', $slug);
    }

    public function testChunk()
    {
        $chunk = Editor::create($this->str)->chunk(2);

        $this->assertEquals(['he', 'll', 'ö ', 'wo', 'rl', 'd'], $chunk);
    }

    public function testSplitWords()
    {
        $split = Editor::create($this->str)->splitWords();

        $this->assertEquals(['hellö', 'world'], $split);
    }

    public function testSlice()
    {
        $sliced = Editor::create($this->str)->slice(1, 9)->str();

        $this->assertEquals('ellö worl', $sliced);
    }

    public function testTrim()
    {
        $trimmed = Editor::create("\n " . $this->str . "\t ")->trim()->str();

        $this->assertEquals($this->str, $trimmed);
    }

    public function testContains()
    {
        $contains = Editor::create($this->str)->contains('hellö');

        $this->assertTrue($contains);

        $contains = Editor::create($this->str)->contains('hello');

        $this->assertFalse($contains);
    }

    public function testStartsWith()
    {
        $startsWith = Editor::create($this->str)->startsWith('hellö');

        $this->assertTrue($startsWith);

        $startsWith = Editor::create($this->str)->startsWith('hello');

        $this->assertFalse($startsWith);
    }

    public function testEndsWith()
    {
        $endsWith = Editor::create($this->str)->endsWith('ö world');

        $this->assertTrue($endsWith);

        $endsWith = Editor::create($this->str)->endsWith('o world');

        $this->assertFalse($endsWith);
    }

    public function testMatches()
    {
        $matches = Editor::create($this->str)->matches('*ö world');

        $this->assertTrue($matches);

        $matches = Editor::create($this->str)->matches('*o world');

        $this->assertFalse($matches);
    }

    public function testLength()
    {
        $length = Editor::create($this->str)->length();

        $this->assertInternalType('int', $length);
        $this->assertEquals(11, $length);
    }

    public function testLowerCase()
    {
        $lower = Editor::create('HELLÖ WORLD')->lowerCase()->str();

        $this->assertEquals($this->str, $lower);
    }

    public function testLowerCaseFirst()
    {
        $lower = Editor::create('HELLÖ WORLD')->lowerCaseFirst()->str();

        $this->assertEquals('hELLÖ WORLD', $lower);
    }

    public function testLowerCaseWords()
    {
        $lower = Editor::create('HELLÖ WORLD')->lowerCaseWords()->str();

        $this->assertEquals('hELLÖ wORLD', $lower);
    }

    public function testUpperCase()
    {
        $upper = Editor::create($this->str)->upperCase()->str();

        $this->assertEquals('HELLÖ WORLD', $upper);
    }

    public function testUpperCaseFirst()
    {
        $upper = Editor::create($this->str)->upperCaseFirst()->str();

        $this->assertEquals('Hellö world', $upper);
    }

    public function testUpperCaseWords()
    {
        $upper = Editor::create($this->str)->upperCaseWords()->str();

        $this->assertEquals('Hellö World', $upper);
    }

    /**
     * @dataProvider titleCaseProvider
     *
     * @param $str
     * @param $expected
     */
    public function testTitleCase($str, $expected, $ignore = [])
    {
        $title = Editor::create($str)->titleCase($ignore)->str();

        $this->assertEquals($expected, $title);
    }

    public function testCamelCase()
    {
        $camel = Editor::create($this->str)->camelCase()->str();

        $this->assertEquals('hellöWorld', $camel);
    }

    public function testStudlyCase()
    {
        $studly = Editor::create($this->str)->studlyCase()->str();

        $this->assertEquals('HellöWorld', $studly);
    }

    public function testSnakeCase()
    {
        $snake = Editor::create($this->str)->snakeCase()->str();

        $this->assertEquals('hellö_world', $snake);
    }

    public function testKebabCase()
    {
        $snake = Editor::create($this->str)->kebabCase()->str();

        $this->assertEquals('hellö-world', $snake);
    }

    public function testAscii()
    {
        $ascii = Editor::create($this->str)->ascii()->str();

        $this->assertEquals('hello world', $ascii);
    }

    public function titleCaseProvider()
    {
        return [
            ['TITLE CASE', 'Title Case'],
            ['testing the method', 'Testing the Method'],
            ['i like to watch DVDs at home', 'I Like to watch DVDs at Home', ['watch']],
            ['  Θα ήθελα να φύγει  ', 'Θα Ήθελα Να Φύγει', []],
            ['For step-by-step directions email someone@gmail.com', 'For Step-by-Step Directions Email someone@gmail.com'],
            ["2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'", "2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'"],
            ['Have you read “The Lottery”?', 'Have You Read “The Lottery”?'],
            ['your hair[cut] looks (nice)', 'Your Hair[cut] Looks (Nice)'],
            ["People probably won't put http://foo.com/bar/ in titles", "People Probably Won't Put http://foo.com/bar/ in Titles"],
            ['Scott Moritz and TheStreet.com’s million iPhone la‑la land', 'Scott Moritz and TheStreet.com’s Million iPhone La‑La Land'],
            ['BlackBerry vs. iPhone', 'BlackBerry vs. iPhone'],
            ['Notes and observations regarding Apple’s announcements from ‘The Beat Goes On’ special event', 'Notes and Observations Regarding Apple’s Announcements From ‘The Beat Goes On’ Special Event'],
            ['Read markdown_rules.txt to find out how _underscores around words_ will be interpretted', 'Read markdown_rules.txt to Find Out How _Underscores Around Words_ Will Be Interpretted'],
            ["Q&A with Steve Jobs: 'That's what happens in technology'", "Q&A with Steve Jobs: 'That's What Happens in Technology'"],
            ["What is AT&T's problem?", "What Is AT&T's Problem?"],
            ['Apple deal with AT&T falls through', 'Apple Deal with AT&T Falls Through'],
            ['this v that', 'This v That'],
            ['this vs that', 'This vs That'],
            ['this v. that', 'This v. That'],
            ['this vs. that', 'This vs. That'],
            ["The SEC's Apple probe: what you need to know", "The SEC's Apple Probe: What You Need to Know"],
            ["'by the way, small word at the start but within quotes.'", "'By the Way, Small Word at the Start but Within Quotes.'"],
            ['Small word at end is nothing to be afraid of', 'Small Word at End Is Nothing to Be Afraid Of'],
            ['Starting sub-phrase with a small word: a trick, perhaps?', 'Starting Sub-Phrase with a Small Word: A Trick, Perhaps?'],
            ["Sub-phrase with a small word in quotes: 'a trick, perhaps?'", "Sub-Phrase with a Small Word in Quotes: 'A Trick, Perhaps?'"],
            ['Sub-phrase with a small word in quotes: "a trick, perhaps?"', 'Sub-Phrase with a Small Word in Quotes: "A Trick, Perhaps?"'],
            ['"Nothing to Be Afraid of?"', '"Nothing to Be Afraid Of?"'],
            ['a thing', 'A Thing'],
            ['Dr. Strangelove (or: how I Learned to Stop Worrying and Love the Bomb)', 'Dr. Strangelove (Or: How I Learned to Stop Worrying and Love the Bomb)'],
            ['  this is trimming', 'This Is Trimming'],
            ['this is trimming  ', 'This Is Trimming'],
            ['  this is trimming  ', 'This Is Trimming'],
            ['IF IT’S ALL CAPS, FIX IT', 'If It’s All Caps, Fix It'],
            ['What could/should be done about slashes?', 'What Could/Should Be Done About Slashes?'],
            ['Never touch paths like /var/run before/after /boot', 'Never Touch Paths Like /var/run Before/After /boot'],
        ];
    }
}
