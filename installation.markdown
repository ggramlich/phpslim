---
layout: yaml
title: Installation
heading: Installation of PhpSlim
section: installation
---
Requirements
------------

The supported way to install to install PhpSlim, uses the
[PEAR](http://pear.php.net) installer. The installer will tell you,
if you are missing any PHP extensions. PhpSlim does not depend on the
PEAR package.

You do **not** need a webserver like Apache.

- PHP version &ge; 5.1.2, CLI access
- PEAR (only needed for installation)
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

Prepare FitNesse
----------------

Now you need a [FitNesse](http://fitnesse.org) installation
to make use of PhpSlim. Go to the
[download page](http://fitnesse.org/FrontPage.FitNesseDevelopment.DownLoad),
click on the **EDGE: Latest Hudson build** link
and download the `fitnesse.jar` file from the Hudson page.

The **edge** version of FitNesse is packaged with all its dependencies,
so that there is no need to install or unpack anything.

Put the `fitnesse.jar` file into some directory where you want to keep 
the FitNesse program.

Continue with [First Steps](first_steps.html).

