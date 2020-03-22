# Shopping Delivery Check

## Overview

The aim of this project is to create a simple tool that will make it possible for people to know how many people in their area are depending on supermarket shopping deliveries, so that they can make informed decisions about how much they should use these services. 

## Installation

This is a PHP application that should run on any server that supports PHP 5.5 or higher with Mod_Rewrite enabled. You will need to connect to a MySQL database:
* Copy db.example.php to db.php
* Update the configuration in db.php
* Import db.sql into your database to create the information table
* Run the loadpostcodes.php script to create the postcodes table

## How to Contribute

Fork us and submit a pull request! All contributions welcome!

## Technical Overview

* First time in, details ae saved to database and a unique ID is returned
* The unique id is passed around in URLs (this means that the user can update their info at any time by bookmarking the page, without any need for logins)
* Layout uses [Bootstrap](http://getbootstrap.com/) 4.1.3
* Rendering charts uses [Chart.js](https://www.chartjs.org/) 2.7.2
* Icons from [Font Awesome Free](https://fontawesome.com/free) 5.3.1

## License

This source code is released under the [MIT license](https://github.com/atosorigin/DevOpsMaturityAssessment/blob/master/LICENSE). Bootstrap and Chart.js are also released under the [MIT license](https://github.com/atosorigin/DevOpsMaturityAssessment/blob/master/LICENSE). Font Awesome Free and Comfortaa is provided under the [SIL OFL 1.1 License](https://scripts.sil.org/cms/scripts/page.php?site_id=nrsi&id=OFL)

## Credits

* Many people who have provided fast feedback on early versons to improve usability and usefulness
* [Bootstrap](http://getbootstrap.com/)
* [Chart.js](https://www.chartjs.org/)
* [Font Awesome Free](https://fontawesome.com/free)
* [Comfortaa Font](https://github.com/alexeiva/comfortaa)
* [Markus Spiske](https://unsplash.com/@markusspiske) for background image, published on [Unsplash](https://unsplash.com/)
* [Vojtech Bruzek](https://unsplash.com/@vojtechbruzek) for og-image.jpg, published on [Unsplash](https://unsplash.com/)