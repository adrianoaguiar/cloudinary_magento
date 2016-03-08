# Cloudinary Extension

Cloudinary Magento extension.

## IMPORTANT INFORMATION

Currently the extension doesn't cope well with changing clouds when the extension is in use because of the image synchronisation between Magento and Cloudinary.
Changing clouds will cause Magento to be unaware that the images that were already synchronised are now not available in the newly specified cloud. If the records for the synchronisation are removed, then it is possible to run the export process for the new cloud, but if the configuration ever reverts to the previous cloud, there will be no record of the previously synchronised images, and, in addition to having to reset the synchronisation records to be able to run the export again, the images will be re-uploaded and exist duplicated on cloudinary.


## Functionality Overview

At the current time (December 2014), the extension is replacing the basic Magento image functionality and providing it via Cloudinary. This allows for on-the-fly image optimisation for specific web clients, and CDN-like capabilities, thus moving the traffic away from the servers as well as serving it from the Cloudinary high performing and geo-location aware network.

### Image Upload

From an admin perspective, Image upload is in no way different from the standard Magento image upload. The only change is that when the extension is enabled, images are uploaded to Cloudinary.

### Image Display

When the extension is enabled and the image is available in Cloudinary, the images served from the Cloudinary network rather than from the local infrastructure. From the user's perspective, there's no difference in behaviour other than the potential performance gains.

### Credentials and cloud configuration

The `key`, `secret` and `cloud` configuration are all available under the `System->Configuration` menu option, in the `Services` group under the name `Cloudinary`.

### Image Export

The Cloudinary extension provides functionality to trigger the upload of pre-existing images to Cloudinary. This process is throttled to prevent network flooding, and is controlled manually to allow for store admins to choose when it should happen.
To start the export process go to the `Cloudinary->Manage` menu in Magento's admin panel, and press the `Start Export` button, note that if there are no images to migrate, the button will be greyed out. Pressing the `Start Export` button (when it's not greyed out), will trigger the export process and show the export progress. When the export finished, the `Start Export` button will become greyed out.
It's possible to pause the export process by pressing the `Stop Export` button. This will allow you to continue the export process later.
Images become available via Cloudinary as soon as they've been uploaded, so stopping the export process still allows the site to benefit from Cloudinary for the images that were already uploaded.

### Enabling/Disabling the extension

The extension can be enabled and disabled at will. To disable the extension, go to the `Cloudinary->Manage` menu, and press the `Enable Cloudinary` \ `Disable Cloudinary` button. Keep in mind that when the extension is disabled, no images will be served from Cloudinary nor will new images be uploaded to Cloudinary.
If the extension is disabled at any point, it's advisable to start the export process after enabling it. The `Start Export` will be greyed out, if there are no images to migrate.


## Enabling/Disabling
The enable/disable button in the Cloudinary admin section determines whether the application will request its media from the remote filesystem (Cloudinary) or from the local server. Since the media will end up being store both locally and remotely, the extension can be enabled/disabled without a major impact on the systems operation. It can be done during/before/after export.

For example, when extension is *enabled*: 
- If the image has already been uploaded to Cloudinary, the system will fetch the image from Cloudinary.
- If the has not yet been uploaded, the system will fetch it locally

When extension is *disabled*
- The system will always fetch the image locally, regardless of whether it has been uploaded to Cloudinary or no.

## Known Issues

- When the export is started, all existing media will gradually be uploaded to cloudinary. If the extension cannot upload an image (e.g. its missing, corrupted or is rejected by the remote service) it will *not* mark the image as having been exported (syncrhonized) and will log an error message to system.log, the said image will thus not be removed from the queue of images to migrate and the export will never complete. It is up to the *Integrator* to be aware of images that could not be uploaded and to decide if they should be deleted from the local database. The export will only be marked as completed when all of the images in the media gallery have been successfully uploaded.

## Running Gherkin Features
- Start phantomjs on the VM: 
```
phantomjs --webdriver 4444 --load-images=no &
```
- Make sure you are in the cloudinary directory:
```
cd /vagrant/vendor/inviqa/cloudinary
```
- Run Behat
```
bin/behat -fprogress
```

## API Version
This module currently uses version 1.1.* of the Cloudinary Api
