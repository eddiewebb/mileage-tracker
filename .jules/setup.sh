sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3-common php8.3-cli php8.3-mbstring php8.3-mysql php8.3-zip php8.3-gd php8.3-curl php8.3-xml
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
