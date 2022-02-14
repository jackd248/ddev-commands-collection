DDEV Command Collection
========================

The DCC (DDEV Commands Collection) provides several predefined DDEV commands for different project types.

The project comes with an automatic copy and update process of the commands as well as several customization options.

For more information see the additional [README.md](src/CommandsCollection/general/static/README.md).

## Installation

Define one of the following project type:
- [TYPO3](src/CommandsCollection/typo3)
- [Symfony](src/CommandsCollection/symfony)
- [Drupal](src/CommandsCollection/drupal)

```json
"config": {
  "dcc-type": "Symfony"
}
```

Add the post scripts in the composer.json:

```json
"scripts": {
    "post-install-cmd": [
      "Kmi\\DdevCommandsCollection\\Composer\\Scripts::postInstall"
    ],
    "post-update-cmd": [
      "Kmi\\DdevCommandsCollection\\Composer\\Scripts::postUpdate"
    ]
}
```

Install from packagist via composer:

```bash
$ composer req kmi/ddev-commands-collection
```

Add the following files to your local project git:

```bash
.ddev/
  dcc-config.sh
  dcc-config.yaml
  .gitignore
```