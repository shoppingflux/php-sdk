# Development

If you want to contribute you can fork us on github and submit pull request with your modification.
Please review the [contributing guideline](../CONTRIBUTING.md) before submiting issues or PR.

## Tools

### Docker

We provide with this repository a docker config that can help you start developing on the project.  
Require to install docker and docker-compose.  
  
In your project root where you have cloned the repo run this commands :

1. Build the image
```bash
docker-compose build
```
2. Start the container
```bash
docker-compose up -d
```
3. Install project dependencies
```bash
docker-compose run sf-php-sdk-dev composer install --dev
```

## PHPUNIT

To run the PHPUNIT test of the project simply run :
```bash
docker-compose run sf-php-sdk-dev vendor/bin/phpunit
```

It will run the tests of the SDK and generated an HTML coverage summary in `build/coverage`.