---
layout: yaml
title: Installation
heading: Installation of PhpSlim
section: installation
---
Requirements
------------

The supported way to install to install PhpSlim, uses the
[PEAR](http://pear.php.net) installer. The installation process will tell you,
if there are any missing PHP extensions.

You do **not** need a webserver like Apache.

- PHP version &ge; 5.1.2, CLI access
- PEAR (only recommended for installation)
- Java &ge; 5 for FitNesse

Install PhpSlim
------------

The installation on Linux and Windows system is basically the same.
On Windows you can ignore the user changes.

As `root` user type the following on the console.

{% highlight bash %}
pear upgrade PEAR
pear channel-update pear.php.net
pear channel-discover ggramlich.github.com
pear install ggramlich/PhpSlim
{% endhighlight %}

That's it. Change back to the normal user.

Install FitNesse
----------------

Now you need a [FitNesse](http://fitnesse.org) installation
to make use PhpSlim. Go to the
[download page](http://fitnesse.org/FrontPage.FitNesseDevelopment.DownLoad)
and download the latest `fitnesse.jar` file.

> The  20090709 release has some problems and it is the first with a new
> self-installing concept, so there are some issues and not all the information
> in the [FitNesse User Guide](http://fitnesse.org/FitNesse.UserGuide)
> is up to date.

Put the jar file into some directory where you want to keep 
the fitnesse installation. Change to this directory on the console and type

{% highlight bash %}
java -jar fitnesse.jar
{% endhighlight %}

The first time you do this, FitNesse unpacks its files. You see the output.

    Unpacking new version of FitNesse resources.  Please be patient.
    ..................................................................
    ...
    ..................................................................
    You must now reload FitNesse.  Thank you for your patience........


When this is done,
you can start FitNesse and set up your first project.

Continue with [First Steps](first_steps.html).

