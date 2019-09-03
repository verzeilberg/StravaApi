# StravaApi

This is a zend framework 3 [StravaApi](https://www.strava.com) api.

## Description

This api is used to connect your Strava account to your website. And to manage all your
Strava stats. You can import your stats into a database and then analyse your data
trough graphs provided by [Chart.js](https://www.chartjs.org/). But you can use any other 
Graph library to do this.

## Installation
Just clone this module into your project by git. I did it by adding this module to
my .gitmodules file with below configuration (or your own if you forked it):

>[submodule "StravaApi"]
>
>	path = module/StravaApi
>
>	url = https://github.com/verzeilberg/StravaApi.git

And did a git submodule update.

Add the Module ('StravaApi') to your config/autoload/modules.config.php

Do a composer dump-autoload and your good to go.

## Attention

This module is stil in development. So there can be big changes along the way. 
I have still to think somethings out to make it easier and clearer.

If you have any questions just send me a [e-mail](http://mailto:sander@verzeilberg.nl). 

 


