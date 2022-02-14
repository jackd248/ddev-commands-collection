#!/bin/bash

############################
# DDEV Commands Collection #
############################

## https://github.com/jackd248/ddev-commands-collection
## v<version/>
## dcc-autogenerated

# Load additional scripts
. "$(dirname "$0")/../scripts/dcc-colors.sh"

# Custom script
echo "${reset}${black}############################################################################################"
echo "${reset}${yellow}[FAQ]${reset} Short help section for the sync process inside a ${bold}DDEV${reset} project with the ${bold}db-sync-tool${reset} & ${bold}file-sync-tool${reset}"
echo "  - ${bold}${yellow}Authentication via SSH key${reset}: Check if you executed \033[90mddev auth ssh\033[m to enable your SSH key inside the web container, if the SSH authorization during the sync process failed."
echo "  - ${bold}${yellow}Authentication via password${reset}: The package tries to connect via ssh key by default. If you want to force the password input append the option ${black}--force-password${reset}."
echo "  - ${bold}${yellow}Credentials${reset}: Check the provided server and application credentials for the requested sync. They are commonly stored in ${black}/deployment/db-sync-tool/sync-*-to-local.json${reset}."
echo "  - ${bold}${yellow}Verbosity${reset}: For detailed output of the sync process, run the command with a higher verbosity level ${black}ddev sync -v${reset}."
echo "  - ${bold}${yellow}General${reset}: Check if you installed the latest versions of the sync-tools. Therefore execute ${black}ddev exec pip3 install --user --upgrade db-sync-tool-kmi file-sync-tool-kmi${reset}."
echo "${reset}${yellow}[FAQ]${reset} More information https://confluence.xima.de/display/OSC/Database+Sync+Tool"