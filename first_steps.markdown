---
layout: yaml
title: First Steps
heading: Prepare FitNesse for the use with PhpSlim
section: tutorials
brace: "{"
---
Create a project directory
--------------------------

After you have finished the [Installation](installation.html), you should
create an empty directory for your new PHP project.
For this tutorial, I assume that your project directory is
`/path/to/project`.

> I prefer to keep my FitNesse wiki pages in my project directory.
> This allows me to keep the tests and the code synchronized
> under version control.
> You can take a look at this [video by Uncle Bob](http://vimeo.com/2765514).


Start FitNesse
--------------

On the console, change to the directory where you put the
`fitnesse.jar` file.
Then start FitNesse on port 8070 like this.

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

Now direct your browser to <http://localhost:8070> and you will see 
your local version of the FitNesse FrontPage (in frames).

Tell FitNesse about PhpSlim
---------------------------

You have to tell FitNesse, to use the PhpSlim server.
First you need to find out, where the PhpSlim script is.
When you installed PhpSlim, the PEAR installer created a 
`runPhpSlim` script for Linux systems, resp. `runPhpSlim.bat`
script for Windows users in the PEAR executables directory (`bin_dir`).
To find out where this directory is, type

{% highlight bash %}
pear config-get bin_dir
{% endhighlight %}

> On my Linux system, I get `/usr/bin`, so the script is `/usr/bin/runPhpSlim`.
> For Windows users with a Xampp installation,
> it might be something like `C:\xampp\php\runPhpSlim.bat`.

The appropriate place to tell FitNesse, how to
call up PhpSlim is <http://localhost:8070/root>. This page
is always parsed, when you run a test.
Open this root page in your browser, click on `Edit`
and enter the following in the editor text area.

    !define TEST_RUNNER {/usr/bin/runPhpSlim}
    !path /path/to/project/Slim

    !define TEST_SYSTEM {slim}
    !define COMMAND_PATTERN {{page.brace}}%m %p}
    !define PATH_SEPARATOR {:}

You need to replace `/path/to/project` with your actual path and adjust the
`/usr/bin/runPhpSlim` setting. These settings are described on the 
[CustomizingTestExecution](http://fitnesse.org/FitNesse.UserGuide.CustomizingTestExecution)
page of the User Guide.

Click on `Save`.

You can now [write your first test](first_test.html).

[StartStop]: http://fitnesse.org/FitNesse.UserGuide.StartingAndStoppingFitNesse

