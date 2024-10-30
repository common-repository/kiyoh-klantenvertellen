=== Kiyoh / Klantenvertellen ===
Contributors: pepbc
Tags: kiyoh, klantenvertellen, customer reviews, reviews
Requires at least: 5.6
Requires PHP: 7.2
Stable tag: 2.0.12
Tested up to: 5.7
License: GPLv2

Show customer reviews from KiyOh or Klantenvertellen.nl on your website with a Widget or Shortcode.

== Description ==
***Show customer reviews from KiyOh or Klantenvertellen.nl***

Because this plugin is for a typically Dutch review-system, the plugin is only available in Dutch.

***En dan gewoon in het Nederlands...***
Sterren bij je zoekresultaten?
Met deze plugin toon je je klantbeoordelingen van KiyOh of Klantenvertellen.nl op je WordPress website.
Toon klantbeoordelingen met een shortcode.

De plugin zorgt voor de volledige integratie met een API-methode. Alle beoordelingen kunnen zo direct op je eigen website geplaatst worden en in je eigen huisstijl. Zo krijg je ook meteen de "Google sterren" in de zoekresultaten.

De Kiyoh / Klantenvertellen plugin is:
- Gratis
- Zonder technische kennis te plaatsen
- Makkelijk te bedienen
- Volledig in de stijl van je website

***Update van Klantenvertellen & KiyOh***
Klantenvertellen en KiyOh zijn bezig met de migratie naar een nieuw systeem. Hierdoor worden oude klantbeoordelingen niet meer getoond. Het is noodzakelijk dat je snel gebruik gaat maken van de nieuwe API-methode om alle klantbeoordelingen te kunnen blijven tonen.

***Mogelijkheden van de plugin***
- Verschillende layouts (standaard, lijst of slider)
- Bepaal wat je van de review toont
- Toon wel of geen sterren
- Bepaal hoeveel reviews je wilt tonen
- Maak een beoordelingspagina met shortcodes
- En nog veel meer...

***WooCommerce uitnodigingen***
Vanaf versie 1.1.0 is het mogelijk om automatisch je WooCommerce klanten uit te nodigen om een beoordeling achter te laten.
- Bepaal de tekst van de uitnodigingsmail
- Bepaal na hoeveel dagen na afronding van de bestelling de mail wordt verzonden
Voor deze functie heb je een Easy Invite link nodig. Dit is onderdeel van je KiyOh of Klantenvertellen account. Neem bij twijfel even contact op met je accountmanager bij KiyOh of Klantenvertellen.

***Kiyoh / Klantenvertellen versies***
- KiyOh.nl
- Klantenvertellen.nl

***Over het gebruik van de plugin***
Deze plugin is ontwikkeld door PEP en word gratis aangeboden. Je hebt alleen een account nodig voor [KiyOh of Klantenvertellen](https://www.meersuccesonline.nl/klantenvertellen/).
Voor vragen maak je gebruik van de [support forums op WordPress.org](https://wordpress.org/support/plugin/kiyoh-klantenvertellen). De klantenservice van KiyOh of Klantenvertellen kan je niet helpen met het instellen van deze plugin.

***Premium support***
Snel hulp nodig? Met onze [PRO plugin](https://wpfortune.com/shop/plugins/kiyoh-klantenvertellen/) ontvang je priority support via e-mail. Ook kan je alle oude XML-klantbeoordelingen tonen onder de nieuwe beoordelingen en toon je aangepaste review-vragen (zoals afbeeldingen).

KiYoh en Klantenvertellen zijn handelsnamen van KV Media Groep BV.

== Installation ==
1. Install KiyOh / Klantenvertellen plugin via the WordPress.org plugin repository or by uploading the files to the '/wp-content/plugins/' directory.
2. Activate the plugin  through the 'Plugins' menu in WordPress.
3. Go to Settings and fill in your KiyOh or Klantenvertellen.nl information
4. Configure your plugin settings and layout
5. Add reviews with a Widget or Shortcode to your website

== Upgrade Notice ==
= 2.0.11 = 
We bewaren de reviews op een andere manier in de database. Zie je geen reviews? Sla dan een keer je plugin-instellingen opnieuw op.

= 2.0.4 = 
To prevent errors during KiyOh / Klantenvertellen downtime we no longer store review data in transients. This data is moved to a different table in your database.

= 2.0.2 = 
Trage website? Door problemen bij KiyOh / Klantnvertellen krijgen we geen connectie. Update naar de nieuwe versie.

= 2.0.0 = 
KiyOh / Klantenvertellen stopt met de huidige XML-methode. Update je gegevens naar de nieuwe API-methode!

= 1.1.8 = 
Problemen met het tonen van sterren? Controleer je instellingen.

= 1.1.5 = 
Link naar referentiepagina toegevoegd voor Klantenvertellen versie 2 (KV2)

= 1.1.4 =
Voor Klantenvertellen versie 2 (KV2) is een nieuwe XML-feed beschikbaar. *Pas je instellingen aan*.

== Usage ==
Ga naar Instellingen > Kiyoh / Klantenvertellen en stel alles in!

== Disclaimer ==
KiyOh / Klantenvertellen plugin will temporary save review data from your KiyOh or Klantenvertellen account. This data could contain personal data of your reviewers. Data is stored in WordPress Options table on your own website. This data is not shared with the developer of this plugin or with others.

KiyOh / Klantenvertellen-plug-in slaat tijdelijk beoordelingsgegevens op van je KiyOh- of Klantenvertellen-account. Deze gegevens kunnen persoonlijke gegevens van je beoordelaars bevatten. Gegevens worden opgeslagen in WordPress Options table op je eigen website. Deze gegevens worden niet gedeeld met de ontwikkelaar van deze plugin of met anderen.

== Changelog ==

***Kiyoh / Klantenvertellen plugin***
= 2021.01.28 - version 2.0.12 =
* Fixed: Bug with slider

= 2020.12.03 - version 2.0.11 =
* Fixed: Bug with saving review data due to changes in review format in API. Thanks @revenanth for the solution. Related topic: https://wordpress.org/support/topic/geen-data-debug-mode-breekt-admin/

= 2020.11.02 - version 2.0.10 =
* Fixed: Bug in javascript with deprectated widgets.

= 2020.03.16 - version 2.0.9 =
* Performance: Changed the general settings logics to make less requests to Kiyoh / Klantenvertellen
* Fixed: Bug when transient was not set correctly when no NEW data was added to feed
* Fixed: several PHP notices
* Added: Dutch formal translations
* Added: Spanish translations (frontend only)
* Added: Polish translations (frontend only)

= 2020.02.20 - version 2.0.8 =
* Fixed: several PHP notices

= 2020.02.18 - version 2.0.7 =
* Fixed: PHP warning rand() in file /includes/class-settings-page on line 380 and 394 (Thanks to Webvriend.nl)

= 2020.02.12 - version 2.0.6 =
* Added: Debug modus
* Added: Option to show review ID below review for debugging purposes
* Added: HouseNumberExtension for company data
* Fixed: Easy Invite link in WooCommerce emails
* Fixed: An easy with not all transients cleared with debug modus on
* Fixed: Schema.org markup for stats shortcode, added postalAddress
* Removed: Old debug modus based on WP_DEBUG

= 2020.02.05 - version 2.0.5 =
* Added: Company response for single review

= 2019.09.11 - version 2.0.4 =
* Added: Switch to back-up data when API is unstable
* Added: WordPress filters for subject and body of WooCommerce invite e-mail
* Fixed: Notice in settings for last connection check fixed
* Fixed: Responsive CSS grid for list layout

= 2019.08.05 - version 2.0.3 = 
* Added: Show last data connection check timestamp
* Fixed: Several small bugfixes
* Fixed: Schema.org to https (instead of http)
* Changed: Clear transients each 24 hours instead of 12 hours
* Changed: Check for data connection in admin only each 24 hours
* Changed: CURL setup for API with timeouts when Kiyoh / KV is not available

= 2019.07.23 - version 2.0.2 = 
* Fixed: Slow website when Kiyoh / Klantenvertellen servers are unavailable

= 2019.07.18 - version 2.0.1 =
* Added: Fixed tenantID for KV 99
* Added: Fixed tenantID for KiyOh 98
* Fixed: Connection check for KiyOh
* Fixed: Kiyoh logo had an unwanted pixel: pixel removed
* Fixed: List view single review rating background & color not shown correctly when no attributes for style are available
* Deprecated: Optional tenantID (is always fixed)
* Updated template: single-review.php

= 2019.07.12 - version 2.0.0 =
* Added: new API methods for KiyOh & Klantenvertellen
* Added: support for premium plugin
* Added: backup for old XML-method reviews
* Changed: support methods
* Changed: Help page
* Deprecated: Old XML-methods
* Deprecated: Widget

= 2019.07.01 - version 1.2.6 =
* Fixed: Review date not correct for KV Mobiliteit
* Added: Custom review data for KV2
* Added: Stars theme (colors) to shortcode kiyoh-klantenvertellen-summary

= 2019.06.01 - version 1.2.5 =
* Added: option to add custom css-class to shortcode containers
* Changed: Better view of list average results

= 2019.05.16 - version 1.2.4 =
* Fixed: Issue where a string showed where not necessary in KV2

= 2019.05.13 - version 1.2.3 =
* Fixed: Issue with recommendation text in listview layout
* Changed: Review date now following date format from WordPress settings (get_option('date_format)) because KV en Kiyoh use several different date formats
* Changed: Dividers between reviewer name, place, company name and date are now better positioned
* Added: Reviewer company name to single review (only for Klantenvertellen V2)

= 2019.05.08 - version 1.2.2 =
* Tested up to WordPress 5.2
* Added: Option to overwrite summary shortcode text with add_filter('kk_plugin_shortcode_summary','your_function_name',10,4)

= 2019.04.09 - version 1.2.1 =
* Fix: XML feed didn't get all results for Klantenvertellen, added /all/ to XML-feed

= 2019.04.01 - version 1.2.0 =
* Added: Template structure which can be overwritten in your theme
* Added: Extra options for WooCommerce invite mail

= 2019.03.21 - version 1.1.11 =
* Fix: Previous review name showing by KV reviews if no name is available. Thank you @oventawebdesign for reporting
* Fix: Recommendation output (Yes or No) will now be translated correctly.
* Added: WorstRating value for single reviews (now recommended by Google)

= 2019.02.21 - version 1.1.10 =
* Added: Option to add customer billing name to WooCommerce invite mail

= 2019.01.17 - version 1.1.9 =
* Fix: Recommendation not always shown correct in list view

= 2018.12.28 - version 1.1.8 =
* Fix: Removed duplicate transient reset button
* Fix: Missing company name at KV 2 data
* Fix: Removed unwanted output at KV mobiliteit data
* Fix: Shortcode summary not showing correct stars
* Added: Company name for KV mobiliteit data
* Added: Option to show or hide single review date
* Added: List view layout
* Added: Shortcode to show rating statistics
* Tweak: Different way to define styling of star-ratings

= 2018.11.27 - version 1.1.7 =
* Fix: Better error messages when wrong profile is used
* Fix: Removing endless loop with XML errors
* Added: Option to choose anonymous data with Klantenvertellen version 1

= 2018.11.13 - version 1.1.6 =
* Fix: Saving settings is working again

= 2018.11.07 - version 1.1.5 =
* Fix: Link to review page not working for Klantenvertellen V2
* Added: New field for Klantenvertellen V2 review-page in settings
* Added: Option to show avarage score on review 

= 2018.11.06 - version 1.1.4 =
* Fix: Klantenvertellen V2 XML-feed working with new XML-feed formatting
* Added: Option to show update notices in the plugin for next updates

= 2018.09.05 - version 1.1.3 =
* Added: Auto slide function to slider layout
* Added: Filter to adjust the auto slide delay
* Added: Possibility to show the full KiyOh / Klantenvertellen logo

= 2018.08.30 - version 1.1.2 =
* Fix: Some files were saved incorrectly and processed errors

= 2018.08.30 - version 1.1.1 =
* Fix: Fixed problem with shortcode display

= 2018.08.29 - version 1.1.0 =
* Fix: Admin widget conditional fields
* Added: Possibility to hide reviews without author
* Added: Filter to show reviews with a rating higher dan x
* Added: Kiyoh.com provider
* Added: Possibility to send automatic invite e-mails after WooCommerce orders, WPML compatible
* Added: New shortcode to show a single review by its id
* Tweak: Review id's are now visible on the widget example in the admin
* Tweak: Added dutch translation of error message
* Tweak: Message when layout is not available in shortcode
* Tweak: Better error handling if XML loading fails

= 2018.06.21 - version 1.0.4 =
* Added: Review display options (in widget, shortcode and default settings) to hide reviews without rating or rating text
* Tweak: Hide empty total score rating
* Tweak: Hide empty single review rating
* Fix: Remove duplicate rating texts if no rating text is available for a single review

= 2018.06.18 - version 1.0.3 =
* Fix: Rich snippet info also available when summary is turned off
* Added: Privacy disclaimer text

= 2018.04.04 - version 1.0.2 =
* Fix: Changed rich snippet to Organization type
* Fix: Possible fatal error on WP Customizer on some server configurations
* Fix: Defaults for widget added by the WP Customizer
* Tweak: Added language to rich snippet reviews
* Tweak: Added possibility to add company name for Klantenvertellen, needed for rich snippet
* Tweak: Optimized logo-image for better performance
* Tweak: Updated Dutch translation
* Added: English translation
* Added: Possibility to choose Mobiliteit or Klantenvertellen V2 feeds
* Added: Possibility to limit length of review texts
* Added: Slider layout

= 2018.03.13 - version 1.0.1 =
* Fix: Fixed shortcode issues when plugin is installed for the first time
* Fix: Review text now can now output line breaks
* Tweak: Name field can now be chosen from the settings page for Klantenvertellen (useful if custom name fields are used)
* Tweak: Added default values for the default settings

= 2018.03.01 - version 1.0.0 =
* First release

== Frequently Asked Questions ==
= Kan ik deze plugin gratis gebruiken? =
Ja. Deze plugin word je gratis aangeboden door PEP. Je hebt natuurlijk wel een account nodig op KiyOh of Klantenvertellen.

= Wat als ik snel hulp nodig heb? =
Met onze [PRO plugin](https://wpfortune.com/shop/plugins/kiyoh-klantenvertellen/) ontvang je priority support via e-mail. We zijn niet telefonisch bereikbaar voor vragen over de plugin.

= Wat is Klantenvertellen.nl? =
[Klantenvertellen.nl](https://www.klantenvertellen.nl/) is een Nederlands system om beoordelingen van je klanten te verzamelen en deze te tonen op je website. Klantenvertellen is Google partner waardoor Google de klantbeoordeling ook kan overnemen.

= Wat is KiyOh.nl? =
[KiYoh.nl](https://www.kiyoh.nl/) is een Nederlands system om beoordelingen van je klanten te verzamelen. KiyOh is speciaal ontwikkeld voor webshops. Ook KiyOh is Google partner waardoor Google de klantbeoordeling ook kan overnemen.

= Wat moet ik doen als de plugin niet goed werkt? =
[Stel je vraag op de WordPress.org forums](https://wordpress.org/support/plugin/kiyoh-klantenvertellen). Wij doen ons best om je vraag zo snel mogelijk te beantwoorden en je verder te helpen.
Bel ons NIET voor plugin-gerelateerde vragen. Heb je snel hulp nodig? Met onze Met onze [PRO plugin](https://wpfortune.com/shop/plugins/kiyoh-klantenvertellen/) ontvang je priority support via e-mail. 

= Kan de klantenservice van KiyOh of Klantenvertellen mij helpen met de plugin? =
Nee, de plugin is ontwikkeld door PEP. De klantenservice van KiYoh of Klantenvertellen kan je niet helpen.

= Werkt de plugin met WPML / Kan ik teksten vertalen? =
Ja, met behulp van WPML kan je de plugin vertalen naar andere talen.

= Hoe maak ik een beoordelingspagina? =
Maak een nieuwe pagina in WordPress. Plaats daarop de volgende shortcodes:
	[kiyoh-klantenvertellen-stats]
	[kiyoh-klantenvertellen layout="list"]