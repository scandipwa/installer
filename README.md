# ScandiPWA installer

Module is a helper to install ScandiPWA Theme.

## Usage

`composer require scandipwa/installer`

After installation flush the caches (Varnish or filesystem).

`scandipwa:theme:bootstrap` must appear in your Magento 2 CLI command list
`php bin/magento`

### New theme bootstrap

Command accepts single parameter, which is treated as following format: "Vendor/theme".

After `php bin/magento scandipwa:theme:bootstrap Scandiweb/pwa` it will make next effect:
1. Check for `<magento_root>/app/design/frontend/Scandiweb/pwa` - bootstrap will quite with error if directory is present to prevent unwanted overrides.
2. Create `<magento_root>/app/design/frontend/Scandiweb/pwa` directory
3. Copy necessary files to the newly created theme root.
4. Answer y/N (No is default) to a prompt for `theme.xml` and `registration.php` generation. You might want to create
 them manually - feel free to do it!
5. Run `php bin/magento setup:upgrade`.
6. You are bootstraped!

## Package re-usage
If you ever need a similar bootstrap and "magento-extra" section within composer.json is not enough - here is a short
 summary of module logic and customization, so feel free to re-use it!
 
 ### Package goal
 Package goal is ensure simple theme bootstrap, by copying specific files, keeping the file structure.
 Apart of it - it must be interactive at some sort, to give you ability to generate more then one theme, without 
 additional configuration.
 
#### In order to achieve this, package provides:
 - Magento 2 CLI command
 - File copying logic (adding directory recursive copy)
 
 #### Customization
 In order to customize copying task - simply edit `di.xml`, passing array with paths.
