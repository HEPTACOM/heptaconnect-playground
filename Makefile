SHELL := /bin/bash
PHP := $(shell which php) $(PHP_EXTRA_ARGS)
COMPOSER := $(PHP) $(shell which composer)

.PHONY: shopware-platform
shopware-platform: repos
	chmod 600 shopware-platform/config/jwt/{public,private}.pem
	[[ -d shopware-platform/vendor ]] || $(COMPOSER) install -d shopware-platform
	[[ -f shopware-platform/composer.lock ]] || $(COMPOSER) install -d shopware-platform
	$(PHP) shopware-platform/bin/shopware playground:init -vvv --force --no-interaction
	$(PHP) shopware-platform/bin/shopware playground:demo-data -vvv --no-interaction

.PHONY: sdk
sdk: repos
	[[ -d sdk ]] || $(COMPOSER) create-project heptacom/heptaconnect-sdk:dev-master sdk --no-scripts --remove-vcs
	$(COMPOSER) config 'repositories.heptaconnect-sources' --json '{"type":"path","url":"../repos/**"}' -d sdk
	$(COMPOSER) config 'repositories.heptaconnect-sources-sdk' --json '{"type":"path","url":"../repos/lib-sdk","options":{"symlink": false}}' -d sdk
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

.PHONY: repos
repos:
	[[ -d repos ]] || mkdir repos
	git -C "repos/storage-shopware-dal" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-storage-shopware-dal.git" "repos/storage-shopware-dal"
	git -C "repos/storage-native" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-storage-native.git" "repos/storage-native"
	git -C "repos/storage-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-storage-base.git" "repos/storage-base"
	git -C "repos/portal-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-portal-base.git" "repos/portal-base"
	git -C "repos/portal-local-shopware-platform" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-portal-local-shopware-platform.git" "repos/portal-local-shopware-platform"
	git -C "repos/lib-sdk" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-lib-sdk.git" "repos/lib-sdk"
	git -C "repos/dataset-base" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-dataset-base.git" "repos/dataset-base"
	git -C "repos/dataset-ecommerce" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-dataset-ecommerce.git" "repos/dataset-ecommerce"
	git -C "repos/core" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-core.git" "repos/core"
	git -C "repos/bridge-shopware-platform" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-bridge-shopware-platform.git" "repos/bridge-shopware-platform"
	git -C "repos/docs" pull --ff-only || git clone "https://github.com/HEPTACOM/heptaconnect-docs.git" "repos/docs"

include dev-ops/make.d/**/*.mk
