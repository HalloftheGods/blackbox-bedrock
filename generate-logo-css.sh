#!/bin/bash
IMG_PATH="youniverse/wp-content/mu-plugins/blackbox-bedrock/hallofthegodsinc.png"
CSS_PATH="youniverse/wp-content/mu-plugins/blackbox-bedrock/css/logo.css"

if [ -f "$IMG_PATH" ]; then
    B64=$(base64 -w 0 "$IMG_PATH")
    echo ":root { --custom-logo-base64: url('data:image/png;base64,$B64'); }" > "$CSS_PATH"
    echo "Generated $CSS_PATH"
else
    echo "Error: Image not found at $IMG_PATH"
    exit 1
fi
