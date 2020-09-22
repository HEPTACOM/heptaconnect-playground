SHELL := /bin/bash
PHP := $(shell which php) $(PHP_EXTRA_ARGS)
COMPOSER := $(PHP) $(shell which composer)

.PHONY: info
info:
	echo "There is no specific make target. Have a look at the README.md for further instructions"

.PHONY: shopware-platform
shopware-platform:
	[[ -d shopware-platform/vendor ]] || $(COMPOSER) install -d shopware-platform
	[[ -f shopware-platform/composer.lock ]] || $(COMPOSER) install -d shopware-platform
	$(PHP) shopware-platform/bin/shopware playground:init -vvv --force --no-interaction

.PHONY: clean
clean: shopware-platform-clean

.PHONY: shopware-platform-clean
shopware-platform-clean:
	[[ ! -f shopware-platform/composer.lock ]] || rm shopware-platform/composer.lock
	[[ ! -d shopware-platform/vendor ]] || rm -rf shopware-platform/vendor

.PHONY: shopware-platform-migration
shopware-platform-migration: shopware-platform
	shopware-platform/bin/shopware database:create-migration 'shopware-platform/vendor/heptacom/heptaconnect-storage-shopware-dal/src/Migration' 'Heptacom\HeptaConnect\Storage\ShopwareDal\Migration'
