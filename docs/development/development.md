# Development

If you want to contribute you can fork us on github and submit pull request with your modifications.
Please review the [contributing guideline](../../CONTRIBUTING.md) before submiting issues or PR.
You should refer to our [coding standard rules](coding-standards.md) rules as any code not following these rules
will not be accepted.

## Tools

### Docker

We provide with this repository a docker config that can help you start developing on the project.  
Require to install docker and docker-compose.  
  
In your project root where you have cloned the repo run this commands :

1. Build the image
```bash
docker-compose build
```
3. Install project dependencies
```bash
docker-compose run sf-php-sdk-dev composer install --dev
```

## Code checks

To help you test your code against our requirement there is a composer test script configured :
```bash
docker-compose run sf-php-sdk-dev composer test 
```

The script will run `phpunit` and `phpcs`.
Reports for the runs can be found in `build/*`.
