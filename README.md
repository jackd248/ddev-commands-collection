DDEV Commands Collection
========================

<p align="center"><img src="./doc/Images/dcc.svg" alt="DCC" width="150">
</p>

The __DCC__ (DDEV Commands Collection) provides several predefined [DDEV](https://ddev.readthedocs.io/en/stable/) commands for different project types.

- [Intention](#intention)
- [Installation](#installation)
- [Impact](#impact)

The project comes with an automatic copy and update process of the commands as well as several customization options. So the DDEV commands in your local project under `.ddev/commands` will always keep updated extended commands.

For more usage information see the additional [README.md](src/CommandsCollection/general/static/README.md).


<a name="intention"></a>
## Intention

The main goals for the DCC are:

- reusable commands in several projects
- reusable functionalities within the commands
- standardization of commands in usage and style
- simplification and transparency of command execution
- customisation of the automated DCC process

<a name="installation"></a>
## Installation

Define one of the following project type within your `composer.json`:
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

Install from [packagist](https://packagist.org/packages/kmi/ddev-commands-collection) via composer:

```bash
$ composer req kmi/ddev-commands-collection
```

Add the following files to your local project git:

```bash
.ddev/
  commands/
    .gitignore
```

__Note__: If your project structure differs from the example below and your `composer.json` and the ddev directory aren't on the same level, you can define the relative path to the ddev directory inside your `composer.json` like the following example:
```json
"config": {
  "ddev-dir": "./../.ddev"
}
```


<a name="impact"></a>
## Impact

The automatic DCC process affects the following files/directories (marked as **bold**) inside your project (example structure for a project):


- `project/`
  - `.ddev/`
    - `commands/`
      - `web`
        - **dcc-cc**
        - **dcc-composer-app**
        - **dcc-composer-deployment**
        - **dcc-console**
        - **dcc-init**
        - **dcc-release**
        - **dcc-sync**
        - **dcc-theme**
        - `...`
      - **faq**/
        - **dcc-faq-web-sync.sh**
        - `...`
      - **scripts**/
        - **dcc-colors.sh**
        - `...`
      - **.gitignore**
      - **dcc-config.yaml**
      - **dcc-config.sh**
      - **README.md**
      - `...`
    - `config.yaml`
    - `...`
  - `app/` -- *Application directory*
    - `composer.json`
    - `composer.lock`
    - `...`
  - `composer.json` -- *Adapted composer file for DCC*
  - `composer.lock`
  - `...`
           
See the additional [README.md](src/CommandsCollection/general/static/README.md) for information about adjustments.
