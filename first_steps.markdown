---
layout: yaml
title: First Steps
heading: First Steps with FitNesse and PhpSlim
section: first_steps
brace: "{"
---
Start FitNesse
--------------

After you have finished the [Installation](installation.html), you should
create a directory for your new PHP project. For this guide, I assume that
your project directory is `/path/to/project`.

> I prefer to keep my FitNesse wiki pages in my project directory.
> This allows me to keep the tests and the code synchronized
> under version control.
> You can take a look at this [video by Uncle Bob](http://vimeo.com/2765514).

On the console, I change to the directory where I have put the
`fitnesse.jar` file.
Assuming that our project directory is `/path/to/project`,
I start FitNesse on port 8070 like this.

{% highlight bash %}
java -jar fitnesse.jar -e 0 -p 8070 -d /path/to/project
{% endhighlight %}

For more information on the command line options take a look at the
[Starting and Stopping FitNesse user guide][StartStop].

You should now see the output

    Unpacking new version of FitNesse resources.  Please be patient.
    ................................................................................
    ...
    ................................................................................
    FitNesse (v20090709) Started...
        port:              8070
        root page:         fitnesse.wiki.FileSystemPage at /path/to/project/FitNesseRoot
        logger:            none
        authenticator:     fitnesse.authentication.PromiscuousAuthenticator
        html page factory: fitnesse.html.HtmlPageFactory
        page version expiration set to 0 days.

You will find, that FitNesse creates the directory `FitNesseRoot` in
our project path. This directory contains a directory `FitNesse` with 
the user guide and more information about FitNesse.

> You may delete the `FitNesse` sub directory,
> if you do not want to keep it under your project path.

The `FitNesseRoot` directory is the place, where the wiki pages for our 
tests will be stored.

Now direct your browser to <http://localhost:8070> and you will see the
FitNesse FrontPage (in frames).

Tell FitNesse about PhpSlim
---------------------------

We have to tell FitNesse, to use the PhpSlim server.
First we need to find out, where the PhpSlim script is.
When we installed PhpSlim, the PEAR installer created a script for us
in the PEAR executables directory (`bin_dir`).
To find out where this directory is, type

{% highlight bash %}
pear config-get bin_dir
{% endhighlight %}

On my system, I get

    /usr/bin

So the script is `/usr/bin/runPhpSlim`. For Windows users it is a 
`runPhpSlim.bat` file in the `bin_dir`.

The appropriate place to tell FitNesse, how to
call up PhpSlim is <http://localhost:8070/root>. This page
is always parsed, when you run a test.
Open the page in your browser, click on `Edit`
and enter the following in the editor text area.

    !define TEST_RUNNER {/usr/bin/runPhpSlim}
    !path /path/to/project/Slim

    !define TEST_SYSTEM {slim}
    !define COMMAND_PATTERN {{page.brace}}%m %p}
    !define PATH_SEPARATOR {:}

You need to replace `/path/to/project` with your actual path and adjust the
`/usr/bin/runPhpSlim` setting. These settings are described on the 
[User Guide page](http://fitnesse.org/FitNesse.UserGuide.CustomizingTestExecution).

Click on `Save`.

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
