TYPO3 extension wdb_language_fallback  
=====================================  

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

Installation:
-------------
Download the extension from TER or github. Composer installation is possible too.    
If you download the extension *by extension-manager from TER* it will be easily installed.  
If you download it *from TER as zip-archive*, save the archive in the folder `typo3conf/ext/`
of your installation and unpack it in a new folder wdb_language_fallback.  
If you want to install by *composer* execute `composer require wdb/wdb-language-fallback`.  

Requirements:
-------------
A running TYPO3 installation with correct configuration including `Site Configuration`
which includes the language setup.  

Configuration:
--------------
Configuration is done in the file `ext_typoscript_setup.txt` of the extension.
In a single-domain Installation it can be probably overwritten in the TypoScript-Setup
in the TYPO3-backend. For a multi-domain-setup Tests are required first, contact me if
you need corresponding functionality.  
This is the configuration which can be adjusted to individual needs:
```
config {
    wdb_language_fallback {
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
Languages which are configured in `config.wdb_language_fallback.activeForLanguages`
with the value `1` will activate this extension for correcting the fallback. Usage
of this extension is usually not required for simple fallback to the default language.  

If you configure `config.wdb_language_fallback.activeForLanguages.languageAspect`
with the value hreflang then `config.wdb_language_fallback.activeForLanguages`
has to be configured a bit different (assume italian again as language which is
activating this fallback extension):
```
config {
    wdb_language_fallback {
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

Support  
-------  
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

Disclaimer:
-----------
This extension was developed with much care about several issues but  

 - never covers all extensions, so it's i.e. not working with powermail in this distribution.  
 - for required support of powermail *the professional distribution can be obtained*.  
 - many available extensions never have been tested with this extension.  
 - any warranty concerning incorrect data, dataloss or any other kind of thinkable malfunction  
  can't be granted. Usage is completely on own risk, the extension is provided as it is.  
 - issues can be reported, questions asked and documentation or pull-requests provided.  

Professional distribution:
--------------------------
The professional distribution is shipped as `wdb_language_fallback_pro`, it covers
support of powermail and other extensions.  
Please contact me for price and amount of required licenses.  
Please provide a list of extensions that have to be supported, so that untested
extensions can be tested.  

Extensions that have been tested together with this language fallback extension:
--------------------------------------------------------------------------------

 - news
 - powermail (only wdb_language_fallback_pro)

Contact:
--------
e-mail: david.bruchmann@gmail.com  
skype:  david.bruchmann-web  

If you contact me by skype, please use a profile picture where no nudity or other
offensive content is shown, else your contact-request will be declined. Also an 
offensive user-name might be a reason for me to decline a contact-request.  

Related to the following issues:  
--------------------------------  

 - https://forge.typo3.org/issues/19114  
 - https://forge.typo3.org/issues/86595  
 - https://forge.typo3.org/issues/86762  
 - https://forge.typo3.org/issues/87121  
 - https://forge.typo3.org/issues/88137  

Changelog:  
----------  
[2019.08.05][v1.0.0] Initial release  
