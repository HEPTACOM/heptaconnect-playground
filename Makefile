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

.PHONY: sdk
sdk: sources
	[[ -d sdk ]] || $(COMPOSER) create-project heptacom/heptaconnect-sdk:dev-master sdk --no-scripts --remove-vcs
	$(COMPOSER) config 'repositories.heptaconnect-sources' --json '{"type":"path","url":"../sources/**"}' -d sdk
	$(COMPOSER) config 'repositories.heptaconnect-sources-sdk' --json '{"type":"path","url":"../sources/lib-sdk","options":{"symlink": false}}' -d sdk
	$(COMPOSER) require 'heptacom/heptaconnect-lib-sdk:>=0.0.1' --update-with-all-dependencies -d sdk
	$(PHP) sdk/vendor/bin/heptaconnect-sdk sdk:install

.PHONY: clean
clean: shopware-platform-clean sdk-clean

.PHONY: shopware-platform-clean
shopware-platform-clean:
	[[ ! -f shopware-platform/composer.lock ]] || rm shopware-platform/composer.lock
	[[ ! -d shopware-platform/vendor ]] || rm -rf shopware-platform/vendor
	[[ ! -d shopware-platform/var ]] || rm -rf shopware-platform/var

.PHONY: sdk-clean
sdk-clean:
	[[ ! -d sdk ]] || rm -rf sdk

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
	git -C "sources/lib-sdk" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-lib-sdk.git" "sources/lib-sdk"
	git -C "sources/dataset-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-dataset-base.git" "sources/dataset-base"
	git -C "sources/dataset-ecommerce" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-dataset-ecommerce.git" "sources/dataset-ecommerce"
	git -C "sources/core" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-core.git" "sources/core"
	git -C "sources/bridge-shopware-platform" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-bridge-shopware-platform.git" "sources/bridge-shopware-platform"
	git -C "sources/docs" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-docs.git" "sources/docs"
