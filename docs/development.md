# Development

If you want to contribute you can fork us on github and submit pull request with your modification.
Please review the [contributing guideline](../CONTRIBUTING.md) before submiting issues or PR.

## Tools

### Docker

We provide with this repository dockerfile that can help you start developing on the project.
In your project root where you have cloned the repo run this commands :

1. Build the image
```bash
docker build sf-php-sdk-dev .
```
2. Start the container
```bash
docker run -d --name php-sdk-dev sf-php-sdk-dev
```
3. Connect ot the container
```bash
docker exec -ti php-dek-dev bash
```

You will then be in `/var/www` where your sources are mounted into the container.

## PHPUNIT

To run the PHPUNIT test of the project simply run :
```bash
docker exec -ti php-dek-dev phpunit
```

It will run the tests of the SDK and generated an HTML coverage summary in `build/coverage`.