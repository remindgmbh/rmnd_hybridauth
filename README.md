# README #

### What is this repository for? ###

Use hybridauth lib to connect to oauth services like facebook
@see https://hybridauth.github.io/

### How do I get set up? ###

 * Install
 * Create TypoScript configuration
 * Add login links to templates
 * See tutorials in folder "Tutorials/" for more use cases

### Extension settings ###

Admin Tools > Settings > Extension Configuration > rmnd_hybridauth

@todo

### Secutiry aspects ###

@todo

### Routing Problems ###

@todo

### Coming next (TODO) ###

 * Search all folders for fe_users with provider
 * Search subfolders for existing fe_users with provider
 * Signal slot to set fields received from provider
 * Signal slot to configure provider settings instead via typoscript
 * Configuration file loader to prevent usage of typoscript for sensible information
 * Logout from hybridauth, hook into TYPO3 logout to disconnect hybridauth by deleting session and cookies
 * Use frontend usergroup settings for after-login behavior (if set)
 * Add provider connections to fe_user
 * Confirmation of creation of new account after provider connect
 * Backend module to generate and test urls