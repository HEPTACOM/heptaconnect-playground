SHELL := /bin/bash
PHP := $(shell which php) $(PHP_EXTRA_ARGS)
COMPOSER := $(PHP) $(shell which composer)

.PHONY: shopware-platform
shopware-platform: sources
	chmod 600 shopware-platform/config/jwt/{public,private}.pem
	[[ -d shopware-platform/vendor ]] || $(COMPOSER) install -d shopware-platform
	[[ -f shopware-platform/composer.lock ]] || $(COMPOSER) install -d shopware-platform
	$(PHP) shopware-platform/bin/shopware playground:init -vvv --force --no-interaction
	$(PHP) shopware-platform/bin/shopware playground:demo-data -vvv --no-interaction

.PHONY: clean
clean: shopware-platform-clean

.PHONY: shopware-platform-clean
shopware-platform-clean:
	[[ ! -f shopware-platform/composer.lock ]] || rm shopware-platform/composer.lock
	[[ ! -d shopware-platform/vendor ]] || rm -rf shopware-platform/vendor
	[[ ! -d shopware-platform/var ]] || rm -rf shopware-platform/var

.PHONY: shopware-platform-migration
shopware-platform-migration: shopware-platform
	shopware-platform/bin/shopware database:create-migration 'shopware-platform/vendor/heptacom/heptaconnect-storage-shopware-dal/src/Migration' 'Heptacom\HeptaConnect\Storage\ShopwareDal\Migration'

.PHONY: sources
sources:
	[[ -d sources ]] || mkdir sources
	git -C "sources/storage-shopware-dal" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-storage-shopware-dal.git" "sources/storage-shopware-dal"
	git -C "sources/storage-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-storage-base.git" "sources/storage-base"
	git -C "sources/portal-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-portal-base.git" "sources/portal-base"
	git -C "sources/portal-local-shopware-platform" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-portal-local-shopware-platform.git" "sources/portal-local-shopware-platform"
	git -C "sources/dataset-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-dataset-base.git" "sources/dataset-base"
	git -C "sources/dataset-ecommerce" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-dataset-ecommerce.git" "sources/dataset-ecommerce"
	git -C "sources/core" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-core.git" "sources/core"
	git -C "sources/bridge-shopware-platform" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-bridge-shopware-platform.git" "sources/bridge-shopware-platform"
	git -C "sources/docs" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-docs.git" "sources/docs"
