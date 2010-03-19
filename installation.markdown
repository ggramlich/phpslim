---
layout: yaml
title: Installation
heading: Installation of PhpSlim
section: tutorials
---
Requirements
------------

You do **not** need a webserver like Apache.

- PHP version &ge; 5.1.2, CGI access
- Java &ge; 6 for FitNesse and PhpSlim

Install PhpSlim
------------

All you need to do is download the
[phpslim.jar](http://github.com/downloads/ggramlich/phpslim/phpslim.jar)
and put it into some directory either in your project or into
a central path like `/opt`.

Prepare FitNesse
----------------

Now you need a [FitNesse](http://fitnesse.org) installation
to make use of PhpSlim. Go to the
[download page](http://fitnesse.org/FrontPage.FitNesseDevelopment.DownLoad),
either download the latest `fitnesse.jar` from the RELEASE section or
click on the **EDGE: Latest Hudson build** link
and download the `fitnesse.jar` file from the Hudson page.

Put the `fitnesse.jar` file into some directory from where you want to 
start the FitNesse system.

Create a project directory
--------------------------

Now create an empty directory for your new PHP project.
For this tutorial, I assume that your project directory is
`/path/to/project`.

> I prefer to keep my FitNesse wiki pages in my project directory.
> This allows me to keep the tests and the code synchronized
> under version control.
> You can take a look at this [video by Uncle Bob](http://vimeo.com/2765514).

> Brett Schuchert describes an alternative way of using 
> [FitNesse as a central wiki
> server](http://schuchert.wikispaces.com/FitNesse.PageHierarchyForTeamDevelopment)

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
    FitNesse (v20100317) Started...
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

The appropriate place to tell FitNesse, how to
call up PhpSlim is <http://localhost:8070/root>. This page
is always parsed, when you run a test.
Open this root page in your browser and click on `Edit`.
Assuming that you saved `phpslim.jar` to `/opt/phpslim.jar`,
enter the following in the editor text area.

    !define TEST_RUNNER {slim.PhpSlimService}
    !define COMMAND_PATTERN (java -cp /opt/phpslim.jar %m -i /path/to/project/Slim)
    !define TEST_SYSTEM {slim}

You need to replace `/path/to/project` with your actual path. The -i parameter 
defines the php include path.

If you saved `phpslim.jar` into `/path/to/project/phpslim.jar` you can
define the COMMAND_PATTERN relative to FITNESSE_ROOTPATH.

    !define COMMAND_PATTERN (java -cp ${FITNESSE_ROOTPATH}/phpslim.jar %m -i ${FITNESSE_ROOTPATH}/Slim)

These settings are described on the 
[CustomizingTestExecution](http://fitnesse.org/FitNesse.UserGuide.CustomizingTestExecution)
page of the User Guide.

Click on `Save`.

You can now [write your first test](first_test.html).

[StartStop]: http://fitnesse.org/FitNesse.UserGuide.StartingAndStoppingFitNesse

