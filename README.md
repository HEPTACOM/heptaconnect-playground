# HEPTAconnect Playground

The purpose of this repository is to enable easy exploration of HEPTAconnect in different environments.
A central Makefile is used to automate most of the examples.

## Shopware Platform

Start the example with `make shopware-platform`.
To supply a different database server you can set the DSN with the `DATABASE_URL` environment variable.

Or run `docker build . -t heptacom/heptaconnect-playground:0.8` to create a new docker image.
Rename the file "docker-compose.yml.dist" to "docker-compose.yml" and adapt it according to your own ideas.
Then run `docker-compose up -d`, please be patient, database imports and additional preparations will be 
made at the first start. After that you can connect to your container with: `docker exec -it heptaconnect-playground bash`. 

### Requirements

* php 7.4+
* mysql 5.7+
* composer


## Contributing

Thank you for considering contributing to this package! Be sure to sign the [CLA](./CLA.md) after creating the pull request. [![CLA assistant](https://cla-assistant.io/readme/badge/HEPTACOM/heptaconnect-playground)](https://cla-assistant.io/HEPTACOM/heptaconnect-playground)


### Steps to contribute

1. Fork the repository
2. `git clone yourname/heptaconnect-playground`
3. Make your changes to master branch
4. Create your Pull-Request


## License

Copyright 2020 HEPTACOM GmbH

Dual licensed under the [GNU Affero General Public License v3.0](./LICENSE.md) (the "License") and proprietary license; you may not use this project except in compliance with the License.
You may obtain a copy of the AGPL License at [https://spdx.org/licenses/AGPL-3.0-or-later.html](https://spdx.org/licenses/AGPL-3.0-or-later.html).
Contact us on [our website](https://www.heptacom.de) for further information about proprietary usage.
