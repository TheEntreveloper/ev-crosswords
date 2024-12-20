=== EV Crosswords ===
Contributors: entreveloper
Tags: crosswords, wordpress, entertainment, word games
Requires at least: 5.9
Tested up to: 6.4.2
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add crosswords to your Wordpress website.

== Description ==

This Plugin allows you to add crosswords to your Wordpress website.
The crosswords exist as text files, in a custom .xml format. The Plugin allows you to upload a crossword file, and display it in the frontend for end users to solve it.

The crosswords file format is explained here:
https://github.com/TheEntreveloper/crosswords
You can also take a look at a few sample crossword files to quickly make sense of its format. You can find some here: https://github.com/TheEntreveloper/crosswords/tree/main/data.

While you can create crosswords manually, and upload your files to your
Wordpress website using this Plugin, it is easier to create those files with the Entreveloper Crossword Making Tools:

- https://Creatorive.com - A new website under active development, that already offers free and premium Crossword making functionality. Using this tool is very easy, you do not have 
to download and install anything to your computer. Simply create a free account and start creating Crosswords. You can then download the crossword in our custom format, and upload it to your Wordpress website using our Plugin. Using this tool you can also print your Crossword to solve by hand, or print them to a .pdf file so you can email them instead.

- A free Java Desktop App that can be downloaded here: https://github.com/TheEntreveloper/crosswords/blob/main/maker/maker.zip
This tool allows you to create Crosswords files interactively, which you can then upload and view, categorize and make available to your visitors.
Watch a short [YouTube video](https://www.youtube.com/watch?v=AnQd8gPxKfw "TheEntreveloper YouTube Channel") that briefly covers this tool and this Plugin.

== Installation ==

Installation and use of this plugin is straightforward:

1. Download the plugin files and copy to your Plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to Dashboard, Ev Crosswords Settings.
4. Upload crossword files previously created (manually or with any of the Crosswords Making Tools)
5. The uploaded crossword is now available through the crosswords entry in the Dashboard, and
can be displayed to end users in the frontend (see the url under the "View" option of each entry).

== Development ==

Development happens in Github:
[EV-Crosswords](https://github.com/TheEntreveloper/ev-crosswords "GitHub Repository")

== Frequently Asked Questions ==

= What does this plugin do? =

It makes easy adding crosswords to your Wordpress website.

= What doesn't this plugin do? =

It does not create the Crossword files for you, just gives you a way to upload and display your crosswords. It is a viewer only, for now.

= Does this plugin modify any core WordPress, plugin or theme files? =

No, but when viewing a Crossword entry it uses its own template files. These template files are available within this Plugin directory, under views/crossword.
So, customizing the look and feel would require changing those at the moment. A future version will check for Plugin support within the active theme.

== Screenshots ==

1. Example of Crossword displayed in the frontend of a Wordpress website.

