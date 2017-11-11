# Freelancer projects

[WordPress][1] plugin for managing and publishing your _WP_ projects (plugins or themes). Is created to meet my personal needs for my site [ondrejd.com][2].

__This plugin is still under development and is not finished yet - see sections [Features](#features) and [TODO](#todo) for more details.__

## Contents

* [Donations](#donations)
* [Features](#features)
* [TODO](#todo)
* [Usage](#usage)
  - [Requirements](#requirements)
  - [Installation](#installation)
* [ChangeLog](#changelog)

## Donations

If your like this plugin and you want to be maintained and improved more frequently consider donation:

[![Make donation](https://www.paypalobjects.com/webstatic/paypalme/images/pp_logo_small.png "PayPal.Me, your link to getting paid")][6]

## Features

* [custom post type][8] _Project_ with some new metaboxes (_Repository_, _Status_, _System name_)
* `cs_CZ` and `en_US` localization

## TODO

- [ ] finish [CPT][8] _Project_ (`0.2.0`)
  - [x] ~~metaboxes: _Repository_, _Status_, _System name_ (`0.2.0`)~~
  - [x] ~~table list: add custom columns with our meta values~~
  - [x] ~~table list: allow change our meta values through _quick edit_~~
  - [ ] table list: add help box (about our custom meta values)
  - [ ] table list: add bulk action (for project's status)
  - [x] ~~table list: add filters by our meta values~~~~
  - [ ] would be great if text of the post/project was the same as `README.md` on [GitHub][9] (`extrafeature`)
  - [ ] metabox _Project Type_ - pre-defined value picked from select box. Values are editable in plugin's settings (`0.2.1`)
  - [ ] metabox _Project Version_ - just simple string (`0.2.1`)
  - [ ] __BUG__: on Admin > Projects page is link `Views Pages` (left side of _+ Actions_) ... see if we can customize the label of it (e.g. `Views Projects`)
- [ ] projects can be attached to the normal posts/pages (as a _tag_?) (`0.2.0`)
- [ ] __Project's detail page__ (`O.2.0`)
  - [ ] if [GitHub][9] repository is attached than show list of issues (or commits or whatever)
  - [ ] do the same for [BitBucket][10] repositories
- [ ] administrator could be able to set user rights for accessing projects
- [ ] enable relations between projects - `uses` indicates that one project _uses_ other one, `suceed` indicates that one project is _successor_ of other one and replace the other it
  - [ ] _Rename relations - these names are really bad_
  - [ ] _Check for some existing designs of project relationships_
- [ ] extend `README.md` with _Developers_ section about available hooks, templates etc.
- [x] ~~prepare `cs_CZ` and `en_US` localization~~
- [ ] `README.cz.md`

## Usage

### Requirements

* plugin requires [PHP 7.0][4] at least
* plugin requires [WordPress 4.8][3] at least

### Installation

* download the latest release from the [repository][5]
* extract archive into your `wp-content/plugins` folder
* navigate browser to [WordPress][1] administration > _Plugins_
* activate plugin __Freelancer projects__

## ChangeLog

### Version 0.2.0

* Updated table list for [CPT][8] _Project_ (added columns with sorting, quick edit, filtering)
* Added `cs_CZ` and `en_US` locales

### Version 0.1.0

* Initial version (_the second one_)
* Code refactored (added bootstrap file as described in [this my post][7] of mine)

[1]: https://wordpress.org/
[2]: https://ondrejd.com/
[3]: https://codex.wordpress.org/Version_4.8
[4]: https://php.net/
[5]: https://github.com/ondrejd/odwp-projects
[6]: https://www.paypal.me/ondrejd
[7]: https://ondrejd.com/XXX
[8]: https://developer.wordpress.org/reference/functions/register_post_type/
[9]: https://github.com/
[10]: https://bitbucket.org/
