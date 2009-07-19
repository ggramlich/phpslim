---
layout: yaml
title: First Steps
heading: First Steps with FitNesse and PhpSlim
section: first_steps
---
Start FitNesse
--------------

After you have finished the [Installation](installation.html), you should
create a directory for your new project. I prefer to keep
my FitNesse wiki pages in my project directory. This allows me to
keep everything synchronized under version control.
You can take a look at this [video by Uncle Bob](http://vimeo.com/2765514).

Assuming that our project directory is `/path/to/project`,
I start FitNesse on port 8070 like this in the directory with
the `fitnesse.jar` file.

{% highlight bash %}
java -jar fitnesse.jar -e 0 -p 8070 -d /path/to/project
{% endhighlight %}

For more information on the command line options take a look at the
[Starting and Stopping FitNesse user guide][StartStop].

> You will find, that FitNesse unpacks its files again, but now to our
> project directory. In there we find the FitNesseRoot directory and a lib
> directory. We can delete the lib directory there.
> We can also delete most of the stuff underneath FitNesseRoot later,
> but will keep it now to have a quick reference.
>
> The server has not started.
> This is one of the quirks with the new FitNesse release.
> Just repeat the last console command.
>
>     java -jar fitnesse.jar -e 0 -p 8070 -d /path/to/project

You should now see the output

    FitNesse (v20090709) Started...
        port:              8070
        root page:         fitnesse.wiki.FileSystemPage at /path/to/projectFitNesseRoot
        logger:            none
        authenticator:     fitnesse.authentication.PromiscuousAuthenticator
        html page factory: fitnesse.html.HtmlPageFactory
        page version expiration set to 0 days.


Now direct your browser to <http://localhost:8070> and you will see the
FitNesse FrontPage (in frames).

[StartStop]: http://fitnesse.org/FitNesse.UserGuide.StartingAndStoppingFitNesse

The first wiki page
-------------------

Start your first wiki page by browsing to a non-existing page
<http://localhost:8070/MyFirstSlimTest>.

You see an editor section. Overwrite the `!contents -R2 -g -p -f -h` with

    !|my fixture     |
    |value|successor?|
    |5    |6         |
    |-4   |-3        |
    |2    |4         |

and click on `Save`. We just created our first test. Since the page name
MyFirstSlimTest ended with Test, FitNesse automatically provides
the `Test` button.

Tell FitNesse about PhpSlim
---------------------------

We have to tell FitNesse, to use the PhpSlim server.
The appropriate place for this is <http://localhost:8070/root>.

First we need to find out, where the phpSlim script is.
