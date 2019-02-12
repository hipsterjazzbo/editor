# Editor

![Editor](https://github.com/helloplacemat/editor/raw/master/readme-header.jpg)

[![Build Status](https://travis-ci.org/helloplacemat/editor.svg?branch=master)](https://travis-ci.org/helloplacemat/editor)
[![Coverage Status](https://coveralls.io/repos/github/helloplacemat/editor/badge.svg?branch=master)](https://coveralls.io/github/helloplacemat/editor?branch=master)
[![Latest Stable Version](https://poser.pugx.org/placemat/editor/v/stable)](https://packagist.org/packages/placemat/editor)
[![Latest Unstable Version](https://poser.pugx.org/placemat/editor/v/unstable)](https://packagist.org/packages/placemat/editor)
[![Total Downloads](https://poser.pugx.org/placemat/editor/downloads)](https://packagist.org/packages/placemat/editor)
[![License](https://poser.pugx.org/placemat/editor/license)](https://packagist.org/packages/placemat/editor)


A clean, elegant, unicode-safe, fluent, immutable, localisable, dependency-free string manipulation library for PHP 7.1+

## Installation

You can install the package via composer:

``` bash
composer require placemat/editor
```

## Usage

### Create an instance

To use Editor, first you've got to instantiate it on your string:

```php
echo Editor::create('hello world')->upperCase();

// HELLO WORLD
```

That's a little verbose though, so Editor comes with a global helper function too:

```php
echo s('hello world')->upperCase();

// HELLO WORLD
```

### Do some stuff!

Editor is fluent and chainable, so you can just keep adding operations to each other:

```php
echo s('    EDITOR is pretty neat I guess 💩      ')
    ->trim()
    ->titleCase();
    
// Editor is Pretty Neat I Guess 💩
```

Editor is also immutable, so you won't mess up your original instance:

```php
$str = s('Apple');

echo $str->plural(); // Apples

echo $str; // Apple
```

Editor implements PHP's `__toString()` magic method, so in most cases you can just use it like a string and it'll work just fine:

```php
$str = s('worlds');

echo 'Hello ' . $str->singular()->upperCaseFirst();

// Hello World
```

If you *do* need to get a regular string back for whatever reason, you can either cast an instance of Editor, or call `str()`:

```php
$hello = s('Hello World');

(string) $hello; // Will be a string

$hello->str(); // Will also be a string
```

### Title Casing

Editor implements proper title casing, based on [John Gruber's crazy title casing script](https://gist.github.com/gruber/9f9e8650d68b13ce4d78):

```php
s('this is a headline that will be properly title-cased')->titleCase();

// This Is a Headline That Will Be Properly Title-Cased
```

If you're after what other libraries so boldly claim is title case, you want `upperCaseFirst()`:

```php
s('this is not actually title casing')->upperCaseFirst();

// This Is Not Actually Title-casing
```

### Inflection

Editor supports making a string singular or plural:

```php
s('apples')->singular(); // 'apple'

s('apple')->plural(); // 'apples'

// plural also has a $count parameter, in case you need to pluralize dynamically
s('apple')->plural($count); // 'apple' if $count is 1, 'apples' otherwise
```

Inflections are localizable in Editor. Right now, Editor supports 6 languages:

- English ('en')
- Spanish ('es')
- French ('fr')
- Portuguese ('pt')
- Norwegian Bokmal ('nb')
- Turkish ('tr')

```php
s('bijou')->plural($count, 'fr'); // 'bijoux'
```

> If you'd like to add an inflector, simply
>
> - Extend `Placemat\Editor\Inflectors\Inflector`
> - Register with `Editor::registerInflector($inflector)`
> - That's it!
>
> If you do add inflectors, feel free to open a Pull Request!

## Function Reference

Full function reference documentation is probably something I should write. But for now, you can peruse [the main Editor class](/src/Editor.php). Every method is doc-blocked and tested 👍

