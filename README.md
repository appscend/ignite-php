IgnitePHP
==========

PHP framework for fast developing mobile apps using IgniteMarkup.

Installing
----------

Before we start we must set up the bare minimum for it to work with composer:

	$ composer init

* Minimum stability must be 'dev'.
* Edit the resulting composer.json file and add the following lines (replacing the "require" field, obviously):


	"repositories": [
		{
		  "type": "git",
		  "url": "https://github.com/appscend/ignite-php.git",
		  "vendor-alias": "appscend"
		},
		{
		  "type": "git",
		  "url": "https://github.com/appscend/ignite-sdk.git",
		  "vendor-alias": "appscend"
		}
	 ],
	"require": {
		"appscend/ignite-php": "dev-develop",
		"appscend/ignite_sdk": "dev-master"
	}
	
* Then finally update the package:


	$ composer update

Copy the script file 'ignite-build.php' to your project root directory, then run it:

	$ cp vendor/appscend/ignite-php/ignite-build.php .
	$ php ignite-build.php