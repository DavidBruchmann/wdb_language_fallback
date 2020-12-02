# TYPO3 extension wdb_language_fallback  

The extension is solving some issues with language fallback in TYPO3.  
Scenario to develop the extension was the following case:  
```
Default language (uid=0): GERMAN  
Fallback for default language: none  

Language with uid=1: French  
Fallback for French: none  

Language with uid=2: Italian  
Fallback for Italian: French  
```
TYPO3 never works correctly and displays German as fallback for Italian which is wrong.  
This extensions verifies the data and is assigning the right language as Fallback.  

Concerning powermail this extension shows the forms correctly but the forms
can't be sent because the form-fields include wrong variables. The professional
distribution is fixing this issue and might be able to solve issues in other special
extensions too.  

Languages can be configured, an example configuration with the mentioned scenario
can be found in the file ext_typoscript_setup.txt (see below).  

## Installation:  

Download the extension from TER or github. Composer installation is possible too.    
If you download the extension *by extension-manager from TER* it will be easily installed.  
If you download it *from TER as zip-archive*, save the archive in the folder `typo3conf/ext/`
of your installation and unpack it in a new folder wdb_language_fallback.  
If you want to install by *composer* execute `composer require wdb/wdb-language-fallback`.  

## Requirements:  

A running TYPO3 installation with correct configuration including `Site Configuration`
which includes the language setup.  

## Configuration:  

### Activating the extension by language  

Configuration is done in the file `ext_typoscript_setup.txt` of the extension.
In a single-domain Installation it can be probably overwritten in the TypoScript-Setup
in the TYPO3-backend. For a multi-domain-setup Tests are required first, contact me if
you need corresponding functionality.  
This is the configuration which can be adjusted to individual needs:
```
config {
    wdb_language_fallback_pro {
        # ===============
        # keys below (like it, fr, de, ...) have to be chosen according to language-aspect below
        # ===============
        activeForLanguages {
            it = 1
            fr = 0
            de = 0
        }
        # ===============
        # typo3Language (it) | iso-639-1 (it) | twoLetterIsoCode (it) | hreflang (it-IT)
        # ===============
        languageAspect = typo3Language
    }
}
```
Lines with values `0` like `fr = 0` could be removed and are only added to include
all used languages in the file.  
Languages which are configured in `config.wdb_language_fallback_pro.activeForLanguages`
with the value `1` will activate this extension for correcting the fallback. Usage
of this extension is usually not required for simple fallback to the default language.  

If you configure `config.wdb_language_fallback_pro.activeForLanguages.languageAspect`
with the value hreflang then `config.wdb_language_fallback_pro.activeForLanguages`
has to be configured a bit different (assume italian again as language which is
activating this fallback extension):
```
config {
    wdb_language_fallback_pro {
        # ===============
        # keys below (like it, fr, de, ...) have to be chosen according to language-aspect below
        # ===============
        activeForLanguages {
            it-IT = 1
            fr-FR = 0
            de-DE = 0
        }
        # ===============
        # typo3Language (it) | iso-639-1 (it) | twoLetterIsoCode (it) | hreflang (it-IT)
        # ===============
        languageAspect = hreflang
    }
}
```
The option `hreflang` has the advantage enabling the extension more granular.
Assume you've a site with the values `it-FR`, `it-CH` and `it-IT`, then it's 
possible to enable the extension for only one of them even the `typo3Language`
or `twoLetterIsoCode` for all of them has the value `it`.
 
### Intrinsic fields (only in pro-distribution)

Intrinsic fields are fields where the value in the translated record is taken from
the original language, so the value in the translated record is ignored.

This is required in the extension powermail to provide the same variables in forms
independent of the language. Other extensions might need this option too, so in
general this option could solve some extension related problems.

The configuration for this option related to the extension powermail:
```
config {
    wdb_language_fallback_pro {
        ...
    }
    plugin {
        powermail_pi1 {
            # ===============
            # intrinsicFields ...
            #   are fields where the value in the translated record is taken from
            #   the original language, so the value in the translated record is ignored.
            # ===============
            intrinsicFields {
                tx_powermail_domain_model_form {
                    0 = uid
                }
                tx_powermail_domain_model_page {
                    0 = uid
                    1 = forms
                }
                tx_powermail_domain_model_field {
                    0 = uid
                    1 = pages
                }
            }
        }
    }
}

```
all keys on level below `intrinsicFields` are tablenames for the extension, then
the intrinsic fieldnames are values whereas the keys are just counting numbers
starting with `0` for each table.  
Concerning this use-case the field `uid` for every table is in every language the
same, in some tables still other field-values are the same too, it's obvious
that in this case with these fields relations between the different tables are saved.  
This option `intrinsicFields` related to powermail provides usage of the same
variables in forms for every language and grants that the submitted form can be
evaluated.  

## Support  

Any questions and issues can be posted as issues on  
https://github.com/DavidBruchmann/wdb_language_fallback/issues  

Wiki:  
https://github.com/DavidBruchmann/wdb_language_fallback/wiki  

Source:  
https://github.com/DavidBruchmann/wdb_language_fallback  

Documentation (this file):  
https://github.com/DavidBruchmann/wdb_language_fallback/tree/master/README.md  

Homepage:  
https://webdevelopment.barlians.com

## Disclaimer:  

This extension was developed with much care about several issues but  

 - never covers all extensions, so it's i.e. not working with powermail in this distribution.  
 - for required support of powermail *the professional distribution can be obtained*.  
 - many available extensions never have been tested with this extension.  
 - any warranty concerning incorrect data, dataloss or any other kind of thinkable malfunction  
   can't be granted. Usage is completely on own risk, the extension is provided as it is.  
 - issues can be reported, questions asked and documentation or pull-requests provided.  

## Professional distribution:  

The professional distribution is shipped as `wdb_language_fallback_pro`, it covers
support of powermail and other extensions.  
Please contact me for price and amount of required licenses.  
Please provide a list of extensions that have to be supported, so that untested
extensions can be tested.  

Extensions that have been tested together with this language fallback extension:
--------------------------------------------------------------------------------

 - news
 - powermail (only wdb_language_fallback_pro)

## Sponsoring:  

You can support development and maintenance of this extension by PayPal.
For info about our bank-account please file a personal request.

PayPal: https://paypal.me/SophieBarlian

## Contact:  

e-mail: david.bruchmann@gmail.com  
skype:  david.bruchmann-web  

If you contact me by skype, please use a profile picture where no nudity or other
offensive content is shown, else your contact-request will be declined. Also an 
offensive user-name might be a reason for me to decline a contact-request.  

## Related to the following issues:  

 - https://forge.typo3.org/issues/19114  
 - https://forge.typo3.org/issues/86595  
 - https://forge.typo3.org/issues/86762  
 - https://forge.typo3.org/issues/87121  
 - https://forge.typo3.org/issues/88137  

## TODO:  

 - Provide check for content-elements for plugins.
   Currently all records will be displayed in the current language if not hidden,
   no matter if the relatedcontent-element for that language is hidden or not.
   The required feature will check if a content element for the parsed data
   is enabled or not. This won't probably grant 100% accuracy on pages where
   a table can belong to several plugins or where several plugins of the same
   type exist on a page but in many cases it will be sufficient.
 - Provide setting or analysis to get connection between data-page (pid) and
   content-elements for plugin-records. Like this it's possible to filter data
   more detailed.
 - Check if common settings like "hideIfNotTranslated" are respected enough
   and provide solution if not.
 - Check outdated extension "languagefallback" for features and options.
   Perhaps even a better solution is / could be used than the used hook in 
   `wdb_language_fallback`.
 - see https://review.typo3.org/c/Packages/TYPO3.CMS/+/66694 if that might help or even change the basic problem(s).

## Changelog:  

[2019.08.15][v1.3.0] fix sys_file_metadata needs null instead of false #9
[2019.08.14][v1.2.0] fix return value for missing results (false), add annotations and comments, remove version from composer.json, fix missing usage of initTsfe()
[2019.08.06][v1.1.1] reformat and add TODOs and chapter `intrinsicFields` in README.md  
[2019.08.06][v1.1.0] fix coding-faults  
[2019.08.05][v1.0.2] Add sponsoring link in README.md  
[2019.08.05][v1.0.1] fix constraint in composer.json, change in README.md  
[2019.08.05][v1.0.0] Initial release  
