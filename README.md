```
     __  _____________   _______   __________  ____  ______
    /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
   / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
  / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/

```

A set of [Moodle](http://moodle.org) plugins that integrate with
[MediaCore](http://mediacore.com).

Designed to work with Moodle 2.3 and 2.4+

Overview
===
These plugins provide a rich set of Moodle-MediaCore integrations using LMS-LTI
to connect with your MediaCore site without having to leave Moodle.

Moodle Plugins
===

* Local (local/mediacore) - Provides a custom LTI integration setting. Used by
  other plugins.
* Repository (repository/mediacore) - allows you to search and insert media
  using the moodle media picker.
* TinyMce (lib/editor/tinymce/plugins/mediacore) - provides own own TinyMCE
  picker with rich integration to MediaCore.
* Filter (filter/mediacore) - transforms the repository and picker URLs into a
  media player.


Installation
===

MediaCore Plugin:
---

To make the installation go smoothly we recommend you remove any old versions
of the MediaCore plugin from your Moodle install. This is done by ensuring that
the following directories do not exist:

- `path/to/moodle/filters/mediacore`
- `path/to/moodle/lib/editor/tinymce/plugins/mediacore`
- `path/to/moodle/local/mediacore`
- `path/to/moodle/repository/mediacore`


After doing this you will also need to Navigate to: `Site administration ->
Plugins -> Plugins Overview` and delete the following MediaCore plugin code
from the Moodle database:

- `MediaCore media filter`
- `Mediacore search`
- `MediaCore media picker`
- `Mediacore Package libraries`

** Note: any previous Moodle/MediaCore settings will be removed when you delete
  or upgrade the MediaCore plugin from v1.6 to v2.0. These old v1.6 settings are
  no longer valid.*

Once any old versions have been removed, you can begin to install the new
version of the plugin. This is done by copying the following folders into the
correct directories.

- `filters/mediacore` into `path/to/moodle/filters/`
- `lib/editor/tinymce/plugins/mediacore` into
  `path/to/moodle/lib/editor/tinymce/plugins/`
- `local/mediacore` into `path/to/moodle/local/`
- `repository/mediacore` into `path/to/moodle/repository/`

To finalize the installation you will need to navigate to: `Site administration
-> Notifications` and hit ""check for available updates"". Click ""Upgrade
Moodle database now"" to complete this step.

To hook your MediaCore site into Moodle you must navigate to: `Site
administration -> Plugins -> Local plugins -> MediaCore LTI config` and enter:

- the URL of your MediaCore site (i.e. http://demo.mediacore.tv).
- the name of your LTI consumer key (this must match a valid LTI consumer in
  your MediaCore site)
- the secret of your LTI shared secret (this also must match the secret in the
  LTI consumer above)

In order for videos to display in Moodle, you must enable the MediaCore Filter.
This can be turned on by navigating to: `Site administration -> Plugins ->
Filters -> Manage Filters` and selecting `On` from drop down menu in the
`active` column next to 'MediaCore media filter'.

You will also need to enable the repository. it may be turned on by navigating
to: `Site administration -> Plugins -> Repositories -> Manage Repositories` and
selecting ""Enabled and visible"" from drop down menu next to "MediaCore
search".

About
===

[MediaCore](http://mediacore.com/) is an online video platform for managing,
encoding, monetizing and delivering video to mobile and desktop devices.
MediaCore makes it easy for any organization to share video either publicly or
privately and build an amazing user experience on both desktop and mobile
browsers around their own content.

Who's using Mediacore? More and more MediaCore powered sites are popping up all
over the world. You can learn more about some of these sites here on our
[MediaCore showcase](http://mediacore.com/why-mediacore).
