#!/bin/bash
# ApexWeave CLI Installer

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

printf "Installing ApexWeave CLI...\n\n"

# Check for required tools
if ! command -v curl &> /dev/null; then
    printf "${RED}✗ curl is required but not installed.${NC}\n"
    exit 1
fi

if ! command -v jq &> /dev/null; then
    printf "${YELLOW}! jq is not installed. Attempting to install...${NC}\n"
    if command -v apt-get &> /dev/null; then
        sudo apt-get install -y jq > /dev/null 2>&1
    elif command -v brew &> /dev/null; then
        brew install jq > /dev/null 2>&1
    elif command -v yum &> /dev/null; then
        sudo yum install -y jq > /dev/null 2>&1
    else
        printf "${RED}✗ Could not install jq automatically. Please install it manually: https://jqlang.github.io/jq/download/${NC}\n"
        exit 1
    fi
    printf "${GREEN}✓ jq installed${NC}\n"
fi

# Create user bin directory if it doesn't exist
BIN_DIR="$HOME/.local/bin"
mkdir -p "$BIN_DIR"

# Download
printf "${BLUE}Downloading latest version...${NC}\n"
TMP_FILE=$(mktemp)

if curl -fsSL https://apexweave.com/tools/apexweave -o "$TMP_FILE"; then
    chmod +x "$TMP_FILE"

    printf "${BLUE}Installing to $BIN_DIR/apexweave...${NC}\n"
    mv "$TMP_FILE" "$BIN_DIR/apexweave"

    # Check if ~/.local/bin is in PATH
    if [[ ":$PATH:" != *":$BIN_DIR:"* ]]; then
        if [ -n "$ZSH_VERSION" ]; then
            SHELL_CONFIG="$HOME/.zshrc"
        elif [ -n "$BASH_VERSION" ]; then
            SHELL_CONFIG="$HOME/.bashrc"
        else
            SHELL_CONFIG="$HOME/.profile"
        fi

        echo "export PATH=\"\$HOME/.local/bin:\$PATH\"" >> "$SHELL_CONFIG"
        printf "${GREEN}✓ PATH updated in $SHELL_CONFIG${NC}\n"
        printf "${YELLOW}! Run: source $SHELL_CONFIG  (or restart your terminal)${NC}\n"
    fi

    # Warn if an old system-wide binary exists and will shadow this one
    OLD=$(command -v apexweave 2>/dev/null || true)
    if [ -n "$OLD" ] && [ "$OLD" != "$BIN_DIR/apexweave" ]; then
        printf "\n${YELLOW}! Old installation found at $OLD${NC}\n"
        printf "${YELLOW}  It will take priority in your PATH until removed.${NC}\n"
        printf "${YELLOW}  To fix: sudo rm $OLD${NC}\n"
    fi

    printf "\n${GREEN}✓ ApexWeave CLI installed successfully!${NC}\n"
    printf "\n  Location: $BIN_DIR/apexweave\n"
    printf "\n  Next steps:\n"
    printf "    apexweave login     -- login to your account\n"
    printf "    apexweave apps      -- list your applications\n\n"

    # Show version
    "$BIN_DIR/apexweave" version

else
    printf "${RED}✗ Download failed${NC}\n"
    rm -f "$TMP_FILE"
    exit 1
fi
