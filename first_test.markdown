---
layout: yaml
title: First Test
heading: Write a first test with FitNesse and PhpSlim
section: first_steps
---
The first wiki page
-------------------

Start your first wiki page by browsing to a non-existing page
<http://localhost:8070/MyFirstSlimTest>.

Overwrite the `!contents -R2 -g -p -f -h` with

    !|my fixture              |
    |my value|value successor?|
    |5       |6               |
    |-4      |-3              |
    |2       |4               |

and click on `Save`. We just created our first specification (or test) with a
[Decision Table][].
Since the page name
MyFirstSlimTest ended with Test,
[FitNesse automatically provides the `Test` button][PageProperties].
Click on it now.

You will see a lot of yellow bars showing exception messages.
The Slim executor tried to call into PHP code which does not exist yet.

You see `my fixture Could not invoke constructor for MyFixture`.
The header of the table is translated into a
[camel case class name](http://fitnesse.org/FitNesse.UserGuide.GracefulName).
PhpSlim does this translation and is oriented at the
[Zend naming conventions][ZendNaming].
A future revision of PhpSlim might allow configurable translation rules,
especially serving for upcoming PHP name spaces.

Let's write some code
---------------------

First create a `Slim` directory in your project
directory (remember the `!path /path/to/project/Slim` setting?).
Then create a file `MyFixture.php` for the PHP class
in that directory and fill it with

{% highlight php-css %}
<?php
class MyFixture
{
}
{% endhighlight %}

I did intentionally 
[leave out the closing `?>` tag][ZendNoClosing].

Click `Test` again. Now the table header `my fixture` is green and on top of
the page you see **`Assertions:`** `1 right, 0 wrong, 0 ignored, 12 exceptions`.
We got **1 right** already!

Let's move on. You see the exception messages

    Method setMyValue[1] not found in MyFixture.
    Method valueSuccessor[0] not found in MyFixture.

On a [Decision Table][] the Slim executor
instantiates an object of the class specified in the
table header and then attempts to call into the object. A column header
(i.e. in the second row of the table)
specifies the method name to call. A `set` prefix is added, if
the column header does not end with a "`?`".
For each row in the table, the calls are made from left to right and
the return value of a "`?`" method is compared with the value in the cell.

The `setMyValue[1]` tells you, that the executor attempts to call
a `setMyValue` method with one argument. The getter method `valueSuccessor`
should not take an argument.

Let's implement them.

{% highlight php-css %}
<?php
class MyFixture
{
    public function setMyValue($value)
    {
    }

    public function valueSuccessor()
    {
    }
}
{% endhighlight %}

Run the `Test`. Great, no more exceptions, just 3 wrong. You can see
`[null] expected [6]` in red. Obviously the `valueSuccessor` method
does not return anything, in PHP this is equivalent to explictely
returning `null`. So we finish the fixture class.

{% highlight php-css %}
<?php
class MyFixture
{
    private $_myValue;
    
    public function setMyValue($value)
    {
        $this->_myValue = (int) $value;
    }

    public function valueSuccessor()
    {
        return $this->_myValue + 1;
    }
}
{% endhighlight %}

Ah - only one more error `[3] expected [4]`. And this error is actually
an error in the specification.
The successor of 2 is 3. So we click on the edit button
and correct our mistake.

    !|my fixture              |
    |my value|value successor?|
    |5       |6               |
    |-4      |-3              |
    |2       |3               |

Click `Save` and then `Test`. We made it. We have a green bar on top, stating
**`Assertions:`** `4 right, 0 wrong, 0 ignored, 0 exceptions`.

Take a break now. You can [stop the FitNesse server][StartStop]
by opening the page <http://localhost:8070/?responder=shutdown>.

There will be more tutorials soon.

[Decision Table]: http://fitnesse.org/FitNesse.UserGuide.SliM.DecisionTable
[PageProperties]: http://fitnesse.org/FitNesse.UserGuide.PageProperties
[ZendNaming]: http://framework.zend.com/manual/en/coding-standard.naming-conventions.html
[ZendNoClosing]: http://framework.zend.com/manual/en/coding-standard.php-file-formatting.html#coding-standard.php-file-formatting.general
[StartStop]: http://fitnesse.org/FitNesse.UserGuide.StartingAndStoppingFitNesse

