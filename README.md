# Installation steps

## Prerequisite
Composer

## Installation steps

```bash
composer install
```

## Usage

### Import siren data
```bash
# eg. ZIP_FILEPATH : http://files.data.gouv.fr/sirene/sirene_2018088_E_Q.zip
php bin/console itl:siren:import ZIP_FILEPATH
```

### Endpoint : Retrieve company data from siren
```bash
GET /companies/{siren}
```

