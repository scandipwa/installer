# ScandiPWA installer
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fscandipwa%2Finstaller.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fscandipwa%2Finstaller?ref=badge_shield)


Module is a helper to install ScandiPWA Theme.

## 2.0.0
Finally theme bootstrap does not rely on Magento state and `scandipwa:theme:bootstrap` does not require additional
 setup:upgrade or initialized magento to be called.
 
 Now copying queue is defined in Bootstrap.php directly to support non-initialized Magento states and DB-less
  bootstrap and build.

## Usage

1) `composer require scandipwa/installer`

2) `php bin/magento scandipwa:theme:bootstrap <Vendor\name>`

### New theme bootstrap

Command accepts single parameter, which is treated as following format: "Vendor/theme".

**Note**

*You can change `Scandiweb/pwa` in examples below to anything suitable for you, keeping the same naming structure: 
`Vendor/theme_name`*

After `php bin/magento scandipwa:theme:bootstrap Scandiweb/pwa` it will make next effect:
1. Check for `<magento_root>/app/design/frontend/Scandiweb/pwa` - bootstrap will quite with error if directory is present to prevent unwanted overrides.
2. Create `<magento_root>/app/design/frontend/Scandiweb/pwa` directory
3. Copy necessary files to the newly created theme root.
4. Answer y/N (No is default) to a prompt for `theme.xml` and `registration.php` generation. You might want to create
 them manually - feel free to do it!
5. Run `php bin/magento setup:upgrade`.
6. You are bootstraped!

### Theme build
The theme must be built after it is bootstrap or after any changes.

1. Go to `app/design/frontend/<vendor/name>`
2. run `npm ci`
3. run `npm run build`


#### Customization
In order to customize copying task - simply edit `di.xml`, passing array with paths.

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fscandipwa%2Finstaller.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fscandipwa%2Finstaller?ref=badge_large)