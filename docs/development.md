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

### Phing

To help develop and stay with clean code we have configured a phing command that will help you ensure your code fits
our requirement
```bash
docker-compose run sf-php-sdk-dev vendor/bin/phing test
```

This will run a test build with `phpcs` and `phpunit` in order to accept a PR your code must pass this test command
with all in the green. 