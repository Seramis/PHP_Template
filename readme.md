# PHP Template #
PHP itself is a powerful language... and very powerful for templating too! Why to start to reinvent the wheel, when you could just use PHP by itself as a templating language? Besides, you already know PHP, so why should I say to you that now you need to learn new language?
This templating class makes it a bit easier to write [alternative php syntax](http://php.net/manual/en/control-structures.alternative-syntax.php) into templates.

## Example template ##
```php
{/* This is a comment. */}
Hello, {=$name}!<br />
{for($i = 0; $i < 10; $i++):}
	Iteration {=$i}<br />
{endfor;}
{Tpl::incl('%path%/relative/path/to/template.tpl'}
```

## So what does it do? ##
Basically only thing it does, is replacing `{blah}` to `<?blah?>`. There's also a rule, that **if there is any whitespace or line ending right after opening bracket, it will be not parsed.** This way you can write JS code into templates and templating engine will not screw it up.

It also provides some keywords for your template:

* `%tpl%` - template path and filename (Ex: tpl/main.tpl)
* `%path%` - template's path (Ex: tpl)
* `%file%` - template's file name (Ex: main.tpl)

And for last, you have `$this` available, that directs to current template's PHP object.

Templates are compiled into PHP files, so it can take advantage of every kind of PHP accelerator for speed.
Eventually, what you will see on screen, will be generated by native PHP code. Compiling is quick and simple and after the file is compiled, it just executes compiled, native PHP code.

## I want to add some function to use in class ##
Just create function in php, and use it. Lets make a function and put it in our index file for example:
```php
function make_safe($sString)
{
	return htmlentities($sString);
}
```
And now, in templates:
```php
{=$sBadString} {/* This is the bad way */}
{=make_safe($sBadString)} {/* This is now safe */}
```
Like said before, it really is just php by itself.

## Licence ##
The MIT License (MIT)

Copyright (c) 2014 Joonatan Uusväli

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.